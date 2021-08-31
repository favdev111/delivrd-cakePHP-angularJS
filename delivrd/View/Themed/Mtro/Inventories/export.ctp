<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title" id="modal-title"><i class="fa fa-download"></i> Export Inventory</h4>
    </div>

    <?php echo $this->Form->create('Inventory', array('url' => array('controller' => 'inventories', 'action' => 'exportcsv'), 'class' => 'form-horizontal', 'id' => 'receive-form')); ?>
    <div class="modal-body clearfix">

        <?php #echo $this->Html->link(__('<i class="fa fa-download"></i> Export'), array('controller'=> 'inventories','action' => 'exportcsv'),array('escape'=> false, 'class' => 'csv-icons', 'title' => 'Export inventory list')); ?>
        <div class="alert alert-info">Please select fields for export</div>
        
        <div class="col-md-12 text-right">
            <input type="checkbox" id="selAll" name="selAll"><strong style="vertical-align: 4px;padding-left: 5px;">Select All</strong>
        </div>

        <div class="col-md-12"><hr style="margin-top: 3px;"></div>

        <div class="col-md-6">
            <?php foreach ($fields as $key => $value) { ?>
                <div class="clearfix">
                    <div class="pull-left"><?php echo $key; ?></div>
                    <div class="pull-right"><input type="checkbox" readonly="true" checked="true" onclick="return false;" name="data[fields][<?php echo $key;?>]" value="<?php echo $value;?>"></div>
                </div>
            <?php } ?>
            <?php $k = 0; foreach ($available_fields as $key => $value) { $k++; ?>
                <div class="clearfix">
                    <div class="pull-left"><?php echo $key; ?></div>
                    <div class="pull-right">
                        <?php echo $this->Form->input('fields.'.$key, array('type' => 'checkbox', 'value' =>  $value, 'class' => 'f_checkboxes', 'label' => false, 'div' => false, 'hiddenField' => false)); ?>
                    </div>
                </div>
                <?php if($k > 9) { break; } ?>
            <?php } ?>
        </div>

        <div class="col-md-6">
            <?php $k = 0; foreach ($available_fields as $key => $value) { $k++; ?>
                <?php if($k < 11) { continue; } ?>
                <div class="clearfix">
                    <div class="pull-left"><?php echo $key; ?></div>
                    <div class="pull-right"><?php echo $this->Form->input('fields.'.$key, array('type' => 'checkbox', 'value' =>  $value, 'class' => 'f_checkboxes', 'label' => false, 'div' => false, 'hiddenField' => false)); ?></div>
                </div>
            <?php } ?>
        </div>

        <?php if($custom_fields) { ?>
            <div class="clearfix"></div>
            <div class="clearfix" style="position: relative;">
                <hr>
                <span style="position: absolute; top:7px; left:9px; font-weight:600; background:#fff;padding:3px 5px;">Custom fields</span>
            </div>

            <?php $k = 0; foreach ($custom_fields as $key => $value) { $k++; ?>
                <div class="col-md-6">
                    <div class="clearfix">
                        <div class="pull-left"><?php echo h($value); ?></div>
                        <div class="pull-right"><?php echo $this->Form->input('custom.'.$key, array('type' => 'checkbox', 'value' =>  $value, 'class' => 'f_checkboxes', 'label' => false, 'div' => false, 'hiddenField' => false)); ?></div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary saveBtn"><i class="fa fa-download"></i> Export</button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>

<script>
$(function() {
    $('#selAll').click(function(){
        if($('#selAll').attr('checked') == 'checked') {
            $('.f_checkboxes').each(function() {
                $(this).prop('checked', true);
            });
        } else {
            $('.f_checkboxes').each(function() {
                $(this).prop('checked', false);
            });
        }
    });

    $('.f_checkboxes').click(function(){
        var is_all = true;
        $('.f_checkboxes').each(function() {
            if($(this).attr('checked') != 'checked') {
                is_all = false;
            }
        });
        $('#selAll').prop('checked', is_all);
    });
});
</script>