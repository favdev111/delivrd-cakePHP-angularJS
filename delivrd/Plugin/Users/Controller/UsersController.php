<?php
/**
 * Copyright 2010 - 2013, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2013, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeEmail', 'Network/Email');
App::uses('UsersAppController', 'Users.Controller');

/**
 * Users Users Controller
 *
 * @package       Users
 * @subpackage    Users.Controller
 * @property      AuthComponent $Auth
 * @property      CookieComponent $Cookie
 * @property      PaginatorComponent $Paginator
 * @property      SecurityComponent $Security
 * @property      SessionComponent $Session
 * @property      User $User
 * @property      RememberMeComponent $RememberMe
 */
class UsersController extends UsersAppController {

    /**
     * Controller name
     *
     * @var string
     */
    public $name = 'Users';
    public $theme = 'Mtro';

    /**
     * If the controller is a plugin controller set the plugin name
     *
     * @var mixed
     */
    public $plugin = null;

    /**
     * Helpers
     *
     * @var array
     */
    public $helpers = array(
        'Html',
        'Form',
        'Session',
        'Time',
        'Text'
    );

    /**
     * Components
     *
     * @var array
     */
    public $components = array(
        'Auth',
        'Session',
        'Cookie',
        'Paginator',
        //'Security',
        'Search.Prg',
        'RequestHandler',
        'Users.RememberMe',
        'Recaptcha.Recaptcha'
    );

    /**
     * Preset vars
     *
     * @var array $presetVars
     * @link https://github.com/CakeDC/search
     */
    public $presetVars = true;

    /**
     * Constructor
     *
     * @param CakeRequest $request Request object for this controller. Can be null for testing,
     *  but expect that features that use the request parameters will not work.
     * @param CakeResponse $response Response object for this controller.
     */
    public function __construct($request, $response) {
        $this->_setupComponents();
        parent::__construct($request, $response);
        $this->_reInitControllerName();
    }

    /**
     * Providing backward compatibility to a fix that was just made recently to the core
     * for users that want to upgrade the plugin but not the core
     *
     * @link http://cakephp.lighthouseapp.com/projects/42648-cakephp/tickets/3550-inherited-controllers-get-wrong-property-names
     * @return void
     */
    protected function _reInitControllerName() {
        $name = substr(get_class($this), 0, -10);
        if ($this->name === null) {
            $this->name = $name;
        } elseif ($name !== $this->name) {
            $this->name = $name;
        }
    }

    /**
     * Returns $this->plugin with a dot, used for plugin loading using the dot notation
     *
     * @return mixed string|null
     */
    protected function _pluginDot() {
        if (is_string($this->plugin)) {
            return $this->plugin . '.';
        }
        return $this->plugin;
    }

    /**
     * Wrapper for CakePlugin::loaded()
     *
     * @param string $plugin
     * @return boolean
     */
    protected function _pluginLoaded($plugin, $exception = true) {
        $result = CakePlugin::loaded($plugin);
        if ($exception === true && $result === false) {
            throw new MissingPluginException(array('plugin' => $plugin));
        }
        return $result;
    }

    /**
     * Setup components based on plugin availability
     *
     * @return void
     * @link https://github.com/CakeDC/search
     */
    protected function _setupComponents() {
        if ($this->_pluginLoaded('Search', false)) {
            $this->components[] = 'Search.Prg';
        }
    }

    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter() {
        parent::beforeFilter();
        $this->_setupAuth();
        $controller = $this->params->params['controller'];
        $action = $this->params->params['action'];
        $index = ((isset($this->params->params['index']) && !empty($this->params->params['index'])) ? $this->params->params['index'] : null);
        $index = ($action == 'viewrord' ? 2 : 1);
        $index = ($action == 'editrord' ? 2 : 1);
        $index = ($action == 'addrord' ? 2 : 1);
        $plugin = (!empty($this->params->params['plugin'])) ? $this->params->params['plugin'] : null;

        //$this->Security->validatePost = false;
        $this->_setupPagination();
        if ((isset($this->Security) && $this->action == 'signup_process') || (isset($this->Security) && $this->action == 'login') || (isset($this->Security) && $this->action == 'editmy')) {
             $this->Security->validatePost = false;
        }
        $this->Auth->allow('login','add','reset_password','signup','laststep','signup_process');

        $this->set('model', $this->modelClass);

        if (!Configure::read('App.defaultEmail')) {
            Configure::write('App.defaultEmail', 'noreply@' . env('HTTP_HOST'));
        }
        $this->set(compact('controller', 'action', 'plugin', 'index'));
    }

    /**
     * beforeRender
     *
     * @return void
     * @access public
     */
    public function beforeRender() {
        parent::beforeRender();
        $this->set('modelClass', $this->modelClass);
        $this->response->disableCache();
    }

/**
 * Sets the default pagination settings up
 *
 * Override this method or the index action directly if you want to change
 * pagination settings.
 *
 * @return void
 */
    protected function _setupPagination() {
        $this->Paginator->settings = array(
            'limit' => 12,
            'conditions' => array(
                $this->modelClass . '.active' => 1,
                $this->modelClass . '.client' => "GSE",
                $this->modelClass . '.email_verified' => 1
            )
        );
    }

/**
 * Sets the default pagination settings up
 *
 * Override this method or the index() action directly if you want to change
 * pagination settings. admin_index()
 *
 * @return void
 */
    protected function _setupAdminPagination() {
        $this->Paginator->settings = array(
            'limit' => 20,
            'order' => array(
                $this->modelClass . '.created' => 'desc'
            )
        );
    }

/**
 * Setup Authentication Component
 *
 * @return void
 */
    protected function _setupAuth() {
        if (Configure::read('Users.disableDefaultAuth') === true) {
            return;
        }

        $this->Auth->allow('add', 'reset', 'verify', 'logout', 'view', 'reset_password', 'login', 'resend_verification');

        if (!is_null(Configure::read('Users.allowRegistration')) && !Configure::read('Users.allowRegistration')) {
            $this->Auth->deny('add');
        }

        if ($this->request->action == 'register') {
            $this->Components->disable('Auth');
        }

        $this->Auth->authenticate = array(
            'Form' => array(
                'fields' => array(
                    'username' => 'email',
                    'password' => 'password'),
                'userModel' => $this->_pluginDot() . $this->modelClass,
                'contain' => array('Country','Currency','Msystem','State'/*,'Integration' => array('fields' => array('backend')), 'Product' => array('conditions' => array(
                'Product.status_id !=' => 13))*/),
                'scope' => array(
                    $this->modelClass . '.active' => 1,
                    $this->modelClass . '.email_verified' => 1)));

        $this->Auth->loginRedirect = '/';
        $this->Auth->logoutRedirect = array('plugin' => Inflector::underscore($this->plugin), 'controller' => 'users', 'action' => 'login');
        $this->Auth->loginAction = array('admin' => false, 'plugin' => Inflector::underscore($this->plugin), 'controller' => 'users', 'action' => 'login');
    }

/**
 * Simple listing of all users
 *
 * @return void
 */
    public function index() {
        $this->layout = 'mtrd';
        if($this->Auth->user('is_admin') == true)
        {

            $this->set('users', $this->Paginator->paginate($this->modelClass));

        } else {
            return $this->redirect('/');
        }
    }

