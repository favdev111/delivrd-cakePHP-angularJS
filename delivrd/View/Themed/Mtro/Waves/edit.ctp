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
						<a href="index.html">Home</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<a href="/waves/">Waves</a>
					</li>
				</ul>
				<div class="page-toolbar">
					<div class="btn-group pull-right">
						<button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
						Actions <i class="fa fa-angle-down"></i>
						</button>
						<ul class="dropdown-menu pull-right" role="menu">
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
		echo "<li>";
		echo $this->Html->link(__('<i class="fa fa-cubes"></i> Pick By Order'), array('action' => 'pickbyorder',$wave['Wave']['id']), array('escape'=> false));  
		echo "</li>";
		}
		echo "<li>";
		echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print Picking Slip'), array('action' => 'pickingslip', $wave['Wave']['id']), array('escape'=> false)); 
		echo "</li>";
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
								<i class="fa fa-barcode"></i>Wave lines list
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
									<th width="20%">
										 Order Num.
									</th>
									<th width="10%">
										 Line Number
									</th>
									<th width="30%">
										Product
									</th>
									<th width="10%">
										 Image
									</th>
									<th width="15%">
										Order Qty
									</th>
									<th width="10%">
										Picked Qty
									</th>
									<th width="10%">
										Actions
									</th>
									
								</tr>
								
								</thead>
								<tbody>
								<?php foreach ($orderslines as $ordersLine): ?>
								<tr role="row">
								
								<td><?php echo$this->Html->link($ordersLine['order_id'], array('controller' => 'orders', 'action' => 'view', $ordersLine['order_id'])); ?>&nbsp;</td>
	<?php // Debugger::dump($wave['OrdersLine']);  ?>
		<td><?php echo h($ordersLine['line_number']); ?>&nbsp;</td>
		<td><?php echo h($ordersLine['Product']['name']); ?>&nbsp;</td>
		<td><?php echo "<img src=".$ordersLine['Product']['imageurl']." height='32px' width='32px'>"; ?></td>
		<td><?php echo h($ordersLine['quantity']); ?>&nbsp;</td>
		<td><?php echo h($ordersLine['sentqty']); ?>&nbsp;</td>

								<td>
			
									
										<?php echo $this->Html->link("<i class='fa fa-trash-o'></i> Remove", array('action' => 'deletewavelines',$wave['Wave']['id'],$ordersLine['id']),array('escape'=> false, 'class' => 'btn btn-xs default btn-editable')); ?>
																	
						
								
								</td>
								</tr>
								<?php endforeach; ?>
								</tbody>
								</table>
								<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div>
	<ul class="pagination">
	<?php
	$paginator = $this->Paginator;
	echo $paginator->first("First",array('tag' => 'li'));
         
        // 'prev' page button, 
        // we can check using the paginator hasPrev() method if there's a previous page
        // save with the 'next' page button
        if($paginator->hasPrev()){
            echo $paginator->prev("Prev", array('tag' => 'li'));
        }
         
        // the 'number' page buttons
        echo $paginator->numbers(array('modulus' => 2,'tag' => 'li','currentTag' => 'a','currentClass' => 'active','separator' => false));
         
        // for the 'next' button
        if($paginator->hasNext()){
            echo $paginator->next("Next",array('tag' => 'li'));
        }
         
        // the 'last' page button
        echo $paginator->last("Last",array('tag' => 'li'));
		// echo $this->Paginator->prev(__('<'), array('tag' => 'li'),null,array('class' => 'prev disabled'));
		// echo $this->Paginator->first('< first');
		// echo $this->Paginator->numbers(array('tag' => 'li'));
		// echo $this->Paginator->next(__('>'), array('tag' => 'li'));
		// echo $this->Paginator->next('>', array('separator' => '<li>'), null, array('class' => 'next disabled'));
	?>
	</ul>
							</div>
						</div>
					</div>
					<!-- End: life time stats -->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
