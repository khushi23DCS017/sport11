<?php
$to = 'khushidadhaniya8@gmail.com'; // Replace with the actual email you want to receive the test email
$subject = 'Test Email from XAMPP Sendmail';
$message = 'This is a test email sent from your local XAMPP server configuration.';
$headers = 'From: khushidadhaniya8@gmail.com' . "\r\n" .
               'Reply-To: khushidadhaniya8@gmail.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

if (@mail($to, $subject, $message, $headers)) {
   echo 'Test email sent successfully to ' . $to;
} else {
   echo 'Failed to send test email.';
   // You can add error logging here if needed, but sendmail.ini's log might be more useful
}
?> 