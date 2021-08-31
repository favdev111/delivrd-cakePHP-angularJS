<style>
figcaption {
    margin: 10px 0 0 0;
    font-variant: small-caps;
    font-family: Arial;
    font-weight: bold;
    font-size: 20px;

}
@media print {
  @page {
        size: auto;   /* auto is the initial value */
        margin: 6mm;    /* this affects the margin in the printer settings */
    }
}
</style>
<!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            
            <!-- BEGIN PAGE CONTENT-->
            <div class="invoice">
                <div class="row invoice-logo">
                    <div class="col-xs-2">
                        <?php if($index == 2) { ?>
                        <?php if(!empty($order['User']['logo']) || !empty($order['User']['logo_url'])) { ?>
                        <figure>
                            <img src="<?php if(!empty($order['User']['logo'])) echo Router::url('/', true) . 'files' . "/user/logo/" . $order['User']['id'] . "/" . $order['User']['logo']; else echo $order['User']['logo_url'];?>" style="max-height:75px">
                            <figcaption><?php echo $order['User']['company']; ?></figcaption>
                        </figure>  
                        <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="col-xs-8">
                        <h3 class="text-center"><?php echo $title .' '. $order['Order']['id']; ?></h3>
                    </div>
                </div>
                <hr/>
                <div class="row">
                <?php if($index != 1) : ?>
                    <div class="col-xs-4">
                        <h3>Supplier Info:</h3>
                        <ul class="list-unstyled">
                            <li>
                                <strong>Name:</strong> <?php echo h($order['Supplier']['name']); ?>
                            </li>
                            <li>
                                <strong>Email:</strong> <?php echo h($order['Supplier']['email']); ?>
                            </li>
                            
                        </ul>
                    </div>
                <?php endif; ?>
                    <?php if($index != 1) : ?>
                    <div class="col-xs-4">
                        <h3>Order Info:</h3>
                        <ul class="list-unstyled">
                            <li>
                                <strong>Purchase Order #:</strong> <?php echo $order['Order']['id']; ?>
                            </li>
                            <li>
                                <strong>Supplier's Order #:</strong> <?php echo h($order['Order']['external_orderid']); ?>
                            </li>
                            <li>
                                <strong>Created On:</strong> <?php echo $this->Admin->localTime("%Y-%m-%d", strtotime($order['Order']['created'])); ?>
                            </li>
                            <li>
                                <strong>Requested Delivery Date:</strong> <?php echo $this->Admin->localTime("%Y-%m-%d", strtotime($order['Order']['requested_delivery_date'])); ?>
                            </li>
                            
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-xs-4">
                    <?php if($index == 1) : 
                    if(!empty($order['User']['logo']) || !empty($order['User']['logo_url'])) : ?>
                        <figure>
                            <img src="<?php if(!empty($order['User']['logo'])) echo Router::url('/', true) . 'files' . "/user/logo/" . $order['User']['id'] . "/" . $order['User']['logo']; else echo $order['User']['logo_url'];?>" style="max-height:75px">
                            <figcaption><?php echo $order['User']['company']; ?></figcaption>
                        </figure>  
                    <?php endif; 
                    endif; ?>
                        <h3><?php if($index != 1) echo 'Ship To:'; else echo 'Customer Details'; ?></h3>
                        <ul class="list-unstyled">
                            <li>
                                <strong>Name:</strong> <?php echo h($order['Order']['ship_to_customerid']); ?>
                            </li>
                            <li>
                                <strong>Street:</strong> <?php echo h($order['Address']['street']); ?>
                            </li>
                            <li>
                                <strong>City:</strong> <?php echo h($order['Address']['city']); ?>
                            </li>
                            <li>
                                <strong>Zip:</strong> <?php echo h($order['Address']['zip']); ?>
                            </li>
                            <?php if($order['Order']['state_id'] != 1) { ?>
                            <li>
                                <strong>State:</strong> <?php echo (!empty($order['Address']['State']) ? $order['Address']['State']['name'] : h($order['Address']['stateprovince'])); ?>
                            </li>
                            <?php } ?>
                            <li>
                                <strong>Country:</strong> <?php if(!empty($order['Address']['Country'])) echo h($order['Address']['Country']['name']); ?>
                            </li>
                            <li>
                                <strong>Phone:</strong> <?php echo h($order['Address']['phone']); ?>
                            </li>
                            <?php if($index == 1) : ?>
                            <li>
                                <strong>Sales Channel:</strong> <?php echo h($order['Schannel']['name']); ?>
                            </li>
                            <?php endif; ?>
                            
                        </ul>
                    </div>
                </div>
                <br />
                <strong>Remarks:</strong>
                  <?php echo h($order['Order']['comments']); ?>
                <br />
                <br />
                <br />
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>
                                 #
                            </th>
                            <th class="hidden-480">
                                 Product Name
                            </th>
                            <th class="hidden-480">
                                 SKU
                            </th>
                            <th class="hidden-480">
                                 Quantity
                            </th>
                            <th class="hidden-480">
                                 Unit Cost
                            </th>
                            <th>
                                 Total
                            </th>
                                                        <th>
                                 Remarks
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $qtySum = 0;
                        foreach ($order['OrdersLine'] as $ordersLine): ?>
                        <tr>
                            <td>
                                 <?php echo h($ordersLine['line_number']); ?>
                            </td>
                            
                            <td class="hidden-480"> 
                                  <?php echo h($ordersLine['Product']['name']); ?>
                            </td>
                            <td class="hidden-480"> 
                                  <?php echo h($ordersLine['Product']['sku']); ?>
                            </td>
                            <td class="hidden-480">
                                 <?php $qtySum = $qtySum + $ordersLine['quantity'];
                                 echo $ordersLine['quantity']; ?>
                            </td>
                            <td class="hidden-480">
                                 <?php echo h($this->Session->read('currencysym')).$ordersLine['unit_price']; ?>
                            </td>
                            <td>
                                 <?php echo h($this->Session->read('currencysym')).$ordersLine['total_line']; ?>
                            </td>
                                                        <td class="hidden-480">
                                 <?php echo h($ordersLine['comments']); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tr>
                            <th></th>
                            <th class="hidden-480">
                                 Totals
                            </th>
                            <th class="hidden-480"></th>
                            <th class="hidden-480">
                            <?php echo $qtySum; ?>
                            </th>
                            <th class="hidden-480"></th>
                            <th>
                            <?php echo h($this->Session->read('currencysym')).$ordertotals['linestotal']; ?>
                            </th>
                            <th></th>
                        </tr>
                        </table>
                    </div>
                </div>
                <br />
                    <br />
                    <br />
                    <br />
                <div class="row">
                    <div class="col-xs-4">
                    <a class="btn btn-lg blue hidden-print margin-bottom-5" onclick="javascript:window.print();">
                        Print <i class="fa fa-print"></i>
                        </a>
                    </div>  
                    <div class="col-xs-4">
                    
                    </div>  
                    <div class="col-xs-4 invoice-block">
                        <div class="well">
                        <ul class="list-unstyled amounts">
                            <li>
                                <strong>Subtotal Amount</strong> <?php echo h($this->Session->read('currencysym')).$ordertotals['linestotal']; ?>
                            </li>
                            <li>
                                <strong>Shipping Costs:</strong> <?php echo h($this->Session->read('currencysym')).$ordertotals['shipping']; ?>
                            </li>
                            <li>
                                <strong>Grand Total:</strong> <?php echo h($this->Session->read('currencysym')).$ordertotals['grand']; ?>
                            </li>
                        </ul>
                        <br/>
                        
                        </div>
                    </div>
                    
                    
                </div>
            </div>
            <!-- END PAGE CONTENT-->
            </div>
            
    </div>

    <!-- END CONTENT -->
