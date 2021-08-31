<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title" id="modal-title"><i class="icon-grid"></i> Serial Checkout</h4>
    </div>

    <?php echo $this->Form->create('Inventory', array('url' => array('controller' => 'inventories', 'action' => 'serial_checkout'), 'class' => 'form-horizontal', 'id' => 'serial_checkout_form')); ?>
    <div class="modal-body">
        <div class="form-control" style="border:none;">
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-barcode"></i></div>
                <?php echo $this->Form->input('code_scan', array('label' => false, 'class'=>'code-scan form-control', 'value' => '', 'placeholder' => 'Enter or scan Serial', 'id'=>'code_scan', 'autofocus' => 'autofocus')); ?>
            </div>
        </div>
        <div class="table-container">
            <table class="table table-hover dataTable no-footer hide" id="tableRes">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Location</th>
                        <th>Serial</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><a href="#" id="clearRes" >Clear</a></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php /*<button id="testBtn" type="button">Test Scanner</button>*/ ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary saveBtn"><i class="fa fa-download"></i> Checkout</button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>

<audio id="scannerSuccess">
    <source src="<?php echo $this->webroot; ?>media/barcode-scanner-beep.mp3" type="audio/mpeg">
</audio>
<audio id="scannerError">
    <source src="<?php echo $this->webroot; ?>media/glitch-error.mp3" type="audio/mpeg">
</audio>

<script>
$(function() {
    $('#code_scan').on('keyup', function (e) {
        if (e.keyCode == 13) {
            $('#serial_checkout_form').submit();
        }
    });

    $('#testBtn').click(function(){
        $('#code_scan').scannerDetection('77773458');
        return false;
    });

    $('#clearRes').click(function(){
        $('#tableRes').addClass('hide').find('tbody').html('');
        return false;
    })

    $('#serial_checkout_form').submit(function(){
        var $form = $(this);
        
        $.ajax({
            method: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            datatype:'json',
        }).success(function (data) {
            //console.log(data);
            var response = jQuery.parseJSON(data);
            //console.log(response);
            if(response.action == 'info') {
                $('#scannerError')[0].play();
                toastr[response.action](response.message);
            } else if(response.action == 'success') {
                $('#tableRes').removeClass('hide');
                $('#scannerSuccess')[0].play();
                $('#tableRes').find('tbody').append('<tr><td>'+response.product+'</td><td>'+response.location+'</td><td>'+response.serial_no+'</td><td>'+response.quantity+'</td></tr>');
            } else {
                $('#scannerError')[0].play();
                toastr[response.action](response.message);
            }
            $('#code_scan').val('').parents('div.form-control').removeClass('has-warning');;
        });
        return false;
    })

    $('#code_scan').scannerDetection({
        timeBeforeScanTest: 200, // wait for the next character for upto 200ms
        startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
        endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
        avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
        minLength: 6,
        onComplete: function(barcode, qty){
            $('#code_scan').val(barcode).parents('div.form-control').addClass('has-warning');
            $('#serial_checkout_form').submit();

            /*$('#line_'+order_list[barcode]).addClass('bg-green-turquoise');
            setTimeout(function() {
                $('#line_'+order_list[barcode]).removeClass('bg-green-turquoise');
            }, 300);

            if(order_list[barcode] == undefined) {
                $('#scannerError')[0].play();
                toastr['error']('The orders line not found. Please, try again.');
            } else {
                var quantity = $('#line_'+order_list[barcode]).find('#OrdersLineQuantity').val();
                var sentqty = $('#line_'+order_list[barcode]).find('#OrdersLineSentqty').val();
                sentqty = Number(sentqty);
                quantity = Number(quantity);

                if(sentqty < quantity) {
                    $('#scannerSuccess')[0].play();
                    $('#line_'+order_list[barcode]).find('#OrdersLineSentqty').val(sentqty + 1);
                    diff = 1;
                    $('#line_'+order_list[barcode]).find('form').submit();
                } else {
                    $('#scannerError')[0].play();
                    $('#autocomplete').val('').focus();
                    toastr['error']('The orders line could not be saved. Please, try again.');
                }
            }
            checkAllStatus();*/
        },
        /*onReceive: function(barcode, qty){
            $('#autocomplete').val(barcode);
            $('#scannerError')[0].play();
        },*/
        onError: function(barcode, qty){
            
        }
    });
    
});
</script>