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
<?php echo $this->Html->css('/assets/global/plugins/select2/select2.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datepicker/css/datepicker.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/fullcalendar/fullcalendar.min.css'); ?>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<?php echo $this->Html->css('/assets/global/css/components-md.css',array( 'id' => 'style_components')); ?>
<?php echo $this->Html->css('/assets/global/css/plugins-md.css'); ?>
<?php echo $this->Html->css('/assets/global/css/plugins.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/layout.css'); ?>
<?php # echo $this->Html->css('/assets/admin/layout/css/themes/darkblue.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/themes/delivrd.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/custom.css?v=0.0.2'); ?>
<?php echo $this->Html->css('/assets/global/css/joyride-2.1.css'); ?>
<?php echo $this->Html->css('/css/toastr.min.css'); ?>

<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<?php echo $this->Html->css('/assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css'); ?>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->


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
<?php }*/ ?>

<script>
    var siteUrl = '<?php echo Router::url('/', true); ?>';
</script>

<?php if($this->request->host() == 'delivrdapp.com') { ?>
  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-K6M6R5N');</script>
  <!-- End Google Tag Manager -->
<?php } ?>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
<body class="page-header-fixed page-quick-sidebar-over-content page-md">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K6M6R5N"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- BEGIN HEADER -->
<?php echo $this->element('top_menu'); ?>
<!-- END HEADER -->
<div class="clearfix"></div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <?php echo $this->element('sidebar_new'); ?>
    <!-- END SIDEBAR -->

    <?php echo $content_for_layout; ?>

    <!-- BEGIN FOOTER -->

<div id="myPopover" class="hide actionbar">
    <div class="tip">
        <ul>
        <?php foreach($this->Session->read('shortcut') as $shortcut) {
            echo '<li class="actnbr-signup actnbr-hidden"><a href="' . $this->base . $shortcut['UserShortcutLink']['url'] .'">' . $shortcut['UserShortcutLink']['name'] . '</a></li>';
        } ?>
        </ul>
    </div>
</div>
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

<?php echo $this->element('scripts'); ?>

<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<?php #echo $this->Html->script('/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap/js/bootstrap.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery.blockui.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery.cokie.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/uniform/jquery.uniform.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js'); ?>
<?php  echo $this->Html->script('/assets/global/plugins/powertour.2.8.0.min.js'); ?>


<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<?php echo $this->Html->script('/assets/global/plugins/jquery-validation/js/jquery.validate.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/select2/select2.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.pulsate.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.sparkline.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-daterangepicker/moment.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/fullcalendar/fullcalendar.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js'); ?>
<!-- BEGIN PAGE LEVEL PLUGINS -->

<?php echo $this->Html->script('/assets/global/plugins/jqvmap/jqvmap/jquery.vmap.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.min.js'); ?>
<?php  echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.resize.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.pie.min.js'); ?>
<?php  echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.stack.min.js'); ?>
<?php  echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.crosshair.min.js'); ?>
<?php  echo $this->Html->script('/assets/global/plugins/flot/jquery.flot.categories.min.js'); ?>

<!-- END PAGE LEVEL PLUGINS -->
<?php  echo $this->Html->script('/assets/global/plugins/bootbox/bootbox.min.js'); ?>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<?php  echo $this->Html->script('/assets/admin/pages/scripts/tasks.js'); ?>
<?php  echo $this->Html->script('/assets/admin/pages/scripts/ui-alert-dialog-api.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.cookie.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/modernizr.mq.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.joyride-2.1.js'); ?>
<?php echo $this->Html->script('/plugins/bootbox/bootbox.min.js'); ?>


<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->

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

<?php  echo $this->Html->script('/assets/global/scripts/metronic.js'); ?>
<?php   echo $this->Html->script('/assets/admin/layout/scripts/layout.js'); ?>
<?php  echo $this->Html->script('/assets/admin/layout/scripts/demo.js'); ?>
<?php  echo $this->Html->script('/assets/global/scripts/datatable.js'); ?>
<?php  echo $this->Html->script('/assets/admin/pages/scripts/ecommerce-orders.js'); ?>
<?php  echo $this->Html->script('/assets/admin/pages/scripts/charts-flotcharts.js'); ?>
<?php  echo $this->Html->script('/local/index.js'); ?>
<?php  echo $this->Html->script('/local/acharts.js'); ?>
<?php echo $this->Html->script('/assets/admin/pages/scripts/form-fileupload.js'); ?>

<?php echo $this->Html->script('/assets/scripts/app.js'); ?>

<!-- END PAGE LEVEL SCRIPTS -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger label label-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn blue start" disabled>
                    <i class="fa fa-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn red cancel">
                    <i class="fa fa-ban"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-download fade">
                <td>
                    <span class="preview">
                        {% if (file.thumbnailUrl) { %}
                            <a href="{%=file.url%}" title="{%=file.orgname%}" download="{%=file.orgname%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                        {% } %}
                    </span>
                </td>
                <td>
                    <p class="name">
                        {% if (file.url) { %}
                            <a href="{%=file.url%}" title="{%=file.orgname%}" download="{%=file.orgname%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.orgname%}</a>
                        {% } else { %}
                            <span>{%=file.orgname%}</span>
                        {% } %}
                    </p>
                    {% if (file.error) { %}
                        <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                    {% } %}
                </td>
                <td>
                    <span class="size">{%=o.formatFileSize(file.size)%}</span>
                </td>
                <td>

                        <a href="/delivrd/products/importcsv/{%=file.name%}" class="btn blue"><i class="fa fa-barcode"></i>Create Products</a>

                </td>

            </tr>
        {% } %}
    </script>

