<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Networks Controller
 *
 * @property Network $Network
 * @property PaginatorComponent $Paginator
 */
class NetworksController extends AppController {

    public $theme = 'Mtro';

    public $helpers = array('Networks.Network');
    
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator','Session', 'Access');

    public $uses = array('Network', 'NetworksUser', 'NetworksInvite', 'NetworksAccess', 'Access', 'User', 'Warehouse');

    /**
     * If the controller is a plugin controller set the plugin name
     *
     * @var mixed
     */
    public $plugin = 'Networks';


    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('signup');
    }

    /**
     * List of my network
     */
    public function index() {
        $this->layout = 'mtrd';

        $networks = $this->Network->find('all', array(
            'contain' => array( 'CreatedByUser', 'AdminUser'),
            'fields' => array('Network.*', 'CreatedByUser.username', 'CreatedByUser.email', 'AdminUser.username', 'AdminUser.email'),
            'conditions'=>array('Network.created_by_user_id' => $this->Auth->user('id'), 'Network.status' => 1)
        ));
        
        $this->set(compact('networks'));
    }

    /**
     * Details of my network
     */
    public function details($id) {
        $this->layout = 'mtrd';

        $this->Network->recursive = 1;
        $network = $this->Network->find('first', array(
            'contain' => array(
                'NetworksInvite'=>array('conditions'=>array('NetworksInvite.status !=' => 2), 'order'=>'NetworksInvite.updated DESC', 'limit' => 10),
                'CreatedByUser',
                'AdminUser'
            ),
            'fields' => array('Network.*', 'CreatedByUser.username', 'CreatedByUser.email', 'AdminUser.username', 'AdminUser.email'),
            'conditions'=>array('Network.created_by_user_id' => $this->Auth->user('id'), 'Network.id' => $id)
        ));

        $this->loadModel('Schannel');
        $channels = $this->Schannel->find('list', ['conditions' => array('Schannel.user_id' => $this->Auth->user('id'))]);

        if(!$network) {
            $this->Session->setFlash(__('You have no own network. Please create it.'),'admin/danger');
            $this->redirect(array('controller'=>'networks', 'action'=>'create'));
        }
        
        $this->Paginator->settings = array(
            'limit' => 10,
            'contain' => array('User')
            
        );
        $users = $this->Paginator->paginate('NetworksUser', array('NetworksUser.network_id' => $network['Network']['id']));
        $roles = $this->Network->getRoles();
        
        $this->set(compact('network', 'users', 'roles', 'channels'));
    }

    /**
     * Each user can create only one network(???)
     */
    public function create() {
        if($this->request->is(array('post', 'put'))) {
            $this->request->data['Network']['created_by_user_id'] = $this->Auth->user('id');
            $this->request->data['Network']['admin_user_id'] = $this->Auth->user('id');
            $this->request->data['Network']['validity_start'] = date('Y-m-d H:i:s');
            $this->request->data['Network']['validity_end'] = date('Y-m-d H:i:s');
            $this->request->data['Network']['status'] = 1;
            if($this->Network->save($this->request->data)){
                $this->Session->setFlash(__('Network successfuly created.'),'admin/success');
                $this->redirect(array('controller'=>'networks', 'action'=>'index'));
            } else {
                $this->Session->setFlash(__('Network could not be saved. Please check errors and try again.'),'admin/danger');
            }
        }
    }

    /**
     * Edit own network
     *
     */
    public function edit($id) {
        $network = $this->Network->find('first', array('conditions'=>array('Network.created_by_user_id' => $this->Auth->user('id'), 'Network.id' => $id)));
        if(!$network) {
            throw new NotFoundException(__('Order not found'));
        }

        if($this->request->is(array('post', 'put'))) {
            if($this->Network->save($this->request->data)){
                $this->Session->setFlash(__('Network successfuly updated.'),'admin/success');
                $this->redirect(array('controller'=>'networks', 'action'=>'index'));
            } else {
                $this->Session->setFlash(__('Network could not be saved. Please check errors and try again.'),'admin/danger');
            }
        } else {
            $this->request->data = $network;
        }
        $this->set(compact('network'));
    }

    /**
     * Deactivate own network
     *
     */
    public function delete($id) {
        $network = $this->Network->find('first', array('conditions'=>array('Network.created_by_user_id' => $this->Auth->user('id'), 'Network.id' => $id)));
        if(!$network) {
            throw new NotFoundException(__('Order not found'));
        }

        if($this->request->is(array('post', 'put'))) {
            $network['Network']['status'] = 0;
            if($this->Network->save($network)){
                $this->Session->setFlash(__('Network successfuly deleted.'),'admin/success');
                $this->redirect(array('controller'=>'networks', 'action'=>'index'));
            } else {
                $this->Session->setFlash(__('Network could not be deleted. Please try again.'),'admin/danger');
            }
        } else {
            $this->request->data = $network;
        }
        $this->set(compact('network'));
    }

    /**
     * Signup to network (link on this method we send in email)
     *
     */
    public function signup($hash) {
        $this->layout = 'mtrl';
        if($this->Auth->user('id')) {
            return $this->redirect('/');
        }

        $invitation = $this->NetworksInvite->find('first', array('conditions' => array('NetworksInvite.hash' => $hash)));
        if($invitation) {
            // Check is user exists
            $user = $this->User->find('first', ['conditions' => array('email' => $invitation['NetworksInvite']['email']), 'fields' => array('User.id')]);
            if($user) {
                $data['NetworksInvite']['user_id'] = $invitation['NetworksInvite']['network_id'];
                $data['Network']['dcop_user_id'] = $user['User']['id'];
                $data['Network']['status_id'] = 1;
                if($this->Network->save($data)) {
                    return $this->redirect(array('controller'=>'networks','action'=>'view', $this->Network->id));
                }
            } else { //generate user
                return $this->redirect(array('plugin'=>false, 'controller'=>'users', 'action' =>'signup', $hash));
            }
        } else {
            throw new NotFoundException(__('Invitation not valid'));
        }
    }

    /**
     * Display all third user networks
     */
    public function lists() {
        $networks = $this->NetworksUser->find('all', array(
            'conditions' => array('NetworksUser.user_id' => $this->Auth->user('id')),
            'contain' => array('Network'),
        ));
        /*if(count($networks) == 1) {
            return $this->redirect(['controller' => 'networks', 'action'=>'view', $networks[0]['Network']['id']]);
        }*/
        $roles = $this->Network->getRoles();
        $this->set(compact('networks','roles'));
    }

    /**
     * Display network for network user
     */
    public function view($id) {
        if($this->Network->exists($id)) {
            $network = $this->NetworksUser->find('first', array(
                'conditions'=>array('NetworksUser.network_id' => $id, 'NetworksUser.user_id' => $this->Auth->user('id')),
                'contain' => array('Network'),
            ));
            if($network) {
                $access = $this->NetworksAccess->find('all', [
                    'conditions' => [
                        'NetworksAccess.network_id' => $network['NetworksUser']['network_id'],
                        'NetworksAccess.user_id' => $network['NetworksUser']['user_id']
                    ],
                    'contain' => ['Warehouse'],
                    //'fields' => ['NetworksAccess.*', 'GROUP_CONCAT(Warehouse.name ORDER BY Warehouse.name ASC SEPARATOR ", ") as warehouse', 'GROUP_CONCAT(DISTINCT NetworksAccess.access ORDER BY NetworksAccess.access ASC SEPARATOR "") as access'],
                    //'group' => 'CONCAT (NetworksAccess.model )'
                ]);
                // Show access details and information about network
                $this->set(compact('network', 'access'));
            } else {
                // User not in network
                //throw new MethodNotAllowedException(__('You have no access to this network'));
                $this->Session->setFlash(__('Access to network not found.'),'admin/success');
                return $this->redirect(array('controller' => 'networks', 'action' => 'lists'));
            }
        } else {
            throw new NotFoundException(__('Network not found'));
        }
    }

    /**
     * Leave network for network user
     */
    public function leave($id) {
        if(!$this->Network->exists($id)) {
            throw new NotFoundException(__('Network not found'));
        }
        
        $this->NetworksAccess->deleteAll(['NetworksAccess.network_id' => $id, 'NetworksAccess.user_id' => $this->Auth->user('id')], false);
        $this->NetworksInvite->deleteAll(['NetworksInvite.network_id' => $id, 'NetworksInvite.email' => $this->Auth->user('email')], false);
        $this->NetworksUser->deleteAll(['network_id' => $id, 'user_id' => $this->Auth->user('id')], false);
        //$this->NetworksUser->updateAll(array('status' => 2), array('network_id' => $id, 'user_id' => $this->Auth->user('id')));

        $this->Session->setFlash(__('Access to network stopped.'),'admin/success');
        return $this->redirect(array('controller' => 'networks', 'action' => 'lists'));
    }

    public function invite($id) {
        $this->layout = false;
        $this->loadModel('Warehouse');
        $warehouse = $this->Warehouse->find('list', array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'))));

        $network = $this->Network->find('first', array('conditions' => array('Network.admin_user_id' => $this->Auth->user('id'), 'Network.id' => $id)));

        if ($this->request->is(array('post', 'put'))) {
            if($this->request->data['NetworksInvite']['email'] == $this->Auth->user('email')) {
                if ($this->request->is('ajax')) {
                    $response = array('action' => 'error', 'msg' => 'You can\'t invite himself to own network!');
                    $this->set('_serialize', 'response');
                    $json = json_encode($response);
                    echo $json;
                    exit;
                } else {
                    $this->Session->setFlash(__('You can\'t invite himself to own network!'),'admin/danger');
                    return $this->redirect(array('controller' => 'networks', 'action' => 'index'));
                }
            }
            // is user already invited?
            $inv = $this->NetworksInvite->find('first', array(
                'conditions'=>array(
                    'NetworksInvite.email' => $this->request->data['NetworksInvite']['email'],
                    'NetworksInvite.network_id' => $network['Network']['id']
                )
            ));
            if($inv) {
                if($inv['NetworksInvite']['status'] == 2) { //User already accept invitation
                    if ($this->request->is('ajax')) {
                        $response = array('action' => 'success', 'msg' => 'User with this email already in your network.');
                        $this->set('_serialize', 'response');
                        $json = json_encode($response);
                        echo $json;
                        exit;
                    } else {
                        $this->Session->setFlash(__('User with this email already in your network.'),'admin/success');
                        return $this->redirect(array('controller' => 'networks', 'action' => 'details', $id));
                    }
                } else { // re-send invitations update status
                    $this->request->data['NetworksInvite']['id'] = $inv['NetworksInvite']['id'];
                    $this->request->data['NetworksInvite']['status'] = 4;
                    $this->request->data['NetworksInvite']['hash'] = $inv['NetworksInvite']['hash'];
                }
                
            } else {
                $this->request->data['NetworksInvite']['hash'] = md5($this->Auth->user('id') . time());
            }
            
            $wids = array_keys($warehouse);
            $this->request->data['NetworksInvite']['warehouse'] = json_encode($wids);
            if(isset($this->request->data['NetworksInvite']['warehouse_id']) && $this->request->data['NetworksInvite']['warehouse_id'] > 0) {
                if(isset($this->request->data['NetworksInvite']['warehouse_id'][0]) && $this->request->data['NetworksInvite']['warehouse_id'][0]) {
                    $this->request->data['NetworksInvite']['warehouse'] = json_encode($this->request->data['NetworksInvite']['warehouse_id']);
                }
            }

            $this->request->data['NetworksInvite']['products'] = 'all';
            if(isset($this->request->data['NetworksInvite']['product_id']) && count($this->request->data['NetworksInvite']['product_id']) > 0) {
                if(isset($this->request->data['NetworksInvite']['product_id'][0]) && $this->request->data['NetworksInvite']['product_id'][0]){
                    $this->request->data['NetworksInvite']['products'] = json_encode($this->request->data['NetworksInvite']['product_id']);
                }
            }

            $this->request->data['NetworksInvite']['schannel'] = 'all';
            if(isset($this->request->data['NetworksInvite']['schannel_id']) && count($this->request->data['NetworksInvite']['schannel_id']) > 0) {
                if(isset($this->request->data['NetworksInvite']['schannel_id'][0]) && $this->request->data['NetworksInvite']['schannel_id'][0]){
                    $this->request->data['NetworksInvite']['schannel'] = json_encode($this->request->data['NetworksInvite']['schannel_id']);
                }
            }
            
            if($this->NetworksInvite->save($this->request->data)) {
                $this->_sendInvitationEmail($this->request->data['NetworksInvite']['email'], $this->request->data['NetworksInvite']['hash'], $network);
                
                if ($this->request->is('ajax')) {
                    $response = array('action' => 'success', 'msg' => 'Invitation successfuly send');
                    $this->set('_serialize', 'response');
                    $json = json_encode($response);
                    echo $json;
                    exit;
                } else {
                    $this->Session->setFlash(__('Your invitation successfuly sent.'),'admin/success');
                    return $this->redirect(array('controller' => 'networks', 'action' => 'details', $id));
                }
            } else {
                $response = array('action' => 'error', 'msg' => 'Please check error');
                $response['errors'] = $this->Invitation->validationErrors;
                $this->set('_serialize', 'response');
                $json = json_encode($response);
                $this->response->body($json);
            }
        }

        

        $this->loadModel('Product');
        $products = $this->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.status_id' => 1)));

        $this->loadModel('Schannel');
        $schannels = $this->Schannel->find('list', array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));

        $roles = $this->Network->getRoles();
        $this->set(compact('warehouse', 'roles', 'network', 'products', 'schannels'));
    }

    /**
     * Sends the invitation email
     *
     * This method is protected and not private so that classes that inherit this
     * controller can override this method to change the varification mail sending
     * in any possible way.
     *
     * @param string $to Receiver email address
     * @param array $options EmailComponent options
     * @return void
     */
    protected function _sendInvitationEmail($to, $hash, $network) {
        $Email = new CakeEmail('mandrill');
        #$Email = new CakeEmail('default');
        $Email->to($to)
            ->emailFormat('html')
            ->subject(__d('users', 'You have been invited to join '. $this->Auth->user('email') .' inventory network'))
            ->template($this->plugin .'.network_invitation', 'default')
            ->viewVars(array(
                'hash' => $hash,
                'network' => $network,
            ))
            ->send();
    }

    public function getinvites() {
        if (!empty($this->request->params['requested'])) {
            $invites = $this->NetworksInvite->find('first', array(
                'conditions' => array('NetworksInvite.email' => $this->Auth->user('email'), 'NetworksInvite.status' => array(1, 4)),
                'contain' => array('Network.CreatedByUser')
            ));
            return $invites;
        } else {
            $this->redirect('/');
        }
        
    }

    /**
     * Accept invitation to network
     */
    public function accept($inv_id) {
        $this->NetworksInvite->id = $inv_id;
        if (!$this->NetworksInvite->exists($inv_id)) {
            throw new NotFoundException(__('Invalid invitation'));
        }
        $invite = $this->NetworksInvite->findById($inv_id);
        
        $data['NetworksInvite']['id'] = $inv_id;
        $data['NetworksInvite']['status'] = 2;
        $this->NetworksInvite->save($data);
        // Add user to network
        $netuser['NetworksUser']['network_id'] = $invite['NetworksInvite']['network_id'];
        $netuser['NetworksUser']['user_id'] = $this->Auth->user('id');
        $netuser['NetworksUser']['role'] = $invite['NetworksInvite']['role'];
        $netuser['NetworksUser']['warehouse'] = $invite['NetworksInvite']['warehouse'];
        $netuser['NetworksUser']['schannel'] = $invite['NetworksInvite']['schannel'];
        $netuser['NetworksUser']['products'] = $invite['NetworksInvite']['products'];
        $netuser['NetworksUser']['limited'] = $invite['NetworksInvite']['limited'];
        $netuser['NetworksUser']['status'] = 1;
        $this->NetworksUser->save($netuser);
        // Add access
        $access = $this->Access->getAccessByInvite($invite);
        $this->NetworksAccess->saveAll($access);

        $this->Session->setFlash(__('Your was successfuly added to network.'),'admin/success');
        $this->redirect(array('controller'=>'networks', 'action'=>'view', $invite['NetworksInvite']['network_id']));
    }

    /**
     * Decline invitation to network
     */
    public function decline($inv_id) {
        $this->NetworksInvite->id = $inv_id;
        if (!$this->NetworksInvite->exists($inv_id)) {
            throw new NotFoundException(__('Invalid invitation'));
        }

        $data['NetworksInvite']['id'] = $inv_id;
        $data['NetworksInvite']['status'] = 3;
        $this->NetworksInvite->save($data);
        if ($this->request->is('ajax')) {
            $response = array('action' => 'success');
            $this->set('_serialize', 'response');
            $json = json_encode($response);
            echo $json;
            exit;
        } else {
            $this->Session->setFlash(__('Invitation to network was declined'),'admin/success');
            $this->redirect(array('controller'=>'networks', 'action'=>'view', $invite['NetworksInvite']['network_id']));
        }
    }

    /**
     * Edit user access to netowrk
     */
    public function edit_access($id, $networkuser_id) {
        $network = $this->NetworksUser->find('first', ['conditions'=>['NetworksUser.id' => $networkuser_id, 'Network.id' => $id], 'contain' => ['Network', 'User']]);
        $warehouse = $this->Warehouse->find('list', array('conditions' => array('Warehouse.user_id' => $network['Network']['created_by_user_id'])));

        $access = $this->NetworksAccess->find('all', [
            'conditions' => [
                'NetworksAccess.network_id' => $network['NetworksUser']['network_id'],
                'NetworksAccess.user_id' => $network['NetworksUser']['user_id'],
                'NetworksAccess.model' => ['Inventory', 'S.O.', 'P.O.', 'Shipments']
            ],
            'contain' => ['Warehouse']
        ]);
        
        if ($this->request->is('ajax')) {
            if ($this->request->is(array('post', 'put'))) {
                $this->request->data['NetworksAccess']['user_id'] = $network['NetworksUser']['user_id'];
                if(!empty($this->request->data['NetworksAccess']['access'])) {
                    $this->request->data['NetworksAccess']['access'] = implode('',$this->request->data['NetworksAccess']['access']);
                }
                $exists = $this->NetworksAccess->find('first', [
                    'conditions' => [
                        'NetworksAccess.user_id' => $network['NetworksUser']['user_id'],
                        'NetworksAccess.warehouse_id' => $this->request->data['NetworksAccess']['warehouse_id'],
                        'NetworksAccess.model' => $this->request->data['NetworksAccess']['model'],
                        'NetworksAccess.network_id' => $id,
                    ],
                    'contain' => false
                ]);
                if($exists) {
                    $access_str = str_split($this->request->data['NetworksAccess']['access'] . $exists['NetworksAccess']['access']);
                    $access_str = array_unique($access_str);
                    sort($access_str);
                    $this->request->data['NetworksAccess']['access'] = implode('', $access_str);
                    $this->request->data['NetworksAccess']['id'] = $exists['NetworksAccess']['id'];
                }
                if($this->NetworksAccess->save($this->request->data)) {
                    $network['NetworksUser']['role'] = 5;
                    $this->NetworksUser->save($network);
                    $row = $this->NetworksAccess->find('first', [
                        'conditions' => [
                            'NetworksAccess.id' => $this->NetworksAccess->id
                        ],
                        'contain' => ['Warehouse']
                    ]);
                    $response = array('action' => 'success', 'row' => $row);
                } else {
                    $response = array('action' => 'error', 'errors' => $this->NetworksAccess->validationErrors);
                }

                $this->set('_serialize', 'response');
                $json = json_encode($response);
                echo $json;
                exit;
            }
        }
        $this->set(compact('network', 'access', 'warehouse'));
    }

    /**
     *
     *
     */
    public function edit_products($id, $networkuser_id) {
        $this->layout = null;
        $network = $this->NetworksUser->find('first', ['conditions'=>['NetworksUser.id' => $networkuser_id, 'Network.id' => $id], 'contain' => ['Network', 'User']]);
        $this->loadModel('Product');
        $products = $this->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.status_id' => 1)));
        if($network['NetworksUser']['products'] != 'all') {
            $product_list = json_decode($network['NetworksUser']['products'], true);
        } else {
            $product_list = []; //all
        }
        
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['NetworksUser']['products'] = 'all';
            if(isset($this->request->data['NetworksUser']['product_id']) && count($this->request->data['NetworksUser']['product_id']) > 0) {
                if(isset($this->request->data['NetworksUser']['product_id'][0]) && $this->request->data['NetworksUser']['product_id'][0]){
                    $this->request->data['NetworksUser']['products'] = json_encode($this->request->data['NetworksUser']['product_id']);
                }
            }

            if($this->NetworksUser->save($this->request->data)){
                $response = ['action' => 'success'];
            }
            $this->set('_serialize', 'response');
            $json = json_encode($response);
            echo $json;
            exit;
        }
        if ($this->request->is('ajax')) {
        }
        $this->set(compact('network', 'products', 'product_list'));
    }

    public function edit_channels($id, $networkuser_id) {
        $this->layout = null;
        $network = $this->NetworksUser->find('first', ['conditions'=>['NetworksUser.id' => $networkuser_id, 'Network.id' => $id], 'contain' => ['Network', 'User']]);
        $this->loadModel('Schannel');
        $schannels = $this->Schannel->find('list', array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));
        if($network['NetworksUser']['schannel'] != 'all') {
            $schannel_list = json_decode($network['NetworksUser']['schannel'], true);
        } else {
            $schannel_list = []; //all
        }
        
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['NetworksUser']['schannel'] = 'all';
            if(isset($this->request->data['NetworksUser']['schannel_id']) && count($this->request->data['NetworksUser']['schannel_id']) > 0) {
                if(isset($this->request->data['NetworksUser']['schannel_id'][0]) && $this->request->data['NetworksUser']['schannel_id'][0]){
                    $this->request->data['NetworksUser']['schannel'] = json_encode($this->request->data['NetworksUser']['schannel_id']);
                }
            }

            if($this->NetworksUser->save($this->request->data)){
                $response = ['action' => 'success'];
            }
            $this->set('_serialize', 'response');
            $json = json_encode($response);
            echo $json;
            exit;
        }
        
        $this->set(compact('network', 'schannels', 'schannel_list'));
    }

    public function remove_access($access_id) {
        if ($this->request->is('ajax')) {
            if ($this->request->is(array('post', 'put'))) {
                $access = $this->NetworksAccess->find('first', ['conditions'=>['NetworksAccess.id' => $access_id]]);
                if($access) {
                    $networkUser = $this->NetworksUser->find('first', [
                        'conditions' => [
                            'NetworksUser.network_id' => $access['NetworksAccess']['network_id'],
                            'NetworksUser.user_id' => $access['NetworksAccess']['user_id']
                        ],
                        'contain' => false
                    ]);
                    if($this->NetworksAccess->delete($access_id)) {
                        $networkUser['NetworksUser']['role'] = 5;
                        $this->NetworksUser->save($networkUser);
                        $response = array('action' => 'success', 'id' => $access_id);
                    } else {
                        $response = array('action' => 'error');
                    }
                } else {
                    $response = array('action' => 'error', 'msg' => 'Row not found');
                }
                $this->set('_serialize', 'response');
                $json = json_encode($response);
                echo $json;
                exit;
            }
        }
        die('Request not found');
    }

    public function delete_user($id, $networkuser_id) {
        $network = $this->NetworksUser->find('first', ['conditions'=>['NetworksUser.id' => $networkuser_id, 'Network.id' => $id], 'contain' => ['Network', 'User']]);
        if(!$network) {
            throw new NotFoundException(__('Order not found'));
        }

        if($this->request->is(array('post', 'put'))) {
            $this->NetworksAccess->deleteAll(['NetworksAccess.network_id' => $network['Network']['id'], 'NetworksAccess.user_id' => $network['User']['id']], false);
            $this->NetworksInvite->deleteAll(['NetworksInvite.network_id' => $network['Network']['id'], 'NetworksInvite.email' => $network['User']['email']], false);
            $this->NetworksUser->delete($networkuser_id, false);
            
            $this->Session->setFlash(__('User from network successfuly deleted.'),'admin/success');
            $this->redirect(array('controller'=>'networks', 'action'=>'details', $network['Network']['id']));
            
        }
    }

    /**
     * Edit user access to netowrk
     */
    public function edit_inv_access($inv_id) {

        $this->NetworksInvite->id = $inv_id;
        if (!$this->NetworksInvite->exists($inv_id)) {
            throw new NotFoundException(__('Invalid invitation'));
        }
        $invite = $this->NetworksInvite->findById($inv_id);
        
        /*$data['NetworksInvite']['id'] = $inv_id;
        $data['NetworksInvite']['status'] = 2;
        $this->NetworksInvite->save($data);
        // Add user to network
        $netuser['NetworksUser']['network_id'] = $invite['NetworksInvite']['network_id'];
        $netuser['NetworksUser']['user_id'] = $this->Auth->user('id');
        $netuser['NetworksUser']['role'] = $invite['NetworksInvite']['role'];
        $netuser['NetworksUser']['warehouse'] = $invite['NetworksInvite']['warehouse'];
        $netuser['NetworksUser']['products'] = $invite['NetworksInvite']['products'];
        $netuser['NetworksUser']['limited'] = $invite['NetworksInvite']['limited'];
        $netuser['NetworksUser']['status'] = 1;
        $this->NetworksUser->save($netuser);
        // Add access
        $access = $this->Access->getAccessByInvite($invite);
        $this->NetworksAccess->saveAll($access);

        $this->Session->setFlash(__('Your was successfuly added to network.'),'admin/success');
        $this->redirect(array('controller'=>'networks', 'action'=>'view', $invite['NetworksInvite']['network_id']));*/

        $network = $this->NetworksUser->find('first', ['conditions'=>['NetworksUser.id' => $networkuser_id, 'Network.id' => $id], 'contain' => ['Network', 'User']]);
        $warehouse = $this->Warehouse->find('list', array('conditions' => array('Warehouse.user_id' => $network['Network']['created_by_user_id'])));

        $access = $this->NetworksAccess->find('all', [
            'conditions' => [
                'NetworksAccess.network_id' => $network['NetworksUser']['network_id'],
                'NetworksAccess.user_id' => $network['NetworksUser']['user_id'],
                'NetworksAccess.model' => ['Inventory', 'S.O.', 'P.O.', 'Shipments']
            ],
            'contain' => ['Warehouse']
        ]);
        
        if ($this->request->is('ajax')) {
            if ($this->request->is(array('post', 'put'))) {
                $this->request->data['NetworksAccess']['user_id'] = $network['NetworksUser']['user_id'];
                if(!empty($this->request->data['NetworksAccess']['access'])) {
                    $this->request->data['NetworksAccess']['access'] = implode('',$this->request->data['NetworksAccess']['access']);
                }
                $exists = $this->NetworksAccess->find('first', [
                    'conditions' => [
                        'NetworksAccess.user_id' => $network['NetworksUser']['user_id'],
                        'NetworksAccess.warehouse_id' => $this->request->data['NetworksAccess']['warehouse_id'],
                        'NetworksAccess.model' => $this->request->data['NetworksAccess']['model'],
                        'NetworksAccess.network_id' => $id,
                    ],
                    'contain' => false
                ]);
                if($exists) {
                    $access_str = str_split($this->request->data['NetworksAccess']['access'] . $exists['NetworksAccess']['access']);
                    $access_str = array_unique($access_str);
                    sort($access_str);
                    $this->request->data['NetworksAccess']['access'] = implode('', $access_str);
                    $this->request->data['NetworksAccess']['id'] = $exists['NetworksAccess']['id'];
                }
                if($this->NetworksAccess->save($this->request->data)) {
                    $network['NetworksUser']['role'] = 5;
                    $this->NetworksUser->save($network);
                    $row = $this->NetworksAccess->find('first', [
                        'conditions' => [
                            'NetworksAccess.id' => $this->NetworksAccess->id
                        ],
                        'contain' => ['Warehouse']
                    ]);
                    $response = array('action' => 'success', 'row' => $row);
                } else {
                    $response = array('action' => 'error', 'errors' => $this->NetworksAccess->validationErrors);
                }

                $this->set('_serialize', 'response');
                $json = json_encode($response);
                echo $json;
                exit;
            }
        }
        $this->set(compact('network', 'access', 'warehouse'));
    }

    public function delete_inv_user($id) {
        $network = $this->NetworksInvite->find('first', ['conditions'=>['NetworksInvite.id' => $id]]);
        if(!$network) {
            throw new NotFoundException(__('Order not found'));
        }

        if($this->request->is(array('post', 'put'))) {
            $this->NetworksInvite->delete($id, false);
            
            $this->Session->setFlash(__('Invitation to network successfuly deleted.'),'admin/success');
            $this->redirect(array('controller'=>'networks', 'action'=>'details', $network['Network']['id']));
            
        }
    }
}
