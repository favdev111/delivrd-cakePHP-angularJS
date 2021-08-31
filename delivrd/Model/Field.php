<?php
App::uses('AppModel', 'Model');
/**
 * Color Model
 *
 * @property User $User
 */
class Field extends AppModel {

    public $useTable = 'custom_fields';

    public $recursive = 0;

    public $actsAs = array('Containable');

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';


    public $validate = array(
        'name' => array(
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
        ),
        'description' => array(
            
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
        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'FieldsValue' => array(
            'className' => 'FieldsValue',
            'foreignKey' => 'field_id',
            'dependent' => false
        ),
        'FieldsData' => array(
            'className' => 'FieldsData',
            'foreignKey' => 'field_id',
            'dependent' => false
        )
    );
    
    public function beforeSave($options = array()) {
        $this->data['Field']['updted'] = date('Y-m-d H:i:s');
        return true;
    }

}
