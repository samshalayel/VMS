<?php

/**
 * Check FACILITIES Table Structure
 * VMS - Vaccination Management System
 */

echo "=== FACILITIES Table Structure ===\n\n";

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

    // Get table structure
    $query = "
        SELECT
            column_name,
            data_type,
            data_length,
            data_precision,
            data_scale,
            nullable,
            data_default
        FROM user_tab_columns
        WHERE table_name = 'FACILITIES'
        ORDER BY column_id
    ";

    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    echo "FACILITIES Table Columns:\n";
    echo str_repeat("-", 100) . "\n";
    printf("%-25s %-15s %-10s %-10s %-8s %-10s\n",
        "Column Name", "Data Type", "Length", "Precision", "Nullable", "Default");
    echo str_repeat("-", 100) . "\n";

    $count = 0;
    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        printf("%-25s %-15s %-10s %-10s %-8s %-10s\n",
            $row['COLUMN_NAME'],
            $row['DATA_TYPE'],
            $row['DATA_LENGTH'] ?? '-',
            $row['DATA_PRECISION'] ? $row['DATA_PRECISION'] . ',' . $row['DATA_SCALE'] : '-',
            $row['NULLABLE'],
            trim($row['DATA_DEFAULT'] ?? '-')
        );
        $count++;
    }

    echo str_repeat("-", 100) . "\n";
    echo "Total columns: $count\n\n";

    // Get constraints
    $query = "
        SELECT
            constraint_name,
            constraint_type,
            CASE constraint_type
                WHEN 'P' THEN 'PRIMARY KEY'
                WHEN 'U' THEN 'UNIQUE'
                WHEN 'R' THEN 'FOREIGN KEY'
                WHEN 'C' THEN 'CHECK'
            END as type_desc
        FROM user_constraints
        WHERE table_name = 'FACILITIES'
        ORDER BY constraint_type
    ";

    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    echo "FACILITIES Table Constraints:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-50s %-30s\n", "Constraint Name", "Type");
    echo str_repeat("-", 80) . "\n";

    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        printf("%-50s %-30s\n",
            $row['CONSTRAINT_NAME'],
            $row['TYPE_DESC']
        );
    }
    echo str_repeat("-", 80) . "\n\n";

    // Get indexes
    $query = "
        SELECT
            index_name,
            uniqueness,
            column_name
        FROM user_ind_columns
        WHERE table_name = 'FACILITIES'
        ORDER BY index_name, column_position
    ";

    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    echo "FACILITIES Table Indexes:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-50s %-15s %-20s\n", "Index Name", "Uniqueness", "Column");
    echo str_repeat("-", 80) . "\n";

    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        printf("%-50s %-15s %-20s\n",
            $row['INDEX_NAME'],
            $row['UNIQUENESS'],
            $row['COLUMN_NAME']
        );
    }
    echo str_repeat("-", 80) . "\n\n";

    echo "✅ FACILITIES table structure verified!\n";

    oci_close($conn);

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Check Complete ===\n";
