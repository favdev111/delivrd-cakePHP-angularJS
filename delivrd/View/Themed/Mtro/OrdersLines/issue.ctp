<style type="text/css">
    .remark-icon{
        font-size: 20px;
        margin-top: 12px;
        margin-left: 24px;
    }
</style>

<!-- BEGIN CONTENT -->
<div class="page-content-wrapper"  ng-controller="IssueProducts">
    <div class="page-content">

        <div id="flasMsg" class="alert alert-success hidden">
            <strong style="font-size:24px;"><i class="fa fa-exclamation-triangle " style="font-size:24px;"></i> Success!!! All lines issued.</strong>
            <div class="lead text-center">You can Complete Order Processing</div>
            <div class="text-center"><?php echo $this->Form->postLink(__('Mark Order As Complete'), array('controller' => 'orders','action' => 'complete', $order['Order']['id']), array('class' => 'btn green-jungle', 'escape'=> false)); ?></div>
        </div>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <!-- Begin: life time stats -->
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-arrow-left"></i> Issue Sales Order Products # <?php echo $order['Order']['id']; ?>
                        </div>
                        <div class="actions">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                            <?php if($is_write) { ?>
                            <div class="btn-group pull-right" style="margin-left: 10px;">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><?php echo $this->Html->link('<i class="fa fa-search"></i> Order Details', array('controller' => 'salesorders','action' => 'details', $order['Order']['id']), array('escape'=> false)); ?></li>
                                    <li><a href ng-click="issueAllLines(<?php echo $order['Order']['id']; ?>, 0)"><i class="fa fa-arrow-left"></i> Issue All Products</a></li>
                                    <li><a href ng-click="issueAllLines(<?php echo $order['Order']['id']; ?>, 1)"><i class="fa fa-arrow-left"></i> Issue All Products &amp; Complete</a></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-flag-checkered"></i> Complete Order Processing'), array('controller' => 'orders','action' => 'complete', $order['Order']['id'], '?' => array('nonang' => 1)), array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal')); ?></li>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <?php echo $this->Form->create('OrdersLine', array(
                                'class' => 'form-horizontal',
                                'url' => array_merge(array('action' => 'issue'), $this->params['pass'])
                                )); 
                                $product_name = (!empty($this->request->params['named']['searchby'])) ? $this->request->params['named']['searchby'] : ''; 
                        ?>
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
                                    <?php echo $this->html->link('<i class="fa fa-undo"></i>', array('plugin' => false, 'controller' => 'orders_lines', 'action' => 'find', $this->params['pass'][0]), array('class' => 'btn btn-md blue filter-submit margin-bottom', 'escape' => false)); ?>
                                </div>
                            </div>
                        <?php  echo $this->Form->end(); ?>
                        <div class="table-container">
                            <table class="table table-advance table-hover">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th width="5%">#</th>
                                            <th width="8%">Line #</th>
                                            <th width="10%">SKU</th>
                                            <th width="25%">Product Name</th>
                                            <th width="15%" class="img_column hide">Product Image</th>
                                            <th width="10%">Ordered Qty.</th>
                                            <th width="10%">Issue Qty.</th>
                                            <th width="15%">Location</th>
                                            <th width="5%">Remarks</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $show_img_column = false; ?>
                                    <?php foreach ($ordersLines as $ordersLine): ?>
                                        <?php if ($ordersLine['Order']['id'] == $order['Order']['id'] && $ordersLine['OrdersLine']['type'] != 4) { ?>
                                        <tr role="row" class="lineRow" id="line_<?php echo $ordersLine['OrdersLine']['id']; ?>" >
                                            <td class="highlight">
                                                
                                                <?php echo $this->Form->create('OrdersLine', array('id' => 'receive_order_'. $ordersLine['OrdersLine']['id'], 'class'=>'receive_order'));?>
                                                <?php echo $this->Form->hidden('label',array('value' => 'issue')); ?>
                                                <?php echo $this->Form->hidden('confirm',array( 'value' => $this->Session->read('allow_negative') )); ?>
                                                <?php echo $this->Form->hidden('OrdersLineId',array('value' => $ordersLine['OrdersLine']['id'],'id' => 'OrdersLineId', 'class'=>'lineId')); ?>
                                                <?php echo $this->Form->hidden('shipment_id',array( 'value' => $ordersLine['OrdersLine']['shipment_id'],'id' => 'shipment_id')); ?>
                                                
                                                <?php if($ordersLine['OrdersLine']['sentqty'] < $ordersLine['OrdersLine']['quantity']) { ?>
                                                    <div class="danger lineStatus"></div>
                                                <?php } else { ?>
                                                    <div class="success lineStatus"></div>
                                                <?php } ?>
                                                <?php echo $this->Html->link($ordersLine['Order']['id'], array('controller' => 'salesorders', 'action' => 'details', $ordersLine['Order']['id'])); ?>
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

                                            <?php if (($order['Order']['ordertype_id'] == 2) && ($ordersLine['OrdersLine']['return'] == 1)) { ?>
                                            <td>
                                                <?php echo h($ordersLine['OrdersLine']['sentqty']); ?>
                                                <?php echo $this->Html->link(__('Return To Supplier'), array('action' => 'sendlines', $ordersLine['OrdersLine']['id'])); ?>
                                            </td>
                                            <?php } ?>

                                            <?php if(($order['Order']['ordertype_id'] == 1) &&  ($order['Order']['status_id'] != 1)) { ?>
                                            <td>
                                                <?php echo $this->Form->input('sentqty',array( 'label' => false, 'class' => 'form-control', 'min' => 0, 'required' => 'true', 'value' => $ordersLine['OrdersLine']['sentqty'], 'required')); ?>
                                                <?php echo $this->Form->hidden('quantity',array( 'value' => $ordersLine['OrdersLine']['quantity'])); ?>
                                            </td>
                                            <td><?php echo $this->Form->input('warehouse_id', array( 'label' => false, 'class' => 'form-control input-sm', 'div' =>false,'required', 'value' => $ordersLine['OrdersLine']['warehouse_id'])); ?></td>
                                            <td>
                                                <?php echo $this->Form->hidden('receivenotes',array('value' => $ordersLine['OrdersLine']['receivenotes'], 'label' => false, 'id' => 'receivenotes-'.$ordersLine['OrdersLine']['id'])); ?>
                                                <a href="#" class="remarks-id" id="<?php echo $ordersLine['OrdersLine']['id']; ?>" data-toggle="modal" data-value="<?php echo $ordersLine['OrdersLine']['receivenotes']; ?>" data-target="#remarks-modal"> <i class="fa fa-plus remark-icon"></i></a>
                                            </td>
                                            <td>
                                                <button class="btn btn-xs green-jungle" id="issue"><i class="fa fa-arrow-left"></i> Issue Line</button>
                                                <?php echo $this->Form->end(); ?>
                                            </td>
                                            <?php } ?>
                                        </tr>
                                        
                                        <?php } ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>

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
                <!-- End: life time stats -->
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
</div>
<!-- END CONTENT -->

