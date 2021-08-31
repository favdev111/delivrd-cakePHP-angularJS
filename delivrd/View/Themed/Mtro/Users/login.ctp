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
<div id="ae136b0ce1ae6dc382f2df65c8f891b1" />
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
	<img src=<?php echo Configure::read('LoginLogoURL') ?> alt=""/>
	</a>
	<h3 class="form-title">Sign In</h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
			Enter any username and password. </span>
		</div>
	<?php 
	echo $this->Form->create($model, array(
				'url' => 'login','div' => false,'class' => 'login-form')); 
			echo '<div class="form-group">';
			echo '<label class="control-label visible-ie8 visible-ie9">Username</label>';
			echo $this->Form->input('email', array(
				'label' => false,'div' => false,'placeholder'=>'Email Address', 'class' => 'form-control form-control-solid placeholder-no-fix'));
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label class="control-label visible-ie8 visible-ie9">Username</label>';
			echo $this->Form->input('password',  array(
				'label' => false,'div' => false,'placeholder'=>'password', 'class' => 'form-control form-control-solid placeholder-no-fix', 'autocomplete' => 'off'));
			echo '</div>';
			?>
			<div class="form-actions">
			<button type="submit" class="btn btn-primary btn-block uppercase">Log In</button>
		</div>
         <?php //if(Configure::read('OperatorName') == 'Delivrd') { ?>
		<div class="form-actions">
			<div class="pull-left">
				<label class="rememberme check">
				<?php //echo $this->Form->input('remember_me', array('type' => 'checkbox','div' => false, 'class' => false ,'label' => __d('users', ' Remember me'))); ?>
			<!--	<input type="hidden" name="data[User][remember]" id="UserRemember_" value="0">
				<input type="checkbox" name="data[User][remember]" id="UserRemember">Remember me </label> -->
			</div>
                    
			<div class="pull-right forget-password-block">
			 <?php echo $this->html->link('Forgot password?', array('plugin' => 'users', 'controller' => 'users', 'action' => 'reset_password'), array('class' => 'forget-password', 'id' => 'forget-password', 'escape' => false)); ?>
			</div>
                     
		</div>
        <?php //} ?>
		<div class="login-options">
			
		</div>
			<?php
			// This is ugly, but works - when ref is /, do not display access error
			// This should be fixed using a better method. for now - it works
			
			echo "<div>";
			if($this->request->referer() !='/')
			{
			echo $this->Session->flash('auth');
			}
		    echo $this->Session->flash(); 
			echo "</div>";
			
			?>
            <?php //if(Configure::read('OperatorName') == 'Delivrd') { ?>
		<div class="create-account">
			<p>
			<?php echo $this->html->link('Create an account', array('plugin' => 'users', 'controller' => 'users', 'action' => 'signup'), array('id' => 'register-btn')); ?>
			</p>
		</div>
          <?php //} ?>
		<?php
			
			echo $this->Form->end(__d('users', ''),array('div' => false));
			?>
			
  <?php /*
			echo '<div class="form-actions">';
			echo '<button type="submit" class="btn btn-primary btn-block uppercase">Login</button>';
			echo '</div>';
			echo '<div class="form-actions">';
			echo '<div class="pull-left">';
			echo '<label class="rememberme check">' . $this->Form->input('remember_me', array('type' => 'checkbox', 'label' => __d('users', ' Remember Me')));
			echo '</lable></div>';
			echo '<div class="pull-right forget-password-block">';
			echo $this->Html->link(__d('users', 'I forgot my password'), array('action' => 'reset_password'),array('class' => 'forget-password'));
			echo '</div>';
			echo $this->Form->end(__d('users', ''),array('div' => false));
			echo '</div>';
			echo '</footer>';
	
			
			
		    echo $this->Session->flash('auth');
		    echo $this->Session->flash();
			
			echo '<div class="create-account">
			<p>
				<a href="/register" id="register-btn">Create an account</a>
			</p>
			</div>';
			* 	*/
			echo $this->Form->hidden('User.return_to', array(
				'value' => $return_to));
			
?>
		
</div>
<div class="copyright">
	 <?php echo date('Y'); ?> © <?php echo Configure::read('OperatorName') ?>
</div>
<!-- END LOGIN -->
<!-- ae136b0ce1ae6dc382f2df65c8f891b1 -->
