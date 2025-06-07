<?php
require 'includes/db.php';
require 'includes/auth.php';

// Allow admins and managers only
requireRole(['admin', 'manager']);

$reportType = $_GET['type'] ?? 'sales';

switch ($reportType) {
    case 'sales':
        // Aggregate total sales by month
        $stmt = $pdo->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total) AS total_sales
            FROM orders
            WHERE status = 'completed'
            GROUP BY month
            ORDER BY month DESC
            LIMIT 12
        ");
        $data = $stmt->fetchAll();
        break;

    case 'users':
        // Count users by role
        $stmt = $pdo->query("
            SELECT r.name AS role, COUNT(u.id) AS count
            FROM users u
            JOIN roles r ON u.role_id = r.id
            GROUP BY r.name
        ");
        $data = $stmt->fetchAll();
        break;

    default:
        $data = [];
        break;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reports - Admin Panel</title>
    <link href="../css/styles.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container pt-5">
<h2>Reports: <?=htmlspecialchars(ucfirst($reportType))?></h2>

<?php if ($reportType === 'sales'): ?>
<table class="table table-custom">
    <thead><tr><th>Month</th><th>Total Sales (R)</th></tr></thead>
    <tbody>
        <?php foreach ($data as $row): ?>
        <tr>
            <td><?=htmlspecialchars($row['month'])?></td>
            <td>R<?=number_format($row['total_sales'], 2)?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php elseif ($reportType === 'users'): ?>

<table class="table table-custom">
    <thead><tr><th>Role</th><th>User Count</th></tr></thead>
    <tbody>
        <?php foreach ($data as $row): ?>
        <tr>
            <td><?=htmlspecialchars($row['role'])?></td>
            <td><?=intval($row['count'])?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>
<p>No report available.</p>
<?php endif; ?>

<a href="admin_dashboard.php" class="btn btn-outline-custom mt-3">Back to Dashboard</a>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
