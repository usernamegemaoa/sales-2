<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require 'CMemoBillingAdress.php';

class CMemoManager {
   function add_memo_billing_address($CMemoBillingAdressObj){
//        $CMemoBillingAdress = new CMemoBillingAdress();
//        $CMemoBillingAdress->bind($CMemoBillingAdressObj);
//        $CMemoBillingAdress->store();
//        return $CMemoBillingAdress->memo_id;
        $CMemoBillingAdress = new CMemoBillingAdress();
        $CMemoBillingAdress->bind($CMemoBillingAdressObj);
        $CMemoBillingAdress->store();
        return $CMemoBillingAdress->memo_id;
    }
    function update_memo_billing_address($memo_id,$customer_id,$address_id,$attention_id){
        $CMemoBillingAdress = new CMemoBillingAdress();
        $CMemoBillingAdress->load($memo_id);
        $CMemoBillingAdress->customer_id=$customer_id;
        $CMemoBillingAdress->address_id = $address_id;
        $CMemoBillingAdress->attention_id = $attention_id;
        return $CMemoBillingAdress->store();
    }
    function get_memo_billing_address($customer_id=false){
        $q = new DBQuery();
        $q->addTable('sales_memo_billingadress');
        $q->addQuery('*');
        if($customer_id)
            $q->addWhere('customer_id = '.intval ($customer_id));
        return $q->loadList();

    }
}
?>
