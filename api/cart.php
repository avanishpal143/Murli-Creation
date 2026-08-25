<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $productId = $_POST['productId'] ?? '';
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    $product = get_product_by_id($productId);
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    $cart = add_to_session_cart($product, $quantity);
    echo json_encode([
        'success' => true,
        'message' => "Added {$product['name']} to cart!",
        'cart' => $cart,
        'cartCount' => get_cart_count(),
        'subtotal' => get_cart_subtotal()
    ]);
    exit;
}

if ($action === 'update') {
    $productId = $_POST['productId'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 1);

    $cart = update_session_cart_quantity($productId, $quantity);
    echo json_encode([
        'success' => true,
        'cart' => $cart,
        'cartCount' => get_cart_count(),
        'subtotal' => get_cart_subtotal()
    ]);
    exit;
}

if ($action === 'remove') {
    $productId = $_POST['productId'] ?? '';
    $cart = remove_from_session_cart($productId);
    echo json_encode([
        'success' => true,
        'cart' => $cart,
        'cartCount' => get_cart_count(),
        'subtotal' => get_cart_subtotal()
    ]);
    exit;
}

if ($action === 'clear') {
    clear_session_cart();
    echo json_encode([
        'success' => true,
        'cart' => [],
        'cartCount' => 0,
        'subtotal' => 0
    ]);
    exit;
}

if ($action === 'apply_coupon') {
    $code = $_POST['couponCode'] ?? '';
    $subtotal = get_cart_subtotal();
    $val = validate_coupon($code, $subtotal);
    if ($val['valid']) {
        $_SESSION['mc_applied_coupon'] = $val;
        echo json_encode([
            'success' => true, 
            'coupon' => $val, 
            'discount' => $val['discount'],
            'message' => $val['message']
        ]);
    } else {
        unset($_SESSION['mc_applied_coupon']);
        echo json_encode(['success' => false, 'message' => $val['message']]);
    }
    exit;
}

// Default get cart status
echo json_encode([
    'success' => true,
    'cart' => get_session_cart(),
    'cartCount' => get_cart_count(),
    'subtotal' => get_cart_subtotal()
]);
