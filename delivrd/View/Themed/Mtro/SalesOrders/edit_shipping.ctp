<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Order Shipping Address <i class="fa fa-angle-right"></i>
                Order #<?php echo h($order['Order']['external_orderid']); ?>
            </h4>
        </div>
        <?php echo $this->Form->create('Order', ['ng-submit' => 'editShipping($event)', 'role'=>'form']); ?>
        <?php echo $this->Form->input('id', array('hidden' => true)); ?>
        <?php echo $this->Form->input('Address.id', array('hidden' => true)); ?>
        <div class="modal-body">
            <div class="alert alert-danger hide" id="modalFormMsg"></div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="form-group">
                        <label>Customer Name:</label>
                        <?php echo $this->Form->input('ship_to_customerid',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <?php echo $this->Form->input('email',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                    </div>
                    <div class="form-group">
                        <label>Address:</label>
                        <?php echo $this->Form->input('Address.street',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                    </div>

                    <div class="form-group">
                        <label>City:</label>
                        <?php echo $this->Form->input('Address.city',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                    </div>

                    <div class="form-group">
                        <label>Zip:</label>
                        <?php echo $this->Form->input('Address.zip',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                    </div>

                    <div class="form-group">
                        <label>Country:</label>
                        <?php echo $this->Form->input('Address.country_id',array('id' => 'country_id', 'label' => false, 'class' => 'form-control select2me', 'placeholder' => '', 'empty' => 'Select...')); ?>
                    </div>

                    <div class="form-group" id="state_id-div">
                        <label>State (US Only): </label>
                        <?php echo $this->Form->input('Address.state_id',array('id' => 'state_id','label' => false, 'class' => 'form-control input-large select2me','div' =>false,'empty' => 'Select...')); ?>
                    </div>

                    <div class="form-group" id="stateprovince-div">
                        <label>State/Province: </label>
                        <?php echo $this->Form->input('Address.stateprovince',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                    </div>

                    <div class="form-group">
                        <label>Phone Number: </label>
                        <?php echo $this->Form->input('Address.phone',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success"><i class="fa fa-plus"></i> Save</button>
            <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal" ng-click="close($event)">Close</button>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#state_id').select2();
        $('#country_id').select2();

        showProvince();
        $('#country_id').on('change', function(){
            showProvince();
        });
    });
    
    function showProvince() {
        if($('#country_id').val() == '1') {
            $('#state_id-div').show();
            $('#stateprovince-div').hide();
        } else {
            $('#state_id-div').hide();
            $('#stateprovince-div').show();
        }
    }
</script>
<style>
    .select2-drop-mask {z-index: 10051}
    .select2-drop {z-index: 10052}
</style>