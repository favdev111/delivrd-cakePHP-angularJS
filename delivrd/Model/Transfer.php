<?php
App::uses('AppModel', 'Model');
/**
 * Transfer Model
 *
 * @property Transfer $Transfer
 */
class Transfer extends AppModel {

    public $actsAs = array(
        'Containable'
    );
    
    public static $types     = ['products' => 1, 'orders' => 2,'inventories' => 3];
    public static $direction = ['import' => 1, 'export' => 2];
    public static $source    = ['csv' => 1, 'shopify' => 2, 'woocommerce' => 3];
    public static $status    = ['started' => 1, 'failed' => 2, 'success' => 3];
    

    /**
     * belongsTo associations
     *
     * @var array
     
    public $belongsTo = array(
        'Integration' => array(
            'className' => 'Integration',
            'foreignKey' => 'source_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );*/
}