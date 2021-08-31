<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <h1 class="page-title"><?php echo h($network['Network']['name']); ?> <small><?php echo ucfirst(h($network['Network']['type'])); ?> Network</small></h1>
            
        <?php echo $this->Session->flash(); ?>

        <!-- BEGIN PAGE CONTENT-->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#usersTab" data-toggle="tab">Users</a></li>
            <li><a href="#invitesTab" data-toggle="tab">Pending Invites</a></li>

            <li class="pull-right">
                <?php echo $this->Html->link(__('<i class="icon-settings"></i> Network Settings'), array('controller'=> 'networks','action' => 'edit', $network['Network']['id']),array('escape'=> false)); ?></li>
            </li>
            <li class="pull-right">
                <?php echo $this->html->link(__('<i class="icon-users"></i> Invite User'), array('plugin' => 'networks', 'controller' => 'networks', 'action' => 'invite', $network['Network']['id']), array('escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal')); ?>
            </li>
        </ul>

        <div class="tab-content">
            <div class="row tab-pane fade active in" id="usersTab">
                <div class="col-md-12">
                    
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-sitemap"></i> Network Users
                            </div>
                        </div>

                        <div class="portlet-body">
                            <div class="table-container">
                                <table class="table table-striped table-bordered table-hover no-footer">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th>Email</th>
                                            <?php /*<th>Role</th>*/ ?>
                                            <th>Channels</th>
                                            <th>Products</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($users as $user) { ?>
                                        <tr>
                                            <td><?php echo h($user['User']['email']); ?></td>
                                            <?php /*<td><?php echo $this->Network->role($user['NetworksUser']['role']); ?></td>*/ ?>
                                            <td><?php echo $this->Network->channels($user['NetworksUser']['schannel'], $channels); ?></td>
                                            <td><?php echo $this->Network->productCount($user['NetworksUser']['products']); ?></td>
                                            <td><?php echo $this->Network->nStatus($user['NetworksUser']['status']); ?></td>
                                            <td>
                                                <?php if($user['NetworksUser']['status'] == 1) { ?>
                                                <?php echo $this->Html->link(__('Edit'), ['controller' => 'networks', 'action' => 'edit_access', $network['Network']['id'], $user['NetworksUser']['id']], ['class' => 'btn btn-primary btn-xs']); ?>
                                                <?php echo $this->Html->link(__('Products'), ['controller' => 'networks', 'action' => 'edit_products', $network['Network']['id'], $user['NetworksUser']['id']], ['class' => 'btn btn-primary btn-xs', 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal']); ?>
                                                <?php echo $this->Html->link(__('Channels'), ['controller' => 'networks', 'action' => 'edit_channels', $network['Network']['id'], $user['NetworksUser']['id']], ['class' => 'btn btn-primary btn-xs', 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal']); ?>
                                                <?php } ?>
                                                <?php echo $this->Form->postLink(__('Remove'), 
                                                    ['controller' => 'networks', 'action' => 'delete_user', $network['Network']['id'], $user['NetworksUser']['id']],
                                                    ['class' => 'btn btn-danger btn-xs'],
                                                    __('Are you sure you want to delete %s from your network?', $user['User']['email'])
                                                ); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row tab-pane fade" id="invitesTab">
                <div class="col-md-12">
                    <div class="portlet">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-sitemap"></i> Latest Invites
                            </div>
                        </div>

                        <div class="portlet-body">
                            <div class="table-container">
                                <table class="table table-striped table-bordered table-hover no-footer">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th>Email</th>
                                            <th>Invitation Link</th>
                                            <?php /*<th>Role</th>*/ ?>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($network['NetworksInvite'] as $invite) { ?>
                                        <tr>
                                            <td><?php echo h($invite['email']); ?></td>
                                            <td><?php echo $this->Html->url(array('controller' => 'networks', 'action' => 'signup', $invite['hash']), true); ?></td>
                                            <?php /*<td><?php echo $this->Network->role($invite['role']); ?></td>*/ ?>
                                            <td><?php echo $this->Network->iStatus($invite['status']); ?></td>
                                            <td>
                                                <?php /*echo $this->Html->link(__('Edit Access'), ['controller' => 'networks', 'action' => 'edit_inv_access', $invite['id']], ['class' => 'btn btn-primary btn-xs']);*/ ?>
                                                <?php echo $this->Form->postLink(__('Delete'), 
                                                    ['controller' => 'networks', 'action' => 'delete_inv_user', $invite['id']],
                                                    ['class' => 'btn btn-danger btn-xs'],
                                                    __('Are you sure you want to delete invitation for user %s?', $invite['email'])
                                                ); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>