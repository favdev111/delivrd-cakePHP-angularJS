<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="userList">
    <div class="page-content">
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users"></i> Delivrd Users
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
                         <div class="tab-content no-space">
                            <?php #pr($transactions); ?>
                            <div class="tab-pane active" id="tab_thistory">
                                <table class="table table-hover no-footer" role="grid">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th><a ng-click="orderBy('User.email')" class="sort-link">Email <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('User.firstname')" class="sort-link">Fullname <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('User.last_login')" class="sort-link">Last Login <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('User.created')" class="sort-link">Created <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="line in users | orderBy:sortType:sortReverse">
                                            <td>{{ line.User.email }}</td>
                                            <td>{{ line.User.firstname }} {{ line.User.lastname }}</td>
                                            <td>{{ line.User.last_login }}</td>
                                            <td>{{ line.User.created }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a class="dropdown-toggle delivrd-act" href data-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-h"></i>
                                                    </a>
                                                    <ul class="dropdown-menu pull-right" role="menu">
                                                        <li><a href="<?php echo $this->Html->url(['controller' => 'users', 'action' => 'import_product']); ?>/{{ line.User.id }}">Import Products</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <p>Total found: {{totals}}</p>
                        <pagination max-size="30" total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="limit"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var limit = <?php echo $limit; ?>
<?php $this->Html->scriptEnd(); ?>

<?php echo $this->Html->script('/app/Admin/Users/index.js?v=0.0.1', array('block' => 'pageBlock')); ?>