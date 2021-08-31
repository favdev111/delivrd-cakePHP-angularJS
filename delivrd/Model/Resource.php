<?php
App::uses('AppModel', 'Model');
/**
 * Resource Model
 *
 * 
 */
class Resource extends AppModel {

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

    public $hasMany = array(
        'Wave'
    );
    
    public $validate = array(
	
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Name cannot be empty',
				'required' => true,
			),
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Some characters are not valid.',
                ),
           'createName' => array(
	            'rule' => 'isValidName',
				'message' => 'Resource name already in use. Please select a different name.',
			),
			
		)	
	);

    
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	// public $belongsTo = array(
	// 	'Supplysource' => array(
	// 		'className' => 'Supplysource',
	// 		'foreignKey' => 'supplysource_id',
	// 		'conditions' => '',
	// 		'fields' => '',
	// 		'order' => ''
	// 	)
	// );
	public function uniqueName($name = null) {

		$count = $this->find('count', array('conditions' => array('Resource.name' => $name)));
	        return $count == 0;

	}

	public function isValidName($check=null){
		if(!empty($this->data['Resource']['id'])) {
			$first = $this->find('first', array('conditions' => array('Resource.id' => $this->data['Resource']['id'])));

			if((!empty($first['Resource']['name'])) && $check['name'] === $first['Resource']['name']) {
				return true;
			} else {
				$count = $this->find('count', array('conditions' => array('Resource.name' => $check['name'], 'Resource.user_id' => CakeSession::read("Auth.User.id"))));
				if(empty($count)) {
			       return true;
			    } else {
			       return false;
			    }
			}
		} else {
			$count = $this->find('count', array('conditions' => array('Resource.name' => $check['name'], 'Resource.user_id' => CakeSession::read("Auth.User.id"))));

		    if(empty($count)) {
		       return true;
		    } else {
		       return false;
		    }
		}
		
	}

}
