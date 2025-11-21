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

<?php init_tail(); ?>

<script>
$(function() {
    $('#location_table').DataTable({
        order: [[1, 'desc']],
        pageLength: 25
    });
});
</script>
</body>
</html>