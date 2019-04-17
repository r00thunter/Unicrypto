<?php

class AstroPayCard {
    /*     * *********** DO NOT CHANGE! *********** */

    //Defined data
    private $base_url = "https://api.astropaycard.com/";
    private $sandbox_base_url = "https://sandbox-api.astropaycard.com/";
    
    private $validator_url = "verif/validator";
    private $transtatus_url = "verif/transtatus";
    /*     * *********** ************ ************* */

    /*     * ******** CHANGE HERE ************ */   
    //Credentials
    private $x_login = "CHANGE HERE";
    private $x_trans_key = "CHANGE HERE";
    //General settings
    private $x_version = "2.0"; //AstroPay API version (default "2.0")
    public $x_delim_char = "|"; //Field delimit character, the character that separates the fields (default "|")
    private $x_test_request; //Change to N for production
    private $x_duplicate_window = 120; //Time window of a transaction with the sames values is taken as duplicated (default 120)
    private $x_method = "CC";
    private $x_response_format = "json"; //Response format: "string", "json", "xml" (default: string) (recommended: json)
    
    //Sandbox (TODO: Change to false in production)
    private $sandbox = true;
    
    public function __construct() {
        if($this->sandbox){
            $this->validator_url = "$this->sandbox_base_url$this->validator_url";
            $this->transtatus_url = "$this->sandbox_base_url$this->transtatus_url";
            $this->x_test_request = "N";
        } else{
            $this->validator_url = "$this->base_url$this->validator_url";
            $this->transtatus_url = "$this->base_url$this->transtatus_url";
            $this->x_test_request = "N";
        }
    }

    /*     * ******** ******* **************** */
    
    /**
     * Authorizes a transaction
     * @param string $x_card_num AstroPay Card number (16 digits)
     * @param string $x_card_code AstroPay Card security code (CVV)
     * @param string $x_exp_date AstroPay Card expiration date
     * @param double $x_amount Amount of the transaction
     * @param string $x_unique_id Unique user ID of the merchant
     * @param string $x_invoice_num Merchant transaction identificator, i.e. the order number
     * @param array $additional_params Array of additional info that you would send to AstroPay for reference purpose.
     * @return array Array of params returned by AstroPay capture API. Please see section 3.1.3 "Response" of AstroPay Card integration manual for more info
     */
    public function auth_transaction($x_card_num, $x_card_code, $x_exp_date, $x_amount, $x_unique_id, $x_invoice_num, $additional_params = null){
        $data['x_login'] = $this->x_login;
        $data['x_tran_key'] = $this->x_trans_key;
        $data['x_card_num'] = $x_card_num;
        $data['x_card_code'] = $x_card_code;
        $data['x_exp_date'] = $x_exp_date;
        $data['x_amount'] = $x_amount;
        $data['x_unique_id'] = $x_unique_id;
        $data['x_version'] = $this->x_version;
        $data['x_test_request'] = $this->x_test_request;
        $data['x_duplicate_window'] = $this->x_duplicate_window;
        $data['x_method'] = $this->x_method;
        $data['x_invoice_num'] = $x_invoice_num;
        $data['x_delim_char'] = $this->x_delim_char;
        $data['x_response_format'] = $this->x_response_format;
        
        $data['x_type'] = "AUTH_ONLY";

        //Optional: Additional params
        if (is_array($additional_params)) {
            foreach ($additional_params as $key => $value) {
                $data[$key] = $value;
            }
        }

        $response = $this->send_curl($this->validator_url, $data);

        return $response;
    }

