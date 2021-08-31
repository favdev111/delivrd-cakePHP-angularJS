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

<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<?php echo $this->Html->css('/assets/global/plugins/select2/select2.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datepicker/css/datepicker.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datepicker/css/datepicker3.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/jquery-minicolors/jquery.minicolors.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-tagsinput/jquery.tagsinput.css'); ?>



<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<?php echo $this->Html->css('/assets/global/css/components-md.css',array( 'id' => 'style_components')); ?>
<?php echo $this->Html->css('/assets/global/css/plugins-md.css'); ?>
<?php echo $this->Html->css('/assets/global/css/plugins.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/layout.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/themes/delivrd.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/custom.css?v=0.0.1'); ?>
<?php echo $this->Html->css('/assets/global/css/joyride-2.1.css'); ?>
<?php echo $this->Html->css('/js/jquery-ui/jquery-ui.css'); ?>
<?php echo $this->Html->css('/css/toastr.min.css'); ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

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

<script type="text/javascript" >
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
<body class="page-md page-header-fixed page-quick-sidebar-over-content ">
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

	<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 <?php echo date('Y'); ?> © <?php echo Configure::read('OperatorName') ?>
	</div>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>


<?php // echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<?php echo $this->element('scripts'); ?>

<?php ##echo $this->Html->script('/js/jquery-1.12.4.js'); ?>
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


<?php echo $this->Html->script('/local/dateinit.js'); ?>
<?php echo $this->Html->script('/assets/scripts/app.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.cookie.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/modernizr.mq.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.joyride-2.1.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/select2/select2.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-tagsinput/jquery.tagsinput.js'); ?>
<?php echo $this->Html->script('/js/category.js'); ?>
<?php echo $this->Html->script('/js/jquery-code-scanner.js'); ?>

<?php echo $this->Html->script('/js/functions.js?v=0.0.5'); ?>
<?php echo $this->Html->script('/js/angular.min.js'); ?>
<?php echo $this->Html->script('/js/angular-apps.js'); ?>
<?php echo $this->Html->script('/plugins/bootbox/bootbox.min.js'); ?>


<script>
$('.code-scan').codeScanner();

toastr.options = {
    tapToDismiss: false,
    closeButton: true,
    closeHtml: '<button><i class="fa fa-close"></i></button>',
    timeOut: false
};

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
		$(function() {
			var regex4 = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;// Email address
			$('#multiple_email').tagsInput({
				width: 'auto',
				'height' :'28px',
				pattern: regex4,
				'defaultText':'Enter an email address',
			});
		});

	$(function(){
	    $('[rel="popover"]').popover({
	        container: 'body',
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

           Metronic.init(); // init metronic core components
Layout.init(); // init current layout
ComponentsFormTools2.init();
// UIGeneral.init();•••••••
DateInit.init();
App.init();



 EcommerceOrders.init();


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

$("#autocomplete").autocomplete({
  source: function( request, response ) {
  	$("#location-id").val('');
  	$("#serial-number").val('');
  	$("#pdt-type").val('');
          $.ajax({
                    url: siteUrl + 'products/get_auto_list',
                    dataType: "json",
                    data: {
                        key: request.term
                    },
                    success: function( data, test ) {
                    	if(data.location !== undefined)
                    	 $("#location-id").val(data.location);

                    	if(data.serialnumber !== undefined)
                    	 $("#serial-number").val(data.serialnumber);

                    	if(data.serialnumber !== undefined)
                    	 $("#pdt-type").val(data.type);

                        response( data.name );
                    }
          });
      }
});

</script>


<!-- <?php // echo $this->fetch('jsSection');
//echo $this->fetch('ajax_validation'); ?> -->
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
