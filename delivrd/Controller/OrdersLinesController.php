<?php
App::uses('AppController', 'Controller');
App::uses('AdminHelper', 'View/Helper');
App::uses('CakeTime', 'Utility');
/**
 * OrdersLines Controller
 *
 * @property OrdersLine $OrdersLine
 * @property PaginatorComponent $Paginator
 */
class OrdersLinesController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator','EventRegister','Search.Prg', 'InventoryManager');
    public $helpers = array('Session','Time');
    public $theme = 'Mtro';

    public $types = [1 => 'S.O.', 2 => 'P.O.'];

    public function __beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow(array('productmob','subscribe'));

    }

    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function isAuthorized($user) {
        return parent::isAuthorized($user);
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->OrdersLine->exists($id)) {
            throw new NotFoundException(__('Invalid orders line'));
        }
        $options = array('conditions' => array('OrdersLine.' . $this->OrdersLine->primaryKey => $id));
        $this->set('ordersLine', $this->OrdersLine->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $this->layout = 'mtrd';
        $this->loadModel('Product');
        $this->loadModel('Order');
        $this->loadModel('Currency');

        if(!isset($this->request->query['lineid']) || !isset($this->request->query['ordid'])) {
            return $this->redirect(array('controller' => 'Dash', 'action' => 'index'));
        }
        $currentOrder = $this->Order->find('first', array(
            'fields' => array('Order.id','Order.ordertype_id', 'Order.user_id', 'Order.dcop_user_id', 'User.currency_id'),
            'contain' => array('User', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
            'conditions' => array('Order.id' => $this->request->query['ordid'])
        ));
        //$line_number = 10;
        if($currentOrder['OrdersLine']) {
            $line_number = $currentOrder['OrdersLine'][0]['line_number'] + $this->request->query['lineid'];
        } else {
            $line_number = $this->request->query['lineid'];
        }

        $is_own = ($currentOrder['Order']['user_id'] == $this->Auth->user('id'));
        if(!$is_own) { // Check access, get products and allowed warehouse
            //If we need to add a pack material, do this:
            if($this->request->query['lineid'] == '999999') {
                $addpack = 1;
                $products = $this->Access->getProducts('S.O.', 'w', $currentOrder['Order']['user_id'], ['Product.consumption' => true, 'Product.status_id' => 1]);
            } else {
                if($currentOrder['Order']['ordertype_id'] == 1) {
                    $products = $this->Access->getProducts('S.O.', 'w', $currentOrder['Order']['user_id'], ['Product.status_id' => 1]);
                } else {
                    $allowedstatusesrepl = [1,12];
                    $products = $this->Access->getProducts('P.O.', 'w', $currentOrder['Order']['user_id'], ['Product.status_id' => $allowedstatusesrepl]);
                }
            }
            $warehouses = $this->Access->locationList('S.O.', false, 'w', $currentOrder['Order']['user_id']);
            if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
            if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
        } else {
            if($this->request->query['lineid'] == '999999') {
                $addpack = 1;
                $products = $this->OrdersLine->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.consumption' => true, 'Product.status_id' => 1)));
            } else {
                if($currentOrder['Order']['ordertype_id'] == 1) {
                    $products = $this->OrdersLine->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.status_id' => 1)));
                } else {
                    $allowedstatusesrepl = [1,12];
                    $products = $this->OrdersLine->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id') , 'Product.status_id' => $allowedstatusesrepl)));
                }
            }
            $warehouses = [$this->Session->read('default_warehouse') => 'rw'];
        }
        $warehouse_id = array_keys($warehouses);
        $warehouse_id = $warehouse_id[0];

        $currencies = $this->Currency->find('first', array('conditions' => array('id' => $currentOrder['User']['currency_id'])));
        $currencyname = $currencies['Currency']['name'];

        $linecount = count($currentOrder['OrdersLine']);

        if($this->request->is('post')) {
            if($this->request->data['OrdersLine']['quantity'] > 0 && isset($this->request->data['OrdersLine']['product_id'])) {
                $this->OrdersLine->create();
                if(empty($this->data['OrdersLine']['product_id'])) {
                    $productsid = $this->Product->find('first', array('conditions' => array('Product.sku' => $this->data['OrdersLine']['sku']), 'callbacks' => false));
                    $this->request->data('OrdersLine.product_id',$productsid['Product']['id']);
                    if(empty($productsid)) {
                        $this->Session->setFlash(__('Could not find SKU.'), 'admin/danger', array());
                        $this->redirect(array('controller' => 'orders_lines', 'action' => 'add','?' => array('ordid' => $this->request->query['ordid'], 'lineid' => ($this->request->query['lineid'] ))));
                    }
                } else {
                    $this->request->data('OrdersLine.product_id', $this->data['OrdersLine']['product_id']);
                }

                $product = $this->Product->find('first', array('conditions' => array('Product.id' => $this->data['OrdersLine']['product_id']), 'callbacks' => false));

                $this->loadModel('Inventory');
                $inventoryRecord = $this->Inventory->find('first', array(
                    'conditions' => array(
                        'Inventory.product_id' => $this->data['OrdersLine']['product_id'],
                        'Inventory.user_id' => $currentOrder['Order']['user_id'],
                        'Inventory.deleted' => 0
                    )
                ));

                if(sizeof($inventoryRecord) == 0) {
                    $this->Inventory->set('user_id', $currentOrder['Order']['user_id']);
                    $this->Inventory->set('dcop_user_id', $this->Auth->user('id'));
                    $this->Inventory->set('product_id', $this->data['OrdersLine']['product_id']);
                    $this->Inventory->set('quantity', 0);
                    $this->Inventory->set('warehouse_id', $warehouse_id);
                    if ($this->Inventory->save($this->request->data)) {
                        $x=1;
                    }
                }
                if($this->request->query['lineid'] < 999999) {
                    ($this->data['OrdersLine']['unit_price']) ? $this->request->data('OrdersLine.unit_price', $this->data['OrdersLine']['unit_price']) : $this->request->data('OrdersLine.unit_price', 0);
                    $this->request->data('OrdersLine.status_id',1);
                    $this->request->data('OrdersLine.order_id', $this->request->query['ordid']);
                    $this->request->data('OrdersLine.line_number', $line_number);
                    $this->request->data('OrdersLine.type', $currentOrder['Order']['ordertype_id']);
                    $this->request->data('OrdersLine.sku', $product['Product']['sku']);
                    $this->request->data('OrdersLine.user_id', $currentOrder['Order']['user_id']);
                    $this->request->data('OrdersLine.sentqty',0);
                    $this->request->data('OrdersLine.receivedqty', 0);
                    $this->request->data('OrdersLine.damagedqty', 0);
                    $this->request->data('OrdersLine.warehouse_id',$warehouse_id);
                    $line_total = $this->data['OrdersLine']['quantity'] * $this->data['OrdersLine']['unit_price'];
                    $this->request->data('OrdersLine.total_line',$line_total);

                    if ($this->OrdersLine->save($this->request->data)) {
                        $packmaterial = $this->OrdersLine->Product->find('first', array('conditions' => array('Product.id' => $product['Product']['packaging_material_id'])));
                        if(!empty($packmaterial) && $this->Session->read('autopacking') == true) {
                            if($packmaterial['Product']['consumption'] == true && $currentOrder['Order']['ordertype_id'] == 1 ) {
                                $this->addpackmaterial($this->request->query['ordid'], $packmaterial['Product']['id'],1);
                            }
                        }
                        $response['status'] = true;
                        $response['message'] = 'The orders line has been added';
                        echo json_encode($response);exit;
                    }

                } else if($this->request->query['lineid'] == 999999) {
                    $this->addpackmaterial($this->request->query['ordid'], $this->request->data['OrdersLine']['product_id'],$this->request->data['OrdersLine']['quantity']);
                    $this->Session->setFlash(__('Packaging material has been added successfully'), 'admin/success', array());
                    return $this->redirect(array('controller' => 'orders', 'action' => 'editcord',$this->request->query['ordid']));
                } else {
                    $this->Session->setFlash(__('The orders line could not be added. Please, try again.'), 'admin/danger', array());
                    $response['status'] = false;
                    $response['message'] = 'The orders line could not be added. Please, try again.';
                    echo json_encode($response);
                    exit;
                }

            } else {
                $this->Session->setFlash(__('The orders line could not be added. Either the product does not exist or quantity is negative or 0.'), 'admin/danger', array());
            }
        }
        $this->set(compact('products','currentOrder','prodselect','linecount', 'currencyname', 'addpack'));
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
        $products = $this->Access->getProducts('S.O.', 'w');
        $warehouses = $this->Access->locationList('S.O.', false, 'w');

        if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
            throw new MethodNotAllowedException(__('Have no access'));
        }
        if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
            throw new MethodNotAllowedException(__('Have no access'));
        }

        if (!$this->OrdersLine->exists($id)) {
            throw new NotFoundException(__('Invalid orders line'));
        }
        if ($this->request->is(array('post', 'put'))) {
            $orderline = $this->OrdersLine->find('first',array('fields' => array('OrdersLine.order_id','Order.ordertype_id'),'conditions' => array('OrdersLine.id' => $id), 'callbacks' => false));
            // recalculate total after update of price or qty
            $line_total = $this->data['OrdersLine']['quantity'] * $this->data['OrdersLine']['unit_price'];
            $this->request->data('OrdersLine.total_line',$line_total);
            $this->request->data['OrdersLine']['sentqty'] = 0;
            if ($this->OrdersLine->save($this->request->data)) {
                $this->Session->setFlash(__('The Orders line has been saved.'), 'admin/success', array());
                if($orderline['Order']['ordertype_id'] == 1) {
                    return $this->redirect(array('controller' => 'orders', 'action' => 'editcord',$orderline['OrdersLine']['order_id']));
                } else {
                    return $this->redirect(array('controller' => 'orders', 'action' => 'editrord',$orderline['OrdersLine']['order_id']));
                }
            } else {
                $this->Session->setFlash(__('The Orders line could not be saved. Please, try again.'), 'admin/danger', array());
            }
        } else {
            $options = array('conditions' => array('OrdersLine.' . $this->OrdersLine->primaryKey => $id), 'callbacks'=>false);
            $this->request->data = $this->OrdersLine->find('first', $options);
        }

        $products = $this->Access->productList();
        $this->set(compact('products'));
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
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
        $editordpage = ($currentOrderLine['OrdersLine']['type'] == 1 ? 'editcord' : 'editrord');
        if ($this->OrdersLine->delete()) {
            $this->Session->setFlash(__('Order line number  %s has been deleted from order number  %s',$currentOrderLine['OrdersLine']['line_number'],$currentOrderLine['OrdersLine']['order_id']), 'admin/success', array());
        } else {
            $this->Session->setFlash(__('The Orders line could not be deleted. Please, try again.'), 'admin/danger', array());
        }
        return $this->redirect(array('controller' => 'orders', 'action' => $editordpage, $order_id));
    }

    /**
     * linereport method
     *
     * @throws NotFoundException
     * @param string $id
     * @param string $month
     * @return void
     */
    public function linereport($id, $month) {
        $this->layout = false;
        $this->loadModel('Product');
        $product = $this->Product->find('first', ['conditions' => array('Product.id' => $id)]);

        $start = date('Y-m', strtotime($month));
        $start = $start .'-01';
        $end = date("Y-m-t", strtotime($start));

        $lines = $this->OrdersLine->find('all', array(
            'conditions' => [
                'OrdersLine.product_id' => $id,
                'Order.status_id IN' => [2,3],
                'Order.ordertype_id' => 2,
                'OR' => array(
                    [
                        'Order.ordertype_id' => 2,
                        'Order.requested_delivery_date >=' => $start,
                        'Order.requested_delivery_date <=' => $end,
                        'OrderSchedule.id' => null
                    ],
                    [
                        'OrderSchedule.delivery_date >=' => $start,
                        'OrderSchedule.delivery_date <=' => $end,
                    ]
                )
            ],
            'contain' => [
                'Order', 'Warehouse', 'OrderSchedule'
            ],
            'joins' => array(
                array('table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Order.supplier_id = Supplier.id'
                    )
                ),
            ),
            'fields' => [
                'Order.id',
                'Order.supplier_id',
                'Order.requested_delivery_date',
                'OrdersLine.line_number',
                'OrdersLine.receivedqty',
                'OrdersLine.quantity',
                'OrdersLine.unit_price',
                'OrdersLine.total_line',
                'Warehouse.name',
                'Supplier.name',
                'OrderSchedule.id',
                'OrderSchedule.delivery_date'
            ]
        ));

        $this->set(compact('month', 'product', 'lines'));
    }

    public function receive($id) {
        $this->layout = 'mtrd';

        $order = $this->OrdersLine->Order->find('first', array(
            'conditions' => array('Order.id' => $id ),
            'contain' => array('Ordertype','State','Country','Supplysource','Supplier','Schannel','Status','Address','Shipment','OrdersLine'),
            'callbacks' => false
        ));
        if (!$order) {
            throw new NotFoundException(__('Invalid order'));
        }

        $can_complete = true;
        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to delete not own order
            $products = $this->Access->getProducts('P.O.', 'w', $order['Order']['user_id']);
            $conditions['OrdersLine.product_id'] = array_keys($products);
            $this->Paginator->settings['callbacks'] = false;

            $is_order = $this->Access->hasOrderAccess($id);
            $is_shipment = 1;// $this->Access->hasOrderShipmentAccess($id);
            $is_write = ($is_order && $is_shipment);
            
            $net_warehouses = $this->Access->getLocations('P.O.', false, 'w', $order['Order']['user_id']);
            $network = array_keys($net_warehouses);
            $network = array_shift($network);
            $warehouses = array_shift($net_warehouses);
        } else {
            $network = false;
            $conditions['OrdersLine.user_id'] = $this->Auth->user('id');
            $is_write = 1;
            $my_warehouse = $this->Access->getLocations('P.O.', false, 'w', $order['Order']['user_id']);
            $warehouses = array_shift($my_warehouse);
        }

        if($id) {
            $conditions['OrdersLine.order_id'] = $id;
        }

        if (isset($this->request->params['named']['searchby'])) {
            $conditions[] = array('OR' => array('Product.name' => $this->request->params['named']['searchby'], 'Product.sku' => $this->request->params['named']['searchby']));
        }

        $this->Prg->commonProcess();
        $this->Paginator->settings = array(
            //'order' => array('OrdersLine.created' => 'DESC'),
            'limit' => 100,
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
            'conditions' => $this->OrdersLine->parseCriteria($this->Prg->parsedParams()),
        );
        $ordersLines = $this->Paginator->paginate($conditions);

        if(count($ordersLines) == 0 && $order['Order']['user_id'] != $this->Auth->user('id')) {
            $this->Session->setFlash(__('You have no access to receive any lines in this order'), 'admin/danger');
            return $this->redirect(array('controller'=>'replorders', 'action' => 'details', $id));
        }
        if(count($ordersLines) != count($order['OrdersLine'])) {
            $can_complete = false;
        }

        $order_list = $this->OrdersLine->find('list', array('conditions'=>$conditions, 'contain' => ['Product'], 'fields'=>['Product.sku', 'OrdersLine.id']));
        $this->loadModel('Productsupplier');
        $part_ids = $this->Productsupplier->find('list', array('conditions'=>['Product.sku IN' => array_keys($order_list), 'Productsupplier.part_number !=' => ''], 'contain' => ['Product'], 'fields'=>['Productsupplier.part_number', 'Product.sku']));
        $this->set(compact('order', 'order_list', 'part_ids', 'ordersLines', 'is_write', 'warehouses', 'network', 'can_complete'));
    }

    public function receivelines() {
        $this->layout = 'mtrd';

        if ($this->request->is(array('post', 'put'))) {
            $id = $this->request->data['OrdersLine']['OrdersLineId'];
            $shipment_id = $this->request->data['OrdersLine']['shipment_id'];
            $options = array('conditions' => array('OrdersLine.id'  => $id), 'contain' => array('Order' => array('fields' => 'id', 'user_id'), 'Product' => array('id', 'name', 'sku', 'packaging_material_id', 'receive_location')));
            $orderline = $this->OrdersLine->find('first', $options);

            $response = $this->InventoryManager->receiveLine(
                $orderline,
                $this->request->data['OrdersLine']['warehouse_id'],
                $this->request->data['OrdersLine']['receivedqty'],
                $shipment_id,
                $this->request->data['OrdersLine']['receivenotes']
            );
            if($response['status']) {
                // Update order status as partially processed
                $this->loadModel('Order');
                $this->Order->id = $orderline['OrdersLine']['order_id'];
                $this->Order->saveField('status_id', 3);

                $response['status'] = 'success';
                $response['message'] = 'Product SKU ' .$orderline['Product']['sku']. ',quantity ' .$this->request->data['OrdersLine']['receivedqty']. ', were received to stock.';
                // Update shipment status as partially processed
                $this->updateshipmentprocess($orderline['OrdersLine']['order_id'], false);
            }
            echo json_encode($response);
            exit;
        } else {
            $options = array('conditions' => array('OrdersLine.' . $this->OrdersLine->primaryKey => $id));
            $this->request->data = $this->OrdersLine->find('first', $options);
        }
    }

    public function receivealllines($id = null , $shipment_id = null)
    {
        $this->loadModel('Order');

        $options = array(
            'conditions' => array('Order.id' => $id),
            'contain' => array('OrdersLine', 'OrdersLine.Product' => array('fields' => array('id', 'name', 'packaging_material_id','receive_location')))
        );
        $order = $this->Order->find('first', $options);
        $type = $this->types[$order['Order']['ordertype_id']];
        $redirect = [];
        if($order['Order']['ordertype_id'] == 1) {
            $redirect['controller'] = 'salesorders';
        } else {
            $redirect['controller'] = 'replorders';
        }
        if($this->request->query('f') == 'd') {
            $redirect['action'] = 'details';
            $redirect[] = $id;
        } else {
            $redirect['action'] = 'index';
        }

        // check access
        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to release not own order
            $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
            $order_lines = $this->Order->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'contain' => array(),
                'recursive' => -1,
                'callbacks' => false
            ));
            if(count($order_lines) != count($order['OrdersLine'])) {
                $this->Session->setFlash(__('You can\'t receive all producst for order number %s. It have products for which you have no access.',$id),'admin/success',array());
                return $this->redirect($redirect);
            }
            $this->loadModel('Warehouse');
            $defaultwarehouse = $this->Warehouse->find('first', array('conditions' => array('Warehouse.user_id' => $order['Order']['user_id']), 'recursive' => -1, 'callbacks' => false));
            $default_warehouse = $defaultwarehouse['Warehouse']['id'];
        } else {
            $default_warehouse = $this->Session->read('default_warehouse');
        }

        $all_data_ord = [];
        $all_data_inv = [];
        $inv_qty = [];

        foreach($order['OrdersLine'] as $orderline) {
            $status = true;
            // 1 We need to be sure that inventory exists
            $inventory = $this->InventoryManager->getInventory($orderline['product_id'], $orderline['warehouse_id']);
            if(empty($inventory)) {
                // line can't be issued
                //$message = 'The product ' . $orderline['Product']['name'] . ' order line could not be issued. Please, try again.';
                $status = false;
            } else {
                // Try to issue line
                $data_ord = [];
                $data_ord['OrdersLine']['id'] = $orderline['id'];
                $data_ord['OrdersLine']['shipment_id'] = $shipment_id;
                $data_ord['OrdersLine']['sentqty'] = 0;
                $data_ord['OrdersLine']['receivedqty'] = $orderline['quantity'];
                $data_ord['OrdersLine']['dcop_user_id'] = $this->Auth->user('id');

                if(!isset($inv_qty[$inventory['Inventory']['id']])) {
                    $inv_qty[$inventory['Inventory']['id']] = $inventory['Inventory']['quantity'];
                }

                $data_inv = [];
                $inventoryoffset = ($orderline['receivedqty'] > 0 ? $orderline['quantity'] - $orderline['receivedqty'] : $orderline['quantity']);
                //$poststock = $inventory['Inventory']['quantity'] + $inventoryoffset;

                $poststock = $inv_qty[$inventory['Inventory']['id']] + $inventoryoffset;
                $inv_qty[$inventory['Inventory']['id']] = $inv_qty[$inventory['Inventory']['id']] + $inventoryoffset;

                //$damageqty = ($orderline['damagedqty'] > 0 ? $inventoryRecord['Inventory']['damaged_qty'] + $orderline['damagedqty'] : $orderline['damagedqty']);
                $data_inv['Inventory']['id'] = $inventory['Inventory']['id'];
                $data_inv['Inventory']['quantity'] = $poststock;
                //$data['Inventory']['damagedqty'] = $damageqty;
                $all_data_ord[] = $data_ord;
                $all_data_inv[] = $data_inv;
            }
        }
        if(count($all_data_ord)) {
            $this->loadModel('Inventory');
            $ds = $this->OrdersLine->getDataSource();
            $ds->begin();
            if($this->OrdersLine->saveAll($all_data_ord)) {
                if($this->Inventory->saveAll($all_data_inv)) {
                    $ds->commit();
                    if($status) {
                        $this->Order->id = $id;
                        $this->request->data('Order.status_id',4);
                        if ($this->Order->save($this->request->data)) {
                            $this->Session->setFlash(__('All products are received to stock and order status set to completed'), 'admin/success');
                            return $this->redirect($redirect);
                        }
                    } else {
                        $this->Session->setFlash(__('Part of products can\'t be received auto. Please try receive it manual.'), 'admin/danger');
                        return $this->redirect($redirect);
                    }
                } else {
                    $ds->rollback();
                    $this->Session->setFlash(__('Products can\'t be received. Please try againe later.'), 'admin/danger');
                    return $this->redirect($redirect);
                }
            }
        } else {
            $this->Session->setFlash(__('Products can\'t be received.'), 'admin/danger');
            return $this->redirect($redirect);
        }
    }

    public function add_receive_notes($line_id) {
        $this->layout = false;
        $this->OrdersLine->recursive = -1;
        $line = $this->OrdersLine->find('first', ['conditions' => array('OrdersLine.id' => $line_id)]);
        if($this->request->is('post') || $this->request->is('put')) {
            //$this->request->data['sentqty'] = $line['OrdersLine']['sentqty'];
            //$this->request->data['receivedqty'] = $line['OrdersLine']['receivedqty'];
            unset($this->OrdersLine->validate['sentqty']);
            if($this->OrdersLine->save($this->request->data)) {
                $response['action'] = 'success';
                $response['message'] = __('Remark successfully updated.');
            } else {
                $response['action'] = 'error';
                $response['message'] = __('The orders line could not be updated.');
                $response['errors'] = $this->OrdersLine->validationErrors;
            }
            echo json_encode($response);
            exit;
        } else {
            $this->request->data = $line;
        }
    }


    public function issue($id) {
        $order = $this->OrdersLine->Order->find('first', array(
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
            'limit' => 100,
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

        if(count($ordersLines) == 0 && $order['Order']['user_id'] != $this->Auth->user('id')) {
            $this->Session->setFlash(__('You have no access to issue any lines in this order'), 'admin/danger', array());
            return $this->redirect(array('controller'=>'salesorders', 'action' => 'details', $id));
        }
        if(count($ordersLines) != count($order['OrdersLine'])) {
            $can_complete = false;
        }

        $order_list = $this->OrdersLine->find('list', array('conditions'=>$conditions, 'contain' => ['Product'], 'fields'=>['Product.sku', 'OrdersLine.id']));
        
        $this->set(compact('ordersLines', 'order', 'order_list', 'is_write', 'warehouses', 'network', 'can_complete'));
    }

    public function issuelines() {
        $this->layout = 'mtrd';

        if ($this->request->is(array('post', 'put'))) {
            $id = $this->request->data['OrdersLine']['OrdersLineId'];
            $options = array(
                'conditions' => array('OrdersLine.id'  => $id),
                'contain' => array(
                    'Order' => array('fields' => 'id', 'user_id', 'status_id'),
                    'Product' => array('id', 'name', 'sku', 'packaging_material_id', 'receive_location', 'uom', 'status_id', 'deleted'),
                    'Warehouse' => array('id', 'name')
                )
            );
            $orderline = $this->OrdersLine->find('first', $options);

            if($orderline['Product']['status_id'] == 13 || $orderline['Product']['deleted'] == 1) {
                $response['action'] = 'error';
                $response['message'] = __('The orders line could not be issue, product blocked or deleted.');
                echo json_encode($response);
                exit;
            }

            $product = $orderline['Product'];
            $product_parts = [];
            $_is_kit = ($product['uom'] == 'Kit' && $this->_authUser['User']['kit_component_issue'] == 'issued');
            if($_is_kit) { // Get components
                $this->loadModel('Kit');
                $product_parts = $this->Kit->find('all',array(
                    'conditions' => array('Kit.product_id' => $product['id']),
                    'contain' => array('ProductPart')
                ));
            }

            // Get invenotry record
            $inventoryRecord = $this->InventoryManager->getInventory($orderline['OrdersLine']['product_id'], $this->request->data['OrdersLine']['warehouse_id']);
            $this->loadModel('Inventory');
            $totalQty = $this->Inventory->getQtyByLoc($orderline['OrdersLine']['product_id']);
            
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


            // If quantiy more then in invenotry
            $offset = $this->request->data['OrdersLine']['sentqty'] - $orderline['OrdersLine']['sentqty'];
            if(!$_is_kit && $inventoryRecord['Inventory']['quantity'] < $offset && $inventoryRecord['Inventory']['quantity'] >= 0 && !$this->Session->read('allow_negative') && !$this->request->data['OrdersLine']['confirm']) {
                $response['action'] = 'confirm';
                $response['line_id'] = $orderline['OrdersLine']['id'];
                $response['issue'] = $offset;
                $response['warehouse'] = $orderline['Warehouse']['name'];
                $response['inventory_qty'] = $inventoryRecord['Inventory']['quantity'];
                $response['all_inventory_qty'] = $totalQty;
                $response['message'] = 'You are trying to issue a quantity greater than the quantity you have in inventory.';
                echo json_encode($response);
                exit;
            }

            if($_is_kit) {
                foreach ($product_parts as $prdt) {
                    $offset = ($this->request->data['OrdersLine']['sentqty'] - $orderline['OrdersLine']['sentqty']) * $prdt['Kit']['quantity'];
                    if($partInventoryRecord[$prdt['ProductPart']['id']]['Inventory']['quantity'] < $this->request->data['OrdersLine']['sentqty'] * $prdt['Kit']['quantity'] && $partInventoryRecord[$prdt['ProductPart']['id']]['Inventory']['quantity'] >=0 && !$this->Session->read('allow_negative') && !$this->request->data['OrdersLine']['confirm']) {
                        $response['action'] = 'confirm';
                        $response['line_id'] = $orderline['OrdersLine']['id'];
                        $response['issue'] = $offset;
                        $response['warehouse'] = $orderline['Warehouse']['name'];
                        $response['inventory_qty'] = $partInventoryRecord[$prdt['ProductPart']['id']]['Inventory']['quantity'];
                        $response['all_inventory_qty'] = $totalQty;
                        $response['message'] = 'You are trying to issue a quantity greater than the quantity you have in inventory for one from component.';
                        echo json_encode($response);
                        exit;
                    }
                }
            }


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
                // Update order status as partially processed
                if(isset($orderline['Order']) && $orderline['Order']['status_id'] != 3) {
                    $this->loadModel('Order');
                    $this->Order->id = $orderline['OrdersLine']['order_id'];
                    $this->Order->saveField('status_id', 3);

                    $this->EventRegister->addEvent(2,3,$this->Auth->user('id'),$this->Order->id);
                }

                $response['action'] = 'success';
                $response['message'] = __('Product SKU %s, quantity %s, were issued.', $orderline['Product']['sku'],$this->request->data['OrdersLine']['sentqty']);
                // Update shipment status as partially processed
                $this->updateshipmentprocess($orderline['OrdersLine']['order_id']);
            } else {
                $response['action'] = 'error';
            }
            echo json_encode($response);
            exit;
        } else {
            $options = array('conditions' => array('OrdersLine.' . $this->OrdersLine->primaryKey => $id));
            $this->request->data = $this->OrdersLine->find('first', $options);
        }
    }

    public function multiissue_report() {
        $this->layout = false;
        $orders = 1;
        $this->set(compact('orders'));
    }

    /**
     * Multi Sales Order Send All Lines
     * issuealllines
     */
    public function multiissue($id = null, $shipment_id = null) {
        $this->layout = false;
        $this->loadModel('Order');

        $redirect = [];
        $redirect['controller'] = 'salesorders';
        $redirect['action'] = 'index';

        $type = 1;
        if($this->request->is('post')) {
            $success = [];
            $access_alert = [];
            $negativ_alert = [];
            $error_alert = [];
            $part_alert = [];
            
            if(!empty($this->request->data['Order']['id'])) {
                $ids = explode(',', $this->request->data['Order']['id']);

                foreach ($ids as $id) {
                    $options = array(
                        'conditions' => array('Order.id' => $id, 'Order.status_id IN' => array(2,3)),
                        'contain' => array('OrdersLine', 'OrdersLine.Product' => array('fields' => array('id', 'name', 'issue_location', 'status_id', 'deleted'))),
                        'recursive' => 2,
                        'callbacks' => false
                    );
                    $order = $this->Order->find('first', $options);

                    if($order) {
                        // check access
                        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to release not own order
                            $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
                            $order_lines = $this->Order->OrdersLine->find('all', array(
                                'order' => array('OrdersLine.line_number' => 'asc'),
                                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                                'contain' => array(),
                                'recursive' => -1,
                                'callbacks' => false
                            ));
                            if(count($order_lines) != count($order['OrdersLine'])) {
                                // User have no access to issue this order, add alert and skip
                                $access_alert[] = $id;
                                continue;
                            }
                            $this->loadModel('Warehouse');
                            $defaultwarehouse = $this->Warehouse->find('first', array('conditions' => array('Warehouse.user_id' => $order['Order']['user_id']), 'recursive' => -1, 'callbacks' => false));
                            $default_warehouse = $defaultwarehouse['Warehouse']['id'];
                        } else {
                            $default_warehouse = $this->Session->read('default_warehouse');
                        }

                        $all_data_ord = [];
                        $all_data_inv = [];
                        $inv_qty = [];
                        
                        foreach($order['OrdersLine'] as $orderline) {
                            //pr($orderline);
                            if($orderline['Product']['status_id'] == 13 || $orderline['Product']['deleted'] == 1) {
                                $error_alert[] = $id;
                                $all_data_ord = [];
                                break;
                            }
                            // 1 We need to be sure that inventory exists

                            $inventory = $this->InventoryManager->getInventory($orderline['product_id'], $orderline['warehouse_id']);
                            if(empty($inventory)) {
                                // line can't be issued
                                $error_alert[] = $id;
                                $all_data_ord = [];
                                break;
                            }

                            // Try to issue line
                            $data_ord = [];
                            $data_ord['OrdersLine']['id'] = $orderline['id'];
                            $data_ord['OrdersLine']['shipment_id'] = $shipment_id;
                            $data_ord['OrdersLine']['sentqty'] = $orderline['quantity'];
                            $data_ord['OrdersLine']['dcop_user_id'] = $this->Auth->user('id');
                            $all_data_ord[] = $data_ord;
                            
                            unset($inventoryoffset);
                            unset($poststock);
                            
                            if(!isset($inv_qty[$inventory['Inventory']['id']])) {
                                $inv_qty[$inventory['Inventory']['id']] = $inventory['Inventory']['quantity'];
                            }
                            
                            $inventoryoffset = ($orderline['sentqty'] > 0 ? $orderline['quantity'] - $orderline['sentqty'] : $orderline['quantity']);
                            $poststock = $inv_qty[$inventory['Inventory']['id']] - $inventoryoffset;
                            $inv_qty[$inventory['Inventory']['id']] = $inv_qty[$inventory['Inventory']['id']] - $inventoryoffset;
                            if($poststock < 0 && !$this->Session->read('allow_negative') && !$this->request->query('confirm') && $inventoryoffset > 0) {
                                $negativ_alert[] = $id;
                                $all_data_ord = [];
                                break;
                            }
                            
                            $data_inv = [];
                            $data_inv['Inventory']['id'] = $inventory['Inventory']['id'];
                            $data_inv['Inventory']['quantity'] = $poststock;
                            
                            $all_data_inv[$data_inv['Inventory']['id']] = $data_inv;
                        }

                        if(count($all_data_ord)) {
                            $this->loadModel('Inventory');
                            $ds = $this->OrdersLine->getDataSource();
                            $ds->begin();
                            if($this->OrdersLine->saveAll($all_data_ord)) {
                                if($this->Inventory->saveAll($all_data_inv)) {
                                    $ds->commit();
                                    $this->EventRegister->addEvent(2,2,$this->Auth->user('id'),$id);
                                    $success[] = $id;
                                } else {
                                    $ds->rollback();
                                    $error_alert[] = $id;
                                    #$response['msg'] = __('Order lines can\'t be issued. Please try againe later.');
                                }
                            }
                        } else {
                            //$error_alert[] = $id;
                            #$response['msg'] = __('Order lines can\'t be issued.');
                        }
                    }
                }

                $response['action'] = 'success';
                $response['success'] = $success;
                $response['access_alert'] = $access_alert;
                $response['negativ_alert'] = $negativ_alert;
                $response['error_alert'] = $error_alert;
                $response['part_alert'] = $part_alert;
                
                echo json_encode($response);
                exit;
            }
        }

        $response['action'] = 'error';
        $response['msg'] = 'Method not allowed';
        echo json_encode($response);
        exit;
    }


    /**
     * Sales Order Send All Lines
     * issuealllines
     */
    public function issuealllines_new($order_id = null, $shipment_id = null) {
        $this->layout = 'mtrd';
        $this->loadModel('Order');

        $options = array(
            'conditions' => array('Order.id' => $order_id),
            'contain' => array('OrdersLine', 'OrdersLine.Product' => array('fields' => array('id', 'name', 'issue_location', 'uom', 'status_id', 'deleted'))),
        );
        $order = $this->Order->find('first', $options);

        // Check user access to order here
        if($order['Order']['user_id'] != $this->Auth->user('id')) {
            $products = $this->Access->getProducts('S.O.', 'w', $order['Order']['user_id']);
            $warehouses = $this->Access->locationList('S.O.', false, false, $order['Order']['user_id']);

            if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
                $this->Session->setFlash(__('You do not have access to issue lines for this order.'), 'admin/success');
                return $this->redirect($redirect);
            }
            if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
                $this->Session->setFlash(__('You do not have access to issue lines for this order.'), 'admin/success');
                return $this->redirect($redirect);
            }
            foreach ($warehouses as $w) {
                if(strpos($w, 'w') !== 0) {
                    $is_write = 1;
                }
            }

            $allowed_list = $this->OrdersLine->find('list', array(
                'fields' => array('OrdersLine.id'),
                'conditions' => array('OrdersLine.order_id' => $order_id, 'OrdersLine.product_id' => array_keys($products)),
                'contain' => false
            ));
        }

        $result = [];
        foreach($order['OrdersLine'] as $orderline) {
            if($order['Order']['user_id'] == $this->Auth->user('id') || in_array($orderline['id'], $allowed_list)) {
                $offset = ($orderline['sentqty'] > 0 ? $orderline['quantity'] - $orderline['sentqty'] : $orderline['quantity']);
                $result[$orderline['id']] = $this->InventoryManager->issueLineFull($orderline, $offset, $orderline['warehouse_id'], $orderline['shipment_id']);
            } else {
                // Do we need notify not owner that part of lines which they can't see not issued because of they have no access to it??
            }
        }
