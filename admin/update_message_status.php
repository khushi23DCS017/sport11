<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if message ID and status are provided
$messageId = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;

$response = ['success' => false, 'message' => 'Invalid request.'];

// Validate status
$allowedStatuses = ['read', 'unread'];

if ($messageId !== null && is_numeric($messageId) && $status !== null && in_array($status, $allowedStatuses)) {
    try {
        // Prepare and execute the update statement
        $stmt = $pdo->prepare("UPDATE contact_submissions SET status = ? WHERE id = ?");
        $stmt->execute([$status, $messageId]);

        // Check if a row was affected
        if ($stmt->rowCount() > 0) {
            $response = ['success' => true, 'message' => 'Message status updated successfully.'];
        } else {
            $response = ['success' => false, 'message' => 'Message not found or status already updated.'];
        }

    } catch(PDOException $e) {
        // Log the error for debugging
        error_log("Database Error in update_message_status.php: " . $e->getMessage());
        $response = ['success' => false, 'message' => 'Database error during status update.'];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?> 