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
						<h3>Inventory List</h3>	
					</div>
				</div>
				<hr/>
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
								Inventory Quantity
							</th>
							<th class="hidden-480">
								Inventory Location
							</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($inventory as $key => $listing): ?>
						<tr>
							<td>
								 <?php echo $key + 1; ?>
							</td>
							
							<td class="hidden-480"> 
								<?php echo h($listing['Product']['name']); ?>
							</td>
							<td class="hidden-480"> 
								<?php echo h($listing['Product']['sku']); ?>
							</td>
							<td class="hidden-480">
								 <?php echo h($listing['Inventory']['quantity']); ?>
							</td>
							<td class="hidden-480">
								 <?php echo h($listing['Warehouse']['name']); ?>
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
