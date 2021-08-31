<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="SerialList">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>

            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->Session->flash(); ?>
                    <!-- Begin: life time stats -->
                    <div class="portlet box delivrd">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-list-ol"></i>Serial Numbers List
                            </div>
                            <div class="actions">
                                <?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Add Serial'), array('controller' => 'serials', 'action' => 'add'), array('class' => 'btn add-delivrd', 'escape' => false, 'title' => 'New Serial')); ?>
                            </div>
                        </div>

                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="csv-div">
                                        <div class="btn-toolbar">
                                            <div class="btn-group">
                                                <?php echo $this->Html->link(__('<i class="fa fa-download"></i> Export'), array('controller'=> 'serials','action' => 'exportcsv'),array('escape'=> false, 'class' => 'csv-icons')); ?>
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
                                    </div>
                                </div>

                                <div class="col-md-9">
                                    <?php echo $this->Form->create('Serial', array(
                                        'class' => 'form-horizontal',
                                        'url' => array('action' => 'index'),
                                        )); 
                                        $product = $this->request->query('searchby');
                                    ?>
                                    <?php echo $this->Form->hidden('limit', ['value' => $limit]); ?>

                                    <div class="row" style="margin-top: 2px;">
                                        <div class="col-md-5">
                                            <div class="input-group" style="margin-bottom: -5px;">
                                                <div class="input-group"> 
                                                    <?php echo $this->Form->input('searchby', array('label' => false, 'class'=>'code-scan form-control input-md', 'placeholder' => 'Search by sku, serial number or product name', 'value' => $product, 'id' => 'autocomplete', 'style' => 'width: 370px;height: 32px;')); ?>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-barcode"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <?php echo $this->Form->input('warehouse_id', array('label' => false,'class'=>'form-control form-filter input','required' => false,'empty' => 'Location...', 'style' => 'width: 140px;height: 32px;')); ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?php echo $this->Form->input('instock',array('label' => false,'class' => 'form-control form-filter ','required' => false,'div' =>false, 'options' => array('0' => 'Out Of Stock','1' => 'In Stock'),'empty' => 'Status..', 'style' => 'width: 140px;height: 32px;')); ?>
                                        </div>
                                        <div class="col-md-2">
                                            <button id='clicksearch' class="btn btn-md blue filter-submit margin-bottom"><i class="fa fa-search"></i></button>
                                            <?php echo $this->html->link('<i class="fa fa-undo"></i>', array('plugin' => false, 'controller' => 'Serials', 'action' => 'index'), array('class' => 'btn btn-md blue filter-submit margin-bottom', 'escape' => false)); ?>
                                        </div>
                                    </div>

                                    <?php  echo $this->Form->end(); ?>  
                                </div>
                            </div>

                            <div class="table-container">
                                <div class="table-actions-wrapper">
                                    <span>
                                    </span>

                                    <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                </div>
                                <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="10%"> <i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('Product.sku', 'SKU') ?> </th>
                                    <th width="15%"> <i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('Product.name', 'Product Name') ?> </th>
                                    <th width="10%"> <i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('serialnumber', 'Serial Number') ?> </th>
                                    <th width="10%"> Location </th>
                                    <th width="5%"> Image </th>
                                    <th width="10%"> <?php echo $this->Paginator->sort('created', 'Creation Date'); ?> </th>
                                    <th width="5%"> Status </th>
                                    <th width="5%"> Actions </th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php if(count($serials) != 0) {
                                foreach ($serials as $serial): ?>
                                    <tr role="row">
                                        <td><?php echo h($serial['Product']['sku']); ?></td>
                                        <td><?php echo $this->element('product_name', array('name' => $serial['Product']['name'], 'id' => $serial['Product']['id'])); ?></td>
                                        <td><?php echo h($serial['Serial']['serialnumber']); ?></td>
                                        <td><?php echo h($serial['Warehouse']['name']); ?></td>
                                        <td><?php echo "<img src=".h($serial['Product']['imageurl'])." height='32px' width='32px'>"; ?></td>
                                        <td><?php echo $this->Admin->localTime("%B %d, %Y", strtotime($serial['Serial']['created'])); ?></td>
                                        <td>
                                        <?php echo $status = ($serial['Serial']['instock'] == 1 ? "<span class='label label-sm label-info'>In Stock</span>" : "<span class='label label-sm label-danger'>Out Of Stock</span>"); ?>
                                        </td>
                                        <td>
                                        <div class="btn-group">
                                            <a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
                                            <i class="fa fa-ellipsis-h"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right">
                                                <li>
                                                <?php echo $this->html->link('<i class="fa fa-search"></i>
                                                View', array('plugin' => false, 'controller' => 'Serials', 'action' => 'view', $serial['Serial']['id']), array('escape' => false)); ?>
                                                </li>                                       
                                                <li>
                                                <?php echo $this->html->link('<i class="fa fa-pencil"></i>
                                                Edit', array('plugin' => false, 'controller' => 'Serials', 'action' => 'edit', $serial['Serial']['id']), array('escape' => false)); ?>
                                                </li>
                                                <li>
                                                    <?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $serial['Serial']['id']), array('escape'=> false), __('Are you sure you want to delete serial number %s?', h($serial['Serial']['serialnumber']))); ?>
                                                </li>
                                                <li>
                                                <?php echo $this->html->link('Serial transactions history', array('plugin' => false, 'controller' => 'serials', 'action' => 'serialTransaction', $serial['Serial']['id']), array('escape' => false)); ?>
                                                </li>
                                                <li>
                                                    <a href ng-click="addDocument(<?php echo $serial['Serial']['id']; ?>, '<?php echo $serial['Serial']['serialnumber']; ?>')"><i class="fa fa-upload"></i> Documents</a>
                                                </li>
                                            </ul>
                                        </div>
                                        </td>
                                    </tr>
                                <?php endforeach;
                                } else {
                                    echo "<tr><td colspan='8' class='text-center no-data'>No data found</td></tr>";
                                } ?>
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
                                            <a href="https://delivrd.freshdesk.com/support/solutions/articles/17000091127-managing-serial-numbers-in-delivrd" target="_blank">Managing Serial Numbers in Delivrd Tutorials</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End: life time stats -->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    
    var doc_title = 'Serial';
    
    $('#SerialWarehouseId').select2({
        minimumResultsForSearch: -1
    });

    $('#SerialInstock').select2({
        minimumResultsForSearch: -1
    });
    
    $('.limit').select2({
        minimumResultsForSearch: -1,
        width: '80px'
    });

    $('.limit').change(function(){
        $('#SerialLimit').val($(this).val());
        $('#SerialIndexForm').submit();
    });
<?php $this->Html->scriptEnd(); ?>
</script>

<?php echo $this->Html->script('/app/Serials/index.js?v=0.0.2', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/app/Documents/view.js?v=0.0.2', array('block' => 'pageBlock')); ?>