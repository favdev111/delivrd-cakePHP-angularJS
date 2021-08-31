<?php $this->AjaxValidation->active();

 ?>
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE HEADER-->
			
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->
		
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box blue-steel">
						<div class="portlet-title">							
							<div class="caption">
							<h2 class="text-center">Payment Status</h2>
							</div>
						</div>
						<div class="portlet-body">
							<h2 class="text-center">
								<?php if(isset($message)) echo $message;?>
							</h2>
							<h2 class="error_msg text-center"><?php if(isset($error)) echo $error;?></h2>
						</div>
					</div>
					
				</div>
			
			</div>
			
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
<style>
.error_msg {color:red;}
</style>