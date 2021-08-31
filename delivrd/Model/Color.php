<?php
App::uses('AppModel', 'Model');
/**
 * Color Model
 *
 * @property User $User
 * @property Product $Product
 */
class Color extends AppModel {

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
			'foreignKey' => 'color_id',
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
				'message' => 'Color name cannot be empty',
			),
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                'message' => 'Some characters are not valid.',
            ),
            'between' => array(
	            'rule'    => array('between', 3, 30),
	            'message' => 'Color name should contain between 3-30 chars.'
            ),
           'unique' => array( 
				'rule' => array('uniqueColor', 'name'),
				'message' => 'Name already in use. Please select a different name.',
				'on' => 'create'
			),
		),
		'htmlcode' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'HTML code name cannot be empty',
			),
			'allowdchars' => array(
				'rule'      => '^#(?:[0-9a-fA-F]{3}){1,2}$',
                'message' => 'Some characters are not valid.',
            ),
			'rule' => array('uniqueHCode', 'htmlcode'),
			'message' => 'HTML Code already in use. Please select a different code.',
		),
		'description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Description cannot be empty',				
			),
            'between' => array(
                'rule'    => array('between', 3, 30),
                'message' => 'Color description should contain between 3-30 chars.'
            ),
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Some characters are not valid.',
            ),
        ),
	);
	
	public function uniqueColor($name = null) {
		$count = $this->find('count', array('conditions' => array('Color.name' => $name,'Color.user_id' => CakeSession::read("Auth.User.id"))));
		return $count == 0;
	}

	public function uniqueHCode($htmlcode = null) {
		if(!empty($this->data['Color']['id'])) {
			$first = $this->find('first', array('conditions' => array('Color.id' => $this->data['Color']['id']), 'recursive' => -1));
			if((!empty($first['Color']['htmlcode'])) && $htmlcode['htmlcode'] === $first['Color']['htmlcode']) {
				return true;
			} else {
				$count = $this->find('count', array('conditions' => array('Color.htmlcode' => $htmlcode['htmlcode'], 'Color.user_id' => CakeSession::read("Auth.User.id"))));
				if(empty($count)) {
			       return true;
			    } else {
			       return false;
			    }
			}
		} else {
			$count = $this->find('count', array('conditions' => array('Color.htmlcode' => $htmlcode['htmlcode'], 'Color.user_id' => CakeSession::read("Auth.User.id"))));

		    if(empty($count)) {
		       return true;
		    } else {
		       return false;
		    }
		} 
	}

}
