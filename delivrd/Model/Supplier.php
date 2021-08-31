<?php
App::uses('AppModel', 'Model');
/**
 * Supplier Model
 *
 * @property Supplysource $Supplysource
 */
class Supplier extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

 public $actsAs = array(
        'Search.Searchable','Containable'
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
			),
			'allowdchars' => array(
				'rule' => 'ValidTextFields',
                // 'required' => false,
                // 'allowEmpty' => true,
                'message' => 'Some characters are not valid.',
                ),
            'unique' => array(
	            'rule' => array('uniqueName', 'name'),
				'message' => 'Supplier name already in use. Please select a different name.',
			),
			
		),
		'url' => array(
			'url' => array(
				'rule' =>'url',
				'message' => 'Please enter a valid URL. For example, www.example.com',
				'allowEmpty' => true,
				//'required' => true,
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
		'Supplysource' => array(
			'className' => 'Supplysource',
			'foreignKey' => 'supplysource_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	public $hasOne = array(
 		'Address' => array('dependent' => true), 
    );

	public function uniqueName($name = null) { 
		$condition = array('Supplier.name' => $name,'Supplier.user_id' => CakeSession::read("Auth.User.id"));
		if(!empty($this->data['Supplier']['id'])) {
			$condition[] = array('Supplier.id != '=> $this->data['Supplier']['id']);
		}
		$count = $this->find('count', array('conditions' => $condition));
	        return $count == 0;

	}
}
