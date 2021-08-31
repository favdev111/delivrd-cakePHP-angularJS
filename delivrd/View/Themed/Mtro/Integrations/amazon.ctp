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
                    url: '<?php echo $this->Html->url(array('controller'=>'products', 'action'=>'generateAmazonReport', $integration['Integration']['id'])); ?>',
                    data: '',
                    dataType:'json',
                    beforeSend: function() {
                        $('#productStatus').html('Generate Amazon Report.<br>Please wait&hellip;<div class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>');
                        $btn = $('#closeBtn').button('loading')
                    },
                    success:function (r, status) {
                        if(r.status == 'success') {
                            //$('#productStatus').append('<div class="text-center"><a href="<?php echo $this->Html->url(['controller'=>'products', 'action'=>'importAmazon']); ?>/'+r.reports.reportId+'" class="btn btn-info">Import products</a></div>');

                            if(r.reports.reportId != undefined ) {
                                $('#productStatus').html('<div><h3>Product Report generated</h3><strong>Report Id: </strong>'+ r.reports.reportId +'</div>');
                                $('#productStatus').append('<div id="importProductsSt"><h3 class="text-center">Import products</h3>Please wait&hellip;<div class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>');
                                $('#productStatus').addClass('alert alert-success');
                                $fn['importProducts'](r.reports.reportId, callback);
                            } else {
                                $('#productStatus').html('<div><h3>Product Report in process</h3></div>');
                                $('#productStatus').append('<div>You need wait some time before Amazon prepare report. Please try againe some later.</div>').addClass('alert alert-info');
                                if(callback) {
                                    $fn[callback](0);
                                }
                            }
                        } else {
                            $('#productStatus').html('<pre>'+ r.error +'</pre>').addClass('alert alert-danger');
                        }
                    }
                });
            },

            importProducts: function(reportId, callback) {
                $.ajax({
                    type: 'GET',
                    url: '<?php echo $this->Html->url(array('controller'=>'products', 'action'=>'importAmazon', $integration['Integration']['id'])); ?>/'+reportId,
                    data: '',
                    dataType:'json',
                    beforeSend: function() {

                    },
                    success:function (r, status) {
                        if(r.status == 'success') {
                            $('#importProductsSt').html('<div><h3>Product Import Finished</h3></div>');
                            $('#importProductsSt').append('<div><strong>Total found: </strong>'+ r.total_found +'</div>');
                            $('#importProductsSt').append('<div><strong>Added: </strong>'+ r.added +'</div>');
                            $('#importProductsSt').append('<div><strong>Updated: </strong>'+ r.updated +'</div>');
                            if(r.errors_count > 0){
                                $('#importProductsSt').append('<div><strong>Errors: </strong>'+ r.errors_count +'<div class="reason text-error" style="padding-left:20px;"></div></div>');
                                $.each(r.errors, function(key, value){
                                    var error_str = '<div><strong>'+ value.title +': </strong> <i>'+ value.error +'</i>';
                                    $.each(value.details, function(field, er) {
                                        error_str += '<div style="padding-left:20px">'+er[0]+'</div>';
                                    });
                                    $('#importProductsSt').find('div.reason').append(error_str + '</div>');
                                });
                                $('#importProductsSt').addClass('alert alert-warning');
                                
                            } else {
                                $('#importProductsSt').addClass('alert alert-success');
                            }
                            if(callback) {
                                $fn[callback](1);
                            }
                        } else {
                            $('#importProductsSt').html('<pre>'+ r.error +'</pre>').addClass('alert alert-danger');
                        }
                    }
                });
            },

            getOrders: function(is_import) {
                $.ajax({
                    type: 'GET',
                    url: '<?php echo $this->Html->url(array('controller'=>'orders', 'action'=>'generateAmazonReport', $integration['Integration']['id'])); ?>',
                    data: '',
                    dataType:'json',
                    beforeSend: function() {
                        $('#orderStatus').html('Generate order report.<br>Please wait&hellip;<div class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>');
                    },
                    success:function (r, status) {
                        if(r.status == 'success') {
                            if(r.reports.reportId != undefined ) {
                                $('#orderStatus').html('<div><h3>Order Report generated</h3><strong>Report Id: </strong>'+ r.reports.reportId +'</div>');
                                $('#orderStatus').append('<div id="importOrdersSt"><h3 class="text-center">Import orders</h3><div class="text-muted">This function not available at this moment.</div>');
                                $('#orderStatus').addClass('alert alert-success');
                                if(is_import) {
                                    //$fn[importOrders](r.reports.reportId);
                                }
                            } else {
                                $('#orderStatus').html('<div><h3>Order Report in process</h3></div>');
                                $('#orderStatus').append('<div>You need wait some time before Amazon prepare report. Please try againe some later.</div>').addClass('alert alert-info');
                            }
                        } else {
                            $('#orderStatus').html('<pre>'+ r.error +'</pre>').addClass('alert alert-danger');
                        }
                        $btn.button('reset');
                    }
                })
            },

            importOrders: function() {
                $.ajax({
                    type: 'GET',
                    url: '<?php echo $this->Html->url(array('controller'=>'orders', 'action'=>'generateAmazonReport', $integration['Integration']['id'])); ?>',
                    data: '',
                    dataType:'json',
                    beforeSend: function() {
                        $('#importOrdersSt').html('Generate order report.<br>Please wait&hellip;<div class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>');
                    },
                    success:function (r, status) {
                        if(r.status == 'success') {
                            $('#orderStatus').html('<div><h3>Order Report generated</h3><strong>Report Id: </strong>'+ r.reports.reportId +'</div>');
                            $('#productStatus').append('<div id="importOrdersSt"><h3 class="text-center">Import orders</h3>Please wait&hellip;<div class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>');
                            $('#productStatus').addClass('alert alert-success');
                        } else {
                            $('#orderStatus').html('<pre>'+ r.error +'</pre>').addClass('alert alert-danger');
                        }
                        $btn.button('reset');
                    }
                });
            }
        };
    $(document).ready(function() {
        var $btn = '';
        $fn['getProducts']('getOrders');
    });
        
</script>