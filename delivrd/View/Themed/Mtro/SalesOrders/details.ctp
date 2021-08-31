<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="SalesOrderDetails" <?php echo ($this->request->query('new') ? 'ng-init="open(1)"':'') ?>>
    <div class="page-content">
            
        <?php /*<!-- BEGIN PAGE HEADER-->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li><i class="fa fa-home"></i> <?php echo $this->Html->link(__('Home'), '/'); ?></li>
                <li><i class="fa fa-angle-right"></i></li>
                <li ng-if="order.Order.status_id != 50"><?php echo $this->Html->link(__('Sales Orders'), array('plugin' => false, 'controller'=> 'salesorders'),array('escape'=> false)); ?></li>
                <li ng-if="order.Order.status_id == 50"><?php echo $this->Html->link(__('Canceled Sales Orders'), array('plugin' => false, 'controller'=> 'salesorders', 'action' => 'canceled'),array('escape'=> false)); ?></li>
            </ul>
            
            <div class="page-toolbar">
                <div class="btn-group pull-right" ng-if="order.Order.status_id != 50">

                    <?php echo $this->Html->link(__('<i class="fa fa-unlock"></i> Release'), array('action' => 'release',$order['Order']['id'], '?' => ['f' => 'd']), array('escape'=> false, 'class' => 'btn btn-fit-height blue', 'ng-if' => 'is_write && order.Order.status_id == 14', 'style' => 'margin-right: 13px;')); ?>
                    <?php echo $this->Html->link(__('<i class="fa fa-arrow-left"></i> Issue'), array('controller' => 'orders_lines', 'action' => 'issue',$order['Order']['id']), array('escape'=> false, 'class'=>'btn btn-fit-height green-jungle', 'ng-if' => 'is_write && (order.Order.status_id == 2 || order.Order.status_id == 3)', 'style' => 'margin-right: 13px;')); ?>

                    <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                        &nbsp;<i class="fa fa-ellipsis-h"></i>&nbsp;
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li ng-if="order.Order.status_id == 14"><?php echo $this->Html->link(__('<i class="fa fa-pencil"></i> Edit Order'), array('controller' => 'salesorders', 'action' => 'edit', $order['Order']['id']),array('escape'=> false)); ?></li>
                        <li ng-if="order.Order.status_id == 14"><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('controller'=>'orders', 'action' => 'delete', $order['Order']['id'], '?'=>['f' => 'd']), array('escape'=> false), __('Are you sure you want to delete shipment # %s?', $order['Order']['id'])); ?></li>
                        <li ng-if="order.Order.status_id == 14"><?php echo $this->Html->link('<i class="fa fa-globe"></i> Validate Address','http://maps.google.com/maps?q='.h($addr),array('target' => '_blank','escape'=> false)); ?></li>
                        <li ng-if="order.Order.status_id == 2"><?php echo $this->Html->link(__('<i class="fa fa-undo"></i> Cancel Order Release'), array('action' => 'unrelease',$order['Order']['id'], '?'=>['f' => 'd']), array('escape'=> false)); ?></li>
                        <li ng-if="order.Order.status_id != 14"><?php echo $this->Html->link(__('<i class="fa fa-print"></i> Print Packing Slip'), array('controller' => 'salesorders', 'action' => 'printslip',$order['Order']['id']), array('escape'=> false)); ?></li>
                        <?php if(($this->Session->read('is_admin') == 3 || $this->Session->read('is_admin') == 1) && $is_shipment) { ?>
                        <li ng-if="order.Order.status_id == 2"><?php echo $this->Html->link(__('<i class="fa fa-truck"></i> Create Shipment'), array('controller' => 'shipments', 'action' => 'add',$order['Order']['id'],1),array('escape'=> false)); ?></li>
                        <?php } ?>

                        <?php if($order['Shipment']) { ?>
                        <li ng-if="order.Order.status_id == 3"><?php echo $this->Html->link(__('<i class="icon-rocket"></i> Shipment Details'), array('controller' => 'shipments', 'action' => 'view',$order['Shipment'][0]['id']), array('escape'=> false)); ?></li>
                        <?php } ?>
                        <?php if($this->Session->read('Auth.User.id') == $order['Order']['user_id']) { ?>
                        <li><?php echo $this->Html->link('<i class="icon-shuffle"></i> Create P.O.', array('action' => 'createrep', $order['Order']['id']), array('escape' => false)); ?></li>
                        <?php } ?>
                        <li ng-if="(order.Order.status_id == 4 || order.Order.status_id == 3)"><a href ng-click="toDraftOrder(order.Order.id)"><i class="fa fa-unlock"></i> Change to Draft</a></li>
                    </ul>
                </div>

                <div class="btn-group pull-right" ng-if="order.Order.status_id == 50">
                    <?php echo $this->Html->link(__('<i class="fa fa-check"></i> Restore'), array('action' => 'restore', $order['Order']['id'], '?' => ['f' => 'd']), array('escape'=> false, 'class' => 'btn btn-fit-height blue')); ?>
                </div>
            </div>
        </div>
        <!-- END PAGE HEADER-->*/ ?>
        
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <!-- Begin: life time stats -->
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="actions">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                            <div class="btn-group">                                                                         
                            </div>

                            <div class="btn-group pull-right" ng-if="order.Order.status_id != 50"  style="margin-left: 10px;">

                                <?php echo $this->Html->link(__('<i class="fa fa-unlock"></i> Release'), array('action' => 'release',$order['Order']['id'], '?' => ['f' => 'd']), array('escape'=> false, 'class' => 'btn btn-fit-height blue', 'ng-if' => 'is_write && order.Order.status_id == 14', 'style' => 'margin-right: 13px;')); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-arrow-left"></i> Issue'), array('controller' => 'orders_lines', 'action' => 'issue',$order['Order']['id']), array('escape'=> false, 'class'=>'btn btn-fit-height green-jungle', 'ng-if' => 'is_write && (order.Order.status_id == 2 || order.Order.status_id == 60 || order.Order.status_id == 3)', 'style' => 'margin-right: 13px;')); ?>

                                <a href ng-if="is_write && order.Order.status_id == 4" style="margin-right: 13px" ng-click="makePaid(<?php echo $order['Order']['id']; ?>)" class="btn btn-fit-height green-jungle"><i class="fa fa-credit-card"></i> Paid</a>

                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    &nbsp;<i class="fa fa-ellipsis-h"></i>&nbsp;
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li ng-if="order.Order.status_id == 14"><?php echo $this->Html->link(__('<i class="fa fa-pencil"></i> Edit Order'), array('controller' => 'salesorders', 'action' => 'edit', $order['Order']['id']),array('escape'=> false)); ?></li>
                                    <li ng-if="order.Order.status_id == 14"><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('controller'=>'orders', 'action' => 'delete', $order['Order']['id'], '?'=>['f' => 'd']), array('escape'=> false), __('Are you sure you want to delete shipment # %s?', $order['Order']['id'])); ?></li>
                                    <li ng-if="order.Order.status_id == 14"><?php echo $this->Html->link('<i class="fa fa-globe"></i> Validate Address','http://maps.google.com/maps?q='.h($addr),array('target' => '_blank','escape'=> false)); ?></li>
                                    <li ng-if="order.Order.status_id == 2 || order.Order.status_id == 60"><?php echo $this->Html->link(__('<i class="fa fa-undo"></i> Cancel Order Release'), array('action' => 'unrelease',$order['Order']['id'], '?'=>['f' => 'd']), array('escape'=> false)); ?></li>
                                    <li ng-if="1 || (order.Order.status_id != 14 && order.Order.status_id != 4 && order.Order.status_id != 55)"><?php echo $this->Html->link(__('<i class="fa fa-print"></i> Print Packing Slip'), array('controller' => 'salesorders', 'action' => 'printslip',$order['Order']['id']), array('escape'=> false)); ?></li>
                                    <?php if(($this->Session->read('is_admin') == 3 || $this->Session->read('is_admin') == 1) && $is_shipment) { ?>
                                    <li ng-if="order.Order.status_id == 2 || order.Order.status_id == 60 || order.Order.status_id == 3"><?php echo $this->Html->link(__('<i class="fa fa-truck"></i> Create Shipment'), array('controller' => 'shipments', 'action' => 'add',$order['Order']['id'],1),array('escape'=> false)); ?></li>
                                    <?php } ?>

                                    <?php if($order['Shipment']) { ?>
                                    <li ng-if="order.Order.status_id == 3"><?php echo $this->Html->link(__('<i class="icon-rocket"></i> Shipment Details'), array('controller' => 'shipments', 'action' => 'view',$order['Shipment'][0]['id']), array('escape'=> false)); ?></li>
                                    <?php } ?>
                                    <?php if($this->Session->read('Auth.User.id') == $order['Order']['user_id']) { ?>
                                    <li><?php echo $this->Html->link('<i class="icon-shuffle"></i> Create P.O.', array('action' => 'createrep', $order['Order']['id']), array('escape' => false)); ?></li>
                                    <?php } ?>
                                    <li ng-if="(order.Order.status_id == 4 || order.Order.status_id == 3)"><a href ng-click="toDraftOrder(order.Order.id)"><i class="fa fa-unlock"></i> Change to Draft</a></li>
                                </ul>
                            </div>

                            <div class="btn-group pull-right" ng-if="order.Order.status_id == 50">
                                <?php echo $this->Html->link(__('<i class="fa fa-check"></i> Restore'), array('action' => 'restore', $order['Order']['id'], '?' => ['f' => 'd']), array('escape'=> false, 'class' => 'btn btn-fit-height blue')); ?>
                            </div>

                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="tabbable">
                            <ul class="nav nav-tabs nav-tabs-lg">
                                <li class="active"><a href="#tab_1" data-toggle="tab">Details</a></li>
                                <?php if($this->Session->read('is_admin') == 1) { ?>
                                <li><a href="#tab_2" data-toggle="tab">Shipments <span class="badge badge-success"><?php echo (isset($order['Shipment'][0]['id']) ? "1" : "0"); ?></span></a></li>
                                <li><a href="#tab_history" data-toggle="tab">History</a></li>
                                <?php } ?>
                            </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_1">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="portlet yellow-crusta box">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-cogs"></i>Sales Order Details
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
                                                                Order Date & Time:
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
                                                                Additional Reference Order:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                {{order.Order.external_orderid2}}
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                Sales Channel
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                {{order.Schannel.name}}
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
                                                                Requested Date:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                <span ng-if="order.Order.requested_delivery_date">
                                                                    {{formatDate(order.Order.requested_delivery_date) | date:'MMMM d, yyyy'}}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                Remarks:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                {{order.Order.comments}}
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

                                                        <div class="row static-info" ng-if="order.Order.status_id == 55">
                                                            <div class="col-md-5 name">
                                                                Payment Date:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                {{formatDate(order.Order.payment_date) | date:'MMMM d, yyyy'}}<br>
                                                                <i style="font-weight: 200;white-space: pre-line">{{order.Order.payment_text}}</i>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row static-info">
                                                            <div class="col-md-12 text-right">
                                                                <a href ng-click="addDocument(<?php echo $order['Order']['id']; ?>)" class="btn btn-primary"><i class="fa fa-upload"></i> Documents</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="col-md-6 col-sm-12">
                                                <div class="portlet red-sunglo box">
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
                                                                    Email
                                                                </div>
                                                                <div class="col-md-7 value">
                                                                    {{order.Order.email}}
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
                                                            <div class="row static-info">
                                                                <div class="col-md-5 name">
                                                                    Phone Number
                                                                </div>
                                                                <div class="col-md-7 value">
                                                                    {{order.Address.phone ? order.Address.phone : order.Order.ship_to_phone}}
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
                                                            <button class="btn btn-primary" ng-click="open(1)"><i class="fa fa-plus"></i> Add Order Lines</button>
                                                        </div>
                                                        <div class="actions" ng-if="is_write && (order.Order.status_id == 2 || order.Order.status_id == 60 || order.Order.status_id == 3 || order.Order.status_id == 8)">
                                                            <button class="btn btn-primary" ng-click="issueAllLines(<?php echo $order['Order']['id']; ?>, 0)"><i class="fa fa-arrow-left"></i> Issue All Products</button>
                                                            <button class="btn green-jungle" ng-click="issueAllLines(<?php echo $order['Order']['id']; ?>, 1)"><i class="fa fa-arrow-left"></i> Issue All Products &amp; Complete</button>
                                                            <button class="btn green-jungle" ng-click="completeOrder(<?php echo $order['Order']['id']; ?>)"><i class="fa fa-flag-checkered"></i> Complete Order Processing</button>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>OrderLine</th>
                                                                    <th>SKU</th>
                                                                    <th>Product</th>
                                                                    <th>Warehouse</th>
                                                                    <th>Quantity</th>
                                                                    <th>Unit Price</th>
                                                                    <th>Total</th>
                                                                    <th>Remarks</th>
                                                                    <th ng-if="order.Order.status_id != 4 && order.Order.status_id != 50  && is_write">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr ng-repeat="line in lines | orderBy:sortType:sortReverse" id="line_{{line.OrdersLine.id}}">
                                                                    <td>{{line.OrdersLine.line_number}}</td>
                                                                    <td>{{line.OrdersLine.sku}}</td>
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
                                                                    <td ng-if="order.Order.status_id != 4 && order.Order.status_id != 50 && is_write">
                                                                        <div ng-if="is_writeable(line.Warehouse.id)">
                                                                            <button ng-if="line.OrdersLine.type != 4 && order.Order.status_id != 3 && order.Order.status_id != '2' && order.Order.status_id != '60'" 
                                                                                class="btn btn-sm default btn-editable"
                                                                                ng-click="openEditRow(line.OrdersLine.id)">
                                                                                <i class="fa fa-edit"></i> Edit
                                                                            </button>
                                                                            <button ng-if="line.OrdersLine.status_id != 8 && line.OrdersLine.status_id != 4 && order.Order.status_id != '2' && order.Order.status_id != '60' && order.Order.status_id != '3'" 
                                                                                class="btn btn-sm default btn-editable"
                                                                                confirmed-click="removeLine(line.OrdersLine.id)"
                                                                                ng-confirm-click="Are you sure you want to delete this order line number?">
                                                                                <i class="fa fa-trash-o"></i> Delete
                                                                            </button>
                                                                            <button ng-if="order.Order.status_id == '2' || order.Order.status_id == '3' || order.Order.status_id == '60'"
                                                                                class="btn btn-sm green btn-editable"
                                                                                ng-click="openIssue(line.OrdersLine.id)">
                                                                                <span class="badge">{{line.OrdersLine.sentqty}}</span> <i class="fa fa-arrow-left"></i> Issue Order Products
                                                                            </button>
                                                                        </div>
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
                                                    <div class="row static-info align-reverse" style="margin-bottom: 0px">
                                                        <div class="col-md-8 name">Sub Total:</div>
                                                        <div class="col-md-3 value">
                                                            {{currency.csymb}}{{ displayPrice(total.linestotal) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-sm-12">
                                                <div class="portlet grey-cascade box">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-cogs"></i>Additional Costs/Discount
                                                        </div>
                                                        <div class="actions" ng-if="is_write && order.Order.status_id == 14">
                                                            <button class="btn btn-primary" ng-click="additionalCosts(<?php echo $order['Order']['id']; ?>)"><i class="fa fa-plus"></i> Add Costs/Discount</button>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Type</th>
                                                                    <th>Amount</th>
                                                                    <th>Remarks</th>
                                                                    <th ng-if="order.Order.status_id != 4 && order.Order.status_id != 50 && is_write && order.Order.status_id != 3 && order.Order.status_id != '2' && order.Order.status_id != '60'">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr ng-repeat="addcost in order_costs" id="addcost_{{addcost.OrdersCosts.id}}">
                                                                    <td>
                                                                        {{ addCostType(addcost.OrdersCosts.type) }}
                                                                        <span ng-if="addcost.OrdersCosts.type == 'discount' && addcost.OrdersCosts.uom == 'percentage'" class="text-danger">
                                                                            (- %{{displayPrice(addcost.OrdersCosts.amount)}})
                                                                        </span>
                                                                        <span ng-if="addcost.OrdersCosts.type == 'surchage' && addcost.OrdersCosts.uom == 'percentage'" class="text-success">
                                                                            (+ %{{displayPrice(addcost.OrdersCosts.amount)}})
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <span ng-if="addcost.OrdersCosts.type == 'discount'" class="text-danger">
                                                                            <span ng-if="addcost.OrdersCosts.uom == 'amount'">-{{currency.csymb}} {{ displayPrice(addcost.OrdersCosts.amount) }}</span>
                                                                            <span ng-if="addcost.OrdersCosts.uom == 'percentage'">-{{currency.csymb}} {{ displayPrice(countAmount(addcost.OrdersCosts.amount)) }}</span>
                                                                        </span>
                                                                        <span ng-if="addcost.OrdersCosts.type == 'surchage'">
                                                                            <span ng-if="addcost.OrdersCosts.uom == 'amount'">{{currency.csymb}} {{ displayPrice(addcost.OrdersCosts.amount) }}</span>
                                                                            <span ng-if="addcost.OrdersCosts.uom == 'percentage'">{{currency.csymb}} {{ displayPrice(countAmount(addcost.OrdersCosts.amount)) }}</span>
                                                                        </span>
                                                                        <span ng-if="addcost.OrdersCosts.type != 'discount' && addcost.OrdersCosts.type != 'surchage'">
                                                                            {{currency.csymb}} {{displayPrice(addcost.OrdersCosts.amount)}}
                                                                        </span>
                                                                    </td>
                                                                    <td>{{ addcost.OrdersCosts.comments }}</td>
                                                                    <td ng-if="order.Order.status_id != 4 && order.Order.status_id != 50 && is_write && order.Order.status_id != 3 && order.Order.status_id != '2' && order.Order.status_id != '60'">
                                                                        <button class="btn btn-sm default btn-editable" ng-click="deleteAddCost(addcost.OrdersCosts.id)"><i class="fa fa-trash-o"></i> Delete</button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                            <div class="col-md-offset-6 col-md-6">
                                                <div class="well">
                                                    <div class="row static-info align-reverse">
                                                        <div class="col-md-8 name">Shipping:</div>
                                                        <div class="col-md-3 value">
                                                            {{currency.csymb}}{{displayPrice(total.shipping)}}
                                                        </div>
                                                    </div>

                                                    <div class="row static-info align-reverse" style="border-top:1px solid #e4ceed;padding-top:7px;margin-bottom: 0px">
                                                        <div class="col-md-8 name "><strong>Grand Total:</strong></div>
                                                        <div class="col-md-3 value">
                                                            {{currency.csymb}}{{displayPrice(total.grand_new)}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if($this->Session->read('is_admin') == 1) { ?>
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

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var order = <?php echo json_encode($order); ?>;
    var order_costs = <?php echo json_encode($order_costs); ?>;
    var costs_types = <?php echo json_encode($costs_types); ?>;
    var orderId = <?php echo $order['Order']['id']; ?>;
    var is_write = <?php echo $is_write; ?>;
    var lines = <?php echo json_encode($orders_lines, JSON_NUMERIC_CHECK) ?>;
    var total = <?php echo json_encode($ordertotals); ?>;
    var currency = <?php echo json_encode($currency['Currency']); ?>;
    var warehouse = <?php echo json_encode($warehouses); ?>;

    var doc_title = 'Sales Order';
<?php $this->Html->scriptEnd(); ?>

<?php echo $this->Html->script('/app/SalesOrders/details.js?v=0.0.75', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/app/Documents/view.js?v=0.0.2', array('block' => 'pageBlock')); ?>