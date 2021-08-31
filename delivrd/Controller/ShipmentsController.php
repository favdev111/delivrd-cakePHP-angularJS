<?php
App::uses('AppController', 'Controller');

/**
 * Shipments Controller
 *
 * @property Shipment $Shipment
 * @property PaginatorComponent $Paginator
 */

class ShipmentsController extends AppController {
	
	public $types = [1 => 'S.O.', 2 => 'P.O.'];

    public function beforeFilter() {
       parent::beforeFilter();
       $this->Auth->allow('trackingdata');
    }

    public function beforeRender() {
        parent::beforeRender();
    }

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator','EventRegister','Search.Prg', 'Access');
	public $theme = 'Mtro';

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index($index = 1) {
		// For network user must be allowed if:
		// 1. User have access to order channel
		// 2. User have access r/w to all order lines warahouse for shipment model

		$this->layout = 'mtrd';

		if ($this->Auth->user('is_limited') && empty($this->Access->_access['Shipments'])) {
            throw new MethodNotAllowedException(__('You have no access.'));
        }

        $limit = $this->Auth->user('list_limit');
        $options = array( 10 => '10', 25 => '25', 50 => '50', 100 => '100' );

        $networks = [];
        if(isset($this->Access->_access['Shipments'])) { // Get networks which alowed S.O. access
            foreach ($this->Access->_access['Shipments'] as $acc) {
                if(!isset($networks[$acc['Network']['created_by_user_id']])) {
                    $networks[$acc['Network']['created_by_user_id']]['name'] = $acc['Network']['name'];
                    $networks[$acc['Network']['created_by_user_id']]['access'] = $acc['NetworksAccess']['access'];
                } else {
                    $networks[$acc['Network']['created_by_user_id']]['access'] .= $acc['NetworksAccess']['access'];
                }
            }
        }

        $shipments = [];

        $direction = (isset($index) ? $index : 1);

        $statuses = $this->Shipment->Status->find('list',array('conditions' => array('object_type_id' => 4)));
        $this->set(compact('statuses', 'shipments', 'direction', 'index', 'networks', 'limit', 'options'));
	}


	public function ajax_index() {
        
        $conditions = array();
        if(isset($this->Access->_access['Shipments'])) {
        	$products = $this->Access->getProducts('Shipments');
        	$schannels = $this->Access->schannelList();
        	$warehouses = $this->Access->locationList('Shipments');

        	$allowed_channels = [];
	        $allowed_products = [];
	        if(isset($this->Access->_access['Shipments'])) {
	            foreach ($schannels as $key => $channel) {
	                if($key != 'My Schannels') {
	                    $allowed_channels = array_merge($allowed_channels, array_keys($channel));
	                }
	            }
	            if($products) {
	                $allowed_products = array_keys($products);
	            }
	        }

        	// Add network results
        	$conditions['OR'] = ['Shipment.user_id' => $this->Auth->user('id')];
        	$this->loadModel('OrdersLine');
            $orderIds = $this->OrdersLine->find('list', [
                'fields' => array('OrdersLine.order_id'),
                'conditions' => array(
                    'OrdersLine.product_id' => $allowed_products,
                    'OrdersLine.warehouse_id' => array_keys($warehouses)
                ),
                'callbacks' => false
            ]);
            $conditions['OR'][] = ['Order.id' => $orderIds, 'Order.schannel_id' => $allowed_channels];
        } else {
        	$conditions['Shipment.user_id'] = $this->Auth->user('id');
        }
        
        $limit = $this->Auth->user('list_limit');
        if($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }

        if ($this->request->query('direction_id')) {
           $conditions['Shipment.direction_id'] =  $this->request->query('direction_id');
        } else {
           $conditions['Shipment.direction_id'] = 1;
        }

        if ($this->request->query('status_id')) {
           $conditions['Shipment.status_id'] =  $this->request->query('status_id');
        }
        if($this->request->query('createdfrom')) {
            $conditions['Shipment.created >='] = $this->request->query('createdfrom');
        }
        if($this->request->query('tracking_number')) {
            $conditions['Shipment.tracking_number  like'] = "%" . $this->request->query('tracking_number') . "%";
        }
        
        $orderBy = 'Shipment.modified';
        if($this->request->query('sortby')) {
            $orderBy = $this->request->query('sortby');
        }
        $orderDir = 'DESC';
        if($this->request->query('sortdir')) {
            $orderDir = $this->request->query('sortdir');
        }
        $this->loadModel('Order');
        $this->Order->virtualFields = [
        	'wave' => '( SELECT wave_id FROM orderslines_waves WHERE ordersline_id IN (SELECT id FROM orders_lines WHERE order_id = Order.id) LIMIT 1)'
        ];

        $this->loadModel('OrdersLine');
        $this->OrdersLine->virtualFields = [
        	'wave' => '( SELECT wave_id FROM orderslines_waves WHERE ordersline_id = OrdersLine.id LIMIT 1)'
        ];
		$this->Paginator->settings = array(
			'contain' => [
				'Order' =>['id', 'status_id', 'user_id', 'external_orderid', 'wave'],
				//'Order.OrdersLine' => ['id', 'wave'],
				'Courier' => ['name'],
				'User' => ['id']],
	        'limit' => $limit,
	        'order' => array('Shipment.modified' => 'DESC')
	    );

        $shipments = $this->paginate($conditions);
        #pr($shipments);
        #exit;

        $response['draw'] = 1;
        $response['recordsTotal'] = $this->request->params['paging']['Shipment']['count'];
        $response['rows_count'] = count($shipments);
        $response['rows'] = $shipments;

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }


