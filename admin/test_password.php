<?php
require_once '../config/database.php';

// Test password
$test_password = 'admin123';

// Get the stored hash from database
try {
    $stmt = $pdo->prepare("SELECT password FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $stored_hash = $stmt->fetchColumn();
    
    echo "Stored hash from database: " . $stored_hash . "<br><br>";
    
    // Generate a new hash for comparison
    $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
    echo "Newly generated hash: " . $new_hash . "<br><br>";
    
    // Test verification with stored hash
    $verify_stored = password_verify($test_password, $stored_hash);
    echo "Verification with stored hash: " . ($verify_stored ? 'SUCCESS' : 'FAILED') . "<br><br>";
    
    // Test verification with new hash
    $verify_new = password_verify($test_password, $new_hash);
    echo "Verification with new hash: " . ($verify_new ? 'SUCCESS' : 'FAILED') . "<br><br>";
    
    // Print hash information
    echo "Stored hash information:<br>";
    print_r(password_get_info($stored_hash));
    echo "<br><br>";
    
    echo "New hash information:<br>";
    print_r(password_get_info($new_hash));
    
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?> 