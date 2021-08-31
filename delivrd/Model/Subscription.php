<?php
App::uses('AppModel', 'Model');
/**
 * OrdersCosts Model
 *
 * @property Order $Order
 * @property User $User
 * @property DcopUser $DcopUser
 */
class Subscription extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'subscriptions';

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
        'User' => array( //Who owner of order line
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
}