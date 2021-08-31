<?php
App::uses('AppModel', 'Model');
/**
 * OrdersCosts Model
 *
 * @property Product $Product
 * @property Schannel $Schannel
 */
class ProductsPrices extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'products_prices';

    public $actsAs = array(
        'Containable'
    );

    public $recursive = 0;

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'product_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Prdduct is required.',
                'required' => true,
            ),
        ),
        'schannel_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Channel is required.',
                'required' => true,
            ),
        ),
        'value' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Amount must be numeric',
                'required' => true,
                'on' => 'create',
            ),
            'positive' => array(
                'rule' => array('comparison', '>', 0),
                'message' => 'Amount must be greater than zero', 
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
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
        ),
        'Schannel' => array(
            'className' => 'Schannel',
            'foreignKey' => 'schannel_id',
        ),
    );
    
    public function beforeSave($options = array()) {
        return true;
    }

    public function afterSave($created, $options = array()) {
        return true;
    }
    
}