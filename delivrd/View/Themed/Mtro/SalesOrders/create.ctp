<?php $this->AjaxValidation->active(); ?>
<style>
	.ui-autocomplete
	{
	  z-index: 99999; 
	}
</style>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content">
		
		<!-- BEGIN PAGE CONTENT-->
		<?php echo $this->Session->flash(); ?>
		<?php echo $this->Form->create('Order', array('novalidate' => false));  ?>
	
		<div class="row">
			<div class="col-md-12">
				<!-- Begin: life time stats -->
				<div class="portlet box red">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-shopping-cart"></i>Add New Sales Order
						</div>
						<div class="actions">
							<a href="#" onclick="goBack()" class="btn default yellow-stripe">
								<i class="fa fa-angle-left"></i> <span class="hidden-480">Back</span>
							</a>
							<button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
							<div class="btn-group"></div>
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="portlet grey-gallery box">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-cogs"></i>Order Details
										</div>
									</div>
									<div class="portlet-body">
										<div class="row static-info">
											<div class="col-md-7 name">
												<label class="control-label">Reference Order: <span class="required"> * </span></label>
											</div>
											<div class="col-md-7 value">
												 <?php echo $this->Form->input('external_orderid',array('label' => false, 'class' => 'form-control')); ?>
											</div>
										</div>
										<div class="row static-info">
											<div class="col-md-7 name">
												<label class="control-label">Additional Reference Order:</label>
											</div>
											<div class="col-md-7 value">
												 <?php echo $this->Form->input('external_orderid2',array('label' => false, 'class' => 'form-control')); ?>
											</div>
										</div>
										<div class="row static-info">
											<div class="col-md-7 name">
												<label class="control-label">Sales Channel: <span class="required"> * </span></label>
											</div>
											<div class="col-md-7 value">
										  		<span class="schannelDropDown">
													<?php echo $this->Form->input('schannel_id',array('label' => false, 'class' => 'form-control select2me', 'default' => $default_schannel, 'empty' => 'Select...','required' => true)); ?>
			                                    </span>
			                                    <?php if(!$authUser['is_limited']) { ?>
			                                    <a href="#" data-toggle="modal" data-target="#schannelForm"><span class="btn btn-sm blue-steel"> Create Sales Channel</span></a>
			                                    <?php } ?>
											</div> 
										</div>														
										<div class="row static-info">
											<div class="col-md-7 name">
												<label class="control-label"> Shipping Costs (<?php echo h($this->Session->read('currencyname')) ?>): </label>
											</div>
											<div class="col-md-7 value">															
												<?php echo $this->Form->input('shipping_costs', array('label' => false, 'class' => 'form-control')); ?>
											</div>
										</div>
										<div class="row static-info">
											<div class="col-md-7 name">
												<label class="control-label"> Requested Date:</label>
											</div>
											<div class="col-md-7 value">											
												<div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd" id="orderdate">
													<input type="text" class="form-control form-filter" name="data[Order][requested_delivery_date]">
													<span class="input-group-btn">
														<button class="btn btn-medium default date-set" type="button"><i class="fa fa-calendar"></i></button>
													</span>
												</div>		
											</div>
										</div>

										<div class="row static-info">
                                            <div class="col-md-7 name">
                                                <label class="control-label">Remarks:</label>
                                            </div>
                                            <div class="col-md-7 value">                                                            
                                                <?php echo $this->Form->input('comments',array('label' => false,'class' => 'form-control')); ?>
                                            </div>
                                        </div>

									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="portlet grey-gallery box">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-truck"></i>Shipping Address
										</div>
									</div>	
									<div class="portlet-body">
										<div class="row static-info">
											<div class="col-md-12 value">
												<div class="row static-info">
													<div class="col-md-5 name">
														<label class="control-label">Customer Name: <span class="required"> * </span></label>
													</div>
													<div class="col-md-9 value">
														<?php echo $this->Form->input('ship_to_customerid',array('label' => false, 'class' => 'form-control','div' =>false, 'required' => true)); ?>
													</div>
													<div class="col-md-3">
													<a href="#" class="btn blue-steel" data-toggle="modal" data-label="Search Customers" data-target="#delivrd-modal">Search Customer</a>
													</div>
												</div>
												<div class="row static-info">
													<div class="col-md-5 name">
												 		<label class="control-label">Email: </label>
													</div>
													<div class="col-md-9 value">
														<?php echo $this->Form->input('email',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
												<div class="row static-info">
													<div class="col-md-5 name">
												 		<label class="control-label">Street Address: </label>
													</div>
													<div class="col-md-9 value">
														<?php echo $this->Form->input('Address.street',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
												<div class="row static-info">
													<div class="col-md-5 name">
														 <label class="control-label">City: </label>
													</div>
													<div class="col-md-9 value">
														<?php echo $this->Form->input('Address.city',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
												<div class="row static-info">
													<div class="col-md-5 name">
														<label class="control-label">Zip: </label>
													</div>
													<div class="col-md-9 value">
														<?php echo $this->Form->input('Address.zip',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
												<div class="row static-info">
													<div class="col-md-12 name">
														<label class="control-label">Country: </label>
													</div>
													<div class="col-md-6 value">
														<?php echo $this->Form->input('Address.country_id',array('id' => 'country_id', 'default' => ((isset($authUser['default_country_id']))?$authUser['default_country_id']:0), 'label' => false, 'class' => 'form-control input-large select2me', 'placeholder' => '','empty' => 'Select...')); ?>
													</div>
												</div>
												<div class="row static-info">
													<div class="col-md-12 name">
														<label class="control-label">Pin Country: </label>
													</div>
													<div class="col-md-3 value" style="padding-top:2px;">
														<div class="bootstrap-switch-container">
				                                            <input name="data[is_default_country]" data-id="" class="make-switch" data-on-text="Yes" data-off-text="No" type="checkbox" <?php echo ((!empty($authUser['default_country_id']))?'checked':''); ?> >
				                                        </div>
													</div>
												</div>
												<div class="row static-info" id="state_id-div">
													<div class="col-md-5 name">
													  	<label class="control-label">State (US Only): <span class="required"> * </span></label>
													</div>
													<div class="col-md-9 value">
														<?php echo $this->Form->input('Address.state_id',array('id' => 'state_id','label' => false, 'class' => 'form-control input-large select2me','div' =>false,'empty' => 'Select...')); ?>
													</div>
												</div>
												<div class="row static-info" id="stateprovince-div">
													<div class="col-md-5 name">
														<label class="control-label">State/Province: </label>
													</div>
													<div class="col-md-9 value">
														<?php echo $this->Form->input('Address.stateprovince',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
												<div class="row static-info">
													<div class="col-md-5 name">
														<label class="control-label">Phone Number: </label>
													</div>
													<div class="col-md-9 value">
														<?php echo $this->Form->input('Address.phone',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
													</div>
												</div>
												<div class="row">
													<div class="col-md-offset-9 col-md-3">
														<button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo $this->Form->end(); ?>
		<!-- END PAGE CONTENT-->

	</div>
</div>
<!-- END CONTENT -->

<div class="modal fade" id="schannelForm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo __('Add New Sales Channel'); ?></h4>
            </div>

            <?php echo $this->Form->create('Schannel', array('url' => array('controller' => 'schannels', 'action' => 'create'), 'class' => 'form-horizontal', 'id' => 'createschannelForm')); ?>
            <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Name'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('name', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('URL'); ?></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('url', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn">Save Sales Channel</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="delivrd-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

     <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="search-key">
	      <input type="text" name="search" id="searchkey" placeholder="Search by customer name" class="typeahead"/>
	      <div class="bgcolor">
			
		  </div> 
      </div>
  </div>
</div>

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    $(document).ready(function(){
    	$('#OrderSchannelId').select2({minimumResultsForSearch: -1});
    	
        //showProvince();
        $('#OrderCountryId').on('change', function(){
            //showProvince();
        });
    });
    
    function showProvince() {
        if($('#OrderCountryId').val() == '227') {
            $('#use_state').show();
        } else {
            $('#use_state').hide();
        }
    }
<?php $this->Html->scriptEnd(); ?>
</script>