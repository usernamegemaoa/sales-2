<?php
if (!defined('DP_BASE_DIR')){
		die('You should not access this file directly.');
}

class CSalesAttention extends CDpObject {
    public $sales_attention_id;
    public $sales_type_id;
    public $attention_id;
    public $sales_type_name;
    public function CSalesAttention() {
            $this-> CDpObject("sales_attention", "sales_attention_id");
    }

    public function store_delete($keyName=null, $id, $type)// Type: {creditNote}{invoice}{quotation}
    {
        // only record history if an delete actually occurs.
        if($keyName==null)
            $keyName = $this->_tbl_key;
        
        $sql = db_loadList("SELECT * FROM $this->_tbl  WHERE $keyName = $id AND sales_type_name = '$type'");
        
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
