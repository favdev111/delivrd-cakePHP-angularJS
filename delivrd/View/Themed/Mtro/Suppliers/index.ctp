	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper" ng-controller="SupplierList">
		<div class="page-content">
			<!-- BEGIN PAGE HEADER-->
			<?php echo $this->element('expirytext'); ?>
			<div class="page-bar">
				<?php echo $this->Form->create('Supplier', array(
				    'class' => 'form-horizontal',
				    'novalidate' => true,
				    'url' => ['controller' => 'suppliers', 'action' => 'index'],
				    'id' => 'user_add_form',
				)); 
				$search = (!empty($this->request->data['Supplier']['search'])) ? $this->request->data['Supplier']['search'] : '';?> 
				<div class="row">
					<div class="col-md-5">
							 <?php 
							echo $this->Form->input('search', array('label' => false, 'class'=>'form-control', 'placeholder' => 'Search by supplier name', 'value' => $search,  'style' => 'width: 370px;height: 32px;')); ?> 
					</div>
					<div class="col-md-2">
						<button class="btn btn-md blue filter-submit margin-bottom" type="submit" id="clicksearch"><i class="fa fa-search"></i></button>
						<?php echo $this->html->link('<i class="fa fa-undo"></i>', array('plugin' => false, 'controller' => 'suppliers', 'action' => 'index'), array('class' => 'btn btn-md blue filter-submit margin-bottom', 'escape' => false)); ?>
					</div>
					<div class="col-md-3">
					</div>
					<div class="col-md-1">
						<a href="<?php echo Router::url(array('controller' => 'suppliers', 'action' => 'edit'), true); ?>" class="btn default yellow-stripe" data-remote="false" data-toggle="modal" data-label="Add Supplier" data-target="#delivrd-modal" title="New Supplier"><i class="fa fa-plus"></i></a>
					</div>
					<div class="col-md-1">
						<div class="page-toolbar" id="actiondd">
							<div class="btn-group pull-right">
								<button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
								<i class="fa fa-ellipsis-h"></i>
								</button>
								<ul class="dropdown-menu pull-right" role="menu">
		                            <li><?php echo $this->Html->link(__('<i class="fa fa-link"></i> Products - Supplier Assignment'), array('controller'=> 'productsuppliers','action' => 'index'),array('escape'=> false)); ?></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<?php  echo $this->Form->end(); ?>	
				
			</div>
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Session->flash(); ?>
					<!-- Begin: life time stats -->
					<div class="portlet box delivrd">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-exchange"></i>Suppliers
							</div>
						</div>
						<div class="portlet-body">
						<div class="csv-div">
							<?php echo $this->Html->link(__('<i class="fa fa-download"></i> Export'), array('controller'=> 'suppliers','action' => 'exportcsv'),array('escape'=> false, 'class' => 'csv-icons')); ?>
						</div>
							<div class="table-container">
								<div class="table-actions-wrapper">
									<span>
									</span>
									
									<button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
								</div>
								<table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
								<thead>
								<tr role="row" class="heading">
									
									<th width="10%">
										 Name
									</th>
									
									<th width="15%">
										 Email
									</th>
									<th width="15%">
										 URL
									</th>
									<th width="15%">
										 Actions
									</th>
									
								</tr>
								</thead>
								<tbody>
								<?php foreach ($suppliers as $supplier): ?>
								<tr>
								
								<td>
																		
								<?php echo h($supplier['Supplier']['name']); ?>
								</td>
								
								<td>
																		
								<?php echo h($supplier['Supplier']['email']); ?>
								</td>
								<td>
																		
								<?php echo h($supplier['Supplier']['url']); ?>
								</td>
								
								
								<td>
								<div class="btn-group">
									<a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
									<i class="fa fa-ellipsis-h"></i>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
										<a href="<?php echo Router::url(array('controller' => 'suppliers', 'action' => 'edit', $supplier['Supplier']['id']), true); ?>" class="edit-form" data-remote="false" data-toggle="modal" data-label="Edit Supplier" id="<?php echo $supplier['Supplier']['id']; ?>" data-target="#delivrd-modal"><i class='fa fa-edit'></i> Edit</a>
										</li>
										<li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $supplier['Supplier']['id']), array('escape'=> false), __('Are you sure you want to delete supplier %s?', h($supplier['Supplier']['name']))); ?></li>
										<li>
										<a href="<?php echo Router::url(array('controller' => 'suppliers', 'action' => 'editAddress', $supplier['Supplier']['id']), true); ?>" class="edit-form" data-remote="false" data-toggle="modal" data-label="Edit Address" id="<?php echo $supplier['Supplier']['id']; ?>" data-target="#delivrd-modal"><i class='fa fa-globe'></i> Address</a>
										</li>
										<li>
                                            <a href ng-click="addDocument(<?php echo $supplier['Supplier']['id']; ?>, '<?php echo h($supplier['Supplier']['name']); ?>')"><i class="fa fa-upload"></i> Documents</a>
                                        </li>						
									</ul>
								</div>
								</td>
								</tr>
								<?php endforeach; ?>
								</tbody>
								</table>
								<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div>
	<ul class="pagination">
	<?php
	$paginator = $this->Paginator;
	echo $paginator->first("First",array('tag' => 'li'));
         
        // 'prev' page button, 
        // we can check using the paginator hasPrev() method if there's a previous page
        // save with the 'next' page button
        if($paginator->hasPrev()){
            echo $paginator->prev("Prev", array('tag' => 'li'));
        }
         
        // the 'number' page buttons
        echo $paginator->numbers(array('modulus' => 2,'tag' => 'li','currentTag' => 'a','currentClass' => 'active','separator' => false));
         
        // for the 'next' button
        if($paginator->hasNext()){
            echo $paginator->next("Next",array('tag' => 'li'));
        }
         
        // the 'last' page button
        echo $paginator->last("Last",array('tag' => 'li'));
		// echo $this->Paginator->prev(__('<'), array('tag' => 'li'),null,array('class' => 'prev disabled'));
		// echo $this->Paginator->first('< first');
		// echo $this->Paginator->numbers(array('tag' => 'li'));
		// echo $this->Paginator->next(__('>'), array('tag' => 'li'));
		// echo $this->Paginator->next('>', array('separator' => '<li>'), null, array('class' => 'next disabled'));
	?>
	</ul>
							</div>
						</div>
					</div>
					<!-- End: life time stats -->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->

<div class="modal fade" id="delivrd-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="form-body">
      
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="delivrd-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="form-body">
      
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
	$(function() {
		var regex4 = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;// Email address
		$('#multiple_email').tagsInput({
			width: 'auto',
			'height' :'28px',
			pattern: regex4,
			'defaultText':'Enter an email address',
		});

	});

	var doc_title = 'Supplier:';
<?php $this->Html->scriptEnd(); ?>
</script>

<?php echo $this->Html->script('/app/Suppliers/index.js?v=0.0.2', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/app/Documents/view.js?v=0.0.2', array('block' => 'pageBlock')); ?>