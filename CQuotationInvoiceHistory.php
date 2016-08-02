<?php

if (!defined('DP_BASE_DIR')){
		die('You should not access this file directly.');
}

class CQuotationInvoiceHistory extends CDpObject {

    public $quo_invc_history_id;
    public $quo_invc_id;
    public $quo_or_invc_history;
    public $quo_invc_history_type;
    public $quo_invc_history_update;
    public $quo_invc_history_user;
    public $quo_invc_history_date;

    public function CQuotationInvoiceHistory() {
            $this-> CDpObject("quo_invc_history", "quo_invc_history_id");
    }

}

?>
