// Product Detail Page Interactions & Review Submissions

document.addEventListener('DOMContentLoaded', () => {
  initProductGallery();
  initQuantityPicker();
  initProductAccordions();
  initReviewForm();
});

function initProductGallery() {
  const thumbnailBtns = document.querySelectorAll('.thumbnail-btn');
  const mainMediaContainer = document.querySelector('.main-media-container');

  thumbnailBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      thumbnailBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const mediaType = btn.getAttribute('data-type');
      const mediaSrc = btn.getAttribute('data-src');

      if (!mainMediaContainer) return;

      if (mediaType === 'video') {
        if (mediaSrc.includes('youtube.com') || mediaSrc.includes('youtu.be')) {
          const embedSrc = mediaSrc.replace('watch?v=', 'embed/');
          mainMediaContainer.innerHTML = `
            <iframe
              src="${embedSrc}"
              title="Product Video"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
              class="main-media-video"
              style="width: 100%; height: 100%; border: none;"
            ></iframe>
          `;
        } else {
          mainMediaContainer.innerHTML = `
            <video
              src="${mediaSrc}"
              controls
              autoplay
              loop
              muted
              class="main-media-video"
            ></video>
          `;
        }
      } else {
        mainMediaContainer.innerHTML = `
          <img
            src="${mediaSrc}"
            alt="Product Image Preview"
            class="main-media-image"
          />
        `;
      }
    });
  });
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
  const accordionHeaders = document.querySelectorAll('.accordion-header-btn');

  accordionHeaders.forEach((btn) => {
    btn.addEventListener('click', () => {
      const parent = btn.closest('.product-accordion');
      if (parent) {
        parent.classList.toggle('open');
      }
    });
  });
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
