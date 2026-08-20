import React, { useState, useEffect } from "react";
import styles from "./FloatingWidgets.module.css";

export default function FloatingWidgets() {
  const [showScrollTop, setShowScrollTop] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      if (window.scrollY > 300) {
        setShowScrollTop(true);
      } else {
        setShowScrollTop(false);
      }
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  };

  return (
    <div className={styles.widgetsContainer}>
      {/* WhatsApp Button (Bottom-Left) */}
      <a
        href="https://wa.me/919876543210?text=Hello%20Murli%20Creations,%20I'd%20like%20to%20inquire%20about%20your%20handcrafted%20Lippan%20Art."
        target="_blank"
        rel="noopener noreferrer"
        className={styles.whatsappBtn}
        aria-label="Chat on WhatsApp"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          className={styles.icon}
        >
          <path d="M12.012 2c-5.506 0-9.988 4.482-9.988 9.988 0 1.761.462 3.486 1.34 5.011l-1.364 4.978 5.093-1.336c1.477.805 3.137 1.229 4.832 1.23h.004c5.505 0 10.03-4.482 10.03-9.988 0-2.668-1.039-5.176-2.929-7.062-1.89-1.887-4.436-2.921-7.118-2.921zm5.95 14.167c-.244.686-1.42 1.261-1.95 1.328-.529.068-1.018.283-3.398-.655-2.871-1.132-4.66-4.088-4.802-4.279-.143-.191-1.157-1.537-1.157-2.93 0-1.393.729-2.074 1.018-2.378.288-.304.629-.381.839-.381.21 0 .42.001.602.009.189.009.444-.072.698.541.258.625.879 2.146.953 2.298.076.152.128.329.025.534-.102.205-.152.329-.304.506-.153.178-.321.397-.457.534-.153.153-.312.32-.136.621.177.301.785 1.295 1.684 2.097.161.144.316.27.47.378.854.774 1.488.75 1.833.397.247-.253 1.05-1.221 1.33-1.637.28-.417.561-.347.946-.205.385.143 2.434 1.149 2.854 1.359.42.21.699.312.802.485.103.173.103 1.001-.141 1.687z" />
        </svg>
        <span className={styles.pulse}></span>
      </a>

      {/* Go to Top Button (Bottom-Right) */}
      <button
        onClick={scrollToTop}
        className={`${styles.scrollTopBtn} ${showScrollTop ? styles.showScrollTop : ""}`}
        aria-label="Scroll to top"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          className={styles.icon}
        >
          <path d="M4 12l1.41 1.41L11 7.83V20h2V7.83l5.58 5.59L20 12l-8-8-8 8z" />
        </svg>
      </button>
    </div>
  );
}