<div class="modal fade" id="confirmModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Negative inventory quantity.</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> You are trying to issue a quantity greater than the quantity you have in inventory.</div>
                <p>Quantity to issue: <b id="issueOffset"></b></p>
                <p>Quantity in location <span id="warehouseName"></span>: <b id="invQty"></b></p>
                <div id="allInvQty" style="padding-left: 15px;"></div>

                <p class="text-warning text-center"><b>If you choose to "Issue Quantity", inventory quantity will be negative.</b></p>
                <input type="hidden" id="modalLineId" value="" >
                <label><input type="checkbox" id="confirmNegative" style="vertical-align: -4px"> Don't show this again</label>
            </div>
            <div class="modal-footer">
                <button class="btn green-jungle" id="submitButton">Issue Quantity</button>
                <?php echo $this->Html->link(__('Update Inventory'), array('controller' => 'inventories', 'action' => 'index'), array('class' => 'btn btn-default')); ?>
            </div>
        </div>
    </div>
</div>

<audio id="scannerSuccess">
    <source src="<?php echo $this->webroot; ?>media/barcode-scanner-beep.mp3" type="audio/mpeg">
</audio>
<audio id="scannerError">
    <source src="<?php echo $this->webroot; ?>media/glitch-error.mp3" type="audio/mpeg">
</audio>

<?php echo $this->Html->script('/app/OrdersLines/issue.js?v=0.0.3', array('block' => 'pageBlock')); ?>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var order_list = <?php echo json_encode($order_list); ?>;
    var order = <?php echo json_encode($order); ?>;
    var orderId = <?php echo $order['Order']['id']; ?>;
    var show_img_column = <?php echo $show_img_column; ?>;
<?php $this->Html->scriptEnd(); ?>

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
</div>