<?php $this->AjaxValidation->active(); ?>
<style>
.message { 
	border-radius: 2px;
	border-width: 0;
	background-color: #FFCCCC;
	border-color: #68caf1;
	color: black;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.18);
	padding: 15px;
	margin-bottom: 20px;
	border: 1px solid transparent;
	}
</style>
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGO -->
<div class="logo">
	
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
	<!-- BEGIN LOGIN FORM -->
	<a href="/">
	<img src="/theme/Mtro/assets/admin/layout/img/logo_b.png" alt=""/>
	</a>
	<h3 class="form-title">Register</h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
			Enter any username and password. </span>
		</div>
	<?php 
	echo $this->Form->create($model, array(
		'url' => array(
			'action' => 'reset_password',
			$token)));
			
			echo '<div class="form-group">';
			echo '<label class="control-label visible-ie8 visible-ie9">Password</label>';
			echo $this->Form->input('new_password', array(
		'label' => __d('users', 'New Password'),
		'type' => 'password'));
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label class="control-label visible-ie8 visible-ie9">Password, Again</label>';
			echo $this->Form->input('confirm_password', array(
		'label' => __d('users', 'Confirm'),
		'type' => 'password'));
			echo '</div>';
			?>
		<?php n->flash('auth');
	
		    echo $this->Session->flash();
	
		    ?>
													<div class="create-account">
		
													<?php
									//	$tosLink = $this->Html->link(__d('users', 'Terms of Service'), 'http://www.delivrd.com/disclaimer/');
									//	echo $this->Form->input('tos', array('label' => __d('users', 'I have read and agreed to ') . $tosLink));
									//echo $this->Form->input('tos', array('label'=>__d('users','I have read and agreed to <a href="http://www.delivrd.com/disclaimer/" target="_blank"><u>Terms Of Service</u></a>.', true), 'hiddenField' => false, 'value' => '1','class' => 'form-control input-medium')); 
										$options = array('label' => 'Submit', 'class' => 'btn green', 'div' => false);
										echo $this->Form->end($options); ?>
										<button type="button" name="back" onclick="goBack()" class="btn default">Cancel</button>
										<script>
											function goBack() {
											window.history.back()
											}
										</script>	
													</form>
			
 
			
?>
				</div>
</div>
<div class="copyright">
	 2015 © Delivrd
</div>
<!-- END LOGIN -->
