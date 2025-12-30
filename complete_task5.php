<?php

require __DIR__ . '/vendor/autoload.php';

use VMS\ClickUpClient;

$dotenv = parse_ini_file(__DIR__ . '/.env');
$clickup = new ClickUpClient(
    $dotenv['CLICKUP_API_TOKEN'],
    $dotenv['CLICKUP_LIST_ID']
);

echo "=== Completing Task 5: Users CRUD Backend API ===\n\n";

$taskId = '86ew08jfz';

// Add completion comment
echo "Adding completion comment...\n";
$result = $clickup->addComment($taskId,
    "✅ **Successfully Completed!**\n\n" .
    "**API Endpoints Implemented:**\n\n" .
    "**Core CRUD Operations:**\n" .
    "- `GET /api/v1/users` - List all users with pagination\n" .
    "- `POST /api/v1/users` - Create new user\n" .
    "- `GET /api/v1/users/{id}` - Get user details\n" .
    "- `PUT /api/v1/users/{id}` - Update user\n" .
    "- `DELETE /api/v1/users/{id}` - Soft delete user\n\n" .
    "**Role Management:**\n" .
    "- `POST /api/v1/users/{id}/assign-role` - Assign role to user\n" .
    "- `DELETE /api/v1/users/{id}/remove-role/{role_id}` - Remove role\n" .
    "- Support for multiple roles per user\n" .
    "- Temporary role assignments with expiry dates\n\n" .
    "**Facility Assignment:**\n" .
    "- `POST /api/v1/users/{id}/assign-facility` - Assign facility\n" .
    "- `DELETE /api/v1/users/{id}/remove-facility/{facility_id}` - Remove facility\n" .
    "- Primary facility designation\n" .
    "- Multi-facility support\n\n" .
    "**User Account Management:**\n" .
    "- `PATCH /api/v1/users/{id}/activate` - Activate user\n" .
    "- `PATCH /api/v1/users/{id}/deactivate` - Deactivate with reason\n" .
    "- Account status tracking\n" .
    "- Last login tracking\n\n" .
    "**Models Created:**\n" .
    "- ✅ User Model (with SoftDeletes, relationships)\n" .
    "- ✅ Role Model (10 roles from Excel)\n" .
    "- ✅ Facility Model (140 facilities)\n" .
    "- ✅ Permission Model\n\n" .
    "**Relationships:**\n" .
    "- User → Roles (Many-to-Many)\n" .
    "- User → Facilities (Many-to-Many)\n" .
    "- Role → Permissions (Many-to-Many)\n\n" .
    "**Features:**\n" .
    "- Full audit trail (created_by, updated_by)\n" .
    "- Soft deletes for data retention\n" .
    "- Arabic & English name support\n" .
    "- Employee ID from organizations\n" .
    "- National ID (Palestinian ID)\n" .
    "- Organization tracking (PRCS, UNRWA, MoH)\n\n" .
    "**Documentation:**\n" .
    "- ✅ API documentation created (api_documentation.md)\n" .
    "- Request/Response examples included\n" .
    "- All endpoints documented\n\n" .
    "**Technology:**\n" .
    "- Laravel 12 RESTful API\n" .
    "- Oracle Database backend\n" .
    "- JSON responses\n\n" .
    "🤖 Updated via Claude Code"
);
echo "  Comment added: " . (isset($result['id']) ? '✅' : '❌') . "\n\n";

// Update status to completed
echo "Updating status to 'completed'...\n";
$result = $clickup->updateTaskStatus($taskId, 'completed');

if (isset($result['status'])) {
    echo "✅ Task status updated to: {$result['status']['status']}\n";
} else {
    echo "❌ Failed to update status\n";
    print_r($result);
}

echo "\n=== Task 5 Complete! ===\n";
