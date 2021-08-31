<?php
App::uses('AppModel', 'Model');
/**
 * Activity Model
 *
 * @property User $User
 */
class Activity extends AppModel {

    public $actsAs = array('Containable');

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'activities';

    public $recursive = 0;
    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'activity';


    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        
    );

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

    public function add_activity($sku, $user) {
        $exists = $this->find('first', [
            'conditions' => ['Activity.sku' => $sku, 'Activity.user_id' => $user]
        ]);
        if($exists) {
            $exists['Activity']['activity'] = $exists['Activity']['activity'] + 1;
            $this->save($exists);
        } else {
            $this->save([
                'user_id' => $user,
                'sku' => $sku,
                'activity' => 1
            ]);
        }
    }
}
