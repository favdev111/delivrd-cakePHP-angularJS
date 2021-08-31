<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE HEADER-->
			<h3 class="page-title">Ebay Orders</h3>
			<!-- END PAGE HEADER-->
			
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Session->flash(); ?>
					<!-- Begin: life time stats -->
					<div class="portlet">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-shopping-cart"></i>Ebay Orders
							</div>
							<div class="actions">
							
								
								<div class="btn-group">
									<a class="btn default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
									<i class="fa fa-share"></i>
									<span class="hidden-480">
									Tools </span>
									<i class="fa fa-angle-down"></i>
									</a>
									<ul class="dropdown-menu pull-right">
										
									</ul>
								</div>
							</div>
						</div>
						<div class="portlet-body">
							<div class="table-container">
								
							<div class="row">
											<div class="col-md-6 col-sm-12">
												<div class="portlet yellow-crusta box">
													<div class="portlet-title">
														<div class="caption">
															<i class="fa fa-cogs"></i>Get Order from Ebay
														</div>
														
													</div>
													<div class="portlet-body">
														<div class="row static-info">
															<div class="col-md-5 name">
																 # of orders returned:
															</div>
															<div class="col-md-7 value">
																 <?php echo $ordersreturned ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 # of orders created:
															</div>
															<div class="col-md-7 value">
																 <?php echo $orderscreated; ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 # of orders with errors:
															</div>
															<div class="col-md-7 value">
																<?php echo $orderserrors ?>
															</div>
														</div>
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
