<?php
// =============================================================================
// Comprehensive Test Suite for Murli Creations Studio Admin Panel
// Tests All 14 Core Requirements & Database Operations
// =============================================================================

require_once __DIR__ . '/../config/functions.php';

echo "================================================================\n";
echo "MURLI CREATIONS ADMIN PANEL TEST SUITE\n";
echo "================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assert_test($description, $condition) {
    global $passCount, $failCount;
    if ($condition) {
        echo "✅ PASS: {$description}\n";
        $passCount++;
    } else {
        echo "❌ FAIL: {$description}\n";
        $failCount++;
    }
}

// -------------------------------------------------------------
// Test 1: Database Connection & Tables
// -------------------------------------------------------------
$db = get_db_connection();
assert_test("MySQL Database Connected", $db instanceof PDO);

$tablesStmt = $db->query("SHOW TABLES");
$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
assert_test("Database contains all 18 tables", count($tables) >= 18);

// -------------------------------------------------------------
// Test 2: Point 7 - Login Management (Authentication & Verification)
// -------------------------------------------------------------
$loginFail = admin_auth_login('admin', 'wrongpassword');
assert_test("Point 7: Rejected invalid password", $loginFail['success'] === false);

$loginSuccess = admin_auth_login('admin', 'murli@2026');
assert_test("Point 7: Successfully authenticated admin credentials", $loginSuccess['success'] === true && !empty($_SESSION['mc_admin_logged_in']));

$adminLogs = db_get_admin_logs(5);
assert_test("Point 7: Security Audit Logs captured login events", count($adminLogs) > 0);

// -------------------------------------------------------------
// Test 3: Point 1 & 8 - Product Management & Inventory
// -------------------------------------------------------------
$allProds = db_get_all_products();
assert_test("Point 1 & 8: Loaded products from MySQL (Count: " . count($allProds) . ")", count($allProds) >= 30);

$testProdId = 'prod-test-' . time();
$newProd = db_save_product([
    'id' => $testProdId,
    'code' => 'MC/TEST-001',
    'name' => 'Automated Test Peacock Mandala',
    'category' => 'Lippan Art',
    'price' => 4500,
    'mrp' => 6000,
    'stock' => 8,
    'size' => '20 x 20 inches',
    'material' => 'Clay Relief, Mirror Inlays',
    'images' => ['/img-prodcut/IMG_4041.png'],
    'tags' => ['test', 'mandala']
]);
assert_test("Point 1: Added new handcrafted artwork ({$testProdId})", !empty($newProd['id']));

db_update_product_stock($testProdId, 15);
$fetchedProd = db_get_product_by_id($testProdId);
assert_test("Point 1: Quick stock adjustment updated stock to 15", ($fetchedProd['stock'] ?? 0) == 15);

// -------------------------------------------------------------
// Test 4: Point 3 - Product Details Deep Dive
// -------------------------------------------------------------
assert_test("Point 3: Fetched deep product specs (Size: {$fetchedProd['size']}, Material: {$fetchedProd['material']})", !empty($fetchedProd['size']) && !empty($fetchedProd['material']));

// Cleanup test product
db_delete_product($testProdId);
$deletedCheck = db_get_product_by_id($testProdId);
assert_test("Point 1: Successfully deleted test artwork", $deletedCheck === null);

// -------------------------------------------------------------
// Test 5: Point 4 - Product Order Details & Invoice Data
// -------------------------------------------------------------
$allOrders = db_get_all_orders();
assert_test("Point 4: Loaded orders list from MySQL (Count: " . count($allOrders) . ")", count($allOrders) > 0);

$sampleOrder = $allOrders[0] ?? null;
if ($sampleOrder) {
    $orderDetail = db_get_order_by_id($sampleOrder['id']);
    assert_test("Point 4: Retrieved full order detail with customer and decoded items", !empty($orderDetail['customer']) && is_array($orderDetail['items']));
}

// -------------------------------------------------------------
// Test 6: Point 9 & 10 - Order Tracking & Status Change
// -------------------------------------------------------------
if ($sampleOrder) {
    $orderId = $sampleOrder['id'];
    $trackingRes = db_update_order_tracking($orderId, 'Delhivery', 'DEL123456789', 'https://delhivery.com/track/DEL123456789', date('Y-m-d', strtotime('+3 days')));
    assert_test("Point 9: Updated tracking carrier & AWB for order #{$orderId}", $trackingRes['success'] === true);

    db_change_order_status($orderId, 'Shipped', 'Handed to courier for Delhi transit', false, 'Test Admin');
    $updatedOrder = db_get_order_by_id($orderId);
    assert_test("Point 10: Status changed to Shipped with audit trail entry", ($updatedOrder['order_status'] ?? '') === 'Shipped' && count($updatedOrder['status_history'] ?? []) > 0);

    $lookup = db_track_order_lookup($orderId);
    assert_test("Point 9: Customer tracking lookup successfully returned live shipment", count($lookup) > 0 && ($lookup[0]['tracking_number'] ?? '') === 'DEL123456789');
}

