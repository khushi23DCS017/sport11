<?php
require_once 'config/database.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unexpected error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $otp = $_POST['otp'] ?? '';

    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($otp)) {

        try {
            // Check if email and OTP match and are within the valid time frame (e.g., 10 minutes)
            // Adjust interval as needed
            $stmt = $pdo->prepare("SELECT * FROM otp_verifications WHERE email = ? AND otp = ? AND created_at >= (NOW() - INTERVAL 10 MINUTE)");
            $stmt->execute([$email, $otp]);
            $verification = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($verification) {
                // OTP is valid, now subscribe the user
                // Use INSERT IGNORE to prevent errors if the email somehow already exists (shouldn't happen with UNIQUE constraint)
                $stmt = $pdo->prepare("INSERT IGNORE INTO newsletter_subscriptions (email, active) VALUES (?, TRUE)");
                $stmt->execute([$email]);

                // Clean up the used OTP from the temporary table
                $stmt = $pdo->prepare("DELETE FROM otp_verifications WHERE email = ?");
                $stmt->execute([$email]);

                $response = ['success' => true, 'message' => 'Email verified and subscribed successfully!'];

            } else {
                // OTP is invalid or expired
                $response = ['success' => false, 'message' => 'Invalid or expired OTP.'];
            }

        } catch(PDOException $e) {
            // Log database errors
            error_log("Database Error in verify_otp.php: " . $e->getMessage());
            $response = ['success' => false, 'message' => 'A database error occurred during verification. Please try again later.'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Invalid request. Please provide email and OTP.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid request method.'];
}

echo json_encode($response);
exit;
?> 