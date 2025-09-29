<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db_connection.php';

// Fetch all indent submissions with department
$sql = "SELECT indent.id, users.fullname AS user_name, users.department, indent.item, indent.particulars, indent.quantity_required, indent.status FROM indent LEFT JOIN users ON indent.user_id = users.id ORDER BY indent.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Indent History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0 auto;
            padding: 20px;
            overflow-x: auto;
            width: 100%;
            max-width: 1700px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 98vw;
            min-width: 1200px;
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
        th.particulars-col, td.particulars-col {
            width: 400px;
            max-width: 400px;
            word-break: break-word;
        }
        th.action-col, td.action-col {
            text-align: center;
            vertical-align: middle;
            width: 1px;
            padding-left: 0;
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
    <a href="admin_panel.php" class="back-link">Back</a>
    <h1>Indent History</h1>
    <?php
    // Handle accept/reject actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['indent_id'], $_POST['action'])) {
        $indent_id = intval($_POST['indent_id']);
        $action = $_POST['action'] === 'accept' ? 'Accepted' : 'Rejected';
        // Update status in indents table (add status column if not exists)
        $stmt = $conn->prepare("UPDATE indent SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $indent_id);
        $stmt->execute();
        $stmt->close();
        // Refresh page to show updated status
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    ?>
    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Department</th>
                    <th>Item</th>
                    <th class="particulars-col">Particulars</th>
                    <th>Quantity Required</th>
                    <th>Status</th>
                    <th class="action-col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $result->data_seek(0); while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                        <td><?php echo htmlspecialchars($row['item']); ?></td>
                        <td class="particulars-col"><?php echo htmlspecialchars($row['particulars']); ?></td>
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
                        <td class="action-col" style="text-align:center; vertical-align:middle;">
                            <?php if ($status !== 'Accepted' && $status !== 'Rejected') { ?>
                                <form method="POST" style="display:flex; justify-content:center; align-items:center; gap:8px; margin:0;">
                                    <input type="hidden" name="indent_id" value="<?php echo htmlspecialchars($row['id']); ?>" />
                                    <button type="submit" name="action" value="accept" style="background-color:#28a745; color:#fff; border:none; border-radius:4px; padding:6px 12px; cursor:pointer;">Accept</button>
                                    <button type="submit" name="action" value="reject" style="background-color:#dc3545; color:#fff; border:none; border-radius:4px; padding:6px 12px; cursor:pointer;">Reject</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No indent history found.</p>
    <?php endif; ?>
</body>
</html>
