<?php
require 'includes/db.php';
session_start();
include 'includes/navbar.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// checks if user has admin role
if ($_SESSION['user']['role'] !== 'admin') {
    
    echo "<h2>Access Denied</h2><p>You do not have permission to access this page.</p>";
    exit;
}


$userCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$productCount = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$orderCount = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalRevenue = $pdo->query("SELECT IFNULL(SUM(total),0) FROM orders WHERE status = 'completed'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard - Local Market</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
</head>
<body>
<div class="container text-center">
<h1>Admin Dashboard</h1>
<p>Welcome, <strong><?=htmlspecialchars($_SESSION['user']['name'])?></strong></p>
</div>
<div class="container text-center">
<div class="row g-4 mt-4 container">
    <div class="col-md-3">
        <div class="stats-card info text-center p-4">
            <h4>Total Users</h4>
            <p class="display-5"><?= $userCount ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success text-center p-4">
            <h4>Total Products</h4>
            <p class="display-5"><?= $productCount ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning text-center p-4">
            <h4>Total Orders</h4>
            <p class="display-5"><?= $orderCount ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card danger text-center p-4">
            <h4>Total Revenue (R)</h4>
            <p class="display-5"><?= number_format($totalRevenue, 2) ?></p>
        </div>
    </div>
</div>
</div>
<div class="container text-center">
    <a href="admin_manage_users.php" class="btn btn-custom me-3"><i class="fas fa-users"></i> Manage Users</a>
    <a href="admin_manage_products.php" class="btn btn-custom me-3"><i class="fas fa-box"></i> Manage Products</a>
    <a href="admin_reports.php" class="btn btn-custom"><i class="fas fa-chart-bar"></i> Generate Reports</a>
</div>

<div class="container text-center">
    <a href="index.php" class="btn btn-custom">Back to Home</a>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<footer class="footer fixed-bottom">
  <div class="container text-center">
    <h5>Local Market</h5>
    <p>&copy; <?=date('Y')?> Local Market. All rights reserved.</p>
    <div>
      <a href="#"><i class="fab fa-facebook fa-lg mx-2"></i></a>
      <a href="#"><i class="fab fa-twitter fa-lg mx-2"></i></a>
      <a href="#"><i class="fab fa-instagram fa-lg mx-2"></i></a>
    </div>
  </div>
</footer>

</body>
</html>
