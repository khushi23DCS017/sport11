<?php
session_start();
require_once '../vendor/autoload.php'; // Load Composer's autoloader
require_once '../config/database.php';
// Manual PHPMailer includes (no longer needed with Composer autoloader)
// require_once '../PHPMailer/src/Exception.php';
// require_once '../PHPMailer/src/PHPMailer.php';
// require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    
    if (empty($email)) {
        $error = 'Please enter your email address';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        try {
            // Check if email exists in admin_users table
            $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store reset token in database
                $stmt = $pdo->prepare("UPDATE admin_users SET reset_token = ?, reset_expires = ? WHERE email = ?");
                $stmt->execute([$token, $expires, $email]);
                
                // Create reset link
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/sport11-landingPage/admin/reset_password.php?token=" . $token;
                
                // Configure PHPMailer
                $mail = new PHPMailer(true);
                
                // Enable debug output (set to 0 for production)
                $mail->SMTPDebug = 0; 
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'khushidadhaniya8@gmail.com';
                $mail->Password = 'odzxbbkvrqlgzmsv'; // Use the working App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Set email content
                $mail->setFrom('khushidadhaniya8@gmail.com', 'Sport11 Admin');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request - Sport11 Admin';
                
                // Email body
                $mail->Body = "
                    <h2>Password Reset Request</h2>
                    <p>Hello,</p>
                    <p>You have requested to reset your password. Click the link below to reset your password:</p>
                    <p><a href='{$reset_link}'>{$reset_link}</a></p>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you did not request this reset, please ignore this email.</p>
                    <p>Best regards,<br>Sport11 Admin Team</p>
                ";

                // Send email
                try {
                    $mail->send();
                    $success = 'Password reset instructions have been sent to your email';
                } catch (Exception $e) {
                    $error = 'Failed to send reset email: ' . $e->getMessage();
                }
            } else {
                // Don't reveal if email exists or not
                $success = 'If your email is registered, you will receive password reset instructions';
            }
        } catch(PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        } catch(Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Sport11 Admin</title>
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

        .forgot-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .forgot-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .forgot-header h2 {
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
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-header">
            <h2>Forgot Password</h2>
            <p class="text-muted">Enter your email to reset your password</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required 
                       placeholder="Enter your registered email">
            </div>
            
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
        </form>
        
        <div class="back-to-login">
            <a href="login.php">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 