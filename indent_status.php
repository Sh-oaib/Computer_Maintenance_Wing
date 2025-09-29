<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'db_connection.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT indent.id, indent.item, indent.particulars, indent.quantity_required, indent.status FROM indent WHERE indent.user_id = ? ORDER BY indent.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Indent Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0 auto;
            padding: 20px;
            max-width: 1200px;
            overflow-x: auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
            table-layout: auto;
            word-wrap: break-word;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
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
    </style>
</head>
<body>
    <a href="Index.php" class="back-link">Back</a>
    <h1>My Indent Status</h1>
    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item</th>
                    <th>Particulars</th>
                    <th>Quantity Required</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item']); ?></td>
                        <td><?php echo htmlspecialchars($row['particulars']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity_required']); ?></td>
                        <td style="text-align:center; vertical-align:middle;">
                            <?php
                            $status = isset($row['status']) ? $row['status'] : '';
                            if ($status === 'Accepted') {
                                echo '<span style="font-weight:bold; color:#28a745;">Accepted</span>';
                            } elseif ($status === 'Rejected') {
                                echo '<span style="font-weight:bold; color:#dc3545;">Rejected</span>';
                            } else {
                                echo '<span style="font-weight:bold; color:#ffc107;">Pending</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No indents submitted yet.</p>
    <?php endif; ?>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
