<?php

require __DIR__ . '/../vms-laravel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

echo "=== Reading Medical Facilities Excel File ===\n\n";

$excelFile = 'D:\Campaigns\Medical_Points.xlsx';

if (!file_exists($excelFile)) {
    echo "❌ Error: File not found at $excelFile\n";
    exit(1);
}

try {
    echo "Loading Excel file...\n";
    $spreadsheet = IOFactory::load($excelFile);
    $worksheet = $spreadsheet->getActiveSheet();

    echo "✅ File loaded successfully\n\n";

    // Get highest row and column
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();

    echo "Sheet: " . $worksheet->getTitle() . "\n";
    echo "Rows: $highestRow\n";
    echo "Columns: $highestColumn\n\n";

    // Display first 15 rows to understand structure
    echo "=== Raw Data (First 15 Rows) ===\n";
    echo str_repeat("=", 150) . "\n";

    for ($row = 1; $row <= min(15, $highestRow); $row++) {
        echo "Row $row: ";
        $rowData = [];
        for ($col = 'A'; $col <= min('J', $highestColumn); $col++) {
            $cellValue = $worksheet->getCell($col . $row)->getValue();
            if ($cellValue !== null && $cellValue !== '') {
                $rowData[] = "$col:$cellValue";
            }
        }
        echo implode(' | ', $rowData) . "\n";
    }
    echo str_repeat("=", 150) . "\n\n";

    echo "\nTotal data rows: " . ($highestRow - 1) . "\n";

    // Summary
    echo "\n=== Summary ===\n";
    echo "Total facilities in file: " . ($highestRow - 1) . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Read Complete ===\n";
