// Cart & Cart Drawer Client Logic for Murli Creations

document.addEventListener('DOMContentLoaded', () => {
  initCartEvents();
});

function initCartEvents() {
  // Drawer open / close triggers
  const cartTrigger = document.querySelector('.cart-trigger');
  const cartDrawerBackdrop = document.querySelector('.cart-drawer-backdrop');
  const cartDrawerPanel = document.querySelector('.cart-drawer-panel');
  const cartDrawerClose = document.querySelector('.cart-drawer-close');

  if (cartTrigger) {
    cartTrigger.addEventListener('click', (e) => {
      e.preventDefault();
      openCartDrawer();
    });
  }

  if (cartDrawerClose) {
    cartDrawerClose.addEventListener('click', closeCartDrawer);
  }

  if (cartDrawerBackdrop) {
    cartDrawerBackdrop.addEventListener('click', closeCartDrawer);
  }

  // Quick Add Buttons on Product Cards
  document.addEventListener('click', (e) => {
    const quickAddBtn = e.target.closest('.quick-add-btn');
    if (quickAddBtn && !quickAddBtn.disabled) {
      e.preventDefault();
      e.stopPropagation();
      const productId = quickAddBtn.getAttribute('data-product-id');
      if (productId) {
        addToCart(productId, 1);
      }
    }
  });

  // Quantity modifier & remove buttons inside cart drawer & cart page
  document.addEventListener('click', (e) => {
    // Drawer quantity buttons
    const drawerQtyBtn = e.target.closest('.cart-drawer-qty-btn');
    if (drawerQtyBtn) {
      const productId = drawerQtyBtn.getAttribute('data-product-id');
      const action = drawerQtyBtn.getAttribute('data-action');
      const currentQty = parseInt(drawerQtyBtn.getAttribute('data-qty') || '1', 10);
      const newQty = action === 'increase' ? currentQty + 1 : currentQty - 1;
      updateCartQuantity(productId, newQty);
    }

    // Drawer remove button
    const drawerRemoveBtn = e.target.closest('.cart-drawer-remove-btn');
    if (drawerRemoveBtn) {
      const productId = drawerRemoveBtn.getAttribute('data-product-id');
      removeFromCart(productId);
    }

    // Cart page quantity buttons
    const cartPageQtyBtn = e.target.closest('.cart-page-qty-btn');
    if (cartPageQtyBtn) {
      const productId = cartPageQtyBtn.getAttribute('data-product-id');
      const action = cartPageQtyBtn.getAttribute('data-action');
      const currentQty = parseInt(cartPageQtyBtn.getAttribute('data-qty') || '1', 10);
      const newQty = action === 'increase' ? currentQty + 1 : currentQty - 1;
      updateCartQuantity(productId, newQty, true);
    }

    // Cart page remove button
    const cartPageRemoveBtn = e.target.closest('.cart-page-remove-btn');
    if (cartPageRemoveBtn) {
      const productId = cartPageRemoveBtn.getAttribute('data-product-id');
      removeFromCart(productId, true);
    }
  });
}

