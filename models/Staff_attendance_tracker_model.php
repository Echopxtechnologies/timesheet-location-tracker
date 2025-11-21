<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Staff_attendance_tracker_model extends App_Model
{
    private $table = 'staff_attendance_locations';

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . $this->table;
    }

    /**
     * Save location
     */
    public function save_location($data)
    {
        if (empty($data['staff_id']) || empty($data['check_type'])) {
            return false;
        }

        $insert_data = [
            'staff_id'       => $data['staff_id'],
            'check_type'     => $data['check_type'],
            'check_datetime' => $data['check_datetime'] ?? date('Y-m-d H:i:s'),
            'latitude'       => $data['latitude'] ?? null,
            'longitude'      => $data['longitude'] ?? null,
            'address'        => $data['address'] ?? null,
            'ip_address'     => $data['ip_address'] ?? null,
            'device_info'    => $data['device_info'] ?? null,
            'notes'          => $data['notes'] ?? null
        ];

        $this->db->insert($this->table, $insert_data);

        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            
            $staff_name = get_staff_full_name($data['staff_id']);
            $type_text = $data['check_type'] == 1 ? 'checked in' : 'checked out';
            $location = $data['address'] ?? 'GPS location';
            
            log_activity('Attendance: ' . $staff_name . ' ' . $type_text . ' from ' . $location);
            
            return $insert_id;
        }

        return false;
    }

    /**
     * Get LATEST check-in/check-out per staff per day
     */
    public function get_latest_locations($params = [])
    {
        $date_from = $params['date_from'] ?? date('Y-m-d');
        $date_to = $params['date_to'] ?? date('Y-m-d');
        $staff_id = $params['staff_id'] ?? null;

        // Get latest check-in and check-out for each staff for each day
        $sql = "
            SELECT 
                s.staffid as staff_id,
                CONCAT(s.firstname, ' ', s.lastname) as staff_name,
                DATE(sal.check_datetime) as date,
                
                -- Latest check-in
                (SELECT check_datetime FROM {$this->table} 
                 WHERE staff_id = s.staffid 
                 AND DATE(check_datetime) = DATE(sal.check_datetime)
                 AND check_type = 1 
                 ORDER BY check_datetime DESC LIMIT 1) as check_in_time,
                 
                (SELECT address FROM {$this->table} 
                 WHERE staff_id = s.staffid 
                 AND DATE(check_datetime) = DATE(sal.check_datetime)
                 AND check_type = 1 
                 ORDER BY check_datetime DESC LIMIT 1) as check_in_location,
                 
                (SELECT latitude FROM {$this->table} 
                 WHERE staff_id = s.staffid 
                 AND DATE(check_datetime) = DATE(sal.check_datetime)
                 AND check_type = 1 
                 ORDER BY check_datetime DESC LIMIT 1) as check_in_lat,
                 
                (SELECT longitude FROM {$this->table} 
                 WHERE staff_id = s.staffid 
                 AND DATE(check_datetime) = DATE(sal.check_datetime)
                 AND check_type = 1 
                 ORDER BY check_datetime DESC LIMIT 1) as check_in_lng,
                 
                (SELECT id FROM {$this->table} 
                 WHERE staff_id = s.staffid 
                 AND DATE(check_datetime) = DATE(sal.check_datetime)
                 AND check_type = 1 
                 ORDER BY check_datetime DESC LIMIT 1) as check_in_id,
                
                -- Latest check-out
                (SELECT check_datetime FROM {$this->table} 
                 WHERE staff_id = s.staffid 
                 AND DATE(check_datetime) = DATE(sal.check_datetime)
                 AND check_type = 2 
                 ORDER BY check_datetime DESC LIMIT 1) as check_out_time,
                 
                (SELECT address FROM {$this->table} 
                 WHERE staff_id = s.staffid 
                 AND DATE(check_datetime) = DATE(sal.check_datetime)
                 AND check_type = 2 
                 ORDER BY check_datetime DESC LIMIT 1) as check_out_location,
                 
                (SELECT latitude FROM {$this->table} 
                 WHERE staff_id = s.staffid 
                 AND DATE(check_datetime) = DATE(sal.check_datetime)
                 AND check_type = 2 
                 ORDER BY check_datetime DESC LIMIT 1) as check_out_lat,
                 
                (SELECT longitude FROM {$this->table} 
                 WHERE staff_id = s.staffid 
                 AND DATE(check_datetime) = DATE(sal.check_datetime)
                 AND check_type = 2 
                 ORDER BY check_datetime DESC LIMIT 1) as check_out_lng,
                 
                (SELECT id FROM {$this->table} 
                 WHERE staff_id = s.staffid 
                 AND DATE(check_datetime) = DATE(sal.check_datetime)
                 AND check_type = 2 
                 ORDER BY check_datetime DESC LIMIT 1) as check_out_id,
                 
                (SELECT ip_address FROM {$this->table} 
                 WHERE staff_id = s.staffid 
                 AND DATE(check_datetime) = DATE(sal.check_datetime)
                 ORDER BY check_datetime DESC LIMIT 1) as ip_address
                
            FROM " . db_prefix() . "staff s
            INNER JOIN {$this->table} sal ON sal.staff_id = s.staffid
            WHERE DATE(sal.check_datetime) >= '{$date_from}'
            AND DATE(sal.check_datetime) <= '{$date_to}'
        ";

        if ($staff_id) {
            $sql .= " AND s.staffid = {$staff_id}";
        }

        $sql .= " GROUP BY s.staffid, DATE(sal.check_datetime)
                  ORDER BY DATE(sal.check_datetime) DESC, s.staffid ASC";

        return $this->db->query($sql)->result_array();
    }

    /**
     * Get ALL locations (history)
     */
    public function get_locations($params = [])
    {
        $this->db->select('
            sal.*,
            CONCAT(s.firstname, " ", s.lastname) as staff_name
        ');
        $this->db->from($this->table . ' sal');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = sal.staff_id', 'left');

        if (!empty($params['staff_id'])) {
            $this->db->where('sal.staff_id', $params['staff_id']);
        }

        if (!empty($params['date_from'])) {
            $this->db->where('DATE(sal.check_datetime) >=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $this->db->where('DATE(sal.check_datetime) <=', $params['date_to']);
        }

        if (isset($params['check_type']) && $params['check_type'] !== '') {
            $this->db->where('sal.check_type', $params['check_type']);
        }

        $this->db->order_by('sal.check_datetime', 'DESC');

        if (!empty($params['limit'])) {
            $this->db->limit($params['limit']);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Delete location
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);

        return $this->db->affected_rows() > 0;
    }
}