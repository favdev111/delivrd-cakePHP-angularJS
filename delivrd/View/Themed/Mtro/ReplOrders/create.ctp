<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->Form->create('Order', array('novalidate' => false));  ?>
    
        <div class="row">
            <div class="col-md-12">
                <!-- Begin: life time stats -->
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-random"></i> Add New Purchase Order
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
                                            <div class="col-md-5 name">
                                                <label class="control-label">Reference Order: 
                                                    <span class="required">*</span>
                                                </label>
                                            </div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('external_orderid',array('label' => false, 'class' => 'form-control')); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                
                                            </div>
                                            <div class="col-md-7 value checkbox" style="padding-left:10px">
                                                <label class="control-label">
                                                    <?php echo $this->Form->input('blanket',array('type' => 'checkbox', 'label' => false, 'div' => false)); ?>
                                                    Is Blanket
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                <label class="control-label">Supplier:
                                                    <span class="required">*</span>
                                                </label>
                                            </div>
                                            <div class="col-md-7 value">
                                                <span class="supplierDropDown">
                                                    <?php echo $this->Form->input('supplier_id',array('label' => false, 'class' => 'form-control select2me','empty' => 'Select...','required' => true)); ?>
                                                </span>
                                                <?php if(!$authUser['is_limited']) { ?>
                                                <a href="#" data-toggle="modal" data-target="#supplierForm"><span class="btn btn-sm blue-steel"> Create Supplier</span></a>
                                                <?php } ?>
                                            </div> 
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Expected Delivery Date:
                                            </div>
                                            <div class="col-md-7 value">
                                                <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd" id="orderdate" data-date-start-date="+0d" >
                                                    <input type="text" class="form-control form-filter" name="data[Order][requested_delivery_date]">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-medium default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Shipping Costs (<?php echo h($this->Session->read('currencyname')) ?>):
                                            </div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('shipping_costs', array('label' => false, 'class' => 'form-control')); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Order URL:
                                            </div>
                                            <div class="col-md-7 value">                                                            
                                                <?php echo $this->Form->input('url',array('label' => false,'class' => 'form-control input-medium')); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Remarks:
                                            </div>
                                            <div class="col-md-7 value">                                                            
                                                <?php echo $this->Form->input('comments',array('label' => false,'class' => 'form-control input-medium')); ?>
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

<div class="modal fade" id="supplierForm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo __('Add New Supplier'); ?></h4>
            </div>

            <?php echo $this->Form->create('Supplier', array('url' => array('controller' => 'suppliers', 'action' => 'create'), 'class' => 'form-horizontal', 'id' => 'createsupplierForm')); ?>
            <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Name'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('name', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Email'); ?></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('email', array('class' => 'form-control', 'label' => false)); ?>
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
                <button type="submit" class="btn btn-primary saveBtn">Save Supplier</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    $(document).ready(function(){
        $('#OrderSupplierId').select2({minimumResultsForSearch: -1});
    });
<?php $this->Html->scriptEnd(); ?>
</script>