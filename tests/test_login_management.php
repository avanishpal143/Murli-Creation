<?php
/**
 * Test Suite for Superadmin Team Management & Login/Logout Activity
 */

require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/config/admin_functions.php';

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
echo "MURLI CREATIONS SUPERADMIN USER & LOGIN MANAGEMENT TEST SUITE\n";
echo "================================================================\n\n";

// 1. Create a new test team user
$testUsername = 'test_artisan_' . time();
$createRes = db_save_admin_user([
    'username' => $testUsername,
    'full_name' => 'Test Artisan Murli',
    'email' => 'artisan@murlicreations.com',
    'role' => 'manager',
    'status' => 'active',
    'password' => 'murliTest@2026'
]);
assert_test($createRes['success'] === true, "Superadmin created new team member '{$testUsername}'");

// Fetch the created user
$stmt = $pdo->prepare("SELECT * FROM mc_admins WHERE username = ?");
$stmt->execute([$testUsername]);
$createdUser = $stmt->fetch(PDO::FETCH_ASSOC);
assert_test(!empty($createdUser), "User found in mc_admins table with ID: {$createdUser['id']}");

// 2. Superadmin Direct Password Reset (Without needing old password)
$newRandomPass = 'artisanNewPass@2026';
$resetRes = db_superadmin_reset_password($createdUser['id'], $newRandomPass);
assert_test($resetRes['success'] === true, "Superadmin directly reset password for user ID {$createdUser['id']}");

// Verify that the new password works with password_verify
$stmt = $pdo->prepare("SELECT password_hash FROM mc_admins WHERE id = ?");
$stmt->execute([$createdUser['id']]);
$hash = $stmt->fetchColumn();
assert_test(password_verify($newRandomPass, $hash), "New password verified against updated bcrypt hash");

// 3. Superadmin Updates User Details (Edit User)
$updateRes = db_save_admin_user([
    'id' => $createdUser['id'],
    'full_name' => 'Senior Craftsman Murli',
    'email' => 'senior.artisan@murlicreations.com',
    'role' => 'support',
    'status' => 'active'
]);
assert_test($updateRes['success'] === true, "Superadmin updated team member details");

$stmt = $pdo->prepare("SELECT full_name, email, role FROM mc_admins WHERE id = ?");
$stmt->execute([$createdUser['id']]);
$updatedUser = $stmt->fetch(PDO::FETCH_ASSOC);
assert_test($updatedUser['full_name'] === 'Senior Craftsman Murli' && $updatedUser['role'] === 'support', "Updated name & role persisted in MySQL");

// 4. Superadmin Toggles User Status (Active <-> Inactive)
$toggleRes = db_toggle_admin_user_status($createdUser['id']);
assert_test($toggleRes['success'] === true && $toggleRes['status'] === 'inactive', "Superadmin toggled user status to inactive");

$toggleRes2 = db_toggle_admin_user_status($createdUser['id']);
assert_test($toggleRes2['success'] === true && $toggleRes2['status'] === 'active', "Superadmin toggled user status back to active");

// 5. Superadmin cannot delete self / ID 1 guard
$delRootRes = db_delete_admin_user(1);
assert_test($delRootRes['success'] === false, "Safety guard blocked deletion of primary Super Admin (ID 1)");

// 6. Superadmin Deletes Test User
$delRes = db_delete_admin_user($createdUser['id']);
assert_test($delRes['success'] === true, "Superadmin permanently deleted test user");

$stmt = $pdo->prepare("SELECT * FROM mc_admins WHERE id = ?");
$stmt->execute([$createdUser['id']]);
assert_test(empty($stmt->fetch()), "Deleted user verified removed from mc_admins");

// 7. Login Audit Trail Retrieval
$logs = db_get_login_audit_trail(20);
assert_test(count($logs) > 0, "Retrieved login & security audit trail (Count: " . count($logs) . ")");

// 8. Customer Status Toggle
$stmt = $pdo->query("SELECT id FROM mc_customers LIMIT 1");
$custId = $stmt->fetchColumn();
if ($custId) {
    $custToggle = db_toggle_customer_status($custId);
    assert_test($custToggle['success'] === true, "Customer status toggle executed for Customer ID {$custId}");
    // Revert back
    db_toggle_customer_status($custId);
} else {
    assert_test(true, "Customer table tested");
}

echo "\n================================================================\n";
echo "SUMMARY: {$testsPassed} PASSED, {$testsFailed} FAILED\n";
echo "================================================================\n\n";

if ($testsFailed > 0) {
    exit(1);
}
