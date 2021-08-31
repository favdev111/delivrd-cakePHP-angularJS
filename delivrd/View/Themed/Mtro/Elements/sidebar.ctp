<div class="page-sidebar-wrapper page-sidebar-fixed">
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
                <?php echo $this->Form->create('search', array('url' => array('plugin' => false, 'controller' => 'Dash', 'action' => 'search'), 'class' => 'sidebar-search', 'id' => 'DashIndexForm', 'type' => 'get', 'ccept-charset' => 'utf-8', '_lpchecked' => 1)); ?>
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

            <?php if($this->Session->read('paid') == 1 || array_key_exists('S.O.', $_access) || array_key_exists('P.O.', $_access)) { ?>
            <?php
                $activeClass = (strcasecmp($controller, 'Products') == 0 || strcasecmp($controller, 'Inventories') == 0 || strcasecmp($controller, 'Serials') == 0 || strcasecmp($controller, 'Warehouses') == 0) ? "active" : "";
                $openClass = ($activeClass || strcasecmp($controller, 'settings') == 0) ? "open" : "";
                $selectedClass = ($openClass) ? "selected" : "";
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                <?php
                    $title = '<i class="icon-briefcase"></i><span class="title">Products & Inventory</span><span class="' . $selectedClass . '"></span><span class="arrow ' . $openClass . '"></span>';
                    echo $this->Html->link($title, array(), array('escape' => false));
                ?>
                <ul class="sub-menu">
            <?php } ?>

            <li class="<?php echo (strcasecmp($controller, 'products')==0) ? 'active' : '';?>">
                <?php echo $this->Html->link(__('<i class="icon-paper-clip"></i>
                    <span class="title">Products</span>'), array('plugin' => false, 'controller'=> 'products','action' => 'index'),array('escape'=> false, 'class'=>'statlink', 'data-link' => 'products')); ?>
            </li>
            <li class="<?php echo (strcasecmp($controller, 'Inventories')==0) ? 'active' : '';?>">
                <?php echo $this->Html->link(__('<i class="icon-pointer"></i>
                    <span class="title">Inventory List</span>'), array('plugin' => false,'controller'=> 'Inventories','action' => 'index'),array('escape'=> false, 'class'=>'statlink', 'data-link' => 'inventories')); ?>
            </li>
            <?php if(!$this->Session->read('Auth.User.is_limited') || array_key_exists('Serials', $_access)) { ?>
            <li class="<?php echo (strcasecmp($controller, 'Serials')==0) ? 'active' : '';?>">
                <?php echo $this->Html->link(__('<i class="icon-grid"></i>
                    <span class="title">Serial Numbers</span>'), array('plugin' => false, 'controller'=> 'serials','action' => 'index'),array('escape'=> false, 'class'=>'statlink', 'data-link' => 'serials')); ?>
            </li>
            <?php } ?>
            <?php if($this->Session->read('paid') == 1 || array_key_exists('S.O.', $_access) || array_key_exists('P.O.', $_access)) { ?>
                </ul>
            </li>
            <?php } ?>

            <?php if($this->Session->read('paid') == 1) { // Partners
                $activeClass = ($controller == 'suppliers' || $controller == 'productsuppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "active" : "";
                $openClass = ($controller == 'suppliers' || $controller == 'productsuppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "open" : "";
                $selectedClass = ($controller == 'suppliers' || $controller == 'productsuppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "selected" : "";
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                <?php
                    $title = '<i class="icon-share"></i><span class="title">Partners</span><span class="' . $selectedClass . '"></span><span class="arrow ' . $openClass . '"></span>';
                    echo $this->Html->link($title, array(), array('escape' => false));
                 ?>
                <ul class="sub-menu">
                    <li class="<?php echo ($controller == 'suppliers' || $controller == 'productsuppliers') ? 'active' : '';?>">
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
            <?php } ?>

            <?php if($this->Session->read('paid') == 1 || array_key_exists('P.O.', $_access)) { // Inbond ?>
            <?php
                $activeClass = (($controller == 'ReplOrders') || ($controller == 'shipments' && $index == 2)) ? "active" : "";
                $openClass = (($controller == 'ReplOrders') || ($controller == 'shipments' && $index == 2)) ? "open" : "";
                $selectedClass = (($controller == 'ReplOrders') || ($controller == 'shipments' && $index == 2)) ? "selected" : "";
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                <?php
                    $title = '<i class="icon-size-actual"></i><span class="title">Inbound Processing</span><span class="' . $selectedClass . '"></span><span class="arrow ' . $openClass . '"></span>';
                    echo $this->Html->link($title, array(), array('escape' => false));
                ?>
                <ul class="sub-menu">
                    <li class="<?php echo (strcasecmp($controller, 'ReplOrders') == 0) ? 'active' : '';?>">
                    <?php echo $this->Html->link(__('<i class="icon-shuffle"></i>
                        Purchase Orders'), array('plugin' => false,'controller'=> 'replorders','action' => 'index'),array('escape'=> false, 'class'=>'statlink', 'data-link' => 'po')); ?>
                    </li>
                    <?php if($this->Session->read('is_admin') == 3 || $this->Session->read('is_admin') == 1) { ?>
                    <li class="<?php echo (strcasecmp($controller, 'shipments') == 0) ? 'active' : '';?>">
                    <?php echo $this->Html->link(__('<i class="icon-plane"></i>
                        Inbound Shipments'), array('plugin' => false,'controller'=> 'shipments','action' => 'index', 'index' => 2),array('escape'=> false, 'class'=>'statlink', 'data-link' => 'po_shipment')); ?>
                    </li>
                    <?php } ?>
                </ul>
            </li>
            <?php } ?>

            <?php if($this->Session->read('paid') == 1 || array_key_exists('S.O.', $_access)) { // Outbound ?>
            <?php
                $activeClass = (($controller == 'SalesOrders') || ($controller == 'shipments' && $index == 1) || $controller == 'waves') ? "active" : "";
                $openClass = (($controller == 'SalesOrders') || ($controller == 'shipments' && $index == 1) || $controller == 'waves') ? "open" : "";
                $selectedClass = (($controller == 'SalesOrders') || ($controller == 'shipments' && $index == 1) || $controller == 'waves') ? "selected" : "";
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                <?php
                    $title = '<i class="icon-size-fullscreen"></i><span class="title">Outbound Processing</span><span class="' . $selectedClass . '"></span><span class="arrow ' . $openClass . '"></span>';
                    echo $this->Html->link($title, array(), array('escape' => false));
                ?>
                <ul class="sub-menu">
                    <li class="<?php echo (strcasecmp($controller, 'SalesOrders') == 0) ? 'active' : '';?>">
                    <?php echo $this->Html->link(__('<i class="icon-basket-loaded"></i>
                        Sales Orders'), array('plugin' => false,'controller'=> 'salesorders','action' => 'index'),array('escape'=> false, 'target' => '_self', 'class'=>'statlink', 'data-link' => 'so')); ?>
                    </li>
                    <?php if($this->Session->read('is_admin') == 3 || $this->Session->read('is_admin') == 1) { ?>
                    <li class="<?php echo (strcasecmp($controller, 'waves') == 0) ? 'active' : '';?>">
                    <?php echo $this->Html->link(__('<i class="icon-control-play"></i>
                        Waves'), array('plugin' => false,'controller'=> 'waves','action' => 'index'),array('escape'=> false)); ?>
                    </li>
                    <li class="<?php echo (strcasecmp($controller, 'shipments') == 0) ? 'active' : '';?>">
                        <?php echo $this->Html->link(__('<i class="icon-rocket"></i> Outbound Shipments'), array('plugin' => false,'controller'=> 'shipments','action' => 'index','index' => 1),
                        array('escape'=> false, 'class'=>'statlink', 'data-link' => 'so_shipment')); ?>
                    </li>
                    <?php } ?>
                </ul>
            </li>
            <?php } ?>

            <?php if($this->Session->read('is_admin') == 1 && $this->Session->read('paid') == 1) { ?>
            <?php
                $activeClass = (($controller == 'users' && $action == 'index') || ($controller == 'shipments' && $action == 'indexsh')) ? "active" : "";
                $openClass = (($controller == 'users' && $action == 'index') || ($controller == 'shipments' && $action == 'indexsh')) ? "open" : "";
                $selectedClass = (($controller == 'users' && $action == 'index') || ($controller == 'shipments' && $action == 'indexsh')) ? "selected" : "";
             ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                <?php
                    $title = '<i class="icon-settings"></i><span class="title">Shipping Admin</span><span class="' . $selectedClass . '"></span><span class="arrow ' . $openClass . '"></span>';
                    echo $this->Html->link($title, array(), array('escape' => false));
                ?>
                <ul class="sub-menu">
                    <li class="<?php echo ($controller == 'shipments') ? 'active' : '';?>">
                        <?php echo $this->Html->link(__('<i class="icon-feed"></i>
                        Shipments Monitor'), array('plugin'=>false, 'controller'=> 'shipments','action' => 'indexsh'),array('escape'=> false)); ?>
                    </li>
                    <li class="<?php echo ($controller == 'users') ? 'active' : '';?>">
                    <?php echo $this->Html->link(__('<i class="icon-user"></i>
                        Partners Monitor'), array('plugin' => false,'controller'=> 'users','action' => 'index'),array('escape'=> false)); ?>
                    </li>
                </ul>
            </li>
            <?php } ?>

            <?php if(isset($sidebar_ad)) { ?>
                <?php if(sizeof($sidebar_ad) > 0) { ?>
                <li>
                    <a href="javascript:;">
                        <i class="icon-basket"></i>
                        <span class="title">Recommended Supplies</span>
                        <span class="arrow open"></span>
                    </a>
                    <div style="text-align:center;">
                        <?php echo $sidebar_ad['url']; ?>
                    </div>
                </li>
                <?php } ?>
            <?php } ?>

            <?php
                $activeClass = ($controller == 'users' && $action == 'edit') ? "active" : "";
                $openClass = ($controller == 'users' && $action == 'edit') ? "open" : "";
                $selectedClass = ($controller == 'users' && $action == 'edit') ? "selected" : "";
                $title = '<i class="icon-settings"></i><span class="title">Settings</span><span class="' . $selectedClass . '"></span>';
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
              <?php echo $this->Html->link(__($title), array('plugin' => 'users', 'controller'=> 'users','action' => 'edit'),array('escape'=> false)); ?>
            </li>
            <?php if(!$authUser['is_limited'] && $this->Session->read('paid') == 1) { ?>
            <?php
                $activeClass = ($controller == 'integrations') ? "active" : "";
                $openClass = ($controller == 'integrations') ? "open" : "";
                $selectedClass = ($controller == 'integrations') ? "selected" : "";
                $title = '<i class="icon-puzzle"></i><span class="title">Integrations</span><span class="' . $selectedClass . '"></span>';
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
              <?php echo $this->Html->link(__($title), array('plugin' => false, 'controller'=> 'integrations','action' => 'index'),array('escape'=> false)); ?>
            </li>
            <?php } ?>


            <?php if(in_array($authUser['email'], ['fordenis@ukr.net', 'technoyos@gmail.com'])) { ?>
            <?php
                $activeClass = ($controller == 'reports') ? "active" : "";
                $openClass = ($controller == 'reports') ? "open" : "";
                $selectedClass = ($controller == 'reports') ? "selected" : "";
                $title = '<i class="icon-settings"></i><span class="title">System Inconsistency</span><span class="' . $selectedClass . '"></span>';
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
              <?php echo $this->Html->link(__($title), array('plugin' => false, 'controller' => 'reports', 'action' => 'index'),array('escape'=> false)); ?>
            </li>
            <?php } ?>

            <?php if(in_array($authUser['email'], ['fordenis@ukr.net', 'technoyos@gmail.com'])) { ?>
            <?php
                $activeClass = ($controller == 'subscriptions') ? "active" : "";
                $openClass = ($controller == 'subscriptions') ? "open" : "";
                $selectedClass = ($controller == 'subscriptions') ? "selected" : "";
                $title = '<i class="fa fa-paypal" aria-hidden="true"></i> <span class="title">Subscriptions</span><span class="' . $selectedClass . '"></span>';
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
              <?php echo $this->Html->link(__($title), array('plugin' => false, 'controller' => 'subscriptions', 'action' => 'index'),array('escape'=> false)); ?>
            </li>
            <?php } ?>
        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
</div>