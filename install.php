<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Create location tracking table
if (!$CI->db->table_exists(db_prefix() . 'staff_attendance_locations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'staff_attendance_locations` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `staff_id` INT(11) NOT NULL,
        `check_type` TINYINT(1) NOT NULL COMMENT "1=Check In, 2=Check Out",
        `check_datetime` DATETIME NOT NULL,
        `latitude` DECIMAL(10, 8) NULL DEFAULT NULL,
        `longitude` DECIMAL(11, 8) NULL DEFAULT NULL,
        `address` TEXT NULL DEFAULT NULL,
        `ip_address` VARCHAR(45) NULL DEFAULT NULL,
        `device_info` TEXT NULL DEFAULT NULL,
        `notes` TEXT NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        INDEX `idx_staff_id` (`staff_id`),
        INDEX `idx_check_datetime` (`check_datetime`),
        INDEX `idx_check_type` (`check_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    
    log_activity('Staff Attendance Tracker: Location table created');
}

// Check if foreign key to staff exists
$check_fk = $CI->db->query('
    SELECT COUNT(*) as count 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = "' . db_prefix() . 'staff_attendance_locations" 
    AND CONSTRAINT_NAME = "fk_staff_attendance_locations_staff"
')->row();

// Add foreign key constraint
if ($check_fk->count == 0) {
    try {
        $CI->db->query('
            ALTER TABLE `' . db_prefix() . 'staff_attendance_locations`
            ADD CONSTRAINT `fk_staff_attendance_locations_staff` 
            FOREIGN KEY (`staff_id`) 
            REFERENCES `' . db_prefix() . 'staff` (`staffid`) 
            ON DELETE CASCADE ON UPDATE CASCADE
        ');
        log_activity('Staff Attendance Tracker: Foreign key created');
    } catch (Exception $e) {
        log_activity('Staff Attendance Tracker: Foreign key failed - ' . $e->getMessage());
    }
}

log_activity('Staff Attendance Tracker module installed successfully');
