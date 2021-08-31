<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Complete Order <i class="fa fa-angle-right"></i>
                Order #<?php echo $order['Order']['external_orderid']; ?>
            </h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php if($is_allow) { ?>
                    <h3 class="text-primary text-center">
                        Are you sure you want to complete order # <?php echo $order['Order']['id']; ?>, Ref. No. <?php echo $order['Order']['external_orderid']; ?>?
                    </h3>
                        <?php if(!$is_ready) { ?>
                        <div class="text-center text-warning"><strong>Some order lines were not issued from stock</strong></div><br>
                        <div class="text-center">
                            <?php if($this->request->query('nonang')) { ?>
                                <?php echo $this->Form->postLink(__('Complete without issuing lines'), array('controller' => 'orders','action' => 'complete', $order['Order']['id']), array('escape'=> false, 'class' => 'btn btn-warning')); ?>
                            <?php } else { ?>
                                <button class="btn btn-warning" ng-click="complete(<?php echo $order['Order']['id']; ?>)">Complete without issuing lines</button>
                            <?php } ?>
                            <a href="<?php echo $this->Html->url(array('controller' => 'orders_lines', 'action' => 'issuealllines', $order['Order']['id'], '?' => array('f' => $this->request->query('f')))); ?>" class="btn btn-success"><i class="fa fa-flag-checkered"></i> Issue all lines and complete</a>
                        </div>
                        <?php } ?>
                    <?php } else { ?>
                    <h3 class="text-danger text-center">
                        You can't complete order number #<?php echo $order['Order']['id']; ?>. It have lines for which you have no access.
                    </h3>
                    <?php } ?> 
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php if($is_allow && $is_ready) { ?>
                <?php if($this->request->query('nonang')) { ?>
                <?php echo $this->Form->postLink(__('<i class="fa fa-flag-checkered"></i> Complete'), array('controller' => 'orders','action' => 'complete', $order['Order']['id']), array('escape'=> false)); ?>
                <?php } else { ?>
                <button class="btn btn-success" ng-click="complete(<?php echo $order['Order']['id']; ?>)"><i class="fa fa-flag-checkered"></i> Complete</button>
                <?php } ?>
            <?php } ?>
            <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal" ng-click="close($event)">Close</button>
        </div>
    </div>
</div>