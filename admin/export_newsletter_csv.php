<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect or show an error if not logged in
    header('Location: login.php');
    exit;
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="newsletter_subscribers.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, array('Email', 'Subscription Date'));

try {
    // Fetch all newsletter subscriptions
    $stmt = $pdo->query("SELECT email, created_at FROM newsletter_subscriptions ORDER BY created_at DESC");
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add subscription data to CSV
    foreach ($subscriptions as $subscription) {
        // Format date if necessary
        $subscription['created_at'] = date('Y-m-d H:i', strtotime($subscription['created_at']));
        fputcsv($output, $subscription);
    }

} catch(PDOException $e) {
    // Log the error for debugging
    error_log("Database Error in export_newsletter_csv.php: " . $e->getMessage());
    // Output an error message to the CSV or browser if necessary, but careful not to break CSV format
    // For simplicity here, we'll just stop and the log will have the error.
    // You might want a more user-friendly error handling in a production environment.
}

// Close the output stream
fclose($output);

exit;
?> 