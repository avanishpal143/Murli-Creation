<?php
// =============================================================================
// Murli Creations - External Webhook & Logistics Status API Endpoint
// Enables Shiprocket, Delhivery, Razorpay, or custom courier APIs to update order status
// Endpoint: /api/webhook.php
// Method: POST
// Headers: X-Api-Key: <api_key> (or GET/POST parameter 'api_key')
// Payload: JSON { "order_id": "MURLI-...", "status": "Shipped", "carrier": "...", "awb": "...", "notes": "..." }
// =============================================================================

header('Content-Type: application/json');
require_once __DIR__ . '/../config/functions.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput, true) ?: $_POST;

// 1. Verify API Key
$headers = function_exists('getallheaders') ? getallheaders() : [];
if (empty($headers)) {
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) === 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
}
$apiKey = $headers['X-Api-Key'] ?? $headers['x-api-key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? $payload['api_key'] ?? '';

if (empty($apiKey)) {
    if (function_exists('db_log_webhook_event')) {
        db_log_webhook_event('/api/webhook.php', $method, $rawInput, 401);
    }
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized: Missing API Key']);
    exit;
}

$verified = function_exists('db_verify_api_key') ? db_verify_api_key($apiKey) : false;
if (!$verified) {
    if (function_exists('db_log_webhook_event')) {
        db_log_webhook_event('/api/webhook.php', $method, $rawInput, 403);
    }
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Forbidden: Invalid or Inactive API Key']);
    exit;
}

// 2. Process Status Update Event
$orderId = trim($payload['order_id'] ?? $payload['orderId'] ?? '');
$newStatus = trim($payload['status'] ?? $payload['order_status'] ?? '');
$carrier = trim($payload['carrier'] ?? $payload['courier_name'] ?? '');
$awb = trim($payload['awb'] ?? $payload['tracking_number'] ?? $payload['awb_code'] ?? '');
$trackingUrl = trim($payload['tracking_url'] ?? '');
$comment = trim($payload['notes'] ?? $payload['comment'] ?? "Updated via External Webhook ({$verified['key_name']})");

if (empty($orderId) || empty($newStatus)) {
    if (function_exists('db_log_webhook_event')) {
        db_log_webhook_event('/api/webhook.php', $method, $rawInput, 400);
    }
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields: order_id and status']);
    exit;
}

// Normalize Status
$validStatuses = ['Pending', 'Confirmed', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered', 'Cancelled'];
$matchedStatus = null;
foreach ($validStatuses as $vs) {
    if (strcasecmp($vs, $newStatus) === 0) {
        $matchedStatus = $vs;
        break;
    }
}
if (!$matchedStatus) {
    $matchedStatus = ucfirst(strtolower($newStatus));
}

// 1. Update Tracking if provided
if (!empty($carrier) || !empty($awb)) {
    if (function_exists('db_update_order_tracking')) {
        db_update_order_tracking($orderId, $carrier ?: 'Courier Partner', $awb, $trackingUrl);
    }
}

// 2. Update Order Status
$updated = false;
if (function_exists('db_change_order_status')) {
    $updated = db_change_order_status($orderId, $matchedStatus, $comment, true, $verified['key_name']);
}

// Log Webhook Hit
if (function_exists('db_log_webhook_event')) {
    db_log_webhook_event('/api/webhook.php', $method, $rawInput, 200);
}

echo json_encode([
    'success' => true,
    'message' => "Order #{$orderId} status successfully updated to '{$matchedStatus}'",
    'orderId' => $orderId,
    'newStatus' => $matchedStatus,
    'timestamp' => date('Y-m-d H:i:s')
]);
