<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Confirm Order Release
            </h4>
        </div>
        <div class="modal-body release-btns">
            <div class="margin-bottom-15">
                <p>Would you like to view order details before releasing order ?</p>
                <?php echo $this->Html->link('Review order', array('controller'=>'salesorders', 'action'=>'details', $id), array('class'=>'btn btn-md blue')); ?>
                <a href="" class="btn btn-md blue" ng-click="orderConfirmRelease(<?php echo $id; ?>)">Release</a>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <label>
                        <input type="checkbox" name="show_message" id="show_message1" ng_click="acceptDontShow()" style="vertical-align: -4px;">
                        Don't show this message again
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>