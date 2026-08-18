import React from "react";
import Link from "next/link";

export default function NotFound() {
  return (
    <div
      style={{
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
        justifyContent: "center",
        minHeight: "70vh",
        textAlign: "center",
        padding: "var(--spacing-jumbo) var(--spacing-lg)",
        backgroundColor: "var(--bg-primary)",
        color: "var(--text-primary)"
      }}
    >
      <div
        style={{
          fontSize: "6rem",
          fontWeight: "700",
          fontFamily: "var(--font-headings)",
          color: "var(--accent-gold-hover)",
          lineHeight: "1"
        }}
      >
        404
      </div>
      
      <span
        style={{
          fontSize: "0.8rem",
          fontWeight: "700",
          textTransform: "uppercase",
          color: "var(--accent-gold)",
          letterSpacing: "0.2em",
          marginBottom: "var(--spacing-md)"
        }}
      >
        Piece Not Found
      </span>

      <h2
        style={{
          fontFamily: "var(--font-headings)",
          fontSize: "2rem",
          marginBottom: "var(--spacing-sm)",
          fontWeight: "600"
        }}
      >
        Page Lost in the Studio
      </h2>

      <p
        style={{
          color: "var(--text-muted)",
          maxWidth: "460px",
          lineHeight: "1.6",
          fontSize: "0.95rem",
          marginBottom: "var(--spacing-xl)"
        }}
      >
        The page you are looking for might have been renamed, removed, or is temporarily unavailable while we sketch new patterns.
      </p>

      <Link
        href="/"
        style={{
          backgroundColor: "var(--accent-blue)",
          color: "var(--bg-secondary)",
          padding: "var(--spacing-md) var(--spacing-xl)",
          borderRadius: "var(--radius-full)",
          fontWeight: "700",
          fontSize: "0.95rem",
          boxShadow: "var(--shadow-sm)",
          display: "inline-block"
        }}
      >
        Return to Home Gallery
      </Link>
    </div>
  );
}
