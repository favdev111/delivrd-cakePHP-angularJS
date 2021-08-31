<?php
App::uses('AppController', 'Controller');
/**
 * Plans Controller
 
 * @property Plan $Plan
 * @property PaginatorComponent $Paginator
 */
class PlansController extends Controller {
/**
 * Components
 *
 * @var array
 */
	public $components = array('PaypalPro','Session');
	public $paginate = array();
		public $theme = 'Mtro-front';
	

/**
 * index method
 *
 * @return void
 */
	public function index($id = false) {
		
		$Auth = $this->Session->read();
			 if(isset($Auth['Auth']))
			 {
				 
				if($id == 1)
				{
					$plan_name = 'ENTERPRISE';
					$amount = 0;
					$description = 'Custom solutions for any need';
				}
				elseif($id = 2){
					$plan_name = 'PRO';
					$amount = 120;
					$description = 'All you re ever need';
				}
				elseif($id = 3){
					$plan_name = 'PLUS';
					$amount = 499;
					$description = 'Most popular';
				}
				elseif($id = 4){
					$plan_name = 'BASIC';
					$amount = 0;
					$description = 'up to 25 products';
				}
				$this->set('plan_id', $id);
				$this->set('plan_name', $plan_name);
				$this->set('amount', $amount);
				$this->set('description', $description);
				 
				if ($this->request->is('post')) 
				{
					$firstName =urlencode( $_POST['firstName']);
					$creditCardType =urlencode( $_POST['creditCardType']);
					$creditCardNumber = urlencode($_POST['creditCardNumber']);
					$expDateMonth =urlencode( $_POST['expDateMonth']);
					$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
					$expDateYear =urlencode( $_POST['expDateYear']);
					$cvv2Number = urlencode($_POST['cvv2Number']);
					$address1 = urlencode($_POST['address']);
					$city = urlencode($_POST['city']);
					$state =urlencode( $_POST['state']);
					$zip = urlencode($_POST['zip']);
					$amount = urlencode($_POST['amount']);
					$currencyCode="USD";
					$paymentAction = urlencode("Sale");
					if($_POST['recurring'] == 1) // For Recurring
					{
						$profileStartDate = urlencode(date_format(date_add(date_create(date("Y-m-d h:i:s")),date_interval_create_from_date_string("30 days")),"Y-m-d h:i:s"));
						//$billingPeriod = urlencode($_POST['billingPeriod']);// or "Day", "Week", "SemiMonth", "Year"
						$billingPeriod = "Month";
						//$billingFreq = urlencode($_POST['billingFreq']);// combination of this and billingPeriod must be at most a year
						$billingFreq = 1;
						$initAmt = $amount;
						$failedInitAmtAction = urlencode("ContinueOnFailure");
						$desc = urlencode("Recurring $".$amount);
						$autoBillAmt = urlencode("AddToNextBilling");
						$profileReference = urlencode("Anonymous");
						$methodToCall = 'CreateRecurringPaymentsProfile';
						$nvpRecurring ='&BILLINGPERIOD='.$billingPeriod.'&BILLINGFREQUENCY='.$billingFreq.'&PROFILESTARTDATE='.$profileStartDate.'&INITAMT='.$initAmt.'&FAILEDINITAMTACTION='.$failedInitAmtAction.'&DESC='.$desc.'&AUTOBILLAMT='.$autoBillAmt.'&PROFILEREFERENCE='.$profileReference;
					}
					else
					{
						$nvpRecurring = '';
						$methodToCall = 'doDirectPayment';
					}
					$nvpstr='&PAYMENTACTION='.$paymentAction.'&AMT='.$amount.'&ACCT='.$creditCardNumber.'&EXPDATE='.         $padDateMonth.$expDateYear.'&CVV2='.$cvv2Number.'&FIRSTNAME='.$firstName.'&STREET='.$address1.'&CITY='.$city.'&STATE='.$state.'&ZIP='.$zip.'&COUNTRYCODE=US&CURRENCYCODE='.$currencyCode.$nvpRecurring;
					$res = $this->PaypalPro->hash_call($methodToCall,$nvpstr);
					$this->layout = 'mtrd-front';
				
					if(($res['ACK'] == 'Success')){
						$this->loadModel('Payment');
						$this->Payment->create();
						$this->request->data('Payment.user_id',$Auth['Auth']['User']['id']);
						if($res['PROFILESTATUS'] == 'PendingProfile'){
							$this->request->data('Payment.transcation_id',$res['PROFILEID']);
						}else{
							$this->request->data('Payment.transcation_id',$res['TRANSACTIONID']);
						}
						$this->request->data('Payment.payment_method',1);
						$this->request->data('Payment.recurring',$billingPeriod);
						$this->request->data('Payment.user_name',$firstName);
						$this->request->data('Payment.address',$address1);
						$this->request->data('Payment.city',$city);
						$this->request->data('Payment.state',$state);
						$this->request->data('Payment.zip',$zip);
						$this->request->data('Payment.country',$currencyCode);
						$this->request->data('Paymen.datetime','CURRENT_TIMESTAMP');
						if (!$res = $this->Payment->save($this->request->data)) 
						{
							$this->set('message', 'error while making Payment');
						}else{
							$this->set('message', 'payment completed successfully');
							$this->Auth->flash(__d('users', 'Payment completed successfully.'),'default',array('class'=>'alert alert-success'));
							//$this->Session->setFlash(__('Payment completed successfully.'), 'admin/success', array());
							return $this->redirect(  array('plugin' => 'users', 'controller' => 'users', 'action' => 'login')  );
						}
						
						$this->render('response');
					}else{
						$this->set('error', $res['L_LONGMESSAGE0']);
						$this->render('response');
					}
					
				}else{
					$this->layout = 'mtrd-front';
					$this->render('view');
				}
		}else{
			return $this->redirect(  array('plugin' => 'users', 'controller' => 'users', 'action' => 'login')  );
		}
	}
	
