<style>
    .checker{margin-top: 8px !important;}
</style>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="NetworksAccess">
    <div class="page-content">

        <h1 class="page-title"><?php echo h($network['Network']['name']); ?> <small><?php echo ucfirst($network['Network']['type']); ?> Network</small></h1>

        <?php echo $this->Session->flash(); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-sitemap"></i> Access for <?php echo h($network['User']['email']); ?>
                                </div>
                                <div class="actions">
                                    <?php echo $this->Html->link(__('Sales Channels'), ['controller' => 'networks', 'action' => 'edit_channels', $network['NetworksUser']['network_id'], $network['NetworksUser']['id']], ['class' => 'btn btn-warning btn-xs', 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal']); ?>
                                    <?php echo $this->Html->link(__('Products'), ['controller' => 'networks', 'action' => 'edit_products', $network['NetworksUser']['network_id'], $network['NetworksUser']['id']], ['class' => 'btn btn-warning btn-xs', 'data-toggle'=>'modal', 'data-target'=>'#ajaxModal']); ?>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="alert alert-danger hide" id="networkFormMsg"></div>
                                <?php echo $this->Form->create('NetworksAccess', ['ng-submit' => 'addRow($event)', 'role'=>'form']); ?>
                                    <?php echo $this->Form->hidden('network_id', ['value' => $network['Network']['id']]); ?>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Module</label>
                                            <?php echo $this->Form->input('model', array(
                                                'options' => ['Inventory' => 'Inventory', 'S.O.' => 'S.O.', 'Shipments' => 'Shipments'/*, 'P.O.' => 'P.O.', 'Serials' => 'Serials'*/],
                                                'placeholder' => 'Select Warehouse',
                                                'class' => 'form-control',
                                                'div' => false,
                                                'label' => false
                                            )); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <label>View</label><br>
                                        <input type="checkbox" name="NetworksAccess[access][]" value="r">
                                    </div>
                                    <div class="col-sm-1">
                                        <label>Update</label><br>
                                        <input type="checkbox" name="NetworksAccess[access][]" value="w">
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Warehouse</label>
                                            <?php echo $this->Form->input('warehouse_id', array(
                                                'options' => $warehouse,
                                                'class' => 'form-control',
                                                'placeholder' => 'Select Warehouse',
                                                'div' => false,
                                                'label' => false
                                            )); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button class="btn btn-success"><i class="fa fa-plus"></i> Add</button>
                                        </div>
                                    </div>
                                <?php echo $this->Form->end(); ?>

                                <table class="table table-bordered" ng=>
                                    <thead>
                                        <tr>
                                            <th class="col-sm-3">Module</th>
                                            <th class="col-sm-1">Access</th>
                                            <th class="col-sm-1">Warehouse</th>
                                            <th class="col-sm-2">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="access_list">
                                        <tr ng-repeat="row in access | orderBy:sortType:sortReverse" id="access_{{row.NetworksAccess.id}}">
                                            <td>{{row.NetworksAccess.model}}</td>
                                            <td>{{formatAccess(row.NetworksAccess.access)}}</td>
                                            <td>{{row.Warehouse.name}}</td>
                                            <td>
                                                <button class="btn btn-danger btn-xs" ng-click="removeAccess(row.NetworksAccess.id)">Remove</button>
                                                <?php /*href="<?php echo $this->Html->url(['controller' => 'networks', 'action' => 'remove_access']); ?>/{{row.NetworksAccess.id}}"*/ ?>
                                            </td>
                                        </tr>
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
<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    app.config(function ($httpProvider) {
        $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        //$httpProvider.defaults.headers.common['Content-Type'] = 'application/x-www-form-urlencoded';
    });
    app.controller("NetworksAccess", function($scope, $http) {
        $scope.access = <?php echo json_encode($access); ?>;
        
        $scope.sortType = ['NetworksAccess.model', 'Warehouse.name'];

        $scope.addRow = function(e){
            e.preventDefault();
            var $form = $('#NetworksAccessEditAccessForm');
            $('#networkFormMsg').html('').addClass('hide');
            $http({
                method  : 'POST',
                url     : $form.attr('action'), //'<?php echo $this->Html->url(['controller' => 'networks', 'action' => 'edit_access', $network['NetworksUser']['id']]); ?>',
                data    : $form.serialize(),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                    var is_update = false;
                    if (data.row.NetworksAccess.id ) {
                        angular.forEach($scope.access, function(value,index){
                            if(value.NetworksAccess.id == data.row.NetworksAccess.id) {
                                $scope.access.splice(index,1);
                                $scope.access.splice(index,0,data.row);
                                is_update = true;
                            }
                        });
                    }
                    if(!is_update) {
                        $scope.access.push(data.row);
                    }
                } else {
                    $.each(data.errors, function(key, value){
                        $.each(value, function(k, m){
                            $('#networkFormMsg').append(m);
                        });
                    });
                    $('#networkFormMsg').removeClass('hide');
                }
            });
            return false;
        };

        $scope.removeAccess = function(access_id) {
            $('#networkFormMsg').html('').addClass('hide');
            $http({
                method  : 'POST',
                url     : '<?php echo $this->Html->url(['controller' => 'networks', 'action' => 'remove_access']); ?>/'+access_id,
                data    : {access_id: access_id},
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data.action == 'success') {
                    angular.forEach($scope.access, function(value,index){
                        if(value.NetworksAccess.id == access_id) {
                            $scope.access.splice(index,1);
                            $scope.access.splice(index,0);
                        }
                    });
                } else {
                    $('#networkFormMsg').append('Can\'t remove this row now. Please try againe.');
                    $('#networkFormMsg').removeClass('hide');
                }
            });
            return false;
        }

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
            return dateOut;
        };
    });

    $(document).ready(function(){
        $('#NetworksAccessModel').select2({
            minimumResultsForSearch: -1
        });
        $('#NetworksAccessWarehouseId').select2({
            minimumResultsForSearch: -1
        });
    });
<?php $this->Html->scriptEnd(); ?>
</script>