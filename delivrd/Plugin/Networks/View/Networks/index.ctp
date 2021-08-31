<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <?php echo $this->element('expirytext'); ?>

        <?php echo $this->Session->flash(); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption"><i class="fa fa-sitemap"></i> My Networks</div>

                        <div class="actions">
                            <div class="btn-group pull-right">
                                <?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Add Network'), array('plugin' => 'networks', 'controller' => 'networks', 'action' => 'create'), array('class' => 'btn default yellow-stripe', 'escape' => false, 'title' => 'Create New Network')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="portlet-body">
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover no-footer">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Network members</th>
                                        <th>Pending Invitation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($networks as $network) { ?>
                                    <tr>
                                        <td><?php echo h($network['Network']['name']); ?></td>
                                        <td><?php echo $this->Network->nType($network['Network']['type']); ?></td>
                                        <td><?php echo $this->Network->status($network['Network']['status']); ?></td>
                                        <td><?php echo $network['Network']['users_count']; ?> member(s)</td>
                                        <td><?php echo $network['Network']['invite_count']; ?></td>
                                        <td>
                                            <?php echo $this->Html->link('<i class="icon-users"></i>'. __('Invite'), ['controller' => 'networks', 'action' => 'invite', $network['Network']['id']], ['class' => 'btn btn-warning btn-xs', 'escape'=> false, 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal']); ?>
                                            <?php echo $this->Html->link(__('Users'), ['controller' => 'networks', 'action' => 'details', $network['Network']['id']], ['class' => 'btn btn-primary btn-xs']); ?>
                                            <?php echo $this->Html->link('<i class="fa fa-edit"></i>'. __('Edit'), ['controller' => 'networks', 'action' => 'edit', $network['Network']['id']], ['class' => 'btn btn-warning btn-xs', 'escape'=> false]); ?>
                                            <?php if($network['Network']['users_count'] == 0) { ?>
                                            <?php 
                                                echo $this->Form->postLink('<i class="icon-trash"></i>'. __('Delete'), array(
                                                            'action' => 'delete', 
                                                            $network['Network']['id']),
                                                            ['class' => 'btn btn-danger btn-xs', 'escape'=> false],
                                                            __('Are you sure you want to delete %s?', $network['Network']['name'])); 
                                            ?>
                                            <?php } ?>
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