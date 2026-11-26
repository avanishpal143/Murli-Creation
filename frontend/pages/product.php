<?php
/**
 * =============================================================================
 * MURLI CREATIONS - PRODUCT VIEW
 * =============================================================================
 * Is page ka design, layout aur storefront logic yahan sthit hai.
 * Backend data: backend/config/functions.php
 * =============================================================================
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}
require_once ROOT_PATH . '/backend/config/functions.php';

require_once ROOT_PATH . '/frontend/components/product-card.php';

$slug = $_GET['slug'] ?? '';
$id = $_GET['id'] ?? '';
$product = $slug ? get_product_by_slug($slug) : ($id ? get_product_by_id($id) : null);
if (!$product && $id) {
    $product = get_product_by_id($id);
}

if (!$product) {
    // If not found, redirect to shop
    header('Location: /shop.php');
    exit;
}

$all_products = get_all_products();
$related_products = array_slice(array_filter($all_products, function($p) use ($product) {
    return $p['id'] !== $product['id'] && $p['category'] === $product['category'];
}), 0, 3);

if (count($related_products) < 3) {
    $extra_products = array_slice(array_filter($all_products, function($p) use ($product, $related_products) {
        return $p['id'] !== $product['id'] && !in_array($p['id'], array_column($related_products, 'id'));
    }), 0, 3 - count($related_products));
    $related_products = array_merge($related_products, $extra_products);
}

$reviews = get_product_reviews($product['id']);
$mrpVal = isset($product['mrp']) && $product['mrp'] > 0 ? $product['mrp'] : round($product['price'] * 1.3);
$ratingVal = isset($product['rating']) ? $product['rating'] : 4.8;
$reviewsCountVal = count($reviews) > 0 ? count($reviews) : ($product['reviewsCount'] ?? 15);
$discountPercent = round((($mrpVal - $product['price']) / $mrpVal) * 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($product['name']); ?> | Murli Creations</title>
  <meta name="description" content="<?php echo htmlspecialchars(substr($product['description'], 0, 150)); ?>...">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/product-card.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/product.css">
</head>
<body>
  <?php include ROOT_PATH . '/frontend/components/header.php'; ?>

  <main style="padding-top: 80px;">
    <div class="product-detail-page">
      <div class="container">
        <!-- Breadcrumb Navigation -->
        <nav class="product-breadcrumbs">
          <a href="/index.php">Home</a> &nbsp;/&nbsp;
          <a href="/shop.php">Shop</a> &nbsp;/&nbsp;
          <a href="/shop.php?category=<?php echo urlencode($product['category']); ?>"><?php echo htmlspecialchars($product['category']); ?></a> &nbsp;/&nbsp;
          <span style="color: var(--text-primary); font-weight: 700;"><?php echo htmlspecialchars($product['name']); ?></span>
        </nav>

        <div class="product-layout">
          <!-- Left Column: Media Gallery -->
          <div class="product-gallery">
            <div class="main-media-container" id="main-media-container" title="Click to view full picture" role="button" tabindex="0" aria-label="View full product image">
              <img
                id="main-product-img"
                src="<?php echo htmlspecialchars($product['images'][0]); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>"
                class="main-media-image"
              />
              <button type="button" class="main-media-zoom-btn" id="main-media-zoom-trigger" aria-label="View full picture">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="15 3 21 3 21 9"></polyline>
                  <polyline points="9 21 3 21 3 15"></polyline>
                  <line x1="21" y1="3" x2="14" y2="10"></line>
                  <line x1="3" y1="21" x2="10" y2="14"></line>
                </svg>
                <span>Full View</span>
              </button>
            </div>

            <!-- Thumbnail Reel -->
            <div class="product-thumbnails">
              <?php foreach ($product['images'] as $idx => $img): ?>
                <button
                  type="button"
                  class="thumbnail-btn <?php echo ($idx === 0) ? 'active' : ''; ?>"
                  data-type="image"
                  data-src="<?php echo htmlspecialchars($img); ?>"
                  aria-label="View product image <?php echo ($idx + 1); ?>"
                >
                  <img src="<?php echo htmlspecialchars($img); ?>" alt="Thumbnail <?php echo ($idx + 1); ?>" class="thumbnail-image">
                </button>
              <?php endforeach; ?>

              <?php if (!empty($product['video'])): ?>
                <button
                  type="button"
                  class="thumbnail-btn"
                  data-type="video"
                  data-src="<?php echo htmlspecialchars($product['video']); ?>"
                  aria-label="View product craft process video"
                >
                  <img src="<?php echo htmlspecialchars($product['images'][0]); ?>" alt="Video Thumbnail" class="thumbnail-image">
                  <span class="product-video-badge">▶</span>
                </button>
              <?php endif; ?>
            </div>
          </div>

          <!-- Right Column: Info & Purchase -->
          <div class="product-info">
            <span class="product-meta-category"><?php echo htmlspecialchars($product['category']); ?></span>
            
            <div class="product-rating-row">
              <a href="#reviews-section" class="product-rating-link" title="Jump to customer reviews and feedback">
                <span class="product-stars">
                  <?php echo icon_svg('star', 'star-gold', 14); ?> <?php echo $ratingVal; ?>
                </span>
                <span class="product-rating-count">(<?php echo $reviewsCountVal; ?> Customer Reviews)</span>
              </a>
              <span class="product-artist-name">• Handcrafted by <?php echo htmlspecialchars($product['artist'] ?? 'Master Craftsman Murli Prasad'); ?></span>
            </div>

            <h1 class="product-name-title"><?php echo htmlspecialchars($product['name']); ?></h1>

            <div class="product-price-container">
              <span class="product-price-val">₹<?php echo format_inr($product['price']); ?></span>
              <span class="product-mrp-val">₹<?php echo format_inr($mrpVal); ?></span>
              <span class="product-discount-badge"><?php echo $discountPercent; ?>% OFF</span>
            </div>

            <p class="product-description-text">
              <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>

            <div class="product-specs-table">
              <?php if (!empty($product['code'])): ?>
                <div class="product-spec-row">
                  <span class="product-spec-label">Product Code (SKU)</span>
                  <span class="product-spec-val" style="font-weight: 700; color: var(--gold-accent, #c99846); letter-spacing: 0.5px;"><?php echo htmlspecialchars($product['code']); ?></span>
                </div>
              <?php endif; ?>
              <div class="product-spec-row">
                <span class="product-spec-label">Dimensions</span>
                <span class="product-spec-val"><?php echo htmlspecialchars($product['size'] ?? '18 x 18 inches'); ?></span>
              </div>
              <div class="product-spec-row">
                <span class="product-spec-label">Artisan Materials</span>
                <span class="product-spec-val"><?php echo htmlspecialchars($product['material'] ?? 'Clay Relief, MDF Board, Shimmer Mirrors'); ?></span>
              </div>
              <div class="product-spec-row">
                <span class="product-spec-label">Craft Origin</span>
                <span class="product-spec-val"><?php echo htmlspecialchars($product['origin'] ?? 'Delhi, India'); ?></span>
              </div>
              <div class="product-spec-row">
                <span class="product-spec-label">Inventory Status</span>
                <span class="product-spec-val">
                  <?php if (($product['stock'] ?? 0) > 0): ?>
                    <?php if ((int)$product['stock'] === 1): ?>
                      <span style="color: #b45309; font-weight: 700; background: #fef3c7; padding: 4px 12px; border-radius: 12px; border: 1px solid #fde68a; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 4px;">
                        ● In stock - Only 1 left
                      </span>
                    <?php elseif ((int)$product['stock'] <= 3): ?>
                      <span style="color: #b45309; font-weight: 700; background: #fef3c7; padding: 4px 12px; border-radius: 12px; border: 1px solid #fde68a; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 4px;">
                        ● In stock - Only <?php echo (int)$product['stock']; ?> left
                      </span>
                    <?php else: ?>
                      <span style="color: #15803d; font-weight: 700; background: #dcfce7; padding: 4px 12px; border-radius: 12px; border: 1px solid #bbf7d0; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 4px;">
                        ● In Stock (<?php echo (int)$product['stock']; ?> units available)
                      </span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span style="color: #b91c1c; font-weight: 700; background: #fee2e2; padding: 4px 12px; border-radius: 12px; border: 1px solid #fecaca; font-size: 0.82rem;">
                      ● Sold Out
                    </span>
                  <?php endif; ?>
                </span>
              </div>
            </div>

            <!-- Purchase Controls -->
            <div class="product-purchase-section">
              <?php if (($product['stock'] ?? 0) > 0): ?>
                <div class="product-qty-picker">
                  <button type="button" id="purchase-qty-dec" class="product-qty-btn" aria-label="Decrease quantity">-</button>
                  <span id="purchase-quantity-val" class="product-qty-val" data-max-stock="<?php echo (int)$product['stock']; ?>">1</span>
                  <button type="button" id="purchase-qty-inc" class="product-qty-btn" aria-label="Increase quantity">+</button>
                </div>

                <button
                  type="button"
                  id="product-page-add-btn"
                  class="product-add-btn"
                  data-product-id="<?php echo htmlspecialchars($product['id']); ?>"
                >
                  <?php echo icon_svg('shopping-bag', '', 16); ?> Add Piece to Cart
                </button>
              <?php else: ?>
                <div style="width: 100%; display: flex; flex-direction: column; gap: 8px;">
                  <button
                    type="button"
                    class="product-add-btn"
                    disabled
                    style="background: #e5e7eb; color: #6b7280; cursor: not-allowed; border: 1px solid #d1d5db; box-shadow: none;"
                  >
                    Currently Sold Out
                  </button>
                  <a href="https://wa.me/919582316454?text=<?php echo urlencode("Namaste Murli Creations, I noticed that " . $product['name'] . " (SKU: " . ($product['code'] ?? '') . ") is Sold Out. Can you custom handcraft a similar bespoke piece for me?"); ?>" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #25D366; color: #fff; font-weight: 700; padding: 12px 18px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(37,211,102,0.25);">
                    <span>Request Bespoke Handcrafted Piece on WhatsApp</span> <?php echo icon_svg('send', '', 14); ?>
                  </a>
                </div>
              <?php endif; ?>
            </div>

            <!-- Accordions -->
            <div class="product-accordions">
              <?php if (!empty($product['folklore'])): ?>
                <div class="product-accordion open">
                  <button type="button" class="accordion-header-btn">
                    <span>Folklore & Devotional Significance</span>
                    <span class="accordion-header-icon"><?php echo icon_svg('plus', '', 16); ?></span>
                  </button>
                  <div class="accordion-content-panel">
                    <p><?php echo nl2br(htmlspecialchars($product['folklore'])); ?></p>
                  </div>
                </div>
              <?php endif; ?>

              <div class="product-accordion">
                <button type="button" class="accordion-header-btn">
                  <span>Care & Maintenance Instructions</span>
                  <span class="accordion-header-icon"><?php echo icon_svg('plus', '', 16); ?></span>
                </button>
                <div class="accordion-content-panel">
                  <p>
                    • Clean gently using a soft, dry micro-fiber cloth or feather duster.<br />
                    • Do not spray liquid cleaners directly on clay relief or wooden frame surfaces.<br />
                    • For embedded mirrors, lightly dampen a cotton swab with glass cleaner to polish.<br />
                    • Suitable for indoor living rooms, pooja mandirs, and sheltered entrances. Avoid direct rainfall exposure.
                  </p>
                </div>
              </div>

              <div class="product-accordion">
                <button type="button" class="accordion-header-btn">
                  <span>Safe Shipping & Transit Guarantee</span>
                  <span class="accordion-header-icon"><?php echo icon_svg('plus', '', 16); ?></span>
                </button>
                <div class="accordion-content-panel">
                  <p>
                    • All artworks are protected with multi-layer high-density cushioning and sturdy reinforced packaging.<br />
                    • Transit replacement policy: In the rare event of damage or mirror crack during transit, we dispatch a brand new piece at no extra charge.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Reviews Section -->
        <section class="reviews-section" id="reviews-section">
          <h3 class="reviews-title">Patron Reviews & Reflections</h3>
          
          <div class="reviews-layout">
            <!-- Left Scorecard -->
            <div class="reviews-summary">
              <div class="summary-card">
                <div class="summary-card-title">Overall Score</div>
                <div class="summary-score"><?php echo $ratingVal; ?></div>
                <div class="summary-stars">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="<?php echo ($i <= round($ratingVal)) ? 'star-active' : 'star-inactive'; ?>">
                      <?php echo icon_svg('star', '', 18); ?>
                    </span>
                  <?php endfor; ?>
                </div>
                <div class="summary-count">Based on <?php echo $reviewsCountVal; ?> verified reviews</div>
              </div>
            </div>

            <!-- Right Reviews Feed & Form -->
            <div class="reviews-feed">
              <div class="reviews-list-header">
                <h4>Recent Feedback</h4>
              </div>

              <div class="reviews-list">
                <?php if (empty($reviews)): ?>
                  <p class="no-reviews-text">No reviews recorded yet for this design. Be the first patron to share your thoughts!</p>
                <?php else: ?>
                  <?php foreach ($reviews as $rev): ?>
                    <div class="review-card">
                      <div class="review-card-header">
                        <div class="review-info">
                          <span class="review-user-name"><?php echo htmlspecialchars($rev['userName']); ?></span>
                          <span class="review-date-val"><?php echo htmlspecialchars($rev['date']); ?></span>
                        </div>
                        <div class="review-stars-list">
                          <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="<?php echo ($i <= $rev['rating']) ? 'star-active' : 'star-inactive'; ?>">
                              <?php echo icon_svg('star', '', 14); ?>
                            </span>
                          <?php endfor; ?>
                        </div>
                      </div>
                      <p class="review-comment-val">"<?php echo htmlspecialchars($rev['comment']); ?>"</p>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>

              <!-- Write a Review Form -->
              <div class="write-review-container">
                <h4>Add Your Review</h4>
                <p class="write-review-sub">Share your feedback on craftsmanship, texture, and visual impact in your home.</p>

                <form id="art-review-form" class="review-form" data-product-id="<?php echo htmlspecialchars($product['id']); ?>">
                  <div class="review-form-row">
                    <div class="review-form-group">
                      <label for="reviewerName">Your Name</label>
                      <input type="text" id="reviewerName" class="review-form-input" placeholder="e.g. Radhika Sharma" required />
                    </div>

                    <div class="review-form-group">
                      <label>Rating</label>
                      <div class="star-selector">
                        <button type="button" class="star-btn active" data-rating="1">★</button>
                        <button type="button" class="star-btn active" data-rating="2">★</button>
                        <button type="button" class="star-btn active" data-rating="3">★</button>
                        <button type="button" class="star-btn active" data-rating="4">★</button>
                        <button type="button" class="star-btn active" data-rating="5">★</button>
                      </div>
                      <input type="hidden" id="review-rating-input" value="5" />
                    </div>
                  </div>

                  <div class="review-form-group">
                    <label for="reviewerComment">Review Message</label>
                    <textarea id="reviewerComment" class="review-form-textarea" rows="3" placeholder="Describe the finish, mirror reflections, and packaging..." required></textarea>
                  </div>

                  <button type="submit" class="submit-review-btn">
                    Submit Review
                  </button>
                </form>
              </div>
            </div>
          </div>
        </section>

        <!-- Related Pieces -->
        <?php if (!empty($related_products)): ?>
          <section class="product-related-section">
            <h3 class="related-title">You May Also Cherish</h3>
            <div class="related-grid">
              <?php foreach ($related_products as $rel): ?>
                <div>
                  <?php render_product_card($rel); ?>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php include ROOT_PATH . '/frontend/components/footer.php'; ?>
  <?php include ROOT_PATH . '/frontend/components/cart-drawer.php'; ?>
  <?php include ROOT_PATH . '/frontend/components/floating-widgets.php'; ?>
  <?php include ROOT_PATH . '/frontend/components/toast.php'; ?>

  <!-- Full-Screen Product Lightbox Modal -->
  <div id="product-lightbox" class="product-lightbox-modal" aria-hidden="true" role="dialog" aria-label="Product Image Viewer">
    <button type="button" class="lightbox-close-btn" id="lightbox-close" aria-label="Close full view">&times;</button>
    <?php if (count($product['images']) > 1): ?>
      <button type="button" class="lightbox-nav-btn lightbox-nav-prev" id="lightbox-prev" aria-label="Previous image">&#10094;</button>
      <button type="button" class="lightbox-nav-btn lightbox-nav-next" id="lightbox-next" aria-label="Next image">&#10095;</button>
    <?php endif; ?>
    <div class="lightbox-content-wrap">
      <img id="lightbox-img" src="<?php echo htmlspecialchars($product['images'][0]); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="lightbox-main-img" />
    </div>
    <div class="lightbox-footer-info">
      <span><?php echo htmlspecialchars($product['name']); ?></span>
      <span class="lightbox-counter" id="lightbox-counter">1 of <?php echo count($product['images']); ?></span>
    </div>
  </div>

  <script>
    window.PRODUCT_IMAGES = <?php echo json_encode(array_values($product['images'] ?? [])); ?>;
    window.PRODUCT_NAME = <?php echo json_encode($product['name'] ?? ''); ?>;
  </script>
  <script src="/assets/js/tilt.js"></script>
  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/main.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/product.js"></script>
</body>
</html>
