<?php
App::uses('AppModel', 'Model');
/**
 * BatchPick Model
 *
 * @property Order $Order
 * @property Product $Product
 */
class BatchPick extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
 
 	public $actsAs = array(
      'Containable'
    );
	
	public $validate = array(

	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'productid',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	
}
