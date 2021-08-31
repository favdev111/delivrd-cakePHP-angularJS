<section class="free_trial_panel"> 
		<div class="container">
			<h1>Start Your Free <span>30-Day Trial</span></h1>
			<h4>Manage your inventory and orders in more efficient way</h4>
			
			
			<div class="no_risk_panel">
				<h5>100% no-risk free trial</h5>
				<div class="col-sm-8 col-xs-12 left">
					<ul>
						<li>Setup your account. Get access to all features.</li>
						<li>NOTHING will be billed to your card for 30 days. Guaranteed.</li>
						<li>If you want to continue after 30 days, only then we'll bill you for your yearly plan.</li>
						<li>During your free trial you can cancel at any time. No questions asked. Nothing billed at all.</li>
					</ul>
				</div>
				
				<div class="col-sm-4 col-xs-12 right">
					<!--<img src="images/free_days.png" />-->
					<?php echo $this->Html->image('/assets/design/images/free_days.png',array('escape' => false));?>
				</div>
			</div><!--/no_risk_panel-->
			
			<div class="billing_panel">
			<?php echo $this->Form->create('perform'); ?>
				<div class="col-sm-6 col-xs-12 left">
					<div class="billing_info_detail">
						<p class="error_msgs"><?php if(isset($message)){ echo $message;}?></p>
						<h5>Billing Information</h5>
						<div id="paypal-button-container" style="float: left;width: 100%;"></div>
						<?php //echo $this->Html->image('/assets/design/images/paypal_btn.png',array('escape' => false));?>
						<p>Or fill in your credit card to get access to free trial</p>
						
						<div class="cards_panel">
<!--							<img src="images/cards_icons.png" />-->
							<?php echo $this->Html->image('/assets/design/images/cards_icons.png',array('escape' => false));?>
						</div><!--/cards_panel-->
						
						<div class="billing_form">
							<div class="col-xs-12 padding_0">
							<?php echo $this->Form->input('username', array('label' => false,'name'=>'firstName','size'=>'30','maxlength'=>'32','placeholder'=>'Name on Card','required'=>'required')); ?>
							</div>
							
							<div class="col-xs-12 padding_0"> 
								<?php echo $this->Form->input('Credit Card Number', array('label' => false,'name'=>'creditCardNumber','placeholder'=>'Credit Card Number','required'=>'required')); ?>
							</div>
							
							<div class="col-xs-12 padding_0">
								<div class="col-sm-8 col-xs-12 padding_0">
									<label>Expiry (Month / Year)</label>
									<div class="col-sm-5 col-xs-12 padding_0 left"><?php 
										$options = array();
										for($i=1;$i<=12;$i++){
											$options[$i] = $i;
										}
								
										echo $this->Form->input('expDateMonth', array('type'=>'select','name'=>'expDateMonth', 'label'=>false, 'options'=>$options, 'default'=>'12'));?>
										</div>
									<div class="col-sm-7 col-xs-12 right">
									<?php 
										$yroptions = array();
										for($i=2015;$i<=2050;$i++){
											$yroptions[$i] = $i;
										}
										echo $this->Form->input('expDateYear', array('type'=>'select','name'=>'expDateYear', 'label'=>false, 'options'=>$yroptions, 'default'=>'2025'));
									?></div>
								</div>
								<div class="col-sm-4 col-xs-12 padding_0">
								<?php echo $this->Form->input('CVV', array('label' => 'Card Verification Number:','name'=>'cvv2Number','size'=>'3','maxlength'=>'4','placeholder'=>'CVV','required'=>'required')); ?>
								<!--	<label>Security Code</label>
									<div class="col-xs-12 padding_0"><input type="text" placeholder="" /></div>-->
								</div>
							</div>
							
							<div class="col-xs-12 padding_0">
							<?php echo $this->Form->input('address:', array('label' => false,'name'=>'address','placeholder'=>'Address','required'=>'required')); ?>
