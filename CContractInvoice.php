<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
class CContractInvoice extends CDpObject {
	var $invoice_contract_id;
        var $contract_id;
	var $invoice_id;
	
	function CContractInvoice() {
		$this->CDpObject( 'sales_invoice_contract', 'invoice_contract_id' );
	}
        
        function addCContractInvoice($obj)
        {
            $CIobj = new CContractInvoice();
            $CIobj->bind($obj);
            $CIobj->store();
            return $CIobj->invoice_contract_id;
        }
        
        function deleteContractInvoice($invoice_id)
        {
            $sql = "delete from sales_invoice_contract where invoice_id = ".intval($invoice_id);

            if(db_exec($sql))
                return true;
            return false;
        }
        
        function getContractInvoice($invoice_id)
        {
            $q = new DBQuery();
            $q->addTable('sales_invoice_contract');
            $q->addQuery('*');
            $q->addWhere('invoice_id='.intval($invoice_id));
            return $q->loadList();
        }
        
        function getInvoiceContract($contract_no)
        {
            $q = new DBQuery();
            $q->addTable('sales_invoice_contract','si');
            $q->addJoin('service_engagements', 'se', 'se.engagement_id=si.contract_id');
            $q->addQuery('si.*,se.engagement_code');
            $q->addWhere('engagement_code LIKE "%'.$contract_no.'%"');
            $q->addGroup('invoice_id');
            return $q->loadList();
        }
}