<script>
jQuery(document).ready(function() {
    Metronic.init();
    Layout.init();
    FormFileUpload.init();
    App.init();
});

$(function(){
    $('#shortcut-pop').popover({
        html: true,
        content: function () {
            var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
            return clone;
        }
    }).click(function(e) {
        e.preventDefault();
    });
});

$(function(){
    $('#supplier-pop').popover({
        html: true,
        content: function () {
            var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
            return clone;
        }
    }).click(function(e) {
        e.preventDefault();
    });

    $('[data-toggle="popover"]').popover()
});

toastr.options = {
    tapToDismiss: false,
    closeButton: true,
    closeHtml: '<button><i class="fa fa-close"></i></button>',
    timeOut: false
};

$("#takeTheTour").click(function(e) {
$('#joyRideTipContent').joyride({
  autoStart : true,
  postStepCallback : function (index, tip) {
  if (index == 2) {
    $(this).joyride('set_li', false, 1);
  }
},
modal:true,
expose: true
});
});

<?php $invitation = $this->requestAction(array('plugin'=>'networks', 'controller' => 'networks', 'action' => 'getinvites')); ?>
<?php if($invitation) { ?>
</script>
<div class="modal fade modal-opacity" id="inviteModal">
    <div class="modal-dialog new-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo $invitation['Network']['CreatedByUser']['email']; ?> Invited you to join his network <strong><?php echo $invitation['Network']['name']; ?></strong>!</h4>
            </div>

            <div class="modal-body">
                <p class="text-center lead">Please accept or decline invitation.</p>
            </div>

            <div class="modal-footer">
                <div class="text-right">
                    <a id="declineInv" href="<?php echo $this->Html->url(array('plugin'=>'networks', 'controller'=>'networks', 'action'=>'decline', $invitation['NetworksInvite']['id'])); ?>" class="btn btn-danger">Decline</a>
                    <a href="<?php echo $this->Html->url(array('plugin'=>'networks', 'controller'=>'networks', 'action'=>'accept', $invitation['NetworksInvite']['id'])); ?>" class="btn btn-success">Accept</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    var countpdt = <?php echo $this->Session->read('productcount') ?>;
    $('#inviteModal').modal('show');
    $('#declineInv').click(function(){
        var link = $(this);

        $.ajax({
            type: 'POST',
            url: link.attr('href'),
            data: '',
            dataType:'json',
            beforeSend: function() {

            },
            success:function (r, status) {
                $('#inviteModal').modal('hide');
                if(countpdt == 0) {
                    //$("#button").click();
                }
            }
        });
        return false;
    })
});

<?php } else { ?>
    <?php if(!$this->Session->read('Auth.User.is_limited') && empty($_access)) { ?>
    $(document).ready(function () {
        var countpdt = <?php echo $this->Session->read('productcount') ?>;
        if(countpdt == 0) {
            $("#button").click();
        }
    });
    <?php } ?>
<?php } ?>

$('#import-csv').click(function(){
   $('#buttons-pop').hide();
});
$('#pdt-manually').click(function(){
   $('#buttons-pop').hide();
});

var data1 = <?php echo $jarrstr ?>

$("#stock").click(function (e) {
 $("#add-product").attr("data-val", "stock");
});

$('#showMainSearch1').click(function(){
    if($('#searchWrapper1').css('display') == 'block') {
        $('#searchWrapper1').css('display', 'none');
    } else {
        $('#searchWrapper1').css('display', 'block');
    }
    return false;
});

$("#add-product").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var formVal = $(this).attr("data-val");
    $( ".error-message" ).remove();
    $('#productErrors').html('').addClass('hide');
    $.ajax({
        type: 'POST',
        url: siteUrl + 'products/addproduct',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (response) {
            if(response.errors){
                /*$.each(response.errors, function (model, errors) {
                  bootbox.alert(response.errors[model][0], function(result){
                  });
                });*/
                //toastr['error'](response.success);
                //console.log(response.errors);
                var error_str = '';
                $.each(response.errors, function(key, value){
                    error_str += '<div>'+ value[0] +'</i>';
                });
                $('#productErrors').html(error_str).removeClass('hide');
            } else {
                $("#add-product")[0].reset();
                if(formVal == 'stock') {
                    bootbox.alert({
                        message : "Now that you have created your first products, it's time to perform inventory transactions. Click on the 'Actions' button to count, issue and receive inventory.",
                        callback : function (result) {
                            url = siteUrl + 'inventories/index?create=1';
                            window.location.href = url;
                        }
                    });
                } else {
                     if(response.status == true) {
                        /* bootbox.alert(response.success, function(result){
                           $("#add-product")[0].reset();
                         });*/
                        if(response.status == true) {
                            toastr['success'](response.success);
                            $("#add-product")[0].reset();
                        } else {
                            
                        }
                     }
                }

            }
        }
    });
    return false;
});

function camelcase(inputstring) {
    var a = inputstring.split('_'), i;
    s = [];
    for (i=0; i<a.length; i++){
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
created_at: "<?php echo strtotime($this->Session->read('Auth.User.created')); ?>",
prd: "<?php echo $this->Session->read('productcount'); ?>",
tx: "<?php echo $this->Session->read('invcount'); ?>",
so: "<?php echo $this->Session->read('saleCount'); ?>",
po: "<?php echo $this->Session->read('replCount'); ?>",
wv: "<?php echo $this->Session->read('wavecount'); ?>",
oso: "<?php echo $this->Session->read('opensaleCount'); ?>",
rso: "<?php echo $this->Session->read('releasesaleCount'); ?>",
sr: "<?php echo $this->Session->read('serialcount'); ?>",
intg: "<?php echo $this->Session->read('integration'); ?>",
type: "<?php echo $this->Session->read('roletype'); ?>",
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
