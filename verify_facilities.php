<?php

/**
 * Verify Facilities Data in Oracle DB
 * VMS - Vaccination Management System
 */

echo "=== Verifying Facilities Data ===\n\n";

// Database configuration
$config = [
    'host' => '144.172.114.103',
    'port' => '1521',
    'service_name' => 'freepdb1',
    'username' => 'vms',
    'password' => 'vms',
];

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

    // Count total facilities
    $query = "SELECT COUNT(*) as total FROM facilities";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_array($stid, OCI_ASSOC);
    echo "Total Facilities: {$row['TOTAL']}\n\n";

    // Count by governorate
    $query = "
        SELECT
            g.name_en,
            g.name_ar,
            COUNT(f.id) as count
        FROM governorates g
        LEFT JOIN facilities f ON g.id = f.governorate_id
        GROUP BY g.id, g.name_en, g.name_ar
        ORDER BY g.id
    ";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    echo "By Governorate:\n";
    echo str_repeat("=", 80) . "\n";
    printf("%-25s %-25s %10s\n", "English Name", "Arabic Name", "Count");
    echo str_repeat("-", 80) . "\n";

    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        printf("%-25s %-25s %10d\n",
            $row['NAME_EN'],
            $row['NAME_AR'],
            $row['COUNT']
        );
    }
    echo str_repeat("=", 80) . "\n\n";

    // Count by facility type
    $query = "
        SELECT
            facility_type,
            COUNT(*) as count
        FROM facilities
        GROUP BY facility_type
        ORDER BY count DESC
    ";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    echo "By Facility Type:\n";
    echo str_repeat("=", 60) . "\n";
    printf("%-30s %10s\n", "Type", "Count");
    echo str_repeat("-", 60) . "\n";

    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        printf("%-30s %10d\n",
            $row['FACILITY_TYPE'],
            $row['COUNT']
        );
    }
    echo str_repeat("=", 60) . "\n\n";

    // Count by managing organization
    $query = "
        SELECT
            managing_organization,
            COUNT(*) as count
        FROM facilities
        GROUP BY managing_organization
        ORDER BY count DESC
    ";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    echo "By Managing Organization:\n";
    echo str_repeat("=", 60) . "\n";
    printf("%-30s %10s\n", "Organization", "Count");
    echo str_repeat("-", 60) . "\n";

    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        printf("%-30s %10d\n",
            $row['MANAGING_ORGANIZATION'],
            $row['COUNT']
        );
    }
    echo str_repeat("=", 60) . "\n\n";

    // Count facilities with cold storage
    $query = "
        SELECT
            COUNT(*) as total,
            SUM(CASE WHEN has_cold_storage = '1' THEN 1 ELSE 0 END) as with_storage
        FROM facilities
    ";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_array($stid, OCI_ASSOC);

    echo "Cold Storage:\n";
    echo str_repeat("=", 60) . "\n";
    printf("Facilities with cold storage: %d / %d (%.1f%%)\n",
        $row['WITH_STORAGE'],
        $row['TOTAL'],
        ($row['WITH_STORAGE'] / $row['TOTAL']) * 100
    );
    echo str_repeat("=", 60) . "\n\n";

    // Sample facilities from each governorate
    echo "Sample Facilities (5 from each governorate):\n";
    echo str_repeat("=", 120) . "\n";

    $query = "
        SELECT
            f.facility_code,
            f.facility_name_en,
            f.facility_type,
            f.managing_organization,
            f.has_cold_storage,
            g.name_en as governorate
        FROM facilities f
        JOIN governorates g ON f.governorate_id = g.id
        WHERE f.governorate_id = (SELECT id FROM governorates WHERE code = ?)
        AND ROWNUM <= 5
        ORDER BY f.id
    ";

    foreach (['NGZ', 'GZ', 'DB', 'KY', 'RF'] as $code) {
        $stid = oci_parse($conn, $query);
        oci_bind_by_name($stid, ':code', $code);
        oci_execute($stid);

        $first = true;
        while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
            if ($first) {
                echo "\n[{$row['GOVERNORATE']}]\n";
                echo str_repeat("-", 120) . "\n";
                $first = false;
            }

            printf("%-12s | %-50s | %-15s | %-15s | Storage: %s\n",
                $row['FACILITY_CODE'],
                substr($row['FACILITY_NAME_EN'], 0, 50),
                $row['FACILITY_TYPE'],
                $row['MANAGING_ORGANIZATION'],
                $row['HAS_COLD_STORAGE'] == '1' ? 'Yes' : 'No'
            );
        }
    }

    echo str_repeat("=", 120) . "\n";

    echo "\n✅ Verification complete!\n";

    oci_close($conn);

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Done ===\n";
