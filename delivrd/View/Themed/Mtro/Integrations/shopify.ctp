<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Import Shopify (<?= $shopify['Integration']['url']; ?>)</h4>
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
    //var tmpPrResponse = {"total_found":77,"skiped":-49,"added":0,"updated":120,"errors":{"42159072324":{"title":"1989 Topps New Kids on the Block Complete Trading Card Set (88 Cards): Default Title","error":"Category can't be empty"},"42160015236":{"title":"kate spade new york Women's Ellie Ballet Flat: Sand","error":"Category can't be empty"},"42160015300":{"title":"kate spade new york Women's Ellie Ballet Flat: Black","error":"Category can't be empty"},"33726534404":{"title":"Screen 24inch: Default Title","error":"Category can't be empty"},"41927926276":{"title":"TracFone LG Rebel 4G LTE Prepaid Smartphone with Free $30 Airtime installed in phone: black","error":"Category can't be empty"},"41928005124":{"title":"TracFone LG Rebel 4G LTE Prepaid Smartphone with Free $30 Airtime installed in phone: white","error":"Category can't be empty"}},"errors_count":6};
    //var tmpOrResponse = {"found":22,"added":0,"errors":{"2987":{"external_orderid":["Reference order 2987 already exists"]},"2983":{"external_orderid":["Reference order 2983 already exists"]},"2981":{"external_orderid":["Reference order 2981 already exists"]},"2980":{"external_orderid":["Reference order 2980 already exists"]},"2979":{"external_orderid":["Reference order 2979 already exists"]},"2977":{"ship_to_customerid":["Please enter customer name"]},"2976":{"ship_to_customerid":["Please enter customer name"]},"2975":{"external_orderid":["Reference order 2975 already exists"]},"2974":{"ship_to_customerid":["Please enter customer name"]},"2971":{"ship_to_customerid":["Please enter customer name"]},"2969":{"ship_to_customerid":["Please enter customer name"]},"2968":{"ship_to_customerid":["Please enter customer name"]},"2967":{"external_orderid":["Reference order 2967 already exists"]},"2966":{"ship_to_customerid":["Please enter customer name"]},"2961":{"ship_to_customerid":["Please enter customer name"]},"2959":{"ship_to_customerid":["Please enter customer name"]},"2958":{"external_orderid":["Reference order 2958 already exists"]},"2957":{"external_orderid":["Reference order 2957 already exists"]},"2956":{"external_orderid":["Reference order 2956 already exists"]},"2955":{"external_orderid":["Reference order 2955 already exists"]},"2954":{"external_orderid":["Reference order 2954 already exists"]},"2952":{"external_orderid":["Reference order 2952 already exists"]}}};
    function showAllProd() {
        $('.skiped_reason div').each(function(){
            $(this).removeClass('hide');
            $('#expProdLink').remove();
        });
    }
    function showAllOrd() {
        $('.skiped_reason2 div').each(function(){
            $(this).removeClass('hide');
            $('#expOrdLink').remove();
        });
    }

    var $fn = {
            getProducts: function(callback) {
                $.ajax({
                    type: 'GET',
                    url: '<?php echo $this->Html->url(array('controller'=>'products', 'action'=>'importshopify', $shopify['Integration']['id'])); ?>',
                    data: '',
                    dataType:'json',
                    beforeSend: function() {
                        $('#productStatus').html('Start to import products.<br>Please wait&hellip;<div class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>');
                        $btn = $('#closeBtn').button('loading')
                    },
                    success:function (r, status) {
                        $('#productStatus').html('<div><strong>Total found: </strong>'+ r.total_found +'</div>');
                        $('#productStatus').append('<div><strong>Added: </strong>'+ r.added +'</div>');
                        $('#productStatus').append('<div><strong>Skipped: </strong>'+ r.skiped +'<br></div>');
                        if(r.skiped > 0) {
                            $('#productStatus').append('<div class="skiped_reason"></div>');
                            var t = 0;
                            var hclass = '';
                            $.each(r.skiped_det, function(key, val){
                                t++;
                                if(t == 5) {
                                    hclass = 'hide';
                                    $('.skiped_reason').append('<div id="expProdLink" class="text-right"><a href="#" style="font-weight:800;" onClick="showAllProd(); return false;"><i class="fa fa-angle-down"></i> expand all</a></div>');
                                }
                                $('.skiped_reason').append('<div class="'+ hclass +'">&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-angle-double-right" aria-hidden="true"></i> <strong>'+ val.name +' ('+ val.sku +')</strong><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><i class="fa fa-exclamation" aria-hidden="true"></i> Product with same SKU already added.</span></div>');
                            });
                        }
                        
                        if(r.errors_count > 0){
                            $('#productStatus').append('<div><strong>Errors: </strong>'+ r.errors_count +'<div class="reason text-error" style="padding-left:20px;"></div></div>');
                            $.each(r.errors, function(key, value){
                                $('#productStatus').find('div.reason').append('<div><strong>'+ value.title +'</strong><br><i>'+ value.error +'</i></div>');
                            });
                            $('#productStatus').addClass('alert alert-warning');
                            if(r.added == 0) {
                                $('#productStatus').append('<div class="text-center lead">No new products added</div>');
                            }
                        } else {
                            $('#productStatus').addClass('alert alert-success bg-green-jungle');
                            if(r.added == 0) {
                                $('#productStatus').append('<div class="text-center lead">No new products added</div>');
                            }
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
                    url: '<?php echo $this->Html->url(array('controller'=>'orders', 'action'=>'importshopify', $shopify['Integration']['id'])); ?>',
                    data: '',
                    dataType:'json',
                    beforeSend: function() {
                        $('#orderStatus').html('Strat to import orders.<br>Please wait&hellip;<div class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>');
                    },
                    success:function (r, status) {
                        //console.log(r);
                        $('#orderStatus').html('<div><strong>Total found: </strong>'+ r.total_found +'</div>');
                        $('#orderStatus').append('<div><strong>Added: </strong>'+ r.added +'</div>');
                        $('#orderStatus').append('<div><strong>Skipped: </strong>'+ r.skiped +'</div>');
                        if(r.skiped > 0) {
                            $('#orderStatus').append('<div class="skiped_reason2"></div>');
                            var t = 0;
                            var hclass = '';
                            $.each(r.skiped_det, function(key, val){
                                t++;
                                if(t == 5) {
                                    hclass = 'hide';
                                    $('.skiped_reason2').append('<div id="expOrdLink" class="text-right"><a href="#" style="font-weight:800;" onClick="showAllOrd(); return false;"><i class="fa fa-angle-down"></i> expand all</a></div>');
                                }
                                $('.skiped_reason2').append('<div class="'+ hclass +'">&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-angle-double-right" aria-hidden="true"></i> <strong>Order #'+ val.external_id +'</strong> <span> - Order with same ID already added.</span></div>');
                            });
                        }

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
                            if(r.added == 0) {
                                $('#orderStatus').append('<div class="text-center lead">No new orders added</div>');
                            }
                        } else {
                            $('#orderStatus').addClass('alert alert-success');
                            if(r.added == 0) {
                                $('#orderStatus').append('<div class="text-center lead">No new orders added</div>');
                            }
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