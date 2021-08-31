<?php
App::uses('AppModel', 'Model');
/**
 * OrderslinesWave Model
 *
 * @property Wave $Wave
 * @property Ordersline $Ordersline
 */
class OrderslinesWave extends AppModel {

	public $useTable = 'orderslines_waves';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'wave_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'ordersline_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Wave' => array(
			'className' => 'Wave',
			'foreignKey' => 'wave_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'OrdersLine' => array(
			'className' => 'OrdersLine',
			'foreignKey' => 'ordersline_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
