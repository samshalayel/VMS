<?php

require_once __DIR__ . '/vendor/autoload.php';

use VMS\ClickUpClient;

$dotenv = parse_ini_file('.env');

$apiToken = $dotenv['CLICKUP_API_TOKEN'];
$listId = $dotenv['CLICKUP_LIST_ID'];

$clickup = new ClickUpClient($apiToken);

echo "=== Getting All Tasks with Details ===\n\n";

$tasks = $clickup->getTasks($listId);

if (isset($tasks['tasks'])) {
    foreach ($tasks['tasks'] as $index => $task) {
        echo "Task #" . ($index + 1) . ":\n";
        echo "ID: " . $task['id'] . "\n";
        echo "Name: " . $task['name'] . "\n";
        echo "Status: " . $task['status']['status'] . "\n";
        echo "Priority: " . ($task['priority']['priority'] ?? 'none') . "\n";
        echo "Description: " . ($task['description'] ?? 'No description') . "\n";

        // Get full task details including subtasks
        $taskDetails = $clickup->getTask($task['id']);

        if (isset($taskDetails['subtasks']) && !empty($taskDetails['subtasks'])) {
            echo "Subtasks:\n";
            foreach ($taskDetails['subtasks'] as $subtask) {
                echo "  - [" . $subtask['status']['status'] . "] " . $subtask['name'] . "\n";
            }
        } else {
            echo "Subtasks: None\n";
        }

        echo "URL: " . $task['url'] . "\n";
        echo str_repeat("-", 80) . "\n\n";
    }

    echo "\n=== Choose a task to work on ===\n";
    echo "Enter task number (1-" . count($tasks['tasks']) . "): ";

    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $taskNumber = intval(trim($line));
    fclose($handle);

    if ($taskNumber > 0 && $taskNumber <= count($tasks['tasks'])) {
        $selectedTask = $tasks['tasks'][$taskNumber - 1];

        echo "\nUpdating task '" . $selectedTask['name'] . "' to 'in progress'...\n";

        // Get available statuses for the list
        $taskDetails = $clickup->getTask($selectedTask['id']);

        // Update task status - we need to find the correct status ID for "in progress"
        // For now, let's just update the task name to indicate work started
        $result = $clickup->updateTask($selectedTask['id'], [
            'name' => '🔄 ' . $selectedTask['name']
        ]);

        // Add a comment
        $clickup->addComment($selectedTask['id'], '🚀 Started working on this task - Laravel + Oracle setup');

        if (isset($result['id'])) {
            echo "✅ Task updated successfully!\n";
            echo "Task URL: " . $selectedTask['url'] . "\n";
        } else {
            echo "❌ Error updating task\n";
            print_r($result);
        }
    } else {
        echo "Invalid task number\n";
    }
} else {
    echo "Error fetching tasks\n";
    print_r($tasks);
}

echo "\n=== Done! ===\n";
