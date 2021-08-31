<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div id="flashMessage"></div>
        <?php echo $this->Session->flash(); ?>

        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Form->create('Product', array('class' => 'form-horizontal form-row-seperated', 'id'=>'add-product')); ?>          
                    <div class="portlet box blue-steel">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-barcode"></i> <?php echo h($title); ?>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="portlet grey-gallery box">
                                        <div class="portlet-body">
                                            <div class="row static-info">
                                                <div class="col-md-6 value">
                                                    <label class="control-label">Product Name: <span class="required">*</span></label>
                                                    <div class="btn-group pull-right">
                                                        <span data-toggle="dropdown" aria-expanded="false"><i class="icon-question"></i></span>
                                                        <ul role="menu" class="dropdown-menu pull-right">
                                                            <li><a href="#">Name of your product.</a></li>
                                                        </ul>
                                                    </div>
                                                    <?php  echo $this->Form->input('name',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
                                                </div>
                                                <div class="col-md-6 value">
                                                    <label class="control-label">SKU: <span class="required">*</span></label>
                                                    <div class="btn-group pull-right">
                                                        <span data-toggle="dropdown" aria-expanded="false"><i class="icon-question"></i></span>
                                                        <ul role="menu" class="dropdown-menu pull-right">
                                                            <li><a href="#">Uniquely identifies the product. Also, the SKU should be printed on barcode labels that will be scanned in Delivrd.</a></li>
                                                        </ul>
                                                    </div>
                                                    <?php  echo $this->Form->input('sku',array( 'label' => false, 'class' => 'form-control input-sm', 'required')); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-6 value">
                                                    <label class="control-label">Stock Quantity: <span class="required">*</span></label>
                                                    <div class="btn-group pull-right">
                                                        <span data-toggle="dropdown" aria-expanded="false"><i class="icon-question"></i></span>
                                                        <ul role="menu" class="dropdown-menu pull-right">
                                                            <li><a href="#">Current quantity of inventory.</a></li>
                                                        </ul>
                                                    </div>
                                                     <?php  echo $this->Form->input('safety_stock',array( 'label' => false, 'class' => 'form-control input-sm', 'required')); ?>                                    
                                                </div>
                                                <div class="col-md-6 value">
                                                    <label class="control-label">Reorder Point: <span class="required">*</span></label>
                                                    <div class="btn-group pull-right">
                                                        <span data-toggle="dropdown" aria-expanded="false"><i class="icon-question"></i></span>
                                                        <ul role="menu" class="dropdown-menu pull-right">
                                                            <li><a href="#">When stock falls below this level, alerts are displayed in low inventory alerts monitor.</a></li>
                                                        </ul>
                                                    </div>
                                                    <?php echo $this->Form->input('reorder_point',array( 'label' => false, 'class' => 'form-control input-sm', 'required')); ?>                                   
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <button class="btn blue" name="bttnsubmit" type="submit" id="product">Save and add another</button>
                                                    <button class="btn blue" name="bttnsubmit" type="submit" id="stock">Save and update stock</button>
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