<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $equipment = $_POST['equipment'] ?? null;
    $equipment_make = $_POST['equipment_make'] ?? null;
    $out_of_warranty = $_POST['out_of_warranty'] ?? null;
    $out_of_warranty_details = $_POST['out_of_warranty_details'] ?? null;
    $under_amc = $_POST['under_amc'] ?? null;
    $under_amc_details = $_POST['under_amc_details'] ?? null;
    $description = $_POST['description'] ?? null;

    // Debugging output (optional, remove in production)
    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";

    // Validate the data
    if (
        empty($equipment) ||
        empty($equipment_make) ||
        empty($out_of_warranty) ||
        empty($under_amc) ||
        empty($description)
    ) {
        die("Please fill in all required fields.");
    }

    // Prepare the SQL query to insert the data
    $sql = "INSERT INTO jobcards (user_id, equipment, equipment_make, out_of_warranty, out_of_warranty_details, under_amc, under_amc_details, description, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $pending_status = 'Pending';
    $stmt->bind_param(
        "issssssss",
        $user_id,
        $equipment,
        $equipment_make,
        $out_of_warranty,
        $out_of_warranty_details,
        $under_amc,
        $under_amc_details,
        $description,
        $pending_status
    );

    // Execute the query
    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    // Redirect to success page
    header("Location: success_page.php");
    exit;
} else {
    echo "This script only handles POST requests.<br>";
}
?>