<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'technician'])) {
    header("Location: login.php");
    exit;
}

require_once 'db_connection.php';

// Fetch all complaint history with status approved or rejected
$sql = "SELECT ch.id, ch.jobcard_id, ch.status, ch.changed_by, ch.changed_at, ch.comments, 
        j.equipment, j.equipment_make, u.fullname AS user_name, u.department
        FROM complaint_history ch
        JOIN jobcards j ON ch.jobcard_id = j.id
        LEFT JOIN users u ON j.user_id = u.id
        WHERE ch.status IN ('approved', 'rejected')
        ORDER BY ch.changed_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Complaint History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0 auto;
            padding: 20px;
            max-width: 1700px;
            overflow-x: hidden;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            max-width: 1800px;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
            table-layout: fixed;
            word-wrap: break-word;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
        .comments {
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <a href="admin_panel.php" class="back-link">Back</a>
    
    <main style="flex-grow: 1; padding: 20px; overflow-x: auto; max-width: 1600px; ">
    <h1>Complaint History </h1>
    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Jobcard ID</th>
                    <th>User</th>
                    <th>Department</th>
                    <th>Equipment</th>
                    <th>Make</th>
                    <th>Status</th>
                    <th>Date/Time</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['jobcard_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                        <td><?php echo htmlspecialchars($row['equipment']); ?></td>
                        <td><?php echo htmlspecialchars($row['equipment_make']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['changed_at']); ?></td>
                        <td class="comments"><?php echo nl2br(htmlspecialchars($row['comments'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No complaint history found.</p>
    <?php endif; ?>
</body>
</html>
