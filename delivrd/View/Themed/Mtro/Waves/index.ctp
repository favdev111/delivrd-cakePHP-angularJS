<?php $this->AjaxValidation->active(); ?>
<style>
.release-btns{
      margin-left: 100px;
}
.release-btns a{
      margin-left: 100px;
}
</style>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
			<!-- BEGIN PAGE HEADER-->
			<div class="page-bar">
				<?php echo $this->Form->create('Wave', array(
					'novalidate' => true,
				    'url' => array_merge(array('action' => 'index'),$this->params['pass'])
				    )); ?>
				<div class="row">
					<div class="col-md-4">
					<?php echo $this->Form->input('searchby', array('label' => false,'class'=>'form-control input-md', 'placeholder' => 'Search by Order Number and Reference Number', 'value' => (!empty($this->request->data['Wave']['searchby']) ? $this->request->data['Wave']['searchby'] : ''))); ?>	
					</div>
					<div class="col-md-2">
						<?php echo $this->Form->input('warehouse_id', array('label' => false,'class'=>'form-control form-filter input-md select2me','required' => false,'empty' => 'Location...')); ?>
					</div>
					<div class="col-md-2">
						<?php echo $this->Form->input('status_id',array('label' => false, 'class'=>'form-control form-filter input-md select2me','options' => array(19 => 'Opened',20 => 'Released' ,16 => 'Partially Processed', 4 => 'Completed'),'empty' => 'Status...')); ?>
					</div>
					<div class="col-md-2">					
						<button class="btn btn-md blue filter-submit margin-bottom" type="submit"><i class="fa fa-search"></i></button>
						<?php echo $this->html->link('<i class="fa fa-undo"></i>', array('plugin' => false, 'controller' => 'waves', 'action' => 'index'), array('class' => 'btn btn-md blue filter-submit margin-bottom', 'escape' => false)); ?>
					</div>
					<div class="col-md-1">
						<?php echo $this->Html->link(__('<i class="fa fa-plus"></i>'), array('controller'=> 'waves','action' => 'add'),array('class' => 'btn default yellow-stripe', 'escape'=> false, 'title' => 'New Wave')); ?>
					</div>
					<div class="col-md-1">
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
							<i class="fa fa-ellipsis-h"></i>
							</button>
							<ul class="dropdown-menu pull-right" role="menu">
								<li>
									<?php echo $this->Html->link(__('<i class="fa fa-print"></i> Print Random Tracking Numbers'), array('controller' => 'Labels', 'action' => 'randtrackinglabel'), array('escape'=> false)); ?>
								</li>
							</ul>
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
					<div id="flash"></div>
					<!-- Begin: life time stats -->
					<div class="portlet box delivrd">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-play"></i>Waves List
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
									
									<th width="15%">
										 Id 
									</th>
									<th width="10%">
										Lines
									</th>
									<th width="10%">
										Packed
									</th>
									<th width="15%">
										Location
									</th>
									<th width="15%">
										Status
									</th>
									<th width="15%"><i class="fa fa-sort"></i>
										 <?php echo $this->Paginator->sort('modified'); ?>
									</th>
									<th width="35%">
										 Actions
									</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($waves as $wave): 
								
									switch ($wave['Status']['id']) {
											case "19":
												$status_label = "label label-default";
												break;
											case "20":
												$status_label = "label label-info";
												break;
											case "16":
												$status_label = "label bg-yellow";
												break;
											case "4":
												$status_label = "label label-success";
												break;
											case "45":
												$status_label = "label label-warning";
												break;
											default:
												$status_label = "label label-default";
										}
					
								
								?>
								<tr role="row">
								
								<td><?php echo h($wave['Wave']['id']); ?>&nbsp;</td>
								<td><?php echo h($wave['Wave']['numberoflines']); ?>&nbsp;</td>
								<td><?php echo h($wave['Wave']['linespacked']); ?>&nbsp;</td>
								<td><?php echo h($wave['Warehouse']['name']); ?>&nbsp;</td>
								<td><span class="<?php echo $status_label ?>"><?php echo h($wave['Status']['name']); ?></span></td>
								<td><?php echo $this->Admin->localTime("%B %d, %Y", strtotime($wave['Wave']['modified'])); ?></td>

								<td>
									<?php if ($wave['Wave']['status_id'] == 19)
									{	
										if($popup == 1) { 
											echo $this->Html->link('Release',array('controller' => 'waves', 'action' => 'release', $wave['Wave']['id']), array('escape' => false, 'class' => 'btn btn-sm blue'));
										} else {
											echo '<a href="#" class="release-id btn btn-sm blue" id="'. $wave['Wave']['id'] .'" data-toggle="modal" data-href1="' . Router::url(array('controller' => 'waves', 'action' => 'view', 'wave_id' => $wave['Wave']['id']), true) . '" data-href2="' . Router::url(array('controller' => 'waves', 'action' => 'release',$wave['Wave']['id']), true) . '"data-target="#release-modal"> Release</a>';
										}
										
									} 
									if ($wave['Wave']['status_id'] == 20 || $wave['Wave']['status_id'] == 16)
									 {
										if($wave['Wave']['type'] != 2) {
											if(!empty($wave['User']['pick_by_order'])) {
												echo $this->Html->link(__(' Pick By Order'), array('action' => 'pickbyorder',$wave['Wave']['id']), array('escape'=> false, 'class' => 'btn btn-sm blue'));  
											} else {
												echo '<a href="' . Router::url(array('controller' => 'waves', 'action' => 'editSettings'), true) . '" class="btn btn-sm blue" data-remote="false" data-toggle="modal" data-label="Set Pick By Order preference" data-target="#delivrd-modal"> Pick By Order</a>';
											}
										} 
									 } ?>
									<div class="btn-group">
										<a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
										<i class="fa fa-ellipsis-h"></i>
										</a>
										<ul class="dropdown-menu pull-right">
											<li>
											<?php echo $this->Html->link(__('<i class="fa fa-search"></i>
											View'), array('controller'=> 'waves','action' => 'view', 'wave_id' => $wave['Wave']['id']),array('escape'=> false)); ?>	
											</li> 
											<?php if ($wave['Wave']['status_id'] == 45)
											{ ?>										
												<li>
													<?php echo $this->Html->link(__('<i class="fa fa-cubes"></i>
													Pack Orders'), array('action' => 'pickbyorder',$wave['Wave']['id']),array('escape'=> false)); ?>
												</li>
											<?php } 
											if ($wave['Wave']['status_id'] == 19)
											{	?>							
												<li>
													<?php echo $this->Html->link(__('<i class="fa fa-pencil"></i>
													Edit'), array('controller'=> 'waves','action' => 'edit', 'wave_id' => $wave['Wave']['id']),array('escape'=> false)); ?>
												</li> 
											<?php } 
											if($wave['Wave']['status_id'] == 16) { ?>
												<li>
													<?php echo $this->Html->link(__('<i class="fa fa-cubes"></i> Batch Picking'), array('action' => 'batchpicking',$wave['Wave']['id']), array('escape'=> false)); ?>
												</li>
											<?php
												// if($wave['Wave']['pick_process'] == 0) {
												// 	if(!empty($wave['User']['batch_pick'])) {
												// 		echo '<li>' .$this->Html->link(__('<i class="fa fa-cubes"></i> Batch Picking'), array('action' => 'batchpicking',$wave['Wave']['id']), array('escape'=> false)) . '<li/>';
												// 	}
												// 	else {
												// 		echo '<li><a href="' . Router::url(array('controller' => 'waves', 'action' => 'editSettings'), true) . '" data-remote="false" data-toggle="modal" data-label="Set Batch Pick preference" data-target="#delivrd-modal"><i class="fa fa-cubes"></i> Batch Picking</a></li>';
												// 	}
												// }
												// if($wave['Wave']['pick_process'] == 1) {
												// 	if(!empty($wave['User']['pick_by_order'])) {
												// 		echo "<li>";
												// 		echo $this->Html->link(__('<i class="fa fa-cubes"></i> Pick By Order'), array('action' => 'pickbyorder',$wave['Wave']['id']), array('escape'=> false));  
												// 		echo "</li>";
												// 	} else {
												// 		echo '<li><a href="' . Router::url(array('controller' => 'waves', 'action' => 'editSettings'), true) . '" data-remote="false" data-toggle="modal" data-label="Set Pick By Order preference" data-target="#delivrd-modal"><i class="fa fa-cubes"></i> Pick By Order</a></li>';
												// 	}
												// }
											 }
											if ($wave['Wave']['status_id'] == 20) { ?>
												<li>
													<?php echo $this->Html->link(__('<i class="fa fa-cubes"></i> Batch Picking'), array('action' => 'batchpicking',$wave['Wave']['id']), array('escape'=> false)); ?>
												</li>
											<?php if($wave['Wave']['status_id'] != 16) {
													echo "<li>";
													echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print Pick Slip By Product'), array('action' => 'pickslipbyproduct', $wave['Wave']['id']), array('escape'=> false)); 
													echo "</li>";
													echo "<li>";
													echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print Pick Slip By Order'), array('action' => 'pickslipbyorder', $wave['Wave']['id']), array('escape'=> false)); 
													echo "</li>";
												}
											} ?>
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
        if($paginator->hasPrev()){
            echo $paginator->prev("Prev", array('tag' => 'li'));
        }
        echo $paginator->numbers(array('modulus' => 2,'tag' => 'li','currentTag' => 'a','currentClass' => 'active','separator' => false));
         
        // for the 'next' button
        if($paginator->hasNext()){
            echo $paginator->next("Next",array('tag' => 'li'));
        }
         
        // the 'last' page button
        echo $paginator->last("Last",array('tag' => 'li')); 
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

<div class="modal fade" id="release-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body release-btns">
          <p>Would you like to view wave details before releasing wave ?</p>
          <a href="" class="btn btn-md blue" id="review-wave">Review wave</a>
          <a href="" class="btn btn-md blue" id="release">Release</a>
          <div class="row" style="margin-top: 13px;">
			<div class="col-md-2 value">
			 <input type="checkbox" name="show_message" id="show_message" />
			</div>
			<div class="col-md-6 name" style="margin-left: -46px;">
				<p>Don't show this message again</p>
			</div>
		  </div>
      </div>
    </div>
  </div>
</div>
