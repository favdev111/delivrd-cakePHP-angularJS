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
<html lang="en" ng-app="delivrd-app">
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
<?php echo $this->Html->css('/assets/global/plugins/jquery-file-upload/css/jquery.fileupload'); ?>
<?php echo $this->Html->css('/assets/global/plugins/jquery-file-upload/css/jquery.fileupload'); ?>

<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<?php echo $this->Html->css('/assets/global/plugins/select2/select2.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datepicker/css/datepicker.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datepicker/css/datepicker3.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/jquery-minicolors/jquery.minicolors.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css'); ?>

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
<?php echo $this->Html->css('/js/jquery-ui/jquery-ui.css'); ?>
<?php echo $this->Html->css('/css/toastr.min.css'); ?>
<?php echo $this->Html->css('/css/custom.css?v=0.0.85'); ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script type="text/javascript" >
    var siteUrl = '<?php echo Router::url('/', true); ?>';
</script>

<?php /*<!-- Begin Inspectlet Embed Code -->
<?php if($this->request->host() == 'delivrdapp.com') { ?>
    <script type="text/javascript" id="inspectletjs">
    window.__insp = window.__insp || [];
    __insp.push(['wid', 1049596160]);
    (function() {
    function __ldinsp(){var insp = document.createElement('script'); insp.type = 'text/javascript'; insp.async = true; insp.id = "inspsync"; insp.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cdn.inspectlet.com/inspectlet.js'; var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(insp, x); };
    document.readyState != "complete" ? (window.attachEvent ? window.attachEvent('onload', __ldinsp) : window.addEventListener('load', __ldinsp, false)) : __ldinsp();
    })();
    </script>
<?php } ?>
<!-- End Inspectlet Embed Code --> */?>

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
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->

<body class="page-md page-header-fixed page-quick-sidebar-over-content">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K6M6R5N"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- BEGIN HEADER -->
<?php echo $this->element('top_menu'); ?>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <?php echo $this->element('sidebar_new'); ?>
    <!-- END SIDEBARRRR -->

    <?php echo $content_for_layout; ?>

<div id="myPopover" class="hide actionbar">
    <div class="tip">
        <ul>
        <?php foreach($this->Session->read('shortcut') as $shortcut) { ?>
            <li class="actnbr-signup actnbr-hidden" style="display: block"><a href="<?php echo $this->base . $shortcut['UserShortcutLink']['url']; ?>"><?php echo $shortcut['UserShortcutLink']['name']; ?></a></li>
        <?php } ?>
        <?php /*if($authUser['settings']) {
          pr(json_encode($authUser['settings']));
        } ?>
        <?php foreach($authUser['settings'] as $shortcut) { ?>
            <li class="actnbr-signup actnbr-hidden"><a href="<?php echo $this->base . $shortcut['UserShortcutLink']['url']; ?>"><?php echo $shortcut['UserShortcutLink']['name']; ?></a></li>
        <?php }*/ ?>
        </ul>
    </div>
</div>

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

<?php echo $this->element('scripts'); ?>


<?php #3echo $this->Html->script('/assets/global/plugins/jquery-migrate.min.js'); ?>
<?php #echo $this->Html->script('/js/jquery-1.12.4.js'); ?>
<?php ##echo $this->Html->script('/js/jquery-ui/jquery-ui.js'); ?>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<?php // echo $this->Html->script('/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap/js/bootstrap.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery.blockui.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/jquery.cokie.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/uniform/jquery.uniform.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js'); ?>
<?php ##echo $this->Html->script('/assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js'); ?>
<!-- END CORE PLUGINS -->

<!-- BEGIN PAGE LEVEL PLUGINS -->
<?php echo $this->Html->script('/assets/global/plugins/jquery-validation/js/jquery.validate.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.pulsate.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-minicolors/jquery.minicolors.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-selectsplitter/bootstrap-selectsplitter.min.js'); ?>

