<?php
require_once __DIR__ . '/config/functions.php';

$cart = get_session_cart();
$subtotal = get_cart_subtotal();

if (empty($cart)) {
    header('Location: /cart.php');
    exit;
}

$shipping = ($subtotal >= 3000) ? 0 : 250;
$final_total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout | Murli Creations</title>
  <meta name="description" content="Secure checkout wizard with COD and Razorpay for handcrafted wall art by Murli Creations.">

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
              <h2 class="checkout-section-title">Shipping & Contact Details</h2>
              
              <div class="checkout-form-grid">
                <div class="checkout-input-group checkout-full-width">
                  <label class="checkout-label" for="checkout-name">Full Name *</label>
                  <input type="text" id="checkout-name" class="checkout-input" placeholder="e.g. Radhika Sharma" />
                  <span id="error-name" class="checkout-error-text"></span>
                </div>

                <div class="checkout-input-group">
                  <label class="checkout-label" for="checkout-email">Email Address *</label>
                  <input type="email" id="checkout-email" class="checkout-input" placeholder="name@domain.com" />
                  <span id="error-email" class="checkout-error-text"></span>
                </div>

                <div class="checkout-input-group">
                  <label class="checkout-label" for="checkout-phone">Mobile Number (10 Digits) *</label>
                  <input type="tel" id="checkout-phone" class="checkout-input" placeholder="9876543210" maxlength="10" />
                  <span id="error-phone" class="checkout-error-text"></span>
                </div>

                <div class="checkout-input-group checkout-full-width">
                  <label class="checkout-label" for="checkout-address">Delivery Address (House / Street / Area) *</label>
                  <input type="text" id="checkout-address" class="checkout-input" placeholder="Flat 402, Lotus Towers, Model Town" />
                  <span id="error-address" class="checkout-error-text"></span>
                </div>

                <div class="checkout-input-group">
                  <label class="checkout-label" for="checkout-city">City *</label>
                  <input type="text" id="checkout-city" class="checkout-input" placeholder="e.g. Rohtak" />
                  <span id="error-city" class="checkout-error-text"></span>
                </div>

                <div class="checkout-input-group">
                  <label class="checkout-label" for="checkout-state">State *</label>
                  <select id="checkout-state" class="checkout-select">
                    <option value="Haryana">Haryana</option>
                    <option value="Delhi NCR">Delhi NCR</option>
                    <option value="Rajasthan">Rajasthan</option>
                    <option value="Punjab">Punjab</option>
                    <option value="Uttar Pradesh">Uttar Pradesh</option>
                    <option value="Maharashtra">Maharashtra</option>
                    <option value="Gujarat">Gujarat</option>
                    <option value="Karnataka">Karnataka</option>
                    <option value="Other States">Other States</option>
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
              <h2 class="checkout-section-title">Select Payment Mode</h2>

              <div class="payment-options">
                <div class="payment-card selected" id="payment-card-cod">
                  <input type="radio" id="payment-cod" name="payment-method-choice" value="COD" class="payment-radio" checked />
                  <div class="payment-details">
                    <span class="payment-name">Cash on Delivery (COD)</span>
                    <span class="payment-desc">Pay directly in cash or UPI QR to delivery executive when art arrives.</span>
                  </div>
                </div>

                <div class="payment-card" id="payment-card-razorpay">
                  <input type="radio" id="payment-razorpay" name="payment-method-choice" value="Razorpay" class="payment-radio" />
                  <div class="payment-details">
                    <span class="payment-name">Instant Online Payment (Razorpay UPI, Cards & NetBanking)</span>
                    <span class="payment-desc">Seamlessly complete payment using GPay, PhonePe, Paytm, or debit/credit card.</span>
                  </div>
                </div>
              </div>

              <div class="payment-warning">
                🔒 <strong>Artisan Delivery Assurance:</strong> Every piece is checked for quality before packing and protected with bubble wrap.
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
                  <span class="review-label">Customer:</span>
                  <span id="review-cust-name" class="review-val"></span>
                </div>
                <div class="review-item">
                  <span class="review-label">Contact:</span>
                  <span id="review-cust-contact" class="review-val"></span>
                </div>
                <div class="review-item">
                  <span class="review-label">Shipping Address:</span>
                  <span id="review-cust-address" class="review-val"></span>
                </div>
                <div class="review-item">
                  <span class="review-label">Payment Mode:</span>
                  <span id="review-payment-method" class="review-val"></span>
                </div>
                <div class="review-item">
                  <span class="review-label">Estimated Delivery:</span>
                  <span class="review-val" style="color: var(--accent-sage);">5 to 7 Business Days (Fragile Handling)</span>
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
                  data-amount="<?php echo $final_total; ?>"
                >
                  Place Order (₹<?php echo format_inr($final_total); ?>)
                </button>
              </div>
            </div>
          </div>

          <!-- Right: Order Summary Sidebar -->
          <div class="checkout-summary-sidebar">
            <h3 class="checkout-summary-title">Order Summary</h3>

            <div class="checkout-summary-items">
              <?php foreach ($cart as $item): 
                $p = $item['product'];
                $itemTotal = $p['price'] * $item['quantity'];
              ?>
                <div class="checkout-summary-item">
                  <div class="checkout-summary-thumb">
                    <img src="<?php echo htmlspecialchars($p['images'][0]); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" />
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

            <div class="checkout-row">
              <span>Subtotal:</span>
              <span>₹<?php echo format_inr($subtotal); ?></span>
            </div>

            <div class="checkout-row">
              <span>Shipping Fee:</span>
              <span>
                <?php if ($shipping === 0): ?>
                  <strong style="color: var(--accent-sage);">FREE</strong>
                <?php else: ?>
                  ₹<?php echo format_inr($shipping); ?>
                <?php endif; ?>
              </span>
            </div>

            <div class="checkout-row-bold">
              <span>Total Payable:</span>
              <span style="color: var(--accent-blue);">₹<?php echo format_inr($final_total); ?></span>
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

  <script src="/assets/js/tilt.js"></script>
  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/main.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/checkout.js"></script>
</body>
</html>
