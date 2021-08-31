<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Orders Controller
 *
 * @property Order $Order
 * @property PaginatorComponent $Paginator
 */
class OrdersController extends AppController {

    /**
    * Components
    *
    * @var array
    */
    public $helpers = array('Lang');
    public $components = array('Paginator','EventRegister','RequestHandler','Csv.Csv','Search.Prg','Shopfy', 'Amazon','WooCommerce', 'Access', 'Cookie', 'InventoryManager');
    public $paginate = array();
    public $theme = 'Mtro';
    public $types = [1 => 'S.O.', 2 => 'P.O.'];

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function isAuthorized($user) {
        return parent::isAuthorized($user);
    }


    /**
    * index method
    *
    * @return void
    */
    public function index($index = 1) {
        if($index == 2) {
            $this->redirect(['controller' => 'replorders']);
        } else {
            $this->redirect(['controller' => 'salesorders']);
        }
    }

    public function details($id) {
        $this->Order->recursive = -1;
        $order = $this->Order->find('first', array('conditions' => array('Order.id' => $id), 'fields' => array('Order.id', 'Order.ordertype_id')));
        if($order) {
            if($order['Order']['ordertype_id'] == 1) {
                $this->redirect(array('controller' => 'salesorders', 'action' => 'details', $id));
            } else {
                $this->redirect(array('controller' => 'replorders', 'action' => 'details', $id));
            }
        } else {
            throw new NotFoundException(__('Invalid order'));
        }
    }
    
    public function getAddress() {
        $keywords = $this->request->query['key'];
        $condition[] = array('Order.user_id' => $this->Auth->user('id'),'Order.ship_to_customerid like ' => "%" . $keywords . "%");
        $result = $this->{$this->modelClass}->find('all', array('conditions' => $condition, 'recursive' => -1));
        $json = array();
        $name = array();
        foreach ($result as $key => $data) {
                $name['id'] = $data['Order']['id'];
                $name['value'] = $data['Order']['ship_to_customerid'];
                 array_push($json ,$name);
        }
        echo json_encode($json);
        exit;
    }

    public function showAddress($id) {
       $condition[] = array('Order.id' => $id);
       $result = $this->Order->find('first', array('conditions' => $condition, 'contain' => 'Address.Country','Address.State','Country','State'));

       if(!empty($result['Order']['ship_to_street']) || !empty($result['Order']['ship_to_city']) || !empty($result['Order']['country_id'])) {
            $json['AddressCity'] = $result['Order']['ship_to_city'];
            $json['AddressStreet'] = $result['Order']['ship_to_street'];
            $json['AddressZip'] = $result['Order']['ship_to_zip'];
            $json['AddressPhone'] = $result['Order']['ship_to_phone'];
            $json['OrderShipToCustomerid'] = $result['Order']['ship_to_customerid'];
            $json['country_id'] = $result['Order']['country_id'];
            $json['state_id'] = ($result['Order']['country_id'] == 1 ? $result['Order']['state_id'] : '');
            $json['AddressStateprovince'] = $result['Order']['ship_to_stateprovince'];
            if(!empty($result['Order']['email'])) {
                $json['OrderEmail'] = $result['Order']['email'];
            }
         } else {
            $json['AddressCity'] = $result['Address']['city'];
            $json['AddressStreet'] = $result['Address']['street'];
            $json['AddressZip'] = $result['Address']['zip'];
            $json['AddressPhone'] = $result['Address']['phone'];
            $json['OrderShipToCustomerid'] = $result['Order']['ship_to_customerid'];
            $json['country_id'] = $result['Address']['country_id'];
            $json['state_id'] = ($result['Address']['country_id'] == 1 ? $result['Address']['state_id'] : '');
            $json['AddressStateprovince'] = $result['Address']['stateprovince'];
            if(!empty($result['Order']['email'])) {
                $json['OrderEmail'] = $result['Order']['email'];
            }
        }

       echo json_encode($json);
       exit;
    }

