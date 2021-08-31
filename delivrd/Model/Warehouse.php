<?php
App::uses('AppModel', 'Model');
/**
 * Country Model
 *
 * @property Order $Order
 */
class Warehouse extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    public $actsAs = array(
        'Containable'
    );

    public $validate = array(
    
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Name cannot be empty',
            ),
            'allowdchars' => array(
                'rule' => 'ValidTextFields',
                'message' => 'Some characters are not valid.',
            ),
            'unique' => array(
                'rule' => array('uniqueName', ['name', 'user_id']),
                'message' => 'Invenotry location Name already in use. Please select a different name.',
                //'on' => 'create'
            ),
                    
            'between' => array(
                'rule'    => array('between', 3, 30),
                'message' => 'Loaction name should contain between 3-30 chars.'
            ),
        ),
     
        'description' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Description cannot be empty',
            ),
            'between' => array(
                'rule'    => array('between', 3, 50),
                'message' => 'Loaction description should contain between 3-50 chars.'
            ),
            'allowdchars' => array(
                'rule' => 'ValidTextFields',
                'message' => 'Some characters are not valid.',
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
        'Inventory' => array(
            'className' => 'Inventory',
            'foreignKey' => 'warehouse_id',
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
        'OrdersLine' => array(
            'className' => 'OrdersLine',
            'foreignKey' => 'warehouse_id',
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
    );

    /**
     * hasOne associations
     *
     * @var array
     */
    public $hasOne = array(
        'Address' => array('dependent' => true), 
    );
    
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
        ),
    );

    public function uniqueName($name = null) {
        if(isset($this->data['Warehouse']['id'])) {
            $conditions = array(
                'Warehouse.name' => $name,
                'Warehouse.user_id' => CakeSession::read("Auth.User.id"),
                'Warehouse.id !=' => $this->data['Warehouse']['id']
            );
        } else {
            // When create new
            $conditions = array(
                'Warehouse.name' => $name,
                'Warehouse.user_id' => CakeSession::read("Auth.User.id"),
            );
        }
        $count = $this->find('count', array('conditions' => $conditions));
        return $count == 0;
    }
}
