<?php
App::uses('AppController', 'Controller');
/**
 * Products Controller

 * @property Invalert $Invalert
 */
class CountriesController extends AppController {

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

    public $uses = array('Country');

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
        $limit = 100;
        $this->set(compact('limit'));
    }

    public function index_js() {
        $this->layout = false;
        $limit = 100;
        if($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }
        $orderBy = 'Country.name';
        if($this->request->query('sortby')) {
            $orderBy = $this->request->query('sortby');
        }
        $orderDir = 'ASC';
        if($this->request->query('sortdir')) {
            $orderDir = $this->request->query('sortdir');
        }
        $page = 1;
        if(isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }

        $this->Country->requrcive = -1;
        $countries = $this->Country->find('all', [
            'conditions' => [],
            'contain' => false,
            'fields' => ['Country.id', 'Country.name', 'Country.code', 'Country.status'],
            'limit' => $limit,
            'page' => $page,
            'order' => array($orderBy => $orderDir)
        ]);

        $recordsCount = $this->Country->find('count');

        $response['recordsTotal'] = $recordsCount;
        $response['rows_count'] = count($countries);
        $response['rows'] = $countries;

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

}