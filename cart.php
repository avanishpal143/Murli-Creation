<?php
require_once __DIR__ . '/config/functions.php';

$cart = get_session_cart();
$subtotal = get_cart_subtotal();

// Coupon processing
$coupon = $_GET['coupon'] ?? '';
$discount = 0;
$coupon_message = '';
$coupon_status = '';

if ($coupon === 'MURLI10') {
    $discount = $subtotal * 0.10;
    $coupon_message = 'Coupon MURLI10 Applied (10% OFF)';
    $coupon_status = 'promo-success';
} elseif ($coupon === 'WELCOME5') {
    $discount = $subtotal * 0.05;
    $coupon_message = 'Coupon WELCOME5 Applied (5% OFF)';
    $coupon_status = 'promo-success';
} elseif (!empty($coupon)) {
    $coupon_message = 'Invalid Coupon Code';
    $coupon_status = 'promo-error';
}

$shipping = ($subtotal >= 3000 || empty($cart)) ? 0 : 250;
$final_total = max(0, $subtotal - $discount + $shipping);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart | Murli Creations</title>
  <meta name="description" content="View your cart items, review order totals, and enter discount coupons on Murli Creations.">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/product-card.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/cart.css">
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main style="padding-top: 80px;">
    <div class="cart-page">
      <div class="container">
        <h1 class="cart-title">Your Gallery Cart</h1>

        <?php if (empty($cart)): ?>
          <div class="cart-empty-state">
            <div class="cart-empty-icon"><?php echo icon_svg('shopping-bag', '', 48); ?></div>
            <h2 class="cart-empty-title">Your Cart is Currently Empty</h2>
            <p class="cart-empty-text">
              You haven't selected any handcrafted wall pieces yet. Explore our authentic Lippan art murals and traditional Jharokhas to add character and warmth to your home.
            </p>
            <a href="/shop.php" class="cart-shop-btn">
              Browse Shop Catalog <?php echo icon_svg('arrow-right', '', 14); ?>
            </a>
          </div>
        <?php else: ?>
          <div class="cart-layout">
            <!-- Left: Cart Items List -->
            <div class="cart-section">
              <?php foreach ($cart as $item): 
                $p = $item['product'];
                $itemTotal = $p['price'] * $item['quantity'];
              ?>
                <div class="cart-item-row">
                  <div class="cart-item-image-box">
                    <img src="<?php echo htmlspecialchars($p['images'][0]); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" />
                  </div>

                  <div class="cart-item-info">
                    <span class="cart-item-cat"><?php echo htmlspecialchars($p['category']); ?></span>
                    <h3 class="cart-item-name"><?php echo htmlspecialchars($p['name']); ?></h3>
                    <div class="cart-item-price">₹<?php echo format_inr($p['price']); ?> each</div>
                  </div>

                  <div class="cart-item-controls">
                    <div class="cart-quantity-picker">
                      <button type="button" class="cart-qty-btn cart-page-qty-btn" data-product-id="<?php echo htmlspecialchars($p['id']); ?>" data-action="decrease" data-qty="<?php echo $item['quantity']; ?>">-</button>
                      <span class="cart-qty-val"><?php echo $item['quantity']; ?></span>
                      <button type="button" class="cart-qty-btn cart-page-qty-btn" data-product-id="<?php echo htmlspecialchars($p['id']); ?>" data-action="increase" data-qty="<?php echo $item['quantity']; ?>">+</button>
                    </div>
                    <button type="button" class="cart-remove-btn cart-page-remove-btn" data-product-id="<?php echo htmlspecialchars($p['id']); ?>">Remove</button>
                  </div>

                  <div class="cart-total-col">
                    ₹<?php echo format_inr($itemTotal); ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Right: Order Summary Sidebar -->
            <div class="cart-summary-section">
              <h3 class="cart-summary-title">Order Summary</h3>

              <div class="cart-row">
                <span>Subtotal (<?php echo count($cart); ?> items):</span>
                <span>₹<?php echo format_inr($subtotal); ?></span>
              </div>

              <?php if ($discount > 0): ?>
                <div class="cart-row">
                  <span>Coupon Discount:</span>
                  <span class="cart-discount-val">-₹<?php echo format_inr($discount); ?></span>
                </div>
              <?php endif; ?>

              <div class="cart-row">
                <span>Estimated Shipping:</span>
                <span>
                  <?php if ($shipping === 0): ?>
                    <strong style="color: var(--accent-sage);">FREE</strong>
                  <?php else: ?>
                    ₹<?php echo format_inr($shipping); ?>
                  <?php endif; ?>
                </span>
              </div>

              <?php if ($subtotal < 3000): ?>
                <p style="font-size: 0.75rem; color: var(--accent-gold-hover); margin-top: -6px;">
                  Add ₹<?php echo format_inr(3000 - $subtotal); ?> more for <strong>FREE Shipping</strong>!
                </p>
              <?php endif; ?>

              <!-- Coupon Code Box -->
              <div class="promo-box">
                <label for="coupon-input" class="promo-title">Have a Promo Coupon?</label>
                <form action="/cart.php" method="GET" class="promo-form">
                  <input
                    type="text"
                    id="coupon-input"
                    name="coupon"
                    placeholder="e.g. MURLI10"
                    class="promo-input"
                    value="<?php echo htmlspecialchars($coupon); ?>"
                  />
                  <button type="submit" class="promo-btn">Apply</button>
                </form>
                <?php if (!empty($coupon_message)): ?>
                  <div class="promo-message <?php echo $coupon_status; ?>">
                    <?php echo htmlspecialchars($coupon_message); ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="cart-row-bold">
                <span>Grand Total:</span>
                <span style="color: var(--accent-blue);">₹<?php echo format_inr($final_total); ?></span>
              </div>

              <a href="/checkout.php" class="cart-checkout-btn">
                Proceed to Checkout &rarr;
              </a>

              <a href="/shop.php" class="cart-continue-btn">
                ← Continue Shopping
              </a>
            </div>
          </div>
        <?php endif; ?>
      </div>
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
  <script src="/assets/js/shop.js"></script>
</body>
</html>
