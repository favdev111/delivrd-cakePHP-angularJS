<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Issue Report</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div ng-if="reports.success.length > 0">
                        <h4 class="text-success"><i class="fa fa-check"></i> <strong>Success Issued</strong></h4>
                        <div>
                            <span ng-repeat="order_num in reports.success" style="padding-right:8px;">
                                #{{order_num}}
                            </span>
                        </div>
                    </div>
                    
                    <div ng-if="reports.access_alert.length > 0 || reports.negativ_alert.length > 0 || reports.error_alert.length > 0">
                        <h4 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Warnings (skiped orders)</h4>

                        <div ng-if="reports.access_alert.length > 0">
                            <p><strong>Access Problems</strong> <small>You can't issue this orders because it have lines for which you have no access</small></p>
                            <span ng-repeat="order_num in reports.access_alert">
                                #{{order_num}}
                            </span>
                        </div>

                        <div ng-if="reports.negativ_alert.length > 0">
                            <p><strong>Negative</strong> <small>Quantity for some lines will be negative if we issue this orders.</small></p>
                            <span ng-repeat="order_num in reports.negativ_alert">
                                #{{order_num}}
                            </span>
                        </div>
                        
                        <div ng-if="reports.error_alert.length > 0">
                            <p><strong>Errors</strong> <small>This orders skipped, please try to issue it manual for more details.</small></p>
                            <span ng-repeat="order_num in reports.error_alert">
                                #{{order_num}}
                            </span>
                        </div>
                    </div>
                    
                    <?php #echo $this->element('sql_dump');?>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn default" style="box-shadow: none;" ng-click="close($event)"><i class="fa fa-close"></i> Close</button>
        </div>
    </div>
</div>