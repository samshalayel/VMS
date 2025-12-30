<?php

require_once __DIR__ . '/vendor/autoload.php';

use VMS\ClickUpClient;

$dotenv = parse_ini_file('.env');

$apiToken = $dotenv['CLICKUP_API_TOKEN'];
$listId = $dotenv['CLICKUP_LIST_ID'];
$teamId = $dotenv['CLICKUP_TEAM_ID'];

$clickup = new ClickUpClient($apiToken);

echo "=== ClickUp Integration Examples ===\n\n";

echo "1. Getting User Info...\n";
$user = $clickup->getUser();
print_r($user);
echo "\n";

echo "2. Getting Teams...\n";
$teams = $clickup->getTeams();
print_r($teams);
echo "\n";

echo "3. Creating a Task...\n";
$newTask = $clickup->createTask($listId, [
    'name' => 'Sample VMS Task from API',
    'description' => 'This is a test task created from VMS system',
    'priority' => 3,
    'due_date' => strtotime('+7 days') * 1000,
    'tags' => ['vms', 'vendor-management']
]);
print_r($newTask);
echo "\n";

if (isset($newTask['id'])) {
    $taskId = $newTask['id'];

    echo "4. Adding Comment to Task...\n";
    $comment = $clickup->addComment($taskId, 'This is an automated comment from VMS');
    print_r($comment);
    echo "\n";

    echo "5. Updating Task...\n";
    $updatedTask = $clickup->updateTask($taskId, [
        'name' => 'Updated VMS Task - Modified via API'
    ]);
    print_r($updatedTask);
    echo "\n";

    echo "6. Getting Task Details...\n";
    $taskDetails = $clickup->getTask($taskId);
    print_r($taskDetails);
    echo "\n";
}

echo "7. Getting All Tasks...\n";
$tasks = $clickup->getTasks($listId);
print_r($tasks);
echo "\n";

echo "8. Setting up Webhook (uncomment to use)...\n";
// $webhook = $clickup->createWebhook($teamId, 'https://yourdomain.com/webhook.php', [
//     'taskCreated',
//     'taskUpdated',
//     'taskDeleted',
//     'taskCommentPosted',
//     'taskStatusUpdated'
// ]);
// print_r($webhook);

echo "9. Getting Webhooks...\n";
$webhooks = $clickup->getWebhooks($teamId);
print_r($webhooks);
echo "\n";

echo "\n=== Done! ===\n";
