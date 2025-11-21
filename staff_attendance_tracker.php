<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Staff Attendance Tracker
Description: Automatically capture GPS location when staff check-in/check-out buttons are clicked
Version: 1.0.0
Author: Emmanuel N
*/

define('STAFF_ATTENDANCE_TRACKER_MODULE_NAME', 'staff_attendance_tracker');

/**
 * Inject JavaScript - FIXED to prevent duplicate saves
 */
hooks()->add_action('app_admin_footer', 'staff_attendance_tracker_inject_js');

function staff_attendance_tracker_inject_js()
{
    $CI = &get_instance();
    
    if (strpos($CI->uri->uri_string(), 'timesheets/timekeeping') !== false || 
        $CI->uri->segment(2) == 'timekeeping') {
        
        // Get API key from settings
        $google_api_key = get_option('staff_attendance_tracker_google_api_key');
        if (empty($google_api_key)) {
            $google_api_key = 'AIzaSyCxItoqJ24V5SR-jyBc_M9snAQIMRPbaAM'; // Default fallback
        }
        ?>
        <script>
        (function($) {
            'use strict';
            
            var LocationCapture = {
                position: null,
                staffId: null,
                isSaving: false,
                originalSubmit: null,
                
                init: function() {
                    console.log('Staff Attendance Tracker: Initialized');
                    
                    // Pre-capture when modal opens
                    $('#clock_attendance_modal').on('shown.bs.modal', function() {
                        LocationCapture.captureLocation();
                    });
                    
                    // Intercept form submissions ONCE
                    $('#timesheets-form-check-in, #timesheets-form-check-out').each(function() {
                        var $form = $(this);
                        var originalAction = $form.attr('action');
                        
                        $form.on('submit', function(e) {
                            if (!LocationCapture.isSaving) {
                                e.preventDefault();
                                LocationCapture.handleFormSubmit($form);
                                return false;
                            }
                        });
                    });
                },
                
                handleFormSubmit: function($form) {
                    if (LocationCapture.isSaving) {
                        console.log('Already saving, skipping...');
                        return false;
                    }
                    
                    LocationCapture.isSaving = true;
                    LocationCapture.staffId = $form.find('input[name="staff_id"]').val();
                    var checkType = $form.find('input[name="type_check"]').val();
                    
                    console.log('Form submit intercepted for check type:', checkType);
                    
                    // Save location first
                    LocationCapture.saveLocation(checkType, function(success) {
                        // Now submit the original form
                        $form.off('submit');
                        $form[0].submit();
                    });
                },
                
                captureLocation: function(callback) {
                    if (!navigator.geolocation) {
                        console.warn('Geolocation not supported');
                        if (callback) callback(null);
                        return;
                    }
                    
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            LocationCapture.position = position;
                            console.log('Location captured:', position.coords);
                            if (callback) callback(position);
                        },
                        function(error) {
                            console.warn('Location error:', error.message);
                            if (callback) callback(null);
                        },
                        { enableHighAccuracy: true, timeout: 5000, maximumAge: 30000 }
                    );
                },
                
                saveLocation: function(checkType, callback) {
                    if (!LocationCapture.position) {
                        console.warn('No location available, submitting anyway...');
                        if (callback) callback(false);
                        return;
                    }
                    
                    var lat = LocationCapture.position.coords.latitude;
                    var lng = LocationCapture.position.coords.longitude;
                    
                    console.log('Saving location to server...');
                    
                    // Get address via Google Maps
                    $.ajax({
                        url: 'https://maps.googleapis.com/maps/api/geocode/json',
                        data: {
                            latlng: lat + ',' + lng,
                            key: '<?php echo $google_api_key; ?>'
                        },
                        success: function(geocodeData) {
                            var address = 'Unknown location';
                            if (geocodeData.status === 'OK' && geocodeData.results[0]) {
                                address = geocodeData.results[0].formatted_address;
                            }
                            
                            // Save to our database
                            $.ajax({
                                url: admin_url + 'staff_attendance_tracker/save_location',
                                type: 'POST',
                                data: {
                                    staff_id: LocationCapture.staffId,
                                    check_type: checkType,
                                    latitude: lat,
                                    longitude: lng,
                                    address: address,
                                    accuracy: LocationCapture.position.coords.accuracy
                                },
                                success: function(response) {
                                    console.log('Location saved:', response);
                                    if (callback) callback(true);
                                },
                                error: function() {
                                    console.error('Failed to save location');
                                    if (callback) callback(false);
                                }
                            });
                        },
                        error: function() {
                            console.warn('Geocoding failed, saving without address');
                            // Save without address
                            $.ajax({
                                url: admin_url + 'staff_attendance_tracker/save_location',
                                type: 'POST',
                                data: {
                                    staff_id: LocationCapture.staffId,
                                    check_type: checkType,
                                    latitude: lat,
                                    longitude: lng,
                                    address: 'Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6),
                                    accuracy: LocationCapture.position.coords.accuracy
                                },
                                success: function(response) {
                                    console.log('Location saved:', response);
                                    if (callback) callback(true);
                                },
                                error: function() {
                                    console.error('Failed to save location');
                                    if (callback) callback(false);
                                }
                            });
                        }
                    });
                }
            };
            
            $(document).ready(function() {
                LocationCapture.init();
            });
            
        })(jQuery);
        </script>
        <?php
    }
}
/**
 * Module activation
 */
register_activation_hook(STAFF_ATTENDANCE_TRACKER_MODULE_NAME, 'staff_attendance_tracker_activate');

function staff_attendance_tracker_activate()
{
    require_once(__DIR__ . '/install.php');
}

/**
 * Register menu items
 */
hooks()->add_action('admin_init', 'staff_attendance_tracker_init_menu_items');

function staff_attendance_tracker_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('staff', '', 'view') || is_admin()) {
        
        // Main menu with submenu
        $CI->app_menu->add_sidebar_menu_item('staff_attendance_tracker_main', [
            'collapse' => true,
            'name'     => 'Attendance Locations',
            'position' => 35,
            'icon'     => 'fa fa-map-marker',
        ]);

        // Submenu - Latest Status
        $CI->app_menu->add_sidebar_children_item('staff_attendance_tracker_main', [
            'slug'     => 'attendance_tracker_latest',
            'name'     => 'Latest Status',
            'href'     => admin_url('staff_attendance_tracker'),
            'icon'     => 'fa fa-dashboard',
        ]);

        // Submenu - Full History
        $CI->app_menu->add_sidebar_children_item('staff_attendance_tracker_main', [
            'slug'     => 'attendance_tracker_history',
            'name'     => 'Full History',
            'href'     => admin_url('staff_attendance_tracker/history'),
            'icon'     => 'fa fa-history',
        ]);
        // Submenu - Settings (Admin only)
        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('staff_attendance_tracker_main', [
                'slug'     => 'attendance_tracker_settings',
                'name'     => 'Settings',
                'href'     => admin_url('staff_attendance_tracker/settings'),
                'icon'     => 'fa fa-cog',
            ]);
    }
}
}
