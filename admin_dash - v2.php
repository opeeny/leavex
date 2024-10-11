<?php
session_start();
require_once 'connection.php';
// Ensure the user is logged in as an admin
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch some basic stats for the dashboard
$total_employees = $con->query("SELECT COUNT(*) as count FROM emp")->fetch_assoc()['count'];
$total_departments = $con->query("SELECT COUNT(*) as count FROM department")->fetch_assoc()['count'];
$total_leave_requests = $con->query("SELECT COUNT(*) as count FROM leave_requests")->fetch_assoc()['count'];

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ELMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="authcss/admin_styles.css">
</head>
<body>
    <header>
        <h4><i class="fas fa-chevron-right"></i>ELMS | ADMIN<i class="fas fa-chevron-left"></i></h4>
        <div class="notification-icon">
            <i class="fas fa-bell"></i>
            <span class="notification-count">1</span> <!-- Example notification count -->
        </div>
    </header>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="avatars/avatar5.png" alt="Profile Picture">
                <h3><?php echo $_SESSION["username"]; ?></h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="admin_dash.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li>
                        <a href="#" class="dropdown-toggle"><i class="fas fa-building"></i> Department <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="add_department.php">Add Department</a></li>
                            <li><a href="manage_departments.php">Manage Departments</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle"><i class="fas fa-calendar-alt"></i> Leave Type <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="add_leave_type.php">Add Leave Type</a></li>
                            <li><a href="manage_leave_types.php">Manage Leave Types</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle"><i class="fas fa-users"></i> Employees <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="add_employee.php">Add Employee</a></li>
                            <li><a href="manage_employees.php">Manage Employees</a></li>
                        </ul>
                    </li>
                    <li><a href="leave_requests.php"><i class="fas fa-clock"></i> Leave Requests</a></li>
                    <li><a href="change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
                    <li><a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <div class="dashboard-stats">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3>Total Employees</h3>
                    <p><?php echo $total_employees; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-building"></i>
                    <h3>Departments</h3>
                    <p><?php echo $total_departments; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <h3>Leave Requests</h3>
                    <p><?php echo $total_leave_requests; ?></p>
                </div>
            </div>
            <!-- Add more dashboard content here -->
        </main>
    </div>
    <script src="js/admin_script.js"></script>
</body>
</html>
