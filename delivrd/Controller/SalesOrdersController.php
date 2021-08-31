<?php
App::uses('AppController', 'Controller');
App::uses('OrdersController', 'Controller');
/**
 * SalesOrders Controller
 *
 * @property Order $Order
 * @property PaginatorComponent $Paginator
 */
class  SalesOrdersController extends OrdersController {

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
    public $components = array('Paginator','EventRegister','RequestHandler','Csv.Csv','Search.Prg','Shopfy','WooCommerce', 'Cookie', 'InventoryManager');

    /**
    * Models
    *
    * @var array
    */
    public $uses = array('Order', 'OrdersLine', 'OrdersCosts', 'Product', 'User', 'Warehouse', 'Currency');

    public $paginate = array();
    public $types = [1 => 'S.O.', 2 => 'P.O.'];


    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function showReleasePopup() {
        $this->layout = false;
        if(!empty($this->request->data['message']) && $this->request->data['message'] == 1) {
            $this->Cookie->write('message', 1);
            $response['popup'] = $this->request->data['message'];
        } else {
            $response['popup'] = 0;
        }
        $response['action'] = 'dontshow';
        $response['message'] = 'Success';
        $this->set('response', json_encode($response));
        $this->response->type('json');
    }


    /**
    * index method with angular ui.bootstrap
    *
    * @return void
    */
    public function index($product_id = 0, $created = null) {
        $this->layout = 'mtrd';

        $products = $this->Access->getProducts($this->types[1]);
        $warehouses = $this->Access->locationList($this->types[1]);
        $schannels = $this->Access->schannelList();

        $limit = $this->Auth->user('list_limit');//10;
        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );

        $is_have_access = true;
        if((empty($products) || empty($warehouses) || empty($schannels)) && $this->_authUser['User']['is_limited']) {
            $is_have_access = false;
        }
        
        if((empty($products) || empty($warehouses) || empty($schannels)) && !$this->_authUser['User']['paid']) {
            //throw new MethodNotAllowedException(__('Have no access'));
            $is_have_access = false;
        }
        $is_write = true;
        if($this->_authUser['User']['is_limited'] || !$this->_authUser['User']['paid']) {
            $is_write = false;
            if(isset($this->Access->_access[$this->types[1]])) {
                foreach ($this->Access->_access[$this->types[1]] as $value) {
                    if(strpos($value['NetworksAccess']['access'], 'w') !== false) {
                        $is_write = true;
                    }
                }
            }
        }
        
        // Get Networks with S.O. alowed access.
        $networks = [];
        if(isset($this->Access->_access['S.O.'])) { // Get networks which alowed S.O. access
            foreach ($this->Access->_access[$this->types[1]] as $acc) {
                if(!isset($networks[$acc['Network']['created_by_user_id']])) {
                    $networks[$acc['Network']['created_by_user_id']]['name'] = $acc['Network']['name'];
                    $networks[$acc['Network']['created_by_user_id']]['access'] = $acc['NetworksAccess']['access'];
                } else {
                    $networks[$acc['Network']['created_by_user_id']]['access'] .= $acc['NetworksAccess']['access'];
                }
            }
        }

        $ship_networks = [];
        if(isset($this->Access->_access['Shipments'])) { // Get networks which alowed S.O. access
            foreach ($this->Access->_access['Shipments'] as $acc) {
                if(!isset($ship_networks[$acc['Network']['created_by_user_id']])) {
                    $ship_networks[$acc['Network']['created_by_user_id']]['name'] = $acc['Network']['name'];
                    $ship_networks[$acc['Network']['created_by_user_id']]['access'] = $acc['NetworksAccess']['access'];
                } else {
                    $ship_networks[$acc['Network']['created_by_user_id']]['access'] .= $acc['NetworksAccess']['access'];
                }
            }
        }

        $popup = $this->Cookie->read('message');
        if(empty($popup)) {
            $popup = 0;
        }

