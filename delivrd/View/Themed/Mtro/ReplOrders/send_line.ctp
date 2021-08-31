<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Receive Order Product <i class="fa fa-angle-right"></i>
                Order #<?php echo h($orderline['Order']['external_orderid']); ?>
            </h4>
        </div>

        <?php echo $this->Form->create('OrdersLine', array('class' => 'form-horizontal form-row-seperated', 'ng-submit' => 'sendProduct($event)', 'novalidate' => true)); ?>
        <div class="modal-body">
            <div class="alert alert-danger hide" id="modalFormMsg"></div>

            <div class="row">
                <div class="col-sm-3" style="margin-right:20px;"><img src="<?php  echo h($product['imageurl']) ?>" style="max-width: 150px;max-height: 150px" class="img-responsive img-thumbnail"></div>
                <div class="col-sm-8">
                    <h2><?php  echo h($product['name']) ?></h2>
                    <p>
                        <?php  echo h($product['description']) ?>
                        <div class="text-right">
                            <a href="<?php echo $this->Html->url(['controller' => 'products', 'action' => 'view', $product['id']]); ?>" class="mt-info uppercase btn default btn-outline">View</a>
                        </div>
                    </p>
                </div>
            </div>

            <div class="clearfix"></div><br>
            

            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->Form->input('id',array( 'hidden' => true )); ?>
                    <div class="portlet box grey-gallery">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-cogs" ></i> Details
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <div class="form-body">
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Order Quantity:
                                    </div>
                                    <div class="col-md-7 value">
                                        <?php   echo $this->Form->input('quantity',array( 'disabled' => 'disabled','label' => false, 'class' => 'form-control input-sm' )); ?>
                                    </div>
                                </div>
                                <?php if($this->Session->read('locationsactive') == 1 || 1) { ?>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        <label class="control-label">Location: <span class="required">*</span></label>
                                    </div>
                                    <div class="col-md-7 value">
                                        <?php  echo $this->Form->input('warehouse_id',array( 'label' => false, 'class' => 'form-control input-sm', 'min' => 0, 'required' => 'true' )); ?>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        <label class="control-label">Sent Quantity: <span class="required">*</span></label>       
                                    </div>
                                    <div class="col-md-7 value">
                                        <?php  echo $this->Form->input('receivedqty',array( 'label' => false, 'class' => 'form-control input-sm', 'min' => 0, 'required' => 'true')); ?>
                                    </div>
                                </div>
                                <?php if ($this->Session->read('managedamaged') == 1) { ?>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        <label class="control-label">Damaged Quantity: <span class="required">*</span></label>   
                                    </div>
                                    <div class="col-md-7 value">
                                        <?php  echo $this->Form->input('damagedqty',array( 'label' => false, 'class' => 'form-control input-sm', 'min' => 0, 'required' => 'true' )); ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success" id="issueLineBtn"><i class="fa fa-plus"></i> Save</button>
            <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal" ng-click="close($event)">Close</button>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#OrdersLineWarehouseId').select2({minimumResultsForSearch: -1});
    });
</script>
<style>
    .select2-drop-mask {z-index: 10051}
    .select2-drop {z-index: 10052}
</style>