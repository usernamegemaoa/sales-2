<?php

if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

class CMemoBillingAdress extends CDpObject {

    public $memo_id;
    public $customer_id;
    public $address_id;
    public $attention_id;
    public function CMemoBillingAdress() {
            $this-> CDpObject("sales_memo_billingadress", "memo_id");
    }

}

?>

