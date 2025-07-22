<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        try {
            // Debug: Print the username and password being used
            error_log("Login attempt - Username: " . $username);
            error_log("Login attempt - Password: " . $password);
            
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user) {
                error_log("User found in database");
                error_log("Stored hash from database: " . $user['password']);
                
                // Debug: Print the exact password being verified
                error_log("Attempting to verify password: " . $password);
                
                // Debug: Print the verification result
                $verify = password_verify($password, $user['password']);
                error_log("Password verification result: " . ($verify ? 'true' : 'false'));
                
                // Debug: Print the hash info
                error_log("Hash info: " . print_r(password_get_info($user['password']), true));
                
                if ($verify) {
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid password';
                    error_log("Password verification failed");
                }
            } else {
                $error = 'User not found';
                error_log("No user found with username: " . $username);
            }
        } catch(PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
        }
    } else {
        $error = 'Please fill all fields';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sport11</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            background: #e9eef6;
            min-height: 100vh;
            min-width: 100vw;
            height: 100vh;
            width: 100vw;
            display: flex;
            align-items: stretch;
            justify-content: stretch;
        }
        .split-container {
            display: flex;
            height: 100vh;
            width: 100vw;
            background: #fff;
            border-radius: 0;
            box-shadow: none;
            overflow: hidden;
        }
        .banner-side {
            flex: 1.2;
            position: relative;
            background: url('../images/banner1.jpg') center center/cover no-repeat;
            min-width: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .banner-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(44,62,80,0.7) 0%, rgba(74,144,226,0.5) 100%);
            z-index: 1;
        }
        .banner-content {
            position: relative;
            z-index: 2;
            color: #fff;
            text-align: center;
            width: 100%;
            padding: 0 2vw;
        }
        .banner-content img {
            width: 90px;
            margin-bottom: 1.5rem;
        }
        .banner-content h1 {
            font-size: 2.3rem;
            font-weight: 800;
            margin-bottom: 0.7rem;
            letter-spacing: 1px;
        }
        .banner-content p {
            font-size: 1.1rem;
            font-weight: 400;
            opacity: 0.95;
        }
        .form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 30px;
            background: #fff;
            height: 100vh;
        }
        .login-form-box {
            width: 100%;
            max-width: 370px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 32px rgba(44,62,80,0.10);
            padding: 2.5rem 2rem 2rem 2rem;
            margin: 0 auto;
        }
        .login-form-box h2 {
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 2rem;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        .form-control {
            border-radius: 8px;
            padding: 1rem;
            font-size: 1.08rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4a90e2, #2c3e50);
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2c3e50, #4a90e2);
        }
        .forgot-link {
            color: #4a90e2;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
        }
        .forgot-link:hover {
            color: #2c3e50;
        }
        @media (max-width: 900px) {
            .split-container { flex-direction: column; width: 100vw; height: 100vh; }
            .banner-side { min-height: 180px; height: 32vh; }
            .form-side { height: 68vh; }
            .banner-content h1 { font-size: 1.5rem; }
        }
        @media (max-width: 600px) {
            .form-side { padding: 10px 2px; }
            .login-form-box { max-width: 100%; padding: 1.2rem 0.5rem; }
            .banner-content img { width: 60px; }
        }
    </style>
</head>
<body>
    <div class="split-container">
        <div class="banner-side">
            <div class="banner-overlay"></div>
            <div class="banner-content">
                <img src="../images/logo.png" alt="Sport11 Logo">
                <h1>Welcome to Sport11 Admin</h1>
                <p>Manage your platform securely and efficiently.<br>Login to access the admin dashboard.</p>
            </div>
        </div>
        <div class="form-side">
            <div class="login-form-box">
                <h2 class="text-center">Admin Login</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                    <div class="text-center mt-2">
                        <a href="forgot_password.php" class="forgot-link">
                            <i class="fas fa-key"></i> Forgot Password?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 