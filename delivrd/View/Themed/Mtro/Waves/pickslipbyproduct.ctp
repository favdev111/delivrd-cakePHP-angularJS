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
							<th>
								#
							</th>
							<th class="hidden-480">
								 Product Name
							</th>
							<th class="hidden-480">
								SKU
							</th>
							<th class="hidden-480">
								 Quantity
							</th>
							<th class="hidden-480">
								 Bin
							</th>
							<th>
								 Image
							</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($picklines as $key=>$pickline): ?>
						<tr>
							<td>
								 <?php echo $key; ?>
							</td>
							
							<td class="hidden-480"> 
								<?php echo h($pickline['productname']); ?>
							</td>
							<td class="hidden-480"> 
								<?php echo h($pickline['productsku']); ?>
							</td>
							<td class="hidden-480">
								 <?php echo h($pickline['pickquantity']); ?>
							</td>
							<td class="hidden-480">
								 <?php echo h($pickline['bin']); ?>
							</td>
							<td>
								 <IMG SRC='<?php echo h($pickline['imageurl']); ?>' WIDTH='64' HEIGHT='64'>
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
