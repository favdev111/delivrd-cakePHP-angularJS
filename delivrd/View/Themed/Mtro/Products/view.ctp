<?php #$this->AjaxValidation->active();

 $packagingtext = ($product['Product']['consumption'] == 1 ? "Yes" : "No");

 ?>
<!-- BEGIN CONTENT -->
    <div class="page-content-wrapper"  ng-controller="ViewProduct">
        <div class="page-content">
            <?php echo $this->element('expirytext'); ?>
            
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <?php if($product['Product']['user_id'] == $this->Session->read('Auth.User.id')) { ?>
                <div class="col-md-6">
                    <div class="portlet box blue-steel">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-reorder"></i>
                                Related Information
                            </div>
                            <div class="actions">
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-fit-height grey-mint dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                            <?php echo $this->Html->link(__('<i class="fa fa-pencil"></i> Edit'), array('action' => 'edit',$product['Product']['id']),array('escape'=> false));  ?>
                                        </li> 
                                        <li>
                                            <?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $product['Product']['id']),array('escape'=> false), __('Are you sure you want to delete %s?', h($product['Product']['name']))); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Html->link(__('<i class="fa fa-history"></i> Transactions History'), array('controller'=> 'inventories','action' => 'transactions_history', $product['Product']['id']),array('escape'=> false)); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Html->link(__('<i class="fa fa-flag"></i> ATP'), array('controller'=> 'products','action' => 'atp', $product['Product']['id']),array('escape'=> false)); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print Product Barcode Label'), array('controller' => 'Labels', 'action' => 'productlabel', $product['Product']['id']),array('escape'=> false)); ?>
                                        </li>

                                        <li class="divider"></li>
                                    
                                        <li>
                                            <?php echo $this->Form->postLink(__('<i class="fa fa-lock"></i> Block For Selling'), array('action' => 'changestatus', $product['Product']['id'],12),array('escape'=> false)); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Form->postLink(__('<i class="fa fa-shield"></i> Complete Block'), array('action' => 'changestatus', $product['Product']['id'],13),array('escape'=> false)); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Form->postLink(__('<i class="fa fa-unlock"></i> Cancel Block'), array('action' => 'changestatus', $product['Product']['id'],1),array('escape'=> false)); ?>
                                        </li>

                                        <li class="divider"></li>
                                        
                                        <li>
                                            <?php echo $this->Html->link(__('<i class="fa fa-globe"></i> Search for Similar on Amazon'),'http://www.amazon.com/s/ref=nb_sb_noss_2?field-keywords='.h($product['Product']['name']),array('target' => '_blank','escape'=> false));  ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Html->link(__('<i class="fa fa-globe"></i> Search for Similar on AliExp'),'http://www.aliexpress.com/wholesale?SearchText='.h($product['Product']['name']),array('target' => '_blank','escape'=> false));  ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <a href="<?php echo $this->Html->url(array('controller' => 'salesorders', 'action' => 'index', $product['Product']['id'])); ?>" class="icon-btn">
                                <i class="fa fa-shopping-cart"></i>
                                <div>Cust. Orders</div><span class="badge badge-success"><?php echo $cord_count; ?></span>
                            </a>

                            <a href="<?php echo $this->Html->url(array('controller' => 'replorders', 'action' => 'index', $product['Product']['id'])); ?>" class="icon-btn">
                                <i class="fa fa-archive"></i>
                                <div>Repl. Orders</div><span class="badge badge-success"><?php echo $rord_count; ?></span>
                            </a>

                            <a href="<?php echo $this->Html->url(array('controller' => 'serials', 'action' => 'index', '?' => array('product' => $product['Product']['id']))); ?>" class="icon-btn">
                                <i class="fa fa-barcode"></i>
                                <div>Serials</div><span class="badge badge-success"><?php echo h($serials) ?></span>
                            </a>
                            
                            <a href="<?php echo $this->Html->url(array('controller' => 'inventories', 'action' => 'index', '?' => array('product' => $product['Product']['id']))); ?>" class="icon-btn">
                                <i class="fa fa-building-o"></i>
                                <div>Inventory</div><span class="<?php echo $inventory_badge; ?>"><?php echo $total_inventory; ?></span>
                            </a>

                            <a href ng-click="addDocument(<?php echo $product['Product']['id']; ?>, '<?php echo $product['Product']['sku']; ?>')" class="icon-btn">
                                <i class="fa fa-upload"></i>
                                <div>Documents</div><span class="badge bg-blue-steel"><?php echo count($documents); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="col-md-3">
                    <div class="portlet box blue-steel">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-camera"></i>
                                Product Image
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="thumbnail">
                                <img class="productImage" rel="product_img" data-id="<?php echo $product['Product']['id']; ?>" src="<?php echo h($product['Product']['imageurl']); ?>" height="128px" width="128px" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        
            <div class="row">
                <div class="col-md-12">
                <?php echo $this->Form->create('Product', array('class' => 'form-horizontal form-row-seperated')); ?>
                    <?php echo $this->Form->input('id'); ?>
                        <div class="portlet box blue-steel">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-barcode"></i><?php echo htmlspecialchars($product['Product']['name'])." - View Details"; ?>
                                </div>
                                <div class="actions btn-set">
                                    <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>

                                    <div class="btn-group pull-right" style="margin-left: 10px;">
                                        <button type="button" class="btn btn-fit-height grey-mint dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                            <i class="fa fa-ellipsis-h"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <li>
                                                <?php echo $this->Html->link(__('<i class="fa fa-pencil"></i> Edit'), array('action' => 'edit',$product['Product']['id']),array('escape'=> false));  ?>
                                            </li> 
                                            <li>
                                                <?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $product['Product']['id']),array('escape'=> false), __('Are you sure you want to delete %s?', h($product['Product']['name']))); ?>
                                            </li>
                                            <li>
                                                <?php echo $this->Html->link(__('<i class="fa fa-history"></i> Transactions History'), array('controller'=> 'inventories','action' => 'transactions_history', $product['Product']['id']),array('escape'=> false)); ?>
                                            </li>
                                            <li>
                                                <?php echo $this->Html->link(__('<i class="fa fa-flag"></i> ATP'), array('controller'=> 'products','action' => 'atp', $product['Product']['id']),array('escape'=> false)); ?>
                                            </li>
                                            <li>
                                                <?php echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print Product Barcode Label'), array('controller' => 'Labels', 'action' => 'productlabel', $product['Product']['id']),array('escape'=> false)); ?>
                                            </li>

                                            <li class="divider"></li>
                                        
                                            <li>
                                                <?php echo $this->Form->postLink(__('<i class="fa fa-lock"></i> Block For Selling'), array('action' => 'changestatus', $product['Product']['id'],12),array('escape'=> false)); ?>
                                            </li>
                                            <li>
                                                <?php echo $this->Form->postLink(__('<i class="fa fa-shield"></i> Complete Block'), array('action' => 'changestatus', $product['Product']['id'],13),array('escape'=> false)); ?>
                                            </li>
                                            <li>
                                                <?php echo $this->Form->postLink(__('<i class="fa fa-unlock"></i> Cancel Block'), array('action' => 'changestatus', $product['Product']['id'],1),array('escape'=> false)); ?>
                                            </li>

                                            <li class="divider"></li>
                                            
                                            <li>
                                                <?php echo $this->Html->link(__('<i class="fa fa-globe"></i> Search for Similar on Amazon'),'http://www.amazon.com/s/ref=nb_sb_noss_2?field-keywords='.h($product['Product']['name']),array('target' => '_blank','escape'=> false));  ?>
                                            </li>
                                            <li>
                                                <?php echo $this->Html->link(__('<i class="fa fa-globe"></i> Search for Similar on AliExp'),'http://www.aliexpress.com/wholesale?SearchText='.h($product['Product']['name']),array('target' => '_blank','escape'=> false));  ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="tabbable">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#tab_general" data-toggle="tab">General</a>
                                        </li>
                                        <li>
                                            <a href="#tab_dimensions" data-toggle="tab">Dimensions</a>
                                        </li>
                                        <li>
                                            <a href="#tab_urls" data-toggle="tab">URL</a>
                                        </li>
                                        <li>
                                            <a href="#tab_logistics" data-toggle="tab">Logistics</a>
                                        </li>
                                        <li>
                                            <a href="#tab_packaging" data-toggle="tab">Packaging</a>
                                        </li>
                                        <li>
                                            <a href="#tab_attributes" data-toggle="tab">Attributes</a>
                                        </li>
                                        <li>
                                            <a href="#tab_custom" data-toggle="tab">Custom Fields</a>
                                        </li>
                                        <li>
                                            <a href="#tab_suppliers" data-toggle="tab">Supplier</a>
                                        </li>
                                        <?php if($this->Session->read('is_admin') == 1) { ?>
                                        <li>
                                            <a href="#tab_history" data-toggle="tab">History</a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                    <div class="tab-content no-space">
                                        <div class="tab-pane active" id="tab_general">
                                            <div class="col-md-12 col-sm-12">
                                                <div class="portlet-body">
                                                <div class="row static-info">
                                                <div class="col-md-3 name">Name:</div>
                                                <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['name']); ?></div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-3 name">Description:</div>
                                                <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['description']); ?></div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-3 name">UOM:</div>
                                                <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['uom']); ?></div>
                                            </div>
                                            <?php if($product['Product']['uom'] == 'Kit') { ?>
                                                <div class="form-group" id="kit_block">
                                                    <label class="col-md-3">Kits: </label>
                                                    <div class="col-md-9">
                                                        <table class="table table-striped table-bordered">
                                                            <tr>
                                                                <th>Component</th>
                                                                <th>Quantity</th>
                                                                <th>Active</th>
                                                            </tr>
                                                            <tr ng-repeat="part in product_parts">
                                                                <td>
                                                                    {{partName(part.Kit.parts_id)}}
                                                                </td>
                                                                <td>{{ part.Kit.quantity }}</td>
                                                                <td>{{ partStatus(part.Kit.active) }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="row static-info">
                                                <div class="col-md-3 name">Category:</div>
                                                <div class="col-md-9 value"><?php echo htmlspecialchars($product['Category']['name']); ?></div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-3 name">Group:</div>
                                                <div class="col-md-9 value"><?php echo htmlspecialchars($product['Group']['name']); ?></div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-3 name">SKU:</div>
                                                <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['sku']); ?></div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-3 name">Bin:</div>
                                                <div class="col-md-9 value"><?php echo htmlspecialchars($bin[0]); ?></div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-3 name">Price:</div>
                                                <div class="col-md-9 value"><?php echo h($this->Session->read('currencyname')); ?> <?php echo h($product['Product']['value']); ?></div>
                                            </div>

                                            <?php if($this->Session->read('paid') == 1) { ?>
                                            <div class="row static-info">
                                                    <div class="col-md-3 name">Channels Prices(<?php echo h($this->Session->read('currencyname')); ?>):</div>
                                                    <div class="col-md-9">
                                                        <div class="row form-group">
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
                                                                            {{ schannels[price.schannel_id] }}
                                                                        </td>
                                                                        <td><?php echo h($this->Session->read('currencyname')); ?> {{ price.value }}</td>
                                                                        <td><button class="btn btn-info btn-xs" type="button" ng-click="removePrice(price.id)"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php } ?>

                                            <div class="row static-info">
                                                <div class="col-md-3 name">Reorder Point:</div>
                                                <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['reorder_point']); ?></div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-3 name">Image URL</div>
                                                <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['imageurl']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                        </div>
                                        <div class="tab-pane" id="tab_dimensions">
                                    
                                            <div class="col-md-6 col-sm-12">
                                                <div class="portlet-body">
                                                    <div class="row static-info">
                                                    <div class="col-md-3 name">Weight (<?php echo h($this->Session->read('weight_unit')) ?>):</div>
                                                    <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['weight']); ?></div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-3 name">Height (<?php echo htmlspecialchars($this->Session->read('volume_unit')) ?>):</div>
                                                    <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['height']); ?></div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-3 name">Width (<?php echo htmlspecialchars($this->Session->read('volume_unit')) ?>):</div>
                                                    <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['width']); ?></div>
                                                </div>                                              
                                            <div class="row static-info">
                                                <div class="col-md-3 name">Depth (<?php echo h($this->Session->read('volume_unit')) ?>):</div>
                                                <div class="col-md-9 value"><?php echo h($product['Product']['depth']); ?></div>
                                            </div>
                                            
                                        </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab_urls">
                                        <div class="col-md-6 col-sm-12">
                                                <div class="portlet-body">
                                                
                                                <div class="row static-info">
                                                    <div class="col-md-3 name">Product URL</div>
                                                    <div class="col-md-9 value"><a href='<?php echo htmlspecialchars($product['Product']['pageurl']); ?>'><?php echo h($product['Product']['pageurl']); ?></a></div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        </div>
                                        <div class="tab-pane" id="tab_logistics">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="portlet-body">
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Barcode System</div>
                                                        <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['barcode_standards_id']); ?></div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Barcode Number</div>
                                                        <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['barcode']); ?></div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Issue Inventory Location</div>
                                                        <div class="col-md-9 value"><?php echo htmlspecialchars($product['Issue']['name']); ?></div>
                                                    </div>  
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Receive Inventory Location</div>
                                                        <div class="col-md-9 value"><?php echo htmlspecialchars($product['Receive']['name']); ?></div>
                                                    </div>  
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Safety Stock</div>
                                                        <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['safety_stock']); ?></div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Bin Number</div>
                                                        <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['bin']); ?></div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Sales Forecast</div>
                                                        <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['sales_forecast']); ?></div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Lead Time</div>
                                                        <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['lead_time']); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="tab_packaging">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="portlet-body">
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Packaging Material</div>
                                                        <?php $packmatname = isset($pack_mat['Product']) ? $pack_mat['Product']['name'] : "Not Defined"; ?>
                                                        <div class="col-md-9 value"><?php echo htmlspecialchars($packmatname); ?></div>
                                                    </div>
                                                    <?php if(isset($pack_mat['Product']['imageurl'])) { ?>
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Packaging Material Image</div>
                                                        <?php $packmatimg = isset($pack_mat['Product']) ? $pack_mat['Product']['imageurl'] : "Not Defined"; ?>
                                                        <div class="col-md-9 value"><img src="<?php echo h($packmatimg) ?>" height="128" width="128" ></div>
                                                    </div>
                                                    <?php } ?> 
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Packaging Instructions</div>
                                                        <div class="col-md-9 value"><?php echo htmlspecialchars($product['Product']['packaging_instructions']); ?></div>
                                                    </div>  
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name">Packaging Material?</div>
                                                        <div class="col-md-9 value"><?php echo htmlspecialchars($packagingtext); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="tab_attributes">
                                        <div class="col-md-6 col-sm-12">
                                                <div class="portlet-body">
                                                    <div class="row static-info">
                                                    <div class="col-md-3 name">Size</div>
                                                    <?php if($product['Size']['name']) { ?>
                                                    <div class="col-md-9 value">
                                                        <?php echo htmlspecialchars($product['Size']['name']); ?>
                                                        <?php if($product['Size']['description']) { ?>
                                                        - <?php echo htmlspecialchars($product['Size']['description']); ?>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?> 
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-3 name">Color</div>
                                                    <div class="col-md-9 value">
                                                    <?php if($product['Color']['name']) { ?>
                                                        <?php echo h($product['Color']['name']); ?>
                                                        <button type="button" class="btn btn-default disabled" style="background-color:#<?php echo htmlspecialchars($product['Color']['htmlcode']); ?>"></button>
                                                    <?php } ?> 
                                                    </div>
                                                </div>                                                  
                                        </div>
                                        </div>
                                        </div>

                                        <div class="tab-pane" id="tab_custom">
                                            <?php if($fields) { ?>
                                            <div class="form-body">
                                                <?php foreach ($fields as $field) { ?>
                                                    <?php if($field['FieldsValue']) { $options = array(); ?>
                                                        <?php foreach ($field['FieldsValue'] as $value) {
                                                            $options[$value['id']] = $value['value'];
                                                        } ?>
                                                        <div class="row static-info">
                                                            <div class="col-md-3 name"><?php echo h($field['Field']['name']); ?></div>
                                                            <div class="col-md-9 value">
                                                                <?php if(isset($custom['FieldsData'][$field['Field']['id']])) { ?>
                                                                    <?php if(isset($options[$custom['FieldsData'][$field['Field']['id']]])) { ?>
                                                                        <?php echo h($options[$custom['FieldsData'][$field['Field']['id']]]); ?>
                                                                    <?php } else { ?>
                                                                        <?php echo h($custom['FieldsData'][$field['Field']['id']]); ?>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    <?php } else { ?>
                                                    <div class="row static-info">
                                                        <div class="col-md-3 name"><?php echo h($field['Field']['name']); ?></div>
                                                        <div class="col-md-9 value"><?php echo (isset($custom['FieldsData'][$field['Field']['id']])? h($custom['FieldsData'][$field['Field']['id']]) :''); ?></div>
                                                    </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                            <?php } else { ?>
                                                <h4 class="text-info text-center margin-top-20 margin-bottom-30">You have no add any custom fields</h4>
                                            <?php } ?>
                                        </div>

                                        <div class="tab-pane" id="tab_suppliers">
                                            <div class="col-md-12 col-sm-12">
                                                <div class="portlet-body">
                                                    <table class="table table-hover dataTable no-footer">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th>Supplier Name</th>
                                                        <th>Active</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if(!empty($suppliers)) { ?>
                                                        <?php foreach ($suppliers as $supplier) { ?>
                                                        <tr role="row">
                                                            <td><?php echo h($supplier['Supplier']['name']); ?></td>
                                                            <td><?php echo (($supplier['Productsupplier']['status'] != 'no') ? 'Active' : 'Inactive'); ?></td>
                                                        </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr><td colspan="2"><h4 class="text-center text-warning">No suppliers found</h4></td></tr>
                                                    <?php } ?>
                                                    </tbody>
                                                    </table>

                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if($this->Session->read('is_admin') == 1) { ?>
                                        <div class="tab-pane" id="tab_history">
                                            <div class="table-container">
                                                <table class="table table-hover dataTable no-footer" id="datatable_history" role="grid">
                                                    <thead>
                                                        <tr role="row" class="heading">
                                                            <th width="25%">
                                                                 Datetime
                                                            </th>
                                                            <th width="55%">
                                                                 Description
                                                            </th>
                                                            
                                                            <th width="10%">
                                                                 Actions
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($objectevents as $key => $objectevent) { ?>
                                                        <tr role="row" class="<?php echo ($key % 2 == 0 ? "odd" : "even"); ?>">
                                                            <td class="sorting_1"><?php echo $this->Admin->localTime("%Y-%m-%d %H:%M:%S", strtotime($objectevent['Event']['created'])); ?></td>
                                                            <td><?php echo "Product status changed to ".h($objectevent['Status']['name']); ?></td>
                                                            <td></td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>  
                                        <?php } ?>
                                        </div>
                                
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
<?php echo $this->Html->script('/app/Products/view.js?v=0.0.3', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/app/Documents/view.js?v=0.0.2', array('block' => 'pageBlock')); ?>

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var product = <?php echo json_encode($product); ?>;
    var channel_prices = <?php echo json_encode($product['ProductsPrices']); ?>;
    var schannels = <?php echo json_encode($schannels); ?>;
    var schannels_a = <?php echo json_encode($schannels_a); ?>;

    var product_parts = <?php echo json_encode($product_parts); ?>;
    var parts = <?php echo json_encode($parts); ?>;
    var parts_a = <?php echo json_encode($parts_a); ?>;

    var doc_title = 'Product SKU: ';

<?php $this->Html->scriptEnd(); ?>
</script>