<?php
/**
 * =============================================================================
 * MURLI CREATIONS - BLOG VIEW
 * =============================================================================
 * Is page ka design, layout aur storefront logic yahan sthit hai.
 * Backend data: backend/config/functions.php
 * =============================================================================
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}
require_once ROOT_PATH . '/backend/config/functions.php';

$posts = get_all_blog_posts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Artisan Journal & Vastu Living Blog | Murli Creations</title>
  <meta name="description" content="Discover guides on Lippan art history, sacred Vastu mandala wall placements, peacock symbolism, and handcrafted clay relief care from Murli Creations.">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/blog.css">
</head>
<body>
  <?php include ROOT_PATH . '/frontend/components/header.php'; ?>

  <main style="padding-top: 80px;">
    
    <!-- Hero Header -->
    <section class="blog-hero-section">
      <div class="container text-center">
        <span class="blog-badge reveal-on-scroll">📖 Artisan Chronicles & Vastu Wisdom</span>
        <h1 class="blog-main-title reveal-on-scroll">The <span class="gradient-text">Murli Creations</span> Journal</h1>
        <p class="blog-lead reveal-on-scroll">
          Explore the rich heritage of Kutchi Lippan Kaam, sacred geometric Mandalas, directional Vastu energies, and time-honored studio crafts.
        </p>
      </div>
    </section>

    <!-- Blog Grid Section -->
    <section class="blog-grid-section">
      <div class="container">
        
        <div class="blog-grid">
          <?php foreach ($posts as $index => $post): ?>
            <article class="blog-card reveal-on-scroll stagger-<?php echo ($index % 4) + 1; ?>">
              <div class="blog-card-image-wrap">
                <a href="/blog-post.php?slug=<?php echo urlencode($post['slug']); ?>">
                  <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="blog-card-img" loading="lazy" onerror="this.onerror=null;this.src='/img-prodcut/IMG_3495.png';" />
                </a>
                <span class="blog-card-cat"><?php echo htmlspecialchars($post['category']); ?></span>
              </div>

              <div class="blog-card-body">
                <div class="blog-card-meta">
                  <span>🗓️ <?php echo htmlspecialchars($post['date']); ?></span>
                  <span>⏱️ <?php echo htmlspecialchars($post['read_time']); ?></span>
                </div>

                <h2 class="blog-card-title">
                  <a href="/blog-post.php?slug=<?php echo urlencode($post['slug']); ?>">
                    <?php echo htmlspecialchars($post['title']); ?>
                  </a>
                </h2>

                <p class="blog-card-excerpt">
                  <?php echo htmlspecialchars($post['excerpt']); ?>
                </p>

                <div class="blog-card-footer">
                  <span class="blog-author">By <?php echo htmlspecialchars($post['author']); ?></span>
                  <a href="/blog-post.php?slug=<?php echo urlencode($post['slug']); ?>" class="blog-read-link">
                    <span>Read Guide</span> <?php echo icon_svg('arrow-right', '', 13); ?>
                  </a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <!-- Newsletter / CTA Card -->
        <div class="blog-cta-card reveal-on-scroll">
          <div class="blog-cta-content">
            <h3>Looking for Custom Wall Dimensions or Sacred Mandalas?</h3>
            <p>Our master craftsmen design custom Lippan wall panels tailored to your living room and Mandir Vastu coordinates.</p>
          </div>
          <div class="blog-cta-action">
            <a href="/contact.php" class="btn btn-primary">Request Studio Consultation &rarr;</a>
          </div>
        </div>

      </div>
    </section>

  </main>

  <?php include ROOT_PATH . '/frontend/components/footer.php'; ?>
  <?php include ROOT_PATH . '/frontend/components/cart-drawer.php'; ?>
  <?php include ROOT_PATH . '/frontend/components/floating-widgets.php'; ?>
  <?php include ROOT_PATH . '/frontend/components/toast.php'; ?>

  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/main.js"></script>
</body>
</html>
