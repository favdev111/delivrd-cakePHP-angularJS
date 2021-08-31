<?php

App::uses('Component', 'Controller');
App::uses('ImportComponent', 'Controller/Component');

require_once(APP . 'Vendor'. DS . 'denisch'. DS .'CacheLib'. DS .'functions.php');

App::import('Vendor', array('file' => 'autoload'));

define ('DATE_FORMAT', 'Y-m-d\TH:i:s\Z');



/**
 * Amazon component
 *
 * Provides a basic functionality to import Products/Inventories/Orders
 * from Amazon with MWS API
 */
class AmazonComponent extends ImportComponent
{
    var $_MarketplaceIds = [
        'US' => 'ATVPDKIKX0DER',
        'Canada' => 'A2EUQ1WTGCTBG2',
        'Mexico' => 'A1AM78C64UM0Y8',
        'Spain' => 'A1RKKUPIHCS9HS',
        'UK' => 'A1F83G8C2ARO7P',
        'France' => 'A13V1IB3VIYZZH',
        'Germany' => 'A1PA6795UKMFR9',
        'Italy' => 'APJ6JRA9NG5V4',
        'Brazil' => 'A2Q3Y263D00KWC',
        'India' => 'A21TJRUUN4KGV',
        'China' => 'AAHKV2X7AFYLW',
        'Japan' => 'A1VC38T7YXB528',
        'Australia' => 'A39IBJ37TRP1C6'
    ];

    var $_endpoints = [
        'US' => 'https://mws.amazonservices.com',
        'Canada' => 'https://mws.amazonservices.com',
        'Mexico' => 'https://mws.amazonservices.com',
        'Spain' => 'https://mws-eu.amazonservices.com',
        'UK' => 'https://mws-eu.amazonservices.com',
        'France' => 'https://mws-eu.amazonservices.com',
        'Germany' => 'https://mws-eu.amazonservices.com',
        'Italy' => 'https://mws-eu.amazonservices.com',
        'Brazil' => 'https://mws.amazonservices.com',
        'India' => 'https://mws.amazonservices.in',
        'China' => 'https://mws.amazonservices.com.cn',
        'Japan' => 'https://mws.amazonservices.jp',
        'Australia' => 'https://mws.amazonservices.com.au'
    ];

    var $settings = [];
    /**
     * Initialize config data and properties.
     *
     * @param array $config The config data.
     * @return void
     */
    public function initialize(Controller $controller) {
        parent::initialize($controller);
        // Curencies: GBP, EUR, USD
        $this->settings['options'] = [
            'debug'           => true,
            'return_as_array' => false,
            'validate_url'    => false,
            'timeout'         => false,
            'ssl_verify'      => false,
        ];
    }

    public function connect() {
        $integration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Amazon','Integration.user_id' => $this->Auth->user('id'))));

        // Connect to Amazon
        $config = array (
           'ServiceURL' => $integration['Integration']['url'],
           'ProxyHost' => null,
           'ProxyPort' => -1,
           'MaxErrorRetry' => 3,
        );

        $service = new MarketplaceWebService_Client(
                $integration['Integration']['access_key'],
                $integration['Integration']['password'], //secret_key
                $config,
                'DelivrdAPP',
                '0.0.1');

    }

