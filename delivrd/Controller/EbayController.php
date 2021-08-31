 <?php

App::uses('AppController', 'Controller');
/**
 * Shipments Controller
 *
 * @property Shipment $Shipment
 * @property PaginatorComponent $Paginator
 */
class EbayController extends AppController {
public $theme = 'Mtro';
/**
 * Components
 *
 * @var array
 */
//	public $components = array('Paginator','EventRegister','Search.Prg');
// $addItem = new eBayGetSellerList();
// $addItem->callEbay('GetSellerTransactions');
// $addItem->printResult('GetSellerTransactions');

// $addItem = new eBayGetSellerList();
// $addItem->callEbay('GetItem');
// $addItem->printResult('GetItem');
 
    private $_siteId = 0;  // default: Germany
    private $_environment = 'production';   // toggle between sandbox and production
    private $_eBayApiVersion = 899;
    private $_call = 'GetSellerTransactions';
    private $_keys = array(
        'sandbox' => array(
            'DEVID'     => '',
            'AppID'     => '',
            'CertID'    => '',
            'UserToken' => 'https://api.sandbox.ebay.com/ws/api.dll', 
            'ServerUrl' => ''
            ),
        'production' => array(
            'DEVID'     => '99c1fe65-3474-4272-99ea-4354017aee80',
            'AppID'     => 'NA6f4040a-1703-46ac-9cce-0a590481fae',
            'CertID'    => '501987d7-aa6f-4e2e-8fe3-10d37f73a2bb',
 //           'UserToken' => 'AgAAAA**AQAAAA**aAAAAA**QCp6VA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AEkYSnDZiDpASdj6x9nY+seQ**zkwCAA**AAMAAA**LhNULDQWsUADZSHd9a29ZJ+xUDMpNvOfNdQTSf0MnQgXKe4DDx2W9pU7csxgwMMpgTXGZtx12D2dif4SIF0WUaJC8W4hbJwiXuoSBtcFQQwOAYMDHzZhIUSlUshAEvGv/fAIFNimIhx36aIcnZXWpHTD/AgHZK9lHfQk5FDPkwWW9tv5xjrV37uH26dZJTKqXDmF2KkRZ5VZzKwQXXqW2F8eTmvUs3ZzlLGpLD8Nwq9o4O6k7bBbjpBDT9DxGU4tF5tIL87eAy66/4obG3yHtV25hA9PPm/7yPFKrwV8fBmYcIqf1O+XOly5qXY/k9Xdun5JIyffp88SvYmOx/ND+yhPsRk9eWq8sn/uJeRLzolQTcH4P3UPsWfZk+vOObFhuTpTdHE888Q0trC2cj9p5DsNo+PZ3j3HfgPX36npG66dUfbrdcatooIwAe2TbZIFaFcpw6WFsmaj9t9SuWzTgRpFTNGEECzR1bqZuJ2DgsBlIQx8Zonm4A5v2LCv1Hq3v3wgIsWz5Dezq8hk+VGCD7eAUsNA9lVVcYH47t0P6gVJxwjPgIbxjIdRRoO+XymY23iQlwWQWaHZm6OpTO0c9TQ/xY4EcPfUzO3IkSoQUB4ljJeWV0Y2WTGPJtmdo2fQnp/RJHgQS4KEX1wriMoq988/4SnhsS1czeYHSR76QwE8gPjJRUv6Mse6WSReRvplOqqX9pucQTe/hs1JeTj0aQk7vDhcL2GNB5KusuPmkA3NfePDYdutZ+aTh4reHCwv',
            'ServerUrl' => 'https://api.ebay.com/ws/api.dll'
        )
    );
    
     //No access to controller, no longer relevant                     
     public function beforeFilter() {
       
        $this->redirect($this->referer());
    }
 
    private function _getRequestBody($ebtoken,$lastebayupdate)
    {
        $apiValues = $this->_keys[$this->_environment];
 
        $dateNow = time();
        //$onedayago = $dateNow - 60*60*24*2;
        
        //if last updated time is not update in user, we get all orders from last day
        $datestart = ($lastebayupdate > 0 ? $lastebayupdate : $onedayago);
       $numofdays = ceil(($dateNow - $lastebayupdate) / (60*60*24));
      //$numofdays = 30;
       // echo $numofdays;
        
 
        $search = array(
            '%%USER_TOKEN%%', '%%EBAY_API_VERSION%%', '%%STARTTIMEFROM%%', '%%STARTTIMETO%%', '%%NUMOFDAYS%%'
        );
        $replace = array(
            $ebtoken, $this->_eBayApiVersion, date('Y-m-d\TH:i:s.000\Z', $datestart), date('Y-m-d\TH:i:s.000\Z', $dateNow),$numofdays 
        );
    
   /*     $search = array(
           '%%USER_TOKEN%%', '%%EBAY_API_VERSION%%',  '%%NUMOFDAYS%%'
        );
        $replace = array(
            $ebtoken, $this->_eBayApiVersion, $numofdays 
        ); */
		
//		if($callname == 'GetItem')
//		{
//			$requestXmlBody = file_get_contents('GetItemSc.xml');
//		} else {
			$requestXmlBody = file_get_contents('GetSellerTransactions.xml');
//		}
        $requestXmlBody = str_replace($search,$replace, $requestXmlBody);
		//echo $requestXmlBody;
        return $requestXmlBody;
    }
 
