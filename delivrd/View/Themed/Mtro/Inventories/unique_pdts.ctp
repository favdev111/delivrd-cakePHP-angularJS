<?php
    $this->AjaxValidation->active(); 
    $paid = null; 
    $action_color = ($this->Session->read('locationsactive') == 1 ? 'green' : 'grey-salt'); // returns true
?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="uniquePdts">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>
        <?php if($this->Session->read('showtours') == 1) { ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa graduation-cap"></i>Inventory Page Tour</div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"> </a>
                    <a href="/users/edit" class="config"> </a>
                    <a href="javascript:;" class="reload"> </a>
                    <a href="javascript:;" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="takeTheTour" class="btn btn red dismissable">Take Page Tour</div>  
                If you want to disable page tour, <a href='/users/edit/'><U>change your settings</U></a>.
            </div>
        </div>
        <?php } ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-barcode"></i>Inventory Alert
                        </div>
                    </div>
                    <div class="portlet-body">

                        <div class="csv-div">
                            <div class="btn-toolbar">
                                <div class="btn-group">
                                    <?php echo $this->Html->link(__('<i class="fa fa-download"></i> Export'), array('controller'=> 'inventories','action' => 'exp_unique_pdts'),array('escape'=> false, 'class' => 'csv-icons', 'title' => 'Export')); ?>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="table-container">
                            <div class="table-actions-wrapper">
                                <span></span>
                                <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                            </div>
                            <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th>Image</th>
                                        <th><a ng-click="orderBy('Product.sku')" class="sort-link">SKU <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        <th><a ng-click="orderBy('Product.name')" class="sort-link">Product&nbsp;Name <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        <th><a ng-click="orderBy('Product.safety_stock')" class="sort-link">Safety Stock <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        <th><a ng-click="orderBy('Product.reorder_point')" class="sort-link">Reorder Point <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        <th><a ng-click="orderBy('Inventory.total')" class="sort-link">Total Qty <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="line in alerts | orderBy:sortType:sortReverse">
                                        <td><img src="{{line.Product.imageurl}}" height="32px" width="32px"></td>
                                        <td>{{line.Product.sku}}</td>
                                        <td><?php echo ang($this->Html->link('{{line.Product.name}}', array('controller'=> 'Products','action' => 'view', 'product_id' => '{{line.Product.id}}'), array('escape'=> false))); ?></td>
                                        <td>{{line.Inventory.safety_stock}}</td>
                                        <td>{{line.Product.reorder_point}}</td>
                                        <td><div class="{{inventoryStatus(line)}}">{{line.Inventory.total}}</div></td>
                                    </tr>
                                </tbody>
                            </table>

                            <pagination max-size="10" total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="limit"></pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<ol id="joyRideTipContent">
    <li data-id="toolsm" data-button="Next" data-options="tipLocation:right">
        <h2>Tools Menu</h2>
        <p>Allows you to perform actions on all products inventory, such as export to CSV file etc.</p>
    </li>
    <li data-id="srch" data-button="Next" data-options="tipLocation:top">
        <h2>Search Fields</h2>
        <p>Input or select your search criteria - by SKU or product name</p>
    </li>
    <li data-id="clicksearch" data-button="Next" data-options="tipLocation:left">
        <h2>Search</h2>
        <p>Click button to perform search.</p>
    </li>
    <?php if(sizeof($inventories) > 0) { ?>
    <li data-id="<?php echo $paid ?>" data-options="tipLocation:left" data-button="Close">
        <h2>Actions</h2>
        <p>Click the Actions button to perform product specific actions: inventory count, receive\issue stock, view transaction history etc.</p>
    </li>
    <?php } ?>
</ol>

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var limit = <?php echo $limit; ?>;
<?php $this->Html->scriptEnd(); ?>

<?php echo $this->Html->script('/app/Inventories/unique_pdts.js?v=0.0.2', array('block' => 'pageBlock')); ?>

<?php function ang($str) {
    $str = str_replace('%7B%7B', '{{', $str);
    $str = str_replace('%7D%7D', '}}', $str);
    return $str;
}?>