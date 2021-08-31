<?php $action_color = ($locationsactive ? 'green' : 'grey-salt'); ?>

    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">

            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->Session->flash(); ?>
                    <!-- Begin: life time stats -->
                    <div class="portlet box delivrd">
                        <div class="portlet-title">
                            <div class="caption"><i class="fa fa-history"></i> Stock Valuation Report</div>
                            <div class="actions">
                                <span style="font-size:18px;">Total Stock Value: <?php echo number_format($total_value[0]['total_value'], 2, '.', ','); ?></span>
                            </div>
                        </div>

                        <div class="portlet-body">
                            <div class="csv-div">
                                <?php echo $this->Html->link(__('<i class="fa fa-download"></i> Export'), array('controller'=> 'inventories','action' => 'vlreport_csv') + $this->params['named'],array('escape'=> false, 'class' => 'csv-icons', 'title' => 'Export Valuation Report')); ?>
                                <?php echo $this->html->link('<i class="fa fa-undo"></i> Show All', array('controller'=> 'inventories', 'action' => 'vlreport'), array('class' => 'csv-icons import-btn', 'escape' => false, 'title' => 'Show all records')); ?>
                            </div>
                            <hr/>
                            <?php echo $this->Form->create('Inventory', array(
                                    'class' => 'form-horizontal',
                                    'id' => 'inv-search',
                                    'url' => array_merge(array('controller'=>'inventories', 'action' => 'vlreport'), $this->params['pass']),
                            )); ?>
                            <?php echo $this->Form->hidden('product', array('id' => 'product', 'type'=>'text', 'value' => $product['product_id'])); ?>
                            <div class="row margin-bottom-20">
                                <div class="col-md-5">
                                    <div class="input-group col-md-10">
                                        <?php echo $this->Form->input('searchby', array('label' => false, 'class'=>'code-scan form-control input-md', 'placeholder' => 'Enter or scan SKU/EAN/UPC/Serial or product name', 'id' => 'product_auto', 'autofocus' => 'autofocus')); ?>
                                        <span class="input-group-addon"><button id="keysearch" class="" title="Search" style="border:none"><i class="fa fa-search"></i></button></span>
                                    </div>
                                    <?php if(!empty($product['name'])) { ?>
                                        <h5 style="font-size: 17px;color:#333333;font-family: -webkit-body;">Search results for <?php echo h($product['name']); ?></h5>
                                    <?php } ?>
                                </div>
                                
                                <label class="col-md-1 control-label">Filter By: </label>
                                <div class="col-md-2">
                                    <?php if ($locationsactive) { ?>
                                        <?php echo $this->Form->input('warehouse_id', array('label' => false,'class'=>'form-control form-filter input', 'id' => 'warehouse_select', 'required' => false,'empty' => 'Location...')); ?>
                                    <?php } ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo $this->Form->input('category_id', array('label' => false,'class'=>'form-control form-filter input','required' => false, 'value'=>$category_id, 'empty' => 'Category...')); ?>
                                </div>
                            </div>
                            <?php  echo $this->Form->end(); ?>

                            <div class="table-container">
                                <div class="table-actions-wrapper">
                                    <span></span>
                                    <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                </div>
                                <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th width="10%"><i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('Product.sku', 'SKU') ?> </th>
                                        <th width="30%"><i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('Product.name', 'Product Name') ?> </th>
                                        <th width="10%">Category </th>
                                        <th width="10%"> Quantity </th>
                                        <?php if ($this->Session->read('locationsactive') == 1) { ?>    
                                            <th width="10%"> Location </th>
                                        <?php } ?>
                                        <th width="10%">Unit Value (<?php echo h($this->Session->read('currencyname')); ?>)</th>
                                        <th width="10%">Total Value (<?php echo h($this->Session->read('currencyname')); ?>)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(count($inventories) !== 0) { ?>
                                        <?php foreach ($inventories as $inventory) { ?>
                                        <tr>
                                            <td><?php echo h($inventory['Product']['sku']); ?></td>
                                            <td><?php echo $this->element('product_name', array('name' => $inventory['Product']['name'], 'id' => $inventory['Product']['id'])); ?></td>
                                            <td>
                                                <?php if(isset($list_categories[$inventory['Product']['category_id']])) { ?>
                                                <?php echo h($list_categories[$inventory['Product']['category_id']]); ?>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo h($inventory['Inventory']['quantity']); ?></td>

                                            <?php if ($this->Session->read('locationsactive') == 1) { ?>
                                            <td>
                                                <?php if(isset($warehouses_list[$inventory['Inventory']['warehouse_id']])) { ?>
                                                <?php echo ($warehouses_list[$inventory['Inventory']['warehouse_id']]); ?>
                                                <?php } ?>
                                            </td>
                                            <?php } ?>
                                            
                                            <td><?php echo number_format($inventory['Product']['value'], 2, '.', ''); ?></td>
                                            <td><?php echo number_format($inventory['Product']['value']*$inventory['Inventory']['quantity'], 2, '.', ''); ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php } else { ?>
                                        <tr><td align='center' colspan='8'><b>No Data Found</b></td></tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                               
                            <p><?php echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'))); ?></p>
                            <div>
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
                                        echo $paginator->last("Last",array('tag' => 'li')); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- End: life time stats -->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    

<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>

    $('#warehouse_select').select2({
        minimumResultsForSearch: -1,
        placeholder: "Select Location"
    });

    $("#warehouse_select").change(function(){
        $('#inv-search').submit();
    });

    $('#InventoryCategoryId').select2({
        minimumResultsForSearch: -1,
        placeholder: "Select Category"
    });

    $("#InventoryCategoryId").change(function(){
        $('#inv-search').submit();
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
        select: function( event, ui ) {
            if(ui.item) {
                
                if(ui.item.product_id !== undefined) {
                    $("#product").val(ui.item.product_id);
                }
                if(ui.item.warehouse_id !== undefined) { // if serialnumber
                    $("#warehouse_select").val(ui.item.warehouse_id);
                }
                $("#product_auto").val('');
                $('#inv-search').submit();
                return ui.item.label;
            }
        },
    });

    $('#keysearch').click(function() {
        $("#product").val('');
        $('#inv-search').submit();
        return false;
    });

    

<?php $this->Html->scriptEnd(); ?>
</script>