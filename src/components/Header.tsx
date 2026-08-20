import React, { useState, useEffect } from "react";
import { Link, useLocation } from "react-router-dom";
import { useShop } from "../context/ShopContext";
import styles from "./Header.module.css";

interface HeaderProps {
  onCartOpen: () => void;
}

export default function Header({ onCartOpen }: HeaderProps) {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  
  const pathname = useLocation().pathname;
  const { cart } = useShop();

  const cartItemCount = cart.reduce((sum, item) => sum + item.quantity, 0);

  // Monitor scroll level
  useEffect(() => {
    const handleScroll = () => {
      if (window.scrollY > 20) {
        setIsScrolled(true);
      } else {
        setIsScrolled(false);
      }
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  useEffect(() => {
    setIsMobileMenuOpen(false);
  }, [pathname]);

  const navLinks = [
    { name: "Home", path: "/" },
    { name: "Shop", path: "/shop" },
    { name: "Founder's Story", path: "/founder-story" },
    { name: "Our Craft", path: "/our-craft" },
    { name: "Journal", path: "/instagram" },
    { name: "Contact", path: "/contact" },
    { name: "Portal", path: "/admin", isSpecial: true }
  ];

  return (
    <>
      {/* Top Promotional Offer Banner */}
      <div className={styles.promoBanner}>
        ✨ Festive Offer: Use code <strong>MURLI10</strong> for 10% OFF + Free Shipping above ₹3,000! ✨
      </div>

      <header className={`${styles.header} ${isScrolled ? styles.scrolled : ""}`}>
        <div className={styles.container}>
        {/* Mobile Toggle Hamburger */}
        <button
          onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
          className={styles.menuToggle}
          aria-expanded={isMobileMenuOpen}
          aria-label="Toggle menu"
        >
          <span className={`${styles.hamburger} ${isMobileMenuOpen ? styles.open : ""}`}></span>
        </button>

        {/* Wordmark Logo Left */}
        <Link to="/" className={styles.logoContainer}>
          <img
            src="/images/brand/black_logo.png"
            alt="Murli Creations Logo"
            width={120}
            height={69}
            className={styles.logoImage}
          />
        </Link>

        {/* Centered Desktop Nav Links */}
        <nav className={styles.navDesktop}>
          {navLinks.map((link) => (
            <Link
              key={link.path}
              to={link.path}
              className={`${styles.navLink} ${pathname === link.path ? styles.active : ""} ${
                link.isSpecial ? styles.specialLink : ""
              }`}
            >
              {link.name}
            </Link>
          ))}
        </nav>

        {/* Right Actions */}
        <div className={styles.actions}>
          <div className={styles.searchWrapper}>
            <button
              onClick={() => setSearchOpen(!searchOpen)}
              className={styles.actionBtn}
              aria-label="Search wall art"
            >
              🔍
            </button>
            {searchOpen && (
              <form action="/shop" method="GET" className={styles.searchForm}>
                <input
                  type="text"
                  name="search"
                  placeholder="Search catalog..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className={styles.searchInput}
                  autoFocus
                />
                <button type="submit" className={styles.searchSubmit}>
                  Go
                </button>
              </form>
            )}
          </div>

          <button onClick={onCartOpen} className={styles.cartTrigger} aria-label="Open cart">
            <span className={styles.cartIcon}>🛒</span>
            {cartItemCount > 0 && (
              <span className={styles.cartBadge}>{cartItemCount}</span>
            )}
          </button>
        </div>
      </div>
    </header>

    {/* Mobile Menu Panel (Moved outside of <header> to prevent transform: translateX positioning bug) */}
    <div className={`${styles.mobileMenu} ${isMobileMenuOpen ? styles.mobileMenuOpen : ""}`}>
      <div className={styles.mobileMenuHeader}>
        <Link to="/" onClick={() => setIsMobileMenuOpen(false)} className={styles.logoContainer}>
          <img
            src="/images/brand/black_logo.png"
            alt="Murli Creations Logo"
            width={100}
            height={58}
            className={styles.logoImageMobile}
          />
        </Link>
        <button
          onClick={() => setIsMobileMenuOpen(false)}
          className={styles.closeMenuBtn}
          aria-label="Close menu"
        >
          &times;
        </button>
      </div>
      <nav className={styles.navMobile}>
        {navLinks.map((link) => (
          <Link
            key={link.path}
            to={link.path}
            onClick={() => setIsMobileMenuOpen(false)}
            className={`${styles.mobileNavLink} ${pathname === link.path ? styles.mobileActive : ""} ${
              link.isSpecial ? styles.mobileSpecialLink : ""
            }`}
          >
            {link.name}
          </Link>
        ))}
      </nav>
      <div className={styles.mobileMenuFooter}>
        <p className={styles.studioInfo}>Studio: Rohtak, Haryana</p>
        <div className={styles.instaLink}>
          <a href="https://www.instagram.com/murlicreationsofficial" target="_blank" rel="noopener noreferrer">
            Follow @murlicreationsofficial
          </a>
        </div>
      </div>
    </div>

    {/* Backdrop (Moved outside of <header> to prevent transform: translateX positioning bug) */}
    {isMobileMenuOpen && (
      <div className={styles.menuBackdrop} onClick={() => setIsMobileMenuOpen(false)} />
    )}
  </>
);
}
