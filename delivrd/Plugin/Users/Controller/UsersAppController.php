<?php
/**
 * Copyright 2010 - 2013, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2013, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Users App Controller
 *
 * @package users
 * @subpackage users.controllers
 */

App::uses('AppController', 'Controller');

class UsersAppController extends AppController {
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login','add','request_new_password','reset_password');
    }
}
