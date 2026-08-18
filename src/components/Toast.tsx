"use client";

import React from "react";
import { useShop } from "../context/ShopContext";
import styles from "./Toast.module.css";

export default function Toast() {
  const { toasts, removeToast } = useShop();

  if (toasts.length === 0) return null;

  return (
    <div className={styles.toastContainer}>
      {toasts.map((toast) => (
        <div
          key={toast.id}
          className={`${styles.toast} ${styles[toast.type]}`}
          role="alert"
        >
          <span className={styles.text}>{toast.text}</span>
          <button
            onClick={() => removeToast(toast.id)}
            className={styles.closeButton}
            aria-label="Close notification"
          >
            &times;
          </button>
        </div>
      ))}
    </div>
  );
}
