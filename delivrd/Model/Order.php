<?php
App::uses('AppModel', 'Model');
App::uses('CakeTime', 'Utility');

/**
 * Order Model
 *
 * @property Ordertype $Ordertype
 * @property User $User
 * @property DcopUser $DcopUser
 * @property State $State
 * @property Country $Country
 * @property Status $Status
 * @property Shipment $Shipment
 */
class Order extends AppModel {
	
	public $actsAs = array(
        'Search.Searchable','Containable'
    );
	
	public $filterArgs = array(
        'external_orderid' => array(
            'type' => 'like',
            'field' => 'external_orderid'
        ),
        'ship_to_customerid' => array(
            'type' => 'like',
            'field' => 'ship_to_customerid'
        ),
        'id' => array(
            'type' => 'value',
            'field' => 'id'
        ),
        'supplier_id' => array(
            'type' => 'value',
            'field' => 'supplier_id'
        ),
        'status_id' => array(
            'type' => 'value',
            'field' => 'status_id'
        ),
        'supplysource_id' => array(
            'type' => 'value',
            'field' => 'supplysource_id'
        ),
        
        'schannel_id' => array(
            'type' => 'value',
            'field' => 'schannel_id'
        ),
        'createdfrom' => array(
            'type' => 'expression',
            'method' => 'makeRangeCondition',
            'field' => 'Order.created >= ?'
        ),
		'search' => array(
            'type' => 'like',
            'field' => 'Order.external_orderid'
        )
    );
	
	public $validate = array(
		'external_orderid' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => 'Please enter reference order number',
					'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'allowdchars' => array(
					'rule' => 'ValidTextFields',
	                'required' => false,
	                'allowEmpty' => true,
	                'message' => 'Some characters are not valid.',
	                'on' => 'create', // Limit validation to 'create' or 'update' operations
	                ), 
	            'unique' => array(
				'rule' => array('uniqueref', 'external_orderid'),
				'message' => 'Order reference number already exists. Please select a different number.',
				'on' => 'create',
			),
		),
		'email' => array(
			'isValid' => array(
				'rule' => 'email',
				'allowEmpty' => true,
				'message' => 'Please enter a valid email address.'
			),
		),
		'shipping_costs' => array(
			'notBlank' => array(
				'rule' => array('money', 'left'),
				'message' => 'Shipping costs can only be a positive number',
				'allowEmpty' => true,
				'required' => true,
				'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'ship_to_customerid' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter customer name',
				'required' => true,
				'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Some characters are not valid.',
               	'on' => 'create', // Limit validation to 'create' or 'update' operations
            ), 
		),
		'ship_to_street' => array(
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Some characters are not valid.',
                'on' => 'create', // Limit validation to 'create' or 'update' operations
            ), 
		),
		'ship_to_city' => array(
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Some characters are not valid.',
                'on' => 'create', // Limit validation to 'create' or 'update' operations
            ), 
		),
		'ship_to_zip' => array(
			'alphaNumeric' => array(
                'rule' => array('custom', '/^[a-z0-9- ]*$/i'),
                 'required' => false,
                'allowEmpty' => true,
                'message' => 'Letters,numbers and spaces only',
              	'on' => 'create', // Limit validation to 'create' or 'update' operations
            ), 
		),
		'schannel_id' => array(
			'notBlank' => array(
	          	'rule' => array('notBlank'),
	          	'message' => 'Sales Channel cannot be empty',
        	), 
		),
		'url' => array(
			'webaddress' => array(
				'rule' => 'url',
				'message' => 'Please enter a valid URL. For example, http://www.example.com/p1.html',
				'required' => false,
				'allowEmpty' => true,
			),
		),
		'ship_to_phone' => array(
            'mobile' => array(
               'rule' => 'numeric',
               'allowEmpty' => true,
               'message' => 'Please enter a valid phone number.',
           )           
        ),
        'unit_price' => array(
        	'price' => array(
        		'rule' => array('decimal', 2)
        	),
        	'positive' => array(
		        'rule' => array('comparison', '>', 0),
		        'message' => 'Must be positive.'
		    )
        ),
        'quantity' => array(
        	'natural' => array(
        		'rule' => 'naturalNumber',
        		'message' => 'Please enter correct quantity.'
        	)
        )
	); 

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
 
	public $belongsTo = array(
		'Ordertype' => array(
			'className' => 'Ordertype',
			'foreignKey' => 'ordertype_id',
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
		'DcopUser' => array(
			'className' => 'DcopUser',
			'foreignKey' => 'dcop_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'State' => array(
			'className' => 'State',
			'foreignKey' => 'state_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Source' => array(
			'className' => 'Source',
			'foreignKey' => 'source_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Country' => array(
			'className' => 'Country',
			'foreignKey' => 'country_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Supplysource' => array(
			'className' => 'Supplysource',
			'foreignKey' => 'supplysource_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Supplier' => array(
			'className' => 'Supplier',
			'foreignKey' => 'supplier_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Schannel' => array(
			'className' => 'Schannel',
			'foreignKey' => 'schannel_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Shipment' => array(
			'className' => 'Shipment',
			'foreignKey' => 'order_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'OrdersLine' => array(
			'className' => 'OrdersLine',
			'foreignKey' => 'order_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public $hasOne = array(
 		'Address' => array('dependent' => true), 
    );
 
 	public function uniqueref($external_orderid = null) {
		$count = $this->find('count', array('conditions' => array('Order.external_orderid' => $external_orderid,'Order.user_id' => CakeSession::read("Auth.User.id"))));
		if($count == 1){
            return "Reference order ".$external_orderid['external_orderid']." already exists";
        } else {
            return true;
        }

	}
 
	public function makeRangeCondition($data = array()) {
		$from = $data['createdfrom'].' 00:08:12';
		return $data['createdfrom'];
	}
 
 	public function checkFutureDate($check) {
	    $value = array_values($check);
	    return CakeTime::fromString($value['0']) >= CakeTime::fromString(date('Y-m-d'));
	}
 
 	public function ValidTextFields($check) {
        $value = array_values($check);
        $value = $value[0];
        return preg_match('|^[0-9a-zA-Z\s_\+\-\/\\@!#&"\'\"\,\(\)\.+]*$|', $value);
    }

    public function distshipcosts($id) {
    	$ord_total_value = 0;
        $x = 0;
        $this->recurcive = -1;
        $order = $this->find('first', array('conditions' => array('Order.id' => $id)));

        //$ordersLine = ClassRegistry::init('OrdersLine');
        $lines = $this->OrdersLine->find('all', array('conditions' => array('OrdersLine.order_id' => $id)));
        foreach($lines as $line) {
            $ord_total_value += $line['OrdersLine']['total_line'];
        }
        // After calc of total order value, we find relative cost for line
        foreach($lines as $line) {
            $line_ship_csts = ($ord_total_value != 0 ? $line['OrdersLine']['total_line'] / $ord_total_value * $line['Order']['shipping_costs'] : 0);
            $this->OrdersLine->id = $line['OrdersLine']['id'];
            $this->OrdersLine->saveField('shipping_costs', $line_ship_csts);
        }
        return true;
    }

}
