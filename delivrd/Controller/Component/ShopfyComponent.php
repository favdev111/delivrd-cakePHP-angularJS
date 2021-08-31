<?php
App::uses('Component', 'Controller');
App::uses('ImportComponent', 'Controller/Component');

class ShopfyComponent extends ImportComponent {

    public function initialize(Controller $controller) {
        parent::initialize($controller);

        /*$this->settings['api'] = '1780b7c65edc94b5f82efef3412bcbb8';
        $this->settings['pwd'] = 'e9a1251e8e3a62f93866c78cab2437b4';
        $this->settings['store'] = 'delivrd-2.myshopify.com';*/
    }

    public function importShopifyProducts($id) {
        $integration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Shopify','Integration.id' => $id,'Integration.user_id' => $this->Auth->user('id'))));

        // Start log.
        $this->Transfer->create();
        $log['user_id'] = $this->Auth->user('id');
        $log['type'] = Transfer::$types['products'];
        $log['direction'] = Transfer::$direction['import'];
        $log['source'] = Transfer::$source['shopify'];
        $log['source_id'] = $integration['Integration']['id'];
        $log['status'] = Transfer::$status['started'];
        $log['recordscount'] = 0;
        $log['response'] = '';
        $this->Transfer->save($log);
        $transfer_id = $this->Transfer->id;

        try {
            // Get total count of products
            $res = $this->getJson(
                '/admin/products/count.json',
                $integration['Integration']['username'],
                $integration['Integration']['password'],
                $integration['Integration']['url']
            );
        } catch(Exception $e) {
            $response = [
                    'total_found'   => 0,
                    'error'         => 'Invalid Shopify API credentials'
                ];

            // Update Log. Failed.
            $log['status'] = Transfer::$status['failed'];
            $log['recordscount'] = 0;
            $log['response'] = json_encode($response);
            $this->Transfer->save($log);

            return $response;
        }

        $total_products = $res->count;
        if($total_products > 0) {
            $limit = 20;
            $pages = ceil($total_products/$limit);

            // Get products details
            $products = array();
            for($i = 1; $i<=$pages ;$i++){
                $res = $this->getJson(
                    "/admin/products.json?limit=". $limit ."&page=".$i,
                    $integration['Integration']['username'],
                    $integration['Integration']['password'],
                    $integration['Integration']['url']
                );
                foreach($res->products as $product){
                    $r = $this->getProducts($product);
                    if($r) {
                        $products = array_merge($products, $r);
                    }
                }
            }
            
            $result = $this->importProduct($products, $integration['Integration']['is_ecommerce'], $transfer_id);

            $response = [
                'total_found'   => count($products), //$total_products,
                'skiped'        => $result['skiped'], //$total_products - count($products), // We skip all products with empty SKU
                'skiped_det'    => $result['skiped_details'],
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

    public function getProducts($data) {
        $imgs = array();
        foreach($data->images as $img){
            $imgs[] = $img->src;
        }

        $options = array();
        foreach($data->options as $opt){
            $nm = strtolower($opt->name);
            $options[$nm] = $opt->position;
        }

        $products = array();
        foreach($data->variants as $var){
            if(!empty($var->sku)) {
                $color = $size = '';
                if(isset($options['size']) && $options['size'] == 1 ){
                    $size = $var->option1;
                }
                if(isset($options['size']) && $options['size'] == 2){
                    $size = $var->option2;
                }
                if(isset($options['size']) && $options['size'] == 3){
                    $size = $var->option3;
                }

                if(isset($options['color']) && $options['color'] == 1 ){
                    $color = $var->option1;
                }
                if(isset($options['color']) && $options['color'] == 2){
                    $color = $var->option2;
                }
                if(isset($options['color']) && $options['color'] == 3){
                    $color = $var->option3;
                }

                $product['Product']['name']                 = $data->title.': '.$var->title;
                $product['Product']['product_id']           = $data->id;
                $product['Product']['parentid']             = $var->id;
                $product['Product']['description']          = $this->prepare_desc($data->body_html);
                $product['Product']['group_id']             = 0;
                $product['Product']['uom']                  = $var->weight_unit; //Weight Units ex: oz
                $product['Product']['weight']               = $var->weight;
                $product['Product']['width']                = '';
                $product['Product']['height']               = '';
                $product['Product']['depth']                = '';
                $product['Product']['barcode']              = $var->barcode;
                $product['Product']['sku']                  = $var->sku;
                $product['Product']['consumption']          = '';
                $product['Product']['bin']                  = '';
                $product['Product']['value']                = number_format((float)$var->price, 2, '.', '');;
                $product['Product']['imageurl']             = (isset($data->image->src)?$data->image->src:'');
                $product['Product']['pageurl']              = '';
                $product['Product']['color_id']             = '';
                $product['Product']['size_id']              = '';
                $product['Product']['category']             = $data->product_type;
                $product['Product']['category_id']          = '';
                $product['Product']['publish']              = '';
                $product['Product']['createdinsource']      = $var->created_at;
                $product['Product']['modifiedinsource']     = $var->updated_at;
                $product['Product']['catalog']              = '';
                $product['Product']['created']              = date('Y-m-d H:i:s');
                $product['Product']['modified']             = date('Y-m-d H:i:s');
                // Question: for some products shopify return negative quantity, what it mean? What we need to do with it?
                $product['Inventory'][0]['quantity']        = abs($var->inventory_quantity);
                $product['Inventory'][0]['user_id']         = $this->Auth->user('id');
                $product['Inventory'][0]['dcop_user_id']    = $this->Auth->user('id');
                $product['Inventory'][0]['warehouse_id']    = $this->Session->read('default_warehouse');

                $products[] = $product;
            }
       }
       return $products;
    }

    public function importShopifyOrders($id) {
        $integration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Shopify','Integration.id' => $id,'Integration.user_id' => $this->Auth->user('id'))));

        // Start log.
        $this->Transfer->create();
        $log['user_id'] = $this->Auth->user('id');
        $log['type'] = Transfer::$types['orders'];
        $log['direction'] = Transfer::$direction['import'];
        $log['source'] = Transfer::$source['shopify'];
        $log['source_id'] = $integration['Integration']['id'];
        $log['status'] = Transfer::$status['started'];
        $log['recordscount'] = 0;
        $log['response'] = '';
        $this->Transfer->save($log);
        $transfer_id = $this->Transfer->id;

        // Get Count of orders
        $two_year_ago = time() - 3600*24*365*2;
        $updated_at_min = date('Y-m-d', $two_year_ago);

        $res = $this->getJson(
            '/admin/orders/count.json?updated_at_min='. $updated_at_min .'&fulfillment_status=unshipped&financial_status=paid&status=open',
            $integration['Integration']['username'],
            $integration['Integration']['password'],
            $integration['Integration']['url']
        );
        $total_orders = $res->count;

        if($total_orders > 0) {
            //Fetch all orders
            $limit = 250;
            $orders = array();
            $pages = ceil($total_orders/$limit);
            for($i = 1; $i <= $pages; $i++) {
                $res = $this->getJson("/admin/orders.json?updated_at_min=". $updated_at_min ."&fulfillment_status=unshipped&financial_status=paid&status=open&limit=". $limit ."&page=".$i, $integration['Integration']['username'],$integration['Integration']['password'],$integration['Integration']['url']);
                if(isset($res->orders)) {
                    foreach($res->orders as $order){
                        $r = $this->getOrders($order);
                        if($r) {
                            $orders[] = $r;
                        }
                    }
                }
            }

            $result = $this->importOrders($orders, $integration['Schannel']['id'], $transfer_id);

            $response = [
                    'total_found'   => $total_orders,
                    'added'         => $result['added'],
                    'updated'       => $result['updated'],
                    'skiped'        => $result['skiped'],
                    'skiped_det'    => $result['skiped_details'],
                    'errors_count'  => $result['errors_count'],
                    'errors'        => $result['errors'],
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

    public function getOrders($data) {
        $order = array();

        $shippingcosts = 0;
        $state = '';
        $stateorprovince = '';
        if(isset($data->shipping_address)) {
            if(isset($data->shipping_lines[0]->price)) {
                $shippingcosts = $data->shipping_lines[0]->price;
            }
            
            if($data->shipping_address->country_code == 'US') {
                $stateorprovince = $data->shipping_address->province_code;
            } else {
                $stateorprovince = $data->shipping_address->province;
            }
            $order['Order']['shipping_costs']           = $shippingcosts;
            $order['Order']['ship_to_customerid']       = $data->shipping_address->first_name.' '.$data->shipping_address->last_name;
            $order['Order']['email']                    = $data->email;
            $order['Order']['ship_to_phone']            = str_replace([' ', '-', '.', '_', '(', ')', '+'], '', $data->shipping_address->phone);
            $order['Order']['ship_to_city']             = $data->shipping_address->city;
            $order['Order']['ship_to_street']           = $data->shipping_address->address1.' '.$data->shipping_address->address2;
            $order['Order']['ship_to_city']             = $data->shipping_address->city;
            $order['Order']['ship_to_zip']              = $data->shipping_address->zip;
            $order['Order']['ship_to_stateprovince']    = $stateorprovince;
            //$order['Order']['state_id']               = $state;
            $order['Order']['country_id']               = $data->shipping_address->country_code;
        } elseif(isset($data->biling_address)) {
            if($data->biling_address->country_code == 'US') {
                $stateorprovince = $data->biling_address->province_code;
            } else {
                $stateorprovince = $data->biling_address->province;
            }
            $order['Order']['shipping_costs']           = $shippingcosts;
            $order['Order']['ship_to_customerid']       = $data->biling_address->first_name.' '.$data->biling_address->last_name;
            $order['Order']['ship_to_city']             = $data->biling_address->city;
            $order['Order']['ship_to_street']           = $data->biling_address->address1.' '.$data->biling_address->address2;
            $order['Order']['ship_to_city']             = $data->biling_address->city;
            $order['Order']['ship_to_zip']              = $data->biling_address->zip;
            $order['Order']['ship_to_stateprovince']    = $stateorprovince;
            $order['Order']['country_id']               = $data->biling_address->country_code;
        } elseif(isset($data->customer->default_address)) {
            if($data->customer->default_address->country_code == 'US') {
                $stateorprovince = $data->customer->default_address->province_code;
            } else {
                $stateorprovince = $data->customer->default_address->province;
            }
            $order['Order']['shipping_costs']           = $shippingcosts;
            $order['Order']['ship_to_customerid']       = $data->customer->default_address->first_name.' '.$data->customer->default_address->last_name;
            $order['Order']['ship_to_city']             = $data->customer->default_address->city;
            $order['Order']['ship_to_street']           = $data->customer->default_address->address1.' '.$data->customer->default_address->address2;
            $order['Order']['ship_to_city']             = $data->customer->default_address->city;
            $order['Order']['ship_to_zip']              = $data->customer->default_address->zip;
            $order['Order']['ship_to_stateprovince']    = $stateorprovince;
            $order['Order']['country_id']               = $data->customer->default_address->country_code;
        } else {
            $order['Order']['shipping_costs']           = $shippingcosts;
            if(isset($data->customer)) {
                $order['Order']['ship_to_customerid']   = $data->customer->first_name.' '.$data->customer->last_name;
            } else {
                $order['Order']['ship_to_customerid']   = '';
            }
            $order['Order']['ship_to_city']             = '';
            $order['Order']['ship_to_street']           = '';
            $order['Order']['ship_to_city']             = '';
            $order['Order']['ship_to_zip']              = '';
            $order['Order']['ship_to_stateprovince']    = $stateorprovince;
            //$order['Order']['state_id']               = $state;
            $order['Order']['country_id']               = '';
        }

        $udpatesourcenew = date("Y-m-d H:m:s", strtotime($data->updated_at));
        $createdsourcenew = date("Y-m-d H:m:s", strtotime($data->created_at));
        $requesteddate = date("Y-m-d");

        $order['Order']['external_orderid']         = $data->order_number;
        $order['Order']['external_orderid2']        = $data->id;
        $order['Order']['schannel_id']              = 'Shopify';
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

    /**
     * Send request to Shopify
     *
     *
     */
    public function getJson($url = '/admin/products/count.json',$username = '',$password = '', $url_store = '') {
        $this->settings['api'] = $username;
        $this->settings['pwd'] = $password;
        $this->settings['store'] = str_replace(['https://', 'http://'], '', rtrim($url_store, '/'));
        
        $url = "https://".$this->settings['api'].":".$this->settings['pwd']."@". $this->settings['store'].$url;
        $result = $this->send_request($url);
        return $result;
    }

    public function send_request($url) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'QA server');
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $output = curl_exec($ch);

        // if ($output === false) 
        //     throw new \Exception(curl_error($ch));

        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($responseCode == 200) {
            try {
                $jo = json_decode($output);
                if ($jo === false)
                    throw new \Exception("Failed to parse JSON");

                $response = $jo;
                $response->status = 'OK';
            } catch (\Exception $e) {
                $response->status = 'REQUEST_FAILED';
                throw $e;
            }
        } else {
            try {
                $jo = json_decode($output);
                if ($jo === false)
                    throw new \Exception("Failed to parse JSON");
                $response = $jo;
                switch ($responseCode) {
                    case 400:
                        $response->status = 'REQUEST_REJECTED';
                        $response->count = 0;
                        break;
                    case 402:
                        $response->status = 'METHOD_NOT_ALLOWED';
                        $response->count = 0;
                        break;
                    case 405:
                        $response->status = 'METHOD_NOT_ALLOWED';
                        $response->count = 0;
                        break;
                    default:
                        $response->status = 'REQUEST_FAILED';
                        $response->count = 0;
                        break;
                }

                return $response;
            } catch (\Exception $e) {
                $response->status = 'REQUEST_FAILED';
                throw $e;
            }
        }
        return $response;
    }
}