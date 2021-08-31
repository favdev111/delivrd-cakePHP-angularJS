<style type="text/css">
    .remark-icon{
        font-size: 20px;
        margin-top: 12px;
        margin-left: 24px;
    }
</style>

<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="ReceiveProducts">
    <div class="page-content">

        <?php if($can_complete) { ?>
        <div id="flasMsg" class="alert alert-success hidden">
            <strong style="font-size:24px;"><i class="fa fa-exclamation-triangle " style="font-size:24px;"></i> Success!!! All lines received.</strong>
            <div class="lead text-center">You can Complete Order Processing</div>
            <div class="text-center"><?php echo $this->Form->postLink(__('Mark Order As Complete'), array('controller' => 'orders','action' => 'complete', $order['Order']['id']), array('class' => 'btn green-jungle', 'escape'=> false)); ?></div>
        </div>
        <?php } ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-arrow-left"></i> Receive Purchase Order Products # <?php echo $order['Order']['id']; ?>
                        </div>
                        <div class="actions">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                            <div class="btn-group pull-right" style="margin-left: 10px;">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><?php echo $this->Html->link('<i class="fa fa-search"></i> Order Details', array('controller' => 'replorders','action' => 'details', $order['Order']['id']), array('escape'=> false)); ?></li>
                                    <?php if($is_write) { ?>
                                    <li><?php echo $this->Html->link('<i class="fa fa-arrow-right"></i> Receive All Products', array('controller' => 'orders_lines','action' => 'receivealllines', $order['Order']['id']), array('escape'=> false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-flag-checkered"></i> Complete Order Processing'), array('controller' => 'orders','action' => 'complete', $order['Order']['id'], '?' => array('nonang' => 1)), array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal')); ?></li>
                                    <?php } ?>
                                </ul>
                                
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <?php echo $this->Form->create('OrdersLine', array( 'class' => 'form-horizontal', 'url' => array_merge(array('action' => 'receive'), $this->params['pass']))); ?>
                            <?php $product_name = (!empty($this->request->params['named']['searchby'])) ? $this->request->params['named']['searchby'] : ''; ?>
                            <div class="row" style="margin-bottom: 24px;margin-top: 18px;">
                                <div class="col-md-5">
                                    <div class="input-group col-md-12"> 
                                        <?php echo $this->Form->input('searchby', array('label' => false, 'class'=>'code-scan form-control input-md', 'placeholder' => 'Search by SKU,EAN or product name', 'value' => $product_name, 'id' => 'autocomplete', 'autofocus' => 'autofocus')); ?>
                                        <span class="input-group-addon">
                                            <i class="fa fa-barcode"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-md blue filter-submit margin-bottom"><i class="fa fa-search"></i></button>
                                    <?php echo $this->html->link('<i class="fa fa-undo"></i>', array('plugin' => false, 'controller' => 'orders_lines', 'action' => 'receive', $this->params['pass'][0]), array('class' => 'btn btn-md blue filter-submit margin-bottom', 'escape' => false)); ?>
                                </div>
                            </div>
                        <?php  echo $this->Form->end(); ?>
                        
                        <div class="table-container">
                            <table class="table table-advance table-hover">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th width="5%">#</th>
                                        <th width="5%">Line #</th>
                                        <th width="10%">SKU</th>
                                        <th width="">Product Name</th>
                                        <th width="8%" class="img_column hide">Product Image</th>
                                        <th width="8%">Ordered Qty.</th>
                                        <th width="8%">Receive Qty.</th>
                                        <th width="12%">Location</th>
                                        <th width="5%">Remarks</th>
                                        <th width="12%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $show_img_column = false; ?>
                                <?php foreach ($ordersLines as $ordersLine): ?>
                                    <?php if ($ordersLine['Order']['id'] == $order['Order']['id'] && $ordersLine['OrdersLine']['type'] != 4) { ?>
                                    
                                    <tr role="row" class="lineRow" id="line_<?php echo $ordersLine['OrdersLine']['id']; ?>" >
                                        <td class="highlight">
                                            <?php echo $this->Form->create('OrdersLine', array('id' => 'receive_order_'. $ordersLine['OrdersLine']['id'], 'class'=>'receive_order'));?>
                                            <?php echo $this->Form->hidden('label',array('value' => 'receive')); ?>
                                            <?php echo $this->Form->hidden('OrdersLineId',array('value' => $ordersLine['OrdersLine']['id'],'id' => 'OrdersLineId')); ?>
                                            <?php echo $this->Form->hidden('shipment_id',array( 'value' => $ordersLine['OrdersLine']['shipment_id'],'id' => 'shipment_id')); ?>
                                            <?php if($ordersLine['OrdersLine']['receivedqty'] < $ordersLine['OrdersLine']['quantity']) { ?>
                                                <div class="danger lineStatus"></div>
                                            <?php } else { ?>
                                                <div class="success lineStatus"></div>
                                            <?php } ?>
                                            <?php echo $this->Html->link($ordersLine['Order']['id'], array('controller' => 'replorders', 'action' => 'details', $ordersLine['Order']['id'])); ?>
                                        </td>
                                        <td><?php echo h($ordersLine['OrdersLine']['line_number']); ?></td>
                                        <td><a href="#" class="skuCode" data-sku="<?php echo h($ordersLine['OrdersLine']['sku']); ?>"><?php echo h($ordersLine['OrdersLine']['sku']); ?></a></td>
                                        <td><?php echo $this->element('product_name', array('name' => $ordersLine['Product']['name'], 'id' => $ordersLine['Product']['id'])); ?></td>
                                        <td class="img_column hide">
                                            <?php /*if(strpos($ordersLine['Product']['imageurl'], 'image_missing.jpg') !== false) { ?>
                                            
                                            <?php } else { $show_img_column = true;?>
                                            <img src="<?php echo h($ordersLine['Product']['imageurl']) ?>" style="max-height:64px;max-width:64px;" alt="">
                                            <?php }*/ ?>
                                            <?php $show_img_column = true; ?>
                                            <img src="<?php echo h($ordersLine['Product']['imageurl']) ?>" style="max-height:42px;max-width:42px;" class="productImage" rel="product_img" data-id="<?php echo $ordersLine['Product']['id']; ?>">
                                        </td>
                                        <td><?php echo h($ordersLine['OrdersLine']['quantity']); ?></td>

                                        <?php if ($ordersLine['OrdersLine']['return'] == 1) { ?>
                                        <td>
                                            <?php echo h($ordersLine['OrdersLine']['sentqty']); ?>
                                            <?php echo $this->Html->link(__('Return To Supplier'), array('action' => 'sendlines', $ordersLine['OrdersLine']['id'])); ?>
                                        </td>
                                        <?php } ?>

                                        <?php if ($ordersLine['OrdersLine']['return'] != 1) { ?>

                                        <td>
                                            <?php echo $this->Form->input('receivedqty',array( 'label' => false, 'class' => 'form-control', 'min' => 0, 'required' => 'true', 'value' => $ordersLine['OrdersLine']['receivedqty'], 'required')); ?>
                                            <?php echo $this->Form->hidden('quantity',array( 'value' => $ordersLine['OrdersLine']['quantity'])); ?>
                                        </td>
                                        <td>
                                            <?php
                                                /*$receive_id = array();
                                                foreach($ordersLine['Product']['Inventory'] as $Inventory) {
                                                    if(!empty($ordersLine['Product']['receive_location'])) {
                                                        $receive_id[$ordersLine['Product']['Receive']['id']] = $ordersLine['Product']['Receive']['name'];
                                                    }
                                                    if($ordersLine['Product']['receive_location'] !== $Inventory['Warehouse']['id']) {
                                                        $receive_id[$Inventory['Warehouse']['id']] = $Inventory['Warehouse']['name'];
                                                    }
                                                }*/
                                                echo $this->Form->input('warehouse_id',array('label' => false, 'id'=>"warehouse_".$ordersLine['OrdersLine']['id'], 'class' => 'form-control input-sm', 'div' =>false,'required', 'value' => $ordersLine['OrdersLine']['warehouse_id']));
                                                echo  $this->Form->hidden('label',array('value' => 'receive'));
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $this->Form->hidden('receivenotes',array('value' => $ordersLine['OrdersLine']['receivenotes'], 'label' => false, 'id' => 'receivenotes-'.$ordersLine['OrdersLine']['id'])); ?>
                                            <?php /*<a href="#" class="remarks-id" id="<?php echo $ordersLine['OrdersLine']['id']; ?>" data-toggle="modal" data-value="<?php echo $ordersLine['OrdersLine']['receivenotes']; ?>" data-target="#remarks-modal"> <i class="fa fa-plus remark-icon"></i></a>*/ ?>
                                            <button class="btn-link" type="button" ng-click="addReceiveNotes(<?php echo $ordersLine['OrdersLine']['id']; ?>)"><i class="fa fa-plus remark-icon"></i></button>
                                        </td>
                                        <td>
                                            <button class='btn btn-xs green-jungle' id='issue'><i class="fa fa-arrow-right"></i> Receive</button>
                                            <?php /* echo $this->Html->link(__('<i class="fa fa-barcode"></i>Serials o'), array('controller' => 'serials','action' => 'add', '?' => array('pid' => $ordersLine['OrdersLine']['product_id'], 'oid' => $ordersLine['OrdersLine']['order_id'])), array('escape'=> false, 'class' => 'btn btn-xs blue-chambray'));*/ ?>
                                            <?php echo $this->Html->link(__('<i class="fa fa-barcode"></i>Serials'), array('controller' => 'serials','action' => 'add_line', $ordersLine['OrdersLine']['id']), array('escape'=> false, 'class' => 'btn btn-xs blue-chambray')); ?>
                                        </td>

                                        <?php } ?>
                                    </tr>
                                    <?php echo $this->Form->end(); ?>
                                    <?php } ?>
                                <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>

                        <?php echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'))); ?>
                        <div>
                            <ul class="pagination">
                                <?php
                                    $paginator = $this->Paginator;
                                    echo $paginator->first("First",array('tag' => 'li'));
                                    if($paginator->hasPrev()){
                                        echo $paginator->prev("Prev", array('tag' => 'li'));
                                    }
                                    echo $paginator->numbers(array('modulus' => 2,'tag' => 'li','currentTag' => 'a','currentClass' => 'active','separator' => false));
                                    if($paginator->hasNext()){
                                        echo $paginator->next("Next",array('tag' => 'li'));
                                    }
                                    echo $paginator->last("Last",array('tag' => 'li'));
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<audio id="scannerSuccess">
    <source src="<?php echo $this->webroot; ?>media/barcode-scanner-beep.mp3" type="audio/mpeg">
</audio>
<audio id="scannerError">
    <source src="<?php echo $this->webroot; ?>media/glitch-error.mp3" type="audio/mpeg">
</audio>

<?php echo $this->Html->script('/app/OrdersLines/receive.js?v=0.0.1', array('block' => 'pageBlock')); ?>
<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
var order_list = <?php echo json_encode($order_list); ?>;
var part_ids = <?php echo json_encode($part_ids); ?>;
$(document).ready(function(){
    <?php if($show_img_column) { ?>
        $('.img_column').removeClass('hide');
    <?php } ?>
    $('.receive_order').submit(function(){
        var $form = $(this);
        var formData = $form.serialize();
        $.ajax({
            method: 'POST',
            url: siteUrl + "orders_lines/receivelines/",
            data: formData,
            datatype:'json',
        }).success(function (data) {
            var response = jQuery.parseJSON(data);
            console.log(response.status);
            var messageType = (response.status == true) ? 'success' : 'error';
            
            toastr[response.status](response.message);
            var sentqty = $form.parents('tr').find('#OrdersLineReceivedqty').val()
            var quantity = $form.parents('tr').find('#OrdersLineQuantity').val()
            if(sentqty < quantity) {
                $form.parents('tr').find('.lineStatus').removeClass('success').addClass('danger');
            } else {
                $form.parents('tr').find('.lineStatus').removeClass('danger').addClass('success');
            }
            $('#autocomplete').val('').focus();
            checkAllStatus();
        });
        return false;
    });

    $('#autocomplete').scannerDetection({
        timeBeforeScanTest: 200, // wait for the next character for upto 200ms
        startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
        endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
        avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
        onComplete: function(barcode, qty){
            if(order_list[barcode] == undefined && part_ids[barcode] != undefined) {
                barcode = part_ids[barcode];
            }
            $('#autocomplete').val(barcode);
            $('#line_'+order_list[barcode]).addClass('bg-green-turquoise');
            setTimeout(function() {
                $('#line_'+order_list[barcode]).removeClass('bg-green-turquoise');
            }, 300);

            if(order_list[barcode] == undefined) {
                $('#scannerError')[0].play();
                toastr['error']('The orders line not found. Please, try again.');
            } else {
                
                var quantity = $('#line_'+order_list[barcode]).find('#OrdersLineQuantity').val();
                var sentqty = $('#line_'+order_list[barcode]).find('#OrdersLineReceivedqty').val();
                sentqty = Number(sentqty);
                quantity = Number(quantity);

                if(sentqty < quantity) {
                    $('#scannerSuccess')[0].play();
                    $('#line_'+order_list[barcode]).find('#OrdersLineReceivedqty').val(sentqty + 1);
                    $('#line_'+order_list[barcode]).find('form').submit();
                } else {
                    $('#scannerError')[0].play();
                    $('#autocomplete').val('').focus();
                    toastr['error']('The orders line could not be saved. Please, try again.');
                }

                checkAllStatus();
            }
        },
        onError: function(barcode, qty){
            console.log('Error');
            //$('#autocomplete').val(barcode);
            //$('#scannerError')[0].play();
        }
    });

    $('.skuCode').click(function() {
        var sku = $(this).data('sku');
        $('#autocomplete').scannerDetection(sku.toString());
        return false;
    });

    function checkAllStatus() {
        var is_all_success = true;
        $('.lineRow').each(function() {
            var quantity = $(this).find('#OrdersLineQuantity').val();
            var sentqty = $(this).find('#OrdersLineReceivedqty').val();
            sentqty = Number(sentqty);
            quantity = Number(quantity);
            if(sentqty < quantity) {
                is_all_success = false;
            }
        });
        <?php if($can_complete) { ?>
        if(is_all_success) {
            $('#flasMsg').removeClass('hidden');
        } else {
            $('#flasMsg').addClass('hidden');
        }
        <?php } ?>
    }

    checkAllStatus();
});
<?php $this->Html->scriptEnd(); ?>
</script>
<?php /*
<div class="modal fade" id="remarks-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Remarks</h4>
      </div>
      <?php echo $this->Form->create(); ?>
      <div class="modal-body release-btns">
          <?php echo $this->Form->textarea('receivenotes',array('value' => '', 'label' => false, 'class' => 'form-control receivenotes', 'id' => '')); ?>
      </div>
      <div class="modal-footer">
        <button type="submit" id="form-remarks" class="btn btn-md blue" data-dismiss="modal">Save</button>
      </div>
      <?php echo $this->Form->end(); ?>
    </div>
  </div>
</div> */ ?>