    public function generateProudctReport($id) {
        $integration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Amazon','Integration.id' => $id,'Integration.user_id' => $this->Auth->user('id'))));
        
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
        

        // Connect to Amazon
        $config = array (
           'ServiceURL' => $integration['Integration']['url'], # .'/Reports/2009-01-01
           'ProxyHost' => null,
           'ProxyPort' => -1,
           'MaxErrorRetry' => 3,
        );

        $service = new MarketplaceWebService_Client(
            $integration['Integration']['access_key'],
            $integration['Integration']['password'], //secret_key
            $config,
            'DelivrdAPP',
            '0.0.1'
        );

        $request = new MarketplaceWebService_Model_RequestReportRequest();
        $request->setMarketplaceIdList(['Id' => array($integration['Integration']['marketplace_id'])]);
        $request->setMerchant($integration['Integration']['username']);
        $request->setReportType('_GET_MERCHANT_LISTINGS_DATA_'); // _GET_FLAT_FILE_OPEN_LISTINGS_DATA_

        try {
            $response = $service->requestReport($request);
            
            ob_start();
            echo ("Service Response\n");
            echo ("=============================================================================\n");
            echo("        RequestReportResponse\n");
            if ($response->isSetRequestReportResult()) {
                echo("            RequestReportResult\n");
                $requestReportResult = $response->getRequestReportResult();
                if ($requestReportResult->isSetReportRequestInfo()) {
                    $reportRequestInfo = $requestReportResult->getReportRequestInfo();
                    echo("                ReportRequestInfo\n");
                    if ($reportRequestInfo->isSetReportRequestId()) {
                        echo("                    ReportRequestId\n");
                        echo("                        " . $reportRequestInfo->getReportRequestId() . "\n");
                    }
                    if ($reportRequestInfo->isSetReportType()) {
                        echo("                    ReportType\n");
                        echo("                        " . $reportRequestInfo->getReportType() . "\n");
                    }
                    if ($reportRequestInfo->isSetStartDate()) {
                        echo("                    StartDate\n");
                        echo("                        " . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "\n");
                    }
                    if ($reportRequestInfo->isSetEndDate()) {
                        echo("                    EndDate\n");
                        echo("                        " . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "\n");
                    }
                    if ($reportRequestInfo->isSetSubmittedDate()) {
                        echo("                    SubmittedDate\n");
                        echo("                        " . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                    }
                    if ($reportRequestInfo->isSetReportProcessingStatus()) {
                        echo("                    ReportProcessingStatus\n");
                        echo("                        " . $reportRequestInfo->getReportProcessingStatus() . "\n");
                    }
                }
            } 
            if ($response->isSetResponseMetadata()) { 
                echo("            ResponseMetadata\n");
                $responseMetadata = $response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId()) {
                    echo("RequestId\n");
                    echo("                    " . $responseMetadata->getRequestId() . "\n");
                }
            } 
            echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

            $log['status'] = Transfer::$status['success'];
            $log['recordscount'] = 0;
            $log['response'] = ob_get_clean();
            $this->Transfer->save($log);

            $response = [
                'status'        => 'success',
            ];

        } catch (MarketplaceWebService_Exception $ex) {
            ob_start();
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            #echo("XML: " . $ex->getXML() . "\n");
            #echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");

            $log['status'] = Transfer::$status['success'];
            $log['recordscount'] = 0;
            $log['response'] = ob_get_clean();
            $this->Transfer->save($log);

            $response = [
                'status'        => 'failed',
                'error'         => $log['response']
            ];
        }
        return $response;
    }