#pr($result);
        if ($this->request->is('ajax')) {
            echo json_encode($response);
            exit;
        } else {
            $st = $response['action'];
            if($response['action'] == 'error') {
                $st = 'danger';
            }
            $redirect = $this->Account->orderRedirectUrl($order);

            $this->Session->setFlash($response['msg'],'admin/'.$st);
            return $this->redirect($redirect);
        }
    }
    /**
     * Sales Order Send All Lines
     * issuealllines
     */
    public function issuealllines($id = null, $shipment_id = null) {
        $this->layout = 'mtrd';
        $this->loadModel('Order');

        $options = array(
            'conditions' => array('Order.id' => $id),
            'contain' => array('OrdersLine', 'OrdersLine.Product' => array('fields' => array('id', 'name', 'issue_location', 'uom', 'status_id', 'deleted'))),
            'recursive' => 2,
            'callbacks' => false
        );
        $order = $this->Order->find('first', $options);

        $type = $this->types[$order['Order']['ordertype_id']];
        $redirect = [];
        if($order['Order']['ordertype_id'] == 1) {
            $redirect['controller'] = 'salesorders';
        } else {
            $redirect['controller'] = 'replorders';
        }
        if($this->request->query('f') == 'd') {
            $redirect['action'] = 'details';
            $redirect[] = $id;
        } else {
            $redirect['action'] = 'index';
        }

        // check access
        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to release not own order
            $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
            $order_lines = $this->Order->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'contain' => array(),
                'recursive' => -1,
                'callbacks' => false
            ));
            if(count($order_lines) != count($order['OrdersLine'])) {
                $this->Session->setFlash(__('You can\'t issue all producst for order number %s. It have products for which you have no access.',$id),'admin/success',array());
                return $this->redirect($redirect);
            }
            $this->loadModel('Warehouse');
            $defaultwarehouse = $this->Warehouse->find('first', array('conditions' => array('Warehouse.user_id' => $order['Order']['user_id']), 'recursive' => -1, 'callbacks' => false));
            $default_warehouse = $defaultwarehouse['Warehouse']['id'];
        } else {
            $default_warehouse = $this->Session->read('default_warehouse');
        }

        $all_data_ord = [];
        $all_data_inv = [];
        $inv_qty = [];
        $status = true;
        
        foreach($order['OrdersLine'] as $orderline) {
            if($orderline['Product']['status_id'] == 13 || $orderline['Product']['deleted'] == 1) {
                $status = false;
                continue;
            }
            
            // Skip complex products with components
            $product_parts = [];
            if($orderline['Product']['uom'] == 'Kit' && $this->_authUser['User']['kit_component_issue'] == 'issued') {
                /*$product_parts = $this->Kit->find('all',array(
                    'conditions' => array('Kit.product_id' => $product['id']),
                    'contain' => array('ProductPart')
                ));*/
                $status = false;
                continue;

                #$this->InventoryManager->issueLineFull($orderline['id']);
            }

            // 1 We need to be sure that inventory exists
            $inventory = $this->InventoryManager->getInventory($orderline['product_id'], $orderline['warehouse_id']);
            if(empty($inventory)) {
                // line can't be issued
                $status = false;
                continue;
            }

            // Try to issue line
            $data_ord = [];
            $data_ord['OrdersLine']['id'] = $orderline['id'];
            $data_ord['OrdersLine']['shipment_id'] = $shipment_id;
            $data_ord['OrdersLine']['sentqty'] = $orderline['quantity'];
            $data_ord['OrdersLine']['dcop_user_id'] = $this->Auth->user('id');
            $all_data_ord[] = $data_ord;
            
            unset($inventoryoffset);
            unset($poststock);
            
            if(!isset($inv_qty[$inventory['Inventory']['id']])) {
                $inv_qty[$inventory['Inventory']['id']] = $inventory['Inventory']['quantity'];
            }
            
            $inventoryoffset = ($orderline['sentqty'] > 0 ? $orderline['quantity'] - $orderline['sentqty'] : $orderline['quantity']);
            $poststock = $inv_qty[$inventory['Inventory']['id']] - $inventoryoffset;
            $inv_qty[$inventory['Inventory']['id']] = $inv_qty[$inventory['Inventory']['id']] - $inventoryoffset;
            if ($this->request->is('ajax')) {
                if($poststock < 0 && !$this->Session->read('allow_negative') && !$this->request->query('confirm') && $inventoryoffset > 0) {
                    $response['action'] = 'confirm';
                    echo json_encode($response);
                    exit;
                }
            }
            
            $data_inv = [];
            $data_inv['Inventory']['id'] = $inventory['Inventory']['id'];
            $data_inv['Inventory']['quantity'] = $poststock;
            
            $all_data_inv[$data_inv['Inventory']['id']] = $data_inv;
        }

        if(count($all_data_ord)) {
            $this->loadModel('Inventory');
            $ds = $this->OrdersLine->getDataSource();
            $ds->begin();
            if($this->OrdersLine->saveAll($all_data_ord)) {
                if($this->Inventory->saveAll($all_data_inv)) {
                    $ds->commit();
                    if($status) {
                        $this->Order->id = $id;
                        if($this->request->query('status_id')) {
                            $this->request->data('Order.status_id', 3);
                            $this->EventRegister->addEvent(2,3,$this->Auth->user('id'),$this->Order->id);
                            $msg = __('All products are issued for order #%s', $this->Order->id);
                        } else {
                            $this->request->data('Order.status_id', 4);
                            $this->EventRegister->addEvent(2,4,$this->Auth->user('id'),$this->Order->id);
                            $msg = __('All products are issued and order status set to completed');
                        }
                        if ($this->Order->save($this->request->data)) {
                            //$this->Session->setFlash($msg, 'admin/success');
                            //return $this->redirect($redirect);
                            $response['action'] = 'success';
                            $response['status_id'] = $this->request->data['Order']['status_id'];
                            $response['msg'] = $msg;
                        }
                    } else {
                        //$this->Session->setFlash(__('Part of products can\'t be issued auto. Please try issue it manual.'), 'admin/danger');
                        //return $this->redirect($redirect);
                        $response['action'] = 'error';
                        $response['msg'] = __('Part of products can\'t be issued auto. Please try issue it manual.');
                    }
                } else {
                    $ds->rollback();
                    #$this->Session->setFlash(__('Order lines can\'t be issued. Please try againe later.'), 'admin/danger');
                    #return $this->redirect($redirect);
                    $response['action'] = 'error';
                    $response['msg'] = __('Order lines can\'t be issued. Please try againe later.');
                }
            }
        } else {
            #$this->Session->setFlash(__('Order lines can\'t be issued.'), 'admin/danger');
            #return $this->redirect($redirect);
            $response['action'] = 'error';
            $response['msg'] = __('Order lines can\'t be issued.');
        }
        if ($this->request->is('ajax')) {
            echo json_encode($response);
            exit;
        } else {
            $st = $response['action'];
            if($response['action'] == 'error') {
                $st = 'danger';
            }
            $this->Session->setFlash($response['msg'],'admin/'.$st);
            return $this->redirect($redirect);
        }
    }

    public function issueallconfirm($id = null) {
        $this->layout = false;
        $this->loadModel('Order');

        $options = array(
            'conditions' => array('Order.id' => $id),
            'contain' => array('OrdersLine', 'OrdersLine.Product' => array('fields' => array('id', 'name', 'issue_location', 'status_id', 'deleted'))),
            'recursive' => 2,
            'callbacks' => false
        );
        $order = $this->Order->find('first', $options);

        $type = $this->types[$order['Order']['ordertype_id']];
        $redirect = [];
        if($order['Order']['ordertype_id'] == 1) {
            $redirect['controller'] = 'salesorders';
        } else {
            $redirect['controller'] = 'replorders';
        }
        if($this->request->query('f') == 'd') {
            $redirect['action'] = 'details';
            $redirect[] = $id;
        } else {
            $redirect['action'] = 'index';
        }

        // check access
        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to release not own order
            $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
            $order_lines = $this->Order->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'contain' => array(),
                'recursive' => -1,
                'callbacks' => false
            ));
            if(count($order_lines) != count($order['OrdersLine'])) {
                $this->Session->setFlash(__('You can\'t issue all producst for order number %s. It have products for which you have no access.',$id),'admin/success',array());
                return $this->redirect($redirect);
            }
            $this->loadModel('Warehouse');
            $defaultwarehouse = $this->Warehouse->find('first', array('conditions' => array('Warehouse.user_id' => $order['Order']['user_id']), 'recursive' => -1, 'callbacks' => false));
            $default_warehouse = $defaultwarehouse['Warehouse']['id'];
        } else {
            $default_warehouse = $this->Session->read('default_warehouse');
        }

        $negative_inv = [];
        $status = true;
        foreach($order['OrdersLine'] as $orderline) {
            if($orderline['Product']['status_id'] == 13 || $orderline['Product']['deleted'] == 1) {
                $status = false;
                continue;
            }
            // 1 We need to be sure that inventory exists

            $inventory = $this->InventoryManager->getInventory($orderline['product_id'], $orderline['warehouse_id'], ['Inventory.id', 'Inventory.quantity', 'Product.name', 'Warehouse.name']);
            if(empty($inventory)) {
                // line can't be issued
                $status = false;
                continue;
            }

            unset($inventoryoffset);
            unset($poststock);
            $inventoryoffset = ($orderline['sentqty'] > 0 ? $orderline['quantity'] - $orderline['sentqty'] : $orderline['quantity']);
            $poststock = $inventory['Inventory']['quantity'] - $inventoryoffset;
            if($poststock < 0) {
                $inventory['Inventory']['offset'] = $inventoryoffset;
                $negative_inv[] = $inventory;
            }
        }

        $this->set(compact('order', 'negative_inv'));
    }

    public function exceptionlines() {
        $conditions = array($type = null);
        if ($this->Auth->user('id')) {
            $conditions['OrdersLine.user_id'] = $this->Auth->user('id');
        } else {
            die();
        }

        $conditions['OrdersLine.type'] =  1;
        $conditions['OrdersLine.status_id'] =  0;
        $conditions['OrdersLine.product_id'] =  0;

        //$this->paginate['conditions'] = $conditions;
        //$this->paginate['ordersline'] = array('OrdersLine.created' => 'desc');
        $this->set('orderslines',$this->paginate($conditions));
   }

    
    public function subscribe() {
        $this->layout = 'mtrd';
    }

    public function productmob() {
        $this->layout = 'mob';
        $x=0;
        $t=0;
        $fd = $this->Auth->user('id');
        $this->loadModel('Product');
        $this->loadModel('Order');
        $this->loadModel('OrdersLine');
        $this->loadModel('User');
        $this->loadModel('Inventory');

        if(isset($this->request->params['pass'][0])) {
            if($this->request->params['pass'][0] == 'XdC52') {
                $uid = '5429dac8-2950-47ba-b754-29216baa15de';
            } else if($this->request->params['pass'][0] == 'ZZDDEE') {
                $uid = '53ebdbef-0d8c-489c-a2d5-1148e9d568ab';
            } else if($this->request->params['pass'][0] == 'ZuP83') {
                $uid = '53ebd4c3-5690-41c1-b3b9-23d46baa15de';
            } else {
                $this->redirect('http://www.google.com');
            }
        } else {
            $this->redirect('http://www.google.com');
        }

        $user = $this->User->find('first',array('conditions' => array('User.id'  =>$uid)));
        $this->User->id = $user['User']['id'];
        $products=$user['Product'];


        $inventoryRecords = $this->Inventory->find('all',array('fields' => array('Inventory.product_id','Inventory.quantity')));
        foreach ($inventoryRecords as $inventoryRecord) {
            $invarr[$inventoryRecord['Inventory']['product_id']] = $inventoryRecord['Inventory']['quantity'];
        }
        
        foreach ($products as $product) {
            $prodmobarr[$x]['name'] = $product['name'];
            $options = array('conditions' => array('OrdersLine.product_id'  => $product['id'],'OrdersLine.type' => 2));
            $orderline = $this->OrdersLine->find('first', $options);
            //$this->Inventory->id = $inventoryRecord['Inventory']['id'];
            $prodmobarr[$x]['inventory'] = $invarr[$product['id']];

            if(!empty($orderline)) {
                $prodmobarr[$x]['order_quantity'] = $orderline['OrdersLine']['quantity'];
                $prodmobarr[$x]['order_duedate'] = $orderline['Order']['requested_delivery_date'];
            } else {
                $prodmobarr[$x]['order_quantity'] = 0;
                $prodmobarr[$x]['order_duedate'] = 'None';
            }
            if(isset($product['value'])) {
                $prodmobarr[$x]['price'] = $product['value'];
            } else {
                $prodmobarr[$x]['price'] = 0;
            }
            $prodmobarr[$x]['sku'] = $product['sku'];

            if(isset($product['imageurl'])) {
                $prodmobarr[$x]['image'] = $product['imageurl'];
            } else {
                $prodmobarr[$x]['image'] = '';
            }

            if(isset($product['safety_stock'])) {
                $prodmobarr[$x]['safety_stock'] = $product['safety_stock'];
            } else {
                $prodmobarr[$x]['safety_stock'] = 0;
            }

            $prodmobarr[$x]['sku'] = $product['sku'];
            $x++;
        }
        $this->set(compact('prodmobarr'));
    }

    public function addpackmaterial($ordid = null, $packpid = null, $qty = 1) {
        //Get pack material price from product master data
        $this->layout = 'mtrd';
        $packmaterialprice = $this->OrdersLine->Product->find('first', array('fields' => array('Product.value'),'conditions' => array('Product.id' => $packpid)));
        $data = array(
            'OrdersLine' => array(
                'order_id' => $ordid,
                'line_number' => 999999,
                'type' => 7,
                'product_id'  => $packpid,
                'warehouse_id'  => $this->Session->read('default_warehouse'),
                'quantity' => $qty,
                'unit_price' => $packmaterialprice['Product']['value'],
                'total_line' => $packmaterialprice['Product']['value']*$qty,
                'user_id' => $this->Auth->user('id')
            )
            );

        // prepare the model for adding a new entry
        $this->OrdersLine->create();
        // save the data
        $this->OrdersLine->save($data);
    }

    public function updateshipmentprocess($id, $is_msg = true) {
        $this->loadModel('Shipment');
        $shipmentdata = $this->Shipment->find('first', array('conditions' => array('Shipment.order_id' => $id,'Shipment.user_id' => $this->Auth->user('id'))));
        if(!empty($shipmentdata)) {
            if($shipmentdata['Shipment']['status_id'] != 16) {
                $this->Shipment->id = $shipmentdata['Shipment']['id'];
                $this->request->data('Shipment.status_id',16);
                $this->EventRegister->addEvent(4,16,$this->Auth->user('id'),$this->Shipment->id);
                if ($this->Shipment->save($this->request->data)) {
                    if($is_msg) {
                        $this->Session->setFlash(__('Shipment status could not be updated.'), 'admin/danger', array());
                    }
                }

            }
        }
    }

    public function sel2t() {
        $this->layout = 'select2t';
        echo "";
    }

    public function fix() {
        $this->layout = 'mtrd';
        $this->OrdersLine->recursive = 3;

        $this->Paginator->settings = array(
            'limit' => 10
        );

        $conditions = array($type = null);
        $conditions['OrdersLine.user_id'] = $this->Auth->user('id');
        $conditions['OrdersLine.status_id'] =  1;
        $conditions['OrdersLine.type'] =  1;
        $this->set('orderslines',$this->paginate($conditions));
    }

    public function getproductwhs($pid = null) {
        $this->loadModel('Product');
        $whs_inv_records = $this->Product->find('first', array(
            'fields' => array('Product.issue_location'),
            'conditions' => array('Product.id' => $pid, 'Product.user_id' => $this->Auth->user('id')),
            'recursive' => -1
        ));

        if(!empty($whs_inv_records['Product']['issue_location'])) {
            $location = $whs_inv_records['Product']['issue_location'];
        } else {
            $location = $this->Session->read('default_warehouse');
        }

        $warehouses = $this->Product->Warehouse->find('list',array('conditions' => array('Warehouse.id' => $location,'Warehouse.user_id' => $this->Auth->user('id'))));
        return $warehouses;
    }

    public function getorderlines($id) {
        $this->layout = false;

        $currentOrder = $this->OrdersLine->Order->find('first', array(
            'fields' => array('Order.id','Order.ordertype_id', 'Order.user_id', 'Order.dcop_user_id', 'User.currency_id'),
            'contain' => array('User', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
            'conditions' => array('Order.id' => $id)
        ));

        if($currentOrder['Order']['user_id'] != $this->Auth->user('id')) {
            $products = $this->Access->getProducts('S.O.', 'w', $currentOrder['Order']['user_id']);

            $currentlines = $this->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'callbacks' => false
            ));
        } else {
            $currentlines = $this->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id),
                'callbacks' => false
            ));
        }
        $this->set(compact('currentlines', 'currentOrder'));
    }

    public function releaseorderlines($id) {
        $this->layout = false;
        $currentlines = $this->OrdersLine->find('all', array(
            'order' => array('OrdersLine.line_number' => 'asc'),
            'conditions' => array('OrdersLine.order_id' => $id),
            'callbacks' => false
        ));
        $currentOrder = $this->OrdersLine->Order->find('first', array(
            'fields' => array('Order.id','Order.ordertype_id'),
            'conditions' => array('Order.id' => $id),
            'callbacks' => false
        ));
        $this->set(compact('currentlines', 'currentOrder'));
    }

    public function deleteorderline() {
        $this->OrdersLine->id = $this->request->data['id'];
        $currentOrderLine = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.' . $this->OrdersLine->primaryKey => $this->request->data['id'])));

        if ($this->OrdersLine->delete()) {
            $response['status'] = true;
            $response['message'] = 'Order line number has been deleted ';

        } else {
            $response['status'] = false;
            $response['message'] = 'The orders line could not be deleted';

        }
        echo json_encode($response);
        exit;
    }

    public function saveorderline($order_id) {
        $this->request->data['OrdersLine']['order_id'] = $order_id;
        if ($this->request->is('post')) {
            if ($this->OrdersLine->save($this->request->data)) {
                $response['status'] = true;
            } else {
                $response['status'] = false;
            }
        }
    }

    public function updateRemarks() {
        $response['status'] = false;
        if($this->request->data) {
            $orderline = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.id' => $this->request->data['pk']), 'recursive' => -1));
            if(!empty($orderline)) {
                $this->OrdersLine->id = $orderline['OrdersLine']['id'];
                 if ($this->OrdersLine->saveField('comments', $this->request->data['value'])) {
                    $response['status'] = true;
                 } else {
                    $response['status'] = false;
                 }  
            }
        }
        echo json_encode($response);
        exit;
    }
}