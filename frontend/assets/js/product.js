// Product Detail Page Interactions & Review Submissions

document.addEventListener('DOMContentLoaded', () => {
  initProductGallery();
  initProductLightbox();
  initQuantityPicker();
  initProductAccordions();
  initReviewForm();
});

let currentLightboxIndex = 0;

function initProductGallery() {
  const thumbnailBtns = document.querySelectorAll('.thumbnail-btn');
  const mainMediaContainer = document.querySelector('.main-media-container');

  thumbnailBtns.forEach((btn, index) => {
    btn.addEventListener('click', () => {
      thumbnailBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const mediaType = btn.getAttribute('data-type');
      const mediaSrc = btn.getAttribute('data-src');

      if (!mainMediaContainer) return;

      if (mediaType === 'video') {
        const videoHtml = (mediaSrc.includes('youtube.com') || mediaSrc.includes('youtu.be'))
          ? `<iframe src="${mediaSrc.replace('watch?v=', 'embed/')}" title="Product Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="main-media-video" style="width: 100%; height: 100%; border: none;"></iframe>`
          : `<video src="${mediaSrc}" controls autoplay loop muted class="main-media-video"></video>`;
        
        mainMediaContainer.innerHTML = videoHtml;
      } else {
        currentLightboxIndex = index;
        mainMediaContainer.innerHTML = `
          <img
            id="main-product-img"
            src="${mediaSrc}"
            alt="Product Image Preview"
            class="main-media-image"
          />
          <button type="button" class="main-media-zoom-btn" id="main-media-zoom-trigger" aria-label="View full picture">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 3 21 3 21 9"></polyline>
              <polyline points="9 21 3 21 3 15"></polyline>
              <line x1="21" y1="3" x2="14" y2="10"></line>
              <line x1="3" y1="21" x2="10" y2="14"></line>
            </svg>
            <span>Full View</span>
          </button>
        `;
        
        const newZoomTrigger = document.getElementById('main-media-zoom-trigger');
        if (newZoomTrigger) {
          newZoomTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            openLightbox(index);
          });
        }
      }
    });
  });

  if (mainMediaContainer) {
    mainMediaContainer.addEventListener('click', (e) => {
      if (e.target.closest('#main-media-zoom-trigger')) return;
      const activeImg = mainMediaContainer.querySelector('.main-media-image');
      if (activeImg) {
        openLightbox(currentLightboxIndex);
      }
    });

    mainMediaContainer.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openLightbox(currentLightboxIndex);
      }
    });
  }

  const zoomBtn = document.getElementById('main-media-zoom-trigger');
  if (zoomBtn) {
    zoomBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      openLightbox(currentLightboxIndex);
    });
  }
}

function initProductLightbox() {
  const lightbox = document.getElementById('product-lightbox');
  const closeBtn = document.getElementById('lightbox-close');
  const prevBtn = document.getElementById('lightbox-prev');
  const nextBtn = document.getElementById('lightbox-next');

  if (!lightbox) return;

  if (closeBtn) {
    closeBtn.addEventListener('click', closeLightbox);
  }

  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox || e.target.classList.contains('lightbox-content-wrap')) {
      closeLightbox();
    }
  });

  if (prevBtn) {
    prevBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const images = window.PRODUCT_IMAGES || [];
      if (images.length <= 1) return;
      currentLightboxIndex = (currentLightboxIndex - 1 + images.length) % images.length;
      updateLightboxImage(currentLightboxIndex);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const images = window.PRODUCT_IMAGES || [];
      if (images.length <= 1) return;
      currentLightboxIndex = (currentLightboxIndex + 1) % images.length;
      updateLightboxImage(currentLightboxIndex);
    });
  }

  document.addEventListener('keydown', (e) => {
    if (!lightbox.classList.contains('active')) return;
    if (e.key === 'Escape') {
      closeLightbox();
    } else if (e.key === 'ArrowLeft') {
      const images = window.PRODUCT_IMAGES || [];
      if (images.length > 1) {
        currentLightboxIndex = (currentLightboxIndex - 1 + images.length) % images.length;
        updateLightboxImage(currentLightboxIndex);
      }
    } else if (e.key === 'ArrowRight') {
      const images = window.PRODUCT_IMAGES || [];
      if (images.length > 1) {
        currentLightboxIndex = (currentLightboxIndex + 1) % images.length;
        updateLightboxImage(currentLightboxIndex);
      }
    }
  });
}

