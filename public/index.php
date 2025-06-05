
<?php
// public/index.php

// Include necessary files
// Note the path adjustment because index.php is inside 'public'
require_once __DIR__ . '/../includes/functions.php';

// --- Form Handling ---
// Check if the request method is POST (form submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'action' field is set
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $taskId = isset($_POST['task_id']) ? (int)$_POST['task_id'] : 0;

        switch ($action) {
            case 'add':
                if (!empty($_POST['task_title'])) {
                    addTask($_POST['task_title']);
                }
                break;
            case 'delete':
                if ($taskId > 0) {
                    deleteTask($taskId);
                }
                break;
            case 'toggle':
                    if ($taskId > 0) {
                    toggleTask($taskId);
                    }
                break;
        }

        // Redirect back to the index page to prevent form resubmission on refresh
        // This is a common pattern called Post/Redirect/Get (PRG)
        header("Location: index.php");
        exit; // Important to stop script execution after redirection
    }
}

// --- Data Fetching ---
// Get all tasks from the database AFTER any potential modifications
$tasks = getAllTasks();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ... (keep head content: meta, title, tailwind cdn) ... -->
    <title>Todo Appsdfdsfds - DB</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto max-w-lg mt-10 bg-white p-6 rounded shadow-md">
        <h1 class="text-2xl font-bold mb-4 text-center">My Tasks</h1>

        <!-- Task Input Form - Now functional! -->
        <form action="index.php" method="POST" class="mb-4">
            <input type="hidden" name="action" value="add"> <!-- Hidden field to specify action -->
            <input type="text" name="task_title" placeholder="Add a new task" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300" required>
            <button type="submit" class="mt-2 w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Add Task
            </button>
        </form>

        <!-- Task List (Dynamic from Database) -->
        <ul>
            <?php if (!empty($tasks)): ?>
                <?php foreach ($tasks as $task): ?>
                    <li class="flex justify-between items-center p-2 border-b border-gray-200">
                        <span class="<?php echo $task['completed'] ? 'line-through text-gray-500' : ''; ?>">
                            <?php echo htmlspecialchars($task['title']); ?>
                        </span>
                        <div class="flex space-x-2">
                            <!-- Toggle Form -->
                            <form action="index.php" method="POST" class="inline-block">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="<?php echo $task['completed'] ? 'text-gray-400' : 'text-green-500'; ?> hover:text-green-700">✓</button>
                            </form>
                                <!-- Delete Form -->
                            <form action="index.php" method="POST" class="inline-block">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this task?');">✗</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="text-gray-500 text-center p-2">No tasks yet! Add one above.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
