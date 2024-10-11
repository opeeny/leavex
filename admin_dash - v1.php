<?php

session_start();
if(!isset($_SESSION['uid']) || $_SESSION['usertype'] !== 'admin'){
	header("Location: index.php");
}
 ?>
 <?php
require_once 'connection.php';

// Ensure the admin is logged in
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== 'admin') {
    header("Location: index.php"); // Redirect to login if not admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="authcss/auth_styles.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h4>Admin Dashboard</h4>
        <div class="logout-container">
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>
    <div class="container">
        <div class="sidebar">
            <div class="profile">
                <img src="profile-pic.jpg" alt="Profile Picture">
                <h3><?php echo $_SESSION["username"]; ?></h3>
            </div>
            <ul>
                <li>
                    <a href="#">Dashboard</a>
                </li>
                <li>
                    <a href="#">Department <i class="fas fa-caret-down"></i></a>
                    <ul>
                        <li><a href="add_department.php">Add Department</a></li>
                        <li><a href="manage_department.php">Manage Department</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">Leave Type <i class="fas fa-caret-down"></i></a>
                    <ul>
                        <li><a href="#">Casual Leave</a></li>
                        <li><a href="#">Official Leave</a></li>
                        <li><a href="#">Sick Leave</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">Employee <i class="fas fa-caret-down"></i></a>
                    <ul>
                        <li><a href="#">Add Employee</a></li>
                        <li><a href="#">Manage Employee</a></li>
                    </ul>
                </li>
                <li><a href="#">Change Password</a></li>
                <li><a href="#">Sign Out</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>Welcome, Admin!</h1>
            <p>Select an option from the sidebar to manage the system.</p>
        </div>
    </div>
</body>
</html>
