<?php
App::uses('AppModel', 'Model');
/**
 * Schannel Model
 *
 * @property Order $Order
 */
class Courier extends AppModel {

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
            'unique' => array(
                'rule' => array('uniqueName', 'name'),
                'message' => 'Courier name already in use.',
                'on' => 'create'
            ),
            'between' => array(
                'rule'    => array('between', 3, 255),
                'message' => 'Courier name should contain between 3-255 chars.'
            ),
        ),
        'allowdchars' => array(
            'rule' => 'ValidTextFields',
            'required' => false,
            'allowEmpty' => true,
            'message' => 'Some characters are not valid.',
            // 'on' => 'create', // Limit validation to 'create' or 'update' operations
        ),
    );


    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Wave' => array(
            'className' => 'Wave',
            'foreignKey' => 'courier_id',
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
        'Shipment' => array(
            'className' => 'Shipment',
            'foreignKey' => 'courier_id',
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

        return preg_match('|^[0-9a-zA-Z\s_\-\\\@!#&"\'+]*$|', $value);
    }
    
    public function uniqueName($name = null) {
        $count = $this->find('count', array('conditions' => array('Courier.name' => $name,'Courier.user_id' => CakeSession::read("Auth.User.id"))));
        return $count == 0;
    }
}
