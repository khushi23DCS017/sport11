<?php
session_start();
require_once '../config/database.php';

$success = $error = '';
$token = $_GET['token'] ?? '';
$valid_token = false;

// Verify token
if (!empty($token)) {
    try {
        error_log("Attempting to verify token: " . $token);
        
        $stmt = $pdo->prepare("SELECT id, reset_token, reset_expires FROM admin_users WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        error_log("User found: " . ($user ? "Yes" : "No"));
        if ($user) {
            error_log("Token expiry: " . $user['reset_expires']);
            error_log("Current time: " . date('Y-m-d H:i:s'));
            
            if (strtotime($user['reset_expires']) > time()) {
                $valid_token = true;
                error_log("Token is valid");
            } else {
                error_log("Token has expired");
                $error = 'Invalid or expired reset token';
            }
        } else {
            error_log("No user found with this token");
            $error = 'Invalid or expired reset token';
        }
    } catch(PDOException $e) {
        error_log("Database error in reset_password.php: " . $e->getMessage());
        $error = 'An error occurred. Please try again.';
    }
} else {
    error_log("No token provided in URL");
    $error = 'No reset token provided';
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($new_password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } else {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
            $stmt->execute([$hashed_password, $token]);
            
            $success = 'Password has been reset successfully. You can now login with your new password.';
            $valid_token = false; // Prevent further use of the token
        } catch(PDOException $e) {
            error_log("Database error in reset_password.php: " . $e->getMessage());
            $error = 'Error resetting password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sport11 Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #2c3e50;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reset-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .reset-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .reset-header h2 {
            color: var(--secondary-color);
            font-weight: 600;
        }

        .form-control {
            border-radius: 5px;
            padding: 0.8rem;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 0.8rem;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .back-to-login {
            text-align: center;
            margin-top: 1rem;
        }

        .back-to-login a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-to-login a:hover {
            color: var(--secondary-color);
        }

        .alert {
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .password-requirements {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h2>Reset Password</h2>
            <p class="text-muted">Enter your new password</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
            <div class="text-center">
                <a href="login.php" class="btn btn-primary">Go to Login</a>
            </div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php if (!$valid_token): ?>
                <div class="text-center">
                    <a href="forgot_password.php" class="btn btn-primary">Request New Reset Link</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($valid_token): ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required 
                           placeholder="Enter new password">
                    <div class="password-requirements">
                        Password must be at least 8 characters long
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required 
                           placeholder="Confirm new password">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-key"></i> Reset Password
                </button>
            </form>
        <?php endif; ?>
        
        <div class="back-to-login">
            <a href="login.php">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 