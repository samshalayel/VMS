# VMS - User Roles & Permissions Definition

## System Roles

### 1. Admin (مدير النظام)
**Description:** Full system access with all administrative capabilities
**Responsibilities:**
- Complete system configuration
- User management (create, edit, delete users)
- Facility management (create, edit, delete facilities)
- Role and permission management
- System-wide reports and analytics
- Audit trail access

**Permissions:**
- users.view, users.create, users.edit, users.delete
- facilities.view, facilities.create, facilities.edit, facilities.delete
- roles.view, roles.create, roles.edit, roles.delete
- governorates.view, governorates.create, governorates.edit
- districts.view, districts.create, districts.edit
- reports.view_all, reports.export
- audit.view_all
- settings.manage

---

### 2. Supervisor (مشرف)
**Description:** Regional or program supervisor with oversight capabilities
**Responsibilities:**
- Monitor facilities within assigned region/program
- View vaccination campaigns and activities
- Generate reports for assigned facilities
- Manage health workers within scope
- View inventory and supplies

**Permissions:**
- users.view, users.edit (limited to assigned facilities)
- facilities.view, facilities.edit (limited to assigned facilities)
- campaigns.view, campaigns.edit
- vaccinations.view, vaccinations.approve
- inventory.view
- reports.view_assigned, reports.export_assigned
- audit.view_assigned

---

### 3. Health Worker (عامل صحي)
**Description:** Frontline health worker performing vaccinations
**Responsibilities:**
- Register children/beneficiaries
- Record vaccinations
- Update vaccination status
- Check vaccine availability
- Print vaccination cards
- View facility information

**Permissions:**
- beneficiaries.view, beneficiaries.create, beneficiaries.edit
- vaccinations.view, vaccinations.create, vaccinations.edit
- vaccination_cards.view, vaccination_cards.print
- inventory.view (read-only for assigned facility)
- facilities.view (read-only for assigned facility)
- reports.view_own

---

### 4. Storekeeper (أمين مخزن)
**Description:** Manages vaccine inventory and cold chain
**Responsibilities:**
- Manage vaccine stock
- Track cold storage temperature
- Record stock in/out movements
- Generate inventory reports
- Alert on low stock levels
- Manage vaccine expiry dates

**Permissions:**
- inventory.view, inventory.create, inventory.edit
- stock_movements.view, stock_movements.create
- cold_chain.view, cold_chain.record
- vaccines.view, vaccines.manage_stock
- facilities.view (storage info for assigned facility)
- reports.view_inventory, reports.export_inventory
- alerts.view_stock

---

### 5. Viewer (مشاهد)
**Description:** Read-only access for monitoring and reporting
**Responsibilities:**
- View dashboards and statistics
- View facility information
- View vaccination coverage
- Export basic reports
- Monitor campaign progress

**Permissions:**
- facilities.view (limited to assigned)
- campaigns.view
- vaccinations.view (aggregated data only)
- inventory.view (summary only)
- reports.view_public, reports.export_public
- dashboard.view

---

## Permission Categories

### User Management (users.*)
- `users.view` - View user list and details
- `users.create` - Create new users
- `users.edit` - Edit existing users
- `users.delete` - Delete users
- `users.assign_roles` - Assign roles to users
- `users.assign_facilities` - Link users to facilities

### Facility Management (facilities.*)
- `facilities.view` - View facilities
- `facilities.create` - Create new facilities
- `facilities.edit` - Edit facility details
- `facilities.delete` - Deactivate facilities
- `facilities.manage_gps` - Edit GPS coordinates

### Beneficiary Management (beneficiaries.*)
- `beneficiaries.view` - View beneficiary records
- `beneficiaries.create` - Register new beneficiaries
- `beneficiaries.edit` - Update beneficiary information
- `beneficiaries.delete` - Remove beneficiary records

### Vaccination Operations (vaccinations.*)
- `vaccinations.view` - View vaccination records
- `vaccinations.create` - Record new vaccinations
- `vaccinations.edit` - Edit vaccination records
- `vaccinations.delete` - Delete vaccination records
- `vaccinations.approve` - Approve vaccination reports

### Inventory Management (inventory.*)
- `inventory.view` - View stock levels
- `inventory.create` - Add new stock
- `inventory.edit` - Adjust stock levels
- `inventory.manage_stock` - Full stock management

### Reports & Analytics (reports.*)
- `reports.view_all` - View all reports
- `reports.view_assigned` - View reports for assigned facilities
- `reports.view_own` - View own activity reports
- `reports.view_public` - View public dashboards
- `reports.export` - Export reports

### System Administration (settings.*)
- `settings.manage` - Manage system settings
- `roles.view` - View roles
- `roles.create` - Create new roles
- `roles.edit` - Edit roles
- `roles.delete` - Delete roles
- `audit.view_all` - View all audit logs
- `audit.view_assigned` - View assigned audit logs

---

## Role-Permission Matrix

| Permission Category | Admin | Supervisor | Health Worker | Storekeeper | Viewer |
|---------------------|-------|------------|---------------|-------------|--------|
| users.* (all)       | ✓     | ✗          | ✗             | ✗           | ✗      |
| facilities.* (all)  | ✓     | ✗          | ✗             | ✗           | ✗      |
| facilities.view     | ✓     | ✓          | ✓             | ✓           | ✓      |
| facilities.edit     | ✓     | ✓ (limited)| ✗             | ✗           | ✗      |
| beneficiaries.*     | ✓     | ✗          | ✓             | ✗           | ✗      |
| beneficiaries.view  | ✓     | ✓          | ✓             | ✗           | ✓      |
| vaccinations.*      | ✓     | ✗          | ✓             | ✗           | ✗      |
| vaccinations.view   | ✓     | ✓          | ✓             | ✗           | ✓      |
| inventory.*         | ✓     | ✗          | ✗             | ✓           | ✗      |
| inventory.view      | ✓     | ✓          | ✓ (limited)   | ✓           | ✓      |
| reports.view_all    | ✓     | ✗          | ✗             | ✗           | ✗      |
| reports.view_assigned| ✓    | ✓          | ✗             | ✗           | ✗      |
| reports.export      | ✓     | ✓          | ✗             | ✓           | ✓      |
| settings.manage     | ✓     | ✗          | ✗             | ✗           | ✗      |
| audit.view_all      | ✓     | ✗          | ✗             | ✗           | ✗      |

---

## Implementation Notes

1. **Hierarchical Roles**: Admin > Supervisor > Health Worker / Storekeeper > Viewer
2. **Facility-Based Access**: Users can be assigned to specific facilities to limit their scope
3. **Dynamic Permissions**: Permissions can be adjusted per role as needed
4. **Multi-Role Support**: Users can have multiple roles if needed (e.g., Supervisor + Health Worker)
5. **Default Role**: New users default to "Viewer" role until assigned proper permissions

---

## Database Schema

### Tables Required:
1. **roles** - System roles definition
2. **permissions** - Available permissions
3. **role_permissions** - Role-to-permission mapping
4. **user_roles** - User-to-role assignment
5. **user_facilities** - User-to-facility access control

---

Generated: 2025-12-29
VMS - Vaccination Management System
Gaza Strip, Palestine
