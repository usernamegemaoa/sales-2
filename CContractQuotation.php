<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
class CContractQuotation extends CDpObject {
	var $quotation_contract_id;
        var $contract_id;
	var $quotation_id;
	
	function CContractQuotation() {
		$this->CDpObject( 'sales_quotation_contract', 'quotation_contract_id' );
	}
        
        function addContractQuotation($obj)
        {
            $CQobj = new CContractQuotation();
            $CQobj->bind($obj);
            $CQobj->store();
            return $CQobj->quotation_contract_id;
        }
        
        function deleteContractQuotation($quotation_id)
        {
            $sql = "delete from sales_quotation_contract where quotation_id = ".intval($quotation_id);

            if(db_exec($sql))
                return true;
            return false;
        }
        
        function getContractQuotaiton($quotation_id)
        {
            $q = new DBQuery();
            $q->addTable('sales_quotation_contract');
            $q->addQuery('*');
            $q->addWhere('quotation_id='.intval($quotation_id));
            $q->addWhere('quotation_id <> 0');
            return $q->loadList();
        }
}


