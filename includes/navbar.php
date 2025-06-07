<?php
$user = $_SESSION['user'] ?? null;
?>
<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container"> 
  <a href="index.php" class="btn btn-link back-arrow-btn" aria-label="Back to Home" title="Back to Home">
      <i class="fas fa-arrow-left"></i>
    </a>
    <a class="navbar-brand text-white" href="index.php"><i class="fas fa-store"></i> Local Market</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <?php if ($user): ?>
            <?php if ($user['role'] === 'seller'): ?>
                <li class="nav-item"><a class="nav-link text-white" href="seller_add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a></li>
            <?php elseif ($user['role'] === 'customer'): ?>
                <li class="nav-item"><a class="nav-link text-white" href="#"><i class="fas fa-shopping-cart"></i> Cart</a></li>
            <?php elseif ($user['role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link text-white" href="admin_dashboard.php"><i class="fas fa-cog"></i> Admin Dashboard</a></li>
            <?php endif; ?>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <?php if ($user): ?>
            <li class="nav-item"><span class="nav-link text-white">Hello, <?=htmlspecialchars($user['name'])?></span></li>
            <li class="nav-item"><a class="nav-link text-white" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
