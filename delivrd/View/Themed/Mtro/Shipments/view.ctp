<?php
    if($shipment['Shipment']['direction_id'] == 1) {
        $viewordtext = "viewcord";
        $indexurl = "/shipments/index/1";
        $pagetext = "Outbound Shipments";
    } else {
        $viewordtext = "viewrord";
        $indexurl = "/shipments/index/2";
        $pagetext = "Inbound Shipments";
    }
?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <!-- Begin: life time stats -->
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            Shipment #<?php echo $shipment['Shipment']['id'] ?>
                            <span class="hidden-480">| <?php echo $this->Admin->localTime("%B %d, %Y, %I:%M %p", strtotime($shipment['Shipment']['created'])); ?></span>
                        </div>
                        <div class="actions">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                            <div class="btn-group"></div>

                            <div class="btn-group pull-right" style="margin-left: 10px;">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php if($is_write) { ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-edit"></i> Edit'), array('action' => 'edit',$shipment['Shipment']['id']), array('escape'=> false)); ?></li>
                                    <?php if ($shipment['Shipment']['status_id'] == 15 ) { ?>
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $shipment['Shipment']['id']), array('escape'=> false), __('Are you sure you want to delete shipment # %s?', $shipment['Shipment']['id'])); ?></li>
                                    <?php } ?>
                                    <?php } ?>
                                    <?php if ($shipment['Shipment']['status_id'] == 15 || $shipment['Shipment']['status_id'] == 16) { ?>
                                        <?php if($shipment['Order']['ordertype_id'] == 1 && $is_write) { ?>
                                        <li><a href="/orders_lines/issue/<?php echo $shipment['Shipment']['order_id'] ?>"><i class="fa fa-cube"></i> Issue </a></li>
                                        <?php } ?>
                                        <?php if($shipment['Order']['ordertype_id'] == 2 && $is_write) { ?>
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-arrow-left"></i> Receive Shipment Products'), array('action' => 'receive', $shipment['Shipment']['id']), array('escape'=> false)); ?></li>
                                        <?php } ?>
                                    <?php } ?>
                                    
                                    <?php if($shipment['Order']['ordertype_id'] == 1) { ?>
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print Shipping Label'), array('controller' => 'pdfs', 'action' => 'shiplabelee', $shipment['Shipment']['id']), array('escape'=> false)); ?></li>
                                    <?php } ?>

                                    <?php if ($shipment['Shipment']['status_id'] == 6) { ?>
                                        <?php if($is_write) { ?>
                                            <li><?php echo $this->Form->postLink(__('<i class="fa fa-undo"></i> Revert Pick & Packed'), array('action' => 'packuncomplete', $shipment['Order']['id']), array('escape'=> false)); ?></li>
                                            <li><?php echo $this->Form->postLink(__('<i class="fa fa-flag-checkered"></i> Complete shipment'), array('action' => 'ship', $shipment['Shipment']['id']), array('escape'=> false)); ?></li>
                                        <?php } ?>
                                    <?php } ?>
                                    
                                    <?php if ( ($shipment['Shipment']['status_id'] == 8 || $shipment['Shipment']['status_id'] == 7) && $is_write ) { ?>
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-undo"></i> Revert Complete shipment'), array('action' => 'uncomplete', $shipment['Shipment']['id']), array('escape'=> false)); ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="tabbable">
                            <ul class="nav nav-tabs nav-tabs-lg">
                                <li class="active"><a href="#tab_1" data-toggle="tab">Details</a></li>
                                <li><a href="#tab_history" data-toggle="tab">History</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="portlet yellow-crusta box">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i> Shipment Details
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">Shipment #:</div>
                                                        <div class="col-md-7 value">
                                                            <?php echo $shipment['Shipment']['id']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">Tracking Number</div>
                                                        <div class="col-md-7 value">
                                                            <?php echo h($shipment['Shipment']['tracking_number']);  ?>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">Status:</div>
                                                        <div class="col-md-7 value">
                                                            <?php echo h($shipment['Status']['name']); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">Shipping Costs(<?php echo h($this->Session->read('currencyname')); ?>):</div>
                                                        <div class="col-md-7 value">
                                                            <?php echo h($this->Session->read('currencysym')).h($shipment['Shipment']['shipping_costs']); ?>
                                                        </div>
                                                    </div>

                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">Remarks:</div>
                                                        <div class="col-md-7 value">
                                                            <?php h($shipment['Shipment']['notes']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="portlet red-sunglo box">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i> Shipping Address
                                                    </div>
                                                </div>

                                                <div class="portlet-body">
                                                    <div class="portlet-body">
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">Name</div>
                                                            <div class="col-md-7 value">
                                                                <?php echo h($order['Order']['ship_to_customerid']) ?>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">Address</div>
                                                            <div class="col-md-7 value">
                                                                <?php echo (!empty($order['Address']['street']) ? $order['Address']['street'] : $order['Order']['ship_to_street']);  ?>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">City</div>
                                                            <div class="col-md-7 value">
                                                                <?php echo (!empty($order['Address']['city']) ? $order['Address']['city'] : $order['Order']['ship_to_city']);  ?>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">State</div>
                                                            <div class="col-md-7 value">
                                                                <?php echo (!empty($order['Address']['stateprovince']) ? $order['Address']['stateprovince'] : $order['Order']['ship_to_stateprovince']);  ?>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">Zip</div>
                                                            <div class="col-md-7 value">
                                                                <?php echo (!empty($order['Address']['zip']) ? $order['Address']['zip'] : $order['Order']['ship_to_zip']);  ?>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">Country</div>
                                                            <div class="col-md-7 value">
                                                                <?php echo (!empty($order['Address']['Country']) ? $order['Address']['Country']['name'] : $order['Country']['name']);  ?>
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
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Order Number</th>
                                                                    <th>Line Number</th>
                                                                    <th>Product</th>
                                                                    <th>Unit Price</th>
                                                                    <th>Quantity</th>
                                                                    <th>Total</th>
                                                                    <th>Remarks</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($orders_lines as $ordersLine) { ?>
                                                                <tr>
                                                                    <td><?php echo $this->Html->link(__($ordersLine['OrdersLine']['order_id']), array('controller' => 'orders', 'action' => 'details', $ordersLine['OrdersLine']['order_id'])); ?></td>
                                                                    <td><?php echo h($ordersLine['OrdersLine']['line_number']); ?>&nbsp;</td>
                                                                    <td><?php echo $this->element('product_name', array('name' => $ordersLine['Product']['name'], 'id' => $ordersLine['Product']['id'])); ?></td>
                                                                    <td><?php echo h($this->Session->read('currencysym')).h($ordersLine['OrdersLine']['unit_price']).h($this->Session->read('currencyname')); ?>&nbsp;</td>
                                                                    <td><?php echo h($ordersLine['OrdersLine']['quantity']); ?>&nbsp;</td>
                                                                    <td><?php echo h($this->Session->read('currencysym')).h($ordersLine['OrdersLine']['total_line']); ?>&nbsp;</td>
                                                                    <td><?php echo h($ordersLine['OrdersLine']['comments']); ?>&nbsp;</td>
                                                                </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="tab_history">
                                    <div class="table-container">
                                        <table class="table table-hover dataTable no-footer" id="datatable_history" role="grid">
                                            <thead>
                                                <tr role="row" class="heading">
                                                    <th width="55%">Description</th>
                                                    <th width="25%">Datetime</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($objectevents as $key => $objectevent) { ?>
                                                <tr role="row" class="<?php echo ($key % 2 == 0 ? "odd" : "even"); ?>">
                                                    <td><?php echo $this->Order->eventShipmentStatus($objectevent); ?></td>
                                                    <td class="sorting_1"><?php echo $this->Admin->localTime("%Y-%m-%d %H:%M:%S", strtotime($objectevent['Event']['created'])); ?></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
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
