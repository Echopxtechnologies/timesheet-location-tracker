<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        
                        <h4>
                            <i class="fa fa-dashboard"></i> Latest Attendance Status
                        </h4>
                        
                        <hr class="hr-panel-heading">

                        <!-- Filters -->
                        <div class="row">
                            <div class="col-md-3">
                                <?php echo render_date_input('date_from', 'Date From', $this->input->get('date_from') ?: date('Y-m-d', strtotime('-7 days'))); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_date_input('date_to', 'Date To', $this->input->get('date_to') ?: date('Y-m-d')); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_select('staff_filter', $staff_members, ['staffid', ['firstname', 'lastname']], 'Staff', $this->input->get('staff_id'), ['data-none-selected-text' => 'All Staff']); ?>
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

                        <hr>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped dt-table" id="location_table">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Date</th>
                                        <th>Check In Time</th>
                                        <th>Check In Location</th>
                                        <th>Check Out Time</th>
                                        <th>Check Out Location</th>
                                        <th>IP Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($locations as $loc): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo admin_url('staff/profile/' . $loc['staff_id']); ?>">
                                                <?php echo $loc['staff_name']; ?>
                                            </a>
                                        </td>
                                        <td><?php echo _d($loc['date']); ?></td>
                                        <td>
                                            <?php if ($loc['check_in_time']): ?>
                                                <span class="label label-success">
                                                    <i class="fa fa-clock-o"></i> <?php echo date('h:i A', strtotime($loc['check_in_time'])); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($loc['check_in_location']): ?>
                                                <i class="fa fa-map-marker text-success"></i>
                                                <small><?php echo substr($loc['check_in_location'], 0, 30) . '...'; ?></small>
                                                <?php if ($loc['check_in_lat']): ?>
                                                    <br><a href="https://www.google.com/maps?q=<?php echo $loc['check_in_lat']; ?>,<?php echo $loc['check_in_lng']; ?>" target="_blank" class="text-info">
                                                        <i class="fa fa-external-link"></i> Map
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($loc['check_out_time']): ?>
                                                <span class="label label-warning">
                                                    <i class="fa fa-clock-o"></i> <?php echo date('h:i A', strtotime($loc['check_out_time'])); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($loc['check_out_location']): ?>
                                                <i class="fa fa-map-marker text-warning"></i>
                                                <small><?php echo substr($loc['check_out_location'], 0, 30) . '...'; ?></small>
                                                <?php if ($loc['check_out_lat']): ?>
                                                    <br><a href="https://www.google.com/maps?q=<?php echo $loc['check_out_lat']; ?>,<?php echo $loc['check_out_lng']; ?>" target="_blank" class="text-info">
                                                        <i class="fa fa-external-link"></i> Map
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $loc['ip_address'] ?: '-'; ?></td>
                                        <td>
                                            <?php if ($loc['check_in_id']): ?>
                                                <a href="#" class="btn btn-default btn-icon btn-sm view-details" data-id="<?php echo $loc['check_in_id']; ?>">
                                                    <i class="fa fa-sign-in text-success"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($loc['check_out_id']): ?>
                                                <a href="#" class="btn btn-default btn-icon btn-sm view-details" data-id="<?php echo $loc['check_out_id']; ?>">
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

<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4><i class="fa fa-info-circle"></i> Location Details</h4>
            </div>
            <div class="modal-body" id="modal_content"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
    $('#location_table').DataTable({
        order: [[1, 'desc']],
        pageLength: 25
    });
    
    $('#filter_btn').click(function() {
        var url = admin_url + 'staff_attendance_tracker?';
        url += 'date_from=' + $('#date_from').val();
        url += '&date_to=' + $('#date_to').val();
        if ($('#staff_filter').val()) {
            url += '&staff_id=' + $('#staff_filter').val();
        }
        window.location.href = url;
    });
    
    $('#reset_btn').click(function() {
        window.location.href = admin_url + 'staff_attendance_tracker';
    });
    
    $(document).on('click', '.view-details', function(e) {
        e.preventDefault();
        $.get(admin_url + 'staff_attendance_tracker/view_details/' + $(this).data('id'), function(response) {
            $('#modal_content').html(response);
            $('#detailsModal').modal('show');
        });
    });
});
</script>
</body>
</html>