<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE HEADER-->
			<h3 class="page-title">
			Edit Order
			</h3>
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->
			<?php echo $this->Form->create($model); ?>
			
			<div class="row">
				<div class="col-md-12">
					<!-- Begin: life time stats -->
					<div class="portlet">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-shopping-cart"></i>Update address data
							</div>
							<div class="actions">
								<a href="#" class="btn default yellow-stripe">
								<i class="fa fa-angle-left"></i>
								<span class="hidden-480">
								Back </span>
								</a>
								<button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
								<div class="btn-group">
										
									<a class="btn default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
									<i class="fa fa-cog"></i>
								
									<span class="hidden-480">
									Tools </span>
									<i class="fa fa-angle-down"></i>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
											<a href="#">
											Export to Excel </a>
										</li>
										<li>
											<a href="#">
											Export to CSV </a>
										</li>
										<li>
											<a href="#">
											Export to XML </a>
										</li>
										<li class="divider">
										</li>
										<li>
											<a href="#">
											Print Invoice </a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="portlet-body">
		
										<div class="row">
											<div class="col-md-6 col-sm-12">
												<div class="portlet yellow-crusta box">
													<div class="portlet-title">
														<div class="caption">
															<i class="fa fa-cogs"></i>Order Details
														</div>
														<div class="actions">
															<a href="#" class="btn btn-default btn-sm">
															<i class="fa fa-pencil"></i> Edit </a>
														</div>
													</div>
													<div class="portlet-body">
														<div class="row static-info">
															<div class="col-md-5 name">
																 Company Name:
															</div>
															<div class="col-md-7 value">
																 <?php echo $this->Form->input('User.company',array('label' => false, 'class' => 'form-control input-medium')); ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Street Address:
															</div>
															<div class="col-md-7 value">
																 <?php echo $this->Form->input('User.street',array('label' => false, 'class' => 'form-control input-medium')); ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 City:
															</div>
															<div class="col-md-7 value">
																 <?php echo $this->Form->input('User.city',array('label' => false, 'class' => 'form-control input-medium')); ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 State:
															</div>
															<div class="col-md-7 value">															
																<?php echo $this->Form->input('User.state_id',array('label' => false)); ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Zip:
															</div>
															<div class="col-md-7 value">															
																<?php echo $this->Form->input('User.zip',array('label' => false)); ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Country:
															</div>
															<div class="col-md-7 value">															
																<?php echo $this->Form->input('User.country_id',array('label' => false)); ?>
															</div>
														</div>
						
														
							</form>				
						</div>
					</div>
					<!-- End: life time stats -->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
