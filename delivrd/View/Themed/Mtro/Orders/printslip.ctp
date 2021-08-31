<?php ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
<!-- BEGIN PAGE CONTENT-->
<div class="invoice">
				<div class="row invoice-logo">
					<div class="col-xs-6">
						<h3>Order Number <?php echo $order['Order']['id']; ?></h3>
						
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-xs-4">
						<h3>Order Info:</h3>
					<?php $addr = (!empty($order['Order']['ship_to_street'])) ? $order['Order']['ship_to_street'] . ',' : ''; 
						  $addr .= (!empty($order['Order']['ship_to_city'])) ? $order['Order']['ship_to_city'] . ',' : ''; 
						  $addr .= (!empty($order['Order']['ship_to_zip'])) ? $order['Order']['ship_to_zip'] . ',' : ''; 
						  $addr .= (!empty($order['Order']['ship_to_stateprovince'])) ? $order['Order']['ship_to_stateprovince'] . ',' : ''; 
						  $addr .= (!empty($order['Order']['Country']['name'])) ? $order['Order']['Country']['name'] . ',' : '';
							echo '<ul class="list-unstyled">
								<li>
									<strong>Customer Name:</strong> '.$order['Order']['ship_to_customerid'].'
								</li>
								<li>
									<strong>Customer Address:</strong> '.rtrim($addr, ",").'
								</li>
								<li>
									<strong>Sales channel:</strong> '.$order['Schannel']['name'].'
								</li>
								<li>
									<strong>Order ID:</strong> '.$order['Order']['id'].'
								</li>
								</ul>'; ?>
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
								 Remarks
							</th>
							<th>
								 Image
							</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($order['OrdersLine'] as $key => $pickline): //pr($pickline);die;?>
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
							<td class="hidden-480 text-center"> 
								  <?php echo h($pickline['comments']); ?>
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
