<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'technician'])) {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

require_once 'db_connection.php';

// Fetch technicians for dropdown
$technicians = [];
$tech_result = $conn->query("SELECT id, fullname FROM technicians");
if ($tech_result && $tech_result->num_rows > 0) {
    while ($row = $tech_result->fetch_assoc()) {
        $technicians[] = $row;
    }
}

$action_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobcard_id = $_POST['jobcard_id'];
    $action = $_POST['action'] ?? '';
    $comments = $_POST['comments'] ?? '';
    $changed_by = $_SESSION['username'] ?? 'admin';
    $selected_technician = $_POST['technician_id'] ?? null;

    // Make comments optional: if empty string, set to NULL
    if (trim($comments) === '') {
        $comments = null;
    }

    // Approve/Reject logic
    if ($action === 'approve' || $action === 'reject') {
        $new_status = $action === 'approve' ? 'approved' : 'rejected';
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

        // Set message for display
        $action_message = $action === 'approve'
            ? "Jobcard has been approved."
            : "Jobcard has been rejected.";
    }

    // Assign task logic
    if ($action === 'assign_task' && $selected_technician) {
        // Insert assignment into assigned_tasks table
        $stmt3 = $conn->prepare("INSERT INTO assigned_tasks (jobcard_id, technician_id, assigned_by, assigned_at) VALUES (?, ?, ?, NOW())");
        $stmt3->bind_param("iis", $jobcard_id, $selected_technician, $changed_by);
        $stmt3->execute();
        $stmt3->close();
        $action_message = "Task assigned to technician successfully.";
    }

    // Status change logic (only for assigned technician)
    if ($action === 'change_status' && isset($_POST['new_status']) && $_SESSION['role'] === 'technician') {
        $new_status = $_POST['new_status'];
        $tech_id = $_SESSION['technician_id'] ?? null;
        // Check if technician is assigned to this jobcard
        $assigned_stmt = $conn->prepare("SELECT technician_id FROM assigned_tasks WHERE jobcard_id = ?");
        $assigned_stmt->bind_param("i", $jobcard_id);
        $assigned_stmt->execute();
        $assigned_res = $assigned_stmt->get_result();
        $is_assigned_tech = false;
        if ($assigned_res && $row = $assigned_res->fetch_assoc()) {
            if ($row['technician_id'] == $tech_id) {
                $is_assigned_tech = true;
            }
        }
        $assigned_stmt->close();

        // Only allow valid transitions
        $valid_transitions = [
            'approved' => 'processing',
            'processing' => 'complete'
        ];
        // Get current status
        $status_stmt = $conn->prepare("SELECT status FROM jobcards WHERE id = ?");
        $status_stmt->bind_param("i", $jobcard_id);
        $status_stmt->execute();
        $status_res = $status_stmt->get_result();
        $current_status = ($status_res && $row = $status_res->fetch_assoc()) ? $row['status'] : '';
        $status_stmt->close();

        if ($is_assigned_tech && isset($valid_transitions[$current_status]) && $valid_transitions[$current_status] === $new_status) {
            // Update jobcard status
            $stmt = $conn->prepare("UPDATE jobcards SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $new_status, $jobcard_id);
            $stmt->execute();
            $stmt->close();

            // Insert into complaint_history
            $stmt2 = $conn->prepare("INSERT INTO complaint_history (jobcard_id, status, changed_by, comments) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("isss", $jobcard_id, $new_status, $changed_by, $comments);
            $stmt2->execute();
            $stmt2->close();

            $action_message = "Status updated successfully.";
        } else {
            $action_message = "Invalid status transition or not assigned technician.";
        }
    }
} else {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo "Invalid jobcard ID.";
        exit;
    }

    $jobcard_id = intval($_GET['id']);
}

// Fetch jobcard details
$stmt = $conn->prepare("SELECT j.id, j.equipment, j.equipment_make, j.out_of_warranty, j.out_of_warranty_details, j.under_amc, j.under_amc_details, j.description, j.Submission_Datetime AS date, u.fullname AS user_name FROM jobcards j LEFT JOIN users u ON j.user_id = u.id WHERE j.id = ?");
$stmt->bind_param("i", $jobcard_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Jobcard not found.";
    exit;
}

$jobcard = $result->fetch_assoc();

?>

<h2 style="font-size: 24px;">Jobcard Details (ID: <?php echo htmlspecialchars($jobcard['id']); ?>)</h2>
<table style="font-size: 25px; border-collapse: collapse; width: 100%; margin-bottom: 15px; border: 1px solid #ccc;">
    <tr style="border-bottom: 1px solid #ccc;">
        <td style="padding: 8px; font-weight: bold; width: 200px; border-right: 1px solid #ccc;">User:</td>
        <td style="padding: 8px;"><?php echo htmlspecialchars($jobcard['user_name']); ?></td>
    </tr>
    <tr style="border-bottom: 1px solid #ccc;">
        <td style="padding: 8px; font-weight: bold; border-right: 1px solid #ccc;">Equipment:</td>
        <td style="padding: 8px;"><?php echo htmlspecialchars($jobcard['equipment']); ?></td>
    </tr>
    <tr style="border-bottom: 1px solid #ccc;">
        <td style="padding: 8px; font-weight: bold; border-right: 1px solid #ccc;">Make:</td>
        <td style="padding: 8px;"><?php echo htmlspecialchars($jobcard['equipment_make']); ?></td>
    </tr>
    <tr style="border-bottom: 1px solid #ccc;">
        <td style="padding: 8px; font-weight: bold; border-right: 1px solid #ccc;">Out of Warranty:</td>
        <td style="padding: 8px;"><?php echo htmlspecialchars($jobcard['out_of_warranty']); ?></td>
    </tr>
    <tr style="border-bottom: 1px solid #ccc;">
        <td style="padding: 8px; font-weight: bold; border-right: 1px solid #ccc;">Warranty Details:</td>
        <td style="padding: 8px;"><?php echo htmlspecialchars($jobcard['out_of_warranty_details']); ?></td>
    </tr>
    <tr style="border-bottom: 1px solid #ccc;">
        <td style="padding: 8px; font-weight: bold; border-right: 1px solid #ccc;">Under AMC:</td>
        <td style="padding: 8px;"><?php echo htmlspecialchars($jobcard['under_amc']); ?></td>
    </tr>
    <tr>
        <td style="padding: 8px; font-weight: bold; border-right: 1px solid #ccc;">AMC Details:</td>
        <td style="padding: 8px;"><?php echo htmlspecialchars($jobcard['under_amc_details']); ?></td>
    </tr>
</table>
<p style="font-size: 25px; margin-bottom: 0;"><strong>Description of job/fault:</strong></p>
<textarea readonly style="width: 100%; height: 100px; resize: none; border-radius: 4px; border: 1px solid #ccc; padding: 5px; font-family: Arial, sans-serif; font-size: 14px;"><?php echo htmlspecialchars($jobcard['description']); ?></textarea>
<p><strong>Date:</strong> 
<?php 
    $datetime = new DateTime($jobcard['date']);
    echo $datetime->format('Y-m-d / h:i A');
?>
</p>

<form method="POST" action="get_jobcard_details.php?id=<?php echo urlencode($jobcard['id']); ?>" style="margin-top: 20px; max-width: 600px;" id="approveRejectForm">
    <?php if (!empty($action_message)): ?>
        <div style="color: #007bff; font-weight: bold; margin-bottom: 10px;">
            <?php echo htmlspecialchars($action_message); ?>
        </div>
    <?php endif; ?>
    <?php
    // Show approve/reject/comment for admin if not assigned
    // Re-check assignment and approval status
    $is_approved = false;
    $is_assigned = false;
    $status_check = $conn->prepare("SELECT status FROM jobcards WHERE id = ?");
    $status_check->bind_param("i", $jobcard['id']);
    $status_check->execute();
    $status_result = $status_check->get_result();
    if ($status_result && $row = $status_result->fetch_assoc()) {
        $is_approved = ($row['status'] === 'approved');
    }
    $status_check->close();
    // Check if already assigned
    $assign_check = $conn->prepare("SELECT id FROM assigned_tasks WHERE jobcard_id = ?");
    $assign_check->bind_param("i", $jobcard['id']);
    $assign_check->execute();
    $assign_result = $assign_check->get_result();
    if ($assign_result && $assign_result->num_rows > 0) {
        $is_assigned = true;
    }
    $assign_check->close();

    if ($_SESSION['role'] === 'admin' && !$is_assigned): ?>
        <input type="hidden" name="jobcard_id" value="<?php echo htmlspecialchars($jobcard['id']); ?>" /> <br>
        <button type="submit" name="action" value="approve" id="approveBtn" style="padding: 8px 16px; font-size: 14px; border: none; border-radius: 4px; color: #fff; cursor: pointer; margin-right: 10px; width: 120px; background-color: #28a745;">Approve</button>
        <button type="submit" name="action" value="reject" id="rejectBtn" style="padding: 8px 16px; font-size: 14px; border: none; border-radius: 4px; color: #fff; cursor: pointer; width: 120px; background-color: #dc3545;">Reject</button><br><br>
        <textarea name="comments" placeholder="Note (optional)" style="width: 42%; min-height: 40px; resize: vertical; border-radius: 4px; border: 1px solid #ccc; padding: 0px; font-size: 14px; margin-bottom: 10px;"></textarea>
    <?php endif; ?>
</form>

<?php if ($is_approved && !$is_assigned): ?>
    <form method="POST" action="get_jobcard_details.php?id=<?php echo urlencode($jobcard['id']); ?>" style="margin-top: 20px; max-width: 600px;">
        <input type="hidden" name="jobcard_id" value="<?php echo htmlspecialchars($jobcard['id']); ?>" />
        <select name="technician_id" id="technician_id" required style="padding: 8px; font-size: 14px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px; width: 200px;">
            <option value="">Select Technician</option>
            <?php foreach ($technicians as $tech): ?>
                <option value="<?php echo htmlspecialchars($tech['id']); ?>"><?php echo htmlspecialchars($tech['fullname']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="action" value="assign_task" style="padding: 8px 16px; font-size: 14px; border: none; border-radius: 4px; color: #fff; cursor: pointer; background-color: #007bff;">Assign Task</button>
    </form>
<?php elseif ($is_assigned): ?>
    <?php
    // Fetch assigned technician's name
    $assigned_tech_name = '';
    $assigned_stmt = $conn->prepare("SELECT t.fullname FROM assigned_tasks a INNER JOIN technicians t ON a.technician_id = t.id WHERE a.jobcard_id = ?");
    $assigned_stmt->bind_param("i", $jobcard['id']);
    $assigned_stmt->execute();
    $assigned_res = $assigned_stmt->get_result();
    if ($assigned_res && $row = $assigned_res->fetch_assoc()) {
        $assigned_tech_name = $row['fullname'];
    }
    $assigned_stmt->close();
    ?>
    <div style="color: #007bff; font-weight: bold;">Assigned Technician: <span style="color:#333; font-weight:normal;"><?php echo htmlspecialchars($assigned_tech_name); ?></span></div>
    <!-- Status Change Form for Assigned Technician -->
    <?php
    // Check if current user is the assigned technician
    $show_tech_status_form = false;
    if ($_SESSION['role'] === 'technician') {
        $tech_id = $_SESSION['technician_id'] ?? null;
        $assigned_stmt = $conn->prepare("SELECT technician_id FROM assigned_tasks WHERE jobcard_id = ?");
        $assigned_stmt->bind_param("i", $jobcard['id']);
        $assigned_stmt->execute();
        $assigned_res = $assigned_stmt->get_result();
        if ($assigned_res && $row = $assigned_res->fetch_assoc()) {
            if ($row['technician_id'] == $tech_id) {
                $show_tech_status_form = true;
            }
        }
        $assigned_stmt->close();
    }

    // Get current status
    $status_stmt = $conn->prepare("SELECT status FROM jobcards WHERE id = ?");
    $status_stmt->bind_param("i", $jobcard['id']);
    $status_stmt->execute();
    $status_res = $status_stmt->get_result();
    $current_status = ($status_res && $row = $status_res->fetch_assoc()) ? $row['status'] : '';
    $status_stmt->close();

    // Only show status change if not already 'complete' and technician is assigned
    if ($show_tech_status_form && $current_status !== 'complete'):
    ?>
    <form method="POST" action="get_jobcard_details.php?id=<?php echo urlencode($jobcard['id']); ?>" style="margin-top: 20px; max-width: 600px;">
        <input type="hidden" name="jobcard_id" value="<?php echo htmlspecialchars($jobcard['id']); ?>" />
        <select name="new_status" required style="padding: 8px; font-size: 14px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px; width: 200px;">
            <option value="">Change Status</option>
            <?php if ($current_status === 'approved'): ?>
                <option value="processing">Processing</option>
            <?php endif; ?>
            <?php if ($current_status === 'processing'): ?>
                <option value="complete">Complete</option>
            <?php endif; ?>
        </select>
        <button type="submit" name="action" value="change_status" style="padding: 8px 16px; font-size: 14px; border: none; border-radius: 4px; color: #fff; cursor: pointer; background-color: #28a745;">Update Status</button>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <textarea name="comments" placeholder="Note (optional)" style="width: 42%; min-height: 40px; resize: vertical; border-radius: 4px; border: 1px solid #ccc; padding: 0px; font-size: 14px; margin-bottom: 10px;"></textarea>
        <?php endif; ?>
    </form>
    <?php endif; ?>
<?php endif; ?>

<!-- Back to Home Button: admin goes to admin_panel.php, technician goes to technician_dashboard.php -->
<div style="position: fixed; bottom: 30px; right: 30px; z-index: 999;">
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="admin_panel.php" style="padding: 12px 28px; background-color: #007bff; margin-right: 100px; color: #fff; border-radius: 6px; font-size: 18px; text-decoration: none; box-shadow: 0 2px 8px rgba(0,0,0,0.12);">Home</a>
    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'technician'): ?>
        <a href="technician_dashboard.php" style="padding: 12px 28px; background-color: #007bff; margin-right: 100px; color: #fff; border-radius: 6px; font-size: 18px; text-decoration: none; box-shadow: 0 2px 8px rgba(0,0,0,0.12);">Home</a>
    <?php endif; ?>
</div>