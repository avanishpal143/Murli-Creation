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
        <a href="/index.php" class="footer-logo-link" aria-label="Murli Creations Home">
          <div class="brand-lockup" style="margin-bottom: 12px;">
            <img
              src="/images/brand/Favicon.png?v=3"
              alt="Murli Creations"
              width="36"
              height="46"
              class="brand-emblem-img"
            />
            <div class="brand-text-wrap">
              <span class="brand-name-main" style="font-size: 1.35rem;">MURLI</span>
              <span class="brand-name-sub" style="font-size: 0.65rem;">CREATIONS</span>
            </div>
          </div>
        </a>
        <p class="footer-blurb">
          <?php echo htmlspecialchars($footer['about_text'] ?? 'Sacred mud & mirror art handcrafted by Murli Prasad. Bringing positive energy, devotion, and traditional Indian grandeur to homes across the globe.'); ?>
        </p>

        <!-- Social Icons with Logos -->
        <div class="footer-social-icons">
          <?php if (!empty($social['instagram'])): ?>
            <a href="<?php echo htmlspecialchars($social['instagram']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-btn" aria-label="Instagram" title="Follow on Instagram">
              <?php echo icon_svg('instagram', '', 17); ?>
            </a>
          <?php endif; ?>

          <?php if (!empty($social['facebook'])): ?>
            <a href="<?php echo htmlspecialchars($social['facebook']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-btn" aria-label="Facebook" title="Follow on Facebook">
              <?php echo icon_svg('facebook', '', 17); ?>
            </a>
          <?php endif; ?>

          <?php if (!empty($social['linkedin'])): ?>
            <a href="<?php echo htmlspecialchars($social['linkedin']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-btn" aria-label="LinkedIn" title="Connect on LinkedIn">
              <?php echo icon_svg('linkedin', '', 17); ?>
            </a>
          <?php endif; ?>

          <?php if (!empty($social['pinterest'])): ?>
            <a href="<?php echo htmlspecialchars($social['pinterest']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-btn" aria-label="Pinterest" title="Follow on Pinterest">
              <?php echo icon_svg('pinterest', '', 17); ?>
            </a>
          <?php endif; ?>

          <?php if (!empty($social['whatsapp'])): ?>
            <a href="<?php echo htmlspecialchars($social['whatsapp']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-btn" aria-label="WhatsApp" title="Chat on WhatsApp">
              <?php echo icon_svg('whatsapp', '', 17); ?>
            </a>
          <?php endif; ?>

          <?php if (!empty($social['linktree'])): ?>
            <a href="<?php echo htmlspecialchars($social['linktree']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-btn" aria-label="Linktree" title="All Links on Linktree">
              <?php echo icon_svg('linktree', '', 17); ?>
            </a>
          <?php endif; ?>

          <?php if (!empty($social['youtube'])): ?>
            <a href="<?php echo htmlspecialchars($social['youtube']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-btn" aria-label="YouTube" title="Subscribe on YouTube">
              <?php echo icon_svg('youtube', '', 17); ?>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Contact & Studio Address -->
    <div class="footer-tilt-wrapper">
      <div class="footer-column-links">
        <h4 class="footer-title">Studio & Contact</h4>
        <div class="footer-address-list">
          <p class="footer-address-item">
            <strong>Studio:</strong> <?php echo htmlspecialchars($studio['address'] ?? 'Pitampura, Delhi - 110034, India'); ?>
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
          <li><a href="/track-order.php" style="color:#d4af37; font-weight:600;">Track Order Live 📦</a></li>
          <li><a href="/founder-story.php">Founder's Story</a></li>
          <li><a href="/blog.php">Artisan Blog & Vastu</a></li>
          <li><a href="/about.php">About Studio</a></li>
          <li><a href="/contact.php">Contact & Custom Art</a></li>
        </ul>
      </div>
    </div>

    <!-- Policies & Legal Links -->
    <div class="footer-tilt-wrapper">
      <div class="footer-column-links">
        <h4 class="footer-title">Policies</h4>
        <ul class="footer-link-list">
          <li><a href="/return-policy.php">Refund & Return Policy</a></li>
          <li><a href="/shipping-policy.php">Shipping & Delivery Policy</a></li>
          <li><a href="/privacy-policy.php">Privacy Policy</a></li>
          <li><a href="/terms.php">Terms of Service</a></li>
          <li><a href="/cancellation-policy.php">Cancellation Policy</a></li>
        </ul>
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
        <?php if (!empty($social['instagram'])): ?>
          <a href="<?php echo htmlspecialchars($social['instagram']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link">
            <?php echo icon_svg('instagram', '', 14); ?> <span>Instagram</span>
          </a>
          <span class="footer-social-separator">•</span>
        <?php endif; ?>

        <?php if (!empty($social['facebook'])): ?>
          <a href="<?php echo htmlspecialchars($social['facebook']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link">
            <?php echo icon_svg('facebook', '', 14); ?> <span>Facebook</span>
          </a>
          <span class="footer-social-separator">•</span>
        <?php endif; ?>

        <?php if (!empty($social['linkedin'])): ?>
          <a href="<?php echo htmlspecialchars($social['linkedin']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link">
            <?php echo icon_svg('linkedin', '', 14); ?> <span>LinkedIn</span>
          </a>
          <span class="footer-social-separator">•</span>
        <?php endif; ?>

        <?php if (!empty($social['pinterest'])): ?>
          <a href="<?php echo htmlspecialchars($social['pinterest']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link">
            <?php echo icon_svg('pinterest', '', 14); ?> <span>Pinterest</span>
          </a>
          <span class="footer-social-separator">•</span>
        <?php endif; ?>

        <?php if (!empty($social['whatsapp'])): ?>
          <a href="<?php echo htmlspecialchars($social['whatsapp']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link">
            <?php echo icon_svg('whatsapp', '', 14); ?> <span>WhatsApp</span>
          </a>
          <span class="footer-social-separator">•</span>
        <?php endif; ?>

        <?php if (!empty($social['youtube'])): ?>
          <a href="<?php echo htmlspecialchars($social['youtube']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link">
            <?php echo icon_svg('youtube', '', 14); ?> <span>YouTube</span>
          </a>
          <span class="footer-social-separator">•</span>
        <?php endif; ?>

        <?php if (!empty($social['linktree'])): ?>
          <a href="<?php echo htmlspecialchars($social['linktree']); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link">
            <?php echo icon_svg('linktree', '', 14); ?> <span>Linktree</span>
          </a>
          <span class="footer-social-separator">•</span>
        <?php endif; ?>

        <a href="/contact.php" class="footer-social-link">Support</a>
      </div>
    </div>
  </div>
</footer>