<!--								<input type="text" placeholder="Address" />-->
							</div>
							
							<div class="col-xs-12 padding_0">
								<?php echo $this->Form->input('City:', array('label' => false,'name'=>'city','placeholder'=>'City','required'=>'required')); ?>
							</div>
							
							<div class="col-xs-12 padding_0">
									<?php echo $this->Form->input('state:', array('label' => false,'name'=>'state','placeholder'=>'State / Province','required'=>'required')); ?>
							</div>
							
							<div class="col-xs-12 padding_0">
								<?php echo $this->Form->input('zip:', array('label' => false,'name'=>'zip','placeholder'=>'Zip / Postal Code','required'=>'required')); ?>
							</div>
							
							<div class="col-xs-12 padding_0">
								<?php echo $this->Form->input('country', array('label' => false,'name'=>'country','options' => array('US', 'AUS', 'CAN', 'IND')));?>
							</div>
							
							
						</div><!--/billing_form-->
					</div><!--/billing_info_detail-->
					
					
					<div class="order_review">
						<h5>ORDER REVIEW</h5>
						<p>Plan:<?php echo $plan_name; ?> <?php echo $description; ?></p>
						<h6>Today's Total: <span>$0 for 30 days</span></h6>
						<p>After 30 Days: $<?php echo $amount; ?>/month</p>
						<?php echo $this->Form->hidden('amount', array('label' => false,'name'=>'amount','value'=>1));?>
						<?php echo $this->Form->hidden('creditCardType', array('label' => false,'name'=>'creditCardType','id'=>'creditCardType'));?>
						<input type="hidden" name="recurring" value="1"/>
						<?php echo $this->Form->submit(__('Start My Free Trail',true), array('class'=>'start_trial_btn','value'=>'submit'));  ?>
						<p><span>By clicking "Start My Free Trial" you are agreeing to the Terms of Use and Privacy Policy.</span></p>
					</div><!--/order_review-->
				</div>
				<?php  echo $this->Form->end();?>
				
				<div class="col-sm-6 col-xs-12 right">
					<!--img src="images/client.png" />-->
					<?php echo $this->Html->image('/assets/design/images/client.png', array('alt' => 'image'));?>
					<h6>Ron Koshman</h6>
					<label>shopify store business owner</label>
					<div class="quote">
						<!--<img src="images/quote.png" />-->
						<?php echo $this->Html->image('/assets/design/images/quote.png', array('alt' => 'image'));?>
					</div>
					<p>Now I manage my invertory and orders fulfilment 2-3 times faster and everething is in one place instead of random excel spreadsheets</p>
				</div>
			</div><!--/billing_panel-->
			
		</div>
		
		<div class="extra_note">
			<div class="container">
				<h5>Why do you need my credit card for a free trial?</h5>
				<p>We ask for your credit card to allow your membership to continue after your free trial, should you choose not to cancel. This also allows us to reduce fraud and prevent multiple free trials for one person. This helps us deliver better service for all the honest customers. Remember that we won't bill you anything during your free trial and that you can cancel at any moment before your trial ends.</p>
			</div>
		</div>
			
	</section>
	<script>
	$(function() {
        $('#performCreditCardNumber').validateCreditCard(function(result) {
			$('#creditCardType').val(result.card_type.name);
			if(result.valid == false){
				$(this).css('border','2px solid red');
			}else{
				$(this).css('border','none');
			}
         });
    });
	</script>
	  <script>
        paypal.Button.render({

            env: 'sandbox', 
			  client: {
                sandbox:    '<?php echo $Paypalsandboxid =  Configure::read('Paypal.sandboxid') ;?>',
                production: '<?php echo $Paypalsandboxid =  Configure::read('Paypal.productionid') ;?>',
            },

            commit: true,

            payment: function(data, actions) {

                // Make a call to the REST api to create the payment
                return actions.payment.create({
                    payment: {
                        transactions: [
                            {
							// amount: { total: '<?php echo $amount; ?>', currency: 'USD' }
							amount: { total: '0.1', currency: 'USD' }
                            }
                        ]
                    }
                });
            },

             onAuthorize: function(data, actions) {

                return actions.payment.execute().then(function(k) {
					var formData = 'transaction_id='+data.paymentID+'&amount=1';
					var url = '../checkout_payment';
					$.post(url,formData,function(data){
						console.log(data);
					});
					$('.error_msgs').html('Payment Complete!');
                   // window.alert('Payment Complete!');
					window.location.href = 'plans/payment_response';
                });
            }

        }, '#paypal-button-container');
		
		
    </script>