<?php
App::uses('AppModel', 'Model');
/**
 * UserShortcutLink
 *
 * PHP version 5
 *
 */
class UserShortcutLink extends AppModel {

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
      'User' => array(
        'className' => 'User',
        'foreignKey' => 'user_id',
        'conditions' => '',
        'fields' => '',
        'order' => ''
      )
    );

    public function getShortList($user_id, $limit=5) {
        $shortcutList =  $this->find('all', array(
            'conditions' => array('user_id' => $user_id),
            'recursive' => -1,
            'order' => ['UserShortcutLink.clicked' => 'DESC'],
            'limit' => $limit
        ));
        return $shortcutList;
    }
  
}
