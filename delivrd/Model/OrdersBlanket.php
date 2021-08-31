<?php
App::uses('AppModel', 'Model');
/**
 * OrdersCosts Model
 *
 * @property Order $Order
 * @property User $User
 * @property DcopUser $DcopUser
 */
class OrdersBlanket extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'orders_blanket';

    public $actsAs = array(
        'Containable'
    );

    public $recursive = 0;

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'order_id'
        ),
        'Shipment' => array(
            'className' => 'Shipment',
            'foreignKey' => 'shipment_id',
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
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'dependent' => true
        ),
        'Serial' => array(
            'className' => 'Serial',
            'foreignKey' => 'serial_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'User' => array( //Who owner of order line
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'DcopUser' => array( //Who create order line (network user for example)
            'className' => 'DcopUser',
            'foreignKey' => 'dcop_user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

}