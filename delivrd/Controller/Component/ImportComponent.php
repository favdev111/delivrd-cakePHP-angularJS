<?php
App::uses('Component', 'Controller');
App::uses('Transfer', 'Model');

class ImportComponent extends Component {

    public $settings = [];
    public $components = array('Auth', 'Session');
    public $uoms = array('Piece' => 'Piece', 'Carton' => 'Carton', 'Kilogram' => 'Kilogram', 'Pound' => 'Pound', 'Box' => 'Box', 'ca' => 'Case');
    public $Integration;
    public $Category;
    public $Product;
    public $Inventory;
    public $OrdersLine;
    public $Transfer;
    public $State;
    public $Country;


    const UNCAT = 'Uncategorized';

    public function initialize(Controller $controller) {
        
        $this->Integration  = ClassRegistry::init('Integration');
        $this->Product      = ClassRegistry::init('Product');
        $this->Order        = ClassRegistry::init('Order');
        $this->Inventory    = ClassRegistry::init('Inventory');
        $this->OrdersLine   = ClassRegistry::init('OrdersLine');
        $this->Category     = ClassRegistry::init('Category');
        $this->Transfer     = ClassRegistry::init('Transfer');
        $this->Country      = ClassRegistry::init('Country');
        $this->State        = ClassRegistry::init('State');
    }

