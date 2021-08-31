<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Invite user</h4>
    </div>
    <?php echo $this->Form->create('NetworksInvite', array('role'=>'form')); ?>
    <?php echo $this->Form->hidden('network_id', array('value'=>$network['Network']['id'])); ?>
    <?php echo $this->Form->hidden('status', array('value'=>1)); ?>
    <?php echo $this->Form->hidden('role', array('value' => 5)); //Custom Role ?>
    <div class="modal-body">
        <div class="portlet-body form">
        
            <div class="form-body">
                <div class="form-group">
                    <label>Email address of invited user</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                        <?php echo $this->Form->input('email', array('class'=>'form-control', 'div'=>false, 'label'=>false)); ?>
                    </div>
                </div>

                <?php /*
                <div class="form-group">
                    <?php echo $this->Form->input('role', array('options'=>$roles, 'class'=>'form-control', 'div'=>false)); ?>
                </div> */ ?>

                <div class="form-group">
                    <label>Inventory Locations <small class="text-muted">Select inventory locations invited user can access</small></label>
                    <?php echo $this->Form->input('warehouse_id', array(
                            'options' => $warehouse,
                            'data-placeholder' => 'All Locations',
                            'empty' => 'All Locations',
                            'multiple' => true,
                            'class'=>'form-control',
                            'div'=>false,
                            'id' => 'warehouse_id',
                            'label'=>false
                        )); ?>
                </div>

                <div class="form-group">
                    <label>Assign Products <small class="text-muted">Select products invited user can access</small></label>
                    <?php echo $this->Form->input('product_id',array(
                            'label' => false,
                            'data-placeholder' => 'All Products',
                            'empty' => 'All Products',
                            'multiple' => true,
                            'class' => 'form-control',
                            'id' => 'select_product_id',
                            'div' =>false
                        )); ?>
                </div>

                <div class="form-group">
                    <label>Assign Channels <small class="text-muted">Select sales channels invited user can access</small></label>
                    <?php echo $this->Form->input('schannel_id',array(
                            'label' => false,
                            'data-placeholder' => 'All Channels',
                            'empty' => 'All Channels',
                            'multiple' => true,
                            'class' => 'form-control',
                            'id' => 'select_channel_id',
                            'div' =>false
                        )); ?>
                </div>

                <div class="form-group">
                    <label>Limited Access</label>
                    <div class="md-checkbox">
                    <?php echo $this->Form->input('limited', array(
                            'type' => 'checkbox',
                            'class' => 'md-check',
                            'div' => false,
                            'label' => false,
                            'id' => 'limited',
                            'checked' => true
                        )); ?>
                    <label for="limited">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span>
                        Invited user can not create their own products, locations, suppliers
                    </label>
                    </div>
                </div>
            </div>
        
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="submitBtn" class="btn btn-success">Send</button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<script type="text/javascript">
    //var warehouse = json_encode($warehouse);
    $(document).ready(function() {
        $('select#warehouse_id').select2({minimumResultsForSearch: -1});
        $('select#select_product_id').select2();
        $('select#select_channel_id').select2({minimumResultsForSearch: -1});
        $('select#NetworksInviteRole').select2({minimumResultsForSearch: -1});

        $('select#select_product_id').on("select2-selecting", function (e) {
            if (e.val == "") {
                $("#select_product_id").select2("val", "");
            } else {
                new_data = $.grep($('#select_product_id').select2('data'), function (value) {
                    return value['id'] != "";
                });
                $('#select_product_id').select2('data', new_data);
            }
        });
        $('#limited').click(function(){
            if(!$(this).attr('checked')) {
                return confirm('Are you sure you want to allow invited user to create their own master data?');
            }
        });
        /*$('#NetworksInviteInviteForm').submit(function(){
            var form = $(this);
            var $btn = $('#submitBtn').button('loading');
            $.ajax({
                type: 'POST',
                url: '<?php echo $this->Html->url(array('controller'=>'networks', 'action'=>'invite')); ?>',
                data: form.serialize(),
                dataType:'json',
                beforeSend: function() {
                    
                },
                success:function (r, status) {
                    console.log(r);
                    $('#ajaxModal').modal('hide');
                    $btn.button('reset');
                }
            });
            return false;
        });*/
    });
</script>