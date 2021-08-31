<?php
App::uses('AppModel', 'Model');
/**
 * Network Model
 *
 * @property User $CreateByUser
 * @property User $AdminUser
 * 
 */
class Network extends AppModel {

    public $actsAs = array('Containable');

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'networks';

    public $recursive = 0;

    // In first version we use specific primary key for speed
    // public $primaryKey = 'user_id';

    public static $roles = array(1 => '3pl supplier customer', 2 => 'Internal customers', 3 => 'Distributors', 4 => 'Suppliers (vendors)');

    // Validation
    public $validate = array(
        'name' => array(
            'alphaNumeric' => array(
                'rule' => array('custom', '/^[a-z0-9 \_\-]*$/i'),//'alphaNumeric',
                'required' => true,
                'message' => 'Name cannot be empty, letters and numbers only',
            ),
            'between' => array(
                'rule' => array('lengthBetween', 5, 15),
                'message' => 'Between 5 to 15 characters'
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
        'NetworksUser' => array(
            'className' => 'NetworksUser',
            'foreignKey' => 'network_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
        ),
        'NetworksInvite' => array(
            'className' => 'NetworksInvite',
            'foreignKey' => 'network_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
        ),
    );

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'CreatedByUser' => array(
            'className' => 'User',
            'foreignKey' => 'created_by_user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'AdminUser' => array(
            'className' => 'User',
            'foreignKey' => 'admin_user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        
    );

    public function getRoles() {
        return self::$roles;
    }

    public function findLocations($network_id) {
        $network = $this->find('all', [
                'conditions' => ['Network.id' => $network_id],
                'contain' => ['CreatedByUser.Warehouse'],
            ]
        );
        return $network;
    }
}