	public function plan_details($id = false)
	{
		$this->render('view');
		if ($this->request->is('post')) {
			$firstName =urlencode( $_POST['firstName']);
			$creditCardType =urlencode( $_POST['creditCardType']);
			$creditCardNumber = urlencode($_POST['creditCardNumber']);
			$expDateMonth =urlencode( $_POST['expDateMonth']);
			$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
			$expDateYear =urlencode( $_POST['expDateYear']);
			$cvv2Number = urlencode($_POST['cvv2Number']);
			$address1 = urlencode($_POST['address1']);
			$address2 = urlencode($_POST['address2']);
			$city = urlencode($_POST['city']);
			$state =urlencode( $_POST['state']);
			$zip = urlencode($_POST['zip']);
			$amount = urlencode($_POST['amount']);
			$currencyCode="USD";
			$paymentAction = urlencode("Sale");
			if($_POST['recurring'] == 1) // For Recurring
			{
				$profileStartDate = urlencode(date('Y-m-d h:i:s'));
				$billingPeriod = urlencode($_POST['billingPeriod']);// or "Day", "Week", "SemiMonth", "Year"
				$billingFreq = urlencode($_POST['billingFreq']);// combination of this and billingPeriod must be at most a year
				$initAmt = $amount;
				$failedInitAmtAction = urlencode("ContinueOnFailure");
				$desc = urlencode("Recurring $".$amount);
				$autoBillAmt = urlencode("AddToNextBilling");
				$profileReference = urlencode("Anonymous");
				$methodToCall = 'CreateRecurringPaymentsProfile';
				$nvpRecurring ='&BILLINGPERIOD='.$billingPeriod.'&BILLINGFREQUENCY='.$billingFreq.'&PROFILESTARTDATE='.$profileStartDate.'&INITAMT='.$initAmt.'&FAILEDINITAMTACTION='.$failedInitAmtAction.'&DESC='.$desc.'&AUTOBILLAMT='.$autoBillAmt.'&PROFILEREFERENCE='.$profileReference;
			}
			else
			{
				$nvpRecurring = '';
				$methodToCall = 'doDirectPayment';
			}
			$nvpstr='&PAYMENTACTION='.$paymentAction.'&AMT='.$amount.'&CREDITCARDTYPE='.$creditCardType.'&ACCT='.$creditCardNumber.'&EXPDATE='.         $padDateMonth.$expDateYear.'&CVV2='.$cvv2Number.'&FIRSTNAME='.$firstName.'&STREET='.$address1.'&CITY='.$city.'&STATE='.$state.'&ZIP='.$zip.'&COUNTRYCODE=US&CURRENCYCODE='.$currencyCode.$nvpRecurring;

			$res = $this->PaypalPro->hash_call($methodToCall,$nvpstr);
			$this->layout = 'mtrd-front';
			$this->set('res', $res);
			$this->render('response');
		}
		else{
			$this->layout = 'mtrd-front';
			$this->set('plan_id', $id);
			$this->render('index');
		}
		
	}
	public function checkout_payment(){
		$Auth = $this->Session->read();
		$id = $Auth['Auth']['User']['id'];
		$amount = $_POST['amount'];
		$transcation_id = $_POST['transaction_id'];
		$user_id = $_POST['transaction_id'];
		
		$this->loadModel('Payment');
		$this->Payment->create();
		$this->request->data('Payment.user_id',$id);
		$this->request->data('Payment.transcation_id',$transcation_id);
		$this->request->data('Payment.payment_method',2);
		$this->request->data('Payment.recurring','Month');
		$this->request->data('Paymen.datetime','CURRENT_TIMESTAMP');
		if (!$res = $this->Payment->save($this->request->data)) 
		{
			echo'errror';
		}else{
			echo'successfully';
		}
		die;
	}
	
	public function payment_response(){
		$this->layout = 'mtrd-front';
		$this->set('message', 'Payment completed successfully');
		// $this->set('error', 'Error while payment');
		$this->render('response');
	}
}