    /**
     * add address method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function addAddress() {
        $this->layout = false;
        $this->loadModel('Address');
        $countries = $this->Address->Country->find('list');
        $states = $this->Address->State->find('list');
        $this->set(compact('countries','states'));
    }

    /**
     * saveAddress method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function saveAddress() {
        if ($this->request->is('ajax')) {
            $this->loadModel('Address');
            if(!empty($this->request->data)) {
                $this->request->data['Address']['user_address_id'] = $this->Auth->user('id');
                if(!empty($this->request->data['Address']['state_id']))
                {
                  $statedate = $this->Address->State->findById($this->request->data['Address']['state_id']);
                  $this->request->data('Address.stateprovince', $statedate['State']['name']);
                }
                if ($this->Address->saveAll($this->request->data)) {
                    $response['status'] = true;
                    $response['url'] = Router::url(array("plugin" => false, "controller" => "orders", "action" => "addrord"), true);;
                    $response['message'] = 'The Address has been saved.';
                } else {
                   $response['status'] = false;
                   $Warehouse = $this->Address->invalidFields();
                   $response['data']=compact('Address');
                   $response['url'] = '';
                   $response['message']='The Address could not be saved. Please, try again.';
                }
                echo json_encode($response);
                die;
            }
        }
    }

    public function findex() {
        $conditions = array();
        if ($this->Auth->user('id')) {
            $conditions['Order.dcop_user_id'] = $this->Auth->user('id');
        }
        $this->set('orders',$this->paginate($conditions));

    }

    public function importcsv($filename = null, $ordersdata = null) {
        $this->layout = 'mtrd';
        $this->orderid = 0;

        $options = array(
            'length'    => 0,
            'delimiter' => "\t",
            'enclosure' => '"',
            'escape'    => '\\',
            'headers'   => true,
            'text'      => false,
        );
        $lastorderdata['Order']['external_orderid'] = '';
        $neworders = array();

        if(isset($filename)) {
            $content = WWW_ROOT."uploads/".$filename;
            $source  = $filename;
            $prefix  = "ileEx";
            $file    = fopen($content, "r");

            while ($line = fgetcsv($file, 1000, "\t")) {
                $numcols = count($line);
                if($numcols != 17) {
                    $linenumber = $line[0];
                    $this->showimporterror("The CSV file should have 17 columns, but line ".$linenumber." has ".$numcols." columns.",$file);
                }
            }
            fclose($file);

            if (strpos($filename, "ileEx") > 0) {

                $filedata = $this->Csv->import($content, array('Importfile.LineNumber',
                'Order.UserId', 'Order.ship_to_customerid','Order.phonenumber', 'Order.email',
                'Order.ship_to_street','Order.address2','Order.ship_to_city','Order.ship_to_stateprovince','Order.ship_to_zip',
                'Order.country','Order.ebayorderid','Order.external_orderid','Order.transactionid',
                'Order.itemtitle','OrdersLine.quantity','OrdersLine.unit_price','Order.shipping_costs',
                'Order.salestax','Order.insurence','Order.totalprice',
                'Order.paymentmethod','Order.paymenttrans','Order.saledate',
                'Order.checkoutdate','Order.paiddate','Order.shipdate','Order.shipservice',
                'Order.feedbackleft','Order.feedbackreceived','Order.comments','OrdersLine.sku',
                'Order.schannel_id','Ordervariation'), $options);

                $allowemptyfields = true;
                $searchcountryid = true;
            } else {
                $filedata = $this->Csv->import($content, array('Importfile.LineNumber','Order.ship_to_customerid',
                'Order.ship_to_street', 'Order.ship_to_city','Order.ship_to_stateprovince','Order.ship_to_zip',
                'Order.country_id','Order.ship_to_phone','Order.email','Order.external_orderid','Order.requested_delivery_date','Order.schannel_id',
                'Order.shipping_costs','OrdersLine.line_number','OrdersLine.sku','OrdersLine.quantity',
                'OrdersLine.unit_price'), $options);
                // $filedata = $this->Csv->import($content, array('Importfile.LineNumber','Order.ship_to_customerid',
                // 'Order.ship_to_street', 'Order.ship_to_city','Order.ship_to_stateprovince','Order.ship_to_zip',
                // 'Order.country_id','Order.external_orderid','Order.requested_delivery_date','Order.schannel_id',
                // 'Order.shipping_costs','OrdersLine.line_number','OrdersLine.sku','OrdersLine.quantity',
                // 'OrdersLine.unit_price'), $options);

                $allowemptyfields = false;
                $searchcountryid = false;
                $nextreforder = '';
                $oc = 0;
                $olc = 0;
                foreach ($filedata as $key=>$forderdata) {
                    if($key > 0) {
                        $currentreforder = $forderdata['Order']['external_orderid'];
                        if(isset($filedata[$key+1]['Order']['external_orderid']))
                        $nextreforder = $filedata[$key+1]['Order']['external_orderid'];
                        $ordersdata[$oc]['Order'] = $forderdata['Order'];
                        $ordersdata[$oc]['Importfile'] = $forderdata['Importfile'];
                        // we are in same ref order, continue to add order lines
                        $ordersdata[$oc]['OrdersLine'][$olc]['sku'] = $forderdata['OrdersLine']['sku'];
                        $ordersdata[$oc]['OrdersLine'][$olc]['quantity'] = $forderdata['OrdersLine']['quantity'];
                        $ordersdata[$oc]['OrdersLine'][$olc]['unit_price'] = $forderdata['OrdersLine']['unit_price'];
                        $ordersdata[$oc]['OrdersLine'][$olc]['line_number'] = $forderdata['OrdersLine']['line_number'];
                        //next line in CSV file is same order, we only advance
                        if($currentreforder == $nextreforder) {
                            $olc++;
                        } else {
                            //next line is different order number, we start new count of line
                            $oc++;
                            $olc =0;
                        }
                    }
                }
            }
            $startkey = 0;
           // $ordersdata = $filedata;
        } else {
            $allowemptyfields = true;
            $startkey = 0;
        }
        $numords = count($ordersdata);

            foreach ($ordersdata as $key=>$orderdata)
            {
                if($key >= $startkey)
                {
                    if(isset($filename) && !is_numeric($orderdata['Importfile']['LineNumber']))
                    {
                        $this->errorstr = "Line number is missing";
                    }
                    if($allowemptyfields == false)
                    {
                        foreach($orderdata['Order'] as $hkey=>$hvalue)
                        {

                            if($hkey !== 'shipping_costs' && empty($hvalue))
                            {
                                $this->errorstr = "In line number ".$orderdata['Importfile']['LineNumber']." value of ".$hkey." is missing";
                                $this->showimporterror($this->errorstr);
                            }
                        }

                        foreach($orderdata['OrdersLine'] as $olkey=>$lvalue)
                        {
                            foreach($lvalue as $flkey=>$flvalue)
                            {
                                if(empty($flvalue))
                                {
                                    $this->errorstr = "In line number ".$orderdata['Importfile']['LineNumber']." value of ".$flkey." is missing";
                                    $this->showimporterror($this->errorstr);
                                }
                            }
                        }
                    }
                }

                $this->Order->create();
                $orderdata['Order']['user_id'] = $this->Auth->user('id');
                $orderdata['Order']['dcop_user_id'] = $this->Auth->user('id');
                $orderdata['Order']['status_id'] = 14;
                $orderdata['Order']['ordertype_id'] = 1;
                $orderdata['Order']['interface'] = 1;

                //If country US, state_id should be a valid US state
                if($orderdata['Order']['country_id'] == 'US')
                {
                    $this->loadModel('State');
                    $state = $this->State->find('first',array('conditions' => array('State.code' => $orderdata['Order']['ship_to_stateprovince'])));
                    if(!empty($state))
                    {
                        $orderdata['Order']['state_id'] = $state['State']['id'];
                        $orderdata['Order']['ship_to_stateprovince'] = $state['State']['name'];
                    } else {
                        $orderdata['Order']['state_id'] = 'XZ';
                        $orderdata['Order']['ship_to_stateprovince'] = '';
                    }

                } else {
                    $orderdata['Order']['state_id'] = 'XZ';
                    $orderdata['Order']['ship_to_stateprovince'] = '';
                }

                $this->loadModel('Country');
                $country = $this->Country->find('first',array('conditions' => array('Country.code' => $orderdata['Order']['country_id'])));
                if(!empty($country))
                {
                    $orderdata['Order']['country_id'] = $country['Country']['id'];
                } else {
                    $this->errorstr = "Country ".$orderdata['Order']['country_id']." in line no. ".$orderdata['Order']['external_orderid']." does not exist";
                }

                $schannel = $this->Order->Schannel->find('first',array('conditions' => array('Schannel.name' => $orderdata['Order']['schannel_id'], 'Schannel.user_id' => $this->Auth->user('id'))));
                if(!empty($schannel))
                {
                    $orderdata['Order']['schannel_id'] = $schannel['Schannel']['id'];
                } else {
                    $this->errorstr = "Sales channel ".$orderdata['Order']['schannel_id']." in order ".$orderdata['Order']['external_orderid']." does not exist";
                }


                if (1 == 1)
                {
                    $ordercreated = $this->Order->save($orderdata);
                    $validationerror = $this->Order->validationErrors;
                    $this->errorstr = "The following valiation error occured:<BR> ";
                    foreach ($validationerror as $key=>$errortext)
                    {
                        $this->errorstr = $this->errorstr."- ".$errortext[0]."<BR>";
                    }

                    $this->orderid = $this->Order->id;
                } else {
                    $ordercreated = true;
                }

                if($ordercreated == false)
                {
                    if(!isset($this->errorstr))
                        $this->errorstr = "Please check your import file";
                        $this->Session->setFlash(__('Order could not be added. %s',$this->errorstr), 'admin/danger', array());
                } else {
                    foreach ($orderdata['OrdersLine'] as $key=>$orderlinedata)
                    {
                        $this->loadModel('Product');
                        $product = $this->Product->find('first',array('fields' => array('Product.id','Product.packaging_material_id'), 'conditions' => array('Product.sku' => $orderlinedata['sku'],'Product.user_id' => $this->Auth->user('id') )));
                        if(empty($product))
                        {
                            if(1 == 2)
                            {
                                $pid = $this->createproduct($orderdata);
                            } else {
                                $pid=1;
                            }
                        } else {
                            $pid = $product['Product']['id'];
                        }

                        $unit_price = $orderlinedata['unit_price'];

                        if(!isset($$orderlinedata['line_number']))
                        {
                            $orderldata['OrdersLine']['line_number'] = 10;
                        }

                        $linenumber = ( isset($orderldata['OrdersLine']['line_number']) ? $orderldata['OrdersLine']['line_number'] : 10 );
                        $this->loadModel('OrdersLine');
                        $orderldata['OrdersLine']['type'] = 1;
                        $orderldata['OrdersLine']['status_id'] = 1;
                        $orderldata['OrdersLine']['order_id'] = $this->orderid;
                        $orderldata['OrdersLine']['line_number'] = $linenumber;
                        $orderldata['OrdersLine']['warehouse_id'] = $this->Session->read('default_warehouse');
                        $orderldata['OrdersLine']['type'] = 1;
                        $orderldata['OrdersLine']['product_id'] = $pid;
                        $orderldata['OrdersLine']['sentqty'] = 0;

                        $orderldata['OrdersLine']['quantity'] = $orderlinedata['quantity'];
                        $orderldata['OrdersLine']['unit_price'] = $unit_price;
                        $orderldata['OrdersLine']['total_line'] = $orderlinedata['quantity'] * $orderlinedata['unit_price'] ;
                        $orderldata['OrdersLine']['sku'] = $orderlinedata['sku'];
                        $orderldata['OrdersLine']['foc'] = 0;
                        $orderldata['OrdersLine']['user_id'] = $this->Auth->user('id');
                        $newline = $this->OrdersLine->saveAll($orderldata);

                        if($pid != 1)
                        {
                            $this->loadModel('Product');
                            if(1 == 2)
                            {
                                $packmaterial = $this->Product->find('first', array('conditions' => array('Product.id' => $product['Product']['packaging_material_id'])));
                                if(!empty($packmaterial) && $this->Session->read('autopacking') == true)
                                {
                                    if($packmaterial['Product']['consumption'] == true )
                                    {
                                        $datapack = array(
                                            'OrdersLine' => array(
                                                'order_id' => $this->Order->id,
                                                'line_number' => 999999,
                                                'warehouse_id'  => $this->Session->read('default_warehouse'),
                                                'type' => 7,
                                                'product_id'  => $packmaterial['Product']['id'],
                                                'quantity' => 1,
                                                'unit_price' => $packmaterial['Product']['value'],
                                                'total_line' => $packmaterial['Product']['value'] * 1,
                                                'foc' => '',
                                                'user_id' => $this->Auth->user('id')
                                            )
                                        );
                                        // prepare the model for adding a new entry
                                        $this->OrdersLine->create();
                                        // save the data
                                        $orderlinecreated = $this->OrdersLine->save($datapack);
                                        $validationerror = $this->OrdersLine->validationErrors;
                                        $this->errorstr = "The following valiation error occured:<BR> ";
                                        foreach ($validationerror as $key=>$errortext)
                                        {
                                            $this->errorstr = $this->errorstr."- ".$errortext[0]."<BR>";
                                        }

                                        if($orderlinecreated == false)
                                        {
                                            if(!isset($this->errorstr)) $this->errorstr = "Please check your import file";
                                             $this->Session->setFlash(__('The orders could not be added. %s',$this->errorstr), 'admin/danger', array());
                                        }
                                    }
                                }
                            }

                        }
                    }
                        $this->Session->setFlash(__('Orders were created successfully'), 'admin/success', array());
                }

            }

        //}

        return $this->redirect(array('action' => 'index',1));
    }

    public function importebaycsv($fullfilename = null,$filename = null,$filesize = null) {
        $this->layout = 'mtrd';
        $options = array(
            'length' => 0,
            'delimiter' => ',',
            'enclosure' => '"',
            'escape' => '\\',
            'headers' => true,
            'text' => false,
        );
        $lastorderdata['Order']['external_orderid'] = '';
        $neworders = array();
        $content = $fullfilename;
        //$content = WWW_ROOT."uploads/ffff.csv";
        //echo "file is in import ".$content;
        $filedata = $this->Csv->import($content, array('Order.SalesRecordNumber','Order.UserId', 'Order.ship_to_customerid','Order.phonenumber', 'Order.ship_to_city','Order.ship_to_stateprovince','Order.ship_to_zip','Order.country_id','Order.external_orderid','Order.requested_delivery_date','Order.schannel_id','Order.shipping_costs','OrdersLine.line_number','OrdersLine.sku','OrdersLine.quantity','OrdersLine.unit_price'));
        $numords = count($filedata);
        //echo "ords lines".$numords;

        foreach ($filedata as $key=>$orderdata) {
            if($key > 0) {
                if(!is_numeric($orderdata['Importfile']['LineNumber'])) {
                    $errorstr = "Line number is missing";
                    $this->returnjsonerror($errorstr,$errorstr,$filesize);
                }
                //Debugger::dump($orderdata);
                foreach($orderdata['Order'] as $fkey=>$value) {
                    if(empty($value)) {
                        $errorstr = "In line number ".$orderdata['Importfile']['LineNumber']." value of ".$fkey." is missing";
                        $this->returnjsonerror($errorstr,$errorstr,$filesize);
                    }
                }
                foreach($orderdata['OrdersLine'] as $olkey=>$value) {
                    if(empty($value)) {
                        $errorstr = "In line number ".$orderdata['Importfile']['LineNumber']." value of ".$olkey." is missing";
                        $this->returnjsonerror($errorstr,$errorstr,$filesize);
                    }
                }

                $nextrefnum = ($key == ($numords-1) ? 1 : $filedata[$key+1]['Order']['external_orderid']);
                if ($orderdata['Order']['external_orderid'] != $nextrefnum) {
                    $this->Order->create();
                    $orderdata['Order']['user_id'] = $this->Auth->user('id');
                    $orderdata['Order']['dcop_user_id'] = $this->Auth->user('id');
                    $orderdata['Order']['status_id'] = 14;
                    $orderdata['Order']['ordertype_id'] = 1;

                    //$this->request->data('Order.user_id',$this->Auth->user('id'));
                    //$this->data('Order.status_id',1);

                    //If country US, state_id should be a valid US state
                    if($orderdata['Order']['country_id'] == 'US') {
                        //  Debugger::dump($orderdata);
                        $this->loadModel('State');
                        $state = $this->State->find('first',array('conditions' => array('State.code' => $orderdata['Order']['ship_to_stateprovince'])));
                        if(!empty($state))
                        {
                            $orderdata['Order']['state_id'] = $state['State']['id'];
                            $orderdata['Order']['ship_to_stateprovince'] = $state['State']['name'];
                        } else {
                            $errorstr = "State ".$orderdata['Order']['state_id']." in line no. ".$orderdata['Importfile']['LineNumber']." does not exist";
                            $this->returnjsonerror($errorstr,$errorstr,$filesize);
                        }

                    } else {
                        $orderdata['Order']['state_id'] = 'XZ';
                    }
                    
                    // Convert country and state names to id
                    $this->loadModel('Country');
                    $country = $this->Country->find('first',array('conditions' => array('Country.code' => $orderdata['Order']['country_id'])));
                    if(!empty($country)) {
                        $orderdata['Order']['country_id'] = $country['Country']['id'];
                    } else {
                        $errorstr = "Country ".$orderdata['Order']['country_id']." in line no. ".$orderdata['Importfile']['LineNumber']." does not exist";
                        $this->returnjsonerror($errorstr,$errorstr,$filesize);
                    }

                    // $this->loadModel('State');
                    $schannel = $this->Order->Schannel->find('first',array('conditions' => array('Schannel.name' => $orderdata['Order']['schannel_id'], 'Schannel.user_id' => $this->Auth->user('id'))));
                    if(!empty($schannel)) {
                        $orderdata['Order']['schannel_id'] = $schannel['Schannel']['id'];
                    } else {
                        $errorstr = "Sales channel ".$orderdata['Order']['schannel_id']." in line no. ".$orderdata['Importfile']['LineNumber']." does not exist";
                        $this->returnjsonerror($errorstr,$errorstr,$filesize);
                    }
                    $this->Order->save($orderdata);
                }
                $this->loadModel('Product');
                $product = $this->Product->find('first',array('fields' => array('Product.id','Product.packaging_material_id'), 'conditions' => array('Product.sku' => $orderdata['OrdersLine']['sku'],'Product.user_id' => $this->Auth->user('id') )));
                if(empty($product)) {
                    $pid=1;
                } else {
                    $pid = $product['Product']['id'];
                }

                $this->loadModel('OrdersLine');
                $orderldata['OrdersLine']['order_id'] = $this->Order->id;
                $orderldata['OrdersLine']['line_number'] = $orderdata['OrdersLine']['line_number'];
                $orderldata['OrdersLine']['warehouse_id'] = $this->Session->read('default_warehouse');
                $orderldata['OrdersLine']['type'] = 1;
                $orderldata['OrdersLine']['product_id'] = $pid;
                $orderldata['OrdersLine']['sentqty'] = 0;
                $orderldata['OrdersLine']['quantity'] = $orderdata['OrdersLine']['quantity'];
                $orderldata['OrdersLine']['unit_price'] = $orderdata['OrdersLine']['unit_price'];;
                $orderldata['OrdersLine']['total_line'] = $orderldata['OrdersLine']['quantity'] * $orderldata['OrdersLine']['unit_price'] ;
                $orderldata['OrdersLine']['sku'] = $orderdata['OrdersLine']['sku'];
                $orderldata['OrdersLine']['foc'] = 0;
                $orderldata['OrdersLine']['user_id'] = $this->Auth->user('id');
                $this->OrdersLine->saveAll($orderldata);

                if($pid != 1) {
                    $this->loadModel('Product');
                    $packmaterial = $this->Product->find('first', array('conditions' => array('Product.id' => $product['Product']['packaging_material_id'])));
                    if(!empty($packmaterial)) {
                        if($packmaterial['Product']['consumption'] == true ) {
                            $datapack = array(
                                'OrdersLine' => array(
                                    'order_id' => $this->Order->id,
                                    'line_number' => 999999,
                                    'warehouse_id'  => $this->Session->read('default_warehouse'),
                                    'type' => 7,
                                    'product_id'  => $packmaterial['Product']['id'],
                                    'quantity' => 1,
                                    'unit_price' => $packmaterial['Product']['value'],
                                    'total_line' => $packmaterial['Product']['value'] * 1,
                                    'foc' => '',
                                    'user_id' => $this->Auth->user('id')
                                )
                            );
                            $this->OrdersLine->create();
                            $this->OrdersLine->save($datapack);
                        }
                    }
                }
            }
        }

        $filesg = array( 'files' => array(array(
            "name" => $filename,
            "size" => $filesize,
            "thumbnailUrl" => "/theme/Mtro/assets/admin/layout/img/csv.png"
        )));
        header('Content-Type: application/json');
        echo json_encode($filesg,JSON_PRETTY_PRINT);
        exit();
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function getExternalOrder($id = null) {
        $this->loadModel('OrdersLine');
        $orders_lines = $this->OrdersLine->find('all', array('conditions' => array('OrdersLine.order_id' => $id)));
        $this->set('orders_lines', $orders_lines,$this->Paginator->paginate());
        if (!$this->Order->exists($id)) {
            throw new NotFoundException(__('Invalid order'));
        }
        $options = array('conditions' => array('Order.' . $this->Order->primaryKey => $id));
        $this->set('order', $this->Order->find('first', $options));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->Order->exists($id)) {
            throw new NotFoundException(__('Invalid order'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Order->save($this->request->data)) {
                $this->Session->setFlash(__('The Order has been saved.'), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Order could not be saved. Please, try again.'), 'admin/danger', array());
            }
        } else {
            $options = array('conditions' => array('Order.' . $this->Order->primaryKey => $id));
            $this->request->data = $this->Order->find('first', $options);
        }
        $users = $this->Order->User->find('list');
        $this->set(compact('users'));
    }

    /**
     * unrelease method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function unrelease($id = null) {
        $this->Order->id = $id;
        $order = $this->Order->find('first', array('conditions' => array('Order.id' => $id)));
        if (!$order) {
            throw new NotFoundException(__('Invalid order'));
        }

        $redirect = [];
        if($order['Order']['ordertype_id'] == 1) {
            $redirect['controller'] = 'salesorders';
        } else {
            $redirect['controller'] = 'replorders';
        }
        if($this->request->query('f') == 'd') {
            $redirect['action'] = 'details';
            $redirect[] = $id;
        } else {
            $redirect['action'] = 'index';
        }
        $type = $this->types[$order['Order']['ordertype_id']];

        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to delete not own order
            $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
            $order_lines = $this->Order->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'callbacks' => false
            ));
            if(count($order_lines) != count($order['OrdersLine'])) {
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('You can\'t unrelease order number %s. It have products for which you have no access.', $id);
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('You can\'t unrelease order number %s. It have products for which you have no access.',$id), 'admin/danger');
                    return $this->redirect($redirect);
                }
            }
        }

        if($order['Order']['status_id'] !=2 && count($order['OrdersLine']) > 0) {
            if(isset($this->request->data['order_id'])) {
                $response['action'] = 'error';
                $response['message'] = __('Order no. %s cannot be unreleased because it is not in status Released',$id);
                echo json_encode($response);
                exit;
            } else {
                $this->Session->setFlash(__('Order no. %s cannot be unreleased because it is not in status Released',$id),'admin/danger',array());
                return $this->redirect($redirect);
            }
        }
        $this->request->data('Order.status_id',14);
        if ($this->Order->save($this->request->data)) {
            $this->EventRegister->addEvent(2,1,$this->Auth->user('id'),$this->Order->id);
            $this->Order->OrdersLine->updateAll(
                    array('OrdersLine.status_id' => 1),
                    array('OrdersLine.order_id' => $id));

            if(isset($this->request->data['order_id'])) {
                $response['action'] = 'success';
                $response['message'] = __('Order no. %s status changed to Draft',$id);
                echo json_encode($response);
                exit;
            } else {
                $this->Session->setFlash(__('Order no. %s status changed to Draft',$id), 'admin/success');
                return $this->redirect($redirect);
            }
        } else {
            if(isset($this->request->data['order_id'])) {
                $response['action'] = 'error';
                $response['message'] = __('Order no. %s  - could not change status',$id);
                echo json_encode($response);
                exit;
            } else {
                $this->Session->setFlash(__('Order no. %s  - could not change status',$id), 'admin/danger');
                return $this->redirect($redirect);
            }
        }
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->Order->id = $id;
        $order = $this->Order->find('first', array('conditions' => array('Order.id' => $id)));
        if (!$order) {
            throw new NotFoundException(__('Invalid order'));
        }

        $redirect = [];
        if($order['Order']['ordertype_id'] == 1) {
            $redirect['controller'] = 'salesorders';
        } else {
            $redirect['controller'] = 'replorders';
        }
        $redirect['action'] = 'index';
        $type = $this->types[$order['Order']['ordertype_id']];

        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to delete not own order
            $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
            $order_lines = $this->Order->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'callbacks' => false
            ));
            if(count($order_lines) != count($order['OrdersLine'])) {
                $this->Session->setFlash(__('You can\'t delete order number %s. It have products for which you have no access.',$id),'default',array('class'=>'alert alert-danger'));
                return $this->redirect($redirect);
            }
        }

        if($order['Order']['status_id'] != 14) {
            if(isset($this->request->data['order_id'])) {
                $response['action'] = 'error';
                $response['message'] = __('Orders could not be deleted is not draft', $id);
                echo json_encode($response);
                exit;
            } else {
                $this->Session->setFlash(__('Orders could not be deleted is not draft', $id),'admin/danger');
                return $this->redirect($redirect);
            }
        }

        $this->request->allowMethod('post', 'delete');
        if ($this->Order->delete()) {
            if(isset($this->request->data['order_id'])) {
                $response['action'] = 'success';
                $response['message'] = __('Order number %s has been deleted', $id);
                echo json_encode($response);
                exit;
            } else {
                $this->Session->setFlash(__('Order number %s, reference order number %s has been deleted.',$id,$order['Order']['external_orderid']),'default',array('class'=>'alert alert-success'));
            }
        } else {
            if(isset($this->request->data['order_id'])) {
                $response['action'] = 'error';
                $response['message'] = __('Order number %s could not be deleted. Please, try again.', $id);
                echo json_encode($response);
                exit;
            } else {
                $this->Session->setFlash(__('Order number %s could not be deleted. Please, try again.',$id),'default',array('class'=>'alert alert-danger'));
            }
        }
        return $this->redirect($redirect);
    }

    /**
     * delete_multiple method
     *
     * @throws NotFoundException
     * @param 
     * @return void
     */
    public function delete_multiple() {
        if($this->request->is('post')) {
            $action = 'succes';
            $total = 0;
            $success = 0;
            $error = 0;
            $report = [];

            if(!empty($this->request->data['Order']['order_id'])) {
                $ids = explode(',', $this->request->data['Order']['order_id']);
                $total = count($ids);
                foreach($ids as $id) {
                    $this->Order->id = $id;
                    $order = $this->Order->find('first', array('conditions' => array('Order.id' => $id, 'Order.status_id' => 14)));

                    if(empty($order)) {
                        $error++;
                        $action = 'warning';
                        $report['not_draft'][] = $id;
                        continue;
                        /*if(isset($this->request->data['ajax'])) {
                            $response['action'] = 'error';
                            $response['message'] = __('Orders could not be deleted as per %s order is not draft', $id);
                            echo json_encode($response);
                            exit;
                        } else {
                            $this->Session->setFlash(__('Orders could not be deleted as per %s order is not draft', $id), 'admin/danger', array());
                            return $this->redirect($this->referer());
                        }*/
                    }

                    $type = $this->types[$order['Order']['ordertype_id']];
                    
                    if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to release not own order
                        $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
                        $order_lines = $this->Order->OrdersLine->find('all', array(
                            'order' => array('OrdersLine.line_number' => 'asc'),
                            'conditions' => array('OrdersLine.order_id' => $order['Order']['user_id'], 'OrdersLine.product_id' => array_keys($products)),
                            'callbacks' => false
                        ));
                        if(count($order_lines) != count($order['OrdersLine'])) {
                            $error++;
                            $action = 'warning';
                            $report['not_allowed'][] = $id;
                            continue;
                            /*if(isset($this->request->data['ajax'])) {
                                $response['action'] = 'error';
                                $response['message'] = __('You can\'t delete order number %s. It have products for which you have no access.', $id);
                                echo json_encode($response);
                                exit;
                            } else {
                                $this->Session->setFlash(__('You can\'t delete order number %s. It have products for which you have no access.',$order['Order']['id']),'admin/danger');
                                return $this->redirect($this->referer());
                            }*/
                        }
                    }

                    #$this->request->allowMethod('post', 'delete');
                    if(!$this->Order->delete()) {
                        $error++;
                        $action = 'warning';
                        $report['not_saved'][] = $id;
                        /*if(isset($this->request->data['ajax'])) {
                            $response['action'] = 'error';
                            $response['message'] = __('Order number %s could not be deleted. Please, try again.', $id);
                            echo json_encode($response);
                            exit;
                        } else {
                            $this->Session->setFlash(__('Order number %s could not be deleted. Please, try again.',$order['Order']['id']),'admin/danger');
                            return $this->redirect($this->referer());
                        }*/
                    } else {
                        $success++;
                        $report['success'][] = $id;
                    }
                }

                if($success == 0) {
                    $action = 'danger';
                }

                $msg = __('Total selected: %s. Success deleted: %s. Ignored: %s.', $total, $success, $error);

                if(isset($this->request->data['ajax'])) {
                    $response['action'] = $action;
                    $response['total'] = $total;
                    $response['success'] = $success;
                    $response['error'] = $error;
                    $response['report'] = $report;
                    $response['message'] = $msg;
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash($msg,'admin/'. $action);
                    return $this->redirect($this->referer());
                }

            } else {
                if(isset($this->request->data['ajax'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('Please select orders');
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('Please select orders'),'admin/danger');
                    return $this->redirect($this->referer());
                }
            }
        } else {
            if(isset($this->request->data['ajax'])) {
                $response['action'] = 'error';
                $response['message'] = __('The Orders could not be deleted. Please, try again.');
                echo json_encode($response);
                exit;
            } else {
                $this->Session->setFlash(__('The Orders could not be deleted. Please, try again.'), 'admin/danger', array());
                return $this->redirect($this->referer());
            }
        }
    }

