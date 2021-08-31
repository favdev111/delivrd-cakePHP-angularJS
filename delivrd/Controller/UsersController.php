<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

/**
 * Components
 *
 * @var array
 */
    public $components = array('Paginator');

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->Paginator->paginate());
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
    }

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function view($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
        $this->set('user', $this->User->find('first', $options));
    }

/**
 * add method
 *
 * @return void
 */
    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
        $created = 0;
        $this->set(compact('created'));
    }

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function edit($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('User.id' => $id));
            $this->request->data = $this->User->find('first', $options);
        }
    }

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function delete($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->User->delete()) {
            $this->Session->setFlash(__('The user has been deleted.'));
        } else {
            $this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }
    
    public function beforeFilter() {
    parent::beforeFilter();
    // Allow users to register and logout.
    $this->Auth->allow('add', 'logout');
}

    public function login() {
        
    if ($this->request->is('post')) {
        if ($this->Auth->login()) {
            return $this->redirect($this->Auth->redirect());
        }
        $this->Session->setFlash(__('Invalid username or password, try again'));
    }
}

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    public function start_trial() {
        $this->layout = false;
        if($this->request->is('post') || $this->request->is('put')) {

            $dataSource = $this->User->getDataSource();
            $dataSource->begin();

            $this->User->id = $this->Auth->user('id');
            $this->User->saveField('locationsactive', 1);
            $this->User->saveField('paid', 1);
            $this->User->saveField('role', 'trial');
            
            // Add subscription
            $this->loadModel('Subscription');
            $subscription = $this->Subscription->find('first', ['conditions' => array('Subscription.user_id' => $this->Auth->user('id'))]); //, 'Subscription.status' => 'Trial'
            if(!$subscription) {
                $subscription['Subscription']['ext_id'] = 'LOC-TRIAL';
                $subscription['Subscription']['user_id'] = $this->Auth->user('id');
                $subscription['Subscription']['amount'] = 0.00;
                $subscription['Subscription']['payer_email'] = '';
                $subscription['Subscription']['expiry_date'] = date('Y-m-d', strtotime('+30 days'));
                $subscription['Subscription']['status'] = 'Trial';
                $subscription['Subscription']['created'] = date('Y-m-d H:i:s');
                $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');
                $subscription['Subscription']['memo'] = 'Strart trial modal';
            } else {
                $stopdate = strtotime(date('Y-m-d', strtotime($subscription['Subscription']['stopdate'])));
                $enddate = strtotime(date('Y-m-d', strtotime($subscription['Subscription']['expiry_date'])));
                $stay_trial_duration = round(($enddate - $stopdate)/(3600*24));
                $stay_trial_duration = min($stay_trial_duration, 30);

                $this->Subscription->id = $subscription['Subscription']['id'];
                $subscription['Subscription']['status'] = 'Trial';
                $subscription['Subscription']['expiry_date'] = date('Y-m-d', strtotime('+'. $stay_trial_duration .' days'));
                $subscription['Subscription']['memo'] = "Re-start trial\n ". $subscription['Subscription']['memo'];
            }
            if($this->Subscription->save($subscription)) {
                $dataSource->commit();
                $this->Session->write('locationsactive', 1);
                $this->Session->write('paid', 1);
                $this->Session->write('role', 'trial');

                $response['action'] = 'success';
            } else {
                $dataSource->rollback();

                $response['action'] = 'error';
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        $type = $this->request->query('type');
        $this->set(compact('type'));
    }

    public function stop_trial() {
        $this->layout = false;

        $this->loadModel('Product');
        $this->loadModel('Warehouse');
        $this->loadModel('Order');
        $productcount = $this->Product->find('count', array('conditions' => ['Product.user_id' => $this->Auth->user('id'), 'Product.status_id NOT IN' => [12, 13], 'Product.deleted' =>0]));
        
        $this->Warehouse->recursive = -1;
        $locationscount = $this->Warehouse->find('count', array('conditions' => ['Warehouse.user_id' => $this->Auth->user('id'), 'Warehouse.status' => 'active']));
        $so_count = $this->Order->find('count', array('conditions' => ['Order.user_id' => $this->Auth->user('id'), 'Order.ordertype_id' => 1]));
        $po_count = $this->Order->find('count', array('conditions' => ['Order.user_id' => $this->Auth->user('id'), 'Order.ordertype_id' => 2]));

        if($this->request->is('post') || $this->request->is('put')) {
            if($productcount <= 10) {
                $dataSource = $this->User->getDataSource();
                $dataSource->begin();

                $this->User->id = $this->Auth->user('id');
                $this->User->saveField('locationsactive', 0);
                $this->User->saveField('paid', 0);
                $this->User->saveField('role', 'free');
                
                if($locationscount > 1) {
                    $location = $this->Warehouse->find('first', array('fields'=>['Warehouse.id'], 'conditions' => ['Warehouse.user_id' => $this->Auth->user('id'), 'Warehouse.status' => 'active'], 'order' => ['Warehouse.created' => 'ASC']));
                    $this->Warehouse->recursive = -1;
                    $this->Warehouse->updateAll(['Warehouse.status' => '"inactive"'], ['Warehouse.user_id' => $this->Auth->user('id'), 'Warehouse.id !=' => $location['Warehouse']['id'] ]);
                }

                // Check subscription
                $this->loadModel('Subscription');
                $subscription = $this->Subscription->find('first', ['conditions' => array('Subscription.user_id' => $this->Auth->user('id'))]); //, 'Subscription.status' => 'Trial'
                $subscription['Subscription']['stopdate'] = date('Y-m-d H:i:s');
                $subscription['Subscription']['status'] = 'Stopped';
                $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');
                $subscription['Subscription']['memo'] = "Stop trial\n ". $subscription['Subscription']['memo'];
                if($this->Subscription->save($subscription)) {
                    $dataSource->commit();
                    $this->Session->write('locationsactive', 0);
                    $this->Session->write('paid', 0);
                    $this->Session->write('role', 'free');

                    $response['action'] = 'success';
                    $response['msg'] = 'Trial period stopped.';
                } else {
                    $dataSource->rollback();
                    $response['action'] = 'error';
                    $response['msg'] = __('Can\'t stop trial period. Please contact with admin.');
                }
            } else {
                $response['action'] = 'error';
                $response['msg'] = __('We can\'t stop trial period because you have more then 10 prducts. Please block or delete it.');
            }
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        $this->set(compact('productcount', 'locationscount', 'so_count', 'po_count'));
    }

    public function add_activity() {
        if($this->request->is('post') || $this->request->is('put')) {
            $this->loadModel('Product');
            $this->loadModel('Activity');
            $this->Product->recursive = -1;
            $product_sku = $this->Product->find('first', ['conditions' => ['Product.id' => $this->request->data['product_id']], 'fields' => ['Product.sku']]);
            $this->Activity->add_activity($product_sku['Product']['sku'], $this->Auth->user('id'));
        }
        exit;
    }

    public function add_stats() {
        $_links = [
            'products' => [
                'title' => 'Products',
                'url' => '/products'
            ],
            'new_product' => [
                'title' => 'Add Product',
                'url' => '/products/add'
            ],
            'inventories' => [
                'title' => 'Inventories',
                'url' => '/inventories'
            ],
            'new_inventory' => [
                'title' => 'Add Inventory',
                'url' => '/inventories/add'
            ],
            'po' => [
                'title' => 'Purchase Orders',
                'url' => '/replorders'
            ],
            'new_po' => [
                'title' => 'Add Purchase Order',
                'url' => '/replorders/create'
            ],
            'po_shipment' => [
                'title' => 'Inbound Shipments',
                'url' => '/shipments/2'
            ],
            'so' => [
                'title' => 'Sales Orders',
                'url' => '/salesorders'
            ],
            'new_so' => [
                'title' => 'Add Sales Order',
                'url' => '/salesorders/create'
            ],
            'so_shipment' => [
                'title' => 'Outbound Shipments',
                'url' => '/shipments/1'
            ],
            'serials' => [
                'title' => 'Serials',
                'url' => '/serials'
            ]
        ];
        if($this->request->is('post') || $this->request->is('put')) {
            $this->loadModel('UserShortcutLink');
            $link = $this->UserShortcutLink->find('first', [
                'conditions' => [
                    'UserShortcutLink.user_id' => $this->Auth->user('id'),
                    'UserShortcutLink.slug' => $this->request->data['page'],
                ],
                'fields' => ['UserShortcutLink.id', 'UserShortcutLink.clicked']
            ]);
            if($link) {
                $link['UserShortcutLink']['clicked'] = $link['UserShortcutLink']['clicked'] + 1;
                $this->UserShortcutLink->save($link);
            } else {
                $link['UserShortcutLink']['user_id'] = $this->Auth->user('id');
                $link['UserShortcutLink']['slug'] = $this->request->data['page'];
                $link['UserShortcutLink']['url'] = $_links[$this->request->data['page']]['url'];
                $link['UserShortcutLink']['name'] = $_links[$this->request->data['page']]['title'];
                $this->UserShortcutLink->save($link);
            }

            $shortcutList = $this->UserShortcutLink->getShortList($this->Auth->user('id'));
            $this->Session->write('shortcut', $shortcutList);
            
            /*if(!isset($settings['top_links'])) {
                $settings['top_links'] = [];
            }
            if(!isset($settings['top_links'][$this->request->data['page']])) {
                $settings['top_links'][$this->request->data['page']] = 1;
            } else {
                $settings['top_links'][$this->request->data['page']] = $settings['top_links'][$this->request->data['page']] + 1;
            }*/

            #$this->User->saveSetting($this->Auth->user('id'), 'top_links', $settings['top_links']);
        }
        exit;
        //return true;
    }
}
