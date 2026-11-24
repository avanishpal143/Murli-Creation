<?php
require_once __DIR__ . '/config/functions.php';
$site_settings = get_site_settings();
$studio = $site_settings['studio'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cancellation Policy | Murli Creations</title>
  <meta name="description" content="Read Murli Creations' order cancellation guidelines, cancellation windows, and instant refund protocols.">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/policy.css">
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main style="padding-top: 80px;">
    <div class="policy-page">
      <div class="container">
        
        <!-- Header -->
        <div class="policy-header reveal-on-scroll">
          <span class="policy-badge"><?php echo icon_svg('refresh', '', 14); ?> Order Management</span>
          <h1 class="policy-title">Cancellation Policy</h1>
          <p class="policy-subtitle">
            We understand plans can change. Here is everything you need to know about cancelling or modifying your order with Murli Creations.
          </p>
          <span class="policy-updated-date">Last Updated: August 2026 • Delhi Studio</span>
        </div>

        <!-- Grid Layout -->
        <div class="policy-grid">
          
          <!-- Sidebar Navigation -->
          <aside class="policy-sidebar">
            <h3 class="policy-nav-title">Cancellation Guide</h3>
            <ul class="policy-nav-list">
              <li><a href="#cancellation-window" class="policy-nav-link active">1. Cancellation Window</a></li>
              <li><a href="#how-to-cancel" class="policy-nav-link">2. How to Request Cancellation</a></li>
              <li><a href="#custom-cancellations" class="policy-nav-link">3. Custom Orders</a></li>
              <li><a href="#refund-timeline" class="policy-nav-link">4. Cancellation Refunds</a></li>
              <li><a href="#contact" class="policy-nav-link">5. Studio Helpdesk</a></li>
            </ul>

            <div class="policy-sidebar-support">
              <h4>Need to Cancel Quickly?</h4>
              <p>Message us immediately with your Order ID on WhatsApp.</p>
              <a href="<?php echo htmlspecialchars($studio['whatsapp'] ?? 'https://wa.me/919999999999'); ?>" target="_blank" rel="noopener noreferrer" class="policy-support-btn">
                <?php echo icon_svg('whatsapp', '', 14); ?> Instant WhatsApp
              </a>
            </div>
          </aside>

          <!-- Main Content -->
          <div class="policy-content">
            
            <!-- Section 1 -->
            <section id="cancellation-window" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('clock', '', 18); ?></span>
                1. Standard Order Cancellation Window
              </h2>
              <p class="policy-text">
                You can cancel your order for standard catalog artworks <strong>within 12 to 24 hours of placing the order</strong>, or anytime before the package is handed over to our courier partners for dispatch.
              </p>
              <div class="policy-alert-box">
                <div class="policy-alert-title">⚡ Fast Dispatch Notice</div>
                <p class="policy-alert-desc">
                  Because our ready pieces are packed quickly for fast delivery, please notify us as early as possible if you wish to make changes to your address or cancel the order.
                </p>
              </div>
            </section>

            <!-- Section 2 -->
            <section id="how-to-cancel" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('mail', '', 18); ?></span>
                2. How to Request a Cancellation
              </h2>
              <p class="policy-text">
                To cancel an order, simply choose either of the following methods:
              </p>
              <ul class="policy-list">
                <li><strong>Via WhatsApp:</strong> Send a message to <strong><?php echo htmlspecialchars($studio['whatsapp'] ?? '+91 99999 99999'); ?></strong> with your <strong>Order ID (e.g. MC-123456)</strong> and reason for cancellation.</li>
                <li><strong>Via Email:</strong> Send an email to <strong><?php echo htmlspecialchars($studio['email'] ?? 'contact@murlicreations.com'); ?></strong> with the subject line <em>"Order Cancellation Request - [Your Order ID]"</em>.</li>
              </ul>
            </section>

            <!-- Section 3 -->
            <section id="custom-cancellations" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('sparkles', '', 18); ?></span>
                3. Custom Commissioned Artworks
              </h2>
              <p class="policy-text">
                For custom commissioned pieces (personalized dimensions, unique deity forms, or bespoke nameplates):
              </p>
              <ul class="policy-list">
                <li>Cancellations are accepted within <strong>24 hours</strong> of placing the order with full 100% refund.</li>
                <li>After 24 hours, once raw clay sculpting and mirror cutting have commenced, a nominal materials fee may apply if work is already in progress.</li>
              </ul>
            </section>

            <!-- Section 4 -->
            <section id="refund-timeline" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('award', '', 18); ?></span>
                4. Cancellation Refund Timelines
              </h2>
              <p class="policy-text">
                Upon cancellation approval by our studio:
              </p>
              <ul class="policy-list">
                <li><strong>Prepaid / UPI Orders:</strong> 100% refund is initiated immediately and will reflect in your original payment source within <strong>3 to 5 business days</strong>.</li>
                <li><strong>Cash on Delivery Orders:</strong> No cancellation charge or pre-authorization holds apply.</li>
              </ul>
            </section>

            <!-- Section 5 -->
            <section id="contact" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('phone', '', 18); ?></span>
                5. Studio Helpdesk & Support
              </h2>
              <p class="policy-text">
                We are always here to assist you with any questions or modifications:
              </p>
              <ul class="policy-list">
                <li><strong>Studio Care Email:</strong> <?php echo htmlspecialchars($studio['email'] ?? 'contact@murlicreations.com'); ?></li>
                <li><strong>WhatsApp Helpline:</strong> <?php echo htmlspecialchars($studio['whatsapp'] ?? '+91 99999 99999'); ?></li>
                <li><strong>Studio Location:</strong> <?php echo htmlspecialchars($studio['address'] ?? 'Pitampura, Delhi - 110034, India'); ?></li>
                <li><strong>Operating Hours:</strong> <?php echo htmlspecialchars($studio['hours'] ?? 'Mon - Sat: 10:00 AM - 7:30 PM (IST)'); ?></li>
              </ul>
            </section>

          </div>
        </div>

      </div>
    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <?php include __DIR__ . '/includes/cart-drawer.php'; ?>
  <?php include __DIR__ . '/includes/floating-widgets.php'; ?>

  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/main.js"></script>
</body>
</html>
