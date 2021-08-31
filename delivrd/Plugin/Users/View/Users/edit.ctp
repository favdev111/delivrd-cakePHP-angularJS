<div class="grid_2">
	<div class="box">
	<h2><?php echo __('Actions'); ?></h2>
	<ul>
		<?php echo $this->element('Users.Users/sidebar'); ?>
	</ul>
	</div>
</div>
<div class="grid_4">
	<?php echo $this->Form->create($model); ?>
		<fieldset>
			<legend><?php echo __d('users', 'Settings'); ?></legend>
			<?php

				echo $this->Form->input('User.printprice',array('label' => 'Print  value? '));
				echo $this->Form->input('User.printweight',array('label' => 'Print  weight? '));
				echo $this->Form->input('User.currency_id',array('label' => 'Currency'));
				echo $this->Form->input('User.msystem_id',array('label' => 'Meas. system'));
				echo $this->Form->input('User.binscount',array('label' => 'Number of bins'));
				echo $this->Form->input('User.userpage',array('label' => 'Homepage URL 2D'));
				echo $this->Form->input('User.showvariants',array('label' => 'Show Variants'));
			?>
		</fieldset>
	<?php echo $this->Form->end(__d('users', 'Submit')); ?>
</div>
