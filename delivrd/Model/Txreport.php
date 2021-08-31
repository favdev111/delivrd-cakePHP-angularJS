<?php
App::uses('AppModel', 'Model');
/**
 * OrdersCosts Model
 *
 * @property Order $Order
 * @property User $User
 * @property DcopUser $DcopUser
 */
class Txreport extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'txreport';

    public $actsAs = array(
        //'Containable'
    );

    public $recursive = 0;
}