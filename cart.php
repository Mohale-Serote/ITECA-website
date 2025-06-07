<?php
include 'includes/navbar.php';
session_start();
require 'includes/db.php';

$cart = $_SESSION['cart'] ?? [];

// for updating and removing cart items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        
        $delete_id = intval($_POST['delete']);
        if (isset($_SESSION['cart'][$delete_id])) {
            unset($_SESSION['cart'][$delete_id]);
        }
    } elseif (isset($_POST['quantities'])) {
        
        foreach ($_POST['quantities'] as $product_id => $qty) {
            $qty = intval($qty);
            if ($qty < 1) {
                $qty = 1; 
            }
            $_SESSION['cart'][$product_id] = $qty;
        }
    }
    header('Location: cart.php');
    exit;
}


$products = [];
$total = 0.0;

if ($cart) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    // Calculate cart total
    foreach ($products as $product) {
        $total += $product['price'] * $cart[$product['id']];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Cart - Local Market</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
</head>
<body>
<div class="container my-5">
<h2>Your Shopping Cart</h2>

<?php if (!$cart): ?>
    <p>Your cart is empty. <a href="index.php">Start shopping</a>.</p>
<?php else: ?>
<form method="POST">
    
<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>Product</th>
            <th>Price (R)</th>
            <th>Quantity</th>
            <th>Subtotal (R)</th>
            <th>Action</th> 
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): 
            $qty = $cart[$product['id']];
            $subtotal = $product['price'] * $qty;
        ?>
        <tr>
            <td><?=htmlspecialchars($product['name'])?></td>
            <td><?=number_format($product['price'], 2)?></td>
            <td>
                <input type="number" name="quantities[<?=$product['id']?>]" value="<?=$qty?>" min="1" class="form-control" style="width:80px;">
            </td>
            <td><?=number_format($subtotal, 2)?></td>
            <td>
                <button type="submit" name="delete" value="<?=$product['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Remove this product from cart?');">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" class="text-end"><strong>Total:</strong></td>
            <td><strong>R<?=number_format($total, 2)?></strong></td>
            <td></td>
        </tr>
    </tbody>
</table>
        


<a href="checkout.php" class="btn btn-success ms-3">Proceed to Checkout</a>
</form>
<?php endif; ?>
<a href="index.php" class="btn btn-custom mt-3">Continue Shopping</a>
</div>
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
