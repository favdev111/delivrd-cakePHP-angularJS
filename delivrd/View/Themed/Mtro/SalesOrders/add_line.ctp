<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Add Order Line <i class="fa fa-angle-right"></i>
                Order #<?php echo h($order['Order']['external_orderid']); ?>
            </h4>
        </div>
        <?php echo $this->Form->create('OrdersLine', ['ng-submit' => 'addProduct($event)', 'role'=>'form']); ?>
        <div class="modal-body">
            <div class="alert alert-danger hide" id="modalFormMsg"></div>
            <?php if($products) { ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><label class="control-label">Product <span class="required">*</span></label></th>
                        <th colspan="2"><label class="control-label">Location <span class="required">*</span></label></th>
                        <th><label class="control-label">Qty. <span class="required">*</span></label></th>
                        <th><label class="control-label">Unit Price(<?php echo h($currency['Currency']['name']); ?>)</label></th>
                        <th>Remarks</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="position: relative;">
                            <?php #echo $this->Form->input('product_id',array('label' => false,'data-placeholder' => 'Select...','class' => 'form-control input-medium', 'id' => 'product_id','div' =>false, 'empty' => 'Select...', 'required')); ?>
                            <input type="text" id="product_id" name="data[OrdersLine][product_id]" class="form-control input-medium" placeholder="Select..." required  style="min-width:150px">
                        </td>
                        <td colspan="2">
                            <?php echo $this->Form->input('warehouse_id',array('label' => false, 'class' => 'form-control select2me', 'placeholder'=>'Select...', 'type'=>'text', 'id' => 'warehouse_id', 'div' => false, 'style' => 'width:120px', 'required')); ?>
                        </td>
                        <td>
                            <?php echo $this->Form->input('quantity',array('label' => false, 'class' => 'form-control','div' =>false, 'min' => 1, 'style' => 'width:70px', 'required')); ?> 
                        </td>
                        <td>
                            <?php echo $this->Form->input('unit_price', array('label' => false, 'class' => 'form-control','div' =>false, 'min' => 0.01, 'required', 'style' => 'width:100px')); ?>
                        </td>
                        <td>
                            <?php echo $this->Form->input('comments',array('label' => false,'class' => 'form-control input-larg','div' =>false)); ?>
                        </td>
                        <td>
                            <button class="btn btn-success" ng-click="addProductContinue($event)" <?php echo (!$products ? 'disabled':'')?> ><i class="fa fa-plus"></i></button>
                        </td>
                    </tr>
                    <tr ng-repeat="line in lines | orderBy:sortType:sortReverse" id="line_{{line.OrdersLine.id}}">
                        <td>{{line.Product.name}}</td>
                        <td>{{line.Warehouse.name}}</td>
                        <td>{{line.OrdersLine.quantity}}</td>
                        <td>{{currency.csymb}}{{line.OrdersLine.unit_price}}</td>
                        <td>{{currency.csymb}}{{line.OrdersLine.total_line}}</td>
                        <td>{{line.OrdersLine.comments}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div class="row">
                <div class="col-md-offset-6 col-md-6">
                    <div class="well">
                        <div class="row static-info align-reverse">
                            <div class="col-md-8 name">Sub Total:</div>
                            <div class="col-md-3 value">
                                {{currency.csymb}}{{ displayPrice(total.linestotal)}}
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
                                {{currency.csymb}}{{total.grand_new}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php } else { ?>
            <h3 class="alert alert-warning text-center">You have no available <?php echo ( $addpack ? 'packaging materials':'products' ); ?>.</h3>
            <h4 class="text-center">Please <a href="<?php echo $this->Html->url(['controller' => 'products', 'action' => 'add']); ?>">add <?php echo ( $addpack ? 'packaging materials':'products' ); ?></a> firstly</h4>
            <?php } ?>
        </div>
        <div class="modal-footer">
            <button class="btn green-jungle" ng-click="addProductContinue($event)" <?php echo (!$products ? 'disabled':'')?> ><i class="fa fa-plus"></i> Save &amp; Add Another</button>
            <button class="btn btn-warning" ng-click="addProductClose($event)" <?php echo (!$products ? 'disabled':'')?> ><i class="fa fa-plus"></i> Save &amp; Close</button>
            <button class="btn default" style="box-shadow: none;" ng-click="close($event)"><i class="fa fa-close"></i> Close</button>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<style type="text/css">
    .select2-disabled {
        opacity: 0.5;
        cursor: default !important;
    }
</style>

<script>
    var warehouses = <?php echo json_encode($warehouses); ?>;
    var products_list = <?php echo json_encode($products_list); ?>;

    function drawLocation($active_loc) {
        var loc_data = []
        $.each(warehouses, function(key, val) {
            if($active_loc[key] != undefined) {
                loc_data.push({'id': key, 'text': val, 'disabled': !$active_loc[key]});
            } else {
                <?php if(!$authUser['zeroquantity']) { ?>
                    loc_data.push({'id': key, 'text': val, 'disabled': true});
                <?php } else { ?>
                    loc_data.push({'id': key, 'text': val, 'disabled': false});
                <?php } ?>
            }
        });

        $('#warehouse_id').select2({
            data: loc_data,
            minimumResultsForSearch: -1,
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) { return m; }
        });
        $('#warehouse_id').trigger('change');
    }

    function format($data) {
        var str = '';
        if($data.disabled) {
            str = '<div class="select2-disabled" style="opacity:0.5"><i class="fa fa-ban font-red-haze"></i> '+$data.text+'</div>';
        } else {
            str = '<div>'+$data.text+'</div>';
        }
        return str;
    }

    $(document).ready(function(){
        

        var productSelect = $('#product_id').select2({
            /*ajax: {
                delay: 250,
                url: '<?php echo $this->Html->url(['controller'=>'orders', 'action' => 'products', $order['Order']['id']]); ?>/1',
                dataType: 'json',
                data: function(params) {
                    var queryParameters = {
                        search: params,
                        type: 'public'
                    }
                    return queryParameters;
                },
                results: function (data, page) {
                    return data;
                }
            },*/
            data: products_list,
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) { return m; }
        }).on('select2-selecting', function (e) {
            $.ajax({
                type: 'POST',
                url: '<?php echo $this->Html->url(array('controller'=>'products', 'action' => 'getShortDet')); ?>/'+e.val,
                data: '',
                dataType:'json',
                success:function (r, status) {
                    <?php if($authUser['copypdtprice']) { ?>
                    $('#OrdersLineUnitPrice').attr('placeholder', '');
                    if( r.schannel_prices[<?php echo $order['Order']['schannel_id']; ?>] != undefined ) {
                        $('#OrdersLineUnitPrice').val( r.schannel_prices[<?php echo $order['Order']['schannel_id']; ?>] );
                    } else {
                        $('#OrdersLineUnitPrice').val(r.price);
                    }
                    <?php } ?>
                    $('#warehouse_id').val('').trigger('change');

                    <?php if(!$authUser['zeroquantity']) { ?>
                        var active_loc = [];
                        $.each(e.choice.quantity, function(key, val) {
                            if(key != 'disabled') {
                                if(val.Inventory.quantity > 0) {
                                    active_loc[val.Warehouse.id] = true;
                                } else {
                                    active_loc[val.Warehouse.id] = false;
                                }
                            }
                        });
                        drawLocation(active_loc);
                    <?php } ?>
                    if(r.issue_location != null && warehouses[r.issue_location] != undefined) {
                        <?php if(!$authUser['zeroquantity']) { ?>
                            if(active_loc[r.issue_location]) {
                                $('#warehouse_id').val(r.issue_location).trigger('change');
                            }
                        <?php } else { ?>
                            $('#warehouse_id').val(r.issue_location).trigger('change');
                        <?php } ?>
                    }
                }
            });
        });
        
        drawLocation([]);
    });
</script>
<style>
    .select2-drop-mask {z-index: 10051}
    .select2-drop {z-index: 10052}
</style>