<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Change Order to Paid <i class="fa fa-angle-right"></i>
                Order #<?php echo h($order['Order']['external_orderid']); ?>
            </h4>
        </div>

        <?php echo $this->Form->create('Order', ['ng-submit' => 'submitPaymentStatus($event)', 'role'=>'form']); ?>
            <?php echo $this->Form->hidden('id', array('hidden' => true, 'value' => $order['Order']['id'])); ?>
            <?php echo $this->Form->hidden('status_id', array('hidden' => true, 'value' => 55)); ?>
            <div class="modal-body">
                <div class="alert alert-danger hide" id="modalFormMsg"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Comments:</label>
                            <?php echo $this->Form->textarea('payment_text', array('label' => false, 'class' => 'form-control')); ?>
                        </div>

                        <div class="form-group">
                            <label>Paid Date:</label>
                            <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                <input type="text" class="form-control form-filter" name="data[Order][payment_date]" value="<?php echo $order['Order']['payment_date']; ?>" >
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary"><i class="fa fa-credit-card"></i> Paid</button>
                <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal" ng-click="close($event)">Close</button>
            </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>


<script>
    $(document).ready(function(){
        DateInit.init();
    });
</script>