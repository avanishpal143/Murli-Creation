"use client";

import React, { useEffect } from "react";
import Link from "next/link";
import Image from "next/image";
import { useShop } from "../context/ShopContext";
import styles from "./CartDrawer.module.css";

interface CartDrawerProps {
  isOpen: boolean;
  onClose: () => void;
}

export default function CartDrawer({ isOpen, onClose }: CartDrawerProps) {
  const { cart, removeFromCart, updateCartQuantity } = useShop();

  // Prevent background scrolling when cart drawer is open
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
    return () => {
      document.body.style.overflow = "";
    };
  }, [isOpen]);

  const subtotal = cart.reduce((sum, item) => sum + item.product.price * item.quantity, 0);

  return (
    <>
      {/* Backdrop overlay */}
      <div
        className={`${styles.backdrop} ${isOpen ? styles.backdropOpen : ""}`}
        onClick={onClose}
      />

      {/* Cart drawer panel */}
      <div className={`${styles.drawer} ${isOpen ? styles.drawerOpen : ""}`}>
        <div className={styles.header}>
          <h2 className={styles.title}>Your Gallery Cart ({cart.length})</h2>
          <button onClick={onClose} className={styles.closeBtn} aria-label="Close cart">
            &times;
          </button>
        </div>

        <div className={styles.content}>
          {cart.length === 0 ? (
            <div className={styles.emptyState}>
              <div className={styles.emptyIcon}>🎨</div>
              <p className={styles.emptyText}>Your cart is empty.</p>
              <p className={styles.emptySubtext}>Explore our handcrafted pieces to bring life to your spaces.</p>
              <Link href="/shop" onClick={onClose} className={styles.shopBtn}>
                Browse Shop
              </Link>
            </div>
          ) : (
            <div className={styles.itemList}>
              {cart.map((item) => (
                <div key={item.product.id} className={styles.item}>
                  <div className={styles.imageContainer}>
                    <Image
                      src={item.product.images[0]}
                      alt={item.product.name}
                      width={80}
                      height={80}
                      className={styles.image}
                    />
                  </div>
                  <div className={styles.details}>
                    <div className={styles.itemMeta}>
                      <span className={styles.itemCategory}>{item.product.category}</span>
                      <h4 className={styles.itemName}>{item.product.name}</h4>
                    </div>
                    <div className={styles.itemPrice}>
                      ₹{item.product.price.toLocaleString("en-IN")}
                    </div>
                    <div className={styles.controls}>
                      <div className={styles.quantityPicker}>
                        <button
                          onClick={() => updateCartQuantity(item.product.id, item.quantity - 1)}
                          className={styles.qtyBtn}
                          aria-label="Decrease quantity"
                        >
                          -
                        </button>
                        <span className={styles.qtyVal}>{item.quantity}</span>
                        <button
                          onClick={() => updateCartQuantity(item.product.id, item.quantity + 1)}
                          className={styles.qtyBtn}
                          aria-label="Increase quantity"
                        >
                          +
                        </button>
                      </div>
                      <button
                        onClick={() => removeFromCart(item.product.id)}
                        className={styles.removeBtn}
                      >
                        Remove
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        {cart.length > 0 && (
          <div className={styles.footer}>
            <div className={styles.subtotalRow}>
              <span>Subtotal:</span>
              <span className={styles.subtotalVal}>₹{subtotal.toLocaleString("en-IN")}</span>
            </div>
            <p className={styles.shippingNotice}>GST included. Shipping calculated at checkout.</p>
            <div className={styles.actions}>
              <Link href="/cart" onClick={onClose} className={styles.viewCartBtn}>
                View Full Cart
              </Link>
              <Link href="/checkout" onClick={onClose} className={styles.checkoutBtn}>
                Checkout Now
              </Link>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
