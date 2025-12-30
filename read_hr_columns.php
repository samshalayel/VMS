<?php

require __DIR__ . '/../vms-laravel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

echo "=== Reading Human Resource Columns ===\n\n";

$excelFile = 'D:\Campaigns\Medical_Points.xlsx';

try {
    $spreadsheet = IOFactory::load($excelFile);
    $worksheet = $spreadsheet->getActiveSheet();

    // Columns AH (34) to AM (39) for Human Resource
    echo "=== Human Resource Columns (AH to AM) ===\n";
    echo str_repeat("=", 120) . "\n";

    // Read headers from row 2
    $hrColumns = [];
    for ($col = 'AH'; $col <= 'AM'; $col++) {
        $header = trim($worksheet->getCell($col . '2')->getValue() ?? '');
        if (!empty($header)) {
            $hrColumns[$col] = $header;
            echo "$col: $header\n";
        }
    }

    echo str_repeat("=", 120) . "\n\n";

    // Show sample data from first 10 facilities
    echo "=== Sample HR Data (First 10 Facilities) ===\n";
    echo str_repeat("=", 120) . "\n";

    for ($row = 4; $row <= 13; $row++) {
        $facilityName = substr($worksheet->getCell('B' . $row)->getValue() ?? '', 0, 40);

        if (empty(trim($facilityName))) {
            continue;
        }

        echo "Row $row: $facilityName\n";

        foreach ($hrColumns as $col => $role) {
            $value = $worksheet->getCell($col . $row)->getValue();
            if (!empty($value)) {
                printf("  %-45s: %s\n", $role, $value);
            }
        }
        echo str_repeat("-", 120) . "\n";
    }

    // Statistics
    echo "\n=== HR Statistics Across All Facilities ===\n";
    echo str_repeat("=", 120) . "\n";

    $stats = [];
    foreach ($hrColumns as $col => $role) {
        $stats[$role] = [
            'total' => 0,
            'facilities_with_role' => 0,
        ];
    }

    for ($row = 4; $row <= 150; $row++) {
        $facilityName = trim($worksheet->getCell('B' . $row)->getValue() ?? '');

        if (empty($facilityName) ||
            stripos($facilityName, 'total') !== false ||
            stripos($facilityName, 'sub total') !== false) {
            continue;
        }

        foreach ($hrColumns as $col => $role) {
            $value = $worksheet->getCell($col . $row)->getValue();
            if (!empty($value) && is_numeric($value)) {
                $stats[$role]['total'] += (int)$value;
                $stats[$role]['facilities_with_role']++;
            }
        }
    }

    foreach ($stats as $role => $data) {
        printf("%-45s: Total: %4d | Facilities: %3d\n",
            $role,
            $data['total'],
            $data['facilities_with_role']
        );
    }

    echo str_repeat("=", 120) . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Done ===\n";
