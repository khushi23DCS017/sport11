<?php
ob_start(); // Start output buffering

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

$response = ['success' => false, 'message' => 'An unexpected error occurred.']; // Default response

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    // Backend Validation
    if (empty($name) || empty($email) || empty($message)) {
        $response = ['success' => false, 'message' => 'Please fill all fields.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = ['success' => false, 'message' => 'Please enter a valid email address.'];
    } else {
        try {
            // Save to database
            $stmt = $pdo->prepare("INSERT INTO contact_submissions (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            
            // Send email notification using PHPMailer
            $mail = new PHPMailer(true);
            
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'khushidadhaniya8@gmail.com';
            $mail->Password = 'odzxbbkvrqlgzmsv';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Disable SSL certificate verification
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            //Recipients
            $mail->setFrom('khushidadhaniya8@gmail.com', 'Sport11 Contact Form');
            $mail->addAddress('khushidadhaniya8@gmail.com', 'Admin');
            $mail->addReplyTo($email, $name);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body = "
                <h2>New Contact Form Submission</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Message:</strong></p>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
            ";
            $mail->AltBody = "Name: {$name}\nEmail: {$email}\nMessage:\n{$message}";

            $mail->send();
            $response = ['success' => true, 'message' => 'Message sent successfully!'];
            
        } catch (PDOException $e) {
            $response = ['success' => false, 'message' => 'Database error occurred. Please try again later.'];
            error_log("PDO Error in contact.php: " . $e->getMessage());
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Failed to send email. Please try again later.'];
            error_log("PHPMailer Error in contact.php: " . $e->getMessage());
        }
    }
}

ob_clean();
header('Content-Type: application/json');
echo json_encode($response);
exit;
?> 