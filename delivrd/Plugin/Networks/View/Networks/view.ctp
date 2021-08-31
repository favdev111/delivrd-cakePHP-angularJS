<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="NetworksView">
    <div class="page-content">
        
        <?php echo $this->Session->flash(); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-sitemap"></i> <?php echo ucfirst($network['Network']['type']); ?> <?php echo h($network['Network']['name']); ?> Network
                        </div>
                        <div class="actions">
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><?php echo $this->Html->link(__('<i class="icon-logout"></i> Leave Network'), array('controller'=> 'networks','action' => 'leave', $network['Network']['id']),array('escape'=> false)); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="portlet-body">
                        <div class="alert alert-info">
                            You have <strong><?php $this->Network->role($network['NetworksUser']['role']); ?></strong> role.<br>
                            You have access to <strong><a href="<?php echo $this->Html->url(['plugin'=>false, 'controller'=>'products', '?' => ['network_id' => $network['Network']['id']]]); ?>"><?php $this->Network->productCount($network['NetworksUser']['products']); ?></a></strong>
                        </div>
                        <table class="table table-bordered" ng=>
                            <thead>
                                <tr>
                                    <th class="col-sm-3">Model</th>
                                    <th class="col-sm-1">Access</th>
                                    <th class="col-sm-1">Inventory Location</th>
                                </tr>
                            </thead>
                            <tbody id="access_list">
                                <tr ng-repeat="row in access | orderBy:sortType:sortReverse" id="access_{{row.NetworksAccess.id}}">
                                    <td>{{formatModel(row.NetworksAccess.model)}}</td>
                                    <td>{{formatAccess(row.NetworksAccess.access)}}</td>
                                    <td>{{row.Warehouse.name}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    
    app.controller("NetworksView", function($scope, $http) {
        $scope.access = <?php echo json_encode($access); ?>;
        $scope.sortType = ['NetworksAccess.model', 'Warehouse.name'];

        $scope.formatAccess = function(access){
            if(access == 'r') {
                return 'View';
            }
            if(access == 'w') {
                return 'Update';
            }
            if(access == 'rw') {
                return 'View,Update';
            }
            return access;
        };
        $scope.formatModel = function(model){
            if(model == 'S.O.') {
                return 'Sales Orders';
            } else if(model == 'P.O.') {
                return 'Purchase Orders';
            } else {
                return model;
            }
        };
    });

<?php $this->Html->scriptEnd(); ?>
</script>