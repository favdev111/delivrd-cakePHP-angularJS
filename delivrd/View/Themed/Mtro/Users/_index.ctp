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
					<div class="portlet box red-thunderbird">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-play"></i>Partners List
							</div>
							<div class="actions">
							<a href="/waves/add" class="btn default yellow-stripe">
								<i class="fa fa-plus"></i>
								<span class="hidden-480">
								 </span>
								</a>
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
										Username
									</th>
									<th width="15%">
										Email
									</th>
                                                                         <th width="15%">
										City
									</th>
									<th width="15%">
										Created On
									</th>
                                                                       
									<th width="10%">
										Last Login
									</th>
						
									<th width="10%">
										 Actions
									</th>
								</tr>
								<tr role="row" class="filter">
									<?php echo $this->Form->create('User', array(
    'url' => array_merge(
            array(
                'action' => 'index'
            ),
            $this->params['pass']
        )
    )
); ?>
									<td>
							
									</td>
									<td>
										
									</td>
									
									<td>
									
									</td>
									<td>
										
									</td>
									<td>
									
								
									</td>
									
									
							
									
									<td>	
									</td>
									<?php  echo $this->Form->end(); ?>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($users as $user): 
								
																	
								?>
								<tr role="row">
								
								<td><?php echo h($user['User']['username']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['email']); ?>&nbsp;</td>
                <td><?php echo h($user['User']['city']);?></td>
		<td><?php echo h($user['User']['created']);?></td>
                <td><?php echo h($user['User']['last_login']); ?>&nbsp;</td>
		

								<td>
								<div class="btn-group">
									<a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
									<i class="fa fa-bolt"></i> Actions.. <i class="fa fa-angle-down"></i>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
										<a href="/users/view/<?php echo $user['User']['id'] ?>">
										<i class="fa fa-search"></i>
										View
										</a>	
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
