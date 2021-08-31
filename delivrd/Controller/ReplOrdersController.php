<?php
App::uses('AppController', 'Controller');
App::uses('OrdersController', 'Controller');
/**
 * ReplOrders Controller
 *
 * @property Order $Order
 * @property PaginatorComponent $Paginator
 */
class  ReplOrdersController extends OrdersController {

    public $theme = 'Mtro';

    /**
    * Helpers
    *
    * @var array
    */
    public $helpers = array('Lang');

    /**
    * Components
    *
    * @var array
    */
    public $components = array('Paginator','EventRegister','RequestHandler','Csv.Csv','Search.Prg','Shopfy','WooCommerce','Cookie', 'InventoryManager');

    /**
    * Models
    *
    * @var array
    */
    public $uses = array('Order', 'OrdersLine', 'Product', 'User', 'Warehouse', 'Currency');
    
    public $paginate = array();
    public $type = 'P.O.';


    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter() {
        parent::beforeFilter();
    }

    /**
    * index method
    *
    * @return void
    */  
    public function index($product_id = null, $created = null) {
        $this->layout = 'mtrd';
        
        $products = $this->Access->getProducts($this->type);
        $warehouses = $this->Access->locationList($this->type);

        $is_have_access = true;
        if((empty($products) || empty($warehouses)) && $this->_authUser['User']['is_limited']) {
            //throw new MethodNotAllowedException(__('Have no access'));
            $is_have_access = false;
        }
        if((empty($products) || empty($warehouses)) && !$this->_authUser['User']['paid']) {
            //throw new MethodNotAllowedException(__('Have no access'));
            $is_have_access = false;
        }
        $is_write = true;
        if($this->_authUser['User']['is_limited'] || !$this->_authUser['User']['paid']) {
            $is_write = false;
            if(isset($this->Access->_access[$this->type])) {
                foreach ($this->Access->_access[$this->type] as $value) {
                    if(strpos($value['NetworksAccess']['access'], 'w') !== false) {
                        $is_write = true;
                    }
                }
            }
        }

        if(!empty($this->request->data['message']) && $this->request->data['message'] == 1) {
            $this->Cookie->write('message', 1);
        }
        $popup = $this->Cookie->read('message');

        $have_address = true;
        if(!$this->Auth->user('is_limited')) { //All not limited users must have address to create R.O.
            $this->loadModel('Address');
            $useraddress = $this->Address->find('first', array('conditions' => array('Address.user_address_id' => $this->Auth->user('id')), 'fields' => array('id','street', 'city', 'country_id')));
            if(!$useraddress) {
                $have_address = false;
            }
        }

        $limit = $this->Auth->user('list_limit');
        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );

        $this->Paginator->settings = array(
            'limit' => $limit,
            'order' => array('Order.modified' => 'DESC'),
            'contain' => array('Supplysource', 'Status', 'Supplier','Schannel'),
            'joins' => array(
                array('table' => 'orders_lines',
                    'alias' => 'OrdersLine',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id',
                    )
                ),
                array('table' => 'networks_access',
                    'alias' => 'NetworksAccess',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'NetworksAccess.warehouse_id = OrdersLine.warehouse_id AND NetworksAccess.model = "'. $this->type .'" AND NetworksAccess.user_id = "'. $this->Auth->user('id') .'"',
                    )
                ),
            ),
            "fields" => array(
                "Order.id",
                "Order.user_id",
                "Order.ordertype_id",
                "Order.supplier_id",
                "Order.supplysource_id",
                "Order.status_id",
                "Order.created",
                "Order.requested_delivery_date",
                "Supplier.name",
                "Supplysource.name",
                "Schannel.name",
                "Order.external_orderid",
                "Order.ship_to_customerid",
                "GROUP_CONCAT(OrdersLine.warehouse_id,',') as warehouse",
                "GROUP_CONCAT(DISTINCT NetworksAccess.access, '') as access"
            ),
            'group' => 'Order.id'
        );

        $conditions = array();
        $conditions['Order.ordertype_id'] = 2;

        $allowed_products = [];
        if(isset($this->Access->_access[$this->type])) {
            if($products) {
                $allowed_products = array_keys($products);
            }
        }

        if($allowed_products) {
            $conditions['OR'] = ['Order.user_id' => $this->Auth->user('id')];
            $orderIds = $this->OrdersLine->find('list', [
                'fields' => array('OrdersLine.order_id'),
                'conditions' => array(
                    'OrdersLine.product_id' => $allowed_products,
                    'OrdersLine.warehouse_id' => array_keys($warehouses)
                ),
                'callbacks' => false
            ]);
            $conditions['OR'][] = ['Order.id' => $orderIds]; 
        } else {
            $conditions['Order.user_id'] = $this->Auth->user('id');
            if($product_id) {
                $orderIds = $this->Order->OrdersLine->find('list', array('fields' => array('OrdersLine.order_id'), 'conditions' => array('OrdersLine.product_id' => $product_id), 'callbacks' => false));
                $conditions['Order.id'] = array_keys($orderIds);
            }
        }

        if ($this->request->is('post')) {
            $this->Prg->commonProcess(null, ['paramType'=>'query']);
        }

        if ($this->request->query('limit')) {
            $limit = $this->request->query('limit');
            $this->Paginator->settings['limit'] = $limit;
        }

        if ($this->request->query('createdfrom')) {
            $conditions['Order.created >='] = $this->request->query('createdfrom');
        }

        $po_filter = [];
        if ($this->request->query('status_id')) {
            $po_filter = $this->request->query('status_id');
            $this->User->saveSetting($this->Auth->user('id'), 'po_filter', $po_filter);
            $conditions['Order.status_id'] =  $this->request->query('status_id');
        } else {
            if($this->request->query('search')) {
                $po_filter = $this->request->query('status_id');
                $conditions['Order.status_id !='] = 50;
                $this->User->saveSetting($this->Auth->user('id'), 'po_filter', $po_filter);
            } else {
                $settings = json_decode($this->Auth->user('settings'), true);
                if(isset($settings['po_filter']) &&  $settings['po_filter']) {
                    $conditions['Order.status_id'] =  $settings['po_filter'];
                    $po_filter = $settings['po_filter'];
                } else {
                    $conditions['Order.status_id !='] = 50;
                }
            }
        }
        
        if ($this->request->query('supplier_id')) {
            $conditions['Order.supplier_id'] =  $this->request->query('supplier_id');
        }

        if ($this->request->query('dash')) {
            $conditions['Order.requested_delivery_date <'] =  date('Y-m-d', strtotime('-1 days'));

            $orderIds = $this->OrdersLine->find('list', [
                'conditions' => [
                    'Order.ordertype_id' => 2,
                    'OR' => array('Order.requested_delivery_date >=' => date('Y-m-d', strtotime('-1 days')), 'Order.requested_delivery_date' => null ),
                    'Order.status_id' => 2,
                    'Order.user_id' => $this->Auth->user('id'),
                    'OrderSchedule.delivery_date <' => date('Y-m-d', strtotime('-1 days')),
                ],
                'contain' => [
                    'Order',
                    'OrderSchedule'
                ],
                'fields' => [
                    'Order.id'
                ],
                'group' =>'Order.id'
            ]);

            
            $new_conditions['OR'] = ['Order.id' => $orderIds, 'AND' => $conditions];
            $conditions = $new_conditions;
        }

        if ($this->request->query('searchby')) {
            $conditions['AND']['OR']['Order.ship_to_customerid like'] = '%'. $this->request->query('searchby') .'%';
            $conditions['AND']['OR']['Order.id'] = $this->request->query('searchby');
            $conditions['AND']['OR']['Order.external_orderid'] = $this->request->query('searchby');
            $conditions['AND']['OR']['Supplier.name like'] = '%'. $this->request->query('searchby') .'%';
        }

        if (isset($product_id) && $product_id) {
            $orderIds = $this->Order->OrdersLine->find('list', [
                        'fields' => array('OrdersLine.order_id'),
                        'conditions' => array('OrdersLine.product_id' => $product_id),
                        'callbacks' => false
                    ]);
            $conditions['Order.id'] = $orderIds;
        }

        $this->request->data['Order'] = $this->request->query;

        $this->paginate['recursive'] = -1;
        $suppliers = $this->Access->suppliersList($this->type, 'r');
