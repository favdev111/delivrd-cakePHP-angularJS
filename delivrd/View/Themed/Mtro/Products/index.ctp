<?php $pasku = null;?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="ProductList">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>

        <?php if($this->Session->read('showtours') == 1) { ?>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa graduation-cap"></i>Products Page Tour</div>
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
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-barcode"></i>
                            <?php if($status) { ?>
                            Blocked
                            <?php } ?>
                            Products List
                        </div>

                        <div class="actions">
                            <?php if(!$authUser['is_limited']) { ?>
                            <?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Add Product'),
                                array('controller'=> 'products','action' => 'add'),
                                array('class' => 'btn default yellow-stripe add-delivrd statlink', 'escape'=> false, 'data-link' => 'new_product'));
                            ?>
                            <?php } ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php if(!$authUser['is_limited']) { ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-list" aria-hidden="true"></i> Custom Fields'), array('controller'=> 'fields','action' => 'index'),array('escape'=> false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-delicious"></i> Manage Colors'), array('controller'=> 'colors','action' => 'index'),array('escape'=> false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-rss"></i> Manage Sizes'), array('controller'=> 'sizes','action' => 'index'),array('escape'=> false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-sitemap"></i> Manage Categories'), array('controller'=> 'categories','action' => 'index'),array('escape'=> false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-upload"></i> Products Importer'), array('action' => 'add_products_csv'),array('escape'=> false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-upload"></i> Import forecast from csv'), array('action' => 'upload_fc_csv'),array('escape'=> false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-link"></i> Products - Supplier Assignment'), array('controller'=> 'productsuppliers','action' => 'index'),array('escape'=> false)); ?></li>
                                    <li><?php echo $this->html->link(__('<i class="fa fa-money"></i> Stock Valuation Report'), array('plugin' => false, 'controller' => 'inventories', 'action' => 'vlreport'), array('escape' => false)); ?></li>
                                    <?php } ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-ban"></i> Blocked Products'), array('controller'=> 'products','action' => 'index', 1),array('escape'=> false)); ?></li>
                                    <li role="separator" class="divider"></li>
                                    <li><?php echo $this->html->link(__('<i class="fa fa-download"></i> Export Product Stock'), array('plugin' => false, 'controller' => 'products', 'action' => 'export_stock'), array('escape' => false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-file-pdf-o"></i> Generate PDF'), array('controller'=> 'products','action' => 'pdf_view'),array('escape'=> false)); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="csv-div">
                            <div class="btn-toolbar">
                                <div class="btn-group">
                                    <?php echo $this->Html->link(__('<i class="fa fa-download"></i> Export'), array('controller'=> 'products','action' => 'exportcsv'),array('escape'=> false, 'class' => 'csv-icons')); ?>
                                    <?php echo $this->Html->link(__('<i class="fa fa-upload"></i> Import'), array('controller'=> 'products','action' => 'add_products_csv'),array('escape'=> false, 'class' => 'csv-icons import-btn')); ?>
                                    <?php echo $this->Html->link(__('<i class="fa fa-columns"></i> Fields'), array('controller'=> 'products','action' => 'list_fields'),array('escape'=> false, 'class' => 'csv-icons import-btn', 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'rel' => 'modal-lg')); ?>
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
                                    <?php echo $this->html->link('<i class="fa fa-undo"></i> Show All', array('plugin' => false, 'controller' => 'products'), array('id' => 'clear', 'class' => 'csv-icons import-btn', 'escape' => false, 'title' => 'Show all records')); ?>
                                </div>
                            </div>
                        </div>

                        <div clsass="container" id="filterBox">
                            <div class="row margin-bottom-20">
                                <div class="col-sm-12">
                                    <?php echo $this->Form->create('Product', array(
                                            'class' => 'form-horizontal',
                                            'novalidate' => true,
                                            'url' => ['controller' => 'products', 'action' => 'index'],
                                            'id' => 'prod-search',
                                        ));
                                    ?>
                                    
                                    <?php echo $this->Form->hidden('limit', ['value' => $limit]); ?>
                                    <div class="row">
                                        
                                        <div class="col-md-5">
                                            <div class="input-group col-md-12">
                                                <?php echo $this->Form->input('searchby', array('label' => false, 'class'=>'code-scan form-control', 'placeholder' => 'Enter or scan SKU/EAN/UPC/Serial or product name', 'id' => 'product_auto', 'value' => $filter['searchby']['text'])); ?>
                                                <span class="input-group-addon"><button id="keysearch" class="" title="Search" style="border:none"><i class="fa fa-search"></i></button></span>
                                            </div>
                                        </div>
                                        
                                        <div class="btn-toolbar">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" id="filterDropdown">Filter By <i class="fa fa-angle-down"></i></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li>
                                                        <div class="row" style="padding: 10px 20px;min-width: 360px">
                                                            <select class="form-control form-filter" id="filters">
                                                                <option value="">Select a Attribute</option>
                                                                <option value="categories">Categories</option>
                                                                <option value="group">Group</option>
                                                                <option value="color">Color</option>
                                                                <option value="size">Size</option>
                                                                <?php foreach ($fields as $field) { ?>
                                                                    <option value="field_<?php echo $field['Field']['id']; ?>"><?php echo $field['Field']['name']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <div class="filtersInput" id="categoriesFilter">
                                                                <?php echo $this->Form->input('category', array('label' => false, 'class'=>'form-control filterSelect', 'required' => false, 'empty' => 'Select..')); ?>
                                                            </div>
                                                            <div class="filtersInput" id="groupFilter">
                                                                <?php echo $this->Form->input('group', array('label' => false,'class'=>'form-control filterSelect', 'required' => false, 'empty' => 'Select..')); ?>
                                                            </div>
                                                            <div class="filtersInput" id="colorFilter">
                                                                <?php echo $this->Form->input('color', array('label' => false, 'class'=>'form-control filterSelect', 'required' => false, 'empty' => 'Select..')); ?>
                                                            </div>
                                                            <div class="filtersInput" id="sizeFilter">
                                                                <?php echo $this->Form->input('size', array('label' => false, 'class'=>'form-control filterSelect', 'required' => false, 'empty' => 'Select..')); ?>
                                                            </div>
                                                            <?php foreach ($fields as $field) { ?>
                                                            <div class="filtersInput" id="field_<?php echo $field['Field']['id']; ?>Filter">
                                                                <?php if($field['FieldsValue']) { ?>
                                                                    <?php $options = []; ?>
                                                                    <?php foreach ($field['FieldsValue'] as $option) { ?>
                                                                        <?php $options[$option['id']] = $option['value']; ?>
                                                                    <?php } ?>
                                                                <?php echo $this->Form->input('field_'. $field['Field']['id'] , array('label' => false, 'options' => $options, 'class'=>'form-control filterSelect', 'required' => false, 'empty' => 'Select..')); ?>
                                                                <?php } else { ?>
                                                                    <div class="input-group">
                                                                        <?php echo $this->Form->input('field_'. $field['Field']['id'] , array('aria-expanded'=>"false", 'label' => false, 'class'=>'form-control inputSelect', 'required' => false)); ?>
                                                                        <span class="input-group-addon"><button id="keysearch" class="" title="Search" style="border:none"><i class="fa fa-search"></i></button></span>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>

                                            <?php /*<div class="btn-group">
                                                <button class="btn blue"><i class="fa fa-search"></i></button>
                                            </div>*/ ?>
                                        </div>
                                    </div>

                                    <div class="filtersDisplay">
                                        
                                        <span class="badge badge-success" id="productFilterDisplay">
                                            Search results for <span class="ftext"></span>
                                            <?php echo $this->Form->hidden('product', array('id' => 'product', 'type'=>'text')); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span >&times;</span></button>
                                        </span>

                                        <span class="badge badge-success" id="categoriesFilterDisplay">
                                            Catergory: <span class="ftext"></span>
                                            <?php echo $this->Form->hidden('category_id'); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span >&times;</span></button>
                                        </span>

                                        <span class="badge badge-success" id="groupFilterDisplay">
                                            Group: <span class="ftext"></span>
                                            <?php echo $this->Form->hidden('group_id'); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span >&times;</span></button>
                                        </span>

                                        <span class="badge badge-success" id="colorFilterDisplay">
                                            Color: <span class="ftext"></span>
                                            <?php echo $this->Form->hidden('color_id'); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span >&times;</span></button>
                                        </span>

                                        <span class="badge badge-success" id="sizeFilterDisplay">
                                            Size: <span class="ftext"></span>
                                            <?php echo $this->Form->hidden('size_id'); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span >&times;</span></button>
                                        </span>
                                        <?php foreach ($fields as $field) { ?>
                                        <span class="badge badge-success" id="field_<?php echo $field['Field']['id']; ?>FilterDisplay">
                                            <?php echo $field['Field']['name']; ?>: <span class="ftext"></span>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span >&times;</span></button>
                                        </span>
                                        <?php } ?>
                                    </div>
                                    <?php  echo $this->Form->end(); ?>
                                </div>
                            </div>
                        </div>

                        <div id="multiFunctions" class="row margin-bottom-30 hide">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-fit-height dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-v"></i> With selected 
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <?php if($status) { ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-unlock"></i> Cancel Block'), array(), array('escape'=> false, 'id' => 'unBlockMultiple')); ?></li>
                                    <?php } else { ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-shield"></i> Block Selected Products'), array(), array('escape'=> false, 'id' => 'blockMultiple')); ?></li>
                                    <?php } ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-trash"></i> Delete Selected Products'), array(), array('escape'=> false, 'id' => 'trashProducts')); ?></li>
                                </ul>
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="15px"><input type="checkbox" name="selAll" id="selAll"></th>
                                    <th> Image </th>
                                    <th> <i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('sku', 'SKU') ?> </th>
                                    <th> <i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('name', 'Product Name') ?> </th>
                                    <th>Category</th>
                                    <th>Product Inventory</th>
                                    
                                    <?php if(isset($settings['product_list']['fields'])) { ?>
                                        <?php foreach($settings['product_list']['fields'] as $field_name => $field_key) { ?>
                                            <th><?php echo $field_name; ?></th>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if(isset($settings['product_list']['custom'])) { ?>
                                        <?php foreach ($settings['product_list']['custom'] as $field_id => $field_name) { ?>
                                           <th><?php echo $field_name; ?></th>
                                        <?php } ?>
                                    <?php } ?>
                                    <th width="40px"> Actions </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $key => $product) { ?>
                                <tr role="row">
                                    <td><?php echo $this->Form->checkbox('Product.id.' . $key, array('class' => 'checkboxes', 'value' => $product['Product']['id'], 'hiddenField' => false)); ?></td>
                                    <td><img class="productImage" rel="product_img" data-id="<?php echo $product['Product']['id']; ?>" src="<?php echo h($product['Product']['imageurl']); ?>" height="32px" width="32px"></td>
                                    <td><?php echo h($product['Product']['sku']); ?></td>
                                    <td><?php echo $this->element('product_name', array('name' => $product['Product']['name'], 'id' => $product['Product']['id'])); ?></td>
                                    <td>
                                        <?php if($product['Product']['user_id'] != $authUser['id']) { ?>
                                        <?php echo h($networks[$product['Product']['user_id']]); ?> <i class="fa fa-angle-right"></i>
                                        <?php } ?>
                                        <?php echo h($product['Category']['name']); ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $this->Html->url(['controller'=>'inventories', 'action'=>'index', '?'=>array('product' => $product['Product']['id'])]); ?>">
                                            <?php $this->Product->locCount($product); ?>
                                        </a>
                                    </td>
                                    
                                    <?php if(isset($settings['product_list']['fields'])) { ?>
                                        <?php foreach($settings['product_list']['fields'] as $field_name => $field_key) { ?>
                                            <?php $fkeys = explode('.', $field_key); ?>
                                            <td><?php echo $product[$fkeys[0]][$fkeys[1]]; ?></td>
                                        <?php } ?>
                                    <?php } ?>

                                    <?php if(isset($settings['product_list']['custom'])) { ?>
                                        <?php foreach ($settings['product_list']['custom'] as $field_id => $field_name) { ?>
                                           <td>
                                                <?php if(!empty($custom[$product['Product']['id']]['FieldsData'][$field_id])) { ?>
                                                    <?php if(count($custom_values[$field_id]) && isset($custom_values[$field_id][$custom[$product['Product']['id']]['FieldsData'][$field_id]])) { ?>
                                                        <?php echo h($custom_values[$field_id][$custom[$product['Product']['id']]['FieldsData'][$field_id]]); ?>
                                                    <?php } else { ?>
                                                        <?php echo h($custom[$product['Product']['id']]['FieldsData'][$field_id]); ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </td>
                                        <?php } ?>
                                    <?php } ?>

                                    <?php if(!$pasku) $pasku = "pa".$product['Product']['id']; ?>
                                    <td id="<?php echo $pasku?>">
                                        <div class="btn-group">
                                            <a class="dropdown-toggle delivrd-act" href="#" data-toggle="dropdown">
                                                <i class="fa fa-ellipsis-h"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right" role="menu">
                                                <li><?php echo $this->Html->link(__('<i class="fa fa-search"></i>  View'), array('controller'=> 'Products','action' => 'view','product_id' => $product['Product']['id']),array('escape'=> false)); ?></li>
                                                <?php if($product['Product']['user_id'] == $authUser['id']) { ?>
                                                <li><?php echo $this->Html->link(__('<i class="fa fa-pencil"></i> Edit'), array('controller'=> 'Products','action' => 'edit','product_id' => $product['Product']['id']),array('escape'=> false)); ?></li>
                                                <?php } ?>
                                                <li><?php echo $this->Html->link(__('<i class="fa fa-history"></i> Transactions History'), array('controller'=> 'inventories','action' => 'transactions_history', $product['Product']['id']),array('escape'=> false)); ?></li>
                                                <li><?php echo $this->Html->link(__('<i class="fa fa-flag"></i> ATP'), array('controller'=> 'products','action' => 'atp', $product['Product']['id']),array('escape'=> false)); ?></li>
                                                <?php if($product['Product']['user_id'] == $authUser['id']) { ?>
                                                    <?php if($product['Product']['status_id'] == 13 || $product['Product']['status_id'] == 12) { ?>
                                                    <li><?php echo $this->Form->postLink(__('<i class="fa fa-unlock"></i> Cancel Block'), array('action' => 'changestatus', $product['Product']['id'],1),array('escape'=> false)); ?></li>
                                                    <?php } else { ?>
                                                    <li><?php echo $this->Form->postLink(__('<i class="fa fa-shield"></i> Complete Block'), array('action' => 'changestatus', $product['Product']['id'],13),array('escape'=> false)); ?></li>
                                                    <?php } ?>
                                                    <li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $product['Product']['id']),array('escape'=> false), __('Are you sure you want to delete %s?', $product['Product']['name'])); ?></li>
                                                <?php } ?>
                                                <li>
                                                    <a href ng-click="addDocument(<?php echo $product['Product']['id']; ?>, '<?php echo $product['Product']['sku']; ?>')"><i class="fa fa-upload"></i> Documents</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
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
                                        echo $paginator->numbers(array('modulus' => 2,'tag' => 'li','currentTag' => 'a','currentClass' => 'active','separator' => false));
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
                                            <a href="https://delivrd.freshdesk.com/support/solutions/articles/17000013480-managing-product-data-in-delivrd" target="_blank">Managing Products in Delivrd Tutorials</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<!-- Tip Content -->
<ol id="joyRideTipContent">
    <li data-id="actiondd" data-text="Next" class="custom" data-options="tipLocation:right">
        <h2>Actions</h2>
        <p>From the Action button you can perform product-related activities, such as craete product colors, sizes, or import products.</p>
    </li>
    <li data-id="newprod" data-button="Next" data-options="tipLocation:right;tipAnimation:fade">
        <h2>New Product</h2>
        <p>Click button to create a new product</p>
    </li>
    <li data-id="toolsm" data-button="Next" data-options="tipLocation:right">
        <h2>Tools Menu</h2>
        <p>Allows you to perform actions on all products, such as export to CSV file etc.</p>
    </li>
    <li data-id="srch" data-button="Next" data-options="tipLocation:top">
        <h2>Search Fields</h2>
        <p>Input or select your search criteria - by SKU, product name, category etc.</p>
    </li>
    <li data-id="clicksearch" data-button="Next" data-options="tipLocation:left">
        <h2>Search</h2>
        <p>Click button to perform search.</p>
    </li>
    <?php if($pasku) { ?>
    <li data-id="<?php echo $pasku ?>" data-options="tipLocation:left" data-button="Close">
        <h2>Actions</h2>
        <p>Click the Actions button to perform product specific actions: view product, edit product, view transaction history</p>
    </li>
    <?php } ?>
</ol>

<?php echo $this->Form->create('Product', array(
        'type' => 'post',
        'id' => 'multi_form',
        'url' => array('action' => 'index'),
        'class' => 'form-horizontal list_data_form',
        'novalidate' => true,
    ));
    echo $this->Form->hidden('product_id',array( 'label' => false, 'id' => 'selected_ids'));
    echo $this->Form->end();
?>

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    
    var filter = <?php echo json_encode($filter); ?>;
    var doc_title = 'Product SKU: ';

    $('#ProductCategory').select2({
        minimumResultsForSearch: -1,
    });
    $('#ProductGroup').select2({
        minimumResultsForSearch: -1
    });
    $('#ProductColor').select2({
        minimumResultsForSearch: -1
    });
    $('#ProductSize').select2({
        minimumResultsForSearch: -1
    });

    <?php foreach ($fields as $field) { ?>
        <?php if($field['FieldsValue']) { ?>
        $('#ProductField<?php echo $field['Field']['id']; ?>').select2({
            minimumResultsForSearch: -1
        });
        <?php } ?>
    <?php } ?>

    $('#filters').select2({
        minimumResultsForSearch: -1
    });

    $('.inputSelect').click(function(e) {
        e.stopPropagation();
    });

    $('.filtersInput').hide();

    $('.filtersDisplay .badge').hide();
    $.each(filter, function(key, value) {
        if(key != 'searchby') {
            if( value.name != '') {
                $('#'+key+'FilterDisplay').show();
                $('#'+key+'FilterDisplay').find('span.ftext').html(value.name);
                $('#'+key+'FilterDisplay').find('input').val(value.id);
            }
        }
    });

    $('#filters').change(function() {
        $('.filtersInput').hide();
        if($(this).val() != '') {
            $('#'+$(this).val()+'Filter').show();
        }
    });

    $('.filterSelect').change(function() {
        var field = $('#filters').val();
        if($(this).val() != '') {
            $('#'+field+'FilterDisplay').find('span.ftext').html($("option:selected", this).text());
            $('#'+field+'FilterDisplay').find('input').val($(this).val());
            $('#'+field+'FilterDisplay').show();
        }
        $('.filtersInput').hide();
        $('#filters').val('').trigger('change');
        $('#filterDropdown').dropdown('toggle')
        $('#prod-search').submit();
    });

    $('.filtersDisplay .close').click(function() {
        var badge = $(this).parents('span.badge');
        badge.find('input').val('');
        badge.find('span.ftext').html('');
        badge.hide();
        $('#prod-search').submit();
    });

    $('.limit').select2({
        minimumResultsForSearch: -1,
        width: '80px'
    });

    $('.limit').change(function(){
        $('#ProductLimit').val($(this).val());
        $('#prod-search').submit();
    });

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
                    $("#ProductCategoryId").val('');
                
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
                $("#product_auto").val('');
                $('#prod-search').submit();
                return ui.item.label;
            }
        },
    }).focus(function() {
        $(this).autocomplete("search", $(this).val());
    });

    $('#product_auto').on('keyup', function (e) {
        if (e.keyCode == 13) {
            $('#prod-search').submit();
        }
    });

    $("#ProductCategoryId").change(function() {
        $('#prod-search').submit();
        return false;
    });

    $("#keysearch").click(function() {
        $("#product").val('');
        $('#prod-search').submit();
        return false;
    });

    $('#unBlockMultiple').click(function(e) {
        e.preventDefault();
        var checkedVals = $('.checkboxes:checkbox:checked').map(function() {
            return this.value;
        }).get();

        if(checkedVals == '') {
            toastr.error('Please select products', "", {tapToDismiss: false, closeButton:true, closeHtml: '<button><i class="fa fa-close"></i></button>',timeOut: false,})
        } else {
            $("#selected_ids").val(checkedVals.join(","));
            $('#multi_form').attr('action', '<?php echo $this->Html->url(['controller' => 'products', 'action' => 'block_multiple', 1]); ?>').submit();
        }
    });

    $('#blockMultiple').click(function(e) {
        e.preventDefault();
        var checkedVals = $('.checkboxes:checkbox:checked').map(function() {
            return this.value;
        }).get();

        if(checkedVals == '') {
            toastr.error('Please select products', "", {tapToDismiss: false, closeButton:true, closeHtml: '<button><i class="fa fa-close"></i></button>',timeOut: false,})
        } else {
            $("#selected_ids").val(checkedVals.join(","));
            $('#multi_form').attr('action', '<?php echo $this->Html->url(['controller' => 'products', 'action' => 'block_multiple', 13]); ?>').submit();
        }
    });

    $('#trashProducts').click(function(e) {
        e.preventDefault();
        var checkedVals = $('.checkboxes:checkbox:checked').map(function() {
            return this.value;
        }).get();

        if(checkedVals == '') {
            toastr.error('Please select products', "", {tapToDismiss: false, closeButton:true, closeHtml: '<button><i class="fa fa-close"></i></button>',timeOut: false,})
        } else {
            $("#selected_ids").val(checkedVals.join(","));
            $('#multi_form').attr('action', '<?php echo $this->Html->url(['controller' => 'products', 'action' => 'delete_multiple' ]); ?>').submit();
        }
    });

    $('#selAll').click(function() {
        if($(this).attr('checked') == 'checked') {
            $('.checkboxes').each(function() {
                $(this).prop('checked', true);
            });
        } else {
            $('.checkboxes').each(function() {
                $(this).prop('checked', false);
            });
        }
        $.uniform.update();

        var checkedVals = $('.checkboxes:checkbox:checked').map(function() {
            return this.value;
        }).get();
        if(checkedVals != '') {
            $('#filterBox').hide();
            $('#multiFunctions').removeClass('hide').show();
        } else {
            $('#filterBox').show();
            $('#multiFunctions').hide();
        }
    });

    $('.checkboxes').click(function() {
        
        var is_all = true;
        $('.checkboxes').each(function() {
            if($(this).attr('checked') != 'checked') {
                is_all = false;
            }
        });
        $('#selAll').prop('checked', is_all);
        $.uniform.update();

        var checkedVals = $('.checkboxes:checkbox:checked').map(function() {
            return this.value;
        }).get();
        if(checkedVals != '') {
            $('#filterBox').hide();
            $('#multiFunctions').removeClass('hide').show();
        } else {
            $('#filterBox').show();
            $('#multiFunctions').hide();
        }
    });

<?php $this->Html->scriptEnd(); ?>
</script>
<?php echo $this->Html->script('/app/Products/index.js?v=0.0.2', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/app/Documents/view.js?v=0.0.2', array('block' => 'pageBlock')); ?>