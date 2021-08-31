<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo $product['Product']['name']; ?> &raquo; <?php echo date('M, Y', strtotime($month .' - 1 Year')); ?></h4>
    </div>

    <div class="modal-body">
        <?php echo $this->Session->flash(); ?>

        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <tr>
                        <th>Order #</th>
                        <th>Line number</th>
                        <th>Location</th>
                        <th>Supplier</th>
                        <th>Qty.</th>
                        <th>Received Qty.</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                <?php foreach ($lines as $line) { ?>
                    <tr>
                        <td><?php echo $this->Html->link(h($line['Order']['id']), array('controller' => 'replorders', 'action' => 'details', $line['Order']['id'])); ?></td>
                        <td><?php echo h($line['OrdersLine']['line_number']); ?></td>
                        <td><?php echo h($line['Warehouse']['name']); ?></td>
                        <td><?php echo h($line['Supplier']['name']); ?></td>
                        <td><?php echo intval($line['OrdersLine']['quantity']); ?></td>
                        <td><?php echo intval($line['OrdersLine']['receivedqty']); ?></td>
                        <td><?php echo h($this->Session->read('currencyname')); ?> <?php echo h($line['OrdersLine']['unit_price']); ?></td>
                        <td><?php echo h($this->Session->read('currencyname')); ?> <?php echo h($line['OrdersLine']['total_line']); ?></td>
                    </tr>
                <?php } ?>
                </table>
                <?php #echo $this->element('sql_dump');?>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
