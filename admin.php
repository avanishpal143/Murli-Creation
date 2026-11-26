<?php
// =============================================================================
// Murli Creations - Minimal Luxury Studio Administration Portal
// Clean, aesthetic, distraction-free administration dashboard
// Exact Tab Architecture Requested:
// 1. Product Management (Visual Cards like Frontend + Table View)
// 2. Order Tracking/Approval/status
// 3. Customer reminders/ (Customer Reminders, CRM Leads, User Details)
// 4. Meta Tags management per page wise
// 5. Email communication
// 6. offer management
// 7. Logins Management (Team, Security, MySQL Database)
// =============================================================================

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . '/config/functions.php';

$is_logged_in = !empty($_SESSION['mc_admin_logged_in']);
$db_connected = is_db_connected();

// Fetch Data for All Modules
$products = function_exists('db_get_all_products') ? db_get_all_products() : get_all_products();
$orders = function_exists('db_get_all_orders') ? db_get_all_orders() : get_all_orders();
$customers = function_exists('db_get_all_customers') ? db_get_all_customers() : [];
$crm_leads = function_exists('db_get_crm_leads') ? db_get_crm_leads() : [];
$seo_pages = function_exists('db_get_all_seo_pages') ? db_get_all_seo_pages() : [];
$email_templates = function_exists('db_get_email_templates') ? db_get_email_templates() : [];
$reminders = function_exists('db_get_reminders') ? db_get_reminders() : [];
$offers_cta = function_exists('db_get_offers_cta') ? db_get_offers_cta() : [];
$api_keys = function_exists('db_get_api_keys') ? db_get_api_keys() : [];
$admin_users = function_exists('db_get_admin_users') ? db_get_admin_users() : [];
$admin_logs = function_exists('db_get_admin_logs') ? db_get_admin_logs(20) : [];
$login_audit_trail = function_exists('db_get_login_audit_trail') ? db_get_login_audit_trail(60) : [];
$coupons = get_all_coupons();
$categories = get_category_tree();
$contact_inquiries = function_exists('db_get_contact_inquiries') ? db_get_contact_inquiries() : [];
$site_settings = get_site_settings();
$homepage_content = get_homepage_content();

$admin_reviews = function_exists('db_get_all_admin_reviews') ? db_get_all_admin_reviews('all') : get_all_reviews();
$abandoned_carts = function_exists('db_get_abandoned_carts') ? db_get_abandoned_carts() : [];
$media_assets = function_exists('db_list_media_assets') ? db_list_media_assets() : [];
$analytics_data = function_exists('db_get_analytics_summary') ? db_get_analytics_summary() : [];

// Operational Metric Counts
$totalRevenue = 0;
$totalOrders = count($orders);
$totalProducts = count($products);
$lowStockCount = 0;
$pendingOrdersCount = 0;
$newLeadsCount = 0;
$newInquiriesCount = 0;
$pendingReviewsCount = 0;

foreach ($admin_reviews as $rev) {
    if (isset($rev['is_approved']) && (int)$rev['is_approved'] === 0) {
        $pendingReviewsCount++;
    }
}
$abandonedCartsCount = count($abandoned_carts);

foreach ($contact_inquiries as $ci) {
    if (($ci['status'] ?? '') === 'new') {
        $newInquiriesCount++;
    }
}

foreach ($orders as $o) {
    $st = $o['orderStatus'] ?? $o['order_status'] ?? '';
    if ($st !== 'Cancelled') {
        $totalRevenue += (float)($o['totalAmount'] ?? $o['total_amount'] ?? $o['subtotal'] ?? 0);
    }
    if ($st === 'Pending' || $st === 'Confirmed') {
        $pendingOrdersCount++;
    }
}

foreach ($products as $p) {
    if (($p['stock'] ?? 0) <= 3) {
        $lowStockCount++;
    }
}

foreach ($crm_leads as $l) {
    if (($l['lead_status'] ?? '') === 'New') {
        $newLeadsCount++;
    }
}