        $this->set(compact('orders', 'schannels', 'is_write', 'networks', 'ship_networks', 'popup', 'limit', 'options', 'is_have_access', 'product_id'));
    }

    public function ajax_index() {
        $schannels = $this->Access->schannelList();
        
        // Get Networks with S.O. alowed access.
        $networks = [];
        if(isset($this->Access->_access['S.O.'])) { // Get networks which alowed S.O. access
            $networks = Set::combine($this->Access->_access[$this->types[1]], '{n}.Network.created_by_user_id', '{n}.Network.name');
        }
        
        $limit = $this->Auth->user('list_limit');
        if($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }

        $product_id = 0;
        if($this->request->query('product_id')) {
            $product_id = $this->request->query('product_id');
        }
        $orderBy = 'Order.modified';
        if($this->request->query('sortby')) {
            $orderBy = $this->request->query('sortby');
        }
        $orderDir = 'DESC';
        if($this->request->query('sortdir')) {
            $orderDir = $this->request->query('sortdir');
        }

        $this->Paginator->settings = array(
            'limit' => $limit,
            'contain' => array('Supplier','Status','Schannel'),
            "fields" => array(
                "Order.id",
                "Order.user_id",
                "Order.supplier_id",
                "Order.supplysource_id",
                "Order.status_id",
                "Order.created",
                "Supplier.name",
                "Status.name",
                "Schannel.name",
                "Order.external_orderid",
                "Order.ship_to_customerid"
            ),
            'group' => 'Order.id',
            'order' => array($orderBy => $orderDir)
        );

        $conditions = array();
        $conditions['Order.ordertype_id'] = 1;
        if($this->request->query('status_id') != 50) {
            $conditions['Order.status_id !='] = 50;
        }

        $allowed_channels = [];
        if(isset($this->Access->_access[$this->types[1]])) {
            foreach ($schannels as $key => $channel) {
                if($key != 'My Schannels') {
                    $allowed_channels = array_merge($allowed_channels, array_keys($channel));
                }
            }
        }

        if($allowed_channels && $networks) { // Result will include network S.O.
            // For network we looks all user orders and all orders from allowed channels and alowed S.O. access
            $conditions['OR'] = ['Order.user_id' => $this->Auth->user('id')];
            $user_ids = array_keys($networks);
            $conditions['OR'][] = ['Order.schannel_id' => $allowed_channels, 'Order.user_id' => $user_ids];
        } else { // Only user S.O.
            $conditions['Order.user_id'] = $this->Auth->user('id');
            if($product_id) {
                $orderIds = $this->Order->OrdersLine->find('list', array('fields' => array('OrdersLine.order_id'), 'conditions' => array('OrdersLine.product_id' => $product_id), 'callbacks' => false));
                $conditions['Order.id'] = array_keys($orderIds);
            }
        }

        if ($this->request->query('createdfrom')) {
            $conditions['Order.created >='] = $this->request->query('createdfrom');
        }

        if($this->request->query('showall') == 1) {
            $this->User->saveSetting($this->Auth->user('id'), 'so_filter', []);
        } else {
            if ($this->request->query('status_id')) {
                if($this->request->query('status_id') == 'all') {
                    $this->User->saveSetting($this->Auth->user('id'), 'so_filter', []);
                } else if($this->request->query('status_id') == 50) {
                    //$this->User->saveSetting($this->Auth->user('id'), 'so_filter', []);
                    $conditions['Order.status_id'] = $this->request->query('status_id');
                } else {
                    $so_filter = $this->request->query('status_id');
                    $this->User->saveSetting($this->Auth->user('id'), 'so_filter', $so_filter);
                    $conditions['Order.status_id'] = $this->request->query('status_id');
                }
            } else {
                $this->User->saveSetting($this->Auth->user('id'), 'so_filter', []);
            }
        }
        if ($this->request->query('schannel_id')) {
            $conditions['Order.schannel_id'] = $this->request->query('schannel_id');
        }
        if ($this->request->query('searchby')) {
            $conditions['AND']['OR']['Order.ship_to_customerid like'] = '%'. $this->request->query('searchby') .'%';
            $conditions['AND']['OR']['Order.id like'] = '%'. $this->request->query('searchby') .'%';
            $conditions['AND']['OR']['Order.external_orderid like'] = '%'. $this->request->query('searchby') .'%';
        }

        if (isset($product_id) && $product_id) {
            $orderIds = $this->Order->OrdersLine->find('list', [
                        'fields' => array('OrdersLine.order_id'),
                        'conditions' => array('OrdersLine.type' => 1,'OrdersLine.product_id' => $product_id, 'OrdersLine.order_id !=' => '4294967294'),
                        'group' => 'OrdersLine.order_id',
                        'callbacks' => false
                    ]);
            $conditions['Order.id'] = $orderIds;
        }

        $this->paginate['recursive'] = -1;
        $orders = $this->Paginator->paginate($conditions);

        $response['draw'] = 1;
        $response['recordsTotal'] = $this->request->params['paging']['Order']['count'];
        $response['rows_count'] = count($orders);
        $response['rows'] = $orders;

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function customer_report() {
        $this->layout = 'mtrd';

        $limit = $this->Auth->user('list_limit');
        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );

        $customers = $this->Order->find('all', [
            'fields' => ['Order.ship_to_customerid'],
            'conditions' => ['Order.user_id' => $this->Auth->user('id'), 'Order.ordertype_id' => 1, 'Order.ship_to_customerid !=' => ''],
            'contain' => false,
            'order' => 'Order.ship_to_customerid ASC',
            'group' => 'Order.ship_to_customerid'
        ]);
        $customer_list = [];
        foreach ($customers as $customer) {
            $customer_list[trim($customer['Order']['ship_to_customerid'])] = trim($customer['Order']['ship_to_customerid']);
        }

        $orderlines = [];
        $this->set(compact('orderlines', 'customer_list', 'limit', 'options'));
    }

    public function customer_report_js() {
        $limit = $this->Auth->user('list_limit');
        if($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }
        
        $page = 1;
        if(isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }

        $response = $this->getCustomerReport($this->request->query, $page, $limit);

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function customer_report_csv() {
        $limit = 20000;
        $page = 1;

        $response = $this->getCustomerReport($this->request->data['Order'], $page, $limit);
        $lines = $response['rows'];

        $_serialize = 'lines';
        $_header = array('Order#', 'Customer', 'Product', 'Location', 'Qty', 'Unit Price', 'Total Price','Created','Remarks');
        $_extract = array('Order.id', 'Order.ship_to_customerid', 'Product.name', 'Warehouse.name','OrdersLine.quantity', 'OrdersLine.unit_price','OrdersLine.total_line','OrdersLine.created', 'OrdersLine.comments');

        $file_name = "Delivrd_".date('Y-m-d-His')."_report.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('lines', '_serialize', '_header', '_extract'));
    }

    public function getCustomerReport($query, $page=1, $limit) {
        
        $conditions = ['OrdersLine.user_id' => $this->Auth->user('id'), 'Order.ordertype_id' => 1];
        if(isset($query['customer'])) {
            foreach ($query['customer'] as $value) {
                $conditions['AND'][1]['OR'][] = ['Order.ship_to_customerid' => $value];
            }
        }

        if(isset($query['status_id'])) {
            foreach ($query['status_id'] as $value) {
                $conditions['AND'][2]['OR'][] = ['Order.status_id' => $value];
            }
        }

        $orderBy = 'OrdersLine.modified';
        if(isset($query['sortby'])) {
            $orderBy = $query['sortby'];
        }
        $orderDir = 'DESC';
        if(isset($query['sortdir'])) {
            $orderDir = $query['sortdir'];
        }

        $this->OrdersLine->recursive = -1;
        $options = array(
            'conditions' => $conditions,
            'fields' => array(
                'OrdersLine.order_id',
                'OrdersLine.unit_price',
                'OrdersLine.quantity',
                'OrdersLine.total_line',
                'OrdersLine.created',
                'OrdersLine.comments',
                'Order.ship_to_customerid',
                'Order.status_id',
                'Warehouse.name',
                'Product.name',
                'Product.sku',
            ),
            'joins' => array(
                array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id'
                    )
                ),
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                ),
                array('table' => 'warehouses',
                    'alias' => 'Warehouse',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.warehouse_id = Warehouse.id'
                    )
                ),
                
            ),
            'limit' => $limit,
            'page' => $page,
            'order' => array($orderBy => $orderDir)
        );
        $orderlines = $this->OrdersLine->find('all', $options);
        
        $recordsCount = $this->OrdersLine->find('count', [
            'conditions' => $conditions,
            'joins' => array(
                array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id'
                    )
                ),
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                ),
                array('table' => 'warehouses',
                    'alias' => 'Warehouse',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.warehouse_id = Warehouse.id'
                    )
                ),
            )
        ]);

        $recordsAmount = $this->OrdersLine->find('first', [
            'fields' => ['MIN(OrdersLine.created) as start', 'SUM(OrdersLine.total_line) as total'],
            'conditions' => $conditions,
            'joins' => array(
                array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id'
                    )
                ),
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                ),
                array('table' => 'warehouses',
                    'alias' => 'Warehouse',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.warehouse_id = Warehouse.id'
                    )
                ),
            )
        ]);

        $month_conditions = $conditions;
        $month_conditions['OrdersLine.created >='] = date('Y-m') .'-01';
        $lastMonthAmount = $this->OrdersLine->find('first', [
            'fields' => ['SUM(OrdersLine.total_line) as total'],
            'conditions' => $month_conditions,
            'joins' => array(
                array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id'
                    )
                ),
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                ),
                array('table' => 'warehouses',
                    'alias' => 'Warehouse',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.warehouse_id = Warehouse.id'
                    )
                ),
            )
        ]);

        $year_conditions = $conditions;
        $year_conditions['OrdersLine.created >='] = date('Y') .'-01-01';
        $lastYearAmount = $this->OrdersLine->find('first', [
            'fields' => ['SUM(OrdersLine.total_line) as total'],
            'conditions' => $year_conditions,
            'joins' => array(
                array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id'
                    )
                ),
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                ),
                array('table' => 'warehouses',
                    'alias' => 'Warehouse',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.warehouse_id = Warehouse.id'
                    )
                ),
            )
        ]);

        $d1 = new DateTime($recordsAmount[0]['start']);
        $d2 = new DateTime(date('Y-m-d'));
        $count_of_month = ($d1->diff($d2)->m + ($d1->diff($d2)->y*12));
        if($count_of_month == 0) {
            $count_of_month = 1;
        }

        $response['recordsTotal'] = $recordsCount;
        $response['rows_count'] = count($orderlines);

        $response['total_amount'] = number_format($recordsAmount[0]['total'], 2, '.', ',');
        $response['total_month_amount'] = number_format($lastMonthAmount[0]['total'], 2, '.', ',');
        $response['total_year_amount'] = number_format($lastYearAmount[0]['total'], 2, '.', ',');
        $response['monthly_average'] = number_format( ($recordsAmount[0]['total']/$count_of_month), 2, '.', ',');
        $response['count_of_month'] = $count_of_month;

        $response['rows'] = $orderlines;
        

        return $response;
    }


    public function profit_report() {
        $this->layout = 'mtrd';

        $limit = $this->Auth->user('list_limit');
        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );

        $customers = $this->Order->find('all', [
            'fields' => ['Order.ship_to_customerid'],
            'conditions' => ['Order.user_id' => $this->Auth->user('id'), 'Order.ordertype_id' => 1, 'Order.ship_to_customerid !=' => ''],
            'contain' => false,
            'order' => 'Order.ship_to_customerid ASC',
            'group' => 'Order.ship_to_customerid'
        ]);
        $customer_list = [];
        foreach ($customers as $customer) {
            $customer_list[trim($customer['Order']['ship_to_customerid'])] = trim($customer['Order']['ship_to_customerid']);
        }

        $products_list = $this->Product->find('list', ['conditions' => ['Product.user_id' => $this->Auth->user('id')]]);

        #$this->getProfitReport([]);
        #exit;

        $orderlines = [];
        $this->set(compact('orderlines', 'customer_list', 'products_list', 'limit', 'options'));
    }

    public function profit_report_js() {
        $limit = $this->Auth->user('list_limit');
        if($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }
        
        $page = 1;
        if(isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }

        $response = $this->getProfitReport($this->request->query, $page, $limit);

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function getProfitReport($query, $page=1, $limit=20) {
        
        $conditions = ['OrdersLine.user_id' => $this->Auth->user('id'), 'Order.ordertype_id' => 1];
        if(isset($query['customer'])) {
            foreach ($query['customer'] as $value) {
                $conditions['AND'][1]['OR'][] = ['Order.ship_to_customerid' => $value];
            }
        }

        if(isset($query['products'])) {
            foreach ($query['products'] as $value) {
                $conditions['AND'][3]['OR'][] = ['OrdersLine.product_id' => $value];
            }
        }

        if(isset($query['status_id'])) {
            foreach ($query['status_id'] as $value) {
                $conditions['AND'][2]['OR'][] = ['Order.status_id' => $value];
            }
        }
        
        if(isset($query['start_date'])) {
            $conditions['AND'][] = ['OrdersLine.created >=' => $query['start_date']];
        }

        if(isset($query['end_date'])) {
            $conditions['AND'][] = ['OrdersLine.created <=' => $query['end_date']];
        }

        $orderBy = 'OrdersLine.modified';
        if(isset($query['sortby'])) {
            $orderBy = $query['sortby'];
        }
        $orderDir = 'DESC';
        if(isset($query['sortdir'])) {
            $orderDir = $query['sortdir'];
        }

        $this->OrdersLine->recursive = -1;
        $options = array(
            'conditions' => $conditions,
            'fields' => array(
                'OrdersLine.order_id',
                'OrdersLine.unit_price',
                'OrdersLine.quantity',
                'OrdersLine.total_line',
                'OrdersLine.created',
                'Order.ship_to_customerid',
                'Order.status_id',
                'Product.id',
                'Product.name',
                'Product.sku',
                'Product.value as product_price',
                '(SELECT order_id FROM orders_lines WHERE type=2 AND product_id = OrdersLine.product_id ORDER BY created DESC LIMIT 1) as purchase_id',
                '(SELECT unit_price FROM orders_lines WHERE type=2 AND product_id = OrdersLine.product_id ORDER BY created DESC LIMIT 1) as purchase_price'
            ),
            'joins' => array(
                array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id'
                    )
                ),
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                )
                
            ),
            'limit' => $limit,
            'page' => $page,
            'order' => array($orderBy => $orderDir)
        );
        $orderlines = $this->OrdersLine->find('all', $options);
        
        $recordsCount = $this->OrdersLine->find('count', [
            'conditions' => $conditions,
            'joins' => array(
                array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id'
                    )
                ),
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                )
            )
        ]);

        $response['recordsTotal'] = $recordsCount;
        $response['rows_count'] = count($orderlines);
        $response['rows'] = $orderlines;

        return $response;
    }

    public function profit_totals() {
        $query = $this->request->query;
        $conditions = ['OrdersLine.user_id' => $this->Auth->user('id'), 'Order.ordertype_id' => 1];
        if(isset($query['customer'])) {
            foreach ($query['customer'] as $value) {
                $conditions['AND'][1]['OR'][] = ['Order.ship_to_customerid' => $value];
            }
        }

        if(isset($query['products'])) {
            foreach ($query['products'] as $value) {
                $conditions['AND'][3]['OR'][] = ['OrdersLine.product_id' => $value];
            }
        }

        if(isset($query['status_id'])) {
            foreach ($query['status_id'] as $value) {
                $conditions['AND'][2]['OR'][] = ['Order.status_id' => $value];
            }
        }
        
        if(isset($query['start_date'])) {
            $conditions['AND'][] = ['OrdersLine.created >=' => $query['start_date']];
        }

        if(isset($query['end_date'])) {
            $conditions['AND'][] = ['OrdersLine.created <=' => $query['end_date']];
        }

        $orderBy = 'OrdersLine.modified';
        if(isset($query['sortby'])) {
            $orderBy = $query['sortby'];
        }
        $orderDir = 'DESC';
        if(isset($query['sortdir'])) {
            $orderDir = $query['sortdir'];
        }

        $this->OrdersLine->recursive = -1;

        // Average profit margin
        // Total profit
        $totals = $this->OrdersLine->find('first', array(
            'conditions' => $conditions,
            'fields' => array(
                'SUM(OrdersLine.unit_price * OrdersLine.quantity) as total_sale',
                'SUM((SELECT unit_price FROM orders_lines WHERE type=2 AND product_id = OrdersLine.product_id ORDER BY created DESC LIMIT 1) * OrdersLine.quantity) as purchase_total'
            ),
            'joins' => array(
                array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id'
                    )
                ),
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                ),
            )
        ));

        // Average profit per line
        $average = $this->OrdersLine->find('first', array(
            'conditions' => $conditions,
            'fields' => array(
                'SUM(OrdersLine.unit_price) as total_sale',
                'SUM((SELECT unit_price FROM orders_lines WHERE type=2 AND product_id = OrdersLine.product_id ORDER BY created DESC LIMIT 1)) as purchase_total'
            ),
            'joins' => array(
                array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id'
                    )
                ),
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                ),
            )
        ));

        $response['total_profit'] = number_format(($totals[0]['total_sale'] - $totals[0]['purchase_total']), 2, '.', ',');
        if($totals[0]['total_sale'] > 0) {
            $response['profit_margin'] = number_format((100*(($totals[0]['total_sale'] - $totals[0]['purchase_total'])/$totals[0]['total_sale'])), 2, '.', ',');
        } else {
            $response['profit_margin'] = '0.00';
        }
        if($average[0]['total_sale'] > 0) {
            $response['average_margin'] = number_format((100*(($average[0]['total_sale'] - $average[0]['purchase_total'])/$average[0]['total_sale'])), 2, '.', ',');
        } else {
            $response['average_margin'] = '0.00';
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
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
                $this->EventRegister->addEvent(1,55,$this->Auth->user('id'),$this->Order->id);
                $this->updateshipmentprocess($id);

                $order = $this->Order->find('first', array(
                    'contain' => array('User', 'Supplier', 'Schannel', 'Country', 'State', 'Shipment', 'Address', 'Address.Country', 'Address.State', 'OrdersLine.OrderSchedule', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
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

    /**
    * index method with angular ui.bootstrap
    *
    * @return void
    */
    public function canceled() {
        $this->layout = 'mtrd';

        $products = $this->Access->getProducts($this->types[1]);
        $warehouses = $this->Access->locationList($this->types[1]);
        $schannels = $this->Access->schannelList();

        $limit = 10;
        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );

        $is_have_access = true;
        if((empty($products) || empty($warehouses) || empty($schannels)) && $this->Auth->user('is_limited')) {
            //throw new MethodNotAllowedException(__('Have no access'));
            $is_have_access = false;
        }
        
        if((empty($products) || empty($warehouses) || empty($schannels)) && !$this->Auth->user('paid')) {
            //throw new MethodNotAllowedException(__('Have no access'));
            $is_have_access = false;
        }
        $is_write = true;
        if($this->Auth->user('is_limited') || !$this->Auth->user('paid')) {
            $is_write = false;
            if(isset($this->Access->_access[$this->types[1]])) {
                foreach ($this->Access->_access[$this->types[1]] as $value) {
                    if(strpos($value['NetworksAccess']['access'], 'w') !== false) {
                        $is_write = true;
                    }
                }
            }
        }
        
        // Get Networks with S.O. alowed access.
        $networks = [];
        if(isset($this->Access->_access['S.O.'])) { // Get networks which alowed S.O. access
            $networks = [];
            foreach ($this->Access->_access[$this->types[1]] as $acc) {
                if(!isset($networks[$acc['Network']['created_by_user_id']])) {
                    $networks[$acc['Network']['created_by_user_id']]['name'] = $acc['Network']['name'];
                    $networks[$acc['Network']['created_by_user_id']]['access'] = $acc['NetworksAccess']['access'];
                } else {
                    $networks[$acc['Network']['created_by_user_id']]['access'] .= $acc['NetworksAccess']['access'];
                }
            }
        }

        $ship_networks = [];
        if(isset($this->Access->_access['Shipments'])) { // Get networks which alowed S.O. access
            foreach ($this->Access->_access['Shipments'] as $acc) {
                if(!isset($ship_networks[$acc['Network']['created_by_user_id']])) {
                    $ship_networks[$acc['Network']['created_by_user_id']]['name'] = $acc['Network']['name'];
                    $ship_networks[$acc['Network']['created_by_user_id']]['access'] = $acc['NetworksAccess']['access'];
                } else {
                    $ship_networks[$acc['Network']['created_by_user_id']]['access'] .= $acc['NetworksAccess']['access'];
                }
            }
        }

        $this->set(compact('orders', 'schannels', 'is_write', 'networks', 'ship_networks', 'limit', 'options', 'is_have_access'));
    }

    /**
     * Create Sales Order
     *
     */
    public function create() {
        $this->layout = 'mtrd';

        $products = $this->Access->getProducts('S.O.', 'w');
        $warehouses = $this->Access->locationList('S.O.', false, 'w');
        $schannels = $this->Access->schannelList('S.O.', 'w');
        $default_schannel = 0;
        if(count($schannels) == 1) {
            $tmp_sch = $schannels;
            $tmp_sch = array_shift($tmp_sch);
            if(count($tmp_sch) == 1) {
                $default_schannel = key($tmp_sch);
            }
        }

        if((empty($products) || empty($warehouses)) && $this->_authUser['User']['is_limited']) {
            throw new MethodNotAllowedException(__('Have no access'));
        }
        if((empty($products) || empty($warehouses)) && !$this->_authUser['User']['paid']) {
            throw new MethodNotAllowedException(__('Have no access'));
        }

        //if no products exist, do not go to order creation page
        $this->loadModel('Product');
        $productscount = $this->Product->find('count',array('conditions' => array('Product.user_id' => $this->Auth->user('id'))));
        if($productscount == 0 && count($products) == 0) {
            $this->Session->setFlash(__('No products exist, you cannot create a cutsomer order. Pleaes create a product and packaging material.'),'default',array('class'=>'alert alert-warning'));
            return $this->redirect(array('action' => 'index'));
        }

        if ($this->request->is('post')) {
            
            if( $this->request->data('is_default_country') == 'on' ) {
                $this->User->id = $this->Auth->user('id');
                $this->User->saveField('default_country_id', intval($this->request->data['Address']['country_id']));
                $this->Session->write('Auth.User.default_country_id', intval($this->request->data['Address']['country_id']));
            } /*else {
                $this->User->id = $this->Auth->user('id');
                $this->User->saveField('default_country_id', 0);
                $this->Session->write('Auth.User.default_country_id', 0);
            }*/
            
            $this->loadModel('Address');
            $this->loadModel('Schannel');
            
            if(isset($this->request->data['schannel_id']) && !empty($this->request->data['schannel_id'])) {
                $this->request->data('Order.schannel_id',$this->request->data['schannel_id']);
            }

            $schannel = $this->Schannel->find('first', ['conditions' => array('Schannel.id' => $this->request->data['Order']['schannel_id']), 'fields' => array('Schannel.user_id')]);

            $this->Order->create();
            $this->request->data('Order.dcop_user_id', $this->Auth->user('id'));
            $this->request->data('Order.user_id', $schannel['Schannel']['user_id']);
            $this->request->data('Address.user_id',$this->Auth->user('id'));
            $this->request->data('Order.status_id',14);
            $this->request->data('Order.interface',0);
            $this->request->data('Order.ordertype_id',1);
            $this->request->data('Address.street',htmlspecialchars($this->request->data['Address']['street']));
            $this->request->data('Address.city',htmlspecialchars($this->request->data['Address']['city']));
            $this->request->data('Address.stateprovince',htmlspecialchars($this->request->data['Address']['stateprovince']));
            $this->request->data('Address.zip',htmlspecialchars($this->request->data['Address']['zip']));

            if(!empty($this->request->data['Address']['state_id']) && $this->request->data['Address']['country_id'] == 1) {
                $statedate = $this->Address->State->findById($this->request->data['Address']['state_id']);      
                $this->request->data('Address.stateprovince', $statedate['State']['name']);
            } else {
               $this->request->data('Address.state_id', ''); 
            }
            unset($this->Address->validate['street']);
            unset($this->Address->validate['city']);
            unset($this->Address->validate['country_id']);
            if ($this->Order->saveAll($this->request->data)) {     
                $this->EventRegister->addEvent(2,1,$this->Auth->user('id'),$this->Order->id);
                $this->Session->setFlash(__('New order has been created, number %s',$this->Order->id), 'admin/success', array());     
                return $this->redirect(array('controller' => 'salesorders', 'action' => 'details', $this->Order->id,'?' => array('new' => 1)));
            } else {
                $this->Session->setFlash(__('The Order could not be saved. Please, try again.'), 'admin/danger', array());
            }
        }

        $states = $this->Order->State->find('list');
        $countries = $this->Order->Country->find('list');
        $ordertypes = $this->Order->Ordertype->find('list');
        $statuses = $this->Order->Status->find('list');

        $this->set(compact('schannels', 'states', 'countries', 'ordertypes', 'statuses', 'default_schannel'));
    }

    /**
     * Create Repl. Order for exists Sales Order
     *
     */
    public function createrep($id = null) {
        $this->loadModel('OrdersLine');
        $options = array('conditions' => array('Order.id' => $id), 'contain' => array('User', 'OrdersLine', 'Address', 'OrdersLine.Product' => array('fields' => array('id','name')), 'OrdersLine.Product.Productsupplier.Product'));
        $order = $this->Order->find('first', $options);
        $repOrder = $this->Order->find('first', array('conditions' => array('Order.external_orderid' => $order['Order']['external_orderid'], 'Order.ordertype_id' => 2)));
        if(empty($repOrder)) {
            // Check Access
            if($this->Access->hasOrderAccess($id)) {
                //echo 'Has access';
            #pr($order['OrdersLine']);
            #exit;
                $suppl = array(); 
                $products = array(); 
                if(!empty($order['OrdersLine'])) {
                    foreach ($order['OrdersLine'] as $key => $suppliers) {
                        if(empty($suppliers['Product']['Productsupplier'])) {
                            $products[$key] = $suppliers['Product']['name'];
                        }
                        foreach ($suppliers['Product']['Productsupplier'] as $key => $supplier) {
                            if($supplier['status'] != 'no') {
                                $suppl[$supplier['supplier_id']][] = $supplier; 
                            }
                        }   
                    }
                    
                    if(!empty($suppl)) {
                        foreach ($suppl as $key => $listsuppl) {
                            $data = array(
                                'Order' => array(
                                    'ordertype_id' => 2,
                                    'external_orderid' => $order['Order']['external_orderid'],
                                    'user_id' => $order['User']['id'],
                                    'ship_to_customerid' => $order['Order']['ship_to_customerid'],
                                    'dcop_user_id' => $this->Auth->user('id'),
                                    'supplier_id'  => $key,
                                    'shipping_costs' => $order['Order']['ordertype_id'],
                                    'status_id' => $order['Order']['status_id'],
                                    'ship_to_street' => $order['User']['street'],
                                    'ship_to_city' => $order['User']['city'],
                                    'ship_to_zip' => $order['User']['zip'],
                                    'state_id' => $order['User']['state_id'],
                                    'ship_to_stateprovince' => $order['User']['stateprovince'],
                                    'country_id' => $order['User']['country_id'],
                                    'created' => $order['Order']['created'],
                                )
                            );
                            
                            $data['Address']['street'] = (!empty($order['Address']['street'])?$order['Address']['street']:$order['Order']['ship_to_street']);
                            $data['Address']['city'] = (!empty($order['Address']['city'])?$order['Address']['city']:$order['Order']['ship_to_city']);
                            $data['Address']['zip'] = (!empty($order['Address']['zip'])?$order['Address']['zip']:$order['Order']['ship_to_zip']);
                            $data['Address']['state_id'] = (!empty($order['Address']['state_id'])?$order['Address']['state_id']:$order['Order']['state_id']);
                            $data['Address']['country_id'] = (!empty($order['Address']['country_id'])?$order['Address']['country_id']:$order['Order']['country_id']);
                            $data['Address']['stateprovince'] = (!empty($order['Address']['stateprovince'])?$order['Address']['stateprovince']:$order['Order']['ship_to_stateprovince']);
                            $data['Address']['phone'] = (!empty($order['Address']['phone'])?$order['Address']['phone']:$order['Order']['ship_to_phone']);

                            $this->Order->create();
                            unset($this->Order->validate['external_orderid']);
                            if($this->Order->saveAll($data)) {
                                foreach ($listsuppl as  $key => $orderline) {
                                    $orderlines = $this->OrdersLine->find('first', array('conditions' => array('order_id' => $id, 'product_id' => $orderline['product_id']), 'recursive' => -1));
                                    $orderline = array(
                                        'order_id' => $this->Order->id,
                                        'line_number' => $orderlines['OrdersLine']['line_number'],
                                        'type' => 2, //$orderlines['OrdersLine']['type'],
                                        'product_id' => $orderline['product_id'],
                                        'warehouse_id' => $orderlines['OrdersLine']['warehouse_id'],
                                        'quantity' => $orderlines['OrdersLine']['quantity'],
                                        'receivedqty' => 0,
                                        'damagedqty' => 0,
                                        'sentqty' => 0,
                                        'unit_price' => $orderlines['OrdersLine']['unit_price'],
                                        'total_line' => $orderlines['OrdersLine']['total_line'],
                                        'sku' => $orderlines['OrdersLine']['sku'],
                                        'user_id' => $order['User']['id'],
                                        'status_id' => $orderlines['OrdersLine']['status_id'],
                                        'comments' => $orderlines['OrdersLine']['comments'],
                                    );
                                    $this->OrdersLine->create();
                                    $this->OrdersLine->save($orderline);
                                }
                                
                            } else{
                                $this->Session->setFlash(__('The replenishment order could not be create related to this order # %s', $id), 'admin/danger', array());
                                return $this->redirect(array('action' => 'index'));
                            }  
                        }
                        if(!empty($products)) {
                            $prefix = $productName = '';
                            foreach($products as $prdt) {
                                $productName .= $prefix . $prdt;
                                $prefix = ', ';
                            }
                            $message = 'P.O. line was not created, because there is no active product-supplier assignment for product ' . $productName;
                            $this->Session->setFlash(__($message), 'admin/warning', array());
                        } else {
                            $this->Session->setFlash(__('Create replenishment order related to this order #%s', $id), 'admin/success', array());  
                        }
                        return $this->redirect(array('action' => 'index'));
                    } else { 
                        $this->Session->setFlash(__("You did not assign any product to any supplier"), 'admin/warning', array());
                        return $this->redirect(array('action' => 'index'));  
                    }
                } else { 
                    $this->Session->setFlash(__('This Salesorder #%s has no order lines', $id), 'admin/danger', array());
                    return $this->redirect(array('action' => 'index'));
                }

            } else {
                $this->Session->setFlash(__('This Salesorder #%s has no order lines', $id), 'admin/danger', array());
                return $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__('Already exist replenishment order related to this sales order # %s', $id), 'admin/danger');
            return $this->redirect(array('action' => 'index'));  
        }
    }

    /**
     * Get order lines
     *
     */
    public function lines($id) {
        $order = $this->Order->find('first', array(
            'contain' => array('User'),
            'conditions' => array('Order.id' => $id)
        ));

        if(!$order) {
            throw new NotFoundException(__('Order not found'));
        }

        $is_own = ($order['Order']['user_id'] == $this->Auth->user('id'));
        $is_write = 0;
        $is_shipment = 0;
        if($order['Order']['user_id'] != $this->Auth->user('id')) {
            $products = $this->Access->getProducts('S.O.', 'w', $order['Order']['user_id']);
            $warehouses = $this->Access->locationList('S.O.', false, false, $order['Order']['user_id']);
            
            foreach ($warehouses as $w) {
                if(strpos($w, 'w') !== 0) {
                    $is_write = 1;
                }
            }

            $orders_lines = $this->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'contain' => ('Warehouse'),
                //'callbacks' => false
            ));

            $is_shipment = $this->Access->hasOrderShipmentAccess($id);
        } else {
            $is_write = 1;
            //$this->OrdersLine->recursive = -1;
            $orders_lines = $this->OrdersLine->find('all', array(
                'conditions' => array('OrdersLine.order_id' => $id,'OrdersLine.user_id' => $this->Auth->user('id')),
                'contain' => ['Warehouse' => ['id', 'name'], 'Product' => ['id', 'name']],
                'fields' => [
                    'OrdersLine.*',
                    '(SELECT sum(quantity) FROM inventories WHERE deleted = 0 AND product_id = OrdersLine.product_id) as totals',
                    '(SELECT sum(quantity) FROM inventories WHERE deleted = 0 AND warehouse_id = OrdersLine.warehouse_id AND product_id = OrdersLine.product_id) as inv_totals',
                ]
                //'callbacks' => false
            ));
            $warehouses = $this->Warehouse->find('list', ['fields'=>['Warehouse.id', 'Warehouse.name'], 'conditions' => array('Warehouse.user_id' => $this->Auth->user('id'), 'Warehouse.status' => 'active')] );
            foreach ($warehouses as $key => $value) {
                $warehouses[$key] = 'rw';
            }
            $is_shipment = 1;
        }
        #pr($orders_lines);
        $response['OrderLines'] = $orders_lines;
        $response['action'] = 'success';

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /**
     * Sales Order Details
     *
     */
    public function details($id) {
        $order = $this->Order->find('first', array(
            'contain' => array('User', 'Schannel', 'Country', 'State', 'Address', 'Address.Country', 'Address.State', 'Shipment', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
            'conditions' => array('Order.id' => $id)
        ));

        if(!$order) {
            throw new NotFoundException(__('Order not found'));
        }

        $order_costs = $this->OrdersCosts->find('all',[ 'conditions' => array('OrdersCosts.order_id' => $id), 'contain' => false ]);

        $is_own = ($order['Order']['user_id'] == $this->Auth->user('id'));
        $is_write = 0;
        $is_shipment = 0;
        if($order['Order']['user_id'] != $this->Auth->user('id')) {
            $products = $this->Access->getProducts('S.O.', 'w', $order['Order']['user_id']);
            $warehouses = $this->Access->locationList('S.O.', false, false, $order['Order']['user_id']);

            if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
                $this->Session->setFlash(__('You do not have access to purchase orders. Please contact admin.'), 'admin/success');
                return $this->redirect(array('action' => 'index'));
            }
            if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
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

        $ordertotals = $this->getordertotals($this->Order->read(null, $id), $orders_lines, $order_costs);

        $currency = $this->Currency->find('first', array('conditions' => array('id' => $order['User']['currency_id'])));
        $linecount = count($order['OrdersLine']);

        $objectevents = $this->EventRegister->getObjectEvent(2, $id, $this->Auth->user('id'));

        $costs_types = $this->OrdersCosts->getTypes();

        $addr = $order['Order']['ship_to_street']." ".$order['Order']['ship_to_city']." ".$order['Order']['ship_to_zip']." ".$order['Country']['name'];
        $this->set(compact('ordertotals', 'order_costs', 'costs_types', 'status_text', 'addr', 'orders_lines', 'order', 'currency', 'linecount', 'is_write', 'is_shipment', 'warehouses', 'objectevents'));
    }

    public function edit($id = null) {
        $this->layout = 'mtrd';
        $this->loadModel('Address');
        if ($this->request->is(array('post', 'put'))) {

            if(!empty($this->request->data['Address']['state_id']) && $this->request->data['Address']['country_id'] == 1) {
                $statedate = $this->Address->State->findById($this->request->data['Address']['state_id']);      
                $this->request->data('Address.stateprovince', $statedate['State']['name']);
            } else {
               $this->request->data('Address.state_id', ''); 
            }
            unset($this->Address->validate['street']);
            unset($this->Address->validate['city']);
            unset($this->Address->validate['country_id']);
            if ($this->Order->saveAll($this->request->data)) {
                $this->Session->setFlash(__('Order number %s has been updated successfully.',$this->Order->id), 'admin/success', array());;
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Order could not be saved. Please, try again.'), 'admin/danger', array());
            }
        } else {
            $options = array('Order.id' => $id);
            $order = $this->Order->find('first', array('conditions' => $options, 'contain' => array('Ordertype','Address','Country','State','Schannel')));

            if(empty($order['Address']['street'])) {
                $order['Address']['street'] = $order['Order']['ship_to_street'];
            }
            if(empty($order['Address']['city'])) {
                $order['Address']['city'] = $order['Order']['ship_to_city'];
            }
            if(empty($order['Address']['zip'])) {
                $order['Address']['zip'] = $order['Order']['ship_to_zip'];
            }
            if(empty($order['Address']['country_id'])) {
                $order['Address']['country_id'] = $order['Order']['country_id'];
            }
            if(empty($order['Address']['state_id'])) {
                $order['Address']['state_id'] = $order['Order']['state_id'];
            }
            if(empty($order['Address']['stateprovince'])) {
                $order['Address']['stateprovince'] = $order['Order']['ship_to_stateprovince'];
            }
            if(empty($order['Address']['phone'])) {
                $order['Address']['phone'] = $order['Order']['ship_to_phone'];
            }
            
            $this->request->data = $order; //$this->Order->find('first', array('conditions' => $options, 'contain' => array('Ordertype','Address','Country','State','Schannel','OrdersLine.Product'=>array('fields' => array('id','name','sku')))));
        }
        
        $states = $this->Order->State->find('list');
        $schannels = $this->Order->Schannel->find('list',array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));
        $countries = $this->Order->Country->find('list');

        $this->set(compact('schannels', 'countries', 'states', 'order'));
    }

    public function edit_shipping($id) {
        $this->layout = false;
        $order = $this->Order->find('first', array(
            'fields' => array('Order.*', 'User.currency_id'),
            'contain' => array('User', 'Address', 'Address.Country', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
            'conditions' => array('Order.id' => $id)
        ));
        if(empty($order['Address'])) {
            $order['Address']['street'] = $order['Order']['ship_to_street'];
            $order['Address']['city'] = $order['Order']['ship_to_city'];
            $order['Address']['zip'] = $order['Order']['ship_to_zip'];
            $order['Address']['country_id'] = $order['Order']['country_id'];
            $order['Address']['state_id'] = $order['Order']['state_id'];
            $order['Address']['stateprovince'] = $order['Order']['ship_to_stateprovince'];
            $order['Address']['phone'] = $order['Order']['ship_to_phone'];
        }

        if(!$order) {
            throw new NotFoundException(__('Order not found'));
        }

        $is_own = ($order['Order']['user_id'] == $this->Auth->user('id'));
        if(!$is_own) { // Check access, get products and allowed warehouse
            $products = $this->Access->getProducts('S.O.', 'w', $order['Order']['user_id']);
            $warehouses = $this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);
            if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
            if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
        } else {
            $products = $this->OrdersLine->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.consumption' => true, 'Product.status_id' => 1)));
            $warehouses = $this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->loadModel('Address');
            if(!empty($this->request->data['Address']['state_id']) && $this->request->data['Address']['country_id'] == 1) {
                $statedate = $this->Address->State->findById($this->request->data['Address']['state_id']);      
                $this->request->data('Address.stateprovince', $statedate['State']['name']);
            } else {
                $this->request->data('Address.state_id', ''); 
            }

            unset($this->Address->validate['street']);
            unset($this->Address->validate['city']);
            unset($this->Address->validate['country_id']);
            
            if ($this->Order->saveAll($this->request->data)) {
                $order = $this->Order->find('first', array(
                    'contain' => array('Schannel', 'State', 'Country', 'Address', 'Address.Country', 'Address.State'),
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
            $products = $this->Access->getProducts('S.O.', 'w', $order['Order']['user_id']);
            $warehouses = $this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);
            if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
            if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
        } else {
            $products = $this->OrdersLine->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.consumption' => true, 'Product.status_id' => 1)));
            $warehouses = $this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);
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
        $schannels = $this->Access->schannelList('S.O.', 'w', $order['User']['id']);

        $this->set(compact('order', 'schannels', 'currency'));
    }

    public function add_line($id, $lineid = null) {
        $this->layout = false;
        $order = $this->Order->find('first', array(
            'fields' => array('Order.id','Order.ordertype_id', 'Order.user_id', 'Order.schannel_id', 'Order.dcop_user_id', 'Order.external_orderid', 'User.currency_id'),
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

        

        #pr($products_list);
        #exit;

        if($this->request->is('post')) {
            if($this->request->data['OrdersLine']['quantity'] > 0 && isset($this->request->data['OrdersLine']['product_id'])) {
                $this->OrdersLine->create();

                $product = $this->Product->find('first', array('conditions' => array('Product.id' => $this->data['OrdersLine']['product_id']), 'callbacks' => false));

                $this->loadModel('Inventory');
                $inventoryRecord = $this->InventoryManager->getInventory($this->request->data['OrdersLine']['product_id'], $this->request->data['OrdersLine']['warehouse_id']);
                if(!$inventoryRecord) {
                    $response['action'] = 'error';
                    $response['message'] = 'We can\'t get invntory record for this location.';
                    echo json_encode($inv_response);
                }

                if($lineid < 999999) {
                    ($this->data['OrdersLine']['unit_price']) ? $this->request->data('OrdersLine.unit_price', $this->data['OrdersLine']['unit_price']) : $this->request->data('OrdersLine.unit_price', 0);
                    $this->request->data('OrdersLine.status_id',1);
                    $this->request->data('OrdersLine.order_id', $id);
                    $this->request->data('OrdersLine.line_number', $line_number);
                    $this->request->data('OrdersLine.type', $order['Order']['ordertype_id']);
                    $this->request->data('OrdersLine.sku', $product['Product']['sku']);
                    $this->request->data('OrdersLine.user_id', $order['Order']['user_id']);
                    $this->request->data('OrdersLine.sentqty',0);
                    $this->request->data('OrdersLine.receivedqty', 0);
                    $this->request->data('OrdersLine.damagedqty', 0);
                    $this->request->data('OrdersLine.serial_id', 0);
                    $this->request->data('OrdersLine.warehouse_id', $this->request->data['OrdersLine']['warehouse_id']);
                    $line_total = $this->data['OrdersLine']['quantity'] * $this->data['OrdersLine']['unit_price'];
                    $this->request->data('OrdersLine.total_line',$line_total);

                    if ($this->OrdersLine->save($this->request->data)) {
                        $response['row'] = $this->OrdersLine->find('first', ['conditions' => ['OrdersLine.id' => $this->OrdersLine->id], 'callbacks' => false]);
                        $response['action'] = 'success';
                        $response['message'] = 'The orders line has been added';
                        echo json_encode($response, JSON_NUMERIC_CHECK);
                        exit;
                    }

                } else {
                    $this->Session->setFlash(__('The orders line could not be added. Please, try again.'),'default',array('class'=>'alert alert-danger'));
                    $response['status'] = false;
                    $response['message'] = 'The orders line could not be added. Please, try again.';
                    echo json_encode($response);
                    exit;
                }
            } else {
                $this->Session->setFlash(__('The orders line could not be added. Either the product does not exist or quantity is negative or 0.'),'default',array('class'=>'alert alert-danger'));
            }
        } else {
            $is_own = ($order['Order']['user_id'] == $this->Auth->user('id'));
            if(!$is_own) { // Check access, get products and allowed warehouse
                if($order['Order']['ordertype_id'] == 1) {
                    $products = $this->Access->getProducts('S.O.', 'w', $order['Order']['user_id'], ['Product.status_id' => 1]);
                } else {
                    $allowedstatusesrepl = [1,12];
                    $products = $this->Access->getProducts('S.O.', 'w', $order['Order']['user_id'], ['Product.status_id' => $allowedstatusesrepl]);
                }
                $warehouses = $this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);
                if((empty($warehouses)) && $this->Auth->user('is_limited')) {
                    throw new MethodNotAllowedException(__('Have no access'));
                }
                if((empty($warehouses)) && !$this->Auth->user('paid')) {
                    throw new MethodNotAllowedException(__('Have no access'));
                }

                $products = $this->Access->getProducts($this->types[1], 'w', $order['Order']['user_id']); //, $conditions

                $products_list = [];
                if($this->Auth->user('zeroquantity')) {
                    foreach ($products as $key => $value) {
                        $result['id'] = $key;
                        $result['text'] = utf8_encode($value);
                        $products_list[] = $result;
                    }
                } else {
                    foreach ($products as $key => $value) {
                        $result['id'] = $key;
                        $result['quantity'] = $this->OrdersLine->Product->getInvQuantity($key);
                        $result['disabled'] = $result['quantity']['disabled'];
                        $result['text'] = utf8_encode($value);
                        $products_list[] = $result;
                    }
                }
            } else {
                $locations = $this->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'), 'Warehouse.status' => 'active')));
                $warehouses['My Locations'] = $locations;//$this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);


                $products = $this->OrdersLine->Product->find('all', array(
                    'conditions' => array(
                        'Product.user_id' => $this->Auth->user('id'),
                        'Product.status_id' => 1
                    ),
                    'contain' => [
                        'Inventory.Warehouse' => [
                            'conditions' => array(
                                'Warehouse.status' => 'active'
                            ),
                            'fields' => array(
                                'id',
                                'name'
                            ),
                        ],
                        'Inventory' => [
                            'conditions' => array(
                                'Inventory.deleted' => 0
                            ),
                            'fields' => array(
                                'quantity'
                            )
                        ]
                    ],
                    'fields' => array(
                        'Product.id',
                        'Product.name',
                    ),
                ));

                $products_list = [];
                if($this->Auth->user('zeroquantity')) {
                    foreach ($products as $key => $value) {
                        $result['id'] = $value['Product']['id'];
                        $result['text'] = utf8_encode($value['Product']['name']);
                        $products_list[] = $result;
                    }
                } else {
                    foreach ($products as $key => $value) {
                        $result = [];
                        $result['id'] = $value['Product']['id'];
                        $is_active = false;
                        foreach ($value['Inventory'] as $quant) {
                            if(!empty($quant['Warehouse']['id'])) {
                                $res = [
                                    'Warehouse' => [
                                        'id' => $quant['Warehouse']['id'],
                                        'name' => $quant['Warehouse']['name']
                                    ],
                                    'Inventory' => [
                                        'quantity' => $quant['quantity']
                                    ]
                                ];
                                $result['quantity'][] = $res;
                                if($quant['quantity'] > 0) {
                                    $is_active = true;
                                }
                            }
                        }
                        
                        $result['disabled'] = !$is_active;
                        $result['text'] = utf8_encode($value['Product']['name']);
                        $products_list[] = $result;
                    }
                }
                //$products = $this->OrdersLine->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.status_id' => 1)));
            }
            $warehouses = array_values($warehouses);
            $warehouses = $warehouses[0];
        }

        $linecount = count($order['OrdersLine']);
        $currency = $this->Currency->find('first', array('conditions' => array('id' => $order['User']['currency_id'])));

        $this->set(compact('order', 'warehouses', 'products', 'products_list', 'currency', 'addpack'));
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
        $options = array('conditions' => array('OrdersLine.id' => $id), 'contain' => array('User', 'Order'), 'callbacks'=>false);
        $order_line = $this->OrdersLine->find('first', $options);
        //pr($order_line);
        $user = $this->User->find('first', ['conditions' => ['User.id' => $order_line['OrdersLine']['user_id']], 'fields' => array('User.*'), 'contain' => false]);
        if($this->Auth->user('id') == $order_line['OrdersLine']['user_id']) {
            $products = $this->OrdersLine->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.status_id' => 1)));
        } else {
            $products = $this->Access->getProducts('S.O.', 'w', $order_line['OrdersLine']['user_id']);
        }
        $warehouses = $this->Access->getLocations('S.O.', false, 'w', $order_line['OrdersLine']['user_id']);
        $warehouses = array_values($warehouses);
        $warehouses = $warehouses[0];

        if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
            throw new MethodNotAllowedException(__('Have no access'));
        }
        if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
            throw new MethodNotAllowedException(__('Have no access'));
        }

        if (!$this->OrdersLine->exists($id)) {
            throw new NotFoundException(__('Invalid orders line'));
        }

        $products_list = [];
        if($this->Auth->user('zeroquantity')) {
            foreach ($products as $key => $value) {
                $result['id'] = $key;
                $result['text'] = utf8_encode($value);
                $products_list[] = $result;
            }
        } else {
            foreach ($products as $key => $value) {
                $result['id'] = $key;
                #$result['quantity'] = $this->OrdersLine->Product->getInvQuantity($key);
                #$result['disabled'] = $result['quantity']['disabled'];
                $result['text'] = utf8_encode($value);
                $products_list[] = $result;
            }
        }

        if ($this->request->is(array('post', 'put'))) {
            if ($this->data['OrdersLine']['product_id'] != $order_line['OrdersLine']['product_id'] || $this->data['OrdersLine']['warehouse_id'] != $order_line['OrdersLine']['warehouse_id'] ) {
                $product = $this->Product->find('first', array('conditions' => array('Product.id' => $this->data['OrdersLine']['product_id']), 'callbacks' => false));

                $this->loadModel('Inventory');
                $inventoryRecord = $this->InventoryManager->getInventory($this->request->data['OrdersLine']['product_id'], $this->request->data['OrdersLine']['warehouse_id']);
                if(!$inventoryRecord) {
                    $response['status'] = false;
                    $response['message'] = 'We can\'t get invntory record for this location.';
                    echo json_encode($inv_response);
                }
            }
            // recalculate total after update of price or qty
            $line_total = $this->data['OrdersLine']['quantity'] * $this->data['OrdersLine']['unit_price'];
            $this->request->data('OrdersLine.total_line',$line_total);
            $this->request->data['OrdersLine']['sentqty'] = 0;

            if ($this->OrdersLine->save($this->request->data)) {
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

        $currency = $this->Currency->find('first', array('conditions' => array('id' => $user['User']['currency_id'])));

        $this->set(compact('products', 'products_list', 'warehouses', 'currency', 'order_line'));
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
            $products = $this->Access->getProducts('S.O.', 'w', $currentOrderLine['OrdersLine']['user_id']);
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
                $products = $this->Access->getProducts('S.O.', 'w', $order['Order']['user_id']);
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
     * printslip method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function printslip($id) {
        if(!$this->Order->exists($id)) {
            throw new NotFoundException(__('Order not found'));
        }
        $this->layout = 'mtrds';
        $this->Order->recursive = 1;

        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id), 'contain' => array(
            'User' => array('fields' => array('id','logo','logo_url','company')),
            'Address',
            'Address.State',
            'Address.Country',
            'Supplier' => array('fields' => array('name','email')),
            'OrdersLine',
            'Schannel'=> array('fields' => array('name')),
            'OrdersLine.Product' => array('fields' => array('name','sku','description', 'bin')))
        ));

        $warehouses = $this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);
        $warehouses = array_values($warehouses);
        $warehouses = $warehouses[0];

        $order_costs = $this->OrdersCosts->find('all',[ 'conditions' => array('OrdersCosts.order_id' => $id), 'contain' => false ]);
        $costs_types = $this->OrdersCosts->getTypes();
        
        $ordertotals = $this->newordertotals($order, $order['OrdersLine']);
        $this->set(compact('ordertotals','order', 'order_costs', 'costs_types', 'warehouses'));
    }

    /**
     * print invoice method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function invoice($id) {
        if(!$this->Order->exists($id)) {
            throw new NotFoundException(__('Order not found'));
        }
        $this->layout = 'mtrds';
        $this->Order->recursive = 1;

        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id), 'contain' => array(
            'User' => array('fields' => array('id','logo','logo_url','company')),
            'Address',
            'Address.State',
            'Address.Country',
            'Supplier' => array('fields' => array('name','email')),
            'OrdersLine',
            'Schannel'=> array('fields' => array('name')),
            'OrdersLine.Product' => array('fields' => array('name','sku','description', 'bin')))
        ));

        $warehouses = $this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);
        $warehouses = array_values($warehouses);
        $warehouses = $warehouses[0];

        $order_costs = $this->OrdersCosts->find('all',[ 'conditions' => array('OrdersCosts.order_id' => $id), 'contain' => false ]);
        $costs_types = $this->OrdersCosts->getTypes();
        
        $ordertotals = $this->newordertotals($order, $order['OrdersLine']);
        $this->set(compact('ordertotals','order', 'order_costs', 'costs_types', 'warehouses'));
    }

    function price_compare() {
        $limit = 100;
        $schannels = $this->Access->schannelList();
        $this->set(compact('limit', 'schannels'));
    }

    function price_compare_js() {
        
        $limit = 100;
        if($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }
        
        $conditions = ['OrdersLine.user_id' => $this->Auth->user('id')];
        if($this->request->query('status_id')) {
            $conditions['Order.status_id'] = $this->request->query('status_id');
        }
        if($this->request->query('schannel_id')) {
            $conditions['Order.schannel_id'] = $this->request->query('schannel_id');
        }
        if($this->request->query('product_id')) {
            $conditions['OrdersLine.product_id'] = $this->request->query('product_id');
        }
        if($this->request->query('difference')) {
            if($this->request->query('difference') == 1) {
                $conditions = $conditions + array('OrdersLine.unit_price != Product.value');
            } else {
                $conditions = $conditions + array('OrdersLine.unit_price != ProductsPrices.value');
                $conditions['ProductsPrices.value !='] = '';
            }
        }


        $orderBy = 'OrdersLine.modified';
        if($this->request->query('sortby')) {
            $orderBy = $this->request->query('sortby');
        }
        $orderDir = 'DESC';
        if($this->request->query('sortdir')) {
            $orderDir = $this->request->query('sortdir');
        }
        $page = 1;
        if(isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }

        $this->OrdersLine->recursive = -1;
        $options = array(
            'conditions' => $conditions,
            'fields' => array(
                'OrdersLine.order_id',
                'OrdersLine.unit_price',
                'OrdersLine.quantity',
                'OrdersLine.modified',
                'Schannel.name',
                'Product.name',
                'Product.sku',
                'Product.value',
                'ProductsPrices.value',
                'DcopUser.email',
                'DcopUser.firstname',
                'DcopUser.lastname'
            ),
            'joins' => array(
                array('table' => 'users',
                    'alias' => 'DcopUser',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'OrdersLine.dcop_user_id = DcopUser.id'
                    )
                ),
                array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id'
                    )
                ),
                array('table' => 'schannels',
                    'alias' => 'Schannel',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Order.schannel_id = Schannel.id'
                    )
                ),
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                ),
                array('table' => 'products_prices',
                    'alias' => 'ProductsPrices',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'ProductsPrices.schannel_id = Order.schannel_id',
                        'ProductsPrices.product_id = OrdersLine.product_id',
                    )
                )
            ),
            'limit' => $limit,
            'page' => $page,
            'order' => array($orderBy => $orderDir)
        );
        $orderlines = $this->OrdersLine->find('all', $options);
        #pr($orderlines);
        #exit;
        
        $recordsCount = $this->OrdersLine->find('count', [
            'conditions' => $conditions,
            'joins' => array(
                array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.order_id = Order.id'
                    )
                ),
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                ),
                array('table' => 'products_prices',
                    'alias' => 'ProductsPrices',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'ProductsPrices.schannel_id = Order.schannel_id',
                        'ProductsPrices.product_id = OrdersLine.product_id',
                    )
                )
            )
        ]);

        $response['recordsTotal'] = $recordsCount;
        $response['rows_count'] = count($orderlines);
        $response['rows'] = $orderlines;

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /*public function issue($id) {
        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id ),
            'contain' => array('Ordertype','State','Country','Supplysource','Supplier','Schannel','Status','Address','Shipment','OrdersLine'),
            'callbacks' => false
        ));
        if (!$order) {
            throw new NotFoundException(__('Invalid order'));
        }
        if ($order['Order']['ordertype_id'] == 2) {
            // If purchase order reidrect on receive
        }

        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to delete not own order
            $products = $this->Access->getProducts('S.O.', 'w', $order['Order']['user_id']);
            $conditions['OrdersLine.product_id'] = array_keys($products);
            $this->Paginator->settings['callbacks'] = false;

            $is_order = $this->Access->hasOrderAccess($id);
            $is_shipment = $this->Access->hasOrderShipmentAccess($id);
            $is_write = ($is_order && $is_shipment);

            $net_warehouses = $this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);
            $network = array_keys($net_warehouses);
            $network = array_shift($network);
            $warehouses = array_shift($net_warehouses);
        } else {
            $network = false;
            $conditions['OrdersLine.user_id'] = $this->Auth->user('id');
            $is_write = 1;
            $my_warehouse = $this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);
            $warehouses = array_shift($my_warehouse);
        }

        $conditions['OrdersLine.order_id'] = $id;

        if (isset($this->request->params['named']['searchby'])) {
            $conditions[] = array('OR' => array('Product.name' => $this->request->params['named']['searchby'], 'Product.sku' => $this->request->params['named']['searchby']));
        }

        $this->Prg->commonProcess();
        $this->Paginator->settings = array(
            //'order' => array('OrdersLine.created' => 'DESC'),
            'order' => ['OrdersLine.line_number ASC'],
            'contain' => array(
                'Order',
                'Shipment',
                'Warehouse',
                'Product.Issue' => array('fields' => array('id', 'name')),
                'Product.Receive' => array('fields' => array('id', 'name')),
                'Product.Inventory',
                'Product.Inventory.Warehouse' => array('fields' => array('id', 'name')),
                'Serial'
            ),
            'conditions' => $conditions, //$this->OrdersLine->parseCriteria($this->Prg->parsedParams())
        );
        $ordersLines = $this->Paginator->paginate('OrdersLine');

        $order_list = $this->OrdersLine->find('list', array('conditions'=>$conditions, 'contain' => ['Product'], 'fields'=>['Product.sku', 'OrdersLine.id']));
        
        $this->set(compact('ordersLines', 'order', 'order_list', 'is_write', 'warehouses', 'network'));
    }

    public function issue_ajax($id) {
        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id ),
            'contain' => array('Ordertype','State','Country','Supplysource','Supplier','Schannel','Status','Address','Shipment','OrdersLine'),
            'callbacks' => false
        ));

        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to delete not own order
            $products = $this->Access->getProducts('S.O.', 'w', $order['Order']['user_id']);
            $conditions['OrdersLine.product_id'] = array_keys($products);
            $this->Paginator->settings['callbacks'] = false;

            $is_order = $this->Access->hasOrderAccess($id);
            $is_shipment = $this->Access->hasOrderShipmentAccess($id);
            $is_write = ($is_order && $is_shipment);

            $net_warehouses = $this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);
            $network = array_keys($net_warehouses);
            $network = array_shift($network);
            $warehouses = array_shift($net_warehouses);
        } else {
            $network = false;
            $conditions['OrdersLine.user_id'] = $this->Auth->user('id');
            $is_write = 1;
            $my_warehouse = $this->Access->getLocations('S.O.', false, 'w', $order['Order']['user_id']);
            $warehouses = array_shift($my_warehouse);
        }

        $conditions['OrdersLine.order_id'] = $id;

        if (isset($this->request->params['named']['searchby'])) {
            $conditions[] = array('OR' => array('Product.name' => $this->request->params['named']['searchby'], 'Product.sku' => $this->request->params['named']['searchby']));
        }

        $this->Prg->commonProcess();
        $this->Paginator->settings = array(
            //'order' => array('OrdersLine.created' => 'DESC'),
            'order' => ['OrdersLine.line_number ASC'],
            'contain' => array(
                'Order',
                'Shipment',
                'Warehouse',
                'Product.Issue' => array('fields' => array('id', 'name')),
                'Product.Receive' => array('fields' => array('id', 'name')),
                'Product.Inventory',
                'Product.Inventory.Warehouse' => array('fields' => array('id', 'name')),
                'Serial'
            ),
            'conditions' => $conditions, //$this->OrdersLine->parseCriteria($this->Prg->parsedParams())
        );
        $ordersLines = $this->Paginator->paginate('OrdersLine');

        $count_total = $this->OrdersLine->find('count', array('conditions' => $conditions));

        $response['draw'] = 1;
        $response['recordsTotal'] = $count_total;
        $response['rows'] = $ordersLines;
        $response['rows_count'] = count($ordersLines);

        echo json_encode($response);
        exit;
    }*/

    /**
     * issue order product method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function send_line($id = null, $shipment_id = null) {
        $this->layout = false;

        $orderline = $this->OrdersLine->find('first', array(
            'conditions' => array('OrdersLine.id' => $id),
            'contain' => array(
                'Order' => array('fields' => 'id', 'user_id', 'status_id', 'external_orderid'),
                'Product' => array('id', 'name', 'sku', 'packaging_material_id', 'description', 'uom', 'imageurl', 'receive_location', 'status_id', 'deleted'),
                'Warehouse' => array('id', 'name')
            )
        ));

        if (!$orderline) {
            throw new NotFoundException(__('Invalid orders line'));
        }
        $product = $orderline['Product'];

        $product_parts = [];
        $_is_kit = ($product['uom'] == 'Kit' && $this->_authUser['User']['kit_component_issue'] == 'issued');
        if($_is_kit) { // Get components
            $this->loadModel('Kit');
            //$this->Kit->recursive = -1;
            $product_parts = $this->Kit->find('all',array(
                'conditions' => array('Kit.product_id' => $product['id']),
                'contain' => array('ProductPart')
            ));
        }


        if($orderline['OrdersLine']['user_id'] != $this->Auth->user('id')) {
            $products = $this->Access->getProducts('S.O.', 'w', $orderline['OrdersLine']['user_id']);
            $warehouses = $this->Access->getLocations('S.O.', false, 'w', $orderline['OrdersLine']['user_id']);
            if(!array_key_exists($orderline['OrdersLine']['product_id'], $products)) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
        } else {
            $warehouses = $this->Access->getLocations('S.O.', false, 'w', $orderline['OrdersLine']['user_id']);
        }

        if($this->request->is(array('post', 'put'))) {
            if($product['status_id'] == 13 || $product['deleted'] == 1) {
                $response['action'] = 'error';
                $response['message'] = __('The orders line could not be issue, product blocked or deleted.');
                echo json_encode($response);
                exit;
            }
            // Get invenotry record
            $inventoryRecord = $this->InventoryManager->getInventory($orderline['OrdersLine']['product_id'], $this->request->data['OrdersLine']['warehouse_id']);
            if(!$inventoryRecord) {
                $response['action'] = 'error';
                $response['message'] = 'We can\'t get invntory record for this location.';
                echo json_encode($response);
                exit;
            }

            if($_is_kit) {
                $partInventoryRecord = [];
                foreach ($product_parts as $prdt) {
                    $partInventoryRecord[$prdt['ProductPart']['id']] = $this->InventoryManager->getInventory($prdt['ProductPart']['id'], $this->request->data['OrdersLine']['warehouse_id']);
                    if(!$partInventoryRecord[$prdt['ProductPart']['id']]) {
                        $response['action'] = 'error';
                        $response['message'] = 'We can\'t get invntory record for this location for one from components.';
                        echo json_encode($response);
                        exit;
                    }
                }
            }


            $offset = $this->request->data['OrdersLine']['sentqty'] - $orderline['OrdersLine']['sentqty'];
            if(!$_is_kit && $inventoryRecord['Inventory']['quantity'] < $this->request->data['OrdersLine']['sentqty'] && $inventoryRecord['Inventory']['quantity'] >=0 && !$this->Session->read('allow_negative') && !$this->request->data['OrdersLine']['confirm']) {
                $response['action'] = 'confirm';
                $response['message'] = 'You are trying to issue a quantity greater than the quantity you have in inventory.';
                echo json_encode($response);
                exit;
            }

            if($_is_kit) {
                foreach ($product_parts as $prdt) {
                    $offset = ($this->request->data['OrdersLine']['sentqty'] - $orderline['OrdersLine']['sentqty']) * $prdt['Kit']['quantity'];
                    if($partInventoryRecord[$prdt['ProductPart']['id']]['Inventory']['quantity'] < $this->request->data['OrdersLine']['sentqty'] * $prdt['Kit']['quantity'] && $partInventoryRecord[$prdt['ProductPart']['id']]['Inventory']['quantity'] >=0 && !$this->Session->read('allow_negative') && !$this->request->data['OrdersLine']['confirm']) {
                        $response['action'] = 'confirm';
                        $response['message'] = 'You are trying to issue a quantity greater than the quantity you have in inventory for one from component.';
                        echo json_encode($response);
                        exit;
                    }
                }
            }


            $this->request->data['OrdersLine']['shipment_id'] = $shipment_id;
            $response = $this->InventoryManager->issueLine($orderline, $inventoryRecord, $this->request->data);
            if($_is_kit) {
                foreach ($product_parts as $prdt) {
                    $data = $this->request->data;
                    $data['OrdersLine']['sentqty'] = $this->request->data['OrdersLine']['sentqty'] * $prdt['Kit']['quantity'];

                    $orderline1 = $orderline;
                    $orderline1['OrdersLine']['sentqty'] = $orderline['OrdersLine']['sentqty'] * $prdt['Kit']['quantity'];
                    $this->InventoryManager->issueKitProduct($orderline1, $partInventoryRecord[$prdt['ProductPart']['id']], $data);
                }
            }

            

            if($response['status']) {
                if(isset($orderline['Order']) && $orderline['Order']['status_id'] != 3) {
                    $this->Order->id = $orderline['OrdersLine']['order_id'];
                    $this->Order->saveField('status_id', 3);
                    $this->EventRegister->addEvent(2, 3, $this->Auth->user('id'),$this->Order->id);
                }
                $this->updateshipmentprocess($orderline['OrdersLine']['order_id']);

                //$response['orderline'] = $orderline;
                $response['action'] = 'success';
                $response['message'] = __('Product SKU %s, quantity %s, were issued.', $orderline['Product']['sku'],$this->request->data['OrdersLine']['sentqty']);
            } else {
                $response['action'] = 'error';
                //$response['message'] = __('The orders line could not be saved. Please, try again.');
            }

            echo json_encode($response);
            exit;
        } else {
            $this->request->data = $orderline;
        }

        $this->set(compact('product','orderline','warehouses','product_parts'));
    }

    public function confirmNegative() {
        if($this->request->is(array('post', 'put'))) {
            if($this->request->data['negative_alowed'] == 1) {
                $this->Session->write('allow_negative', 1);
            } else {
                $this->Session->write('allow_negative', 0);
            }
        }
        exit;
    }

    public function uploadcsv($index=1) {
        set_time_limit(0);
        $this->layout = 'mtrd';
        $schannels = $this->Order->Schannel->find('list',array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));

        if(sizeof($schannels) == 0) {
            $this->Session->setFlash(__('No sales channel exist, you cannot create a cutsomer order. Under "Partners" menu, click "Sales Channel" and cretae one.'), 'admin/warning', array());
            return $this->redirect(array('action' => 'index',1));
        } else {
            $this->set(compact('schannels'));
        }
        if ($this->request->is('post')) {
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
            } else{
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

    public function importcsv($filename = null, $uname = null) { //, $ordersdata = null
        set_time_limit(0);
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
            if($numcols != 17) {
                $is_error = true;
                $msg = "Error: The CSV file should have 17 columns, but line 1 has ". $numcols ." columns.";
            }
            break;
        }
        fclose($file);

        if($is_error) {
            // stop script
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
            if (strpos($filename, "ileEx") > 0) {
                $filedata = $this->Csv->import($content, array('Importfile.LineNumber',
                'Order.UserId', 'Order.ship_to_customerid','Order.phonenumber', 'Order.email',
                'Order.ship_to_street','Order.address2','Order.ship_to_city','Order.ship_to_stateprovince','Order.ship_to_zip',
                'Order.country','Order.ebayorderid','Order.external_orderid','Order.transactionid',
                'Order.itemtitle','OrdersLine.quantity','OrdersLine.unit_price','Order.shipping_costs',
                'Order.salestax','Order.insurence','Order.totalprice',
                'Order.paymentmethod','Order.paymenttrans','Order.saledate',
                'Order.checkoutdate','Order.paiddate','Order.shipdate','Order.shipservice',
                'Order.feedbackleft','Order.feedbackreceived','Order.comments','OrdersLine.sku',
                'Order.schannel_id','Ordervariation'), $options);

                $allowemptyfields = true;
                $searchcountryid = true;
            } else {

                $filedata = $this->Csv->import($content, array('Importfile.LineNumber','Order.ship_to_customerid',
                'Order.ship_to_street', 'Order.ship_to_city','Order.ship_to_stateprovince','Order.ship_to_zip',
                'Order.country_id','Order.ship_to_phone','Order.email','Order.external_orderid','Order.requested_delivery_date','Order.schannel_id',
                'Order.shipping_costs','OrdersLine.line_number','OrdersLine.sku','OrdersLine.quantity',
                'OrdersLine.unit_price'), $options);
                // $filedata = $this->Csv->import($content, array('Importfile.LineNumber','Order.ship_to_customerid',
                // 'Order.ship_to_street', 'Order.ship_to_city','Order.ship_to_stateprovince','Order.ship_to_zip',
                // 'Order.country_id','Order.external_orderid','Order.requested_delivery_date','Order.schannel_id',
                // 'Order.shipping_costs','OrdersLine.line_number','OrdersLine.sku','OrdersLine.quantity',
                // 'OrdersLine.unit_price'), $options);

                $allowemptyfields = false;
                $searchcountryid = false;
                $nextreforder = '';
                $oc = 0;
                $olc = 0;
                foreach ($filedata as $key=>$forderdata) {
                    if($key > 0) {
                        $currentreforder = $forderdata['Order']['external_orderid'];
                        if(isset($filedata[$key+1]['Order']['external_orderid']))
                            $nextreforder = $filedata[$key+1]['Order']['external_orderid'];
                        $ordersdata[$oc]['Order'] = $forderdata['Order'];
                        $ordersdata[$oc]['Importfile'] = $forderdata['Importfile'];
                        // we are in same ref order, continue to add order lines
                        $ordersdata[$oc]['OrdersLine'][$olc]['sku'] = $forderdata['OrdersLine']['sku'];
                        $ordersdata[$oc]['OrdersLine'][$olc]['quantity'] = $forderdata['OrdersLine']['quantity'];
                        $ordersdata[$oc]['OrdersLine'][$olc]['unit_price'] = $forderdata['OrdersLine']['unit_price'];
                        $ordersdata[$oc]['OrdersLine'][$olc]['line_number'] = $forderdata['OrdersLine']['line_number'];
                        //next line in CSV file is same order, we only advance
                        if($currentreforder == $nextreforder) {
                            $olc++;
                        } else {
                            //next line is different order number, we start new count of line
                            $oc++;
                            $olc =0;
                        }

                    }
                }

            }
            $startkey = 0;
            $numords = count($ordersdata);

            if($numords > 0 ) {
                foreach ($ordersdata as $key => $orderdata) {
                    
                    $this->Order->create();
                    $orderdata['Order']['user_id'] = $this->Auth->user('id');
                    $orderdata['Order']['dcop_user_id'] = $this->Auth->user('id');
                    $orderdata['Order']['status_id'] = 14;
                    $orderdata['Order']['ordertype_id'] = 1;
                    $orderdata['Order']['interface'] = 1;
                    $orderdata['Order']['transfer_id'] = $transfer_id;

                    //If country US, state_id should be a valid US state
                    if($orderdata['Order']['country_id'] == 'US') {
                        $this->loadModel('State');
                        $state = $this->State->find('first',array('conditions' => array('State.code' => $orderdata['Order']['ship_to_stateprovince'])));
                        if(!empty($state)) {
                            $orderdata['Order']['state_id'] = $state['State']['id'];
                            $orderdata['Order']['ship_to_stateprovince'] = $state['State']['name'];
                        } else {
                            $orderdata['Order']['state_id'] = 'XZ';
                            $orderdata['Order']['ship_to_stateprovince'] = '';
                        }
                    } else {
                        $orderdata['Order']['state_id'] = 'XZ';
                        $orderdata['Order']['ship_to_stateprovince'] = '';
                    }

                    $this->loadModel('Country');
                    $country = $this->Country->find('first',array('conditions' => array('Country.code' => $orderdata['Order']['country_id'])));
                    if(!empty($country)) {
                        $orderdata['Order']['country_id'] = $country['Country']['id'];
                    } else {
                        $this->errorstr = "Country ".$orderdata['Order']['country_id']." in line no. ".$orderdata['Order']['external_orderid']." does not exist";
                    }

                    $schannel = $this->Order->Schannel->find('first',array('conditions' => array('Schannel.name' => $orderdata['Order']['schannel_id'], 'Schannel.user_id' => $this->Auth->user('id'))));
                    if(!empty($schannel)) {
                        $orderdata['Order']['schannel_id'] = $schannel['Schannel']['id'];
                    } else {
                        $this->errorstr = "Sales channel ".$orderdata['Order']['schannel_id']." in order ".$orderdata['Order']['external_orderid']." does not exist";
                    }

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
                        $this->orderid = $this->Order->id;
                        foreach ($orderdata['OrdersLine'] as $key=>$orderlinedata) {
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

                            if(!isset($orderlinedata['line_number'])) {
                                $orderldata['OrdersLine']['line_number'] = 10;
                            }

                            $linenumber = ( isset($orderldata['OrdersLine']['line_number']) ? $orderldata['OrdersLine']['line_number'] : 10 );
                            $this->loadModel('OrdersLine');
                            $orderldata['OrdersLine']['type'] = 1;
                            $orderldata['OrdersLine']['status_id'] = 1;
                            $orderldata['OrdersLine']['order_id'] = $this->orderid;
                            $orderldata['OrdersLine']['line_number'] = $linenumber;
                            $orderldata['OrdersLine']['warehouse_id'] = $this->Session->read('default_warehouse');
                            $orderldata['OrdersLine']['type'] = 1;
                            $orderldata['OrdersLine']['product_id'] = $pid;
                            $orderldata['OrdersLine']['sentqty'] = 0;

                            $orderldata['OrdersLine']['quantity'] = $orderlinedata['quantity'];
                            $orderldata['OrdersLine']['unit_price'] = $unit_price;
                            $orderldata['OrdersLine']['total_line'] = $orderlinedata['quantity'] * $orderlinedata['unit_price'];
                            $orderldata['OrdersLine']['sku'] = $sku;
                            $orderldata['OrdersLine']['foc'] = 0;
                            $orderldata['OrdersLine']['user_id'] = $this->Auth->user('id');
                            $newline = $this->OrdersLine->saveAll($orderldata);
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
}
