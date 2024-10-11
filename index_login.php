<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'connection.php'; // Make sure this file exists

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['usertype'])) {
        $username = htmlspecialchars(trim($_POST["username"]));
        $password = $_POST["password"];
        $inputUsertype = htmlspecialchars(trim($_POST["usertype"]));

        if (!in_array($inputUsertype, ['admin', 'emp'])) {
            $error = "Invalid user type";
        } else {
            $table = ($inputUsertype === 'emp') ? 'emp' : 'admin';
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
                    // Verify password
                    if (password_verify($password, $user['password'])) {
                        if ($user['usertype'] === $inputUsertype) {
                            $_SESSION["uid"] = $user["id"];
                            $_SESSION["username"] = $user["username"];
                            $_SESSION["usertype"] = $user["usertype"];
                            
                            session_regenerate_id(true);
                            
                            header("Location: " . ($inputUsertype === 'emp' ? 'emp_dash.php' : 'admin_dash.php'));
                            exit();
                        } else {
                            $error = "Invalid user type for this account";
                        }
                    } else {
                        $error = "Invalid username or password";
                    }
                } else {
                    $error = "Invalid username or password";
                }

                $stmt->close();
            }
        }
    }

    // Password Recovery Handling
    if (isset($_POST['email'])) {
        $email = htmlspecialchars(trim($_POST["email"]));
        
        // Check if email exists in the database
        $sql = "SELECT * FROM admin WHERE email = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Generate a reset token
            $token = bin2hex(random_bytes(50));
            $expires = date("U") + 3600; // 1 hour expiration

            // Store the token in the database
            $sql = "INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssi", $email, $token, $expires);
            $stmt->execute();

            // Send reset email
            $resetLink = "http://yourdomain.com/reset_password.php?token=" . $token; // Update with your domain
            $mail = new PHPMailer;
            // Configure PHPMailer (SMTP settings)
            $mail->setFrom('your_email@example.com', 'Your Name');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click this link to reset your password: $resetLink";
            if ($mail->send()) {
                echo "An email has been sent to your email address.";
            } else {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            echo "Email not found.";
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
    <title>ELMS | Employee Leave Mgt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="authcss/auth_styles.css">
</head>
<body>
    <header>
        <h4>ELMS | LOGIN</h4>
    </header>
    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="?type=emp"><i class="fas fa-user-tie">&nbsp; Employee Login</i></a></li>
                <li><a href="?type=password-recovery"><i class="fas fa-key">&nbsp; Password Recovery</i></a></li>
                <li><a href="?type=admin"><i class="fas fa-user-shield">&nbsp; Admin Login</i></a></li>
            </ul>
			<h4>ELMS</h4>
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
                    <form method="POST">
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
