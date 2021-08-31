<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="FieldsList">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i> Custom Product Fields
                        </div>

                        <div class="actions">
                            <?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Add Field'),
                                array('controller'=> 'fields','action' => 'add'),
                                array('class' => 'btn default yellow-stripe add-delivrd', 'escape'=> false));
                            ?>
                            <div class="btn-group">
                                <?php echo $this->Html->link(__('<i class="fa fa-barcode"></i> Products'), array('controller'=> 'products','action' => 'index'),array('escape'=> false, 'class' => 'btn btn-fit-height green dropdown-toggle')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <?php if($fields) { ?>
                        <div class="table-container">
                            <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th>Field</th>
                                        <th>Description</th>
                                        <th>Value List</th>
                                        <th>Used</th>
                                        <th>Created</th>
                                        <th width="40px"> Actions </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fields as $key => $field) { ?>
                                    <tr role="row">
                                        <td>
                                            <?php if($field['Field']['is_filter']) { ?>
                                                <i class="fa fa-eye" aria-hidden="true" ></i>
                                            <?php } else { ?>
                                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                            <?php } ?>

                                            <?php echo h($field['Field']['name']); ?>
                                        </td>
                                        <td><?php echo h($field['Field']['description']); ?></td>
                                        <td>
                                            <?php if($field['FieldsValue']) { $options = array(); ?>
                                                <?php foreach ($field['FieldsValue'] as $value) {
                                                    $options[] = $value['value'];
                                                } ?>
                                                <?php echo h(implode(', ', $options)); ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a style="text-decoration: none;" ng-click="showUsage(<?php echo $field['Field']['id']; ?>)"><span class="badge badge-success"><?php echo $field['Field']['values_count']; ?></span> times</a>
                                            <?php /*style="text-decoration: none;" href="<?php echo $this->Html->url(array('controller' => 'fields', 'action' => 'used', $field['Field']['id'])); ?>" data-toggle="modal" data-target="#ajaxModal" rel="modal-lg" */ ?>
                                        </td>
                                        <td><?php echo date("F j, Y", strtotime($field['Field']['created'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a class="dropdown-toggle delivrd-act" href="#" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </a>
                                                <ul class="dropdown-menu pull-right" role="menu">
                                                    <li><?php echo $this->Html->link(__('<i class="fa fa-pencil"></i> Edit'), array('controller'=>'fields', 'action'=>'edit', $field['Field']['id']), array('escape' => false)); ?></li>
                                                    <li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $field['Field']['id']),array('escape'=> false), __('Are you sure you want to delete field %s?', $field['Field']['name'])); ?></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } else { ?>
                            <h3 class="text-info text-center">You have no any custom fields</h3>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>

<?php echo $this->Html->script('/app/Fields/index.js?v=0.0.1', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/app/Fields/usage.js?v=0.0.1', array('block' => 'pageBlock')); ?>