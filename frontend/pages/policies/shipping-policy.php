<?php
/**
 * =============================================================================
 * MURLI CREATIONS - SHIPPING-POLICY POLICY VIEW
 * =============================================================================
 * Legal and store policy view.
 * =============================================================================
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 3));
}
require_once ROOT_PATH . '/backend/config/functions.php';

$site_settings = get_site_settings();
$studio = $site_settings['studio'] ?? [];
$shipping = $site_settings['shipping'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shipping & Delivery Policy | Murli Creations</title>
  <meta name="description" content="Learn about Murli Creations' shipping rates, free delivery thresholds, dispatch turnaround, courier tracking, and transit protection across India.">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/policy.css">
</head>
<body>
  <?php include ROOT_PATH . '/frontend/components/header.php'; ?>

  <main style="padding-top: 80px;">
    <div class="policy-page">
      <div class="container">
        
        <!-- Header -->
        <div class="policy-header reveal-on-scroll">
          <span class="policy-badge"><?php echo icon_svg('truck', '', 14); ?> Nationwide Transit</span>
          <h1 class="policy-title">Shipping & Delivery Policy</h1>
          <p class="policy-subtitle">
            We deliver handcrafted Lippan art masterpieces to homes, temples, and living spaces across all states and union territories in India with speed and utmost care.
          </p>
          <span class="policy-updated-date">Last Updated: August 2026 • Free Shipping Above ₹<?php echo number_format($shipping['free_threshold'] ?? 3000); ?></span>
        </div>

        <!-- Grid Layout -->
        <div class="policy-grid">
          
          <!-- Sidebar Navigation -->
          <aside class="policy-sidebar">
            <h3 class="policy-nav-title">Policy Navigation</h3>
            <ul class="policy-nav-list">
              <li><a href="#shipping-charges" class="policy-nav-link active">1. Shipping Charges & Free Delivery</a></li>
              <li><a href="#dispatch-timelines" class="policy-nav-link">2. Processing & Dispatch Timelines</a></li>
              <li><a href="#delivery-time" class="policy-nav-link">3. Estimated Delivery Times</a></li>
              <li><a href="#packaging" class="policy-nav-link">4. Protective Packaging Protocol</a></li>
              <li><a href="#tracking" class="policy-nav-link">5. Real-Time Order Tracking</a></li>
              <li><a href="#serviceable-areas" class="policy-nav-link">6. Serviceable Pincodes & Locations</a></li>
            </ul>

            <div class="policy-sidebar-support">
              <h4>Tracking Your Package?</h4>
              <p>Send your Order ID on WhatsApp to receive real-time courier updates.</p>
              <a href="<?php echo htmlspecialchars($studio['whatsapp'] ?? 'https://wa.me/919999999999'); ?>" target="_blank" rel="noopener noreferrer" class="policy-support-btn">
                <?php echo icon_svg('whatsapp', '', 14); ?> Track on WhatsApp
              </a>
            </div>
          </aside>

          <!-- Main Content -->
          <div class="policy-content">
            
            <!-- Section 1 -->
            <section id="shipping-charges" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('tag', '', 18); ?></span>
                1. Shipping Rates & Free Delivery Threshold
              </h2>
              <p class="policy-text">
                We believe in straightforward, transparent pricing without hidden checkout surprises:
              </p>
              <ul class="policy-list">
                <li><strong>Free Insured Shipping:</strong> All cart orders with a subtotal of <strong>₹<?php echo number_format($shipping['free_threshold'] ?? 3000); ?> and above</strong> qualify for automatic 100% Free Doorstep Delivery across India.</li>
                <li><strong>Standard Orders (Below ₹<?php echo number_format($shipping['free_threshold'] ?? 3000); ?>):</strong> A flat shipping charge of <strong>₹<?php echo number_format($shipping['standard_charge'] ?? 149); ?></strong> is applied to cover specialized insured courier handling.</li>
              </ul>
            </section>

            <!-- Section 2 -->
            <section id="dispatch-timelines" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('clock', '', 18); ?></span>
                2. Processing & Dispatch Timelines
              </h2>
              <p class="policy-text">
                Every artwork is carefully inspected, packed, and insured before handoff to couriers:
              </p>
              <ul class="policy-list">
                <li><strong>Ready-to-Ship Catalog Pieces:</strong> Dispatched within <strong>3 to 5 business days</strong> of order placement.</li>
                <li><strong>Custom Commissioned & Personalized Pieces:</strong> Dispatched within <strong>7 to 10 business days</strong>, allowing essential time for master clay sculpting, mirror curing, and natural mineral painting.</li>
              </ul>
            </section>

            <!-- Section 3 -->
            <section id="delivery-time" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('truck', '', 18); ?></span>
                3. Estimated Delivery Times
              </h2>
              <p class="policy-text">
                We currently deliver within Delhi & NCR only. Pan-India shipping coming soon.
              </p>
              <ul class="policy-list">
                <li><strong>Delhi & NCR:</strong> 1 to 2 business days.</li>
              </ul>
              <div class="policy-alert-box" style="background:#fff8e1;border-left:4px solid #f59e0b;padding:12px 16px;border-radius:6px;margin-top:12px;">
                <strong>📦 Note:</strong> We are currently unable to deliver outside of Delhi & NCR. We apologize for the inconvenience and will be expanding our delivery soon.
              </div>
            </section>

            <!-- Section 4 -->
            <section id="packaging" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('shield-check', '', 18); ?></span>
                4. Specialized Protective Packaging
              </h2>
              <p class="policy-text">
                Due to the delicate nature of hand-cut glass mirrors and polymer clay relief work, we use specialized multi-layer shock-absorbent cushioning and reinforced outer boxes to guarantee safe door-to-door delivery.
              </p>
              <div class="policy-alert-box gold">
                <div class="policy-alert-title">🛡️ 100% Transit Insurance Guarantee</div>
                <p class="policy-alert-desc">
                  Every single shipment is fully covered by our studio's transit guarantee. If your artwork experiences transit breakage, we replace it completely free of charge.
                </p>
              </div>
            </section>

            <!-- Section 5 -->
            <section id="tracking" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('map-pin', '', 18); ?></span>
                5. Real-Time Tracking Updates
              </h2>
              <p class="policy-text">
                As soon as your package is dispatched from our workshop:
              </p>
              <ul class="policy-list">
                <li>You will receive an instant SMS and WhatsApp notification containing your Courier Tracking AWB number and live tracking link.</li>
                <li>You can also contact our studio support team with your Order ID anytime to get live delivery updates.</li>
              </ul>
            </section>

            <!-- Section 6 -->
            <section id="serviceable-areas" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('mail', '', 18); ?></span>
                6. Questions & Studio Assistance
              </h2>
              <p class="policy-text">
                For special delivery requests, express gifting timelines, or address changes, please contact:
              </p>
              <ul class="policy-list">
                <li><strong>Studio Email:</strong> <?php echo htmlspecialchars($studio['email'] ?? 'contact@murlicreations.com'); ?></li>
                <li><strong>WhatsApp Helpline:</strong> <?php echo htmlspecialchars($studio['whatsapp'] ?? '+91 99999 99999'); ?></li>
                <li><strong>Studio:</strong> <?php echo htmlspecialchars($studio['address'] ?? 'Pitampura, Delhi - 110034, India'); ?></li>
              </ul>
            </section>

          </div>
        </div>

      </div>
    </div>
  </main>

  <?php include ROOT_PATH . '/frontend/components/footer.php'; ?>
  <?php include ROOT_PATH . '/frontend/components/cart-drawer.php'; ?>
  <?php include ROOT_PATH . '/frontend/components/floating-widgets.php'; ?>

  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/main.js"></script>
</body>
</html>
