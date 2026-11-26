// =============================================================================
// Murli Creations - Unified Studio Administration Portal Client Controller
// Powers All 14 Core Functional Points via High-Performance REST / AJAX
// =============================================================================

document.addEventListener('DOMContentLoaded', () => {
  initAdminDashboard();
});

function initAdminDashboard() {
  initNavigation();
  initAuthHandlers();
  initProductHandlers();
  initOrderHandlers();
  initTrackingHandlers();
  initCustomerHandlers();
  initCrmHandlers();
  initInquiryHandlers();
  initCmsHandlers();
  initSeoHandlers();
  initEmailHandlers();
  initReminderHandlers();
  initOfferCtaHandlers();
  initTeamSecurityHandlers();
  initStudioPowerTools();
}

// -----------------------------------------------------------------------------
// 1. Navigation & Mobile Sidebar Toggle
// -----------------------------------------------------------------------------
window.switchAdminTab = function(targetTab) {
  const navBtns = document.querySelectorAll('.admin-nav-item[data-tab]');
  const tabPanes = document.querySelectorAll('.admin-tab-pane');
  const sidebar = document.getElementById('adminSidebar');

  let targetFound = false;

  tabPanes.forEach(pane => {
    if (pane.id === `tab-${targetTab}`) {
      pane.classList.add('active');
      pane.style.display = 'block';
      targetFound = true;
    } else {
      pane.classList.remove('active');
      pane.style.display = 'none';
    }
  });

  navBtns.forEach(b => {
    if (b.getAttribute('data-tab') === targetTab) {
      b.classList.add('active');
      // Update Topbar Title
      const titleEl = document.getElementById('topbarPageTitle');
      if (titleEl) {
        titleEl.textContent = b.querySelector('span:nth-child(2)')?.textContent || 'Studio Portal';
      }
    } else {
      b.classList.remove('active');
    }
  });

  // Update hash without jumping
  if (history.replaceState) {
    history.replaceState(null, null, `#${targetTab}`);
  }

  // Close mobile sidebar if open
  if (sidebar && window.innerWidth <= 900) {
    sidebar.classList.remove('open');
  }

  return targetFound;
};

function initNavigation() {
  const navBtns = document.querySelectorAll('.admin-nav-item[data-tab]');
  const toggleBtn = document.getElementById('mobileSidebarToggle');
  const sidebar = document.getElementById('adminSidebar');

  navBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const targetTab = btn.getAttribute('data-tab');
      if (targetTab) {
        window.switchAdminTab(targetTab);
      }
    });
  });

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });
  }

  // Auto-switch to tab from URL hash if provided
  const hash = window.location.hash.replace('#', '');
  if (hash) {
    window.switchAdminTab(hash);
  }
}

// -----------------------------------------------------------------------------
// 7. Authentication Handlers (Login, Logout)
// -----------------------------------------------------------------------------
function initAuthHandlers() {
  const loginForm = document.getElementById('admin-login-form');
  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const username = document.getElementById('admin-username')?.value.trim();
      const password = document.getElementById('admin-password')?.value;

      try {
        const formData = new FormData();
        formData.append('action', 'login');
        formData.append('username', username);
        formData.append('password', password);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
          showNotification(data.message || 'Access granted!', 'success');
          setTimeout(() => window.location.reload(), 600);
        } else {
          showNotification(data.message || 'Login failed.', 'error');
        }
      } catch (err) {
        showNotification('Server communication error during login.', 'error');
      }
    });
  }

  const logoutBtns = document.querySelectorAll('.portal-logout-btn');
  logoutBtns.forEach(btn => {
    btn.addEventListener('click', async () => {
      if (confirm('Are you sure you want to log out of the administration portal?')) {
        const formData = new FormData();
        formData.append('action', 'logout');
        await fetch('/api/admin.php', { method: 'POST', body: formData });
        window.location.reload();
      }
    });
  });
}

