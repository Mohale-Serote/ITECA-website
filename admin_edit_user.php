<?php
require 'includes/db.php';
session_start();

// Only admin can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$user_id = $_GET['id'] ?? null;

if (!$user_id || !is_numeric($user_id)) {
    die('Invalid user ID.');
}

// Fetch user data
$stmt = $pdo->prepare('SELECT u.id, u.name, u.email, u.role_id, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die('User not found.');
}

// Fetch all roles
$rolesStmt = $pdo->query('SELECT id, name FROM roles ORDER BY name');
$roles = $rolesStmt->fetchAll();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_role_id = $_POST['role_id'] ?? null;
    if (!$new_role_id || !is_numeric($new_role_id)) {
        $error = "Please select a valid role.";
    } else {
        // Update user role
        $updateStmt = $pdo->prepare('UPDATE users SET role_id = ? WHERE id = ?');
        $updateStmt->execute([$new_role_id, $user_id]);
        $success = "User role updated successfully.";

        // Refreshes user data
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit User Role - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
</head>
<body class="container pt-5">

<h2>Edit Role for User: <?=htmlspecialchars($user['name'])?></h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?=htmlspecialchars($success)?></div>
<?php endif; ?>

<form method="POST" style="max-width: 400px;">
    <div class="mb-3">
        <label>Email</label>
        <input type="email" class="form-control" value="<?=htmlspecialchars($user['email'])?>" disabled>
    </div>
    <div class="mb-3">
        <label for="role_id">Role</label>
        <select name="role_id" id="role_id" class="form-select" required>
            <option value="">-- Select Role --</option>
            <?php foreach ($roles as $role): ?>
                <option value="<?=intval($role['id'])?>" <?=($role['id'] == $user['role_id']) ? 'selected' : ''?>>
                    <?=htmlspecialchars(ucfirst($role['name']))?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn btn-custom" type="submit">Update Role</button>
    <a href="admin_manage_users.php" class="btn btn-link">Back to User List</a>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
