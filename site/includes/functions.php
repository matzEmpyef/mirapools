<?php
/**
 * Shared helpers: JSON storage, settings, images, gallery, security, uploads.
 * Storage is plain JSON files in /data (no database needed).
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/defaults.php';
require_once __DIR__ . '/content.php';
require_once __DIR__ . '/ui.php';

/* ---------- output ---------- */

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Absolute URL for canonical/OG use. */
function abs_url(string $path = '/'): string
{
    if (preg_match('#^https?://#', $path)) {
        return $path;
    }
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}

/** Local asset URL with a cache-busting version. */
function asset(string $path): string
{
    $file = BASE_DIR . '/assets/' . ltrim($path, '/');
    $v = is_file($file) ? (string) filemtime($file) : '1';
    return '/assets/' . ltrim($path, '/') . '?v=' . $v;
}

function tel_link(string $phone): string
{
    return 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
}

function wa_link(string $text = ''): string
{
    $n = preg_replace('/\D/', '', (string) settings('whatsapp'));
    return 'https://wa.me/' . $n . ($text !== '' ? '?text=' . rawurlencode($text) : '');
}

/* ---------- JSON storage ---------- */

function data_path(string $name): string
{
    return DATA_DIR . '/' . $name . '.json';
}

function json_read(string $name, $fallback = null)
{
    $file = data_path($name);
    if (!is_file($file)) {
        return $fallback;
    }
    $fh = fopen($file, 'rb');
    if (!$fh) {
        return $fallback;
    }
    flock($fh, LOCK_SH);
    $raw = stream_get_contents($fh);
    flock($fh, LOCK_UN);
    fclose($fh);
    $data = json_decode((string) $raw, true);
    return is_array($data) ? $data : $fallback;
}

/** Atomic write: temp file + rename, guarded by a lock file. */
function json_write(string $name, array $data): bool
{
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0775, true);
    }
    $file = data_path($name);
    $lock = fopen($file . '.lock', 'c');
    if (!$lock) {
        return false;
    }
    flock($lock, LOCK_EX);
    $tmp = $file . '.' . bin2hex(random_bytes(4)) . '.tmp';
    $ok = file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) !== false
        && rename($tmp, $file);
    if (!$ok && is_file($tmp)) {
        unlink($tmp);
    }
    flock($lock, LOCK_UN);
    fclose($lock);
    return $ok;
}

/* ---------- settings ---------- */

function all_settings(): array
{
    static $cache = null;
    if ($cache === null) {
        $saved = json_read('settings', []);
        $cache = array_replace(default_settings(), is_array($saved) ? $saved : []);
    }
    return $cache;
}

function settings(string $key, $fallback = '')
{
    $s = all_settings();
    return $s[$key] ?? $fallback;
}

function full_address(): string
{
    $s = all_settings();
    return trim($s['address_line'] . ', ' . $s['locality'] . ', ' . $s['city'] . ', ' . $s['region'] . ' ' . $s['postcode'], ', ');
}

/* ---------- images ---------- */

function slot_overrides(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = json_read('images', []);
    }
    return $cache;
}

/** Current src for an image slot (uploaded override, else default). */
function img(string $slot): string
{
    $over = slot_overrides();
    if (!empty($over[$slot]['src'])) {
        return $over[$slot]['src'];
    }
    $slots = image_slots();
    return $slots[$slot][2] ?? '';
}

/** Smaller version of a slot image for cards. */
function img_thumb(string $slot): string
{
    $over = slot_overrides();
    if (!empty($over[$slot]['thumb'])) {
        return $over[$slot]['thumb'];
    }
    $src = img($slot);
    return str_contains($src, 'images.unsplash.com') ? preg_replace('/w=\d+/', 'w=900', $src) : $src;
}

/* ---------- gallery ---------- */

function gallery_all(): array
{
    $items = json_read('gallery', null);
    if (!is_array($items)) {
        $items = default_gallery();
    }
    return array_values(array_filter($items, 'is_array'));
}

function gallery_featured(int $limit = 8): array
{
    $f = array_values(array_filter(gallery_all(), fn($i) => !empty($i['featured'])));
    return array_slice($f, 0, $limit);
}

function gallery_save(array $items): bool
{
    return json_write('gallery', array_values($items));
}

/* ---------- session / security ---------- */

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('mira_sid');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function csrf_token(): string
{
    start_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): bool
{
    start_session();
    $sent = (string) ($_POST['csrf'] ?? '');
    return !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $sent);
}

function client_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

/**
 * Simple file-based rate limit. Returns true when the action is allowed.
 * $bucket: e.g. 'login' or 'enquiry'.
 */
