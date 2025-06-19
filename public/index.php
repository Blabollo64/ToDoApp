<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Start session management
require_once __DIR__ . '/../includes/auth.php'; // Auth first
require_once __DIR__ . '/../includes/functions.php'; // Then functions

requireLogin(); // Redirect to login if not authenticated

$userId = getCurrentUserId(); // Get logged-in user's ID

// Handle ADD task form submission (user-specific)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!empty($_POST['task_title']) && $userId) {
        $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        $dueTime = !empty($_POST['due_time']) ? $_POST['due_time'] : null;
        $dueDateTime = null;
        if ($dueDate) {
            $dueDateTime = $dueDate;
            if ($dueTime) {
                $dueDateTime .= ' ' . $dueTime;
            }
        }
        addTask($_POST['task_title'], $userId, $dueDateTime);
    }
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!empty($_POST['task_id']) && $userId) {
        deleteTask($_POST['task_id'], $userId);
    }
    header("Location: calendar.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    if (!empty($_POST['task_id']) && $userId) {
        $title = trim($_POST['task_title']);
        $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        $dueTime = !empty($_POST['due_time']) ? $_POST['due_time'] : null;
        $dueDateTime = null;
        if ($dueDate) {
            $dueDateTime = $dueDate;
            if ($dueTime) {
                $dueDateTime .= ' ' . $dueTime;
            }
        }
        updateTask($_POST['task_id'], $userId, $title, $dueDateTime);
    }
    header("Location: index.php");
    exit;
}

// Fetch only the current user's tasks
$regularTasks = getRegularTasks($userId);

$pageTitle = "My Tasks";
require_once __DIR__ . '/../includes/header.php'; // Include shared header
?>

<!-- Alpine.js x-data component (keep similar structure) -->
<div
    class="container mx-auto max-w-lg mt-10 bg-white p-6 rounded shadow-md"
    x-data="tasks">
    <h1 class="text-2xl font-bold mb-4 text-center">My Daily Tasks</h1>

    <!-- Add Task Form with Alpine.js toggle -->
    <div x-data="{ showInput: false, newTask: '', newDueDate: '', newDueTime: '' }" class="mb-1 mt-2 px-50">
        <template x-if="!showInput">
            <button
                @click="showInput = true; $nextTick(() => $refs.taskInput.focus())"
                type="button"
                class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 mx-auto block min-w-[96px] h-10">Add Task</button>
        </template>
        <template x-if="showInput">
            <form action="index.php" method="POST" class="flex flex-col space-y-2 mt-2">
                <input type="hidden" name="action" value="add">
                <input
                    x-ref="taskInput"
                    type="text"
                    name="task_title"
                    x-model="newTask"
                    placeholder="Enter a new task"
                    class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                    required>
                <div class="flex space-x-2">
                    <input type="date" name="due_date" x-model="newDueDate" class="border rounded px-2 py-1" />
                    <input type="time" name="due_time" x-model="newDueTime" class="border rounded px-2 py-1" />
                </div>
                <div class="flex space-x-2">
                    <button
                        type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Save</button>
                    <button
                        type="button"
                        @click="showInput = false; newTask = ''; newDueDate = ''; newDueTime = ''"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">Cancel</button>
                </div>
            </form>
        </template>
    </div>

    <!-- Task List -->
    <ul x-sortable="tasks"
        @sort-order="reorderTasks($event.detail.newOrder)"
        class="divide-y divide-gray-200 bg-white rounded shadow">
        <template x-for="task in tasks" :key="task.id">
            <li
                x-sortable-item="task.id"
                class="flex justify-between items-center p-3 hover:bg-gray-50 transition">
                <div class="flex items-center space-x-2 flex-1">
                    <!-- Drag handle -->
                    <span x-sortable-handle class="cursor-move text-gray-400 hover:text-gray-600 mr-2 select-none">☰</span>
                    <span
                        :class="{ 'line-through text-gray-400': task.completed, 'text-gray-800': !task.completed }"
                        x-text="task.title"
                        class="flex-1"></span>
                </div>
                <div class="flex space-x-2 ml-4">
                    <button
                        @click="toggleTask(task)"
                        :class="task.completed ? 'text-gray-400 hover:text-green-400' : 'text-green-500 hover:text-green-700'"
                        class="px-2 py-1 rounded focus:outline-none focus:ring-2 focus:ring-green-300 transition"
                        title="Toggle Complete">✓</button>
                    <button
                        @click="deleteTask(task.id)"
                        class="text-red-500 hover:text-red-700 px-2 py-1 rounded focus:outline-none focus:ring-2 focus:ring-red-300 transition"
                        title="Delete Task">✗</button>
                    <button
                        @click="
                            editTask = task;
                            editTitle = task.title;
                            editDueDate = task.due_date ? task.due_date.split(' ')[0] : '';
                            editDueTime = task.due_date && task.due_date.includes(' ') ? task.due_date.split(' ')[1].slice(0,5) : '';
                        "
                        class="text-yellow-500 hover:text-yellow-700 px-2 py-1 rounded focus:outline-none focus:ring-2 focus:ring-yellow-300 transition"
                        title="Edit Task">Edit</button>
                </div>
            </li>
        </template>
        <template x-if="tasks.length === 0">
            <li class="text-gray-400 text-center p-4">No tasks yet! Add one above.</li>
        </template>
    </ul>

    <!-- Global Edit Modal -->
    <div
        x-show="editTask"
        style="display: none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:1000;"
        class="flex"
    >
        <form
            @submit.prevent="
                $refs.editForm.submit();
                editTask = null;
            "
            x-ref="editForm"
            action="index.php"
            method="POST"
            class="bg-white p-6 rounded shadow-md min-w-[300px]"
        >
            <h2 class="mb-2 font-bold">Edit Task</h2>
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="task_id" :value="editTask?.id">
            <input
                type="text"
                name="task_title"
                x-model="editTitle"
                placeholder="Task title"
                class="border rounded px-3 py-2 w-full mb-2"
                required
            >
            <div class="flex space-x-2 mb-2">
                <input type="date" name="due_date" x-model="editDueDate" class="border rounded px-2 py-1" />
                <input type="time" name="due_time" x-model="editDueTime" class="border rounded px-2 py-1" />
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                <button type="button" @click="editTask = null" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">Cancel</button>
            </div>
        </form>
    </div>

    <!-- Alpine.js Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tasks', () => ({
                tasks: <?php echo json_encode($regularTasks); ?>,
                newTaskTitle: '',
                newTaskDueDate: '',
                newTaskDueTime: '',
                editTask: null,
                editTitle: '',
                editDueDate: '',
                editDueTime: '',
                toggleTask(task) {
                    const index = this.tasks.findIndex(t => t.id === task.id);
                    if (index === -1) return;
                    const originalCompleted = this.tasks[index].completed;
                    this.tasks[index].completed = !this.tasks[index].completed;

                    const formData = new FormData();
                    formData.append('action', 'toggle');
                    formData.append('task_id', task.id);

                    fetch('api.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('API Error');
                        })
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

                    fetch('api.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('API Error');
                        })
                        .catch(error => {
                            console.error('Delete Error:', error);
                            this.tasks.splice(index, 0, removedTask);
                            alert('Failed to delete task.');
                        });
                },
                reorderTasks(newOrder) {
                    // newOrder is an array of task IDs in the new order
                    fetch('api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'reorder',
                            orderedTaskIds: newOrder
                        })
                    });
                }
            }));
        });
    </script>
</div> <!-- End Alpine component -->

<?php include __DIR__ . '/../includes/footer.php'; // Include shared footer ?>