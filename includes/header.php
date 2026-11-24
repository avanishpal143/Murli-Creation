<?php
require_once __DIR__ . '/../config/functions.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$cart_count = get_cart_count();
$category_tree = get_category_tree();
$site_settings = get_site_settings();
$announcement = $site_settings['announcement'] ?? [];
$isBannerEnabled = !empty($announcement['enabled']);
$bannerText = $announcement['text'] ?? 'Festive Offer: Use code {coupon} for 10% OFF on all orders!';
$couponCode = $announcement['coupon_code'] ?? 'MURLI10';

// Offer & CTA Manager Sync
if (function_exists('db_get_offers_cta')) {
    $ctaOffers = db_get_offers_cta();
    if (!empty($ctaOffers['announcement_bar'])) {
        $annCta = $ctaOffers['announcement_bar'];
        $isBannerEnabled = !empty($annCta['is_active']);
        if (!empty($annCta['title'])) $bannerText = $annCta['title'];
        if (!empty($annCta['coupon_code'])) $couponCode = $annCta['coupon_code'];
    }
}

$renderedBanner = str_replace('{coupon}', '<strong>' . htmlspecialchars($couponCode) . '</strong>', htmlspecialchars($bannerText));
?>
<!-- Top Promotional Offer Banner & Fixed Navigation System -->
<div class="site-header-sticky-wrapper" id="siteHeaderWrapper">
  <?php if ($isBannerEnabled): ?>
  <div class="promo-banner">
    <span><?php echo $renderedBanner; ?></span>
  </div>
  <?php endif; ?>

  <header class="site-header">
  <div class="header-container">
    <!-- Mobile Toggle Hamburger -->
    <button class="menu-toggle" aria-label="Toggle navigation menu">
      <span class="hamburger"></span>
    </button>

    <!-- Wordmark Logo Left -->
    <a href="/index.php" class="logo-container" aria-label="Murli Creations Home">
      <div class="brand-lockup">
        <img
          src="/images/brand/Favicon.png?v=3"
          alt="Murli Creations"
          width="32"
          height="42"
          class="brand-emblem-img"
        />
        <div class="brand-text-wrap">
          <span class="brand-name-main">MURLI</span>
          <span class="brand-name-sub">CREATIONS</span>
        </div>
      </div>
    </a>

    <!-- Centered Desktop Nav Links with Nested Dropdown -->
    <nav class="nav-desktop">
      <!-- Home -->
      <a href="/index.php" class="nav-link <?php echo ($current_page === 'index' || $current_page === '') ? 'active' : ''; ?>">
        Home
      </a>

      <!-- Shop with Multi-Tier Mega-Dropdown on Hover -->
      <div class="nav-item-dropdown">
        <a href="/shop.php" class="nav-link nav-dropdown-trigger <?php echo ($current_page === 'shop' || $current_page === 'product') ? 'active' : ''; ?>">
          <span>Shop</span>
          <?php echo icon_svg('chevron-right', 'dropdown-chevron', 14); ?>
        </a>

        <!-- Mega Menu Dropdown Container -->
        <div class="mega-dropdown-menu">
          <div class="mega-menu-inner">
            
            <!-- Column 1: Lippan Art Collections -->
            <div class="mega-column mega-col-lippan">
              <div class="mega-col-header">
                <a href="/shop.php?category=Lippan+Art" class="mega-cat-title">
                  <span>Lippan Art Collections</span>
                </a>
              </div>
              <ul class="mega-sub-list">
                <li><a href="/shop.php?category=Lippan+Art&subcategory=Lippan+Wall+Art">Lippan Wall Art Panels</a></li>
                <li><a href="/shop.php?category=Lippan+Art&subcategory=Lippan+Mirrors">Lippan Mirrors & Round Panels</a></li>
                <li><a href="/shop.php?category=Lippan+Art&subcategory=Lippan+Name+Plates">Custom Name Plates</a></li>
                <li><a href="/shop.php?category=Lippan+Art&subcategory=Lippan+Spiritual+Art">Spiritual & Mandir Art</a></li>
                <li><a href="/shop.php?category=Lippan+Art&subcategory=Lippan+Mandala+Art">Sacred Mandala Art</a></li>
                <li><a href="/shop.php?category=Lippan+Art&subcategory=Lippan+Peacock+Art">Peacock Artwork</a></li>
                <li><a href="/shop.php?category=Lippan+Art&subcategory=Lippan+Floral+Art">Floral & Lotus Art</a></li>
                <li><a href="/shop.php?category=Lippan+Art&subcategory=Lippan+Gift+Collection">Handmade Gift Collection</a></li>
              </ul>
            </div>

            <!-- Column 2: Shyam Ji (Khatu Baba Collection) -->
            <div class="mega-column mega-col-shyam">
              <div class="mega-col-header">
                <a href="/shop.php?category=Shyam+Ji+Decor" class="mega-cat-title">
                  <span>Shyam Ji (Khatu Baba)</span>
                </a>
              </div>
              <ul class="mega-sub-list">
                <li><a href="/shop.php?category=Shyam+Ji+Decor&subcategory=Shyam+Baba+Dresses+%26+Poshak">Shyam Baba Dresses & Poshak</a></li>
                <li><a href="/shop.php?category=Shyam+Ji+Decor&subcategory=Shyam+Baba+Shringar+%26+Flowers">Shringar & Flower Garlands</a></li>
                <li><a href="/shop.php?category=Shyam+Ji+Decor&subcategory=Shyam+Baba+Haar">Shyam Baba Haar</a></li>
                <li><a href="/shop.php?category=Shyam+Ji+Decor&subcategory=Shyam+Baba+Kalangi">Shyam Baba Kalangi</a></li>
                <li><a href="/shop.php?category=Shyam+Ji+Decor&subcategory=Shyam+Baba+Kalangi">Shyam Baba Kalangi</a></li>
              </ul>
            </div>

            <!-- Column 3: Vastu Species & Sacred Art -->
            <div class="mega-column mega-col-vastu">
              <div class="mega-col-header">
                <a href="/shop.php?category=Vastu+Species" class="mega-cat-title">
                  <span>Vastu Species & Energy</span>
                </a>
              </div>
              <ul class="mega-sub-list">
                <li><a href="/shop.php?category=Vastu+Species&subcategory=Vastu+Peacock+(North-East)">Vastu Peacock (North-East Energy)</a></li>
                <li><a href="/shop.php?category=Vastu+Species&subcategory=Tree+of+Life+(Kalpavriksha)">Tree of Life (Kalpavriksha)</a></li>
                <li><a href="/shop.php?category=Vastu+Species&subcategory=Kamdhenu+%26+Gau+Mata">Kamdhenu & Gau Mata (Peace)</a></li>
              </ul>
            </div>

            <!-- Column 4: Diwali & Festive Decor -->
            <div class="mega-column mega-col-festive">
              <div class="mega-col-header">
                <a href="/shop.php?category=Diwali+%26+Festive+Decor" class="mega-cat-title">
                  <span>Diwali & Festive Decor</span>
                </a>
              </div>
              <ul class="mega-sub-list">
                <li><a href="/shop.php?category=Diwali+%26+Festive+Decor&subcategory=Diwali+Toran+%26+Door+D%C3%A9cor">Torans & Door Bandarwals</a></li>
                <li><a href="/shop.php?category=Diwali+%26+Festive+Decor&subcategory=Diwali+Thali+%26+Mandir+Decor">Diwali Aarti Thalis & Mandir Decor</a></li>
                <li><a href="/shop.php?category=Diwali+%26+Festive+Decor&subcategory=Customised+Shubh-Labh">Customised Shubh-Labh</a></li>
              </ul>
            </div>

            <!-- Column 5: Featured Preview Card & Quick Links -->
            <div class="mega-column mega-preview-col">
              <div class="mega-preview-card">
                <div class="mega-preview-img">
                  <img src="/img-prodcut/IMG_4041.png" alt="Peacock Jharokha Masterpiece Preview" />
                  <span class="mega-badge">Featured Art</span>
                </div>
                <div class="mega-preview-details">
                  <h4>Custom Commissions</h4>
                  <p>Hand-sculpted to your wall dimensions & color preferences.</p>
                  <a href="/shop.php" class="mega-explore-btn">
                    <span>View All Catalog (15 Pieces)</span>
                    <?php echo icon_svg('arrow-right', '', 12); ?>
                  </a>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Founder's Story -->
      <a href="/founder-story.php" class="nav-link <?php echo ($current_page === 'founder-story') ? 'active' : ''; ?>">
        Founder's Story
      </a>

      <!-- Journal / Blog -->
      <a href="/blog.php" class="nav-link <?php echo ($current_page === 'blog' || $current_page === 'blog-post') ? 'active' : ''; ?>">
        Blog
      </a>

      <!-- About Us -->
      <a href="/about.php" class="nav-link <?php echo ($current_page === 'about') ? 'active' : ''; ?>">
        About
      </a>

      <!-- Contact -->
      <a href="/contact.php" class="nav-link <?php echo ($current_page === 'contact') ? 'active' : ''; ?>">
        Contact
      </a>
    </nav>

    <!-- Right Actions with Standard SVG Icons -->
    <div class="header-actions">
      <div class="search-wrapper">
        <button class="action-btn search-toggle-btn" aria-label="Search wall art">
          <?php echo icon_svg('search', '', 17); ?>
        </button>
        <form action="/shop.php" method="GET" class="search-form">
          <input
            type="text"
            name="search"
            placeholder="Search by artwork name, SKU code (e.g. MC/S-016)..."
            class="search-input"
            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
          />
          <button type="submit" class="search-submit">
            <?php echo icon_svg('search', '', 14); ?>
          </button>
        </form>
      </div>

      <button class="cart-trigger" aria-label="Open cart drawer">
        <?php echo icon_svg('shopping-bag', 'cart-icon-svg', 18); ?>
        <span class="cart-badge" style="<?php echo ($cart_count > 0) ? 'display: flex;' : 'display: none;'; ?>">
          <?php echo $cart_count; ?>
        </span>
      </button>
    </div>
  </div>
