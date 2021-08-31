<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
							<h4 class="modal-title">Modal title</h4>
						</div>
						<div class="modal-body">
							 Widget settings form goes here
						</div>
						<div class="modal-footer">
							<button type="button" class="btn blue">Save changes</button>
							<button type="button" class="btn default" data-dismiss="modal">Close</button>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->
			<!-- END SAMPLE index/1/sort:id/direction:ascPORTLET CONFIGURATION MODAL FORM-->
			<!-- BEGIN STYLE CUSTOMIZER -->
			<div class="theme-panel hidden-xs hidden-sm">
				<div class="toggler">
				</div>
				<div class="toggler-close">
				</div>
				<div class="theme-options">
					<div class="theme-option theme-colors clearfix">
						<span>
						THEME COLOR </span>
						<ul>
							<li class="color-default current tooltips" data-style="default" data-container="body" data-original-title="Default">
							</li>
							<li class="color-darkblue tooltips" data-style="darkblue" data-container="body" data-original-title="Dark Blue">
							</li>
							<li class="color-blue tooltips" data-style="blue" data-container="body" data-original-title="Blue">
							</li>
							<li class="color-grey tooltips" data-style="grey" data-container="body" data-original-title="Grey">
							</li>
							<li class="color-light tooltips" data-style="light" data-container="body" data-original-title="Light">
							</li>
							<li class="color-light2 tooltips" data-style="light2" data-container="body" data-html="true" data-original-title="Light 2">
							</li>
						</ul>
					</div>
					<div class="theme-option">
						<span>
						Layout </span>
						<select class="layout-option form-control input-sm">
							<option value="fluid" selected="selected">Fluid</option>
							<option value="boxed">Boxed</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Header </span>
						<select class="page-header-option form-control input-sm">
							<option value="fixed" selected="selected">Fixed</option>
							<option value="default">Default</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Top Menu Dropdown</span>
						<select class="page-header-top-dropdown-style-option form-control input-sm">
							<option value="light" selected="selected">Light</option>
							<option value="dark">Dark</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Sidebar Mode</span>
						<select class="sidebar-option form-control input-sm">
							<option value="fixed">Fixed</option>
							<option value="default" selected="selected">Default</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Sidebar Menu </span>
						<select class="sidebar-menu-option form-control input-sm">
							<option value="accordion" selected="selected">Accordion</option>
							<option value="hover">Hover</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Sidebar Style </span>
						<select class="sidebar-style-option form-control input-sm">
							<option value="default" selected="selected">Default</option>
							<option value="light">Light</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Sidebar Position </span>
						<select class="sidebar-pos-option form-control input-sm">
							<option value="left" selected="selected">Left</option>
							<option value="right">Right</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Footer </span>
						<select class="page-footer-option form-control input-sm">
							<option value="fixed">Fixed</option>
							<option value="default" selected="selected">Default</option>
						</select>
					</div>
				</div>
			</div>
			<!-- END STYLE CUSTOMIZER -->
			<!-- BEGIN PAGE HEADER-->
			<h3 class="page-title">
			Product Edit <small>create & edit product</small>
			</h3>
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
				<?php echo $this->Form->create('Product', array('class' => 'form-horizontal form-row-seperated')); ?>
					
					<?php // echo $this->Form->input('id'); ?>
						<div class="portlet">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-shopping-cart"></i><?php echo $this->Form->value('Product.name') ?>
								</div>
								<div class="actions btn-set">
									<button type="button" name="back" class="btn default"><i class="fa fa-angle-left"></i> Back</button>
									<button class="btn default"><i class="fa fa-reply"></i> Reset</button>
									<button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
									
									<div class="btn-group">
										<a class="btn yellow dropdown-toggle" href="#" data-toggle="dropdown">
										<i class="fa fa-share"></i> More <i class="fa fa-angle-down"></i>
										</a>
										<ul class="dropdown-menu pull-right">
											<li>
												<a href="#">
												Duplicate </a>
											</li>
											<li>
												<a href="#">
												Delete </a>
											</li>
											<li class="divider">
											</li>
											<li>
												<a href="#">
												Print Barcode Labels</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="portlet-body">
								<div class="tabbable">
									<ul class="nav nav-tabs">
										<li class="active">
											<a href="#tab_general" data-toggle="tab">
											General </a>
										</li>
										<li>
											<a href="#tab_dimensions" data-toggle="tab">
											Dimensions </a>
										</li>
										<li>
											<a href="#tab_urls" data-toggle="tab">
											URL </a>
										</li>
										<li>
											<a href="#tab_logistics" data-toggle="tab">
											Logistics
											</a>
										</li>
										<li>
											<a href="#tab_packaging" data-toggle="tab">
											Packaging
											</a>
										</li>
										<li>
											<a href="#tab_attributes" data-toggle="tab">
											Attributes
											</a>
										</li>
										<li>
											<a href="#tab_history" data-toggle="tab">
											History </a>
										</li>
									</ul>
									<div class="tab-content no-space">
										<div class="tab-pane active" id="tab_general">
											<div class="form-body">
												<div class="form-group">
													<label class="col-md-2 control-label">Name: <span class="required">
													* </span>
													</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('name',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Description: <span class="required">
													* </span>
													</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('description',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Category <span class="required">
													* </span>
													</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('group_id',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
														<span class="help-block">
														shown in product listing </span>
													</div>
												</div>
												
												
												<div class="form-group">
													<label class="col-md-2 control-label">SKU: <span class="required">
													* </span>
													</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('sku',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Price: <span class="required">
													* </span>
													</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('value',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
					
												
											</div>
										</div>
										<div class="tab-pane" id="tab_dimensions">
											<div class="form-body">
												<div class="form-group">
													<label class="col-md-2 control-label">Weight (<?php echo $this->Session->read('weight_unit') ?>):</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('weight',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Height (<?php echo $this->Session->read('volume_unit') ?>):</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('height',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
														
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Width (<?php echo $this->Session->read('volume_unit') ?>):</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('width',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
														
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Depth (<?php echo $this->Session->read('volume_unit') ?>):</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('depth',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
			
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane" id="tab_urls">
										<div class="form-body">
												<div class="form-group">
													<label class="col-md-2 control-label">Image URL</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('imageurl',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Product Page URL</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('ebay_itemlist_url',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
														
													</div>
												</div>
												
											</div>	
										</div>
										<div class="tab-pane" id="tab_logistics">
										<div class="form-body">
												<div class="form-group">
													<label class="col-md-2 control-label">Barcode System</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('barcode_standards_id',array('label' => false,'class' => 'form-control','div' =>false, 'options' => array('EAN' => 'EAN','UPC' => 'UPC','ISBN' => 'ISBN'),'empty' => '(choose one)')); ?>
													<span class="help-block">
														EAN/UPC/ISBN etc. </span>
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Barcode Number</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('barcode',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
														<span class="help-block">
														12 or 13 Charecters. For example, 7290103127459 </span>
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Safety Stock</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('safety_stock',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Bin Number</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('bin',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
											</div>		
										</div>
										<div class="tab-pane" id="tab_packaging">
										<div class="form-body">
												<div class="form-group">
													<label class="col-md-2 control-label">Packaging Material</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('packaging_material_id',array('label' => false,'options' => $packmaterialsarr,'class' => 'form-control','div' =>false)); ?>
												
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Packaging Instructions</label>
													<div class="col-md-10">
														<?php echo $this->Form->textarea('packaging_instructions',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
								
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Consumed on shipping?</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('consumption',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
												
											</div>		
										</div>
										<div class="tab-pane" id="tab_attributes">
										<div class="form-body">
												<div class="form-group">
													<label class="col-md-2 control-label">Color</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('color_id',array('label' => false,'class' => 'form-control','div' =>false)); ?>
												
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Size</label>
													<div class="col-md-10">
														<?php echo $this->Form->input('size_id',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
								
													</div>
												</div>
												
											</div>		
										</div>
										<div class="tab-pane" id="tab_history">
											<div class="table-container">
												<table class="table table-hover" id="datatable_history">
												<thead>
												<tr role="row" class="heading">
													<th width="25%">
														 Datetime
													</th>
													<th width="55%">
														 Description
													</th>
													<th width="10%">
														 Notification
													</th>
													<th width="10%">
														 Actions
													</th>
												</tr>
												<tr role="row" class="filter">
													<td>
														<div class="input-group date datetime-picker margin-bottom-5" data-date-format="dd/mm/yyyy hh:ii">
															<input type="text" class="form-control form-filter input-sm" readonly name="product_history_date_from" placeholder="From">
															<span class="input-group-btn">
															<button class="btn btn-sm default date-set" type="button"><i class="fa fa-calendar"></i></button>
															</span>
														</div>
														<div class="input-group date datetime-picker" data-date-format="dd/mm/yyyy hh:ii">
															<input type="text" class="form-control form-filter input-sm" readonly name="product_history_date_to" placeholder="To">
															<span class="input-group-btn">
															<button class="btn btn-sm default date-set" type="button"><i class="fa fa-calendar"></i></button>
															</span>
														</div>
													</td>
													<td>
														<input type="text" class="form-control form-filter input-sm" name="product_history_desc" placeholder="To"/>
													</td>
													<td>
														<select name="product_history_notification" class="form-control form-filter input-sm">
															<option value="">Select...</option>
															<option value="pending">Pending</option>
															<option value="notified">Notified</option>
															<option value="failed">Failed</option>
														</select>
													</td>
													<td>
														<div class="margin-bottom-5">
															<button class="btn btn-sm yellow filter-submit margin-bottom"><i class="fa fa-search"></i> Search</button>
														</div>
														<button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
