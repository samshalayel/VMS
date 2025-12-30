<?php

/**
 * Oracle Database Connection Test
 * VMS - Vaccination Management System
 */

echo "=== Oracle Database Connection Test ===\n\n";

// Check if OCI8 extension is loaded
if (!extension_loaded('oci8')) {
    echo "❌ Error: OCI8 extension is not loaded!\n";
    echo "Please install PHP OCI8 extension for Oracle connectivity.\n\n";
    echo "Installation steps:\n";
    echo "1. Download Oracle Instant Client\n";
    echo "2. Enable extension=oci8 in php.ini\n";
    echo "3. Restart Apache\n";
    exit(1);
}

echo "✅ OCI8 extension is loaded\n\n";

// Database configuration
$config = [
    'host' => '144.172.114.103',
    'port' => '1521',
    'service_name' => 'freepdb1',
    'username' => 'vms',
    'password' => 'vms',
];

echo "Attempting to connect to Oracle...\n";
echo "Host: {$config['host']}:{$config['port']}\n";
echo "Service: {$config['service_name']}\n";
echo "Username: {$config['username']}\n\n";

// Connection string
$connection_string = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST={$config['host']})(PORT={$config['port']}))(CONNECT_DATA=(SERVICE_NAME={$config['service_name']})))";

try {
    // Attempt connection
    $conn = oci_connect(
        $config['username'],
        $config['password'],
        $connection_string,
        'AL32UTF8'  // Character set for Arabic support
    );

    if (!$conn) {
        $error = oci_error();
        throw new Exception($error['message']);
    }

    echo "✅ Successfully connected to Oracle Database!\n\n";

    // Test query
    $query = "SELECT * FROM v\$version WHERE rownum = 1";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    echo "Oracle Version:\n";
    while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
        foreach ($row as $item) {
            echo "  " . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "\n";
        }
    }
    echo "\n";

    // Get current user and database info
    $query = "SELECT USER, SYS_CONTEXT('USERENV', 'DB_NAME') as DB_NAME FROM DUAL";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_array($stid, OCI_ASSOC);

    echo "Connection Details:\n";
    echo "  Current User: " . $row['USER'] . "\n";
    echo "  Database Name: " . $row['DB_NAME'] . "\n\n";

    // List existing tables
    $query = "SELECT table_name FROM user_tables ORDER BY table_name";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    echo "Existing Tables:\n";
    $count = 0;
    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        echo "  - " . $row['TABLE_NAME'] . "\n";
        $count++;
    }

    if ($count === 0) {
        echo "  (No tables found - clean database)\n";
    }
    echo "\n";

    echo "✅ Database is ready for VMS setup!\n";

    oci_close($conn);

} catch (Exception $e) {
    echo "❌ Connection Error: " . $e->getMessage() . "\n\n";
    echo "Common issues:\n";
    echo "1. Oracle service not running\n";
    echo "2. Incorrect credentials\n";
    echo "3. Wrong host/port/service name\n";
    echo "4. Firewall blocking connection\n";
    echo "5. TNS listener not configured\n";
    exit(1);
}

echo "\n=== Test Complete ===\n";