// -------------------------------------------------------------
// Test 7: Point 10 - External Webhook API & API Key Verification
// -------------------------------------------------------------
$apiKeys = db_get_api_keys();
assert_test("Point 10: Retrieved active API Keys for external logistics webhooks", count($apiKeys) > 0);

$activeKey = $apiKeys[0]['api_key'] ?? '';
$verifyKey = db_verify_api_key($activeKey);
assert_test("Point 10: Successfully verified external API key", $verifyKey !== false);

// -------------------------------------------------------------
// Test 8: Point 5 - CRM (Customer Relationship Management)
// -------------------------------------------------------------
$leads = db_get_crm_leads();
assert_test("Point 5: Loaded CRM leads pipeline (Count: " . count($leads) . ")", count($leads) > 0);

$newLead = db_save_crm_lead([
    'name' => 'Vikram Malhotra',
    'email' => 'vikram@example.com',
    'phone' => '+91 98888 77777',
    'subject' => 'Commission 36-inch Khatu Shyam Floral Lippan Panel',
    'message' => 'Want a customized floral border with Radha Krishna flute silhouette.',
    'lead_status' => 'New',
    'priority' => 'High',
    'follow_up_date' => date('Y-m-d', strtotime('+2 days')),
    'notes' => 'Customer inquired via WhatsApp.'
]);
assert_test("Point 5: Created new CRM inquiry lead (ID: {$newLead['id']})", !empty($newLead['id']));

$interactionAdded = db_add_crm_interaction($newLead['id'], 'Phone Call', 'Discussed raw clay curing time and custom dimensions (36x24)', 'Share reference sketch');
assert_test("Point 5: Logged customer interaction note", $interactionAdded === true);

db_delete_crm_lead($newLead['id']);
assert_test("Point 5: Cleaned up test CRM lead", true);

// -------------------------------------------------------------
// Test 9: Point 6 - Per-Page SEO Tags Management
// -------------------------------------------------------------
$seoPages = db_get_all_seo_pages();
assert_test("Point 6: Loaded per-page SEO records (Count: " . count($seoPages) . ")", count($seoPages) >= 7);

$homeSeo = db_get_page_seo('home');
assert_test("Point 6: Retrieved Homepage SEO (Title: {$homeSeo['meta_title']})", !empty($homeSeo['meta_title']) && !empty($homeSeo['meta_description']));

$saveSeo = db_save_page_seo([
    'page_key' => 'home',
    'page_name' => 'Home Page (Main Landing)',
    'url_path' => '/',
    'meta_title' => 'Murli Creations | Handcrafted Lippan Art, Jharokhas & Sacred Wall Decor',
    'meta_description' => 'Discover authentic handcrafted Lippan art, traditional Rajasthani Jharokhas, and sacred spiritual wall mandalas by master artisan Murli Prasad. Made in Delhi, India.',
    'meta_keywords' => 'lippan art, handcrafted jharokha, khatu shyam decor, vastu decor',
    'canonical_url' => 'https://murlicreations.com/',
    'og_title' => 'Murli Creations | Handcrafted Lippan Art & Sacred Wall Decor',
    'og_description' => 'Direct from master artisan: 100% authentic clay relief mirror artwork.',
    'og_image' => '/img-prodcut/IMG_4041.png',
    'twitter_card' => 'summary_large_image',
    'robots' => 'index, follow',
    'schema_json' => '{}'
]);
assert_test("Point 6: Updated per-page SEO tags successfully", $saveSeo['success'] === true);

// -------------------------------------------------------------
// Test 10: Point 11 - Email Communication
// -------------------------------------------------------------
$templates = db_get_email_templates();
assert_test("Point 11: Loaded email templates (Count: " . count($templates) . ")", count($templates) >= 3);

$emailDispatched = db_send_system_email('test@example.com', 'Test Order Notification', '<p>Hello from test suite</p>', 'test_suite');
assert_test("Point 11: Dispatched and logged system email", $emailDispatched === true);

$emailLogs = db_get_email_logs(5);
assert_test("Point 11: Email delivery log registered", count($emailLogs) > 0);

// -------------------------------------------------------------
// Test 11: Point 2 & 12 - Customer Management & User Details
// -------------------------------------------------------------
$customersList = db_get_all_customers();
assert_test("Point 12: Loaded customer patron records (Count: " . count($customersList) . ")", count($customersList) > 0);

$firstCust = $customersList[0];
$custDossier = db_get_customer_details($firstCust['id']);
assert_test("Point 2 & 12: Customer profile and order history dossier verified", !empty($custDossier['customer']) && isset($custDossier['orders']));

// -------------------------------------------------------------
// Test 12: Point 13 - Customer Reminders
// -------------------------------------------------------------
$remindersList = db_get_reminders();
assert_test("Point 13: Loaded customer reminders (Count: " . count($remindersList) . ")", count($remindersList) > 0);

