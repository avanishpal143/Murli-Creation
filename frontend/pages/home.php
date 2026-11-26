<?php
/**
 * =============================================================================
 * MURLI CREATIONS - HOME VIEW
 * =============================================================================
 * Is page ka design, layout aur storefront logic yahan sthit hai.
 * Backend data: backend/config/functions.php
 * =============================================================================
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}
require_once ROOT_PATH . '/backend/config/functions.php';


$homepage_content = get_homepage_content();
$site_settings = get_site_settings();
$hero = $homepage_content['hero'] ?? [];
$seasonal = $homepage_content['seasonal_vibes'] ?? [];
$seasonalCards = $seasonal['cards'] ?? [];
$festiveOffer = $homepage_content['festive_offer'] ?? [];
$story = $homepage_content['story'] ?? [];
$customCommission = $homepage_content['custom_commission'] ?? [];
$faqs = !empty($site_settings['faqs']) ? $site_settings['faqs'] : ($homepage_content['faqs'] ?? []);

$featured_products = get_featured_products(8);
if (empty($featured_products)) {
    $all = get_all_products();
    $featured_products = array_slice($all, 0, 8);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php if (function_exists('render_page_seo_head')): ?>
    <?php echo render_page_seo_head('home'); ?>
  <?php else: ?>
    <title>Murli Creations | Handcrafted Lippan Art & Sacred Wall Decor</title>
    <meta name="description" content="Discover authentic handcrafted Lippan art, Jharokhas, and sacred spiritual wall mandalas by master artisan Murli Prasad. Made in Delhi, India." />
  <?php endif; ?>
  
  <!-- CSS Stylesheets -->
  <link rel="stylesheet" href="/assets/css/globals.css" />
  <link rel="stylesheet" href="/assets/css/header.css" />
  <link rel="stylesheet" href="/assets/css/footer.css" />
  <link rel="stylesheet" href="/assets/css/product-card.css" />
  <link rel="stylesheet" href="/assets/css/cart-drawer.css" />
  <link rel="stylesheet" href="/assets/css/floating-widgets.css" />
  <link rel="stylesheet" href="/assets/css/toast.css" />
  <link rel="stylesheet" href="/assets/css/home.css" />
</head>
<body>
  <?php include ROOT_PATH . '/frontend/components/header.php'; ?>

  <main style="padding-top: 80px;">
    
    <!-- 1. Hero Showcase with Interactive Dark & Gold Particles Canvas -->
    <section class="hero" id="hero-section">
      <canvas id="hero-particles-canvas" class="hero-particles-canvas" aria-hidden="true"></canvas>
      
      <div class="art-particles-container" aria-hidden="true">
        <div class="art-particle particle-shard-1"></div>
        <div class="art-particle particle-shard-2"></div>
        <div class="art-particle particle-orb-1"></div>
        <div class="art-particle particle-shard-3"></div>
        <div class="art-particle particle-orb-2"></div>
        <div class="art-particle particle-sparkle-1"><?php echo icon_svg('sparkles', 'particle-svg', 18); ?></div>
        <div class="art-particle particle-sparkle-2"><?php echo icon_svg('sparkles', 'particle-svg', 14); ?></div>
      </div>

      <div class="container hero-grid">
        <div class="hero-content reveal-on-scroll">
          <div class="hero-contact-badge-wrap">
            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $site_settings['studio']['phone'] ?? '+919999999999'); ?>" class="hero-contact-badge" title="Call Direct Studio Support">
              <?php echo icon_svg('phone', 'hero-badge-svg', 13); ?>
              <span>Direct Support: <strong><?php echo htmlspecialchars($site_settings['studio']['phone'] ?? '+91 99999 99999'); ?></strong></span>
            </a>
            <a href="<?php echo htmlspecialchars($site_settings['social']['whatsapp'] ?? ('https://wa.me/' . preg_replace('/[^0-9]/', '', $site_settings['studio']['whatsapp'] ?? '919999999999'))); ?>" target="_blank" rel="noopener noreferrer" class="hero-contact-badge hero-whatsapp-badge" title="Chat with Studio on WhatsApp">
              <?php echo icon_svg('whatsapp', 'hero-badge-svg', 14); ?>
              <span>WhatsApp Studio</span>
            </a>
          </div>
          <h1 class="hero-title">
            <?php echo htmlspecialchars($hero['title_main'] ?? 'Authentic Handcrafted Lippan Art for'); ?> <span class="hero-title-highlight"><?php echo htmlspecialchars($hero['title_highlight'] ?? 'Soulful Living Spaces'); ?></span>
          </h1>
          <p class="hero-description">
            <?php echo htmlspecialchars($hero['description'] ?? 'Bring auspicious energy and timeless Indian grandeur into your home. Master artisan Murli Prasad blends sacred mud clay, precision mirrors, and vibrant devotions into heirloom-grade wall masterpieces.'); ?>
          </p>
          <div class="hero-actions">
            <a href="<?php echo htmlspecialchars($hero['cta_primary_link'] ?? '/shop.php'); ?>" class="hero-primary-btn">
              <?php echo htmlspecialchars($hero['cta_primary_text'] ?? 'Explore Catalog'); ?> <?php echo icon_svg('arrow-right', 'btn-icon-svg', 16); ?>
            </a>
            <a href="<?php echo htmlspecialchars($hero['cta_secondary_link'] ?? '/about.php'); ?>" class="hero-secondary-btn">
              <?php echo htmlspecialchars($hero['cta_secondary_text'] ?? 'About Our Studio'); ?>
            </a>
          </div>
        </div>

        <div class="hero-image-tilt reveal-on-scroll" data-tilt>
          <div class="hero-image-container">
            <img 
              src="<?php echo htmlspecialchars($hero['image'] ?? '/img-prodcut/IMG_4041.png'); ?>" 
              alt="Handcrafted Sacred Lippan Masterpiece by Murli Prasad" 
            />
          </div>
        </div>
      </div>
    </section>

    <!-- 2. Dynamic Progressive Number Counter Stats Section (Visible on Scroll below Hero) -->
    <section class="home-stats reveal-on-scroll">
      <div class="container">
        <div class="stats-grid">
          <?php foreach (($homepage_content['stats'] ?? []) as $st): ?>
            <div class="stat-card">
              <div class="stat-num" data-count="<?php echo htmlspecialchars($st['count']); ?>" data-decimals="<?php echo htmlspecialchars($st['decimals'] ?? '0'); ?>" data-suffix="<?php echo htmlspecialchars($st['suffix'] ?? ''); ?>">
                0<?php echo htmlspecialchars($st['suffix'] ?? ''); ?>
              </div>
              <div class="stat-label"><?php echo htmlspecialchars($st['label']); ?></div>
              <p class="stat-desc"><?php echo htmlspecialchars($st['desc']); ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- 3. Featured Masterpieces Section (Sleek Compact Cards + Beautiful Preview) -->
    <section class="home-featured reveal-on-scroll">
      <div class="container">
        <div class="section-header">
          <div>
            <h2 class="section-title">Featured Masterpieces</h2>
            <p class="section-subtitle">Hand-sculpted centerpiece artworks ready to illuminate your walls</p>
          </div>
          <a href="/shop.php" class="section-link">
            View All Catalog <?php echo icon_svg('arrow-right', '', 14); ?>
          </a>
        </div>

        <div class="products-grid compact-grid">
          <?php foreach ($featured_products as $product): ?>
            <div>
              <?php render_product_card($product); ?>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Prominent Visit Shop CTA under Featured -->
        <div class="visit-shop-container">
          <a href="/shop.php" class="home-visit-shop-btn">
            <span>Visit Shop & Explore Entire Collection (<?php echo count(get_all_products()); ?> Pieces)</span>
            <?php echo icon_svg('arrow-right', '', 18); ?>
          </a>
        </div>
      </div>
    </section>

    <!-- 4. Seasonal Vibe / Festive Curations Section -->
    <section class="home-season-vibe reveal-on-scroll">
      <div class="container">
        <div class="season-vibe-header">
          <span class="season-pill-badge"><?php echo icon_svg('sun', '', 14); ?> <?php echo htmlspecialchars($seasonal['badge'] ?? 'Curated Seasonal Collections'); ?></span>
          <h2 class="season-vibe-title"><?php echo htmlspecialchars($seasonal['title'] ?? 'Seasonal Vibes & Festive Auspicious Curations'); ?></h2>
          <p class="season-vibe-subtitle">
            <?php echo htmlspecialchars($seasonal['subtitle'] ?? 'Curated themes matching Indian celebrations, festive light reflections, and sanctuary harmony.'); ?>
          </p>
        </div>

        <div class="season-vibe-grid">
          <?php foreach ($seasonalCards as $card): ?>
            <div class="season-vibe-card">
              <div class="season-vibe-img">
                <img src="<?php echo htmlspecialchars($card['image'] ?? '/img-prodcut/IMG_3426.png'); ?>" alt="<?php echo htmlspecialchars($card['title'] ?? 'Festive Decor'); ?>" />
                <span class="season-theme-tag"><?php echo htmlspecialchars($card['tag'] ?? 'Curated'); ?></span>
              </div>
              <div class="season-vibe-info">
                <h3 class="season-card-title"><?php echo htmlspecialchars($card['title'] ?? ''); ?></h3>
                <p class="season-card-desc"><?php echo htmlspecialchars($card['desc'] ?? ''); ?></p>
                <a href="<?php echo htmlspecialchars($card['link'] ?? '/shop.php'); ?>" class="season-card-link">
                  <?php echo htmlspecialchars($card['link_text'] ?? 'Explore Collection'); ?> <?php echo icon_svg('arrow-right', '', 14); ?>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- 5. Dedicated Our Story Showcase Section -->
    <section class="home-story-section reveal-on-scroll">
      <div class="container">
        <div class="home-story-grid">
          <!-- Visual Column -->
          <div class="home-story-visual">
            <div class="home-story-frame">
              <img src="<?php echo htmlspecialchars($story['image'] ?? '/img-prodcut/IMG_3495.png'); ?>" alt="Handcrafted Lotus Mandala by Murli Creations" loading="lazy" />
              <div class="story-floating-badge">
                <span>🌸 Handcrafted with Love & Devotion</span>
              </div>
            </div>
            <div class="home-story-quote-card">
              <p class="story-card-quote">
                <?php echo htmlspecialchars($story['quote'] ?? '“What started with watching my mother create with her hands became my own journey of creating with love.”'); ?>
              </p>
              <span class="story-card-tag">— <?php echo htmlspecialchars($story['author'] ?? 'Murli Creations'); ?></span>
              <small style="display: block; font-size: 0.72rem; color: var(--gold-accent, #c99846); margin-top: 4px; font-weight: 600;">
                <?php echo htmlspecialchars($story['promise'] ?? 'Handcrafted with Love • Inspired by Tradition • Created to Be Cherished'); ?>
              </small>
            </div>
          </div>

          <!-- Content Column -->
          <div class="home-story-content">
            <span class="story-section-pill"><?php echo htmlspecialchars($story['badge'] ?? '✨ Our Story • A Daughter\'s Tribute'); ?></span>
            <h2 class="home-story-title"><?php echo htmlspecialchars($story['title'] ?? 'From a Mother’s Hands to Murli Creations'); ?></h2>
            <p class="home-story-lead">
              <?php echo htmlspecialchars($story['lead'] ?? 'A journey of handmade art, memories, devotion and love.'); ?>
            </p>
            <p class="home-story-text">
              The inspiration behind Murli Creations began in my childhood, with my mother. As a little girl, I would often watch my mother decorate earthen pots with vibrant colors and transform simple wool into cherished handmade pieces. After she passed away, those memories inspired me to continue her tradition of creating with love and patience.
            </p>
            <p class="home-story-text">
              Inspired by <strong>Khatu Shyam Baba</strong>, I began crafting devotional poshak and temple shringar. When devotees offered these creations at the Lord's feet, knowing my hands had become part of someone's prayer gave my work eternal purpose. Today, that devotion lives in every Lippan mural, sacred frame, and custom creation.
            </p>

            <div class="home-story-highlights">
              <?php 
              $hlIcons = ['heart', 'sparkles', 'award'];
              foreach (($story['highlights'] ?? []) as $i => $hl): 
                $iconName = $hlIcons[$i % count($hlIcons)];
              ?>
                <div class="story-highlight-item">
                  <div class="story-hl-icon"><?php echo icon_svg($iconName, '', 18); ?></div>
                  <div class="story-hl-text">
                    <strong><?php echo htmlspecialchars($hl['title'] ?? ''); ?></strong>
                    <span><?php echo htmlspecialchars($hl['desc'] ?? ''); ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="home-story-actions">
              <a href="/founder-story.php" class="home-story-btn">
                Read Our Full Story <?php echo icon_svg('arrow-right', '', 15); ?>
              </a>
              <a href="/shop.php?category=Shyam+Ji+Decor" class="home-story-link">
                Explore Shyam Baba Collection →
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 6. Special Festive Offer Section -->
    <?php if (!empty($festiveOffer['enabled'])): ?>
    <section class="home-offer-section reveal-on-scroll">
      <div class="container">
        <div class="offer-card">
          <div class="offer-badge-col">
            <span class="offer-tag"><?php echo icon_svg('tag', '', 14); ?> <?php echo htmlspecialchars($festiveOffer['badge'] ?? 'Limited Period Offer'); ?></span>
            <div class="offer-discount-huge"><?php echo htmlspecialchars($festiveOffer['discount_label'] ?? '10% OFF'); ?></div>
          </div>

          <div class="offer-content-col">
            <h3 class="offer-title"><?php echo htmlspecialchars($festiveOffer['title'] ?? 'Celebrate Authentic Art in Your Home'); ?></h3>
            <p class="offer-desc">
              <?php echo htmlspecialchars($festiveOffer['description'] ?? ''); ?>
            </p>
            <div class="offer-actions">
              <button class="offer-copy-btn" data-copy-coupon="<?php echo htmlspecialchars($festiveOffer['coupon_code'] ?? 'MURLI10'); ?>">
                <?php echo icon_svg('copy', '', 14); ?> Copy Code: <?php echo htmlspecialchars($festiveOffer['coupon_code'] ?? 'MURLI10'); ?>
              </button>
              <a href="<?php echo htmlspecialchars($festiveOffer['button_link'] ?? '/shop.php'); ?>" class="offer-shop-btn">
                <?php echo htmlspecialchars($festiveOffer['button_text'] ?? 'Claim Offer in Shop'); ?> <?php echo icon_svg('arrow-right', '', 14); ?>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- 6. CTA 1: Minimalist Glassmorphic Custom Commission CTA -->
    <section class="home-cta-section reveal-on-scroll">
      <div class="container">
        <div class="glass-cta-card">
          <div class="glass-cta-body">
            <span class="glass-cta-badge"><?php echo icon_svg('sparkles', '', 14); ?> <?php echo htmlspecialchars($customCommission['badge'] ?? 'Bespoke Commissions'); ?></span>
            <h2 class="glass-cta-title"><?php echo htmlspecialchars($customCommission['title'] ?? 'Need a Custom Sized Artwork for Your Living Room or Pooja Mandir?'); ?></h2>
            <p class="glass-cta-text">
              <?php echo htmlspecialchars($customCommission['description'] ?? 'Murli Prasad personally collaborates with homeowners, architects, and interior designers to sculpt custom dimension pieces matching your color palette and spiritual intentions.'); ?>
            </p>
            <div class="glass-cta-buttons">
              <a href="/contact.php" class="glass-cta-primary-btn">
                Book Consultation <?php echo icon_svg('send', '', 14); ?>
              </a>
              <a href="<?php echo htmlspecialchars($customCommission['cta_link'] ?? ('https://wa.me/' . preg_replace('/[^0-9]/', '', $site_settings['studio']['whatsapp'] ?? '919999999999'))); ?>" target="_blank" rel="noopener noreferrer" class="glass-cta-outline-btn">
                <?php echo icon_svg('phone', '', 14); ?> WhatsApp Studio
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 7. Interactive Testimonials Slider Section with Side Navigation -->
    <section class="home-testimonials reveal-on-scroll">
      <div class="container">
        <div class="section-header-centered">
          <span class="section-badge-pill"><?php echo icon_svg('heart', '', 13); ?> Verified Collector Love</span>
          <h2 class="section-title-centered">Cherished in Over 100+ Sacred Homes</h2>
          <p class="section-subtitle-centered">Read authentic reflections from patrons who welcomed Murli Creations art into their sanctuaries.</p>
        </div>

        <div class="testimonials-slider-wrapper">
          <!-- Left Navigation Button -->
          <button class="slider-nav-btn slider-prev" id="testi-prev" aria-label="Previous Testimonial">
            <?php echo icon_svg('chevron-right', 'prev-icon', 22); ?>
          </button>

          <!-- Slider Track -->
          <div class="testimonials-slider-track" id="testi-track">
            <!-- Review 1 -->
            <div class="testimonial-slide">
              <div class="testimonial-card">
                <div class="testimonial-rating">
                  <?php for ($i=0; $i<5; $i++) echo icon_svg('star', 'star-gold', 14); ?>
                </div>
                <p class="testimonial-quote">
                  "The Tree of Life Lippan piece exceeded our expectations! The mud relief work is so crisp and the mirrors sparkle beautifully under our warm pooja lights. Arrived flawlessly packed in wooden framing."
                </p>
                <div class="testimonial-author">
                  <div class="author-avatar">RS</div>
                  <div class="author-info">
                    <span class="author-name">Radhika Sharma</span>
                    <span class="author-location">Gurugram, Haryana • Verified Buyer</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Review 2 -->
            <div class="testimonial-slide">
              <div class="testimonial-card">
                <div class="testimonial-rating">
                  <?php for ($i=0; $i<5; $i++) echo icon_svg('star', 'star-gold', 14); ?>
                </div>
                <p class="testimonial-quote">
                  "Ordered the Khatu Shyam Baba relief frame for our new flat's entrance. The aura of the artwork is pure tranquility. You can truly feel the artisan’s devotion in every stroke."
                </p>
                <div class="testimonial-author">
                  <div class="author-avatar">AK</div>
                  <div class="author-info">
                    <span class="author-name">Amit Khandelwal</span>
                    <span class="author-location">Jaipur, Rajasthan • Verified Buyer</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Review 3 -->
            <div class="testimonial-slide">
              <div class="testimonial-card">
                <div class="testimonial-rating">
                  <?php for ($i=0; $i<5; $i++) echo icon_svg('star', 'star-gold', 14); ?>
                </div>
                <p class="testimonial-quote">
                  "As an interior designer, finding authentic, non-plastic Lippan art is rare. Murli Creations delivers real heirloom quality. My client was ecstatic with the 36-inch Royal Peacock Mandala."
                </p>
                <div class="testimonial-author">
                  <div class="author-avatar">PM</div>
                  <div class="author-info">
                    <span class="author-name">Pooja Mehra</span>
                    <span class="author-location">South Delhi • Interior Stylist</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Review 4 -->
            <div class="testimonial-slide">
              <div class="testimonial-card">
                <div class="testimonial-rating">
                  <?php for ($i=0; $i<5; $i++) echo icon_svg('star', 'star-gold', 14); ?>
                </div>
                <p class="testimonial-quote">
                  "The Gilded Jharokha is the centerpiece of our living room in Bangalore. The mirrors catch the morning sun in a magical way. Packaging was 10/10 with super secure protective pack."
                </p>
                <div class="testimonial-author">
                  <div class="author-avatar">VR</div>
                  <div class="author-info">
                    <span class="author-name">Dr. Vikramaditya Rao</span>
                    <span class="author-location">Bengaluru, Karnataka • Verified Collector</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Review 5 -->
            <div class="testimonial-slide">
              <div class="testimonial-card">
                <div class="testimonial-rating">
                  <?php for ($i=0; $i<5; $i++) echo icon_svg('star', 'star-gold', 14); ?>
                </div>
                <p class="testimonial-quote">
                  "We gifted the Radha Krishna Shringar panel for our daughter's housewarming. Everyone at the ceremony praised the divine beauty and mud-mirror craftsmanship. Truly auspicious!"
                </p>
                <div class="testimonial-author">
                  <div class="author-avatar">SA</div>
                  <div class="author-info">
                    <span class="author-name">Sunita Aggarwal</span>
                    <span class="author-location">Chandigarh • Verified Buyer</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Review 6 -->
            <div class="testimonial-slide">
              <div class="testimonial-card">
                <div class="testimonial-rating">
                  <?php for ($i=0; $i<5; $i++) echo icon_svg('star', 'star-gold', 14); ?>
                </div>
                <p class="testimonial-quote">
                  "The geometric precision in the Sacred Lotus Mandala is breathtaking. It brings a grounded, meditative energy to my workspace. Thank you Murli Prasad ji for keeping this heritage alive."
                </p>
                <div class="testimonial-author">
                  <div class="author-avatar">DJ</div>
                  <div class="author-info">
                    <span class="author-name">Deepak Joshi</span>
                    <span class="author-location">Ahmedabad, Gujarat • Art Patron</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Navigation Button -->
          <button class="slider-nav-btn slider-next" id="testi-next" aria-label="Next Testimonial">
            <?php echo icon_svg('chevron-right', '', 22); ?>
          </button>
        </div>

        <!-- Slider Pagination Dots -->
        <div class="slider-dots" id="testi-dots"></div>
      </div>
    </section>

    <!-- 8. FAQ Section on Homepage -->
    <section class="home-faq-section reveal-on-scroll">
      <div class="container">
        <div class="faq-container-box">
          <div class="section-header-centered">
            <span class="section-badge-pill">Common Inquiries</span>
            <h2 class="section-title-centered">Frequently Asked Questions</h2>
            <p class="section-subtitle-centered">Everything you need to know about our handcrafted artworks and care.</p>
          </div>

          <div class="faq-accordions">
            <?php foreach ($faqs as $idx => $f): ?>
              <?php 
                $faqQ = $f['question'] ?? $f['q'] ?? '';
                $faqA = $f['answer'] ?? $f['a'] ?? '';
              ?>
              <div class="faq-accordion <?php echo ($idx === 0) ? 'open' : ''; ?>">
                <button class="faq-header-btn">
                  <span><?php echo htmlspecialchars($faqQ); ?></span>
                  <span class="faq-icon"><?php echo icon_svg('plus', '', 16); ?></span>
                </button>
                <div class="faq-content">
                  <?php echo htmlspecialchars($faqA); ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- 9. Direct Contact Form on Homepage -->
    <section class="home-contact-section reveal-on-scroll">
      <div class="container">
        <div class="home-contact-grid">
          <div class="home-contact-intro">
            <span class="section-badge-pill"><?php echo icon_svg('mail', '', 13); ?> Connect With Studio</span>
            <h2 class="home-contact-title">Have a Question or Looking for Art Consultation?</h2>
            <p class="home-contact-desc">
              Send us a direct message. Murli Prasad and our studio care team will respond within 24 hours with sizing recommendations and availability.
            </p>
            
            <div class="contact-quick-list">
              <div class="contact-quick-item">
                <?php echo icon_svg('map-pin', 'contact-svg', 18); ?>
                <span>Workshop: <?php echo htmlspecialchars($site_settings['studio']['location'] ?? 'Pitampura, Delhi, India'); ?></span>
              </div>
              <div class="contact-quick-item">
                <?php echo icon_svg('phone', 'contact-svg', 18); ?>
                <span>Phone / WhatsApp: <?php echo htmlspecialchars($site_settings['studio']['whatsapp'] ?? '+91 99999 99999'); ?></span>
              </div>
              <div class="contact-quick-item">
                <?php echo icon_svg('clock', 'contact-svg', 18); ?>
                <span>Studio Hours: <?php echo htmlspecialchars($site_settings['studio']['hours'] ?? 'Monday – Saturday (10:00 AM – 7:30 PM)'); ?></span>
              </div>
            </div>
          </div>

          <div class="home-contact-form-box">
            <form id="home-contact-form" class="home-contact-form">
              <div class="form-row">
                <div class="form-group">
                  <label for="home-name">Your Full Name *</label>
                  <input type="text" id="home-name" name="name" required placeholder="e.g. Radhika Sharma" class="form-input" />
                </div>
                <div class="form-group">
                  <label for="home-phone">Phone / WhatsApp *</label>
                  <input type="tel" id="home-phone" name="phone" required placeholder="e.g. +91 98765 43210" class="form-input" />
                </div>
              </div>

              <div class="form-group">
                <label for="home-email">Email Address</label>
                <input type="email" id="home-email" name="email" placeholder="e.g. radhika@example.com" class="form-input" />
              </div>

              <div class="form-group">
                <label for="home-interest">Artwork Interest</label>
                <select id="home-interest" name="subject" class="form-select">
                  <option value="Lippan Wall Art">Lippan Mud & Mirror Art</option>
                  <option value="Spiritual / Pooja Room">Spiritual & Pooja Mandir Art</option>
                  <option value="Jharokha Frame">Traditional Jharokha Frames</option>
                  <option value="Custom Commission Sizing">Custom Size / Interior Commission</option>
                  <option value="Bulk / Gifting Order">Corporate / Wedding Return Gifts</option>
                </select>
              </div>

              <div class="form-group">
                <label for="home-message">Message / Dimensions</label>
                <textarea id="home-message" name="message" rows="3" placeholder="Tell us about your room, wall size or preferred colors..." class="form-textarea" required></textarea>
              </div>

              <button type="submit" class="home-contact-submit-btn">
                <span>Send Studio Inquiry</span>
                <?php echo icon_svg('send', '', 14); ?>
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- 10. CTA 2: Minimalist Collector Community CTA -->
    <section class="home-community-cta reveal-on-scroll">
      <div class="container">
        <div class="community-cta-card">
          <div class="community-cta-content">
            <span class="community-pill"><?php echo icon_svg('award', '', 14); ?> VIP Collector Circle</span>
            <h2 class="community-title">Be the First to Receive Rare Deity Panels & Limited Editions</h2>
            <p class="community-text">
              Join over 100+ discerning homeowners. Receive direct studio previews before public releases and seasonal festive care guides.
            </p>
            <div class="community-actions">
              <a href="/shop.php" class="community-primary-btn">
                Browse Full Catalog <?php echo icon_svg('arrow-right', '', 16); ?>
              </a>
              <a href="/about.php" class="community-outline-btn">
                Our Story & Heritage
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include ROOT_PATH . '/frontend/components/footer.php'; ?>
  <?php include ROOT_PATH . '/frontend/components/cart-drawer.php'; ?>
  <?php include ROOT_PATH . '/frontend/components/floating-widgets.php'; ?>
  <?php include ROOT_PATH . '/frontend/components/toast.php'; ?>

  <!-- Scripts -->
  <script src="/assets/js/tilt.js"></script>
  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/shop.js"></script>
  <script src="/assets/js/main.js"></script>
</body>
</html>
