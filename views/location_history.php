<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <h4>
                                    <i class="fa fa-history"></i> Full Attendance History
                                    <small class="text-muted">(All Check-In/Out Records)</small>
                                </h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('staff_attendance_tracker'); ?>" class="btn btn-default">
                                    <i class="fa fa-dashboard"></i> Latest Status
                                </a>
                                <a href="<?php echo admin_url('staff_attendance_tracker/export'); ?>" class="btn btn-success">
                                    <i class="fa fa-download"></i> Export
                                </a>
                            </div>
                        </div>
                        
                        <hr class="hr-panel-heading">

                        <!-- Statistics Summary -->
                        <div class="row mtop15 mbot15">
                            <?php
                            $total_records = count($locations);
                            $check_ins = 0;
                            $check_outs = 0;
                            
                            foreach ($locations as $loc) {
                                if ($loc['check_type'] == 1) {
                                    $check_ins++;
                                } else {
                                    $check_outs++;
                                }
                            }
                            ?>
                            <div class="col-md-3">
                                <div class="panel_s">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h3 class="text-info mtop0 mbot5">
                                            <i class="fa fa-database"></i> <?php echo $total_records; ?>
                                        </h3>
                                        <p class="text-muted mbot0"><strong>Total Records</strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel_s">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h3 class="text-success mtop0 mbot5">
                                            <i class="fa fa-sign-in"></i> <?php echo $check_ins; ?>
                                        </h3>
                                        <p class="text-muted mbot0"><strong>Check-In Records</strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel_s">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h3 class="text-warning mtop0 mbot5">
                                            <i class="fa fa-sign-out"></i> <?php echo $check_outs; ?>
                                        </h3>
                                        <p class="text-muted mbot0"><strong>Check-Out Records</strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel_s">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h3 class="text-primary mtop0 mbot5">
                                            <i class="fa fa-users"></i> <?php echo count(array_unique(array_column($locations, 'staff_id'))); ?>
                                        </h3>
                                        <p class="text-muted mbot0"><strong>Unique Staff</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-table" id="history_table">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Type</th>
                                        <th>Date & Time</th>
                                        <th>Location & Coordinates</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($locations as $loc): ?>
                                    <tr>
                                        <!-- Staff Name -->
                                        <td>
                                            <a href="<?php echo admin_url('staff/profile/' . $loc['staff_id']); ?>">
                                                <strong><?php echo $loc['staff_name']; ?></strong>
                                            </a>
                                        </td>
                                        
                                        <!-- Type Badge -->
                                        <td>
                                            <?php if ($loc['check_type'] == 1): ?>
                                                <span class="label label-success" style="font-size: 12px; padding: 5px 10px;">
                                                    <i class="fa fa-sign-in"></i> Check In
                                                </span>
                                            <?php else: ?>
                                                <span class="label label-warning" style="font-size: 12px; padding: 5px 10px;">
                                                    <i class="fa fa-sign-out"></i> Check Out
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Date & Time -->
                                        <td>
                                            <strong><?php echo _dt($loc['check_datetime']); ?></strong>
                                            <br><small class="text-muted">
                                                <i class="fa fa-clock-o"></i> <?php echo time_ago($loc['check_datetime']); ?>
                                            </small>
                                        </td>
                                        
                                        <!-- Location & Coordinates -->
                                        <td>
                                            <?php if ($loc['address']): ?>
                                                <i class="fa fa-map-marker <?php echo $loc['check_type'] == 1 ? 'text-success' : 'text-warning'; ?>"></i>
                                                <small><?php echo $loc['address']; ?></small>
                                                
                                                <?php if ($loc['latitude'] && $loc['longitude']): ?>
                                                    <br><small class="text-muted">
                                                        <strong>Coordinates:</strong> <?php echo number_format($loc['latitude'], 6); ?>, <?php echo number_format($loc['longitude'], 6); ?>
                                                    </small>
                                                    <br><a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($loc['address']); ?>" target="_blank" class="text-info">
                                                        <i class="fa fa-external-link"></i> View on Map
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">No location data</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- IP Address -->
                                        <td>
                                            <small><?php echo $loc['ip_address'] ?: '-'; ?></small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (empty($locations)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <p class="mtop20 mbot20">
                                                <i class="fa fa-info-circle fa-2x"></i>
                                                <br><br>No attendance records found.
                                            </p>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
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
    'use strict';
    
    // Initialize DataTable
    var table = $('#history_table').DataTable({
        order: [[2, 'desc']], // Sort by date & time (newest first)
        pageLength: 50,
        language: {
            emptyTable: "No attendance records found"
        }
    });
});
</script>
</body>
</html>