    public function getEbayOrders()
    {
		$this->layout = 'mtrd';
		// Get ebay current ebay user tokens
		$this->loadModel('User');
		$user = $this->User->find('first',array('conditions' => array('User.id'  => $this->Auth->user('id'))));
		$ebtoken=$user['User']['ebtoken'];
		$lastebayupdate = $user['User']['lastebayupdate'];
	//	Debugger::dump($ebtoken); 
	//	return;
		
		$ordersreturned =0;
		$orderscreated = 0;
		$orderserrors = 0;
        $apiValues = $this->_keys[$this->_environment];
 
        $connection = curl_init();
        curl_setopt($connection, CURLOPT_URL, $apiValues['ServerUrl']);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
 
        $headers = array (
            'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->_eBayApiVersion,
            'X-EBAY-API-DEV-NAME: ' . $apiValues['DEVID'],
            'X-EBAY-API-APP-NAME: ' . $apiValues['AppID'],
            'X-EBAY-API-CERT-NAME: ' . $apiValues['CertID'],
            'X-EBAY-API-CALL-NAME: ' . $this->_call,
//			'X-EBAY-API-CALL-NAME: ' . $callname,
            'X-EBAY-API-SITEID: ' . $this->_siteId,
        );
 
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($connection, CURLOPT_POST, 1);
 
        $requestBody = $this->_getRequestBody($ebtoken,$lastebayupdate);
 
        curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        $responseXml = curl_exec($connection);
        curl_close($connection);
        $this->_responseXml = $responseXml;
      echo $responseXml;
      //Debugger::dump($responseXml); 
      // exit();
        $neworders = $this->createOrders();
		
		//$ut = time();
		// Only update last update timestamp if everything worked, meaning newcount=newcreated
		if($neworders['errors'] == 0)
		{
			$this->loadModel('User');
			$this->User->id = $this->Auth->user('id');
			$this->User->saveField('lastebayupdate', time());
			$ordersreturned = $neworders['newcount'];
			$orderscreated = $neworders['newcreated'];
			$orderserrors = $neworders['errors'];
			$this->set(compact('ordersreturned','orderscreated','orderserrors'));
		}

		
    }
 
