<?php
App::uses('AppController', 'Controller');
App::uses('AdminHelper', 'View/Helper');
App::uses('CakeTime', 'Utility');
/**
 * OrdersCosts Controller
 *
 * @property OrdersCosts $OrdersCosts
 * @property PaginatorComponent $Paginator
 */
class OrdersBlanketController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator','InventoryManager','EventRegister');
    public $helpers = array('Session','Time');
    public $theme = 'Mtro';

    /**
    * Models
    *
    * @var array
    */
    public $uses = array('OrdersBlanket', 'Order', 'OrdersLine', 'Currency', 'Product', 'User');

    public $types = [1 => 'S.O.', 2 => 'P.O.'];

    public function __beforeFilter() {
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
     * add_blanket method
     *
     * @throws NotFoundException
     * @param int $order_id
     * @return void
     */
    public function add_blanket($id) {
        $this->layout = false;
        $order = $this->Order->find('first', array(
            'fields' => array('Order.id','Order.ordertype_id', 'Order.user_id', 'Order.dcop_user_id', 'Order.external_orderid', 'User.currency_id'),
            'contain' => array('User'),
            'conditions' => array('Order.id' => $id)
        ));
        if(!$order) {
            throw new NotFoundException(__('Order not found'));
        }
        
        $is_own = ($order['Order']['user_id'] == $this->Auth->user('id'));
        if(!$is_own) { // Check access, get products and allowed warehouse
            $allowedstatusesrepl = [1,12];
            $products = $this->Access->getProducts($this->type, 'w', $order['Order']['user_id'], ['Product.status_id' => $allowedstatusesrepl]);
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $order['Order']['user_id']);
            if((empty($warehouses)) && $this->Auth->user('is_limited')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
            if((empty($warehouses)) && !$this->Auth->user('paid')) {
                throw new MethodNotAllowedException(__('Have no access'));
            }
        } else {
            $allowedstatusesrepl = [1,12];
            $products = $this->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id') , 'Product.status_id' => $allowedstatusesrepl)));
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $order['Order']['user_id']);
        }

        $warehouses = array_values($warehouses);
        $warehouses = $warehouses[0];

        if($this->request->is('post')) {
            if($this->request->data['OrdersBlanket']['quantity'] > 0 && isset($this->request->data['OrdersBlanket']['product_id'])) {
                $this->OrdersBlanket->create();
        
                $product = $this->Product->find('first', array('conditions' => array('Product.id' => $this->data['OrdersBlanket']['product_id']), 'callbacks' => false));

                $this->loadModel('Inventory');
                $inventoryRecord = $this->InventoryManager->getInventory($this->request->data['OrdersBlanket']['product_id'], $this->request->data['OrdersBlanket']['warehouse_id']);
                if(!$inventoryRecord) {
                    $response['status'] = false;
                    $response['message'] = 'We can\'t get invntory record for this location.';
                    echo json_encode($response);
                    exit;
                }
                
                ($this->data['OrdersBlanket']['unit_price']) ? $this->request->data('OrdersBlanket.unit_price', $this->request->data['OrdersBlanket']['unit_price']) : $this->request->data('OrdersBlanket.unit_price', 0);
                $this->request->data('OrdersBlanket.status_id',1);
                $this->request->data('OrdersBlanket.order_id', $id);
                $this->request->data('OrdersBlanket.type', $order['Order']['ordertype_id']);
                $this->request->data('OrdersBlanket.sku', $product['Product']['sku']);
                $this->request->data('OrdersBlanket.user_id', $order['Order']['user_id']);
                $this->request->data('OrdersBlanket.dcop_user_id', $this->Auth->user('id'));
                $this->request->data('OrdersBlanket.sentqty',0);
                $this->request->data('OrdersBlanket.receivedqty', 0);
                $this->request->data('OrdersBlanket.serial_id', 0);
                //$this->request->data('OrdersBlanket.warehouse_id', $this->request->data['OrdersBlanket']['warehouse_id']);
                $line_total = $this->data['OrdersBlanket']['quantity'] * $this->request->data['OrdersBlanket']['unit_price'];
                $this->request->data('OrdersBlanket.total_line',$line_total);
        
                if ($this->OrdersBlanket->save($this->request->data)) {
                    $response['row'] = $this->OrdersBlanket->find('first', ['conditions' => ['OrdersBlanket.id' => $this->OrdersBlanket->id]]);
                    $response['action'] = 'success';
                    $response['message'] = 'The orders blanket line has been added';
                    echo json_encode($response);
                    exit;
                } else {
                    $response['status'] = false;
                    $response['message'] = 'The orders blanket line could not be added. Please, try again.';
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

        //$linecount = count($order['OrdersBlanket']);
        $currency = $this->Currency->find('first', array('conditions' => array('id' => $order['User']['currency_id'])));
        $this->set(compact('order', 'warehouses', 'products', 'currency'));
    }

    /**
     * edit blanket line method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $this->layout = false;
        $options = array('conditions' => array('OrdersBlanket.id' => $id), 'contain' => array('User', 'Order'));
        $order_line = $this->OrdersBlanket->find('first', $options);
        
        if($this->Auth->user('id') == $order_line['OrdersBlanket']['user_id']) {
            $products = $this->OrdersBlanket->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.status_id' => 1)));
        } else {
            $products = $this->Access->getProducts($this->type, 'w', $order_line['OrdersBlanket']['user_id']);
        }
        $warehouses = $this->Access->getLocations($this->type, false, 'w', $order_line['OrdersBlanket']['user_id']);

        if((empty($products) || empty($warehouses)) && $this->Auth->user('is_limited')) {
            throw new MethodNotAllowedException(__('Have no access'));
        }
        if((empty($products) || empty($warehouses)) && !$this->Auth->user('paid')) {
            throw new MethodNotAllowedException(__('Have no access'));
        }
        
        if (!$this->OrdersBlanket->exists($id)) {
            throw new NotFoundException(__('Invalid orders line'));
        }

        $warehouses = array_values($warehouses);
        $warehouses = array_values($warehouses);
        $warehouses = $warehouses[0];

        if ($this->request->is(array('post', 'put'))) {
            if ($this->data['OrdersBlanket']['product_id'] != $order_line['OrdersBlanket']['product_id'] || $this->data['OrdersBlanket']['warehouse_id'] != $order_line['OrdersBlanket']['warehouse_id'] ) {
                $product = $this->Product->find('first', array('conditions' => array('Product.id' => $this->data['OrdersBlanket']['product_id']), 'callbacks' => false));

                $this->loadModel('Inventory');
                $inventoryRecord = $this->InventoryManager->getInventory($this->request->data['OrdersBlanket']['product_id'], $this->request->data['OrdersBlanket']['warehouse_id']);
                if(!$inventoryRecord) {
                    $response['status'] = false;
                    $response['message'] = 'We can\'t get invntory record for this location.';
                    echo json_encode($response);
                    exit;
                }
            }
            // recalculate total after update of price or qty
            $line_total = $this->data['OrdersBlanket']['quantity'] * $this->data['OrdersBlanket']['unit_price'];
            $this->request->data('OrdersBlanket.total_line', $line_total);
            //$this->request->data['OrdersBlanket']['sentqty'] = 0;
            if(!empty($product)) {
                $this->request->data('OrdersBlanket.sku', $product['Product']['sku']);
            }
            $this->request->data('OrdersBlanket.dcop_user_id', $this->Auth->user('id'));

            if ($this->OrdersBlanket->save($this->request->data)) {
                $response['row'] = $this->OrdersBlanket->find('first', ['conditions' => ['OrdersBlanket.id' => $id], 'contain' => array('Product', 'Warehouse')]);
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
        
        $user = $this->User->find('first', ['conditions' => ['User.id' => $order_line['OrdersBlanket']['user_id']], 'fields' => array('User.*'), 'contain' => false]);
        $currency = $this->Currency->find('first', array('conditions' => array('id' => $user['User']['currency_id'])));
        $this->set(compact('products', 'warehouses', 'currency', 'order_line'));
    }

    /**
     * delete line method
     *
     * @throws NotFoundException
     * @param in $id
     * @return void
     */
    public function delete($id) {
        $this->OrdersBlanket->id = $id;
        $current = $this->OrdersBlanket->find('first', array('conditions' => array('OrdersBlanket.id' => $id)));
        if (!$current) {
            throw new NotFoundException(__('Invalid orders line'));
        }

        if ($this->OrdersBlanket->delete()) {
            $response['action'] = 'success';
            $response['line_id'] = $id;
            $response['message'] = __('Order blanket line has been deleted');

        } else {
            $response['action'] = 'success';
            $response['message'] = __('The orders blanket line could not be deleted. Please, try again.');
        }
        echo json_encode($response);
        exit;
    }

    public function receive($id, $shipment_id = 0) {
        $this->layout = false;
        $blanketline = $this->OrdersBlanket->find('first', array('conditions' => array('OrdersBlanket.id' => $id)));

        if (!$blanketline) {
            $response['status'] = false;
            $response['message'] = 'Line not found';
            echo json_encode($response);
            exit;
        }

        
        $product = $blanketline['Product'];

        if($blanketline['OrdersBlanket']['user_id'] != $this->Auth->user('id')) {
            $products = $this->Access->getProducts($this->type, 'w', $blanketline['OrdersBlanket']['user_id']);
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $blanketline['OrdersBlanket']['user_id']);
            if(!array_key_exists($blanketline['OrdersBlanket']['product_id'], $products)) {
                $response['status'] = false;
                $response['message'] = 'Have no access';
                echo json_encode($response);
                exit;
            }
        } else {
            $warehouses = $this->Access->getLocations($this->type, false, 'w', $blanketline['OrdersBlanket']['user_id']);
        }

        if($this->request->is(array('post', 'put'))) {
            // Check quantity
            if(($blanketline['OrdersBlanket']['receivedqty'] + $this->request->data['OrdersBlanket']['new_receivedqty']) > $blanketline['OrdersBlanket']['quantity']) {
                $response['status'] = false;
                $response['message'] = 'You can\'t receive more than '. $blanketline['OrdersBlanket']['quantity'] .' '. $blanketline['Product']['uom'] .' products.';
                echo json_encode($response);
                exit;
            }

            // Step 1. Create order line
            $line_number = $this->OrdersLine->find('first', [
                'fields' => ['OrdersLine.line_number'],
                'contain' => false,
                'conditions' => array('OrdersLine.order_id' =>  $blanketline['OrdersBlanket']['order_id']),
                'order' => 'OrdersLine.line_number DESC',
                'callbacks' => false
            ]);

            if($line_number) {
                $line_number = $line_number['OrdersLine']['line_number'] + 10;
            } else {
                $line_number = 20; //First line is blanket have line number 10, we start from 20.
            }

            $orderline['OrdersLine']['unit_price']      = $blanketline['OrdersBlanket']['unit_price'];
            $orderline['OrdersLine']['product_id']      = $blanketline['OrdersBlanket']['product_id'];
            $orderline['OrdersLine']['status_id']       = $blanketline['OrdersBlanket']['status_id'];
            $orderline['OrdersLine']['order_id']        = $blanketline['OrdersBlanket']['order_id'];
            $orderline['OrdersLine']['line_number']     = $line_number;
            $orderline['OrdersLine']['type']            = $blanketline['Order']['ordertype_id'];
            $orderline['OrdersLine']['sku']             = $blanketline['OrdersBlanket']['sku'];
            $orderline['OrdersLine']['user_id']         = $blanketline['OrdersBlanket']['user_id'];
            $orderline['OrdersLine']['dcop_user_id']    = $this->Auth->user('id');
            $orderline['OrdersLine']['sentqty']         = 0;
            $orderline['OrdersLine']['receivedqty']     = 0;
            $orderline['OrdersLine']['damagedqty']      = 0;
            $orderline['OrdersLine']['serial_id']       = $blanketline['OrdersBlanket']['serial_id'];
            $orderline['OrdersLine']['warehouse_id']    = $blanketline['OrdersBlanket']['warehouse_id'];
            $orderline['OrdersLine']['quantity']        = $this->request->data['OrdersBlanket']['new_receivedqty'];
            $orderline['OrdersLine']['total_line']      = $this->request->data['OrdersBlanket']['new_receivedqty'] * $blanketline['OrdersBlanket']['unit_price'];
            $orderline['OrdersLine']['comments']      = $this->request->data['OrdersBlanket']['comments'];
            if (!$this->OrdersLine->save($orderline)) {
                $response['status'] = false;
                $response['message'] = 'Can\'t receive products, please try againe later';
                echo json_encode($response);
                exit;
            } else {
                $orderline_id = $this->OrdersLine->id;
                $orderline = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.id' => $orderline_id), 'callbacks' => false));
            }

            // Step 2. Receive order line
            $response = $this->InventoryManager->receiveLine(
                $orderline,
                $blanketline['OrdersBlanket']['warehouse_id'],
                $this->request->data['OrdersBlanket']['new_receivedqty'],
                $shipment_id
            );
            if($response['status']) {
                // Update order status as partially processed
                $this->Order->id = $orderline['OrdersLine']['order_id'];
                $this->Order->saveField('status_id', 3);

                $this->OrdersBlanket->id = $id;
                $this->OrdersBlanket->saveField('receivedqty', ($blanketline['OrdersBlanket']['receivedqty'] + $this->request->data['OrdersBlanket']['new_receivedqty']));

                // Update shipment status as partially processed
                //$this->updateshipmentprocess($orderline['OrdersLine']['order_id']);
            }
            echo json_encode($response);
            exit;
        } else {
            $this->request->data = $blanketline;
        }

        $this->set(compact('product','blanketline','warehouses'));
    }
    
}