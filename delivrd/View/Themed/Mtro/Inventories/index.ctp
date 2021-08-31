<?php
    $paid = null;
    $action_color = ($locationsactive == 1 ? 'green' : 'grey-salt'); // returns true
?>
<style>
#flashMessage {
    display:none;
}
</style>

    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <?php /*<h4><?php echo $inventories[0]['Inventory']['modified']; ?></h4>
            <h4><?php echo $this->Admin->localTime("%Y-%m-%d %H:%M:%S", strtotime($inventories[0]['Inventory']['modified'])); ?></h4>
            <h4><?php echo $this->Session->read('timezone'); ?></h4>*/ ?>

            <?php echo $this->element('expirytext'); ?>

            <?php if($_authUser['User']['showtours'] == 1) { ?>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa graduation-cap"></i>Inventory Page Tour
                    </div>
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
                    <!-- Begin: life time stats -->
                    <div class="portlet box delivrd">
                        <div class="portlet-title"  style="background:#eee;">
                            <div class="caption">
                                <i class="fa fa-barcode"></i> Inventory List
                            </div>

                            <?php if($is_write) { ?>
                            <div class="actions">
                                <?php if($locationsactive) { ?>
                                    <?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Add Inventory Location'),
                                        array('controller'=> 'locations','action' => 'index'),
                                        array('class' => 'btn default yellow-stripe add-delivrd', 'escape'=> false));
                                    ?>
                                <?php } ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-fit-height <?php echo $action_color; ?> dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true" id>
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>
                                    <?php if($locationsactive) { ?>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <li><?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Add Inventory Record'), array('controller'=> 'inventories','action' => 'add'), array('escape'=> false, 'class' => 'statlink', 'data-link' => 'new_inventory')); ?><li>
                                            <?php if(!$authUser['is_limited']) { ?>
                                            <li><?php echo $this->Html->link(__('<i class="fa fa-at"></i> Manage Locations'), array('controller'=> 'warehouses','action' => 'index'),array('escape'=> false)); ?></li>
                                            <li><?php echo $this->Html->link(__('<i class="fa fa-bell"></i> Low Inventory Alerts'), array('controller'=> 'invalerts','action' => 'index'),array('escape'=> false)); ?></li>
                                            <?php } ?>
                                            <li><?php echo $this->html->link('<i class="fa fa-envelope"></i> Send Inventory Alerts', array('plugin' => false, 'controller' => 'Inventories', 'action' => 'invenotry_alert'), array('escape' => false)); ?></li>
                                            <li><?php echo $this->Html->link(__('<i class="fa fa-print"></i> Print Inventory List'), array('controller'=> 'inventories','action' => 'printInventory'),array('escape'=> false)); ?></li>
                                            <?php if(!$authUser['is_limited']) { ?>
                                            <li><?php echo $this->html->link('<i class="fa fa-history"></i> Inventory Transactions Report', array('plugin' => false, 'controller' => 'inventories', 'action' => 'transactions_history'), array('escape' => false)); ?></li>
                                            <li><?php echo $this->html->link('<i class="fa fa-money"></i> Stock Valuation Report', array('plugin' => false, 'controller' => 'inventories', 'action' => 'vlreport'), array('escape' => false)); ?></li>
                                            <?php } ?>
                                            <li><?php echo $this->Html->link(__('<i class="icon-grid"></i> Serial Checkout'), array('controller'=> 'inventories','action' => 'serial_checkout'), array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'rel' => 'modal-lg')); ?><li>
                                        </ul>
                                     <?php } ?>
                                </div>
                            </div>
                            <?php } ?>

                        </div>

                        <div class="portlet-body">
                            <div class="csv-div">
                                <div class="btn-toolbar">
                                    <div class="btn-group">
                                        <?php echo $this->Html->link(__('<i class="fa fa-download"></i> Export'), array('controller'=> 'inventories','action' => 'export'),array('escape'=> false, 'class' => 'csv-icons', 'title' => 'Export inventory list', 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'rel' => 'modal-lg')); ?>
                                        <?php echo $this->Html->link(__('<i class="fa fa-upload"></i> Import'), array('controller'=> 'inventories','action' => 'uploadcsv'),array('escape'=> false, 'class' => 'csv-icons import-btn', 'title' => 'Import inventory from csv')); ?>
                                    </div>
                                    
                                    <div class="btn-group pageLimit">
                                        <div class="form-inline">
                                            <div class="form-group">
                                                <label><i class="fa fa-list"></i> Results:</label>
                                                <?php echo $this->Form->select('pageBottom', $options, array(
                                                    'value' => $limit,
                                                    'default' => 10,
                                                    'empty' => false,
                                                    'class'=>'form-control form-filter input-md limit'
                                                    )
                                                ); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <?php echo $this->html->link('<i class="fa fa-undo"></i> Show All', array('plugin' => false, 'controller' => 'inventories', 'action' => 'index'), array('id' => 'clear', 'class' => 'csv-icons import-btn', 'escape' => false, 'title' => 'Show all records')); ?>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <?php echo $this->Form->create('Inventory', array(
                                    'class' => 'form-horizontal',
                                    'id' => 'inv-search',
                                    'url' => array_merge(array('controller'=>'inventories', 'action' => 'index'), $this->params['pass']),
                            )); ?>
                                <?php echo $this->Form->hidden('product', array('id' => 'product', 'type'=>'text', 'value' => $product['product_id'])); ?>
                                <?php echo $this->Form->hidden('limit', ['value' => $limit]); ?>

                                <div class="row margin-bottom-20">
                                    <div class="col-md-5">
                                        <div class="input-group col-md-10">
                                            <?php echo $this->Form->input('searchby', array('label' => false, 'class'=>'code-scan form-control input-md', 'value' => $this->request->query('searchby'), 'placeholder' => 'Enter or scan SKU/EAN/UPC/Serial or product name', 'id' => 'product_auto', 'autofocus' => 'autofocus')); ?>
                                            <span class="input-group-addon"><button id="keysearch" class="" title="Search" style="border:none"><i class="fa fa-search"></i></button></span>
                                        </div>
                                        <?php if(!empty($product['name'])) : ?>
                                            <h5 style="font-size: 17px;color:#333333;font-family: -webkit-body;">Search results for <?php echo h($product['name']); ?></h5>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($locationsactive) { ?>
                                    <label class="col-md-1 control-label">Filter By: </label>
                                    <div class="col-md-3">
                                        <?php echo $this->Form->input('warehouse_id', array('label' => false,'class'=>'form-control form-filter input', 'id' => 'warehouse_select', 'required' => false, 'value' => $this->request->query('warehouse_id'), 'empty' => 'Location...')); ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            <?php echo $this->Form->end(); ?>

                            <div class="table-container">
                                <div class="table-actions-wrapper">
                                    <span></span>
                                    <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                </div>
                                <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th >Image</th>
                                            <th><i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('Product.sku', 'SKU') ?></th>
                                            <th><i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('Product.name', 'Product Name') ?></th>
                                            <?php if($supplier_count == 1) { ?>
                                            <th>Supplier</th>
                                            <?php } ?>

                                            <?php if ($locationsactive) { ?>
                                            <th>Location</th>
                                            <?php } ?>

                                            <th>Quantity.</th>
                                            <?php if($is_virtual_fields) { ?>
                                                <th>Virtual Qty.</th>
                                            <?php } ?>

                                            <?php if ($_authUser['User']['managedamaged'] == 1) { ?>
                                            <th>Damg. Qty.</th>
                                            <?php } ?>

                                            <?php if ($_authUser['User']['showvariants'] == 1) { ?>
                                            <th>Color</th>
                                            <th>Size</th>
                                            <?php } ?>

                                            <?php if(count($inventories) == 1 || $count_pdt <= 5) {
                                                echo '<th>Inventory Transactions</th>';
                                            } else {
                                                echo '<th>Last Trans.</th>'; 
                                            } ?>
                                            <th id="more-actions">Actions</th>
                                        </tr>
                                    </thead>
                                <tbody>

                                <?php if(count($inventories) !== 0) { ?>
                                    <?php foreach ($inventories as $inventory) { ?>
                                <tr>
                                    <td><img src="<?php echo h($inventory['Product']['imageurl']); ?>" class="productImage" rel="product_img" data-id="<?php echo $inventory['Product']['id']; ?>" height='32px' width='32px' alt="product"></td>
                                    <td><?php echo h($inventory['Product']['sku']); ?></td>
                                    <td><?php echo $this->element('product_name', array('name' => $inventory['Product']['name'], 'id' => $inventory['Product']['id'])); ?></td>
                                    <?php if($supplier_count == 1) { ?>
                                    <?php echo $this->element('supplier', array('listOne' => $inventory['Product']['Productsupplier'], 'id' => $inventory['Product']['id'])); ?>
                                    <?php } ?>

                                    <?php if ($locationsactive) { ?>
                                    <td id="td-location">
                                        <?php echo ($warehouses_list[$inventory['Inventory']['warehouse_id']]); ?>
                                        <?php if($inventory['NetworksAccess']['access']) { ?>
                                        <sup class="text-muted">[<?php echo h($inventory['NetworksAccess']['access']); ?>]</sup>
                                        <?php } ?>
                                    </td>
                                    <?php } ?>

                                    <td>
                                        <?php if($inventory['Product']['uom'] == 'Kit') { ?>
                                            <?php if($authUser['kit_component_issue'] == 'build') { ?>
                                            <?php echo $this->Html->link(h($inventory['Inventory']['quantity']), array('controller'=> 'inventories','action' => 'assemble', $inventory['Inventory']['id']),array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'rel' => 'modal-lg', 'style' => 'width:110px;', 'class' => 'btn '. $this->Product->inv_status($inventory))); ?>
                                            <?php } else { ?>
                                            <button class="btn <?php echo $this->Product->inv_status($inventory); ?>" style="width:110px;"><?php echo $inventory['Inventory']['quantity'];?></button>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php if($inventory['Inventory']['user_id'] == $this->Session->read('Auth.User.id') || in_array($inventory['NetworksAccess']['access'], ['w', 'rw'])) { ?>
                                            <input
                                                type="text"
                                                id="<?php echo $inventory['Inventory']['id']; ?>"
                                                <?php /*onkeypress="return isNumber(event);"*/ ?>
                                                data-inventory="<?php echo $inventory['Inventory']['id']; ?>"
                                                data-value="<?php echo $inventory['Inventory']['quantity'];?>"
                                                data-sku="<?php echo h($inventory['Product']['sku']);?>"
                                                style="width:110px;"
                                                data-id="<?php echo $inventory['Product']['id']; ?>"
                                                value="<?php echo $inventory['Inventory']['quantity']; ?>"
                                                class="btn <?php echo $this->Product->inv_status($inventory); ?> edit-inventory-quantity"
                                            >
                                            <a href="#" class="editQuantityPen" style="color:#6e7a89" data-inventory="<?php echo $inventory['Inventory']['id']; ?>"><i class="fa fa-pencil"></i></a>
                                            <?php } else { ?>
                                            <button class="btn <?php echo $this->Product->inv_status($inventory); ?>" style="width:110px;"><?php echo $inventory['Inventory']['quantity'];?></button>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                    
                                    <?php if($is_virtual_fields) { ?>
                                    <td>
                                        <?php if($inventory['Product']['uom'] == 'Kit') { ?>
                                            <?php echo $inventory['Inventory']['virtual_quantity']; ?>
                                        <?php } else { ?>
                                            n/a
                                        <?php } ?>
                                    </td>
                                    <?php } ?>

                                    <?php
                                    if ($_authUser['User']['managedamaged'] == 1) { ?>
                                        <td>
                                        <?php if(isset($inventory['Inventory']['damaged_qty'])) { ?>
                                            <?php echo h($inventory['Inventory']['damaged_qty']); ?>
                                        <?php } else { ?>
                                            0
                                        <?php } ?>
                                        </td>
                                    <?php } ?>

                                    <?php if ($_authUser['User']['showvariants'] == 1) { ?>
                                    <td>
                                        <?php if(!empty($inventory['Product']['color_id'])) { ?>
                                        <li class="list-group-item" style="color:#<?php echo h($inventory['Product']['Color']['htmlcode']); ?>;background-color:#<?php echo h($inventory['Product']['Color']['htmlcode']); ?>">
                                            <span class="badge"><?php echo h($inventory['Product']['Color']['name']); ?></span>
                                        </li>
                                        <?php } ?>
                                    </td>
                                    <td><?php if(!empty($inventory['Product']['size_id']))  echo h($inventory['Product']['Size']['name']); ?></td>
                                    <?php } ?>

                                    <td class="text-center">
                                        <?php if(count($inventories) == 1 || $count_pdt <= 5) { ?>
                                            <?php if($inventory['Inventory']['user_id'] == $this->Session->read('Auth.User.id') || in_array($inventory['NetworksAccess']['access'], ['w', 'rw'])) { ?>
                                            <div class="row" id="ri-stock">
                                                <div class="col-md-12 text-center">

                                                    <?php if(!$serial_no) { ?>
                                                        <?php if($this->Session->read('inventoryremarks') != 1) { ?>
                                                            <a href="#" data-toggle="modal" data-target="#receive-issue" id="plus-one" data-id="<?php echo $inventory['Inventory']['id']; ?>" data-quantity="<?php echo $inventory['Inventory']['quantity']; ?>" data-warehouse="<?php echo $inventory['Inventory']['warehouse_id']; ?>" class="btn btn-sm blue filter-submit margin-bottom fast-inventory">+1</a>
                                                            <a href="#" data-toggle="modal" data-target="#receive-issue" id="minus-one" data-id="<?php echo $inventory['Inventory']['id']; ?>" data-quantity="<?php echo $inventory['Inventory']['quantity']; ?>" data-warehouse="<?php echo $inventory['Inventory']['warehouse_id']; ?>" class="btn btn-sm blue filter-submit margin-bottom fast-inventory">-1</a>
                                                        <?php } else { ?>
                                                            <a href="javascript:void(0)" id="no-remark-plus" data-id="<?php echo $inventory['Inventory']['id']; ?>" data-quantity="<?php echo $inventory['Inventory']['quantity']; ?>" data-getqty="1" data-warehouse="<?php echo $inventory['Inventory']['warehouse_id']; ?>" class="btn btn-sm blue filter-submit margin-bottom fast-inventory">+1</a>
                                                            <a href="javascript:void(0)" id="no-remark-minus" data-id="<?php echo $inventory['Inventory']['id']; ?>" data-quantity="<?php echo $inventory['Inventory']['quantity']; ?>" data-getqty="1" data-warehouse="<?php echo $inventory['Inventory']['warehouse_id']; ?>" class="btn btn-sm blue filter-submit margin-bottom fast-inventory">-1</a>
                                                        <?php } ?>
                                                        <a href="#" data-toggle="modal" data-target="#receive-issue" id="receive-btn" data-id="<?php echo $inventory['Inventory']['id']; ?>" data-quantity="<?php echo $inventory['Inventory']['quantity']; ?>" data-warehouse="<?php echo $inventory['Inventory']['warehouse_id']; ?>" class="btn btn-sm blue filter-submit margin-bottom fast-inventory">Receive</a>
                                                        <a href="#" data-toggle="modal" id="issue-btn" data-target="#receive-issue" data-id="<?php echo $inventory['Inventory']['id']; ?>" data-quantity="<?php echo $inventory['Inventory']['quantity']; ?>" data-warehouse="<?php echo $inventory['Inventory']['warehouse_id']; ?>" class="btn btn-sm blue filter-submit margin-bottom fast-inventory">Issue</a>
                                                    <?php } ?>

                                                    <?php if($serial_no) { ?>
                                                        <a href="#" data-toggle="modal" data-target="#issue-transfer" id="minus-one" class="btn btn-sm blue filter-submit margin-bottom">Issue Serial</a>
                                                        <a href="#" data-toggle="modal" data-target="#serial-transfer" id="minus-one" class="btn btn-sm blue filter-submit margin-bottom">Transfer Serial</a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                                <?php echo h($inventory['Inventory']['modified']); ?>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php echo $this->Admin->localTime("%Y-%m-%d %H:%M:%S", strtotime($inventory['Inventory']['modified'])); ?>
                                        <?php } ?>
                                    </td>

                                    <?php if(!$paid) $paid = "pa".$inventory['Inventory']['id']; ?>
                                    <td id='<?php echo $paid ?>'>
                                        <div class="btn-group">
                                            <a class="dropdown-toggle delivrd-act" href="#" data-toggle="dropdown">
                                                <i class="fa fa-ellipsis-h"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right">
                                                <?php if($inventory['Product']['uom'] == 'Kit') { ?>
                                                    <?php if($authUser['kit_component_issue'] == 'build') { ?>
                                                        <li><?php echo $this->Html->link(__('<i class="fa fa-compress"></i> Assemble Kit'), array('controller'=> 'inventories','action' => 'assemble', $inventory['Inventory']['id']),array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'rel' => 'modal-lg')); ?></li>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?php if($inventory['Inventory']['user_id'] == $this->Session->read('Auth.User.id') || in_array($inventory['NetworksAccess']['access'], ['w', 'rw'])) { ?>
                                                        <li><?php echo $this->Html->link(__('<i class="fa fa-clipboard"></i> Count'), array('controller'=> 'inventories','action' => 'count', $inventory['Inventory']['id']),array('escape'=> false)); ?></li>
                                                        <li><?php echo $this->Html->link(__('<i class="fa fa-exchange"></i> Issue/Receive'), array('controller'=> 'inventories','action' => 'grgi',$inventory['Inventory']['id']),array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'rel' => 'modal-lg')); ?></li>
                                                    <?php } ?>
                                                    <?php if($locationsactive && ($inventory['Inventory']['user_id'] == $this->Session->read('Auth.User.id') || in_array($inventory['NetworksAccess']['access'], ['w', 'rw']))) { ?>
                                                        <li><?php echo $this->Html->link(__('<i class="fa fa-ship"></i> Location Transfer'), array('controller'=> 'Inventories','action' => 'transfer',$inventory['Inventory']['id']),array('escape'=> false)); ?></li>
                                                        <?php if($inventory['Inventory']['user_id'] == $this->Session->read('Auth.User.id')) { ?>
                                                        <li><?php echo $this->Html->link(__('<i class="fa fa-bell" aria-hidden="true"></i> Inventory Alert'), array('controller'=> 'invalerts', 'action' => 'create', $inventory['Inventory']['product_id'], $inventory['Inventory']['warehouse_id']), array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal')); ?></li>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                                
                                                
                                                <li><?php echo $this->Html->link(__('<i class="fa fa-search"></i>View Product Details'), array('controller'=> 'products','action' => 'view',$inventory['Inventory']['product_id']),array('escape'=> false)); ?></li>
                                                <li><?php echo $this->Html->link(__('<i class="fa fa-history"></i> Transaction History'), array('controller'=> 'inventories','action' => 'transactions_history', $inventory['Inventory']['product_id']),array('escape'=> false)); ?></li>
                                                <?php if($locationsactive && ($inventory['Inventory']['user_id'] == $this->Session->read('Auth.User.id') || in_array($inventory['NetworksAccess']['access'], ['w', 'rw']))) { ?>
                                                    <li><?php echo $this->Html->link(__('<i class="fa fa-barcode"></i> Add Inventory Record'), array('controller'=> 'inventories','action' => 'add', $inventory['Inventory']['product_id']),array('escape'=> false)); ?></li>
                                                    <?php if($inventory['Inventory']['user_id'] == $authUser['id']) { ?>
                                                    <li><?php echo $this->Html->link(__('<i class="fa fa-trash-o"></i> Delete Inventory Record'), array('controller'=> 'inventories', 'action' => 'delete', $inventory['Inventory']['id']), array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'rel' => 'modal-md')); ?></li>
                                                    <?php } ?>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                                <?php } ?>
                                <?php } else { ?>
                                    <tr><td align='center' colspan='8'><b>No Data Found</b></td></tr>
                                <?php } ?>
                                </tbody>
                            </table>

                            <p><?php echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'))); ?></p>
                            <div class="btn-toolbar">
                                <div class="btn-group">
                                    <ul class="pagination">
                                    <?php
                                    $paginator = $this->Paginator;
                                    echo $paginator->first("First",array('tag' => 'li'));
                                        if($paginator->hasPrev()){
                                            echo $paginator->prev("Prev", array('tag' => 'li'));
                                        }
                                        echo $paginator->numbers(array('modulus' => 6,'tag' => 'li','currentTag' => 'a','currentClass' => 'active','separator' => false));
                                        if($paginator->hasNext()){
                                            echo $paginator->next("Next",array('tag' => 'li'));
                                        }
                                        echo $paginator->last("Last",array('tag' => 'li'));
                                    ?>
                                        <li></li>
                                    </ul>
                                </div>
                                <div class="btn-group pageLimit">
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <label><i class="fa fa-list"></i> Results:</label>
                                            <?php echo $this->Form->select('pageBottom', $options, array(
                                                'value'=>$limit,
                                                'default' => 10,
                                                'empty' => false,
                                                'class'=>'form-control form-filter input-md limit'
                                                )
                                            ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="bs-callout bs-callout-info">
                                        <span class="font-blue-steel help">
                                            <i class="fa fa-info" aria-hidden="true"></i>
                                            <a href="https://delivrd.freshdesk.com/support/solutions/articles/17000091137-simple-inventory-transactions-in-delivrd" target="_blank">Simple Inventory Transactions in Delivrd</a>
                                            <span class="separator">|</span>
                                            <a href="https://delivrd.freshdesk.com/support/solutions/articles/17000014151-multi-location-inventory-management-in-delivrd" target="_blank">Multi-Location Inventory Management in Delivrd</a>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- End: life time stats -->
                </div>

                <?php #echo $this->element('sql_dump');?>
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
        <h4>Search</h4></br>
        <p>Enter or scan a label with a barcode of an SKU/EAN/UPC/serial number of a product, or start typing the product's name or SKU/EAN/UPC/serial.</p>
    </li>
    <?php if(sizeof($inventories) > 0) { ?>
    <li data-id="<?php echo $paid ?>" data-options="tipLocation:left" data-button="Close">
        <h4>Do more</h4></br>
        <p>You can do more from here:</p>
        <p>Transfer inventory between locations</p>
        <p>View transactions history</p>
        <p>and more...</p>
    </li>
    <?php } ?>
</ol>

<ol id="newRideTipContent" style="display:none;">
    <li data-id="clicksearch" data-button="Next" data-options="tipLocation:right">
        <h4>Search for a product by barcode scanning</h4></br>
        <p>Enter or scan a label with a barcode of an SKU/EAN/UPC/serial number of a product, or start typing the product's name or SKU/EAN/UPC/serial.
        </p>
    </li>
    <li data-id="ri-stock" data-button="Next" data-options="tipLocation:top">
        <h4>Receive or Issue stock</h4></br>
        <p>To receive or issue inventory, click :</p>
        <p>+1 to recieve (add) one piece to stock</p>
        <p>-1 to issue (deduct) one piece of stock</p>
        <p>Receive to receive any quantity to stock</p>
        <p>Issue to issue any quantity from stock</p>
    </li>
    <li data-id="more-actions" data-button="Next" data-options="tipLocation:left">
        <h4>Do more</h4></br>
        <p>You can do more from here:</p>
        <p>Transfer inventory between locations</p>
        <p>View transactions history</p>
        <p>and more...</p>
    </li>
</ol>

<div class="modal fade" id="receive-issue" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="modal-title"></h4>
            </div>

            <?php echo $this->Form->create('Inventory', array('url' => array('controller' => 'inventories', 'action' => 'create'), 'class' => 'form-horizontal', 'id' => 'receive-form')); ?>
            <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Location'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                    <?php echo $this->Form->hidden('id',array('value' => '','id' => 'inventory-id'));
                     echo $this->Form->hidden('ttype',array('value' => '', 'id' => 'inventory-ttype')); ?>
                    <?php echo $this->Form->input('warehouse_id',array('value' => '','label' => false, 'class' => 'form-control','div' =>false , 'id' => 'dmq','readonly' => true, 'disabled')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Available Qty.'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo "<button type='button' class='btn btn-default available-qty' id='available-qty'></button>" ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3" id="modal-acc"><?php echo __('Quantity'); ?></label>
                    <div class="col-md-8">
                    <?php echo $this->Form->input('tquantity',array('label' => false, 'class' => 'form-control','div' =>false, 'id' => 'tqty','min' => '0','type' => 'number', 'required' => 'true')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Remarks'); ?></label>
                    <div class="col-md-8">
                   <?php echo $this->Form->input('comments',array('label' => false, 'class' => 'form-control','div' =>false , 'id' => 'cremarks')); ?>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary saveBtn">Save</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="issue-transfer" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Issue</h4>
            </div>

            <?php echo $this->Form->create('Inventory', array('url' => array('controller' => 'inventories', 'action' => 'issueTransfer'), 'class' => 'form-horizontal', 'id' => 'issue-transfer-form'));
            echo $this->Form->hidden('id',array('value' => $inventory_id));
            echo $this->Form->hidden('serial_no',array('value' => $serial_no['Serial']['serialnumber'])); ?>
            <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Serial Number'); ?></label>
                    <div class="col-md-8">
                        <?php echo "<button type='button' class='btn btn-default' id='available-qty'>".(($serial_no) ? $serial_no['Serial']['serialnumber'] : '')."</button>" ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Remarks'); ?></label>
                    <div class="col-md-8">
                   <?php echo $this->Form->input('comments',array('label' => false, 'class' => 'form-control','div' =>false , 'id' => 'cremarks')); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary saveBtn">Save</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="serial-transfer" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Transfer</h4>
            </div>

            <?php echo $this->Form->create('Inventory', array('url' => array('controller' => 'inventories', 'action' => 'serialTransfer'), 'class' => 'form-horizontal', 'id' => 'serial-transfer-form'));
            echo $this->Form->hidden('id',array('value' => $inventory_id));
            echo $this->Form->hidden('serial_no',array('value' => $serial_no['Serial']['serialnumber'])); ?>
            <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Serial Number'); ?></label>
                    <div class="col-md-8">
                        <?php echo "<button type='button' class='btn btn-default' id='available-qty'>".(($serial_no) ? $serial_no['Serial']['serialnumber'] : '')."</button>" ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Location'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('warehouse_id',array('label' => false, 'class' => 'form-control','div' =>false , 'id' => 'dmq', 'options' => $locations)); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Remarks'); ?></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('comments',array('label' => false, 'class' => 'form-control','div' =>false , 'id' => 'cremarks')); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary saveBtn">Save</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    $(function() {
        var alertMessage = $( "div#flashMessage" ).text();
        var classDiv = $( "div#flashMessage" ).attr('class');

        if(alertMessage != '') {
            var classDiv = $( "div#flashMessage" ).attr('class');
            if(classDiv == 'alert alert-success')
                toastr["info"](alertMessage);
            else
                toastr["error"](alertMessage);
        }

        $('.modal').on('shown.bs.modal', function() {
            $(this).find('[autofocus]').focus();
        });
    });

    $('#warehouse_select').select2({
        minimumResultsForSearch: -1,
        placeholder: "Select Location"
    });

    //alert(moment().tz('<?php echo $inventories[0]['Inventory']['modified']; ?>', "America/Los_Angeles").format());

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
        minLength: 0,
        select: function( event, ui ) {
            if(ui.item) {
                
                if(ui.item.product_id !== undefined) {
                    $("#product").val(ui.item.product_id);

                    // Send activity
                    $.ajax({
                        url: siteUrl + 'user/add_activity/',
                        dataType: "json",
                        method: 'POST',
                        data: {
                            product_id: ui.item.product_id
                        }
                    });
                }
                if(ui.item.warehouse_id !== undefined) { // if serialnumber
                    $("#warehouse_select").val(ui.item.warehouse_id);
                }
                $("#product_auto").val('');
                $('#inv-search').submit();
                return ui.item.label;
            }
        },
    }).click(function() {
        $(this).autocomplete("search", $(this).val());
    });

    $('.editQuantityPen').click(function(){
        var inventory_id = $(this).attr("data-inventory");
        $("#" + inventory_id).removeClass("btn");
        $("#" + inventory_id).val('').focus();
        return false;
    })

    $('#product_auto').on('keyup', function (e) {
        if (e.keyCode == 13) {
            $('#inv-search').submit();
        }
    });

    $('#keysearch').click(function() {
        $("#product").val('');
        $('#inv-search').submit();
        return false;
    });

    $("#warehouse_select").change(function(){
        $('#inv-search').submit();
    });

    $('.limit').select2({
        minimumResultsForSearch: -1,
        width: '80px'
    });

    $('.limit').change(function(){
        $('#InventoryLimit').val($(this).val());
        $('#inv-search').submit();
    });

<?php $this->Html->scriptEnd(); ?>
</script>