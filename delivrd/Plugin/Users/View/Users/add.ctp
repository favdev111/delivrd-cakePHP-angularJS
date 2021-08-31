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
<div class="users form">
	<h2><?php echo __d('users', 'Add User'); ?></h2>
	<fieldset>
		<?php
			echo $this->Form->create($model);
			echo $this->Form->input('username', array(
				'label' => __d('users', 'Username')));
			echo $this->Form->input('email', array(
				'label' => __d('users', 'E-mail'),
				'error' => array('isValid' => __d('users', 'Must be a valid email address'),
				'isUnique' => __d('users', 'An account with that email already exists'))));
			echo $this->Form->input('password', array(
				'label' => __d('users', 'Password'),
				'type' => 'password'));
			echo $this->Form->input('temppassword', array(
				'label' => __d('users', 'Password (conf)'),
				'type' => 'password'));
			echo $this->Form->input('street', array(
				'label' => __d('users', 'Street')));
			echo $this->Form->input('city', array(
				'label' => __d('users', 'City')));
			echo $this->Form->input('zip', array(
				'label' => __d('users', 'Zip')));	
			echo $this->Form->input('state_id', array(
				'label' => __d('users', 'State')));	
			echo $this->Form->input('country_id', array(
				'label' => __d('users', 'Country')));
			$tosLink = $this->Html->link(__d('users', 'Terms of Service'), array('controller' => 'pages', 'action' => 'tos', 'plugin' => null));
			echo $this->Form->input('tos', array(
				'label' => __d('users', 'I have read and agreed to ') . $tosLink));
			echo $this->Form->end(__d('users', 'Submit'));
		?>
	</fieldset>
</div>
<?php echo $this->element('Users.Users/sidebar'); ?>
