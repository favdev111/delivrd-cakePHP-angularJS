<?php
App::uses('AppModel', 'Model');
/**
 * Color Model
 *
 * @property User $User
 * @property Field $Field
 */
class FieldsData extends AppModel {

	public $useTable = 'custom_data';

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'value';

	public $actsAs = array('Containable');

	public $recursive = 0;


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
		'Field' => array(
			'className' => 'Field',
			'foreignKey' => 'field_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'counterCache' => array(
				'values_count' =>  ['FieldsData.value !=' => '']
			)
		)
	);

	public function saveProduct($product_id, $values) {
		foreach ($values as $key => $value) {
			if($fieldValue = $this->find('first', array('conditions' => array('FieldsData.field_id' => $key, 'FieldsData.object_id' => $product_id, 'FieldsData.object_type' => 1)))) {
				$fieldValue['FieldsData']['value'] = $value;
				$this->save($fieldValue);
			} else {
				$fieldValue['FieldsData']['object_id'] = $product_id;
				$fieldValue['FieldsData']['object_type'] = 1;
				$fieldValue['FieldsData']['field_id'] = $key;
				$fieldValue['FieldsData']['value'] = $value;
				$fieldValue['FieldsData']['user_id'] = CakeSession::read('Auth.User.id');
				$fieldValue['FieldsData']['created'] = date('Y-m-d H:i:s');
				$this->create();
				$this->save($fieldValue);
			}
		}
	}

	public function beforeSave($options = array()) {
        $this->data['FieldsData']['updted'] = date('Y-m-d H:i:s');
        return true;
    }

}
