<?php

if (!defined('DP_BASE_DIR')){
		die('You should not access this file directly.');
}

class CInvoice extends CDpObject {

    public $invoice_id;
    public $user_id;
    public $quotation_id;
    public $supplier_id;
    public $customer_id;
    public $address_id;
    public $attention_id;
    public $invoice_date;
    public $invoice_no;
    public $invoice_sale_person;
    public $invoice_sale_person_email;
    public $invoice_sale_person_phone;
    public $invoice_status;
    public $invoice_internal_notes;
    public $invoice_template;
    public $contact_coordinator_id;
    public $po_number;
    public $our_delivery_order_no;
    public $job_location_id;
    public $term;
    public $invoice_subject;
    public $contact_id;
    public $invoice_CO;
    public $sub_heading;
    public $invoice_your_ref;
    public $department_id;
    
    public function CInvoice() {
            $this-> CDpObject("sales_invoice", "invoice_id");
    }
    
	/**
	 *	Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 *	Can be overloaded/supplemented by the child class
	 *	@return null|string null if successful otherwise returns and error message
	 */
    
    function store($updateNulls = false, $cron=false) {
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
