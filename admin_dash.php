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

// Fetch departments for the employee form
$departments = $con->query("SELECT * FROM department");

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['form_type'])) {
        switch ($_POST['form_type']) {
            case 'add_department':
                $dept_name = $con->real_escape_string($_POST['department_name']);
                $dept_code = $con->real_escape_string($_POST['department_code']);
                $location = $con->real_escape_string($_POST['location']);
                
                $sql = "INSERT INTO department (department_name, department_code, location) VALUES (?, ?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('sss', $dept_name, $dept_code, $location);
                if ($stmt->execute()) {
                    $success_message = "Department added successfully!";
                } else {
                    $error_message = "Error adding department: " . $con->error;
                }
                $stmt->close();
                break;

            case 'add_leave_type':
                $leave_type = $con->real_escape_string($_POST['leave_type']);
                $description = $con->real_escape_string($_POST['description']);
                $sql = "INSERT INTO leave_types (leave_type, description) VALUES (?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('ss', $leave_type, $description);
                if ($stmt->execute()) {
                    $success_message = "Leave type added successfully!";
                } else {
                    $error_message = "Error adding leave type: " . $con->error;
                }
                $stmt->close();
                break;

            case 'add_employee':
                $ra = $con->real_escape_string($_POST['ra']);
                $rank = $con->real_escape_string($_POST['rank']);
                $fname = $con->real_escape_string($_POST['fname']);
                $lname = $con->real_escape_string($_POST['lname']);
                $email = $con->real_escape_string($_POST['email']);
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];
                $gender = $con->real_escape_string($_POST['gender']);
                $birthday = NULL;
                if (!empty($_POST['birthday'])) {
                    $birthday_obj = DateTime::createFromFormat('Y-m-d', $_POST['birthday']);
                    if ($birthday_obj !== false) {
                        $birthday = $birthday_obj->format('Y-m-d');
                    }
                }
                $dept = $con->real_escape_string($_POST['dept']);
                $address = $con->real_escape_string($_POST['address']);
                $telephone = $con->real_escape_string($_POST['telephone']);

                if ($password !== $confirm_password) {
                    $error_message = "Passwords do not match.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    $sql = "INSERT INTO solider (ra, rank, fname, lname, email, password, gender, birthday, dept, address, telephone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param('sssssssssss', $ra, $rank, $fname, $lname, $email, $hashed_password, $gender, $birthday, $dept, $address, $telephone);
                    if ($stmt->execute()) {
                        $success_message = "User added successfully!";
                    } else {
                        $error_message = "Error adding employee: " . $con->error;
                    }
                    $stmt->close();
                }
                break;

            case 'password_change':
				
				 // Ensure 'username' is set in the session
				if (!isset($_SESSION['username'])) {
					header("Location: login.php"); // Redirect to login if no 'username' exists
					exit();
				}
                $current_password = $_POST['password'];
                $new_password = $_POST['npassword'];
                $confirm_password = $_POST['confirm_password'];
				$username = $_SESSION['username'];
                //$user_id = $_SESSION['user_id']; // Ensure this is set when the user logs in
                //$sql = "SELECT password FROM admin WHERE id = ?";
				$sql = "SELECT id, password FROM admin WHERE username = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();

                if ($user && password_verify($current_password, $user['password'])) {
                    if ($new_password === $confirm_password) {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $sql = "UPDATE admin SET password = ? WHERE id = ?";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param('si', $hashed_password, $user['id']);
                        if ($stmt->execute()) {
                            $success_message = "Password changed successfully!";
                        } else {
                            $error_message = "Error changing password: " . $con->error;
                        }
                        $stmt->close();
                    } else {
                        $error_message = "New password and confirm password do not match.";
                    }
                } else {
                    $error_message = "Current password is incorrect.";
                }
                break;
        }
    }
}

$con->close();

