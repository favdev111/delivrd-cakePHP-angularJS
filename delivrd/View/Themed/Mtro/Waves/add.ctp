<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>

<!-- BEGIN PAGE CONTENT-->
        <?php echo $this->Session->flash(); ?>
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Form->create('Wave', array('class' => 'form-horizontal form-row-seperated')); ?>
                <div class="portlet box red-thunderbird">
                    <div class="portlet-title">
                        <div class="caption"><i class="fa fa-play"></i><?php echo $title; ?></div>
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

                            <div class="col-md-6 col-sm-12">
                                <div class="portlet box grey-gallery">
                                    <div class="portlet-title">
                                        <div class="caption"><i class="fa fa-cogs"></i>Wave Creation Options</div>  
                                    </div>

                                    <div class="portlet-body">
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Maximum No. Of lines:
                                            </div>
                                            <div class="col-md-7 value">
                                                 <?php  echo $this->Form->input('maxlines',array( 'label' => false, 'class' => 'form-control input-sm', 'autofocus' => true, 'type' => 'number', 'min' => '0', 'step' => '1' )); ?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                <label class="control-label">Sales Channel: </label>
                                            </div>
                                            <div class="col-md-7 value">
                                                 <?php  
                                                 $disable = (!empty($id)) ? 'disabled' : '';
                                                 echo $this->Form->input('schannel_id',array( 'options' => $schannels,'label' => false, 'class' => 'form-control input-sm select2me','empty' => 'Select...', $disable )); ?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                  <label class="control-label">Resources: </label>
                                            </div>
                                            <div class="col-md-7 value">
                                                 <?php  echo $this->Form->input('resource_id',array( 'label' => false, 'class' => 'form-control input-sm select2me','empty' => 'Select...' )); ?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                              <label class="control-label">Type: 
                                                <span class="required">* </span>
                                              </label>
                                            </div>
                                            <div class="col-md-7 value">
                                                 <?php  echo $this->Form->input('type',array('options' => $type, 'label' => false, 'class' => 'form-control input-sm select2me','empty' => 'Select...', 'required' )); ?>
                                            </div>
                                        </div>
                                    </div>  
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="portlet box grey-gallery">
                                    <div class="portlet-title">
                                        <div class="caption"><i class="fa fa-cogs"></i>Set Wave Data</div>
                                    </div>

                                    <div class="portlet-body">
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                <label class="control-label">Courier: </label>
                                            </div>
                                            <div class="col-md-7 value">
                                                <span class="courierDropDown">
                                                    <?php echo $this->Form->input('courier_id',array( 'label' => false, 'class' => 'form-control input-sm select2me', 'empty' => 'Select...')); ?>
                                                </span>
                                                <a href="#" data-toggle="modal" data-target="#courierForm" class="btn btn-sm blue-steel">Create Courier</a>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                  <label class="control-label">Location: </label>
                                            </div>
                                            <div class="col-md-7 value">
                                                 <?php  echo $this->Form->input('location_id',array( 'label' => false, 'class' => 'form-control input-sm select2me','empty' => 'Select...' )); ?>
                                            </div>
                                        </div> 
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                <label class="control-label">Country:</label>
                                            </div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('Country', array('label' => false, 'placeholder' => 'Select Country', 'class' => 'form-control input-sm multiple', 'options' => $countries,'multiple','empty' => 'Select...')); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
                </form>
        </div>
    </div>
<!-- END PAGE CONTENT-->
</div>
</div>
<!-- END CONTENT -->

<div class="modal fade" id="courierForm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo __('Add New Courier'); ?></h4>
            </div>

            <?php echo $this->Form->create('Courier', array('url' => array('controller' => 'couriers', 'action' => 'create'), 'class' => 'form-horizontal', 'id' => 'createcourierForm')); ?>
            <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Name'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('name', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn">Save Courier</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>
