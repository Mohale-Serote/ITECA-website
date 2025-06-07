<?php
require 'includes/db.php';
session_start();
include 'includes/navbar.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT u.*, r.name AS role FROM users u JOIN roles r ON u.role_id = r.id WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && hash('sha256', $password) === $user['password_hash']) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Local Market</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
</head>
<body>
  <div class="page-wrapper">
    <div class="content">
      <h2>Login</h2>
      <?php if ($error): ?>
          <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
      <?php endif; ?>
      <form method="POST" style="max-width: 400px; width: 100%;">
          <div class="container">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required value="<?=htmlspecialchars($email ?? '')?>">
          </div>
          <div class="container">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
          </div>
          <button class="btn btn-custom" type="submit">Login</button>
          <a href="register.php" class="btn btn-custom">Register</a>
      </form>
    </div>
  <div class="w-100 text-center"> 
    <footer class="footer">
      <div class="container">
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
  

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
</body>
</html>
