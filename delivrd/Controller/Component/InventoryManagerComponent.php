<?php
App::uses('Component', 'Controller');
class InventoryManagerComponent extends Component {

    public $components = array('Auth', 'Session');
    public $Inventory;
    public $OrdersLine;
    public $Kit;

    public function initialize(Controller $controller) {
        
        $this->Inventory  = ClassRegistry::init('Inventory');
        $this->OrdersLine  = ClassRegistry::init('OrdersLine');
        $this->Kit  = ClassRegistry::init('Kit');
    }

    public function updateStock($productid,$userid,$quantity,$pm) {
        $this->Inventory = ClassRegistry::init('Inventory');
        $this->Inventory->create();
        //Get current stock
        $this->Inventory->set('user_id',$userid);
        $this->Inventory->set('user_id',$userid);
        $this->Inventory->set('product_id', $productid);
        $this->Inventory->set('quantity', $quantity);
        if ($this->Inventory->save($this->request->data)) {         
                return 0;
            } else {
                return 1;
            }
    }
    
    public function addStockRecord($userid,$productid,$user_id) {
        $this->Inventory = ClassRegistry::init('Inventory');
        $this->Inventory->create();
        $this->Inventory->set('user_id',$userid);
        $this->Inventory->set('dcop_user_id', $this->Auth->user('id'));
        $this->Inventory->set('product_id', $productid);
        $this->Inventory->set('quantity', 0);
        if ($this->Inventory->save($this->request->data)) {         
            return 0;
        } else {
            return 1;
        }
    }

    /**
     *  Receive order line
     *  
     *  @param int $product_id
     *  @param int $warehouse_id
     *  @return array
     */
    public function getInventory($product_id, $warehouse_id, $fields = ['Inventory.id', 'Inventory.quantity', 'Inventory.product_id', 'Inventory.user_id']) {
        $inventoryRecord = $this->Inventory->getInvenotry($product_id, $warehouse_id, $fields);
        if(!$inventoryRecord && $this->Session->read('inventoryauto')) {
            $this->Inventory->create();
            if(!$this->Inventory->createRecord($product_id, $warehouse_id, 0, 0)) {
                return false;
            } else {
                $inventoryRecord = $this->Inventory->getInvenotry($product_id, $warehouse_id, $fields);
                if($inventoryRecord) {
                    return $inventoryRecord;
                }
            }
        } else {
            return $inventoryRecord;
        }
        return false;
    }

    /**
     *  Receive order line
     *  
     *  @param $orderline array OrdersLine
     *  @param 
     */
    public function receiveLine($orderline, $warehouse_id, $receivedqty, $shipment_id = 0, $receivenotes = '') {
        // Check is inventory record exists
        $inventoryRecord = $this->getInventory($orderline['OrdersLine']['product_id'], $warehouse_id);
        if(!$inventoryRecord) {
            $response['status'] = false;
            $response['message'] = 'We can\'t get invntory record for this location.';
            return $response;
        }

        $data['OrdersLine']['id'] = $orderline['OrdersLine']['id'];
        $data['OrdersLine']['warehouse_id'] = $warehouse_id;
        $data['OrdersLine']['shipment_id'] = $shipment_id;
        $data['OrdersLine']['quantity'] = $orderline['OrdersLine']['quantity'];
        $data['OrdersLine']['receivedqty'] = $receivedqty;
        $data['OrdersLine']['sentqty'] = 0;
        $data['OrdersLine']['dcop_user_id'] = $this->Auth->user('id');
        $data['OrdersLine']['receivenotes'] = $receivenotes;
            
        $dataSource = $this->OrdersLine->getDataSource();
        $dataSource->begin();
        if ($this->OrdersLine->save($data)) {

            // Check is user change inventory
            $ds2 = $this->Inventory->getDataSource();
            $ds2->begin();
            $existsQuantitiy = 0;
            if($orderline['OrdersLine']['warehouse_id'] != $warehouse_id) { // User change inventory
                if ($orderline['OrdersLine']['receivedqty'] > 0) { //we need move quantity from old inventory to new inventory
                    $is_error = false;
                    $oldInventoryRecord = $this->Inventory->getInvenotry($orderline['OrdersLine']['product_id'], $orderline['OrdersLine']['warehouse_id'], ['Inventory.id', 'Inventory.quantity']);
                    if($oldInventoryRecord) {
                        $this->Inventory->id = $oldInventoryRecord['Inventory']['id'];
                        $existsQuantitiy = $orderline['OrdersLine']['receivedqty'];
                        if(!$this->Inventory->saveField('quantity', $oldInventoryRecord['Inventory']['quantity'] - $orderline['OrdersLine']['receivedqty'])) {
                            $is_error = true;
                        }
                    } else {
                        $is_error = true;
                    }
                    if($is_error) {
                        $dataSource->rollback();

                        // Response
                        $response['status'] = false;
                        $response['message'] = __('The orders line could not be saved in new location. Please, try again.');
                        return $response;
                    }
                }
            }

            $this->Inventory->id = $inventoryRecord['Inventory']['id'];
            if ($receivedqty > 0) {
                $inventoryoffset = $receivedqty - $orderline['OrdersLine']['receivedqty'] ;
            } else {
                $inventoryoffset = $receivedqty;
            }
            $inventoryoffset = $inventoryoffset + $existsQuantitiy;

            $poststock = $inventoryRecord['Inventory']['quantity'] + $inventoryoffset;
            if($this->Inventory->saveField('quantity', $poststock)) {
                $dataSource->commit();
                $ds2->commit();
                $orderline = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.id' => $orderline['OrdersLine']['id']), 'callbacks' => false));
                $response['orderline'] = $orderline;
                $response['status'] = true;
                $response['message'] = __('Product SKU %s, quantity %s, were received to stock.', $orderline['Product']['sku'], $receivedqty);
                return $response;
            } else {
                $dataSource->rollback();
                $ds2->rollback();
            }
        }
        $response['status'] = false;
        $response['message'] = __('The orders line could not be saved. Please, try again.');
        return $response;
    }

