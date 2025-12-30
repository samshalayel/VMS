<?php

require __DIR__ . '/../vms-laravel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

echo "=== Checking Gaza Records (16-47) ===\n\n";

$excelFile = 'D:\Campaigns\Medical_Points.xlsx';

try {
    $spreadsheet = IOFactory::load($excelFile);
    $worksheet = $spreadsheet->getActiveSheet();

    echo "Rows 16-47 from Excel:\n";
    echo str_repeat("=", 120) . "\n";

    for ($row = 16; $row <= 47; $row++) {
        $governorate = trim($worksheet->getCell('A' . $row)->getValue() ?? '');
        $facilityName = trim($worksheet->getCell('B' . $row)->getValue() ?? '');
        $storageLevel = trim($worksheet->getCell('C' . $row)->getValue() ?? '');
        $facilityType = trim($worksheet->getCell('E' . $row)->getValue() ?? '');

        if (!empty($facilityName)) {
            printf("Row %2d: Gov: %-15s | Facility: %-60s | Type: %-15s | Storage: %s\n",
                $row,
                $governorate,
                substr($facilityName, 0, 60),
                $facilityType,
                $storageLevel
            );
        } elseif (!empty($governorate)) {
            printf("Row %2d: [Governorate Header: %s]\n", $row, $governorate);
        }
    }

    echo str_repeat("=", 120) . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Done ===\n";
