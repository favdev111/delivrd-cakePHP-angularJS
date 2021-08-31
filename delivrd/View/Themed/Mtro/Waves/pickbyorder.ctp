<?php $this->AjaxValidation->active(); ?>
<style>
.release-btns{
      margin-left: 44px;
}
.release-btns a{
      margin-left: 150px;
}
</style>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper" ng-controller="PickByOrder" ng-init = "Ordercount(<?php echo $id; ?>)" ng-cloak>
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
		  	<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
					<?php echo $this->html->link('<i class="fa fa-home"></i> Home', array('plugin' => false, 'controller' => 'Dash', 'action' => 'ofindex'), array('class' => '', 'escape' => false)); ?>
						<i class="fa fa-angle-right"></i>
						
					</li>
					<li>
						<a href="/waves/">Pick by order</a>
					</li>
				</ul>
				<?php
						if($this->Session->read('lefttopick') == -1)
						{
							$actioncolor = "green";
							$showactions = true;
						} else {
							$actioncolor = "grey-salt";
							$showactions = false;
						}
					
						?>
			<div class="page-toolbar">
				<div class="btn-group pull-right">
					<!-- <button type="button" class="btn btn-fit-height <?php echo $actioncolor; ?> dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
					Actions <i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu pull-right" role="menu">
					<?php if($showactions)
					{
						echo "<li>";
						echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print Shipping Label'), array('controller' => 'Labels', 'action' => 'shiplabel', $this->Session->read('shipmentid')),array('escape'=> false));
						echo "</li>";	
						echo "<li>";
						echo $this->Html->link(__('<i class="fa fa-print"></i> Print Packing Slip'), array('controller' => 'orders', 'action' => 'packinglist', $pickline['ordernumber']),array('target' => '_blank','escape'=> false));
						echo "</li>";
					}
					?>
					</ul> -->
				</div>
			</div>
		  </div>
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->
			<div class="row" id="counts_pick">
				<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat blue-madison">
						<div class="visual">
							<i class="fa fa-comments"></i>
						</div>
						<div class="details">
							<div class="number">
							{{count.total_order}}
								 <?php //echo $total_order; ?>
								 
							</div>
							<div class="desc">
								 Total orders to pick
							</div>
						</div>
						
					</div>
				</div>
				<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat red-intense">
						<div class="visual">
							<i class="fa fa-bar-chart-o"></i>
						</div>
						<div class="details">
							<div class="number">
							{{count.total_orderline}}
							
							</div>
							<div class="desc">
								 Total lines to pick
							</div>
						</div>
						
					</div>
				</div>
				<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat green-haze">
						<div class="visual">
							<i class="fa fa-shopping-cart"></i>
						</div>
						<div class="details">
							<div class="number">
							{{count.total_order - count.orderpicked}}
							</div>
							<div class="desc">
								 Orders picked
							</div>
						</div>
						
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat blue-madison">
						<div class="visual">
							<i class="fa fa-comments"></i>
						</div>
						<div class="details">
							<div class="number">{{count.total_line_current_order}}
								 
								 
							</div>
							<div class="desc">
								 Total lines for current order
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
							{{count.total_line_current_order - count.remain_orderline}}/{{count.total_line_current_order}}
							
							</div>
							<div class="desc">
								 Lines picked for current order
							</div>
						</div>
						
					</div>
				</div>
				
				
				
			<!-- END DASHBOARD STATS -->
				<div class="clearfix">
				</div>
			</div>
 
			<?php echo $this->Session->flash(); ?>
			<div id="flash"></div>
			


			<div class="row">
				<div class="col-md-6 col-sm-12" ng-hide="previous_product && previous_product != count.ordernumber">
					<div class="portlet yellow-crusta box">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-cogs"></i>Product to pick: <small></small>
							</div>
						</div>
						<div class="portlet-body">
							<div class="tiles">
								<div class="tile image selected">
									<div class="tile-body">
										<img src="{{count.imageurl}}" alt="">
									</div>
								<div class="tile-object">
								
								</div>
								</div>
								<h2>{{count.productname}}</h2>
									<span ng-if='count.productsku'><h4>{{count.label}} : {{count.productsku}}</h4></span>
							</div>
								
							<?php if(!empty($pickline['color'])) 
								echo "<li class='list-group-item' style='color:".$pickline['colorhtml'].";background-color:#".$pickline['colorhtml']."'>".$pickline['color']."<span class='badge'>".$pickline['color']."</span></li>"; ?>
								<?php if(!empty($pickline['size'])) 
								echo "<li class='list-group-item'>".$pickline['sizedescription']." <span class='badge'>".$pickline['size']."</span></li>"; ?>
						
						</div>
					</div>
				</div>
			
				<div class="col-md-6 col-sm-12" ng-hide="previous_product && previous_product != count.ordernumber">
					<div class="portlet yellow-crusta box" >
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-cogs"></i>Order Line Status
							</div>
						</div>
						<div class="portlet-body" style="height:170px">
							<div class="row static-info">
								<div class="col-md-5 name">
									Order Number
								</div>
							<div class="col-md-7 value">{{count.ordernumber}}
						<?php //echo	$pickline['ordernumber']; ?>
							</div>
							</div>
							
							<div class="row static-info">
								<div class="col-md-5 name">
									Line Number
								</div>
							<div class="col-md-7 value">{{count.orderlinenumber}}
							</div>

							</div>

							<div class="row static-info">
								<div class="col-md-5 name">
									Products Pick
								</div>
							<div class="col-md-7 value"> 
							{{count.sentqty}}/{{count.lineqty}}

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

