<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
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
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));
// Adding default From mail for sending mails to new users

Configure::write('Recaptcha.publicKey', '6LeHmAkTAAAAAHgtG4PJS5hNmBC0iT5GPRdDr19Y');
Configure::write('Recaptcha.privateKey', '6LeHmAkTAAAAACepTHHLXmE_OcIEig64un7hF4hp');
Configure::write('Config.language', 'eng');
Configure::write('Productlimit.paiduser',"99999999");
Configure::write('Productlimit.freeuser',"10");
Configure::write('FileuploadSize',500000);
Configure::write('Paypal.sandboxid',"AUdkyk9-Z2NDCw9dqC33vYmF2z2sQnXkhuTxmzH5LIcqmmLmF81wK62rmU13BdIPGLReA2uRr_gKrJDZ");
Configure::write('Paypal.productionid',"<insert production client id>");
Configure::write('Paypal.API_USERNAME',"rajesh.freak6_api1.gmail.com");
Configure::write('Paypal.API_PASSWORD',"435EXD38HHEGQ7A4");
Configure::write('Paypal.API_SIGNATURE',"AFcWxV21C7fd0v3bYYYRCpSSRl31ARz9tt0vtdYrWklfzMPbsyz1ymnS");
Configure::write('Paypal.API_ENDPOINT',"https://api-3t.sandbox.paypal.com/nvp");
Configure::write('Paypal.USE_PROXY',FALSE);
Configure::write('Paypal.IS_ONLINE',FALSE);
Configure::write('Paypal.PROXY_HOST','127.0.0.1');
Configure::write('Paypal.PROXY_PORT','808');

Configure::write('cleanup.expire_period','101'); // How many days ago user last login

if(Configure::read('Paypal.IS_ONLINE')  == FALSE) {
    Configure::write('Paypal.PAYPAL_URL','https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=');
} else {
    Configure::write('Paypal.PAYPAL_URL','https://www.paypal.com/webscr&cmd=_express-checkout&token=');
}
Configure::write('Paypal.VERSION','57.0');

Configure::write('Product.image_missing', 'https://delivrd.com/image_missing.jpg');

// Inventory alerts emails
Configure::write('InventoryAlerts.role', 'all'); //values: all, admin, registred, paid. Ex: 'admin|paid' will receive only admins and paid users
Configure::write('InventoryAlerts.last_login', 7); //send email only to users that last login is within X days. 0 allow any last login date

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models/', '/next/path/to/models/'),
 *     'Model/Behavior'            => array('/path/to/behaviors/', '/next/path/to/behaviors/'),
 *     'Model/Datasource'          => array('/path/to/datasources/', '/next/path/to/datasources/'),
 *     'Model/Datasource/Database' => array('/path/to/databases/', '/next/path/to/database/'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions/', '/next/path/to/sessions/'),
 *     'Controller'                => array('/path/to/controllers/', '/next/path/to/controllers/'),
 *     'Controller/Component'      => array('/path/to/components/', '/next/path/to/components/'),
 *     'Controller/Component/Auth' => array('/path/to/auths/', '/next/path/to/auths/'),
 *     'Controller/Component/Acl'  => array('/path/to/acls/', '/next/path/to/acls/'),
 *     'View'                      => array('/path/to/views/', '/next/path/to/views/'),
 *     'View/Helper'               => array('/path/to/helpers/', '/next/path/to/helpers/'),
 *     'Console'                   => array('/path/to/consoles/', '/next/path/to/consoles/'),
 *     'Console/Command'           => array('/path/to/commands/', '/next/path/to/commands/'),
 *     'Console/Command/Task'      => array('/path/to/tasks/', '/next/path/to/tasks/'),
 *     'Lib'                       => array('/path/to/libs/', '/next/path/to/libs/'),
 *     'Locale'                    => array('/path/to/locales/', '/next/path/to/locales/'),
 *     'Vendor'                    => array('/path/to/vendors/', '/next/path/to/vendors/'),
 *     'Plugin'                    => array('/path/to/plugins/', '/next/path/to/plugins/'),
 * ));
 *
 */

/**
 * Custom Inflector rules can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. Make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */
 
CakePlugin::load('Search');
CakePlugin::load('CakePdf', array('bootstrap' => true, 'routes' => true));
CakePlugin::load('Users', array('routes' => true));
CakePlugin::load('Utils');
CakePlugin::load('Upload');
CakePlugin::load('MenuBuilder');
CakePlugin::load('AjaxValidation');
CakePlugin::load('Csv');
CakePlugin::load('GoogleCharts');
CakePlugin::load('CsvView');
CakePlugin::load('Recaptcha');
CakePlugin::load('Paypal');
CakePlugin::load('Mandrill');
CakePlugin::load('Networks');
CakePlugin::load('Admin');

/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter. By default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyCacheFilter' => array('prefix' => 'my_cache_'), //  will use MyCacheFilter class from the Routing/Filter package in your app with settings array.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 *		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
    'CacheDispatcher',
	'AccessDispatcher', // we will use it to optimize speed later
));

Configure::write(
    'Metric',
    array(
        'weight' => 'kg',
        'volume' => 'cm'
    )
);
Configure::write(
    'US',
    array(
        'weight' => 'lbs',
        'volume' => 'inches'
    )
);



/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'File',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'File',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));
CakeLog::config('default', [
    'engine' => 'Syslog'
]);
CakeLog::config('mandrill', array(
    'engine' => 'File',
    'file' => 'mandrill.log',
));

//Is this partner installation or Delivrd 
$server_name = $_SERVER['SERVER_NAME'];
if($server_name == 'http://eeldeliverysolutions.com')
{
    Configure::write('DelivrdHosted',true );
    Configure::write('LoginLogoURL',"/theme/Mtro/assets/admin/layout/img/logo_e.png" );
    Configure::write('InAppLogoURL',"/theme/Mtro/assets/admin/layout/img/logo_es.png" );
    Configure::write('OperatorName',"Eagle Express Ghana" );
    Configure::write('App.defaultEmail', 'do-not-reply@eeldeliverysolutions.com');
} else {
    Configure::write('DelivrdHosted',false );
    Configure::write('LoginLogoURL',"/theme/Mtro/assets/admin/layout/img/logo_b.png" );
    Configure::write('InAppLogoURL',"/theme/Mtro/assets/admin/layout/img/logo.png" );
    Configure::write('App.defaultEmail', 'do-not-reply@delivrd.com'); 
    Configure::write('OperatorName',"Delivrd" );
}

Configure::write('Recaptcha.key', '6LfAq5oUAAAAAGzOytWRHFJcSxGsdd7qcl_YHbAS');
Configure::write('Recaptcha.secret', '6LfAq5oUAAAAACv_zj4rtJ4J_kqiLNDM6bJAtfUT');
