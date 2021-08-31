<?php
App::uses('AppModel', 'Model');
/**
 * Wave Model
 *
 * @property User $User
 */
class Wave extends AppModel {

 public $actsAs = array('Containable');

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'type' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select type',
				'required' => true,
				'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

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
		'Courier' => array(
			'className' => 'Courier',
			'foreignKey' => 'courier_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Packstation' => array(
			'className' => 'Packstation',
			'foreignKey' => 'packstation_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Warehouse' => array(
            'className' => 'Warehouse',
            'foreignKey' => 'location_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Schannel' => array(
            'className' => 'Schannel',
            'foreignKey' => 'schannel_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
	);

	
	public $hasAndBelongsToMany = array(
        'OrdersLine' =>
            array(
                'className' => 'OrdersLine',
                'joinTable' => 'orderslines_waves',
                'foreignKey' => 'wave_id',
                'associationForeignKey' => 'ordersline_id',
                'unique' => true
            ),
        'Country' =>
            array(
                'className' => 'Country',
                'joinTable' => 'waves_countries',
            )
    );
        
        
}
