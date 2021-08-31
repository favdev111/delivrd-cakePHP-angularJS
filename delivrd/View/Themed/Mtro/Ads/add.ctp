<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE CONTENT-->
			<?php echo $this->Session->flash(); ?>
			<div class="row">
				<div class="col-md-12">
				<?php echo $this->Form->create('Ad', array('class' => 'form-horizontal form-row-seperated'));
				
			
				 ?>			
						<div class="portlet box yellow-saffron">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-truck"></i>
									Add Ads
								</div>
								<div class="actions btn-set">
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
											<div class="col-md-6 col-sm-12">
												<div class="portlet grey-gallery box">
													<div class="portlet-title">
														<div class="caption">
															<i class="fa fa-cogs"></i> Details
														</div>
														
													</div>
													<div class="portlet-body">
													<div class="row static-info">
															
															<div class="col-md-5 name">
																 <label class="control-label">Name: 
																<span class="required">
																* </span>
																</label>
															</div>
															<div class="col-md-7 value">
																 <?php  echo $this->Form->input('name',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
															</div>
                                                                                                        </div>
                                                                                                            <div class="row static-info">
                                                                                                                         <div class="col-md-5 name">
																 <label class="control-label">URL: 
																<span class="required">
																* </span>
																</label>
															</div>
                                                                                                                        <div class="col-md-7 value">
																 <?php  echo $this->Form->input('url',array( 'label' => false, 'class' => 'form-control input', 'type' => 'textarea' )); ?>
															</div>
                                                                                                            </div>
                                                                                                               <div class="row static-info">
                                                                                                                        <div class="col-md-5 name">
																 <label class="control-label">Pretext: 
																<span class="required">
																* </span>
																</label>
															</div>
                                                                                                                        <div class="col-md-7 value">
																 <?php  echo $this->Form->input('pretext',array( 'label' => false, 'class' => 'form-control input' ,'type' => 'textarea')); ?>
															</div>
                                                                                                                   </div>
                                                                                                                      <div class="row static-info">
                                                                                                                        <div class="col-md-5 name">
																 <label class="control-label">Controller: 
																<span class="required">
																* </span>
																</label>
															</div>
                                                                                                                         <div class="col-md-7 value">
																 <?php  echo $this->Form->input('rel_controller',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
															</div>
                                                                                                                        </div>
                                                                                                                        <div class="row static-info">
                                                                                                                        <div class="col-md-5 name">
																 <label class="control-label">View: 
																<span class="required">
																* </span>
																</label>
															</div>
                                                                                                                         <div class="col-md-7 value">
																 <?php  echo $this->Form->input('rel_view',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
															</div>
                                                                                                                         </div>
                                                                                                                        <div class="row static-info">
                                                                                                                        <div class="col-md-5 name">
																 <label class="control-label">View: 
																<span class="required">
																* </span>
																</label>
															</div>
                                                                                                                         <div class="col-md-7 value">
																 <?php echo $this->Form->input('position',array('label' => false,'class' => 'form-control','div' =>false, 'options' => array('Header' => 'Header','Footer' => 'Footer','SideBar' => 'SideBar','ProductResults' => 'ProductResults'),'empty' => '(choose one)')); ?>
															</div>
                                                                                                                         </div>
														</div>
													
										
									</div>
									
						</div>
					</form>
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
