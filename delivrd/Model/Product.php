<?php
App::uses('AppModel', 'Model');
App::uses('Security', 'Utility'); 

/**
 * Product Model
 *
 * @property User $User
 * @property Group $Group
 */
class Product extends AppModel {


    public $actsAs = array(
        'Search.Searchable','Utils.SoftDelete','Containable'
    );

    public $inserted_ids = array();
    
    public $filterArgs = array(
        'name' => array(
            'type' => 'like',
            'field' => 'name'
        ),
        'sku' => array(
            'type' => 'like',
            'field' => 'sku'
        ),
        'category_id' => array(
            'type' => 'value',
            'field' => 'category_id'
        ),
        
        'status_id' => array(
            'type' => 'value',
            'field' => 'status_id'
        ),
            'publish' => array(
            'type' => 'value',
            'field' => 'publish'
        ),
        'search' => array(
            'type' => 'like',
            'field' => 'Product.sku'
        ),
        'search' => array(
            'type' => 'like',
            'field' => 'Product.name'
        ),
        'search' => array(
            'type' => 'value',
            'field' => 'Product.group_id'
        ),
        'search' => array(
            'type' => 'value',
            'field' => 'Product.status_id'
        ),        
    );

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'sku' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'SKU cannot be empty',
            ),
             'allowdchars' => array(
                'rule' => 'ValidSKU',
                'required' => true,
                'message' => 'Valid SKU characters are letters and numbers, dash and underscore.',
            ),
            'unique' => array(
                'rule' => array('uniqueSKU', 'sku'),
                //'message' => 'SKU already exists.'
                //'rule' => array('isUnique',array('user_id'), false)
            ),
        ),
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Product name cannot be empty',
            ),
            'between' => array(
                'rule'    => array('between', 3, 1024),
                'message' => 'Product name should contain between 3-1024 chars.'
            ),
            'unique' => array(
                'rule' => array('uniqueName', 'name'),
                'message' => 'Product Name already in use. Please select a different name.',
                'on' => 'create'
            )
        ),
        'group_id' => array(
        ),
        'barcode' => array(
            'numeric' => array(
                'rule'     => 'alphanumeric',
                'required' => false,
                'allowEmpty' => true,
              
                'message'  => 'Barcode should contain numbers only'
            ),
            'between' => array(
                'rule'    => array('between', 3, 30),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Barcode should contain between 12-13 chars.'
            ),
            'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'message' => 'Height must be positive'
            ),
        ),
        'imageurl' => array(
            /*'webaddress' => array(
                'rule' => 'url',
                'message' => 'Please enter a valid image URL. For example, http://www.example.com/images/p1.jpg',
            ),*/
        ),
        'pageurl' => array(
            'webaddress' => array(
                'rule' => 'url',
                'message' => 'Please enter a valid URL. For example, http://www.example.com/p1.html',
                'required' => false,
                'allowEmpty' => true,
            ),
        ),
        'weight' => array(
            'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Weight must be positive'
            )
        ),
        'height' => array(
        'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Height must be positive'
            )
        ),
        'width' => array(
            'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Width must be positive'
            )
        ),
        'depth' => array(
            'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Depth must be positive'
            )
        ),
        'safety_stock' => array(
            'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Safety stock must be a positive number'
            ),
        ),
        'reorder_point' => array(
            'positive' => array(
                'rule' => array('comparison', '>=', 0),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Reorder point must be a positive number'
            ),
        ),
        'packaging_instructions' => array(
            'allowdchars' => array(
                'rule' => 'ValidTextFields',
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Some characters are not valid.',
            ),
        ),
        'bin' => array(
        'allowdchars' => array(
            'rule' => array('custom', '/^[a-z0-9 \-]*$/i'),
            'required' => false,
            'allowEmpty' => true,
            'message' => 'Letters,numbers and spaces only',
            ),
        ),  
        
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'BarcodeStandard' => array(
            'className' => 'BarcodeStandard',
            'foreignKey' => 'barcode_standards_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Status' => array(
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Color' => array(
            'className' => 'Color',
            'foreignKey' => 'color_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Size' => array(
            'className' => 'Size',
            'foreignKey' => 'size_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Category' => array(
            'className' => 'Category',
            'foreignKey' => 'category_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Issue' => array(
            'className' => 'Warehouse',
            'foreignKey' => 'issue_location',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Receive' => array(
            'className' => 'Warehouse',
            'foreignKey' => 'receive_location',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
    
    public $hasMany = array(        
        'Inventory' => array(
            'className' => 'Inventory',
            'foreignKey' => 'product_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OrdersLine' => array(
            'className' => 'OrdersLine',
            'foreignKey' => 'product_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => ''
        ),
        'Serial' => array(
            'className' => 'Serial',
            'foreignKey' => 'product_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => ''
        ),
        'Productsupplier' => array(
            'className' => 'Productsupplier',
            'foreignKey' => 'product_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => ''
        ),
        'ProductsPrices' => array(
            'className' => 'ProductsPrices',
            'foreignKey' => 'product_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => ''
        )
    );

    public $hasAndBelongsToMany = array(
        'Bin' =>
            array(
                'className' => 'Bin',
                'joinTable' => 'products_bins',
            )
    );

    public function getBySku($sku, $user_id, $fields = array('Product.id')) {
        $product = $this->find('first', array(
            'fields' => $fields,
            'conditions' => array(
                'Product.sku' => $sku,
                'Product.user_id' => $user_id,
                'Product.deleted' => 0, // not get delete products
                'Product.status_id NOT IN' => [12, 13], // not get blocked products
            )
        ));
        return $product;
    }

    public function getShortDet($id) {
        $this->id = $id;
        $prod['price'] = $this->field('value');
        $prod['issue_location'] = $this->field('issue_location');
        $prod['receive_location'] = $this->field('receive_location');
        $productPrices = ClassRegistry::init('ProductsPrices');
        $pr = $productPrices->find('list', array('fields' => ['schannel_id', 'value'], 'conditions' => array('product_id' => $this->id)));
        $prod['schannel_prices'] = $pr;
        return $prod;
    }

    public function isOwnedBy($product, $user) {
        return $this->field('id', array('id' => $product, 'user_id' => $user)) !== false;
    }
    
    public function uniqueSKU($sku = null) {
        
        if(!empty($this->data['Product']['user_id'])) {
            $user_id = $this->data['Product']['user_id'];
        } else {
            $user_id = CakeSession::read('Auth.User.id');
        }
        
        $product = $this->find('first', array('conditions' => array('Product.sku' => $sku['sku'], 'Product.user_id' => $user_id), 'contain' => false));
        if($product) {
            if( (isset($this->data['Product']['id']) && $this->data['Product']['id'] == $product['Product']['id']) || ( $this->id && $this->id == $product['Product']['id'])) {
                // Update product
                return true;
            }
        } else {
            return true;
        }
        return "SKU ".$sku['sku']." already exists.";
    }

    public function uniqueName($name = null) {

        if(!empty($this->data['Product']['user_id'])) {
            $user_id = $this->data['Product']['user_id'];
        } else {
            $user_id = CakeSession::read('Auth.User.id');
        }
        
        $count = $this->find('count', array('conditions' => array('Product.name' => $name,'Product.user_id' => $user_id)));
        //return $count == 0;
         if($count == 1){
            return "Product name ".$name['name']." already exists.";
        } else {
            return true;
        }

    }
    
    public function beforeFind($queryData) {
        /*if(CakeSession::read("Auth.User.is_admin") == false) {
            if (is_array($queryData['conditions'])) {   
                $defaultConditions = array('Product.user_id' => CakeSession::read("Auth.User.id"));
                $queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
                return $queryData;
            }
        }*/
    }
    
    public function beforeSave($options = array()) {
        #if($this->data['Product']['user_id'] == CakeSession::read("Auth.User.id")) {
            return true;
        #}
    }
    
    function afterSave($created,$options = null) {
        if($created) {
            $this->inserted_ids[] = $this->getInsertID();
            $user_id = CakeSession::read('Auth.User.id');
            $productcount = $this->find('count', array('conditions' => ['Product.user_id' => $user_id, 'Product.status_id NOT IN' => [12, 13], 'Product.deleted' =>0]));
            CakeSession::write('productcount', $productcount);
        }
        return true;
    }
    
    function isImageFile($imageurl){
        $url = $imageurl['imageurl'];
        $imagedata = getimagesize($url);
        $isimage = (!empty($imagedata) ? true : false); // returns true
        return $isimage;
    }
    
    function isValidUrl($purl){
        
        // first do some quick sanity checks:
        $keys = array_keys($purl);
        $url = $purl[$keys[0]];
        if(!$url || !is_string($url)){
            return false;
        }
        // quick check url is roughly a valid http request: ( http://blah/... ) 
        if( ! preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url) ){
            return false;
        }
        // the next bit could be slow:
        if($this->getHttpResponseCode_using_curl($url) != 200){
        //  if(getHttpResponseCode_using_getheaders($url) != 200){  // use this one if you cant use curl
            return false;
        }
        
        return true;
    }

    function myMime($file){
        if(!empty($_FILES['data'])) {
            if(isset($_FILES['data']['type']['Product']['imageurl'])) {
                if(in_array($_FILES['data']['type']['Product']['imageurl'], array('image/png', 'image/jpg', 'image/jpeg', 'image/gif'))) {
                    return true;
                }
            }
            return false;
        } else {
            return true;
        }
        
    }

    function getHttpResponseCode_using_curl($url, $followredirects = true){
        
        if(! $url || ! is_string($url)){
            return false;
        }
        $ch = @curl_init($url);
        if($ch === false){
            return false;
        }
        @curl_setopt($ch, CURLOPT_HEADER         ,true);    // we want headers
        @curl_setopt($ch, CURLOPT_NOBODY         ,true);    // dont need body
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);    // catch output (do NOT print!)
        if($followredirects){
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,true);
            @curl_setopt($ch, CURLOPT_MAXREDIRS      ,10);  // fairly random number, but could prevent unwanted endless redirects with followlocation=true
        }else{
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,false);
        }
        
        @curl_exec($ch);
        if(@curl_errno($ch)){   // should be 0
            @curl_close($ch);
            return false;
        }
        $code = @curl_getinfo($ch, CURLINFO_HTTP_CODE); // note: php.net documentation shows this returns a string, but really it returns an int
        @curl_close($ch);
        return $code;
    }

    function getHttpResponseCode_using_getheaders($url, $followredirects = true){
        if(! $url || ! is_string($url)){
            return false;
        }
        $headers = @get_headers($url);
        if($headers && is_array($headers)){
            if($followredirects){
                // we want the the last errorcode, reverse array so we start at the end:
                $headers = array_reverse($headers);
            }
            foreach($headers as $hline){
                // search for things like "HTTP/1.1 200 OK" , "HTTP/1.0 200 OK" , "HTTP/1.1 301 PERMANENTLY MOVED" , "HTTP/1.1 400 Not Found" , etc.
                // note that the exact syntax/version/output differs, so there is some string magic involved here
                if(preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $hline, $matches) ){// "HTTP/*** ### ***"
                    $code = $matches[1];
                    return $code;
                }
            }
            return false;
        }
        return false;
    }
    
    public function ValidSKU($check) {
       
        $value = array_values($check);
        $value = $value[0];

        return preg_match('/^[A-Za-z0-9\_\-\.]+$/', $value);
    }

    public function getInvQuantity($id) {
        $res = $this->Inventory->find('all', [
            'conditions' => ['Inventory.product_id' => $id, 'Inventory.deleted' => 0, 'Warehouse.status' => 'active'],
            'fields' => ['Warehouse.id', 'Warehouse.name', 'Inventory.quantity'],
        ]);
        $is_active = false;
        foreach ($res as $value) {
            if($value['Inventory']['quantity'] > 0) {
                $is_active = true;
            }
        }
        $res['disabled'] = !$is_active;
        return $res;
    }

}
