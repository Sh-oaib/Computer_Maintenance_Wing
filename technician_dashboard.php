<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
    header("Location: login.php");
    exit;
}

require_once 'db_connection.php';

// Fetch only jobcards that are approved and assigned to this technician
$technician_id = $_SESSION['technician_id'] ?? null;
$jobcards_result = null;
if (!$technician_id) {
    echo '<div style="color:red;text-align:center;margin:20px 0;font-size:18px;">Technician session not set. Please log in again or contact admin.</div>';
} else {
    $sql = "SELECT j.id, j.equipment, j.equipment_make, j.out_of_warranty, j.out_of_warranty_details, j.under_amc, j.under_amc_details, j.description, j.Submission_Datetime AS date, u.fullname AS user_name, u.department 
        FROM jobcards j 
        LEFT JOIN users u ON j.user_id = u.id 
        INNER JOIN assigned_tasks a ON a.jobcard_id = j.id 
        WHERE a.technician_id = " . intval($technician_id) . " AND j.status IN ('approved', 'processing', 'complete') 
        ORDER BY j.Submission_Datetime DESC";
    $jobcards_result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Technician Dashboard - Jobcards</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            /* margin: 0 auto; */
            overflow-x: hidden;
            width: 100%;
            /* max-width: 1800px; */
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    border-collapse: collapse;
    background: #fff;
    border-radius: 8px;
    /* overflow: hidden; */
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    margin-top: 20px;
    table-layout: auto;
    overflow-x: auto;
}
th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #ddd;
    text-align: left;
    vertical-align: top;
    overflow-wrap: break-word;
}
th:nth-child(2), td:nth-child(2),
th.user-col, td.user-col {
    width: 180px;      /* Increase as needed */
     
    padding-right: 24px; /* Optional: adds more space to the right */
}
th:not(:last-child), td:not(:last-child) {
    border-right: 1px solid #ccc;
}
th.datetime-col, td.datetime-col {
    padding-left: 32px; /* Increase as needed for more space */
}
th:nth-child(4), td:nth-child(4) {
    width: 200px; 
    padding-right: 40px; 
}
th:nth-child(3), td:nth-child(3) {
    width: 200px; /* Adjust width as needed */
    padding-right: 40px; /* Increase as needed */
}
        th.id-col, td.id-col {
            width: 20px;
            max-width: 70px;
            min-width: 10px;
            word-break: break-all;
        }
        th.user-col, td.user-col {
            width: 110px;
            max-width: 110px;
            min-width: 80px;
            word-break: break-all;
        }
        th.status-col, td.status-col {
            width: 700px;
            margin-right: 10px;
            max-width: 100px;
            min-width: 80px;
            margin-left: 100px;
            word-break: break-all;
        }
        th.datetime-col, td.datetime-col {
            width: 120px;
            max-width: 140px;
            min-width: 80px;
            word-break: break-all;
            padding-right: 80px !important;
            /* padding-left: 80px; */
        }
        th.view-col, td.view-col {
            width: 600px;
            max-width: 600px;
            min-width: 60px;
            word-break: break-all;
            /* padding-right: 80px; */
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        button {
            padding: 5px 10px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            width: 100px;
        }
        .view-details-btn {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
        }
        nav {
            width: 180px;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        main {
            flex-grow: 1;
            padding: 20px;
            overflow-x: auto;
            margin-left: 220px;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        nav h2 {
            color: #000000;
        }
        nav button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            color: #000000;
            margin-bottom: 10px;
        }
        nav button.logout-btn {
            color: #df4d4d;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <nav>
            <h2>Dashboard</h2>
            <button onclick="window.location.href='complaint_history.php'" class="view-details-btn" style="background-color:#17a2b8; margin-bottom:10px;">Complaint History</button>
            <button onclick="window.location.href='logout.php'" class="logout-btn">Logout</button>
        </nav>
        <main>
            <h1>Jobcards</h1>
            <?php if ($jobcards_result && $jobcards_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th class="id-col">ID</th>
                            <th class="user-col">User</th>
                            <th>Department</th>
                            <th class="datetime-col">Date/Time</th>
                            <th class="status-col">Status</th>
                            <th class="view-col">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($jobcard = $jobcards_result->fetch_assoc()): ?>
                            <tr>
                                <td class="id-col"><?php echo htmlspecialchars($jobcard['id']); ?></td>
                                <td class="user-col"><?php echo htmlspecialchars($jobcard['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($jobcard['department']); ?></td>
                                <td class="datetime-col">
                                    <?php
                                    $dateTime = new DateTime($jobcard['date']);
                                    echo $dateTime->format('Y-m-d / h:i A');
                                    ?>
                                </td>
                                <td class="status-col">
                                    <?php
                                    // Fetch current status for this jobcard
                                    $status_stmt = $conn->prepare("SELECT status FROM jobcards WHERE id = ?");
                                    $status_stmt->bind_param("i", $jobcard['id']);
                                    $status_stmt->execute();
                                    $status_res = $status_stmt->get_result();
                                    $current_status = ($status_res && $row = $status_res->fetch_assoc()) ? $row['status'] : '';
                                    $status_stmt->close();
                                    echo htmlspecialchars($current_status);
                                    ?>
                                </td>
                                <td class="view-col">
                                    <button type="button" class="view-details-btn" data-jobcard-id="<?php echo $jobcard['id']; ?>" onclick="window.location.href='get_jobcard_details.php?id=<?php echo $jobcard['id']; ?>'">View Details</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Department</th>
                            <th>Date/Time</th>
                            <th>Status</th>
                            <th>View</th>
                        </tr>
                    </thead>
                </table>
                <p style="color:#888;text-align:center;font-size:18px;">No jobcards assigned to you yet. If you believe this is an error, please contact the admin.</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
