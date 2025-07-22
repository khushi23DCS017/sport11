<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if message ID is provided
$messageId = $_POST['id'] ?? null;

$response = ['success' => false, 'message' => 'Invalid request.'];

if ($messageId !== null && is_numeric($messageId)) {
    try {
        // Prepare and execute the delete statement
        $stmt = $pdo->prepare("DELETE FROM contact_submissions WHERE id = ?");
        $stmt->execute([$messageId]);

        // Check if a row was affected
        if ($stmt->rowCount() > 0) {
            $response = ['success' => true, 'message' => 'Message deleted successfully.'];
        } else {
            $response = ['success' => false, 'message' => 'Message not found.'];
        }

    } catch(PDOException $e) {
        // Log the error for debugging
        error_log("Database Error in delete_message.php: " . $e->getMessage());
        $response = ['success' => false, 'message' => 'Database error during deletion.'];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?> 