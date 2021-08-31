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

				echo $this->Form->input('User.street',array('label' => 'Street'));
				echo $this->Form->input('User.city',array('label' => 'City'));
				echo $this->Form->input('User.state_id',array('label' => 'State'));
				echo $this->Form->input('User.zip',array('label' => 'Zip'));
				echo $this->Form->input('User.country_id',array('label' => 'Country'));
				echo $this->Form->input('User.company',array('label' => 'Company'));
			?>
		</fieldset>
	<?php echo $this->Form->end(__d('users', 'Submit')); ?>
</div>
