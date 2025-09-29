<?php
require_once 'db_connection.php';
// Only select columns that exist
$result = $conn->query("SELECT id, fullname, email, department FROM users ORDER BY id ASC");
if ($result && $result->num_rows > 0) {
    echo '<table style=\'width:100%;font-size:14px;border-collapse:collapse;margin-top:10px;border:1px solid #ccc;\'>';
    echo '<tr style=\'background:#007bff;color:#fff;\'><th>ID</th><th>Full Name</th><th>Email</th><th>Department</th></tr>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td style=\'padding:6px;border-bottom:1px solid #ccc;\'>'.htmlspecialchars($row['id']).'</td>';
        echo '<td style=\'padding:6px;border-bottom:1px solid #ccc;\'>'.htmlspecialchars($row['fullname']).'</td>';
        echo '<td style=\'padding:6px;border-bottom:1px solid #ccc;\'>'.htmlspecialchars($row['email']).'</td>';
        echo '<td style=\'padding:6px;border-bottom:1px solid #ccc;\'>'.htmlspecialchars($row['department']).'</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo 'No users found.';
}
?>
