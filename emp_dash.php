<?php 

session_start();
if(!isset($_SESSION['uid']) || $_SESSION['usertype'] !== 'emp'){
	header("Location: emp_dash.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard_styles.css">
</head>
<body>
    <header>
        <h1>Employee Dashboard</h1>
		<div class="logout-container">
        <a href="#" class="logout-btn" title="Logout" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i>
        </a>
		</div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>
    <main>
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        <!-- Add Employee-specific content here -->
    </main>
</body>
</html>