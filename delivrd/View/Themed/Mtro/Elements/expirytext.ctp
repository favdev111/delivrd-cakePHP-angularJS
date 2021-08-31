<?php if($_authUser['User']['role'] == 'trial') { ?>
    <?php $remaining_days = $this->App->getRemainingDays($_authUser['Subscription']['expiry_date']); ?>
                    
    <?php if($remaining_days <= 5 && $remaining_days > 0 ) { ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-share font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Delivrd Trial Ends</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="alert alert-block alert-danger fade in" style="background-color:#fbe1e3;color:#e73d4a">
                    <h4 class="alert-heading">Trial expiry notice</h4>
                    <p>
                        Your trial will expire in <?php echo $remaining_days; ?> days. <BR />To continue managing your inventory with Delivrd, please subscribe: <BR />
                        <a href="<?php echo $this->Html->url(['plugin' => false, 'controller' => 'subscriptions', 'action' => 'presignin']); ?>" taraget='' class='btn' style='margin: 8px 0px 10px 2px;border-radius: 6px;color: #fff;background-color: #f19071;border-color: #f19071;'>SUBSCRIBE TO DELIVRD</a>
                    </p>
                </div>
            </div>
        </div>
    <?php } elseif($remaining_days <= 0) { ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-share font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Delivrd Trial Ends</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="alert alert-block alert-danger fade in" style="background-color:#fbe1e3;color:#e73d4a">
                    <button type="button" class="close" data-dismiss="alert"></button>
                    <h4 class="alert-heading">Trial expiry notice</h4>
                    <p>
                        Your trial has expired.<BR />To continue managing your inventory with Delivrd, please subscribe: <BR />
                        <a href="<?php echo $this->Html->url(['plugin' => false, 'controller' => 'subscriptions', 'action' => 'presignin']); ?>" taraget='' class='btn' style='margin: 8px 0px 10px 2px;border-radius: 6px;color: #fff;background-color: #f19071;border-color: #f19071;'>SUBSCRIBE TO DELIVRD</a> <BR />
                        If you have any questions, please <a href='https://delivrd.freshdesk.com/support/tickets/new' taraget='_blank' >contact our support</a>
                    </p>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>