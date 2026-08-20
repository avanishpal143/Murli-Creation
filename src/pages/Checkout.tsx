import React, { useState, useEffect, Suspense } from "react";
import { useNavigate, useSearchParams, Link } from "react-router-dom";
import { useShop } from "../context/ShopContext";
import styles from "./Checkout.module.css";

// Declare Razorpay type globally for TS
declare global {
  interface Window {
    Razorpay: any;
  }
}

interface FormValues {
  name: string;
  phone: string;
  email: string;
  address: string;
  city: string;
  state: string;
  pinCode: string;
}

interface FormErrors {
  [key: string]: string;
}

function CheckoutContent() {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const { cart, placeOrder, showToast } = useShop();

  // Load Razorpay dynamically
  useEffect(() => {
    const script = document.createElement("script");
    script.src = "https://checkout.razorpay.com/v1/checkout.js";
    script.async = true;
    document.body.appendChild(script);
    return () => {
      document.body.removeChild(script);
    };
  }, []);

  // Wizard state: 1 = Shipping, 2 = Payment Method, 3 = Review & Place
  const [step, setStep] = useState(1);

  // Form states
  const [formValues, setFormValues] = useState<FormValues>({
    name: "",
    phone: "",
    email: "",
    address: "",
    city: "",
    state: "Haryana",
    pinCode: ""
  });
  const [errors, setErrors] = useState<FormErrors>({});

  // Payment State: 'COD' or 'Razorpay'
  const [paymentMethod, setPaymentMethod] = useState<"COD" | "Razorpay">("COD");
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Read discount from URL (passed from Cart page)
  const couponCode = searchParams.get("code") || "";
  const couponPercent = Number(searchParams.get("percent") || "0");

  const subtotal = cart.reduce((sum, item) => sum + item.product.price * item.quantity, 0);
  const discountAmount = couponPercent > 0 ? Math.round(subtotal * (couponPercent / 100)) : 0;
  const shippingCharge = subtotal - discountAmount >= 3000 ? 0 : 150;
  const finalTotal = subtotal - discountAmount + shippingCharge;

  // Redirect if cart is empty on mount
  useEffect(() => {
    if (cart.length === 0 && !isSubmitting) {
      showToast("Your cart is empty. Please add items before checking out.", "error");
      navigate("/shop");
    }
  }, [cart, navigate, showToast, isSubmitting]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormValues({ ...formValues, [name]: value });
    if (errors[name]) {
      setErrors({ ...errors, [name]: "" });
    }
  };

  const validateStep1 = () => {
    const newErrors: FormErrors = {};
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[0-9]{10}$/;
    const pinRegex = /^[0-9]{6}$/;

    if (!formValues.name.trim()) newErrors.name = "Name is required";
    if (!formValues.address.trim()) newErrors.address = "Address is required";
    if (!formValues.city.trim()) newErrors.city = "City is required";
    
    if (!formValues.email.trim()) {
      newErrors.email = "Email is required";
    } else if (!emailRegex.test(formValues.email)) {
      newErrors.email = "Invalid email format";
    }

    if (!formValues.phone.trim()) {
      newErrors.phone = "Phone number is required";
    } else if (!phoneRegex.test(formValues.phone)) {
      newErrors.phone = "Phone must be exactly 10 digits";
    }

    if (!formValues.pinCode.trim()) {
      newErrors.pinCode = "PIN code is required";
    } else if (!pinRegex.test(formValues.pinCode)) {
      newErrors.pinCode = "PIN code must be exactly 6 digits";
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleNextStep = () => {
    if (step === 1) {
      if (validateStep1()) {
        setStep(2);
      } else {
        showToast("Please correct the form fields.", "error");
      }
    } else if (step === 2) {
      setStep(3);
    }
  };

  const handlePrevStep = () => {
    setStep(Math.max(1, step - 1));
  };

  // Online checkout gateway initializer
  const triggerRazorpayPayment = () => {
    if (!window.Razorpay) {
      showToast("Payment system is loading, please try again in a moment.", "error");
      return;
    }

    const options = {
      key: "rzp_test_51PxM23K9", // Sandbox test key
      amount: finalTotal * 100, // In paise
      currency: "INR",
      name: "Murli Creations",
      description: "Payment for Handcrafted Wall Art Order",
      image: "/vercel.svg",
      handler: function (response: any) {
        // Payment successful callback
        showToast(`Online payment successful! Ref: ${response.razorpay_payment_id}`, "success");
        submitOrder("Razorpay", "Paid");
      },
      prefill: {
        name: formValues.name,
        email: formValues.email,
        contact: formValues.phone
      },
      theme: {
        color: "#C39B62" // Brand Gold Color accent
      }
    };

    const rzp = new window.Razorpay(options);
    rzp.on("payment.failed", function (response: any) {
      showToast(`Payment failed: ${response.error.description}`, "error");
    });
    rzp.open();
  };

  const submitOrder = (method: "COD" | "Razorpay", paymentStatus: "Pending" | "Paid" | "Failed") => {
    setIsSubmitting(true);
    try {
      const order = placeOrder(formValues, method, paymentStatus);
      showToast(`Order ${order.id} placed successfully!`, "success");
      navigate(`/order-confirmation?orderId=${order.id}`);
    } catch (e) {
      showToast("Failed to place order. Try again.", "error");
      setIsSubmitting(false);
    }
  };

  const handlePlaceOrder = () => {
    if (paymentMethod === "Razorpay") {
      triggerRazorpayPayment();
    } else {
      submitOrder("COD", "Pending");
    }
  };

  if (cart.length === 0) return null;

  return (
    <div className={styles.checkoutPage}>
      <div className="container">
        <div className={styles.layout}>
          {/* Column 1: Progress Form wizard */}
          <div>
            {/* Step Wizard Indicator dots */}
            <div className={styles.stepsHeader}>
              <div className={`${styles.stepIndicator} ${step >= 1 ? styles.stepDone : ""}`}>
                1
                <span className={`${styles.stepText} ${step === 1 ? styles.stepTextActive : ""}`}>
                  Shipping
                </span>
              </div>
              <div
                className={`${styles.stepIndicator} ${step > 2 ? styles.stepDone : step === 2 ? styles.stepActive : ""}`}
              >
                2
                <span className={`${styles.stepText} ${step === 2 ? styles.stepTextActive : ""}`}>
                  Payment
                </span>
              </div>
              <div className={`${styles.stepIndicator} ${step === 3 ? styles.stepActive : ""}`}>
                3
                <span className={`${styles.stepText} ${step === 3 ? styles.stepTextActive : ""}`}>
                  Review
                </span>
              </div>
            </div>

            {/* Form Panels */}
            <div className={styles.formSection}>
              {/* STEP 1: SHIPPING details */}
              {step === 1 && (
                <>
                  <h3 className={styles.sectionTitle}>Shipping Address</h3>
                  <div className={styles.formGrid}>
                    <div className={styles.inputGroup}>
                      <label className={styles.label}>Full Name</label>
                      <input
                        type="text"
                        name="name"
                        value={formValues.name}
                        onChange={handleInputChange}
                        className={styles.input}
                      />
                      {errors.name && <span className={styles.errorText}>{errors.name}</span>}
                    </div>

                    <div className={styles.inputGroup}>
                      <label className={styles.label}>Phone Number</label>
                      <input
                        type="tel"
                        name="phone"
                        value={formValues.phone}
                        onChange={handleInputChange}
                        placeholder="10-digit mobile"
                        className={styles.input}
                      />
                      {errors.phone && <span className={styles.errorText}>{errors.phone}</span>}
                    </div>

                    <div className={`${styles.inputGroup} ${styles.fullWidth}`}>
                      <label className={styles.label}>Email Address</label>
                      <input
                        type="email"
                        name="email"
                        value={formValues.email}
                        onChange={handleInputChange}
                        placeholder="for order tracking"
                        className={styles.input}
                      />
                      {errors.email && <span className={styles.errorText}>{errors.email}</span>}
                    </div>

                    <div className={`${styles.inputGroup} ${styles.fullWidth}`}>
                      <label className={styles.label}>Street Address</label>
                      <input
                        type="text"
                        name="address"
                        value={formValues.address}
                        onChange={handleInputChange}
                        placeholder="House/Plot no, Area details"
                        className={styles.input}
                      />
                      {errors.address && <span className={styles.errorText}>{errors.address}</span>}
                    </div>

                    <div className={styles.inputGroup}>
                      <label className={styles.label}>City</label>
                      <input
                        type="text"
                        name="city"
                        value={formValues.city}
                        onChange={handleInputChange}
                        className={styles.input}
                      />
                      {errors.city && <span className={styles.errorText}>{errors.city}</span>}
                    </div>

                    <div className={styles.inputGroup}>
                      <label className={styles.label}>State</label>
                      <select
                        name="state"
                        value={formValues.state}
                        onChange={handleInputChange}
                        className={styles.select}
                      >
                        <option value="Haryana">Haryana</option>
                        <option value="Delhi">Delhi NCR</option>
                        <option value="Punjab">Punjab</option>
                        <option value="Rajasthan">Rajasthan</option>
                        <option value="Uttar Pradesh">Uttar Pradesh</option>
                        <option value="Maharashtra">Maharashtra</option>
                        <option value="Karnataka">Karnataka</option>
                        <option value="Gujarat">Gujarat</option>
                      </select>
                    </div>

                    <div className={styles.inputGroup}>
                      <label className={styles.label}>PIN Code</label>
                      <input
                        type="text"
                        name="pinCode"
                        value={formValues.pinCode}
                        onChange={handleInputChange}
                        placeholder="6-digit postal code"
                        className={styles.input}
                      />
                      {errors.pinCode && <span className={styles.errorText}>{errors.pinCode}</span>}
                    </div>
                  </div>

                  <div className={styles.buttonRow}>
                    <Link to="/cart" className={styles.prevBtn}>
                      Back to Cart
                    </Link>
                    <button onClick={handleNextStep} className={styles.nextBtn}>
                      Proceed to Payment
                    </button>
                  </div>
                </>
              )}

              {/* STEP 2: PAYMENT METHOD selection */}
              {step === 2 && (
                <>
                  <h3 className={styles.sectionTitle}>Payment Method</h3>
                  <div className={styles.paymentOptions}>
                    {/* Razorpay Online Option */}
                    <div
                      onClick={() => setPaymentMethod("Razorpay")}
                      className={`${styles.paymentCard} ${
                        paymentMethod === "Razorpay" ? styles.paymentCardSelected : ""
                      }`}
                    >
                      <input
                        type="radio"
                        name="payment"
                        checked={paymentMethod === "Razorpay"}
                        onChange={() => setPaymentMethod("Razorpay")}
                        className={styles.radioInput}
                        aria-label="Razorpay Online Payment"
                      />
                      <div className={styles.paymentDetails}>
                        <span className={styles.paymentName}>Online Payment (Razorpay Secure Gateway)</span>
                        <span className={styles.paymentDesc}>Pay securely via UPI (GPay/Paytm), Cards, or Netbanking.</span>
                      </div>
                    </div>

                    {/* Cash on Delivery Option */}
                    <div
                      onClick={() => setPaymentMethod("COD")}
                      className={`${styles.paymentCard} ${
                        paymentMethod === "COD" ? styles.paymentCardSelected : ""
                      }`}
                    >
                      <input
                        type="radio"
                        name="payment"
                        checked={paymentMethod === "COD"}
                        onChange={() => setPaymentMethod("COD")}
                        className={styles.radioInput}
                        aria-label="Cash on Delivery"
                      />
                      <div className={styles.paymentDetails}>
                        <span className={styles.paymentName}>Cash on Delivery (COD)</span>
                        <span className={styles.paymentDesc}>Pay cash at your doorstep. Standard shipping applies.</span>
                      </div>
                    </div>
                  </div>

                  {paymentMethod === "Razorpay" && (
                    <div className={styles.paymentWarning}>
                      💡 <strong>Sandbox Integration:</strong> Online transactions are running in Test Mode. Use any test card numbers or mock UPI requests in the popup screen to simulate completed payments.
                    </div>
                  )}

                  <div className={styles.buttonRow}>
                    <button onClick={handlePrevStep} className={styles.prevBtn}>
                      Back to Address
                    </button>
                    <button onClick={handleNextStep} className={styles.nextBtn}>
                      Review Order details
                    </button>
                  </div>
                </>
              )}

              {/* STEP 3: ORDER REVIEW & CONFIRM */}
              {step === 3 && (
                <>
                  <h3 className={styles.sectionTitle}>Review & Place Order</h3>
                  
                  <div className={styles.reviewList}>
                    <div className={styles.reviewItem}>
                      <span className={styles.reviewLabel}>Deliver To</span>
                      <span className={styles.reviewVal}>{formValues.name}</span>
                    </div>
                    <div className={styles.reviewItem}>
                      <span className={styles.reviewLabel}>Address</span>
                      <span className={styles.reviewVal}>
                        {formValues.address}, {formValues.city}, {formValues.state} - {formValues.pinCode}
                      </span>
                    </div>
                    <div className={styles.reviewItem}>
                      <span className={styles.reviewLabel}>Contact Contact</span>
                      <span className={styles.reviewVal}>
                        {formValues.phone} | {formValues.email}
                      </span>
                    </div>
                    <div className={styles.reviewItem}>
                      <span className={styles.reviewLabel}>Payment Route</span>
                      <span className={styles.reviewVal}>
                        {paymentMethod === "Razorpay" ? "Online Gateway (Razorpay)" : "Cash on Delivery (COD)"}
                      </span>
                    </div>
                  </div>

                  <div className={styles.buttonRow}>
                    <button onClick={handlePrevStep} className={styles.prevBtn}>
                      Back to Payments
                    </button>
                    <button
                      onClick={handlePlaceOrder}
                      disabled={isSubmitting}
                      className={styles.submitBtn}
                    >
                      {isSubmitting ? "Processing Order..." : "Confirm & Place Order"}
                    </button>
                  </div>
                </>
              )}
            </div>
          </div>

          {/* Column 2: Sticky Order summary panel */}
          <aside className={styles.summarySidebar}>
            <h3 className={styles.summaryTitle}>Order Summary</h3>
            <div className={styles.summaryItems}>
              {cart.map((item) => (
                <div key={item.product.id} className={styles.summaryItem}>
                  <div className={styles.summaryThumb} style={{ position: "relative" }}>
                    <img
                      src={item.product.images[0]}
                      alt={item.product.name}
                      style={{ width: "100%", height: "100%", position: "absolute", top: 0, left: 0, objectFit: "cover" }}
                    />
                  </div>
                  <div className={styles.summaryInfo}>
                    <span className={styles.summaryName}>{item.product.name}</span>
                    <span className={styles.summaryQtyPrice}>
                      {item.quantity} x ₹{item.product.price.toLocaleString("en-IN")}
                    </span>
                  </div>
                  <div className={styles.summaryTotalCol}>
                    ₹{(item.product.price * item.quantity).toLocaleString("en-IN")}
                  </div>
                </div>
              ))}
            </div>

            <div className={styles.row}>
              <span>Items Subtotal</span>
              <span>₹{subtotal.toLocaleString("en-IN")}</span>
            </div>

            {couponPercent > 0 && (
              <div className={styles.row}>
                <span>Discount ({couponCode})</span>
                <span className={styles.discountVal}>- ₹{discountAmount.toLocaleString("en-IN")}</span>
              </div>
            )}

            <div className={styles.row}>
              <span>Shipping Fee</span>
              <span>{shippingCharge === 0 ? "FREE" : `₹${shippingCharge}`}</span>
            </div>

            <div className={styles.rowBold}>
              <span>Total Price</span>
              <span>₹{finalTotal.toLocaleString("en-IN")}</span>
            </div>
          </aside>
        </div>
      </div>
    </div>
  );
}

export default function Checkout() {
  return (
    <Suspense
      fallback={
        <div className="container" style={{ padding: "100px 0", textAlign: "center" }}>
          <div className="spinner"></div>
          <p style={{ marginTop: "10px", color: "var(--text-muted)" }}>Loading checkout panels...</p>
        </div>
      }
    >
      <CheckoutContent />
    </Suspense>
  );
}
