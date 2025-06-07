<?php
require 'includes/db.php'; 
session_start(); 

$user = $_SESSION['user'] ?? null; 

// Fetch all products with seller's name 
$stmt = $pdo->query('SELECT p.*, u.name AS seller_name FROM products p JOIN users u ON p.seller_id = u.id');
$products = $stmt->fetchAll(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Local Market - Home</title>
   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
</head>
<body>

<!-- Navbar with links for users with specific roles -->
<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="fas fa-store"></i> Local Market</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <?php if ($user): ?>
            <?php if ($user['role'] === 'seller'): ?>
                <!-- Seller options -->
                <li class="nav-item"><a class="nav-link" href="seller_add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a></li>
                <li class="nav-item"><a class="nav-link" href="seller_manage_products.php"><i class="fas fa-box-open"></i> Manage Products</a></li>
            <?php elseif ($user['role'] === 'customer'): ?>
                
                <li class="nav-item">
                    <a href="cart.php" class="btn btn-outline-custom">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <span id="cart-count" class="badge bg-danger">
                            <?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>
                        </span>
                    </a>
                </li>
            <?php elseif ($user['role'] === 'admin'): ?>
                <!-- Admin dashboard link -->
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php"><i class="fas fa-cog"></i> Admin Dashboard</a></li>
            <?php endif; ?>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav">
        <?php if ($user): ?>
            
            <li class="nav-item"><span class="nav-link">Hello, <?=htmlspecialchars($user['name'])?></span></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php else: ?>
            <!-- Login and register buttons -->
            <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>


<section class="hero-section">
  <div class="container hero-content">
    <h1 class="hero-title">Empowering South Africa's Informal Traders</h1>
    <p class="hero-subtitle">Buy and sell locally with ease and trust.</p>
    <a href="index.php" class="btn-custom"><i class="fas fa-shopping-bag"></i> Start Shopping</a>
    <?php if ($user && $user['role'] === 'seller'): ?>
        
        <a href="seller_add_product.php" class="btn-outline-custom"><i class="fas fa-store"></i> List Your Products</a>
    <?php else: ?>
        
        <a href="register.php" class="btn-outline-custom"><i class="fas fa-store"></i> Become a Seller</a>
    <?php endif; ?>
  </div>
</section>

<!-- Product Listings -->
<div class="container my-5">
  <h2 class="section-title">Available Products</h2>
  <div class="row">
    <?php foreach ($products as $product): ?>
      <div class="col-md-4">
        <div class="product-card">
          <div class="product-image"><i class="fas fa-box"></i></div>
          <div class="p-3">
            <h5><?=htmlspecialchars($product['name'])?></h5>
            <p><?=htmlspecialchars($product['description'])?></p>
            <p><strong>R<?=number_format($product['price'], 2)?></strong></p>
            <p><small>Seller: <?=htmlspecialchars($product['seller_name'])?></small></p>
            
            <!-- Add to Cart Button -->
            <?php if ($user && $user['role'] === 'customer'): ?>
                <button class="btn btn-custom add-to-cart-btn" data-product-id="<?= $product['id'] ?>">Add to Cart</button>
            <?php else: ?>
                <small class="text-muted">Login as customer to buy</small>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>


<footer class="footer">
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


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>


<script>
document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();

        const productId = this.getAttribute('data-product-id');

        // Send a get request to cart_add.php
        fetch('cart_add.php?product_id=' + encodeURIComponent(productId), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added to cart!');
                
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(() => alert('Network error.'));
    });
});
</script>
</body>
</html>
