<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                            <i class="fa fa-cog"></i> Staff Attendance Tracker Settings
                        </h4>
                        
                        <hr class="hr-panel-heading">

                        <?php echo form_open(admin_url('staff_attendance_tracker/settings')); ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h5 class="bold">Google Maps Configuration</h5>
                                        <hr>
                                        
                                        <div class="form-group">
                                            <label for="google_api_key" class="control-label">
                                                <i class="fa fa-map-marker"></i> Google Maps API Key
                                                <small class="text-danger">*</small>
                                            </label>
                                            <input type="text" 
                                                   id="google_api_key" 
                                                   name="google_api_key" 
                                                   class="form-control" 
                                                   value="<?php echo set_value('google_api_key', $google_api_key); ?>"
                                                   placeholder="Enter your Google Maps API Key">
                                            <p class="text-muted mtop10">
                                                <i class="fa fa-info-circle"></i> This API key is used for:
                                            </p>
                                            <ul class="text-muted">
                                                <li>Reverse geocoding (converting GPS coordinates to addresses)</li>
                                                <li>Displaying location maps in detail views</li>
                                            </ul>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fa fa-lightbulb-o"></i> <strong>How to get a Google Maps API Key:</strong>
                                            <ol class="mtop10">
                                                <li>Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                                                <li>Create a new project or select an existing one</li>
                                                <li>Enable these APIs:
                                                    <ul>
                                                        <li>Maps JavaScript API</li>
                                                        <li>Geocoding API</li>
                                                    </ul>
                                                </li>
                                                <li>Go to "Credentials" and create an API Key</li>
                                                <li>Copy the API key and paste it here</li>
                                            </ol>
                                            <p class="mtop10">
                                                <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" class="btn btn-info btn-xs">
                                                    <i class="fa fa-external-link"></i> View Full Documentation
                                                </a>
                                            </p>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="col-md-4">
                                
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h5 class="bold">Module Information</h5>
                                        <hr>
                                        
                                        <table class="table table-bordered table-condensed">
                                            <tr>
                                                <th width="40%">Module Name</th>
                                                <td>Staff Attendance Tracker</td>
                                            </tr>
                                            <tr>
                                                <th>Version</th>
                                                <td>1.0.0</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td><span class="label label-success">Active</span></td>
                                            </tr>
                                            <tr>
                                                <th>Author</th>
                                                <td>Emmanuel N</td>
                                            </tr>
                                        </table>

                                        <div class="alert alert-warning mtop15">
                                            <i class="fa fa-exclamation-triangle"></i> 
                                            <strong>Note:</strong> Changes to the API key will affect all location tracking features.
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <button type="submit" class="btn btn-primary pull-right">
                                    <i class="fa fa-save"></i> Save Settings
                                </button>
                                <a href="<?php echo admin_url('staff_attendance_tracker'); ?>" class="btn btn-default pull-right mright5">
                                    <i class="fa fa-arrow-left"></i> Back to Locations
                                </a>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <?php echo form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
</body>
</html>