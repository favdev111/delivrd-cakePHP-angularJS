<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="CustomerRport">
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
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-users"></i></span>
                                            <?php echo $this->Form->input('customer', array(
                                                'label' => false,
                                                'class'=>'form-control input-md',
                                                'placeholder' => 'Select Customers ...',
                                                'options' => $customer_list,
                                                'ng-change' => 'applySearch()',
                                                'ng-model' => 'customer',
                                                'multiple' => 'true',
                                                'select2' => ''
                                            )); ?>
                                        </div>
                                        <button id="SubmitBtn" class="btn btn-md blue-354fdc filter-submit margin-bottom" type="button" ng-click="applySearch()" style="display:none;"><i class="fa fa-search"></i></button>
                                    </div>

                                    <div class="col-md-3">
                                        <?php echo $this->Form->input('status_id',array(
                                            'label' => false,
                                            'class'=>'form-control form-filter input-md',
                                            'options' => array(14 => 'Draft', 2 => 'Released',3 => 'Ship. Proc.', 4 => 'Completed', 8 => 'Shipped', 55 => 'Paid'),
                                            'empty' => 'Status...',
                                            'ng-change' => 'applySearch()',
                                            'ng-model' => "status_id",
                                            'multiple' => true,
                                            
                                        )); ?>
                                    </div>

                                    <div class="col-md-1">
                                        <button class="btn btn-md blue-354fdc filter-submit margin-bottom" id="csvExport"><i class="fa fa-download"></i> Export</button>
                                    </div>
                                </div>
                            <?php echo $this->Form->end(); ?>

                            <div class="row margin-bottom-20">
                                <div class="col-md-3">
                                    <div class="dashboard-stat green-haze">
                                        <div class="visual">
                                            <i class="fa fa-shopping-cart"></i>
                                        </div>
                                        <div class="details">
                                            <div class="number"><?php echo h($this->Session->read('currencyname')); ?> {{ total_amount }}</div>
                                            <div class="desc">Total sales</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="dashboard-stat red-intense">
                                        <div class="visual">
                                            <i class="fa fa-bar-chart-o"></i>
                                        </div>
                                        <div class="details">
                                            <div class="number"><?php echo h($this->Session->read('currencyname')); ?> {{ monthly_average }}</div>
                                            <div class="desc">Average monthly sales</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="dashboard-stat blue">
                                        <div class="visual">
                                            <i class="fa fa-globe"></i>
                                        </div>
                                        <div class="details">
                                            <div class="number"><?php echo h($this->Session->read('currencyname')); ?> {{ total_month_amount }}</div>
                                            <div class="desc">Month to date sales</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="dashboard-stat purple-plum">
                                        <div class="visual">
                                            <i class="fa fa-bar-chart-o"></i>
                                        </div>
                                        <div class="details">
                                            <div class="number"><?php echo h($this->Session->read('currencyname')); ?> {{ total_year_amount }}</div>
                                            <div class="desc">Year to date sales</div>
                                        </div>
                                    </div>
                                </div>
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
                                            <th><a ng-click="orderBy('Order.id')" class="sort-link">Order # <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Order.ship_to_customerid')" class="sort-link">Customer <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Product.name')" class="sort-link">Product <i class="fa fa-sort" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Warehouse.name')" class="sort-link">Location <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('OrdersLine.quantity')" class="sort-link">Qty. <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('OrdersLine.unit_price')" class="sort-link">Unit Price <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('OrdersLine.total_line')" class="sort-link">Total Price <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Order.status_id')" class="sort-link">Status <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('OrdersLine.created')" class="sort-link">Created <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th width="140px">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="line in orderlines | orderBy:sortType:sortReverse" id="line_{{line.OrdersLine.id}}" class="filter">
                                            <td><a href="<?php echo $this->App->ang($this->Html->url(array('controller' => 'salesorders', 'action' => 'details', '{{line.OrdersLine.order_id}}'))); ?>">{{ line.OrdersLine.order_id }}</a></td>
                                            <td>{{ line.Order.ship_to_customerid }}</td>
                                            <td>{{ line.Product.name }}</td>
                                            <td>{{ line.Warehouse.name }}</td>
                                            <td>{{ line.OrdersLine.quantity }}</td>
                                            <td>{{ priceDisplay(line.OrdersLine.unit_price) }}</td>
                                            <td>{{ priceDisplay(line.OrdersLine.total_line) }}</td>
                                            
                                            <td ng-bind-html="status(line.Order.status_id)">{{status(line.Order.status_id)}}</td>
                                            <td>{{ line.OrdersLine.created }}</td>
                                            <td>{{ line.OrdersLine.comments }}</td>
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

        $('#csvExport').click(function() {
            var action = $('#OrderIndexForm').attr('action');

            $('#OrderIndexForm').attr('action', '<?php echo $this->Html->url(['controller' => 'salesorders', 'action' => 'customer_report_csv']); ?>');
            $('#OrderIndexForm').attr('target', '_blank');
            //$('#OrderIndexForm').submit();

            //$('#OrderIndexForm').attr('action', action);
        })
    });
<?php $this->Html->scriptEnd(); ?>

<?php echo $this->Html->script('/app/SalesOrders/customer_report.js?v=0.0.2', array('block' => 'pageBlock')); ?>

<?php function ang($str) {
    $str = str_replace('%7B%7B', '{{', $str);
    $str = str_replace('%7D%7D', '}}', $str);
    return $str;
}?>