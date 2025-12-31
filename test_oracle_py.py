#!/usr/bin/env python3
"""
Oracle Database Connection Test - Python
VMS - Vaccination Management System
"""

import oracledb

print("=== Oracle Database Connection Test (Python) ===\n")

# Database configuration
config = {
    'host': '144.172.114.103',
    'port': 1521,
    'service_name': 'freepdb1',
    'user': 'vms',
    'password': 'vms'
}

print(f"Connecting to Oracle...")
print(f"Host: {config['host']}:{config['port']}")
print(f"Service: {config['service_name']}")
print(f"Username: {config['user']}\n")

try:
    # Connect using thin mode (no Oracle client needed)
    connection = oracledb.connect(
        user=config['user'],
        password=config['password'],
        host=config['host'],
        port=config['port'],
        service_name=config['service_name']
    )

    print("✅ Successfully connected to Oracle Database!\n")

    # Test query - Get Oracle version
    cursor = connection.cursor()
    cursor.execute("SELECT * FROM v$version WHERE rownum = 1")
    row = cursor.fetchone()
    print(f"Oracle Version:\n  {row[0]}\n")

    # Get current user and database info
    cursor.execute("SELECT USER, SYS_CONTEXT('USERENV', 'DB_NAME') as DB_NAME FROM DUAL")
    row = cursor.fetchone()
    print(f"Connection Details:")
    print(f"  Current User: {row[0]}")
    print(f"  Database Name: {row[1]}\n")

    # List existing tables
    cursor.execute("SELECT table_name FROM user_tables ORDER BY table_name")
    rows = cursor.fetchall()

    print("Existing Tables:")
    if rows:
        for row in rows:
            print(f"  - {row[0]}")
    else:
        print("  (No tables found - clean database)")
    print()

    print("✅ Database is ready for VMS setup!")

    cursor.close()
    connection.close()

except Exception as e:
    print(f"❌ Connection Error: {str(e)}\n")
    print("Common issues:")
    print("1. Oracle service not running")
    print("2. Incorrect credentials")
    print("3. Wrong host/port/service name")
    print("4. Firewall blocking connection")
    exit(1)

print("\n=== Test Complete ===")