    /**
     * cancel_multiple method
     *
     * @throws NotFoundException
     * @param 
     * @return void
     */
    public function cancel_multiple() {
        if($this->request->is('post')) {
            $action = 'succes';
            $total = 0;
            $success = 0;
            $error = 0;
            $report = [];

            if(!empty($this->request->data['Order']['order_id'])) {
                $ids = explode(',', $this->request->data['Order']['order_id']);
                $total = count($ids);
                foreach($ids as $id) {
                    $this->Order->id = $id;
                    $order = $this->Order->find('first', array('conditions' => array('Order.id' => $id)));

                    if($order['Order']['status_id'] != 4) {
                        $error++;
                        $action = 'warning';
                        $report['not_completed'][] = $id;
                        continue;
                    }

                    $type = $this->types[$order['Order']['ordertype_id']];
                    
                    if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to release not own order
                        $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
                        $order_lines = $this->Order->OrdersLine->find('all', array(
                            'order' => array('OrdersLine.line_number' => 'asc'),
                            'conditions' => array('OrdersLine.order_id' => $order['Order']['user_id'], 'OrdersLine.product_id' => array_keys($products)),
                            'callbacks' => false
                        ));
                        if(count($order_lines) != count($order['OrdersLine'])) {
                            $error++;
                            $action = 'warning';
                            $report['not_allowed'][] = $id;
                            continue;
                        }
                    }

                    if($order['Order']['ordertype_id'] == 2) {
                        unset($this->Order->validate['schannel_id']);
                    }
                    $order['Order']['status_id'] = '50';
                    if(!$this->Order->save($order)) {
                        $error++;
                        $action = 'warning';
                        $report['not_saved'][] = $id;
                    } else {
                        $success++;
                        $report['success'][] = $id;
                    }

                }

                if($success == 0) {
                    $action = 'danger';
                }

                $msg = __('Total selected: %s. Success canceled: %s. Ignored: %s.', $total, $success, $error);

                if(isset($this->request->data['ajax'])) {
                    $response['action'] = $action;
                    $response['total'] = $total;
                    $response['success'] = $success;
                    $response['error'] = $error;
                    $response['report'] = $report;
                    $response['message'] = $msg;
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash($msg,'admin/'. $action);
                    return $this->redirect($this->referer());
                }
            } else {
                if(isset($this->request->data['ajax'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('Please select orders');
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('Please select orders'),'admin/danger');
                    return $this->redirect($this->referer());
                }
            }
        } else {
            if(isset($this->request->data['ajax'])) {
                $response['action'] = 'error';
                $response['message'] = __('The Orders could not be deleted. Please, try again.');
                echo json_encode($response);
                exit;
            } else {
                $this->Session->setFlash(__('The Orders could not be deleted. Please, try again.'), 'admin/danger', array());
                return $this->redirect($this->referer());
            }
        }
    }


