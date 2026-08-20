import React, { useState } from "react";
import { Link } from "react-router-dom";
import { useShop } from "../context/ShopContext";
import styles from "./Cart.module.css";

export default function CartPage() {
  const { cart, updateCartQuantity, removeFromCart } = useShop();
  
  // Promo code states
  const [promoCode, setPromoCode] = useState("");
  const [activeDiscount, setActiveDiscount] = useState<{ code: string; percent: number } | null>(null);
  const [promoError, setPromoError] = useState("");
  const [promoSuccess, setPromoSuccess] = useState("");

  const subtotal = cart.reduce((sum, item) => sum + item.product.price * item.quantity, 0);

  // Discount calculation
  const discountAmount = activeDiscount ? Math.round(subtotal * (activeDiscount.percent / 100)) : 0;

  // Shipping logic: Free shipping for orders above ₹3000, otherwise flat ₹150
  const shippingThreshold = 3000;
  const isShippingFree = subtotal - discountAmount >= shippingThreshold;
  const shippingCharge = subtotal === 0 ? 0 : isShippingFree ? 0 : 150;

  // Final Total calculation
  const finalTotal = subtotal - discountAmount + shippingCharge;

  const handleApplyPromo = (e: React.FormEvent) => {
    e.preventDefault();
    setPromoError("");
    setPromoSuccess("");

    const code = promoCode.trim().toUpperCase();

    if (code === "MURLI10") {
      setActiveDiscount({ code: "MURLI10", percent: 10 });
      setPromoSuccess("Coupon Applied! 10% Discount credited to your order.");
      setPromoCode("");
    } else if (code === "WELCOME5") {
      setActiveDiscount({ code: "WELCOME5", percent: 5 });
      setPromoSuccess("Coupon Applied! 5% Welcome Discount credited.");
      setPromoCode("");
    } else if (code === "") {
      setPromoError("Please enter a valid coupon code.");
    } else {
      setPromoError("Invalid coupon code. Try 'MURLI10' or 'WELCOME5'.");
    }
  };

  if (cart.length === 0) {
    return (
      <div className={styles.cartPage}>
        <div className={`container ${styles.emptyState}`}>
          <div className={styles.emptyIcon}>🛍️</div>
          <h2 className={styles.emptyTitle}>Your Bag is Empty</h2>
          <p className={styles.emptyText}>
            You haven't added any handcrafted art pieces to your cart yet. Explore our collections of traditional Lippan art, carved brackets, and hand-painted mandala plates.
          </p>
          <Link to="/shop" className={styles.shopBtn}>
            Start Shopping
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className={styles.cartPage}>
      <div className="container">
        <h2 className={styles.title}>Your Shopping Bag</h2>

        <div className={styles.layout}>
          {/* Column 1: Items List */}
          <div className={styles.cartSection}>
            {cart.map((item) => (
              <div key={item.product.id} className={styles.item}>
                <div className={styles.imageContainer}>
                  <img
                    src={item.product.images[0]}
                    alt={item.product.name}
                    className={styles.image}
                    style={{ width: "100%", height: "100%", position: "absolute", top: 0, left: 0, objectFit: "cover" }}
                  />
                </div>
                <div className={styles.info}>
                  <span className={styles.category}>{item.product.category}</span>
                  <Link to={`/product/${item.product.slug}`}>
                    <h3 className={styles.name}>{item.product.name}</h3>
                  </Link>
                  <span className={styles.price}>₹{item.product.price.toLocaleString("en-IN")}</span>
                </div>
                <div className={styles.controls}>
                  <div className={styles.quantityPicker}>
                    <button
                      onClick={() => updateCartQuantity(item.product.id, item.quantity - 1)}
                      className={styles.qtyBtn}
                    >
                      -
                    </button>
                    <span className={styles.qtyVal}>{item.quantity}</span>
                    <button
                      onClick={() => updateCartQuantity(item.product.id, item.quantity + 1)}
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
                <div className={styles.totalCol}>
                  ₹{(item.product.price * item.quantity).toLocaleString("en-IN")}
                </div>
              </div>
            ))}
          </div>

          {/* Column 2: Order Summary Card */}
          <div className={styles.summarySection}>
            <h3 className={styles.summaryTitle}>Summary</h3>
            
            <div className={styles.row}>
              <span>Subtotal</span>
              <span>₹{subtotal.toLocaleString("en-IN")}</span>
            </div>

            {activeDiscount && (
              <div className={styles.row}>
                <span>Discount ({activeDiscount.code})</span>
                <span className={styles.discountVal}>- ₹{discountAmount.toLocaleString("en-IN")}</span>
              </div>
            )}

            <div className={styles.row}>
              <span>Shipping Estimate</span>
              <span>{shippingCharge === 0 ? "FREE" : `₹${shippingCharge}`}</span>
            </div>

            {!isShippingFree && subtotal > 0 && (
              <p style={{ fontSize: "0.75rem", color: "var(--accent-terracotta)", marginTop: "-10px" }}>
                Add ₹{(shippingThreshold - (subtotal - discountAmount)).toLocaleString("en-IN")} more to unlock FREE shipping!
              </p>
            )}

            {/* Promo Code Input block */}
            <div className={styles.promoBox}>
              <span className={styles.promoTitle}>Have a Promo Code?</span>
              <form onSubmit={handleApplyPromo} className={styles.promoForm}>
                <input
                  type="text"
                  placeholder="e.g. MURLI10"
                  value={promoCode}
                  onChange={(e) => setPromoCode(e.target.value)}
                  className={styles.promoInput}
                  aria-label="Promo code input"
                />
                <button type="submit" className={styles.promoBtn}>
                  Apply
                </button>
              </form>
              {promoSuccess && <span className={`${styles.promoMessage} styles.promoSuccess`}>{promoSuccess}</span>}
              {promoError && <span className={`${styles.promoMessage} styles.promoError`}>{promoError}</span>}
            </div>

            <div className={styles.rowBold}>
              <span>Grand Total</span>
              <span>₹{finalTotal.toLocaleString("en-IN")}</span>
            </div>

            <Link
              to={activeDiscount ? `/checkout?code=${activeDiscount.code}&percent=${activeDiscount.percent}` : "/checkout"}
              className={styles.checkoutBtn}
            >
              Secure Checkout
            </Link>

            <Link to="/shop" className={styles.continueBtn}>
              Continue Shopping
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
