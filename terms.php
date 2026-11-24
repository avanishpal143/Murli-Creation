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
  <title>Terms of Service | Murli Creations</title>
  <meta name="description" content="Terms of Service and legal conditions for purchasing authentic handcrafted Lippan art from Murli Creations.">

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
          <span class="policy-badge"><?php echo icon_svg('award', '', 14); ?> Legal Agreement</span>
          <h1 class="policy-title">Terms of Service</h1>
          <p class="policy-subtitle">
            Please review the terms and conditions governing your purchases, bespoke commissions, and website use with Murli Creations.
          </p>
          <span class="policy-updated-date">Last Updated: August 2026 • Delhi Studio</span>
        </div>

        <!-- Grid Layout -->
        <div class="policy-grid">
          
          <!-- Sidebar Navigation -->
          <aside class="policy-sidebar">
            <h3 class="policy-nav-title">Terms Navigation</h3>
            <ul class="policy-nav-list">
              <li><a href="#artisan-variance" class="policy-nav-link active">1. Handmade Nature & Variance</a></li>
              <li><a href="#pricing-orders" class="policy-nav-link">2. Pricing & Orders</a></li>
              <li><a href="#bespoke-terms" class="policy-nav-link">3. Bespoke Commissions</a></li>
              <li><a href="#intellectual-prop" class="policy-nav-link">4. Intellectual Property</a></li>
              <li><a href="#liability" class="policy-nav-link">5. Limitation of Liability</a></li>
              <li><a href="#jurisdiction" class="policy-nav-link">6. Governing Law</a></li>
            </ul>

            <div class="policy-sidebar-support">
              <h4>Questions on Terms?</h4>
              <p>Contact our support desk for clarifications regarding commissions.</p>
              <a href="mailto:<?php echo htmlspecialchars($studio['email'] ?? 'contact@murlicreations.com'); ?>" class="policy-support-btn">
                <?php echo icon_svg('mail', '', 14); ?> Write to Studio
              </a>
            </div>
          </aside>

          <!-- Main Content -->
          <div class="policy-content">
            
            <!-- Section 1 -->
            <section id="artisan-variance" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('sparkles', '', 18); ?></span>
                1. Authentic Handmade Nature & Subtle Variance
              </h2>
              <p class="policy-text">
                Every Lippan artwork sold on this website is individually sculpted, hand-painted, and mirror-inlaid by artisan hands. Because our process uses traditional clay relief modeling and natural pigments rather than industrial machine stamping, <strong>slight organic variations in cone thickness, mirror angles, and tint</strong> are natural and celebrate the soul of handmade Indian heritage art.
              </p>
            </section>

            <!-- Section 2 -->
            <section id="pricing-orders" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('tag', '', 18); ?></span>
                2. Pricing, Invoicing & Order Acceptance
              </h2>
              <p class="policy-text">
                All prices displayed on Murli Creations are listed in Indian Rupees (INR ₹) inclusive of all applicable taxes. We reserve the right to modify prices or discontinue specific limited-edition designs without prior notice.
              </p>
              <ul class="policy-list">
                <li>Receipt of an order confirmation does not signify our final acceptance; we reserve the right to verify address deliverability before dispatch.</li>
                <li>In the unlikely event of a pricing or inventory discrepancy, we will contact you immediately with the option to confirm or cancel.</li>
              </ul>
            </section>

            <!-- Section 3 -->
            <section id="bespoke-terms" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('shield-check', '', 18); ?></span>
                3. Bespoke Commissions & Custom Sizes
              </h2>
              <p class="policy-text">
                Custom commissions (such as oversized wall panels, custom deity themes, or corporate gifts) require upfront confirmation. Once master clay curing has commenced for a custom commission, modifications or cancellations are subject to mutual agreement.
              </p>
            </section>

            <!-- Section 4 -->
            <section id="intellectual-prop" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('award', '', 18); ?></span>
                4. Intellectual Property & Design Copyright
              </h2>
              <p class="policy-text">
                All original artwork designs, product photographs, branding elements, folklore narratives, and content published on this website are the intellectual property of <strong>Murli Creations</strong> and protected under Indian copyright laws. Unauthorized reproduction or commercial resale of our designs is strictly prohibited.
              </p>
            </section>

            <!-- Section 5 -->
            <section id="liability" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('plus', '', 18); ?></span>
                5. Product Care & Limitation of Liability
              </h2>
              <p class="policy-text">
                Our Lippan pieces are crafted for indoor display and pooja mandirs. Artworks must be kept away from direct persistent water exposure and harsh corrosive chemicals. Murli Creations shall not be liable for damage resulting from improper wall mounting, mishandling post-delivery, or exposure to excessive moisture.
              </p>
            </section>

            <!-- Section 6 -->
            <section id="jurisdiction" class="policy-section">
              <h2 class="policy-section-heading">
                <span class="policy-icon-box"><?php echo icon_svg('map-pin', '', 18); ?></span>
                6. Governing Law & Studio Contact
              </h2>
              <p class="policy-text">
                These terms shall be governed by and construed in accordance with the laws of India. Any disputes arising in connection with orders shall be subject to the exclusive jurisdiction of the courts in <strong>Delhi, India</strong>.
              </p>
              <ul class="policy-list">
                <li><strong>Studio Care Email:</strong> <?php echo htmlspecialchars($studio['email'] ?? 'contact@murlicreations.com'); ?></li>
                <li><strong>WhatsApp Helpline:</strong> <?php echo htmlspecialchars($studio['whatsapp'] ?? '+91 99999 99999'); ?></li>
                <li><strong>Studio:</strong> <?php echo htmlspecialchars($studio['address'] ?? 'Pitampura, Delhi - 110034, India'); ?></li>
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
