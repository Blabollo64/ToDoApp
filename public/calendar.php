<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
$userId = getCurrentUserId();

// Fetch all tasks with due dates
$tasks = getAllTasks($userId); // Make sure this returns due_date as well

$pageTitle = "Task Calendar";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto max-w-2xl mt-10 bg-white p-6 rounded shadow-md">
    <h1 class="text-2xl font-bold mb-4 text-center">Task Calendar</h1>
    <div id="calendar"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: <?php
            $events = [];
            foreach ($tasks as $task) {
                if (!empty($task['due_date'])) {
                    $events[] = [
                        'title' => $task['title'],
                        'start' => $task['due_date'],
                        'allDay' => true
                    ];
                }
            }
            echo json_encode($events);
        ?>
    });
    calendar.render();
});
</script>