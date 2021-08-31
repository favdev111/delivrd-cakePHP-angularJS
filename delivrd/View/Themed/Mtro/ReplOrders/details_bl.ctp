<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="SalesOrderDetails" <?php echo ($this->request->query('new') ? 'ng-init="addLine()"':'') ?>>
    <div class="page-content">
            
        <!-- BEGIN PAGE HEADER-->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li><i class="fa fa-home"></i> <?php echo $this->Html->link(__('Home'), '/'); ?></li>
                <li><i class="fa fa-angle-right"></i></li>
                <li><?php echo $this->Html->link(__('Purchase Orders'), array('plugin' => false, 'controller'=> 'replorders'),array('escape'=> false)); ?></li>
            </ul>
            
            <div class="page-toolbar">
                <div class="btn-group pull-right">

                    <?php echo $this->Html->link(__('<i class="fa fa-unlock"></i> Release'), array('action' => 'release',$order['Order']['id'], '?' => ['f' => 'd']), array('escape'=> false, 'class' => 'btn btn-fit-height blue', 'ng-if' => 'is_write && order.Order.status_id == 14', 'style' => 'margin-right: 13px;')); ?>
                    <?php #echo $this->Html->link(__('<i class="fa fa-arrow-right"></i> Receive'), array('controller' => 'orders_lines', 'action' => 'receive', $order['Order']['id']), array('escape'=> false, 'class'=>'btn btn-fit-height green-jungle', 'ng-if' => 'is_write && (order.Order.status_id == 2 || order.Order.status_id == 3)', 'style' => 'margin-right: 13px;')); ?>

                    <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                        &nbsp;<i class="fa fa-ellipsis-h"></i>&nbsp;
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li ng-if="order.Order.status_id == 14 && is_write"><?php echo $this->Html->link(__('<i class="fa fa-unlock"></i> Release'), array('action' => 'release',$order['Order']['id'], '?' => ['f' => 'd']), array('escape'=> false)); ?></li>
                        <li ng-if="order.Order.status_id == 14 && is_write"><?php echo $this->Html->link(__('<i class="fa fa-pencil"></i> Edit Order'), array('controller' => 'replorders', 'action' => 'edit', $order['Order']['id']),array('escape'=> false)); ?></li>
                        <?php if($this->Session->read('Auth.User.id') == $order['Order']['user_id']) { ?>
                        <li ng-if="order.Order.status_id == 14 && is_write"><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $order['Order']['id']), array('escape'=> false), __('Are you sure you want to delete shipment # %s?', $order['Order']['id'])); ?></li>
                        <?php } ?>
        
                        <li ng-if="order.Order.status_id == 2 && is_write"><?php echo $this->Html->link(__('<i class="fa fa-undo"></i> Cancel Release'), array('action' => 'unrelease',$order['Order']['id'], '?' => ['f' => 'd']), array('escape'=> false)); ?></li>
                        <li ng-if="order.Order.status_id == 2 && is_write"><?php echo $this->Html->link(__('<i class="fa fa-print"></i> Print Purchase Order'), array('controller' => 'orders', 'action' => 'purchaseorderform',$order['Order']['id'],2), array('escape'=> false));?></li>
                        <?php if($this->Session->read('is_admin') == 3 || $this->Session->read('is_admin') == 1) { ?>
                            <li ng-if="order.Order.status_id == 2 && is_write"><?php echo $this->Html->link(__('<i class="fa fa-truck"></i> Create Shipment'), array('controller' => 'shipments', 'action' => 'add',$order['Order']['id']), array('escape'=> false)); ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END PAGE HEADER-->
        
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <!-- Begin: life time stats -->
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-random"></i>Blanket Order #<?php echo h($order['Order']['id']) ?> <span class="hidden-480">
                            | Created <?php echo date("F j, Y, g:i a",strtotime($order['Order']['created'])); ?></span>
                        </div>
                        <div class="actions">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                            <div class="btn-group"></div>
                        </div>
                    </div>

                    <div class="portlet-body">
                        <div class="tabbable">
                            <ul class="nav nav-tabs nav-tabs-lg">
                                <li class="active"><a href="#tab_1" data-toggle="tab">Details </a></li>
                                <?php if($this->Session->read('is_admin') == 1) { ?>
                                <li><a href="#tab_2" data-toggle="tab">Shipments <span class="badge badge-success"><?php echo (isset($order['Shipment'][0]['id']) ? "1" : "0"); ?></span></a></li>
                                <li><a href="#tab_history" data-toggle="tab">History</a></li>
                                <?php } ?>
                            </ul>
                            
                            <div class="tab-content">
                                    <div class="tab-pane active" id="tab_1">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="portlet grey-gallery box">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-cogs"></i> Order Details
                                                        </div>
                                                        <div class="actions" ng-if="is_write && order.Order.status_id == 14">
                                                            <button class="btn btn-circle btn-icon-only btn-default" ng-click="editDetails()"><i class="fa fa-wrench"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                Order #:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                {{order.Order.id}}
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                Order Date &amp; Time:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                {{formatDate(order.Order.created) | date:'MMMM d, yyyy, h:mm a'}}
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                Reference Order:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                {{order.Order.external_orderid}}
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                Supplier
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                {{order.Supplier.name}}
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                Shipping Costs ({{currency.name}}):
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                {{currency.csymb}}{{ displayPrice(order.Order.shipping_costs) }}
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                Estimated Delivery Date:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                {{formatDate(order.Order.requested_delivery_date) | date:'MMMM d, yyyy'}}
                                                            </div>
                                                        </div>
                        
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                Order Status:
                                                            </div>
                                                            <div class="col-md-7 value" ng-bind-html="status(order.Order.status_id)">
                                                                {{order.Order.status_id}}
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="col-md-6 col-sm-12">
                                                <div class="portlet grey-gallery box">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-truck"></i> Shipping Address
                                                        </div>
                                                        <div class="actions" ng-if="is_write && order.Order.status_id == 14">
                                                            <button class="btn btn-circle btn-icon-only btn-default" ng-click="editShipping()"><i class="fa fa-wrench"></i></button>
                                                        </div>
                                                    </div>  
                                                    <div class="portlet-body">
                                                        <div class="portlet-body">
                                                            <div class="row static-info">
                                                                <div class="col-md-5 name">
                                                                    Name
                                                                </div>
                                                                <div class="col-md-7 value">
                                                                    {{order.Order.ship_to_customerid}}
                                                                </div>
                                                            </div>
                                                            <div class="row static-info">
                                                                <div class="col-md-5 name">
                                                                    Address
                                                                </div>
                                                                <div class="col-md-7 value">
                                                                    {{order.Address.street ? order.Address.street : order.Order.ship_to_street}}
                                                                </div>
                                                            </div>
                                                            <div class="row static-info">
                                                                <div class="col-md-5 name">
                                                                    City
                                                                </div>
                                                                <div class="col-md-7 value">
                                                                    {{order.Address.city ? order.Address.city : order.Order.ship_to_city}}
                                                                </div>
                                                            </div>
                                                            <div class="row static-info">
                                                                <div class="col-md-5 name">
                                                                    State/Province
                                                                </div>
                                                                <div class="col-md-7 value">
                                                                    {{ order.Address.stateprovince ? order.Address.stateprovince : (order.Order.ship_to_stateprovince ? order.Order.ship_to_stateprovince : (order.Order.state_id != 0 ? order.Order.state_id : '') )}}
                                                                </div>
                                                            </div>
                                                            <div class="row static-info">
                                                                <div class="col-md-5 name">
                                                                    State
                                                                </div>
                                                                <div class="col-md-7 value">
                                                                    {{order.Address.State.name ? order.Address.State.name : order.State.name}}
                                                                </div>
                                                            </div>
                                                            <div class="row static-info">
                                                                <div class="col-md-5 name">
                                                                    Zip
                                                                </div>
                                                                <div class="col-md-7 value">
                                                                    {{order.Address.zip ? order.Address.zip : order.Order.ship_to_zip}}
                                                                </div>
                                                            </div>
                                                            <div class="row static-info">
                                                                <div class="col-md-5 name">
                                                                    Country
                                                                </div>
                                                                <div class="col-md-7 value">
                                                                    {{order.Address.Country.name ? order.Address.Country.name : order.Country.name}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <div class="portlet grey-cascade box">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-cogs"></i>Order Lines
                                                        </div>
                                                        <div class="actions" ng-if="is_write && order.Order.status_id == 14">
                                                            <button class="btn btn-primary" ng-click="addLine()" ng-if="blanket.length == 0 "><i class="fa fa-plus"></i> Add Order Lines</button>
                                                        </div>
                                                        <div class="actions" ng-if="is_write && (order.Order.status_id == 2 || order.Order.status_id == 3)">
                                                            <?php #echo $this->Html->link(__('<i class="fa fa-shopping-cart"></i> Receive All Products'), array('controller' => 'orders_lines','action' => 'receivealllines', $order['Order']['id']), array('escape'=> false, 'class' => 'btn btn-primary')); ?>
                                                            <button class="btn btn-primary" ng-click="completeOrder(<?php echo $order['Order']['id']; ?>)"><i class="fa fa-flag-checkered"></i> Complete Order Processing</button>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>OrderLine</th>
                                                                    <th>Product</th>
                                                                    <th>Warehouse</th>
                                                                    <th>Quantity</th>
                                                                    <th>Unit Price</th>
                                                                    <th>Total</th>
                                                                    <th>Remarks</th>
                                                                    <th ng-if="order.Order.status_id != 4 && is_write">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr ng-if="blanket">
                                                                    <td>10</td>
                                                                    <td><a href="<?php echo $this->Html->url(['controller' => 'inventories', 'action' => 'index']);?>?product={{blanket.Product.id}}">{{blanket.Product.name}}</a></td>
                                                                    <td>{{blanket.Warehouse.name}}</td>
                                                                    <td>{{blanket.OrdersBlanket.quantity}}</td>
                                                                    <td>
                                                                        <div ng-if="line.OrdersLine.type != 7">
                                                                            {{currency.csymb}}{{ displayPrice(blanket.OrdersBlanket.unit_price) }}
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div ng-if="line.OrdersLine.type != 7">
                                                                            {{currency.csymb}}{{ displayPrice(blanket.OrdersBlanket.total_line) }}
                                                                        </div>
                                                                    </td>
                                                                    <td>{{blanket.OrdersBlanket.comments}}</td>
                                                                    <td ng-if="order.Order.status_id != 4 && is_write">
                                                                        <div ng-if="is_writeable(blanket.Warehouse.id)">
                                                                            <button ng-if="order.Order.status_id == '14'" 
                                                                                class="btn btn-sm default btn-editable"
                                                                                ng-click="openEditRow(blanket.OrdersBlanket.id)">
                                                                                <i class="fa fa-edit"></i> Edit
                                                                            </button>
                                                                            <button ng-if="order.Order.status_id == '14'" 
                                                                                class="btn btn-sm default btn-editable"
                                                                                confirmed-click="removeLine(blanket.OrdersBlanket.id)"
                                                                                ng-confirm-click="Are you sure you want to delete this order line number?">
                                                                                <i class="fa fa-trash-o"></i> Delete
                                                                            </button>
                                                                            <button ng-if="order.Order.status_id == '2' || order.Order.status_id == '3'"
                                                                                class="btn btn-sm green btn-editable"
                                                                                ng-click="openReceive(blanket.OrdersBlanket.id)">
                                                                                <span class="badge">{{blanket.OrdersBlanket.receivedqty}}</span> <i class="fa fa-arrow-left"></i> Receive Order Products
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr ng-repeat="line in lines | orderBy:sortType:sortReverse" id="line_{{line.OrdersLine.id}}">
                                                                    <td>{{line.OrdersLine.line_number}}</td>
                                                                    <td><a href="<?php echo $this->Html->url(['controller' => 'inventories', 'action' => 'index']);?>?product={{line.Product.id}}">{{line.Product.name}}</a></td>
                                                                    <td>{{line.Warehouse.name}}</td>
                                                                    <td>{{line.OrdersLine.quantity}}</td>
                                                                    <td>
                                                                        <div ng-if="line.OrdersLine.type != 7">
                                                                            {{currency.csymb}}{{ displayPrice(line.OrdersLine.unit_price) }}
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div ng-if="line.OrdersLine.type != 7">
                                                                            {{currency.csymb}}{{ displayPrice(line.OrdersLine.total_line) }}
                                                                        </div>
                                                                    </td>
                                                                    <td>{{line.OrdersLine.comments}}</td>
                                                                    <td>
                                                                        {{formatDate(line.OrdersLine.modified) | date:'MMMM d, yyyy, h:mm a'}}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-offset-6 col-md-6">
                                                <div class="well">
                                                    <div class="row static-info align-reverse" style="border-bottom: 1px solid #dcdcdc;padding-bottom: 10px;">
                                                        <div class="col-md-8 name">Total:</div>
                                                        <div class="col-md-3 value">
                                                            {{currency.csymb}}{{ displayPrice(total.blanket_total) }}
                                                        </div>
                                                    </div>

                                                    <div class="row static-info align-reverse">
                                                        <div class="col-md-8 name">Received Total:</div>
                                                        <div class="col-md-3 value">
                                                            {{currency.csymb}}{{ displayPrice(total.linestotal) }}
                                                        </div>
                                                    </div>
                                                    <div class="row static-info align-reverse">
                                                        <div class="col-md-8 name">Shipping:</div>
                                                        <div class="col-md-3 value">
                                                            {{currency.csymb}}{{ displayPrice(total.shipping) }}
                                                        </div>
                                                    </div>
                                                    <div class="row static-info align-reverse">
                                                        <div class="col-md-8 name">Grand Total:</div>
                                                        <div class="col-md-3 value">
                                                            {{currency.csymb}}{{ displayPrice(total.grand) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php  if($this->Session->read('is_admin') == 1) { ?>
                                    <div class="tab-pane" id="tab_2">
                                        <div class="table-container">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr role="row" class="heading">
                                                        <th>Shipment</th>
                                                        <th>Tracking Number</th>
                                                        <th>Created</th>                                               
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr role="row" class="filter">
                                                        <?php if(isset($order['Shipment'][0]['id'])) { ?>
                                                        <td><?php echo $this->Html->link(__($order['Shipment'][0]['id']), array('controller' => 'shipments', 'action' => 'view',$order['Shipment'][0]['id']), array('escape'=> false)); ?></td>
                                                        <td><?php echo h($order['Shipment'][0]['tracking_number']); ?></td>
                                                        <td><?php echo h($order['Shipment'][0]['created']); ?></td>
                                                        <?php } ?>                                                     
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="tab_history">
                                        <div class="table-container">
                                            <table class="table table-hover dataTable no-footer" id="datatable_history" role="grid">
                                                <thead>
                                                    <tr role="row" class="heading">
                                                        <th width="25%">Datetime</th>
                                                        <th width="55%">Description</th>
                                                        <th width="10%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($objectevents as $key => $objectevent) { ?>
                                                    <tr role="row" class="<?php echo ($key % 2 == 0 ? "odd" : "even"); ?>">
                                                        <td class="sorting_1"><?php echo $objectevent['Event']['created']; ?></td>
                                                        <td><?php echo "Order status changed to ".$objectevent['Status']['name']; ?></td>
                                                        <td></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                
                                    
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End: life time stats -->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var order = <?php echo json_encode($order); ?>;
    var blanket = <?php echo json_encode($blanket); ?>;
    var is_write = <?php echo $is_write; ?>;
    var lines = <?php echo json_encode($orders_lines, JSON_NUMERIC_CHECK) ?>;
    var total = <?php echo json_encode($ordertotals); ?>;
    var currency = <?php echo json_encode($currency['Currency']); ?>;
    var warehouse = <?php echo json_encode($warehouses); ?>;

    var orderId = <?php echo $order['Order']['id']; ?>;
<?php $this->Html->scriptEnd(); ?>
</script>

<?php echo $this->Html->script('/app/ReplOrders/details_bl.js?v=0.0.7', array('block' => 'pageBlock')); ?>