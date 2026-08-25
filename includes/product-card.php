<?php
// Myntra-Style Premium Product Card Template
// Expects $product array

if (!isset($product)) {
    return;
}

$mrp = $product['mrp'] ?? round($product['price'] * 1.3);
$discount = round((($mrp - $product['price']) / $mrp) * 100);
$rating = $product['rating'] ?? 4.8;
$reviewsCount = $product['reviewsCount'] ?? 15;
$inStock = ($product['stock'] ?? 1) > 0;
$primaryImg = $product['images'][0] ?? '/img-prodcut/IMG_4041.png';
$secondaryImg = $product['images'][1] ?? $primaryImg;
$productUrl = '/product.php?slug=' . urlencode($product['slug']);
$productCode = $product['code'] ?? '';
?>
<div class="myntra-card-wrapper">
  <a href="<?php echo $productUrl; ?>" class="myntra-product-card" aria-label="<?php echo htmlspecialchars($product['name']); ?>">
    
    <!-- Image Box with Floating Badges & Quick Actions -->
    <div class="myntra-img-box">
      <img 
        src="<?php echo htmlspecialchars($primaryImg); ?>" 
        alt="<?php echo htmlspecialchars($product['name']); ?>" 
        class="myntra-img primary"
        loading="lazy"
      />
      <?php if ($secondaryImg !== $primaryImg): ?>
        <img 
          src="<?php echo htmlspecialchars($secondaryImg); ?>" 
          alt="<?php echo htmlspecialchars($product['name']); ?> alternate angle" 
          class="myntra-img secondary"
          loading="lazy"
        />
      <?php endif; ?>

      <!-- Top Badges -->
      <div class="myntra-top-badges">
        <?php if (!$inStock): ?>
          <span class="myntra-badge sold-out">Sold Out</span>
        <?php elseif (!empty($product['bestseller'])): ?>
          <span class="myntra-badge bestseller">Bestseller</span>
        <?php elseif (!empty($product['featured'])): ?>
          <span class="myntra-badge featured">Featured</span>
        <?php endif; ?>
      </div>

      <!-- Floating Rating Pill (Bottom Left of Image like Myntra) -->
      <div class="myntra-rating-pill">
        <span class="pill-rating"><?php echo number_format($rating, 1); ?></span>
        <span class="pill-star">★</span>
        <span class="pill-divider">|</span>
        <span class="pill-count"><?php echo $reviewsCount; ?></span>
      </div>
    </div>

    <!-- Product Metadata (Clean, modern typography) -->
    <div class="myntra-info-box">
      <div class="myntra-brand-row">
        <?php if (!empty($productCode)): ?>
          <span class="myntra-code-tag" style="font-size: 0.72rem; font-weight: 700; color: var(--gold-accent, #c99846); background: rgba(201, 152, 70, 0.12); padding: 2px 6px; border-radius: 4px; border: 1px solid rgba(201, 152, 70, 0.25);"><?php echo htmlspecialchars($productCode); ?></span>
        <?php endif; ?>
        <span class="myntra-cat-tag"><?php echo htmlspecialchars($product['category']); ?></span>
      </div>

      <h3 class="myntra-title"><?php echo htmlspecialchars($product['name']); ?></h3>

      <!-- Price details row -->
      <div class="myntra-price-row">
        <span class="myntra-price">₹<?php echo number_format($product['price']); ?></span>
        <span class="myntra-mrp">₹<?php echo number_format($mrp); ?></span>
        <span class="myntra-discount">(<?php echo $discount; ?>% OFF)</span>
      </div>

      <!-- Action Button: Buy Now -->
      <div class="myntra-card-actions">
        <button 
          type="button" 
          class="myntra-buy-now-btn"
          onclick="event.preventDefault(); event.stopPropagation(); addToCart('<?php echo htmlspecialchars($product['id']); ?>', 1);"
          <?php echo !$inStock ? 'disabled' : ''; ?>
          aria-label="Buy <?php echo htmlspecialchars($product['name']); ?>"
        >
          <?php echo icon_svg('shopping-bag', '', 14); ?>
          <span><?php echo $inStock ? 'Buy Now' : 'Sold Out'; ?></span>
        </button>
      </div>
    </div>

  </a>
</div>
