<?php

require __DIR__ . '/../vms-laravel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

echo "=== Finding Governorate Ranges ===\n\n";

$excelFile = 'D:\Campaigns\Medical_Points.xlsx';

try {
    $spreadsheet = IOFactory::load($excelFile);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();

    echo "Scanning all rows for governorate headers...\n";
    echo str_repeat("=", 100) . "\n";

    $lastGov = '';
    $govStart = [];
    $currentGov = '';
    $facilityCount = 0;

    for ($row = 1; $row <= $highestRow; $row++) {
        $governorate = trim($worksheet->getCell('A' . $row)->getValue() ?? '');
        $facilityName = trim($worksheet->getCell('B' . $row)->getValue() ?? '');

        // Check if this is a governorate header
        if (!empty($governorate) &&
            $governorate !== 'Governorate' &&
            (stripos($governorate, 'Gaza') !== false ||
             stripos($governorate, 'Zone') !== false ||
             stripos($governorate, 'Khan') !== false ||
             stripos($governorate, 'Rafah') !== false ||
             stripos($governorate, 'North') !== false)) {

            // Print previous governorate summary
            if (!empty($currentGov)) {
                printf("  [Row %3d - %3d] %-25s: %3d facilities\n",
                    $govStart[$currentGov],
                    $row - 1,
                    $currentGov,
                    $facilityCount
                );
            }

            // Start new governorate
            $currentGov = $governorate;
            $govStart[$currentGov] = $row;
            $facilityCount = 0;

            echo str_repeat("-", 100) . "\n";
            printf("Row %3d: NEW GOVERNORATE: %s\n", $row, $governorate);
        }

        // Count facilities (non-empty, non-total rows)
        if (!empty($facilityName) &&
            stripos($facilityName, 'total') === false &&
            stripos($facilityName, 'sub total') === false) {
            $facilityCount++;

            // Show first 3 facilities of each gov
            if ($facilityCount <= 3) {
                printf("    Row %3d: %s\n", $row, substr($facilityName, 0, 70));
            }
        }

        // Check for "Sub Total" or "Total" which marks end of section
        if (!empty($facilityName) &&
            (stripos($facilityName, 'sub total') !== false ||
             stripos($facilityName, 'grand total') !== false)) {
            printf("    Row %3d: [%s]\n", $row, $facilityName);
        }
    }

    // Print last governorate
    if (!empty($currentGov)) {
        printf("  [Row %3d - %3d] %-25s: %3d facilities\n",
            $govStart[$currentGov],
            $highestRow,
            $currentGov,
            $facilityCount
        );
    }

    echo str_repeat("=", 100) . "\n\n";

    echo "Summary of Ranges:\n";
    echo str_repeat("=", 100) . "\n";
    foreach ($govStart as $gov => $start) {
        printf("%-30s starts at row %d\n", $gov, $start);
    }
    echo str_repeat("=", 100) . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Done ===\n";
