<?php
/**
 * =============================================================================
 * MURLI CREATIONS - ABOUT VIEW
 * =============================================================================
 * Is page ka design, layout aur storefront logic yahan sthit hai.
 * Backend data: backend/config/functions.php
 * =============================================================================
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}
require_once ROOT_PATH . '/backend/config/functions.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Our Story | From a Mother’s Hands to Murli Creations</title>
  <meta name="description" content="Every creation has a story. Discover how Murli Creations began from childhood memories of a mother's handmade art and devotion to Khatu Shyam Baba." />
  
  <!-- CSS Stylesheets -->
  <link rel="stylesheet" href="/assets/css/globals.css" />
  <link rel="stylesheet" href="/assets/css/header.css" />
  <link rel="stylesheet" href="/assets/css/footer.css" />
  <link rel="stylesheet" href="/assets/css/cart-drawer.css" />
  <link rel="stylesheet" href="/assets/css/floating-widgets.css" />
  <link rel="stylesheet" href="/assets/css/toast.css" />
  <link rel="stylesheet" href="/assets/css/about.css" />
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main style="padding-top: 80px;">
    <div class="about-page">
      
      <!-- 1. Hero Header -->
      <section class="about-hero">
        <div class="container about-hero-grid">
          <div class="about-hero-content reveal-on-scroll">
            <span class="about-playful-badge">✨ Our Story • A Journey of Art & Devotion</span>
            <h1 class="about-main-title">From a Mother’s Hands to Murli Creations</h1>
            <p class="about-lead-paragraph">
              Every creation has a story. Ours began with a mother, a daughter, and an enduring love for handmade art. A journey of childhood memories, patient craftsmanship, devotion to Shyam Baba, and the sacred beauty of handcrafted art.
            </p>
            <div class="about-hero-actions">
              <a href="/shop.php" class="about-primary-btn">
                Explore Our Creations <?php echo icon_svg('arrow-right', 'btn-icon', 16); ?>
              </a>
              <a href="/contact.php" class="about-secondary-btn">
                Get in Touch
              </a>
            </div>
          </div>
          
          <div class="about-hero-image-frame reveal-on-scroll">
            <img 
              src="/img-prodcut/IMG_4041.png" 
              alt="Handcrafted Sacred Peacock Jharokha Art by Murli Creations" 
            />
            <div class="about-image-caption-pill">
              <span>🦚 Handcrafted with Love & Devotion</span>
            </div>
          </div>
        </div>
      </section>

      <!-- 2. Emotional Quote Banner -->
      <section class="about-quote-section reveal-on-scroll">
        <div class="container">
          <div class="about-quote-box">
            <div class="about-quote-icon"><?php echo icon_svg('quote', '', 32); ?></div>
            <p class="about-playful-quote">
              “What started with watching my mother create with her hands became my own journey of creating with love.”
            </p>
            <span class="about-quote-author">— Murli Creations</span>
          </div>
        </div>
      </section>

      <!-- 3. Story Chapters Narrative -->
      <section class="about-narrative">
        <div class="container about-narrative-grid">
          
          <!-- Chapter 1 -->
          <div class="about-narrative-text reveal-on-scroll">
            <span class="chapter-number-tag">Chapter 01</span>
            <h2 class="about-block-title">Where It All Began</h2>
            <p class="about-paragraph">
              The inspiration behind Murli Creations began in my childhood, with my mother.
            </p>
            <p class="about-paragraph">
              When I was little, I would often watch my mother create beautiful things with her hands. I remember seeing her decorate the earthen pots in our home with vibrant colours and transform simple wool into beautiful handmade creations with care and patience.
            </p>
            <p class="about-paragraph">
              At that age, I often wondered — <em>Why do things made by hand seem so much more attractive, special, and full of life than things made in a factory?</em>
            </p>
            <p class="about-paragraph">
              As I grew older, I slowly understood the answer: <strong>every handmade creation is different</strong>. It carries not only creativity and art, but also time, patience, effort, and a little piece of the person who made it. No two handmade pieces are ever exactly alike — and that is what makes them truly priceless.
            </p>
          </div>

          <!-- Chapter 2 -->
          <div class="about-narrative-text reveal-on-scroll">
            <span class="chapter-number-tag">Chapter 02</span>
            <h2 class="about-block-title">A Memory That Became My Inspiration</h2>
            <p class="about-paragraph">
              After my mother passed away, those childhood memories remained deeply cherished and close to my heart. I frequently found myself thinking about the things she used to make and the warmth, happiness, and joy she brought into our home through her creativity.
            </p>
            <p class="about-paragraph">
              One thought kept coming back to my heart:
            </p>
            <div class="story-highlight-quote">
              <p>“Why don't I create things with my own hands, just as Maa used to do when I was a child?”</p>
            </div>
            <p class="about-paragraph">
              That very thought became the spark and the beginning of my own creative journey.
            </p>
          </div>

          <!-- Chapter 3: Inspired by Shyam Baba -->
          <div class="about-devotion-block reveal-on-scroll">
            <div class="about-devotion-flex">
              <div class="about-devotion-content">
                <span class="about-badge-devotion">Chapter 03 • Divine Faith 🦚</span>
                <h3 class="about-devotion-title">Inspired by Shyam Baba</h3>
                <p class="about-paragraph">
                  When I decided to start creating handmade products, I wanted the name of my work to carry a deeper, spiritual meaning. That is how I thought of <strong>Murli Creations</strong> — a name inspired by <strong>Khatu Shyam Baba</strong>, whose presence has been a constant source of faith, strength, and inspiration in my journey.
                </p>
                <p class="about-paragraph">
                  I began my work with handmade creations for Khatu Shyam Baba: designer heavy dresses (poshak), handmade pearl necklaces, mor pankh creations, and morpankhi tilak.
                </p>
                <p class="about-paragraph">
                  At first, I created these pieces with pure devotion for friends and relatives. Soon, something unforgettable happened: devotees began not only purchasing them, but <strong>offering them with heartfelt love at the feet of Shyam Baba</strong>.
                </p>
                <div class="story-blessing-tag">
                  ✨ Knowing that something made by my hands became part of someone's sacred prayer gave my work eternal purpose.
                </div>
                <div class="about-actions-row">
                  <a href="/shop.php?category=Shyam+Ji+Decor" class="about-cta-btn">
                    Explore Shyam Baba Collection <?php echo icon_svg('arrow-right', '', 14); ?>
                  </a>
                </div>
              </div>
              <div class="about-devotion-image">
                <img 
                  src="/img-prodcut/IMG_4158.png" 
                  alt="Khatu Shyam Baba Sacred Temple Decor & Lippan Art" 
                />
              </div>
            </div>
          </div>

          <!-- Chapter 4: Discovering Lippan Art -->
          <div class="about-narrative-text reveal-on-scroll">
            <span class="chapter-number-tag">Chapter 04</span>
            <h2 class="about-block-title">From Devotion to Lippan Art</h2>
            <p class="about-paragraph">
              The love and trust of patrons encouraged me to explore further. Soon, requests arrived for temple décor, festive decorations, and wall decor.
            </p>
            <p class="about-paragraph">
              As my journey expanded, I wanted to incorporate rich Indian heritage art forms. That is when <strong>Lippan Art (traditional clay-mirror relief work)</strong> became an integral part of Murli Creations.
            </p>
            <p class="about-paragraph">
              What started with a few devotional creations gradually blossomed into an extensive collection: sacred wall murals, Peacock panels 🦚, Jharokha window frames, customized family name plates, and festive Diwali pieces.
            </p>
          </div>

          <!-- Chapter 5: The Heart Behind Murli Creations -->
          <div class="about-narrative-text reveal-on-scroll">
            <span class="chapter-number-tag">Chapter 05</span>
            <h2 class="about-block-title">The Heart Behind Our Creations</h2>
            <p class="about-paragraph">
              Murli Creations is not simply a brand that sells handmade items. It is a daughter's heartfelt tribute to her mother's memory.
            </p>
            <p class="about-paragraph">
              Every single piece reminds me of where this journey began. It carries that childhood inspiration, the patience learned from handmade craftsmanship, and the devotion that inspired our name.
            </p>
            <p class="about-paragraph">
              Whether you are looking for a statement piece for your home, something auspicious for your mandir, a devotional poshak for Shyam Baba, or a personalized gift, we pour love into every contour and mirror.
            </p>
          </div>

        </div>
      </section>

      <!-- 4. The Studio Promise -->
      <section class="about-milestones reveal-on-scroll">
        <div class="container">
          <div class="promise-box-card">
            <span class="promise-badge">Our Eternal Promise</span>
            <h2 class="promise-title">Handcrafted with Love • Inspired by Tradition • Created to Be Cherished</h2>
            <p class="promise-subtext">
              “This is more than a brand. This is my journey. This is my mother's inspiration. This is my devotion. This is Murli Creations. 🦚”
            </p>
          </div>

          <div class="milestones-grid" style="margin-top: 32px;">
            <div class="milestone-card">
              <div class="milestone-num" data-count="100" data-suffix="%">0%</div>
              <div class="milestone-label">Handcrafted Authenticity</div>
              <p class="milestone-desc">Zero mass machinery — 100% sculpted and mirror-inlaid by artisan hands.</p>
            </div>

            <div class="milestone-card">
              <div class="milestone-num" data-count="100" data-suffix="+">0+</div>
              <div class="milestone-label">Sanctuaries Blessed</div>
              <p class="milestone-desc">Adorning pooja mandirs, luxury villas, and loving residences across India.</p>
            </div>

            <div class="milestone-card">
              <div class="milestone-num" data-count="4.9" data-decimals="1" data-suffix="★">0.0★</div>
              <div class="milestone-label">Patron Rating</div>
              <p class="milestone-desc">Acclaimed for safe protective packaging, precision, and sacred aesthetic.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 5. Bespoke Custom Commission CTA -->
      <section class="about-cta-section reveal-on-scroll">
        <div class="container">
          <div class="about-cta-card">
            <div class="about-cta-body">
              <span class="cta-mini-badge"><?php echo icon_svg('sparkles', '', 16); ?> Bespoke Commissions</span>
              <h2 class="cta-card-title">Looking for Custom Sizing, Family Name Plates or Devotional Themes?</h2>
              <p class="cta-card-text">
                Whether you need a custom-dimension Lippan centerpiece for your living room, a handcrafted name plate for your entryway, or a specific poshak and shringar set for your deity, we craft personalized creations with love.
              </p>
              <div class="cta-buttons-flex">
                <a href="/contact.php" class="cta-white-btn">
                  Consult With Artisan <?php echo icon_svg('send', '', 14); ?>
                </a>
                <a href="https://wa.me/919999999999?text=Hello%20Murli%20Creations,%20I%20am%20interested%20in%20a%20custom%20artwork" target="_blank" rel="noopener noreferrer" class="cta-outline-btn">
                  WhatsApp Inquiry
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <?php include __DIR__ . '/includes/cart-drawer.php'; ?>
  <?php include __DIR__ . '/includes/floating-widgets.php'; ?>
  <?php include __DIR__ . '/includes/toast.php'; ?>

  <script src="/assets/js/tilt.js"></script>
  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/main.js"></script>
</body>
</html>
