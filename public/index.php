<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Start session management
// public/index.php
require_once __DIR__ . '/../includes/auth.php'; // Auth first
require_once __DIR__ . '/../includes/functions.php'; // Then functions

requireLogin(); // Redirect to login if not authenticated

$userId = getCurrentUserId(); // Get logged-in user's ID

// Handle ADD task form submission (user-specific)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!empty($_POST['task_title']) && $userId) {
        addTask($_POST['task_title'], $userId); // Pass user ID
    }
    header("Location: index.php"); // PRG pattern
             exit;
}

// Fetch only the current user's tasks
$initialTasks = getAllTasks($userId);

$pageTitle = "My Tasks";
require_once __DIR__ . '/../includes/header.php'; // Include shared header
?>


<!-- Alpine.js x-data component (keep similar structure) -->
<div
    class="container mx-auto max-w-lg mt-10 bg-white p-6 rounded shadow-md"
    x-data="tasks"
>
    <h1 class="text-2xl font-bold mb-4 text-center">My Tasks</h1>

    <!-- Add Task Form (no change needed here, PHP handles user ID) -->
    <form action="index.php" method="POST" class="mb-4">
        <!-- ... form content ... -->
    </form>

    <!-- Task List (no change needed here, Alpine gets user-specific tasks via PHP) -->
    <ul>
        <!-- Alpine template x-for loop remains the same -->
        <template x-for="task in tasks" :key="task.id">
            <!-- ... li structure with toggle/delete buttons ... -->
                <li class="flex justify-between items-center p-2 border-b border-gray-200">
                <span :class="{ 'line-through text-gray-500': task.completed }" x-text="task.title"></span>
                <div class="flex space-x-2">
                    <button @click="toggleTask(task)" :class="task.completed ? 'text-gray-400' : 'text-green-500'" class="hover:text-green-700">✓</button>
                    <button @click="deleteTask(task.id)" class="text-red-500 hover:text-red-700">✗</button>
                </div>
            </li>
        </template>
        <!-- ... no tasks message ... -->
        <template x-if="tasks.length === 0">
                <li class="text-gray-500 text-center p-2">No tasks yet! Add one above.</li>
        </template>
    </ul>

    <!-- Alpine.js Logic (toggleTask/deleteTask need no changes as api.php handles user context) -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tasks', () => ({
                tasks: <?php echo json_encode($initialTasks); ?>,
                newTaskTitle: '',
                toggleTask(task) {
                    const index = this.tasks.findIndex(t => t.id === task.id);
                    if (index === -1) return;
                    const originalCompleted = this.tasks[index].completed;
                    this.tasks[index].completed = !this.tasks[index].completed;

                    const formData = new FormData();
                    formData.append('action', 'toggle');
                    formData.append('task_id', task.id);

                    fetch('api.php', { method: 'POST', body: formData })
                        .then(response => { if (!response.ok) throw new Error('API Error'); })
                        .catch(error => {
                            console.error('Toggle Error:', error);
                            this.tasks[index].completed = originalCompleted;
                            alert('Failed to update task status.');
                        });
                },
                deleteTask(taskId) {
                    if (!confirm('Are you sure?')) return;
                    const index = this.tasks.findIndex(t => t.id === taskId);
                    if (index === -1) return;
                    const removedTask = this.tasks.splice(index, 1)[0];

                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('task_id', taskId);

                    fetch('api.php', { method: 'POST', body: formData })
                        .then(response => { if (!response.ok) throw new Error('API Error'); })
                        .catch(error => {
                            console.error('Delete Error:', error);
                            this.tasks.splice(index, 0, removedTask);
                            alert('Failed to delete task.');
                        });
                }
            }));
        });
    </script>

</div> <!-- End Alpine component -->

<!-- Add Task Form with Alpine.js toggle -->
<div x-data="{ showInput: false, newTask: '' }" class="mb-1 mt-2 px-50">
    <template x-if="!showInput">
        <button
            @click="showInput = true; $nextTick(() => $refs.taskInput.focus())"
            type="button"
            class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 mx-auto block min-w-[96px] h-10"
        >
            Add Task
        </button>
        >
            Add Task
        </button>
    </template>
    <template x-if="showInput">
        <form action="index.php" method="POST" class="flex space-x-0.5 mt-0.5 w-full h-16 items-center">
            <input type="hidden" name="action" value="add">
            <input
                x-ref="taskInput"
                type="text"
                name="task_title"
                x-model="newTask"
                placeholder="New task"
                class="flex-1 border rounded px-1 py-0.5 text-xs focus:outline-none h-10"
                required
            >
            <button
                type="submit"
                class="bg-blue-500 text-white px-1 py-0.5 rounded text-xs hover:bg-blue-600 min-w-[96px] h-10"
            >
                Save
            </button>
            <button
                type="button"
                @click="showInput = false; newTask = ''"
                class="bg-gray-300 text-gray-700 px-1 py-0.5 rounded text-xs h-10"
            >
                Cancel
            </button>
        </form>
    </template>
</div>

<?php include __DIR__ . '/../includes/footer.php'; // Include shared footer ?>