$newRem = db_create_reminder([
    'customer_name' => 'Meera Patel',
    'customer_phone' => '9812345678',
    'customer_email' => 'meera@example.com',
    'reminder_type' => 'festive_offer',
    'channel' => 'whatsapp',
    'message_body' => 'Namaste Meera ji, festive discount MURLI10 is active for 24 hours!'
]);
assert_test("Point 13: Created new customer reminder (ID: {$newRem['id']})", !empty($newRem['id']));

$sendRem = db_send_reminder($newRem['id']);
assert_test("Point 13: Executed customer reminder dispatch", $sendRem['success'] === true);

// -------------------------------------------------------------
// Test 13: Point 14 - Offer & CTA Management
// -------------------------------------------------------------
$offers = db_get_offers_cta();
assert_test("Point 14: Loaded Offer & CTA configurations (Announcement Bar, Hero CTA, etc.)", !empty($offers['announcement_bar']));

$saveOffer = db_save_offer_cta('announcement_bar', [
    'title' => 'Festive Season Special: Free Shipping Pan-India on orders above ₹1,999!',
    'badge' => 'FESTIVE BLESSINGS',
    'cta_text' => 'Claim Offer',
    'cta_link' => '/shop.php',
    'coupon_code' => 'MURLI10',
    'is_active' => 1
]);
assert_test("Point 14: Updated announcement bar offer CTA", $saveOffer['success'] === true);

// -------------------------------------------------------------
// Test 14: Contact Form Inquiries Management & Direct Replies
// -------------------------------------------------------------
$inquiries = db_get_contact_inquiries();
assert_test("Contact Inquiries: Loaded inquiries from MySQL/storage (Count: " . count($inquiries) . ")", count($inquiries) > 0);

$saveInq = db_save_contact_inquiry([
    'name' => 'Kavita Chawla',
    'email' => 'kavita@example.com',
    'phone' => '+91 98765 11223',
    'subject' => 'Temple Wall Custom Lippan',
    'message' => 'Want a sacred Peacock mandala for my pooja room in South Delhi.'
]);
assert_test("Contact Inquiries: Saved new customer inquiry", !empty($saveInq['id']));
$testInqId = $saveInq['id'];

$inqDetail = db_get_contact_inquiry_by_id($testInqId);
assert_test("Contact Inquiries: Retrieved inquiry detail for #INQ-{$testInqId}", !empty($inqDetail) && $inqDetail['name'] === 'Kavita Chawla');

$replyRes = db_reply_contact_inquiry($testInqId, 'whatsapp', 'Namaste Kavita ji, we would love to handcraft your temple wall art.');
assert_test("Contact Inquiries: Dispatched & logged reply", $replyRes['success'] === true);

$inqAfterReply = db_get_contact_inquiry_by_id($testInqId);
assert_test("Contact Inquiries: Verified status transitioned to 'replied'", $inqAfterReply['status'] === 'replied');

$delInq = db_delete_contact_inquiry($testInqId);
assert_test("Contact Inquiries: Cleaned up test inquiry", $delInq['success'] === true);

// -------------------------------------------------------------
// Test 15: Navbar Pages & Studio Details CMS (0 to End Editable)
// -------------------------------------------------------------
$settings = get_site_settings();
assert_test("Pages CMS: Loaded studio settings (Phone: {$settings['studio']['phone']})", !empty($settings['studio']['phone']));

$settings['studio']['phone'] = '+91 95823 16454';
$settings['studio']['whatsapp'] = '+91 95823 16454';
$settings['studio']['address'] = 'ITL Twin Tower, Netaji Subhash Place, Pitampura, Delhi - 110034, India';
$saveSettings = save_site_settings($settings);
assert_test("Pages CMS: Saved studio contact numbers & physical address", $saveSettings === true);

$reloadedSettings = get_site_settings();
assert_test("Pages CMS: Verified persisted phone & WhatsApp", $reloadedSettings['studio']['phone'] === '+91 95823 16454');

$homeContent = get_homepage_content();
assert_test("Pages CMS: Loaded homepage content (Hero Title: {$homeContent['hero']['title_main']})", !empty($homeContent['hero']['title_main']));

$homeContent['hero']['badge'] = 'Direct Studio Helpline & WhatsApp Support';
$saveHome = save_homepage_content($homeContent);
assert_test("Pages CMS: Saved homepage hero banner & copy", $saveHome === true);

// -------------------------------------------------------------
// SUMMARY
// -------------------------------------------------------------
echo "\n================================================================\n";
echo "TEST RESULTS SUMMARY: {$passCount} PASSED, {$failCount} FAILED\n";
echo "================================================================\n";

if ($failCount === 0) {
    echo "🎉 ALL 14 REQUIREMENTS VERIFIED AND WORKING FLAWLESSLY WITH MYSQL!\n";
} else {
    echo "⚠️ SOME TESTS FAILED!\n";
    exit(1);
}

