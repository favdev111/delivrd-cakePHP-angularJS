<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="dpReport">
    <div class="page-content">

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
                            <div class="tab-pane active">
                                <table class="table no-footer" role="grid">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th><a ng-click="orderBy('Product.sku')" class="sort-link">SKU <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Product.name')" class="sort-link">Product <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('User.email')" class="sort-link">User <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Product.created')" class="sort-link">Created <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th>Duplicates</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody ng-repeat="line in products | orderBy:sortType:sortReverse">

                                        <tr class="bg-grey">
                                            <td>{{ line.p.sku }}</td>
                                            <td>{{ line.p.id }}</td>
                                            <td>{{ users[line.p.user_id] }} <span ng-if="users[line.p.user_id] == undefined"><i class="fa fa-exclamation-triangle font-red-soft" title="User not Found"></i> {{ line.p.user_id }}</span></td>
                                            <td>{{ line.p.created }}</td>
                                            <td>{{ line.0.product_count }}</td>
                                            <td>
                                                <button ng-if="line.details == undefined" ng-click="showDetails(line.p.sku, line.p.user_id)" class="btn btn-xs btn-info"><i class="fa fa-plus"></i>
                                                <button ng-if="line.details != undefined" ng-click="hideDetails(line.p.sku, line.p.user_id)" class="btn btn-xs btn-info"><i class="fa fa-minus"></i>
                                            </td>
                                        </tr>
                                        <tr ng-if="line.details != undefined">
                                            <td colspan="6">
                                                <table class="table table-bordered table-hover">
                                                    <tr>
                                                        <th width="70%">Product</th>
                                                        <th>Status</th>
                                                        <th>Tx. number</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr ng-repeat="prod in line.details">
                                                        <td>
                                                            <?php echo ang($this->Html->link(__('<i class="fa fa-barcode"></i> {{prod.Product.name}}'), array('controller' => 'products', 'action' => 'view', '{{prod.Product.id}}'), array('escape'=> false))); ?>
                                                        </td>
                                                        <td>
                                                            <span class="label label-danger" ng-if="prod.Product.deleted == 1">
                                                                Deleted
                                                            </span>
                                                            <span class="label label-warning" ng-if="prod.Product.status_id == 12 || prod.Product.status_id == 13">
                                                                Blocked
                                                            </span>
                                                            <span class="label label-success" ng-if="prod.Product.status_id != 12 && prod.Product.status_id != 13 && prod.Product.deleted != 1">
                                                                Active
                                                            </span>
                                                        </td>
                                                        <td>{{prod.0.tx_count}}</td>
                                                        <td>
                                                            <?php echo ang($this->Html->link(__('<i class="fa fa-edit"></i> Edit'), array('controller' => 'products', 'action' => 'edit', '{{prod.Product.id}}'), array('escape'=> false, 'class' => 'btn btn-warning btn-xs'))); ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <pagination max-size="30" total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="limit"></pagination>
                        <p>Total found: {{totals}} row(s).</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var limit = <?php echo $limit; ?>;
    var users = <?php echo json_encode($users); ?>;
<?php $this->Html->scriptEnd(); ?>

<?php echo $this->Html->script('/app/Reports/duplicate.js?v=0.0.1', array('block' => 'pageBlock')); ?>
<?php function ang($str) {
    $str = str_replace('%7B%7B', '{{', $str);
    $str = str_replace('%7D%7D', '}}', $str);
    return $str;
}?>