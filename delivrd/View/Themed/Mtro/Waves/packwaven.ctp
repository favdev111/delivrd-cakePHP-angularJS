<?php $this->AjaxValidation->active(); ?>
<style>
.form-group.form-md-line-input .form-control
	{
	font-size: 24px;
	}
	
	.help-block
	{
	font-size: 24px;
	}
</style>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
		   	<?php echo $this->element('expirytext'); ?>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<?php echo $this->html->link('<i class="fa fa-home"></i> Home', array('plugin' => false, 'controller' => 'Dash', 'action' => 'ofindex'), array('class' => '', 'escape' => false)); ?>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<a href="/waves/">Waves</a>
					</li>
				</ul>
			
				<div class="page-toolbar">
					<div class="btn-group pull-right">
						<button type="button" class="btn btn-fit-height dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
						Actions <i class="fa fa-angle-down"></i>
						</button>
						<ul class="dropdown-menu pull-right" role="menu">
						
						</ul>
					</div>
				</div>
			</div>
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat blue-madison">
						<div class="visual">
							<i class="fa fa-comments"></i>
						</div>
						<div class="details">
							<div class="number">
								<?php echo $wavelinespacked."/".$wavelinescount; ?>
							</div>
							<div class="desc">
								 Packed Lines
							</div>
						</div>
						
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat red-intense">
						<div class="visual">
							<i class="fa fa-bar-chart-o"></i>
						</div>
						<div class="details">
							<div class="number">
								<?php
								
									if(isset($trackingnumber))
									{
										echo $trackingnumber;
									} else {
										echo "N/A";
										} ?>
							</div>
							<div class="desc">
								 Shipment No.
							</div>
						</div>
						
					</div>
				</div>
				
			<!-- END DASHBOARD STATS -->
			<div class="clearfix">
			</div>
			<?php echo $this->Session->flash(); 
			if(!(isset($gettrackingnumber))) { ?>
			<div class="row">
				<div class="col-md-4 col-sm-12">
					<div class="portlet yellow-crusta box">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-cogs"></i>Product to pack: <small></small>
							</div>
						</div>
						<div class="portlet-body">
							<div class="tiles">
							<div class="tile image selected">
								<div class="tile-body">
									<img src="<?php echo $pickline['imageurl'] ?>" alt="">
								</div>
							<div class="tile-object">
							
							</div>
							</div>
							<H2><?php echo $pickline['productname']; ?></h2>
							</div>
								
								<?php if(!empty($pickline['color'])) 
										echo "<li class='list-group-item' style='color:".$pickline['colorhtml'].";background-color:#".$pickline['colorhtml']."'>".$pickline['color']."<span class='badge'>".$pickline['color']."</span></li>"; ?>
										<?php if(!empty($pickline['size'])) 
										echo "<li class='list-group-item'>".$pickline['sizedescription']." <span class='badge'>".$pickline['size']."</span></li>"; ?>
						
					</div>
				</div>
			</div>
			<?php if(!empty($pickline['packmaterialimageurl']) && !empty($pickline['packmaterialdescription'])) { ?>
			<div class="col-md-4 col-sm-12">
				<div class="portlet yellow-crusta box">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cogs"></i>Packing Material:
						</div>
					</div>
					<div class="portlet-body">
						<div class="tiles">
						<div class="tile image selected">
							
								<div class="tile-body">
								
								</div>
								<div class="tile-object">
								</div>
							</div>
							<H2></h2>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="col-md-4 col-sm-12">
				<div class="portlet yellow-crusta box">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cogs"></i>Order Line Status
						</div>
					</div>
					<div class="portlet-body">
						<div class="row static-info">
							<div class="col-md-5 name">
								Order Number
							</div>
						<div class="col-md-7 value">
					<?php echo	$pickline['ordernumber'] ?>
						</div>
						</div>
						
						<div class="row static-info">
							<div class="col-md-5 name">
								Line Quantity
							</div>
						<div class="col-md-7 value">
						<?php echo $pickline['pickquantity'] ?>
						</div>
						</div>
											
												
					</div>
				</div>
			</div>				
</div>

<?php
	if($this->Session->read('showvariants') == 4)
	{
	echo "<table class='table table-hover dataTable no-footer' id='datatable_products' aria-describedby='datatable_products_info' role='grid'>
			<thead>
			<tr style='font-size: 16px;line-height:0.8;'>
				<th>Color</th>
				<th>Size</th>
			</tr>
			</thead>
		<tbody>
			<tr style='font-size: 20px;line-height:1;'>
				<td style='background-color:".$pickline['colorhtml'].";'>".$pickline['color']."</td>
				<td>".$pickline['size']."</td>
			</tr>
		</table>";

}
?>

	
<?php } ?>
	<div style='width: 450px;display: block;margin-left: auto;margin-right: auto;font-size: 24px'>
<?php
		echo $this->Form->create(); 
		if(isset($ordernumber))
			echo $this->Form->input('ordernumber',array('value' => $ordernumber, 'hidden' => true, 'label' => false));
		if(isset($pickline['productid']))	
			echo $this->Form->input('productid',array('value' => $pickline['productid'], 'hidden' => true, 'label' => false));
		if(isset($pickline['lineid']))	
			echo $this->Form->input('lineid',array('value' => $pickline['lineid'], 'hidden' => true, 'label' => false));
		if(isset($trackingnumber))	
			echo $this->Form->input('trackingnumber',array('value' => $trackingnumber, 'hidden' => true, 'label' => false));
		echo "<table><tr>";	
		if(isset($gettrackingnumber))
		{						
		/*	echo "<td>".$this->Form->label('trackingnumber','Tracking Number','label')."</td>";
			echo "<td>".$this->Form->label('productscan','SKU/Serial/EAN/ISBN','label')."</td>";
			echo "</tr><tr>";
			echo "<td>".$this->Form->input('trackingnumber',array('label' => false, 'class' => 'track','div' => false))."</td>";		
			echo "<td>".$this->Form->input('productscan',array('label' => false, 'class' => 'productid','div' => false))."</td>";
			  */
			echo '<div class="form-group form-md-line-input form-md-floating-label">';		
			echo $this->Form->input('newtrackingnumber',array('label' => false,'id' => 'track', 'class' => 'form-control','div' => false));
			echo '<label for="form_control_1">Tracking number</label>';
			echo '<span class="help-block" style="font-size:24px;">Scan barcode of tracking number</span>';
			echo "</div>";
			
		} 
		
		if(isset($pickline))
		{
			echo '<div class="form-group form-md-line-input form-md-floating-label">';	
				echo $this->Form->input('productscan',array('label' => false,'id' => 'productid', 'class' => 'form-control','div' => false));
			echo '<label for="form_control_1">Product barcode</label>';
			echo '<span class="help-block" >Scan the products SKU/Serial/EAN/UPC barcode</span>';
			echo "</div>";
			echo '<div class="form-group form-md-line-input form-md-floating-label">';	
				echo $this->Form->input('sentqty',array('label' => false,'id' => 'sentqty', 'class' => 'form-control','div' => false, 'value' => $pickline['pickquantity']));
		
			echo '<span class="help-block">Product Quantity Shipped</span>';
			echo "</div>";
		}
		
	//	if(isset($weighton))
	//	{
			
	//		echo '<div class="form-group form-md-line-input form-md-floating-label">';	
	//			echo $this->Form->input('weight',array('label' => false,'id' => 'productid', 'class' => 'form-control','div' => false));
	//		echo '<label for="form_control_1">Shipment weight</label>';
	//		echo '<span class="help-block">Weigh the packed products and enter the weight</span>';
	//		echo "</div>";
	//		
	//	}
		// echo "lef to pick is.".$this->Session->read('lefttopick');
	/*	if(1 == -1)
		{
					$options = array(
				'label' => 'Next Shipment',
				'div' => array(
					'class' => 'btn blue',
				)
);
			echo $this->Form->end($options);
			echo $this->Form->input('printed',array('value' => true, 'hidden' => true,'label' => false));	
			
	
			
			
		} */
	
		if(1 > -1)
			echo $this->Form->end(__('Submit'));
			
			
		?>
	
</table>
					<!-- End: life time stats -->
				</div>
		

			<!-- END PAGE CONTENT-->
		</div>
	</div>
	
	<!-- END CONTENT -->

