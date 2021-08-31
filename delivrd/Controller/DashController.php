<?php

App::uses('AppController', 'Controller');
App::uses('GoogleCharts', 'GoogleCharts.Lib');
App::uses('CakeTime', 'Utility');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class DashController extends AppController {

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array();
    public $components = array('Search.Prg');
    public $helpers = array('GoogleCharts.GoogleCharts');
    public $theme = 'Mtro';

    /**
     * Displays a view
     *
     * @param mixed What page to display
     * @return void
     * @throws NotFoundException When the view file could not be found
     *  or MissingViewException in debug mode.
     */
    public function index() {
        $this->layout = 'mtrd';
        //Set the chart for your view
        $orstep = '';
        $this->loadModel('Product');
        $productscount = $this->Product->find('count',array('conditions' => array('Product.user_id' => $this->Auth->user('id'))));
        if($productscount == 0) {
            $orstep = 'prod';
        } else {
            $this->loadModel('Inventory');
            $inventorycount = $this->Inventory->find('count',array('conditions' => array('Inventory.quantity >' => 0, 'Inventory.user_id' => $this->Auth->user('id'))));
            //If we have all products for this user has 0 stock, guide him to inventory page
            $this->set(compact('productscount'));
            if($inventorycount == 0) {
                $orstep = 'inv';
            } else {
                $orstep = 'hasinv';
                $slowmovers = $this->getslowmovers();
                $this->set(compact('slowmovers'));
                $highestvalues = $this->getshighestvalued();
                $this->set(compact('highestvalues'));
            }
        }
        $this->set(compact('orstep'));
    }


    public function ofindex() { 
        $this->layout = 'mtrdc';
        $orstep = '';
        $this->loadModel('Product');
        $this->loadModel('Inventory');
        $user_id = $this->Auth->user('id');
        
        if($this->Auth->user('is_limited')) {
            if(isset($this->Access->_networks[1]['created_by_user_id'])) {
                $user_id = $this->Access->_networks[1]['created_by_user_id'];
            }
        }
        $productscount = $this->Product->find('count',array('conditions' => array('Product.user_id' => $this->Auth->user('id')), 'recursive' => -1));
        if($productscount == 0) {
            $orstep = 'prod';
        } else {

            $inventorycount = $this->Inventory->find('count',array(
                'conditions' => array(
                    'Inventory.quantity >' => 0, 
                    'Inventory.user_id' => $this->Auth->user('id')
                    ),
                'recursive' => -1
                )
            );
            //If we have all products for this user has 0 stock, guide him to inventory page
            $this->set(compact('productscount'));
            
            if($inventorycount == 0) {
                $orstep = 'inv';
            } else {
                $orstep = 'hasinv';
            }
        }
        
        $this->set(compact('orstep'));
        $topsellers = $this->topsellers();
        $this->set(compact('topsellers'));
        $totalsales = $this->totalsales();
        $this->set(compact('totalsales'));
        $monthschart = $this->hourtohour($totalsales['hourly']);
        $this->set(compact('monthschart'));
        $orderscount = $this->getorderscount();
        $this->set(compact('orderscount'));
        $salesbycity = $this->salesbycity();
        $this->set(compact('salesbycity'));

        $warehouses = $this->Inventory->Warehouse->find('list',array('conditions' => array('Warehouse.status' => 'active', 'Warehouse.user_id' => $this->Auth->user('id'))));
        $unique_pdts = $this->Inventory->find('all', array(
                'conditions' => array(
                    'Product.user_id' => $this->Auth->user('id'),
                    'Inventory.warehouse_id IN' => array_keys($warehouses),
                    'OR' => array(
                        'Product.safety_stock !=' => 0,
                        'Product.reorder_point !='=> 0
                    ),
                ),
                'fields'  => array(
                        'Product.id',
                        'Product.safety_stock',
                        'Product.reorder_point',
                    ),
            'group' => 'Inventory.product_id HAVING Product.safety_stock > sum(Inventory.quantity) OR Product.reorder_point > sum(Inventory.quantity)',
            'recursive' => 1));
        $unique_pdts = count($unique_pdts);
        #$unique_pdts = $this->Inventory->uniqueQuantity();
        $this->set(compact('unique_pdts'));
    }

    public function search() {
        $this->layout = 'mtrd';
        //Set the chart for your view
        $results = array();
        $i = 0;
        // $this->loadModel('Order');
        // $orders = $this->Order->find('all', array('recursive' => 0,'fields' => array('Order.id','Order.created','Order.external_orderid','Order.ordertype_id'),'conditions' => array('Order.external_orderid' => $this->request->query['q'],'Order.user_id' => $this->Auth->user('id'))));
        
        // foreach ($orders as $order): 
        // $results[$i]['id']   = $order['Order']['id'];
        // $results[$i]['res'] = $order['Order']['external_orderid'];
        // $results[$i]['created'] = $order['Order']['created'];
        // if($order['Order']['ordertype_id'] == 1)
        // {
        //  $results[$i]['type'] = "Customer Order";
        //  $results[$i]['url'] = "/orders/viewcord/".$order['Order']['id'];
            
        // }
        // if($order['Order']['ordertype_id'] == 2)
        // {
        //  $results[$i]['type'] = "Repl. Order";
        //  $results[$i]['url'] = "/orders/viewrord/".$order['Order']['id'];
        // }
        // $i++;
        // endforeach;
        // $this->loadModel('Shipment');
        
        // $shipments = $this->Shipment->find('all', array('recursive' => 0,'fields' => array('Shipment.id','Shipment.created','Shipment.tracking_number','Shipment.direction_id'),'conditions' => array('Shipment.tracking_number' => $this->request->query['q'],'Shipment.user_id' => $this->Auth->user('id'))));
        // foreach ($shipments as $shipment):   
        // $results[$i]['id']   = $shipment['Shipment']['id'];
        // $results[$i]['res'] = $shipment['Shipment']['tracking_number'];
        // $results[$i]['created'] = $shipment['Shipment']['created'];
        // if($shipment['Shipment']['direction_id'] == 1)
        // {
        //  $results[$i]['type'] = "Outbound Shipment";
        //  $results[$i]['url'] = "/shipments/view/".$shipment['Shipment']['id'];
            
        // }
        // if($shipment['Shipment']['direction_id'] == 2)
        // {
        //  $results[$i]['type'] = "Inbound Shipment";
        //  $results[$i]['url'] = "/shipments/view/".$shipment['Shipment']['id'];
        // }
        // $i++;
        // endforeach;
        
        $this->loadModel('Product');
        $products = $this->Product->find('all', array('recursive' => 0,'fields' => array('Product.id','Product.created','Product.sku','Product.name'),'conditions' => array('Product.name like "%' . $this->request->query['q'] . '%"','Product.user_id' => $this->Auth->user('id'))));
        
        foreach ($products as $product) {
            $results[$i]['id']  = $product['Product']['name'];
            $results[$i]['res'] = $product['Product']['sku'];
            $results[$i]['created'] = $product['Product']['created'];
            $results[$i]['type'] = "Product";
            $results[$i]['url'] = "/products/view/".$product['Product']['id'];  

            $i++;
        }
        $this->set(compact('results'));
    }


    public function getslowmovers() {
        $inventory_data = array();
        $this->loadModel('Inventory');
        $this->Inventory->recursive = 1;
        $inventory_slow = $this->Inventory->find('all',array(
            'recursive' => 1,
            'contain' => array('Product','Warehouse'),
            'fields' => 
            array('Product.name',
                'Inventory.quantity',
                'Inventory.modified',
                'Warehouse.name',
                'Product.value',
                'Product.id',
                'Product.sku'
                ),
            'order' => array('Inventory.modified' => 'asc'),
            'limit' => 10, 
            'conditions' => array('Inventory.user_id' => $this->Auth->user('id'))
            )
        );
        return $inventory_slow;
    }

    public function getshighestvalued() {
        $highestvaluetemp = array();
        $highestvalue = array();
        $this->loadModel('Inventory');
        $this->Inventory->recursive = 1;        
        $inventory_values = $this->Inventory->find('all',
            array(
                'recursive' => -1,
                'contain' => array('Product','Warehouse'),
                'fields' => 
                array(
                    'Product.name',
                    'Inventory.quantity',
                    'Inventory.modified',
                    'Warehouse.name',
                    'Product.value',
                    'Product.id',
                    'Product.sku'
                ),
                'conditions' => array('Inventory.user_id' => $this->Auth->user('id'))
            )
        );
   
        foreach($inventory_values as $key => $inventory_value){
            $total_values[$key] = $inventory_value['Product']['value'] * $inventory_value['Inventory']['quantity'];
        }
        //Sort by highest value
        arsort($total_values);
        //Get top ten keys
        //$highestvaluekeys = array_slice($total_value, 0, 10);
             
        $x=0;
        foreach($total_values as $key=>$total_value) {
            if($x<10) {
                $highestvalue[$x] = $inventory_values[$key];
            } else {
                break;
            }
            $x++;  
        }
        return $highestvalue;
    }
        
    function aasort (&$array, $key) {
        $sorter=array();
        $ret=array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }
        $array=$ret;
    }

    public function getorderscount() {
        $this->loadModel('Order');
        $corders = $this->Order->find('count', array('recursive' => -1,'conditions' => array('Order.ordertype_id' => 1,'Order.status_id' => 2,'Order.user_id' => $this->Auth->user('id'))));
        //$roders = $this->Order->find('count',array('conditions' => array('Order.ordertype_id' => 2,'Order.status_id' => 2,'Order.user_id' => $this->Auth->user('id'))));
        $this->loadModel('Shipment');
        $oshipments = $this->Shipment->find('count',array('recursive' => -1,'conditions' => array('Shipment.direction_id' => 1,'Shipment.status_id' => 8,'Shipment.user_id' => $this->Auth->user('id'))));
        $this->loadModel('Order');
        $cshipments = $this->Order->find('count',array(
            'recursive' => -1,
            'conditions' => array(
                'Order.ordertype_id' => 2,
                'Order.requested_delivery_date <' => date('Y-m-d', strtotime('-1 days')),
                'Order.status_id' => 2,
                'Order.user_id' => $this->Auth->user('id')
            )
        ));

        $shipments2 = $this->OrdersLine->find('count', [
            #'fields' => ['OrdersLine.order_id'],
            'conditions' => [
                'Order.ordertype_id' => 2,
                'OR' => array('Order.requested_delivery_date >=' => date('Y-m-d', strtotime('-1 days')), 'Order.requested_delivery_date' => null ),
                'Order.status_id' => 2,
                'Order.user_id' => $this->Auth->user('id'),
                'OrderSchedule.delivery_date <' => date('Y-m-d', strtotime('-1 days'))
            ]
        ]);

        $cshipments = $cshipments + $shipments2;
        
        $orderscount = array();
        //Customer orders count
        $orderscount['C'] = $corders;
        //Replenishment orders count
        //$orderscount['R'] = $roders;
        // Outbound shipments ready for pick up
        $orderscount['S'] = $oshipments;
        // Outbound shipments picked up by courier
        $orderscount['P'] = $cshipments;
        return $orderscount;
    }

    public function totalsales() {
        $this->loadModel('OrdersLine');
        $orderslines = $this->OrdersLine->find('all', array(
            'order' => array('OrdersLine.product_id'),
            'fields' => array('OrdersLine.created','OrdersLine.total_line'),
            'recursive' => -1,
            'conditions' => array('OrdersLine.type' => 1,'OrdersLine.user_id' => $this->Auth->user('id'))
        ));
        $salessum = array();
        $mothlysales = array();
        $weeklysales = array();
        $hourlysales = array();
        $salessum['alltime'] = 0;
        $salessum['mtd'] = 0;
        $salessum['ytd'] = 0;
        $salessum['wtd'] = 0;
        $salessum['weekly'] = 0;
        $salessum['monthly'] = 0;
        $salessum['hourly'] = 0;
    
        $currentmonth = date("m");
        $currentyear = date("y");
        $currentweek = date("W");
    
        foreach($orderslines as $ordersline){
            $orderlinemonth = date("m",strtotime($ordersline['OrdersLine']['created']));
            $orderlineyear = date("y",strtotime($ordersline['OrdersLine']['created']));
            $orderlineweek = date("W",strtotime($ordersline['OrdersLine']['created']));
            $orderlinehour = date("H",strtotime($ordersline['OrdersLine']['created']));
            $salessum['alltime'] +=$ordersline['OrdersLine']['total_line'];

            if($orderlinemonth == $currentmonth) {
                $salessum['mtd'] += $ordersline['OrdersLine']['total_line'];
            }
            if($orderlineyear == $currentyear) {
                $salessum['ytd'] += $ordersline['OrdersLine']['total_line'];
            }
            if($orderlineweek == $currentweek) {
                $salessum['wtd'] += $ordersline['OrdersLine']['total_line'];
            }
            if(array_key_exists( $orderlinemonth, $mothlysales)) {
                $mothlysales[$orderlinemonth]  += $ordersline['OrdersLine']['total_line'];
            } else {
                $mothlysales[$orderlinemonth] = $ordersline['OrdersLine']['total_line'];
            }
            
            if(array_key_exists( $orderlineweek, $weeklysales)) {
                $weeklysales[$orderlineweek]  += $ordersline['OrdersLine']['total_line'];
            } else {
                $weeklysales[$orderlineweek] = $ordersline['OrdersLine']['total_line'];
            }

            if(array_key_exists( $orderlinehour, $hourlysales)) {
                $hourlysales[$orderlinehour]  += $ordersline['OrdersLine']['total_line'];
            } else {
                $hourlysales[$orderlinehour] = $ordersline['OrdersLine']['total_line'];
            }
         
        }
        $salessum['monthly'] = $mothlysales;
        $salessum['weekly'] = $weeklysales;
        $salessum['hourly'] = $hourlysales;
        //Debugger::dump($salessum);
        return $salessum;
    }
    
    public function topsellers() {
        $this->loadModel('OrdersLine');
        $orderslines = $this->OrdersLine->find('all', array(
            'order' => array('OrdersLine.product_id'),
            'fields' => array('OrdersLine.product_id','Product.name','OrdersLine.quantity','OrdersLine.total_line'),
            'recursive' => -1,
            'contain' => array('Product'),
            'conditions' => array('OrdersLine.type' => 1,'OrdersLine.user_id' => $this->Auth->user('id')))
        );
        $prodsum =  array();
        
        foreach($orderslines as $ordersline){
            if(!empty($ordersline['Product']['id'])) {
                $curarr['product_id'] = $ordersline['OrdersLine']['product_id'];
                $curarr['product_name'] = $ordersline['Product']['name'];
                $curarr['total_line'] = $ordersline['OrdersLine']['total_line'];
                $curarr['quantity'] = $ordersline['OrdersLine']['quantity'];
                array_push($prodsum,$curarr);
            }
        }
        $topsellerdata = array();
        foreach($prodsum as $row) {
            if(array_key_exists( $row['product_id'], $topsellerdata)) {
                $topsellerdata[$row['product_id']]['total_line'] += $row['total_line'];
                $topsellerdata[$row['product_id']]['quantity'] += $row['quantity'];
            } else {
                $topsellerdata[$row['product_id']] = $row;
            }
        }
        $topsellerdata = Set::sort($topsellerdata, '{n}.total_line', 'desc');
        $toptsellerdata = array_slice($topsellerdata, 0, 10);
        return $toptsellerdata;
    }
    
    public function getDateWiseScore($data) {
        $groups = array();
        $key = 0;
        foreach ($data as $item) {
            $key = $item[0];
            if (!array_key_exists($key, $groups)) {
                $groups[$key] = array(
                    'id' => $item[0],
                    'score' => $item[1],
                    'itemMaxPoint' => $item[2],
                );
            } else {
                $groups[$key][1] = $groups[$key][1] + $item[1];
                $groups[$key][2] = $groups[$key][2] + $item[2];
            }
            $key++;
        }
        return $groups;
    }

    //We attemp to use charts.js to plot our charts, may the force be with us
    public function monthtomonth($monthlysales) {
        $jarrstr = '[';
        $datatochart = array();
        ksort($monthlysales);
        $this->set(compact('monthlysales'));
        
        foreach($monthlysales as $key => $value){
            $dateObj   = DateTime::createFromFormat('m', $key);
            $monthName = $dateObj->format('M'); 
            
            $datatochart[$monthName] = $value;
            $jarrstr = $jarrstr."['".ucwords($monthName)."',".ceil($value)."],";
        }
        $jarrstr = substr($jarrstr, 0, -1);
        $jarrstr = $jarrstr."];";
        if($jarrstr == "];")
            $jarrstr = "[['Jan',0]]";
        //Debugger::dump($jarrstr);
        $this->set(compact('jarrstr'));
    }
        
    public function hourtohour($hourlysales) {
        $jarrstr = '[';
        $datatochart = array();
        ksort($hourlysales);
        $this->set(compact('hourlysales'));
        
        foreach($hourlysales as $key => $value){
            $dateObj   = DateTime::createFromFormat('H', $key);
            $hourName = $dateObj->format('H'); 
            
            $datatochart[$hourName] = $value;
            $jarrstr = $jarrstr."['".ucwords($hourName)."',".ceil($value)."],";
        }
        $jarrstr = substr($jarrstr, 0, -1);
        $jarrstr = $jarrstr."];";
        if($jarrstr == "];")
            $jarrstr = "[['01:00',0]]";
        //Debugger::dump($jarrstr);
        $this->set(compact('jarrstr'));
    }

    // This is an old attemp to use google charts to chart charts, which failed missreably. we keep this for old times sake.
    public function monthtomonthgoog($monthlysales) {
        $chart = new GoogleCharts();
        $chart->type("ColumnChart");  
        //Options array holds all options for Chart API
        $chart->options(array('title' => "Sales By Month",'width' => '530')); 
        $chart->columns(array(
            //Each column key should correspond to a field in your data array
            'month' => array(
                //Tells the chart what type of data this is
                'type' => 'string',     
                //The chart label for this column           
                'label' => 'month'
            ),
            'sales' => array(
                'type' => 'number',
                'label' => 'Sales',
                //Optional NumberFormat pattern
                //'format' => '#,###'
            )
        ));
        //$monthlysales = Set::sort($monthlysales, 'month', 'desc');
        ksort( $monthlysales );
        foreach($monthlysales as $key => $value){
        
            //$countryname = $this->Country->find('list',array('fields' => array('Country.name'),'conditions' => array('Country.id' => $key)));
            //Debugger::dump($countryname);
            $chart->addRow(array('month' => $key, 'sales' => $value));
        }
        return $chart;
    }

    public function salesbycountry() {
        //$chart = new GoogleCharts();

        //$chart->type("PieChart");  
        //Options array holds all options for Chart API
        //$chart->options(array('title' => "Global Sales",'width' => '530')); 
        //$chart->columns(array(
        //Each column key should correspond to a field in your data array
        //     'country' => array(
        //Tells the chart what type of data this is
        //         'type' => 'string',     
        //         //The chart label for this column           
        //         'label' => 'Country'
        //     ),
        //      'sales' => array(
        //         'type' => 'number',
        //         'label' => 'Sales',
        //Optional NumberFormat pattern
        //         'format' => '#,###'
        //    )
        //));
        $salesdata = array();
        $this->loadModel('Order');
        $this->loadModel('Country');
        $orders = $this->Order->find('list', array('fields' => array('Order.country_id'),'conditions' => array('Order.ordertype_id' => 1,'Order.user_id' => $this->Auth->user('id'))));
    
        //$orders = $this->Order->find('count');
        $countbycountry = array_count_values($orders);

        foreach($countbycountry as $key => $value){
            $countryname = $this->Country->find('list',array('fields' => array('Country.name'),'conditions' => array('Country.id' => $key)));
            $salesdata[$countryname[$key]]=$value;
            //Debugger::dump($countryname);
            //$chart->addRow(array('country' => $countryname[$key], 'sales' => ($value)));
        }
        //Debugger::dump($salesdata);
        arsort($salesdata);
        $topsales = array_slice($salesdata, 0, 10);
        //Debugger::dump($salesdata);
        return $topsales;
    }
        
    public function salesbycity() {
    
        $salesdata = array();
        $this->loadModel('Order');
        $orders = $this->Order->find('list', array('recursive' => -1,'fields' => array('Order.ship_to_city'),'conditions' => array('Order.ordertype_id' => 1,'Order.user_id' => $this->Auth->user('id'), 'Order.ship_to_city IS NOT NULL')));
        $new_order = array();
        foreach($orders as $key => $value){ 
            $new_order[$key] = (($value !== '') ? $value : 0);               
        }
        
        $countbycity = array_count_values($new_order);
    
        foreach($countbycity as $key => $value){
            if(isset($ship_to_city))
                $salesdata[$ship_to_city[$key]]=$value;
        }
        arsort($countbycity);
        $topsalescity = array_slice($countbycity, 0, 10);   
        return $topsalescity;
    }
    
    public function shok() {
        $this->layout = 'mtrdgd';
    }
}
