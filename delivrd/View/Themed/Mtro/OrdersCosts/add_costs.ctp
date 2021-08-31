<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Additional Costs/Discount <i class="fa fa-angle-right"></i>
                Order #<?php echo $order['Order']['external_orderid']; ?>
            </h4>
        </div>
        <?php echo $this->Form->create('OrdersCosts', ['ng-submit' => 'addCosts($event)', 'role'=>'form']); ?>
            <?php echo $this->Form->hidden('order_id', ['value' => $order['Order']['id']]); ?>
        <div class="modal-body">
            <div class="alert alert-danger hide" id="modalFormMsg"></div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="25%">Costs/Discount</th>
                        <th width="25%">Init of Measure</th>
                        <th width="15%">Amount / %</th>
                        <th width="35%">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $this->Form->input('type', array('label' => false, 'class' => 'form-control', 'empty' => 'Select...', 'options' => ['surchage' => 'Surcharge', 'discount' => 'Discount', 'shipping' => 'Shipping Costs', 'other_costs' => 'Other Costs'], 'div' => false, 'required')); ?></td>
                        <td><?php echo $this->Form->input('uom', array('label' => false, 'class' => 'form-control','div' =>false, 'empty' => 'Select...', 'options' => ['percentage' => 'Percentage', 'amount' => 'Amount' ], 'required')); ?></td>
                        <td><?php echo $this->Form->input('amount', array('label' => false, 'class' => 'form-control','div' =>false)); ?></td>
                        <td>
                            <div class="form-group has-feedback" style="margin-bottom: 0px;">
                                <?php echo $this->Form->input('comments',array('label' => false,'class' => 'form-control input-larg','div' =>false)); ?>
                                <span class="glyphicon glyphicon-pencil form-control-feedback" aria-hidden="true" style="margin-top: 11px;"></span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success" ng-click="addCosts($event)"><i class="fa fa-plus"></i> Save</button>
            <button class="btn default" style="box-shadow: none;" ng-click="close($event)"><i class="fa fa-close"></i> Close</button>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#OrdersCostsType').select2({minimumResultsForSearch: -1});
        $('#OrdersCostsUom').select2({minimumResultsForSearch: -1});
        $('#OrdersCostsType').change(function(){
            if($(this).val() == 'shipping' || $(this).val() == 'other_costs') {
                $('#OrdersCostsUom').val('amount').attr('readonly', true).trigger('change');
            } else {
                $('#OrdersCostsUom').attr('readonly', false).trigger('change');
            }
        });
    });
</script>
<style>
    .select2-drop-mask {z-index: 10051}
    .select2-drop {z-index: 10052}

</style>