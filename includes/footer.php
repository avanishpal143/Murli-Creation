<?php
$site_settings = get_site_settings();
$studio = $site_settings['studio'] ?? [];
$shipping = $site_settings['shipping'] ?? [];
$social = $site_settings['social'] ?? [];
$footer = $site_settings['footer'] ?? [];
?>
<footer class="site-footer">
  <div class="footer-container">
    <!-- Brand / Blurb -->
    <div class="footer-tilt-wrapper">
      <div class="footer-column-brand">
        <a href="/index.php" class="footer-logo-container">
          <img
            src="/images/brand/white_logo.png"
            alt="Murli Creations Logo"
            width="120"
            height="62"
            class="footer-logo-image"
          />
        </a>
        <p class="footer-blurb">
          <?php echo htmlspecialchars($footer['about_text'] ?? 'Sacred mud & mirror art handcrafted by Murli Prasad. Bringing positive energy, devotion, and traditional Indian grandeur to homes across the globe.'); ?>
        </p>
      </div>
    </div>

    <!-- Contact & Studio Address -->
    <div class="footer-tilt-wrapper">
      <div class="footer-column-links">
        <h4 class="footer-title">Studio & Contact</h4>
        <div class="footer-address-list">
          <p class="footer-address-item">
            <strong>Studio:</strong> <?php echo htmlspecialchars($studio['address'] ?? 'Rohtak, Haryana, 124001, India'); ?>
          </p>
          <p class="footer-address-item">
            <strong>Care:</strong> <?php echo htmlspecialchars($studio['email'] ?? 'contact@murlicreations.com'); ?>
          </p>
          <p class="footer-address-item">
            <strong>WhatsApp:</strong> <?php echo htmlspecialchars($studio['whatsapp'] ?? '+91 99999 99999'); ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Collections Links -->
    <div class="footer-tilt-wrapper">
      <div class="footer-column-links">
        <h4 class="footer-title">Collections</h4>
        <ul class="footer-link-list">
          <li><a href="/shop.php?category=Lippan+Art">Lippan Art Collections</a></li>
          <li><a href="/shop.php?category=Shyam+Ji+Decor">Shyam Ji (Khatu Baba)</a></li>
          <li><a href="/shop.php?category=Vastu+Species">Vastu Species & Energy</a></li>
          <li><a href="/shop.php?category=Diwali+%26+Festive+Decor">Diwali & Festive Decor</a></li>
          <li><a href="/shop.php?category=Customised+Creations">Customised Creations</a></li>
          <li><a href="/shop.php?category=Jharokhas+%26+Frames">Royal Jharokhas</a></li>
        </ul>
      </div>
    </div>

    <!-- Navigation Quick Links -->
    <div class="footer-tilt-wrapper">
      <div class="footer-column-links">
        <h4 class="footer-title">Explore</h4>
        <ul class="footer-link-list">
          <li><a href="/index.php">Home</a></li>
          <li><a href="/shop.php">Full Catalog</a></li>
          <li><a href="/about.php">About Us</a></li>
          <li><a href="/contact.php">Contact & Support</a></li>
          <li><a href="/cart.php">Shopping Bag</a></li>
        </ul>
      </div>
    </div>

    <!-- Trust Guarantees -->
    <div class="footer-tilt-wrapper">
      <div class="footer-column-links">
        <h4 class="footer-title">Studio Promise</h4>
        <div class="footer-address-list">
          <p class="footer-address-item">
            <?php echo icon_svg('truck', '', 14); ?> <strong>Free Shipping:</strong> On orders above ₹<?php echo number_format($shipping['free_threshold'] ?? 3000); ?>
          </p>
          <p class="footer-address-item">
            <?php echo icon_svg('shield-check', '', 14); ?> <strong>Transit Safe:</strong> <?php echo htmlspecialchars($shipping['transit_safe_note'] ?? '5-layer wood crate pack'); ?>
          </p>
          <p class="footer-address-item">
            <?php echo icon_svg('award', '', 14); ?> <strong>100% Artisan:</strong> <?php echo htmlspecialchars($studio['location'] ?? 'Made in Haryana'); ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Bottom Bar -->
  <div class="footer-bottom-bar">
    <div class="footer-bottom-container">
      <p class="footer-copyright">
        <?php echo htmlspecialchars($footer['copyright'] ?? ('© ' . date('Y') . ' Murli Creations. Handcrafted in India with Devotion. All Rights Reserved.')); ?>
      </p>
      <div class="footer-socials">
        <a href="<?php echo htmlspecialchars($social['instagram'] ?? 'https://instagram.com/murlicreations'); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link">Instagram</a>
        <span class="footer-social-separator">•</span>
        <a href="<?php echo htmlspecialchars($social['whatsapp'] ?? 'https://wa.me/919999999999'); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link">WhatsApp</a>
        <span class="footer-social-separator">•</span>
        <a href="/contact.php" class="footer-social-link">Support</a>
      </div>
    </div>
  </div>
</footer>
