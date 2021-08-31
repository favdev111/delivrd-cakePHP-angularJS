<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-at"></i>Locations
                        </div>
                        <div class="actions">
                            <?php echo $this->Html->link('<i class="fa fa-plus"></i><span class="hidden-480">New Location </span>', array('plugin' => false,'controller'=> 'warehouses','action' => 'add'),array('escape'=> false, 'class' => 'btn default yellow-stripe')); ?> 
                            <?php /*<div class="btn-group">
                                <a class="btn default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
                                    <i class="fa fa-share"></i> Tools <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li></li>
                                </ul>
                            </div>*/ ?>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            <div class="table-actions-wrapper">
                                <span></span>
                                <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                            </div>
                            <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th width="20%">Name</th>
                                        <th>Description</th>
                                        <th width="15%">Status</th>
                                        <th width="100px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($warehouses as $warehouse): ?>
                                <tr>
                                    <td><?php echo h($warehouse['Warehouse']['name']); ?></td>
                                    <td><?php echo h($warehouse['Warehouse']['description']); ?></td>
                                    <td>
                                        <?php /*<span class="label label-<?php echo (($warehouse['Warehouse']['status'] == 'active')?'success':'danger'); ?>"><?php echo h($warehouse['Warehouse']['status']); ?></span>*/ ?>
                                        <div class="bootstrap-switch-container">
                                            <input name="is_active" data-id="<?php echo $warehouse['Warehouse']['id']; ?>" class="make-switch" data-on-text="On" data-off-text="Off" type="checkbox" <?php echo (($warehouse['Warehouse']['status'] == 'active')?'checked':''); ?> >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
                                                <i class="fa fa-bolt"></i> Actions.. <i class="fa fa-angle-down"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right">
                                                <li><?php echo $this->html->link('<i class="fa fa-edit"></i> Edit', array('plugin' => false, 'controller' => 'warehouses', 'action' => 'edit',$warehouse['Warehouse']['id']), array('escape' => false)); ?></li>
                                                <li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $warehouse['Warehouse']['id']), array('escape'=> false), __('Are you sure you want to delete the location %s?', h($warehouse['Warehouse']['name']))); ?></li>
                                                <li><a href="<?php echo Router::url(array('controller' => 'warehouses', 'action' => 'editAddress', $warehouse['Warehouse']['id']), true); ?>" class="edit-form" data-remote="false" data-toggle="modal" data-label="Edit Address" id="<?php echo $warehouse['Warehouse']['id']; ?>" data-target="#delivrd-modal"><i class='fa fa-globe'></i> Address</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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
                                        echo $paginator->last("Last",array('tag' => 'li'));
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
</div>
<!-- END CONTENT -->

<div class="modal fade" id="delivrd-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="form-body">
      
            </div>
        </div>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
$(document).ready(function(){
    $('input[name="is_active"]').on('switchChange.bootstrapSwitch', function(event, state) {
        var location_id = $(this).data('id');
        if(state) {
            state = 1;
        } else {
            state = 0;
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo $this->Html->url(['controller'=>'warehouses', 'action'=>'isactive']); ?>',
            data: 'status='+ state+'&id='+location_id,
            dataType:'json',
            beforeSend: function() {

            },
            statusCode: {
                403: function() {
                    window.location.href = '/login';
                }
            },
            success:function (response, textStatus) {
                
            }
        });
        console.log($(this).data('id'));
        //console.log(event); // jQuery event
        console.log(state); // true | false
    });
});
<?php $this->Html->scriptEnd(); ?>