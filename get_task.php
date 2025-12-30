<?php

require __DIR__ . '/vendor/autoload.php';

use VMS\ClickUpClient;

$dotenv = parse_ini_file(__DIR__ . '/.env');
$clickup = new ClickUpClient(
    $dotenv['CLICKUP_API_TOKEN'],
    $dotenv['CLICKUP_LIST_ID']
);

// Task 3: Define User Roles & Permissions - ID: 86ew08jbt
$taskId = '86ew08jbt';

echo "=== Fetching Task Details ===\n\n";

$task = $clickup->getTask($taskId);

if (isset($task['id'])) {
    echo "Task ID: {$task['id']}\n";
    echo "Name: {$task['name']}\n";
    echo "Status: {$task['status']['status']}\n";
    echo "Priority: {$task['priority']['priority']}\n";
    echo "Description: {$task['description']}\n";
    echo "URL: {$task['url']}\n";
} else {
    echo "Failed to fetch task\n";
    print_r($task);
}

echo "\n=== Done ===\n";
