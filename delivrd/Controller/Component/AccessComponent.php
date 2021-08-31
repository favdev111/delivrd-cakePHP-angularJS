<?php
App::uses('Component', 'Controller');

class AccessComponent extends Component {

    public $components = array('Auth', 'Session');
    public $Network;
    public $Inventory;
    public $Warehouse;
    public $NetworksUser;
    public $NetworksInvite;
    
    public $_access;
    public $_networks;

    public function initialize(Controller $controller) {

        $this->Network          = ClassRegistry::init('Network');
        $this->Warehouse        = ClassRegistry::init('Warehouse');
        $this->NetworksUser     = ClassRegistry::init('NetworksUser');
        $this->NetworksInvite   = ClassRegistry::init('NetworksInvite');
        $this->NetworksAccess   = ClassRegistry::init('NetworksAccess');
        
        $this->_access = $this->getAccessList();
    }

    function getModels() {
        return array('Inventory', 'Warehouse', 'Purchase_Orders');
    }

    function getAccessList() {
        $access = $this->NetworksAccess->find('all', [
            'conditions' => ['NetworksAccess.user_id' => $this->Auth->user('id')],
            //'contain' => ['Warehouse', 'Network.NetworksUser'=>['conditions' => ['NetworksUser.user_id'=> $this->Auth->user('id')]]]
        ]);
        
        $this->_networks = Set::combine($access, '{n}.Network.id', '{n}.Network' );

        $netuser = $this->NetworksUser->find('all', [
            'conditions' => ['NetworksUser.user_id' => $this->Auth->user('id'), 'NetworksUser.status' => 1],
            'contain' => false
        ]);

        $access = Set::combine($access, '{n}.NetworksAccess.warehouse_id', '{n}', '{n}.NetworksAccess.model' );
        $access['Product'] = [];
        $access['Schanel'] = [];
        foreach ($netuser as $list) {
            if($list['NetworksUser']['products'] != 'all') {
                $access['Product'][$list['NetworksUser']['network_id']] = json_decode($list['NetworksUser']['products'], true);
            } else {
                $access['Product'][$list['NetworksUser']['network_id']] = 'all';
            }
            if($list['NetworksUser']['schannel'] != 'all') {
                $access['Schanel'][$list['NetworksUser']['network_id']] = json_decode($list['NetworksUser']['schannel'], true);
            } else {
                $access['Schanel'][$list['NetworksUser']['network_id']] = 'all';
            }
        }
        if(count($access['Product']) == 0) {
            unset($access['Product']);
        }
        if(count($access['Schanel']) == 0) {
            unset($access['Schanel']);
        }
        return $access;
    }

    /**
     * Return auth user network list for Product list
     *
     */
    function networkList() {
        $networks = Set::combine($this->_networks, '{n}.id', '{n}.name');
        
        foreach($networks as $key => $net) {
            if(!isset($this->_access['Product'][$key])) {
                unset($networks[$key]);
            }
        }
        if(!$this->Auth->user('is_limited')) {
            $networks = ['my' => 'My Products'] + $networks;
        }
        return $networks;
    }

    function networkCats($network_id = null) {
        $this->Category = ClassRegistry::init('Category');
        if($network_id) {
            if(isset($this->_networks[$network_id])) {
                $conditions['Category.user_id'] = $this->_networks[$network_id]['created_by_user_id'];
                $categories[$this->_networks[$network_id]['name']] = $this->Category->find('list', array('conditions' => $conditions, 'order' => 'Category.name ASC'));
            } else {
                $categories = [];
            }
        } else {
            if(!$this->Auth->user('is_limited')) {
                $conditions['Category.user_id'] = $this->Auth->user('id');
                $categories['My Categories'] = $this->Category->find('list', array('conditions' => $conditions, 'order' => 'Category.name ASC'));
            }

            if($this->_networks) {
                $user_ids = [];
                foreach ($this->_networks as $key => $value) {
                    //if(!in_array($value['created_by_user_id'], $user_ids)) {
                        $user_ids[] = $value['created_by_user_id'];
                        $conditions['Category.user_id'] = $value['created_by_user_id'];
                        $categories[$value['name']] = $this->Category->find('list', array('conditions' => $conditions, 'order' => 'Category.name ASC'));
                    //}
                }
            }
        }

        return $categories;
    }

