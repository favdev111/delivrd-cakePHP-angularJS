<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-shopping-cart" style="font-size:18px"></i>
                Negative inventory quantity
            </h4>
        </div>
        <div class="modal-body">
            <p class="text-warning text-center"><b>If you choose to "Issue Quantity", inventory quantity will be negative.</b></p>
            <table class="table table-advance">
                <tr>
                    <th>Product</th>
                    <th>Location</th>
                    <th>Quantity</th>
                    <th>Issue</th>
                    <th>Result</th>
                </tr>
            <?php foreach ($negative_inv as $inv) { ?>
                <tr>
                    <td><?php echo $inv['Product']['name']; ?></td>
                    <td><?php echo $inv['Warehouse']['name']; ?></td>
                    <td><?php echo $inv['Inventory']['quantity']; ?></td>
                    <td><?php echo $inv['Inventory']['offset']; ?></td>
                    <td class="danger text-center"><strong><?php echo $inv['Inventory']['quantity'] - $inv['Inventory']['offset']; ?></strong></td>
                </tr>
            <?php } ?>
            </table>
            <div class="text-right">
                <label><input type="checkbox" id="confirmNegative" style="vertical-align: -4px"> Don't show this again</label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-warning" ng-click="issueAllConfirm(<?php echo $order['Order']['id']; ?>, <?php echo $this->request->query('is_complete'); ?>)"><i class="fa fa-check"></i> Confirm Issue Quantity</button>
            <button class="btn default" style="box-shadow: none;" ng-click="close($event)"><i class="fa fa-close"></i> Close</button>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $('#confirmNegative').click(function() {
        var is_negative_alowed = 0;
        if($(this).attr('checked') == 'checked') {
            is_negative_alowed = 1
        }

        $.ajax({
            type: 'POST',
            url: siteUrl + 'salesorders/confirmNegative',
            data: 'negative_alowed='+is_negative_alowed,
            dataType:'json',
            beforeSend: function() {
                
            },
            success:function (r, status) {
                
            }
        });
    });
});
</script>