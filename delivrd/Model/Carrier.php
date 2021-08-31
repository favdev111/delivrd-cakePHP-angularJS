<?php
App::uses('AppModel', 'Model');
/**
 * Carrier Model
 *
 * @property Carrier $Carrier
 * @property Carrier $Carrier
 * @property Shipment $Shipment
 */
class Carrier extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'carrier_id';

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
		'Carrier' => array(
			'className' => 'Carrier',
			'foreignKey' => 'carrier_id',
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
		'Carrier' => array(
			'className' => 'Carrier',
			'foreignKey' => 'carrier_id',
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
			'foreignKey' => 'carrier_id',
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
