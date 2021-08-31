<?php $this->AjaxValidation->active(); ?>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
			<!-- BEGIN PAGE HEADER-->
			
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="fa fa-home"></i>
						<a href="/">Home</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<a href="/waves/">Waves</a>
					</li>
				</ul>
				<div class="page-toolbar">
					<div class="btn-group pull-right">
						<button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
						<i class="fa fa-ellipsis-h"></i>
						</button>
						<ul class="dropdown-menu pull-right" role="menu">
							<?php if(!empty($orderslines)) {
							echo "<li>";
							echo $this->Html->link(__('<i class="fa fa-trash-o"></i> Delete'), array(), array('escape'=> false, 'id' => 'delete')); 
							echo "</li>"; 
							} ?>
							<?php if ($wave['Wave']['status_id'] == 19)
							{
								echo "<li>";
								echo $this->Html->link(__('<i class="fa fa-unlock"></i> Release'), array('action' => 'release',$wave['Wave']['id']), array('escape'=> false));  
								echo "</li>";
							}	
							
							if ($wave['Wave']['status_id'] == 20 || $wave['Wave']['status_id'] == 16)
							{
							echo "<li>";
							echo $this->Html->link(__('<i class="fa fa-cubes"></i> Pack Wave'), array('action' => 'packwaven',$wave['Wave']['id']), array('escape'=> false));  
							echo "</li>";
							echo "<li>";
							echo $this->Html->link(__('<i class="fa fa-cubes"></i> Batch Picking'), array('action' => 'batchpicking',$wave['Wave']['id']), array('escape'=> false));  
							echo "</li>";
							}
							if ($wave['Wave']['status_id'] == 20 || $wave['Wave']['status_id'] == 16)
							{
								if($wave['Wave']['type'] != 2) {
								echo "<li>";
								echo $this->Html->link(__('<i class="fa fa-cubes"></i> Pick By Order'), array('action' => 'pickbyorder',$wave['Wave']['id']), array('escape'=> false));  
								echo "</li>";
								}
							}
							if ($wave['Wave']['status_id'] == 20)
							{

								echo "<li>";
								echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print Pick Slip By Product'), array('action' => 'pickslipbyproduct', $wave['Wave']['id']), array('escape'=> false)); 
								echo "</li>";
								echo "<li>";
								echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print Pick Slip By Order'), array('action' => 'pickslipbyorder', $wave['Wave']['id']), array('escape'=> false)); 
								echo "</li>";
							} 
							?>	
						</ul>
					</div>
				</div>
			</div>
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Session->flash(); ?>
					<!-- Begin: life time stats -->
					<div class="portlet box red-thunderbird">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-play"></i>Wave lines list
							</div>
							
						</div>
						<div class="portlet-body">
							<div class="table-container">
								<div class="table-actions-wrapper">
									<span>
									</span>
									
									<button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
								</div>
								<table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
								<thead>
								<tr role="row" class="heading">
									<th>
										 #
									</th>
									<th>
										 Order Num.
									</th>
									<th>
										 Line Number
									</th>
									<th>
										Product
									</th>
									<th>
										 Image
									</th>
									<th>
										Order Qty
									</th>
									<th>
										Picked Qty
									</th>
									<th>
										Shipment Number
									</th>
									<th>
									Date
									</th>
									<th>
									Time
									</th>
								</tr>
								
								</thead>
								<tbody>
								<?php foreach ($orderslines as $key => $ordersLine): ?>
								<tr role="row" id="checkbox_row">
									<td><?php if($ordersLine['status_id'] == 17) echo $this->Form->checkbox('Orderline.id.' . $key, array('class' => 'checkboxes', 'value' => $ordersLine['id'], 'hiddenField' => false)); ?></td>						
									<td><?php echo $this->Html->link($ordersLine['order_id'], array('controller' => 'orders', 'action' => 'details', $ordersLine['order_id'])); ?>&nbsp;</td>	 
									<td><?php echo h($ordersLine['line_number']); ?>&nbsp;</td>
									<td><?php echo $this->element('product_name', array('name' => $ordersLine['Product']['name'], 'id' => $ordersLine['Product']['id'])); ?>&nbsp;</td>
									<td><?php echo "<img src=".h($ordersLine['Product']['imageurl'])." height='32px' width='32px'>"; ?></td>
									<td><?php echo h($ordersLine['quantity']); ?>&nbsp;</td>
									<td><?php echo h($ordersLine['sentqty']); ?>&nbsp;</td>
									<td><?php if(!empty($ordersLine['Order']['Shipment'])) echo $this->Html->link($ordersLine['Order']['Shipment'][0]['id'], array('controller' => 'shipments', 'action' => 'view', $ordersLine['Order']['Shipment'][0]['id'])); ?>&nbsp;</td>
									<td> <?php echo $this->Admin->localTime("%B %d, %Y", strtotime($ordersLine['modified'])); ?></td>
									<td> <?php echo $this->Admin->localTime("%I:%M %p", strtotime($ordersLine['modified'])); ?></td>
								</tr>
								<?php endforeach; ?>
								</tbody>
								</table>
								<?php echo $this->Form->create('Orderline', array(
			                        'type' => 'post',
			                        'id' => 'orderline_form',
			                        'url' => array_merge(
								            array(
								            	'controller' => 'waves',
								                'action' => 'deleteorder'
								            ),
								            $this->params['pass']
								        ),
			                        'class' => 'form-horizontal list_data_form',
			                        'novalidate' => true,
                    			)); 
								 echo $this->Form->hidden('id', array('id' => 'orderline_id')); 
                    			echo $this->Form->end(); ?>
					
						</div>
					</div>
					<!-- End: life time stats -->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
