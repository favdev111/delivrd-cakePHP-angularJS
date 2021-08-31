<?php $this->AjaxValidation->active(); ?>
<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
		<?php echo $this->Session->flash(); ?>
			
<div class="row">
				<div class="col-md-12">
					<blockquote>
                                            <?php if($this->Session->read('magento') == 1) { ?>
						<p style="font-size:16px">
							 Click to import Magento orders
						</p>
						<p style="font-size:16px">
							
                                                    <a href="/orders/importmagento2"><img src="http://www.magbooster.com/wp-content/uploads/magento.png" /></a>
						</p>
                                                 <?php } ?>
                                                <?php 
                                                if($this->Session->read('woo') == 1) { ?>
						 <p style="font-size:16px">
							 Click to import Woocommerce orders
						</p>
                                                <p style="font-size:16px">
							
                                                    <a href="/orders/importwoo"><img src="https://www.skyverge.com/wp-content/uploads/2012/03/woocommerce_logo_leader.png" /></a>
						</p>
                                                 <?php } ?>
                                                 <?php if($this->Session->read('magento') != 1 && $this->Session->read('woo') != 1) { ?>
					 <p style="font-size:16px">
							 No integration defined.
						</p>
                                               <?php } ?>
					</blockquote>
					<br>
					
					
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	</div> 
	</div>
	</div> 