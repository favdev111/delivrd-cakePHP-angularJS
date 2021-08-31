<?php #$this->AjaxValidation->active(); ?>
<style>
.error-message { 
    background-color: #ff0033; 
    color: #ffffff;
    font-weight: bold;
    }
</style>

<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="EditProduct">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>
        
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="portlet box blue-steel">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-camera"></i> Product Image
                            </div>
                            <div class="actions">
                                <div class="btn-group pull-right" style="margin-left: 10px;">
                                    <a href ng-click="addDocument(<?php echo $product['Product']['id']; ?>, '<?php echo $product['Product']['sku']; ?>')" class="btn btn-fit-height blue" style="margin-right: 13px;"><i class="fa fa-upload"></i> Documents</a>

                                    <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-search"></i> View'), array('action' => 'view',$product['Product']['id']),array('escape'=> false));  ?></li> 
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $product['Product']['id']),array('escape'=> false), __('Are you sure you want to delete %s?', $product['Product']['name'])); ?></li>
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-history"></i> Transactions History'), array('controller'=> 'inventories', 'action' => 'transactions_history', $product['Product']['id']),array('escape'=> false)); ?></li>
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print Product Barcode Label'), array('controller' => 'Labels', 'action' => 'productlabel', $product['Product']['id']),array('escape'=> false)); ?></li>
                                        <li class="divider"></li>
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-shopping-cart"></i> Change publish status'), array('action' => 'togglepublishstatus', $product['Product']['id'],$product['Product']['publish']),array('escape'=> false)); ?></li>
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-lock"></i> Block For Selling'), array('action' => 'changestatus', $product['Product']['id'],12),array('escape'=> false)); ?></li>
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-shield"></i> Complete Block'), array('action' => 'changestatus', $product['Product']['id'],13),array('escape'=> false)); ?></li>
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-unlock"></i> Cancel Block'), array('action' => 'changestatus', $product['Product']['id'],1),array('escape'=> false)); ?></li>
                                        <li class="divider"></li>
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-globe"></i> Search similar on Ebay'),'http://search.ebay.com/ws/search/SaleSearch?satitle='.$product['Product']['name'],array('target' => '_blank','escape'=> false)); ?></li>
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-globe"></i> Search similar on Amazon'),'http://www.amazon.com/s/ref=nb_sb_noss_2?field-keywords='.$product['Product']['name'],array('target' => '_blank','escape'=> false)); ?></li>
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-globe"></i> Search similar on AliExp'),'http://www.aliexpress.com/wholesale?SearchText='.$product['Product']['name'],array('target' => '_blank','escape'=> false)); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <img src="<?php echo h($product['Product']['imageurl']); ?>" height="128px" width="128px"  class="productImage" rel="product_img" data-id="<?php echo $product['Product']['id']; ?>" data-input="ProductImageurl">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                <?php echo $this->Form->create('Product', array('class' => 'form-horizontal form-row-seperated','novalidate' => true)); ?>
                    
                    <?php echo $this->Form->input('id'); ?>
                        <div class="portlet box blue-steel">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-barcode"></i><?php echo h($product['Product']['name'])." - Edit Details "; ?>
                                </div>
                                <div class="actions btn-set">
                                    <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                                    <button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
                                </div>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="tabbable">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tab_general" data-toggle="tab">General </a></li>
                                        <li><a href="#tab_dimensions" data-toggle="tab">Dimensions </a></li>
                                        <li><a href="#tab_urls" data-toggle="tab">URL </a></li>
                                        <li><a href="#tab_logistics" data-toggle="tab">Logistics</a></li>
                                        <li><a href="#tab_packaging" data-toggle="tab">Packaging</a></li>
                                        <li><a href="#tab_attributes" data-toggle="tab">Attributes</a></li>
                                        <li><a href="#tab_custom" data-toggle="tab">Custom Fields</a></li>
                                    </ul>
                                    <div class="tab-content no-space">
                                        <div class="tab-pane active" id="tab_general">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Name: <span class="required">*</span></label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('name',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Description: </label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('description',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">UOM</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('uom',array('label' => false, 'class' => 'form-control input-large','options' => $uoms, 'div' =>false,'empty' => 'Select...')); ?>
                                                        <span class="help-block">Unit Of Measure</span>
                                                    </div>
                                                </div>

                                                <div class="form-group" id="kit_block">
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
                                                    <label class="col-md-2 control-label">Category </label>
                                                    <div class="col-md-10">
                                                        <span class="categoriesDropDown">
                                                            <?php echo $this->Form->input('category_id',array('label' => false, 'class' => 'form-control select2me','div' =>false,'empty' => 'Select...')); ?>
                                                        </span>
                                                        <a href="#" data-toggle="modal" data-target="#addCategoryForm">
                                                            <span class="btn btn-sm blue-steel"><i class="fa fa-sitemap"></i> Create new category</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Group </label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('group_id',array('label' => false, 'class' => 'form-control select2me','div' =>false)); ?>
                                                        <span class="help-block">
                                                         </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">SKU: <span class="required">*</span></label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('sku',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">BIN: </label>
                                                    <div class="col-md-10">
                                                        <span class="binDropDown">
                                                            <?php echo $this->Form->input('Bin', array('label' => false, 'placeholder' => 'Select bin', 'class' => 'form-control input-large multiple', 'options' => $bins, 'div' => false, 'multiple')); ?><br>
                                                            <a href="#" data-toggle="modal" data-target="#addBinForm">
                                                                <span class="btn btn-sm blue-steel"><i class="fa fa-list"></i> Create new bin</span>
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Price(<?php echo h($this->Session->read('currencyname')); ?>): </label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('value',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>

                                                <?php if($this->Session->read('paid') == 1) { ?>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Prices for Sales Channels(<?php echo h($this->Session->read('currencyname')); ?>): 
                                                    </label>
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
                                                                        </td>
                                                                        <td><?php echo h($this->Session->read('currencyname')); ?> {{ price.ProductsPrices.value }}</td>
                                                                        <td><button class="btn btn-info btn-xs" type="button" ng-click="removePrice(price.ProductsPrices.id)"><i class="fa fa-times" aria-hidden="true"></i></button></td>
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
                                                        <?php echo $this->Form->input('reorder_point',array('label' => false, 'class' => 'form-control','div' =>false, 'placeholder' => '15')); ?>
                                                    <span class="help-block">
                                                    Below this level of stock, system will display low inventory warnings.</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Image URL:<span class="required">
                                                    </span></label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('imageurl',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5" style="text-align: center;">
                                                        <button class="btn green" type="submit" id="savebtn"><i class="fa fa-check"></i> Save</button>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab_dimensions">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Weight (<?php echo h($this->Session->read('weight_unit')) ?>):</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('weight',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Height (<?php echo h($this->Session->read('volume_unit')) ?>):</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('height',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Width (<?php echo h($this->Session->read('volume_unit')) ?>):</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('width',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Depth (<?php echo h($this->Session->read('volume_unit')) ?>):</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('depth',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="tab_urls">
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

                                        <div class="tab-pane" id="tab_logistics">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Barcode System</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('barcode_standards_id',array('label' => false,'class' => 'form-control','div' =>false, 'options' => array('EAN' => 'EAN','UPC' => 'UPC','ISBN' => 'ISBN'),'empty' => '(choose one)')); ?>
                                                        <span class="help-block">EAN/UPC/ISBN etc. </span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Barcode Number</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('barcode',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                        <span class="help-block">
                                                        12 or 13 Characters. For example, 7290103127459 </span>
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
                                                        <?php echo $this->Form->input('safety_stock',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Bin Number:</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('bin',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
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
                                        
                                        <div class="tab-pane" id="tab_packaging">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Packaging Material</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('packaging_material_id',array('label' => false,'options' => $packmaterialsarr,'class' => 'form-control','div' =>false ,'empty' => 'Select...')); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Packaging Instructions</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->textarea('packaging_instructions',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Packaging Material?</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('consumption',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="tab-pane" id="tab_attributes">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Color</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('color_id',array('label' => false,'empty' => 'choose one','class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Size</label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('size_id',array('label' => false, 'empty' => 'choose one', 'class' => 'form-control','div' =>false)); ?>
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
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
</div>
<!-- END CONTENT -->

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
                    <label class="control-label col-md-3"><?php echo __('Name'); ?></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('name', array('class' => 'form-control', 'label' => false, 'value'=>'')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo __('Description'); ?></label>
                    <div class="col-md-8">
                        <?php echo $this->Form->textarea('description', array('class' => 'form-control', 'rows' => 4, 'required'=>false, 'value'=>'')); ?>
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

<?php echo $this->Html->script('/app/Products/edit.js?v=0.0.2', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/app/Documents/view.js?v=0.0.2', array('block' => 'pageBlock')); ?>

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var product = <?php echo json_encode($product); ?>;
    var channel_prices = <?php echo json_encode($channel_prices); ?>;
    var schannels = <?php echo json_encode($schannels); ?>;
    var schannels_a = <?php echo json_encode($schannels_a); ?>;

    var product_parts = <?php echo json_encode($product_parts); ?>;
    var parts = <?php echo json_encode($parts); ?>;
    var parts_a = <?php echo json_encode($parts_a); ?>;

    var doc_title = 'Product SKU: ';

    $('#KitPartlId').select2({
    });
    
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
    if($('#ProductUom').val() == 'Kit') {
        $('#kit_block').removeClass('hidden');
    } else {
        $('#kit_block').addClass('hidden');
    }

    $('#createCatetegoryForm').submit(function() {
        if($('#CategoryDescription').val() == '') {
            $('#CategoryDescription').val($('#CategoryName').val());
        }
        return true;
    });
<?php $this->Html->scriptEnd(); ?>
</script>