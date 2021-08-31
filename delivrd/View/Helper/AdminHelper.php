<?php
App::uses('CakeTime', 'Utility');
App::uses('Helper', 'View');

class AdminHelper extends Helper {

    var $helpers = array('Session', 'Html');

    public function localTime($format = NULL,$created = NULL) {
    	if(!empty($this->Session->read('timezone'))) {
    		$render = CakeTime::format($created, $format, 'N/A', $this->Session->read('timezone'));
    	} else {
    		$render = CakeTime::format($created, $format);
    	}
       
        return $render;
    }
	
	public function localTimeN($created = NULL, $format = '%Y-%m-%d %H:%M:%S') {
    	if(!empty($this->Session->read('timezone'))) {
    		$render = CakeTime::format($created, $format, 'N/A', $this->Session->read('timezone'));
    	} else {
    		$render = CakeTime::format($created, $format);
    	}
       
        return $render;
    }

}
