<?php
class LabelsController extends AppController {
     var  $uses = null;
     var  $helpers = array('Pdf');
     
     //No access to controller, no longer relevant                     
     public function beforeFilter() {
       
        //$this->redirect($this->referer());
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
     
     
	  function binslabel($binscount = null) {
		$this->set('binscount',$binscount);
        $this->layout='pdf';       
     }
}
?>
