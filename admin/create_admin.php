<?php
require_once '../config/database.php';

// Admin credentials
$username = 'admin';
$password = 'admin123';

// Generate password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // First, check if admin user already exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        // Update existing admin user
        $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_password, $username]);
        echo "Admin password updated successfully!";
    } else {
        // Create new admin user
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);
        echo "Admin user created successfully!";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>