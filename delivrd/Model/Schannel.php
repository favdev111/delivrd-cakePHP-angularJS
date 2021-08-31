<?php
App::uses('AppModel', 'Model');
/**
 * Schannel Model
 *
 * @property Order $Order
 */
class Schannel extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
    public $actsAs = array(
        'Search.Searchable'
    );
	
	public $filterArgs = array(
        'name' => array(
            'type' => 'like',
            'field' => 'name'
        ),
      
    );
    
    public $validate = array(
	
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Name cannot be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Some characters are not valid.',
               // 'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
                     'unique' => array(
            'rule' => array('uniqueName', 'name'),
			'message' => 'Sales channel Name already in use. Please select a different name.',
			'on' => 'create'
			),
		),
		
		'url' => array(
			'webaddress' => array(
				'rule' => 'url',
				'message' => 'Please enter a valid URL. For example, www.example.com',
				'required' => false,
				'allowEmpty' => true,
			),
			),
		
	);


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Order' => array(
			'className' => 'Order',
			'foreignKey' => 'schannel_id',
			'dependent' => false,
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
	
//	public function ValidTextFields($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
 //       $value = array_values($check);
//        $value = $value[0];

 //       return preg_match('|^[0-9a-zA-Z\s_\-\\\@!#&"\'+]*$|', $value);
 //   }
	public function uniqueName($name = null) {

	$count = $this->find('count', array('conditions' => array('Schannel.name' => $name,'Schannel.user_id' => CakeSession::read("Auth.User.id"))));
        return $count == 0;

}

}
