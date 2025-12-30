<?php

require __DIR__ . '/vendor/autoload.php';

use VMS\ClickUpClient;

// Load environment variables
$dotenv = parse_ini_file(__DIR__ . '/.env');

$clickup = new ClickUpClient(
    $dotenv['CLICKUP_API_TOKEN'],
    $dotenv['CLICKUP_LIST_ID']
);

echo "=== Updating ClickUp Tasks ===\n\n";

// Task 1: Define Facility Data Model (86ew08j6j)
echo "Task 1: Define Facility Data Model\n";
$result = $clickup->addComment('86ew08j6j',
    "✅ **Completed Successfully!**\n\n" .
    "**Data Model Defined:**\n" .
    "- 5 Governorates (المحافظات): شمال غزة، غزة، دير البلح، خان يونس، رفح\n" .
    "- 18 Districts (المديريات) across all governorates\n" .
    "- Comprehensive Facilities schema with:\n" .
    "  - Basic info (code, names in Arabic/English, type)\n" .
    "  - Administrative location (governorate, district, address)\n" .
    "  - GPS coordinates (latitude, longitude, altitude)\n" .
    "  - Managing organization (UNICEF, UNRWA, Red Crescent, MoH, Other)\n" .
    "  - Capacity and cold storage capabilities\n" .
    "  - Active/inactive status with activation dates\n" .
    "  - Audit fields (created_by, updated_by, timestamps)\n\n" .
    "**Implementation:**\n" .
    "- Laravel migrations created and executed\n" .
    "- Oracle database schema ready\n" .
    "- Foreign key relationships established\n" .
    "- Proper indexing for performance\n\n" .
    "🤖 Updated via Claude Code"
);
echo "  Comment added: " . (isset($result['id']) ? '✅' : '❌') . "\n\n";

// Task 2: Create Facilities Database Schema (86ew08j81)
echo "Task 2: Create Facilities Database Schema\n";
$result = $clickup->addComment('86ew08j81',
    "✅ **Completed Successfully with REAL DATA!**\n\n" .
    "**Database Schema Created:**\n" .
    "- ✅ GOVERNORATES table (5 records)\n" .
    "- ✅ DISTRICTS table (18 records)\n" .
    "- ✅ FACILITIES table (140 REAL facilities from Medical_Points.xlsx)\n\n" .
    "**Real Facilities Data Seeded:**\n" .
    "- شمال غزة (North Gaza): 11 facilities\n" .
    "- غزة (Gaza City): 31 facilities\n" .
    "- دير البلح (Deir al-Balah): 48 facilities\n" .
    "- خان يونس (Khan Yunis): 50 facilities\n" .
    "- رفح (Rafah): 0 (excluded - under Israeli occupation)\n\n" .
    "**Total: 140 active medical facilities**\n\n" .
    "**Facility Types:**\n" .
    "- Health Centers: 68\n" .
    "- Mobile Points: 61\n" .
    "- Hospitals: 11\n\n" .
    "**Managing Organizations:**\n" .
    "- MoH: 115 facilities\n" .
    "- Red Crescent (PRCS): 19 facilities\n" .
    "- Other NGOs: 5 facilities\n" .
    "- UNRWA: 1 facility\n\n" .
    "**Cold Storage:** 129/140 facilities (92.1%) have cold storage capabilities\n\n" .
    "**Database:** Oracle AI Database 26ai Free on 144.172.114.103\n" .
    "**Connection:** Stable via Laravel OCI8 driver\n\n" .
    "🤖 Updated via Claude Code"
);
echo "  Comment added: " . (isset($result['id']) ? '✅' : '❌') . "\n\n";

// Get list to see all tasks
echo "Fetching all tasks from list...\n";
$tasks = $clickup->getList($dotenv['CLICKUP_LIST_ID']);

if (isset($tasks['tasks'])) {
    echo "\n=== All Tasks in List ===\n";
    echo str_repeat("=", 100) . "\n";
    printf("%-15s %-50s %-20s\n", "Task ID", "Name", "Status");
    echo str_repeat("-", 100) . "\n";

    foreach ($tasks['tasks'] as $task) {
        printf("%-15s %-50s %-20s\n",
            $task['id'],
            substr($task['name'], 0, 50),
            $task['status']['status']
        );
    }
    echo str_repeat("=", 100) . "\n\n";

    echo "Total tasks: " . count($tasks['tasks']) . "\n";
} else {
    echo "Could not fetch tasks list\n";
}

echo "\n=== ClickUp Update Complete ===\n";
