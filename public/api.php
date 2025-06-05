
<?php
// public/api.php

// Include functions (adjust path as needed)
require_once __DIR__ . '/../includes/functions.php';

// Basic API endpoint for handling AJAX requests
// We expect POST requests with an 'action' parameter

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $taskId = isset($_POST['task_id']) ? (int)$_POST['task_id'] : 0;

    // Set header to indicate JSON response (optional but good practice)
    // header('Content-Type: application/json'); // Uncomment if sending JSON back

    $success = false; // Flag to indicate success

    if ($taskId > 0) {
            switch ($action) {
                case 'toggle':
                    // Call the existing toggle function
                // In a real API, you might want connectDB() inside the function
                // or have the function return success/failure
                    toggleTask($taskId);
                // For this simple case, assume success if no DB error occurred
                    $success = true; // Assume success for now
                    break;

                case 'delete':
                    // Call the existing delete function
                    deleteTask($taskId);
                    $success = true; // Assume success for now
                    break;

                    // Add other actions like 'update_title' later if needed
        }
    }

    // Send a response (optional, could be simple status or JSON)
    if ($success) {
        // Send a minimal success response (HTTP 200 is default)
        // echo json_encode(['success' => true]); // Example JSON response
        http_response_code(200); // OK
    } else {
        // Send an error response code
        http_response_code(400); // Bad Request (or 500 Internal Server Error if DB failed)
        // echo json_encode(['success' => false, 'message' => 'Invalid request or task ID']);
    }

} else {
    // Not a POST request or action not set
    http_response_code(405); // Method Not Allowed
    // echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

exit; // Stop script execution

?>
