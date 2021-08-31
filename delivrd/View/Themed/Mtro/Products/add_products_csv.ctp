<div class="page-content-wrapper">
	<div class="page-content" style="min-height:333px">
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
			 	<!-- Begin: life time stats -->
				<div class="portlet box blue-steel">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-upload"></i> Import Products
						</div>
					</div>
					<div class="portlet-body" style="width: 100%;display: inline-block;">
						<div class="margin-bottom-20">
							<?php echo $this->Html->link('<i class="fa fa-cloud-download"></i> Download Sample File','/products/downloadsamplefile',array('class' => '','escape'=> false)); ?> |
							<a href="https://delivrd.freshdesk.com/support/solutions/articles/17000091134-importing-products-to-delivrd" target="_blank"><i class="fa fa-question-circle"></i> Product Import Tutorial</a>
						</div>
						
						<div id="validationErrors"></div>
						<div class="admin_pro_list" style="width: 100%;display: inline-block;">

						<?php echo (isset($html) ? $html : '') ?>
						<?php if(!$matchColumnDisplay){ ?>
							<?php echo $this->Form->create('fileupload', array('class'=>'form-inline', 'url' => 'add_products_csv','type' => 'file')); ?>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<span class="btn btn-sm green btn-file" style="margin-right:20px;">
											<i class="fa fa-plus"></i> <span>Choose file</span>
											<?php echo $this->Form->input('photo', ['type' => 'file', 'div' => false, 'label' => false]); ?>
										</span>
									</div>
									<div class="form-group">
										<?php echo $this->Form->submit(__('Upload file',true), array('class'=>'btn  btn-sm btn-info','value'=>'submit')); ?>
									</div>
									<div id="divFileName"></div>
								</div>
								<div class="col-md-2">
									
								</div>
							<?php echo $this->Form->end(); ?>
						<?php } else { ?>
							<p style="color:red">* Product name &amp; SKU fields must be mapped.</p>
							<form action="add_product" method="post" id="matchForm" name="matchForm">
								<input type="hidden" name="file_name" value="<?php echo $file; ?>">
								<input type="hidden" name="transfer_id" value="<?php echo $transfer_id; ?>">
								<table border="2">	
									<tr>
										<th>Upload file field</th>
										<th>Delivrd product field</th>
									</tr>
									<?php foreach($headerCols as $key => $headerCol) { ?>
										<tr>
											<td id="<?php echo $key; ?>" class="csv_fields">
												<?php echo $headerCol; ?>
												<input type="hidden" name="csvcol[]" value="<?php echo $headerCol; ?>" class="csvBox_'<?php echo $key; ?>">
											</td>
											<td>
												<select id="dropDown_<?php echo $key; ?>" name="dbcol[<?php echo $key; ?>]" class="dropDown" empty="Select">
													<option value="0" selected>Select</option>
													<?php foreach($fields as $field) { ?>
														<?php $mand = ($field['is_mandatory'] == 1) ? '*' : ''; ?>
														<option value="<?php echo $field['database_value']; ?>"><?php echo $field['display_value']; ?><?php echo h($mand); ?></option>
													<?php } ?>
												</select>
											</td>
										</tr>
									<?php } ?>
									<tr>
										<td colspan="2" style="text-align:center;">
											<button type="button" id="validate_data">Check Data</button>
										</td>
									</tr>
								</table>
							</form>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<!-- END PAGE CONTENT-->
		</div>
	</div>
</div>


<!-- to add create fields items that not exists in database -->	
<div class="modal fade" id="FieldsModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form action="create_new_fields" id="newfieldsform" method="Post" >
			
				<div class="modal-header">
					<div class="modal-title clsfield"><h3 class="text-center">New Fields that needs to be created</h3></div>
				</div>
				<div class="modal-body">
						<button type="submit" class="btn btn-info pull-right clsfield">Create New Fields</button>
						<div class="form-check clsfield">
						<label class="form-check-label">
							<input class="form-check-input" id="CheckNewFields" type="Checkbox">
									Check ALL
								</label>
							 <br>
						</div>
						<div class="form-check clsfield" id="NewCatDiv" style="display:none;">
						
						<hr>
						</div>
						<div class="form-check clsfield" id="NewColorDiv" style="display:none;">
						
						<hr>
						</div>
						<div class="form-check clsfield" id="NewSizeDiv" style="display:none;">
						
						</div>
					<div class="modal-title"><h3 class="text-center" id="catName"></h3></div>
					<div class="admin_pro_list" id="catFields">
					</div>
					<div class="modal-title"><h3 class="text-center" id="colorName"></h3></div>
					<div class="admin_pro_list" id="colorFields">
					</div>
					<div class="modal-title"><h3 class="text-center" id="sizeName"></h3></div>
					<div class="admin_pro_list" id="sizeFields">
					</div>	
					<div class="modal-title"><h3 class="text-center">Products Validation Status</h3></div>
					<div class="admin_pro_list" style="width: 100%;display: inline-block;" id="errorProducts">
					</div>
				</div>
					<div class="modal-footer">
						<a href="add_products_csv" class="btn" >Upload CSV file Again</a>
						<button type="button" id="uploadProducts" class="btn btn-success" >Create Products</button>
					</div>
			</form>
		</div>
	</div>
</div>
<!-- -->
<style>
.alert {
    /* padding: 3px;
	margin-bottom: 5px; */
}
.admin_pro_list table {
    width: 100%;
  /*   float: left; */
}
.admin_pro_list .portlet {margin-top:20px;}
.admin_pro_list p.lead {color:#fff; margin:0; padding:10px 20px;}
.admin_pro_list .portlet-body {overflow:auto; height:400px;}
.admin_pro_list table th, .admin_pro_list table td{    padding: 10px 10px;
    font-size: 16px;}
	.admin_pro_list table th {font-weight:bold;}
	.admin_pro_list table  .dropDown {
    padding: 4px 10px;
}
.admin_pro_list table input[type=submit] {
    background: #4B77BE;
    border: none;
    padding: 8px 20px;
    font-size: 16px;
    text-transform: uppercase;
    color: #fff;
    font-weight: bold;
}
.admin_pro_list table button{
    background: #4B77BE;
    border: none;
    padding: 8px 20px;
    font-size: 16px;
    text-transform: uppercase;
    color: #fff;
    font-weight: bold;
}
#validationErrors{
	background-color: #fff;
}
</style>
<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
  	$('#fileuploadPhoto').change(function() {
  		var fileName = $(this).val();
  		fileName = fileName.split('\\').pop();
  		$('#divFileName').html(fileName);
  	})

	/*$(".dropDown").click(function(){
	   	var selected_box = $(this).attr('id');
	   	$("#"+ selected_box).change(function() {
		  	var selected_val = $( "#" + selected_box + " option:selected" ).text();
		  	console.log(selected_val);
		  	var split_id = selected_box.split('_');
		  	console.log(split_id);
		  	console.log($("#" + split_id[1]).text());
		  	$(".csvBox_" + split_id[1]).val($("#" + split_id[1]).text());
	   	});
	});*/

	<?php if($show_trial) { ?>
	$(document).ready(function() {
		$('#startTrialHidden').trigger('click');
	});
	<?php } ?>

$('#fileuploadPhoto').siblings('label').css('display','none');
 $(document).on('click','#validate_data',function(){
	var formData =  $('#matchForm').serialize();
	var url = 'validate_csv';
	$.post(url,formData,function(response){	
		$('.clsfield').css('display','none');
		$('#FieldsModal').modal('show');
		var fieldsDiv = false;
		if(response.newfields.catarray.length){
			var fieldsDiv = true;
			$('#NewCatDiv').show();
			$('#NewCatDiv').html('');
			$.each(response.newfields.catarray, function(key , value){
				$('#NewCatDiv').append('<label class="form-check-label">'+
							'<input class="form-check-input" name="category_name[]" type="checkbox" value="'+value+'">'+value+
							' Category is not created do you want to create it </label><br>');
			});
		}
		if(response.newfields.colorarray.length){
			var fieldsDiv = true;
			$('#NewColorDiv').show();
			$('#NewColorDiv').html('');
			$.each(response.newfields.colorarray, function(key , value){
				$('#NewColorDiv').append('<label class="form-check-label">'+
							'<input class="form-check-input" name="colors_name[]" type="checkbox" value="'+value+'">'+value+
							' Color is not created do you want to create it</label><br>');
			});
		}
		if(response.newfields.sizearray.length){
			var fieldsDiv = true;
			$('#NewSizeDiv').show();
			$('#NewSizeDiv').html('');
			$.each(response.newfields.sizearray, function(key , value){
				$('#NewSizeDiv').append('<label class="form-check-label">'+
							'<input class="form-check-input" name="size[]" type="checkbox" value="'+value+'">'+value+
							' Size is not created do you want to create it</label><br>');
			});
		}
		$('#uploadProducts').html(response.btn_title);
		if(fieldsDiv == true)
			$('.clsfield').css('display','block');
		$('#errorProducts').html(response.products);
	},'json');
 });
 
 $(document).on('submit','#newfieldsform',function(e){
	 e.preventDefault();
	 $.post($(this).attr('action'),$(this).serialize(),function(response){
	 	var response1=jQuery.parseJSON(response);
	 	if(response1.catSuccess != true) {
	 		$('#catFields').html(response1.catView);
	 		$('#catName').html(response1.catTable);
	 	}
	 	if(response1.cSuccess != true) {
	 		$('#colorFields').html(response1.colorView);
	 		$('#colorName').html(response1.cTable);
	 	}
	 	if(response1.sSuccess != true) {
	 		$('#sizeFields').html(response1.sizeView);
	 		$('#sizeName').html(response1.sTable);
	 	}
	 	
		$('.clsfield').toggle('slow');
//		 $('#FieldsModal').modal('hide');
	 });
 });
 $(document).on('click','#uploadProducts',function(){
	//$('#exportButton').prop('disabled',false);
	$('#FieldsModal').modal('hide');
	$('#matchForm').submit();
});
 $(document).on('click','#CheckNewFields',function(){
	$('input:checkbox').not(this).prop('checked', this.checked);
});

$('select').change(function(){                               //to stop multiselect database fields
	// start by setting everything to enabled
    $('select option').attr('disabled',false);
    // loop each select and set the selected value to disabled in all other selects
    $('select').each(function(){
        var $this = $(this);
        $('select').not($this).find('option').each(function(){
           if($(this).attr('value') == $this.val() && $this.val() != '0')
               $(this).attr('disabled',true);
        });
    });
	
});
<?php $this->Html->scriptEnd(); ?>
</script>