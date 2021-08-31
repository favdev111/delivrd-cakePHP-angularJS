<?php #$this->AjaxValidation->active(); ?>
<style>
ul,
li {
  padding: 0;
  margin: 0;
  list-style: none;
}

ul {
  margin: 2em 0;
}

li {
  margin: 1em;
  margin-left: 3em;
}

li:before {
    content: "•";
    font-family: 'FontAwesome';
    font-size: 24px;
    float: left;
    margin: -9px;
    margin-left: -1.1em;
    color: #92c1ea;
}
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
.tos {margin-bottom: 10px}

.tos label {
    font-weight: normal;
}

.g-recaptcha {
        display: inline-block;
    }

.blue-delivrd.btn {
  color: #FFFFFF;
  background-color: #204781;
}
.blue-delivrd.btn:hover, .blue-delivrd.btn:focus, .blue-delivrd.btn:active, .blue-delivrd.btn.active {
  color: #FFFFFF;
  background-color: #203E81;
}
.open .blue-delivrd.btn.dropdown-toggle {
  color: #FFFFFF;
  background-color: #203E81;
}
.blue-delivrd.btn:active, .blue-delivrd.btn.active {
  background-image: none;
  background-color: #202E81;
}
.blue-delivrd.btn:active:hover, .blue-delivrd.btn.active:hover {
  background-color: #202E81;
}
.open .blue-delivrd.btn.dropdown-toggle {
  background-image: none;
}
.blue-delivrd.btn.disabled, .blue-delivrd.btn.disabled:hover, .blue-delivrd.btn.disabled:focus, .blue-delivrd.btn.disabled:active, .blue-delivrd.btn.disabled.active, .blue-delivrd.btn[disabled], .blue-delivrd.btn[disabled]:hover, .blue-delivrd.btn[disabled]:focus, .blue-delivrd.btn[disabled]:active, .blue-delivrd.btn[disabled].active, fieldset[disabled] .blue-delivrd.btn, fieldset[disabled] .blue-delivrd.btn:hover, fieldset[disabled] .blue-delivrd.btn:focus, fieldset[disabled] .blue-delivrd.btn:active, fieldset[disabled] .blue-delivrd.btn.active {
  background-color: #204781;
}
#signup {
    margin-bottom: 8px;
}
div.copyright {
    color:#FFFFFF !important;
    font-size:16px !important;
}
</style>
<?php /*<script src="https://www.google.com/recaptcha/api.js" async defer></script> */?>
<!-- BEGIN BODY -->
<body class="login" style="background: #3462a2 !important">
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->

