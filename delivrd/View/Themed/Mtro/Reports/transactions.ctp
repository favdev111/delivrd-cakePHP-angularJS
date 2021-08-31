<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="trReport">
    <div class="page-content">
        <?php if(!$tx_reports_all) { ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-share font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Delivrd Reports</span>
                </div>
            </div>
            <div class="portlet-body">
                <h3 class="text-center text-danger" style="margin-top: 0px;margin-bottom: 31px;">TX Report not ready. To generate TX Report use link</h3>
                <div class="text-center"><a href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'collect_data']); ?>" class="btn btn-warning">Generate TX Report</a></div>
            </div>
        </div>
        <?php } ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-barcode"></i>
                        </div>
                        <div class="actions">
                        </div>
                    </div>

                    <div class="portlet-body">
                    	 <div class="tab-content no-space">
                    	 	<?php #pr($transactions); ?>
                            <div class="tab-pane active" id="tab_thistory">
                                <table class="table table-hover no-footer" role="grid">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th><a ng-click="orderBy('Product.sku')" class="sort-link">SKU <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Product.name')" class="sort-link">Product <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Warehouse.name')" class="sort-link">Location <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('User.email')" class="sort-link">User <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th>Inv Qty</th>
                                            <th>Cum Qty</th>
                                            <th><a ng-click="orderBy('Txreport.inv_modified')" class="sort-link">Inv. Update <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Txreport.tx_last')" class="sort-link">Last Txn. <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    	<tr ng-repeat="line in transactions | orderBy:sortType:sortReverse">
                                    		<td>{{ line.Product.sku }}</td>
                                    		<td>{{ line.Product.name }}</td>
                                    		<td>{{ line.Warehouse.name }}</td>
                                    		<td>{{ line.User.email }}</td>
                                    		<td>{{ line.Txreport.inv_quantity }}</td>
                                    		<td>{{ line.Txreport.tx_quantity }}</td>
                                    		<td>{{ line.Txreport.inv_modified }}</td>
                                    		<td>{{ line.Txreport.tx_last }}</td>
                                    	</tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <p>Total found: {{totals}}</p>
                        <pagination max-size="30" total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="limit"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var limit = <?php echo $limit; ?>
<?php $this->Html->scriptEnd(); ?>

<?php echo $this->Html->script('/app/Reports/transactions.js?v=0.0.1', array('block' => 'pageBlock')); ?>