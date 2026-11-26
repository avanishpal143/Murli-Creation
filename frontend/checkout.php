<?php
/**
 * =============================================================================
 * MURLI CREATIONS - CHECKOUT VIEW
 * =============================================================================
 * Is page ka design, layout aur storefront logic yahan sthit hai.
 * Backend data: backend/config/functions.php
 * =============================================================================
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}
require_once ROOT_PATH . '/backend/config/functions.php';


$cart = get_session_cart();
$subtotal = get_cart_subtotal();

if (empty($cart)) {
    header('Location: /cart.php');
    exit;
}

$settings = get_site_settings();
$freeThreshold = (float)($settings['shipping']['free_threshold'] ?? 2500);
$standardCharge = (float)($settings['shipping']['standard_charge'] ?? 199);

$appliedCoupon = $_SESSION['mc_applied_coupon'] ?? null;
$discountAmount = 0;
$couponCode = '';

if ($appliedCoupon && !empty($appliedCoupon['valid'])) {
    $couponCode = $appliedCoupon['code'];
    $discountAmount = (float)($appliedCoupon['discount'] ?? 0);
}

$effectiveSubtotal = max(0, $subtotal - $discountAmount);
$shipping = ($effectiveSubtotal >= $freeThreshold) ? 0 : $standardCharge;
$final_total = $effectiveSubtotal + $shipping;

$tokenAmount = min(500, $final_total);
$balanceOnDelivery = max(0, $final_total - $tokenAmount);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout | Murli Creations</title>
  <meta name="description" content="Secure checkout with Delhi NCR delivery, Partial COD with ₹500 token deposit, and Razorpay for Murli Creations.">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/product-card.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/checkout.css">

  <!-- Razorpay Standard Checkout Script -->
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main style="padding-top: 80px;">
    <div class="checkout-page">
      <div class="container">
        
        <!-- 3-Step Wizard Navigation Indicator -->
        <div class="steps-header">
          <div id="step-indicator-1" class="step-indicator active">
            1
            <span class="step-text active">1. Shipping</span>
          </div>
          <div id="step-indicator-2" class="step-indicator">
            2
            <span class="step-text">2. Payment</span>
          </div>
          <div id="step-indicator-3" class="step-indicator">
            3
            <span class="step-text">3. Confirm</span>
          </div>
        </div>

        <div class="checkout-layout">
          <!-- Left: Multi-Step Forms -->
          <div>
            <!-- Step 1: Shipping Address Form -->
            <div id="step-1-panel" class="checkout-form-section">
              <div class="checkout-section-header">
                <h2 class="checkout-section-title">Shipping & Contact Details</h2>
                <span class="checkout-location-pill">📍 Exclusively Delhi & NCR</span>
              </div>

              <!-- Location Notice Alert -->
              <div class="checkout-alert-box">
                <div class="alert-icon"><?php echo icon_svg('truck', '', 18); ?></div>
                <div class="alert-content">
                  <strong>Service Area Notice:</strong> Handcrafted creations are currently delivered exclusively within <strong>Delhi & NCR</strong>.
                </div>
              </div>
              
              <div class="checkout-form-grid">
                <div class="checkout-input-group checkout-full-width">
                  <label class="checkout-label" for="checkout-name">Full Name *</label>
                  <input type="text" id="checkout-name" class="checkout-input" placeholder="e.g. Radhika Sharma" />
                  <span id="error-name" class="checkout-error-text"></span>
                </div>

                <div class="checkout-input-group">
                  <label class="checkout-label" for="checkout-email">Email Address (for Order Receipt) *</label>
                  <input type="email" id="checkout-email" class="checkout-input" placeholder="name@domain.com" />
                  <span id="error-email" class="checkout-error-text"></span>
                </div>

                <div class="checkout-input-group">
                  <label class="checkout-label" for="checkout-phone">Mobile Number (10 Digits for Courier WhatsApp) *</label>
                  <input type="tel" id="checkout-phone" class="checkout-input" placeholder="9876543210" maxlength="10" />
                  <span id="error-phone" class="checkout-error-text"></span>
                </div>

                <div class="checkout-input-group checkout-full-width">
                  <label class="checkout-label" for="checkout-address">Delivery Address (House / Street / Landmark) *</label>
                  <input type="text" id="checkout-address" class="checkout-input" placeholder="Flat 402, Lotus Towers, Model Town" />
                  <span id="error-address" class="checkout-error-text"></span>
                </div>

                <div class="checkout-input-group">
                  <label class="checkout-label" for="checkout-city">City / District *</label>
                  <input type="text" id="checkout-city" class="checkout-input" placeholder="e.g. Delhi / Gurugram / Noida" />
                  <span id="error-city" class="checkout-error-text"></span>
                </div>

                <div class="checkout-input-group">
                  <label class="checkout-label" for="checkout-state">NCR State / Region *</label>
                  <select id="checkout-state" class="checkout-select">
                    <option value="Delhi">Delhi (All Districts)</option>
                    <option value="Haryana (NCR)">Haryana (Gurugram, Faridabad, Sonipat)</option>
                    <option value="Uttar Pradesh (NCR)">Uttar Pradesh (Noida, Greater Noida, Ghaziabad, Meerut)</option>
                    <option value="Rajasthan (NCR)">Rajasthan (Alwar, Bharatpur NCR)</option>
                  </select>
                </div>

                <div class="checkout-input-group">
                  <label class="checkout-label" for="checkout-pin">PIN Code (6 Digits) *</label>
                  <input type="text" id="checkout-pin" class="checkout-input" placeholder="124001" maxlength="6" />
                  <span id="error-pin" class="checkout-error-text"></span>
                </div>
              </div>

              <div class="checkout-button-row">
                <div></div>
                <button type="button" id="to-step-2-btn" class="checkout-next-btn">
                  Continue to Payment &rarr;
                </button>
              </div>
            </div>

            <!-- Step 2: Payment Method Selection -->
            <div id="step-2-panel" class="checkout-form-section" style="display: none;">
              <h2 class="checkout-section-title">Select Payment Method</h2>

              <div class="payment-options">
                <!-- Option 1: Partial COD (Advance Token Deposit ₹500) -->
                <div class="payment-card selected" id="payment-card-partial-cod">
                  <input type="radio" id="payment-partial-cod" name="payment-method-choice" value="Partial COD" class="payment-radio" checked />
                  <div class="payment-details">
                    <div class="payment-title-row">
                      <span class="payment-name">Partial COD — Advance Token Deposit</span>
                      <span class="payment-badge-popular">Standard for Art</span>
                    </div>
                    <span class="payment-desc">
                      Pay a small <strong>₹500 advance token deposit online</strong> (UPI/Card) to confirm your handcrafted order. The remaining balance of <strong>₹<span class="cod-balance-label"><?php echo format_inr($balanceOnDelivery); ?></span></strong> is paid directly to the delivery executive upon doorstep arrival.
                    </span>
                    <div class="payment-token-box">
                      <span>⚡ <strong>Deposit to Pay Now:</strong> ₹500</span>
                      <span>📦 <strong>Pay on Delivery:</strong> ₹<span class="cod-balance-label"><?php echo format_inr($balanceOnDelivery); ?></span></span>
                    </div>
                  </div>
                </div>

                <!-- Option 2: 100% Full Prepaid -->
                <div class="payment-card" id="payment-card-prepaid">
                  <input type="radio" id="payment-prepaid" name="payment-method-choice" value="Prepaid" class="payment-radio" />
                  <div class="payment-details">
                    <div class="payment-title-row">
                      <span class="payment-name">Full Online Payment (100% Prepaid)</span>
                      <span class="payment-badge-fast">Priority Dispatch</span>
                    </div>
                    <span class="payment-desc">
                      Pay the complete amount online securely using GPay, PhonePe, Paytm, Debit/Credit Card, or NetBanking. Priority workshop scheduling and instant courier label generation.
                    </span>
                  </div>
                </div>
              </div>

              <div class="payment-warning">
                🔒 <strong>Artisan Commitment:</strong> Because each Lippan mural is sculpted with delicate mirrors and cured clay, the advance token deposit secures raw materials and artisan workshop scheduling.
              </div>

              <div class="checkout-button-row">
                <button type="button" id="back-to-step-1-btn" class="checkout-prev-btn">
                  &larr; Back to Shipping
                </button>
                <button type="button" id="to-step-3-btn" class="checkout-next-btn">
                  Review Order &rarr;
                </button>
              </div>
            </div>

            <!-- Step 3: Review Details & Place Order -->
            <div id="step-3-panel" class="checkout-form-section" style="display: none;">
              <h2 class="checkout-section-title">Review & Confirm Your Order</h2>

              <div class="review-list">
                <div class="review-item">
                  <span class="review-label">Customer Name:</span>
                  <span id="review-cust-name" class="review-val"></span>
                </div>
                <div class="review-item">
                  <span class="review-label">Contact:</span>
                  <span id="review-cust-contact" class="review-val"></span>
                </div>
                <div class="review-item">
                  <span class="review-label">Delivery Address:</span>
                  <span id="review-cust-address" class="review-val"></span>
                </div>
                <div class="review-item">
                  <span class="review-label">Payment Mode:</span>
                  <span id="review-payment-method" class="review-val"></span>
                </div>
                <div class="review-item">
                  <span class="review-label">Deposit Payable Now:</span>
                  <span id="review-pay-now-val" class="review-val" style="color: var(--accent-green-vibrant); font-weight: 800;">₹500</span>
                </div>
                <div class="review-item" id="review-balance-row">
                  <span class="review-label">Balance on Doorstep Delivery:</span>
                  <span id="review-balance-val" class="review-val" style="color: #d97706; font-weight: 700;">₹<?php echo format_inr($balanceOnDelivery); ?></span>
                </div>
                <div class="review-item">
                  <span class="review-label">Delivery Window:</span>
                  <span class="review-val" style="color: var(--accent-green-vibrant);">2 to 4 Business Days (Delhi & NCR Express)</span>
                </div>
              </div>

              <div class="checkout-button-row">
                <button type="button" id="back-to-step-2-btn" class="checkout-prev-btn">
                  &larr; Back to Payment
                </button>
                <button 
                  type="button" 
                  id="place-order-btn" 
                  class="checkout-submit-btn" 
                  data-amount="<?php echo $tokenAmount; ?>"
                  data-total="<?php echo $final_total; ?>"
                >
                  Pay ₹500 Deposit & Confirm Order &rarr;
                </button>
              </div>
            </div>
          </div>

          <!-- Right: Order Summary Sidebar -->
          <div class="checkout-summary-sidebar">
            <h3 class="checkout-summary-title">Order Summary</h3>

            <!-- Order Items -->
            <div class="checkout-summary-items">
              <?php foreach ($cart as $item): 
                $p = $item['product'];
                $itemTotal = $p['price'] * $item['quantity'];
              ?>
                <div class="checkout-summary-item">
                  <div class="checkout-summary-thumb">
                    <img src="<?php echo htmlspecialchars($p['images'][0] ?? '/img-prodcut/IMG_3495.png'); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" />
                  </div>
                  <div class="checkout-summary-info">
                    <span class="checkout-summary-name"><?php echo htmlspecialchars($p['name']); ?></span>
                    <span class="checkout-summary-qty-price">Qty: <?php echo $item['quantity']; ?> × ₹<?php echo format_inr($p['price']); ?></span>
                  </div>
                  <div class="checkout-summary-total-col">
                    ₹<?php echo format_inr($itemTotal); ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Coupon Code Form -->
            <div class="checkout-coupon-box">
              <label class="coupon-box-label" for="checkout-coupon-input">Have a Promo / Coupon Code?</label>
              <div class="checkout-coupon-input-wrap">
                <input 
                  type="text" 
                  id="checkout-coupon-input" 
                  class="checkout-coupon-input" 
                  placeholder="e.g. MURLI10" 
                  value="<?php echo htmlspecialchars($couponCode); ?>"
                  style="text-transform: uppercase;"
                />
                <button type="button" id="apply-coupon-btn" class="checkout-coupon-btn">Apply</button>
              </div>
              <div id="applied-coupon-tag" class="applied-coupon-tag" style="<?php echo empty($couponCode) ? 'display: none;' : ''; ?>">
                <span class="tag-text">🏷️ <strong id="applied-coupon-code"><?php echo htmlspecialchars($couponCode); ?></strong> Applied (-₹<span id="applied-discount-val"><?php echo format_inr($discountAmount); ?></span>)</span>
                <button type="button" id="remove-coupon-btn" class="remove-coupon-btn" title="Remove Coupon">&times;</button>
              </div>
            </div>

            <!-- Financial Breakdown Table -->
            <div class="checkout-breakdown">
              <div class="checkout-row">
                <span>Subtotal:</span>
                <span id="summary-subtotal-val">₹<?php echo format_inr($subtotal); ?></span>
              </div>

              <div class="checkout-row discount-row" id="summary-discount-row" style="<?php echo ($discountAmount <= 0) ? 'display: none;' : ''; ?>">
                <span>Coupon Discount:</span>
                <span style="color: var(--accent-green-vibrant); font-weight: 700;">-₹<span id="summary-discount-val"><?php echo format_inr($discountAmount); ?></span></span>
              </div>

              <div class="checkout-row">
                <span>
                  Shipping Fee:
                  <small style="display:block; font-size: 0.72rem; color: var(--text-muted);">Free on orders ₹2,500+</small>
                </span>
                <span id="summary-shipping-val">
                  <?php if ($shipping === 0): ?>
                    <strong style="color: var(--accent-green-vibrant);">FREE</strong>
                  <?php else: ?>
                    ₹<?php echo format_inr($shipping); ?>
                  <?php endif; ?>
                </span>
              </div>

              <div class="checkout-row-bold">
                <span>Total Order Value:</span>
                <span style="color: var(--text-primary);">₹<span id="summary-total-val"><?php echo format_inr($final_total); ?></span></span>
              </div>

              <!-- Partial COD Token Breakdown Callout -->
              <div class="checkout-token-breakdown-box" id="token-breakdown-box">
                <div class="token-row">
                  <span>Advance Token (Pay Now):</span>
                  <strong style="color: var(--accent-green-vibrant);">₹<span id="summary-token-val">500</span></strong>
                </div>
                <div class="token-row">
                  <span>Balance on Doorstep Delivery:</span>
                  <strong style="color: #d97706;">₹<span id="summary-balance-val"><?php echo format_inr($balanceOnDelivery); ?></span></strong>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <?php include __DIR__ . '/includes/cart-drawer.php'; ?>
  <?php include __DIR__ . '/includes/floating-widgets.php'; ?>
  <?php include __DIR__ . '/includes/toast.php'; ?>

  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/main.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/checkout.js"></script>
</body>
</html>
