<?php
App::uses('AppController', 'Controller');
/**
 * Products Controller

 * @property Invalert $Invalert
 */
class AdminController extends AppController {

    //public $theme = 'Mtro';

	/**
     * Components
     *
     * @var array
     */
    public $components = array('Access', 'Paginator');

    /**
     * Helpers
     *
     * @var array
     */
    public $helpers = array();

    /**
     * If the controller is a plugin controller set the plugin name
     *
     * @var mixed
     */
    public $plugin = 'Admin';

    public $uses = array('Txreport', 'Inventory', 'Product', 'User');

    public function beforeFilter() {
       parent::beforeFilter();
    }
    
    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function isAuthorized($user) {
        if($user['email'] == 'fordenis@ukr.net' || $user['email'] == 'technoyos@gmail.com') {
            return true;
        } else {
            return false;
        }
    }

    public function index() {
        
        $tx_reports_all = $this->Txreport->find('count');
        if($tx_reports_all) {
            $tx_errors = $this->Txreport->find('count', ['conditions' => ['inv_quantity != tx_quantity']]);
        } else {
            $tx_errors = 'N/A';
        }

        $sql_count = "
            SELECT CONCAT_WS(',', user_id, sku) as gfield
            FROM `products`
            WHERE 1
            GROUP BY gfield
            HAVING COUNT(gfield) > 1";
        $recordsCount = $this->Product->query($sql_count);
        $duplicateCount = count($recordsCount);

        // Get not active users
        $exp_period = intval(Configure::read('cleanup.expire_period'));
        if(!$exp_period) {
            $exp_period = 100;
        }

        $exp_date = date('Y-m-d', strtotime('today - '. $exp_period .' days'));
        $users_count = $this->User->find('count', [
            'contain' => false,
            'conditions' => ['User.last_login <' => $exp_date, 'User.role !=' => 'paid' ],
        ]);

        $this->loadModel('Country');
        $allCountry = $this->Country->find('count');
        $disabledCountry = $this->Country->find('count', ['conditions' => array('Country.status' => 0)]);

        $this->set(compact('duplicateCount', 'tx_errors', 'tx_reports_all', 'users_count', 'exp_period', 'allCountry', 'disabledCountry'));
    }

}