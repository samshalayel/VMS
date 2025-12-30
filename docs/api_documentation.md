# VMS API Documentation

## Base URL
```
http://localhost/vms-laravel/public/api/v1
```

## Users API Endpoints

### 1. List All Users
```
GET /users
```
**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "username": "ahmad.hassan",
      "email": "ahmad@vms.ps",
      "first_name": "Ahmad",
      "last_name": "Hassan",
      "full_name_ar": "أحمد حسن",
      "phone": "+970599123456",
      "organization": "PRCS",
      "job_title": "Nurse",
      "is_active": true,
      "roles": [...],
      "facilities": [...]
    }
  ]
}
```

### 2. Create New User
```
POST /users
```
**Request Body:**
```json
{
  "username": "ahmad.hassan",
  "email": "ahmad@vms.ps",
  "password": "SecurePass123!",
  "first_name": "Ahmad",
  "last_name": "Hassan",
  "full_name_ar": "أحمد حسن",
  "phone": "+970599123456",
  "organization": "PRCS",
  "job_title": "Nurse",
  "employee_id": "PRCS-2025-001"
}
```

### 3. Get User Details
```
GET /users/{id}
```

### 4. Update User
```
PUT /users/{id}
```

### 5. Delete User (Soft Delete)
```
DELETE /users/{id}
```

### 6. Assign Role to User
```
POST /users/{id}/assign-role
```
**Request Body:**
```json
{
  "role_id": 3,
  "assigned_by": 1
}
```

### 7. Remove Role from User
```
DELETE /users/{id}/remove-role/{role_id}
```

### 8. Assign Facility to User
```
POST /users/{id}/assign-facility
```
**Request Body:**
```json
{
  "facility_id": 5,
  "is_primary": true,
  "assigned_by": 1
}
```

### 9. Remove Facility from User
```
DELETE /users/{id}/remove-facility/{facility_id}
```

### 10. Activate User
```
PATCH /users/{id}/activate
```

### 11. Deactivate User
```
PATCH /users/{id}/deactivate
```
**Request Body:**
```json
{
  "deactivation_reason": "Left organization"
}
```

---

## Database Schema

### USERS Table
- Core authentication & personal info
- Employment details (employee_id, job_title, organization)
- Account status tracking
- Soft deletes enabled

### USER_ROLES Table (Pivot)
- Links users to roles
- Supports temporary assignments (expires_at)
- Tracks who assigned the role

### USER_FACILITIES Table (Pivot)
- Links users to facilities
- Primary facility designation
- Access control per facility

---

## Implementation Status
✅ Models created with relationships
✅ API routes defined
✅ CRUD endpoints structured
✅ Role & Facility assignment endpoints
✅ Activate/Deactivate functionality

Generated: 2025-12-29
VMS - Vaccination Management System
