<?php ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
<!-- BEGIN PAGE CONTENT-->
<div class="invoice">
				<div class="row invoice-logo">
					<div class="col-xs-6">
						<h3>Order Number <?php echo h($order['Order']['external_orderid']); ?></h3>
						
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-xs-4">
						<h3>Order Info:</h3>
						<ul class="list-unstyled">
							<li>
								<strong>Order Number:</strong> <?php echo h($order['Order']['external_orderid']); ?>
							</li>
							<li>
								<strong>Created On:</strong> <?php echo $this->Admin->localTime("%Y-%m-%d %H:%M:%S", strtotime($order['Order']['created'])); ?>
							</li>
							<li>
								<strong>Ordered Through:</strong> <?php echo h($order['Schannel']['name']); ?>
							</li>
							
						</ul>
					</div>
					
					<div class="col-xs-4">
						<h3>Ship To:</h3>
						<ul class="list-unstyled">
							<li>
								<strong>Name:</strong> <?php echo h($order['Order']['ship_to_customerid']); ?>
							</li>
							<li>
								<strong>Street:</strong> <?php echo h($order['Order']['ship_to_street']); ?>
							</li>
							<li>
								<strong>City:</strong> <?php echo h($order['Order']['ship_to_city']); ?>
							</li>
							<li>
								<strong>Zip:</strong> <?php echo h($order['Order']['ship_to_zip']); ?>
							</li>
							<?php if($order['Order']['state_id'] != 1) { ?>
							<li>
								<strong>State:</strong> <?php echo h($order['State']['name']); ?>
							</li>
							<?php } ?>
							<li>
								<strong>Country:</strong> <?php echo h($order['Country']['name']); ?>
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
							<th>
								 #
							</th>
							<th class="hidden-480">
								 Description
							</th>
							<th class="hidden-480">
								 Quantity
							</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($order['OrdersLine'] as $key=>$ordersLine): ?>
						<?php if($ordersLine['Product']['consumption'] == false) { ?>
						<tr>
							
							<td>
								 <?php echo ($key + 1); ?>
							</td>
							
							<td class="hidden-480"> 
								  <?php echo h($ordersLine['Product']['description']); ?>
							</td>
							<td class="hidden-480">
								 <?php echo h($ordersLine['quantity']); ?>
							</td>
						</tr>
						<?php } ?>
						<?php endforeach; ?>
						</tbody>
						</table>
					</div>
				</div>
				<br />
					<br />
					<br />
					<br />
				<div class="row">
					<div class="col-xs-4">
					<a class="btn btn-lg blue hidden-print margin-bottom-5" onclick="javascript:window.print();">
						Print <i class="fa fa-print"></i>
						</a>
					</div>	
					<div class="col-xs-4">
					
					</div>	
					
					
					
				</div>
			</div>
			<!-- END PAGE CONTENT-->
			</div>
	</div>
	<!-- END CONTENT -->
