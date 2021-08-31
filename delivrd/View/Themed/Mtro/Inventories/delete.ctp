<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Delete Invntory: <?= $inventory['Warehouse']['name']; ?> <i class="fa fa-angle-right"></i> <?= $inventory['Product']['name']; ?></h4>
    </div>

    <?php echo $this->Form->create('Inventory', array('class' => 'form-horizontal form-row-seperated','id' => 'count-inv')); ?>
    <div class="modal-body">
        <?php echo $this->Session->flash(); ?>

        <div class="row">
            <div class="col-md-12">
                <?php $show_footer = true; ?>
                <?php if($inventory['Inventory']['user_id'] == $authUser['id']) { ?>
                    <?php if($inventory['Warehouse']['name'] == 'Default') { ?>
                        <div class="alert alert-danger">
                            <strong class="lead">You cannot delete a <strong>Default</strong> inventory location record.</strong><br>
                            You can rename Default inventory location to any other name
                        </div>
                    <?php } elseif(!empty($so_lines) || !empty($po_lines)) { ?>
                        <div class="alert alert-danger">
                            <strong class="lead">Inventory record cannot be deleted.</strong><br>
                            <?php if(!empty($so_lines)) { ?>
                            <div class="margin-bottom-20" style="padding:5px;">
                                <strong>Sales orders exists for product <?= $inventory['Product']['sku']; ?>, location <?= $inventory['Warehouse']['name']; ?></strong>
                                <?php foreach ($so_lines as $line) { ?>
                                    <div style="padding-left: 10px;">Order number: <b><?php echo $line['Order']['id']; ?></b> Ref. order number: <b><?php echo h($line['Order']['external_orderid']); ?></b></div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            <?php if(!empty($po_lines)) { ?>
                            <div class="margin-bottom-20" style="padding:5px;">
                                <strong>Purchase orders exists for product <?= $inventory['Product']['sku']; ?>, location <?= $inventory['Warehouse']['name']; ?></strong>
                                <?php foreach ($po_lines as $line) { ?>
                                    <div style="padding-left: 10px;">Order number: <b><?php echo $line['Order']['id']; ?></b> Ref. order number: <b><?php echo h($line['Order']['external_orderid']); ?></b></div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        </div>
                    <?php } else { $show_footer = false; ?>
                        <h3 class="text-center text-warning margin-bottom-30">
                            <?php echo __('Are you sure you want to delete inventory record for product SKU %s?', h($inventory['Product']['sku'])); ?>
                        </h3>
                    
                    <div class="text-center">
                        <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button class="btn green" type="submit" id='clicksave'><i class="fa fa-check"></i> Confirm</button>
                    </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="alert alert-danger">
                        <strong class="lead">You have no access to delete this inventory location record.</strong><br>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php if($show_footer) { ?>
    <div class="modal-footer">
        <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
    <?php } ?>
    <?php echo $this->Form->end(); ?>
</div>
