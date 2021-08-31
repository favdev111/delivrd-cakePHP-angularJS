<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Order Details <i class="fa fa-angle-right"></i>
                Order #<?php echo h($order['Order']['external_orderid']); ?>
            </h4>
        </div>
        <?php echo $this->Form->create('Order', ['ng-submit' => 'editDetails($event)', 'role'=>'form']); ?>
        <?php echo $this->Form->input('id', array('hidden' => true)); ?>
        <div class="modal-body">
            <div class="alert alert-danger hide" id="modalFormMsg"></div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="form-group">
                        <label>Reference Order:</label>
                        <?php echo $this->Form->input('external_orderid',array('label' => false, 'class' => 'form-control')); ?>
                    </div>

                    <div class="form-group">
                        <label>Sales Channel</label>
                        <?php echo $this->Form->input('schannel_id',array('label' => false, 'class' => 'form-control select2me')); ?>
                    </div>

                    <div class="form-group">
                        <label>Shipping Costs (<?php echo $currency['Currency']['name']; ?>):</label>
                        <?php echo $this->Form->input('shipping_costs', array('label' => false, 'class' => 'form-control')); ?>
                    </div>

                    <div class="form-group">
                        <label>Requested Date:</label>
                        <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd" data-date-start-date="+0d" data-date="<?php echo date('Y-m-d'); ?>">
                            <input type="text" class="form-control form-filter" name="data[Order][requested_delivery_date]" value="<?php echo $order['Order']['requested_delivery_date']; ?>" >
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Remarks:</label>
                        <?php echo $this->Form->input('comments',array('label' => false,'class' => 'form-control')); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success"><i class="fa fa-plus"></i> Save</button>
            <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal" ng-click="close($event)">Close</button>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#OrderSchannelId').select2({minimumResultsForSearch: -1});
        DateInit.init();
    });
</script>
<style>
    .select2-drop-mask {z-index: 10051}
    .select2-drop {z-index: 10052}
</style>