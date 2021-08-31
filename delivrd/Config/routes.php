<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
	/**
	 * Here, we are connecting '/' (base path) to controller called 'Pages',
	 * its action called 'display', and we pass a param to select the view file
	 * to use (in this case, /app/View/Pages/home.ctp)...
	 */
	Router::connect('/plans', array( 'controller' => 'plans', 'action' => 'plan_details'));
	Router::connect('/', array('controller' => 'Dash', 'action' => 'ofindex'));
	/**
	 * ...and connect the rest of 'Pages' controller's URLs.
	 */
	Router::connect('/login', array('plugin' => 'users', 'controller' => 'users', 'action' => 'login'));
	Router::connect('/editmy', array('plugin' => 'users', 'controller' => 'users', 'action' => 'editmy'));
	Router::connect('/user-login', array('plugin' => 'users', 'controller' => 'users', 'action' => 'firstlogin'));
	Router::connect('/reset-password', array('plugin' => 'users', 'controller' => 'users', 'action' => 'reset_password'));
	Router::connect('/logout', array( 'controller' => 'users', 'action' => 'logout'));
	Router::connect('/signup/:slug', array( 'plugin' => 'users', 'controller' => 'users', 'action' => 'add'), array('pass' => array("slug")));
	Router::connect('/laststep/:slug', array( 'plugin' => 'users', 'controller' => 'users', 'action' => 'laststep'), array('pass' => array("slug")));
	Router::connect('/register', array( 'plugin' => 'users', 'controller' => 'users', 'action' => 'signup'));
	Router::connect('/firststep', array( 'plugin' => 'users', 'controller' => 'users', 'action' => 'signup_process'));
    Router::connect('/subscribe', array( 'controller' => 'users', 'action' => 'subscribe'));
    Router::connect('/user/add_activity', array( 'controller' => 'users', 'action' => 'add_activity'));
    Router::connect('/user/add_stats', array( 'controller' => 'users', 'action' => 'add_stats'));
    Router::connect('/user/start_trial', array( 'controller' => 'users', 'action' => 'start_trial'));
    Router::connect('/user/stop_trial', array( 'controller' => 'users', 'action' => 'stop_trial'));
	Router::connect('/locations', array( 'controller' => 'warehouses', 'action' => 'index'));
	Router::connect('/addlocation', array( 'controller' => 'warehouses', 'action' => 'add'));
	Router::connect('/editlocation', array( 'controller' => 'warehouses', 'action' => 'edit'));
	Router::connect('/listcorders/:product_id/:index', array( 'controller' => 'orders', 'action' => 'index'), array('pass' => array('index', 'product_id')));
	//Router::connect('/orders/:index', array( 'controller' => 'orders', 'action' => 'index'), array('pass' => array('index')));
	Router::connect('/orders/1', array( 'controller' => 'orders', 'action' => 'index', 1));
	Router::connect('/orders/2', array( 'controller' => 'orders', 'action' => 'index', 2));
	Router::connect('/plan', array( 'controller' => 'plans', 'action' => 'plan_details'));
	Router::connect('/plan_view/*', array( 'controller' => 'plans', 'action' => 'index'));
	Router::connect('/checkout_payment', array( 'controller' => 'plans', 'action' => 'checkout_payment'));

	Router::connect('/createcorder', array( 'controller' => 'orders', 'action' => 'addcord'));
	Router::connect('/countorder', array( 'controller' => 'orders', 'action' => 'countOrders'));
	Router::connect('/fixorder', array( 'controller' => 'orders', 'action' => 'fix'));
	Router::connect('/createrorder', array( 'controller' => 'orders', 'action' => 'addrord'));
	Router::connect('/add-settings', array( 'controller' => 'orders', 'action' => 'addAddress'));
	Router::connect('/save-address', array( 'controller' => 'orders', 'action' => 'saveAddress'));
	Router::connect('/get-address', array( 'controller' => 'orders', 'action' => 'getAddress'));
	Router::connect('/show-address/:id', array( 'controller' => 'orders', 'action' => 'showAddress'), array('pass' => array('id')));
	#Router::connect('/transactions-history/:product_id/:cum_qty', array( 'controller' => 'orders_lines', 'action' => 'linesbyproduct'), array('pass' => array('product_id','cum_qty')));
	#Router::connect('/transactions-history/:product_id/:cum_qty', array( 'controller' => 'orders_lines', 'action' => 'linesbyproduct'), array('pass' => array('product_id','cum_qty')));
	#Router::connect('/transactionshistory', array( 'controller' => 'orders_lines', 'action' => 'linesbyproduct'));
	// Router::connect('/transactionshistory/:location_id', array( 'controller' => 'orders_lines', 'action' => 'linesbyproduct'), array('pass' => array('location_id')));
	Router::connect('/product-view/:product_id', array('controller' => 'Products', 'action' => 'view'), array('pass' => array('product_id')));
	Router::connect('/product-edit/:product_id', array('controller' => 'Products', 'action' => 'edit'), array('pass' => array('product_id')));
	Router::connect('/add-product', array('controller' => 'products', 'action' => 'addproduct'));
	Router::connect('/count/:product_id', array( 'controller' => 'Inventories', 'action' => 'count'), array('pass' => array('product_id')));
	
	Router::connect('/inventories/index', array( 'controller' => 'Inventories', 'action' => 'index'), array('pass' => array('product_id')));
	Router::connect('/waves', array('controller' => 'waves', 'action' => 'index'));
	Router::connect('/waves-edit/:wave_id', array( 'controller' => 'waves', 'action' => 'edit'), array('pass' => array('wave_id')));
	Router::connect('/waves-view/:wave_id', array( 'controller' => 'waves', 'action' => 'view'), array('pass' => array('wave_id')));
	Router::connect('/bin-edit/:id', array( 'controller' => 'Bins', 'action' => 'edit'), array('pass' => array('id')));
	Router::connect('/resource-edit/:id', array( 'controller' => 'Resources', 'action' => 'edit'), array('pass' => array('id')));
	Router::connect('/shipments/:index/:tracking_number/:createdfrom/:status_id', array('plugin' => false, 'controller' => 'shipments', 'action' => 'index'), array('pass' => array('index', 'tracking_number', 'createdfrom', 'status_id')));
	Router::connect('/shipments/:index', array( 'controller' => 'shipments', 'action' => 'index'), array('pass' => array('index'))); 
	Router::connect('/shipments/view/:id/:index', array( 'controller' => 'shipments', 'action' => 'view'), array('pass' => array('id', 'index'))); 
	Router::connect('/shipments/edit/:id/:index', array( 'controller' => 'shipments', 'action' => 'edit'), array('pass' => array('id', 'index'))); 
	Router::connect('/shopifyorders', array( 'controller' => 'products', 'action' => 'importshopify'));
	Router::connect('/shopifyimportorders', array( 'controller' => 'orders', 'action' => 'importshopify'));
	Router::connect('/woocomorders', array( 'controller' => 'orders', 'action' => 'importwoo'));
	Router::connect('/uploadcsv', array( 'controller' => 'orders', 'action' => 'uploadcsv'));
	Router::connect('/genorderdata', array( 'controller' => 'orders', 'action' => 'genorderdata'));
	Router::connect('/importcsv', array( 'controller' => 'orders', 'action' => 'importcsv'), array('pass' => array('name')));
	Router::connect('/product-supplier-edit/:id', array( 'controller' => 'productsuppliers', 'action' => 'edit'), array('pass' => array('id')));

	Router::connect('/networks', array('plugin' => 'networks', 'controller' => 'networks', 'action' => 'index'));

	Router::connect('/salesorders', array('controller' => 'SalesOrders'));
	Router::connect('/salesorders/:action/*', array('controller' => 'SalesOrders'));

	Router::connect('/replorders', array('controller' => 'ReplOrders'));
	Router::connect('/replorders/:action/*', array('controller' => 'ReplOrders'));

	Router::connect('/orderscosts', array('controller' => 'OrdersCosts'));
	Router::connect('/orderscosts/:action/*', array('controller' => 'OrdersCosts'));
	
	Router::connect('/ordersblanket/:action/*', array('controller' => 'OrdersBlanket'));

	// Admin Controllers
	Router::connect('/admin', array('plugin' => 'admin', 'controller' => 'admin', 'action' => 'index'));
	Router::connect('/admin/users', array('plugin' => 'admin', 'controller' => 'users'));
	Router::connect('/admin/users/:action/*', array('plugin' => 'admin', 'controller' => 'users'));
	#Router::connect('/admin/users/index_js/*', array('plugin' => 'admin', 'controller' => 'users', 'action' => 'index_js'));
	#Router::connect('/admin/users/noactive/*', array('plugin' => 'admin', 'controller' => 'users', 'action' => 'noactive'));
	#Router::connect('/admin/users/remove/*', array('plugin' => 'admin', 'controller' => 'users', 'action' => 'remove'));


/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';

/**
 * Denis comments about DEL 533
 * Not found that we use SITEURL constant in code, but any way FULL_BASE_URL const must be
 * defined in vendor CakePHP lib(file: lib/Cake/bootstrap.php)
 * Not sure why we receive error: 'Use of undefined constant FULL_BASE_URL...' but think we can comment this line.
 */
//define('SITEURL',FULL_BASE_URL.router::url('/',false));
Router::parseExtensions('pdf');


