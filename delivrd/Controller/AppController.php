<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package     app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $theme = 'Mtro';

    public $components = array(
        'Session',
        'Auth' => array(
            'loginRedirect' => array(
                'controller' => 'Dash',
                'action' => 'index'
            ),
            'logoutRedirect' => array(
                'controller' => 'users',
                'action' => 'login'
            ),
            /*'authenticate' => array(
                'Form' => array(
                    'contain' => array('Country', 'Currency', 'Msystem', 'State')
                ),
                'Basic' => array(
                    'contain' => array('Country', 'Currency', 'Msystem', 'State')
                ),
            ),*/
            'authorize' => array('Controller') //load problems
        ),
        'Access',
        'Account'
    );
    
    public $helpers = array('MenuBuilder.MenuBuilder', 'App');

    public $uoms = array('Piece' => 'Piece', 'Carton' => 'Carton', 'Kilogram' => 'Kilogram', 'Pound' => 'Pound', 'Box' => 'Box', 'ca' => 'Case', 'Each' => 'Each', 'Tray' => 'Tray', 'Kit' => 'Kit');
    public $uoms_display = array(
        'Piece' => [
            'm' => 'Pieces',
            'o' => 'Piece'
        ],
        'Carton' => [
            'm' => 'Cartons',
            'o' => 'Carton'
        ],
        'Kilogram' => [
            'm' => 'Kilograms',
            'o' => 'Kilogram'
        ],
        'Pound' => [
            'm' => 'Pounds',
            'o' => 'Pound',
        ],
        'Box' => [
            'm' => 'Boxes',
            'o' => 'Box',
        ],
        'ca' => [
            'm' => 'Cases',
            'o' => 'Case'
        ],
        'Each' => [
            'm' => 'Each',
            'o' => 'Each'
        ],
        'Tray' => [
            'm' => 'Tray',
            'o' => 'Tray'
        ]
    );
    
    public $_access = [];

    public $_authUser = false;

    public function beforeRender() {
        parent::beforeRender();

        $controller = $this->params->params['controller'];
        $action = $this->params->params['action'];
        $plugin = (!empty($this->params->params['plugin'])) ? $this->params->params['plugin'] : null;

        if($this->Auth->user('id')) {
            if($this->_authUser['User']['role'] !== 'paid' && $this->_authUser['User']['btn'] > 0 && (strcasecmp($controller, 'Payment') != 0 && strcasecmp($controller, 'Subscriptions') != 0)) {
                #pr($controller);
                #exit;
                $this->redirect(array('plugin' => false, 'controller' => 'subscriptions', 'action' => 'presignin'));
            }

            if($this->_authUser['User']['role'] !== 'paid' && (($controller !== 'Dash' && $action !== 'ofindex') && ($action !== 'login') && ($action !== 'signup'))) {
                if(!($controller == 'subscriptions' || $controller == 'payment')) {
                    if($this->_authUser['User']['role'] == 'trial') {
                        $remaining_days = $this->Account->getRemainingDays($this->_authUser['Subscription']['expiry_date']);
                        if($remaining_days <= 0) { //  && $this->_authUser['User']['role'] !== 'extend'
                            $this->redirect(array('plugin' => false, 'controller' => 'Dash', 'action' => 'ofindex'));
                        }
                    }
                }
            }
        }
   

        $this->set('uoms',$this->uoms);
        $this->set('uoms_display',$this->uoms_display);
        
        //Get ads for current controller
        $this->loadModel('Ad');
        $current_controller_name = $this->name;
        $ads = $this->Ad->find('all');
        $header_ad = $this->searchForId($this->view,'Header', $ads);
        $sidebar_ad = $this->searchForId($this->view,'SideBar', $ads);
        
        $this->set(compact('header_ad','sidebar_ad'));
    }
    
    public function searchForId($view,$position, $array) {
        $ads_array = array();
        //Currently, we show ads on all pages. Later, we will target ads based on Model
        foreach ($array as $key => $val) {
            array_push($ads_array,$val['Ad']);
        }
        if(sizeof($ads_array) > 0) {
            $random_key = array_rand($ads_array);
            return $ads_array[$random_key];
        }
    }

    public function beforeFilter() {
        $this->layout = 'mtrd';

        $controller = $this->params->params['controller'];
        $action = $this->params->params['action'];
        $index = ((isset($this->params->params['index']) && !empty($this->params->params['index'])) ? $this->params->params['index'] : null);
        $plugin = (!empty($this->params->params['plugin'])) ? $this->params->params['plugin'] : null;
        $this->set(compact('controller', 'action', 'plugin', 'index'));
    
        //Get user access
        if($this->Auth->user('id')) {
            $this->set('_access', $this->Access->_access);
        }

        $this->loadModel('User');
        $this->_authUser = $this->User->getAuthUser($this->Auth->user('id'));

        $this->set('_authUser', $this->_authUser);
        $this->set('authUser', $this->Auth->user());
    }

    public function isAuthorized($user) {
        if($user) {
            return true;
        }
        // Default deny
        return false;
    }
}
