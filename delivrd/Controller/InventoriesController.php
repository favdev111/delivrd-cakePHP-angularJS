<?php
App::uses('AppController', 'Controller');
App::uses('AdminHelper', 'View/Helper');
/**
 * Inventories Controller
 *
 * @property Inventory $Inventory
 * @property PaginatorComponent $Paginator
 */
class InventoriesController extends AppController {


    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator','Search.Prg','Csv.Csv','Session', 'Access', 'InventoryManager', 'Cookie');

    public $uses = array('Inventory', 'Product');

    public $theme = 'Mtro';

    public function beforeFilter() {
       parent::beforeFilter();
       $this->Auth->allow('cron_invenotry_alert');
    }

    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->layout = 'mtrd';
        $this->loadModel('Serial');

        $serial_no = 0;
        if ($this->Auth->user('is_limited') && empty($this->Access->_access['Inventory'])) {
            die('404');
        }

        if ($this->request->is('post')) {
            $this->Prg->commonProcess(null, ['paramType'=>'query']);
        }


        $pcond['Product.uom'] = 'Kit';
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
            $pcond['OR']['Product.user_id'] = $user_ids;
            if($product_ids) {
                $pcond['OR']['Product.id'] = $product_ids;
            }
        } else {
            $pcond['Product.user_id'] = $this->Auth->user('id');
        }
        $is_virtual_fields = $this->Product->find('count', ['conditions' => $pcond]);

        $is_write = true;
        if($this->Auth->user('is_limited')) {
            $is_write = false;
            $accesslist = Set::combine($this->Access->_access['Inventory'], '{n}.Warehouse.id', '{n}.NetworksAccess.access');
            foreach ($accesslist as $value) {
                if($value != 'r') {
                    $is_write = true;
                    break;
                }
            }
        }
        $this->Product->recursive = -1;
        $count_pdt = $this->Product->find('count', ['conditions' => ['Product.user_id' => $this->Auth->user('id')]]);

        // Access part
        if(!empty($this->Access->_access['Inventory'])) { //user has network inventory
            $warehouses = $this->Access->getLocations('Inventory');

            $loc_ids = $my_loc = $other_loc = [];

            $warehouses_list = [];
            foreach ($warehouses as $net => $loc) {
                foreach ($loc as $key => $val) {
                    $loc_ids[] = $key;
                    if($net !=  'My Locations') {
                        $other_loc[] = $key;
                        $warehouses_list[$key] = $net .' <i class="fa fa-angle-right"></i> '. $val;
                    } else {
                        $my_loc[] = $key;
                        $warehouses_list[$key] = $val;
                    }
                }
            }
            $products = $this->Access->getProducts('Inventory');
            $conditions['OR'] = [
                'Inventory.warehouse_id' => $my_loc, //'Inventory.user_id' => $this->Auth->user('id'),
                ['Inventory.warehouse_id' => $other_loc, 'Inventory.product_id' => array_keys($products)]
            ];
            $count_pdt = $count_pdt + count($products);
        } else {
            
            $warehouses = $warehouses_list = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.status' => 'active', 'Warehouse.user_id' => $this->Auth->user('id'))));

            $loc_ids = array_keys($warehouses);
            $conditions = [/*'Inventory.user_id' => $this->Auth->user('id'),*/ 'Inventory.warehouse_id' => $loc_ids];
        }

        // if setting locationactive set to 1 or user have access to other locations
        $locationsactive = 0;
        if(count($warehouses) > 1 || $this->Session->read('locationsactive')) {
            $locationsactive = 1;
        }

        //$limit = 10;
        $limit = $this->Auth->user('list_limit');
        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );

        if ($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }

        $searchby = '';
        if ($this->request->query('searchby')) {
            $serial = $this->Serial->find('first', array(
                'conditions' => ['Serial.serialnumber' => $this->request->query('searchby'), 'Serial.warehouse_id' => $loc_ids],
                'contain' => ['Product'],
                'fields' => array('Product.id', 'Serial.warehouse_id')
            ));
            
            $conditions['AND']['OR'] = [
                'Product.name LIKE' => '%'. $this->request->query('searchby') .'%',
                'Product.sku' => $this->request->query('searchby'),
                'Product.barcode' => $this->request->query('searchby'),
                
            ];
            if($serial) {
                $serial_no = $this->request->query('searchby');
                $conditions['AND']['OR']['AND'] = ['Product.id' => $serial['Product']['id'], 'Inventory.warehouse_id' => $serial['Serial']['warehouse_id']];
            }
        }

        if ($this->request->query('warehouse_id')) {
            $conditions['Inventory.warehouse_id'] =  $this->request->query('warehouse_id');
        }

        $product = ['product_id' => '', 'name' => ''];
        if ($this->request->query('product')) {
            $product['product_id'] = $conditions['Inventory.product_id'] =  $this->request->query('product');
            $this->Product->id = $product['product_id'];
            $product['name'] = $this->Product->field('name');
        }

        $conditions['Product.status_id NOT IN'] = [12, 13];
        $conditions['Product.deleted'] = 0;
        //$conditions['Warehouse.status'] = 'active';
        
        $this->paginate = array(
            'conditions' => $conditions,
            'limit' => $limit,
        );
        $inventories = $this->paginate();
        if(!(count($inventories) == 1 && $serial_no)) {
            $serial_no = 0;
        }

        if($this->Session->read('serial_no')) {
            $serial_no = $this->Session->read('serial_no');
            $this->Session->write('serial_no', 0);
        }

        $inventory_id = 0;
        $locations = [];
        if($serial_no) {
            $serial_no = $this->Serial->find('first', ['conditions' => array('Serial.serialnumber' => $serial_no)]);
            if($inventories) {
                $inventory_id = $inventories[0]['Inventory']['id'];
                // We need locations only from the same network of inventory
                if($inventories[0]['Network']['name'] && isset($warehouses[$inventories[0]['Network']['name']])) {
                    $locations = $warehouses[$inventories[0]['Network']['name']];
                } else if(isset($warehouses['My Locations'])) {
                    $locations = $warehouses['My Locations'];
                } else {
                    $locations = $warehouses;
                }
            }
        }

        $supplier_count = 0;
        $this->loadModel('Kit');
        foreach($inventories as &$inv) {
            if($inv['Product']['uom'] == 'Kit') {
                $inv['Inventory']['virtual_quantity'] = $this->Kit->getVirtualQuantity($inv['Product']['id'], $inv['Inventory']['warehouse_id']);
            }
            if(!empty($inv['Product']['Productsupplier'])) {
                foreach($inv['Product']['Productsupplier'] as $supp) {
                    if($supp['status'] == 'yes') {
                        $supplier_count = 1;
                        break;
                    }
                }
            }
        }
        $this->countInventory();
        $this->set(compact('count_pdt', 'inventories', 'supplier_count', 'locationsactive', 'is_write', 'is_virtual_fields', 'warehouses', 'warehouses_list', 'product', 'serial_no', 'inventory_id', 'locations', 'limit', 'options')); //
    }

    public function countInventory() {
        $this->loadModel('OrdersLine');
        $this->OrdersLine->recursive = -1;
        $options = array('OrdersLine.user_id' => $this->Auth->user('id'));
        $this->Session->write('invcount', $this->OrdersLine->find('count', array('conditions' => $options)));
    }

    public function unique_pdts() {
        $this->layout = 'mtrd';
        $limit = 10;
        $this->set(compact('limit'));
    }

    public function ajax_unique_pdts() {

        $warehouses = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.status' => 'active', 'Warehouse.user_id' => $this->Auth->user('id'))));

        $orderBy = 'Product.name';
        if($this->request->query('sortby')) {
            $orderBy = $this->request->query('sortby');
        }
        $orderDir = 'ASC';
        if($this->request->query('sortdir')) {
            $orderDir = $this->request->query('sortdir');
        }

        $this->Inventory->recursive = 2;
        $this->Inventory->virtualFields['total'] = 'round(sum(Inventory.quantity))';
        $this->Inventory->virtualFields['safety_stock'] = 'round(ROUND(Product.safety_stock, 0))';
        $inventory = $this->Inventory->find('all', array(
            'conditions' => array(
                'Product.user_id' => $this->Auth->user('id'),
                'Inventory.warehouse_id IN' => array_keys($warehouses),
                'OR' => array(
                    'Product.safety_stock !=' => 0,
                    'Product.reorder_point !='=> 0
                ),
            ),
            'fields'  => array(
                    'Product.id',
                    'Product.name',
                    'Inventory.safety_stock',
                    'Product.safety_stock',
                    'Product.reorder_point',
                    'Product.sku',
                    'Product.imageurl ',
                    'Inventory.product_id',
                    'Inventory.total',
                ),
            'group' => 'Inventory.product_id HAVING Product.safety_stock > sum(Inventory.quantity) OR Product.reorder_point > sum(Inventory.quantity)',
            'order' => array($orderBy => $orderDir),
            'recursive' => 1
        ));

        $page = 1;
        if($this->request->query('page')) {
            $page = $this->request->query('page');
        }

        $limit = 5;
        if($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }
        
        $total = count($inventory);
        $totalPages = ceil( $total / $limit ); //calculate total pages
        $page = max($page, 1); //get 1 page when $_GET['page'] <= 0
        $page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
        $offset = ($page - 1) * $limit;

        if( $offset < 0 ) $offset = 0;
        $result = array_slice($inventory, $offset, $limit);
        
        $response['draw'] = 1;
        $response['recordsTotal'] = count($inventory);
        $response['rows_count'] = count($result);
        $response['rows'] = $result;

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;

    }

    public function exp_unique_pdts() {
        $this->layout = 'mtrd';
        
        $warehouses = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.status' => 'active', 'Warehouse.user_id' => $this->Auth->user('id'))));

        $this->Inventory->recursive = 2;
        $this->Inventory->virtualFields['total'] = 'round(sum(Inventory.quantity), 2)';
        $inventory = $this->Inventory->find('all', array(
            'conditions' => array(
                'Product.user_id' => $this->Auth->user('id'),
                'Inventory.warehouse_id IN' => array_keys($warehouses),
                'OR' => array(
                    'Product.safety_stock !=' => 0,
                    'Product.reorder_point !='=> 0
                ),
            ),
            'fields'  => array(
                'Product.id',
                'Product.name',
                'Product.safety_stock',
                'Product.reorder_point',
                'Product.sku',
                'Product.imageurl ',
                'Inventory.product_id',
                'Inventory.total',
            ),
            'group' => 'Inventory.product_id HAVING Product.safety_stock > sum(Inventory.quantity) OR Product.reorder_point > sum(Inventory.quantity)',
            'recursive' => 1
        ));

        $_serialize = 'inventory';
        $_header = array('SKU', 'Product Name', 'Safety Stock', 'Reorder Point', 'Total Qty');
        $_extract = array('Product.sku','Product.name','Product.safety_stock', 'Product.reorder_point','Inventory.total');

        $file_name = "Delivrd_".date('Y-m-d-His')."_inventory_alert.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('inventory', '_serialize', '_header', '_extract'));
    }

    /**
     * print inventory method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function printInventory() {
        $this->layout = 'mtrds';
        $inventory = $this->Inventory->find('all', array('conditions' => array('Product.user_id'  => $this->Auth->user('id')),'contain' => array('Product' => array('fields' => array('Product.id', 'Product.name', 'Product.sku', 'Product.created')), 'Warehouse' => array('fields' => array('Warehouse.id', 'Warehouse.name')))));

        $this->set('inventory',$inventory);
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->layout = false;
        $this->Inventory->id = $id;
        $this->loadModel('OrdersLine');
        $inventory = $this->Inventory->find('first', array(
            'conditions' => array('Inventory.id'  => $id),
            'contain' => array('Warehouse', 'Product'),
            'fields' => array('Inventory.id', 'Inventory.product_id', 'Inventory.warehouse_id', 'Inventory.user_id', 'Warehouse.name', 'Product.name', 'Product.sku'),
            'callbacks' => false
        ));
        
        

        if(!$id || empty($inventory)) {
            throw new NotFoundException(__('Invalid inventory'));
        }

        // Check is invenotry have any transactions history
        $this->loadModel('OrdersLine');
        $this->OrdersLine->recursive = 0;
        $so_lines = $this->OrdersLine->find('all', array(
            'conditions' => array(
                'OrdersLine.product_id' => $inventory['Inventory']['product_id'],
                'OrdersLine.warehouse_id' => $inventory['Inventory']['warehouse_id'],
                'OrdersLine.type' => 1
            ),
            'contain' => array('Order'),
            'fields' => array(
                'Order.id',
                'Order.external_orderid'
                /*'GROUP_CONCAT(DISTINCT IF(OrdersLine.type = 1, OrdersLine.order_id, null) SEPARATOR ", ") as SO',
                'GROUP_CONCAT(DISTINCT IF(OrdersLine.type = 2, OrdersLine.order_id, null) SEPARATOR ", ") as PO',
                'COUNT(DISTINCT IF(OrdersLine.type != 2 AND OrdersLine.type != 1, OrdersLine.id, null)) as TX',*/
            ),
            /*'group' => 'OrdersLine.warehouse_id'*/
        ));

        $po_lines = $this->OrdersLine->find('all', array(
            'conditions' => array(
                'OrdersLine.product_id' => $inventory['Inventory']['product_id'],
                'OrdersLine.warehouse_id' => $inventory['Inventory']['warehouse_id'],
                'OrdersLine.type' => 2
            ),
            'contain' => array('Order'),
            'fields' => array(
                'Order.id',
                'Order.external_orderid'
            )
        ));

        if ($this->request->is(array('post', 'put'))) {
            if($inventory['Inventory']['user_id'] != $this->Auth->user('id')) {
                $this->Session->setFlash(__('You have no access to delete this record.'), 'admin/danger');
                return $this->redirect(array('action' => 'index'));
            }
            if($inventory['Warehouse']['name'] == 'Default') {
                $this->Session->setFlash(__("You cannot delete a Default inventory location record.<br>You can rename Default inventory location to any other name"), 'admin/danger');
            } else {
                if(!empty($po_lines) || !empty($so_lines)) {
                    $this->Session->setFlash(__('Inventory record cannot be deleted.'), 'admin/danger');
                } else {
                    if ($this->Inventory->delete($id)) {
                        $this->OrdersLine->deleteAll(
                            array('OrdersLine.product_id' => $inventory['Inventory']['product_id'], 'OrdersLine.warehouse_id' => $inventory['Inventory']['warehouse_id']),
                            false
                        );
                        $this->Session->setFlash(__('The inventory record has been deleted.'), 'admin/success');
                    } else {
                        $this->Session->setFlash(__('The inventory record could not be deleted. Please, try again.'), 'admin/danger');
                    }
                }
            }
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->set(compact('inventory', 'so_lines', 'po_lines'));
        }
        
    }

    

    /**
     * count quantity method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function count($id = null, $quantity = null,$import = null) {
        $this->layout = 'mtrd';
        if (!$this->Inventory->exists($id)) {
            throw new NotFoundException(__('Invalid inventory'));
        }

        $sentqty = 0;
        $recqty = 0;

        if(isset($quantity)) {
            $this->request->data['Inventory']['id'] = $id;
            $this->request->data['Inventory']['quantity'] = $quantity;
            $this->request->data['Inventory']['comments'] = 'By import file';
        }

        $this->Inventory->recursive = 2;
        $conditions = array(
            'conditions' => array('Inventory.id' => $id),
            'contain' => array('Product' => array('fields' => array('Product.name','Product.imageurl ','Product.value')))
        );
        $current_inv = $this->Inventory->find('first', $conditions);

        if($this->Access->hasInventoryAccess($current_inv)) {
            if ($this->request->is(array('post', 'put')) || isset($quantity)) {
                $delta = $current_inv['Inventory']['quantity'] - $this->request->data['Inventory']['quantity'];
                $sentqty = ($delta > 0) ? abs($delta) : 0;
                $recqty = ($delta < 0) ? abs($delta) : 0;
                $validation = true;

                $data = array(
                    'OrdersLine' => array(
                        'order_id' => 4294967294,
                        'line_number' => 1,
                        'type' => 3,
                        'product_id'  => $current_inv['Inventory']['product_id'],
                        'quantity' => $this->request->data['Inventory']['quantity'],
                        'receivedqty' => $recqty,
                        'damagedqty' => 0,
                        'sentqty' => $sentqty,
                        'unit_price' => $current_inv['Product']['value'],
                        'total_line' => $current_inv['Product']['value'] * abs($delta),
                        'foc' => '',
                        'warehouse_id' => $current_inv['Inventory']['warehouse_id'],
                        'return' => '',
                        'comments' => $this->request->data['Inventory']['comments'],
                        'user_id' => $current_inv['Inventory']['user_id']
                    )
                );
                if($this->request->data['Inventory']['quantity'] >= 0) {
                    $result = $this->Inventory->saveInventory($this->request->data, $data, $validation);
                    if($result === true) {
                        $this->Session->setFlash(__('Inventory quantity update successfully.'), 'admin/success', array());
                    return $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('Inventory quantity could not be updated. Please, try again.'), 'admin/danger', array());
                    }
                } else {
                    $this->Session->setFlash(__('Inventory quantity could not be updated. Please, try again.'), 'admin/danger', array());
                }
            } else {
                $this->request->data = $current_inv;
            }
        } else {
            // Have no access
            $this->Session->setFlash(__('Inventory quantity could not be updated. You have no access.'),'default',array('class'=>'alert alert-danger'));
            return $this->redirect(array('action' => 'index'));
        }

        $warehouses = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.id' => $current_inv['Inventory']['warehouse_id'],'Warehouse.user_id' => $current_inv['Inventory']['user_id'])));
        $this->set(compact('warehouses','current_inv'));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add($product_id = null) {
        $this->layout = 'mtrd';
        $this->loadModel('Product');
        $this->loadModel('Warehouse');

        $network = false;
        if(!empty($product_id)) { //Add inventory for selected products
            $product = $this->Product->find('first', [
                'conditions' => ['Product.id' => $product_id],
                'contain' => ['Inventory'],
                'fields' => ['Product.*'],
                'callbacks' => false
            ]);

            if(!$product) {
                throw new NotFoundException(__('Invalid inventory'));
            }

            $products = [$product['Product']['id'] => $product['Product']['name']];

            if($product['Product']['user_id'] == $this->Auth->user('id')) {
                $warehouses = $this->Inventory->Warehouse->find('list',array('conditions' => array('user_id' => $this->Auth->user('id'))));
            } else {
                $this->loadModel('Network');
                $network = $this->Network->find('first',['conditions' => ['Network.created_by_user_id' => $product['Product']['user_id']]]);
                $warehouses = $this->Access->getLocations('Inventory', $network['Network']['id'], 'w');
                if(!$warehouses) {
                    $this->Session->setFlash(__('You have no access to add invontory to '. $network['Network']['name'] .' network.'),'admin/danger', array());
                    return $this->redirect(array('action' => 'index'));
                }
            }
        } else {
            $warehouses = $this->Access->getLocations('Inventory', false, 'w');
            $products = $this->Access->productList('Inventory', 'w'); // Network Products
            if(!$this->Auth->user('is_limited')) {
                $my_products = $this->Inventory->Product->find('list',array('conditions' => array('Product.user_id' => $this->Auth->user('id'))));
                $products = ['My Products' => $my_products] + $products;
            }

            if($this->Auth->user('is_limited') && empty($warehouses)) {
                $this->Session->setFlash(__('You have no access to any location.'),'default',array('class'=>'alert alert-warning'));
                return $this->redirect(array('action' => 'index'));
            }

            if($this->Auth->user('is_limited') && empty($products)) {
                $this->Session->setFlash(__('You have no access to any product.'),'default',array('class'=>'alert alert-warning'));
                return $this->redirect(array('action' => 'index'));
            }
        }
        $locationsactive = 1;

        if ($this->request->is(array('post', 'put'))) {
            //check if inventory record already exists. if it does - do count. outherwise - create new one
            $inv_id = $this->Inventory->getInvenotry($this->request->data['Inventory']['product_id'],$this->request->data['Inventory']['warehouse_id'], array('Inventory.id','Inventory.quantity'));
            if(empty($inv_id)) {
                //inventory record does not exist, create a new one
                $damaged_qty = '';
                if(isset($this->request->data['Inventory']['damaged_qty'])) {
                    $damaged_qty = $this->request->data['Inventory']['damaged_qty'];
                }
                if($this->Inventory->createRecord($this->request->data['Inventory']['product_id'], $this->request->data['Inventory']['warehouse_id'], $this->request->data['Inventory']['quantity'], $damaged_qty, $this->request->data['Inventory']['comments'])) {
                    $this->Session->setFlash(__('Inventory quantity update successfully.'), 'admin/success', array());
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Inventory quantity could not be updated. Please, try again.'), 'admin/danger', array());
                }
            } else {
                $this->Session->setFlash(__('Inventory record already exists for this product. Inventory not updated. Use inventory count to update existing inventory records.'), 'admin/danger', array());
                return $this->redirect(array('action' => 'index'));
            }
        }
        $this->set(compact('products','warehouses', 'network', 'locationsactive'));
    }

    /**
     * Issue/Receive product
     *
     * @param int $id
     * @return void
     */
    public function grgi($id = null) {
        $this->layout = false;

        if (!$this->Inventory->exists($id)) {
            $this->Session->setFlash(__('Product does not exist.'), 'admin/danger', array());
            return $this->redirect(array('action' => 'index'));
        }

        $this->Inventory->recursive = 1;
        $loptions = array(
            'conditions' => array('Inventory.id' => $id),
            'contain' => array(
                'Product' => array('fields' => array(
                    'Product.name',
                    'Product.imageurl',
                    'Product.value',
                    'Product.safety_stock',
                    'Product.reorder_point'
                )),
                'Product.Color.htmlcode',
                'Product.Color.name',
                'Product.Size.name',
                'Product.Size.description',
                'Warehouse' => array('fields' => array('Warehouse.name')),
            )
        );
        $current_inv = $this->Inventory->find('first', $loptions);

        if($this->Access->hasInventoryAccess($current_inv)) {
            $availableinv = $current_inv['Inventory']['quantity'];
            $sourcesafetystock = $current_inv['Product']['safety_stock'];
            $sourcereorderpoint = $current_inv['Product']['reorder_point'];

            if($this->request->is(array('post', 'put'))) {
                if($this->request->data['Inventory']['ttype'] == 'GI') {
                    $result = $this->Inventory->issueQuantity($current_inv['Inventory']['id'], $this->request->data['Inventory']['tquantity'], 4294967294, $this->request->data['Inventory']['comments']);
                } else if($this->request->data['Inventory']['ttype'] == 'GR') {
                    $result = $this->Inventory->receiveQuantity($current_inv['Inventory']['id'], $this->request->data['Inventory']['tquantity'], 4294967294, $this->request->data['Inventory']['comments']);
                } else {
                    $this->Session->setFlash(__('You did not select Issue from Inventory or Receive to Inventory. Please try again.'), 'admin/danger', array());
                    return $this->redirect(array('action' => 'grgi',$id));
                }
                if($result === true) {
                    $this->Session->setFlash(__('Inventory quantity update successfully.'), 'admin/success', array());
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Inventory quantity could not be updated. Please, try again.'), 'admin/danger', array());
                }
            } else {
                $this->request->data = $current_inv;
            }

            //$products = $this->Inventory->Product->find('all', array('fields' => array('Product.name', 'Product.imageurl')));
            $warehouses = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.id' => $current_inv['Inventory']['warehouse_id'],'Warehouse.user_id' => $current_inv['Inventory']['user_id'])));
            
            $this->set(compact('warehouses', 'availableinv','sourcesafetystock','sourcereorderpoint','current_inv'));
        } else {
            $this->Session->setFlash(__('You have no access to add Issue/Receive.'),'default',array('class'=>'alert alert-danger'));
            return $this->redirect(array('action' => 'index'));
        }
    }

    /**
     * Assemble Kit product
     *
     * @param int $id
     * @return void
     */
    public function assemble($id = null) {
        $this->layout = false;

        $this->loadModel('Kit');
        
        if (!$this->Inventory->exists($id)) {
            $this->Session->setFlash(__('Product does not exist.'), 'admin/danger', array());
            return $this->redirect(array('action' => 'index'));
        }

        $this->Inventory->recursive = 1;
        $loptions = array(
            'conditions' => array('Inventory.id' => $id),
            'contain' => array(
                'Product' => array('fields' => array(
                    'Product.name',
                    'Product.imageurl',
                    'Product.value',
                    'Product.safety_stock',
                    'Product.reorder_point'
                )),
                'Product.Color.htmlcode',
                'Product.Color.name',
                'Product.Size.name',
                'Product.Size.description',
                'Warehouse' => array('fields' => array('Warehouse.name')),
            )
        );
        $current_inv = $this->Inventory->find('first', $loptions);

        if($this->Access->hasInventoryAccess($current_inv)) {
            $vquantity = $this->Kit->getVirtualQuantity($current_inv['Inventory']['product_id'], $current_inv['Inventory']['warehouse_id']);

            $availableinv = $vquantity; // $current_inv['Inventory']['quantity'];
            $sourcesafetystock = $current_inv['Product']['safety_stock'];
            $sourcereorderpoint = $current_inv['Product']['reorder_point'];

            if($this->request->is(array('post', 'put'))) {
                $kitQuantity = $this->Kit->getKitsQuantity($current_inv['Inventory']['product_id']);
                foreach ($kitQuantity as $prod_id => $qty) {
                    $issue_quantity = $qty * $this->request->data['Inventory']['tquantity'];
                    $kitInv = $this->Inventory->getInvenotry($prod_id, $this->request->data['Inventory']['warehouse_id']);
                    $this->Inventory->issueToAssemble($kitInv['Inventory']['id'], $issue_quantity, 4294967294, 'Issued to assemble product');
                }
                $result = $this->Inventory->receiveQuantity($current_inv['Inventory']['id'], $this->request->data['Inventory']['tquantity'], 4294967294, $this->request->data['Inventory']['comments']);
                
                if($result === true) {
                    $this->Session->setFlash(__('Inventory quantity update successfully.'), 'admin/success', array());
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Inventory quantity could not be updated. Please, try again.'), 'admin/danger', array());
                }
            } else {
                $this->request->data = $current_inv;
            }

            //$products = $this->Inventory->Product->find('all', array('fields' => array('Product.name', 'Product.imageurl')));
            $warehouses = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.id' => $current_inv['Inventory']['warehouse_id'],'Warehouse.user_id' => $current_inv['Inventory']['user_id'])));
            
            $this->set(compact('warehouses', 'availableinv','sourcesafetystock','sourcereorderpoint','current_inv'));
        } else {
            $this->Session->setFlash(__('You have no access to add Issue/Receive.'),'default',array('class'=>'alert alert-danger'));
            return $this->redirect(array('action' => 'index'));
        }
    }

    /**
     * Transfer product from one location to other
     *
     * @param int $id
     * @return void
     */
    public function transfer($id = null) {
        $this->layout = 'mtrd';
        $sentqty = 0;
        $recqty = 0;
        
        $loptions = array(
            'conditions' => array('Inventory.id' => $id),
            'contain' => array(
                'Warehouse' => array('fields' => array('Warehouse.id','Warehouse.name')),
                'Product' => array('fields' => array('Product.id','Product.name','Product.imageurl','Product.value','Product.safety_stock','Product.reorder_point')
            ),
            'callbacks' => false
        ));
        $sourceinv = $this->Inventory->find('first', $loptions);

        if($this->Access->hasInventoryAccess($sourceinv)) {

            $availableinv = $sourceinv['Inventory']['quantity'];
            $sourcesafetystock = $sourceinv['Product']['safety_stock'];
            $sourcereorderpoint = $sourceinv['Product']['reorder_point'];
            if ($this->request->is(array('post', 'put'))) {
                if($availableinv < $this->request->data['Inventory']['tquantity']) {
                    $this->Session->setFlash(__('You are trying to transfer a quantity greater than quantity available.'), 'admin/danger', array());
                    return $this->redirect(array('action' => 'transfer', $id));
                }

                $destinv = $this->Inventory->getInvenotry($this->request->data['Inventory']['product_id'], $this->request->data['Inventory']['warehouse_id_to']);
                
                if(empty($destinv) && $this->Session->read('inventoryauto')) {
                    // Add inventory
                    if($this->Inventory->createRecord($this->request->data['Inventory']['product_id'], $this->request->data['Inventory']['warehouse_id_to'], 0, null, $this->request->data['Inventory']['comments'])) {
                        $destinv = $this->Inventory->getInvenotry($this->request->data['Inventory']['product_id'], $this->request->data['Inventory']['warehouse_id_to']);
                    } else {
                        $this->Session->setFlash(__('We have problem with ceating destination inventory record. Please try againe later.'), 'admin/danger', array());
                        return $this->redirect(array('action' => 'transfer', $id));
                    }
                }
                
                if(!$this->Access->hasInventoryAccess($destinv)) {
                    $this->Session->setFlash(__('You have no update access for '. $destinv['Warehouse']['name'] .' location.'), 'admin/danger', array());
                    return $this->redirect(array('action' => 'transfer', $id));
                }

                $is_success = false;
                $dataSource = $this->Inventory->getDataSource();
                $dataSource->begin();
                if($this->Inventory->issueQuantity($sourceinv['Inventory']['id'], $this->request->data['Inventory']['tquantity'], 4294967294, $this->request->data['Inventory']['comments'])) {
                    if($this->Inventory->receiveQuantity($destinv['Inventory']['id'], $this->request->data['Inventory']['tquantity'], 4294967294, $this->request->data['Inventory']['comments'])) {
                        $dataSource->commit();
                        $is_success = true;
                    } else {
                        $dataSource->rollback();
                    }
                }

                if($is_success) {
                    $this->Session->setFlash(__('Inventory quantity update successfully.'), 'admin/success');
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Couldn\'t transfer product. Please try again.'), 'admin/danger');
                    return $this->redirect(array('action' => 'index'));
                }
            }
            
            $source_warehouse = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.id' => $sourceinv['Warehouse']['id'],'Warehouse.user_id' => $sourceinv['Inventory']['user_id'])));
            $product = $this->Product->find('list',array('conditions' => array('Product.id' => $sourceinv['Product']['id']), 'callbacks' => false)); //,'Product.user_id' => $sourceinv['Inventory']['user_id']

            //If we have only one inventory record, we cannot transfer anything, we throw error
            if($this->Session->read('inventoryauto') == 0) {
                $whs_inv_records = $this->Inventory->find('list',array('fields' => array('Inventory.warehouse_id'),'conditions' => array('Inventory.user_id' => $sourceinv['Inventory']['user_id'],'Inventory.product_id' => $sourceinv['Product']['id'])));
                if(sizeof($whs_inv_records) == 1) {
                    $this->Session->setFlash(__('Product is defined for a single inventory location. You cannot transfer it to another location.'), 'admin/danger', array());
                    return $this->redirect(array('action' => 'index'));
                }
                $key = array_search($sourceinv['Warehouse']['id'], $whs_inv_records);
                unset($whs_inv_records[$key]);
                $warehouses = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.id' => $whs_inv_records)));
                $warehouses2 = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.id' => $whs_inv_records, 'Warehouse.status' => 'active')));
            } else {
                $warehouses = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.id != ' => $sourceinv['Warehouse']['id'],'Warehouse.user_id' => $sourceinv['Inventory']['user_id'])));
                $warehouses2 = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.id != ' => $sourceinv['Warehouse']['id'],'Warehouse.user_id' => $sourceinv['Inventory']['user_id'], 'Warehouse.status' => 'active')));
            }

            $this->set(compact('product','warehouses','warehouses2','source_warehouse','availableinv','sourcesafetystock','sourcereorderpoint'));
        } else {
            $this->Session->setFlash(__('You have no access to Location transfer.'), 'admin/danger', array());
            return $this->redirect(array('action' => 'index'));
        }
    }



    public function export() {
        $this->layout = false;

        $fields = array('SKU' => 'Product.sku','ProductName' => 'Product.name','Location' => 'Warehouse.name', 'AvailableStock'=>'Inventory.quantity');

        $available_fields = array(
            'Description' => 'Product.description',
            'Group' => 'Product.Group.name',
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
            'Status' => 'Product.Status.name',
            'Image' => 'Product.imageurl',
            'PageUrl' => 'Product.pageurl',
            'Color' => 'Product.Color.name',
            'Size' => 'Product.Size.name',
            'IssueLocation' => 'Product.issue_location',
            'ReceiveLocation' => 'Product.receive_location',
            'Category' => 'Product.Category.name',
            'SalesForecast' => 'Product.sales_forecast',
            'LeadTime' => 'Product.lead_time'
        );

        $this->loadModel('Field');
        $this->Field->recursive = 0;
        $custom_fields = $this->Field->find('list', array('conditions' => array('Field.user_id' => $this->Auth->user('id')), 'contain' => false ));

        $def_fields = [];
        $def_fields['fields'] = $this->Cookie->read('available_fields');
        $def_fields['custom'] = $this->Cookie->read('available_custom');
        $this->request->data = $def_fields;

        $this->set(compact('fields', 'available_fields', 'def_fields', 'custom_fields'));
    }

    public function exportcsv() {
        $this->Inventory->recursive = 0;
        $inventories = $this->Inventory->find('all',array(
            'conditions' => array('Inventory.user_id' => $this->Auth->user('id'), 'Warehouse.status' => 'active'),
            'contain' => array(
                'Warehouse',
                'Product',
                'Product.Size' => array('name'),
                'Product.Group'=>array('name'),
                'Product.Color'=>array('name'),
                'Product.Category'=>array('name'),
                'Product.Status'=>array('name')
            ),
            'fields' => array(
                'Inventory.id',
                'Inventory.quantity',
                'Product.sku',
                'Product.name',
                'Product.description',
                'Product.uom',
                'Product.width',
                'Product.height',
                'Product.depth',
                'Product.barcode',
                'Product.barcode_standards_id',
                'Product.bin',
                'Product.value',
                'Product.safety_stock',
                'Product.reorder_point',
                'Product.status_id',
                'Product.imageurl',
                'Product.pageurl',
                'Product.color_id',
                'Product.size_id',
                'Product.group_id',
                'Product.category_id',
                'Product.issue_location',
                'Product.receive_location',
                'Product.sales_forecast',
                'Product.lead_time',
                'Warehouse.name'
            )
        ));
        $this->loadModel('Warehouse');
        $wareouses = $this->Warehouse->find('list', array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'))));
        foreach ($inventories as &$value) {
            if(isset($wareouses[$value['Product']['issue_location']])) {
                $value['Product']['issue_location'] = $wareouses[$value['Product']['issue_location']];
            }
            if(isset($wareouses[$value['Product']['receive_location']])) {
                $value['Product']['receive_location'] = $wareouses[$value['Product']['receive_location']];
            }
        }

        if ($this->request->is(array('post', 'put'))) {
            if(isset($this->request->data['custom'])) {
                $this->loadModel('FieldsData');
                $field_data = $this->FieldsData->find('all', [
                    'conditions' => array('FieldsData.object_type' => 1, 'FieldsData.user_id' => $this->Auth->user('id')),
                    'contain' => array('Field' => array('name'), 'Field.FieldsValue' => array('id', 'value')),
                    'fields' => array('FieldsData.object_id as product_id', 'FieldsData.field_id', 'FieldsData.value')
                ]);
                
                $field_data = Set::combine($field_data, '{n}.FieldsData.field_id', '{n}', '{n}.FieldsData.product_id');

                foreach ($inventories as &$invent) {
                    if(isset($field_data[$invent['Product']['id']])) {
                        $fl = $field_data[$invent['Product']['id']];
                        foreach ($fl as $k => $v) {
                            if(array_key_exists($k, $this->request->data['custom'])) {
                                if(count($v['Field']['FieldsValue'])>0) {
                                    $options = Set::combine($v['Field']['FieldsValue'], '{n}.id', '{n}.value');
                                    echo h($v['FieldsData']['value']);
                                    if(isset($options[$v['FieldsData']['value']])) {
                                        $invent['Product']['Custom'. $k] = $options[$v['FieldsData']['value']];
                                    } else {
                                        $invent['Product']['Custom'. $k] = 'Not defined';
                                    }
                                } else {
                                    $invent['Product']['Custom'. $k] = $v['FieldsData']['value'];
                                }
                            }
                        }
                    } else {
                        foreach ($this->request->data['custom'] as $k => $v) {
                            $invent['Product']['Custom'. $k] = '';
                        }
                    }
                }
            }
            $available_fields = $this->request->data['fields'];
            foreach ($this->request->data['custom'] as $key => $val) {
                $available_fields[$val] = 'Product.Custom'. $key;
            }
            $this->Cookie->write('available_fields', $this->request->data['fields']);
            $this->Cookie->write('available_custom', $this->request->data['custom']);
        } else {
            $available_fields = array('SKU' => 'Product.sku','ProductName' => 'Product.name','Location' => 'Warehouse.name', 'AvailableStock'=>'Inventory.quantity');
        }
       

        $_serialize = 'inventories';
        $_header = array_keys($available_fields);
        $_extract = array_values($available_fields);

        $file_name = "Delivrd_".date('Y-m-d-His')."_inventory.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('inventories', '_serialize', '_header', '_extract'));
    }

    public function uploadcsv() {
        $this->layout = 'mtrd';

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
    }

    public function importcsv($filename = null, $uname = null) {
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
            if($numcols != 4) {
                $msg = "CSV file should have 4 columns, but line ".$line[0]." has ".$numcols." columns";
                $is_error = true;
            }
            break;
        }
        fclose($file);
        
        if(!$is_error) {

            $this->loadModel('Transfer');
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
            $transfer_id = $this->Transfer->id;

            $filedata = $this->Csv->import($content, array('Inventory.sku', 'Inventory.name', 'Inventory.location', 'Inventory.quantity'));
            $numrec = count($filedata) - 1;
            foreach ($filedata as $key => $inventorydata) {
                if($key > 0) {
                    $err = false;
                    if(empty($inventorydata['Inventory']['sku'] || empty($inventorydata['Inventory']['warehouse_id'] || empty($inventorydata['Inventory']['quantity'])))) {
                        $errors[] = "SKU or warehouse number are missing in line ".$key;
                        $err = true;
                    }
            
                    $pid = $this->Inventory->Product->find('first', array('fields' => array('Product.id'),'conditions' => array('Product.sku' => $inventorydata['Inventory']['sku'],'Product.user_id' => $this->Auth->user('id'))));
                    if(empty($pid)) {
                        $errors[] = "SKU ".$inventorydata['Inventory']['sku']." does not exist, line number ".$key;
                        $err = true;
                    }

                    $warhouseid = $this->Inventory->Warehouse->find('list', array('conditions' => array('Warehouse.name' => $inventorydata['Inventory']['location'],'Warehouse.user_id' => $this->Auth->user('id'))));
                    if(empty($warhouseid)) {
                        $errors[] = "Location ".$inventorydata['Inventory']['location']."in line number ".$key." does not exist";
                        $err = true;
                    }

                    if(!$err) {
                        $inv_id = $this->InventoryManager->getInventory($pid['Product']['id'], key($warhouseid), array('Inventory.id','Inventory.quantity'));
                        if($inv_id) {
                            if($this->Inventory->changeCount($inv_id['Inventory']['id'], $inventorydata['Inventory']['quantity'])) {
                                $success++;
                            } else {
                                $danger++;
                                $errors[] = 'Line '.$key.' couldn\'t be updated';
                            }
                        } else {
                            $danger++;
                            $errors[] = 'Line '.$key.' couldn\'t be created';
                        }
                    } else {
                        $danger++;
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
            $msg['total_found'] = $numrec;
            $msg['added'] = $success;
            $msg['updated'] = 0;
            $msg['errors_count'] = $danger;
            $msg['errors'] = $errors;
            $log['response'] = json_encode($msg); 
            $this->Transfer->save($log);
        }

        $this->set(compact('filename', 'uname', 'is_error', 'msg', 'numrec', 'errors', 'success', 'danger'));
    }

    public function downloadsamplefile() {
        $filename = $target_path = WWW_ROOT."sampledata/Delivrd_sample_inventory.csv";
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

    public function saveQuantity() {
        $response['status'] = false;
        $qty = $this->request->data['quantity'];
        $id = $this->request->data['product_id'];
        $inventory_id = $this->request->data['inventory_id'];

        if ($this->request->is('post')) {
            $this->request->data['Inventory']['id'] = $inventory_id;
            $this->request->data['Inventory']['quantity'] = $qty;
            $this->request->data['Inventory']['comments'] = '';
            $current_inv = $this->Inventory->find('first', array(
                'conditions' => array('Inventory.id' => $inventory_id),
                'contain' => array('Product' => array('fields' => array('id', 'name', 'value'))),
                'callbacks' => false
            ));

            if($this->Access->hasInventoryAccess($current_inv)) {
                $delta = $current_inv['Inventory']['quantity'] - $this->request->data['Inventory']['quantity'];
                $sentqty = ($delta > 0) ? abs($delta) : 0;
                $recqty = ($delta < 0) ? abs($delta) : 0;
                $validation = true;
                $data = array(
                    'OrdersLine' => array(
                        'order_id' => 4294967294,
                        'line_number' => 1,
                        'type' => 3,
                        'product_id'  => $current_inv['Inventory']['product_id'],
                        'quantity' => $this->request->data['Inventory']['quantity'],
                        'receivedqty' => $recqty,
                        'damagedqty' => 0,
                        'sentqty' => $sentqty,
                        'unit_price' => $current_inv['Product']['value'],
                        'total_line' => $current_inv['Product']['value'] * abs($delta),
                        'foc' => '',
                        'warehouse_id' => $current_inv['Inventory']['warehouse_id'],
                        'return' => '',
                        'comments' => $this->request->data['Inventory']['comments'],
                        'user_id' => $current_inv['Inventory']['user_id']
                        )
                    );
                $result = $this->Inventory->saveInventory($this->request->data, $data, $validation);
                if($result === true) {
                    $response['class'] = 'btn green';
                    if($qty < $current_inv['Product']['reorder_point'] && $qty > $current_inv['Product']['safety_stock'])
                        $response['class'] = 'btn yellow-crusta';
                    if($qty < $current_inv['Product']['safety_stock'])
                        $response['class'] = 'btn red';
                    $response['status'] = true;
                    $response['message'] = 'Your quantity has been save.';
                    $response['value'] = $qty;
                } else {
                    $response['message'] = 'error';
                    $response['status'] = false;
                }
            } else {
                $response['message'] = 'Have no access';
                $response['status'] = false;
            }
        }
        echo json_encode($response);
        exit;
    }

    public function create() {
        $success ='';
        $errors = array();
        if(!isset($this->request->data['Inventory']['tquantity'])) {
            $this->request->data['Inventory']['tquantity'] = 1;
        }

        $sentqty = 0;
        $recqty = 0;

        $loptions = array('Inventory.' . $this->Inventory->primaryKey => $this->request->data['Inventory']['id']);
        $current_inv = $this->Inventory->find('first', array(
            'conditions' => $loptions,
            'contain' => array('Product' => array('fields' => array('id', 'name', 'safety_stock', 'reorder_point', 'value'))),
            'callbacks' => false
        ));

        if($this->Access->hasInventoryAccess($current_inv)) {
            $availableinv = $current_inv['Inventory']['quantity'];
            $this->request->data['Inventory']['warehouse_id'] = $current_inv['Inventory']['warehouse_id'];
            $sourcesafetystock = $current_inv['Product']['safety_stock'];
            $sourcereorderpoint = $current_inv['Product']['reorder_point'];

            if($this->request->is('ajax')) {
                if($this->request->data['Inventory']['tquantity'] < 0 ) {
                    $errors = 'Transaction quantity must be greater than 0.';
                    $status = false;
                }

                if($availableinv < $this->request->data['Inventory']['tquantity'] && isset($this->request->data['Inventory']['ttype']) && $this->request->data['Inventory']['ttype'] == 'GI') {
                    $errors = 'You are trying to issue a quantity greater than quantity available.';
                    $status = false;
                }

                if(isset($this->request->data['Inventory']['ttype'])) {
                    $ttype = $this->request->data['Inventory']['ttype'];
                    $tquantity = $this->request->data['Inventory']['tquantity'];
                    //if user has locations enabled, we get warehouse from grgi page
                    if($this->Session->read('locationsactive') == 1) {
                        $warehouseid = $this->request->data['Inventory']['warehouse_id'];
                    } else {
                        $warehouseid = $this->Session->read('default_warehouse');
                    }
                } else {
                    // function was called from another function rather than from GRGI page
                    $ttype = $grgi;
                    $tquantity = $quantity;
                    $this->request->data('Inventory.id',$id);
                }
                if($ttype == 'GI') {
                    $direction = -1;
                    $linetype = 5;
                    $sentqty = $tquantity;
                    if(!isset($warehouseid)) {
                        $warehouseid = $this->request->data['Inventory']['warehouse_id_from'];
                    }
                } else if($ttype == 'GR') {
                    $direction = 1;
                    $linetype = 6;
                    $recqty = $tquantity;
                    if(!isset($warehouseid)) {
                        $warehouseid = $this->request->data['Inventory']['warehouse_id_to'];
                    }
                } else {
                    $errors = 'You did not select Issue from Inventory or Receive to Inventory. Please try again.';
                    $status = false;
                }

                $new_inv = $current_inv['Inventory']['quantity'] + $direction * $tquantity;
                $this->request->data('Inventory.quantity',$new_inv);
                if($current_inv['Inventory']['quantity'] == 0) {
                    $current_inv['Inventory']['quantity'] = $this->request->data['Inventory']['tquantity'];
                }

                $data = array(
                    'OrdersLine' => array(
                        'order_id' => 4294967294,
                        'line_number' => 1,
                        'type' => $linetype,
                        'product_id'  => $current_inv['Inventory']['product_id'],
                        'quantity' => $current_inv['Inventory']['quantity'],
                        'receivedqty' => $recqty,
                        'damagedqty' => 0,
                        'sentqty' => $sentqty,
                        'unit_price' => $current_inv['Product']['value'],
                        'total_line' => $current_inv['Product']['value'] * abs($this->request->data['Inventory']['tquantity']),
                        'foc' => '',
                        'warehouse_id' => $warehouseid,
                        'return' => '',
                        'comments' => $this->request->data['Inventory']['comments'],
                        'user_id' => $current_inv['Inventory']['user_id']
                    )
                );
                
                $result = $this->Inventory->saveInventory($this->request->data, $data);
                if($result === true) {
                    if(!isset($grgi)) {
                        $success = 'Inventory quantity update successfully';
                        $inv_data['id'] = $current_inv['Inventory']['id'];
                        $inv_data['quantity'] = $new_inv;
                        $status = true;
                    }
                } else {
                    $success = 'Inventory quantity could not be updated. Please, try again.';
                    $inv_data['id'] = $current_inv['Inventory']['id'];
                    $inv_data['quantity'] = $new_inv;
                    $status = false;
                }
            } else {
                $options = array('conditions' => array('Inventory.' . $this->Inventory->primaryKey => $id));
                $this->request->data = $this->Inventory->find('first', $options);
            }

        }

        $products = $this->Inventory->Product->find('all', array('contain' => array(), 'fields' => array('Product.name', 'Product.imageurl')));
        $warehouses = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.id' => $current_inv['Inventory']['warehouse_id'],'Warehouse.user_id' => $this->Auth->user('id'))));

        $this->set(compact('availableinv','sourcesafetystock','sourcereorderpoint'));
        // Set varriables for use on view ctp file
        $this->set(compact('errors', 'id', 'success', 'status', 'inv_data'));
    }

    public function serial_checkout() {
        $this->layout = false;
        if($this->request->is('post') || $this->request->is('put')) {
            $this->loadModel('Serial');
            $serialdata = $this->Serial->find('first', array('conditions' => array('serialnumber' => $this->request->data['Inventory']['code_scan']), 'recursive' => -1));
            if($serialdata) {
                if($serialdata['Serial']['instock'] == 0) {
                    $response['action'] = 'info';
                    $response['message'] = 'Serial number is out of stock.';
                } else {
                    $options = array('conditions' => array(
                        'Inventory.product_id' => $serialdata['Serial']['product_id'],
                        'Inventory.warehouse_id' => $serialdata['Serial']['warehouse_id'],
                        'Inventory.deleted' => 0
                    ));
                    $inv = $this->Inventory->find('first', $options);
                    if($inv) {
                        $availableinv = $inv['Inventory']['quantity'];

                        $this->request->data['Inventory']['id'] = $inv['Inventory']['id'];
                        $this->request->data['Inventory']['quantity'] = $availableinv - 1;
                        $linetype = 5;
                        $sentqty = 1;
                        $data = array(
                            'OrdersLine' => array(
                                'order_id' => 4294967294,
                                'line_number' => 1,
                                'type' => $linetype,
                                'product_id'  => $inv['Inventory']['product_id'],
                                'quantity' => $inv['Inventory']['quantity'],
                                'receivedqty' => 0,
                                'damagedqty' => 0,
                                'sentqty' => $sentqty,
                                'unit_price' => $inv['Product']['value'],
                                'total_line' => $inv['Product']['value'] * abs(1),
                                'foc' => '',
                                'warehouse_id' => $inv['Inventory']['warehouse_id'],
                                'serial' => $this->request->data['Inventory']['code_scan'],
                                'return' => '',
                                'comments' => '',
                                'user_id' => $this->Auth->user('id')
                            )
                        );
                        $result = $this->Inventory->saveInventory($this->request->data, $data);
                        if($result === true) {
                            $this->Serial->id = $serialdata['Serial']['id'];
                            $this->Serial->saveField('warehouse_id', '');
                            $this->Serial->saveField('instock', 0);
                            $response['action'] = 'success';
                            $response['message'] = 'Inventory quantity deduct successfully.';
                            $response['product'] = $inv['Product']['name'];
                            $response['location'] = $inv['Warehouse']['name'];
                            $response['quantity'] = $this->request->data['Inventory']['quantity'];
                            $response['serial_no'] = $serialdata['Serial']['serialnumber'];
                        } else {
                            $response['action'] = 'error';
                            $response['message'] = 'Inventory quantity could not updated.';
                        }
                    } else {
                        $response['action'] = 'error';
                        $response['message'] = 'Inventory not found, quantity could not updated.';
                    }
                }
            } else {
                $response['action'] = 'error';
                $response['message'] = 'Serial not found';
            }
            echo json_encode($response);
            exit;
        }
    }

    public function issueTransfer() {
        $this->loadModel('Serial');
        $this->loadModel('OrdersLine');
        if($this->request->is('ajax')) {
            $loptions = array('conditions' => array('Inventory.' . $this->Inventory->primaryKey => $this->request->data['Inventory']['id']));
            $inv = $this->Inventory->find('first', $loptions);
            $availableinv = $inv['Inventory']['quantity'];

            $serialdata = $this->Serial->find('first', array('conditions' => array('serialnumber' => $this->request->data['Inventory']['serial_no']), 'recursive' => -1)); //, 'user_id' => $this->Auth->user('id')

            if($serialdata['Serial']['instock'] == 0) {
                $success = 'Serial number is out of stock.';
                $current_inv['qty'] = $availableinv;
                $current_inv['id'] = $this->request->data['Inventory']['id'];
                $status = false;
            } else {
                $this->request->data['Inventory']['quantity'] = $availableinv - 1;
                  $linetype = 5;
                  $sentqty = 1;
                  $data = array(
                    'OrdersLine' => array(
                        'order_id' => 4294967294,
                        'line_number' => 1,
                        'type' => $linetype,
                        'product_id'  => $inv['Inventory']['product_id'],
                        'quantity' => $inv['Inventory']['quantity'],
                        'receivedqty' => 0,
                        'damagedqty' => 0,
                        'sentqty' => $sentqty,
                        'unit_price' => $inv['Product']['value'],
                        'total_line' => $inv['Product']['value'] * abs(1),
                        'foc' => '',
                        'warehouse_id' => $inv['Inventory']['warehouse_id'],
                        'serial' => $this->request->data['Inventory']['serial_no'],
                        'return' => '',
                        'comments' => $this->request->data['Inventory']['comments'],
                        'user_id' => $this->Auth->user('id')
                    )
                  );
                $result = $this->Inventory->saveInventory($this->request->data, $data);
                if($result === true) {
                    $this->Serial->id = $serialdata['Serial']['id'];
                    $this->Serial->saveField('warehouse_id', '');
                    $this->Serial->saveField('instock', 0);
                    $success = 'Inventory quantity deduct successfully.';
                    $current_inv['qty'] = $availableinv - 1;
                    $current_inv['id'] = $this->request->data['Inventory']['id'];
                    $status = true;
                } else {
                    $success = 'Inventory quantity could not update successfully.';
                    $current_inv['qty'] = $availableinv;
                    $current_inv['id'] = $this->request->data['Inventory']['id'];
                    $status = false;
                }
            }
            $this->set(compact('errors', 'id', 'success', 'status', 'current_inv'));
        }
    }

    public function serialTransfer() {
        $this->loadModel('Serial');
        $this->loadModel('OrdersLine');
        $this->loadModel('Warehouse');
        $validation = true;
        if($this->request->is('ajax')) {
            $loptions = array('conditions' => array('Inventory.' . $this->Inventory->primaryKey => $this->request->data['Inventory']['id']), 'contain' => array('Product'));
            $sourceinv = $this->Inventory->find('first', $loptions);
            $destinv = $this->Inventory->find('first',array('conditions' => array('Inventory.product_id'  => $sourceinv['Inventory']['product_id'],'Inventory.warehouse_id'=> $this->request->data['Inventory']['warehouse_id']), 'contain' => array('Product')));

            $serialdata = $this->Serial->find('first', array('conditions' => array('serialnumber' => $this->request->data['Inventory']['serial_no']), 'recursive' => -1)); //, 'user_id' => $this->Auth->user('id')
            $availableinv = $sourceinv['Inventory']['quantity'];
        
                if($sourceinv) {
                    $inv['Inventory']['id'] = $sourceinv['Inventory']['id'];
                    $inv['Inventory']['quantity'] = $sourceinv['Inventory']['quantity'] - 1;
                    $linetype = 5;
                    $sentqty = 1;
                    $data = array(
                    'OrdersLine' => array(
                        'order_id' => 4294967294,
                        'line_number' => 1,
                        'type' => $linetype,
                        'product_id'  => $sourceinv['Inventory']['product_id'],
                        'quantity' => $sourceinv['Inventory']['quantity'],
                        'receivedqty' => 0,
                        'damagedqty' => 0,
                        'sentqty' => $sentqty,
                        'unit_price' => $sourceinv['Product']['value'],
                        'total_line' => $sourceinv['Product']['value'] * abs(1),
                        'foc' => '',
                        'warehouse_id' => $sourceinv['Inventory']['warehouse_id'],
                        'serial_id' => $serialdata['Serial']['id'],
                        'return' => '',
                        'comments' => $this->request->data['Inventory']['comments'],
                        'user_id' => $this->Auth->user('id')
                    )
                    );
                    $result = $this->Inventory->saveInventory($inv, $data, $validation);
                }         

                if($destinv) {
                    $destInv['Inventory']['id'] = $destinv['Inventory']['id'];
                    $destInv['Inventory']['quantity'] = $destinv['Inventory']['quantity'] + 1;
                    $linetype = 6;
                    $recqty = 1;
                    $data = array(
                        'OrdersLine' => array(
                            'order_id' => 4294967294,
                            'line_number' => 1,
                            'type' => $linetype,
                            'product_id'  => $destinv['Inventory']['product_id'],
                            'quantity' => $destinv['Inventory']['quantity'],
                            'receivedqty' => $recqty,
                            'damagedqty' => 0,
                            'sentqty' => 0,
                            'unit_price' => $destinv['Product']['value'],
                            'total_line' => $destinv['Product']['value'] * abs(1),
                            'foc' => '',
                            'warehouse_id' => $this->request->data['Inventory']['warehouse_id'],
                            'serial_id' => $serialdata['Serial']['id'],
                            'return' => '',
                            'comments' => $this->request->data['Inventory']['comments'],
                            'user_id' => $this->Auth->user('id')
                        )
                    );

                    $result = $this->Inventory->saveInventory($destInv, $data, $validation);
                }
                else {
                    $data = array(
                    'Inventory' => array(
                        'product_id' => $sourceinv['Inventory']['product_id'],
                        'user_id' => $this->Auth->user('id'),
                        'dcop_user_id' => $this->Auth->user('id'),
                        'warehouse_id' => $this->request->data['Inventory']['warehouse_id'],
                        'quantity' => 1
                        )
                    );
                    $ds = $this->Inventory->getdatasource();
                    $ds->begin();
                    $this->Inventory->create();
                    if($invcreated = $this->Inventory->save($data)) {
                        $linetype = 6;
                        $recqty = 1;
                        $data = array(
                        'OrdersLine' => array(
                            'order_id' => 4294967294,
                            'line_number' => 1,
                            'type' => $linetype,
                            'product_id'  => $invcreated['Inventory']['product_id'],
                            'quantity' => $invcreated['Inventory']['quantity'],
                            'receivedqty' => $recqty,
                            'damagedqty' => 0,
                            'sentqty' => 0,
                            'unit_price' => $sourceinv['Product']['value'],
                            'total_line' => $sourceinv['Product']['value'] * abs(1),
                            'foc' => '',
                            'warehouse_id' => $this->request->data['Inventory']['warehouse_id'],
                            'serial_id' => $serialdata['Serial']['id'],
                            'return' => '',
                            'comments' => $this->request->data['Inventory']['comments'],
                            'user_id' => $this->Auth->user('id')
                            )
                        );

                        if($this->OrdersLine->save($data)) {
                    
                        } else {
                            $errorFlag['OrdersLine'] = 'Error in model OrdersLine';
                        }
                    } else {
                        $errorFlag['Inventory'] = "Error in model Inventory";
                    }

                    if(empty($errorFlag)){
                        $ds->commit();
                        $result = true;
                    } else {
                        $ds->rollback();
                        $result = false;
                    }
                    
                }

                if($result === true) {
                    $this->Serial->id = $serialdata['Serial']['id'];
                    $this->Serial->saveField('warehouse_id', $this->request->data['Inventory']['warehouse_id']);
                    
                    $success = 'Inventory quantity update successfully.';
                    $current_inv['qty'] = ($this->request->data['Inventory']['warehouse_id'] == $sourceinv['Inventory']['warehouse_id']) ? $availableinv + 1 : $availableinv - 1;
                    $current_inv['id'] = $this->request->data['Inventory']['id'];
                    $current_inv['warehouse'] = $this->request->data['Inventory']['warehouse_id'];
                    $status = true;
                } else {
                    $success = 'Inventory quantity could not update successfully.';
                    $current_inv['qty'] = $availableinv;
                    $current_inv['id'] = $this->request->data['Inventory']['id'];
                    $status = false;
                }

            $this->set(compact('errors', 'id', 'success', 'status', 'current_inv'));
        }
    }

    function invenotry_alert_email_header($mailtext){
        $html = $mailtext;
        $html .='<table class="table table-striped" cellspacing="0" cellpadding="10" border=0>';
        $html .='<tr style="background-color:#3598dc; color:#fff;">';
        $html .='<th>#</th>';
        $html .='<th>Product Name</th>';
        $html .='<th>SKU</th>';
        $html .='<th>Inventory Quantity</th>';
        $html .='<th>Location</th>';
        $html .='<th>Reorder Point</th>';
        $html .='<th>Safety Stock</th>';
        $html .='</tr>';
        return $html;
    }

    function invenotry_alert_email_content($key, $product){
        $trstyle = ($key%2 ==0) ? '' : 'style = "background-color: #f9f9f9; "' ;
        $html ='<tr '.$trstyle.'>';
        $html .='<td style="border-bottom:1pt solid #e2dcdc;">'.++$key . '</td>';
        $html .='<td style="border-bottom:1pt solid #e2dcdc;">'.$product['Product']['name'].'</td>';
        $html .='<td style="border-bottom:1pt solid #e2dcdc;">'.$product['Product']['sku'].'</td>';
        $html .='<td style="border-bottom:1pt solid #e2dcdc;">'.$product['Inventory']['quantity'].'</td>';
        $html .='<td style="border-bottom:1pt solid #e2dcdc;">';
        $html .= h($product['Warehouse']['name']);
        $html .='</td>';
        $html .='<td style="border-bottom:1pt solid #e2dcdc;">'.$product[0]['reorder_point'].'</td>';
        $html .='<td style="border-bottom:1pt solid #e2dcdc;">'.round($product[0]['safety_stock']).'</td>';
        $html .='</tr>';
        return $html;
    }

    /**
     * Use this method for crontab inventory alert
     * ex: Run Script each day at 8:00
     * 0 8 * * *    curl http://delivrdapp.com/app/inventories/cron_invenotry_alert/eKdo9324823kRsd8ww32 > /dev/null 2>&1
     *
     */
    public function cron_invenotry_alert($pswd = null) {
        if($pswd == 'eKdo9324823kRsd8ww32') {
            $this->loadModel('User');
            $this->User->recursive = -1;

            $conditions['User.inventoryalert !='] = 1;
            //'User.id IN' =>['59f08d49-a320-4a3f-ac82-7afad6d0b2e3', '59f8e20a-b2d4-4a4a-a127-0724d6d0b2e3']

            $roles = Configure::read('InventoryAlerts.role');
            $role_cond = [];
            if($roles) {
                $roles = explode('|', $roles);
                if(!in_array('all', $roles)) {
                    foreach ($roles as $role) {
                        if($role == 'admin') {
                            $role_cond['User.is_admin'] = 1;
                        } else {
                            $role_cond['User.role'] = $role;
                        }
                    }
                }
            }
            
            if(count($role_cond) > 1) {
                $conditions['OR'] = $role_cond;
            } else {
                $conditions = array_merge($conditions, $role_cond);
            }

            if($days = Configure::read('InventoryAlerts.last_login')) {
                $conditions['User.last_login >='] = date('Y-m-d', time() - $days*3600*24);
            }
            
            $users = $this->User->find('all', [
                'conditions' => $conditions
            ]);
            
            $res = [];
            foreach ($users as $user) {
                $res[] = $this->send_invenotry_alert($user['User']);
            }
            exit;
        } else {
            die('Nothing do here');
        }
    }

    public function invenotry_alert()
    {
        if($this->Session->read('inventoryalert') != 1) {

            $response = $this->send_invenotry_alert($this->Auth->user());

            switch ($response) {
                case ($response['status'] == 'sent' && !empty($response['splitEmail'])) :
                    $message = 'Email send successfully for suppliers '.$response['splitEmail'];
                    $msgcolor = 'admin/success';
                    break;
                case ($response['status'] == 'sent') :
                    $message = 'Email send successfully';
                    $msgcolor = 'admin/success';
                    break;
                case ($response['status'] == 'error' && !empty($response['message'])) :
                    $message = $response['message'];
                    $msgcolor = 'admin/warning';
                    break;
                case ($response['status'] == 'queued') :
                    $message = 'Email is in queue.';
                    $msgcolor = 'admin/info';
                    break;
                case ($response['status'] == 'error') :
                    $message = 'Unable to send Inventory Alert email.';
                    $msgcolor = 'admin/error';
                    break;
                case ($response['status'] == 'warning') :
                    $message = 'Not any product whose inventory quantity greater than reorder point';
                    $msgcolor = 'admin/warning';
                    break;
            }

            $this->Session->setFlash(__($message), $msgcolor, array());
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__("Don't send any alert. Please set Inventory alert from settings."), 'admin/danger', array());
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function send_invenotry_alert($user) {
        App::uses('CakeEmail', 'Network/Email');
        $this->loadModel('User');
        $this->loadModel('Product');

        $email = new CakeEmail('mandrill');
        #$email = new CakeEmail('default');
        $email->emailFormat('html');
        //$email->bcc('fordenis@ukr.net');
        
        $mailResponse = array();

        $subject = ($user['inventoryalert'] == 2) ? 'Low Inventory Alerts for '.$user['company'] : 'Inventory Report for '.$user['company'];
        $emailText = ($user['inventoryalert'] == 2) ? 'Your products inventory falling short of reorder point/safety stock levels are listed below<br><br>' : 'The following list contains updated inventory for all products in '.$user['company'].'<br><br>';

        $conditions['Warehouse.status'] = 'active';
        $conditions['Product.user_id'] = $user['id'];
        $conditions['Product.deleted'] = 0;
        $conditions['Product.status_id NOT IN'] = [12, 13];
        if($user['inventoryalert'] == 2) {
            $conditions['OR'] = array('Inventory.quantity <= IF(Invalert.reorder_point, Invalert.reorder_point, Product.reorder_point)', 'Inventory.quantity <= IF(Invalert.safety_stock, Invalert.safety_stock, Product.safety_stock)');
        }

        $products = $this->Inventory->find('all',array(
            'fields' => array(
                'Inventory.quantity',
                'Inventory.user_id',
                'Product.name',
                'Product.id',
                'Product.sku',
                'Invalert.safety_stock',
                'Product.safety_stock',
                'IF(Invalert.reorder_point, Invalert.reorder_point, Product.reorder_point) as reorder_point',
                'IF(Invalert.safety_stock, Invalert.safety_stock, Product.safety_stock) as safety_stock',
                'Warehouse.name'
            ),
            'contain' => array(
                'Product',
                'Warehouse',
                'Product.Productsupplier',
                'Product.Productsupplier.Supplier' => array('fields' => array('Supplier.id','Supplier.email','Supplier.name'))
            ),
            'conditions' => $conditions,
            'joins' => array(
                array('table' => 'invalerts',
                    'alias' => 'Invalert',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Invalert.warehouse_id = Inventory.warehouse_id AND Invalert.product_id = Inventory.product_id',
                    )
                ),
            )
        ));

        if(empty($products)) {
            $response['status'] = 'warning';
            return $response;
            #$this->Session->setFlash(__(''), 'admin/danger', array());
            #return $this->redirect(array('action' => 'index'));
        }

        $productsSuppliers = array();
        $html = '';
        // Group products suppliers wise to send alert to supplirs
        if(!empty($products)) {
            if($user['firstname'] && $user['lastname']) {
                $username = $user['firstname'] .' '. $user['lastname'];
            } else {
                $username = $user['username'];
            }
            $html = '<h4>Dear '.$username.'</h4>';
            $html .= $this->invenotry_alert_email_header($emailText);
            foreach($products as $key => $product){
                $html .= $this->invenotry_alert_email_content($key, $product);
                if(!empty($product['Product']['Productsupplier'])) {
                    foreach($product['Product']['Productsupplier'] as $Supplier){
                        unset($product['Product']['Productsupplier']);
                        $productsSuppliers[$Supplier['supplier_id']][] = array("supplier" =>$Supplier['Supplier'], "product" => $product);
                    }
                }
            }

            $html .='</table>';
            
            // send alert to user
            $email->to($user['email']);
            //$email->to('testemail@email.com'); // Uncomment this for testing pursone and set testing email address here
            $email->subject($subject);
            $mailResponse[] = $resp = $email->send($html);
            
            $html = '';
            foreach($productsSuppliers as $productsSupplier){

                $html = '<h4>Dear '.$productsSupplier[0]['supplier']['name'].'</h4>';
                $html .= $this->invenotry_alert_email_header($emailText);
                foreach ($productsSupplier as $key => $product) {
                    $html .= $this->invenotry_alert_email_content($key, $product['product']);
                }

                $html .='</table>';
                // Send alert to supplier
                $mailids = array();
                $emailarray = array();
                if(!empty($productsSupplier[0]['supplier']['email'])) {
                    if(strpos($productsSupplier[0]['supplier']['email'], ',') !== false) {
                        $emailarray = explode(',', $productsSupplier[0]['supplier']['email']);
                        $mailids = array_merge($mailids, $emailarray);
                    } else {
                        $emailarray[] = $productsSupplier[0]['supplier']['email'];
                        $mailids = array_merge($mailids, $emailarray);
                    }
                }
                if($mailids) {
                    $mailids = $mailids[0];
                }
                $email->to($mailids);
                //$email->to('testemail@email.com'); // Uncomment this for testing pursone and set testing email address here
                $email->subject($subject);
                $mailResponse[] = $email->send($html);
                $html = '';
            }
        }
        $emailid = array();
        $status = '';

        foreach($mailResponse as $mailparams) {
            if(isset($mailparams['status']) && $mailparams['status'] == 'error') {
                $response['status'] = $mailparams['status'];
                $response['message'] = $mailparams['message'];
            } else {
                foreach($mailparams as $email) {
                    if($email['status'] != 'error') {
                        $response['status'] = $email['status'];
                        if($status != 'error')
                        $emailid[] = $email['email'];
                    } else {
                        $response['status'] = $email['status'];
                        $response['message'] = $email['message'];
                    }
                }
            }
        }

        $response['splitEmail'] = implode(', ',$emailid);
        return $response;
    }

    /**
     * transactions_history method
     *
     * @throws NotFoundException
     * @param int $product_id
     * @return void
     */
    public function transactions_history($product_id = null) {
        if($product_id) {
            $product = $this->Product->find('first', array('conditions' => array('Product.id' => $product_id), 'callbacks' => false));
            
            if(empty($product)) {
                throw new NotFoundException(__('Prduct not found'));
            }
        } else {
            $product['Product']['id'] = 0;
            $product['Product']['user_id'] = $product['User']['id'] = $this->Auth->user('id');
            $product['User']['locationsactive'] = $this->Auth->user('locationsactive');
            $product['Product']['name'] = 'All products';
            $product['Product']['sku'] = '';
        }

        $warehouses = $this->Access->getLocationsWithInactive('Inventory', false, 'r', $product['User']['id']);
        //$warehouses2 = $this->Access->getLocations('Inventory', false, 'r', $product['User']['id']);

        if($product['User']['id'] == $this->Auth->user('id')) {
            $warehouses = array_shift($warehouses);
            $warehouses_access = array_keys($warehouses);
            $warehouses_access = array_fill_keys($warehouses_access, 'rw');
        } else {
            $warehouses_access = $this->Access->locationList('Inventory', false, 'r', $product['User']['id']);
        }

        // Get cum_qty for each location
        $cum_qty = [];
        if($product_id) {
            $this->loadModel('OrdersLine');
            foreach ($warehouses as $warehouse_id => $value) {
                $line_qty = $this->OrdersLine->find('first', [
                    'conditions' => ['OrdersLine.product_id' => $product_id, 'OrdersLine.warehouse_id' => $warehouse_id],
                    'contain' => false,
                    'fields' => ['(SUM(receivedqty) - SUM(sentqty)) as cum_qty']
                ]);

                $inv_qty = $this->Inventory->find('first', [
                    'conditions' => ['Inventory.product_id' => $product_id, 'Inventory.warehouse_id' => $warehouse_id, 'Inventory.deleted' => 0],
                    'contain' => false
                ]);
                $cum_qty[$warehouse_id] = [
                    'cum_qty' => $line_qty[0]['cum_qty'],
                    'inv_qty' => $inv_qty['Inventory']['quantity']
                ];

            }
        }

        $productlinesdata = [];

        $limit = $this->Auth->user('list_limit');//10;
        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );

        $this->set(compact('product', 'warehouses', 'warehouses_access', 'productlinesdata', 'cum_qty', 'limit', 'options'));
    }


    /**
     * align_qty method
     *
     * @throws NotFoundException
     * @param int $product_id
     * @param int $warehouse_id
     * @return void
     */
    public function align_qty($product_id, $warehouse_id) {
        $this->layout = false;

        $this->loadModel('OrdersLine');
        $this->loadModel('Warehouse');

        $this->Product->recursive = 0;
        $this->Warehouse->recursive = -1;

        $product = $this->Product->find('first', array('conditions' => array('Product.id' => $product_id), 'callbacks' => false));
        $warehouses = $this->Access->getLocationsWithInactive('Inventory', false, 'r', $product['User']['id']);
        //$warehouses2 = $this->Access->getLocations('Inventory', false, 'r', $product['User']['id']);

        if($product['User']['id'] == $this->Auth->user('id')) {
            $warehouses = array_shift($warehouses);
            $warehouses_access = array_keys($warehouses);
            $warehouses_access = array_fill_keys($warehouses_access, 'rw');
        } else {
            $warehouses_access = $this->Access->locationList('Inventory', false, 'r', $product['User']['id']);
        }

        // Get cum_qty for each location
        $cum_qty = [];
        if($product_id) {
            $this->loadModel('OrdersLine');
            foreach ($warehouses as $warehouse_id => $value) {
                $line_qty = $this->OrdersLine->find('first', [
                    'conditions' => ['OrdersLine.product_id' => $product_id, 'OrdersLine.warehouse_id' => $warehouse_id],
                    'contain' => false,
                    'fields' => ['(SUM(receivedqty) - SUM(sentqty)) as cum_qty']
                ]);

                $inv_qty = $this->Inventory->find('first', [
                    'conditions' => ['Inventory.product_id' => $product_id, 'Inventory.warehouse_id' => $warehouse_id, 'Inventory.deleted' => 0],
                    'contain' => false
                ]);
                $cum_qty[$warehouse_id] = [
                    'cum_qty' => $line_qty[0]['cum_qty'],
                    'inv_qty' => $inv_qty['Inventory']['quantity']
                ];

            }
        }

        
        $this->set(compact('warehouses', 'product', 'cum_qty'));
    }

    public function align($product_id, $warehouse_id) {
        if($this->request->is('post')) {
            $this->loadModel('OrdersLine');
            $line_qty = $this->OrdersLine->find('first', [
                'conditions' => ['OrdersLine.product_id' => $product_id, 'OrdersLine.warehouse_id' => $warehouse_id],
                'contain' => false,
                'fields' => ['(SUM(receivedqty) - SUM(sentqty)) as cum_qty']
            ]);

            $inv_qty = $this->Inventory->find('first', [
                'conditions' => ['Inventory.product_id' => $product_id, 'Inventory.warehouse_id' => $warehouse_id, 'Inventory.deleted' => 0],
                'contain' => false
            ]);

            $dif_qty = abs($line_qty[0]['cum_qty'] - $inv_qty['Inventory']['quantity']);
            if($dif_qty > 0) {
                $inv_qty['Inventory']['quantity'] = $line_qty[0]['cum_qty'];
                $this->Inventory->id = $inv_qty['Inventory']['id'];
                $this->Inventory->save($inv_qty);

                $order_line = [
                    'order_id' => 999999,
                    'line_number' => 1,
                    'type' => 99,
                    'return' => 0,
                    'product_id' => $product_id,
                    'warehouse_id' => $warehouse_id,
                    'quantity' => 0,
                    'receivedqty' => 0,
                    'damagedqty' => 0,
                    'sentqty' => 0,
                    'unit_price' => 0,
                    'total_line' => 0,
                    'comments' => 'Inventory quantity was offset by '. $dif_qty,
                    'receivenotes' => 'Inventory quantity was offset by '. $dif_qty,
                    'user_id' => $this->Auth->user('id'),
                    'dcop_user_id' => $this->Auth->user('id')
                ];
                $this->OrdersLine->save($order_line);

                $response['action'] = 'success';
                $response['msg'] = 'Inventory quantity was offset by '. $dif_qty;
            } else {
                $response['action'] = 'info';
                $response['msg'] = 'Inventory quantity not need in correction';
            }
        } else {
            $response['action'] = 'error';
            $response['msg'] = 'Error, please contact admin';
        }



        echo json_encode($response);
        exit;
    }


    /**
     * tx_history method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function tx_history($pid = null) {
        $this->loadModel('OrdersLine');
        $helper = new AdminHelper(new View());

        $limit = $this->Auth->user('list_limit');//10;
        $conditions = array();
        if($pid) {
            $conditions['OrdersLine.product_id'] =  $pid;
            $this->Product->recursive = -1;
            $product = $this->Product->find('first', array('conditions' => array('Product.id' => $pid), 'fields' => array('Product.user_id')));
            if($product['Product']['user_id'] != $this->Auth->user('id')) {
                $warehouses_access = $this->Access->locationList('Inventory', false, 'r', $product['Product']['user_id']);
                $conditions['OrdersLine.warehouse_id IN'] = array_keys($warehouses_access);
            }
        } else {
            $warehouses_access = $this->Access->locationList('Inventory');
            if($warehouses_access) {
                $conditions['OR']['OrdersLine.warehouse_id IN'] = array_keys($warehouses_access);
                $conditions['OR']['OrdersLine.user_id'] = $this->Auth->user('id');
            } else {
                $conditions['OrdersLine.user_id'] = $this->Auth->user('id');
            }
        }

        if(!empty($this->request->params['named']['limit'])) {
            $limit = $this->request->params['named']['limit'];
        }
        
        if(!empty($this->request->params['named']['location'])) {
            $conditions['OrdersLine.warehouse_id'] = $this->request->params['named']['location'];
        }

        if(!empty($this->request->params['named']['q'])) {
            $conditions['OrdersLine.comments LIKE'] = '%'. $this->request->params['named']['q'] .'%';
        }

        $this->paginate = array(
            'conditions' => $conditions,
            'contain' => array('Order', 'Warehouse', 'Product', 'DcopUser', 'User'),
            'fields' => array(
                'OrdersLine.id',
                'OrdersLine.order_id',
                'OrdersLine.quantity',
                'OrdersLine.receivedqty',
                'OrdersLine.sentqty',
                'OrdersLine.damagedqty',
                'OrdersLine.unit_price',
                'OrdersLine.total_line',
                'OrdersLine.comments',
                'OrdersLine.modified',
                'OrdersLine.type',
                'OrdersLine.return',
                'Order.ordertype_id',
                'Warehouse.name',
                'Product.name',
                'Product.sku',
                'Product.safety_stock',
                'Product.reorder_point',
                'DcopUser.username',
                'User.username'
            ),
            'limit' => $limit,
            'order' => array('OrdersLine.modified' => 'ASC'),
        );
        $productlines = $this->paginate('OrdersLine');

        $count_total = $this->OrdersLine->find('count', array('conditions' => $conditions, 'contain' => false));

        $cum_qty = 0;
        if(!empty($this->request->params['named']['page']) && $this->request->params['named']['page'] > 1 && empty($this->request->params['named']['q'])) {
            $this->OrdersLine->recursive = -1;
            $cum_gty_res = $this->OrdersLine->find('all', [
                'fields' => array('(IFNULL(OrdersLine.receivedqty, 0) - IFNULL(OrdersLine.sentqty, 0) - IFNULL(OrdersLine.damagedqty, 0)) AS ctotal'),
                'conditions' => $conditions,
                'contain' => false,
                'limit' => $limit * ($this->request->params['named']['page'] - 1),
                'order' => array('OrdersLine.modified' => 'ASC'),
                'callbacks' => false
            ]);
            foreach ($cum_gty_res as $value) {
                $cum_qty = $cum_qty + $value[0]['ctotal'];
            }
        }

        if(empty($productlines)) {
            $productlinesdata = [];
        }

        $safety_stock = (isset($productlines[0]['Product']['safety_stock']) ? intval($productlines[0]['Product']['safety_stock']) : 0);
        $reorder_point = (isset($productlines[0]['Product']['reorder_point']) ? intval($productlines[0]['Product']['reorder_point']) : 0);

        $chartd1 = [];
        $chartd2 = [];
        $chartd3 = [];

        foreach($productlines as $x => $productline) {
            $productlinesdata[$x]['warehouse_name'] = $productline['Warehouse']['name'];
            $productlinesdata[$x]['product_sku'] = $productline['Product']['sku'];
            $productlinesdata[$x]['product_name'] = $productline['Product']['name'];
            $productlinesdata[$x]['order_id'] = abs($productline['OrdersLine']['order_id']);
            $productlinesdata[$x]['comments'] = $productline['OrdersLine']['comments'];
            $productlinesdata[$x]['id'] = $productline['OrdersLine']['id'];
            $productlinesdata[$x]['creator'] = (isset($productline['DcopUser']['username'])?$productline['DcopUser']['username']:$productline['User']['username']);
            $productlinesdata[$x]['date'] = $helper->localTime("%Y-%m-%d %H:%M", strtotime($productline['OrdersLine']['modified']));
            #$productlinesdata[$x]['date'] = $productline['OrdersLine']['modified'];
            #$productline['OrdersLine']['created'];
            $productlinesdata[$x]['quantity'] = $productline['OrdersLine']['quantity'];
            $productlinesdata[$x]['type'] = $productline['Order']['ordertype_id'];

            if($productline['Order']['ordertype_id'] == 2 || $productline['OrdersLine']['type'] == 6 || $productline['Order']['ordertype_id'] == 1 || $productline['OrdersLine']['type'] == 5)
            {
                // Receive from supplier
                if($productline['Order']['ordertype_id'] == 1 || $productline['OrdersLine']['type'] == 5) {
                    $factor = -1;
                    $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? "-".($productline['OrdersLine']['sentqty'] +  $productline['OrdersLine']['damagedqty'])* $factor : "+".($productline['OrdersLine']['receivedqty'] +  $productline['OrdersLine']['damagedqty'])* $factor);
                    $productlinesdata[$x]['order_id'] = $productline['OrdersLine']['order_id'];
                    $productlinesdata[$x]['tname'] = ($productline['OrdersLine']['type'] == 2 ? "Replns. Order" : ($productline['OrdersLine']['type'] == 6 ? "Receive To Inventory" : ($productline['OrdersLine']['type'] == 5 ? "Issue From Inventory" : ($productline['OrdersLine']['type'] == 1 ? "Sales Order" : ""))));
                    $productlinesdata[$x]['ticon'] = ($productline['OrdersLine']['type'] == 2 ? "fa-truck" : ($productline['OrdersLine']['type'] == 6 ? "fa-arrow-left" : ($productline['OrdersLine']['type'] == 5 ? "fa-arrow-right" : ($productline['OrdersLine']['type'] == 1 ? "fa-truck" : ""))));
                    $cum_qty = $cum_qty + $productlinesdata[$x]['tquantity'];
                    $chartd1[] = [$x, ceil($cum_qty), 'test'];
                    $chartd2[] = [$x, $safety_stock];
                    $chartd3[] = [$x, $reorder_point];
                    $productlinesdata[$x]['cum_qty'] = $cum_qty;
                    $productlinesdata[$x]['unit_price'] = $productline['OrdersLine']['unit_price'];
                    $productlinesdata[$x]['total_line'] = $productline['OrdersLine']['total_line'] * $factor * -1;
                    if($productline['OrdersLine']['receivedqty'] > 0) {
                        $productlinesdata[$x]['product_name'] = $productline['Product']['name'];
                        $productlinesdata[$x]['order_id'] = $productline['OrdersLine']['order_id'];
                        //$productlinesdata[$x]['date'] = $helper->localTime("%d-%m-%y", strtotime($productline['OrdersLine']['modified']));
                        $productline['OrdersLine']['created'];
                        $productlinesdata[$x]['quantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? $productline['OrdersLine']['sentqty'] : $productline['OrdersLine']['receivedqty']);
                        $factor = 1;
                        $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? "+".($productline['OrdersLine']['receivedqty'] * $factor) : "-".($productline['OrdersLine']['sentqty'] * $factor));
                        $productlinesdata[$x]['tname'] =  'Return From Customer';
                    }
                }
                if($productline['OrdersLine']['return'] == false) {
                    //factor is to determine if the transaction qty is in + or -
                    $factor = 1;
                    $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? "-".($productline['OrdersLine']['sentqty'] +  $productline['OrdersLine']['damagedqty'])* $factor : "+".($productline['OrdersLine']['receivedqty'] +  $productline['OrdersLine']['damagedqty'])* $factor);
                    $productlinesdata[$x]['quantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? $productline['OrdersLine']['sentqty'] : $productline['OrdersLine']['receivedqty']);
                    $productlinesdata[$x]['tname'] = ($productline['OrdersLine']['type'] == 2 ? "Replns. Order" : ($productline['OrdersLine']['type'] == 6 ? "Receive To Inventory" : ($productline['OrdersLine']['type'] == 5 ? "Issue From Inventory" : ($productline['OrdersLine']['type'] == 1 ? "Sales Order" : ""))));
                    $productlinesdata[$x]['ticon'] = ($productline['OrdersLine']['type'] == 2 ? "fa-truck" : ($productline['OrdersLine']['type'] == 6 ? "fa-arrow-left" : ($productline['OrdersLine']['type'] == 5 ? "fa-arrow-right" : ($productline['OrdersLine']['type'] == 1 ? "fa-truck" : ""))));
                    $cum_qty = $cum_qty + $productlinesdata[$x]['tquantity'];
                    $chartd1[] = [$x, ceil($cum_qty), 'test'];
                    $chartd2[] = [$x, $safety_stock];
                    $chartd3[] = [$x, $reorder_point];
                    $productlinesdata[$x]['cum_qty'] = $cum_qty;
                    $productlinesdata[$x]['unit_price'] = $productline['OrdersLine']['unit_price'];
                    $productlinesdata[$x]['total_line'] = $productline['OrdersLine']['total_line'] * $factor ;
                } else {
                    // A return to supplier
                    $factor = -1;
                    $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? "-".($productline['OrdersLine']['sentqty'] +  $productline['OrdersLine']['damagedqty'])* $factor : "+".($productline['OrdersLine']['receivedqty'] +  $productline['OrdersLine']['damagedqty'])* $factor);
                    $productlinesdata[$x]['tname'] =  'Return To Supplier';
                    $cum_qty = $cum_qty + $productlinesdata[$x]['tquantity'];
                }
            }
            //Inventory count lines
            if($productline['OrdersLine']['type'] == 3) {
                if($productline['OrdersLine']['sentqty'] > 0) {
                    $factor = -1;
                    $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? ($productline['OrdersLine']['sentqty'] +  $productline['OrdersLine']['damagedqty'])* $factor : "+".($productline['OrdersLine']['receivedqty'] +  $productline['OrdersLine']['damagedqty'])* $factor);
                    $productlinesdata[$x]['tname'] =  "Inventory Count";
                    $productlinesdata[$x]['ticon'] =  "fa-barcode";
                    $productlinesdata[$x]['quantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? $productline['OrdersLine']['sentqty'] : $productline['OrdersLine']['receivedqty']);
                    $cum_qty = $cum_qty + $productlinesdata[$x]['tquantity'];
                    $chartd1[] = [$x, ceil($cum_qty), 'test'];
                    $chartd2[] = [$x, $safety_stock];
                    $chartd3[] = [$x, $reorder_point];
                    $productlinesdata[$x]['cum_qty'] = $cum_qty;
                    $productlinesdata[$x]['unit_price'] = $productline['OrdersLine']['unit_price'];
                    $productlinesdata[$x]['total_line'] = $productline['OrdersLine']['total_line'] * $factor * -1;
                } else {
                    $factor = 1;
                    $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? "-".($productline['OrdersLine']['sentqty'] +  $productline['OrdersLine']['damagedqty'])* $factor : "+".($productline['OrdersLine']['receivedqty'] +  $productline['OrdersLine']['damagedqty'])* $factor);
                    $productlinesdata[$x]['tname'] =  "Inventory Count";
                    $productlinesdata[$x]['ticon'] =  "fa-barcode";
                    $cum_qty = $cum_qty + $productlinesdata[$x]['tquantity'];
                    $chartd1[] = [$x, ceil($cum_qty), 'test'];
                    $chartd2[] = [$x, $safety_stock];
                    $chartd3[] = [$x, $reorder_point];
                    $productlinesdata[$x]['cum_qty'] = $cum_qty;
                    $productlinesdata[$x]['unit_price'] = $productline['OrdersLine']['unit_price'];
                    $productlinesdata[$x]['total_line'] = $productline['OrdersLine']['total_line'] * $factor * -1;
                    // Only on initial entry to stock do we consider inventory count value
                    if($productline['OrdersLine']['receivedqty'] == $productline['OrdersLine']['quantity']) {
                        $productlinesdata[$x]['tname'] =  "Initial Inventory Count";
                        $productlinesdata[$x]['ticon'] =  "fa-flag";
                        $productlinesdata[$x]['quantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? $productline['OrdersLine']['sentqty'] : $productline['OrdersLine']['receivedqty']);
                        if($productlinesdata[$x]['comments'] == 'By import file') {
                            $productlinesdata[$x]['cum_qty'] = abs($productlinesdata[$x]['tquantity']);
                        }
                    }
                }
            }

            //Inventory assemble kit
            if($productline['OrdersLine']['type'] == 7) {
                if($productline['OrdersLine']['sentqty'] >= 0) {
                    $factor = -1;
                    $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? ($productline['OrdersLine']['sentqty'] +  $productline['OrdersLine']['damagedqty'])* $factor : "+".($productline['OrdersLine']['receivedqty'] +  $productline['OrdersLine']['damagedqty'])* $factor);
                    $productlinesdata[$x]['tname'] =  "Issue to Assemble Kit";
                    $productlinesdata[$x]['ticon'] =  "fa-compress";
                    $productlinesdata[$x]['quantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? $productline['OrdersLine']['sentqty'] : $productline['OrdersLine']['receivedqty']);
                    $cum_qty = $cum_qty + $productlinesdata[$x]['tquantity'];
                    $chartd1[] = [$x, ceil($cum_qty), 'test'];
                    $chartd2[] = [$x, $safety_stock];
                    $chartd3[] = [$x, $reorder_point];
                    $productlinesdata[$x]['cum_qty'] = $cum_qty;
                    $productlinesdata[$x]['unit_price'] = $productline['OrdersLine']['unit_price'];
                    $productlinesdata[$x]['total_line'] = $productline['OrdersLine']['total_line'] * $factor * -1;
                }
            }

            //Inventory correction
            if($productline['OrdersLine']['type'] == 99) {
                $productlinesdata[$x]['order_id'] = '';
                $productlinesdata[$x]['tquantity'] = 0;
                $productlinesdata[$x]['tname'] =  "Inventory offset";
                $productlinesdata[$x]['ticon'] =  "fa-recycle";
                $productlinesdata[$x]['quantity'] = 0;
                $cum_qty = $cum_qty;
                $chartd1[] = [$x, ceil($cum_qty), 'test'];
                $chartd2[] = [$x, $safety_stock];
                $chartd3[] = [$x, $reorder_point];
                $productlinesdata[$x]['cum_qty'] = $cum_qty;
                $productlinesdata[$x]['unit_price'] = $productline['OrdersLine']['unit_price'];
                $productlinesdata[$x]['total_line'] = $productline['OrdersLine']['total_line'] * $factor * -1;
            }
        }

        $response['draw'] = 1;
        $response['chartd1'] = $chartd1;
        $response['chartd2'] = $chartd2;
        $response['chartd3'] = $chartd3;
        $response['recordsTotal'] = $count_total;
        $response['cum_qty'] = $cum_qty;
        $response['rows_count'] = count($productlinesdata);
        $response['rows'] = $productlinesdata;

        echo json_encode($response);
        exit;
    }


    /**
     * tx_history method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function trn_export($pid = null) {
        $this->loadModel('OrdersLine');

        $limit = 500;
        $conditions = array();
        if($pid) {
            $conditions['OrdersLine.product_id'] =  $pid;
            $this->Product->recursive = -1;
            $product = $this->Product->find('first', array('conditions' => array('Product.id' => $pid), 'fields' => array('Product.user_id')));
            if($product['Product']['user_id'] != $this->Auth->user('id')) {
                $warehouses_access = $this->Access->locationList('Inventory', false, 'r', $product['Product']['user_id']);
                $conditions['OrdersLine.warehouse_id IN'] = array_keys($warehouses_access);
            }
        } else {
            $warehouses_access = $this->Access->locationList('Inventory');
            if($warehouses_access) {
                $conditions['OR']['OrdersLine.warehouse_id IN'] = array_keys($warehouses_access);
                $conditions['OR']['OrdersLine.user_id'] = $this->Auth->user('id');
            } else {
                $conditions['OrdersLine.user_id'] = $this->Auth->user('id');
            }
        }
        
        if(!empty($this->request->params['named']['location'])) {
            $conditions['OrdersLine.warehouse_id'] = $this->request->params['named']['location'];
        }

        if(!empty($this->request->params['named']['q'])) {
            $conditions['OrdersLine.comments LIKE'] = '%'. $this->request->params['named']['q'] .'%';
        }

        $productlines = $this->OrdersLine->find('all', array(
            'conditions' => $conditions,
            'contain' => array('Order', 'Warehouse', 'Product', 'DcopUser', 'User'),
            'fields' => array(
                'OrdersLine.id',
                'OrdersLine.order_id',
                'OrdersLine.quantity',
                'OrdersLine.receivedqty',
                'OrdersLine.sentqty',
                'OrdersLine.damagedqty',
                'OrdersLine.unit_price',
                'OrdersLine.total_line',
                'OrdersLine.comments',
                'OrdersLine.modified',
                'OrdersLine.type',
                'OrdersLine.return',
                'Order.ordertype_id',
                'Warehouse.name',
                'Product.name',
                'Product.sku',
                'DcopUser.username',
                'User.username'
            ),
            'order' => array('OrdersLine.modified' => 'ASC'),
        ));
        #$productlines = $this->paginate('OrdersLine');
        //$count_total = $this->OrdersLine->find('count', array('conditions' => $conditions, 'contain' => false));

        $cum_qty = 0;

        if(empty($productlines)) {
            $productlinesdata = [];
        }

        $safety_stock = (isset($product[0]['Product']['safety_stock']) ? $product[0]['Product']['safety_stock'] : 0);
        $reorder_point = (isset($product[0]['Product']['reorder_point']) ? $product[0]['Product']['reorder_point'] : 0);

        foreach($productlines as $x => $productline) {
            $productlinesdata[$x]['warehouse_name'] = $productline['Warehouse']['name'];
            $productlinesdata[$x]['product_sku'] = $productline['Product']['sku'];
            $productlinesdata[$x]['product_name'] = $productline['Product']['name'];
            $productlinesdata[$x]['order_id'] = $productline['OrdersLine']['order_id'];
            $productlinesdata[$x]['comments'] = $productline['OrdersLine']['comments'];
            $productlinesdata[$x]['id'] = $productline['OrdersLine']['id'];
            $productlinesdata[$x]['creator'] = (isset($productline['DcopUser']['username'])?$productline['DcopUser']['username']:$productline['User']['username']);
            $productlinesdata[$x]['date'] = $productline['OrdersLine']['modified'];
            $productlinesdata[$x]['quantity'] = $productline['OrdersLine']['quantity'];
            $productlinesdata[$x]['type'] = $productline['Order']['ordertype_id'];

            if($productline['Order']['ordertype_id'] == 2 || $productline['OrdersLine']['type'] == 6 || $productline['Order']['ordertype_id'] == 1 || $productline['OrdersLine']['type'] == 5)
            {
                // Receive from supplier
                if($productline['Order']['ordertype_id'] == 1 || $productline['OrdersLine']['type'] == 5) {
                    $factor = -1;
                    $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? "-".($productline['OrdersLine']['sentqty'] +  $productline['OrdersLine']['damagedqty'])* $factor : "+".($productline['OrdersLine']['receivedqty'] +  $productline['OrdersLine']['damagedqty'])* $factor);
                    $productlinesdata[$x]['order_id'] = $productline['OrdersLine']['order_id'];
                    $productlinesdata[$x]['tname'] = ($productline['OrdersLine']['type'] == 2 ? "Replns. Order" : ($productline['OrdersLine']['type'] == 6 ? "Receive To Inventory" : ($productline['OrdersLine']['type'] == 5 ? "Issue From Inventory" : ($productline['OrdersLine']['type'] == 1 ? "Sales Order" : ""))));
                    $productlinesdata[$x]['ticon'] = ($productline['OrdersLine']['type'] == 2 ? "fa-truck" : ($productline['OrdersLine']['type'] == 6 ? "fa-arrow-left" : ($productline['OrdersLine']['type'] == 5 ? "fa-arrow-right" : ($productline['OrdersLine']['type'] == 1 ? "fa-truck" : ""))));
                    $cum_qty = $cum_qty + $productlinesdata[$x]['tquantity'];
                    $productlinesdata[$x]['cum_qty'] = $cum_qty;
                    $productlinesdata[$x]['unit_price'] = $productline['OrdersLine']['unit_price'];
                    $productlinesdata[$x]['total_line'] = $productline['OrdersLine']['total_line'] * $factor * -1;
                    if($productline['OrdersLine']['receivedqty'] > 0) {
                        $productlinesdata[$x]['product_name'] = $productline['Product']['name'];
                        $productlinesdata[$x]['order_id'] = $productline['OrdersLine']['order_id'];
                        $productline['OrdersLine']['created'];
                        $productlinesdata[$x]['quantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? $productline['OrdersLine']['sentqty'] : $productline['OrdersLine']['receivedqty']);
                        $factor = 1;
                        $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? "+".($productline['OrdersLine']['receivedqty'] * $factor) : "-".($productline['OrdersLine']['sentqty'] * $factor));
                        $productlinesdata[$x]['tname'] =  'Return From Customer';
                    }
                }
                if($productline['OrdersLine']['return'] == false) {
                    //factor is to determine if the transaction qty is in + or -
                    $factor = 1;
                    $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? "-".($productline['OrdersLine']['sentqty'] +  $productline['OrdersLine']['damagedqty'])* $factor : "+".($productline['OrdersLine']['receivedqty'] +  $productline['OrdersLine']['damagedqty'])* $factor);
                    $productlinesdata[$x]['quantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? $productline['OrdersLine']['sentqty'] : $productline['OrdersLine']['receivedqty']);
                    $productlinesdata[$x]['tname'] = ($productline['OrdersLine']['type'] == 2 ? "Replns. Order" : ($productline['OrdersLine']['type'] == 6 ? "Receive To Inventory" : ($productline['OrdersLine']['type'] == 5 ? "Issue From Inventory" : ($productline['OrdersLine']['type'] == 1 ? "Sales Order" : ""))));
                    $productlinesdata[$x]['ticon'] = ($productline['OrdersLine']['type'] == 2 ? "fa-truck" : ($productline['OrdersLine']['type'] == 6 ? "fa-arrow-left" : ($productline['OrdersLine']['type'] == 5 ? "fa-arrow-right" : ($productline['OrdersLine']['type'] == 1 ? "fa-truck" : ""))));
                    $cum_qty = $cum_qty + $productlinesdata[$x]['tquantity'];
                    $productlinesdata[$x]['cum_qty'] = $cum_qty;
                    $productlinesdata[$x]['unit_price'] = $productline['OrdersLine']['unit_price'];
                    $productlinesdata[$x]['total_line'] = $productline['OrdersLine']['total_line'] * $factor ;
                } else {
                    // A return to supplier
                    $factor = -1;
                    $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? "-".($productline['OrdersLine']['sentqty'] +  $productline['OrdersLine']['damagedqty'])* $factor : "+".($productline['OrdersLine']['receivedqty'] +  $productline['OrdersLine']['damagedqty'])* $factor);
                    $productlinesdata[$x]['tname'] =  'Return To Supplier';
                    $cum_qty = $cum_qty + $productlinesdata[$x]['tquantity'];
                }
            }
            //Inventory count lines
            if($productline['OrdersLine']['type'] == 3) {
                if($productline['OrdersLine']['sentqty'] > 0) {
                    $factor = -1;
                    $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? ($productline['OrdersLine']['sentqty'] +  $productline['OrdersLine']['damagedqty'])* $factor : "+".($productline['OrdersLine']['receivedqty'] +  $productline['OrdersLine']['damagedqty'])* $factor);
                    $productlinesdata[$x]['tname'] =  "Inventory Count";
                    $productlinesdata[$x]['ticon'] =  "fa-barcode";
                    $productlinesdata[$x]['quantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? $productline['OrdersLine']['sentqty'] : $productline['OrdersLine']['receivedqty']);
                    $cum_qty = $cum_qty + $productlinesdata[$x]['tquantity'];
                    $productlinesdata[$x]['cum_qty'] = $cum_qty;
                    $productlinesdata[$x]['unit_price'] = $productline['OrdersLine']['unit_price'];
                    $productlinesdata[$x]['total_line'] = $productline['OrdersLine']['total_line'] * $factor * -1;
                } else {
                    $factor = 1;
                    $productlinesdata[$x]['tquantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? "-".($productline['OrdersLine']['sentqty'] +  $productline['OrdersLine']['damagedqty'])* $factor : "+".($productline['OrdersLine']['receivedqty'] +  $productline['OrdersLine']['damagedqty'])* $factor);
                    $productlinesdata[$x]['tname'] =  "Inventory Count";
                    $productlinesdata[$x]['ticon'] =  "fa-barcode";
                    $cum_qty = $cum_qty + $productlinesdata[$x]['tquantity'];
                    $productlinesdata[$x]['cum_qty'] = $cum_qty;
                    $productlinesdata[$x]['unit_price'] = $productline['OrdersLine']['unit_price'];
                    $productlinesdata[$x]['total_line'] = $productline['OrdersLine']['total_line'] * $factor * -1;
                    // Only on initial entry to stock do we consider inventory count value
                    if($productline['OrdersLine']['receivedqty'] == $productline['OrdersLine']['quantity']) {
                        $productlinesdata[$x]['tname'] =  "Initial Inventory Count";
                        $productlinesdata[$x]['ticon'] =  "fa-flag";
                        $productlinesdata[$x]['quantity'] = (($productline['OrdersLine']['receivedqty'] == 0) ? $productline['OrdersLine']['sentqty'] : $productline['OrdersLine']['receivedqty']);
                        if($productlinesdata[$x]['comments'] == 'By import file') {
                            $productlinesdata[$x]['cum_qty'] = abs($productlinesdata[$x]['tquantity']);
                        }
                    }

                }
            }
        }

        //pr($productlinesdata);
        $_serialize = 'productlinesdata';
        $_header = array('Type', 'Order Number', 'SKU', 'Name', 'Location', 'Quantity', 'Inv. Change', 'Cum Qty', 'User', 'Remarks', 'Time & Date');
        $_extract = array('tname', 'order_id', 'product_sku', 'product_name', 'warehouse_name', 'quantity', 'tquantity', 'cum_qty', 'creator', 'comments', 'date');

        $file_name = "Delivrd_".date('Y-m-d-His')."_trn_history.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('productlinesdata', '_serialize', '_header', '_extract'));
    }


    public function vlreport() {
        $this->layout = 'mtrd';
        $this->loadModel('Serial');
        $this->loadModel('Product');

        if ($this->Auth->user('is_limited') && empty($this->Access->_access['Inventory'])) {
            die('404');
        }
        $is_write = true;
        if($this->Auth->user('is_limited')) {
            $is_write = false;
            $accesslist = Set::combine($this->Access->_access['Inventory'], '{n}.Warehouse.id', '{n}.NetworksAccess.access');
            foreach ($accesslist as $value) {
                if($value != 'r') {
                    $is_write = true;
                    break;
                }
            }
        }

        // Access part
        if(!empty($this->Access->_access['Inventory'])) { //user has network inventory
            $warehouses = $this->Access->getLocations('Inventory');

            $loc_ids = [];
            $warehouses_list = [];
            foreach ($warehouses as $net => $loc) {
                foreach ($loc as $key => $val) {
                    $loc_ids[] = $key;
                    if($net !=  'My Locations') {
                        $warehouses_list[$key] = $net .' <i class="fa fa-angle-right"></i> '. $val;
                    } else {
                        $warehouses_list[$key] = $val;
                    }
                }
            }

            $products = $this->Access->getProducts('Inventory');
            $conditions['OR'] = [
                'Inventory.user_id' => $this->Auth->user('id'),
                ['Inventory.warehouse_id' => $loc_ids, 'Inventory.product_id' => array_keys($products)]
            ];
        } else {
            $conditions = ['Inventory.user_id' => $this->Auth->user('id')];
            $warehouses = $warehouses_list = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'), 'Warehouse.status' => 'active')));
        }

        // if setting locationactive set to 1 or user have access to other locations
        $locationsactive = 0;
        if(count($warehouses) > 1 || $this->Session->read('locationsactive')) {
            $locationsactive = 1;
        }

        $searchby = '';
        if (!empty($this->request->params['named']['searchby'])) {
            $conditions['AND']['OR'] = ['Product.name LIKE' => '%'. $this->request->params['named']['searchby'] .'%'];
        }

        if (!empty($this->request->params['named']['warehouse_id'])) {
            $conditions['Inventory.warehouse_id'] =  $this->request->params['named']['warehouse_id'];
        } else {
            $conditions['Inventory.warehouse_id'] = array_keys($warehouses_list);
        }

        $product = ['product_id' => '', 'name' => ''];
        if (!empty($this->request->params['named']['product'])) {
            $product['product_id'] = $conditions['Inventory.product_id'] =  $this->request->params['named']['product'];
            $this->Product->id = $product['product_id'];
            $product['name'] = $this->Product->field('name');
        }

        $category_id = '';
        if(!empty($this->request->params['named']['category_id'])) {
            $category_id = $conditions['Product.category_id'] =  $this->request->params['named']['category_id'];
        }

        $this->Prg->commonProcess();
        $this->paginate = array(
            'conditions' => $conditions,
            'fields' => array(
                "Inventory.id",
                "Inventory.product_id",
                "Inventory.user_id",
                "Inventory.warehouse_id",
                "Inventory.quantity",
                "Inventory.damaged_qty",
                "Inventory.modified",
                "Product.name",
                "Product.safety_stock",
                "Product.reorder_point",
                "Product.imageurl ",
                "Product.sku ",
                "Product.category_id",
                "Product.value",
                //"Warehouse.name ",
                //"Warehouse.id ",
                "NetworksAccess.access",
                "Network.name"
            ),
            'limit' => 10,
        );
        $inventories = $this->paginate();
        #pr($inventories);
        #exit;

        //$virtualFields = array('total' => 'SUM(Product.value * Inventory.quantity)');
        $total_value = $this->Inventory->find('first', ['conditions'=>$conditions, 'fields'=>['SUM(Product.value * Inventory.quantity) as total_value'], 'contains'=>['Product']]);

        $categories = $this->Access->networkCats();
        $list_categories = [];
        foreach ($categories as $net_cat) {
            $list_categories += $net_cat;
        }

        $this->set(compact('count_pdt', 'total_value', 'inventories', 'networks_list', 'locationsactive', 'is_write', 'warehouses', 'warehouses_list', 'product', 'categories', 'category_id', 'list_categories'));
    }

    public function vlreport_csv() {
        $conditions = array();
        // Access part
        if(!empty($this->Access->_access['Inventory'])) { //user has network inventory
            $warehouses = $this->Access->getLocations('Inventory');

            $loc_ids = [];
            foreach ($warehouses as $net => $loc) {
                foreach ($loc as $key => $val) {
                    $loc_ids[] = $key;
                }
            }
            $products = $this->Access->getProducts('Inventory');
            $conditions['OR'] = [
                'Inventory.user_id' => $this->Auth->user('id'),
                ['Inventory.warehouse_id' => $loc_ids, 'Inventory.product_id' => array_keys($products)]
            ];
        } else {
            $conditions = ['Inventory.user_id' => $this->Auth->user('id')];
            $warehouses = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'), 'Warehouse.status' => 'active')));
        }

        // if setting locationactive set to 1 or user have access to other locations
        $locationsactive = 0;
        if(count($warehouses) > 1 || $this->Session->read('locationsactive')) {
            $locationsactive = 1;
        }

        $searchby = '';
        if (!empty($this->request->params['named']['searchby'])) {
            $conditions['AND']['OR'] = ['Product.name LIKE' => '%'. $this->request->params['named']['searchby'] .'%'];
        }

        if (!empty($this->request->params['named']['warehouse_id'])) {
            $conditions['Inventory.warehouse_id'] =  $this->request->params['named']['warehouse_id'];
        } else {
            $conditions['Inventory.warehouse_id'] = array_keys($warehouses_list);
        }

        $product = ['product_id' => '', 'name' => ''];
        if (!empty($this->request->params['named']['product'])) {
            $product['product_id'] = $conditions['Inventory.product_id'] =  $this->request->params['named']['product'];
            $this->Product->id = $product['product_id'];
            $product['name'] = $this->Product->field('name');
        }

        $category_id = '';
        if(!empty($this->request->params['named']['category_id'])) {
            $category_id = $conditions['Product.category_id'] =  $this->request->params['named']['category_id'];
        }

        $this->Inventory->virtualFields['total_value'] = 'ROUND((`Inventory`.`quantity` * `Product`.`value` ), 2)';
        $inventories = $this->Inventory->find('all',array(
            'conditions' => $conditions,
            'contain' => ['Warehouse','Product','Product.Category'],
            'fields'=>[
                'Inventory.quantity',
                'Product.*',
                'Warehouse.name',
                'total_value'
            ],
            'joins' => array(
                array('table' => 'networks_access',
                    'alias' => 'NetworksAccess',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'NetworksAccess.warehouse_id = Inventory.warehouse_id AND NetworksAccess.model = "Inventory" AND NetworksAccess.user_id = "'. CakeSession::read("Auth.User.id") .'"',
                    )
                ),
                array('table' => 'networks',
                    'alias' => 'Network',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Network.id = NetworksAccess.network_id',
                    )
                )
            )
        ));

        $_serialize = 'inventories';
        $_header = array('SKU','ProductName','Location', 'Category', 'AvailableStock', 'Unit Value('. $this->Session->read('currencyname') .')', 'Total Value('. $this->Session->read('currencyname') .')');
        $_extract = array('Product.sku', 'Product.name', 'Warehouse.name', 'Product.Category.name', 'Inventory.quantity', 'Product.value', 'Inventory.total_value');

        $file_name = "Delivrd_".date('Y-m-d-His')."_valuation.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('inventories', '_serialize', '_header', '_extract'));
    }

    public function receive_issue() {
        $this->layout = false;
    }

    public function issue_transfer($serial_no) {
        $this->layout = false;

        $inventory_id = 0;
        $locations = [];
        if($serial_no) {
            $serial_no = $this->Serial->find('first', ['conditions' => array('Serial.serialnumber' => $serial_no)]);
            if($inventories) {
                $inventory_id = $inventories[0]['Inventory']['id'];
                // We need locations only from the same network of inventory
                if($inventories[0]['Network']['name'] && isset($warehouses[$inventories[0]['Network']['name']])) {
                    $locations = $warehouses[$inventories[0]['Network']['name']];
                } else if(isset($warehouses['My Locations'])) {
                    $locations = $warehouses['My Locations'];
                } else {
                    $locations = $warehouses;
                }
            }
        }
    }

    public function serial_transfer($serial_no) {
        $this->layout = false;

        $inventory_id = 0;
        $locations = [];
        if($serial_no) {
            $serial_no = $this->Serial->find('first', ['conditions' => array('Serial.serialnumber' => $serial_no)]);
            if($inventories) {
                $inventory_id = $inventories[0]['Inventory']['id'];
                // We need locations only from the same network of inventory
                if($inventories[0]['Network']['name'] && isset($warehouses[$inventories[0]['Network']['name']])) {
                    $locations = $warehouses[$inventories[0]['Network']['name']];
                } else if(isset($warehouses['My Locations'])) {
                    $locations = $warehouses['My Locations'];
                } else {
                    $locations = $warehouses;
                }
            }
        }
    }

    public function inventory_low() {
        App::uses('CakeEmail', 'Network/Email');
        //$email = new CakeEmail('mandrill');
        $email = new CakeEmail('default');
        //$email->bcc('fordenis@ukr.net');
        $email->viewVars(array('username' => 'Denis Ch', 'title' => 'Low inventory alert for product $SKU $NAME'));
        $email->template('inventory_low', 'main')
            ->emailFormat('html')
            ->to('fordenis@ukr.net')
            ->subject('Low inventory alert for product $SKU $NAME')
            ->send();

        exit;
    }
}
