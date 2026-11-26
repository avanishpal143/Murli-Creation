<?php
/**
 * =============================================================================
 * MURLI CREATIONS - RETURN-POLICY POLICY VIEW
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
  <title>Refund & Return Policy | Murli Creations</title>
  <meta name="description" content="Read Murli Creations' Refund & Return Policy for authentic handcrafted Lippan art, transit damage replacements, and hassle-free returns.">

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
          <span class="policy-badge"><?php echo icon_svg('shield-check', '', 14); ?> Customer Protection</span>
          <h1 class="policy-title">Refund & Return Policy</h1>
          <p class="policy-subtitle">
            At Murli Creations, each artwork is an authentic, handcrafted labor of love and devotion. We are committed to ensuring your complete satisfaction and peace of mind.
          </p>
          <span class="policy-updated-date">Last Updated: August 2026 • Delhi Studio</span>
        </div>

        <!-- Grid Layout -->
        <div class="policy-grid">
          
          <!-- Sidebar Navigation -->
          <aside class="policy-sidebar">
            <h3 class="policy-nav-title">Policy Navigation</h3>
            <ul class="policy-nav-list">
              <li><a href="#transit-damage" class="policy-nav-link active">1. Transit Damage Guarantee</a></li>
              <li><a href="#eligibility" class="policy-nav-link">2. Return Eligibility</a></li>
              <li><a href="#process" class="policy-nav-link">3. Step-by-Step Return Process</a></li>
              <li><a href="#refunds" class="policy-nav-link">4. Refund Methods & Timelines</a></li>
              <li><a href="#custom-orders" class="policy-nav-link">5. Custom & Commissioned Art</a></li>
              <li><a href="#cancellations" class="policy-nav-link">6. Order Cancellations</a></li>
            </ul>

            <div class="policy-sidebar-support">
              <h4>Need Immediate Help?</h4>
              <p>Contact our studio care team directly on WhatsApp for swift assistance.</p>
              <a href="<?php echo htmlspecialchars($studio['whatsapp'] ?? 'https://wa.me/919999999999'); ?>" target="_blank" rel="noopener noreferrer" class="policy-support-btn">
                <?php echo icon_svg('whatsapp', '', 14); ?> WhatsApp Support
              </a>
            </div>
          </aside>

          <!-- Main Content -->
          <div class="policy-content">
            
            <!-- Section 1 -->
            <section id="transit-damage" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('shield-check', '', 18); ?></span>
                1. 100% Free Transit Damage Replacement
              </h2>
              <p class="policy-text">
                Every Lippan artwork leaves our Delhi studio encased in multi-layer protective packaging. However, in the rare event that your artwork arrives damaged or with broken mirrors during shipping, <strong>we provide a 100% free immediate replacement</strong> at zero additional charge to you.
              </p>

              <div class="policy-alert-box">
                <div class="policy-alert-title">📦 Important: Record an Unboxing Video</div>
                <p class="policy-alert-desc">
                  To claim a free transit damage replacement, please record a continuous, uncut video while unboxing your package upon delivery and share it with us within <strong>48 hours</strong> of receiving the order.
                </p>
              </div>
            </section>

            <!-- Section 2 -->
            <section id="eligibility" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('check-circle', '', 18); ?></span>
                2. Return Eligibility Window
              </h2>
              <p class="policy-text">
                You may request a return within <strong>7 days</strong> of delivery under the following conditions:
              </p>
              <ul class="policy-list">
                <li>The item received is physically defective, damaged, or significantly differs from the ordered design/size.</li>
                <li>The artwork is in its original, unused condition with all original packaging and protective fittings intact.</li>
                <li>Proof of purchase (Order ID, Invoice, or Registered Phone Number) is provided.</li>
              </ul>
              <p class="policy-text">
                <em>Note:</em> Because each Lippan piece is individually hand-sculpted and painted by artisan hands, subtle organic variations in clay relief textures and mirror alignments are the hallmark of authentic handmade craftsmanship and are not considered manufacturing defects.
              </p>
            </section>

            <!-- Section 3 -->
            <section id="process" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('refresh', '', 18); ?></span>
                3. Step-by-Step Return Process
              </h2>
              <div class="policy-steps-grid">
                <div class="policy-step-card">
                  <div class="policy-step-number">01</div>
                  <div class="policy-step-title">Notify Studio</div>
                  <p class="policy-step-desc">WhatsApp or email us with your Order ID and unboxing photos/video within 48h.</p>
                </div>

                <div class="policy-step-card">
                  <div class="policy-step-number">02</div>
                  <div class="policy-step-title">Verification</div>
                  <p class="policy-step-desc">Our studio team reviews and approves your replacement or return request within 24 hours.</p>
                </div>

                <div class="policy-step-card">
                  <div class="policy-step-number">03</div>
                  <div class="policy-step-title">Reverse Pickup</div>
                  <p class="policy-step-desc">Our courier partner will arrange a doorstep pickup of the package from your address.</p>
                </div>

                <div class="policy-step-card">
                  <div class="policy-step-number">04</div>
                  <div class="policy-step-title">Dispatch / Refund</div>
                  <p class="policy-step-desc">A fresh handcrafted replacement is dispatched immediately or refund processed.</p>
                </div>
              </div>
            </section>

            <!-- Section 4 -->
            <section id="refunds" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('award', '', 18); ?></span>
                4. Refund Methods & Timelines
              </h2>
              <p class="policy-text">
                Once the returned item is received at our studio and inspected, your refund will be initiated:
              </p>
              <ul class="policy-list">
                <li><strong>Prepaid / UPI / Card Orders:</strong> Refund will be credited back to your original payment method within <strong>5 to 7 business days</strong>.</li>
                <li><strong>Cash on Delivery (COD) / Bank Transfer:</strong> Refund will be transferred directly to your verified Bank Account or UPI ID within <strong>3 to 5 business days</strong> after receiving your details.</li>
              </ul>
            </section>

            <!-- Section 5 -->
            <section id="custom-orders" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('sparkles', '', 18); ?></span>
                5. Custom Commissioned Artworks
              </h2>
              <p class="policy-text">
                Bespoke commissioned pieces (custom sizes, personalized deity reliefs, family nameplates, or tailor-made color schemes) are hand-cured specifically for you and cannot be returned for general reasons. However, our <strong>100% Free Transit Damage Replacement Guarantee</strong> fully applies to all custom pieces.
              </p>
            </section>

            <!-- Section 6 -->
            <section id="cancellations" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('mail', '', 18); ?></span>
                6. Questions & Studio Contact
              </h2>
              <p class="policy-text">
                For any questions regarding returns or order status, please reach out to:
              </p>
              <ul class="policy-list">
                <li><strong>Studio Care Email:</strong> <?php echo htmlspecialchars($studio['email'] ?? 'contact@murlicreations.com'); ?></li>
                <li><strong>WhatsApp / Call:</strong> <?php echo htmlspecialchars($studio['whatsapp'] ?? '+91 99999 99999'); ?></li>
                <li><strong>Studio:</strong> <?php echo htmlspecialchars($studio['address'] ?? 'Pitampura, Delhi - 110034, India'); ?></li>
                <li><strong>Business Hours:</strong> <?php echo htmlspecialchars($studio['hours'] ?? 'Mon - Sat: 10:00 AM - 7:30 PM (IST)'); ?></li>
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
