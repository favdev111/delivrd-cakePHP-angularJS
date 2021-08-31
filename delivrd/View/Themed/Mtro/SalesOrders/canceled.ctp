<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="SalesOrderList as sol">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-shopping-cart"></i> Canceled Sales Orders
                        </div>
                        <div class="actions">
                            <?php if($is_write) { ?>
                            <?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Add Sales Order'), array('plugin' => false, 'controller' => 'salesorders', 'action' => 'create'), array('class' => 'btn default yellow-stripe add-delivrd', 'escape' => false, 'title' => 'New Order')); ?>
                            
                            <div class="btn-group">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php if($this->Session->read('showebaylink') == true) { ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-cloud-download"></i> Import Ebay Orders'), array('controller' => 'ebay','action' => 'getEbayOrders'),array('escape'=> false)); ?></li>
                                    <?php } ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-shopping-cart"></i> Sales Orders List'), array('controller' => 'salesorders', 'action' => 'index'), array('escape' => false)); ?></li>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if($is_have_access) { ?>
                    <div class="portlet-body">
                        <div class="csv-div">
                            <div class="btn-toolbar">
                                <div class="btn-group pageLimit">
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <label><i class="fa fa-list"></i> Results:</label>
                                            <?php echo $this->Form->select('pageBottom', $options, array(
                                                'value'=>$limit,
                                                'default' => 10,
                                                'empty' => false,
                                                'class'=>'form-control form-filter input-md limit',
                                                'ng-change' => 'applySearch()',
                                                'ng-model' => 'limit',
                                                'select2' => ''
                                                )
                                            ); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <a href class="csv-icons import-btn" title="Show all records" ng-click="$event.preventDefault(); showAllCanceled();"><i class="fa fa-undo"></i> Show All</a>
                                </div>
                            </div>
                        </div>

                        <?php echo $this->Form->create('Order', array('class' => 'form-horizontal', 'id'=>'OrderIndexForm', 'ng-submit' => 'applySearch()')); ?>
                            <?php echo $this->Form->hidden('status_id', ['ng-model' => "status_id", 'value' => 50, 'ng-change' => 'applySearch()']); ?>
                            <div class="row margin-bottom-20">
                                <div class="col-md-5">
                                    <div class="input-group col-md-10">
                                        <?php echo $this->Form->input('searchby', array(
                                            'label' => false,
                                            'class'=>'form-control input-md',
                                            'custom-autofocus' => 'autofocus',
                                            'placeholder' => 'Search by Order #, Ref. # or Cust. name',
                                            'ng-change' => 'applySearch()',
                                            'ng-model' => "searchby"
                                        )); ?>  
                                        <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                    </div>
                                </div>

                                <label class="col-md-1 control-label">Filter By: </label>
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
                                    <button class="btn btn-md blue filter-submit margin-bottom" type="button" ng-click="applySearch()"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        <?php echo $this->Form->end(); ?>

                        <div id="multiFunctions" class="row margin-bottom-20 hide">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-fit-height dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-v"></i> With selected 
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-lock"></i> Release Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); releaseMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Create Wave For Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); waveAddMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-ban"></i> Cancel Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); cancelMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-trash"></i> Delete Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); trashMultiple();')); ?></li>
                                </ul>
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="table table-hover">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th width="15px"><input type="checkbox" name="selAll" id="selAll" ng-click="checkAll()"></th>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Sales Channel</th>
                                        <th>Reference Order</th>
                                        <th>Created</th>
                                        <th>Status</th>
                                        <th width="140px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="line in orders | orderBy:sortType:sortReverse" id="line_{{line.Order.id}}">
                                        <td><input type="checkbox" class="so_checkboxes" name="data[Order][id][]" value="{{line.Order.id}}" ng-click="checkOrder(line.Order.id)"></td>
                                        <td>{{line.Order.id}}</td>
                                        <td>{{line.Order.ship_to_customerid}}</td>
                                        <td ng-bind-html="schannel(line)">
                                            {{schannel(line)}} 
                                        </td>
                                        <td>{{line.Order.external_orderid}}</td>
                                        <td>{{line.Order.created}}</td>
                                        <td ng-bind-html="status(line.Order.status_id)">{{status(line.Order.status_id)}}</td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a class="dropdown-toggle delivrd-act" href="#" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </a>
                                                <ul class="dropdown-menu pull-right">
                                                    <li><?php echo ang($this->Html->link('<i class="fa fa-search"></i> Details', array('controller'=>'salesorders', 'action' => 'details', '{{line.Order.id}}'), array('escape'=>false))); ?></li>
                                                    <li><?php echo ang($this->Html->link('<i class="fa fa-check"></i> Restore', array('controller'=>'salesorders', 'action' => 'restore', '{{line.Order.id}}'), array('escape'=>false))); ?></li>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr ng-show="$data.length == 0">
                                        <td colspan="8"> 
                                            <h3 class="text-warning text-center">There's no orders found</h3>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <pagination max-size="10" total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="limit"></pagination>
                    <?php } else { ?>
                    <div class="portlet-body">
                        <div class="alert alert-danger">
                            <p class="lead text-center">
                                You do not have access to sales orders.
                                <?php if(!$authUser['is_limited']) { ?>
                                If you need to manage sales orders, enable 'Order Fulfillment' from 
                                <?php echo $this->Html->link('Settings', ['controller'=>'users', 'action'=>'edit']); ?>
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <!-- End: life time stats -->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var prev = 10;
    var popup = 0;
    var is_write = '<?php echo $is_write ?>';
    var networks = <?php echo json_encode($networks) ?>;
    var ship_networks = <?php echo json_encode($ship_networks) ?>;

    var productId = 0;

    var status_id = 50;
    var schannel_id = '<?php echo (isset($this->params['named']['schannel_id'])?$this->params['named']['schannel_id']:''); ?>';
    var searchby = '<?php echo (isset($this->params['named']['searchby'])?$this->params['named']['searchby']:''); ?>';
    var limit = '<?php echo (isset($this->params['named']['limit'])?$this->params['named']['searchby']:'10'); ?>';
    var userUid = '<?php echo $authUser['id']; ?>';

    $(document).ready(function() {
        $('#OrderSchannelId').select2({
            placeholder: 'Select Channel',
            minimumResultsForSearch: -1
        });

        $('select.limit').select2({
            minimumResultsForSearch: -1,
            width: '80px'
        });
    });
<?php $this->Html->scriptEnd(); ?>

<?php echo $this->Html->script('/app/SalesOrders/index.js?v=0.0.6', array('block' => 'pageBlock')); ?>

<?php function ang($str) {
    $str = str_replace('%7B%7B', '{{', $str);
    $str = str_replace('%7D%7D', '}}', $str);
    return $str;
}?>