<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE CONTENT-->

			<?php echo $this->Session->flash(); ?>
			<div class="row">
				<div class="col-md-12">
				<?php echo $this->Form->create('Color', array('class' => 'form-horizontal form-row-seperated'));
				echo $this->Form->input('id',array('hidden' => true));
			
				 ?>			
						<div class="portlet box blue-steel">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-exchange"></i>
									Add Color
								</div>
								<div class="actions btn-set">
									<button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
									<button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>	
								</div>
							</div>
							
							<div class="portlet-body">
								<div class="row">
											<div class="col-md-6 col-sm-12">
												<div class="portle grey-gallery box">
													<div class="portlet-title">
														<div class="caption">
															<i class="fa fa-cogs"></i> Details
														</div>
														
													</div>
													<div class="portlet-body">
													<div class="row static-info">
															
															<div class="col-md-5 name">
																 Name:
															</div>
															<div class="col-md-7 value">
																 <?php  echo $this->Form->input('name',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
															</div>
														</div>
													<div class="row static-info">
															
															<div class="col-md-5 name">
																 Description:
															</div>
															<div class="col-md-7 value">
																 <?php  echo $this->Form->input('description',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
															</div>
														</div>
														
														<div class="row static-info">
															
															<div class="col-md-5 name">
																HTML Code:
															</div>
															<div>
															
															<div class="col-md-5">
																<input type="text" id="htmlcode" name="data[Color][htmlcode]" class="form-control demo" data-position="bottom left" value=<?php  echo $this->request->data['Color']['htmlcode']; ?>>
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
