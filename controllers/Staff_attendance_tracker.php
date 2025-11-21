<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Staff_attendance_tracker extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_attendance_tracker_model');
        $this->load->model('staff_model');
    }

    /**
     * Main view - LATEST check-in/check-out per staff
     */
    public function index()
    {
        if (!has_permission('staff', '', 'view') && !is_admin()) {
            access_denied('staff_attendance_tracker');
        }

        $date_from = $this->input->get('date_from') ?: date('Y-m-d', strtotime('-7 days'));
        $date_to = $this->input->get('date_to') ?: date('Y-m-d');
        $staff_id = $this->input->get('staff_id') ?: null;

        // Get LATEST check-in/check-out per staff per day
        $data['locations'] = $this->staff_attendance_tracker_model->get_latest_locations([
            'date_from' => $date_from,
            'date_to' => $date_to,
            'staff_id' => $staff_id
        ]);

        $data['title'] = 'Latest Attendance Status';
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['google_api_key'] = 'AIzaSyCxItoqJ24V5SR-jyBc_M9snAQIMRPbaAM';
        
        $this->load->view('location_latest', $data);
    }

    /**
     * History view - ALL check-in/check-out records
     */
    public function history()
    {
        if (!has_permission('staff', '', 'view') && !is_admin()) {
            access_denied('staff_attendance_tracker_history');
        }

        $date_from = $this->input->get('date_from') ?: date('Y-m-d', strtotime('-7 days'));
        $date_to = $this->input->get('date_to') ?: date('Y-m-d');
        $staff_id = $this->input->get('staff_id') ?: null;

        // Get ALL records
        $data['locations'] = $this->staff_attendance_tracker_model->get_locations([
            'date_from' => $date_from,
            'date_to' => $date_to,
            'staff_id' => $staff_id
        ]);

        $data['title'] = 'Attendance Location History';
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['google_api_key'] = 'AIzaSyCxItoqJ24V5SR-jyBc_M9snAQIMRPbaAM';
        
        $this->load->view('location_history', $data);
    }

    /**
     * AJAX: Save location
     */
    public function save_location()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $staff_id = $this->input->post('staff_id');
        $check_type = $this->input->post('check_type');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        $address = $this->input->post('address');
        $accuracy = $this->input->post('accuracy');

        if (empty($staff_id) || empty($check_type)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing data']);
            return;
        }

        if (!is_admin() && $staff_id != get_staff_user_id()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $location_data = [
            'staff_id'       => $staff_id,
            'check_type'     => $check_type,
            'check_datetime' => date('Y-m-d H:i:s'),
            'latitude'       => $latitude,
            'longitude'      => $longitude,
            'address'        => $address,
            'ip_address'     => $this->input->ip_address(),
            'device_info'    => $this->input->user_agent(),
            'notes'          => 'Accuracy: ' . round($accuracy) . 'm'
        ];

        $result = $this->staff_attendance_tracker_model->save_location($location_data);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $result ? true : false,
            'message' => $result ? 'Location saved' : 'Failed'
        ]);
    }

    /**
     * View details modal
     */
    public function view_details($id)
    {
        if (!has_permission('staff', '', 'view') && !is_admin()) {
            access_denied();
        }

        $this->db->select('sal.*, CONCAT(s.firstname, " ", s.lastname) as staff_name');
        $this->db->from(db_prefix() . 'staff_attendance_locations sal');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = sal.staff_id', 'left');
        $this->db->where('sal.id', $id);
        $location = $this->db->get()->row_array();

        if (!$location) {
            echo '<div class="alert alert-danger">Location not found</div>';
            return;
        }

        $type_badge = $location['check_type'] == 1 ? 'success' : 'warning';
        $type_text = $location['check_type'] == 1 ? 'Check In' : 'Check Out';
        ?>
        
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Staff</th>
                        <td><?php echo $location['staff_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td><span class="label label-<?php echo $type_badge; ?>"><?php echo $type_text; ?></span></td>
                    </tr>
                    <tr>
                        <th>Date & Time</th>
                        <td><?php echo _dt($location['check_datetime']); ?></td>
                    </tr>
                    <tr>
                        <th>IP Address</th>
                        <td><?php echo $location['ip_address']; ?></td>
                    </tr>
                    <tr>
                        <th>Device</th>
                        <td><small><?php echo $location['device_info']; ?></small></td>
                    </tr>
                    <?php if ($location['notes']): ?>
                    <tr>
                        <th>Notes</th>
                        <td><?php echo $location['notes']; ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <div class="col-md-6">
                <?php if ($location['latitude'] && $location['longitude']): ?>
                    <div class="form-group">
                        <label>Location</label>
                        <div id="map_<?php echo $id; ?>" style="height: 300px; border: 1px solid #ddd;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Address</label>
                        <p><?php echo $location['address']; ?></p>
                    </div>
                    
                    <div class="form-group">
                        <label>Coordinates</label>
                        <p>
                            <strong>Lat:</strong> <?php echo $location['latitude']; ?><br>
                            <strong>Lng:</strong> <?php echo $location['longitude']; ?>
                        </p>
                    </div>
                    
                    <a href="https://www.google.com/maps?q=<?php echo $location['latitude']; ?>,<?php echo $location['longitude']; ?>" 
                       target="_blank" class="btn btn-info btn-block">
                        <i class="fa fa-external-link"></i> Open in Google Maps
                    </a>
                    
                    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxItoqJ24V5SR-jyBc_M9snAQIMRPbaAM"></script>
                    <script>
                    var map = new google.maps.Map(document.getElementById('map_<?php echo $id; ?>'), {
                        center: {lat: <?php echo $location['latitude']; ?>, lng: <?php echo $location['longitude']; ?>},
                        zoom: 15
                    });
                    
                    var marker = new google.maps.Marker({
                        position: {lat: <?php echo $location['latitude']; ?>, lng: <?php echo $location['longitude']; ?>},
                        map: map,
                        title: '<?php echo $type_text; ?>'
                    });
                    </script>
                <?php else: ?>
                    <div class="alert alert-warning">
                        No GPS location available
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
    }

    /**
     * Export
     */
    public function export()
    {
        if (!has_permission('staff', '', 'view') && !is_admin()) {
            access_denied();
        }

        $date_from = $this->input->get('date_from') ?: date('Y-m-d', strtotime('-30 days'));
        $date_to = $this->input->get('date_to') ?: date('Y-m-d');
        $staff_id = $this->input->get('staff_id') ?: null;

        $locations = $this->staff_attendance_tracker_model->get_locations([
            'date_from' => $date_from,
            'date_to' => $date_to,
            'staff_id' => $staff_id
        ]);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="attendance_locations_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Staff', 'Type', 'Date & Time', 'Latitude', 'Longitude', 'Address', 'IP Address']);

        foreach ($locations as $location) {
            fputcsv($output, [
                $location['staff_name'],
                $location['check_type'] == 1 ? 'Check In' : 'Check Out',
                $location['check_datetime'],
                $location['latitude'],
                $location['longitude'],
                $location['address'],
                $location['ip_address']
            ]);
        }

        fclose($output);
    }
}