// -----------------------------------------------------------------------------
// 1 & 8. Product Management Handlers (CRUD, Stock, Filters)
// -----------------------------------------------------------------------------
function initProductHandlers() {
  const searchInput = document.getElementById('productSearchInput');
  const catSelect = document.getElementById('productCategoryFilter');
  const subcatSelect = document.getElementById('productSubcategoryFilter');
  const stockSelect = document.getElementById('productStockFilter');
  const sortSelect = document.getElementById('productSortFilter');

  // View Switcher (Grid vs Table)
  const btnGrid = document.getElementById('btnViewGrid');
  const btnTable = document.getElementById('btnViewTable');
  const gridView = document.getElementById('productsGridView');
  const tableView = document.getElementById('productsTableView');

  if (btnGrid && btnTable && gridView && tableView) {
    btnGrid.addEventListener('click', () => {
      btnGrid.classList.add('active');
      btnTable.classList.remove('active');
      gridView.style.display = 'grid';
      tableView.style.display = 'none';
    });
    btnTable.addEventListener('click', () => {
      btnTable.classList.add('active');
      btnGrid.classList.remove('active');
      gridView.style.display = 'none';
      tableView.style.display = 'block';
    });
  }

  // Populate dynamic subcategories for product filter
  const updateProductSubcategories = () => {
    if (!subcatSelect) return;
    const cat = catSelect ? catSelect.value : 'all';
    subcatSelect.innerHTML = '<option value="all">All Subcategories</option>';

    if (cat === 'all') {
      subcatSelect.disabled = false;
      return;
    }

    const catData = window.MURLI_CATEGORIES ? window.MURLI_CATEGORIES[cat] : null;
    const subcats = new Set();
    if (catData && catData.subcategories) {
      Object.keys(catData.subcategories).forEach(s => subcats.add(s));
    }
    if (window.MURLI_PRODUCTS) {
      window.MURLI_PRODUCTS.forEach(p => {
        const pCat = p.category || '';
        const match = (pCat === cat) || (catData && pCat === catData.name);
        if (match && p.subcategory) subcats.add(p.subcategory);
      });
    }

    subcats.forEach(sub => {
      let count = 0;
      if (window.MURLI_PRODUCTS) {
        count = window.MURLI_PRODUCTS.filter(p => {
          const pCat = p.category || '';
          const match = (pCat === cat) || (catData && pCat === catData.name);
          return match && (p.subcategory === sub);
        }).length;
      }
      const opt = document.createElement('option');
      opt.value = sub;
      opt.textContent = `${sub} (${count})`;
      subcatSelect.appendChild(opt);
    });
  };

  const sortProducts = () => {
    const sortBy = sortSelect ? sortSelect.value : 'default';
    if (!sortBy || sortBy === 'default') return;

    const sortFn = (a, b) => {
      if (sortBy === 'price-asc') {
        return parseFloat(a.getAttribute('data-price') || 0) - parseFloat(b.getAttribute('data-price') || 0);
      } else if (sortBy === 'price-desc') {
        return parseFloat(b.getAttribute('data-price') || 0) - parseFloat(a.getAttribute('data-price') || 0);
      } else if (sortBy === 'stock-desc') {
        return parseInt(b.getAttribute('data-stock') || 0, 10) - parseInt(a.getAttribute('data-stock') || 0, 10);
      } else if (sortBy === 'name-asc') {
        return (a.getAttribute('data-name') || '').localeCompare(b.getAttribute('data-name') || '');
      }
      return 0;
    };

    if (gridView) {
      const cards = Array.from(gridView.querySelectorAll('.product-item-container'));
      cards.sort(sortFn);
      cards.forEach(c => gridView.appendChild(c));
    }

    const tableBody = document.querySelector('#productsTableView tbody');
    if (tableBody) {
      const rows = Array.from(tableBody.querySelectorAll('.product-table-row'));
      rows.sort(sortFn);
      rows.forEach(r => tableBody.appendChild(r));
    }
  };

  const filterProducts = () => {
    const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const cat = catSelect ? catSelect.value : 'all';
    const subcat = subcatSelect ? subcatSelect.value : 'all';
    const stock = stockSelect ? stockSelect.value : 'all';

    const catData = (window.MURLI_CATEGORIES && cat !== 'all') ? window.MURLI_CATEGORIES[cat] : null;

    const items = document.querySelectorAll('.product-item-container, .product-table-row');
    items.forEach(el => {
      const name = el.getAttribute('data-name')?.toLowerCase() || '';
      const code = el.getAttribute('data-code')?.toLowerCase() || '';
      const category = el.getAttribute('data-category') || '';
      const itemSubcat = el.getAttribute('data-subcategory') || '';
      const qty = parseInt(el.getAttribute('data-stock') || '0', 10);

      const matchesSearch = !term || 
        name.includes(term) || 
        code.includes(term) || 
        category.toLowerCase().includes(term) ||
        itemSubcat.toLowerCase().includes(term) ||
        (term.replace(/[^a-z0-9]/g, '') && code.replace(/[^a-z0-9]/g, '').includes(term.replace(/[^a-z0-9]/g, '')));

      const matchesCat = (cat === 'all') || (category === cat) || (catData && category === catData.name);
      const matchesSubcat = (subcat === 'all') || (itemSubcat === subcat);

      let matchesStock = true;
      if (stock === 'low') matchesStock = (qty > 0 && qty <= 3);
      if (stock === 'out') matchesStock = (qty <= 0);
      if (stock === 'in') matchesStock = (qty > 0);

      el.style.display = (matchesSearch && matchesCat && matchesSubcat && matchesStock) ? '' : 'none';
    });
  };

  if (searchInput) searchInput.addEventListener('input', filterProducts);
  if (catSelect) {
    catSelect.addEventListener('change', () => {
      updateProductSubcategories();
      filterProducts();
    });
  }
  if (subcatSelect) subcatSelect.addEventListener('change', filterProducts);
  if (stockSelect) stockSelect.addEventListener('change', filterProducts);
  if (sortSelect) {
    sortSelect.addEventListener('change', () => {
      sortProducts();
      filterProducts();
    });
  }

  // 1-Click In Stock / Out of Stock Status Toggle
  document.querySelectorAll('.btn-toggle-stock-status').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      e.stopPropagation();
      const id = btn.getAttribute('data-id');
      if (!id) return;

      btn.disabled = true;
      btn.style.opacity = '0.6';

      try {
        const formData = new FormData();
        formData.append('action', 'toggle_stock_status');
        formData.append('id', id);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
          const newStock = Number(data.stock);
          const isInStock = !!data.in_stock;

          // Update all toggle buttons for this product (card & table)
          document.querySelectorAll(`[data-id="${id}"].btn-toggle-stock-status`).forEach(toggleBtn => {
            toggleBtn.setAttribute('data-stock', newStock);
            if (isInStock) {
              toggleBtn.innerHTML = `<span class="stock-pill in-stock" style="color:#15803d; background:#dcfce7; font-weight:700; font-size:0.75rem; padding:3px 8px; border-radius:12px; border:1px solid #bbf7d0; display:inline-flex; align-items:center; gap:4px;">● In Stock</span>`;
            } else {
              toggleBtn.innerHTML = `<span class="stock-pill out-of-stock" style="color:#b91c1c; background:#fee2e2; font-weight:700; font-size:0.75rem; padding:3px 8px; border-radius:12px; border:1px solid #fecaca; display:inline-flex; align-items:center; gap:4px;">● Out of Stock</span>`;
            }
          });

          // Update stock counts in card stepper
          document.querySelectorAll(`#stock-val-${id}`).forEach(s => s.textContent = newStock);

          // Update table view units count
          const tableUnitsSpan = document.getElementById(`stock-val-table-${id}`);
          if (tableUnitsSpan) {
            tableUnitsSpan.textContent = `(${newStock} units)`;
            tableUnitsSpan.style.color = isInStock ? (newStock <= 3 ? '#b45309' : '#1c1815') : '#b91c1c';
          }

          // Update container attributes and styling
          const cardEl = document.getElementById(`prod-card-${id}`);
          const rowEl = document.getElementById(`prod-row-${id}`);
          if (cardEl) {
            cardEl.setAttribute('data-stock', newStock);
            if (!isInStock) {
              cardEl.classList.add('out-of-stock-card');
            } else {
              cardEl.classList.remove('out-of-stock-card');
            }
          }
          if (rowEl) {
            rowEl.setAttribute('data-stock', newStock);
          }

          showNotification(data.message || (isInStock ? 'Product marked In Stock!' : 'Product marked Out of Stock!'), 'success');
        } else {
          showNotification(data.message || 'Failed to toggle stock status.', 'error');
        }
      } catch (err) {
        showNotification('Network error while updating stock status.', 'error');
      } finally {
        btn.disabled = false;
        btn.style.opacity = '1';
      }
    });
  });

  // Quick Stock Adjusters
  document.querySelectorAll('.btn-quick-stock').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-id');
      const change = parseInt(btn.getAttribute('data-change') || '0', 10);
      const qtySpans = document.querySelectorAll(`#stock-val-${id}`);
      let current = parseInt(qtySpans[0]?.textContent || '0', 10);
      let updated = Math.max(0, current + change);

      try {
        const formData = new FormData();
        formData.append('action', 'update_stock');
        formData.append('id', id);
        formData.append('stock', updated);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          qtySpans.forEach(s => s.textContent = updated);
          const isInStock = updated > 0;

          // Synchronize 1-click toggle buttons
          document.querySelectorAll(`[data-id="${id}"].btn-toggle-stock-status`).forEach(toggleBtn => {
            toggleBtn.setAttribute('data-stock', updated);
            if (isInStock) {
              toggleBtn.innerHTML = `<span class="stock-pill in-stock" style="color:#15803d; background:#dcfce7; font-weight:700; font-size:0.75rem; padding:3px 8px; border-radius:12px; border:1px solid #bbf7d0; display:inline-flex; align-items:center; gap:4px;">● In Stock</span>`;
            } else {
              toggleBtn.innerHTML = `<span class="stock-pill out-of-stock" style="color:#b91c1c; background:#fee2e2; font-weight:700; font-size:0.75rem; padding:3px 8px; border-radius:12px; border:1px solid #fecaca; display:inline-flex; align-items:center; gap:4px;">● Out of Stock</span>`;
            }
          });

          // Synchronize table units label
          const tableUnitsSpan = document.getElementById(`stock-val-table-${id}`);
          if (tableUnitsSpan) {
            tableUnitsSpan.textContent = `(${updated} units)`;
            tableUnitsSpan.style.color = isInStock ? (updated <= 3 ? '#b45309' : '#1c1815') : '#b91c1c';
          }

          const cardEl = document.getElementById(`prod-card-${id}`);
          const rowEl = document.getElementById(`prod-row-${id}`);
          if (cardEl) {
            cardEl.setAttribute('data-stock', updated);
            if (!isInStock) {
              cardEl.classList.add('out-of-stock-card');
            } else {
              cardEl.classList.remove('out-of-stock-card');
            }
          }
          if (rowEl) {
            rowEl.setAttribute('data-stock', updated);
          }

          showNotification(`Stock updated: ${updated} units`, 'success');
        }
      } catch (err) {
        showNotification('Failed to update stock.', 'error');
      }
    });
  });

  // ===========================================================================
  // MULTI-STEP PRODUCT CREATION & EDITING WIZARD ENGINE
  // ===========================================================================
  const addProdBtn = document.getElementById('openAddProductModalBtn');
  const prodModal = document.getElementById('productFormModal');
  const prodForm = document.getElementById('productForm');
  const prodStockInput = document.getElementById('prodFormStock');
  const prodStockStatusSelect = document.getElementById('prodFormStockStatus');
  const prodNameInput = document.getElementById('prodFormName');
  const prodSlugInput = document.getElementById('prodFormSlug');
  const slugPreview = document.getElementById('slugUrlPreview');
  const prodPriceInput = document.getElementById('prodFormPrice');
  const prodMrpInput = document.getElementById('prodFormMrp');
  const prodCategoryHidden = document.getElementById('prodFormCategory');
  const prodSubcatHidden = document.getElementById('prodFormSubcategory');
  const prodImagesInput = document.getElementById('prodFormImages');
  const customSubcatInput = document.getElementById('wizardCustomSubcatInput');
  const sizeSelect = document.getElementById('prodFormSizeSelect');
  const sizeInput = document.getElementById('prodFormSize');

  let currentWizardStep = 1;
  let isSlugManuallyEdited = false;

  // Wizard Navigation: Go to specific step (1 to 5)
  window.goToWizardStep = function(targetStep) {
    targetStep = parseInt(targetStep, 10);
    if (isNaN(targetStep) || targetStep < 1 || targetStep > 5) return;

    // Validate if advancing
    if (targetStep > currentWizardStep) {
      if (currentWizardStep === 1) {
        if (!prodCategoryHidden || !prodCategoryHidden.value) {
          showNotification('Please select a collection category to proceed.', 'error');
          return;
        }
      } else if (currentWizardStep === 2) {
        if (!prodNameInput || !prodNameInput.value.trim()) {
          showNotification('Please enter the Artwork Title.', 'error');
          prodNameInput?.focus();
          return;
        }
      } else if (currentWizardStep === 3) {
        const price = parseFloat(prodPriceInput ? prodPriceInput.value : '0');
        if (!price || price <= 0) {
          showNotification('Please enter a valid Patron Selling Price (₹).', 'error');
          prodPriceInput?.focus();
          return;
        }
      } else if (currentWizardStep === 4) {
        if (!prodImagesInput || !prodImagesInput.value.trim()) {
          showNotification('Please specify at least one Primary Image URL.', 'error');
          prodImagesInput?.focus();
          return;
        }
      }
    }

    currentWizardStep = targetStep;

    // Switch active pane
    document.querySelectorAll('.wizard-pane').forEach(pane => {
      const step = parseInt(pane.getAttribute('data-step') || '1', 10);
      if (step === currentWizardStep) {
        pane.classList.add('active');
      } else {
        pane.classList.remove('active');
      }
    });

    // Update Stepper Bar Header
    document.querySelectorAll('.wizard-step').forEach(stepEl => {
      const step = parseInt(stepEl.getAttribute('data-step') || '1', 10);
      stepEl.classList.remove('active', 'completed');
      if (step === currentWizardStep) {
        stepEl.classList.add('active');
      } else if (step < currentWizardStep) {
        stepEl.classList.add('completed');
      }
    });

    // Update Step Connectors
    const connectors = document.querySelectorAll('.step-connector');
    connectors.forEach((conn, idx) => {
      if (idx + 1 < currentWizardStep) {
        conn.classList.add('active');
      } else {
        conn.classList.remove('active');
      }
    });

    // If reaching Step 5 (Publish Review), refresh live storefront review preview
    if (currentWizardStep === 5) {
      updateWizardReviewSummary();
    }
  };

  // Stepper Header click listener
  document.querySelectorAll('.wizard-step').forEach(stepEl => {
    stepEl.addEventListener('click', () => {
      const target = stepEl.getAttribute('data-step');
      if (target) window.goToWizardStep(target);
    });
  });

  // Next and Prev buttons inside wizard panes
  document.addEventListener('click', (e) => {
    const nextBtn = e.target.closest('.btn-wizard-next');
    if (nextBtn) {
      e.preventDefault();
      const nextStep = nextBtn.getAttribute('data-next');
      if (nextStep) window.goToWizardStep(nextStep);
    }

    const prevBtn = e.target.closest('.btn-wizard-prev');
    if (prevBtn) {
      e.preventDefault();
      const prevStep = prevBtn.getAttribute('data-prev');
      if (prevStep) window.goToWizardStep(prevStep);
    }
  });

  // Category Selection in Step 1
  const selectCategoryCard = (catKey) => {
    if (!catKey) return;
    if (prodCategoryHidden) prodCategoryHidden.value = catKey;

    document.querySelectorAll('.category-pick-card').forEach(card => {
      if (card.getAttribute('data-category') === catKey) {
        card.classList.add('selected');
      } else {
        card.classList.remove('selected');
      }
    });

    populateWizardSubcategories(catKey);

    // Auto suggest SKU prefix if empty
    const codeInput = document.getElementById('prodFormCode');
    if (codeInput && (!codeInput.value || codeInput.value.startsWith('MC/'))) {
      if (catKey === 'Shyam Ji Decor' && !codeInput.value.startsWith('MC/S-')) {
        codeInput.value = 'MC/S-020';
      } else if (catKey === 'Lippan Art' && !codeInput.value.startsWith('MC/L-')) {
        codeInput.value = 'MC/L-025';
      }
    }
  };

  document.querySelectorAll('.category-pick-card').forEach(card => {
    card.addEventListener('click', () => {
      const catKey = card.getAttribute('data-category');
      selectCategoryCard(catKey);
    });
  });

  // Dynamic Subcategory Chips Generator
  const populateWizardSubcategories = (catKey, activeSubcat = '') => {
    const chipContainer = document.getElementById('wizardSubcatChips');
    if (!chipContainer) return;
    chipContainer.innerHTML = '';

    const catData = window.MURLI_CATEGORIES ? window.MURLI_CATEGORIES[catKey] : null;
    const subcats = new Set();
    if (catData && catData.subcategories) {
      Object.keys(catData.subcategories).forEach(s => subcats.add(s));
    }
    if (window.MURLI_PRODUCTS) {
      window.MURLI_PRODUCTS.forEach(p => {
        const pCat = p.category || '';
        const match = (pCat === catKey) || (catData && pCat === catData.name);
        if (match && p.subcategory) subcats.add(p.subcategory);
      });
    }

    subcats.forEach(sub => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'wizard-subcat-chip';
      btn.setAttribute('data-sub', sub);
      btn.innerHTML = `<span class="material-symbols-outlined" style="font-size:14px;">check</span> ${sub}`;

      if (activeSubcat && activeSubcat === sub) {
        btn.classList.add('active');
        if (prodSubcatHidden) prodSubcatHidden.value = sub;
      }

      btn.addEventListener('click', (e) => {
        e.preventDefault();
        chipContainer.querySelectorAll('.wizard-subcat-chip').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        if (prodSubcatHidden) prodSubcatHidden.value = sub;
        if (customSubcatInput) customSubcatInput.value = '';
      });

      chipContainer.appendChild(btn);
    });

    // If activeSubcat was custom, put it into custom input
    if (activeSubcat && !subcats.has(activeSubcat)) {
      if (customSubcatInput) customSubcatInput.value = activeSubcat;
      if (prodSubcatHidden) prodSubcatHidden.value = activeSubcat;
    }
  };

  if (customSubcatInput) {
    customSubcatInput.addEventListener('input', () => {
      const val = customSubcatInput.value.trim();
      if (val) {
        document.querySelectorAll('.wizard-subcat-chip').forEach(c => c.classList.remove('active'));
        if (prodSubcatHidden) prodSubcatHidden.value = val;
      }
    });
  }

  // Auto Slug Generator in Step 2
  if (prodNameInput && prodSlugInput) {
    prodNameInput.addEventListener('input', () => {
      if (!isSlugManuallyEdited) {
        const slug = prodNameInput.value.toLowerCase().trim()
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/^-+|-+$/g, '');
        prodSlugInput.value = slug;
        if (slugPreview) slugPreview.textContent = '/product/' + (slug || 'your-slug');
      }
    });

    prodSlugInput.addEventListener('input', () => {
      isSlugManuallyEdited = true;
      const slug = prodSlugInput.value.toLowerCase().trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
      if (slugPreview) slugPreview.textContent = '/product/' + (slug || 'your-slug');
    });
  }

  // Size Preset in Step 2
  if (sizeSelect && sizeInput) {
    sizeSelect.addEventListener('change', () => {
      if (sizeSelect.value) {
        sizeInput.value = sizeSelect.value;
      }
    });
  }

  // Real-Time Discount Calculator in Step 3
  const updateWizardDiscountCalculation = () => {
    const price = parseFloat(prodPriceInput ? prodPriceInput.value : '0') || 0;
    let mrp = parseFloat(prodMrpInput ? prodMrpInput.value : '0') || 0;
    const discountDisplay = document.getElementById('wizardDiscountDisplay');

    if (!mrp && price > 0) {
      mrp = Math.round(price * 1.3);
      if (prodMrpInput && !prodMrpInput.value) {
        prodMrpInput.placeholder = String(mrp);
      }
    }

    if (discountDisplay) {
      if (mrp > price && price > 0) {
        const pct = Math.round(((mrp - price) / mrp) * 100);
        const saved = mrp - price;
        discountDisplay.innerHTML = `<span style="color:#15803D;">${pct}% OFF</span> (Customer saves ₹${saved.toLocaleString('en-IN')})`;
      } else {
        discountDisplay.innerHTML = `<span style="color:#64748B;">Standard Catalog Price (0% Discount)</span>`;
      }
    }
  };

  if (prodPriceInput) prodPriceInput.addEventListener('input', updateWizardDiscountCalculation);
  if (prodMrpInput) prodMrpInput.addEventListener('input', updateWizardDiscountCalculation);

  // Stock Stepper in Step 3
  document.addEventListener('click', (e) => {
    if (e.target.closest('.btn-wizard-stock-inc')) {
      e.preventDefault();
      if (prodStockInput) {
        prodStockInput.value = Math.max(0, parseInt(prodStockInput.value || '0', 10) + 1);
        if (prodStockStatusSelect) prodStockStatusSelect.value = 'in_stock';
      }
    }
    if (e.target.closest('.btn-wizard-stock-dec')) {
      e.preventDefault();
      if (prodStockInput) {
        const val = Math.max(0, parseInt(prodStockInput.value || '0', 10) - 1);
        prodStockInput.value = val;
        if (val === 0 && prodStockStatusSelect) prodStockStatusSelect.value = 'out_of_stock';
      }
    }
  });

  if (prodStockStatusSelect && prodStockInput) {
    prodStockStatusSelect.addEventListener('change', () => {
      if (prodStockStatusSelect.value === 'out_of_stock') {
        prodStockInput.value = 0;
      } else if (prodStockStatusSelect.value === 'in_stock' && Number(prodStockInput.value) <= 0) {
        prodStockInput.value = 10;
      }
    });
    prodStockInput.addEventListener('input', () => {
      const val = Number(prodStockInput.value);
      if (val <= 0) {
        prodStockStatusSelect.value = 'out_of_stock';
      } else {
        prodStockStatusSelect.value = 'in_stock';
      }
    });
  }

  // Media Presets & Live Photo Preview in Step 4
  const updateWizardPhotoPreview = () => {
    const rawVal = prodImagesInput ? prodImagesInput.value.trim() : '';
    const firstImg = rawVal ? rawVal.split(',')[0].trim() : '';
    const imgEl = document.getElementById('wizardPrimaryImagePreview');
    const noImgEl = document.getElementById('wizardNoImagePlaceholder');

    if (imgEl && noImgEl) {
      if (firstImg) {
        imgEl.src = firstImg;
        imgEl.style.display = 'block';
        noImgEl.style.display = 'none';
        imgEl.onerror = () => {
          imgEl.style.display = 'none';
          noImgEl.style.display = 'block';
        };
      } else {
        imgEl.style.display = 'none';
        noImgEl.style.display = 'block';
      }
    }
  };

  if (prodImagesInput) {
    prodImagesInput.addEventListener('input', updateWizardPhotoPreview);
  }

  document.querySelectorAll('.btn-media-preset').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const src = btn.getAttribute('data-src');
      if (src && prodImagesInput) {
        prodImagesInput.value = src;
        updateWizardPhotoPreview();
      }
    });
  });

  // Step 5 Review & Storefront Live Summary Updater
  const updateWizardReviewSummary = () => {
    const name = prodNameInput ? prodNameInput.value.trim() : 'Artwork Name';
    const cat = prodCategoryHidden ? prodCategoryHidden.value : 'Lippan Art';
    const subcat = prodSubcatHidden ? prodSubcatHidden.value : '';
    const code = document.getElementById('prodFormCode')?.value || 'MC/L-001';
    const size = sizeInput ? sizeInput.value : '';
    const material = document.getElementById('prodFormMaterial')?.value || '';
    const price = parseFloat(prodPriceInput ? prodPriceInput.value : '0') || 0;
    const mrp = parseFloat(prodMrpInput ? prodMrpInput.value : '0') || Math.round(price * 1.3);
    const stock = parseInt(prodStockInput ? prodStockInput.value : '0', 10);
    const isBestseller = document.getElementById('prodFormBestseller')?.checked;
    const isFeatured = document.getElementById('prodFormFeatured')?.checked;
    const slug = prodSlugInput ? prodSlugInput.value : '';
    const imgRaw = prodImagesInput ? prodImagesInput.value.trim() : '';
    const imgUrl = imgRaw ? imgRaw.split(',')[0].trim() : '/img-prodcut/IMG_4041.png';

    // Update Storefront Card Preview
    const cardImg = document.getElementById('revCardImg');
    const cardTitle = document.getElementById('revCardTitle');
    const cardCat = document.getElementById('revCardCat');
    const cardCode = document.getElementById('revCardCode');
    const cardPrice = document.getElementById('revCardPrice');
    const cardMrp = document.getElementById('revCardMrp');
    const cardDiscount = document.getElementById('revCardDiscount');
    const cardBadge = document.getElementById('revCardBadge');
    const cardStockPill = document.getElementById('revCardStockPill');
    const cardStockVal = document.getElementById('revCardStockVal');

    if (cardImg) cardImg.src = imgUrl;
    if (cardTitle) cardTitle.textContent = name;
    if (cardCat) cardCat.textContent = cat;
    if (cardCode) cardCode.textContent = code;
    if (cardPrice) cardPrice.textContent = '₹' + price.toLocaleString('en-IN');
    if (cardMrp) cardMrp.textContent = '₹' + mrp.toLocaleString('en-IN');

    if (cardDiscount) {
      if (mrp > price && price > 0) {
        const pct = Math.round(((mrp - price) / mrp) * 100);
        cardDiscount.textContent = `(${pct}% OFF)`;
        cardDiscount.style.display = 'inline';
      } else {
        cardDiscount.style.display = 'none';
      }
    }

    if (cardBadge) {
      if (isBestseller) {
        cardBadge.textContent = 'Bestseller';
        cardBadge.className = 'card-badge bestseller';
        cardBadge.style.display = 'inline-block';
      } else if (isFeatured) {
        cardBadge.textContent = 'Featured';
        cardBadge.className = 'card-badge featured';
        cardBadge.style.display = 'inline-block';
      } else {
        cardBadge.style.display = 'none';
      }
    }

    if (cardStockPill && cardStockVal) {
      if (stock > 0) {
        cardStockPill.className = 'stock-pill in-stock';
        cardStockPill.textContent = '● In Stock';
        cardStockVal.textContent = `${stock} units`;
        cardStockVal.style.color = '#0F382C';
      } else {
        cardStockPill.className = 'stock-pill out-of-stock';
        cardStockPill.textContent = '● Out of Stock';
        cardStockVal.textContent = '0 units';
        cardStockVal.style.color = '#DC2626';
      }
    }

    // Update Dossier Table
    const setElem = (id, text) => {
      const el = document.getElementById(id);
      if (el) el.textContent = text || '—';
    };
    setElem('revSummaryCat', cat);
    setElem('revSummarySubcat', subcat);
    setElem('revSummaryCode', code);
    setElem('revSummarySize', size);
    setElem('revSummaryMaterial', material);
    setElem('revSummaryStock', `${stock} units (${stock > 0 ? 'In Stock' : 'Sold Out'})`);
    setElem('revSummarySlug', slug ? `/product/${slug}` : '/product/artwork');
  };

  // Open Add Product Modal handler
  if (addProdBtn && prodModal) {
    addProdBtn.addEventListener('click', () => {
      if (prodForm) prodForm.reset();
      isSlugManuallyEdited = false;
      document.getElementById('productModalTitle').textContent = 'Add New Handcrafted Artwork';
      document.getElementById('prodFormId').value = '';
      if (prodStockStatusSelect) prodStockStatusSelect.value = 'in_stock';
      if (prodStockInput) prodStockInput.value = '10';
      if (prodImagesInput) prodImagesInput.value = '/img-prodcut/IMG_4041.png';
      selectCategoryCard('Lippan Art');
      updateWizardDiscountCalculation();
      updateWizardPhotoPreview();
      window.goToWizardStep(1);
      prodModal.classList.add('active');
    });
  }

  // Helper to open Edit Product modal safely in multi-step wizard
  window.openEditProductModal = function(dataStr) {
    if (!dataStr) return;
    try {
      const p = (typeof dataStr === 'string') ? JSON.parse(dataStr) : dataStr;
      const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = (val !== null && val !== undefined) ? val : '';
      };
      const setChecked = (id, bool) => {
        const el = document.getElementById(id);
        if (el) el.checked = !!bool;
      };

      const titleEl = document.getElementById('productModalTitle');
      if (titleEl) titleEl.textContent = `Edit Artwork: ${p.name || ''}`;

      setVal('prodFormId', p.id || '');
      setVal('prodFormName', p.name || '');
      setVal('prodFormCode', p.code || '');
      setVal('prodFormSlug', p.slug || '');
      if (slugPreview) slugPreview.textContent = '/product/' + (p.slug || 'artwork');
      isSlugManuallyEdited = true;

      const pCat = p.category || 'Lippan Art';
      const pSubcat = p.subcategory || '';
      selectCategoryCard(pCat);
      populateWizardSubcategories(pCat, pSubcat);

      setVal('prodFormPrice', p.price || '');
      setVal('prodFormMrp', p.mrp || '');
      const currentStock = Number(p.stock ?? 0);
      setVal('prodFormStock', currentStock);
      setVal('prodFormStockStatus', (currentStock > 0) ? 'in_stock' : 'out_of_stock');
      setVal('prodFormSize', p.size || '');
      setVal('prodFormMaterial', p.material || '');
      setVal('prodFormOrigin', p.origin || 'Delhi, India');
      setVal('prodFormArtist', p.artist || 'Master Craftsman Murli Prasad');
      setVal('prodFormImages', Array.isArray(p.images) ? p.images.join(', ') : (p.images || ''));
      setVal('prodFormTags', Array.isArray(p.tags) ? p.tags.join(', ') : (p.tags || ''));
      setVal('prodFormVideo', p.video || '');
      setVal('prodFormFolklore', p.folklore || '');
      setVal('prodFormDesc', p.description || '');
      setChecked('prodFormFeatured', p.featured);
      setChecked('prodFormBestseller', p.bestseller);

      updateWizardDiscountCalculation();
      updateWizardPhotoPreview();
      window.goToWizardStep(1);

      const modal = document.getElementById('productFormModal');
      if (modal) {
        modal.classList.add('active');
      }
    } catch (err) {
      console.error('Error opening product edit modal:', err);
      showNotification('Could not open edit modal: ' + err.message, 'error');
    }
  };

  // Event delegation for Edit Product button
  document.addEventListener('click', (e) => {
    const editBtn = e.target.closest('.btn-edit-product');
    if (editBtn) {
      e.preventDefault();
      e.stopPropagation();
      const dataStr = editBtn.getAttribute('data-product');
      window.openEditProductModal(dataStr);
    }
  });

  // Save Product Submit
  if (prodForm) {
    prodForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = document.getElementById('btnSubmitProduct') || prodForm.querySelector('button[type="submit"]');
      const origHtml = submitBtn ? submitBtn.innerHTML : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="material-symbols-outlined" style="animation:spin 1s linear infinite; font-size:18px; vertical-align:middle; margin-right:6px;">progress_activity</span> Publishing Artwork...`;
      }

      const isEdit = !!document.getElementById('prodFormId').value;
      const formData = new FormData(prodForm);
      formData.append('action', isEdit ? 'edit_product' : 'add_product');

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message || (isEdit ? 'Artwork updated successfully!' : 'New artwork published successfully!'), 'success');
          prodModal?.classList.remove('active');
          setTimeout(() => window.location.reload(), 650);
        } else {
          showNotification(data.message || 'Error saving product.', 'error');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = origHtml;
          }
        }
      } catch (err) {
        showNotification('Server communication error.', 'error');
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = origHtml;
        }
      }
    });
  }

  // Delete Product with Event Delegation
  document.addEventListener('click', async (e) => {
    const delBtn = e.target.closest('.btn-delete-product');
    if (delBtn) {
      e.preventDefault();
      e.stopPropagation();
      const id = delBtn.getAttribute('data-id');
      const name = delBtn.getAttribute('data-name');
      if (confirm(`Are you sure you want to permanently delete "${name}" (${id})?`)) {
        try {
          const formData = new FormData();
          formData.append('action', 'delete_product');
          formData.append('id', id);

          const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
          const data = await res.json();
          if (data.success) {
            showNotification(data.message, 'success');
            document.getElementById(`prod-row-${id}`)?.remove();
            document.getElementById(`prod-card-${id}`)?.remove();
          } else {
            showNotification(data.message || 'Delete failed.', 'error');
          }
        } catch (err) {
          showNotification('Error deleting product.', 'error');
        }
      }
    }
  });

  // 3. Product Details Deep Dive Inspector with Event Delegation
  document.addEventListener('click', async (e) => {
    const inspectBtn = e.target.closest('.btn-inspect-product');
    if (inspectBtn) {
      e.preventDefault();
      e.stopPropagation();
      const id = inspectBtn.getAttribute('data-id');
      const inspectModal = document.getElementById('productDetailModal');
      const container = document.getElementById('productDetailContent');
      if (!inspectModal || !container) return;

      container.innerHTML = `<div style="text-align:center; padding:40px;"><p>Loading product specifications...</p></div>`;
      inspectModal.classList.add('active');

      try {
        const res = await fetch(`/api/admin.php?action=get_product_detail&id=${encodeURIComponent(id)}`);
        const data = await res.json();
        if (data.success && data.product) {
          const p = data.product;
          const imagesHtml = (p.images || []).map(img => `<img src="${img}" style="width:70px; height:70px; object-fit:cover; border-radius:8px; border:1px solid #ddd;" />`).join('');

          container.innerHTML = `
            <div style="display:grid; grid-template-columns: 280px 1fr; gap:24px;">
              <div>
                <img src="${(p.images && p.images[0]) ? p.images[0] : '/img-prodcut/IMG_4041.png'}" style="width:100%; border-radius:12px; border:1px solid var(--admin-border); object-fit:cover;" />
                <div style="display:flex; gap:8px; margin-top:10px; overflow-x:auto;">${imagesHtml}</div>
              </div>
              <div>
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                  <div>
                    <span class="badge badge-gold">${p.category}</span>
                    <h3 style="margin:6px 0 2px 0; font-size:1.4rem;">${p.name}</h3>
                    <p style="color:#777; margin:0; font-size:0.85rem;">SKU: <strong>${p.code}</strong> • ID: <code>${p.id}</code></p>
                  </div>
                  <div style="text-align:right;">
                    <div style="font-size:1.6rem; font-weight:800; color:var(--admin-text-main);">₹${Number(p.price).toLocaleString()}</div>
                    <div style="font-size:0.85rem; color:#888; text-decoration:line-through;">MRP: ₹${Number(p.mrp).toLocaleString()} (${p.discountPercent}% OFF)</div>
                  </div>
                </div>

                <div style="background:#fcfaf7; border:1px solid var(--admin-border); border-radius:10px; padding:16px; margin:20px 0;">
                  <h4 style="margin:0 0 10px 0; font-size:0.9rem; text-transform:uppercase; color:#887a6d;">Craftsmanship & Specifications</h4>
                  <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.85rem;">
                    <div><strong>Dimensions:</strong> ${p.size || '18x18 inches'}</div>
                    <div><strong>Materials:</strong> ${p.material || 'Clay Relief, Mirror Inlays'}</div>
                    <div><strong>Origin:</strong> ${p.origin || 'Delhi, India'}</div>
                    <div><strong>Master Artisan:</strong> ${p.artist || 'Murli Prasad'}</div>
                    <div><strong>Stock In Studio:</strong> ${p.stock > 0 ? `<span class="badge badge-success" style="color:#15803d; background:#dcfce7; border:1px solid #bbf7d0;">● In Stock (${p.stock} units)</span>` : `<span class="badge badge-danger" style="color:#b91c1c; background:#fee2e2; border:1px solid #fecaca;">● Out of Stock</span>`}</div>
                    <div><strong>Customer Rating:</strong> ⭐ ${p.rating} (${p.reviewsCount} verified reviews)</div>
                  </div>
                </div>

                <div style="font-size:0.9rem; color:#444; line-height:1.5;">
                  <strong>Folklore & Spiritual Significance:</strong>
                  <p style="margin:4px 0 14px 0; font-style:italic; color:#665c52;">"${p.folklore || 'Authentic traditional Indian heritage artwork handcrafted for positive home vibrations.'}"</p>
                  
                  <strong>Description:</strong>
                  <p style="margin:4px 0 0 0;">${p.description || 'No detailed description.'}</p>
                </div>
              </div>
            </div>
          `;
        }
      } catch (err) {
        container.innerHTML = `<p style="color:red;">Error loading product details.</p>`;
      }
    }
  });
}

// -----------------------------------------------------------------------------
// 4 & 9 & 10. Order Details, Tracking & Status Change
// -----------------------------------------------------------------------------
function initOrderHandlers() {
  const searchInput = document.getElementById('orderSearchInput');
  const statusSelect = document.getElementById('orderStatusFilter');

  const filterOrders = () => {
    const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const st = statusSelect ? statusSelect.value : 'all';

    document.querySelectorAll('.order-table-row').forEach(row => {
      const id = row.getAttribute('data-id')?.toLowerCase() || '';
      const name = row.getAttribute('data-customer')?.toLowerCase() || '';
      const orderSt = row.getAttribute('data-status') || '';

      const matchSearch = !term || id.includes(term) || name.includes(term);
      const matchSt = st === 'all' || orderSt === st;

      row.style.display = (matchSearch && matchSt) ? '' : 'none';
    });
  };

  if (searchInput) searchInput.addEventListener('input', filterOrders);
  if (statusSelect) statusSelect.addEventListener('change', filterOrders);

  // View Order Details Drawer / Modal
  document.querySelectorAll('.btn-view-order').forEach(btn => {
    btn.addEventListener('click', async () => {
      const orderId = btn.getAttribute('data-id');
      const orderModal = document.getElementById('orderDetailModal');
      const container = document.getElementById('orderDetailContent');
      if (!orderModal || !container) return;

      container.innerHTML = `<div style="text-align:center; padding:40px;"><p>Loading order invoice & timeline...</p></div>`;
      orderModal.classList.add('active');

      try {
        const res = await fetch(`/api/admin.php?action=get_order_detail&orderId=${encodeURIComponent(orderId)}`);
        const data = await res.json();
        if (data.success && data.order) {
          const o = data.order;
          const cust = o.customer || {};
          const items = o.items || [];

          let itemsHtml = items.map(it => {
            const prod = it.product || it;
            const title = prod.title || prod.name || it.productName || 'Artwork';
            const price = Number(prod.price || it.price || 0);
            const qty = Number(it.quantity || 1);
            const img = prod.images?.[0] || it.image || '/img-prodcut/IMG_4041.png';
            return `
              <tr>
                <td style="padding:10px;"><img src="${img}" style="width:48px; height:48px; object-fit:cover; border-radius:6px;" /></td>
                <td style="padding:10px;"><strong>${title}</strong></td>
                <td style="padding:10px; text-align:center;">₹${price.toLocaleString()}</td>
                <td style="padding:10px; text-align:center;">${qty}</td>
                <td style="padding:10px; text-align:right; font-weight:bold;">₹${(price * qty).toLocaleString()}</td>
              </tr>
            `;
          }).join('');

          let historyHtml = (o.status_history || []).map(h => `
            <div style="font-size:0.8rem; padding:6px 0; border-bottom:1px dashed #eee;">
              <strong>${h.new_status}</strong> <span style="color:#888;">by ${h.changed_by} (${h.created_at})</span>: ${h.comment || ''}
            </div>
          `).join('');

          container.innerHTML = `
            <div id="printableInvoiceArea">
              <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid var(--admin-primary); padding-bottom:16px; margin-bottom:20px;">
                <div>
                  <h2 style="margin:0; font-family:serif; letter-spacing:1px; color:#14100c;">MURLI CREATIONS</h2>
                  <p style="margin:2px 0 0 0; font-size:0.8rem; color:#888;">Studio Tax Invoice & Delivery Manifest</p>
                </div>
                <div style="text-align:right;">
                  <div style="font-size:1.2rem; font-weight:bold; color:#14100c;">Order #${o.id}</div>
                  <div style="font-size:0.85rem; color:#666;">Date: ${o.createdAt || o.date || 'Recent'}</div>
                </div>
              </div>

              <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; font-size:0.9rem;">
                <div style="background:#fcfaf7; padding:16px; border-radius:8px;">
                  <h4 style="margin:0 0 6px 0; font-size:0.85rem; text-transform:uppercase; color:#888;">Shipping Destination</h4>
                  <strong>${cust.name || 'Patron'}</strong><br/>
                  ${cust.address || ''}<br/>
                  ${cust.city || ''}, ${cust.state || ''} - ${cust.pinCode || cust.pin || ''}<br/>
                  Phone: <strong>${cust.phone || ''}</strong><br/>
                  Email: ${cust.email || ''}
                </div>
                <div style="background:#fcfaf7; padding:16px; border-radius:8px;">
                  <h4 style="margin:0 0 6px 0; font-size:0.85rem; text-transform:uppercase; color:#888;">Payment & Fulfillment</h4>
                  <div>Mode: <strong>${o.paymentMethod || 'Partial COD'}</strong></div>
                  <div>Payment Status: <span class="badge ${o.paymentStatus === 'Paid' ? 'badge-success' : 'badge-warning'}">${o.paymentStatus}</span></div>
                  <div>Order Status: <span class="badge badge-gold">${o.orderStatus || o.status}</span></div>
                  <div>Tracking Carrier: <strong>${o.tracking_carrier || o.trackingCarrier || 'Not assigned'}</strong></div>
                  <div>AWB / Consignment: <strong>${o.tracking_number || o.trackingNumber || 'Pending'}</strong></div>
                </div>
              </div>

              <table style="width:100%; border-collapse:collapse; margin-bottom:20px; font-size:0.9rem;">
                <thead>
                  <tr style="background:#faf8f5; border-bottom:1px solid #ddd; text-transform:uppercase; font-size:0.75rem;">
                    <th style="padding:8px 10px;">Item</th>
                    <th style="padding:8px 10px; text-align:left;">Artwork Description</th>
                    <th style="padding:8px 10px; text-align:center;">Rate</th>
                    <th style="padding:8px 10px; text-align:center;">Qty</th>
                    <th style="padding:8px 10px; text-align:right;">Amount</th>
                  </tr>
                </thead>
                <tbody>${itemsHtml}</tbody>
              </table>

              <div style="display:flex; justify-content:flex-end;">
                <div style="width:280px; font-size:0.9rem; line-height:1.6;">
                  <div style="display:flex; justify-content:space-between;"><span>Subtotal:</span> <strong>₹${Number(o.subtotal || 0).toLocaleString()}</strong></div>
                  <div style="display:flex; justify-content:space-between;"><span>Discount (${o.coupon_code || o.couponCode || 'None'}):</span> <strong>-₹${Number(o.discount_amount || o.discountAmount || 0).toLocaleString()}</strong></div>
                  <div style="display:flex; justify-content:space-between;"><span>Shipping Charge:</span> <strong>₹${Number(o.shipping_fee || o.shipping || 0).toLocaleString()}</strong></div>
                  <div style="display:flex; justify-content:space-between; border-top:2px solid #ddd; padding-top:6px; font-size:1.1rem; color:#14100c;">
                    <span>Total Order Value:</span> <strong>₹${Number(o.totalAmount || o.totalPayable || 0).toLocaleString()}</strong>
                  </div>
                  ${(o.balance_on_delivery > 0 || o.balanceOnDelivery > 0) ? `
                    <div style="display:flex; justify-content:space-between; color:#b45309; font-weight:bold; margin-top:4px;">
                      <span>Balance on Delivery:</span> <span>₹${Number(o.balance_on_delivery || o.balanceOnDelivery).toLocaleString()}</span>
                    </div>
                  ` : ''}
                </div>
              </div>

              <div style="margin-top:24px; padding-top:16px; border-top:1px solid #eee;">
                <h4 style="margin:0 0 8px 0; font-size:0.85rem; color:#777;">Audit Timeline & Status History:</h4>
                ${historyHtml || '<p style="color:#999; font-size:0.8rem;">Initial order booking registered.</p>'}
              </div>
            </div>
          `;
        }
      } catch (err) {
        container.innerHTML = `<p style="color:red;">Error loading invoice.</p>`;
      }
    });
  });

  // Print Invoice Button
  const printBtn = document.getElementById('btnPrintInvoice');
  if (printBtn) {
    printBtn.addEventListener('click', () => {
      window.print();
    });
  }

  // Change Status Modal
  document.querySelectorAll('.btn-change-order-status').forEach(btn => {
    btn.addEventListener('click', () => {
      const orderId = btn.getAttribute('data-id');
      const currentSt = btn.getAttribute('data-status');
      document.getElementById('statusModalOrderId').value = orderId;
      document.getElementById('statusModalSelect').value = currentSt;
      document.getElementById('changeStatusModal').classList.add('active');
    });
  });

  const statusForm = document.getElementById('changeStatusForm');
  if (statusForm) {
    statusForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const orderId = document.getElementById('statusModalOrderId').value;
      const status = document.getElementById('statusModalSelect').value;
      const comment = document.getElementById('statusModalComment').value;
      const notify = document.getElementById('statusModalNotify').checked;

      try {
        const formData = new FormData();
        formData.append('action', 'update_order_status');
        formData.append('orderId', orderId);
        formData.append('status', status);
        formData.append('comment', comment);
        if (notify) formData.append('notifyCustomer', '1');

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          setTimeout(() => window.location.reload(), 700);
        } else {
          showNotification(data.message || 'Status update failed.', 'error');
        }
      } catch (err) {
        showNotification('Server communication error.', 'error');
      }
    });
  }
}

// -----------------------------------------------------------------------------
// 9. Order Tracking Handlers
// -----------------------------------------------------------------------------
function initTrackingHandlers() {
  document.querySelectorAll('.btn-edit-tracking').forEach(btn => {
    btn.addEventListener('click', () => {
      const orderId = btn.getAttribute('data-id');
      const carrier = btn.getAttribute('data-carrier') || 'Shiprocket Express';
      const awb = btn.getAttribute('data-awb') || '';
      const url = btn.getAttribute('data-url') || '';
      const est = btn.getAttribute('data-est') || '';

      document.getElementById('trackingModalOrderId').value = orderId;
      document.getElementById('trackingModalCarrier').value = carrier;
      document.getElementById('trackingModalAwb').value = awb;
      document.getElementById('trackingModalUrl').value = url;
      document.getElementById('trackingModalEst').value = est;

      document.getElementById('trackingModal').classList.add('active');
    });
  });

  const trackingForm = document.getElementById('orderTrackingForm');
  if (trackingForm) {
    trackingForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const orderId = document.getElementById('trackingModalOrderId').value;
      const carrier = document.getElementById('trackingModalCarrier').value;
      const awb = document.getElementById('trackingModalAwb').value;
      const url = document.getElementById('trackingModalUrl').value;
      const est = document.getElementById('trackingModalEst').value;

      try {
        const formData = new FormData();
        formData.append('action', 'update_order_tracking');
        formData.append('orderId', orderId);
        formData.append('carrier', carrier);
        formData.append('trackingNumber', awb);
        formData.append('trackingUrl', url);
        formData.append('estimatedDelivery', est);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          setTimeout(() => window.location.reload(), 700);
        } else {
          showNotification(data.message || 'Failed to update tracking.', 'error');
        }
      } catch (err) {
        showNotification('Communication error.', 'error');
      }
    });
  }
}

// -----------------------------------------------------------------------------
// 2 & 12. Customer Management Handlers
// -----------------------------------------------------------------------------
function initCustomerHandlers() {
  const custSearch = document.getElementById('customerSearchInput');
  if (custSearch) {
    custSearch.addEventListener('input', () => {
      const term = custSearch.value.toLowerCase().trim();
      document.querySelectorAll('.customer-table-row').forEach(row => {
        const name = row.getAttribute('data-name')?.toLowerCase() || '';
        const email = row.getAttribute('data-email')?.toLowerCase() || '';
        const phone = row.getAttribute('data-phone')?.toLowerCase() || '';
        const match = !term || name.includes(term) || email.includes(term) || phone.includes(term);
        row.style.display = match ? '' : 'none';
      });
    });
  }

  // View Customer Profile Drawer
  document.querySelectorAll('.btn-view-customer').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-id');
      const custModal = document.getElementById('customerDetailModal');
      const container = document.getElementById('customerDetailContent');
      if (!custModal || !container) return;

      container.innerHTML = `<p style="text-align:center; padding:30px;">Loading customer dossier...</p>`;
      custModal.classList.add('active');

      try {
        const res = await fetch(`/api/admin.php?action=get_customer_detail&id=${encodeURIComponent(id)}`);
        const data = await res.json();
        if (data.success && data.data) {
          const c = data.data.customer;
          const orders = data.data.orders || [];

          let ordersHtml = orders.map(o => `
            <tr>
              <td style="padding:8px 12px;"><strong>#${o.id}</strong></td>
              <td style="padding:8px 12px;">${o.created_at || 'Recent'}</td>
              <td style="padding:8px 12px; font-weight:bold;">₹${Number(o.total_amount || 0).toLocaleString()}</td>
              <td style="padding:8px 12px;"><span class="badge badge-gold">${o.order_status}</span></td>
            </tr>
          `).join('');

          container.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
              <div>
                <h3 style="margin:0; font-size:1.3rem;">${c.name}</h3>
                <p style="margin:2px 0 0 0; color:#777; font-size:0.85rem;">Customer Tier: <span class="badge badge-gold">${c.tier || 'Patron'}</span> • Status: <span class="badge badge-success">${c.status || 'Active'}</span></p>
              </div>
              <div style="display:flex; gap:8px;">
                <a href="https://wa.me/91${(c.phone || '').replace(/\D/g, '')}" target="_blank" class="btn btn-sm btn-success">WhatsApp Patron</a>
                <a href="mailto:${c.email}" class="btn btn-sm btn-secondary">Send Email</a>
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; background:#fbf9f6; padding:16px; border-radius:10px; margin-bottom:20px; font-size:0.85rem;">
              <div>
                <strong>Contact Details:</strong><br/>
                Email: ${c.email}<br/>
                Phone: ${c.phone}
              </div>
              <div>
                <strong>Delivery Address:</strong><br/>
                ${c.address || ''}<br/>
                ${c.city || ''}, ${c.state || ''} ${c.pincode || ''}
              </div>
            </div>

            <h4 style="margin:0 0 10px 0; font-size:0.9rem;">Customer Order History (${orders.length} Orders - Total Spent: ₹${Number(c.total_spent || 0).toLocaleString()}):</h4>
            <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
              <thead>
                <tr style="background:#faf8f5; border-bottom:1px solid #ddd; text-align:left;">
                  <th style="padding:8px 12px;">Order ID</th>
                  <th style="padding:8px 12px;">Date</th>
                  <th style="padding:8px 12px;">Total Value</th>
                  <th style="padding:8px 12px;">Status</th>
                </tr>
              </thead>
              <tbody>${ordersHtml || '<tr><td colspan="4" style="text-align:center; padding:16px; color:#888;">No historical orders.</td></tr>'}</tbody>
            </table>
          `;
        }
      } catch (err) {
        container.innerHTML = `<p style="color:red;">Error loading customer data.</p>`;
      }
    });
  });
}

