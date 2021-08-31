<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title" id="modal-title"></h4>
    </div>

    <?php echo $this->Form->create('Inventory', array('url' => array('controller' => 'inventories', 'action' => 'create'), 'class' => 'form-horizontal', 'id' => 'receive-form')); ?>
    <div class="modal-body">
        <div id="response-bin"></div>
        <div class="form-group">
            <label class="control-label col-md-3"><?php echo __('Location'); ?><span class="required">* </span></label>
            <div class="col-md-8">
            <?php echo $this->Form->hidden('id',array('value' => '','id' => 'inventory-id'));
             echo $this->Form->hidden('ttype',array('value' => '', 'id' => 'inventory-ttype')); ?>
            <?php echo $this->Form->input('warehouse_id',array('value' => '','label' => false, 'class' => 'form-control','div' =>false , 'id' => 'dmq','readonly' => true, 'disabled')); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3"><?php echo __('Available Qty.'); ?><span class="required">* </span></label>
            <div class="col-md-8">
                <?php echo "<button type='button' class='btn btn-default available-qty' id='available-qty'></button>" ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3"><?php echo __('Quantity'); ?></label>
            <div class="col-md-8">
            <?php echo $this->Form->input('tquantity',array('label' => false, 'class' => 'form-control','div' =>false, 'id' => 'tqty','min' => '0','type' => 'number', 'required' => 'true')); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3"><?php echo __('Remarks'); ?></label>
            <div class="col-md-8">
           <?php echo $this->Form->input('comments',array('label' => false, 'class' => 'form-control','div' =>false , 'id' => 'cremarks')); ?>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary saveBtn">Save</button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>