<?php $this->AjaxValidation->active(); ?>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
			
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Session->flash(); ?>
					<!-- Begin: life time stats -->
					<div class="portlet box blue-chambray">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-list-ol"></i>Serial Transaction History
							</div>
						</div>
						<div class="portlet-body">
							<div class="table-container">
								<table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
								<thead>
								<tr role="row" class="heading">
									
									<th width="10%">
										 Transaction Type
									</th>
									<th width="15%">
										SKU
									</th>
									<th width="10%">
										 Product Name
									</th>
									<th width="15%">
										Location
									</th>
									<th width="15%">
										Date & Time
									</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($orderlines as $orderline): ?>
								<tr role="row">
								<td><?php echo ($orderline['OrdersLine']['type'] == 6 ? 'Receive To Inventory' : 'Issue From Inventory'); ?></td>
								<td><?php echo h($orderline['Product']['sku']); ?></td>
								<td><?php echo h($orderline['Product']['name']); ?></td>
								<td><?php echo h($orderline['Warehouse']['name']); ?></td>
								<td><?php echo h($orderline['OrdersLine']['modified']); ?></td>
								</tr>
								<?php endforeach; ?>
								</tbody>
								</table>

									<ul class="pagination">
										<?php $link = '?page=%d';
											$pagerContainer = '';   
											if( $totalPages != 0 ) 
											{
											  if( $page == 1 ) 
											  { 
											    $pagerContainer .= ''; 
											  } 
											  else 
											  { 
											    $pagerContainer .= sprintf('<li class="prev"><a href="' . $link . '">Prev</a></li>', $page - 1 ); 
											  }
											  if( $page == $totalPages ) 
											  { 
											    $pagerContainer .= ''; 
											  }
											  else 
											  { 
											    $pagerContainer .= sprintf('<li class="prev"><a href="' . $link . '"> Next </a></li>', $page + 1 ); 
											  }           
											}                   

										echo $pagerContainer; ?>
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