    public function generateOrdersReport() {
        $integration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Amazon','Integration.user_id' => $this->Auth->user('id'))));
        
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
        

        // Connect to Amazon
        $config = array (
           'ServiceURL' => $integration['Integration']['url'], # .'/Reports/2009-01-01
           'ProxyHost' => null,
           'ProxyPort' => -1,
           'MaxErrorRetry' => 3,
        );

        $service = new MarketplaceWebService_Client(
            $integration['Integration']['access_key'],
            $integration['Integration']['password'], //secret_key
            $config,
            'DelivrdAPP',
            '0.0.1'
        );

        $request = new MarketplaceWebService_Model_RequestReportRequest();
        $request->setMarketplaceIdList(['Id' => array($integration['Integration']['marketplace_id'])]);
        $request->setMerchant($integration['Integration']['username']);
        $request->setReportType('_GET_FLAT_FILE_ORDERS_DATA_'); //_GET_ORDERS_DATA_

        try {
            $response = $service->requestReport($request);

            ob_start();
            echo ("Service Response\n");
            echo ("=============================================================================\n");
            echo("        RequestReportResponse\n");
            if ($response->isSetRequestReportResult()) {
                echo("            RequestReportResult\n");
                $requestReportResult = $response->getRequestReportResult();
                if ($requestReportResult->isSetReportRequestInfo()) {
                    $reportRequestInfo = $requestReportResult->getReportRequestInfo();
                    echo("                ReportRequestInfo\n");
                    if ($reportRequestInfo->isSetReportRequestId()) {
                        echo("                    ReportRequestId\n");
                        echo("                        " . $reportRequestInfo->getReportRequestId() . "\n");
                    }
                    if ($reportRequestInfo->isSetReportType()) {
                        echo("                    ReportType\n");
                        echo("                        " . $reportRequestInfo->getReportType() . "\n");
                    }
                    if ($reportRequestInfo->isSetStartDate()) {
                        echo("                    StartDate\n");
                        echo("                        " . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "\n");
                    }
                    if ($reportRequestInfo->isSetEndDate()) {
                        echo("                    EndDate\n");
                        echo("                        " . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "\n");
                    }
                    if ($reportRequestInfo->isSetSubmittedDate()) {
                        echo("                    SubmittedDate\n");
                        echo("                        " . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                    }
                    if ($reportRequestInfo->isSetReportProcessingStatus()) {
                        echo("                    ReportProcessingStatus\n");
                        echo("                        " . $reportRequestInfo->getReportProcessingStatus() . "\n");
                    }
                }
            }
            if ($response->isSetResponseMetadata()) { 
                echo("            ResponseMetadata\n");
                $responseMetadata = $response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId()) {
                    echo("RequestId\n");
                    echo("                    " . $responseMetadata->getRequestId() . "\n");
                }
            }
            echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

            $log['status'] = Transfer::$status['success'];
            $log['recordscount'] = 0;
            $log['response'] = ob_get_clean();
            $this->Transfer->save($log);

            $response = [
                'status'        => 'success',
            ];
        } catch (MarketplaceWebService_Exception $ex) {
            
            ob_start();
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            #echo("XML: " . $ex->getXML() . "\n");
            #echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");

            $log['status'] = Transfer::$status['failed'];
            $log['recordscount'] = 0;
            $log['response'] = ob_get_clean();
            $this->Transfer->save($log);

            $response = [
                'status'        => 'failed',
                'error'         => $log['response']
            ];
        }
        return $response;
    }

    /**
     * param $type string _GET_ORDERS_DATA_ | _GET_MERCHANT_LISTINGS_DATA_
     *
     */
    public function getReportList($type) {

        $integration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Amazon','Integration.user_id' => $this->Auth->user('id'))));

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

        // Connect to Amazon
        $config = array (
           'ServiceURL' => $integration['Integration']['url'], # .'/Reports/2009-01-01
           'ProxyHost' => null,
           'ProxyPort' => -1,
           'MaxErrorRetry' => 3,
        );

        $service = new MarketplaceWebService_Client(
                $integration['Integration']['access_key'],
                $integration['Integration']['password'], //secret_key
                $config,
                'DelivrdAPP',
                '0.0.1');

        $request = new MarketplaceWebService_Model_GetReportListRequest();
        $request->setMerchant($integration['Integration']['username']);
        $request->setAvailableToDate(new DateTime('now', new DateTimeZone('UTC')));
        $request->setAvailableFromDate(new DateTime('-1 day', new DateTimeZone('UTC')));
        $request->setAcknowledged(false);
        $typeList = new MarketplaceWebService_Model_TypeList();
        $typeList->setType($type);
        $request->setReportTypeList($typeList);

        $reports = [];
        try {
            $response = $service->getReportList($request);
            if ($response->isSetGetReportListResult()) { 
                $getReportListResult = $response->getGetReportListResult();
                $reportInfoList = $getReportListResult->getReportInfoList();
                foreach ($reportInfoList as $reportInfo) {
                    $report = [];
                    if ($reportInfo->isSetReportId()) {
                        $report['reportId'] = $reportInfo->getReportId();
                    }
                    if ($reportInfo->isSetReportType()) {
                        $report['reportType'] = $reportInfo->getReportType();
                    }
                    $reports[] = $report;
                }
            }
            $log['status'] = Transfer::$status['failed'];
            $log['recordscount'] = 0;
            $log['response'] = 'Success';
            $this->Transfer->save($log);

            $final = [];
            if($reports) {
                $final = $reports[0];
            }
            $response = [
                'status'        => 'success',
                'reports'       => $final
            ];

        } catch (MarketplaceWebService_Exception $ex) {
            ob_start();
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            #echo("XML: " . $ex->getXML() . "\n");
            #echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");

            $log['status'] = Transfer::$status['failed'];
            $log['recordscount'] = 0;
            $log['response'] = ob_get_clean();
            $this->Transfer->save($log);

            $response = [
                'status'        => 'failed',
                'error'         => $log['response']
            ];
        }
        return $response;
    }

