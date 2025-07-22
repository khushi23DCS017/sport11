<?php
require_once '../config/database.php';

// Admin credentials
$username = 'admin';
$password = 'admin123';

// Generate a fresh password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // First, check if admin user exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        // Update existing admin user
        $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_password, $username]);
        echo "Admin password updated successfully!<br>";
        echo "New hash: " . $hashed_password . "<br>";
        echo "Please try logging in with:<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        // Create new admin user
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);
        echo "Admin user created successfully!<br>";
        echo "Hash: " . $hashed_password . "<br>";
        echo "Please try logging in with:<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 