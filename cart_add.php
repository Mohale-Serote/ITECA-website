<?php
session_start();
require 'includes/db.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$product_id = intval($_GET['product_id'] ?? 0);
if ($product_id <= 0) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Invalid product']);
    } else {
        header('Location: index.php');
    }
    exit;
}

// Fetch product to verify it exists
$stmt = $pdo->prepare('SELECT id, name, price FROM products WHERE id = ?');
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Product not found']);
    } else {
        header('Location: index.php');
    }
    exit;
}


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update product quantity
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]++;
} else {
    $_SESSION['cart'][$product_id] = 1;
}

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'cartCount' => array_sum($_SESSION['cart'])]);
} else {
    header('Location: cart.php');
}
exit;
