<?php

/**
 * Verify Seeded Data
 * VMS - Vaccination Management System
 */

echo "=== Verifying Gaza Data ===\n\n";

// Database configuration
$config = [
    'host' => '144.172.114.103',
    'port' => '1521',
    'service_name' => 'freepdb1',
    'username' => 'vms',
    'password' => 'vms',
];

// Connection string
$connection_string = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST={$config['host']})(PORT={$config['port']}))(CONNECT_DATA=(SERVICE_NAME={$config['service_name']})))";

try {
    $conn = oci_connect(
        $config['username'],
        $config['password'],
        $connection_string,
        'AL32UTF8'
    );

    if (!$conn) {
        $error = oci_error();
        throw new Exception($error['message']);
    }

    echo "✅ Connected to Oracle Database\n\n";

    // Check Governorates
    $query = "SELECT id, name_ar, name_en, code FROM governorates ORDER BY id";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    echo "المحافظات (Governorates):\n";
    echo str_repeat("=", 80) . "\n";
    printf("%-5s %-20s %-25s %-10s\n", "ID", "الاسم بالعربي", "Name (English)", "Code");
    echo str_repeat("-", 80) . "\n";

    $gov_count = 0;
    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        printf("%-5s %-20s %-25s %-10s\n",
            $row['ID'],
            $row['NAME_AR'],
            $row['NAME_EN'],
            $row['CODE']
        );
        $gov_count++;
    }
    echo str_repeat("=", 80) . "\n";
    echo "Total Governorates: $gov_count\n\n";

    // Check Districts
    $query = "
        SELECT
            d.id,
            d.name_ar,
            d.name_en,
            d.code,
            g.name_ar as gov_name_ar
        FROM districts d
        JOIN governorates g ON d.governorate_id = g.id
        ORDER BY g.id, d.id
    ";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    echo "المديريات (Districts):\n";
    echo str_repeat("=", 100) . "\n";
    printf("%-5s %-20s %-25s %-15s %-20s\n", "ID", "الاسم بالعربي", "Name (English)", "Code", "المحافظة");
    echo str_repeat("-", 100) . "\n";

    $dist_count = 0;
    $current_gov = '';
    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        if ($current_gov !== $row['GOV_NAME_AR']) {
            if ($current_gov !== '') {
                echo str_repeat("-", 100) . "\n";
            }
            $current_gov = $row['GOV_NAME_AR'];
        }

        printf("%-5s %-20s %-25s %-15s %-20s\n",
            $row['ID'],
            $row['NAME_AR'],
            $row['NAME_EN'],
            $row['CODE'],
            $row['GOV_NAME_AR']
        );
        $dist_count++;
    }
    echo str_repeat("=", 100) . "\n";
    echo "Total Districts: $dist_count\n\n";

    echo "✅ Data verification complete!\n";
    echo "\nSummary:\n";
    echo "  - $gov_count governorates in Gaza Strip\n";
    echo "  - $dist_count districts across all governorates\n";

    oci_close($conn);

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Verification Complete ===\n";
