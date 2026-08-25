// Core UI Interactions, Progressive Animations, Testimonial Slider & Standard Handlers for Murli Creations

document.addEventListener('DOMContentLoaded', () => {
  // 1. Scroll-reveal Observer
  const revealElements = document.querySelectorAll('.reveal-on-scroll');
  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
          revealObserver.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.05,
      rootMargin: '0px 0px -40px 0px'
    }
  );

  revealElements.forEach((el) => revealObserver.observe(el));

  // Initialize Interactive Hero Background Particles
  initHeroParticles();

  // 2. Header Scroll Detector & Scroll to Top
  const siteHeader = document.querySelector('.site-header');
  const scrollTopBtn = document.querySelector('.scroll-top-btn');

  window.addEventListener('scroll', () => {
    if (window.scrollY > 20) {
      if (siteHeader) siteHeader.classList.add('scrolled');
    } else {
      if (siteHeader) siteHeader.classList.remove('scrolled');
    }

    if (window.scrollY > 300) {
      if (scrollTopBtn) scrollTopBtn.classList.add('show');
    } else {
      if (scrollTopBtn) scrollTopBtn.classList.remove('show');
    }
  });

  if (scrollTopBtn) {
    scrollTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // 3. Mobile Menu Drawer Toggle
  const menuToggle = document.querySelector('.menu-toggle');
  const mobileMenu = document.querySelector('.mobile-menu');
  const menuBackdrop = document.querySelector('.menu-backdrop');
  const closeMenuBtn = document.querySelector('.close-menu-btn');
  const hamburger = document.querySelector('.hamburger');

  function openMobileMenu() {
    if (mobileMenu) mobileMenu.classList.add('open');
    if (menuBackdrop) menuBackdrop.classList.add('open');
    if (hamburger) hamburger.classList.add('open');
  }

  function closeMobileMenu() {
    if (mobileMenu) mobileMenu.classList.remove('open');
    if (menuBackdrop) menuBackdrop.classList.remove('open');
    if (hamburger) hamburger.classList.remove('open');
  }

  if (menuToggle) {
    menuToggle.addEventListener('click', () => {
      if (mobileMenu && mobileMenu.classList.contains('open')) {
        closeMobileMenu();
      } else {
        openMobileMenu();
      }
    });
  }

  if (closeMenuBtn) closeMenuBtn.addEventListener('click', closeMobileMenu);
  if (menuBackdrop) menuBackdrop.addEventListener('click', closeMobileMenu);

  // Mobile Submenu Accordion Toggle
  const mobileAccToggle = document.querySelector('.mobile-accordion-toggle');
  if (mobileAccToggle) {
    mobileAccToggle.addEventListener('click', () => {
      const parentAcc = mobileAccToggle.closest('.mobile-nav-accordion');
      if (parentAcc) {
        parentAcc.classList.toggle('open');
      }
    });
  }

  // 4. Search Bar Toggle
  const searchBtn = document.querySelector('.search-toggle-btn');
  const searchForm = document.querySelector('.search-form');

  if (searchBtn && searchForm) {
    searchBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      searchForm.classList.toggle('open');
      if (searchForm.classList.contains('open')) {
        const input = searchForm.querySelector('.search-input');
        if (input) input.focus();
      }
    });

    document.addEventListener('click', (e) => {
      if (!searchForm.contains(e.target) && e.target !== searchBtn) {
        searchForm.classList.remove('open');
      }
    });
  }

  // 5. Progressive Number Counter Animation
  function initProgressiveCounters() {
    const counterElements = document.querySelectorAll('[data-count]');
    if (!counterElements.length) return;

    const counterObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseFloat(el.getAttribute('data-count'));
          const decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
          const prefix = el.getAttribute('data-prefix') || '';
          const suffix = el.getAttribute('data-suffix') || '';
          const duration = 1800; // ms
          const startTime = performance.now();

          function updateCounter(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // Ease out cubic
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const currentVal = (target * easeOut).toFixed(decimals);

            el.textContent = `${prefix}${currentVal}${suffix}`;

            if (progress < 1) {
              requestAnimationFrame(updateCounter);
            } else {
              el.textContent = `${prefix}${target.toFixed(decimals)}${suffix}`;
            }
          }

          requestAnimationFrame(updateCounter);
          observer.unobserve(el);
        }
      });
    }, {
      threshold: 0.2
    });

    counterElements.forEach((el) => counterObserver.observe(el));
  }

  initProgressiveCounters();

  // 6. Interactive Testimonials Slider Controller
  function initTestimonialsSlider() {
    const track = document.getElementById('testi-track');
    const prevBtn = document.getElementById('testi-prev');
    const nextBtn = document.getElementById('testi-next');
    const dotsContainer = document.getElementById('testi-dots');

    if (!track) return;

    const slides = track.querySelectorAll('.testimonial-slide');
    if (!slides.length) return;

    // Create pagination dots
    if (dotsContainer) {
      dotsContainer.innerHTML = '';
      slides.forEach((_, idx) => {
        const dot = document.createElement('span');
        dot.className = `slider-dot ${idx === 0 ? 'active' : ''}`;
        dot.setAttribute('data-slide-index', idx);
        dot.addEventListener('click', () => {
          scrollToSlide(idx);
        });
        dotsContainer.appendChild(dot);
      });
    }

    function getSlideStep() {
      const slide = slides[0];
      const style = window.getComputedStyle(slide);
      const gap = parseInt(window.getComputedStyle(track).gap || '20', 10);
      return slide.offsetWidth + gap;
    }

    function scrollToSlide(index) {
      const step = getSlideStep();
      track.scrollTo({
        left: index * step,
        behavior: 'smooth'
      });
    }

    if (prevBtn) {
      prevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const step = getSlideStep();
        track.scrollBy({
          left: -step,
          behavior: 'smooth'
        });
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const step = getSlideStep();
        track.scrollBy({
          left: step,
          behavior: 'smooth'
        });
      });
    }

    // Update active dot on scroll
    let scrollTimeout;
    track.addEventListener('scroll', () => {
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        if (!dotsContainer) return;
        const step = getSlideStep();
        const currentIndex = Math.round(track.scrollLeft / step);
        const dots = dotsContainer.querySelectorAll('.slider-dot');
        dots.forEach((d, idx) => {
          if (idx === currentIndex) {
            d.classList.add('active');
          } else {
            d.classList.remove('active');
          }
        });
      }, 60);
    });
  }

  initTestimonialsSlider();

  // 7. Accordions (Home & Page FAQs)
  document.querySelectorAll('.faq-header-btn, .accordion-header-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      const parent = btn.closest('.faq-accordion') || btn.closest('.product-accordion');
      if (parent) {
        const isOpen = parent.classList.contains('open');
        const container = parent.parentElement;
        if (container) {
          container.querySelectorAll('.faq-accordion, .product-accordion').forEach((item) => {
            if (item !== parent) item.classList.remove('open');
          });
        }
        parent.classList.toggle('open', !isOpen);
      }
    });
  });

  // 8. Copy Coupon Code Buttons
  document.querySelectorAll('[data-copy-coupon]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const code = btn.getAttribute('data-copy-coupon') || 'MURLI10';
      if (navigator.clipboard) {
        navigator.clipboard.writeText(code).then(() => {
          if (typeof showToast === 'function') {
            showToast(`Coupon "${code}" copied to clipboard!`, 'success');
          }
        });
      } else {
        if (typeof showToast === 'function') {
          showToast(`Use coupon code: ${code}`, 'info');
        }
      }
    });
  });

  // 9. Home / Contact Form Instant AJAX Submission
  const contactForms = document.querySelectorAll('#home-contact-form, #contact-page-form');
  contactForms.forEach((form) => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn ? submitBtn.innerHTML : 'Submit';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Sending...';
      }

      const formData = new FormData(form);

      try {
        const res = await fetch('/api/contact.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();

        if (data.success) {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Inquiry submitted successfully! We will connect within 24 hours.', 'success');
          }
          form.reset();
        } else {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Failed to submit inquiry. Please try again.', 'error');
          }
        }
      } catch (err) {
        if (typeof showToast === 'function') {
          showToast('Inquiry sent! Our studio team will get back to you shortly.', 'success');
        }
        form.reset();
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
      }
    });
  });

  // 10. Global Quick View Modal Handler
  initGlobalQuickView();
});

