<?php // $this->AjaxValidation->active(); ?>
	<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content">

	<!-- BEGIN PAGE CONTENT-->
	<div class="row">
	  <div class="col-md-12">
		<?php echo $this->Session->flash(); ?>
		<!-- Begin: life time stats -->
		<div class="portlet box blue-steel">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-barcode"></i>Product:  
					<?php if(!empty($product[0]['Product']['name'])) 
					echo $product[0]['Product']['name'] . ', ' . $product[0]['Product']['sku'];?>
				</div>
				<div class="actions">
				 	<?php echo $this->Html->link(__('<i class="fa fa-angle-left"></i> Back'), array('controller'=> 'Inventories','action' => 'index'),array('escape'=> false, 'class' => 'btn default yellow-stripe')); ?>
				</div>
			</div>

			<div class="portlet-body">
				<div class="tabbable">
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#tab_thistory" data-toggle="tab"> Transaction History </a>
						</li>
					 	<?php if(sizeof($this->params['pass']) == 1) { ?>  	
						<li>
							<a href="#tab_charts" data-toggle="tab"> Charts </a>
						</li>
					 <?php } ?>	
					</ul>	
			    </div>
			    <div class="csv-div">
					<?php //echo $this->Html->link(__('<i class="fa fa-download"></i> Export'), array('controller'=> 'inventories','action' => 'exportcsv'),array('escape'=> false, 'class' => 'csv-icons', 'title' => 'Export inventory list')); ?>
					<?php //echo $this->Html->link(__('<i class="fa fa-upload"></i> Import'), array('controller'=> 'inventories','action' => 'uploadcsv'),array('escape'=> false, 'class' => 'csv-icons import-btn', 'title' => 'Import inventory from csv')); ?>
					<?php 
					if(!empty($pid)) {
						echo $this->html->link('<i class="fa fa-undo"></i> Show All Locations', array('plugin' => false, 'controller' => 'orders_lines', 'action' => 'linesbyproduct','product_id' => $pid,'cum_qty' => 0), array('id' => 'clear', 'class' => 'csv-icons import-btn', 'escape' => false, 'title' => 'Show all locations')); 	
					} else {
						echo $this->html->link('<i class="fa fa-undo"></i> Show All Locations', array('plugin' => false, 'controller' => 'orders_lines', 'action' => 'linesbyproduct'), array('id' => 'clear', 'class' => 'csv-icons import-btn', 'escape' => false, 'title' => 'Show all locations')); 
					}
					?>
				</div>
				<hr/>
				<?php echo $this->Form->create('OrdersLine', array(
					'class' => 'form-horizontal',
					'id' => 'orderline-search',
					'url' => (!empty($this->request->params['pass']) ? array('controller' => 'orders_lines', 'action' => 'linesbyproduct', 'product_id' => $this->request->params['pass']['0'], 'cum_qty' => $this->request->params['pass']['1']) : ''),
				    )); ?>

					<div class="row" style="margin-bottom: 20px;">
						<!-- <div class="col-md-5">
							<div class="input-group" style="margin-bottom: -5px;">
			                    <div class="input-group"> 
									<?php 
									echo $this->Form->input('searchby', array('label' => false, 'class'=>'form-control input-md', 'placeholder' => 'Search by product name', 'value' => '', 'id' => 'ajax-search', 'style' => 'width: 400px;height: 32px;')); ?>
				                </div>	
					        </div>
						</div> -->
						<label class="col-md-1 control-label">Filter By: 
						</label>
						<div class="col-md-3">
							<?php if ($this->Session->read('locationsactive') == 1) { 	
								echo $this->Form->input('warehouse_id', array('label' => false,'class'=>'form-control form-filter input select2me', 'id' => 'warehouse_select', 'required' => false,'empty' => 'Location...', 'style' => 'width: 140px;height: 32px;')); 	
							} ?>
						</div>
					</div>
				<?php  echo $this->Form->end(); ?>

				<div class="tab-content no-space">
					<div class="tab-pane active" id="tab_thistory">
						<table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
							<thead>
								<tr role="row" class="heading">
									<th> Type </th>	
			                        <th> Order Number </th>							
			                        <?php if($this->Session->read('locationsactive') == 1) { 
			                           echo "<th>Location</th>";
			                        } 
			                        if(empty($product[0]['Product']['name'])) {
			                        	echo "<th>SKU</th><th>Name</th>";
			                        } ?>	
			                        <th> Quantity </th>
			                        <?php if(sizeof($this->params['pass']) != 0) { 
			                        	echo "<th>Inv. Change</th><th>Cum Qty</th>";
			                        	} ?>
									<th>User</th>
									<th>Remarks</th>
									<th><?php echo $this->Paginator->sort(__('modified'), 'Time & Date'); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php $cum_qty = 0; 
							$key = 0; 
							if(count($productlines) !== 0) {
							foreach ($productlinesdata as $productlinedata): 
								$key++;?>
							<tr role="row">
							<td><?php if(isset($productlinedata['tname'])) echo $productlinedata['tname']; ?></td>
							<td>
								<?php 

								if (isset($productlinedata['tquantity']) && $productlinedata['tquantity'] <= 0 && $productlinedata['order_id'] != 4294967294)
								{
									echo $this->Html->link($productlinedata['order_id'], array('controller' => 'orders', 'action' => 'viewcord', h($productlinedata['order_id']))); 
								} else if(isset($productlinedata['tquantity']) && $productlinedata['tquantity'] > 0 && $productlinedata['order_id'] != 4294967294){
									echo $this->Html->link($productlinedata['order_id'], array('controller' => 'orders', 'action' => 'viewrord', h($productlinedata['order_id']))); 
								} 
								?>
							</td>
							<?php if($this->Session->read('locationsactive') == 1) { ?>
						    <td><?php echo h($productlinedata['warehouse_name']); ?></td>
						    <?php } 
						    if(empty($product[0]['Product']['name'])) {
						   	 	echo "<td>" .$productlinedata['product_sku'] . "</td><td>" . $productlinedata['product_name'] . "</td>";        
						   	 } ?>
							<td><?php echo h($productlinedata['quantity']); ?></td>
				            <?php if(sizeof($this->params['pass']) != 0) { ?>   
							<td><?php if(isset($productlinedata['tquantity'])) echo h($productlinedata['tquantity']); ?>&nbsp;</td>
							<td><?php if(isset($productlinedata['cum_qty'])) echo h($productlinedata['cum_qty']); ?>&nbsp;</td>	
				            <?php } ?>
							<td><?php echo $productlinedata['creator']; ?></td>
							<td><a href="#" id="<?php echo $key; ?>" class="remarks-editable" data-title="Enter remarks" data-type="text" data-pk="<?php echo h($productlinedata['id']); ?>" data-url="<?php echo Router::url('/orders_lines/updateRemarks', true); ?>" data-title="Enter username"><?php echo h($productlinedata['comments']); ?><span style="float: right;"><i class="fa fa-pencil"></i></span></a></td>
							<td><?php echo h($productlinedata['date']); ?>&nbsp;</td>
									
							</tr>
							<?php 
							  $cum_qty = ($pid) ? (isset($productlinedata['cum_qty']) ? $productlinedata['cum_qty'] : '') : '';
							endforeach; 
							} else {
								echo "<tr><td align='center' colspan='8'><b>No Data Found</b></td></tr>";
							} ?>
							</tbody>
						</table>		
					</div>					

					<div class="tab-pane" id="tab_charts">
						<div class="portlet-body">
							<div id="chart_2" class="chart">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<!-- END PAGE CONTENT-->
	  </div>
	</div>	
	<?php if(count($productlines) !== 0) {	?>
	<ul class="pagination">
		<?php
			$paginator = $this->Paginator;
			$this->Paginator->options['url'] = array('controller' => 'orders_lines', 'action' => 'linesbyproduct/'.$pid.'/'.$cum_qty,'?' => array('location' => $location_id)); 
		    if($paginator->hasPrev()){
		        echo '<li class="prev"><a href="javascript:history.back()" rel="prev">Prev</a></li>';
		    }
	         
		    if($paginator->hasNext()){
		        echo $paginator->next("Next",array('tag' => 'li'));
		    }
		?>
	</ul>
	<?php } ?>
	<!-- END CONTENT -->