    public function indexsh() {
		$this->layout = 'mtrd';
		$this->Paginator->settings = array(
	        'limit' => 10,'order' => array('Shipment.modified' => 'DESC')
	    );
	    $direction = 1;
	    $conditions = array();

		if(empty($this->viewVars['isSearch'])) {
			if ( isset($this->request->params['pass'][0])) {
		   		$conditions['Shipment.direction_id'] =  $direction;
		   	} else {
			   	//by default, we show outbound shipments
			   	$conditions['Shipment.direction_id'] = 1;
		   	}
		    if (isset($this->request->query['status'])) {
		   		$conditions['Shipment.status_id'] =  $this->request->query['status'];
		   	}
	    	$this->set('shipments',$this->paginate($conditions));
			$this->set('direction',$direction);
		}

	 	$this->Prg->commonProcess();
        $this->Paginator->settings['conditions'] = $this->Shipment->parseCriteria($this->Prg->parsedParams());
		// If we are in search mode, paginator should be search results. else, we display all shipments
		if(isset($this->viewVars['isSearch']) && $this->viewVars['isSearch']) {
			if ($this->Auth->user('id')) {
	        	$conditions['Shipment.user_id'] = $this->Auth->user('id');
			}
			if ( isset($this->request->params['pass'][0])) {
		   		$conditions['Shipment.direction_id'] =  $direction;
		   	}
       		$this->set('shipments', $this->Paginator->paginate($conditions));
	   	}

		$this->Session->delete('scannedshipments');
		$this->Session->delete('shippinginvoice');
		$this->Session->delete('totalcost');
		$this->Session->delete('shipmentcount');

        // Status type 3 is only for shipping company to update
		$statuses = $this->Shipment->Status->find('list',array('conditions' => array('object_type_id' => 3)));
        $couriers = $this->Shipment->Courier->find('list',array('conditions' => array('Courier.user_id' => $this->Auth->user('id'))));
		$statussearch = array(15 => 'Opened', 6 => 'Picked & Packed',7 => 'Fully Received', 8 => 'Ready for pickup',16 => 'Partially Processed');
        foreach ($statuses as $key=>$status) {
          	$statussearch[$key] = $status;
        }

        $this->set('statuses', $statuses);
        $this->set('couriers', $couriers);
        $this->set('statussearch', $statussearch);
	}

