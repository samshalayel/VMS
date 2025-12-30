<?php

require __DIR__ . '/../vms-laravel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

echo "=== Extracting Medical Facilities from Excel ===\n\n";

$excelFile = 'D:\Campaigns\Medical_Points.xlsx';

if (!file_exists($excelFile)) {
    echo "❌ Error: File not found at $excelFile\n";
    exit(1);
}

try {
    $spreadsheet = IOFactory::load($excelFile);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();

    echo "✅ File loaded: $highestRow rows\n\n";

    $facilities = [];
    $currentGovernorate = '';
    $stats = [
        'total' => 0,
        'by_governorate' => [],
        'by_type' => [],
        'with_storage' => 0,
        'excluded_rafah' => 0,
    ];

    // Define row ranges for each governorate (based on actual data structure)
    $governorateRanges = [
        'North Gaza' => [4, 15],       // Rows 4-15 (12 facilities)
        'Gaza' => [16, 47],             // Rows 16-47 (31 facilities - Gaza City)
        'Middle Zone' => [48, 96],      // Rows 48-96 (48 facilities - Deir al-Balah)
        'Khanyounis' => [97, 147],      // Rows 97-147 (50 facilities - Khan Yunis)
        // Rafah after row 147 is excluded - under Israeli occupation
    ];

    // Start from row 4 (data starts here)
    for ($row = 4; $row <= $highestRow; $row++) {
        $governorate = trim($worksheet->getCell('A' . $row)->getValue() ?? '');
        $facilityName = trim($worksheet->getCell('B' . $row)->getValue() ?? '');
        $storageLevel = trim($worksheet->getCell('C' . $row)->getValue() ?? '');
        $distributionSite = trim($worksheet->getCell('D' . $row)->getValue() ?? '');
        $facilityType = trim($worksheet->getCell('E' . $row)->getValue() ?? '');

        // Determine governorate based on row ranges
        if ($row >= 4 && $row <= 15) {
            $currentGovernorate = 'North Gaza';
        } elseif ($row >= 16 && $row <= 47) {
            $currentGovernorate = 'Gaza';
        } elseif ($row >= 48 && $row <= 96) {
            $currentGovernorate = 'Middle Zone';
        } elseif ($row >= 97 && $row <= 147) {
            $currentGovernorate = 'Khanyounis';
        } else {
            // Rafah or other - skip (under Israeli occupation)
            if (!empty($facilityName) &&
                stripos($facilityName, 'sub total') === false &&
                stripos($facilityName, 'total') === false &&
                stripos($facilityName, 'grand total') === false) {
                $stats['excluded_rafah']++;
            }
            continue;
        }

        // Skip if no facility name or if it's a subtotal/total row
        if (empty($facilityName) ||
            stripos($facilityName, 'sub total') !== false ||
            stripos($facilityName, 'total') !== false ||
            stripos($facilityName, 'grand total') !== false) {
            continue;
        }

        // Create facility record
        $facility = [
            'row' => $row,
            'governorate' => $currentGovernorate,
            'name' => $facilityName,
            'storage_level' => $storageLevel,
            'distribution_site' => $distributionSite,
            'type' => $facilityType,
        ];

        $facilities[] = $facility;

        // Update stats
        $stats['total']++;

        if (!isset($stats['by_governorate'][$currentGovernorate])) {
            $stats['by_governorate'][$currentGovernorate] = 0;
        }
        $stats['by_governorate'][$currentGovernorate]++;

        if (!isset($stats['by_type'][$facilityType])) {
            $stats['by_type'][$facilityType] = 0;
        }
        $stats['by_type'][$facilityType]++;

        if (!empty($storageLevel)) {
            $stats['with_storage']++;
        }
    }

    // Display statistics
    echo "=== Extraction Statistics ===\n";
    echo str_repeat("=", 80) . "\n";
    echo "Total Facilities: {$stats['total']}\n";
    echo "Facilities with Storage: {$stats['with_storage']}\n";
    if ($stats['excluded_rafah'] > 0) {
        echo "Excluded (Rafah - under occupation): {$stats['excluded_rafah']}\n";
    }
    echo "\n";

    echo "By Governorate:\n";
    foreach ($stats['by_governorate'] as $gov => $count) {
        printf("  %-30s: %4d facilities\n", $gov, $count);
    }
    echo "\n";

    echo "By Type:\n";
    foreach ($stats['by_type'] as $type => $count) {
        printf("  %-30s: %4d facilities\n", $type, $count);
    }
    echo str_repeat("=", 80) . "\n\n";

    // Display sample facilities from each governorate
    echo "=== Sample Facilities (5 from each Governorate) ===\n";
    echo str_repeat("=", 120) . "\n";

    $prevGov = '';
    $countPerGov = [];

    foreach ($facilities as $facility) {
        $gov = $facility['governorate'];

        if (!isset($countPerGov[$gov])) {
            $countPerGov[$gov] = 0;
        }

        if ($countPerGov[$gov] < 5) {
            if ($prevGov !== $gov) {
                echo "\n[{$gov}]\n";
                echo str_repeat("-", 120) . "\n";
                $prevGov = $gov;
            }

            printf("%-50s | Type: %-20s | Storage: %-15s\n",
                substr($facility['name'], 0, 50),
                $facility['type'],
                $facility['storage_level']
            );

            $countPerGov[$gov]++;
        }
    }
    echo str_repeat("=", 120) . "\n\n";

    // Save to JSON for later use
    $jsonFile = __DIR__ . '/facilities_data.json';
    file_put_contents($jsonFile, json_encode([
        'extracted_at' => date('Y-m-d H:i:s'),
        'total_count' => $stats['total'],
        'statistics' => $stats,
        'facilities' => $facilities,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "✅ Data saved to: $jsonFile\n";
    echo "   Total facilities extracted: {$stats['total']}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Extraction Complete ===\n";