<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<?php echo $this->Html->script('/assets/global/scripts/metronic.js'); ?>
<?php echo $this->Html->script('/assets/admin/layout/scripts/layout.js'); ?>
<?php echo $this->Html->script('/assets/admin/layout/scripts/demo.js'); ?>
<?php echo $this->Html->script('/assets/global/scripts/datatable.js'); ?>
<?php echo $this->Html->script('/assets/admin/pages/scripts/components-form-tools.js'); ?>
<?php echo $this->Html->script('/assets/admin/pages/scripts/ecommerce-orders.js'); ?>
<?php echo $this->Html->script('/assets/admin/pages/scripts/ui-general.js'); ?>
<?php echo $this->Html->script('/assets/admin/pages/scripts/components-form-tools2.js'); ?>


<?php echo $this->Html->script('/local/dateinit.js?v=0.0.1'); ?>
<?php echo $this->Html->script('/assets/scripts/app.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.cookie.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/modernizr.mq.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.joyride-2.1.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/select2/select2.min.js'); ?>

<?php echo $this->Html->script('/assets/global/plugins/bootstrap-tagsinput/jquery.tagsinput.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js'); ?>
<?php echo $this->Html->script('/js/category.js?v=0.0.1'); ?>
<?php echo $this->Html->script('/js/jquery-code-scanner.js'); ?>

<?php echo $this->Html->script('/js/functions.js?v=0.0.6'); ?>
<?php echo $this->Html->script('/js/angular.min.js'); ?>
<?php echo $this->Html->script('/js/ui-bootstrap-tpls-0.12.0.min'); ?>
<?php echo $this->Html->script('/js/angular-apps.js'); ?>
<?php echo $this->Html->script('/plugins/bootbox/bootbox.min.js'); ?>
<?php echo $this->Html->script('/js/jquery.scannerdetection.js'); ?>

<?php echo $this->Html->script('/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js'); ?>


<?php echo $this->fetch('pageBlock'); ?>
<!-- END PAGE LEVEL SCRIPTS -->

<?php echo $this->element('invitation'); ?>

<script>
$('.code-scan').codeScanner();

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

    $('a.statlink').click(function(){
      var $link = $(this).data('link');
      $.ajax({
        url: siteUrl + 'user/add_stats/',
        dataType: "json",
        method: 'POST',
        data: {
            page: $link
        }
      });
    })
});


function getModel(target){
  var name = $(target).attr("name");
  name_match = name.match(/data\[(.+)\]\[(.+)\]/);
  return name_match[1];
}

function getField(target){
  var name = $(target).attr("name");
  name_match = name.match(/data\[(.+)\]\[(.+)\]/);
  return name_match[2];
}

function getParentDiv(target){
  return $(target).parent('div.input');
}

function getErrorMessageDiv(parentDiv){
  return parentDiv.children("div.error-message");
}

function addErrorMessage(data,id){
  $('#' + id).after("<div class='error-message'>" + data + "</div>");
}

function hasParentError(parentDiv){
  parentDivClass = parentDiv.attr("class");
  hasError = (typeof( parentDivClass ) != 'undefined' && parentDivClass.indexOf("error")!=-1);
  return hasError;
}

function hasErrorMessage(parentDiv){
  errorMessageDiv = parentDiv.find("div.error-message");
  hasError = ( errorMessageDiv[0] !== undefined );
  return hasError;
}

</script>
<script>

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
    });

      $("#takeTheTour").click(function(e) {
        $('#joyRideTipContent').joyride({
          autoStart : true,
          postStepCallback : function (index, tip) {
          if (index == 8) {
            $(this).joyride('set_li', false, 1);
          }
        },
        modal:true,
        expose: true
        });
      });
      $(".multiple").select2({
          placeholder: "Select Bin"
        });
</script>



<!-- END PAGE LEVEL SCRIPTS -->
<script>
    jQuery(document).ready(function() {
        Metronic.init(); 
        Layout.init(); 
        ComponentsFormTools2.init();
        DateInit.init();
        App.init();
    });
</script>
<?php if(Configure::read('OperatorName') == 'Delivrd' && $this->request->host() == 'delivrdapp.com') { ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-61652601-2', 'auto');
  ga('send', 'pageview');
</script>
<?php }  ?>
<script type="text/javascript">
$("#product").click(function (e) {
  $("#add-product").attr("data-val", "product");
});

