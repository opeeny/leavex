<?php
session_start();
if (isset($_SESSION['uid'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header("Location: admin_dash.php");
    } else {
        header("Location: emp_dash.php");
    }
    exit();
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
                <li><a href="#" onclick="showLogin('employee')"><i class="fas fa-user-tie">&nbsp; Employee Login</i> </a></li>
                <li><a href="#" onclick="showLogin('password-recovery')"><i class="fas fa-key">&nbsp; Employee Password Recovery</i></a></li>
                <li><a href="#" onclick="showLogin('admin')"><i class="fas fa-user-shield">&nbsp;Admin Login</i></a></li>
            </ul>
        </div>
        <div class="main-content" id="main-content">
            <!-- Content will be dynamically loaded here -->ELMS!
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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

       function login(userType) {
		let username, password;
		if (userType === 'employee') {
			username = document.getElementById('employee-username').value;
			password = document.getElementById('employee-password').value;
		} else {
			username = document.getElementById('admin-username').value;
			password = document.getElementById('admin-password').value;
		}

		$.ajax({
			url: 'login.php',
			type: 'POST',
			data: {
				username: username,
				password: password,
				userType: userType
			},
			dataType: 'json',
			success: function(response) {
				console.log("Success response:", response);
				if (response.success) {
					if (response.userType === 'admin') {
						window.location.href = 'admin_dash.php';
					} else {
						window.location.href = 'emp_dash.php';
					}
				} else {
					alert(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log("Error details:", jqXHR.responseText, textStatus, errorThrown);
				alert('An error occurred. Please try again.');
			}
		});

		return false;
	}
function login(userType) {
    let username, password;
    if (userType === 'employee') {
        username = document.getElementById('employee-username').value;
        password = document.getElementById('employee-password').value;
    } else {
        username = document.getElementById('admin-username').value;
        password = document.getElementById('admin-password').value;
    }

    $.ajax({
        url: 'login.php',
        type: 'POST',
        data: {
            username: username,
            password: password,
            userType: userType
        },
        dataType: 'json',
        success: function(response) {
            console.log("Success response:", response);
            if (response.success) {
                if (response.userType === 'admin') {
                    window.location.href = 'admin_dash.php';
                } else {
                    window.location.href = 'emp_dash.php';
                }
            } else {
                alert(response.message);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Error details:", jqXHR.responseText, textStatus, errorThrown);
            alert('An error occurred. Please try again.');
        }
    });

    return false;
}
        function recoverPassword() {
            // Implement password recovery logic here
            alert('Password recovery functionality not implemented yet.');
            return false;
        }

        function logout() {
            // Implement logout logic here
            alert('Logout functionality not implemented yet.');
        }
    </script>
</body>
</html>