$currentUser = $_SESSION['mc_admin_username'] ?? 'admin';
$currentRole = $_SESSION['mc_admin_role'] ?? 'super_admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Studio Administration Portal | Murli Creations</title>
  <meta name="description" content="Centralized Studio Administration Portal for Murli Creations.">

  <!-- Minimal Fonts & CSS -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link rel="stylesheet" href="/assets/css/globals.css?v=<?php echo filemtime(__DIR__ . '/assets/css/globals.css'); ?>">
  <link rel="stylesheet" href="/assets/css/admin.css?v=<?php echo filemtime(__DIR__ . '/assets/css/admin.css'); ?>">
  <link rel="stylesheet" href="/assets/css/toast.css?v=<?php echo filemtime(__DIR__ . '/assets/css/toast.css'); ?>">

  <script>
    // Bulletproof Tab Switching Engine - Runs immediately without external dependencies
    window.MURLI_CATEGORIES = <?php echo json_encode($categories); ?>;
    window.MURLI_PRODUCTS = <?php 
      $minProds = array_map(function($p) {
        return [
          "id" => $p["id"] ?? "",
          "code" => $p["code"] ?? "",
          "name" => $p["name"] ?? "",
          "category" => $p["category"] ?? "",
          "subcategory" => $p["subcategory"] ?? "",
          "price" => (float)($p["price"] ?? 0),
          "mrp" => (float)($p["mrp"] ?? round(($p["price"] ?? 0) * 1.3)),
          "stock" => (int)($p["stock"] ?? 0)
        ];
      }, $products);
      echo json_encode($minProds);
    ?>;

    window.switchAdminTab = function(targetTab) {
      try {
        var navBtns = document.querySelectorAll('.admin-nav-item');
        var tabPanes = document.querySelectorAll('.admin-tab-pane');
        var sidebar = document.getElementById('adminSidebar');
        var titleEl = document.getElementById('topbarPageTitle');

        var found = false;
        tabPanes.forEach(function(pane) {
          if (pane.id === 'tab-' + targetTab) {
            pane.classList.add('active');
            pane.style.display = 'block';
            found = true;
          } else {
            pane.classList.remove('active');
            pane.style.display = 'none';
          }
        });

        navBtns.forEach(function(btn) {
          if (btn.getAttribute('data-tab') === targetTab) {
            btn.classList.add('active');
            if (titleEl) {
              var labelSpan = btn.querySelector('span:nth-child(2)');
              if (labelSpan) {
                titleEl.textContent = labelSpan.textContent.trim();
              }
            }
          } else {
            btn.classList.remove('active');
          }
        });

        if (window.history && window.history.replaceState) {
          window.history.replaceState(null, null, '#' + targetTab);
        }

        if (sidebar && window.innerWidth <= 900) {
          sidebar.classList.remove('open');
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
        return found;
      } catch (err) {
        console.error('[Admin] Tab Switch Error:', err);
      }
    };

    // Auto-detect hash on load
    document.addEventListener('DOMContentLoaded', function() {
      var hash = (window.location.hash || '').replace('#', '');
      if (hash && document.getElementById('tab-' + hash)) {
        window.switchAdminTab(hash);
      }
    });
  </script>
</head>
<body class="admin-body">

<?php if (!$is_logged_in): ?>
  <!-- =========================================================================
       SIGNATURE ROYAL FOREST EMERALD LOGIN CARD (Frontend Aesthetic)
       ========================================================================= -->
  <div class="admin-login-wrapper">
    <div class="admin-login-card">
      <div class="admin-login-header">
        <img src="/images/brand/Favicon.png?v=3" alt="Murli Creations" class="admin-login-emblem">
        <h1 class="admin-login-brand-title">MURLI</h1>
        <span class="admin-login-brand-sub">Creations Studio</span>
      </div>

      <h2 class="admin-login-heading">Studio Portal Login</h2>
      <p class="admin-login-desc">Enter authorized credentials to manage handcrafted catalog, order tracking, SEO, and operations.</p>

      <form id="admin-login-form" style="text-align: left;">
        <div style="margin-bottom: 18px;">
          <label style="display: block; font-size: 0.76rem; text-transform: uppercase; letter-spacing: 1px; color: #c2e2c9; margin-bottom: 6px; font-weight: 700;" for="admin-username">Username</label>
          <input type="text" id="admin-username" class="admin-login-input" placeholder="admin" required autocomplete="username">
        </div>

        <div style="margin-bottom: 24px;">
          <label style="display: block; font-size: 0.76rem; text-transform: uppercase; letter-spacing: 1px; color: #c2e2c9; margin-bottom: 6px; font-weight: 700;" for="admin-password">Password</label>
          <input type="password" id="admin-password" class="admin-login-input" placeholder="••••••••" required autocomplete="current-password">
        </div>

        <button type="submit" class="admin-login-btn">
          Enter Studio Administration &rarr;
        </button>
      </form>

      <div style="margin-top: 24px; font-size: 0.76rem; color: #88b894;">
        Studio Master: <code style="background:rgba(255,255,255,0.1); padding:2px 6px; border-radius:4px; color:#fff;">admin</code> / <code style="background:rgba(255,255,255,0.1); padding:2px 6px; border-radius:4px; color:#fff;">murli@2026</code><br>
        Database: <?php echo $db_connected ? '<span style="color:#4ade80; font-weight:700;"><span class="live-status-dot pulse"></span> <span class="material-symbols-outlined" style="font-size:16px; vertical-align:middle;">database</span> MySQL Connected</span>' : '<span style="color:#fbbf24; font-weight:700;">● Storage Mode Active</span>'; ?>
      </div>
    </div>
  </div>

<?php else: ?>
  <!-- =========================================================================
       AUTHENTICATED STUDIO PORTAL VIEW
       ========================================================================= -->

  <!-- Left Sidebar Navigation -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-header">
      <img src="/images/brand/Favicon.png?v=3" alt="Murli Creations" class="admin-brand-emblem">
      <div class="admin-brand-info">
        <h2>MURLI</h2>
        <span>Studio Administration</span>
      </div>
    </div>

    <nav class="admin-nav">
      <div class="nav-section-title">Navigation Hub</div>
      
      <!-- 1. Product Management -->
      <button type="button" class="admin-nav-item active" data-tab="products" onclick="window.switchAdminTab('products'); return false;">
        <span class="nav-icon"><span class="material-symbols-outlined">inventory_2</span></span>
        <span class="nav-label">Product Management</span>
        <span class="nav-badge"><?php echo count($products); ?></span>
      </button>

      <!-- 2. Order Tracking/Approval/status -->
      <button type="button" class="admin-nav-item" data-tab="orders" onclick="window.switchAdminTab('orders'); return false;">
        <span class="nav-icon"><span class="material-symbols-outlined">local_shipping</span></span>
        <span class="nav-label">Order Tracking/Approval/status</span>
        <span class="nav-badge"><?php echo count($orders); ?></span>
      </button>

      <!-- 3. Contact Form Inquiries (NEW) -->
      <button type="button" class="admin-nav-item" data-tab="contact-inquiries" onclick="window.switchAdminTab('contact-inquiries'); return false;">
        <span class="nav-icon"><span class="material-symbols-outlined">mark_email_unread</span></span>
        <span class="nav-label">Contact Form Inquiries</span>
        <?php if ($newInquiriesCount > 0): ?>
          <span class="nav-badge" style="background:#fee2e2; color:#991b1b;"><?php echo $newInquiriesCount; ?> New</span>
        <?php else: ?>
          <span class="nav-badge"><?php echo count($contact_inquiries); ?></span>
        <?php endif; ?>
      </button>

      <!-- 4. Navbar Pages & Studio Details CMS (NEW) -->
      <button type="button" class="admin-nav-item" data-tab="pages-content" onclick="window.switchAdminTab('pages-content'); return false;">
        <span class="nav-icon"><span class="material-symbols-outlined">edit_document</span></span>
        <span class="nav-label">Page Details & Studio CMS</span>
      </button>

      <!-- 5. Customer reminders/ -->
      <button type="button" class="admin-nav-item" data-tab="reminders" onclick="window.switchAdminTab('reminders'); return false;">
        <span class="nav-icon"><span class="material-symbols-outlined">notifications_active</span></span>
        <span class="nav-label">Customer reminders/</span>
        <?php if ($newLeadsCount > 0): ?>
          <span class="nav-badge" style="background:#fee2e2; color:#991b1b;"><?php echo $newLeadsCount; ?></span>
        <?php endif; ?>
      </button>

      <!-- 6. Meta Tags management per page wise -->
      <button type="button" class="admin-nav-item" data-tab="seo" onclick="window.switchAdminTab('seo'); return false;">
        <span class="nav-icon"><span class="material-symbols-outlined">travel_explore</span></span>
        <span class="nav-label">Meta Tags management per page wise</span>
      </button>

      <!-- 7. Email communication -->
      <button type="button" class="admin-nav-item" data-tab="email" onclick="window.switchAdminTab('email'); return false;">
        <span class="nav-icon"><span class="material-symbols-outlined">mail</span></span>
        <span class="nav-label">Email communication</span>
      </button>

      <!-- 8. offer management -->
      <button type="button" class="admin-nav-item" data-tab="offers" onclick="window.switchAdminTab('offers'); return false;">
        <span class="nav-icon"><span class="material-symbols-outlined">loyalty</span></span>
        <span class="nav-label">offer management</span>
        <span class="nav-badge"><?php echo count($coupons); ?></span>
      </button>

      <!-- 9. Customer Reviews & Testimonials Hub (NEW) -->
      <button type="button" class="admin-nav-item" data-tab="reviews" onclick="window.switchAdminTab('reviews'); return false;">
        <span class="nav-icon"><span class="material-symbols-outlined">reviews</span></span>
        <span class="nav-label">Customer Reviews</span>
        <?php if ($pendingReviewsCount > 0): ?>
          <span class="nav-badge" style="background:#fee2e2; color:#991b1b;"><?php echo $pendingReviewsCount; ?> New</span>
        <?php else: ?>
          <span class="nav-badge"><?php echo count($admin_reviews); ?></span>
        <?php endif; ?>
      </button>

      <!-- 10. Logins Management -->
      <button type="button" class="admin-nav-item" data-tab="logins" onclick="window.switchAdminTab('logins'); return false;">
        <span class="nav-icon"><span class="material-symbols-outlined">admin_panel_settings</span></span>
        <span class="nav-label">Logins Management</span>
      </button>
    </nav>

    <div class="admin-sidebar-footer">
      <div class="admin-user-pill">
        <div class="user-avatar"><?php echo strtoupper(substr($currentUser, 0, 1)); ?></div>
        <div class="user-info-text">
          <strong><?php echo htmlspecialchars($currentUser); ?></strong>
          <span><?php echo ucfirst($currentRole); ?></span>
        </div>
      </div>
      <button type="button" class="portal-logout-btn" title="Sign Out" style="background:transparent; border:none; color:#a89988; cursor:pointer; font-size:1.15rem;">
        <span class="material-symbols-outlined">logout</span>
      </button>
    </div>
  </aside>

  <!-- Main Administrative Viewport -->
  <main class="admin-main">
    <!-- Top Minimal Bar -->
    <header class="admin-topbar">
      <div class="topbar-left">
        <button type="button" class="mobile-sidebar-toggle" id="mobileSidebarToggle" aria-label="Toggle navigation"><span class="material-symbols-outlined">menu</span></button>
        <h1 class="topbar-title" id="topbarPageTitle">Product Management</h1>
      </div>

      <div class="topbar-right">
        <!-- Interactive Notification Bell with Dropdown & Web Audio Chime -->
        <div class="notification-bell-wrap" style="position:relative;">
          <button type="button" class="topbar-icon-btn" id="topbarNotificationBtn" title="Studio Alerts & Notifications" aria-label="Notifications">
            <span class="material-symbols-outlined">notifications</span>
            <?php 
              $totalAlerts = $newInquiriesCount + $lowStockCount + $pendingOrdersCount + $pendingReviewsCount;
              if ($totalAlerts > 0): 
            ?>
              <span class="notification-badge-pulse"><?php echo $totalAlerts; ?></span>
            <?php endif; ?>
          </button>
          
          <div class="notification-dropdown" id="notificationDropdown">
            <div class="notif-header">
              <h4 style="margin:0; font-size:0.88rem; font-weight:800; color:var(--admin-text-main);"><span class="material-symbols-outlined" style="font-size:18px; vertical-align:middle; margin-right:4px;">notifications</span> Studio Alerts</h4>
              <button type="button" class="notif-chime-test" id="btnTestChime" title="Test Alert Audio Chime"><span class="material-symbols-outlined" style="font-size:16px; vertical-align:middle;">volume_up</span> Chime</button>
            </div>
            <div class="notif-list">
              <?php if ($newInquiriesCount > 0): ?>
                <div class="notif-item" onclick="window.switchAdminTab('contact-inquiries');">
                  <div class="notif-icon"><span class="material-symbols-outlined">mark_email_unread</span></div>
                  <div class="notif-text">
                    <strong><?php echo $newInquiriesCount; ?> New Contact Inquiries</strong>
                    <small>Patrons waiting for response on bespoke artworks</small>
                  </div>
                </div>
              <?php endif; ?>
              <?php if ($lowStockCount > 0): ?>
                <div class="notif-item" onclick="window.switchAdminTab('products');">
                  <div class="notif-icon"><span class="material-symbols-outlined">warning</span></div>
                  <div class="notif-text">
                    <strong><?php echo $lowStockCount; ?> Artworks Low in Stock</strong>
                    <small>Immediate artisan craftsmanship replenishment needed</small>
                  </div>
                </div>
              <?php endif; ?>
              <?php if ($pendingOrdersCount > 0): ?>
                <div class="notif-item" onclick="window.switchAdminTab('orders');">
                  <div class="notif-icon"><span class="material-symbols-outlined">package_2</span></div>
                  <div class="notif-text">
                    <strong><?php echo $pendingOrdersCount; ?> Orders Pending Fulfillment</strong>
                    <small>Assign courier AWB & print parcel label</small>
                  </div>
                </div>
              <?php endif; ?>
              <?php if ($pendingReviewsCount > 0): ?>
                <div class="notif-item" onclick="window.switchAdminTab('reviews');">
                  <div class="notif-icon"><span class="material-symbols-outlined">reviews</span></div>
                  <div class="notif-text">
                    <strong><?php echo $pendingReviewsCount; ?> Customer Reviews Pending</strong>
                    <small>Verify and approve to display on storefront</small>
                  </div>
                </div>
              <?php endif; ?>
              <div class="notif-item" onclick="window.switchAdminTab('orders');">
                <div class="notif-icon"><span class="material-symbols-outlined">verified_user</span></div>
                <div class="notif-text">
                  <strong>Studio Security Active</strong>
                  <small>Database connected & live tracking operational</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php if ($db_connected): ?>
          <span class="db-status-pill db-status-connected" title="MySQL Connected (Database: <?php echo DB_NAME; ?>)">
            <span class="live-status-dot pulse"></span> <span class="material-symbols-outlined" style="font-size:16px; vertical-align:middle;">database</span> MySQL Connected
          </span>
        <?php else: ?>
          <span class="db-status-pill db-status-disconnected" title="Using JSON Storage. Update config/database.php for MySQL.">
            <span class="material-symbols-outlined" style="font-size:16px; vertical-align:middle;">storage</span> JSON Mode
          </span>
        <?php endif; ?>

        <a href="/index.php" target="_blank" class="topbar-btn">
          <span class="material-symbols-outlined" style="font-size:17px; vertical-align:middle;">storefront</span> <span>View Site</span> <span class="material-symbols-outlined" style="font-size:15px; vertical-align:middle;">open_in_new</span>
        </a>
        <a href="/track-order.php" target="_blank" class="topbar-btn">
          <span class="material-symbols-outlined" style="font-size:17px; vertical-align:middle;">local_shipping</span> <span>Tracking Page</span> <span class="material-symbols-outlined" style="font-size:15px; vertical-align:middle;">open_in_new</span>
        </a>
        <button type="button" class="topbar-btn primary portal-logout-btn">
          <span class="material-symbols-outlined" style="font-size:17px;">logout</span> <span>Logout</span>
        </button>
      </div>
    </header>

    <!-- Content Workspace -->
    <div class="admin-content">

      <!-- =====================================================================
           TAB 1: PRODUCT MANAGEMENT (Visual Frontend Cards + List Toggle)
           ===================================================================== -->
      <div class="admin-tab-pane active" id="tab-products">
        <!-- Top Metrics Row -->
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-info">
              <h3>Artworks In Catalog</h3>
              <div class="metric-val"><?php echo $totalProducts; ?></div>
            </div>
            <div class="metric-icon-wrap"><span class="material-symbols-outlined">palette</span></div>
          </div>
          <div class="metric-card">
            <div class="metric-info">
              <h3>Low Stock Alert</h3>
              <div class="metric-val" style="color:<?php echo $lowStockCount > 0 ? '#b91c1c' : '#1c1815'; ?>;"><?php echo $lowStockCount; ?></div>
            </div>
            <div class="metric-icon-wrap"><span class="material-symbols-outlined">warning</span></div>
          </div>
          <div class="metric-card">
            <div class="metric-info">
              <h3>Total Orders Booked</h3>
              <div class="metric-val"><?php echo $totalOrders; ?></div>
            </div>
            <div class="metric-icon-wrap"><span class="material-symbols-outlined">package_2</span></div>
          </div>
          <div class="metric-card">
            <div class="metric-info">
              <h3>Gross Revenue</h3>
              <div class="metric-val">₹<?php echo number_format($totalRevenue); ?></div>
            </div>
            <div class="metric-icon-wrap"><span class="material-symbols-outlined">payments</span></div>
          </div>
        </div>

        <div class="admin-box">
          <div class="admin-box-header">
            <div>
              <h2 class="admin-box-title">Studio Handcrafted Product Catalog</h2>
              <p class="admin-box-subtitle">Visual live preview matching the customer storefront with instant stock adjusters</p>
            </div>
            
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
              <!-- View Switcher -->
              <div class="view-switcher-pill">
                <button type="button" class="view-btn active" id="btnViewGrid">
                  <span>⊞</span> Visual Cards
                </button>
                <button type="button" class="view-btn" id="btnViewTable">
                  <span class="material-symbols-outlined" style="font-size:17px;">table_rows</span> Table View
                </button>
              </div>

              <!-- Power Tools: Export, Bulk Import, Festive Discount -->
              <a href="/api/admin.php?action=export_products_csv" class="btn btn-secondary" title="Export all artworks to CSV spreadsheet">
                <span class="material-symbols-outlined" style="font-size:17px;">download</span> Export CSV
              </a>
              <button type="button" class="btn btn-secondary" id="btnOpenBulkImportModal" title="Upload or paste updated CSV to batch update prices and stock">
                <span class="material-symbols-outlined" style="font-size:17px;">upload</span> Bulk Import
              </button>
              <button type="button" class="btn btn-secondary" id="btnOpenFestiveDiscountModal" title="Category & Subcategory Wise Bulk Price & Discount Manager">
                <span class="material-symbols-outlined" style="font-size:18px; color:#1EA659;">percent</span> Category Discount
              </button>

              <button type="button" class="btn btn-gold" id="openAddProductModalBtn">
                + Add New Artwork
              </button>
            </div>
          </div>

          <!-- Filter Toolbar with Category & Subcategory Drilldown -->
          <div class="admin-toolbar" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-bottom:24px;">
            <div class="admin-search-wrap" style="flex:1; min-width:250px; position:relative;">
              <span class="admin-search-icon"><span class="material-symbols-outlined">search</span></span>
              <input type="text" id="productSearchInput" class="admin-search-input" placeholder="Search artwork by title, SKU code (e.g. MC/L-001), material...">
            </div>

            <!-- Category Filter -->
            <select id="productCategoryFilter" class="admin-select" style="min-width:180px;">
              <option value="all">All Categories (<?php echo count($products); ?>)</option>
              <?php foreach ($categories as $catKey => $catData): ?>
                <?php 
                  $catPCount = count(array_filter($products, function($item) use ($catKey, $catData) {
                    $c = $item['category'] ?? '';
                    return $c === $catKey || $c === ($catData['name'] ?? '');
                  }));
                ?>
                <option value="<?php echo htmlspecialchars($catKey); ?>">
                  <?php echo htmlspecialchars($catData['name'] ?? $catKey); ?> (<?php echo $catPCount; ?>)
                </option>
              <?php endforeach; ?>
            </select>

            <!-- Subcategory Filter (Populated dynamically) -->
            <select id="productSubcategoryFilter" class="admin-select" style="min-width:180px;">
              <option value="all">All Subcategories</option>
            </select>

            <!-- Stock Filter -->
            <select id="productStockFilter" class="admin-select">
              <option value="all">All Inventory</option>
              <option value="in">In Stock (>3)</option>
              <option value="low">Low Stock (≤3)</option>
              <option value="out">Sold Out (0)</option>
            </select>

            <!-- Sort Filter -->
            <select id="productSortFilter" class="admin-select">
              <option value="default">Sort: Default</option>
              <option value="price-asc">Price: Low to High</option>
              <option value="price-desc">Price: High to Low</option>
              <option value="stock-desc">Stock: High to Low</option>
              <option value="name-asc">Name: A to Z</option>
            </select>
          </div>

          <!-- 1. Visual Cards Grid (Luxury Studio Aesthetic) -->
          <div class="frontend-products-grid" id="productsGridView">
            <?php foreach ($products as $p): ?>
              <?php 
                $pImg = !empty($p['images'][0]) ? $p['images'][0] : '/img-prodcut/IMG_4041.png';
                $pStock = (int)($p['stock'] ?? 0);
                $pPrice = (float)($p['price'] ?? 0);
                $pMrp = (float)($p['mrp'] ?? round($pPrice * 1.3));
                $pDiscount = $pMrp > 0 ? round((($pMrp - $pPrice) / $pMrp) * 100) : 0;
                $pJson = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
              ?>
              <div class="admin-product-card product-item-container" 
                   id="prod-card-<?php echo htmlspecialchars($p['id']); ?>"
                   data-name="<?php echo htmlspecialchars($p['name']); ?>"
                   data-code="<?php echo htmlspecialchars($p['code'] ?? ''); ?>"
                   data-category="<?php echo htmlspecialchars($p['category']); ?>"
                   data-subcategory="<?php echo htmlspecialchars($p['subcategory'] ?? ''); ?>"
                   data-price="<?php echo $pPrice; ?>"
                   data-stock="<?php echo $pStock; ?>">
                
                <!-- Image Container with Badges -->
                <div class="admin-prod-img-box">
                  <img src="<?php echo htmlspecialchars($pImg); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="admin-prod-img" loading="lazy">
                  
                  <div class="admin-prod-top-badges">
                    <?php if ($pStock <= 0): ?>
                      <span class="card-badge sold-out">Sold Out</span>
                    <?php elseif (!empty($p['bestseller'])): ?>
                      <span class="card-badge bestseller">Bestseller</span>
                    <?php elseif (!empty($p['featured'])): ?>
                      <span class="card-badge featured">Featured</span>
                    <?php endif; ?>
                  </div>

                  <div class="admin-prod-rating-pill">
                    <span class="star"><span class="material-symbols-outlined" style="font-size:16px; color:#F59E0B; vertical-align:middle;">star</span></span>
                    <span><?php echo number_format($p['rating'] ?? 4.9, 1); ?></span>
                    <span style="opacity:0.35;">|</span>
                    <span><?php echo $p['reviewsCount'] ?? 15; ?></span>
                  </div>
                </div>

                <!-- Product Info Box -->
                <div class="admin-prod-info">
                  <div class="admin-prod-cat-row">
                    <span class="admin-prod-code"><?php echo htmlspecialchars($p['code'] ?? 'MC/L-000'); ?></span>
                    <span class="admin-prod-cat"><?php echo htmlspecialchars($p['category']); ?></span>
                  </div>

                  <h3 class="admin-prod-title" title="<?php echo htmlspecialchars($p['name']); ?>"><?php echo htmlspecialchars($p['name']); ?></h3>

                  <div class="admin-prod-pricing-row">
                    <span class="admin-prod-price">₹<?php echo number_format($pPrice); ?></span>
                    <span class="admin-prod-mrp">₹<?php echo number_format($pMrp); ?></span>
                    <span class="admin-prod-discount">(<?php echo $pDiscount; ?>% OFF)</span>
                  </div>

                  <!-- In Stock / Out of Stock Status & Stepper Strip -->
                  <div class="admin-prod-stock-strip" style="display:flex; justify-content:space-between; align-items:center; background:#faf8f5; border:1px solid #e8e1d7; border-radius:8px; padding:6px 10px; margin:8px 0;">
                    <!-- 1-Click Status Toggle -->
                    <button type="button" 
                            class="btn-toggle-stock-status" 
                            id="stock-toggle-card-<?php echo htmlspecialchars($p['id']); ?>"
                            data-id="<?php echo htmlspecialchars($p['id']); ?>" 
                            data-stock="<?php echo $pStock; ?>"
                            title="Click to toggle between In Stock & Out of Stock"
                            style="border:none; background:transparent; cursor:pointer; padding:0;">
                      <?php if ($pStock > 0): ?>
                        <span class="stock-pill in-stock" style="color:#15803d; background:#dcfce7; font-weight:700; font-size:0.75rem; padding:3px 8px; border-radius:12px; border:1px solid #bbf7d0; display:inline-flex; align-items:center; gap:4px;">
                          ● In Stock
                        </span>
                      <?php else: ?>
                        <span class="stock-pill out-of-stock" style="color:#b91c1c; background:#fee2e2; font-weight:700; font-size:0.75rem; padding:3px 8px; border-radius:12px; border:1px solid #fecaca; display:inline-flex; align-items:center; gap:4px;">
                          ● Out of Stock
                        </span>
                      <?php endif; ?>
                    </button>

                    <!-- Quick Stock Stepper -->
                    <div class="stock-stepper" style="display:inline-flex; align-items:center; gap:4px;">
                      <button type="button" class="stock-btn btn-quick-stock" data-id="<?php echo htmlspecialchars($p['id']); ?>" data-change="-1" title="Decrease stock">-</button>
                      <strong id="stock-val-<?php echo htmlspecialchars($p['id']); ?>" style="color:<?php echo $pStock <= 0 ? '#b91c1c' : ($pStock <= 3 ? '#b45309' : '#1c1815'); ?>; min-width:24px; text-align:center; font-size:0.85rem;">
                        <?php echo $pStock; ?>
                      </strong>
                      <button type="button" class="stock-btn btn-quick-stock" data-id="<?php echo htmlspecialchars($p['id']); ?>" data-change="1" title="Increase stock">+</button>
                    </div>
                  </div>

                  <!-- Direct Card Action Buttons -->
                  <div class="admin-prod-action-row">
                    <a href="/product.php?slug=<?php echo urlencode($p['slug'] ?? ''); ?>" target="_blank" class="btn btn-secondary" title="View live on customer storefront" style="flex:0 0 34px; padding:0; display:inline-flex; align-items:center; justify-content:center;"><span class="material-symbols-outlined" style="font-size:17px;">visibility</span></a>
                    <button type="button" class="btn btn-secondary btn-edit-product" data-product="<?php echo $pJson; ?>"><span class="material-symbols-outlined" style="font-size:16px;">edit</span> Edit</button>
                    <button type="button" class="btn btn-secondary btn-inspect-product" data-id="<?php echo htmlspecialchars($p['id']); ?>">Specs</button>
                    <button type="button" class="btn btn-danger btn-delete-product" data-id="<?php echo htmlspecialchars($p['id']); ?>" data-name="<?php echo htmlspecialchars($p['name']); ?>" style="flex:0 0 34px; padding:0; display:inline-flex; align-items:center; justify-content:center;"><span class="material-symbols-outlined" style="font-size:17px;">delete</span></button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- 2. Dense Table View (Toggled via button) -->
          <div class="admin-table-responsive" id="productsTableView" style="display:none; margin-top:20px;">
            <table class="admin-table">
              <thead>
                <tr>
                  <th style="width:60px;">Image</th>
                  <th>SKU Code</th>
                  <th>Artwork Name</th>
                  <th>Category</th>
                  <th>Price / MRP</th>
                  <th>Stock Units</th>
                  <th>Tags</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($products as $p): ?>
                  <?php 
                    $pImg = !empty($p['images'][0]) ? $p['images'][0] : '/img-prodcut/IMG_4041.png';
                    $pStock = (int)($p['stock'] ?? 0);
                    $pPrice = (float)($p['price'] ?? 0);
                    $pMrp = (float)($p['mrp'] ?? round($pPrice * 1.3));
                    $pDiscount = $pMrp > 0 ? round((($pMrp - $pPrice) / $pMrp) * 100) : 0;
                    $pJson = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                  ?>
                  <tr class="product-table-row" id="prod-row-<?php echo htmlspecialchars($p['id']); ?>"
                      data-name="<?php echo htmlspecialchars($p['name']); ?>"
                      data-code="<?php echo htmlspecialchars($p['code'] ?? ''); ?>"
                      data-category="<?php echo htmlspecialchars($p['category']); ?>"
                      data-subcategory="<?php echo htmlspecialchars($p['subcategory'] ?? ''); ?>"
                      data-price="<?php echo $pPrice; ?>"
                      data-stock="<?php echo $pStock; ?>">
                    <td>
                      <img src="<?php echo htmlspecialchars($pImg); ?>" alt="" style="width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0;">
                    </td>
                    <td><code style="font-size:0.75rem; background:#f8fafc; border:1px solid #e2e8f0; padding:2px 6px; border-radius:4px; font-weight:700; color:#0f382c;"><?php echo htmlspecialchars($p['code'] ?? 'MC/L-000'); ?></code></td>
                    <td>
                      <strong style="color:#0f382c; font-size:0.9rem;"><?php echo htmlspecialchars($p['name']); ?></strong><br>
                      <small style="color:#64748b;"><?php echo htmlspecialchars($p['size'] ?? ($p['subcategory'] ?? 'Handmade')); ?></small>
                    </td>
                    <td>
                      <span class="badge" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; font-size:0.72rem; padding:3px 8px; border-radius:6px; font-weight:600;">
                        <?php echo htmlspecialchars($p['category']); ?>
                      </span>
                      <?php if (!empty($p['subcategory'])): ?>
                        <div style="font-size:0.72rem; color:#64748b; margin-top:3px;"><?php echo htmlspecialchars($p['subcategory']); ?></div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="table-price-wrap" style="line-height:1.25;">
                        <strong style="font-size:0.92rem; color:#0f382c;">₹<?php echo number_format($pPrice); ?></strong>
                        <?php if ($pMrp > $pPrice): ?>
                          <span style="color:#94a3b8; font-size:0.78rem; text-decoration:line-through; margin-left:4px;">₹<?php echo number_format($pMrp); ?></span>
                          <span style="color:#16a34a; font-size:0.72rem; font-weight:700; margin-left:4px;">(<?php echo $pDiscount; ?>% OFF)</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <div style="display:inline-flex; align-items:center; gap:8px;">
                        <button type="button" 
                                class="btn-toggle-stock-status" 
                                id="stock-toggle-table-<?php echo htmlspecialchars($p['id']); ?>"
                                data-id="<?php echo htmlspecialchars($p['id']); ?>" 
                                data-stock="<?php echo $pStock; ?>"
                                title="Click to toggle In/Out of Stock"
                                style="border:none; background:transparent; cursor:pointer; padding:0;">
                          <?php if ($pStock > 0): ?>
                            <span class="stock-pill in-stock" style="color:#15803d; background:#dcfce7; font-weight:700; font-size:0.75rem; padding:2px 8px; border-radius:12px; border:1px solid #bbf7d0; display:inline-flex; align-items:center;">
                              ● In Stock
                            </span>
                          <?php else: ?>
                            <span class="stock-pill out-of-stock" style="color:#b91c1c; background:#fee2e2; font-weight:700; font-size:0.75rem; padding:2px 8px; border-radius:12px; border:1px solid #fecaca; display:inline-flex; align-items:center;">
                              ● Out of Stock
                            </span>
                          <?php endif; ?>
                        </button>
                        <span id="stock-val-table-<?php echo htmlspecialchars($p['id']); ?>" style="color:<?php echo $pStock <= 0 ? '#b91c1c' : ($pStock <= 3 ? '#b45309' : '#1c1815'); ?>; font-weight:600; font-size:0.82rem;">(<?php echo $pStock; ?> units)</span>
                      </div>
                    </td>
                    <td>
                      <span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size:0.72rem; padding:3px 8px; border-radius:12px; font-weight:700; display:inline-flex; align-items:center; gap:3px;">
                        <span class="material-symbols-outlined" style="font-size:12px;">check_circle</span> Published
                      </span>
                    </td>
                    <td style="text-align:right;">
                      <div style="display:inline-flex; gap:6px;">
                        <a href="/product.php?slug=<?php echo urlencode($p['slug'] ?? ''); ?>" target="_blank" class="btn btn-sm btn-secondary" title="View live on storefront" style="padding:6px 9px; font-size:0.75rem;"><span class="material-symbols-outlined" style="font-size:15px;">visibility</span></a>
                        <button type="button" class="btn btn-sm btn-secondary btn-inspect-product" data-id="<?php echo htmlspecialchars($p['id']); ?>" style="padding:6px 9px; font-size:0.75rem;">Specs</button>
                        <button type="button" class="btn btn-sm btn-secondary btn-edit-product" data-product="<?php echo $pJson; ?>" style="padding:6px 9px; font-size:0.75rem;"><span class="material-symbols-outlined" style="font-size:15px;">edit</span> Edit</button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete-product" data-id="<?php echo htmlspecialchars($p['id']); ?>" data-name="<?php echo htmlspecialchars($p['name']); ?>" style="padding:6px 9px; font-size:0.75rem;"><span class="material-symbols-outlined" style="font-size:15px;">delete</span></button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- =====================================================================
           TAB 2: ORDER TRACKING / APPROVAL / STATUS & REVENUE ANALYTICS
           ===================================================================== -->
      <div class="admin-tab-pane" id="tab-orders">
        
        <!-- VISUAL BUSINESS ANALYTICS & REVENUE GRAPHS -->
        <div class="admin-box" style="margin-bottom:24px;">
          <div class="admin-box-header">
            <div>
              <h2 class="admin-box-title"><span class="material-symbols-outlined" style="font-size:22px; vertical-align:middle; margin-right:8px; color:#1EA659;">analytics</span> Studio Business & Revenue Intelligence</h2>
              <p class="admin-box-subtitle">Live sales trajectories, top handcrafted collections, order statuses, and patron locations</p>
            </div>
            <span class="badge badge-success">● Live Sync</span>
          </div>

          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
            <!-- 1. Monthly Revenue Growth -->
            <div style="background:#f7fdf9; border:1px solid var(--admin-border); border-radius:12px; padding:18px;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <strong style="font-size:0.86rem; color:var(--admin-text-main); text-transform:uppercase; letter-spacing:0.5px;">Monthly Revenue Trajectory</strong>
                <span style="font-size:0.75rem; color:#15803d; font-weight:700;">+38% Festive MoM</span>
              </div>
              <div style="display:flex; align-items:flex-end; gap:12px; height:120px; padding-top:10px;">
                <?php 
                  $mRevs = $analytics_data['monthly_revenue'] ?? ['Jun'=>14200,'Jul'=>18900,'Aug'=>26400,'Sep'=>38700,'Oct'=>52100,'Nov'=>64300];
                  $maxRev = max(array_values($mRevs)) ?: 1;
                  foreach ($mRevs as $mName => $mVal):
                    $pct = round(($mVal / $maxRev) * 100);
                ?>
                  <div style="flex:1; display:flex; flex-direction:column; align-items:center; height:100%; justify-content:flex-end;">
                    <span style="font-size:0.68rem; font-weight:700; color:#15803d; margin-bottom:4px;">₹<?php echo round($mVal / 1000); ?>k</span>
                    <div style="width:100%; max-width:32px; background:var(--gradient-primary); border-radius:4px 4px 0 0; height:<?php echo $pct; ?>%; transition:height 0.4s ease;" title="<?php echo $mName; ?>: ₹<?php echo number_format($mVal); ?>"></div>
                    <span style="font-size:0.72rem; color:var(--admin-text-muted); font-weight:600; margin-top:6px;"><?php echo $mName; ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- 2. Top-Selling Artworks -->
            <div style="background:#f7fdf9; border:1px solid var(--admin-border); border-radius:12px; padding:18px;">
              <strong style="font-size:0.86rem; color:var(--admin-text-main); text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:14px;">Top Performing Artworks</strong>
              <div style="display:flex; flex-direction:column; gap:10px;">
                <?php 
                  $topProds = $analytics_data['top_products'] ?? [];
                  foreach ($topProds as $tp):
                ?>
                  <div style="display:flex; align-items:center; gap:10px;">
                    <img src="<?php echo htmlspecialchars($tp['image']); ?>" alt="" style="width:34px; height:34px; border-radius:6px; object-fit:cover; border:1px solid rgba(30,166,89,0.2);">
                    <div style="flex:1; min-width:0;">
                      <div style="display:flex; justify-content:space-between; font-size:0.8rem; font-weight:700; color:var(--admin-text-main);">
                        <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;"><?php echo htmlspecialchars($tp['name']); ?></span>
                        <span style="color:#15803d;"><?php echo $tp['units']; ?> sold</span>
                      </div>
                      <div style="width:100%; height:5px; background:#e8f5e9; border-radius:3px; margin-top:4px; overflow:hidden;">
                        <div style="width:<?php echo min(100, $tp['units'] * 5.5); ?>%; height:100%; background:var(--gradient-primary); border-radius:3px;"></div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- 3. Top Patron Cities -->
            <div style="background:#f7fdf9; border:1px solid var(--admin-border); border-radius:12px; padding:18px;">
              <strong style="font-size:0.86rem; color:var(--admin-text-main); text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:14px;">Top Patron Locations</strong>
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                <?php 
                  $topCities = $analytics_data['top_cities'] ?? ['Delhi-NCR'=>14,'Mumbai'=>11,'Bengaluru'=>9,'Jaipur'=>7,'Pune'=>5,'Hyderabad'=>4];
                  foreach ($topCities as $cName => $cCount):
                ?>
                  <div style="background:#ffffff; border:1px solid rgba(30,166,89,0.18); border-radius:6px; padding:8px 10px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:0.78rem; font-weight:700; color:var(--admin-text-main);"><span class="material-symbols-outlined" style="font-size:15px; vertical-align:middle; color:#1EA659;">location_on</span> <?php echo htmlspecialchars($cName); ?></span>
                    <span class="badge badge-success" style="font-size:0.7rem; padding:1px 6px;"><?php echo $cCount; ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="admin-box">
          <div class="admin-box-header">
            <div>
              <h2 class="admin-box-title">Order Tracking, Approval & Status Management</h2>
              <p class="admin-box-subtitle">Approve bookings, change status, print 4x6" shipping parcel labels, send WhatsApp tracking, and assign logistics AWBs</p>
            </div>
            <a href="/track-order.php" target="_blank" class="btn btn-secondary">
              <span>Live Customer Tracking Page</span> <span class="material-symbols-outlined" style="font-size:16px; vertical-align:middle;">open_in_new</span>
            </a>
          </div>

          <!-- Toolbar -->
          <div class="admin-toolbar">
            <div class="admin-search-wrap">
              <span class="admin-search-icon"><span class="material-symbols-outlined">search</span></span>
              <input type="text" id="orderSearchInput" class="admin-search-input" placeholder="Search by Order ID (#MURLI-...), patron name, mobile number, or AWB...">
            </div>
            <select id="orderStatusFilter" class="admin-select">
              <option value="all">All Statuses</option>
              <option value="Pending">Pending Approval</option>
              <option value="Confirmed">Confirmed / Approved</option>
              <option value="Processing">Processing (Handcrafting)</option>
              <option value="Shipped">Shipped / Dispatched</option>
              <option value="Out for Delivery">Out for Delivery</option>
              <option value="Delivered">Delivered</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>

          <!-- Orders Table -->
          <div class="admin-table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th style="width:36px; text-align:center;">
                    <input type="checkbox" id="selectAllOrdersCheckbox" title="Select all orders for bulk update">
                  </th>
                  <th>Order ID</th>
                  <th>Patron Details</th>
                  <th>Artworks</th>
                  <th>Total Amount</th>
                  <th>Payment Mode</th>
                  <th>Order Status</th>
                  <th>Carrier / AWB</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $o): ?>
                  <?php 
                    $oId = $o['id'];
                    $cust = $o['customer'] ?? [];
                    if (is_string($cust)) $cust = json_decode($cust, true) ?: [];
                    $oName = $cust['name'] ?? $o['customer_name'] ?? 'Patron';
                    $oPhone = $cust['phone'] ?? $o['customer_phone'] ?? '';
                    $oCity = $cust['city'] ?? $o['customer_city'] ?? 'India';
                    $oAmt = (float)($o['totalAmount'] ?? $o['total_amount'] ?? 0);
                    $oPayMethod = $o['paymentMethod'] ?? $o['payment_method'] ?? 'Partial COD';
                    $oPayStatus = $o['paymentStatus'] ?? $o['payment_status'] ?? 'Pending';
                    $oStatus = $o['orderStatus'] ?? $o['order_status'] ?? 'Pending';
                    $oCarrier = $o['tracking_carrier'] ?? $o['trackingCarrier'] ?? '';
                    $oAwb = $o['tracking_number'] ?? $o['trackingNumber'] ?? '';
                    $items = $o['items'] ?? [];
                    if (is_string($items)) $items = json_decode($items, true) ?: [];
                    $itemsCount = count($items);
                    $orderJson = htmlspecialchars(json_encode($o), ENT_QUOTES, 'UTF-8');
                  ?>
                  <tr class="order-table-row" id="order-row-<?php echo htmlspecialchars($oId); ?>"
                      data-id="<?php echo htmlspecialchars($oId); ?>"
                      data-customer="<?php echo htmlspecialchars($oName); ?>"
                      data-status="<?php echo htmlspecialchars($oStatus); ?>">
                    <td style="text-align:center;">
                      <input type="checkbox" class="order-select-checkbox" data-id="<?php echo htmlspecialchars($oId); ?>">
                    </td>
                    <td>
                      <strong>#<?php echo htmlspecialchars($oId); ?></strong><br>
                      <small style="color:#777;"><?php echo htmlspecialchars($o['createdAt'] ?? $o['date'] ?? 'Recent'); ?></small>
                    </td>
                    <td>
                      <strong><?php echo htmlspecialchars($oName); ?></strong><br>
                      <small style="color:#555;"><span class="material-symbols-outlined" style="font-size:14px; vertical-align:middle;">phone</span> <?php echo htmlspecialchars($oPhone); ?> (<?php echo htmlspecialchars($oCity); ?>)</small>
                    </td>
                    <td>
                      <span class="badge badge-gold"><?php echo $itemsCount; ?> Item<?php echo $itemsCount > 1 ? 's' : ''; ?></span>
                    </td>
                    <td>
                      <strong style="font-size:1.02rem;">₹<?php echo number_format($oAmt); ?></strong><br>
                      <small style="color:<?php echo $oPayStatus === 'Paid' ? '#16a34a' : '#b45309'; ?>;">● <?php echo htmlspecialchars($oPayStatus); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($oPayMethod); ?></td>
                    <td>
                      <span class="badge <?php 
                        if ($oStatus === 'Delivered') echo 'badge-success';
                        elseif ($oStatus === 'Shipped') echo 'badge-info';
                        elseif ($oStatus === 'Cancelled') echo 'badge-danger';
                        else echo 'badge-warning';
                      ?>">
                        ● <?php echo htmlspecialchars($oStatus); ?>
                      </span>
                    </td>
                    <td>
                      <?php if (!empty($oAwb)): ?>
                        <small><strong><?php echo htmlspecialchars($oCarrier ?: 'Courier'); ?></strong><br><code><?php echo htmlspecialchars($oAwb); ?></code></small>
                      <?php else: ?>
                        <small style="color:#999;">Not Assigned</small>
                      <?php endif; ?>
                    </td>
                    <td style="text-align:right;">
                      <div style="display:inline-flex; gap:6px; flex-wrap:wrap; justify-content:flex-end;">
                        <button type="button" class="btn btn-sm btn-success btn-whatsapp-order" 
                                data-id="<?php echo htmlspecialchars($oId); ?>" 
                                data-phone="<?php echo htmlspecialchars($oPhone); ?>" 
                                data-name="<?php echo htmlspecialchars($oName); ?>" 
                                data-status="<?php echo htmlspecialchars($oStatus); ?>" 
                                data-carrier="<?php echo htmlspecialchars($oCarrier); ?>" 
                                data-awb="<?php echo htmlspecialchars($oAwb); ?>" 
                                title="1-Click Dispatch Tracking Notification to Patron WhatsApp">
                          <span class="material-symbols-outlined" style="font-size:16px;">chat</span> WhatsApp
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary btn-print-shipping-label" 
                                data-id="<?php echo htmlspecialchars($oId); ?>" 
                                data-order="<?php echo $orderJson; ?>" 
                                title="Print 4x6 Thermal Shipping Parcel Label">
                          <span class="material-symbols-outlined" style="font-size:16px;">print</span> Label
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary btn-view-order" data-id="<?php echo htmlspecialchars($oId); ?>" title="View & Print Invoice">Invoice</button>
                        <button type="button" class="btn btn-sm btn-primary btn-change-order-status" data-id="<?php echo htmlspecialchars($oId); ?>" data-status="<?php echo htmlspecialchars($oStatus); ?>" title="Change Status / Approve">Status</button>
                        <button type="button" class="btn btn-sm btn-secondary btn-edit-tracking" data-id="<?php echo htmlspecialchars($oId); ?>" data-carrier="<?php echo htmlspecialchars($oCarrier); ?>" data-awb="<?php echo htmlspecialchars($oAwb); ?>" title="Update Courier AWB">Track</button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- FLOATING BULK ORDER ACTION BAR -->
          <div class="bulk-order-bar" id="bulkOrderActionBar" style="display:none;">
            <div class="bulk-bar-inner">
              <span style="font-weight:700;">Selected: <span id="bulkSelectedCount">0</span> orders</span>
              <select id="bulkNewStatusSelect" class="admin-select" style="padding:6px 12px; font-size:0.82rem;">
                <option value="">-- Choose New Status --</option>
                <option value="Confirmed">Confirmed / Approved</option>
                <option value="Processing">Processing (Handcrafting)</option>
                <option value="Shipped">Shipped / Dispatched</option>
                <option value="Delivered">Delivered</option>
                <option value="Cancelled">Cancelled</option>
              </select>
              <button type="button" class="btn btn-sm btn-primary" id="btnApplyBulkStatus">Update Selected</button>
              <button type="button" class="btn btn-sm btn-secondary" id="btnCancelBulkOrders">Cancel</button>
            </div>
          </div>

          <!-- Webhook API Endpoint Card -->
          <div style="margin-top:28px; background:#fbf9f6; border:1px solid var(--admin-border); border-radius:10px; padding:18px 20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:8px;">
              <strong style="color:#1c1815; font-size:0.95rem;"><span class="material-symbols-outlined" style="font-size:18px; vertical-align:middle; color:#1EA659;">bolt</span> Automated Webhook Status Update API (Shiprocket / Courier):</strong>
              <button type="button" class="btn btn-sm btn-primary" id="btnCreateApiKey">+ Generate New API Key</button>
            </div>
            <p style="font-size:0.82rem; color:#666; margin:0 0 10px 0;">External logistics systems can automatically update order statuses by sending a POST request to <code>/api/webhook.php</code>.</p>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
              <?php foreach ($api_keys as $k): ?>
                <span style="background:#fff; border:1px solid #ded6ca; padding:6px 12px; border-radius:6px; font-size:0.8rem; display:inline-flex; align-items:center; gap:8px;" id="apikey-row-<?php echo $k['id']; ?>">
                  <span><?php echo htmlspecialchars($k['key_name']); ?>: <code><?php echo htmlspecialchars($k['api_key']); ?></code></span>
                  <button type="button" class="btn-delete-api-key" data-id="<?php echo $k['id']; ?>" style="border:none; background:transparent; color:#dc2626; cursor:pointer; font-size:0.9rem;">&times;</button>
                </span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- =====================================================================
           TAB: CONTACT FORM INQUIRIES & REPLIES
           ===================================================================== -->
      <div class="admin-tab-pane" id="tab-contact-inquiries">
        <!-- Top Metrics Row -->
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-info">
              <h3>Total Inquiries</h3>
              <div class="metric-val"><?php echo count($contact_inquiries); ?></div>
            </div>
            <div class="metric-icon-wrap"><span class="material-symbols-outlined">inbox</span></div>
          </div>
          <div class="metric-card">
            <div class="metric-info">
              <h3>New Inquiries</h3>
              <div class="metric-val" style="color:<?php echo $newInquiriesCount > 0 ? '#b91c1c' : '#1c1815'; ?>;"><?php echo $newInquiriesCount; ?></div>
            </div>
            <div class="metric-icon-wrap"><span class="material-symbols-outlined">mark_email_unread</span></div>
          </div>
          <div class="metric-card">
            <div class="metric-info">
              <h3>Replied Inquiries</h3>
              <div class="metric-val"><?php echo count(array_filter($contact_inquiries, fn($x) => ($x['status'] ?? '') === 'replied')); ?></div>
            </div>
            <div class="metric-icon-wrap"><span class="material-symbols-outlined">chat</span></div>
          </div>
          <div class="metric-card">
            <div class="metric-info">
              <h3>Resolved</h3>
              <div class="metric-val"><?php echo count(array_filter($contact_inquiries, fn($x) => ($x['status'] ?? '') === 'resolved')); ?></div>
            </div>
            <div class="metric-icon-wrap"><span class="material-symbols-outlined">check_circle</span></div>
          </div>
        </div>

        <div class="admin-box">
          <div class="admin-box-header">
            <div>
              <h2 class="admin-box-title">Contact Form Inquiries Management</h2>
              <p class="admin-box-subtitle">Customer messages received from the storefront Contact page. Reply directly via WhatsApp or Email.</p>
            </div>
            <a href="/contact.php" target="_blank" class="btn btn-secondary">
              <span class="material-symbols-outlined" style="font-size:17px; vertical-align:middle;">contact_support</span> <span>View Live Contact Page</span> <span class="material-symbols-outlined" style="font-size:15px; vertical-align:middle;">open_in_new</span>
            </a>
          </div>

          <!-- Toolbar -->
          <div class="admin-toolbar">
            <div class="admin-search-wrap">
              <span class="admin-search-icon"><span class="material-symbols-outlined">search</span></span>
              <input type="text" id="inquirySearchInput" class="admin-search-input" placeholder="Search by patron name, phone (+91...), email, subject, or message keyword...">
            </div>
            <select id="inquiryStatusFilter" class="admin-select">
              <option value="all">All Inquiries</option>
              <option value="new">New / Unread</option>
              <option value="replied">Replied</option>
              <option value="resolved">Resolved</option>
              <option value="archived">Archived</option>
            </select>
          </div>

          <!-- Inquiries Table -->
          <div class="admin-table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>ID & Date</th>
                  <th>Patron Details</th>
                  <th>Inquiry Nature</th>
                  <th>Message Preview</th>
                  <th>Status</th>
                  <th>Reply Status</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody id="inquiriesTableBody">
                <?php if (empty($contact_inquiries)): ?>
                  <tr>
                    <td colspan="7" style="text-align:center; padding:30px; color:#888;">
                      No contact form inquiries yet. Test by submitting a note on <a href="/contact.php" target="_blank" style="color:var(--admin-gold);">contact.php</a>.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($contact_inquiries as $inq): ?>
                    <?php 
                      $inqId = $inq['id'];
                      $inqName = $inq['name'];
                      $inqPhone = $inq['phone'];
                      $inqEmail = $inq['email'] ?? '';
                      $inqSub = $inq['subject'] ?? 'General Inquiry';
                      $inqMsg = $inq['message'] ?? '';
                      $inqSt = $inq['status'] ?? 'new';
                      $inqReplyCh = $inq['reply_channel'] ?? '';
                      $inqReplyMsg = $inq['reply_message'] ?? '';
                      $rawPhone = preg_replace('/\D/', '', $inqPhone);
                      $cleanPhone = str_starts_with($rawPhone, '91') ? $rawPhone : ('91' . $rawPhone);
                      $defaultWaMsg = "Namaste {$inqName} ji, thank you for contacting Murli Creations Studio regarding '{$inqSub}'. Master Craftsman Murli Prasad has received your request. How may we assist your home sanctum today?";
                    ?>
                    <tr class="inquiry-table-row" id="inquiry-row-<?php echo $inqId; ?>"
                        data-id="<?php echo $inqId; ?>"
                        data-name="<?php echo htmlspecialchars($inqName); ?>"
                        data-phone="<?php echo htmlspecialchars($inqPhone); ?>"
                        data-email="<?php echo htmlspecialchars($inqEmail); ?>"
                        data-subject="<?php echo htmlspecialchars($inqSub); ?>"
                        data-status="<?php echo htmlspecialchars($inqSt); ?>">
                      <td>
                        <strong>#INQ-<?php echo $inqId; ?></strong><br>
                        <small style="color:#777;"><?php echo htmlspecialchars($inq['created_at'] ?? 'Recent'); ?></small>
                      </td>
                      <td>
                        <strong><?php echo htmlspecialchars($inqName); ?></strong><br>
                        <small style="color:#555;"><span class="material-symbols-outlined" style="font-size:14px; vertical-align:middle;">phone</span> <a href="tel:<?php echo htmlspecialchars($inqPhone); ?>"><?php echo htmlspecialchars($inqPhone); ?></a></small><br>
                        <?php if (!empty($inqEmail)): ?>
                          <small style="color:#777;"><span class="material-symbols-outlined" style="font-size:14px; vertical-align:middle;">mail</span> <a href="mailto:<?php echo htmlspecialchars($inqEmail); ?>"><?php echo htmlspecialchars($inqEmail); ?></a></small>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="badge badge-gold"><?php echo htmlspecialchars($inqSub); ?></span>
                      </td>
                      <td style="max-width:280px;">
                        <div style="font-size:0.83rem; color:#444; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                          <?php echo htmlspecialchars($inqMsg); ?>
                        </div>
                      </td>
                      <td>
                        <span class="badge <?php 
                          if ($inqSt === 'resolved') echo 'badge-success';
                          elseif ($inqSt === 'replied') echo 'badge-info';
                          elseif ($inqSt === 'new') echo 'badge-danger';
                          else echo 'badge-warning';
                        ?>" id="inq-badge-<?php echo $inqId; ?>">
                          ● <?php echo ucfirst($inqSt); ?>
                        </span>
                      </td>
                      <td>
                        <?php if ($inqSt === 'replied' || !empty($inqReplyMsg)): ?>
                          <small style="color:#16a34a; font-weight:600;">
                            <span class="material-symbols-outlined" style="font-size:15px; vertical-align:middle; color:#15803d;">check</span> Replied via <?php echo ucfirst($inqReplyCh ?: 'Studio'); ?>
                          </small><br>
                          <small style="color:#777; font-size:0.75rem;"><?php echo htmlspecialchars(substr($inqReplyMsg, 0, 45)); ?>...</small>
                        <?php else: ?>
                          <small style="color:#b45309; font-weight:600;"><span class="material-symbols-outlined" style="font-size:15px; vertical-align:middle; color:#b45309;">hourglass_empty</span> Awaiting Reply</small>
                        <?php endif; ?>
                      </td>
                      <td style="text-align:right;">
                        <div style="display:inline-flex; gap:6px;">
                          <!-- WhatsApp Reply -->
                          <a href="https://wa.me/<?php echo $cleanPhone; ?>?text=<?php echo urlencode($defaultWaMsg); ?>" 
                             target="_blank" 
                             class="btn btn-sm btn-gold btn-whatsapp-reply" 
                             data-id="<?php echo $inqId; ?>"
                             title="1-Click WhatsApp Reply">
                            WhatsApp
                          </a>
                          
                          <!-- Email Reply Modal Trigger -->
                          <?php if (!empty($inqEmail)): ?>
                            <button type="button" 
                                    class="btn btn-sm btn-secondary btn-open-reply-modal" 
                                    data-id="<?php echo $inqId; ?>"
                                    data-name="<?php echo htmlspecialchars($inqName); ?>"
                                    data-email="<?php echo htmlspecialchars($inqEmail); ?>"
                                    data-subject="<?php echo htmlspecialchars($inqSub); ?>"
                                    data-message="<?php echo htmlspecialchars($inqMsg); ?>"
                                    title="Reply via Email">
                              Email
                            </button>
                          <?php endif; ?>

                          <!-- Status Dropdown Quick Changer -->
                          <select class="admin-select inq-status-changer" data-id="<?php echo $inqId; ?>" style="padding:4px 8px; font-size:0.78rem;">
                            <option value="new" <?php if ($inqSt === 'new') echo 'selected'; ?>>New</option>
                            <option value="read" <?php if ($inqSt === 'read') echo 'selected'; ?>>Read</option>
                            <option value="replied" <?php if ($inqSt === 'replied') echo 'selected'; ?>>Replied</option>
                            <option value="resolved" <?php if ($inqSt === 'resolved') echo 'selected'; ?>>Resolved</option>
                            <option value="archived" <?php if ($inqSt === 'archived') echo 'selected'; ?>>Archived</option>
                          </select>

                          <!-- Delete -->
                          <button type="button" class="btn btn-sm btn-danger btn-delete-inquiry" data-id="<?php echo $inqId; ?>" title="Delete Inquiry">&times;</button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- =====================================================================
           TAB: NAVBAR PAGES CONTENT & STUDIO DETAILS (0 to End CMS)
           ===================================================================== -->
      <div class="admin-tab-pane" id="tab-pages-content">
        <!-- Subnav Pills -->
        <div class="cms-subnav">
          <button type="button" class="cms-subnav-btn active" data-sub="contact-info"><span class="material-symbols-outlined">storefront</span> Studio & Contact Page</button>
          <button type="button" class="cms-subnav-btn" data-sub="home-page"><span class="material-symbols-outlined">auto_stories</span> Home Page Hero & Story</button>
          <button type="button" class="cms-subnav-btn" data-sub="header-footer"><span class="material-symbols-outlined">view_compact</span> Header, Footer & Socials</button>
          <button type="button" class="cms-subnav-btn" data-sub="shipping-policies"><span class="material-symbols-outlined">local_shipping</span> Shipping & Delivery Policies</button>
          <button type="button" class="cms-subnav-btn" data-sub="faqs-editor"><span class="material-symbols-outlined">quiz</span> Interactive FAQs Editor</button>
          <button type="button" class="cms-subnav-btn" data-sub="media-assets"><span class="material-symbols-outlined">photo_library</span> Media Library</button>
          <button type="button" class="cms-subnav-btn" data-sub="holiday-mode"><span class="material-symbols-outlined">campaign</span> Studio Notice & Holiday</button>
        </div>

        <?php 
          $studio = $site_settings['studio'] ?? [];
          $shipping = $site_settings['shipping'] ?? [];
          $social = $site_settings['social'] ?? [];
          $footer = $site_settings['footer'] ?? [];
          $ann = $site_settings['announcement'] ?? [];
          $hero = $homepage_content['hero'] ?? [];
          $story = $homepage_content['story'] ?? [];
          $comm = $homepage_content['custom_commission'] ?? [];
          $festiveOffer = $homepage_content['festive_offer'] ?? [];
          $faqsList = !empty($site_settings['faqs']) ? $site_settings['faqs'] : ($homepage_content['faqs'] ?? []);
        ?>

        <!-- SUBPANEL 1: Studio & Contact Page Info -->
        <div class="cms-section-panel active" id="sub-contact-info">
          <div class="admin-box">
            <div class="admin-box-header">
              <div>
                <h3 class="admin-box-title">Studio Identity & Contact Page Details</h3>
                <p class="admin-box-subtitle">Edit studio contact numbers, WhatsApp helpline, physical address, and workshop visiting hours</p>
              </div>
              <button type="button" class="btn btn-gold btn-submit-cms-form" data-form="formStudioContact">Save Studio Details</button>
            </div>

            <form id="formStudioContact">
              <input type="hidden" name="action" value="save_settings">
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label" for="cfg_studio_name">Studio Wordmark / Name</label>
                  <input type="text" id="cfg_studio_name" name="studio_name" class="form-control" value="<?php echo htmlspecialchars($studio['name'] ?? 'Murli Creations'); ?>" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_studio_tagline">Tagline</label>
                  <input type="text" id="cfg_studio_tagline" name="studio_tagline" class="form-control" value="<?php echo htmlspecialchars($studio['tagline'] ?? 'Handcrafted Lippan Art & Sacred Devotional Heritage'); ?>">
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_studio_founder">Master Craftsman / Founder</label>
                  <input type="text" id="cfg_studio_founder" name="studio_founder" class="form-control" value="<?php echo htmlspecialchars($studio['founder'] ?? 'Master Craftsman Murli Prasad'); ?>">
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_studio_phone">Direct Studio Phone Number *</label>
                  <input type="text" id="cfg_studio_phone" name="studio_phone" class="form-control" value="<?php echo htmlspecialchars($studio['phone'] ?? '+91 95823 16454'); ?>" required>
                  <small style="color:#777; font-size:0.75rem;">Displayed on Contact page, footer and receipts</small>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_studio_whatsapp">WhatsApp Direct Helpline *</label>
                  <input type="text" id="cfg_studio_whatsapp" name="studio_whatsapp" class="form-control" value="<?php echo htmlspecialchars($studio['whatsapp'] ?? '+91 95823 16454'); ?>" required>
                  <small style="color:#777; font-size:0.75rem;">Powers the floating WhatsApp button across all pages</small>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_studio_email">Customer Care Email *</label>
                  <input type="email" id="cfg_studio_email" name="studio_email" class="form-control" value="<?php echo htmlspecialchars($studio['email'] ?? 'contact@murlicreations.com'); ?>" required>
                </div>
                <div class="form-group full-width">
                  <label class="form-label" for="cfg_studio_address">Studio & Workshop Physical Address *</label>
                  <textarea id="cfg_studio_address" name="studio_address" class="form-control" rows="2" required><?php echo htmlspecialchars($studio['address'] ?? 'ITL Twin Tower, Netaji Subhash Place, Pitampura, Delhi - 110034, India'); ?></textarea>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_studio_hours">Workshop Visiting Hours</label>
                  <input type="text" id="cfg_studio_hours" name="studio_hours" class="form-control" value="<?php echo htmlspecialchars($studio['hours'] ?? 'Monday – Saturday: 10:00 AM – 7:30 PM (IST)'); ?>">
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_studio_location">Studio City / Region</label>
                  <input type="text" id="cfg_studio_location" name="studio_location" class="form-control" value="<?php echo htmlspecialchars($studio['location'] ?? 'Pitampura, Delhi, India'); ?>">
                </div>
              </div>
              <div style="margin-top:16px;">
                <button type="submit" class="btn btn-gold">Save Studio & Contact Details &rarr;</button>
              </div>
            </form>
          </div>
        </div>

        <!-- SUBPANEL 2: Home Page Hero, Story, Festive Offer & Live Split Preview -->
        <div class="cms-section-panel" id="sub-home-page">
          <!-- 1-Click Festive Campaign Presets Bar -->
          <div class="festive-presets-bar">
            <div class="festive-presets-title">
              <span class="material-symbols-outlined" style="font-size:24px; color:#D4AF37;">celebration</span>
              <div>
                <strong>1-Click Festive Campaign Presets:</strong>
                <span style="color:var(--admin-text-muted); font-size:0.8rem; margin-left:6px;">Instantly load ready-made festive headlines, promo codes, and blessings</span>
              </div>
            </div>
            <div class="festive-presets-buttons">
              <button type="button" class="btn-preset-theme" data-preset="diwali"><span class="material-symbols-outlined" style="font-size:16px;">flare</span> Diwali & Dhanteras</button>
              <button type="button" class="btn-preset-theme" data-preset="shyamji"><span class="material-symbols-outlined" style="font-size:16px;">spa</span> Khatu Shyam Mela</button>
              <button type="button" class="btn-preset-theme" data-preset="navratri"><span class="material-symbols-outlined" style="font-size:16px;">local_florist</span> Navratri Mahotsav</button>
              <button type="button" class="btn-preset-theme" data-preset="grihapravesh"><span class="material-symbols-outlined" style="font-size:16px;">home</span> Griha Pravesh</button>
              <button type="button" class="btn-preset-theme btn-preset-default" data-preset="default"><span class="material-symbols-outlined" style="font-size:16px;">nature</span> Standard Heritage</button>
            </div>
          </div>

          <!-- Split Layout: Form Inputs on Left + Live Interactive Storefront Preview on Right -->
          <div class="cms-split-grid">
            <!-- Left Column: Form Settings -->
            <div class="cms-form-col">
              <div class="admin-box">
                <div class="admin-box-header">
                  <div>
                    <h3 class="admin-box-title">Home Page & Festive Content Editor</h3>
                    <p class="admin-box-subtitle">Changes instantly reflect in the live interactive preview on the right</p>
                  </div>
                  <button type="button" class="btn btn-gold btn-submit-cms-form" data-form="formHomeCopy">Save All Changes</button>
                </div>

                <form id="formHomeCopy">
                  <input type="hidden" name="action" value="save_homepage_content">
                  <input type="hidden" name="has_festive_offer_form" value="1">

                  <!-- 1. Hero Showcase -->
                  <h4 class="cms-form-heading" style="margin-top:0;">1. Hero Banner Showcase</h4>
                  <div class="form-grid">
                    <div class="form-group full-width">
                      <label class="form-label" for="cfg_hero_badge">Hero Badge Tag Text</label>
                      <input type="text" id="cfg_hero_badge" name="hero_badge" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($hero['badge'] ?? 'Direct Studio Helpline & WhatsApp Support'); ?>">
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_hero_title_main">Main Hero Title *</label>
                      <input type="text" id="cfg_hero_title_main" name="hero_title_main" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($hero['title_main'] ?? 'Authentic Handcrafted Lippan Art for'); ?>" required>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_hero_title_highlight">Hero Gold Highlight Title *</label>
                      <input type="text" id="cfg_hero_title_highlight" name="hero_title_highlight" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($hero['title_highlight'] ?? 'Soulful Living Spaces'); ?>" required>
                    </div>
                    <div class="form-group full-width">
                      <label class="form-label" for="cfg_hero_description">Hero Description</label>
                      <textarea id="cfg_hero_description" name="hero_description" class="form-control live-preview-trigger" rows="3"><?php echo htmlspecialchars($hero['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_hero_cta_primary">Primary Button Label</label>
                      <input type="text" id="cfg_hero_cta_primary" name="hero_cta_primary_text" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($hero['cta_primary_text'] ?? 'Explore Catalog'); ?>">
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_hero_cta_link">Primary Button URL</label>
                      <input type="text" id="cfg_hero_cta_link" name="hero_cta_primary_link" class="form-control" value="<?php echo htmlspecialchars($hero['cta_primary_link'] ?? '/shop.php'); ?>">
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_hero_cta_secondary">Secondary Button Label</label>
                      <input type="text" id="cfg_hero_cta_secondary" name="hero_cta_secondary_text" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($hero['cta_secondary_text'] ?? 'About Our Studio'); ?>">
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_hero_cta_sec_link">Secondary Button URL</label>
                      <input type="text" id="cfg_hero_cta_sec_link" name="hero_cta_secondary_link" class="form-control" value="<?php echo htmlspecialchars($hero['cta_secondary_link'] ?? '/about.php'); ?>">
                    </div>
                  </div>

                  <!-- 2. Special Festive Offer Card -->
                  <h4 class="cms-form-heading">2. Special Festive Offer Section (Section 6)</h4>
                  <div class="form-grid">
                    <div class="form-group full-width">
                      <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:700; color:var(--admin-text-main);">
                        <input type="checkbox" id="cfg_festive_offer_enabled" name="festive_offer_enabled" value="1" <?php echo !empty($festiveOffer['enabled']) ? 'checked' : ''; ?> class="live-preview-trigger" style="width:18px; height:18px; accent-color:#1ea659;">
                        <span>Display Special Festive Celebration Banner on Homepage</span>
                      </label>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_festive_offer_badge">Offer Badge Tag</label>
                      <input type="text" id="cfg_festive_offer_badge" name="festive_offer_badge" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($festiveOffer['badge'] ?? 'Special Festive Celebration'); ?>">
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_festive_offer_discount">Discount Label (Huge Text)</label>
                      <input type="text" id="cfg_festive_offer_discount" name="festive_offer_discount_label" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($festiveOffer['discount_label'] ?? '10% FLAT OFF'); ?>">
                    </div>
                    <div class="form-group full-width">
                      <label class="form-label" for="cfg_festive_offer_title">Festive Offer Heading *</label>
                      <input type="text" id="cfg_festive_offer_title" name="festive_offer_title" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($festiveOffer['title'] ?? 'Claim 10% Off On Your First Handcrafted Artwork'); ?>">
                    </div>
                    <div class="form-group full-width">
                      <label class="form-label" for="cfg_festive_offer_desc">Offer Description</label>
                      <textarea id="cfg_festive_offer_desc" name="festive_offer_description" class="form-control live-preview-trigger" rows="2"><?php echo htmlspecialchars($festiveOffer['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_festive_offer_coupon">Promo Coupon Code</label>
                      <input type="text" id="cfg_festive_offer_coupon" name="festive_offer_coupon_code" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($festiveOffer['coupon_code'] ?? 'MURLI10'); ?>" style="text-transform:uppercase;">
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_festive_offer_btn">Button Action Label</label>
                      <input type="text" id="cfg_festive_offer_btn" name="festive_offer_button_text" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($festiveOffer['button_text'] ?? 'Shop Auspicious Masterpieces'); ?>">
                    </div>
                  </div>

                  <!-- 3. Our Story Quote -->
                  <h4 class="cms-form-heading">3. Founder Story Quote Section</h4>
                  <div class="form-grid">
                    <div class="form-group full-width">
                      <label class="form-label" for="cfg_story_quote">Story Quote Text</label>
                      <textarea id="cfg_story_quote" name="story_quote" class="form-control live-preview-trigger" rows="2"><?php echo htmlspecialchars($story['quote'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_story_author">Quote Author</label>
                      <input type="text" id="cfg_story_author" name="story_author" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($story['author'] ?? 'Murli Creations'); ?>">
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="cfg_story_title">Story Heading</label>
                      <input type="text" id="cfg_story_title" name="story_title" class="form-control live-preview-trigger" value="<?php echo htmlspecialchars($story['title'] ?? "From a Mother's Hands to Murli Creations"); ?>">
                    </div>
                  </div>

                  <!-- 4. Bespoke Art Commission -->
                  <h4 class="cms-form-heading">4. Bespoke Art Commission Prompt</h4>
                  <div class="form-grid">
                    <div class="form-group">
                      <label class="form-label" for="cfg_comm_title">Commission Section Title</label>
                      <input type="text" id="cfg_comm_title" name="custom_commission_title" class="form-control" value="<?php echo htmlspecialchars($comm['title'] ?? 'Commission a Custom Masterpiece'); ?>">
                    </div>
                    <div class="form-group full-width">
                      <label class="form-label" for="cfg_comm_desc">Commission Description</label>
                      <textarea id="cfg_comm_desc" name="custom_commission_desc" class="form-control" rows="2"><?php echo htmlspecialchars($comm['description'] ?? ''); ?></textarea>
                    </div>
                  </div>

                  <div style="margin-top:24px;">
                    <button type="submit" class="btn btn-gold">Save Home Page Copy &rarr;</button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Right Column: Interactive Live Storefront Preview Simulator -->
            <div class="cms-preview-col">
              <div class="live-preview-container">
                <div class="browser-mockup-bar">
                  <div class="browser-dots">
                    <span class="dot red"></span>
                    <span class="dot yellow"></span>
                    <span class="dot green"></span>
                  </div>
                  <div class="browser-url">
                    <span class="material-symbols-outlined" style="font-size:16px; color:#10b981; vertical-align:middle; margin-right:4px;">lock</span> https://murlicreations.com
                  </div>
                  <div class="preview-device-toggle">
                    <button type="button" class="btn-device-toggle active" data-device="desktop" title="Preview Desktop Layout"><span class="material-symbols-outlined">desktop_windows</span></button>
                    <button type="button" class="btn-device-toggle" data-device="mobile" title="Preview Mobile Layout (375px)"><span class="material-symbols-outlined">smartphone</span></button>
                    <a href="/" target="_blank" class="preview-ext-link" title="Open Real Storefront in New Tab"><span class="material-symbols-outlined" style="font-size:15px;">open_in_new</span></a>
                  </div>
                </div>

                <div class="preview-status-pill">
                  <span class="pulse-dot"></span> <span id="previewStatusText">Live Preview Active</span>
                </div>

                <div class="live-preview-viewport">
                  <div class="device-screen" id="deviceScreenWrapper">
                    <!-- Top Announcement Bar Preview -->
                    <div class="preview-announcement-strip" id="prevAnnStrip">
                      <span id="prevAnnText"><?php echo htmlspecialchars($ann['text'] ?? 'Festive Offer: Use code {coupon} for 10% OFF on all orders!'); ?></span>
                    </div>

                    <!-- Header Lockup Strip Preview -->
                    <div class="preview-header-strip">
                      <div style="font-size:1.1rem; color:#fff;"><span class="material-symbols-outlined">menu</span></div>
                      <div class="preview-brand-lockup">
                        <span style="color:#d4af37; font-size:1.2rem;">🦚</span>
                        <div>
                          <strong style="letter-spacing:1.5px; font-size:0.85rem; font-family:'Cinzel', serif; color:#fff;">MURLI</strong>
                          <span style="font-size:0.55rem; letter-spacing:1px; color:#d4af37; display:block; line-height:1;">CREATIONS</span>
                        </div>
                      </div>
                      <div style="display:flex; gap:10px; font-size:0.9rem; color:#fff;">
                        <span class="material-symbols-outlined" style="font-size:16px;">search</span>
                        <span class="material-symbols-outlined" style="font-size:16px;">shopping_bag</span>
                      </div>
                    </div>

                    <!-- Hero Showcase Preview Section -->
                    <div class="preview-hero-box">
                      <div class="preview-badge-pills">
                        <span class="preview-badge-pill" id="prevHeroBadge"><?php echo htmlspecialchars($hero['badge'] ?? 'Direct Studio Helpline & WhatsApp Support'); ?></span>
                      </div>
                      <h2 class="preview-hero-title">
                        <span id="prevHeroTitleMain"><?php echo htmlspecialchars($hero['title_main'] ?? 'Authentic Handcrafted Lippan Art for'); ?></span>
                        <span class="preview-hero-highlight" id="prevHeroTitleHighlight"><?php echo htmlspecialchars($hero['title_highlight'] ?? 'Soulful Living Spaces'); ?></span>
                      </h2>
                      <p class="preview-hero-desc" id="prevHeroDesc">
                        <?php echo htmlspecialchars($hero['description'] ?? 'Bring auspicious energy and timeless Indian grandeur into your home. Master artisan Murli Prasad blends sacred mud clay, precision mirrors, and vibrant devotions into heirloom-grade wall masterpieces.'); ?>
                      </p>
                      <div class="preview-hero-actions">
                        <span class="preview-btn-primary" id="prevHeroCtaPrimary"><?php echo htmlspecialchars($hero['cta_primary_text'] ?? 'Explore Catalog'); ?> &rarr;</span>
                        <span class="preview-btn-secondary" id="prevHeroCtaSecondary"><?php echo htmlspecialchars($hero['cta_secondary_text'] ?? 'About Our Studio'); ?></span>
                      </div>

                      <div class="preview-hero-img-wrap">
                        <img src="/img-prodcut/IMG_4041.png" alt="Artwork Hero Preview" class="preview-hero-img">
                      </div>
                    </div>

                    <!-- Special Festive Offer Card Preview -->
                    <div class="preview-offer-box" id="prevFestiveOfferSection" style="<?php echo empty($festiveOffer['enabled']) ? 'display:none;' : ''; ?>">
                      <div class="preview-offer-header">
                        <span class="preview-offer-tag" id="prevOfferBadge"><?php echo htmlspecialchars($festiveOffer['badge'] ?? 'Special Festive Celebration'); ?></span>
                        <div class="preview-offer-discount" id="prevOfferDiscount"><?php echo htmlspecialchars($festiveOffer['discount_label'] ?? '10% FLAT OFF'); ?></div>
                      </div>
                      <h4 class="preview-offer-title" id="prevOfferTitle"><?php echo htmlspecialchars($festiveOffer['title'] ?? 'Claim 10% Off On Your First Handcrafted Artwork'); ?></h4>
                      <p class="preview-offer-desc" id="prevOfferDesc"><?php echo htmlspecialchars($festiveOffer['description'] ?? 'Every creation comes with certified authenticity and authentic brass hooks ready to grace your walls.'); ?></p>
                      <div class="preview-offer-actions">
                        <span class="preview-copy-code" id="prevOfferCode">Copy Code: <?php echo htmlspecialchars($festiveOffer['coupon_code'] ?? 'MURLI10'); ?></span>
                        <span class="preview-shop-btn" id="prevOfferBtn"><?php echo htmlspecialchars($festiveOffer['button_text'] ?? 'Shop Auspicious Masterpieces'); ?> &rarr;</span>
                      </div>
                    </div>

                    <!-- Our Story Quote Preview -->
                    <div class="preview-story-box">
                      <div style="font-size:0.65rem; text-transform:uppercase; letter-spacing:1px; color:#deb887; font-weight:700;" id="prevStoryTitle">
                        <?php echo htmlspecialchars($story['title'] ?? "From a Mother's Hands to Murli Creations"); ?>
                      </div>
                      <p class="preview-story-quote" id="prevStoryQuote">
                        "<?php echo htmlspecialchars($story['quote'] ?? 'What started with watching my mother create with her hands became my own journey of creating with love.'); ?>"
                      </p>
                      <div class="preview-story-author" id="prevStoryAuthor">
                        — <?php echo htmlspecialchars($story['author'] ?? 'Murli Creations'); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SUBPANEL 3: Header, Footer & Socials -->
        <div class="cms-section-panel" id="sub-header-footer">
          <div class="admin-box">
            <div class="admin-box-header">
              <div>
                <h3 class="admin-box-title">Global Header, Footer & Social Media Channels</h3>
                <p class="admin-box-subtitle">Configure top announcement bar, footer about text, copyright line, and official social media profile URLs</p>
              </div>
              <button type="button" class="btn btn-gold btn-submit-cms-form" data-form="formHeaderFooter">Save Header & Footer</button>
            </div>

            <form id="formHeaderFooter">
              <input type="hidden" name="action" value="save_settings">
              <h4 class="cms-form-heading" style="margin-top:0;">Top Announcement Bar Banner</h4>
              <div class="form-grid">
                <div class="form-group full-width">
                  <label class="form-label" for="cfg_ann_text">Announcement Bar Text (Use {coupon} to highlight code)</label>
                  <input type="text" id="cfg_ann_text" name="announcement_text" class="form-control" value="<?php echo htmlspecialchars($ann['text'] ?? 'Festive Offer: Use code {coupon} for 10% OFF on all orders!'); ?>" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_ann_coupon">Highlight Promo Code</label>
                  <input type="text" id="cfg_ann_coupon" name="announcement_coupon" class="form-control" value="<?php echo htmlspecialchars($ann['coupon_code'] ?? 'MURLI10'); ?>">
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_ann_badge">Badge Tag</label>
                  <input type="text" id="cfg_ann_badge" name="announcement_badge" class="form-control" value="<?php echo htmlspecialchars($ann['badge'] ?? '✨ Special Celebration ✨'); ?>">
                </div>
                <div class="form-group full-width">
                  <label style="display:flex; align-items:center; gap:8px; font-weight:600; cursor:pointer;">
                    <input type="checkbox" name="announcement_enabled" value="1" <?php if (!empty($ann['enabled'])) echo 'checked'; ?>>
                    <span>Display top sticky promotional announcement bar on all store pages</span>
                  </label>
                </div>
              </div>

              <h4 class="cms-form-heading">Official Social Media Links</h4>
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label" for="cfg_soc_insta">Instagram Profile URL</label>
                  <input type="url" id="cfg_soc_insta" name="social_instagram" class="form-control" value="<?php echo htmlspecialchars($social['instagram'] ?? 'https://instagram.com/murlicreationsofficial'); ?>">
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_soc_fb">Facebook Page URL</label>
                  <input type="url" id="cfg_soc_fb" name="social_facebook" class="form-control" value="<?php echo htmlspecialchars($social['facebook'] ?? ''); ?>">
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_soc_yt">YouTube Channel URL</label>
                  <input type="url" id="cfg_soc_yt" name="social_youtube" class="form-control" value="<?php echo htmlspecialchars($social['youtube'] ?? ''); ?>">
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_soc_li">LinkedIn Profile URL</label>
                  <input type="url" id="cfg_soc_li" name="social_linkedin" class="form-control" value="<?php echo htmlspecialchars($social['linkedin'] ?? ''); ?>">
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_soc_pin">Pinterest Profile URL</label>
                  <input type="url" id="cfg_soc_pin" name="social_pinterest" class="form-control" value="<?php echo htmlspecialchars($social['pinterest'] ?? ''); ?>">
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_soc_lt">Linktree URL</label>
                  <input type="url" id="cfg_soc_lt" name="social_linktree" class="form-control" value="<?php echo htmlspecialchars($social['linktree'] ?? ''); ?>">
                </div>
              </div>

              <h4 class="cms-form-heading">Footer Information</h4>
              <div class="form-grid">
                <div class="form-group full-width">
                  <label class="form-label" for="cfg_footer_about">Footer Studio About Text</label>
                  <textarea id="cfg_footer_about" name="footer_about_text" class="form-control" rows="2"><?php echo htmlspecialchars($footer['about_text'] ?? 'Sacred mud & mirror art handcrafted by Murli Prasad. Bringing positive energy, devotion, and traditional Indian grandeur to homes across the globe.'); ?></textarea>
                </div>
                <div class="form-group full-width">
                  <label class="form-label" for="cfg_footer_copyright">Footer Copyright Notice</label>
                  <input type="text" id="cfg_footer_copyright" name="footer_copyright" class="form-control" value="<?php echo htmlspecialchars($footer['copyright'] ?? ('© ' . date('Y') . ' Murli Creations. Handcrafted in India with Devotion. All Rights Reserved.')); ?>">
                </div>
              </div>

              <div style="margin-top:20px;">
                <button type="submit" class="btn btn-gold">Save Header, Footer & Socials &rarr;</button>
              </div>
            </form>
          </div>
        </div>

        <!-- SUBPANEL 4: Shipping Policies -->
        <div class="cms-section-panel" id="sub-shipping-policies">
          <div class="admin-box">
            <div class="admin-box-header">
              <div>
                <h3 class="admin-box-title">Shipping, Packaging & Store Logistics Defaults</h3>
                <p class="admin-box-subtitle">Adjust delivery charge rules, free shipping eligibility, and transit protection notes</p>
              </div>
              <button type="button" class="btn btn-gold btn-submit-cms-form" data-form="formShippingPolicies">Save Shipping Rules</button>
            </div>

            <form id="formShippingPolicies">
              <input type="hidden" name="action" value="save_settings">
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label" for="cfg_ship_free">Free Shipping Minimum Threshold (₹) *</label>
                  <input type="number" id="cfg_ship_free" name="shipping_free_threshold" class="form-control" value="<?php echo htmlspecialchars($shipping['free_threshold'] ?? 2500); ?>" required>
                  <small style="color:#777; font-size:0.75rem;">Orders above this value receive automated zero shipping</small>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_ship_charge">Standard Flat Shipping Delivery Fee (₹) *</label>
                  <input type="number" id="cfg_ship_charge" name="shipping_standard_charge" class="form-control" value="<?php echo htmlspecialchars($shipping['standard_charge'] ?? 199); ?>" required>
                  <small style="color:#777; font-size:0.75rem;">Charged when order is below threshold</small>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_ship_dispatch">Estimated Dispatch Timeframe</label>
                  <input type="text" id="cfg_ship_dispatch" name="shipping_dispatch_time" class="form-control" value="<?php echo htmlspecialchars($shipping['dispatch_time'] ?? 'Dispatched in 3 to 5 business days'); ?>">
                </div>
                <div class="form-group">
                  <label class="form-label" for="cfg_ship_area">Serviceable Regions</label>
                  <input type="text" id="cfg_ship_area" name="shipping_serviceable_area" class="form-control" value="<?php echo htmlspecialchars($shipping['serviceable_area'] ?? 'Pan-India Insured Express Courier'); ?>">
                </div>
                <div class="form-group full-width">
                  <label class="form-label" for="cfg_ship_safe_note">Transit Safety & Insurance Guarantee Note</label>
                  <input type="text" id="cfg_ship_safe_note" name="shipping_transit_safe_note" class="form-control" value="<?php echo htmlspecialchars($shipping['transit_safe_note'] ?? '100% Insured Safe Doorstep Delivery'); ?>">
                </div>
              </div>
              <div style="margin-top:20px;">
                <button type="submit" class="btn btn-gold">Save Shipping Policies &rarr;</button>
              </div>
            </form>
          </div>
        </div>

        <!-- SUBPANEL 5: FAQs Editor -->
        <div class="cms-section-panel" id="sub-faqs-editor">
          <div class="admin-box">
            <div class="admin-box-header">
              <div>
                <h3 class="admin-box-title">Interactive Contact Page & Store FAQs Editor</h3>
                <p class="admin-box-subtitle">Add, edit, or remove questions and answers displayed in the FAQ accordion on contact.php and home page</p>
              </div>
              <div style="display:flex; gap:10px; align-items:center;">
                <button type="button" class="btn btn-secondary" id="btnAddNewFaqRow">+ Add Question</button>
                <button type="button" class="btn btn-gold btn-submit-cms-form" data-form="formFaqsEditor"><span class="material-symbols-outlined">save</span> Save All FAQs</button>
              </div>
            </div>

            <form id="formFaqsEditor">
              <input type="hidden" name="action" value="save_settings">
              <div id="faqsListContainer">
                <?php foreach ($faqsList as $fIdx => $fItem): ?>
                  <?php 
                    $fQ = $fItem['q'] ?? $fItem['question'] ?? '';
                    $fA = $fItem['a'] ?? $fItem['answer'] ?? '';
                  ?>
                  <div class="faq-builder-item" data-index="<?php echo $fIdx; ?>">
                    <div class="faq-builder-header">
                      <span class="faq-builder-num">Question #<?php echo ($fIdx + 1); ?></span>
                      <button type="button" class="btn btn-sm btn-danger btn-remove-faq" title="Remove FAQ">&times; Delete</button>
                    </div>
                    <div class="form-group" style="margin-bottom:10px;">
                      <label class="form-label">Question Text *</label>
                      <input type="text" class="form-control faq-input-q" value="<?php echo htmlspecialchars($fQ); ?>" required placeholder="e.g. Can I place a custom-sized Lippan Art order?">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label">Answer Text *</label>
                      <textarea class="form-control faq-input-a" rows="3" required placeholder="Detailed answer..."><?php echo htmlspecialchars($fA); ?></textarea>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

              <div style="margin-top:20px; display:flex; gap:12px;">
                <button type="submit" class="btn btn-gold">Save All FAQs &rarr;</button>
                <button type="button" class="btn btn-secondary" id="btnAddNewFaqRowBottom">+ Add Another Question</button>
              </div>
            </form>
          </div>
        </div>

        <!-- SUBPANEL 6: Studio Media & Asset Library -->
        <div class="cms-section-panel" id="sub-media-assets">
          <div class="admin-box">
            <div class="admin-box-header">
              <div>
                <h3 class="admin-box-title"><span class="material-symbols-outlined" style="font-size:22px; vertical-align:middle; margin-right:8px; color:#1EA659;">photo_library</span> Studio Media & Image Asset Library</h3>
                <p class="admin-box-subtitle">Browse all uploaded sacred artwork photos, banners, and brand insignia. Click any asset to instantly copy its URL for products or CMS.</p>
              </div>
              <span class="badge badge-success"><?php echo count($media_assets); ?> Assets Found</span>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:16px; margin-top:16px;">
              <?php foreach ($media_assets as $ma): ?>
                <div style="background:#ffffff; border:1px solid var(--admin-border); border-radius:10px; overflow:hidden; box-shadow:var(--shadow-card); display:flex; flex-direction:column;">
                  <div style="position:relative; width:100%; padding-top:85%; background:#eef8f1; overflow:hidden;">
                    <img src="<?php echo htmlspecialchars($ma['url']); ?>" alt="" style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover;">
                  </div>
                  <div style="padding:10px 12px; display:flex; flex-direction:column; gap:4px; flex:1;">
                    <small style="font-weight:700; color:var(--admin-text-main); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo htmlspecialchars($ma['filename']); ?>">
                      <?php echo htmlspecialchars($ma['filename']); ?>
                    </small>
                    <small style="color:var(--admin-text-muted); font-size:0.72rem;"><?php echo $ma['size_kb']; ?> KB • <?php echo strtoupper($ma['ext']); ?></small>
                    <button type="button" class="btn btn-sm btn-secondary btn-copy-media-url" data-url="<?php echo htmlspecialchars($ma['url']); ?>" style="margin-top:auto; font-size:0.75rem; padding:4px 8px;">
                      <span class="material-symbols-outlined" style="font-size:16px;">content_copy</span> Copy URL
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- SUBPANEL 7: Studio Holiday & Maintenance Notice -->
        <div class="cms-section-panel" id="sub-holiday-mode">
          <div class="admin-box">
            <div class="admin-box-header">
              <div>
                <h3 class="admin-box-title"><span class="material-symbols-outlined" style="font-size:22px; vertical-align:middle; margin-right:8px; color:#EF4444;">campaign</span> Studio Festival Holiday & Vacation Notice</h3>
                <p class="admin-box-subtitle">Display a top announcement bar during festivals or studio workshop maintenance without interrupting browsing</p>
              </div>
              <button type="button" class="btn btn-gold btn-submit-cms-form" data-form="formHolidayNotice">Save Notice</button>
            </div>

            <form id="formHolidayNotice">
              <input type="hidden" name="action" value="save_settings">
              <div class="form-grid">
                <div class="form-group full-width">
                  <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:700; color:var(--admin-text-main); font-size:0.9rem;">
                    <input type="checkbox" name="announcement_enabled" value="1" <?php echo !empty($ann['enabled']) ? 'checked' : ''; ?> style="width:18px; height:18px; accent-color:#1ea659;">
                    <span>Display top announcement bar banner across all store pages</span>
                  </label>
                </div>

                <div class="form-group full-width">
                  <label class="form-label" for="cfg_holiday_banner_text">Banner Announcement Message *</label>
                  <input type="text" id="cfg_holiday_banner_text" name="announcement_text" class="form-control" 
                         value="<?php echo htmlspecialchars($ann['text'] ?? 'Festive Offer: Use code {coupon} for 10% OFF on all orders!'); ?>" required>
                  <small style="color:#666; font-size:0.78rem;">Use <code>{coupon}</code> to dynamically highlight your active promo code in bold font.</small>
                </div>

                <div class="form-group">
                  <label class="form-label" for="cfg_holiday_promo_code">Highlight Promo Code</label>
                  <input type="text" id="cfg_holiday_promo_code" name="announcement_coupon" class="form-control" 
                         value="<?php echo htmlspecialchars($ann['coupon_code'] ?? 'MURLI10'); ?>">
                </div>

                <div class="form-group">
                  <label class="form-label" for="cfg_holiday_badge_tag">Badge Tag Text</label>
                  <input type="text" id="cfg_holiday_badge_tag" name="announcement_badge" class="form-control" 
                         value="<?php echo htmlspecialchars($ann['badge'] ?? '✨ Special Celebration ✨'); ?>">
                </div>
              </div>

              <!-- Live Visual Announcement Bar Preview -->
              <div style="margin-top:20px; padding:16px; background:#f0fdf4; border:1px solid rgba(30,166,89,0.25); border-radius:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                  <strong style="font-size:0.85rem; color:#142318;"><span class="material-symbols-outlined" style="font-size:16px; vertical-align:middle;">visibility</span> Live Announcement Bar Preview:</strong>
                  <span style="font-size:0.75rem; color:#15803d; font-weight:700;"><span class="live-status-dot pulse"></span> Live Responsive Preview</span>
                </div>
                <div id="holidayLivePreviewStrip" style="background:linear-gradient(135deg, #072613 0%, #104825 100%); color:#fff; padding:10px 16px; border-radius:8px; text-align:center; font-size:0.88rem; font-weight:600; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                  <span id="holidayLiveBadge" style="background:#deb887; color:#1c1815; font-size:0.72rem; font-weight:800; padding:2px 8px; border-radius:12px; margin-right:8px;"><?php echo htmlspecialchars($ann['badge'] ?? '✨ Special Celebration ✨'); ?></span>
                  <span id="holidayLiveMessage"><?php 
                    $currMsg = $ann['text'] ?? 'Festive Offer: Use code {coupon} for 10% OFF on all orders!';
                    $currCode = $ann['coupon_code'] ?? 'MURLI10';
                    echo str_replace('{coupon}', '<strong style="color:#ffd700; text-decoration:underline;">' . htmlspecialchars($currCode) . '</strong>', htmlspecialchars($currMsg));
                  ?></span>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- =====================================================================
           TAB 3: CUSTOMER REMINDERS/ (Reminders, CRM Leads, User Details)
           ===================================================================== -->
      <div class="admin-tab-pane" id="tab-reminders">
        <!-- 1. Customer Reminders Hub -->
        <div class="admin-box">
          <div class="admin-box-header">
            <div>
              <h2 class="admin-box-title">Customer Reminders & Abandoned Cart Recovery</h2>
              <p class="admin-box-subtitle">Send 1-click WhatsApp and email follow-up reminders with personalized discount codes</p>
            </div>
          </div>

          <div class="admin-table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Customer Name</th>
                  <th>Mobile / Email</th>
                  <th>Order Reference</th>
                  <th>Type</th>
                  <th>Reminder Message</th>
                  <th>Status</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reminders as $rem): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($rem['customer_name']); ?></strong></td>
                    <td><span class="material-symbols-outlined" style="font-size:14px; vertical-align:middle;">phone</span> <?php echo htmlspecialchars($rem['customer_phone'] ?? ''); ?><br><small><?php echo htmlspecialchars($rem['customer_email'] ?? ''); ?></small></td>
                    <td><?php echo !empty($rem['order_id']) ? '<code>#' . htmlspecialchars($rem['order_id']) . '</code>' : '<span style="color:#888;">Cart Follow-up</span>'; ?></td>
                    <td><span class="badge badge-gold"><?php echo htmlspecialchars($rem['reminder_type']); ?></span></td>
                    <td style="max-width:320px; font-size:0.82rem; color:#555;"><?php echo htmlspecialchars($rem['message_body']); ?></td>
                    <td><span class="badge <?php echo $rem['status'] === 'sent' ? 'badge-success' : 'badge-warning'; ?>" id="rem-status-<?php echo $rem['id']; ?>"><?php echo ucfirst($rem['status']); ?></span></td>
                    <td style="text-align:right;">
                      <button type="button" class="btn btn-sm btn-gold btn-trigger-reminder" 
                              data-id="<?php echo $rem['id']; ?>"
                              data-phone="<?php echo htmlspecialchars($rem['customer_phone'] ?? ''); ?>"
                              data-channel="<?php echo htmlspecialchars($rem['channel'] ?? 'whatsapp'); ?>"
                              data-message="<?php echo htmlspecialchars($rem['message_body']); ?>">
                        Dispatch Reminder
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 1B. Live Abandoned Carts Recovery -->
        <div class="admin-box" style="margin-top:24px;">
          <div class="admin-box-header">
            <div>
              <h3 class="admin-box-title"><span class="material-symbols-outlined" style="font-size:22px; vertical-align:middle; margin-right:8px; color:#1EA659;">shopping_cart_checkout</span> Live Abandoned Carts & Patron Recovery</h3>
              <p class="admin-box-subtitle">Patrons who began checkout but dropped off. Send a 1-click WhatsApp message with an instant 5% discount to complete their order.</p>
            </div>
            <span class="badge badge-warning"><?php echo count($abandoned_carts); ?> Incomplete Checkouts</span>
          </div>

          <div class="admin-table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Order Reference</th>
                  <th>Customer Name & Phone</th>
                  <th>Artworks in Cart</th>
                  <th>Cart Value</th>
                  <th>Payment Status</th>
                  <th style="text-align:right;">Recovery Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($abandoned_carts)): ?>
                  <tr><td colspan="6" style="text-align:center; padding:20px; color:#888;">No abandoned carts currently. All patrons successfully completed checkout!</td></tr>
                <?php else: ?>
                  <?php foreach ($abandoned_carts as $ac): ?>
                    <?php 
                      $acPhone = preg_replace('/\D/', '', $ac['customer_phone']);
                      $acCleanPhone = str_starts_with($acPhone, '91') ? $acPhone : ('91' . $acPhone);
                      $acWaMsg = urlencode("Namaste {$ac['customer_name']} ji! 🌸 We noticed you left handcrafted Lippan Art in your cart on Murli Creations. As a token of blessings, here is an exclusive 5% extra discount code: MURLI5. Complete your sacred order here: http://127.0.0.1:8000/cart.php . With regards, Murli Creations Studio");
                    ?>
                    <tr>
                      <td><code>#<?php echo htmlspecialchars($ac['id']); ?></code><br><small style="color:#777;"><?php echo htmlspecialchars($ac['created_at']); ?></small></td>
                      <td>
                        <strong><?php echo htmlspecialchars($ac['customer_name']); ?></strong><br>
                        <small style="color:#555;"><span class="material-symbols-outlined" style="font-size:14px; vertical-align:middle;">phone</span> <?php echo htmlspecialchars($ac['customer_phone']); ?></small>
                      </td>
                      <td style="max-width:260px; font-size:0.84rem;"><?php echo htmlspecialchars($ac['items_summary']); ?></td>
                      <td><strong style="color:#15803d;">₹<?php echo number_format($ac['total_amount']); ?></strong></td>
                      <td><span class="badge badge-warning">● Incomplete</span></td>
                      <td style="text-align:right;">
                        <a href="https://wa.me/<?php echo $acCleanPhone; ?>?text=<?php echo $acWaMsg; ?>" target="_blank" class="btn btn-sm btn-success" style="font-weight:700;">
                          <span class="material-symbols-outlined" style="font-size:16px;">chat</span> Send 5% WhatsApp Offer
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 2. CRM Leads & Custom Inquiries -->
        <div class="admin-box">
          <div class="admin-box-header">
            <div>
              <h3 class="admin-box-title">CRM Inquiries & Custom Art Commissions Pipeline</h3>
              <p class="admin-box-subtitle">Leads received through website contact forms and bespoke temple decor requests</p>
            </div>
            <select id="crmLeadStatusFilter" class="admin-select">
              <option value="all">All Lead Stages</option>
              <option value="New">New Leads</option>
              <option value="Contacted">Contacted</option>
              <option value="In Discussion">In Discussion</option>
              <option value="Quote Sent">Quote Sent</option>
              <option value="Converted">Converted</option>
              <option value="Closed">Closed</option>
            </select>
          </div>

          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:18px;">
            <?php foreach ($crm_leads as $lead): ?>
              <div class="crm-lead-card" id="crm-lead-<?php echo $lead['id']; ?>" data-status="<?php echo htmlspecialchars($lead['lead_status']); ?>"
                   style="background:#fff; border:1px solid var(--admin-border); border-radius:12px; padding:18px; box-shadow:var(--shadow-subtle); display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                  <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                      <span class="badge <?php echo $lead['priority'] === 'Urgent' || $lead['priority'] === 'High' ? 'badge-danger' : 'badge-info'; ?>"><?php echo htmlspecialchars($lead['priority']); ?> Priority</span>
                      <h4 style="margin:6px 0 2px 0; font-size:1.05rem; color:#1c1815;"><?php echo htmlspecialchars($lead['name']); ?></h4>
                      <small style="color:#777;"><span class="material-symbols-outlined" style="font-size:14px; vertical-align:middle;">phone</span> <?php echo htmlspecialchars($lead['phone'] ?? 'No phone'); ?> • <?php echo htmlspecialchars($lead['email']); ?></small>
                    </div>
                  </div>

                  <div style="background:#faf8f5; border-left:3px solid var(--admin-gold); padding:10px 12px; margin:12px 0; border-radius:4px;">
                    <strong style="font-size:0.84rem; color:#1c1815;"><?php echo htmlspecialchars($lead['subject']); ?></strong>
                    <p style="margin:4px 0 0 0; font-size:0.82rem; color:#555;"><?php echo nl2br(htmlspecialchars($lead['message'])); ?></p>
                  </div>
                </div>

                <div style="border-top:1px solid #f0e9df; padding-top:12px; margin-top:10px;">
                  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <label style="font-size:0.78rem; font-weight:700; color:#555;">Stage:</label>
                    <select class="admin-select crm-status-select" data-id="<?php echo $lead['id']; ?>" style="padding:4px 8px; font-size:0.8rem;">
                      <option value="New" <?php if ($lead['lead_status'] === 'New') echo 'selected'; ?>>New Lead</option>
                      <option value="Contacted" <?php if ($lead['lead_status'] === 'Contacted') echo 'selected'; ?>>Contacted</option>
                      <option value="In Discussion" <?php if ($lead['lead_status'] === 'In Discussion') echo 'selected'; ?>>In Discussion</option>
                      <option value="Quote Sent" <?php if ($lead['lead_status'] === 'Quote Sent') echo 'selected'; ?>>Quote Sent</option>
                      <option value="Converted" <?php if ($lead['lead_status'] === 'Converted') echo 'selected'; ?>>Converted</option>
                      <option value="Closed" <?php if ($lead['lead_status'] === 'Closed') echo 'selected'; ?>>Closed</option>
                    </select>
                  </div>

                  <div style="display:flex; gap:6px;">
                    <a href="https://wa.me/91<?php echo preg_replace('/\D/', '', $lead['phone'] ?? ''); ?>?text=<?php echo urlencode('Namaste ' . $lead['name'] . ', Murli Prasad here from Murli Creations studio regarding: ' . $lead['subject']); ?>" target="_blank" class="btn btn-sm btn-success" style="flex:1;">WhatsApp</a>
                    <button type="button" class="btn btn-sm btn-secondary btn-log-interaction" data-id="<?php echo $lead['id']; ?>" data-name="<?php echo htmlspecialchars($lead['name']); ?>" style="flex:1;">Log Call</button>
                    <button type="button" class="btn btn-sm btn-danger btn-delete-lead" data-id="<?php echo $lead['id']; ?>"><span class="material-symbols-outlined" style="font-size:16px;">delete</span></button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- 3. Verified Patrons & User Details Directory -->
        <div class="admin-box">
          <div class="admin-box-header">
            <div>
              <h3 class="admin-box-title">Verified Patrons & Customer Accounts</h3>
              <p class="admin-box-subtitle">Lifetime spend metrics, order history, and direct communication actions</p>
            </div>
          </div>

          <div class="admin-table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Patron Name</th>
                  <th>Phone Number</th>
                  <th>Email</th>
                  <th>Location</th>
                  <th>Lifetime Orders</th>
                  <th>Total Spent</th>
                  <th>Tier</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($customers as $c): ?>
                  <tr class="customer-table-row">
                    <td><strong><?php echo htmlspecialchars($c['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($c['phone']); ?></td>
                    <td><?php echo htmlspecialchars($c['email']); ?></td>
                    <td><?php echo htmlspecialchars($c['city'] ?? 'India'); ?></td>
                    <td><strong><?php echo (int)($c['total_orders'] ?? 1); ?></strong></td>
                    <td style="font-weight:700;">₹<?php echo number_format($c['total_spent'] ?? 0); ?></td>
                    <td><span class="badge badge-gold"><?php echo htmlspecialchars($c['tier'] ?? 'Patron'); ?></span></td>
                    <td style="text-align:right;">
                      <div style="display:inline-flex; gap:6px;">
                        <a href="https://wa.me/91<?php echo preg_replace('/\D/', '', $c['phone'] ?? ''); ?>" target="_blank" class="btn btn-sm btn-success">WhatsApp</a>
                        <button type="button" class="btn btn-sm btn-secondary btn-view-customer" data-id="<?php echo htmlspecialchars($c['id']); ?>">View History</button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- =====================================================================
           TAB 4: META TAGS MANAGEMENT PER PAGE WISE
           ===================================================================== -->
      <div class="admin-tab-pane" id="tab-seo">
        <div class="admin-box">
          <div class="admin-box-header">
            <div>
              <h2 class="admin-box-title">Meta Tags Management Per Page Wise</h2>
              <p class="admin-box-subtitle">Customize Google search snippet meta tags, Open Graph cards, canonical URLs, and schema markup per page</p>
            </div>
            <select id="seoPageSelector" class="admin-select" style="min-width:260px; font-weight:700;">
              <?php foreach ($seo_pages as $sp): ?>
                <option value="<?php echo htmlspecialchars($sp['page_key']); ?>"><?php echo htmlspecialchars($sp['page_name']); ?> (<?php echo htmlspecialchars($sp['url_path']); ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <?php $firstPage = $seo_pages[0] ?? []; ?>
          <form id="seoPageForm">
            <input type="hidden" id="seoPageKey" name="page_key" value="<?php echo htmlspecialchars($firstPage['page_key'] ?? 'home'); ?>">
            <input type="hidden" id="seoPageName" name="page_name" value="<?php echo htmlspecialchars($firstPage['page_name'] ?? 'Home'); ?>">

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
              <!-- Form inputs -->
              <div>
                <div class="form-group">
                  <label class="form-label" for="seoUrlPath">Target Page Path</label>
                  <input type="text" id="seoUrlPath" name="url_path" class="form-control" value="<?php echo htmlspecialchars($firstPage['url_path'] ?? '/'); ?>" required>
                </div>

                <div class="form-group">
                  <label class="form-label" for="seoMetaTitle">Meta Title (SERP Page Title)</label>
                  <input type="text" id="seoMetaTitle" name="meta_title" class="form-control" value="<?php echo htmlspecialchars($firstPage['meta_title'] ?? ''); ?>" required>
                  <small style="color:#777; font-size:0.75rem;">Recommended length: 50–60 characters</small>
                </div>

                <div class="form-group">
                  <label class="form-label" for="seoMetaDesc">Meta Description (SERP Snippet)</label>
                  <textarea id="seoMetaDesc" name="meta_description" class="form-control" required><?php echo htmlspecialchars($firstPage['meta_description'] ?? ''); ?></textarea>
                  <small style="color:#777; font-size:0.75rem;">Recommended length: 150–160 characters</small>
                </div>

                <div class="form-group">
                  <label class="form-label" for="seoKeywords">Meta Keywords</label>
                  <input type="text" id="seoKeywords" name="meta_keywords" class="form-control" value="<?php echo htmlspecialchars($firstPage['meta_keywords'] ?? ''); ?>">
                </div>

                <div class="form-group">
                  <label class="form-label" for="seoCanonicalUrl">Canonical Link URL</label>
                  <input type="url" id="seoCanonicalUrl" name="canonical_url" class="form-control" value="<?php echo htmlspecialchars($firstPage['canonical_url'] ?? 'https://murlicreations.com/'); ?>">
                </div>

                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label" for="seoRobots">Robots Directive</label>
                    <select id="seoRobots" name="robots" class="form-control">
                      <option value="index, follow">index, follow (Standard)</option>
                      <option value="noindex, follow">noindex, follow</option>
                      <option value="noindex, nofollow">noindex, nofollow</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="seoTwitterCard">Twitter Card Type</label>
                    <select id="seoTwitterCard" name="twitter_card" class="form-control">
                      <option value="summary_large_image">summary_large_image</option>
                      <option value="summary">summary</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="form-label" for="seoOgImage">Open Graph Image URL (WhatsApp/Social Share)</label>
                  <input type="text" id="seoOgImage" name="og_image" class="form-control" value="<?php echo htmlspecialchars($firstPage['og_image'] ?? '/img-prodcut/IMG_4041.png'); ?>">
                </div>

                <div class="form-group">
                  <label class="form-label" for="seoSchemaJson">Structured Data (Schema JSON-LD)</label>
                  <textarea id="seoSchemaJson" name="schema_json" class="form-control" style="font-family:monospace; font-size:0.78rem;"><?php echo htmlspecialchars($firstPage['schema_json'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; padding:12px;">Save Meta Tags &rarr;</button>
              </div>

              <!-- Live Previews -->
              <div>
                <h4 style="margin:0 0 8px 0; font-size:0.88rem; text-transform:uppercase; color:#888;">Live Search Engine Preview (Google SERP)</h4>
                <div class="serp-preview-box">
                  <div class="serp-url" id="previewSerpUrl"><?php echo htmlspecialchars($firstPage['canonical_url'] ?? 'https://murlicreations.com/'); ?></div>
                  <div class="serp-title" id="previewSerpTitle"><?php echo htmlspecialchars($firstPage['meta_title'] ?? ''); ?></div>
                  <div class="serp-desc" id="previewSerpDesc"><?php echo htmlspecialchars($firstPage['meta_description'] ?? ''); ?></div>
                </div>

                <h4 style="margin:24px 0 8px 0; font-size:0.88rem; text-transform:uppercase; color:#888;">Live Social Card Preview (WhatsApp / Facebook)</h4>
                <div class="social-card-preview">
                  <img src="<?php echo htmlspecialchars($firstPage['og_image'] ?? '/img-prodcut/IMG_4041.png'); ?>" id="previewSocialImg" class="social-card-img" alt="">
                  <div class="social-card-body">
                    <div class="social-card-domain">MURLICREATIONS.COM</div>
                    <div class="social-card-title" id="previewSocialTitle"><?php echo htmlspecialchars($firstPage['meta_title'] ?? ''); ?></div>
                    <div class="social-card-desc" id="previewSocialDesc"><?php echo htmlspecialchars($firstPage['meta_description'] ?? ''); ?></div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- =====================================================================
           TAB 5: EMAIL COMMUNICATION
           ===================================================================== -->
      <div class="admin-tab-pane" id="tab-email">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
          <!-- Direct Studio Email Composer -->
          <div class="admin-box">
            <h3 class="admin-box-title">Direct Studio Email Composer</h3>
            <p class="admin-box-subtitle">Send official handcrafted communication to any patron or CRM inquiry</p>

            <form id="directEmailComposerForm" style="margin-top:16px;">
              <div class="form-group">
                <label class="form-label" for="emailComposerTo">Recipient Email</label>
                <input type="email" id="emailComposerTo" class="form-control" placeholder="patron@example.com" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="emailComposerSubject">Subject Line</label>
                <input type="text" id="emailComposerSubject" class="form-control" placeholder="Regarding your Handcrafted Lippan Artwork Commission..." required>
              </div>
              <div class="form-group">
                <label class="form-label" for="emailComposerMessage">Email Message Body</label>
                <textarea id="emailComposerMessage" class="form-control" style="min-height:130px;" placeholder="Namaste, Master Craftsman Murli Prasad has received..." required></textarea>
              </div>
              <button type="submit" class="btn btn-gold" style="width:100%;">Dispatch Email &rarr;</button>
            </form>
          </div>

          <!-- HTML Email Templates Editor -->
          <div class="admin-box">
            <h3 class="admin-box-title">System Email Notification Templates</h3>
            <p class="admin-box-subtitle">Automated email receipts for order confirmations, shipments, and reminders</p>

            <?php foreach ($email_templates as $tpl): ?>
              <div style="border:1px solid #e7ded0; border-radius:10px; padding:16px; margin-bottom:14px; background:#fff;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                  <strong style="color:#1c1815;"><?php echo htmlspecialchars($tpl['template_name']); ?></strong>
                  <button type="button" class="btn btn-sm btn-gold btn-save-email-template" data-key="<?php echo htmlspecialchars($tpl['template_key']); ?>">Save</button>
                </div>
                <div style="margin-bottom:8px;">
                  <label style="font-size:0.75rem; color:#777;">Subject Line:</label>
                  <input type="text" id="tpl-subject-<?php echo $tpl['template_key']; ?>" class="form-control" value="<?php echo htmlspecialchars($tpl['subject']); ?>" style="padding:6px 10px; font-size:0.85rem;">
                </div>
                <div>
                  <label style="font-size:0.75rem; color:#777;">HTML Body Template:</label>
                  <textarea id="tpl-body-<?php echo $tpl['template_key']; ?>" class="form-control" style="font-family:monospace; font-size:0.75rem; min-height:90px;"><?php echo htmlspecialchars($tpl['body_html']); ?></textarea>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- =====================================================================
           TAB 6: OFFER MANAGEMENT
           ===================================================================== -->
      <div class="admin-tab-pane" id="tab-offers">
        <!-- Top Announcement Bar Banner CTA -->
        <div class="admin-box">
          <div class="admin-box-header">
            <div>
              <h3 class="admin-box-title">Top Announcement Bar Offer Banner</h3>
              <p class="admin-box-subtitle">Sticky banner displayed at the top of every store page for special festive sales</p>
            </div>
          </div>

          <?php $ann = $offers_cta['announcement_bar'] ?? []; ?>
          <form class="offer-cta-form">
            <input type="hidden" name="section_key" value="announcement_bar">
            <div class="form-grid">
              <div class="form-group full-width">
                <label class="form-label" for="ann_title">Announcement Bar Offer Text</label>
                <input type="text" id="ann_title" name="title" class="form-control" value="<?php echo htmlspecialchars($ann['title'] ?? 'Festive Season Special: Free Shipping Pan-India on orders above ₹1,999!'); ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="ann_badge">Badge Tag</label>
                <input type="text" id="ann_badge" name="badge" class="form-control" value="<?php echo htmlspecialchars($ann['badge'] ?? 'FESTIVE BLESSINGS'); ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="ann_coupon">Highlight Promo Code</label>
                <input type="text" id="ann_coupon" name="coupon_code" class="form-control" value="<?php echo htmlspecialchars($ann['coupon_code'] ?? 'MURLI10'); ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="ann_cta">CTA Button Text</label>
                <input type="text" id="ann_cta" name="cta_text" class="form-control" value="<?php echo htmlspecialchars($ann['cta_text'] ?? 'Claim Offer'); ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="ann_link">CTA Target Link</label>
                <input type="text" id="ann_link" name="cta_link" class="form-control" value="<?php echo htmlspecialchars($ann['cta_link'] ?? '/shop.php'); ?>">
              </div>
              <div class="form-group full-width">
                <label style="display:flex; align-items:center; gap:8px; font-weight:bold; cursor:pointer;">
                  <input type="checkbox" name="is_active" value="1" <?php if (!empty($ann['is_active'])) echo 'checked'; ?>>
                  Display Announcement Banner Live on Website
                </label>
              </div>
            </div>
            <button type="submit" class="btn btn-gold">Save Announcement Offer &rarr;</button>
          </form>
        </div>

        <!-- Discount Coupons Manager -->
        <div class="admin-box">
          <div class="admin-box-header">
            <div>
              <h3 class="admin-box-title">Discount Coupon Codes</h3>
              <p class="admin-box-subtitle">Promo codes applicable on checkout & cart</p>
            </div>
          </div>

          <form id="couponForm" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px; background:#faf8f5; padding:16px; border-radius:10px;">
            <input type="text" name="code" placeholder="COUPON CODE (e.g. FESTIVE15)" required class="form-control" style="flex:1; min-width:160px; text-transform:uppercase;">
            <input type="number" name="discountPercent" placeholder="Discount %" required class="form-control" style="width:120px;">
            <input type="number" name="minOrder" placeholder="Min Spend ₹" class="form-control" style="width:130px;">
            <input type="text" name="description" placeholder="Description..." class="form-control" style="flex:2; min-width:200px;">
            <button type="submit" class="btn btn-gold">+ Create Coupon</button>
          </form>

          <div class="admin-table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Discount %</th>
                  <th>Min Order Spend</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($coupons as $cp): ?>
                  <tr id="coupon-row-<?php echo htmlspecialchars($cp['code']); ?>">
                    <td><strong><?php echo htmlspecialchars($cp['code']); ?></strong></td>
                    <td><?php echo (float)($cp['discountPercent'] ?? 10); ?>%</td>
                    <td>₹<?php echo number_format($cp['minOrder'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($cp['description'] ?? ''); ?></td>
                    <td><span class="badge <?php echo !empty($cp['active']) ? 'badge-success' : 'badge-danger'; ?>"><?php echo !empty($cp['active']) ? 'Active' : 'Disabled'; ?></span></td>
                    <td style="text-align:right;">
                      <button type="button" class="btn btn-sm btn-danger btn-delete-coupon" data-code="<?php echo htmlspecialchars($cp['code']); ?>">Delete</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- =====================================================================
           TAB 9: CUSTOMER REVIEWS & TESTIMONIALS MANAGEMENT HUB (NEW)
           ===================================================================== -->
      <div class="admin-tab-pane" id="tab-reviews">
        <div class="admin-box">
          <div class="admin-box-header">
            <div>
              <h2 class="admin-box-title"><span class="material-symbols-outlined" style="font-size:22px; vertical-align:middle; margin-right:8px; color:#F59E0B;">reviews</span> Customer Reviews & Testimonials Management</h2>
              <p class="admin-box-subtitle">Approve customer feedback for live website display, manage ratings, and add verified reviews from offline exhibitions</p>
            </div>
            <button type="button" class="btn btn-gold" id="btnOpenAddReviewModal">+ Add Offline / WhatsApp Review</button>
          </div>

          <!-- Top Metrics -->
          <div class="metrics-grid" style="margin-bottom:20px;">
            <div class="metric-card">
              <div class="metric-info">
                <h3>Total Patron Reviews</h3>
                <div class="metric-val"><?php echo count($admin_reviews); ?></div>
              </div>
              <div class="metric-icon-wrap"><span class="material-symbols-outlined">reviews</span></div>
            </div>
            <div class="metric-card">
              <div class="metric-info">
                <h3>Average Studio Rating</h3>
                <div class="metric-val">4.9 <span class="material-symbols-outlined" style="font-size:18px; color:#f59e0b; vertical-align:middle;">star</span></div>
              </div>
              <div class="metric-icon-wrap"><span class="material-symbols-outlined">hotel_class</span></div>
            </div>
            <div class="metric-card">
              <div class="metric-info">
                <h3>Approved (Live on Site)</h3>
                <div class="metric-val" style="color:#15803d;"><?php echo count($admin_reviews) - $pendingReviewsCount; ?></div>
              </div>
              <div class="metric-icon-wrap"><span class="material-symbols-outlined">check_circle</span></div>
            </div>
            <div class="metric-card">
              <div class="metric-info">
                <h3>Pending Verification</h3>
                <div class="metric-val" style="color:<?php echo $pendingReviewsCount > 0 ? '#b91c1c' : 'var(--admin-text-main)'; ?>;"><?php echo $pendingReviewsCount; ?></div>
              </div>
              <div class="metric-icon-wrap"><span class="material-symbols-outlined">pending</span></div>
            </div>
          </div>

          <!-- Reviews Filter & Search Toolbar -->
          <div class="reviews-filter-bar">
            <div style="flex:1; min-width:240px;">
              <input type="text" id="reviewSearchInput" class="admin-search-input" placeholder="Search reviews by patron name, city, or comment...">
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
              <label for="reviewStatusFilter" style="font-size:0.82rem; font-weight:700; color:var(--admin-text-sub);">Status:</label>
              <select id="reviewStatusFilter" class="admin-select" style="padding:6px 14px; font-size:0.82rem;">
                <option value="all">All Reviews (<?php echo count($admin_reviews); ?>)</option>
                <option value="approved">Live on Store (<?php echo count($admin_reviews) - $pendingReviewsCount; ?>)</option>
                <option value="pending">Hidden / Pending (<?php echo $pendingReviewsCount; ?>)</option>
              </select>
            </div>
          </div>

          <!-- Reviews Table -->
          <div class="admin-table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Patron</th>
                  <th>Artwork Collection</th>
                  <th>Rating</th>
                  <th>Review Message</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody id="adminReviewsTableBody">
                <?php foreach ($admin_reviews as $rev): ?>
                  <?php 
                    $rId = $rev['id'];
                    $rName = $rev['name'] ?? $rev['userName'] ?? 'Patron';
                    $rRating = (float)($rev['rating'] ?? 5.0);
                    $rComment = $rev['comment'] ?? '';
                    $rDate = $rev['date_text'] ?? $rev['date'] ?? 'Recent';
                    $rApproved = !isset($rev['is_approved']) || (int)$rev['is_approved'] === 1;
                    $rProdName = $rev['product_name'] ?? 'Handcrafted Lippan Art';
                  ?>
                  <tr id="review-row-<?php echo htmlspecialchars($rId); ?>" class="admin-review-row" 
                      data-name="<?php echo htmlspecialchars(strtolower($rName . ' ' . ($rev['location'] ?? ''))); ?>" 
                      data-comment="<?php echo htmlspecialchars(strtolower($rComment)); ?>" 
                      data-status="<?php echo $rApproved ? 'approved' : 'pending'; ?>">
                    <td>
                      <strong><?php echo htmlspecialchars($rName); ?></strong><br>
                      <small style="color:#777;"><span class="material-symbols-outlined" style="font-size:14px; vertical-align:middle; color:#1EA659;">location_on</span> <?php echo htmlspecialchars($rev['location'] ?? 'India'); ?></small>
                    </td>
                    <td>
                      <small style="font-weight:700; color:#15803d;"><?php echo htmlspecialchars($rProdName); ?></small>
                    </td>
                    <td>
                      <span style="color:#f59e0b; font-weight:800; font-size:0.92rem;">
                        <?php for($i=1; $i<=5; $i++): echo $i <= $rRating ? '<span class="material-symbols-outlined" style="font-size:16px; color:#F59E0B; vertical-align:middle;">star</span>' : '<span class="material-symbols-outlined" style="font-size:16px; color:#CBD5E1; vertical-align:middle;">star</span>'; endfor; ?>
                      </span>
                      <small style="font-weight:700; color:#444;">(<?php echo number_format($rRating, 1); ?>)</small>
                    </td>
                    <td style="max-width:320px; font-size:0.85rem; color:var(--admin-text-sub); line-height:1.4;">
                      "<?php echo htmlspecialchars($rComment); ?>"
                    </td>
                    <td><small style="color:#777; font-weight:600;"><?php echo htmlspecialchars($rDate); ?></small></td>
                    <td>
                      <button type="button" class="btn-toggle-review-status" data-id="<?php echo htmlspecialchars($rId); ?>" style="border:none; background:transparent; cursor:pointer;">
                        <span class="stock-pill <?php echo $rApproved ? 'in-stock' : 'out-of-stock'; ?>" id="rev-status-pill-<?php echo htmlspecialchars($rId); ?>">
                          <?php echo $rApproved ? '● Live on Store' : '● Hidden / Pending'; ?>
                        </span>
                      </button>
                    </td>
                    <td style="text-align:right;">
                      <button type="button" class="btn btn-sm btn-danger btn-delete-review" data-id="<?php echo htmlspecialchars($rId); ?>" title="Permanently delete review"><span class=\"material-symbols-outlined\" style=\"font-size:16px;\">delete</span></button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- =====================================================================
           TAB 7: LOGINS MANAGEMENT (Team, Security, Login/Logout, Customer Logins)
           ===================================================================== -->
      <div class="admin-tab-pane" id="tab-logins">
        <!-- Subnav Navigation Pills -->
        <div class="cms-subnav" id="loginsSubnav">
          <button type="button" class="cms-subnav-btn active logins-subnav-btn" data-loginsub="team"><span class="material-symbols-outlined">group</span> Studio Team & Permissions</button>
          <button type="button" class="cms-subnav-btn logins-subnav-btn" data-loginsub="audit"><span class="material-symbols-outlined">security</span> Live Login & Security Trail</button>
          <button type="button" class="cms-subnav-btn logins-subnav-btn" data-loginsub="customers"><span class="material-symbols-outlined">person_pin</span> Customer Accounts & Activity</button>
          <button type="button" class="cms-subnav-btn logins-subnav-btn" data-loginsub="env"><span class="material-symbols-outlined">database</span> Database & Security Environment</button>
        </div>

        <!-- 1. STUDIO TEAM & ROLE PERMISSIONS SUBPANEL -->
        <div class="logins-section-panel active" id="loginsub-team">
          <div class="admin-box">
            <div class="admin-box-header">
              <div>
                <h3 class="admin-box-title">Studio Team & Role Permissions</h3>
                <p class="admin-box-subtitle">Superadmin control: add team members, edit roles, directly reset passwords, or revoke access</p>
              </div>
              <button type="button" class="btn btn-gold" id="btnOpenAddTeamUserModal">+ Add Team Member</button>
            </div>

            <div class="admin-table-responsive" style="margin-top:14px;">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Member</th>
                    <th>Username & Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Active / Login</th>
                    <th style="text-align:right;">Superadmin Actions</th>
                  </tr>
                </thead>
                <tbody id="teamUsersTableBody">
                  <?php foreach ($admin_users as $au): ?>
                    <?php 
                      $isSelfOrRoot = ($au['id'] == 1 || $au['username'] === $currentUser);
                      $initials = strtoupper(substr($au['full_name'] ?? $au['username'], 0, 2));
                      $roleLabel = ($au['role'] === 'super_admin') ? 'Super Admin' : (($au['role'] === 'manager') ? 'Manager' : 'Support');
                      $roleBadgeClass = ($au['role'] === 'super_admin') ? 'badge-gold' : (($au['role'] === 'manager') ? 'badge-info' : 'badge-success');
                      $statusClass = ($au['status'] === 'active') ? 'badge-success' : 'badge-danger';
                      $userJson = htmlspecialchars(json_encode($au), ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr id="team-user-row-<?php echo $au['id']; ?>">
                      <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                          <div style="width:36px; height:36px; border-radius:50%; background:var(--gradient-primary); color:#ffffff; font-weight:800; font-size:0.82rem; display:flex; align-items:center; justify-content:center; border:1.5px solid #4ade80; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                            <?php echo $initials; ?>
                          </div>
                          <div>
                            <strong><?php echo htmlspecialchars($au['full_name']); ?></strong>
                            <?php if ($au['username'] === $currentUser): ?>
                              <span style="font-size:0.7rem; color:#15803d; font-weight:700; background:#dcfce7; padding:1px 6px; border-radius:4px; margin-left:4px;">You (Active)</span>
                            <?php endif; ?>
                          </div>
                        </div>
                      </td>
                      <td>
                        <strong><?php echo htmlspecialchars($au['username']); ?></strong><br>
                        <small style="color:#777; font-size:0.8rem;"><?php echo htmlspecialchars($au['email']); ?></small>
                      </td>
                      <td>
                        <span class="badge <?php echo $roleBadgeClass; ?>"><?php echo $roleLabel; ?></span>
                      </td>
                      <td>
                        <span class="badge <?php echo $statusClass; ?>" id="user-status-badge-<?php echo $au['id']; ?>">
                          ● <?php echo ucfirst($au['status']); ?>
                        </span>
                      </td>
                      <td>
                        <small style="color:#555; font-weight:600; font-size:0.82rem;">
                          <?php echo !empty($au['last_login']) ? htmlspecialchars($au['last_login']) : 'No logins yet'; ?>
                        </small>
                      </td>
                      <td style="text-align:right;">
                        <div style="display:inline-flex; align-items:center; gap:6px;">
                          <!-- Edit User Details -->
                          <button type="button" 
                                  class="btn btn-sm btn-secondary btn-edit-team-user" 
                                  data-user="<?php echo $userJson; ?>"
                                  title="Edit full name, email, role, or status">
                            <span class="material-symbols-outlined" style="font-size:15px;">edit</span> Edit
                          </button>
                          
                          <!-- Direct Password Reset by Superadmin -->
                          <button type="button" 
                                  class="btn btn-sm btn-gold btn-reset-user-pwd" 
                                  data-id="<?php echo $au['id']; ?>" 
                                  data-username="<?php echo htmlspecialchars($au['username']); ?>"
                                  title="Directly reset this user's password without needing old password">
                            <span class="material-symbols-outlined" style="font-size:15px;">key</span> Password
                          </button>

                          <!-- Toggle Active / Inactive Status -->
                          <button type="button" 
                                  class="btn btn-sm btn-secondary btn-toggle-user-status" 
                                  data-id="<?php echo $au['id']; ?>" 
                                  data-username="<?php echo htmlspecialchars($au['username']); ?>"
                                  title="Toggle account between active and inactive"
                                  <?php echo $isSelfOrRoot ? 'disabled style="opacity:0.4; cursor:not-allowed;"' : ''; ?>>
                            <span class="material-symbols-outlined" style="font-size:15px;">sync</span> Status
                          </button>

                          <!-- Delete Team Member -->
                          <button type="button" 
                                  class="btn btn-sm btn-danger btn-delete-team-user" 
                                  data-id="<?php echo $au['id']; ?>" 
                                  data-username="<?php echo htmlspecialchars($au['username']); ?>"
                                  title="Delete user account"
                                  <?php echo $isSelfOrRoot ? 'disabled style="opacity:0.35; cursor:not-allowed;"' : ''; ?>><span class=\"material-symbols-outlined\" style=\"font-size:16px;\">delete</span></button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- 2. LIVE LOGIN / LOGOUT & SECURITY AUDIT TRAIL SUBPANEL -->
        <div class="logins-section-panel" id="loginsub-audit" style="display:none;">
          <div class="admin-box">
            <div class="admin-box-header">
              <div>
                <h3 class="admin-box-title">Live Admin Login & Logout Activity Trail</h3>
                <p class="admin-box-subtitle">Full security logging: tracks every login, logout, failed attempt, IP address, and timestamp</p>
              </div>
              <div style="display:flex; align-items:center; gap:10px;">
                <label for="loginAuditFilter" style="font-size:0.85rem; font-weight:700; color:#1c1815;">Filter Activity:</label>
                <select id="loginAuditFilter" class="form-control" style="width:auto; padding:6px 12px; font-size:0.85rem;">
                  <option value="all">All Security Events</option>
                  <option value="LOGIN_SUCCESS">Successful Logins</option>
                  <option value="LOGOUT">Logouts</option>
                  <option value="LOGIN_FAILED">Failed Login Attempts</option>
                  <option value="PASSWORD">Password Changes & Resets</option>
                </select>
              </div>
            </div>

            <div class="admin-table-responsive" style="margin-top:14px; max-height:480px; overflow-y:auto;">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Event Type</th>
                    <th>User / Account</th>
                    <th>Activity Details</th>
                    <th>IP Address</th>
                    <th>Date & Timestamp</th>
                  </tr>
                </thead>
                <tbody id="loginAuditTableBody">
                  <?php foreach ($login_audit_trail as $log): ?>
                    <?php 
                      $act = $log['action'] ?? '';
                      $badgeClass = 'badge-info';
                      $badgeLabel = $act;
                      if ($act === 'LOGIN_SUCCESS') {
                          $badgeClass = 'badge-success';
                          $badgeLabel = '<span class="badge-dot badge-success"></span> Logged In';
                      } elseif ($act === 'LOGOUT') {
                          $badgeClass = 'badge-secondary';
                          $badgeLabel = '<span class="badge-dot badge-muted"></span> Logged Out';
                      } elseif ($act === 'LOGIN_FAILED') {
                          $badgeClass = 'badge-danger';
                          $badgeLabel = '<span class="badge-dot badge-danger"></span> Login Failed';
                      } elseif (strpos($act, 'PASSWORD') !== false) {
                          $badgeClass = 'badge-gold';
                          $badgeLabel = '<span class="badge-dot badge-warning"></span> Password Updated';
                      }
                    ?>
                    <tr class="login-audit-row" data-action="<?php echo htmlspecialchars($act); ?>">
                      <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeLabel; ?></span></td>
                      <td><strong><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></strong></td>
                      <td><?php echo htmlspecialchars($log['details'] ?? 'Studio administration access event'); ?></td>
                      <td><code><?php echo htmlspecialchars($log['ip_address'] ?? '127.0.0.1'); ?></code></td>
                      <td><small style="color:#555; font-weight:600;"><?php echo htmlspecialchars($log['created_at']); ?></small></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- 3. CUSTOMER / PATRON ACCOUNTS & ACTIVITY SUBPANEL -->
        <div class="logins-section-panel" id="loginsub-customers" style="display:none;">
          <div class="admin-box">
            <div class="admin-box-header">
              <div>
                <h3 class="admin-box-title">Registered Customer & Patron Accounts</h3>
                <p class="admin-box-subtitle">Storefront patrons, purchase history, order volume, and account access controls</p>
              </div>
            </div>

            <div class="admin-table-responsive" style="margin-top:14px;">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Customer Name & Contact</th>
                    <th>Email Address</th>
                    <th>Location</th>
                    <th>Orders & Spend</th>
                    <th>Patron Tier</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($customers)): ?>
                    <tr>
                      <td colspan="7" style="text-align:center; padding:30px; color:#888;">No patron records registered yet.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($customers as $cust): ?>
                      <?php 
                        $cStatus = $cust['status'] ?? 'active';
                        $isBlocked = ($cStatus === 'blocked');
                      ?>
                      <tr id="cust-row-<?php echo $cust['id']; ?>">
                        <td>
                          <strong><?php echo htmlspecialchars($cust['name']); ?></strong><br>
                          <small style="color:#777;"><?php echo htmlspecialchars($cust['phone'] ?? 'N/A'); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($cust['email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(($cust['city'] ?? '') . ', ' . ($cust['state'] ?? 'India')); ?></td>
                        <td>
                          <strong><?php echo (int)($cust['total_orders'] ?? 0); ?> orders</strong><br>
                          <small style="color:#15803d; font-weight:700;">₹<?php echo number_format((float)($cust['total_spent'] ?? 0)); ?></small>
                        </td>
                        <td>
                          <span class="badge badge-gold"><?php echo htmlspecialchars($cust['tier'] ?? 'Devotee Patron'); ?></span>
                        </td>
                        <td>
                          <span class="badge <?php echo $isBlocked ? 'badge-danger' : 'badge-success'; ?>" id="cust-status-badge-<?php echo $cust['id']; ?>">
                            ● <?php echo ucfirst($cStatus); ?>
                          </span>
                        </td>
                        <td style="text-align:right;">
                          <div style="display:inline-flex; gap:6px;">
                            <button type="button" 
                                    class="btn btn-sm btn-secondary btn-toggle-customer-status" 
                                    data-id="<?php echo $cust['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($cust['name']); ?>">
                              <?php echo $isBlocked ? 'Unblock' : 'Block Access'; ?>
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-secondary btn-view-customer" 
                                    data-id="<?php echo $cust['id']; ?>">
                              View Dossier
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- 4. MY PASSWORD & HOSTINGER ENVIRONMENT SUBPANEL -->
        <div class="logins-section-panel" id="loginsub-env" style="display:none;">
          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:28px;">
            <!-- Change Current Admin Password -->
            <div class="admin-box">
              <h3 class="admin-box-title">Change My Account Password</h3>
              <p class="admin-box-subtitle">Update password for current logged-in account: <strong><?php echo htmlspecialchars($currentUser); ?></strong></p>

              <form id="changePasswordForm" style="margin-top:16px;">
                <div class="form-group">
                  <label class="form-label" for="currentPassword">Current Password *</label>
                  <input type="password" id="currentPassword" class="form-control" placeholder="Enter current password" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="newPassword">New Password *</label>
                  <input type="password" id="newPassword" class="form-control" placeholder="Minimum 6 characters" minlength="6" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="confirmNewPassword">Confirm New Password *</label>
                  <input type="password" id="confirmNewPassword" class="form-control" placeholder="Repeat new password" minlength="6" required>
                </div>
                <button type="submit" class="btn btn-gold">Update My Password &rarr;</button>
              </form>
            </div>

            <!-- MySQL Database & Hostinger Environment Card -->
            <div class="admin-box">
              <div class="admin-box-header">
                <div>
                  <h3 class="admin-box-title">MySQL Database & Hostinger Environment</h3>
                  <p class="admin-box-subtitle">Database connection, server environment, and one-click data migration sync</p>
                </div>
                <button type="button" class="btn btn-gold" id="btnRunDbMigration">Run Database Sync</button>
              </div>

              <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-top:10px;">
                <div style="background:#fff; border:1px solid var(--admin-border); padding:14px; border-radius:10px;">
                  <small style="text-transform:uppercase; color:#888; font-weight:bold; font-size:0.75rem;">MySQL Status</small>
                  <div style="font-size:1.1rem; font-weight:bold; color:<?php echo $db_connected ? '#16a34a' : '#dc2626'; ?>; margin-top:4px;">
                    <?php echo $db_connected ? '<span class="badge-dot badge-success"></span> Connected' : '<span class="badge-dot badge-danger"></span> Disconnected'; ?>
                  </div>
                </div>
                <div style="background:#fff; border:1px solid var(--admin-border); padding:14px; border-radius:10px;">
                  <small style="text-transform:uppercase; color:#888; font-weight:bold; font-size:0.75rem;">Database Name</small>
                  <div style="font-size:1rem; font-weight:bold; color:#1c1815; margin-top:4px;">
                    <code><?php echo DB_NAME; ?></code>
                  </div>
                </div>
                <div style="background:#fff; border:1px solid var(--admin-border); padding:14px; border-radius:10px;">
                  <small style="text-transform:uppercase; color:#888; font-weight:bold; font-size:0.75rem;">PHP Engine</small>
                  <div style="font-size:1rem; font-weight:bold; color:#1c1815; margin-top:4px;">
                    PHP <?php echo phpversion(); ?>
                  </div>
                </div>
                <div style="background:#fff; border:1px solid var(--admin-border); padding:14px; border-radius:10px;">
                  <small style="text-transform:uppercase; color:#888; font-weight:bold; font-size:0.75rem;">Hostinger Root</small>
                  <div style="font-size:0.85rem; font-weight:bold; color:#1c1815; margin-top:4px;">
                    <code>public_html (LAMP)</code>
                  </div>
                </div>
              </div>

              <div style="margin-top:16px; background:#f7fdf9; border:1px solid var(--admin-border); border-radius:8px; padding:12px; font-size:0.82rem; color:var(--admin-text-sub);">
                <strong style="display:inline-flex; align-items:center; gap:4px;"><span class="material-symbols-outlined" style="font-size:18px; color:#D4AF37;">lightbulb</span> Hostinger Deployment Note:</strong> Admin user accounts and customer records reside in MySQL table <code>mc_admins</code> and <code>mc_customers</code>, backed up to JSON fallback storage for 100% zero-downtime reliability.
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>

  <!-- =========================================================================
       MODALS: Product Form, Product Specs Inspector, Invoice, Tracking, CRM
       ========================================================================= -->

  <!-- Add / Edit Product Modal -->
  <div class="admin-modal-overlay" id="productFormModal">
    <div class="admin-modal modal-lg">
      <div class="modal-header">
        <h3 class="modal-title" id="productModalTitle">Add New Handcrafted Artwork</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <form id="productForm">
        <div class="modal-body">
          <input type="hidden" id="prodFormId" name="id" value="">
          <div class="form-grid">
            <div class="form-group full-width">
              <label class="form-label" for="prodFormName">Artwork Title *</label>
              <input type="text" id="prodFormName" name="name" class="form-control" placeholder="e.g. Royal Peacock Jharokha Wall Art (12x18”)" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="prodFormCode">SKU Code</label>
              <input type="text" id="prodFormCode" name="code" class="form-control" placeholder="MC/L-001">
            </div>

            <div class="form-group">
              <label class="form-label" for="prodFormCategory">Collection Category</label>
              <select id="prodFormCategory" name="category" class="form-control">
                <?php foreach ($categories as $catKey => $catData): ?>
                  <option value="<?php echo htmlspecialchars($catKey); ?>"><?php echo htmlspecialchars($catKey); ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="prodFormSubcategory">Subcategory</label>
              <input type="text" id="prodFormSubcategory" name="subcategory" class="form-control" placeholder="e.g. Wall Decor, Mini Art, Sacred Art">
            </div>

            <div class="form-group">
              <label class="form-label" for="prodFormPrice">Patron Price (₹) *</label>
              <input type="number" id="prodFormPrice" name="price" class="form-control" placeholder="4100" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="prodFormMrp">MRP (₹)</label>
              <input type="number" id="prodFormMrp" name="mrp" class="form-control" placeholder="6500">
            </div>

            <div class="form-group">
              <label class="form-label" for="prodFormStockStatus">Stock Availability *</label>
              <select id="prodFormStockStatus" name="stock_status" class="form-control">
                <option value="in_stock">● In Stock (Available for Purchase)</option>
                <option value="out_of_stock">● Out of Stock (Sold Out)</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="prodFormStock">Studio Inventory Units *</label>
              <input type="number" id="prodFormStock" name="stock" class="form-control" placeholder="6" min="0" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="prodFormSize">Size / Dimensions</label>
              <input type="text" id="prodFormSize" name="size" class="form-control" placeholder="12 x 18 inches">
            </div>

            <div class="form-group full-width">
              <label class="form-label" for="prodFormMaterial">Material & Finish</label>
              <input type="text" id="prodFormMaterial" name="material" class="form-control" placeholder="Solid Wood Arch, Clay Relief Paste, Mirror Glass Inlays">
            </div>

            <div class="form-group">
              <label class="form-label" for="prodFormOrigin">Origin</label>
              <input type="text" id="prodFormOrigin" name="origin" class="form-control" value="Delhi, India">
            </div>

            <div class="form-group">
              <label class="form-label" for="prodFormArtist">Master Artisan</label>
              <input type="text" id="prodFormArtist" name="artist" class="form-control" value="Master Craftsman Murli Prasad">
            </div>

            <div class="form-group full-width">
              <label class="form-label" for="prodFormImages">Image URLs (comma-separated)</label>
              <input type="text" id="prodFormImages" name="images" class="form-control" placeholder="/img-prodcut/IMG_4041.png, /img-prodcut/IMG_4041 2.png">
            </div>

            <div class="form-group full-width">
              <label class="form-label" for="prodFormVideo">Showcase Video URL / Embed</label>
              <input type="text" id="prodFormVideo" name="video" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
            </div>

            <div class="form-group full-width">
              <label class="form-label" for="prodFormTags">Search Tags (comma-separated)</label>
              <input type="text" id="prodFormTags" name="tags" class="form-control" placeholder="bestseller, peacock, jharokha, festive">
            </div>

            <div class="form-group full-width">
              <label class="form-label" for="prodFormFolklore">Artisan Heritage Folklore</label>
              <textarea id="prodFormFolklore" name="folklore" class="form-control" placeholder="Traditional significance behind this sacred artwork..."></textarea>
            </div>

            <div class="form-group full-width">
              <label class="form-label" for="prodFormDesc">Artwork Description</label>
              <textarea id="prodFormDesc" name="description" class="form-control" placeholder="Detailed handcrafted description..."></textarea>
            </div>

            <div class="form-group full-width" style="display:flex; gap:24px;">
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="checkbox" id="prodFormFeatured" name="featured" value="1">
                Featured Artwork
              </label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="checkbox" id="prodFormBestseller" name="bestseller" value="1">
                Studio Bestseller
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-modal-cancel">Cancel</button>
          <button type="submit" class="btn btn-gold">Save Artwork</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Product Specifications Inspector Modal -->
  <div class="admin-modal-overlay" id="productDetailModal">
    <div class="admin-modal modal-lg">
      <div class="modal-header">
        <h3 class="modal-title">Artwork Specifications Dossier</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <div class="modal-body" id="productDetailContent"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-modal-cancel">Close</button>
      </div>
    </div>
  </div>

  <!-- Order Details & Printable Invoice Modal -->
  <div class="admin-modal-overlay" id="orderDetailModal">
    <div class="admin-modal modal-lg">
      <div class="modal-header no-print">
        <h3 class="modal-title">Studio Tax Invoice & Delivery Manifest</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <div class="modal-body" id="orderDetailContent"></div>
      <div class="modal-footer no-print">
        <button type="button" class="btn btn-gold" id="btnPrintInvoice"><span class="material-symbols-outlined" style="font-size:18px;">print</span> Print Packing Slip / Invoice</button>
        <button type="button" class="btn btn-secondary btn-modal-cancel">Close</button>
      </div>
    </div>
  </div>

  <!-- Change Order Status Modal -->
  <div class="admin-modal-overlay" id="changeStatusModal">
    <div class="admin-modal" style="max-width:460px;">
      <div class="modal-header">
        <h3 class="modal-title">Update Order Status</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <form id="changeStatusForm">
        <div class="modal-body">
          <input type="hidden" id="statusModalOrderId" name="orderId">
          <div class="form-group">
            <label class="form-label" for="statusModalSelect">New Order Status</label>
            <select id="statusModalSelect" name="status" class="form-control">
              <option value="Pending">Pending Approval</option>
              <option value="Confirmed">Confirmed / Approved</option>
              <option value="Processing">Processing (In Handcrafting)</option>
              <option value="Shipped">Shipped (Dispatched)</option>
              <option value="Out for Delivery">Out for Delivery</option>
              <option value="Delivered">Delivered</option>
              <option value="Cancelled">Cancelled (Restore Stock)</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="statusModalComment">Status Change Note</label>
            <input type="text" id="statusModalComment" name="comment" class="form-control" placeholder="e.g. Handcrafted and verified by studio master">
          </div>
          <div class="form-group">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
              <input type="checkbox" id="statusModalNotify" name="notifyCustomer" value="1" checked>
              Send status notification email to patron
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-modal-cancel">Cancel</button>
          <button type="submit" class="btn btn-gold">Update Status</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Update Tracking Modal -->
  <div class="admin-modal-overlay" id="trackingModal">
    <div class="admin-modal" style="max-width:500px;">
      <div class="modal-header">
        <h3 class="modal-title">Update Logistics Tracking</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <form id="orderTrackingForm">
        <div class="modal-body">
          <input type="hidden" id="trackingModalOrderId" name="orderId">
          <div class="form-group">
            <label class="form-label" for="trackingModalCarrier">Courier Partner</label>
            <select id="trackingModalCarrier" name="carrier" class="form-control">
              <option value="Shiprocket Express">Shiprocket Express</option>
              <option value="Delhivery">Delhivery</option>
              <option value="Blue Dart">Blue Dart</option>
              <option value="DTDC">DTDC</option>
              <option value="India Post (Speed Post)">India Post (Speed Post)</option>
              <option value="Shadowfax">Shadowfax</option>
              <option value="Studio Van Delivery">Studio Van Delivery</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="trackingModalAwb">AWB Consignment Number *</label>
            <input type="text" id="trackingModalAwb" name="trackingNumber" class="form-control" placeholder="e.g. 143289087612" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="trackingModalUrl">Custom Tracking URL (optional)</label>
            <input type="url" id="trackingModalUrl" name="trackingUrl" class="form-control" placeholder="https://...">
          </div>
          <div class="form-group">
            <label class="form-label" for="trackingModalEst">Estimated Delivery Date</label>
            <input type="date" id="trackingModalEst" name="estimatedDelivery" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-modal-cancel">Cancel</button>
          <button type="submit" class="btn btn-gold">Save & Mark Shipped</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Customer Dossier Modal -->
  <div class="admin-modal-overlay" id="customerDetailModal">
    <div class="admin-modal modal-lg">
      <div class="modal-header">
        <h3 class="modal-title">Patron Profile & Lifetime Order History</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <div class="modal-body" id="customerDetailContent"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-modal-cancel">Close</button>
      </div>
    </div>
  </div>

  <!-- CRM Interaction Modal -->
  <div class="admin-modal-overlay" id="crmInteractionModal">
    <div class="admin-modal" style="max-width:500px;">
      <div class="modal-header">
        <h3 class="modal-title">Log Customer Interaction</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <form id="crmInteractionForm">
        <div class="modal-body">
          <input type="hidden" id="crmLogLeadId" name="lead_id">
          <p style="margin:0 0 14px 0; font-size:0.85rem; color:#666;">Inquiry from: <strong id="crmLogLeadName">Customer</strong></p>
          <div class="form-group">
            <label class="form-label" for="crmChannelSelect">Channel</label>
            <select id="crmChannelSelect" name="channel" class="form-control">
              <option value="Phone Call">Phone Call</option>
              <option value="WhatsApp">WhatsApp Message</option>
              <option value="Email">Email</option>
              <option value="Studio Visit">Delhi Studio Visit</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="crmInteractionSummary">Summary *</label>
            <textarea id="crmInteractionSummary" name="summary" class="form-control" placeholder="Discussed dimensions and mirror patterns..." required></textarea>
          </div>
          <div class="form-group">
            <label class="form-label" for="crmNextStep">Next Action</label>
            <input type="text" id="crmNextStep" name="next_step" class="form-control" placeholder="e.g. Send prototype photo on WhatsApp">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-modal-cancel">Cancel</button>
          <button type="submit" class="btn btn-gold">Save Interaction</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Inquiry Email Reply Modal -->
  <div class="admin-modal-overlay" id="inquiryReplyModal">
    <div class="admin-modal" style="max-width: 620px;">
      <div class="modal-header">
        <h3 class="modal-title" id="inquiryReplyModalTitle">Reply to Customer Inquiry</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <form id="inquiryReplyForm">
        <input type="hidden" id="inquiryReplyId" name="id">
        <input type="hidden" name="reply_channel" value="email">
        <div class="modal-body">
          <div style="background:#fcfaf7; border:1px solid #e7ded0; border-radius:8px; padding:12px 16px; margin-bottom:16px;">
            <div style="font-size:0.82rem; color:#888;">Inquiry From:</div>
            <strong id="inquiryReplyCustomerName" style="font-size:1.05rem; color:#1c1815;">Patron</strong>
            <div style="font-size:0.82rem; color:#555; margin-top:2px;">Subject: <span id="inquiryReplySubjectTag" class="badge badge-gold">Inquiry</span></div>
            <div style="margin-top:8px; padding-top:8px; border-top:1px dashed #ded6ca; font-size:0.85rem; color:#444;" id="inquiryReplyCustomerMessage">
              "Customer message will be loaded here..."
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="inquiryReplyTo">Recipient Email</label>
            <input type="email" id="inquiryReplyTo" class="form-control" readonly style="background:#f5f0ea;">
          </div>

          <div class="form-group">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; flex-wrap:wrap; gap:6px;">
              <label class="form-label" for="inquiryReplyMessage" style="margin-bottom:0;">Official Studio Reply *</label>
              <div style="display:flex; gap:6px;">
                <button type="button" class="btn btn-sm btn-secondary btn-quick-tpl" data-tpl="quote">Quote Template</button>
                <button type="button" class="btn btn-sm btn-secondary btn-quick-tpl" data-tpl="tracking">Order Query Template</button>
              </div>
            </div>
            <textarea id="inquiryReplyMessage" name="reply_message" class="form-control" style="min-height:140px;" placeholder="Namaste, Master Craftsman Murli Prasad has reviewed your dimensions..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-modal-cancel">Cancel</button>
          <button type="submit" class="btn btn-gold">Dispatch Official Email Reply &rarr;</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Team User Add / Edit Modal -->
  <div class="admin-modal-overlay" id="teamUserModal">
    <div class="admin-modal" style="max-width: 580px;">
      <div class="modal-header">
        <h3 class="modal-title" id="teamUserModalTitle">Add New Team Member</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <form id="teamUserForm">
        <input type="hidden" id="teamUserId" name="id" value="">
        <div class="modal-body">
          <div class="form-grid">
            <div class="form-group full-width">
              <label class="form-label" for="teamUserFullName">Full Name *</label>
              <input type="text" id="teamUserFullName" name="full_name" class="form-control" placeholder="e.g. Master Craftsman Murli Prasad" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="teamUserUsername">Username *</label>
              <input type="text" id="teamUserUsername" name="username" class="form-control" placeholder="e.g. murli_artisan" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="teamUserEmail">Email Address *</label>
              <input type="email" id="teamUserEmail" name="email" class="form-control" placeholder="artisan@murlicreations.com" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="teamUserRole">Access Role *</label>
              <select id="teamUserRole" name="role" class="form-control">
                <option value="manager">Manager (Catalog, Orders, Tracking)</option>
                <option value="support">Support (Orders, Inquiries, CRM)</option>
                <option value="super_admin">Super Admin (Full Unrestricted Access)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="teamUserStatus">Account Status *</label>
              <select id="teamUserStatus" name="status" class="form-control">
                <option value="active">Active (Access Allowed)</option>
                <option value="inactive">Inactive (Access Suspended)</option>
              </select>
            </div>
            <div class="form-group full-width">
              <label class="form-label" for="teamUserPassword">Password <span id="teamUserPasswordNote" style="font-size:0.75rem; font-weight:normal; color:#888;">(Required for new member, leave empty to keep unchanged when editing)</span></label>
              <input type="password" id="teamUserPassword" name="password" class="form-control" placeholder="Minimum 6 characters" minlength="6">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-modal-cancel">Cancel</button>
          <button type="submit" class="btn btn-gold" id="btnSubmitTeamUser">Save Team Member</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Superadmin Direct Password Reset Modal -->
  <div class="admin-modal-overlay" id="superadminResetPasswordModal">
    <div class="admin-modal" style="max-width: 500px;">
      <div class="modal-header">
        <h3 class="modal-title">Reset User Password</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <form id="superadminResetPasswordForm">
        <input type="hidden" id="resetTargetUserId" name="user_id" value="">
        <div class="modal-body">
          <div id="resetTargetUserBanner" style="background:#fcfaf7; border:1px solid #e7ded0; border-radius:8px; padding:12px 16px; margin-bottom:16px;">
            <div style="font-size:0.8rem; color:#888; text-transform:uppercase; font-weight:700;">Account Selected:</div>
            <div id="resetTargetUserLabel" style="font-size:1.1rem; font-weight:800; color:#1c1815; margin-top:2px;">username</div>
            <small style="color:#665c52; font-size:0.75rem; display:block; margin-top:4px;">As Superadmin, you can directly set a new password without needing their old password.</small>
          </div>

          <div class="form-group">
            <label class="form-label" for="resetTargetNewPassword">New Password *</label>
            <input type="password" id="resetTargetNewPassword" class="form-control" placeholder="Enter new password (min. 6 chars)" minlength="6" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="resetTargetConfirmPassword">Confirm New Password *</label>
            <input type="password" id="resetTargetConfirmPassword" class="form-control" placeholder="Confirm new password" minlength="6" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-modal-cancel">Cancel</button>
          <button type="submit" class="btn btn-gold">Update Password Now</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 4x6 Thermal Shipping Parcel Label Modal -->
  <div class="admin-modal-overlay" id="shippingLabelModal">
    <div class="admin-modal" style="max-width:540px;">
      <div class="modal-header">
        <h3 class="modal-title"><span class="material-symbols-outlined" style="font-size:20px; vertical-align:middle; margin-right:6px;">print</span> Print Thermal Parcel Shipping Label</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <div class="modal-body">
        <div id="shippingLabelPrintArea" class="shipping-thermal-label">
          <!-- Branded Label Header -->
          <div class="label-section header-section" style="padding-bottom:8px; border-bottom:2px solid #000;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <div>
                <h2 style="margin:0; font-family:'Cinzel',serif; font-size:1.3rem; letter-spacing:2px; font-weight:800; color:#000;">MURLI CREATIONS</h2>
                <div style="font-size:0.68rem; text-transform:uppercase; letter-spacing:1px; font-weight:700;">Sacred Clay & Mirror Lippan Art Studio</div>
              </div>
              <div style="text-align:right;">
                <span style="border:2px solid #000; padding:2px 8px; font-weight:900; font-size:0.8rem; text-transform:uppercase;">STANDARD AIR</span>
              </div>
            </div>
          </div>

          <!-- Barcode Area -->
          <div class="label-section barcode-section" style="text-align:center; padding:12px 0; border-bottom:2px solid #000;">
            <div style="font-family:monospace; font-size:2.2rem; letter-spacing:4px; font-weight:900; line-height:1;">||| | |||| | |||| || ||| | |||</div>
            <div style="font-size:0.85rem; font-weight:800; margin-top:4px;" id="lblAwbNumber">AWB: MURLI-AWB-457702</div>
          </div>

          <!-- Ship To Details -->
          <div class="label-section to-section" style="padding:10px 0; border-bottom:2px solid #000;">
            <div style="font-size:0.68rem; font-weight:800; text-transform:uppercase; letter-spacing:1px;">DELIVER TO (PATRON):</div>
            <div style="font-size:1.05rem; font-weight:800; color:#000; margin-top:2px;" id="lblCustomerName">Radhika Sharma</div>
            <div style="font-size:0.85rem; line-height:1.3; margin-top:2px;" id="lblCustomerAddress">Flat 402, Lotus Towers, Near City Park</div>
            <div style="font-size:0.95rem; font-weight:800; margin-top:4px;" id="lblCustomerCityPin">ROHTAK, HARYANA - 124001</div>
            <div style="font-size:0.85rem; font-weight:700; margin-top:2px;" id="lblCustomerPhone"><span class="material-symbols-outlined" style="font-size:15px; vertical-align:middle;">phone</span> Phone: 9876543210</div>
          </div>

          <!-- Ship From Details -->
          <div class="label-section from-section" style="padding:8px 0; border-bottom:2px solid #000; font-size:0.75rem; line-height:1.3;">
            <div style="font-weight:800; text-transform:uppercase; font-size:0.65rem;">IF UNDELIVERED, RETURN TO:</div>
            <strong>Murli Creations Master Studio</strong><br>
            C-Block Sector 10, Noida, Gautam Buddha Nagar, Uttar Pradesh - 201301<br>
            Studio Helpline: +91 95823 16454 | GSTIN: 09AABCU9603R1ZM
          </div>

          <!-- Order Summary & Fragile Warning -->
          <div class="label-section footer-section" style="padding-top:8px; display:flex; justify-content:space-between; align-items:center;">
            <div>
              <div style="font-size:0.72rem; font-weight:700;" id="lblOrderId">Order #MURLI-457702-467</div>
              <div style="font-size:0.75rem; font-weight:800; color:#000;" id="lblOrderAmount">Total Amount: ₹3,690 (Prepaid)</div>
            </div>
            <div style="text-align:right;">
              <span style="background:#000; color:#fff; font-size:0.75rem; font-weight:900; padding:3px 8px; border-radius:3px; text-transform:uppercase;">
                <span class="material-symbols-outlined" style="font-size:14px; vertical-align:middle; color:#b45309;">warning</span> FRAGILE: GLASS/MIRROR
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-modal-cancel">Close</button>
        <button type="button" class="btn btn-gold" id="btnPrintThermalLabelNow"><span class="material-symbols-outlined" style="font-size:18px;">print</span> Print Label (4x6")</button>
      </div>
    </div>
  </div>

  <!-- Bulk CSV Import Modal -->
  <div class="admin-modal-overlay" id="bulkCsvImportModal">
    <div class="admin-modal" style="max-width:620px;">
      <div class="modal-header">
        <h3 class="modal-title"><span class="material-symbols-outlined" style="font-size:20px; vertical-align:middle; margin-right:6px;">upload</span> Bulk Import / Update Catalog via CSV</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <form id="formBulkCsvImport">
        <div class="modal-body">
          <p style="font-size:0.85rem; color:var(--admin-text-sub); margin-top:0;">
            Upload an updated CSV spreadsheet or paste CSV lines to update prices, MRPs, and stock quantities in bulk across all artworks.
          </p>
          <div class="form-group">
            <label class="form-label" for="csvFileInput">Choose CSV Spreadsheet</label>
            <input type="file" id="csvFileInput" name="csv_file" accept=".csv" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label" for="csvTextInput">Or Paste CSV Content Directly</label>
            <textarea id="csvTextInput" name="csv_text" class="form-control" rows="6" placeholder="SKU Code,Product ID,Product Name,Category,Price (INR),MRP (INR),Stock&#10;MC/L-001,prod-1,Peacock Jharokha,Lippan Art,4100,6500,10"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-modal-cancel">Cancel</button>
          <button type="submit" class="btn btn-gold">Import & Update Catalog &rarr;</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Category & Subcategory Bulk Price Discount Modal -->
  <div class="admin-modal-overlay" id="festiveDiscountModal">
    <div class="admin-modal" style="max-width:580px;">
      <div class="modal-header">
        <h3 class="modal-title">
          <span class="material-symbols-outlined" style="font-size:24px; vertical-align:middle; margin-right:8px; color:#1EA659;">percent</span> 
          Category & Subcategory Bulk Discount Manager
        </h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <form id="formFestiveDiscount">
        <div class="modal-body">
          <p style="font-size:0.86rem; color:#475569; margin-top:0; line-height:1.5;">
            Select a target category or drill down to a specific subcategory to apply festive discounts, custom price cuts, or revert back to standard catalog pricing in 1-click.
          </p>

          <!-- Category Select -->
          <div class="form-group">
            <label class="form-label" for="discountCategorySelect">
              <span class="material-symbols-outlined" style="font-size:16px;">category</span> Target Category
            </label>
            <select id="discountCategorySelect" name="category" class="form-control">
              <option value="all">Entire Studio Catalog (All 39+ Artworks)</option>
              <?php foreach ($categories as $catKey => $catData): ?>
                <?php 
                  $countInCat = count(array_filter($products, function($item) use ($catKey, $catData) {
                    $c = $item['category'] ?? '';
                    return $c === $catKey || $c === ($catData['name'] ?? '');
                  }));
                ?>
                <option value="<?php echo htmlspecialchars($catKey); ?>">
                  <?php echo htmlspecialchars($catData['name'] ?? $catKey); ?> (<?php echo $countInCat; ?> artworks)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Subcategory Select (Populated dynamically) -->
          <div class="form-group">
            <label class="form-label" for="discountSubcategorySelect">
              <span class="material-symbols-outlined" style="font-size:16px;">subdirectory_arrow_right</span> Target Subcategory
            </label>
            <select id="discountSubcategorySelect" name="subcategory" class="form-control">
              <option value="all">All Subcategories in Selected Collection</option>
            </select>
            <small style="color:#64748B; font-size:0.75rem;">Optional: Select a subcategory to only discount that specific slice of products.</small>
          </div>

          <!-- Quick Preset Chips -->
          <div class="form-group">
            <label class="form-label">
              <span class="material-symbols-outlined" style="font-size:16px;">bolt</span> Quick Discount Presets
            </label>
            <div class="discount-preset-chips" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px;">
              <button type="button" class="btn-preset-discount" data-discount="5">5% OFF</button>
              <button type="button" class="btn-preset-discount active" data-discount="10">10% OFF</button>
              <button type="button" class="btn-preset-discount" data-discount="15">15% OFF</button>
              <button type="button" class="btn-preset-discount" data-discount="20">20% OFF</button>
              <button type="button" class="btn-preset-discount" data-discount="25">25% OFF</button>
              <button type="button" class="btn-preset-discount" data-discount="30">30% OFF</button>
              <button type="button" class="btn-preset-discount" data-discount="50">50% OFF</button>
              <button type="button" class="btn-preset-discount reset" data-discount="0">Reset (0%)</button>
            </div>
          </div>

          <!-- Discount Value Input -->
          <div class="form-group">
            <label class="form-label" for="discountPercentInput">
              <span class="material-symbols-outlined" style="font-size:16px;">price_change</span> Discount Percentage (%)
            </label>
            <div style="position:relative;">
              <input type="number" id="discountPercentInput" name="discount" class="form-control" min="0" max="90" step="1" value="10" required style="padding-right:45px; font-weight:700; font-size:1.1rem; color:#0F382C;">
              <span style="position:absolute; right:15px; top:50%; transform:translateY(-50%); font-weight:800; color:#64748B;">%</span>
            </div>
            <small style="color:#64748B; font-size:0.75rem;">Prices will be recalculated against original MRP. Entering 0% resets to standard catalog prices.</small>
          </div>

          <!-- Real-Time Live Scope Impact Box -->
          <div class="discount-impact-box" id="discountImpactBox" style="background:#F0FDF4; border:1.5px solid #BBF7D0; border-radius:12px; padding:12px 16px; display:flex; align-items:center; gap:12px; margin-top:14px;">
            <div style="width:36px; height:36px; border-radius:8px; background:#DCFCE7; color:#15803D; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              <span class="material-symbols-outlined">track_changes</span>
            </div>
            <div style="font-size:0.84rem; color:#14532D; line-height:1.4;">
              <strong>Target Scope:</strong> <span id="discountScopeText">All 39 artworks across all collections</span> will receive <strong id="discountScopePercent">10% OFF</strong>.
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-modal-cancel">Cancel</button>
          <button type="submit" class="btn btn-gold" id="btnSubmitDiscount" style="padding:12px 24px;">
            <span class="material-symbols-outlined">check_circle</span> Apply Category Discount Now &rarr;
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Offline Review Modal -->
  <div class="admin-modal-overlay" id="addReviewModal">
    <div class="admin-modal" style="max-width:580px;">
      <div class="modal-header">
        <h3 class="modal-title"><span class="material-symbols-outlined" style="font-size:20px; vertical-align:middle; margin-right:6px; color:#F59E0B;">rate_review</span> Add Verified Patron Review</h3>
        <button type="button" class="modal-close-btn">&times;</button>
      </div>
      <form id="formAddReview">
        <div class="modal-body">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="revPatronName">Patron Name *</label>
              <input type="text" id="revPatronName" name="name" class="form-control" required placeholder="e.g. Ananya Deshmukh">
            </div>
            <div class="form-group">
              <label class="form-label" for="revLocation">City / Location</label>
              <input type="text" id="revLocation" name="location" class="form-control" placeholder="e.g. Pune, Maharashtra" value="India">
            </div>
            <div class="form-group">
              <label class="form-label" for="revProductId">Art Collection Piece *</label>
              <select id="revProductId" name="productId" class="form-control">
                <?php foreach ($products as $p): ?>
                  <option value="<?php echo htmlspecialchars($p['id']); ?>">
                    <?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['code'] ?? ''); ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="revRating">Rating (Stars)</label>
              <select id="revRating" name="rating" class="form-control">
                <option value="5" selected>★★★★★ (5.0 / 5.0 Exceptional)</option>
                <option value="4.5">★★★★☆ (4.5 / 5.0 Great)</option>
                <option value="4">★★★★☆ (4.0 / 5.0 Good)</option>
              </select>
            </div>
            <div class="form-group full-width">
              <label class="form-label" for="revComment">Review Testimonial Text *</label>
              <textarea id="revComment" name="comment" class="form-control" rows="3" required placeholder="Patron's genuine feedback regarding mirror shine, clay detailing, packaging, etc..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-modal-cancel">Cancel</button>
          <button type="submit" class="btn btn-gold">Publish Review &rarr;</button>
        </div>
      </form>
    </div>
  </div>

<?php endif; ?>

<script src="/assets/js/admin.js?v=<?php echo filemtime(__DIR__ . '/assets/js/admin.js'); ?>"></script>
</body>
</html>
