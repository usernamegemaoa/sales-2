<?php

if (!defined('DP_BASE_DIR')){
		die('You should not access this file directly.');
}

class CPaymentSchedule extends CDpObject {

    public $payment_schedule_id;
    public $invoice_revision_id;
    public $payment_schedule_paid;
    public $payment_schedule_paid_date;
    public $payment_schedule_notes;

    public function CPaymentSchedule() {
            $this-> CDpObject("sales_payment_schedule", "payment_schedule_id");
    }

}

?>
