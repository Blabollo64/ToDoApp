<?php
// includes/functions.php

// Include the database configuration
require_once __DIR__ . '/../config/database.php';

// Function to get all tasks from the database
function getAllTasks($userId) {
    $mysqli = connectDB();
    $tasks = [];

    $sql = "SELECT id, title, completed, due_date FROM tasks WHERE user_id = ?";
    $stmt = $mysqli->prepare($sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $userId); // <-- ADD THIS LINE
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        mysqli_stmt_close($stmt);
    }


    mysqli_close($mysqli); // Close the connection
    return $tasks;

     $mysqli = connectDB();
    $tasks = [];
    $sql = "SELECT id, title, completed, due_date FROM tasks WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $mysqli->prepare($sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($mysqli);
    return $tasks;
}

// Function to add a new task
function addTask($title, $userId, $dueDate = null) {
    $mysqli = connectDB();
    $title = trim($title);
    $userId = (int)$userId;

    if (!empty($title) && $userId > 0) {
        $sql = "INSERT INTO tasks (title, user_id, due_date) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sis', $title, $userId, $dueDate);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($mysqli);
}

function updateTask($taskId, $userId, $title, $dueDateTime = null) {
    $mysqli = connectDB();
    $taskId = (int)$taskId;
    $userId = (int)$userId;
    $title = trim($title);

    if ($taskId > 0 && $userId > 0 && !empty($title)) {
        $sql = "UPDATE tasks SET title = ?, due_date = ? WHERE id = ? AND user_id = ?";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssii', $title, $dueDateTime, $taskId, $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($mysqli);
}

// Function to delete a task by its ID
function deleteTask($id) {
    $mysqli = connectDB();
    $id = (int)$id;

    if ($id > 0) {
        $sql = "DELETE FROM tasks WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($mysqli);
}

// Function to toggle the completion status of a task by its ID
function toggleTask($id) {
    $mysqli = connectDB();
    $id = (int)$id;

    if ($id > 0) {
        $sql = "UPDATE tasks SET completed = NOT completed WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($mysqli);
}
/**
 * Update the order of tasks for a user.
 * @param int $userId
 * @param array $orderedTaskIds Array of task IDs in the new order
 * @return void
 */
function updateTaskOrder($userId, $orderedTaskIds) {
    $mysqli = connectDB();
    $userId = (int)$userId;

    if ($userId > 0 && is_array($orderedTaskIds)) {
        $order = 1;
        $sql = "UPDATE tasks SET sort_order = ? WHERE id = ? AND user_id = ?";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            foreach ($orderedTaskIds as $taskId) {
                $taskId = (int)$taskId;
                mysqli_stmt_bind_param($stmt, 'iii', $order, $taskId, $userId);
                mysqli_stmt_execute($stmt);
                $order++;
            }
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($mysqli);
}
// Get only regular tasks (no due date)
function getRegularTasks($userId) {
    $mysqli = connectDB();
    $tasks = [];
    $sql = "SELECT id, title, completed FROM tasks WHERE user_id = ? AND (due_date IS NULL OR due_date = '') ORDER BY sort_order ASC, created_at DESC";
    $stmt = $mysqli->prepare($sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($mysqli);
    return $tasks;
}
?>