    /**
     *  Issue order line
     *  
     *  @param array $orderline (OrdersLine object)
     *  @param array $inventory (Inventory object)
     *  @param array $data (Request['OrdersLine'])
     */
    public function issueLine($orderline, $inventory, $data) {
        
        $updata['OrdersLine']['id'] = $orderline['OrdersLine']['id'];
        $updata['OrdersLine']['warehouse_id'] = $data['OrdersLine']['warehouse_id'];
        $updata['OrdersLine']['shipment_id'] = $data['OrdersLine']['shipment_id'];
        //$updata['OrdersLine']['quantity'] = $orderline['OrdersLine']['quantity'];
        $updata['OrdersLine']['sentqty'] = $data['OrdersLine']['sentqty'];
        $updata['OrdersLine']['receivedqty'] = 0;
        $updata['OrdersLine']['dcop_user_id'] = $this->Auth->user('id');

        $dataSource = $this->OrdersLine->getDataSource();
        $dataSource->begin();
        if ($this->OrdersLine->save($updata)) {

            // Check is user change inventory
            $ds2 = $this->Inventory->getDataSource();
            $ds2->begin();
            $existsQuantitiy = 0;
            if($orderline['OrdersLine']['warehouse_id'] != $data['OrdersLine']['warehouse_id']) { // User change inventory
                if ($orderline['OrdersLine']['sentqty'] > 0) { //we need move quantity from old inventory to new inventory
                    $is_error = false;
                    $oldInventoryRecord = $this->Inventory->getInvenotry($orderline['OrdersLine']['product_id'], $orderline['OrdersLine']['warehouse_id'], ['Inventory.id', 'Inventory.quantity']);
                    if($oldInventoryRecord) {
                        $this->Inventory->id = $oldInventoryRecord['Inventory']['id'];
                        $existsQuantitiy = $orderline['OrdersLine']['sentqty'];
                        if(!$this->Inventory->saveField('quantity', $oldInventoryRecord['Inventory']['quantity'] + $orderline['OrdersLine']['sentqty'])) {
                            $is_error = true;
                        }
                    } else {
                        $is_error = true;
                    }
                    if($is_error) {
                        // Response
                        $dataSource->rollback();
                        $response['status'] = false;
                        $response['message'] = __('The orders line could not be saved in new location. Please, try again.');
                        return $response;
                    }
                }
            }

            $this->Inventory->id = $inventory['Inventory']['id'];
            if ($orderline['OrdersLine']['sentqty'] > 0) {
                $inventoryoffset = $data['OrdersLine']['sentqty'] - $orderline['OrdersLine']['sentqty'];
            } else {
                $inventoryoffset = $data['OrdersLine']['sentqty'];
            }
            $inventoryoffset = $inventoryoffset + $existsQuantitiy;

            $poststock = $inventory['Inventory']['quantity'] - $inventoryoffset;
            if($this->Inventory->saveField('quantity', $poststock)) {
                $dataSource->commit();
                $ds2->commit();
                $orderline = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.id' => $orderline['OrdersLine']['id'])));
                $response['orderline'] = $orderline;
                $response['status'] = true;
                $response['message'] = __('Product SKU %s, quantity %s, were issued.', $orderline['Product']['sku'], $data['OrdersLine']['sentqty']);
                return $response;
            } else {
                $dataSource->rollback();
                $ds2->rollback();
            }
        }
        $response['status'] = false;
        $response['message'] = __('The orders line could not be saved. Please, try again.');
        return $response;
    }

