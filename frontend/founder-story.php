<?php
/**
 * =============================================================================
 * MURLI CREATIONS - FOUNDER-STORY VIEW
 * =============================================================================
 * Is page ka design, layout aur storefront logic yahan sthit hai.
 * Backend data: backend/config/functions.php
 * =============================================================================
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
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
  <title>Our Story — From a Mother's Hands to Murli Creations | Handcrafted Heritage</title>
  <meta name="description" content="A journey of handmade art, memories, devotion and love. How childhood memories of a mother decorating earthen pots and wool crafts inspired Murli Creations.">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/founder-story.css">
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main style="padding-top: 80px;">
    
    <!-- Hero Section -->
    <section class="story-hero-section">
      <div class="container">
        <div class="story-hero-grid">
          <div class="story-hero-content reveal-on-scroll">
            <span class="story-badge">🌸 Authentic Handcrafted Heritage</span>
            <h1 class="story-hero-title">From a Mother’s Hands to <span class="gradient-text">Murli Creations</span></h1>
            <p class="story-hero-lead">
              A sacred journey of handmade art, cherished childhood memories, divine devotion, and enduring family love.
            </p>
            <div class="story-meta-row">
              <span class="story-meta-item">📍 Pitampura, Delhi Studio</span>
              <span class="story-meta-item">🦚 Inspired by Shyam Baba</span>
              <span class="story-meta-item">✨ 1,200+ Homes Blessed</span>
            </div>
          </div>
          <div class="story-hero-image-wrap reveal-scale">
            <div class="story-image-card">
              <img src="/img-prodcut/IMG_3495.png" alt="Handcrafted Pink Lotus Mandala Relief Art" class="story-main-img" />
              <div class="story-img-caption">
                <strong>Handcrafted Pink Lotus Mandala (12"x12")</strong>
                <span>Sculpted with traditional clay relief and mirror work</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Narrative Chapters Section -->
    <section class="story-narrative-section">
      <div class="container story-narrow-container">
        
        <!-- Hero Introduction Quote -->
        <div class="story-quote-card reveal-on-scroll">
          <div class="quote-mark">“</div>
          <blockquote>
            Every creation has a story. Ours began with a mother, a daughter, and a love for handmade art.
          </blockquote>
          <cite>— Murli Creations Studio</cite>
        </div>

        <!-- Chapter 1: Where It All Began -->
        <article class="story-chapter reveal-on-scroll">
          <div class="chapter-number">01</div>
          <h2 class="chapter-title">Where It All Began</h2>
          <p class="chapter-paragraph">
            The inspiration behind <strong>Murli Creations</strong> began in my childhood, with my mother.
          </p>
          <p class="chapter-paragraph">
            As a little girl, I would often watch my mother create beautiful things with her hands. I remember seeing her decorate the earthen pots around our home with vibrant colours and transform simple wool into cherished handmade pieces, carefully making each item by hand with quiet patience.
          </p>
          <p class="chapter-paragraph">
            Back then, I often wondered: <em>“Why do things made by hand feel so much more beautiful and attractive than things available in the market or made in a factory?”</em>
          </p>
          <p class="chapter-paragraph">
            As I grew older, I slowly understood the answer: <strong>A handmade creation is never just an object.</strong> It carries time, patience, creativity, effort, and a little piece of the person who made it. No two handmade pieces are ever exactly alike—and that is what makes them special.
          </p>
        </article>

        <!-- Chapter 2: The Thought That Became a Beginning -->
        <article class="story-chapter reveal-on-scroll">
          <div class="chapter-number">02</div>
          <h2 class="chapter-title">The Thought That Became a Beginning</h2>
          <p class="chapter-paragraph">
            After my mother passed away, those childhood memories stayed close to my heart. I found myself thinking about the things she used to make and the joy she brought into our home through her creativity.
          </p>
          <p class="chapter-paragraph">
            One thought kept coming back to me:
          </p>
          <div class="story-callout-box">
            <p class="story-callout-text">“Why don't I create things with my own hands, just like Maa used to?”</p>
          </div>
          <p class="chapter-paragraph">
            That single thought became the beginning of something very special. And then came <strong>Murli Creations</strong>.
          </p>
        </article>

        <!-- Chapter 3: Inspired by Shyam Baba -->
        <article class="story-chapter reveal-on-scroll">
          <div class="chapter-number">03</div>
          <h2 class="chapter-title">Inspired by Shyam Baba 🦚</h2>
          <p class="chapter-paragraph">
            The name <strong>Murli Creations</strong> carries a very special meaning for me. Its inspiration comes from <strong>Khatu Shyam Baba</strong>, whose divine presence has been a constant source of faith, strength, and inspiration in my journey.
          </p>
          <p class="chapter-paragraph">
            When I started, I began with handmade creations for Khatu Shyam Baba—beautiful dresses (poshak), handmade pearl necklaces, morpankh chadi, morpankhi tilak, and sacred devotional accessories.
          </p>
          <p class="chapter-paragraph">
            At first, I created these pieces with love and devotion for my friends and relatives. To my happiness, they loved the creations and encouraged me to continue.
          </p>
        </article>

        <!-- Chapter 4: From Devotion to Creation -->
        <article class="story-chapter reveal-on-scroll">
          <div class="chapter-number">04</div>
          <h2 class="chapter-title">From Devotion to Creation</h2>
          <p class="chapter-paragraph">
            Slowly, people began not only appreciating the things I made, but also purchasing them and, most importantly, <strong>offering them with love at the feet of Shyam Baba.</strong>
          </p>
          <p class="chapter-paragraph">
            That feeling was unforgettable. Knowing that something made by my own hands had become a part of someone's devotion gave my work a meaning far beyond simply creating and selling handmade products.
          </p>
          <p class="chapter-paragraph">
            As more people discovered my work, I started receiving requests for new temple décor, festive decorations, and home décor pieces. Their love and trust encouraged me to experiment with new designs, materials, and techniques.
          </p>
        </article>

        <!-- Chapter 5: Discovering Lippan Art -->
        <article class="story-chapter reveal-on-scroll">
          <div class="chapter-number">05</div>
          <h2 class="chapter-title">Discovering Lippan Art</h2>
          <p class="chapter-paragraph">
            As my journey grew, I wanted to bring more forms of traditional Indian handmade art into my work. That is when <strong>Lippan Art</strong> (the ancient mud and mirror craft of Kutch, Gujarat) became an integral part of my creative journey.
          </p>
          <p class="chapter-paragraph">
            Using rich clay relief dough, hand-cut mirrors, and sacred geometric Mandalas, every panel is sculpted by hand without artificial stencils or mass machine stamping.
          </p>
          <p class="chapter-paragraph">
            Little by little, new ideas, new techniques, and new creations began coming together. And that is how Murli Creations truly came to life.
          </p>
        </article>

        <!-- Chapter 6: The Heart Behind Murli Creations -->
        <article class="story-chapter reveal-on-scroll">
          <div class="chapter-number">06</div>
          <h2 class="chapter-title">The Heart Behind Murli Creations</h2>
          <p class="chapter-paragraph">
            Murli Creations is not simply a business that sells handmade products. It is a journey that began with a daughter's memories of watching her mother create with her hands.
          </p>
          <p class="chapter-paragraph">
            Every piece I create reminds me of where this journey began. It carries a little bit of that childhood inspiration, the patience I learned from handmade art, and the devotion that inspired our name.
          </p>
          <p class="chapter-paragraph">
            At Murli Creations, we want every piece to feel personal—whether it is created for your home, your mandir, a special celebration, a loved one, or a moment of devotion.
          </p>
        </article>

        <!-- Chapter 7: Today & The Journey Ahead -->
        <article class="story-chapter reveal-on-scroll">
          <div class="chapter-number">07</div>
          <h2 class="chapter-title">Today & The Journey Ahead</h2>
          <p class="chapter-paragraph">
            Today, Murli Creations continues to grow through creativity, learning, and the love of the people who appreciate authentic handmade art.
          </p>
          <p class="chapter-paragraph">
            From Shyam Baba decorations and dresses to Lippan Art, temple décor, home décor, and customised creations, every new piece is another step in this journey.
          </p>
          <p class="chapter-paragraph">
            And this is only the beginning. We hope to keep learning, creating, and bringing handmade pieces into more homes, while keeping the same love and personal touch with which this journey began.
          </p>
        </article>

        <!-- Closing Tribute Card -->
        <div class="story-tribute-card reveal-on-scroll">
          <p class="story-tribute-quote">
            “What started with watching my mother create with her hands became my own journey of creating with love.”
          </p>
          <div class="story-tribute-brand">
            <strong>MURLI CREATIONS</strong>
            <span>Handcrafted with Love • Inspired by Tradition • Created to Be Cherished</span>
          </div>
        </div>

        <!-- Values Grid -->
        <div class="story-values-grid">
          <div class="story-val-card reveal-on-scroll stagger-1">
            <span class="val-icon">🌿</span>
            <h3>Handcrafted with Love</h3>
            <p>Every clay line, cone motif, and mirror angle is placed by hand with organic patience and care.</p>
          </div>

          <div class="story-val-card reveal-on-scroll stagger-2">
            <span class="val-icon">🦚</span>
            <h3>Inspired by Tradition</h3>
            <p>Rooted in Khatu Shyam Baba devotion, traditional Kutchi mud & mirror craft, and Vedic sacred geometry.</p>
          </div>

          <div class="story-val-card reveal-on-scroll stagger-3">
            <span class="val-icon">✨</span>
            <h3>Created to Be Cherished</h3>
            <p>Heirloom-quality wall art and temple shringar crafted to bring blessings, warmth, and peace to your family.</p>
          </div>
        </div>

        <!-- Studio CTA -->
        <div class="story-cta-box reveal-on-scroll">
          <h2>Bring Sacred Handmade Art to Your Living Sanctuary</h2>
          <p>Explore our handcrafted Lippan murals, Shyam Baba devotional collections, and custom orders.</p>
          <div class="story-cta-btns">
            <a href="/shop.php" class="btn btn-primary">Browse Art Collections &rarr;</a>
            <a href="/contact.php" class="btn btn-outline">Request Custom Commission</a>
          </div>
        </div>

      </div>
    </section>

  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <?php include __DIR__ . '/includes/cart-drawer.php'; ?>
  <?php include __DIR__ . '/includes/floating-widgets.php'; ?>
  <?php include __DIR__ . '/includes/toast.php'; ?>

  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/main.js"></script>
</body>
</html>
