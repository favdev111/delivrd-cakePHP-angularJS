<?php
App::uses('AppModel', 'Model');
/**
 * Direction Model
 *
 * @property Direction $Direction
 * @property Direction $Direction
 * @property Shipment $Shipment
 */
class Direction extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'direction';

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'direction_id';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Direction' => array(
			'className' => 'Direction',
			'foreignKey' => 'direction_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Direction' => array(
			'className' => 'Direction',
			'foreignKey' => 'direction_id',
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
		'Shipment' => array(
			'className' => 'Shipment',
			'foreignKey' => 'direction_id',
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

}
