<?php
require 'includes/db.php';
include 'includes/navbar.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'seller') {
    header('Location: login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);

    if ($name && $price > 0 && $stock >= 0) {
        $stmt = $pdo->prepare('INSERT INTO products (seller_id, name, description, price, stock) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user']['id'], $name, $description, $price, $stock]);
        header('Location: index.php');
        exit;
    } else {
        $error = "Please fill all fields correctly.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Product - Local Market</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
</head>
<body>

<div class="content">
    
<h2>Add Product</h2>
<?php if ($error): ?>
<div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
<?php endif; ?>
<form method="POST" style="max-width: 600px;">
    <div class="mb-3">
        <label>Product Name</label>
        <input type="text" name="name" class="form-control" required value="<?=htmlspecialchars($name ?? '')?>">
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="4"><?=htmlspecialchars($description ?? '')?></textarea>
    </div>
    <div class="mb-3">
        <label>Price (R)</label>
        <input type="number" step="0.01" name="price" class="form-control" required value="<?=htmlspecialchars($price ?? '')?>">
    </div>
    <div>
        <label>Stock Quantity</label>
        <input type="number" name="stock" class="form-control" required value="<?=htmlspecialchars($stock ?? '')?>">
    </div>
    <button class="btn btn-custom" type="submit">Add Product</button>
    <a href="index.php" class="btn btn-custom">Cancel</a>
</div>


</form>
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
