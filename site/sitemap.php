<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=UTF-8');

$galleryFile = data_path('gallery');
$galleryMod = is_file($galleryFile) ? filemtime($galleryFile) : filemtime(__FILE__);
$urls = [
    ['/', filemtime(__DIR__ . '/index.php'), '1.0'],
    ['/about', filemtime(__DIR__ . '/about.php'), '0.8'],
    ['/services', filemtime(__DIR__ . '/services.php'), '0.9'],
    ['/gallery', $galleryMod, '0.8'],
    ['/contact', filemtime(__DIR__ . '/contact.php'), '0.7'],
];
$contentMod = filemtime(__DIR__ . '/includes/content.php');
foreach (array_keys(services()) as $slug) {
    $urls[] = ['/services/' . $slug, $contentMod, '0.8'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as [$path, $mod, $prio]) {
    echo '  <url><loc>' . e(abs_url($path)) . '</loc><lastmod>' . date('Y-m-d', (int) $mod) . '</lastmod><priority>' . $prio . "</priority></url>\n";
}
echo "</urlset>\n";
