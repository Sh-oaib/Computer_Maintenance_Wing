<?php
require_once 'db_connection.php';

// Fetch the stored hash from the database
$sql = "SELECT password FROM users WHERE role = 'admin'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $stored_hash = $row['password']; // The hashed password from the database

    // The password you want to verify
    $input_password = 'admin123'; // Replace with the password you are testing

    // Verify the password
    if (password_verify($input_password, $stored_hash)) {
        echo "Password matches!";
    } else {
        echo "Password does not match!";
    }
} else {
    echo "Admin user not found.";
}

$conn->close();
?>