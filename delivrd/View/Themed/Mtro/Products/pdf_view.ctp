<style>
@media print {
  	@page {
        size: auto;   /* auto is the initial value */
        margin: 6mm;    /* this affects the margin in the printer settings */
    }
    div.bg-grey-salsa {background: #ACB5C3;}
    img.img-responsive {
    	max-height: 256px !important;
    	vertical-align: middle;
    }
    div.col-xs-8 {
    	font-size:6pt;
    }
    div.col-xs-4 {
    	font-size:6pt;
    }
    div.lead {
    	font-size:8pt;
    }

}
</style>
<div class="page-content-wrapper">
    <div class="page-content">
    	<div class="row">
    		<div class="col-md-12 text-right">
    			<a class="btn btn-lg blue hidden-print margin-bottom-5" style="margin-right: 5px;" onclick="javascript:window.print();">Print <i class="fa fa-print"></i></a>
    		</div>
    	</div>
    	<div class="tiles">
		<?php foreach ($products as $product) { ?>
			<div class="col-xs-4 block" style="margin-top: 10px;">
				<div class="bg-grey-salsa clearfix">
					<div class="text-center">
						<div class="imgWrapper" style="display: inline-block;margin-top: 20px;height: 256px;">
							<?php if( strpos($product['Product']['imageurl'], 'no-photo.svg')) { ?>
							<img class="img-responsive" style="height: 256px;" src="<?php echo h($product['Product']['imageurl']); ?>">
							<?php } else { ?>
							<img class="img-responsive" style="max-height: 256px;" src="<?php echo h($product['Product']['imageurl']); ?>">
							<?php } ?>
						</div>
					</div>
					<div style="margin: 10px 10px">
						<div class="lead" style="white-space: nowrap;overflow: hidden;margin-bottom: 0px;"><?php echo h($product['Product']['name']); ?></div>
						<div class="row">
							<div class="col-xs-8">
								SKU: <?php echo h($product['Product']['sku']); ?>
							</div>
							<div class="col-xs-4 text-right">
								<?php echo h($currency['Currency']['csymb']); ?><?php echo h($product['Product']['value']); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
		</div>
		<div class="row">
    		<div class="col-md-12 text-right">
    			<a class="btn btn-lg blue hidden-print margin-bottom-5" style="margin-right: 5px;" onclick="javascript:window.print();">Print <i class="fa fa-print"></i></a>
    		</div>
    	</div>
	</div>
</div>
