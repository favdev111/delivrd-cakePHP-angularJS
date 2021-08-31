<?php
App::uses('AppController', 'Controller');
/**
 * Products Controller

 * @property Product $Product
 * @property PaginatorComponent $Paginator
 */
class ProductsController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Cookie', 'Search.Prg','EventRegister','InventoryManager','Csv.Csv','Shopfy', 'WooCommerce', 'Amazon', 'Access');
    public $helpers = array('Product');
    public $paginate = array();
    public $theme = 'Mtro';

    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function isAuthorized($user) {

        if (in_array($this->action, array('edit', 'delete'))) {
            $productId = (int) $this->request->params['pass'][0];
            if ($this->Product->isOwnedBy($productId, $user['id'])) {
                return true;
            } else {
                //You have no access to '. $this->action .' this product.
                $this->Session->setFlash(__('Authorization failed. You have no access for this action.'), 'admin/danger');
                return false;
            }
        }

        if (in_array($this->action, array('view'))) {
            $productId = (int) $this->request->params['pass'][0];
            if (!$this->Access->hasProductAccess($productId, 'r')) {
                $this->Session->setFlash(__('Authorization failed. You have no access for this product.'), 'admin/danger');
                return $this->redirect('index');
            }
        }

        if (in_array($this->action, array('add'))) {
            if($user['is_limited']) {
                $this->Session->setFlash(__('Authorization failed. You have no access to add products.'), 'admin/danger');
                return $this->redirect('index');
            }
        }

        if (in_array($this->action, array('upImg', 'backImg'))) {
            if(isset($user['is_admin']) && $user['is_admin'] == 1) {
                return true;
            } else {
                $this->Session->setFlash(__('Authorization failed.'), 'admin/danger');
                return $this->redirect('index');
            }
        }

        return parent::isAuthorized($user);
    }

    /**
     * index method
     *
     * @return void
     */
    public function index($status = false) {
        $this->layout = 'mtrd';

        $limit = $this->Auth->user('list_limit');
        $this->Paginator->settings = array(
            'limit' => $limit, 'order' => array('Product.modified' => 'DESC'), 'callbacks' => false
        );
        $this->loadModel('NetworksUser');
        $this->loadModel('Field');

        $joins = array(
            array('table' => 'inventories',
                'alias' => 'Inventory',
                'type'  => 'LEFT',
                'conditions' => array(
                    'Inventory.product_id = Product.id',
                    'Inventory.deleted = 0'
                )
            ),
            array('table' => 'warehouses',
                'alias' => 'Warehouse',
                'type'  => 'LEFT',
                'conditions' => array(
                    'Inventory.warehouse_id = Warehouse.id',
                )
            )
        );
        $conditions = array();
        $is_join = false;

        $get_fields = array(
            'Product.id',
            'Product.user_id',
            'Product.status_id',
            'Product.name',
            'Product.sku',
            'Product.uom',
            'Product.reorder_point',
            'Product.imageurl',
            'Category.name',

            'Product.description',
            'Group.name',
            'Product.uom',
            'Product.weight',
            'Product.width',
            'Product.height',
            'Product.depth',
            'Product.barcode',
            'Product.barcode_standards_id',
            'Product.bin',
            'Product.value',
            'Product.safety_stock',
            'Product.reorder_point',
            'Status.name',
            'Product.pageurl',
            'Color.name',
            'Size.name',
            'Product.sales_forecast',
            'Product.lead_time',

            'SUM(if(Warehouse.status = "active", Inventory.quantity, 0)) as quantity',
            'SUM( if( (Inventory.id > 0 AND Warehouse.status = "active"), 1, 0 ) ) as product_locs'
        );
        $settings = json_decode($this->Auth->user('settings'), true);

        if(!$status) {
            $conditions['Product.status_id NOT IN'] = [12, 13];
        } else {
            $conditions['Product.status_id'] = [12, 13];
        }

        if ($this->request->is('post')) {
            $this->Prg->commonProcess('Product', ['paramType'=>'query']);
        }

        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );
        if ($this->request->query('limit')) {
            $limit = $this->request->query('limit');
            $this->Paginator->settings['limit'] = $limit;
        }

        $categories = $this->Access->networkCats();
        $statuses = $this->Product->Status->find('list',array('conditions' => array('object_type_id' => 1)));

        $groups = $this->Product->Group->find('list', array('order' => array('Group.id')));
        $colors = $this->Product->Color->find('list', array('conditions' => array('user_id' => $this->Auth->user('id'))));
        if (empty($colors))
            $colors = $this->Product->Color->find('list', array('conditions' => array('user_id' => '6513d0a71f365ff2fad9a42158e212b2')));

        $sizes = $this->Product->Size->find('list', array('conditions' => array('user_id' => $this->Auth->user('id'))));
        if (empty($sizes))
            $sizes = $this->Product->Size->find('list', array('conditions' => array('user_id' => '6513d0a71f365ff2fad9a42158e212b2')));

        $filter = [];
        $filter['searchby']['text'] = '';
        if ($this->request->query('searchby')) {
            $conditions['AND']['OR'] = [
                'Product.name LIKE' => '%'. $this->request->query('searchby') .'%',
                'Product.sku' => $this->request->query('searchby'),
                'Product.barcode' => $this->request->query('searchby')
            ];
            $filter['searchby']['text'] = $this->request->query('searchby');
        }
        if ($this->request->query('status_id')) {
            $conditions['Product.status_id'] =  $this->request->query('status_id');
        }

        $filter['categories'] = ['id' => '', 'name' => ''];
        if ($this->request->query('category_id')) {
            $conditions['Product.category_id'] =  $this->request->query('category_id');
            $filter['categories']['id'] = $this->request->query('category_id');
            $cats = [];
            foreach ($categories as $cat) {
                $cats = $cats + $cat;
            }
            $filter['categories']['name'] = $cats[$this->request->query('category_id')];
        }

        $filter['group'] = ['id' => '', 'name' => ''];
        if ($this->request->query('group_id')) {
            $conditions['Product.group_id'] =  $this->request->query('group_id');
            $filter['group']['id'] = $this->request->query('group_id');
            $filter['group']['name'] = $groups[$this->request->query('group_id')];
        }

        $filter['color'] = ['id' => '', 'name' => ''];
        if ($this->request->query('color_id')) {
            $conditions['Product.color_id'] =  $this->request->query('color_id');
            $filter['color']['id'] = $this->request->query('color_id');
            $filter['color']['name'] = $colors[$this->request->query('color_id')];
        }

        $filter['size'] = ['id' => '', 'name' => ''];
        if ($this->request->query('size_id')) {
            $conditions['Product.size_id'] =  $this->request->query('size_id');
            $filter['size']['id'] = $this->request->query('size_id');
            $filter['size']['name'] = $sizes[$this->request->query('size_id')];
        }

        $filter['product'] = ['id' => '', 'name' => ''];
        if ($this->request->query('product')) {
            $filter['product']['id'] = $conditions['Product.id'] =  $this->request->query('product');
            $this->Product->id = $filter['product']['id'];
            $filter['product']['name'] = $this->Product->field('name');
        }

        $network_id = 0;
        if($this->request->query('network_id')) { // Get All products of network for which auth user have access
            $network_id = $this->request->query('network_id');
            if($network_id == 'my') {
                $conditions['Product.user_id'] = $this->Auth->user('id');
            } else {
                if(isset($this->Access->_access['Product'][$network_id])) {
                    if($this->Access->_access['Product'][$network_id] == 'all') {
                        $conditions['Product.user_id'] = $this->Access->_networks[$network_id]['created_by_user_id'];
                    } else {
                        $conditions['Product.id'] = $this->Access->_access['Product'][$network_id];
                    }
                } else {
                    $conditions['Product.id'] = [];
                }
            }
        } else {
            if(!empty($this->Access->_access['Product'])) {
                $user_ids[] = $this->Auth->user('id');
                $product_ids = [];
                foreach ($this->Access->_access['Product'] as $key => $value) {
                    if($value == 'all'){
                        $user_ids[] = $this->Access->_networks[$key]['created_by_user_id'];
                    } else {
                        $product_ids = array_merge($product_ids, $value);
                    }
                }
                $conditions['OR']['Product.user_id'] = $user_ids;
                if($product_ids) {
                    $conditions['OR']['Product.id'] = $product_ids;
                }
            } else {
                $conditions['Product.user_id'] = $this->Auth->user('id');
            }
        }

        if(!empty($this->Access->_access['Product'])) {
            $user_fids[] = $this->Auth->user('id');
            foreach ($this->Access->_access['Product'] as $key => $value) {
                $user_fids[] = $this->Access->_networks[$key]['created_by_user_id'];
            }
            $fields = $this->Field->find('all', ['contain' => array('FieldsValue'), 'conditions' => array('Field.user_id' => $user_fids, 'Field.is_filter' => 1)]);
        } else {
            $fields = $this->Field->find('all', ['contain' => array('FieldsValue'), 'conditions' => array('Field.user_id' => $this->Auth->user('id'), 'Field.is_filter' => 1)]);
        }
        
        foreach ($fields as $field) {
            if ($this->request->query('field_'. $field['Field']['id'])) {
                $conditions['FieldsData.field_id'] = $field['Field']['id'];
                if(count($field['FieldsValue']) > 0) {
                    $conditions['FieldsData.value'] = $this->request->query('field_'. $field['Field']['id']);
                    $filter['field_'. $field['Field']['id']]['id'] = $this->request->query('field_'. $field['Field']['id']);
                    foreach ($field['FieldsValue'] as $val) {
                        if($val['id'] == $this->request->query('field_'. $field['Field']['id'])) {
                            $filter['field_'. $field['Field']['id']]['name'] = $val['value'];
                            break;
                        }
                    }
                } else {
                    $conditions['FieldsData.value LIKE'] = '%'. $this->request->query('field_'. $field['Field']['id']) .'%';
                    $filter['field_'. $field['Field']['id']]['id'] = $this->request->query('field_'. $field['Field']['id']);
                    $filter['field_'. $field['Field']['id']]['name'] = $this->request->query('field_'. $field['Field']['id']);
                }

                if(!$is_join) {
                    $is_join = true;
                    $joins[] = array(
                        'table' => 'custom_data',
                        'alias' => 'FieldsData',
                        'type'  => 'LEFT',
                        'conditions' => array(
                            'Product.id = FieldsData.object_id',
                            'FieldsData.object_type = 1'
                        )
                    );
                }
            }
        }
        
        $this->Paginator->settings = array(
            'contain' => array('Category', 'Group', 'Status', 'Color', 'Size'),
            'fields' => $get_fields,
            'joins' => $joins,
            'group' => 'Product.id',
            'limit' => $limit,
            'order' => array('Product.modified' => 'DESC')
        );
        
        $products = $this->Paginator->paginate($conditions);
        #pr($products);
        #exit;
        
        $networks = Set::combine($this->Access->_networks, '{n}.created_by_user_id', '{n}.name');

        if(isset($settings['product_list']['custom']) && $settings['product_list']['custom']) {
            $this->loadModel('FieldsValue');
            $this->loadModel('FieldsData');

            $custom_values = [];
            foreach ($settings['product_list']['custom'] as $field_id => $field_value) {
                $custom_values[$field_id] = $this->FieldsValue->find('list', array(
                    'conditions' => array('FieldsValue.field_id' => $field_id),
                    'fields' => array('id', 'value')
                ));
            }
            #pr($custom_values);
            #exit;
            $custom = [];
            foreach ($products as $product) {
                $this->FieldsData->recursive = -1;
                $field_values = $this->FieldsData->find('all', array(
                    'conditions' => array('FieldsData.object_id' => $product['Product']['id'], 'FieldsData.object_type' => 1),
                    'contain' => false
                ));
                $custom[$product['Product']['id']] = array('FieldsData' => array());
                foreach ($field_values as $val) {
                    $custom[$product['Product']['id']]['FieldsData'][$val['FieldsData']['field_id']] = $val['FieldsData']['value'];
                }
            }
        }
        
        $this->countProduct();
        $this->set(compact('products', 'custom', 'custom_values', 'settings', 'categories', 'groups', 'colors', 'sizes', 'statuses', 'networks', 'network_id', 'filter', 'status', 'limit', 'options', 'fields'));
    }

    public function countProduct() {
        $options = array('Product.user_id' => $this->Auth->user('id'), 'Product.status_id NOT IN' => [12, 13], 'Product.deleted' =>0);
        $this->Session->write('productcount', $this->Product->find('count', array('conditions' => $options)));
    }

    public function list_fields() {
        $this->layout = false;

        $fields = array('Image' => 'Product.imageurl', 'SKU' => 'Product.sku', 'Product Name' => 'Product.name', 'Category' => 'Warehouse.name', 'Product Inventory' => 'quantity');

        $available_fields = array(
            'Description' => 'Product.description',
            'PageUrl' => 'Product.pageurl',
            'UOM' => 'Product.uom',
            'Weight' => 'Product.weight',
            'Width' => 'Product.width',
            'Height' => 'Product.height',
            'Depth' => 'Product.depth',
            'Barcode' => 'Product.barcode',
            'BarcodeStandards' => 'Product.barcode_standards_id',
            'Bin' => 'Product.bin',
            'Price' => 'Product.value',
            'SafetyStock' => 'Product.safety_stock',
            'ReorderPoint' => 'Product.reorder_point',
            'SalesForecast' => 'Product.sales_forecast',
            'LeadTime' => 'Product.lead_time',
            'Status' => 'Status.name',
            'Group' => 'Group.name',
            'Color' => 'Color.name',
            'Size' => 'Size.name'
        );

        if ($this->request->is('post')) {
            $this->loadModel('User');

            $this->User->saveSetting($this->Auth->user('id'), 'product_list', $this->request->data);
            $response['action'] = 'success';
            echo json_encode($response);
            exit;
        }

        $this->loadModel('Field');
        $this->Field->recursive = 0;
        if(!empty($this->Access->_access['Product'])) {
            $user_ids[] = $this->Auth->user('id');
            foreach ($this->Access->_access['Product'] as $key => $value) {
                $user_ids[] = $this->Access->_networks[$key]['created_by_user_id'];
                
            }
            $custom_fields = $this->Field->find('list', array('conditions' => array('Field.user_id' => $user_ids), 'contain' => false ));
        } else {
            $custom_fields = $this->Field->find('list', array('conditions' => array('Field.user_id' => $this->Auth->user('id')), 'contain' => false ));
        }

        

        $def_fields = [];
        $settings = json_decode($this->Auth->user('settings'), true);
        if(isset($settings['product_list']) &&  $settings['product_list']) {
            $def_fields = $settings['product_list'];
        }
        
        $this->request->data = $def_fields;

        $this->set(compact('fields', 'available_fields', 'def_fields', 'custom_fields'));
    }

    /**
     * add method
     *
     * @return void
     */
    public function addproduct() {
        if($this->request->is('ajax')) {
            $this->request->data['Product']['user_id'] = $this->Session->read('Auth.User.id');
            $this->request->data['Product']['value'] = 0;
            $this->request->data['Product']['status_id'] = 1;
            $this->request->data['Product']['consumption'] = 0;
            $this->request->data['Product']['deleted'] = 0;
            $this->request->data['Product']['imageurl'] = Configure::read('Product.image_missing');
            if ($this->Product->saveAll($this->request->data)) {
            	$this->createinventoryrecord($this->Product->id, $this->request->data['Product']['stock_quantity']);
                $response['status'] = true;
                $response['success'] = 'Product "'. $this->request->data('Product.name') . '" successfully added';
                $response['errors'] = '';
            } else {
               $response['status'] = false;
               $response['success'] = 'The Product could not be saved. Please, try again.';
               $response['errors'] = $this->Product->validationErrors;
            }
        }
        echo json_encode($response);
        die;
    }

    public function getproduct() {
        $this->Session->delete('Flash');
        if (!empty($this->request->data)) {
            $this->request->data['Product']['user_id'] = $this->Session->read('Auth.User.id');
            $this->request->data['Product']['value'] = 0;
            $this->request->data['Product']['status_id'] = 1;
            $this->request->data['Product']['consumption'] = 0;
            $this->request->data['Product']['deleted'] = 0;
            $this->request->data['Product']['imageurl'] = Configure::read('Product.image_missing');
            if ($this->Product->saveAll($this->request->data)) {
                $this->createinventoryrecord($this->Product->id);
                $response['status'] = true;
                $response['message']='Product '. $this->request->data('Product.name') . ' added';
                echo json_encode($response);
                die;
            } else {
               $response['status'] = false;
               $Product = $this->Product->invalidFields();
               $response['data']=compact('Product');
               $response['message']='The Product could not be saved. Please, try again.';
               echo json_encode($response);
               die;
            }
        }
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        $this->layout = 'mtrd';

        $this->loadModel('Kit');
        $this->loadModel('Field');
        $this->loadModel('FieldsData');

        //Get packaging material data
        $options = array('Product.' . $this->Product->primaryKey => $id);
        $product = $this->Product->find('first', array(
            'conditions' => $options,
            'contain' => array(
                //'Productsupplier.Supplier' => array('fields' => ['name']),
                'Inventory', 'Category', 'Issue', 'Receive', 'Status','Group','Size','Color','Bin',
                'Serial' => array('fields' => array('Serial.product_id')),
                'OrdersLine' => array('fields' => array('OrdersLine.id','OrdersLine.type'))
            ),
            'callbacks' => false
        ));

        $this->loadModel('Warehouse');
        $warehouses = $this->Warehouse->find('list', [
            'conditions' => [
                'Warehouse.user_id' => $product['Product']['user_id'],
                'Warehouse.status' => 'active'
            ],
            'fields' => ['Warehouse.id', 'Warehouse.name'],
            'callbacks' => false
        ]);

        $suppliers = $this->Product->Productsupplier->find('all', array('conditions' => array('Productsupplier.product_id' => $id), 'contain' => 'Supplier'));

        $ordertype1 = array();
        $ordertype2 = array();

        if (!$product) {
            throw new NotFoundException(__('Invalid product'));
        }

        $ex = [];
        foreach($product['OrdersLine'] as $orderline) {
            if($orderline['type'] == 1 && !in_array($orderline['order_id'], $ex)) {
                $ex[] = $orderline['order_id'];
                $ordertype1[] = $orderline;
            }
            if($orderline['type'] == 2 && !in_array($orderline['order_id'], $ex)) {
                $ex[] = $orderline['order_id'];
                $ordertype2[] = $orderline;
            }
        }
        
        $cord_count = count($ordertype1);
        $rord_count = count($ordertype2);
        $serials = count($product['Serial']);

        $prefix = $produts = '';
        foreach($product['Bin'] as $key => $bins) {
          $produts .= $prefix . $bins['title'];
          $prefix = ', ';
        }

        $bin[] = $produts;

        $total_inventory = 0;
        foreach ($product['Inventory'] as $inv_record) {
            if(array_key_exists($inv_record['warehouse_id'], $warehouses)) {
                $total_inventory += $inv_record['quantity'] + $inv_record['damaged_qty'];
            }
        }
        //exit;

        if($total_inventory == 0) {
            $inventory_badge = "badge badge--danger";
        }
        if($total_inventory == 0) {
            $inventory_badge = "badge badge-success";
        }

        $inventory_badge = ($total_inventory < $product['Product']['safety_stock'] ? "badge badge-danger" : "badge badge-success");
        $objectevents = $this->EventRegister->getObjectEvent(1,$id,$this->Auth->user('id'));
        $this->set('product', $product);

        
        $allowedstatusesrepl = [1,12];
        $parts = $this->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'), 'Product.status_id' => $allowedstatusesrepl)));
        foreach ($parts as $key => $value) {
            $parts_a[] = array('id' => $key, 'name' => $value);
        }

        $this->Kit->recursive = -1;
        $product_parts = $this->Kit->find('all',array('conditions' => array('Kit.product_id' => $id)));

        $this->loadModel('Schannel');
        $schannels = $this->Schannel->find('list',array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));
        $schannels_a = [];
        foreach ($schannels as $key => $value) {
            $schannels_a[] = array('id' => $key, 'name' => $value);
        }

        $this->Field->recursive = 0;
        $fields = $this->Field->find('all', array('conditions' => array('Field.user_id' => $this->Auth->user('id')), 'contain' => array('FieldsValue' => array('id', 'value')) ));
        
        $this->FieldsData->recursive = -1;
        $field_values = $this->FieldsData->find('all', array('conditions' => array('FieldsData.object_id' => $id, 'FieldsData.object_type' => 1), 'contain' => false ));
        $custom = array('FieldsData' => array());
        foreach ($field_values as $val) {
            $custom['FieldsData'][$val['FieldsData']['field_id']] = $val['FieldsData']['value'];
        }

        $this->set(compact('cord_count','rord_count','serials','total_inventory','inventory_badge','objectevents','bin', 'suppliers', 'schannels', 'schannels_a', 'fields', 'custom'));
        $pack_mat = $this->Product->findById($product['Product']['packaging_material_id']);

        if(isset($pack_mat)) {
            $this->set('pack_mat',$pack_mat);
        }

        $this->loadModel('Document');
        $this->Document->recursive = -1;
        $documents = $this->Document->find('all', array(
            'conditions' => array('model_type' => 'product', 'model_id' => $id),
        ));

        $this->set(compact('product_parts', 'parts_a', 'parts', 'documents'));
    }


    /**
     * atp method (Available to Promise)
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function atp($id = null) {
        $this->layout = 'mtrd';

        $this->Product->recursive = -1;
        $options = array('Product.id' => $id);
        $product = $this->Product->find('first', array(
            'conditions' => $options,
            'contain' => array(),
            'callbacks' => false
        ));

        $this->loadModel('Forecast');
        if ($this->request->is('post', 'put')) {
            $data = [];

            foreach ($this->request->data['forecast'] as $key => $value) {
                $this->Forecast->deleteAll(['period' => 'month', 'product_id' => $id, 'forecast_date' => $key .'-01']);
                $row['product_id'] = $id;
                $row['period'] = 'month';
                $row['forecast_date'] = $key .'-01';
                $row['forecast'] = $value;
                $data[] = $row;
            }
            if($this->Forecast->saveMany($data, ['deep' => false])) {
                $response['action'] = 'success';
                $response['message'] = 'Forecast values successfully saved';
            } else {
                $response['action'] = 'error';
                $response['message'] = 'Can\'t save forecast values. Please try again later.';
            }
            echo json_encode($response);
            exit;
        } else {
            
            $forecasts = $this->Forecast->getMonthlyForecast($id, intval($product['Product']['sales_forecast']));

            $this->loadModel('Inventory');
            $this->Inventory->virtualFields['total'] = 'SUM(Inventory.quantity)';
            $inventory = $this->Inventory->find('first', array(
                'conditions' => array(
                    'Inventory.product_id' => $id,
                    'Inventory.deleted' => 0,
                    'Warehouse.status' => 'active'
                ),
                'fields'  => array(
                    'Inventory.product_id',
                    'Inventory.total',
                ),
                'group' => 'Inventory.product_id',
                'recursive' => 1
            ));

            $this->loadModel('OrdersLine');

            $today = date('Y-m');
            $from = date('Y-m-d 00:00:00', strtotime($today));
            $before = date('Y-m-01 00:00:00', strtotime($from .' + 12 month'));

            $this->OrdersLine->virtualFields['total_received'] = 'SUM(OrdersLine.quantity - OrdersLine.receivedqty)';
            $this->OrdersLine->virtualFields['lines'] = 'CONCAT_WS(",", OrdersLine.id)';
            $this->OrdersLine->virtualFields['month'] = 'DATE_FORMAT( (Order.requested_delivery_date), "%Y-%m")';
            $lines2 = $this->OrdersLine->find('all', array(
                'conditions' => [
                    'OrdersLine.product_id' => $id,
                    'Order.ordertype_id' => 2,
                    'Order.status_id IN' => [2,3],
                    'Order.requested_delivery_date >=' => $from,
                    'Order.requested_delivery_date <' => $before,
                    'OrderSchedule.id' => null
                ],
                'fields' => [
                    'lines',
                    'total_received',
                    'month',
                    'unit_price'
                ],
                'contain' => ['OrderSchedule', 'Order'],
                'group' => 'month'
            ));

            $lines2 = Set::combine($lines2, '{n}.OrdersLine.month', '{n}');

            $this->OrdersLine->virtualFields['total_received'] = 'SUM(OrdersLine.quantity - OrdersLine.receivedqty)';
            $this->OrdersLine->virtualFields['total_value'] = 'SUM((OrdersLine.quantity - OrdersLine.receivedqty) * OrdersLine.unit_price)';
            $this->OrdersLine->virtualFields['month'] = 'DATE_FORMAT( (OrderSchedule.delivery_date), "%Y-%m")';
            $lines = $this->OrdersLine->find('all', array(
                'conditions' => [
                    'OrdersLine.product_id' => $id,
                    'Order.ordertype_id' => 2,
                    'Order.status_id IN' => [2,3],
                    'OrderSchedule.delivery_date >=' => $from,
                    'OrderSchedule.delivery_date <' => $before
                ],
                'fields' => [

                    'total_received',
                    'total_value',
                    'month',
                    'unit_price'
                ],
                'contain' => ['OrderSchedule', 'Order'],
                'group' => 'month'
            ));

            $lines = Set::combine($lines, '{n}.OrdersLine.month', '{n}');

            foreach($forecasts as $forecast) {
                if(empty($lines[$forecast['month']])) {
                    $lines[$forecast['month']] = array(
                        'OrdersLine' => [
                            'total_received' => 0,
                            'total_value' => 0,
                            'month' => $forecast['month'],
                            'value' => 0,
                        ]
                    );
                } else {
                    $lines[$forecast['month']]['OrdersLine']['total_received'] = intval($lines[$forecast['month']]['OrdersLine']['total_received']);
                    $lines[$forecast['month']]['OrdersLine']['total_value'] = round($lines[$forecast['month']]['OrdersLine']['total_value'], 2);
                    $lines[$forecast['month']]['OrdersLine']['month'] = $forecast['month'];
                    $lines[$forecast['month']]['OrdersLine']['value'] = round($lines[$forecast['month']]['OrdersLine']['unit_price'], 2);
                    
                }
                if($forecast['forecast']) {
                    $lines[$forecast['month']]['Forecast']['value'] = $forecast['forecast'];
                } else {
                    $lines[$forecast['month']]['Forecast']['value'] = 0; //intval($product['Product']['sales_forecast']);
                }
                unset($lines[$forecast['month']]['Order']);

                if(isset($lines2[$forecast['month']])) {
                    $lines[$forecast['month']]['OrdersLine']['total_received'] = $lines[$forecast['month']]['OrdersLine']['total_received'] + $lines2[$forecast['month']]['OrdersLine']['total_received'];
                }
            }

            ksort($lines);
            $this->set(compact('product', 'lines', 'inventory'));
        }
    }

    /**
     * atp method (Available to Promise)
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function atp_import($id = null) {
        $this->layout = 'mtrd';

        $this->Product->recursive = -1;
        $options = array('Product.id' => $id);
        $product = $this->Product->find('first', array(
            'conditions' => $options,
            'contain' => array(),
            'callbacks' => false
        ));

        $this->loadModel('Forecast');
        
        
        $forecasts = $this->Forecast->getMonthlyForecast($id, intval($product['Product']['sales_forecast']));

        $this->loadModel('Inventory');
        $this->Inventory->virtualFields['total'] = 'SUM(Inventory.quantity)';
        $inventory = $this->Inventory->find('first', array(
            'conditions' => array(
                'Inventory.product_id' => $id,
                'Inventory.deleted' => 0,
                'Warehouse.status' => 'active'
            ),
            'fields'  => array(
                'Inventory.product_id',
                'Inventory.total',
            ),
            'group' => 'Inventory.product_id',
            'recursive' => 1
        ));

        $this->loadModel('OrdersLine');

        $today = date('Y-m');
        $from = date('Y-m-d 00:00:00', strtotime($today));
        $before = date('Y-m-01 00:00:00', strtotime($from .' + 12 month'));

        $this->OrdersLine->virtualFields['total_received'] = 'SUM(OrdersLine.quantity - OrdersLine.receivedqty)';
        $this->OrdersLine->virtualFields['lines'] = 'CONCAT_WS(",", OrdersLine.id)';
        $this->OrdersLine->virtualFields['month'] = 'DATE_FORMAT( (Order.requested_delivery_date), "%Y-%m")';
        $lines2 = $this->OrdersLine->find('all', array(
            'conditions' => [
                'OrdersLine.product_id' => $id,
                'Order.ordertype_id' => 2,
                'Order.status_id IN' => [2,3],
                'Order.requested_delivery_date >=' => $from,
                'Order.requested_delivery_date <' => $before,
                'OrderSchedule.id' => null
            ],
            'fields' => [
                'lines',
                'total_received',
                'month'
            ],
            'contain' => ['OrderSchedule', 'Order'],
            'group' => 'month'
        ));

        $lines2 = Set::combine($lines2, '{n}.OrdersLine.month', '{n}');

        $this->OrdersLine->virtualFields['total_received'] = 'SUM(OrdersLine.quantity - OrdersLine.receivedqty)';
        $this->OrdersLine->virtualFields['total_value'] = 'SUM((OrdersLine.quantity - OrdersLine.receivedqty) * OrdersLine.unit_price)';
        $this->OrdersLine->virtualFields['month'] = 'DATE_FORMAT( (OrderSchedule.delivery_date), "%Y-%m")';
        $lines = $this->OrdersLine->find('all', array(
            'conditions' => [
                'OrdersLine.product_id' => $id,
                'Order.ordertype_id' => 2,
                'Order.status_id IN' => [2,3],
                'OrderSchedule.delivery_date >=' => $from,
                'OrderSchedule.delivery_date <' => $before
            ],
            'fields' => [
                'total_received',
                'total_value',
                'month'
            ],
            'contain' => ['OrderSchedule', 'Order'],
            'group' => 'month'
        ));

        $lines = Set::combine($lines, '{n}.OrdersLine.month', '{n}');

        foreach($forecasts as $forecast) {
            if(empty($lines[$forecast['month']])) {
                $lines[$forecast['month']] = array(
                    'OrdersLine' => [
                        'total_received' => 0,
                        'total_value' => 0,
                        'month' => $forecast['month'],
                    ]
                );
            } else {
                $lines[$forecast['month']]['OrdersLine']['total_received'] = intval($lines[$forecast['month']]['OrdersLine']['total_received']);
                $lines[$forecast['month']]['OrdersLine']['total_value'] = round($lines[$forecast['month']]['OrdersLine']['total_value'], 2);
                $lines[$forecast['month']]['OrdersLine']['month'] = $forecast['month'];
                
            }
            if($forecast['forecast']) {
                $lines[$forecast['month']]['Forecast']['value'] = $forecast['forecast'];
            } else {
                $lines[$forecast['month']]['Forecast']['value'] = 0; //intval($product['Product']['sales_forecast']);
            }
            unset($lines[$forecast['month']]['Order']);

            if(isset($lines2[$forecast['month']])) {
                $lines[$forecast['month']]['OrdersLine']['total_received'] = $lines[$forecast['month']]['OrdersLine']['total_received'] + $lines2[$forecast['month']]['OrdersLine']['total_received'];
            }
        }

        ksort($lines);
        $csv_lines = [];
        $start = intval($inventory['Inventory']['total']);
        foreach ($lines as $month => $line) {
            $csv_line = [];

            if(date('Y-m', strtotime($month)) == date('Y-m')) {
                $cur_month_forecast = round(((date('t') - date('j'))/date('t')) * intval($line['Forecast']['value']));
            } else {
                $cur_month_forecast = intval($line['Forecast']['value']);
            }
            $start = ($start - $cur_month_forecast + $line['OrdersLine']['total_received']);

            $csv_line['Month'] = $line['OrdersLine']['month'];
            $csv_line['Receipts'] = $line['OrdersLine']['total_received'];
            $csv_line['TotalValue'] = $line['OrdersLine']['total_value'];
            $csv_line['Demand'] = $cur_month_forecast;
            $csv_line['ATP'] = $start;
            $csv_lines[] = $csv_line;
        }

        $_serialize = 'csv_lines';
        $_header = array('Month','Receipts','Demand', 'ATP','TotalValue');
        $_extract = array('Month','Receipts','Demand', 'ATP','TotalValue');

        $file_name = "Delivrd_".date('Y-m-d-His')."_atp.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('csv_lines', '_serialize', '_header', '_extract'));
    }

    function upload_fc_csv() {
        $matchColumnDisplay = false;
        if($this->request->is('post', 'put')) {
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
                echo json_encode($filesg, JSON_PRETTY_PRINT);
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
        $this->set('matchColumnDisplay', $matchColumnDisplay);
        $this->layout = 'mtrd';
    }

    function import_fc_csv($filename = null, $uname = null) {
        $this->layout = null;
        $is_error = false;
        $msg = '';
        $errors = [];

        $numrec = 0;
        $success = 0;
        $danger = 0;

        $content = WWW_ROOT."uploads/".$filename;
       //first, get number of columns to see if this is a basic file or full file
        $numcols = 0;
        $file = fopen($content, "r"); 
        while ($line = fgetcsv($file)) {
            $numcols = count($line);
            if($numcols != 3) {
                $msg = "CSV file should have 4 columns, but line ".$line[0]." has ".$numcols." columns";
                $is_error = true;
            }
            break;
        }
        fclose($file);
        
        if(!$is_error) {

            /*$this->loadModel('Transfer');
            $this->Transfer->create();
            $log['user_id'] = $this->Auth->user('id');
            $log['type'] = Transfer::$types['inventories'];
            $log['direction'] = Transfer::$direction['import'];
            $log['source'] = Transfer::$source['csv'];
            $log['source_id'] = 0;
            $log['status'] = Transfer::$status['started'];
            $log['recordscount'] = 0;
            $log['response'] = '';
            $this->Transfer->save($log);
            $transfer_id = $this->Transfer->id;*/

            $filedata = $this->Csv->import($content, array('Product.sku', 'Forecast.forecast_date', 'Forecast.forecast'));
            $numrec = count($filedata) - 1;
            $this->loadModel('Forecast');
            foreach ($filedata as $key => $forecast) {
                if($key > 0) {
                    $err = false;
                    if(empty($forecast['Product']['sku'] || empty($forecast['Forecast']['forecast_date'] || empty($forecast['Forecast']['forecast'])))) {
                        $errors[] = "SKU or Forecast data are missing in line ".$key;
                        $err = true;
                    }
            
                    $pid = $this->Product->find('first', array('fields' => array('Product.id'), 'contain' => false, 'conditions' => array('Product.sku' => $forecast['Product']['sku'], 'Product.user_id' => $this->Auth->user('id'))));
                    if(empty($pid)) {
                        $errors[] = "SKU ".$forecast['Product']['sku']." does not exist, line number ".$key;
                        $err = true;
                    }
                    
                    if( !preg_match('/^(0?[1-9]|10|11|12)\/20[0-9]{2}$/', $forecast['Forecast']['forecast_date']) ) {
                        $errors[] = "Forecast date must be in format mm/yyyy, line ".$key;
                        $err = true;
                    }

                    if(!$err) {
                        $new_data = [];
                        $new_data['Forecast']['product_id'] = $pid['Product']['id'];
                        $date = explode('/', $forecast['Forecast']['forecast_date']);
                        $new_data['Forecast']['forecast_date'] = date('Y-m-d', strtotime($date[1] .'-'. $date[0] .'-01'));
                        $new_data['Forecast']['period'] = 'month';
                        $new_data['Forecast']['forecast'] = intval($forecast['Forecast']['forecast']);
                        $new_data['Forecast']['created'] = date('Y-m-d H:i:s');
                        
                        $this->Forecast->deleteAll(['period' => 'month', 'product_id' => $new_data['Forecast']['product_id'], 'forecast_date' => $new_data['Forecast']['forecast_date']]);
                        $this->Forecast->create();
                        if($this->Forecast->save($new_data)) {
                            $success++;
                        } else {
                            $danger++;
                            $errors[] = 'Line '.$key.' couldn\'t be saved';
                        }
                       
                    } else {
                        $danger++;
                    }
                }
            }
            // Update Log. Success.
            /*if($success > 0) {
                $log['status'] = Transfer::$status['success'];
            } else {
                $log['status'] = Transfer::$status['failed'];
            }
            $log['recordscount'] = $success;
            $msg['total_found'] = $numrec;
            $msg['added'] = $success;
            $msg['updated'] = 0;
            $msg['errors_count'] = $danger;
            $msg['errors'] = $errors;
            $log['response'] = json_encode($msg); 
            $this->Transfer->save($log);*/
        }

        $this->set(compact('filename', 'uname', 'is_error', 'msg', 'numrec', 'errors', 'success', 'danger'));
    }

    function forecastcsv() {
        $forecasts = [
            ['sku' => 'B05543234', 'date' => '2/2019', 'forecast' => '25'],
            ['sku' => 'B05543234', 'date' => '3/2019', 'forecast' => '27'],
            ['sku' => 'B05543234', 'date' => '4/2019', 'forecast' => '35'],
            ['sku' => 'B07743234', 'date' => '1/2019', 'forecast' => '30'],
            ['sku' => 'B07743234', 'date' => '2/2019', 'forecast' => '35'],
            ['sku' => 'B07743234', 'date' => '12/2019', 'forecast' => '31'],
        ];
        $_serialize = 'forecasts';
        $_header = ['SKU', 'Date', 'Forecast'];
        $_extract = ['sku', 'date', 'forecast'];

        $file_name = "Delivrd_".date('Y-m-d-His')."_forecast.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('forecasts', '_serialize', '_header', '_extract'));
    }


    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $this->layout = 'mtrd';

        $this->loadModel('Field');
        $this->loadModel('FieldsData');
        $this->loadModel('ProductsPrices');
        $this->loadModel('Kit');

        $show_trial = false;
        if($this->Auth->user('role') != 'trail' && $this->Auth->user('role') != 'paid') {
            $productcount = $this->Product->find('count', array('conditions' => ['Product.user_id' => $this->Auth->user('id'), 'Product.status_id NOT IN' => [12, 13], 'Product.deleted' =>0]));
            if($productcount >= Configure::read('Productlimit.freeuser')) {
                $show_trial = true;
            }
        }
