<?php
App::uses('AppController', 'Controller');
/**
 * Products Controller

 * @property Invalert $Invalert
 */
class InvalertsController extends AppController {

	/**
     * Components
     *
     * @var array
     */
    public $components = array('Access', 'Paginator', 'Search.Prg');
    public $helpers = array();
    public $paginate = array();
    public $theme = 'Mtro';

    public function beforeFilter() {
       parent::beforeFilter();
    }
    
    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function isAuthorized($user) {
        return parent::isAuthorized($user);
    }

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $limit = 10;
        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );

        if ($this->request->is(array('post', 'put'))) {
            $this->Prg->commonProcess(null, ['paramType'=>'query']);
        }

        if ($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }

        $this->Paginator->settings = array(
            'contain' => array('Product', 'Warehouse'),
            'fields' => array(
                'Invalert.id',
                'Invalert.product_id',
                'Invalert.warehouse_id',
                'Invalert.reorder_point',
                'Invalert.safety_stock',
                'Product.id',
                'Product.name',
                'Product.sku',
                'Product.imageurl',
                'Warehouse.id',
                'Warehouse.name'
            ),
            /*'joins' => array(
                array('table' => 'inventories',
                    'alias' => 'Inventory',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Inventory.product_id = Invalert.product_id AND Inventory.warehouse_id = Invalert.warehouse_id'
                    )
                )
            ),*/
            'limit' => $limit,

            'order' => array('Product.modified' => 'DESC')
        );
        $conditions['Invalert.user_id'] = $this->Auth->user('id');
        $conditions['Product.deleted'] = 0;
        $conditions['Product.status_id NOT IN'] = [12, 13];
        $invalerts = $this->Paginator->paginate($conditions);
        $this->set(compact('invalerts', 'options', 'limit'));
    }
    
    public function add() {
        $this->layout = false;

        $this->loadModel('Product');
        $this->Product->recurcive = -1;
        $products = $this->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'))));
        $this->loadModel('Warehouse');
        $this->Warehouse->recurcive = -1;
        $warehouses = $this->Warehouse->find('list', array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'), 'Warehouse.status' => 'active')));

        if($this->request->is(array('post', 'put'))) {
            $invalert = $this->Invalert->find('first', array(
                'conditions' => array('product_id' => $this->request->data['Invalert']['product_id'], 'warehouse_id' => $this->request->data['Invalert']['warehouse_id']),
                'fields' => array('Invalert.id', 'Invalert.user_id', 'Invalert.warehouse_id', 'Invalert.product_id', 'Invalert.reorder_point', 'Invalert.safety_stock')
            ));
            if($invalert) {
                $this->Invalert->id = $invalert['Invalert']['id'];
                $this->request->data['Invalert']['id'] = $invalert['Invalert']['id'];
                $msg = 'Inventory Alert was successfully updated';
            } else {
                $msg = 'Inventory Alert was successfully added';
            }
            $this->request->data['Invalert']['user_id'] = $this->Auth->user('id');
            if($this->Invalert->save($this->request->data)) {
                $this->Session->setFlash($msg, 'admin/success');
                if($this->request->query('f') == 'a') {
                    return $this->redirect(array('controller' => 'invalerts', 'action' => 'index'));
                } else {
                    return $this->redirect(array('controller' => 'inventories', 'action' => 'index'));
                }
            } else {
                $this->Session->setFlash(__('Inventory Alert can\'t be saved. Please check errors and try again.'), 'admin/danger');
            }
        } else {
           
        }
        $this->set(compact('products', 'warehouses'));
    }

    /**
     * Create Invalert
     *
     */
    public function create($product_id, $warehouse_id) {
        $this->layout = false;

        $this->loadModel('Product');
        $this->Product->recurcive = -1;
        $product = $this->Product->find('first', array('conditions' => array('Product.id' => $product_id), 'fields' => array('Product.id', 'Product.user_id', 'Product.name')));

        $this->loadModel('Warehouse');
        $this->Warehouse->recurcive = -1;
        $warehouse = $this->Warehouse->find('first', array('conditions' => array('Warehouse.id' => $warehouse_id), 'fields' => array('Warehouse.id', 'Warehouse.user_id', 'Warehouse.name')));

        $invalert = $this->Invalert->find('first', array(
        	'conditions' => array('product_id' => $product_id, 'warehouse_id' => $warehouse_id),
        	'fields' => array('Invalert.id', 'Invalert.user_id', 'Invalert.warehouse_id', 'Invalert.product_id', 'Invalert.reorder_point', 'Invalert.safety_stock')
        ));

        if($this->request->is(array('post', 'put'))) {
        	if($invalert) {
        		$this->Invalert->id = $invalert['Invalert']['id'];
        		$this->request->data['Invalert']['id'] = $invalert['Invalert']['id'];
        	}
        	$this->request->data['Invalert']['user_id'] = $product['Product']['user_id'];
        	if($this->Invalert->save($this->request->data)) {
        		$this->Session->setFlash(__('Inventory Alert was successfully added'), 'admin/success');
                if($this->request->query('f') == 'a') {
                    return $this->redirect(array('controller' => 'invalerts', 'action' => 'index'));
                } else {
                    return $this->redirect(array('controller' => 'inventories', 'action' => 'index'));
                }
        	} else {
        		$this->Session->setFlash(__('Inventory Alert can\'t be saved. Please check errors and try again.'), 'admin/danger');
        	}
        } else {
        	if($invalert) {
        		$this->request->data = $invalert;
        	} else {
        		$this->request->data['Invalert']['product_id'] = $product_id;
        		$this->request->data['Invalert']['warehouse_id'] = $warehouse_id;
        	}
        }
        $this->set(compact('product', 'warehouse'));
    }
}