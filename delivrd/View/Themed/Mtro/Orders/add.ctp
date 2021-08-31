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
			<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
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
			Form Controls <small>form controls and more</small>
			</h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="fa fa-home"></i>
						<a href="index.html">Home</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<a href="#">Form Stuff</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<a href="#">Form Controls</a>
					</li>
				</ul>
				<div class="page-toolbar">
					<div class="btn-group pull-right">
						<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
						Actions <i class="fa fa-angle-down"></i>
						</button>
						<ul class="dropdown-menu pull-right" role="menu">
							<li>
								<a href="#">Action</a>
							</li>
							<li>
								<a href="#">Another action</a>
							</li>
							<li>
								<a href="#">Something else here</a>
							</li>
							<li class="divider">
							</li>
							<li>
								<a href="#">Separated link</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-6 ">
					<!-- BEGIN SAMPLE FORM PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i> Default Form
							</div>
							<div class="tools">
								<a href="" class="collapse">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="" class="reload">
								</a>
								<a href="" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
							<form role="form">
								<div class="form-body">
									<div class="form-group">
										<label>Email Address</label>
										<div class="input-group">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
											<input type="text" class="form-control" placeholder="Email Address">
										</div>
									</div>
									<div class="form-group">
										<label>Circle Input</label>
										<div class="input-group">
											<span class="input-group-addon input-circle-left">
											<i class="fa fa-envelope"></i>
											</span>
											<input type="text" class="form-control input-circle-right" placeholder="Email Address">
										</div>
									</div>
									<div class="form-group">
										<label for="exampleInputPassword1">Password</label>
										<div class="input-group">
											<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
											<span class="input-group-addon">
											<i class="fa fa-user"></i>
											</span>
										</div>
									</div>
									<div class="form-group">
										<label>Left Icon</label>
										<div class="input-icon">
											<i class="fa fa-bell-o"></i>
											<input type="text" class="form-control" placeholder="Left icon">
										</div>
									</div>
									<div class="form-group">
										<label>Left Icon(.input-sm)</label>
										<div class="input-icon input-icon-sm">
											<i class="fa fa-bell-o"></i>
											<input type="text" class="form-control input-sm" placeholder="Left icon">
										</div>
									</div>
									<div class="form-group">
										<label>Left Icon(.input-lg)</label>
										<div class="input-icon input-icon-lg">
											<i class="fa fa-bell-o"></i>
											<input type="text" class="form-control input-lg" placeholder="Left icon">
										</div>
									</div>
									<div class="form-group">
										<label>Right Icon</label>
										<div class="input-icon right">
											<i class="fa fa-microphone"></i>
											<input type="text" class="form-control" placeholder="Right icon">
										</div>
									</div>
									<div class="form-group">
										<label>Right Icon(.input-sm)</label>
										<div class="input-icon input-icon-sm right">
											<i class="fa fa-bell-o"></i>
											<input type="text" class="form-control input-sm" placeholder="Left icon">
										</div>
									</div>
									<div class="form-group">
										<label>Right Icon(.input-lg)</label>
										<div class="input-icon input-icon-lg right">
											<i class="fa fa-bell-o"></i>
											<input type="text" class="form-control input-lg" placeholder="Left icon">
										</div>
									</div>
									<div class="form-group">
										<label>Circle Input</label>
										<div class="input-icon right">
											<i class="fa fa-microphone"></i>
											<input type="text" class="form-control input-circle" placeholder="Right icon">
										</div>
									</div>
									<div class="form-group">
										<label>Input with Icon</label>
										<div class="input-group input-icon right">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
											<i class="fa fa-exclamation tooltips" data-original-title="Invalid email." data-container="body"></i>
											<input id="email" class="input-error form-control" type="text" value="">
										</div>
									</div>
									<div class="form-group">
										<label>Input With Spinner</label>
										<input class="form-control spinner" type="text" placeholder="Process something"/>
									</div>
									<div class="form-group">
										<label>Static Control</label>
										<p class="form-control-static">
											 email@example.com
										</p>
									</div>
									<div class="form-group">
										<label>Disabled</label>
										<input type="text" class="form-control" placeholder="Disabled" disabled>
									</div>
									<div class="form-group">
										<label>Readonly</label>
										<input type="text" class="form-control" placeholder="Readonly" readonly>
									</div>
									<div class="form-group">
										<label>Dropdown</label>
										<select class="form-control">
											<option>Option 1</option>
											<option>Option 2</option>
											<option>Option 3</option>
											<option>Option 4</option>
											<option>Option 5</option>
										</select>
									</div>
									<div class="form-group">
										<label>Multiple Select</label>
										<select multiple class="form-control">
											<option>Option 1</option>
											<option>Option 2</option>
											<option>Option 3</option>
											<option>Option 4</option>
											<option>Option 5</option>
										</select>
									</div>
									<div class="form-group">
										<label>Textarea</label>
										<textarea class="form-control" rows="3"></textarea>
									</div>
									<div class="form-group">
										<label for="exampleInputFile1">File input</label>
										<input type="file" id="exampleInputFile1">
										<p class="help-block">
											 some help text here.
										</p>
									</div>
									<div class="form-group">
										<label>Checkboxes</label>
										<div class="checkbox-list">
											<label>
											<input type="checkbox"> Checkbox 1 </label>
											<label>
											<input type="checkbox"> Checkbox 2 </label>
											<label>
											<input type="checkbox" disabled> Disabled </label>
										</div>
									</div>
									<div class="form-group">
										<label>Inline Checkboxes</label>
										<div class="checkbox-list">
											<label class="checkbox-inline">
											<input type="checkbox" id="inlineCheckbox1" value="option1"> Checkbox 1 </label>
											<label class="checkbox-inline">
											<input type="checkbox" id="inlineCheckbox2" value="option2"> Checkbox 2 </label>
											<label class="checkbox-inline">
											<input type="checkbox" id="inlineCheckbox3" value="option3" disabled> Disabled </label>
										</div>
									</div>
									<div class="form-group">
										<label>Radio</label>
										<div class="radio-list">
											<label>
											<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked> Option 1</label>
											<label>
											<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2"> Option 2 </label>
											<label>
											<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3" disabled> Disabled </label>
										</div>
									</div>
									<div class="form-group">
										<label>Inline Radio</label>
										<div class="radio-list">
											<label class="radio-inline">
											<input type="radio" name="optionsRadios" id="optionsRadios4" value="option1" checked> Option 1 </label>
											<label class="radio-inline">
											<input type="radio" name="optionsRadios" id="optionsRadios5" value="option2"> Option 2 </label>
											<label class="radio-inline">
											<input type="radio" name="optionsRadios" id="optionsRadios6" value="option3" disabled> Disabled </label>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<button type="submit" class="btn blue">Submit</button>
									<button type="button" class="btn default">Cancel</button>
								</div>
							</form>
						</div>
					</div>
					<!-- END SAMPLE FORM PORTLET-->
					<!-- BEGIN SAMPLE FORM PORTLET-->
					<div class="portlet box red">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i> Default Form Height Sizing
							</div>
							<div class="tools">
								<a href="" class="collapse">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="" class="reload">
								</a>
								<a href="" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
							<form role="form">
								<div class="form-body">
									<div class="form-group">
										<label>Large Input</label>
										<input type="text" class="form-control input-lg" placeholder="input-lg">
									</div>
									<div class="form-group">
										<label>Default Input</label>
										<input type="text" class="form-control" placeholder="">
									</div>
									<div class="form-group">
										<label>Small Input</label>
										<input type="text" class="form-control input-sm" placeholder="input-sm">
									</div>
									<div class="form-group">
										<label>Large Select</label>
										<select class="form-control input-lg">
											<option>Option 1</option>
											<option>Option 2</option>
											<option>Option 3</option>
											<option>Option 4</option>
											<option>Option 5</option>
										</select>
									</div>
									<div class="form-group">
										<label>Default Select</label>
										<select class="form-control">
											<option>Option 1</option>
											<option>Option 2</option>
											<option>Option 3</option>
											<option>Option 4</option>
											<option>Option 5</option>
										</select>
									</div>
									<div class="form-group">
										<label>Small Select</label>
										<select class="form-control input-sm">
											<option>Option 1</option>
											<option>Option 2</option>
											<option>Option 3</option>
											<option>Option 4</option>
											<option>Option 5</option>
										</select>
									</div>
								</div>
								<div class="form-actions right">
									<button type="button" class="btn default">Cancel</button>
									<button type="submit" class="btn green">Submit</button>
								</div>
							</form>
						</div>
					</div>
					<!-- END SAMPLE FORM PORTLET-->
					<!-- BEGIN SAMPLE FORM PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i> Form Input Width Sizing
							</div>
							<div class="tools">
								<a href="" class="collapse">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="" class="reload">
								</a>
								<a href="" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
							<form role="form">
								<div class="form-body">
									<div class="form-group">
										<label>Fluid Input</label>
										<input type="text" class="form-control" placeholder="fluid">
										<div class="input-icon right margin-top-10">
											<i class="fa fa-check"></i>
											<input type="text" class="form-control" placeholder="fluid">
										</div>
										<div class="input-icon margin-top-10">
											<i class="fa fa-user"></i>
											<input type="text" class="form-control" placeholder="fluid">
										</div>
										<div class="input-group margin-top-10">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
											<input type="email" class="form-control" placeholder=".input-xlarge">
										</div>
										<div class="input-group margin-top-10">
											<input type="email" class="form-control" placeholder=".input-xlarge">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
										</div>
										<hr>
									</div>
									<div class="form-group">
										<label>Extra Large Input</label>
										<input type="text" class="form-control input-xlarge" placeholder=".input-xlarge">
										<div class="input-icon right input-xlarge margin-top-10">
											<i class="fa fa-check"></i>
											<input type="text" class="form-control" placeholder=".input-xlarge">
										</div>
										<div class="input-icon input-xlarge margin-top-10">
											<i class="fa fa-user"></i>
											<input type="text" class="form-control" placeholder=".input-xlarge">
										</div>
										<div class="input-group input-xlarge margin-top-10">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
											<input type="email" class="form-control" placeholder=".input-xlarge">
										</div>
										<div class="input-group input-xlarge margin-top-10">
											<input type="email" class="form-control" placeholder=".input-xlarge">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
										</div>
										<hr>
									</div>
									<div class="form-group">
										<label>Large Input</label>
										<input type="text" class="form-control input-large" placeholder=".input-large">
										<div class="input-icon right input-large margin-top-10">
											<i class="fa fa-check"></i>
											<input type="text" class="form-control" placeholder=".input-large">
										</div>
										<div class="input-icon input-large margin-top-10">
											<i class="fa fa-user"></i>
											<input type="text" class="form-control" placeholder=".input-large">
										</div>
										<div class="input-group input-large margin-top-10">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
											<input type="email" class="form-control" placeholder=".input-large">
										</div>
										<div class="input-group input-large margin-top-10">
											<input type="email" class="form-control" placeholder=".input-large">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
										</div>
										<hr>
									</div>
									<div class="form-group">
										<label>Medium Input</label>
										<input type="text" class="form-control input-medium" placeholder=".input-medium">
										<div class="input-icon right input-medium margin-top-10">
											<i class="fa fa-check"></i>
											<input type="text" class="form-control" placeholder=".input-medium">
										</div>
										<div class="input-icon input-medium margin-top-10">
											<i class="fa fa-user"></i>
											<input type="text" class="form-control" placeholder=".input-medium">
										</div>
										<div class="input-group input-medium margin-top-10">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
											<input type="email" class="form-control" placeholder=".input-medium">
										</div>
										<div class="input-group input-medium margin-top-10">
											<input type="email" class="form-control" placeholder=".input-medium">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
										</div>
										<hr>
									</div>
									<div class="form-group">
										<label>Small Input</label>
										<input type="text" class="form-control input-small" placeholder=".input-small">
										<div class="input-icon right input-small margin-top-10">
											<i class="fa fa-check"></i>
											<input type="text" class="form-control" placeholder=".input-small">
										</div>
										<div class="input-icon input-small margin-top-10">
											<i class="fa fa-user"></i>
											<input type="text" class="form-control" placeholder=".input-small">
										</div>
										<div class="input-group input-small margin-top-10">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
											<input type="email" class="form-control" placeholder=".input-small">
										</div>
										<div class="input-group input-small margin-top-10">
											<input type="email" class="form-control" placeholder=".input-small">
											<span class="input-group-addon">
											<i class="fa fa-envelope"></i>
											</span>
										</div>
									</div>
									<div class="form-group">
										<label>Extra Small Input</label>
										<input type="text" class="form-control input-xsmall" placeholder=".input-xsmall">
									</div>
									<div class="form-group">
										<label>Extra Large Select</label>
										<select class="form-control input-xlarge">
											<option>Option 1</option>
											<option>Option 2</option>
											<option>Option 3</option>
											<option>Option 4</option>
											<option>Option 5</option>
										</select>
									</div>
									<div class="form-group">
										<label>Large Select</label>
										<select class="form-control input-large">
											<option>Option 1</option>
											<option>Option 2</option>
											<option>Option 3</option>
											<option>Option 4</option>
											<option>Option 5</option>
										</select>
									</div>
									<div class="form-group">
										<label>Medium Select</label>
										<select class="form-control input-medium">
											<option>Option 1</option>
											<option>Option 2</option>
											<option>Option 3</option>
											<option>Option 4</option>
											<option>Option 5</option>
										</select>
									</div>
									<div class="form-group">
										<label>Small Select</label>
										<select class="form-control input-small">
											<option>Option 1</option>
											<option>Option 2</option>
											<option>Option 3</option>
											<option>Option 4</option>
											<option>Option 5</option>
										</select>
									</div>
									<div class="form-group">
										<label>Extra Small Select</label>
										<select class="form-control input-xsmall">
											<option>Option 1</option>
											<option>Option 2</option>
											<option>Option 3</option>
											<option>Option 4</option>
											<option>Option 5</option>
										</select>
									</div>
								</div>
								<div class="form-actions right">
									<button type="button" class="btn default">Cancel</button>
									<button type="submit" class="btn green">Submit</button>
								</div>
							</form>
						</div>
					</div>
					<!-- END SAMPLE FORM PORTLET-->
				</div>
				<div class="col-md-6 ">
					<!-- BEGIN SAMPLE FORM PORTLET-->
					<div class="portlet box green ">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i> Horizontal Form
							</div>
							<div class="tools">
								<a href="" class="collapse">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="" class="reload">
								</a>
								<a href="" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" role="form">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-3 control-label">Block Help</label>
										<div class="col-md-9">
											<input type="text" class="form-control" placeholder="Enter text">
											<span class="help-block">
											A block of help text. </span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Inline Help</label>
										<div class="col-md-9">
											<input type="text" class="form-control input-inline input-medium" placeholder="Enter text">
											<span class="help-inline">
											Inline help. </span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Input Group</label>
										<div class="col-md-9">
											<div class="input-inline input-medium">
												<div class="input-group">
													<span class="input-group-addon">
													<i class="fa fa-user"></i>
													</span>
													<input type="email" class="form-control" placeholder="Email Address">
												</div>
											</div>
											<span class="help-inline">
											Inline help. </span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Email Address</label>
										<div class="col-md-9">
											<div class="input-group">
												<span class="input-group-addon">
												<i class="fa fa-envelope"></i>
												</span>
												<input type="email" class="form-control" placeholder="Email Address">
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Password</label>
										<div class="col-md-9">
											<div class="input-group">
												<input type="password" class="form-control" placeholder="Password">
												<span class="input-group-addon">
												<i class="fa fa-user"></i>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Left Icon</label>
										<div class="col-md-9">
											<div class="input-icon">
												<i class="fa fa-bell-o"></i>
												<input type="text" class="form-control" placeholder="Left icon">
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Right Icon</label>
										<div class="col-md-9">
											<div class="input-icon right">
												<i class="fa fa-microphone"></i>
												<input type="text" class="form-control" placeholder="Right icon">
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Icon Input in Group Input</label>
										<div class="col-md-9">
											<div class="input-group">
												<div class="input-icon">
													<i class="fa fa-lock fa-fw"></i>
													<input id="newpassword" class="form-control" type="text" name="password" placeholder="password"/>
												</div>
												<span class="input-group-btn">
												<button id="genpassword" class="btn btn-success" type="button"><i class="fa fa-arrow-left fa-fw"/></i> Random</button>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Input With Spinner</label>
										<div class="col-md-9">
											<input type="password" class="form-control spinner" placeholder="Password">
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Static Control</label>
										<div class="col-md-9">
											<p class="form-control-static">
												 email@example.com
											</p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Disabled</label>
										<div class="col-md-9">
											<input type="password" class="form-control" placeholder="Disabled" disabled>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Readonly</label>
										<div class="col-md-9">
											<input type="password" class="form-control" placeholder="Readonly" readonly>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Dropdown</label>
										<div class="col-md-9">
											<select class="form-control">
												<option>Option 1</option>
												<option>Option 2</option>
												<option>Option 3</option>
												<option>Option 4</option>
												<option>Option 5</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Multiple Select</label>
										<div class="col-md-9">
											<select multiple class="form-control">
												<option>Option 1</option>
												<option>Option 2</option>
												<option>Option 3</option>
												<option>Option 4</option>
												<option>Option 5</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Textarea</label>
										<div class="col-md-9">
											<textarea class="form-control" rows="3"></textarea>
										</div>
									</div>
									<div class="form-group">
										<label for="exampleInputFile" class="col-md-3 control-label">File input</label>
										<div class="col-md-9">
											<input type="file" id="exampleInputFile">
											<p class="help-block">
												 some help text here.
											</p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Checkboxes</label>
										<div class="col-md-9">
											<div class="checkbox-list">
												<label>
												<input type="checkbox"> Checkbox 1 </label>
												<label>
												<input type="checkbox"> Checkbox 1 </label>
												<label>
												<input type="checkbox" disabled> Disabled </label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Inline Checkboxes</label>
										<div class="col-md-9">
											<div class="checkbox-list">
												<label class="checkbox-inline">
												<input type="checkbox" id="inlineCheckbox21" value="option1"> Checkbox 1 </label>
												<label class="checkbox-inline">
												<input type="checkbox" id="inlineCheckbox22" value="option2"> Checkbox 2 </label>
												<label class="checkbox-inline">
												<input type="checkbox" id="inlineCheckbox23" value="option3" disabled> Disabled </label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Radio</label>
										<div class="col-md-9">
											<div class="radio-list">
												<label>
												<input type="radio" name="optionsRadios" id="optionsRadios22" value="option1" checked> Option 1 </label>
												<label>
												<input type="radio" name="optionsRadios" id="optionsRadios23" value="option2" checked> Option 2 </label>
												<label>
												<input type="radio" name="optionsRadios" id="optionsRadios24" value="option2" disabled> Disabled </label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Inline Radio</label>
										<div class="col-md-9">
											<div class="radio-list">
												<label class="radio-inline">
												<input type="radio" name="optionsRadios" id="optionsRadios25" value="option1" checked> Option 1 </label>
												<label class="radio-inline">
												<input type="radio" name="optionsRadios" id="optionsRadios26" value="option2" checked> Option 2 </label>
												<label class="radio-inline">
												<input type="radio" name="optionsRadios" id="optionsRadios27" value="option3" disabled> Disabled </label>
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<div class="row">
										<div class="col-md-offset-3 col-md-9">
											<button type="submit" class="btn green">Submit</button>
											<button type="button" class="btn default">Cancel</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					<!-- END SAMPLE FORM PORTLET-->
					<!-- BEGIN SAMPLE FORM PORTLET-->
					<div class="portlet box purple ">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i> Horizontal Form Height Sizing
							</div>
							<div class="tools">
								<a href="" class="collapse">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="" class="reload">
								</a>
								<a href="" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" role="form">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-3 control-label">Large Input</label>
										<div class="col-md-9">
											<input type="text" class="form-control input-lg" placeholder="Large Input">
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Default Input</label>
										<div class="col-md-9">
											<input type="text" class="form-control" placeholder="Default Input">
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Small Input</label>
										<div class="col-md-9">
											<input type="text" class="form-control input-sm" placeholder="Default Input">
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Large Select</label>
										<div class="col-md-9">
											<select class="form-control input-lg">
												<option>Option 1</option>
												<option>Option 2</option>
												<option>Option 3</option>
												<option>Option 4</option>
												<option>Option 5</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Default Select</label>
										<div class="col-md-9">
											<select class="form-control">
												<option>Option 1</option>
												<option>Option 2</option>
												<option>Option 3</option>
												<option>Option 4</option>
												<option>Option 5</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Small Select</label>
										<div class="col-md-9">
											<select class="form-control input-sm">
												<option>Option 1</option>
												<option>Option 2</option>
												<option>Option 3</option>
												<option>Option 4</option>
												<option>Option 5</option>
											</select>
										</div>
									</div>
								</div>
								<div class="form-actions right1">
									<button type="button" class="btn default">Cancel</button>
									<button type="submit" class="btn green">Submit</button>
								</div>
							</form>
						</div>
					</div>
					<!-- END SAMPLE FORM PORTLET-->
					<!-- BEGIN SAMPLE FORM PORTLET-->
					<div class="portlet box purple ">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i> Fluid Input Groups
							</div>
							<div class="tools">
								<a href="" class="collapse">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="" class="reload">
								</a>
								<a href="" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<h4 class="block">Checkboxe Addons</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-6">
										<div class="input-group">
											<span class="input-group-addon">
											<input type="checkbox">
											</span>
											<input type="text" class="form-control">
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
									<div class="col-md-6">
										<div class="input-group">
											<input type="text" class="form-control">
											<span class="input-group-addon">
											<input type="checkbox">
											</span>
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
								<!-- /.row -->
							</form>
							<h4 class="block">Button Addons</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-6">
										<div class="input-group">
											<span class="input-group-btn">
											<button class="btn red" type="button">Go!</button>
											</span>
											<input type="text" class="form-control">
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
									<div class="col-md-6">
										<div class="input-group">
											<input type="text" class="form-control">
											<span class="input-group-btn">
											<button class="btn blue" type="button">Go!</button>
											</span>
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
								<!-- /.row -->
							</form>
							<h4 class="block">Button Addons On Both Sides</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-12">
										<div class="input-group">
											<span class="input-group-btn">
											<button class="btn red" type="button">Go!</button>
											</span>
											<input type="text" class="form-control">
											<span class="input-group-btn">
											<button class="btn blue" type="button">Go!</button>
											</span>
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
							</form>
							<h4 class="block">Buttons With Dropdowns</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-6">
										<div class="input-group">
											<div class="input-group-btn">
												<button type="button" class="btn green dropdown-toggle" data-toggle="dropdown">Action <i class="fa fa-angle-down"></i></button>
												<ul class="dropdown-menu">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
											<!-- /btn-group -->
											<input type="text" class="form-control">
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
									<div class="col-md-6">
										<div class="input-group">
											<input type="text" class="form-control">
											<div class="input-group-btn">
												<button type="button" class="btn yellow dropdown-toggle" data-toggle="dropdown">Action <i class="fa fa-angle-down"></i></button>
												<ul class="dropdown-menu pull-right">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
											<!-- /btn-group -->
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
								<!-- /.row -->
							</form>
							<h4 class="block">Buttons With Dropdowns On Both Sides</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-12">
										<div class="input-group">
											<div class="input-group-btn">
												<button type="button" class="btn green dropdown-toggle" data-toggle="dropdown">Action <i class="fa fa-angle-down"></i></button>
												<ul class="dropdown-menu">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
											<!-- /btn-group -->
											<input type="text" class="form-control">
											<div class="input-group-btn">
												<button type="button" class="btn yellow dropdown-toggle" data-toggle="dropdown">Action <i class="fa fa-angle-down"></i></button>
												<ul class="dropdown-menu pull-right">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
											<!-- /btn-group -->
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
								<!-- /.row -->
							</form>
							<h4 class="block">Segmented Buttons</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-6">
										<div class="input-group">
											<div class="input-group-btn">
												<button type="button" class="btn default" tabindex="-1">Action</button>
												<button type="button" class="btn default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
												<i class="fa fa-angle-down"></i>
												</button>
												<ul class="dropdown-menu" role="menu">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
											<input type="text" class="form-control">
										</div>
										<!-- /.input-group -->
									</div>
									<!-- /.col-md-6 -->
									<div class="col-md-6">
										<div class="input-group">
											<input type="text" class="form-control">
											<div class="input-group-btn">
												<button type="button" class="btn green" tabindex="-1">Action</button>
												<button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" tabindex="-1">
												<i class="fa fa-angle-down"></i>
												</button>
												<ul class="dropdown-menu pull-right" role="menu">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
										</div>
										<!-- /.input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
							</form>
						</div>
					</div>
					<!-- END SAMPLE FORM PORTLET-->
					<!-- BEGIN SAMPLE FORM PORTLET-->
					<div class="portlet box purple ">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i> Fixed Input Groups
							</div>
							<div class="tools">
								<a href="" class="collapse">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="" class="reload">
								</a>
								<a href="" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<h4 class="block">Checkboxe Addons</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-6">
										<div class="input-group input-medium">
											<span class="input-group-addon">
											<input type="checkbox">
											</span>
											<input type="text" class="form-control" placeholder=".input-medium">
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
									<div class="col-md-6">
										<div class="input-group input-medium">
											<input type="text" class="form-control" placeholder=".input-medium">
											<span class="input-group-addon">
											<input type="checkbox">
											</span>
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
								<!-- /.row -->
							</form>
							<h4 class="block">Button Addons</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-6">
										<div class="input-group input-medium">
											<span class="input-group-btn">
											<button class="btn red" type="button">Go!</button>
											</span>
											<input type="text" class="form-control" placeholder=".input-medium">
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
									<div class="col-md-6">
										<div class="input-group input-medium">
											<input type="text" class="form-control" placeholder=".input-medium">
											<span class="input-group-btn">
											<button class="btn blue" type="button">Go!</button>
											</span>
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
								<!-- /.row -->
							</form>
							<h4 class="block">Button Addons On Both Sides</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-12">
										<div class="input-group input-large">
											<span class="input-group-btn">
											<button class="btn red" type="button">Go!</button>
											</span>
											<input type="text" class="form-control" placeholder=".input-large">
											<span class="input-group-btn">
											<button class="btn blue" type="button">Go!</button>
											</span>
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
							</form>
							<h4 class="block">Buttons With Dropdowns</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-6">
										<div class="input-group input-medium">
											<div class="input-group-btn">
												<button type="button" class="btn green dropdown-toggle" data-toggle="dropdown">Action <i class="fa fa-angle-down"></i></button>
												<ul class="dropdown-menu">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
											<!-- /btn-group -->
											<input type="text" class="form-control" placeholder=".input-medium">
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
									<div class="col-md-6">
										<div class="input-group input-medium">
											<input type="text" class="form-control" placeholder=".input-medium">
											<div class="input-group-btn">
												<button type="button" class="btn yellow dropdown-toggle" data-toggle="dropdown">Action <i class="fa fa-angle-down"></i></button>
												<ul class="dropdown-menu pull-right">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
											<!-- /btn-group -->
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
								<!-- /.row -->
							</form>
							<h4 class="block">Buttons With Dropdowns On Both Sides</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-12">
										<div class="input-group input-xlarge">
											<div class="input-group-btn">
												<button type="button" class="btn green dropdown-toggle" data-toggle="dropdown">Action <i class="fa fa-angle-down"></i></button>
												<ul class="dropdown-menu">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
											<!-- /btn-group -->
											<input type="text" class="form-control" placeholder=".input-xlarge">
											<div class="input-group-btn">
												<button type="button" class="btn yellow dropdown-toggle" data-toggle="dropdown">Action <i class="fa fa-angle-down"></i></button>
												<ul class="dropdown-menu pull-right">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
											<!-- /btn-group -->
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
								<!-- /.row -->
							</form>
							<h4 class="block">Segmented Buttons</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-12">
										<div class="input-group input-large">
											<div class="input-group-btn">
												<button type="button" class="btn default" tabindex="-1">Action</button>
												<button type="button" class="btn default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
												<i class="fa fa-angle-down"></i>
												</button>
												<ul class="dropdown-menu" role="menu">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
											<input type="text" class="form-control" placeholder=".input-large">
										</div>
										<!-- /.input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
							</form>
							<form role="form" class="margin-top-10">
								<div class="row">
									<div class="col-md-12">
										<div class="input-group input-large">
											<input type="text" class="form-control" placeholder=".input-large">
											<div class="input-group-btn">
												<button type="button" class="btn green" tabindex="-1">Action</button>
												<button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" tabindex="-1">
												<i class="fa fa-angle-down"></i>
												</button>
												<ul class="dropdown-menu" role="menu">
													<li>
														<a href="#">
														Action </a>
													</li>
													<li>
														<a href="#">
														Another action </a>
													</li>
													<li>
														<a href="#">
														Something else here </a>
													</li>
													<li class="divider">
													</li>
													<li>
														<a href="#">
														Separated link </a>
													</li>
												</ul>
											</div>
										</div>
										<!-- /.input-group -->
									</div>
									<!-- /.col-md-6 -->
								</div>
							</form>
						</div>
					</div>
					<!-- END SAMPLE FORM PORTLET-->
					<div class="portlet box blue ">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i> Validation States
							</div>
							<div class="tools">
								<a href="" class="collapse">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="" class="reload">
								</a>
								<a href="" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
							<form role="form">
								<div class="form-body">
									<div class="form-group has-success">
										<label class="control-label">Input with success</label>
										<input type="text" class="form-control" id="inputSuccess">
									</div>
									<div class="form-group has-warning">
										<label class="control-label">Input with warning</label>
										<input type="text" class="form-control" id="inputWarning">
									</div>
									<div class="form-group has-error">
										<label class="control-label">Input with error</label>
										<input type="text" class="form-control" id="inputError">
									</div>
								</div>
								<div class="form-actions">
									<button type="button" class="btn default">Cancel</button>
									<button type="submit" class="btn red">Submit</button>
								</div>
							</form>
						</div>
					</div>
					<div class="portlet box yellow ">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i> Validation States With Icons
							</div>
							<div class="tools">
								<a href="" class="collapse">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="" class="reload">
								</a>
								<a href="" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
							<form role="form">
								<div class="form-body">
									<div class="form-group">
										<label class="control-label">Default input</label>
										<div class="input-icon right">
											<i class="fa fa-info-circle tooltips" data-original-title="Email address" data-container="body"></i>
											<input type="text" class="form-control">
										</div>
									</div>
									<div class="form-group has-success">
										<label class="control-label">Input with success</label>
										<div class="input-icon right">
											<i class="fa fa-check tooltips" data-original-title="You look OK!" data-container="body"></i>
											<input type="text" class="form-control">
										</div>
									</div>
									<div class="form-group has-warning">
										<label class="control-label">Input with warning</label>
										<div class="input-icon right">
											<i class="fa fa-warning tooltips" data-original-title="please provide an email" data-container="body"></i>
											<input type="text" class="form-control">
										</div>
									</div>
									<div class="form-group has-error">
										<label class="control-label">Input with error</label>
										<div class="input-icon right">
											<i class="fa fa-exclamation tooltips" data-original-title="please write a valid email" data-container="body"></i>
											<input type="text" class="form-control">
										</div>
									</div>
								</div>
								<div class="form-actions right">
									<button type="button" class="btn default">Cancel</button>
									<button type="submit" class="btn green">Submit</button>
								</div>
							</form>
						</div>
					</div>
					<div class="portlet box purple">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i> Horizontal Form Validation States
							</div>
							<div class="tools">
								<a href="" class="collapse">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="" class="reload">
								</a>
								<a href="" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
							<form role="form" class="form-horizontal">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-4 control-label">Default input</label>
										<div class="col-md-8">
											<div class="input-icon right">
												<i class="fa fa-info-circle tooltips" data-original-title="Email address" data-container="body"></i>
												<input type="text" class="form-control">
											</div>
										</div>
									</div>
									<div class="form-group has-success">
										<label class="col-md-4 control-label">Input with success</label>
										<div class="col-md-8">
											<div class="input-icon right">
												<i class="fa fa-check tooltips" data-original-title="You look OK!" data-container="body"></i>
												<input type="text" class="form-control">
											</div>
										</div>
									</div>
									<div class="form-group has-warning">
										<label class="col-md-4 control-label">Input with warning</label>
										<div class="col-md-8">
											<div class="input-icon right">
												<i class="fa fa-warning tooltips" data-original-title="please provide an email" data-container="body"></i>
												<input type="text" class="form-control">
											</div>
										</div>
									</div>
									<div class="form-group has-error">
										<label class="col-md-4 control-label">Input with error</label>
										<div class="col-md-8">
											<div class="input-icon right">
												<i class="fa fa-exclamation tooltips" data-original-title="please write a valid email" data-container="body"></i>
												<input type="text" class="form-control">
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<div class="row">
										<div class="col-md-offset-4 col-md-8">
											<button type="button" class="btn default">Cancel</button>
											<button type="submit" class="btn blue">Submit</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="row ">
				<div class="col-md-12">
					<!-- BEGIN SAMPLE FORM PORTLET-->
					<div class="portlet box yellow">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i> More Form Samples
							</div>
							<div class="tools">
								<a href="" class="collapse">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="" class="reload">
								</a>
								<a href="" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<h4>Inline Form</h4>
							<form class="form-inline" role="form">
								<div class="form-group">
									<label class="sr-only" for="exampleInputEmail2">Email address</label>
									<input type="email" class="form-control" id="exampleInputEmail2" placeholder="Enter email">
								</div>
								<div class="form-group">
									<label class="sr-only" for="exampleInputPassword2">Password</label>
									<input type="password" class="form-control" id="exampleInputPassword2" placeholder="Password">
								</div>
								<div class="checkbox">
									<label>
									<input type="checkbox"> Remember me </label>
								</div>
								<button type="submit" class="btn btn-default">Sign in</button>
							</form>
							<hr>
							<h4>Inline Form With Icons</h4>
							<form class="form-inline" role="form">
								<div class="form-group">
									<label class="sr-only" for="exampleInputEmail22">Email address</label>
									<div class="input-icon">
										<i class="fa fa-envelope"></i>
										<input type="email" class="form-control" id="exampleInputEmail22" placeholder="Enter email">
									</div>
								</div>
								<div class="form-group">
									<label class="sr-only" for="exampleInputPassword42">Password</label>
									<div class="input-icon">
										<i class="fa fa-user"></i>
										<input type="password" class="form-control" id="exampleInputPassword42" placeholder="Password">
									</div>
								</div>
								<div class="checkbox">
									<label>
									<input type="checkbox"> Remember me </label>
								</div>
								<button type="submit" class="btn btn-default">Sign in</button>
							</form>
							<hr>
							<h4>Horizontal Form</h4>
							<form class="form-horizontal" role="form">
								<div class="form-group">
									<label for="inputEmail1" class="col-md-2 control-label">Email</label>
									<div class="col-md-4">
										<input type="email" class="form-control" id="inputEmail1" placeholder="Email">
									</div>
								</div>
								<div class="form-group">
									<label for="inputPassword12" class="col-md-2 control-label">Password</label>
									<div class="col-md-4">
										<input type="password" class="form-control" id="inputPassword12" placeholder="Password">
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-offset-2 col-md-4">
										<div class="checkbox">
											<label>
											<input type="checkbox"> Remember me </label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-offset-2 col-md-10">
										<button type="submit" class="btn blue">Sign in</button>
									</div>
								</div>
							</form>
							<hr>
							<h4>Horizontal Form With Icons</h4>
							<form class="form-horizontal" role="form">
								<div class="form-group">
									<label for="inputEmail12" class="col-md-2 control-label">Email</label>
									<div class="col-md-4">
										<div class="input-icon">
											<i class="fa fa-envelope"></i>
											<input type="email" class="form-control" id="inputEmail12" placeholder="Email">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="inputPassword1" class="col-md-2 control-label">Password</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa fa-user"></i>
											<input type="password" class="form-control" id="inputPassword1" placeholder="Password">
										</div>
										<div class="help-block">
											 with right aligned icon
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-offset-2 col-md-4">
										<div class="checkbox">
											<label>
											<input type="checkbox"> Remember me </label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-offset-2 col-md-10">
										<button type="submit" class="btn green">Sign in</button>
									</div>
								</div>
							</form>
							<hr>
							<h4>Column Sizing</h4>
							<form role="form">
								<div class="row">
									<div class="col-md-2">
										<input type="text" class="form-control" placeholder=".col-md-2">
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" placeholder=".col-md-3">
									</div>
									<div class="col-md-4">
										<input type="text" class="form-control" placeholder=".col-md-4">
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" placeholder=".col-md-2">
									</div>
								</div>
							</form>
						</div>
					</div>
					<!-- END SAMPLE FORM PORTLET-->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
