<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>
        
        <?php echo $this->Session->flash(); ?>

        <!-- BEGIN PAGE CONTENT-->
        <?php echo $this->Form->create('User', array('url' => array('plugin' => 'users', 'controller' => 'users', 'action' => 'edit'), 'class' => 'form-horizontal', 'id' => 'createUserForm')); ?>
        <?php echo $this->Form->hidden('User.default_country_id'); ?>
        <div class="row">
            <div class="col-md-18">
                <!-- Begin: life time stats -->
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>Update system settings
                        </div>
                        <div class="actions">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                            <button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-8 col-sm-12">
                                <div class="portlet yellow-crusta box">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i>System Settings
                                        </div>
                                    </div>

                                    <div class="portlet-body">
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Show page Tours
                                            </div>
                                            <div class="col-md-7 value">
                                                <div class="md-checkbox">
                                                    <?php echo $this->Form->input('User.showtours',array('label' => false, 'class' => 'md-check saveCheck', 'data-name'=>'showtours', 'placeholder' => '', 'div'=>false,'id'=>'stcheckbox')); ?>
                                                    <label for="stcheckbox">
                                                        <span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Multiple inventory locations
                                                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Allows you to manage products in more than one inventory location"></i>
                                            </div>
                                            <div class="col-md-7 value">
                                                <div class="md-checkbox">
                                                    <?php echo $this->Form->input('User.locationsactive',array('label' => false, 'class' => 'md-check saveCheck', 'data-name'=>'locationsactive', 'placeholder' => '', 'div'=>false,'id'=>'lacheckbox')); ?>
                                                    <label for="lacheckbox" id="locationsactiveCheck">
                                                        <span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Order management:
                                                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Allow you to manage sales order and purchase orders in Delivrd"></i>
                                            </div>
                                            <div class="col-md-7 value">
                                                <div class="md-checkbox">
                                                    <?php echo $this->Form->input('User.paid',array('label' => false, 'class' => 'md-check saveCheck', 'data-name'=>'paid', 'placeholder' => '', 'div'=>false,'id'=>'pdcheckbox')); ?>
                                                    <label for="pdcheckbox" id="paidCheck">
                                                        <span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Create inventory record automatically during Transfer
                                            </div>
                                            <div class="col-md-7 value">
                                                <div class="md-checkbox">
                                                    <?php echo $this->Form->input('User.inventoryauto',array('label' => false, 'class' => 'md-check saveCheck', 'data-name'=>'inventoryauto', 'placeholder' => '', 'div'=>false,'id'=>'itcheckbox')); ?>
                                                    <label for="itcheckbox">
                                                        <span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Disable remarks in inventory transactions
                                            </div>
                                            <div class="col-md-7 value">
                                                <div class="md-checkbox">
                                                    <?php echo $this->Form->input('User.inventoryremarks',array('label' => false, 'class' => 'md-check saveCheck', 'data-name'=>'inventoryremarks', 'placeholder' => '', 'div'=>false,'id'=>'ircheckbox')); ?>
                                                    <label for="ircheckbox">
                                                        <span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Copy product price to sales order line
                                            </div>
                                            <div class="col-md-7 value">
                                                <div class="md-checkbox">
                                                    <?php echo $this->Form->input('User.copypdtprice',array('label' => false, 'class' => 'md-check saveCheck', 'data-name'=>'copypdtprice', 'placeholder' => '', 'div'=>false,'id'=>'copyprice')); ?>
                                                    <label for="copyprice">
                                                        <span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Allow create order line for any products
                                                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Allow create order lines for product with zero or negative quantity or even if inventory not exists"></i>
                                            </div>
                                            <div class="col-md-7 value">
                                                <div class="md-checkbox">
                                                    <?php echo $this->Form->input('User.zeroquantity',array('label' => false, 'class' => 'md-check saveCheck', 'data-name'=>'zeroquantity', 'placeholder' => '', 'div'=>false, 'id'=>'zeroquantity')); ?>
                                                    <label for="zeroquantity">
                                                        <span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Show product description in packing slip
                                                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Show product description in packing slip"></i>
                                            </div>
                                            <div class="col-md-7 value">
                                                <div class="md-checkbox">
                                                    <?php echo $this->Form->input('User.packinslip_desc',array('label' => false, 'type'=>'checkbox', 'class' => 'md-check saveCheck', 'data-name'=>'packinslip_desc', 'placeholder' => '', 'div'=>false, 'id'=>'packinslip_desc')); ?>
                                                    <label for="packinslip_desc">
                                                        <span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">Kit components issue:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.kit_component_issue', array('label' => false, 'options' => ['build'=>'When a kit is built', 'issued'=>'When a kit is issued'], 'data-name'=>'kit_component_issue', 'class' => 'form-control input-medium saveSelect')); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">Default number of results in list page:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.list_limit', array('label' => false, 'options' => [10=>10, 25=>25, 50=>50, 100=>100], 'data-name'=>'list_limit', 'class' => 'form-control input-medium saveSelect')); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">Sales Order Title:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.sales_title',array('label' => false, 'class' => 'form-control input-medium')); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">Inventory Alert:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.inventoryalert',array('label' => false, 'class' => 'form-control input-medium saveSelect', 'data-name'=>'inventoryalert', 'placeholder' => '', 'options' => $invAlert)); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">Fast Inventory Alert:</div>
                                            <div class="col-md-7 value">
                                                <div class="md-checkbox">
                                                    <?php echo $this->Form->input('User.fast_invalert',array('label' => false, 'type'=>'checkbox', 'class' => 'md-check saveCheck', 'data-name'=>'fast_invalert', 'placeholder' => '', 'div'=>false, 'id'=>'fast_invalert')); ?>
                                                    <label for="fast_invalert">
                                                        <span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">Currency:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.currency_id',array('label' => false, 'class' => 'form-control input-medium select2me saveSelect', 'data-name'=>'currency_id')); ?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">Measurement System:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.msystem_id',array('label' => false, 'class' => 'form-control input-medium saveSelect', 'data-name'=>'msystem_id')); ?>
                                            </div>
                                        </div>
                                        <div class="row static-info" id="stateprovince-div">
                                            <div class="col-md-5 name">Batch Pick:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.batch_pick',array('label' => false, 'id' => 'scan', 'class' => 'form-control input-medium saveSelect', 'data-name'=>'batch_pick', 'placeholder' => '', 'empty' => 'Select', 'options' => $batch)); ?>
                                            </div>
                                        </div>
                                        <div class="row static-info" id="stateprovince-div">
                                            <div class="col-md-5 name">Pick By Order:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.pick_by_order',array('label' => false, 'class' => 'form-control input-medium saveSelect', 'data-name'=>'pick_by_order', 'placeholder' => '', 'empty' => 'Select', 'options' => $pickbyorder)); ?>
                                            </div>
                                        </div>
                                        <div class="row static-info" id="stateprovince-div">
                                            <div class="col-md-5 name">Timezone:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.timezone_id',array('label' => false, 'id' => 'timezone', 'class' => 'form-control input-medium select2me saveSelect', 'data-name'=>'timezone_id', 'placeholder' => '', 'empty' => 'Select', 'options' => $timezone)); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 col-sm-12">
                                <div class="portlet green-jungle box">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-usd"></i> Subscriptions
                                        </div>
                                    </div>

                                    <div class="portlet-body">
                                        <?php if($_authUser['User']['role'] == 'trial') { ?>
                                        <div class="text-center">
                                            <p>You can stop trial period and later continue it from same period</p>
                                            <?php echo $this->Html->link(
                                                    __('<i class="fa fa-stop"></i> Switch to Free'),
                                                    array('plugin' => false, 'controller' => 'user', 'action' => 'stop_trial'),
                                                    array('class' => 'btn red-intense', 'escape' => false, 'data-toggle' => 'modal', 'data-target' => '#ajaxModal')
                                                );
                                            ?>
                                        </div>
                                        <?php } else if($_authUser['User']['role'] == 'paid') { ?>
                                            <div class="text-center">
                                                <h4>You have <strong>Unlimited</strong> plan</h4>
                                                <?php if($_authUser['Subscription']['id']) { ?>
                                                    <?php $memo = json_decode($_authUser['Subscription']['memo']); ?>
                                                    <table class="table">
                                                        <tr>
                                                            <td>Account Status:</td>
                                                            <td><?php echo ucfirst($_authUser['Subscription']['status']); ?></td>
                                                        </tr>
                                                        <?php if($_authUser['Subscription']['ext_id'] == 'WAIT-CONFIRM') { ?>
                                                        <tr>
                                                            <td>Subscription ID:</td>
                                                            <td >
                                                                Wait for payment confirmation.
                                                            </td>
                                                        </tr>
                                                        <?php } else { ?>
                                                            <tr>
                                                                <td>Subscription ID:</td>
                                                                <td><?php echo $_authUser['Subscription']['ext_id']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Amount:</td>
                                                                <td>$<?php echo $_authUser['Subscription']['amount']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Paid till:</td>
                                                                <td><?php echo date('Y-m-d H:i', strtotime($_authUser['Subscription']['expiry_date'])); ?></td>
                                                            </tr>
                                                            <?php if($memo && isset($memo->payment_date)) { ?>
                                                                <tr>
                                                                    <td>Last Payment Date:</td>
                                                                    <td><?php echo date('Y-m-d H:i', strtotime($memo->payment_date)); ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </table>
                                                    
                                                    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&switch_classic=true" class="btn red-intense">
                                                        <i class="fa fa-ban"></i> Cancel Subscription
                                                    </a>
                                                <?php } else { ?>
                                                    <p>Your plan was created by Admin. To know more details contact with administration.</p>
                                                    <?php echo $this->Html->link(
                                                            __('<i class="fa fa-stop"></i> Switch to Free'),
                                                            array('plugin' => false, 'controller' => 'user', 'action' => 'stop_trial'),
                                                            array('class' => 'btn red-intense', 'escape' => false, 'data-toggle' => 'modal', 'data-target' => '#ajaxModal')
                                                        );
                                                    ?>
                                                <?php } ?>
                                            </div>
                                        <?php } else { //if have free account ?>
                                        <div class="text-center">
                                            <p>Start free trial period</p>
                                            <?php echo $this->Html->link(
                                                    __('<i class="fa fa-start"></i> Start free trial'),
                                                    array('plugin' => false, 'controller' => 'user', 'action' => 'start_trial'),
                                                    array('class' => 'btn btn-success', 'escape' => false, 'data-toggle' => 'modal', 'data-target' => '#ajaxModal')
                                                );
                                            ?>
                                        </div>

                                        <?php } ?>
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
<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    $(document).ready(function() {
        $('.saveCheck').click(function() {
            var box = $(this);
            var name = box.data('name');
            var value = 0;
            if(box.attr('checked') == 'checked') {
                value = 1;
            }
            <?php if($_authUser['User']['role'] != 'paid' && $_authUser['User']['role'] != 'trial') { ?>
            if( (name == 'locationsactive' || name == 'paid') && value == 1 ) {
                // show modal
                $('#startTrialHidden').trigger('click');
                return false;
            }
            <?php } ?>
            $.ajax({
                type: 'POST',
                url: '<?php echo $this->Html->url(array('plugin'=>'users', 'controller'=>'users', 'action'=>'saveparam')); ?>',
                data: 'name='+name+'&value='+value,
                dataType:'json',
                beforeSend: function() {
                    
                },
                success:function (r, status) {
                    
                }
            });
        });

        $('.saveSelect').change(function() {
            var box = $(this);
            var name = box.data('name');
            var value = box.val();
            
            $.ajax({
                type: 'POST',
                url: '<?php echo $this->Html->url(array('plugin'=>'users', 'controller'=>'users', 'action'=>'saveparam')); ?>',
                data: 'name='+name+'&value='+value,
                dataType:'json',
                beforeSend: function() {
                    
                },
                success:function (r, status) {
                    
                }
            });
        });

        $('#UserKitComponentIssue').select2({
            minimumResultsForSearch: -1
        });

        $('#UserListLimit').select2({
            minimumResultsForSearch: -1
        });

        $('#UserInventoryalert').select2({
            minimumResultsForSearch: -1
        });

        $('#UserMsystemId').select2({
            minimumResultsForSearch: -1
        });

        $('#scan').select2({
            minimumResultsForSearch: -1
        });

        $('#UserPickByOrder').select2({
            minimumResultsForSearch: -1
        });
    })
<?php $this->Html->scriptEnd(); ?>
</script>