<?php
App::uses('AppModel', 'Model');
/**
 * OrdersCosts Model
 *
 * @property Order $Order
 * @property User $User
 * @property DcopUser $DcopUser
 */
class OrdersCosts extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'orders_costs';

    public $actsAs = array(
        'Containable'
    );

    public $recursive = 0;

    public static $_types = ['surchage' => 'Surcharge', 'discount' => 'Discount', 'shipping' => 'Shipping Costs', 'other_costs' => 'Other Costs'];
    public static $_uoms = ['percentage' => 'Percentage', 'amount' => 'Amount' ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'type' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Type of Costs/Discount is required.',
                'required' => true,
            ),
        ),
        'uom' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Unit of Measure is required.',
                'required' => true,
            ),
        ),
        'amount' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Amount must be numeric',
                'required' => true,
                'on' => 'create',
            ),
            'positive' => array(
                'rule' => array('comparison', '>', 0),
                'message' => 'Amount must be greater than zero', 
            ),
            'validAmount' => array(
                'rule' => 'ValidAmount',
                'message' => 'Amount must be less then order total', 
            ),
            'validDiscount' => array(
                'rule' => 'ValidDiscount',
                'message' => 'Discount can\'t be more then 100%'
            ),
        ),

    );

    public function ValidAmount($amount) {
        if($this->data['OrdersCosts']['uom'] == 'amount' && $this->data['OrdersCosts']['type'] == 'discount') { //need to check that amout less then 
            $ordersLine = ClassRegistry::init('OrdersLine');
            $sub_total = $ordersLine->find('first', array(
                'conditions' => array('OrdersLine.order_id' => $this->data['OrdersCosts']['order_id']),
                'fields' => array('SUM(OrdersLine.total_line) as total'),
                'contain' => false
            ));
            if(isset($sub_total[0]['total']) && $this->data['OrdersCosts']['amount'] > $sub_total[0]['total']) {
                return false;
            }
        }
        return true;
    }

    public function ValidDiscount($amount) {
        if($this->data['OrdersCosts']['uom'] == 'percentage') {
            if($this->data['OrdersCosts']['amount'] > 100) {
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

    public function getTypes() {
        return self::$_types;
    }

    public function getUoms() {
        return self::$_uoms;
    }

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'order_id',
        ),
        'User' => array( //Who owner of order line
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'DcopUser' => array( //Who create order costs (network user for example)
            'className' => 'DcopUser',
            'foreignKey' => 'dcop_user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
    
    public function beforeSave($options = array()) {
        $this->data['OrdersCosts']['dcop_user_id'] = CakeSession::read("Auth.User.id");
        return true;
    }

    public function afterSave($created, $options = array()) {
        // Update modified order date
        $order_id = $this->find('first', ['conditions' => array('OrdersCosts.id' => $this->id), 'fields' => array('OrdersCosts.order_id')]);
        $orderModel = ClassRegistry::init('Order');
        $order = $orderModel->find('first', ['conditions' => array('Order.id' => $order_id['OrdersCosts']['order_id']), 'fields' => array('Order.*'), 'contain'=>array()]);
        if($order) {
            $order['Order']['modified'] = date('Y-m-d H:i:s');
            $orderModel->save($order, false);
        }
        return true;
    }
    
}