"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
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
  
  const pathname = usePathname();
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
    { name: "Our Craft", path: "/our-craft" },
    { name: "Journal", path: "/instagram" },
    { name: "Contact", path: "/contact" },
    { name: "Portal", path: "/admin", isSpecial: true }
  ];

  return (
    <header className={`${styles.header} ${isScrolled ? styles.scrolled : ""}`}>
      {/* Top Promotional Offer Banner */}
      <div className={styles.promoBanner}>
        ✨ Festive Offer: Use code <strong>MURLI10</strong> for 10% OFF + Free Shipping above ₹3,000! ✨
      </div>

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
        <Link href="/" className={styles.logoContainer}>
          <h1 className={styles.wordmark}>MURLI</h1>
          <span className={styles.tagline}>Creations</span>
        </Link>

        {/* Centered Desktop Nav Links */}
        <nav className={styles.navDesktop}>
          {navLinks.map((link) => (
            <Link
              key={link.path}
              href={link.path}
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

      {/* Mobile Menu Panel */}
      <div className={`${styles.mobileMenu} ${isMobileMenuOpen ? styles.mobileMenuOpen : ""}`}>
        <div className={styles.mobileMenuHeader}>
          <Link href="/" onClick={() => setIsMobileMenuOpen(false)} className={styles.logoContainer}>
            <h2 className={styles.wordmark}>MURLI</h2>
            <span className={styles.tagline}>Creations</span>
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
              href={link.path}
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

      {/* Backdrop */}
      {isMobileMenuOpen && (
        <div className={styles.menuBackdrop} onClick={() => setIsMobileMenuOpen(false)} />
      )}
    </header>
  );
}
