<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
			
			<!-- BEGIN PAGE CONTENT-->
			<?php echo $this->Session->flash(); ?>
			<div class="row">
				<div class="col-md-12">
				<?php echo $this->Form->create('Resource', array('class' => 'form-horizontal form-row-seperated'));
				if(!empty($this->request->data['Resource']['id'])) {
				  echo $this->Form->input('id',array('hidden' => true, 'value' => $this->request->data['Resource']['id']));
				}
					?>			
						<div class="portlet box yellow-gold">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-exchange"></i>
									<?php echo $title; ?>
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
                                                                                                                            <label class="control-label">
																Description:
                                                                                                                                 </label>
															</div>
															<div class="col-md-7 value">
																 <?php  echo $this->Form->textarea('description',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
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
