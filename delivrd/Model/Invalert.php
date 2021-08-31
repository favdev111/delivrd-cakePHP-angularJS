<?php
App::uses('AppModel', 'Model');

/**
 * Invalert Model
 *
 * @property User $User
 * @property Product $Product
 * @property Warehouse $Warehouse
 */
class Invalert extends AppModel {


    public $actsAs = array(
       'Containable',
       'Search.Searchable'
    );

    public $recursive = -1;

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'product_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Product cannot be empty',
            ),
        ),
        'warehouse_id' => array(
			'notBlank' => array(
	          	'rule' => array('notBlank'),
	          	'message' => 'Location cannot be empty',
        	), 
		),
        'safety_stock' => array(
            'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Safety stock must be a positive number'
            ),
        ),
        'reorder_point' => array(
            'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Reorder point must be a positive number'
            ),
        ),
    );

    public $filterArgs = array(
        'product_id' => array(
            'type' => 'value',
            'field' => 'Invalert.product_id'
        ),
        'warehouse_id' => array(
            'type' => 'value',
            'field' => 'Invalert.warehouse_id'
        ),
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
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
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

    public function beforeSave($options = array()) {
        return true;
    }
}