// -----------------------------------------------------------------------------
// 5. CRM (Customer Relationship Management) Handlers
// -----------------------------------------------------------------------------
function initCrmHandlers() {
  const leadStatusFilter = document.getElementById('crmLeadStatusFilter');
  if (leadStatusFilter) {
    leadStatusFilter.addEventListener('change', () => {
      const st = leadStatusFilter.value;
      document.querySelectorAll('.crm-lead-card').forEach(card => {
        const leadSt = card.getAttribute('data-status');
        card.style.display = (st === 'all' || leadSt === st) ? '' : 'none';
      });
    });
  }

  // Update Lead Status dropdown
  document.querySelectorAll('.crm-status-select').forEach(sel => {
    sel.addEventListener('change', async () => {
      const id = sel.getAttribute('data-id');
      const newStatus = sel.value;

      try {
        const formData = new FormData();
        formData.append('action', 'update_lead_status');
        formData.append('id', id);
        formData.append('status', newStatus);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          const card = document.getElementById(`crm-lead-${id}`);
          if (card) card.setAttribute('data-status', newStatus);
        }
      } catch (err) {
        showNotification('Failed to update lead stage.', 'error');
      }
    });
  });

  // Log Interaction modal
  document.querySelectorAll('.btn-log-interaction').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const name = btn.getAttribute('data-name');
      document.getElementById('crmLogLeadId').value = id;
      document.getElementById('crmLogLeadName').textContent = name;
      document.getElementById('crmInteractionModal').classList.add('active');
    });
  });

  const interactionForm = document.getElementById('crmInteractionForm');
  if (interactionForm) {
    interactionForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const leadId = document.getElementById('crmLogLeadId').value;
      const channel = document.getElementById('crmChannelSelect').value;
      const summary = document.getElementById('crmInteractionSummary').value;
      const nextStep = document.getElementById('crmNextStep').value;

      try {
        const formData = new FormData();
        formData.append('action', 'add_crm_interaction');
        formData.append('lead_id', leadId);
        formData.append('channel', channel);
        formData.append('summary', summary);
        formData.append('next_step', nextStep);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          document.getElementById('crmInteractionModal').classList.remove('active');
          interactionForm.reset();
        }
      } catch (err) {
        showNotification('Error logging interaction.', 'error');
      }
    });
  }

  // Delete Lead
  document.querySelectorAll('.btn-delete-lead').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-id');
      if (confirm(`Remove lead #${id} from CRM?`)) {
        const formData = new FormData();
        formData.append('action', 'delete_crm_lead');
        formData.append('id', id);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          document.getElementById(`crm-lead-${id}`)?.remove();
        }
      }
    });
  });
}

