-- Create facilities table
CREATE TABLE facilities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    facility_name VARCHAR(255) NOT NULL,
    governorate VARCHAR(100) NOT NULL,
    facility_type VARCHAR(100),
    storage_level VARCHAR(100),
    distribution_site VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create user-facility mapping table
CREATE TABLE user_facility_mapping (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    facility_id INT NOT NULL,
    access_level ENUM('read', 'write', 'admin') DEFAULT 'read',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    assigned_by INT,
    status ENUM('active', 'suspended') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_facility (user_id, facility_id)
);

-- Create facility_permissions table for detailed access control
CREATE TABLE facility_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mapping_id INT NOT NULL,
    permission_name VARCHAR(100) NOT NULL,
    permission_value BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (mapping_id) REFERENCES user_facility_mapping(id) ON DELETE CASCADE,
    UNIQUE KEY unique_mapping_permission (mapping_id, permission_name)
);

-- Add indexes for performance
CREATE INDEX idx_user_facility_user ON user_facility_mapping(user_id);
CREATE INDEX idx_user_facility_facility ON user_facility_mapping(facility_id);
CREATE INDEX idx_user_facility_status ON user_facility_mapping(status);
CREATE INDEX idx_facility_governorate ON facilities(governorate);
CREATE INDEX idx_facility_type ON facilities(facility_type);