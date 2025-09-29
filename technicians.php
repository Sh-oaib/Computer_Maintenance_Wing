<?php
require_once 'db_connection.php';
$result = $conn->query("SELECT id, fullname, email, phone_number FROM technicians ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Technicians</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        h1 { text-align: center; margin-top: 30px; }
        table { width: 80%; margin: 30px auto; border-collapse: collapse; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        th, td { padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: #fff; }
        tr:hover { background-color: #f1f1f1; }
        .count { text-align: center; font-size: 18px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <a href="admin_panel.php" style="display:inline-block; margin:30px 0 0 30px; padding:8px 16px; background-color:#007bff; color:#fff; text-decoration:none; border-radius:4px; font-size:16px;">&larr; Back</a>
    <h1>Technicians</h1>
    <div class="count">Total Technicians: <?php echo $result ? $result->num_rows : 0; ?></div>
    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">No technicians found.</p>
    <?php endif; ?>
</body>
</html>
