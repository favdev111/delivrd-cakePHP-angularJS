<?php echo $this->Form->create('Bin', array('url' => array('controller' => 'bins', 'action' => 'add'), 'class' => 'form-horizontal', 'id' => 'add-edit-form')); ?>
   <div class="modal-body"> 
         <div class="form-group">
                <label class="control-label col-md-3">Title: <span class="required"> * </span></label>
                <div class="col-md-8">
                <?php  
                echo $this->Form->input('id',array('hidden' => true));
                echo $this->Form->input('title',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
                </div>
         </div>
         <div class="form-group">
                <label class="control-label col-md-3">Sort Sequence:</label>
                <div class="col-md-8">
                <?php  echo $this->Form->input('sort_sequence',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
                </div>
         </div>
        <div class="form-group">
                <label class="control-label col-md-3">Location: <span class="required"> * </span></label>
                <div class="col-md-8">
                <?php  echo $this->Form->input('location_id',array( 'label' => false, 'class' => 'form-control input-sm select2me','empty' => 'Select...' )); ?>
                </div>
        </div>
        <div class="form-group">
                <label class="control-label col-md-3">Status:</label>
                <div class="col-md-8">
                <?php  echo $this->Form->input('status',array( 'label' => false, 'class' => 'form-control input-sm','empty' => 'Select...','options' => $status )); ?>
                </div>
        </div>
  </div> 
  <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary saveBtn" id="submit">Save</button>
  </div> 
<?php echo $this->Form->end(); ?>
		
						