    /**
     *
     * @param $product array() list of products
     * @param $is_ecommerce bool IF true - every sync will update Delivrd inventory based on backend inventory
     *                           IF false - Delivrd inventory is not updates by backend inventory
     */
    public function importProduct($products, $is_ecommerce, $transfer_id=0) {

        $unCat = $this->Category->find('first',array('conditions' => array('Category.name' => self::UNCAT,'Category.user_id' => $this->Auth->user('id'))));
        if(!$unCat) { // Add uncat if not exists
            $unCat['Category']['name'] = self::UNCAT;
            $unCat['Category']['user_id'] = $this->Auth->user('id');
            $this->Category->save($unCat);
            $unCatId = $this->Category->id;
        } else {
            $unCatId = $unCat['Category']['id'];
        }

        $added = 0;
        $updated = 0;
        $skiped = 0;
        $skiped_details = [];
        $errors = [];
        
        foreach ($products as $productdata) {
            $productdata['Product']['name'] = $this->prepare_name($productdata['Product']['name']);
            $productdata['Product']['description'] = $this->prepare_desc($productdata['Product']['description']);
            $productdata['Product']['user_id'] = $this->Auth->user('id');
            $productdata['Product']['dcop_user_id'] = $this->Auth->user('id');
            $productdata['Product']['transfer_id'] = $transfer_id;
            $productdata['Inventory'][0]['transfer_id'] = $transfer_id;

            if(empty($productdata['Product']['description'])) {
                $productdata['Product']['description'] = $productdata['Product']['name'];
            }
            if(empty($productdata['Product']['imageurl'])) {
                $productdata['Product']['imageurl'] = Configure::read('Product.image_missing');
            }
            // We need new map for UOMs
            /*if(!empty($productdata['Product']['uom'])) {
                $uomvalid = array_search($productdata['Product']['uom'],$this->uoms);
                if(!$uomvalid) {
                    $this->showimporterror("UOM ".$productdata['Product']['uom']." is not valid.",$file); 
                }
            }*/
        
            if(!empty($productdata['Product']['group'])) {
                $this->loadModel('Group');
                $group = $this->Group->find('first',array('conditions' => array('Group.name' => $productdata['Product']['group'])));
                
                if(!empty($group)) {
                    $productdata['Product']['group_id'] = $group['Group']['id'];
                } else {
                    $productdata['Product']['group_id'] = '99';
                }
            }
            
            if(!empty($productdata['Product']['category'])) {
                $category = $this->Category->find('first',array('conditions' => array('Category.name' => $productdata['Product']['category'],'Category.user_id' => $this->Auth->user('id'))));
                if(!empty($category)) {
                    $productdata['Product']['category_id'] = $category['Category']['id'];
                } else {
                    // Add Category:
                    $this->Category->create();
                    $cat['Category']['name'] = $productdata['Product']['category'];
                    $cat['Category']['user_id'] = $this->Auth->user('id');
                    $r = $this->Category->save($cat);
                    $productdata['Product']['category_id'] = $this->Category->id;
                }
            } else {
                $productdata['Product']['category_id'] = $unCatId;
            }

            $skuexists = $this->Product->find('first',array('fields' => 'Product.sku','conditions' => array('Product.sku' =>$productdata['Product']['sku'], 'Product.user_id' => $this->Auth->user('id'))));
            
            if(sizeof($skuexists) == 0) { // Add new product
                $productdata['Product']['status_id'] = 1;
                $productdata['Product']['deleted'] = 0;
                if($is_ecommerce ){ // Backend system (shopify) manages inventory.
                    
                } else { // Delivrd manages inventory
                    // Inventory in Delivrd is not updated. We ignore the inventory quantity in backend (shopify).
                    // When product is created for the first time, an inventory is created with quantity 0, but no ordersline entry is created.
                    $productdata['Inventory'][0]['quantity'] = 0;

                }
                $this->Product->create();
                if($this->Product->saveAssociated($productdata, array('validate'=>false))) {
                    $added++;
                    // Add order lines
                    $this->OrdersLine->create();
                    $ordersLine = array(
                        'OrdersLine' => array(
                            'order_id' => 4294967294,
                            'line_number' => 1,
                            'type' => 3,
                            'product_id'  => $this->Product->id,
                            'quantity' => $productdata['Inventory'][0]['quantity'],
                            'receivedqty' => $productdata['Inventory'][0]['quantity'],
                            'damagedqty' => 0,
                            'sentqty' => 0,
                            'unit_price' => $productdata['Product']['value'],
                            'total_line' => $productdata['Product']['value'] * abs($productdata['Inventory'][0]['quantity']),
                            'foc' => '',
                            'warehouse_id' => $productdata['Inventory'][0]['warehouse_id'],
                            'return' => '',
                            'comments' => 'Add product from Shopify',
                            'user_id' => $this->Auth->user('id')
                        )
                    );
                    $this->OrdersLine->save($ordersLine);
                } else {
                    $errors[$productdata['Product']['parentid']]['title'] = $productdata['Product']['name'];
                    $errors[$productdata['Product']['parentid']]['error'] = 'Can\'t save new product';
                    $errors[$productdata['Product']['parentid']]['details'] = $this->Product->validationErrors;
                }
            } else { //Update order

                /*$productdata['Product']['id'] = $skuexists['Product']['id'];
                $existsInv = $this->Inventory->find('first', [
                    'contain'    => false,
                    'conditions' => [
                        'Inventory.product_id' => $skuexists['Product']['id'],
                        'Inventory.warehouse_id' => $productdata['Inventory'][0]['warehouse_id']
                    ]
                ]);

                if($is_ecommerce ) { // Backend system (shopify) manages inventory.
                    if($existsInv) { // At this moment user can change default warehouse and we will have no inventory row
                        $this->Inventory->read(null, $existsInv['Inventory']['id']);
                        $this->Inventory->set('quantity', $productdata['Inventory'][0]['quantity']);
                        $this->Inventory->save();
                    }
                    // Add order lines
                    // Do we need it if quantity not changed
                    $delta = $productdata['Inventory'][0]['quantity'] - $existsInv['Inventory']['quantity'];
                    if($delta != 0) { // Add new line only if quantity changed from last import process
                        if($delta > 0){
                            $receivedqty = abs($delta);
                            $sentqty = 0;
                        } else {
                            $receivedqty = 0;
                            $sentqty = abs($delta);
                        }

                        $this->OrdersLine->create();
                        $ordersLine = array(
                            'OrdersLine' => array(
                                'order_id' => 4294967294,
                                'line_number' => 1,
                                'type' => 3,
                                'product_id'  => $skuexists['Product']['id'],
                                'quantity' => $productdata['Inventory'][0]['quantity'],
                                'receivedqty' => $receivedqty,
                                'damagedqty' => 0,
                                'sentqty' => $sentqty,
                                'unit_price' => $productdata['Product']['value'],
                                'total_line' => $productdata['Product']['value'] * abs($delta),
                                'foc' => '',
                                'warehouse_id' => $productdata['Inventory'][0]['warehouse_id'],
                                'return' => '',
                                'comments' => 'Update product from Shopify',
                                'user_id' => $this->Auth->user('id')
                            )
                        );
                        $this->OrdersLine->save($ordersLine);
                    }
                } else { // Delivrd manages inventory
                    // We need it only if wareohouse is changed
                    $productdata['Inventory'][0]['quantity'] = 0;
                }
                if($existsInv){
                    if($this->Product->save($productdata)) { //, array('deep'=>true, 'validate'=>false)
                        $updated++;
                    } else {
                        $errors[$productdata['Product']['parentid']]['title'] = $productdata['Product']['name'];
                        $errors[$productdata['Product']['parentid']]['error'] = 'Can\'t update product';
                        $errors[$productdata['Product']['parentid']]['details'] = $this->Product->validationErrors;
                    }
                } else {
                    // We need it only if wareohouse is changed
                    if($this->Product->saveAssociated($productdata, array('deep'=>true, 'validate'=>false))) { // Save with inventory
                        $updated++;
                    } else {
                        $errors[$productdata['Product']['parentid']]['title'] = $productdata['Product']['name'];
                        $errors[$productdata['Product']['parentid']]['error'] = 'Can\'t update product';
                        $errors[$productdata['Product']['parentid']]['details'] = $this->Product->validationErrors;
                    }
                }*/
                $skiped_details[] = ['name' => $productdata['Product']['name'], 'sku' => $productdata['Product']['sku']];
                $skiped++;
            }
        }
        return ['found'=>count($products), 'added'=>$added, 'updated'=>$updated, 'skiped'=>$skiped, 'skiped_details' => $skiped_details, 'errors'=>$errors];
    }

