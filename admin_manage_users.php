<?php
require 'includes/db.php';
require 'includes/auth.php';

// Only admin and managers can access
requireRole(['admin', 'manager']);

$error = '';
$success = '';

// Handle create/update/delete actions here (POST requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Example: Delete user
    if (isset($_POST['delete_user_id'])) {
        $userId = intval($_POST['delete_user_id']);
        // Prevent deleting self
        if ($userId === $_SESSION['user']['id']) {
            $error = "You cannot delete your own account.";
        } else {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $success = "User deleted successfully.";
        }
    }
    // Similarly implement create and update logic here
}

// Fetch users with role names
$stmt = $pdo->query('SELECT u.id, u.name, u.email, r.name AS role FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id DESC');
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users - Admin Panel</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container pt-5">
<h2>User Management</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?=htmlspecialchars($success)?></div>
<?php endif; ?>

<table class="table table-custom">
    <thead>
        <tr>
            <th>Name</th><th>Email</th><th>Role</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?=htmlspecialchars($user['name'])?></td>
            <td><?=htmlspecialchars($user['email'])?></td>
            <td><?=htmlspecialchars($user['role'])?></td>
            <td>
                
                <a href="admin_edit_user.php?id=<?=intval($user['id'])?>" class="btn btn-sm btn-primary">Edit</a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                    <input type="hidden" name="delete_user_id" value="<?=intval($user['id'])?>">
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="container text-center">
<a href="admin_dashboard.php" class="btn btn-custom">Back to Dashboard</a>
        </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
