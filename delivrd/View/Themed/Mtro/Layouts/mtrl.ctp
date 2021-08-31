<!DOCTYPE html>
<!--
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.1
Version: 3.3.0
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
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css">
<?php echo $this->Html->css('/assets/global/plugins/font-awesome/css/font-awesome.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/simple-line-icons/simple-line-icons.min.css'); ?>
<?php echo $this->Html->css('/plugins/switchery/dist/switchery.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap/css/bootstrap.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/uniform/css/uniform.default.css'); ?>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->

<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<?php echo $this->Html->css('/assets/global/css/components-md.css',array( 'id' => 'style_components')); ?>
<?php echo $this->Html->css('/assets/global/css/plugins-md.css'); ?>
<?php echo $this->Html->css('/assets/admin/pages/css/login.css?v=0.0.2'); ?>
<?php echo $this->Html->css('/assets/global/css/plugins.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/layout.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/themes/delivrd.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/custom.css'); ?>
<?php echo $this->Html->css('/css/toastr.min.css'); ?>

<?php /*if($this->request->host() == 'delivrdapp.com') { ?>
<!-- Begin Inspectlet Embed Code -->
<script type="text/javascript" id="inspectletjs">
window.__insp = window.__insp || [];
__insp.push(['wid', 1049596160]);
(function() {
function __ldinsp(){var insp = document.createElement('script'); insp.type = 'text/javascript'; insp.async = true; insp.id = "inspsync"; insp.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cdn.inspectlet.com/inspectlet.js'; var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(insp, x); };
document.readyState != "complete" ? (window.attachEvent ? window.attachEvent('onload', __ldinsp) : window.addEventListener('load', __ldinsp, false)) : __ldinsp();

})();
</script>
<!-- End Inspectlet Embed Code -->
<?php } */?>

<script type="text/javascript">
var siteUrl = '<?php echo Router::url('/', true); ?>';
</script>

<?php if($this->request->host() == 'delivrdapp.com') { ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-K6M6R5N');</script>
<?php } ?>
<!-- End Google Tag Manager -->
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="/favicon.ico"/>
<?php echo  $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
</head>
<!-- END HEAD -->
  <?php echo $content_for_layout; ?>

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../assets/global/plugins/respond.min.js"></script>
<script src="../../assets/global/plugins/excanvas.min.js"></script>
<![endif]-->

<?php echo $this->element('scripts'); ?>

<?php echo $this->Html->script('/plugins/bootbox/bootbox.min.js'); ?>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap/js/bootstrap.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery.blockui.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery.cokie.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/uniform/jquery.uniform.min.js'); ?>
<?php echo $this->Html->script('/plugins/switchery/dist/switchery.min.js'); ?>


<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<?php echo $this->Html->script('/assets/global/scripts/metronic.js'); ?>
<?php echo $this->Html->script('/assets/admin/layout/scripts/layout.js'); ?>
<?php echo $this->Html->script('/assets/admin/layout/scripts/demo.js'); ?>

<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {
  Metronic.init(); // init metronic core components
  Layout.init(); // init current layout
});
</script>
<?php if($this->request->host() == 'delivrdapp.com') { ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-61652601-2', 'auto');
  ga('send', 'pageview');
</script>
<?php } ?>
<script>
$('#e-commerce').change(function() {
  console.log($("#e-commerce").attr('checked'));
  if ($("#e-commerce").attr('checked') == undefined){
    $('#user_stores').hide();
  }
  else if($("#e-commerce").attr('checked') == 'checked') {
    $('#user_stores').show();
  }
});

$("#fullfilling_order").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var $btn = $('#signup').button('Loading ...');

    $.ajax({
        type: 'POST',
        url: siteUrl + 'firststep',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (response) {
            var messageType = (response.status == true) ? 'alert' : 'danger';

            var message = '<div class="alert alert-' + messageType + '"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' + response.message + '</div>';

            if (response.status == false) {
                toastr['error'](response.message);
                $btn.button('reset');
            } else {
                if(response.url !== undefined) {
                    window.location = response.url;
                } else {
                    window.location = '<?php echo $this->Html->url('/'); ?>';
                }
            }

        }
    });
    return false;
});

$('#show-password').click(function() {
    if ($(this).is(':checked')) {
        $('#password').attr('type', 'text');
    } else {
        $('#password').attr('type', 'password');
    }
});
function camelcase(inputstring) {
    var a = inputstring.split('_'), i;
    s = [];
    for (i = 0; i < a.length; i++) {
        s.push(a[i].charAt(0).toUpperCase() + a[i].substring(1));
    }
    s = s.join('');
    return s;
}
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
