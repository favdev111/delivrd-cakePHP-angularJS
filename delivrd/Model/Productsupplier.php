<?php
App::uses('AppModel', 'Model');
/**
 * Productsupplier Model
 *
 * @property Order $Order
 */
class Productsupplier extends AppModel {

    
    public $actsAs = array(
        'Search.Searchable','Containable'
    );
    
    public $filterArgs = array(
        'name' => array(
            'type' => 'like',
            'field' => 'name'
        ),
      
    );
    
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
        'Supplier' => array(
            'className' => 'Supplier',
            'foreignKey' => 'supplier_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    
    public $validate = array(
        'product_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Please select product from list',
            ),
        ),
        'supplier_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Please select supplier from list',
            ),
            'checkSupplier' => array(
                'rule' => 'existSupplier',
                'message' => 'Supplier for this product already exist',
            ),
        ),
        'price' => array(
            'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Price must be a positive number'
            ),
        ),
    );
    
    public function existSupplier($check=null) {
        if(!empty($this->data['Productsupplier']['id'])) {
            $first = $this->find('first', array('conditions' => array('Productsupplier.id' => $this->data['Productsupplier']['id']),'recursive' => -1));
            if((!empty($first['Productsupplier']['supplier_id'])) && $check['supplier_id'] === $first['Productsupplier']['supplier_id']) {
                return true;
            } else {
                $count = $this->find('count', array('conditions' => array('Productsupplier.product_id' => $this->data['Productsupplier']['product_id'], 'Productsupplier.supplier_id' => $this->data['Productsupplier']['supplier_id'])));
                if(empty($count)) {
                   return true;
                } else {
                   return false;
                }
            }
        } else {
            if(!empty($this->data['Productsupplier']['product_id'])) {
                $count = $this->find('count', array('conditions' => array('Productsupplier.product_id' => $this->data['Productsupplier']['product_id'], 'Productsupplier.supplier_id' => $this->data['Productsupplier']['supplier_id'])));

                if(empty($count)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true; // We need check it!!!
            }
        }
    }

    public $status = array(
        'yes' => 'Active',
        'no' => 'Inactive'
    );

}
