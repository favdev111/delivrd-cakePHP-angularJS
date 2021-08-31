<?php
App::uses('AppModel', 'Model');
/**
 * NetworksAccess Model
 *
 * 
 */
class NetworksAccess extends AppModel {

    public $actsAs = array('Containable');

    public $recursive = 0;

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'networks_access';

    public $validate = array(
        'access' => array(
            'required' => array(
                'required' => true,
                'on' => 'create',
                'rule' => array('lengthBetween', 1, 2),
                'message' => 'Please select type of access'
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
        
    );

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Network' => array(
            'className' => 'Network',
            'foreignKey' => 'network_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Warehouse' => array(
            'className' => 'Warehouse',
            'foreignKey' => 'warehouse_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

}