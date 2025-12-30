<?php

require __DIR__ . '/../vms-laravel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

echo "=== Checking Excel Last Columns for Roles ===\n\n";

$excelFile = 'D:\Campaigns\Medical_Points.xlsx';

try {
    $spreadsheet = IOFactory::load($excelFile);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestColumn = $worksheet->getHighestColumn();

    echo "Highest column: $highestColumn\n\n";

    // Show header row with ALL columns
    echo "=== Header Row (All Columns) ===\n";
    echo str_repeat("=", 150) . "\n";

    $col = 'A';
    while ($col <= $highestColumn) {
        $cellValue = $worksheet->getCell($col . '2')->getValue();
        if ($cellValue) {
            printf("Column %-3s: %s\n", $col, $cellValue);
        }
        $col++;
    }

    echo str_repeat("=", 150) . "\n\n";

    // Show last 10 columns specifically
    echo "=== Last 10 Columns Data (Rows 2-10) ===\n";
    echo str_repeat("=", 150) . "\n";

    // Get column index for highestColumn (handles multi-character columns like EZ)
    $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    $startColIndex = max(1, $highestColIndex - 9);
    $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startColIndex);

    echo "Showing columns from $startCol (index $startColIndex) to $highestColumn (index $highestColIndex)\n\n";

    for ($row = 2; $row <= 10; $row++) {
        echo "Row $row:\n";

        for ($colIndex = $startColIndex; $colIndex <= $highestColIndex; $colIndex++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $header = $worksheet->getCell($col . '2')->getValue();
            $cellValue = $worksheet->getCell($col . $row)->getValue();

            if ($header) {
                printf("  %-30s: %s\n", substr($header, 0, 30), $cellValue ?? '(empty)');
            }
        }
        echo str_repeat("-", 150) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Done ===\n";
