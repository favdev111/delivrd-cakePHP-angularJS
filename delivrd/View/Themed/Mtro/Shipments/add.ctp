<?php
    if($currenOrder['Order']['ordertype_id'] == 1) {
        $indexurl = "/shipments/index/1";
        $pagetext = "Outbound Shipments";
        $pagecolor = "red-flamingo";
        $icon = "fa-space-shuttle";
        $trackinggenerator = 0;
    } else {
        $indexurl = "/shipments/index/1";
        $pagetext = "Inbound Shipments";
        $pagecolor = "green-jungle";
        $icon = "fa-plane";
        $trackinggenerator = 0;
    }
?>

<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>

        <!-- BEGIN PAGE CONTENT-->
        <?php echo $this->Form->create('Shipment'); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box <?php echo $pagecolor ?>">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa <?php echo $icon ?>"></i>Create <?php echo $pagetext ?> <span class="hidden-480"></span>
                            </div>
                            <div class="actions">
                                <a href="#" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> <span class="hidden-480"> Back </span> </a>
                                <button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <?php echo $this->Session->flash(); ?>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="portlet grey-gallery box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i><?php echo $pagetext ?> Details
                                            </div>
                                        </div>
                                        <div class="portlet-body">

                                             <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    <label class="control-label">
                                                        Tracking Number: <span class="required">*</span>
                                                    </label>
                                                </div>
                                                <div class="col-md-7 value">
                                                    <?php  
                                                        if ($trackinggenerator == 1) {
                                                            echo $this->Form->input('tracking_number',array( 'label' => false, 'class' => 'form-control', 'readonly' => true, 'value' => $trackingnumber ));
                                                        } else {
                                                            echo $this->Form->input('tracking_number',array( 'label' => false, 'class' => 'form-control'));
                                                        }
                                                    ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                     Shipping Costs(<?php echo $this->Session->read('currencyname'); ?>):
                                                </div>
                                                <div class="col-md-7 value">
                                                     <?php  echo $this->Form->input('shipping_costs',array( 'label' => false, 'class' => 'form-control' )); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    <label class="control-label">
                                                        Courier: <span class="required">*</span>
                                                    </label>
                                                </div>
                                                <div class="col-md-7 value">
                                                    <span class="courierDropDown">
                                                    <?php echo $this->Form->input('courier_id',array( 'label' => false, 'class' => 'form-control select2me','empty' => 'Select...' )); ?>
                                                    </span><a href="#" data-toggle="modal" data-target="#courierForm"><span class="btn btn-sm blue-steel"> Create Courier</span></a>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    Weight:
                                                </div>
                                                <div class="col-md-7 value">
                                                    <?php  echo $this->Form->input('weight',array( 'label' => false, 'class' => 'form-control' )); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    Remarks:
                                                </div>
                                                <div class="col-md-7 value">
                                                    <?php  echo $this->Form->input('notes',array( 'label' => false, 'class' => 'form-control' )); ?>
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
        </form>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<div class="modal fade" id="courierForm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo __('Add New Courier'); ?></h4>
            </div>

            <?php echo $this->Form->create('Courier', array('url' => array('controller' => 'couriers', 'action' => 'create'), 'class' => 'form-horizontal', 'id' => 'createcourierForm')); ?>
            <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Name'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('name', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn">Save Courier</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>