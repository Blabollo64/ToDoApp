
<?php
// includes/functions.php

// Make sure to include the database configuration
// Note the path adjustment if index.php is in public/
require_once __DIR__ . '/../config/database.php';

// Function to get all tasks from the database
function getAllTasks() {
    $conn = connectDB();
    $tasks = []; // Initialize empty array

    // SQL query to select all tasks, ordered by creation date
    $sql = "SELECT id, title, completed FROM tasks ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Fetch all results into an associative array
        $tasks = $result->fetch_all(MYSQLI_ASSOC);
    }

    $conn->close(); // Close the connection
    return $tasks;
}

// Function to add a new task
// Takes the task title as input
function addTask($title) {
    $conn = connectDB();
    $title = $conn->real_escape_string(trim($title)); // Sanitize input

    if (!empty($title)) {
        // SQL query to insert a new task
        $sql = "INSERT INTO tasks (title) VALUES ('$title')";
        $conn->query($sql);
        // Basic error checking could be added here: if ($conn->error) { ... }
    }

    $conn->close();
}

// Function to delete a task by its ID
function deleteTask($id) {
    $conn = connectDB();
    $id = (int)$id; // Cast to integer for security

    if ($id > 0) {
        // SQL query to delete a task
        $sql = "DELETE FROM tasks WHERE id = $id";
        $conn->query($sql);
        // Basic error checking could be added here
    }

    $conn->close();
}

// Function to toggle the completion status of a task by its ID
function toggleTask($id) {
    $conn = connectDB();
    $id = (int)$id; // Cast to integer

    if ($id > 0) {
        // SQL query to toggle the 'completed' status
        // Flips the boolean value (0 becomes 1, 1 becomes 0)
        $sql = "UPDATE tasks SET completed = NOT completed WHERE id = $id";
        $conn->query($sql);
        // Basic error checking could be added here
    }

    $conn->close();
}

?>
