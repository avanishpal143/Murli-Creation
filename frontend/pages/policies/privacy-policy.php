<?php
/**
 * =============================================================================
 * MURLI CREATIONS - PRIVACY-POLICY POLICY VIEW
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy | Murli Creations</title>
  <meta name="description" content="Learn how Murli Creations protects and respects your personal data, secure payment processing, and customer privacy.">

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
          <span class="policy-badge"><?php echo icon_svg('shield-check', '', 14); ?> Data Security</span>
          <h1 class="policy-title">Privacy Policy</h1>
          <p class="policy-subtitle">
            Your trust and privacy are paramount to us at Murli Creations. We are committed to safeguarding the personal information you share with our studio.
          </p>
          <span class="policy-updated-date">Last Updated: August 2026 • Delhi, India</span>
        </div>

        <!-- Grid Layout -->
        <div class="policy-grid">
          
          <!-- Sidebar Navigation -->
          <aside class="policy-sidebar">
            <h3 class="policy-nav-title">Privacy Sections</h3>
            <ul class="policy-nav-list">
              <li><a href="#info-collection" class="policy-nav-link active">1. Information We Collect</a></li>
              <li><a href="#how-we-use" class="policy-nav-link">2. How We Use Information</a></li>
              <li><a href="#payment-security" class="policy-nav-link">3. Secure Payment Handling</a></li>
              <li><a href="#data-sharing" class="policy-nav-link">4. Third-Party Sharing</a></li>
              <li><a href="#cookies" class="policy-nav-link">5. Cookies & Local Storage</a></li>
              <li><a href="#contact-us" class="policy-nav-link">6. Privacy Inquiries</a></li>
            </ul>

            <div class="policy-sidebar-support">
              <h4>Privacy Inquiries</h4>
              <p>Have questions about your data? Contact our studio care officer.</p>
              <a href="mailto:<?php echo htmlspecialchars($studio['email'] ?? 'contact@murlicreations.com'); ?>" class="policy-support-btn">
                <?php echo icon_svg('mail', '', 14); ?> Email Care Team
              </a>
            </div>
          </aside>

          <!-- Main Content -->
          <div class="policy-content">
            
            <!-- Section 1 -->
            <section id="info-collection" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('user', '', 18); ?></span>
                1. Information We Collect
              </h2>
              <p class="policy-text">
                When you interact with Murli Creations, place an order, or submit an art inquiry, we collect information necessary to fulfill your request:
              </p>
              <ul class="policy-list">
                <li><strong>Contact Details:</strong> Your full name, phone/WhatsApp number, email address, and shipping address.</li>
                <li><strong>Order Information:</strong> The artworks selected, custom dimensions requested, delivery notes, and transaction ID.</li>
                <li><strong>Communications:</strong> Messages exchanged with our studio via WhatsApp or direct website forms for art sizing and consultations.</li>
              </ul>
            </section>

            <!-- Section 2 -->
            <section id="how-we-use" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('sparkles', '', 18); ?></span>
                2. How We Use Your Information
              </h2>
              <p class="policy-text">
                We use your personal data strictly for legitimate operational purposes:
              </p>
              <ul class="policy-list">
                <li>Processing, hand-crafting, packaging, and delivering your ordered artworks.</li>
                <li>Sending order confirmations, tracking numbers, and delivery status notifications.</li>
                <li>Providing personalized studio consultations for bespoke interior commissions.</li>
                <li>Delivering customer support and processing replacement/refund requests.</li>
              </ul>
            </section>

            <!-- Section 3 -->
            <section id="payment-security" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('shield-check', '', 18); ?></span>
                3. Secure Payment Handling
              </h2>
              <p class="policy-text">
                Murli Creations <strong>never stores or processes your credit/debit card numbers or UPI PINs</strong> on our servers. All transactions are securely processed through RBI-approved, PCI-DSS certified payment gateways and direct UPI encrypted protocols.
              </p>
              <div class="policy-alert-box">
                <div class="policy-alert-title">🔒 256-Bit SSL Encryption</div>
                <p class="policy-alert-desc">
                  All checkout and contact interactions on our website are protected by industry-standard 256-bit SSL encryption.
                </p>
              </div>
            </section>

            <!-- Section 4 -->
            <section id="data-sharing" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('award', '', 18); ?></span>
                4. Third-Party Sharing Policies
              </h2>
              <p class="policy-text">
                We <strong>do not sell, rent, or trade</strong> your personal information to third-party marketers or advertisers. We share details solely with verified delivery partners (courier companies like BlueDart, Delhivery) strictly to execute package delivery to your address.
              </p>
            </section>

            <!-- Section 5 -->
            <section id="cookies" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('shopping-bag', '', 18); ?></span>
                5. Cookies & Local Storage
              </h2>
              <p class="policy-text">
                We use functional cookies and browser storage solely to preserve your shopping cart items, applied coupons, and website preferences during your browsing session.
              </p>
            </section>

            <!-- Section 6 -->
            <section id="contact-us" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('mail', '', 18); ?></span>
                6. Contact Our Privacy Officer
              </h2>
              <p class="policy-text">
                For questions regarding this policy or to request removal of your contact details:
              </p>
              <ul class="policy-list">
                <li><strong>Email:</strong> <?php echo htmlspecialchars($studio['email'] ?? 'contact@murlicreations.com'); ?></li>
                <li><strong>Phone / WhatsApp:</strong> <?php echo htmlspecialchars($studio['whatsapp'] ?? '+91 99999 99999'); ?></li>
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
