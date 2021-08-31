<?php
App::uses('AppModel', 'Model');
/**
 * State Model
 *
 * @property Order $Order
 */
class State extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
     * Model associations: belongsTo
     *
     * @var array
     * @access public
     */
    public $belongsTo = array(
        'Country' => array(
            'className' => 'Country',
        ),
    );

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Order' => array(
			'className' => 'Order',
			'foreignKey' => 'state_id',
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
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'country_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public function statesList($country = null) {
        $conditions = array();
        if ($country) {
            $conditions = array('State.country_id' => $country);
        }
        return $this->find('list', array('conditions' => $conditions,'fields' => array('id','name')));
    }

}
