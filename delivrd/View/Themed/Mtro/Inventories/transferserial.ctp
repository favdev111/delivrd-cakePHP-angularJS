
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
			<?php if($this->Session->read('showtours') == 1) { ?>
			<div class="portlet light bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa graduation-cap"></i>Inventory Page Tour</div>
                                    <div class="tools">
                                        <a href="javascript:;" class="collapse"> </a>
                                        <a href="/users/edit" class="config"> </a>
                                        <a href="javascript:;" class="reload"> </a>
                                        <a href="javascript:;" class="remove"> </a>
                                    </div>
                                </div>
                                <div class="portlet-body">
									<div id="takeTheTour" class="btn btn red dismissable">Take Page Tour</div>  
                  If you want to disable page tour, <a href='/users/edit/'><U>change your settings</U></a>.
		
            </div>
                        </div>
            <?php } ?>
			
			<!-- BEGIN PAGE CONTENT-->
			<?php echo $this->Session->flash(); ?>
			<div class="row">
				<div class="col-md-8">
				<?php echo $this->Form->create('Inventory', array('class' => 'form-horizontal form-row-seperated'));
				echo $this->Form->input('id',array( 'hidden' => true ));
			
				 ?>
					
				
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-barcode"></i> Transfer Serial
								</div>
								<div class="actions btn-set">
									<button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>

									<script>
									function goBack() {
										window.history.back()
									}
									</script>	
									<button class="btn green" type="submit" id='clicksave'><i class="fa fa-check"></i> Save</button>
									
									
								</div>
							</div>
						
							<div class="portlet-body">
								<div class="form-group">
													<label class="col-md-4 control-label">Serial Number: 
                                                                                                             <span class="required">
																* </span>
													</label>
													<div class="col-md-4">
														<?php echo $this->Form->input('serialnumber',array('label' => false, 'class' => 'form-control','div' =>false, 'required' => 'true')); ?>
													</div>
												</div>
											
								<div class="form-group">
									<label class="col-md-4 control-label">Transaction: 
                                                                                             <span class="required">
												* </span>
									</label>
									<div class="col-md-4">
										<?php echo $this->Form->input('ttype',array('label' => false,'class' => 'form-control select2me','id' => 'grgi','div' =>false, 'options' => array('IS' => 'Issue' ,'TR' => 'Transfer'),'empty' => 'Select...')); ?>
									</div>
								</div>


											</div>
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

