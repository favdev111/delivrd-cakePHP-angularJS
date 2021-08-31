<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE CONTENT-->
			<?php echo $this->Form->create('Order'); 
			echo $this->Form->input('id'); ?>
			<div class="row">
				<div class="col-md-12">
					<!-- Begin: life time stats -->
					<div class="portlet">
						<div class="portlet-title">
							
							<div class="actions">
								<button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>

									<script>
									function goBack() {
										window.history.back()
									}
									</script>		
								
								<div class="btn-group">																			
								</div>
							</div>
						</div>
						<div class="portlet-body">
							<div class="tabbable">
								<ul class="nav nav-tabs nav-tabs-lg">
									<li class="active">
										<a href="#tab_1" data-toggle="tab">
										Details </a>
									</li>
                                                                       
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="tab_1">
										<div class="row">
											<div class="col-md-6 col-sm-12">
												<div class="portlet yellow-crusta box">
													<div class="portlet-title">
														<div class="caption">
															<i class="fa fa-cogs"></i>User Details
														</div>
														
													</div>
													<div class="portlet-body">
														<div class="row static-info">
															<div class="col-md-5 name">
																 Username:
															</div>
															<div class="col-md-7 value">
																 <?php echo h($user['User']['slug']) ?>
															</div>
														</div>
                                                                                                            <div class="row static-info">
															<div class="col-md-5 name">
																 Email:
															</div>
															<div class="col-md-7 value">
																 <?php echo h($user['User']['email']) ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Created:
															</div>
															<div class="col-md-7 value">
																 <?php echo date("F j, Y, g:i a",strtotime($user['User']['created'])); ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Last Login:
															</div>
															<div class="col-md-7 value">
																<?php echo date("F j, Y, g:i a",strtotime($user['User']['last_login'])); ?>
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Active Interfaces
															</div>
															<div class="col-md-7 value">
															Magento
																
															</div>
														</div>
														
													</div>
												</div>
											</div>
										<div class="col-md-6 col-sm-12">
												<div class="portlet red-sunglo box">
													<div class="portlet-title">
														<div class="caption">
															<i class="fa fa-cogs"></i>User Address
														</div>
														
													</div>	
													<div class="portlet-body">
														<div class="portlet-body">
														<div class="row static-info">
															<div class="col-md-5 name">
																 Street
															</div>
															<div class="col-md-7 value">
															<?php echo htmlspecialchars($user['User']['street']); ?>
																
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 City
															</div>
															<div class="col-md-7 value">
															<?php echo htmlspecialchars($user['User']['city']); ?>
																
															</div>
														</div>
														
														<div class="row static-info">
															<div class="col-md-5 name">
																 State/Province
															</div>
															<div class="col-md-7 value">
															<?php echo htmlspecialchars($user['User']['stateprovince']); ?>
																
															</div>
														</div>
														
														<div class="row static-info">
															<div class="col-md-5 name">
																 Zip
															</div>
															<div class="col-md-7 value">
															<?php echo htmlspecialchars($user['User']['zip']); ?>
																
															</div>
														</div>
														<div class="row static-info">
															<div class="col-md-5 name">
																 Country
															</div>
															<div class="col-md-7 value">
															<?php echo htmlspecialchars($user['User']['country_id']); ?>
																
															</div>
														</div>			
															
															</div>
													</div>
												</div>
											</div>
											</div>
										<div class="row">
											<div class="col-md-12 col-sm-12">
												<div class="portlet grey-cascade box">
													<div class="portlet-title">
														<div class="caption">
															<i class="fa fa-cogs"></i>Activity
														</div>
														
													</div>
													<div class="portlet-body">
														<div class="table-responsive">
															<table class="table table-hover table-bordered table-striped">
															<thead>
															<tr>
																<th>
																	 Products Count
																</th>
																<th>
																	 Sales Orders Count
																</th>
																<th>
																	 Repl. Orders Count
																</th>
																
																<th>
																	 Shipments Count
																</th>
                                                                                                                                
																
															</tr>
															</thead>
															<tbody>
															
															<tr>
														
																	<td><A href='/users/viewuserproducts/<?php echo $user['User']['id']."'/>".$productscount ?></td>
																<td>
																	<?php echo htmlspecialchars($salesorderscount); ?> 
																</td>
																<td>
																	<?php echo htmlspecialchars($replorderscount); ?>
																</td>
															
																	<td><?php echo htmlspecialchars($totalshipmentscount); ?></td>
																	
															</tr>
															
															</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
										
									</div>
							  <?php  if($this->Session->read('is_admin') == 1) { ?>
								
                                                                   
									<div class="tab-pane" id="tab_history">
											<div class="table-container">
												<table class="table table-hover dataTable no-footer" id="datatable_history" role="grid">
												<thead>
												<tr role="row" class="heading">
													<th width="25%">
														 Datetime
													</th>
													<th width="55%">
														 Description
													</th>
													
													<th width="10%">
														 Actions
													</th>
												</tr>

												</thead>
												<tbody>
												<?php foreach ($objectevents as $key => $objectevent): ?>
												<tr role="row" class="<?php echo ($key % 2 == 0 ? "odd" : "even"); ?>">
													<td class="sorting_1"><?php echo $objectevent['Event']['created']; ?></td>
													<td><?php echo "Order status changed to ".$objectevent['Status']['name']; ?></td>
													<td></td>
												</tr>
												<?php endforeach; ?>
											
												
												</tbody>
												</table>
											</div>
										</div>
                                                          <?php } ?>
									</div>
								
									
								</div>
							</div>
						</div>
					</div>
					<!-- End: life time stats -->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
