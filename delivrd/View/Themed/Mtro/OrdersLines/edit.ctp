<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE CONTENT-->
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->Form->create('OrdersLine', array('class' => 'form-horizontal form-row-seperated'));	
			echo $this->Form->input('id', array('hidden' => true));
			?>
			<div class="row">
				<div class="col-md-12">
					<!-- Begin: life time stats -->
					<div class="portlet">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-shopping-cart"></i>Edit Order Line, Order #<?php echo $this->request->data['Order']['id']; ?> <span class="hidden-480">
								</span>
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

			
			<div class="portlet-body">
							<div class="table-scrollable">
								<table class="table table-hover">
								<thead>
								<tr>
									<th width='15%'>
										 Product
									</th>
									<th width='10%'>
										 Qty.
									</th >
									<th width='10%'>
										 Unit Price
									</th>
									
									
									<th width='45%'>
										 Remarks
									</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td>
										 <?php  echo $this->Form->input('product_id',array('label' => false,'data-placeholder' => 'Select...','class' => 'form-control input-large select2me','div' =>false)); ?>
									</td>
									<td>
										 <?php echo $this->Form->input('quantity',array('label' => false, 'class' => 'form-control','div' =>false, 'min' => 0)); ?>
									</td>
								<td>
								<?php echo $this->Form->input('unit_price',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
								</td>
								<td>
								<?php echo $this->Form->input('comments',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
								</td>
								</tr>
								</tbody>
								</table>
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
