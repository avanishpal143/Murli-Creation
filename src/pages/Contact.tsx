import React, { useState } from "react";
import { useShop } from "../context/ShopContext";
import styles from "./Contact.module.css";

export default function ContactPage() {
  const { showToast } = useShop();

  // Form states
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [subject, setSubject] = useState("Custom Lippan Art Request");
  const [message, setMessage] = useState("");
  
  // Accordion state
  const [openFAQ, setOpenFAQ] = useState<string | null>("shipping");

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name || !email || !message) {
      showToast("Please fill in all required fields.", "error");
      return;
    }

    // Trigger Toast on success
    showToast(`Thank you, ${name}! Your inquiry has been sent to Murli Creations.`, "success");
    
    // Reset Form fields
    setName("");
    setEmail("");
    setMessage("");
  };

  const toggleFAQ = (id: string) => {
    setOpenFAQ(openFAQ === id ? null : id);
  };

  return (
    <div className={styles.contactPage}>
      <div className="container">
        <h2 className={styles.title}>Contact Our Studio</h2>

        <div className={styles.layout}>
          {/* Column 1: Contact Form */}
          <div className={styles.formSection}>
            <h3 className={styles.sectionTitle}>Send a Message</h3>
            <form onSubmit={handleSubmit} className={styles.form}>
              <div className={styles.inputGroup}>
                <label className={styles.label}>Your Name *</label>
                <input
                  type="text"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  className={styles.input}
                  required
                />
              </div>

              <div className={styles.inputGroup}>
                <label className={styles.label}>Your Email *</label>
                <input
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className={styles.input}
                  required
                />
              </div>

              <div className={styles.inputGroup}>
                <label className={styles.label}>Topic / Subject</label>
                <select
                  value={subject}
                  onChange={(e) => setSubject(e.target.value)}
                  className={styles.input}
                >
                  <option value="Custom Lippan Art Request">Custom Lippan Art Commission</option>
                  <option value="Order Tracking">Order & Shipping Tracking</option>
                  <option value="Wholesale / Gifting">Wholesale or Corporate Gifting</option>
                  <option value="General Query">General Query</option>
                </select>
              </div>

              <div className={styles.inputGroup}>
                <label className={styles.label}>Message / Details *</label>
                <textarea
                  value={message}
                  onChange={(e) => setMessage(e.target.value)}
                  placeholder="Tell us about your wall dimensions or customization colors..."
                  className={styles.textarea}
                  required
                />
              </div>

              <button type="submit" className={styles.submitBtn}>
                Send Message
              </button>
            </form>
          </div>

          {/* Column 2: Info & FAQ Accordion */}
          <div className={styles.infoSection}>
            {/* Address Card */}
            <div className={styles.addressCard}>
              <h3 className={styles.sectionTitle}>Studio Location</h3>
              <div className={styles.addressDetails}>
                <p><strong>Workshop:</strong> Murli Creations Studio, Budh Bazar Road, Rohtak, Haryana, India</p>
                <p><strong>Pincode:</strong> 124001</p>
                <p><strong>Inquiries:</strong> +91 98765 43210 (Mon-Sat, 10 AM to 6 PM)</p>
                <p><strong>General Support:</strong> support@murlicreations.com</p>
              </div>
              
              {/* Map Placeholder */}
              <div className={styles.mapPlaceholder}>
                📍 Google Maps: Rohtak, Haryana Workshop
              </div>
            </div>

            {/* FAQ Sections */}
            <div className={styles.faqSection}>
              <h3 className={styles.faqTitle}>Frequently Asked Questions</h3>
              
              <div className={styles.accordions}>
                {/* FAQ 1: Shipping */}
                <div className={`${styles.accordion} ${openFAQ === "shipping" ? styles.accordionOpen : ""}`}>
                  <button onClick={() => toggleFAQ("shipping")} className={styles.accordionHeader}>
                    <span>Are clay panels safe from damage during transit?</span>
                    <span className={styles.accordionIcon}>+</span>
                  </button>
                  {openFAQ === "shipping" && (
                    <div className={styles.accordionContent}>
                      Yes, absolutely. Lippan art is delicate, so we pack each piece inside double-thickness heavy bubble sheets, wrapped with cardboard corner guards, and enclosed in a custom 5-ply corrugated shipping crate. In the rare event of transit damage (e.g. glass breaks), please email us photos of the unboxed item within 24 hours of delivery, and we will ship a replacement panel at no charge.
                    </div>
                  )}
                </div>

                {/* FAQ 2: Returns */}
                <div className={`${styles.accordion} ${openFAQ === "returns" ? styles.accordionOpen : ""}`}>
                  <button onClick={() => toggleFAQ("returns")} className={styles.accordionHeader}>
                    <span>What is your return policy?</span>
                    <span className={styles.accordionIcon}>+</span>
                  </button>
                  {openFAQ === "returns" && (
                    <div className={styles.accordionContent}>
                      Since each decor plate and clay mural is individually handcrafted to order, we do not support general returns or exchanges if you change your mind. However, we offer full replacements or refunds for transit damages or defective items. Custom orders (personalized colors/dimensions) cannot be returned or cancelled once sculpting has commenced.
                    </div>
                  )}
                </div>

                {/* FAQ 3: Art Care */}
                <div className={`${styles.accordion} ${openFAQ === "care" ? styles.accordionOpen : ""}`}>
                  <button onClick={() => toggleFAQ("care")} className={styles.accordionHeader}>
                    <span>How do I clean and maintain Lippan art mirrors?</span>
                    <span className={styles.accordionIcon}>+</span>
                  </button>
                  {openFAQ === "care" && (
                    <div className={styles.accordionContent}>
                      Lippan clay art should only be dusted with a dry, clean micro-fiber cloth or a soft painting brush. Do not use wet rags, water sprays, or damp sponges, as moisture can soften the clay plaster structure. The glass mirrors can be gently wiped using a dry cotton swab. Hang the panels in dry, well-ventilated indoor walls, away from bathrooms, outdoor rain, or constant moisture.
                    </div>
                  )}
                </div>

                {/* FAQ 4: Customizations */}
                <div className={`${styles.accordion} ${openFAQ === "custom" ? styles.accordionOpen : ""}`}>
                  <button onClick={() => toggleFAQ("custom")} className={styles.accordionHeader}>
                    <span>How do custom commission orders work?</span>
                    <span className={styles.accordionIcon}>+</span>
                  </button>
                  {openFAQ === "custom" && (
                    <div className={styles.accordionContent}>
                      To commission a custom size, backdrop shade, or layout motif: submit your request via this contact form or message us on Instagram @murlicreationsofficial. We will share a price quotation and draft sketches. Once you approve the sketch and complete the partial booking advance, our artists will begin sculpting. Custom pieces require 10-15 days to sculpt, dry, paint, and varnish before shipping.
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
