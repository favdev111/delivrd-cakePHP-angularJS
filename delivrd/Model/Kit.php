<?php
App::uses('AppModel', 'Model');
/**
 * Kit Model
 *
 * @property Product $Product
 * @property Product $Product
 */
class Kit extends AppModel {

	/**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'kits';

    public $actsAs = array(
        'Containable'
    );

    public $recursive = 0;

    /**
     * Model associations: hasOne
     *
     * @var array
     * @access public
     */
    public $hasOne = array();

    /**
     * Model associations: belongsTo
     *
     * @var array
     * @access public
     */
    public $belongsTo = array(
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProductPart' => array(
            'className' => 'Product',
            'foreignKey' => 'parts_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

    /**
     * Validation rules
     *
     * @var array 
     */
    public $validate = array(
        'product_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Prdduct is required.',
                'required' => true,
            ),
        ),
        'parts_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Prdduct part is required.',
                'required' => true,
            ),
        ),
        'quantity' => array(
            'integer' => array(
                'rule' => '/^[0-9]+$/',
                'message' => 'Quantity must be integer',
                'required' => true,
                'on' => 'create',
            ),
            'positive' => array(
                'rule' => array('comparison', '>', 0),
                'message' => 'Quantity must be greater than zero', 
            ),
        ),
    );

    public function getKits($product_id) {
        $kits = $this->find('all', array(
            //'fields' => ['Kit.*'],
            'conditions' => ['Kit.product_id' => $product_id, 'Kit.active' => 1]
        ));
        return $kits;
    }

    public function getKitsQuantity($product_id) {
        $kits = $this->find('list', array(
            'fields' => ['Kit.parts_id', 'Kit.quantity'],
            'conditions' => ['Kit.product_id' => $product_id, 'Kit.active' => 1]
        ));
        return $kits;
    }

    public function getVirtualQuantity($product_id, $warehouse_id=0) {
        $kits = $this->getKitsQuantity($product_id);
        
        $conditions = ['Inventory.product_id' => array_keys($kits), 'Inventory.deleted' => 0, 'Warehouse.status' => 'active'];
        if($warehouse_id) {
            $conditions['Inventory.warehouse_id'] = $warehouse_id;
        }
        $quantity = $this->Product->Inventory->find('all', array(
            'fields' => ['Inventory.product_id', 'Inventory.warehouse_id', 'SUM(Inventory.quantity) as pquant'],
            'conditions' => $conditions,
            'group' => ['Inventory.product_id']
        ));
        
        $v_quant = 0;
        if(count($kits) == count($quantity)) {
            foreach ($quantity as $quant) {
                if($quant[0]['pquant'] > 0) {
                    if($v_quant == 0) {
                        $v_quant = floor($quant[0]['pquant']/$kits[$quant['Inventory']['product_id']]);
                    } else {
                        $v_quant = min($v_quant, floor($quant[0]['pquant']/$kits[$quant['Inventory']['product_id']]));
                    }
                    if($v_quant <= 0) {
                        $v_quant = 0;
                        break;
                    }
                } else {
                    $v_quant = 0;
                    break;
                }
            }
        }
        return $v_quant;
    }
}
