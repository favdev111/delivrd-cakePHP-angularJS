<?php $this->AjaxValidation->active(); ?>

	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE HEADER-->
			<h3 class="page-title" id="Welcome">
			Delivrd Inventory Management and Order Fulfillment
			</h3>

            <?php echo $this->element('expirytext'); ?>
			<?php echo $this->Session->flash(); ?>
			<!-- END PAGE HEADER-->
			<!-- BEGIN DASHBOARD STATS -->
				<?php if($orstep == 'prod') {?>
			<blockquote>
				<H2>Welcome to Delivrd!</H2>
                        <p> The first step in setting up Delivrd is to create your first products.</p>
                        <a href='/products/add'><span class="btn blue-steel"><i class="fa fa-barcode"></i>create products manually</span></a> or <a href='<?php echo $this->Html->url(['controller' => 'products', 'action' => 'add_products_csv']);?>'><span class="btn blue-steel"><i class="fa fa-file-excel-o"></i>import products from csv file</span></a>
                        <p> Once you have created your products, you can return to this <a href='/'><i class="icon-home"></i> home page</a> for instruction on your next steps.</p>
                       
                </blockquote>
			<?php } else if($orstep == 'inv'){ ?>	
			<blockquote>
				
                        <p> You have created <?php echo $productscount ?> product(s) in Delivrd, it's time to <a href='/inventories'><span class="btn blue"><i class="fa fa-barcode"></i>update their inventory quantities.</span></a></p>
                   
                </blockquote>
                <?php } else if($orstep == 'ordl'){ ?>	
			<blockquote>
				
                        <p> You have created <?php echo $productscount ?> product(s) in Delivrd and updated their inventory, you are ready for your next steps.</p>
                     
                       <div class="tabbable-line">
                                        <ul class="nav nav-tabs ">
                                            <li class="active">										
                                                <a href="#tab_binv" data-toggle="tab"> Basic Inventory Management </a>
                                            </li>
                                            <li>
                                                <a href="#tab_15_2" data-toggle="tab"> Create Purchase Order </a>
                                            </li>
                                            <li>
                                                <a href="#tab_15_3" data-toggle="tab"> Create Sales Order </a>
                                            </li>
                                          
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_binv">
                                             
                                                <p> To start receiving and issuing stock, go to the inventory page, click on the 'Actions' drop down menu of a product, and use 'Receive/Issue' for inventory management.</p>
                                            <a href='/inventories'><span class="btn blue"><i class="fa fa-barcode"></i>Inventory Page</span></a>
                                            </div>
                                            <div class="tab-pane" id="tab_15_2">
                                              
                                                <p> To create replenishment (purchase) orders, you first need to create suppliers. Once suppliers have been created, you can create replenishment orders.</p>
                                                <p>
                                                <a href='/suppliers/add'><span class="btn yellow-gold"><i class="fa fa-exchange"></i>Create new supplier</span></a>
                                                <a href='/orders/addrord'><span class="btn green"><i class="fa fa-random"></i>Create replenishment order</span></a>
                                             
                                                </p>
                                            </div>
                                            <div class="tab-pane" id="tab_15_3">
                                               <p> To create a sales orders, you first need to create at least one sales channel. Once a sales channel has been created, you can create a sales orders.</p>
                                                <p>
                                                <a href='/schannels/add'><span class="btn yellow-lemon"><i class="fa fa-th"></i>Create sales channel</span></a>
                                                <a href='/orders/addcord'><span class="btn red"><i class="fa fa-shopping-cart"></i>Create sales order</span></a>
                                             
                                                </p>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                </blockquote>
              <?php } else if($orstep == 'hasinv'){ ?> 
			
			<!-- END DASHBOARD STATS -->
			<div class="clearfix">
			</div>
			
			<div class="clearfix">
			</div>
			<div class="row">					
				<div class="col-md-6 col-sm-6">
					<div class="portlet box blue">
						<div class="portlet-title">
						<div class="caption">
						<i class="fa fa-trophy font-grey"></i>
						Top 10 Slow Movers
						</div>

						</div>
						<div class="portlet-body">
							<div class="table-scrollable">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>#</th>
											<th>Name</th>
                                            <th>SKU</th>
											<th>Last Trans.</th>
											<th>Quantity</th>
                                            <th>Location</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($slowmovers as $key=>$slowmover):
                                            $lasttransactiondate = date("m-d-y",strtotime($slowmover['Inventory']['modified']));
										echo "<tr>";
										echo "<td>".($key+1)."</td>";
										echo "<td>" . $this->Html->link(__($slowmover['Product']['name']), array('controller'=> 'inventories','action' => 'transactions_history', $slowmover['Product']['id']), array('escape'=> false)). "</td>"; 
										//echo "<td>".'<a href="/transactionshistory/'.$slowmover['Product']['id'].'">'.$slowmover['Product']['name']."</a></td>";
										echo "<td>".$slowmover['Product']['sku']."</td>";
                                        echo "<td>".$lasttransactiondate."</td>";
										echo "<td>".$slowmover['Inventory']['quantity']."</td>";
                                        echo "<td>".$slowmover['Warehouse']['name']."</td>";
										echo "</tr>";
										endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
									</div>
								</div>
							
							<!--END TABS-->
					
				
					<!-- END PORTLET-->
			
				<div class="col-md-6 col-sm-6">
					<div class="portlet box blue">
						<div class="portlet-title">
						<div class="caption">
						<i class="fa fa-trophy font-grey"></i>
						Most Valuable Products
						</div>

						</div>
						<div class="portlet-body">
							<div class="table-scrollable">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>#</th>
											<th>Product</th>
                                            <th>SKU</th>
                                            <th>Quantity</th>
											<th>Total value</th>
										</tr>
									</thead>
									<tbody>
										<?php 
										$x=1;
										foreach ($highestvalues as $key=>$highestvalue):
										echo "<tr>";
										echo "<td>".$x."</td>";
										echo "<td>" . $this->Html->link(__($highestvalue['Product']['name']), array('plugin' => false,'controller'=> 'inventories','action' => 'index', '?' => ['product' => $highestvalue['Product']['id']]),array('escape'=> false)) . "</td>";
                                        echo "<td>".$highestvalue['Product']['sku']."</td>";
                                        echo "<td>".$highestvalue['Inventory']['quantity']."</td>";
                                        echo "<td>".$highestvalue['Product']['value']*$highestvalue['Inventory']['quantity']."</td>";
										echo "</tr>";
										$x++;
										 endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
						
									</div>
								</div>
							</div>
							<?php } ?>	
							<!--END TABS-->
						</div>
					</div>
					<!-- END PORTLET-->
				</div>
			</div>
			<div class="clearfix">
			</div>
			
				</div>
			</div>
		</div>
	</div>


<!-- END CONTAINER -->


      <!-- Tip Content -->
    <ol id="joyRideTipContent">
      <li data-class="page-title" data-text="Next" class="custom">
        <h2>Stop #1</h2>
        <p>You can control all the details for you tour stop. Any valid HTML will work inside of Joyride.</p>
      </li>
      <li data-id="prodlink" data-button="Next" data-options="tipLocation:top;tipAnimation:fade">
        <h2>Stop #2</h2>
        <p>Get the details right by styling Joyride with a custom stylesheet!</p>
      </li>
      <li data-id="prodsub" data-button="Next" data-options="tipLocation:right">
        <h2>Stop #3</h2>
        <p>It works right aligned.</p>
      </li>
      <li data-button="Next">
        <h2>Stop #4</h2>
        <p>It works as a modal too!</p>
      </li>
      <li data-class="someclass" data-button="Next" data-options="tipLocation:right">
        <h2>Stop #4.5</h2>
        <p>It works with classes, and only on the first visible element with that class.</p>
      </li>
      <li data-id="numero5" data-button="Close">
        <h2>Stop #5</h2>
        <p>Now what are you waiting for? Add this to your projects and get the most out of your apps!</p>
      </li>
    </ol>



