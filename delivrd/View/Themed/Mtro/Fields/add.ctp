<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="AddField">
    <div class="page-content">

        <?php echo $this->element('expirytext'); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>

                <?php echo $this->Form->create('Field', array('class' => 'form-horizontal form-row-seperated')); ?>
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i> Custom Product Fields
                        </div>

                        <div class="actions">
                            <?php echo $this->Html->link(__('<i class="fa fa-angle-left"></i> Back'), array('controller'=> 'fields','action' => 'index'),array('escape'=> false, 'class' => 'btn default yellow-stripe')); ?>
                            <button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Name: <span class="required">*</span></label>
                                    <div class="col-md-8">
                                        <?php echo $this->Form->input('Field.name', array('class' => 'form-control', 'required' => true, 'div' => false, 'label' => false )); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Description: </label>
                                    <div class="col-md-8">
                                        <?php echo $this->Form->textarea('Field.description', array('class' => 'form-control', 'required' => false, 'div' => false, 'label' => false )); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Options: </label>
                                    <div class="col-md-8">
                                        <div ng-repeat="option in field_options" class="clearfix" style="margin-bottom: 5px;">
                                            <div class="col-md-8" style="padding-left: 0px;">
                                                <input type="text" name="data[FieldsValue][{{option.FieldsValue.id}}][value]" class="form-control"  value="{{option.FieldsValue.value}}"/>
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn btn-xs btn-danger" style="margin-top:5px;" type="button" ng-click="removeOption(option.FieldsValue.id)">x</button>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="col-md-8" style="padding-left: 0px;">
                                                <?php echo $this->Form->input('new_option', array('ng-model'=>'new_option', 'placeholder' => 'Add option', 'class' => 'form-control', 'label' => false, 'div' => false)); ?>
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn btn-xs btn-info" style="margin-top:5px;" type="button" ng-click="addOption()">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-4"></div>
                                    <label class="col-md-8"><?php echo $this->Form->input('Field.is_filter', array('class' => 'form-control', 'type' => 'checkbox', 'value' => 1, 'div' => false, 'label' => false )); ?> Filter by custom field</label>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary">Save</button>
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

<?php echo $this->Html->script('/app/Fields/add.js', array('block' => 'pageBlock')); ?>

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var field_options = <?php echo json_encode(array()); ?>;

<?php $this->Html->scriptEnd(); ?>
</script>