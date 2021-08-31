<style>
    .dropdown-network > a {color:#FFFFFF !important;font-size:13px;line-height:20px;padding: 15px 6px 11px 8px !important;font-weight:400 !important;}
    .dropdown-user > a > span {color:#FFFFFF !important;font-weight:400 !important;}
    .dropdown-quick-sidebar-toggler > a > i {color:#FFFFFF !important;font-weight:400 !important;}
</style>
<div class="page-header md-shadow-z-1-i navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="/">
                <img src="<?php echo Configure::read('InAppLogoURL') ?>" alt="logo" class="logo-default"/>
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
            <?php
                if($_authUser['User']['role'] == 'trial') {
                    $remaining_days  = $this->App->getRemainingDays($_authUser['Subscription']['expiry_date']);
                    echo '<span class="username username-hide-on-mobile subscribe-btn">' .$remaining_days .' days of free trial left!</span>';
                    /*if(Configure::read('OperatorName') == 'Delivrd' && $this->request->host() == 'delivrdapp.com') {
                        echo '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WEYVWFC35BA84" taraget="" class="btn paypal-btn">Subscribe To Delivrd</a>';
                    } else {
                        echo '<a href="https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K7XAYPUDS93JJ" taraget="" class="btn paypal-btn">Subscribe To Delivrd</a>';
                    }*/
                    echo '<a href="'. $this->Html->url(array('plugin' => false, 'controller' => 'subscriptions', 'action' => 'presignin')) .'" class="btn paypal-btn">Subscribe To Delivrd</a>';
                }
            ?>
            <ul class="nav navbar-nav pull-right">

                <!-- BEGIN USER LOGIN DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <?php if($authUser['is_limited'] || $authUser['paid'] != 1) { ?>
                <li class="dropdown dropdown-network">
                    <?php echo $this->Html->link('<i class="fa fa-sitemap"></i> Networks', array('plugin' => 'networks', 'controller' => 'networks', 'action' => 'lists'), ['escape'=>false, 'class' => 'dropdown-toggle']); ?>
                </li>
                <?php } else { ?>
                <li class="dropdown dropdown-network">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <span class="username username-hide-on-mobile">
                            <i class="fa fa-sitemap"></i> Networks
                            <i class="fa fa-angle-down"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li><?php echo $this->Html->link('My Network', array('plugin' => 'networks', 'controller' => 'networks', 'action' => 'index')); ?></li>
                        <li><?php echo $this->Html->link('Partner Networks', array('plugin' => 'networks', 'controller' => 'networks', 'action' => 'lists')); ?></li>
                    </ul>
                </li>
                <?php } ?>

                <li class="dropdown dropdown-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">

                    <span class="username username-hide-on-mobile">
                        Hello, <?php echo $this->App->username($authUser); ?>
                    </span>
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li>
                            <a href="<?php echo Router::url(array('plugin' => 'users','controller' => 'users', 'action' => 'editmy'), true); ?>">
                            <i class="icon-user"></i> My Profile </a>
                        </li>
                        <li>
                            <?php echo $this->Html->link(__('<i class="fa fa-retweet"></i> Reset Password'), array('plugin' => false, 'controller'=> 'reset-password'),array('escape'=> false)); ?>
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