    public function issueKitProduct($orderline, $inventory, $request) {

        $kit_order_id = (-1 * $orderline['OrdersLine']['order_id']);
        #pr($orderline);
        #pr($request);
        #pr($inventory);

        if($data = $this->OrdersLine->find('first', ['conditions' => array('OrdersLine.order_id' => $kit_order_id, 'OrdersLine.product_id' => $inventory['Inventory']['product_id'])])) {
            $data['OrdersLine']['sentqty'] = $request['OrdersLine']['sentqty'];
            $data['OrdersLine']['comments'] = 'Assemble Kit';
        } else {
            $this->OrdersLine->create();
            $data = [];
            $data['OrdersLine'] = array(
                'order_id' => $kit_order_id,
                'line_number' => 1,
                'type' => 7,
                'product_id'  => $inventory['Inventory']['product_id'],
                'quantity' => abs($request['OrdersLine']['sentqty']),
                'damagedqty' => 0,
                'sentqty' => $request['OrdersLine']['sentqty'],
                'receivedqty' => 0,
                'foc' => '',
                'warehouse_id' => $request['OrdersLine']['warehouse_id'],
                'return' => '',
                'comments' => 'Assemble Kit',
                'user_id' => $inventory['Inventory']['user_id'],
                'dcop_user_id' => AuthComponent::user('id')
            );
        }
        
        $dataSource = $this->OrdersLine->getDataSource();
        $dataSource->begin();
        if ($this->OrdersLine->save($data)) {

            // Check is user change inventory
            $ds2 = $this->Inventory->getDataSource();
            $ds2->begin();
            $existsQuantitiy = 0;
            if($orderline['OrdersLine']['warehouse_id'] != $request['OrdersLine']['warehouse_id']) { // User change inventory
                if ($orderline['OrdersLine']['sentqty'] > 0) { //we need move quantity from old inventory to new inventory
                    $is_error = false;
                    $oldInventoryRecord = $this->Inventory->getInvenotry($inventory['Inventory']['product_id'], $orderline['OrdersLine']['warehouse_id'], ['Inventory.id', 'Inventory.quantity']);
                    if($oldInventoryRecord) {
                        $this->Inventory->id = $oldInventoryRecord['Inventory']['id'];
                        $existsQuantitiy = $orderline['OrdersLine']['sentqty'];
                        if(!$this->Inventory->saveField('quantity', $oldInventoryRecord['Inventory']['quantity'] + $orderline['OrdersLine']['sentqty'])) {
                            $is_error = true;
                        }
                    } else {
                        $is_error = true;
                    }
                    if($is_error) {
                        return false;
                    }
                }
            }

            $this->Inventory->id = $inventory['Inventory']['id'];
            if ($orderline['OrdersLine']['sentqty'] > 0) {
                $inventoryoffset = $data['OrdersLine']['sentqty'] - $orderline['OrdersLine']['sentqty'];
            } else {
                $inventoryoffset = $data['OrdersLine']['sentqty'];
            }
            $inventoryoffset = $inventoryoffset + $existsQuantitiy;

            $poststock = $inventory['Inventory']['quantity'] - $inventoryoffset;
            if($this->Inventory->saveField('quantity', $poststock)) {
                $dataSource->commit();
                $ds2->commit();
                
                return true;
            } else {
                $dataSource->rollback();
                $ds2->rollback();
            }
        }
        return false;
    }


