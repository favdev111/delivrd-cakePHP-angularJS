<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Cancel Order <i class="fa fa-angle-right"></i>
                Order #<?php echo $order['Order']['external_orderid']; ?>
            </h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php if(!$is_completed) { ?>
                        <h4 class="text-danger text-center">
                            You can't Cancel this order. Here is possible to cancel only complete orders.
                        </h4>
                    <?php } else if(!$is_allow) { ?>
                        <h4 class="text-danger text-center">
                            You can't cancel order number #<?php echo $order['Order']['id']; ?>. It have lines for which you have no access.
                        </h4>
                    <?php } else { ?>
                        <h4 class="text-primary text-center">
                            <strong>Are you sure you want to cancel order # <?php echo $order['Order']['id']; ?>, Ref. No. <?php echo $order['Order']['external_orderid']; ?>?</strong>
                        </h4>
                        <div class="text-center">
                            <label><input type="checkbox" id="confirmReturn" name="confirm" checked style="vertical-align:-4px"> Return issue quantities to stock</label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php if($is_allow && $is_completed) { ?>
                <button class="btn btn-success" ng-click="cancel(<?php echo $order['Order']['id']; ?>)"><i class="fa fa-check"></i> Cancel Order</button>
            <?php } ?>
            <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal" ng-click="close($event)">Close</button>
        </div>
    </div>
</div>