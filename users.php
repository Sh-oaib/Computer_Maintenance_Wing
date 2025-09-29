<?php
require_once 'db_connection.php';
// Get all users with department, phone_number, and registration date/time
$user_result = $conn->query("SELECT id, fullname, email, phone_number, department, registration_datetime FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registered Users & Technicians</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        h1, h2 { text-align: center; margin-top: 30px; }
        table { width: 80%; margin: 30px auto; border-collapse: collapse; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        th, td { padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: #fff; }
        tr:hover { background-color: #f1f1f1; }
        .count { text-align: center; font-size: 18px; margin-bottom: 10px; }
    </style>
</head>
<body>
    
    <a href="admin_panel.php" style="display:inline-block; margin:30px 0 0 30px; padding:8px 16px; background-color:#007bff; color:#fff; text-decoration:none; border-radius:4px; font-size:16px;">&larr; Back</a>
    <h1>Registered Users</h1>
    <div class="count">Total Users: <?php echo $user_result ? $user_result->num_rows : 0; ?></div>
    <?php if ($user_result && $user_result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Department</th>
                    <th>Date/Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $user_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                        <td>
                            <?php
                            if (!empty($row['registration_datetime'])) {
                                $dt = new DateTime($row['registration_datetime']);
                                echo $dt->format('Y-m-d H:i:s');
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">No users found.</p>
    <?php endif; ?>

    
</body>
</html>