function rate_limit(string $bucket, int $max, int $windowSeconds): bool
{
    $all = json_read('ratelimit', []);
    $key = $bucket . ':' . hash('sha256', client_ip());
    $now = time();
    $hits = array_values(array_filter($all[$key] ?? [], fn($t) => $t > $now - $windowSeconds));
    // prune old keys so the file stays small
    foreach ($all as $k => $list) {
        if (!array_filter($list, fn($t) => $t > $now - 86400)) {
            unset($all[$k]);
        }
    }
    if (count($hits) >= $max) {
        $all[$key] = $hits;
        json_write('ratelimit', $all);
        return false;
    }
    $hits[] = $now;
    $all[$key] = $hits;
    json_write('ratelimit', $all);
    return true;
}

/* ---------- uploads ---------- */

/**
 * Validate and store an uploaded image. Re-encodes with GD when available
 * (strips metadata, caps size, makes a thumbnail). Returns [src, thumb, w, h] or throws.
 */
function store_uploaded_image(array $file, string $subdir): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException(upload_error_text((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)));
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Upload failed. Please try again.');
    }
    if ($file['size'] > MAX_UPLOAD_BYTES) {
        throw new RuntimeException('The image is larger than ' . (MAX_UPLOAD_BYTES / 1024 / 1024) . ' MB.');
    }
    $info = @getimagesize($file['tmp_name']);
    $allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($allowed[$info[2]])) {
        throw new RuntimeException('Only JPG, PNG or WebP images are allowed.');
    }

    $dir = UPLOAD_DIR . '/' . $subdir;
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $base = date('Ymd') . '-' . bin2hex(random_bytes(5));
    [$w, $h] = [$info[0], $info[1]];

    if (!function_exists('imagecreatetruecolor')) {
        // No GD on this host: keep the validated original as-is.
        $name = $base . '.' . $allowed[$info[2]];
        move_uploaded_file($file['tmp_name'], $dir . '/' . $name);
        $url = '/uploads/' . $subdir . '/' . $name;
        return [$url, $url, $w, $h];
    }

    $srcImg = match ($info[2]) {
        IMAGETYPE_JPEG => imagecreatefromjpeg($file['tmp_name']),
        IMAGETYPE_PNG  => imagecreatefrompng($file['tmp_name']),
        IMAGETYPE_WEBP => imagecreatefromwebp($file['tmp_name']),
    };
    if (!$srcImg) {
        throw new RuntimeException('This image could not be read.');
    }
    if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
        $srcImg = apply_exif_orientation($srcImg, $file['tmp_name']);
        [$w, $h] = [imagesx($srcImg), imagesy($srcImg)];
    }

    $useWebp = function_exists('imagewebp');
    $ext = $useWebp ? 'webp' : 'jpg';
    $full = resize_image($srcImg, IMAGE_MAX_WIDTH);
    $thumb = resize_image($srcImg, THUMB_WIDTH);
    save_image($full, "$dir/$base.$ext", $useWebp, 82);
    save_image($thumb, "$dir/$base-sm.$ext", $useWebp, 76);
    [$fw, $fh] = [imagesx($full), imagesy($full)];
    imagedestroy($srcImg);
    imagedestroy($full);
    imagedestroy($thumb);

    return ["/uploads/$subdir/$base.$ext", "/uploads/$subdir/$base-sm.$ext", $fw, $fh];
}

function resize_image($img, int $maxW)
{
    $w = imagesx($img);
    $h = imagesy($img);
    $nw = min($w, $maxW);
    $nh = (int) round($h * $nw / $w);
    $out = imagecreatetruecolor($nw, $nh);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    imagecopyresampled($out, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
    return $out;
}

function save_image($img, string $path, bool $webp, int $q): void
{
    $webp ? imagewebp($img, $path, $q) : imagejpeg($img, $path, $q);
}

function apply_exif_orientation($img, string $path)
{
    $exif = @exif_read_data($path);
    $o = (int) ($exif['Orientation'] ?? 1);
    $map = [3 => 180, 6 => -90, 8 => 90];
    return isset($map[$o]) ? (imagerotate($img, $map[$o], 0) ?: $img) : $img;
}

/** Delete an uploaded file pair; never touches files outside /uploads. */
function delete_upload(?string $url): void
{
    if (!$url || !str_starts_with($url, '/uploads/')) {
        return;
    }
    $path = realpath(BASE_DIR . $url);
    $root = realpath(UPLOAD_DIR);
    if ($path && $root && str_starts_with($path, $root . DIRECTORY_SEPARATOR) && is_file($path)) {
        unlink($path);
    }
}

function upload_error_text(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The image is too large for this server.',
        UPLOAD_ERR_PARTIAL => 'The upload was interrupted. Please try again.',
        UPLOAD_ERR_NO_FILE => 'Please choose an image.',
        default => 'Upload failed (code ' . $code . ').',
    };
}

/* ---------- enquiries ---------- */

function enquiries_all(): array
{
    return json_read('enquiries', []);
}

function enquiries_save(array $list): bool
{
    return json_write('enquiries', array_values($list));
}
