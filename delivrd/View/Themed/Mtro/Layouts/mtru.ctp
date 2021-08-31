<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.1
Version: 3.6.1
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>Inventory Management and Order Management</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="Delivrd - Free Inventory Management and Order Fulfillment" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css">
<?php echo $this->Html->css('/assets/global/plugins/font-awesome/css/font-awesome.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/simple-line-icons/simple-line-icons.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap/css/bootstrap.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/uniform/css/uniform.default.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css'); ?>

<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<?php echo $this->Html->css('/assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css'); ?>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<?php echo $this->Html->css('/assets/global/css/components-md.css',array( 'id' => 'style_components')); ?>
<?php echo $this->Html->css('/assets/global/css/plugins-md.css'); ?>
<?php echo $this->Html->css('/assets/global/css/plugins.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/layout.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/themes/delivrd.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/custom.css'); ?>
<?php echo $this->Html->css('/css/toastr.min.css'); ?>
<!-- END THEME STYLES -->

<?php /*<!-- Begin Inspectlet Embed Code -->
<script type="text/javascript" id="inspectletjs">
window.__insp = window.__insp || [];
__insp.push(['wid', 1049596160]);
(function() {
function __ldinsp(){var insp = document.createElement('script'); insp.type = 'text/javascript'; insp.async = true; insp.id = "inspsync"; insp.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cdn.inspectlet.com/inspectlet.js'; var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(insp, x); };
document.readyState != "complete" ? (window.attachEvent ? window.attachEvent('onload', __ldinsp) : window.addEventListener('load', __ldinsp, false)) : __ldinsp();

})();
</script>
<!-- End Inspectlet Embed Code -->*/ ?>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-K6M6R5N');</script>
<!-- End Google Tag Manager -->
<link rel="shortcut icon" href="/favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-md page-header-fixed page-quick-sidebar-over-content ">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K6M6R5N" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	<!-- BEGIN HEADER -->
	<?php echo $this->element('top_menu'); ?>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
    <?php echo $this->element('sidebar_new'); ?>
    <!-- END SIDEBARRRR -->

	<?php echo $content_for_layout; ?>
			

<!-- BEGIN FOOTER -->
<div class="page-footer">
    <div class="page-footer-inner">
        <?php echo date('Y'); ?> © <?php echo Configure::read('OperatorName') ?>
    </div>
    <?php if($this->Session->read('productcount') > 0) : ?>
    <div class="shortcut">
        <button type="button" class="btn btn-success btn-circle btn-lg" rel="popover" id="shortcut-pop" data-placement="top" title="Shortcuts" data-popover-content="#myPopover"><i class="fa fa-ellipsis-h"></i></button>
    </div>
    <?php endif; ?>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END FOOTER -->

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../assets/global/plugins/respond.min.js"></script>
<script src="../../assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
<?php ##echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>

<?php echo $this->element('scripts'); ?>

<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<?php ##echo $this->Html->script('/assets/global/plugins/jquery-ui/jquery-ui.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap/js/bootstrap.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery.blockui.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery.cokie.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/uniform/jquery.uniform.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js'); ?>



<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->  
<?php echo $this->Html->script('/assets/global/plugins/select2/select2.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/vendor/load-image.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js'); ?>

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<?php echo $this->Html->script('/assets/global/scripts/metronic.js'); ?>
<?php echo $this->Html->script('/assets/admin/layout/scripts/layout.js'); ?>
<?php echo $this->Html->script('/assets/admin/layout/scripts/demo.js'); ?>
<?php echo $this->Html->script('/assets/global/scripts/datatable.js'); ?>
<?php echo $this->Html->script('/assets/admin/pages/scripts/ecommerce-orders.js'); ?>
<?php echo $this->Html->script('/local/dateinit.js'); ?>
<?php echo $this->Html->script('/assets/scripts/app.js'); ?>
<?php echo $this->Html->script('/assets/admin/pages/scripts/form-fileupload.js'); ?>

<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {    
    Metronic.init(); // init metronic core components
	Layout.init(); // init current layout
	FormFileUpload.init();
});

(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-61652601-2', 'auto');
ga('send', 'pageview');

</script>

<?php if(Configure::read('OperatorName') == 'Delivrd') { ?>
<script type="text/javascript" src="https://assets.freshdesk.com/widget/freshwidget.js"></script>
<script type="text/javascript">
    FreshWidget.init("", {"queryString": "&widgetType=popup&formTitle=Help+%26+Support&submitThanks=Thank+you+for+you+feedback", "utf8": "✓", "widgetType": "popup", "buttonType": "text", "buttonText": "Support", "buttonColor": "white", "buttonBg": "#156400", "alignment": "2", "offset": "235px", "submitThanks": "Thank you for you feedback", "formHeight": "500px", "url": "https://delivrd.freshdesk.com"} );
</script>
<?php } ?>

<?php /*if(Configure::read('OperatorName') == 'Delivrd' && $this->request->host() == 'delivrdapp.com') { ?>
<script>
window.intercomSettings = {
	app_id: "copjsblw",
	name: "<?php echo $this->App->username($authUser); ?>",
	email: "<?php echo $this->Session->read('Auth.User.email'); ?>", 
	created_at: "<?php echo $this->Session->read('Auth.User.created');?>",
	low_alerts: "<?php echo $this->Session->read('low_alerts'); ?>",
	invited: "<?php echo $this->Session->read('invited'); ?>"
};

(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/copjsblw';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()
</script>
<?php }*/ ?>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>