<?php
App::uses('AppModel', 'Model');
/**
 * Invitation Model
 *
 * 
 */
class NetworksInvite extends AppModel {

    public $actsAs = array('Containable');

    public $recursive = 0;

    public static $status = array('new' => 1, 'accepted' => 2, 'declined' => 3, 're-new' => 4);
    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'networks_invites';

    public $validate = array(
        'email' => array(
            'required' => array(
                'rule' => array('email'),
                'message' => 'Please enter valid email'
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
            'order' => '',
            'counterCache' => [
                'invite_count' => ['NetworksInvite.status' => [1, 4]]
            ]
        ),
    );

    public function getStatuses() {
        return self::$status;
    }
}