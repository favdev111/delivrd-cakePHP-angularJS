<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>
            
        <!-- BEGIN PAGE CONTENT-->
        <?php echo $this->Session->flash(); ?>
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Form->create('Serial', array('class' => 'form-horizontal form-row-seperated')); ?>
                <?php echo $this->Form->hidden('order_id_in', ['value' => $orderline['Order']['id']]); ?>
                <?php echo $this->Form->hidden('product_id', ['value' => $orderline['Product']['id']]); ?>
                
                <div class="portlet box blue-chambray">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list-ol" ></i> Add Serial Number
                        </div>
                        <div class="actions btn-set">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                            <button class="btn green" id="saveBtn" type="submit"><i class="fa fa-check"></i> Save</button>

                            <?php echo $this->html->link(__('<i class="fa fa-arrow-right"></i> Completed serials'), array('plugin' => false, 'controller' => 'orders_lines', 'action' => 'receive', $orderline['Order']['id']), array('class' => 'btn default yellow-stripe', 'escape' => false, 'title' => 'New Order', 'style' => 'margin-right: 50px;')); ?>
                        </div>
                    </div>
                        
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Product Name:
                                            </div>
                                            <div class="col-md-7 value">
                                                  <?php echo $orderline['Product']['name']; ?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Quantity Received:
                                            </div>
                                            <div class="col-md-7 value">
                                                 <?php echo $orderline['OrdersLine']['receivedqty'] ?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Serial Numbers Received:
                                            </div>
                                            <div class="col-md-7 value">
                                                <span id="recievedQty"><?php echo $countorderserials ?></span>
                                                <span class="label label-danger hidden" id="quantityWrn"> Qty scanned greater than recieved</span>
                                                
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Order Number:
                                            </div>
                                            <div class="col-md-7 value">
                                                 <?php echo $orderline['Order']['id'] ?>
                                            </div>
                                        </div>
    
                       
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Location: </label>
                                            <div class="col-md-6 value">
                                                 <?php  echo $this->Form->input('warehouse_id',array('label' => false, 'value'=>$orderline['OrdersLine']['warehouse_id'], 'class' => 'form-control input-sm', 'empty' => 'Select...' )); ?>
                                            </div>
                                        </div>                                                                      
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Serial No.: <span class="required">*</span></label>
                                            <div class="col-md-6">
                                                <?php echo $this->Form->input('serialnumber',array('label' => false, 'class' => 'form-control','div' =>false, 'autofocus' => 'autofocus')); ?>
                                            </div>
                                        </div>
                                                
                                    </div>
                        
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    $(document).ready(function(){
        var receivedqty = <?php echo $orderline['OrdersLine']['receivedqty']; ?>;
        <?php if($countorderserials > $orderline['OrdersLine']['receivedqty']) { ?>
            $('#quantityWrn').removeClass('hidden');
        <?php } ?>

        $('#SerialWarehouseId').select2({minimumResultsForSearch: -1});

        /*$("#SerialSerialnumber").keypress(function(e) {
            if(e.keyCode == 13) {
                $('#SerialAddLineForm').submit();
            }
        });*/

        $('#SerialAddLineForm').submit(function(){
            var $form = $(this);
            var $btn;
            $.ajax({
                type: 'POST',
                url: $form.attr('action'),
                data: $form.serialize(),
                dataType:'json',
                beforeSend: function() {
                    $btn = $('#saveBtn').button('loading')
                },
                success:function (r, status) {
                    if(r.action == 'success') {
                        toastr["success"](r.msg);
                        $('#recievedQty').html(r.countorderserials);
                        if(r.countorderserials > receivedqty) {
                            $('#quantityWrn').removeClass('hidden');
                        }
                        $('#SerialSerialnumber').val('').focus();
                    } else {
                        $.each(r.errors, function(key, value){
                            r.msg = r.msg + '<div><i>'+ value[0] +'</i></div>';
                        });
                        toastr["error"](r.msg);
                    }
                    $btn.button('reset');
                }
            });
            return false;
        });
    });
<?php $this->Html->scriptEnd(); ?>
</script>