<?php
/**
 * Copyright 2010 - 2013, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2013, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<div class="users index">
	<h2><?php echo __d('users', 'Users'); ?></h2>

	<p><?php
	echo $this->Paginator->counter(array(
		'format' => __d('users', 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?></p>
	<table cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->Paginator->sort('username'); ?></th>
		<th><?php echo $this->Paginator->sort('created'); ?></th>
		<th><?php echo $this->Paginator->sort('status'); ?></th>
		<th class="actions"><?php echo __d('users', 'Actions'); ?></th>
	</tr>
	<?php
	$i = 0;
//	Debugger::dump($networks);
	foreach ($networks as $network):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		?>
		<tr<?php echo $class; ?>>
			<td><?php echo $this->Html->link($network['User']['username'], array('action' => 'view', $network['User']['id'])); ?></td>
			<td><?php echo $network['Status']['name']; ?></td>
			
			<td class="actions">
				<?php echo $this->Html->link(__d('users', 'View'), array('action' => 'view', $network['User']['id'])); ?>
				<?php 
				if($network['Status']['id'] == 10)
				{
				 echo $this->Html->link(__d('users', 'Approve'), array('action' => 'approvedcop', $network['Network']['id'])); 
				 }
				 ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	<?php echo $this->element('Users.pagination'); ?>
</div>
<?php echo $this->element('Users.Users/sidebar'); ?>
