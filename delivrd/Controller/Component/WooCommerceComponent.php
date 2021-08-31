<?php
App::uses('Component', 'Controller');
App::uses('ImportComponent', 'Controller/Component');

class WooCommerceComponent extends ImportComponent {

    public function initialize(Controller $controller) {
        parent::initialize($controller);

        $this->settings['options'] = [
            'debug'           => true,
            'return_as_array' => false,
            'validate_url'    => false,
            'timeout'         => false,
            'ssl_verify'      => false,
        ];
    }


    public function importWooOrders($id) {
        $integration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Woocommerce','Integration.id' => $id,'Integration.user_id' => $this->Auth->user('id'))));
        
        // Start log.
        $this->Transfer->create();
        $log['user_id'] = $this->Auth->user('id');
        $log['type'] = Transfer::$types['orders'];
        $log['direction'] = Transfer::$direction['import'];
        $log['source'] = Transfer::$source['woocommerce'];
        $log['source_id'] = $integration['Integration']['id'];
        $log['status'] = Transfer::$status['started'];
        $log['recordscount'] = 0;
        $log['response'] = '';
        $this->Transfer->save($log);
        $transfer_id = $this->Transfer->id;

        $vednor_path = App::path('Vendor');
        require_once($vednor_path[0]."wooapi/lib/woocommerce-api.php");

        // Initialize the class
        $client = new WC_API_Client(
            $integration['Integration']['url'],
            $integration['Integration']['username'],
            $integration['Integration']['password'],
            $this->settings['options']
        );
        try {
            $orders = $client->orders->get('',array( 'filter[limit]' => 10000 ));
            $res = $orders->orders;
        } catch (Exception $e) {
            $response = [
                    'total_found'   => 0,
                    'error'         => 'Invalid WooCommerce API credentials'
                ];

            // Update Log. Failed.
            $log['status'] = Transfer::$status['failed'];
            $log['recordscount'] = 0;
            $log['response'] = json_encode($response);
            $this->Transfer->save($log);

            return $response;
        }

        if (!empty($res)) {
            $all_orders = [];
            foreach ($res as $r) {
                $all_orders[] = $this->getOrder($r);
            }
            $result = $this->importOrders($all_orders, $integration['Schannel']['id'], $transfer_id);

            $response = [
                    'total_found'   => count($res),
                    'added'         => $result['added'],
                    'updated'       => $result['updated'],
                    'errors'        => $result['errors'],
                    'errors_count'  => $result['errors_count']
                ];
        } else {
            $response = [
                    'total_found'   => 0,
                    'added'         =>  0
                ];
        }
        // Update Log. Success.
        $log['status'] = Transfer::$status['success'];
        $log['recordscount'] = $response['added'];
        $log['response'] = json_encode($response);
        $this->Transfer->save($log);

        return $response;
    }

    public function getOrder($data) {
        
        if(isset($data->shipping_address)) {
            $order['Order']['ship_to_customerid']       = $data->shipping_address->first_name.' '.$data->shipping_address->last_name;
            $order['Order']['email']                    = $data->email;
            $order['Order']['ship_to_phone']            = $data->shipping_address->phone;
            $order['Order']['ship_to_city']             = $data->shipping_address->city;
            $order['Order']['ship_to_street']           = $data->shipping_address->address_1.' '.$data->shipping_address->address_2;
            $order['Order']['ship_to_zip']              = $data->shipping_address->postcode;
            $order['Order']['ship_to_stateprovince']    = $data->shipping_address->state;
            $order['Order']['country_id']               = $data->shipping_address->country;

        } elseif(isset($data->billing_address)) {
            $order['Order']['ship_to_customerid']       = $data->billing_address->first_name.' '.$data->billing_address->last_name;
            $order['Order']['email']                    = $data->email;
            $order['Order']['ship_to_phone']            = $data->billing_address->phone;
            $order['Order']['ship_to_city']             = $data->billing_address->city;
            $order['Order']['ship_to_street']           = $data->billing_address->address_1.' '.$data->billing_address->address_2;
            $order['Order']['ship_to_zip']              = $data->billing_address->postcode;
            $order['Order']['ship_to_stateprovince']    = $data->billing_address->state;
            $order['Order']['country_id']               = $data->billing_address->country;

        } else {
            $order['Order']['ship_to_customerid']       = $data->customer->first_name.' '.$data->customer->last_name;
            $order['Order']['email']                    = $data->email;
            $order['Order']['ship_to_phone']            = $data->billing_address->phone;
            $order['Order']['ship_to_city']             = $data->billing_address->city;
            $order['Order']['ship_to_street']           = '';
            $order['Order']['ship_to_zip']              = '';
            $order['Order']['ship_to_stateprovince']    = '';
            $order['Order']['country_id']               = '';
        }

        $udpatesourcenew = date("Y-m-d H:m:s", strtotime($data->updated_at));
        $createdsourcenew = date("Y-m-d H:m:s", strtotime($data->created_at));
        $requesteddate = date("Y-m-d");

        $order['Order']['shipping_costs']           = $data->total_shipping;
        $order['Order']['external_orderid']         = $data->order_number;
        $order['Order']['external_orderid2']        = $data->id;
        $order['Order']['schannel_id']              = 'Woocommerce';
        $order['Order']['requested_delivery_date']  = $requesteddate;
        $order['Order']['currency']                 = $data->currency;
        $order['Order']['remarks']                  = $data->note;
        $order['Order']['createdinsource']          = $createdsourcenew;
        $order['Order']['modifiedinsource']         = $udpatesourcenew;
        
        $order['OrdersLine'] = [];

        $i = 0;
        foreach($data->line_items as $item) {
            $order['OrdersLine'][$i]['line_number'] = ($i+1) * 10;
            $order['OrdersLine'][$i]['sku']         = $item->sku;
            $order['OrdersLine'][$i]['quantity']    = $item->quantity;
            $order['OrdersLine'][$i]['unit_price']  = $item->price;

            /* fulfillment_status
                fulfilled: Every line item in the order has been fulfilled.
                null: None of the line items in the order have been fulfilled.
                partial: At least one line item in the order has been fulfilled.
            */
            $i++;
        }
        return $order;
    }


