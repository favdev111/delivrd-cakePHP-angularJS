<?php
App::uses('Component', 'Controller');
class EventRegisterComponent extends Component {
    
    var $Event;
    
    public function addEvent($objecttypeid, $statusid, $userid, $objectid) {
        $this->Event = ClassRegistry::init('Event');
        $this->Event->create();
        $this->Event->set('user_id',$userid);
        $this->Event->set('object_type_id', $objecttypeid);
        $this->Event->set('object_id', $objectid);
        $this->Event->set('status_id', $statusid);
        //echo "from shipment we got $objecttypeid, $statusid,$userid,$objectid"; 
        if ($this->Event->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function getObjectEvent($objecttypeid, $objectid, $user_id) {
        $this->Event = ClassRegistry::init('Event');
        $events = $this->Event->find('all', array('conditions' => array('Event.object_type_id' => $objecttypeid,'Event.object_id' => $objectid,'Event.user_id' => $user_id)));
        return $events;
    }

    public function getObjectEventShort($objecttypeid, $objectid, $user_id) {
        $this->Event = ClassRegistry::init('Event');
        $events = $this->Event->find('all', array(
                'conditions' => array('Event.object_type_id' => $objecttypeid,'Event.object_id' => $objectid,'Event.user_id' => $user_id),
                'contain' => array('Status')
            )
        );
        return $events;
    }
}
