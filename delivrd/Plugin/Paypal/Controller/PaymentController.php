<?php
App::uses('AppController', 'Controller');
App::uses('PaypalIPN', 'Lib');
/**
 * Bins Controller
 *
 */
class PaymentController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Search.Prg', 'Csv.Csv');
	public $theme = 'Mtro';

    /**
    * Models
    *
    * @var array
    */
    public $uses = array('Subscription', 'User', 'Payment');

	public function beforeFilter() {
       parent::beforeFilter();
       $this->Auth->allow('ipn', 'testipn');
    }

    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function ipn() {
    	$this->layout = false;

        if ($this->request->is(array('post', 'put'))) {
            //Log::info('PAYMENT IPN REQUEST', ['scope' => ['payments']]);
            $paypal = new PaypalIPN();
            if(Configure::read('OperatorName') == 'Delivrd' && $this->request->host() == 'delivrdapp.com') {
                // Real live version
            } else {
                // Sandbox version
                $paypal->useSandbox();
            }
            $memo = json_encode($_POST);

            if($paypal->verifyIPN()) {
                #Log::info('VERIFIED IPN REQUEST', ['scope' => ['payments']]);
                #Log::info($_POST, ['scope' => ['payments']]);
                #Log::info(http_build_query($_POST), ['scope' => ['payments']]);
                //pr('@@@');
                //exit;

                if($_POST['txn_type'] == 'subscr_cancel') {
                    $subscr_id = $_POST['subscr_id'];
                    $subscription = $this->Subscription->find('first', ['conditions' => array('Subscription.ext_id' => $subscr_id)]);

                    $this->Subscription->id = $subscription['Subscription']['id'];
                    $subscription['Subscription']['status'] = 'Canceled';
                    $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');
                    $subscription['Subscription']['memo'] = $memo;
                    $this->Subscription->save($subscription);
                    //Log::info('INFO: TRN Type:'. $_POST['txn_type'], ['scope' => ['payments']]);
                    // We nothing to do, paid status will disabled auto when exp time end.
                } else if($_POST['txn_type'] == 'subscr_payment') { //Receive new subscr payment
                    $subscr_id = $_POST['subscr_id'];
                    $user_id = $_POST['custom'];

                    // Try to find subscription
                    #$subscription = $this->Subscription->find('first', ['conditions' => array('ext_id' => $subscr_id)]);
                    $subscription = $this->Subscription->find('first', ['conditions' => array('Subscription.user_id' => $user_id)]);
                    if($subscription) {
                        $this->Subscription->id = $subscription['Subscription']['id'];

                        $status = false;
                        if($subscription['Subscription']['last_txn_id'] == $_POST['txn_id'] && $subscription['Subscription']['last_txn_id']) {

                            // Payment already added
                            if($_POST['payment_status'] == 'Completed') { // If first time status was pending and now complete
                                $subscription['Subscription']['expiry_date'] = $paypal->plusMonth(date('Y-m-d', strtotime($_POST['payment_date']))); //$paypal->plusMonth(date('Y-m-d'));
                                $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');
                                $subscription['Subscription']['memo'] = $memo;
                                $this->Subscription->save($subscription);
                                $status = true;
                            }
                        } else {
                            // Update last_txn_id
                            $subscription['Subscription']['last_txn_id'] = $_POST['txn_id'];
                            if($_POST['payment_status'] == 'Completed') {
                                $subscription['Subscription']['expiry_date'] = $paypal->plusMonth(date('Y-m-d', strtotime($_POST['payment_date']))); //$paypal->plusMonth(date('Y-m-d'));
                            }
                            $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');
                            $subscription['Subscription']['memo'] = $memo;
                            $this->Subscription->save($subscription);
                            $status = true;
                        }

                        
                        // Update user role if payment success
                        if($status) {
                            $this->User->id = $user_id;
                            $this->User->saveField('role', 'paid');
                        }
                        
                    } else {
                        // We receive notify but have no info about subscription.
                        // Inform admin about this payment
                        // Can be already exists subsribers which sign in before IPN was installed.

                        $subscription['Subscription']['ext_id'] = $subscr_id;
                        $subscription['Subscription']['user_id'] = $_POST['custom'];
                        $subscription['Subscription']['amount'] = $_POST['mc_gross'];
                        $subscription['Subscription']['payer_email'] = $_POST['payer_email'];
                        $subscription['Subscription']['last_txn_id'] = $_POST['txn_id'];
                        $subscription['Subscription']['expiry_date'] = $paypal->plusMonth(date('Y-m-d', strtotime($_POST['payment_date']))); //$paypal->plusMonth(date('Y-m-d'));
                        $subscription['Subscription']['created'] = date('Y-m-d H:i:s');
                        $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');
                        $subscription['Subscription']['memo'] = $memo;
                        if($this->Subscription->save($subscription)) {
                            $this->User->id = $user_id;
                            $this->User->saveField('role', 'paid');
                        }
                    }

                    // Add Payment details
                    if( $payment = $this->Payment->find('first', ['conditions' => array('Payment.transcation_id' => $_POST['txn_id'])]) ) {
                        $this->Payment->id = $payment['Payment']['id'];
                        $payment['Payment']['subscription'] = $subscr_id;
                        $payment['Payment']['user_name'] = $_POST['first_name'] .' '. $_POST['last_name'];
                        $payment['Payment']['payment_status'] = $_POST['payment_status'];
                        $payment['Payment']['amount'] = $_POST['mc_gross'];
                        $payment['Payment']['payment_date'] = $_POST['payment_date'];
                        $this->Payment->save($payment);
                    } else {
                        $payment = [];
                        $payment['Payment']['user_id'] = $_POST['custom'];
                        $payment['Payment']['subscription'] = $subscr_id;
                        $payment['Payment']['transcation_id'] = $_POST['txn_id'];
                        $payment['Payment']['payment_method'] = 1;
                        $payment['Payment']['payment_status'] = $_POST['payment_status'];
                        $payment['Payment']['amount'] = $_POST['mc_gross'];
                        $payment['Payment']['payment_date'] = $_POST['payment_date'];
                        $payment['Payment']['recurring'] = 'Month';
                        $payment['Payment']['user_name'] = $_POST['first_name'] .' '. $_POST['last_name'];
                        $payment['Payment']['address'] = '';
                        $payment['Payment']['city'] = '';
                        $payment['Payment']['state'] = '';
                        $payment['Payment']['zip'] = '';
                        $payment['Payment']['country'] = $_POST['residence_country'];
                        $this->Payment->save($payment);
                    }


                } else if($_POST['txn_type'] == 'subscr_signup') { //new signup
                    $subscr_id = $_POST['subscr_id'];
                    $user_id = $_POST['custom'];
                    // Try to find subscription
                    //$subscription = $this->Subscription->find('first', ['conditions' => array('ext_id' => $subscr_id)]);
                    $subscription = $this->Subscription->find('first', ['conditions' => array('Subscription.user_id' => $user_id)]);
                    if($subscription) {
                        // Nothing to do
                        
                        $this->Subscription->id = $subscription['Subscription']['id'];
                        $subscription['Subscription']['ext_id'] = $subscr_id;
                        $subscription['Subscription']['amount'] = $_POST['mc_amount3'];
                        $subscription['Subscription']['payer_email'] = $_POST['payer_email'];
                        $subscription['Subscription']['expiry_date'] = $paypal->plusMonth(date('Y-m-d'));;
                        $subscription['Subscription']['memo'] = $memo;
                        $subscription['Subscription']['user_id'] = $_POST['custom'];
                        $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');
                        $this->Subscription->save($subscription);

                    } else {
                        $subscription['Subscription']['ext_id'] = $subscr_id;
                        $subscription['Subscription']['user_id'] = $_POST['custom'];
                        $subscription['Subscription']['amount'] = $_POST['mc_amount3'];
                        $subscription['Subscription']['payer_email'] = $_POST['payer_email'];
                        $subscription['Subscription']['expiry_date'] = $paypal->plusMonth(date('Y-m-d')); // We not receive payment, just new subscription
                        $subscription['Subscription']['created'] = date('Y-m-d H:i:s');
                        $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');
                        $subscription['Subscription']['memo'] = $memo;
                        $this->Subscription->save($subscription);
                    }

                } else {
                    //subscr_eot, subscr_failed, subscr_modify
                    #Log::info('INFO: TRN Type:'. $_POST['txn_type'], ['scope' => ['payments']]);
                }
            } else {
                #Log::info('DANGER: Request not verified', ['scope' => ['payments']]);
                #Log::info($_POST, ['scope' => ['payments']]);
            }
            #Log::info('==================== END IPN REQUEST ====================', ['scope' => ['payments']]);
        } else {
            #Log::info('==================== ALERT GET!!! NEW IPN REQUEST ERROR!!! ====================', ['scope' => ['payments']]);
            #Log::info($_GET, ['scope' => ['payments']]);
        }
        header("HTTP/1.1 200 OK");
        exit;
    }

    public function testipn() {
        /*$query_str = 'mc_gross=49.99&protection_eligibility=Eligible&address_status=confirmed&payer_id=7DXWNGDETLW3W&address_street=1 Main St&payment_date=07:36:56 Aug 13, 2018 PDT&payment_status=Completed&charset=windows-1252&address_zip=95131&first_name=Den&mc_fee=1.75&address_country_code=US&address_name=Den Chernov&notify_version=3.9&subscr_id=I-KXE2S322BR57&custom=0&payer_status=unverified&business=d_sale_1359066991_biz@hotmail.com&address_country=United States&address_city=San Jose&verify_sign=A.XwXCxFb.uToqGcWgw-KgKnUDpkA0HqRnfe6jNtgs7IB8E1sh7zZDrn&payer_email=bayer_1262801479_per@hotmail.com&txn_id=853896764V8809745&payment_type=instant&btn_id=3883600&last_name=Chernov&address_state=CA&receiver_email=d_sale_1359066991_biz@hotmail.com&payment_fee=1.75&receiver_id=7XZMYXZ6PMMPJ&txn_type=subscr_payment&item_name=DelivRD&mc_currency=USD&item_number=0001&residence_country=US&test_ipn=1&transaction_subject=DelivRD&payment_gross=49.99&ipn_track_id=7bde0eb05113f';
        $query_str = 'amount3=49.99&address_status=confirmed&subscr_date=07:34:24 Aug 13, 2018 PDT&payer_id=7DXWNGDETLW3W&address_street=1 Main St&mc_amount3=49.99&charset=windows-1252&address_zip=95131&first_name=Den&reattempt=1&address_country_code=US&address_name=Den Chernov&notify_version=3.9&subscr_id=I-UWPNNXUP4HP0&custom=0&payer_status=unverified&business=d_sale_1359066991_biz@hotmail.com&address_country=United States&address_city=San Jose&verify_sign=Avil5zNgI4a7bYWDidXv4.tc0t6NAlEP6jeCapodayrzOwt-8rBAE0Zv&payer_email=bayer_1262801479_per@hotmail.com&last_name=Chernov&address_state=CA&receiver_email=d_sale_1359066991_biz@hotmail.com&recurring=1&txn_type=subscr_cancel&item_name=DelivRD&mc_currency=USD&item_number=0001&residence_country=US&test_ipn=1&period3=1 D&ipn_track_id=f3ea75425ae31';
        $query_str = 'amount3=49.99&address_status=confirmed&subscr_date=07:36:52 Aug 13, 2018 PDT&payer_id=7DXWNGDETLW3W&address_street=1 Main St&mc_amount3=49.99&charset=windows-1252&address_zip=95131&first_name=Den&reattempt=1&address_country_code=US&address_name=Den Chernov&notify_version=3.9&subscr_id=I-KXE2S322BR57&custom=0&payer_status=unverified&business=d_sale_1359066991_biz@hotmail.com&address_country=United States&address_city=San Jose&verify_sign=AvsDjOMmqyESfLs3QFedB-d7.oTyAG95tzq0yL1yXFFQ10XJlL99Ysr9&payer_email=bayer_1262801479_per@hotmail.com&btn_id=3883600&last_name=Chernov&address_state=CA&receiver_email=d_sale_1359066991_biz@hotmail.com&recurring=1&txn_type=subscr_signup&item_name=DelivRD&mc_currency=USD&item_number=0001&residence_country=US&test_ipn=1&period3=1 D&ipn_track_id=7bde0eb05113f';
        
        $data = parse_str($query_str, $res);
        pr($res);
        exit;*/
        // Subscription signup
        /*$data = array(
            'txn_type' => 'subscr_signup',
            'subscr_id' => 'I-GPWUGTHY7UMU',
            'last_name' => 'Chernyavskij',
            'residence_country' => 'UA',
            'mc_currency' => 'USD',
            'item_name' => 'Delivrd Subscription',
            'business' => 'fordenis70@gmail.com',
            'recurring' => 1,
            'verify_sign' => 'A3Y1IabViDnLM.hMAUvK-kr83JP5AGwPOZTfcum17OATCH6pacuf9Qvq',
            'payer_status' => 'verified',
            'payer_email' => 'bayer_1262801479_per@hotmail.com',
            'first_name' => 'Denis',
            'receiver_email' => 'fordenis70@gmail.com',
            'payer_id' => 'K638H2ZNE57VW',
            'reattempt' => 1,
            'item_number' => 3,
            'subscr_date' => '12:41:47 Apr 28, 2018 PDT',
            //'custom' => '59f8e20a-b2d4-4a4a-a127-0724d6d0b2e3', // user_id
            'custom' => '', // user_id
            'charset' => 'windows-1252',
            'notify_version' => 3.8,
            'period1' => '7 D',
            'mc_amount1' => 0.00,
            'period3' => '1 M',
            'mc_amount3' => 49.99,
            'ipn_track_id' => '5b8fcc72e70f1'
        );*/

        // Subscription payment
        /*$data = array (
            'transaction_subject' => 'Unlimited Plan',
            'payment_date' => '16:12:06 July 10, 2018 PDT',
            'txn_type' => 'subscr_payment',
            'subscr_id' => 'I-GPWUGTHY7UMW',
            'last_name' => 'Chernyavskij',
            'residence_country' => 'UA',
            'item_name' => 'Delivrd Subscription',
            'payment_gross' => '',
            'mc_currency' => 'USD',
            'business' => 'fordenis70@gmail.com',
            'payment_type' => 'instant',
            'protection_eligibility' => 'Ineligible',
            'verify_sign' => 'A2jrx-KercBz8RSwEkW0EVl7N0dWApskLtziD.1ZEijd0P42Zc.iZGZc',
            'payer_status' => 'verified',
            'payer_email' => 'alex.soocra@mail.com',
            'txn_id' => '7VK88387G4831433H',
            'receiver_email' => 'fordenis70@gmail.com',
            'first_name' => 'Denis',
            'payer_id' => 'K638H2ZNE57VW',
            'receiver_id' => 'KPGVTK2AE38RS',
            'item_number' => 3,
            'payment_status' => 'Completed',
            'payment_fee' => '',
            'mc_fee' => 0.88,
            'mc_gross' => 49.99,
            'custom' => '59f8e20a-b2d4-4a4a-a127-0724d6d0b2e3',
            'charset' => 'windows-1252',
            'notify_version' => 3.8,
            'ipn_track_id' => 'b308bfc5cc5aa',
        );

        $req = http_build_query($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Router::url(array('plugin' => 'paypal', 'controller' => 'payment', 'action' => 'ipn'), true));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: delivrd.ys'));
        $res = curl_exec($ch);
        curl_close($ch);
        pr($res);
        exit;*/
    }

    //amount3=49.99&address_status=confirmed&subscr_date=11:53:01 Aug 13, 2018 PDT&payer_id=7DXWNGDETLW3W&address_street=1 Main St&mc_amount3=49.99&charset=windows-1252&address_zip=95131&first_name=Den&reattempt=1&address_country_code=US&address_name=Den Chernov&notify_version=3.9&subscr_id=I-3CSGKT5C57JL&custom=5a953515-cb5c-4b32-b060-0437a2f34d2b&payer_status=unverified&business=d_sale_1359066991_biz@hotmail.com&address_country=United States&address_city=San Jose&verify_sign=AnyMl.zBSVP237k4PR3AXcp4FVWoA18DtG4IBu3pE2J6Iw.FL7OMGkm5&payer_email=bayer_1262801479_per@hotmail.com&btn_id=3883600&last_name=Chernov&address_state=CA&receiver_email=d_sale_1359066991_biz@hotmail.com&recurring=1&txn_type=subscr_signup&item_name=DelivRD&mc_currency=USD&item_number=0001&residence_country=US&test_ipn=1&period3=1 D&ipn_track_id=d88f10f08ba87
    //mc_gross=49.99&protection_eligibility=Eligible&address_status=confirmed&payer_id=7DXWNGDETLW3W&address_street=1 Main St&payment_date=11:53:05 Aug 13, 2018 PDT&payment_status=Completed&charset=windows-1252&address_zip=95131&first_name=Den&mc_fee=1.75&address_country_code=US&address_name=Den Chernov&notify_version=3.9&subscr_id=I-3CSGKT5C57JL&custom=5a953515-cb5c-4b32-b060-0437a2f34d2b&payer_status=unverified&business=d_sale_1359066991_biz@hotmail.com&address_country=United States&address_city=San Jose&verify_sign=Al3UTKLI1i68WmIDgQz7KCVQD1kLArGfsDczoFcejBpnA.g-DAtd3Kjl&payer_email=bayer_1262801479_per@hotmail.com&txn_id=2JP5160428092844G&payment_type=instant&btn_id=3883600&last_name=Chernov&address_state=CA&receiver_email=d_sale_1359066991_biz@hotmail.com&payment_fee=1.75&receiver_id=7XZMYXZ6PMMPJ&txn_type=subscr_payment&item_name=DelivRD&mc_currency=USD&item_number=0001&residence_country=US&test_ipn=1&transaction_subject=DelivRD&payment_gross=49.99&ipn_track_id=d88f10f08ba87
    //?tx=2JP5160428092844G&st=Completed&amt=49.99&cc=USD&cm=5a953515-cb5c-4b32-b060-0437a2f34d2b&item_number=0001&sig=orhici4fyJQFfEroUHH%2bVlgzMKxR8KqvvLr77Nb4GEtJLKKtSH4Ro2%2fIqezmWcM8mJl2XHRZBKM1PhyGhJS8YZZXqMVEThj%2fhGTqH8YHGZUvjb7ttFvIJyJ9NhRh1afrGp943pptupcRPbwAV%2fzCL4LtIgMxmwS2lTCySWaLNN8%3d
    // Return url: ex:http://mstr.nanodc.net/app/paypal/payment/success?tx=7X64073694890780R&st=Completed&amt=49.99&cc=USD&cm=0&item_number=0001&sig=Le6mWvLiB10d4PqqS3r4cMBggbee%2fimBQsLqMVyrUz7wd9qObAx0CKG3q7HdwQpz4Txs5YSjeatr0Jd%2bodp%2bzSX7Va6Lk8kxFSWiyagbUvEGmxQ%2b5%2b3lkghLtgr3G9XXGcRFpDbM43P%2beE2Nd6Vf1UlP5WKptUqd0PY1TCpuKjk%3d
    public function success() {
        $result = $this->request->query;
        if ($this->request->query('tx')) {
            if($this->request->query('st') == 'Completed') {
                if($this->request->query('cm') == $this->Auth->user('id')) {
                    
                    $tx_token = $this->request->query('tx');

                    $paypal = new PaypalIPN();
                    if(false && function_exists('curl_init()')) { //
                        
                        if(Configure::read('OperatorName') == 'Delivrd' && $this->request->host() == 'delivrdapp.com') {
                            // Real live version
                        } else {
                            // Sandbox version
                            $paypal->useSandbox();
                        }


                        $result = $paypal->verifyPDT($tx_token);

                        // Debuging
                    } else {
                        $subscription = $this->Subscription->find('first', ['conditions' => array('Subscription.user_id' => $this->Auth->user('id'))]);
                        if($subscription) {
                            $this->Subscription->id = $subscription['Subscription']['id'];
                            $subscription['Subscription']['ext_id'] = 'WAIT-CONFIRM';
                            $subscription['Subscription']['status'] = 'Active';
                            $subscription['Subscription']['amount'] = $this->request->query('amt');
                            $subscription['Subscription']['expiry_date'] = $paypal->plusMonth(date('Y-m-d'));
                            $subscription['Subscription']['memo'] = 'Payment Completed, but not verified';
                            $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');

                            if($this->Subscription->save($subscription)) {
                                $this->User->id = $this->Auth->user('id');
                                $this->User->saveField('role', 'paid');
                                $this->User->saveField('paid', 1);
                                $this->User->saveField('locationsactive', 1);
                            }

                        } else {
                            $subscription['Subscription']['ext_id'] = 'WAIT-CONFIRM';
                            $subscription['Subscription']['status'] = 'Active';
                            $subscription['Subscription']['user_id'] = $this->Auth->user('id');
                            $subscription['Subscription']['amount'] = $this->request->query('amt');
                            $subscription['Subscription']['expiry_date'] = $paypal->plusMonth(date('Y-m-d'));
                            $subscription['Subscription']['created'] = date('Y-m-d H:i:s');
                            $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');
                            $subscription['Subscription']['memo'] = 'Payment Completed, but not verified';
                            
                            if($this->Subscription->save($subscription)) {
                                $this->User->id = $this->Auth->user('id');
                                $this->User->saveField('role', 'paid');
                                $this->User->saveField('paid', 1);
                                $this->User->saveField('locationsactive', 1);
                            }
                        }
                        #$this->_authUser = $this->User->getAuthUser($this->Auth->user('id'));
                        #$this->Account->reNewUserSettings($this->_authUser);
                    }

                    $result = 1;
                    if($result) {
                        $status = 'completed';
                    } else {
                        $status = 'failed';
                    }
                } else {
                    $status = 'error';
                }
            } else {
                $status = $this->request->query('st');
            }
        } else {
            // If no tx parametr then free trial
            $paypal = new PaypalIPN();
            $subscription = $this->Subscription->find('first', ['conditions' => array('Subscription.user_id' => $this->Auth->user('id'))]);
            if($subscription) {
                $this->Subscription->id = $subscription['Subscription']['id'];
                $subscription['Subscription']['ext_id'] = 'WAIT-CONFIRM';
                $subscription['Subscription']['status'] = 'Active';
                $subscription['Subscription']['amount'] = 0.00;
                $subscription['Subscription']['expiry_date'] = $paypal->plusMonth(date('Y-m-d'));
                $subscription['Subscription']['memo'] = 'Payment Completed, but not verified';
                $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');

                if($this->Subscription->save($subscription)) {
                    $this->User->id = $this->Auth->user('id');
                    $this->User->saveField('role', 'paid');
                    $this->User->saveField('paid', 1);
                    $this->User->saveField('locationsactive', 1);
                }

            } else {
                $subscription['Subscription']['ext_id'] = 'WAIT-CONFIRM';
                $subscription['Subscription']['status'] = 'Active';
                $subscription['Subscription']['user_id'] = $this->Auth->user('id');
                $subscription['Subscription']['amount'] = 0.00;
                $subscription['Subscription']['expiry_date'] = $paypal->plusMonth(date('Y-m-d'));
                $subscription['Subscription']['created'] = date('Y-m-d H:i:s');
                $subscription['Subscription']['modified'] = date('Y-m-d H:i:s');
                $subscription['Subscription']['memo'] = 'Payment Completed, but not verified';
                
                if($this->Subscription->save($subscription)) {
                    $this->User->id = $this->Auth->user('id');
                    $this->User->saveField('role', 'paid');
                    $this->User->saveField('paid', 1);
                    $this->User->saveField('locationsactive', 1);
                }
            }
            $status = 'trial';
        }
        return $this->redirect(array('action' => 'complete', $status));
    }

    public function complete($status='complete') {
        $this->_authUser = $this->User->getAuthUser($this->Auth->user('id'));
        $this->Account->reNewUserSettings($this->_authUser);
        $this->set(compact('status'));
    }

    public function cancel() {
        $result = $this->request->query;
        $this->set(compact('result'));
    }
}