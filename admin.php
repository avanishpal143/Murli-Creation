<?php
require_once __DIR__ . '/config/functions.php';

$is_logged_in = !empty($_SESSION['mc_admin_logged_in']);

$products = get_all_products();
$orders = get_all_orders();
$site_settings = get_site_settings();
$homepage_content = get_homepage_content();
$coupons = get_all_coupons();
$reviews = get_all_reviews();
$available_images = get_available_product_images();

// Metrics calculations
$totalRevenue = 0;
$totalOrders = count($orders);
$totalProducts = count($products);
$lowStockCount = 0;

foreach ($orders as $o) {
    if (($o['orderStatus'] ?? $o['status'] ?? '') !== 'Cancelled') {
        $totalRevenue += (float)($o['totalAmount'] ?? $o['subtotal'] ?? 0);
    }
}

foreach ($products as $p) {
    if (($p['stock'] ?? 0) <= 3) {
        $lowStockCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Studio Administration Portal | Murli Creations</title>
  <meta name="description" content="Complete administrative management portal for Murli Creations inventory, festive sales, homepage CMS, reviews, coupons and studio settings.">

  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/product-card.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main style="padding-top: 80px;">
    <div class="admin-page">
      <div class="container">
        <?php if (!$is_logged_in): ?>
          <!-- Login View -->
          <div class="login-page">
            <div class="login-card">
              <div class="login-header">
                <a href="/index.php" style="display: flex; flex-direction: column; align-items: center; text-decoration: none;">
                  <img src="/images/brand/black_logo.png" alt="Murli Creations" width="120" style="margin-bottom: 8px;">
                  <span class="login-wordmark">MURLI</span>
                  <span class="login-tagline">Creations</span>
                </a>
              </div>
              <p class="login-subtitle">Studio Administration Portal</p>

              <form id="admin-login-form" class="login-form">
                <div class="admin-input-group">
                  <label class="admin-label" for="admin-username">Username</label>
                  <input type="text" id="admin-username" class="admin-input" placeholder="admin" required autocomplete="username" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label" for="admin-password">Password</label>
                  <input type="password" id="admin-password" class="admin-input" placeholder="••••••••" required autocomplete="current-password" />
                </div>

                <button type="submit" class="login-submit-btn">
                  Enter Portal &rarr;
                </button>
              </form>
            </div>
          </div>
        <?php else: ?>
          <!-- Authenticated Portal View -->
          <div class="portal-header">
            <div>
              <h1 class="portal-title">Studio Administration Portal</h1>
              <p style="color: var(--text-muted); font-size: 0.9rem;">
                Logged in as <strong>Administrator</strong> • Rohtak Heritage Studio
              </p>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
              <a href="/index.php" target="_blank" class="admin-view-site-btn">
                <span>View Live Site</span> &UpperRightArrow;
              </a>
              <button type="button" class="portal-logout-btn">
                Logout Portal
              </button>
            </div>
          </div>

          <!-- 7-Tab Navigation System -->
          <div class="admin-tab-nav">
            <button type="button" class="admin-tab-btn active" data-tab="dashboard">
              📊 Analytics
            </button>
            <button type="button" class="admin-tab-btn" data-tab="products">
              🎨 Products (<?php echo $totalProducts; ?>)
            </button>
            <button type="button" class="admin-tab-btn" data-tab="orders">
              📦 Orders (<?php echo $totalOrders; ?>)
            </button>
            <button type="button" class="admin-tab-btn" data-tab="festive">
              🏷️ Festive & Coupons (<?php echo count($coupons); ?>)
            </button>
            <button type="button" class="admin-tab-btn" data-tab="homepage">
              🌐 Homepage CMS
            </button>
            <button type="button" class="admin-tab-btn" data-tab="reviews">
              ⭐ Reviews (<?php echo count($reviews); ?>)
            </button>
            <button type="button" class="admin-tab-btn" data-tab="settings">
              ⚙️ Studio Settings
            </button>
          </div>

          <!-- =======================================================
               TAB 1: ANALYTICS OVERVIEW
               ======================================================= -->
          <div id="tab-dashboard" class="admin-tab-content">
            <div class="dashboard-grid">
              <div class="metric-card">
                <span class="metric-label">Total Realized Revenue</span>
                <div class="metric-value">₹<?php echo format_inr($totalRevenue); ?></div>
                <span class="metric-sub">From online & UPI orders</span>
              </div>

              <div class="metric-card">
                <span class="metric-label">Total Orders Booked</span>
                <div class="metric-value"><?php echo $totalOrders; ?></div>
                <span class="metric-sub"><?php echo count(array_filter($orders, fn($o) => ($o['orderStatus']??'') === 'Delivered')); ?> Delivered successfully</span>
              </div>

              <div class="metric-card">
                <span class="metric-label">Active Art Pieces</span>
                <div class="metric-value"><?php echo $totalProducts; ?></div>
                <span class="metric-sub">Authentic catalog items</span>
              </div>

              <div class="metric-card">
                <span class="metric-label">Low Stock Alerts (&le; 3)</span>
                <div class="metric-value <?php echo ($lowStockCount > 0) ? 'low-stock-warning' : ''; ?>">
                  <?php echo $lowStockCount; ?>
                </div>
                <span class="metric-sub"><?php echo ($lowStockCount > 0) ? 'Action needed soon' : 'Healthy inventory'; ?></span>
              </div>
            </div>

            <!-- Recent Orders Quick Preview -->
            <div class="admin-card-section" style="margin-top: var(--spacing-xl);">
              <div class="admin-section-header">
                <h3 class="admin-section-title">Recent Studio Orders</h3>
                <button type="button" class="admin-text-btn" onclick="switchAdminTab('orders')">View All Orders &rarr;</button>
              </div>
              
              <?php if (empty($orders)): ?>
                <p style="color: var(--text-muted); font-size: 0.9rem; padding: 20px; text-align: center;">No orders recorded yet.</p>
              <?php else: ?>
                <div class="admin-table-container">
                  <table class="admin-table">
                    <thead>
                      <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach (array_slice($orders, 0, 5) as $ord): ?>
                        <tr>
                          <td><strong><?php echo htmlspecialchars($ord['id']); ?></strong></td>
                          <td><?php echo htmlspecialchars($ord['customer']['name'] ?? 'Guest'); ?></td>
                          <td>₹<?php echo format_inr($ord['totalAmount'] ?? $ord['subtotal'] ?? 0); ?></td>
                          <td><span class="admin-badge-neutral"><?php echo htmlspecialchars($ord['paymentMethod'] ?? 'UPI'); ?></span></td>
                          <td><span class="status-badge status-<?php echo strtolower($ord['orderStatus'] ?? $ord['status'] ?? 'pending'); ?>"><?php echo htmlspecialchars($ord['orderStatus'] ?? $ord['status'] ?? 'Pending'); ?></span></td>
                          <td><?php echo htmlspecialchars($ord['createdAt'] ?? $ord['date'] ?? '-'); ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- =======================================================
               TAB 2: ART CATALOG & INVENTORY MANAGER
               ======================================================= -->
          <div id="tab-products" class="admin-tab-content" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg); flex-wrap: wrap; gap: 12px;">
              <div>
                <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 4px;">Art Catalog Management</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Manage titles, SKU codes, pricing, MRP, stock levels, folklore & photos</p>
              </div>
              <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <input type="text" id="admin-product-search" class="admin-input" placeholder="🔍 Search by name, SKU, category..." style="min-width: 260px;" />
                <button type="button" id="open-add-product-btn" class="admin-submit-btn">
                  + Add New Art Piece
                </button>
              </div>
            </div>

            <!-- Add Product Form Section -->
            <div id="add-product-section" class="admin-form-section" style="display: none;">
              <div class="admin-section-header">
                <h3 class="admin-section-title">Add New Art Piece</h3>
                <button type="button" id="cancel-add-product-btn" class="admin-cancel-btn">&times; Close</button>
              </div>

              <form id="add-product-form" class="admin-form-grid">
                <div class="admin-input-group">
                  <label class="admin-label">Product Name *</label>
                  <input type="text" name="name" class="admin-input" placeholder="e.g. Lotus Mandala Wall Art (12x12”)" required />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Product Code (SKU) *</label>
                  <input type="text" name="code" class="admin-input" placeholder="e.g. MC/L-016" required />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Category *</label>
                  <select name="category" class="admin-select" required>
                    <option value="Lippan Art">Lippan Art</option>
                    <option value="Jharokhas & Frames">Jharokhas & Frames</option>
                    <option value="Spiritual & Khatu Shyam">Spiritual & Khatu Shyam</option>
                    <option value="Shyam Ji Decor">Shyam Ji Decor</option>
                    <option value="Vastu Species">Vastu Species</option>
                    <option value="Diwali & Festive Decor">Diwali & Festive Decor</option>
                  </select>
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Selling Price (INR) *</label>
                  <input type="number" name="price" class="admin-input" placeholder="2499" required />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">MRP (INR)</label>
                  <input type="number" name="mrp" class="admin-input" placeholder="3999" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Stock Quantity *</label>
                  <input type="number" name="stock" class="admin-input" placeholder="10" required />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Dimensions</label>
                  <input type="text" name="size" class="admin-input" placeholder="12 x 12 inches" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Materials</label>
                  <input type="text" name="material" class="admin-input" placeholder="Clay Relief, MDF Board, Mirrors" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Origin</label>
                  <input type="text" name="origin" class="admin-input" placeholder="Rohtak, Haryana, India" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Artisan Name</label>
                  <input type="text" name="artist" class="admin-input" placeholder="Master Craftsman Murli Prasad" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Image URLs (comma separated)</label>
                  <input type="text" name="images" class="admin-input" placeholder="/img-prodcut/IMG_4041.png" />
                  <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 3px; display: block;">Select from available photos or paste path</small>
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">YouTube Video URL</label>
                  <input type="text" name="video" class="admin-input" placeholder="https://www.youtube.com/watch?v=..." />
                </div>

                <div class="admin-input-group admin-full-width">
                  <label class="admin-label">Folklore & Significance</label>
                  <textarea name="folklore" class="admin-textarea" placeholder="Describe historical, mythological, or devotional story..."></textarea>
                </div>

                <div class="admin-input-group admin-full-width">
                  <label class="admin-label">Description *</label>
                  <textarea name="description" class="admin-textarea" placeholder="Detailed product narrative..." required></textarea>
                </div>

                <div class="admin-actions-row admin-full-width">
                  <button type="submit" class="admin-submit-btn">Save Product</button>
                </div>
              </form>
            </div>

            <!-- Edit Product Form Section -->
            <div id="edit-product-section" class="admin-form-section" style="display: none;">
              <div class="admin-section-header">
                <h3 class="admin-section-title">Edit Artwork</h3>
                <button type="button" id="cancel-edit-product-btn" class="admin-cancel-btn">&times; Close</button>
              </div>

              <form id="edit-product-form" class="admin-form-grid">
                <input type="hidden" name="id" id="edit-id" />

                <div class="admin-input-group">
                  <label class="admin-label">Product Name *</label>
                  <input type="text" name="name" id="edit-name" class="admin-input" required />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Product Code (SKU) *</label>
                  <input type="text" name="code" id="edit-code" class="admin-input" required />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Category *</label>
                  <select name="category" id="edit-category" class="admin-select" required>
                    <option value="Lippan Art">Lippan Art</option>
                    <option value="Jharokhas & Frames">Jharokhas & Frames</option>
                    <option value="Spiritual & Khatu Shyam">Spiritual & Khatu Shyam</option>
                    <option value="Shyam Ji Decor">Shyam Ji Decor</option>
                    <option value="Vastu Species">Vastu Species</option>
                    <option value="Diwali & Festive Decor">Diwali & Festive Decor</option>
                  </select>
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Selling Price (INR) *</label>
                  <input type="number" name="price" id="edit-price" class="admin-input" required />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">MRP (INR)</label>
                  <input type="number" name="mrp" id="edit-mrp" class="admin-input" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Stock Quantity *</label>
                  <input type="number" name="stock" id="edit-stock" class="admin-input" required />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Dimensions</label>
                  <input type="text" name="size" id="edit-size" class="admin-input" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Materials</label>
                  <input type="text" name="material" id="edit-material" class="admin-input" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Origin</label>
                  <input type="text" name="origin" id="edit-origin" class="admin-input" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Artisan Name</label>
                  <input type="text" name="artist" id="edit-artist" class="admin-input" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Rating Score</label>
                  <input type="text" name="rating" id="edit-rating" class="admin-input" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Reviews Count</label>
                  <input type="number" name="reviewsCount" id="edit-reviews-count" class="admin-input" />
                </div>

                <div class="admin-input-group admin-full-width">
                  <label class="admin-label">Image URLs (comma separated)</label>
                  <input type="text" name="images" id="edit-images" class="admin-input" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">YouTube Video URL</label>
                  <input type="text" name="video" id="edit-video" class="admin-input" />
                </div>

                <div class="admin-input-group admin-full-width">
                  <label class="admin-label">Folklore & Significance</label>
                  <textarea name="folklore" id="edit-folklore" class="admin-textarea"></textarea>
                </div>

                <div class="admin-input-group admin-full-width">
                  <label class="admin-label">Description *</label>
                  <textarea name="description" id="edit-description" class="admin-textarea" required></textarea>
                </div>

                <div class="admin-actions-row admin-full-width">
                  <button type="submit" class="admin-submit-btn">Update Artwork</button>
                </div>
              </form>
            </div>

            <!-- Products Data Table -->
            <div class="admin-table-container">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>Art Piece</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>MRP</th>
                    <th>Inventory</th>
                    <th>Rating</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($products as $p): 
                    $pJson = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                  ?>
                    <tr class="admin-table-row">
                      <td>
                        <span style="font-weight: 700; color: var(--gold-accent, #c99846); font-size: 0.85rem; letter-spacing: 0.5px;">
                          <?php echo htmlspecialchars($p['code'] ?? '-'); ?>
                        </span>
                      </td>
                      <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                          <img src="<?php echo htmlspecialchars($p['images'][0]); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" width="44" height="44" style="border-radius: 6px; object-fit: contain; background: #f8faf8; border: 1px solid #e5e7eb;" />
                          <span class="admin-product-name"><?php echo htmlspecialchars($p['name']); ?></span>
                        </div>
                      </td>
                      <td><span class="admin-product-cat"><?php echo htmlspecialchars($p['category']); ?></span></td>
                      <td><strong>₹<?php echo format_inr($p['price']); ?></strong></td>
                      <td><span style="color: var(--text-muted); text-decoration: line-through;">₹<?php echo format_inr($p['mrp'] ?? round($p['price'] * 1.3)); ?></span></td>
                      <td>
                        <span class="<?php echo ($p['stock'] <= 3) ? 'admin-low-stock-text' : 'admin-in-stock-text'; ?>">
                          <?php echo $p['stock']; ?> units
                        </span>
                      </td>
                      <td>★ <?php echo $p['rating'] ?? 4.8; ?> (<?php echo $p['reviewsCount'] ?? 15; ?>)</td>
                      <td>
                        <div class="admin-table-actions">
                          <button type="button" class="admin-edit-btn" data-product="<?php echo $pJson; ?>">Edit</button>
                          <button type="button" class="admin-delete-btn" data-product-id="<?php echo htmlspecialchars($p['id']); ?>" data-product-name="<?php echo htmlspecialchars($p['name']); ?>">Delete</button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- =======================================================
               TAB 3: ORDER MANAGER
               ======================================================= -->
          <div id="tab-orders" class="admin-tab-content" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
              <div>
                <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 4px;">Orders & Fulfillment</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Manage customer dispatch statuses, addresses, items, and inventory tracking</p>
              </div>
              <div class="orders-filter-row" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <input type="text" id="admin-order-search" class="admin-input" placeholder="🔍 Search Order ID, Customer, Phone..." style="min-width: 240px;" />
                <select id="admin-order-filter" class="admin-select" style="min-width: 160px;">
                  <option value="All">All Statuses</option>
                  <option value="Pending">Pending</option>
                  <option value="Processing">Processing</option>
                  <option value="Dispatched">Dispatched</option>
                  <option value="Delivered">Delivered</option>
                  <option value="Cancelled">Cancelled</option>
                </select>
              </div>
            </div>

            <?php if (empty($orders)): ?>
              <div class="admin-card-section" style="text-align: center; padding: 40px;">
                <p style="color: var(--text-muted);">No orders found in database.</p>
              </div>
            <?php else: ?>
              <div class="admin-table-container">
                <table class="admin-table">
                  <thead>
                    <tr>
                      <th>Order ID</th>
                      <th>Date</th>
                      <th>Customer & Address</th>
                      <th>Items</th>
                      <th>Subtotal</th>
                      <th>Payment</th>
                      <th>Status Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($orders as $ord): 
                      $currentStatus = $ord['orderStatus'] ?? $ord['status'] ?? 'Pending';
                    ?>
                      <tr class="admin-table-row order-row" data-order-status="<?php echo htmlspecialchars($currentStatus); ?>">
                        <td><strong><?php echo htmlspecialchars($ord['id']); ?></strong></td>
                        <td><small><?php echo htmlspecialchars($ord['createdAt'] ?? $ord['date'] ?? '-'); ?></small></td>
                        <td>
                          <div>
                            <strong><?php echo htmlspecialchars($ord['customer']['name'] ?? 'Guest'); ?></strong><br>
                            <small style="color: var(--text-muted);">
                              <?php echo htmlspecialchars($ord['customer']['phone'] ?? ''); ?> • <?php echo htmlspecialchars($ord['customer']['city'] ?? ''); ?>, <?php echo htmlspecialchars($ord['customer']['state'] ?? ''); ?>
                            </small>
                          </div>
                        </td>
                        <td>
                          <div style="max-width: 200px; font-size: 0.82rem;">
                            <?php foreach (($ord['items'] ?? []) as $it): ?>
                              <div>• <?php echo htmlspecialchars($it['product']['name'] ?? $it['productName'] ?? 'Artwork'); ?> &times; <?php echo $it['quantity'] ?? 1; ?></div>
                            <?php endforeach; ?>
                          </div>
                        </td>
                        <td><strong>₹<?php echo format_inr($ord['totalAmount'] ?? $ord['subtotal'] ?? 0); ?></strong></td>
                        <td>
                          <span class="admin-badge-neutral"><?php echo htmlspecialchars($ord['paymentMethod'] ?? 'UPI'); ?></span>
                        </td>
                        <td>
                          <select class="admin-select order-status-select" data-order-id="<?php echo htmlspecialchars($ord['id']); ?>" style="padding: 4px 8px; font-size: 0.8rem;">
                            <option value="Pending" <?php echo ($currentStatus === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="Processing" <?php echo ($currentStatus === 'Processing') ? 'selected' : ''; ?>>Processing</option>
                            <option value="Dispatched" <?php echo ($currentStatus === 'Dispatched') ? 'selected' : ''; ?>>Dispatched</option>
                            <option value="Delivered" <?php echo ($currentStatus === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                            <option value="Cancelled" <?php echo ($currentStatus === 'Cancelled') ? 'selected' : ''; ?>>Cancelled (Restores Stock)</option>
                          </select>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>

          <!-- =======================================================
               TAB 4: FESTIVE SALES, ANNOUNCEMENTS & COUPONS
               ======================================================= -->
          <div id="tab-festive" class="admin-tab-content" style="display: none;">
            <div style="margin-bottom: var(--spacing-lg);">
              <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 4px;">Festive Offers, Top Banner & Promo Coupons</h2>
              <p style="color: var(--text-muted); font-size: 0.85rem;">Live customize header announcement banners, homepage festive sale cards, and checkout discount coupons</p>
            </div>

            <div class="admin-cards-2col">
              <!-- Section 1: Top Announcement Bar -->
              <div class="admin-card-section">
                <div class="admin-section-header">
                  <h3 class="admin-section-title">Top Header Announcement Bar</h3>
                </div>

                <form id="announcement-settings-form">
                  <input type="hidden" name="action" value="save_settings" />
                  
                  <div class="admin-input-group" style="margin-bottom: 14px;">
                    <label class="admin-toggle-label">
                      <input type="checkbox" name="announcement_enabled" <?php echo !empty($site_settings['announcement']['enabled']) ? 'checked' : ''; ?> class="admin-toggle-checkbox" />
                      <span class="admin-toggle-text"><strong>Enable Top Announcement Banner</strong></span>
                    </label>
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Announcement Text *</label>
                    <input type="text" name="announcement_text" class="admin-input" value="<?php echo htmlspecialchars($site_settings['announcement']['text'] ?? ''); ?>" placeholder="Use {coupon} to highlight code" required />
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Tip: Write <code>{coupon}</code> where you want the coupon code to appear bold.</small>
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Coupon Code to Highlight</label>
                    <input type="text" name="announcement_coupon" class="admin-input" value="<?php echo htmlspecialchars($site_settings['announcement']['coupon_code'] ?? 'MURLI10'); ?>" placeholder="e.g. MURLI10" />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Banner Destination Link</label>
                    <input type="text" name="announcement_link" class="admin-input" value="<?php echo htmlspecialchars($site_settings['announcement']['link'] ?? '/shop.php'); ?>" placeholder="/shop.php" />
                  </div>

                  <button type="submit" class="admin-submit-btn" style="margin-top: 10px;">Save Announcement Bar</button>
                </form>
              </div>

              <!-- Section 2: Homepage Festive Offer Card -->
              <div class="admin-card-section">
                <div class="admin-section-header">
                  <h3 class="admin-section-title">Homepage Festive Promo Card</h3>
                </div>

                <form id="festive-card-form">
                  <input type="hidden" name="action" value="save_homepage_content" />
                  
                  <div class="admin-input-group" style="margin-bottom: 14px;">
                    <label class="admin-toggle-label">
                      <input type="checkbox" name="festive_offer_enabled" <?php echo !empty($homepage_content['festive_offer']['enabled']) ? 'checked' : ''; ?> class="admin-toggle-checkbox" />
                      <span class="admin-toggle-text"><strong>Show Festive Promo Section on Homepage</strong></span>
                    </label>
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Badge Label</label>
                    <input type="text" name="festive_offer_badge" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['festive_offer']['badge'] ?? 'Special Festive Celebration'); ?>" />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Main Heading *</label>
                    <input type="text" name="festive_offer_title" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['festive_offer']['title'] ?? ''); ?>" required />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Discount Badge Text</label>
                    <input type="text" name="festive_offer_discount_label" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['festive_offer']['discount_label'] ?? '10% FLAT OFF'); ?>" placeholder="10% OFF" />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Coupon Code</label>
                    <input type="text" name="festive_offer_coupon" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['festive_offer']['coupon_code'] ?? 'MURLI10'); ?>" />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Description Text</label>
                    <textarea name="festive_offer_description" class="admin-textarea" rows="2"><?php echo htmlspecialchars($homepage_content['festive_offer']['description'] ?? ''); ?></textarea>
                  </div>

                  <button type="submit" class="admin-submit-btn" style="margin-top: 10px;">Save Homepage Festive Card</button>
                </form>
              </div>
            </div>

            <!-- Section 3: Checkout Promo Coupons Manager -->
            <div class="admin-card-section" style="margin-top: var(--spacing-xl);">
              <div class="admin-section-header">
                <div>
                  <h3 class="admin-section-title">Checkout Promo Coupons Manager</h3>
                  <p style="color: var(--text-muted); font-size: 0.82rem;">Create, activate, or disable discount codes applicable during checkout</p>
                </div>
                <button type="button" id="toggle-add-coupon-btn" class="admin-submit-btn">+ Add New Coupon</button>
              </div>

              <!-- Add Coupon Form -->
              <div id="add-coupon-box" style="display: none; background: #f9fbf9; border: 1px solid #e0ebd0; border-radius: 8px; padding: 18px; margin-bottom: 20px;">
                <h4 style="margin-bottom: 12px; font-weight: 700;">Create / Edit Promo Coupon</h4>
                <form id="save-coupon-form" class="admin-form-grid">
                  <input type="hidden" name="action" value="save_coupon" />
                  <div class="admin-input-group">
                    <label class="admin-label">Coupon Code (Uppercase) *</label>
                    <input type="text" name="code" class="admin-input" placeholder="e.g. FESTIVE25" required />
                  </div>
                  <div class="admin-input-group">
                    <label class="admin-label">Discount Percentage (%) *</label>
                    <input type="number" name="discountPercent" class="admin-input" placeholder="15" min="1" max="100" required />
                  </div>
                  <div class="admin-input-group">
                    <label class="admin-label">Minimum Cart Order Value (₹)</label>
                    <input type="number" name="minOrder" class="admin-input" placeholder="2500" value="0" />
                  </div>
                  <div class="admin-input-group">
                    <label class="admin-label">Coupon Description</label>
                    <input type="text" name="description" class="admin-input" placeholder="15% discount on Diwali orders above ₹2,500" />
                  </div>
                  <div class="admin-input-group admin-full-width">
                    <label class="admin-toggle-label">
                      <input type="checkbox" name="active" checked class="admin-toggle-checkbox" />
                      <span class="admin-toggle-text">Active & Redeemable at Checkout</span>
                    </label>
                  </div>
                  <div class="admin-actions-row admin-full-width">
                    <button type="submit" class="admin-submit-btn">Save Coupon</button>
                    <button type="button" id="cancel-coupon-btn" class="admin-cancel-btn">Cancel</button>
                  </div>
                </form>
              </div>

              <!-- Coupons Table -->
              <div class="admin-table-container">
                <table class="admin-table">
                  <thead>
                    <tr>
                      <th>Coupon Code</th>
                      <th>Discount</th>
                      <th>Min Order</th>
                      <th>Description</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($coupons as $cp): ?>
                      <tr>
                        <td>
                          <span style="font-weight: 800; color: var(--accent-green-vibrant); background: rgba(30, 166, 89, 0.1); padding: 3px 8px; border-radius: 4px; letter-spacing: 0.5px;">
                            <?php echo htmlspecialchars($cp['code']); ?>
                          </span>
                        </td>
                        <td><strong><?php echo $cp['discountPercent']; ?>% OFF</strong></td>
                        <td>₹<?php echo format_inr($cp['minOrder'] ?? 0); ?></td>
                        <td><small><?php echo htmlspecialchars($cp['description'] ?? '-'); ?></small></td>
                        <td>
                          <span class="status-badge <?php echo !empty($cp['active']) ? 'status-delivered' : 'status-cancelled'; ?>">
                            <?php echo !empty($cp['active']) ? 'Active' : 'Disabled'; ?>
                          </span>
                        </td>
                        <td>
                          <button type="button" class="admin-delete-btn delete-coupon-btn" data-coupon-code="<?php echo htmlspecialchars($cp['code']); ?>">Delete</button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- =======================================================
               TAB 5: HOMEPAGE CMS & CONTENT EDITOR
               ======================================================= -->
          <div id="tab-homepage" class="admin-tab-content" style="display: none;">
            <div style="margin-bottom: var(--spacing-lg);">
              <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 4px;">Homepage CMS & Content Customizer</h2>
              <p style="color: var(--text-muted); font-size: 0.85rem;">Edit hero banners, titles, descriptions, seasonal curation cards, and story highlights in real time</p>
            </div>

            <form id="homepage-cms-form">
              <input type="hidden" name="action" value="save_homepage_content" />

              <!-- Section 1: Hero Customizer -->
              <div class="admin-card-section" style="margin-bottom: 24px;">
                <h3 class="admin-section-title" style="margin-bottom: 14px;">1. Hero Banner Customizer</h3>
                <div class="admin-form-grid">
                  <div class="admin-input-group">
                    <label class="admin-label">Top Location / Badge Text</label>
                    <input type="text" name="hero_badge" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['hero']['badge'] ?? ''); ?>" required />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Showcase Image Path *</label>
                    <select name="hero_image" class="admin-select">
                      <?php foreach ($available_images as $img): ?>
                        <option value="<?php echo htmlspecialchars($img); ?>" <?php echo (($homepage_content['hero']['image'] ?? '') === $img) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($img); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Hero Main Title</label>
                    <input type="text" name="hero_title_main" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['hero']['title_main'] ?? ''); ?>" required />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Hero Title Gradient Highlight Phrase</label>
                    <input type="text" name="hero_title_highlight" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['hero']['title_highlight'] ?? ''); ?>" required />
                  </div>

                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Hero Lead Narrative Paragraph</label>
                    <textarea name="hero_description" class="admin-textarea" rows="3" required><?php echo htmlspecialchars($homepage_content['hero']['description'] ?? ''); ?></textarea>
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Primary Button Text</label>
                    <input type="text" name="hero_cta_primary_text" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['hero']['cta_primary_text'] ?? 'Explore Catalog'); ?>" />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Primary Button Link</label>
                    <input type="text" name="hero_cta_primary_link" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['hero']['cta_primary_link'] ?? '/shop.php'); ?>" />
                  </div>
                </div>
              </div>

              <!-- Section 1.5: Milestone Stats Numbers Customizer -->
              <div class="admin-card-section" style="margin-bottom: 24px;">
                <h3 class="admin-section-title" style="margin-bottom: 6px;">1.5 Key Milestones & Stats Counters</h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 16px;">These numbers animate smoothly on scroll right below the full-height Hero viewport.</p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                  <?php for ($s = 1; $s <= 3; $s++): 
                    $stat = $homepage_content['stats'][$s - 1] ?? [];
                  ?>
                    <div style="background: #f8faf8; border: 1px solid #e2e8e2; border-radius: 8px; padding: 14px;">
                      <h4 style="font-weight: 700; margin-bottom: 10px; color: var(--accent-green-vibrant);">Stat Counter <?php echo $s; ?></h4>
                      
                      <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                        <div>
                          <label class="admin-label">Number Value</label>
                          <input type="text" name="stat<?php echo $s; ?>_count" class="admin-input" value="<?php echo htmlspecialchars($stat['count'] ?? '100'); ?>" placeholder="100" required />
                        </div>
                        <div>
                          <label class="admin-label">Suffix</label>
                          <input type="text" name="stat<?php echo $s; ?>_suffix" class="admin-input" value="<?php echo htmlspecialchars($stat['suffix'] ?? '+'); ?>" placeholder="+" />
                        </div>
                        <div>
                          <label class="admin-label">Decimals</label>
                          <input type="number" name="stat<?php echo $s; ?>_decimals" class="admin-input" value="<?php echo htmlspecialchars($stat['decimals'] ?? '0'); ?>" min="0" max="2" />
                        </div>
                      </div>

                      <div class="admin-input-group" style="margin-bottom: 8px;">
                        <label class="admin-label">Metric Label</label>
                        <input type="text" name="stat<?php echo $s; ?>_label" class="admin-input" value="<?php echo htmlspecialchars($stat['label'] ?? ''); ?>" required />
                      </div>

                      <div class="admin-input-group">
                        <label class="admin-label">Supporting Description</label>
                        <textarea name="stat<?php echo $s; ?>_desc" class="admin-textarea" rows="2"><?php echo htmlspecialchars($stat['desc'] ?? ''); ?></textarea>
                      </div>
                    </div>
                  <?php endfor; ?>
                </div>
              </div>

              <!-- Section 2: Seasonal Vibes & 3 Curated Cards -->
              <div class="admin-card-section" style="margin-bottom: 24px;">
                <h3 class="admin-section-title" style="margin-bottom: 14px;">2. Seasonal Vibes & 3 Curations</h3>
                
                <div class="admin-form-grid" style="margin-bottom: 16px;">
                  <div class="admin-input-group">
                    <label class="admin-label">Section Badge</label>
                    <input type="text" name="seasonal_badge" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['seasonal_vibes']['badge'] ?? ''); ?>" />
                  </div>
                  <div class="admin-input-group">
                    <label class="admin-label">Section Main Title</label>
                    <input type="text" name="seasonal_title" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['seasonal_vibes']['title'] ?? ''); ?>" />
                  </div>
                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Section Subtitle</label>
                    <input type="text" name="seasonal_subtitle" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['seasonal_vibes']['subtitle'] ?? ''); ?>" />
                  </div>
                </div>

                <!-- 3 Curated Cards Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                  <?php for ($i = 1; $i <= 3; $i++): 
                    $card = $homepage_content['seasonal_vibes']['cards'][$i - 1] ?? [];
                  ?>
                    <div style="background: #f8faf8; border: 1px solid #e2e8e2; border-radius: 8px; padding: 14px;">
                      <h4 style="font-weight: 700; margin-bottom: 10px; color: var(--accent-green-vibrant);">Card <?php echo $i; ?></h4>
                      
                      <div class="admin-input-group" style="margin-bottom: 8px;">
                        <label class="admin-label">Theme Tag</label>
                        <input type="text" name="card<?php echo $i; ?>_tag" class="admin-input" value="<?php echo htmlspecialchars($card['tag'] ?? ''); ?>" />
                      </div>

                      <div class="admin-input-group" style="margin-bottom: 8px;">
                        <label class="admin-label">Card Title</label>
                        <input type="text" name="card<?php echo $i; ?>_title" class="admin-input" value="<?php echo htmlspecialchars($card['title'] ?? ''); ?>" />
                      </div>

                      <div class="admin-input-group" style="margin-bottom: 8px;">
                        <label class="admin-label">Card Image</label>
                        <select name="card<?php echo $i; ?>_image" class="admin-select">
                          <?php foreach ($available_images as $img): ?>
                            <option value="<?php echo htmlspecialchars($img); ?>" <?php echo (($card['image'] ?? '') === $img) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($img); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>

                      <div class="admin-input-group" style="margin-bottom: 8px;">
                        <label class="admin-label">Description</label>
                        <textarea name="card<?php echo $i; ?>_desc" class="admin-textarea" rows="2"><?php echo htmlspecialchars($card['desc'] ?? ''); ?></textarea>
                      </div>

                      <div class="admin-input-group">
                        <label class="admin-label">Link URL</label>
                        <input type="text" name="card<?php echo $i; ?>_link" class="admin-input" value="<?php echo htmlspecialchars($card['link'] ?? '/shop.php'); ?>" />
                      </div>
                    </div>
                  <?php endfor; ?>
                </div>
              </div>

              <!-- Section 3: Our Story Showcase -->
              <div class="admin-card-section" style="margin-bottom: 24px;">
                <h3 class="admin-section-title" style="margin-bottom: 14px;">3. Our Story Section Customizer</h3>
                <div class="admin-form-grid">
                  <div class="admin-input-group">
                    <label class="admin-label">Story Badge</label>
                    <input type="text" name="story_badge" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['story']['badge'] ?? ''); ?>" />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Story Heading Title</label>
                    <input type="text" name="story_title" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['story']['title'] ?? ''); ?>" />
                  </div>

                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Story Subtitle / Lead Paragraph</label>
                    <input type="text" name="story_lead" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['story']['lead'] ?? 'A journey of handmade art, memories, devotion and love.'); ?>" />
                  </div>

                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Featured Quote</label>
                    <textarea name="story_quote" class="admin-textarea" rows="2"><?php echo htmlspecialchars($homepage_content['story']['quote'] ?? ''); ?></textarea>
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Quote Author</label>
                    <input type="text" name="story_author" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['story']['author'] ?? 'Murli Creations'); ?>" />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Brand Promise Slogan</label>
                    <input type="text" name="story_promise" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['story']['promise'] ?? 'Handcrafted with Love • Inspired by Tradition • Created to Be Cherished'); ?>" />
                  </div>

                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Story Visual Photo</label>
                    <select name="story_image" class="admin-select">
                      <?php foreach ($available_images as $img): ?>
                        <option value="<?php echo htmlspecialchars($img); ?>" <?php echo (($homepage_content['story']['image'] ?? '') === $img) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($img); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- 3 Story Highlights -->
                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Highlight 1 (Title + Description)</label>
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px;">
                      <input type="text" name="story_h1_title" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['story']['highlights'][0]['title'] ?? ''); ?>" placeholder="Title" />
                      <input type="text" name="story_h1_desc" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['story']['highlights'][0]['desc'] ?? ''); ?>" placeholder="Description" />
                    </div>
                  </div>

                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Highlight 2 (Title + Description)</label>
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px;">
                      <input type="text" name="story_h2_title" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['story']['highlights'][1]['title'] ?? ''); ?>" placeholder="Title" />
                      <input type="text" name="story_h2_desc" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['story']['highlights'][1]['desc'] ?? ''); ?>" placeholder="Description" />
                    </div>
                  </div>

                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Highlight 3 (Title + Description)</label>
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px;">
                      <input type="text" name="story_h3_title" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['story']['highlights'][2]['title'] ?? ''); ?>" placeholder="Title" />
                      <input type="text" name="story_h3_desc" class="admin-input" value="<?php echo htmlspecialchars($homepage_content['story']['highlights'][2]['desc'] ?? ''); ?>" placeholder="Description" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="admin-actions-row">
                <button type="submit" class="admin-submit-btn" style="padding: 12px 28px; font-size: 0.95rem;">Save All Homepage Content</button>
              </div>
            </form>
          </div>

          <!-- =======================================================
               TAB 6: REVIEWS & TESTIMONIALS MANAGER
               ======================================================= -->
          <div id="tab-reviews" class="admin-tab-content" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg); flex-wrap: wrap; gap: 12px;">
              <div>
                <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 4px;">Customer Reviews & Testimonials</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Manage patron feedback and ratings shown across the website and product pages</p>
              </div>
              <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <input type="text" id="admin-review-search" class="admin-input" placeholder="🔍 Search reviews by name or text..." style="min-width: 250px;" />
                <button type="button" id="toggle-add-review-btn" class="admin-submit-btn">+ Add Customer Review</button>
              </div>
            </div>

            <!-- Add Review Box -->
            <div id="add-review-box" style="display: none; background: #f9fbf9; border: 1px solid #e0ebd0; border-radius: 8px; padding: 18px; margin-bottom: 20px;">
              <h4 style="margin-bottom: 12px; font-weight: 700;">Add Verified Review</h4>
              <form id="save-review-form" class="admin-form-grid">
                <input type="hidden" name="action" value="save_review" />

                <div class="admin-input-group">
                  <label class="admin-label">Product *</label>
                  <select name="productId" class="admin-select" required>
                    <?php foreach ($products as $pr): ?>
                      <option value="<?php echo htmlspecialchars($pr['id']); ?>"><?php echo htmlspecialchars($pr['name']); ?> (<?php echo htmlspecialchars($pr['code'] ?? ''); ?>)</option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Reviewer Name *</label>
                  <input type="text" name="name" class="admin-input" placeholder="e.g. Radhika Sharma" required />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">City / Location</label>
                  <input type="text" name="location" class="admin-input" placeholder="e.g. Gurgaon, Haryana" />
                </div>

                <div class="admin-input-group">
                  <label class="admin-label">Rating (1 to 5 Stars)</label>
                  <select name="rating" class="admin-select">
                    <option value="5.0">5.0 ★★★★★</option>
                    <option value="4.9">4.9 ★★★★★</option>
                    <option value="4.8">4.8 ★★★★☆</option>
                    <option value="4.5">4.5 ★★★★☆</option>
                    <option value="4.0">4.0 ★★★★☆</option>
                  </select>
                </div>

                <div class="admin-input-group admin-full-width">
                  <label class="admin-label">Review Comment *</label>
                  <textarea name="comment" class="admin-textarea" rows="3" placeholder="Write feedback comment..." required></textarea>
                </div>

                <div class="admin-actions-row admin-full-width">
                  <button type="submit" class="admin-submit-btn">Save Review</button>
                  <button type="button" id="cancel-review-btn" class="admin-cancel-btn">Cancel</button>
                </div>
              </form>
            </div>

            <!-- Reviews Table -->
            <div class="admin-table-container">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Reviewer</th>
                    <th>Product</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($reviews as $rev): 
                    // Find product name
                    $pName = 'Lippan Artwork';
                    foreach ($products as $pr) {
                      if ($pr['id'] === ($rev['productId'] ?? '')) {
                        $pName = $pr['name'];
                        break;
                      }
                    }
                  ?>
                    <tr>
                      <td>
                        <strong><?php echo htmlspecialchars($rev['name'] ?? 'Patron'); ?></strong><br>
                        <small style="color: var(--text-muted);"><?php echo htmlspecialchars($rev['location'] ?? 'India'); ?></small>
                      </td>
                      <td><small><?php echo htmlspecialchars($pName); ?></small></td>
                      <td><span style="color: #10B981; font-weight: 700;">★ <?php echo $rev['rating'] ?? 5.0; ?></span></td>
                      <td><small style="display: block; max-width: 320px; line-height: 1.4;"><?php echo htmlspecialchars($rev['comment'] ?? ''); ?></small></td>
                      <td><small><?php echo htmlspecialchars($rev['date'] ?? '-'); ?></small></td>
                      <td>
                        <button type="button" class="admin-delete-btn delete-review-btn" data-review-id="<?php echo htmlspecialchars($rev['id']); ?>">Delete</button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- =======================================================
               TAB 7: STUDIO & GENERAL SETTINGS
               ======================================================= -->
          <div id="tab-settings" class="admin-tab-content" style="display: none;">
            <div style="margin-bottom: var(--spacing-lg);">
              <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 4px;">Studio Information & General Settings</h2>
              <p style="color: var(--text-muted); font-size: 0.85rem;">Manage contact details, workshop address, free shipping policies, social media, and footer copy</p>
            </div>

            <form id="studio-settings-form">
              <input type="hidden" name="action" value="save_settings" />

              <div class="admin-card-section" style="margin-bottom: 20px;">
                <h3 class="admin-section-title" style="margin-bottom: 14px;">Studio Profile & Contacts</h3>
                <div class="admin-form-grid">
                  <div class="admin-input-group">
                    <label class="admin-label">Studio Brand Name</label>
                    <input type="text" name="studio_name" class="admin-input" value="<?php echo htmlspecialchars($site_settings['studio']['name'] ?? 'Murli Creations'); ?>" required />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Master Craftsman / Founder</label>
                    <input type="text" name="studio_founder" class="admin-input" value="<?php echo htmlspecialchars($site_settings['studio']['founder'] ?? 'Master Craftsman Murli Prasad'); ?>" />
                  </div>

                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Tagline</label>
                    <input type="text" name="studio_tagline" class="admin-input" value="<?php echo htmlspecialchars($site_settings['studio']['tagline'] ?? ''); ?>" />
                  </div>

                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Workshop Address (Rohtak, Haryana)</label>
                    <input type="text" name="studio_address" class="admin-input" value="<?php echo htmlspecialchars($site_settings['studio']['address'] ?? ''); ?>" required />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Contact Phone</label>
                    <input type="text" name="studio_phone" class="admin-input" value="<?php echo htmlspecialchars($site_settings['studio']['phone'] ?? ''); ?>" />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">WhatsApp Number *</label>
                    <input type="text" name="studio_whatsapp" class="admin-input" value="<?php echo htmlspecialchars($site_settings['studio']['whatsapp'] ?? ''); ?>" required />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Support Email</label>
                    <input type="email" name="studio_email" class="admin-input" value="<?php echo htmlspecialchars($site_settings['studio']['email'] ?? ''); ?>" required />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Studio Business Hours</label>
                    <input type="text" name="studio_hours" class="admin-input" value="<?php echo htmlspecialchars($site_settings['studio']['hours'] ?? 'Mon - Sat: 10:00 AM - 7:30 PM (IST)'); ?>" />
                  </div>
                </div>
              </div>

              <!-- Shipping & Policy Settings -->
              <div class="admin-card-section" style="margin-bottom: 20px;">
                <h3 class="admin-section-title" style="margin-bottom: 14px;">Shipping & Transit Policies</h3>
                <div class="admin-form-grid">
                  <div class="admin-input-group">
                    <label class="admin-label">Free Shipping Cart Threshold (INR ₹) *</label>
                    <input type="number" name="shipping_free_threshold" class="admin-input" value="<?php echo htmlspecialchars($site_settings['shipping']['free_threshold'] ?? 3000); ?>" required />
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Orders above this value get automatic 100% Free Shipping.</small>
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">Standard Shipping Charge (INR ₹)</label>
                    <input type="number" name="shipping_standard_charge" class="admin-input" value="<?php echo htmlspecialchars($site_settings['shipping']['standard_charge'] ?? 149); ?>" required />
                  </div>

                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Estimated Dispatch Turnaround</label>
                    <input type="text" name="shipping_dispatch_time" class="admin-input" value="<?php echo htmlspecialchars($site_settings['shipping']['dispatch_time'] ?? 'Dispatched in 2-3 business days'); ?>" />
                  </div>
                </div>
              </div>

              <!-- Social Links & Footer -->
              <div class="admin-card-section" style="margin-bottom: 20px;">
                <h3 class="admin-section-title" style="margin-bottom: 14px;">Social Links & Footer Blurb</h3>
                <div class="admin-form-grid">
                  <div class="admin-input-group">
                    <label class="admin-label">Instagram Profile URL</label>
                    <input type="url" name="social_instagram" class="admin-input" value="<?php echo htmlspecialchars($site_settings['social']['instagram'] ?? ''); ?>" />
                  </div>

                  <div class="admin-input-group">
                    <label class="admin-label">WhatsApp Direct Link</label>
                    <input type="url" name="social_whatsapp" class="admin-input" value="<?php echo htmlspecialchars($site_settings['social']['whatsapp'] ?? ''); ?>" />
                  </div>

                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Footer About Blurb</label>
                    <textarea name="footer_about" class="admin-textarea" rows="2"><?php echo htmlspecialchars($site_settings['footer']['about_text'] ?? ''); ?></textarea>
                  </div>

                  <div class="admin-input-group admin-full-width">
                    <label class="admin-label">Footer Copyright Notice</label>
                    <input type="text" name="footer_copyright" class="admin-input" value="<?php echo htmlspecialchars($site_settings['footer']['copyright'] ?? ''); ?>" />
                  </div>
                </div>
              </div>

              <div class="admin-actions-row">
                <button type="submit" class="admin-submit-btn" style="padding: 12px 28px; font-size: 0.95rem;">Save All Studio Settings</button>
              </div>
            </form>
          </div>

        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <?php include __DIR__ . '/includes/cart-drawer.php'; ?>
  <?php include __DIR__ . '/includes/floating-widgets.php'; ?>

  <!-- Scripts -->
  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/main.js"></script>
  <script src="/assets/js/admin.js"></script>
</body>
</html>
