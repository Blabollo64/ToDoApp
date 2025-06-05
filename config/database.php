
<?php
// config/database.php

// Database configuration constants
define('DB_HOST', 'localhost');        // Or your DB host (usually localhost for XAMPP)
define('DB_USER', 'root');            // Your MySQL username (default 'root' for XAMPP)
define('DB_PASS', '');                // Your MySQL password (default empty for XAMPP)
define('DB_NAME', 'todoist_clone_db'); // The database name you created

// Function to establish database connection (using mysqli)
function connectDB() {
    // Create connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); // Basic error handling
    }

    return $conn;
}

// You might want to add error reporting settings here for development
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

?>
