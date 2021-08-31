<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="prReport">
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-shopping-cart"></i>
                            Price Differences Report
                        </div>
                        <div class="actions">
                        </div>
                    </div>

                    <div class="portlet-body">

                        <?php echo $this->Form->create('Order', array('class' => 'form-horizontal', 'id'=>'OrderIndexForm', 'ng-submit' => 'applySearch()')); ?>
                            <div class="row margin-bottom-20">

                                <label class="col-md-1 control-label text-left">Filter By: </label>
                                <div class="col-md-3">
                                    <div class="input-group col-md-12">
                                        <?php echo $this->Form->input('searchby', array(
                                            'label' => false,
                                            'class'=>'code-scan form-control',
                                            'placeholder' => 'Enter or scan SKU/EAN/UPC/Serial or product name',
                                            'id' => 'product_auto',
                                            'value' => ''
                                        )); ?>
                                        <span class="input-group-addon"><button type="button" ng-click="clearProduct()" class="" title="Search" style="border:none"><i class="fa fa-eraser"></i></button></span>
                                    </div>
                                    <input 
                                        type="hidden"
                                        ng-change="applySearch()"
                                        ng-model="product_id"
                                        id="product_id"
                                    >
                                </div>
                                
                                <div class="col-md-2">
                                    <?php echo $this->Form->input('schannel_id', array(
                                        'label' => false,
                                        'class'=>'form-control form-filter input-md',
                                        'required' => false,
                                        'empty' => 'Sales Channel...',
                                        'ng-change' => 'applySearch()',
                                        'ng-model' => "schannel_id",
                                        'select2' => ''
                                    )); ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo $this->Form->input('status_id',array(
                                        'label' => false,
                                        'class'=>'form-control form-filter input-md',
                                        'options' => array(14 => 'Draft', 2 => 'Released',3 => 'Ship. Proc.', 4 => 'Completed', 8 => 'Shipped'),
                                        'empty' => 'Order Status...',
                                        'ng-change' => 'applySearch()',
                                        'ng-model' => "status_id",
                                        'select2' => ''
                                    )); ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo $this->Form->input('difference',array(
                                        'label' => false,
                                        'class'=>'form-control form-filter input-md',
                                        'options' => array(1 => 'Only differences from sales channel price', 2 => 'Only differences from product price'),
                                        'empty' => 'Select Report Type...',
                                        'ng-change' => 'applySearch()',
                                        'ng-model' => "difference",
                                        'select2' => ''
                                    )); ?>
                                </div>
                                <div class="col-md-2 text-right">
                                    <button class="btn btn-md blue filter-submit margin-bottom" type="button" ng-click="showAll()">Clear Filters</button>
                                </div>
                            </div>
                        <?php echo $this->Form->end(); ?>

                    	 <div class="tab-content no-space">
                            <div class="tab-pane active" id="tab_thistory">
                                <table class="table table-hover no-footer" role="grid">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th><a ng-click="orderBy('OrdersLine.order_id')" class="sort-link">Order # <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Schannel.name')" class="sort-link">Channel <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Product.sku')" class="sort-link">SKU <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Product.name')" class="sort-link">Product <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th>Quantity</th>
                                            <th>Order Price(<?php echo h($this->Session->read('currencysym')); ?>)</th>
                                            <th>Product Price(<?php echo h($this->Session->read('currencysym')); ?>)</th>
                                            <th>Schannel Price(<?php echo h($this->Session->read('currencysym')); ?>)</th>
                                            <th><a ng-click="orderBy('OrdersLine.modified')" class="sort-link">Modified <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('DcopUser.email')" class="sort-link">Created By <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    	<tr ng-repeat="line in orderlines | orderBy:sortType:sortReverse">
                                            <td><a href="<?php echo $this->App->ang($this->Html->url(array('controller' => 'salesorders', 'action' => 'details', '{{line.OrdersLine.order_id}}'))); ?>">{{ line.OrdersLine.order_id }}</a></td>
                                            <td>{{ line.Schannel.name }}</td>
                                    		<td>{{ line.Product.sku }}</td>
                                            <td>{{ line.Product.name }}</td>
                                    		<td>{{ line.OrdersLine.quantity }}</td>
                                    		<td>{{ priceDisplay(line.OrdersLine.unit_price) }}</td>
                                            <td ng-bind-html="priceDefDisplay(line)">{{ priceDefDisplay(line) }}</td>
                                    		<td ng-bind-html="priceChannelDisplay(line)">{{ priceChannelDisplay(line) }}</td>
                                            <td>{{ line.OrdersLine.modified }}</td>
                                    		<td>{{ line.DcopUser.email }}</td>
                                    	</tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <p>Total found: {{totals}}</p>
                        <pagination max-size="30" total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="limit"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var limit = <?php echo $limit; ?>

    $("#product_auto").autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: siteUrl + 'products/get_auto_list',
                dataType: "json",
                data: {
                    key: request.term
                },
                success: function( data, test ) {
                    response( data.name );
                }
            });
        },
        select: function( event, ui ) {
            if(ui.item) {
                
                if(ui.item.product_id !== undefined) {
                    $("#product_id").val(ui.item.product_id);
                } else {
                    $("#product_id").val('');
                }
                
                var scope = angular.element("#product_id").scope();
                scope.product_id = $("#product_id").val();
                scope.applySearch();
                return ui.item.label;
            }
        },
    });
<?php $this->Html->scriptEnd(); ?>

<?php echo $this->Html->script('/app/SalesOrders/price_compare.js?v=0.0.1', array('block' => 'pageBlock')); ?>