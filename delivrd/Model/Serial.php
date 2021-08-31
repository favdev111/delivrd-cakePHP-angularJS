<?php
App::uses('AppModel', 'Model');
/**
 * Serial Model
 *
 * @property Product $Product
 */
class Serial extends AppModel {

public $actsAs = array(
        'Search.Searchable', 'Containable'
    );
	
	public $filterArgs = array(
        'serialnumber' => array(
            'type' => 'like',
            'field' => 'serialnumber'
        ),
        'instock' => array(
            'type' => 'value',
            'field' => 'instock'
        ),
        'warehouse_id' => array(
            'type' => 'value',
            'field' => 'warehouse_id'
        ),
		'search' => array(
            'type' => 'like',
            'field' => 'Serial.serialnumber'
        ),
        'search' => array(
            'type' => 'value',
            'field' => 'Serial.instock'
        ),
    );

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'product_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'required' => true,
				'on' => 'create', 
			),
		),
		'serialnumber' => array(
			'numeric' => array(
				'rule' => array('alphanumeric'),
				'required' => true,
				'on' => 'create', 
			),
			'rule' => array('uniqueSerial', 'serialnumber'),
			'message' => 'This serial already exists'
		),
		'warehouse_id' => array( // check that Warehouse.user_id == Product.user_id
            'validWarehouse' => array(
                'rule' => 'validWarehouse',
                'allowEmpty' => true,
                'message' => 'Please select location from same network as product',
            ),
        )
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Warehouse' => array(
            'className' => 'Warehouse',
            'foreignKey' => 'warehouse_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
	);

	public $hasMany = array(		
		'OrdersLine' => array(
			'className' => 'OrdersLine',
			'foreignKey' => 'serial_id',
			'dependent' => true
		)
	);
	
	public function uniqueSerial($serialnumber = null) {
    	$count = $this->find('count', array('conditions' => array('Serial.serialnumber' => $serialnumber,'Serial.user_id' => CakeSession::read("Auth.User.id"))));
    	return $count == 0;
	}

    public function validWarehouse($warehouse_id = 0) {
        if (isset($this->data['Serial']['product_id'])) {
            $productModel = ClassRegistry::init('Product');
            $product = $productModel->find('first', ['conditions' => ['Product.id' => $this->data['Serial']['product_id']], 'fields'=>['Product.user_id'], 'callbacks' => false]);
            $warehouseModel = ClassRegistry::init('Warehouse');
            $warehouse = $warehouseModel->find('first', ['conditions' => ['Warehouse.id' => $warehouse_id], 'fields'=>['Warehouse.user_id']]);
            if($product['Product']['user_id'] == $warehouse['Warehouse']['user_id']) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}