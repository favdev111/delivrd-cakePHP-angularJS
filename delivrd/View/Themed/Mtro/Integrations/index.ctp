<?php $this->AjaxValidation->active(); ?>
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
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-exchange"></i>Integrations
                            </div>
                            <div class="actions">
                            <?php echo $this->Html->link(__('<i class="fa fa-plus"></i>
                                <span class="hidden-480">New Integration </span>'), array('controller'=> 'integrations','action' => 'add'),array("class" => "btn default yellow-stripe", 'escape'=> false)); ?>
                            <!-- <?php if(Configure::read('OperatorName') != 'Delivrd') { ?>    
                            <a href="/integrations/add" class="btn default ">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-480">
                                New Integration </span>
                                </a>
                            <?php } ?> -->
                            <div class="btn-group">
                                    <a class="btn default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
                                    <i class="fa fa-share"></i> Tools <i class="fa fa-angle-down"></i>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                    
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <div class="table-actions-wrapper">
                                    <span>
                                    </span>
                                    
                                    <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                </div>
                                <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                                <thead>
                                <tr role="row" class="heading">
                                    <th>Ecommerce Platform</th>
                                    <th></th>
                                    <th>Last Sync Date</th>
                                    <th>Sales Channel</th>
                                    <th>Ecommerce manage inventory</th>
                                    <th>Actions</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <?php echo $this->Form->create('Integration', array(
                                        'url' => array_merge(
                                                array(
                                                    'action' => 'index'
                                                ),
                                                $this->params['pass']
                                            )
                                        )
                                    ); ?>
                                
                                    <td>
                                        <?php echo $this->Form->input('backend', array(
                                            'label' => false,
                                            'class'=>'form-control form-filter input-sm',
                                            'required' => false,
                                            'value' => (isset($this->params['named']['backend'])?$this->params['named']['backend']:'')
                                        )); ?>
                                    </td>
                                    <td></td>                   
                                    <td></td>
                                    <td></td>
                                    <td></td>                   
                                    <td>
                                        <div class="margin-bottom-5">
                                            <button class="btn btn-sm blue filter-submit margin-bottom" type="submit"><i class="fa fa-search"></i> Search</button>
                                        </div>
                                        
                                    </td>
                                    <?php  echo $this->Form->end(); ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($integrations as $integration): ?>
                                <tr>
                                
                                    <td>
                                        <?php echo h($integration['Integration']['backend']); ?>
                                    </td>
                                    <td>
                                        <?php if($integration['Integration']['backend'] == 'Shopify'){ ?>
                                        <?php echo $this->Html->link(__('<i class="fa fa-refresh" aria-hidden="true"></i> Sync Delivrd'), array('controller'=> 'integrations','action' => 'shopify', $integration['Integration']['id']),array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'class' => 'btn btn-sm default', 'rel' => 'modal-lg')); ?>
                                        <?php } elseif($integration['Integration']['backend'] == 'Woocommerce') { ?>
                                        <?php echo $this->Html->link(__('<i class="fa fa-refresh" aria-hidden="true"></i> Sync Delivrd'), array('controller'=> 'integrations','action' => 'import', $integration['Integration']['id']),array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'class' => 'btn btn-sm default')); ?>
                                        <?php } elseif($integration['Integration']['backend'] == 'Amazon') { ?>
                                        <?php echo $this->Html->link(__('<i class="fa fa-refresh" aria-hidden="true"></i> Sync Delivrd'), array('controller'=> 'integrations','action' => 'amazon', $integration['Integration']['id']),array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'class' => 'btn btn-sm default', 'rel' => 'modal-lg')); ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($integration['Transfer']) { ?>
                                        <?php echo $this->Admin->localTime("%Y-%m-%d %H:%M:%S", strtotime($integration['Transfer'][0]['created'])); ?>
                                        <?php } else { ?>
                                        <span class="label label-warning">No sync</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php echo h($integration['Schannel']['name']); ?>
                                    </td>
                                    <td>
                                        <div class="bootstrap-switch-container">
                                            <input name="is_ecommerce" data-id="<?php echo $integration['Integration']['id']; ?>" class="make-switch" data-on-text="Yes" data-off-text="No" type="checkbox" <?php echo (($integration['Integration']['is_ecommerce'])?'checked':''); ?> >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown">
                                                <i class="fa fa-bolt"></i> Actions.. <i class="fa fa-angle-down"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right">
                                                <li><?php echo $this->Html->link(__('<i class="fa fa-edit"></i> Edit'), array('controller'=> 'integrations','action' => 'edit', $integration['Integration']['id']),array('escape'=> false)); ?></li>
                                                <li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $integration['Integration']['id']), array('escape'=> false), __('Are you sure you want to delete integration %s?', $integration['Integration']['backend'])); ?></li>
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
                                        echo $paginator->last("Last",array('tag' => 'li')); ?>
                                    </ul>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="bs-callout bs-callout-info">
                                            <span class="font-blue-steel help">
                                                <i class="fa fa-info" aria-hidden="true"></i>
                                                <a href="https://delivrd.freshdesk.com/support/solutions/articles/17000091132--integrating-woocommerce-with-delivrd" target="_blank">Integrating WooCommerce with Delivrd Tutorials</a>
                                                <span class="separator">|</span>
                                                <a href="https://delivrd.freshdesk.com/support/solutions/articles/17000091131--integrating-shopify-with-delivrd" target="_blank">Integrating Shopify with Delivrd Tutorials</a>
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

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
$(document).ready(function(){
    $('input[name="is_ecommerce"]').on('switchChange.bootstrapSwitch', function(event, state) {
        var integration_id = $(this).data('id');
        if(state) {
            state = 1;
        } else {
            state = 0;
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo $this->Html->url(['controller'=>'integrations', 'action'=>'isecommerce']); ?>',
            data: 'isecommerce='+ state+'&id='+integration_id,
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