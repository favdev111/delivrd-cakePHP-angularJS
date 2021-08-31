<?php echo $this->Form->create('Warehouse', array('url' => array('controller' => 'warehouses', 'action' => 'saveAddress'), 'class' => 'form-horizontal', 'id' => 'add-edit-form')); ?>
	 <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group">
	                <div class="col-md-3">
	                <?php echo $this->Form->hidden('Warehouse.id',array('id' => 'location-id','hidden' => true)); ?>
	                <?php echo $this->Form->hidden('Address.id',array('hidden' => true)); ?>
	                <label class="control-label">Street Address: 
	                	<span class="required">* </span>
	                </label>

	                </div>
                    <div class="col-md-8">
                    	<?php echo $this->Form->input('Address.street',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                    </div>
                </div>
                <div class="form-group">
	                <div class="col-md-3">
	                <label class="control-label">City: 
	                   <span class="required">* </span>
	                </label>
	                </div>
                    <div class="col-md-8">
                    	<?php echo $this->Form->input('Address.city',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                    </div>
                </div>
                <div class="form-group">
	                <div class="col-md-3">
	               	<label class="control-label">Zip: </label>
	                </div>
                    <div class="col-md-8">
                    	<?php echo $this->Form->input('Address.zip',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                    </div>
                </div>
                <div class="form-group">
	                <div class="col-md-3">
	                <label class="control-label">Country: 
 					  <span class="required">* </span>
	                </label>
	                </div>
                    <div class="col-md-8">
                    	<?php echo $this->Form->input('Address.country_id',array('id' => 'country_id', 'label' => false, 'class' => 'form-control input-large select2me', 'placeholder' => '','empty' => 'Select...')); ?>
                    </div>
                </div>
                <div class="row static-info" id="state_id-div">
					<div class="col-md-3 name">
						 State (US Only):
					</div>
					<div class="col-md-8 value">															
						<?php echo $this->Form->input('Address.state_id',array('id' => 'state_id','label' => false, 'class' => 'form-control input-large select2me', 'empty' => 'Select')); ?>
					</div>
				</div>
				<div class="row static-info" id="stateprovince-div">
					<div class="col-md-3 name">
						 State or Province:
					</div>
					<div class="col-md-8 value">															
						<?php echo $this->Form->input('Address.stateprovince',array('label' => false, 'id' => 'stateprovince', 'class' => 'form-control', 'placeholder' => '')); ?>
					</div>
				</div>
            </div>

  <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary saveBtn" id="submit">Save Location Address</button>
  </div> 
<?php echo $this->Form->end(); ?>
<script>
        $(function(){
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