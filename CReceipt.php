<?php

if (!defined('DP_BASE_DIR')){
		die('You should not access this file directly.');
}

class CReceipt extends CDpObject {

    public $receipt_id;
    public $payment_id;
    public $receipt_no;

    public function CReceipt() {
            $this-> CDpObject("sales_receipt", "receipt_id");
    }

}

?>
