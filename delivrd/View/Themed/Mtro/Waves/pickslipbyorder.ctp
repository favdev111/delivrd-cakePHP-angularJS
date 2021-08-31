<style>
@media print {

  @page {
        size: auto;   /* auto is the initial value */
        margin: 5mm;  /* this affects the margin in the printer settings */
    }
	
}
</style>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
			
			<!-- BEGIN PAGE CONTENT-->
			<div class="invoice">
				<div class="row invoice-logo">
					<div class="col-xs-6">
						<h3>Wave Number <?php echo $wave['Wave']['id']; ?></h3>
						
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-xs-4">
						<h3>Wave Info:</h3>
						<ul class="list-unstyled">
							<li>
								<strong>Created:</strong> <?php echo $this->Admin->localTime("%Y-%m-%d %H:%M:%S", strtotime($wave['Wave']['created'])); ?>
							</li>
							<li>
								<strong>No Of Lines:</strong> <?php echo sizeof($wave['OrdersLine']); ?>
							</li>
							<li>
								<strong>Courier:</strong> <?php echo h($wave['Courier']['name']); ?>
							</li>
							
						</ul>
					</div>
					
				</div>
				<br />
				<br />
				<br />
				<br />
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-striped table-hover">
						<thead>
						<tr>
							<th class="hidden-480">
								 Product Name
							</th>
							<th class="hidden-480">
								 SKU
							</th>
							<th class="hidden-480">
								 Quantity
							</th>
							<th>
								 Bin
							</th>
							<th>
								 Image
							</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($wave['OrdersLine'] as $key => $pickline):
						$order_id[$key] = $pickline['order_id'];
						if((isset($order_id[$key - 1]) && $pickline['order_id'] != $order_id[$key - 1]) || ($key == 0)) {
							$addr = (!empty($pickline['Order']['ship_to_street'])) ? $pickline['Order']['ship_to_street'] . ',' : ''; 
							$addr .= (!empty($pickline['Order']['ship_to_city'])) ? $pickline['Order']['ship_to_city'] . ',' : ''; 
							$addr .= (!empty($pickline['Order']['ship_to_zip'])) ? $pickline['Order']['ship_to_zip'] . ',' : ''; 
							$addr .= (!empty($pickline['Order']['ship_to_stateprovince'])) ? $pickline['Order']['ship_to_stateprovince'] . ',' : ''; 
							$addr .= (!empty($pickline['Order']['Country']['name'])) ? $pickline['Order']['Country']['name'] . ',' : '';
							echo '<tr><td colspan="5"><ul class="list-unstyled">
								<li>
									<strong>Customer Name:</strong> '.$pickline['Order']['ship_to_customerid'].'
								</li>
								<li>
									<strong>Customer Address:</strong> '.rtrim($addr, ",").'
								</li>
								<li>
									<strong>Sales channel:</strong> '.$pickline['Order']['Schannel']['name'].'
								</li>
								<li>
									<strong>Order ID:</strong> '.$pickline['Order']['id'].'
								</li>
								</ul></td></tr>';
							} ?>
						<tr>
							<td class="hidden-480"> 
								  <?php echo h($pickline['Product']['name']); ?>
							</td>
							<td class="hidden-480"> 
								  <?php echo h($pickline['Product']['sku']); ?>
							</td>
							<td class="hidden-480 text-center"> 
								  <?php echo h($pickline['quantity']); ?>
							</td>
							<td class="hidden-480">
								<?php $prefix = $bin = '';
							    foreach($pickline['Product']['Bin'] as $key => $binList):
							        $bin .= $prefix . $binList['title'];
							        $prefix = ', ';
							    endforeach; ?> 
								 <?php echo h($bin); ?>
							</td>
							<td>
								<IMG SRC='<?php echo h($pickline['Product']['imageurl']); ?>' WIDTH='64' HEIGHT='64'> 
							</td>
						</tr>
						<?php endforeach; ?>
						</tbody>
						</table>
					</div>
				</div>
				<br />
				<br />
				<br />
				<br />
	
				</div>
		
			<div class="row">
					<div class="col-xs-4">
					<a class="btn btn-lg blue hidden-print margin-bottom-5" onclick="javascript:window.print();">
						Print <i class="fa fa-print"></i>
						</a>
					</div>	
					</div>
			<!-- END PAGE CONTENT-->
			</div>
	</div>
	<!-- END CONTENT -->