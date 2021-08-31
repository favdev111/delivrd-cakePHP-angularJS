<?php $this->AjaxValidation->active(); ?>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE HEADER-->
			<?php echo $this->element('expirytext'); ?>
			<div class="page-bar">
				<?php echo $this->Form->create('Resource', array(
				    'class' => 'form-horizontal',
				    'novalidate' => true,
				    'url' => ['controller' => 'resources', 'action' => 'index'],
				    'id' => 'resource_form',
				)); 
				$search = (!empty($this->request->data['Resource']['name'])) ? $this->request->data['Resource']['name'] : '';?> 
				<div class="row">
					<div class="col-md-5">
							 <?php 
							echo $this->Form->input('name', array('label' => false, 'class'=>'form-control', 'placeholder' => 'Search by resource name', 'value' => $search,  'style' => 'width: 370px;height: 32px;')); ?> 
					</div>
					<div class="col-md-2">
						<button class="btn btn-md blue filter-submit margin-bottom" type="submit" id="clicksearch"><i class="fa fa-search"></i></button>
						<?php echo $this->html->link('<i class="fa fa-undo"></i>', array('plugin' => false, 'controller' => 'resources', 'action' => 'index'), array('class' => 'btn btn-md blue filter-submit margin-bottom', 'escape' => false)); ?>
					</div>
					<div class="col-md-4">
					</div>
					<div class="col-md-1">
					<a href="<?php echo Router::url(array('controller' => 'resources', 'action' => 'edit'), true); ?>" class="btn default yellow-stripe pull-right" data-remote="false" data-toggle="modal" data-label="Add Resource" data-target="#delivrd-modal" title="New Resource"><i class="fa fa-plus"></i></a>
					</div>
				</div>
				<?php echo $this->Form->end(); ?>
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
								<i class="fa fa-exchange"></i>Resources
							</div>
						</div>
						<div class="portlet-body">
							<div class="table-container">
								<div class="table-actions-wrapper">
									<span>
									</span>
									
									<button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
								</div>
								<table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
								<thead>
								<tr role="row" class="heading">
									
									<th width="20%">
										 Name
									</th>
									
									<th width="60%">
										 Description
									</th>
									
									<th width="15%">
										 Actions
									</th>
									
								</tr>
								</thead>
								<tbody>
								<?php if(count($data) !== 0) {
								foreach ($data as $listOne): ?>
								<tr>
									<td><?php echo h($listOne['Resource']['name']); ?></td>
									<td><?php echo h($listOne['Resource']['description']); ?></td>
									<td>
										<div class="btn-group">
											<a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
											<i class="fa fa-ellipsis-h"></i>
											</a>
											<ul class="dropdown-menu pull-right">
												<li>
												<a href="<?php echo Router::url(array('controller' => 'resources', 'action' => 'edit', $listOne['Resource']['id']), true); ?>" class="edit-form" data-remote="false" data-toggle="modal" data-label="Edit Resource" id="<?php echo $listOne['Resource']['id']; ?>" data-target="#delivrd-modal"><i class='fa fa-edit'></i> Edit</a>
												</li>
												<li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $listOne['Resource']['id']), array('escape'=> false), __('Are you sure you want to delete resource %s?', h($listOne['Resource']['name']))); ?></li>
																			
											</ul>
										</div>
									</td>
								</tr>
								<?php endforeach; 
								} else {
									echo "<tr><td align='center' colspan='8'><b>No Data Found</b></td></tr>";
								} ?>
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
