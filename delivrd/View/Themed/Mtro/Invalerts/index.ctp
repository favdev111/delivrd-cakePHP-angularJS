<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <?php echo $this->element('expirytext'); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <!-- Begin: life time stats -->
                <div class="portlet box delivrd">
                    <div class="portlet-title"  style="background:#eee;">
                        <div class="caption">
                            <i class="fa fa-barcode"></i> Low Inventory Alerts
                        </div>
                        <div class="actions">
                                <?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Add Inventory Alert'),
                                    array('controller'=> 'invalerts','action' => 'add', '?' => ['f' => 'a']),
                                    array(
                                        'class' => 'btn default add-delivrd',
                                        'escape'=> false,
                                        'data-toggle'=>'modal', 'data-target'=>'#ajaxModal'
                                    )
                                );
                                ?>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <?php echo $this->Form->create('Inventory', array(
                                    'class' => 'form-horizontal',
                                    'id' => 'invalert-search',
                                    'url' => array('controller'=>'invalerts', 'action' => 'index'),
                            )); ?>
                            <?php echo $this->Form->hidden('product_id', ['value' => '']); ?>
                            <?php echo $this->Form->hidden('limit', ['value' => $limit]); ?>
                        <?php echo $this->Form->end(); ?>
                        <div class="table-container">
                            <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th width="15%"><i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('Product.name', 'Product') ?></th>
                                        <th width="15%"><i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('Product.sku', 'SKU') ?></th>
                                        <th width="15%"><i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('Warehouse.name', 'Warehouse') ?></th>
                                        <th width="15%"><i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('Invalert.reorder_point', 'Reorder point') ?></th>
                                        <th width="15%"><i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('Invalert.safety_stock', 'Safety stock') ?></th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($invalerts) { ?>
                                    <?php foreach ($invalerts as $invalert) { ?>
                                        <tr>
                                            <td><?php echo h($invalert['Product']['name']); ?></td>
                                            <td><?php echo h($invalert['Product']['sku']); ?></td>
                                            <td><?php echo h($invalert['Warehouse']['name']); ?></td>
                                            <td><?php echo h($invalert['Invalert']['reorder_point']); ?></td>
                                            <td><?php echo h($invalert['Invalert']['safety_stock']); ?></td>
                                            <td><?php echo $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i> Edit'), array('controller'=> 'invalerts', 'action' => 'create', $invalert['Invalert']['product_id'], $invalert['Invalert']['warehouse_id'], '?' => ['f' => 'a']), array('escape'=> false, 'class' => 'btn btn-xs blue', 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal')); ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php } else { ?>
                                    <tr>
                                        <td colspan="5"><h3 class="text-center text-warning">No data found</h3></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
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
                            <?php /*<div class="btn-group pageLimit">
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
                            </div> */ ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    $(function() {
        $('.limit').select2({
            minimumResultsForSearch: -1,
            width: '80px'
        });

        $('.limit').change(function(){
            $('#InvalertLimit').val($(this).val());
            $('#invalert-search').submit();
        });
    });
<?php $this->Html->scriptEnd(); ?>
</script>