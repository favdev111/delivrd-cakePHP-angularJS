<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>
        
        <!-- BEGIN PAGE CONTENT-->

        <?php echo $this->Session->flash(); ?>
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Form->create('Integration', array('class' => 'form-horizontal form-row-seperated'));?>   
                    <div class="portlet box yellow-saffron">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-exchange"></i>
                                Add Integration
                            </div>
                            <div class="actions btn-set">
                                <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                                <button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
                            </div>
                        </div>
                        
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-9 col-sm-12">
                                    <div class="portlet grey-gallery box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i> Details
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="row static-info">
                                                <label class="col-md-3 control-label">Ecommerce Platform: <span class="required">*</span></label>
                                                <div class="col-md-7 value">
                                                     <?php echo $this->Form->input('backend',array('label' => false, 'class' => 'form-control input-medium select2me','empty' => 'Select...','required' => true,'options' => array('Shopify' => 'Shopify','Woocommerce' => 'Woocommerce'/*,'Amazon' => 'Amazon'*/))); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info Amazon">
                                                <label class="col-md-3 control-label">Marketplace: <span class="required">*</span></label>
                                                <div class="col-md-7 value">
                                                    <?php echo $this->Form->input('marketplace_id',array('label' => false, 'class' => 'form-control input-medium select2me','empty' => 'Select...')); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <label class="col-md-3 control-label Woocommerce Shopify">Username/ Key: <span class="required">*</span></label>
                                                <label class="col-md-3 control-label Amazon">Amazon Seller ID: <span class="required">*</span></label>
                                                <div class="col-md-7 value">
                                                    <?php echo $this->Form->input('username',array('label' => false, 'class' => 'form-control input-extra-large')); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <label class="col-md-3 control-label Woocommerce Shopify">Password / Secret: <span class="required">*</span></label>
                                                <label class="col-md-3 control-label Amazon">Amazon Secret Key: <span class="required">*</span></label>
                                                <div class="col-md-7 value">
                                                    <?php echo $this->Form->input('password',array('type' => 'text','label' => false, 'class' => 'form-control input-extra-large')); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info Amazon">
                                                <label class="col-md-3 control-label ">Amazon Developer Account Number: <span class="required">*</span></label>
                                                <div class="col-md-7 value">
                                                    <?php echo $this->Form->input('account_number',array('type' => 'text','label' => false, 'class' => 'form-control input-extra-large')); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info Amazon">
                                                <label class="col-md-3 control-label">Amazon AWS Access Key Id: <span class="required">*</span></label>
                                                <div class="col-md-7 value">
                                                    <?php echo $this->Form->input('access_key',array('type' => 'text','label' => false, 'class' => 'form-control input-extra-large')); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info Woocommerce Shopify Amazon">
                                                <label class="col-md-3 control-label">URL: <span class="required">*</span></label>
                                                <div class="col-md-7 value">
                                                    <?php echo $this->Form->input('url',array('label' => false, 'class' => 'form-control input-extra-large')); ?>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <label class="col-md-3 control-label">Sales Channel: <span class="required">*</span></label>
                                                <div class="col-md-7 value">
                                                    <span class="schannelDropDown">
                                                        <?php echo $this->Form->input('schannel_id',array('label' => false, 'class' => 'form-control input-medium ','empty' => 'Select...','required' => true)); ?>
                                                    </span>
                                                    <a href="#" data-toggle="modal" data-target="#schannelForm"><span class="btn btn-sm blue-steel"> Create Sales Channel</span></a>
                                                </div> 
                                            </div>

                                            <div class="row static-info">
                                                <label class="col-md-3 control-label"></label>
                                                <div class="col-md-8 value">
                                                    <div class="md-checkbox">
                                                        <?php echo $this->Form->input('is_ecommerce', array('label' => false, 'div'=>false, 'type'=>'checkbox', 'class' => 'form-control md-check', 'id'=>"stcheckbox")); ?>
                                                        <label for="stcheckbox">
                                                            <span></span>
                                                            <span class="check"></span>
                                                            <span class="box"></span>
                                                            Ecommerce platform manages inventory
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<div class="modal fade" id="schannelForm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo __('Add New Sales Channel'); ?></h4>
            </div>

            <?php echo $this->Form->create('Schannel', array('url' => array('controller' => 'schannels', 'action' => 'create'), 'class' => 'form-horizontal', 'id' => 'createschannelForm')); ?>
            <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Name'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('name', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('URL'); ?></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('url', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn">Save Sales Channel</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var amazon_urls = <?php echo json_encode($endpoints); ?>;

    $(function() {
        $('#IntegrationBackend').change(function(){
            var platform = $(this).val();
            platformSelect(platform);
        });
        platformSelect('Woocommerce');

        $('#IntegrationBackend').select2({
            minimumResultsForSearch: -1,
            placeholder: "Select Location"
        });

        $('#IntegrationMarketplaceId').change(function(){
            var country = $( "#IntegrationMarketplaceId option:selected" ).text();
            $('#IntegrationUrl').val(amazon_urls[country]);
        });

        $('#IntegrationMarketplaceId').select2({
            minimumResultsForSearch: -1,
            placeholder: "Select Location"
        });

        $('#IntegrationSchannelId').select2({
            minimumResultsForSearch: -1,
            placeholder: "Select Location"
        });
    });

    function platformSelect(platform) {
        $('.Amazon').hide();
        $('.Woocommerce').hide();
        $('.Shopify').hide();
        $('.'+platform).show();
        if(platform == 'Amazon') {
            $('#IntegrationUrl').val('').attr('readonly', true);
        } else {
            $('#IntegrationUrl').attr('readonly', false);
        }
    }

<?php $this->Html->scriptEnd(); ?>
</script>