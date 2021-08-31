<?php
App::uses('AppModel', 'Model');
/**
 * Group Model
 *
 * @property Product $Product
 */
class Category extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Category name cannot be empty',
			),
            'between' => array(
                    'rule'    => array('between', 3, 30),
                    'message' => 'Category name should contain between 3-30 chars.'
                ),
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                'required' => true,
                'message' => 'Some characters are not valid.',
                ),
			'unique' => array( 
					'rule' => array('uniqueCategory', 'name'),
					'message' => 'Name already in use. Please select a different name.',
					'on' => 'create',
					'last' => true
				)
		),            
        'description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Description cannot be empty',
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
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'category_id',
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
	
	public function ValidTextFields($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
        $value = array_values($check);
        $value = $value[0];

        return preg_match('|^[0-9a-zA-Z\s_\+\-\/\\@!#&"\'\"\,\(\)\.+]*$|', $value);
    }

    public function uniqueCategory($name = null) {

    	if(!empty($this->data['Category']['user_id'])) {
            $user_id = $this->data['Category']['user_id'];
        } else {
            $user_id = CakeSession::read('Auth.User.id');
        }

		$count = $this->find('count', array('conditions' => array('Category.name' => $name, 'Category.user_id' => $user_id)));

		if(empty($count))
			return true;
		else 
			return false;
	}

}
