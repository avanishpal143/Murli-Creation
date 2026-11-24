<?php
require_once __DIR__ . '/config/functions.php';

$orderId = $_GET['orderId'] ?? '';
$order = get_order_by_id($orderId);

$estimated_delivery = date('F d, Y', strtotime('+6 days'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Receipt | Murli Creations</title>
  <meta name="description" content="Your handcrafted wall art order has been successfully placed with Murli Creations.">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/product-card.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/order-confirmation.css">
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main style="padding-top: 80px;">
    <div class="confirmation-page">
      <div class="container">
        <?php if ($order): ?>
          <div class="confirmation-card">
            <div class="confirmation-success-icon"><?php echo icon_svg('check-circle', '', 36); ?></div>
            <h1 class="confirmation-title">Thank You, <?php echo htmlspecialchars($order['customer']['name']); ?>!</h1>
            <p class="confirmation-text">
              Your order has been recorded in our workshop. Master craftsmen are now preparing your handcrafted wall creations with utmost care and bubble packaging.
            </p>

            <div class="order-num-box">
              Order Reference ID: <?php echo htmlspecialchars($order['id']); ?>
            </div>

            <div class="confirmation-details-grid">
              <div class="confirmation-detail-col">
                <span class="confirmation-detail-title">Payment Mode</span>
                <span class="confirmation-detail-val"><?php echo htmlspecialchars($order['paymentMethod'] ?? 'Prepaid'); ?></span>
              </div>

              <div class="confirmation-detail-col">
                <span class="confirmation-detail-title">Order Total</span>
                <span class="confirmation-detail-val">₹<?php echo format_inr($order['totalPayable'] ?? $order['totalAmount'] ?? $order['subtotal'] ?? 0); ?></span>
              </div>

              <?php if (!empty($order['balanceOnDelivery']) && $order['balanceOnDelivery'] > 0): ?>
                <div class="confirmation-detail-col">
                  <span class="confirmation-detail-title">Advance Token Paid</span>
                  <span class="confirmation-detail-val" style="color: var(--accent-green-vibrant); font-weight: 700;">₹<?php echo format_inr($order['tokenPaid'] ?? 500); ?></span>
                </div>

                <div class="confirmation-detail-col">
                  <span class="confirmation-detail-title">Balance Due on Delivery</span>
                  <span class="confirmation-detail-val" style="color: #d97706; font-weight: 700;">₹<?php echo format_inr($order['balanceOnDelivery']); ?></span>
                </div>
              <?php endif; ?>

              <div class="confirmation-detail-col">
                <span class="confirmation-detail-title">Shipping Address</span>
                <span class="confirmation-detail-val">
                  <?php echo htmlspecialchars($order['customer']['address'] ?? ''); ?>, <?php echo htmlspecialchars($order['customer']['city'] ?? ''); ?>, <?php echo htmlspecialchars($order['customer']['state'] ?? ''); ?> - <?php echo htmlspecialchars($order['customer']['pinCode'] ?? $order['customer']['pin'] ?? ''); ?>
                </span>
              </div>

              <div class="confirmation-detail-col">
                <span class="confirmation-detail-title">Contact Phone</span>
                <span class="confirmation-detail-val"><?php echo htmlspecialchars($order['customer']['phone'] ?? ''); ?></span>
              </div>
            </div>

            <div class="confirmation-timeline">
              <span class="confirmation-timeline-title">📦 Estimated Dispatch & Delivery:</span>
              <span>Expected arrival on or before <strong><?php echo $estimated_delivery; ?></strong>. We will share your courier tracking number over WhatsApp / SMS once dispatched.</span>
            </div>

            <a href="/shop.php" class="confirmation-continue-btn">
              <span>Continue Shopping</span> <?php echo icon_svg('arrow-right', '', 14); ?>
            </a>
          </div>
        <?php else: ?>
          <div class="confirmation-card">
            <div class="confirmation-warning-icon"><?php echo icon_svg('shield-check', '', 36); ?></div>
            <h1 class="confirmation-title">Order Lookup</h1>
            <p class="confirmation-text">
              We could not find an active order matching this reference. If you recently placed an order, please check your confirmation email or message our support team on WhatsApp.
            </p>
            <a href="/shop.php" class="confirmation-continue-btn">
              <span>Return to Catalog</span> <?php echo icon_svg('arrow-right', '', 14); ?>
            </a>
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
</body>
</html>