<div class="row">
  <div class="col-md-6 col-sm-12">
  </div>
  <div class="col-md-2 col-sm-12">
 	<div id="input-error" style="color:red"></div>
 	<input type="hidden" value={{count.sentqty}} id="pdt_calc"> 
 	<input type="hidden" value={{count.lineqty}} id="count_lineqty"> 
 			<div class="form-group form-md-line-input form-md-floating-label" ng-show="previous_product && previous_product != count.ordernumber">
 			<div class="modal fade" id="continue-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			      </div>
			      <div class="modal-body release-btns">
			          <p>Order {{previous_product}} has been fully pick. Click next order to pick next order, or exit to leave picking screen.</p>
			          <a href="#" class="btn btn-md blue" ng-click = "Showform()">Next Order</a>
			          <a href="<?php echo Router::url(array('controller' => 'waves', 'action' => 'index'), true); ?>" class="btn btn-md blue">Exit</a>
			      </div>
			    </div>
			  </div>
			</div>	
			</div>


<?php
		
			echo $this->Form->create('', array('id' => 'pickbyorder', 'ng-submit' => 'Pickbyorder($event, '.$wave['Wave']['id']. ')', 'ng-hide' =>"previous_product && previous_product != count.ordernumber")); 
			echo $this->Form->hidden('locationid',array('value' => $wave['Wave']['location_id'], 'hidden' => true));
			echo $this->Form->input('id',array('value' => '{{count.lineqty}}', 'hidden' => true));	
			echo $this->Form->input('ordernumber',array('value' => '{{count.ordernumber}}', 'hidden' => true, 'label' => false));
			echo $this->Form->input('productid',array('value' => '{{count.productid}}', 'hidden' => true, 'label' => false));
			echo $this->Form->input('lineid',array('value' => '{{count.lineid}}', 'hidden' => true, 'label' => false));
			echo "<table><tr>";							
			if($scan['User']['pick_by_order'] == 1){
				$disable = true;
				$auto_focus = 'autofocus';
			}	
			else{
				$disable = false;
				$auto_focus = '';
			}	
				
			if($scan['User']['pick_by_order'] != 5 && $scan['User']['pick_by_order'] != 6 && $scan['User']['pick_by_order'] == 2)
			{
				echo '<div class="form-group form-md-line-input form-md-floating-label">';		
				echo $this->Form->input('scan',array('label' => false,'id' => 'track', 'disabled' => $disable, 'class' => 'form-control code-scan','div' => false, 'ng-keydown' => 'handle($event, '.$wave['Wave']['id']. ')'));
				echo '<label for="form_control_1">Product barcode</label>';
				echo '<span class="help-block">Scan the products bin number</span>';
				echo "</div>";
			}
			elseif($scan['User']['pick_by_order'] == 3 || $scan['User']['pick_by_order'] == 4){
				echo '<div class="form-group form-md-line-input form-md-floating-label">';		
				echo $this->Form->input('sku',array('label' => false,'id' => 'track', 'disabled' => $disable, 'class' => 'form-control code-scan','div' => false, 'ng-keydown' => 'handle($event, '.$wave['Wave']['id']. ')'));
				echo '<label for="form_control_1">Product barcode</label>';
				echo '<span class="help-block">Scan the products SKU/EAN number</span>';
				echo "</div>";
			}
			elseif($scan['User']['pick_by_order'] == 5 || $scan['User']['pick_by_order'] == 6)
			{
				echo '<div class="form-group form-md-line-input form-md-floating-label">';		
				echo $this->Form->input('bin',array('label' => false,'id' => 'track', 'class' => 'form-control code-scan','div' => false, 'ng-keydown' => 'handle($event, '.$wave['Wave']['id']. ')'));
				echo '<label for="form_control_1">Product BIN</label>';
				echo '<span class="help-block">Scan the products bin number</span>';
				echo "</div>";
				echo '<div class="form-group form-md-line-input form-md-floating-label">';		
				echo $this->Form->input('sku',array('label' => false,'id' => 'track1', 'class' => 'form-control code-scan','div' => false,'ng-keydown' => 'handle($event, '.$wave['Wave']['id']. ')'));
				echo '<label for="form_control_1">Product SKU</label>';
				echo '<span class="help-block">Scan the products sku number</span>';
				echo "</div>";
			}
		
				if($scan['User']['pick_by_order'] != 4 && $scan['User']['pick_by_order'] != 6)
				{	
					echo '<div class="count-input space-bottom"><div class="form-group form-md-line-input form-md-floating-label">';	
					echo '<a class="incr-btn" data-action="decrease" href="#">–</a>';
					echo $this->Form->input('sentqty',array('value' => '{{count.lineqty}}','max' => '{{count.lineqty}}','label' => false,'id' => 'sentqty','disabled' =>false, 'class' => 'quantity form-control','div' => false, 'after' => '', $auto_focus));	
					echo '<a class="incr-btn" data-action="increase" href="#">&plus;</a>';
					echo '<span class="help-block">Product quantity shipped</span>';
					echo "</div></div>";
				}
				else{
					echo '<div class="form-group form-md-line-input form-md-floating-label">';	
					echo $this->Form->input('sentqty',array('value' => '{{count.sentqty}}/{{count.lineqty}}','label' => false,'disabled' => true,'id' => 'qty_fix', 'data-val' => 1, 'class' => 'form-control','div' => false));
					echo '<label for="form_control_1"></label>';
					echo '<span class="help-block">Product shipped</span>';
					echo "</div>";
				}
				
				echo '<div class="form-group form-md-line-input form-md-floating-label">';
					echo '<button class= "btn btn-fit-height green">SUBMIT</button>';
					echo "</div>";

			
			echo $this->Form->end();

			?>
			</table>
					<!-- End: life time stats -->
				</div>
				</div>
		

			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<div id="lefttopick" data-value="<?php echo $this->Session->read('lefttopick') ?>"></div>
	<div id="sameshipment" data-value="<?php echo $this->Session->read('sameshipment') ?>"></div>
	<?php echo "lfet ".$this->Session->read('lefttopick') ?>
	<!-- END CONTENT -->
	<script>
		$(function() {
			$('#continue-modal').modal('show'); 
		});
	</script>

