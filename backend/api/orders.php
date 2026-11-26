<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'place_order') {
    $customerJson = $_POST['customer'] ?? '';
    $paymentMethod = $_POST['paymentMethod'] ?? 'Partial COD';
    $paymentStatus = $_POST['paymentStatus'] ?? 'Pending';
    $couponCode = $_POST['couponCode'] ?? '';
    $discountAmount = (float)($_POST['discountAmount'] ?? 0);
    $shippingFee = isset($_POST['shippingFee']) ? (float)$_POST['shippingFee'] : null;
    $tokenAmount = (float)($_POST['tokenAmount'] ?? 0);
    $remainingBalance = (float)($_POST['remainingBalance'] ?? 0);

    $customer = json_decode($customerJson, true);
    if (!$customer || empty($customer['name']) || empty($customer['phone'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid customer details provided']);
        exit;
    }

    $cart = get_session_cart();
    if (empty($cart)) {
        echo json_encode(['success' => false, 'message' => 'Your cart is empty']);
        exit;
    }

    $order = place_new_order(
        $customer,
        $paymentMethod,
        $paymentStatus,
        $cart,
        $couponCode,
        $discountAmount,
        $shippingFee,
        $tokenAmount,
        $remainingBalance
    );

    echo json_encode([
        'success' => true,
        'orderId' => $order['id'],
        'message' => "Order {$order['id']} placed successfully!",
        'tokenPaid' => $order['tokenPaid'],
        'balanceOnDelivery' => $order['balanceOnDelivery'],
        'totalPayable' => $order['totalPayable']
    ]);
    exit;
}

if ($action === 'update_order_status') {
    $orderId = $_POST['orderId'] ?? '';
    $status = $_POST['status'] ?? 'Pending';

    $updated = update_order_status_by_id($orderId, $status);
    if ($updated) {
        echo json_encode(['success' => true, 'message' => "Order status updated to {$status}"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action']);
