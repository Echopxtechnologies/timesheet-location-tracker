<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        
                        <h4>
                            <i class="fa fa-history"></i> Full Attendance History
                        </h4>
                        
                        <hr>

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
                                <a href="<?php echo admin_url('staff_attendance_tracker/export'); ?>" class="btn btn-success">
                                    <i class="fa fa-download"></i> Export
                                </a>
                            </div>
                        </div>

                        <hr>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped dt-table" id="history_table">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Type</th>
                                        <th>Date & Time</th>
                                        <th>Location</th>
                                        <th>Coordinates</th>
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
                                        <td>
                                            <?php if ($loc['check_type'] == 1): ?>
                                                <span class="label label-success">Check In</span>
                                            <?php else: ?>
                                                <span class="label label-warning">Check Out</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo _dt($loc['check_datetime']); ?></td>
                                        <td>
                                            <?php if ($loc['address']): ?>
                                                <i class="fa fa-map-marker"></i>
                                                <small><?php echo substr($loc['address'], 0, 40) . '...'; ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($loc['latitude'] && $loc['longitude']): ?>
                                                <a href="https://www.google.com/maps?q=<?php echo $loc['latitude']; ?>,<?php echo $loc['longitude']; ?>" target="_blank" class="text-info">
                                                    <i class="fa fa-external-link"></i> <?php echo number_format($loc['latitude'], 6); ?>, <?php echo number_format($loc['longitude'], 6); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $loc['ip_address']; ?></td>
                                        <td>
                                            <a href="#" class="btn btn-default btn-icon btn-sm view-details" data-id="<?php echo $loc['id']; ?>">
                                                <i class="fa fa-eye"></i>
                                            </a>
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
    $('#history_table').DataTable({
        order: [[2, 'desc']],
        pageLength: 50
    });
    
    $('#filter_btn').click(function() {
        var url = admin_url + 'staff_attendance_tracker/history?';
        url += 'date_from=' + $('#date_from').val();
        url += '&date_to=' + $('#date_to').val();
        if ($('#staff_filter').val()) {
            url += '&staff_id=' + $('#staff_filter').val();
        }
        window.location.href = url;
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