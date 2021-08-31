<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content">
			
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-6 col-sm-12">
			  <div class="portlet green-jungle box">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-arrow-left"></i>Product to Receive <small></small>
					</div>
				</div>
				<div class="portlet-body">
				  <div class="tiles">
					<div class="tile image selected">
						<div class="tile-body">
							<img src="<?php  echo $product['imageurl'] ?>" alt="">
						</div>
						<div class="tile-object">
						
						</div>
					</div>
					<h2><?php echo h($product['description']); ?></h2>
				  </div>
			    </div>
			  </div>
			</div>
		</div>
		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-6 col-sm-12">
				<?php echo $this->Form->create('OrdersLine', array('class' => 'form-horizontal form-row-seperated')); ?>	
				<div class="portlet box grey-gallery">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cogs"></i>Details:
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
						<div class="row static-info">
							<div class="col-md-5 name">
                                <label class="control-label">Order Quantity:
                                </label>
							</div>
							<div class="col-md-7 value">
								 <?php  echo $this->Form->input('quantity',array( 'disabled' => 'disabled','label' => false, 'class' => 'form-control input-sm', 'autofocus' => 'autofocus' )); ?>
							</div>
						</div>
                        <?php if($this->Session->read('locationsactive') == 1) { ?>
                        <div class="row static-info">
							<div class="col-md-5 name">
                                <label class="control-label">Location:
                                <span class="required">* </span>
                                </label>
						    </div>
							<div class="col-md-7 value">
								 <?php  echo $this->Form->input('warehouse_id',array( 'label' => false, 'class' => 'form-control input-sm', 'div' =>false)); ?>
							</div>
					    </div>
                        <?php } ?>
						<div class="row static-info">
							<div class="col-md-5 name">
                                <label class="control-label">Received Quantity:
                                <span class="required">* </span>
                                </label>
							</div>
								<div class="col-md-7 value">
									 <?php  echo $this->Form->input('receivedqty',array( 'label' => false, 'class' => 'form-control', 'min' => 0, 'required' => 'true' )); ?>
								</div>
						</div>
                        <?php if ($this->Session->read('managedamaged') == 1) { ?>
							<div class="row static-info">			
								<div class="col-md-5 name">
									 Damaged Quantity:
								</div>
								<div class="col-md-7 value">
									 <?php  echo $this->Form->input('damagedqty',array( 'label' => false, 'class' => 'form-control','min' => 0,'required' => 'true' )); ?>
								</div>
							</div>
                            <?php } ?>
                            <div class="row static-info">
								<div class="col-md-5 name">Score:
								</div>
								<div class="col-md-7 value">
									 <?php echo $this->Form->input('score',array('label' => false,'class' => 'form-control input-sm select2me','div' =>false, 'options' => array('1' => '1 - Lowest','2' => '2 - Low','3' => '3 - Medium', '4' => '4 - Higher','5' => '5 - Highest'),'empty' => 'Select...')); ?>
								</div>
							</div>
                            <div class="row static-info">							
								<div class="col-md-5 name">
									 Remarks:
								</div>
								<div class="col-md-7 value">
									 <?php  echo $this->Form->input('receivenotes',array( 'label' => false, 'class' => 'form-control' )); ?>
								</div>
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
