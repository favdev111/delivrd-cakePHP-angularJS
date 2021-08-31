<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-calculator" style="font-size:18px"></i>
                Recalculate inventory qty for <?php echo $product['Product']['name']; ?> (SKU: <?php echo $product['Product']['sku']; ?>)
            </h4>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger hide" id="modalFormMsg"></div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Cum Qty.</th>
                        <th>Inventory Qty.</th>
                        <th>Difference</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cum_qty as $warehouse_id => $qty) { ?>
                    <tr>
                        <td><?php echo $warehouses[$warehouse_id]; ?></td>
                        <td><?php echo $qty['cum_qty']; ?></td>
                        <td><?php echo $qty['inv_qty']; ?></td>
                        <td>
                            <span class="<?php echo (abs($qty['cum_qty'] - $qty['inv_qty'])>0)?'font-red-soft bold':''; ?>"><?php echo abs($qty['cum_qty'] - $qty['inv_qty']); ?></span>
                        </td>
                        <td>
                            <?php if(abs($qty['cum_qty'] - $qty['inv_qty']) > 0) { ?>
                                <button class="btn btn-xs red-pink" ng-click="inventoryAlign(<?php echo $product['Product']['id']; ?>, <?php echo $warehouse_id; ?>)">Update</button>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
                
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn default" style="box-shadow: none;" ng-click="close($event)"><i class="fa fa-close"></i> Close</button>
        </div>
    </div>
</div>
