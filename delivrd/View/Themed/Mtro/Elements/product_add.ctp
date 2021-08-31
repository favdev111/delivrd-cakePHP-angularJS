<?php
$tabFirstError = true;
$tabSecondError = false;
$tabThirdError = false;
$tabFourthError = false;
$tabFifthError = false;
$tabSixError = false;


if ($this->request->is(array('post', 'put'))) {
    $invalidFields = array_keys($errors);
    
    $tabFirstElements = array(
        'name',
        'description',
        'uom',
        'category_id',
        'group_id',
        'Bin',
        'sku',
        'value',
        'reorder_point',
        'imageurl',
    );

    $tabFirstError = (count(array_intersect($invalidFields, $tabFirstElements))) ? true : false;

// Check any validatoin error in tab second
    $tabSecondElements = array(
        'weight',
        'height',
        'width',
        'depth',
    );

    $tabSecondError = (!$tabFirstError && count(array_intersect($invalidFields, $tabSecondElements))) ? true : false;

// Check any validatoin error in tab third
    $tabThirdElements = array(
        'pageurl',
    );

    $tabThirdError = (!$tabFirstError && !$tabSecondError && count(array_intersect($invalidFields, $tabThirdElements))) ? true : false;

// Check any validatoin error in tab Fourth
    $tabFourthElements = array(
        'barcode_standards_id',
        'barcode',
        'safety_stock',
        'bin'
    );

    $tabFourthError = (!$tabFirstError && !$tabSecondError && !$tabThirdError && count(array_intersect($invalidFields, $tabFourthElements))) ? true : false;

// Check any validatoin error in tab Fifth
    $tabFifthElements = array(
        'packaging_material_id',
        'packaging_instructions',
        'consumption',
    );

    $tabFifthError = (!$tabFirstError && !$tabSecondError && !$tabThirdError && !$tabFourthError && count(array_intersect($invalidFields, $tabFifthElements))) ? true : false;

// Check any validatoin error in tab Sixth
    $tabSixElements = array(
        'color_id',
        'size_id',
    );

    $tabSixError = (!$tabFirstError && !$tabSecondError && !$tabThirdError && !$tabFourthError && !$tabFifthError && count(array_intersect($invalidFields, $tabSixElements))) ? true : false;
} 
?>

