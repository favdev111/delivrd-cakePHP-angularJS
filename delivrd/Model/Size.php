<?php
App::uses('AppModel', 'Model');
/**
 * Size Model
 *
 * @property User $User
 * @property Product $Product
 */
class Size extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
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
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'size_id',
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
	
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Size name cannot be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                //  'required' => false,
                // 'allowEmpty' => true,
                'message' => 'Some characters are not valid.',
                // 'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        	'between' => array(
                'rule'    => array('between', 1, 30),
                'message' => 'Size name should contain between 1-30 chars.'
            ),
        	'unique' => array( 
				'rule' => array('uniqueSize', 'name'),
				'message' => 'Name already in use. Please select a different name.',
				'on' => 'create'
			),
		),
		'description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Description cannot be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
            'between' => array(
                    'rule'    => array('between', 1, 30),
                    'message' => 'Size description should contain between 1-30 chars.'
                ),
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Some characters are not valid.',
                // 'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        )
	);
	
//	public function ValidTextFields($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
//        $value = array_values($check);
//        $value = $value[0];

 //       return preg_match('|^[0-9a-zA-Z\s_\-\\\@!#&"\'+]*$|', $value);
//    }
	
	public function uniqueSize($name = null) {
		$count = $this->find('count', array('conditions' => array('Size.name' => $name, 'Size.user_id' => CakeSession::read("Auth.User.id"))));

		if(empty($count))
			return true;
		else 
			return false;
	}
	

}