    public function importWooProducts($id) {
        $integration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Woocommerce','Integration.id' => $id,'Integration.user_id' => $this->Auth->user('id'))));
        
        // Start log.
        $this->Transfer->create();
        $log['user_id'] = $this->Auth->user('id');
        $log['type'] = Transfer::$types['products'];
        $log['direction'] = Transfer::$direction['import'];
        $log['source'] = Transfer::$source['woocommerce'];
        $log['source_id'] = $integration['Integration']['id'];
        $log['status'] = Transfer::$status['started'];
        $log['recordscount'] = 0;
        $log['response'] = '';
        $this->Transfer->save($log);
        $transfer_id = $this->Transfer->id;

        $vednor_path = App::path('Vendor');
        require_once($vednor_path[0]."wooapi/lib/woocommerce-api.php");

        // Initialize the class
        $client = new WC_API_Client(
            $integration['Integration']['url'],
            $integration['Integration']['username'],
            $integration['Integration']['password'],
            $this->settings['options']
        );

        try {
            $response   = $client->products->get('',array( 'filter[limit]' => 100 )); //000
            $res        = $response->products;
        } catch (Exception $e) {

            $response = [
                    'total_found'   => 0,
                    'error'         => 'Invalid WooCommerce API credentials'
                ];

            // Update Log. Failed.
            $log['status'] = Transfer::$status['failed'];
            $log['recordscount'] = 0;
            $log['response'] = json_encode($response);
            $this->Transfer->save($log);
            return $response;
        }

        if (!empty($res)) {
            $all_products = [];
            foreach($res as $r){
                $product = $this->getProduct($r);
                $all_products = array_merge($all_products, $product);
            }
            $result = $this->importProduct($all_products, $integration['Integration']['is_ecommerce'], $transfer_id);

            $response = [
                    'total_found'   => count($res),
                    'variants'      => count($all_products), // some more products with all variants
                    'added'         => $result['added'],
                    'updated'       => $result['updated'],
                    'errors_count'  => count($result['errors']),
                    'errors'        => $result['errors'],
                ];
        } else {
            $response = [
                    'total_found'   => 0,
                    'added'         => 0
                ];
        }

        // Update Log. Success.
        $log['status'] = Transfer::$status['success'];
        $log['recordscount'] = $response['added'];
        $log['response'] = json_encode($response);
        $this->Transfer->save($log);

        return $response;
    }

    function getProduct($data) {
        if(empty($data->variations)) {
            $product[] = $this->getSimpleProduct($data);
        } else {
            foreach ($data->variations as $value) {
                $product[] = $this->getVariationProduct($value, $data);
            }
        }
        return $product;
    }

