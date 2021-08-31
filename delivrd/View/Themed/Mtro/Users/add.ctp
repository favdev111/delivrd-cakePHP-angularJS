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
.login .content {  
    width: 502px;
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
<div class="content register-margin">
	<!-- BEGIN LOGIN FORM -->
	
	<h3 class="form-title">Fullfill 100% of your orders</h3>
	<h4>Your email is verfied. Please fill up the fields</h4>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
			Enter any username and password. </span>
		</div>
	<?php
	echo $this->Form->create($modelClass, array(
				'div' => false,'class' => 'login-form')); 
                    
			echo '<div class="form-group">';
			echo '<label class="control-label">Business Email Address</label>';
			echo $this->Form->input('email',  array(
				'label' => false,'div' => false,'placeholder'=>'Email Address', 'class' => 'form-control form-control-solid placeholder-no-fix','disabled'));
			echo '</div>';
			echo '<div class="form-group"><div class="row"><div class="col-md-6">';
			echo '<label class="control-label">First Name</label>';
			echo $this->Form->input('username', array(
				'label' => false,'div' => false,'placeholder'=>'', 'class' => 'form-control form-control-solid placeholder-no-fix'));
			echo '</div>';
			echo '<div class="col-md-6">';
			echo '<label class="control-label">Last Name</label>';
			echo $this->Form->input('lastname', array(
				'label' => false,'div' => false,'placeholder'=>'', 'class' => 'form-control form-control-solid placeholder-no-fix'));
			echo '</div></div></div>';
			echo '<div class="form-group">';
			echo '<label class="control-label">Password</label>';
			echo $this->Form->input('password',  array(
				'label' => false,'div' => false,'placeholder'=>'', 'class' => 'form-control form-control-solid placeholder-no-fix'));
			echo '</div>';
	
			?>
		
		   <?php echo $this->Session->flash();
	
		    ?>
		    
		  
	
													<div class="create-account">
		
													<?php
													
										
										$options = array('label' => 'Continue', 'class' => 'btn green', 'div' => false);
										echo $this->Form->end($options); ?>
										<script>
											function goBack() {
											window.history.back()
											}
										</script>	
													</form>
			
  
				</div>
				
</div>
 
<!-- END LOGIN -->
