<?php
App::uses('AppModel', 'Model');
/**
 * Shipment Model
 *
 * @property Order $Order
 * @property Asn $Asn
 * @property Status $Status
 * @property User $User
 */
class Shipment extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
 
 public $actsAs = array(
        'Search.Searchable','Containable'
    );
	
	public $filterArgs = array(

        'tracking_number' => array(
            'type' => 'like',
            'field' => 'tracking_number'
        ),
		
		'status_id' => array(
            'type' => 'value',
            'field' => 'status_id'
        ),
            
		
		'courier_id' => array(
            'type' => 'value',
            'field' => 'courier_id'
        ),
        
        'createdfrom' => array(
            'type' => 'expression',
            'method' => 'makeRangeCondition',
            'field' => 'Shipment.created >= ?'
        ),
		
		'search' => array(
            'type' => 'like',
            'field' => 'Shipment.tracking_number'
        ),
        
        'search' => array(
            'type' => 'value',
            'field' => 'Shipment.status_id'
        ),
             'search' => array(
            'type' => 'value',
            'field' => 'Shipment.courier_id'
        ),
        
    );
 
	public $validate = array(
		'tracking_number' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Tracking number cannot be empty',
				'required' => true,
				'on' => 'create', 
			),
			 'between' => array(
                'rule'    => array('between', 3, 100),
                'message' => 'Tracking number description should contain between 3-100 chars.'
            ),
            'unique' => array(
            'rule' => array('uniqueTracking', 'tracking_number'),
			'message' => 'Tracking number already in use. Please input a different number.',
			),
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Some characters are not valid.',
               // 'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
		),
		'weight' => array(
			'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'message' => 'Weight must be positive',
               'allowEmpty' => true,
				'required' => false,
            )
		), 
		'courier_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Courier cannot be empty',
				//'allowEmpty' => false,
				'required' => true,
				'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		), 
		
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Order' => array(
			'className' => 'Order',
			'foreignKey' => 'order_id',
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
		),
		'Courier' => array(
			'className' => 'Courier',
			'foreignKey' => 'courier_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Direction' => array(
			'className' => 'Direction',
			'foreignKey' => 'direction_id',
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
		)
	);
	
	public $hasMany = array(
		
		'OrdersLine' => array(
			'className' => 'OrdersLine',
			'foreignKey' => 'shipment_id',
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
	
/*	public function beforeFind($queryData) {
		if(CakeSession::read("Auth.User.is_admin") == false)
		{
	if (is_array($queryData['conditions']))
{	
        $defaultConditions = array('Shipment.user_id' => CakeSession::read("Auth.User.id"));
        $queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
        return $queryData;
		}
    }
   } */
   
   public function makeRangeCondition($data = array()) {
	$from = $data['createdfrom'].' 00:08:12';
    return $data['createdfrom'];
   }

   public function uniqueTracking($tracking_number = null) {
	   	if(!empty($this->data['Shipment']['id'])) {
	   		$this->requrcive = -1;
			$first = $this->find('first', array('conditions' => array('Shipment.id' => $this->data['Shipment']['id'])));
			if((!empty($first['Shipment']['tracking_number'])) && $tracking_number['tracking_number'] === $first['Shipment']['tracking_number']) {
				return $res = true;
			} else {
				$count = $this->find('count', array('conditions' => array('Shipment.tracking_number' => $tracking_number['tracking_number'], 'Shipment.user_id' => CakeSession::read("Auth.User.id"))));
				if(empty($count)) {
			       return $res = true;
			    } else {
			       return $res = false;
			    }
			}
		} else {
			$count = $this->find('count', array('conditions' => array('Shipment.tracking_number' => $tracking_number['tracking_number'],'Shipment.user_id' => CakeSession::read("Auth.User.id"))));
		    if(empty($count)) {
		       return true;
		    } else {
		       return false;
		    }
		}
   }
// public function ValidTextFields($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
//        $value = array_values($check);
//        $value = $value[0];

//        return preg_match('|^[0-9a-zA-Z\s_\-\\\@!#&"\'+]*$|', $value);
//   }
    
   

	//	public function beforeSave($options = array()) {
	//		if($this->data['Shipment']['user_id'] == CakeSession::read("Auth.User.id"))
	//		{
	//       return true;
	//		}
	//   }
}
