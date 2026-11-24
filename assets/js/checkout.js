// 3-Step Checkout Wizard with Delhi & NCR Validation, Coupon Engine & Partial COD Token Deposit

document.addEventListener('DOMContentLoaded', () => {
  initCheckoutWizard();
});

function initCheckoutWizard() {
  let currentStep = 1;

  const step1Panel = document.getElementById('step-1-panel');
  const step2Panel = document.getElementById('step-2-panel');
  const step3Panel = document.getElementById('step-3-panel');

  const step1Indicator = document.getElementById('step-indicator-1');
  const step2Indicator = document.getElementById('step-indicator-2');
  const step3Indicator = document.getElementById('step-indicator-3');

  const toStep2Btn = document.getElementById('to-step-2-btn');
  const toStep3Btn = document.getElementById('to-step-3-btn');
  const backToStep1Btn = document.getElementById('back-to-step-1-btn');
  const backToStep2Btn = document.getElementById('back-to-step-2-btn');
  const placeOrderBtn = document.getElementById('place-order-btn');

  // Form inputs
  const nameInput = document.getElementById('checkout-name');
  const emailInput = document.getElementById('checkout-email');
  const phoneInput = document.getElementById('checkout-phone');
  const addressInput = document.getElementById('checkout-address');
  const cityInput = document.getElementById('checkout-city');
  const stateInput = document.getElementById('checkout-state');
  const pinInput = document.getElementById('checkout-pin');

  // Payment radios
  const partialCodRadio = document.getElementById('payment-partial-cod');
  const prepaidRadio = document.getElementById('payment-prepaid');

  // Coupon elements
  const couponInput = document.getElementById('checkout-coupon-input');
  const applyCouponBtn = document.getElementById('apply-coupon-btn');
  const appliedCouponTag = document.getElementById('applied-coupon-tag');
  const appliedCouponCode = document.getElementById('applied-coupon-code');
  const appliedDiscountVal = document.getElementById('applied-discount-val');
  const removeCouponBtn = document.getElementById('remove-coupon-btn');

  // Summary labels
  const summarySubtotalVal = document.getElementById('summary-subtotal-val');
  const summaryDiscountRow = document.getElementById('summary-discount-row');
  const summaryDiscountVal = document.getElementById('summary-discount-val');
  const summaryShippingVal = document.getElementById('summary-shipping-val');
  const summaryTotalVal = document.getElementById('summary-total-val');
  const summaryTokenVal = document.getElementById('summary-token-val');
  const summaryBalanceVal = document.getElementById('summary-balance-val');
  const tokenBreakdownBox = document.getElementById('token-breakdown-box');

  // State variables
  let baseSubtotal = parseFloat((summarySubtotalVal?.textContent || '0').replace(/[^0-9.]/g, '')) || 0;
  let activeCouponCode = appliedCouponCode?.textContent?.trim() || '';
  let activeDiscount = parseFloat(appliedDiscountVal?.textContent || '0') || 0;

  function recalculateTotals() {
    const effectiveSubtotal = Math.max(0, baseSubtotal - activeDiscount);
    // Free shipping threshold ₹2,500, else ₹199 standard
    const shippingFee = (effectiveSubtotal >= 2500) ? 0 : 199;
    const finalTotal = effectiveSubtotal + shippingFee;

    const isPartialCod = partialCodRadio && partialCodRadio.checked;
    const tokenDeposit = isPartialCod ? Math.min(500, finalTotal) : finalTotal;
    const balanceOnDelivery = isPartialCod ? Math.max(0, finalTotal - tokenDeposit) : 0;

    // Update Summary UI
    if (summaryDiscountRow) {
      summaryDiscountRow.style.display = (activeDiscount > 0) ? 'flex' : 'none';
      if (summaryDiscountVal) summaryDiscountVal.textContent = Math.round(activeDiscount).toLocaleString('en-IN');
    }

    if (summaryShippingVal) {
      if (shippingFee === 0) {
        summaryShippingVal.innerHTML = '<strong style="color: var(--accent-green-vibrant);">FREE</strong>';
      } else {
        summaryShippingVal.textContent = '₹' + shippingFee;
      }
    }

    if (summaryTotalVal) {
      summaryTotalVal.textContent = Math.round(finalTotal).toLocaleString('en-IN');
    }

    if (summaryTokenVal) {
      summaryTokenVal.textContent = Math.round(tokenDeposit).toLocaleString('en-IN');
    }

    if (summaryBalanceVal) {
      summaryBalanceVal.textContent = Math.round(balanceOnDelivery).toLocaleString('en-IN');
    }

    document.querySelectorAll('.cod-balance-label').forEach(el => {
      el.textContent = Math.round(balanceOnDelivery).toLocaleString('en-IN');
    });

    if (tokenBreakdownBox) {
      tokenBreakdownBox.style.display = isPartialCod ? 'flex' : 'none';
    }

    // Update place order button
    if (placeOrderBtn) {
      placeOrderBtn.setAttribute('data-amount', tokenDeposit);
      placeOrderBtn.setAttribute('data-total', finalTotal);
      placeOrderBtn.setAttribute('data-shipping', shippingFee);
      placeOrderBtn.setAttribute('data-balance', balanceOnDelivery);

      if (isPartialCod) {
        placeOrderBtn.textContent = `Pay ₹500 Token Deposit & Confirm Order →`;
      } else {
        placeOrderBtn.textContent = `Pay ₹${Math.round(finalTotal).toLocaleString('en-IN')} & Place Order →`;
      }
    }

    // Step 3 values
    const payNowReview = document.getElementById('review-pay-now-val');
    const balanceReview = document.getElementById('review-balance-val');
    const balanceReviewRow = document.getElementById('review-balance-row');

    if (payNowReview) {
      payNowReview.textContent = `₹${Math.round(tokenDeposit).toLocaleString('en-IN')}`;
    }
    if (balanceReview) {
      balanceReview.textContent = `₹${Math.round(balanceOnDelivery).toLocaleString('en-IN')}`;
    }
    if (balanceReviewRow) {
      balanceReviewRow.style.display = isPartialCod ? 'flex' : 'none';
    }
  }

  // --- Coupon Handlers ---
  if (applyCouponBtn && couponInput) {
    applyCouponBtn.addEventListener('click', async () => {
      const code = couponInput.value.trim().toUpperCase();
      if (!code) {
        if (typeof showToast === 'function') showToast('Please enter a coupon code.', 'error');
        return;
      }

      applyCouponBtn.disabled = true;
      applyCouponBtn.textContent = 'Applying...';

      try {
        const formData = new FormData();
        formData.append('action', 'apply_coupon');
        formData.append('couponCode', code);

        const res = await fetch('/api/cart.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();

        if (data.success && data.discount > 0) {
          activeCouponCode = code;
          activeDiscount = parseFloat(data.discount);

          if (appliedCouponTag) appliedCouponTag.style.display = 'flex';
          if (appliedCouponCode) appliedCouponCode.textContent = code;
          if (appliedDiscountVal) appliedDiscountVal.textContent = Math.round(activeDiscount).toLocaleString('en-IN');

          recalculateTotals();
          if (typeof showToast === 'function') {
            showToast(`Coupon ${code} applied! Saved ₹${Math.round(activeDiscount)}`, 'success');
          }
        } else {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Invalid or expired coupon code', 'error');
          }
        }
      } catch (err) {
        console.error('Coupon apply error:', err);
        if (typeof showToast === 'function') showToast('Failed to apply coupon', 'error');
      } finally {
        applyCouponBtn.disabled = false;
        applyCouponBtn.textContent = 'Apply';
      }
    });
  }

  if (removeCouponBtn) {
    removeCouponBtn.addEventListener('click', async () => {
      try {
        const formData = new FormData();
        formData.append('action', 'remove_coupon');
        await fetch('/api/cart.php', { method: 'POST', body: formData });

        activeCouponCode = '';
        activeDiscount = 0;
        if (couponInput) couponInput.value = '';
        if (appliedCouponTag) appliedCouponTag.style.display = 'none';

        recalculateTotals();
        if (typeof showToast === 'function') showToast('Coupon removed', 'info');
      } catch (err) {
        console.error('Coupon remove error:', err);
      }
    });
  }

  // --- Step 1 Validation (Delhi NCR) ---
  function validateStep1() {
    let isValid = true;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[0-9]{10}$/;
    const pinRegex = /^[0-9]{6}$/;

    document.querySelectorAll('.checkout-error-text').forEach((el) => (el.textContent = ''));

    if (!nameInput.value.trim()) {
      document.getElementById('error-name').textContent = 'Full name is required';
      isValid = false;
    }

    if (!emailInput.value.trim()) {
      document.getElementById('error-email').textContent = 'Email address is required';
      isValid = false;
    } else if (!emailRegex.test(emailInput.value.trim())) {
      document.getElementById('error-email').textContent = 'Please enter a valid email';
      isValid = false;
    }

    if (!phoneInput.value.trim()) {
      document.getElementById('error-phone').textContent = '10-digit mobile number is required';
      isValid = false;
    } else if (!phoneRegex.test(phoneInput.value.trim())) {
      document.getElementById('error-phone').textContent = 'Phone number must be exactly 10 digits';
      isValid = false;
    }

    if (!addressInput.value.trim()) {
      document.getElementById('error-address').textContent = 'Delivery address is required';
      isValid = false;
    }

    if (!cityInput.value.trim()) {
      document.getElementById('error-city').textContent = 'City / District is required';
      isValid = false;
    }

    if (!pinInput.value.trim()) {
      document.getElementById('error-pin').textContent = 'PIN code is required';
      isValid = false;
    } else if (!pinRegex.test(pinInput.value.trim())) {
      document.getElementById('error-pin').textContent = 'PIN code must be exactly 6 digits';
      isValid = false;
    }

    return isValid;
  }

  function setStep(step) {
    currentStep = step;

    step1Panel.style.display = step === 1 ? 'flex' : 'none';
    step2Panel.style.display = step === 2 ? 'flex' : 'none';
    step3Panel.style.display = step === 3 ? 'flex' : 'none';

    if (step === 1) {
      step1Indicator.className = 'step-indicator active';
      step2Indicator.className = 'step-indicator';
      step3Indicator.className = 'step-indicator';
    } else if (step === 2) {
      step1Indicator.className = 'step-indicator done';
      step2Indicator.className = 'step-indicator active';
      step3Indicator.className = 'step-indicator';
    } else if (step === 3) {
      step1Indicator.className = 'step-indicator done';
      step2Indicator.className = 'step-indicator done';
      step3Indicator.className = 'step-indicator active';
      populateReviewStep();
    }
  }

  function populateReviewStep() {
    document.getElementById('review-cust-name').textContent = nameInput.value.trim();
    document.getElementById('review-cust-contact').textContent = `${phoneInput.value.trim()} • ${emailInput.value.trim()}`;
    document.getElementById('review-cust-address').textContent = `${addressInput.value.trim()}, ${cityInput.value.trim()}, ${stateInput.value.trim()} - ${pinInput.value.trim()}`;

    const isPartialCod = partialCodRadio && partialCodRadio.checked;
    document.getElementById('review-payment-method').textContent = isPartialCod
      ? 'Partial COD (₹500 Advance Token Online + Balance on Delivery)'
      : 'Full Online Payment (100% Prepaid via UPI/Card)';

    recalculateTotals();
  }

  if (toStep2Btn) {
    toStep2Btn.addEventListener('click', () => {
      if (validateStep1()) {
        setStep(2);
      } else {
        if (typeof showToast === 'function') {
          showToast('Please correct the highlighted address fields.', 'error');
        }
      }
    });
  }

  if (toStep3Btn) {
    toStep3Btn.addEventListener('click', () => {
      setStep(3);
    });
  }

  if (backToStep1Btn) {
    backToStep1Btn.addEventListener('click', () => {
      setStep(1);
    });
  }

  if (backToStep2Btn) {
    backToStep2Btn.addEventListener('click', () => {
      setStep(2);
    });
  }

  // Payment radio selections
  document.querySelectorAll('.payment-card').forEach((card) => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.payment-card').forEach((c) => c.classList.remove('selected'));
      card.classList.add('selected');
      const radio = card.querySelector('input[type="radio"]');
      if (radio) {
        radio.checked = true;
        recalculateTotals();
      }
    });
  });

  // --- Place Order & Token Deposit ---
  if (placeOrderBtn) {
    placeOrderBtn.addEventListener('click', () => {
      const isPartialCod = partialCodRadio && partialCodRadio.checked;
      const payableAmount = parseFloat(placeOrderBtn.getAttribute('data-amount') || '500');

      if (typeof window.Razorpay !== 'undefined') {
        triggerRazorpay(payableAmount, isPartialCod ? 'Partial COD' : 'Prepaid');
      } else {
        // Fallback direct placement
        submitOrder(isPartialCod ? 'Partial COD' : 'Prepaid', 'Paid');
      }
    });
  }

  function triggerRazorpay(payableAmount, method) {
    const isPartialCod = (method === 'Partial COD');
    const options = {
      key: 'rzp_test_51PxM23K9',
      amount: Math.round(payableAmount * 100), // In paise
      currency: 'INR',
      name: 'Murli Creations',
      description: isPartialCod ? '₹500 Advance Token Deposit' : 'Handcrafted Heritage Order',
      image: '/images/brand/black_logo.png',
      handler: function (response) {
        if (typeof showToast === 'function') {
          showToast(`Deposit received! Payment Ref: ${response.razorpay_payment_id}`, 'success');
        }
        submitOrder(method, 'Paid', response.razorpay_payment_id);
      },
      prefill: {
        name: nameInput.value.trim(),
        email: emailInput.value.trim(),
        contact: phoneInput.value.trim()
      },
      theme: {
        color: '#1ea659'
      }
    };

    const rzp = new window.Razorpay(options);
    rzp.on('payment.failed', function (response) {
      if (typeof showToast === 'function') {
        showToast(`Payment interrupted: ${response.error.description}`, 'error');
      }
    });
    rzp.open();
  }

  async function submitOrder(method, paymentStatus, transactionRef = '') {
    placeOrderBtn.disabled = true;
    placeOrderBtn.textContent = 'Securing Your Order...';

    const isPartialCod = (method === 'Partial COD');
    const finalTotal = parseFloat(placeOrderBtn.getAttribute('data-total') || '0');
    const shippingFee = parseFloat(placeOrderBtn.getAttribute('data-shipping') || '0');
    const tokenAmount = isPartialCod ? Math.min(500, finalTotal) : finalTotal;
    const remainingBalance = isPartialCod ? Math.max(0, finalTotal - tokenAmount) : 0;

    const customer = {
      name: nameInput.value.trim(),
      phone: phoneInput.value.trim(),
      email: emailInput.value.trim(),
      address: addressInput.value.trim(),
      city: cityInput.value.trim(),
      state: stateInput.value.trim(),
      pinCode: pinInput.value.trim(),
      transactionRef: transactionRef
    };

    try {
      const formData = new FormData();
      formData.append('action', 'place_order');
      formData.append('customer', JSON.stringify(customer));
      formData.append('paymentMethod', method);
      formData.append('paymentStatus', paymentStatus);
      formData.append('couponCode', activeCouponCode);
      formData.append('discountAmount', activeDiscount);
      formData.append('shippingFee', shippingFee);
      formData.append('tokenAmount', tokenAmount);
      formData.append('remainingBalance', remainingBalance);

      const response = await fetch('/api/orders.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.success && data.orderId) {
        if (typeof showToast === 'function') {
          showToast(`Order #${data.orderId} successfully registered!`, 'success');
        }
        window.location.href = `/order-confirmation.php?orderId=${data.orderId}`;
      } else {
        if (typeof showToast === 'function') {
          showToast(data.message || 'Failed to place order', 'error');
        }
        placeOrderBtn.disabled = false;
        recalculateTotals();
      }
    } catch (err) {
      console.error('Order submission error:', err);
      if (typeof showToast === 'function') {
        showToast('Network error while placing order', 'error');
      }
      placeOrderBtn.disabled = false;
      recalculateTotals();
    }
  }

  // Initial calculation
  recalculateTotals();
}
