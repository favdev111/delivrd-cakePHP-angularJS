<?php //$this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" id="product_add_html"  ng-controller="AddProduct">
   <?php echo $this->element('product_add');?>
</div>
<!-- END CONTENT -->
<ol id="joyRideTipContent">
    <li data-class="nav-tabs" data-text="Next" class="custom" data-options="tipLocation:left">
        <h2>Product</h2>
        <p>Product data is grouped by tabs.</p>
    </li>

    <li data-id="bastab" data-button="Next" data-options="tipLocation:left">
        <h2>Basic</h2>
        <p>Contains all mandatory fields - short and long name, category,SKU and price</p>
    </li>
    <li data-id="dimtab" data-button="Next" data-options="tipLocation:left">
        <h2>Dimesions</h2>
        <p>Contains product dimensions - weight, width, height, length </p>
    </li>
    <li data-id="urltab" data-button="Next" data-options="tipLocation:left">
        <h2>URL</h2>
        <p>Product, image URL</p>
    </li>
    <li data-id="logtab" data-button="Next" data-options="tipLocation:left">
        <h2>Logistics</h2>
        <p>Logistics related data: barcode, safety stock, bin</p>
    </li>
    <li data-id="pactab" data-button="Next" data-options="tipLocation:left">
        <h2>Packaging</h2>
        <p>Contains packaging related data - packaging mateiral, packing instrucitons etc.</p>
    </li>
    <li data-id="atrtab" data-button="Next" data-options="tipLocation:left">
        <h2>Attributes</h2>
        <p>Set product color and size</p>
    </li>
    <li data-id="savebtn" data-button="Close" data-options="tipLocation:left">
        <h2>Save Product</h2>
        <p>Once product data is entered, click this button to save the new product.</p>
    </li>

</ol>
<?php /*
<div class="modal fade" id="addCategoryForm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo __('Add New Category'); ?></h4>
            </div>

            <?php echo $this->Form->create('Category', array('url' => array('controller' => 'categories', 'action' => 'create'), 'class' => 'form-horizontal', 'id' => 'createCatetegoryForm')); ?>
            <div class="modal-body">
                <div id="response"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Name'); ?> <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('name', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Description'); ?> <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->textarea('description', array('class' => 'form-control', 'rows' => 4)); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn">Save Category</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>*/ ?>

<div class="modal fade" id="addBinForm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo __('Add New Bin'); ?></h4>
            </div>

            <?php echo $this->Form->create('Bin', array('url' => array('controller' => 'bins', 'action' => 'create'), 'class' => 'form-horizontal', 'id' => 'createBinForm')); ?>
            <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Title'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('title', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Sort Sequence'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('sort_sequence', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Location'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('location_id', array('class' => 'form-control', 'label' => false,'empty' => 'Select...')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Status'); ?></label>
                    <div class="col-md-8">
                     <?php echo $this->Form->input('status',array( 'label' => false, 'class' => 'form-control input-sm','empty' => 'Select...','options' => $status )); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn">Save Bin</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<?php echo $this->Html->script('/app/Products/add.js?v=0.0.1', array('block' => 'pageBlock')); ?>


<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var channel_prices = <?php echo json_encode(array()); ?>;
    var schannels = <?php echo json_encode($schannels); ?>;
    var schannels_a = <?php echo json_encode($schannels_a); ?>;

    var product_parts = <?php echo json_encode(array()); ?>;
    var parts = <?php echo json_encode($parts); ?>;
    var parts_a = <?php echo json_encode($parts_a); ?>;

    <?php if($show_trial) { ?>
    $(document).ready(function () {
        $('#startTrialHidden').trigger('click');

        $(document).on('submit','#add_product_form',function (event) {
            $('#startTrialHidden').trigger('click');
        });
    });
    <?php } ?>
<?php $this->Html->scriptEnd(); ?>

<?php $this->start('jsSection'); ?>
<script type="text/javascript">

    $('#KitPartlId').select2({
    });
    
    <?php if(!$show_trial) { ?>
    $(document).ready(function () {
        $(document).on('submit','#add_product_form',function (event) {
            var formData = $('#add_product_form').serialize();
            alert('Product add submit');
            event.preventDefault();
            $.ajax({
                type: 'POST',
                //url: $('#add_product_form').attr('action'),
                data: formData,
                beforeSend: function ()
                {
                    $('#savebtn').html('<i class="fa fa-spinner"></i> Saving...');
                    $('#savebtn').attr('disabled', true);
                    $(".error-message").remove();

                },
                success: function (response) {
                    if(response == 'done'){
                        window.location.href = '<?php echo Router::url(array('action' => 'index'),true);?>';
                    }
                    $('#product_add_html').html(response);
                },
                error: function () {
                    alert('Somethig went wrong. Please refresh page and try again.');
                }
            });

        });
    });
    <?php } ?>
</script>
<?php $this->end(); ?>
