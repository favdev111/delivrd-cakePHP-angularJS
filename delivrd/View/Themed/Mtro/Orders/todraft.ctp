<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Change Order to Draft <i class="fa fa-angle-right"></i>
                Order #<?php echo $order['Order']['external_orderid']; ?>
            </h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php if($is_allow) { ?>
                    <h3 class="text-primary text-center">
                        Are you sure you want change to Draft order # <?php echo $order['Order']['id']; ?>, Ref. No. <?php echo $order['Order']['external_orderid']; ?>?
                    </h3>
                    <div class="text-center">
                        <label><input type="checkbox" id="confirmReturn" name="confirm" checked style="vertical-align:-4px"> Return issue quantities to stock</label>
                    </div>
                    <?php } else { ?>
                    <h3 class="text-danger text-center">
                        You can't change to Draft order number #<?php echo $order['Order']['id']; ?>. It have lines for which you have no access.
                    </h3>
                    <?php } ?> 
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php if($is_allow) { ?>
                <?php if($this->request->query('nonang')) { ?>
                <?php echo $this->Form->postLink(__('<i class="fa fa-unlock"></i> Change to Draft'), array('controller' => 'orders','action' => 'todraft', $order['Order']['id']), array('escape'=> false)); ?>
                <?php } else { ?>
                <button class="btn btn-success" ng-click="complete(<?php echo $order['Order']['id']; ?>)"><i class="fa fa-unlock"></i> Change to Draft</button>
                <?php } ?>
            <?php } ?>
            <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal" ng-click="close($event)">Close</button>
        </div>
    </div>
</div>