<style type="text/css">
	.remark-icon{
		font-size: 20px;
	    margin-top: 12px;
	    margin-left: 24px;
	}
</style>
<?php if($order['Order']['ordertype_id'] == 1) {
		$action = 'packcomplete';
		$orderprefix = (!empty($this->Session->read('sales_title')) ? ucwords($this->Session->read('sales_title')) : 'Sales Order');
		$headertext = (!empty($this->Session->read('sales_title')) ? 'Issue ' .ucwords($this->Session->read('sales_title')) : 'Issue Sales Order');
		$color = 'red-flamingo';
        $actionqtytext = 'Issue Qty.';
        $allissue = '<i class="fa fa-arrow-left"></i> Issue All Products';
        $allissueaction = 'sendalllines';
        $action_btn = '<i class="fa fa-arrow-left"></i> Issue Line';
	}  else {
		$action = 'receivecomplete';
		$orderprefix = 'Purchase Orders';
		$headertext = 'Receive Purchase Order';
		$color = 'green-jungle';
        $actionqtytext = 'Receive Qty.';
        $allissue = '<i class="fa fa-arrow-left"></i> Receive All Products';
        $allissueaction = 'receivealllines';
        $action_btn = '<i class="fa fa-arrow-left"></i> Receive';
	}
?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content">

		<!-- BEGIN PAGE HEADER-->
		<div id="flashMessage"></div>
		<!-- END PAGE HEADER-->

		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
				<?php echo $this->Session->flash(); ?>
				<!-- Begin: life time stats -->
				<div class="portlet box <?php echo $color; ?>">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-arrow-left"></i><?php echo $headertext; ?> Products
						</div>
						<div class="actions">
							<button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
							<div class="btn-group pull-right" style="margin-left: 10px;">
								<button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
									<i class="fa fa-ellipsis-h"></i>
								</button>
								<ul class="dropdown-menu pull-right" role="menu">
									<?php if($is_write) { ?>
									<?php
										$action = ($order['Order']['ordertype_id'] == 1 ? 'packcomplete' : 'receivecomplete');
										echo "<li>";
										echo $this->Html->link(__($allissue), array('controller' => 'orders_lines','action' => $allissueaction, $order['Order']['id']), array('escape'=> false));
										echo "</li>";
										echo "<li>";
										echo $this->Form->postLink(__('<i class="fa fa-flag-checkered"></i> Complete Order Processing'), array('controller' => 'orders','action' => 'complete', $order['Order']['id']), array('escape'=> false));
										echo "</li>";
									?>
									<?php } ?>
								</ul>
							</div>
						</div>
					</div>
						<div class="portlet-body">
							<?php echo $this->Form->create('OrdersLine', array(
									'class' => 'form-horizontal',
									'url' => array_merge(array('action' => 'find'), $this->params['pass'])
								    )); 
									$product_name = (!empty($this->request->params['named']['searchby'])) ? $this->request->params['named']['searchby'] : ''; 
									?>
								<div class="row" style="margin-bottom: 24px;margin-top: 18px;">
									<div class="col-md-5">
										<div class="input-group" style="margin-bottom: -5px;">
						                    <div class="input-group"> 
												<?php echo $this->Form->input('searchby', array('label' => false, 'class'=>'code-scan form-control input-md', 'placeholder' => 'Search by SKU,EAN or product name', 'value' => $product_name, 'id' => 'autocomplete', 'style' => 'width: 392px;height: 32px;')); ?>
							                    <span class="input-group-addon">
							                        <i class="fa fa-barcode"></i>
							                    </span>
							                </div>
							            </div>
									</div>
									<div class="col-md-2">
										<button type="submit" class="btn btn-md blue filter-submit margin-bottom"><i class="fa fa-search"></i></button>
										<?php echo $this->html->link('<i class="fa fa-undo"></i>', array('plugin' => false, 'controller' => 'orders_lines', 'action' => 'find', $this->params['pass'][0]), array('class' => 'btn btn-md blue filter-submit margin-bottom', 'escape' => false)); ?>
									</div>
								</div>
							<?php  echo $this->Form->end(); ?>
							<div class="table-container">
								
								<table class="table table-hover" id="datatable_orders">
									<thead>
										<tr role="row" class="heading">
											<th width="5%">#</th>
											<th width="8%">Line #</th>
											<th width="10%">SKU</th>
											<th width="25%">Product Name</th>
											<th width="25%" class="img_column hide">Product Image</th>
											<th width="10%">Ordered Qty.</th>
											<th width="10%"><?php echo $actionqtytext; ?></th>
											<th width="10%">Location</th>
											<th width="5%">Remarks</th>
											<th width="10%">Actions</th>
										</tr>
									</thead>
								<tbody>
								<?php $show_img_column = false; ?>
								<?php foreach ($ordersLines as $ordersLine): ?>
									<?php
										if ($ordersLine['Order']['id'] == $order['Order']['id'] && $ordersLine['OrdersLine']['type'] != 4) { ?>
									<?php $shipment_id = (isset($order['Shipment'][0]['id'])  ? $order['Shipment'][0]['id'] : null); ?>
									<?php echo $this->Form->create('OrdersLine', array('id' => 'receive_order_'. $ordersLine['OrdersLine']['id'], 'class'=>'receive_order'));?>
									<?php echo $this->Form->hidden('OrdersLineId',array('value' => $ordersLine['OrdersLine']['id'],'id' => 'OrdersLineId')); ?>
									<?php echo $this->Form->hidden('shipment_id',array( 'value' => $shipment_id,'id' => 'shipment_id')); ?>
	    							<tr role="row">
										<td>
											<?php echo $this->Html->link($ordersLine['Order']['id'], array('controller' => 'orders', 'action' => 'viewrord', $ordersLine['Order']['id'])); ?>
										</td>
										<td><?php echo h($ordersLine['OrdersLine']['line_number']); ?></td>
										<td><?php echo h($ordersLine['OrdersLine']['sku']); ?></td>
										<td><?php echo $this->element('product_name', array('name' => $ordersLine['Product']['name'], 'id' => $ordersLine['Product']['id'])); ?></td>
										<td class="img_column hide">
											<?php if(strpos($ordersLine['Product']['imageurl'], 'image_missing.jpg') !== false) { ?>
											
											<?php } else { $show_img_column = true;?>
											<img src="<?php echo h($ordersLine['Product']['imageurl']) ?>" style="max-height:64px;max-width:64px;" alt="">
											<?php } ?>
										</td>
										<td><?php echo h($ordersLine['OrdersLine']['quantity']); ?></td>

										<?php if (($order['Order']['ordertype_id'] == 2) && ($ordersLine['OrdersLine']['return'] == 1)) { ?>
										<td>
											<?php echo h($ordersLine['OrdersLine']['sentqty']); ?>
											<?php echo $this->Html->link(__('Return To Supplier'), array('action' => 'sendlines', $ordersLine['OrdersLine']['id'])); ?>
										</td>
										<?php } ?>

										<?php if ((($order['Order']['ordertype_id'] == 2) && ($ordersLine['OrdersLine']['return'] != 1)) || (($order['Order']['ordertype_id'] == 1) &&  ($order['Order']['status_id'] != 1))) { ?>

										<td>
											<?php if($order['Order']['ordertype_id'] == 2) echo $this->Form->input('receivedqty',array( 'label' => false, 'class' => 'form-control', 'min' => 0, 'required' => 'true', 'value' => $ordersLine['OrdersLine']['receivedqty'], 'required'));
												 else
												 echo $this->Form->input('sentqty',array( 'label' => false, 'class' => 'form-control', 'min' => 0, 'required' => 'true', 'value' => $ordersLine['OrdersLine']['sentqty'], 'required')); ?>
											<?php echo $this->Form->hidden('quantity',array( 'value' => $ordersLine['OrdersLine']['quantity'])); ?>
										</td>
										<td>
											<?php
												//$issue_id = array();
												$receive_id = array();

												/*foreach($ordersLine['Product']['Inventory'] as $Inventory) {
													if(!empty($ordersLine['Product']['issue_location'])) {
														$issue_id[$ordersLine['Product']['Issue']['id']] = $ordersLine['Product']['Issue']['name'];
													}

													if($ordersLine['Product']['issue_location'] !== $Inventory['Warehouse']['id']) {
										    			$issue_id[$Inventory['Warehouse']['id']] = $Inventory['Warehouse']['name'];
										    		}
										    	}*/


												foreach($ordersLine['Product']['Inventory'] as $Inventory) {
													if(!empty($ordersLine['Product']['receive_location'])) {
														$receive_id[$ordersLine['Product']['Receive']['id']] = $ordersLine['Product']['Receive']['name'];
													}
													if($ordersLine['Product']['receive_location'] !== $Inventory['Warehouse']['id']) {
										    			$receive_id[$Inventory['Warehouse']['id']] = $Inventory['Warehouse']['name'];
										    		}
										    	}

										    if($order['Order']['ordertype_id'] == 1)
												echo $this->Form->input('warehouse_id', array( 'label' => false, 'class' => 'form-control input-sm', 'div' =>false,'required', 'value' => $ordersLine['OrdersLine']['warehouse_id']));
											else
												echo $this->Form->input('warehouse_id',array( 'label' => false, 'class' => 'form-control input-sm', 'div' =>false,'required', 'options' => $receive_id));
											echo  $this->Form->hidden('label',array('value' => ($order['Order']['ordertype_id'] == 2) ? 'receive' : 'issue')); ?>
										</td>
										<td>
											<?php echo $this->Form->hidden('receivenotes',array('value' => $ordersLine['OrdersLine']['receivenotes'], 'label' => false, 'id' => 'receivenotes-'.$ordersLine['OrdersLine']['id'])); 
											echo'<a href="#" class="remarks-id" id="'. $ordersLine['OrdersLine']['id'] .'" data-toggle="modal" data-value="' . $ordersLine['OrdersLine']['receivenotes'] . '" data-target="#remarks-modal"> <i class="fa fa-plus remark-icon"></i></a>'; ?>
										</td>
										<td>
											<button class='btn btn-xs green-jungle' id='issue'><?php echo $action_btn; ?></button>
											<?php if($order['Order']['ordertype_id'] == 2) echo $this->Html->link(__('<i class="fa fa-barcode"></i>Serials'), array('controller' => 'serials','action' => 'add', '?' => array('pid' => $ordersLine['OrdersLine']['product_id'], 'oid' => $ordersLine['OrdersLine']['order_id'])), array('escape'=> false, 'class' => 'btn btn-xs blue-chambray')); ?>
										</td>

										<?php } ?>
									</tr>
									<?php echo $this->Form->end(); ?>
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

<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
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
	        var messageType = (response.status == true) ? 'success' : 'danger';
	        var message = '<div class="alert alert-' + messageType + '" id="msg"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' + response.message + '</div>';
	        $('#flashMessage').html(message);
	        setTimeout(function() {
	            $('#msg').hide(1000);
	        }, 1000);
	    });
	    return false;
	});
});
<?php $this->Html->scriptEnd(); ?>
</script>

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
