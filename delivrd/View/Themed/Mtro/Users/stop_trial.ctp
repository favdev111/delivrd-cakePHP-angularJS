<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><i class="fa fa-stop"></i> Switch to Free</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <?php echo $this->Form->create('User'); ?>
                <?php echo $this->Form->input('User.locationsactive',array('type' => 'hidden', 'value'=>0)); ?>
                <?php echo $this->Form->input('User.paid',array('type' => 'hidden', 'value'=>0)); ?>
                <?php echo $this->Form->input('User.role',array('type' => 'hidden', 'value'=>'free')); ?>
                <div class="col-md-12">
                    <h4 class="text-center font-blue-soft"><strong>You are about to switch to a free plan.</strong></h4>
                    <div style="font-size: 15px">
                        Free plan is limited to managing 10 products in a single inventory location.<br>
                        
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <?php echo ($productcount > 10)?'<i class="fa fa-exclamation-triangle font-red-soft"></i>':'<i class="fa fa-check font-green"></i>'; ?>
                        You currently have <strong class="font-blue-steel"><?php echo $productcount; ?></strong> product(s).<br>
                        
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <?php echo ($locationscount > 1)?'<i class="fa fa-exclamation-triangle font-red-soft"></i>':'<i class="fa fa-check font-green"></i>'; ?>
                        You currently have <strong class="font-blue-steel"><?php echo $locationscount; ?></strong> location(s).<br>
                        
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <?php echo ($po_count > 0 || $so_count > 0)?'<i class="fa fa-exclamation-triangle font-red-soft"></i>':'<i class="fa fa-check font-green"></i>'; ?>
                        You have created <strong class="font-blue-steel"><?php echo $po_count; ?></strong> number of purchase orders
                        and <strong class="font-blue-steel"><?php echo $so_count; ?></strong> number of sales orders<br>

                        <div class="text-center" style="padding: 10px 0;">
                            <strong class="font-red-soft">If you switch to the free plan, you will no longer have access to any purchase order or sales order you have created.</strong>
                        </div>
                        <div class="text-center">
                            Also, you must deactivate products so that number<br> of active products is less than 11
                        </div>
                    </div><br>
                    <div class="text-center">
                        <button class="btn btn-lg blue-steel" id="startTrialBtn" <?php echo ($productcount > 10)?'disabled':''; ?>>
                            <i class="fa fa-stop" aria-hidden="true"></i>
                            Downgrade to Free
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#UserStopTrialForm').submit(function(){
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
                        toastr["success"](r.msg);
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr["error"](r.msg);
                    }
                }
            });
            return false;
        })
    });
</script>