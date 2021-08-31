<?php
App::uses('AppModel', 'Model');
/**
 * Zone Model
 *
 * @property User $User
 */
class Zone extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'zone_name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'timezone_id',
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
