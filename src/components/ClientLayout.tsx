import React, { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";
import { ShopProvider } from "../context/ShopContext";
import Header from "./Header";
import Footer from "./Footer";
import CartDrawer from "./CartDrawer";
import Toast from "./Toast";
import FloatingWidgets from "./FloatingWidgets";

interface ClientLayoutProps {
  children: React.ReactNode;
}

export default function ClientLayout({ children }: ClientLayoutProps) {
  const [isCartOpen, setIsCartOpen] = useState(false);
  const pathname = useLocation().pathname;

  // Scroll reveal Intersection Observer
  useEffect(() => {
    const revealElements = document.querySelectorAll(".reveal-on-scroll");

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("active");
            // Unobserve after showing to avoid duplicate triggers
            observer.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.05,
        rootMargin: "0px 0px -40px 0px" // Trigger slightly early
      }
    );

    revealElements.forEach((el) => observer.observe(el));

    return () => {
      revealElements.forEach((el) => observer.unobserve(el));
    };
  }, [pathname]);

  return (
    <ShopProvider>
      <div style={{ display: "flex", flexDirection: "column", minHeight: "100vh" }}>
        <Header onCartOpen={() => setIsCartOpen(true)} />
        
        <main style={{ flexGrow: 1, paddingTop: "128px" }}>{children}</main>
        
        <Footer />
        <CartDrawer isOpen={isCartOpen} onClose={() => setIsCartOpen(false)} />
        <FloatingWidgets />
        <Toast />
      </div>
    </ShopProvider>
  );
}