// -----------------------------------------------------------------------------
// Contact Form Inquiries Management & Direct Replies
// -----------------------------------------------------------------------------
function initInquiryHandlers() {
  const searchInput = document.getElementById('inquirySearchInput');
  const statusFilter = document.getElementById('inquiryStatusFilter');

  const filterInquiries = () => {
    const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const st = statusFilter ? statusFilter.value : 'all';

    document.querySelectorAll('.inquiry-table-row').forEach(row => {
      const name = row.getAttribute('data-name')?.toLowerCase() || '';
      const phone = row.getAttribute('data-phone')?.toLowerCase() || '';
      const email = row.getAttribute('data-email')?.toLowerCase() || '';
      const sub = row.getAttribute('data-subject')?.toLowerCase() || '';
      const rowSt = row.getAttribute('data-status') || '';

      const matchSearch = !term || name.includes(term) || phone.includes(term) || email.includes(term) || sub.includes(term);
      const matchSt = st === 'all' || rowSt === st;

      row.style.display = (matchSearch && matchSt) ? '' : 'none';
    });
  };

  if (searchInput) searchInput.addEventListener('input', filterInquiries);
  if (statusFilter) statusFilter.addEventListener('change', filterInquiries);

  // Quick Status changer dropdown
  document.querySelectorAll('.inq-status-changer').forEach(sel => {
    sel.addEventListener('change', async () => {
      const id = sel.getAttribute('data-id');
      const newStatus = sel.value;

      try {
        const formData = new FormData();
        formData.append('action', 'update_contact_inquiry_status');
        formData.append('id', id);
        formData.append('status', newStatus);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          const badge = document.getElementById(`inq-badge-${id}`);
          if (badge) {
            badge.textContent = `● ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}`;
            badge.className = 'badge ' + (newStatus === 'resolved' ? 'badge-success' : (newStatus === 'replied' ? 'badge-info' : (newStatus === 'new' ? 'badge-danger' : 'badge-warning')));
          }
          const row = document.getElementById(`inquiry-row-${id}`);
          if (row) row.setAttribute('data-status', newStatus);
        }
      } catch (err) {
        showNotification('Failed to update inquiry status.', 'error');
      }
    });
  });

  // WhatsApp reply click tracking
  document.querySelectorAll('.btn-whatsapp-reply').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-id');
      try {
        const formData = new FormData();
        formData.append('action', 'update_contact_inquiry_status');
        formData.append('id', id);
        formData.append('status', 'replied');
        await fetch('/api/admin.php', { method: 'POST', body: formData });
        const row = document.getElementById(`inquiry-row-${id}`);
        if (row) row.setAttribute('data-status', 'replied');
        const badge = document.getElementById(`inq-badge-${id}`);
        if (badge) {
          badge.textContent = '● Replied';
          badge.className = 'badge badge-info';
        }
      } catch (e) {}
    });
  });

  // Open Email Reply Modal
  const replyModal = document.getElementById('inquiryReplyModal');
  document.querySelectorAll('.btn-open-reply-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const name = btn.getAttribute('data-name');
      const email = btn.getAttribute('data-email');
      const subject = btn.getAttribute('data-subject');
      const message = btn.getAttribute('data-message');

      document.getElementById('inquiryReplyId').value = id;
      document.getElementById('inquiryReplyCustomerName').textContent = name;
      document.getElementById('inquiryReplyTo').value = email;
      document.getElementById('inquiryReplySubjectTag').textContent = subject;
      document.getElementById('inquiryReplyCustomerMessage').textContent = `"${message}"`;
      document.getElementById('inquiryReplyMessage').value = `Namaste ${name} ji,\n\nMaster Craftsman Murli Prasad has personally received your inquiry regarding "${subject}". We would be honoured to handcraft this auspicious piece for your home.\n\nCould you please confirm the preferred wall dimensions and color palette? You may also reach us directly on WhatsApp at +91 95823 16454.\n\nWarm regards,\nMurli Creations Studio • Delhi`;

      if (replyModal) replyModal.classList.add('active');
    });
  });

  // Quick Templates in Reply Modal
  document.querySelectorAll('.btn-quick-tpl').forEach(btn => {
    btn.addEventListener('click', () => {
      const tpl = btn.getAttribute('data-tpl');
      const name = document.getElementById('inquiryReplyCustomerName')?.textContent || 'Patron';
      const sub = document.getElementById('inquiryReplySubjectTag')?.textContent || 'Artwork';
      const textarea = document.getElementById('inquiryReplyMessage');
      if (!textarea) return;

      if (tpl === 'quote') {
        textarea.value = `Namaste ${name} ji,\n\nThank you for considering Murli Creations for your custom art commission. Based on your inquiry regarding "${sub}", our standard pricing for bespoke 24x24 inch Lippan Art panels is ₹2,999 to ₹4,499 including insured express transit and heavy-duty brass wall hooks.\n\nWould you like us to share our sample mirror layout catalog via WhatsApp?\n\nWarm regards,\nMaster Craftsman Murli Prasad`;
      } else if (tpl === 'tracking') {
        textarea.value = `Namaste ${name} ji,\n\nYour handcrafted order is currently in final inspection at our Pitampura, Delhi studio. You can track live courier movements anytime using our tracking portal: https://murlicreations.com/track-order.php\n\nPlease let us know if you have any questions!\n\nWarm regards,\nMurli Creations Dispatch Team`;
      }
    });
  });

  // Send Email Reply Form
  const replyForm = document.getElementById('inquiryReplyForm');
  if (replyForm) {
    replyForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = replyForm.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Dispatching Email...';
      }

      const id = document.getElementById('inquiryReplyId').value;
      const formData = new FormData(replyForm);
      formData.append('action', 'reply_contact_inquiry');

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          if (replyModal) replyModal.classList.remove('active');
          const badge = document.getElementById(`inq-badge-${id}`);
          if (badge) {
            badge.textContent = '● Replied';
            badge.className = 'badge badge-info';
          }
          const row = document.getElementById(`inquiry-row-${id}`);
          if (row) row.setAttribute('data-status', 'replied');
        } else {
          showNotification(data.message || 'Error sending reply.', 'error');
        }
      } catch (err) {
        showNotification('Communication error.', 'error');
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Dispatch Official Email Reply →';
        }
      }
    });
  }

  // Delete Inquiry
  document.querySelectorAll('.btn-delete-inquiry').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-id');
      if (confirm(`Permanently remove inquiry #INQ-${id}?`)) {
        try {
          const formData = new FormData();
          formData.append('action', 'delete_contact_inquiry');
          formData.append('id', id);

          const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
          const data = await res.json();
          if (data.success) {
            showNotification(data.message, 'success');
            document.getElementById(`inquiry-row-${id}`)?.remove();
          }
        } catch (err) {
          showNotification('Delete failed.', 'error');
        }
      }
    });
  });
}

