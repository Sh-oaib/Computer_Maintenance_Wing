<?php
require_once 'db_connection.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $department = trim($_POST['Departments']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phnumber']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate the full name
if (!preg_match('/^[A-Za-z\s]{1,50}$/', $fullname)) {
    $error_message = "Full Name can only contain letters and spaces.";
}

    // Phone number validation (Indian, 10 digits, starts with 6-9) 
    if (!preg_match('/^[6-9]\d{9}$/', $phone_number)) {
        $error_message = "Please enter a valid 10-digit mobile number.";
    } elseif (!empty($fullname) && !empty($department) && !empty($email) && !empty($phone_number) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (fullname, department, email, phone_number, password, is_approved) VALUES (?, ?, ?, ?, ?, 0)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssss", $fullname, $department, $email, $phone_number, $hashedPassword);

                if ($stmt->execute()) {

                    $success_message = "Registration successful! Redirecting to login...";
                    header("refresh:2;url=login.php");
                    exit;
                } else {
                    $error_message = "Error: Could not execute query. " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Error: Could not prepare query. " . $conn->error;
            }
        } else {
            $error_message = "Passwords do not match.";
        }
    } else {
        $error_message = "All fields are required.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .registration-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
        }
        .registration-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .registration-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .registration-container input,
        .registration-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .registration-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .registration-container button:hover {
            background-color: #0056b3;
        }
        .registration-container .link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
        }
        .registration-container .link:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .success-message {
            color: green;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <h2>Registration Page</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form action="registration.php" method="POST">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required maxlength="50" pattern="[A-Za-z\s]{1,50}" title="Only letters and spaces.">
            
            <label for="dropdown">Department:</label>
            <select name="Departments" id="dropdown" required>
                <option value="">Select</option>
                <option value="Department Of Computer Science">Department Of Computer Science</option>
                <option value="Department Of Information Technology">Department Of Information Technology</option>
                <option value="Department Of Mathematical Sciences">Department Of Mathematical Sciences</option>
                <option value="Department Of Arabic">Department Of Arabic</option>
                <option value="Department Of Physics">Department Of Physics</option>
                <option value="Department Of Civil Engineering">Department Of Civil Engineering</option>
                <option value="Department Of Computer Science and Engineering">Department Of Computer Science and Engineering</option>
                <option value="Department Of Electrical Engineering">Department Of Electrical Engineering</option>
                <option value="Department Of Electronics and Communication Engineering">Department Of Electronics and Communication Engineering</option>
                <option value="Department Of Information Technology Engineering">Department Of Information Technology Engineering</option>
                <option value="Department Of Mechanical Engineering">Department Of Mechanical Engineering</option>
                <option value="University Polytechnic">University Polytechnic</option>
                <option value="Centre Of Hospitality and Tourism">Centre Of Hospitality and Tourism</option>
                <option value="Department Of Management and Studies">Department Of Management and Studies</option>
                <option value="Department Of Biotechnology">Department Of Biotechnology</option>
                <option value="Department Of Zoology">Department Of Zoology</option>
                <option value="Department Of Botany">Department Of Botany</option>
                <option value="Centre Of Biodiversity Studies">Centre Of Biodiversity Studies</option>
                <option value="Department Of Education">Department Of Education</option>
                <option value="Department Of Urdu">Department Of Urdu</option>
                <option value="Department Of English">Department Of English</option>
                <option value="Department Of Economics">Department Of Economics</option>
                <option value="Department Of History">Department Of History</option>
                <option value="Department Of Political Studies and International Relations">Department Of Political Studies and International Relations</option>
                <option value="Department Of Islamic Studies">Department Of Islamic Studies</option>
                <option value="Department Of Sociology">Department Of Sociology</option>
            </select>
           
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="phnumber">Phone Number:</label>
            <input type="tel" id="phnumber" name="phnumber" required pattern="[6-9]{1}[0-9]{9}" maxlength="10" title="Enter a valid 10-digit phone number">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirmpass">Confirm Password:</label>
            <input type="password" id="confirmpass" name="confirm_password" required>
            
            <button type="submit">Register</button>
        </form>
        <a href="login.php" class="link">Already have an account? Login</a>
    </div>
</body>
</html>