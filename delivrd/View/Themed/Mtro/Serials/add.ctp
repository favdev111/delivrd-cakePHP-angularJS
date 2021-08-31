<?php 
	$prodid = $this->Session->read('prodid');
	$ordid = $this->Session->read('ordid');
	$imgurl = $this->Session->read('imgurl');
	$recievedqty = $this->Session->read('recievedqty');
?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
			
			<!-- BEGIN PAGE CONTENT-->
			<?php // currently we do not show image
                              if(1 == 2)
			{
				?>
			<div class="tiles">
					<div class="tile image selected">
					<div class="tile-body">
						<img src="<?php if($this->Session->read('prodid') != null) echo $product['Product']['imageurl'] ?>" height="512" width="512" alt="">
					</div>
					<div class="tile-object">
						<div class="name">
							<?php echo $product['Product']['name'] ?>
						</div>
					</div>
				</div>

				</div>
				<?php 
			}
				?>
			<?php echo $this->Session->flash(); ?>
			<div class="row">
				<div class="col-md-12">
				<?php echo $this->Form->create('Serial', array('class' => 'form-horizontal form-row-seperated')); ?>
					
				
						<div class="portlet box blue-chambray">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-list-ol" ></i>
										Add Serial Number
									
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
									<div class="portlet box grey-gallery">
									<div class="portlet-title">
										<div class="caption">
										<i class="fa fa-cogs" ></i>
										Details
										</div>
									
									</div>
									<div class="portlet-body form">
										<div class="form-body">
												<?php if($this->Session->read('prodid') != null) {?>
							<div class="row static-info">
								<div class="col-md-5 name">
									 Product Name:
								</div>
								<div class="col-md-7 value">
									  <?php echo $product['Product']['name']; ?>
								</div>
							</div>
							<?php  } ?>
							<?php if($this->Session->read('prodid') != null && $this->Session->read('ordid') != null) { ?>
							<div class="row static-info">
								<div class="col-md-5 name">
									 Quantity Received:
								</div>
								<div class="col-md-7 value">
									 <?php echo $recievedqty ?>
								</div>
							</div>
							<div class="row static-info">
								<div class="col-md-5 name">
									 Serial Numbers Received:
								</div>
								<div class="col-md-7 value">
									 <?php echo $countorderserials ?>
							
								<?php if($countorderserials > $recievedqty)
								{
									echo '
									<span class="label label-danger">
										Qty scanned greater than recieved</span>';
								}
								?>
									</div>
							</div>
							<div class="row static-info">
								<div class="col-md-5 name">
									 Order Number:
								</div>
								<div class="col-md-7 value">
									 <?php echo $ordid ?>
								</div>
							</div>
	
						<?php } else { ?>
						<div class="form-group">
							<label class="col-md-3 control-label">Product: <span class="required">*</span></label>
							<div class="col-md-6">
								<?php $this->Network->productSelect(); ?>
							</div>
						</div>		
						<?php } ?>
						<div class="form-group">
							<label class="col-md-3 control-label">Location: </label>
							<div class="col-md-6 value">
								 <?php  echo $this->Form->input('warehouse_id',array( 'label' => false, 'class' => 'form-control input-sm select2me','empty' => 'Select...' )); ?>									
							</div>
						</div> 																		
						<div class="form-group">
							<label class="col-md-3 control-label">Serial No.: <span class="required">*</span></label>
							<div class="col-md-6">
								<?php echo $this->Form->input('serialnumber',array('label' => false, 'class' => 'form-control','div' =>false, 'autofocus' => true)); ?>
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
