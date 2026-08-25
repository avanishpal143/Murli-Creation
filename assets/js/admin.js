// Complete Studio Administration & CMS Client Logic for Murli Creations

document.addEventListener('DOMContentLoaded', () => {
  initAdminPortal();
});

function switchAdminTab(tabTarget) {
  const tabBtns = document.querySelectorAll('.admin-tab-btn');
  const tabPanels = document.querySelectorAll('.admin-tab-content');

  tabBtns.forEach(b => {
    if (b.getAttribute('data-tab') === tabTarget) {
      b.classList.add('active');
    } else {
      b.classList.remove('active');
    }
  });

  tabPanels.forEach(panel => {
    if (panel.id === `tab-${tabTarget}`) {
      panel.style.display = 'block';
    } else {
      panel.style.display = 'none';
    }
  });
}

function initAdminPortal() {
  // 1. Tab Switching
  const tabBtns = document.querySelectorAll('.admin-tab-btn');
  tabBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      const tabTarget = btn.getAttribute('data-tab');
      switchAdminTab(tabTarget);
    });
  });

  // 2. Login Form Submission
  const loginForm = document.getElementById('admin-login-form');
  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const username = document.getElementById('admin-username')?.value;
      const password = document.getElementById('admin-password')?.value;

      try {
        const formData = new FormData();
        formData.append('action', 'login');
        formData.append('username', username);
        formData.append('password', password);

        const response = await fetch('/api/admin.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();

        if (data.success) {
          if (typeof showToast === 'function') {
            showToast('Access Granted. Welcome back, Administrator.', 'success');
          }
          setTimeout(() => window.location.reload(), 600);
        } else {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Incorrect Username or Password.', 'error');
          }
        }
      } catch (err) {
        console.error('Login error:', err);
      }
    });
  }

  // 3. Logout Handler
  const logoutBtn = document.querySelector('.portal-logout-btn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      try {
        const formData = new FormData();
        formData.append('action', 'logout');
        await fetch('/api/admin.php', { method: 'POST', body: formData });
        window.location.reload();
      } catch (err) {
        console.error('Logout error:', err);
      }
    });
  }

  // 4. Product Add / Edit / Delete Handlers
  const openAddBtn = document.getElementById('open-add-product-btn');
  const addProductSection = document.getElementById('add-product-section');
  const cancelAddBtn = document.getElementById('cancel-add-product-btn');

  if (openAddBtn && addProductSection) {
    openAddBtn.addEventListener('click', () => {
      addProductSection.style.display = 'block';
      openAddBtn.style.display = 'none';
    });
  }

  if (cancelAddBtn && addProductSection && openAddBtn) {
    cancelAddBtn.addEventListener('click', () => {
      addProductSection.style.display = 'none';
      openAddBtn.style.display = 'inline-block';
    });
  }

  const addProductForm = document.getElementById('add-product-form');
  if (addProductForm) {
    addProductForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(addProductForm);
      formData.append('action', 'add_product');

      try {
        const response = await fetch('/api/admin.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();

        if (data.success) {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Product created successfully!', 'success');
          }
          setTimeout(() => window.location.reload(), 900);
        } else {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Error creating product', 'error');
          }
        }
      } catch (err) {
        console.error('Add product error:', err);
      }
    });
  }

  const editProductSection = document.getElementById('edit-product-section');
  const editProductForm = document.getElementById('edit-product-form');
  const cancelEditBtn = document.getElementById('cancel-edit-product-btn');

  document.addEventListener('click', (e) => {
    const editBtn = e.target.closest('.admin-edit-btn');
    if (editBtn) {
      const productJson = editBtn.getAttribute('data-product');
      if (productJson && editProductSection && editProductForm) {
        try {
          const product = JSON.parse(productJson);
          populateEditForm(product);
          editProductSection.style.display = 'block';
          editProductSection.scrollIntoView({ behavior: 'smooth' });
        } catch (err) {
          console.error('Error opening edit form:', err);
        }
      }
    }
  });

  if (cancelEditBtn && editProductSection) {
    cancelEditBtn.addEventListener('click', () => {
      editProductSection.style.display = 'none';
    });
  }

  if (editProductForm) {
    editProductForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(editProductForm);
      formData.append('action', 'edit_product');

      try {
        const response = await fetch('/api/admin.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();

        if (data.success) {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Product updated successfully!', 'success');
          }
          setTimeout(() => window.location.reload(), 900);
        } else {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Error updating product', 'error');
          }
        }
      } catch (err) {
        console.error('Edit product error:', err);
      }
    });
  }

  document.addEventListener('click', async (e) => {
    const deleteBtn = e.target.closest('.admin-delete-btn');
    if (deleteBtn && !deleteBtn.classList.contains('delete-coupon-btn') && !deleteBtn.classList.contains('delete-review-btn')) {
      const productId = deleteBtn.getAttribute('data-product-id');
      const productName = deleteBtn.getAttribute('data-product-name');

      if (confirm(`Are you sure you want to delete "${productName}" from your catalog?`)) {
        try {
          const formData = new FormData();
          formData.append('action', 'delete_product');
          formData.append('productId', productId);

          const response = await fetch('/api/admin.php', {
            method: 'POST',
            body: formData
          });
          const data = await response.json();

          if (data.success) {
            if (typeof showToast === 'function') {
              showToast(data.message || 'Product deleted', 'info');
            }
            setTimeout(() => window.location.reload(), 800);
          }
        } catch (err) {
          console.error('Delete error:', err);
        }
      }
    }
  });

  function populateEditForm(p) {
    document.getElementById('edit-id').value = p.id;
    document.getElementById('edit-name').value = p.name;
    const codeEl = document.getElementById('edit-code');
    if (codeEl) codeEl.value = p.code || '';
    document.getElementById('edit-category').value = p.category;
    document.getElementById('edit-price').value = p.price;
    document.getElementById('edit-mrp').value = p.mrp || Math.round(p.price * 1.3);
    document.getElementById('edit-stock').value = p.stock;
    document.getElementById('edit-size').value = p.size || '';
    document.getElementById('edit-material').value = p.material || '';
    document.getElementById('edit-origin').value = p.origin || '';
    document.getElementById('edit-artist').value = p.artist || '';
    document.getElementById('edit-rating').value = p.rating || '4.8';
    document.getElementById('edit-reviews-count').value = p.reviewsCount || '15';
    document.getElementById('edit-images').value = (p.images || []).join(', ');
    document.getElementById('edit-video').value = p.video || '';
    document.getElementById('edit-folklore').value = p.folklore || '';
    document.getElementById('edit-description').value = p.description || '';
  }

  // 4.5 Live Search Listeners
  const productSearch = document.getElementById('admin-product-search');
  if (productSearch) {
    productSearch.addEventListener('input', () => {
      const q = productSearch.value.toLowerCase().trim();
      const rows = document.querySelectorAll('#tab-products .admin-table-row');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  const reviewSearch = document.getElementById('admin-review-search');
  if (reviewSearch) {
    reviewSearch.addEventListener('input', () => {
      const q = reviewSearch.value.toLowerCase().trim();
      const rows = document.querySelectorAll('#tab-reviews .admin-table tbody tr');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  // 5. Order Management & Status Updates
  const orderSearch = document.getElementById('admin-order-search');
  const orderFilter = document.getElementById('admin-order-filter');

  function filterOrders() {
    const q = (orderSearch?.value || '').toLowerCase().trim();
    const selected = orderFilter?.value || 'All';
    const rows = document.querySelectorAll('.order-row');

    rows.forEach(row => {
      const rowStatus = row.getAttribute('data-order-status') || '';
      const text = row.textContent.toLowerCase();
      const matchesStatus = (selected === 'All' || rowStatus === selected);
      const matchesQuery = (!q || text.includes(q));

      if (matchesStatus && matchesQuery) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  if (orderSearch) orderSearch.addEventListener('input', filterOrders);
  if (orderFilter) orderFilter.addEventListener('change', filterOrders);

  document.addEventListener('change', async (e) => {
    const statusSelect = e.target.closest('.order-status-select');
    if (statusSelect) {
      const orderId = statusSelect.getAttribute('data-order-id');
      const newStatus = statusSelect.value;

      try {
        const formData = new FormData();
        formData.append('action', 'update_order_status');
        formData.append('orderId', orderId);
        formData.append('status', newStatus);

        const response = await fetch('/api/orders.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();

        if (data.success) {
          if (typeof showToast === 'function') {
            showToast(`Order ${orderId} updated to ${newStatus}`, 'success');
          }
          setTimeout(() => window.location.reload(), 800);
        }
      } catch (err) {
        console.error('Order status update error:', err);
      }
    }
  });

  // 6. Generic AJAX Form Submit Helper for CMS Settings
  function bindAjaxForm(formId, successMsg) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(form);

      try {
        const response = await fetch('/api/admin.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();

        if (data.success) {
          if (typeof showToast === 'function') {
            showToast(data.message || successMsg, 'success');
          }
          setTimeout(() => window.location.reload(), 800);
        } else {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Failed to save changes', 'error');
          }
        }
      } catch (err) {
        console.error(`Form submit error (${formId}):`, err);
      }
    });
  }

  bindAjaxForm('announcement-settings-form', 'Announcement banner updated!');
  bindAjaxForm('festive-card-form', 'Festive promo card updated!');
  bindAjaxForm('homepage-cms-form', 'Homepage content updated!');
  bindAjaxForm('studio-settings-form', 'Studio settings updated!');

  // 7. Coupons Manager
  const toggleCouponBtn = document.getElementById('toggle-add-coupon-btn');
  const addCouponBox = document.getElementById('add-coupon-box');
  const cancelCouponBtn = document.getElementById('cancel-coupon-btn');
  const saveCouponForm = document.getElementById('save-coupon-form');

  if (toggleCouponBtn && addCouponBox) {
    toggleCouponBtn.addEventListener('click', () => {
      addCouponBox.style.display = (addCouponBox.style.display === 'none') ? 'block' : 'none';
    });
  }

  if (cancelCouponBtn && addCouponBox) {
    cancelCouponBtn.addEventListener('click', () => {
      addCouponBox.style.display = 'none';
    });
  }

  if (saveCouponForm) {
    saveCouponForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(saveCouponForm);

      try {
        const response = await fetch('/api/admin.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();

        if (data.success) {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Coupon saved successfully!', 'success');
          }
          setTimeout(() => window.location.reload(), 800);
        } else {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Error saving coupon', 'error');
          }
        }
      } catch (err) {
        console.error('Save coupon error:', err);
      }
    });
  }

  document.addEventListener('click', async (e) => {
    const deleteCouponBtn = e.target.closest('.delete-coupon-btn');
    if (deleteCouponBtn) {
      const code = deleteCouponBtn.getAttribute('data-coupon-code');
      if (confirm(`Delete coupon "${code}"?`)) {
        try {
          const formData = new FormData();
          formData.append('action', 'delete_coupon');
          formData.append('code', code);

          const response = await fetch('/api/admin.php', {
            method: 'POST',
            body: formData
          });
          const data = await response.json();

          if (data.success) {
            if (typeof showToast === 'function') {
              showToast(data.message || 'Coupon deleted', 'info');
            }
            setTimeout(() => window.location.reload(), 800);
          }
        } catch (err) {
          console.error('Delete coupon error:', err);
        }
      }
    }
  });

  // 8. Reviews Manager
  const toggleReviewBtn = document.getElementById('toggle-add-review-btn');
  const addReviewBox = document.getElementById('add-review-box');
  const cancelReviewBtn = document.getElementById('cancel-review-btn');
  const saveReviewForm = document.getElementById('save-review-form');

  if (toggleReviewBtn && addReviewBox) {
    toggleReviewBtn.addEventListener('click', () => {
      addReviewBox.style.display = (addReviewBox.style.display === 'none') ? 'block' : 'none';
    });
  }

  if (cancelReviewBtn && addReviewBox) {
    cancelReviewBtn.addEventListener('click', () => {
      addReviewBox.style.display = 'none';
    });
  }

  if (saveReviewForm) {
    saveReviewForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(saveReviewForm);

      try {
        const response = await fetch('/api/admin.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();

        if (data.success) {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Review saved!', 'success');
          }
          setTimeout(() => window.location.reload(), 800);
        } else {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Error saving review', 'error');
          }
        }
      } catch (err) {
        console.error('Save review error:', err);
      }
    });
  }

  document.addEventListener('click', async (e) => {
    const deleteReviewBtn = e.target.closest('.delete-review-btn');
    if (deleteReviewBtn) {
      const reviewId = deleteReviewBtn.getAttribute('data-review-id');
      if (confirm(`Delete this review?`)) {
        try {
          const formData = new FormData();
          formData.append('action', 'delete_review');
          formData.append('id', reviewId);

          const response = await fetch('/api/admin.php', {
            method: 'POST',
            body: formData
          });
          const data = await response.json();

          if (data.success) {
            if (typeof showToast === 'function') {
              showToast(data.message || 'Review deleted', 'info');
            }
            setTimeout(() => window.location.reload(), 800);
          }
        } catch (err) {
          console.error('Delete review error:', err);
        }
      }
    }
  });
}