<?php #$this->AjaxValidation->active(array('block' => 'ajax_validation')); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <?php if ($this->Session->read('showtours') == 1) { ?>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption"><i class="fa graduation-cap"></i>Add Product Page Tour</div>
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
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Form->create('Product', array('class' => 'form-horizontal form-row-seperated', 'novalidate' => true,  'id' => 'add_product_form')); ?>
                <div class="portlet box blue-steel">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-barcode"></i>
                            Add New Product
                        </div>
                        <div class="actions btn-set">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                            <button class="btn green <?php echo ($show_trial?'disabled':''); ?>" type="submit" id="savebtn"><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="tabbable">
                            <ul class="nav nav-tabs">
                                <li class="<?php echo ($tabFirstError) ? 'active' : ''; ?>">
                                    <a href="#tab_basic" data-toggle="tab" id="bastab">Basic</a>
                                </li>
                                <li class="<?php echo ($tabSecondError) ? 'active' : ''; ?>">
                                    <a href="#tab_dimensions" data-toggle="tab" id="dimtab">Dimensions</a>
                                </li>
                                <li class="<?php echo ($tabThirdError) ? 'active' : ''; ?>">
                                    <a href="#tab_urls" data-toggle="tab" id="urltab">URL</a>
                                </li>
                                <li class="<?php echo ($tabFourthError) ? 'active' : ''; ?>">
                                    <a href="#tab_logistics" data-toggle="tab" id="logtab">Logistics</a>
                                </li>
                                <li class="<?php echo ($tabFifthError) ? 'active' : ''; ?>">
                                    <a href="#tab_packaging" data-toggle="tab" id="pactab">Packaging</a>
                                </li>
                                <li class="<?php echo ($tabSixError) ? 'active' : ''; ?>">
                                    <a href="#tab_attributes" data-toggle="tab" id="atrtab">Attributes</a>
                                </li>
                                <li class="<?php echo ($tabSixError) ? 'active' : ''; ?>">
                                    <a href="#tab_custom" data-toggle="tab" id="custtab">Custom Fields</a>
                                </li>
                            </ul>
                            <div class="tab-content no-space">
                                <div class="tab-pane <?php echo ($tabFirstError) ? 'active' : ''; ?>" id="tab_basic">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Name: <span class="required">*</span></label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('name', array('label' => false, 'class' => 'form-control input-large', 'div' => false, 'maxlength' => '50', 'id' => 'maxlength_productname', 'placeholder' => 'Example product name')); ?>
                                                <span class="help-block">Product name, cannot be longer than 50 characters. </span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Description: </label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('description', array('label' => false, 'class' => 'form-control', 'div' => false, 'placeholder' => 'Example of a longer product description')); ?>
                                                <span class="help-block">Product description, cannot be longer than 255 characters.</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">UOM: </label>
                                            <div class="col-md-6">
                                                <?php echo $this->Form->input('uom', array('label' => false, 'class' => 'form-control input-large', 'options' => $uoms, 'div' => false, 'empty' => 'Select...')); ?>
                                                <span class="help-block">Unit Of Measure</span>
                                            </div>
                                        </div>

                                        <div class="form-group hidden" id="kit_block">
                                            <label class="col-md-2 control-label">Kits: </label>
                                            <div class="col-md-10">
                                                <table class="table table-striped table-bordered">
                                                    <tr>
                                                        <th>Component</th>
                                                        <th>Quantity</th>
                                                        <th>Active</th>
                                                        <th width="100px"></th>
                                                    </tr>
                                                    <tr>
                                                        <td class="form-inline">
                                                            <div class="input-group col-md-7">
                                                                <select name="data[Part][parts_id]" ng-model="parts_id" class="form-control select2me" id="KitPartlId" ng-options="part.name for part in parts_a track by part.id">
                                                                    <option value="">Select Product</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <?php echo $this->Form->input('Part.quantity', array('ng-model'=>'quantity', 'placeholder' => 'Quantity', 'type' => 'number', 'class' => 'form-control', 'label' => false, 'div' => false)); ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <?php echo $this->Form->input('Part.active', array('ng-model'=>'active', 'type' => 'checkbox', 'class' => 'form-control', 'label' => false, 'div' => false)); ?>
                                                            </div>
                                                        </td>
                                                        <td><button class="btn btn-info" type="button" ng-click="addKit()">Add</button></td>
                                                    </tr>
                                                    <tr ng-repeat="part in product_parts">
                                                        <td>
                                                            {{partName(part.Kit.parts_id)}}
                                                            <?php echo $this->Form->hidden('Kit.{{$index}}.Kit.parts_id', array('value' => '{{part.Kit.parts_id}}' )); ?>
                                                            <?php echo $this->Form->hidden('Kit.{{$index}}.Kit.quantity', array('value' => '{{part.Kit.quantity}}' )); ?>
                                                            <?php echo $this->Form->hidden('Kit.{{$index}}.Kit.active', array('value' => '{{part.Kit.active}}' )); ?>
                                                        </td>
                                                        <td>{{ part.Kit.quantity }}</td>
                                                        <td>{{ partStatus(part.Kit.active) }}</td>
                                                        <td><button class="btn btn-info btn-xs" type="button" ng-click="removeKit(part.Kit.id)"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Category <span class="required"></span></label>
                                            <div class="col-md-10">
                                                <span class="categoriesDropDown">
                                                    <?php echo $this->Form->input('Product.category_id', array('class' => 'form-control input-large select2me', 'data-placeholder' => 'Select Category', 'label' => false, 'options' => $categories, 'empty' => 'Select...')); ?>
                                                </span>
                                                <a href="#" data-toggle="modal" data-target="#addCategoryForm">
                                                    <span class="btn btn-sm blue-steel"><i class="fa fa-sitemap"></i> Create new category</span>
                                                </a>
                                                <span class="help-block">
                                                    Create your own categories to classify your products.You can search for products by category.
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Group</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('group_id', array('label' => false, 'class' => 'form-control input-large select2me', 'div' => false, 'empty' => 'Select...')); ?>
                                                <span class="help-block">Classifies your products with Delivrd's pre-defined groups. </span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label">SKU: <span class="required">*</span></label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('sku', array('label' => false, 'class' => 'form-control input-large', 'div' => false, 'placeholder' => '165276')); ?>
                                                <span class="help-block">Uniquely identifies a product</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">BIN: </label>
                                            <div class="col-md-10">
                                                <span class="binDropDown">
                                                    <?php echo $this->Form->input('Bin', array('label' => false, 'placeholder' => 'Select Bin', 'class' => 'form-control input-large multiple', 'options' => $bins, 'multiple')); ?>
                                                </span>
                                                <a href="#" data-toggle="modal" data-target="#addBinForm">
                                                    <span class="btn btn-sm blue-steel"><i class="fa fa-list"></i> Create new bin</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Price (<?php echo h($this->Session->read('currencyname')); ?>): </label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('value', array('label' => false, 'class' => 'form-control input-small', 'div' => false, 'placeholder' => '24.3', 'min'=>'0.01', 'step'=>'0.01')); ?>
                                                <span class="help-block">Estimated value of a product. Used for inventory value calculation. </span>
                                            </div>
                                        </div>

                                        <?php if($this->Session->read('paid') == 1) { ?>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Prices for Sales Channels(<?php echo h($this->Session->read('currencyname')); ?>): </label>
                                            <div class="col-md-10">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-striped table-bordered">
                                                            <tr>
                                                                <th width="50%">Sales Channel</th>
                                                                <th>Price</th>
                                                                <th>Action</th>
                                                            </tr>
                                                            <tr>
                                                                <td class="form-inline">
                                                                    <div class="input-group col-md-7">
                                                                        <select name="data[Product][schannel_id]" ng-model="schannel_id" class="form-control " id="ProductSchannelId" ng-options="channel.name for channel in schannels_a track by channel.id">
                                                                            <option value="">Select Channel</option>
                                                                        </select>
                                                                        <?php #echo $this->Form->input('schannel_id', array('options' => $schannels, 'empty'=>'Select Channel', 'ng-model'=>'schannel_id', 'class' => 'form-control', 'label' => false, 'div' => false)); ?>
                                                                    </div>
                                                                    <button class="btn btn-info" type="button" title="Add Sales Channel" ng-click="addSchannel()"><i class="fa fa-plus"></i><span class="hidden-sm hidden-xs hidden-md"> Add Sales Channel</span></button>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <div class="input-group-addon"><?php echo h($this->Session->read('currencyname')); ?></div>
                                                                        <?php echo $this->Form->input('schannel_price', array('ng-model'=>'schannel_price', 'placeholder' => 'Channel Price', 'class' => 'form-control', 'label' => false, 'div' => false)); ?>
                                                                    </div>
                                                                </td>
                                                                <td><button class="btn btn-info" type="button" ng-click="addPrice()">Add</button></td>
                                                            </tr>
                                                            <tr ng-repeat="price in channel_prices">
                                                                <td>
                                                                    {{ schannels[price.ProductsPrices.schannel_id] }}
                                                                    <?php echo $this->Form->hidden('ProductsPrices.{{$index}}.ProductsPrices.schannel_id', array('value' => '{{price.ProductsPrices.schannel_id}}' )); ?>
                                                                    <?php echo $this->Form->hidden('ProductsPrices.{{$index}}.ProductsPrices.value', array('value' => '{{price.ProductsPrices.value}}' )); ?>
                                                                </td>
                                                                <td><?php echo h($this->Session->read('currencyname')); ?> {{ price.ProductsPrices.value }}</td>
                                                                <td><button class="btn btn-info btn-xs" type="button" ng-click="removeKit(price.ProductsPrices.id)"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Reorder Point:</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('reorder_point', array('label' => false, 'class' => 'form-control', 'div' => false, 'placeholder' => '15','min' => '0','type' => 'number','step' => '0.01')); ?>
                                                <span class="help-block">Below this level of stock, the system will display low inventory warnings.</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Image URL: </label>
                                            <div class="col-md-10">
                                                <img src="https://delivrd.com/image_missing.jpg" rel="product_img" data-id="new" data-input="ProductImageurl" style="max-width:128px;max-height:128px" class="productImage">
                                                <?php echo $this->Form->input('imageurl', array('label' => false, 'class' => 'form-control', 'div' => false, 'placeholder' => 'http://www.example.com/images/p1.jpg')); ?>
                                                <span class="help-block">URL of a product image, including http://  </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5" style="text-align: center;">
                                                <button class="btn green  <?php echo ($show_trial?'disabled':''); ?>" type="submit" id="savebtn"><i class="fa fa-check"></i> Save</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane <?php echo ($tabSecondError) ? 'active' : ''; ?>" id="tab_dimensions">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Weight (<?php echo h($this->Session->read('weight_unit')) ?>):</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('weight', array('label' => false, 'class' => 'form-control', 'div' => false, 'placeholder' => '0.25')); ?>
                                                <span class="help-block">Weight of a single piece. </span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Height (<?php echo h($this->Session->read('volume_unit')) ?>):</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('height', array('label' => false, 'class' => 'form-control', 'div' => false, 'placeholder' => '1.6')); ?>
                                                <span class="help-block">Height of a single piece. </span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Width (<?php echo h($this->Session->read('volume_unit')) ?>):</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('width', array('label' => false, 'class' => 'form-control', 'div' => false, 'placeholder' => '0.3')); ?>
                                                <span class="help-block">Width of a single piece. </span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Depth (<?php echo h($this->Session->read('volume_unit')) ?>):</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('depth', array('label' => false, 'class' => 'form-control', 'div' => false, 'placeholder' => '1.8')); ?>
                                                <span class="help-block">Depth of a single piece. </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane <?php echo ($tabThirdError) ? 'active' : ''; ?>" id="tab_urls">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Product Page URL:</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('pageurl', array('label' => false, 'class' => 'form-control', 'div' => false, 'placeholder' => 'http://www.example.com/p1.html')); ?>
                                                <span class="help-block">URL of a product's page. For example, the product's page on Amazon or on eBay, or on your supplier's website.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane <?php echo ($tabFourthError) ? 'active' : ''; ?>" id="tab_logistics">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Barcode System:</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('barcode_standards_id', array('label' => false, 'class' => 'form-control', 'div' => false, 'options' => array('EAN' => 'EAN', 'UPC' => 'UPC', 'ISBN' => 'ISBN'), 'empty' => '(choose one)')); ?>
                                                <span class="help-block">EAN/UPC/ISBN etc. </span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Barcode Number:</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('barcode', array('label' => false, 'class' => 'form-control', 'div' => false, 'maxlength' => '13', 'id' => 'maxlength_productean', 'placeholder' => '4011200296908')); ?>
                                                <span class="help-block">12 or 13 characters. For example, 7290103127459 </span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Issue Inventory Location:</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('issue_location', array('label' => false, 'class' => 'form-control', 'div' => false, 'empty' => 'Select', 'options' => $warehouses)); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Receive Inventory Location:</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('receive_location', array('label' => false, 'class' => 'form-control', 'div' => false, 'empty' => 'Select', 'options' => $warehouses)); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Safety Stock:</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('safety_stock', array('label' => false, 'class' => 'form-control', 'div' => false, 'placeholder' => '15','min' => '0','type' => 'number','step' => '0.01',)); ?>
                                                <span class="help-block">Below this level of stock, system will display low inventory warnings.</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Bin Number:</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('bin', array('label' => false, 'class' => 'form-control', 'div' => false, 'placeholder' => '103')); ?>
                                                <span class="help-block">Number of storage bin that holds this product.</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Sales Forecast:</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('sales_forecast',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Lead Time:</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('lead_time',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane <?php echo ($tabFifthError) ? 'active' : ''; ?>" id="tab_packaging">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Packaging Material:</label>
                                            <div class="col-md-10">
                                                <?php
                                                    if (!isset($packmaterialsarr))
                                                        $packmaterialsarr = "";
                                                    echo $this->Form->input('packaging_material_id', array('label' => false, 'options' => $packmaterialsarr, 'class' => 'form-control', 'div' => false, 'empty' => 'Select...'));
                                                ?>
                                                <span class="help-block">Packaging material that is used when packing this product before the shipment.</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Packaging Instructions:</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('packaging_instructions', array('label' => false, 'class' => 'form-control', 'div' => false, 'placeholder' => 'Pack product in padded envelope. Place Return To label on back of envelope.')); ?>
                                                <span class="help-block">Instructions on how to pack this product.</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Is packaging material</label>
                                            <div class="col-md-10">
                                                <?php echo $this->Form->input('consumption', array('label' => false, 'class' => 'form-control', 'div' => false)); ?>
                                                <span class="help-block">Select this option if this product is used as a packaging material for sellable products.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane <?php echo ($tabSixError) ? 'active' : ''; ?>" id="tab_attributes">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Color:</label>
                                            <div class="col-md-10">
                                                <?php
                                                    if (sizeof($colors) == 0) {
                                                        echo "<B>No categories exists.</B> <a href='/colors/add' target='_blank'><span class='btn btn-sm blue-steel'><i class='fa fa-delicious'></i>Create colors</span></a>";
                                                    } else {
                                                        echo $this->Form->input('color_id', array('label' => false, 'class' => 'form-control input-large select2me', 'div' => false, 'empty' => 'Select...'));
                                                    }
                                                ?>
                                                <span class="help-block">Set the color of this product.</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Size:</label>
                                            <div class="col-md-10">
                                                <?php
                                                    if (sizeof($sizes) == 0) {
                                                        echo "<B>No sizes exists.</B> <a href='/sizes/add' target='_blank'><span class='btn btn-sm blue-steel'><i class='fa fa-rss'></i>Create sizes</span></a>";
                                                    } else {
                                                        echo $this->Form->input('size_id', array('label' => false, 'class' => 'form-control input-large select2me', 'div' => false, 'empty' => 'Select...'));
                                                    }
                                                ?>
                                                <span class="help-block">Set the size of this product.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="tab_custom">
                                    <?php if($fields) { ?>
                                    <div class="form-body">
                                        <?php foreach ($fields as $field) { ?>
                                            <?php if($field['FieldsValue']) {  $options = array(); ?>
                                                <?php foreach ($field['FieldsValue'] as $value) {
                                                    $options[$value['id']] = $value['value'];
                                                } ?>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label"><?php echo h($field['Field']['name']); ?></label>
                                                <div class="col-md-4">
                                                    <?php echo $this->Form->input('FieldsData.'.$field['Field']['id'], array('options' => $options, 'empty' => 'Please Select..', 'label' => false, 'class' => 'form-control select2me', 'div' =>false)); ?>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label"><?php echo h($field['Field']['name']); ?></label>
                                                <div class="col-md-10">
                                                    <?php echo $this->Form->input('FieldsData.'.$field['Field']['id'], array('label' => false, 'class' => 'form-control', 'div' =>false)); ?>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <?php } else { ?>
                                        <h4 class="text-info text-center margin-top-20 margin-bottom-30">You have no add any custom fields</h4>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->
<ol id="joyRideTipContent">
    <li data-class="nav-tabs" data-text="Next" class="custom" data-options="tipLocation:left">
        <h2>Product</h2>
        <p>Product data is grouped by tabs.</p>
    </li>

    <li data-id="bastab" data-button="Next" data-options="tipLocation:left">
        <h2>Basic</h2>
        <p>Contains all mandatory fields - short and long name, category,SKU and price</p>
    </li>
    <li data-id="dimtab" data-button="Next" data-options="tipLocation:left">
        <h2>Dimesions</h2>
        <p>Contains product dimensions - weight, width, height, length </p>
    </li>
    <li data-id="urltab" data-button="Next" data-options="tipLocation:left">
        <h2>URL</h2>
        <p>Product, image URL</p>
    </li>
    <li data-id="logtab" data-button="Next" data-options="tipLocation:left">
        <h2>Logistics</h2>
        <p>Logistics related data: barcode, safety stock, bin</p>
    </li>
    <li data-id="pactab" data-button="Next" data-options="tipLocation:left">
        <h2>Packaging</h2>
        <p>Contains packaging related data - packaging mateiral, packing instrucitons etc.</p>
    </li>
    <li data-id="atrtab" data-button="Next" data-options="tipLocation:left">
        <h2>Attributes</h2>
        <p>Set product color and size</p>
    </li>
    <li data-id="savebtn" data-button="Close" data-options="tipLocation:left">
        <h2>Save Product</h2>
        <p>Once product data is entered, click this button to save the new product.</p>
    </li>

</ol>
<div class="modal fade" id="addCategoryForm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo __('Add New Category'); ?></h4>
            </div>

            <?php echo $this->Form->create('Category', array('url' => array('controller' => 'categories', 'action' => 'create'), 'class' => 'form-horizontal', 'id' => 'createCatetegoryForm')); ?>
            <div class="modal-body">
                <div id="response"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Name'); ?> <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('name', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Description'); ?> <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->textarea('description', array('class' => 'form-control', 'rows' => 4, 'required'=>false)); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn">Save Category</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="addBinForm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo __('Add New Bin'); ?></h4>
            </div>

            <?php echo $this->Form->create('Bin', array('url' => array('controller' => 'bins', 'action' => 'create'), 'class' => 'form-horizontal', 'id' => 'createBinForm')); ?>
            <div class="modal-body">
                <div id="response-bin"></div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Title'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('title', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Location'); ?><span class="required">* </span></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('location_id', array('class' => 'form-control', 'label' => false,'empty' => 'Select...')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Sort Sequence'); ?></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('sort_sequence', array('class' => 'form-control', 'label' => false)); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Status'); ?></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('status',array( 'label' => false, 'class' => 'form-control input-sm','options' => $status )); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn">Save Bin</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    $('#ProductUom').select2({
        minimumResultsForSearch: -1,
    }).on('change', function() {
        var selected = $(this).val();
        if(selected == 'Kit') {
            $('#kit_block').removeClass('hidden');
        } else {
            $('#kit_block').addClass('hidden');
        }
    });

    
    $('#createCatetegoryForm').submit(function(){
        if($('#CategoryDescription').val() == '') {
            $('#CategoryDescription').val($('#CategoryName').val());
        }
        return true;
    });
<?php $this->Html->scriptEnd(); ?>
</script>