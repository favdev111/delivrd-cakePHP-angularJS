<?php //$this->AjaxValidation->active(); 
    $indexurl = "/shipments/indexsh";
    $exportcsvurl = "/shipments/exportcsv/99";	
	$pagetext = "Shipments List";
	$pagecolor = "green-jungle";
	$icon = "fa-plane";
?>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<?php echo $this->element('expirytext'); ?>
			<!-- BEGIN PAGE HEADER-->
			<?php #echo $this->Html->link(__('<i class="fa fa-cube"></i>'.$status), array(), array('escape'=> false, 'class' => 'change_status', 'data-id' => $key)); ?>
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Session->flash(); ?>
					<!-- Begin: life time stats -->
					<div class="portlet box <?php echo $pagecolor ?>">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa <?php echo $icon; ?>"></i><?php echo $pagetext ?> List
							</div>
							<div class="actions">
							
								<div class="btn-group">
									<a class="btn default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
									<i class="fa fa-share"></i> Tools <i class="fa fa-angle-down"></i>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
											<a href="<?php echo $exportcsvurl ?>">
											Export shipments to CSV </a>
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
									<th>
										 #
									</th>
									<th>
										 Id
									</th>
									<th>Ext. Order#</th>
									<th>
										 Partner
									</th>
                                                                        <th>
										 Pick From
									</th>
									<th>
										 Ship To
									</th>
                                                                        <th>
										 Courier
									</th>
                                                                        <th>
										 Tracking Number
									</th>
									
									<th>
									<i class="fa fa-sort"></i>
										<?php echo $this->Paginator->sort('created') ?>
									</th>
                                                                        <th>
									
										Updated
									</th>
									<th>
										 Status
									</th>
									<th>
										 Actions
									</th>
								</tr>
								<tr role="row" class="filter">
									<?php echo $this->Form->create('Shipment', array(
								    'url' => array_merge(
								            array(
								                'action' => 'indexsh'
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
								
									
										<?php	//echo $this->Form->input('courier_id',array('label' => false, 'class'=>'form-control form-filter input-sm'));
									?>
									</td>
									<td>
									<?php	 echo $this->Form->input('tracking_number', array('label' => false,'class'=>'form-control form-filter input-sm','required' => false, 'autofocus' => true)); ?>	
									</td>
									
									<td>
										<div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
											
											<input type="text" class="form-control form-filter input-sm" name="data[Shipment][createdfrom]" placeholder="From">
											<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
										</div>
									</td>
                                                                        <td>
								
									</td>
									<td>
										<?php	echo $this->Form->input('status_id',array('label' => false, 'class'=>'form-control form-filter input-sm','options' => $statussearch,'empty' => '(Select..)'));
									?>
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
								
								<?php foreach ($shipments as $key => $shipment): 
								
								switch ($shipment['Status']['id']) {
											case "1":
												$status_label = "label label-default";
												break;
											case "6":
												$status_label = "label bg-yellow-gold";
												break;
											case "16":
												$status_label = "label bg-yellow";
												break;
											case "7":
												$status_label = "label label-success";
												break;
											case "8":
												$status_label = "label label-success";
												break;
                                                                                         case "31":
												$status_label = "label bg-blue";
												break;
                                                                                        case "32":
												$status_label = "label bg-purple";
												break;
                                                                                        case "35":
												$status_label = "label bg-yellow";
												break;
                                                                                        case "36":
												$status_label = "label bg-red";
												break;
                                                                                        case "37":
												$status_label = "label bg-red";
												break;
                                                                                        case "38":
												$status_label = "label bg-red";
												break;
                                                                                        case "39":
												$status_label = "label bg-red";
												break;
                                                                                        case "40":
												$status_label = "label bg-red";
												break;
                                                                                        case "41":
												$status_label = "label bg-purple";
												break;
                                                                                            case "42":
												$status_label = "label label-success";
												break;
                                                                                            case "43":
												$status_label = "label label-success";
												break;
                                                                                            case "44":
												$status_label = "label label-success";
												break;
											default:
												$status_label = "label label-default";
										}
								
								?>

								<tr role="row">
								<td><?php echo $this->Form->checkbox('Shipment.id.' . $key, array('class' => 'checkboxes', 'value' => $shipment['Shipment']['id'], 'hiddenField' => false)); ?>
								<?php //echo $this->Form->hidden('id', array('value' => 5)); ?>
								</td>
								<td><?php echo h($shipment['Shipment']['id']); ?></td>
								<?php  echo "<td>".$this->Html->link(__(h($shipment['Order']['external_orderid'])), array('controller' => 'orders', 'action' => 'viewrord', $shipment['Shipment']['order_id']))."</td>"; ?>
                                                                <?php  echo "<td>".h($shipment['User']['company'])."</td>"; ?>
                                                                <?php  echo "<td>".h($shipment['User']['city'])."</td>"; ?>
                                                                <?php  echo "<td>".h($shipment['Order']['ship_to_city'])."</td>"; ?>
								<td><?php echo h($shipment['Courier']['name']); ?></td>
								<td><?php echo $shipment['Shipment']['tracking_number']; ?> </td>
								<td><?php echo $this->Admin->localTime("%B %d, %Y", strtotime($shipment['Shipment']['created'])); ?></td>
                                <td><?php echo $this->Admin->localTime("%B %d, %Y %H:%M:%S", strtotime($shipment['Shipment']['modified'])); ?></td>
								<td><span class="<?php echo h($status_label) ?>"><?php echo h($shipment['Status']['name']); ?></span></td>
								<td>
								<div class="btn-group">
									<a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
									<i class="fa fa-bolt"></i> Actions.. <i class="fa fa-angle-down"></i>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
										<a href="/shipments/view/<?php echo $shipment['Shipment']['id'] ?>">
										<i class="fa fa-search"></i>
										View
										</a>	
										</li>										
										<li>
										<a href="/shipments/edit/<?php echo $shipment['Shipment']['id'] ?>">
										<i class="fa fa-pencil"></i>
										Edit
										</a>
										</li>
                                                                                <?php if($shipment['Shipment']['status_id'] == 15 || $shipment['Shipment']['status_id'] == 16) { ?>
                                                                                <li>
										<a href="/orders_lines/find/<?php echo $shipment['Shipment']['order_id'] ?>">
										<i class="fa fa-cube"></i>
										Pick & Pack
										</a>
										</li>
                                                                                 <?php } ?>
                                                                                <?php foreach ($statuses as $key=>$status): ?>
                                                                                
                                                                               
                                                                                <li>
										<a href="/shipments/changestatusp/<?php echo $shipment['Shipment']['id']."/".$key; ?>">
										<i class="fa fa-cube"></i>
                                                                                <?php echo $status; ?>
										</a>
										</li>
                                                                                <?php endforeach; ?>
										
									</ul>
								</div>
								
								
								</td>
								</tr>
								<?php endforeach; ?>
								</tbody>
								 
								</table>
								<?php echo $this->Form->create('Shipment', array(
			                        'type' => 'post',
			                        'id' => 'checkbox_form',
			                        'url' => array('action' => 'multiplechangestatus'),
			                        'class' => 'form-horizontal list_data_form',
			                        'novalidate' => true,
                    			)); 
                    			echo $this->Form->hidden('status', array('id' => 'shipment_status')); 
                    			echo $this->Form->hidden('id', array('id' => 'shipment_id')); 
                    			echo $this->Form->end(); ?> 
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
