<?php
App::uses('AppModel', 'Model');
/**
 * Color Model
 *
 * @property User $User
 */
class Document extends AppModel {

    public $useTable = 'documents';

    public $recursive = 0;

    public $actsAs = array('Containable');

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';


    public $validate = array(
        'attachment_name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Name can\'t be empty',
            )
        ),
        'attachment_path' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Path can\'t be empty',
            )
        ),
        'model_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Model can\'t be empty',
            )
        ),
        'model_type' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Model can\'t be empty',
            )
        ),
        
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
        /*'FieldsValue' => array(
            'className' => 'FieldsValue',
            'foreignKey' => 'field_id',
            'dependent' => false
        ),
        'FieldsData' => array(
            'className' => 'FieldsData',
            'foreignKey' => 'field_id',
            'dependent' => false
        )*/
    );
    
    public function beforeSave($options = array()) {
        $this->data['Document']['modified'] = date('Y-m-d H:i:s');
        return true;
    }

}
