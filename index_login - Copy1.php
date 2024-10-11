<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
    $password = $_POST["password"];
    $inputUsertype = filter_input(INPUT_POST, "usertype", FILTER_SANITIZE_STRING);

    if (!in_array($inputUsertype, ['admin', 'emp'])) {
        $error = "Invalid user type";
    } else {
        $table = ($inputUsertype === 'admin') ? 'admin' : 'emp';
        $sql = "SELECT * FROM " . $table . " WHERE username = ?";
        $stmt = $con->prepare($sql);
        
        if ($stmt === false) {
            $error = "Database error";
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
				echo "User found: " . print_r($user, true);  // Debug output
                if (password_verify($password, $user['password'])) {
					echo "Password verified";  // Debug output
                    if ($user['usertype'] === $inputUsertype) {
                        $_SESSION["uid"] = $user["id"];
                        $_SESSION["username"] = $user["username"];
                        $_SESSION["usertype"] = $user["usertype"];
                        
                        session_regenerate_id(true);
                        
                        header("Location: " . ($inputUsertype === 'admin' ? 'admin_dash.php' : 'emp_dash.php'));
                        exit();
                    } else {
                        $error = "Invalid user type for this account";
                    }
                } else {
					echo "Password verification failed";  // Debug output
                    $error = "Invalid username or password xx";
                }
            } else {
				  // Debug output
                $error = "Invalid username or password";
				echo "No user found ".$error;
            }

            $stmt->close();
        }
    }

    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELMS | Employee Leave Mgt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="authcss/auth_styles.css">
</head>
<body>
    <header>
        <h4>ELMS | EMPLOYEE LEAVE MGT</h4>
    </header>
    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="?type=emp"><i class="fas fa-user-tie">&nbsp; Employee Login</i></a></li>
                <li><a href="?type=password-recovery"><i class="fas fa-key">&nbsp; Employee Password Recovery</i></a></li>
                <li><a href="?type=admin"><i class="fas fa-user-shield">&nbsp;Admin Login</i></a></li>
            </ul>
        </div>
        <div class="main-content" id="main-content">
            <?php
            if (isset($error)) {
                echo "<p style='color: red;'>$error</p>";
            }

            $type = $_GET['type'] ?? 'emp';

            if ($type === 'emp') {
                ?>
                <div class="login-container">
                    <h2>Employee Login</h2>
                    <form method="POST">
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="hidden" name="usertype" value="emp">
                        <button type="submit">Login</button>
                    </form>
                    <p>Forgot password? <a href="?type=password-recovery">Reset here</a></p>
                </div>
                <?php
            } elseif ($type === 'password-recovery') {
                ?>
                <div class="login-container">
                    <h2>Password Recovery</h2>
                    <form method="POST" action="recover_password.php">
                        <input type="email" name="email" placeholder="Email" required>
                        <button type="submit">Reset Password</button>
                    </form>
                    <p>Remember your password? <a href="?type=emp">Login here</a></p>
                </div>
                <?php
            } elseif ($type === 'admin') {
                ?>
                <div class="login-container">
                    <h2>Admin Login</h2>
                    <form method="POST">
                        <input type="text" name="username" placeholder="Admin Username" required>
                        <input type="password" name="password" placeholder="Admin Password" required>
                        <input type="hidden" name="usertype" value="admin">
                        <button type="submit">Login</button>
                    </form>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</body>
</html>