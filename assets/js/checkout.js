// 3-Step Checkout Wizard & Razorpay Payment Modal

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

  // Form input references
  const nameInput = document.getElementById('checkout-name');
  const emailInput = document.getElementById('checkout-email');
  const phoneInput = document.getElementById('checkout-phone');
  const addressInput = document.getElementById('checkout-address');
  const cityInput = document.getElementById('checkout-city');
  const stateInput = document.getElementById('checkout-state');
  const pinInput = document.getElementById('checkout-pin');

  // Payment radios
  const codRadio = document.getElementById('payment-cod');
  const razorpayRadio = document.getElementById('payment-razorpay');

  function validateStep1() {
    let isValid = true;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[0-9]{10}$/;
    const pinRegex = /^[0-9]{6}$/;

    // Clear previous errors
    document.querySelectorAll('.checkout-error-text').forEach(el => el.textContent = '');

    if (!nameInput.value.trim()) {
      document.getElementById('error-name').textContent = 'Name is required';
      isValid = false;
    }

    if (!emailInput.value.trim()) {
      document.getElementById('error-email').textContent = 'Email is required';
      isValid = false;
    } else if (!emailRegex.test(emailInput.value.trim())) {
      document.getElementById('error-email').textContent = 'Invalid email format';
      isValid = false;
    }

    if (!phoneInput.value.trim()) {
      document.getElementById('error-phone').textContent = 'Phone number is required';
      isValid = false;
    } else if (!phoneRegex.test(phoneInput.value.trim())) {
      document.getElementById('error-phone').textContent = 'Phone must be exactly 10 digits';
      isValid = false;
    }

    if (!addressInput.value.trim()) {
      document.getElementById('error-address').textContent = 'Address is required';
      isValid = false;
    }

    if (!cityInput.value.trim()) {
      document.getElementById('error-city').textContent = 'City is required';
      isValid = false;
    }

    if (!pinInput.value.trim()) {
      document.getElementById('error-pin').textContent = 'PIN code is required';
      isValid = false;
    } else if (!pinRegex.test(pinInput.value.trim())) {
      document.getElementById('error-pin').textContent = 'PIN code must be 6 digits';
      isValid = false;
    }

    return isValid;
  }

  function setStep(step) {
    currentStep = step;

    step1Panel.style.display = (step === 1) ? 'flex' : 'none';
    step2Panel.style.display = (step === 2) ? 'flex' : 'none';
    step3Panel.style.display = (step === 3) ? 'flex' : 'none';

    // Update indicator states
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
    
    const isRazorpay = razorpayRadio && razorpayRadio.checked;
    document.getElementById('review-payment-method').textContent = isRazorpay ? 'Online UPI & Cards (Razorpay)' : 'Cash on Delivery (COD)';
  }

  if (toStep2Btn) {
    toStep2Btn.addEventListener('click', () => {
      if (validateStep1()) {
        setStep(2);
      } else {
        if (typeof showToast === 'function') {
          showToast('Please correct the form fields.', 'error');
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

  // Payment card clicks
  document.querySelectorAll('.payment-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      const radio = card.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    });
  });

  // Place Order handler
  if (placeOrderBtn) {
    placeOrderBtn.addEventListener('click', () => {
      const isRazorpay = razorpayRadio && razorpayRadio.checked;
      if (isRazorpay) {
        triggerRazorpay();
      } else {
        submitOrder('COD', 'Pending');
      }
    });
  }

  function triggerRazorpay() {
    const finalAmount = parseFloat(placeOrderBtn.getAttribute('data-amount') || '0');
    
    if (typeof window.Razorpay === 'undefined') {
      if (typeof showToast === 'function') {
        showToast('Payment system loading... please try again.', 'error');
      }
      return;
    }

    const options = {
      key: 'rzp_test_51PxM23K9',
      amount: Math.round(finalAmount * 100), // In paise
      currency: 'INR',
      name: 'Murli Creations',
      description: 'Handcrafted Wall Art Order',
      image: '/images/brand/black_logo.png',
      handler: function(response) {
        if (typeof showToast === 'function') {
          showToast(`Payment successful! Ref: ${response.razorpay_payment_id}`, 'success');
        }
        submitOrder('Razorpay', 'Paid');
      },
      prefill: {
        name: nameInput.value.trim(),
        email: emailInput.value.trim(),
        contact: phoneInput.value.trim()
      },
      theme: {
        color: '#0099fa'
      }
    };

    const rzp = new window.Razorpay(options);
    rzp.on('payment.failed', function(response) {
      if (typeof showToast === 'function') {
        showToast(`Payment failed: ${response.error.description}`, 'error');
      }
    });
    rzp.open();
  }

  async function submitOrder(method, paymentStatus) {
    placeOrderBtn.disabled = true;
    placeOrderBtn.textContent = 'Processing Order...';

    const customer = {
      name: nameInput.value.trim(),
      phone: phoneInput.value.trim(),
      email: emailInput.value.trim(),
      address: addressInput.value.trim(),
      city: cityInput.value.trim(),
      state: stateInput.value.trim(),
      pinCode: pinInput.value.trim()
    };

    try {
      const formData = new FormData();
      formData.append('action', 'place_order');
      formData.append('customer', JSON.stringify(customer));
      formData.append('paymentMethod', method);
      formData.append('paymentStatus', paymentStatus);

      const response = await fetch('/api/orders.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.success && data.orderId) {
        if (typeof showToast === 'function') {
          showToast(`Order ${data.orderId} placed successfully!`, 'success');
        }
        window.location.href = `/order-confirmation.php?orderId=${data.orderId}`;
      } else {
        if (typeof showToast === 'function') {
          showToast(data.message || 'Failed to register order', 'error');
        }
        placeOrderBtn.disabled = false;
        placeOrderBtn.textContent = 'Place Order';
      }
    } catch (err) {
      console.error('Order submission error:', err);
      if (typeof showToast === 'function') {
        showToast('Network error while placing order', 'error');
      }
      placeOrderBtn.disabled = false;
      placeOrderBtn.textContent = 'Place Order';
    }
  }
}
