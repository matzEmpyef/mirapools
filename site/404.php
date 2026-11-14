<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

if (!headers_sent()) {
    http_response_code(404);
}
$page = [
    'title'       => 'Page not found',
    'description' => 'This page could not be found.',
    'path'        => '/404',
    'body'        => 'has-dark-hero',
];
require __DIR__ . '/includes/header.php';
?>
<section class="nf">
  <div class="caustics caustics-strong" aria-hidden="true"></div>
  <div class="wrap">
    <p class="display" data-reveal>404</p>
    <h1 class="display" style="font-size:clamp(2rem,4vw,3rem);margin-bottom:.5em" data-split>This end of the pool is <em>empty.</em></h1>
    <p data-reveal>The page you were looking for has moved or never existed. Let's get you back into the water.</p>
    <div class="hero-actions" style="justify-content:center;margin-top:30px" data-reveal>
      <a class="btn btn-sun" href="/">Go to the home page <?= arrow() ?></a>
      <a class="btn btn-ghost-light" href="/contact">Contact us</a>
    </div>
  </div>
  <?= wave_edge('paper') ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
