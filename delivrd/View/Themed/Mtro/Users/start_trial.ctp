<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Upgrade your plan</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <?php echo $this->Form->create('User'); ?>
                <?php echo $this->Form->input('User.locationsactive',array('type' => 'hidden', 'value'=>1)); ?>
                <?php echo $this->Form->input('User.paid',array('type' => 'hidden', 'value'=>1)); ?>
                <?php echo $this->Form->input('User.role',array('type' => 'hidden', 'value'=>'trial')); ?>
                <div class="col-md-12">
                    <h4 class="text-center font-blue-soft"><strong>You are using Delivrd's free plan.</strong></h4>
                    <p style="font-size: 15px">
                        <?php if($type == 'so') { ?>
                            To manage Sales Order in Delivrd  you will have to upgrade to Unlimited plan.<br>
                        <?php } else if($type == 'po') { ?>
                            To manage Purchase Order in Delivrd  you will have to upgrade to Unlimited plan.<br>
                        <?php } else { ?>
                            To use more of Delivrd's functionality you need to upgrade your plan.<br>
                        <?php } ?>
                        If you choose to upgrade to Unlimited plan, you will be able to:<br>
                        
                        &nbsp;&nbsp;&nbsp;&nbsp;- Manage unlimited number of products in unlimited number of inventory locations<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;- Manage purchase orders<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;- Manage sales orders<br>
                        and much more.
                    </p>
                    <div class="text-center">
                        <button class="btn btn-lg blue-steel" id="startTrialBtn">
                            <i class="fa fa-check" aria-hidden="true"></i>
                            Start 30 days free trial
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#UserStartTrialForm').submit(function(){
            var $form = $(this);
            $.ajax({
                type: 'POST',
                url: $form.attr('action'),
                data: $form.serialize(),
                dataType:'json',
                beforeSend: function() {
                    $btn = $('#startTrialBtn').button('loading')
                },
                success:function (r, status) {
                    if(r.action == 'success') {
                        toastr["success"]('Trial period started');
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                        
                    }
                }
            });
            return false;
        })
    });
</script>