// -----------------------------------------------------------------------------
// Navbar Pages & Studio Details CMS Handlers (0 to End Editable)
// -----------------------------------------------------------------------------
function initCmsHandlers() {
  // CMS Subnav Switching
  const subBtns = document.querySelectorAll('.cms-subnav-btn[data-sub]');
  const subPanels = document.querySelectorAll('.cms-section-panel');

  subBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetSub = btn.getAttribute('data-sub');
      subBtns.forEach(b => b.classList.toggle('active', b === btn));
      subPanels.forEach(p => {
        p.classList.toggle('active', p.id === `sub-${targetSub}`);
      });
    });
  });

  // Top header button triggers form submission
  document.querySelectorAll('.btn-submit-cms-form').forEach(btn => {
    btn.addEventListener('click', () => {
      const formId = btn.getAttribute('data-form');
      const form = document.getElementById(formId);
      if (form) {
        if (typeof form.requestSubmit === 'function') {
          form.requestSubmit();
        } else {
          form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }
      }
    });
  });

  // Studio & Contact Info Form
  const formContact = document.getElementById('formStudioContact');
  if (formContact) {
    formContact.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formContact);
      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
        } else {
          showNotification(data.message || 'Error saving studio details.', 'error');
        }
      } catch (err) {
        showNotification('Communication error.', 'error');
      }
    });
  }

  // Home Copy Form
  const formHome = document.getElementById('formHomeCopy');
  if (formHome) {
    formHome.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formHome);
      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
        } else {
          showNotification(data.message || 'Error saving home copy.', 'error');
        }
      } catch (err) {
        showNotification('Communication error.', 'error');
      }
    });
  }

  // Header & Footer Form
  const formHeaderFooter = document.getElementById('formHeaderFooter');
  if (formHeaderFooter) {
    formHeaderFooter.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formHeaderFooter);
      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
        } else {
          showNotification(data.message || 'Error saving header & footer.', 'error');
        }
      } catch (err) {
        showNotification('Communication error.', 'error');
      }
    });
  }

  // Shipping Policies Form
  const formShipping = document.getElementById('formShippingPolicies');
  if (formShipping) {
    formShipping.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formShipping);
      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
        } else {
          showNotification(data.message || 'Error saving shipping policies.', 'error');
        }
      } catch (err) {
        showNotification('Communication error.', 'error');
      }
    });
  }

  // Interactive FAQs Builder
  const faqsContainer = document.getElementById('faqsListContainer');
  const addFaqBtn1 = document.getElementById('btnAddNewFaqRow');
  const addFaqBtn2 = document.getElementById('btnAddNewFaqRowBottom');

  const createFaqRow = (q = '', a = '') => {
    const count = faqsContainer ? faqsContainer.querySelectorAll('.faq-builder-item').length + 1 : 1;
    const div = document.createElement('div');
    div.className = 'faq-builder-item';
    div.innerHTML = `
      <div class="faq-builder-header">
        <span class="faq-builder-num">Question #${count}</span>
        <button type="button" class="btn btn-sm btn-danger btn-remove-faq" title="Remove FAQ">&times; Delete</button>
      </div>
      <div class="form-group" style="margin-bottom:10px;">
        <label class="form-label">Question Text *</label>
        <input type="text" class="form-control faq-input-q" value="${escapeHtml(q)}" required placeholder="e.g. Can I place a custom-sized Lippan Art order?">
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label">Answer Text *</label>
        <textarea class="form-control faq-input-a" rows="3" required placeholder="Detailed answer...">${escapeHtml(a)}</textarea>
      </div>
    `;
    div.querySelector('.btn-remove-faq').addEventListener('click', () => div.remove());
    return div;
  };

  const escapeHtml = (str) => {
    return (str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  };

  if (addFaqBtn1 && faqsContainer) {
    addFaqBtn1.addEventListener('click', () => {
      const row = createFaqRow();
      faqsContainer.appendChild(row);
      row.scrollIntoView({ behavior: 'smooth' });
    });
  }
  if (addFaqBtn2 && faqsContainer) {
    addFaqBtn2.addEventListener('click', () => {
      const row = createFaqRow();
      faqsContainer.appendChild(row);
      row.scrollIntoView({ behavior: 'smooth' });
    });
  }

  // Attach delete to pre-rendered FAQ rows
  document.querySelectorAll('.btn-remove-faq').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.closest('.faq-builder-item')?.remove();
    });
  });

  // Submit FAQs Form
  const formFaqs = document.getElementById('formFaqsEditor');
  if (formFaqs && faqsContainer) {
    formFaqs.addEventListener('submit', async (e) => {
      e.preventDefault();
      const items = [];
      faqsContainer.querySelectorAll('.faq-builder-item').forEach(item => {
        const q = item.querySelector('.faq-input-q')?.value.trim();
        const a = item.querySelector('.faq-input-a')?.value.trim();
        if (q && a) {
          items.push({
            q: q,
            a: a,
            question: q,
            answer: a
          });
        }
      });

      const formData = new FormData();
      formData.append('action', 'save_settings');
      formData.append('faqs_json', JSON.stringify(items));

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification('All FAQs saved and published live to Contact & Home pages!', 'success');
        } else {
          showNotification(data.message || 'Error saving FAQs.', 'error');
        }
      } catch (err) {
        showNotification('Communication error.', 'error');
      }
    });
  }
}

// -----------------------------------------------------------------------------
// 6. SEO Tags Management (Per-Page SEO Manager)
// -----------------------------------------------------------------------------
function initSeoHandlers() {
  const pageSelector = document.getElementById('seoPageSelector');
  const seoForm = document.getElementById('seoPageForm');
  const metaTitleInput = document.getElementById('seoMetaTitle');
  const metaDescInput = document.getElementById('seoMetaDesc');

  // Real-Time SERP & Social Preview
  const updatePreviews = () => {
    const title = metaTitleInput?.value || 'Murli Creations | Handcrafted Lippan Art';
    const desc = metaDescInput?.value || 'Discover authentic handcrafted Lippan art and sacred mandalas...';
    const url = document.getElementById('seoCanonicalUrl')?.value || 'https://murlicreations.com/';
    const img = document.getElementById('seoOgImage')?.value || '/img-prodcut/IMG_4041.png';

    const serpTitleEl = document.getElementById('previewSerpTitle');
    const serpUrlEl = document.getElementById('previewSerpUrl');
    const serpDescEl = document.getElementById('previewSerpDesc');

    if (serpTitleEl) serpTitleEl.textContent = title;
    if (serpUrlEl) serpUrlEl.textContent = url;
    if (serpDescEl) serpDescEl.textContent = desc;

    const socialTitleEl = document.getElementById('previewSocialTitle');
    const socialDescEl = document.getElementById('previewSocialDesc');
    const socialImgEl = document.getElementById('previewSocialImg');

    if (socialTitleEl) socialTitleEl.textContent = title;
    if (socialDescEl) socialDescEl.textContent = desc;
    if (socialImgEl) socialImgEl.src = img;
  };

  if (metaTitleInput) metaTitleInput.addEventListener('input', updatePreviews);
  if (metaDescInput) metaDescInput.addEventListener('input', updatePreviews);

  // Load SEO for selected page
  if (pageSelector) {
    pageSelector.addEventListener('change', async () => {
      const key = pageSelector.value;
      try {
        const res = await fetch(`/api/admin.php?action=get_page_seo&page_key=${encodeURIComponent(key)}`);
        const data = await res.json();
        if (data.success && data.seo) {
          const s = data.seo;
          document.getElementById('seoPageKey').value = s.page_key;
          document.getElementById('seoPageName').value = s.page_name;
          document.getElementById('seoUrlPath').value = s.url_path;
          document.getElementById('seoMetaTitle').value = s.meta_title;
          document.getElementById('seoMetaDesc').value = s.meta_description;
          document.getElementById('seoKeywords').value = s.meta_keywords || '';
          document.getElementById('seoCanonicalUrl').value = s.canonical_url || '';
          document.getElementById('seoOgTitle').value = s.og_title || '';
          document.getElementById('seoOgDesc').value = s.og_description || '';
          document.getElementById('seoOgImage').value = s.og_image || '';
          document.getElementById('seoRobots').value = s.robots || 'index, follow';
          document.getElementById('seoSchemaJson').value = s.schema_json || '';
          updatePreviews();
        }
      } catch (err) {
        showNotification('Error loading page SEO tags.', 'error');
      }
    });
  }

  // Save Page SEO
  if (seoForm) {
    seoForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(seoForm);
      formData.append('action', 'save_page_seo');

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
        } else {
          showNotification(data.message || 'Error saving SEO.', 'error');
        }
      } catch (err) {
        showNotification('Communication error.', 'error');
      }
    });
  }
}

// -----------------------------------------------------------------------------
// 11. Email Communication Handlers
// -----------------------------------------------------------------------------
function initEmailHandlers() {
  const directEmailForm = document.getElementById('directEmailComposerForm');
  if (directEmailForm) {
    directEmailForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const to = document.getElementById('emailComposerTo').value;
      const subject = document.getElementById('emailComposerSubject').value;
      const message = document.getElementById('emailComposerMessage').value;

      try {
        const formData = new FormData();
        formData.append('action', 'send_direct_email');
        formData.append('to', to);
        formData.append('subject', subject);
        formData.append('message', message);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          directEmailForm.reset();
        } else {
          showNotification(data.message || 'Failed to dispatch email.', 'error');
        }
      } catch (err) {
        showNotification('Error sending email.', 'error');
      }
    });
  }

  // Save Email Template
  document.querySelectorAll('.btn-save-email-template').forEach(btn => {
    btn.addEventListener('click', async () => {
      const key = btn.getAttribute('data-key');
      const subject = document.getElementById(`tpl-subject-${key}`)?.value;
      const body = document.getElementById(`tpl-body-${key}`)?.value;

      try {
        const formData = new FormData();
        formData.append('action', 'save_email_template');
        formData.append('template_key', key);
        formData.append('subject', subject);
        formData.append('body_html', body);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
        }
      } catch (err) {
        showNotification('Failed to save template.', 'error');
      }
    });
  });
}

// -----------------------------------------------------------------------------
// 13. Customer Reminder Handlers
// -----------------------------------------------------------------------------
function initReminderHandlers() {
  document.querySelectorAll('.btn-trigger-reminder').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-id');
      const phone = btn.getAttribute('data-phone');
      const channel = btn.getAttribute('data-channel');
      const msg = btn.getAttribute('data-message');

      if (channel === 'whatsapp') {
        const cleanPhone = phone.replace(/\D/g, '');
        const targetPhone = cleanPhone.startsWith('91') ? cleanPhone : ('91' + cleanPhone);
        const waUrl = `https://wa.me/${targetPhone}?text=${encodeURIComponent(msg)}`;
        window.open(waUrl, '_blank');
      }

      // Mark reminder sent in backend
      try {
        const formData = new FormData();
        formData.append('action', 'send_reminder');
        formData.append('id', id);
        await fetch('/api/admin.php', { method: 'POST', body: formData });
        showNotification(`Reminder dispatched!`, 'success');
        document.getElementById(`rem-status-${id}`)?.setAttribute('class', 'badge badge-success');
        const stSpan = document.getElementById(`rem-status-${id}`);
        if (stSpan) stSpan.textContent = 'Sent';
      } catch (err) {
        console.error(err);
      }
    });
  });

  // Schedule Custom Reminder Form
  const reminderForm = document.getElementById('newReminderForm');
  if (reminderForm) {
    reminderForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(reminderForm);
      formData.append('action', 'create_reminder');

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification('Reminder scheduled successfully!', 'success');
          setTimeout(() => window.location.reload(), 700);
        }
      } catch (err) {
        showNotification('Failed to schedule reminder.', 'error');
      }
    });
  }
}

// -----------------------------------------------------------------------------
// 14. Offers & CTA Management Handlers
// -----------------------------------------------------------------------------
function initOfferCtaHandlers() {
  // Save CTA Banners
  document.querySelectorAll('.offer-cta-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      formData.append('action', 'save_offer_cta');

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
        }
      } catch (err) {
        showNotification('Failed to save CTA.', 'error');
      }
    });
  });

  // Save Coupon
  const couponForm = document.getElementById('couponForm');
  if (couponForm) {
    couponForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(couponForm);
      formData.append('action', 'save_coupon');

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          setTimeout(() => window.location.reload(), 700);
        } else {
          showNotification(data.message || 'Coupon save failed.', 'error');
        }
      } catch (err) {
        showNotification('Communication error.', 'error');
      }
    });
  }

  // Delete Coupon
  document.querySelectorAll('.btn-delete-coupon').forEach(btn => {
    btn.addEventListener('click', async () => {
      const code = btn.getAttribute('data-code');
      if (confirm(`Delete coupon code ${code}?`)) {
        const formData = new FormData();
        formData.append('action', 'delete_coupon');
        formData.append('code', code);
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          document.getElementById(`coupon-row-${code}`)?.remove();
        }
      }
    });
  });
}

