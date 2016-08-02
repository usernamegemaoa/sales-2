<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
class CCreditNote extends CDpObject{
    public $credit_note_id;
    public $user_id;
    public $invoice_id;
//    public $suppllier_id;
    public $customer_id;
    public $address_id;
    public $credit_note_no;
    public $credit_note_date;
    public $credit_note_status;
    public $credit_note_tax_id;
    public $tax_edit_value;
    public $credit_note_co;
    public function CCreditNote (){
        $this->CDpObject('sales_credit_note', "credit_note_id");
    }
}

