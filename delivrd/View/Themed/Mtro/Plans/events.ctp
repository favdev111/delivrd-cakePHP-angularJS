<div class="grid_2">
	<div class="box">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Products'), array('action' => 'index'));  ?></li>
	</ul>
</div>
</div>
<div class="grid_10">
<div class="box">
<h2><?php echo __('Product'); ?></h2>
	<dl>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($product['Product']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($product['Product']['description']); ?>
			&nbsp;
		</dd>
		
	</dl>
	
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>Time</th>
			<th>Status</th>
			<th>Text</th>
			<th>User</th>
	</tr>
	<?php foreach ($product_events as $event): ?>
	<tr>
		<td><?php echo h($event['Event']['created']); ?>&nbsp;</td>
		<td><?php echo h($event['Status']['name']); ?>&nbsp;</td>
		<td><?php echo h($event['Event']['status_id']); ?>&nbsp;</td>
		<td><?php echo h($event['User']['username']); ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</table>
	
</div>