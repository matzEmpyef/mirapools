<?php
/**
 * Site configuration. Edit SITE_URL before going live.
 * Contact details and images are edited from /admin, not here.
 */

declare(strict_types=1);

// Public address of the site, no trailing slash. Used for canonical URLs, sitemap and social cards.
define('SITE_URL', getenv('MIRA_SITE_URL') ?: 'https://mirapools.in');

// One-time key needed to create the first admin password at /admin.
// Left empty in the code on purpose: tools/package.py fills in a fresh random key
// (saved to dist/SETUP_KEY.txt). Setup is refused while this is empty.
define('ADMIN_SETUP_KEY', '');

// Where enquiry e-mails go (leave empty to only store enquiries in the admin panel).
define('ENQUIRY_MAIL_TO', 'info@mirapools.in');

define('BASE_DIR', dirname(__DIR__));
define('DATA_DIR', BASE_DIR . '/data');
define('UPLOAD_DIR', BASE_DIR . '/uploads');

define('MAX_UPLOAD_BYTES', 12 * 1024 * 1024);
define('IMAGE_MAX_WIDTH', 2200);
define('THUMB_WIDTH', 760);

date_default_timezone_set('Asia/Kolkata');

if (getenv('MIRA_DEBUG')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}
