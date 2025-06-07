<?php
require 'includes/db.php';
session_start();
include 'includes/navbar.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'customer';

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $password && in_array($role, ['customer', 'seller'])) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $role_id = $pdo->prepare('SELECT id FROM roles WHERE name = ?');
            $role_id->execute([$role]);
            $role_id = $role_id->fetchColumn();

            $password_hash = hash('sha256', $password);

            $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role_id) VALUES (?, ?, ?, ?)');
            $insert->execute([$name, $email, $password_hash, $role_id]);

            $_SESSION['message'] = "Registration successful! Please login.";
            header('Location: login.php');
            exit;
        }
    } else {
        $error = "Please fill all fields correctly.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register - Local Market</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
</head>
<body>
  <div class="page-wrapper">
    <div class="content">
      <h2>Register</h2>
      <?php if ($error): ?>
          <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
      <?php endif; ?>
      <form method="POST" style="max-width: 400px; width: 100%;">
          <div class="mb-3">
              <label>Name</label>
              <input type="text" name="name" class="form-control" required value="<?=htmlspecialchars($name ?? '')?>">
          </div>
          <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required value="<?=htmlspecialchars($email ?? '')?>">
          </div>
          <div class="mb-3">
              <label>Role</label>
              <select name="role" class="form-select" required>
                  <option value="customer" <?= (isset($role) && $role === 'customer') ? 'selected' : '' ?>>Customer</option>
                  <option value="seller" <?= (isset($role) && $role === 'seller') ? 'selected' : '' ?>>Seller (Informal Trader)</option>
              </select>
          </div>
          <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
          </div>
          <button class="btn btn-custom" type="submit">Register</button>
          <a href="login.php" class="btn btn-link btn btn-custom">Login</a>
      </form>
    </div>
    <footer class="footer w-100">
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
  </div>
</body>

</html>