    public function nindex() {
    $this->Paginator->settings = array(
            'limit' => 12,
            'conditions' => array(
                $this->modelClass . '.active' => 1,
                $this->modelClass . '.email_verified' => 1,
                $this->modelClass . '.isdcop' => 1
            )
        );
        $this->loadModel('Network');
        $networks = $this->Network->find('all', array('conditions' => array('Network.user_id' => $this->Auth->user('id'))));
        $this->set(compact('networks'));
        $this->set('users', $this->Paginator->paginate($this->modelClass));
    }

    public function fnindex() {

        $this->Paginator->settings = array(
            'limit' => 12,
            'conditions' => array(
                $this->modelClass . '.active' => 1,
                $this->modelClass . '.email_verified' => 1,
                $this->modelClass . '.isdcop' => 1
            )
        );
        $this->loadModel('Network');
        $networks = $this->Network->find('all', array('conditions' => array('Network.dcop_user_id' => $this->Auth->user('id'))));
        $this->set(compact('networks'));
        $this->set('users', $this->Paginator->paginate($this->modelClass));

            }



/**
 * The homepage of a users giving him an overview about everything
 *
 * @return void
 */
    public function dashboard() {
        $user = $this->{$this->modelClass}->read(null, $this->Auth->user('id'));
        $this->set('user', $user);
    }

/**
 * Shows a users profile
 *
 * @param string $slug User Slug
 * @return void
 */
    public function viewuserproducts($slug = null) {
        $this->layout = 'mtrd';
        try {
        $user = $this->{$this->modelClass}->view($slug);
        //var_dump($user);die;
        $this->loadModel('Product');
        $products = $this->Product->find('all',array('conditions' => array('Product.user_id' => $user['User']['id'] )));

    //  var_dump($products);
        $this->set('user', $this->{$this->modelClass}->view($slug));
        $this->set('products', $products);
        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage());
            $this->redirect('/');
        }
    }

        public function view($slug = null)
        {
            $this->layout = 'mtrd';
        try {
        $user = $this->{$this->modelClass}->view($slug);
        //var_dump($user['User']['id']);
        $this->loadModel('Product');
        $productscount = $this->Product->find('count',array('conditions' => array('Product.user_id' => $user['User']['id'] )));
                $this->loadModel('Order');
                $salesorderscount = $this->Order->find('count',array('conditions' => array('Order.ordertype_id' => '1', 'Order.user_id' => $user['User']['id'] )));
                $replorderscount = $this->Order->find('count',array('conditions' => array('Order.ordertype_id' => '2', 'Order.user_id' => $user['User']['id'] )));
                $this->loadModel('Shipment');
                $totalshipmentscount = $this->Shipment->find('count',array('conditions' => array('Shipment.user_id' => $user['User']['id'] )));
    //  var_dump($products);
        $this->set('user', $this->{$this->modelClass}->view($slug));
        $this->set('productscount', $productscount);
                $this->set('salesorderscount', $salesorderscount);
                $this->set('replorderscount', $replorderscount);
                $this->set('totalshipmentscount', $totalshipmentscount);

        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage());
            $this->redirect('/');
        }

        }

    /**
     * Edit
     *
     * @param string $id User ID
     * @return void
     */
    public function edit() {
        $this->layout = 'mtrd';
        $id = $this->Auth->user('id');

        if ($this->request->is(array('post', 'put'))) {
            $this->User->id = $id;

            $options = array(
                'showtours','msystem_id','currency_id','paid','locationsactive','inventoryauto','inventoryalert','fast_invalert','inventoryremarks','timezone_id','copypdtprice', 'zeroquantity','packinslip_desc','default_country_id','sales_title','list_limit','kit_component_issue'
            );
            foreach ($options as $option) {
                $this->setparams($option);
            }
            if($this->request->data['User']['timezone_id']) {
                $defaultTimezone = $this->User->Zone->find('list', array('conditions' => array('Zone.id' => $this->request->data['User']['timezone_id'])));
                $this->Session->write('timezone', $defaultTimezone[$this->request->data['User']['timezone_id']]);
            } else {
                $this->Session->write('timezone', '');
            }

            if(isset($this->request->data['User']['inventory_alert']) && $this->request->data['User']['inventory_alert'] != '') {
                $res = $this->User->saveField('inventory_alert', $this->request->data['User']['inventory_alert']);
            }

            if($this->request->data['User']['pick_by_order'] == '') {
                $this->User->saveField('pick_by_order', 0);
            } else {
                $this->User->saveField('pick_by_order', $this->request->data['User']['pick_by_order']);
            }

            if($this->request->data['User']['batch_pick'] == '') {
                $this->User->saveField('batch_pick', 0);
            } else {
                $this->User->saveField('batch_pick', $this->request->data['User']['batch_pick']);
            }

            $this->User->saveField('settingson', 1);

            $this->User->updateRole($this->Auth->user('id'));

            $this->Session->write('Auth', $this->User->read(null, $id));
            $this->Session->setFlash(__('Settings updated successfully.'), 'admin/success', array());
            return $this->redirect(array('plugin' => 'users','controller' => 'users', 'action' => 'edit'));
        } else {
            $user = $this->User->getAuthUser($id);
            $this->request->data = $user;
        }

        #$inventory_alert = $this->request->data['User']['inventory_alert'];
        $currencies = $this->User->Currency->find('list');
        $timezone = $this->User->Zone->find('list', array('order' => array('Zone.zone_name' => 'asc')));
        $msystems = $this->User->Msystem->find('list');
        $pickbyorder = $this->User->pickOptions;
        $batch = $this->User->batchOptions;
        $invAlert = $this->User->invAlerts;
        $this->set(compact('currencies','msystems', 'pickbyorder' ,'batch', 'invAlert', 'timezone'));
    }

    public function editmy() {
        $this->layout = 'mtrd';
        $this->User->id = $this->Auth->user('id');
        if ($this->request->is(array('post', 'put'))) {
            unset($this->User->validate['tos']);

            if ($this->User->save($this->request->data)) {
                $this->loadModel('Address');
                $this->request->data('Address.user_id',$this->Auth->user('id'));
                $this->request->data('Address.user_address_id',$this->User->id);
                $this->request->data('Address.street', htmlspecialchars($this->request->data['Address']['street']));
                $this->request->data('Address.city', htmlspecialchars($this->request->data['Address']['city']));
                $this->request->data('Address.zip', htmlspecialchars($this->request->data['Address']['zip']));
                $this->request->data('Address.stateprovince', htmlspecialchars($this->request->data['Address']['stateprovince']));

                if(!empty($this->request->data['Address']['state_id']) && $this->request->data['Address']['country_id'] == 1) {
                    $statedate = $this->Address->State->findById($this->request->data['Address']['state_id']);
                    $this->request->data('Address.stateprovince', $statedate['State']['name']);
                } else {
                   $this->request->data('Address.state_id', '');
                }

                if ($this->Address->save($this->request->data)) {
                    $this->Session->write('Auth', $this->User->read(null, $this->Auth->user('id')));
                    if(!empty($this->request->data['User']['logo_url'] && !empty($this->Auth->user('logo')))) {
                        $this->User->id = $this->Auth->user('id');
                        unlink(WWW_ROOT . 'files/user/logo/'. $this->Auth->user('id') .'/'. $this->Auth->user('logo'));
                        $this->User->saveField('logo', '');
                        $this->Session->write('Auth', $this->User->read(null, $this->Auth->user('id')));
                    }
                    $this->Session->setFlash(__('Your settings have been updated'), 'admin/success', array());
                    return $this->redirect(array('plugin' => 'users','controller' => 'users', 'action' => 'editmy'));
                }
            } else {
                //pr($this->User->validate)
                $this->Session->setFlash(__('The User could not be saved. Please, try again.'), 'admin/danger', array());
                if(!empty($this->User->validationErrors['logo'][0])) {
                    $this->Session->setFlash($this->User->validationErrors['logo'][0], 'admin/danger');
                }
            }
        } else {
            $this->request->data = $this->User->find('first', array('conditions' => array('User.id' => $this->Auth->user('id')), 'contain' => array('Address')));
        }

        $states = $this->User->State->find('list');
        $countries = $this->User->Country->find('list');
        $btypes = $this->User->businessType;
        unset($btypes[0]);
        $this->set(compact('states', 'countries', 'btypes'));
    }


    public function editint() {
        $this->layout = 'mtrd';
        $id = $this->Auth->user('id');
        if ($this->request->is(array('post', 'put'))) {

            $this->User->id = $id;
            $this->User->saveField('magentousername', $this->request->data['User']['magentousername']);
            $this->User->saveField('magentopassword', $this->request->data['User']['magentopassword']);
            $this->User->saveField('magentourl', $this->request->data['User']['magentourl']);
            $this->User->saveField('wooconsumerkey', $this->request->data['User']['wooconsumerkey']);

            $this->Session->write('Auth', $this->User->read(null, $id));
            $this->Session->setFlash(__('Your integration settings have been updated'),'default',array('class'=>'alert alert-success'));
        } else {
            $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
            $this->request->data = $this->User->find('first', $options);
        }
    }

    public function saveparam() {
        if ($this->request->is(array('post', 'put'))) {
            $id = $this->Auth->user('id');
            $settings_av = ['showtours', 'locationsactive', 'paid', 'inventoryauto', 'inventoryremarks', 'copypdtprice', 'zeroquantity', 'packinslip_desc', 'default_country_id', 'inventoryalert', 'fast_invalert', 'currency_id', 'msystem_id', 'batch_pick', 'pick_by_order', 'timezone_id', 'list_limit', 'kit_component_issue'];
            if(!empty($this->request->data['name']) && in_array($this->request->data['name'], $settings_av)) {
                $this->User->id = $id;
                
                if($this->request->data['name'] == 'msystem_id') {
                    $this->User->saveField($this->request->data['name'], $this->request->data['value']);
                    if ($this->request->data['value'] == 1) {
                        $this->Session->write('weight_unit', Configure::read('Metric.weight'));
                        $this->Session->write('volume_unit', Configure::read('Metric.volume'));
                    }
                    if ($this->request->data['value'] == 2) {
                        $this->Session->write('weight_unit', Configure::read('US.weight'));
                        $this->Session->write('volume_unit', Configure::read('US.volume'));
                    }
                } elseif($this->request->data['name'] == 'sales_title') {
                    $this->User->saveField($this->request->data['name'], strip_tags($this->request->data['value'], '<script>'));
                    $this->Session->write($this->request->data['name'], strip_tags($this->request->data['value']));
                } else if($this->request->data['name'] == 'currency_id') {
                    $this->User->saveField($this->request->data['name'], $this->request->data['value']);
                    $this->setcurrency($this->request->data['value']);
                }  else if($this->request->data['name'] == 'timezone_id') {
                    $this->User->saveField($this->request->data['name'], $this->request->data['value']);
                    $defaultTimezone = $this->User->Zone->find('list', array('conditions' => array('Zone.id' => $this->request->data['value'])));
                    $this->Session->write('timezone', $defaultTimezone[$this->request->data['value']]);
                } else {
                    $this->User->saveField($this->request->data['name'], $this->request->data['value']);
                    $this->Session->write($this->request->data['name'], htmlspecialchars($this->request->data['value']));
                    $this->Session->write('Auth', $this->User->read(null, $id));
                }
                
                //$this->setparams($this->request->data['name']);
                //$this->Session->write('Auth', $this->User->read(null, $id));
                $response['action'] = 'success';
                $response['msg'] = 'Parameter success changed.';
            } else {
                $response['action'] = 'error';
                $response['msg'] = 'You try to configure something what not exists.';
            }
            echo json_encode($response);
            exit;
        }
    }


