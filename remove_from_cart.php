<?php
session_start();

$productId = $_POST['product_id'] ?? null;
if ($productId && isset($_SESSION['cart'][$productId])) {
  unset($_SESSION['cart'][$productId]);
}

echo json_encode(['success' => true]);