</header>
</div>

<!-- Mobile Menu Panel with Accordion Submenus -->
<div class="mobile-menu">
  <div class="mobile-menu-header">
    <a href="/index.php" class="logo-container" aria-label="Murli Creations Home">
      <div class="brand-lockup">
        <img
          src="/images/brand/Favicon.png?v=3"
          alt="Murli Creations"
          width="28"
          height="36"
          class="brand-emblem-img"
        />
        <div class="brand-text-wrap">
          <span class="brand-name-main" style="font-size: 1.05rem;">MURLI</span>
          <span class="brand-name-sub" style="font-size: 0.52rem;">CREATIONS</span>
        </div>
      </div>
    </a>
    <button class="close-menu-btn" aria-label="Close menu">
      <?php echo icon_svg('x', '', 22); ?>
    </button>
  </div>
  <nav class="nav-mobile">
    <a href="/index.php" class="mobile-nav-link <?php echo ($current_page === 'index' || $current_page === '') ? 'active' : ''; ?>">
      Home
    </a>

    <!-- Mobile Shop Submenu Accordion -->
    <div class="mobile-nav-accordion">
      <button type="button" class="mobile-accordion-toggle">
        <span>Shop Collections</span>
        <?php echo icon_svg('chevron-right', 'mobile-acc-icon', 16); ?>
      </button>
      <div class="mobile-accordion-panel">
        <a href="/shop.php" class="mobile-sub-link bold">View All (36 Creations)</a>
        <a href="/shop.php?category=Lippan+Art" class="mobile-sub-link">Lippan Art Collections</a>
        <a href="/shop.php?category=Shyam+Ji+Decor" class="mobile-sub-link">Shyam Ji (Khatu Baba Collection)</a>
        <a href="/shop.php?category=Vastu+Species" class="mobile-sub-link">Vastu Species & Auspicious Art</a>
        <a href="/shop.php?category=Diwali+%26+Festive+Decor" class="mobile-sub-link">Diwali & Festive Decor</a>
        <a href="/shop.php?category=Customised+Creations" class="mobile-sub-link">Customised Name Plates & Art</a>
        <a href="/shop.php?category=Jharokhas+%26+Frames" class="mobile-sub-link">Royal Jharokhas & Frames</a>
      </div>
    </div>

    <a href="/founder-story.php" class="mobile-nav-link <?php echo ($current_page === 'founder-story') ? 'active' : ''; ?>">
      Founder's Story
    </a>
    <a href="/blog.php" class="mobile-nav-link <?php echo ($current_page === 'blog' || $current_page === 'blog-post') ? 'active' : ''; ?>">
      Artisan Blog & Vastu
    </a>
    <a href="/about.php" class="mobile-nav-link <?php echo ($current_page === 'about') ? 'active' : ''; ?>">
      About
    </a>
    <a href="/contact.php" class="mobile-nav-link <?php echo ($current_page === 'contact') ? 'active' : ''; ?>">
      Contact
    </a>
  </nav>
  <div class="mobile-menu-footer">
    <p class="studio-info">Studio: ITL Twin Tower, Netaji Subhash Place, Pitampura, Delhi - 110034</p>
    <div class="insta-link">
      <a href="https://www.instagram.com/murlicreationsofficial" target="_blank" rel="noopener noreferrer">
        Follow @murlicreationsofficial
      </a>
    </div>
  </div>
</div>

<div class="menu-backdrop"></div>
