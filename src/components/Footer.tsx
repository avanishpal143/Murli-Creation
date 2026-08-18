"use client";

import React, { useState } from "react";
import Link from "next/link";
import { useShop } from "../context/ShopContext";
import styles from "./Footer.module.css";

export default function Footer() {
  const { showToast } = useShop();
  const [email, setEmail] = useState("");

  const handleSubscribe = (e: React.FormEvent) => {
    e.preventDefault();
    if (!email) return;
    showToast(`Subscribed successfully with ${email}! Welcome to our art journal.`, "success");
    setEmail("");
  };

  return (
    <footer className={styles.footer}>
      <div className={styles.container}>
        {/* Brand Column */}
        <div className={styles.columnBrand}>
          <Link href="/" className={styles.logoContainer}>
            <h2 className={styles.wordmark}>MURLI</h2>
            <span className={styles.tagline}>Creations</span>
          </Link>
          <p className={styles.blurb}>
            Handcrafting premium Lippan art murals, vintage wooden Jharokhas, and hand-painted spiritual mandala plates. Bring authentic Indian heritage into modern living spaces.
          </p>
          <div className={styles.socials}>
            <a
              href="https://www.instagram.com/murlicreationsofficial"
              target="_blank"
              rel="noopener noreferrer"
              className={styles.socialLink}
              aria-label="Instagram Profile"
            >
              Instagram @murlicreationsofficial
            </a>
          </div>
        </div>

        {/* Sitemap Links Column */}
        <div className={styles.columnLinks}>
          <h3 className={styles.title}>Shop Collections</h3>
          <ul className={styles.linkList}>
            <li><Link href="/shop?category=Lippan+Art">Lippan Clay Murals</Link></li>
            <li><Link href="/shop?category=Jharokhas+%26+Frames">Wooden Jharokha Mirrors</Link></li>
            <li><Link href="/shop?category=Wall+Plates+%26+Mandalas">Handpainted Wall Plates</Link></li>
            <li><Link href="/shop">All Handcrafts</Link></li>
          </ul>
        </div>

        {/* Studio Info Column */}
        <div className={styles.columnLinks}>
          <h3 className={styles.title}>Our Studio</h3>
          <ul className={styles.linkList}>
            <li><Link href="/our-craft">Artisan Process</Link></li>
            <li><Link href="/contact">FAQs & Care Info</Link></li>
            <li><Link href="/contact">Contact & Map</Link></li>
            <li><Link href="/admin" className={styles.adminFooterLink}>Staff Portal (Admin)</Link></li>
          </ul>
        </div>

        {/* Contact Details & Newsletter Column */}
        <div className={styles.columnContact}>
          <h3 className={styles.title}>Join Our Journal</h3>
          <p className={styles.newsletterText}>
            Subscribe for sneak peeks of new collections, artisan interviews, and home styling tips.
          </p>
          <form onSubmit={handleSubscribe} className={styles.newsletterForm}>
            <input
              type="email"
              placeholder="Your email address"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              className={styles.newsletterInput}
              aria-label="Email address for newsletter"
            />
            <button type="submit" className={styles.subscribeBtn}>
              Subscribe
            </button>
          </form>

          <div className={styles.contactDetails}>
            <p><strong>Studio:</strong> Rohtak, Haryana, India - 124001</p>
            <p><strong>Email:</strong> care@murlicreations.com</p>
            <p><strong>Call:</strong> +91 98765 43210</p>
          </div>
        </div>
      </div>

      {/* Footer Bottom bar */}
      <div className={styles.bottomBar}>
        <div className={styles.bottomContainer}>
          <p className={styles.copyright}>
            &copy; {new Date().getFullYear()} Murli Creations. All Rights Reserved. Handcrafted in Haryana, India.
          </p>
          <div className={styles.payments}>
            <span className={styles.paymentBadge}>UPI (Paytm/GPay)</span>
            <span className={styles.paymentBadge}>Debit & Credit Cards</span>
            <span className={styles.paymentBadge}>Net Banking</span>
            <span className={styles.paymentBadge}>Cash on Delivery</span>
          </div>
        </div>
      </div>
    </footer>
  );
}
