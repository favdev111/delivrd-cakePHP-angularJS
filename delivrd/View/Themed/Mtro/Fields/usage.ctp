<div class="modal-dialog modal-lg" >
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Field "<?php echo $field['Field']['name']; ?>" Products</h4>
        </div>

        <div class="modal-body">
            <?php echo $this->Session->flash(); ?>

            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->Form->create('Product', array(
                            'class' => 'form-horizontal',
                            'novalidate' => true,
                        ));
                    ?>
                    <div class="col-md-2">
                        <label class="control-label">Search by: </label>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <?php echo $this->Form->input('searchby', array(
                                'label' => false,
                                'class'=>'code-scan form-control',
                                'placeholder' => 'Enter SKU or product name',
                                'ng-model' => 'query',
                                'ng-change' => 'applySearch()'
                            )); ?>
                            <span class="input-group-addon"><button id="keysearch" class="" title="Search" style="border:none"><i class="fa fa-search"></i></button></span>
                        </div>
                    </div>
                    <?php  echo $this->Form->end(); ?>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-12">
                    
                    <table class="table table-bordered">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th><?php echo $field['Field']['name']; ?></th>
                        </tr>
                        <tr ng-repeat="line in products">
                            <td><img class="productImage" src="{{line.Product.imageurl}}" height="32px" width="32px"></td>
                            <td>{{line.Product.name}}</td>
                            <td>{{line.Product.sku}}</td>
                            <td>{{line.FieldsData.value}}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <pagination total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="limit" max-size="maxSize"></pagination>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal" ng-click="close($event)">Close</button>
        </div>
    </div>
</div>