<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="SalesOrderList as sol">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-shopping-cart"></i> Sales Orders List
                        </div>
                        <div class="actions">
                            <?php if($is_write) { ?>
                            <?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Add Sales Order'),
                                array('plugin' => false, 'controller' => 'salesorders', 'action' => 'create'),
                                array('class' => 'btn default yellow-stripe add-delivrd statlink', 'escape' => false, 'title' => 'New Order', 'data-link' => 'new_so'));
                            ?>
                            
                            <div class="btn-group">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php if($this->Session->read('showebaylink') == true) { ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-cloud-download"></i> Import Ebay Orders'), array('controller' => 'ebay','action' => 'getEbayOrders'),array('escape'=> false)); ?></li>
                                    <?php } ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-tasks"></i> Sales report by customer'), array('controller' => 'salesorders', 'action' => 'customer_report'), array('escape' => false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-tasks"></i> Order line profitability'), array('controller' => 'salesorders', 'action' => 'profit_report'), array('escape' => false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-bell"></i> Price differences report'), array('controller' => 'salesorders', 'action' => 'price_compare'), array('escape' => false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-ban"></i> Canceled Orders'), array('controller' => 'salesorders', 'action' => 'canceled'), array('escape' => false)); ?></li>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if($is_have_access) { ?>
                    <div class="portlet-body">
                        <div class="csv-div">
                            <div class="btn-toolbar">
                                <div class="btn-group">
                                    <?php echo $this->Html->link(__('<i class="fa fa-download"></i> Export'), array('controller'=> 'orders','action' => 'exportcsv', 1),array('escape'=> false, 'class' => 'csv-icons', 'title' => 'Export inventory list')); ?>
                                    <?php if($is_write) { ?>
                                    <?php echo $this->Html->link(__('<i class="fa fa-upload"></i> Import'), array('controller'=> 'salesorders','action' => 'uploadcsv', 1),array('escape'=> false, 'class' => 'csv-icons import-btn', 'title' => 'Import inventory from csv')); ?>
                                    <?php } ?>
                                </div>
                                <div class="btn-group pageLimit">
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <label><i class="fa fa-list"></i> Results:</label>
                                            <?php echo $this->Form->select('pageBottom', $options, array(
                                                'value'=>$limit,
                                                'default' => 10,
                                                'empty' => false,
                                                'class'=>'form-control form-filter input-md limit',
                                                'ng-change' => 'applySearch()',
                                                'ng-model' => 'limit',
                                                'select2' => ''
                                                )
                                            ); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <a href class="csv-icons import-btn" title="Show all records" ng-click="$event.preventDefault(); showAll();"><i class="fa fa-undo"></i> Show All</a>
                                </div>
                            </div>
                        </div>

                        <?php echo $this->Form->create('Order', array('class' => 'form-horizontal', 'id'=>'OrderIndexForm', 'ng-submit' => 'applySearch()')); ?>
                            <div class="row margin-bottom-20">
                                <div class="col-md-5">
                                    <div class="input-group col-md-10">
                                        <?php echo $this->Form->input('searchby', array(
                                            'label' => false,
                                            'class'=>'form-control input-md',
                                            'custom-autofocus' => 'autofocus',
                                            'placeholder' => 'Search by Order #, Ref. # or Cust. name',
                                            'ng-change' => 'applySearch()',
                                            'ng-model' => "searchby"
                                        )); ?>
                                        <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                    </div>
                                </div>

                                <label class="col-md-1 control-label">Filter By: </label>
                                <div class="col-md-2">
                                    <?php echo $this->Form->input('schannel_id', array(
                                        'label' => false,
                                        'class'=>'form-control form-filter input-md',
                                        'required' => false,
                                        'empty' => 'Sales Channel...',
                                        'ng-change' => 'applySearch()',
                                        'ng-model' => "schannel_id",
                                        'select2' => ''
                                    )); ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo $this->Form->input('status_id',array(
                                        'label' => false,
                                        'class'=>'form-control form-filter input-md',
                                        'options' => array(14 => 'Draft', 2 => 'Released', 60 => 'In Wave', 3 => 'Ship. Proc.', 4 => 'Completed', 8 => 'Shipped', 55 => 'Paid'),
                                        'empty' => 'Status...',
                                        'ng-change' => 'applySearch()',
                                        'ng-model' => "status_id",
                                        'multiple' => true,
                                        
                                    )); ?>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-md blue-354fdc filter-submit margin-bottom" type="button" ng-click="applySearch()"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        <?php echo $this->Form->end(); ?>

                        <div id="multiFunctions" class="row margin-bottom-20 hide">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-fit-height dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-v"></i> With selected 
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-lock"></i> Release Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); releaseMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-arrow-left"></i> Issue Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); issueMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Create Wave For Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); waveAddMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-ban"></i> Cancel Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); cancelMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-trash"></i> Delete Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); trashMultiple();')); ?></li>
                                </ul>
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="table table-hover">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th width="15px"><input type="checkbox" name="selAll" id="selAll" ng-click="checkAll()"></th>
                                        <th><a ng-click="orderBy('Order.id')" class="sort-link">Order # <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        <th><a ng-click="orderBy('Supplier.name')" class="sort-link">Customer <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        <th><a ng-click="orderBy('Schannel.name')" class="sort-link">Sales Channel <i class="fa fa-sort" aria-hidden="true"></i></a></th>
                                        <th><a ng-click="orderBy('Order.external_orderid')" class="sort-link">Reference Order <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        <th><a ng-click="orderBy('Order.created')" class="sort-link">Created <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        <th width="140px"><a ng-click="orderBy('Order.status_id')" class="sort-link">Status <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        <th width="140px"><a ng-click="orderBy('Order.modified')" class="sort-link">Actions <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat-start="line in orders | orderBy:sortType:sortReverse" id="line_{{line.Order.id}}" class="filter">
                                        <td><input type="checkbox" class="so_checkboxes" name="data[Order][id][]" value="{{line.Order.id}}" ng-click="checkOrder(line.Order.id)"></td>
                                        <td>
                                            <i class="{{ (line.expanded == 'yes')?('fa fa-minus-circle text-primary'):('fa fa-plus-circle text-info') }}" aria-hidden="true" ng-click="expendLines(line.Order.id)" style="margin-right: 10px;cursor: pointer;"></i>
                                            {{line.Order.id}}
                                        </td>
                                        <td>{{line.Order.ship_to_customerid}}</td>
                                        <td ng-bind-html="schannel(line)">
                                            {{schannel(line)}} 
                                        </td>
                                        <td>{{line.Order.external_orderid}}</td>
                                        <td>{{line.Order.created}}</td>
                                        <td ng-bind-html="status(line.Order.status_id)">{{status(line.Order.status_id)}}</td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <span ng-if="(line.Order.status_id == 2 || line.Order.status_id == 3 || line.Order.status_id == 60) && is_order_write(line)">
                                                    <?php echo ang($this->Html->link(__('<i class="fa fa-arrow-left"></i>Issue'), array('controller' => 'orders_lines', 'action' => 'issue', '{{line.Order.id}}'), array('escape'=> false, 'class'=>'btn btn-xs default'))); ?>
                                                </span>
                                                
                                                <span ng-if="line.Order.status_id == 14">
                                                    <button class="release-id btn btn-xs blue-354fdc" ng-click="orderRelease(line.Order.id)">Release</button>
                                                </span>

                                                <span ng-if="line.Order.status_id == 4 && is_order_write(line)">
                                                    <a href ng-click="makePaid(line.Order.id)" class="btn btn-xs grey-salsa"><i class="fa fa-credit-card"></i> Paid</a>
                                                </span>

                                                <a class="dropdown-toggle delivrd-act" href="#" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </a>
                                                <ul class="dropdown-menu pull-right">
                                                    <li><?php echo ang($this->Html->link('<i class="fa fa-search"></i> Details', array('controller'=>'salesorders', 'action' => 'details', '{{line.Order.id}}'), array('escape'=>false))); ?></li>
                                                    
                                                    <?php /*<li ng-if="line.Order.status_id == 2 && is_order_write(line)"><?php echo ang($this->Html->link('<i class="fa fa-undo"></i> Cancel Order Release', array('controller'=>'salesorders', 'action' => 'unrelease', '{{line.Order.id}}'), array('escape'=>false))); ?></li>*/ ?>
                                                    <li ng-if="(line.Order.status_id == 2 || line.Order.status_id == 60) && is_order_write(line)"><a href ng-click="cancelRelease(line.Order.id)"><i class="fa fa-undo"></i> Cancel Order Release</a></li>
                                                    <li ng-if="(line.Order.status_id == 2 || line.Order.status_id == 60 || line.Order.status_id == 3)  && is_order_write(line) && is_shipment_write(line)"><?php echo ang($this->Html->link('<i class="fa fa-truck"></i> Create Shipment', array('controller'=>'shipments', 'action' => 'add', '{{line.Order.id}}'), array('escape'=> false))); ?></li>
                                                    <li ng-if="(line.Order.status_id == 2 || line.Order.status_id == 60) && is_order_write(line)"><?php echo ang($this->Html->link('<i class="fa fa-arrow-left"></i> Issue Order Products', array('controller'=>'orders_lines', 'action' => 'issue', '{{line.Order.id}}'), array('escape'=> false))); ?></li>
                                                    <li ng-if="(line.Order.status_id == 2 || line.Order.status_id == 60 || line.Order.status_id == 3 || line.Order.status_id == 8) && is_order_write(line)">
                                                        <a href ng-click="completeOrder(line.Order.id)"><i class="fa fa-flag-checkered"></i> Complete</a>
                                                    </li>
                                                    <li ng-if=" 1 || (line.Order.status_id != 14 && line.Order.status_id != 4 && line.Order.status_id != 55)"><?php echo ang($this->Html->link('<i class="fa fa-print"></i> Print Packing Slip', array('controller'=>'salesorders', 'action' => 'printslip', '{{line.Order.id}}'), array('escape'=> false))); ?></li>
                                                    <li ng-if="line.Order.status_id == 4"><?php echo ang($this->Html->link('<i class="fa fa-print"></i> Invoice', array('controller'=>'salesorders', 'action' => 'invoice', '{{line.Order.id}}'), array('escape'=> false))); ?></li>

                                                    <li ng-if="is_order_owner(line)"><?php echo ang($this->Html->link('<i class="icon-shuffle"></i> Create P.O.', array('action' => 'createrep', '{{line.Order.id}}'), array('escape' => false))); ?></li>
                                                    
                                                    <li ng-if="line.Order.status_id == 14 && is_order_write(line)">
                                                        <?php echo ang($this->Html->link(__('<i class="fa fa-pencil"></i> Edit Order'), array('controller' => 'salesorders', 'action' => 'edit', '{{line.Order.id}}'),array('escape'=> false))); ?>
                                                    </li>
                                                    <li ng-if="line.Order.status_id == 14 && is_order_write(line)">
                                                        <a href ng-click="deleteOrder(line.Order.id)"><i class="fa fa-trash-o"></i> Delete</a>
                                                    </li>
                                                    <li ng-if="line.Order.status_id == 4 && is_order_write(line)">
                                                        <a href ng-click="cancelOrder(line.Order.id)"><i class="fa fa-ban"></i> Cancel Order</a>
                                                    </li>
                                                    <li ng-if="(line.Order.status_id == 4 || line.Order.status_id == 3) && is_order_write(line)">
                                                        <a href ng-click="toDraftOrder(line.Order.id)"><i class="fa fa-unlock"></i> Change to Draft</a>
                                                    </li>
                                                    <li ng-if="is_order_write(line)">
                                                        <a href ng-click="addDocument(line.Order.id)"><i class="fa fa-upload"></i> Document</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr ng-repeat-end class="hide1">
                                        <td colspan="8" ng-if="line.OrderLines" style="padding: 0;font-size:90%">
                                            <table class="table table-condensed table-light" style="margin: 0px">
                                                <tr class="bg-grey">
                                                    <th>&nbsp;</th>
                                                    <th>Line No.</th>
                                                    <th>SKU</th>
                                                    <th>Product</th>
                                                    <th>Inventory Location</th>
                                                    <th>Quantity</th>
                                                    <th>Issue Qty.</th>
                                                    <th>Unit Price</th>
                                                    <th>Total</th>
                                                    <th>Remarks</th>
                                                </tr>
                                                <tr ng-repeat="(key, orderline) in line.OrderLines">
                                                    <td class="text-right">
                                                        <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                                    </td>
                                                    <td>
                                                        <span ng-bind-html="lineStatus(orderline)"></span> {{orderline.OrdersLine.line_number}}
                                                    </td>
                                                    <td>{{orderline.OrdersLine.sku}}</td>
                                                    <td><a href="<?php echo $this->Html->url(['controller' => 'inventories', 'action' => 'index']);?>?product={{orderline.Product.id}}">{{orderline.Product.name}}</a></td>
                                                    <td>{{orderline.Warehouse.name}}</td>
                                                    <td>{{orderline.OrdersLine.quantity}}</td>
                                                    <td>{{orderline.OrdersLine.sentqty}}</td>
                                                    <td>
                                                        <div ng-if="line.OrdersLine.type != 7">
                                                            {{currency.csymb}}{{ displayPrice(orderline.OrdersLine.unit_price) }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div ng-if="line.OrdersLine.type != 7">
                                                            {{currency.csymb}}{{ displayPrice(orderline.OrdersLine.total_line) }}
                                                        </div>
                                                    </td>
                                                    <td>{{orderline.OrdersLine.comments}}</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr ng-show="$data.length == 0">
                                        <td colspan="8"> 
                                            <h3 class="text-warning text-center">There's no orders found</h3>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <pagination max-size="10" total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="limit"></pagination>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="bs-callout bs-callout-info">
                                    <span class="font-blue-steel help">
                                        <i class="fa fa-info" aria-hidden="true"></i>
                                        <a href="https://delivrd.freshdesk.com/support/solutions/articles/17000013491-managing-sales-orders-in-delivrd" target="_blank">Managing Sales Orders in Delivrd Tutorials</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                    <div class="portlet-body">
                        <div class="alert alert-danger">
                            <p class="lead text-center">
                                You do not have access to sales orders.
                                <?php if(!$authUser['is_limited']) { ?>
                                <?php echo $this->Html->link(
                                        __('Start trial'),
                                        array('plugin' => false, 'controller' => 'user', 'action' => 'start_trial', '?' => ['type' => 'so']),
                                        array('style' => 'display:none', 'id'=>'startSoTrial', 'escape' => false, 'data-toggle' => 'modal', 'data-target' => '#ajaxModal')
                                    );
                                ?>
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <!-- End: life time stats -->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var prev = 10;
    var popup = '<?php echo $popup; ?>';
    var is_write = '<?php echo $is_write ?>';
    var networks = <?php echo json_encode($networks) ?>;
    var ship_networks = <?php echo json_encode($ship_networks) ?>;

    var productId = <?php echo $product_id; ?>;
<?php
    $settings = json_decode($authUser['settings'], true);
    if(isset($settings['so_filter'])) {
        $filter = $settings['so_filter'];
    } else {
        $filter = [];
    }
?>
    var status_id = <?php echo json_encode($filter); //(isset($this->params['named']['status_id'])?json_encode($this->params['named']['status_id']):json_encode([])); ?>;
    var schannel_id = '<?php echo (isset($this->params['named']['schannel_id'])?$this->params['named']['schannel_id']:''); ?>';
    var searchby = '<?php echo (isset($this->params['named']['searchby'])?$this->params['named']['searchby']:''); ?>';
    var limit = '<?php echo (isset($this->params['named']['limit'])?$this->params['named']['searchby']: $limit); ?>';
    var userUid = '<?php echo $authUser['id']; ?>';

    var doc_title = 'Sales Order';

    $(document).ready(function() {

        <?php if(!$is_have_access) { ?>
            $('#startSoTrial').trigger('click');
        <?php } ?>
            
        $('#OrderSchannelId').select2({
            placeholder: 'Select Channel',
            minimumResultsForSearch: -1
        });

        $('#OrderStatusId').select2({
            placeholder: 'Select..',
            minimumResultsForSearch: -1
        });

        $('select.limit').select2({
            minimumResultsForSearch: -1,
            width: '80px'
        });
    });
<?php $this->Html->scriptEnd(); ?>

<?php echo $this->Html->script('/app/SalesOrders/index.js?v=0.0.73', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/app/Documents/view.js?v=0.0.2', array('block' => 'pageBlock')); ?>

<?php function ang($str) {
    $str = str_replace('%7B%7B', '{{', $str);
    $str = str_replace('%7D%7D', '}}', $str);
    return $str;
}?>