    /**
     * Caputures previous authorized transaction
     * @param string $x_auth_code The x_auth_code returned by ::auth_transaction method
     * @param string $x_card_num AstroPay Card number (16 digits)
     * @param string $x_card_code AstroPay Card security code (CVV)
     * @param string $x_exp_date AstroPay Card expiration date
     * @param double $x_amount Amount of the transaction
     * @param string $x_unique_id Unique user ID of the merchant
     * @param string $x_invoice_num Merchant transaction identificator, i.e. the order number
     * @param array $additional_params Array of additional info that you would send to AstroPay for reference purpose.
     * @return array Array of params returned by AstroPay capture API. Please see section 3.1.3 "Response" of AstroPay Card integration manual for more info
     */
    public function capture_transaction($approval_code, $x_card_num, $x_card_code, $x_exp_date, $x_amount, $x_unique_id, $x_invoice_num, $additional_params = null){
        $data['x_login'] = $this->x_login;
        $data['x_tran_key'] = $this->x_trans_key;
        $data['x_card_num'] = $x_card_num;
        $data['x_card_code'] = $x_card_code;
        $data['x_exp_date'] = $x_exp_date;
        $data['x_amount'] = $x_amount;
        $data['x_unique_id'] = $x_unique_id;
        $data['x_version'] = $this->x_version;
        $data['x_test_request'] = $this->x_test_request;
        $data['x_duplicate_window'] = $this->x_duplicate_window;
        $data['x_method'] = $this->x_method;
        $data['x_invoice_num'] = $x_invoice_num;
        $data['x_delim_char'] = $this->x_delim_char;
        $data['x_response_format'] = $this->x_response_format;
        
        $data['x_auth_code'] = $approval_code;

        $data['x_type'] = "CAPTURE_ONLY";

        //Optional: Additional params
        if (is_array($additional_params)) {
            foreach ($additional_params as $key => $value) {
                $data[$key] = $value;
            }
        }

        $response = $this->send_curl($this->validator_url, $data);

        return $response;
    }

    /**
     * Authorize and capture a transaction at the same time (if it is possible)
     * @param string $x_card_num AstroPay Card number (16 digits)
     * @param string $x_card_code AstroPay Card security code (CVV)
     * @param string $x_exp_date AstroPay Card expiration date
     * @param double $x_amount Amount of the transaction
     * @param string $x_unique_id Unique user ID of the merchant
     * @param string $x_invoice_num Merchant transaction identificator, i.e. the order number
     * @param array $additional_params Array of additional info that you would send to AstroPay for reference purpose.
     * @return array Array of params returned by AstroPay capture API. Please see section 3.1.3 "Response" of AstroPay Card integration manual for more info
     */
    public function auth_capture_transaction($x_card_num, $x_card_code, $x_exp_date, $x_amount, $x_unique_id, $x_invoice_num, $additional_params = null) {
        $data['x_login'] = $this->x_login;
        $data['x_tran_key'] = $this->x_trans_key;
        $data['x_card_num'] = $x_card_num;
        $data['x_card_code'] = $x_card_code;
        $data['x_exp_date'] = $x_exp_date;
        $data['x_amount'] = $x_amount;
        $data['x_unique_id'] = $x_unique_id;
        $data['x_version'] = $this->x_version;
        $data['x_test_request'] = $this->x_test_request;
        $data['x_duplicate_window'] = $this->x_duplicate_window;
        $data['x_method'] = $this->x_method;
        $data['x_invoice_num'] = $x_invoice_num;
        $data['x_delim_char'] = $this->x_delim_char;
        $data['x_response_format'] = $this->x_response_format;

        $data['x_type'] = "AUTH_CAPTURE";

        //Optional: Additional params
        if (is_array($additional_params)) {
            foreach ($additional_params as $key => $value) {
                $data[$key] = $value;
            }
        }

        $response = $this->send_curl($this->validator_url, $data);

        return $response;
    }

