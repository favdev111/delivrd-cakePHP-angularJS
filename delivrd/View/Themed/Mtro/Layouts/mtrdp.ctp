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
<title>Delivrd</title>
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
<?php echo $this->Html->css('/assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css'); ?>
<?php echo $this->Html->css('/assets/global/plugins/fullcalendar/fullcalendar.min.css'); ?>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<?php echo $this->Html->css('/assets/global/css/components-md.css',array( 'id' => 'style_components')); ?>
<?php echo $this->Html->css('/assets/global/css/plugins-md.css'); ?>
<?php echo $this->Html->css('/assets/global/css/plugins.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/layout.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/themes/delivrd.css'); ?>
<?php echo $this->Html->css('/assets/admin/layout/css/custom.css?v=0.0.1'); ?>

<!-- END THEME STYLES -->
<?php if($this->request->host() == 'delivrdapp.com') { ?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-K6M6R5N');</script>
<?php } ?>
<!-- End Google Tag Manager -->
<link rel="shortcut icon" href="favicon.ico"/>

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
<body class="page-header-fixed page-quick-sidebar-over-content ">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K6M6R5N"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="/">
			<img src="/theme/Mtro/assets/admin/layout/img/logo.png" alt="logo" class="logo-default"/>
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<?php if($_authUser['User']['role'] !== 'paid' && $_authUser['User']['role'] == 'trial') {
                $today = new DateTime();
                $today1 = date_format($today, 'Y-m-d');
                $expiredDate = date('Y-m-d', strtotime($_authUser['Subscription']['expiry_date']));

                $expireDay = new DateTime($expiredDate);
                $remaining_days = $today->diff($expireDay)->format('%R%a');
                $remaining_days  = ($remaining_days > 0) ? $remaining_days : 0;

                echo '<span class="username username-hide-on-mobile subscribe-btn">' .$remaining_days .' days of free trial left!</span>';
                echo '<a href="'. $this->Html->url(array('plugin' => false, 'controller' => 'subscriptions', 'action' => 'presignin')) .'" class="btn paypal-btn">Subscribe To Delivrd</a>';
            } ?>
			<ul class="nav navbar-nav pull-right">


				<!-- BEGIN USER LOGIN DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-user">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">

					<span class="username username-hide-on-mobile">
					Hello, <?php echo $this->App->username($authUser); ?></span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a href="<?php echo Router::url(array('plugin' => 'users','controller' => 'users', 'action' => 'editmy'), true); ?>">
							<i class="icon-user"></i> My Profile </a>
						</li>
						<li>
							<a href="<?php echo Router::url(array('plugin' => 'users','controller' => 'users', 'action' => 'edit'), true); ?>">
							<i class="icon-settings"></i> Settings </a>
						</li>

						<li class="divider">
						</li>
						<li>
						<?php echo $this->Html->link(__('<i class="icon-key"></i> Log Out'), array('plugin' => 'users', 'controller'=> 'users','action' => 'logout'),array('escape'=> false)); ?>
						</li>
					</ul>
				</li>
				<!-- END USER LOGIN DROPDOWN -->
				<!-- BEGIN QUICK SIDEBAR TOGGLER -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-quick-sidebar-toggler">
					<a href="/users/logout" class="dropdown-toggle">
					<i class="icon-logout"></i>
					</a>
				</li>
				<!-- END QUICK SIDEBAR TOGGLER -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
		<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
			<!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
			<!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
			<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
			<!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
			<!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
			<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
				<!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
				<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler">
					</div>
					<!-- END SIDEBAR TOGGLER BUTTON -->
				</li>
				<!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
				<li class="sidebar-search-wrapper">
					<!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
					<!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
					<!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
					<?php echo $this->Form->create('search', array('url' => array('controller' => 'Dash', 'action' => 'search'), 'class' => 'sidebar-search', 'id' => 'DashIndexForm', 'type' => 'get', 'ccept-charset' => 'utf-8', '_lpchecked' => 1)); ?>
						<a href="javascript:;" class="remove">
						<i class="icon-close"></i>
						</a>
						<div class="input-group">
							<input type="text" name="q" id="q" class="form-control" placeholder="Search...">
							<span class="input-group-btn">
							<a class="btn submit"><i class="icon-magnifier"></i></a>
							</span>
						</div>
					</form>
					<!-- END RESPONSIVE QUICK SEARCH FORM -->
				</li>

				<?php
		            $activeClass = ($controller == 'Dash') ? "active" : "";
		            $openClass = ($controller == 'Dash') ? "open" : "";
		            $selectedClass = ($controller == 'Dash') ? "selected" : "";
		            $title = '<i class="icon-home"></i><span class="title">Dashboard</span><span class="' . $selectedClass . '"></span>';
		        ?>
				<li class="<?php echo $activeClass . ' ' . $openClass; ?>">
				  <?php echo $this->Html->link(__($title), array('plugin' => false, 'controller'=> 'Dash','action' => 'ofindex'),array('escape'=> false)); ?>

				</li>
			    <?php if($this->Session->read('paid') == 1) {
		            $activeClass = ($controller == 'Products' || $controller == 'Inventories' || $controller == 'Serials' || ($controller == 'orders_lines' && $action == 'transactions_history')) ? "active" : "";
		            $openClass = ($controller == 'Products' || $controller == 'Inventories' || $controller == 'Serials' || $controller == 'settings' || ($controller == 'orders_lines' && $action == 'transactions_history')) ? "open" : "";
		            $selectedClass = ($controller == 'Products' || $controller == 'Inventories' || $controller == 'Serials'  || $controller == 'settings' || ($controller == 'orders_lines' && $action == 'transactions_history')) ? "selected" : "";
		        ?>
				<li class="<?php echo $activeClass . ' ' . $openClass; ?>">
					<?php
	                $title = '<i class="icon-briefcase"></i><span class="title">Products & Inventory</span><span class="' . $selectedClass . '"></span><span class="arrow ' . $openClass . '"></span>';
	                echo $this->Html->link($title, array(), array('escape' => false));
	                ?>
					<ul class="sub-menu">
              		<?php } ?>
						<li class="<?php echo ($controller == 'Products') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-paper-clip"></i>
							Products'), array('plugin' => false, 'controller'=> 'Products','action' => 'index'),array('escape'=> false)); ?>
						</li>
						<li class="<?php echo ($controller == 'Inventories' || ($controller == 'inventories' && $action == 'transactions_history')) ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-pointer"></i>
							Inventory List'), array('plugin' => false,'controller'=> 'Inventories','action' => 'index'),array('escape'=> false)); ?>
						</li>
						<li class="<?php echo ($controller == 'Serials') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-grid"></i>
							Serial Numbers'), array('plugin' => false, 'controller'=> 'Serials','action' => 'index'),array('escape'=> false)); ?>
						</li>
                        <?php if($this->Session->read('paid') == 1) { ?>
					</ul>
                        <?php } ?>
				</li>
				<?php if($this->Session->read('paid') == 1) { 
			  		$activeClass = ($controller == 'suppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "active" : "";
		            $openClass = ($controller == 'suppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "open" : "";
		            $selectedClass = ($controller == 'suppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "selected" : "";
			    ?>
				<li class="<?php echo $activeClass . ' ' . $openClass; ?>">
						<?php
			                $title = '<i class="icon-share"></i><span class="title">Partners</span><span class="' . $selectedClass . '"></span><span class="arrow ' . $openClass . '"></span>';
			                echo $this->Html->link($title, array(), array('escape' => false));
		            	?>
					<ul class="sub-menu">
						<li class="<?php echo ($controller == 'suppliers') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-puzzle"></i>
							Suppliers'), array('plugin' => false, 'controller'=> 'suppliers','action' => 'index'),array('escape'=> false)); ?>
						</li>
						<li class="<?php echo ($controller == 'resources') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-puzzle"></i>
							Resources'), array('plugin' => false,'controller'=> 'resources','action' => 'index'),array('escape'=> false)); ?>
						</li>
						<li class="<?php echo ($controller == 'bins') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-feed"></i>
							Bins'), array('plugin' => false,'controller'=> 'bins','action' => 'index'),array('escape'=> false)); ?>
						</li>
						<li class="<?php echo ($controller == 'schannels') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-equalizer"></i>
							Sales Channel'), array('plugin' => false,'controller'=> 'schannels','action' => 'index'),array('escape'=> false)); ?>
						</li> 
                        <?php if($this->Session->read('is_admin') == 3 || $this->Session->read('is_admin') == 1) { ?>
						<li class="<?php echo ($controller == 'couriers') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-envelope"></i>
							Couriers'), array('plugin' => false,'controller'=> 'couriers','action' => 'index'),array('escape'=> false)); ?>
						</li>
                       	<?php } ?>
					</ul>
				</li>
				<?php 
					$activeClass = (($controller == 'orders' && $index == 2) || ($controller == 'orders' && $action == 'viewrord') || ($controller == 'orders' && $action == 'editrord') || ($controller == 'orders' && $action == 'addrord') || ($controller == 'shipments' && $index == 2)) ? "active" : "";
		            $openClass = (($controller == 'orders' && $index == 2) || ($controller == 'orders' && $action == 'viewrord') || ($controller == 'orders' && $action == 'editrord') || ($controller == 'orders' && $action == 'addrord') || ($controller == 'shipments' && $index == 2)) ? "open" : "";
		            $selectedClass = (($controller == 'orders' && $index == 2) || ($controller == 'orders' && $action == 'viewrord') || ($controller == 'orders' && $action == 'editrord') || ($controller == 'orders' && $action == 'addrord') || ($controller == 'shipments' && $index == 2)) ? "selected" : "";
			    ?>
				<li class="<?php echo $activeClass . ' ' . $openClass; ?>">
					<?php
		                $title = '<i class="icon-size-actual"></i><span class="title">Inbound Processing</span><span class="' . $selectedClass . '"></span><span class="arrow ' . $openClass . '"></span>';
		                echo $this->Html->link($title, array(), array('escape' => false));
		            ?>
					<ul class="sub-menu">
						<li class="<?php echo ($controller == 'orders') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-shuffle"></i>
							Purchase Orders'), array('plugin' => false,'controller'=> 'orders','action' => 'index', 'index' => 2),array('escape'=> false)); ?>
						</li>
                        <?php if($this->Session->read('is_admin') == 3 || $this->Session->read('is_admin') == 1) { ?>
						<li class="<?php echo ($controller == 'shipments') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-plane"></i>
							Inbound Shipments'), array('plugin' => false,'controller'=> 'shipments','action' => 'index', 'index' => 2),array('escape'=> false)); ?>
						</li>
                        <?php } ?>
					</ul>
				</li>
				<?php 
					$activeClass = (($controller == 'orders' && $index == 1) || ($controller == 'orders' && $action == 'viewcord') || ($controller == 'orders' && $action == 'editcord') || ($controller == 'orders' && $action == 'addcord') || ($controller == 'shipments' && $index == 1) || $controller == 'waves') ? "active" : "";
		            $openClass = (($controller == 'orders' && $index == 1) || ($controller == 'orders' && $action == 'viewcord') || ($controller == 'orders' && $action == 'editcord') || ($controller == 'orders' && $action == 'addcord') || ($controller == 'shipments' && $index == 1) || $controller == 'waves') ? "open" : "";
		            $selectedClass = (($controller == 'orders' && $index == 1) || ($controller == 'orders' && $action == 'viewcord') || ($controller == 'orders' && $action == 'editcord') || ($controller == 'orders' && $action == 'addcord') || ($controller == 'shipments' && $index == 1) || $controller == 'waves') ? "selected" : "";
			    ?>
				<li class="<?php echo $activeClass . ' ' . $openClass; ?>">
					<?php
		                $title = '<i class="icon-size-fullscreen"></i><span class="title">Outbound Processing</span><span class="' . $selectedClass . '"></span><span class="arrow ' . $openClass . '"></span>';
		                echo $this->Html->link($title, array(), array('escape' => false));
		            ?>
					<ul class="sub-menu">
						<li class="<?php echo ($controller == 'orders') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-basket-loaded"></i> ' .(!empty($this->Session->read('sales_title')) ? ucwords($this->Session->read('sales_title')) : 'Sales Order')), array('plugin' => false,'controller'=> 'orders','action' => 'index','index' => 1),array('escape'=> false)); ?>
						</li>
                        <?php if($this->Session->read('is_admin') == 3 || $this->Session->read('is_admin') == 1) { ?>
						<li class="<?php echo ($controller == 'waves') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-control-play"></i>
							Waves'), array('plugin' => false,'controller'=> 'waves','action' => 'index'),array('escape'=> false)); ?>
						</li>
						<li class="<?php echo ($controller == 'shipments') ? 'active' : '';?>">
						<?php echo $this->Html->link(__('<i class="icon-rocket"></i> Outbound Shipments'), array('plugin' => false,'controller'=> 'shipments','action' => 'index','index' => 1),array('escape'=> false)); ?>
						</li>
	                    <?php } ?>
					</ul>
				</li>
				<?php } ?>
				<?php
		            $activeClass = ($controller == 'users' || $action == 'edit') ? "active" : "";
		            $openClass = ($controller == 'users' || $action == 'edit') ? "open" : "";
		            $selectedClass = ($controller == 'users' || $action == 'edit') ? "selected" : "";
		            $title = '<i class="icon-settings"></i><span class="title">Settings</span><span class="' . $selectedClass . '"></span>';
		        ?>
				<li class="<?php echo $activeClass . ' ' . $openClass; ?>">
				  <?php echo $this->Html->link(__($title), array('plugin' => 'users', 'controller'=> 'users','action' => 'edit'),array('escape'=> false)); ?>
				</li>
				<?php
		            $activeClass = ($controller == 'integrations' || $action == 'index') ? "active" : "";
		            $openClass = ($controller == 'integrations' || $action == 'index') ? "open" : "";
		            $selectedClass = ($controller == 'integrations' || $action == 'index') ? "selected" : "";
		            $title = '<i class="icon-puzzle"></i><span class="title">Integrations</span><span class="' . $selectedClass . '"></span>';
		        ?>
				<li class="<?php echo $activeClass . ' ' . $openClass; ?>">
				  <?php echo $this->Html->link(__($title), array('plugin' => false, 'controller'=> 'integrations','action' => 'index'),array('escape'=> false)); ?>
				</li>
				</li>
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	<!-- END SIDEBAR -->


			<?php echo $content_for_layout; ?>


	<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 2016 &copy; Delivrd.
	</div>
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
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-migrate.min.js'); ?>

<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<?php //echo $this->Html->script('/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap/js/bootstrap.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.blockui.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.cokie.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/uniform/jquery.uniform.min.js'); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js'); ?>



<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
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
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js'); ?>
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

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<?php  echo $this->Html->script('/assets/global/scripts/metronic.js'); ?>
<?php  echo $this->Html->script('/assets/admin/layout/scripts/layout.js'); ?>
<?php  echo $this->Html->script('/assets/admin/layout/scripts/demo.js'); ?>
<?php  echo $this->Html->script('/assets/global/scripts/datatable.js'); ?>
<?php  echo $this->Html->script('/assets/admin/pages/scripts/charts-flotcharts.js'); ?>
<?php  echo $this->Html->script('/local/index.js'); ?>
<?php  echo $this->Html->script('/local/acharts.js'); ?>
<?php  echo $this->Html->script('/local/dateinit.js'); ?>
<?php  echo $this->Html->script('/assets/admin/pages/scripts/tasks.js'); ?>

<!-- END PAGE LEVEL SCRIPTS -->

<script>
	jQuery(document).ready(function() {
	   Metronic.init(); // init metronic core componets
	   Layout.init(); // init layout
	   Index.init();
	   acharts.init();
	   DateInit.init();
	});
	
	$(document).ready(function() {
		$.fn.editable.defaults.mode = 'inline';
    	$('.remarks-editable').editable();
	});
</script>
<script>

$("#warehouse_select").change(function(){
	var txUrl = $( '#orderline-search' ).attr( 'action' );
	window.location =  txUrl + '?location=' + $(this).val();
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

</body>
<!-- END BODY -->
</html>