    function hasInventoryAccess($inventory) {
        $loc = $this->locationList('Inventory');
        if($inventory['Inventory']['user_id'] == $this->Auth->user('id') ||
            (isset($loc[$inventory['Inventory']['warehouse_id']]) && in_array($loc[$inventory['Inventory']['warehouse_id']], ['rw', 'w']))
            ) {
            return true;
        } else {
            return false;
        }
    }

    function hasSerialAccess($serial, $access = 'w') {
        if($serial['Serial']['user_id'] == $this->Auth->user('id')) {
            return true;
        }
        $locs = $this->locationList('Serials');
        $prods = $this->getProducts();

        if(isset($locs[$serial['Serial']['warehouse_id']]) && in_array($locs[$serial['Serial']['warehouse_id']], ['rw', 'w']) && array_key_exists($serial['Serial']['product_id'], $prods)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Users can only view other products
     */
    function hasProductAccess($product_id, $type = 'r') {
        $loc = $this->locationList('Inventory');
        $this->Product = ClassRegistry::init('Product');
        $product = $this->Product->find('first', [
            'conditions' => ['Product.id' => $product_id],
            'contain' => ['Inventory'],
            'fields' => ['Product.*'],
            'callbacks' => false

        ]);

        if($product['Product']['user_id'] == $this->Auth->user('id')) {
            return true;
        }
        
        foreach ($product['Inventory'] as $warehouse) {
            if(array_key_exists($warehouse['warehouse_id'], $loc)) {
                if(strpos($loc[$warehouse['warehouse_id']], $type) !== false ) {
                    return true;
                }
            }
        }
        return false;
    }

    function getProducts($model = 'Inventory', $access = false, $user_id = 0, $prod_cond = []) {
        $results = $this->productList($model, $access, $user_id, $prod_cond);
        
        $products = [];
        foreach ($results as $res) {
            $products = $products + $res;
        }
        return $products;
    }

    function productList($model = 'Inventory', $access = false, $user_id = 0, $prod_cond = []) {
        
        $allowed = false;
        if($model) {
            $cond['NetworksAccess.model'] = $model;
        }
        if($user_id) {
            $cond['Network.created_by_user_id'] = $user_id;
        }
        if($access) {
            // only get products from network where user have write access to $model
            $cond['NetworksAccess.access LIKE'] = '%'. $access .'%';
        }
        if(!empty($cond)) {
            $cond['NetworksAccess.user_id'] = $this->Auth->user('id');
            $allowed = $this->NetworksAccess->find('list', [
                'conditions' => $cond,
                'contain' => ['Network'],
                'fields' => ['Network.id', 'Network.name']
            ]);
        }

        $networks = $this->NetworksUser->find('all', [
            'conditions' => ['NetworksUser.user_id'=>$this->Auth->user('id')],
            'contain' => 'Network'
        ]);

        $this->Product = ClassRegistry::init('Product');
        $products = [];
        foreach ($networks as $network) {
            if($allowed && array_key_exists($network['Network']['id'], $allowed)) {
                if($network['NetworksUser']['products'] != 'all') {
                    $tmp = json_decode($network['NetworksUser']['products'], true);
                    $prod_cond['Product.id'] = $tmp;
                    $products[$network['Network']['name']] = $this->Product->find('list', ['conditions'=>$prod_cond, 'callbacks' => false]);
                } else {
                    $prod_cond['Product.user_id'] = $network['Network']['created_by_user_id'];
                    $products[$network['Network']['name']] = $this->Product->find('list', ['conditions'=>$prod_cond, 'callbacks' => false]);
                }
            } elseif($allowed === false) {
                if($network['NetworksUser']['products'] != 'all') {
                    $tmp = json_decode($network['NetworksUser']['products'], true);
                    $prod_cond['Product.id'] = $tmp;
                    $products[$network['Network']['name']] = $this->Product->find('list', ['conditions'=>$prod_cond, 'callbacks' => false]);
                } else {
                    $prod_cond['Product.user_id'] = $network['Network']['created_by_user_id'];
                    $products[$network['Network']['name']] = $this->Product->find('list', ['conditions'=>$prod_cond, 'callbacks' => false]);
                }
            }
        }
        return $products;
    }

    function schannelList($model=false, $access = false, $user_id = 0) {
        $networks = [];
        if($model) {
            $tmp = [];
            if($access) {
                if(isset($this->_access[$model])) {
                    foreach ($this->_access[$model] as $value) {
                        if(strpos($value['NetworksAccess']['access'], $access) !== false ) {
                            $tmp[$value['Network']['id']] = $value['Network'];
                        }
                    }
                }
            } else {
                foreach ($this->_access[$model] as $value) {
                    $tmp[$value['Network']['id']] = $value['Network'];
                }
            }

            if($user_id) {
                foreach ($tmp as $value) {
                    if($value['created_by_user_id'] == $user_id) {
                        $networks[$value['id']] = $value;
                    }
                }
            } else {
                $networks = $tmp;
            }
        } else {
            $networks = $this->_networks;
        }
        
        $this->Schannel = ClassRegistry::init('Schannel');
        $schannels = [];
        if(!$this->Auth->user('is_limited') && (!$user_id || $user_id == $this->Auth->user('id'))) {
            $schannels['My Schannels'] = $this->Schannel->find('list',array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));
        }
        foreach ($networks as $network) {
            if($this->_access['Schanel'][$network['id']] == 'all') {
                $schannels[$network['name']] = $this->Schannel->find('list', ['conditions'=>['Schannel.user_id' => $network['created_by_user_id']], 'callbacks' => false]);
            } else {
                if($this->_access['Schanel'][$network['id']]) {
                    $schannels[$network['name']] = $this->Schannel->find('list', ['conditions'=>['Schannel.id' => $this->_access['Schanel'][$network['id']]], 'callbacks' => false]);
                }
            }
        }
        return $schannels;
    }

    function suppliersList($model=false, $access = false, $user_id = 0) {
        $networks = [];
        if($model) {
            $tmp = [];
            if($access) {
                if(isset($this->_access[$model])) {
                    foreach ($this->_access[$model] as $value) {
                        if(strpos($value['NetworksAccess']['access'], $access) !== false ) {
                            $tmp[$value['Network']['id']] = $value['Network'];
                        }
                    }
                }
            } else {
                foreach ($this->_access[$model] as $value) {
                    $tmp[$value['Network']['id']] = $value['Network'];
                }
            }

            if($user_id) {
                foreach ($tmp as $value) {
                    if($value['created_by_user_id'] == $user_id) {
                        $networks[$value['id']] = $value;
                    }
                }
            } else {
                $networks = $tmp;
            }
            
        } else {
            $networks = $this->_networks;
        }
        
        $this->Supplier = ClassRegistry::init('Supplier');
        $suppliers = [];
        if(!$this->Auth->user('is_limited') && (!$user_id || $user_id == $this->Auth->user('id'))) {
            $suppliers['My Suppliers'] = $this->Supplier->find('list',array('conditions' => array('Supplier.user_id' => $this->Auth->user('id'))));
        }
        foreach ($networks as $network) {
            $suppliers[$network['name']] = $this->Supplier->find('list', ['conditions'=>['Supplier.user_id' => $network['created_by_user_id']], 'callbacks' => false]);
        }
        return $suppliers;
    }

    function couriersList($model=false, $access = false, $user_id = 0) {
        $networks = [];
        if($model) {
            $tmp = [];
            if($access) {
                if(isset($this->_access[$model])) {
                    foreach ($this->_access[$model] as $value) {
                        if(strpos($value['NetworksAccess']['access'], $access) !== false ) {
                            $tmp[$value['Network']['id']] = $value['Network'];
                        }
                    }
                }
            } else {
                if(!empty($this->_access[$model])) {
                    foreach ($this->_access[$model] as $value) {
                        $tmp[$value['Network']['id']] = $value['Network'];
                    }
                }
            }

            if($user_id) {
                foreach ($tmp as $value) {
                    if($value['created_by_user_id'] == $user_id) {
                        $networks[$value['id']] = $value;
                    }
                }
            } else {
                $networks = $tmp;
            }
            
        } else {
            $networks = $this->_networks;
        }
        
        $this->Courier = ClassRegistry::init('Courier');
        $couriers = [];
        if(!$this->Auth->user('is_limited') && (!$user_id || $user_id == $this->Auth->user('id'))) {
            $couriers['My Couriers'] = $this->Courier->find('list',array('conditions' => array('Courier.user_id' => $this->Auth->user('id'))));
        }
        foreach ($networks as $network) {
            $couriers[$network['name']] = $this->Courier->find('list', ['conditions'=>['Courier.user_id' => $network['created_by_user_id']], 'callbacks' => false]);
        }
        return $couriers;
    }

    function resourcesList($model=false, $access = false, $user_id = 0) {
        $networks = [];
        if($model) {
            $tmp = [];
            if($access) {
                if(isset($this->_access[$model])) {
                    foreach ($this->_access[$model] as $value) {
                        if(strpos($value['NetworksAccess']['access'], $access) !== false ) {
                            $tmp[$value['Network']['id']] = $value['Network'];
                        }
                    }
                }
            } else {
                if(!empty($this->_access[$model])) {
                    foreach ($this->_access[$model] as $value) {
                        $tmp[$value['Network']['id']] = $value['Network'];
                    }
                }
            }

            if($user_id) {
                foreach ($tmp as $value) {
                    if($value['created_by_user_id'] == $user_id) {
                        $networks[$value['id']] = $value;
                    }
                }
            } else {
                $networks = $tmp;
            }
            
        } else {
            $networks = $this->_networks;
        }
        
        $this->Resource = ClassRegistry::init('Resource');
        $resources = [];
        if(!$this->Auth->user('is_limited') && (!$user_id || $user_id == $this->Auth->user('id'))) {
            $resources['My Resources'] = $this->Resource->find('list',array('conditions' => array('Resource.user_id' => $this->Auth->user('id'))));
        }
        foreach ($networks as $network) {
            $resources[$network['name']] = $this->Resource->find('list', ['conditions'=>['Resource.user_id' => $network['created_by_user_id']], 'callbacks' => false]);
        }
        return $resources;
    }

    public function locationList($model = 'Inventory', $network_id = false, $access = false, $user_id = 0) {
        $conditions = array(
                'NetworksAccess.user_id' => $this->Auth->user('id'),
                'NetworksAccess.model' => $model,
            );
        if($network_id) {
            $conditions['NetworksAccess.network_id'] = $network_id;
        }
        if($access) {
            if($access != 'r') {
                $conditions['NetworksAccess.access LIKE'] = '%'. $access .'%';
            }
            
        }
        if($user_id) {
            $conditions['Network.created_by_user_id'] = $user_id;
        }
        $locations = $this->NetworksAccess->find('list', array(
            'conditions' => $conditions,
            'contain' => ['Network'],
            'fields' => array('NetworksAccess.warehouse_id','NetworksAccess.access')
        ));
        return $locations;
    }

    /**
     * Return all available locations (own locations and network locations)
     *
     */
    function getLocations($model, $network_id = false, $access = false, $user_id = 0) {
        $locations = [];
        $conditions = array(
            'NetworksAccess.user_id' => $this->Auth->user('id'),
            'NetworksAccess.model' => $model,
            'Warehouse.status' => 'active'
        );
        if($network_id) {
            $conditions['NetworksAccess.network_id'] = $network_id;
        }
        if($access) {
            if($access != 'r') {
                $conditions['NetworksAccess.access LIKE'] = '%'. $access .'%';
            }
        }
        if($user_id) {
            $conditions['Network.created_by_user_id'] = $user_id;
        }
        if($network_id) {
            $networks = $this->NetworksAccess->find('all', array(
                'conditions' => $conditions,
                'contain' => array('Network', 'Warehouse' => array('fields'=>array('Warehouse.id', 'Warehouse.name'))),
                'fields' => array('NetworksAccess.warehouse_id','Warehouse.name', 'Network.*')
            ));
            
        } else {
            $this->Warehouse = ClassRegistry::init('Warehouse');
            if(!$this->Auth->user('is_limited') && (!$user_id || $user_id == $this->Auth->user('id')) ) {
                $locations['My Locations'] = $this->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'), 'Warehouse.status' => 'active')));
            }
            $networks = $this->NetworksAccess->find('all', array(
                'conditions' => $conditions,
                'contain' => array('Network', 'Warehouse' => array('fields'=>array('Warehouse.id', 'Warehouse.name'))),
                'fields' => array('NetworksAccess.access', 'Network.*')
            ));
        }
        
        if($networks) {
            foreach ($networks as $network) {
                $locations[$network['Network']['name']][$network['Warehouse']['id']] = $network['Warehouse']['name'];
            }
        }
        return $locations;
    }

    /**
     * Return all available locations (own locations and network locations)
     *
     */
    function getLocationsWithInactive($model, $network_id = false, $access = false, $user_id = 0) {
        $locations = [];
        $conditions = array(
            'NetworksAccess.user_id' => $this->Auth->user('id'),
            'NetworksAccess.model' => $model,
        );
        if($network_id) {
            $conditions['NetworksAccess.network_id'] = $network_id;
        }
        if($access) {
            if($access != 'r') {
                $conditions['NetworksAccess.access LIKE'] = '%'. $access .'%';
            }
        }
        if($user_id) {
            $conditions['Network.created_by_user_id'] = $user_id;
        }
        if($network_id) {
            $networks = $this->NetworksAccess->find('all', array(
                'conditions' => $conditions,
                'contain' => array('Network', 'Warehouse' => array('fields'=>array('Warehouse.id', 'Warehouse.name'))),
                'fields' => array('NetworksAccess.warehouse_id','Warehouse.name', 'Network.*')
            ));
            
        } else {
            $this->Warehouse = ClassRegistry::init('Warehouse');
            if(!$this->Auth->user('is_limited') && (!$user_id || $user_id == $this->Auth->user('id')) ) {
                $locations['My Locations'] = $this->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'), 'Warehouse.status' => 'active')));
            }
            $networks = $this->NetworksAccess->find('all', array(
                'conditions' => $conditions,
                'contain' => array('Network', 'Warehouse' => array('fields'=>array('Warehouse.id', 'Warehouse.name'))),
                'fields' => array('NetworksAccess.access', 'Network.*')
            ));
        }
        
        if($networks) {
            foreach ($networks as $network) {
                $locations[$network['Network']['name']][$network['Warehouse']['id']] = $network['Warehouse']['name'];
            }
        }
        return $locations;
    }

    /**
     * Is user can change order
     *
     */
    function hasOrderAccess($order_id) {
        $this->Order = ClassRegistry::init('Order');
        $order = $this->Order->find('first', ['conditions' => array('Order.id' => $order_id), 'contain'=>false]);
        
        if($order['Order']['user_id'] == $this->Auth->user('id')) {
            return true;
        }

        if($order['Order']['ordertype_id'] == 1) {
            $type = 'S.O.';
        } else {
            $type = 'P.O.';
        }

        if(isset($this->_access[$type])) {
            $acc_table = Set::combine($this->_access[$type], '{n}.NetworksAccess.warehouse_id', '{n}.NetworksAccess.access');
            $this->OrdersLine = ClassRegistry::init('OrdersLine');
            $orders_lines = $this->OrdersLine->find('list', array(
                'fields' => array('OrdersLine.id', 'OrdersLine.warehouse_id'),
                'conditions' => array('OrdersLine.order_id' => $order_id),
                'callbacks' => false
            ));
            
            $is_write = 1;
            foreach ($orders_lines as $line => $warehouse_id) {
                if(!isset($acc_table[$warehouse_id]) || strpos($acc_table[$warehouse_id], 'w') === false) {
                    $is_write = 0;
                }
            }

            return $is_write;
        } else {
            return 0;
        }
    }


    

    function networks_owners($model='S.O.') {
        $owner_ids = [];
        if(!empty($this->_access[$model])) {
            foreach ($this->_access[$model] as $val) {
                $owner_ids[] = $val['Network']['created_by_user_id'];
            }
        }
        return $owner_ids;
    }

    /**
     * Is user can create shipment for order
     *
     */
    function hasOrderShipmentAccess($order_id) {
        if(isset($this->_access['Shipments'])) {
            $this->OrdersLine = ClassRegistry::init('OrdersLine');
            $shipment = Set::combine($this->_access['Shipments'], '{n}.NetworksAccess.warehouse_id', '{n}.NetworksAccess.access');
            $orders_lines = $this->OrdersLine->find('list', array(
                'fields' => array('OrdersLine.id', 'OrdersLine.warehouse_id'),
                'conditions' => array('OrdersLine.order_id' => $order_id),
                'callbacks' => false
            ));

            $is_shipment = 1;
            foreach ($orders_lines as $line => $warehouse_id) {
                if(!isset($shipment[$warehouse_id]) || strpos($shipment[$warehouse_id], 'w') === false) {
                    $is_shipment = 0;
                }
            }

            return $is_shipment;
        } else {
            return 0;
        }
    }

    public function getAccessByInvite($invite) {
        $access = [];
        if($invite['NetworksInvite']['warehouse']) {
            $warehouses = json_decode($invite['NetworksInvite']['warehouse']);
        } elseif($invite['NetworksInvite']['warehouse'] == 'all') { //all warehouses
            $warehouses = $this->Warehouse->find('list', [
                'conditions'=>['Warehouse.user_id' => $invite['Network']['created_by_user_id'], 'Warehouse.status' => 'active'],
                'fields'=>['Warehouse.id','Warehouse.name']
            ]);
            $warehouses = array_keys($warehouses);
        } else { //no warehouses
            $warehouses = [];
        }
        
        foreach ($warehouses as $warehouse_id) {
            switch ($invite['NetworksInvite']['role']) {
                case 1:
                    // 3pl supplier customer full access
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Inventory';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    /*$netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Serials';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);*/
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Shipments';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'S.O.';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'P.O.';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    break;
                case 2:
                    // Internal customers
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Inventory';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    /*$netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Serials';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);*/
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Shipments';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'S.O.';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'P.O.';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    break;
                case 3:
                    // Internal customers
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Inventory';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    /*$netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Serials';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);*/
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Shipments';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'S.O.';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'P.O.';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    break;
                case 4:
                    // Internal customers
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Inventory';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    /*$netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Serials';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);*/
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Shipments';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'S.O.';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'P.O.';
                    $netaccess['NetworksAccess']['access'] = 'rw';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    break;
                default:
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Inventory';
                    $netaccess['NetworksAccess']['access'] = 'r';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'S.O.';
                    $netaccess['NetworksAccess']['access'] = 'r';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'P.O.';
                    $netaccess['NetworksAccess']['access'] = 'r';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    $netaccess['NetworksAccess']['network_id'] = $invite['NetworksInvite']['network_id'];
                    $netaccess['NetworksAccess']['user_id'] = $this->Auth->user('id');
                    $netaccess['NetworksAccess']['model'] = 'Shipments';
                    $netaccess['NetworksAccess']['access'] = 'r';
                    $netaccess['NetworksAccess']['warehouse_id'] = $warehouse_id;
                    $access[] = $netaccess;
                    unset($netaccess);
                    break;
            }
            
        }
        return $access;
    }
}