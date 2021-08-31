<?php $this->AjaxValidation->active(); ?>
<div class="page-content-wrapper">
	<div class="page-content">
		<!-- BEGIN PAGE CONTENT-->
		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-12">
				<blockquote>
					<p style="font-size:16px">
						 Upload your products data file in csv format as described <a href='http://www.delivrd.com/2015/08/importing-products-into-delivrd/' target="_blank"><U>here</U></a>
					</p>
					<p style="font-size:16px">
						<?php
						 echo $this->Html->link('<i class="fa fa-cloud-upload"></i> Go to upload page','/products/uploadcsv',array('class' => 'btn blue-hoki fileinput-button','escape'=> false));
						?>
					</p>
					
				</blockquote>
				<br>
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
</div>
