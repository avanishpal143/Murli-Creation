<?php
/**
 * =============================================================================
 * MURLI CREATIONS - TRACK-ORDER VIEW
 * =============================================================================
 * Is page ka design, layout aur storefront logic yahan sthit hai.
 * Backend data: backend/config/functions.php
 * =============================================================================
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}
require_once ROOT_PATH . '/backend/config/functions.php';


$query = trim($_GET['order'] ?? $_GET['id'] ?? $_GET['phone'] ?? '');
$trackingResults = [];

if (!empty($query)) {
    if (function_exists('db_track_order_lookup')) {
        $trackingResults = db_track_order_lookup($query);
    } else {
        $orders = get_all_orders();
        foreach ($orders as $o) {
            if ($o['id'] === $query || ($o['customer']['phone'] ?? '') === $query) {
                $trackingResults[] = $o;
            }
        }
    }
}

// Per-Page SEO
$pageSeo = function_exists('db_get_page_seo') ? db_get_page_seo('track_order') : null;
$metaTitle = $pageSeo['meta_title'] ?? 'Live Order Tracking | Murli Creations Handcrafted Studio';
$metaDesc = $pageSeo['meta_description'] ?? 'Track the live handcrafting, packing, and courier dispatch status of your handcrafted Lippan Art order.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($metaTitle); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($metaDesc); ?>" />

  <link rel="stylesheet" href="/assets/css/globals.css" />
  <link rel="stylesheet" href="/assets/css/header.css" />
  <link rel="stylesheet" href="/assets/css/footer.css" />
  <link rel="stylesheet" href="/assets/css/product-card.css" />
  <link rel="stylesheet" href="/assets/css/cart-drawer.css" />
  <link rel="stylesheet" href="/assets/css/floating-widgets.css" />
  <link rel="stylesheet" href="/assets/css/toast.css" />

  <style>
    .track-hero {
      background: linear-gradient(135deg, #120e0b 0%, #1e1712 100%);
      color: #fff;
      padding: 120px 20px 60px;
      text-align: center;
      border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    }
    .track-title {
      font-size: 2.4rem;
      font-family: var(--font-serif, 'Cinzel', serif);
      color: #f7e7ce;
      margin-bottom: 12px;
    }
    .track-subtitle {
      color: #c5b8a5;
      font-size: 1rem;
      max-width: 600px;
      margin: 0 auto 30px;
    }
    .track-search-box {
      max-width: 580px;
      margin: 0 auto;
      display: flex;
      gap: 10px;
      background: rgba(255, 255, 255, 0.08);
      padding: 8px;
      border-radius: 50px;
      border: 1px solid rgba(212, 175, 55, 0.4);
      box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    }
    .track-input {
      flex: 1;
      background: transparent;
      border: none;
      padding: 12px 20px;
      color: #fff;
      font-size: 1rem;
      outline: none;
    }
    .track-input::placeholder { color: #8e8273; }
    .track-btn {
      background: linear-gradient(135deg, #d4af37, #b8860b);
      color: #120e0b;
      border: none;
      padding: 12px 28px;
      border-radius: 50px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .track-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
    }
    .track-results-wrap {
      max-width: 850px;
      margin: 40px auto 80px;
      padding: 0 20px;
    }
    .track-card {
      background: #fff;
      border: 1px solid #e7ded0;
      border-radius: 16px;
      padding: 30px;
      margin-bottom: 30px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .track-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 15px;
      border-bottom: 1px solid #f0e6d6;
      padding-bottom: 18px;
      margin-bottom: 24px;
    }
    .track-order-id {
      font-size: 1.3rem;
      font-weight: 700;
      color: #1b1510;
    }
    .track-badge {
      display: inline-block;
      padding: 6px 16px;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 700;
      text-transform: uppercase;
    }
    .status-shipped { background: #e3f2fd; color: #1976d2; }
    .status-processing { background: #fff8e1; color: #f57f17; }
    .status-delivered { background: #e8f5e9; color: #2e7d32; }
    .status-pending { background: #fbe9e7; color: #d84315; }

    /* Timeline Stepper */
    .timeline-steps {
      display: flex;
      justify-content: space-between;
      position: relative;
      margin: 35px 0 25px;
    }
    .timeline-steps::before {
      content: '';
      position: absolute;
      top: 18px;
      left: 30px;
      right: 30px;
      height: 4px;
      background: #e0d8cc;
      z-index: 1;
    }
    .step-item {
      position: relative;
      z-index: 2;
      text-align: center;
      flex: 1;
    }
    .step-bubble {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #e0d8cc;
      color: #777;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 10px;
      font-weight: bold;
      transition: all 0.3s;
    }
    .step-item.active .step-bubble {
      background: #d4af37;
      color: #1b1510;
      box-shadow: 0 0 0 5px rgba(212, 175, 55, 0.2);
    }
    .step-item.completed .step-bubble {
      background: #2e7d32;
      color: #fff;
    }
    .step-label {
      font-size: 0.85rem;
      font-weight: 600;
      color: #555;
    }

    .tracking-details-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      background: #fbf9f5;
      padding: 20px;
      border-radius: 12px;
      margin-top: 25px;
    }
    .track-detail-item label {
      font-size: 0.75rem;
      text-transform: uppercase;
      color: #887b6d;
      font-weight: 700;
      display: block;
      margin-bottom: 4px;
    }
    .track-detail-item span {
      font-size: 0.95rem;
      font-weight: 600;
      color: #1b1510;
    }
    .external-awb-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #1b1510;
      color: #d4af37;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.9rem;
      margin-top: 15px;
      transition: all 0.2s;
    }
    .external-awb-btn:hover {
      background: #33271d;
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="track-hero">
    <h1 class="track-title">Live Order & Shipment Tracking</h1>
    <p class="track-subtitle">Enter your Order ID (e.g. MURLI-XXXXXX) or registered 10-digit mobile number to view live handcrafting and courier dispatch status.</p>

    <form method="GET" action="/track-order.php" class="track-search-box">
      <input type="text" name="order" value="<?php echo htmlspecialchars($query); ?>" placeholder="Enter Order ID or Mobile Number..." class="track-input" required />
      <button type="submit" class="track-btn">Track Order</button>
    </form>
  </div>

  <div class="track-results-wrap">
    <?php if (!empty($query)): ?>
      <?php if (empty($trackingResults)): ?>
        <div class="track-card" style="text-align: center; padding: 50px 20px;">
          <div style="font-size: 3rem; margin-bottom: 15px;">🔍</div>
          <h3 style="color: #1b1510; margin-bottom: 10px;">No Active Order Found</h3>
          <p style="color: #777; max-width: 480px; margin: 0 auto 20px;">We could not locate any order with ID or Phone <strong>"<?php echo htmlspecialchars($query); ?>"</strong>. Please check your order confirmation email or WhatsApp message.</p>
          <a href="/contact.php" style="color: #b8860b; font-weight: 600; text-decoration: underline;">Need assistance? Contact our Delhi Studio</a>
        </div>
      <?php else: ?>
        <?php foreach ($trackingResults as $res): ?>
          <?php
            $oId = $res['id'];
            $st = $res['order_status'] ?? $res['orderStatus'] ?? 'Pending';
            $carrier = $res['tracking_carrier'] ?? $res['trackingCarrier'] ?? 'Shiprocket Express';
            $awb = $res['tracking_number'] ?? $res['trackingNumber'] ?? '';
            $tUrl = $res['tracking_url'] ?? $res['trackingUrl'] ?? '';
            $estDate = !empty($res['estimated_delivery']) ? date('d M Y', strtotime($res['estimated_delivery'])) : 'Within 4-6 business days';
            
            // Stepper logic
            $step = 1;
            if ($st === 'Confirmed' || $st === 'Processing') $step = 2;
            if ($st === 'Shipped') $step = 3;
            if ($st === 'Out for Delivery') $step = 4;
            if ($st === 'Delivered') $step = 5;
          ?>
          <div class="track-card">
            <div class="track-card-header">
              <div>
                <span style="font-size: 0.8rem; color: #888; text-transform: uppercase;">Order Number</span>
                <div class="track-order-id">#<?php echo htmlspecialchars($oId); ?></div>
              </div>
              <div>
                <span class="track-badge status-<?php echo strtolower(str_replace(' ', '-', $st)); ?>">
                  ● <?php echo htmlspecialchars($st); ?>
                </span>
              </div>
            </div>

            <!-- Visual Stepper -->
            <div class="timeline-steps">
              <div class="step-item <?php echo $step >= 1 ? ($step > 1 ? 'completed' : 'active') : ''; ?>">
                <div class="step-bubble">1</div>
                <div class="step-label">Booked</div>
              </div>
              <div class="step-item <?php echo $step >= 2 ? ($step > 2 ? 'completed' : 'active') : ''; ?>">
                <div class="step-bubble">2</div>
                <div class="step-label">Handcrafting</div>
              </div>
              <div class="step-item <?php echo $step >= 3 ? ($step > 3 ? 'completed' : 'active') : ''; ?>">
                <div class="step-bubble">3</div>
                <div class="step-label">Dispatched</div>
              </div>
              <div class="step-item <?php echo $step >= 4 ? ($step > 4 ? 'completed' : 'active') : ''; ?>">
                <div class="step-bubble">4</div>
                <div class="step-label">Out for Delivery</div>
              </div>
              <div class="step-item <?php echo $step >= 5 ? 'completed active' : ''; ?>">
                <div class="step-bubble">5</div>
                <div class="step-label">Delivered</div>
              </div>
            </div>

            <div class="tracking-details-grid">
              <div class="track-detail-item">
                <label>Courier Partner</label>
                <span><?php echo htmlspecialchars($carrier); ?></span>
              </div>
              <div class="track-detail-item">
                <label>Tracking / AWB</label>
                <span><?php echo !empty($awb) ? htmlspecialchars($awb) : 'Generating in Studio...'; ?></span>
              </div>
              <div class="track-detail-item">
                <label>Estimated Delivery</label>
                <span><?php echo htmlspecialchars($estDate); ?></span>
              </div>
              <div class="track-detail-item">
                <label>Destination City</label>
                <span><?php echo htmlspecialchars($res['customer_city'] ?? $res['customer']['city'] ?? 'India'); ?></span>
              </div>
            </div>

            <?php if (!empty($awb) && !empty($tUrl)): ?>
              <div style="text-align: right;">
                <a href="<?php echo htmlspecialchars($tUrl); ?>" target="_blank" class="external-awb-btn">
                  <span>Track Live on <?php echo htmlspecialchars($carrier); ?></span> &rarr;
                </a>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <?php include __DIR__ . '/includes/toast.php'; ?>
</body>
</html>

