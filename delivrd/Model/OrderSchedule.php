<?php
App::uses('AppModel', 'Model');
/**
 * OrderslinesWave Model
 *
 * @property Wave $Wave
 * @property Ordersline $Ordersline
 */
class OrderSchedule extends AppModel {

	public $useTable = 'orders_schedule';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
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
		'OrdersLine' => array(
			'className' => 'OrdersLine',
			'foreignKey' => 'ordersline_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