function openCartDrawer() {
  const backdrop = document.querySelector('.cart-drawer-backdrop');
  const panel = document.querySelector('.cart-drawer-panel');
  if (backdrop) backdrop.classList.add('open');
  if (panel) panel.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeCartDrawer() {
  const backdrop = document.querySelector('.cart-drawer-backdrop');
  const panel = document.querySelector('.cart-drawer-panel');
  if (backdrop) backdrop.classList.remove('open');
  if (panel) panel.classList.remove('open');
  document.body.style.overflow = '';
}

async function addToCart(productId, quantity = 1, showDrawer = true) {
  try {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('productId', productId);
    formData.append('quantity', quantity);

    const response = await fetch('/api/cart.php', {
      method: 'POST',
      body: formData
    });
    const data = await response.json();

    if (data.success) {
      updateCartUI(data);
      if (typeof showToast === 'function') {
        showToast(data.message || 'Added to cart!', 'success');
      }
      if (showDrawer) {
        openCartDrawer();
      }
    } else {
      if (typeof showToast === 'function') {
        showToast(data.message || 'Failed to add item', 'error');
      }
    }
  } catch (err) {
    console.error('Add to cart error:', err);
  }
}

async function updateCartQuantity(productId, quantity, reloadPage = false) {
  try {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('productId', productId);
    formData.append('quantity', quantity);

    const response = await fetch('/api/cart.php', {
      method: 'POST',
      body: formData
    });
    const data = await response.json();

    if (data.success) {
      if (reloadPage) {
        window.location.reload();
      } else {
        updateCartUI(data);
      }
    }
  } catch (err) {
    console.error('Update cart error:', err);
  }
}

async function removeFromCart(productId, reloadPage = false) {
  try {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('productId', productId);

    const response = await fetch('/api/cart.php', {
      method: 'POST',
      body: formData
    });
    const data = await response.json();

    if (data.success) {
      if (reloadPage) {
        window.location.reload();
      } else {
        updateCartUI(data);
        if (typeof showToast === 'function') {
          showToast('Removed item from cart', 'info');
        }
      }
    }
  } catch (err) {
    console.error('Remove from cart error:', err);
  }
}

function updateCartUI(data) {
  // 1. Update Header Badge
  const badges = document.querySelectorAll('.cart-badge');
  const count = data.cartCount || 0;

  badges.forEach((badge) => {
    badge.textContent = count;
    badge.style.display = count > 0 ? 'flex' : 'none';
  });

  // 2. Update Drawer Content if present
  const drawerTitle = document.querySelector('.cart-drawer-title');
  if (drawerTitle) {
    drawerTitle.textContent = `Your Gallery Cart (${(data.cart || []).length})`;
  }

  const drawerContent = document.querySelector('.cart-drawer-content');
  const drawerFooter = document.querySelector('.cart-drawer-footer');

  if (drawerContent) {
    if (!data.cart || data.cart.length === 0) {
      drawerContent.innerHTML = `
        <div class="cart-drawer-empty">
          <div class="cart-drawer-empty-icon">🎨</div>
          <p class="cart-drawer-empty-text">Your cart is empty.</p>
          <p class="cart-drawer-empty-subtext">Explore our handcrafted pieces to bring life to your spaces.</p>
          <a href="/shop.php" onclick="closeCartDrawer()" class="cart-drawer-shop-btn">Browse Shop</a>
        </div>
      `;
      if (drawerFooter) drawerFooter.style.display = 'none';
    } else {
      let itemsHtml = '<div class="cart-drawer-list">';
      data.cart.forEach((item) => {
        const p = item.product;
        itemsHtml += `
          <div class="cart-drawer-item">
            <div class="cart-drawer-item-img">
              <img src="${p.images[0]}" alt="${p.name}" width="80" height="80">
            </div>
            <div class="cart-drawer-item-details">
              <div class="cart-drawer-item-meta">
                <span class="cart-drawer-item-category">${p.category}</span>
                <h4 class="cart-drawer-item-name">${p.name}</h4>
              </div>
              <div class="cart-drawer-item-price">₹${Number(p.price).toLocaleString('en-IN')}</div>
              <div class="cart-drawer-controls">
                <div class="cart-drawer-qty-picker">
                  <button class="cart-drawer-qty-btn" data-product-id="${p.id}" data-action="decrease" data-qty="${item.quantity}">-</button>
                  <span class="cart-drawer-qty-val">${item.quantity}</span>
                  <button class="cart-drawer-qty-btn" data-product-id="${p.id}" data-action="increase" data-qty="${item.quantity}">+</button>
                </div>
                <button class="cart-drawer-remove-btn" data-product-id="${p.id}">Remove</button>
              </div>
            </div>
          </div>
        `;
      });
      itemsHtml += '</div>';
      drawerContent.innerHTML = itemsHtml;

      if (drawerFooter) {
        drawerFooter.style.display = 'block';
        const subtotalVal = drawerFooter.querySelector('.cart-drawer-subtotal-val');
        if (subtotalVal) {
          subtotalVal.textContent = `₹${Number(data.subtotal || 0).toLocaleString('en-IN')}`;
        }
      }
    }
  }
}
