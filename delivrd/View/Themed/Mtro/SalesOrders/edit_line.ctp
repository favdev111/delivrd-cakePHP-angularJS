<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Add Order Line <i class="fa fa-angle-right"></i>
                Order #<?php echo h($order_line['Order']['external_orderid']); ?>
            </h4>
        </div>
        <?php echo $this->Form->create('OrdersLine', ['ng-submit' => 'editProduct($event)', 'role'=>'form']); ?>
        <?php echo $this->Form->input('id', array('hidden' => true)); ?>
        <div class="modal-body">
            <div class="alert alert-danger hide" id="modalFormMsg"></div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><label class="control-label">Product <span class="required">*</span></label></th>
                        <th><label class="control-label">Location <span class="required">*</span></label></th>
                        <th><label class="control-label">Qty. <span class="required">*</span></label></th>
                        <th><label class="control-label">Unit Price (<?php echo h($currency['Currency']['name']); ?>)</label></th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php echo $this->Form->input('product_id',array('label' => false,'data-placeholder' => 'Select...','class' => 'form-control input-medium select2me', 'id' => 'product_id','div' =>false, 'empty' => 'Select...', 'required')); ?>
                        </td>
                        <td>
                            <?php echo $this->Form->input('warehouse_id',array('label' => false, 'class' => 'form-control select2me', 'id' => 'warehouse_id', 'div' => false, 'required')); ?>
                        </td>
                        <td>
                            <?php echo $this->Form->input('quantity',array('label' => false, 'class' => 'form-control','div' =>false, 'min' => 1, 'style' => 'width:100px', 'required')); ?> 
                        </td>
                        <td>
                            <?php echo $this->Form->input('unit_price', array('label' => false, 'class' => 'form-control','div' =>false, 'min' => 0.01, 'style' => 'width:100px', 'required')); ?>
                        </td>
                        <td>
                            <?php echo $this->Form->input('comments',array('label' => false,'class' => 'form-control input-larg','div' =>false)); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success"><i class="fa fa-plus"></i> Save</button>
            <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal" ng-click="close($event)">Close</button>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<script>
    var warehouses = <?php echo json_encode($warehouses); ?>;
    var products_list = <?php echo json_encode($products_list); ?>;

    $(document).ready(function(){
        $('#product_id').select2().on('select2-selecting', function (e) {
            
            $('#OrdersLineUnitPrice').attr('placeholder', 'load...');
            $.ajax({
                type: 'POST',
                url: '<?php echo $this->Html->url(array('controller'=>'products', 'action' => 'getShortDet')); ?>/'+e.val,
                data: '',
                dataType:'json',
                success:function (r, status) {
                    <?php if($authUser['copypdtprice']) { ?>
                    $('#OrdersLineUnitPrice').attr('placeholder', '');
                    //$('#OrdersLineUnitPrice').val(r.price);
                    if( r.schannel_prices[<?php echo $order_line['Order']['schannel_id']; ?>] != undefined ) {
                        $('#OrdersLineUnitPrice').val( r.schannel_prices[<?php echo $order_line['Order']['schannel_id']; ?>] );
                    } else {
                        $('#OrdersLineUnitPrice').val(r.price);
                    }
                    <?php } ?>
                    if(r.issue_location != null && warehouses[r.issue_location] != undefined) {
                        $('#warehouse_id').val(r.issue_location).trigger('change');
                    }
                }
            });
            
        });
        $('#warehouse_id').select2({minimumResultsForSearch: -1});
    });
</script>
<style>
    .select2-drop-mask {z-index: 10051}
    .select2-drop {z-index: 10052}
</style>