<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Optional: Uncomment to drop table on uninstall
// if ($CI->db->table_exists(db_prefix() . 'staff_attendance_locations')) {
//     $CI->db->query('DROP TABLE `' . db_prefix() . 'staff_attendance_locations`');
// }

// Remove settings
delete_option('staff_attendance_tracker_google_api_key');
delete_option('staff_attendance_tracker_require_location');
delete_option('staff_attendance_tracker_location_radius');

log_activity('Staff Attendance Tracker Module Uninstalled');