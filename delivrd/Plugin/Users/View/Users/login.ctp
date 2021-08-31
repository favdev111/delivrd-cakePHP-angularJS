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
<div id="login-form">
	<h3><?php echo __d('users', 'Login'); ?></h3>
	
	<fieldset> 
		<?php
		
		
			echo $this->Form->create($model, array(
				'action' => 'login','div' => false)); 


			echo $this->Form->input('email', array(
				'label' => false,'div' => false,'default'=>'Email Addressss'));
			echo $this->Form->input('password',  array(
				'label' => false,'div' => false,'default'=>'password'));
   
  		echo $this->Form->end(__d('users', 'Submit'),array('div' => false));
			echo '<footer class="clearfix">';
			echo '<p>' . $this->Form->input('remember_me', array('type' => 'checkbox', 'label' => __d('users', ' Remember Me'))) . '</p>';
			echo '<p><span class="info">?</span>' . $this->Html->link(__d('users', 'I forgot my password'), array('action' => 'reset_password')) . '</p>';
		    echo $this->Session->flash('auth');
			echo '</footer>';
			echo '<div class="create-account">
			<p>
				<a href="/register" id="register-btn">Create an account</a>
			</p>
			</div>';
			echo $this->Form->hidden('User.return_to', array(
				'value' => $return_to));
			
		?>
	</fieldset>
	<?php  // echo $this->element('Users.Users/sidebar'); ?>
</div>

