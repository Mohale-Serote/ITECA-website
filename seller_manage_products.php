<?php
require 'includes/db.php';
session_start();
include 'includes/navbar.php';

// check if current user is a seller
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'seller') {
    header('Location: login.php');
    exit;
}

$seller_id = $_SESSION['user']['id'];
$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product_id'])) {
    $product_id = intval($_POST['delete_product_id']);

    // check if product belongs to user selling it
    $stmt = $pdo->prepare('SELECT id FROM products WHERE id = ? AND seller_id = ?');
    $stmt->execute([$product_id, $seller_id]);
    $product = $stmt->fetch();

    if ($product) {
        // Deletes the product
        $delStmt = $pdo->prepare('DELETE FROM products WHERE id = ? AND seller_id = ?');
        $delStmt->execute([$product_id, $seller_id]);
        $success = "Product deleted successfully.";
    } else {
        $error = "Product not found or you don't have permission to delete it.";
    }
}

// Get all sellers products
$stmt = $pdo->prepare('SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC');
$stmt->execute([$seller_id]);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Your Products - Local Market</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
</head>
<body>
    
    <div class="content">
    <div class="container-fluid pt-5 w-75">
<h2 class=>Your Listed Products</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?=htmlspecialchars($success)?></div>
<?php endif; ?>

<?php if (count($products) === 0): ?>
    <p>You have not listed any products yet.</p>

<?php else: ?>
    <table class="table table-custom">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price (R)</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?=htmlspecialchars($product['name'])?></td>
                <td><?=htmlspecialchars($product['description'])?></td>
                <td><?=number_format($product['price'], 2)?></td>
                <td><?=intval($product['stock'])?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" style="display:inline;">
                        <input type="hidden" name="delete_product_id" value="<?=intval($product['id'])?>">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>
            

<a href="index.php" class="btn btn-custom">Back to Home</a>
<a href="seller_add_product.php" class="btn btn-custom">Create Product Listing</a>
</div>
</div>


  
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



<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
            
</body>
</html>
