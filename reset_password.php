<?php
session_start();
require_once 'connection.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify the token
    $sql = "SELECT * FROM password_resets WHERE token = ? AND expires >= ?";
    $stmt = $con->prepare($sql);
    $expires = date("U");
    $stmt->bind_param("si", $token, $expires);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Token is valid, display reset form
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['password'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the user's password in the database
            $user = $result->fetch_assoc();
            $email = $user['email'];
            $sql = "UPDATE admin SET password = ? WHERE email = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ss", $hashed_password, $email);
            $stmt->execute();

            // Delete the token after use
            $sql = "DELETE FROM password_resets WHERE token = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();

            echo "Your password has been reset!";
            exit();
        }
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reset Password</title>
        </head>
        <body>
            <h2>Reset Your Password</h2>
            <form method="POST">
                <input type="password" name="password" placeholder="New Password" required>
                <button type="submit">Reset Password</button>
            </form>
        </body>
        </html>

        <?php
    } else {
        echo "This token is invalid or has expired.";
    }

    $stmt->close();
    $con->close();
} else {
    echo "No token provided.";
}
?>
