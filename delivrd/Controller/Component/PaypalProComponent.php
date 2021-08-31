<?php
class PaypalProComponent extends Component {

	function __construct()
	{	
		
		$this->API_USERNAME = Configure::read('Paypal.API_USERNAME'); 
		$this->API_PASSWORD = Configure::read('Paypal.API_PASSWORD');
		$this->API_SIGNATURE = Configure::read('Paypal.API_SIGNATURE');
		$this->API_ENDPOINT = Configure::read('Paypal.API_ENDPOINT');
		$this->USE_PROXY = Configure::read('Paypal.USE_PROXY');
		$this->PROXY_HOST =Configure::read('Paypal.PROXY_HOST');
		$this->PROXY_PORT = Configure::read('Paypal.PROXY_PORT');
		$this->PAYPAL_URL = Configure::read('Paypal.PAYPAL_URL');
		$this->VERSION = Configure::read('Paypal.VERSION');
		
	}

	function hash_call($methodName,$nvpStr)
	{	
		$this->PROXY_PORT ; 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$this->API_ENDPOINT);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		if($this->USE_PROXY)
		{
			curl_setopt ($ch, CURLOPT_PROXY, $this->PROXY_HOST.":".$this->PROXY_PORT); 
		}
		$nvpreq="METHOD=".urlencode($methodName)."&VERSION=".urlencode($this->VERSION)."&PWD=".urlencode($this->API_PASSWORD)."&USER=".urlencode($this->API_USERNAME)."&SIGNATURE=".urlencode($this->API_SIGNATURE).$nvpStr;
		curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);
		$response = curl_exec($ch);
		$nvpResArray=$this->deformatNVP($response);
		$nvpReqArray=$this->deformatNVP($nvpreq);
		$_SESSION['nvpReqArray']=$nvpReqArray;
		
		
		if (curl_errno($ch))
		{
			die("CURL send a error during perform operation: ".curl_errno($ch));
		} 
		else 
		{
			curl_close($ch);
		}

	return $nvpResArray;
	}

	function deformatNVP($nvpstr)
	{
		$intial=0;
		$nvpArray = array();
		while(strlen($nvpstr))
		{
			$keypos= strpos($nvpstr,'='); 
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr); 
			$keyval=substr($nvpstr,$intial,$keypos);
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
		 }
		return $nvpArray;
	}

	function __destruct() 
	{

	}
}