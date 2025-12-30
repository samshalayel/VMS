<?php

require_once __DIR__ . '/vendor/autoload.php';

use VMS\ClickUpClient;

$dotenv = parse_ini_file('.env');

$apiToken = $dotenv['CLICKUP_API_TOKEN'];
$listId = $dotenv['CLICKUP_LIST_ID'];

$clickup = new ClickUpClient($apiToken);

// Get list details to see available statuses
echo "=== Getting Available Statuses ===\n\n";
$listDetails = $clickup->getList($listId);

if (isset($listDetails['statuses'])) {
    echo "Available statuses:\n";
    foreach ($listDetails['statuses'] as $index => $status) {
        echo ($index + 1) . ". " . $status['status'] . " (" . $status['type'] . ")\n";
    }
    echo "\n";
} else {
    echo "Could not get statuses\n";
    print_r($listDetails);
    exit;
}

// Get all tasks
$tasks = $clickup->getTasks($listId);

// Filter real tasks
$realTasks = [];
if (isset($tasks['tasks'])) {
    foreach ($tasks['tasks'] as $task) {
        if (strpos($task['name'], 'Sample VMS') === false &&
            strpos($task['name'], 'Updated VMS Task') === false) {
            $realTasks[] = $task;
        }
    }
}

echo "=== Project Tasks ===\n\n";
foreach ($realTasks as $index => $task) {
    echo ($index + 1) . ". " . $task['name'] . "\n";
    echo "   Current Status: " . $task['status']['status'] . "\n";
    echo "   Priority: " . ($task['priority']['priority'] ?? 'none') . "\n\n";
}

echo "Choose a task to update (1-" . count($realTasks) . "): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$taskChoice = intval(trim($line));

if ($taskChoice < 1 || $taskChoice > count($realTasks)) {
    echo "Invalid choice\n";
    exit;
}

$selectedTask = $realTasks[$taskChoice - 1];

echo "\nSelected: " . $selectedTask['name'] . "\n";
echo "Current Status: " . $selectedTask['status']['status'] . "\n\n";

echo "Choose new status:\n";
foreach ($listDetails['statuses'] as $index => $status) {
    echo ($index + 1) . ". " . $status['status'] . "\n";
}

echo "\nChoice: ";
$line = fgets($handle);
$statusChoice = intval(trim($line));
fclose($handle);

if ($statusChoice < 1 || $statusChoice > count($listDetails['statuses'])) {
    echo "Invalid choice\n";
    exit;
}

$newStatus = $listDetails['statuses'][$statusChoice - 1]['status'];

echo "\nUpdating task to '" . $newStatus . "'...\n";

$result = $clickup->updateTaskStatus($selectedTask['id'], $newStatus);

if (isset($result['id'])) {
    echo "✅ Task status updated successfully!\n";
    echo "New Status: " . $result['status']['status'] . "\n";
    echo "🔗 " . $selectedTask['url'] . "\n";

    // Add a comment
    $comment = "Status changed to: " . $newStatus;
    $clickup->addComment($selectedTask['id'], $comment);
    echo "💬 Comment added\n";
} else {
    echo "❌ Error updating task\n";
    print_r($result);
}

echo "\n=== Done! ===\n";
