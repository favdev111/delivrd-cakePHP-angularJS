<body class="login">
<div class="content">
	<div class="text-center margin-bottom-30"><a href="<?php echo $this->Html->url('/');?>"><img src=<?php echo Configure::read('LoginLogoURL') ?> ></a></div>

	<div>
		<?php if(Configure::read('OperatorName') == 'Delivrd' && $this->request->host() == 'delivrdapp.com') { ?>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" id="PayPalForm">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="WEYVWFC35BA84">
			<input type="hidden" name="custom" value="<?php echo $authUser['id']; ?>">
			<p class="text-center">You will be redirected to PayPal in some seconds</p>
			<p class="text-center text-muted">Please wait ...</p>
			<p class="text-center text-muted margin-bottom-30 "><i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i></p>
		</form>
		<?php } else { ?>
		<h4 class="text-center">Sandbox PayPal Payment</h4>
		<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top" id="PayPalForm">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="K7XAYPUDS93JJ">
			<input type="hidden" name="custom" value="<?php echo $authUser['id']; ?>">
			<p class="text-center">You will be redirected to PayPal in some seconds</p>
			<p class="text-center text-muted">Please wait ...</p>
			<p class="text-center text-muted margin-bottom-30 "><i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i></p>
		</form>
		<?php } ?>
	</div>
</div>
<div class="copyright">
	<?php echo date('Y'); ?> © <?php echo Configure::read('OperatorName') ?>
</div>
<script>
	setTimeout(function() { $('#PayPalForm').submit() }, 1000);
</script>