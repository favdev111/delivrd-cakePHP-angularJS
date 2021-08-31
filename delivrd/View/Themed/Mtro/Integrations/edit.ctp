<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>

        <!-- BEGIN PAGE CONTENT-->
        <?php echo $this->Session->flash(); ?>
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Form->create('Integration', array('class' => 'form-horizontal form-row-seperated')); ?>
                <?php echo $this->Form->input('id',array('hidden' => true)); ?>
                <div class="portlet box yellow-saffron">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-exchange"></i>
                            Edit Integration
                        </div>
                        <div class="actions btn-set">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                            <script>
                                function goBack() {
                                    window.history.back()
                                }
                            </script>   
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
                                                 <?php echo $this->Form->input('backend',array('label' => false, 'class' => 'form-control input-medium','readonly' => true)); ?>
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
                                                <?php echo $this->Form->input('url', array('label' => false, 'class' => 'form-control input-extra-large')); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <label class="col-md-3 control-label">Sales Channel: <span class="required">*</span></label>
                                            <div class="col-md-7 value">
                                             <?php echo $this->Form->input('schannel_id',array('label' => false, 'class' => 'form-control input-medium select2me','empty' => 'Select...','required' => true));?>
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
    </div>
    <!-- END PAGE CONTENT-->
</div>
<!-- END CONTENT -->

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var amazon_urls = <?php echo json_encode($endpoints); ?>;

    $(function() {
        var platform = $('#IntegrationBackend').val();
        platformSelect(platform);

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
            $('#IntegrationUrl').attr('readonly', true);
        } else {
            $('#IntegrationUrl').attr('readonly', false);
        }
    }

<?php $this->Html->scriptEnd(); ?>
</script>