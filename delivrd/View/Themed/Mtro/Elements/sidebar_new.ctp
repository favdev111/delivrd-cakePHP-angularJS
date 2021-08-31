<div class="page-sidebar-wrapper page-sidebar-fixed">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <li class="sidebar-toggler-wrapper" style="margin-bottom:8px;">
                <div class="sidebar-toggler"></div>
                <a href="#" class="bg-grey-silver short_side_icons search_ico" id="showMainSearch1"><i class="icon-magnifier"></i></a>
                <?php echo $this->Html->link(__('<i class="icon-home"></i>'),
                    array('plugin' => false, 'controller'=> 'Dash','action' => 'ofindex'),
                    array('escape'=> false, 'class' => 'bg-grey-silver short_side_icons'));
                ?>
            </li>

            <li class="sidebar-search-wrapper" id="searchWrapper1" style="display: none;">
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
            </li>

            <?php
                $activeClass = (strcasecmp($controller, 'Products') == 0) ? "active" : "";
                $openClass = (strcasecmp($controller, 'Products') == 0) ? "open" : "";
                $selectedClass = ($openClass) ? "selected" : "";
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                <?php echo $this->Html->link(__('<i class="icon-paper-clip"></i>
                    <span class="title">Products</span>'), array('plugin' => false, 'controller'=> 'products','action' => 'index'),array('escape'=> false, 'class'=>'statlink', 'data-link' => 'products')); ?>
            </li>

            <?php #if(array_key_exists('S.O.', $_access) || array_key_exists('P.O.', $_access)) { ?>
                <?php
                    $activeClass = (strcasecmp($controller, 'Inventories') == 0 || strcasecmp($controller, 'Warehouses') == 0 || strcasecmp($controller, 'invalerts') == 0) ? "active" : "";
                    $openClass = (strcasecmp($controller, 'Inventories') == 0 || strcasecmp($controller, 'Warehouses') == 0 || strcasecmp($controller, 'invalerts') == 0) ? "open" : "";
                    $selectedClass = ($openClass) ? "selected" : "";
                ?>
                <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                    <?php echo $this->Html->link(__('<i class="icon-pointer"></i>
                        <span class="title">Inventory</span>'), array('plugin' => false,'controller'=> 'Inventories','action' => 'index'),array('escape'=> false, 'class'=>'statlink', 'data-link' => 'inventories')); ?>
                </li>

                <?php if(!$_authUser['User']['is_limited'] || array_key_exists('Serials', $_access)) { ?>
                    <?php
                        $activeClass = (strcasecmp($controller, 'Serials') == 0) ? "active" : "";
                        $openClass = (strcasecmp($controller, 'Serials') == 0) ? "open" : "";
                        $selectedClass = ($openClass) ? "selected" : "";
                    ?>
                    <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                        <?php echo $this->Html->link(__('<i class="icon-grid"></i>
                            <span class="title">Serial Numbers</span>'), array('plugin' => false, 'controller'=> 'serials','action' => 'index'),array('escape'=> false, 'class'=>'statlink', 'data-link' => 'serials')); ?>
                    </li>
                <?php } ?>
            <?php #} ?>

            <?php if($_authUser['User']['paid'] == 1 || array_key_exists('P.O.', $_access)) { // Inbond ?>
            <?php
                $activeClass = (strcasecmp($controller, 'ReplOrders') == 0) ? "active" : "";
                $openClass = (strcasecmp($controller, 'ReplOrders') == 0) ? "open" : "";
                $selectedClass = ($openClass) ? "ReplOrders" : "";
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                <?php echo $this->Html->link(__('<i class="icon-shuffle"></i> <span class="title">Purchase Orders</span>'), array(
                    'plugin' => false,'controller'=> 'replorders','action' => 'index'),array('escape'=> false, 'class'=>'statlink', 'data-link' => 'po'
                )); ?>
            </li>
            <?php } ?>

            <?php if($_authUser['User']['paid'] == 1 || array_key_exists('S.O.', $_access)) { // Sales Orders ?>
            <?php
                $activeClass = (strcasecmp($controller, 'SalesOrders') == 0) ? "active" : "";
                $openClass = (strcasecmp($controller, 'SalesOrders') == 0) ? "open" : "";
                $selectedClass = ($openClass) ? "SalesOrders" : "";
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                <?php echo $this->Html->link(__('<i class="icon-basket-loaded"></i> <span class="title">Sales Orders</span>'),
                    array('plugin' => false,'controller'=> 'salesorders','action' => 'index'),
                    array('escape'=> false, 'target' => '_self', 'class'=>'statlink', 'data-link' => 'so')
                ); ?>
            </li>
            <?php } ?>


            <?php
                $activeClass = (strcasecmp($controller, 'integrations') == 0 || $controller == 'waves' || $controller == 'shipments' || $controller == 'suppliers' || $controller == 'productsuppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "active" : "";
                $openClass = (strcasecmp($controller, 'integrations') == 0 || $controller == 'waves' || $controller == 'shipments' || $controller == 'suppliers' || $controller == 'productsuppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "open" : "";
                $selectedClass = (strcasecmp($controller, 'integrations') == 0 || $controller == 'waves' || $controller == 'shipments' || $controller == 'suppliers' || $controller == 'productsuppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "selected" : "";
            ?>
            <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                <?php
                    #$title = '<i class="icon-share"></i><span class="title">More...</span><span class="' . $selectedClass . '"></span><span class="arrow ' . $openClass . '"></span>';
                    #echo $this->Html->link($title, array(), array('escape' => false));
                ?>
                <?php if($_authUser['User']['role'] == 'paid' || $_authUser['User']['role'] == 'trial') { ?>
                <a href >
                    <i class="icon-share"></i><span class="title">More...</span><span class="<?php echo $selectedClass; ?>"></span>
                    <span class="arrow <?php echo $openClass; ?>"></span>
                </a>

                <ul class="sub-menu">
                    <?php if($_authUser['User']['paid'] == 1) { //Partners ?>
                    <?php
                        $activeClass = ($controller == 'suppliers' || $controller == 'productsuppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "active" : "";
                        $openClass = ($controller == 'suppliers' || $controller == 'productsuppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "open" : "";
                        $selectedClass = ($controller == 'suppliers' || $controller == 'productsuppliers' || $controller == 'resources' || $controller == 'bins' || $controller == 'schannels' || $controller == 'couriers') ? "selected" : "";
                    ?>
                    <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                            <?php
                                $title = '<i class="icon-share"></i> <span class="title"> Partners</span><span class="' . $selectedClass . '"></span><span class="arrow ' . $openClass . '"></span>';
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
                            <?php if($_authUser['User']['is_admin'] == 3 || $_authUser['User']['is_admin'] == 1) { ?>
                                <li class="<?php echo ($controller == 'couriers') ? 'active' : '';?>">
                                <?php echo $this->Html->link(__('<i class="icon-envelope"></i>
                                    Couriers'), array('plugin' => false,'controller'=> 'couriers','action' => 'index'),array('escape'=> false)); ?>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>

                    <?php if($_authUser['User']['paid'] == 1 || array_key_exists('P.O.', $_access)) { // Inbond ?>
                        <?php if($_authUser['User']['is_admin'] == 3 || $_authUser['User']['is_admin'] == 1) { ?>
                            <li class="<?php echo (strcasecmp($controller, 'shipments') == 0 && $index == 2) ? 'active' : '';?>">
                            <?php echo $this->Html->link(__('<i class="icon-plane"></i>
                                Inbound Shipments'), array('plugin' => false,'controller'=> 'shipments','action' => 'index', 'index' => 2),array('escape'=> false, 'class'=>'statlink', 'data-link' => 'po_shipment')); ?>
                            </li>
                        <?php } ?>
                    <?php } ?>

                    <?php if($_authUser['User']['paid'] == 1 || array_key_exists('S.O.', $_access)) { // Outbound ?>
                        <?php if($_authUser['User']['is_admin'] == 3 || $_authUser['User']['is_admin'] == 1) { ?>
                            <li class="<?php echo (strcasecmp($controller, 'shipments') == 0 && $index == 1) ? 'active' : '';?>">
                                <?php echo $this->Html->link(__('<i class="icon-rocket"></i> <span class="title">Outbound Shipments</span>'), array('plugin' => false,'controller'=> 'shipments','action' => 'index','index' => 1),
                                array('escape'=> false, 'class'=>'statlink', 'data-link' => 'so_shipment')); ?>
                            </li>
                            <li class="<?php echo (strcasecmp($controller, 'waves') == 0) ? 'active' : '';?>">
                            <?php echo $this->Html->link(__('<i class="icon-control-play"></i>
                                Waves'), array('plugin' => false,'controller'=> 'waves','action' => 'index'),array('escape'=> false)); ?>
                            </li>
                        <?php } ?>
                    <?php } ?>

                    <?php if($_authUser['User']['is_admin'] == 1 && $_authUser['User']['paid'] == 1) { //Shipping Admin 
                        $activeClass = (($controller == 'users' && $action == 'index') || ($controller == 'shipments' && $action == 'index'  && ($index != 1 && $index != 2))) ? "active" : "";
                        $openClass = (($controller == 'users' && $action == 'index') || ($controller == 'shipments' && $action == 'index'  && ($index != 1 && $index != 2))) ? "open" : "";
                        $selectedClass = (($controller == 'users' && $action == 'index') || ($controller == 'shipments' && $action == 'index'  && ($index != 1 && $index != 2))) ? "selected" : "";
                        #echo $controller .' : '. $action;
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

                    <?php if(!$_authUser['User']['is_limited'] && $_authUser['User']['paid'] == 1) { //Integrations ?>
                        <li class="<?php echo (strcasecmp($controller, 'integrations') == 0) ? 'active' : '';?>">
                            <?php echo $this->Html->link(__('<i class="icon-puzzle"></i> Integrations'),
                                array('plugin' => false, 'controller'=> 'integrations','action' => 'index'),
                                array('escape'=> false)
                            ); ?>
                        </li>
                    <?php } ?>
                </ul>
                <?php } else { ?>
                <a href="<?php echo $this->Html->url(array('plugin' => false, 'controller'=>'user','action'=>'start_trial')); ?>" data-toggle="modal" data-target="#ajaxModal" id="startTrialHidden" >
                    <i class="icon-share"></i><span class="title">More...</span><span class="<?php echo $selectedClass; ?>"></span>
                </a>
                <?php } ?>
            </li>

            <?php
                $activeClass = (strcasecmp($controller, 'users') == 0 && $plugin != 'admin') ? "active" : "";
                $openClass = (strcasecmp($controller, 'users') == 0 && $plugin != 'admin') ? "open" : "";
                $selectedClass = (strcasecmp($controller, 'users') == 0 && $plugin != 'admin') ? "selected" : "";
            ?>
            <?php $title = '<i class="icon-settings"></i> <span class="title">Settings</span>'; ?>
            <li class="<?php echo $activeClass;?>">
                <?php echo $this->Html->link(__($title), array('plugin' => 'users', 'controller'=> 'users','action' => 'edit'),array('escape'=> false)); ?>
            </li>

            
            <?php if(in_array($_authUser['User']['email'], ['fordenis@ukr.net', 'technoyos@gmail.com'])) { //Only SU Admin Links ?>
                <?php
                    $activeClass = (strcasecmp($controller, 'reports') == 0 || strcasecmp($controller, 'subscriptions') == 0 || $plugin == 'admin') ? "active" : "";
                    $openClass = (strcasecmp($controller, 'reports') == 0 || strcasecmp($controller, 'subscriptions') == 0 || $plugin == 'admin') ? "open" : "";
                    $selectedClass = (strcasecmp($controller, 'reports') == 0 || strcasecmp($controller, 'subscriptions') == 0 || $plugin == 'admin') ? "selected" : "";
                ?>
                <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                    <a href >
                        <i class="icon-bar-chart"></i><span class="title">Admin</span><span class="<?php echo $selectedClass; ?>"></span>
                        <span class="arrow <?php echo $openClass; ?>"></span>
                    </a>
                    <ul class="sub-menu">

                        <?php
                            $activeClass = ($controller == 'reports') ? "active" : "";
                            $openClass = ($controller == 'reports') ? "open" : "";
                            $selectedClass = ($controller == 'reports') ? "selected" : "";
                            $title = '<i class="icon-settings"></i><span class="title">Dashboard</span><span class="' . $selectedClass . '"></span>'; //System Inconsistency
                        ?>
                        <li>
                            <?php echo $this->Html->link(__($title), array('plugin' => false, 'controller' => 'reports', 'action' => 'index'),array('escape'=> false)); ?>
                        </li>

                        <?php
                            $activeClass = ($controller == 'users') ? "active" : "";
                            $openClass = ($controller == 'users') ? "open" : "";
                            $selectedClass = ($controller == 'users') ? "selected" : "";
                            $title = '<i class="fa fa-users" aria-hidden="true"></i> <span class="title">Users</span><span class="' . $selectedClass . '"></span>';
                        ?>
                        <li class="<?php echo $activeClass .' '. $openClass; ?>">
                            <?php echo $this->Html->link(__($title), array('plugin' => 'admin', 'controller' => 'users', 'action' => 'index'), array('escape'=> false)); ?>
                        </li>

                        <?php
                            $activeClass = ($controller == 'subscriptions') ? "active" : "";
                            $openClass = ($controller == 'subscriptions') ? "open" : "";
                            $selectedClass = ($controller == 'subscriptions') ? "selected" : "";
                            $title = '<i class="fa fa-paypal" aria-hidden="true"></i> <span class="title">Subscriptions</span><span class="' . $selectedClass . '"></span>';
                        ?>
                        <li class="<?php echo $activeClass . ' ' . $openClass; ?>">
                            <?php echo $this->Html->link(__($title), array('plugin' => false, 'controller' => 'subscriptions', 'action' => 'index'),array('escape'=> false)); ?>
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
        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
</div>