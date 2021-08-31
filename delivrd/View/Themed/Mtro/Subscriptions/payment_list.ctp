<div class="page-content-wrapper">
    <div class="page-content">
        
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <?php #pr($subscriptions); ?>
                <!-- Begin: life time stats -->
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-paypal"></i> Payments
                        </div>
                        <div class="actions">
                            <div class="btn-group">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true" id>
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-list"></i> <span class="hidden-480">Subscriptions List</span>'), array('controller'=> 'subscriptions','action' => 'index'),array('escape'=> false)); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">

                        <div class="csv-div">
                            <div class="btn-toolbar">
                                <div class="btn-group">
                                    <?php echo $this->html->link('<i class="fa fa-undo"></i> Show All', array('plugin' => false, 'controller' => 'subscriptions', 'action' => 'payment_list'), array('id' => 'clear', 'class' => 'csv-icons import-btn', 'escape' => false, 'title' => 'Show all records')); ?>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="table-container">
                            <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th colspan="2"><?php echo $this->Paginator->sort('ext_id', 'Subscription ID'); ?></th>
                                        <th><?php echo __('User Role'); ?></th>
                                        <th><?php echo $this->Paginator->sort('payer_email', 'Payer Email'); ?></th>
                                        <th><?php echo $this->Paginator->sort('User.email', 'Delivrd User'); ?></th>
                                        <th><?php echo $this->Paginator->sort('last_txn_id', 'Last Payment ID'); ?></th>
                                        <th><?php echo $this->Paginator->sort('expiry_date', 'Paid Till'); ?></th>
                                        <th><?php echo $this->Paginator->sort('modified', 'Modified'); ?></th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($payments as $payment) { ?>
                                    <tr>
                                        <td width="10px">
                                            <?php if(!empty($payment['Payment']['payment_status'] == 'Completed')) { ?>
                                                <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                                            <?php } else { ?>
                                                <i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php echo h($payment['Payment']['subscription']); ?>
                                        </td>
                                        <td><?php echo h(ucfirst($payment['User']['email'])); ?></td>
                                        <td><?php echo h($payment['Payment']['transcation_id']); ?></td>
                                        <td><?php echo h($payment['Payment']['payment_status']); ?></td>
                                        <td><?php echo h($payment['Payment']['amount']); ?></td>
                                        <td><?php echo $this->Admin->localTime("%Y-%m-%d", strtotime($payment['Payment']['payment_date'])); ?></td>
                                        <td><?php echo $this->Admin->localTime("%Y-%m-%d %H:%I", strtotime($payment['Payment']['datetime'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a class="dropdown-toggle delivrd-act" href="#" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </a>
                                                <ul class="dropdown-menu pull-right">
                                                    <li><?php echo $this->Html->link(__('<i class="fa fa-list"></i> Memo'), array('controller'=> 'subscriptions','action' => 'memo', $payment['Payment']['id']),array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'rel' => 'modal-lg')); ?></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
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
                </div>
                <!-- End: life time stats -->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->