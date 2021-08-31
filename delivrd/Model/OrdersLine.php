<?php
	App::uses('AppModel', 'Model');
	/**
	 * OrdersLine Model
	 *
	 * @property Order $Order
	 * @property Product $Product
	 * @property DcopUser $DcopUser
	 */
	class OrdersLine extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
 
 	public $actsAs = array(
        'Search.Searchable','Containable'
    );
	
	public $filterArgs = array(
        'sku' => array(
            'type' => 'like',
            'field' => array('Product.sku','Product.barcode')
        ),
		 'order_id' => array(
            'type' => 'like',
            'field' => 'order_id'
        ),	
		'search' => array(
            'type' => 'like',
            'field' => 'OrdersLine.sku'
        ),
        'warehouse_id' => array(
          'type' => 'value',
          'field' => 'warehouse_id'
      	),
    );
	public $validate = array(
		'quantity' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Quantity should be a number',
				'required' => true,
				'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
            'positive' => array(
                'rule' => array('comparison', '>', 0),
                'message' => 'Quantity must be greater than 0', 
                'on' => 'update', // Limit validation to 'create' or 'update' operations
            ),
		), 
   		'unit_price' => array(
		),
		'sentqty' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Sent quantity field is not blank',
            ),
            'positive' => array(
                'rule' => array('money', 'left'),
                'required' => true,
                'message' => 'The value cannot be negative',
            ),
            /*'limit' => array(
            'rule' => 'checkSentqty',
            'message' => 'Sent quantity is not greater than Order quantity'
          	)*/
        ),   
        /*
		 * We allow negative inventory Denisch
        'receivedqty' => array(
            'limit' => array(
            'rule' => 'checkReceivedqty',
            'message' => 'Receive quantity is not greater than Order quantity'
          	)
        ),*/

	);


	public function checkSentqty() {
	  if($this->data['OrdersLine']['sentqty'] > 0) {
	  	if ($this->data['OrdersLine']['sentqty'] <= $this->data['OrdersLine']['quantity']){
        	return true;
      	} else {
      		 return false;
      	} 
	  }
	  return true;
    }

    public function checkReceivedqty() {
	  if($this->data['OrdersLine']['receivedqty'] > 0) {
	  	if ($this->data['OrdersLine']['receivedqty'] <= $this->data['OrdersLine']['quantity']){
        	return true;
      	} else {
      		 return false;
      	} 
	  }
	  return true;
    }

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	public function isfoc($check) {
		if($this->data['OrdersLine']['foc'])
		{ 
			if($check['unit_price'] > 0)
			{
			return false;
			} else {
			return true;
		}
		} else {
			return true;
		}     

	}

	public function checkreturn($check) 
	{
		if(isset($this->data['OrdersLine']['id']) && isset($this->data['OrdersLine']['return']))
		{
			$sentqty = $this->find('first',array('fields' => array('sentqty'), 'conditions' => array('OrdersLine.id'  => $this->data['OrdersLine']['id'])));
		
			if($sentqty['OrdersLine']['sentqty'] < $this->data['OrdersLine']['receivedqty'])
			{ 
				return false;
			} else {
				return true;
			}     
		} else {
			return true;
		}
	}

	/**
	 * hasOne associations
	 *
	 * @var array
	 */
	public $hasOne = array(
		'OrderSchedule' => array(
			'className' => 'OrderSchedule',
			'foreignKey' => 'ordersline_id'
		),
	);
	
	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Order' => array(
			'className' => 'Order',
			'foreignKey' => 'order_id',
			'counterCache' => [
                'orderlines_count' => ['OrdersLine.type !=' => 10]
            ]
		),
		'Shipment' => array(
			'className' => 'Shipment',
			'foreignKey' => 'shipment_id',
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
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		),
		'Serial' => array(
			'className' => 'Serial',
			'foreignKey' => 'serial_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array( //Who owner of order line
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DcopUser' => array( //Who create order line (network user for example)
			'className' => 'DcopUser',
			'foreignKey' => 'dcop_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
	
	public function beforeSave($options = array()) {
		$this->data['OrdersLine']['dcop_user_id'] = CakeSession::read("Auth.User.id");
		return true;
	}
    
    public function beforeFind($queryData) {
    	if (is_array($queryData['conditions'])) {	
            //$defaultConditions = array('OrdersLine.user_id' => CakeSession::read("Auth.User.id"));
            //$queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
            return $queryData;
		}
    }

    public function afterSave($created, $options = array()) {
    	$order_id = $this->find('first', ['conditions' => array('OrdersLine.id' => $this->id), 'fields' => array('OrdersLine.order_id')]);
    	$orderModel = ClassRegistry::init('Order');
    	$order = $orderModel->find('first', ['conditions' => array('Order.id' => $order_id['OrdersLine']['order_id']), 'fields' => array('Order.*'), 'contain'=>array()]);
    	if($order) {
	    	$order['Order']['modified'] = date('Y-m-d H:i:s');
	    	$orderModel->save($order, false);
	    }
    	return true;
    }
	
}