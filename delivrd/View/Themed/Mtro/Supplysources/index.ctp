<?php $this->AjaxValidation->active(); ?>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
			
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Session->flash(); ?>
					<!-- Begin: life time stats -->
					<div class="portlet box delivrd">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-sliders"></i>Supply Sources
							</div>
							<div class="actions">
								
							<a href="/supplysources/add" class="btn default yellow-stripe">
								<i class="fa fa-plus"></i>
								<span class="hidden-480">
								New Supply Source </span>
								</a>
							<div class="btn-group">
									<a class="btn default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
									<i class="fa fa-share"></i> Tools <i class="fa fa-angle-down"></i>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
											<a href="/supplysources/exportcsv">
											Export Supply Sources to CSV </a>
										</li>
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
										 Type
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
								<tr role="row" class="filter">
									<?php echo $this->Form->create('Supplysource', array(
    'url' => array_merge(
            array(
                'action' => 'index'
            ),
            $this->params['pass']
        )
    )
); ?>
								
									<td>
										<?php	 echo $this->Form->input('name', array('label' => false,'class'=>'form-control form-filter input-sm','required' => false, 'autofocus' => true)); ?>
									</td>
														
									</td>									
									<td>									
									</td>
									<td>	
									</td>
									<td>								
									</td>		
									<td>
										<div class="margin-bottom-5">
											<button class="btn btn-sm blue filter-submit margin-bottom" type="submit"><i class="fa fa-search"></i> Search</button>
										</div>
										
									</td>
									<?php  echo $this->Form->end(); ?>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($supplysources as $supplysource): ?>
								<tr>
								
								<td>
																		
								<?php echo h($supplysource['Supplysource']['name']); ?>
								</td>
								<td>
																		
								<?php echo h($supplysource['Stype']['name']); ?>
								</td>
								<td>
																		
								<?php echo h($supplysource['Supplysource']['email']); ?>
								</td>
								<td>
																		
								<?php echo h($supplysource['Supplysource']['url']); ?>
								</td>
								
								
								<td>
								<div class="btn-group">
									<a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
									<i class="fa fa-bolt"></i> Actions.. <i class="fa fa-angle-down"></i>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
										<a href="/supplysources/edit/<?php echo $supplysource['Supplysource']['id'] ?>">
										<i class="fa fa-edit"></i>
										Edit
										</a>	
										</li>
										<li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $supplysource['Supplysource']['id']), array('escape'=> false), __('Are you sure you want to delete supply source %s?', $supplysource['Supplysource']['name'])); ?></li>
																	
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
