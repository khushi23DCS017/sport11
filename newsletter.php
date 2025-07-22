<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO newsletter_subscriptions (email) VALUES (?)");
            $stmt->execute([$email]);
            $response = ['success' => true, 'message' => 'Subscribed successfully!'];
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $response = ['success' => false, 'message' => 'Email already subscribed.'];
            } else {
                $response = ['success' => false, 'message' => 'Error subscribing. Please try again.'];
            }
        }
    } else {
        $response = ['success' => false, 'message' => 'Please enter a valid email address.'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?> 