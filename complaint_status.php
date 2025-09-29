<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db_connection.php';

$user_id = $_SESSION['user_id'];

// Fetch complaint history for logged-in user

// Fetch jobcards submitted by the logged-in user, showing their status
$sql = "SELECT id AS jobcard_id, equipment, equipment_make, status, Submission_Datetime AS changed_at, '' AS comments FROM jobcards WHERE user_id = ? ORDER BY Submission_Datetime DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Complaint Status & History</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            max-width: 1700px;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0 auto;
            
        }
        .content {
            flex: 1;
            margin-top: 100px;
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
        .comments {
            white-space: pre-wrap;
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
    <div class="content">
        <h1>Complaint Status</h1>
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
            <thead>
                <tr>
                    <th>Jobcard ID</th>
                    <th>Equipment</th>
                    <th>Make</th>
                    <th>Status</th>
                    <th>Date/Time</th>
                    <th>Technician Assigned</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['jobcard_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['equipment']); ?></td>
                        <td><?php echo htmlspecialchars($row['equipment_make']); ?></td>
                        <td>
                            <?php
                            $status = $row['status'];
                            echo ($status === null || $status === '' ? '<span style="color:orange;font-weight:bold;">Pending</span>' : htmlspecialchars($status));
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['changed_at']); ?></td>
                        <td>
                            <?php
                            // Fetch assigned technician's name for this jobcard
                            $assigned_tech_name = '';
                            $assigned_stmt = $conn->prepare("SELECT t.fullname FROM assigned_tasks a INNER JOIN technicians t ON a.technician_id = t.id WHERE a.jobcard_id = ?");
                            $assigned_stmt->bind_param("i", $row['jobcard_id']);
                            $assigned_stmt->execute();
                            $assigned_res = $assigned_stmt->get_result();
                            if ($assigned_res && $assigned_row = $assigned_res->fetch_assoc()) {
                                $assigned_tech_name = $assigned_row['fullname'];
                            }
                            $assigned_stmt->close();
                            echo $assigned_tech_name ? htmlspecialchars($assigned_tech_name) : '<span style="color:#888;">Not Assigned</span>';
                            ?>
                        </td>
                        <td class="comments"><?php echo nl2br(htmlspecialchars($row['comments'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No complaint status found.</p>
    <?php endif; ?>
    </div>
</body>
</html>
