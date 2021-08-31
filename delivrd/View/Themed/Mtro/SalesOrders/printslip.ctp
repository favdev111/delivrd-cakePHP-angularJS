<style>
    figcaption {
        margin: 10px 0 0 0;
        font-variant: small-caps;
        font-family: Arial;
        font-weight: bold;
        font-size: 20px;
    }
    .amounts {font-size:24px;}
    .quantity {width:5%;}
    .unitcost {padding-left:60px !important; }

    @media print {
        @page {
            size: auto;   /* auto is the initial value */
            margin: 6mm;    /* this affects the margin in the printer settings */
        }

        .amounts {font-size:18px;}
        .well {
            background-color: #eee !important;
            border: 1px solid #333;
            border-radius: 2px;
        }

        .unitcost {padding-left:0px !important; }
    }
</style>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
            
        <div class="portlet-body hidden-print">
            <div class="csv-div text-right">
                <div class="btn-toolbar">
                    <button class="btn btn-warning" id="hidePrice">Hide Price</button>
                    <button class="btn btn-info" id="showPrice">Show Price</button>
                </div>
            </div>
        </div>


        <!-- BEGIN PAGE CONTENT-->
        <div class="invoice">
            <div class="row invoice-logo" >
                <div class="col-xs-12">
                    <h3 class="text-center" style="margin-bottom:0;"><?php echo __('Packing slip for order #') .' '. $order['Order']['id']; ?></h3>
                </div>
            </div>
            <hr style="margin-top:5px;" />
            <div class="row">
                <div class="col-xs-12">
                    <?php if(!empty($order['User']['logo']) || !empty($order['User']['logo_url'])) { ?>
                    <figure>
                        <img src="<?php if(!empty($order['User']['logo'])) echo Router::url('/', true) . 'files' . "/user/logo/" . $order['User']['id'] . "/" . $order['User']['logo']; else echo h($order['User']['logo_url']);?>" style="max-height:75px">
                        <figcaption><?php echo h($order['User']['company']); ?></figcaption>
                    </figure>  
                    <?php } ?>
                    <h4><?php echo __('Customer Details'); ?></h4>
                    <ul class="list-unstyled">
                        <li>
                            <strong>Name:</strong> <?php echo h($order['Order']['ship_to_customerid']); ?>
                        </li>
                        <li>
                            <?php
                                $address = array();
                                if(!empty($order['Address']['street'])) {
                                    $address[] = $order['Address']['street'];
                                }
                                if(!empty($order['Address']['city'])) {
                                    $address[] = $order['Address']['city'];
                                }
                                if(!empty($order['Address']['State'])) {
                                    $address[] = $order['Address']['State']['name'];
                                } elseif(!empty($order['Address']['stateprovince'])) {
                                    $address[] = $order['Address']['stateprovince'];
                                }
                                if(!empty($order['Address']['zip'])) {
                                    $address[] = $order['Address']['zip'];
                                }
                                if(!empty($order['Address']['Country'])) {
                                    $address[] = $order['Address']['Country']['name'];
                                }
                            ?>
                            <strong>Address:</strong> <?php echo implode(', ', $address); ?>
                        </li>
                        <li>
                            <strong>Phone:</strong> <?php echo h($order['Address']['phone']); ?>
                        </li>
                        <li>
                            <strong>Sales Channel:</strong> <?php echo h($order['Schannel']['name']); ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
            <div class="margin-bottom-10">
                <strong>Remarks:</strong>
                <?php echo h($order['Order']['comments']); ?>
            </div>
            <div class="row margin-bottom-10">
                <div class="col-xs-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="20px">#</th>
                                <th class="hidden-480">Product Name</th>
                                <th class="hidden-480">SKU</th>
                                <th class="hidden-480 quantity">Quantity</th>
                                <th class="hidden-480 quantity displayAdd hide">Picked Qty.</th>
                                <th class="hidden-480 unitcost hidePrice">Unit Cost</th>
                                <th class="hidePrice">Total</th>
                                <th class="hidden-480 quantity displayAdd hide">BIN</th>
                                <th class="displayAdd hide">Location</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $qtySum = 0; foreach ($order['OrdersLine'] as $ordersLine) { ?>
                            <tr>
                                <td <?php echo ( (isset($authUser['packinslip_desc']) && $authUser['packinslip_desc']) ?'rowspan="2" style="vertical-align:middle;border-right:1px solid #EBEBEB;"':''); ?>><?php echo h($ordersLine['line_number']); ?></td>
                                
                                <td class="hidden-480"> 
                                    <?php echo h($ordersLine['Product']['name']); ?>
                                </td>
                                <td class="hidden-480"> 
                                    <?php echo h($ordersLine['Product']['sku']); ?>
                                </td>
                                <td class="hidden-480 text-center">
                                    <?php
                                        $qtySum = $qtySum + $ordersLine['quantity'];
                                        echo h($ordersLine['quantity']);
                                    ?>
                                </td>
                                <td class="displayAdd hide"><?php echo $ordersLine['sentqty']; ?></td>
                                <td class="hidden-480 unitcost hidePrice">
                                    <?php echo h($this->Session->read('currencysym')). number_format($ordersLine['unit_price'], 2, '.', ''); ?>
                                </td>
                                <td class="hidePrice">
                                    <?php echo h($this->Session->read('currencysym')). number_format($ordersLine['total_line'], 2, '.', ''); ?>
                                </td>
                                <td class="hidden-480 displayAdd hide">
                                    <?php echo h($ordersLine['Product']['bin']); ?>
                                </td>
                                <td class="displayAdd hide">
                                    <?php echo h($warehouses[$ordersLine['warehouse_id']]); ?>
                                </td>
                                <td class="hidden-480">
                                    <?php echo h($ordersLine['comments']); ?>
                                </td>
                            </tr>
                            <?php if( isset($authUser['packinslip_desc']) && $authUser['packinslip_desc'] ) { ?>
                                <tr>
                                    <td colspan="9">&raquo; <?php echo h($ordersLine['Product']['description']); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                            <tr class="hidePrice">
                                <th></th>
                                <th class="hidden-480">Totals</th>
                                <th class="hidden-480"></th>
                                <th class="hidden-480 text-center"><?php echo h($qtySum); ?></th>
                                <th class="hidden-480"></th>
                                <th><?php echo h($this->Session->read('currencysym')). number_format($ordertotals['linestotal'], 2, '.', ''); ?></th>
                                <th></th>
                            </tr>
                        <?php if($order_costs || $ordertotals['shipping']) { ?>
                            <tr class="hidePrice">
                                <th colspan="7">Others:</th>
                            </tr>
                            <?php /*<tr>
                                <th colspan="5">Type</th>
                                <th>Amount</th>
                                <th class="hidden-480">Remarks</th>
                            </tr>*/ ?>
                            <?php if($ordertotals['shipping']) { ?>
                                <tr class="hidePrice">
                                    <td></td>
                                    <td colspan="4">Shipping Costs</td>
                                    <td><?php echo h($this->Session->read('currencysym')). number_format($ordertotals['shipping'], 2, '.', ''); ?></td>
                                    <td class="hidden-480"></td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($order_costs as $costs) { ?>
                                <tr class="hidePrice">
                                    <td></td>
                                    <td colspan="4">
                                        <?php echo h($costs_types[$costs['OrdersCosts']['type']]); ?>
                                        <?php if($costs['OrdersCosts']['uom'] == 'percentage') { ?>
                                                %<?php echo h($costs['OrdersCosts']['amount']); ?>
                                            </span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($costs['OrdersCosts']['uom'] == 'percentage') { ?>
                                        <span class="<?php echo(($costs['OrdersCosts']['type'] == 'discount')?'text-danger':''); ?>">
                                            <?php echo(($costs['OrdersCosts']['type'] == 'discount')?'-':''); ?><?php echo h($this->Session->read('currencysym')) . number_format(round($ordertotals['linestotal'] * $costs['OrdersCosts']['amount']/100, 2), 2, '.', ''); ?>
                                        </span>
                                        <?php } else { ?>
                                        <span class="<?php echo(($costs['OrdersCosts']['type'] == 'discount')?'text-danger':''); ?>">
                                            <?php echo(($costs['OrdersCosts']['type'] == 'discount')?'-':''); ?><?php echo h($this->Session->read('currencysym')) . number_format($costs['OrdersCosts']['amount'], 2, '.', ''); ?>
                                        </span>
                                        <?php } ?>
                                    </td>
                                    <td class="hidden-480"><?php echo h($costs['OrdersCosts']['comments']); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-4">
                    <a class="btn btn-lg blue hidden-print margin-bottom-5" onclick="javascript:window.print();">
                        Print <i class="fa fa-print"></i>
                    </a>
                </div>  
                <div class="col-xs-2">
                
                </div>  
                <div class="col-xs-6 invoice-block hidePrice">
                    <div class="well amounts">
                        <strong>Grand Total:</strong> <?php echo h($this->Session->read('currencysym')).  number_format($ordertotals['grand_new'], 2, '.', ''); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<script>
    $(document).ready(function() {
        $('#hidePrice').click(function(){
            $('.hidePrice').addClass('hide');
            $('.displayAdd').removeClass('hide');
        });
        $('#showPrice').click(function(){
            $('.hidePrice').removeClass('hide');
            $('.displayAdd').addClass('hide');
        });
    });
</script>