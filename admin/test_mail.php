<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$to = "khushidadhaniya8@gmail.com"; // Replace with your email
$subject = "Test Email";
$message = "This is a test email from your Sport11 admin panel.";
$headers = "From: noreply@sport11.com";

echo "Attempting to send test email...<br>";

if (mail($to, $subject, $message, $headers)) {
    echo "Test email sent successfully!";
} else {
    echo "Failed to send test email. Please check your server's mail configuration.";
}

// Display PHP mail configuration
echo "<br><br>PHP Mail Configuration:<br>";
echo "SMTP Server: " . ini_get('SMTP') . "<br>";
echo "SMTP Port: " . ini_get('smtp_port') . "<br>";
echo "sendmail_path: " . ini_get('sendmail_path') . "<br>";
?> 