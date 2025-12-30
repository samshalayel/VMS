<?php

require_once __DIR__ . '/vendor/autoload.php';

use VMS\ClickUpClient;

$dotenv = parse_ini_file('.env');

$apiToken = $dotenv['CLICKUP_API_TOKEN'];
$listId = $dotenv['CLICKUP_LIST_ID'];

$clickup = new ClickUpClient($apiToken);

echo "=== VMS Project Tasks (Filtered) ===\n\n";

$tasks = $clickup->getTasks($listId);

// Filter out test tasks
$realTasks = [];
if (isset($tasks['tasks'])) {
    foreach ($tasks['tasks'] as $task) {
        if (strpos($task['name'], 'Sample VMS') === false &&
            strpos($task['name'], 'Updated VMS Task') === false) {
            $realTasks[] = $task;
        }
    }
}

// Sort by priority (high first)
usort($realTasks, function($a, $b) {
    $priorityOrder = ['urgent' => 0, 'high' => 1, 'normal' => 2, 'low' => 3];
    $aPriority = $priorityOrder[$a['priority']['priority'] ?? 'normal'];
    $bPriority = $priorityOrder[$b['priority']['priority'] ?? 'normal'];
    return $aPriority - $bPriority;
});

foreach ($realTasks as $index => $task) {
    $priorityIcon = [
        'urgent' => '🔴',
        'high' => '🟠',
        'normal' => '🟡',
        'low' => '🟢'
    ];
    $icon = $priorityIcon[$task['priority']['priority'] ?? 'normal'];

    echo ($index + 1) . ". " . $icon . " " . $task['name'] . "\n";
    echo "   Priority: " . ($task['priority']['priority'] ?? 'none') . "\n";
    echo "   Status: " . $task['status']['status'] . "\n";
    echo "   " . substr($task['description'], 0, 80) . "...\n\n";
}

echo "Choose a task to start (1-" . count($realTasks) . ") or 0 to see recommended order: ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$choice = intval(trim($line));
fclose($handle);

if ($choice === 0) {
    echo "\n=== Recommended Implementation Order ===\n\n";
    $recommended = [
        'Define Facility Data Model',
        'Create Facilities Database Schema',
        'Define User Roles & Permissions',
        'Users Database Schema',
        'Users CRUD Backend API',
        'Link Users to Facilities',
        'Facilities CRUD Backend API',
        'Facilities Management UI',
        'Users Management UI',
        'Basic Audit Trail'
    ];

    foreach ($recommended as $idx => $taskName) {
        // Find the task
        foreach ($realTasks as $task) {
            if (strpos($task['name'], $taskName) !== false) {
                echo ($idx + 1) . ". " . $task['name'] . "\n";
                echo "   ID: " . $task['id'] . "\n";
                echo "   Why: ";
                switch ($idx) {
                    case 0:
                        echo "تعريف نموذج البيانات الأساسي للمنشآت الصحية\n";
                        break;
                    case 1:
                        echo "بناء قاعدة البيانات Oracle للمنشآت\n";
                        break;
                    case 2:
                        echo "تحديد صلاحيات المستخدمين (Admin, Supervisor, etc.)\n";
                        break;
                    case 3:
                        echo "إنشاء جداول المستخدمين في Oracle\n";
                        break;
                    default:
                        echo "Building on previous tasks\n";
                }
                echo "\n";
                break;
            }
        }
    }
    exit;
}

if ($choice > 0 && $choice <= count($realTasks)) {
    $selectedTask = $realTasks[$choice - 1];

    echo "\n=== Starting: " . $selectedTask['name'] . " ===\n\n";

    // Get full task details
    $taskDetails = $clickup->getTask($selectedTask['id']);

    echo "Description:\n" . $taskDetails['description'] . "\n\n";

    if (isset($taskDetails['subtasks']) && !empty($taskDetails['subtasks'])) {
        echo "Subtasks:\n";
        foreach ($taskDetails['subtasks'] as $subtask) {
            echo "  - [" . $subtask['status']['status'] . "] " . $subtask['name'] . "\n";
        }
        echo "\n";
    }

    // Add a comment that work has started
    $clickup->addComment($selectedTask['id'], '🚀 بدأ العمل على هذه المهمة - Laravel + Oracle VMS System');

    echo "✅ Comment added to ClickUp\n";
    echo "🔗 Task URL: " . $selectedTask['url'] . "\n\n";

    // Save task info for reference
    file_put_contents('current_task.json', json_encode($taskDetails, JSON_PRETTY_PRINT));
    echo "💾 Task details saved to current_task.json\n\n";

    echo "Ready to implement! What do you need help with?\n";
} else {
    echo "Invalid choice\n";
}
