<style>
    .select2 {
        width:100%!important;
    }
</style>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="ProfitRport">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-tasks"></i> Sales report by customer
                        </div>
                        <div class="actions">
                            
                            <div class="btn-group">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><?php echo $this->Html->link(__('<i class="icon-basket-loaded"></i> Orders'), array('controller' => 'salesorders', 'action' => 'index'), array('escape' => false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-bell"></i> Price differences report'), array('controller' => 'salesorders', 'action' => 'price_compare'), array('escape' => false)); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    
                    <div class="portlet-body">
                        <div class="container-fluid">
                            <?php echo $this->Form->create('Order', array('class' => 'form-horizontal', 'id'=>'OrderIndexForm', 'ng-submit' => 'applySearch()')); ?>
                                <div class="row margin-bottom-20">
                                    

                                    <div class="col-md-2">
                                                <?php echo $this->Form->input('customer', array(
                                                    'label' => false,
                                                    'class'=>'form-control form-filter',
                                                    'placeholder' => 'Select Customers ...',
                                                    'options' => $customer_list,
                                                    'ng-change' => 'applySearch()',
                                                    'ng-model' => 'customer',
                                                    'multiple' => 'true',
                                                    'select2' => ''
                                                )); ?>
                                    </div>

                                    <div class="col-md-2">
                                        
                                                <?php echo $this->Form->input('products', array(
                                                    'label' => false,
                                                    'id' => 'products_id',
                                                    'class'=>'form-control form-filter',
                                                    'placeholder' => 'Enter product name',
                                                    'options' => $products_list,
                                                    'ng-change' => 'applySearch()',
                                                    'ng-model' => 'products',
                                                    'multiple' => 'true',
                                                )); ?>
                                            
                                    </div>

                                    <div class="col-md-2">
                                        <?php echo $this->Form->input('status_id',array(
                                            'label' => false,
                                            'class'=>'form-control form-filter',
                                            'options' => array(14 => 'Draft', 2 => 'Released',3 => 'Ship. Proc.', 4 => 'Completed', 8 => 'Shipped', 55 => 'Paid'),
                                            'empty' => 'Status...',
                                            'ng-change' => 'applySearch()',
                                            'ng-model' => "status_id",
                                            'multiple' => true,
                                            
                                        )); ?>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd" data-date-orientation="right">
                                            <input datepickercustom
                                                type="text"
                                                class="form-control form-filter datepicker"
                                                placeholder="From"
                                                name="start_date"
                                                ng-change="applySearch()"
                                                ng-model="start_date"
                                            >
                                            <span class="input-group-btn" ng-click="setDateTime()">
                                                <button class="btn btn-medium default date-set" type="button" ><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd" data-date-orientation="right">
                                            <input datepickercustom
                                                type="text"
                                                class="form-control form-filter datepicker"
                                                placeholder="From"
                                                name="end_date"
                                                ng-change="applySearch()"
                                                ng-model="end_date"
                                            >
                                            <span class="input-group-btn">
                                                <button class="btn btn-medium default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-1 form-inline">
                                        <div class="btn-group pageLimit">
                                            <div class="form-group">
                                                <?php echo $this->Form->select('pageBottom', $options, array(
                                                    'value'=>$limit,
                                                    'default' => 10,
                                                    'empty' => false,
                                                    'class'=>'form-control form-filter input-md limit',
                                                    'ng-change' => 'applySearch()',
                                                    'ng-model' => 'limit',
                                                    'select2' => ''
                                                    )
                                                );  ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 text-right">
                                        <button class="btn btn-fit-height green" id="showTotals" type="button" ng-click="showTotals()">Show totals</button>
                                    </div>

                                </div>
                            <?php echo $this->Form->end(); ?>

                            <div class="row margin-bottom-20" ng-if="showTotalsBlock">
                                <div class="col-md-3 col-md-offset-3">
                                    <div class="dashboard-stat green-haze">
                                        <div class="visual">
                                            <i class="fa fa-shopping-cart"></i>
                                        </div>
                                        <div class="details">
                                            <div class="number">% {{ profit_margin }}</div>
                                            <div class="desc">Average profit margin</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="dashboard-stat red-intense">
                                        <div class="visual">
                                            <i class="fa fa-bar-chart-o"></i>
                                        </div>
                                        <div class="details">
                                            <div class="number">% {{ average_margin }}</div>
                                            <div class="desc">Average profit per line</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="dashboard-stat blue">
                                        <div class="visual">
                                            <i class="fa fa-globe"></i>
                                        </div>
                                        <div class="details">
                                            <div class="number"><?php echo h($this->Session->read('currencyname')); ?> {{ total_profit }}</div>
                                            <div class="desc">Total profit</div>
                                        </div>
                                    </div>
                                </div>

                                <?php /*<div class="col-md-3">
                                    <div class="dashboard-stat purple-plum">
                                        <div class="visual">
                                            <i class="fa fa-bar-chart-o"></i>
                                        </div>
                                        <div class="details">
                                            <div class="number"><?php echo h($this->Session->read('currencyname')); ?> {{ total_year_amount }}</div>
                                            <div class="desc">Year to date sales</div>
                                        </div>
                                    </div>
                                </div>*/ ?>
                            </div>
                        </div>
                        <div id="multiFunctions" class="row margin-bottom-20 hide">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-fit-height dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-v"></i> With selected 
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-lock"></i> Release Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); releaseMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-arrow-left"></i> Issue Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); issueMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Create Wave For Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); waveAddMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-ban"></i> Cancel Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); cancelMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-trash"></i> Delete Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); trashMultiple();')); ?></li>
                                </ul>
                            </div>
                        </div>

                        <div ng-if="orderlines.length > 0">
                            <div class="table-container">
                                <table class="table table-hover">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th><a ng-click="orderBy('Product.sku')" class="sort-link">SKU <i class="fa fa-sort" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Product.name')" class="sort-link">Product <i class="fa fa-sort" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Order.ship_to_customerid')" class="sort-link">Customer <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Order.id')" class="sort-link">Order # <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('OrdersLine.quantity')" class="sort-link">Qty. <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('OrdersLine.unit_price')" class="sort-link">Unit Price <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th>Purchase Price </th>
                                            <th>Margin</th>
                                            <th>Product Price</th>
                                            <th>Margin</th>
                                            <th><a ng-click="orderBy('Order.status_id')" class="sort-link">Status <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('OrdersLine.created')" class="sort-link">Created <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="line in orderlines | orderBy:sortType:sortReverse" id="line_{{line.OrdersLine.id}}" class="filter ">
                                            <td>{{ line.Product.sku }}</td>
                                            <td>{{ line.Product.name }}</td>
                                            <td>{{ line.Order.ship_to_customerid }}</td>
                                            <td><a href="<?php echo $this->App->ang($this->Html->url(array('controller' => 'salesorders', 'action' => 'details', '{{line.OrdersLine.order_id}}'))); ?>">{{ line.OrdersLine.order_id }}</a></td>
                                            <td>{{ line.OrdersLine.quantity }}</td>
                                            <td>{{ priceDisplay(line.OrdersLine.unit_price) }}</td>

                                            <td>
                                                <span ng-if="line[0].purchase_id">
                                                    <a href="<?php echo $this->App->ang($this->Html->url(array('controller' => 'replorders', 'action' => 'details', '{{line[0].purchase_id}}'))); ?>">{{ priceDisplay(line[0].purchase_price) }}</a>
                                                </span>
                                                <span ng-if="!line[0].purchase_id">{{ priceDisplay(line[0].purchase_price) }}</span>
                                            </td>
                                            <td class="{{marginPurchaseClass(line)}}">{{ priceDisplay(100*((line.OrdersLine.unit_price - line[0].purchase_price)/line.OrdersLine.unit_price)) }}%</td>
                                            <td>{{ priceDisplay(line.Product.product_price) }}</td>
                                            <td class="{{marginProductClass(line)}}">{{ priceDisplay(100*((line.OrdersLine.unit_price - line.Product.product_price)/line.OrdersLine.unit_price)) }}%</td>
                                            
                                            <td ng-bind-html="status(line.Order.status_id)">{{status(line.Order.status_id)}}</td>
                                            <td>{{ line.OrdersLine.created }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <p>Total found: {{totals}}</p>
                        <pagination max-size="10" total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="limit"></pagination>

                </div>
                <!-- End: life time stats -->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
</div>
<!-- END CONTENT -->

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    
    var limit = <?php echo (isset($this->params['named']['limit'])?$this->params['named']['limit']: $limit); ?>;
    

    $(document).ready(function() {
        $('#OrderCustomer').select2({
            placeholder: 'Select Customers ..',
        });

        $('select.limit').select2({
            minimumResultsForSearch: -1,
            width: '80px'
        });

        $('#OrderStatusId').select2({
            placeholder: 'Select..',
            minimumResultsForSearch: -1
        });

        $('#products_id').select2({
            placeholder: 'Select Products ..',
        });
    });
<?php $this->Html->scriptEnd(); ?>

<?php echo $this->Html->script('/app/SalesOrders/profit_report.js?v=0.0.2', array('block' => 'pageBlock')); ?>