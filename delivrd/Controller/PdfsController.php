<?php
class PdfsController extends AppController {
     var  $uses = null;
     var  $helpers = array('Pdf');
     
     //No access to controller, no longer relevant                     
    public function beforeFilter() {       
      //  $this->redirect($this->referer());
    }
      
     function index($id = null) {
	 $this->loadModel('Shipment');
	 $this->loadModel('Order');
	 $this->loadModel('OrdersLine');
	
		$shipment = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $id)));
		$order = $this->Order->find('first', array('conditions' => array('Order.id' => $this->shipment['order_id'])));
		$orderslines = $this->OrdersLine->find('all', array('conditions' => array('Order.id' => $this->shipment['order_id'])));
		$this->set('shipment', $shipment);
		$this->set('order', $order);
		$this->set('orderslines', $orderslines);
        $this->layout='pdf';       
     }
	 
	 function shiplabel($id = null) {
	 $this->loadModel('Shipment');
	 $this->loadModel('Order');
	 $this->loadModel('OrdersLine');
	 $this->loadModel('User');
	 $this->loadModel('Product');
	
		$shipment = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $this->request->params['pass'][0])));
		$order = $this->Order->find('first', array('conditions' => array('Order.id' => $shipment['Shipment']['order_id'])));
		$userdetails = $this->User->find('first', array('conditions' => array('User.id' => $this->Auth->user('id'))));
		$orderslines = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.order_id' => $shipment['Shipment']['order_id'])));
		$first_product = $this->Product->find('first', array('conditions' => array('Product.id' => $orderslines['OrdersLine']['product_id'])));
		$this->loadModel('Group');
		$groups = $this->Group->find('first', array('conditions' => array('id' => $first_product['Product']['group_id'])));
		$categoryname = $groups['Group']['name'];
		$this->set(compact('categoryname'));
		$this->set('shipment', $shipment);
		$this->set('order', $order);
		$this->set('orderslines', $orderslines);
		$this->set('userdetails', $userdetails);
		$this->set('first_product', $first_product);
		
		if ($this->Auth->user('msystem_id') == 1)
		{
		$weight_unit = Configure::read('Metric.weight');
		$volume_unit = Configure::read('Metric.volume');
		}
		if ($this->Auth->user('msystem_id') == 2)
		{
		$weight_unit = Configure::read('US.weight');
		$volume_unit = Configure::read('US.volume');
		}

		$this->set(compact('weight_unit', 'volume_unit'));
	
		$this->loadModel('Currency');
		$currencies = $this->Currency->find('first', array('conditions' => array('id' => $this->Auth->user('currency_id'))));
		$currencyname = $currencies['Currency']['name'];
		$this->set(compact('currencyname'));
               
        $this->layout='pdf';       
     }
     
    function shiplabelee($id = null) {
	 	$this->loadModel('Shipment');
	 	$this->loadModel('Order');
	 	$this->loadModel('OrdersLine');
	 	$this->loadModel('User');
	 	$this->loadModel('Product');
	
		$shipment = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $this->request->params['pass'][0])));
		$order = $this->Order->find('first', array('conditions' => array('Order.id' => $shipment['Shipment']['order_id']), 'contain' => array('State','Source','Country','Supplysource','Supplier','Supplier','Address.State','Address.Country','Shipment','OrdersLine')));

		$userdetails = $this->User->find('first', array('conditions' => array('User.id' => $this->Auth->user('id'))));
		$orderslines = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.order_id' => $shipment['Shipment']['order_id']), 'callbacks' => false));
		$first_product = $this->Product->find('first', array('conditions' => array('Product.id' => $orderslines['OrdersLine']['product_id']), 'callbacks' => false));
		$this->loadModel('Group');
		$groups = $this->Group->find('first', array('conditions' => array('id' => $first_product['Product']['group_id'])));

		$this->set('shipment', $shipment);
		$this->set('order', $order);
		$this->set('orderslines', $orderslines);
		$this->set('userdetails', $userdetails);
		$this->set('first_product', $first_product);
		
		if ($this->Auth->user('msystem_id') == 1)
		{
			$weight_unit = Configure::read('Metric.weight');
			$volume_unit = Configure::read('Metric.volume');
		}
		if ($this->Auth->user('msystem_id') == 2)
		{
			$weight_unit = Configure::read('US.weight');
			$volume_unit = Configure::read('US.volume');
		}

		$this->set(compact('weight_unit', 'volume_unit'));
	
		$this->loadModel('Currency');
		$currencies = $this->Currency->find('first', array('conditions' => array('id' => $this->Auth->user('currency_id'))));
		$currencyname = $currencies['Currency']['name'];
		$this->set(compact('currencyname'));

		$this->loadModel('Address');
		$user_address = $this->Address->find('first', ['conditions' => ['Address.user_id' => $this->Auth->user('id')]]);
		//pr($returnaddr);
		//exit;
        $returnaddr['name'] = $this->Auth->user('company');
        $returnaddr['street'] = $user_address['Address']['street']; //$this->Auth->user('street');
        $returnaddr['city'] = $user_address['Address']['city']; //$this->Auth->user('city');
        $returnaddr['country'] = $user_address['Country']['name']; //$this->Auth->user('country');
        //if(!empty($order['Address'])) {
        	$toroder['street'] = (isset($order['Address']['street']) ? $order['Address']['street'] : (isset($order['Order']['ship_to_street']) ? $order['Order']['ship_to_street'] : ""));
        	$toroder['city'] = (isset($order['Address']['city']) ? $order['Address']['city'] : (isset($order['Order']['ship_to_city']) ? $order['Order']['ship_to_city'] : ""));
        	$toroder['country'] = (!empty($order['Address']['Country']) ? $order['Address']['Country']['name'] : (!empty($order['Country']) ? $order['Country']['name'] : ""));
        	$toroder['stateprovince'] = (isset($order['Address']['stateprovince']) ? $order['Address']['stateprovince'] : (isset($order['Order']['ship_to_stateprovince']) ? $order['Order']['ship_to_stateprovince'] : ""));
        	$toroder['zip'] = (isset($order['Address']['zip']) ? $order['Address']['zip'] : (isset($order['Order']['ship_to_zip']) ? $order['Order']['ship_to_zip'] : ""));
        //} 
        // else {
        // 	$toroder['street'] = $order['Order']['ship_to_street'];
        // 	$toroder['city'] = $order['Order']['ship_to_city'];
        // 	$toroder['country'] = (!empty($order['Country']) ? $order['Country']['name'] : '');
        // 	$toroder['stateprovince'] = $order['Order']['ship_to_stateprovince'];
        // 	$toroder['zip'] = $order['Order']['ship_to_zip'];
        // }

        $this->set(compact('returnaddr', 'toroder'));
        $this->layout='pdf';       
     }
     
    function shiplabelland($id = null) {
	 $this->loadModel('Shipment');
	 $this->loadModel('Order');
	 $this->loadModel('OrdersLine');
	 $this->loadModel('User');
	 $this->loadModel('Product');
	
		$shipment = $this->Shipment->find('first', array('conditions' => array('Shipment.id' => $this->request->params['pass'][0])));
		$order = $this->Order->find('first', array('conditions' => array('Order.id' => $shipment['Shipment']['order_id'])));
		$userdetails = $this->User->find('first', array('conditions' => array('User.id' => $this->Auth->user('id'))));
		$orderslines = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.order_id' => $shipment['Shipment']['order_id'])));
		$first_product = $this->Product->find('first', array('conditions' => array('Product.id' => $orderslines['OrdersLine']['product_id'])));
		$this->loadModel('Group');
		$groups = $this->Group->find('first', array('conditions' => array('id' => $first_product['Product']['group_id'])));
		$categoryname = $groups['Group']['name'];
		$this->set(compact('categoryname'));
		$this->set('shipment', $shipment);
		$this->set('order', $order);
		$this->set('orderslines', $orderslines);
		$this->set('userdetails', $userdetails);
		$this->set('first_product', $first_product);
		
		if ($this->Auth->user('msystem_id') == 1)
		{
		$weight_unit = Configure::read('Metric.weight');
		$volume_unit = Configure::read('Metric.volume');
		}
		if ($this->Auth->user('msystem_id') == 2)
		{
		$weight_unit = Configure::read('US.weight');
		$volume_unit = Configure::read('US.volume');
		}

		$this->set(compact('weight_unit', 'volume_unit'));
	
		$this->loadModel('Currency');
		$currencies = $this->Currency->find('first', array('conditions' => array('id' => $this->Auth->user('currency_id'))));
		$currencyname = $currencies['Currency']['name'];
		$this->set(compact('currencyname'));
        $this->layout='pdf';       
     }
	 
	 function productlabel($id = null) {
		$this->loadModel('Product');
		$product = $this->Product->find('first', array('conditions' => array('Product.id' => $this->request->params['pass'][0])));
		$this->set('product',$product);
        $this->layout='pdf';       
     }
     
     function randtrackinglabel($id = null) {
        $this->layout='pdf';       
     }
     
     function pickingslip($id = null) {
		$this->ordersinwave = array();

		$this->loadModel('Wave');
		//Get products qty for entire wave
		$wave = $this->Wave->find('first', array('conditions' => array('Wave.id' => $this->request->params['pass'][0])));
		
		$productqty = array_reduce($wave['OrdersLine'], function($result, $item) {
			if (!isset($result[$item['product_id']])) $result[$item['product_id']] = 0;
				$result[$item['product_id']] += $item['quantity'];
				$this->ordersinwave[] = $item['order_id'];
			return $result;
		}, array());

		$uordersinwave = array_unique($this->ordersinwave);
		//Get packaging materials qty for entire waves
		$this->loadModel('OrdersLine');
		$packlinesdata = $this->OrdersLine->find('all', array('conditions' => array('OrdersLine.order_id' => $uordersinwave,'OrdersLine.type' => 4)));
		foreach($packlinesdata as $packline)
		{
			if (!isset($productqty[$packline['OrdersLine']['product_id']])) $productqty[$packline['OrdersLine']['product_id']] = 0;
				$productqty[$packline['OrdersLine']['product_id']] += $packline['OrdersLine']['quantity'];
		}
		
		
	//$pickqty = array_merge($productqty, $result);
	
		Debugger::dump($productqty);
		
	
		$x=0;
		foreach ($productqty as $key=>$value):
		//Get product data
		$this->loadModel('Product');
		$product = $this->Product->find('first', array('conditions' => array('Product.id' => $key)));
				$picklines[$x]['productname'] = $product['Product']['name'];
				$picklines[$x]['pickquantity'] =$value;
				$picklines[$x]['imageurl'] = $product['Product']['imageurl'];
				$picklines[$x]['bin'] = $product['Product']['bin'];
				$x++;
		endforeach;
//		var_dump($picklines);
//		return;
		$this->set('wave',$wave);
		$this->set('picklines',$picklines);
        $this->layout='pdf';       
     }
	 
	  function binslabel($binscount = null) {
		$this->set('binscount',$binscount);
        $this->layout='pdf';       
     }
}
?>
