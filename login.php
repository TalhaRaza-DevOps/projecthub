<?php
session_start();
include 'db_connect.php';

$message = '';
$is_admin_login = false; 

if (isset($_POST['admin_login']) && $_POST['admin_login'] === 'true') {
    $is_admin_login = true;
}
// ... (rest of the PHP login logic remains the same) ...
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uni_id = $conn->real_escape_string($_POST['uni_id']);
    $password = $conn->real_escape_string($_POST['password']);
    
    $sql = "SELECT id, name, password, role FROM users WHERE uni_id = '$uni_id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if ($password === $user['password']) {
            
            if ($is_admin_login && $user['role'] !== 'admin') {
                $message = "Access Denied! This account is not an Admin account.";
            } elseif (!$is_admin_login && $user['role'] === 'admin') {
                $message = "You should use the 'Login as Admin' option to access the Admin Panel.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] == 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: senior_dashboard.php"); 
                }
                exit();
            }

        } else {
            $message = "Invalid Password!";
        }
    } else {
        $message = "User ID not found!";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - University Project Hub</title> <link rel="stylesheet" href="style.css"> 
</head>
<body class="auth-page">
    <div class="login-box auth-box"> 
        <h2>University Project Hub Login</h2> <?php if (!empty($message)) { echo "<p style='color:red;'>$message</p>"; } ?>
        
        <form method="POST" action="">
            <label>University ID:</label>
            <input type="text" name="uni_id" required>
            
            <label>Password:</label>
            <input type="password" name="password" required>
            
            <input type="hidden" name="admin_login" id="admin_login_flag" value="false">
            
            <div class="button-group">
                <button type="button" id="student_submit" class="login-btn active-btn" onclick="setLoginMode('student', true)">Login as Student</button>
                <button type="button" id="admin_submit" class="login-btn" onclick="setLoginMode('admin', true)">Login as Admin</button>
            </div>
        </form>
        
        <p id="admin_mode_text">You are logging in as ADMIN. Be careful!</p>

        <p style="margin-top: 20px;">
            <a href="register.php" class="login-btn-link">New Student Registration</a>
        </p>
    </div>

    <script>
        // ... (JavaScript remains the same) ...
        const loginForm = document.querySelector('.auth-box form');
        const flag = document.getElementById('admin_login_flag');
        const studentBtn = document.getElementById('student_submit');
        const adminBtn = document.getElementById('admin_submit');
        const modeText = document.getElementById('admin_mode_text');

        function setLoginMode(mode, shouldSubmit = false) {
            if (mode === 'admin') {
                flag.value = 'true';
                adminBtn.classList.add('active-btn');
                studentBtn.classList.remove('active-btn');
                modeText.style.display = 'block';
            } else { // Student Mode
                flag.value = 'false';
                studentBtn.classList.add('active-btn');
                adminBtn.classList.remove('active-btn');
                modeText.style.display = 'none';
            }

            if (shouldSubmit) {
                loginForm.submit();
            }
        }
        
        setLoginMode('student');
    </script>
</body>
</html>