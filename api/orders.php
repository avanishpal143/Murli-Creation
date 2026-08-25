<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'place_order') {
    $customerJson = $_POST['customer'] ?? '';
    $paymentMethod = $_POST['paymentMethod'] ?? 'COD';
    $paymentStatus = $_POST['paymentStatus'] ?? 'Pending';

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

    $order = place_new_order($customer, $paymentMethod, $paymentStatus, $cart);
    echo json_encode([
        'success' => true,
        'orderId' => $order['id'],
        'message' => "Order {$order['id']} placed successfully!"
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
