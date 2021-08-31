<?php
App::uses('AppModel', 'Model');
/**
 * NetworksUser Model
 *
 * @property Network $Network
 * @property User $User
 * 
 */
class NetworksUser extends AppModel {

    public $actsAs = array('Containable');
    
    public static $status = array('active' => 1, 'stopped' => 2, 'declined' => 3);

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'networks_users';

    public $recursive = -1;

    // Validation
    public $validate = array(
        'network_id' => array(
            
        ),
        'user_id' => array(
            
        ),
        'role' => array(
            
        ),
        'status' => array(
            
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
                'users_count' => ['NetworksUser.status' => 1]
            ]
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        
    );

    public function getStatuses() {
        return self::$status;
    }
}