<?php
class PaypalIPN
{
    /**
     * @var bool $use_sandbox     Indicates if the sandbox endpoint is used.
     */
    private $use_sandbox = false;
    /**
     * @var bool $use_local_certs Indicates if the local certificates are used.
     */
    private $use_local_certs = false;
    /** Production Postback URL */
    const VERIFY_URI = 'https://ipnpb.paypal.com/cgi-bin/webscr';
    /** Sandbox Postback URL */
    const SANDBOX_VERIFY_URI = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
    /** Response from PayPal indicating validation was successful */
    const VALID = 'VERIFIED';
    /** Response from PayPal indicating validation failed */
    const INVALID = 'INVALID';
    /**
     * Sets the IPN verification to sandbox mode (for use when testing,
     * should not be enabled in production).
     * @return void
     */
    public function useSandbox()
    {
        $this->use_sandbox = true;
    }
    /**
     * Sets curl to use php curl's built in certs (may be required in some
     * environments).
     * @return void
     */
    public function usePHPCerts()
    {
        $this->use_local_certs = false;
    }
    /**
     * Determine endpoint to post the verification data to.
     * @return string
     */
    public function getPaypalUri()
    {
        if ($this->use_sandbox) {
            return self::SANDBOX_VERIFY_URI;
        } else {
            return self::VERIFY_URI;
        }
    }

    

    /**
     * Verification Function
     * Sends the incoming post data back to PayPal using the cURL library.
     *
     * @return bool
     * @throws Exception
     */
    public function verifyIPN()
    {
        if ( ! count($_POST)) {
            throw new Exception("Missing POST Data");
        }
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                // Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
                if ($keyval[0] === 'payment_date') {
                    if (substr_count($keyval[1], '+') === 1) {
                        $keyval[1] = str_replace('+', '%2B', $keyval[1]);
                    }
                }
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }
        // Build the body of the verification post request, adding the _notify-validate command.
        $req = 'cmd=_notify-validate';
        $get_magic_quotes_exists = false;
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }
        // Post the data back to PayPal, using curl. Throw exceptions if errors occur.
        $ch = curl_init($this->getPaypalUri());
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        // This is often required if the server is missing a global cert bundle, or is using an outdated one.
        if ($this->use_local_certs) {
            curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/cert/cacert.pem");
        }
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        $res = curl_exec($ch);
        if ( ! ($res)) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: [$errno] $errstr");
        }
        $info = curl_getinfo($ch);
        $http_code = $info['http_code'];
        if ($http_code != 200) {
            throw new Exception("PayPal responded with http code $http_code");
        }
        curl_close($ch);
        // Check if PayPal verifies the IPN data, and if so, return true.
        if ($res == self::VALID) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Determine endpoint to post the verification data to.
     * @return string
     */
    public function getPaypalHost()
    {
        if ($this->use_sandbox) {
            return 'www.sandbox.paypal.com';
        } else {
            return 'www.paypal.com';;
        }
    }

    /**
     * Verification Function
     * Sends the incoming post data back to PayPal using the cURL library.
     *
     * @return bool
     * @throws Exception
     */
    public function verifyPDT($tx_token)
    {
        $req = 'cmd=_notify-synch';
        
        $auth_token = 'll-wcdqv9aO3F1eHBpTfDEWVooRFh1V7u3JDoEVyeIQOna57n44KVyeyDSa';
        $req .= "&tx=$tx_token&at=$auth_token";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://'. $this->getPaypalHost() .'/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: ". $this->getPaypalHost()));
        $res = curl_exec($ch);
        curl_close($ch);
        
        if(!$res){
            //HTTP ERROR
            return false;
        } else {
            $lines = explode("\n", $res);
            $keyarray = array();
            if (strcmp ($lines[0], "SUCCESS") == 0) {
                for ($i=1; $i<count($lines); $i++) {
                    if(!empty($lines[$i])) {
                        list($key,$val) = explode("=", $lines[$i]);
                        $keyarray[urldecode($key)] = urldecode($val);
                    }
                }
                return $keyarray;
            } else {
                return false;
            }
        }
    }

    function plusMonth($date) {
        $day = date('j', strtotime($date));

        $month = date('n', strtotime($date)) + 1;
        $year = date('Y', strtotime($date));
        if($month > 12) {
            $month = 1;
            $year = $year + 1;
        }
        $h = date('H:i:s');
        if(date('t', strtotime($year .'-'. $month)) < $day) {
            $day = 1;
            $h = '00:00:00';
            $month = $month + 1;
            if($month > 12) {
                $month = 1;
                $year = $year + 1;
            }
        }

        return date('Y-m-d H:i:s', strtotime($year .'-'. $month .'-'. $day .' '. $h));
    }
}