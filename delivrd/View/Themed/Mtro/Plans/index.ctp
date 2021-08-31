<section class="pricing_panel">
		<div class="container">
			<h2>To finish creating your Delivrd account please choose a plan below</h2>
			<p>Start managing your inventory & orders fulfillment in less than 60 seconds</p>
			
			<div class="chose_plan">
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="chose_plan_detail">
						<div class="top">
							<h6>ENTERPRISE</h6>
							<p>Custom solutions for any need</p>
						</div>
						<div class="bottom">
							<!-- <p>$120 monthly</p> -->
						</div>	<?php //die('sdfdslkfhnds');?>
						<?php echo $this->Html->link('Try for free',	array('controller' => 'PlanView','action' => 'index','full_base' => true)); ?>
					</div><!--/chose_plan_detail-->
				</div>
				
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="chose_plan_detail">
						<div class="top">
							<h6>PRO</h6>
							<p>All you're ever need</p>
						</div>
						<div class="bottom">
							<p>$120 monthly</p>
						</div>
						<?php echo $this->Html->link('Try for free',	array('controller' => 'plans','action' => 'index','plan_id'=>2,'full_base' => true)); ?>
					</div><!--/chose_plan_detail-->
				</div>
				
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="chose_plan_detail">
						<div class="top">
							<h6>PLUS</h6>
							<p>Most popular</p>
						</div>
						<div class="bottom">
							<p>$49,9 monthly</p>
						</div>
						<?php echo $this->Html->link('Try for free',	array('controller' => 'plans','action' => 'index','plan_id'=>3,'full_base' => true)); ?>
					</div><!--/chose_plan_detail-->
				</div>
				
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="chose_plan_detail">
						<div class="top">
							<h6>BASIC</h6>
							<p>up to 25 products</p>
						</div>
						<div class="bottom">
							<p>Free forever!</p>
						</div>
						<?php echo $this->Html->link('Try for free',	array('controller' => 'PlanView','action' => 'index','full_base' => true)); ?>
					</div><!--/chose_plan_detail-->
				</div>
				
			</div><!--/chose_plan-->
		</div><!--/container-->
	</section><!--/pricing_panel-->