// Determine which section to display
$active_section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

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
            <span class="notification-count"><a href="signup.php">1</a></span>
        </div>
    </header>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="avatars/avatar5.png" alt="Profile Picture">
                <h3><?php echo htmlspecialchars($_SESSION["username"]); ?></h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="?section=dashboard" class="<?php echo $active_section == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li>
                        <a href="#" class="dropdown-toggle"><i class="fas fa-building"></i> Department <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu <?php echo in_array($active_section, ['add_department', 'manage_departments']) ? 'active' : ''; ?>">
                            <li><a href="?section=add_department" class="<?php echo $active_section == 'add_department' ? 'active' : ''; ?>">Add Department</a></li>
                            <li><a href="?section=manage_departments" class="<?php echo $active_section == 'manage_departments' ? 'active' : ''; ?>">Manage Departments</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle"><i class="fas fa-calendar-alt"></i> Leave Type <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu <?php echo in_array($active_section, ['add_leave_type', 'manage_leave_types']) ? 'active' : ''; ?>">
                            <li><a href="?section=add_leave_type" class="<?php echo $active_section == 'add_leave_type' ? 'active' : ''; ?>">Add Leave Type</a></li>
                            <li><a href="?section=manage_leave" class="<?php echo $active_section == 'manage_leave' ? 'active' : ''; ?>">Manage Leave Types</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle"><i class="fas fa-users"></i> Employees <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu <?php echo in_array($active_section, ['add_employee', 'manage_employees']) ? 'active' : ''; ?>">
                            <li><a href="?section=add_employee" class="<?php echo $active_section == 'add_employee' ? 'active' : ''; ?>">Add Employee</a></li>
                            <li><a href="?section=manage_employees" class="<?php echo $active_section == 'manage_employees' ? 'active' : ''; ?>">Manage Employees</a></li>
                        </ul>
                    </li>
                     <li><a href="?section=leave_requests" class="<?php echo $active_section == 'leave_requests' ? 'active' : ''; ?>"><i class="fas fa-clock"></i>Leave Requests</a></li>
                    <li><a href="?section=password_change" class="<?php echo $active_section == 'password_change' ? 'active' : ''; ?>"><i class="fas fa-key"></i>Password Change</a></li>
                    <li><a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if ($active_section == 'dashboard'): ?>
                <div id="dashboard" class="content-section active">
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
                </div>
            <?php elseif ($active_section == 'manage_employees'): ?>
                <div id="manage-employees" class="content-section active">
                    <div class="dashboard-stats">
                        <div class="stat-card">
                            <i class="fas fa-users"></i>
                            <h3>M.E</h3>
                            <p>Manage employees</p>
                        </div>   
                    </div>
                </div>
            <?php elseif ($active_section == 'manage_leave'): ?>
                <div id="manage_leave" class="content-section active">
                    <div class="dashboard-stats">
                        <div class="stat-card">
                            <i class="fas fa-users"></i>
                            <h3>ML</h3>
                            <p>Manage Leave</p>
                        </div>   
                    </div>
                 </div>
            <?php elseif ($active_section == 'add_department'): ?>
                <div id="add-department" class="content-section active">
                    <h2>Add Department</h2>
                    <form method="POST">
                        <input type="hidden" name="form_type" value="add_department">
                        <input type="text" name="department_name" placeholder="Department Name" required>
                        <input type="text" name="department_code" placeholder="Department Code" required>
                        <input type="text" name="location" placeholder="Location" required>
                        <button type="submit">Add Department</button>
                    </form>
                </div>
            <?php elseif ($active_section == 'add_leave_type'): ?>
                <div id="add-leave-type" class="content-section active">
                    <h2>Add Leave Type</h2>
                    <form method="POST">
                        <input type="hidden" name="form_type" value="add_leave_type">
                        <input type="text" name="leave_type" placeholder="Leave Type" required>
                        <textarea name="description" placeholder="Description" required></textarea>
                        <button type="submit">Add Leave Type</button>
                    </form>
                </div>
            <?php elseif ($active_section == 'add_employee'): ?>
                <div id="add-employee" class="content-section active">
                    <h2>Add Employee</h2>
                    <form method="POST">
                        <input type="hidden" name="form_type" value="add_employee">
						<input type="text" name="ra" placeholder="RA Number" required>
						<input type="text" name="rank" placeholder="Rank" required>
                        <input type="text" name="fname" placeholder="First Name" required>
                        <input type="text" name="lname" placeholder="Last Name" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <input type="date" name="birthday" required>
                        <select name="dept" required>
                            <option value="">Select Department</option>
                            <?php 
                            $departments->data_seek(0);
                            while($row = $departments->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $row['dept_id']; ?>"><?php echo $row['department_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                        <textarea name="address" placeholder="Address" required></textarea>
                        <input type="tel" name="telephone" placeholder="Telephone" required>
                        <button type="submit">Add Employee</button>
                    </form>
                </div>
				<?php elseif ($active_section == 'manage_departments'): ?>
				<div id="leave-requests" class="content-section active">
					<h2>Manage Departments</h2>
				</div>
				<?php elseif ($active_section == 'leave_requests'): ?>
				<div id="leave-requests" class="content-section active">
					<h2>Leave Requests</h2>
				</div>
				<?php elseif ($active_section == 'password_change'): ?>
				
				<div id="change-password" class="content-section active">
				<h2>Password Change</h2>
				<form method="POST">
					<input type="hidden" name="form_type" value="password_change">
					<input type="password" name="password" placeholder="Current Password" required>
					<input type="password" name="npassword" placeholder="New Password" required>
					<input type="password" name="confirm_password" placeholder="Confirm Password" required>
					<button type="submit">Change</button>
				</form>
				</div>
			<?php endif; ?>
        </main>
    </div>
    <script>
        // Simple JavaScript to toggle dropdown menus
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                const menu = toggle.nextElementSibling;
                menu.classList.toggle('active');
            });
        });
    </script>
</body>
</html>