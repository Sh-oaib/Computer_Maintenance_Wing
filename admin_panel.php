<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'db_connection.php';

// Handle form submission for approving/rejecting jobcards with optional comments
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobcard_id = $_POST['jobcard_id'];
    $action = $_POST['action'];
    $comments = $_POST['comments'] ?? '';
    $changed_by = $_SESSION['username'] ?? 'admin';

    // Make comments optional: if empty string, set to NULL
    if (trim($comments) === '') {
        $comments = null;
    }

    // Determine new status
    if ($action === 'approve') {
        $new_status = 'approved';
    } elseif ($action === 'reject') {
        $new_status = 'rejected';
    } else {
        $new_status = null;
    }

    if ($new_status) {
        // Update jobcards status
        $stmt = $conn->prepare("UPDATE jobcards SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $jobcard_id);
        $stmt->execute();
        $stmt->close();

        // Insert into complaint_history
        $stmt2 = $conn->prepare("INSERT INTO complaint_history (jobcard_id, status, changed_by, comments) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("isss", $jobcard_id, $new_status, $changed_by, $comments);
        $stmt2->execute();
        $stmt2->close();
    }
}

// Fetch jobcards without status column
$jobcards_result = $conn->query("SELECT j.id, j.equipment, j.equipment_make, j.out_of_warranty, j.out_of_warranty_details, j.under_amc, j.under_amc_details, j.description, j.Submission_Datetime AS date, u.fullname AS user_name, u.department FROM jobcards j LEFT JOIN users u ON j.user_id = u.id ORDER BY j.Submission_Datetime DESC");

// Prepare statement to fetch complaint history for each jobcard
$history_stmt = $conn->prepare("SELECT status, changed_by, changed_at, comments FROM complaint_history WHERE jobcard_id = ? ORDER BY changed_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel - Jobcards</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0 auto;
           
            overflow-x: hidden;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {  width: 100%;
            margin-left: auto;
            margin-right: auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
            table-layout: auto;
            
            white-space: normal;
            overflow-x: auto;
            word-break: break-word ;
        }
        th:nth-child(3), td:nth-child(3) {
            width: 170px;
            padding-left: 4px;
            padding-right: 8px;
        }
        th:nth-child(4), td:nth-child(4) {
            width: 130px;
            padding-right: 4px;
            padding-left: 4px;
        }
        th:nth-child(5), td:nth-child(5) {
            width: 70px;
            padding-left: 4px;
            padding-right: 2px;
        }
        th:nth-child(6), td:nth-child(6) {
            width: 80px;
            padding-right: 2px;
            padding-left: 4px;
        }
        th:nth-child(7), td:nth-child(7) {
            width: 180px;
            padding-left: 4px;
        }
           
        th:nth-child(1), td:nth-child(1) {
            width: 18px;
            padding-right: 2px;
        }
        th:nth-child(2), td:nth-child(2) {
            width: 140px;
            padding-left: 4px;
            padding-right: 8px;
        }
        
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }
        th, td {
    border-bottom: 1px solid #ddd;
    text-align: left;
    vertical-align: top;
}

th:not(:last-child), td:not(:last-child) {
    border-right: 1px solid #ccc;
}
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-form {
            display: flex;
            flex-direction: column;
            gap: 5px;

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
        .approve-btn {
            background-color: #28a745;
        }
        .reject-btn {
            background-color: #dc3545;
        }
        textarea {
            width: 100%;
            resize: vertical;
            min-height: 50px;
            font-size: 14px;
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .history {
            font-size: 12px;
            background: #f9f9f9;
            padding: 4px;
            border-radius: 4px;
            max-height: 100px;
            overflow-y: auto;
            margin-top: 5px;
            max-width: 250px;
            word-wrap: break-word;
        }
        .history-entry {
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }
        .history-entry:last-child {
            border-bottom: none;
        }
        .history-status {
            font-weight: bold;
        }
        .history-meta {
            color: #666;
            font-style: italic;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div style="display: flex; min-height: 100vh;">
        <nav style="width: 180px; background-color: #ffffffff; padding: 20px; box-shadow: 2px 0 5px rgba(0,0,0,0.1); position: fixed; height: 100vh; overflow-y: auto;">
            <h2 style="color: #000000ff;">Dashboard</h2>
            <button onclick="window.location.href='users.php'" id="showUsersBtn" style="width: 100%; padding: 10px; font-size: 16px; cursor: pointer; color: #000000ff; margin-bottom: 10px;">Show Users</button>
            <button onclick="window.location.href='technicians.php'" id="showTechBtn" style="width: 100%; padding: 10px; font-size: 16px; cursor: pointer; color: #000000ff; margin-bottom: 10px;">Show Technicians</button>
            <button onclick="window.location.href='indent_history.php'" style="width: 100%; padding: 10px; font-size: 16px; cursor: pointer; color: #000000ff; margin-bottom: 10px;">Show Indents</button>
            <button onclick="window.location.href='manage_indent_items.php'" style="width: 100%; padding: 10px; font-size: 16px; cursor: pointer; color: #007bff; margin-bottom: 10px;">Manage Indent Items</button>
            <button onclick="window.location.href='user_approvals.php'" style="width: 100%; padding: 10px; font-size: 16px; cursor: pointer; color: #007bff; margin-bottom: 10px;">User Approvals</button>
            <button onclick="window.location.href='complaint_history.php'" style="width: 100%; padding: 10px; font-size: 16px; cursor: pointer; color: #000000ff;">Complaint History</button>
            <button onclick="window.location.href='add_technician.php'" style="width: 100%; padding: 10px; font-size: 16px; cursor: pointer; color: #000000ff;">Add Technician</button>
            <button onclick="window.location.href='logout.php'" style="width: 100%; margin-bottom: 10px; padding: 10px; font-size: 16px; cursor: pointer; color: #df4d4dff;">Logout</button>
            <!-- usersList removed -->
        </nav>
        <main style="flex-grow: 1; padding: 20px; overflow-x: auto; margin-left: 220px;">
            <h1>Jobcards</h1>

            
            </section>
            <?php if ($jobcards_result && $jobcards_result->num_rows > 0): ?>
                <table style="width: 100%; max-width: none;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Department</th>
                            <th>Date/Time</th>
                            <th>Status</th>
                            <th>View</th>
                            <th>Assigned Technician</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($jobcard = $jobcards_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($jobcard['id']); ?></td>
                                <td><?php echo htmlspecialchars($jobcard['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($jobcard['department']); ?></td>
                                <td>
                                    <?php
                                    $dateTime = new DateTime($jobcard['date']);
                                    echo $dateTime->format('Y-m-d / h:i A');
                                    ?>
                                </td>
                                <?php
                                // Fetch latest status from complaint_history
                                $history_stmt->bind_param("i", $jobcard['id']);
                                $history_stmt->execute();
                                $history_result = $history_stmt->get_result();
                                $latest_status = 'pending';
                                if ($history_result && $history_result->num_rows > 0) {
                                    $latest_status_row = $history_result->fetch_assoc();
                                    $latest_status = $latest_status_row['status'];
                                }
                                ?>
                                <td><?php echo htmlspecialchars($latest_status); ?></td>
                                <td>
                                    <button type="button" class="view-details-btn" data-jobcard-id="<?php echo $jobcard['id']; ?>" style="background-color: #007bff; color: white; border: none; border-radius: 4px; padding: 5px 10px; cursor: pointer;">View Details</button>
                                </td>
                                <td>
                                    <?php
                                    // Fetch assigned technician's name for this jobcard
                                    $assigned_tech_name = '';
                                    $assigned_stmt = $conn->prepare("SELECT t.fullname FROM assigned_tasks a INNER JOIN technicians t ON a.technician_id = t.id WHERE a.jobcard_id = ?");
                                    $assigned_stmt->bind_param("i", $jobcard['id']);
                                    $assigned_stmt->execute();
                                    $assigned_res = $assigned_stmt->get_result();
                                    if ($assigned_res && $row = $assigned_res->fetch_assoc()) {
                                        $assigned_tech_name = $row['fullname'];
                                    }
                                    $assigned_stmt->close();
                                    echo $assigned_tech_name ? htmlspecialchars($assigned_tech_name) : '<span style="color:#888;">Not Assigned</span>';
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No jobcards found.</p>
            <?php endif; ?>
        </main>
    </div>
    <script>
        const buttons = document.querySelectorAll('.view-details-btn');
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const jobcardId = button.getAttribute('data-jobcard-id');
                window.location.href = 'get_jobcard_details.php?id=' + jobcardId;
            });
        });

        // Removed showUsers() JS
    </script>
</body>
</html>
