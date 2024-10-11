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
                <li><a href="#" onclick="showLogin('employee')"><i class="fas fa-user-tie">&nbsp; Employee Login</i> </a></li>
                <li><a href="#" onclick="showLogin('password-recovery')"><i class="fas fa-key">&nbsp; Employee Password Recovery</i></a></li>
                <li><a href="#" onclick="showLogin('admin')"><i class="fas fa-user-shield">&nbsp;Admin Login</i></a></li>
            </ul>
        </div>
        <div class="main-content" id="main-content">
            <!-- Content will be dynamically loaded here -->ELMS!
        </div>
    </div>
    <div class="logout-container">
        <a href="#" class="logout-btn" title="Logout" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
    <script>
        function showLogin(type) {
            let content = '';
            if (type === 'employee') {
                content = `
                    <div class="login-container">
                        <h2>Employee Login</h2>
                        <form onsubmit="return login('employee')">
                            <input type="text" id="employee-username" placeholder="Username" required>
                            <input type="password" id="employee-password" placeholder="Password" required>
                            <button type="submit">Login</button>
                        </form>
                        <p>Forgot password? <a href="#" onclick="showLogin('password-recovery')">Reset here</a></p>
                    </div>
                `;
            } else if (type === 'password-recovery') {
                content = `
                    <div class="login-container">
                        <h2>Password Recovery</h2>
                        <form onsubmit="return recoverPassword()">
                            <input type="email" id="recovery-email" placeholder="Email" required>
                            <button type="submit">Reset Password</button>
                        </form>
                        <p>Remember your password? <a href="#" onclick="showLogin('employee')">Login here</a></p>
                    </div>
                `;
            } else if (type === 'admin') {
                content = `
                    <div class="login-container">
                        <h2>Admin Login</h2>
                        <form onsubmit="return login('admin')">
                            <input type="text" id="admin-username" placeholder="Admin Username" required>
                            <input type="password" id="admin-password" placeholder="Admin Password" required>
                            <button type="submit">Login</button>
                        </form>
                    </div>
                `;
            }
            document.getElementById('main-content').innerHTML = content;
        }

        function login(type) {
            let username, password;
            if (type === 'employee') {
                username = document.getElementById('employee-username').value;
                password = document.getElementById('employee-password').value;
            } else if (type === 'admin') {
                username = document.getElementById('admin-username').value;
                password = document.getElementById('admin-password').value;
            }

            // Here you would typically make an AJAX call to your server to verify credentials
            // For demonstration, we'll use a simple check
            if (type === 'employee' && username === 'emp' && password === 'emp123') {
                showEmployeeDashboard();
            } else if (type === 'admin' && username === 'admin' && password === 'admin123') {
                showAdminDashboard();
            } else {
                alert('Invalid credentials');
            }

            return false; // Prevent form submission
        }

        function recoverPassword() {
            const email = document.getElementById('recovery-email').value;
            // Here you would typically make an AJAX call to your server to initiate password recovery
            alert(`Password recovery email sent to ${email}`);
            return false; // Prevent form submission
        }

        function showEmployeeDashboard() {
            document.getElementById('main-content').innerHTML = `
                <h2>Employee Dashboard</h2>
                <p>Welcome, Employee! Here you can manage your leave requests.</p>
                <!-- Add more employee dashboard content here -->
            `;
        }

        function showAdminDashboard() {
            document.getElementById('main-content').innerHTML = `
                <h2>Admin Dashboard</h2>
                <p>Welcome, Admin! Here you can manage employee leave requests and accounts.</p>
                <!-- Add more admin dashboard content here -->
            `;
        }

        function logout() {
            // Here you would typically clear any session data
            document.getElementById('main-content').innerHTML = 'ELMS!';
            showLogin('employee'); // Reset to employee login view
        }
    </script>
</body>
</html>