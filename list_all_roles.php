<?php

require __DIR__ . '/vendor/autoload.php';

// Database config
$dotenv = parse_ini_file(__DIR__ . '/.env');
$conn = oci_connect('vms', 'vms', "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=144.172.114.103)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=freepdb1)))", 'AL32UTF8');

echo "=== All Roles in VMS ===\n\n";

$query = 'SELECT name, name_ar, name_en, "LEVEL" FROM roles ORDER BY "LEVEL" DESC';
$stid = oci_parse($conn, $query);
oci_execute($stid);

printf("%-20s | %-25s | %-30s | %s\n", "Name", "Arabic", "English", "Level");
echo str_repeat("=", 100) . "\n";

while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
    printf("%-20s | %-25s | %-30s | %d\n",
        $row['NAME'],
        $row['NAME_AR'],
        $row['NAME_EN'],
        $row['LEVEL']
    );
}

oci_close($conn);
echo "\n";
