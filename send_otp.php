<?php
require_once 'config/database.php';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Manual includes for PHPMailer (adjust paths if needed)
// require 'vendor/Exception.php';
// require 'vendor/PHPMailer.php';
// require 'vendor/SMTP.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unexpected error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {

        try {
            // Check if email is already subscribed and active
            $stmt = $pdo->prepare("SELECT id FROM newsletter_subscriptions WHERE email = ? AND active = TRUE");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $response = ['success' => false, 'message' => 'This email is already subscribed and active.'];
                echo json_encode($response);
                exit;
            }

            // Generate a random 6-digit OTP
            $otp = rand(100000, 999999);
            $otp = strval($otp); // Ensure it's a string

            // Store OTP in the temporary table (replace if email already exists)
            $stmt = $pdo->prepare("INSERT INTO otp_verifications (email, otp) VALUES (?, ?) ON DUPLICATE KEY UPDATE otp = ?, created_at = CURRENT_TIMESTAMP");
            
            $stmt->execute([$email, $otp, $otp]);

            // Send OTP email using PHPMailer
            $mail = new PHPMailer(true); // Pass true to enable exceptions

            //Server settings (use your existing PHPMailer configuration)
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Disable verbose debug output in production
            $mail->isSMTP(); // Send using SMTP
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'khushidadhaniya8@gmail.com'; // SMTP username (your full Gmail address)
            $mail->Password = 'odzxbbkvrqlgzmsv'; // SMTP password (your Gmail password or App Password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port = 587; // TCP port to connect to

            //Recipients
            $mail->setFrom('khushidadhaniya8@gmail.com', 'Sport11 Newsletter'); // Sender
            $mail->addAddress($email); // Add a recipient (the user's email)

            // Content
            $mail->isHTML(false); // Set email format to not HTML
            $mail->Subject = 'Your Sport11 Newsletter Verification Code';
            $mail->Body = "Your verification code is: " . $otp;

            $mail->send();

            $response = ['success' => true, 'message' => 'OTP sent to your email.'];

        } catch(PDOException $e) {
            $response = ['success' => false, 'message' => 'A database error occurred. Please try again later.'];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Could not send verification email. Mailer Error: ' . $mail->ErrorInfo];
        }
    } else {
        $response = ['success' => false, 'message' => 'Please enter a valid email address.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid request method.'];
}

echo json_encode($response);
exit; 