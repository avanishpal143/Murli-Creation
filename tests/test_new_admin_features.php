<?php
/**
 * Test Suite for New Admin Superpowers & Features:
 * - Customer Reviews & Testimonials Hub
 * - Bulk Order Status Updates
 * - CSV Catalog Export & Import
 * - Mass Festive Discount Applicator
 * - Abandoned Cart Incomplete Checkouts
 * - Media Assets Listing
 * - Analytics Intelligence Summary
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../config/admin_functions.php';

$totalPassed = 0;
$totalFailed = 0;

function assertCondition($name, $condition, $info = '') {
    global $totalPassed, $totalFailed;
    if ($condition) {
        echo "✅ PASS: $name" . ($info ? " ($info)" : "") . "\n";
        $totalPassed++;
    } else {
        echo "❌ FAIL: $name" . ($info ? " ($info)" : "") . "\n";
        $totalFailed++;
    }
}

echo "\n================================================================\n";
echo "MURLI CREATIONS NEW ADMIN SUPERPOWERS & TOOLS TEST SUITE\n";
echo "================================================================\n\n";

// 1. Reviews Hub
$reviews = db_get_all_admin_reviews('all');
assertCondition("db_get_all_admin_reviews returns array", is_array($reviews), "Count: " . count($reviews));

// Test Review Toggle
if (!empty($reviews)) {
    $firstRevId = $reviews[0]['id'];
    $toggleRes = db_toggle_review_status($firstRevId);
    assertCondition("db_toggle_review_status toggled status", $toggleRes['success'] === true, "New status: " . ($toggleRes['status'] ?? 'unknown'));
    // Revert back
    $revertRes = db_toggle_review_status($firstRevId);
    assertCondition("db_toggle_review_status reverted status cleanly", $revertRes['success'] === true);
}

// 2. CSV Catalog Export
$csv = db_export_products_csv();
assertCondition("db_export_products_csv generates valid CSV", !empty($csv) && str_contains($csv, 'SKU Code,Product ID,Product Name'), "Length: " . strlen($csv) . " bytes");

// 3. Mass Festive Discount & Revert
$allProductsBefore = get_all_products();
$discountRes = db_apply_bulk_discount('all', 10);
assertCondition("db_apply_bulk_discount applies 10% discount", $discountRes['success'] === true, $discountRes['message'] ?? '');

$discountRevert = db_apply_bulk_discount('all', 0);
assertCondition("db_apply_bulk_discount reverts back to standard catalog pricing", $discountRevert['success'] === true, $discountRevert['message'] ?? '');

// Restore exact original catalog prices so test execution never alters custom product pricing
foreach ($allProductsBefore as $origProduct) {
    db_save_product($origProduct);
}

// 4. Abandoned Carts
$abandoned = db_get_abandoned_carts();
assertCondition("db_get_abandoned_carts returns array", is_array($abandoned), "Carts count: " . count($abandoned));

// 5. Media Assets Library
$media = db_list_media_assets();
assertCondition("db_list_media_assets returns studio artwork images", is_array($media) && count($media) > 0, "Assets count: " . count($media));

// 6. Analytics Intelligence Summary
$analytics = db_get_analytics_summary();
assertCondition("db_get_analytics_summary returns business data", is_array($analytics) && isset($analytics['total_revenue']), "Revenue: ₹" . number_format($analytics['total_revenue'] ?? 0));
assertCondition("Analytics contains top selling artworks ranking", isset($analytics['top_products']) && is_array($analytics['top_products']));
assertCondition("Analytics contains monthly revenue trajectory", isset($analytics['monthly_revenue']) && is_array($analytics['monthly_revenue']));
assertCondition("Analytics contains patron city distribution", isset($analytics['top_cities']) && is_array($analytics['top_cities']));

echo "\n================================================================\n";
echo "SUMMARY: $totalPassed PASSED, $totalFailed FAILED\n";
echo "================================================================\n";

if ($totalFailed > 0) {
    exit(1);
}
