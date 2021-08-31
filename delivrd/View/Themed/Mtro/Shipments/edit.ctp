<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>
        
        
        <?php
            $type_text = ($shipment['Shipment']['direction_id'] == 1 ? "Outbound" : "Inbound");
            $portletcolor =  ($shipment['Shipment']['direction_id'] == 1 ? "red-flamingo" : "green-jungle");
            $icon = ($shipment['Shipment']['direction_id'] == 1 ? "fa-space-shuttle" : "fa-plane");
        ?>     
                

        <!-- BEGIN PAGE CONTENT-->
        <?php echo $this->Session->flash(); ?>
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Form->create('Shipment', array('class' => 'form-horizontal form-row-seperated')); ?>
                    <?php echo $this->Form->input('id',array('hidden' => true)); ?>   
                    <div class="portlet box <?php echo $portletcolor; ?>">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa <?php echo $icon ?>"></i>Edit Shipment #<?php echo $this->Html->link(__($shipment['Shipment']['id']), array('action' => 'view', $shipment['Shipment']['id'])); ?> <span class="hidden-480">
                                </span>
                            </div>
                            <div class="actions btn-set">
                                <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                                <button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>

                                <div class="btn-group pull-right" style="margin-left: 10px;">
                                    <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-edit"></i> Edit'), array('action' => 'edit',$shipment['Shipment']['id']), array('escape'=> false)); ?></li>
                                        <?php if ($shipment['Shipment']['status_id'] == 15) { ?>
                                            <li>
                                                <?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $shipment['Shipment']['id']), array('escape'=> false), __('Are you sure you want to delete shipment # %s?', $shipment['Shipment']['id'])); ?>
                                            </li>
                                        <?php }
                                        //We allow Pick & Pack for both open shipments and partialy picked shipments
                                        if ($shipment['Shipment']['status_id'] == 15 || $shipment['Shipment']['status_id'] == 16)
                                        {       
                                                if($shipment['Order']['ordertype_id'] == 1)
                                                {
                                                    echo "<li>" . $this->Form->postLink(__('<i class="fa fa-cube"></i> Pick & Pack'), array('action' => 'send', $shipment['Shipment']['id']), array('escape'=> false)). "</li>"; 
                                                }
                                                if($shipment['Order']['ordertype_id'] == 2) 
                                                {
                                                    echo "<li>" . $this->Form->postLink(__('<i class="fa fa-arrow-left"></i> Receive Shipment Products'), array('action' => 'receive', $shipment['Shipment']['id']), array('escape'=> false)) . "</li>";
                                                }
                                                        
                                        }
                                        if ($shipment['Shipment']['status_id'] == 6)
                                        { 
                                            echo "<li>" . $this->Form->postLink(__('<i class="fa fa-undo"></i> Revert Pick&Packed'), array('action' => 'packuncomplete', $shipment['Order']['id']), array('escape'=> false)) . "</li>"; 
                                            echo "<li>" . $this->Form->postLink(__('<i class="fa fa-print"></i> Print Shipping Label'), array('controller' => 'pdfs', 'action' => 'shiplabelee', $shipment['Shipment']['id']), array('escape'=> false)) . "</li>"; 
                                            echo "<li>" . $this->Form->postLink(__('<i class="fa fa-flag-checkered"></i> Complete shipment'), array('action' => 'ship', $shipment['Shipment']['id']), array('escape'=> false)) . "</li>";           
                                        }
                    
                                        if ($shipment['Shipment']['status_id'] == 8 || $shipment['Shipment']['status_id'] == 7)
                                        { 
                                            echo "<li>" . $this->Html->link(__('<i class="fa fa-undo"></i> Revert Complete shipment'), array('action' => 'uncomplete', $shipment['Shipment']['id']), array('escape'=> false)) . "</li>";            
                                        }
                                        ?>  
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="portlet-body">
                            <?php echo $this->Session->flash(); ?>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="portlet grey-gallery box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>Shipment Details
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="row static-info">
                                                <div class="col-md-5 name">Tracking Number: <span class="text-danger">*</span></div>
                                                <div class="col-md-7 value">
                                                     <?php  echo $this->Form->input('tracking_number',array( 'label' => false, 'class' => 'form-control' )); ?> 
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">Shipping Costs(<?php echo $this->Session->read('currencyname'); ?>):
                                                </div>
                                                <div class="col-md-7 value">
                                                     <?php  echo $this->Form->input('shipping_costs',array( 'label' => false, 'class' => 'form-control' )); ?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    Courier: <span class="text-danger">*</span>
                                                </div>
                                                <div class="col-md-7 value">
                                                <span class="courierDropDown">
                                                    <?php  echo $this->Form->input('courier_id',array( 'label' => false, 'class' => 'form-control select2me', 'empty' => 'Select...', 'required' => true)); ?>
                                                    <?php if($shipment['Order']['user_id'] == $authUser['id']) { // we alow to add curier only owner ?>
                                                        </span><a href="#" data-toggle="modal" data-target="#courierForm"><span class="btn btn-sm blue-steel"> Create Courier</span></a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">Weight:</div>
                                                <div class="col-md-7 value">
                                                    <?php  echo $this->Form->input('weight',array( 'label' => false, 'class' => 'form-control' )); ?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">Remarks:</div>
                                                <div class="col-md-7 value">
                                                    <?php echo $this->Form->input('notes',array('label' => false, 'class' => 'form-control'));?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
<!-- END CONTENT -->