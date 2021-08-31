<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>

                <?php echo $this->Form->create('Subscription', array('class' => 'form-horizontal form-row-seperated')); ?>
                    <div class="portlet box blue-steel">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-paypal"></i> Add PayPal Subscription
                            </div>
                            <div class="actions btn-set">
                                <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                                <button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
                            </div>
                        </div>
                            
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    
                                    <div class="row static-info">
                                        <div class="col-md-5 name">
                                            <label class="control-label">Subscription ID: <span class="required">*</span></label>
                                        </div>
                                        <div class="col-md-7 value">
                                            <?php  echo $this->Form->input('ext_id',array('type'=>'text', 'label' => false, 'class' => 'form-control', 'required')); ?>
                                        </div>
                                    </div>

                                    <div class="row static-info">
                                        <div class="col-md-5 name">
                                            <label class="control-label">Payer Email: <span class="required">*</span></label>
                                        </div>
                                        <div class="col-md-7 value">
                                            <?php echo $this->Form->input('payer_email',array('type' => 'email', 'label' => false, 'class' => 'form-control', 'required')); ?>
                                        </div>
                                    </div>

                                    <div class="row static-info">
                                        <div class="col-md-5 name">
                                            <label class="control-label">Delivrd User: <span class="required">*</span></label>
                                        </div>
                                        <div class="col-md-7 value">
                                            <?php echo $this->Form->input('user_id',array('type' => 'text', 'div'=>'false', 'label' => false, 'class' => 'form-control', 'id' => 'user_id', 'placeholder' => 'Select...', 'required' )); ?>
                                        </div>
                                    </div>

                                    <div class="row static-info">
                                        <div class="col-md-5 name">
                                            <label class="control-label">Paid Till: <span class="required">*</span></label>
                                        </div>
                                        <div class="col-md-7 value">
                                            <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd" id="orderdate" data-date-start-date="-1d" data-date="<?php echo date('Y-m-d'); ?>">
                                                <?php echo $this->Form->input('expiry_date',array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'required')); ?>
                                                <span class="input-group-btn">
                                                    <button class="btn btn-medium default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
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
<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    $('#user_id').select2({
        ajax: {
            delay: 250,
            url: '<?php echo $this->Html->url(['controller'=>'subscriptions', 'action' => 'userlist']); ?>',
            dataType: 'json',
            data: function(params) {
                var queryParameters = {
                    search: params,
                    type: 'public'
                }
                return queryParameters;
            },
            results: function (data, page) {
                return data;
            }
        }
    }).on('select2-selecting', function (e) {
        
    });
<?php $this->Html->scriptEnd(); ?>
</script>