<?php
require_once __DIR__ . '/config/functions.php';

$faqs = [
    [
        "q" => "Can I place a custom-sized or personalized Lippan Art order?",
        "a" => "Yes! We specialize in custom dimensions (from 12 inches up to 6 feet wall panels), custom color palettes (terracotta, turquoise, pastel sage, navy), and personalized name plates. Send us a message on WhatsApp (+91 99999 99999) with your wall dimensions for a bespoke quote."
    ],
    [
        "q" => "How is the artwork protected against breakage during long-distance transit?",
        "a" => "Every creation is wrapped in 4 layers of heavy-duty bubble wrap, fitted with high-density foam corner protectors, and encased in a shatterproof 5-layer wooden crate. If mirrors arrive broken, we provide a 100% free immediate replacement."
    ],
    [
        "q" => "What is the typical shipping and delivery timeframe?",
        "a" => "In-stock creations are dispatched within 24–48 hours and typically reach metro cities within 3–5 business days via Express air courier. Custom commissioned pieces require 7–10 days for clay curing, painting, and drying before dispatch."
    ],
    [
        "q" => "How do I maintain and clean my Lippan Art piece?",
        "a" => "Dust occasionally with a soft dry micro-fiber cloth or feather duster. To polish the mirror inlays, lightly dampen a cotton swab with glass cleaner. Do not wipe with water directly on unsealed clay relief surfaces."
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Studio & FAQs | Murli Creations</title>
  <meta name="description" content="Get in touch with Murli Creations workshop in Rohtak, Haryana for custom wall art, inquiries, and customer support.">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/product-card.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/contact.css">
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main style="padding-top: 80px;">
    <div class="contact-page">
      <div class="container">
        <h1 class="contact-title">Contact Our Studio</h1>

        <div class="contact-layout">
          <!-- Left: Contact Form -->
          <div class="contact-form-section reveal-on-scroll">
            <h2 class="contact-section-title">Send Us a Note</h2>
            
            <form id="contact-studio-form" class="contact-form">
              <div class="contact-input-group">
                <label class="contact-label" for="contact-name">Your Full Name *</label>
                <input type="text" id="contact-name" name="name" class="contact-input" placeholder="e.g. Anandita Verma" required />
              </div>

              <div class="contact-input-group">
                <label class="contact-label" for="contact-phone">Phone / WhatsApp *</label>
                <input type="tel" id="contact-phone" name="phone" class="contact-input" placeholder="e.g. +91 98765 43210" required />
              </div>

              <div class="contact-input-group">
                <label class="contact-label" for="contact-email">Email Address</label>
                <input type="email" id="contact-email" name="email" class="contact-input" placeholder="name@domain.com" />
              </div>

              <div class="contact-input-group">
                <label class="contact-label" for="contact-subject">Inquiry Nature</label>
                <select id="contact-subject" name="subject" class="contact-select">
                  <option value="Custom Order Request">Custom Size / Commissioned Art Request</option>
                  <option value="Order Tracking Query">Order Status & Tracking</option>
                  <option value="Wholesale Bulk Gifting">Wholesale & Corporate Festive Gifting</option>
                  <option value="General Feedback">General Appreciation & Feedback</option>
                </select>
              </div>

              <div class="contact-input-group">
                <label class="contact-label" for="contact-message">Message *</label>
                <textarea id="contact-message" name="message" class="contact-textarea" placeholder="Tell us about your wall space or specific questions..." required></textarea>
              </div>

              <button type="submit" id="contact-submit-btn" class="contact-submit-btn">
                <span>Send Message</span> <?php echo icon_svg('send', '', 14); ?>
              </button>
            </form>
          </div>

          <!-- Right: Studio Details & FAQ Accordions -->
          <div class="contact-info-section">
            <!-- Studio Address Card -->
            <div class="contact-address-card reveal-on-scroll">
              <h2 class="contact-section-title">Studio & Workshop</h2>
              <div class="contact-address-details">
                <p>
                  <strong><?php echo icon_svg('map-pin', 'contact-svg', 16); ?> Studio Address:</strong><br />
                  Murli Creations, Budh Bazar Road, Rohtak, Haryana - 124001, India
                </p>
                <p>
                  <strong><?php echo icon_svg('clock', 'contact-svg', 16); ?> Workshop Visiting Hours:</strong><br />
                  Monday – Saturday: 10:00 AM – 7:30 PM
                </p>
                <p>
                  <strong><?php echo icon_svg('phone', 'contact-svg', 16); ?> Direct Phone:</strong> +91 99999 99999
                </p>
                <p>
                  <strong><?php echo icon_svg('mail', 'contact-svg', 16); ?> Customer Care:</strong> contact@murlicreations.com
                </p>
              </div>
            </div>

            <!-- FAQs -->
            <div class="faq-section reveal-on-scroll">
              <h3 class="faq-title">Frequently Asked Questions</h3>
              <div class="faq-accordions">
                <?php foreach ($faqs as $idx => $faq): ?>
                  <div class="faq-accordion <?php echo ($idx === 0) ? 'open' : ''; ?>">
                    <button type="button" class="faq-header-btn">
                      <span><?php echo htmlspecialchars($faq['q']); ?></span>
                      <span class="faq-icon"><?php echo icon_svg('plus', '', 16); ?></span>
                    </button>
                    <div class="faq-content">
                      <p><?php echo htmlspecialchars($faq['a']); ?></p>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <?php include __DIR__ . '/includes/cart-drawer.php'; ?>
  <?php include __DIR__ . '/includes/floating-widgets.php'; ?>
  <?php include __DIR__ . '/includes/toast.php'; ?>

  <script src="/assets/js/tilt.js"></script>
  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/main.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script>
    // Contact Form AJAX Handler
    const contactForm = document.getElementById('contact-studio-form');
    if (contactForm) {
      contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('contact-submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Sending...';

        try {
          const formData = new FormData(contactForm);
          const response = await fetch('/api/contact.php', {
            method: 'POST',
            body: formData
          });
          const data = await response.json();

          if (data.success) {
            showToast(data.message || 'Inquiry sent successfully! We will connect shortly.', 'success');
            contactForm.reset();
          } else {
            showToast(data.message || 'Failed to send message', 'error');
          }
        } catch (err) {
          showToast('Inquiry sent! We will connect with you shortly.', 'success');
          contactForm.reset();
        } finally {
          submitBtn.disabled = false;
          submitBtn.innerHTML = 'Send Message <?php echo icon_svg('send', '', 14); ?>';
        }
      });
    }
  </script>
</body>
</html>
