<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Invenotry Alert</h4>
    </div>

    <?php echo $this->Form->create('Invalert', array('class' => 'form-horizontal form-row-seperated','id' => 'count-inv')); ?>
    <div class="modal-body">
        <?php echo $this->Session->flash(); ?>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-4 control-label">Location: <span class="required">*</span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('warehouse_id', array('class' => 'form-control', 'empty' => 'Select Location', 'div' =>false, 'label' => false, 'required' => true)); ?>
                    </div>
                </div> 

                <div class="form-group">
                    <label class="col-md-4 control-label">Product: <span class="required">*</span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('product_id', array('class' => 'form-control', 'empty' => 'Select Product', 'div' =>false, 'label' => false, 'required' => true)); ?>
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
	$('select#InvalertWarehouseId').select2({
        minimumResultsForSearch: -1,
    });
    $('select#InvalertProductId').select2();
    $('#count-inv').submit(function() {
        $btn = $('#clicksave').button('loading');
    });
</script>