/**
 * Admin Index
 *
 * @return void
 */
    public function admin_index() {
        $this->Prg->commonProcess();
        unset($this->{$this->modelClass}->validate['username']);
        unset($this->{$this->modelClass}->validate['email']);
        $this->{$this->modelClass}->data[$this->modelClass] = $this->passedArgs;

        if ($this->{$this->modelClass}->Behaviors->loaded('Searchable')) {
            $parsedConditions = $this->{$this->modelClass}->parseCriteria($this->passedArgs);
        } else {
            $parsedConditions = array();
        }

        $this->_setupAdminPagination();
        $this->Paginator->settings[$this->modelClass]['conditions'] = $parsedConditions;
        $this->set('users', $this->Paginator->paginate());
    }

/**
 * Admin view
 *
 * @param string $id User ID
 * @return void
 */
    public function admin_view($id = null) {
        try {
            $user = $this->{$this->modelClass}->view($id, 'id');
        } catch (NotFoundException $e) {
            $this->Session->setFlash(__d('users', 'Invalid User.'));
            $this->redirect(array('action' => 'index'));
        }

        $this->set('user', $user);
    }

/**
 * Admin add
 *
 * @return void
 */
    public function admin_add() {
        if (!empty($this->request->data)) {
            $this->request->data[$this->modelClass]['tos'] = true;
            $this->request->data[$this->modelClass]['email_verified'] = true;
                        $this->request->data[$this->modelClass]['is_admin'] = 3;

            if ($this->{$this->modelClass}->add($this->request->data)) {
                $this->Session->setFlash(__d('users', 'The User has been saved'));
                $this->redirect(array('action' => 'index'));
            }
        }
        $this->set('roles', Configure::read('Users.roles'));
    }

