<?php

if (!defined('DP_BASE_DIR')){
		die('You should not access this file directly.');
}

class CPaymentNew extends CDpObject {

    public $payment_id;
    public $payment_method;
    public $payment_date;
    public $payment_notes;
    public $payment_receipt_no;
    public $credit_note_id;
    public $bank_account_id;
    public $payment_check_reconcile;
    public $payment_cheque_nos;
    public $payment_description;

    public function CPaymentNew() {
            $this-> CDpObject("sales_payment", "payment_id");
    }

    function store($updateNulls = false) {
            $this->dPTrimAll();

            $msg = $this->check();
            if ($msg) {
                    return (get_class($this) . '::store-check failed<br />' . $msg);
            }
            $k = $this->_tbl_key;
            if ($this->$k) {
                    $store_type = 'update';
                    $ret = db_updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
            } 
            else {
                    $store_type = 'add';
                    $ret = db_insertObject($this->_tbl, $this, $this->_tbl_key);
            }
            if ($ret && !$cron) {
                    // only record history if an update or insert actually occurs.
                    $Obj = get_object_vars($this);
                    $content = serialize($Obj);
                    addHistory($this->_tbl, $this->$k, $store_type, 
                               ($this->_tbl . '_' . $store_type . '(' . $this->$k . ')'),0, $content);
            }
            return ((!$ret) ? (get_class($this) . '::store failed <br />' . db_error()) : NULL) ;
    }
    
    public function store_delete($keyName=null, $id)// Type: {creditNote}{invoice}{quotation}
    {
        // only record history if an delete actually occurs.
        if($keyName==null)
            $keyName = $this->_tbl_key;
        
        $sql = db_loadList("SELECT * FROM $this->_tbl  WHERE $keyName = $id");
        
        $content = serialize($sql);
        $store_type = 'delete';
        addHistory($this->_tbl, $id, $store_type, 
                   ($this->_tbl . '_' . $store_type . '(' . $id . ')'),0, $content);
        
        //DELETE SQL
        $ret = db_delete($this->_tbl, $keyName, $id);
        return $ret;
    }
}

?>

