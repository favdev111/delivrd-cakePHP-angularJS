<?php
App::uses('AppModel', 'Model');
/**
 * Inventory Model
 *
 * @property Product $Product
 * @property User $User
 * @property DcopUser $DcopUser
 */
class Inventory extends AppModel {

    public $actsAs = array(
        'Search.Searchable','Utils.SoftDelete','Containable'
    );

    public $issuedQuant = 0;

    public $filterArgs = array(
        'name' => array(
            'type' => 'like',
            'field' => 'Product.name'
        ),
        'sku' => array(
            'type' => 'like',
            'field' => 'Product.sku'
        ),
        'searchby' => array(
            'type' => 'like',
            'field' => 'Product.sku'
        ),
        'searchby' => array(
            'type' => 'like',
            'field' => 'Product.name'
        ),
        'warehouse_id' => array(
            'type' => 'value',
            'field' => 'warehouse_id'
        ),
        'searchby' => array(
            'type' => 'value',
            'field' => 'Inventory.warehouse_id'
        ),
        'searchby' => array(
            'type' => 'value',
            'field' => 'Inventory.product_id'
        ),
    );

    public $validate = array(
        'product_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'You must select a product when creating an inventory record',
                'on' => 'create',
            ),
        ),
        'warehouse_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'You must select a location when creating an inventory record',
                'required' => true,
                'on' => 'create',
            ),
            'warehouse_id' => array(
                'rule'=>array('warehouse_confirm'),
                'message'=>'Product and Location must be from one Network',
                'on' => 'create',
            )
        ),
        'quantity' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'You must select a quantity when creating an inventory record',
                'required' => true,
                'on' => 'create',
            ),
            /*'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'message' => 'Quantity must be positive'
            ),*/
        ),
        'damaged_qty' => array(
            'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'message' => 'Quantity must be positive'
            ),
        ),
    ); 

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Warehouse' => array(
            'className' => 'Warehouse',
            'foreignKey' => 'warehouse_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'DcopUser' => array(
            'className' => 'DcopUser',
            'foreignKey' => 'dcop_user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function isPositive($quantity = null) {
        if($quantity > 0) {
            return true; 
        } else {
            return false;
        }
    }

    public function warehouse_confirm() {
        $productModel = ClassRegistry::init('Product');
        $warehouseModel = ClassRegistry::init('Warehouse');
        if(!empty($this->data['Inventory']['product_id'])) {
            $productModel->id = $this->data['Inventory']['product_id'];

            $product_user_id = $productModel->find('first', ['conditions'=>array('Product.id' => $this->data['Inventory']['product_id']), 'fields' => array('Product.user_id'), 'callbacks' => false]);
            $warehouseModel->id = $this->data['Inventory']['warehouse_id'];
            $warehouse_user_id = $warehouseModel->find('first', ['conditions'=>array('Warehouse.id' => $this->data['Inventory']['warehouse_id']), 'fields' => array('Warehouse.user_id'), 'callbacks' => false]);;
            
            if($product_user_id['Product']['user_id'] == $warehouse_user_id['Warehouse']['user_id']) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
        
    }
                
    /*public function beforeFind($queryData) {
        //$userid = CakeSession::read("Auth.User.id");
        //$defaultConditions = array('Inventory.user_id' => CakeSession::read("Auth.User.id"));
        //$queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
        return $queryData;
    }*/

    public function uniqueQuantity() {
        $unique_pdt = $this->find('all', array(
            'recursive' => -1,
            'contain' => array('Product'),
            'conditions' => array(
                'Product.user_id' => CakeSession::read("Auth.User.id"),
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
                'sum(Inventory.quantity) as total',
            ),
            'group' => 'Inventory.product_id HAVING Product.safety_stock > sum(Inventory.quantity) OR Product.reorder_point > sum(Inventory.quantity)',
            'recursive' => 1
        ));
        return count($unique_pdt);
    }

    public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
        //pr($conditions);
        //exit;
        if(empty($fields)) {
            $fields = array(
                "Inventory.id",
                "Inventory.product_id",
                "Inventory.user_id",
                "Inventory.warehouse_id",
                "Inventory.quantity",
                "Inventory.damaged_qty",
                "Inventory.modified",
                "Product.name",
                "Product.uom",
                "Product.safety_stock",
                "Product.reorder_point",
                "Product.imageurl ",
                "Product.sku ",
                "Invalert.safety_stock",
                "Invalert.reorder_point",
                //"Product.category_id",
                //"Product.value",
                //"Warehouse.name ",
                //"Warehouse.id ",
                "NetworksAccess.access",
                "Network.name"
            );
        }

        $contain = array(
            "Product" => array('conditions' => array('Product.status_id !=' => [13,12])),
            "Product.Productsupplier.Supplier" => array('fields' => array('name')),
            //"Warehouse",
            //"User" //,"User.Product" => array('limit' => 10)
        );
        $joins = array(
            array('table' => 'invalerts',
                'alias' => 'Invalert',
                'type'  => 'LEFT',
                'conditions' => array(
                    'Invalert.warehouse_id = Inventory.warehouse_id AND Invalert.product_id = Inventory.product_id',
                )
            ),
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
        );
        $recursive = 2;
        $group = array('Inventory.id');
        if(empty($order)){
            $order = array('Inventory.modified' => 'DESC');
        }
        $result = $this->find(
            'all',
            compact('conditions', 'contain', 'joins', 'fields', 'order', 'limit', 'page', 'recursive', 'group')
        );
        /*pr('Custom Paginate');
        pr($conditions);
        pr($result);
        exit;*/
        return $result;
    }

    public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
        $group = array('Inventory.id');
        $contain = array(
            "Product",
        );
        return $this->find(
            'count',
            compact('conditions', 'contain', 'recursive', 'group')
        );
    }

    public function beforeDelete($cascade = true) {
        return true;
    }

    public function getInvenotry($product_id, $warehouse_id, $fields = array('Inventory.id', 'Inventory.user_id', 'Inventory.warehouse_id', 'Inventory.product_id', 'Warehouse.name')) {
        return $this->find('first', array('fields' => $fields,
            'conditions' => array(
                'Inventory.product_id'  => $product_id,
                'Inventory.warehouse_id'=> $warehouse_id,
                'Inventory.deleted' => 0
            )
        ));
    }

    /**
     * Save invnetory global method
     *
     * @param array $inventory 
     * @param array $orderline 
     * @return bool
     */
    public function saveInventory($inventory, $orderline, $validation = null) {
        $ds = $this->getDataSource();
        $ds->begin();
        if ($new_inv = $this->save($inventory)) {
            $orderline['OrdersLine']['quantity'] = $new_inv['Inventory']['quantity'];
            
            $ordersLineModel = ClassRegistry::init('OrdersLine');
            $ordersLineModel->create();
            if($validation != null && $validation == true) {
                unset($ordersLineModel->validate['sentqty']);
                unset($ordersLineModel->validate['receivedqty']);
            }
 
            if($ordersLineModel->save($orderline)) {
                $ds->commit();
                return true;
            }
        }
        $ds->rollback();
        return false;
    }

    /**
     * Important: must be atomic
     *
     */
    public function createRecord($pid = null, $warehouseid = null, $quantity = 0, $dquantity = 0, $comments = null) {

        $this->create();
        $this->Product->contain(false);
        $product = $this->Product->find('first', array('conditions' => array('Product.id' => $pid), 'callbacks' => false));

        $data['Inventory']['user_id'] = $product['Product']['user_id'];
        $data['Inventory']['dcop_user_id'] = AuthComponent::user('id');
        $data['Inventory']['product_id'] = $pid;
        $data['Inventory']['quantity'] = $quantity;
        $data['Inventory']['warehouse_id'] = $warehouseid;
        if($dquantity)
            $data['Inventory']['damaged_qty'] = $dquantity;
        if($quantity) {
            $linedata['OrdersLine'] = array(
                'order_id' => 4294967294,
                'line_number' => 1,
                'type' => 3, // $lintype = 6
                'product_id'  => $pid,
                'quantity' => $quantity,
                'receivedqty' => $quantity, // 1
                'damagedqty' => 0,
                'sentqty' => 0,
                'unit_price' => $product['Product']['value'],
                'total_line' => $product['Product']['value'] * abs($quantity),
                'foc' => '',
                'warehouse_id' => $warehouseid,
                'return' => '',
                'comments' => $comments,
                'user_id' => $product['Product']['user_id'],
                'dcop_user_id' => AuthComponent::user('id')
            );
        }
        $dataSource = $this->getDataSource();
        $dataSource->begin(); // Start transaction
        if($this->save($data)) {
            if(!empty($linedata)) {
                $ordersLineModel = ClassRegistry::init('OrdersLine');
                $ordersLineModel->create();
                if($ordersLineModel->save($linedata)) {
                    $dataSource->commit();
                    return true;
                } else {
                    $dataSource->rollback();
                    return false;
                }
            } else {
                $dataSource->commit();
                return true;
            }
        } else {
            $dataSource->rollback();
            return false;
        }
    }

    /**
     * changeCount quantity method
     *
     * @param int $id
     * @param int $quantity
     * @return bool
     */
    public function changeCount($id = null, $quantity = null) {

        $this->recursive = 2;
        $conditions = array(
            'conditions' => array('Inventory.id' => $id, 'Inventory.deleted' => 0),
            'contain' => array('Product' => array('fields' => array('Product.name','Product.imageurl ','Product.value')))
        );
        $current_inv = $this->find('first', $conditions);

        $delta = $current_inv['Inventory']['quantity'] - $quantity;

        $sentqty = 0;
        $recqty = 0;
        $sentqty = ($delta > 0) ? abs($delta) : 0;
        $recqty = ($delta < 0) ? abs($delta) : 0;

        $current_inv['Inventory']['quantity'] = $quantity;
        $current_inv['Inventory']['comments'] = 'By import file';
        unset($current_inv['Inventory']['modified']);
        $data = array(
            'OrdersLine' => array(
                'order_id' => 4294967294,
                'line_number' => 1,
                'type' => 3,
                'product_id'  => $current_inv['Inventory']['product_id'],
                'quantity' => $quantity,
                'receivedqty' => $recqty,
                'damagedqty' => 0,
                'sentqty' => $sentqty,
                'unit_price' => $current_inv['Product']['value'],
                'total_line' => $current_inv['Product']['value'] * abs($delta),
                'foc' => '',
                'warehouse_id' => $current_inv['Inventory']['warehouse_id'],
                'return' => '',
                'comments' => '',
                'user_id' => $current_inv['Inventory']['user_id'],
                'dcop_user_id' => AuthComponent::user('id')
            )
        );

        $dataSource = $this->getDataSource();
        $dataSource->begin(); // Start transaction
        if($this->save($current_inv)) {
            $ordersLineModel = ClassRegistry::init('OrdersLine');
            $ordersLineModel->create();
            if($ordersLineModel->save($data)) {
                $dataSource->commit();
                return true;
            } else {
                $dataSource->rollback();
                return false;
            }
        } else {
            $dataSource->rollback();
            return false;
        }
    }

    /**
     * Receive Inventory Quantity 
     *
     */
    function receiveQuantity($id, $quantity, $order_id = 4294967294, $comments = null) {
        $this->contain(['Product', 'Warehouse']);
        $inventory = $this->find('first', array('conditions' => array('Inventory.id' => $id), 'callbacks'=> false));
        
        $dataSource = $this->getDataSource();
        $dataSource->begin();
        $data['Inventory'] = $inventory['Inventory'];
        $data['Inventory']['quantity'] = $data['Inventory']['quantity'] + $quantity;
        unset($data['Inventory']['modified']);
        if($this->save($data)) {
            $ordersLineModel = ClassRegistry::init('OrdersLine');
            $ordersLineModel->create();
            $data = [];
            $data['OrdersLine'] = array(
                'order_id' => $order_id,
                'line_number' => 1,
                'type' => 6,
                'product_id'  => $inventory['Inventory']['product_id'],
                'quantity' => $inventory['Inventory']['quantity'],
                'receivedqty' => $quantity,
                'damagedqty' => 0,
                'sentqty' => 0,
                'unit_price' => $inventory['Product']['value'],
                'total_line' => $inventory['Product']['value'] * abs($quantity),
                'foc' => '',
                'warehouse_id' => $inventory['Warehouse']['id'],
                'return' => '',
                'comments' => $comments,
                'user_id' => $inventory['Inventory']['user_id'],
                'dcop_user_id' => AuthComponent::user('id')
            );
            if($ordersLineModel->save($data)) {
                $dataSource->commit();
                return true;
            } else {
                $dataSource->rollback();
            }
        }
        return false;
    }

    /**
     * Issue Inventory Quantity 
     *
     */
    function issueQuantity($id, $quantity, $order_id = 4294967294, $comments = null) {
        $this->contain(['Product', 'Warehouse']);
        $inventory = $this->find('first', array('conditions' => array('Inventory.id' => $id), 'callbacks'=> false));
        
        $dataSource = $this->getDataSource();
        $dataSource->begin();
        $data['Inventory'] = $inventory['Inventory'];
        $data['Inventory']['quantity'] = $data['Inventory']['quantity'] - $quantity;
        unset($data['Inventory']['modified']);
        if($this->save($data)) {
            $ordersLineModel = ClassRegistry::init('OrdersLine');
            $ordersLineModel->create();
            $data = [];
            $data['OrdersLine'] = array(
                'order_id' => $order_id,
                'line_number' => 1,
                'type' => 5,
                'product_id'  => $inventory['Inventory']['product_id'],
                'quantity' => $inventory['Inventory']['quantity'],
                'receivedqty' => 0,
                'damagedqty' => 0,
                'sentqty' => $quantity,
                'unit_price' => $inventory['Product']['value'],
                'total_line' => $inventory['Product']['value'] * abs($quantity),
                'foc' => '',
                'warehouse_id' => $inventory['Warehouse']['id'],
                'return' => '',
                'comments' => $comments,
                'user_id' => $inventory['Inventory']['user_id'],
                'dcop_user_id' => AuthComponent::user('id')
            );
            if($ordersLineModel->save($data)) {
                $dataSource->commit();
                return true;
            } else {
                $dataSource->rollback();
            }
        }
        return false;
    }

    /**
     * Issue Inventory Quantity 
     *
     */
    function issueToAssemble($id, $quantity, $order_id = 4294967294, $comments = null) {
        $this->contain(['Product', 'Warehouse']);
        $inventory = $this->find('first', array('conditions' => array('Inventory.id' => $id), 'callbacks'=> false));
        
        $dataSource = $this->getDataSource();
        $dataSource->begin();
        $data['Inventory'] = $inventory['Inventory'];
        $data['Inventory']['quantity'] = $data['Inventory']['quantity'] - $quantity;
        unset($data['Inventory']['modified']);
        if($this->save($data)) {
            $ordersLineModel = ClassRegistry::init('OrdersLine');
            $ordersLineModel->create();
            $data = [];
            $data['OrdersLine'] = array(
                'order_id' => $order_id,
                'line_number' => 1,
                'type' => 7,
                'product_id'  => $inventory['Inventory']['product_id'],
                'quantity' => $inventory['Inventory']['quantity'],
                'receivedqty' => 0,
                'damagedqty' => 0,
                'sentqty' => $quantity,
                'unit_price' => $inventory['Product']['value'],
                'total_line' => $inventory['Product']['value'] * abs($quantity),
                'foc' => '',
                'warehouse_id' => $inventory['Warehouse']['id'],
                'return' => '',
                'comments' => $comments,
                'user_id' => $inventory['Inventory']['user_id'],
                'dcop_user_id' => AuthComponent::user('id')
            );
            if($ordersLineModel->save($data)) {
                $dataSource->commit();
                return true;
            } else {
                $dataSource->rollback();
            }
        }
        return false;
    }

    /**
     * Get total produc QTY
     *
     *
     */
    function getTotalQty($product_id) {
        $qty = $this->find('first', array('fields' => ['SUM(quantity) as total'],
            'conditions' => array(
                'Inventory.product_id'  => $product_id,
                'Inventory.deleted' => 0,
                'Warehouse.status' => 'active'
            )
        ));
        if(isset($qty[0])) {
            return $qty[0]['total'];
        } else {
            return 0;
        }
    }

    /**
     * Get qty by location
     *
     *
     */
    function getQtyByLoc($product_id) {
        $qty = $this->find('all', array('fields' => ['Warehouse.id', 'Warehouse.name', 'Inventory.quantity'],
            'conditions' => array(
                'Inventory.product_id'  => $product_id,
                'Inventory.deleted' => 0,
                'Warehouse.status' => 'active'
            )
        ));
        return $qty;
    }

    /**
     * Set Inventory Quantity 
     *
     */
    function setQuantity($id, $quantity, $order_id = 4294967294) {
        
    }

    /**
     * Transfer Inventory Quantity 
     *
     */
    function transferQuantity($issue_id, $receive_id, $quantity, $order_id = 4294967294) {

    }

    public function beforeSave($options = array()) {
        if(isset($this->data['Inventory']['quantity']) && isset($inv['Inventory']['quantity'])) {
            $inv = $this->find('first', ['fields' => ['Inventory.quantity'], 'contain' => false, 'conditions' => ['Inventory.id' => $this->id]]);
            if($inv['Inventory']['quantity'] > $this->data['Inventory']['quantity']) {
                $this->issuedQuant = $inv['Inventory']['quantity'] - $this->data['Inventory']['quantity'];
            }
        }
    }

    public function afterSave($created, $options = null) {
        if( $this->issuedQuant > 0) {
            $inv = $this->find('first', ['fields' => ['User.email', 'User.username', 'User.firstname', 'User.lastname', 'User.fast_invalert', 'Inventory.quantity', 'Product.sku', 'Product.name', 'Product.safety_stock', 'Product.reorder_point'], 'contain' => ['Product', 'User'], 'conditions' => ['Inventory.id' => $this->id]]);

            if( $this->issuedQuant > 0 && $inv['User']['fast_invalert'] && (
                    ($inv['Inventory']['quantity'] < $inv['Product']['safety_stock'] && $inv['Product']['safety_stock'] > 0) || 
                    ($inv['Inventory']['quantity'] < $inv['Product']['reorder_point'] && $inv['Product']['reorder_point'] > 0)
                )) {
                $prev_quant = $inv['Inventory']['quantity'] + $this->issuedQuant;
                if( !($prev_quant < $inv['Product']['safety_stock'] || $prev_quant < $inv['Product']['reorder_point']) ) {
                    App::uses('CakeEmail', 'Network/Email');
                    $email = new CakeEmail('mandrill');
                    //$email = new CakeEmail('default');
                    //$email->bcc('fordenis@ukr.net');

                    $email->viewVars(array(
                        'product' => $inv,
                        'issuedQuant' => $this->issuedQuant,
                        'title' => 'Low inventory alert for product '. $inv['Product']['sku'] .' '. $inv['Product']['name'])
                    );
                    
                    $email->template('inventory_low', 'main')
                        ->emailFormat('html')
                        ->to($inv['User']['email'])
                        //->to('fordenis@ukr.net')
                        ->subject('Low inventory alert for product '. $inv['Product']['sku'] .' '. $inv['Product']['name'])
                        ->send();
                }

            }
            $this->issuedQuant = 0;
        }
    }
}