<?php $this->AjaxValidation->active(); ?>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Session->flash(); ?>
					<!-- Begin: life time stats -->
					<div class="portlet box delivrd">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-delicious"></i>Colors
							</div>
							<div class="actions">
							<?php echo $this->Html->link(__('<i class="fa fa-plus"></i><span class="hidden-480">New Color </span>'), array('controller'=> 'colors','action' => 'add'),array('class' => 'btn default yellow-stripe','escape'=> false)); ?>
							
							<div class="btn-group">
									<a class="btn default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
									<i class="fa fa-share"></i> Tools <i class="fa fa-angle-down"></i>
									</a>
									<ul class="dropdown-menu pull-right">
									
									</ul>
								</div>
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
									
									<th width="10%">
										 Name
									</th>
									<th width="15%">
										 Description
									</th>
									<th width="15%">
										 Color Code
									</th>
									<th width="15%">
										 Color
									</th>
									<th width="15%">
										 Actions
									</th>
							
									
								
								</thead>
								<tbody>
								<?php foreach ($colors as $color): ?>
								<tr>
								
								<td>
																		
								<?php echo h($color['Color']['name']); ?>
								</td>
								<td>
																		
								<?php echo h($color['Color']['description']); ?>
								</td>
								<td>
																		
								<?php echo h($color['Color']['htmlcode']); ?>
								</td>
								<td bgcolor="<?php echo h($color['Color']['htmlcode']) ?>">
								 									
								</td>
								
								
								<td>
								<div class="btn-group">
									<a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
									<i class="fa fa-bolt"></i> Actions.. <i class="fa fa-angle-down"></i>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
										<?php echo $this->Html->link(__('<i class="fa fa-edit"></i>
										Edit'), array('controller'=> 'colors','action' => 'edit',$color['Color']['id']),array('escape'=> false)); ?>
										</li>
										<li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', h($color['Color']['id'])), array('escape'=> false), __('Are you sure you want to delete the color %s?', h($color['Color']['name']))); ?></li>
																	
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
