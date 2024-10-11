<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs, // For preventing XSS when outputting to HTML
    $username = htmlspecialchars(trim($_POST["username"]));
    $password = $_POST["password"];
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $first_name = htmlspecialchars(trim($_POST["first_name"]));
    $last_name = htmlspecialchars(trim($_POST["last_name"]));
    $department = htmlspecialchars(trim($_POST["department"]));
    $position = htmlspecialchars(trim($_POST["position"]));
    $hire_date = $_POST["hire_date"];
    $employee_id = htmlspecialchars(trim($_POST["employee_id"]));
    $phone_number = htmlspecialchars(trim($_POST["phone_number"]));
    $address = htmlspecialchars(trim($_POST["address"]));
    $usertype = $_POST["usertype"];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Get current timestamp for created_at
    $created_at = date('Y-m-d H:i:s');
	$last_login = date('Y-m-d H:i:s');
    // Prepare SQL statement based on user type
    if ($usertype === 'admin') {
        $sql = "INSERT INTO admin (username, password, email, first_name, last_name, department, position, hire_date, employee_id, phone_number, address, created_at, last_login, usertype) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    } else {
        $sql = "INSERT INTO emp (username, password, email, first_name, last_name, department, position, hire_date, employee_id, phone_number, address, created_at, last_login, usertype) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    }

    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        $error = "Database error: " . $con->error;
    } else {
        if ($usertype === 'admin') {
            
			$stmt->bind_param("ssssssssssssss", $username, $hashed_password, $email, $first_name, $last_name, $department, $position, $hire_date, $employee_id, $phone_number, $address, $created_at, $last_login, $usertype);
        } else {
            $stmt->bind_param("ssssssssssssss", $username, $hashed_password, $email, $first_name, $last_name, $department, $position, $hire_date, $employee_id, $phone_number, $address, $created_at, $last_login, $usertype);
        }

        if ($stmt->execute()) {
            $success = "User added successfully!";
        } else {
            $error = "Error adding user: " . $stmt->error;
        }

        $stmt->close();
    }

    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELMS | Add User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="authcss/auth_styles.css">
    <style>
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        select, input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <h4>ELMS | EMPLOYEE LEAVE MGT</h4>
    </header>
    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="#"><i class="fas fa-user-plus">&nbsp; Add User</i></a></li>
                <!-- Add other sidebar items as needed -->
            </ul>
        </div>
        <div class="main-content" id="main-content">
            <div class="login-container">
                <h2>Add New User</h2>
                <?php
                if (isset($error)) {
                    echo "<p style='color: red;'>$error</p>";
                }
                if (isset($success)) {
                    echo "<p style='color: green;'>$success</p>";
                }
                ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="usertype">User Type:</label>
                        <select id="usertype" name="usertype" required>
                            <option value="emp">Employee</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group" id="department-group">
                        <label for="department">Department:</label>
                        <input type="text" id="department" name="department">
                    </div>
                    <div class="form-group">
                        <label for="position">Position:</label>
                        <input type="text" id="position" name="position" required>
                    </div>
                    <div class="form-group">
                        <label for="hire_date">Hire Date:</label>
                        <input type="date" id="hire_date" name="hire_date" required>
                    </div>
                    <div class="form-group">
                        <label for="employee_id">Employee ID:</label>
                        <input type="text" id="employee_id" name="employee_id" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number:</label>
                        <input type="tel" id="phone_number" name="phone_number" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" required></textarea>
                    </div>
                    <button type="submit">Add User</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('usertype').addEventListener('change', function() {
            var departmentGroup = document.getElementById('department-group');
            if (this.value === 'admin') {
                departmentGroup.style.display = 'none';
            } else {
                departmentGroup.style.display = 'block';
            }
        });
    </script>
</body>
</html>