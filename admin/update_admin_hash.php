<?php
require_once '../config/database.php';

// Admin credentials
$username = 'admin';
$password = 'admin123';

// Generate a fresh password hash with explicit bcrypt algorithm
$hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

try {
    // Update the admin user's password
    $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
    $stmt->execute([$hashed_password, $username]);
    
    if ($stmt->rowCount() > 0) {
        echo "Admin password updated successfully!<br>";
        echo "New hash: " . $hashed_password . "<br><br>";
        
        // Verify the new hash
        $verify = password_verify($password, $hashed_password);
        echo "Verification test: " . ($verify ? 'SUCCESS' : 'FAILED') . "<br><br>";
        
        // Show hash information
        echo "New hash information:<br>";
        print_r(password_get_info($hashed_password));
        
        echo "<br><br>Please try logging in with:<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "No admin user found to update.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 