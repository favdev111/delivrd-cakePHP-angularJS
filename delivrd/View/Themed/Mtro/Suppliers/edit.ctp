<?php echo $this->Form->create('Supplier', array('url' => array('controller' => 'suppliers', 'action' => 'add'), 'class' => 'form-horizontal', 'id' => 'add-edit-form')); ?>
   <div class="modal-body"> 
         <div class="form-group">
                <label class="control-label col-md-3">Name: <span class="required"> * </span></label>
                <div class="col-md-8">
                <?php  
                echo $this->Form->input('id',array('hidden' => true));
                echo $this->Form->input('name',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
                </div>
         </div>
         <div class="form-group">
                <label class="control-label col-md-3">Email:</label>
                <div class="col-md-8 value">
                <?php  echo $this->Form->input('email',array('type' => 'text','label' => false, 'class' => 'form-control input-sm tags', 'data-role' => 'tagsinput', 'id' => 'multiple_email','placeholder' => 'add an email address')); ?>
                </div>
         </div>
        <div class="form-group">
                <label class="control-label col-md-3">URL: </label>
                <div class="col-md-8">
                <?php   echo $this->Form->input('url',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
                </div>
        </div>
        <div class="form-group">
                <label class="control-label col-md-3">Vendor is customer:</label>
                <div class="col-md-8">
                <?php echo $this->Form->input('is_customer', array('label' => false, 'class' => 'md-check', 'div' => false)); ?>
                </div>
        </div>
  </div> 
  <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary saveBtn" id="submit">Save</button>
  </div> 
<?php echo $this->Form->end(); ?>