// -----------------------------------------------------------------------------
// Team Security, Superadmin User Management & Database Migration
// -----------------------------------------------------------------------------
function initTeamSecurityHandlers() {
  // 1. Logins Subnav Switching
  const loginSubBtns = document.querySelectorAll('.logins-subnav-btn[data-loginsub]');
  const loginSubPanels = document.querySelectorAll('.logins-section-panel');

  loginSubBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetSub = btn.getAttribute('data-loginsub');
      loginSubBtns.forEach(b => b.classList.toggle('active', b === btn));
      loginSubPanels.forEach(p => {
        const isTarget = (p.id === `loginsub-${targetSub}`);
        p.classList.toggle('active', isTarget);
        p.style.display = isTarget ? 'block' : 'none';
      });
    });
  });

  // 2. Open Add Team User Modal
  const openAddUserBtn = document.getElementById('btnOpenAddTeamUserModal');
  const teamUserModal = document.getElementById('teamUserModal');
  const teamUserForm = document.getElementById('teamUserForm');

  if (openAddUserBtn && teamUserModal && teamUserForm) {
    openAddUserBtn.addEventListener('click', () => {
      teamUserForm.reset();
      document.getElementById('teamUserId').value = '';
      document.getElementById('teamUserModalTitle').textContent = 'Add New Team Member';
      const userField = document.getElementById('teamUserUsername');
      if (userField) {
        userField.readOnly = false;
        userField.style.background = '#fff';
      }
      const pwdField = document.getElementById('teamUserPassword');
      if (pwdField) pwdField.required = true;
      const note = document.getElementById('teamUserPasswordNote');
      if (note) note.textContent = '(Required for new account, min. 6 chars)';
      teamUserModal.classList.add('active');
    });
  }

  // 3. Edit Team User (Event Delegation)
  document.addEventListener('click', (e) => {
    const editBtn = e.target.closest('.btn-edit-team-user');
    if (editBtn) {
      const dataStr = editBtn.getAttribute('data-user');
      if (!dataStr) return;
      try {
        const u = JSON.parse(dataStr);
        document.getElementById('teamUserId').value = u.id || '';
        document.getElementById('teamUserFullName').value = u.full_name || '';
        const userField = document.getElementById('teamUserUsername');
        if (userField) {
          userField.value = u.username || '';
          userField.readOnly = true;
          userField.style.background = '#f5f0ea';
        }
        document.getElementById('teamUserEmail').value = u.email || '';
        document.getElementById('teamUserRole').value = u.role || 'manager';
        document.getElementById('teamUserStatus').value = u.status || 'active';
        const pwdField = document.getElementById('teamUserPassword');
        if (pwdField) {
          pwdField.value = '';
          pwdField.required = false;
        }
        const note = document.getElementById('teamUserPasswordNote');
        if (note) note.textContent = '(Leave empty to keep current password unchanged)';
        document.getElementById('teamUserModalTitle').textContent = `Edit Team Member: ${u.username}`;
        teamUserModal?.classList.add('active');
      } catch (err) {
        console.error('Error parsing team user:', err);
      }
    }
  });

  // 4. Save Team User Submit
  if (teamUserForm) {
    teamUserForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(teamUserForm);
      formData.append('action', 'save_admin_user');

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          teamUserModal?.classList.remove('active');
          setTimeout(() => window.location.reload(), 700);
        } else {
          showNotification(data.message || 'Error saving team member.', 'error');
        }
      } catch (err) {
        showNotification('Communication error with server.', 'error');
      }
    });
  }

  // 5. Superadmin Direct Password Reset
  const resetModal = document.getElementById('superadminResetPasswordModal');
  const resetForm = document.getElementById('superadminResetPasswordForm');

  document.addEventListener('click', (e) => {
    const resetBtn = e.target.closest('.btn-reset-user-pwd');
    if (resetBtn) {
      const id = resetBtn.getAttribute('data-id');
      const username = resetBtn.getAttribute('data-username');
      if (resetForm) resetForm.reset();
      document.getElementById('resetTargetUserId').value = id;
      document.getElementById('resetTargetUserLabel').textContent = `${username} (ID: #${id})`;
      resetModal?.classList.add('active');
    }
  });

  if (resetForm) {
    resetForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const id = document.getElementById('resetTargetUserId').value;
      const nw = document.getElementById('resetTargetNewPassword').value;
      const conf = document.getElementById('resetTargetConfirmPassword').value;

      if (nw !== conf) {
        showNotification('New passwords do not match!', 'error');
        return;
      }
      if (nw.length < 6) {
        showNotification('Password must be at least 6 characters.', 'error');
        return;
      }

      try {
        const formData = new FormData();
        formData.append('action', 'superadmin_reset_password');
        formData.append('user_id', id);
        formData.append('new_password', nw);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          resetModal?.classList.remove('active');
        } else {
          showNotification(data.message || 'Password reset failed.', 'error');
        }
      } catch (err) {
        showNotification('Server error while resetting password.', 'error');
      }
    });
  }

  // 6. Toggle Team User Status
  document.addEventListener('click', async (e) => {
    const toggleBtn = e.target.closest('.btn-toggle-user-status');
    if (toggleBtn) {
      const id = toggleBtn.getAttribute('data-id');
      const username = toggleBtn.getAttribute('data-username');
      if (confirm(`Toggle access status for user "${username}"?`)) {
        try {
          const formData = new FormData();
          formData.append('action', 'toggle_admin_user_status');
          formData.append('id', id);

          const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
          const data = await res.json();
          if (data.success) {
            showNotification(data.message, 'success');
            const badge = document.getElementById(`user-status-badge-${id}`);
            if (badge) {
              const isAct = (data.status === 'active');
              badge.className = `badge ${isAct ? 'badge-success' : 'badge-danger'}`;
              badge.textContent = `● ${isAct ? 'Active' : 'Inactive'}`;
            }
          } else {
            showNotification(data.message || 'Failed to toggle status.', 'error');
          }
        } catch (err) {
          showNotification('Error toggling user status.', 'error');
        }
      }
    }
  });

  // 7. Delete Team Member
  document.addEventListener('click', async (e) => {
    const delBtn = e.target.closest('.btn-delete-team-user');
    if (delBtn) {
      const id = delBtn.getAttribute('data-id');
      const username = delBtn.getAttribute('data-username');
      if (confirm(`Permanently delete team account "${username}"? This cannot be undone.`)) {
        try {
          const formData = new FormData();
          formData.append('action', 'delete_admin_user');
          formData.append('id', id);

          const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
          const data = await res.json();
          if (data.success) {
            showNotification(data.message, 'success');
            document.getElementById(`team-user-row-${id}`)?.remove();
          } else {
            showNotification(data.message || 'Delete failed.', 'error');
          }
        } catch (err) {
          showNotification('Error deleting user account.', 'error');
        }
      }
    }
  });

  // 8. Toggle Customer Block / Active Status
  document.addEventListener('click', async (e) => {
    const toggleCustBtn = e.target.closest('.btn-toggle-customer-status');
    if (toggleCustBtn) {
      const id = toggleCustBtn.getAttribute('data-id');
      const name = toggleCustBtn.getAttribute('data-name');
      try {
        const formData = new FormData();
        formData.append('action', 'toggle_customer_status');
        formData.append('id', id);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          const isBlocked = (data.status === 'blocked');
          toggleCustBtn.textContent = isBlocked ? 'Unblock' : 'Block Access';
          const badge = document.getElementById(`cust-status-badge-${id}`);
          if (badge) {
            badge.className = `badge ${isBlocked ? 'badge-danger' : 'badge-success'}`;
            badge.textContent = `● ${isBlocked ? 'Blocked' : 'Active'}`;
          }
        } else {
          showNotification(data.message || 'Failed to toggle customer status.', 'error');
        }
      } catch (err) {
        showNotification('Error updating customer status.', 'error');
      }
    }
  });

  // 9. Login Audit Trail Filter
  const auditFilter = document.getElementById('loginAuditFilter');
  if (auditFilter) {
    auditFilter.addEventListener('change', () => {
      const val = auditFilter.value;
      document.querySelectorAll('.login-audit-row').forEach(row => {
        const act = row.getAttribute('data-action') || '';
        let show = true;
        if (val === 'PASSWORD') {
          show = act.includes('PASSWORD');
        } else if (val !== 'all') {
          show = (act === val);
        }
        row.style.display = show ? '' : 'none';
      });
    });
  }

  // 10. Self Change Password Form
  const pwdForm = document.getElementById('changePasswordForm');
  if (pwdForm) {
    pwdForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const cur = document.getElementById('currentPassword').value;
      const nw = document.getElementById('newPassword').value;
      const conf = document.getElementById('confirmNewPassword').value;

      if (nw !== conf) {
        showNotification('New passwords do not match!', 'error');
        return;
      }

      try {
        const formData = new FormData();
        formData.append('action', 'change_password');
        formData.append('current_password', cur);
        formData.append('new_password', nw);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message, 'success');
          pwdForm.reset();
        } else {
          showNotification(data.message || 'Password change failed.', 'error');
        }
      } catch (err) {
        showNotification('Error changing password.', 'error');
      }
    });
  }

  // DB Migration & Re-sync Wizard Trigger
  const migrateBtn = document.getElementById('btnRunDbMigration');
  if (migrateBtn) {
    migrateBtn.addEventListener('click', async () => {
      if (confirm('Run full database migration and sync tables with current JSON records?')) {
        migrateBtn.disabled = true;
        migrateBtn.textContent = 'Migrating...';
        try {
          const res = await fetch('/api/admin.php?action=run_db_migration');
          const data = await res.json();
          if (data.success) {
            showNotification('Database migration complete! Tables synced.', 'success');
            setTimeout(() => window.location.reload(), 800);
          } else {
            showNotification(data.message || 'Migration error.', 'error');
          }
        } catch (err) {
          showNotification('Migration request failed.', 'error');
        } finally {
          migrateBtn.disabled = false;
          migrateBtn.textContent = 'Run Database Sync';
        }
      }
    });
  }

  // Create API Key
  const createApiKeyBtn = document.getElementById('btnCreateApiKey');
  if (createApiKeyBtn) {
    createApiKeyBtn.addEventListener('click', async () => {
      const name = prompt('Enter a name for this API Key (e.g. "Shiprocket Production"):', 'Logistics Courier Webhook');
      if (name) {
        const formData = new FormData();
        formData.append('action', 'create_api_key');
        formData.append('key_name', name);
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification('API Key generated!', 'success');
          setTimeout(() => window.location.reload(), 700);
        }
      }
    });
  }

  // Delete API Key
  document.querySelectorAll('.btn-delete-api-key').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-id');
      if (confirm('Revoke this API Key? Incoming webhooks using it will be blocked.')) {
        const formData = new FormData();
        formData.append('action', 'delete_api_key');
        formData.append('id', id);
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification('API Key deleted.', 'success');
          document.getElementById(`apikey-row-${id}`)?.remove();
        }
      }
    });
  });

  // Close modals
  document.querySelectorAll('.modal-close-btn, .btn-modal-cancel').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.admin-modal-overlay').forEach(m => m.classList.remove('active'));
    });
  });

  // Close modals when clicking outside modal content
  document.querySelectorAll('.admin-modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        overlay.classList.remove('active');
      }
    });
  });
}

// -----------------------------------------------------------------------------
// Toast Notification Helper
// -----------------------------------------------------------------------------
function showNotification(msg, type = 'success') {
  if (typeof showToast === 'function') {
    showToast(msg, type);
    return;
  }
  const toast = document.createElement('div');
  toast.style.cssText = `
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: ${type === 'success' ? '#1b1510' : '#b91c1c'};
    color: #fff;
    border-left: 4px solid ${type === 'success' ? '#d4af37' : '#f87171'};
    padding: 14px 22px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    z-index: 9999;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    animation: fadeIn 0.3s ease;
  `;
  toast.textContent = msg;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.4s';
    setTimeout(() => toast.remove(), 400);
  }, 3500);
}

