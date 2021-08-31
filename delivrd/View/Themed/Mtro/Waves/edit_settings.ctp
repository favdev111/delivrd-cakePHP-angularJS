<?php echo $this->Form->create('User', array('url' => array('controller' => 'waves', 'action' => 'saveAddress'), 'class' => 'form-horizontal', 'id' => 'add-edit-form')); ?>
	 <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group batch-pick">
	                <div class="col-md-4">
	               	<label class="control-label"> Batch Pick: </label>
	                </div>
                    <div class="col-md-8">
                    	<?php echo $this->Form->input('User.batch_pick',array('label' => false, 'id' => 'scan', 'class' => 'form-control input-large', 'placeholder' => '', 'empty' => 'Select', 'options' => $batch)); ?>
					</div>
                </div>
                <div class="form-group pick-by-order">
	                <div class="col-md-4">
	               	<label class="control-label"> Pick By Order: </label>
	                </div>
                    <div class="col-md-8">
                    	<?php echo $this->Form->input('User.pick_by_order',array('label' => false, 'id' => 'scan', 'class' => 'form-control input-large', 'placeholder' => '', 'empty' => 'Select', 'options' => $pickbyorder)); ?>
					</div>
                </div>
				
            </div>

  <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary saveBtn" id="submit">Save Settings</button>
  </div> 
<?php echo $this->Form->end(); ?>
<script>
        $(function(){
        	
        	if($("#myModalLabel").text() == 'Set Batch Pick preference') {
        		$('.pick-by-order').remove();
        	} else {
        		$('.batch-pick').remove();
        	}
           $("#country_id").change(function()
		    {
		        $('#state_id').html('');
		        var country_id = $("#country_id").val();
		        country_select(country_id);
		        $.ajax({
		        method: 'POST',
		        url: siteUrl + "states/stateList",
		        data: {'country_id' : country_id},
		        datatype:'json',
		        }).success(function (data) {

		         var json = $.parseJSON(data);
		         for(var i = 0;i < json.states.length;i++)
		         {
		            $('#state_id').append('<option value="' + json.states[i].State.id + '" selected>' + json.states[i].State.name + '</option>');
		         }
		             
		        });
		            
			});

			function country_select(country_id) {
			  if(country_id == 1)
			  {
			  	$("#stateprovince-div").hide();
			  	$("#state_id-div").show();
			  }
			  else{
			  	$("#stateprovince-div").show();
			  	$("#state_id-div").hide();
			  }
			}

			country_select($("#country_id").val());                        
        });
</script>