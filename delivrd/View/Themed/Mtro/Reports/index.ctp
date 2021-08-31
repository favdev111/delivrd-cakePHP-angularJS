<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="trReport">
    <div class="page-content">
    	
        <?php echo $this->Session->flash(); ?>

        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-share font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Delivrd Reports</span>
                </div>
            </div>
            <div class="portlet-body">
                <?php if($tx_reports_all) { ?>
                    <div class="text-center"><a href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'collect_data']); ?>" class="btn btn-success">RE-Generate TX Report</a></div>
                <?php } else { ?>
                    <h3 class="text-center text-danger" style="margin-top: 0px;margin-bottom: 31px;">TX Report not ready. To generate TX Report use link</h3>
                    <div class="text-center"><a href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'collect_data']); ?>" class="btn btn-warning">Generate TX Report</a></div>
                <?php } ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat green-haze">
                    <div class="visual">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <?php echo $tx_errors; ?>
                        </div>
                        <div class="desc">
                            Inventory quantity vs Cumulative quantity
                        </div>
                    </div>
                    <a class="more" href="<?php echo $this->Html->url(['controller' => 'reports', 'action'=>'transactions']); ?>">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat red-intense">
                    <div class="visual">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <?php echo $duplicateCount; ?>
                        </div>
                        <div class="desc">
                            Duplicate SKU Report
                        </div>
                    </div>
                    <?php echo $this->Html->link(__('View more <i class="m-icon-swapright m-icon-white"></i>'), array('controller' => 'reports', 'action' => 'duplicate'), array('class' => 'more', 'escape' => false)); ?>
                </div>
            </div>
        </div>
    </div>
</div>