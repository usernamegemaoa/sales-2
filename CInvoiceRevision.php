<?php

if (!defined('DP_BASE_DIR')){
		die('You should not access this file directly.');
}

class CInvoiceRevision extends CDpObject {

    public $invoice_revision_id;
    public $user_id;
    public $invoice_id;
    public $invoice_revision;
    public $invoice_revision_tax;
    public $invoice_revision_currency;
    public $invoice_revision_notes;
    public $invoice_revision_term_condition;
    public $invoice_revision_tax_edit;
    public $invoice_revision_discount;
    public $reference_no;
    public function CInvoiceRevision() {
                $this-> CDpObject("sales_invoice_revision", "invoice_revision_id");
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
    
    public function store_delete($keyName=null, $id)
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
