<div class="page-content-wrapper" ng-controller="transactionController as trn">
    <div class="page-content">
        
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <!-- Begin: life time stats -->
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-barcode"></i>Product: <?php echo h($product['Product']['name']); ?> <?php echo h($product['Product']['sku']); ?>
                        </div>
                        <div class="actions">
                            <?php echo $this->Html->link(__('<i class="fa fa-angle-left"></i> Back'), array('controller'=> 'inventories','action' => 'index'),array('escape'=> false, 'class' => 'btn default yellow-stripe')); ?>
                        </div>
                    </div>

                    <div class="portlet-body">
                        <div class="tabbable">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_thistory" data-toggle="tab"> Transaction History</a></li>
                                <li><a href="#tab_charts" data-toggle="tab"> Charts </a></li>
                            </ul>
                        </div>
                        
                        <div class="csv-div">
                            <div class="col-md-3">
                                <a class="csv-icons" href="<?php echo $this->Html->url([ 'controller' => 'inventories', 'action' => 'trn_export', $product['Product']['id']]); ?>"><i class="fa fa-download"></i> Export </a>
                                <button class="csv-icons import-btn btn-link" ng-click="showAll()"><i class="fa fa-undo"></i> Show All Locations</button>
                                <?php /*echo $this->html->link('<i class="fa fa-undo"></i> Show All Locations', array('plugin' => false, 'controller' => 'inventories', 'action' => 'transactions_history', $product['Product']['id']), array('id' => 'clear', 'class' => 'csv-icons import-btn', 'ng-click'=>'showAll()', 'escape' => false, 'title' => 'Show all locations'));*/ ?>
                            </div>

                            <div class="col-md-9 text-right">
                                <?php if($cum_qty) { ?>
                                    <?php foreach ($cum_qty as $warehouse_id => $qnt) { ?>
                                        <?php
                                            $btn_class = 'btn-success';
                                            if($qnt['cum_qty'] != $qnt['inv_qty']) {
                                                $btn_class = 'btn-warning';
                                            }
                                        ?>
                                        <button ng-click="alignQty(<?php echo $product['Product']['id']; ?>, <?php echo $warehouse_id; ?>);" class="btn btn-xs <?php echo $btn_class; ?>"><?php echo $warehouses[$warehouse_id]; ?> - Cum qty: <?php echo ($qnt['cum_qty']); ?> / Inv. qty: <?php echo ($qnt['inv_qty']); ?></button>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>

                        
                        <hr/>

                        <?php echo $this->Form->create('OrdersLine', array(
                            'class' => 'form-horizontal',
                            'id' => 'orderline-search',
                            'url' => array('controller' => 'inventories', 'action' => 'transactions_history', 'product_id' => $product['Product']['id']),
                            )); ?>

                            <div class="row margin-bottom-20">
                                <?php if ($product['User']['locationsactive']) { ?>
                                <label class="col-md-1 control-label">Filter By: </label>
                                <div class="col-md-3">
                                    <?php echo $this->Form->input('warehouse_id', array(
                                        'label' => false,
                                        'class'=>'form-control form-filter input',
                                        'required' => false,
                                        'empty' => 'All Locations',
                                        'ng-change' => 'applySearch()',
                                        'ng-model' => "selectedItem"));
                                    ?>
                                </div>
                                <?php } else { ?>
                                <div class="col-md-3"></div>
                                <?php } ?>

                                <div class="col-md-7">
                                    <div class="input-group col-md-offset-7 col-md-5">
                                        <?php echo $this->Form->input('searchby', array(
                                            'label' => false,
                                            'class'=>'form-control',
                                            'placeholder' => 'Search by remarks',
                                            'ng-model' => 'query',
                                            'ng-change' => 'applySearch()',
                                            'value' => ''));
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-1 text-right">
                                    <?php echo $this->Form->select('pageBottom', $options, array(
                                        'value' => $limit,
                                        'default' => 10,
                                        'empty' => false,
                                        'class'=>'form-control form-filter input-md limit',
                                        'ng-model' => 'limit',
                                        'ng-change' => 'applySearch()'
                                        )
                                    ); ?>
                                </div>
                            </div>
                        <?php  echo $this->Form->end(); ?>

                        <div class="tab-content no-space">
                            <div class="tab-pane active" id="tab_thistory">
                                <table class="table table-hover no-footer" role="grid">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th> Type </th>
                                            <th> Order # </th>
                                            <th> SKU </th>
                                            <th> Name </th>
                                            <th ng-if="product.User.locationsactive == 1">Location</th>
                                            <th> Quantity </th>
                                            <th>Inv. Change</th>
                                            <th ng-if="is_cum_qty == 1">Cum Qty</th>
                                            <th>User</th>
                                            <th>Remarks</th>
                                            <th>Time &amp; Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="line in transactions | orderBy:sortType:sortReverse" id="line_{{line.date}}">
                                            <td><i class="fa {{line.ticon}}"></i> {{line.tname}}</td>
                                            <td><a ng-if="line.order_id != '4294967294'" href="<?php echo $this->Html->url(array('controller'=>'orders','action'=>'details')); ?>/{{line.order_id}}">{{line.order_id}}</a></td>
                                            <td>{{line.product_sku}}</td>
                                            <td ng-bind-html="product_name(line.product_name)">{{product_name(line.product_name)}}</td>
                                            <td ng-if="product.User.locationsactive == 1">{{line.warehouse_name}}</td>
                                            <td>{{line.quantity}}</td>
                                            <td>{{line.tquantity}}</td>
                                            <td ng-if="is_cum_qty == 1">{{line.cum_qty}}</td>
                                            <td>{{line.creator}}</td>
                                            <td>
                                                <a 
                                                    href="#" 
                                                    id="{{$index + 1}}"
                                                    class="remarks-editable"
                                                    data-inputclass="form-control"
                                                    data-title="Enter remarks"
                                                    data-type="text"
                                                    data-pk="{{line.id}}"
                                                    data-url="<?php echo Router::url('/orders_lines/updateRemarks', true); ?>"
                                                    data-title="Enter username"
                                                >{{line.comments}}<span style="float: right;"><i class="fa fa-pencil"></i></span></a>
                                            </td>
                                            <td>{{line.date}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane" id="tab_charts">
                                <div class="portlet-body">
                                    <div id="chart_2" class="chart">
                                    </div>
                                </div>
                            </div>

                            <p style="margin:0px;">Total found: {{totalItems}} rows</p>
                            <div class="row">
                                <div class="col-md-10">
                                    <pagination total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="limit" max-size="maxSize"></pagination>
                                </div>
                                <div class="col-md-2 text-right">
                                    <?php echo $this->Form->select('pageBottom', $options, array(
                                        'value' => $limit,
                                        'default' => 10,
                                        'empty' => false,
                                        'class'=>'form-control form-filter input-md limit',
                                        'ng-model' => 'limit',
                                        'ng-change' => 'applySearch()'
                                        )
                                    ); ?>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>  
<!-- END CONTENT -->

<?php echo $this->Html->script('/app/Inventories/transactions_history.js?v=0.0.1', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.min.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.resize.min.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.pie.min.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.stack.min.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.crosshair.min.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.categories.min.js', array('block' => 'pageBlock')); ?>

<?php echo $this->Html->script('/assets/admin/pages/scripts/charts-flotcharts.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/local/index.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/local/acharts.js?v=0.0.2', array('block' => 'pageBlock')); ?>

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    
    var product = <?php echo json_encode($product); ?>;
    var limit = <?php echo $limit; ?>;

    $(document).ready(function() {
        acharts.init();
        Index.init();

        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });
        //$('[data-toggle="tooltip"]').tooltip();

        $('select#OrdersLineWarehouseId').select2({
            minimumResultsForSearch: -1,
            placeholder: "Select Location"
        });

        $('select.limit').select2({
            minimumResultsForSearch: -1,
            width: '80px'
        });

        $.fn.editable.defaults.mode = 'inline';
        $(document).on('mousemove', function() {
            $('.remarks-editable').editable();
        });
    });

<?php $this->Html->scriptEnd(); ?>
</script>