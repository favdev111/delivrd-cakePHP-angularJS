<?php $this->AjaxValidation->active(); ?>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Session->flash(); ?>
					<!-- Begin: life time stats -->
					<div class="portlet box blue-steel">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-barcode">Product created by user <?php echo $user['User']['slug'] ?></i>
							</div>
							<div class="actions">
							
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
									
									<th>
										 SKU
									</th>
									<th>
										 Product&nbsp;Name
									</th>
									<th>
										 Category
									</th>
									<th>
										 Image
									</th>
									
									<th>
										 Date&nbsp;Created
									</th>
									<th>
										 Status
									</th>
									<?php if ($this->Session->read('showvariants') == 1) { ?>				
									<th>
										 Color
									</th>
									<th>
										 Size
									</th>
									<?php } ?>	
									<th>
										 Actions
									</th>
								</tr>
								<tr role="row" class="filter">
									<?php echo $this->Form->create('Product', array(
    'url' => array_merge(
            array(
                'action' => 'index'
            ),
            $this->params['pass']
        )
    )
); ?>
									<td>
								<?php	 echo $this->Form->input('sku', array('label' => false,'class'=>'form-control form-filter input-sm','required' => false,'autofocus' => true));
									?>
									</td>
									<td>
										<?php	 echo $this->Form->input('name', array('label' => false,'class'=>'form-control form-filter input-sm','required' => false)); ?>
									</td>
									<td>
									<?php	 echo $this->Form->input('group_id', array('label' => false,'class'=>'form-control form-filter input-sm','required' => false,'empty' => 'Select..'));
									?>
								
									</td>
									<td>
										
									</td>
									
									<td>
										
									</td>
									<td>
										<?php	 echo $this->Form->input('status_id', array('label' => false,'class'=>'form-control form-filter input-sm','required' => false,'empty' => 'Select..'));
									?>
									</td>
									<?php if ($this->Session->read('showvariants') == 1) { ?>	
									<td>
										
									</td>
									
									<td>
										
									</td>
									<?php } ?>
									<td>
										<div class="margin-bottom-5">
											<button class="btn btn-sm blue filter-submit margin-bottom" type="submit"><i class="fa fa-search"></i> Search</button>
										</div>
										
									</td>
									<?php  echo $this->Form->end(); ?>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($products as $product): 
								switch ($product['Status']['id']) {
											case "1":
												$status_label = "label label-default";
												break;
											case "12":
												$status_label = "label label-warning";
												break;
											case "13":
												$status_label = "label label-danger";
												break;
											default:
												$status_label = "label label-default";
										}
										
										?>
								<tr role="row">
								
								<td><?php echo h($product['Product']['sku']); ?></td>
								<td><?php echo h($product['Product']['name']); ?></td>
								<td><?php echo h($product['Group']['name']); ?></td>
								<td><?php echo "<img src=".$product['Product']['imageurl']." height='32px' width='32px'>"; ?></td>
								<td><?php echo date("F j, Y",strtotime($product['Product']['created']));?></td>
								<td><span class="label label-sm <?php echo $status_label ?>"><?php echo h($product['Status']['name']); ?></span></td>
								
								<?php if ($this->Session->read('showvariants') == 1) { ?>			
								
								<td><?php if(!empty($product['Product']['color_id'])) 
								echo "<li class='list-group-item'>".$product['Color']['name']." <span class='badge' style='background-color:#".$product['Color']['htmlcode']."'>".$product['Color']['name']."</span></li>"; ?></td>
								<td><?php if(!empty($product['Product']['size_id']))  echo h($product['Size']['name']); ?></td>
								<?php } ?>
								<td>
								
								<?php endforeach; ?>
								</tbody>
								</table>
							
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
