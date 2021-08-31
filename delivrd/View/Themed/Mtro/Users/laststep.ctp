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
<style type="text/css">
.checkbox, .radio {
    display: inline-block;
    width: 30%;
}
.checkbox input[type=checkbox], .checkbox-inline input[type=checkbox], .radio input[type=radio], .radio-inline input[type=radio] {
    margin-left: -10px;
}
.checkbox > label, .form-horizontal .checkbox > label {
    padding-left: 3px;
}
.login .content {  
    width: 502px;
}
</style>
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
	
	<h3 class="form-title">One last step</h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
			Enter any username and password. </span>
		</div>
	<?php
	echo $this->Form->create($modelClass, array(
				'div' => false,'class' => 'login-form')); 
                     
			echo '<div class="form-group">';
			echo '<label class="control-label">Country</label>';
			echo $this->Form->input('country_id',array('id' => 'country_id','label' => false, 'class' => 'form-control form-control-solid placeholder-no-fix', 'empty' => 'Select Country','required' => "required"));
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label class="control-label">Phone Number</label>';
			 echo $this->Form->input('mobile', array('type' => 'text', 'placeholder' => '','label' => false, 'class' => 'form-control form-control-solid placeholder-no-fix', 'required' => "required", 'between' => '<div class="input-group"><span class="input-group-addon">+972</span>', 'after' => '</div>'));
			
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label class="control-label visible-ie8 visible-ie9">Password, Again</label>
			<h2>Selling on one or multiple channels?</h2></br>
			Tell us where and we will guide you how to sync your inventory with delivrd</br></br>';
			 echo '<label class="control-label">Physical Store?</label>';
			echo $this->Form->checkbox('status', [
            'checked' => '',
            'label' => false,
            'class' => "switchery_with_action",
            'data-size'=>"small",
            'data-model' => $modelClass,
            'data-id' => '',
            'data-field' => 'status'
            ]);
            echo '</div>';
            echo '<div class="form-group">
            <label class="control-label">E-commerce store?</label>';
			echo $this->Form->checkbox('status', [
            'checked' => true,
            'label' => false,
            'id' => 'e-commerce',
            ]);
			echo '</div>';
			echo '<div class="row" id="user_stores">';	
			echo '<div class="col-md-12">
			<div class="form-group">';

           echo $this->Form->input('Store', array(
			    'label' => false,
			    'type' => 'select',
			    'multiple' => 'checkbox',
			  ));
			echo '</div></div>';
			
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label class="control-label visible-ie8 visible-ie9">Phone Number</label>';
			 echo $this->Form->input('other', array('type' => 'text', 'placeholder' => 'Other','label' => false, 'class' => 'form-control form-control-solid placeholder-no-fix'));
			
			echo '</div>';
			?>
		
		   
		   <?php echo $this->Session->flash();
	
		    ?>
		  
	
													<div class="create-account">
		
													<?php
													
										
										$options = array('label' => 'Start Using Delivrd', 'class' => 'btn green', 'div' => false);
										echo $this->Form->end($options); ?>
										
								
													</form>
	
				</div>
				
</div>


<!-- END LOGIN -->
