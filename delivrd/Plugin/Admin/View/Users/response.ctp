<div class="page-content-wrapper">
	<div class="page-content" style="min-height:333px">
		
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
				<!-- Begin: life time stats -->
				<div class="portlet box blue-steel">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-barcode"></i>Import Products
						</div>
					</div>
					<?php if(!empty($result)) { ?>
						<div class="portlet-body">
							<table class="table">
								<thead>
									<tr>
										<th>Product Name</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach($result as $getResult) : ?>
									<tr>
										<td><?= $getResult['name']; ?></td>
										<th><?= $getResult['error']; ?></th>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php } ?>

					<div class="portlet-body" style="width: 100%;display: none;" id="updateproductdiv">
						<div class="text-center"><b>These products are already in your Inventory list. Please click the checkboxes next to the products if you want to update them</b></div>
						<form action="/admin/users/update_products/<?php echo $user['User']['id']; ?>" method="POST" id="update_products" name="update_products">
						<table class="table table-striped" id="tblGrid">
							<thead id="tblHead">
								<tr>
									<th><input type="Checkbox" id="checkAll"></th>
									<th>Product Name</th>
									<th>Sku</th>
									<th>status</th>
								</tr>
								<tbody  id="errorDiv">
								<tbody>
							</thead>
							<tbody id="validationDiv">
							</tbody>
						</table>
						<input type="submit" name="updatea_products" value="Update Products" id="UpdateProductBtn" class="btn btn-success pull-right"/>
						</form>
					</div>
					<div class="portlet-body admin_pro_list" style="width: 100%;display: inline-block;" id="responseDiv">
						<!-- <div class="progress">
							<div class="progress-bar" id="progress" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:0%">
							  <span class="sr-only">70% Complete</span>
							</div>
						</div> -->
					</div>
				</div>
			</div>
		<!-- END PAGE CONTENT-->
		</div>
	</div>
</div>


<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	       		<h4 class="modal-title">Import Products</h4>
	      	</div>
	      	<div class="modal-body">
				<p class="lead text-success"><span id="newCount"></span> product(s) have been created</p>
				<h3>It's time to:</h3>
				<p class="text-info" style="font-size:15px;margin-left:20px">
					<i class="fa fa-chevron-right"></i> <?php echo $this->Html->link('Manage Inventory of new products', array('controller'=>'inventories', 'action'=>'index')); ?><br>
					<?php if($_authUser['User']['role'] == 'trial' || $_authUser['User']['role'] == 'paid') { ?>
						<i class="fa fa-chevron-right"></i> <?php echo $this->Html->link('Sell products with sales orders', array('controller'=>'orders', 1)); ?><br>
						<i class="fa fa-chevron-right"></i> <?php echo $this->Html->link('Get more products from your supplier with purchase orders', array('controller'=>'orders', 2)); ?>
					<?php } else { ?>
						<i class="fa fa-chevron-right"></i> <?php echo $this->Html->link('Sell products with sales orders', array('controller'=>'orders', 1), array('id' => 'soAdvrt')); ?><br>
						<i class="fa fa-chevron-right"></i> <?php echo $this->Html->link('Get more products from your supplier with purchase orders', array('controller'=>'orders', 2), array('id' => 'poAdvrt')); ?>
					<?php } ?>
				</p>
			</div>
		</div>
	</div>
</div>

<style>
.admin_pro_list table {
    width: 50%;
    float: left;
}
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
</style>
<?php
	if(!isset($size))
	$size = array();
	$progressbar = 0;
	$progress = 100/count($products);
	$num = 1;
	$group = array_map('utf8_encode', $group);
?>
<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
	var reload = true;
	var category  = [];
	var category_id,group_id,successcount =0;
	category = <?php echo json_encode($category)?>;
	products = <?php echo json_encode($products);?>;
	group = <?php echo json_encode($group);?>;
	size = <?php echo json_encode($size);?>;
	colours = <?php echo json_encode($colours);?>;
	
	$.each(products,function(k , product) {
		productParam = [];
		var formData = {};
		$.each(product,function( key , p){
			formData[key] = p;
		});
		
		//ajax call to add product
		var url;
		url = '/admin/users/add_csv_products/<?php echo $user['User']['id']; ?>';
		$.ajax({
			type: 'POST',
			url: url,
			data: formData,
			async:false,
			success: function(response){
				var arrVal = Object.keys(formData).map(function (key) { return formData[key]; });
				var arrKey = Object.keys(formData);
				
				console.log('add_csv_products', arrKey)
				response = JSON.parse(response);
				if(response.success == 1){
					successcount = successcount + 1;
					$('#responseDiv').append(' => <span class="text-success"><i class="fa fa-check"></i> '+ response.message +'</span><br>');
				} else if(response.success == 0 ){
					$('#responseDiv').append('=> <span class="text-danger"><i class="fa fa-exclamation-circle"></i> '+ response.message+'. ' +response.status +'.</span><br>');
				}else{
					$('#errorDiv').append('<tr><td>'+
					'<input type="Checkbox" class="updproduct" name="updproduct[]">'+
					'</td>'+ response.html+
					'</tr><input type=hidden name=product_id[] value='+response.product_id+'>'+
					'<input type="hidden" name="product_details[]" value="'+ arrVal +'">'+
					'<input type="hidden" name="product_keys[]" value="'+ arrKey +'">');
					reload = false;
					$('#updateproductdiv').css('display','inline-block');
				}
			}
		});
	});

	$('#newCount').html(successcount);
	$('#successModal').modal('show');

	/* to check all checkbox by clicking on checkbox */
	$(document).on('click','#checkAll',function(){
		$('.updproduct').prop('checked', this.checked);
	});

	/* to update transfer table */
	var url,formdata,transfer_id;
	url='/transfer/update_transfer';
	transfer_id = $('#transfer_id').val();
	formdata = 'id=<?php echo $transfer_id; ?>&status=3&recordscount='+successcount;

	$.post(url,formdata,function(response){
		if(reload == true){
			//setTimeout(function(){ window.location = '../products'; }, 2000);
		}
	});

	$(document).on('click','#UpdateProductBtn',function(){
		var checkbox = $('.updproduct').prop('unchecked',false).length;
	});

	/* to update products that are already exists in database */
	$(document).on('submit','#update_products',function(e){
		e.preventDefault();
		var url,formData;
		url = $(this).attr('action');
		formData = $(this).serialize();
		console.log('formData', formData)
		$.ajax({
			type: 'POST',
			url: url,
			data: formData,
			async:false,
			success: function(data){
				console.log('submit', data)
				window.location = '/admin/users';
			}
		});
		return false;
	});
	
	function ucwords(str) {
		var text = str;
		var parts = text.split(' '),
			len = parts.length,
			i, words = [];
		for (i = 0; i < len; i++) {
			var part = parts[i];
			var first = part[0].toUpperCase();
			var rest = part.substring(1, part.length);
			var word = first + rest;
			words.push(word);

		}
		return words.join(' ');
	}

	$(document).on('click','#soAdvrt',function(){
		$('#startTrialHidden').trigger('click');
		return false;
	});

	$(document).on('click','#poAdvrt',function(){
		$('#startTrialHidden').trigger('click');
		return false;
	});

<?php $this->Html->scriptEnd(); ?>
</script>