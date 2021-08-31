<?php
App::uses('AppController', 'Controller');
/**
 * Serials Controller
 *
 * @property Serial $Serial
 * @property PaginatorComponent $Paginator
 */
class SerialsController extends AppController {


    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator','Search.Prg','Csv.Csv', 'InventoryManager');

    var $helpers = array('Networks.Network');

    public $theme = 'Mtro';

    public function beforeFilter() {
       parent::beforeFilter();

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
        if($this->Auth->user('is_limited') && !array_key_exists('Serials', $this->Access->_access)) {
            throw new MethodNotAllowedException(__('Have no access'));
        }

        $limit = $this->Auth->user('list_limit');
        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );

        $is_write = true;
        if($this->Auth->user('is_limited')) {
            $is_write = false;
            foreach ($this->Access->_access['Serials'] as $value) {
                if(strpos($value['NetworksAccess']['access'], 'w') !== false) {
                    $is_write = true;
                }
            }
        }
        $this->layout = 'mtrd';

        if ($this->request->is('post')) {
            $this->Prg->commonProcess(null, ['paramType'=>'query']);
        }
        if ($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }

        $this->Paginator->settings = array (
            'limit' => $limit,
            'fields' => ['Serial.*', 'Warehouse.name', 'Product.id', 'Product.name', 'Product.sku', 'Product.imageurl', 'Network.name', 'NetworksAccess.access'],
            'joins' => array (
                array('table' => 'networks_access',
                    'alias' => 'NetworksAccess',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'NetworksAccess.warehouse_id = Serial.warehouse_id AND NetworksAccess.model = "Serials" AND NetworksAccess.user_id = "'. CakeSession::read("Auth.User.id") .'"',
                    )
                ),
                array('table' => 'networks',
                    'alias' => 'Network',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Serial.user_id = Network.created_by_user_id',
                    )
                )
            ),
            'order' => array('Serial.modified' => 'DESC'),
            'group' => 'Serial.id'
        );
        $this->Serial->recursive = 0;

        $locations = $this->Access->getLocations('Serials');
        $loc_ids = [];
        foreach ($locations as $net => $loc) {
            foreach ($loc as $key => $val) {
                $loc_ids[] = $key;
            }
        }

        $net_products = $this->Access->getProducts('Serials');
        if($this->Auth->user('is_limited')) {
            $conditions['AND'] = [
                'Serial.warehouse_id' => $loc_ids,
                'Serial.product_id' => array_keys($net_products)
            ];
        } else {
            if($loc_ids) {
                $conditions['OR'] = [
                    'Serial.user_id' => $this->Auth->user('id'), // Own serials
                    ['Serial.warehouse_id' => $loc_ids, 'Serial.product_id' => array_keys($net_products)]
                ];
            } else {
                $conditions['Serial.user_id'] = $this->Auth->user('id');
            }
        }

        if (isset($this->request->query['product'])) {
            $serialids = $this->Serial->find('list', array('fields' => array('Serial.id'), 'conditions' => array('Serial.product_id' => $this->request->query['product'])));
            $conditions['Serial.id'] =  $serialids;
        }

        if($this->request->query('searchby')) {
            $conditions[] = array('OR' => array('Serial.serialnumber' => $this->request->query('searchby'), 'Product.name' => $this->request->query('searchby'), 'Product.sku' => $this->request->query('searchby')));
        }
        if($this->request->query('instock')) {
            $conditions['Serial.instock'] =  $this->request->query('instock');
        }
        if($this->request->query('warehouse_id')) {
            $conditions['Serial.warehouse_id'] =  $this->request->query('warehouse_id');
        }

        $this->set('serials', $this->Paginator->paginate($conditions));

        $warehouses = $this->Serial->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'))));
        $this->countSerial();
        $this->set(compact('is_write', 'warehouses', 'limit', 'options'));
    }

    public function countSerial() {
        $options = array('Serial.user_id' => $this->Auth->user('id'));
        $this->Session->write('serialcount', $this->Serial->find('count', array('conditions' => $options)));
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
        if (!$this->Serial->exists($id)) {
            throw new NotFoundException(__('Invalid serial'));
        }
        $options = array('conditions' => array('Serial.' . $this->Serial->primaryKey => $id));
        $this->set('serial', $this->Serial->find('first', $options));
    }


    /**
     * add method
     *
     * @return void
     */
    public function add($prodid = null,$ordid = null) {
        $this->layout = 'mtrd';

        $products = $this->Access->productList('Serials','w');
        if(!$this->Auth->user('is_limited')) {
            $my_products = $this->Serial->Product->find('list',array('conditions' => array('Product.user_id' => $this->Auth->user('id'))));
            $products = ['My Products' => $my_products] + $products;
        }
        $warehouses = $this->Access->getLocations('Serials', false, 'w');

        // Is user has access to this page?
        if($this->Auth->user('is_limited')) {
            if(!count($products) || !count($warehouses)) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
        }

        if ($this->request->is('post')) {
            $this->Serial->create();
            $this->request->data('Serial.instock',1);
            if(isset($this->request->params['pass'][1])) {
                $this->request->data('Serial.order_id_in',$this->request->params['pass'][1]);
            }
            if($this->Session->read('ordid') != null) {
                $this->request->data('Serial.order_id_in',$this->Session->read('ordid'));
            }
            if($this->Session->read('prodid') != null) {
                $this->request->data('Serial.product_id',$this->Session->read('prodid'));
            }

            if($this->Session->read('prodid') != null && $this->Session->read('ordid') == null) {
                $product = $this->Serial->Product->find('first', ['conditions' => ['Product.id' => $this->Session->read('prodid')], 'callbacks' => false]);
            } else {
                $product = $this->Serial->Product->find('first', ['conditions' => ['Product.id' => $this->request->data['Serial']['product_id']], 'callbacks' => false]);
            }

            $this->request->data('Serial.user_id', $product['Product']['user_id']); // Do we need to know what user add serial???

            if ($this->Serial->save($this->request->data)) {
                if($this->request->data['Serial']['warehouse_id']) {
                    $this->loadModel('Inventory');
                    $this->loadModel('Product');
                    $this->loadModel('OrdersLine');
                    $inventory = $this->Inventory->find('first', array('conditions' => array('Inventory.warehouse_id'  => $this->request->data['Serial']['warehouse_id'], 'Inventory.product_id'  => $this->request->data['Serial']['product_id']), 'recursive' => 1));
                    $product = $this->Product->find('first', array('conditions' => array('Product.id'  => $this->request->data['Serial']['product_id']), 'recursive' => -1, 'callbacks' => false));

                    $linetype = 6;
                    $recqty = 1;
                    if($inventory) {
                        $this->Inventory->id = $inventory['Inventory']['id'];
                        $this->Inventory->saveField('quantity', $inventory['Inventory']['quantity'] + 1);
                        $data = array(
                            'OrdersLine' => array(
                                'order_id' => 4294967294,
                                'line_number' => 1,
                                'type' => $linetype,
                                'product_id'  => $inventory['Inventory']['product_id'],
                                'quantity' => $inventory['Inventory']['quantity'],
                                'receivedqty' => $recqty,
                                'damagedqty' => 0,
                                'sentqty' => 0,
                                'unit_price' => $inventory['Product']['value'],
                                'total_line' => $inventory['Product']['value'] * abs(1),
                                'foc' => '',
                                'warehouse_id' => $this->request->data['Serial']['warehouse_id'],
                                'serial_id' => $this->Serial->id,
                                'return' => '',
                                'comments' => '',
                                'user_id' => $product['Product']['user_id']
                            )
                        );
                    } else {
                        $data = array(
                            'Inventory' => array(
                                'product_id' => $this->request->data['Serial']['product_id'],
                                'user_id' => $product['Product']['user_id'],
                                'dcop_user_id' => $this->Auth->user('id'),
                                'warehouse_id' => $this->request->data['Serial']['warehouse_id'],
                                'quantity' => 1
                            )
                        );
                        $this->Inventory->create();
                        $invcreated = $this->Inventory->save($data);
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
                                'unit_price' => $product['Product']['value'],
                                'total_line' => $product['Product']['value'] * abs(1),
                                'foc' => '',
                                'warehouse_id' => $this->request->data['Serial']['warehouse_id'],
                                'serial_id' => $this->Serial->id,
                                'return' => '',
                                'comments' => '',
                                'user_id' => $product['Product']['user_id']
                            )
                        );
                    }
                    $this->OrdersLine->create();
                    $linecreated = $this->OrdersLine->save($data);
                }
                $this->Session->setFlash(__('Serial number %s has been added to product sku %s',$this->request->data['Serial']['serialnumber'],$product['Product']['sku']), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The serial could not be saved. Please, try again.'), 'admin/danger', array());
            }
        }

        //we have product id and order id, that means we do GR of serials from repl. order and we
        //were sent from the GR page
        $orderline = [];
        if($this->request->query('pid') != null && $this->request->query('oid') != null) {
            $this->Session->write('prodid', $this->request->query('pid'));
            $this->Session->write('ordid', $this->request->query('oid'));
            //$orderid = $this->request->params['pass'][1];
            $this->Serial->Product->recursive = -1;
            //Get product image
            $productimageurl = $this->Serial->Product->find('first',array(
                'fields' => 'Product.imageurl',
                'conditions' => array('Product.id' => $this->Session->read('prodid')),
                'callbacks' => false
            ));
            $this->Session->write('imgurl', $productimageurl['Product']['imageurl']);
            //Get order line received qty
            $this->loadModel('OrdersLine');
            $orderline = $recievedqty = $this->OrdersLine->find('first',array(
                'fields' => 'OrdersLine.receivedqty, OrdersLine.warehouse_id',
                'conditions' => array(
                    'OrdersLine.order_id' => $this->Session->read('ordid'),
                    'OrdersLine.product_id' => $this->Session->read('prodid'),
                    //'OrdersLine.user_id' => $this->Auth->user('id'),
                    //'OrdersLine.warehouse_id' => $this->Session->read('default_warehouse')
                )
            ));
            $this->Session->write('recievedqty', $recievedqty['OrdersLine']['receivedqty']);
            $product = $this->Serial->Product->findById($this->Session->read('prodid'));
        }
        if($prodid == 'x'){
            $this->Session->delete('prodid');
            $this->Session->delete('ordid');
        }

        //we already scanned the first serial and ordid was set, then we continue GR against the same repl. order
        if($this->Session->read('ordid') != null) {
            //Get count of serials for this order
            $orderid = $this->Session->read('ordid');
            //echo "we count lines here and order id is ".$orderid;
            $countorderserials = $this->Serial->find('count', array('conditions' => array('Serial.order_id_in' => $orderid)));
        //  Debugger::dump($countorderserials);
            $this->set(compact('countorderserials'));
            $product = $this->Serial->Product->findById($this->Session->read('prodid'));
        //  Debugger::dump($product);
            $this->set(compact('product'));
        }
        $this->set(compact('product','products','warehouses', 'orderline'));
    }

    public function add_line($line_id) {
        $this->layout = 'mtrd';
        $this->loadModel('OrdersLine');
        
        if (!$this->OrdersLine->exists($line_id)) {
            throw new NotFoundException(__('Invalid order line'));
        }

        $products = $this->Access->productList('Serials','w');
        if(!$this->Auth->user('is_limited')) {
            $my_products = $this->Serial->Product->find('list',array('conditions' => array('Product.user_id' => $this->Auth->user('id'))));
            $products = ['My Products' => $my_products] + $products;
        }
        $warehouses = $this->Access->getLocations('Serials', false, 'w');

        // Is user has access to this page?
        if($this->Auth->user('is_limited')) {
            if(!count($products) || !count($warehouses)) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
        }
        
        /*$orderline = $this->OrdersLine->find('first',array(
            'fields' => ['OrdersLine.receivedqty', 'OrdersLine.warehouse_id', 'Order.id', 'Product.id', 'Product.name', 'Product.sku', 'Product.user_id', 'Product.value'],
            'conditions' => array(
                'OrdersLine.id' => $line_id
            )
        ));*/

        $options = array(
            'conditions' => array('OrdersLine.id'  => $line_id),
            'contain' => array(
                'Order' => array('fields' => 'id', 'user_id'),
                'Product' => array('id', 'user_id', 'name', 'sku', 'packaging_material_id', 'receive_location')
        ));
        $orderline = $this->OrdersLine->find('first', $options);

        if ($this->request->is('post')) {
            $this->Serial->create();
            $this->request->data('Serial.instock',1);
            $this->request->data('Serial.user_id', $orderline['Product']['user_id']); // Do we need to know what user add serial???

            if ($this->Serial->save($this->request->data)) {
                if($this->request->data['Serial']['warehouse_id']) {
                    //echo '@@@';
                    $this->InventoryManager->receiveLine(
                        $orderline,
                        $this->request->data['Serial']['warehouse_id'],
                        (intval($orderline['OrdersLine']['receivedqty']) + 1),//$this->request->data['OrdersLine']['receivedqty'],
                        0, //$shipment_id,
                        ''//$this->request->data['OrdersLine']['receivenotes']
                    );

                    /*$this->loadModel('Inventory');
                    
                    $inventory = $this->Inventory->find('first', array('conditions' => array('Inventory.warehouse_id'  => $this->request->data['Serial']['warehouse_id'], 'Inventory.product_id'  => $this->request->data['Serial']['product_id']), 'recursive' => 1));

                    $linetype = 6;
                    $recqty = 1;
                    $quantity = 1;
                    if($inventory) {
                        $this->Inventory->id = $inventory['Inventory']['id'];
                        $quantity = $inventory['Inventory']['quantity'] + 1;
                        $this->Inventory->saveField('quantity', $quantity);
                        $data = array(
                            'OrdersLine' => array(
                                'order_id' => 4294967294,
                                'line_number' => 1,
                                'type' => $linetype,
                                'product_id'  => $inventory['Inventory']['product_id'],
                                'quantity' => $inventory['Inventory']['quantity'],
                                'receivedqty' => $recqty,
                                'damagedqty' => 0,
                                'sentqty' => 0,
                                'unit_price' => $inventory['Product']['value'],
                                'total_line' => $inventory['Product']['value'] * abs(1),
                                'foc' => '',
                                'warehouse_id' => $this->request->data['Serial']['warehouse_id'],
                                'serial_id' => $this->Serial->id,
                                'return' => '',
                                'comments' => '',
                                'user_id' => $orderline['Product']['user_id']
                            )
                        );
                    } else {
                        $data = array(
                            'Inventory' => array(
                                'product_id' => $this->request->data['Serial']['product_id'],
                                'user_id' => $orderline['Product']['user_id'],
                                'dcop_user_id' => $this->Auth->user('id'),
                                'warehouse_id' => $this->request->data['Serial']['warehouse_id'],
                                'quantity' => $quantity
                            )
                        );
                        $this->Inventory->create();
                        $invcreated = $this->Inventory->save($data);
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
                                'unit_price' => $orderline['Product']['value'],
                                'total_line' => $orderline['Product']['value'] * abs(1),
                                'foc' => '',
                                'warehouse_id' => $this->request->data['Serial']['warehouse_id'],
                                'serial_id' => $this->Serial->id,
                                'return' => '',
                                'comments' => '',
                                'user_id' => $orderline['Product']['user_id']
                            )
                        );
                    }
                    $this->OrdersLine->create();
                    $linecreated = $this->OrdersLine->save($data);
                    */
                }
                if($this->request->is('ajax')) {
                    $response['action'] = 'success';
                    $response['countorderserials'] = $this->Serial->find('count', array('conditions' => array('Serial.order_id_in' => $orderline['Order']['id'])));
                    $response['msg'] = __('Serial number %s has been added to product sku %s', $this->request->data['Serial']['serialnumber'], $orderline['Product']['sku']);
                    die(json_encode($response));
                } else {
                    $this->Session->setFlash(__('Serial number %s has been added to product sku %s', $this->request->data['Serial']['serialnumber'], $orderline['Product']['sku']), 'admin/success', array());
                    return $this->redirect(array('controller'=>'orders_lines', 'action' => 'receive', $orderline['Order']['id']));
                }
            } else {
                if($this->request->is('ajax')) {
                    $response['action'] = 'error';
                    $response['errors'] = $this->Serial->validationErrors;
                    $response['msg'] = __('The serial could not be saved. Please, try again.');
                    
                    die(json_encode($response));
                } else {
                    $this->Session->setFlash(__('The serial could not be saved. Please, try again.'), 'admin/danger', array());
                }
            }
        }
        
        $countorderserials = $this->Serial->find('count', array('conditions' => array('Serial.order_id_in' => $orderline['Order']['id'])));
        $this->set(compact('orderline', 'warehouses', 'countorderserials'));
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
        if (!$this->Serial->exists($id)) {
            throw new NotFoundException(__('Invalid serial'));
        }

        $serial = $this->Serial->find('first', ['conditions' => ['Serial.id' => $id]]);
        $products = $this->Access->productList('Serials','w');
        if(!$this->Auth->user('is_limited')) {
            $my_products = $this->Serial->Product->find('list',array('conditions' => array('Product.user_id' => $this->Auth->user('id'))));
            $products = ['My Products' => $my_products] + $products;
        }
        $warehouses = $this->Access->getLocations('Serials', false, 'w');

        if( !$this->Access->hasSerialAccess($serial, 'w') ) {
            throw new MethodNotAllowedException(__('Have no access'));
        }
        if ($this->request->is(array('post', 'put'))) {

            if ($this->Serial->save($this->request->data)) {
                $this->Session->setFlash(__('The Serial Number has been saved.'), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Serial Number could not be saved. Please, try again.'), 'admin/danger', array());
            }
        } else {
            $options = array('conditions' => array('Serial.' . $this->Serial->primaryKey => $id));
            $this->request->data = $this->Serial->find('first', $options);
        }

        $productimageurl = $this->request->data['Product']['imageurl'];
        $this->set(compact('products','productimageurl', 'serial'));
    }

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->Serial->id = $id;
		if (!$this->Serial->exists()) {
			throw new NotFoundException(__('Invalid serial'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Serial->delete()) {
			$this->Session->setFlash(__('The Serial Number has been deleted.'), 'admin/success', array());
		} else {
			$this->Session->setFlash(__('The Serial Number could not be deleted. Please, try again.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function find() {
	  $products = $this->paginate();
	    if ($this->request->is('requested')) {
	        return $serials;
	    } else {
	        $this->set('serials', $serials);
	    }

        $this->Prg->commonProcess();
        $this->Paginator->settings['conditions'] = $this->Serial->parseCriteria($this->Prg->parsedParams());
        $this->set('serials', $this->Paginator->paginate());
    }

    public function exportcsv() {
        $serials = $this->Serial->find('all',array('conditions' => array('Serial.user_id' => $this->Auth->user('id'))));
        $_serialize = 'serials';
        $_header = array('ProductName', 'SKU','SerialNumber', 'InStock', 'OrderReceived', 'OrderShipped', 'Created');
        $_extract = array('Product.name', 'Product.sku','Serial.serialnumber','Serial.instock','Serial.order_id_in','Serial.order_id_out','Serial.created');

        $file_name = "Delivrd_".date('Y-m-d-His')."_serials.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('serials', '_serialize', '_header', '_extract'));
    }

    public function uploadcsv() {
      $this->layout = 'mtru';
        if ($this->request->is('post')) {
            $target_path = WWW_ROOT."uploads/";
            $target_path = $target_path . basename( $_FILES['uploadedfile']['name']);

            if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
            //  echo "The file ".  basename( $_FILES['uploadedfile']['name']).
            //  " has been uploaded";
            //  echo "we boot is $this->webroot";

            $filesg = array( 'files' => array(array(
                "name" => $_FILES['uploadedfile']['name'],
                "size" => $_FILES['uploadedfile']['size'],
                "thumbnailUrl" => "/theme/Mtro/assets/admin/layout/img/csv.png"
            )));
            header('Content-Type: application/json');
            echo json_encode($filesg,JSON_PRETTY_PRINT);
            exit();

            //$this->importcsv($target_path,$_FILES['uploadedfile']['name'],$_FILES['uploadedfile']['size']);

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

    public function importcsv($filename = null) {
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
        $newserials = array();

        $content = WWW_ROOT."uploads/".$filename;


        $filedata = $this->Csv->import($content, array('Importfile.LineNumber','Serial.sku','Serial.serialnumber'));

        $errorstr = ".";
        $numprods = count($filedata);
        $createdcount = 0;
        foreach ($filedata as $key=>$serialdata) {

            if($key > 0) {
                //Debugger::dump($productdata);
                if(!is_numeric($serialdata['Importfile']['LineNumber'])) {
                    $this->errorstr = "Line number is missing";
                }

                if(empty($serialdata['Serial']['sku'] || empty($serialdata['Serial']['serialnumber']))) {
                    $this->errorstr = "Line no. ".$serialdata['Importfile']['LineNumber']. " has SKU or serial number missing";
                    $this->Session->setFlash(__($this->errorstr),'default',array('class'=>'alert alert-danger'));
                    return;
                }

                $this->loadModel('Product');
                $pid = $this->Product->find('first',array('fields' => 'Product.id','conditions' => array('Product.sku' =>$serialdata['Serial']['sku'], 'Product.user_id' => $this->Auth->user('id'))));
                if(empty($pid)) {
                    $this->errorstr = "SKU ".$serialdata['Serial']['sku']." in line no. ".$serialdata['Importfile']['LineNumber']." has no matching product in Delivrd.";
                    $this->Session->setFlash(__($this->errorstr),'default',array('class'=>'alert alert-danger'));
                    return;
                }

                $this->Serial->create();
                $this->request->data('Serial.user_id',$this->Auth->user('id'));
                $this->request->data('Serial.instock',1);
                $this->request->data('Serial.warehouse_id',$this->Session->read('default_warehouse'));
                $this->request->data('Serial.product_id',$pid['Product']['id']);
                $this->request->data('Serial.serialnumber',$serialdata['Serial']['serialnumber']);
                $createserial = $this->Serial->save($this->request->data);

            }
        }
        $this->Session->setFlash(__("%s serial numbers have been created successfully.", $createdcount),'default',array('class'=>'alert alert-success'));
    }

    public function downloadsamplefile() {

        $filename = $target_path = WWW_ROOT."uploads/Delivrd_sample_serials.csv"; // of course find the exact filename....
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

    public function serialTransaction($id = null) {
        $this->layout = 'mtrd';
        $this->loadModel('OrdersLine');
        $options = array('conditions' => array('OrdersLine.serial_id' => $id), 'contain' => array('Product','Warehouse'), 'callbacks' => false);
        $orderlines = $this->OrdersLine->find('all', $options);

        $page = ! empty( $_GET['page'] ) ? (int) $_GET['page'] : 1;
        $total = count($orderlines); //total items in array
        $limit = 9;
        $totalPages = ceil( $total/ $limit ); //calculate total pages
        $page = max($page, 1); //get 1 page when $_GET['page'] <= 0
        $page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
        $offset = ($page - 1) * $limit;

        if( $offset < 0 ) $offset = 0;
        $orderlines = array_slice($orderlines, $offset, $limit);

        $this->set('orderlines', $orderlines);
        $this->set(compact('totalPages', 'page'));
	}

}
