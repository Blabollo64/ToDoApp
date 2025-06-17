
<?php
session_start(); // Start the session to access session variables
// public/logout.php
require_once __DIR__ . '/../includes/auth.php';

logoutUser(); // Call the logout function

// Redirect to login page after logout
header('Location: login.php');
exit;
?>
