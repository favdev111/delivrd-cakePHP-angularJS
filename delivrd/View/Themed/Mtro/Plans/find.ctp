<div class="products index">
<?php echo $this->Form->create('Product', array(
    'url' => array_merge(
            array(
                'action' => 'find'
            ),
            $this->params['pass']
        )
    )
);
echo $this->Form->input('sku', array(
        'div' => false
    )
);

echo $this->Form->submit(__('Search'), array(
        'div' => false
    )
);
echo $this->Form->end();

?>
	<h2><?php echo __('Products'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('part_number'); ?></th>
			<th><?php echo $this->Paginator->sort('description'); ?></th>
			<th><?php echo $this->Paginator->sort('group_id'); ?></th>
			<th><?php echo $this->Paginator->sort('uom'); ?></th>
			<th><?php echo $this->Paginator->sort('weight'); ?></th>
			<th><?php echo $this->Paginator->sort('width'); ?></th>
			<th><?php echo $this->Paginator->sort('height'); ?></th>
			<th><?php echo $this->Paginator->sort('barcode'); ?></th>
			<th><?php echo $this->Paginator->sort('barcode_system'); ?></th>
			<th><?php echo $this->Paginator->sort('sku'); ?></th>
			<th><?php echo $this->Paginator->sort('packaging_material'); ?></th>
			<th><?php echo $this->Paginator->sort('packaging_instructions'); ?></th>
			<th><?php echo $this->Paginator->sort('value'); ?></th>
			<th><?php echo $this->Paginator->sort('status'); ?></th>
			<th><?php echo $this->Paginator->sort('ebay_itemlist_url'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($products as $product): ?>
	<tr>
		<td><?php echo h($product['Product']['part_number']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['description']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($product['Group']['name'], array('controller' => 'groups', 'action' => 'view', $product['Group']['id'])); ?>
		</td>
		<td><?php echo h($product['Product']['uom']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['weight']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['width']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['height']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['barcode']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['barcode_system']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['sku']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['packaging_material']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['packaging_instructions']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['value']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['status']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['ebay_itemlist_url']); ?>&nbsp;</td>
		<td><?php echo h($product['Product']['created']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $product['Product']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $product['Product']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $product['Product']['id']), array(), __('Are you sure you want to delete # %s?', $product['Product']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Product'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Groups'), array('controller' => 'groups', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Group'), array('controller' => 'groups', 'action' => 'add')); ?> </li>
	</ul>
</div>