    public function createOrders()
    {
		$neworders = array("newcount" => 0,"newcreated" => 0,"errors" => 0);
		// Get  ebay sales channel id of the current user
		$this->loadModel('Schannel'); 
		$this->Schannel->recursive = -1;
		$ebayschannel = $this->Schannel->find('first', array('fields' => array('Schannel.id'),'conditions'=>array('Schannel.user_id' => $this->Auth->user('id'), 'Schannel.name'=>array('ebay','eBay','EBAY'))));
        Debugger::dump($ebayschannel); 
        //Xml string is parsed and creates a DOM Document object
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($this->_responseXml);
 
        //get any error nodes
        $errors = $responseDoc->getElementsByTagName('Errors');
 
        //if there are error nodes
        if($errors->length > 0)
        {
            echo '<P><B>eBay returned the following error(s):</B>';
            //display each error
            //Get error code, ShortMesaage and LongMessage
            $code     = $errors->item(0)->getElementsByTagName('ErrorCode');
            $shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
            $longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
            //Display code and shortmessage
            echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
            //if there is a long message (ie ErrorLevel=1), display it
            if(count($longMsg) > 0) {
                echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
            }
            $neworders['errors'] = 1;
			return $neworders;
        } else { //no errors
			
		 
			
            //get results nodes
            $responses = $responseDoc->getElementsByTagName("GetSellerTransactionsResponse");
            foreach ($responses as $response) {
                $acks = $response->getElementsByTagName("Ack");
                $ack   = $acks->item(0)->nodeValue;
              //  echo "Ack = $ack <BR />\n";   // Success if successful
 
                $totalNumberOfEntries  = $response->getElementsByTagName("TotalNumberOfEntries");
           //   $totalNumberOfEntries = 7;
               $totalNumberOfEntries  = $totalNumberOfEntries->item(0)->nodeValue;
               $actualresultsreturned = $response->getElementsByTagName("ReturnedTransactionCountActual");
               $actualresultsreturned = $actualresultsreturned->item(0)->nodeValue;
               $neworders['newcount'] = $totalNumberOfEntries;
    //            echo "totalNumberOfEntries = $totalNumberOfEntries <BR />\n";
               // var_dump($responses);
               //  return;
				//$sku_data = new Array();
			//var_dump($response);
			//	return;
				$item_data= array();
				$items  = $response->getElementsByTagName("Item");
				
				for($i=0; $i<$actualresultsreturned; $i++) {
					if(isset($items->item($i)->getElementsByTagName('SKU')->item(0)->nodeValue))
					{
						$item_data[$i]['sku'] = $items->item($i)->getElementsByTagName('SKU')->item(0)->nodeValue;
					} else {
						$item_data[$i]['sku'] = '';						
					}
						$item_data[$i]['title'] = $items->item($i)->getElementsByTagName('Title')->item(0)->nodeValue;
				}
        
		
                $transactions  = $response->getElementsByTagName("Transaction");
				
			//	var_dump($transactions);
                for($i=0; $i<$actualresultsreturned; $i++) {
	//				echo $i;
                     $streetstr = '';            
                    $customer_name = $transactions->item($i)->getElementsByTagName('Name')->item(0)->nodeValue;
                    if($transactions->item($i)->getElementsByTagName('Street')->length > 0)
                    {
						$street = $transactions->item($i)->getElementsByTagName('Street')->item(0)->nodeValue;
						$streetstr = $street.",";
					}
                    $street1 = $transactions->item($i)->getElementsByTagName('Street1')->item(0)->nodeValue;
                    $streetstr=$streetstr.$street1;
                    if($transactions->item($i)->getElementsByTagName('Street2')->length >0)
                    {
						$street2 = $transactions->item($i)->getElementsByTagName('Street2')->item(0)->nodeValue; 
						$streetstr=$streetstr.",".$street2;						
					}
					
					//$street2 = $items->item($i)->getElementsByTagName('Street2')->item(0)->nodeValue; 
					//	echo "out of if".$street2;
                    $city = $transactions->item($i)->getElementsByTagName('CityName')->item(0)->nodeValue; 
                    $state = $transactions->item($i)->getElementsByTagName('StateOrProvince')->item(0)->nodeValue; 
                    $country = $transactions->item($i)->getElementsByTagName('Country')->item(0)->nodeValue; 
                    $zip_code = $transactions->item($i)->getElementsByTagName('PostalCode')->item(0)->nodeValue;
                    $qty = $transactions->item($i)->getElementsByTagName('QuantityPurchased')->item(0)->nodeValue;
                    $ship_costs = $transactions->item($i)->getElementsByTagName('ActualShippingCost')->item(0)->nodeValue;
                    $price = $transactions->item($i)->getElementsByTagName('TransactionPrice')->item(0)->nodeValue;
                    $refernceorder = $transactions->item($i)->getElementsByTagName('OrderLineItemID')->item(0)->nodeValue;
                    $productname = $transactions->item($i)->getElementsByTagName('Title')->item(0)->nodeValue;
                    //$firstname = $transactions->item($i)->getElementsByTagName('UserFirstName')->item(0)->nodeValue;
                    //$lastname = $transactions->item($i)->getElementsByTagName('UserLastName')->item(0)->nodeValue;
                    $shippingcosts = $transactions->item($i)->getElementsByTagName('ShippingServiceCost')->item(0)->nodeValue;
                   
                 //   $sku = $items->item($i)->getElementsByTagName('SKU')->item(0)->nodeValue;
                 
					
                //Concat street address
               // $streetstr = '';
               // if(isset($street))
				//	$streetstr = $street." ".$street1;
				//if(isset($street2))
				//	$streetstr = $streetstr." ".$street1." ".$street2;
				//if(!isset($street2) && !isset($street))
				//	$streetstr = $street1;
					
					// Get SKU and Title from Item node
					$sku = $item_data[$i]['sku'];
					$title = $item_data[$i]['title'];
					
					//Get country id from code
					$this->loadModel('Country'); 
					$current_country = $this->Country->find('first', array('conditions' => array('Country.code'  => $country)));
					if($current_country['Country']['code'] == 'US')
					{
						$this->loadModel('State');
						//echo "state code is ".$state; 
						$current_state = $this->State->find('first', array('conditions' => array('State.code'  => $state)));
						$state_id = $current_state['State']['id'];
					} else {
						$state_id =1;
					
						//for non-usa countries, sometime state element is filled with some data, we append it to the city field
					//	if(isset($state))
					//		{
					//		$city = $city.",".$state;
					//		}
					}
				                          
                   // $priceInEUR = $items->item($i)->getElementsByTagName('ConvertedCurrentPrice')->item(0)->nodeValue;
                    $status = $transactions->item($i)->getElementsByTagName('ListingStatus')->item(0)->nodeValue;
                  //  $title = $items->item($i)->getElementsByTagName('Title')->item(0)->nodeValue;
                  //  $watchCount = $items->item($i)->getElementsByTagName('WatchCount')->item(0)->nodeValue;

                $this->loadModel('Order'); 
                
                // Check if order does not already exist
                $current_order = $this->Order->find('first', array('conditions' => array('Order.external_orderid'  => $refernceorder)));
                //only if we do not have another order with same reference order id, we continue to create a new one
                //Get ebay sales channel
                
				
                if(empty($current_order))
                {	  
                $this->Order->create();
				$this->request->data('Order.dcop_user_id',$this->Auth->user('id'));
				$this->request->data('Order.user_id',$this->Auth->user('id'));
				$this->request->data('Order.status_id',14);
				$this->request->data('Order.interface',1);
				$this->request->data('Order.ordertype_id',1);
				$this->request->data('Order.ship_to_customerid',ucwords($customer_name));
				$this->request->data('Order.ship_to_street',ucfirst($streetstr));
				$this->request->data('Order.ship_to_city',ucwords($city));
				$this->request->data('Order.ship_to_zip',$zip_code);
				$this->request->data('Order.state_id',$state_id);
				$this->request->data('Order.ship_to_stateprovince',$state);
				$this->request->data('Order.country_id',$current_country['Country']['id']);
				$this->request->data('Order.schannel_id',$ebayschannel['Schannel']['id']);
				$this->request->data('Order.external_orderid',$refernceorder);
				$this->request->data('Order.shipping_costs',$shippingcosts);
				$this->request->data('Order.requested_delivery_date');
							
				
			if ($this->Order->save($this->request->data)) {		
			//	$this->EventRegister->addEvent(2,1,$this->Auth->user('id'),$this->Order->id);
			//	$this->Session->setFlash(__('New order has been created, number %s',$this->Order->id));
				// get product by sku
				
				$this->loadModel('Product');
				$product = $this->Product->find('first', array('conditions' => array('Product.sku'  => $sku)));	
				 
				if(empty($product))
				{
					$product_id = 0;
					$comments = $title;
					$sku = '';
					$status_id = 1;
				} else {
					$product_id = $product['Product']['id'];
					$comments = '';
					$status_id = 1;
				}
//				Debugger::dump($this->Order->id); 
				$this->loadModel('OrdersLine');
			$data = array(
			'OrdersLine' => array(
				'order_id' => $this->Order->id,
				'line_number' => 10,
				'type' => 1,
				'product_id'  => $product_id,
				'warehouse_id'  => $this->Session->read('default_warehouse'),
				'quantity' => $qty,
				'sentqty' => 0,
				'unit_price' => $price,
				'total_line' => $price * $qty,
				'foc' => '',
				'return' => '',
				'sku' => $sku, 
				'status_id' => $status_id,
				'user_id' => $this->Auth->user('id'),
				'comments' => $comments
			)
			);

			// prepare the model for adding a new entry
		$this->OrdersLine->create();
		// save the data
		// $this->OrdersLine->save($data);
		if ($this->OrdersLine->save($data)) {
	//			$this->EventRegister->addEvent(3,1,$this->Auth->user('id'),$this->request->query['lineid']);
	// Add packaging material only if we could match a product to the sku
	if($product_id != 0)
	{
		$this->loadModel('Product');
			$packmaterial = $this->Product->find('first', array('conditions' => array('Product.id' => $product['Product']['packaging_material_id'])));
			if(!empty($packmaterial))
			{
			if($packmaterial['Product']['consumption'] == true )
			{
			$datapack = array(
			'OrdersLine' => array(
				'order_id' => $this->Order->id,
				'line_number' => 9999,
				'type' => 4,
				'product_id'  => $packmaterial['Product']['id'],
				'warehouse_id'  => $this->Session->read('default_warehouse'),
				'quantity' => 1,
				'unit_price' => $packmaterial['Product']['value'],
				'total_line' => $packmaterial['Product']['value'] * 1,
				'foc' => '',
				'user_id' => $this->Auth->user('id'),
				'status_id' => 1
			)
			);
		// prepare the model for adding a new entry
		$this->OrdersLine->create();
		// save the data
		$this->OrdersLine->save($datapack);
	}
}
}
}
		$neworders['newcreated'] += 1;
	   } else {
			//Order which does not exist in dcnet could not be created from some reason
			$neworders['errors'] += 1;
	   }
                    
                 //   echo "title = $title <BR />\n";
                //    echo "watchCount = $watchCount <BR />\n";
                }
               }
            }
            
            return $neworders;
        }
       //   return $this->redirect(array('controller' => 'orders', 'action' => 'index',1));
    }
    public function beforeRender()
    {
            if($this->Auth->user('is_admin') != 1)
                 return $this->redirect('/');
        }
     
}
