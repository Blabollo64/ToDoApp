<?php
session_start(); // Ensure session is started
// public/api.php

// Include functions (adjust path as needed)
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Basic API endpoint for handling AJAX requests
// We expect POST requests with an 'action' parameter

$userId = getCurrentUserId(); // Get the current user's ID

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $userId) {
$action = $_POST['action'];
$taskId = isset($_POST['task_id']) ? (int)$_POST['task_id'] : 0;
$success = false;

    if ($taskId > 0) {
    switch ($action) {
        case 'toggle':
                // Call function with user ID check included
                toggleTask($taskId, $userId);
                $success = true; // Assume success if function doesn't throw error
            break;
        case 'delete':
                 // Call function with user ID check included
                deleteTask($taskId, $userId);
                $success = true; // Assume success
             break;
        }
    }

    if ($success) {
        http_response_code(200);
        // echo json_encode(['success' => true]); // Optional success response
    } else {
        http_response_code(400); // Bad Request or task not found/not owned
        // echo json_encode(['success' => false, 'message' => 'Invalid request or task ID.']);
    }

} else {
    http_response_code(400); // Bad Request if method isn't POST or action missing
}
exit;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['action']) && $data['action'] === 'reorder' && isset($data['orderedTaskIds'])) {
        updateTaskOrder($userId, $data['orderedTaskIds']);
        http_response_code(200);
        exit;
    }
    // ...other actions...
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $userId) {
    $action = $_POST['action'];
    if ($action === 'add' && !empty($_POST['task_title'])) {
        $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        addTask($_POST['task_title'], $userId, $dueDate);
        http_response_code(200);
        exit;
    }
    // ... other actions ...
}
?>