	public function findex() {
	    $conditions = array();
	    if ($this->Auth->user('id')) {
	        $conditions['Shipment.dcop_user_id'] = $this->Auth->user('id');
            $conditions['Shipment.status_id'] = 8;
	    }
	    $this->set('shipments',$this->paginate($conditions));
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
		$this->checkshipmentexist($id);
		if (!$this->Shipment->exists($id)) {
			throw new NotFoundException(__('Invalid shipment'));
		}
		$options = array('conditions' => array('Shipment.id' => $id),'recursive' => 2);
		$shipment = $this->Shipment->find('first', $options);
		if($shipment['Shipment']['user_id'] != $this->Auth->user('id')) {
			$is_write = $this->Access->hasOrderShipmentAccess($shipment['Shipment']['order_id']);
		} else {
			$is_write = 1;
		}

		// currently, only one order per shipment, in the future - one shipment many orders
		$order = $this->Shipment->Order->find('first', array('conditions' => array('Order.id' => $shipment['Shipment']['order_id'])));
		$this->loadModel('OrdersLine');
		$orders_lines = $this->OrdersLine->find('all', array('conditions' => array('OrdersLine.order_id' => $shipment['Shipment']['order_id']), 'callbacks' => false));
		$objectevents = $this->EventRegister->getObjectEvent(4,$id,$this->Auth->user('id'));
		$statuses = $this->Shipment->Status->find('list');

		$this->set(compact('shipment', 'orders_lines', 'order','statuses', 'objectevents', 'is_write'));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add($order_id) {
		$this->layout = 'mtrd';

		$currenOrder = $this->Shipment->Order->find('first', array('conditions' => array('Order.id' => $order_id)));
		
        if (!$currenOrder) {
            throw new NotFoundException(__('Invalid order'));
        }

		if($currenOrder['Order']['user_id'] != $this->Auth->user('id')) { // Try to delete not own order
			if (!$this->Access->hasOrderShipmentAccess($order_id)) {
	        	throw new MethodNotAllowedException(__('You have no access to add shipment for this order.'));
	        }
            $products = $this->Access->getProducts($this->types[$currenOrder['Order']['ordertype_id']], 'w', $currenOrder['Order']['user_id']);
            $this->loadModel('OrdersLine');
            $order_lines = $this->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $order_id, 'OrdersLine.product_id' => array_keys($products)),
                'callbacks' => false
            ));
            if(count($order_lines) != count($currenOrder['OrdersLine'])) {
                $this->Session->setFlash(__('You can\'t add shipment to order number %s. It have products for which you have no access.',$order_id),'default',array('class'=>'alert alert-danger'));
                if($currenOrder['Order']['ordertype_id'] == 1) {
                    return $this->redirect(array('controller' => 'salesorders'));
                } else {
                    return $this->redirect(array('controller' => 'orders', 'action' => 'index', 2));
                }
            }
        }

		if ($this->request->is('post')) {
			$this->loadModel('Order');
		    $this->Order->id = $order_id;
		    $this->Order->saveField('status_id', 3);

			$this->Shipment->create();
			$this->request->data('Shipment.user_id',$currenOrder['Order']['user_id']);
			$this->request->data('Shipment.dcop_user_id', $this->Auth->user('id'));
			$this->request->data('Shipment.status_id',15);
            $this->request->data('Shipment.client', $currenOrder['Order']['user_id']);
			$this->request->data('Shipment.order_id', $order_id);
			if($currenOrder['Order']['ordertype_id'] == 1) {
				$this->request->data('Shipment.direction_id',1);
				$redirectto = 1;
			}
			if($currenOrder['Order']['ordertype_id'] == 2) {
				$this->request->data('Shipment.direction_id',2);
				$redirectto = 2;
			}
			if ($this->Shipment->save($this->request->data)) {
				$this->EventRegister->addEvent(4,1,$this->Auth->user('id'),$this->Shipment->id);
				$this->Session->setFlash(__('The shipment has been saved.'), 'admin/success', array());
				return $this->redirect(array('action' => 'index', 'index' => $redirectto));
			} else {
				$this->Session->setFlash(__('The shipment could not be saved. Please, try again.'), 'admin/danger', array());
			}
		}

		$statuses = $this->Shipment->Status->find('list');
		$couriers = $this->Shipment->Courier->find('list', array('conditions' => array('Courier.user_id' => $currenOrder['Order']['user_id'])));

        /*$usercity = strtoupper(substr($currenOrder['User']['city'], 0, 3));
        $rand = rand(10,99);
        $now = time();
        $trackingnumber = $usercity.$now.$rand;*/
		$this->set(compact('statuses', 'users','currenOrder','couriers'));
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
		$this->checkshipmentexist($id);
		if (!$this->Shipment->exists($id)) {
			throw new NotFoundException(__('Invalid shipment'));
		}

		$options = array('conditions' => array('Shipment.' . $this->Shipment->primaryKey => $id),'recursive' => 2);
		$shipment = $this->Shipment->find('first', $options);
		if($shipment['Order']['user_id'] != $this->Auth->user('id')) {
			if (!$this->Access->hasOrderShipmentAccess($shipment['Order']['id'])) {
	        	throw new MethodNotAllowedException(__('You have no access to edit shipment for this order.'));
	        }
        }

		if ($this->request->is(array('post', 'put'))) {
			$this->Shipment->recursive = -1;
			$shipment = $this->Shipment->findById($id);
			$verifyweight = $this->Session->read('verifyweight');
			if(isset($this->request->data['Shipment']['weight']) &&  $verifyweight == true)
			{

				$weightverified = $this->verifyweight($shipment['Shipment']['order_id'], $this->request->data['Shipment']['weight']);
				if($weightverified == 0)
				{
					$this->Session->setFlash(__('Shipment weight not correct.'), 'admin/danger', array());
				    return $this->redirect(array('action' => 'edit',$id));
				}
			}
			if ($this->Shipment->save($this->request->data)) {
				$this->Session->setFlash(__('Shipment number %s has been saved.', $id), 'admin/success', array());
				return $this->redirect(array('action' => 'index',$shipment['Shipment']['direction_id']));
			} else { 
				$options = array('conditions' => array('Shipment.' . $this->Shipment->primaryKey => $id),'recursive' => 2);
				$this->request->data = $this->Shipment->find('first', $options);
				$this->Session->setFlash(__('The shipment could not be saved. Please, try again.'), 'admin/danger', array());
			}
		} else {
			$this->request->data = $shipment;
		}

		$statuses = $this->Shipment->Status->find('list');
		$couriers = $this->Shipment->Courier->find('list', array('conditions' => array('Courier.user_id' => $shipment['Order']['user_id'])));

		$this->set(compact('statuses', 'shipment','couriers'));
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->Shipment->id = $id;
		$shipmentdata = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $id,'Shipment.user_id' => $this->Auth->user('id'))));
		if(empty($shipmentdata)) {
			$this->Session->setFlash(__('Shipment does not exist.'), 'admin/danger', array());
            return $this->redirect(array('action' => 'index','index' => 1));
		}
		if($shipmentdata['Shipment']['status_id'] != 15) {
			$this->Session->setFlash(__('Shipment not deleted. Only Opened shipments can be deleted.'), 'admin/warning', array());
            return $this->redirect(array('action' => 'index','index' => $shipmentdata['Shipment']['direction_id']));
		}

		if($shipmentdata['Order']['user_id'] != $this->Auth->user('id')) {
			if (!$this->Access->hasOrderShipmentAccess($shipmentdata['Order']['id'])) {
	        	throw new MethodNotAllowedException(__('You have no access to delete shipment for this order.'));
	        }
        }

		$this->loadModel('Order');

		$this->Order->id = $shipmentdata['Shipment']['order_id'];
		$this->Order->saveField('status_id', 2);
		if (!$this->Shipment->exists()) {
			throw new NotFoundException(__('Invalid shipment'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Shipment->delete()) {
			$this->Session->setFlash(__('The Shipment has been deleted.'), 'admin/success', array());
		} else {
			$this->Session->setFlash(__('The Shipment could not be deleted. Please, try again.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index','index' => $shipmentdata['Shipment']['direction_id']));
	}

	public function send($id = null) {
		$this->Shipment->id = $id;
		$shipmentdata = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $id)));

		if ($this->Shipment->save($this->request->data)) {
			$this->loadModel('Order');
			$this->Order->id = $this->request->params['pass'][0];
			$this->Order->saveField('status_id', 8);
			$this->EventRegister->addEvent(2,8,$this->Auth->user('id'),$this->Order->id);
			return $this->redirect(array('controller' => 'orders_lines','action' => 'issue', $shipmentdata['Shipment']['order_id']));

            // It will not work after first return???
            $this->EventRegister->addEvent(4,8,$this->Auth->user('id'),$id);
            $this->Session->setFlash(__('Shipment status set to Shipped'), 'admin/success', array());
            return $this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The Shipment could not be saved. Please, try again.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function receive($id = null) {
		$shipmentdata = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $id)));
		$this->loadModel('OrdersLine');
        $orders_lines = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.order_id' => $shipmentdata['Shipment']['order_id'])));
		return $this->redirect(array('controller' => 'orders_lines','action' => 'receive', $shipmentdata['Shipment']['order_id']));
	}

	public function packcomplete($id = null) {
		$this->Shipment->id = $id;
		$this->request->data('Shipment.status_id',6);
		if ($this->Shipment->save($this->request->data)) {
			$this->Session->setFlash(__('Shipment picked & packed.'), 'admin/success', array());
			return $this->redirect(array('action' => 'index',1));
		} else {
			$this->Session->setFlash(__('The Shipment could not be saved. Please, try again.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function packuncomplete($id = null) {
		$this->loadModel('Order');
		$order = $this->Order->find('first', array('conditions' => array('Order.id' => $id)));
		$this->Order->id = $id;
		$this->request->data('Order.status_id',3);
		$this->Shipment->id = $order['Shipment'][0]['id'];
		$this->request->data('Shipment.status_id',15);
		$this->EventRegister->addEvent(4,15,$this->Auth->user('id'),$this->Shipment->id);
		if($this->Order->save($this->request->data)){
			if ($this->Shipment->save($this->request->data)) {
				$this->Session->setFlash(__('Shipment pick and pack cancelled'), 'admin/success', array());
					return $this->redirect(array('action' => 'index',1));
				} else {
					$this->Session->setFlash(__('The shipment could not be saved. Please, try again.'), 'admin/danger', array());
				}
		}
		return $this->redirect(array('action' => 'index',1));
	}



	public function receivecomplete($id = null) {
		$this->Shipment->id = $id;
		$this->request->data('Shipment.status_id',7);
		$shipmentdata = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $id)));
		if ($this->Shipment->save($this->request->data)) {
			$this->Session->setFlash(__('Shipment was fully received'), 'admin/success', array());
			$this->loadModel('Order');
			$this->Order->id = $shipmentdata['Shipment']['order_id'];
			$this->Order->saveField('status_id', 4);
			$this->EventRegister->addEvent(2,4,$this->Auth->user('id'),$this->Order->id);
			return $this->redirect(array('action' => 'index',2));
		} else {
			$this->Session->setFlash(__('The shipment could not be saved. Please, try again.'), 'admin/danger', array());
		}

		return $this->redirect(array('action' => 'index'));
	}

	public function ship($id = null) {
		$this->Shipment->id = $id;
		$this->request->data('Shipment.status_id',8);
		$shipmentdata = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $id)));
		$this->loadModel('Order');
		$this->Order->id = $shipmentdata['Shipment']['order_id'];
		$this->Order->saveField('status_id', 8);
		$this->EventRegister->addEvent(2,8,$this->Auth->user('id'),$this->Order->id);
		if ($this->Shipment->save($this->request->data)) {
			$this->Session->setFlash(__('Shipment processing completed.'), 'admin/success', array());
			return $this->redirect(array('action' => 'index',1));
		} else {
			$this->Session->setFlash(__('The shipment could not be saved. Please, try again.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index'));
	}

    //Change status - by shipper
    public function changestatusp($id = null,$statusid = 8) {

		$this->Shipment->id = $id;
		if ($this->Shipment->exists($id)) {
			$this->Shipment->recursive = -1;
			$shipmentdata = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $id)));

			$this->request->data('Shipment.status_id', $statusid);
			if ($this->Shipment->save($this->request->data)) {
	            $this->EventRegister->addEvent(2,$statusid,$this->Auth->user('id'),$this->Shipment->id);
	            if($statusid == 8) {
	            	// We need set order status to 'Shipped'
	            	$this->loadModel('Order');
					$this->Order->id = $shipmentdata['Shipment']['order_id'];
					$this->Order->saveField('status_id', 8);
					$this->EventRegister->addEvent(2,8,$this->Auth->user('id'),$this->Order->id);
	            }
				$this->Session->setFlash(__('Shipment status changed.'), 'admin/success', array());
				
			} else {
				$this->Session->setFlash(__('The Shipment could not be saved. Please, try again.'), 'admin/danger', array());
			}
		} else {
			$this->Session->setFlash(__('Shipment not found.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function multiplechangestatus() {

        if($this->request->is('post')) {
        	if(!empty($this->request->data['Shipment']['id'])) {
	        	$ids = explode(',', $this->request->data['Shipment']['id']);

				foreach($ids as $id){
					$this->Shipment->id = $id;
					$this->request->data['Shipment1']['status_id'] = $this->request->data['Shipment']['status'];
					$this->request->data['Shipment1']['id'] = $id;
					$shipmentdata = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $id)));

					$this->Shipment->save($this->request->data['Shipment1']);
					$this->EventRegister->addEvent(2,$this->request->data['Shipment']['status'],$this->Auth->user('id'),$this->Shipment->id);
				}
				$this->Session->setFlash(__('Shipment status changed.'), 'admin/success', array());
					return $this->redirect(array('action' => 'indexsh'));
			} else{
				$this->Session->setFlash(__('The Shipment could not be saved. Please, try again.'), 'admin/danger', array());
				return $this->redirect(array('action' => 'indexsh'));
			}
        }
	}

	public function uncomplete($id = null) {
		$shipmentdata = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $id)));
		$this->Shipment->id = $id;
		$new_status = ($shipmentdata['Shipment']['direction_id'] == 1 ? 6 : 15);

		$this->request->data('Shipment.status_id',$new_status);

		$this->loadModel('Order');
		$this->Order->id = $shipmentdata['Shipment']['order_id'];
		$this->Order->saveField('status_id', 3);
		$this->EventRegister->addEvent(2,3,$this->Auth->user('id'),$this->Order->id);
		if ($this->Shipment->save($this->request->data)) {
				$this->EventRegister->addEvent(2,3,$this->Auth->user('id'),$this->Shipment->id);
				$this->Session->setFlash(__('Shipment completion reverted.'), 'admin/success', array());
				$indextype = ($shipmentdata['Shipment']['direction_id'] == 1 ? 1 : 2);
				return $this->redirect(array('action' => 'index',$indextype));
			} else {
				$this->Session->setFlash(__('The shipment could not be saved. Please, try again.'), 'admin/danger', array());
			}

			return $this->redirect(array('action' => 'index'));
	}

	public function find() {
	  	if ($this->Auth->user('id')) {
	        $conditions['Shipment.user_id'] = $this->Auth->user('id');
	    }
		if ( $this->request->params['pass'][0]) {
	   		$conditions['Shipment.direction_id'] =  $this->request->params['pass'][0];
	   	}

        $this->Prg->commonProcess();
        $this->Paginator->settings['conditions'] = $this->Shipment->parseCriteria($this->Prg->parsedParams());
		$users = $this->Shipment->User->find('list');
		$myuser = $this->Auth->user('id');
		$this->set(compact('myuser','users'));
        $this->set('shipments', $this->Paginator->paginate());
    }

    public function massupdate() {

		if ($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['updateshipment']['shippinginvoice']))
			{
				$this->Session->write('shippinginvoice', $this->request->data['updateshipment']['shippinginvoice']);
				$this->Session->write('totalcost', $this->request->data['updateshipment']['totalcost']);
				$this->Session->write('shipmentsnum', $this->request->data['updateshipment']['shipmentsnum']);
			}
			if(isset($this->request->data['updateshipment']['trackingnumber']))
				{
				$shipment = $this->Shipment->findByTrackingNumber($this->request->data['updateshipment']['trackingnumber']);
				if(!empty($shipment))
				{

					if($shipment['Shipment']['shippinginvoice'] != $this->Session->read('shippinginvoice'))
					{
					$shipinvstr = "'".$this->Session->read('shippinginvoice')."'";
					$this->Shipment->updateAll(
					array('Shipment.status_id' => 8, 'Shipment.shippinginvoice' => $shipinvstr),
					array('Shipment.tracking_number' => $this->request->data['updateshipment']['trackingnumber']));

					$avgcost = round(($this->Session->read('totalcost') / $this->Session->read('shipmentcount')),2);
					$this->Session->write('shipmentcount', $this->Session->read('shipmentcount') + 1);
					$this->Session->write('avgshipcost', $avgcost);
					$this->Session->setFlash(__('Shipment with tracking number %s  status changed to ship',$this->request->data['updateshipment']['trackingnumber']), 'admin/success', array());
				} else {
					$this->Session->setFlash(__('Shipment with tracking number  %s already updated',$this->request->data['updateshipment']['trackingnumber']), 'admin/danger', array());
				}
				} else {
					$this->Session->setFlash(__('Shipment with tracking number  %s could not be found',$this->request->data['updateshipment']['trackingnumber']), 'admin/danger', array());
			}
		}
		if(isset($this->request->data['updateshipment']['dc']))
		{
			$avgcost = round(($this->Session->read('totalcost') / $this->Session->read('shipmentcount')),2);
			$this->Shipment->updateAll(
					array('Shipment.shipping_costs' =>  $avgcost),
					array('Shipment.shippinginvoice' => $this->Session->read('shippinginvoice')));
		}
	}


	}

	public function exportcsv($direction = null) {

		$exportlines = array();
		
		$this->Shipment->contain(array(
			'Status' => array('name'),
			'Courier' => array('name'),
			'OrdersLine' => array(
				'line_number',
				'quantity',
				'receivedqty',
				'damagedqty',
				'unit_price',
				'total_line',
				'comments'
			),
			'OrdersLine.Order',
			'OrdersLine.Order.State',
			'OrdersLine.Order.Country',
			'OrdersLine.Order.Supplier' => array('name'),
			'OrdersLine.Product' => array('name', 'sku'),
			'OrdersLine.Order.Address'
		));

		$shipments = $this->Shipment->find('all',array('conditions' => array('Shipment.direction_id' => 1), 'recursive' => 2));

		$_serialize = 'exportlines';

        if($direction == 1) {
			foreach($shipments as $shipment) {
				foreach($shipment['OrdersLine'] as $orderline) {
					$line = array();
					array_push($line,$shipment['Shipment']['id']);
					array_push($line,$shipment['Shipment']['created']);
					array_push($line,$shipment['Status']['name']);
					array_push($line,$shipment['Courier']['name']);
					array_push($line,$shipment['Shipment']['shipping_costs']);
					array_push($line,$shipment['Shipment']['weight']);
					
					array_push($line,$orderline['Order']['ship_to_customerid']);
					array_push($line,$orderline['Order']['ship_to_street']);
					array_push($line,$orderline['Order']['ship_to_city']);
					array_push($line,$orderline['Order']['ship_to_zip']);
					if(!empty($orderline['Order']['State'])) {
						array_push($line,$orderline['Order']['State']['code']);
						array_push($line,$orderline['Order']['State']['name']);
					} else {
						array_push($line,'');
						array_push($line,'');
					}
					if(!empty($orderline['Order']['Country'])) {
						array_push($line,$orderline['Order']['Country']['code']);
						array_push($line,$orderline['Order']['Country']['name']);
					} else {
						array_push($line,'');
						array_push($line,'');
					}
					
					array_push($line,$orderline['Order']['id']);
					array_push($line,$orderline['line_number']);
					array_push($line,$orderline['Order']['external_orderid']);
					array_push($line,$orderline['Product']['name']);
					array_push($line,$orderline['Product']['sku']);
					array_push($line,$orderline['quantity']);
					array_push($line,$orderline['receivedqty']);
					array_push($line,$orderline['damagedqty']);
					array_push($line,$orderline['unit_price']);
					array_push($line,$orderline['total_line']);
					array_push($line,$orderline['comments']);
					array_push($exportlines,$line);
				}
			}
			$_header = array('Id', 'Created', 'Status','Carrier', 'ShippingCosts', 'ShipmentWeight','CustomerName','ShipToAddress','ShipToCity','ShipToZip','ShipToStateCode','ShipToStateName','ShipToCountryCode', 'ShipToCountryName','OrderId','OrderLineNumber','ReferenceOrderId','ProductName','SKU','OrderedQuantity','ReceivedQuantity','DamagedQuantity','UnitPrice','TotalLine', 'Notes');
		}

    	if($direction == 2) {
			foreach($shipments as $shipment) {
				foreach($shipment['OrdersLine'] as $orderline) {
					$line = array();
					array_push($line,$shipment['Shipment']['id']);
					array_push($line,$shipment['Shipment']['created']);
					array_push($line,$shipment['Status']['name']);
					array_push($line,$shipment['Courier']['name']);
					array_push($line,$shipment['Shipment']['shipping_costs']);
					array_push($line,$shipment['Shipment']['weight']);
					if(isset($orderline['Order']['Supplier']['name'])) {
						array_push($line,$orderline['Order']['Supplier']['name']);
					} else {
						array_push($line, '');
					}
					array_push($line,$orderline['Order']['id']);
					array_push($line,$orderline['line_number']);
					array_push($line,$orderline['Order']['external_orderid']);
					array_push($line,$orderline['Product']['name']);
					array_push($line,$orderline['Product']['sku']);
					array_push($line,$orderline['quantity']);
					array_push($line,$orderline['receivedqty']);
					array_push($line,$orderline['damagedqty']);
					array_push($line,$orderline['unit_price']);
					array_push($line,$orderline['total_line']);
					array_push($line,$orderline['comments']);
					array_push($exportlines,$line);
				}

			}
			$_header = array('Id', 'Created','Status','Carrier', 'ShippingCosts', 'ShipmentWeight','Supplier','OrderId','LineNumber','ReferenceOrderId','ProductName','SKU','OrderedQuantity','ReceivedQuantity','DamagedQuantity','UnitPrice','TotalLine', 'Notes');
		}

       	if($direction == 99) {
			foreach($shipments as $shipment){
				$line = array();
				array_push($line,$shipment['Shipment']['id']);
				array_push($line,$shipment['Shipment']['created']);
				array_push($line,$shipment['Status']['name']);
				array_push($line,$shipment['Courier']['name']);
				array_push($line,$shipment['Shipment']['shipping_costs']);
				array_push($line,$shipment['Shipment']['weight']);
                array_push($line,$shipment['User']['company']);
                array_push($line,$shipment['User']['street']);
                array_push($line,$shipment['User']['city']);
                array_push($line,$shipment['User']['stateprovince']);
                array_push($line,$shipment['User']['zip']);
                if(isset($shipment['User']['country']))
                    array_push($line,$shipment['User']['country']);
				array_push($line,$shipment['Order']['ship_to_customerid']);
				array_push($line,$shipment['Order']['ship_to_street']);
				array_push($line,$shipment['Order']['ship_to_city']);
				array_push($line,$shipment['Order']['ship_to_zip']);
                if(isset($shipment['Order']['State']['code']))
					array_push($line,$shipment['Order']['State']['code']);
                if(isset($shipment['Order']['State']['code']))
					array_push($line,$shipment['Order']['State']['name']);
               	if(isset($shipment['Order']['Country']['code']))
				 	array_push($line,$shipment['Order']['Country']['code']);
                if(isset($shipment['Order']['Country']['name']))
					array_push($line,$shipment['Order']['Country']['name']);
				array_push($exportlines,$line);

			}
			$_header = array('Id', 'Created', 'Status','Carrier', 'ShippingCosts', 'ShipmentWeight','PartnerName','PartnerStreet','PartnerCity','PartnerRegion','Partnerzip','PartnerCountry','CustomerName','ShipToAddress','ShipToCity','ShipToZip','ShipToStateCode','ShipToStateName','ShipToCountryCode', 'ShipToCountryName');
		}

		$file_name = "Delivrd_".date('Y-m-d-His')."_shipments.csv";
		$this->response->download($file_name);
	    $this->viewClass = 'CsvView.Csv';
	   	$this->set(compact('exportlines', '_serialize', '_header'));
	}

	public function verifyweight($order_id = null, $enteredweight = null)
			{
				//Get all order lines
				$totalweight = 0;
				$this->loadModel('Order');

				$order = $this->Order->find('first', array('contain' => array('OrdersLine'),'conditions' => array('Order.id' => $order_id, 'Order.user_id' => $this->Auth->user('id'))));
				//Loop through order lines, including pack material
				foreach ($order['OrdersLine'] as $orderline)
				{
					$this->loadModel('Product');
					if($orderline['line_number'] == '9999')
					{

						$packweightarr = $this->Product->find('first',array('fields'=>array('Product.weight'),'conditions' => array('Product.id' => $orderline['product_id'])));
						$packweight = $packweightarr['Product']['weight'];
					} else {
						$packweight = 0;
						$lineweight = $this->Product->find('first',array('fields'=>array('Product.weight'),'conditions' => array('Product.id' => $orderline['product_id'])));
					}

					$totalweight += ($lineweight['Product']['weight']*$orderline['sentqty'] + $packweight);
				}

					$delta = abs(1-($totalweight/$enteredweight));
					if( $delta > 0.1)
					{
						return 0;
					} else {
						return 1;
					}

			}


    public function checkshipmentexist($id) {
		$this->Shipment->contain();
		$hasshipment = $this->Shipment->findById($id);
		if (!$hasshipment) {
			$this->Session->setFlash(__('Shipment does not exist.'), 'admin/danger', array());
			return $this->redirect(array('action' => 'index',1));
		}

	}

    public function trackingdata($trackingnumber = null)
    {
        $statusarr = array();
        $this->Shipment->recursive = 0;
        $shipment = $this->Shipment->findByTrackingNumber($trackingnumber);
        $this->loadModel('Event');
        $this->Event->recursive = 0;
        $trackingevents = $this->Event->find('all', array('conditions' => array('Event.object_type_id' => 2, 'Event.object_id' => $shipment['Shipment']['id'])));
        $this->loadModel('Status');
        $statuses = $this->Status->find('list');
        var_dump($statuses);


        $this->layout = null ;

        foreach ($trackingevents as $key=>$trackingevent) {
            $status_description = $statuses[$trackingevent['Event']['status_id']];
            $statusarr[$key] = $status_description." at ".$trackingevent['Event']['created'];
        }
        $statusreturn =  json_encode($statusarr, JSON_PRETTY_PRINT);
        $this->set('statusreturn', $statusreturn);
    }

    public function is_shipment_access($order_id) {
    	$this->autoRender = false;
    	$this->response->type('json');
    	$response['access'] = $this->Access->hasOrderShipmentAccess($order_id);
    	$json = json_encode($response);
    	$this->response->body($json);
    }
}