    public function issueLineFull($orderline, $offset, $warehouse_id = null, $shipment_id = 0) {
        //pr($orderline);
        
        
       /* if($product['status_id'] == 13 || $product['deleted'] == 1) {
            $response['action'] = 'error';
            $response['message'] = __('The orders line could not be issue, product blocked or deleted.');
            echo json_encode($response);
            exit;
        }

        // Get invenotry record
        $inventoryRecord = $this->InventoryManager->getInventory($orderline['OrdersLine']['product_id'], $warehouse_id);
        if(!$inventoryRecord) {
            $response['action'] = 'error';
            $response['message'] = 'We can\'t get invntory record for this location.';
            echo json_encode($response);
            exit;
        }*/

        if( $orderline['Product']['uom'] == 'Kit' ) { //&& $this->_authUser['User']['kit_component_issue'] == 'issued' ) {
            return $this->issueKitLine($orderline, $offset, $warehouse_id, $shipment_id);
        } else {
            return ('Usual: '. $offset);
            //return $this->issueLine($orderline, $offset, $warehouse_id);
        }

        //pr($orderline);
        //exit;
    }

    public function issueKitLine($orderline, $offset, $warehouse_id = null, $shipment_id = 0) {

        $product_parts = [];
        //$this->Kit->recursive = -1;
        $product_parts = $this->Kit->find('all',array(
            'conditions' => array('Kit.product_id' => $orderline['product_id']),
            'contain' => array('ProductPart')
        ));

        // Get Kit invenotry record
        $inventoryRecord = $this->getInventory($orderline['product_id'], $warehouse_id);
        if(!$inventoryRecord) {
            $response['action'] = 'error';
            $response['message'] = 'We can\'t get invntory record for this location.';
            return $response;
        }

        $partInventoryRecord = [];
        foreach ($product_parts as $prdt) {
            $partInventoryRecord[$prdt['ProductPart']['id']] = $this->getInventory($prdt['ProductPart']['id'], $warehouse_id);
            if(!$partInventoryRecord[$prdt['ProductPart']['id']]) {
                $response['action'] = 'error';
                $response['message'] = 'We can\'t get invntory record for this location for one from components.';
                return $response;
            } else {
                #$partInventoryRecord[$prdt['ProductPart']['id']]['Inventory']['product_id'] = $prdt['ProductPart']['id'];
                #$partInventoryRecord[$prdt['ProductPart']['id']]['Inventory']['warehouse_id'] = $warehouse_id;
            }
        }

        // Get offests for each part
        foreach ($product_parts as $prdt) {
            $part_offset = $offset * $prdt['Kit']['quantity'];
            if(
                $partInventoryRecord[$prdt['ProductPart']['id']]['Inventory']['quantity'] < $part_offset * $prdt['Kit']['quantity'] && 
                $partInventoryRecord[$prdt['ProductPart']['id']]['Inventory']['quantity'] >=0 /*&& 
                !$this->Session->read('allow_negative') && !$this->request->data['OrdersLine']['confirm']*/
            ) {
                $response['action'] = 'confirm';
                $response['message'] = 'You are trying to issue a quantity greater than the quantity you have in inventory for one from component.';
                return $response;
            }
        }


        //$data['OrdersLine']['id'] = $orderline['id'];
        $data['OrdersLine']['shipment_id'] = $shipment_id;
        $data['OrdersLine']['warehouse_id'] = $warehouse_id;
        $data['OrdersLine']['sentqty'] = $offset;

        $ordline['OrdersLine'] = $orderline;
        $response = $this->issueLine($ordline, $inventoryRecord, $data);

        foreach ($product_parts as $prdt) {
            $p_data = $data;
            $p_data['OrdersLine']['sentqty'] = $offset * $prdt['Kit']['quantity'];

            $orderline1 = $ordline;
            $orderline1['OrdersLine']['sentqty'] = $offset * $prdt['Kit']['quantity'];
            $r = $this->issueKitProduct($orderline1, $partInventoryRecord[$prdt['ProductPart']['id']], $p_data);
        }

        #$response['action'] = 'success';
        #$response['message'] = 'Line successfully issued';
        return $response;


        /*    if($response['status']) {
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
        */
    }

}

?>
