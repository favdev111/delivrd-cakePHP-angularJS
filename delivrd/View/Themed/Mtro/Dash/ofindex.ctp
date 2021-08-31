<?php #$this->AjaxValidation->active(); ?>

<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <h3 class="page-title" id="Welcome">
            <?php echo Configure::read('OperatorName') ?> Inventory Management and Order Fulfillment
        </h3>

        <?php #echo $this->element('sql_dump');?>

        <?php echo $this->element('expirytext'); ?>

        <?php echo $this->Session->flash(); ?>
            
            <!-- END PAGE HEADER-->
            <!-- BEGIN DASHBOARD STATS -->
            <?php if($orstep == 'prod') {?>

                <style>
                    .modal {
                      text-align: center;
                      padding: 0!important;
                    }

                    .modal:before {
                      content: '';
                      display: inline-block;
                      height: 100%;
                      vertical-align: middle;
                      margin-right: -4px;
                    }

                    .modal-dialog {
                      display: inline-block;
                      text-align: left;
                      vertical-align: middle;
                    }

                    .modal-backdrop, .modal-backdrop.fade.in {
                        opacity: 0.7;
                        filter: alpha(opacity=20);
                    }
                    components-md.css:4183
                    .modal-backdrop, .modal-backdrop.fade.in {
                        background-color: #000 !important;
                    }

                </style>
            
                <a href="#" data-toggle="modal" data-target="#buttons-pop" id="button"></a>
            <?php } else if($orstep == 'inv'){ ?>   
            <blockquote>
                <p> You have created <?php echo $productscount ?> product(s) in <?php echo Configure::read('OperatorName') ?>, it's time to <a href='<?php echo Router::url(array('controller' => 'inventories', 'action' => 'index'), true); ?>'><span class="btn blue"><i class="fa fa-barcode"></i>update their inventory quantities.</span></a></p>
            </blockquote>
                <?php } else if($orstep == 'ordl'){ ?>  
            <blockquote>
                <p>You have created <?php echo $productscount ?> product(s) in <?php echo Configure::read('OperatorName') ?> and updated their inventory, you are ready for your next steps.</p>
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
            <?php } else { ?> 
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat green-haze">
                        <div class="visual">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 <?php echo $orderscount['C'] ?>
                            </div>
                            <div class="desc">
                                 Sales Orders To Fulfill
                            </div>
                        </div>
                        <?php if($_authUser['User']['role'] == 'trial' || $_authUser['User']['role'] == 'paid') { ?>
                            <a class="more" href="<?php echo $this->Html->url(['controller' => 'salesorders', 'action'=>'index', 'status_id'=>2 ]); ?>">
                                View more <i class="m-icon-swapright m-icon-white"></i>
                            </a>
                        <?php } else { ?>
                            <?php echo $this->Html->link(
                                    __('View more <i class="m-icon-swapright m-icon-white"></i>'),
                                    array('plugin' => false, 'controller' => 'user', 'action' => 'start_trial', '?' => ['type' => 'so']),
                                    array('class' => 'more', 'escape' => false, 'data-toggle' => 'modal', 'data-target' => '#ajaxModal')
                                );
                            ?>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat red-intense">
                        <div class="visual">
                            <i class="fa fa-bar-chart-o"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 <?php echo $orderscount['S'] ?>
                            </div>
                            <div class="desc">
                                 Shipments Awaiting Pick-Up
                            </div>
                        </div>
                        <?php if($_authUser['User']['role'] == 'trial' || $_authUser['User']['role'] == 'paid') { ?>
                        <?php echo $this->Html->link(__('View more <i class="m-icon-swapright m-icon-white"></i>'), array('plugin' => false, 'controller' => 'shipments', 'action' => 'index', 'index' => 1, 'status_id' => 8), array('class' => 'more', 'escape' => false)); ?>
                        <?php } else { ?>
                            <?php echo $this->Html->link(
                                    __('View more <i class="m-icon-swapright m-icon-white"></i>'),
                                    array('plugin' => false, 'controller' => 'user', 'action' => 'start_trial'),
                                    array('class' => 'more', 'escape' => false, 'data-toggle' => 'modal', 'data-target' => '#ajaxModal')
                                );
                            ?>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat blue">
                        <div class="visual">
                            <i class="fa fa-globe"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 <?php echo $orderscount['P'] ?>
                            </div>
                            <div class="desc">
                                Overdue Purchase Orders
                            </div>
                        </div>
                        <?php if($_authUser['User']['role'] == 'trial' || $_authUser['User']['role'] == 'paid') { ?>
                        <?php echo $this->Html->link(__('View more <i class="m-icon-swapright m-icon-white"></i>'), array('plugin' => false,'controller'=> 'replorders','action' => 'index', '?' => array('dash' => 'true', 'created' => 1, 'page' => 1, 'limit' => 10, 'status_id' => 2, 'sortby' => 'Order.modified', 'sortdir' => 'DESC')), array('class' => 'more', 'escape' => false)); ?>
                        <?php } else { ?>
                            <?php echo $this->Html->link(
                                    __('View more <i class="m-icon-swapright m-icon-white"></i>'),
                                    array('plugin' => false, 'controller' => 'user', 'action' => 'start_trial', '?' => ['type' => 'po']),
                                    array('class' => 'more', 'escape' => false, 'data-toggle' => 'modal', 'data-target' => '#ajaxModal')
                                );
                            ?>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat purple-plum">
                        <div class="visual">
                            <i class="fa fa-globe"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 <?php echo $unique_pdts; ?>
                            </div>
                            <div class="desc">
                                 Inventory Alerts
                            </div>
                        </div>
                         <?php echo $this->Html->link('View more <i class="m-icon-swapright m-icon-white"></i>', array('plugin' => false, 'controller' => 'inventories', 'action' => 'unique_pdts'), array('escape' => false,'class'=>'more')); ?>
                        
                    </div>
                </div>
            </div>
            <!-- END DASHBOARD STATS -->
            <div class="clearfix">
            </div>
            <!-- <div class="row">                  
                <div class="col-md-12 col-sm-12">
                    
                    <div class="portlet light bg-inverse">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-share font-red-sunglo"></i>
                                <span class="caption-subject font-red-sunglo ">Revenue</span>
                                <span class="caption-helper">Hourly stats...</span>
                            </div>
                            <div class="actions">
                                <div class="btn-group pull-right">
                                
                                </div>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div id="site_activities_loading">
                                <img src="../../assets/admin/layout/img/loading.gif" alt="loading"/>
                            </div>
                            <div id="site_activities_content" class="display-none">
                                <div id="site_activities" style="height: 228px;">
                                </div>
                            </div>
                            
                        </div>
                    </div>
                
                </div>
            </div> -->
            <div class="clearfix">
            </div>
            <div class="row">                   
                <div class="col-md-6 col-sm-6">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-trophy font-grey"></i> Top 10 Sellers - Products
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-scrollable">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Quantity</th>
                                            <th>Value (<?php echo h($this->Session->read('currencyname')); ?>)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($topsellers as $key=>$topsellers):
                                        echo "<tr>";
                                        echo "<td>".($key+1)."</td>";
                                        echo "<td>".$topsellers['product_name']."</td>";
                                        echo "<td>".$topsellers['quantity']."</td>";
                                        echo "<td>".$topsellers['total_line']."</td>";                          
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
                                <i class="fa fa-trophy font-grey"></i> Sales By City - Top 10
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-scrollable">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>No. Of Orders</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $x=1;
                                        foreach ($salesbycity as $key=>$value):
                                            if($key !== 0) {
                                                echo "<tr>";
                                                echo "<td>".$x."</td>";
                                                echo "<td>".$key."</td>";
                                                echo "<td>".$value."</td>";                     
                                                echo "</tr>";
                                                $x++;
                                            }
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
    <div class="clearfix"></div>
        </div>
    </div>
</div>
</div>
<div class="modal fade modal-opacity" id="buttons-pop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="basic" aria-hidden="true" style="display: none;">
    <div class="modal-dialog new-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Welcome to Delivrd!</h4>
                <h5 class="modal-title">The first step in setting up Delivrd is to create your first products.</h5>
            </div>

            <div class="modal-body">
            <div id="response-bin"></div>
                <div class="row static-info">
                    <div class="col-md-6 value">
                    <a href="#" data-toggle="modal" data-target="#add-new-pdt" id="pdt-manually"><span class="btn blue-steel"><i class="fa fa-barcode"></i>create products manually</span></a>
                    <!-- <?php echo $this->Html->link(__('<span class="btn blue-steel"><i class="fa fa-barcode"></i> create products manually</span>'), array('plugin' => false, 'controller'=> 'products','action' => 'addproduct'),array('escape'=> false)); ?> -->
                    </div>
                    <div class="col-md-6 value">
                    <?php /*<a href="#" data-toggle="modal" data-target="#import-pdt" id="import-csv"><span class="btn blue-steel"><i class="fa fa-file-excel-o"></i> import products from csv file</span></a>*/ ?>
                    <a href="<?php echo $this->Html->url(['controller' => 'products', 'action' => 'add_products_csv']);?>"><span class="btn blue-steel"><i class="fa fa-file-excel-o"></i> import products from csv file</span></a>
                    
                    <!-- <?php echo $this->Html->link(__('<span class="btn blue-steel"><i class="fa fa-file-excel-o"></i> import products from csv file</span>'), array('plugin' => false, 'controller'=> 'products','action' => 'uploadcsv'),array('escape'=> false)); ?> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-opacity" id="add-new-pdt" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add product and inventory</h4>
            </div>

            <?php echo $this->Form->create('Product', array('url' => array('controller' => 'products', 'action' => 'addproduct'), 'class' => 'form-horizontal', 'id' => 'add-product')); ?>
            <div class="modal-body">
                <div id="response-bin"></div>
                <div class="alert alert-danger hide" id="productErrors"></div>
                <div class="row static-info">
                    <div class="col-md-6 value">
                     <label class="control-label">Product Name: <span class="required">* </span></label>
                        <a href="javascript:void(0)" data-toggle="popover" data-placement="top" data-content="Name of your product."><span class="btn-group pull-right"><i class="icon-question"></i></span></a>
                        <?php  echo $this->Form->input('name',array( 'label' => false, 'class' => 'form-control input-sm' )); ?>
                    </div>
                    <div class="col-md-6 value">
                        <label class="control-label">SKU: <span class="required">* </span></label>
                        <a href="javascript:void(0)" data-toggle="popover" data-placement="top" data-content="Uniquely identifies the product. Also, the SKU should be printed on barcode labels that will be scanned in Delivrd."><span class="btn-group pull-right"><i class="icon-question"></i></span></a>
                        <?php  echo $this->Form->input('sku',array( 'label' => false, 'class' => 'form-control input-sm', 'required')); ?>
                    </div>
                </div>

                <div class="row static-info">
                    <div class="col-md-6 value">
                        <label class="control-label">Stock Quantity: <span class="required">* </span></label>
                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" data-content="Current quantity of inventory."><span class="btn-group pull-right"><i class="icon-question"></i></span></a>
                        <?php  echo $this->Form->input('stock_quantity',array( 'label' => false, 'class' => 'form-control input-sm', 'required', 'type' => 'number', 'min' => 0, 'step'=>1)); ?>
                    </div>
                    <div class="col-md-6 value">
                        <label class="control-label">Reorder Point: <span class="required">* </span></label>
                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" data-content="When stock falls below this level, alerts are displayed in low inventory alerts monitor."><span class="btn-group pull-right"><i class="icon-question"></i></span></a>
                        <?php  echo $this->Form->input('reorder_point',array( 'label' => false, 'class' => 'form-control input-sm', 'required')); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row static-info">
                    <div class="col-md-12">
                        <button class="btn blue saveBtn" name="bttnsubmit" type="submit" id="product">Save and add another</button>
                        <button class="btn blue saveBtn" name="bttnsubmit" type="submit" id="stock">Save and update stock</button>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div> 

<div class="modal fade modal-opacity" id="import-pdt" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Upload your products data file in csv format</h4>
            </div>

          <!--   <?php echo $this->Form->create('Product', array('url' => array('controller' => 'products', 'action' => 'addproduct'), 'class' => 'form-horizontal', 'id' => 'add-product')); ?> -->
            <div class="modal-body">
                <div id="response-bin"></div>
              <div class="row">
                    <div class="col-md-12">
                        <blockquote>
                            <p style="font-size:16px">
                                 Upload your products data file in csv format
                            </p>
                            <p style="font-size:16px">
                                <?php
                                 echo $this->Html->link('<i class="fa fa-cloud-download"></i> Download Sample File','/products/downloadsamplefile',array('class' => 'btn blue-hoki fileinput-button','escape'=> false));
                                ?>                         
                            </p>
                            
                        </blockquote>
                        <br>
                        <?php echo $this->Form->create('Product', array('url' => array('controller' => 'products', 'action' => 'uploadcsv'), 'class' => 'form-horizontal', 'id' => 'fileupload', 'enctype' => 'multipart/form-data')); ?>
                        <!-- <form id="fileupload" action="/products/uploadcsv" method="POST" enctype="multipart/form-data"> -->
                            <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                            <div class="row fileupload-buttonbar">
                                <div class="col-lg-10">
                                    <!-- The fileinput-button span is used to style the file input field as button -->
                                    <span class="btn green fileinput-button">
                                    <i class="fa fa-plus"></i>
                                    <span>
                                    Add file... </span>
                                    <input type="file" accept=".csv" name="uploadedfile">
                                    </span>
                                    <button type="submit" class="btn blue start">
                                    <i class="fa fa-upload"></i>
                                    <span>
                                    Start upload </span>
                                    </button>
                                    <button type="reset" class="btn warning cancel">
                                    <i class="fa fa-ban-circle"></i>
                                    <span>
                                    Cancel upload </span>
                                    </button>
                                    <!-- The global file processing state -->
                                    <span class="fileupload-process">
                                    </span>
                                </div>
                                <!-- The global progress information -->
                                <div class="col-lg-5 fileupload-progress fade">
                                    <!-- The global progress bar -->
                                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar progress-bar-success" style="width:0%;">
                                        </div>
                                    </div>
                                    <!-- The extended global progress information -->
                                    <div class="progress-extended">
                                         &nbsp;
                                    </div>
                                </div>
                            </div>
                            <!-- The table listing the files available for upload/download -->
                            <table role="presentation" class="table table-striped clearfix">
                            <tbody class="files">
                            </tbody>
                            </table>
                        <?php echo $this->Form->end(); ?>
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h3 class="panel-title">Important!</h3>
                            </div>
                            <div class="panel-body">
                                <ul>
                                    <li>
                                         Maximum file size for uploads is <strong>100 KB.</strong>
                                    </li>
                                    <li>
                                         You can upload only CSV files.
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            
            </div>
            <?php #echo $this->Form->end(); ?>
        </div>
        
    </div>
</div> 

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger label label-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn blue start" disabled>
                    <i class="fa fa-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn red cancel">
                    <i class="fa fa-ban"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>

<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-download fade">
                <td>
                    <span class="preview">
                        {% if (file.thumbnailUrl) { %}
                            <a href="{%=file.url%}" title="{%=file.orgname%}" download="{%=file.orgname%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                        {% } %}
                    </span>
                </td>
                <td>
                    <p class="name">
                        {% if (file.url) { %}
                            <a href="{%=file.url%}" title="{%=file.orgname%}" download="{%=file.orgname%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.orgname%}</a>
                        {% } else { %}
                            <span>{%=file.orgname%}</span>
                        {% } %}
                    </p>
                    {% if (file.error) { %}
                        <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                    {% } %}
                </td>
                <td>
                    <span class="size">{%=o.formatFileSize(file.size)%}</span>
                </td>
                <td>
                
                        <a href="/products/importcsv/{%=file.name%}" class="btn blue"><i class="fa fa-barcode"></i>Create Products</a>
                   
                </td>
               
            </tr>
        {% } %}
</script>