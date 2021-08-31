<?php
App::uses('AppModel', 'Model');
/**
 * Transfer Model
 *
 * @property Payment $Payment
 */
class Payment extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $name = 'Payment';

	public $actsAs = array(
        'Containable'
    );

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	
	
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
    );
}