function openLightbox(index = 0) {
  const lightbox = document.getElementById('product-lightbox');
  if (!lightbox) return;
  const images = window.PRODUCT_IMAGES || [];
  if (!images.length) return;

  currentLightboxIndex = Math.max(0, Math.min(index, images.length - 1));
  updateLightboxImage(currentLightboxIndex);

  lightbox.classList.add('active');
  lightbox.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  const lightbox = document.getElementById('product-lightbox');
  if (!lightbox) return;
  lightbox.classList.remove('active');
  lightbox.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

function updateLightboxImage(index) {
  const lightboxImg = document.getElementById('lightbox-img');
  const counter = document.getElementById('lightbox-counter');
  const images = window.PRODUCT_IMAGES || [];
  if (!images.length || !lightboxImg) return;

  const src = images[index] || images[0];
  lightboxImg.src = src;

  if (counter) {
    counter.textContent = `${index + 1} of ${images.length}`;
  }

  const thumbnailBtns = document.querySelectorAll('.thumbnail-btn');
  if (thumbnailBtns[index]) {
    thumbnailBtns.forEach(b => b.classList.remove('active'));
    thumbnailBtns[index].classList.add('active');
    const mainImg = document.getElementById('main-product-img');
    if (mainImg) {
      mainImg.src = src;
    }
  }
}

function initQuantityPicker() {
  const qtyVal = document.getElementById('purchase-quantity-val');
  const decBtn = document.getElementById('purchase-qty-dec');
  const incBtn = document.getElementById('purchase-qty-inc');
  const maxStock = parseInt(qtyVal?.getAttribute('data-max-stock') || '99', 10);
  const addToCartBtn = document.getElementById('product-page-add-btn');

  if (decBtn && qtyVal) {
    decBtn.addEventListener('click', () => {
      let current = parseInt(qtyVal.textContent || '1', 10);
      if (current > 1) {
        current -= 1;
        qtyVal.textContent = current;
      }
    });
  }

  if (incBtn && qtyVal) {
    incBtn.addEventListener('click', () => {
      let current = parseInt(qtyVal.textContent || '1', 10);
      if (current < maxStock) {
        current += 1;
        qtyVal.textContent = current;
      }
    });
  }

  if (addToCartBtn) {
    addToCartBtn.addEventListener('click', () => {
      const productId = addToCartBtn.getAttribute('data-product-id');
      const qty = parseInt(qtyVal?.textContent || '1', 10);
      if (productId && typeof addToCart === 'function') {
        addToCart(productId, qty);
      }
    });
  }
}

function initProductAccordions() {
  const accordionHeaders = document.querySelectorAll('.product-accordion .accordion-header-btn');

  accordionHeaders.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const parent = btn.closest('.product-accordion');
      if (!parent) return;

      const container = parent.closest('.product-accordions');
      const wasOpen = parent.classList.contains('open');

      if (container) {
        container.querySelectorAll('.product-accordion').forEach((item) => {
          if (item !== parent) {
            item.classList.remove('open');
          }
        });
      }

      parent.classList.toggle('open', !wasOpen);
    });
  });

  // Smooth scroll for reviews anchor link
  const ratingLink = document.querySelector('.product-rating-link');
  if (ratingLink) {
    ratingLink.addEventListener('click', (e) => {
      e.preventDefault();
      const target = document.getElementById('reviews-section');
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }
}

function initReviewForm() {
  const starBtns = document.querySelectorAll('.star-selector .star-btn');
  const ratingInput = document.getElementById('review-rating-input');
  const reviewForm = document.getElementById('art-review-form');

  starBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      const rating = parseInt(btn.getAttribute('data-rating') || '5', 10);
      if (ratingInput) ratingInput.value = rating;

      starBtns.forEach((b) => {
        const bRating = parseInt(b.getAttribute('data-rating') || '5', 10);
        if (bRating <= rating) {
          b.classList.add('active');
          b.classList.remove('inactive');
        } else {
          b.classList.remove('active');
          b.classList.add('inactive');
        }
      });
    });
  });

  if (reviewForm) {
    reviewForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const productId = reviewForm.getAttribute('data-product-id');
      const name = document.getElementById('reviewerName')?.value;
      const comment = document.getElementById('reviewerComment')?.value;
      const rating = ratingInput?.value || 5;

      if (!name || !comment) {
        if (typeof showToast === 'function') {
          showToast('Please fill all review fields', 'error');
        }
        return;
      }

      try {
        const formData = new FormData();
        formData.append('productId', productId);
        formData.append('userName', name);
        formData.append('rating', rating);
        formData.append('comment', comment);

        const response = await fetch('/api/reviews.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();

        if (data.success) {
          if (typeof showToast === 'function') {
            showToast('Review submitted successfully!', 'success');
          }
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Failed to submit review', 'error');
          }
        }
      } catch (err) {
        console.error('Error submitting review:', err);
      }
    });
  }
}
