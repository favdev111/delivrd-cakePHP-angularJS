<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <?php echo $this->Form->create('Order', array('novalidate' => true)); ?>
            <?php echo $this->Form->input('id'); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->Session->flash(); ?>
                    <!-- Begin: life time stats -->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-shopping-cart"></i>Order #<?php echo $this->Form->value('Order.id') ?> <span class="hidden-480">
                                | <?php echo $this->Admin->localTime("%B %d, %Y, %I:%M %p", strtotime($order['Order']['created'])); ?></span>
                            </div>
                            <div class="actions">
                                <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                                <?php echo $this->Html->link(__('<i class="fa fa-search"></i> Order Details'), array('controller' => 'salesorders', 'action' => 'details', $order['Order']['id']),array('class'=>'btn blue', 'escape'=> false)); ?>
                                <button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
                                <div class="btn-group"> </div>
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
                                                <div class="col-md-5 name">
                                                     Order #:
                                                </div>
                                                <div class="col-md-9 value">
                                                     <?php echo $this->Form->value('Order.id') ?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                     Order Date & Time:
                                                </div>
                                                <div class="col-md-9 value">
                                                     <?php echo date("F j, Y, g:i a",strtotime($order['Order']['created'])); ?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                <label class="control-label">Reference Order: 
                                                <span class="required">
                                                * </span>
                                                </label>
                                                </div>
                                                <div class="col-md-9 value">
                                                    <?php echo $this->Form->input('external_orderid',array('label' => false, 'class' => 'form-control input-medium', 'required')); ?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                <label class="control-label">Additional Reference Order:
                                                <span class="required">
                                                * </span>
                                                </label>
                                                </div>
                                                <div class="col-md-9 value">
                                                    <?php echo $this->Form->input('external_orderid2',array('label' => false, 'class' => 'form-control input-medium')); ?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                <label class="control-label">Sales Channel: 
                                                <span class="required">
                                                * </span>
                                                </label>
                                                </div>
                                                <div class="col-md-9 value">
                                                     <?php echo $this->Form->input('schannel_id',array('label' => false, 'class' => 'form-control input-medium', 'required')); ?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                     Shipping Costs (<?php echo $this->Session->read('currencyname') ?>):
                                                </div>
                                                <div class="col-md-9 value">                                        
                                                <?php echo $this->Form->input('shipping_costs', array('label' => false, 'class' => 'form-control input-medium')); ?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    Requested Date:
                                                </div>
                                                <div class="col-md-9 value">
                                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                                        <span class="input-group-btn">
                                                            <input type="text" class="form-control form-filter input-medium" name="data[Order][requested_delivery_date]" value="<?php echo $order['Order']['requested_delivery_date']; ?>" >
                                                            <button class="btn btn-medium default" type="button"><i class="fa fa-calendar"></i></button>
                                                        </span>
                                                    </div>                                                          
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    <label class="control-label">Remarks:</label>
                                                </div>
                                                <div class="col-md-9 value">                                                            
                                                    <?php echo $this->Form->input('comments',array('label' => false,'class' => 'form-control')); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                     Order Status:
                                                </div>
                                                <div class="col-md-9 value">
                                                    <?php echo $this->Order->status($order['Order']['status_id']); ?>
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
                                                        <label class="control-label">Customer Name: 
                                                          <span class="required">* </span>
                                                        </label>
                                                        </div>
                                                        <div class="col-md-9 value">                                            
                                                        <?php echo $this->Form->input('ship_to_customerid',array('label' => false, 'class' => 'form-control','div' =>false, 'required')); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        <label class="control-label">Email:
                                                         </label>
                                                    </div>
                                                    <div class="col-md-9 value">                                                            
                                                        <?php echo $this->Form->input('email',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">
                                                        <label class="control-label">Street Address: 
                                                        </label>
                                                        </div>
                                                        <div class="col-md-9 value">    
                                                        <?php echo $this->Form->hidden('Address.id',array()); 
                                                        echo $this->Form->input('Address.street',array('label' => false, 'class' => 'form-control','div' => false, 'required' => false)); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">
                                                        <label class="control-label">City: 
                                                        </label>
                                                        </div>
                                                        <div class="col-md-9 value">
                                                            <?php echo $this->Form->input('Address.city',array('label' => false, 'class' => 'form-control','div' =>false, 'required' => false)); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">
                                                             Zip:
                                                        </div>
                                                        <div class="col-md-9 value">
                                                            <?php echo $this->Form->input('Address.zip',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">
                                                        <label class="control-label">Country: 
                                                        </label>
                                                        </div>
                                                        <div class="col-md-9 value">
                                                        <?php echo $this->Form->input('Address.country_id',array('id' => 'country_id','label' => false, 'class' => 'form-control input-large select2me', 'placeholder' => '', 'required' => false, 'empty' => 'Select...')); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info" id="stateprovince-div">
                                                        <div class="col-md-5 name">
                                                             <label class="control-label">State/Province: 
                                                        </div>
                                                        <div class="col-md-9 value">                                            
                                                        <?php echo $this->Form->input('Address.stateprovince',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info" id="state_id-div">
                                                        <div class="col-md-5 name">
                                                             State (US Only):
                                                        </div>
                                                        <div class="col-md-9 value">                                            
                                                        <?php echo $this->Form->input('Address.state_id',array('label' => false, 'class' => 'form-control input-large select2me','div' =>false, 'empty' => 'Select...')); ?>
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
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
