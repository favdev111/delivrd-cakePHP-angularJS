<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Invenotry Alert <?php echo h($warehouse['Warehouse']['name']) ?> &raquo; <?php echo h($product['Product']['name']) ?></h4>
    </div>

    <?php echo $this->Form->create('Invalert', array('class' => 'form-horizontal form-row-seperated','id' => 'count-inv')); ?>
    <div class="modal-body">
        <?php echo $this->Session->flash(); ?>

        <div class="row">
            <div class="col-md-12">
                <?php #echo $this->element('sql_dump');?>
                <?php echo $this->Form->hidden('product_id'); ?>
                <?php echo $this->Form->hidden('warehouse_id'); ?>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Warehouse: <span class="required">*</span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('product', array('value' => $warehouse['Warehouse']['name'], 'class' => 'form-control', 'div' =>false, 'label' => false, 'readonly' => true)); ?>
                    </div>
                </div> 

                <div class="form-group">
                    <label class="col-md-4 control-label">Product: <span class="required">*</span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('product', array('value' => $product['Product']['name'], 'class' => 'form-control', 'div' =>false, 'label' => false, 'readonly' => true)); ?>
                    </div>
                </div>  
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Reorder Point:</label>
                    <div class="col-md-4">
                        <?php echo $this->Form->input('reorder_point', array('label' => false, 'class' => 'form-control', 'default' => 0, 'div' => false, 'min' => '0','type' => 'number', 'step' => '1', 'required' => 'true')); ?>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-md-4 control-label">Safety Stock:</label>
                    <div class="col-md-4">
                        <?php echo $this->Form->input('safety_stock', array('label' => false, 'class' => 'form-control', 'default' => 0, 'div' => false, 'min' => '0','type' => 'number', 'step' => '1', 'required' => 'true')); ?>
                    </div>
                </div>
                    
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal">Close</button>
        <button class="btn green" type="submit" id="clicksave"><i class="fa fa-check"></i> Save</button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<script>
    $('#count-inv').submit(function() {
        $btn = $('#clicksave').button('loading');
    });
</script>
