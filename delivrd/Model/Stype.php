<?php
App::uses('AppModel', 'Model');
/**
 * Stype Model
 *
 * @property Supplysource $Supplysource
 */
class Stype extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Supplysource' => array(
			'className' => 'Supplysource',
			'foreignKey' => 'stype_id',
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
