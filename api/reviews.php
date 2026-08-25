<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/functions.php';

$productId = $_POST['productId'] ?? '';
$userName = $_POST['userName'] ?? '';
$rating = (int)($_POST['rating'] ?? 5);
$comment = $_POST['comment'] ?? '';

if (empty($productId) || empty($userName) || empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all review details']);
    exit;
}

$review = submit_product_review($productId, $userName, $rating, $comment);

echo json_encode([
    'success' => true,
    'message' => 'Review submitted successfully!',
    'review' => $review
]);
