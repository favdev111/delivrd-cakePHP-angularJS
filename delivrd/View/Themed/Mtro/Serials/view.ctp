<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper" ng-controller="SerialView">
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
		
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
			<div class="col-md-4">
					<div class="portlet box blue-chambray">
						<div class="portlet-title">							
							<div class="caption">
							<i class="fa fa-camera"></i>
							Product Image
							</div>
						</div>
						<div class="portlet-body">
						<?php echo '<img src='.h($serial['Product']['imageurl'])." height='256px' width='256px' >"; ?>	
						
						</div>
					</div>
					
				</div>

				<div class="col-md-8">
				<?php echo $this->Form->create('Serial', array('class' => 'form-horizontal form-row-seperated')); ?>
					
					<?php echo $this->Form->input('id'); ?>
						<div class="portlet box blue-chambray">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-list-ol"></i><?php echo "Serial Number ".h($serial['Serial']['serialnumber'])." details"; ?>
								</div>
								<div class="actions btn-set">
									<button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
									<a href ng-click="addDocument(<?php echo $serial['Serial']['id']; ?>, '<?php echo $serial['Serial']['serialnumber']; ?>')" class="btn btn-fit-height blue"><i class="fa fa-upload"></i> Documents</a>
								</div>
							</div>
							<div class="portlet-body">
								
									<div class="tab-content no-space">
										<div class="tab-pane active" id="tab_general">
											<div class="form-body">
												<div class="row static-info">
															<div class="col-md-5 name">
																 Serial #:
															</div>
															<div class="col-md-7 value">
																 <?php echo h($serial['Serial']['serialnumber']) ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Product name #:
															</div>
															<div class="col-md-7 value">
																 <?php echo h($serial['Product']['name']) ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Product SKU:
															</div>
															<div class="col-md-7 value">
																 <?php echo h($serial['Product']['sku']) ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Received in order #:
															</div>
															<div class="col-md-7 value">
																<?php if($serial['Serial']['order_id_in']) { ?>
                                                                	<?php echo $this->Html->link(__('<i class="fa fa-random"></i> %s', $serial['Serial']['order_id_in']), array('controller'=> 'orders','action' => 'details', $serial['Serial']['order_id_in']),array('escape'=> false)); ?>
																<?php } ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Shipped in order #:
															</div>
															<div class="col-md-7 value">
																 <?php echo h($serial['Serial']['order_id_out']) ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Inventory Status:
															</div>
															<div class="col-md-7 value">
																<?php
																if($serial['Serial']['instock'] == 1)
																{
																	echo "<span class='label label-sm label-info'>In Stock</span>";
																} 
																else {
																	echo "<span class='label label-sm label-danger'>Out Of Stock</span>";
																	}
																	?>
															</div>
														</div>
												
											</div>
										</div>
											</td>
												</tr>
												</thead>
												<tbody>
												</tbody>
												</table>
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

<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    
    var doc_title = 'Serial #';

<?php $this->Html->scriptEnd(); ?>
</script>
<?php echo $this->Html->script('/app/Serials/view.js?v=0.0.1', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/app/Documents/view.js?v=0.0.1', array('block' => 'pageBlock')); ?>