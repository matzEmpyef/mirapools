<?php
// Kept in old PHP syntax on purpose: it must run, and explain, on servers with outdated PHP.
if (PHP_VERSION_ID < 80100) {
    http_response_code(503);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<meta name="robots" content="noindex"><title>PHP update needed</title></head>'
        . '<body style="font-family:system-ui,sans-serif;max-width:560px;margin:12vh auto;padding:0 20px;color:#0a2328;line-height:1.6">'
        . '<h1 style="font-size:1.5rem">This website needs a newer PHP version</h1>'
        . '<p>It needs PHP 8.1 or newer, but this server is running PHP ' . htmlspecialchars(PHP_VERSION) . '.</p>'
        . '<p>In cPanel, open <b>Select PHP Version</b> or <b>MultiPHP Manager</b>, choose 8.1 or newer for this domain, and reload this page.</p>'
        . '</body></html>';
    exit;
}
