<?php

require __DIR__ . '/vendor/autoload.php';

use VMS\ClickUpClient;

// Load environment variables
$dotenv = parse_ini_file(__DIR__ . '/.env');

$clickup = new ClickUpClient(
    $dotenv['CLICKUP_API_TOKEN'],
    $dotenv['CLICKUP_LIST_ID']
);

echo "=== Completing Task 1: Define Facility Data Model ===\n\n";

// Task 1: 86ew08j6j - Change status to completed
$taskId = '86ew08j6j';

echo "Updating status to 'completed'...\n";
$result = $clickup->updateTaskStatus($taskId, 'completed');

if (isset($result['status'])) {
    echo "✅ Task status updated to: {$result['status']['status']}\n";
} else {
    echo "❌ Failed to update status\n";
    print_r($result);
}

echo "\n=== Done ===\n";