#var_dump($show_trial);
#exit;
        // check url of image is in really an image
        $errors = array();
        if ($this->request->is('post', 'put')) {
            if($show_trial) {
                $this->Session->setFlash(__('You need upgrade you account to add more products'), 'admin/danger', array());
                return $this->redirect(array('action' => 'add'));
            }
            if (!$this->request->data['Product']['imageurl']) {
                $this->request->data['Product']['imageurl'] = Configure::read('Product.image_missing');
            } else {
                $imageexists = $this->isValidUrl($this->request->data['Product']['imageurl']);
            }

            $this->Product->create();
            $this->request->data('Product.user_id', $this->Auth->user('id'));
            $this->request->data('Product.status_id', 1);
            $this->request->data('Product.deleted', 0);
            $this->request->data('Product.name', strip_tags($this->request->data['Product']['name']) );
            $this->request->data('Product.description', strip_tags($this->request->data['Product']['description']) );

            if ($this->Product->save($this->request->data)) {

                if(isset($this->request->data['FieldsData'])) {
                    $this->FieldsData->saveProduct($this->Product->id, $this->request->data['FieldsData']);
                }
                // Save kits
                $this->Kit->deleteAll(['Kit.product_id' => $this->Product->id], false);
                if($this->request->data['Product']['uom'] == 'Kit') {
                    if(!empty($this->request->data['Kit'])) {
                        $parts = $this->request->data['Kit'];
                        foreach ($parts as $key => $value) {
                            $parts[$key]['Kit']['user_id'] = $this->Auth->user('id');
                            $parts[$key]['Kit']['product_id'] = $this->Product->id;
                            $parts[$key]['Kit']['active'] = (($value['Kit']['active'] == 'true')?'1':'0');
                        }
                        $this->Kit->saveMany($parts);
                    }
                }

                // Save channel prices
                $this->ProductsPrices->deleteAll(['ProductsPrices.product_id' => $this->Product->id], false);
                if(!empty($this->request->data['ProductsPrices'])) {
                    $prpr = $this->request->data['ProductsPrices'];
                    foreach ($prpr as $key => $value) {
                        $prpr[$key]['ProductsPrices']['product_id'] = $this->Product->id;
                    }
                    $this->ProductsPrices->saveMany($prpr);
                }
                // End channel prices

                if($this->request->data['Product']['issue_location'] == $this->request->data['Product']['receive_location']) {
                    $this->createinventoryrecord($this->Product->id, 0, $this->request->data['Product']['issue_location']);
                } else {
                    if(!empty($this->request->data['Product']['issue_location']))
                    $this->createinventoryrecord($this->Product->id, 0, $this->request->data['Product']['issue_location']);
                    if(!empty($this->request->data['Product']['receive_location']))
                        $this->createinventoryrecord($this->Product->id, 0, $this->request->data['Product']['receive_location']);
                    if(empty($this->request->data['Product']['receive_location']) && empty($this->request->data['Product']['issue_location']))
                    {
                        $this->createinventoryrecord($this->Product->id, 0);
                    }
                }

                $this->Session->setFlash(__('The Product  %s, SKU number  %s, has been added successfully.', $this->request->data('Product.name'), $this->request->data('Product.sku')), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));

            } else {
                $errors = $this->Product->validationErrors;
                $this->Session->setFlash(__('The Product could not be saved. Please, try again.'), 'admin/danger', array());
            }
        }

        if ($this->Auth->user('msystem_id') == 1) {
            $weight_unit = Configure::read('Metric.weight');
            $volume_unit = Configure::read('Metric.volume');
        }
        if ($this->Auth->user('msystem_id') == 2) {
            $weight_unit = Configure::read('US.weight');
            $volume_unit = Configure::read('US.volume');
        }

        $this->loadModel('Schannel');
        $schannels = $this->Schannel->find('list',array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));
        $schannels_a = [];
        foreach ($schannels as $key => $value) {
            $schannels_a[] = array('id' => $key, 'name' => $value);
        }

        $this->set(compact('weight_unit', 'volume_unit'));

        $this->loadModel('Currency');
        $currencies = $this->Currency->find('first', array('conditions' => array('id' => $this->Auth->user('currency_id'))));
        if (isset($currencies)) {
            $currencyname = 'USD';
        } else {
            $currencyname = 'USD';
        }
        $this->set(compact('currencyname'));
        $users = $this->Product->User->find('list');
        $packmaterials = $this->Product->find('all', array('conditions' => array('Product.consumption' => true, 'Product.user_id' => $this->Auth->user('id'))));
        //$packmaterialsarr[0] = 'Other';
        foreach ($packmaterials as $packmaterial) {
            $packmaterialsarr[$packmaterial['Product']['id']] = $packmaterial['Product']['name'];
        }
        $this->set(compact('packmaterialsarr'));
        $groups = $this->Product->Group->find('list', array('order' => array('Group.id')));
        $categories = $this->Product->Category->find('list', array('order' => array('Category.id'), 'conditions' => array('user_id' => $this->Auth->user('id'))));
        //User can cretae thier own colors and sizes data. If they did not create anything, we display a list of
        //standard values under non-existing user id 6513d0a71f365ff2fad9a42158e212b2
        $colors = $this->Product->Color->find('list', array('conditions' => array('user_id' => $this->Auth->user('id'))));
        if (empty($colors))
            $colors = $this->Product->Color->find('list', array('conditions' => array('user_id' => '6513d0a71f365ff2fad9a42158e212b2')));

        $sizes = $this->Product->Size->find('list', array('conditions' => array('user_id' => $this->Auth->user('id'))));
        if (empty($sizes))
            $sizes = $this->Product->Size->find('list', array('conditions' => array('user_id' => '6513d0a71f365ff2fad9a42158e212b2')));

        //Generate sku to suggest user
        $suggestedsku = $this->generatesku();
        $bins = $this->Product->Bin->find('list',array('conditions' => array('Bin.user_id' => $this->Auth->user('id'), 'Bin.status' => 1)));
        $this->loadModel('Bin');
        $locations = $this->Bin->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'))));
        $status = $this->Bin->status;
        $warehouses = $this->Product->Issue->find('list',array('conditions' => array('user_id' => $this->Auth->user('id'))));
        $uoms = array_combine($this->uoms, $this->uoms);

        $allowedstatusesrepl = [1,12];
        $parts = $this->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'), 'Product.uom !=' => 'Kit', 'Product.status_id' => $allowedstatusesrepl)));
        foreach ($parts as $key => $value) {
            $parts_a[] = array('id' => $key, 'name' => $value);
        }

        $this->Field->recursive = 0;
        $fields = $this->Field->find('all', array('conditions' => array('Field.user_id' => $this->Auth->user('id')), 'contain' => array('FieldsValue' => array('id', 'value')) ));

        $this->set(compact('groups', 'parts', 'parts_a', 'sizes', 'colors', 'suggestedsku', 'categories', 'uoms', 'errors', 'bins', 'locations', 'status', 'warehouses', 'schannels', 'schannels_a', 'fields', 'show_trial'));

        if ($this->request->is('post')) {
            $this->render('/Elements/product_add');
        }
    }

    public function copy($id = null) {
        $this->layout = 'mtrd';
        if ($this->request->is(array('post', 'put'))) {
            //Debugger::dump($this->request);
            $this->Product->create();
            $this->request->data('Product.user_id',$this->Auth->user('id'));
            $this->request->data('Product.status_id',1);
            if ($this->Product->save($this->request->data)) {
                $this->Session->setFlash(__('The Product has been added.'), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Product could not be saved. Please, try again.'), 'admin/danger', array());
            }
        } else {
            $options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id));
            $this->request->data = $this->Product->find('first', $options);
        }
        if ($this->Auth->user('msystem_id') == 1) {
            $weight_unit = Configure::read('Metric.weight');
            $volume_unit = Configure::read('Metric.volume');
        }
        if ($this->Auth->user('msystem_id') == 2)
        {
        $weight_unit = Configure::read('US.weight');
        $volume_unit = Configure::read('US.volume');
        }
        $this->set(compact('weight_unit', 'volume_unit'));
        $currency = $this->Auth->user('currency_id');
        $users = $this->Product->User->find('list');
        $groups = $this->Product->Group->find('list');
        $this->set(compact('users', 'groups'));
        $packmaterials = $this->Product->find('all', array('conditions' => array('consumption' => true)));
        $packmaterialsarr[0] = 'Other';
        foreach($packmaterials as $packmaterial)
        {
        $packmaterialsarr[$packmaterial['Product']['id']] = $packmaterial['Product']['name'];
        }
        $colors = $this->Product->Color->find('list',array('conditions' => array('user_id' => $this->Auth->user('id'))));
        if(empty($colors))
            $colors = $this->Product->Color->find('list',array('conditions' => array('user_id' => '6513d0a71f365ff2fad9a42158e212b2')));

        $sizes = $this->Product->Size->find('list',array('conditions' => array('user_id' => $this->Auth->user('id'))));
        if(empty($sizes))
            $sizes = $this->Product->Size->find('list',array('conditions' => array('user_id' => '6513d0a71f365ff2fad9a42158e212b2')));
        $suggestedsku = $this->generatesku();
        $this->set(compact('packmaterialsarr','suggestedsku','sizes','colors'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $this->layout = 'mtrd';
        if (!$this->Product->exists($id)) {
            throw new NotFoundException(__('Invalid product'));
        }

        $this->loadModel('Field');
        $this->loadModel('FieldsData');
        $this->loadModel('ProductsPrices');
        $this->loadModel('Kit');

        $product = $this->Product->find('first', array('conditions' => array('Product.id' => $id)));
        if ($this->request->is(array('post', 'put'))) {
            //check if SKU is not in use by this user
            $skuexist = $this->Product->findAllBysku($this->request->data['Product']['sku']);

            if ($this->Product->save($this->request->data)) {
                if(isset($this->request->data['FieldsData'])) {
                    $this->FieldsData->saveProduct($id, $this->request->data['FieldsData']);
                }

                // Save kits
                if($this->request->data['Product']['uom'] != 'Kit') {
                    $this->Kit->deleteAll(['Kit.product_id' => $this->Product->id], false);
                }

                // Save channel prices
                /*$this->ProductsPrices->deleteAll(['ProductsPrices.product_id' => $this->Product->id], false);
                if(!empty($this->request->data['ProductsPrices'])) {
                    $prpr = $this->request->data['ProductsPrices'];
                    foreach ($prpr as $key => $value) {
                        $prpr[$key]['ProductsPrices']['product_id'] = $this->Product->id;
                    }
                    $this->ProductsPrices->saveMany($prpr);
                }*/
                // End channel prices

                $this->Session->setFlash(__('The Product has been added.'), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Product could not be saved. Please, try again.'), 'admin/danger', array());
            }
        } else {
            $this->request->data = $product;
        }

        $this->loadModel('Kit');
        $allowedstatusesrepl = [1,12];
        $parts = $this->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'), 'Product.uom !=' => 'Kit', 'Product.status_id' => $allowedstatusesrepl)));
        foreach ($parts as $key => $value) {
            $parts_a[] = array('id' => $key, 'name' => $value);
        }

        $this->Kit->recursive = -1;
        $product_parts = $this->Kit->find('all',array('conditions' => array('Kit.product_id' => $id)));

        $this->loadModel('Schannel');
        $schannels = $this->Schannel->find('list',array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));
        $schannels_a = [];
        foreach ($schannels as $key => $value) {
            $schannels_a[] = array('id' => $key, 'name' => $value);
        }
        
        $this->ProductsPrices->recursive = -1;
        $channel_prices = $this->ProductsPrices->find('all',array('conditions' => array('ProductsPrices.product_id' => $id)));

        //$users = $this->Product->User->find('list');
        $groups = $this->Product->Group->find('list');
        $statuses = $this->Product->Status->find('list',array('conditions' => array('object_type_id' => 1)));
        $this->set(compact('groups','statuses'));
        $packmaterials = $this->Product->find('all', array('conditions' => array('consumption' => true)));
        $packmaterialsarr[0] = 'Other';
        foreach($packmaterials as $packmaterial) {
            $packmaterialsarr[$packmaterial['Product']['id']] = $packmaterial['Product']['name'];
        }
        $categories = $this->Product->Category->find('list',array('order' => array('Category.id'),'conditions' => array('user_id' => $product['User']['id'])));
        $colors = $this->Product->Color->find('list',array('conditions' => array('user_id' => $product['User']['id'])));
        if(empty($colors)) {
            $colors = $this->Product->Color->find('list',array('conditions' => array('user_id' => $product['User']['id'])));
        }

        $sizes = $this->Product->Size->find('list',array('conditions' => array('user_id' => $product['User']['id'])));
        if(empty($sizes)) {
            $sizes = $this->Product->Size->find('list',array('conditions' => array('user_id' => $product['User']['id'])));
        }
        $suggestedsku = $this->generatesku();
        $bins = $this->Product->Bin->find('list',array('conditions' => array('Bin.user_id' => $product['User']['id'], 'Bin.status' => 1)));
        $warehouses = $this->Product->Issue->find('list',array('conditions' => array('user_id' => $product['User']['id'])));
        $uoms = array_combine($this->uoms, $this->uoms);
        $this->loadModel('Bin');
        $locations = $this->Bin->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $product['User']['id'])));
        $status = $this->Bin->status;

        $this->Field->recursive = 0;
        $fields = $this->Field->find('all', array('conditions' => array('Field.user_id' => $this->Auth->user('id')), 'contain' => array('FieldsValue' => array('id', 'value')) ));

        $this->FieldsData->recursive = -1;
        $field_values = $this->FieldsData->find('all', array('conditions' => array('FieldsData.object_id' => $id, 'FieldsData.object_type' => 1), 'contain' => false ));
        
        $custom = array('FieldsData' => array());
        foreach ($field_values as $val) {
            $custom['FieldsData'][$val['FieldsData']['field_id']] = $val['FieldsData']['value'];
        }
        $this->request->data['FieldsData'] = $custom['FieldsData'];

        $this->set(compact('packmaterialsarr','colors','sizes','suggestedsku','product','categories', 'bins', 'uoms', 'warehouses', 'status', 'locations', 'schannels', 'schannels_a', 'channel_prices', 'parts', 'parts_a', 'product_parts', 'fields'));
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->Product->id = $id;

        if (!$this->Product->exists($id)) {
            throw new NotFoundException(__('Invalid product'));
        }
        $this->Product->recursive = -1;
        $product = $this->Product->find('first', array(
            'conditions' => ['Product.id' => $id],
            'fields' => ['Product.user_id']
        ));
        
        $this->loadModel('OrdersLine');
        $ProdOrderLine1 = $this->OrdersLine->find('count', array('conditions' => array('OrdersLine.product_id' => $id, 'OrdersLine.type' => 1)));
        $ProdOrderLine2 = $this->OrdersLine->find('count', array('conditions' => array('OrdersLine.product_id' => $id, 'OrdersLine.type' => 2)));

        if($ProdOrderLine1 > 0 || $ProdOrderLine2 > 0) {
            $msg = 'Product has ';
            if($ProdOrderLine1 > 0) {
                $msg .= 'sales order'. (($ProdOrderLine2 == 0)?', ':' ');
            }
            if($ProdOrderLine2 > 0) {
                $msg .= (($ProdOrderLine1 > 0)?'& ':'') .'purchase orders, ';
            } 
            $msg .= 'please delete them first. Otherwise, you can block the products.';

            $this->Session->setFlash(__($msg), 'admin/danger');
            return $this->redirect(array('action' => 'index'));
        }
        
        $this->request->allowMethod('post', 'delete');
        if ($this->Product->delete()) {
            $this->loadModel('Inventory');
            $InventoryRecord = $this->Inventory->find('first', array('conditions' => array('Inventory.product_id' => $id)));
            if(!empty($InventoryRecord)) {
                if($this->Inventory->delete($InventoryRecord['Inventory']['id'])) {
                    $this->Session->setFlash(__('The Product has been deleted.'), 'admin/success', array());
                }
            }
            $this->Session->setFlash(__('The Product has been deleted.'), 'admin/success', array());
        } else {
            $this->Session->setFlash(__('The Product could not be saved. Please, try again.'), 'admin/danger', array());
        }
        return $this->redirect(array('action' => 'index'));
    }

    /**
     * delete_multiple method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete_multiple() {
        if($this->request->is('post')) {
            if(!empty($this->request->data['Product']['product_id'])) {
                $ids = explode(',', $this->request->data['Product']['product_id']);
                $total = count($ids);
                $success = 0;
                $warning = 0;
                $this->loadModel('OrdersLine');
                $this->loadModel('Inventory');
                foreach ($ids as $id) {
                    
                    $ProdOrderLine = $this->OrdersLine->find('count', array('conditions' => array('OrdersLine.product_id' => $id, 'OrdersLine.type' => [1,2])));
                    if($ProdOrderLine > 0) {
                        $warning++;
                    } else {
                        $this->request->allowMethod('post', 'delete');
                        $this->Product->clear();
                        $this->Product->id = $id;
                        if ($this->Product->delete()) {
                            $InventoryRecord = $this->Inventory->find('first', array('conditions' => array('Inventory.product_id' => $id)));
                            if(!empty($InventoryRecord)) {
                                $this->Inventory->delete($InventoryRecord['Inventory']['id']);
                            }
                            $success++;
                        } else {
                            $warning++;
                        }
                    }
                }
                $msg = [];
                $type = 'success';
                if($success > 0) {
                    $msg[] = $success .' product(s) has been deleted.';
                }
                if($warning > 0) {
                    $type = 'danger';
                    $msg[] = $warning .' product(s) has sales & purchase orders, please delete them first. Otherwise, you can block the products.';
                }
                $msg = implode('<br>', $msg);
                $this->Session->setFlash(__($msg), 'admin/'. $type);
            } else {
                $this->Session->setFlash(__('Please select products.'), 'admin/danger');
            }
            $this->redirect($this->referer());
        } else {
            throw new NotFoundException(__('Page not found'));
        }
    }

    /**
     * block_multiple method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function block_multiple($status) {
        if($this->request->is('post')) {
            if(!empty($this->request->data['Product']['product_id'])) {
                $ids = explode(',', $this->request->data['Product']['product_id']);
                $total = count($ids);
                
                $this->Product->updateAll(
                    array('Product.status_id' => $status),
                    array('Product.id' => $ids)
                );
                foreach ($ids as $id) {
                    $this->EventRegister->addEvent(1, $status, $this->Auth->user('id'), $id);
                }
                $this->Session->setFlash(__('Products status changed succesfully.'), 'admin/success', array());
            } else {
                $this->Session->setFlash(__('Please select products.'), 'admin/danger');
            }
            $this->redirect($this->referer());
        } else {
            throw new NotFoundException(__('Page not found'));
        }
        
    }

    public function undelete($id = null) {
        $this->Product->id = $id;

        if ($this->Product->undelete($id)) {
            $this->Session->setFlash(__('The Product has been un-deleted.'), 'admin/success', array());
        } else {
            $this->Session->setFlash(__('The Product could not be undeleted. Please, try again.'), 'admin/danger', array());
        }
        return $this->redirect(array('action' => 'index'));
    }


    public function find() {
        $products = $this->paginate();
        if ($this->request->is('requested')) {
            return $products;
        } else {
            $this->set('products', $products);
        }
        $this->Prg->commonProcess();
        $this->Paginator->settings['conditions'] = $this->Product->parseCriteria($this->Prg->parsedParams());
        $this->set('products', $this->Paginator->paginate());
    }

    public function events($id = null) {
        $this->loadModel('Event');
        $product_events = $this->Event->find('all', array('conditions' => array('object_id' => $id)));

        $conditions = array($type = null);
        if ($this->Auth->user('id')) {
          $conditions['Event.user_id'] = $this->Auth->user('id');
        }
        if ( $this->request->params['pass'][0])
        {
           $conditions['Event.object_type_id'] =  1;
           $conditions['Event.object_id'] =  $id;
        }

        $this->set('product_events',$product_events);
        $options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id));
            $this->set('product', $this->Product->find('first', $options));
    }


    public function generatesku()
    {
        //A default, just in case
        $newsku = 111111;
        //We need to generate a random sku number that does not already exist
        $exists = true;
        $newsku = mt_rand(100000,999999);
        do {
            $product = $this->Product->findBysku($newsku);
            if(empty($product))
                $exists = false;
        } while($exists);
        return $newsku;
    }

    public function changestatus($id = null, $status_id = null)
    {
        $this->Product->updateAll(
            array('Product.status_id' => $status_id),
            array('Product.id' => $id));
        $this->EventRegister->addEvent(1,$status_id,$this->Auth->user('id'),$id);
        $this->Session->setFlash(__('Product status changed succesfully.'), 'admin/success', array());
        return $this->redirect(array('action' => 'index'));
    }



    public function togglepublishstatus($id = null, $publish_status = null)
    {
        $this->Product->updateAll(
                    array('Product.publish' => !$publish_status),
                    array('Product.id' => $id));
        $success_text = ($publish_status == 0 ? "Product published to marketplace" : "Product unpublished from marketplace"); // returns true
        $this->Session->setFlash(__($success_text), 'admin/success', array());
        return $this->redirect(array('action' => 'index'));
    }

    function pdf_view() {
        $this->Product->recursive = -1;
        $products = $this->Product->find('all', array(
            'conditions' => array('Product.user_id' => $this->Auth->user('id'), 'Product.deleted' => 0, 'Product.status_id NOT IN'=>[12, 13]),
            'fields' => array('Product.name', 'Product.imageurl', 'Product.sku', 'Product.value'),
            'limit' => 501
        ));

        $this->loadModel('Currency');
        $this->Currency->recursive = -1;
        $currency = $this->Currency->find('first', array('conditions' => array('id' => $this->Auth->user('id'))));

        $this->set(compact('products', 'currency'));
    }

    public function exportcsv() {

        $products = $this->Product->find('all',array('conditions' => array('Product.user_id' => $this->Auth->user('id'))));

        $_serialize = 'products';
        $_header = array('serial_no','name','price', 'description', 'uom',
            'group', 'category','sku','safety_stock',
            'reorder_point','Sales_Forecast','Lead_Time','Weight', 'width', 'height','depth',
            'barcode','barcode_standards_id','packaging_instructions',
            'bin','imageurl','pageurl','size_id','color_id');
        /*$this->loadModel('Field');
        $custom_fields = $this->Field->find('all', array('conditions' => array('Field.user_id' => $this->Auth->user('id'))));
        foreach ($products as $prod) {
            
        }*/
        $_extract = array('Product.id','Product.name','Product.value', 'Product.description','Product.uom',
            'Group.name','Category.name','Product.sku','Product.safety_stock',
            'Product.reorder_point','Product.sales_forecast','Product.lead_time','Product.weight','Product.width','Product.height','Product.depth',
            'Product.barcode', 'Product.barcode_standards_id','Product.packaging_instructions',
            'Product.bin','Product.imageurl','Product.pageurl','Product.color_id','Product.size_id');

        $file_name = "Delivrd_".date('Y-m-d-His')."_products.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('products', '_serialize', '_header', '_extract'));
    }

    public function export_stock() {

        $fields = [
            'Product.sku',
            'Product.name',
            'Product.value',
            'SUM(if(Warehouse.status = "active", Inventory.quantity, 0)) as quantity',
        ];
        $this->Product->recursive = -1;
        $products = $this->Product->find('all',array(
            'conditions' => array('Product.user_id' => $this->Auth->user('id')),
            'joins' => array(
                array('table' => 'inventories',
                    'alias' => 'Inventory',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Inventory.product_id = Product.id',
                        'Inventory.deleted = 0'
                    )
                ),
                array('table' => 'warehouses',
                    'alias' => 'Warehouse',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Inventory.warehouse_id = Warehouse.id'
                    )
                )
            ),
            'fields' => $fields,
            'group' => 'Product.id'
        ));

        $_serialize = 'products';
        $_header = array('SKU','Product Name', 'Price', 'Total Inventory');
        $_extract = array('Product.sku','Product.name','Product.value', '0.quantity');

        $file_name = "Delivrd".date('Y-m-d-His')."_products_stock.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('products', '_serialize', '_header', '_extract'));
    }

    public function downloadsamplefile() {

        $filename = $target_path = WWW_ROOT."sampledata/Delivrd_sample_products_l.csv"; // of course find the exact filename....
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


    public function importcsv($filename = null,$productsarray = null) {
        ini_set("auto_detect_line_endings", "1");
        $this->layout = 'mtru';
        $options = array(
        // Refer to php.net fgetcsv for more information
        'length' => 0,
        'delimiter' => ',',
        'enclosure' => '"',
        'escape' => '\\',
        // Generates a Model.field headings row from the csv file
        'headers' => false,
        // If true, String $content is the data, not a path to the file
        'text' => false,
        );
        $newproducts = array();
        $newinventory = array();
        $currentfileskus = array();
        $currentfilepnames = array();

    if(isset($filename))
    {
        //For CSV import, we start with line 1, ignoring header
        $startkey = 1;
        $content = WWW_ROOT."uploads/".$filename;

        //first, get number of columns to see if this is a basic file or full file
       $numcols = 0;
       $file = fopen($content, "r");
       while ($line = fgetcsv($file))
          {
            $numcols = count($line);
                if($numcols != 22)
                {

                if($numcols != 10) {
                     $this->showimporterror("The CSV file should have 10 columns for a basic file or 22 columns for a full file, but line ".$line[0]." has ".$numcols." columns.",$file);
                }

                }
          }
        fclose($file);
        if($numcols == 10)
        {

            $filedata = $this->Csv->import($content, array('serial_no','Product.name', 'Product.description','Product.uom', 'Product.group','Product.category','Product.sku','Product.value','Product.safety_stock','Product.reorder_point'));
        }
        else if($numcols == 22){
            $filedata = $this->Csv->import($content, array('serial_no','name', 'description','uom', 'group','category','sku','value','safety_stock','reorder_point','weight','width','height','depth','barcode','barcode_standards_id','packaging_instructions','bin','imageurl','pageurl','size_id','color_id'));
        } else {
            $this->showimporterror("Some data fields are missing from import file. Basic import file should contain 10 fields and extended import file should contain 22 fields. It has ".$numcols." fields",$file);
        }
        $errorstr = ".";
        $numprods = count($filedata);
                $productsarray = $filedata;
    } else {
        $startkey = 0;
    }

        foreach ($productsarray as $key=>$productdata)
        {
            //First line of CSV file is header so we don't import it
            if($key >= $startkey)
            {
            //Debugger::dump($productdata);
            // if(isset($filename) && !is_numeric($productdata['Importfile']['LineNumber']))
            // {
            //   $this->showimporterror("Line number is missing",$file);
            //  // $this->Session->setFlash(__('Could not create products. Please, try again.'),'default',array('class'=>'alert alert-danger'));
            //  //return $this->redirect(array('controller' => 'products', 'action' => 'uploadcsv'));

            // }
            //  $nextrefnum = ($key == ($numords-1) ? 1 : $filedata[$key+1]['Order']['external_orderid']);
            //      echo "num is ".$numords." current ext is ".$orderdata['Order']['external_orderid']." next ext is ".$nextrefnum."<BR>";
                //  if ($orderdata['Order']['external_orderid'] != $nextrefnum)
            //  {

            //if(empty($productdata['Product']['name'] || empty($productdata['Product']['description'])))
            //{
            //  $this->errorstr = "Line no. ".$productdata['Importfile']['LineNumber']. " has product name or description missing";

            //}

            //We also need to check if same SKU exists in a previous row in our import file
            /* if(in_array($productdata['Product']['name'],$currentfilepnames))
                {
                   $this->errorstr = "Product name ".$productdata['Product']['name']." in line no. ".$productdata['Importfile']['LineNumber']." already exists in another product in this import file. Product names need to be unique";
                } else {
                   array_push($currentfilepnames,$productdata['Product']['name']);
                } */
            // This checks are now covered by standard validation
            //if(empty($productdata['Product']['value'])   )
            //{
            //  $this->errorstr = "Line no. ".$productdata['Importfile']['LineNumber']. " has some missing information.";

            //}

            //stript tabs from name and description

            $productdata['Product']['name'] = strip_tags($productdata['Product']['name']);
            $productdata['Product']['description'] = strip_tags($productdata['Product']['description']);
            if(empty($productdata['Product']['imageurl']))
            {
                $productdata['Product']['imageurl'] = Configure::read('Product.image_missing');
            }

            if(empty($productdata['Product']['sku']))
            {
                $this->showimporterror("Line no. ".$productdata['Product']['serial_no']. " has SKU missing",$file);
            } else {
                $skuexists = $this->Product->find('first',array('fields' => 'Product.sku','conditions' => array('Product.sku' =>$productdata['Product']['sku'], 'Product.user_id' => $this->Auth->user('id'))));
               
                if(!empty($skuexists))
                {
                    $this->showimporterror("SKU ".$productdata['Product']['sku']." in line no. ".$productdata['Product']['serial_no']." already exists in another product. SKU numbers need to be unique",$file);
                }
                //We also need to check if same SKU exists in a previous row in our import file
                if(in_array($productdata['Product']['sku'],$currentfileskus))
                {
                    $this->showimporterror("SKU ".$productdata['Product']['sku']." in line no. ".$productdata['Product']['serial_no']." already exists in another product in this import file. SKU numbers need to be unique",$file);
                } else {
                   array_push($currentfileskus,$productdata['Product']['sku']);
                }

            }
            
            if(!empty($productdata['Product']['uom']))
            {
                $uomvalid = array_search($productdata['Product']['uom'],$this->uoms);
                if(!$uomvalid)
                {
                    $this->showimporterror("UOM ".$productdata['Product']['uom']." is not valid.",$file);
                }
            }
             
            //cyrrently variant condiguration is off
            /*  if(!empty($productdata['Product']['color_id']))
            {
            //  echo "color and size ".$productdata['Product']['color_id']."  ".$productdata['Product']['size_id'];
                $this->loadModel('Color');
                $color = $this->Color->find('first',array('conditions' => array('Color.name' => $productdata['Product']['color_id'])));
                if(!empty($color))
                {
                $productdata['Product']['color_id'] = $color['Color']['id'];
                } else {
                    $this->errorstr = "Color ".$productdata['Product']['color_id']." in line no. ".$productdata['Importfile']['LineNumber']." does not exist";
                    //$this->Session->setFlash(__('Could not create products. Please, try again.'),'default',array('class'=>'alert alert-danger'));
                }
            }

            if(!empty($productdata['Product']['size_id']))
            {
            //  echo "not emprt";
            //  echo "color and size ".$productdata['Product']['color_id']."  ".$productdata['Product']['size_id'];
                $this->loadModel('Size');
                $size = $this->Size->find('first',array('conditions' => array('Size.name' => $productdata['Product']['size_id'])));

                if(!empty($size))
                {
                $productdata['Product']['size_id'] = $size['Size']['id'];
                } else {
                    $this->errorstr = "Size ".$productdata['Product']['size_id']." in line no. ".$productdata['Importfile']['LineNumber']." does not exist";
                    //$this->Session->setFlash(__('Could not create products. %s',$this->errorstr),'default',array('class'=>'alert alert-danger'));
                }
            }
            */

            if(!empty($productdata['Product']['group']))
            {
                $this->loadModel('Group');
                $group = $this->Group->find('first',array('conditions' => array('Group.name' => $productdata['Product']['group'])));

                if(!empty($group))
                {
                    $productdata['Product']['group_id'] = $group['Group']['id'];
                } else {
                    $productdata['Product']['group_id'] = '99';
                }
                //      $this->errorstr = "Group ".$productdata['Product']['group']." in line no. ".$productdata['Importfile']['LineNumber']." does not exist";
                    //$this->Session->setFlash(__('Could not create products, %s',$errorstr),'default',array('class'=>'alert alert-danger'));

            }
            //  } else {

            //      $this->errorstr = "Line no. ".$productdata['Importfile']['LineNumber']. " product group ".$productdata['Product']['group']." is missing.";
            //      $this->Session->setFlash(__('Could not create products, %s',$errorstr),'default',array('class'=>'alert alert-danger'));

            //  }

            if(!empty($productdata['Product']['category']))
            {
                $this->loadModel('Category');
                $category = $this->Category->find('first',array('conditions' => array('Category.name' => $productdata['Product']['category'],'Category.user_id' => $this->Auth->user('id'))));

                if(!empty($category))
                {
                $productdata['Product']['category_id'] = $category['Category']['id'];
                } else {
                   $productdata['Product']['category_id'] = '';
                   // $this->showimporterror("Category ".$productdata['Product']['category']." in line no. ".$productdata['Importfile']['LineNumber']." does not exist",$file);
                    //      $this->errorstr = "Category ".$productdata['Product']['category']." in line no. ".$productdata['Importfile']['LineNumber']." does not exist";
                    //      $this->Session->setFlash(__('Could not create products, %s',$errorstr),'default',array('class'=>'alert alert-danger'));
                }
                //  } else {

                //      $this->errorstr = "Line no. ".$productdata['Importfile']['LineNumber']. " product category ".$productdata['Product']['category']." is missing.";
                //      $this->Session->setFlash(__('Could not create products, %s',$errorstr),'default',array('class'=>'alert alert-danger'));

                //  }

                /*        if(!empty($productdata['Product']['UOM']))
                {
                //  echo "not emprt";
                //  echo "color and size ".$productdata['Product']['color_id']."  ".$productdata['Product']['size_id'];
                    $this->loadModel('Category');
                    $category = $this->Category->find('first',array('conditions' => array('Category.name' => $productdata['Product']['group_id'])));

                    if(!empty($category))
                    {
                    $productdata['Product']['category_id'] = $category['Category']['id'];
                    } else {
                        $this->errorstr = "Category ".$productdata['Product']['category_id']." in line no. ".$productdata['Importfile']['LineNumber']." does not exist";
                        //$this->Session->setFlash(__('Could not create products, %s',$errorstr),'default',array('class'=>'alert alert-danger'));
                        }
                    } else {

                    $this->errorstr = "Line no. ".$productdata['Importfile']['LineNumber']. " product UOM is missing.";
                    $this->Session->setFlash(__('Could not create products, %s',$errorstr),'default',array('class'=>'alert alert-danger'));

                }
                */
                // if($this->errorstr)
                // {
                // $this->showimporterror($this->errorstr);
                       // exit();
            }
           
            $skuexists = $this->Product->find('first',array('fields' => 'Product.sku','conditions' => array('Product.sku' =>$productdata['Product']['sku'], 'Product.user_id' => $this->Auth->user('id'))));

                if(sizeof($skuexists) == 0){
                    $this->Product->create();
                    $productdata['Product']['user_id'] = $this->Auth->user('id');
                    $productdata['Product']['dcop_user_id'] = $this->Auth->user('id');
                    $productdata['Product']['status_id'] = 1;
                    $productdata['Product']['deleted'] = 0;
                    array_push($newproducts,$productdata);
                }
            }
        }
        //No products to add
        if(sizeof($newproducts) == 0)
        {
            $this->Session->setFlash(__('There were no new products to import from Magento.'), 'admin/danger', array());
            return $this->redirect(array('controller' => 'products', 'action' => 'index'));
        }
        if($this->Product->saveAll($newproducts)) {
            $product_ids=$this->Product->inserted_ids; //contain insert_ids
        } 
        // else{
        //     pr($this->Product->validationErrors);die;
        // }

        if(!isset($product_ids))
        {
            if(!isset($this->errorstr))
                $this->errorstr_val = "";
                $this->errorstr = "Please check your import file";
                    if($this->Product->validationErrors)
                         $validation_errors = $this->Product->validationErrors; 
                         $this->errorstr_val = "";
                        foreach ($validation_errors as $key => $value)
                        {
                            foreach ($value as $key2 => $value2)
                            {
                                $this->errorstr_val .= 'Line '.($key+1).' - '.$value2[0]."<BR />";
                                $this->showimporterror($this->errorstr_val, $file);
                            }
                        }
                        $this->set(compact('validation_errors'));
            $this->Session->setFlash(__('Could not create products.<BR /> %s',$this->errorstr_val), 'admin/danger', array());
        } else {
            foreach ($product_ids as $pid) {
                $this->createinventoryrecord($pid);
            }
            $products_created_count = sizeof($product_ids);
            $success_str = $products_created_count.' products were created successfully.';
            $this->Session->setFlash(__($success_str), 'admin/success', array());
            return $this->redirect(array('controller' => 'products', 'action' => 'index'));
        }



    }

    public function uploadcsv() {
        $this->layout = 'mtru';
        if ($this->request->is('post')) {
                //generate file uniqeness string
            $rand = rand();
            $now = time();
            $key = $now * $rand;
            $pre = md5($key);
            $target_path = WWW_ROOT."uploads/";
            $target_path = $target_path . $pre ."-".basename( $_FILES['uploadedfile']['name']);

            if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {

                $filesg = array( 'files' => array(array(
                                "orgname" => $_FILES['uploadedfile']['name'],
                    "name" => $pre."-".$_FILES['uploadedfile']['name'],
                    "size" => $_FILES['uploadedfile']['size'],
                    "thumbnailUrl" => "/theme/Mtro/assets/admin/layout/img/csv.png"
                )));
                header('Content-Type: application/json');
                echo json_encode($filesg,JSON_PRETTY_PRINT);
                exit();
            } else{
                $filesg = array( 'files' => array(array(
                                "orgname" => $_FILES['uploadedfile']['name'],
                    "name" => $pre."-".$_FILES['uploadedfile']['name'],
                    "size" => $_FILES['uploadedfile']['size'],
                    "error" => "Could not upload file. Please try again",
                )));
                header('Content-Type: application/json');
                echo json_encode($filesg,JSON_PRETTY_PRINT);
                exit();
            }
        }
    }

    public function returnjsonerror($text = null,$filename,$filesize)
    {
        $filese = array( 'files' => array(array(
                    "name" => $filename,
                    "size" => $filesize,
                    "error" => $text,
                )));
                header('Content-Type: application/json');
                echo json_encode($filese,JSON_PRETTY_PRINT);
                exit();
    }

    public function returnerror($text = null)
    {
        $this->Session->setFlash(__('Could not create products. Please, try again.'), 'admin/danger', array());
        return $this->redirect(array('controller' => 'products', 'action' => 'uploadcsv'));
    }

    public function createinventoryrecord($pid = null, $qty = null, $warehouse_id = null)
	{
		$this->loadModel('Inventory');
		$sentqty = 0;
		$recqty = 0;
		$qty = (!empty($qty) ? $qty : 0);
        $location = (!empty($warehouse_id) ? $warehouse_id : $this->Session->read('default_warehouse'));
        $result = $this->Inventory->find('first', array('conditions' => array('product_id' => $pid, 'warehouse_id' => $location)));
        if(empty($result)) {
            $this->Inventory->create();
            $this->Inventory->set('user_id',$this->Auth->user('id'));
            $this->Inventory->set('dcop_user_id',$this->Auth->user('id'));
            $this->Inventory->set('product_id', $pid);
            $this->Inventory->set('quantity', $qty);
            $this->Inventory->set('warehouse_id', $location);
            if ($this->Inventory->save($this->request->data)) {
                if($qty != 0) {
                    $delta = 0 - $qty;

                    if($delta > 0)
                    {
                    $sentqty = abs($delta);
                    }
                    if($delta < 0)
                    {
                    $recqty = abs($delta);
                    }
                    $this->loadModel('OrdersLine');
                    $data = array(
                    'OrdersLine' => array(
                        'order_id' => 4294967294,
                        'line_number' => 1,
                        'type' => 3,
                        'product_id'  => $pid,
                        'quantity' => $qty,
                        'receivedqty' => $recqty,
                        'damagedqty' => 0,
                        'sentqty' => $sentqty,
                        'foc' => '',
                        'warehouse_id' => $this->Session->read('default_warehouse'),
                        'return' => '',
                        'user_id' => $this->Auth->user('id')
                    )
                    );
                    $this->OrdersLine->create();
                    $this->OrdersLine->save($data);
                }

                return 0;
            } else {
                $this->Session->setFlash(__('The Product inventory record could not be saved. Please, try again.'), 'admin/danger', array());
            }
        }

	}

    function isValidUrl($url){
        // first do some quick sanity checks:
        if(!$url || !is_string($url)){
            return false;
        }
        // quick check url is roughly a valid http request: ( http://blah/... )
        if( ! preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url) ){
            return false;
        }
        // the next bit could be slow:
        if($this->getHttpResponseCode_using_curl($url) != 200){
        //  if(getHttpResponseCode_using_getheaders($url) != 200){  // use this one if you cant use curl
            return false;
        }
        return true;
    }

    function getHttpResponseCode_using_curl($url, $followredirects = true){
        // returns int responsecode, or false (if url does not exist or connection timeout occurs)
        // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
        // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
        // if $followredirects == true : return the LAST  known httpcode (when redirected)
        if(! $url || ! is_string($url)){
            return false;
        }
        $ch = @curl_init($url);
        if($ch === false){
            return false;
        }
        @curl_setopt($ch, CURLOPT_HEADER         ,true);    // we want headers
        @curl_setopt($ch, CURLOPT_NOBODY         ,true);    // dont need body
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);    // catch output (do NOT print!)
        if($followredirects){
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,true);
            @curl_setopt($ch, CURLOPT_MAXREDIRS      ,10);  // fairly random number, but could prevent unwanted endless redirects with followlocation=true
        }else{
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,false);
        }
        //  @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);   // fairly random number (seconds)... but could prevent waiting forever to get a result
        //  @curl_setopt($ch, CURLOPT_TIMEOUT        ,6);   // fairly random number (seconds)... but could prevent waiting forever to get a result
        //  @curl_setopt($ch, CURLOPT_USERAGENT      ,"Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1");   // pretend we're a regular browser
        @curl_exec($ch);
        if(@curl_errno($ch)){   // should be 0
            @curl_close($ch);
            return false;
        }
        $code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);
        return $code;
    }

    function getHttpResponseCode_using_getheaders($url, $followredirects = true){
        // returns string responsecode, or false if no responsecode found in headers (or url does not exist)
        // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
        // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
        // if $followredirects == true : return the LAST  known httpcode (when redirected)
        if(! $url || ! is_string($url)){
            return false;
        }
        $headers = @get_headers($url);
        if($headers && is_array($headers)){
            if($followredirects){
                // we want the the last errorcode, reverse array so we start at the end:
                $headers = array_reverse($headers);
            }
            foreach($headers as $hline){
                // search for things like "HTTP/1.1 200 OK" , "HTTP/1.0 200 OK" , "HTTP/1.1 301 PERMANENTLY MOVED" , "HTTP/1.1 400 Not Found" , etc.
                // note that the exact syntax/version/output differs, so there is some string magic involved here
                if(preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $hline, $matches) ){// "HTTP/*** ### ***"
                    $code = $matches[1];
                    return $code;
                }
            }
            // no HTTP/xxx found in headers:
            return false;
        }
        // no headers :
        return false;
    }
     public function addEvent($objecttypeid, $statusid,$userid,$objectid) {

        $this->loadModel('Event');
         $this->Event->create();
        $this->request->data('user_id',$userid);
        $this->request->data('object_type_id', $objecttypeid);
        $this->request->data('object_id', $objectid);
        $this->request->data('status_id', $statusid);
        //echo "from shipment we got $objecttypeid, $statusid,$userid,$objectid";
        if ($this->Event->save($this->request->data)) {
                echo "";
            } else {
                echo "";
            }

    }

     public function checkproductexist($id) {
            $this->Product->recursive = 1;
            $hasproduct = $this->Product->findById($id);
        if (!$hasproduct) {
            $this->Session->setFlash(__('Product does not exist.'), 'admin/danger', array());
            return $this->redirect(array('action' => 'index'));
        }

    }

    public function showimporterror($errstr = null, $file = null)
    {
        fclose($file);
        $gotoimport = "<a href='" . Router::url(array('plugin' => false,'controller' => 'products', 'action' => 'uploadcsv'), true) . "' class='btn blue-hoki fileinput-button'><i class='fa fa-cloud-upload'></i> Go to upload page</a>";
        $this->Session->setFlash(__('Products could not be created. %s  %s',$errstr, $gotoimport), 'admin/danger', array());
        return $this->redirect(array('controller' => 'products', 'action' => 'index'));
    }

    public function importmagento()
    {
        //site - change according to your credentials
        $base_url="http://myshoes.fastcomet.host/Magentos/";
        //API user
        $user="apiuser";
        //API key
        $password="81eRvINu9r";


        $api_url=$base_url.'index.php/api/soap/?wsdl';
        $client = new SoapClient($api_url);
        $session = $client->login($user,$password);

        $params = array(array(
            'status'=>array('eq'=>'enabled')
        ));

        $result1 = $client->call($session, 'catalog_product.list');

        $i=0;
        foreach($result1 as $key => $value)
        {
            $result2 = $client->call($session, 'catalog_product.info',$result1[$key]['product_id']);
            $result3 = $client->call($session, 'cataloginventory_stock_item.list',$result1[$key]['product_id']);
            $arr[$i]['Product']['product_id']=$result1[$key]['product_id'];
            $arr[$i]['Product']['name']=$result1[$key]['name'];
            $arr[$i]['Product']['description']=$result2['description'];
            $arr[$i]['Product']['uom']='';
            $arr[$i]['Product']['category']= '';
            $arr[$i]['Product']['group']='';
            $arr[$i]['Product']['sku']=$result1[$key]['sku'];
            $arr[$i]['Product']['value']= number_format((float)$result2['price'], 2, '.', '');
            $arr[$i]['Product']['reorderpoint']='';
            $arr[$i]['Product']['safetystock']='';
            $arr[$i]['Product']['bin']='';
            $arr[$i]['Product']['imageurl']='';
            $arr[$i]['Product']['pageurl']=$base_url.$result2['url_path'];
            $arr[$i]['Product']['weight']= number_format((float)$result2['weight'], 2, '.', '');
            $arr[$i]['Product']['height']='';
            $arr[$i]['Product']['width']='';
            $arr[$i]['Product']['depth']='';
            $arr[$i]['Product']['barcodesystem']='';
            $arr[$i]['Product']['barcode_number']='';
            $arr[$i]['Product']['packaginginstructions']='';
            $arr[$i]['Product']['color']='';
            $arr[$i]['Product']['size']='';
            $arr[$i]['Inventory']['inventoryquantity']=$result3[0]['qty'];
            $arr[$i]['Product']['createdinsource']=$result2['created_at'];
            $arr[$i]['Product']['modifiedinsource']=$result2['updated_at'];
            $i++;
        }
         $this->importcsv(null, $arr);
         return $this->redirect(array('controller' => 'products', 'action' => 'index'));
    }

    /**
     * Import products from Shopify
     *
     *
     */
    public function importshopify($id) {
        $this->autoRender = false;
        set_time_limit(0);

        $response = $this->Shopfy->importShopifyProducts($id);
        
        if ($this->request->is('ajax')) {
            $this->set('_serialize', 'response');
            $json = json_encode($response);
            $this->response->body($json);
        } else {
            $this->Session->setFlash(__('Total found: '. $response['found'] .'. Product(s) added: '. $response['added'] .'. Product(s) updated: '. $response['updated']), 'default', array('class' => 'alert alert-success'));
            return $this->redirect(array('controller' => 'orders', 'action' => 'importshopify', $id));
        }
    }

    /**
     * Import producs from WooCommerce
     *
     *
     */
    public function importwoocom($id) {
        $this->autoRender = false;
        set_time_limit(0);

        $response = $this->WooCommerce->importWooProducts($id);
        if ($this->request->is('ajax')) {
            $this->set('_serialize', 'response');
            $json = json_encode($response);
            $this->response->body($json);
        } else {
            $this->Session->setFlash(__('Total found: '. $response['found'] .'. Product(s) added: '. $response['added'] .'. Product(s) updated: '. $response['updated']), 'default', array('class' => 'alert alert-success'));
            return $this->redirect(array('controller' => 'orders', 'action' => 'importwoo', $id));
        }
    }

    /**
     * Generate Amazon Report
     *
     *
     */
    public function generateAmazonReport($id) {
        $this->autoRender = false;
        set_time_limit(0);

        $response = $this->Amazon->generateProudctReport($id);
        if($response['status'] == 'success') {
            $response = $this->Amazon->getReportList('_GET_MERCHANT_LISTINGS_DATA_');
        }

        if ($this->request->is('ajax')) {
            $this->set('_serialize', 'response');
            $json = json_encode($response);
            $this->response->body($json);
        } else {
            //$this->Session->setFlash(__('Total found: '. $response['found'] .'. Product(s) added: '. $response['added'] .'. Product(s) updated: '. $response['updated']), 'default', array('class' => 'alert alert-success'));
            //return $this->redirect(array('controller' => 'orders', 'action' => 'importwoo'));
            return $response;
        }
    }

    /**
     * Import Amazon Products
     *
     *
     */
    public function importAmazon($id, $reportId) {
        $this->autoRender = false;
        set_time_limit(0);

        $response = $this->Amazon->getReport($id, $reportId);

        if ($this->request->is('ajax')) {
            $this->set('_serialize', 'response');
            $json = json_encode($response);
            $this->response->body($json);
        } else {
            #$this->Session->setFlash(__('Total found: '. $response['found'] .'. Product(s) added: '. $response['added'] .'. Product(s) updated: '. $response['updated']), 'default', array('class' => 'alert alert-success'));
            #return $this->redirect(array('controller' => 'orders', 'action' => 'importwoo'));
            return $response;
        }
    }

    /*public function importecom() {
        $this->layout = 'mtrd';
    }*/

    public function get_list() {
        $keywords = $this->request->query['query'];
        $limit = $this->request->query['page_limit'];
        $conditions = array('Product.deleted' => 0, 'Product.user_id' => $this->Auth->user('id'),'Product.name like ' => "%" . $keywords . "%");
        $result = $this->{$this->modelClass}->find('all', array('conditions' => $conditions, 'fields' => array(
                'Product.id', 'Product.name', 'Product.sku'
            ), 'limit' => $limit));
        $listData = array();
        foreach ($result as $key => $data) {
            $listData[$key]['id'] = $data['Product']['name'];
            $listData[$key]['name'] = $data['Product']['name'];

        }
        $result = json_encode(array('data' => $listData));
        echo $result;
        exit;
    }

    public function get_sku_list() {
        $keywords = $this->request->query['query'];
        $limit = $this->request->query['page_limit'];
        $conditions = array(
            'Product.deleted' => 0,
            'Product.status_id NOT IN' => [12, 13],
            'Product.user_id' => $this->Auth->user('id'),
            'Product.sku like ' => "%" . $keywords . "%"
        );
        $result = $this->{$this->modelClass}->find('all', array('conditions' => $conditions, 'fields' => array(
                'Product.id', 'Product.name', 'Product.sku'
            ),  'group' => 'Product.sku','limit' => $limit));

        if(empty($result))
        {
            $conditions = array('Product.user_id' => $this->Auth->user('id'), 'Product.barcode like ' => "%" . $keywords . "%");
            $result = $this->{$this->modelClass}->find('all', array('conditions' => $conditions, 'fields' => array(
                'Product.id', 'Product.name', 'Product.barcode'
            ), 'limit' => $limit));

            $listData = array();
            foreach ($result as $key => $data) {
                 $listData[$key]['id'] = $data['Product']['barcode'];
                 $listData[$key]['sku'] = $data['Product']['barcode'];
            }

            $result = json_encode(array('data' => $listData));
            echo $result;
            exit;
        }


        $listData = array();
        foreach ($result as $key => $data) {
             $listData[$key]['id'] = $data['Product']['sku'];
             $listData[$key]['sku'] = $data['Product']['sku'];

        }
        $result = json_encode(array('data' => $listData));
        echo $result;
        exit;
    }

    public function get_auto_list() {
        $keywords = $this->request->query['key'];

        if($keywords) {
            $network_products = $this->Access->getProducts(false, false, false, ['OR' => [
                'Product.name like ' => "%" . $keywords . "%",
                'Product.sku like ' => "%" . $keywords . "%",
                'Product.barcode like ' => "%" . $keywords . "%"]]
            );

            $condition['Product.deleted'] = 0;
            $condition['Product.status_id NOT IN'] = [12, 13];
            $condition['OR'] = [
                'Product.user_id' => $this->Auth->user('id'),
                'Product.id' => array_keys($network_products),
            ];
            $condition[] = array('or' => array(
                'Product.name like ' => "%" . $keywords . "%",
                'Product.sku like ' => "%" . $keywords . "%",
                'Product.barcode like ' => "%" . $keywords . "%"
            ));

            $result = $this->{$this->modelClass}->find('all', array(
                'conditions' => $condition,
                'fields' => array('Product.id', 'Product.name', 'Product.sku'),
                'recursive' => 0,
                'callbacks' => false
            ));

            $json = [];
            foreach ($result as $key => $data) {
                $pr = ['label' => $data['Product']['name'], 'value' => $data['Product']['name'], 'product_id' => $data['Product']['id']];
                $json['name'][] = $pr;
            }

            if(preg_match('/[0-9]{2,}/', $keywords)) {
                $this->loadModel('Serial');
                $cond = array('Serial.serialnumber' => $keywords, 'Serial.user_id' => $this->Auth->user('id'));
                $serial = $this->Serial->find('first', array(
                    'conditions' => $cond,
                    'fields' => array('Serial.serialnumber', 'Serial.warehouse_id', 'Product.name', 'Product.id', 'Warehouse.name')
                ));
                if($serial) {
                    $this->Session->write('serial_no', $keywords);
                    $pr = [
                        'label' => $serial['Product']['name'] .' ('. $serial['Warehouse']['name'] .')',
                        'value' => $serial['Product']['name'] .' ('. $serial['Warehouse']['name'] .')',
                        'product_id' => $serial['Product']['id'],
                        'serialnumber' => $serial['Serial']['serialnumber'],
                        'warehouse_id' => $serial['Serial']['warehouse_id']
                    ];
                    $json['name'][] = $pr;
                }
            }
        } else {
            $this->loadModel('Activity');
            $result = $this->Activity->find('all', [
                'conditions' => ['Activity.user_id' => $this->Auth->user('id'), 'Product.user_id' => $this->Auth->user('id')],
                'joins' => [
                    array('table' => 'products',
                        'alias' => 'Product',
                        'type'  => 'INNER',
                        'conditions' => array(
                            'Activity.sku = Product.sku',
                            'Product.deleted = 0'
                        )
                    )
                ],
                'fields' => ['Product.id', 'Product.name', 'Product.sku'],
                'order' => ['Activity.activity' => 'DESC'],
                'limit' => 10

            ]);
            $json = [];
            foreach ($result as $key => $data) {
                $pr = ['label' => $data['Product']['name'], 'value' => $data['Product']['name'], 'product_id' => $data['Product']['id']];
                $json['name'][] = $pr;
            }
        }
        echo json_encode($json);
        exit;
    }


    public function get_cats($network_id = null) {

        $categories = $this->Access->networkCats($network_id);

        pr($categories);
        exit;
    }

    public function getproductPrice($id) {
        $response['price'] = '';
        if(!empty($id)) {
            $price = $this->Product->find('first', array('conditions' => array('Product.id' => $id), 'fields' => array('value'),'recursive' => -1));
             if(!empty($price))
                $response['price'] = $price['Product']['value'];

        }

        echo json_encode($response);
        exit;
    }

    public function add_products_csv(){
        $matchColumnDisplay = false;
        $show_trial = false;
        if($this->request->is('post'))
        {
            $uploadPath = WWW_ROOT.'uploads/';
            $fileName = $this->request->data['fileupload']['photo'];
            $target_file = WWW_ROOT.'uploads/'. basename($fileName["name"]);
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
            $html = '';
            $max_file_size = Configure::read('FileuploadSize') ;
            if($imageFileType != "csv") {
                $html .=  '<div class="alert alert-danger">only csv format is allowed</div>';
            } elseif($fileName["size"] > $max_file_size){
                $html .=  '<div class="alert alert-danger">file size is '.$fileName["size"] .' bytes that is too larger Please try other file</div>';
            } else {
                $auth = $this->Auth->user();
                $f = explode('.',$fileName['name']);
                $fname = $f[0].time().'.'.$imageFileType;
                $file = $uploadPath.$fname;
                if(move_uploaded_file($fileName['tmp_name'],$file)) {
                    $product_limit = $auth['paid'] == 1 ? Configure::read('Productlimit.paiduser') : Configure::read('Productlimit.freeuser');
                    $fp = file($uploadPath.$fname);
                    $csvrecords = count($fp)-1;
                    $rowcount = $this->Product->find('count', array('conditions' => array('Product.user_id'=>$this->Auth->user('id'))));
                    $productlimit = $product_limit - $rowcount;
                    if($csvrecords < $productlimit){
                        //  to insert new record in transfer
                        $this->loadModel('Transfer');
                        $this->Transfer->create();
                        $this->request->data('Transfer.user_id',$this->Auth->user('id'));
                        $this->request->data('Transfer.type',1);
                        $this->request->data('Transfer.direction',1);
                        $this->request->data('Transfer.source',1);
                        $this->request->data('Transfer.status',1);
                        $this->request->data('Transfer.recordscount',0);
                        if ($res = $this->Transfer->save($this->request->data))
                        {
                            $transfer_id = $res['Transfer']['id'];
                            $matchColumnDisplay = true;
                            $file = fopen($file,"r");
                            $headers = fgetcsv($file);
                            //echo "<pre>"; print_r($headers); die;
                            if(count($headers) < 2){
                                $html .=  '<div class="alert alert-danger">You are uploading the blank CSV.</div>';
                                $matchColumnDisplay = false;
                            } else {
                                $headerCols = array();
                                $q = 0;
                                foreach($headers as $header){
                                    $headerCols[$q] = $header;
                                    $q++;
                                }

                                $this->loadModel('Fields_value');
                                $Columns = $this->Fields_value->find('all',array('conditions'=>array('field_for'=>1)));
                                $fields = array();
                                foreach($Columns as $key =>$Column)
                                {
                                    $fields [$Column['Fields_value']['id']]['is_mandatory'] = $Column['Fields_value']['is_mandatory'];
                                    $fields [$Column['Fields_value']['id']]['database_value'] = $Column['Fields_value']['database_value'];
                                    $fields [$Column['Fields_value']['id']]['display_value'] = $Column['Fields_value']['display_value'];
                                }
                                                                
                                $this->set('file', $uploadPath.$fname);
                                $this->set('transfer_id', $transfer_id);
                                $this->set('headerCols', $headerCols);
                                $this->set('fields', $fields);
                                $this->set('matchColumnDisplay', $matchColumnDisplay);
                                $this->layout = 'mtrd';
                                $this->set('fields', $fields);
                            }
                        }
                        else
                        {
                            $html .=  '<div class="alert alert-danger">â€˜Could not upload file. Please try againâ€™</div>';
                        }
                    }else{
                        $html .=  '<div class="alert alert-danger">You have already uploaded '.$rowcount .' products. The maximum limit for your account is '.$product_limit.'</div>';
                        $show_trial = true; 
                    }
                }
                else{
                    $html .=  '<div class="alert alert-danger">Error while uploading file. Please try again.</div>';
                }
            }
            $this->set('html', $html);
        }
        $this->set('show_trial', $show_trial);
        $this->set('matchColumnDisplay', $matchColumnDisplay);
        $this->layout = 'mtrd';
        $this->render('add_products_csv');
    }

    public function validate_csv() {
        $csvcol = array_filter($_POST['csvcol']);
        $dbcol = array_filter($_POST['dbcol']);
        $completeArr = [];
        for($i=0; $i <= count($csvcol); $i++) {
            if(isset($dbcol[$i]) && isset($csvcol[$i])) {
                $completeArr[$csvcol[$i]] = $dbcol[$i];
            }
        }

        $filename = $_POST['file_name'];
        $file = fopen($filename,"r");
        $title = fgetcsv($file);
        $data = fgetcsv($file, 1000, ",");
        $header = array();
        $values = array();
        $sizes = array();
        $file = fopen($filename,"r");
        $fp = file($filename, FILE_SKIP_EMPTY_LINES);
        $title = fgetcsv($file);
        $data = fgetcsv($file, 1000, ",");
        $row = 1;
        if (($handle = fopen($filename, "r")) !== false) {
            for($t=0; $t < count($title); $t++) {
                $header[$t] = $title[$t];
            }
            while (($data = fgetcsv($handle, 1000, ",")) !== false) { 
                $num = count($data);
                $values[$row] = $data; 
                $row++;
            } 
            fclose($handle);
        }

        $indexarr = [];
        $indexarr = $values[1];
        $j = 1; 
        for($t=2; $t <= count($values); $t++) {
            $index = [];
            $index = $values[$t];
            for($i=0; $i < count($index); $i++) {
                $valarray[$j][$indexarr[$i]] = $index[$i];
            }
            $j++;
        }

        $products = [];
        for($q=1; $q<=count($fp)-1;$q++) {
            $r=1;
            foreach($completeArr as $c => $key) {
                $products[$q][$key] = $valarray[$q][$c];
                $r++;
            }
        }

        $this->loadModel('Category');
        $categories = $this->Category->find('all',array('conditions' => array('Category.user_id' => $this->Auth->user('id'))));
        $cat = array();
        foreach($categories as $cate) {
            $cat[$cate['Category']['id']] = $cate['Category']['name'];
        }
        
        $this->loadModel('Colors');
        $Colors = $this->Colors->find('all',array('conditions' =>array('Colors.user_id' => $this->Auth->user('id'))));
        $col = array();
        foreach($Colors as $Color) {
            $col[$Color['Colors']['id']] = $Color['Colors']['name'];
        }
        
        $this->loadModel('Size');
        $Size = $this->Size->find('all',array('conditions' =>array('Size.user_id' => $this->Auth->user('id'))));

        if(count($Size)){
            foreach($Size as $si){
                $sizes[$si['Size']['id']] = $si['Size']['name'];
            }
        }
        
        $catarray = array();
        $colorarray = array();
        $sizearray = array();
        foreach($products as $product){
            foreach($product as $key => $p){
                if($key == 'category_id') {
                    if (!in_array($p, $cat)) {
                        array_push($catarray,$p);
                    }
                }
                if($key == 'color_id'){
                    if (!in_array($p, $col)) {
                        array_push($colorarray,$p);
                    }
                }
                if($key == 'size_id'){
                    if (!in_array($p, $sizes)) {
                        array_push($sizearray,$p);
                    }
                }
            }
        }

        // we need only unique category names, colors and sizes
        $catarray = array_unique($catarray);
        $colorarray = array_unique($colorarray);
        $colorarray = array_values($colorarray);
        $sizearray = array_unique($sizearray);

        $productView ='<table border="2" width="100%">';
        $productView .='<tr><th>Product Name</th><th>Status</th></tr>';
        $is_update = false;
        $is_create = false;
        foreach($products as $key => $p) {
            $a = array();
            $a['Product'] = $p;
            $data = $this->Product->set($a);
            $productView .= '<tr><td>';
            $productView .= (isset($a['Product']['name'])) ? $a['Product']['name'] : $key-1;
            $productView .= '</td><td>';
            if ($this->Product->validates()) {
                $productView .= '<span class="text-success"><i class="fa fa-check"></i> Validated succesfully</span>';
                $is_create = true;
            } else {
                $productView .= '';
                $errors = $this->Product->validationErrors;
                $class = 'warning';
                $productViewT = '';
                foreach($errors as $e){
                    if(preg_match('/SKU [a-z0-9]+ already exists/i', $e[0]) || preg_match('/Product name (.?)+ already exists/i', $e[0])) {
                        $productViewT .= '<i class="fa fa-exclamation-circle"></i> '. $e[0].'<br>';
                    } else {
                        $class = 'danger';
                        $productViewT .= '<i class="fa fa-exclamation-circle"></i> '. $e[0].'<br>';
                    }
                }
                $success = 0;
                $productView .= '<span class="text-'. $class .'">'. $productViewT. (($class == 'warning')?' Product will be updated.':'') .'</span>';
                if($class == 'warning') {
                    $is_update = true;
                }
            }
            $productView .= '</td></tr>';
        }
        $productView .= '</table>';
        $newfields = array();
        $newfields['catarray'] = $catarray;
        $newfields['colorarray'] = $colorarray;
        $newfields['sizearray'] = $sizearray;
        
        $response['success'] = 0;
        $response['newfields'] = $newfields;
        $response['products'] = $productView;
        if($is_create && $is_update) {
            $response['btn_title'] = 'Create &amp; Update Products';
        } else if($is_create) {
            $response['btn_title'] = 'Create Products';
        } else {
            $response['btn_title'] = 'Update Products';
        }
        echo json_encode($response);
        exit;
    }

    public function add_product() {
        if($this->request->is('post')) {
            #$completeArr = array_combine(array_filter($_POST['csvcol'], 'strlen'),array_filter($_POST['dbcol']));
            $csvcol = array_filter($_POST['csvcol']);
            $dbcol = array_filter($_POST['dbcol']);
            $completeArr = [];
            for($i=0; $i <= count($csvcol); $i++) {
                if(isset($dbcol[$i]) && isset($csvcol[$i])) {
                    $completeArr[$csvcol[$i]] = $dbcol[$i];
                }
            }

            $filename = $_POST['file_name'];
            $this->layout = 'mtrd';
            $file = fopen($filename,"r");
            $title = fgetcsv($file);
            $data = fgetcsv($file, 1000, ",");
            $header = array();
            $values = array();
            $csvdata = array();
            $file = fopen($filename,"r");
            $fp = file($filename, FILE_SKIP_EMPTY_LINES);
            $title = fgetcsv($file);
            $data = fgetcsv($file, 1000, ",");
            $tdata = fgetcsv($file, 1000, ",");
            $row = 1;
            if (($handle = fopen($filename, "r")) !== FALSE) {
                for($t=0; $t< count($title); $t++) {
                    $header[$t] = $title[$t];
                }
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    $values[$row] = $data;
                    $row++;
                }
                fclose($handle);
            }

            $indexarr = [];
            $indexarr = $values[1];
            $j = 1; 
            for($t=2; $t <= count($values); $t++) {
                $index = [];
                $index = $values[$t];
                for($i=0; $i < count($index); $i++) {
                    $valarray[$j][$indexarr[$i]] = $index[$i];
                }
                $j++;
            }
     
            for($q=1; $q<=count($fp)-1;$q++) {
                $r=1;
                foreach($completeArr as $c => $key) {
                    $product[$q][$key] = $valarray[$q][$c];
                    if($key == 'category_id') {
                        $this->loadModel('Category');
                        $category = $this->Category->find('first',array('conditions' => array('Category.name' => $valarray[$q][$c],'Category.user_id' => $this->Auth->user('id'))));

                        if(!empty($category)) {
                            $product[$q]['category_id'] = $category['Category']['id'];
                        }
                    }
                    if($key == 'group_id') {
                        $this->loadModel('Group');
                        $group = $this->Group->find('first',array('conditions' => array('Group.name' => $valarray[$q][$c])));

                        if(!empty($group)) {
                            $product[$q]['group_id'] = $group['Group']['id'];
                        }
                    }
                    if($key == 'size_id') {
                        $this->loadModel('Size');
                        $size = $this->Size->find('first',array('conditions' => array('Size.name' => $valarray[$q][$c],'Size.user_id' => $this->Auth->user('id'))));

                        if(!empty($size)) {
                            $product[$q]['size_id'] = $size['Size']['id'];
                        }
                    }
                    if($key == 'color_id') {
                        $this->loadModel('Color');
                        $color = $this->Color->find('first',array('conditions' => array('Color.name' => $valarray[$q][$c],'Color.user_id' => $this->Auth->user('id'))));

                        if(!empty($color)) {
                            $product[$q]['color_id'] = $color['Color']['id'];
                        }
                    }

                    $product[$q]['user_id'] = $this->Auth->user('id');
                    $r++;
                }
            }


            $this->loadModel('Category');
            $categories = $this->Category->find('all',array('conditions' =>array('Category.user_id' => $this->Auth->user('id'))));
            $cat = array();
            foreach($categories as $cate)
            $cat[$cate['Category']['id']] = $cate['Category']['name'];
            
            $this->loadModel('Group');
            $Groups = $this->Group->find('all');
            $groups = array();
            foreach($Groups as $gro)
            $groups[$gro['Group']['id']] = $gro['Group']['name'];
            
            $this->loadModel('Colors');
            $Colors = $this->Colors->find('all',array('conditions' =>array('Colors.user_id' => $this->Auth->user('id'))));
            $col = array();
            foreach($Colors as $Color)
            $col[$Color['Colors']['id']] = $Color['Colors']['name'];

            
            $this->loadModel('Size');
            $Size = $this->Size->find('all',array('conditions' =>array('Size.user_id' => $this->Auth->user('id'))));
            if(count($Size)){
                foreach($Size as $si)
                $sizes[$si['Size']['id']] = $si['Size']['name'];
            }
            
            $this->set('colours', $col);
            
            if(count($Size)){
                $this->set('size', $sizes);
            }

            $this->set('group', $groups);
            $this->set('category', $cat);
            $this->set('transfer_id', $_POST['transfer_id']);
            $this->set('products', $product);
            $this->render('response');
        } else {
            $this->layout = 'mtrd';
            $this->set('matchColumnDisplay', false);
            $this->render('add_products_csv');
        }
    }

    public function create_new_fields(){
        if(ISSET($_POST['category_name'])){
            $categories = $_POST['category_name'];
            $this->loadModel('Category');
            $catView ='<table border="2" width="100%">';
            $catView .='<tr><th>Category Name</th><th>Status</th></tr>';
            foreach($categories as $key => $category){
                $this->Category->create();
                $this->request->data('Category.name', $category);
                $this->request->data('Category.description', $category);
                $this->request->data('Category.user_id',$this->Auth->user('id'));
                $catView .= '<tr><td>';
                $catView .= $category;
                $catView .= '</td><td>';
                if ($res = $this->Category->save($this->request->data)) {
                    $catView .= 'Validated succesfully';
                    $catSuccess = true;
                }else{
                    $errors = $this->Category->validationErrors;
                    foreach($errors as $e){
                        $catView .= $e[0].'<br>';
                    }
                    $catSuccess = false;
                } 
                $catView .= '</td></tr>'; 
            }
            $catView .= '</table>';
            $response['catView'] = $catView;
            $response['catTable'] = 'Category Validation Status';
            $response['catSuccess'] = $catSuccess;
        }
        if(isset($_POST['colors_name'])){
            $colors = $_POST['colors_name'];
            $this->loadModel('Color');
            $colorView ='<table border="2" width="100%">';
            $colorView .='<tr><th>Size Name</th><th>Status</th></tr>';
            foreach($colors as $key => $color){
                $this->Color->create();
                $this->request->data('Color.user_id',$this->Auth->user('id'));
                $this->request->data('Color.name',$color);
                $colorView .= '<tr><td>';
                $colorView .= $color;
                $colorView .= '</td><td>';
                if ($res = $this->Color->save($this->request->data)) {
                    $colorView .= 'Validated succesfully';
                    $colorSuccess = true;
                }else{
                    $errors = $this->Color->validationErrors;
                    foreach($errors as $e){
                        $colorView .= $e[0].'<br>';
                    }
                    $colorSuccess = false;
                }
                $colorView .= '</td></tr>'; 
            }
            $colorView .= '</table>';
            $response['colorView'] = $colorView;
            $response['cTable'] = 'Color Validation Status';
            $response['cSuccess'] = $colorSuccess;
        }
        
        if(isset($_POST['size'])){
            $sizes = $_POST['size'];
            $this->loadModel('Size');
            $sizeView ='<table border="2" width="100%">';
            $sizeView .='<tr><th>Size Name</th><th>Status</th></tr>';
            foreach($sizes as $key => $size){
                $this->Size->create();
                $this->request->data('Size.user_id',$this->Auth->user('id'));
                $this->request->data('Size.name',$size);
                $sizeView .= '<tr><td>';
                $sizeView .= $size;
                $sizeView .= '</td><td>';
                if ($res = $this->Size->save($this->request->data)) {
                    $sizeView .= 'Validated succesfully';
                    $sizeSuccess = true;
                }else {
                    $errors = $this->Size->validationErrors;
                    foreach($errors as $e){
                        $sizeView .= $e[0].'<br>';
                    }
                    $sizeSuccess = false;
                }
                $sizeView .= '</td></tr>';
            }
            $sizeView .= '</table>';
            $response['sizeView'] = $sizeView;
            $response['sTable'] = 'Size Validation Status';
            $response['sSuccess'] = $sizeSuccess;
        }
        echo json_encode($response);
        exit;
    }

    public function add_csv_products(){
        $response= array();
        $product ='';
        //echo "<pre>"; print_r($this->request->data); die;
        if(isset($_POST['sku'])){
            $this->Product->recursive = -1;
            $product= $this->Product->find('first',array('conditions' => array('Product.sku'=>$_POST['sku'], 'Product.user_id'=>$this->Auth->user('id')) ));
        }
        if(!$product){
            $quantity = isset($_POST['quantity']) ? $_POST['quantity']: '0' ;
            
            $this->Product->create();
            $data = array();
            $this->request->data('user_id',$this->Auth->user('id'));
            $this->request->data('deleted',0);
            $this->request->data('status_id',1);
        
            foreach($_POST as $c =>$key) {
                $this->request->data($c,$key);
            }
            if(!ISSET($_POST['imageurl']) || $_POST['imageurl'] == '') {
                $this->request->data('imageurl', Configure::read('Product.image_missing'));
            }
            
            if (!$res = $this->Product->save($this->request->data)) {
                $response['success'] = 0;
                $response['status'] = 'Failed';
                $errors = $this->Product->validationErrors;
                foreach($errors as $e){
                    $response['message'] = $_POST['name'] .' - '. ($e[0]);
                }
            } else {

                // Add Inventory
                if(isset($this->request->data['quantity']) && $this->request->data['quantity'] > 0) {
                    $this->createinventoryrecord($res['Product']['id'],$this->request->data['quantity']);
                }

                $response['invent'] = $res['Product']['id'];
                $response['success'] = 1;
                $response['status'] = 'Success';
                $response['message']  = $_POST['name'].' - Product Created Successfully';
                $this->createinventoryrecord($res['Product']['id'],$quantity);
            }
        
        } else {
            if($product['Product']['deleted'] == 1) {
                $response['success'] = 2;
                $response['status'] = 'You already have this product, but it was deleted.';
                $response['product_id'] = $product['Product']['id'];
                $response['html']  = '<td>'.$product['Product']['name'].'</td>
                <td>'.$product['Product']['sku'].'</td>
                <td>Already Exists, have status deleted</td></tr>';
            } elseif($product['Product']['status_id'] == 13 || $product['Product']['status_id'] == 12) {
                $response['success'] = 2;
                $response['status'] = 'You already have this product, but it was blocked.';
                $response['product_id'] = $product['Product']['id'];
                $response['html']  = '<td>'.$product['Product']['name'].'</td>
                <td>'.$product['Product']['sku'].'</td>
                <td>Already Exists, have status blocked</td></tr>';
            } else {
                $response['success'] = 2;
                $response['status'] = 'already Added';
                $response['product_id'] = $product['Product']['id'];
                $response['html']  = '<td>'.$product['Product']['name'].'</td>
                <td>'.$product['Product']['sku'].'</td>
                <td>Already Exists</td></tr>';
            }
        }
        echo json_encode($response);
        die;
    }

    public function update_products()
    {
        $product_list = array();
        //$products = $_POST['product_details'];
        $products = $_POST['product_details'];
        $i=0;
        for($i = 0;$i < count($_POST['product_keys']); $i++) {
            $keys[] = explode(',', $_POST['product_keys'][$i]);
        }
        for($i = 0;$i < count($_POST['product_details']); $i++) {
            $vals[] = explode(',', $_POST['product_details'][$i]);
        }
        
       foreach($vals as $key => $product){
            if(isset($_POST['updproduct'][$key])){
                $product_list[$key]['id'] = $_POST['product_id'][$key];
                foreach($product as $k => $list){
                    $product_list[$key][$keys[$key][$k]] = $list;
                }
            }
        }

        //pr($product_list);die;
        foreach($product_list as $key => $plist){
            $product_id = $plist['id'];
            //unset($plist['product_id']);
            
            if(isset($plist['quantity'])){
                $inventory_quantity = $plist['quantity'];
                
                $rowcount = $this->Product->find('first', array('conditions' => array('Product.id'=>$product_id)));
                if(isset($rowcount['Inventory'])){
                    $this->loadModel('Inventory');
                    $invres = $this->Inventory->updateAll(
                            array('Inventory.quantity' => $inventory_quantity),
                            array('Inventory.product_id' => $product_id));
                            print_r($rowcount['Inventory'][0]['id']);
                }
                unset($plist['Product.quantity']);
            }

            $res = $this->Product->save($plist);
            if ($res == true){
                $this->Session->setFlash(__('Product are Updated Successfully'),'default',array('class'=>'alert alert-success'));
            } else{
                $this->Session->setFlash(__('Products record could not be saved. Please, try again'),'default',array('class'=>'alert alert-danger'));
            }
        }
        exit;
    }
    
    function upload() {
        $error_messages = array(
            1 => 'The uploaded file must be less then 2Mb',
            2 => 'The uploaded file must be less then 2Mb',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk',
            8 => 'A PHP extension stopped the file upload',
            'post_max_size' => 'The uploaded file must be less then 2Mb',
            'max_file_size' => 'File is too big',
            'min_file_size' => 'File is too small',
            'accept_file_types' => 'Filetype not allowed',
            'max_number_of_files' => 'Maximum number of files exceeded',
            'max_width' => 'Image exceeds maximum width',
            'min_width' => 'Image requires a minimum width',
            'max_height' => 'Image exceeds maximum height',
            'min_height' => 'Image requires a minimum height',
            'abort' => 'File upload aborted',
            'image_resize' => 'Failed to resize image'
        );

        $this->Product->validate = array(
            'imageurl' => array(
                'myMime' => array(
                    'rule' => 'myMime',
                    'message' => 'You can upload only gif, jpg or png files'
                ),
                'fileExtention' => array (
                    'rule' => array('extension',  array('gif', 'jpeg', 'png', 'jpg')),
                    'message' => 'You can upload only gif, jpg or png files'
                ),
            ),
        );

        if ($this->request->is('post')) {

            
            $target_dir = WWW_ROOT."uploads/products/";
            $file = pathinfo($this->request->data['Product']['imageurl']['name']);
            $extension = pathinfo($this->request->data['Product']['imageurl']['name'], PATHINFO_EXTENSION);
            if($extension) {
                $extension = '.'.$extension;
            }
            $target_file = md5(time()) . $extension;
            $target_path = $target_dir . $target_file;
            
            $this->Product->set(['Product'=>['imageurl' => $target_path]]);
            if ($this->Product->validates($this->Product->validate)) {
                if(move_uploaded_file($this->request->data['Product']['imageurl']['tmp_name'], $target_path)) {
                    if($this->request->data['Product']['id'] != 'new') {
                        $this->Product->recursive = -1;
                        $product = $this->Product->find('first', ['conditions' => array('Product.id' => $this->request->data['Product']['id']), 'callbacks'=>false]);
                        if($product) {

                            if($product['Product']['user_id'] != $this->Auth->user('id')) {
                                $response['action'] = 'error';
                                $response['msg'] = 'You have no access to update this product.';
                            } else {
                                // Clear garbage
                                if(!empty($product['Product']['imageurl'])) { // && substr($product['Product']['imageurl'], 0, 4) != 'http'
                                    $ex_file = pathinfo($product['Product']['imageurl']);
                                    if(file_exists($target_dir  . $ex_file['basename'] . $extension)) {
                                        @unlink($target_dir  . $ex_file['basename'] . $extension);
                                    }
                                }

                                $product['Product']['imageurl'] = Router::url('/', true). 'uploads/products/'. $target_file;
                                //unset($this->Product->validate['imageurl']);
                                if($this->Product->save($product)) {
                                    $response['action'] = 'success';
                                    $response['imageurl'] = Router::url('/', true). 'uploads/products/'. $target_file;
                                    $response['id'] = $product['Product']['id'];
                                } else {
                                    $response['action'] = 'error';
                                    if(!empty($this->Product->validationErrors['imageurl'][0])) {
                                        $response['msg'] = $this->Product->validationErrors['imageurl'][0];
                                    } else {
                                        $response['msg'] = 'Can\'t update product.';
                                    }
                                }
                            }
                        } else {
                            $response['action'] = 'error';
                            $response['msg'] = 'Can\'t update product.';
                        }
                    } else {
                        $response['action'] = 'success';
                        $response['imageurl'] = Router::url('/', true). 'uploads/products/'. $target_file;
                        $response['id'] = 'new';
                    }
                } else {
                    $response['action'] = 'error';
                    $response['msg'] = $error_messages[$this->request->data['Product']['imageurl']['error']];
                }
            } else {
                $response['action'] = 'error';
                $response['msg'] = $this->Product->validationErrors['imageurl'][0];
            }
        } else {
            $response['action'] = 'error';
            $response['msg'] = 'Request not found';
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    function add_schannel_price() {
        if ($this->request->is('post')) {
            $this->loadModel('ProductsPrices');
            $this->ProductsPrices->recursive = -1;
            $price = $this->ProductsPrices->find('first', [
                'conditions' => [
                    'schannel_id' => $this->request->data['schannel_id'],
                    'product_id' => $this->request->data['product_id'],
                ]
            ]);

            if($price) {
                $price['ProductsPrices']['value'] = $this->request->data['value'];
            } else {
                $price = [];
                $price['ProductsPrices'] = $this->request->data;
            }
            //pr($price);
            if($this->ProductsPrices->save($price)) {
                $price['ProductsPrices']['id'] = $this->ProductsPrices->id;
                $response['action'] = 'success';
                $response['price'] = $price['ProductsPrices'];
            } else {
                $response['action'] = 'error';
                $response['errors'] = $this->ProductsPrices->validationErrors;
            }
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    function delete_product_kit() {
        if ($this->request->is('post')) {
            $this->loadModel('Kit');
            $this->Kit->id = $this->request->data['id'];
            $this->Kit->delete();
            $response['action'] = 'success';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    function add_product_kit() {
        if ($this->request->is('post')) {
            $this->loadModel('Kit');
            $this->Kit->recursive = -1;
            $part = $this->Kit->find('first', [
                'conditions' => [
                    'parts_id' => $this->request->data['parts_id'],
                    'product_id' => $this->request->data['product_id'],
                ]
            ]);

            if($part) {
                $part['Kit']['quantity'] = $this->request->data['quantity'];
                if($this->request->data['active'] == 'true') {
                    $part['Kit']['active'] = 1;
                } else {
                    $part['Kit']['active'] = 0;
                }
            } else {
                $part = [];
                $part['Kit'] = $this->request->data;
                if($this->request->data['active'] == 'true') {
                    $part['Kit']['active'] = 1;
                } else {
                    $part['Kit']['active'] = 0;
                }
                $part['Kit']['user_id'] = $this->Auth->user('id');
            }
            
            if($this->Kit->save($part)) {
                $part['Kit']['id'] = $this->Kit->id;
                $response['action'] = 'success';
                $response['part'] = $part['Kit'];
            } else {
                $response['action'] = 'error';
                $response['errors'] = $this->Kit->validationErrors;
            }
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    function delete_schannel_price() {
        if ($this->request->is('post')) {
            $this->loadModel('ProductsPrices');
            $this->ProductsPrices->id = $this->request->data['id'];
            $this->ProductsPrices->delete();
            $response['action'] = 'success';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    function getShortDet($id) {
        $result = $this->Product->getShortDet($id);
        echo json_encode($result);
        exit;
    }

    /**
     * Get price for purchase orders
     *
     * @throws NotFoundException
     * @param int $id
     * @param int $supplier_id
     * @return void
     */
    function getPurchasePrice($id, $supplier_id = null) {
        
        $result = $this->Product->getShortDet($id);

        $this->loadModel('Productsupplier');
        $this->Productsupplier->recursive = -1;
        $condition['product_id'] = $id;
        $condition['status'] = 'yes';
        if($supplier_id) {
            $condition['supplier_id'] = $supplier_id;
        }
        $supplierPrice = $this->Productsupplier->find('first', array('conditions' => $condition, 'fields' => array('price')));
        if($supplierPrice && $supplierPrice['Productsupplier']['price']) {
            $result['price'] = $supplierPrice['Productsupplier']['price'];
        }
        
        echo json_encode($result);
        exit;
    }

    function upImg() {
        $url = Router::url('/', true);
        $new_img = $url . 'img/no-photo.svg';

        $old_img = '/image_missing.jpg'; //https://delivrd.com/image_missing.jpg
        $this->Product->recursive = -1;
        $res = $this->Product->updateAll(
            ['Product.imageurl' => "'". $new_img ."'"],
            ['Product.imageurl like' => "%". $old_img]
        );
        if($res) {
            die('Success');
        } else {
            die('Error');
        }
        exit;
    }

    function backImg() {
        $url = Router::url('/', true);
        $new_img = $url . 'image_missing.jpg';

        $old_img = '/img/no-photo.svg'; //https://delivrd.com/image_missing.jpg
        $this->Product->recursive = -1;
        $res = $this->Product->updateAll(
            ['Product.imageurl' => "'". $new_img ."'"],
            ['Product.imageurl like' => "%". $old_img]
        );
        if($res) {
            die('Success');
        }
        exit;
    }

}