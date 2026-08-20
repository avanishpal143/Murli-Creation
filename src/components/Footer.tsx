import React from "react";
import { Link } from "react-router-dom";
import styles from "./Footer.module.css";
import Interactive3DTilt from "./Interactive3DTilt";

export default function Footer() {
  return (
    <footer className={styles.footer}>
      <div className={styles.container}>
        {/* Column 1: Brand details wrapped in 3D tilt */}
        <Interactive3DTilt maxTilt={8} scale={1.02} className={styles.tiltWrapper}>
          <div className={styles.columnBrand}>
            <Link to="/" className={styles.logoContainer} style={{ display: 'block', marginBottom: '12px' }}>
              <img
                src="/images/brand/white_logo.png"
                alt="Murli Creations Logo"
                width={110}
                height={59}
                style={{ objectFit: "contain" }}
              />
            </Link>
            <p className={styles.blurb}>
              Handcrafting premium Lippan art murals, vintage wooden Jharokhas, and hand-painted spiritual mandala plates. Bring authentic Indian heritage into modern living spaces.
            </p>
          </div>
        </Interactive3DTilt>

        {/* Column 2: Address and Contact details */}
        <div className={styles.columnLinks}>
          <h3 className={styles.title}>Address</h3>
          <div className={styles.addressList}>
            <p className={styles.addressItem}>
              <strong>📍 Workshop Studio:</strong><br />
              Budh Bazar Road, Rohtak, Haryana, India - 124001
            </p>
            <p className={styles.addressItem}>
              <strong>✉️ Email:</strong><br />
              care@murlicreations.com
            </p>
            <p className={styles.addressItem}>
              <strong>📞 Call:</strong><br />
              +91 98765 43210
            </p>
          </div>
        </div>

        {/* Column 3: Shop Collections */}
        <div className={styles.columnLinks}>
          <h3 className={styles.title}>Collections</h3>
          <ul className={styles.linkList}>
            <li><Link to="/shop?category=Lippan+Art">Lippan Clay Murals</Link></li>
            <li><Link to="/shop?category=Jharokhas+%26+Frames">Wooden Jharokha Mirrors</Link></li>
            <li><Link to="/shop?category=Wall+Plates+%26+Mandalas">Handpainted Wall Plates</Link></li>
            <li><Link to="/shop">All Handcrafts</Link></li>
          </ul>
        </div>

        {/* Column 4: Our Studio */}
        <div className={styles.columnLinks}>
          <h3 className={styles.title}>Our Studio</h3>
          <ul className={styles.linkList}>
            <li><Link to="/founder-story">Founder's Story</Link></li>
            <li><Link to="/our-craft">Artisan Process</Link></li>
            <li><Link to="/contact">Contact & Map</Link></li>
          </ul>
        </div>

        {/* Column 5: Support / Policies */}
        <div className={styles.columnLinks}>
          <h3 className={styles.title}>Support</h3>
          <ul className={styles.linkList}>
            <li><Link to="/contact">FAQs & Care Info</Link></li>
            <li><Link to="/contact">Return & Refund</Link></li>
            <li><Link to="/contact">Shipping Policy</Link></li>
            <li><Link to="/contact">Terms of Service</Link></li>
          </ul>
        </div>
      </div>

      {/* Footer Bottom bar */}
      <div className={styles.bottomBar}>
        <div className={styles.bottomContainer}>
          <p className={styles.copyright}>
            &copy; {new Date().getFullYear()} Murli Creations. All Rights Reserved. Handcrafted in Haryana, India.
          </p>
          <div className={styles.socials}>
            <a
              href="https://www.instagram.com/murlicreationsofficial"
              target="_blank"
              rel="noopener noreferrer"
              className={styles.socialLink}
              aria-label="Instagram Profile"
            >
              Instagram
            </a>
            <span className={styles.socialSeparator}>|</span>
            <a
              href="https://wa.me/919876543210"
              target="_blank"
              rel="noopener noreferrer"
              className={styles.socialLink}
              aria-label="WhatsApp Studio"
            >
              WhatsApp Support
            </a>
          </div>
        </div>
      </div>
    </footer>
  );
}
