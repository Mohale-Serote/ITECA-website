<?php
session_start();
//save cart items
$cart = $_SESSION['cart'] ?? [];

session_destroy();

session_start();
$_SESSION['cart'] = $cart;

header('Location: index.php');
exit;

