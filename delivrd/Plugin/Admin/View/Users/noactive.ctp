<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="noActive">
    <div class="page-content">
    	
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <!-- Begin: life time stats -->
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user"></i>
                            No active users
                        </div>

                        <div class="actions">
                            <div class="btn-group">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-users"></i> User list'), array('plugin' => 'admin', 'controller' => 'users','action' => 'index'),array('escape'=> false)); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-ban"></i> No active users'), array('plugin' => 'admin', 'controller' => 'users','action' => 'noactive'),array('escape'=> false)); ?></li>
                                    <li role="separator" class="divider"></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-paypal"></i> Subscriptions'), array('plugin' => false, 'controller' => 'subscriptions','action' => 'index'),array('escape'=> false)); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            <table class="table table-hover">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th width="20%"> <i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('User.email', 'Email') ?> </th>
                                        <th width="20%"> <i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('User.firstname', 'Full name') ?> </th>
                                        <?php /*<th width="10%"> <i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('User.role', 'Role') ?> </th>*/ ?>
                                        <th width="10%"> <i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('User.last_login', 'Last Login') ?> </th>
                                        <th width="10%"> Products </th>
                                        <th width="10%"> Orders </th>
                                        <th width="10%"> Lines </th>
                                        <th width="10%"> Actions </th>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php if(count($data) != 0) { ?>
                                    <?php foreach ($data as $user) { ?>
                                    <tr role="row">
                                        <td><?php echo h($user['User']['email']) ?></td>
                                        <td><?php echo h($user['User']['firstname']) ?> <?php echo h($user['User']['lastname']) ?></td>
                                        <?php /*<td><?php echo h($user['User']['role']) ?></td> */?>
                                        <td><?php echo h($user['User']['last_login']) ?></td>
                                        <td><?php echo h($user['User']['product_conut']) ?></td>
                                        <td><?php echo h($user['User']['order_count']) ?></td>
                                        <td><?php echo h($user['User']['orderlines_count']) ?></td>
                                        <td>
                                            <?php echo $this->Form->postLink(__('<i class="fa fa-remove"></i> Delete User'), array('action' => 'remove', $user['User']['id']), array('escape'=> false),  __('Are you sure you want to delete user: %s?', $user['User']['email'])); ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>

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
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>