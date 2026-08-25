<?php
require_once __DIR__ . '/config/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Page Not Found | Murli Creations</title>
  <meta name="description" content="The page you requested could not be found.">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/product-card.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main style="padding-top: 80px; min-height: 70vh; display: flex; align-items: center;">
    <div class="container" style="text-align: center; padding: 60px 20px;">
      <div style="font-size: 5rem; line-height: 1; margin-bottom: 16px;">🦚</div>
      <h1 style="font-family: var(--font-headings); font-size: 2.5rem; color: var(--text-primary); margin-bottom: 8px;">
        404 - Creation Not Found
      </h1>
      <p style="color: var(--text-secondary); max-width: 480px; margin: 0 auto 24px auto; font-size: 1.05rem;">
        The requested wall art piece or page does not exist in our studio archive. Explore our handcrafted collection in the catalog.
      </p>
      <a href="/shop.php" style="display: inline-block; background-color: var(--accent-blue); color: var(--bg-secondary); padding: 12px 32px; border-radius: var(--radius-full); font-weight: 700; font-size: 0.95rem;">
        Return to Catalog &rarr;
      </a>
    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <?php include __DIR__ . '/includes/cart-drawer.php'; ?>
  <?php include __DIR__ . '/includes/floating-widgets.php'; ?>
  <?php include __DIR__ . '/includes/toast.php'; ?>

  <script src="/assets/js/tilt.js"></script>
  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/main.js"></script>
  <script src="/assets/js/cart.js"></script>
</body>
</html>