function initGlobalQuickView() {
  document.addEventListener('click', (e) => {
    const quickViewBtn = e.target.closest('.quick-view-btn');
    if (quickViewBtn) {
      e.preventDefault();
      e.stopPropagation();
      const productJson = quickViewBtn.getAttribute('data-product-json');
      if (productJson) {
        try {
          const product = JSON.parse(productJson);
          openQuickViewModal(product);
        } catch (err) {
          console.error('Error parsing product JSON:', err);
        }
      }
    }
  });

  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-backdrop') || e.target.closest('.modal-close')) {
      closeQuickViewModal();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeQuickViewModal();
    }
  });
}

function openQuickViewModal(product) {
  let backdrop = document.getElementById('quick-view-modal-backdrop');
  if (!backdrop) {
    backdrop = document.createElement('div');
    backdrop.id = 'quick-view-modal-backdrop';
    backdrop.className = 'modal-backdrop';
    document.body.appendChild(backdrop);
  }

  const mrpVal = product.mrp || Math.round(product.price * 1.3);
  const ratingVal = product.rating || 4.8;
  const reviewsCountVal = product.reviewsCount || 15;
  const artistVal = product.artist || 'Master Craftsman Murli Prasad';
  const discountPercent = Math.round(((mrpVal - product.price) / mrpVal) * 100);

  backdrop.innerHTML = `
    <div class="modal-content" onclick="event.stopPropagation()">
      <button class="modal-close" aria-label="Close modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
      <div class="modal-grid">
        <div class="modal-image-container">
          <img src="${product.images[0]}" alt="${product.name}" class="modal-image">
        </div>
        <div class="modal-details">
          <span class="modal-category">${product.category}</span>
          <h2 class="modal-name">${product.name}</h2>

          <div class="modal-rating-row">
            <span class="card-stars">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" class="star-active-svg"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
              ${ratingVal}
            </span>
            <span class="card-rating-count">(${reviewsCountVal} customer reviews)</span>
            <span class="modal-artist">• By ${artistVal}</span>
          </div>

          <div class="modal-price-container">
            <span class="modal-price">₹${Number(product.price).toLocaleString('en-IN')}</span>
            <span class="modal-mrp">₹${Number(mrpVal).toLocaleString('en-IN')}</span>
            <span class="modal-discount">${discountPercent}% OFF</span>
          </div>

          <p class="modal-description">${product.description}</p>

          <div class="modal-specs">
            ${product.code ? `<p><strong>Product Code:</strong> <span style="font-weight: 700; color: var(--gold-accent, #c99846);">${product.code}</span></p>` : ''}
            <p><strong>Dimensions:</strong> ${product.size || '18 x 18 inches'}</p>
            <p><strong>Artisan Materials:</strong> ${product.material || 'Clay Relief, MDF Board, Shimmer Mirrors'}</p>
            <p><strong>Craft Origin:</strong> ${product.origin || 'Rohtak, Haryana, India'}</p>
            <p>
              <strong>Status:</strong>
              ${product.stock > 0 
                ? `<span class="modal-in-stock">In Stock (${product.stock} units)</span>` 
                : `<span class="modal-out-stock">Sold Out</span>`}
            </p>
          </div>

          <div class="modal-actions">
            <button 
              class="modal-add-btn" 
              ${product.stock === 0 ? 'disabled' : ''} 
              onclick="addToCart('${product.id}', 1); closeQuickViewModal();"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
              ${product.stock === 0 ? 'Currently Sold Out' : 'Add to Cart'}
            </button>
            <a href="/product.php?slug=${product.slug}" class="modal-details-link">
              View Full Product Page &rarr;
            </a>
          </div>
        </div>
      </div>
    </div>
  `;

  backdrop.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeQuickViewModal() {
  const backdrop = document.getElementById('quick-view-modal-backdrop');
  if (backdrop) {
    backdrop.classList.remove('open');
  }
  document.body.style.overflow = '';
}

// =======================================================
// INTERACTIVE DARK EMERALD & GOLD HERO PARTICLES ENGINE
// =======================================================
function initHeroParticles() {
  const canvas = document.getElementById('hero-particles-canvas');
  const hero = document.getElementById('hero-section') || document.querySelector('.hero');
  if (!canvas || !hero) return;

  const ctx = canvas.getContext('2d');
  let width, height;
  let particles = [];
  let mouse = { x: null, y: null, radius: 130 };
  let animationFrameId;

  const particleColors = [
    { r: 20, g: 45, b: 30, a: 0.65 },   // Dark Forest Charcoal-Emerald
    { r: 30, g: 166, b: 89, a: 0.7 },   // Vibrant Emerald
    { r: 197, g: 160, b: 89, a: 0.8 },  // Sacred Gold Mote
    { r: 45, g: 125, b: 124, a: 0.65 }, // Peacock Teal
    { r: 16, g: 32, b: 22, a: 0.75 }    // Deep Obsidian Node
  ];

  function resize() {
    const rect = hero.getBoundingClientRect();
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    width = rect.width;
    height = rect.height;

    canvas.width = width * dpr;
    canvas.height = height * dpr;
    canvas.style.width = width + 'px';
    canvas.style.height = height + 'px';
    ctx.scale(dpr, dpr);

    createParticles();
  }

  function createParticles() {
    particles = [];
    const count = Math.min(Math.floor((width * height) / 16000), 55);
    for (let i = 0; i < count; i++) {
      const color = particleColors[Math.floor(Math.random() * particleColors.length)];
      particles.push({
        x: Math.random() * width,
        y: Math.random() * height,
        vx: (Math.random() - 0.5) * 0.65,
        vy: (Math.random() - 0.5) * 0.65,
        radius: Math.random() * 2.5 + 1.2,
        baseRadius: Math.random() * 2.5 + 1.2,
        color: color,
        pulseAngle: Math.random() * Math.PI * 2,
        pulseSpeed: 0.02 + Math.random() * 0.03
      });
    }
  }

  hero.addEventListener('mousemove', (e) => {
    const rect = hero.getBoundingClientRect();
    mouse.x = e.clientX - rect.left;
    mouse.y = e.clientY - rect.top;
  });

  hero.addEventListener('mouseleave', () => {
    mouse.x = null;
    mouse.y = null;
  });

  function draw() {
    ctx.clearRect(0, 0, width, height);

    // Update & Draw Particles
    for (let i = 0; i < particles.length; i++) {
      const p = particles[i];

      // Float position
      p.x += p.vx;
      p.y += p.vy;

      // Pulse size & brightness
      p.pulseAngle += p.pulseSpeed;
      const currentRadius = p.baseRadius + Math.sin(p.pulseAngle) * 0.5;

      // Bounce off borders
      if (p.x < 0 || p.x > width) p.vx *= -1;
      if (p.y < 0 || p.y > height) p.vy *= -1;

      // Mouse interactive force
      if (mouse.x !== null && mouse.y !== null) {
        const dx = mouse.x - p.x;
        const dy = mouse.y - p.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < mouse.radius) {
          const force = (mouse.radius - dist) / mouse.radius;
          p.x -= (dx / dist) * force * 2.5;
          p.y -= (dy / dist) * force * 2.5;
        }
      }

      // Draw particle circle with soft glow
      ctx.beginPath();
      ctx.arc(p.x, p.y, currentRadius, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(${p.color.r}, ${p.color.g}, ${p.color.b}, ${p.color.a})`;
      ctx.fill();

      // Connecting lines between nearby particles
      for (let j = i + 1; j < particles.length; j++) {
        const p2 = particles[j];
        const dx = p.x - p2.x;
        const dy = p.y - p2.y;
        const dist = Math.sqrt(dx * dx + dy * dy);

        if (dist < 110) {
          const lineAlpha = (1 - dist / 110) * 0.22;
          ctx.beginPath();
          ctx.moveTo(p.x, p.y);
          ctx.lineTo(p2.x, p2.y);
          ctx.strokeStyle = `rgba(20, 48, 30, ${lineAlpha})`;
          ctx.lineWidth = 0.85;
          ctx.stroke();
        }
      }
    }

    animationFrameId = requestAnimationFrame(draw);
  }

  window.addEventListener('resize', () => {
    cancelAnimationFrame(animationFrameId);
    resize();
    draw();
  });

  resize();
  draw();
}