    function getVariationProduct($data, $parent) {
        
        $imgs = array();
        if($data->image) { // variation image
            foreach($data->image as $img){
                $imgs[] = $img->src;
            }
        }

        foreach($parent->images as $img){ // parent image
            $imgs[] = $img->src;
        }

        $options = array();
        foreach($parent->attributes as $opt){
            $nm = strtolower($opt->slug);
            $options[$opt->slug] = $opt->position;
        }

        $category = '';
        if(!empty($parent->categories)) {
            $category = $parent->categories[0];
        }

        $product['Product']['name']                 = $parent->title;
        $product['Product']['product_id']           = $data->id;
        $product['Product']['parentid']             = $parent->id;
        $product['Product']['description']          = $this->prepare_desc($parent->description);
        $product['Product']['group_id']             = 0;
        $product['Product']['uom']                  = ''; // not return any units
        $product['Product']['weight']               = $data->weight;
        $product['Product']['width']                = $data->dimensions->width;
        $product['Product']['height']               = $data->dimensions->height;
        $product['Product']['depth']                = $data->dimensions->length; //$data->dimensions->unit;

        $product['Product']['barcode']              = '';
        $product['Product']['sku']                  = $data->sku;
        $product['Product']['consumption']          = '';
        $product['Product']['bin']                  = '';
        $product['Product']['value']                = number_format((float)$data->price, 2, '.', '');;
        $product['Product']['imageurl']             = (count($imgs) > 0?$imgs[0]:'');
        $product['Product']['pageurl']              = $data->permalink;
        $product['Product']['color_id']             = '';
        $product['Product']['size_id']              = '';
        $product['Product']['category']             = $category;
        $product['Product']['category_id']          = '';
        $product['Product']['publish']              = '';
        $product['Product']['createdinsource']      = $data->created_at;
        $product['Product']['modifiedinsource']     = $data->updated_at;
        $product['Product']['catalog']              = '';
        $product['Product']['created']              = date('Y-m-d H:i:s');
        $product['Product']['modified']             = date('Y-m-d H:i:s');
        // Question: for some products shopify return negative quantity, what it mean? What we need to do with it?
        $product['Inventory'][0]['quantity']        = abs($data->stock_quantity);
        $product['Inventory'][0]['user_id']         = $this->Auth->user('id');
        $product['Inventory'][0]['dcop_user_id']    = $this->Auth->user('id');
        $product['Inventory'][0]['warehouse_id']    = $this->Session->read('default_warehouse');
    
        return $product;
    }

    function getSimpleProduct($data) {
        
        $imgs = array();
        foreach($data->images as $img){
            $imgs[] = $img->src;
        }

        $options = array();
        foreach($data->attributes as $opt){
            $nm = strtolower($opt->slug);
            $options[$opt->slug] = $opt->position;
        }

        $category = '';
        if(!empty($data->categories)) {
            $category = $data->categories[0];
        }
        
        $product['Product']['name']                 = $data->title;
        $product['Product']['product_id']           = $data->id;
        $product['Product']['parentid']             = $data->id;
        $product['Product']['description']          = $this->prepare_desc($data->description);
        $product['Product']['group_id']             = 0;
        $product['Product']['uom']                  = ''; // not return any units
        $product['Product']['weight']               = $data->weight;
        $product['Product']['width']                = $data->dimensions->width;
        $product['Product']['height']               = $data->dimensions->height;
        $product['Product']['depth']                = $data->dimensions->length; //$data->dimensions->unit;

        $product['Product']['barcode']              = '';
        $product['Product']['sku']                  = $data->sku;
        $product['Product']['consumption']          = '';
        $product['Product']['bin']                  = '';
        $product['Product']['value']                = number_format((float)$data->price, 2, '.', '');;
        $product['Product']['imageurl']             = (count($imgs) > 0?$imgs[0]:'');
        $product['Product']['pageurl']              = $data->permalink;
        $product['Product']['color_id']             = '';
        $product['Product']['size_id']              = '';
        $product['Product']['category']             = $category;
        $product['Product']['category_id']          = '';
        $product['Product']['publish']              = '';
        $product['Product']['createdinsource']      = $data->created_at;
        $product['Product']['modifiedinsource']     = $data->updated_at;
        $product['Product']['catalog']              = '';
        $product['Product']['created']              = date('Y-m-d H:i:s');
        $product['Product']['modified']             = date('Y-m-d H:i:s');
        // Question: for some products shopify return negative quantity, what it mean? What we need to do with it?
        $product['Inventory'][0]['quantity']        = abs($data->stock_quantity);
        $product['Inventory'][0]['user_id']         = $this->Auth->user('id');
        $product['Inventory'][0]['dcop_user_id']    = $this->Auth->user('id');
        $product['Inventory'][0]['warehouse_id']    = $this->Session->read('default_warehouse');
    
        return $product;
    }
}