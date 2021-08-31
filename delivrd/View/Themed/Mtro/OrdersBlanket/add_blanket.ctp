<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Add Blanket Order Line <i class="fa fa-angle-right"></i>
                Order #<?php echo $order['Order']['external_orderid']; ?>
            </h4>
        </div>
        <?php echo $this->Form->create('OrdersBlanket', ['ng-submit' => 'addProduct($event)', 'role'=>'form']); ?>
        <div class="modal-body">
            <div class="alert alert-danger hide" id="modalFormMsg"></div>
            <?php if($products) { ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><label class="control-label">Product <span class="required">*</span></label></th>
                        <th colspan="2"><label class="control-label">Location <span class="required">*</span></label></th>
                        <th><label class="control-label">Qty. <span class="required">*</span></label></th>
                        <th><label class="control-label">Unit Price (<?php echo $currency['Currency']['name']; ?>)</label></th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="position: relative;">
                            <?php #echo $this->Form->input('product_id',array('label' => false,'data-placeholder' => 'Select...','class' => 'form-control input-medium select2me', 'id' => 'product_id','div' =>false, 'empty' => 'Select...', 'required')); ?>
                            <input type="text" id="product_id" name="data[OrdersBlanket][product_id]" class="form-control input-medium" placeholder="Select..." required>
                        </td>
                        <td colspan="2" style="position: relative;">
                            <?php echo $this->Form->input('warehouse_id',array('label' => false, 'class' => 'form-control', 'id' => 'warehouse_id', 'div' => false, 'required')); ?>
                        </td>
                        <td>
                            <?php echo $this->Form->input('quantity',array('label' => false, 'class' => 'form-control','div' =>false, 'min' => 1, 'style' => 'width:100px', 'required')); ?> 
                        </td>
                        <td>
                            <?php echo $this->Form->input('unit_price', array('label' => false, 'class' => 'form-control','div' =>false, 'min' => 0.01, 'style' => 'width:100px')); ?>
                        </td>
                        <td>
                            <?php echo $this->Form->input('comments',array('label' => false,'class' => 'form-control input-larg','div' =>false)); ?>
                        </td>
                    </tr>
                    <tr ng-repeat="line in lines | orderBy:sortType:sortReverse" id="line_{{line.OrdersLine.id}}">
                        <td>{{line.Product.name}}</td>
                        <td>{{line.Warehouse.name}}</td>
                        <td>{{line.OrdersLine.quantity}}</td>
                        <td>{{currency.csymb}}{{line.OrdersLine.unit_price}}</td>
                        <td>{{currency.csymb}}{{line.OrdersLine.total_line}}</td>
                        <td>{{line.OrdersLine.comments}}</td>
                    </tr>
                </tbody>
            </table>

            <div class="row" ng-if="is_blanket == 0">
                <div class="col-md-offset-6 col-md-6">
                    <div class="well">
                        <div class="row static-info align-reverse">
                            <div class="col-md-8 name">Sub Total:</div>
                            <div class="col-md-3 value">
                                {{currency.csymb}}{{total.linestotal}}
                            </div>
                        </div>
                        <div class="row static-info align-reverse">
                            <div class="col-md-8 name">Shipping:</div>
                            <div class="col-md-3 value">
                                {{currency.csymb}}{{total.shipping}}
                            </div>
                        </div>
                        <div class="row static-info align-reverse">
                            <div class="col-md-8 name">Grand Total:</div>
                            <div class="col-md-3 value">
                                {{currency.csymb}}{{total.grand}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php } else { ?>
            <h3 class="alert alert-warning text-center">You have no available <?php echo ( $addpack ? 'packaging materials':'products' ); ?>.</h3>
            <h4 class="text-center">Please <a href="#">add <?php echo ( $addpack ? 'packaging materials':'products' ); ?></a> firstly</h4>
            <?php } ?>
        </div>
        <div class="modal-footer">
            <button class="btn green-jungle" <?php echo (!$products ? 'disabled':'')?> ><i class="fa fa-plus"></i> Save &amp; Close</button>
            <button class="btn default" style="box-shadow: none;" ng-click="close($event)"><i class="fa fa-close"></i> Close</button>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<script>
    $(document).ready(function(){

        var warehouses = <?php echo json_encode($warehouses); ?>;

        $('#product_id').select2({
            ajax: {
                delay: 250,
                url: '<?php echo $this->Html->url(['controller'=>'orders', 'action' => 'products', $order['Order']['id']]); ?>',
                dataType: 'json',
                data: function(params) {
                    console.log(params);
                    var queryParameters = {
                        search: params,
                        type: 'public'
                    }
                    return queryParameters;
                },
                results: function (data, page) {
                    return data;
                }
            }
        }).on('select2-selecting', function (e) {
            <?php if($authUser['copypdtprice']) { ?>
            $('#OrdersBlanketUnitPrice').attr('placeholder', 'load...');
            $.ajax({
                type: 'POST',
                url: '<?php echo $this->Html->url(array('controller'=>'products', 'action' => 'getPurchasePrice')); ?>/'+e.val,
                data: '',
                dataType:'json',
                success:function (r, status) {
                    $('#OrdersBlanketUnitPrice').attr('placeholder', '');
                    $('#OrdersBlanketUnitPrice').val(r.price);
                    if(warehouses[r.receive_location] != undefined) {
                        $('#warehouse_id').val(r.receive_location).trigger('change');
                    }
                }
            });
            <?php } ?>
        });
        $('#warehouse_id').select2({minimumResultsForSearch: -1});
    });
</script>
<style>
    .select2-drop-mask {z-index: 10051}
    .select2-drop {z-index: 10052}
</style>