<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                            <i class="fa fa-map-marker"></i>
                            Staff Attendance Locations
                        </h4>
                        
                        <hr class="hr-panel-heading">

                        <!-- Filters -->
                        <div class="row">
                            <div class="col-md-3">
                                <?php echo render_date_input('date_from', 'Date From', date('Y-m-d', strtotime('-7 days'))); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_date_input('date_to', 'Date To', date('Y-m-d')); ?>
                            </div>
                            <div class="col-md-3">
                                <?php 
                                echo render_select('staff_filter', $staff_members, ['staffid', ['firstname', 'lastname']], 'Staff', '', ['data-none-selected-text' => 'All Staff']); 
                                ?>
                            </div>
                            <div class="col-md-3 mtop25">
                                <button type="button" id="filter_btn" class="btn btn-info">
                                    <i class="fa fa-filter"></i> Filter
                                </button>
                                <button type="button" id="reset_btn" class="btn btn-default">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <hr>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped dt-table" id="location_table">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Date</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Check In Location</th>
                                        <th>Check Out Location</th>
                                        <th>IP Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($locations as $location): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo admin_url('staff/profile/' . $location['staff_id']); ?>">
                                                <?php echo $location['staff_name']; ?>
                                            </a>
                                        </td>
                                        <td><?php echo _d($location['date']); ?></td>
                                        <td>
                                            <?php if ($location['check_in_time']): ?>
                                                <span class="label label-success">
                                                    <i class="fa fa-clock-o"></i> <?php echo $location['check_in_time']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($location['check_out_time']): ?>
                                                <span class="label label-warning">
                                                    <i class="fa fa-clock-o"></i> <?php echo $location['check_out_time']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($location['check_in_location']): ?>
                                                <i class="fa fa-map-marker text-success"></i>
                                                <small><?php echo substr($location['check_in_location'], 0, 40) . '...'; ?></small>
                                                <?php if ($location['check_in_lat']): ?>
                                                    <br>
                                                    <a href="https://www.google.com/maps?q=<?php echo $location['check_in_lat']; ?>,<?php echo $location['check_in_lng']; ?>" target="_blank" class="text-info">
                                                        <i class="fa fa-external-link"></i> View Map
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($location['check_out_location']): ?>
                                                <i class="fa fa-map-marker text-warning"></i>
                                                <small><?php echo substr($location['check_out_location'], 0, 40) . '...'; ?></small>
                                                <?php if ($location['check_out_lat']): ?>
                                                    <br>
                                                    <a href="https://www.google.com/maps?q=<?php echo $location['check_out_lat']; ?>,<?php echo $location['check_out_lng']; ?>" target="_blank" class="text-info">
                                                        <i class="fa fa-external-link"></i> View Map
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $location['ip_address'] ?: '-'; ?></td>
                                        <td>
                                            <?php if ($location['check_in_id']): ?>
                                                <a href="#" class="btn btn-default btn-icon btn-sm view-details" 
                                                   data-id="<?php echo $location['check_in_id']; ?>" 
                                                   data-toggle="tooltip" title="Check In Details">
                                                    <i class="fa fa-sign-in text-success"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($location['check_out_id']): ?>
                                                <a href="#" class="btn btn-default btn-icon btn-sm view-details" 
                                                   data-id="<?php echo $location['check_out_id']; ?>" 
                                                   data-toggle="tooltip" title="Check Out Details">
                                                    <i class="fa fa-sign-out text-warning"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> Location Details</h4>
            </div>
            <div class="modal-body" id="modal_content">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
    'use strict';
    
    // Initialize DataTable
    var table = $('#location_table').DataTable({
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true
    });
    
    // Filter button
    $('#filter_btn').on('click', function() {
        var dateFrom = $('#date_from').val();
        var dateTo = $('#date_to').val();
        var staffId = $('#staff_filter').val();
        
        var url = admin_url + 'staff_attendance_tracker?date_from=' + dateFrom + '&date_to=' + dateTo;
        if (staffId) {
            url += '&staff_id=' + staffId;
        }
        window.location.href = url;
    });
    
    // Reset button
    $('#reset_btn').on('click', function() {
        window.location.href = admin_url + 'staff_attendance_tracker';
    });
    
    // View details
    $(document).on('click', '.view-details', function(e) {
        e.preventDefault();
        var locationId = $(this).data('id');
        
        $.ajax({
            url: admin_url + 'staff_attendance_tracker/view_details/' + locationId,
            type: 'GET',
            success: function(response) {
                $('#modal_content').html(response);
                $('#detailsModal').modal('show');
            },
            error: function() {
                alert_float('danger', 'Failed to load details');
            }
        });
    });
});
</script>

</body>
</html>