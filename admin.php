<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="profile">
                <img src="profile-pic.jpg" alt="Profile Picture">
                <h3>Admin Name</h3>
            </div>
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="add_department.php">Department</a>
                    <ul>
                        <li><a href="add_department.php">Add Department</a></li>
                        <li><a href="manage_department.php">Manage Department</a></li>
                    </ul>
                </li>
                <li><a href="#">Leave Type</a>
                    <ul>
                        <li><a href="#">Casual Leave</a></li>
                        <li><a href="#">Official Leave</a></li>
                        <li><a href="#">Sick Leave</a></li>
                    </ul>
                </li>
                <li><a href="#">Employee</a>
                    <ul>
                        <li><a href="#">Add Employee</a></li>
                        <li><a href="#">Manage Employee</a></li>
                    </ul>
                </li>
                <li><a href="#">Change Password</a></li>
                <li><a href="#">Sign Out</a></li>
            </ul>
        </div>
        <div class="main">
            <h1>Welcome to Admin Dashboard</h1>
            <!-- Content goes here -->
        </div>
    </div>
</body>
</html>