<!-- BEGIN LOGIN -->
<div class="content register-margin">
    <!-- BEGIN LOGIN FORM -->
    
    <?php
        $ref_path =  parse_url($this->request->referer(),PHP_URL_PATH);
        if(($ref_path = '/users/login' && $created == 0) || ($ref_path == '/login' && $created == 0) || ($ref_path == '/register' && $created != 1) || ($ref_path == '/users/add' && $created != 1)) { ?>
        <h4 class="form-title" style="color: #626c6b;text-align: center;font-size: 22px;font-weight: 400">Sign up to Delivrd inventory management</h4>
        <?php if($invite) { ?>
        <div class="alert alert-info">
            <i class="fa fa-sitemap"></i> You join Delivrd from invitation by <?php echo (($invite['Network']['CreatedByUser']['firstname'])? $invite['Network']['CreatedByUser']['firstname'] .' '. $invite['Network']['CreatedByUser']['lastname'] .' ('. $invite['Network']['CreatedByUser']['email'] .')': $invite['Network']['CreatedByUser']['email']) ; ?>
        </div>
        <?php } ?>
        <div class="alert alert-danger display-hide">
            <button class="close" data-close="alert"></button>
            <span> Enter any username and password. </span>
        </div>
        <?php echo $this->Form->create($modelClass, array('div' => false,'class' => 'login-form', 'id' => 'fullfilling_order')); ?>
            <?php echo $this->Form->hidden('userpage', array('value'=> $userpage)); ?>
            <?php echo $this->Form->hidden('btype_id', array('value'=> 3)); ?>
            <?php if($invite) { ?>
                <?php echo $this->Form->hidden('invite_id', array('value'=> $invite['NetworksInvite']['id'])); ?>
                <?php echo $this->Form->hidden('showtours', array('value'=> 0)); ?>
            <?php } else { ?>
                <?php echo $this->Form->hidden('showtours', array('value'=> 0)); ?>
            <?php } ?>

            <?php if(isset($this->request->query['btn'])) { ?>
                <?php echo $this->Form->input('btn', array('type' => 'hidden', 'value'=> $this->request->query['btn'])); ?>
            <?php } else { ?>
                <?php echo $this->Form->input('btn', array('type' => 'hidden', 'value'=> 0)); ?>
            <?php } ?>

            <?php if((isset($this->request->query['t']) && $this->request->query['t'] == 1) || $invite) { 
                $t = 1;
            } else {
                $t = 0;
            }
                echo $this->Form->input('is_limited', array('type' => 'hidden', 'value'=> $limited));
                echo $this->Form->input('paid', array('type' => 'hidden', 'value'=> $t));
                echo $this->Form->input('locationsactive', array('type' => 'hidden', 'value'=> $t));
            ?>

            <div class="row">
                <?php /*<div class="form-group col-sm-6">
                    <label class="control-label">Full Name <span class="text-danger">*</span></label>
                    <?php echo $this->Form->input('firstname',  array('label' => false,'div' => false,'placeholder'=>'First Name', 'class' => 'form-control form-control-solid placeholder-no-fix', 'required')); ?>
                </div>

                <div class="form-group col-sm-6">
                    <label class="control-label">&nbsp;</label>
                    <?php echo $this->Form->input('lastname',  array('label' => false,'div' => false,'placeholder'=>'Last Name', 'class' => 'form-control form-control-solid placeholder-no-fix', 'required')); ?>
                </div>*/ ?>

                <div class="form-group col-sm-12">
                    <label class="control-label">Name <span class="text-danger">*</span></label>
                    <?php echo $this->Form->input('company',  array('label' => false,'div' => false,'placeholder'=>'Company', 'class' => 'form-control form-control-solid placeholder-no-fix', 'required')); ?>
                </div>
            </div>

            <?php /*<div class="form-group">
                <label class="control-label">Business Type <span class="text-danger">*</span></label>
                <?php echo $this->Form->input('btype_id',  array('label' => false, 'empty' => 'Select...', 'div' => false, 'class' => 'form-control form-control-solid placeholder-no-fix', 'required')); ?>
            </div>*/ ?>

            <div class="form-group">
                <label class="control-label">Email Address <span class="text-danger">*</span></label>
                <?php echo $this->Form->input('email',  array('label' => false,'div' => false,'placeholder'=>'Email Address', 'class' => 'form-control form-control-solid placeholder-no-fix')); ?>
            </div>

            <div class="form-group" style="margin-bottom:0px">
                <label class="control-label">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <?php echo $this->Form->input('password',  array('label' => false,'div' => false,'placeholder'=>'Password', 'class' => 'form-control', 'type' => 'password', 'id' => 'password')); ?>
                    <span class="input-group-addon"><input id="show-password" type="checkbox"> Show</span>
                </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <ul style="margin-top: 0px;">
                 <!--  <li>One uppercase character</li> -->
                  <li>One character</li>
                  <li>One number</li>
                </ul>
              </div>
                <div class="col-sm-6">
                  <ul style="margin-top: 0px;">
                    <li>6 characters minimum</li>
                  </ul>
                </div>
            </div>

            <?php if(Configure::read('OperatorName') == 'Delivrd') { ?>
                <div class="tos required">
                    <?php echo $this->Form->input('tos', array('label'=>__d('users','By registering I agreed to <a href="http://www.delivrd.com/terms/" target="_blank"><u>Terms Of Service</u></a>.', true),  'type'=>'checkbox', 'div'=>false, 'checked' => false)); ?>
                </div>

                <div class="tos">
                    <input type="checkbox" name="data[User][tos2]" value="2">
                    <label style="display: inline">I agreed to receive Delivrd's educational &amp; marketing materials to my email</label>
                    <?php #echo $this->Form->input('tos2', array('label'=>__d('users',, true),  'type'=>'checkbox', 'div'=>false, 'checked' => false)); ?>
                </div>
            <?php } ?>
            <?php /*
            <div style="margin:10px" class="text-center">
                <div class="g-recaptcha" data-sitekey="<?php echo Configure::read('Recaptcha.key'); ?>"></div>
            </div> */ ?>

        <?php } ?>

            <div id="response"></div>
            <?php echo $this->Session->flash(); ?>
            <?php if($created == 1 && Configure::read('OperatorName') == 'Delivrd') { ?>
            <div><H3>Fill 5 questions survey</H3></div>
            While you are waiting, please take a minute to fill our 5 questions survey.
            <div align="center">
                <a href="http://www.surveygizmo.com/s3/2544118/Delivrd-Beta" class="btn btn-lg blue"> Take Survey
                <i class="fa fa-edit"></i>
                </a>
            </div>
       
          <?php } ?>
          
        <?php if(($ref_path = '/users/login' && $created == 0) || ($ref_path == '/login' && $created == 0) || ($ref_path == '/register' && $created != 1) || ($ref_path == '/users/add' && $created != 1)) { ?>
        <div class="create-account">
            <button class="btn blue-delivrd" id="signup">Start managing your inventory</button><br>
            <?php echo $this->Form->end(); #array('label' => 'Start managing your inventory', 'class' => 'btn green', 'id' => 'signup', 'div' => false) ?>
        </div>
        <?php } ?>
</div>
 <?php  if(Configure::read('OperatorName') == 'Delivrd') { ?>
<div class="copyright">
    <?php echo date('Y'); ?> © Delivrd
</div>
<?php } ?>
<!-- END LOGIN -->
