<?php
// =============================================================================
// Murli Creations - Unified Admin REST / AJAX API Controller
// Handles all administrative actions for 14 operational points:
// 1. Product management   2. User details          3. Product details
// 4. Product order details 5. CRM                  6. SEO tags management
// 7. Login management     8. Catalog inventory     9. Order tracking
// 10. Status change & API 11. Email communication  12. Customer management
// 13. Customer reminders  14. Offer & CTA management
// =============================================================================

header('Content-Type: application/json');
require_once __DIR__ . '/../config/functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// -----------------------------------------------------------------------------
// 7. LOGIN MANAGEMENT (Authentication, Session & Password)
// -----------------------------------------------------------------------------

if ($action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please provide both username and password.']);
        exit;
    }

    $authResult = function_exists('admin_auth_login') ? admin_auth_login($username, $password) : ['success' => false];
    if ($authResult['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Access granted. Welcome to Murli Creations Studio Portal.',
            'admin' => $authResult['admin'] ?? ['username' => $username, 'role' => 'super_admin']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $authResult['message'] ?? 'Invalid username or password.'
        ]);
    }
    exit;
}

if ($action === 'logout') {
    if (function_exists('admin_auth_logout')) {
        admin_auth_logout();
    } else {
        $_SESSION['mc_admin_logged_in'] = false;
        unset($_SESSION['mc_admin_logged_in']);
    }
    echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
    exit;
}

// Ensure Admin Authentication for all protected actions below
if (empty($_SESSION['mc_admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please log in.']);
    exit;
}

// -----------------------------------------------------------------------------
// 1 & 8. PRODUCT MANAGEMENT (CRUD, Categories & Stock Control)
// -----------------------------------------------------------------------------

if ($action === 'get_products') {
    $search = trim($_GET['search'] ?? $_POST['search'] ?? '');
    $category = trim($_GET['category'] ?? $_POST['category'] ?? '');
    $stockFilter = trim($_GET['stock_filter'] ?? $_POST['stock_filter'] ?? '');
    $sort = trim($_GET['sort'] ?? $_POST['sort'] ?? 'newest');

    $products = function_exists('db_get_all_products') ? db_get_all_products($search, $category, $stockFilter, $sort) : get_all_products();
    echo json_encode(['success' => true, 'products' => $products]);
    exit;
}

