<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
$userId = getCurrentUserId();

$tasks = getAllTasks($userId); // This should return all tasks with due_date

if (empty($tasks)) {
    $tasks = []; // Ensure $tasks is an array even if no tasks are found
}

// print_r($tasks); // <-- Place it here, after $tasks is defined  

$pageTitle = "Task Calendar";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto max-w-2xl mt-10 bg-white p-6 rounded shadow-md">
    <h1 class="text-2xl font-bold mb-4 text-center">Task Calendar</h1>
    <div id="calendar"></div>
</div>

<div id="calendarTaskModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:1000;">
    <form id="calendarTaskForm" style="background:white; padding:2rem; border-radius:8px; min-width:300px;">
        <h2 class="mb-2 font-bold">Edit Task Date/Time</h2>
        <input type="hidden" name="task_id" id="modalTaskId">
        <div class="mb-2">
            <label class="block mb-1">Title:</label>
            <input type="text" id="modalTaskTitle" class="border rounded px-2 py-1 w-full" readonly>
        </div>
        <div class="flex space-x-2 mb-2">
            <input type="date" name="due_date" id="modalDueDate" class="border rounded px-2 py-1" required>
            <input type="time" name="due_time" id="modalDueTime" class="border rounded px-2 py-1">
        </div>
        <div class="flex space-x-2">
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
    <button type="button" onclick="deleteCalendarTask()" class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
    <button type="button" onclick="closeCalendarModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">Cancel</button>
</div>
    </form>
</div>

<script>
    function deleteCalendarTask() {
    if (!confirm('Are you sure you want to delete this task?')) return;
    var taskId = document.getElementById('modalTaskId').value;
    var formData = new FormData();
    formData.append('action', 'delete');
    formData.append('task_id', taskId);

    fetch('index.php', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (response.ok) {
            closeCalendarModal();
            location.reload();
        } else {
            alert('Failed to delete task.');
        }
    });
}

function closeCalendarModal() {
    document.getElementById('calendarTaskModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: <?php
            $events = [];
            foreach ($tasks as $task) {
                if (!empty($task['due_date'])) {
                    // Split date and time for modal
                    $date = substr($task['due_date'], 0, 10);
                    $time = strlen($task['due_date']) > 10 ? substr($task['due_date'], 11, 5) : '';
                    $events[] = [
                        'id' => $task['id'],
                        'title' => $task['title'],
                        'start' => $task['due_date'],
                        'allDay' => false,
                        'extendedProps' => [
                            'due_date' => $date,
                            'due_time' => $time
                        ]
                    ];
                }
            }
            echo json_encode($events);
        ?>,
        eventClick: function(info) {
            // Fill modal fields
            document.getElementById('modalTaskId').value = info.event.id;
            document.getElementById('modalTaskTitle').value = info.event.title;
            document.getElementById('modalDueDate').value = info.event.extendedProps.due_date;
            document.getElementById('modalDueTime').value = info.event.extendedProps.due_time;
            document.getElementById('calendarTaskModal').style.display = 'flex';
        }
    });
    calendar.render();

    // Handle modal form submit
    document.getElementById('calendarTaskForm').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'edit');
        fetch('index.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                closeCalendarModal();
                location.reload();
            } else {
                alert('Failed to update task.');
            }
        });
    };
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>