//        Cakelog::write('debug', print_r($conditions,true));
        $orders = $this->Paginator->paginate($conditions);

        $this->set(compact('orders', 'suppliers', 'status', 'po_filter', 'schannels', 'is_write', 'have_address', 'popup', 'options', 'limit', 'is_have_access'));
    }

    /**
    * index method with angular ui.bootstrap
    *
    * @return void
    */
    public function canceled($product_id = null, $created = null) {
        $products = $this->Access->getProducts($this->type);
        $warehouses = $this->Access->locationList($this->type);

        $is_have_access = true;
        if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
            //throw new MethodNotAllowedException(__('Have no access'));
            $is_have_access = false;
        }
        if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
            //throw new MethodNotAllowedException(__('Have no access'));
            $is_have_access = false;
        }
        $is_write = true;
        if($this->Auth->user('is_limited') || !$this->Auth->user('paid')) {
            $is_write = false;
            if(isset($this->Access->_access[$this->type])) {
                foreach ($this->Access->_access[$this->type] as $value) {
                    if(strpos($value['NetworksAccess']['access'], 'w') !== false) {
                        $is_write = true;
                    }
                }
            }
        }

        if(!empty($this->request->data['message']) && $this->request->data['message'] == 1) {
            $this->Cookie->write('message', 1);
        }
        $popup = $this->Cookie->read('message');

        $have_address = true;
        if(!$this->Auth->user('is_limited')) { //All not limited users must have address to create R.O.
            $this->loadModel('Address');
            $useraddress = $this->Address->find('first', array('conditions' => array('Address.user_address_id' => $this->Auth->user('id')), 'fields' => array('id','street', 'city', 'country_id')));
            if(!$useraddress) {
                $have_address = false;
            }
        }

        $limit = $this->Auth->user('list_limit');
        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );

        $this->Paginator->settings = array(
            'limit' => $limit,
            'order' => array('Order.modified' => 'DESC'),
            'contain' => array('Supplysource', 'Status', 'Supplier','Schannel'),
            'joins' => array(
                array('table' => 'orders_lines',
                    'alias' => 'OrdersLine',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id',
                    )
                ),
                array('table' => 'networks_access',
                    'alias' => 'NetworksAccess',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'NetworksAccess.warehouse_id = OrdersLine.warehouse_id AND NetworksAccess.model = "'. $this->type .'" AND NetworksAccess.user_id = "'. $this->Auth->user('id') .'"',
                    )
                ),
            ),
            "fields" => array(
                "Order.id",
                "Order.user_id",
                "Order.ordertype_id",
                "Order.supplier_id",
                "Order.supplysource_id",
                "Order.status_id",
                "Order.created",
                "Order.requested_delivery_date",
                "Supplier.name",
                "Supplysource.name",
                "Schannel.name",
                "Order.external_orderid",
                "Order.ship_to_customerid",
                "GROUP_CONCAT(OrdersLine.warehouse_id,',') as warehouse",
                "GROUP_CONCAT(DISTINCT NetworksAccess.access, '') as access"
            ),
            'group' => 'Order.id'
        );

        $conditions = array();
        $conditions['Order.ordertype_id'] = 2;

        $allowed_products = [];
        if(isset($this->Access->_access[$this->type])) {
            if($products) {
                $allowed_products = array_keys($products);
            }
        }

        if($allowed_products) {
            $conditions['OR'] = ['Order.user_id' => $this->Auth->user('id')];
            $orderIds = $this->OrdersLine->find('list', [
                'fields' => array('OrdersLine.order_id'),
                'conditions' => array(
                    'OrdersLine.product_id' => $allowed_products,
                    'OrdersLine.warehouse_id' => array_keys($warehouses)
                ),
                'callbacks' => false
            ]);
            $conditions['OR'][] = ['Order.id' => $orderIds]; 
        } else {
            $conditions['Order.user_id'] = $this->Auth->user('id');
            if($product_id) {
                $orderIds = $this->Order->OrdersLine->find('list', array('fields' => array('OrdersLine.order_id'), 'conditions' => array('OrdersLine.product_id' => $product_id), 'callbacks' => false));
                $conditions['Order.id'] = array_keys($orderIds);
            }
        }

        if ($this->request->is('post')) {
            $this->Prg->commonProcess(null, ['paramType'=>'query']);
        }

        if ($this->request->query('limit')) {
            $limit = $this->request->query('limit');
            $this->Paginator->settings['limit'] = $limit;
        }

        if ($this->request->query('createdfrom')) {
            $conditions['Order.created >='] = $this->request->query('createdfrom');
        }

        
        $conditions['Order.status_id'] = 50;
               
        
        if ($this->request->query('supplier_id')) {
            $conditions['Order.supplier_id'] =  $this->request->query('supplier_id');
        }

        if ($this->request->query('dash')) {
            $conditions['Order.requested_delivery_date <'] =  date('Y-m-d', strtotime('-1 days'));
        }
        
        if ($this->request->query('searchby')) {
            $conditions['AND']['OR']['Order.ship_to_customerid like'] = '%'. $this->request->query('searchby') .'%';
            $conditions['AND']['OR']['Order.id'] = $this->request->query('searchby');
            $conditions['AND']['OR']['Order.external_orderid'] = $this->request->query('searchby');
            $conditions['AND']['OR']['Supplier.name like'] = '%'. $this->request->query('searchby') .'%';
        }

        if (isset($product_id) && $product_id) {
            $orderIds = $this->Order->OrdersLine->find('list', [
                        'fields' => array('OrdersLine.order_id'),
                        'conditions' => array('OrdersLine.product_id' => $product_id),
                        'callbacks' => false
                    ]);
            $conditions['Order.id'] = $orderIds;
        }

        $this->request->data['Order'] = $this->request->query;

        $this->paginate['recursive'] = -1;
        $suppliers = $this->Access->suppliersList($this->type, 'r');
        $orders = $this->Paginator->paginate($conditions);

        $this->set(compact('orders', 'suppliers', 'status', 'schannels', 'is_write', 'have_address', 'popup', 'options', 'limit', 'is_have_access'));
    }

    /**
     * Create Repl Order
     *
     */
    public function create() {
        $this->layout = 'mtrd';

        $products = $this->Access->getProducts($this->type, 'w');
        $warehouses = $this->Access->locationList($this->type, false, 'w');
        $suppliers = $this->Access->suppliersList($this->type, 'w');

        if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
            throw new MethodNotAllowedException(__('Have no access'));
        }
        if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
            throw new MethodNotAllowedException(__('Have no access'));
        }

        //if no products exist, do not go to order creation page
        $this->loadModel('Product');
        $productscount = $this->Product->find('count', array('conditions' => array('Product.user_id' => $this->Auth->user('id'))));
        if($productscount == 0 && count($products) == 0) {
            $this->Session->setFlash(__('No products exist, you cannot create a cutsomer order. Pleaes create a product and packaging material.'),'admin/danger');
            return $this->redirect(array('action' => 'index'));
        }

        if ($this->request->is('post')) {
            if(isset($this->request->data['supplier_id']))
                $this->request->data['Order']['supplier_id'] = $this->request->data['supplier_id'];
            
            $this->loadModel('Supplier');
            $supplysourceid = $this->Supplier->find('first', array('fields' => array('Supplier.supplysource_id, Supplier.user_id'), 'conditions' => array('Supplier.id' => $this->request->data['Order']['supplier_id'])));
            $owner = $this->User->find('first', ['conditions' => array('User.id' => $supplysourceid['Supplier']['user_id'])]);

            $userdata = $this->User->find('first', array('conditions' => array('User.id' => $supplysourceid['Supplier']['user_id']), 'contain'=>false));

            #$this->loadModel('Supplier');
            #$supplysourceid = $this->Supplier->find('first', array('fields' => array('Supplier.supplysource_id'), 'conditions' => array('Supplier.id' => $this->request->data['Order']['supplier_id'])));
        
            $this->Order->create();
            $this->request->data('Order.dcop_user_id', $this->Auth->user('id'));
            $this->request->data('Order.user_id', $userdata['User']['id']);
            $this->request->data('Order.status_id',14);
            $this->request->data('Order.interface',0);
            $this->request->data('Order.ordertype_id',2);
            $this->request->data('Order.source_id',1);
            $this->request->data('Order.supplysource_id',$supplysourceid['Supplier']['supplysource_id']);
            $this->request->data('Order.ship_to_customerid',$userdata['User']['username']);
            $this->request->data('Order.ship_to_street',$userdata['User']['street']);
            $this->request->data('Order.ship_to_city',$userdata['User']['city']);
            $this->request->data('Order.ship_to_zip',$userdata['User']['zip']);
            $this->request->data('Order.state_id',$userdata['User']['state_id']);
            $this->request->data('Order.country_id',$userdata['User']['country_id']);

            if($owner['Address']) {
                $this->request->data('Address.street',$owner['Address']['street']);
                $this->request->data('Address.city',$owner['Address']['city']);
                $this->request->data('Address.zip',$owner['Address']['zip']);
                $this->request->data('Address.state_id',$owner['Address']['state_id']);
                $this->request->data('Address.country_id',$owner['Address']['country_id']);
                $this->request->data('Address.stateprovince',$owner['Address']['stateprovince']);
            }
            
            if ($this->Order->saveAll($this->request->data)) {
                $this->EventRegister->addEvent(2,1,$this->Auth->user('id'),$this->Order->id);
                $this->Session->setFlash(__('New order has been created, number %s',$this->Order->id),'admin/success');
                return $this->redirect(array('controller' => 'replorders', 'action' => 'details', $this->Order->id,'?' => array('new' => 1)));
            } else {
                $this->Session->setFlash(__('The order could not be created. Please, try again.'),'admin/danger');
            }
            
            if ($this->Order->save($this->request->data)) {     
                $this->EventRegister->addEvent(2,1,$this->Auth->user('id'),$this->Order->id);
                $this->Session->setFlash(__('New order has been created, number %s',$this->Order->id),'admin/success');
                return $this->redirect(array('controller' => 'salesorders', 'action' => 'details', $this->Order->id,'?' => array('new' => 1)));
            } else {
                $this->Session->setFlash(__('The order could not be saved. Please, try again.'),'admin/danger');
            }
        }

        $this->set(compact('suppliers'));
    }

    /**
     * Repl Order Details
     *
     */
    public function details($id) {
        $order = $this->Order->find('first', array(
            'contain' => array('User', 'Supplier', 'Country', 'State', 'Shipment', 'Address', 'Address.Country', 'Address.State', 'OrdersLine.OrderSchedule', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
            'conditions' => array('Order.id' => $id)
        ));

        if($order['Order']['blanket']) {
            $this->redirect(['controller' => 'replorders', 'action' => 'details_bl', $id]);
        }

        if(!$order) {
            throw new NotFoundException(__('Order not found'));
        }

        $is_own = ($order['Order']['user_id'] == $this->Auth->user('id'));
        $is_write = 0;
        $is_shipment = 0;
        if($order['Order']['user_id'] != $this->Auth->user('id')) {
            $products = $this->Access->getProducts($this->type, 'w', $order['Order']['user_id']);
            $warehouses = $this->Access->locationList($this->type, false, false, $order['Order']['user_id']);

            if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
                //throw new MethodNotAllowedException(__('Have no access'));
                $this->Session->setFlash(__('You do not have access to purchase orders. Please contact admin.'), 'admin/success');
                return $this->redirect(array('action' => 'index'));
            }
            if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
                //throw new MethodNotAllowedException(__('Have no access'));
                $this->Session->setFlash(__('You do not have access to purchase orders. Please contact admin.'), 'admin/success');
                return $this->redirect(array('action' => 'index'));
            }
            foreach ($warehouses as $w) {
                if(strpos($w, 'w') !== 0) {
                    $is_write = 1;
                }
            }
            
            $orders_lines = $this->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'callbacks' => false
            ));

            $is_shipment = $this->Access->hasOrderShipmentAccess($id);
        } else {
            $is_write = 1;
            $orders_lines = $this->OrdersLine->find('all', array('conditions' => array('OrdersLine.order_id' => $id,'OrdersLine.user_id' => $this->Auth->user('id')), 'callbacks' => false));
            $warehouses = $this->Warehouse->find('list', ['fields'=>['Warehouse.id', 'Warehouse.name'], 'conditions' => array('Warehouse.user_id' => $this->Auth->user('id'), 'Warehouse.status' => 'active')] );
            foreach ($warehouses as $key => $value) {
                $warehouses[$key] = 'rw';
            }
            $is_shipment = 1;
        }
        
        $ordertotals = $this->getordertotals($this->Order->read(null, $id), $orders_lines);

        $currency = $this->Currency->find('first', array('conditions' => array('id' => $order['User']['currency_id'])));
        $linecount = count($order['OrdersLine']);

        $objectevents = $this->EventRegister->getObjectEvent(2, $id, $this->Auth->user('id'));

        $addr = $order['Order']['ship_to_street']." ".$order['Order']['ship_to_city']." ".$order['Order']['ship_to_zip']." ".$order['Country']['name'];
        $this->set(compact('ordertotals', 'status_text', 'addr', 'orders_lines', 'order', 'currency', 'linecount', 'is_write', 'is_shipment', 'warehouses', 'objectevents'));
    }

    /**
     * Repl Order Blanket Details page
     *
     */
    public function details_bl($id) {
        $order = $this->Order->find('first', array(
            'contain' => array('User', 'Supplier', 'Country', 'State', 'Shipment', 'Address', 'Address.Country', 'Address.State', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
            'conditions' => array('Order.id' => $id)
        ));

        if($order['Order']['blanket'] == 0) {
            $this->redirect(['controller' => 'replorders', 'action' => 'details', $id]);
        }

        if(!$order) {
            throw new NotFoundException(__('Order not found'));
        }

        $this->loadModel('OrdersBlanket');

        $is_own = ($order['Order']['user_id'] == $this->Auth->user('id'));
        $is_write = 0;
        $is_shipment = 0;
        if($order['Order']['user_id'] != $this->Auth->user('id')) {
            $products = $this->Access->getProducts($this->type, 'w', $order['Order']['user_id']);
            $warehouses = $this->Access->locationList($this->type, false, false, $order['Order']['user_id']);

            if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
                //throw new MethodNotAllowedException(__('Have no access'));
                $this->Session->setFlash(__('You do not have access to purchase orders. Please contact admin.'), 'admin/success');
                return $this->redirect(array('action' => 'index'));
            }
            if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
                //throw new MethodNotAllowedException(__('Have no access'));
                $this->Session->setFlash(__('You do not have access to purchase orders. Please contact admin.'), 'admin/success');
                return $this->redirect(array('action' => 'index'));
            }
            foreach ($warehouses as $w) {
                if(strpos($w, 'w') !== 0) {
                    $is_write = 1;
                }
            }
            
            $blanket = $this->OrdersBlanket->find('first', array(
                'conditions' => array('OrdersBlanket.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
            ));

            $orders_lines = $this->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'callbacks' => false
            ));

            $is_shipment = $this->Access->hasOrderShipmentAccess($id);
        } else {
            $is_write = 1;
            
            $blanket = $this->OrdersBlanket->find('first', array(
                'conditions' => array('OrdersBlanket.order_id' => $id),
                'contain' => array('Product', 'Warehouse')
            ));
            $orders_lines = $this->OrdersLine->find('all', array('conditions' => array('OrdersLine.order_id' => $id,'OrdersLine.user_id' => $this->Auth->user('id')), 'callbacks' => false));

            $warehouses = $this->Warehouse->find('list', ['fields'=>['Warehouse.id', 'Warehouse.name'], 'conditions' => array('Warehouse.user_id' => $this->Auth->user('id'))] );
            foreach ($warehouses as $key => $value) {
                $warehouses[$key] = 'rw';
            }
            $is_shipment = 1;
        }

        $ordertotals = $this->getordertotals($this->Order->read(null, $id), $orders_lines);
        if($blanket) {
            $ordertotals['blanket_total'] = $blanket['OrdersBlanket']['total_line'];
        } else {
            $ordertotals['blanket_total'] = 0.00;
        }

        $currency = $this->Currency->find('first', array('conditions' => array('id' => $order['User']['currency_id'])));
        $linecount = count($order['OrdersLine']);

        $objectevents = $this->EventRegister->getObjectEvent(2, $id, $this->Auth->user('id'));

        $addr = $order['Order']['ship_to_street']." ".$order['Order']['ship_to_city']." ".$order['Order']['ship_to_zip']." ".$order['Country']['name'];
        $this->set(compact('ordertotals', 'status_text', 'addr', 'orders_lines', 'order', 'currency', 'linecount', 'is_write', 'is_shipment', 'warehouses', 'objectevents', 'blanket'));
    }

    public function edit($id = null) {
        $this->layout = 'mtrd';
        
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Order->save($this->request->data)) {
                $this->Session->setFlash(__('Replenishment order no. %s has been saved.',$id), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Order could not be saved. Please, try again.'), 'admin/danger', array());
            }
        } else {
            $options = array('conditions' => array('Order.' . $this->Order->primaryKey => $id),'contain' => array(
                'Ordertype','Country','State','Supplier','OrdersLine.Product'=>array('fields' => array('id','name')),
                'Address', 'Address.State', 'Address.Country'
            ));
            $order = $this->Order->find('first', $options);
            if(empty($order['Address'])) {
                $order['Address']['street'] = $order['Order']['ship_to_street'];
                $order['Address']['city'] = $order['Order']['ship_to_city'];
                $order['Address']['zip'] = $order['Order']['ship_to_zip'];
                $order['Address']['country_id'] = $order['Order']['country_id'];
                $order['Address']['state_id'] = $order['Order']['state_id'];
                $order['Address']['stateprovince'] = $order['Order']['ship_to_stateprovince'];
                $order['Address']['phone'] = $order['Order']['ship_to_phone'];
            }
            $this->request->data = $order;
        }
        
        $states = $this->Order->State->find('list');
        $countries = $this->Order->Country->find('list');
        $suppliers = $this->Order->Supplier->find('list',array('conditions' => array('Supplier.user_id' => $this->Auth->user('id'))));
        $this->set(compact('states','countries','suppliers','order'));
    }

    /**
     * Repl Edit Order Details
     * Change channel to suppliers
     */
    public function edit_details($id) {
        $this->layout = false;
        $order = $this->Order->find('first', array(
            'fields' => array('Order.*', 'User.currency_id'),
            'contain' => array('User', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
            'conditions' => array('Order.id' => $id)
        ));
        if(!$order) {
            throw new NotFoundException(__('Order not found'));
        }

        $is_own = ($order['Order']['user_id'] == $this->Auth->user('id'));
        if(!$is_own) { // Check access, get products and allowed warehouse
            $products = $this->Access->getProducts($this->type, 'w', $order['Order']['user_id']);
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $order['Order']['user_id']);
            if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
            if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
        } else {
            $products = $this->OrdersLine->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.consumption' => true, 'Product.status_id' => 1)));
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $order['Order']['user_id']);
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Order->save($this->request->data)) {
                $order = $this->Order->find('first', array(
                    'contain' => array('Schannel', 'State', 'Country'),
                    'conditions' => array('Order.id' => $id)
                ));
                $response['order'] = $order;
                $response['action'] = 'success';
                $response['message'] = 'The order has been saved.';
                
            } else {
                $response = array('action' => 'error', 'errors' => $this->Order->validationErrors);
            }
            echo json_encode($response);
            exit;
        } else {
            $this->request->data = $order;
        }

        $currency = $this->Currency->find('first', array('conditions' => array('id' => $order['User']['currency_id'])));
        $schannels = $this->Access->schannelList($this->type, 'w', $order['User']['id']);

        $this->set(compact('order', 'schannels', 'currency'));
    }

    /**
     * Repl Edit Order Shipping Details
     * Just few word difference with S.O.
     */
    public function edit_shipping($id) {
        $this->layout = false;
        $order = $this->Order->find('first', array(
            'fields' => array('Order.*', 'User.currency_id'),
            'contain' => array('User', 'Address', 'Address.State', 'Address.Country', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
            'conditions' => array('Order.id' => $id)
        ));

        if(!$order) {
            throw new NotFoundException(__('Order not found'));
        }

        if(empty($order['Address'])) {
            $order['Address']['street'] = $order['Order']['ship_to_street'];
            $order['Address']['city'] = $order['Order']['ship_to_city'];
            $order['Address']['zip'] = $order['Order']['ship_to_zip'];
            $order['Address']['country_id'] = $order['Order']['country_id'];
            $order['Address']['state_id'] = $order['Order']['state_id'];
            $order['Address']['stateprovince'] = $order['Order']['ship_to_stateprovince'];
            $order['Address']['phone'] = $order['Order']['ship_to_phone'];
        }

        $is_own = ($order['Order']['user_id'] == $this->Auth->user('id'));
        if(!$is_own) { // Check access, get products and allowed warehouse
            $products = $this->Access->getProducts($this->type, 'w', $order['Order']['user_id']);
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $order['Order']['user_id']);
            if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
            if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
        } else {
            $products = $this->OrdersLine->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.consumption' => true, 'Product.status_id' => 1)));
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $order['Order']['user_id']);
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Order->save($this->request->data)) {
                $order = $this->Order->find('first', array(
                    'contain' => array('Schannel', 'State', 'Country'),
                    'conditions' => array('Order.id' => $id)
                ));
                $response['order'] = $order;
                $response['action'] = 'success';
                $response['message'] = 'The order has been saved.';
                
            } else {
                $response = array('action' => 'error', 'errors' => $this->Order->validationErrors);
            }
            echo json_encode($response);
            exit;
        } else {
            $this->request->data = $order;
        }

        $states = $this->Order->State->find('list');
        $countries = $this->Order->Country->find('list');

        $this->set(compact('order', 'countries', 'states'));
    }

    /**
     * Repl Order Add product
     * 
     */
    public function add_line($id, $lineid = null) {
        $this->layout = false;
        $order = $this->Order->find('first', array(
            'fields' => array('Order.id','Order.supplier_id','Order.ordertype_id', 'Order.user_id', 'Order.dcop_user_id', 'Order.external_orderid', 'User.currency_id'),
            'contain' => array('User', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
            'conditions' => array('Order.id' => $id)
        ));
        if(!$order) {
            throw new NotFoundException(__('Order not found'));
        }
        
        if($order['OrdersLine']) {
            $line_number = $order['OrdersLine'][0]['line_number'] + 10;
        } else {
            $line_number = 10;
        }
        $addpack = 0;
        $is_own = ($order['Order']['user_id'] == $this->Auth->user('id'));
        if(!$is_own) { // Check access, get products and allowed warehouse
            $allowedstatusesrepl = [1,12];
            $products = $this->Access->getProducts($this->type, 'w', $order['Order']['user_id'], ['Product.status_id' => $allowedstatusesrepl]);
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $order['Order']['user_id']);
            #pr($warehouses);
            if((empty($warehouses)) && $this->Auth->user('is_limited')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
            if((empty($warehouses)) && !$this->Auth->user('paid')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
        } else {
            $allowedstatusesrepl = [1,12];
            $products = $this->OrdersLine->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id') , 'Product.status_id' => $allowedstatusesrepl)));
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $order['Order']['user_id']);
        }

        $warehouses = array_values($warehouses);
        $warehouses = $warehouses[0];
        #pr($warehouses);
        #exit;

        if($this->request->is('post')) {
            if($this->request->data['OrdersLine']['quantity'] > 0 && isset($this->request->data['OrdersLine']['product_id'])) {
                $this->OrdersLine->create();
        
                $product = $this->Product->find('first', array('conditions' => array('Product.id' => $this->data['OrdersLine']['product_id']), 'callbacks' => false));

                $this->loadModel('Inventory');
                $inventoryRecord = $this->InventoryManager->getInventory($this->request->data['OrdersLine']['product_id'], $this->request->data['OrdersLine']['warehouse_id']);
                if(!$inventoryRecord) {
                    $response['status'] = false;
                    $response['message'] = 'We can\'t get invntory record for this location.';
                    echo json_encode($response);
                    exit;
                }

                if($lineid < 999999) {
                    ($this->data['OrdersLine']['unit_price']) ? $this->request->data('OrdersLine.unit_price', $this->request->data['OrdersLine']['unit_price']) : $this->request->data('OrdersLine.unit_price', 0);
                    $this->request->data('OrdersLine.status_id',1);
                    $this->request->data('OrdersLine.order_id', $id);
                    $this->request->data('OrdersLine.line_number', $line_number);
                    $this->request->data('OrdersLine.type', $order['Order']['ordertype_id']);
                    $this->request->data('OrdersLine.sku', $product['Product']['sku']);
                    $this->request->data('OrdersLine.user_id', $order['Order']['user_id']);
                    $this->request->data('OrdersLine.dcop_user_id', $this->Auth->user('id'));
                    $this->request->data('OrdersLine.sentqty',0);
                    $this->request->data('OrdersLine.receivedqty', 0);
                    $this->request->data('OrdersLine.damagedqty', 0);
                    $this->request->data('OrdersLine.serial_id', 0);
                    $this->request->data('OrdersLine.warehouse_id', $this->request->data['OrdersLine']['warehouse_id']);
                    $line_total = $this->data['OrdersLine']['quantity'] * $this->request->data['OrdersLine']['unit_price'];
                    $this->request->data('OrdersLine.total_line',$line_total);
            
                    if ($this->OrdersLine->save($this->request->data)) {
                        if(!empty($this->request->data['OrderSchedule']['delivery_date'])) {
                            $this->loadModel('OrderSchedule');
                            $data['OrderSchedule']['order_id'] = $id;
                            $data['OrderSchedule']['ordersline_id'] = $this->OrdersLine->id;
                            $data['OrderSchedule']['delivery_date'] = $this->request->data['OrderSchedule']['delivery_date'];
                            $this->OrderSchedule->save($data);
                        }
                        
                        $response['row'] = $this->OrdersLine->find('first', ['conditions' => ['OrdersLine.id' => $this->OrdersLine->id], 'callbacks' => false]);
                        $response['row']['OrderSchedule']['delivery_date'] = $this->request->data['OrderSchedule']['delivery_date'];
                        $response['action'] = 'success';
                        $response['message'] = 'The orders line has been added';
                        echo json_encode($response, JSON_NUMERIC_CHECK);
                        exit;
                    } else {
                        $response['status'] = false;
                        $response['message'] = 'The orders line could not be added. Please, try again.';
                        echo json_encode($response);
                        exit;
                    }
                } else {
                    $response['status'] = false;
                    $response['message'] = 'The orders line could not be added. Please, try again.';
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response['status'] = false;
                $response['message'] = 'The orders line could not be added. Either the product does not exist or quantity is negative or 0.';
                echo json_encode($response);
                exit;
            }
        }

        $linecount = count($order['OrdersLine']);
        $currency = $this->Currency->find('first', array('conditions' => array('id' => $order['User']['currency_id'])));

        $this->set(compact('order', 'warehouses', 'products', 'currency', 'addpack'));
    }

    /**
     * edit line method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit_line($id = null) {
        $this->layout = false;
        $options = array('conditions' => array('OrdersLine.id' => $id), 'contain' => array('User', 'Order', 'OrderSchedule'), 'callbacks'=>false);
        $order_line = $this->OrdersLine->find('first', $options);
        
        if($this->Auth->user('id') == $order_line['OrdersLine']['user_id']) {
            $products = $this->OrdersLine->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.status_id' => 1)));
        } else {
            $products = $this->Access->getProducts($this->type, 'w', $order_line['OrdersLine']['user_id']);
        }
        $warehouses = $this->Access->getLocations($this->type, false, 'w', $order_line['OrdersLine']['user_id']);

        if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
            throw new MethodNotAllowedException(__('Have no access'));
        }
        if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
            throw new MethodNotAllowedException(__('Have no access'));
        }
        
        if (!$this->OrdersLine->exists($id)) {
            throw new NotFoundException(__('Invalid orders line'));
        }

        $warehouses = array_values($warehouses);
        $warehouses = array_values($warehouses);
        $warehouses = $warehouses[0];

        if ($this->request->is(array('post', 'put'))) {
            if ($this->data['OrdersLine']['product_id'] != $order_line['OrdersLine']['product_id'] || $this->data['OrdersLine']['warehouse_id'] != $order_line['OrdersLine']['warehouse_id'] ) {
                $product = $this->Product->find('first', array('conditions' => array('Product.id' => $this->data['OrdersLine']['product_id']), 'callbacks' => false));

                $this->loadModel('Inventory');
                $inventoryRecord = $this->InventoryManager->getInventory($this->request->data['OrdersLine']['product_id'], $this->request->data['OrdersLine']['warehouse_id']);
                if(!$inventoryRecord) {
                    $response['status'] = false;
                    $response['message'] = 'We can\'t get invntory record for this location.';
                    echo json_encode($response);
                    exit;
                }
            }
            // recalculate total after update of price or qty
            $line_total = $this->data['OrdersLine']['quantity'] * $this->data['OrdersLine']['unit_price'];
            $this->request->data('OrdersLine.total_line', $line_total);
            $this->request->data['OrdersLine']['sentqty'] = 0;
            if(!empty($product)) {
                $this->request->data('OrdersLine.sku', $product['Product']['sku']);
            }
            $this->request->data('OrdersLine.dcop_user_id', $this->Auth->user('id'));

            if ($this->OrdersLine->save($this->request->data)) {
                
                $this->loadModel('OrderSchedule');
                $this->OrderSchedule->deleteAll(['order_id' => $order_line['Order']['id'], 'ordersline_id' => $this->OrdersLine->id], false);
                if(!empty($this->request->data['OrderSchedule']['delivery_date'])) {
                    $data['OrderSchedule']['order_id'] = $order_line['Order']['id'];
                    $data['OrderSchedule']['ordersline_id'] = $this->OrdersLine->id;
                    $data['OrderSchedule']['delivery_date'] = $this->request->data['OrderSchedule']['delivery_date'];
                    $this->OrderSchedule->save($data);
                }

                $response['row'] = $this->OrdersLine->find('first', ['conditions' => ['OrdersLine.id' => $id], 'callbacks' => false]);

                if($order_line['Order']['user_id'] != $this->Auth->user('id')) {
                    $orders_lines = $this->OrdersLine->find('all', array(
                        'order' => array('OrdersLine.line_number' => 'asc'),
                        'conditions' => array('OrdersLine.order_id' => $order_line['Order']['id'], 'OrdersLine.product_id' => array_keys($products)),
                        'callbacks' => false
                    ));
                } else {
                    $orders_lines = $this->OrdersLine->find('all', array('conditions' => array('OrdersLine.order_id' => $order_line['Order']['id'], 'OrdersLine.user_id' => $this->Auth->user('id')), 'callbacks' => false));
                }
                
                $response['ordertotals'] = $this->getordertotals($this->Order->read(null, $order_line['Order']['id']), $orders_lines);

                $response['action'] = 'success';
                $response['message'] = 'The orders line has been saved.';
                echo json_encode($response);
                exit;
            } else {
                $response['action'] = 'error';
                $response['message'] = 'The orders line could not be saved. Please, try again.';
                echo json_encode($response);
                exit;
            }
        } else {
            $this->request->data = $order_line;
        }
        
        $user = $this->User->find('first', ['conditions' => ['User.id' => $order_line['OrdersLine']['user_id']], 'fields' => array('User.*'), 'contain' => false]);
        $currency = $this->Currency->find('first', array('conditions' => array('id' => $user['User']['currency_id'])));
        $this->set(compact('products', 'warehouses', 'currency', 'order_line'));
    }

    /**
     * delete line method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete_line($id = null) {
        $this->OrdersLine->id = $id;
        $currentOrderLine = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.id' => $id), 'callbacks' => false));
        if (!$currentOrderLine) {
            throw new NotFoundException(__('Invalid orders line'));
        }
        if($currentOrderLine['OrdersLine']['user_id'] != $this->Auth->user('id')) {
            $products = $this->Access->getProducts($this->type, 'w', $currentOrderLine['OrdersLine']['user_id']);
            if(!array_key_exists($currentOrderLine['OrdersLine']['product_id'], $products)) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
        }
        $order_id = $currentOrderLine['OrdersLine']['order_id'];
        
        
        if ($this->OrdersLine->delete()) {

            $order = $this->Order->find('first', array(
                'contain' => array('User', 'Schannel', 'Country', 'State', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
                'conditions' => array('Order.id' => $order_id)
            ));
            
            if($order['Order']['user_id'] != $this->Auth->user('id')) {
                $products = $this->Access->getProducts($this->type, 'w', $order['Order']['user_id']);
                $orders_lines = $this->OrdersLine->find('all', array(
                    'order' => array('OrdersLine.line_number' => 'asc'),
                    'conditions' => array('OrdersLine.order_id' => $order_id, 'OrdersLine.product_id' => array_keys($products)),
                    'callbacks' => false
                ));
            } else {
                $orders_lines = $this->OrdersLine->find('all', array('conditions' => array('OrdersLine.order_id' => $order_id,'OrdersLine.user_id' => $this->Auth->user('id')), 'callbacks' => false));
            }
            
            $ordertotals = $this->getordertotals($this->Order->read(null, $order_id), $orders_lines);

            $response['action'] = 'success';
            $response['ordertotals'] = $ordertotals;
            $response['message'] = __('Order line number %s has been deleted from order number %s', $currentOrderLine['OrdersLine']['line_number'], $currentOrderLine['OrdersLine']['order_id']);
            
        } else {
            $response['action'] = 'success';
            $response['message'] = __('The orders line could not be deleted. Please, try again.');
        }
        echo json_encode($response);
        exit;
    }

    /**
     * issue order product method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function send_line($id = null, $shipment_id = null) {
        $this->layout = false;

        $orderline = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.id' => $id), 'callbacks' => false));

        if (!$orderline) {
            $response['status'] = false;
            $response['message'] = 'Invalid orders line';
            echo json_encode($response);
            exit;
        }
        $product = $orderline['Product'];

        if($orderline['OrdersLine']['user_id'] != $this->Auth->user('id')) {
            $products = $this->Access->getProducts($this->type, 'w', $orderline['OrdersLine']['user_id']);
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $orderline['OrdersLine']['user_id']);
            if(!array_key_exists($orderline['OrdersLine']['product_id'], $products)) {
                $response['status'] = false;
                $response['message'] = 'Have no access';
                echo json_encode($response);
                exit;
            }
        } else {
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $orderline['OrdersLine']['user_id']);
        }

        if($this->request->is(array('post', 'put'))) {
            $response = $this->InventoryManager->receiveLine(
                $orderline,
                $this->request->data['OrdersLine']['warehouse_id'],
                $this->request->data['OrdersLine']['receivedqty'],
                $shipment_id
            );
            if($response['status']) {
                // Update order status as partially processed
                $this->Order->id = $orderline['OrdersLine']['order_id'];
                $this->Order->saveField('status_id', 3);

                // Update shipment status as partially processed
                $this->updateshipmentprocess($orderline['OrdersLine']['order_id']);
            }
            echo json_encode($response);
            exit;
        } else {
            $this->request->data = $orderline;
        }

        $this->set(compact('product','orderline','warehouses'));
    }

    public function uploadcsv($index=1) {
        $this->layout = 'mtrd';

        $this->loadModel('Supplier');
        $supplier = $this->Supplier->find('list', ['fields'=>['Supplier.id', 'Supplier.name'], 'conditions' => ['Supplier.user_id' => $this->Auth->user('id')]]);

        if(sizeof($supplier) == 0) {
            $this->Session->setFlash(__('No supplier exist, you cannot create a purchase order. Click under "Partners" menu, "Suppliers" link to add it.'), 'admin/warning', array());
            return $this->redirect(array('action' => 'index'));
        }

        if ($this->request->is('post')) {
            #$target_path = WWW_ROOT."uploads/";
            #$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);

            $target_path = WWW_ROOT."uploads/";
            $file = basename($_FILES['uploadedfile']['name']);
            $fname = md5(time()) .'.'. pathinfo($file, PATHINFO_EXTENSION);
            $target_path = $target_path . $fname;

            if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
                $this->Csv->replaceDelimiters($target_path);
                $filesg = array( 'files' => array(array(
                    "name" => $fname,
                    "uname" => $_FILES['uploadedfile']['name'],
                    "size" => $_FILES['uploadedfile']['size'],
                    "thumbnailUrl" => "/theme/Mtro/assets/admin/layout/img/csv.png"
                )));
                header('Content-Type: application/json');
                echo json_encode($filesg,JSON_PRETTY_PRINT);
                exit();
            } else {
                $filesg = array( 'files' => array(array(
                    "name" => $_FILES['uploadedfile']['name'],
                    "size" => $_FILES['uploadedfile']['size'],
                    "error" => "Could not upload file. Please try again",
                )));
                header('Content-Type: application/json');
                echo json_encode($filesg,JSON_PRETTY_PRINT);
                exit();
            }
        }
    }

    public function importcsv($filename = null, $uname = null) { //$ordersdata = null
        $this->layout = null;

        $is_error = false;
        $msg = '';
        $order_errors = [];
        $ordersdata = [];
        $numords = 0;
        $success = 0;
        $warning = 0;
        $danger = 0;

        $lastorderdata['Order']['external_orderid'] = '';
        $neworders = array();

        $content = WWW_ROOT."uploads/".$filename;

        $file = fopen($content, "r");
        while ($line = fgetcsv($file, 1000)) {
            $numcols = count($line);
            if($numcols != 10) {
                $is_error = true;
                $msg = "Error: The CSV file should have 10 columns, but line 1 has ". $numcols ." columns.";
            }
            break;
        }
        fclose($file);
        
        if($is_error) {
            
        } else {
            $this->loadModel('Transfer');
            $this->Transfer->create();
            $log['user_id'] = $this->Auth->user('id');
            $log['type'] = Transfer::$types['orders'];
            $log['direction'] = Transfer::$direction['import'];
            $log['source'] = Transfer::$source['csv'];
            $log['source_id'] = 0;
            $log['status'] = Transfer::$status['started'];
            $log['recordscount'] = 0;
            $log['response'] = '';
            $this->Transfer->save($log);
            $transfer_id = $this->Transfer->id;

            $options = [];
            $filedata = $this->Csv->import($content, array(
                'Order.supplier',
                'Order.external_orderid',
                'Order.requested_delivery_date',
                'Order.comments',
                'Order.shipping_costs',
                'OrdersLine.line_number',
                'OrdersLine.sku',
                'OrdersLine.quantity',
                'OrdersLine.unit_price',
                'OrdersLine.comments'
            ), $options);

            $map = array_shift($filedata);

            $nextreforder = '';
            $oc = 0;
            $olc = 0;
            foreach ($filedata as $key => $forderdata) {
                $currentreforder = $forderdata['Order']['external_orderid'];
                if(isset($filedata[$key+1]['Order']['external_orderid']))
                    $nextreforder = $filedata[$key+1]['Order']['external_orderid'];
                $ordersdata[$oc]['Order'] = $forderdata['Order'];
                $ordersdata[$oc]['OrdersLine'][$olc]['sku'] = $forderdata['OrdersLine']['sku'];
                $ordersdata[$oc]['OrdersLine'][$olc]['quantity'] = $forderdata['OrdersLine']['quantity'];
                $ordersdata[$oc]['OrdersLine'][$olc]['unit_price'] = $forderdata['OrdersLine']['unit_price'];
                $ordersdata[$oc]['OrdersLine'][$olc]['line_number'] = $forderdata['OrdersLine']['line_number'];
                $ordersdata[$oc]['OrdersLine'][$olc]['comments'] = $forderdata['OrdersLine']['comments'];
                if($currentreforder == $nextreforder) {
                    $olc++;
                } else {
                    $oc++;
                    $olc =0;
                }
            }
        
            $numords = count($ordersdata);
            
            if($numords > 0 ) {
                foreach ($ordersdata as $key => $orderdata) {
                    // Find Supplier
                    $this->loadModel('Supplier');
                    $supplier = $this->Supplier->find('first', ['fields'=>['Supplier.id'], 'conditions' => ['Supplier.name' => $orderdata['Order']['supplier'], 'Supplier.user_id' => $this->Auth->user('id')]]);
                    if(!$supplier) {
                        $danger++;
                        $order_errors[] = "Error: Order #". $orderdata['Order']['external_orderid'] ." Supplier <strong>". $orderdata['Order']['supplier'] ."</strong> could not be found";
                    } else {
                        $this->Order->create();
                        $orderdata['Order']['user_id'] = $this->Auth->user('id');
                        $orderdata['Order']['dcop_user_id'] = $this->Auth->user('id');
                        $orderdata['Order']['ship_to_customerid'] = $this->Auth->user('username');
                        $orderdata['Order']['status_id'] = 14;
                        $orderdata['Order']['ordertype_id'] = 2;
                        $orderdata['Order']['interface'] = 1;
                        $orderdata['Order']['supplier_id'] = $supplier['Supplier']['id'];
                        $orderdata['Order']['transfer_id'] = $transfer_id;

                        if(!$this->Order->save($orderdata)) {
                            $danger++;
                            $erm = 'Error: Order #'. $orderdata['Order']['external_orderid'] .'<br>';
                            $validationerror = $this->Order->validationErrors;
                            if($validationerror) {
                                foreach ($validationerror as $key => $errortext) {
                                    $erm .= $this->errorstr."- ".$errortext[0]."<BR>";
                                }
                            } else {
                                $erm .= "Please check your import file";
                            }
                            $order_errors[] = $erm;
                        } else {
                            $success++;
                            $linenumber = 0;
                            foreach ($orderdata['OrdersLine'] as $key => $orderlinedata) {
                                $this->loadModel('Product');
                                
                                $product = $this->Product->getBySku($orderlinedata['sku'], $this->Auth->user('id'), array('Product.id','Product.packaging_material_id'));
                                if(empty($product)) {
                                    $pid = 1;
                                    $sku = '';
                                } else {
                                    $pid = $product['Product']['id'];
                                    $sku = $orderlinedata['sku'];
                                }

                                $unit_price = $orderlinedata['unit_price'];

                                
                                /*if(!isset($orderlinedata['line_number'])) {
                                    $orderldata['OrdersLine']['line_number'] = $linenumber;
                                }*/
                                if(isset($orderlinedata['OrdersLine']['line_number'])) {
                                    $linenumber = $orderlinedata['OrdersLine']['line_number'];
                                } else {
                                    $linenumber = $linenumber + 10;
                                }

                                

                                $this->loadModel('OrdersLine');
                                $orderldata['OrdersLine']['type'] = 2;
                                $orderldata['OrdersLine']['status_id'] = 1;
                                $orderldata['OrdersLine']['order_id'] = $this->Order->id;
                                $orderldata['OrdersLine']['line_number'] = $linenumber;
                                $orderldata['OrdersLine']['warehouse_id'] = $this->Session->read('default_warehouse');
                                $orderldata['OrdersLine']['product_id'] = $pid;
                                $orderldata['OrdersLine']['sentqty'] = 0;

                                $orderldata['OrdersLine']['quantity'] = $orderlinedata['quantity'];
                                $orderldata['OrdersLine']['unit_price'] = $unit_price;
                                $orderldata['OrdersLine']['total_line'] = $orderlinedata['quantity'] * $orderlinedata['unit_price'] ;
                                $orderldata['OrdersLine']['sku'] = $sku;
                                $orderldata['OrdersLine']['foc'] = 0;
                                $orderldata['OrdersLine']['user_id'] = $this->Auth->user('id');
                                $orderldata['OrdersLine']['comments'] = $orderlinedata['comments'];
                                $this->OrdersLine->saveAll($orderldata);
                                
                            }
                        }
                    }
                }
                // Update Log. Success.
                if($success > 0) {
                    $log['status'] = Transfer::$status['success'];
                } else {
                    $log['status'] = Transfer::$status['failed'];
                }
                $log['recordscount'] = $success;
                $msg['total_found'] = $numords;
                $msg['added'] = $success;
                $msg['updated'] = 0;
                $msg['errors_count'] = $danger;
                $msg['errors'] = $order_errors;
                $log['response'] = json_encode($msg); 
                $this->Transfer->save($log);
            } else {
                $is_error = true;
                $msg = "We not find any order info in ". $filename ." CSV file. Please be sure that it have properly structure.";

                 // Update Log. Error.
                $log['status'] = Transfer::$status['failed'];;
                $log['recordscount'] = 0;
                $log['response'] = $msg;
                $this->Transfer->save($log);
            }
        }
        
        $this->set(compact('filename', 'uname', 'is_error', 'msg', 'numords', 'order_errors', 'success', 'danger'));
    }

    /**
     * Cahnge complete order to paid
     *
     * @param id (int) - order id
     */
    public function paid($id) {
        $this->layout = false;
        $order = $this->Order->find('first', array(
            'fields' => array('Order.*', 'User.currency_id'),
            'contain' => array('User', 'Address', 'Address.State', 'Address.Country', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
            'conditions' => array('Order.id' => $id)
        ));

        if(!$order) {
            throw new NotFoundException(__('Order not found'));
        }

        if ($this->request->is(array('post', 'put'))) {
            if ($this->Order->save($this->request->data)) {
                $this->EventRegister->addEvent(2,55,$this->Auth->user('id'),$this->Order->id);
                $this->updateshipmentprocess($id);

                $order = $this->Order->find('first', array(
                    'contain' => array('User', 'Supplier', 'Country', 'State', 'Shipment', 'Address', 'Address.Country', 'Address.State', 'OrdersLine.OrderSchedule', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
                    'conditions' => array('Order.id' => $id)
                ));

                $response['action'] = 'success';
                $response['order'] = $order;
                $response['message'] = __('Order status set to Paid');
                echo json_encode($response);
                exit;
            } else {
                $response['action'] = 'error';
                $response['message'] = __('Order status could not be set to Paid. Please try again.');
                echo json_encode($response);
                exit;
            }

        }

        $this->set(compact('order'));
    }

    public function downloadsamplefile() {

        $filename = $target_path = WWW_ROOT."sampledata/Delivrd_PO_sample.csv"; // of course find the exact filename....
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for certain browsers
        header('Content-Type: application/csv');

        header('Content-Disposition: attachment; filename="'. basename($filename) . '";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filename));

        readfile($filename);
        exit;
    }
}