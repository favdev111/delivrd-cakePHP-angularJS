<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="countriesList">
    <div class="page-content">
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users"></i> Delivrd Countries
                        </div>
                        <div class="actions">
                            <span class="font-grey-cararra" style="padding-right: 20px">Admin Panel <sup>&copy;</sup></span>
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
                            <div class="tab-pane active" id="tab_thistory">
                                <table class="table table-hover no-footer" role="grid">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th><a ng-click="orderBy('Country.name')" class="sort-link">Name <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Country.code')" class="sort-link">Code <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                            <th><a ng-click="orderBy('Country.status')" class="sort-link">Status <i class="fa fa-sort text-muted" aria-hidden="true"></i></a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="line in countries | orderBy:sortType:sortReverse">
                                            <td>{{ line.Country.name }}</td>
                                            <td>{{ line.Country.code }}</td>
                                            <td>
                                                <a style="text-decoration: none" title="Click to change status" ng-click="changeStatus(line.Country.id)" ng-bind-html="status(line.Country.status)">{{ status(line.Country.status) }}</a>
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

<?php echo $this->Html->script('/app/Admin/Countries/index.js?v=0.0.1', array('block' => 'pageBlock')); ?>