<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
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
                            <i class="fa fa-paypal"></i> Subscriptions
                        </div>
                        <div class="actions">
                            <?php echo $this->Html->link(__('<i class="fa fa-plus"></i> <span class="hidden-480">New Subscription</span>'), array('controller'=> 'subscriptions','action' => 'add'),array("class" => "btn default add-delivrd", 'escape'=> false)); ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true" id>
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-list"></i> <span class="hidden-480">Payments List</span>'), array('controller'=> 'subscriptions','action' => 'payment_list'),array('escape'=> false)); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
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
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($subscriptions as $subscription) { ?>
                                    <tr>
                                        <td width="10px">
                                            <?php if(!empty($subscription['User']['email'])) { ?>
                                                <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                                            <?php } else { ?>
                                                <i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php echo h($subscription['Subscription']['ext_id']); ?>
                                        </td>
                                        <td><?php echo h(ucfirst($subscription['User']['role'])); ?></td>
                                        <td><?php echo h($subscription['Subscription']['payer_email']); ?></td>
                                        <td><?php echo h($subscription['User']['email']); ?></td>
                                        <td><?php echo h($subscription['Subscription']['last_txn_id']); ?></td>
                                        <td><?php echo $this->Admin->localTime("%Y-%m-%d", strtotime($subscription['Subscription']['expiry_date'])); ?></td>
                                        <td><?php echo $this->Admin->localTime("%Y-%m-%d %H:%I", strtotime($subscription['Subscription']['modified'])); ?></td>
                                        <td><?php echo h($subscription['Subscription']['status']); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a class="dropdown-toggle delivrd-act" href="#" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </a>
                                                <ul class="dropdown-menu pull-right">
                                                    <li><?php echo $this->Html->link(__('<i class="fa fa-edit"></i> Edit'), array('controller'=> 'subscriptions','action' => 'edit', $subscription['Subscription']['id']),array('escape'=> false)); ?></li>
                                                    <li><?php echo $this->Html->link(__('<i class="fa fa-paypal"></i> Payments'), array('controller'=> 'subscriptions','action' => 'payment_list', $subscription['Subscription']['ext_id']),array('escape'=> false)); ?></li>
                                                    <li><?php echo $this->Html->link(__('<i class="fa fa-list"></i> Memo'), array('controller'=> 'subscriptions','action' => 'memo', $subscription['Subscription']['id']),array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal', 'rel' => 'modal-lg')); ?></li>
                                                    <li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $subscription['Subscription']['id']), array('escape'=> false), __('Are you sure you want to delete subscription %s?', $subscription['Subscription']['ext_id'])); ?></li>
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

                            <div class="info margin-botom-20">
                                <p class="text-muted"><i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>  - subscription work correct. User found for this subscription.</p>
                                <p class="text-muted"><i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i> - subscription work <strong>not</strong> correct. We can't find delivrd user for this subscription. All users which subscribe before our PayPal updates must be linked manual with edit button.
                            </div><br>

                            <blockquote>
                                <h3 style="margin-top:0;">How to enable IPN in your PayPal account</h3>
                                <p>
                                    - In your PayPal merchant account settings, enable the IPN Message Service and specify the URL of your listener.<br>
                                    <samll class="text-info">(see: https://developer.paypal.com/docs/classic/ipn/ht_ipn/)</samll><br>
                                    URL must be set to:
                                    <code class="text-danger"><?php echo $this->Html->url('/paypal/payment/ipn', true); ?></code>
                                </p>
                                <p>Founded Roles: <code>`Free`, `Paid`, `Trial`, `extend`</code></p>
                            </blockquote>
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