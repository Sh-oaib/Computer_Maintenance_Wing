<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);
    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET email = ?, password = ? WHERE role = 'admin'";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $new_email, $hashedPassword);
        if ($stmt->execute()) {
            echo "Credentials updated successfully.";
        } else {
            echo "Error updating credentials.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admin Credentials</title>
</head>
<body>
    <h2>Update Admin Credentials</h2>
    <form action="manage_users.php" method="POST">
        <label for="email">New Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Update</button>
    </form>
</body>
</html>