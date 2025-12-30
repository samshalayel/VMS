<?php

require __DIR__ . '/vendor/autoload.php';

use VMS\ClickUpClient;

$dotenv = parse_ini_file(__DIR__ . '/.env');
$clickup = new ClickUpClient(
    $dotenv['CLICKUP_API_TOKEN'],
    $dotenv['CLICKUP_LIST_ID']
);

echo "=== Completing Task 3: Define User Roles & Permissions ===\n\n";

$taskId = '86ew08jbt';

// Add completion comment
echo "Adding completion comment...\n";
$result = $clickup->addComment($taskId,
    "✅ **Successfully Completed!**\n\n" .
    "**5 System Roles Defined:**\n" .
    "1. **Admin (مدير النظام)** - Level 100\n" .
    "   - Full system access\n" .
    "   - All administrative capabilities\n" .
    "   - User, facility, and role management\n\n" .
    "2. **Supervisor (مشرف)** - Level 80\n" .
    "   - Regional/program oversight\n" .
    "   - Manage facilities and health workers\n" .
    "   - View reports for assigned scope\n\n" .
    "3. **Health Worker (عامل صحي)** - Level 50\n" .
    "   - Register beneficiaries\n" .
    "   - Record vaccinations\n" .
    "   - Print vaccination cards\n\n" .
    "4. **Storekeeper (أمين مخزن)** - Level 50\n" .
    "   - Manage vaccine inventory\n" .
    "   - Track cold chain\n" .
    "   - Monitor stock levels\n\n" .
    "5. **Viewer (مشاهد)** - Level 10\n" .
    "   - Read-only access\n" .
    "   - View dashboards and reports\n" .
    "   - Monitor campaign progress\n\n" .
    "**Database Implementation:**\n" .
    "- ✅ ROLES table created with hierarchy levels\n" .
    "- ✅ PERMISSIONS table created\n" .
    "- ✅ ROLE_PERMISSIONS mapping table created\n" .
    "- ✅ All 5 roles seeded with Arabic/English names\n" .
    "- ✅ Documentation created: user_roles_permissions.md\n\n" .
    "**Permission Categories Defined:**\n" .
    "- Users Management (users.*)\n" .
    "- Facility Management (facilities.*)\n" .
    "- Beneficiary Management (beneficiaries.*)\n" .
    "- Vaccination Operations (vaccinations.*)\n" .
    "- Inventory Management (inventory.*)\n" .
    "- Reports & Analytics (reports.*)\n" .
    "- System Administration (settings.*)\n\n" .
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
}

echo "\n=== Task 3 Complete ===\n";
