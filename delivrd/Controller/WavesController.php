<?php
App::uses('AppController', 'Controller');
/**
 * Waves Controller
 *
 * @property Wave $Wave
 * @property PaginatorComponent $Paginator
 */
class WavesController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator','EventRegister', 'Access', 'Cookie');
    public $theme = 'Mtro';
    public $types = [1 => 'S.O.', 2 => 'P.O.'];

    var $helpers = array('Html','Form','Session');

    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    //No access to waves controller, will reactivate sometime in the future
    public function beforeFilter() {
        parent::beforeFilter();
    }

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->layout = 'mtrd';

        // Remove not own line waves
        $this->loadModel('OrdersLine');
        $this->loadModel('OrderslinesWave');
        $broken_waves = $this->OrderslinesWave->find('all', array(
            'conditions' => array(
                'Wave.user_id' => $this->Auth->user('id'),
                'OrdersLine.user_id !=' => $this->Auth->user('id')
            )
        ));
        if($broken_waves) {
            foreach ($broken_waves as $wave) {
                // Delete WaveLine
                $this->OrderslinesWave->delete($wave['OrderslinesWave']['id'], false);

                // Return OrderLine status to 2
                $this->OrdersLine->id = $wave['OrdersLine']['id'];
                $this->OrdersLine->saveField('status_id', 2);
            }

            // check for empty waves
            $empty_waves = $this->Wave->find('all', array(
                'conditions' => array(
                    'Wave.user_id' => $this->Auth->user('id')
                ),
                'contain' => array('OrdersLine' => array('fields' => array('OrdersLine.id'))),
                'fields' => array('Wave.id')
            ));
            foreach ($empty_waves as $val) {
                if(empty($val['OrdersLine'])) {
                    $this->Wave->delete($val['Wave']['id'], false);
                }
            }
        }
        // End remove not own waves

        $limit = $this->Auth->user('list_limit');
        $this->Paginator->settings = array(
            'limit' => $limit,'order' => array('Wave.modified' => 'DESC'),
            'contain' => array('OrdersLine', 'Schannel', 'Warehouse', 'Status', 'Packstation', 'Courier', 'User'), //OrdersLine
        );
        if(!empty($this->request->data['message']) && $this->request->data['message'] == 1) {
          $this->Cookie->write('message', 1);
        }
        $popup = $this->Cookie->read('message');
        $conditions['Wave.user_id'] = $this->Auth->user('id');

        if (isset($this->request->data['Wave']['status_id']) && $this->request->data['Wave']['status_id'] != null) {
            $conditions['Wave.status_id'] =  $this->request->data['Wave']['status_id'];
        }

        if (!empty($this->request->data['Wave']['searchby'])) {
            $this->loadModel('Order');
            $order = $this->Order->find('list', array(
                'conditions' => array('Order.external_orderid' => $this->request->data['Wave']['searchby'])
            ));

            $this->loadModel('OrderslinesWave');
            $waves = $this->OrderslinesWave->find('all', array(
                'conditions' => array('OrdersLine.order_id' => (!empty($order) ? $order : $this->request->data['Wave']['searchby'])),  
            ));
            
            $wave = array();
            foreach($waves as $waveid) {
                $wave = $waveid['OrderslinesWave']['wave_id'];
            }

            $conditions['Wave.id'] =  $wave;
        }
        if (isset($this->request->data['Wave']['warehouse_id']) && $this->request->data['Wave']['warehouse_id'] != null) {
            $conditions['Wave.location_id'] =  $this->request->data['Wave']['warehouse_id'];
        }
        $this->Wave->recursive = 0;
        //pr($this->Paginator->paginate($conditions));
        //exit;

        $this->set('waves', $this->Paginator->paginate($conditions));
        $statuses = $this->Wave->Status->find('list');
        $warehouses = $this->Wave->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'))));
        $this->countProduct();
        $this->set(compact('statuses','warehouses', 'popup'));
    }

    public function countProduct() {
        $options = array('Wave.user_id' => $this->Auth->user('id'));
        $this->Session->write('wavecount', $this->Wave->find('count', array('conditions' => $options)));
    }

    /**
     * add address method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function editSettings() {
        $this->layout = false;
        $this->loadModel('User');
        $invAlert = $this->User->invAlerts;
        $pickbyorder = $this->User->pickOptions;
        $batch = $this->User->batchOptions;
        $currencies = $this->User->Currency->find('list');
        $timezone = $this->User->Zone->find('list');
        $msystems = $this->User->Msystem->find('list');
        $options = array('conditions' => array('User.id' => $this->Auth->user('id')),'contain' => array('Zone'));
        $this->request->data = $this->User->find('first', $options);

        $this->set(compact('invAlert','pickbyorder','batch','currencies','timezone','msystems'));
    }

    /**
     * saveAddress method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function saveAddress($id = null) {
        if ($this->request->is('ajax')) { 
            $this->loadModel('User');
            if(!empty($this->request->data)) {
                $this->request->data['User']['id'] = $id;
                if ($this->User->saveAll($this->request->data)) {
                    $response['status'] = true;
                    $response['message'] = 'Settings has been saved.'; 
                } else {
                   $response['status'] = false;
                   $User = $this->User->invalidFields();
                   $response['data']=compact('User');
                   $response['message']='Settings could not be saved. Please, try again.';
                }
                echo json_encode($response);
                die;
            }
        } 
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        $this->layout = 'mtrd';
        $this->Paginator->settings = array(
            'limit' => 10
        );
        if (!$this->Wave->exists($id)) {
            throw new NotFoundException(__('Invalid wave'));
        }

        $conditions = array('Wave.id' => $id, 'user_id' => $this->Auth->user('id'));
        $wave = $this->Wave->find('first', array(
            'conditions' => $conditions,
            'contain' => array(
                'OrdersLine' => array('conditions' => array('OrdersLine.user_id' => $this->Auth->user('id'))),
                'OrdersLine.Product'
            )
        ));

        $orderlinesarr = array();
        foreach ($wave['OrdersLine'] as $key=>$orderline) {
            array_push($orderlinesarr,$orderline);
        }
        
        $this->set('orderslines', $orderlinesarr);
        $options = array('conditions' => array('Wave.' . $this->Wave->primaryKey => $id), 'user_id' => $this->Auth->user('id'));
        $this->set('wave', $wave);
    }

    public function checksku(){
        if ($this->request->is(array('post','put'))) {
            if((!empty($this->request->data['Wave']['bin']) && (empty($this->request->data['Wave']['sku']))))
            {
                $product_id=$this->getproductbin($this->request->data);
                if($product_id == 0){
                    $status = false;
                    $message = 'Please fill correct bin';
                }
                else{
                    $status = 'undefined';
                    $message = 'Your bin is correct';
                }
            }
            else if(!empty($this->request->data['Wave']['scan']))
            {
                $product_id=$this->getproductbin($this->request->data);
                if($product_id == 0){
                    $status = false;
                    $message = 'Please fill correct bin';
                }
                else{
                    $status = true;
                    $message = 'Your bin is correct';
                }
            }
            else if(!empty($this->request->data['Wave']['sku']))
            {
                $product_id=$this->getproductid($this->request->data);

                if($product_id == 0){
                    $status = false;
                    $message = 'Please fill correct sku or EAN';
                }
                else{
                    $status = true;
                    $message = 'Your sku is correct';
                }
            }
            else{
                $status = true;
                $message = '';
            }
        }

        echo json_encode(array(
            'status' => $status,
            'message' => $message,
        ));
        die;
    }

    public function deleteorder($id = null){
        if($this->request->is('post')) {
            $this->loadModel('OrdersLine');
         if(!empty($this->request->data['Orderline']['id']))
         {
            $ids = explode(',', $this->request->data['Orderline']['id']);
            foreach($ids as $id){

                $this->OrdersLine->id = $id;
                if ($this->OrdersLine->delete()) {
                    $this->Session->setFlash(__('Selected Order line are deleted.'), 'admin/success', array());

                } else {
                    $this->Session->setFlash(__('Order line could not be deleted. Please, try again.'), 'admin/danger', array());
                }
            }

         }
        }
        return $this->redirect($this->referer());

    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $this->layout = 'mtrd';
        $title = 'Add New Wave';

        $this->loadModel('Order');

        if ($this->request->is('post')) {
            $maxlines = 9999;
            if($this->request->data['Wave']['maxlines'] > 0)
            {
                $maxlines = $this->request->data['Wave']['maxlines'];
            }

            if($this->request->data['Wave']['schannel_id'] == '') {
                $options = array('conditions' => array('OrdersLine.type' => 1, 'OrdersLine.status_id' => 2, 'OrdersLine.sentqty' => 0, 'OrdersLine.user_id' => $this->Auth->user('id')), 'limit' => $maxlines);
            } else {
                $options = array('conditions' => array('OrdersLine.type' => 1,'OrdersLine.status_id' => 2,'OrdersLine.sentqty' => 0, 'Order.schannel_id' => $this->request->data['Wave']['schannel_id'], 'OrdersLine.user_id' => $this->Auth->user('id')),'limit' => $maxlines);
            }

            $wavelines = $this->Wave->OrdersLine->find('all', $options);

            if(!empty($wavelines))
            {
                $this->Wave->create();
                $this->request->data('Wave.user_id',$this->Auth->user('id'));
                $this->request->data('Wave.packstation_id',$this->Session->read('default_packstation'));
                $this->request->data('Wave.numberoflines',sizeof($wavelines));
                $this->request->data('Wave.linespacked',0);
                $this->request->data('Wave.status_id',19);

                if ($this->Wave->save($this->request->data)) {
                    $orders = [];
                    foreach ($wavelines as $waveline) {
                        $this->Wave->OrderslinesWave->saveAll(array("OrderslinesWave"=>array("ordersline_id"=> $waveline['OrdersLine']['id'],"wave_id"=>$this->Wave->id),array('deep' => true)));
                        $this->Wave->OrdersLine->id = $waveline['OrdersLine']['id'];
                        $this->Wave->OrdersLine->saveField('status_id', 17);
                        $orders[] = $waveline['OrdersLine']['order_id'];
                    }

                    $orders = array_unique($orders);
                    foreach ($orders as $order) {
                        $this->Order->id = $order;
                        //$this->Order->saveField('status_id', 2); // Set Released
                        $this->Order->saveField('status_id', 60); // Set In Wave
                    }

                    $this->Session->setFlash(__('Wave no. %s has been created.',$this->Wave->id), 'admin/success', array());
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('The Wave could not be saved. Please, try again.'), 'admin/danger', array());
                }
            } else {
                $this->Session->setFlash(__('No order lines were found for this wave. Make sure released customer orders for the selected sales channel exists.'), 'admin/danger', array());
            }
        }

        $couriers = $this->Wave->Courier->find('list', array('conditions' => array('Courier.user_id' => $this->Auth->user('id'))));
        $this->set(compact('couriers'));

        $this->loadModel('Order');
        $schannels = $this->Order->Schannel->find('list', array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));

        $this->loadModel('Resource');
        $resources = $this->Resource->find('list', array('conditions' => array('Resource.user_id' => $this->Session->read('Auth.User.id'))));

        $this->loadModel('Product');
        $products = $this->Product->find('list', array('conditions' => array('Product.consumption' => 1, 'Product.user_id' => $this->Auth->user('id'))));

        $locations = $this->Wave->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'))));

        $this->loadModel('Country');
        $countries = $this->Country->find('list');

        $type = array('1' => 'Pick', '2' => 'Pick & Pack');

        $this->set(compact('schannels', 'resources', 'products', 'locations', 'countries', 'type', 'title'));

    }

    public function createWave() {
        $this->layout = false;
        $this->loadModel('Order');
        
        if ($this->request->is(array('post', 'put'))) {
            $ids = explode(',', $this->request->data['Wave']['order_id']);

            $warehouses = $this->Access->getLocations('S.O.');
            $warehouses_list = [];
            $other_loc = []; // Location allowed by other users
            $my_loc = [];
            foreach ($warehouses as $net => $loc) {
                foreach ($loc as $key => $val) {
                    $loc_ids[] = $key;
                    if($net !=  'My Locations') {
                        $other_loc[] = $key;
                        $warehouses_list[$key] = $net .' <i class="fa fa-angle-right"></i> '. $val;
                    } else {
                        $my_loc[] = $key;
                        $warehouses_list[$key] = $val;
                    }
                }
            }

            // For now only own orders
            $options = array(
                'conditions' => array(
                    'OrdersLine.order_id' => $ids,
                    'OrdersLine.type' => 1,
                    'OrdersLine.status_id IN' => [2, 1],
                    'OrdersLine.sentqty' => 0,
                    'OrdersLine.user_id' => $this->Auth->user('id'),
                    // To be sure that we create wace only for allowed lines
                    /*'OR' => array(
                        'OrdersLine.user_id' => $this->Auth->user('id'),
                        'OrdersLine.warehouse_id' => $other_loc
                    )*/
                ),
                'recursive' => -1
            );
            $waveLines = $this->Wave->OrdersLine->find('all', $options);

            if(!empty($waveLines)) {

                $this->Wave->create();
                $data['Wave']['user_id'] = $this->Auth->user('id');
                $data['Wave']['courier_id'] = !empty($this->request->data['Wave']['courier_id']) ? $this->request->data['Wave']['courier_id'] : $this->Session->read('default_packstation');
                $data['Wave']['resource_id'] = $this->request->data['Wave']['resource_id'];
                $data['Wave']['linespacked'] = 0;
                $data['Wave']['type'] = 1;
                $data['Wave']['status_id'] = 19;

                if ($this->Wave->save($data)) {
                    $orders = [];
                    foreach ($waveLines as $waveline) {
                        $this->Wave->OrderslinesWave->saveAll(array("OrderslinesWave"=>array("ordersline_id"=> $waveline['OrdersLine']['id'],"wave_id"=>$this->Wave->id),array('deep' => true)));
                        $this->Wave->OrdersLine->id = $waveline['OrdersLine']['id'];
                        $this->Wave->OrdersLine->saveField('status_id', 17);

                        $orders[] = $waveline['OrdersLine']['order_id'];
                    }
                    
                    $orders = array_unique($orders);
                    foreach ($orders as $order) {
                        $this->Order->id = $order;
                        //$this->Order->saveField('status_id', 2); // Set Released
                        $this->Order->saveField('status_id', 60); // Set In Wave
                    }
                    $status = 'success';
                    $message = 'Wave no. '. $this->Wave->id . ' has been created.';
                } else {
                    $status = 'error';
                    $message = 'The Wave could not be saved. Please, try again.';
                }
            } else {
                $status = 'error';
                $message = 'Selected orders can\'t be added in Wave.';
            }
            echo json_encode(array(
                'status' => $status,
                'message' => $message,
            ));
            die;
        }
        /*$this->loadModel('Resource');
        $resources = $this->Resource->find('list', array('conditions' => array('Resource.user_id' => $this->Session->read('Auth.User.id'))));
        $couriers = $this->Wave->Courier->find('list', array('conditions' => array('Courier.user_id' => $this->Auth->user('id'))));*/

        $couriers = $this->Access->couriersList('S.O.');
        $resources = $this->Access->resourcesList('S.O.');

        $this->set(compact('couriers','resources', 'orders_released', 'orders_draft', 'ids'));
    }

    public function release($id = null) {

        $wave = $this->Wave->find('first', array('conditions' => array('Wave.id' => $id, 'Wave.user_id' => $this->Auth->user('id'))));

        if(empty($wave)) {
            $this->Session->setFlash(__('Wave number %s Does no exist',$id), 'admin/danger', array());
        }
    
        if(empty($wave['OrdersLine'])) {
            $this->Session->setFlash(__('Wave number %s could not be released as it has no order lines',$id), 'admin/danger', array());
            return $this->redirect(array('action' => 'index'));
        }

        $this->Wave->id = $id;
        $this->Wave->saveField('status_id', 20);
        $this->Session->setFlash(__('Wave number %s status set to Released',$id), 'admin/success', array());
        return $this->redirect(array('action' => 'index'));
    }


    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $this->layout = 'mtrd';
        $title = 'Edit Wave';
        $options = array('conditions' => array('Wave.' . $this->Wave->primaryKey => $id), 'contain' => array('OrdersLine'));
        $wave_data = $this->Wave->find('first', $options);

        if ($this->request->is(array('post', 'put'))) {
            $this->request->data('Wave.id',$id);
            $this->request->data('Wave.user_id',$this->Auth->user('id'));
            $this->request->data('Wave.numberoflines',$wave_data['Wave']['numberoflines']);
            $this->request->data('Wave.linespacked',0);
            $this->request->data('Wave.status_id',19);
            if ($this->Wave->save($this->request->data)) {
                $this->Session->setFlash(__('The Wave has been saved.'), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Wave could not be saved. Please, try again.'), 'admin/danger', array());
            }
        } else {
            $options = array('conditions' => array('Wave.' . $this->Wave->primaryKey => $id), 'contain' => array('OrdersLine'));
            $this->request->data = $this->Wave->find('first', $options);            
        }
        $options = array('conditions' => array('Wave.id' => $id), 'contain' => array('OrdersLine'));
        $wavedata = $this->Wave->find('first', $options);

            $this->set('orderslines', $wavedata['OrdersLine'],$this->Paginator->paginate());
            $this->Session->delete('nextline');
            $this->Session->delete('lasttrackingnumber');
            $this->Session->delete('trackingnumber');
            $this->Session->delete('lefttopick');
            $this->Session->delete('lastorder');
            $this->Session->delete('orderlinescount');
            $this->Session->delete('curweight');

        $wave = $this->request->data;
        $product = $this->Wave->OrdersLine->Product->find('list');
        $couriers = $this->Wave->Courier->find('list', array('conditions' => array('Courier.user_id' => $this->Auth->user('id'))));

        $this->loadModel('Order');
        $schannels = $this->Order->Schannel->find('list', array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));

        $this->loadModel('Resource');
        $resources = $this->Resource->find('list', array('conditions' => array()));

        $this->loadModel('Product');
        $products = $this->Product->find('list', array('conditions' => array('Product.consumption' => 1, 'Product.user_id' => $this->Auth->user('id'))));

        $locations = $this->Wave->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'))));

        $this->loadModel('Country');
        $countries = $this->Country->find('list');
        $type = array('1' => 'Pick', '2' => 'Pick & Pack');

        $this->set(compact('couriers', 'schannels', 'resources', 'products', 'locations', 'id', 'countries', 'type', 'title'));
        $this->set(compact('products'));
        $this->set(compact('wave'));
        $this->render('add');
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->Wave->id = $id;
        if (!$this->Wave->exists()) {
            throw new NotFoundException(__('Invalid wave'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Wave->delete()) {
            $this->Session->setFlash(__('The Wave has been deleted.'), 'admin/success', array());
        } else {
            $this->Session->setFlash(__('The Wave could not be deleted. Please, try again.'), 'admin/danger', array());
        }
        return $this->redirect(array('action' => 'index'));
    }




    public function packwave($id = null) {
        $this->layout = 'mtrd';
        $this->isserial = false;
        $this->verify = false;
            if (!$this->Wave->exists($id)) {
                throw new NotFoundException(__('Invalid wave'));
            }

            if ($this->request->is(array('post', 'put'))) {

            //Check if scanned barcode is of the product exists and is the order line product
            //  Only if we have more piece to pack for this order line, we verify scanned barcode is of order line

                if($this->Session->read('lefttopick') > 0)
                {
                    $product_id=$this->getproductid($this->request->data);
                }

                //if user wants to skip line due to lack of available stock, he can enter XXXXXX in tracking no. and product barcode
                if(isset($this->request->data['Wave']['trackingnumber']) && $this->request->data['Wave']['trackingnumber'] == 'XXXXXX' AND $this->request->data['Wave']['productscan'] == 'XXXXXX')
                {
                    $this->deletewavelines($id,$this->request->data['Wave']['lineid']);
                }
                //all pieces of order line packed and scanned shipment == current shipment
                //  if($this->Session->read('lefttopick') == 0 && $this->Session->read('sameshipment') == 0)
                if($this->Session->read('lefttopick') == 0)
                {

                    if($this->request->data['Wave']['trackingnumber'] == $this->Session->read('trackingnumber'))
                    {
                        //  echo "we're here";
                        $this->updateshipmentweight($this->request->data['Wave']['trackingnumber'],$this->request->data['Wave']['weight'],$this->request->data['Wave']['lineid'],$this->request->data['Wave']['ordernumber']);
                        $this->Session->write('curweight',$this->request->data['Wave']['weight']);

                    } else {
                        $this->Session->setFlash(__('Scanned shipment number is incorrect.'), 'admin/danger', array());
                    }
                }

                //if we performed weighing, lefttopick =-1, change order line status
                if($this->Session->read('lefttopick') == -1  )
                {
                    if($this->Session->read('sameshipment') == 0)
                    {
                        $this->Session->write('lasttrackingnumber', $this->Session->read('trackingnumber'));
                        $this->Session->write('trackingnumber', 'N/A');
                    }
                    $this->updateorderlines($this->request->data['Wave']['lineid'],$id);
                }
                //we go here only when we have more pieces to scan for current order line
                //  Check if scanned barcode/sku eixsts, and that it belongs to the current wave line and that the line is not fully packed
                if(isset($product_id) && ($product_id != 0) && ($product_id == $this->request->data['Wave']['productid']) && ($this->Session->read('lefttopick') != 0))
                {

                    $shipmentreturn = $this->Session->read('trackingnumber');

                    //echo "we got game ".$this->Session->read('remainingorderlines')." and ".$this->Session->read('orderlinescount');
                        //if this line has quantity 1, or order qty is greater than one but this is the first scan of the line, chekc shipment
                    if(($this->Session->read('lineqty') == 1 && $this->Session->read('orderlinescount') == 1) || ($this->Session->read('lineqty') > 0 && $this->Session->read('lefttopick') == $this->Session->read('lineqty') && $this->Session->read('remainingorderlines') == $this->Session->read('orderlinescount')))
                    {

                        // We create new shipment for order line with qty = 1 or for qty > 1 and no qty packed yet

                        //if($this->Session->read('sameshipment') == 0 || $this->Session->read('trackingnumber') == null )
                        $this->Session->write('curweight',0);
                        $shipmentreturn = $this->singleshipment($this->request->data);
                    } else {
                        // if we have a line with qty > 1, we verify that scanned tracking number is same as current tracking number
                        if($this->Session->read('sameshipment') == 0 || $this->Session->read('trackingnumber') == null)
                            $shipmentreturn = $this->multishipment($this->request->data);
                    }


                    // If shipment is OK, either created or already existed, we do some updating
                    if(strlen($shipmentreturn) > 3)
                    {

                        $this->updateorderstatus($this->request->data['Wave']['ordernumber']);
                        $this->updateorderlines($this->request->data['Wave']['lineid'],$id);
                        $this->updateinventory($this->request->data['Wave']['productid']);

                        if($this->isserial)
                            $this->removeserial($this->request->data);

                    //return $this->redirect(array('action' => 'packwave', $id));
                    } else {
                        $this->Session->setFlash(__('Shipment number %s already exists',$this->request->data['Wave']['trackingnumber']), 'admin/danger', array());
                    }
                } else {
                    if(isset($product_id) && $product_id == $this->request->data['Wave']['productid'])
                    $this->Session->setFlash(__('Scanned barcode is not of product to pick'),'default',array('class'=>'alert alert-danger'));
                    $options = array('conditions' => array('Wave.' . $this->Wave->primaryKey => $id));
                    $this->request->data = $this->Wave->find('first', $options);

                }

        }


        $wave = $this->Wave->find('first',array('conditions' => array('Wave.' . $this->Wave->primaryKey => $id)));
        //pr($wave);die;
        //If pack start time was not updated, update it now
        if(empty($wave['Wave']['packstart']))
            $this->updatewavetimes($wave['Wave']['id'],true);

        //Get wave courier and use it to update the shipments we create in this wave
        $this->Session->write('courierid', $wave['Wave']['courier_id']);
        //$this->Session->write('courierid', $wave['Wave']['courier_id']);
        if($wave['Wave']['status_id'] == 20)
            $this->updatewavestatus($wave['Wave']['id'],16);

        $x=0;
        //Debugger::dump($wave['OrdersLine']);
        // We look for the first order line with status not completed, either pack complete, partialy packed, weighed
        foreach ($wave['OrdersLine'] as $orderline):
            if(($orderline['status_id'] == 17 || $orderline['status_id'] == 18 || $orderline['status_id'] == 21 || $orderline['status_id'] == 22) && $orderline['type'] == 1)
            {
                $waveline[$x] = $orderline;
                $x++;
            }
        endforeach;
    
        if(empty($waveline))
        {
            $this->updatewavestatus($wave['Wave']['id'],4);
            $this->updatewavetimes($wave['Wave']['id'],false);
            return $this->redirect(array('action' => 'index'));
        }


        //Get packaging material for current line's order
        $this->loadModel('OrdersLine');
        $packorderline = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.order_id' => $waveline[0]['order_id'],'OrdersLine.type' => 4)));

        // Should we show variant attributes - size and color?
        $showvariants = $this->Auth->user('showvariants');
        $this->loadModel('Product');
        //Get product data
        $product = $this->Product->find('first', array('conditions' => array('Product.id' => $waveline[0]['product_id'])));


                $pickline['productname'] = $product['Product']['name'];
                $pickline['pickquantity'] = $waveline[0]['quantity'];
                $pickline['imageurl'] = $product['Product']['imageurl'];
                $pickline['bin'] = $product['Product']['bin'];
                $pickline['ordernumber'] = $waveline[0]['order_id'];
                $pickline['id'] = $waveline[0]['id'];
                $pickline['productid'] = $waveline[0]['product_id'];
                $pickline['lineid'] = $waveline[0]['id'];
                $pickline['sentqty'] = $waveline[0]['sentqty'];
                $pickline['linenumber'] = $waveline[0]['line_number'];
                $pickline['weight'] = '';
                if(!empty($packorderline['OrdersLine']['product_id']))
                {
                $pickline['packmaterialnumber'] = $packorderline['OrdersLine']['product_id'];
                $pickline['packmaterialdescription'] = $packorderline['Product']['description'];
                $pickline['packmaterialimageurl'] = $packorderline['Product']['imageurl'];
                }
                if($showvariants == 1)
                    {
                    $this->Session->write('showvariants', 1);
                    if(isset($product['Color']['name']))
                        $pickline['color'] = $product['Color']['name'];
                    if(isset($product['Color']['htmlcode']))
                        $pickline['colorhtml'] = $product['Color']['htmlcode'];
                    if(isset($product['Size']['name']))
                        $pickline['size'] = $product['Size']['name'];
                        if(isset($product['Size']['description']))
                        $pickline['sizedescription'] = $product['Size']['description'];
                    } else {
                        $this->Session->write('showvariants', 0);
                    }


        $this->Session->write('lastorder', $waveline[0]['order_id']);

        $this->Session->write('lefttopick', $waveline[0]['quantity'] - $waveline[0]['sentqty']);
        if($waveline[0]['quantity'] - $waveline[0]['sentqty'] == 0)
                    $this->Session->write('packstatusc', 'glyphicon glyphicon-ok font-green');

        //Get number of lines in current order
        //$this->loadModel('Order');

    //  echo "last order:";
    //  Debugger::dump($this->Session->read('lastorder'));

    //  echo "new order:";
    //  echo $waveline[0]['order_id'];


        $remainingorderlines =  $this->Wave->OrdersLine->find('count', array('conditions' => array('OrdersLine.order_id' => $pickline['ordernumber'],'OrdersLine.type' => 1,'OrdersLine.status_id' => 17)));
    //  echo "<BR>orders line count:";
    //  Debugger::dump($remainingorderlines);
        //if this is the first line of order, we set orderlinecount == to remainingorderlines
        if($this->Session->read('orderlinescount') == null)
            $this->Session->write('orderlinescount', $remainingorderlines);
        $this->Session->write('remainingorderlines', $remainingorderlines);
        //Debugger::dump($this->Session->read('orderlinescount'));
    // echo 'is this same';
        //we have completed packing all pieces for line, we need to see if next line is of same order or different order
        //echo "we check if next order is same as this line";
        if($this->Session->read('lefttopick') == 0 && isset($waveline[1]) )
        {
            //  echo "we check if next order is same as this line";
            if($waveline[0]['order_id'] == $waveline[1]['order_id'])
            {
                //  echo "next order is the same as the current order";
                //if next line has the same order id, we keep using the same shipment
                $this->Session->write('sameshipment', 1);
                // Set order line status to 23, so we skip weigh and move to next line. we only weigh when all pieces have been packed
                // assuming all of them are packed into a single shipment
                $this->Session->write('lefttopick', -1);
                $this->updateorderlines($this->request->data['Wave']['lineid'],$id);
            } else {
                $this->Session->write('sameshipment', 0);
                $orderlinescount =  $this->Wave->OrdersLine->find('count', array('conditions' => array('OrdersLine.order_id' => $pickline['ordernumber'],'OrdersLine.type' => 1,'OrdersLine.status_id' => 17)));
                $this->Session->write('orderlinescount', $orderlinescount);
            }
        }

        if($waveline[0]['status_id'] == 22)
        {
            $this->Session->write('packstatusc', 'glyphicon glyphicon-ok font-green');
            $this->Session->write('weighstatusc', 'glyphicon glyphicon-ok font-green');
            $this->Session->write('lefttopick', -1);

                $this->Wave->updateAll(
                array('Wave.linespacked' => 'Wave.linespacked + 1'),
                array('Wave.id' => $wave['Wave']['id'])
            );

        }
        $this->Session->write('lineqty', $waveline[0]['quantity']);
        if($this->Session->read('lefttopick') == $this->Session->read('lineqty'))
        {
            $this->Session->write('packstatusc', 'glyphicon glyphicon-remove font-red');
            $this->Session->write('weighstatusc', 'glyphicon glyphicon-remove font-red');
        }

            $this->set(compact('pickline','wave'));
        }

    public function pickbyorder($id = null) {
        $this->layout = 'mtrd';
        $this->loadModel('User');
        $scan = $this->User->find('first', array('conditions' => array('id' => $this->Auth->user('id')), 'fields' => 'pick_by_order', 'recursive' => -1));
        if($scan['User']['pick_by_order'] == 0){
            $this->Session->setFlash(__('Please select pick by order from settings.'), 'admin/danger', array());
            return $this->redirect(array('action' => 'edit',$id));
        }

        $this->Session->write('qty', 0);

        if($scan['User']['pick_by_order'] == 2)
            $this->Session->write('label', 'Scan the products bin number');
        elseif($scan['User']['pick_by_order'] == 3)
            $this->Session->write('label', 'Scan the products SKU/EAN number');
        $wave = $this->Wave->find('first',array('conditions' => array('Wave.id' => $id)));
        $this->set(compact('pickline', 'wave', 'total_orderline', 'total_order', 'total_line_current_order', 'remain_orderline', 'id', 'scan'));
    }

    public function batchpicking($id = null) {
        $this->layout = 'mtrd';

        $this->loadModel('User');
        $scan = $this->User->find('first', array('conditions' => array('id' => $this->Auth->user('id')), 'fields' => 'batch_pick', 'recursive' => -1));
        $wave = $this->Wave->find('first',array('conditions' => array('Wave.id' => $id), 'recursive' => -1));
        if($scan['User']['batch_pick'] == 0){
            $this->Session->setFlash(__('Please select batch pick from settings.'), 'admin/danger', array());
            return $this->redirect(array('action' => 'index'));
        }

        $options = array('joins' => array(
                array(
                    'alias' => 'OrderslinesWave',
                    'table' => 'orderslines_waves',
                    'type' => 'INNER',
                    'conditions' => array(
                          '`OrderslinesWave`.`ordersline_id` = `OrdersLine`.`id`',
                          '`OrderslinesWave`.`wave_id` = '.$id
                    ),
                ),
            ),
            'fields'  => array(
                    'Product.id',
                    'Product.name',
                    'Product.imageurl',
                    'Product.user_id',
                    'OrdersLine.order_id',
                    'sum(OrdersLine.quantity) as total_qty',
            ),
           'recursive' => 1,
           'group' => 'OrdersLine.product_id',
           'order' => 'OrdersLine.created Asc'
           );

        $this->loadModel('OrdersLine');
        $products = $this->OrdersLine->find('all',$options);

        if($wave['Wave']['type'] == 2) {
            $this->loadModel('BatchPick');
            foreach ($products as $pdtlist):
                $batchpick = $this->BatchPick->find('first',array('conditions' => array('BatchPick.waveid' => $id, 'BatchPick.productid ' => $pdtlist['Product']['id'], 'BatchPick.user_id' => $pdtlist['Product']['user_id']), 'recursive' => -1));

                 if(empty($batchpick)) {
                    $this->BatchPick->create();
                    $this->request->data('BatchPick.waveid', $id);
                    $this->request->data('BatchPick.productid', $pdtlist['Product']['id']);
                    $this->request->data('BatchPick.user_id', $pdtlist['Product']['user_id']);
                    $this->request->data('BatchPick.quantity', $pdtlist[0]['total_qty']);
                    $this->BatchPick->save($this->request->data);
                }

            endforeach;
        }

        $this->set(compact('products', 'scan', 'id', 'wave'));
    }

    public function batchProductList($id){
        $options = array('joins' => array(
                array(
                    'alias' => 'OrderslinesWave',
                    'table' => 'orderslines_waves',
                    'type' => 'INNER',
                    'conditions' => array(
                          '`OrderslinesWave`.`ordersline_id` = `OrdersLine`.`id`',
                          '`OrderslinesWave`.`wave_id` = '.$id
                    ),
                ),
            ),
            'fields'  => array(
                        'Product.id',
                        'Product.name',
                        'Product.imageurl',
                        'OrdersLine.id',
                        'OrdersLine.order_id',
                        'OrdersLine.product_id',
                        'OrdersLine.line_number',
                        'OrdersLine.status_id',
                        'OrdersLine.type',
                        'OrdersLine.sentqty',
                        'OrdersLine.quantity',
                        'sum(OrdersLine.quantity) as total_qty',
                    ),
           'recursive' => 1,
           'group' => 'OrdersLine.product_id',
           'order' => 'OrdersLine.created Asc'
        );
        $this->loadModel('OrdersLine');
        $products = $this->OrdersLine->find('all',$options);

        $x = 0;
        foreach ($products as $orderline):
            if(($orderline['OrdersLine']['status_id'] == 17 || $orderline['OrdersLine']['status_id'] == 18 || $orderline['OrdersLine']['status_id'] == 21 || $orderline['OrdersLine']['status_id'] == 22) && $orderline['OrdersLine']['type'] == 1) {
                $waveline[$x] = $orderline;
                $x++;
            }

        endforeach;
        
        $this->loadModel('Product');
        $product = $this->Product->find('first', array('conditions' => array('Product.id' => $waveline[0]['OrdersLine']['product_id'])));

                $count['productname'] = $product['Product']['name'];
                $count['pickquantity'] = $waveline[0]['OrdersLine']['quantity'];
                $count['imageurl'] = $product['Product']['imageurl'];
                $count['ordernumber'] = $waveline[0]['OrdersLine']['order_id'];
                $count['orderlinenumber'] = $waveline[0]['OrdersLine']['id'];
                $count['bin'] = $product['Product']['bin'];
                $count['productid'] = $waveline[0]['OrdersLine']['product_id'];
                $count['lineid'] = $waveline[0]['OrdersLine']['id'];
                $count['sentqty'] = $waveline[0][0]['total_qty'];
                $count['linenumber'] = $waveline[0]['OrdersLine']['line_number'];

         echo json_encode(array(
            'count' => $count,
        ));
        die;
    }

    public function batchpickProduct($id){
        $this->loadModel('BatchPick');
        $x=0;
        $waveline = [];
        $batchlist = $this->BatchPick->find('all', array('conditions' => array('waveid' => $id)));
        
            foreach ($batchlist as $list):

                if($list['BatchPick']['actual_quantity'] == 0)
                {
                    $x++;
                    $waveline[$x] = $list;

                }
            endforeach;
            
            if(!empty($waveline)) {
                $pickline['productname'] = $waveline[1]['Product']['name'];
                $pickline['imageurl'] = $waveline[1]['Product']['imageurl'];
                $pickline['bin'] = $waveline[1]['Product']['bin'];
                $pickline['productid'] = $waveline[1]['Product']['id'];
                $pickline['bin'] = $waveline[1]['Product']['bin'];
                $pickline['sentqty'] = $waveline[1]['BatchPick']['quantity'];
            }

         echo json_encode(array(
            'count' => $pickline,
        ));
        die;
    }

    public function batchprocess($id) { 
        $url = false;
        $status = true;
        $message = '';
        $pickline = '';
        $this->loadModel('User');
        if(!empty($this->request->data)) {
            $options = array('joins' => array(
                    array(
                        'alias' => 'OrderslinesWave',
                        'table' => 'orderslines_waves',
                        'type' => 'INNER',
                        'conditions' => array(
                              '`OrderslinesWave`.`ordersline_id` = `OrdersLine`.`id`',
                              '`OrderslinesWave`.`wave_id` = '. $id
                        ),
                    ),
                ),
                'recursive' => 1,
                'conditions' => array('OrdersLine.product_id' => $this->request->data['Wave']['productid']),
            );

            $this->loadModel('OrdersLine');
            $wave = $this->OrdersLine->find('all',$options);
            $scan = $this->User->find('first', array('conditions' => array('id' => $this->Auth->user('id')), 'fields' => 'batch_pick', 'recursive' => -1));

            if($scan['User']['batch_pick'] == 1){

                if(!empty($this->request->data['Wave']['sentqty'])) {
                    $orderLine = $this->Wave->OrdersLine->findById($this->request->data['Wave']['lineid']);
                    $product_id = $orderLine['OrdersLine']['product_id'];
                    $all_sentqty = $this->request->data['Wave']['sentqty'];
                    foreach ($wave as $line) {
                        if($line['OrdersLine']['product_id'] == $product_id) {
                            $to_add = $line['OrdersLine']['quantity'] - $line['OrdersLine']['sentqty'];
                            if($to_add > 0) {
                                if($all_sentqty > $to_add) {
                                    $all_sentqty = $all_sentqty - $to_add;
                                }
                                
                                $quantity = $this->updateorderlines($line['OrdersLine']['id'], $id, $to_add);
                                $this->updateorderstatus($line['OrdersLine']['order_id']);
                                $this->updateinventory($line['OrdersLine']['product_id'], $to_add, $line['OrdersLine']['warehouse_id']);
                            }
                        }
                    }
                    $status = true;
                    $message = 'Products quantity added succesfully.';
                } else{
                    $status = false;
                    $message = 'Please fill quantity';
                }

            } elseif($scan['User']['batch_pick'] == 4) {
                if(!empty($this->request->data['Wave']['bin']) && !empty($this->request->data['Wave']['sku']) && !empty($this->request->data['Wave']['sentqty']))
                {

                    if(!empty($this->request->data['Wave']['bin']))
                    {
                        $product_bin=$this->getproductbin($this->request->data);
                        if($product_bin == 0){
                            $status = false;
                            $message = 'Please enter correct bin';
                        }

                        if(!empty($this->request->data['Wave']['sku'])){
                            $product_sku=$this->getproductid($this->request->data);
                            if($product_sku == 0){
                                $status = false;
                                $message = 'Please enter correct sku or EAN';
                            }
                        }

                        if($product_sku != 0 && $product_bin != 0){
                            $all_sentqty = $this->request->data['Wave']['sentqty'];
                            foreach ($wave as $line) {
                                if($line['OrdersLine']['product_id'] == $product_sku) {
                                    $to_add = $line['OrdersLine']['quantity'] - $line['OrdersLine']['sentqty'];
                                    if($to_add > 0) {
                                        if($all_sentqty > $to_add) {
                                            $all_sentqty = $all_sentqty - $to_add;
                                        }
                                        
                                        $quantity = $this->updateorderlines($line['OrdersLine']['id'], $id, $to_add);
                                        $this->updateorderstatus($line['OrdersLine']['order_id']);
                                        $this->updateinventory($line['OrdersLine']['product_id'], $to_add, $line['OrdersLine']['warehouse_id']);
                                    }
                                }
                            }
                            $status = true;
                            $message = 'Products quantity added succesfully.';
                        }
                    }
                }
                else{
                    $status = false;
                    $message = 'Please fill all input boxes';
                }
            } else {
                if(!empty($this->request->data['Wave']['scan']) || !empty($this->request->data['Wave']['sku'])) {
                    if(!empty($this->request->data['Wave']['sku'])){
                        $product_id = $this->getproductid($this->request->data);
                        $status = false;
                        $message = 'Please enter correct sku or EAN';
                    } elseif(!empty($this->request->data['Wave']['scan'])) {
                        $product_id=$this->getproductbin($this->request->data);
                        $status = false;
                        $message = 'Please enter correct bin';
                    } else{

                    }

                    if($product_id != 0) {
                        $all_sentqty = $this->request->data['Wave']['sentqty'];
                        foreach ($wave as $line) {
                            if($line['OrdersLine']['product_id'] == $product_id) {
                                $to_add = $line['OrdersLine']['quantity'] - $line['OrdersLine']['sentqty'];
                                if($to_add > 0) {
                                    if($all_sentqty > $to_add) {
                                        $all_sentqty = $all_sentqty - $to_add;
                                    }
                                    
                                    $quantity = $this->updateorderlines($line['OrdersLine']['id'], $id, $to_add);
                                    $this->updateorderstatus($line['OrdersLine']['order_id']);
                                    $this->updateinventory($line['OrdersLine']['product_id'], $to_add, $line['OrdersLine']['warehouse_id']);
                                }
                            }
                        }
                        $status = true;
                        $message = 'Products quantity added succesfully.';
                    }
                } else {
                    $status = false;
                    $message = 'Please scan the barcode number';
                }
            }

        $wave = $this->Wave->find('first',array('conditions' => array('Wave.id' => $id)));

        $result = array();
        $waves = array();
        foreach($wave['OrdersLine'] as $key =>  $orders){
           if(!isset($result[$orders["product_id"]])){
              $result[$orders["product_id"]] = $orders;
           }
        }

        $total_orderline = count($result);
        
        foreach($result as $key => $orders){
            $order_list[] = $orders['order_id'];
            $total_line_current_order[$orders['order_id']][] = $orders['id'];
        }

        //total orders to pick
        $total_order = count(array_unique($order_list));

        //If pack start time was not updated, update it now
        if(empty($wave['Wave']['packstart']))
            $this->updatewavetimes($wave['Wave']['id'],true);

        if($wave['Wave']['status_id'] == 20) {
            $this->updatewavestatus($wave['Wave']['id'],16,0);
        }

                $x=0;
                $waveline = [];
                $orderlineid = [];
                //look for the first order line with status not completed, either pack complete, partialy packed, weighed
                foreach ($result as $key => $orderline):
                        if(($orderline['status_id'] == 17 || $orderline['status_id'] == 18 || $orderline['status_id'] == 21 || $orderline['status_id'] == 22) && $orderline['type'] == 1)
                        {
                            $waveline[$x] = $orderline;
                            $remain_orderline[$orderline['order_id']][] = $orders['id'];
                            $x++;
                        }
                endforeach;
                
                if(empty($waveline)){
                    $this->updatewavestatus($wave['Wave']['id'],4);

                    $this->updatewavetimes($wave['Wave']['id'],false);

                    $this->updateOrders($wave['Wave']['id']);

                    $status = true;
                    $message = 'Product batch pick completed succesfully.';
                    $url = Router::url(array("plugin" => false, "controller" => "waves", "action" => "index"), true);

                        echo json_encode(array(
                            'status' => $status,
                            'message' => $message,
                            'url' => $url
                        ));
                    die;
                }

                //Get packaging material for current line's order
                $this->loadModel('OrdersLine');
                $packorderline = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.order_id' => $waveline[0]['order_id'],'OrdersLine.type' => 4)));

                // Should we show variant attributes - size and color?
                $showvariants = $this->Auth->user('showvariants');
                $this->loadModel('Product');
                //Get product data
                $product = $this->Product->find('first', array('conditions' => array('Product.id' => $waveline[0]['product_id'])));

                        $pickline['productname'] = $product['Product']['name'];
                        $pickline['pickquantity'] = $waveline[0]['quantity'];
                        $pickline['imageurl'] = $product['Product']['imageurl'];
                        $pickline['bin'] = $product['Product']['bin'];
                        $pickline['ordernumber'] = $waveline[0]['order_id'];
                        $pickline['orderlinenumber'] = $waveline[0]['id'];
                        $pickline['id'] = $waveline[0]['id'];
                        $pickline['productid'] = $waveline[0]['product_id'];
                        $pickline['lineid'] = $waveline[0]['id'];
                        $pickline['sentqty'] = $waveline[0]['sentqty'];
                        $pickline['linenumber'] = $waveline[0]['line_number'];
                        $pickline['weight'] = '';


                        if(!empty($packorderline['OrdersLine']['product_id']))
                        {
                            $pickline['packmaterialnumber'] = $packorderline['OrdersLine']['product_id'];
                            $pickline['packmaterialdescription'] = $packorderline['Product']['description'];
                            $pickline['packmaterialimageurl'] = $packorderline['Product']['imageurl'];
                        }


            $this->Session->write('lastorder', $waveline[0]['order_id']);

            $this->Session->write('lefttopick', $waveline[0]['quantity'] - $waveline[0]['sentqty']);

            $pickline['continue'] = $waveline[0]['quantity'] - $waveline[0]['sentqty'];
            //pr($this->Session->read('lefttopick'));die;
                if($waveline[0]['quantity'] - $waveline[0]['sentqty'] == 0)
                    $this->Session->write('packstatusc', 'glyphicon glyphicon-ok font-green');

                $remainingorderlines =  $this->Wave->OrdersLine->find('count', array('conditions' => array('OrdersLine.order_id' => $pickline['ordernumber'],'OrdersLine.type' => 1,'OrdersLine.status_id' => 17)));

            //if this is the first line of order, we set orderlinecount == to remainingorderlines
            if($this->Session->read('orderlinescount') == null)
                $this->Session->write('orderlinescount', $remainingorderlines);
            $this->Session->write('remainingorderlines', $remainingorderlines);

                //we have completed packing all pieces for line, we need to see if next line is of same order or different order
                //echo "we check if next order is same as this line";
                if($this->Session->read('lefttopick') == 0 && isset($waveline[1]) )
                {
                    //  echo "we check if next order is same as this line";
                    if($waveline[0]['order_id'] == $waveline[1]['order_id'])
                    {
                    //  echo "next order is the same as the current order";
                        //if next line has the same order id, we keep using the same shipment
                        $this->Session->write('sameshipment', 1);
                        // Set order line status to 23, so we skip weigh and move to next line. we only weigh when all pieces have been packed
                        // assuming all of them are packed into a single shipment
                        $this->Session->write('lefttopick', -1);
                        $this->updateorderlines($this->request->data['Wave']['lineid'],$id);
                    } else {
                        $this->Session->write('sameshipment', 0);
                        $orderlinescount =  $this->Wave->OrdersLine->find('count', array('conditions' => array('OrdersLine.order_id' => $pickline['ordernumber'],'OrdersLine.type' => 1,'OrdersLine.status_id' => 17)));
                        $this->Session->write('orderlinescount', $orderlinescount);
                    }
                }


                if($waveline[0]['status_id'] == 22)
                {
                    $this->Session->write('packstatusc', 'glyphicon glyphicon-ok font-green');
                    $this->Session->write('weighstatusc', 'glyphicon glyphicon-ok font-green');
                    $this->Session->write('lefttopick', -1);

                        $this->Wave->updateAll(
                        array('Wave.linespacked' => 'Wave.linespacked + 1'),
                        array('Wave.id' => $wave['Wave']['id'])
                    );

                }
                $this->Session->write('lineqty', $waveline[0]['quantity']);
                if($this->Session->read('lefttopick') == 0)
                {
                    $this->updatewavestatus($wave['Wave']['id'],4);
                    $this->updatewavetimes($wave['Wave']['id'],false);
                    $status = true;
                    $message = 'Success';
                    $url = Router::url(array("plugin" => false, "controller" => "waves", "action" => "index"), true);
                }

                if($this->Session->read('lefttopick') == $this->Session->read('lineqty')) {
                    $this->Session->write('packstatusc', 'glyphicon glyphicon-remove font-red');
                    $this->Session->write('weighstatusc', 'glyphicon glyphicon-remove font-red');
                }

        }

        echo json_encode(array(
            'status' => $status,
            'message' => $message,
            'url' => $url,
            'pickline' => $pickline,
        ));
        die;

    }

    public function batchpick($id){
        $url = false;
        $status = true;
        $message = '';
        $pickline = '';

        if(!empty($this->request->data))
        {

            $this->loadModel('User');
            $scan = $this->User->find('first', array('conditions' => array('id' => $this->Auth->user('id')), 'fields' => 'batch_pick', 'recursive' => -1));
            $this->loadModel('BatchPick');
            $batchpick = $this->BatchPick->find('first', array('conditions' => array('waveid' => $id, 'productid' => $this->request->data['Wave']['productid'])));

            if($scan['User']['batch_pick'] == 1) {

                if(!empty($this->request->data['Wave']['sentqty'])) {

                    $actual_quantity = $batchpick['BatchPick']['actual_quantity'] + $this->request->data['Wave']['sentqty'];
                    $quantity = $this->updateactual_quantity($batchpick['BatchPick']['id'], $actual_quantity);
                    $status = true;
                    $message = 'Products quantity added succesfully.';

                } else {
                    $status = false;
                    $message = 'Please fill quantity';
                }

            } elseif($scan['User']['batch_pick'] == 4) {
                if(!empty($this->request->data['Wave']['bin']) && !empty($this->request->data['Wave']['sku']) && !empty($this->request->data['Wave']['sentqty']))
                {

                    if(!empty($this->request->data['Wave']['bin']))
                    {
                        $product_bin=$this->getproductbin($this->request->data);
                        if($product_bin == 0){
                            $status = false;
                            $message = 'Please enter correct bin';
                        }

                        if(!empty($this->request->data['Wave']['sku'])){
                            $product_sku=$this->getproductid($this->request->data);
                            if($product_sku == 0){
                                $status = false;
                                $message = 'Please enter correct sku or EAN';
                            }
                        }

                        if($product_sku != 0 && $product_bin != 0){
                          $actual_quantity = $batchpick['BatchPick']['actual_quantity'] + $this->request->data['Wave']['sentqty'];
                          $quantity = $this->updateactual_quantity($batchpick['BatchPick']['id'], $actual_quantity);
                          $status = true;
                          $message = 'Products quantity added succesfully.';
                        }
                    }
                }
                else{
                    $status = false;
                    $message = 'Please fill all input boxes';
                }
            } else {
                if(!empty($this->request->data['Wave']['scan']) || !empty($this->request->data['Wave']['sku']))
                {
                    if(!empty($this->request->data['Wave']['sku'])){
                        $product_id=$this->getproductid($this->request->data);
                        $status = false;
                        $message = 'Please enter correct sku or EAN';
                    }
                    elseif(!empty($this->request->data['Wave']['scan']))
                    {
                        $product_id=$this->getproductbin($this->request->data);
                        $status = false;
                        $message = 'Please enter correct bin';
                    } else{

                    }
                    if($product_id != 0){
                        $actual_quantity = $batchpick['BatchPick']['actual_quantity'] + $this->request->data['Wave']['sentqty'];
                        $quantity = $this->updateactual_quantity($batchpick['BatchPick']['id'], $actual_quantity);
                        $status = true;
                        $message = 'Products quantity added succesfully.';
                    }
                } else {
                    $status = false;
                    $message = 'Please scan the barcode number';
                }
            }

            $x=0;
            $waveline = [];
            $batchlist = $this->BatchPick->find('all', array('conditions' => array('waveid' => $id)));
            foreach ($batchlist as $list) {
                if($list['BatchPick']['actual_quantity'] == 0) {
                    $x++;
                    $waveline[$x] = $list;
                }
            }

            if(empty($waveline)) {
                $this->updatewavestatus($id,45);
                $this->updatetype($id);
                
                $this->updateOrders($id);

                $status = true;
                $message = 'Product batch pick completed succesfully.';
                $url = Router::url(array("plugin" => false, "controller" => "waves", "action" => "index"), true);
            } else {
                $pickline['productname'] = $waveline[1]['Product']['name'];
                $pickline['imageurl'] = $waveline[1]['Product']['imageurl'];
                $pickline['bin'] = $waveline[1]['Product']['bin'];
                $pickline['productid'] = $waveline[1]['Product']['id'];
                $pickline['sentqty'] = $waveline[1]['BatchPick']['quantity'];
            }

        }

        echo json_encode(array(
            'status' => $status,
            'message' => $message,
            'url' => $url,
            'pickline' => $pickline,
        ));
        die;

    }

    function updateOrders($id) {
        // Update orders status to complete
        $this->loadModel('Order');
        $orders = $this->Wave->find('first', array(
            'conditions' => array('Wave.id' => $id, 'user_id' => $this->Auth->user('id')),
            'contain' => array('OrdersLine'),
        ));
        
        $ids = [];
        foreach ($orders['OrdersLine'] as $val) {
            $ids[] = $val['order_id'];
        }
        $ids = array_unique($ids);
        
        foreach ($ids as $order_id) {
            $this->Order->id = $order_id;
            $this->Order->saveField('status_id', 4); // Set Complete
        }
    }

    public function updateactual_quantity($id = null,$actualqty = null)
    {
        $this->loadModel('BatchPick');
        $this->BatchPick->id = $id;
        $this->BatchPick->saveField('actual_quantity', $actualqty);
    }


    public function totalCounts($id){
        $wave = $this->Wave->find('first',array('conditions' => array('Wave.id' => $id)));

        //total lines to pick
        $count['total_orderline'] = count($wave['OrdersLine']);

        foreach($wave['OrdersLine'] as $key => $orders){
            $order_list[] = $orders['order_id'];
            $total_line_current_order[$orders['order_id']][] = $orders['id'];
        }

        //total orders to pick
        $count['total_order'] = count(array_unique($order_list));

        $x=0;

        //look for the first order line with status not completed, either pack complete, partialy packed, weighed
        foreach ($wave['OrdersLine'] as $orderline):
            if(($orderline['status_id'] == 17 || $orderline['status_id'] == 18 || $orderline['status_id'] == 21 || $orderline['status_id'] == 22) && $orderline['type'] == 1)
            {
                $waveline[$x] = $orderline;
                $remain_orderline[$orderline['order_id']][] = $orders['id'];
                $x++;
            }
            else{
                $waveline = '';
            }
        endforeach;

        $this->loadModel('Product');
        $product = $this->Product->find('first', array('conditions' => array('Product.id' => $waveline[0]['product_id'])));

        $this->loadModel('User');
        $scan = $this->User->find('first', array('conditions' => array('id' => $this->Auth->user('id')), 'fields' => 'pick_by_order', 'recursive' => -1));

        if($scan['User']['pick_by_order'] == 2)
        {
            $count['productsku'] = $product['Product']['sku'];
            $count['label'] = 'SKU';
        }
        elseif($scan['User']['pick_by_order'] == 3 || $scan['User']['pick_by_order'] == 4)
        {

            $prefix = $produts = '';
              foreach($product['Bin'] as $key => $bins):
                  $produts .= $prefix . $bins['title'];
                  $prefix = ', ';
              endforeach;

                $count['productsku'] = $produts;
                $count['label'] = 'BIN';
        }


                $count['productname'] = $product['Product']['name'];
                $count['orderpicked'] = count($remain_orderline);
                $count['pickquantity'] = $waveline[0]['quantity'];
                $count['imageurl'] = $product['Product']['imageurl'];
                $count['bin'] = $product['Product']['bin'];
                $count['ordernumber'] = $waveline[0]['order_id'];
                $count['orderlinenumber'] = $waveline[0]['id'];
                $count['id'] = $waveline[0]['id'];
                $count['productid'] = $waveline[0]['product_id'];
                $count['lineid'] = $waveline[0]['id'];
                $count['sentqty'] = $waveline[0]['sentqty'];
                $count['linenumber'] = $waveline[0]['line_number'];
                $count['lineqty'] = $waveline[0]['quantity'];
                $count['remain_orderline'] = count($remain_orderline[$waveline[0]['order_id']]);
                $count['total_line_current_order'] = count($total_line_current_order[$waveline[0]['order_id']]);

         echo json_encode(array(
            'count' => $count,
        ));
        die;
    }

    public function pickprocess($id){
        $url = false;
        $status = true;
        $message = '';
        $this->loadModel('User');

        if(!empty($this->request->data))
        {
            $scan = $this->User->find('first', array('conditions' => array('id' => $this->Auth->user('id')), 'fields' => 'pick_by_order', 'recursive' => -1));

            if($scan['User']['pick_by_order'] == 4  || $scan['User']['pick_by_order'] == 6)
                $this->request->data['Wave']['sentqty'] = 1;

            if($scan['User']['pick_by_order'] == 1){
                if(!empty($this->request->data['Wave']['sentqty']))
                {
                    $orderLine = $this->Wave->OrdersLine->findById($this->request->data['Wave']['lineid']);
                    $sentqty = $orderLine['OrdersLine']['sentqty'] + $this->request->data['Wave']['sentqty'];
                    $quantity = $this->updateorderlines($this->request->data['Wave']['lineid'],$id, $this->request->data['Wave']['sentqty']);
                    $this->updateorderstatus($this->request->data['Wave']['ordernumber']);
                    $this->updateinventory($this->request->data['Wave']['productid'], $this->request->data['Wave']['sentqty'], $this->request->data['Wave']['locationid']);
                    $status = true;
                    $message = 'Products quantity added succesfully.';
                }
                else{
                    $status = false;
                    $message = 'Please fill quantity';
                }
            }
            else{
                if(!empty($this->request->data['Wave']['scan']) || !empty($this->request->data['Wave']['sku']) || !empty($this->request->data['Wave']['bin']) && !empty($this->request->data['Wave']['sentqty']))
                {
                    if(isset($this->request->data['Wave']['productid']))
                    {

                        $orderLine = $this->Wave->OrdersLine->findById($this->request->data['Wave']['lineid']);
                        $sentqty = $orderLine['OrdersLine']['sentqty'] + $this->request->data['Wave']['sentqty'];
                        $this->updateorderlines($this->request->data['Wave']['lineid'],$id, $this->request->data['Wave']['sentqty']);
                        $this->updateorderstatus($this->request->data['Wave']['ordernumber']);
                        $this->updateinventory($this->request->data['Wave']['productid'], $this->request->data['Wave']['sentqty'], $this->request->data['Wave']['locationid']);
                    } else {
                        $status = false;
                        $message = 'Please fill up the input boxes';
                    }
                }
                else{
                    $status = false;
                    $message = 'Please fill up the input boxes';
                }
            }
        }

        $wave = $this->Wave->find('first',array('conditions' => array('Wave.id' => $id)));

        //total lines to pick
        $total_orderline = count($wave['OrdersLine']);

        foreach($wave['OrdersLine'] as $key => $orders){
            $order_list[] = $orders['order_id'];
            $total_line_current_order[$orders['order_id']][] = $orders['id'];
        }

        //total orders to pick
        $total_order = count(array_unique($order_list));

        //If pack start time was not updated, update it now
        if(empty($wave['Wave']['packstart']))
            $this->updatewavetimes($wave['Wave']['id'],true);

        if($wave['Wave']['status_id'] == 20)
            $this->updatewavestatus($wave['Wave']['id'],16,1);

        $x=0;
        $waveline = [];
        //look for the first order line with status not completed, either pack complete, partialy packed, weighed
        foreach ($wave['OrdersLine'] as $orderline):
            if(($orderline['status_id'] == 17 || $orderline['status_id'] == 18 || $orderline['status_id'] == 21 || $orderline['status_id'] == 22) && $orderline['type'] == 1)
            {
                $waveline[$x] = $orderline;
                $remain_orderline[$orderline['order_id']][] = $orders['id'];
                $x++;
            }
        endforeach;

        if(empty($waveline)){
            $this->updatewavestatus($wave['Wave']['id'],4);
            $this->updatewavetimes($wave['Wave']['id'],false);
            $this->updateOrders($wave['Wave']['id']);
            
            $status = true;
            $message = 'All orders in wave have been fully picked';
            $url = Router::url(array("plugin" => false, "controller" => "waves", "action" => "index"), true);

                echo json_encode(array(
                'status' => $status,
                'message' => $message,
                'url' => $url
                ));
            die;
        }

        //Get packaging material for current line's order
        $this->loadModel('OrdersLine');
        $packorderline = $this->OrdersLine->find('first', array('conditions' => array('OrdersLine.order_id' => $waveline[0]['order_id'],'OrdersLine.type' => 4)));

        // Should we show variant attributes - size and color?
        $showvariants = $this->Auth->user('showvariants');
        $this->loadModel('Product');
        //Get product data
        $product = $this->Product->find('first', array('conditions' => array('Product.id' => $waveline[0]['product_id'])));

                $pickline['productname'] = $product['Product']['name'];
                $pickline['pickquantity'] = $waveline[0]['quantity'];
                $pickline['imageurl'] = $product['Product']['imageurl'];
                $pickline['bin'] = $product['Product']['bin'];
                $pickline['ordernumber'] = $waveline[0]['order_id'];
                $pickline['orderlinenumber'] = $waveline[0]['id'];
                $pickline['id'] = $waveline[0]['id'];
                $pickline['productid'] = $waveline[0]['product_id'];
                $pickline['lineid'] = $waveline[0]['id'];
                $pickline['sentqty'] = $waveline[0]['sentqty'];
                $pickline['linenumber'] = $waveline[0]['line_number'];
                $pickline['weight'] = '';


                if(!empty($packorderline['OrdersLine']['product_id']))
                {
                $pickline['packmaterialnumber'] = $packorderline['OrdersLine']['product_id'];
                $pickline['packmaterialdescription'] = $packorderline['Product']['description'];
                $pickline['packmaterialimageurl'] = $packorderline['Product']['imageurl'];
                }


        $this->Session->write('lastorder', $waveline[0]['order_id']);

        $this->Session->write('lefttopick', $waveline[0]['quantity'] - $waveline[0]['sentqty']);

        $pickline['continue'] = $waveline[0]['quantity'] - $waveline[0]['sentqty'];

        if($waveline[0]['quantity'] - $waveline[0]['sentqty'] == 0)
                    $this->Session->write('packstatusc', 'glyphicon glyphicon-ok font-green');

            $remainingorderlines =  $this->Wave->OrdersLine->find('count', array('conditions' => array('OrdersLine.order_id' => $pickline['ordernumber'],'OrdersLine.type' => 1,'OrdersLine.status_id' => 17)));

        //if this is the first line of order, we set orderlinecount == to remainingorderlines
        if($this->Session->read('orderlinescount') == null)
            $this->Session->write('orderlinescount', $remainingorderlines);
        $this->Session->write('remainingorderlines', $remainingorderlines);

        //we have completed packing all pieces for line, we need to see if next line is of same order or different order
        //echo "we check if next order is same as this line";
        if($this->Session->read('lefttopick') == 0 && isset($waveline[1]) )
        {
            //  echo "we check if next order is same as this line";
            if($waveline[0]['order_id'] == $waveline[1]['order_id'])
            {
            //  echo "next order is the same as the current order";
                //if next line has the same order id, we keep using the same shipment
                $this->Session->write('sameshipment', 1);
                // Set order line status to 23, so we skip weigh and move to next line. we only weigh when all pieces have been packed
                // assuming all of them are packed into a single shipment
                $this->Session->write('lefttopick', -1);
                $this->updateorderlines($this->request->data['Wave']['lineid'],$id);
            } else {
                $this->Session->write('sameshipment', 0);
                $orderlinescount =  $this->Wave->OrdersLine->find('count', array('conditions' => array('OrdersLine.order_id' => $pickline['ordernumber'],'OrdersLine.type' => 1,'OrdersLine.status_id' => 17)));
                $this->Session->write('orderlinescount', $orderlinescount);
            }
        }


        if($waveline[0]['status_id'] == 22)
        {
            $this->Session->write('packstatusc', 'glyphicon glyphicon-ok font-green');
            $this->Session->write('weighstatusc', 'glyphicon glyphicon-ok font-green');
            $this->Session->write('lefttopick', -1);

                $this->Wave->updateAll(
                array('Wave.linespacked' => 'Wave.linespacked + 1'),
                array('Wave.id' => $wave['Wave']['id'])
            );

        }

        $this->Session->write('lineqty', $waveline[0]['quantity']);
        if($this->Session->read('lefttopick') == 0)
        {
            $this->updatewavestatus($wave['Wave']['id'],4);
            $this->updatewavetimes($wave['Wave']['id'],false);
            $status = true;
            $message = 'Success';
            $url = Router::url(array("plugin" => false, "controller" => "waves", "action" => "index"), true);
        }

        if($this->Session->read('lefttopick') == $this->Session->read('lineqty'))
        {
            $this->Session->write('packstatusc', 'glyphicon glyphicon-remove font-red');
            $this->Session->write('weighstatusc', 'glyphicon glyphicon-remove font-red');
        }

        echo json_encode(array(
            'status' => $status,
            'message' => $message,
            'url' => $url,
            'pickline' => $pickline,
        ));
        die;

    }


    public function singleshipment($requset_data = null)
    {

        $this->loadModel('Shipment');
        // Make sure sihpment does not already exist
        $shipment = $this->Shipment->find('first', array('conditions' => array('Shipment.tracking_number' => $requset_data['Wave']['trackingnumber'])));

        if(empty($shipment))
        {
            $shipmentnumber = $this->createshipment($requset_data);
            return $shipmentnumber;
        } else {
        $this->Session->setFlash(__('Shipment already exists.Scan a different tracking number.'), 'admin/danger', array());
        return 0;
        }
    }

    public function multishipment($requset_data = null)
        {
            $this->loadModel('Order');
            $order = $this->Order->find('first', array('conditions' => array('Order.id' => $requset_data['Wave']['ordernumber'])));

            if($order['Shipment'][0]['tracking_number'] != $requset_data['Wave']['trackingnumber'])
            {
                return 0;
            } else {
                $this->Session->write('trackingnumber',$requset_data['Wave']['trackingnumber']);
                return $requset_data['Wave']['trackingnumber'];
            }
        }


    public function updateorderstatus($id = null) {
        $this->loadModel('Order');
        $this->Order->id = $id;
        $this->Order->saveField('status_id', 3);
    }

        public function updateshipmentweight($trackingnumber = null, $weight = null,$orderid = null)
        {
        //First, check weight is within tolerance
        $weightverification = $this->verifyweight($orderid,$weight);
        if($weightverification == 1)
        {
        $this->loadModel('Shipment');
        $this->Shipment->updateAll(
            array('Shipment.weight' => $weight),
            array('Shipment.tracking_number' => $trackingnumber));


        //Get shipment id so we can update order line with shipment id
        $shipmentid = $this->Shipment->find('first',array('fields' => array('Shipment.id'), 'conditions' => array('Shipment.tracking_number' => $trackingnumber,'Shipment.user_id' => $this->Auth->user('id') )));

                $this->Session->write('packstatusc', 'glyphicon glyphicon-ok font-green');
                $this->Session->write('weighstatusc', 'glyphicon glyphicon-ok font-green');
            return;
        } else {
            $this->Session->setFlash(__('Shipment weight is incorrect.'), 'admin/danger', array());
        }


        }

        public function verifyweight($order_id = null, $enteredweight = null)
        {
            //Get all order lines
            $totalweight = 0;
            $this->loadModel('Order');
            //$this->Order->id = $order_id;
            //Get order details
            $order = $this->Order->find('first', array('contain' => array('OrdersLine'),'conditions' => array('Order.id' => $order_id, 'Order.user_id' => $this->Auth->user('id'))));
            //Loop through order lines, including pack material
            foreach ($order['OrdersLine'] as $orderline)
            {
                // Debugger::dump($orderline);
                $this->loadModel('Product');
                //Get product weight
                if($orderline['line_number'] == '9999')
                {

                    $packweightarr = $this->Product->find('first',array('fields'=>array('Product.weight'),'conditions' => array('Product.id' => $orderline['product_id'])));
                    $packweight = $packweightarr['Product']['weight'];
                } else {
                    $packweight = 0;
                    $lineweight = $this->Product->find('first',array('fields'=>array('Product.weight'),'conditions' => array('Product.id' => $orderline['product_id'])));
                }
                
                $totalweight += ($lineweight['Product']['weight']*$orderline['sentqty'] + $packweight);
                 echo "total w ".$totalweight;
                 echo "pack wiehgt ".$packweight['Product']['weight'];
            }

            echo $totalweight;
                $delta = abs(1-($totalweight/$enteredweight));
                if( $delta > 0.1)
                {
                    // $this->Session->setFlash(__('Out of tolerance %s ',$delta));
                    return 0;
                } else {
                //  $this->Session->setFlash(__('In tolerance %s ',$delta));
                    return 1;
                }

        }

        public function updateorderline($id = null,$sentqty = 0,$status = 0,$shipmentid = 0)
        {
            $this->loadModel('OrdersLine');
            $this->OrdersLine->updateAll(
                array('OrdersLine.sentqty' => $sentqty, 'OrdersLine.status_id' => 21, 'OrdersLine.shipment_id' => $shipmentid),
                array('OrdersLine.id' => $id));

        }

        public function updatebatchorderlines($wave)
        {

            $this->loadModel('OrdersLine');
            foreach($wave as $orderline){
                $this->Wave->OrdersLine->id = $orderline['OrdersLine']['id'];
                $this->Wave->OrdersLine->saveField('sentqty',$orderline['OrdersLine']['quantity']);
                $this->Wave->OrdersLine->saveField('status_id', 23);

            }

        }

    public function updateorderlines($id = null,$wave_id = null,$sentqty = null, $productid = null) {   
        $this->Wave->OrdersLine->id = $id;
        $order_line = $this->Wave->OrdersLine->find('first', array('conditions' => array('OrdersLine.id' => $id)));
        $wave_list = $this->Wave->find('first', array('conditions' => array('Wave.id' => $wave_id), 'contain' => array('OrdersLine' => array('conditions' => array('OrdersLine.product_id' => $productid))), 'recursive' => -1));
        $sentqty = $order_line['OrdersLine']['sentqty'] + $sentqty;
        if($sentqty <= $order_line['OrdersLine']['quantity']) {
            $this->Wave->OrdersLine->saveField('sentqty', $sentqty);
            $this->Wave->OrdersLine->saveField('status_id', 23);
        } else {
            if(count($wave_list['OrdersLine']) > 1) {
                foreach($wave_list['OrdersLine'] as $orderline) {
                    $sentqty = $orderline['sentqty'] + $sentqty;
                    if($sentqty <= $orderline['quantity'])
                    {
                        $this->Wave->OrdersLine->id = $orderline['id'];
                        $this->Wave->OrdersLine->saveField('sentqty', $sentqty);
                        $this->Wave->OrdersLine->saveField('status_id', 23);
                    } else {
                        $this->Wave->OrdersLine->id = $orderline['id'];
                        $this->Wave->OrdersLine->saveField('sentqty', $orderline['quantity']);
                        $this->Wave->OrdersLine->saveField('status_id', 23);
                        $sentqty = $sentqty - $orderline['quantity'];
                    }
                }
            }
        }
    }

    public function completeshipment() {

    }

    public function updatewavelp($id = null)
    {
        //Debugger::dump($id);
        $this->Wave->updateAll(
        array('Wave.linespacked' => 'Wave.linespacked + 1'),
        array('Wave.id' => $id)
    );
    }

    public function updateinventory($product_id = null, $sentqty = null, $locationid = null)
    {
        $this->loadModel('Inventory');
        //Get current inventory
        if($locationid == null)
            $locationid = $this->Session->read('default_warehouse');

        $inventory = $this->Inventory->find('first',array('conditions' => array('Inventory.product_id' => $product_id, 'Inventory.user_id' => $this->Auth->user('id'), 'Inventory.warehouse_id' => $locationid), 'recursive' => -1));
        if(empty($inventory)) {
            $inventory = $this->Inventory->find('first',array('conditions' => array('Inventory.product_id' => $product_id, 'Inventory.user_id' => $this->Auth->user('id'), 'Inventory.warehouse_id' => $this->Session->read('default_warehouse')), 'recursive' => -1));
        }

        $this->Inventory->id = $inventory['Inventory']['id'];
        $this->Inventory->saveField('quantity', $inventory['Inventory']['quantity'] - $sentqty);
        return;
    }

        public function updatebatchinventory($wave)
        {

            $this->loadModel('Inventory');
            foreach($wave as $orderline){
                $inventory = $this->Inventory->find('all',array('fields' => array('Inventory.quantity'),'conditions' => array('Inventory.product_id' => $orderline['OrdersLine']['product_id'], 'Inventory.user_id' => $this->Auth->user('id'))));

            $this->Inventory->updateAll(
            array('Inventory.quantity' => $inventory[0]['Inventory']['quantity'] - $orderline['OrdersLine']['quantity']),
            array('Inventory.product_id' => $orderline['OrdersLine']['product_id'],
            'Inventory.user_id' => $this->Auth->user('id'),
            'Inventory.dcop_user_id' => $this->Auth->user('id')));

            }
            return;

        }

    public function removeserial($requset_data = null)
        {
            $this->loadModel('Serial');
            $this->Serial->updateAll(
            array('Serial.instock' => 0, 'Serial.order_id_out' => $this->request->data['Wave']['ordernumber']),
            array('Serial.serialnumber' => $requset_data['Wave']['productscan'])
        );
        return;
        }

        public function updatewavestatus($id = null,$status = null, $pick = null)
        {

            // we also update wave end time
            $now = date("Y-m-d H:i:s");
            $this->Wave->id = $id;
            $this->Wave->saveField('status_id', $status);
            $this->Wave->saveField('pick_process', $pick);
        }
        public function updatetype($id = null)
        {
            // we also update wave end time
            $now = date("Y-m-d H:i:s");
            $this->Wave->updateAll(
            array('Wave.type' => 1),
            array('Wave.id' => $id)
            );
        }
        public function updatewavetimes($id = null,$start = null)
        {
            $now = "'".date("Y-m-d H:i:s")."'";
            $this->Wave->id = $id;
            if($start == true)
            {
                $this->Wave->updateAll(
                    array('Wave.packstart' => $now),
                    array('Wave.id' => $id)
                );
            } else {
                $this->Wave->updateAll(
                    array('Wave.packend' => $now),
                    array('Wave.id' => $id)
                );

            }
            return 1;
        }

    public function getproductbin($requset_data = null)
    {
        if(!empty($requset_data['Wave']['scan']))
         $scan = $requset_data['Wave']['scan'];
        elseif(!empty($requset_data['Wave']['bin']))
         $scan = $requset_data['Wave']['bin'];

        $this->loadModel('Bin');
        $options = array(
            'conditions' => array('Bin.title like "'.$scan.'"'),
            'joins' => array(
                array(
                    'alias' => 'ProductBin',
                    'table' => 'products_bins',
                    'type' => 'INNER',
                    'conditions' => array(
                          '`Bin`.`id` = `ProductBin`.`bin_id`',
                          '`ProductBin`.`product_id` = '.$requset_data['Wave']['productid']
                    ),
                )
            ),
            'fields'  => array(
                        'Bin.id',
                        'Bin.title',
                        'ProductBin.bin_id',
                        'ProductBin.product_id',
                    ),
           );
        $bin = $this->Bin->find('all', $options);

        if(!empty($bin))
            return $bin[0]['ProductBin']['product_id'];
        else
            return 0;
    }

    public function getproductid($requset_data = null)
    {
        if(!empty($requset_data['Wave']['scan']))
         $scan = $requset_data['Wave']['scan'];
        elseif(!empty($requset_data['Wave']['sku']))
         $scan = $requset_data['Wave']['sku'];
        elseif(!empty($requset_data['Wave']['productscan']))
         $scan = $requset_data['Wave']['productscan'];

            $this->loadModel('Product');
            $options = array('conditions' => array('Product.id' => $requset_data['Wave']['productid'], array('OR' => array(
            'Product.sku' => $scan,
            'Product.barcode' => $scan,
            ))));
            $product = $this->Product->find('first', $options);

            if(!empty($product))
                return $product['Product']['id'];
            if(empty($product))
            {
            // could not find product sku or ean, look for serial
            //echo " we in serial";
            $this->loadModel('Serial');
            $options = array('conditions' => array('AND' => array(
            'Serial.serialnumber' => $scan, 'Serial.instock' => 1,'Serial.user_id' => $this->Auth->user('id'), 'Serial.warehouse_id' => $this->Session->read('default_warehouse')
            )));
            $serial = $this->Serial->find('first', $options);
            if(!empty($serial))
            {

            //  $this->removeserial($requset_data);
            $this->isserial = true;
                return $serial['Serial']['product_id'];
            }
        }

            return 0;
    }

    public function productbin($requset_data = null)
    {
            $this->loadModel('Product');
            $options = array('conditions' => array('OR' => array(
            'Product.sku' => $requset_data['Wave']['bin'],
            'Product.barcode' => $requset_data['Wave']['productscan']
            )));
            $product = $this->Product->find('first', $options);
            if(!empty($product))
                return $product['Product']['id'];

            return 0;

    }

    public function createshipment($requset_data = null)
    {
    //  echo "we create shipment";
        $this->loadModel('Shipment');
        $data = array(
            'Shipment' => array(
                'user_id' => $this->Auth->user('id'),
                'dcop_user_id' => $this->Auth->user('id'),
                'status_id' => 6,
                'order_id'  => $requset_data['Wave']['ordernumber'],
                'tracking_number' => $requset_data['Wave']['trackingnumber'],
                'courier_id' => 1,
                'direction_id' => 1
            )
        );

    $this->Shipment->create();
    // save the data
    // $this->OrdersLine->save($data);
    if ($shipment = $this->Shipment->save($data)) {
            return $requset_data['Wave']['trackingnumber'];
        } else {
            return 0;
        }

    }

    public function deletewavelines($wave_id = null,$line_id = null)
    {
    //Get orderline status to verify that we are allowed to remove line from wave

        $this->Wave->id = $wave_id;
        $wavelinestatus = $this->Wave->OrdersLine->find('first',array('conditions' => array("OrdersLine.id" => $line_id)));
        if($wavelinestatus['OrdersLine']['status_id'] == 17)
        {
        $wavelineid = $this->Wave->OrderslinesWave->find('first',array('conditions' => array("wave_id"=> $wave_id, "ordersline_id" => $line_id)));


        $this->Wave->OrderslinesWave->delete(array("id" => $wavelineid['OrderslinesWave']['id']));
        $this->Wave->updateAll(
                array('Wave.numberoflines' => 'Wave.numberoflines - 1 '),
                array('Wave.id' => $wave_id));

        $this->Wave->OrdersLine->updateAll(
                array('OrdersLine.status_id' => 2),
                array('OrdersLine.id' => $line_id));
                $this->Session->setFlash(__('Line removed from wave number %d succefully',$wave_id), 'admin/success', array());
                return $this->redirect(array('action' => 'edit',$wave_id));
        } else {
            $this->Session->setFlash(__('Cannot remove line from wave because it is already packed.'), 'admin/danger', array());
            return $this->redirect(array('action' => 'edit',$wave_id));
        }
    }

    public function pickslipbyproduct($id = null)
    {

        $this->layout = 'mtrds';
            $this->ordersinwave = array();

        $wave = $this->Wave->find('first', array('conditions' => array('Wave.id' => $this->request->params['pass'][0],'Wave.user_id' => $this->Auth->user('id'))));
        
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
    
        $x=0;
        $prefix = $bin = '';
        foreach ($productqty as $key=>$value):
        //Get product data
        $this->loadModel('Product');
        $product = $this->Product->find('first', array('conditions' => array('Product.id' => $key), 'contain' => array('Bin')));
        
                $picklines[$x]['productname'] = $product['Product']['name'];
                $picklines[$x]['productsku'] = $product['Product']['sku'];
                $picklines[$x]['pickquantity'] =$value;
                $picklines[$x]['imageurl'] = $product['Product']['imageurl'];
                if(!empty($product['Bin'])) {
                    foreach($product['Bin'] as $key => $binList):
                    $bin .= $prefix . $binList['title'];
                    $prefix = ', ';
                    endforeach;
                    $picklines[$x]['bin'] = $bin;
                } 
                else {
                    $picklines[$x]['bin'] = '';     
                }

                $x++;
        endforeach;
        $this->set('wave',$wave);
        $this->set('picklines',$picklines);

    }

    public function pickslipbyorder($id = null)
    {
        $this->layout = 'mtrds';
        $this->ordersinwave = array();
 
        $wave = $this->Wave->find('first', array('conditions' => array('Wave.id' => $this->request->params['pass'][0],'Wave.user_id' => $this->Auth->user('id')), 'contain' => array('Courier', 'OrdersLine', 'OrdersLine.Order','OrdersLine.Order.Country','OrdersLine.Order.Schannel', 'OrdersLine.Product', 'OrdersLine.Product.Bin')));
                    
        $this->set('wave',$wave);
 
    }

        public function packwaven($id = null) {
            $this->layout = 'mtrd';
        if ($this->request->is(array('post', 'put'))) {

            if(isset($this->request->data['Wave']['newtrackingnumber']))

                    $shipmentnumber = $this->createshipment($this->request->data);

            //we got product id
            if(isset($this->request->data['Wave']['productscan']))
            {
                $product_id=$this->getproductid($this->request->data);

                // we have found the product based on the sku\barcode\serial
                // and it is our current lines' product

                if(isset($product_id) && isset($this->request->data['Wave']['productid']) && $product_id == $this->request->data['Wave']['productid'])
                {
                    //we update the sent qty in the order line and update order line status
                    $this->updateorderline($this->request->data['Wave']['lineid'],$this->request->data['Wave']['sentqty'],1,1);
                    //we update inventory levels for this line
                    $this->updateinventory($this->request->data['Wave']['productid'],$this->request->data['Wave']['sentqty']);
                    //update number of lines packed for this wave
                    $this->Wave->updateAll(
                        array('Wave.linespacked' => 'Wave.linespacked + 1'),
                        array('Wave.id' => $id)
                    );
                } else {
                    $this->Session->setFlash(__('Scanned product barcode is incorrect.'), 'admin/warning', array());
                }
            }

        }

        $wave = $this->Wave->find('first',array('conditions' => array('Wave.' . $this->Wave->primaryKey => $id)));

        //set number of lines and paked lines
        $wavelinescount = $wave['Wave']['numberoflines'];
        $wavelinespacked = $wave['Wave']['linespacked'];
        $this->set(compact('wavelinescount','wavelinespacked'));

        //If pack start time was not updated, update it now
        if(empty($wave['Wave']['packstart']))
            $this->updatewavetimes($wave['Wave']['id'],true);

        //Get wave courier and use it to update the shipments we create in this wave
        if(empty($this->Session->read('courierid')))
            $this->Session->write('courierid', $wave['Wave']['courier_id']);

        //We get all wave lines that has status
        $x = 0;
        foreach ($wave['OrdersLine'] as $orderline):
            if(($orderline['status_id'] == 17 ) && $orderline['type'] == 1)
            {
                $waveline[$x] = $orderline;
                $x++;
            }
        endforeach;

        //no more line for this wave
        if(empty($waveline))
        {

            $updatestatus = $this->updatewavestatus($wave['Wave']['id'],4);
            $updatetimes = $this->updatewavetimes($wave['Wave']['id'],false);
            //Debugger::dump($wave['Wave']);
            if($updatetimes == 1)
            {
                $this->Session->setFlash(__('Wave number  %s packed & shipped succesful', $id), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'index'));
            }
        }

        //check if order of first line has a shipment
        $this->loadModel('Shipment');
        $getordershipment = $this->Shipment->find('first', array('fields' => array('Shipment.order_id','Shipment.tracking_number','Shipment.weight'),'conditions' => array('Shipment.order_id' => $waveline[0]['order_id'],'Shipment.user_id' => $this->Auth->user('id'))));
        if(isset($getordershipment) && !empty($getordershipment))
            $trackingnumber = $getordershipment['Shipment']['tracking_number'];
        //if no shipment has this order number, we need to get from user shipment number

        if(empty($getordershipment))
        {

             $gettrackingnumber = true;
             $ordernumber = $waveline[0]['order_id'];
             $this->set(compact('gettrackingnumber','ordernumber'));
        } else {
            if(1==1)
            {
                $this->loadModel('Product');
        //Get product data
        $product = $this->Product->find('first', array('conditions' => array('Product.id' => $waveline[0]['product_id'])));


                $pickline['productname'] = $product['Product']['name'];
                $pickline['pickquantity'] = $waveline[0]['quantity'];
                $pickline['imageurl'] = $product['Product']['imageurl'];
                $pickline['ordernumber'] = $waveline[0]['order_id'];
                $pickline['id'] = $waveline[0]['id'];
                $pickline['productid'] = $waveline[0]['product_id'];
                $pickline['lineid'] = $waveline[0]['id'];
                $pickline['sentqty'] = $waveline[0]['sentqty'];
                $pickline['linenumber'] = $waveline[0]['line_number'];
                $pickline['weight'] = '';
                if(!empty($packorderline['OrdersLine']['product_id']))
                {
                    $pickline['packmaterialnumber'] = $packorderline['OrdersLine']['product_id'];
                    $pickline['packmaterialdescription'] = $packorderline['Product']['description'];
                    $pickline['packmaterialimageurl'] = $packorderline['Product']['imageurl'];
                }


                $this->set(compact('pickline','wave','trackingnumber','islatorderline'));
        }
    }


        }

    }
