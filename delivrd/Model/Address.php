<?php
App::uses('AppModel', 'Model');
/**
 * Address
 *
 * PHP version 5
 *
 */
class Address extends AppModel {

    /**
     * Model associations: hasOne
     *
     * @var array
     * @access public
     */
    public $hasMany = array(
      
    );

    /**
     * Model associations: belongsTo
     *
     * @var array
     * @access public
     */
    public $belongsTo = array(
      'Country' => array(
        'className' => 'Country',
        'foreignKey' => 'country_id',
        'conditions' => '',
        'fields' => '',
        'order' => ''
      ),
      'State' => array(
        'className' => 'State',
        'foreignKey' => 'state_id',
        'conditions' => '',
        'fields' => '',
        'order' => ''
      )
    );

    /**
     * Validation rules
     *
     * @var array 
     */
    public $validate = array(
      'street' => array(
        'allowdchars' => array(
          'rule' => 'ValidTextFields',
                  'required' => false,
                  'allowEmpty' => true,
                  'message' => 'Some characters are not valid.',
          ), 
      ),
      'city' => array(
        'allowdchars' => array(
        'rule' => 'ValidTextFields',
            'required' => false,
            'allowEmpty' => true,
            'message' => 'Some characters are not valid.',
        ), 
      ),
      'zip' => array(
        'alphaNumeric' => array(
            'rule' => array('custom', '/^[a-z0-9- ]*$/i'),
            'allowEmpty' => true,
            'message' => 'Letters,numbers and spaces only',
        ), 
      ),
      'phone' => array(
          'mobile' => array(
             'rule' => array('custom', '/^[0-9 +-]+$/'),
             'allowEmpty' => true,
             'message' => 'Please enter a valid phone number.',
         )           
      ),
      'country_id' => array(
        'notBlank' => array(
          'rule' => array('notBlank'),
          'required' => false,
          'allowEmpty' => true,
          'message' => 'Country cannot be empty',
        ), 
      ),
    );

    public function ValidTextFields($check) {
        $value = array_values($check);
        $value = $value[0];
        return preg_match('|^[0-9a-zA-Z\s_\+\-\/\\@!#&"\'\"\,\(\)\.+]*$|', $value);
    }
  
}
