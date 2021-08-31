<?php echo $this->Form->create('Schannel', array('url' => array('controller' => 'schannels', 'action' => 'add'), 'class' => 'form-horizontal', 'id' => 'add-edit-form')); ?>
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
                <label class="control-label col-md-3">URL:</label>
                <div class="col-md-8">
                <?php  echo $this->Form->input('url',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
                </div>
         </div>
  </div> 
  <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary saveBtn" id="submit">Save</button>
  </div> 
<?php echo $this->Form->end(); ?>
