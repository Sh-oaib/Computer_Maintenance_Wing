<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db_connection.php';

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    if ($_POST['action'] === 'approve') {
        $conn->query("UPDATE users SET is_approved = 1 WHERE id = $user_id");
    } elseif ($_POST['action'] === 'reject') {
        $conn->query("DELETE FROM users WHERE id = $user_id");
    }
}

// Fetch pending users
$result = $conn->query("SELECT id, fullname, department, email, phone_number, registration_datetime FROM users WHERE is_approved = 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Approvals</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        table { width: 90%; margin: 30px auto; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: #fff; }
        tr:hover { background-color: #f1f1f1; }
        .btn { padding: 6px 14px; border: none; border-radius: 4px; color: #fff; cursor: pointer; }
        .approve { background: #28a745; }
        .reject { background: #dc3545; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Pending User Approvals</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Department</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Registration Date/Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['fullname']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                <td><?= htmlspecialchars($row['registration_datetime']) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="action" value="approve" class="btn approve">Approve</button>
                        <button type="submit" name="action" value="reject" class="btn reject" onclick="return confirm('Are you sure to reject and delete this user?');">Reject</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>