    public function importOrders($ordersdata, $schannel_id = 0, $transfer_id = 0) {

        $added = 0;
        $updated = 0;
        $skiped = 0;
        $skiped_details = [];
        $errors = [];

        $neworders = array();
        $numords = count($ordersdata);
        foreach ($ordersdata as $orderdata) {
            $this->Order->create();
            $orderdata['Order']['user_id'] = $this->Auth->user('id');
            $orderdata['Order']['dcop_user_id'] = $this->Auth->user('id');
            $orderdata['Order']['status_id'] = 14;
            $orderdata['Order']['ordertype_id'] = 1;
            $orderdata['Order']['interface'] = 1;
            $orderdata['Order']['transfer_id'] = $transfer_id;

            // Convert country and state names to id
            
            $country = $this->Country->find('first',array('conditions' => array('Country.code' => $orderdata['Order']['country_id'])));
            if(!empty($country)) {
                $orderdata['Order']['country_id'] = $country['Country']['id'];
            } else {
                $orderdata['Order']['country_id'] = 0;
            }

            //If country US, state_id should be a valid US state
            $orderdata['Order']['state_id'] = 'XZ';
            $orderdata['Order']['ship_to_stateprovince'] = '';
            if($orderdata['Order']['country_id'] == 'US') {
                $state = $this->State->find('first',array('conditions' => array('State.code' => $orderdata['Order']['ship_to_stateprovince'])));
                if(!empty($state)) {
                    $orderdata['Order']['state_id'] = $state['State']['id'];
                    $orderdata['Order']['ship_to_stateprovince'] = $state['State']['name'];
                }
            }
            
            // Schannel
            $orderdata['Order']['schannel_id'] = $schannel_id;
            
            $exists = $this->Order->find('first', array('fields' => array('Order.id'), 'contain'=>false, 'conditions'=>array('Order.user_id' => $this->Auth->user('id'), 'Order.external_orderid' => $orderdata['Order']['external_orderid'] )));
            if($exists) {
                $skiped++;
                $skiped_details[] = ['external_id' => $orderdata['Order']['external_orderid']];
                continue; //We temporary not update exists orders!!!
                $orderdata['Order']['id'] = $exists['Order']['id'];
                // We need remove exists line items for this order
                $this->OrdersLine->deleteAll(array('OrdersLine.order_id' => $exists['Order']['id']), false);
            }
            
            if($this->Order->save($orderdata)) {
                if($exists) {
                    $updated++;
                } else {
                    $added++;
                }
                $this->orderid = $this->Order->id;
                $addressdata = array();
                $addressdata['order_id'] = $this->orderid;
                $addressdata['street'] = $orderdata['Order']['ship_to_street'];
                $addressdata['city'] = $orderdata['Order']['ship_to_city'];
                $addressdata['zip'] = $orderdata['Order']['ship_to_zip'];
                $addressdata['stateprovince'] = $orderdata['Order']['ship_to_stateprovince'];
                $addressdata['state_id'] = $orderdata['Order']['state_id'];
                $addressdata['country_id'] = $orderdata['Order']['country_id'];
                $addressdata['phone'] = $orderdata['Order']['ship_to_phone'];
                $this->Order->Address->create();
                $this->Order->Address->save($addressdata);
                //we ead each line item and create line
                foreach ($orderdata['OrdersLine'] as $orderlinedata) {
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
                    
                    
                    $orderldata['OrdersLine']['type'] = 1;
                    $orderldata['OrdersLine']['status_id'] = 1;
                    //Not proud of this..
                    $orderldata['OrdersLine']['order_id'] = $this->orderid;
                    $orderldata['OrdersLine']['line_number'] = $linenumber;
                    $orderldata['OrdersLine']['warehouse_id'] = $this->Session->read('default_warehouse');
                    $orderldata['OrdersLine']['type'] = 1;
                    $orderldata['OrdersLine']['product_id'] = $pid;
                    $orderldata['OrdersLine']['sentqty'] = 0;
                    $orderldata['OrdersLine']['quantity'] = $orderlinedata['quantity'];
                    $orderldata['OrdersLine']['unit_price'] = $unit_price;
                    $orderldata['OrdersLine']['total_line'] = $orderlinedata['quantity'] * $orderlinedata['unit_price'] ;
                    $orderldata['OrdersLine']['sku'] = $sku;
                    $orderldata['OrdersLine']['foc'] = 0;
                    $orderldata['OrdersLine']['user_id'] = $this->Auth->user('id');
                    $newline = $this->OrdersLine->saveAll($orderldata);
                    //add addresses 
                }
                
            } else {
                $errors[$orderdata['Order']['external_orderid']] = $this->Order->validationErrors;
                $errors[$orderdata['Order']['external_orderid']]['ex_id'] = $orderdata['Order']['external_orderid2'];
            }
        }
        return ['found'=>count($ordersdata), 'added'=>$added, 'updated'=>$updated, 'skiped'=>$skiped, 'skiped_details'=>$skiped_details, 'errors_count'=>count($errors), 'errors'=>$errors];
    }

    public function prepare_desc($html) {
        return preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", strip_tags($html));
    }

    public function prepare_name($text) {
        $text = str_replace(': Default Title', '', $text);
        return $text;
    }

}