    /**
     * release method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function confirm_release($id = null) {
        $this->layout = false;
        $this->set(compact('id'));
    }
    /**
     * release method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function release($id = null) {
        $this->Order->id = $id;

        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id),
            'contain' => array('OrdersLine', 'OrdersLine.Product')
        ));

        if(empty($order)) {
            throw new NotFoundException(__('Invalid order'));
        }

        $redirect = [];
        if($order['Order']['ordertype_id'] == 1) {
            $redirect['controller'] = 'salesorders';
        } else {
            $redirect['controller'] = 'replorders';
        }
        if($this->request->query('f') == 'd') {
            $redirect['action'] = 'details';
            $redirect[] = $id;
        } else {
            $redirect['action'] = 'index';
        }
        $type = $this->types[$order['Order']['ordertype_id']];

        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to release not own order
            $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
            $order_lines = $this->Order->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'callbacks' => false
            ));
            if(count($order_lines) != count($order['OrdersLine'])) {
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('You can\'t released order number %s. It have products for which you have no access.',$id);
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('You can\'t released order number %s. It have products for which you have no access.',$id), 'admin/danger');
                    return $this->redirect($redirect);
                }
            }
        }

        if(empty($order['OrdersLine']) && !$order['Order']['blanket']) {
            if(isset($this->request->data['order_id'])) {
                $response['action'] = 'error';
                $response['message'] = __('Order number %s could not be released as it has no order lines',$id);
                echo json_encode($response);
                exit;
            } else {
                $this->Session->setFlash(__('Order number %s could not be released as it has no order lines',$id), 'admin/danger');
                return $this->redirect($redirect);
            }
        }
        
        foreach ($order['OrdersLine'] as $orderline) {
            if($orderline['product_id'] == 0 || $orderline['product_id'] == 1) {
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('Order number %s could not be released as it has lines with no product',$id);
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('Order number %s could not be released as it has lines with no product',$id), 'admin/danger');
                    return $this->redirect($redirect);
                }
            }
            if(empty($orderline['sku'])) {
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('Orders could not be released as it has lines with no sku',$id);
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('Orders could not be released as it has lines with no sku',$id), 'admin/danger');
                    return $this->redirect($redirect);
                }
            }
            if($orderline['Product']['status_id'] == 13 || $orderline['Product']['deleted'] == 1 ) {
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('Orders could not be released as it has lines with blocked or deleted products',$id);
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('Orders could not be released as it has lines with blocked or deleted products',$id), 'admin/danger');
                    return $this->redirect($redirect);
                }
            }
        }

        $this->request->data('Order.status_id',2);
        if ($this->Order->save($this->request->data)) {
            $this->EventRegister->addEvent(2,2,$this->Auth->user('id'),$this->Order->id);
            $this->loadModel('OrdersLine');
            $this->OrdersLine->updateAll(
                array('OrdersLine.status_id' => 2),
                array('OrdersLine.order_id' => $id));

            $this->Order->distshipcosts($id);
            if(isset($this->request->data['order_id'])) {
                $response['action'] = 'success';
                $response['message'] = __('Order number %s status set to Released',$id);
                echo json_encode($response);
                exit;
            } else {
                $this->Session->setFlash(__('Order number %s status set to Released',$id), 'admin/success');
                return $this->redirect($redirect);
            }
        } else {
            if(isset($this->request->data['order_id'])) {
                $response['action'] = 'error';
                $response['message'] = __('The order could not be saved. Please, try again.');
                echo json_encode($response);
                exit;
            } else {
                $this->Session->setFlash(__('The order could not be saved. Please, try again.'), 'admin/danger');
                return $this->redirect($redirect);
            }
        }
    }

    /**
     *
     *
     *
     */
    public function release_multiple() {
        if($this->request->is('post')) {
            if(!empty($this->request->data['Order']['id'])) {
                $ids = explode(',', $this->request->data['Order']['id']);
                foreach($ids as $id) {
                    $this->Order->id = $id;
                    //$order = $this->Order->find('first', array('conditions' => array('Order.id' => $id, 'Order.status_id' => 14)));
                    $order = $this->Order->find('first', array(
                        'conditions' => array('Order.id' => $id, 'Order.status_id' => 14),
                        'contain' => array('OrdersLine', 'OrdersLine.Product')
                    ));

                    if(empty($order)) {
                        if(isset($this->request->data['ajax'])) {
                            $response['action'] = 'error';
                            $response['message'] = __('Orders could not be released as per %s order is not draft', $id);
                            echo json_encode($response);
                            exit;
                        } else {
                            $this->Session->setFlash(__('Orders could not be released as per %s order is not draft', $id), 'admin/danger', array());
                            return $this->redirect($this->referer());
                        }
                    }
                    $type = $this->types[$order['Order']['ordertype_id']];
                    if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to release not own order
                        $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
                        $order_lines = $this->Order->OrdersLine->find('all', array(
                            'order' => array('OrdersLine.line_number' => 'asc'),
                            'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                            'callbacks' => false
                        ));
                        if(count($order_lines) != count($order['OrdersLine'])) {
                            if(isset($this->request->data['ajax'])) {
                                $response['action'] = 'error';
                                $response['message'] = __('You can\'t released order number %s. It have products for which you have no access.',$id);
                                echo json_encode($response);
                                exit;
                            } else {
                                $this->Session->setFlash(__('You can\'t released order number %s. It have products for which you have no access.',$id), 'admin/danger');
                                return $this->redirect($this->referer());
                            }
                        }
                    }

                    if(empty($order['OrdersLine'])) {
                        if(isset($this->request->data['ajax'])) {
                            $response['action'] = 'error';
                            $response['message'] = __('Orders could not be released as per %s has no order lines',$id);
                            echo json_encode($response);
                            exit;
                        } else {
                            $this->Session->setFlash(__('Orders could not be released as per %s has no order lines',$id), 'admin/danger', array());;
                            return $this->redirect($this->referer());
                        }
                    }
                    $haspack = false;
                    foreach ($order['OrdersLine'] as $orderline) {
                        if($orderline['type'] == 4)
                            $haspack = true;
                        if($orderline['product_id'] == 0) {
                            if(isset($this->request->data['ajax'])) {
                                $response['action'] = 'error';
                                $response['message'] = __('Orders could not be released as it has lines with no product',$id);
                                echo json_encode($response);
                                exit;
                            } else {
                                $this->Session->setFlash(__('Orders could not be released as it has lines with no product',$id), 'admin/danger');
                                return $this->redirect($this->referer());
                            }
                        }
                        if(empty($orderline['sku'])) {
                            if(isset($this->request->data['ajax'])) {
                                $response['action'] = 'error';
                                $response['message'] = __('Orders could not be released as it has lines with no sku',$id);
                                echo json_encode($response);
                                exit;
                            } else {
                                $this->Session->setFlash(__('Orders could not be released as it has lines with no sku',$id), 'admin/danger');
                                return $this->redirect($this->referer());
                            }
                        }

                        if($orderline['Product']['status_id'] == 13 || $orderline['Product']['deleted'] == 1 ) {
                            if(isset($this->request->data['ajax'])) {
                                $response['action'] = 'error';
                                $response['message'] = __('Orders could not be released as it has lines with blocked or deleted products',$id);
                                echo json_encode($response);
                                exit;
                            } else {
                                $this->Session->setFlash(__('Orders could not be released as it has lines with blocked or deleted products'), 'admin/danger');
                                return $this->redirect($this->referer());
                            }
                        }
                    }
                    $data['id'] = $id;
                    $data['status_id'] = 2;

                    $this->Order->create();
                    if ($this->Order->save($data, array('validate' => false))) {
                        $this->EventRegister->addEvent(2,2,$this->Auth->user('id'),$this->Order->id);
                        $this->loadModel('OrdersLine');
                        $this->OrdersLine->updateAll(
                        array('OrdersLine.status_id' => 2),
                        array('OrdersLine.order_id' => $id));
                        $this->Order->distshipcosts($id);
                    }
                }
                if(isset($this->request->data['ajax'])) {
                    $response['action'] = 'success';
                    $response['message'] = __('Orders status set to Released.');
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('Orders status set to Released'), 'admin/success', array());
                    return $this->redirect($this->referer());
                }
            } else {
                if(isset($this->request->data['ajax'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('The Orders could not be released. Please, try again.',$id);
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('The Orders could not be released. Please, try again.'), 'admin/danger', array());
                    return $this->redirect($this->referer());
                }
            }
        } else {
            throw new NotFoundException(__('Page Not Found'));
        }
    }

    public function distshipcosts($ord_id = null) {
        if(!isset($ord_id)) {
            $ord_id = $this->request->params['pass'][0];
        }
        $this->Order->distshipcosts($ord_id);
        $this->Session->setFlash(__('Shipping Costs Calculated.'), 'admin/success', array());
        return $this->redirect('details/'. $ord_id);
    }

    public function sendpo($id = null) {
        $state = '';
        $this->Order->id = $id;
        $order = $this->Order->find('first', array('conditions' => array('Order.id' => $id)));
        if($order['Country']['code'] == 'US')
        {
            $state = ",".$order['State']['name'];
        }
        $addr = $order['Order']['ship_to_street']."<BR>".$order['Order']['ship_to_city']."<BR>".$order['Order']['ship_to_zip'].$state."<BR>".$order['Country']['name'];


        $ship_costs = $order['Order']['shipping_costs'];
        $reference_number = $order['Order']['external_orderid'];

        $this->loadModel('OrdersLine');
        $orders_lines = $this->OrdersLine->find('all', array('conditions' => array('OrdersLine.order_id' => $id,'OrdersLine.user_id' => $this->Auth->user('id'))));

        $Email = new CakeEmail();
        $Email->config('smtp');
        $Email-> emailFormat('html');
        $Email->template('po', 'po');

        $Email->from(array('technoyos@gmail.com' => 'My Site'));
        $Email->to('technoyos@gmail.com');
        $Email->subject('Order Confirmation');
        //$Email->viewVars(array('addr' => $addr,'orders_lines' => $orders_lines, 'quantity' => $quantity,'ship_costs'=>$ship_costs,'unit_price' => $unit_price,'total_line' => $total_line,'description' => $description));
        $Email->viewVars(array('addr' => $addr,'orders_lines' => $orders_lines,'ship_costs'=>$ship_costs,'reference_number' => $reference_number));
        $Email->send('Hello');
        $this->Session->setFlash(__('Mail with order details have been sent to vendor'), 'admin/success', array());
        return $this->redirect(array('action' => 'index',2));

    }

    public function complete($id = null) {
        $this->Order->id = $id;
        $this->request->data('Order.status_id',4);
        //$order = $this->Order->find('first', array('conditions' => array('Order.id' => $id)));
        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id),
            'contain' => array('OrdersLine' => array('fields' => array('OrdersLine.quantity', 'OrdersLine.sentqty', 'OrdersLine.receivedqty'))),
            'fields' => array('Order.id', 'Order.user_id', 'Order.ordertype_id', 'Order.external_orderid')
        ));

        if($order['Order']['ordertype_id'] == 1) {
            $key = 'sentqty';
        } else {
            $key = 'receivedqty';
        }
        $is_ready = true;
        foreach ($order['OrdersLine'] as $line) {
            if($line[$key] < $line['quantity']) {
                $is_ready = false;
            }
        }


        $redirect = [];
        if($order['Order']['ordertype_id'] == 1) {
            $redirect['controller'] = 'salesorders';
        } else {
            $redirect['controller'] = 'replorders';
        }
        if($this->request->query('f') == 'd') {
            $redirect['action'] = 'details';
            $redirect[] = $id;
        } else {
            $redirect['action'] = 'index';
        }

        $type = $this->types[$order['Order']['ordertype_id']];

        $is_allow = true;
        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to delete not own order
            $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
            $order_lines = $this->Order->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'callbacks' => false
            ));
            if(count($order_lines) != count($order['OrdersLine'])) {
                $is_allow = false;
            }
        }

        if ($this->request->is(array('post', 'put'))) {
            if(!$is_allow) {
                $this->Session->setFlash(__('You can\'t complete order number %s. It have products for which you have no access.',$id), 'admin/danger');
                return $this->redirect($redirect);
            }
            if ($this->Order->save($this->request->data)) {
                $this->EventRegister->addEvent(2,4,$this->Auth->user('id'),$this->Order->id);
                $this->updateshipmentprocess($id);
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'success';
                    $response['message'] = __('Order status set to Completed');
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('Order status set to Completed'), 'admin/success', array());
                    return $this->redirect($redirect);
                }
            } else {
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('Order status could not be set to Completed. Please try again.');
                    echo json_encode($response);
                    exit;
                } else {
                    return $this->redirect($redirect);
                }
            }
        }
        $this->set(compact('order', 'is_allow', 'is_ready'));
    }

    public function todraft($id = null) {
        $this->Order->id = $id;
        $this->request->data('Order.status_id',14);
        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id),
            #'contain' => array('OrdersLine' => array('fields' => array('OrdersLine.quantity', 'OrdersLine.sentqty', 'OrdersLine.receivedqty'))),
            #'fields' => array('Order.id', 'Order.user_id', 'Order.ordertype_id', 'Order.external_orderid')

            'contain' => array(
                'OrdersLine' => array('fields' => array('OrdersLine.id', 'OrdersLine.warehouse_id', 'OrdersLine.quantity', 'OrdersLine.sentqty', 'OrdersLine.receivedqty')),
                'OrdersLine.Product' => array('fields' => array('id', 'name', 'issue_location', 'status_id', 'deleted'))
            ),
            'fields' => array('Order.id', 'Order.user_id', 'Order.status_id', 'Order.ordertype_id', 'Order.external_orderid')
        ));


        $redirect = [];
        if($order['Order']['ordertype_id'] == 1) {
            $redirect['controller'] = 'salesorders';
        } else {
            $redirect['controller'] = 'replorders';
        }
        if($this->request->query('f') == 'd') {
            $redirect['action'] = 'details';
            $redirect[] = $id;
        } else {
            $redirect['action'] = 'index';
        }

        $type = $this->types[$order['Order']['ordertype_id']];

        $is_allow = true;
        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to delete not own order
            $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
            $order_lines = $this->Order->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'callbacks' => false
            ));
            if(count($order_lines) != count($order['OrdersLine'])) {
                $is_allow = false;
            }
        }

        if ($this->request->is(array('post', 'put'))) {
            if(!$is_allow) {
                $this->Session->setFlash(__('You can\'t move to draft order number %s. It have products for which you have no access.',$id), 'admin/danger');
                return $this->redirect($redirect);
            }

            $success = 1;
            if($this->request->data['return_issue']) {
                $this->loadModel('Inventory');
                $this->loadModel('OrdersLine');

                $all_data_ord = [];
                $all_data_inv = [];
                $inv_qty = [];

                foreach($order['OrdersLine'] as $orderline) {
                    if($orderline['Product']['status_id'] == 13 || $orderline['Product']['deleted'] == 1) {
                        continue;
                    }
                    if($orderline['sentqty'] > 0) {
                        $inventory = $this->InventoryManager->getInventory($orderline['product_id'], $orderline['warehouse_id']);
                        if(empty($inventory)) {
                            continue;
                        }
                        if(!isset($inv_qty[$inventory['Inventory']['id']])) {
                            $inv_qty[$inventory['Inventory']['id']] = $inventory['Inventory']['quantity'];
                        }
                    
                        $data_ord = [];
                        $data_ord['OrdersLine']['id'] = $orderline['id'];
                        $data_ord['OrdersLine']['sentqty'] = 0;
                        $data_ord['OrdersLine']['dcop_user_id'] = $this->Auth->user('id');
                        $all_data_ord[] = $data_ord;

                        $poststock = $inv_qty[$inventory['Inventory']['id']] = $inv_qty[$inventory['Inventory']['id']] - $orderline['sentqty'];

                        $data_inv = [];
                        $data_inv['Inventory']['id'] = $inventory['Inventory']['id'];
                        $data_inv['Inventory']['quantity'] = $poststock;
                        
                        $all_data_inv[$data_inv['Inventory']['id']] = $data_inv;
                    }
                }

                if($all_data_ord) {
                    $success = 0;
                    $ds = $this->OrdersLine->getDataSource();
                    $ds->begin();
                    if($this->OrdersLine->saveAll($all_data_ord)) {
                        if($this->Inventory->saveAll($all_data_inv)) {
                            $ds->commit();
                            $success = 1;
                        } else {
                            $ds->rollback();
                        }
                    }
                }
            }
            if($success) {
                if ($this->Order->save($this->request->data)) {
                    $this->EventRegister->addEvent(2,14,$this->Auth->user('id'),$this->Order->id);
                    //$this->updateshipmentprocess($id);
                    if(isset($this->request->data['order_id'])) {
                        $response['action'] = 'success';
                        $response['message'] = __('Order status changed to Draft');
                        echo json_encode($response);
                        exit;
                    } else {
                        $this->Session->setFlash(__('Order status changed to Draft'), 'admin/success', array());
                        return $this->redirect($redirect);
                    }
                } else {
                    if(isset($this->request->data['order_id'])) {
                        $response['action'] = 'error';
                        $response['message'] = __('Order status could not be set to Draft. Please try again.');
                        echo json_encode($response);
                        exit;
                    } else {
                        return $this->redirect($redirect);
                    }
                }
            } else {
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['msg'] = __('Can\'t return issue quantities to stock.');
                } else {
                    return $this->redirect($redirect);
                }
            }
        }
        $this->set(compact('order', 'is_allow'));
    }

    public function cancel($id = null) {
        $this->Order->id = $id;
        $this->request->data('Order.status_id',50);
        
        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id),
            'contain' => array(
                'OrdersLine' => array('fields' => array('OrdersLine.id', 'OrdersLine.warehouse_id', 'OrdersLine.quantity', 'OrdersLine.sentqty', 'OrdersLine.receivedqty')),
                'OrdersLine.Product' => array('fields' => array('id', 'name', 'issue_location', 'status_id', 'deleted'))
            ),
            'fields' => array('Order.id', 'Order.user_id', 'Order.status_id', 'Order.ordertype_id', 'Order.external_orderid')
        ));

        $redirect = [];
        if($order['Order']['ordertype_id'] == 1) {
            $redirect['controller'] = 'salesorders';
        } else {
            $redirect['controller'] = 'replorders';
        }
        if($this->request->query('f') == 'd') {
            $redirect['action'] = 'details';
            $redirect[] = $id;
        } else {
            $redirect['action'] = 'index';
        }

        // 
        $is_completed = true;
        if($order['Order']['ordertype_id'] != 1 || $order['Order']['status_id'] != 4) {
            $is_completed = false;
        }

        $type = $this->types[$order['Order']['ordertype_id']];

        $is_allow = true;
        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to delete not own order
            $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
            $order_lines = $this->Order->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'callbacks' => false
            ));
            if(count($order_lines) != count($order['OrdersLine'])) {
                $is_allow = false;
            }
        }

        if ($this->request->is(array('post', 'put'))) {
            if(!$is_allow) {
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('You can\'t cancel order number %s. It have products for which you have no access.',$id);
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('You can\'t cancel order number %s. It have products for which you have no access.',$id), 'admin/danger');
                    return $this->redirect($redirect);
                }
            }

            if(!$is_completed) {
                // you can cancel only Complete Sales Orders
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('Error: you can Cancel only Complete Sales Orders.');
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('Error: you can cancel only Complete Sales Orders.',$id), 'admin/danger');
                    return redirect($redirect);
                }
            }

            $success = 1;
            if($this->request->data['return_issue']) {
                $this->loadModel('Inventory');
                $this->loadModel('OrdersLine');

                $all_data_ord = [];
                $all_data_inv = [];
                $inv_qty = [];

                foreach($order['OrdersLine'] as $orderline) {
                    if($orderline['Product']['status_id'] == 13 || $orderline['Product']['deleted'] == 1) {
                        continue;
                    }
                    if($orderline['sentqty'] > 0) {
                        $inventory = $this->InventoryManager->getInventory($orderline['product_id'], $orderline['warehouse_id']);
                        if(empty($inventory)) {
                            continue;
                        }
                        if(!isset($inv_qty[$inventory['Inventory']['id']])) {
                            $inv_qty[$inventory['Inventory']['id']] = $inventory['Inventory']['quantity'];
                        }
                    
                        $data_ord = [];
                        $data_ord['OrdersLine']['id'] = $orderline['id'];
                        $data_ord['OrdersLine']['sentqty'] = 0;
                        $data_ord['OrdersLine']['dcop_user_id'] = $this->Auth->user('id');
                        $all_data_ord[] = $data_ord;

                        $poststock = $inv_qty[$inventory['Inventory']['id']] = $inv_qty[$inventory['Inventory']['id']] - $orderline['sentqty'];

                        $data_inv = [];
                        $data_inv['Inventory']['id'] = $inventory['Inventory']['id'];
                        $data_inv['Inventory']['quantity'] = $poststock;
                        
                        $all_data_inv[$data_inv['Inventory']['id']] = $data_inv;
                    }
                }

                if($all_data_ord) {
                    $success = 0;
                    $ds = $this->OrdersLine->getDataSource();
                    $ds->begin();
                    if($this->OrdersLine->saveAll($all_data_ord)) {
                        if($this->Inventory->saveAll($all_data_inv)) {
                            $ds->commit();
                            $success = 1;
                        } else {
                            $ds->rollback();
                        }
                    }
                }
            }

            if($success) {
                if ($this->Order->save($this->request->data)) {
                    unset($this->request->data['return_issue']);
                    //unset($this->request->data['order_id']);
                    $this->EventRegister->addEvent(2,4,$this->Auth->user('id'),$this->Order->id);
                    if(isset($this->request->data['order_id'])) {
                        $response['action'] = 'success';
                        $response['message'] = __('Order status set to Canceled');
                        echo json_encode($response);
                        exit;
                    } else {
                        $this->Session->setFlash(__('Order status set to Canceled'), 'admin/success', array());
                        return $this->redirect($redirect);
                    }
                } else {
                    if(isset($this->request->data['order_id'])) {
                        $response['action'] = 'error';
                        $response['message'] = __('Order status could not be set to Canceled. Please try again.');
                        echo json_encode($response);
                        exit;
                    } else {
                        return $this->redirect($redirect);
                    }
                }
            } else {
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['msg'] = __('Can\'t return issue quantities to stock.');
                } else {
                    return $this->redirect($redirect);
                }
            }
        }
        $this->set(compact('order', 'is_allow', 'is_completed'));
    }

    public function restore($id = null) {
        $this->Order->id = $id;
        $this->request->data('Order.status_id',4);
        
        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id),
            'contain' => array('OrdersLine' => array('fields' => array('OrdersLine.quantity', 'OrdersLine.sentqty', 'OrdersLine.receivedqty'))),
            'fields' => array('Order.id', 'Order.user_id', 'Order.status_id', 'Order.ordertype_id', 'Order.external_orderid')
        ));

        $redirect = [];
        if($order['Order']['ordertype_id'] == 1) {
            $redirect['controller'] = 'salesorders';
        } else {
            $redirect['controller'] = 'replorders';
        }
        if($this->request->query('f') == 'd') {
            $redirect['action'] = 'details';
            $redirect[] = $id;
        } else {
            $redirect['action'] = 'index';
        }

        // 
        $is_completed = true;
        if($order['Order']['ordertype_id'] != 1 || $order['Order']['status_id'] != 50) {
            $is_completed = false;
        }

        $type = $this->types[$order['Order']['ordertype_id']];

        $is_allow = true;
        if($order['Order']['user_id'] != $this->Auth->user('id')) { // Try to delete not own order
            $products = $this->Access->getProducts($type, 'w', $order['Order']['user_id']);
            $order_lines = $this->Order->OrdersLine->find('all', array(
                'order' => array('OrdersLine.line_number' => 'asc'),
                'conditions' => array('OrdersLine.order_id' => $id, 'OrdersLine.product_id' => array_keys($products)),
                'callbacks' => false
            ));
            if(count($order_lines) != count($order['OrdersLine'])) {
                $is_allow = false;
            }
        }

        //if ($this->request->is(array('post', 'put'))) {
            if(!$is_allow) {
                $this->Session->setFlash(__('You can\'t restore order number %s. It have products for which you have no access.',$id), 'admin/danger');
                return $this->redirect($redirect);
            }

            if(!$is_completed) {
                // you can cancel only Complete Sales Orders
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('Error: you can restore only Canceled Sales Orders.');
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('Error: you can restore only Canceled Sales Orders.',$id), 'admin/danger');
                    return redirect($redirect);
                }
            }

            if ($this->Order->save($this->request->data)) {
                $this->EventRegister->addEvent(2,4,$this->Auth->user('id'),$this->Order->id);
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'success';
                    $response['message'] = __('Order status set to Completed');
                    echo json_encode($response);
                    exit;
                } else {
                    $this->Session->setFlash(__('Order status set to Completed'), 'admin/success', array());
                    return $this->redirect($redirect);
                }
            } else {
                if(isset($this->request->data['order_id'])) {
                    $response['action'] = 'error';
                    $response['message'] = __('Order status could not be set to Completed. Please try again.');
                    echo json_encode($response);
                    exit;
                } else {
                    return $this->redirect($redirect);
                }
            }
        //}
        //$this->set(compact('order', 'is_allow', 'is_completed'));
    }

    public function view_pdf($id = null) {
        $this->Order->id = $id;
        if (!$this->Order->exists()) {
            throw new NotFoundException(__('Invalid order'));
        }
        // increase memory limit in PHP
        ini_set('memory_limit', '512M');
        $this->set('order', $this->Order->read(null, $id));
    }


    public function getordertotals($order = null,$orderlines = null, $ordercosts = null ) {
        $ordertotals['linestotal'] = 0;
        $ordertotals['shipping'] = floatval($order['Order']['shipping_costs']);
        foreach ($orderlines as $orderline) {
            if($orderline['OrdersLine']['type'] == 1 || $orderline['OrdersLine']['type'] == 2) {
                $ordertotals['linestotal'] += $orderline['OrdersLine']['total_line'];
            }
        }

        $ordertotals['grand'] = round(($ordertotals['linestotal'] + $ordertotals['shipping']), 2);
        $new_total = $ordertotals['linestotal'];
        if($ordercosts === null) {
            $this->loadModel('OrdersCosts');
            $ordercosts = $this->OrdersCosts->find('all',[ 'conditions' => array('OrdersCosts.order_id' => $order['Order']['id']), 'contain' => false ]);
        }
        foreach ($ordercosts as $costs) {
            $sum = $costs['OrdersCosts']['amount'];
            if($costs['OrdersCosts']['uom'] == 'percentage') {
                $sum = $ordertotals['linestotal'] * $costs['OrdersCosts']['amount'] / 100;
            }
            if($costs['OrdersCosts']['type'] == 'discount') {
                $new_total = $new_total - $sum;
            } else {
                $new_total = $new_total + $sum;
            }
        }

        $ordertotals['grand_new'] = round(($new_total + $ordertotals['shipping']), 2);
        #pr($ordertotals);
        #exit;
        return $ordertotals;
    }

    public function newordertotals($order = null,$orderlines = null, $ordercosts = null) {
        $ordertotals['linestotal'] = 0;
        $ordertotals['shipping'] = $order['Order']['shipping_costs'];
        foreach ($orderlines as $orderline):
            if($orderline['type'] == 1 || $orderline['type'] == 2)
                $ordertotals['linestotal'] += $orderline['total_line'];

        endforeach;
        $ordertotals['grand'] = $ordertotals['linestotal'] + $ordertotals['shipping'];

        $new_total = $ordertotals['linestotal'];
        if($ordercosts === null) {
            $this->loadModel('OrdersCosts');
            $ordercosts = $this->OrdersCosts->find('all',[ 'conditions' => array('OrdersCosts.order_id' => $order['Order']['id']), 'contain' => false ]);
        }
        foreach ($ordercosts as $costs) {
            $sum = $costs['OrdersCosts']['amount'];
            if($costs['OrdersCosts']['uom'] == 'percentage') {
                $sum = $ordertotals['linestotal'] * $costs['OrdersCosts']['amount'] / 100;
            }
            if($costs['OrdersCosts']['type'] == 'discount') {
                $new_total = $new_total - $sum;
            } else {
                $new_total = $new_total + $sum;
            }
        }
        $ordertotals['grand_new'] = $new_total + $ordertotals['shipping'];

        return $ordertotals;
    }

    public function getproductatf($product_id = null, $warehouse_id = null)
    {
        $this->loadModel('Inventory');
        $this->loadModel('OrdersLine');
        $productatf['totalrequired'] = 0;
        $totalstock = $this->Inventory->find('first',array('fields' => array('Inventory.quantity'),'conditions' => array('Inventory.user_id' => $this->Auth->user('id'),'Inventory.product_id' => $product_id,'Inventory.warehouse_id' => $warehouse_id)));
        $this->OrdersLine->contain();
        $openorderlines = $this->OrdersLine->find('all',array('fields' => array('OrdersLine.quantity'),'conditions' => array('OrdersLine.user_id' => $this->Auth->user('id'),'OrdersLine.product_id' => $product_id,'OrdersLine.warehouse_id' => $warehouse_id,'OrdersLine.status_id' => 1)));
        foreach ($openorderlines as $orderline) {
            $productatf['totalrequired'] += $orderline['OrdersLine']['quantity'];
        }
        
        if(isset($totalstock['Inventory']['quantity'])) {
            $productatf['available'] = $totalstock['Inventory']['quantity'];
        }

        return $productatf;
    }

    /*public function fix($errorsonly = null) {

        $this->layout = 'mtrd';
        $this->loadModel('Inventory');

        $ordertoreview = array();
        $inventory = array();
        $inventoryset = array();
        $metaphones = array();
        $hassamecustomer = array();
        $orders = $this->Order->find('all',array('conditions' => array('Order.user_id' => $this->Auth->user('id'),'Order.ordertype_id' => 1,'Order.status_id' => 14)));
        if(sizeof($orders) == 0)
        {
            $this->Session->setFlash(__('There are no orders to review.'), 'admin/warning', array());
        }
        foreach($orders as $order):
            $line = array();

                $line['HasPack'] = false;
                $line['HasNoProduct'] = false;
                $line['ATP'] = true;
                $line['HasConsolidateCandidate'] = true;
                $line['OrderId'] = $order['Order']['id'];
                $line['ExtOrderId'] = $order['Order']['external_orderid'];
                $line['Created'] = $order['Order']['created'];
                $line['CustomerName'] = $order['Order']['ship_to_customerid'];
                $line['Street'] = $order['Order']['ship_to_street'];
                $line['City'] = $order['Order']['ship_to_city'];
                $line['Zip'] = $order['Order']['ship_to_zip'];
                $line['StateCode'] = $order['Order']['state_id'];
                $line['StateName'] = $order['State']['name'];
                $line['CountryCode'] = $order['Order']['country_id'];
                $line['CountryName'] = $order['Country']['name'];

                // we try to find duplicate orders or several different orders from the same customer.
                // we base our comparison on name and address
                $customerdata = $order['Order']['ship_to_customerid'].$order['Order']['ship_to_street'].$order['Order']['ship_to_city'].$order['Order']['ship_to_zip'].$order['Country']['name'];

                //We use metaphone to identify similar sounding names and addresses
                $metaphonestr = metaphone($customerdata);

                // to get even fuzzier, we use levinshtein's string comparison algo
                foreach ($metaphones as $key => $value) {
                    $diff = levenshtein($metaphonestr,$value);
                    // Should it be 3? time will tell, this will need a fine-tune
                    if($diff < 3)
                        $line['ConsolidateCandidate'] = $key;
                }
                $metaphones[$order['Order']['id']] = $metaphonestr;


                    foreach($order['OrdersLine'] as $orderline):

                        //Get remaining inventory for product
                        if(empty($inventory[$orderline['product_id']]) && empty($inventoryset[$orderline['product_id']]))
                        {

                        //  echo "in initial";
                            $totalstock = $this->Inventory->find('first',array('fields' => array('Inventory.quantity'),'conditions' => array('Inventory.user_id' => $this->Auth->user('id'),'Inventory.product_id' => $orderline['product_id'],'Inventory.warehouse_id' => $this->Session->read('default_warehouse'))));
                            if(isset($totalstock['Inventory']))
                            {
                            $inventory[$orderline['product_id']] = $totalstock['Inventory']['quantity'] - $orderline['quantity'];
                            } else {
                                $inventory[$orderline['product_id']] = 0;
                            }
                            $inventoryset[$orderline['product_id']] = 1;

                        }
                        else {
                            $inventory[$orderline['product_id']] = $inventory[$orderline['product_id']] - $orderline['quantity'];
                        }
                        if($orderline['type'] == 7)
                        {
                }
                        if($inventory[$orderline['product_id']] < 0 )
                        {
                            $line['ATP'] = false;
                      
                        }

                        if($orderline['type'] == 7)
                                $line['HasPack'] = true;
                        if($orderline['product_id'] == 0)
                                $line['HasNoProduct'] = true;

                    endforeach;
                array_push($ordertoreview,$line);
        endforeach;

        $this->set(compact('ordertoreview'));

    }*/

    public function exportcsv($type = null) {
        set_time_limit(0);
        $exportlines = array();
        $orders = $this->Order->find('all',array(
            'conditions' => array(
                'Order.user_id' => $this->Auth->user('id'),
                'Order.ordertype_id' => $type,
            ),
            'contain' => array(
                'OrdersLine' => ['line_number', 'product_id', 'sku', 'quantity', 'sentqty', 'damagedqty', 'receivedqty', 'unit_price', 'total_line'],
                'OrdersLine.OrderSchedule' => ['delivery_date'],
                'Schannel',
                'State',
                'Country',
                'Address',
                'Address.State' => ['code', 'name'],
                'Address.Country' => ['code', 'name'],
                'Supplier'
            ),
            'fields' => array(
                'Order.id',
                'Order.external_orderid',
                'Order.ship_to_customerid',
                'Order.ship_to_street',
                'Order.ship_to_city',
                'Order.ship_to_zip',
                'Order.requested_delivery_date',
                'Order.shipping_costs',
                'Order.created',
                'Schannel.name',
                'Address.id',
                'Address.street',
                'Address.city',
                'Address.zip',
                'Address.state_id',
                'Address.stateprovince',
                'State.code',
                'State.name',
                'Country.code',
                'Country.name',
                'Supplier.name'
            )
        ));

        $_serialize = 'exportlines';

        if($type == 1) {
            foreach($orders as $order) {
                foreach($order['OrdersLine'] as $orderline) {
                    $line = array();
                    array_push($line,$order['Order']['id']);
                    array_push($line,$order['Order']['external_orderid']);
                    array_push($line,$order['Order']['id']);
                    array_push($line,$orderline['line_number']);
                    array_push($line,$order['Schannel']['name']);
                    array_push($line,$order['Order']['ship_to_customerid']);

                    if($order['Address']['id']) {
                        array_push($line,$order['Address']['street']);
                        array_push($line,$order['Address']['city']);
                        array_push($line,$order['Address']['zip']);
                        if($order['Address']['state_id']) {
                            array_push($line,$order['Address']['State']['code']);
                            array_push($line,$order['Address']['State']['name']);
                        } else {
                            array_push($line,$order['Address']['stateprovince']);
                            array_push($line,'');
                        }

                        if($order['Address']['Country']) {
                            array_push($line,$order['Address']['Country']['code']);
                            array_push($line,$order['Address']['Country']['name']);
                        } else {
                            array_push($line,$order['Country']['code']);
                            array_push($line,$order['Country']['name']);
                        }
                    } else {
                        
                        array_push($line,$order['Order']['ship_to_street']);
                        array_push($line,$order['Order']['ship_to_city']);
                        array_push($line,$order['Order']['ship_to_zip']);
                        if($order['State']['code'] != 'XZ')
                            {
                            array_push($line,$order['State']['code']);
                            array_push($line,$order['State']['name']);
                        } else {
                            array_push($line,'');
                            array_push($line,'');
                        }
                        array_push($line,$order['Country']['code']);
                        array_push($line,$order['Country']['name']);
                    }

                    array_push($line,$order['Order']['requested_delivery_date']);
                    array_push($line,$order['Order']['shipping_costs']);
                    $dateonly = date_format(date_create($order['Order']['created']), 'Y-m-d');
                    array_push($line,$dateonly);
                    array_push($line,$orderline['product_id']);
                    array_push($line,$orderline['sku']);
                    array_push($line,$orderline['quantity']);
                    array_push($line,$orderline['sentqty']);
                    array_push($line,$orderline['unit_price']);
                    array_push($line,$orderline['total_line']);

                    array_push($exportlines,$line);
                }
            }
            $_header = array('Id', 'ReferenceOrder', 'ReferenceOrder 2', 'LineNumber', 'SalesChannel', 'Name', 'Street', 'City','Zip','StateCode','StateName','CountryCode','CountryName','RequestedDate','ShippingCosts','Created','Productid','SKU','OrderedQuantity','ShippedQuantity','UnitPrice','LineTotal');
            //$_extract = array('Order.id', 'Order.supplier_id', 'Order.external_orderid','Order.requested_delivery_date','OrdersLine[0].product_id');
        }

        if($type == 2) {
            foreach($orders as $order) {
                foreach($order['OrdersLine'] as $orderline) {
                    $line = array();
                    array_push($line,$order['Order']['id']);
                    array_push($line,$order['Order']['external_orderid']);
                    array_push($line,$orderline['line_number']);
                    array_push($line,$order['Supplier']['name']);

                    array_push($line,$order['Order']['requested_delivery_date']);
                    array_push($line,$order['Order']['shipping_costs']);
                    $dateonly = date_format(date_create($order['Order']['created']), 'Y-m-d');
                    array_push($line,$dateonly);
                    array_push($line,$orderline['product_id']);
                    array_push($line,$orderline['sku']);
                    array_push($line,$orderline['quantity']);
                    array_push($line,$orderline['receivedqty']);
                    array_push($line,$orderline['damagedqty']);
                    array_push($line,$orderline['unit_price']);
                    array_push($line,$orderline['total_line']);
                    if(isset($orderline['OrderSchedule']['delivery_date'])) {
                        array_push($line,$orderline['OrderSchedule']['delivery_date']);
                    } else {
                        array_push($line, '');
                    }
                    array_push($exportlines,$line);
                }
            }
            $_header = array('Id', 'ReferenceOrder','LineNumber', 'Supplier', 'RequestedDate','ShippingCosts','Created','Productid','SKU','OrderedQuantity','ReceivedQuantity','DamagedQuantity','UnitPrice','total_line', 'DeliveryDate');
        }

        $file_name = "Delivrd_".date('Y-m-d-His')."_orders.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('exportlines', '_serialize', '_header'));
    }

    public function returnjsonerror($text = null,$filename,$filesize) {
        $filese = array( 'files' => array(array(
                    "name" => $filename,
                    "size" => $filesize,
                    "error" => $text

                )));
                header('Content-Type: application/json');
                echo json_encode($filese,JSON_PRETTY_PRINT);
                exit();
    }

    public function purchaseorderform($id = null, $index = null)
    {
        $this->layout = 'mtrds';
        $title_for_layout = (($index == 1) ? (!empty($this->Session->read('sales_title')) ? ucwords($this->Session->read('sales_title')) : 'Sales Order') : 'Replenishment Order');
        $this->Order->recursive = 1;
        $title = ($index == 1) ? 'Packing slip for order #': 'Purchase Order Number';

        $order = $this->Order->find('first', array('conditions' => array('Order.id' => $id), 'contain' => array(
            'User' => array('fields' => array('id','logo','logo_url','company')),
            'Address',
            'Address.State',
            'Address.Country',
            'Supplier' => array('fields' => array('name','email')),
            'OrdersLine',
            'Schannel'=> array('fields' => array('name')),
            'OrdersLine.Product' => array('fields' => array('name','sku','description')))));
        $this->set('order', $order);

        $ordertotals = $this->newordertotals($order, $order['OrdersLine']);
        $this->set(compact('ordertotals','index','title', 'title_for_layout'));

    }

    public function packinglist($id = null)
    {
        $this->layout = 'mtrd';
        $this->Order->recursive = 3;
        $order = $this->Order->findById($id);

        $this->set('order', $order);

        $this->loadModel('Currency');

        $schannels = $this->Order->Schannel->find('list',array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));
        $this->set(compact('schannels'));
        $states = $this->Order->State->find('list');
        $this->set(compact('states'));
        $countries = $this->Order->Country->find('list');
        $this->set(compact('countries'));
    }

    public function genorderdata()
    {
        $orders = $this->Order->find('all',array('conditions' => array('Order.user_id' => $this->Auth->user('id')), 'contain' => array('Ordertype', 'State', 'Source', 'Country', 'Supplysource', 'Supplier', 'Schannel', 'Status', 'Address', 'OrdersLine', 'OrdersLine.Product' => array('fields' => array('value'))), 'order' => array('Order.modified' => 'desc')));
        $_serialize = 'orders';
        $_header = array('0','Name','Street' ,'City','PostalCode','StateProvince' ,'Country','PhoneNumber','Email','RefOrder','RequestedDate' ,'SalesChannel','ShippingCosts if known','LineNumber' ,'SKU','Quantity','Value');
        $_extract = array('Order.id','Order.ship_to_customerid', 'Order.ship_to_street', 'Order.ship_to_city', 'Order.ship_to_zip', 'Order.ship_to_stateprovince', 'Country.name','Address.phone','Order.email','Order.external_orderid', 'Order.requested_delivery_date', 'Schannel.name', 'Order.shipping_costs', 'OrdersLine.line_number', 'OrdersLine.sku', 'OrdersLine.quantity', 'OrdersLine.Product.value');

        $file_name = "Delivrd_".date('Y-m-d-His')."_orders.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('orders', '_serialize', '_header', '_extract'));
    }

    public function downloadsamplefile() {

        $filename = $target_path = WWW_ROOT."sampledata/Delivrd_sample_orders.csv"; // of course find the exact filename....
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for certain browsers
        header('Content-Type: application/csv');

        header('Content-Disposition: attachment; filename="'. basename($filename) . '";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filename));

        readfile($filename);
        exit;
    }

    public function checkorderexist($id) {
        $this->Order->contain();
            $hasorder = $this->Order->findById($id);
        if (!$hasorder) {
            $this->Session->setFlash(__('Order does not exist.'), 'admin/danger', array());
            return $this->redirect(array('action' => 'index',2));
        }

    }

    public function createproduct($orderdata = null)
    {
        $this->Product->create();
        $this->request->data('Product.user_id',$this->Auth->user('id'));
        $this->request->data('Product.status_id',1);
        $this->request->data('Product.deleted',0);
        $this->request->data('Product.name',substr($orderdata['Order']['itemtitle'],0,30));
        $this->request->data('Product.description',$orderdata['Order']['itemtitle']);
        $this->request->data('Product.group_id',34);
        $this->request->data('Product.value',substr($orderdata['OrdersLine']['unit_price'],1));
        $this->request->data('Product.sku',$orderdata['OrdersLine']['sku']);
        $this->request->data('Product.imageurl',"http://realfevr.pt/assets/image-not-found-4a963b95bf081c3ea02923dceaeb3f8085e1a654fc54840aac61a57a60903fef.png");
        if ($this->Product->save($this->request->data)) {
            $this->EventRegister->addEvent(1,1,$this->Auth->user('id'),$this->Product->id);
            $this->createinventoryrecord($this->Product->id);
            return $this->Product->id;
        } else {
            return 1;
        }
    }


    public function createinventoryrecord($pid = null)
    {

        $this->loadModel('Inventory');
            $this->Inventory->create();
            $this->Inventory->set('user_id',$this->Auth->user('id'));
            $this->Inventory->set('dcop_user_id',$this->Auth->user('id'));
            $this->Inventory->set('product_id', $pid);
            $this->Inventory->set('quantity', 0);
            $this->Inventory->set('warehouse_id', $this->Session->read('default_warehouse'));
            if ($this->Inventory->save($this->request->data)) {
                return 0;
            } else {
                        $this->Session->setFlash(__('The product inventory record could not be saved. Please, try again.'), 'admin/danger', array());
            }

    }

    public function updateshipmentprocess($id) {
        $this->loadModel('Shipment');
        $shipmentdata = $this->Shipment->find('first', array('conditions' => array('Shipment.order_id' => $id)));
        if(!empty($shipmentdata)) {
            if($shipmentdata['Shipment']['status_id'] != 6) {
                $this->Shipment->id = $shipmentdata['Shipment']['id'];
                $this->request->data('Shipment.status_id',6);
                $this->EventRegister->addEvent(4,16,$this->Auth->user('id'),$this->Shipment->id);
                if ($this->Shipment->save($this->request->data)) {
                    $this->Session->setFlash(__('Shipment status could not be updated'),'default',array('class'=>'alert alert-danger'));
                }
            }
        }
    }


    public function importmagento() {
        // add this if you need to diplay the errors if any
        error_reporting(E_ALL); ini_set('display_errors', 1);

        $base_url="http://myshoes.fastcomet.host/Magentos/";
        //API user
        $api_user="apiuser";
        //API key
        $api_key="81eRvINu9r";


        $api_url=$base_url.'index.php/api/soap/?wsdl';
        $client = new SoapClient($api_url);
        $session = $client->login($api_user, $api_key);
        $result = $client->call($session, 'order.list');
        $j=0;


        foreach($result as $key => $value)
        {
          $result1 = $client->call($session, 'order.info', $result[$key]['increment_id']);
          $arr[$j]['Order']['external_orderid'] = $result[$key]['increment_id'];
          $arr[$j]['Order']['schannel_id']= 'Magento';
          $arr[$j]['Order']['shipping_costs']= number_format((float)$result[$key]['base_shipping_amount'], 2, '.', '');
          $arr[$j]['Order']['requested_delivery_date'] ='';
          $arr[$j]['Order']['currency'] =$result[$key]['order_currency_code'];
          $arr[$j]['Order']['remarks'] =$result[$key]['customer_note'];
          $arr[$j]['Order']['createdinsource'] =$result[$key]['created_at'];
          $arr[$j]['Order']['modifiedinsource'] =$result[$key]['updated_at'];
          $arr[$j]['Order']['ship_to_customerid'] = $result[$key]['customer_firstname']." ".$result[$key]['customer_lastname'];
          $arr[$j]['Order']['ship_to_city'] =$result[$key]['customer_email'];
          $arr[$j]['Order']['ship_to_street'] =$result1['shipping_address']['street'];
          $arr[$j]['Order']['ship_to_city'] =$result1['shipping_address']['city'];
          $arr[$j]['Order']['ship_to_zip'] =$result1['shipping_address']['postcode'];
          $arr[$j]['Order']['ship_to_stateprovince'] = '';
          $arr[$j]['Order']['state_id'] = '';
          $arr[$j]['Order']['country_id'] =$result1['shipping_address']['country_id'];

          $arr[$j]['OrdersLine']=[];
          $adr=$result1['items'];
          $i=0;
            foreach( $adr as $keys => $values){
               $sk =$adr[$keys]['sku'];
               $qo=$adr[$keys]['qty_ordered'];
               $up=$adr[$keys]['price'];
                   $arr[$j]['OrdersLine'][$i]['line_number'] = ($i+1) * 10;
               $arr[$j]['OrdersLine'][$i]['sku'] =$sk;
               $arr[$j]['OrdersLine'][$i]['quantity'] = number_format((float)$qo, 2, '.', '');
               $arr[$j]['OrdersLine'][$i]['unit_price'] = number_format((float)$up, 2, '.', '');

              $i++;
            }
          $j++;
        }
        $this->importcsv(null,$arr);
        return $this->redirect(array('action' => 'index',1));

    }

    /**
     * Import orders from Shopify
     *
     *
     */
    public function importshopify($id) {
        $this->autoRender = false;
        set_time_limit(0);

        $response = $this->Shopfy->importShopifyOrders($id);

        if ($this->request->is('ajax')) {
            $this->set('_serialize', 'response');
            $json = json_encode($response);
            $this->response->body($json);
        } else {
            $this->Session->setFlash(__('Total found: '. $response['total_orders']), 'default', array('class' => 'alert alert-success'));
            return $this->redirect(array('controller'=>'integrations', 'action' => 'index'));
        }
    }

    /**
     * Import orders from WooCommerce
     *
     *
     */
    public function importwoo($id) {
        $this->autoRender = false;
        set_time_limit(0);

        $response = $this->WooCommerce->importWooOrders($id);
        if ($this->request->is('ajax')) {
            $this->set('_serialize', 'response');
            $json = json_encode($response);
            $this->response->body($json);
        } else {
            $this->Session->setFlash(__('Total found: '. $response['total_orders']), 'default', array('class' => 'alert alert-success'));
            return $this->redirect(array('controller'=>'integrations', 'action' => 'index'));
        }
    }


    /**
     * Generate Amazon Report
     *
     *
     */
    public function generateAmazonReport() {
        $this->autoRender = false;
        set_time_limit(0);

        $response = $this->Amazon->generateOrdersReport();
        if($response['status'] == 'success') {
            $response = $this->Amazon->getReportList('_GET_FLAT_FILE_ORDERS_DATA_'); //_GET_ORDERS_DATA_
        }

        if ($this->request->is('ajax')) {
            $this->set('_serialize', 'response');
            $json = json_encode($response);
            $this->response->body($json);
        } else {
            //$this->Session->setFlash(__('Total found: '. $response['found'] .'. Product(s) added: '. $response['added'] .'. Product(s) updated: '. $response['updated']), 'default', array('class' => 'alert alert-success'));
            //return $this->redirect(array('controller' => 'orders', 'action' => 'importwoo'));
            return $response;
        }
    }

    /**
     * Import Amazon Orders
     *
     *
     */
    public function importAmazon() {
        $this->autoRender = false;
        set_time_limit(0);

        //$response = $this->Amazon->importProducts();
        //$response = $this->Amazon->getReportList();
        $response = $this->Amazon->getReport('8374613794017578');
        exit;
        if ($this->request->is('ajax')) {
            $this->set('_serialize', 'response');
            $json = json_encode($response);
            $this->response->body($json);
        } else {
            $this->Session->setFlash(__('Total found: '. $response['found'] .'. Product(s) added: '. $response['added'] .'. Product(s) updated: '. $response['updated']), 'default', array('class' => 'alert alert-success'));
            return $this->redirect(array('controller' => 'orders', 'action' => 'importwoo'));
        }
    }


    /*public function importecom() {
        $this->layout = 'mtrd';
    }*/

    public function importmagento2()
    {
        $this->loadModel('Integration');
        $mgintegration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Magento','Integration.user_id' => $this->Auth->user('id'))));
        $musername     = $mgintegration['Integration']['username'];
        $mpassword     = $mgintegration['Integration']['password'];
        $request_url   = $mgintegration['Integration']['url'];
        $token         = $musername = $mgintegration['Integration']['username'];
        $token_access  = $token;
        if($token == null)
        {
            $this->Session->setFlash(__('Could not connect to Magento.Please check your settings.'), 'admin/danger', array());
            return $this->redirect(array('controller' => 'products', 'action' => 'index'));
        }

        $headers = array("Authorization: Bearer ".$token_access);

        $requestUrl=$request_url.'index.php/rest/V1/orders/?searchCriteria=';

        $ch = curl_init();
        $ch = curl_init($requestUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultjs = curl_exec($ch);
        $result   =  json_decode($resultjs,true);

        if(!isset($result['items']))
        {
            $this->Session->setFlash(__('Could not connect to Magento.Please check your settings.'), 'admin/danger', array());
            return $this->redirect(array('controller' => 'products', 'action' => 'index'));
        }

        $j=0;
        foreach($result['items'] as $key => $value)
        {
            $arr[$j]['Order']['external_orderid']        = $value['increment_id'];
            $arr[$j]['Order']['schannel_id']             = 'Magento';
            $arr[$j]['Order']['shipping_costs']          = number_format((float)$value['base_shipping_amount'], 2, '.', '');
            $arr[$j]['Order']['requested_delivery_date'] ='';
            $arr[$j]['Order']['currency']                = $value['order_currency_code'];
            $arr[$j]['Order']['remarks']                 = '';
            $arr[$j]['Order']['createdinsource']         = $value['created_at'];
            $arr[$j]['Order']['modifiedinsource']        = $value['updated_at'];
            $arr[$j]['Order']['ship_to_customerid']      = $value['customer_firstname']." ".$value['customer_lastname'];
            $arr[$j]['Order']['ship_to_city']            = $value['billing_address']['city'];
            $arr[$j]['Order']['ship_to_street']          = $value['billing_address']['street'][0];
            $arr[$j]['Order']['ship_to_city']            = $value['billing_address']['city'];
            $arr[$j]['Order']['ship_to_zip']             = $value['billing_address']['postcode'];
            $arr[$j]['Order']['ship_to_stateprovince']   = $value['billing_address']['region'];;
            $arr[$j]['Order']['state_id']                = '';
            $arr[$j]['Order']['country_id']              = $value['billing_address']['country_id'];

            $arr[$j]['OrdersLine'] = [];
            $adr = $value['items'];
            $i = 0;
            foreach( $adr as $keys => $values){
                $sk = $adr[$keys]['sku'];
                $qo = $adr[$keys]['qty_ordered'];
                $up = $adr[$keys]['price'];
                $arr[$j]['OrdersLine'][$i]['line_number'] = ($i+1) * 10;
                $arr[$j]['OrdersLine'][$i]['sku']         = $sk;
                $arr[$j]['OrdersLine'][$i]['quantity']    = number_format((float)$qo, 2, '.', '');
                $arr[$j]['OrdersLine'][$i]['unit_price']  = number_format((float)$up, 2, '.', '');
                $i++;
            }
            $j++;
        }
        $this->importcsv(null,$arr);
        return $this->redirect(array('action' => 'index',1));
    }

    public function showimporterror($errstr = null, $file = null)
    {
        if(isset($file)) fclose($file);
        $gotoimport = '<a href="/orders/uploadcsv" class="btn blue-hoki fileinput-button"><i class="fa fa-cloud-upload"></i> Go to upload page</a>';
        $this->Session->setFlash(__('Orders could not be created. %s', $errstr), 'admin/danger', array());
        return $this->redirect(array('action' => 'index',1));
    }

    public function products($id, $so = false) {
        
        $order = $this->Order->find('first', array(
            'fields' => array('Order.id','Order.ordertype_id', 'Order.user_id', 'Order.dcop_user_id', 'Order.external_orderid', 'User.currency_id'),
            'contain' => array(false),
            'conditions' => array('Order.id' => $id)
        ));

        $this->loadModel('Productsupplier');
        $part_ids = $this->Productsupplier->find('list', [
            'fields' => ['Product.id', 'Product.name'],
            'contain' => ['Product'],
            'conditions' => [
                'Product.user_id' => $order['Order']['user_id'],
                'Productsupplier.part_number like' => '%'. $this->request->query('search') .'%',
            ]
        ]);
        
        $conditions = ['Product.deleted' => 0];
        if($this->request->query('search')) {
            $conditions['OR'] = [
                'Product.name like' => '%'. $this->request->query('search') .'%',
                'Product.sku like' => '%'. $this->request->query('search') .'%',
            ];
            if($part_ids) {
                $conditions['OR']['Product.id IN'] = array_keys($part_ids); 
            }
        }

        $is_own = ($order['Order']['user_id'] == $this->Auth->user('id'));
        if(!$is_own) { // Check access, get products and allowed warehouse
            if($order['Order']['ordertype_id'] == 1) {
                $conditions['Product.status_id'] = 1;
            } else {
                $allowedstatusesrepl = [1,12];
                $conditions['Product.status_id'] = $allowedstatusesrepl;
            }
            $products = $this->Access->getProducts($this->types[$order['Order']['ordertype_id']], 'w', $order['Order']['user_id'], $conditions);
        } else {
            $this->loadModel('OrdersLine');
            if($order['Order']['ordertype_id'] == 1) {
                $conditions['Product.user_id'] = $this->Auth->user('id');
                $conditions['Product.status_id'] = 1;
            } else {
                $allowedstatusesrepl = [1,12];
                $conditions['Product.user_id'] = $this->Auth->user('id');
                $conditions['Product.status_id'] = $allowedstatusesrepl;
            }
            $products = $this->OrdersLine->Product->find('list', array('conditions' => $conditions));
        }

        $results['results'] = [];
        //var_dump($this->Auth->user('zeroquantity'));
        if($this->Auth->user('zeroquantity') || !$so) { //get quantity
            foreach ($products as $key => $value) {
                $result['id'] = $key;
                $result['text'] = utf8_encode($value);
                $results['results'][] = $result;
            }
        } else {
            foreach ($products as $key => $value) {
                $result['id'] = $key;
                $result['quantity'] = $this->OrdersLine->Product->getInvQuantity($key);
                $result['disabled'] = $result['quantity']['disabled'];
                $result['text'] = utf8_encode($value);
                $results['results'][] = $result;
            }
        }
        
            
        
        //header('Content-Type: application/json');
        echo (json_encode($results));
        exit;
    }

    public function checkLineStatuses($id) {
        $this->Order->id = $id;
        if (!$this->Order->exists($id)) {
            throw new NotFoundException(__('Invalid order')); // we need show exception for ajax requests
        }
        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id),
            'contain' => array('OrdersLine' => array('fields' => array('OrdersLine.quantity', 'OrdersLine.sentqty', 'OrdersLine.receivedqty'))),
            'fields' => array('Order.id', 'Order.ordertype_id')
        ));

        if($order['Order']['ordertype_id'] == 1) {
            $key = 'sentqty';
        } else {
            $key = 'receivedqty';
        }
        $is_success = 'ready';
        foreach ($order['OrdersLine'] as $line) {
            if($line[$key] < $line['quantity']) {
                $is_success = 'not_ready';
            }
        }
        
        $response['action'] = 'success';
        $response['status'] = $is_success;

        echo json_encode($response);
        exit;
    }
}
