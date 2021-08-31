<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	
public function exists($id = null) {
    if ($this->Behaviors->attached('SoftDelete')) {
        return $this->existsAndNotDeleted($id);
    } else {
        return parent::exists($id);
    }
}

public function delete($id = null, $cascade = true) {
    $result = parent::delete($id, $cascade);
    if ($result === false && $this->Behaviors->enabled('SoftDelete')) {
       return (bool)$this->field('deleted', array('deleted' => 1));
    }
    return $result;
}

function checkUnique($data, $fields) {
    if (!is_array($fields)) {
            $fields = array($fields);
        }
        foreach($fields as $key) {
            $tmp[$key] = $this->data[$this->name][$key];
        }
    if (isset($this->data[$this->name][$this->primaryKey]) && $this->data[$this->name][$this->primaryKey] > 0) {
            $tmp[$this->primaryKey." !="] = $this->data[$this->name][$this->primaryKey];
        }
    //return false;
        return $this->isUnique($tmp, false); 
    }
    
    public function ValidTextFields($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
        $value = array_values($check);
        $value = $value[0];

        return preg_match('/^[\pL\pN\pZ\p{Pc}\p{Pd}\p{Po}]++$/uD', $value);
    }
	

}
