<?php $this->AjaxValidation->active(); ?>

<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Session->flash(); ?>
					<!-- Begin: life time stats -->
					<div class="portlet box red-sunglo">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-shopping-cart"></i>Open Orders List
							</div>
							<div class="actions">
								
								
								
							</div>
						</div>
						<div class="portlet-body">
							<div class="table-container">
								
								<table class="table table-hover" id="datatable_orders">
								<thead>
								<tr role="row" class="heading">
									
									<th>
										 Order&nbsp;#
									</th>
									<th width="15%">
										<i class="fa fa-sort"></i>
										Created:
									</th>
									<th>
										 Customer Name
									</th>
									<th>
										 Reference&nbsp;Order
									</th>
									<th>
									Products
									</th>
									<th>
									A.T.P.
									</th>
									<th>
									Pack Mat.	
									</th>
									<th>
									Consold.	
									</th>
																	
									<th>
									Address Validation	
									</th>
									<th>
										 Actions
									</th>
								</tr>
								
								</thead>
								<tbody>
								<?php foreach ($ordertoreview as $order): ?>
								<tr role="row" class="filter">
									
									<td>
										<?php echo h($order['OrderId']); ?>
									</td>
									
									<td><?php echo $this->Admin->localTime("%B %d, %Y", strtotime($order['Created'])); ?></td>
									<td>
										<?php echo h($order['CustomerName']); ?>
									</td>
									<td><?php echo h($order['ExtOrderId']); ?>&nbsp;</td>	
									<td>
								
									<?php
									
											if($order['HasNoProduct'])
											{
												echo '<i class="glyphicon glyphicon-remove font-red"></i>';
											} else {
												echo '<i class="glyphicon glyphicon-ok font-green"></i>';
											}
											?>
									</td>
									<td>
									<?php 
									
										if($order['ATP'])
											{
												
												echo '<i class="glyphicon glyphicon-ok font-green"></i>';
											} else {
												echo '<i class="glyphicon glyphicon-remove font-red"></i>';
											}
									 ?>
									 </td>
									
									<td>
									<?php
										if($order['HasPack'])
											{
												echo '<i class="glyphicon glyphicon-ok font-green"></i>';
											} else {
												echo '<i class="glyphicon glyphicon-remove font-red"></i>';
											}
											
										
											?>
									</td>
									<td>
										<?php
									if(!empty($order['ConsolidateCandidate']))
											{
												echo "<span class='label label-danger'>".$order['ConsolidateCandidate']."</span>";
											} else {
												echo "<span class='label label-success'>None</span>";
											}
										?>
									</td>
									<?php
								
												if($order['StateCode'] != 1)
			{
				$addr = $order['Street'].",".$order['City'].",".$order['StateName'].",".$order['Zip'].",".$order['CountryName'];
			} else {
				$addr = $order['Street'].",".$order['City'].",".$order['Zip'].",".$order['CountryName'];
			}
											echo "<td>
											<iframe width=\"250\" height=\"75\" frameborder=\"0\" style=\"border:0\" src=\"https://www.google.com/maps/embed/v1/search?key=AIzaSyCUNb1khtmBzVwLRUmKWzK4jD-tYMe_dbY&q=".h($addr)."\">
											</iframe>
											</td>";
								
									?>
									<td>
										<div class="btn-group">
									<a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
									<i class="fa fa-bolt"></i> Actions.. <i class="fa fa-angle-down"></i>
									</a>
									<ul class="dropdown-menu" role="menu">
																	
									<?php
									
									 echo "<li>";
									 echo $this->Html->link('<i class="fa fa-search"></i> View',array('action' => 'viewcord', $order['OrderId']), array('escape' => false,'target' => '_blank')); 
									 echo "</li>";
									 echo "<li>";
									 echo $this->Html->link('<i class="fa fa-edit"></i> Edit',array('action' => 'editcord', $order['OrderId'],'?' => array('s' => 'fix')), array('escape' => false,'target' => '_blank')); 
									 echo "</li>";
									 echo "<li>";
									 echo $this->Html->link('<i class="fa fa-unlock"></i> Release',array('action' => 'release', $order['OrderId'],'?' => array('s' => 'fix')), array('escape' => false)); 
									 echo "</li>";
									
									?>
								
									</ul>
									</td>
									</tr>
								<?php endforeach; ?>
								
								</tbody>
								</table>
									
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
