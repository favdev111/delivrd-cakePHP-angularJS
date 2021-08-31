<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 * @property Product $Product
 */
class User extends AppModel {

	/**
	 * Name
	 *
	 * @var string
	 */
	public $name = 'User';

	public $displayField = 'username';

	//public $recursive = -1;

	public $actsAs = array(
       'Containable',
       'Upload.Upload' => array(
            'logo' => array(
                'deleteFolderOnDelete' => true,
                'deleteOnUpdate' => true,
                'uploadFileNameMaxSize' => 250,
            )
        )
    );

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'username' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),

		'password' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'logo_url' => array(
			'webaddress' => array(
				'rule' => 'url',
				'message' => 'Please enter a valid image URL. For example, http://www.example.com/images/p1.jpg',
			),
		)
	);

	public $pickOptions = array(1 => 'No Scanning', 2 => 'Scan Bin', 3 => 'Scan SKU/EAN', 5 => 'Scan both bin and SKU/EAN');
	// 6 => 'Scan both bin and SKU/EAN with count'
	// 4 => 'Scan SKU/EAN with count'
	public $batchOptions = array(1 => 'No Scanning', 2 => 'Scan Bin', 3 => 'Scan SKU/EAN', 4 => 'Scan both bin and SKU/EAN');
	public $invAlerts = array(1 => 'Dont send any alerts', 2 => 'Send only low inventory alerts', 3 => 'Send list of all inventory');

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'user_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Warehouse' => array(
			'className' => 'Warehouse',
			'foreignKey' => 'user_id',
		)
	);

	public $hasOne = array(
 		'Address' => array(
 			'className' => 'Address',
 			'foreignKey' => 'user_address_id',
 			'dependent' => true
 		),
 		'Subscription' => array(
 			'className' => 'Subscription',
 			'foreignKey' => 'user_id',
 			'dependent' => true
 		),
    );

	public $belongsTo = array(
	    'Zone' => array(
				'className' => 'Zone',
				'foreignKey' => 'timezone_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
	    'Currency' => array(
			'className' => 'Currency',
			'foreignKey' => 'currency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Msystem' => array(
			'className' => 'Msystem',
			'foreignKey' => 'msystem_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	public function beforeSave($options = array()) {
	    if (isset($this->data[$this->alias]['password'])) {
	        $this->data[$this->alias]['password'] = AuthComponent::password(
	            $this->data[$this->alias]['password']
	        );
	    }
	    if (isset($this->data[$this->alias]['username'])) {
	        $this->data[$this->alias]['slug'] = strtolower(Inflector::slug($this->data[$this->alias]['username'], '-'));
	    }
	    return true;
	}

	public function saveSetting($id, $param, $value) {
		$user = $this->find('first', ['conditions' => ['User.id' => $id], 'contain' => false]);
		$settings = json_decode($user['User']['settings'], true);
		if(!$settings) {
			$settings = [];
		}
		
		$settings[$param] = $value;
		$this->id = $id;
		$this->saveField('settings', json_encode($settings));
		#$this->Session->write('settings', $settings);
		CakeSession::write('Auth.User.settings', json_encode($settings));
		return true;
	}

	public function productCount($user_id) {
		#$product_count = $this->Product->find('count', ['conditions' => array('Product.user_id' => $user_id)]);
		#return $product_count;
	}


	public function getAuthUser($user_id) {
		$user = $this->find('first', [
            'conditions' => array('User.id' => $user_id),
            'contain' => array('Subscription')
        ]);
        return $user;
	}
}
