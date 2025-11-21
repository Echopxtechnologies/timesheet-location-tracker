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
    $('#history_table').DataTable({
        order: [[2, 'desc']],
        pageLength: 50
    });
});
</script>
</body>
</html>