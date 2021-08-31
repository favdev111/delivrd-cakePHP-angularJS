<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Import <?= $integration['Integration']['backend']; ?> (<?= $integration['Integration']['url']; ?>)</h4>
    </div>
    <div class="modal-body">
        <h3><i class="fa fa-barcode" style="font-size:22px"></i> Products</h3>
        <div id="productStatus"></div>
        <h3><i class="fa fa-shopping-cart" style="font-size:22px"></i> Orders</h3>
        <div id="orderStatus"></div>
    </div>
    <div class="modal-footer">
        <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
<script type="text/javascript">
    var $fn = {
            getProducts: function(callback) {
                $.ajax({
                    type: 'GET',
                    url: '<?php echo $this->Html->url(array('controller'=>'products', 'action'=>'importwoocom', $integration['Integration']['id'])); ?>',
                    data: '',
                    dataType:'json',
                    beforeSend: function() {
                        $('#productStatus').html('Strat to import products.<br>Please wait&hellip;<div class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>');
                        $btn = $('#closeBtn').button('loading')
                    },
                    success:function (r, status) {
                        $('#productStatus').html('<div><strong>Total found: </strong>'+ r.total_found +' ('+ r.variants +')</div>');
                        $('#productStatus').append('<div><strong>Added: </strong>'+ r.added +'</div>');
                        $('#productStatus').append('<div><strong>Updated: </strong>'+ r.updated +'</div>');
                        if(r.errors_count > 0){
                            $('#productStatus').append('<div><strong>Errors: </strong>'+ r.errors_count +'<div class="reason text-error" style="padding-left:20px;"></div></div>');
                            $.each(r.errors, function(key, value){
                                var error_str = '<div><strong>'+ value.title +': </strong> <i>'+ value.error +'</i>';
                                $.each(value.details, function(field, er) {
                                    error_str += '<div style="padding-left:20px">'+er[0]+'</div>';
                                });
                                $('#productStatus').find('div.reason').append(error_str + '</div>');
                            });
                            $('#productStatus').addClass('alert alert-warning');
                        } else {
                            $('#productStatus').addClass('alert alert-success');
                        }
                        if(callback) {
                            $fn[callback]();
                        }
                    }
                });
            },

            getOrders: function() {
                $.ajax({
                    type: 'GET',
                    url: '<?php echo $this->Html->url(array('controller'=>'orders', 'action'=>'importwoo', $integration['Integration']['id'])); ?>',
                    data: '',
                    dataType:'json',
                    beforeSend: function() {
                        $('#orderStatus').html('Strat to import orders.<br>Please wait&hellip;<div class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>');
                    },
                    success:function (r, status) {
                        //console.log(r);
                        $('#orderStatus').html('<div><strong>Total found: </strong>'+ r.total_found +'</div>');
                        $('#orderStatus').append('<div><strong>Added: </strong>'+ r.added +'</div>');
                        $('#orderStatus').append('<div><strong>Updated: </strong>'+ r.updated +'</div>');
                        if(r.errors_count > 0){
                            $('#orderStatus').append('<div><strong>Alerts: </strong>'+ r.errors_count +'<div class="reason text-error" style="padding-left:20px;"></div></div>');
                            $.each(r.errors, function(key, value){
                                if(value != undefined) {
                                    var ex_id = value.ex_id;
                                    delete value.ex_id;
                                    $.each(value, function(field, mess){
                                        $('#orderStatus').find('div.reason').append('<div>Order #'+key+' (ID: '+ex_id+'): <i>'+ mess[0]+'</i></div>');
                                    });
                                }
                            });
                            $('#orderStatus').addClass('alert alert-warning');
                        } else {
                            $('#orderStatus').addClass('alert alert-success');
                        }
                        $btn.button('reset');
                    }
                })
            }
        };
    $(document).ready(function() {
        var $btn = '';
        $fn['getProducts']('getOrders');
    });
        
</script>