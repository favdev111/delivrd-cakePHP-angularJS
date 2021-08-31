<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<?php echo $this->Session->flash(); ?>
			
			<!-- BEGIN PAGE CONTENT-->
			<?php echo $this->Form->create($model); ?>
              
			<div class="row">
				<div class="col-md-18">
					<!-- Begin: life time stats -->
					<div class="portlet">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-edit"></i>Update system settings
							</div>
							<div class="actions">
								<button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
									<script>
									function goBack() {
										window.history.back()
									}
									</script>
								<button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
								
							</div>
						</div>
						<div class="portlet-body">
		
										<div class="row">
											<div class="col-md-8 col-sm-18">
												<div class="portlet yellow-crusta box">
													<div class="portlet-title">
														<div class="caption">
															<i class="fa fa-cogs"></i>System Settings
														</div>
														
													</div>
													
															</div>
															</div>
														</div>
														
														
                                                                                                            <div class="row static-info">
															<div class="col-md-5 name">
																 Magento Username:
															</div>
															<div class="col-md-7 value">
																 <?php echo $this->Form->input('User.magentousername',array('label' => false, 'class' => 'form-control input-extra-large')); ?>
															</div>
														</div>
													
                                                                                                             <div class="row static-info">
															<div class="col-md-5 name">
																 Magento Password:
															</div>
															<div class="col-md-7 value">
																 <?php echo $this->Form->input('User.magentopassword',array('label' => false, 'class' => 'form-control input-extra-large')); ?>
															</div>
														</div>
														
                                                                                                            <div class="row static-info">
															<div class="col-md-5 name">
																 Magento URL:
															</div>
															<div class="col-md-7 value">
																 <?php echo $this->Form->input('User.magentourl',array('label' => false, 'class' => 'form-control input-extra-large')); ?>
															</div>
														</div>
                                                                                                            <div class="row static-info">
															<div class="col-md-5 name">
																 Woocommerce Key:
															</div>
															<div class="col-md-7 value">
																 <?php echo $this->Form->input('User.wooconsumerkey',array('label' => false, 'class' => 'form-control input-extra-large')); ?>
															</div>
														</div>
                                                                                                            <div class="row static-info">
															<div class="col-md-5 name">
																 Woocommerce Secret:
															</div>
															<div class="col-md-7 value">
																 <?php echo $this->Form->input('User.wooconsumersecret',array('label' => false, 'class' => 'form-control input-extra-large')); ?>
															</div>
														</div>
                                                                                                            
                                                                                                            <div class="row static-info">
															<div class="col-md-5 name">
																 Woocommerce url:
															</div>
															<div class="col-md-7 value">
																 <?php echo $this->Form->input('User.woourl',array('label' => false, 'class' => 'form-control input-extra-large')); ?>
															</div>
														</div>
                                                                                                             
                                                                                                            			
				
													
								<?php echo $this->Form->end(__d('users', '')); ?>		
						</div>
					</div>
					<!-- End: life time stats -->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
