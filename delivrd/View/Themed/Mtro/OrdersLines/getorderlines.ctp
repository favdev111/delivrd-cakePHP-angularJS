<?php foreach ($currentlines as $ordersLine): ?>
	<tr>

<td><?php echo h($ordersLine['OrdersLine']['order_id']); ?>&nbsp;</td>
	<td><?php echo h($ordersLine['OrdersLine']['line_number']); ?>&nbsp;</td>
	<td>
		<?php echo $this->Html->link($ordersLine['Product']['name'], array('controller' => 'products', 'action' => 'view', $ordersLine['Product']['id'])); ?>
	</td>
	<td><?php echo h($ordersLine['OrdersLine']['quantity']); ?>&nbsp;</td>
	<td><?php if($ordersLine['OrdersLine']['line_number'] != 9999) echo $this->Session->read('currencysym').$ordersLine['OrdersLine']['unit_price']; ?>&nbsp;</td>
	<td><?php if($ordersLine['OrdersLine']['line_number'] != 9999) echo $this->Session->read('currencysym').$ordersLine['OrdersLine']['total_line']; ?>&nbsp;</td>
	<!-- <td><?php // echo '',($ordersLine['OrdersLine']['foc'] == 1 ? 'X' : ''); ?>&nbsp;</td> -->
	<!-- <td><?php // echo '',($ordersLine['OrdersLine']['return'] == 1 ? 'X' : ''); ?>&nbsp;</td> -->
	<td><?php echo h($ordersLine['OrdersLine']['comments']); ?>&nbsp;</td>
	<td class="actions">
		<?php echo $this->Html->link(__('<i class="fa fa-trash-o"></i>  Delete'),array(), array('onclick' => 'delete_order_line('.$ordersLine['OrdersLine']['id'].',event);','class'=>'btn btn-sm default btn-editable','escape'=> false));  ?>

	</td>
	</tr>
<?php endforeach; ?>


		


