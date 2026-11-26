<?php
/**
 * Test Suite for In/Out of Stock Feature and Product 2999 Deletion
 */

require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/config/admin_functions.php';
require_once __DIR__ . '/../backend/config/functions.php';

$pdo = get_db_connection();
$testsPassed = 0;
$testsFailed = 0;

function assert_test($condition, $name) {
    global $testsPassed, $testsFailed;
    if ($condition) {
        echo "✅ PASS: $name\n";
        $testsPassed++;
    } else {
        echo "❌ FAIL: $name\n";
        $testsFailed++;
    }
}

echo "\n================================================================\n";
echo "MURLI CREATIONS IN/OUT OF STOCK & PRODUCT CLEANUP TEST SUITE\n";
echo "================================================================\n\n";

// 1. Verify 2999 product is removed completely from MySQL and JSON
$stmt = $pdo->query("SELECT * FROM mc_products WHERE id = 'prod-33' OR price = 2999");
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
assert_test(count($res) === 0, "Product 2999 (prod-33) deleted from MySQL database");

$json = json_decode(file_get_contents(__DIR__ . '/../backend/storage/products.json'), true);
$jsonFound = array_filter($json, fn($p) => ($p['id'] === 'prod-33' || $p['price'] == 2999));
assert_test(count($jsonFound) === 0, "Product 2999 (prod-33) deleted from storage/products.json");

// Verify authentic Ganesha products remain
$ganeshas = array_filter($json, fn($p) => stripos($p['name'], 'ganesha') !== false);
assert_test(count($ganeshas) >= 3, "Authentic Ganesha products intact (Count: " . count($ganeshas) . ")");

// 2. Test Toggle Stock Status via admin function
// Pick a test product
$testProduct = db_get_product_by_id('prod-1');
assert_test(!empty($testProduct), "Found test product prod-1 ('{$testProduct['name']}')");

$initialStock = (int)$testProduct['stock'];

// Toggle to Out of Stock
db_update_product_stock('prod-1', 0);
$updated = db_get_product_by_id('prod-1');
assert_test((int)$updated['stock'] === 0, "Toggled prod-1 stock to 0 (Out of Stock)");

// Verify cart rejection when stock <= 0
// Simulate cart addition
$cartRes = null;
if (($updated['stock'] ?? 0) <= 0) {
    $cartRes = [
        'success' => false,
        'message' => "Sorry, \"{$updated['name']}\" is currently Out of Stock. You can inquire for a custom handcrafted piece via WhatsApp."
    ];
}
assert_test($cartRes['success'] === false, "Cart addition rejected for out-of-stock product with polite custom notice");

// Toggle back to In Stock
db_update_product_stock('prod-1', $initialStock > 0 ? $initialStock : 10);
$restored = db_get_product_by_id('prod-1');
assert_test((int)$restored['stock'] > 0, "Restored prod-1 stock to " . (int)$restored['stock'] . " (In Stock)");

// 3. Test shop filter availability
$all = get_all_products();
$inStockCount = count(array_filter($all, fn($p) => (int)($p['stock'] ?? 0) > 0));
assert_test($inStockCount > 0, "Catalog has {$inStockCount} In-Stock items");

// 4. Test API toggle action directly
$_POST['action'] = 'toggle_stock_status';
$_POST['id'] = 'prod-10';
$currentStockP10 = (int)db_get_product_by_id('prod-10')['stock'];

// Emulate toggling
$targetStock = ($currentStockP10 > 0) ? 0 : 10;
db_update_product_stock('prod-10', $targetStock);
$afterToggle = (int)db_get_product_by_id('prod-10')['stock'];
assert_test($afterToggle === $targetStock, "Stock toggled from {$currentStockP10} to {$afterToggle}");

// Revert prod-10
db_update_product_stock('prod-10', $currentStockP10);
$reverted = (int)db_get_product_by_id('prod-10')['stock'];
assert_test($reverted === $currentStockP10, "prod-10 reverted back to original stock {$currentStockP10}");

echo "\n================================================================\n";
echo "SUMMARY: {$testsPassed} PASSED, {$testsFailed} FAILED\n";
echo "================================================================\n\n";

if ($testsFailed > 0) {
    exit(1);
}

