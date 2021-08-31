
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content">
		
		<!-- BEGIN PAGE CONTENT-->
		<div class="tiles">
			<div class="tile image selected">
				<div class="tile-body">
					<img src="<?php  echo h($product['imageurl']) ?>" height="256" width="256" alt="">
				</div>
				<div class="tile-object">
					<div class="name">
						
					</div>
				</div>
			</div>
		</div>
		<?php  echo h($product['description']) ?>

		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-12">
				<?php echo $this->Form->create('OrdersLine', array('class' => 'form-horizontal form-row-seperated', 'novalidate' => true)); ?>
					<?php echo $this->Form->input('id',array( 'hidden' => true )); ?>
					<div class="portlet">
						<div class="portlet-title">
							<div class="caption">
								
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
						
						
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="portlet box grey-gallery">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-cogs" ></i> Details
										</div>
									</div>
									<div class="portlet-body form">
										<div class="form-body">
											<div class="row static-info">
												<div class="col-md-5 name">
													Order Quantity:
												</div>
												<div class="col-md-7 value">
													<?php   echo $this->Form->input('quantity',array( 'disabled' => 'disabled','label' => false, 'class' => 'form-control input-sm' )); ?>
												</div>
											</div>
                                            <?php if($this->Session->read('locationsactive') == 1 || 1) { ?>
                                            <div class="row static-info">
												<div class="col-md-5 name">
                                                    <label class="control-label">Location: <span class="required">*</span></label>
												</div>
												<div class="col-md-7 value">
													<?php  echo $this->Form->input('warehouse_id',array( 'label' => false, 'class' => 'form-control input-sm', 'min' => 0, 'required' => 'true' )); ?>
												</div>
											</div>
										 	<?php } ?>
											<div class="row static-info">
												<div class="col-md-5 name">
                                                    <label class="control-label">Sent Quantity: <span class="required">*</span></label>       
												</div>
												<div class="col-md-7 value">
													<?php  echo $this->Form->input('sentqty',array( 'label' => false, 'class' => 'form-control input-sm', 'min' => 0, 'required' => 'true')); ?>
												</div>
											</div>
											<?php if ($this->Session->read('managedamaged') == 1) { ?>
											<div class="row static-info">
												<div class="col-md-5 name">
                                                    <label class="control-label">Damaged Quantity: <span class="required">*</span></label>   
												</div>
												<div class="col-md-7 value">
													<?php  echo $this->Form->input('damagedqty',array( 'label' => false, 'class' => 'form-control input-sm', 'min' => 0, 'required' => 'true' )); ?>
												</div>
											</div>
                                        	<?php } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
</div>
<!-- END CONTENT -->