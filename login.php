<?php
require_once 'db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username_or_email) && !empty($password)) {
        // Query to check if the user exists with the provided email/username in users table
        $sql = "SELECT * FROM users WHERE (fullname = ? OR email = ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $username_or_email, $username_or_email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        if (isset($user['is_approved']) && $user['is_approved'] == 1) {
            // Store user information in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = isset($user['role']) ? $user['role'] : 'user';

            // Redirect based on role
            if (isset($user['role']) && $user['role'] === 'admin') {
                header("Location: admin_panel.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error_message = "Your account is pending approval by admin.";
        }
    } else {
        $error_message = "Invalid password.";
    }
} else {
                // If not found in users table, check admins table
                $stmt->close();
                $sql_admin = "SELECT * FROM admins WHERE name = ?";
                if ($stmt_admin = $conn->prepare($sql_admin)) {
                    $stmt_admin->bind_param("s", $username_or_email);
                    $stmt_admin->execute();
                    $result_admin = $stmt_admin->get_result();

                    if ($result_admin->num_rows == 1) {
                        $admin = $result_admin->fetch_assoc();
                        // Debug logs for admin login
                        error_log("Admin login attempt: name=" . $admin['name'] . ", hash=" . $admin['password']);
                        if (password_verify($password, $admin['password'])) {
                            // Store admin information in session
                            $_SESSION['user_id'] = $admin['id'];
                            $_SESSION['email'] = null;
                            $_SESSION['role'] = 'admin';

                            header("Location: admin_panel.php");
                            exit;
                        } else {
                            $error_message = "Invalid password.";
                        }
                    } else {
                        $error_message = "No user found.";
                    }
                    $stmt_admin->close();
                } else {
                    $error_message = "Error: Could not prepare query.";
                }
                // Removed duplicate $stmt->close() here to avoid closing already closed statement
            }
            // Check if user is a technician
            $sql_tech = "SELECT * FROM technicians WHERE (fullname = ? OR email = ? OR phone_number = ?)";
            if ($stmt_tech = $conn->prepare($sql_tech)) {
                error_log("Technician login attempt: username_or_email='" . $username_or_email . "'");
                $stmt_tech->bind_param("sss", $username_or_email, $username_or_email, $username_or_email);
                $stmt_tech->execute();
                $result_tech = $stmt_tech->get_result();
                error_log("Technician login query returned rows: " . $result_tech->num_rows);

                // Log all technician emails for debugging
                $res_all_techs = $conn->query("SELECT email FROM technicians");
                if ($res_all_techs) {
                    while ($row = $res_all_techs->fetch_assoc()) {
                        error_log("Technician email in DB: '" . $row['email'] . "'");
                    }
                }

                if ($result_tech->num_rows == 1) {
                    $tech = $result_tech->fetch_assoc();
                    error_log("Technician record fetched: id=" . $tech['id'] . ", email=" . $tech['email'] . ", fullname=" . $tech['fullname']);
                    if (password_verify($password, $tech['password'])) {
                        // Store technician info in session
                        $_SESSION['user_id'] = $tech['id'];
                        $_SESSION['email'] = $tech['email'];
                        $_SESSION['role'] = 'technician';
                        $_SESSION['technician_id'] = $tech['id']; // Fix: set technician_id for dashboard

                        header("Location: technician_dashboard.php");
                        exit;
                    } else {
                        $error_message = "Invalid password.";
                    }
                } else {
                    $error_message = "No user found.";
                }
                $stmt_tech->close();
            } else {
                $error_message = "Error: Could not prepare query.";
            }
        } else {
            $error_message = "Error: Could not prepare query.";
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .login-container {
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
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .login-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .login-container .link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
        }
        .login-container .link:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="username">Username or Email:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>