/**
 * Admin edit
 *
 * @param null $userId
 * @return void
 */
    public function admin_edit($userId = null) {
        try {
            $result = $this->{$this->modelClass}->edit($userId, $this->request->data);
            if ($result === true) {
                $this->Session->setFlash(__d('users', 'User saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->request->data = $result;
            }
        } catch (OutOfBoundsException $e) {
            $this->Session->setFlash($e->getMessage());
            $this->redirect(array('action' => 'index'));
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->{$this->modelClass}->read(null, $userId);
        }
        $this->set('roles', Configure::read('Users.roles'));
    }

/**
 * Delete a user account
 *
 * @param string $userId User ID
 * @return void
 */
    public function admin_delete($userId = null) {
        if ($this->{$this->modelClass}->delete($userId)) {
            $this->Session->setFlash(__d('users', 'User deleted'));
        } else {
            $this->Session->setFlash(__d('users', 'Invalid User'));
        }

        $this->redirect(array('action' => 'index'));
    }

/**
 * Search for a user
 *
 * @return void
 */
    public function admin_search() {
        $this->search();
    }

    /**
     * User register action
     *
     * @return void
     */
    public function red()
    {
        $this->redirect(array('action' => 'login'));
    }

    public function regend()
    {
        $this->layout = 'mtrl';
    }

    public function subscribe()
    {
        $this->layout = 'mtrd';
    }

    public function signup($invite_hash = null) {
        $invite = array();
        $limited = 0;
        if($invite_hash) {
            $this->loadModel('NetworksInvite');
            $invite = $this->NetworksInvite->find('first', array('conditions' => array('NetworksInvite.hash' => $invite_hash), 'contain' => array('Network', 'Network.CreatedByUser')));
            
            $limited = $invite['NetworksInvite']['limited'];
            if($invite) {
                $this->request->data['User']['email'] = $invite['NetworksInvite']['email'];
            }
        }
        
        $userpage = $this->here;
        $query = http_build_query($this->request->query);
        if($query) {
            $userpage = $userpage .'?'. $query;
        }

        $this->layout = 'mtrl';
        $modelClass = $this->modelClass;
        $created = 0;
        $btypes = $this->User->businessType;
        unset($btypes[0]);
        $this->set(compact('invite', 'created', 'modelClass', 'limited', 'btypes', 'userpage'));

        // if ($this->request->is('post')) {
        //  unset($this->User->validate['username']);
        //  unset($this->User->validate['password']);
        //  unset($this->User->validate['temppassword']);
        //  $this->User->set($this->request->data);
        //  if($this->User->validates())
  //           {
        //      if (!empty($this->request->data)) {
        //          $activationKey = md5(uniqid());
        //             $this->request->data['User']['token'] = $activationKey;
        //          $this->User->create();

        //          if($this->User->save($this->request->data)){

        //              $this->_sendVerificationEmail($this->request->data);
        //              $response['status'] = true;
  //                       $response['message']='Your account has been created. You should shortly receive a confirmation email. Follow the instructions in the email to activate your Delivrd account.';
  //                       echo json_encode($response);
  //                       die;

        //          } else {
        //              $response['status'] = false;
  //                       $response['message']='Your account could not be created. Please, try again.';
  //                       echo json_encode($response);
  //                       die;
        //          }
        //      }
        //  }else{
        //       $response=array();
     //             $user = $this->User->invalidFields();
     //             $response['status']=false;
     //             $response['message']='Please fix the error.';
     //             $response['data']=compact('user');
     //             echo json_encode($response);
     //             die;
        //  }

        // }

    }

    public function signup_process(){
        if ($this->request->is('post')) {
            
            unset($this->User->validate['username']);
            unset($this->User->validate['temppassword']);

            if (!empty($this->request->data)) {

                if(true || $this->request->data['g-recaptcha-response']) {
                    /*$r['secret'] = Configure::read('Recaptcha.secret');
                    $r['response'] = $this->request->data['g-recaptcha-response'];

                    $url = 'https://www.google.com/recaptcha/api/siteverify';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($r));
                    $output = curl_exec($ch);
                    $output = json_decode($output);
                    curl_close($ch);*/

                    if(true || (isset($output->success) && $output->success)) {

                        $ip_adress = $this->request->clientIp(); //'82.114.90.126'; //
                        $code = $this->Account->getCountryByIp($ip_adress);

                        $countryAllowed = true;
                        if($code) {
                            $this->loadModel('Country');
                            $countries = $this->Country->find('list', ['fields'=> ['Country.code', 'Country.status']]);
                            
                            if ($countries[$code] === "0") {
                                $countryAllowed = false;
                            }
                        }
                        
                        if($countryAllowed) {
                            $activationKey = md5(uniqid());
                            $username = explode( '@', $this->request->data['User']['email']);
                            $this->request->data['User']['username'] = $username[0];
                            $this->request->data['User']['token'] = $activationKey;
                            //$this->request->data['User']['email'] = $this->request->data['User']['email'];
                            //$this->request->data['User']['password'] = $this->request->data['User']['password'];
                            $this->request->data['User']['active'] = 1;
                            $this->request->data['User']['email_verified'] = 1;
                            if(isset($this->request->data['User']['tos']) && $this->request->data['User']['tos'] == 1 && isset($this->request->data['User']['tos2']) && $this->request->data['User']['tos2'] == 2) {
                                $this->request->data['User']['tos'] = 2;
                            }

                            //$this->User->create();
                            $user = $this->User->find('first', array(
                                'conditions' => array('User.email' =>  $this->request->data['User']['email'], 'User.password' => AuthComponent::password($this->request->data['User']['password']))
                            ));
                            if($user) {
                                $this->User->id = $user['User']['id'];
                                $this->User->saveField('token', $this->request->data['User']['token']);
                                $response['status'] = true;
                                $response['token'] = $this->request->data['User']['token'];
                                $response['url'] = Router::url(array("plugin" => "users", "controller" => "users", "action" => "login", $this->request->data['User']['token']), true);
                                $response['message']='Your account already has been created.';
                                echo json_encode($response);
                                die;
                            } else{
                                $user = $this->User->find('first', array(
                                'conditions' => array('User.email' =>  $this->request->data['User']['email']))
                                );
                                if($user) {
                                    $response['status'] = true;
                                    $response['message']='Your password is incorrect.';
                                    echo json_encode($response);
                                    die;
                                } else {
                                    if($this->User->save($this->request->data)){
                                        //$this->_sendVerificationEmail($this->request->data);
                                        $response['status'] = true;
                                        $response['token'] = $this->request->data['User']['token'];
                                        $response['url'] = Router::url(array("plugin" => "users", "controller" => "users", "action" => "login", $this->request->data['User']['token']), false);
                                        $response['message']='Your account has been created. You should shortly receive a confirmation email. Follow the instructions in the email to activate your Delivrd account.';
                                        echo json_encode($response);
                                        die;
                                    } else {
                                        $response['status'] = false;
                                        $response['data'] = $this->User->invalidFields();
                                        //$response['message'] = 'Your account could not be created. Please, try again.';
                                        foreach ($this->User->validationErrors as $key => $value) {
                                            $msg[] = $value[0];
                                        }
                                        $response['message'] = '- '. implode('<br> - ', $msg); //$this->User->validationErrors;
                                        echo json_encode($response);
                                        die;
                                    }
                                }
                            }
                        } else {
                            $response['status'] = false;
                            $response['message'] = 'Registration not allowed for you country';
                            echo json_encode($response);
                            die;
                        }
                    }
                } else{
                    $response['status'] = false;
                    $response['message'] = 'Please confirm that you are human.';
                    echo json_encode($response);
                    die;
                }
            } else{
                 $response['status']=false;
                 $response['message']='Please fix the error.';
                 echo json_encode($response);
                 die;
            }
        exit;
        }
    }

    public function add($token = null) {

        $user = $this->User->find('first', array(
                'conditions' => array('User.token' => $token)
            ));

        if(!empty($user)){
         $this->User->id = $user['User']['id'];
         $this->User->saveField('email_verified', 1);
         $this->User->saveField('token', NULL);
         $this->Session->setFlash(__d('users', 'Your account is verify. Please login using your new account'),'default',array('class'=>'alert alert-success'));
         $this->redirect(array('action' => 'login'));

        } else {
         $this->Session->setFlash(__d('users', 'Access token is expired or invalid.'),'default',array('class'=>'alert alert-danger'));
         $this->redirect(array('action' => 'login'));

        }

    }

    public function laststep($token = null) {
        $this->layout = 'mtrl';
        $this->set('modelClass', $this->modelClass);
        $user = $this->User->find('first', array(
            'conditions' => array('User.token' => $slug)
        ));

        if(empty($user)){
        $this->Session->setFlash(__d('users', 'Access token is expired or invalid.'),'default',array('class'=>'alert alert-danger'));
        $this->redirect(array('action' => 'login'));
        }

        if (!empty($this->request->data)) {
            $this->request->data['User']['id'] = $user['User']['id'];
            $this->request->data['User']['token'] = null;
            unset($this->User->validate['username']);
            unset($this->User->validate['email']);
            if($this->User->save($this->request->data)){
                $this->request->data = $this->User->findById($user['User']['id']);

                $this->login(true);

            } else {
                $this->Session->setFlash(__d('users', 'Your account could not be created. Please, try again.'), 'default',array('class'=>'alert alert-danger'));
            }
        }
        $this->set(compact('countries', 'stores', 'user'));
    }

    public function adddcop($dcopid = null) {

    //$this->loadModel('Country');


            $this->loadModel('Network');
            $this->Network->create();
            $this->Network->set('user_id',$this->Auth->user('id'));
            $this->Network->set('dcop_user_id',$dcopid);
            $this->Network->set('status_id', 10);
            if ($this->Network->save($this->request->data)) {
               $this->Session->setFlash(__('You have requested network membership. Please wait.'));
                $this->redirect(array('action' => 'nindex'));
            } else {
                $this->Session->setFlash(__('DCOP lready added to network'));
            }

    $states = $this->User->State->find('list');
    $this->set(compact('states'));
    $countries = $this->User->Country->find('list');
    $this->set(compact('countries'));

    }

    public function approvedcop($nid = null) {

      $this->loadModel('Network');

            $this->Network->id = $nid;
            $this->Network->saveField('status_id', 11);
               $this->Session->setFlash(__('Network request approved'));
                $this->redirect(array('action' => 'fnindex'));

    $req = $this->request;
$this->set(compact('req'));

    }

    // public function firstlogin($token = null) {
    //  $user = $this->User->find('first', array(
 //            'conditions' => array('User.token' => $token)
 //        ));

 //        if(empty($user)){

    //  $this->Session->setFlash(__d('users', 'Access token is expired or invalid.'),'default',array('class'=>'alert alert-danger'));
    //  $this->redirect(array('action' => 'login'));
    //  } else {
    //      $this->Auth->login(true);
    //  }
    //  die;
    // }

/**
 * Common login action
 *
 * @return void
 */
    public function login($token = null, $autologin = false) {
        //echo $this->Auth->password('123456');die;
        $this->layout = 'mtrl';
        $Event = new CakeEvent(
            'Users.Controller.Users.beforeLogin',
            $this,
            array(
                'data' => $this->request->data,
            )
        );

        $this->getEventManager()->dispatch($Event);

        if ($Event->isStopped()) {
            return;
        }

        if ($this->request->is('post') || (!empty($token))) {


            if($autologin == true) {
                $this->User->id = $token['User']['id'];
                $this->User->saveField('token', '');
                $login = $this->Auth->login($token['User']);
                $this->Session->setFlash(__('Your email is verified.'), 'admin/success', array());
            } elseif(($autologin == false) && !empty($token)) {
                $user = $this->User->find('first', array(
                    'conditions' => array('User.token' => $token)
                ));
                if(empty($user['User'])){
                    $this->Session->setFlash(__('Access token is expired or invalid.'), 'admin/danger', array());
                    $this->redirect(array('action' => 'login'));
                } else {
                    $type = (empty($user['User']['role']) ? "fr" : ($user['User']['role'] == 'paid' ? "pd" : "tr"));
                    $this->Session->write('roletype', $type);
                    $this->Auth->login($user['User']); 
                }
            } else{
                $login = $this->Auth->login();
            }

            if ($this->Auth->login()) {
                
                $Event = new CakeEvent(
                    'Users.Controller.Users.afterLogin',
                    $this,
                    array(
                        'data' => $this->request->data,
                        'isFirstLogin' => !$this->Auth->user('last_login')
                    )
                );

                $this->getEventManager()->dispatch($Event);

                $this->{$this->modelClass}->id = $this->Auth->user('id');
                $this->{$this->modelClass}->saveField('last_login', date('Y-m-d H:i:s'));


                if ($this->here == $this->Auth->loginRedirect) {
                    $this->Auth->loginRedirect = '/';
                }
                /*$this->loadModel('User');
                $product_count = $this->User->Product->find('count', ['conditions' => array('Product.user_id' => $this->Auth->user('id'))]);
                $this->Session->write('product_count', $product_count);*/

                $this->Session->write('client', $this->Auth->user('client'));
                // Did user set settings?
                $this->Session->write('settingson', $this->Auth->user('settingson'));
                //Set default warehouse
                $this->loadModel('Warehouse');
                $defaultwarehouse = $this->Warehouse->find('first', array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id')), 'recursive' => -1));
                //if default wareouse for user does not exist, create it
                if($defaultwarehouse == null)
                {
                    $warehousedata = array(
                        'Warehouse' => array(
                            'name' => 'Default',
                            'lat' => 1.111,
                            'long' => 1.11,
                            'user_id' => $this->Auth->user('id')
                        )
                    );
                    $this->Warehouse->create();
                    // save the data
                    $returned_warehouse = $this->Warehouse->save($warehousedata);
                    $default_warehouse = $returned_warehouse['Warehouse']['id'];
                } else {
                    $default_warehouse = $defaultwarehouse['Warehouse']['id'];
                }
                $this->Session->write('default_warehouse', $default_warehouse);

                if ($this->Auth->user('msystem_id') == 1)
                {
                    $this->Session->write('weight_unit', Configure::read('Metric.weight'));
                    $this->Session->write('volume_unit', Configure::read('Metric.volume'));
                }
                if ($this->Auth->user('msystem_id') == 2)
                {
                    $this->Session->write('weight_unit', Configure::read('US.weight'));
                    $this->Session->write('volume_unit', Configure::read('US.volume'));
                }
                $this->copyShortcuts();
                $this->loadModel('Currency');
                $cid = $this->Auth->user('currency_id');
                if(isset($cid)) {
                    $currencies = $this->Currency->find('first', array('conditions' => array('id' => $this->Auth->user('currency_id')), 'recursive' => -1));
                    
                    $this->Session->write('currencyname', $currencies['Currency']['name']);
                    $this->Session->write('currencysym', $currencies['Currency']['csymb']);
                } else {
                    $this->Session->write('currencyname', 'USD');
                    $this->Session->write('currencysym', '$');  
                }
            
                $this->Session->write('showvariants', $this->Auth->user('showvariants'));
                $this->Session->write('verifyweight', $this->Auth->user('verifyweight'));
                $this->Session->write('autopacking', $this->Auth->user('autopacking'));
                $this->Session->write('autoproducts', $this->Auth->user('autoproducts'));
                $this->Session->write('showtours', $this->Auth->user('showtours'));
                $this->Session->write('locationsactive', $this->Auth->user('locationsactive'));
                $this->Session->write('magentousername', $this->Auth->user('magentousername'));
                $this->Session->write('magentopassword', $this->Auth->user('magentopassword'));
                $this->Session->write('paid', $this->Auth->user('paid'));
                $this->Session->write('inventoryauto', $this->Auth->user('inventoryauto'));
                $this->Session->write('inventoryremarks', $this->Auth->user('inventoryremarks'));
                $this->Session->write('copypdtprice', $this->Auth->user('copypdtprice'));
                $this->Session->write('zeroquantity', $this->Auth->user('zeroquantity'));
                $this->Session->write('packinslip_desc', $this->Auth->user('packinslip_desc'));
                $this->Session->write('default_country_id', $this->Auth->user('default_country_id'));
                $this->Session->write('fast_invalert', $this->Auth->user('fast_invalert'));

                $this->loadModel('Product');
                $this->Product->recursive = -1;
                $productcount = $this->Product->find('count', array('conditions' => ['Product.user_id' => $this->Auth->user('id'), 'Product.status_id NOT IN' => [12, 13], 'Product.deleted' =>0]));
                $this->Session->write('productcount', $productcount);
                
                $this->Session->write('inventoryalert', $this->Auth->user('inventoryalert'));
                $this->Session->write('sales_title', strip_tags($this->Auth->user('sales_title')));


                // New Subscription
                // Check if user have trial but not have subscription (with new subscription system)
                if($this->Auth->user('role') != 'paid') {
                    if($productcount > 10 || $this->Auth->user('locationsactive') || $this->Auth->user('paid')) {
                        $this->loadModel('Subscription');
                        $subscription = $this->Subscription->find('first', ['conditions' => array('Subscription.user_id' => $this->Auth->user('id'))]);
                        if(!$subscription) {
                            $this->User->id = $this->Auth->user('id');
                            $this->User->saveField('role', 'trial');

                            $today = strtotime(date('Y-m-d'));
                            $created = strtotime(date('Y-m-d', strtotime($this->Auth->user('created'))));
                            $stay_duration = abs(30 - round(($today - $created)/(3600*24)));
                            $stay_duration = min($stay_duration, 30);

                            $subscription['Subscription']['ext_id'] = 'LOC-TRIAL';
                            $subscription['Subscription']['user_id'] = $this->Auth->user('id');
                            $subscription['Subscription']['amount'] = 0.00;
                            $subscription['Subscription']['payer_email'] = '';
                            $subscription['Subscription']['expiry_date'] = date('Y-m-d', strtotime('+'. $stay_duration .' days'));
                            $subscription['Subscription']['status'] = 'Trial';
                            $subscription['Subscription']['created'] = date('Y-m-d H:i:s');
                            $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');
                            $subscription['Subscription']['memo'] = 'Strart trial signin';
                            $this->Subscription->save($subscription);
                        }
                    }
                }
                // End New Subscription
                

                // low_alerts
                $low_alerts = $this->User->Product->find('count', ['conditions'=>['Product.value >' => 0, 'Product.user_id' => $this->Auth->user('id')]]);
                $this->Session->write('low_alerts', $low_alerts);
                
                // invited
                $this->loadModel('NetworksInvite');
                $invited = $this->NetworksInvite->find('count', ['contain'=>['Network'], 'conditions'=>['Network.created_by_user_id' => $this->Auth->user('id')]]);
                $this->Session->write('invited', $invited);

                if($this->Auth->user('email') == "admin@eeldeliverysolutions.com" || $this->Auth->user('is_admin') == 1) {
                    $this->Session->write('is_admin', 1);
                } else {
                    $this->Session->write('is_admin', 3);
                }

                $this->loadModel('Integration');
                $this->Integration->recursive = -1;
                $integr = $this->Integration->find('first', array('conditions' => array('Integration.user_id' => $this->Auth->user('id')), 'fields' => array('Integration.backend')));
                if($integr) {
                    $this->Session->write('integration', strtolower(substr($integr['Integration']['backend'], 0 , 2)));
                }

                $mgintegration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Magento','Integration.user_id' => $this->Auth->user('id'))));
                if($mgintegration != null) {
                    $mgun = $mgintegration['Integration']['username'];
                    $mgps = $mgintegration['Integration']['password'];
                    $mgul = $mgintegration['Integration']['url'];

                    if( $mgun != null && $mgul != null ) {
                        $this->Session->write('magento', 1);
                    }
                }

                $woointegration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Woocommerce','Integration.user_id' => $this->Auth->user('id'))));
                if($woointegration != null) {
                    $woun = $woointegration['Integration']['username'];
                    $wops = $woointegration['Integration']['password'];
                    $woul = $woointegration['Integration']['url'];

                    if( $woun != null && $wops != null && $woul != null ) {
                        $this->Session->write('woo', 1);
                    }
                }

                $this->Session->write('managedamaged', $this->Auth->user('managedamaged'));

                //Calculate expired user
                //Trial is 30 days
                $triallength = 30;
                //Get features usage
                //Default - free basic
                $planstatus = 0;
                if($this->Auth->user('locationsactive') == 1 || $this->Auth->user('paid') == 1 )
                   $planstatus = 0;

                $this->Session->write('planstatus', $planstatus);

                $this->_authUser = $this->User->getAuthUser($this->Auth->user('id'));
                
                /*if($this->_authUser['User']['role'] == 'trial') {
                    $remaining_days = $this->Account->getRemainingDays($this->_authUser['Subscription']['expiry_date']);
                    //https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WEYVWFC35BA84
                    if($remaining_days <= 5 && !($remaining_days <= 0)) {
                        $expirytext = "Your trial will expire in ".$remaining_days." days. <BR />To continue managing your inventory with Delivrd, please subscribe: <BR />
                            <a href='". Router::url(['plugin' => false, 'controller' => 'subscriptions', 'action' => 'signin']) ."' taraget='' class='btn' style='margin: 8px 0px 10px 2px;border-radius: 6px;color: #fff;background-color: #f19071;border-color: #f19071;'>SUBSCRIBE TO DELIVRD</a>";

                        $this->Session->write('expirytext', $expirytext);
                        $this->Session->write('close', false);
                    } elseif($remaining_days <= 0) {
                        $expirytext = "Your trial has expired.<BR />To continue managing your inventory with Delivrd, please subscribe: <BR />
                            <a href='". Router::url(['plugin' => false, 'controller' => 'subscriptions', 'action' => 'signin']) ."' taraget='' class='btn' style='margin: 8px 0px 10px 2px;border-radius: 6px;color: #fff;background-color: #f19071;border-color: #f19071;'>SUBSCRIBE TO DELIVRD</a> <BR />
                            If you have any questions, please <a href='https://delivrd.freshdesk.com/support/tickets/new' taraget='_blank' >contact our support</a>";

                        $this->Session->write('expirytext', $expirytext);
                        $this->Session->write('close', true);
                    }
                } */

                if (!empty($this->request->data)) {
                    $data = $this->request->data[$this->modelClass];
                    if (empty($this->request->data[$this->modelClass]['remember_me'])) {
                        $this->RememberMe->destroyCookie();
                    } else {
                        $this->_setCookie();
                    }
                }

                if (empty($data[$this->modelClass]['return_to'])) {
                    $data[$this->modelClass]['return_to'] = null;
                }

                // Checking for 2.3 but keeping a fallback for older versions
                if (method_exists($this->Auth, 'redirectUrl')) {
                    $this->redirect($this->Auth->redirectUrl($data[$this->modelClass]['return_to']));
                } else {
                    $this->redirect($this->Auth->redirect($data[$this->modelClass]['return_to']));
                }
            } else {
                $this->Auth->flash(__d('users', 'Invalid e-mail / password combination.  Please try again'),'default',array('class'=>'alert alert-warning'));
            }
        }
        if (isset($this->request->params['named']['return_to'])) {
            $this->set('return_to', urldecode($this->request->params['named']['return_to']));
        } else {
            $this->set('return_to', false);
        }
        $allowRegistration = Configure::read('Users.allowRegistration');
        $this->set('allowRegistration', (is_null($allowRegistration) ? true : $allowRegistration));
    }

    public function copyShortcuts() {
        $this->loadModel('ShortcutLink');
        $list =  $this->ShortcutLink->find('all', array());
        $this->loadModel('UserShortcutLink');
        $checkList = $this->UserShortcutLink->getShortList($this->Auth->user('id'));
        $this->Session->write('shortcut', $checkList);
        if(empty($checkList)) {
            foreach($list as $savedata) {
                $data = array(
                        'UserShortcutLink' => array(
                            'user_id' => $this->Auth->user('id'),
                            'name' => $savedata['ShortcutLink']['name'],
                            'url' => $savedata['ShortcutLink']['url']
                        )
                    );
                $this->UserShortcutLink->create();
                $this->UserShortcutLink->save($data);

            }
            $shortcutList = $this->UserShortcutLink->getShortList($this->Auth->user('id'));
            $this->Session->write('shortcut', $shortcutList);
        }

    }

    /**
     * Search - Requires the CakeDC Search plugin to work
     *
     * @throws MissingPluginException
     * @return void
     * @link https://github.com/CakeDC/search
     */
    public function search() {
        $this->_pluginLoaded('Search');

        $searchTerm = '';
        $this->Prg->commonProcess($this->modelClass);

        $by = null;
        if (!empty($this->request->params['named']['search'])) {
            $searchTerm = $this->request->params['named']['search'];
            $by = 'any';
        }
        if (!empty($this->request->params['named']['username'])) {
            $searchTerm = $this->request->params['named']['username'];
            $by = 'username';
        }
        if (!empty($this->request->params['named']['email'])) {
            $searchTerm = $this->request->params['named']['email'];
            $by = 'email';
        }
        $this->request->data[$this->modelClass]['search'] = $searchTerm;

        $this->Paginator->settings = array(
            'search',
            'limit' => 12,
            'by' => $by,
            'search' => $searchTerm,
            'conditions' => array(
                    'AND' => array(
                        $this->modelClass . '.active' => 1,
                        $this->modelClass . '.email_verified' => 1)));

        $this->set('users', $this->Paginator->paginate($this->modelClass));
        $this->set('searchTerm', $searchTerm);
    }

    /**
     * Common logout action
     *
     * @return void
     */
    public function logout() {
        $user = $this->Auth->user();
        $this->Session->destroy();
        if (isset($_COOKIE[$this->Cookie->name])) {
        $this->Cookie->destroy();
        }
        $this->RememberMe->destroyCookie();
        $this->Session->setFlash(sprintf(__d('users', '%s You have successfully logged out'), $user[$this->{$this->modelClass}->displayField]),'default',array('class'=>'alert alert-info'));
        $this->redirect($this->Auth->logout());
    }

    /**
     * Checks if an email is already verified and if not renews the expiration time
     *
     * @return void
     */
    public function resend_verification() {
        if ($this->request->is('post')) {
            try {
                if ($this->{$this->modelClass}->checkEmailVerification($this->request->data)) {
                    $this->_sendVerificationEmail($this->{$this->modelClass}->data);
                    $this->Session->setFlash(__d('users', 'The email was resent. Please check your inbox.'),'default',array('class'=>'alert alert-info'));
                    $this->redirect('login');
                } else {
                    $this->Session->setFlash(__d('users', 'The email could not be sent. Please check errors.'),'default',array('class'=>'alert alert-warning'));
                }
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage());
            }
        }
    }

    /**
     * Confirm email action
     *
     * @param string $type Type, deprecated, will be removed. Its just still there for a smooth transistion.
     * @param string $token Token
     * @return void
     */
    public function verify($type = 'email', $token = null) {
        if ($type == 'reset') {
            // Backward compatiblity
            $this->request_new_password($token);
        }

        try {
            $this->{$this->modelClass}->verifyEmail($token);
            $this->Session->setFlash(__d('users', 'Your e-mail has been validated!'));
            return $this->redirect(array('action' => 'login'));
        } catch (RuntimeException $e) {
            $this->Session->setFlash($e->getMessage());
            return $this->redirect('/');
        }
    }

    /**
     * This method will send a new password to the user
     *
     * @param string $token Token
     * @throws NotFoundException
     * @return void
     */
    public function request_new_password($token = null) {
        if (Configure::read('Users.sendPassword') !== true) {
            throw new NotFoundException();
        }

        $data = $this->{$this->modelClass}->verifyEmail($token);

        if (!$data) {
            $this->Session->setFlash(__d('users', 'The url you accessed is not longer valid'));
            return $this->redirect('/');
        }

        if ($this->{$this->modelClass}->save($data, array('validate' => false))) {
            $this->_sendNewPassword($data);
            $this->Session->setFlash(__d('users', 'Your password was sent to your registered email account'));
            $this->redirect(array('action' => 'login'));
        }

        $this->Session->setFlash(__d('users', 'There was an error verifying your account. Please check the email you were sent, and retry the verification link.'));
        $this->redirect('/');
    }

    /**
     * Sends the password reset email
     *
     * @param array
     * @return void
     */
    protected function _sendNewPassword($userData) {
        $Email = $this->_getMailInstance();
        $Email->from(Configure::read('App.defaultEmail'))
            ->to($userData[$this->modelClass]['email'])
            ->replyTo(Configure::read('App.defaultEmail'))
            ->return(Configure::read('App.defaultEmail'))
            ->subject(env('HTTP_HOST') . ' ' . __d('users', 'Password Reset'))
            ->template($this->_pluginDot() . 'new_password')
            ->viewVars(array(
                'model' => $this->modelClass,
                'userData' => $userData))
            ->send();
    }

    /**
     * Allows the user to enter a new password, it needs to be confirmed by entering the old password
     *
     * @return void
     */
    public function change_password() {
        if ($this->request->is('post')) {
            $this->request->data[$this->modelClass]['id'] = $this->Auth->user('id');
            if ($this->{$this->modelClass}->changePassword($this->request->data)) {
                $this->Session->setFlash(__d('users', 'Password changed.'));
                // we don't want to keep the cookie with the old password around
                $this->RememberMe->destroyCookie();
                $this->redirect('/');
            }
        }
    }

    /**
     * Reset Password Action
     *
     * Handles the trigger of the reset, also takes the token, validates it and let the user enter
     * a new password.
     *
     * @param string $token Token
     * @param string $user User Data
     * @return void
     */
    public function reset_password($token = null, $user = null) {
        $this->layout = 'mtrl';
        if (empty($token)) {
            $admin = false;
            if ($user) {
                $this->request->data = $user;
                $admin = true;
            }
            $this->_sendPasswordReset($admin);
        } else {
            $user = $this->{$this->modelClass}->checkPasswordToken($token);
            if (empty($user)) {
                $this->Session->setFlash(__d('users', 'Invalid password reset token.'));
                $this->redirect(array('action' => 'reset_password'));
            }

            if (!empty($this->request->data)) {
                $this->request->data['User']['id'] = $user['User']['id'];
                $this->request->data['User']['password_token'] = NULL;
                $this->request->data['User']['password'] = $this->request->data['User']['new_password'];
                unset($this->User->validate['username'], $this->User->validate['email'], $this->User->validate['tos']);

                if($this->User->save($this->request->data)){
                    $this->Session->setFlash(__d('users', 'Password changed, you can now login with your new password.'));
                    $this->redirect(array('action' => 'login'));
                } else {
                    $this->Session->setFlash(__d('users', 'Invalid Password. Please, try again.'), 'default',array('class'=>'alert alert-danger'));
                }
            }
        }

        $this->set('token', $token);

    }

    /**
     * Sets a list of languages to the view which can be used in selects
     *
     * @deprecated No fallback provided, use the Utils plugin in your app directly
     * @param string $viewVar View variable name, default is languages
     * @throws MissingPluginException
     * @return void
     * @link https://github.com/CakeDC/utils
     */
    protected function _setLanguages($viewVar = 'languages') {
        $this->_pluginLoaded('Utils');
        $Languages = new Languages();
        $this->set($viewVar, $Languages->lists('locale'));
    }

    /**
     * Sends the verification email
     *
     * This method is protected and not private so that classes that inherit this
     * controller can override this method to change the varification mail sending
     * in any possible way.
     *
     * @param string $to Receiver email address
     * @param array $options EmailComponent options
     * @return void
     */
    protected function _sendVerificationEmail($userData, $options = array()) {
        $defaults = array(
            'from' => Configure::read('App.defaultEmail'),
            'subject' => __d('users', 'Account verification'),
            'template' => $this->_pluginDot() . 'account_verification',
            'layout' => 'default',
            'emailFormat' => CakeEmail::MESSAGE_TEXT
        );

        $options = array_merge($defaults, $options);

        $Email = $this->_getMailInstance();
        $Email->to($userData[$this->modelClass]['email'])
            ->from($options['from'])
            ->emailFormat($options['emailFormat'])
            ->subject($options['subject'])
            ->template($options['template'], $options['layout'])
            ->viewVars(array(
            'model' => $this->modelClass,
                'user' => $userData
            ))
            ->send();
    }

    /**
     * Checks if the email is in the system and authenticated, if yes create the token
     * save it and send the user an email
     *
     * @param boolean $admin Admin boolean
     * @param array $options Options
     * @return void
     */
    protected function _sendPasswordReset($admin = null, $options = array()) {
        $defaults = array(
            'from' => Configure::read('App.defaultEmail'),
            'subject' => __d('users', 'Password Reset'),
            'template' => $this->_pluginDot() . 'password_reset_request',
            'emailFormat' => CakeEmail::MESSAGE_TEXT,
            'layout' => 'default'
        );

        $options = array_merge($defaults, $options);

        if (!empty($this->request->data)) {
            $user = $this->{$this->modelClass}->passwordReset($this->request->data);

            if (!empty($user)) {
                $Email = $this->_getMailInstance();
                $Email->to($user[$this->modelClass]['email'])
                    ->from($options['from'])
                    ->emailFormat($options['emailFormat'])
                    ->subject($options['subject'])
                    ->template($options['template'], $options['layout'])
                    ->viewVars(array(
                    'model' => $this->modelClass,
                    'user' => $this->{$this->modelClass}->data,
                        'token' => $this->{$this->modelClass}->data[$this->modelClass]['password_token']))
                    ->send();

                if ($admin) {
                    $this->Session->setFlash(sprintf(
                        __d('users', '%s has been sent an email with instruction to reset their password.'),
                        $user[$this->modelClass]['email']));
                    $this->redirect(array('action' => 'index', 'admin' => true));
                } else {
                    $this->Session->setFlash(__d('users', 'You should receive an email with further instructions shortly'));
                    $this->redirect(array('action' => 'login'));
                }
            } else {
                $this->Session->setFlash(__d('users', 'No user was found with that email.'));
                $this->redirect($this->referer('/'));
            }
        }
        $this->render('request_password_change');
    }

    /**
     * Sets the cookie to remember the user
     *
     * @param array RememberMe (Cookie) component properties as array, like array('domain' => 'yourdomain.com')
     * @param string Cookie data keyname for the userdata, its default is "User". This is set to User and NOT using the model alias to make sure it works with different apps with different user models across different (sub)domains.
     * @return void
     * @link http://book.cakephp.org/2.0/en/core-libraries/components/cookie.html
     */
    protected function _setCookie($options = array(), $cookieKey = 'rememberMe') {
        $this->RememberMe->settings['cookieKey'] = $cookieKey;
        $this->RememberMe->configureCookie($options);
        $this->RememberMe->setCookie();
    }

    /**
     * This method allows the user to change his password if the reset token is correct
     *
     * @param string $token Token
     * @return void
     */
    protected function _resetPassword($token) {
        $user = $this->{$this->modelClass}->checkPasswordToken($token);
        if (empty($user)) {
            $this->Session->setFlash(__d('users', 'Invalid password reset token.'));
            $this->redirect(array('action' => 'reset_password'));
        }

        if (!empty($this->request->data)) {
            $this->request->data['User']['token'] = NULL;
            $this->request->data['User']['password'] = $this->request->data['User']['new_password'];
            if($this->User->save($this->request->data)){  pr($this->request->data);die;
                $this->Session->setFlash(__d('users', 'Password changed, you can now login with your new password.'));
                $this->redirect($this->Auth->loginAction);
            } else {

            }

        }

        $this->set('token', $token);
    }

    /**
     * Returns a CakeEmail object
     *
     * @return object CakeEmail instance
     * @link http://book.cakephp.org/2.0/en/core-utility-libraries/email.html
     */
    protected function _getMailInstance() {
        $emailConfig = Configure::read('Users.emailConfig');
        if ($emailConfig) {
            return new CakeEmail($emailConfig);
        } else {
            return new CakeEmail('default');
        }
    }

    /**
     * Default isAuthorized method
     *
     * This is called to see if a user (when logged in) is able to access an action
     *
     * @param array $user
     * @return boolean True if allowed
     * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#using-controllerauthorize
     */
    public function isAuthorized($user = null) {
        return parent::isAuthorized($user);
    }

    public function setparams($param = null)
    {
        if($param == 'sales_title') {
            $this->User->saveField($param, strip_tags($this->request->data['User'][$param], '<script>'));
            $this->Session->write($param, strip_tags($this->request->data['User'][$param]));
        } else {
            $this->User->saveField($param, $this->request->data['User'][$param]);
            $this->Session->write($param, htmlspecialchars($this->request->data['User'][$param]));
            if($param == 'currency_id')
                   $this->setcurrency($this->request->data['User'][$param]);
        }

    }

    public function setcurrency($currency_id = null)
    {
        $this->loadModel('Currency');
        $cid = $this->Auth->user('currency_id');
        if(isset($cid))
        {
            $currencies = $this->Currency->find('first', array('conditions' => array('id' => $currency_id)));
            $this->Session->write('currencyname', $currencies['Currency']['name']);
            $this->Session->write('currencysym', $currencies['Currency']['csymb']);
        } else {
            $this->Session->write('currencyname', 'USD');
            $this->Session->write('currencysym', '$');
        }
    }


    

}
