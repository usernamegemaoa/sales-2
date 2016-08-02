<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
class CCreditNoteItem extends CDpObject{
    public $credit_note_item_id;
    public $user_id;
    public $credit_note_id;
    public $credit_note_item;
    public $credit_note_item_amount;
    public $credit_note_item_order;
    
    public function CCreditNoteItem(){
        $this->CDpObject('sales_credit_note_item', "credit_note_item_id");
    } 
}

