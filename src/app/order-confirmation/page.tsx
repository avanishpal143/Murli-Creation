"use client";

import React, { use, Suspense } from "react";
import { useSearchParams } from "next/navigation";
import Link from "next/link";
import { useShop } from "../../context/ShopContext";
import styles from "./page.module.css";

function ConfirmationContent() {
  const searchParams = useSearchParams();
  const { orders } = useShop();

  const orderId = searchParams.get("orderId");
  const order = orders.find((o) => o.id === orderId);

  if (!order) {
    return (
      <div className={styles.confirmationPage}>
        <div className={`container ${styles.card}`}>
          <div className={styles.successIcon} style={{ borderColor: "var(--accent-terracotta)", color: "var(--accent-terracotta)", backgroundColor: "var(--accent-terracotta-light)" }}>
            ⚠️
          </div>
          <h2 className={styles.title}>Invoice Lookup</h2>
          <p className={styles.text}>
            We could not instantly pull up details for order ID <strong>{orderId || "unknown"}</strong>. 
            If you just placed an order, please check your email for receipt confirmations.
          </p>
          <Link href="/shop" className={styles.continueBtn}>
            Return to Store
          </Link>
        </div>
      </div>
    );
  }

  // Calculate estimated delivery: standard 6 days from today
  const deliveryDate = new Date();
  deliveryDate.setDate(deliveryDate.getDate() + 6);
  const formattedDelivery = deliveryDate.toLocaleDateString("en-IN", {
    weekday: "long",
    day: "numeric",
    month: "long"
  });

  return (
    <div className={styles.confirmationPage}>
      <div className={`container ${styles.card}`}>
        <div className={styles.successIcon}>✓</div>
        
        <h2 className={styles.title}>Order Placed!</h2>
        <p className={styles.text}>
          Thank you for supporting authentic local artisans. Your order has been registered at our workshop and we are preparing your pieces.
        </p>

        <div className={styles.orderNumBox}>
          ORDER ID: {order.id}
        </div>

        {/* Invoice Grid Details */}
        <div className={styles.detailsGrid}>
          <div className={styles.detailCol}>
            <span className={styles.detailTitle}>Shipping Destination</span>
            <span className={styles.detailVal}>{order.customer.name}</span>
            <span className={styles.detailVal}>
              {order.customer.address}, {order.customer.city}, {order.customer.state} - {order.customer.pinCode}
            </span>
          </div>

          <div className={styles.detailCol}>
            <span className={styles.detailTitle}>Billing Information</span>
            <span className={styles.detailVal}><strong>Phone:</strong> {order.customer.phone}</span>
            <span className={styles.detailVal}><strong>Email:</strong> {order.customer.email}</span>
            <span className={styles.detailVal}>
              <strong>Payment:</strong> {order.paymentMethod} ({order.paymentStatus})
            </span>
            <span className={styles.detailVal}>
              <strong>Amount Paid:</strong> ₹{order.subtotal.toLocaleString("en-IN")}
            </span>
          </div>
        </div>

        {/* Shipping Schedule timeline */}
        <div className={styles.timeline}>
          <span className={styles.timelineTitle}>📅 Estimated Arrival</span>
          <p>
            Your handcrafted murals are estimated to arrive by <strong>{formattedDelivery}</strong>. 
            A dispatch tracking link will be sent to <strong>{order.customer.email}</strong> as soon as the package leaves our Rohtak workshop.
          </p>
        </div>

        <Link href="/shop" className={styles.continueBtn}>
          Continue Shopping
        </Link>
      </div>
    </div>
  );
}

export default function OrderConfirmation() {
  return (
    <Suspense
      fallback={
        <div className="container" style={{ padding: "100px 0", textAlign: "center" }}>
          <div className="spinner"></div>
          <p style={{ marginTop: "10px", color: "var(--text-muted)" }}>Loading order details...</p>
        </div>
      }
    >
      <ConfirmationContent />
    </Suspense>
  );
}
