<?php

require __DIR__ . '/../vms-laravel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

echo "=== Scanning ALL Excel Columns ===\n\n";

$excelFile = 'D:\Campaigns\Medical_Points.xlsx';

try {
    $spreadsheet = IOFactory::load($excelFile);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestColumn = $worksheet->getHighestColumn();
    $highestColIndex = Coordinate::columnIndexFromString($highestColumn);

    echo "Total columns: $highestColIndex (A to $highestColumn)\n\n";

    // Scan ALL columns for headers
    echo "=== Columns with Data (checking row 2 for headers) ===\n";
    echo str_repeat("=", 120) . "\n";

    $columnsWithData = [];

    for ($colIndex = 1; $colIndex <= $highestColIndex; $colIndex++) {
        $col = Coordinate::stringFromColumnIndex($colIndex);
        $header = trim($worksheet->getCell($col . '2')->getValue() ?? '');

        if (!empty($header)) {
            $columnsWithData[$col] = $header;
            printf("%-5s: %s\n", $col, $header);
        }
    }

    echo str_repeat("=", 120) . "\n";
    echo "Total columns with headers: " . count($columnsWithData) . "\n\n";

    // Show sample data from first facility (row 4)
    echo "=== Sample Data (Row 4 - First Facility) ===\n";
    echo str_repeat("=", 120) . "\n";

    foreach ($columnsWithData as $col => $header) {
        $value = $worksheet->getCell($col . '4')->getValue();
        printf("%-50s: %s\n", substr($header, 0, 50), $value ?? '(empty)');
    }

    echo str_repeat("=", 120) . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Done ===\n";
