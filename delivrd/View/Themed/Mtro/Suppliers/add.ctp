<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
			
			<!-- BEGIN PAGE CONTENT-->
			<?php echo $this->Session->flash(); ?>
			<div class="row">
				<div class="col-md-12">
				<?php echo $this->Form->create('Supplier', array('class' => 'form-horizontal form-row-seperated')); ?>			
						<div class="portlet box yellow-gold">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-exchange"></i>
									Add Supplier
								</div>
								<div class="actions btn-set">
									<button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
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
																Email:
															</label>
															</div>
															<div class="col-md-7 value">
															<?php  echo $this->Form->input('email',array('type' => 'text','label' => false, 'class' => 'form-control input-sm tags', 'data-role' => 'tagsinput', 'id' => 'multiple_email','placeholder' => 'add an email address')); ?>
															</div>
														</div>
														<div class="row static-info">
															
															<div class="col-md-5 name">
                                                             <label class="control-label">URL: 
																<span class="required">
                                                              * </span>                                        
															</div>
															<div class="col-md-7 value">
																 <?php  echo $this->Form->input('url',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
															</div>
														</div>
														<div class="row static-info">
															
															<div class="col-md-5 name">
																 <label class="control-label">Vendor is customer: 
																</label>
															</div>
															<div class="col-md-7 value">
																 <?php echo $this->Form->input('is_customer', array('label' => false, 'class' => 'md-check', 'div' => false)); ?>	
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
	<style>
	div.tagsinput input {
	  padding: 3px 6px;
	  width: auto !important;
	}

	</style>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
$(function() {
	var regex4 = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;// Email address
	$('#multiple_email').tagsInput({
		width: 'auto',
		'height' :'28px',
		pattern: regex4,
		'defaultText':'Enter an email address',
	});
});
<?php $this->Html->scriptEnd(); ?>