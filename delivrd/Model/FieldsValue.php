<?php
App::uses('AppModel', 'Model');
/**
 * Color Model
 *
 * @property User $User
 * @property Product $Product
 */
class FieldsValue extends AppModel {

	public $useTable = 'custom_values';

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'value';

	public $recursive = 0;

	public $validate = array(
        'value' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Field Name can\'t be empty',
            ),
            'between' => array(
                'rule'    => array('between', 2, 32),
                'message' => 'Field Name should contain between 2-32 chars.'
            ),
            'allowedChars' => array(
                'rule' => array('custom', '|^([a-z0-9\_\-\ \@\#]{2,32})$|i'),
                'message' => 'Only alfanumeric chars, -, _, #, and @ allowed.'
            )
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
		'Field' => array(
			'className' => 'Field',
			'foreignKey' => 'field_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		)
	);

	public function update_options($field_id, $options) {
        foreach($options as $val) {
            $this->id = $val['id'];
            $fieldOptions['FieldsValue'] = $val;
            $this->save($fieldOptions);
        }
    }

    public function add_options($field_id, $options) {
        foreach($options as $val) {
            $fieldOptions['FieldsValue']['field_id'] = $field_id;
			$fieldOptions['FieldsValue']['user_id'] = CakeSession::read('Auth.User.id');
            $fieldOptions['FieldsValue']['value'] = $val['value'];
            $fieldOptions['FieldsValue']['created'] = date('Y-m-d H:i:s');
            $this->create();
            $this->save($fieldOptions);
        }
    }

	public function beforeSave($options = array()) {
        $this->data['FieldsValue']['updted'] = date('Y-m-d H:i:s');
        return true;
    }

}