    /**
     * Refund a transaction
     * @param string $transaction_id merchant invoice number sent in previus call of capture_transaction or auth_transaction
     * @param string $x_card_num AstroPay Card number (16 digits)
     * @param string $x_card_code AstroPay Card security code (CVV)
     * @param string $x_exp_date AstroPay Card expiration date
     * @param double $x_amount Amount of the transaction
     * @param array $additional_params Array of additional info that you would send to AstroPay for reference purpose.
     * @return array Array of params returned by AstroPay capture API. Please see section 3.2.2 "Response" of AstroPay Card integration manual for more info
     */
    public function refund_transaction($transaction_id, $x_card_num, $x_card_code, $x_exp_date, $x_amount, $additional_params = null) {
        $data['x_login'] = $this->x_login;
        $data['x_tran_key'] = $this->x_trans_key;
        $data['x_card_num'] = $x_card_num;
        $data['x_card_code'] = $x_card_code;
        $data['x_exp_date'] = $x_exp_date;
        $data['x_amount'] = $x_amount;
        $data['x_version'] = $this->x_version;
        $data['x_test_request'] = $this->x_test_request;
        $data['x_duplicate_window'] = $this->x_duplicate_window;
        $data['x_method'] = $this->x_method;
        $data['x_trans_id'] = $transaction_id;
        $data['x_delim_char'] = $this->x_delim_char;
        $data['x_response_format'] = $this->x_response_format;

        $data['x_type'] = "REFUND";

        //Optional: Additional params
        if (is_array($additional_params)) {
            foreach ($additional_params as $key => $value) {
                $data[$key] = $value;
            }
        }

        $response = $this->send_curl($this->validator_url, $data);

        return $response;
    }

    /**
     * VOID a transaction
     * @param string $transaction_id merchant invoice number sent in previus call of capture_transaction or auth_transaction
     * @param string $x_card_num AstroPay Card number (16 digits)
     * @param string $x_card_code AstroPay Card security code (CVV)
     * @param string $x_exp_date AstroPay Card expiration date
     * @param double $x_amount Amount of the transaction
     * @param array $additional_params Array of additional info that you would send to AstroPay for reference purpose.
     * @return array Array of params returned by AstroPay capture API. Please see section 3.2.2 "Response" of AstroPay Card integration manual for more info
     */
    public function void_transaction($transaction_id, $x_card_num, $x_card_code, $x_exp_date, $x_amount, $additional_params = null) {
        $data['x_login'] = $this->x_login;
        $data['x_tran_key'] = $this->x_trans_key;
        $data['x_card_num'] = $x_card_num;
        $data['x_card_code'] = $x_card_code;
        $data['x_exp_date'] = $x_exp_date;
        $data['x_amount'] = $x_amount;
        $data['x_version'] = $this->x_version;
        $data['x_test_request'] = $this->x_test_request;
        $data['x_duplicate_window'] = $this->x_duplicate_window;
        $data['x_method'] = $this->x_method;
        $data['x_trans_id'] = $transaction_id;
        $data['x_delim_char'] = $this->x_delim_char;
        $data['x_response_format'] = $this->x_response_format;

        $data['x_type'] = "VOID";

        //Optional: Additional params
        if (is_array($additional_params)) {
            foreach ($additional_params as $key => $value) {
                $data[$key] = $value;
            }
        }

        $response = $this->send_curl($this->validator_url, $data);

        return $response;
    }

    /**
     * Checks the status of a transaction
     * @param string $x_invoice_num The merchant id sent in the transaction
     * @param int $type 0 for basic info, 1 for detailed info
     * @return array Response array. Please see section 3.2.3 of APC integration manual from more details.
     */
    public function check_transaction_status($x_invoice_num, $type = 0) {
        $data['x_login'] = $this->x_login;
        $data['x_trans_key'] = $this->x_trans_key;
        $data['x_invoice_num'] = $x_invoice_num;
        $data['x_delim_char'] = $this->x_delim_char;
        $data['x_test_request'] = $this->x_test_request;
        $data['x_response_format'] = $this->x_response_format;
        $data['x_type'] = $type;

        $response = $this->send_curl($this->transtatus_url, $data);

        return $response;
    }

    private function send_curl($url, $fields_array) {
        $fields = '';
        $first = true;
        foreach ($fields_array as $key => $value) {
            if (!$first) {
                $fields .= '&';
            }
            $fields .= "$key=$value";
            $first = false;
        }
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $curl_response = curl_exec($ch);
        curl_close($ch);

        return $curl_response;
    }

}

?>