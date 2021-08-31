<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
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
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {

	public function username($authUser) {
		if(!empty($authUser['firstname'])) {
			return $authUser['firstname'] . ( !empty($authUser['lastname']) ? ' '. $authUser['lastname'] : '');
		} elseif(!empty($authUser['username'])) {
			return $authUser['username'];
		} else {
			return $authUser['email'];
		}
	}

	public function ang($str) {
	    $str = str_replace('%7B%7B', '{{', $str);
	    $str = str_replace('%7D%7D', '}}', $str);
	    $str = str_replace('%5B', '[', $str);
    	$str = str_replace('%5D', ']', $str);
	    return $str;
	}

	/**
     * @param expiredDate date('Y-m-d')
     *
     */
    public function getRemainingDays($expiredDate) {
        $today = date('Y-m-d');
        $today = new DateTime($today);
        
        $expiredDate = date('Y-m-d', strtotime($expiredDate));
        $expiredDate = new DateTime($expiredDate);
        
        $remaining_days = $today->diff($expiredDate)->format('%R%a');
        $remaining_days  = ($remaining_days > 0) ? $remaining_days : 0;

        return $remaining_days;
    }
}
