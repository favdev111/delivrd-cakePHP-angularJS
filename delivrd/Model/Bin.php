<?php
App::uses('AppModel', 'Model');
App::uses('Security', 'Utility');
/**
 * Bin
 *
 * PHP version 5
 *
 */
class Bin extends AppModel {

    /**
     * Model associations: hasOne
     *
     * @var array
     * @access public
     */
    public $hasOne = array(
    );

    /**
     * Model associations: belongsTo
     *
     * @var array
     * @access public
     */
    public $belongsTo = array(
        'Warehouse' => array(
            'className' => 'Warehouse',
            'foreignKey' => 'location_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

    public $hasAndBelongsToMany = array(
        'OrdersLine' =>
            array(
                'className' => 'OrdersLine',
                'joinTable' => 'orderslines_waves',
                'foreignKey' => 'wave_id',
                'associationForeignKey' => 'ordersline_id',
                'unique' => true
            )
    );

    /**
     * Validation rules
     *
     * @var array 
     */
    public $validate = array(
        'title' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Title field is not blank',
            ),
            'unique' => array(
                'rule' => array('uniqueTitle', 'title'),
                'required' => true,
                'message' => 'Bin Title already in use. Please choose a different title.'
            ),
            'regex0' => array(
                'rule' => '/^[A-Za-z0-9-]+$/',
                'message' => 'Please enter alphanumeric and "-" special character',
            ),
        ),
        'sort_sequence' => array(
          // 'notBlank' => array(
          //       'rule' => 'notBlank',
          //       'required' => true,
          //       'message' => 'Sort Sequence field is not blank',
          //   ),
          'rule0' => array(
                'rule' => 'naturalNumber',
                'message' => 'Please enter a valid numeric value',
                'allowEmpty' => true
            ),
          'between' => array(
            'rule' => 'checkOver',
            'message' => 'Sort sequence value must between 0 to 99999999'
          )
        ),
        'location_id' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Location field is not blank',
            ),
        ),
        
    );

    public function uniqueTitle($title = null) {
//        CakeLog::write('debug', print_r($title['title'], true));
        $count = $this->find('count', array('conditions' => array('Bin.title' => $title['title'],'Bin.user_id' => CakeSession::read("Auth.User.id"))));
//        CakeLog::write('debug', 'Counted bin with same title - '.$count);
        if($count > 0){
            return "Bin title ".$title['title']." already exists.";
//            CakeLog::write('debug', 'Bin with the title exist');
        } else {
//            CakeLog::write('debug', 'Bin with the title not exist');
            return true;
        }

    }


    public function checkOver($check) {
     
      if (($check['sort_sequence'] >= 0) && ($check['sort_sequence'] <= 99999999)){
        return true;
      } 
      return false;
    }
    public $status = array(
        1 => 'Active',
        0 => 'Inactive'
    );
  
}