$("#stock").click(function (e) {
 $("#add-product").attr("data-val", "stock");
});

$("#add-product").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var formVal = $(this).attr("data-val");
    $( ".error-message" ).remove();
    $.ajax({
        type: 'POST',
        url: siteUrl + 'products/getproduct',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (response) {

            if(response.data){
               $.each(response.data, function(model, errors) {
                   for (fieldName in this) {

                       var element = $("#" + camelcase(model + '_' + fieldName));
                       var create = $(document.createElement('div')).insertAfter(element);
                       create.addClass('error-message').text(this[fieldName][0]);
                   }
               });
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
                         bootbox.alert(response.message, function(result){

                         });
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

$('#searchkey').autocomplete({
    source: function (query, response) {
        $.ajax({
            url: siteUrl + 'get-address',
            data: {
                key: query.term
            },
            dataType: "json",
            success: function (data) {
                    response(data);
            }
        });
    },
    select: function (event, ui) {
        $.ajax({
            url: siteUrl + 'show-address/' + ui.item.id,
            dataType: "json",
            success: function (data) {
             $(".close").click();
             $("#searchkey").val('');
                for(var key in data){
                    if(key == 'country_id')
                        $('#country_id').select2().select2('val', data[key]);
                    if(key == 'state_id' && data['state_id'] != '') {
                        $('#state_id-div').show();
                        $('#stateprovince-div').hide();
                        $('#state_id').select2().select2('val', data[key]);
                    }
                    $('#' + key).val(data[key]);
                }

            }
        });
    }
});
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

<form action="<?php echo $this->Html->url(['controller' => 'products', 'action' => 'upload']); ?>" id="ProductImageFormCCC" method="POST" enctype="multipart/form-data" style="display:none;">
    <input type="hidden" name="data[Product][id]" id="ProductIdCCC" value="new">
    <input type="file" name="data[Product][imageurl]" id="ProductImageurlCCC">
</form>

<script>
    $(function(){
        /*$('.productImage').tooltip({
            placement: 'right',
            title: 'Upload Image<div class="text-center"><i class="fa fa-upload"></i></div>',
            html: true,
            template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        })*/
        $('.productImage').popover({
            placement: 'right',
            content: '<a class="uploudProductPopover" style="text-decoration:none;"><i class="fa fa-upload"></i> Upload Image</a>',
            html: true,
            trigger: 'manual',
            template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
            delay: 0
        }).on("mouseenter", function () {
            var _this = this;
            $(this).popover("show");
            $(".popover").on("mouseleave", function () {
                $(_this).popover('hide');
            });
        }).on("mouseleave", function () {
            var _this = this;
            setTimeout(function () {
                if (!$(".popover:hover").length) {
                    $(_this).popover("hide");
                }
            }, 200);
        });

        $('body').on('click', '.uploudProductPopover', function(){
            $(this).parents('.popover').prev('img').trigger('click');
            //alert(id);
            //alert($('img[aria-descibedby='+ id +']').attr('src'));
        });

        $('[rel=product_img]').click(function() {
            var img = $(this);
            $('#ProductIdCCC').val($(this).data('id'));
            $('#ProductImageurlCCC').trigger('click');
            return false;
        });

        $('#ProductImageurlCCC').change(function(){
            $('#ProductImageFormCCC').submit();
        });
        $('#ProductImageFormCCC').submit(function() {
            var form = $(this);
            var data = new FormData($('#ProductImageFormCCC')[0]);
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: data,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response.action == 'success') {
                        var img = $('img[rel=product_img][data-id='+ response.id +']');
                        img.attr('src', response.imageurl);
                        if(img.data('input') != undefined) {
                            $('#'+img.data('input')).val(response.imageurl);
                        }
                    } else {
                        toastr.error(response.msg);
                    }
                }
            });
            return false;
        });
    });
</script>
<?php echo $this->fetch('jsAction'); ?>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>