    public function getReport($id, $reportId, $type='products') {          

        $integration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Amazon','Integration.id' => $id,'Integration.user_id' => $this->Auth->user('id'))));

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

        // Connect to Amazon
        $config = array (
           'ServiceURL' => $integration['Integration']['url'], # .'/Reports/2009-01-01
           'ProxyHost' => null,
           'ProxyPort' => -1,
           'MaxErrorRetry' => 3,
        );

        $service = new MarketplaceWebService_Client(
                $integration['Integration']['access_key'],
                $integration['Integration']['password'], //secret_key
                $config,
                'DelivrdAPP',
                '0.0.1');

        $request = new MarketplaceWebService_Model_GetReportRequest();
        $request->setMerchant($integration['Integration']['username']);
        $request->setReport(@fopen('php://memory', 'rw+'));
        $request->setReportId($reportId);

        $items = [];
        try {
            

            $response = $service->getReport($request);
            $fp = $request->getReport();
            $fields = [];
            $line1 = fgets($fp, 4096);
            $line1 = trim($line1);
            $fields = explode("\t", $line1);
            
            while (($buffer = fgets($fp, 4096)) !== false) {
                $line = trim($buffer);
                $line = explode("\t", $line);
                $item = array_combine($fields, $line);
                $items[] = $item;
            }

            ob_start();
            echo '<pre>';
            echo ("Service Response\n");
            echo ("=============================================================================\n");
            echo("        GetReportResponse\n");
            if ($response->isSetGetReportResult()) {
                $getReportResult = $response->getGetReportResult(); 
                echo ("            GetReport");
                if ($getReportResult->isSetContentMd5()) {
                    echo ("                ContentMd5");
                    echo ("                " . $getReportResult->getContentMd5() . "\n");
                }
            }
            if ($response->isSetResponseMetadata()) { 
                echo("            ResponseMetadata\n");
                $responseMetadata = $response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId()) {
                    echo("                RequestId\n");
                    echo("                    " . $responseMetadata->getRequestId() . "\n");
                }
            }
            #echo ("        Report Contents\n");
            #echo ("=============================================================================\n");
            #echo (stream_get_contents($request->getReport()) . "\n");

            echo ("=============================================================================\n");
            echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

            $log['status'] = Transfer::$status['success'];
            $log['recordscount'] = count($items);
            $log['response'] = ob_get_clean();
            $this->Transfer->save($log);

            if( count($items) > 0) {
                if($type == 'products') {
                    $all_products = [];
                    foreach($items as $item) {
                        $product = $this->getProduct($item);
                        $all_products = array_merge($all_products, $product);
                    }
                    $result = $this->importProduct($all_products, $integration['Integration']['is_ecommerce'], $transfer_id);
                    $response = [
                        'status'        => 'success',
                        'total_found'   => count($items),
                        'added'         => $result['added'],
                        'updated'       => $result['updated'],
                        'errors_count'  => count($result['errors']),
                        'errors'        => $result['errors'],
                    ];
                } else {
                    $all_orders = [];
                    foreach ($res as $r) {
                        $all_orders[] = $this->getOrder($r);
                    }
                    $result = $this->importOrders($all_orders, $integration['Schannel']['id'], $transfer_id);
                    $response = [
                        'status'        => 'success',
                        'total_found'   => count($res),
                        'added'         => $result['added'],
                        'updated'       => $result['updated'],
                        'errors'        => $result['errors'],
                        'errors_count'  => $result['errors_count']
                    ];
                }
            } else {
                $response = [
                    'status'        => 'success',
                    'total_found'   => 0,
                    'added'         => 0
                ];
            }

        } catch (MarketplaceWebService_Exception $ex) {
            ob_start();
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");

            $log['status'] = Transfer::$status['failed'];
            $log['recordscount'] = 0;
            $log['response'] = ob_get_clean();
            $this->Transfer->save($log);

            $response = [
                'status'        => 'failed',
                'error'         => $log['response']
            ];
        }
        return $response;
    }

    public function test_request($params) {
        $config = array (
           'ServiceURL' => 'https://mws-eu.amazonservices.com/Products/2011-10-01',
           'ProxyHost' => null,
           'ProxyPort' => -1,
           'ProxyUsername' => null,
           'ProxyPassword' => null,
           'MaxErrorRetry' => 3,
        );

        $service = new MarketplaceWebServiceProducts_Client(
                $params->access_key,
                $params->secret_key,
                'MyAWESOMEProduct',
                '0.0.1',
                $config);

        $request = new MarketplaceWebServiceProducts_Model_GetMatchingProductRequest();
        $request->setSellerId($params->amazon_seller_id);
        $request->setMarketplaceId('A1F83G8C2ARO7P');

        $asinList = new MarketplaceWebServiceProducts_Model_ASINListType();
        $asinList->setASIN('B01AC1E87M');

        $request->setASINList($asinList);
        $response = $service->GetMatchingProduct($request);
        
        return $response;
    }

    function getProduct($data) {
        /*$imgs = array();
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
        }*/
        
        $product['Product']['name']                 = $data['item-name'];
        $product['Product']['product_id']           = $data['product-id'];
        $product['Product']['parentid']             = $data['product-id'];
        $product['Product']['description']          = $this->prepare_desc($data['item-description']);
        $product['Product']['group_id']             = 0;
        $product['Product']['uom']                  = ''; // not return any units
        $product['Product']['weight']               = '';
        $product['Product']['width']                = '';
        $product['Product']['height']               = '';
        $product['Product']['depth']                = ''; //$data->dimensions->unit;

        $product['Product']['barcode']              = '';
        $product['Product']['sku']                  = $data['seller-sku'];
        $product['Product']['consumption']          = '';
        $product['Product']['bin']                  = '';
        $product['Product']['value']                = number_format((float)$data['price'], 2, '.', '');
        $product['Product']['imageurl']             = $data['image-url'];
        $product['Product']['pageurl']              = '';
        $product['Product']['color_id']             = '';
        $product['Product']['size_id']              = '';
        $product['Product']['category']             = '';
        $product['Product']['category_id']          = '';
        $product['Product']['publish']              = '';
        $product['Product']['createdinsource']      = $data['open-date'];
        $product['Product']['modifiedinsource']     = $data['open-date'];
        $product['Product']['catalog']              = '';
        $product['Product']['created']              = date('Y-m-d H:i:s');
        $product['Product']['modified']             = date('Y-m-d H:i:s');
        // Question: for some products shopify return negative quantity, what it mean? What we need to do with it?
        $product['Inventory'][0]['quantity']        = abs($data['quantity']);
        $product['Inventory'][0]['user_id']         = $this->Auth->user('id');
        $product['Inventory'][0]['dcop_user_id']    = $this->Auth->user('id');
        $product['Inventory'][0]['warehouse_id']    = $this->Session->read('default_warehouse');
        $prducts[] = $product;

        return $prducts;
    }

    public function getOrder($data) {
        
        /*if(isset($data->shipping_address)) {
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
            $i++;
        }*/
        $order = [];
        return $order;
    }
}