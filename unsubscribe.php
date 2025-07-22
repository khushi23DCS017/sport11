<?php
require_once 'config/database.php';

$response = ['success' => false, 'message' => 'An unexpected error occurred.'];

// Get email from URL parameter
$email = $_GET['email'] ?? null;

if ($email !== null) {
    // Basic email validation
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            // Update the subscriber's status to inactive
            $stmt = $pdo->prepare("UPDATE newsletter_subscriptions SET active = FALSE WHERE email = ?");
            $stmt->execute([$email]);

            // Check if a row was affected
            if ($stmt->rowCount() > 0) {
                $response = ['success' => true, 'message' => 'You have been successfully unsubscribed.'];
            } else {
                // If no row was affected, the email might not be in the database or already inactive
                $response = ['success' => false, 'message' => 'This email address was not found in our subscription list or is already unsubscribed.'];
            }

        } catch(PDOException $e) {
            // Log the error for debugging
            error_log("Database Error in unsubscribe.php: " . $e->getMessage());
            $response = ['success' => false, 'message' => 'A database error occurred. Please try again later.'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Invalid email address provided.'];
    }
} else {
    $response = ['success' => false, 'message' => 'No email address provided.'];
}

// You can display a simple HTML response or a JSON response
// For simplicity, let's display an HTML page with the message.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"> <!-- Adjust path as needed -->
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            background-color: #f8f9fa;
        }
        .container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert <?php echo $response['success'] ? 'alert-success' : 'alert-danger'; ?>" role="alert">
            <?php echo htmlspecialchars($response['message']); ?>
        </div>
        <p><a href="index.html">Go back to the homepage</a></p> <!-- Adjust path as needed -->
    </div>
</body>
</html> 