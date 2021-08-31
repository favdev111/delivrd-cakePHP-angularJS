<?php
App::uses('Component', 'Controller');

class AccountComponent extends Component {

    public $components = array('Auth', 'Session');

    public $types = [1 => 'S.O.', 2 => 'P.O.'];

    public function initialize(Controller $controller) {

        /*$this->Network          = ClassRegistry::init('Network');
        $this->Warehouse        = ClassRegistry::init('Warehouse');
        $this->NetworksUser     = ClassRegistry::init('NetworksUser');
        $this->NetworksInvite   = ClassRegistry::init('NetworksInvite');
        $this->NetworksAccess   = ClassRegistry::init('NetworksAccess');
        
        $this->_access = $this->getAccessList();*/
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

    /**
     * We need modify it later to update all settings
     *
     *
     */
    public function reNewUserSettings($data) {
        $options = array(
            'showtours','msystem_id','paid','locationsactive','inventoryauto','inventoryalert','inventoryremarks','timezone_id','copypdtprice','zeroquantity','packinslip_desc','default_country_id','sales_title','list_limit','kit_component_issue'
        );
        foreach ($options as $option) {
            if(isset($data['User']['option'])) {
                if($option == 'sales_title') {
                    $this->Session->write($option, strip_tags($data['User'][$option] , '<script>'));
                } else {
                    $this->Session->write($option, htmlspecialchars($data['User'][$option]));
                }
            }
        }
    }

    public function orderRedirectUrl($order) {
        $type = $this->types[$order['Order']['ordertype_id']];
        $redirect = [];
        if($order['Order']['ordertype_id'] == 1) {
            $redirect['controller'] = 'salesorders';
        } else {
            $redirect['controller'] = 'replorders';
        }
        if($this->request->query('f') == 'd') {
            $redirect['action'] = 'details';
            $redirect[] = $$order['Order']['id'];
        } else {
            $redirect['action'] = 'index';
        }
        return $redirect;
    }

    function getCountryByIp($ip_adress) {
        $country = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip_adress));
        
        if($country && !empty($country->geoplugin_countryCode)) {
            return $country->geoplugin_countryCode;
        } else {
            return false;
        }
    }
}