// Shop Filtering, Sorting & Range Slider Controls for Murli Creations

document.addEventListener('DOMContentLoaded', () => {
  initShopControls();
});

function initShopControls() {
  const priceSlider = document.getElementById('price-range-slider');
  const priceDisplay = document.getElementById('price-range-display');
  const sortSelect = document.getElementById('sort-select');

  if (priceSlider && priceDisplay) {
    priceSlider.addEventListener('input', (e) => {
      const val = Number(e.target.value);
      priceDisplay.textContent = `₹${val.toLocaleString('en-IN')}`;
    });

    priceSlider.addEventListener('change', () => {
      applyFilters();
    });
  }

  if (sortSelect) {
    sortSelect.addEventListener('change', () => {
      applyFilters();
    });
  }
}

function applyFilters() {
  const priceSlider = document.getElementById('price-range-slider');
  const sortSelect = document.getElementById('sort-select');
  
  const url = new URL(window.location.href);
  if (priceSlider) {
    url.searchParams.set('maxPrice', priceSlider.value);
  }
  if (sortSelect) {
    url.searchParams.set('sort', sortSelect.value);
  }
  window.location.href = url.toString();
}
