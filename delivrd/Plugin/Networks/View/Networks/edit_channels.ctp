<div class="modal-content" ng-controller="NetworksProduct">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
            <i class="fa fa-sitemap" style="font-size:18px"></i> <?php echo h($network['Network']['name']); ?> Network <i class="fa fa-angle-right"></i>
            <small> Available Channels for <strong><?php echo h($network['User']['email']); ?></strong></small>
        </h4>
    </div>
    <?php echo $this->Form->create('NetworksUser', ['role'=>'form']); ?>
    <div class="modal-body">
        <div class="alert alert-danger hide" id="networkFormMsg"></div>
        <?php echo $this->Form->hidden('id', ['value' => $network['NetworksUser']['id']]); ?>
        <div class="col-md-12">
            <div class="form-group">
                <label>Channels</label>
                <?php echo $this->Form->input('schannel_id',array(
                    'label' => false,
                    'data-placeholder' => 'All Channels',
                    'empty' => 'All Channels',
                    'multiple' => true,
                    'class' => 'form-control',
                    'id' => 'select_schannel_id',
                    'div' =>false,
                    'value' => $schannel_list
                )); ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-success"><i class="fa fa-plus"></i> Save</button>
        <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>

<script>
    $(document).ready(function(){
        $('#select_schannel_id').select2();
        $('#select_schannel_id').on("select2-selecting", function (e) {
            if (e.val == "") {
                $("#select_schannel_id").select2("val", "");
            } else {
                new_data = $.grep($('#select_schannel_id').select2('data'), function (value) {
                    return value['id'] != "";
                });
                $('#select_schannel_id').select2('data', new_data);
            }
        });
        $('#NetworksUserEditChannelsForm').submit(function(){
            var $form = $('#NetworksUserEditChannelsForm');
            $('#networkFormMsg').html('').addClass('hide');
            $.ajax({
                type: 'POST',
                url: $form.attr('action'),
                data: $form.serialize(),
                dataType:'json',
                beforeSend: function() {
                    //$btn = $('#closeBtn').button('loading')
                },
                success:function (data, status) {
                    if (data.action == 'success') {
                        $('#ajaxModal').modal('hide');
                    } else {
                        $.each(data.errors, function(key, value){
                            $.each(value, function(k, m){
                                $('#networkFormMsg').append(m);
                            });
                        });
                        $('#networkFormMsg').removeClass('hide');
                    }
                }
            });
            return false;
        });
    });
</script>