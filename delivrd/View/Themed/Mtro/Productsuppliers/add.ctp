<?php $this->AjaxValidation->active(); ?>

<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>
        
        <!-- BEGIN PAGE CONTENT-->
        <?php echo $this->Session->flash(); ?>
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Form->create('Productsupplier', array('class' => 'form-horizontal form-row-seperated')); ?>           
                    <div class="portlet box yellow-gold">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-exchange"></i> <?php echo $title; ?>
                            </div>
                            <div class="actions btn-set">
                                <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                                <button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
                            </div>
                        </div>
                            
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-8 col-sm-12">
                                    <div class="portlet grey-gallery box">
                                        <div class="portlet-title">
                                            <div class="caption"><i class="fa fa-cogs"></i> Details</div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    <label class="control-label">Product: <span class="required">*</span></label>
                                                </div>
                                                <div class="col-md-7 value">
                                                    <?php  echo $this->Form->input('product_id',array('label' => false,'data-placeholder' => 'Select...','class' => 'form-control input-large select2me', 'id' => 'select_product_id','div' =>false, 'empty' => 'Select...', 'required')); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    <label class="control-label">Supplier: <span class="required">*</span></label>
                                                </div>
                                                <div class="col-md-7 value">
                                                    <?php  echo $this->Form->input('supplier_id',array('label' => false,'data-placeholder' => 'Select...','class' => 'form-control input-large select2me', 'id' => 'select_supplier_id','div' =>false, 'empty' => 'Select...', 'required')); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    <label class="control-label">Manufacturer Part Number:</label>
                                                </div>
                                                <div class="col-md-7 value">
                                                    <?php echo $this->Form->input('part_number', array('label' => false, 'class' => 'form-control input-large', 'div' =>false)); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    <label class="control-label">Price (<?php echo $this->Session->read('currencyname'); ?>): </label>
                                                </div>
                                                <div class="col-md-7 value">
                                                    <?php echo $this->Form->input('price', array('label' => false, 'class' => 'form-control input-large', 'div' => false)); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    <label class="control-label">Status: </label>
                                                </div>
                                                <div class="col-md-7 value">
                                                    <?php  echo $this->Form->input('status',array( 'label' => false, 'class' => 'form-control input-large','options' => $status)); ?>                                  
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->
