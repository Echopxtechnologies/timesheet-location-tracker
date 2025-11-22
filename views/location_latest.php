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
                                    <i class="fa fa-dashboard"></i> Latest Attendance Status
                                    <small class="text-muted">(Last Check-In/Out per Staff)</small>
                                </h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('staff_attendance_tracker/history'); ?>" class="btn btn-default">
                                    <i class="fa fa-history"></i> Full History
                                </a>
                            </div>
                        </div>
                        
                        <hr class="hr-panel-heading">

                        <!-- Status Summary Cards -->
                        <div class="row mtop15 mbot15">
                            <?php
                            $checked_in = 0;
                            $checked_out = 0;
                            $no_activity = 0;
                            
                            foreach ($locations as $staff) {
                                if ($staff['current_status'] == 'Checked In') {
                                    $checked_in++;
                                } elseif ($staff['current_status'] == 'Checked Out') {
                                    $checked_out++;
                                } else {
                                    $no_activity++;
                                }
                            }
                            ?>
                            <div class="col-md-3">
                                <div class="panel_s">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h3 class="text-success mtop0 mbot5">
                                            <i class="fa fa-sign-in"></i> <?php echo $checked_in; ?>
                                        </h3>
                                        <p class="text-muted mbot0"><strong>Currently Checked In</strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel_s">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h3 class="text-warning mtop0 mbot5">
                                            <i class="fa fa-sign-out"></i> <?php echo $checked_out; ?>
                                        </h3>
                                        <p class="text-muted mbot0"><strong>Currently Checked Out</strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel_s">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h3 class="text-muted mtop0 mbot5">
                                            <i class="fa fa-user"></i> <?php echo $no_activity; ?>
                                        </h3>
                                        <p class="text-muted mbot0"><strong>No Activity</strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel_s">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h3 class="text-info mtop0 mbot5">
                                            <i class="fa fa-users"></i> <?php echo count($locations); ?>
                                        </h3>
                                        <p class="text-muted mbot0"><strong>Total Active Staff</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-table" id="location_table">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Current Status</th>
                                        <th>Last Check-In</th>
                                        <th>Check-In Location</th>
                                        <th>Last Check-Out</th>
                                        <th>Check-Out Location</th>
                                        <th>Last IP</th>
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
                                            <?php if (!empty($loc['staff_email'])): ?>
                                                <br><small class="text-muted"><?php echo $loc['staff_email']; ?></small>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Current Status Badge -->
                                        <td>
                                            <?php if ($loc['current_status'] == 'Checked In'): ?>
                                                <span class="label label-success" style="font-size: 12px; padding: 5px 10px;">
                                                    <i class="fa fa-circle"></i> Checked In
                                                </span>
                                            <?php elseif ($loc['current_status'] == 'Checked Out'): ?>
                                                <span class="label label-warning" style="font-size: 12px; padding: 5px 10px;">
                                                    <i class="fa fa-circle"></i> Checked Out
                                                </span>
                                            <?php else: ?>
                                                <span class="label label-default" style="font-size: 12px; padding: 5px 10px;">
                                                    <i class="fa fa-circle-o"></i> No Activity
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Last Check-In Time -->
                                        <td>
                                            <?php if ($loc['check_in_time']): ?>
                                                <strong><?php echo _dt($loc['check_in_time']); ?></strong>
                                                <br><small class="text-muted">
                                                    <i class="fa fa-clock-o"></i> <?php echo time_ago($loc['check_in_time']); ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Check-In Location -->
                                        <td>
                                            <?php if ($loc['check_in_location']): ?>
                                                <i class="fa fa-map-marker text-success"></i>
                                                <small><?php echo $loc['check_in_location']; ?></small>
                                                
                                                <?php if ($loc['check_in_lat'] && $loc['check_in_lng']): ?>
                                                    <br><small class="text-muted">
                                                        <strong>Coordinates:</strong> <?php echo number_format($loc['check_in_lat'], 6); ?>, <?php echo number_format($loc['check_in_lng'], 6); ?>
                                                    </small>
                                                    <br><a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($loc['check_in_location']); ?>" target="_blank" class="text-info">
                                                        <i class="fa fa-external-link"></i> View on Map
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Last Check-Out Time -->
                                        <td>
                                            <?php if ($loc['check_out_time']): ?>
                                                <strong><?php echo _dt($loc['check_out_time']); ?></strong>
                                                <br><small class="text-muted">
                                                    <i class="fa fa-clock-o"></i> <?php echo time_ago($loc['check_out_time']); ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Check-Out Location -->
                                        <td>
                                            <?php if ($loc['check_out_location']): ?>
                                                <i class="fa fa-map-marker text-warning"></i>
                                                <small><?php echo $loc['check_out_location']; ?></small>
                                                
                                                <?php if ($loc['check_out_lat'] && $loc['check_out_lng']): ?>
                                                    <br><small class="text-muted">
                                                        <strong>Coordinates:</strong> <?php echo number_format($loc['check_out_lat'], 6); ?>, <?php echo number_format($loc['check_out_lng'], 6); ?>
                                                    </small>
                                                    <br><a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($loc['check_out_location']); ?>" target="_blank" class="text-info">
                                                        <i class="fa fa-external-link"></i> View on Map
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Last IP Address -->
                                        <td>
                                            <small><?php echo $loc['ip_address'] ?: '-'; ?></small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (empty($locations)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
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
    var table = $('#location_table').DataTable({
        order: [[1, 'asc'], [2, 'desc']], // Sort by status (checked in first), then by check-in time
        pageLength: 25,
        language: {
            emptyTable: "No attendance records found"
        }
    });
});
</script>
</body>
</html>