// =============================================================================
// STUDIO POWER-TOOLS, REAL-TIME NOTIFICATIONS, LOGISTICS & BULK WORKFLOWS
// =============================================================================
function initStudioPowerTools() {
  // 1. Interactive Topbar Notification Bell & Web Audio Alert Chime
  const notifBtn = document.getElementById('topbarNotificationBtn');
  const notifDropdown = document.getElementById('notificationDropdown');
  const btnChime = document.getElementById('btnTestChime');

  if (notifBtn && notifDropdown) {
    notifBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      notifDropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
      if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
        notifDropdown.classList.remove('active');
      }
    });
  }

  // Melodious synthesized Web Audio chime (Two-tone alert: A5 880Hz -> D6 1174.66Hz)
  function playStudioAlertChime() {
    try {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (!AudioCtx) return;
      const ctx = new AudioCtx();
      if (ctx.state === 'suspended') {
        ctx.resume();
      }
      const now = ctx.currentTime;

      // Note 1 (880 Hz)
      const osc1 = ctx.createOscillator();
      const gain1 = ctx.createGain();
      osc1.type = 'sine';
      osc1.frequency.setValueAtTime(880, now);
      gain1.gain.setValueAtTime(0.18, now);
      gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
      osc1.connect(gain1);
      gain1.connect(ctx.destination);
      osc1.start(now);
      osc1.stop(now + 0.35);

      // Note 2 (1174.66 Hz)
      const osc2 = ctx.createOscillator();
      const gain2 = ctx.createGain();
      osc2.type = 'sine';
      osc2.frequency.setValueAtTime(1174.66, now + 0.12);
      gain2.gain.setValueAtTime(0.22, now + 0.12);
      gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
      osc2.connect(gain2);
      gain2.connect(ctx.destination);
      osc2.start(now + 0.12);
      osc2.stop(now + 0.6);
    } catch (e) {
      console.warn('Web Audio chime could not play:', e);
    }
  }

  if (btnChime) {
    btnChime.addEventListener('click', (e) => {
      e.stopPropagation();
      playStudioAlertChime();
      showNotification('🔔 Studio alert audio chime tested successfully!', 'success');
    });
  }

  // 2. Floating Bulk Order Operations & Selection Bar
  const selectAllOrders = document.getElementById('selectAllOrdersCheckbox');
  const bulkBar = document.getElementById('bulkOrderActionBar');
  const bulkCountSpan = document.getElementById('bulkSelectedCount');
  const bulkStatusSelect = document.getElementById('bulkNewStatusSelect');
  const btnApplyBulkStatus = document.getElementById('btnApplyBulkStatus');
  const btnCancelBulkOrders = document.getElementById('btnCancelBulkOrders');

  function updateBulkOrderUI() {
    const checkedBoxes = document.querySelectorAll('.order-select-checkbox:checked');
    const count = checkedBoxes.length;
    if (bulkCountSpan) bulkCountSpan.textContent = count;
    if (bulkBar) {
      bulkBar.style.display = count > 0 ? 'block' : 'none';
    }
    if (selectAllOrders) {
      const allBoxes = document.querySelectorAll('.order-select-checkbox');
      selectAllOrders.checked = allBoxes.length > 0 && checkedBoxes.length === allBoxes.length;
    }
  }

  if (selectAllOrders) {
    selectAllOrders.addEventListener('change', () => {
      const isChecked = selectAllOrders.checked;
      document.querySelectorAll('.order-select-checkbox').forEach(cb => {
        cb.checked = isChecked;
      });
      updateBulkOrderUI();
    });
  }

  document.addEventListener('change', (e) => {
    if (e.target && e.target.classList.contains('order-select-checkbox')) {
      updateBulkOrderUI();
    }
  });

  if (btnCancelBulkOrders) {
    btnCancelBulkOrders.addEventListener('click', () => {
      document.querySelectorAll('.order-select-checkbox').forEach(cb => cb.checked = false);
      if (selectAllOrders) selectAllOrders.checked = false;
      updateBulkOrderUI();
    });
  }

  if (btnApplyBulkStatus) {
    btnApplyBulkStatus.addEventListener('click', async () => {
      const newStatus = bulkStatusSelect ? bulkStatusSelect.value.trim() : '';
      if (!newStatus) {
        showNotification('Please choose a new status from the dropdown.', 'error');
        return;
      }
      const selectedBoxes = document.querySelectorAll('.order-select-checkbox:checked');
      const orderIds = Array.from(selectedBoxes).map(cb => cb.value);
      if (orderIds.length === 0) {
        showNotification('No orders selected.', 'error');
        return;
      }

      btnApplyBulkStatus.disabled = true;
      btnApplyBulkStatus.textContent = 'Updating...';

      try {
        const formData = new FormData();
        formData.append('action', 'bulk_update_order_status');
        formData.append('status', newStatus);
        formData.append('order_ids', JSON.stringify(orderIds));

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message || 'Orders updated successfully!', 'success');
          setTimeout(() => window.location.reload(), 700);
        } else {
          showNotification(data.message || 'Bulk status update failed.', 'error');
        }
      } catch (err) {
        showNotification('Communication error during bulk update.', 'error');
      } finally {
        btnApplyBulkStatus.disabled = false;
        btnApplyBulkStatus.textContent = 'Update Selected';
      }
    });
  }

  // 3. 1-Click WhatsApp Order Tracking Dispatch
  document.addEventListener('click', (e) => {
    const waBtn = e.target.closest('.btn-whatsapp-order');
    if (waBtn) {
      e.preventDefault();
      const id = waBtn.getAttribute('data-id') || '';
      const rawPhone = waBtn.getAttribute('data-phone') || '';
      const name = waBtn.getAttribute('data-name') || 'Patron';
      const status = waBtn.getAttribute('data-status') || 'Confirmed';
      const carrier = waBtn.getAttribute('data-carrier') || 'Studio Express Courier';
      const awb = waBtn.getAttribute('data-awb') || 'In Studio Dispatch Processing';

      let cleanPhone = rawPhone.replace(/\D/g, '');
      if (cleanPhone.length === 10) cleanPhone = '91' + cleanPhone;

      if (!cleanPhone) {
        showNotification('Patron phone number is missing.', 'error');
        return;
      }

      const msg = `Namaste ${name} ji! 🙏 Warm greetings from Murli Creations Lippan Art Studio.\n\nYour handcrafted sacred artwork order #${id} is currently: *${status}*.\n\n📦 Logistics Courier: ${carrier}\n🔖 Consignment / AWB: ${awb}\n\nTrack your parcel anytime with our live tracking tool:\nhttps://murlicreations.com/track-order.php?orderId=${encodeURIComponent(id)}\n\nThank you for bringing divine heritage artwork into your sacred space! ✨\nMurli Creations Master Studio`;

      const waUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(msg)}`;
      window.open(waUrl, '_blank');
    }
  });

  // 4. 4x6" Thermal Shipping Parcel Label Modal & Print Handler
  const thermalModal = document.getElementById('shippingLabelModal');
  const btnPrintThermalNow = document.getElementById('btnPrintThermalLabelNow');

  document.addEventListener('click', (e) => {
    const labelBtn = e.target.closest('.btn-print-shipping-label');
    if (labelBtn) {
      e.preventDefault();
      const orderJsonStr = labelBtn.getAttribute('data-order') || '{}';
      try {
        const order = JSON.parse(orderJsonStr);
        const cust = order.customer || order.shippingAddress || {};
        const awb = order.tracking_number || order.awb || ('MURLI-' + (order.id || 'AWB-1001'));
        const name = cust.name || cust.fullName || 'Patron';
        const address = cust.address || cust.addressLine1 || 'Delivery Address';
        const city = (cust.city || 'India').toUpperCase();
        const state = (cust.state || '').toUpperCase();
        const pin = cust.pinCode || cust.pin || cust.postalCode || '';
        const phone = cust.phone || '9876543210';
        const amount = Number(order.totalAmount || order.total || order.totalPayable || 0);
        const payMethod = order.paymentMethod || 'Prepaid';

        document.getElementById('lblAwbNumber').textContent = `AWB: ${awb}`;
        document.getElementById('lblCustomerName').textContent = name;
        document.getElementById('lblCustomerAddress').textContent = address;
        document.getElementById('lblCustomerCityPin').textContent = `${city}${state ? ', ' + state : ''}${pin ? ' - ' + pin : ''}`;
        document.getElementById('lblCustomerPhone').textContent = `📞 Phone: ${phone}`;
        document.getElementById('lblOrderId').textContent = `Order #${order.id || ''}`;
        document.getElementById('lblOrderAmount').textContent = `Total Amount: ₹${amount.toLocaleString()} (${payMethod})`;

        if (thermalModal) thermalModal.classList.add('active');
      } catch (err) {
        console.error('Error opening thermal label:', err);
        showNotification('Could not format thermal label data.', 'error');
      }
    }
  });

  if (btnPrintThermalNow) {
    btnPrintThermalNow.addEventListener('click', () => {
      window.print();
    });
  }

  // 5. Catalog Power-Tools: Bulk CSV Import & Festive Discount Modals
  const btnOpenCsvImport = document.getElementById('btnOpenBulkImportModal');
  const modalCsvImport = document.getElementById('bulkCsvImportModal');
  const formCsvImport = document.getElementById('formBulkCsvImport');

  if (btnOpenCsvImport && modalCsvImport) {
    btnOpenCsvImport.addEventListener('click', () => {
      modalCsvImport.classList.add('active');
    });
  }

  if (formCsvImport) {
    formCsvImport.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formCsvImport);
      formData.append('action', 'import_products_csv');

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message || 'Catalog imported successfully!', 'success');
          modalCsvImport?.classList.remove('active');
          setTimeout(() => window.location.reload(), 700);
        } else {
          showNotification(data.message || 'CSV import failed.', 'error');
        }
      } catch (err) {
        showNotification('Communication error during CSV import.', 'error');
      }
    });
  }

  // 5. Category & Subcategory Bulk Discount Manager Handlers
  const btnOpenFestive = document.getElementById('btnOpenFestiveDiscountModal');
  const modalFestive = document.getElementById('festiveDiscountModal');
  const formFestive = document.getElementById('formFestiveDiscount');
  const discountCatSelect = document.getElementById('discountCategorySelect');
  const discountSubcatSelect = document.getElementById('discountSubcategorySelect');
  const discountInput = document.getElementById('discountPercentInput');
  const discountScopeText = document.getElementById('discountScopeText');
  const discountScopePercent = document.getElementById('discountScopePercent');

  const updateDiscountScopeDisplay = () => {
    if (!discountCatSelect || !discountInput) return;
    const catVal = discountCatSelect.value;
    const subcatVal = discountSubcatSelect ? discountSubcatSelect.value : 'all';
    const percentVal = parseInt(discountInput.value || '0', 10);

    const catData = (window.MURLI_CATEGORIES && catVal !== 'all') ? window.MURLI_CATEGORIES[catVal] : null;
    const catDisplayName = catData ? (catData.name || catVal) : catVal;

    let matchedCount = 0;
    if (window.MURLI_PRODUCTS) {
      matchedCount = window.MURLI_PRODUCTS.filter(p => {
        const pCat = p.category || '';
        const matchCat = (catVal === 'all') || (pCat === catVal) || (catData && pCat === catData.name);
        if (!matchCat) return false;
        if (subcatVal !== 'all') {
          return p.subcategory === subcatVal;
        }
        return true;
      }).length;
    }

    if (discountScopeText) {
      if (catVal === 'all') {
        discountScopeText.innerHTML = `All <strong>${matchedCount} artworks</strong> across the entire studio catalog`;
      } else if (subcatVal === 'all') {
        discountScopeText.innerHTML = `All <strong>${matchedCount} artworks</strong> in <em>${catDisplayName}</em>`;
      } else {
        discountScopeText.innerHTML = `<strong>${matchedCount} artworks</strong> in <em>${catDisplayName} &gt; ${subcatVal}</em>`;
      }
    }

    if (discountScopePercent) {
      if (percentVal <= 0) {
        discountScopePercent.innerHTML = `<span style="color:#dc2626;">Reset to Standard Catalog MRP</span>`;
      } else {
        discountScopePercent.innerHTML = `<span style="color:#15803d;">${percentVal}% OFF</span>`;
      }
    }
  };

  const populateDiscountSubcategories = () => {
    if (!discountCatSelect || !discountSubcatSelect) return;
    const catVal = discountCatSelect.value;
    discountSubcatSelect.innerHTML = '';

    if (catVal === 'all') {
      const opt = document.createElement('option');
      opt.value = 'all';
      opt.textContent = 'All Subcategories in Entire Studio Catalog';
      discountSubcatSelect.appendChild(opt);
      discountSubcatSelect.disabled = true;
    } else {
      discountSubcatSelect.disabled = false;
      const optAll = document.createElement('option');
      optAll.value = 'all';
      optAll.textContent = 'All Subcategories in this Collection';
      discountSubcatSelect.appendChild(optAll);

      const catData = window.MURLI_CATEGORIES ? window.MURLI_CATEGORIES[catVal] : null;
      const subcats = new Set();
      if (catData && catData.subcategories) {
        Object.keys(catData.subcategories).forEach(s => subcats.add(s));
      }
      if (window.MURLI_PRODUCTS) {
        window.MURLI_PRODUCTS.forEach(p => {
          const pCat = p.category || '';
          const match = (pCat === catVal) || (catData && pCat === catData.name);
          if (match && p.subcategory) subcats.add(p.subcategory);
        });
      }

      subcats.forEach(sub => {
        let count = 0;
        if (window.MURLI_PRODUCTS) {
          count = window.MURLI_PRODUCTS.filter(p => {
            const pCat = p.category || '';
            const match = (pCat === catVal) || (catData && pCat === catData.name);
            return match && (p.subcategory === sub);
          }).length;
        }
        const opt = document.createElement('option');
        opt.value = sub;
        opt.textContent = `${sub} (${count} artworks)`;
        discountSubcatSelect.appendChild(opt);
      });
    }

    updateDiscountScopeDisplay();
  };

  if (discountCatSelect) {
    discountCatSelect.addEventListener('change', populateDiscountSubcategories);
  }
  if (discountSubcatSelect) {
    discountSubcatSelect.addEventListener('change', updateDiscountScopeDisplay);
  }

  // Quick Preset Chips (5%, 10%, 15%, 20%, 25%, 30%, 50%, Reset)
  document.querySelectorAll('.btn-preset-discount').forEach(chip => {
    chip.addEventListener('click', (e) => {
      e.preventDefault();
      document.querySelectorAll('.btn-preset-discount').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      const val = chip.getAttribute('data-discount') || '0';
      if (discountInput) {
        discountInput.value = val;
      }
      updateDiscountScopeDisplay();
    });
  });

  if (discountInput) {
    discountInput.addEventListener('input', () => {
      const val = discountInput.value;
      document.querySelectorAll('.btn-preset-discount').forEach(c => {
        if (c.getAttribute('data-discount') === val) {
          c.classList.add('active');
        } else {
          c.classList.remove('active');
        }
      });
      updateDiscountScopeDisplay();
    });
  }

  if (btnOpenFestive && modalFestive) {
    btnOpenFestive.addEventListener('click', () => {
      populateDiscountSubcategories();
      updateDiscountScopeDisplay();
      modalFestive.classList.add('active');
    });
  }

  if (formFestive) {
    formFestive.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = formFestive.querySelector('button[type="submit"]');
      const originalText = submitBtn ? submitBtn.innerHTML : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="material-symbols-outlined" style="animation:spin 1s linear infinite; vertical-align:middle; font-size:18px;">progress_activity</span> Applying Bulk Discount...`;
      }

      const formData = new FormData(formFestive);
      formData.append('action', 'apply_bulk_discount');

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message || 'Category discount applied successfully!', 'success');
          modalFestive?.classList.remove('active');
          setTimeout(() => window.location.reload(), 600);
        } else {
          showNotification(data.message || 'Failed to apply discount.', 'error');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
        }
      } catch (err) {
        showNotification('Server communication error.', 'error');
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
      }
    });
  }

  // 6. Customer Reviews & Testimonials Hub Handlers
  const btnOpenAddReview = document.getElementById('btnOpenAddReviewModal');
  const modalAddReview = document.getElementById('addReviewModal');
  const formAddReview = document.getElementById('formAddReview');

  if (btnOpenAddReview && modalAddReview) {
    btnOpenAddReview.addEventListener('click', () => {
      modalAddReview.classList.add('active');
    });
  }

  if (formAddReview) {
    formAddReview.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formAddReview);
      formData.append('action', 'save_review');

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message || 'Patron review added successfully!', 'success');
          modalAddReview?.classList.remove('active');
          formAddReview.reset();
          setTimeout(() => window.location.reload(), 700);
        } else {
          showNotification(data.message || 'Error saving review.', 'error');
        }
      } catch (err) {
        showNotification('Communication error.', 'error');
      }
    });
  }

  // Toggle Live / Pending Status of Customer Reviews
  document.addEventListener('click', async (e) => {
    const toggleBtn = e.target.closest('.btn-toggle-review-status');
    if (toggleBtn) {
      e.preventDefault();
      const reviewId = toggleBtn.getAttribute('data-id');
      if (!reviewId) return;

      try {
        const formData = new FormData();
        formData.append('action', 'toggle_review_status');
        formData.append('id', reviewId);

        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message || 'Review status updated!', 'success');
          const pill = document.getElementById(`rev-status-pill-${reviewId}`);
          const row = document.getElementById(`review-row-${reviewId}`);
          if (pill) {
            const isApproved = data.is_approved === 1;
            pill.className = `stock-pill ${isApproved ? 'in-stock' : 'out-of-stock'}`;
            pill.textContent = isApproved ? '● Live on Store' : '● Hidden / Pending';
            if (row) {
              row.setAttribute('data-status', isApproved ? 'approved' : 'pending');
            }
          }
        } else {
          showNotification(data.message || 'Could not toggle review status.', 'error');
        }
      } catch (err) {
        showNotification('Error updating review status.', 'error');
      }
    }
  });

  // Permanently Delete Review
  document.addEventListener('click', async (e) => {
    const delBtn = e.target.closest('.btn-delete-review');
    if (delBtn) {
      e.preventDefault();
      const reviewId = delBtn.getAttribute('data-id');
      if (!reviewId) return;

      if (confirm('Permanently delete this patron testimonial? This cannot be undone.')) {
        try {
          const formData = new FormData();
          formData.append('action', 'delete_review');
          formData.append('id', reviewId);

          const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
          const data = await res.json();
          if (data.success) {
            showNotification(data.message || 'Review deleted.', 'success');
            const row = document.getElementById(`review-row-${reviewId}`);
            if (row) {
              row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
              row.style.opacity = '0';
              row.style.transform = 'translateX(20px)';
              setTimeout(() => row.remove(), 300);
            }
          } else {
            showNotification(data.message || 'Failed to delete review.', 'error');
          }
        } catch (err) {
          showNotification('Error deleting review.', 'error');
        }
      }
    }
  });

  // Review Search & Status Filter
  const revSearchInput = document.getElementById('reviewSearchInput');
  const revStatusFilter = document.getElementById('reviewStatusFilter');

  function filterReviews() {
    const term = revSearchInput ? revSearchInput.value.toLowerCase().trim() : '';
    const st = revStatusFilter ? revStatusFilter.value : 'all';

    document.querySelectorAll('.admin-review-row').forEach(row => {
      const name = row.getAttribute('data-name') || '';
      const comment = row.getAttribute('data-comment') || '';
      const rowStatus = row.getAttribute('data-status') || '';

      const matchTerm = !term || name.includes(term) || comment.includes(term);
      const matchSt = (st === 'all') || (rowStatus === st);

      row.style.display = (matchTerm && matchSt) ? '' : 'none';
    });
  }

  if (revSearchInput) revSearchInput.addEventListener('input', filterReviews);
  if (revStatusFilter) revStatusFilter.addEventListener('change', filterReviews);

  // 7. Media Asset Library 1-Click "Copy URL" Handler
  document.addEventListener('click', async (e) => {
    const copyBtn = e.target.closest('.btn-copy-media-url');
    if (copyBtn) {
      e.preventDefault();
      const url = copyBtn.getAttribute('data-url') || '';
      const fullUrl = url.startsWith('http') ? url : (window.location.origin + url);

      try {
        if (navigator.clipboard && navigator.clipboard.writeText) {
          await navigator.clipboard.writeText(fullUrl);
        } else {
          const tempInput = document.createElement('input');
          tempInput.value = fullUrl;
          document.body.appendChild(tempInput);
          tempInput.select();
          document.execCommand('copy');
          tempInput.remove();
        }

        const originalText = copyBtn.innerHTML;
        copyBtn.innerHTML = '✓ Copied!';
        copyBtn.style.background = '#15803d';
        copyBtn.style.color = '#ffffff';
        copyBtn.style.borderColor = '#15803d';

        showNotification('Media asset URL copied to clipboard!', 'success');

        setTimeout(() => {
          copyBtn.innerHTML = originalText;
          copyBtn.style.background = '';
          copyBtn.style.color = '';
          copyBtn.style.borderColor = '';
        }, 2200);
      } catch (err) {
        showNotification('Could not copy URL to clipboard.', 'error');
      }
    }
  });

  // 8. Studio Holiday / Vacation Notice Form Handler
  const formHoliday = document.getElementById('formHolidayNotice');
  if (formHoliday) {
    formHoliday.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formHoliday);

      try {
        const res = await fetch('/api/admin.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          showNotification(data.message || 'Studio holiday announcement banner saved!', 'success');
        } else {
          showNotification(data.message || 'Error saving holiday notice.', 'error');
        }
      } catch (err) {
        showNotification('Server communication error.', 'error');
      }
    });
  }

  // 9. Interactive Live Storefront Split Preview Synchronizer & Device Toggle
  const deviceScreen = document.getElementById('deviceScreenWrapper');
  const previewStatus = document.getElementById('previewStatusText');

  // Device Toggle Buttons (Desktop vs Mobile 375px)
  document.querySelectorAll('.btn-device-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.btn-device-toggle').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const dev = btn.getAttribute('data-device');
      if (deviceScreen) {
        if (dev === 'mobile') {
          deviceScreen.classList.add('mobile-view');
        } else {
          deviceScreen.classList.remove('mobile-view');
        }
      }
    });
  });

  // Real-Time WYSIWYG Synchronizer for Home Copy & Festive Offer
  function syncHomePreview() {
    const getVal = (id, fallback = '') => {
      const el = document.getElementById(id);
      return (el && el.value.trim()) ? el.value.trim() : fallback;
    };

    // Hero Badge
    const elHeroBadge = document.getElementById('prevHeroBadge');
    if (elHeroBadge) elHeroBadge.textContent = getVal('cfg_hero_badge', 'Direct Studio Helpline & WhatsApp Support');

    // Hero Title Main & Highlight
    const elHeroTitleMain = document.getElementById('prevHeroTitleMain');
    if (elHeroTitleMain) elHeroTitleMain.textContent = getVal('cfg_hero_title_main', 'Authentic Handcrafted Lippan Art for');

    const elHeroTitleHighlight = document.getElementById('prevHeroTitleHighlight');
    if (elHeroTitleHighlight) elHeroTitleHighlight.textContent = getVal('cfg_hero_title_highlight', 'Soulful Living Spaces');

    // Hero Description
    const elHeroDesc = document.getElementById('prevHeroDesc');
    if (elHeroDesc) elHeroDesc.textContent = getVal('cfg_hero_description', 'Bring auspicious energy and timeless Indian grandeur into your home.');

    // Hero Action Buttons
    const elHeroCtaPrimary = document.getElementById('prevHeroCtaPrimary');
    if (elHeroCtaPrimary) elHeroCtaPrimary.innerHTML = `${getVal('cfg_hero_cta_primary', 'Explore Catalog')} &rarr;`;

    const elHeroCtaSecondary = document.getElementById('prevHeroCtaSecondary');
    if (elHeroCtaSecondary) elHeroCtaSecondary.textContent = getVal('cfg_hero_cta_secondary', 'About Our Studio');

    // Special Festive Offer Card
    const festiveOfferEnabled = document.getElementById('cfg_festive_offer_enabled')?.checked ?? true;
    const prevFestiveSection = document.getElementById('prevFestiveOfferSection');
    if (prevFestiveSection) {
      prevFestiveSection.style.display = festiveOfferEnabled ? 'block' : 'none';
    }

    const elOfferBadge = document.getElementById('prevOfferBadge');
    if (elOfferBadge) elOfferBadge.textContent = getVal('cfg_festive_offer_badge', 'Special Festive Celebration');

    const elOfferDiscount = document.getElementById('prevOfferDiscount');
    if (elOfferDiscount) elOfferDiscount.textContent = getVal('cfg_festive_offer_discount', '10% FLAT OFF');

    const elOfferTitle = document.getElementById('prevOfferTitle');
    if (elOfferTitle) elOfferTitle.textContent = getVal('cfg_festive_offer_title', 'Claim 10% Off On Your First Handcrafted Artwork');

    const elOfferDesc = document.getElementById('prevOfferDesc');
    if (elOfferDesc) elOfferDesc.textContent = getVal('cfg_festive_offer_desc', 'Every creation comes with certified authenticity and authentic brass hooks ready to grace your walls.');

    const elOfferCode = document.getElementById('prevOfferCode');
    if (elOfferCode) elOfferCode.textContent = `Copy Code: ${getVal('cfg_festive_offer_coupon', 'MURLI10')}`;

    const elOfferBtn = document.getElementById('prevOfferBtn');
    if (elOfferBtn) elOfferBtn.innerHTML = `${getVal('cfg_festive_offer_btn', 'Shop Auspicious Masterpieces')} &rarr;`;

    // Story Quote
    const elStoryTitle = document.getElementById('prevStoryTitle');
    if (elStoryTitle) elStoryTitle.textContent = getVal('cfg_story_title', "From a Mother's Hands to Murli Creations");

    const elStoryQuote = document.getElementById('prevStoryQuote');
    if (elStoryQuote) elStoryQuote.textContent = `"${getVal('cfg_story_quote', 'What started with watching my mother create with her hands became my own journey of creating with love.')}"`;

    const elStoryAuthor = document.getElementById('prevStoryAuthor');
    if (elStoryAuthor) elStoryAuthor.textContent = `— ${getVal('cfg_story_author', 'Murli Creations')}`;

    // Pulse feedback
    if (previewStatus) {
      previewStatus.textContent = 'Live Preview Synced';
    }
  }

  // Attach live event listeners to all triggers
  document.querySelectorAll('.live-preview-trigger').forEach(input => {
    input.addEventListener('input', syncHomePreview);
    input.addEventListener('change', syncHomePreview);
  });

  // 10. 1-Click Festive Campaign Presets
  const festivePresets = {
    diwali: {
      hero_badge: '🪔 Grand Diwali Mahotsav 2026 🪔',
      hero_title_main: 'Illuminate Your Sacred Home with Auspicious',
      hero_title_highlight: 'Deepavali Lippan Art & Mandir Decor',
      hero_description: 'Celebrate the divine festival of lights with heirloom handcrafted mud & mirror masterpieces. Each piece sanctified with authentic glass mirror reflections to invite Lakshmi and Ganesha blessings into your sanctuary.',
      hero_cta_primary_text: 'Explore Diwali Curation',
      festive_offer_enabled: true,
      festive_offer_badge: '🪔 Deepotsav Grand Offer 🪔',
      festive_offer_discount_label: '15% FLAT OFF',
      festive_offer_title: 'Grand Diwali Celebration: Flat 15% OFF + Free Safe Packaging',
      festive_offer_description: 'Apply code DIWALI15 at checkout to receive an instant 15% discount across all handcrafted mirrors, wall jharokhas & pooja mandir decor.',
      festive_offer_coupon_code: 'DIWALI15',
      festive_offer_button_text: 'Claim Diwali Discount',
      story_quote: 'May every sacred mirror in your home reflect health, prosperity, and divine joy this Deepavali.',
      story_author: 'Master Craftsman Murli Prasad',
      story_title: "Diwali Blessings from Our Master Studio"
    },
    shyamji: {
      hero_badge: '🦚 Shri Khatu Shyam Baba Phalguna Mela Special 🦚',
      hero_title_main: 'Bask in the Divine Blessings & Grace of',
      hero_title_highlight: 'Khatu Ke Shyam Pyare Decor',
      hero_description: 'Handcrafted with unyielding devotion, sparkling glass mirror inlays, and traditional sacred clay relief work. Specially sculpted for pooja mandirs, prayer rooms, and devotional living focal points.',
      hero_cta_primary_text: 'Explore Shyam Ji Decor',
      festive_offer_enabled: true,
      festive_offer_badge: '🦚 Shyam Kripa Mahotsav 🦚',
      festive_offer_discount_label: '10% FLAT OFF',
      festive_offer_title: 'Jai Shri Shyam: 10% Blessing on All Devotional Decor',
      festive_offer_description: 'Celebrate Baba Shyam’s grace with authentic temple frames, peacock kalangi accents, and sacred shringar wall decor.',
      festive_offer_coupon_code: 'SHYAM10',
      festive_offer_button_text: 'Shop Shyam Devotional Art',
      story_quote: 'Creating devotional artworks for Shyam Baba is not just craft, it is our humble prayer offered with love.',
      story_author: 'Murli Creations Studio',
      story_title: "Inspired by Shyam Baba's Divine Grace"
    },
    navratri: {
      hero_badge: '🌺 Shubh Navratri & Durga Mahotsav 🌺',
      hero_title_main: 'Welcome Divine Feminine Shakti with',
      hero_title_highlight: 'Vibrant Auspicious Mandalas',
      hero_description: 'Symmetrical sacred geometry, vibrant festive earthen colors, and sparkling mirror work crafted to bring boundless positive energy, abundance, and festive harmony into your household.',
      hero_cta_primary_text: 'Explore Navratri Curations',
      festive_offer_enabled: true,
      festive_offer_badge: '🌺 Navratri Celebration 🌺',
      festive_offer_discount_label: '10% FLAT OFF',
      festive_offer_title: 'Celebrate 9 Divine Nights with Handcrafted Sacred Art',
      festive_offer_description: 'Receive an auspicious 10% discount on all circular mandala wall plates and temple frames with code NAVRATRI10.',
      festive_offer_coupon_code: 'NAVRATRI10',
      festive_offer_button_text: 'Shop Auspicious Mandalas',
      story_quote: 'Every stroke of clay and mirror inlay is an ode to the divine shakti that protects and sanctifies Indian homes.',
      story_author: 'Master Craftsman Murli Prasad',
      story_title: "A Celebration of Sacred Traditions"
    },
    grihapravesh: {
      hero_badge: '🏡 Shubh Griha Pravesh & Housewarming Curation 🏡',
      hero_title_main: 'Sanctify Your New Living Abode with',
      hero_title_highlight: 'Vastu Positive Mirror Artwork',
      hero_description: 'Traditional Indian Lippan clay relief mirror art cleanses negative energy, enhances natural light, and radiates positivity. The perfect timeless housewarming blessing and living room centerpiece.',
      hero_cta_primary_text: 'Explore Vastu Collection',
      festive_offer_enabled: true,
      festive_offer_badge: '🏡 Griha Pravesh Special 🏡',
      festive_offer_discount_label: '10% FLAT OFF',
      festive_offer_title: 'New Home Blessing: Flat 10% OFF on Luxury Wall Art',
      festive_offer_description: 'Adorn your new pooja mandir or entryway with certified authentic artisan Lippan art. Use code NEWLOOK10.',
      festive_offer_coupon_code: 'NEWLOOK10',
      festive_offer_button_text: 'Claim Housewarming Offer',
      story_quote: 'A house becomes a temple of peace when graced with handcrafted devotion and authentic heritage art.',
      story_author: 'Murli Creations',
      story_title: "Artisan Heritage for Your New Home"
    },
    default: {
      hero_badge: 'Direct Studio Helpline & WhatsApp Support',
      hero_title_main: 'Authentic Handcrafted Lippan Art for',
      hero_title_highlight: 'Soulful Living Spaces',
      hero_description: 'Bring auspicious energy and timeless Indian grandeur into your home. Master artisan Murli Prasad blends sacred mud clay, precision mirrors, and vibrant devotions into heirloom-grade wall masterpieces.',
      hero_cta_primary_text: 'Explore Catalog',
      festive_offer_enabled: true,
      festive_offer_badge: 'Special Festive Celebration',
      festive_offer_discount_label: '10% FLAT OFF',
      festive_offer_title: 'Claim 10% Off On Your First Handcrafted Artwork',
      festive_offer_description: 'Every creation comes with certified authenticity, transit-insured bespoke packaging, and authentic brass hooks ready to grace your walls.',
      festive_offer_coupon_code: 'MURLI10',
      festive_offer_button_text: 'Shop Auspicious Masterpieces',
      story_quote: 'What started with watching my mother create with her hands became my own journey of creating with love.',
      story_author: 'Murli Creations',
      story_title: "From a Mother's Hands to Murli Creations"
    }
  };

  document.querySelectorAll('.btn-preset-theme').forEach(btn => {
    btn.addEventListener('click', () => {
      const presetKey = btn.getAttribute('data-preset');
      const p = festivePresets[presetKey];
      if (!p) return;

      const setInput = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val;
      };

      setInput('cfg_hero_badge', p.hero_badge);
      setInput('cfg_hero_title_main', p.hero_title_main);
      setInput('cfg_hero_title_highlight', p.hero_title_highlight);
      setInput('cfg_hero_description', p.hero_description);
      setInput('cfg_hero_cta_primary', p.hero_cta_primary_text);

      const chk = document.getElementById('cfg_festive_offer_enabled');
      if (chk) chk.checked = !!p.festive_offer_enabled;

      setInput('cfg_festive_offer_badge', p.festive_offer_badge);
      setInput('cfg_festive_offer_discount', p.festive_offer_discount_label);
      setInput('cfg_festive_offer_title', p.festive_offer_title);
      setInput('cfg_festive_offer_desc', p.festive_offer_description);
      setInput('cfg_festive_offer_coupon', p.festive_offer_coupon_code);
      setInput('cfg_festive_offer_btn', p.festive_offer_button_text);

      setInput('cfg_story_quote', p.story_quote);
      setInput('cfg_story_author', p.story_author);
      setInput('cfg_story_title', p.story_title);

      syncHomePreview();
      showNotification(`🎉 Loaded "${btn.textContent.trim()}" campaign preset! Preview updated.`, 'success');
    });
  });

  // 11. Live Holiday Announcement Bar Preview Synchronizer
  const holidayText = document.getElementById('cfg_holiday_banner_text');
  const holidayPromo = document.getElementById('cfg_holiday_promo_code');
  const holidayBadge = document.getElementById('cfg_holiday_badge_tag');
  const holidayLiveBadge = document.getElementById('holidayLiveBadge');
  const holidayLiveMessage = document.getElementById('holidayLiveMessage');

  function syncHolidayPreview() {
    if (holidayLiveBadge && holidayBadge) {
      holidayLiveBadge.textContent = holidayBadge.value || '✨ Special Celebration ✨';
    }
    if (holidayLiveMessage && holidayText) {
      const code = (holidayPromo ? holidayPromo.value : 'MURLI10') || 'MURLI10';
      const rawText = holidayText.value || 'Festive Offer: Use code {coupon} for 10% OFF on all orders!';
      holidayLiveMessage.innerHTML = rawText.replace(
        /\{coupon\}/g,
        `<strong style="color:#ffd700; text-decoration:underline;">${code}</strong>`
      );
    }
  }

  if (holidayText) holidayText.addEventListener('input', syncHolidayPreview);
  if (holidayPromo) holidayPromo.addEventListener('input', syncHolidayPreview);
  if (holidayBadge) holidayBadge.addEventListener('input', syncHolidayPreview);
}
