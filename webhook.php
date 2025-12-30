<?php

require_once __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');

$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

$logFile = __DIR__ . '/logs/webhook.log';
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

$logEntry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'event' => $data['event'] ?? 'unknown',
    'data' => $data
];

file_put_contents($logFile, json_encode($logEntry, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);

if (isset($data['event'])) {
    switch ($data['event']) {
        case 'taskCreated':
            handleTaskCreated($data);
            break;
        case 'taskUpdated':
            handleTaskUpdated($data);
            break;
        case 'taskDeleted':
            handleTaskDeleted($data);
            break;
        case 'taskCommentPosted':
            handleTaskCommentPosted($data);
            break;
        case 'taskStatusUpdated':
            handleTaskStatusUpdated($data);
            break;
        case 'taskAssigneeUpdated':
            handleTaskAssigneeUpdated($data);
            break;
        default:
            handleGenericEvent($data);
            break;
    }
}

http_response_code(200);
echo json_encode(['status' => 'success']);

function handleTaskCreated($data)
{
    $taskId = $data['task_id'] ?? null;
    $taskName = $data['history_items'][0]['comment'] ?? 'New Task';

    error_log("New task created: {$taskName} (ID: {$taskId})");
}

function handleTaskUpdated($data)
{
    $taskId = $data['task_id'] ?? null;

    error_log("Task updated: ID {$taskId}");
}

function handleTaskDeleted($data)
{
    $taskId = $data['task_id'] ?? null;

    error_log("Task deleted: ID {$taskId}");
}

function handleTaskCommentPosted($data)
{
    $taskId = $data['task_id'] ?? null;
    $comment = $data['history_items'][0]['comment'] ?? '';

    error_log("New comment on task {$taskId}: {$comment}");
}

function handleTaskStatusUpdated($data)
{
    $taskId = $data['task_id'] ?? null;
    $status = $data['history_items'][0]['after']['status'] ?? 'unknown';

    error_log("Task {$taskId} status changed to: {$status}");
}

function handleTaskAssigneeUpdated($data)
{
    $taskId = $data['task_id'] ?? null;

    error_log("Task {$taskId} assignee updated");
}

function handleGenericEvent($data)
{
    $event = $data['event'] ?? 'unknown';

    error_log("Generic event received: {$event}");
}
