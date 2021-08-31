<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        
        <?php echo $this->element('expirytext'); ?>

        <?php echo $this->Session->flash(); ?>

        <!-- BEGIN PAGE CONTENT-->
        <?php echo $this->Form->create('User',array( 'type' => 'file')); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>Update your address
                        </div>
                        <div class="actions">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                            <button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>

                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="portlet yellow-crusta box">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i>Your Details
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="row static-info">
                                            <div class="col-md-5 name">First Name:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.firstname',array('label' => false, 'class' => 'form-control input-medium')); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">Last Name:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.lastname',array('label' => false, 'class' => 'form-control input-medium')); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">Company Name:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.company',array('label' => false, 'class' => 'form-control input-medium')); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">Business Type:</div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.btype_id',array('label' => false, 'empty' => 'Select...', 'class' => 'form-control input-medium')); ?>
                                            </div>
                                        </div>

                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                <label class="control-label">Username: <span class="required">*</span></label>
                                            </div>
                                            <div class="col-md-7 value">
                                                <?php echo $this->Form->input('User.username',array('label' => false, 'class' => 'form-control input-medium')); ?>
                                            </div>
                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                             <label class="control-label">Street Address: 
                                                               <span class="required">* </span>
                                                             </label>
                                                            </div>
                                                            <div class="col-md-7 value">
                                                            <?php echo $this->Form->hidden('Address.id',array()); ?>
                                                                 <?php echo $this->Form->input('Address.street',array('label' => false, 'class' => 'form-control input-medium')); ?>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                             <label class="control-label">City: 
                                                               <span class="required">* </span>
                                                            </label>
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                 <?php echo $this->Form->input('Address.city',array('label' => false, 'class' => 'form-control input-medium')); ?>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                 Zip:
                                                            </div>
                                                            <div class="col-md-7 value">                                                            
                                                                <?php echo $this->Form->input('Address.zip',array('label' => false, 'class' => 'form-control input-medium')); ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                            <label class="control-label">Country: 
                                                              <span class="required">* </span>
                                                            </label>
                                                            </div>
                                                            <div class="col-md-7 value">                                                            
                                                                <?php echo $this->Form->input('Address.country_id',array('id' => 'country_id','label' => false, 'class' => 'form-control input-medium select2me', 'empty' => 'Select')); ?>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info" id="state_id-div">
                                                            <div class="col-md-5 name">
                                                                 State (US Only):
                                                            </div>
                                                            <div class="col-md-7 value">                                                            
                                                                <?php echo $this->Form->input('Address.state_id',array('id' => 'state_id','label' => false, 'class' => 'form-control input-medium select2me', 'empty' => 'Select', 'options' => $states)); ?>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info" id="stateprovince-div">
                                                            <div class="col-md-5 name">
                                                                 State or Province:
                                                            </div>
                                                            <div class="col-md-7 value">                                                            
                                                                <?php echo $this->Form->input('Address.stateprovince',array('label' => false, 'id' => 'stateprovince', 'class' => 'form-control input-medium', 'placeholder' => '')); ?>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                 Logo:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                 <div class="fileinput fileinput-new" data-provides="fileinput">
                                                                    <div class="fileinput-new thumbnail">
                                                                        <?php 
                                                                            if (!empty($this->request->data[$modelClass]['logo']) && !empty($this->request->data[$modelClass]['id'])) {
                                                                                echo $this->Image->img('user/logo/' . $this->request->data[$modelClass]['id'], $this->request->data[$modelClass]['logo'], '', array(), 'no-image.gif');
                                                                                echo $this->Form->hidden('old_image', array('value' => $this->request->data[$modelClass]['logo']));
                                                                            } elseif(!empty($this->request->data[$modelClass]['logo_url'])) {
                                                                                echo $this->Image->img('',$this->request->data[$modelClass]['logo_url'], '', array(), 'no-image.gif');
                                                                            } else {
                                                                                echo $this->Image->img('user/logo/', 200, 200, array(), 'no-image.gif');
                                                                            }
                                                                        ?>
                                                                    </div>

                                                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 200px;">
                                                                    </div>
                                                                    <div>
                                                                        <span class="btn default green btn-file">
                                                                            <span class="fileinput-new">
                                                                                Select image </span>
                                                                            <span class="fileinput-exists">
                                                                                Change </span>
                                                                                <?php echo $this->Form->file('User.logo', array('type' => 'file', 'class' => 'm-wrap large', 'label' => false)); ?>
                                                                        </span>
                                                                        <a href="#" class="btn red fileinput-exists" data-dismiss="fileinput">
                                                                            Remove </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <span style="font-size: 20px;margin: 0px 0px 0px 349px;"> OR</span>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                 Enter Your Logo url:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                 <?php echo $this->Form->input('User.logo_url',array('label' => false, 'class' => 'form-control input-medium')); ?>
                                                            </div>
                                                        </div>
                                                        
                        
                                                        
                                <?php echo $this->Form->end(__d('users', '')); ?>       
                            </div>
                    </div>
                    
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
        </div>
    </div>
</div>
</div>
</div>
    <!-- END CONTENT -->

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
$(function() {
    $('#UserBtypeId').select2({
        minimumResultsForSearch: -1
    });
});
<?php $this->Html->scriptEnd(); ?>