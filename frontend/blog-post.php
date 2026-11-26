<?php
/**
 * =============================================================================
 * MURLI CREATIONS - BLOG-POST VIEW
 * =============================================================================
 * Is page ka design, layout aur storefront logic yahan sthit hai.
 * Backend data: backend/config/functions.php
 * =============================================================================
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}
require_once ROOT_PATH . '/backend/config/functions.php';


$slug = $_GET['slug'] ?? '';
$post = get_blog_post_by_slug($slug);

if (!$post) {
    header('Location: /blog.php');
    exit;
}

$all_posts = get_all_blog_posts();
$related_posts = array_filter($all_posts, function($p) use ($post) {
    return $p['slug'] !== $post['slug'];
});
$related_posts = array_slice($related_posts, 0, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($post['title']); ?> | Murli Creations Blog</title>
  <meta name="description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/blog.css">
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main style="padding-top: 80px;">
    
    <!-- Article Header -->
    <article class="article-container">
      <div class="container article-narrow">
        
        <div class="article-breadcrumbs">
          <a href="/index.php">Home</a> &rarr; 
          <a href="/blog.php">Journal</a> &rarr; 
          <span><?php echo htmlspecialchars($post['category']); ?></span>
        </div>

        <span class="article-cat-badge"><?php echo htmlspecialchars($post['category']); ?></span>
        <h1 class="article-main-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        
        <div class="article-author-bar">
          <div class="author-info">
            <span class="author-name">By <?php echo htmlspecialchars($post['author']); ?></span>
            <span class="author-meta">• <?php echo htmlspecialchars($post['date']); ?> • <?php echo htmlspecialchars($post['read_time']); ?></span>
          </div>
          <a href="/shop.php" class="article-shop-pill">Explore Art Pieces &rarr;</a>
        </div>

        <!-- Featured Media Frame -->
        <div class="article-featured-image-wrap">
          <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="article-featured-img" onerror="this.onerror=null;this.src='/img-prodcut/IMG_3495.png';" />
        </div>

        <!-- Article Body Content -->
        <div class="article-body-content">
          <?php foreach ($post['content'] as $para): ?>
            <p class="article-paragraph"><?php echo htmlspecialchars($para); ?></p>
          <?php endforeach; ?>
        </div>

        <!-- Share & WhatsApp Consultation Box -->
        <div class="article-action-box">
          <div class="article-action-text">
            <h3>Love this traditional art piece?</h3>
            <p>Speak directly with our master craftsmen on WhatsApp for custom color palettes and sizes.</p>
          </div>
          <a href="https://wa.me/919999999999?text=<?php echo urlencode('Hello Murli Creations, I read your article: ' . $post['title']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
            <?php echo icon_svg('whatsapp', '', 16); ?> Inquire on WhatsApp
          </a>
        </div>

        <!-- Related Articles -->
        <?php if (!empty($related_posts)): ?>
          <div class="related-posts-section">
            <h3 class="related-title">More from the Artisan Journal</h3>
            <div class="related-grid">
              <?php foreach ($related_posts as $rel): ?>
                <div class="related-card">
                  <a href="/blog-post.php?slug=<?php echo urlencode($rel['slug']); ?>" class="related-img-link">
                    <img src="<?php echo htmlspecialchars($rel['image']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>" onerror="this.onerror=null;this.src='/img-prodcut/IMG_3495.png';" />
                  </a>
                  <div class="related-body">
                    <span class="related-cat"><?php echo htmlspecialchars($rel['category']); ?></span>
                    <h4><a href="/blog-post.php?slug=<?php echo urlencode($rel['slug']); ?>"><?php echo htmlspecialchars($rel['title']); ?></a></h4>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </article>

  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <?php include __DIR__ . '/includes/cart-drawer.php'; ?>
  <?php include __DIR__ . '/includes/floating-widgets.php'; ?>
  <?php include __DIR__ . '/includes/toast.php'; ?>

  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/main.js"></script>
</body>
</html>
