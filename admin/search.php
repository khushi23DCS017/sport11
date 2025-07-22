<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$keyword = $_GET['keyword'] ?? '';
$keyword = sanitize_input($keyword); // Sanitize input

$searchResults = [
    'contact_submissions' => [],
    'newsletter_subscriptions' => []
];

if (!empty($keyword)) {
    $searchTerm = '%' . $keyword . '%';

    try {
        // Search Contact Submissions by name or email
        $stmt = $pdo->prepare("SELECT id, name, email, message, created_at FROM contact_submissions WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC");
        $stmt->execute([$searchTerm, $searchTerm]);
        $searchResults['contact_submissions'] = $stmt->fetchAll();

        // Search Newsletter Subscriptions by email
        $stmt = $pdo->prepare("SELECT id, email, created_at FROM newsletter_subscriptions WHERE email LIKE ? ORDER BY created_at DESC");
        $stmt->execute([$searchTerm]);
        $searchResults['newsletter_subscriptions'] = $stmt->fetchAll();

    } catch(PDOException $e) {
        // Log the error
        error_log("Database Error in admin/search.php: " . $e->getMessage());
        // Return an error response
        $searchResults = ['error' => 'Database error during search.'];
    }
}

header('Content-Type: application/json');
echo json_encode($searchResults);
exit;
?> 