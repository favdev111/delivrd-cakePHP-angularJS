<?php
App::uses('AppModel', 'Model');
/**
 * BarcodeStandard Model
 *
 */
class Integration extends AppModel {

    public $actsAs = array(
        'Search.Searchable',
        'Containable'
    );
	
	public $filterArgs = array(
        'name' => array(
            'type' => 'like',
            'field' => 'name'
        ),
    );
        
    public $validate = array(
	
		'backend' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Name cannot be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
            /*'unique' => array(
                'rule' => array('uniqueName', 'name'),
    			'message' => 'Integration already exists.',
    			'on' => 'create'
			),*/
            'between' => array(
                'rule'    => array('between', 3, 255),
                'message' => 'Integration name should contain between 3-255 chars.'
            ),
		),
		'allowdchars' => array(
			'rule' => 'ValidTextFields',
            'required' => false,
            'allowEmpty' => true,
            'message' => 'Some characters are not valid.',
           // 'on' => 'create', // Limit validation to 'create' or 'update' operations
        ),
        
        'username' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Username cannot be empty'
			),
        ),
        'password' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Password / Secret cannot be empty'
			),
        ),
        'url' => array(
            'webaddress' => array(
			'rule' => 'url',
			'message' => 'Please enter a valid image URL. For example, http://my.store.com/',
		),),
	
		
	);

	public function ValidTextFields($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
        $value = array_values($check);
        $value = $value[0];

        return preg_match('|^[0-9a-zA-Z\s_\-\\\@!#&"\'+]*$|', $value);
    }
    
    public function uniqueName($backend = null) {
		$count = $this->find('count', array('conditions' => array('Integration.backend' => $backend,'Integration.user_id' => CakeSession::read("Auth.User.id"))));
        return $count == 0;

	}

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    
	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Schannel' => array(
			'className' => 'Schannel',
			'foreignKey' => 'schannel_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

    /**
     * hasMany associations
     *
     * @var array
     
    public $hasMany = array(
        'Transfer' => array(
            'className' => 'Transfer',
            'foreignKey' => 'source_id',
            'conditions' => '',
        )
    );*/
}