if ($action === 'add_product' || $action === 'edit_product') {
    $isEdit = ($action === 'edit_product');
    $id = trim($_POST['id'] ?? '');
    if (!$isEdit && empty($id)) {
        $id = 'prod-' . round(microtime(true) * 1000);
    }

    $name = trim($_POST['name'] ?? '');
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Product Name is required.']);
        exit;
    }

    $code = trim($_POST['code'] ?? '');
    $category = trim($_POST['category'] ?? 'Lippan Art');
    $subcategory = trim($_POST['subcategory'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $mrp = !empty($_POST['mrp']) ? (float)$_POST['mrp'] : round($price * 1.3);
    $stock = max(0, (int)($_POST['stock'] ?? 0));
    $stockStatus = trim($_POST['stock_status'] ?? '');
    if ($stockStatus === 'out_of_stock') {
        $stock = 0;
    } elseif ($stockStatus === 'in_stock' && $stock <= 0) {
        $stock = 10;
    }
    $size = trim($_POST['size'] ?? '18 x 18 inches');
    $material = trim($_POST['material'] ?? 'Clay Relief Paste, Mirror Inlays, MDF');
    $origin = trim($_POST['origin'] ?? 'Delhi, India');
    $artist = trim($_POST['artist'] ?? 'Master Craftsman Murli Prasad');
    $rating = (float)($_POST['rating'] ?? 5.0);
    $reviewsCount = (int)($_POST['reviewsCount'] ?? 15);
    $video = trim($_POST['video'] ?? '');
    $folklore = trim($_POST['folklore'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $featured = isset($_POST['featured']) && ($_POST['featured'] === '1' || $_POST['featured'] === 'true' || $_POST['featured'] === 'on');
    $bestseller = isset($_POST['bestseller']) && ($_POST['bestseller'] === '1' || $_POST['bestseller'] === 'true' || $_POST['bestseller'] === 'on');

    $imagesRaw = trim($_POST['images'] ?? '');
    $images = array_values(array_filter(array_map('trim', explode(',', $imagesRaw))));
    if (empty($images)) {
        $images = ['/img-prodcut/IMG_4041.png'];
    }

    $tagsRaw = trim($_POST['tags'] ?? '');
    $tags = array_values(array_filter(array_map('trim', explode(',', $tagsRaw))));

    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) {
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
    }

    $productData = [
        'id' => $id,
        'code' => $code,
        'slug' => $slug,
        'name' => $name,
        'category' => $category,
        'subcategory' => $subcategory,
        'price' => $price,
        'mrp' => $mrp,
        'stock' => $stock,
        'size' => $size,
        'material' => $material,
        'origin' => $origin,
        'artist' => $artist,
        'rating' => $rating,
        'reviewsCount' => $reviewsCount,
        'video' => $video,
        'folklore' => $folklore,
        'description' => $description,
        'featured' => $featured,
        'bestseller' => $bestseller,
        'images' => $images,
        'tags' => $tags
    ];

    $saved = function_exists('db_save_product') ? db_save_product($productData) : false;
    if ($saved) {
        if (function_exists('db_log_admin_action')) {
            db_log_admin_action($isEdit ? 'EDIT_PRODUCT' : 'ADD_PRODUCT', "Product ID {$id}: {$name}");
        }
        echo json_encode([
            'success' => true,
            'message' => $isEdit ? "Product '{$name}' updated successfully!" : "Product '{$name}' created successfully!",
            'product' => $saved
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save product.']);
    }
    exit;
}

if ($action === 'delete_product') {
    $productId = trim($_POST['productId'] ?? $_POST['id'] ?? '');
    if (empty($productId)) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
        exit;
    }

    $deleted = function_exists('db_delete_product') ? db_delete_product($productId) : false;
    if ($deleted) {
        if (function_exists('db_log_admin_action')) {
            db_log_admin_action('DELETE_PRODUCT', "Deleted product ID: {$productId}");
        }
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete product.']);
    }
    exit;
}

if ($action === 'update_stock') {
    $id = trim($_POST['id'] ?? '');
    $stock = max(0, (int)($_POST['stock'] ?? 0));
    if (function_exists('db_update_product_stock')) {
        db_update_product_stock($id, $stock);
        if (function_exists('db_log_admin_action')) {
            db_log_admin_action('UPDATE_STOCK', "Updated stock for {$id} to {$stock}");
        }
        echo json_encode([
            'success' => true,
            'id' => $id,
            'stock' => $stock,
            'in_stock' => ($stock > 0),
            'message' => ($stock > 0) ? "Stock updated to {$stock} units (In Stock)." : "Product marked as Out of Stock (0 units)."
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Stock update failed.']);
    }
    exit;
}

if ($action === 'toggle_stock_status') {
    $id = trim($_POST['id'] ?? '');
    $product = function_exists('db_get_product_by_id') ? db_get_product_by_id($id) : null;
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }
    $currentStock = (int)($product['stock'] ?? 0);
    // If currently > 0, toggle to 0 (Out of Stock). If <= 0, toggle to 10 (In Stock).
    $newStock = ($currentStock > 0) ? 0 : 10;
    
    if (function_exists('db_update_product_stock')) {
        db_update_product_stock($id, $newStock);
        if (function_exists('db_log_admin_action')) {
            $stLabel = ($newStock > 0) ? 'IN_STOCK' : 'OUT_OF_STOCK';
            db_log_admin_action('TOGGLE_STOCK', "Toggled stock for {$id} to {$stLabel} ({$newStock} units)");
        }
        $statusMsg = ($newStock > 0) ? "{$product['name']} marked as In Stock (10 units)!" : "{$product['name']} marked as Out of Stock!";
        echo json_encode([
            'success' => true,
            'id' => $id,
            'stock' => $newStock,
            'in_stock' => ($newStock > 0),
            'message' => $statusMsg
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update stock status.']);
    }
    exit;
}

// -----------------------------------------------------------------------------
// 3. PRODUCT DETAILS (Deep Specifications & Media Inspector)
// -----------------------------------------------------------------------------

if ($action === 'get_product_detail') {
    $id = trim($_GET['id'] ?? $_POST['id'] ?? '');
    $product = function_exists('db_get_product_by_id') ? db_get_product_by_id($id) : null;
    if ($product) {
        // Calculate profit margins & discount
        $mrp = $product['mrp'] ?: round($product['price'] * 1.3);
        $discountPct = $mrp > 0 ? round((($mrp - $product['price']) / $mrp) * 100) : 0;
        $product['discountPercent'] = $discountPct;
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
    }
    exit;
}

// -----------------------------------------------------------------------------
// 4. PRODUCT ORDER DETAILS & INVOICE
// -----------------------------------------------------------------------------

if ($action === 'get_orders') {
    $search = trim($_GET['search'] ?? $_POST['search'] ?? '');
    $status = trim($_GET['status'] ?? $_POST['status'] ?? '');
    $paymentStatus = trim($_GET['payment_status'] ?? $_POST['payment_status'] ?? '');

    $orders = function_exists('db_get_all_orders') ? db_get_all_orders($search, $status, $paymentStatus) : get_all_orders();
    echo json_encode(['success' => true, 'orders' => $orders]);
    exit;
}

if ($action === 'get_order_detail') {
    $orderId = trim($_GET['orderId'] ?? $_POST['orderId'] ?? '');
    $order = function_exists('db_get_order_by_id') ? db_get_order_by_id($orderId) : get_order_by_id($orderId);
    if ($order) {
        echo json_encode(['success' => true, 'order' => $order]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found.']);
    }
    exit;
}

// -----------------------------------------------------------------------------
// 9 & 10. ORDER TRACKING & STATUS CHANGE MANAGEMENT
// -----------------------------------------------------------------------------

if ($action === 'update_order_status') {
    $orderId = trim($_POST['orderId'] ?? '');
    $status = trim($_POST['status'] ?? 'Pending');
    $comment = trim($_POST['comment'] ?? "Status updated manually to {$status}");
    $notifyCustomer = isset($_POST['notifyCustomer']) && ($_POST['notifyCustomer'] === '1' || $_POST['notifyCustomer'] === 'true' || $_POST['notifyCustomer'] === 'on');

    if (empty($orderId)) {
        echo json_encode(['success' => false, 'message' => 'Order ID is required.']);
        exit;
    }

    $updated = function_exists('db_change_order_status') ? db_change_order_status($orderId, $status, $comment, $notifyCustomer, $_SESSION['mc_admin_username'] ?? 'Admin') : false;
    if ($updated) {
        echo json_encode(['success' => true, 'message' => "Order #{$orderId} status updated to '{$status}'."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order status.']);
    }
    exit;
}

if ($action === 'update_order_tracking') {
    $orderId = trim($_POST['orderId'] ?? '');
    $carrier = trim($_POST['carrier'] ?? 'Shiprocket Express');
    $trackingNumber = trim($_POST['trackingNumber'] ?? '');
    $trackingUrl = trim($_POST['trackingUrl'] ?? '');
    $estimatedDelivery = !empty($_POST['estimatedDelivery']) ? trim($_POST['estimatedDelivery']) : null;

    if (empty($orderId) || empty($trackingNumber)) {
        echo json_encode(['success' => false, 'message' => 'Order ID and Tracking/AWB Number are required.']);
        exit;
    }

    $res = function_exists('db_update_order_tracking') ? db_update_order_tracking($orderId, $carrier, $trackingNumber, $trackingUrl, $estimatedDelivery) : ['success' => false];
    if ($res['success']) {
        echo json_encode([
            'success' => true,
            'message' => "Tracking updated: Carrier '{$carrier}' (AWB: {$trackingNumber})",
            'trackingUrl' => $res['trackingUrl']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update tracking.']);
    }
    exit;
}

if ($action === 'track_order_lookup') {
    $query = trim($_GET['query'] ?? $_POST['query'] ?? '');
    $results = function_exists('db_track_order_lookup') ? db_track_order_lookup($query) : [];
    echo json_encode(['success' => true, 'results' => $results]);
    exit;
}

// -----------------------------------------------------------------------------
// 10. EXTERNAL WEBHOOK & API KEYS MANAGEMENT
// -----------------------------------------------------------------------------

if ($action === 'get_api_keys') {
    $keys = function_exists('db_get_api_keys') ? db_get_api_keys() : [];
    $logs = function_exists('db_get_webhook_logs') ? db_get_webhook_logs(20) : [];
    echo json_encode(['success' => true, 'keys' => $keys, 'logs' => $logs]);
    exit;
}

if ($action === 'create_api_key') {
    $keyName = trim($_POST['key_name'] ?? 'Logistics Courier Integration');
    $service = trim($_POST['service'] ?? 'Shiprocket / Courier Webhook');
    $res = function_exists('db_create_api_key') ? db_create_api_key($keyName, $service) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'delete_api_key') {
    $id = (int)($_POST['id'] ?? 0);
    $deleted = function_exists('db_delete_api_key') ? db_delete_api_key($id) : false;
    echo json_encode(['success' => $deleted, 'message' => 'API Key deleted.']);
    exit;
}

// -----------------------------------------------------------------------------
// 2 & 12. USER DETAILS & CUSTOMER MANAGEMENT
// -----------------------------------------------------------------------------

if ($action === 'get_customers') {
    $search = trim($_GET['search'] ?? $_POST['search'] ?? '');
    $tier = trim($_GET['tier'] ?? $_POST['tier'] ?? '');
    $status = trim($_GET['status'] ?? $_POST['status'] ?? '');

    $customers = function_exists('db_get_all_customers') ? db_get_all_customers($search, $tier, $status) : [];
    echo json_encode(['success' => true, 'customers' => $customers]);
    exit;
}

if ($action === 'get_customer_detail') {
    $id = trim($_GET['id'] ?? $_POST['id'] ?? '');
    $details = function_exists('db_get_customer_details') ? db_get_customer_details($id) : null;
    if ($details && $details['customer']) {
        echo json_encode(['success' => true, 'data' => $details]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Customer profile not found.']);
    }
    exit;
}

if ($action === 'save_customer') {
    $res = function_exists('db_save_customer') ? db_save_customer($_POST) : ['success' => false];
    echo json_encode($res);
    exit;
}

// -----------------------------------------------------------------------------
// 5. CRM (CUSTOMER RELATIONSHIP MANAGEMENT) & INQUIRIES
// -----------------------------------------------------------------------------

if ($action === 'get_crm_leads') {
    $status = trim($_GET['status'] ?? $_POST['status'] ?? '');
    $priority = trim($_GET['priority'] ?? $_POST['priority'] ?? '');
    $search = trim($_GET['search'] ?? $_POST['search'] ?? '');

    $leads = function_exists('db_get_crm_leads') ? db_get_crm_leads($status, $priority, $search) : [];
    echo json_encode(['success' => true, 'leads' => $leads]);
    exit;
}

if ($action === 'save_crm_lead') {
    $res = function_exists('db_save_crm_lead') ? db_save_crm_lead($_POST) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'update_lead_status') {
    $id = (int)($_POST['id'] ?? 0);
    $status = trim($_POST['status'] ?? 'New');
    $notes = trim($_POST['notes'] ?? '');

    if ($id && function_exists('db_save_crm_lead')) {
        $lead = ['id' => $id, 'lead_status' => $status, 'notes' => $notes];
        // Fetch current lead to preserve fields
        $all = db_get_crm_leads();
        foreach ($all as $l) {
            if ($l['id'] == $id) {
                $lead = array_merge($l, $lead);
                break;
            }
        }
        $res = db_save_crm_lead($lead);
        echo json_encode(['success' => true, 'message' => "Lead #{$id} status updated to '{$status}'"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid lead ID']);
    }
    exit;
}

if ($action === 'delete_crm_lead') {
    $id = (int)($_POST['id'] ?? 0);
    $res = function_exists('db_delete_crm_lead') ? db_delete_crm_lead($id) : false;
    echo json_encode(['success' => $res, 'message' => 'Lead removed from CRM pipeline.']);
    exit;
}

if ($action === 'add_crm_interaction') {
    $leadId = (int)($_POST['lead_id'] ?? 0);
    $channel = trim($_POST['channel'] ?? 'Phone Call');
    $summary = trim($_POST['summary'] ?? '');
    $nextStep = trim($_POST['next_step'] ?? '');

    $res = function_exists('db_add_crm_interaction') ? db_add_crm_interaction($leadId, $channel, $summary, $nextStep, $_SESSION['mc_admin_username'] ?? 'Admin') : false;
    echo json_encode(['success' => $res, 'message' => 'Interaction logged.']);
    exit;
}

if ($action === 'get_crm_interactions') {
    $leadId = (int)($_GET['lead_id'] ?? $_POST['lead_id'] ?? 0);
    $interactions = function_exists('db_get_crm_interactions') ? db_get_crm_interactions($leadId) : [];
    echo json_encode(['success' => true, 'interactions' => $interactions]);
    exit;
}

// -----------------------------------------------------------------------------
// 6. SEO TAGS MANAGEMENT (PER-PAGE SEO TAG MANAGEMENT)
// -----------------------------------------------------------------------------

if ($action === 'get_seo_pages') {
    $pages = function_exists('db_get_all_seo_pages') ? db_get_all_seo_pages() : [];
    echo json_encode(['success' => true, 'pages' => $pages]);
    exit;
}

if ($action === 'get_page_seo') {
    $key = trim($_GET['page_key'] ?? $_POST['page_key'] ?? '');
    $seo = function_exists('db_get_page_seo') ? db_get_page_seo($key) : null;
    if ($seo) {
        echo json_encode(['success' => true, 'seo' => $seo]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Page SEO config not found.']);
    }
    exit;
}

if ($action === 'save_page_seo') {
    $res = function_exists('db_save_page_seo') ? db_save_page_seo($_POST) : ['success' => false];
    echo json_encode($res);
    exit;
}

// -----------------------------------------------------------------------------
// 11. EMAIL COMMUNICATION & TEMPLATES
// -----------------------------------------------------------------------------

if ($action === 'get_email_templates') {
    $templates = function_exists('db_get_email_templates') ? db_get_email_templates() : [];
    $logs = function_exists('db_get_email_logs') ? db_get_email_logs(20) : [];
    echo json_encode(['success' => true, 'templates' => $templates, 'logs' => $logs]);
    exit;
}

if ($action === 'save_email_template') {
    $res = function_exists('db_save_email_template') ? db_save_email_template($_POST) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'send_direct_email') {
    $to = trim($_POST['to'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($to) || empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Recipient email, subject, and message body are required.']);
        exit;
    }

    $bodyHtml = "<div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'><h2 style='color:#1b1510;'>Murli Creations Studio</h2><p>" . nl2br(htmlspecialchars($message)) . "</p><hr/><p style='font-size:12px; color:#777;'>Sent by Master Craftsman Murli Prasad Studio, Delhi</p></div>";
    $sent = function_exists('db_send_system_email') ? db_send_system_email($to, $subject, $bodyHtml, 'direct_composer') : false;

    echo json_encode(['success' => true, 'message' => "Email dispatched to {$to}!"]);
    exit;
}

// -----------------------------------------------------------------------------
// 13. CUSTOMER REMINDER MANAGEMENT
// -----------------------------------------------------------------------------

if ($action === 'get_reminders') {
    $reminders = function_exists('db_get_reminders') ? db_get_reminders() : [];
    echo json_encode(['success' => true, 'reminders' => $reminders]);
    exit;
}

if ($action === 'create_reminder') {
    $res = function_exists('db_create_reminder') ? db_create_reminder($_POST) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'send_reminder') {
    $id = (int)($_POST['id'] ?? 0);
    $res = function_exists('db_send_reminder') ? db_send_reminder($id) : ['success' => false];
    echo json_encode($res);
    exit;
}

// -----------------------------------------------------------------------------
// 14. OFFER & CTA MANAGEMENT (Coupons, Announcements, Banners)
// -----------------------------------------------------------------------------

if ($action === 'get_offers_cta') {
    $offers = function_exists('db_get_offers_cta') ? db_get_offers_cta() : [];
    $coupons = get_all_coupons();
    echo json_encode(['success' => true, 'offers' => $offers, 'coupons' => $coupons]);
    exit;
}

if ($action === 'save_offer_cta') {
    $sectionKey = trim($_POST['section_key'] ?? 'announcement_bar');
    $res = function_exists('db_save_offer_cta') ? db_save_offer_cta($sectionKey, $_POST) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'save_coupon') {
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $discountPercent = (float)($_POST['discountPercent'] ?? 10);
    $minOrder = (float)($_POST['minOrder'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $active = isset($_POST['active']) && ($_POST['active'] === '1' || $_POST['active'] === 'true' || $_POST['active'] === 'on');

    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Coupon code is required.']);
        exit;
    }

    // Save in MySQL if connected
    $db = get_db_connection();
    if ($db) {
        $stmt = $db->prepare("
            INSERT INTO mc_coupons (code, discount_type, discount_value, min_order, is_active, description)
            VALUES (?, 'percentage', ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE discount_value = VALUES(discount_value), min_order = VALUES(min_order), is_active = VALUES(is_active), description = VALUES(description)
        ");
        $stmt->execute([$code, $discountPercent, $minOrder, $active ? 1 : 0, $description]);
    }

    // Sync in coupons.json
    $coupons = get_all_coupons();
    $found = false;
    foreach ($coupons as &$c) {
        if (strtoupper($c['code']) === $code) {
            $c['discountPercent'] = $discountPercent;
            $c['minOrder'] = $minOrder;
            $c['description'] = $description;
            $c['active'] = $active;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $coupons[] = [
            'code' => $code,
            'discountPercent' => $discountPercent,
            'minOrder' => $minOrder,
            'description' => $description,
            'active' => $active
        ];
    }
    save_all_coupons($coupons);

    echo json_encode(['success' => true, 'message' => "Coupon '{$code}' saved successfully!"]);
    exit;
}

if ($action === 'delete_coupon') {
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $db = get_db_connection();
    if ($db) {
        $stmt = $db->prepare("DELETE FROM mc_coupons WHERE code = ?");
        $stmt->execute([$code]);
    }
    $coupons = get_all_coupons();
    $coupons = array_values(array_filter($coupons, fn($c) => strtoupper($c['code']) !== $code));
    save_all_coupons($coupons);
    echo json_encode(['success' => true, 'message' => "Coupon '{$code}' deleted."]);
    exit;
}

// -----------------------------------------------------------------------------
// TEAM ADMIN USERS & PASSWORD CHANGE
// -----------------------------------------------------------------------------

if ($action === 'get_team_users') {
    $users = function_exists('db_get_admin_users') ? db_get_admin_users() : [];
    $logs = function_exists('db_get_admin_logs') ? db_get_admin_logs(25) : [];
    echo json_encode(['success' => true, 'users' => $users, 'logs' => $logs]);
    exit;
}

if ($action === 'save_admin_user') {
    $res = function_exists('db_save_admin_user') ? db_save_admin_user($_POST) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'delete_admin_user') {
    $targetId = (int)($_POST['id'] ?? 0);
    $currentAdminId = (int)($_SESSION['mc_admin_id'] ?? 1);
    if ($targetId === $currentAdminId || $targetId === 1) {
        echo json_encode(['success' => false, 'message' => 'Primary Super Admin account cannot be deleted.']);
        exit;
    }
    $res = function_exists('db_delete_admin_user') ? db_delete_admin_user($targetId) : ['success' => false, 'message' => 'Failed to delete.'];
    echo json_encode($res);
    exit;
}

if ($action === 'superadmin_reset_password') {
    $targetId = (int)($_POST['user_id'] ?? 0);
    $newPass = trim($_POST['new_password'] ?? '');
    if (empty($targetId) || empty($newPass)) {
        echo json_encode(['success' => false, 'message' => 'Target user ID and new password are required.']);
        exit;
    }
    if (strlen($newPass) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        exit;
    }
    $res = function_exists('db_superadmin_reset_password') ? db_superadmin_reset_password($targetId, $newPass) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'toggle_admin_user_status') {
    $targetId = (int)($_POST['id'] ?? 0);
    $currentAdminId = (int)($_SESSION['mc_admin_id'] ?? 1);
    if ($targetId === $currentAdminId || $targetId === 1) {
        echo json_encode(['success' => false, 'message' => 'Cannot deactivate the primary Super Admin account.']);
        exit;
    }
    $res = function_exists('db_toggle_admin_user_status') ? db_toggle_admin_user_status($targetId) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'toggle_customer_status') {
    $customerId = (int)($_POST['id'] ?? 0);
    $res = function_exists('db_toggle_customer_status') ? db_toggle_customer_status($customerId) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'get_login_activity_logs') {
    $logs = function_exists('db_get_login_audit_trail') ? db_get_login_audit_trail(60) : [];
    echo json_encode(['success' => true, 'logs' => $logs]);
    exit;
}

if ($action === 'change_password') {
    $currentPass = trim($_POST['current_password'] ?? '');
    $newPass = trim($_POST['new_password'] ?? '');
    $adminId = $_SESSION['mc_admin_id'] ?? 1;

    if (empty($currentPass) || empty($newPass)) {
        echo json_encode(['success' => false, 'message' => 'Please enter both current and new passwords.']);
        exit;
    }

    $res = function_exists('db_change_admin_password') ? db_change_admin_password($adminId, $currentPass, $newPass) : ['success' => false];
    echo json_encode($res);
    exit;
}

// -----------------------------------------------------------------------------
// DATABASE RE-SYNC & MIGRATION WIZARD
// -----------------------------------------------------------------------------

if ($action === 'run_db_migration') {
    require_once __DIR__ . '/../config/db_init.php';
    $res = run_database_migrations(true);
    echo json_encode($res);
    exit;
}

// -----------------------------------------------------------------------------
// CONTACT FORM INQUIRIES & REPLIES
// -----------------------------------------------------------------------------

if ($action === 'get_contact_inquiries') {
    $status = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';
    $inquiries = function_exists('db_get_contact_inquiries') ? db_get_contact_inquiries($status, $search) : [];
    echo json_encode(['success' => true, 'inquiries' => $inquiries]);
    exit;
}

if ($action === 'get_contact_inquiry_detail') {
    $id = $_GET['id'] ?? '';
    $inq = function_exists('db_get_contact_inquiry_by_id') ? db_get_contact_inquiry_by_id($id) : null;
    if ($inq) {
        echo json_encode(['success' => true, 'inquiry' => $inq]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Inquiry not found.']);
    }
    exit;
}

if ($action === 'update_contact_inquiry_status') {
    $id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? 'read';
    $notes = $_POST['notes'] ?? '';
    $res = function_exists('db_update_contact_inquiry_status') ? db_update_contact_inquiry_status($id, $status, $notes) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'reply_contact_inquiry') {
    $id = $_POST['id'] ?? '';
    $channel = $_POST['reply_channel'] ?? 'whatsapp';
    $message = trim($_POST['reply_message'] ?? '');
    $repliedBy = $_SESSION['mc_admin_username'] ?? 'Master Artisan Murli Prasad';

    if (empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a reply message.']);
        exit;
    }

    $res = function_exists('db_reply_contact_inquiry') 
        ? db_reply_contact_inquiry($id, $channel, $message, $repliedBy)
        : ['success' => false, 'message' => 'Reply failed.'];
    echo json_encode($res);
    exit;
}

if ($action === 'delete_contact_inquiry') {
    $id = $_POST['id'] ?? '';
    $res = function_exists('db_delete_contact_inquiry') ? db_delete_contact_inquiry($id) : ['success' => false];
    echo json_encode($res);
    exit;
}

// -----------------------------------------------------------------------------
// SITE SETTINGS & NAVBAR PAGES CONTENT (Full Dynamic CMS)
// -----------------------------------------------------------------------------

if ($action === 'save_settings') {
    $settings = get_site_settings();
    
    // Announcement Bar
    if (isset($_POST['announcement_text'])) $settings['announcement']['text'] = trim($_POST['announcement_text']);
    if (isset($_POST['announcement_coupon'])) $settings['announcement']['coupon_code'] = strtoupper(trim($_POST['announcement_coupon']));
    if (isset($_POST['announcement_badge'])) $settings['announcement']['badge'] = trim($_POST['announcement_badge']);
    if (isset($_POST['announcement_link'])) $settings['announcement']['link'] = trim($_POST['announcement_link']);
    if (isset($_POST['announcement_enabled'])) {
        $settings['announcement']['enabled'] = ($_POST['announcement_enabled'] === '1' || $_POST['announcement_enabled'] === 'true' || $_POST['announcement_enabled'] === 'on');
    }

    // Studio & Contact Page Info
    if (isset($_POST['studio_name'])) $settings['studio']['name'] = trim($_POST['studio_name']);
    if (isset($_POST['studio_tagline'])) $settings['studio']['tagline'] = trim($_POST['studio_tagline']);
    if (isset($_POST['studio_founder'])) $settings['studio']['founder'] = trim($_POST['studio_founder']);
    if (isset($_POST['studio_phone'])) $settings['studio']['phone'] = trim($_POST['studio_phone']);
    if (isset($_POST['studio_whatsapp'])) $settings['studio']['whatsapp'] = trim($_POST['studio_whatsapp']);
    if (isset($_POST['studio_email'])) $settings['studio']['email'] = trim($_POST['studio_email']);
    if (isset($_POST['studio_address'])) $settings['studio']['address'] = trim($_POST['studio_address']);
    if (isset($_POST['studio_hours'])) $settings['studio']['hours'] = trim($_POST['studio_hours']);
    if (isset($_POST['studio_location'])) $settings['studio']['location'] = trim($_POST['studio_location']);

    // Shipping & Delivery Policy
    if (isset($_POST['shipping_free_threshold'])) $settings['shipping']['free_threshold'] = (float)$_POST['shipping_free_threshold'];
    if (isset($_POST['shipping_standard_charge'])) $settings['shipping']['standard_charge'] = (float)$_POST['shipping_standard_charge'];
    if (isset($_POST['shipping_dispatch_time'])) $settings['shipping']['dispatch_time'] = trim($_POST['shipping_dispatch_time']);
    if (isset($_POST['shipping_transit_safe_note'])) $settings['shipping']['transit_safe_note'] = trim($_POST['shipping_transit_safe_note']);
    if (isset($_POST['shipping_serviceable_area'])) $settings['shipping']['serviceable_area'] = trim($_POST['shipping_serviceable_area']);

    // Social Links
    if (isset($_POST['social_instagram'])) $settings['social']['instagram'] = trim($_POST['social_instagram']);
    if (isset($_POST['social_facebook'])) $settings['social']['facebook'] = trim($_POST['social_facebook']);
    if (isset($_POST['social_whatsapp'])) $settings['social']['whatsapp'] = trim($_POST['social_whatsapp']);
    if (isset($_POST['social_youtube'])) $settings['social']['youtube'] = trim($_POST['social_youtube']);
    if (isset($_POST['social_linkedin'])) $settings['social']['linkedin'] = trim($_POST['social_linkedin']);
    if (isset($_POST['social_pinterest'])) $settings['social']['pinterest'] = trim($_POST['social_pinterest']);
    if (isset($_POST['social_linktree'])) $settings['social']['linktree'] = trim($_POST['social_linktree']);

    // Footer
    if (isset($_POST['footer_about_text'])) $settings['footer']['about_text'] = trim($_POST['footer_about_text']);
    if (isset($_POST['footer_copyright'])) $settings['footer']['copyright'] = trim($_POST['footer_copyright']);

    // Contact & Home Page FAQs (JSON array)
    if (isset($_POST['faqs_json'])) {
        $decodedFaqs = json_decode($_POST['faqs_json'], true);
        if (is_array($decodedFaqs)) {
            $normalizedFaqs = [];
            foreach ($decodedFaqs as $fq) {
                $qText = trim($fq['question'] ?? $fq['q'] ?? '');
                $aText = trim($fq['answer'] ?? $fq['a'] ?? '');
                if (!empty($qText) && !empty($aText)) {
                    $normalizedFaqs[] = [
                        'question' => $qText,
                        'answer' => $aText,
                        'q' => $qText,
                        'a' => $aText
                    ];
                }
            }
            $settings['faqs'] = $normalizedFaqs;

            // Synchronize with homepage_content so both index.php & contact.php are 100% updated
            $hpContent = get_homepage_content();
            $hpContent['faqs'] = $normalizedFaqs;
            save_homepage_content($hpContent);
        }
    }

    $saved = save_site_settings($settings);
    if (function_exists('db_log_admin_action')) {
        db_log_admin_action('Updated Site Settings', 'Modified studio contact details & page settings');
    }
    echo json_encode(['success' => $saved, 'message' => 'Studio details and page settings saved successfully!']);
    exit;
}

if ($action === 'save_homepage_content') {
    $content = get_homepage_content();

    // Hero Section
    if (isset($_POST['hero_badge'])) $content['hero']['badge'] = trim($_POST['hero_badge']);
    if (isset($_POST['hero_title_main'])) $content['hero']['title_main'] = trim($_POST['hero_title_main']);
    if (isset($_POST['hero_title_highlight'])) $content['hero']['title_highlight'] = trim($_POST['hero_title_highlight']);
    if (isset($_POST['hero_description'])) $content['hero']['description'] = trim($_POST['hero_description']);
    if (isset($_POST['hero_cta_primary_text'])) $content['hero']['cta_primary_text'] = trim($_POST['hero_cta_primary_text']);
    if (isset($_POST['hero_cta_primary_link'])) $content['hero']['cta_primary_link'] = trim($_POST['hero_cta_primary_link']);
    if (isset($_POST['hero_cta_secondary_text'])) $content['hero']['cta_secondary_text'] = trim($_POST['hero_cta_secondary_text']);
    if (isset($_POST['hero_cta_secondary_link'])) $content['hero']['cta_secondary_link'] = trim($_POST['hero_cta_secondary_link']);

    // Story Section
    if (isset($_POST['story_badge'])) $content['story']['badge'] = trim($_POST['story_badge']);
    if (isset($_POST['story_title'])) $content['story']['title'] = trim($_POST['story_title']);
    if (isset($_POST['story_quote'])) $content['story']['quote'] = trim($_POST['story_quote']);
    if (isset($_POST['story_author'])) $content['story']['author'] = trim($_POST['story_author']);

    // Custom Commission Section
    if (isset($_POST['custom_commission_title'])) $content['custom_commission']['title'] = trim($_POST['custom_commission_title']);
    if (isset($_POST['custom_commission_desc'])) $content['custom_commission']['description'] = trim($_POST['custom_commission_desc']);
    if (isset($_POST['custom_commission_cta'])) $content['custom_commission']['cta_text'] = trim($_POST['custom_commission_cta']);

    // Festive Special Offer Section
    if (isset($_POST['has_festive_offer_form'])) {
        $content['festive_offer']['enabled'] = !empty($_POST['festive_offer_enabled']);
    }
    if (isset($_POST['festive_offer_badge'])) $content['festive_offer']['badge'] = trim($_POST['festive_offer_badge']);
    if (isset($_POST['festive_offer_title'])) $content['festive_offer']['title'] = trim($_POST['festive_offer_title']);
    if (isset($_POST['festive_offer_description'])) $content['festive_offer']['description'] = trim($_POST['festive_offer_description']);
    if (isset($_POST['festive_offer_coupon_code'])) $content['festive_offer']['coupon_code'] = strtoupper(trim($_POST['festive_offer_coupon_code']));
    if (isset($_POST['festive_offer_discount_label'])) $content['festive_offer']['discount_label'] = trim($_POST['festive_offer_discount_label']);
    if (isset($_POST['festive_offer_button_text'])) $content['festive_offer']['button_text'] = trim($_POST['festive_offer_button_text']);
    if (isset($_POST['festive_offer_button_link'])) $content['festive_offer']['button_link'] = trim($_POST['festive_offer_button_link']);

    $saved = save_homepage_content($content);
    if (function_exists('db_log_admin_action')) {
        db_log_admin_action('Updated Homepage Content', 'Modified hero, story quote, and custom commission copy');
    }
    echo json_encode(['success' => $saved, 'message' => 'Homepage content and navbar copy updated successfully!']);
    exit;
}

if ($action === 'save_review') {
    $id = $_POST['id'] ?? ('rev-' . round(microtime(true) * 1000));
    $productId = $_POST['productId'] ?? 'prod-1';
    $name = trim($_POST['name'] ?? 'Verified Patron');
    $location = trim($_POST['location'] ?? 'India');
    $rating = (float)($_POST['rating'] ?? 5.0);
    $comment = trim($_POST['comment'] ?? '');

    $db = get_db_connection();
    if ($db) {
        $stmt = $db->prepare("INSERT INTO mc_reviews (id, product_id, name, location, rating, comment, date_text) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)");
        $stmt->execute([$id, $productId, $name, $location, $rating, $comment, date('d M Y')]);
    }

    $reviews = get_all_reviews();
    $found = false;
    foreach ($reviews as &$r) {
        if ($r['id'] === $id) {
            $r['productId'] = $productId;
            $r['name'] = $name;
            $r['location'] = $location;
            $r['rating'] = $rating;
            $r['comment'] = $comment;
            $found = true;
            break;
        }
    }
    if (!$found) {
        array_unshift($reviews, [
            'id' => $id,
            'productId' => $productId,
            'name' => $name,
            'location' => $location,
            'rating' => $rating,
            'comment' => $comment,
            'date' => date('d M Y')
        ]);
    }
    save_all_reviews($reviews);
    echo json_encode(['success' => true, 'message' => 'Review saved successfully!']);
    exit;
}

if ($action === 'delete_review') {
    $id = $_POST['id'] ?? '';
    $db = get_db_connection();
    if ($db) {
        $stmt = $db->prepare("DELETE FROM mc_reviews WHERE id = ?");
        $stmt->execute([$id]);
    }
    $reviews = get_all_reviews();
    $reviews = array_values(array_filter($reviews, fn($r) => $r['id'] !== $id));
    save_all_reviews($reviews);
    echo json_encode(['success' => true, 'message' => 'Review deleted.']);
    exit;
}

// -----------------------------------------------------------------------------
// EXPANDED ADMIN ACTIONS (Reviews, Bulk Orders, CSV, Media, Analytics)
// -----------------------------------------------------------------------------

if ($action === 'get_reviews') {
    $status = $_GET['status'] ?? 'all';
    $productId = $_GET['product_id'] ?? '';
    $reviews = function_exists('db_get_all_admin_reviews') ? db_get_all_admin_reviews($status, $productId) : get_all_reviews();
    echo json_encode(['success' => true, 'reviews' => $reviews]);
    exit;
}

if ($action === 'toggle_review_status') {
    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Review ID required.']);
        exit;
    }
    $res = function_exists('db_toggle_review_status') ? db_toggle_review_status($id) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'bulk_update_order_status') {
    $orderIds = $_POST['order_ids'] ?? [];
    $newStatus = trim($_POST['status'] ?? '');
    if (is_string($orderIds)) {
        $orderIds = json_decode($orderIds, true) ?: [];
    }
    if (empty($orderIds) || empty($newStatus)) {
        echo json_encode(['success' => false, 'message' => 'Please select orders and a status.']);
        exit;
    }
    $res = function_exists('db_bulk_update_orders_status') ? db_bulk_update_orders_status($orderIds, $newStatus) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'export_products_csv') {
    $csv = function_exists('db_export_products_csv') ? db_export_products_csv() : '';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="murli_creations_catalog_' . date('Y-m-d') . '.csv"');
    echo $csv;
    exit;
}

if ($action === 'import_products_csv') {
    $csvContent = '';
    if (isset($_FILES['csv_file']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
        $csvContent = file_get_contents($_FILES['csv_file']['tmp_name']);
    } elseif (!empty($_POST['csv_text'])) {
        $csvContent = $_POST['csv_text'];
    }
    if (empty($csvContent)) {
        echo json_encode(['success' => false, 'message' => 'Please upload a CSV file or paste CSV content.']);
        exit;
    }
    $res = function_exists('db_import_products_csv') ? db_import_products_csv($csvContent) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'apply_bulk_discount') {
    $category = $_POST['category'] ?? 'all';
    $subcategory = $_POST['subcategory'] ?? 'all';
    $discount = (float)($_POST['discount'] ?? 10);
    $res = function_exists('db_apply_bulk_discount') ? db_apply_bulk_discount($category, $discount, $subcategory) : ['success' => false];
    echo json_encode($res);
    exit;
}

if ($action === 'get_abandoned_carts') {
    $carts = function_exists('db_get_abandoned_carts') ? db_get_abandoned_carts() : [];
    echo json_encode(['success' => true, 'abandoned_carts' => $carts]);
    exit;
}

if ($action === 'list_media_assets') {
    $assets = function_exists('db_list_media_assets') ? db_list_media_assets() : [];
    echo json_encode(['success' => true, 'assets' => $assets]);
    exit;
}

if ($action === 'get_analytics_data') {
    $analytics = function_exists('db_get_analytics_summary') ? db_get_analytics_summary() : [];
    echo json_encode(['success' => true, 'analytics' => $analytics]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action: ' . htmlspecialchars($action)]);
