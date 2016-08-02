<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once 'CQuotation.php';
require_once 'CQuotationItem.php';
require_once 'CQuotationRevision.php';
require_once 'CQuotationInvoiceHistory.php';
require_once 'CQuotationAttention.php';
//require_once 'CSalesAttention.php';
/**
 * XXX detailed description
 *
 * @author    XXX
 * @version   XXX
 * @copyright XXX
 */
class CQuotationManager {
    // Attributes
    // Associations
    // Operations
    /**
     * XXX
     * 
     * @param  int $user_id XXX
     * @param  int $customer_id XXX
     * @param  int $status_id XXX
     * @return array XXX
     * @access public
     */

    public function list_quotation($customer_id=false, $status_id=false, $dept_id=false, $quotation_no=false) {
        global $AppUI;
        $q = new DBQuery();
            $q->addTable('sales_quotation', 'tbl1');
            $q->addQuery('tbl1.*, tbl3.company_name');
            //$q->addQuery('tbl1.*, tbl3.company_name, tbl2.quotation_revision, tbl2.quotation_revision_id');
            //$q->addJoin('sales_quotation_revision', 'tbl2', "tbl2.quotation_id = tbl1.quotation_id");
            $q->addOrder('tbl1.quotation_date DESC, quotation_id DESC');
            $q->addJoin('clients', 'tbl3', "tbl3.company_id = tbl1.customer_id");
            if($customer_id != '' && $customer_id != null)
                $q->addWhere('tbl1.customer_id = '.intval($customer_id));
            else if($status_id != 5 && ($status_id != NULL || $status_id ==""))
                $q->addWhere ('tbl1.quotation_status = '.intval($status_id));
            if($dept_id)
                $q->addWhere('tbl1.department_id='.intval($dept_id));
            if($quotation_no)
                $q->addWhere('tbl1.quotation_no LIKE "%'.$quotation_no.'%"');
            if(dPisListOwnSalesPerson()){
                $q->addWhere('tbl1.user_id = '.  intval($AppUI->user_id));
            }
            $q->order_by = 'tbl1.quotation_no DESC';
            return $q->loadList();
        
    }
    
    function get_latest_quotation_revision($quotation_id) {
        $q = new DBQuery();
        $q->addTable('sales_quotation_revision');
        $q->addQuery('*');
        $q->addWhere('quotation_id = '.intval($quotation_id) . ' ORDER BY quotation_revision_id DESC  LIMIT 0, 1');
        return $q->loadList();
    }

    /**
     * XXX
     * 
     * @param  Cquotation $quotation XXX
     * @return boolean XXX
     * @access public
     */
    public function add_quotation($quotationObj) {
        $CQuotation = new CQuotation();
        $CQuotation->bind($quotationObj);
        $CQuotation->store();
        return $CQuotation->quotation_id;
    }

    /**
     * XXX
     * 
     * @param  CquotationItem $quotation_itemObj XXX
     * @return boolean XXX
     * @access public
     */
    public function add_quotation_item($quotation_itemObj) {
        $CQuotationItem = new CQuotationItem();        
        $CQuotationItem->bind($quotation_itemObj);
        $CQuotationItem->store();
        return $CQuotationItem->quotation_item_id;        
    }
    public function update_quotation_item($id, $quo_id, $quo_rev_id, $quotation_item, $quotation_price, $quotation_qty, $quo_dis, $quo_order){
        $CQuotationItem = new CQuotationItem();
        $CQuotationItem ->load($id);
        $CQuotationItem -> quotation_item_discount = $quo_dis;
        $CQuotationItem -> quotation_revision_id = $quo_rev_id;
        $CQuotationItem ->quotation_id = $quo_id;
        $CQuotationItem ->quotation_item = $quotation_item;
        $CQuotationItem ->quotation_item_quantity = $quotation_qty;
        $CQuotationItem ->quotation_item_price = $quotation_price;
        $CQuotationItem ->quotation_item_discount = $quo_dis;
        $CQuotationItem ->quotation_order = $quo_order;
        $CQuotationItem->store();
        return $CQuotationItem->quotation_item_id;
        
    }

    /**
     * XXX
     * 
     * @param  int $quotation_id XXX
     * @param  string $field XXX
     * @param  string $value XXX
     * @return boolean XXX
     * @access public
     */
    public function update_quotation($id, $no, $date, $address, $persion, $email, $phone, $status, $seviceCoordinter, $service_order, $joblocation, $sub_heading, $department_id) {
        $Quotation = new CQuotation();
        $Quotation->load($id);
        $Quotation->quotation_no = $no;
        $Quotation->quotation_date = $date;
        $Quotation->address_id = $address;
        $Quotation->quotation_sale_person = $persion;
        $Quotation->quotation_sale_person_email = $email;
        $Quotation->quotation_sale_person_phone = $phone;
        $Quotation->quotation_status = $status;
        $Quotation->contact_coordinator_id = $seviceCoordinter;
        $Quotation->service_order = $service_order;
        $Quotation->job_location_id = $joblocation;
        $Quotation->sub_heading = $sub_heading;
        $Quotation->department_id = $department_id;
        $Quotation->store();
        return $Quotation->quotation_id;
    }
    function update_quotation_no_revision($id, $no, $rev_id, $note, $tax, $term, $tax_edit,$date,$discount,$reference_no) {
        $Revison = new CQuotationRevision();
        $Revison->load($rev_id);
        $Revison->quotation_id = $id;
        $Revison->quotation_revision = $no;
        $Revison->quotation_revision_notes = $note;
        $Revison->quotation_revision_tax = $tax;
        $Revison->quotation_revision_term_condition_contents = $term;
        $Revison->quotation_revision_tax_edit = $tax_edit;
        $Revison->quotation_revision_date = $date;
        $Revison->quotation_revision_discount = $discount;
        $Revison->reference_no=$reference_no;
        $Revison->store();
        return $Revison->quotation_revision_id;
    }
    /**
     * XXX
     * 
     * @param  int $quotation_revision_id XXX
     * @return array XXX
     * @access public
     */
     public function update_quotation_revision($quotation_revision_id, $quotation_item_deleted_arr = array(), $quotation_item_edited_id_arr = array()) {
        global $AppUI;
        if ($_POST['status_rev'] == 'update') {
            $quotation_id = $_POST['quotation_id'];
            $quotaiton_revision_arr = $this->get_latest_quotation_revision($quotation_id);
            $quotation_revision = $quotaiton_revision_arr[0]['quotation_revision'];
            if(isset($_POST['quotation_no'])) {
//                $quotation_revision_arr = $this->get_db_quotation_revsion($quotation_revision_id);
//                foreach ($quotation_revision_arr as $quotation_revision_arr1) {
                    $_POST['quotation_revision'] = CSalesManager::create_quotation_revision($quotation_revision, null, $_POST['quotation_no']);
//                }
                
            } else {
                $_POST['quotation_revision'] = CSalesManager::create_quotation_revision($quotation_revision, null);
            }
            $_POST['quotation_revision_id'] = 0; // gan quotation_revision_id = 0 de thuc hien add
            $_POST['quotation_revision_date'] = date('Y-m-d');
            $quotation_revision_id_new = $this->add_quotation_revision($_POST); // add sang quotation_revision moi
        } else {
            $quotation_revision_arr = $this->get_db_quotation_revsion($quotation_revision_id); // lay ra toan bo ban ghi cua revision cu
            $quotation_id = $quotation_revision_arr[0]['quotation_id'];
            $quotation_revision = $quotation_revision_arr[0]['quotation_revision'];
            $quotation_revision_arr[0]['quotation_revision'] = CSalesManager::create_quotation_revision($quotation_revision,null,$_POST['quotation_no']);
            //$quotation_revision_arr[0]['quotation_revision'] = $this->create_quotation_revision($quotation_id, $quotation_revision); // thay doi quotation_revision nay
            $quotation_revision_arr[0]['quotation_revision_id'] = 0; // thay doi quotation_revision nay
            $quotation_revision_arr[0]['quotation_revision_date'] = date('Y-m-d');
            $quotation_revision_id_new = $this->add_quotation_revision($quotation_revision_arr[0]); // add sang quotation_revision moi
        }
            //print_r($quotation_revision_id_new);
            //print_rD($quotation_revision_arr);
        if ($quotation_item_deleted_arr) { // neu truong hop xoa item kem theo cac ban ghi duoc xoa.
            $quotation_revision_item_arr = $this->get_db_quotation_item($quotation_id, $quotation_revision_id); // lay ra toan bo ban ghi cua revision cu
            if (count($quotation_revision_item_arr) > 0) {
                foreach ($quotation_revision_item_arr as $quotation_revision_item) {
                    if (!in_array($quotation_revision_item['quotation_item_id'], $quotation_item_deleted_arr)) { // kiem tra quotation_item_id co trong mang duoc xoa thi k copy
                        $quotation_revision_item['quotation_item_id'] = 0;
                        $quotation_revision_item['quotation_revision_id'] = $quotation_revision_id_new;
                        $this->add_quotation_item($quotation_revision_item);
                    }
                }
            }
        } else { 
           if ($quotation_item_edited_id_arr) { // Truong hop nay khi nhan save all co inline add va inline edit
                $quotation_revision_item_arr = $this->get_db_quotation_item($quotation_id, $quotation_revision_id); // lay ra toan bo ban ghi cua revision cu
                if (count($quotation_revision_item_arr) >= 0) {                    
                    foreach ($quotation_revision_item_arr as $quotation_revision_item) {
                        $in_array_edit = in_array($quotation_revision_item['quotation_item_id'], $quotation_item_edited_id_arr);
                        if ($in_array_edit == false) { // neu id duoc lap qua khong co trong mang post
                            $quotation_revision_item['quotation_item_id'] = 0;
                            $quotation_revision_item['quotation_revision_id'] = $quotation_revision_id_new;
                            $this->add_quotation_item($quotation_revision_item);
                        } else { // thuc hien add cac ban ghi duoc edit
                            $key = array_search($quotation_revision_item['quotation_item_id'], $quotation_item_edited_id_arr); // tim ra key cua mang can tim
                            $quotation_revision_item_edit = array();
                            $quotation_revision_item_edit['quotation_item_id'] = 0;
                            $quotation_revision_item_edit['quotation_revision_id'] = $quotation_revision_id_new;
                            $quotation_revision_item_edit['quotation_id'] = $_POST['quotation_id'];
                            $quotation_revision_item_edit['user_id'] = $AppUI->user_id;
                            $quotation_revision_item_edit['quotation_item'] = $_POST['quotation_item'][$key];
                            $quotation_revision_item_edit['quotation_item_price'] = $_POST['quotation_item_price'][$key];
                            $quotation_revision_item_edit['quotation_item_quantity'] = $_POST['quotation_item_quantity'][$key];
                            $quotation_revision_item_edit['quotation_item_discount'] = $_POST['quotation_item_discount'][$key];
                            $quotation_revision_item_edit['quotation_item_type'] = 0;
                            $quotation_revision_item_edit['quotation_item_notes'] = null;
                            $this->add_quotation_item($quotation_revision_item_edit);
                            
                            unset($quotation_item_edited_id_arr[$key]); // xoa di phan tu trong mang $quotation_item_edited_id_arr
                            
                        }
                    }
                    
                    for ($j = 0; $j < count($quotation_item_edited_id_arr); $j++) { // thuc hien nhiem vu add nhung ban ghi moi
                        if (intval($quotation_item_edited_id_arr[$j]) == 0) { // chi nhung ban ghi item co gia tri = 0 moi duoc add
                            $quotation_revision_item_add = array();
                            $quotation_revision_item_add['quotation_item_id'] = 0;
                            $quotation_revision_item_add['quotation_revision_id'] = $quotation_revision_id_new;
                            $quotation_revision_item_add['quotation_id'] = $_POST['quotation_id'];
                            $quotation_revision_item_add['user_id'] = $AppUI->user_id;
                            $quotation_revision_item_add['quotation_item'] = $_POST['quotation_item'][$j];
                            $quotation_revision_item_add['quotation_item_price'] = $_POST['quotation_item_price'][$j];
                            $quotation_revision_item_add['quotation_item_quantity'] = $_POST['quotation_item_quantity'][$j];
                            $quotation_revision_item_add['quotation_item_discount'] = $_POST['quotation_item_discount'][$j];
                            $quotation_revision_item_add['quotation_item_type'] = 0;
                            $quotation_revision_item_add['quotation_item_notes'] = null;
                            $this->add_quotation_item($quotation_revision_item_add);
                        }
                    }
                    
                 }
            } 
               else { // Truong hop nay khi nhan save all nhung khong co inline add
                $quotation_revision_item_arr = $this->get_db_quotation_item($quotation_id, $quotation_revision_id); // lay ra toan bo ban ghi cua revision cu
                if (count($quotation_revision_item_arr) > 0) {
                    foreach ($quotation_revision_item_arr as $quotation_revision_item) {
                        $quotation_revision_item['quotation_item_id'] = 0;
                        $quotation_revision_item['quotation_revision_id'] = $quotation_revision_id_new;
                        $this->add_quotation_item($quotation_revision_item);
                    }
                }
            }
        }

            return $quotation_revision_id_new;
            
    }

    /**
     * XXX
     * 
     * @param  CquotationRevision $quotation_revisionOBj XXX
     * @return boolean XXX
     * @access public
     */
    public function add_quotation_revision($quotation_revisionOBj) {
        $CQuotationRevision = new CQuotationRevision();
        $CQuotationRevision->bind($quotation_revisionOBj);
        $CQuotationRevision->store();
        return $CQuotationRevision->quotation_revision_id;
    }
    


    function updateQuotation_Revision($quotation_rev_id, $quotation_no, $revesion_arr) {     
         $CQuotationRevision = new CQuotationRevision();
         $CQuotationRevision->load($quotation_rev_id);
         $CQuotationRevision->quotation_revision = $quotation_no.'-'.$revesion_arr;
         $msg = $CQuotationRevision->store();
         return $msg;
                        
    }

    /**
     * XXX
     * 
     * @param  int $quotation_id XXX
     * @return array XXX
     * @access public
     */
    public function get_db_quotation($quotation_id) {

            $q = new DBQuery();
            $q->addTable('sales_quotation', 'tbl1');
            $q->addQuery('tbl1.*, tbl3.company_name');
            $q->addJoin('clients', 'tbl3', "tbl3.company_id = tbl1.customer_id");
            $q->addWhere('tbl1.quotation_id = '.$quotation_id);
            return $q->loadList();
            
    }
    
    public function get_db_quotation_already($quotation_no,$deparment_id=false) {

            $q = new DBQuery();
            $q->addTable('sales_quotation', 'tbl1');
            $q->addQuery('tbl1.*, tbl3.company_name');
            $q->addJoin('clients', 'tbl3', "tbl3.company_id = tbl1.customer_id");
            $q->addWhere('tbl1.quotation_no = "'.$quotation_no.'"');
            
            if($deparment_id)
                $q->addWhere('tbl1.department_id='.intval($deparment_id));
            
            $rows = $q->loadList();
            if(!$rows)
                return FALSE;
            else
                return true;
    }
    
    public function get_db_quotation_status($quotation_id) {

            $q = new DBQuery();
            $q->addTable('sales_quotation', 'tbl1');
            $q->addQuery('tbl1.*');
            $q->addWhere('tbl1.quotation_id = '.intval($quotation_id));
            return $q->loadList();
            
    }
    
    public function get_db_quotation_rev($quotation_id) {

            $q = new DBQuery();
            $q->addTable('sales_quotation_revision', 'tbl1');
            $q->addQuery('tbl1.quotation_revision');
            $q->addWhere('tbl1.quotation_id = '.intval($quotation_id));
            return $q->loadList();
            
    }
    

    /**
     * XXX
     * 
     * @param  int $quotation_revision_id XXX
     * @return array XXX
     * @access public
     */
    public function get_db_quotation_revsion($quotation_revision_id) {
        
            $q = new DBQuery();
            $q->addTable('sales_quotation_revision', 'tbl1');
            $q->addQuery('tbl1.*');
            $q->addWhere('quotation_revision_id = '.intval($quotation_revision_id));
            return $q->loadList();
            
    }
    
    /**
     * XXX
     *
     * @param  int $quotation_id XXX
     * @param  int $quotation_revision_id XXX
     * @return array XXX
     * @access public
     */
    public function get_db_quotation_item($quotation_id, $quotation_revision_id) {
            $q = new DBQuery();
            $q->addTable('sales_quotation_item', 'tbl1');
            $q->addQuery('tbl1.*');
            $q->addWhere('quotation_id = '.intval($quotation_id));
            $q->addWhere('quotation_revision_id = '.intval($quotation_revision_id));
            $q->addOrder('quotation_order ASC');
            return $q->loadList();
//            $CQuotationItem = new CQuotationItem();
//            return $CQuotationItem->loadAll('quotation_order ASC, quotation_item_id DESC', 'quotation_id = '. intval($quotation_id) .' AND quotation_revision_id = '. intval($quotation_revision_id));
    }

    
    public function get_invoice_id($quotation_id) {
        $q = new DBQuery();
        $q->addTable('sales_invoice', 'tbl1');
        $q->addQuery('tbl1.invoice_id', 'tbl2.quotation_id');
        $q->addJoin('sales_quotation', 'tbl2', "tbl2.quotation_id = tbl1.quotation_id");
        $q->addWhere('tbl2.quotation_id = '. $quotation_id);
        $rows = $q->loadList();
        return $rows[0]['invoice_id'];
    }
    
    function get_quotation_field_by_id($quotation_id, $field_name) {
            $q = new DBQuery();
            $q->addTable('sales_quotation');
            $q->addQuery($field_name);
            $q->addWhere('quotation_id = '.intval($quotation_id));
            $return = $q->loadList();
            return $return[0][$field_name];
    }
    
    function get_invoice_field_by_id($invoice_id, $field_name) {
        $q = new DBQuery();
        $q->addTable('sales_invoice');
        $q->addQuery($field_name);
        $q->addWhere('invoice_id = '.intval($invoice_id));
        $return = $q->loadList();
        return $return[0][$field_name];
    }
    
    function list_invoice($invoice_id) {
        require_once 'CInvoiceManager.php';
        $CInvoiceManager = new CInvoiceManager();
        return $CInvoiceManager->loadAll(null, 'invoice_id = '.$invoice_id);
    }
    
    /**
     * XXX
     * 
     * @param  int $quotation_id_arr XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_quotation($quotation_id_arr) { // format nhu sau: $quotation_id_arr = array(1, 2, 5, 6, 9);
        $CQuotation = new CQuotation();
        $CQuotationItem = new CQuotationItem();
        $CQuotationRevision = new CQuotationRevision();
        $CSalesAttention = new CSalesAttention();
        
        if (($count = count($quotation_id_arr)) > 0) {
            for ($i = 0; $i < $count; $i++) {
                $sql1 = $CQuotation->store_delete(null,$quotation_id_arr[$i]);
                $sql2 = $CQuotationItem->store_delete('quotation_id',$quotation_id_arr[$i]);
                $sql3 = $CQuotationRevision->store_delete('quotation_id',$quotation_id_arr[$i]);
                $sql4 = $CSalesAttention->store_delete('sales_type_id',$quotation_id_arr[$i],'quotation');
            }
        }
            if($sql1 && $sql2 && $sql3 && $sql4) {
                return true;
            } else
                return false;
//        if (($count = count($quotation_id_arr)) > 0) {
//            $where = 'quotation_id = '. $quotation_id_arr[0];
//            $where_attn.= 'sales_type_name = "quotation" AND  sales_type_id ='. intval($quotation_id_arr[0]);
//            for ($i = 1; $i < $count; $i++) {
//                $where .= ' OR quotation_id = '. $quotation_id_arr[$i];
//                $where_attn.= ' OR sales_type_id ='. intval($quotation_id_arr[$i]);
//            }
//        }
//        if ($where) {
//            $sql1 = "delete from sales_quotation where ".$where; // xoa bang quotation
//            $sql2 = "delete from sales_quotation_revision where ".$where; // xoa bang quotation_revision
//            $sql3 = "delete from sales_quotation_item where ".$where; // xoa bang quotation_item
//            $sql4 = "delete from sales_attention where ". $where_attn;
//            if(db_exec($sql1) && db_exec($sql2) && db_exec($sql3) && db_exec($sql4)) {
////                for ($i = 1; $i < $count; $i++) {
////                    $servoce_order=CSalesManager::get_serviceoder_id($quotation_id_arr[$i],'quotation');
////                    
////                }
//                return true;
//            } else
//                return false;
//        } else
//            return false;
    }

    /**
     * XXX
     *
     * @param  int $quotation_revision_id_arr XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_quotation_revision($quotation_revision_id_arr) { // format nhu sau: $quotation_revision_id_arr = array(1, 2, 5, 6, 9);
        $CQuotationRevision = new CQuotationRevision();
        $CQuotationItem = new CQuotationItem();
        if (($count = count($quotation_revision_id_arr)) > 0) {
            for ($i = 0; $i < $count; $i++) {
                $res1 = $CQuotationRevision->store_delete(null, $quotation_revision_id_arr[$i]);
                $res2 = $CQuotationItem->store_delete('quotation_revision_id',$quotation_revision_id_arr[$i]);
            }
        }
        if ($res1 && $res2)
                return true;
            else
                return false;
    }

    /**
     * XXX
     *
     * @param  int $quotation_item_id_arr XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_quotation_item($quotation_item_id_arr) { // format nhu sau: $quotation_item_id_arr = array(1, 2, 5, 6, 9);
        $CQuotationItem = new CQuotationItem();
        if (($count = count($quotation_item_id_arr)) > 0) {
            for ($i = 0; $i < $count; $i++) {
                $ret = $CQuotationItem->store_delete(null, $quotation_item_id_arr[$i]);
            }
        }
        
        //update gst
        $Revison = new CQuotationRevision();
        $Revison->load($_POST['quotation_revision_id']);
        $Revison->quotation_revision_tax_edit=0;
        $Revison->store();
        if($ret)
            return true;
        return false;

    }

    /**
     * XXX
     * 
     * @param  array $quotation_id_array XXX
     * @param  array $quotation_revision_id_array XXX
     * @return boolean XXX
     * @access public
     */
//    public function send_mail_quotation_revision($quotation_id_arr, $quotation_revision_id_arr = null, $content, $sender, $subject, $reciver) { // format nhu sau: $quotation_id_array = array(1, 2, 5, 6, 9); $quotation_revision_id_array tuong tu
//        
//        global $AppUI;
//        require_once($AppUI->getSystemClass( 'libmail' ));
//        
//        if (($count = count($quotation_id_arr)) > 0) {
//            for ($i = 0; $i < $count; $i++) {
//
//                $quotation_id = $quotation_id_arr[$i];
//                if ($quotation_revision_id_arr != null)
//                   $quotation_revision_id = $quotation_revision_id_arr[$i];
//                else
//                    $quotation_revision_id = $this->get_quotation_revision_lastest($quotation_id_arr);
//                
//        $mail = new Mail; // create the mail
//        //$content = 'chua config noi dung';
//        //$from = CSalesManager::get_customer_email_quo($quotation_id_arr);
//        $mail->From($sender);                
//        $mail->To($reciver);
//        $mail->Subject($subject);
//        $mail->Body($content);
//        $mail->Attach($this->create_quotation_revision_pdf_file($quotation_id_arr, $quotation_revision_id_arr, true), 'application/pdf', 'inline', 'quotation.pdf');
//        $mail->Send();
//            }
//        }
//        return true;
//        //echo $mail->Get(); // show the mail source
//    }
    
    public function send_mail_quotation_revision($quotation_id_arr, $quotation_revision_id_arr = null, $content, $sender, $subject, $reciver) { // format nhu sau: $quotation_id_array = array(1, 2, 5, 6, 9); $quotation_revision_id_array tuong tu
        
        global $AppUI;
        require_once './lib/swift/swift_required.php';
        
        if (($count = count($quotation_id_arr)) > 0) {
            for ($i = 0; $i < $count; $i++) {

                $quotation_id = $quotation_id_arr[$i];
                if ($quotation_revision_id_arr != null)
                   $quotation_revision_id = $quotation_revision_id_arr[$i];
                else
                    $quotation_revision_id = $this->get_quotation_revision_lastest($quotation_id_arr);
                
                    
                    //$transport = Swift_SmtpTransport::newInstance('localhost', 25);
                    $transport = Swift_SmtpTransport::newInstance(dPgetConfig('mailhost'), dPgetConfig('mailport'));
                    $transport->setUsername(dPgetConfig('mailusername'));
                    $transport->setPassword(dPgetConfig('mailpassword'));
        
                    // Create the Mailer using your created Transport
                    $mailer = Swift_Mailer::newInstance($transport);

                    // Create the message
                    $mail = Swift_Message::newInstance();

                    // Give the message a subject
                    $mail->setSubject($subject);

                    // Set the From address with an associative array
                    $mail->setFrom($sender);

                    // Set the To addresses with an associative array
                    $mail->setTo($reciver);

                    // Give it a body
                    $mail->setBody($content);

                    // And optionally an alternative body

                    $attachment = Swift_Attachment::newInstance($this->create_quotation_revision_pdf_file($quotation_id_arr, $quotation_revision_id_arr, true), 'quotation.pdf', 'application/pdf');
                    $mail->attach($attachment);

                    // Send the message
                    $result = $mailer->send($mail);
            }
        }
        return true;
        //echo $mail->Get(); // show the mail source
    }

    /**
     * XXX
     * 
     * @param  int $customer_id XXX
     * @return string XXX
     * @access public
     */
    //public function get_customer_email($customer_id)
     //{
       // trigger_error('Not Implemented!', E_USER_WARNING);
    //}

    /**
     * XXX
     * 
     * @param  int $quotation_id XXX
     * @param  int $quotation_revision_id XXX
     * @return boolean XXX
     * @access public
     */
    public function create_quotation_revision_pdf_file($quotation_id_arr, $quotation_revision_id_arr, $is_send_mail = false,$dir=false) {
        global $ocio_config;
        require_once (DP_BASE_DIR . '/modules/sales/CHeaderQuotation.php');
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        require_once (DP_BASE_DIR . "/modules/contacts/contacts_manager.class.php");
        require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
        require_once(DP_BASE_DIR."/modules/sales/CQuotationManager.php");
        $CQuotationManager = new CQuotationManager();
        require_once(DP_BASE_DIR."/modules/sales/CSalesManager.php");
        $CSalesManager = new CSalesManager();
        $ContactManager = new ContactManager();
        $cconfig = new CConfigNew();
        require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
        $CTemplatePDF = new CTemplatePDF();
        
        $pdf = new MYPDFQuotation(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->add; 
        $pdf->SetCreator(PDF_CREATOR);
        //$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //$pdf->SetMargins(10, 37, 8, -15);
        
        $pdf->setHeaderMargin(3);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(true);
        $pdf->setPrintFooter(true);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 9.5);
            
        
        // active default Template pdf
        $template_pdf_1 = $CSalesManager->get_template_pdf(1);
        $template_pdf_2 = $CSalesManager->get_template_pdf(2);
        $template_pdf_3 = $CSalesManager->get_template_pdf(3);
        $template_pdf_4 = $CSalesManager->get_template_pdf(4);
        $template_pdf_5 = $CSalesManager->get_template_pdf(5);
        $template_pdf_6 = $CSalesManager->get_template_pdf(6);
        // end active default Template pdf
        
        if ($quotation_revision_id_arr == null) {
            if (($count = count($quotation_id_arr)) > 0) {
                for ($i = 0; $i < $count; $i++) {
                    $quotation_id = $quotation_id_arr[$i];
                    $quotation_revision_id = $this->get_quotation_revision_lastest($quotation_id);
                    
                }
            }
        } else {
             $quotation_id = $quotation_id_arr;
             $quotation_revision_id = $quotation_revision_id_arr;   
       }
       $quotation_arr =$CQuotationManager->get_db_quotation($quotation_id);
       $quotation_arr_rev = $CQuotationManager->get_db_quotation_revsion($quotation_revision_id);
       
        $customer_id = $quotation_arr[0]['customer_id'];
        $Customer_co_id = $quotation_arr[0]['quotation_CO'];
        $date = $quotation_arr[0]['quotation_date'];
        $quotation_no = $quotation_arr[0]['quotation_no'];
        $service_order = $quotation_arr[0]['service_order'];
        $sub_heading = $quotation_arr[0]['sub_heading'];

        $sale_person_name = $quotation_arr[0]['quotation_sale_person'];
        $sale_person_email = $quotation_arr[0]['quotation_sale_person_email'];
        $sale_person_phone = $quotation_arr[0]['quotation_sale_person_phone'];
        $sale_coordinator_id = $quotation_arr[0]['contact_coordinator_id'];

        $client_id = $quotation_arr[0]['customer_id'];
        $address_id = $quotation_arr[0]['address_id'];
        $attention_id = $quotation_arr[0]['attention_id'];
        $contact_id = $quotation_arr[0]['contact_id'];
        $email_rr=CSalesManager::get_attention_email($attention_id);
        $job_location_id = $quotation_arr[0][job_location_id];
        $subject = $quotation_arr[0]['quotation_subject'];
        
        $discount = 0;
        if(count($quotation_arr_rev)>0)
        {
            $discount = $quotation_arr_rev[0]['quotation_revision_discount'];
        }
        $tax_id = $quotation_arr_rev[0]['quotation_revision_tax'];
        $tax_update = $quotation_arr_rev[0]['quotation_revision_tax_edit'];

        // Info Quotaiton
        $info2 = 'Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            : '. date($ocio_config['php_abms_date_format'],  strtotime($date)).'<br>';
        $info2 .= 'Quotation No&nbsp;&nbsp;: '. $quotation_no .'<br>';
        //$info2 .= 'Quotation Rev: '. $quotation_rev.'<br/>';
        if($service_order!="")
            $info2 .= 'Service Oder &nbsp;: '. $service_order;
        
        $info4='Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            : '. date('d-m-Y',  strtotime($date)).'<br>';
        $info4 .= 'Quotation No&nbsp;&nbsp;: '. $quotation_arr_rev[0]['quotation_revision'] .'<br>';
        //$info4 .= 'Quotation Rev: '. $quotation_rev.'<br/>';
        if($service_order!="")
            $info4 .= 'Service Oder : '. $service_order;
        
        $sub_quot_rev = substr($quotation_arr_rev[0]['quotation_revision'], -2);
        $tr_revision_no = "";
        if($sub_quot_rev!="R0")
        {
            $tr_revision_no='<tr>
                        <td width="45%" align="right">Revision No :</td>
                        <td width="2%"></td>
                        <td width="53%">'.$quotation_arr_rev[0]['quotation_revision'].'</td>
                    </tr>';
        }
        $tr_seviser_order = "";
        if($service_order)
        {
            $tr_seviser_order='Service Oder &nbsp;&nbsp;: '.$service_order.'<br>';
        }
        $info6 = 'Quotation No &nbsp;&nbsp;: '.$quotation_no.'<br>'
                .'Quotation Rev : '.$quotation_arr_rev[0]['quotation_revision'].'<br>'
                .'Date '
                . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                . ': '.date($ocio_config['php_abms_date_format'],  strtotime($date)).'<br>'
                .''.$tr_seviser_order;
        
        $quotation_rev = $quotation_arr_rev[0]['quotation_revision'];
        $sub_revision_no = substr($quotation_rev, -2);
        
        // Get LOGO sales //
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        
        $files = list_files($url);
        if(count($files)>0)
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
                $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" /></td>';
        }
        
//        $files = scandir($url);
//        $i = count($files) -1;
//        if (count($files) > 2 && $files[$i]!=".svn") {
//            $path_file = $url . $files[$i];
//            $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" /></td>';
//        } // Get LOGO sales //

        // company name && CO
        $customer_arr = $CSalesManager->get_customer_name($customer_id);
        $customer_co_arr = $CSalesManager->get_customer_name($Customer_co_id);

        // Get address
        $client_address =  CSalesManager::get_address_by_id($address_id);

            $phone = "";
            if (count($client_address) > 0) {
                if($client_address['address_phone_1']!="") 
                    $phone = 'Phone: '.$client_address['address_phone_1'].'&nbsp;&nbsp;&nbsp;&nbsp;';
                elseif($client_address['address_phone_2']!="")
                    $phone = 'Phone: '.$client_address['address_phone_2'].'&nbsp;&nbsp;&nbsp;&nbsp;';
                
                if($client_address['address_mobile_1']!="") 
                    $mobile = ' Mobile Phone: '.$client_address['address_mobile_1'];
                elseif($client_address['address_mobile_2']!="")
                    $mobile = ' Mobile Phone: '.$client_address['address_mobile_2'];
                $email_ad = "";
                if($client_address['address_email']!="")
                    $email_ad = '<br>Email: '.$client_address['address_email'];
                elseif($client_address['address_email_2']!="")
                    $email_ad = '<br>Email: '.$client_address['address_email_2'];
                
                if($client_address['address_branch_name']!="")
                    $option.=$client_address['address_branch_name'].'<br>';
                 $option.= $client_address['address_street_address_1'];
                if($client_address['address_street_address_2']!="")
                    $option.=' '.$client_address['address_street_address_2'];
        //        if($list_countries[$client_address['address_country']]!="")
        //            $option.=', '.$list_countries[$client_address['address_country']];
                $option .='<br>Singapore '.$client_address['address_postal_zip_code'];
                if($client_address['address_phone_1']!="" || $client_address['address_fax']!="" || $client_address['address_phone_2']!="")
                    $option.='<br>';
                $option.=$phone;
                if($client_address['address_fax']!="")
                    $option.='Fax: '.$client_address['address_fax'].'&nbsp;&nbsp;&nbsp;&nbsp;';
                if($template_pdf_5!=1)
                    $option.=$mobile;
                $option.=$email_ad;
        //        if($client_address['ddress_country']!="")
        //            $option.=$client_address['ddress_country'];
        //        $option = $client_address['address_phone_1'] .', '. $client_address['address_fax'] , '. $client_address['address_email'];
            }
        $address= $option;

        ######
         $quo_attention_arr = CSalesManager::get_salesAttention_by_SalesType($_REQUEST['quotation_id'], "quotation");
         $count_inv_attention = count($quo_attention_arr);
         $contac_titlet_arr = dPgetSysVal('ContactsTitle');
         $attention_option_print = "";$attention_option_print5=array();$attention_option_print6="";
         if($count_inv_attention<=0){
          $attention_arr = CSalesManager::get_list_attention($client_id);
           if (count($attention_arr) > 0) {
               //$attention_option = '';
               foreach ($attention_arr as $attention) {
                 //  $selected = '';
                   if ($attention_id == $attention['contact_id']){
                      if($attention['contact_title']!=0)
                          $contac_title = $contac_titlet_arr[$attention['contact_title']]." ";
                      $attention_option = $contac_title .$attention['contact_first_name'] .' '. $attention['contact_last_name'] ;
                   }
               }
           }
           if($attention_option!="")
           {
            $attention_option_print = $attention_option;
            $attention_option_print5[]= $attention_option;
            $attention_option_print6 .= $attention_option;
           }
            //Lay Email by Attention
           $email_rr = CSalesManager::get_attention_email($attention_id);
           $email_cus="";$email_cus6="";$phone="";
           if(count($email_rr) >0){
               
                if($email_rr['contact_phone']!="") 
                    $phone = 'Phone: '.$email_rr['contact_phone'].'&nbsp;&nbsp;';
                elseif($email_rr['contact_phone2']!="")
                    $phone = 'Phone: '.$email_rr['contact_phone2'].'&nbsp;&nbsp;';
               if($email_rr['contact_email']!="")
                   $email_cus = "Email: ". $email_rr['contact_email']. "&nbsp;&nbsp;&nbsp;";
               if($email_rr['contact_mobile']!="")
               {
                   $email_cus .= "Mobile: ". $email_rr['contact_mobile']. "&nbsp;&nbsp;&nbsp;";
                   $email_cus6 .="Mobile: ". $email_rr['contact_mobile']. "&nbsp;&nbsp;&nbsp;";
               }
              $email_cus.=$phone;
              $email_cus6.=$phone;
              if($email_rr['contact_fax']!="")
              {
                  $email_cus6 .= "Fax: ". $email_rr['contact_fax'];
              }
           }
           if($email_cus!="")
           {
              $attention_option_print.='<br>'.$email_cus;
              $attention_option_print6.='<br>'.$email_cus6;
           }
         }else{
              $attention_option_print ="";
              $i=0;
              foreach ($quo_attention_arr as $inv_attention_row) {
                  $i++;
                  $attention_id = $inv_attention_row['attention_id'];
                  $attention_arr = CSalesManager::get_list_attention($client_id);
                  if (count($attention_arr) > 0) {
                      //$attention_option = '';
                      foreach ($attention_arr as $attention) {
                        //  $selected = '';
                          if ($attention_id == $attention['contact_id']){
                              if($attention['contact_title']!=0)
                                  $contac_title = $contac_titlet_arr[$attention['contact_title']]." ";
                              $attention_option = $contac_title.$attention['contact_first_name'] .' '. $attention['contact_last_name'] ;
                          }
                      }
                  }
                  if($attention_option!="")
                  {
                    $attention_option_print.= $attention_option;
                    $attention_option_print5[]= $attention_option;
                    $attention_option_print6.= $attention_option;
                  }
                  // Lay Email by Attention
                  $email_cus ="";$email_cus6="";$phone="";
                  $email_rr = CSalesManager::get_attention_email($attention_id);
                  if(count($email_rr) >0){
                      
                    if($email_rr['contact_phone']!="") 
                        $phone = 'Phone: '.$email_rr['contact_phone'].'&nbsp;&nbsp;';
                    elseif($email_rr['contact_phone']!="")
                        $phone = 'Phone: '.$email_rr['contact_phone'].'&nbsp;&nbsp;';
                      
                      if($email_rr['contact_email']!="")
                      {
                          $email_cus = "Email: ". $email_rr['contact_email']. "&nbsp;&nbsp;&nbsp;";
                      }
                      if($email_rr['contact_mobile']!="")
                      {
                          $email_cus .= "Mobile: ". $email_rr['contact_mobile']. "&nbsp;&nbsp;&nbsp;";
                          $email_cus6 .= "Mobile: ". $email_rr['contact_mobile']."&nbsp;&nbsp;&nbsp;";
                      }
                        $email_cus.=$phone;
                        $email_cus6.=$phone;
                      if($email_rr['contact_fax']!="")
                      {
                          $email_cus6 .= "Fax: ". $email_rr['contact_fax'];
                      }
                      
                  }
                  if($email_cus!="")
                  {
                      $attention_option_print.='<br>'.$email_cus;
                      $attention_option_print6.='<br>'.$email_cus6;
                  }
                  if($i<count($quo_attention_arr))
                  {
                    $attention_option_print.='<br>';
                    $attention_option_print6.='<br>';
                  }
                      
              }
         }
         
        $title_arr = dPgetSysVal('CustomerTitle');
        $title = "";
        if($title_arr[$customer_arr[0]['company_title']]!="")
            $title = $title_arr[$customer_arr[0]['company_title']]." ";
        $customer = '<b>Bill To: '.$title. $customer_arr[0]['company_name'] .'</b>';
        
        $quotation_co_name = $customer_co_arr[0]['company_name'];
        if($quotation_co_name!="")
            $customer .='<br/><b>C/O: '.$quotation_co_name.'</b>';
        if($address!="")
            $customer .= '<br/>'. $address;

        if($attention_option_print!="")
            $customer .= '<br/>Attention: '.$attention_option_print.'';

        $customer5 = '<b>'.$title. $customer_arr[0]['company_name'] .'</b>';
        if($quotation_co_name!="")
            $customer5 .='<br/><b>C/O: '.$quotation_co_name.'</b>';
        if($address!="")
            $customer5 .= '<br/>'. $address;

        if($attention_option_print5!="")
            $customer5 .= '<br/>Attention: '.  implode('<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $attention_option_print5).'';
        
        // Customer template 6
        //$customer6 ='Bill To:<br>'.$title. $customer_arr[0]['company_name'];
        if($quotation_co_name!="")
            $co .='<br>C/O: '.$quotation_co_name;
        if($address!="")
            $address6 .= '<br/>'. $address;

        if(count($attention_option_print)>0)
            $attention6 .= $attention_option_print6;
        
        $customer6 ='<b>'.$title.$customer_arr[0]['company_name'].'</b>
                        '.$co.'
                        '.$address6.'
                    <p>Attention To: '.$attention6.'</p>';
        // END customer template 6

        // Get Joblocation
        $postal_code = "";
        $job_location_arr =  CSalesManager::get_address_by_id($job_location_id);
        if($job_location_arr>0){
            $postal_code = ', Singapore '.$client_address['address_postal_zip_code'];
        }

        // Load ra Email ca Mobile Cua Jo Location.
            $contact_arr = CSalesManager::get_attention_email($contact_id);
           //print_r($contact_arr);
           $contact = "";$phone="";
           if(count($contact_arr)>0){
                if($contact_arr['contact_phone']!="") 
                    $phone = 'Phone: '.$contact_arr['contact_phone'].'&nbsp;&nbsp;';
                elseif($contact_arr['contact_phone']!="")
                    $phone = 'Phone: '.$contact_arr['contact_phone'].'&nbsp;&nbsp;';
                
                $contact.="<br>Contact: ".$contact_arr['contact_first_name'].' '.$contact_arr['contact_last_name'];
                if($contact_arr['contact_email']!="")
                    $contact.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email: ".$contact_arr['contact_email'].'&nbsp;&nbsp;&nbsp;';
                if($contact_arr['contact_mobile']!="")
                    $contact.="Mobile: ".$contact_arr['contact_mobile'].'&nbsp;&nbsp;&nbsp;';
                $contact.=$phone;
           }
            $brand="";
            if($job_location_arr['address_branch_name']!="")
                $brand=$job_location_arr['address_branch_name'].' - ';
            if($job_location_arr['address_street_address_2']!="")
                $address_2= ', '.$job_location_arr['address_street_address_2'];
            $postal_code_job = ',Singapore ';
            if($job_location_arr['address_postal_zip_code']!="")
                $postal_code_job .=$job_location_arr['address_postal_zip_code'];
            $job_location = "";

            if($job_location_arr!=""){
                    $job_location.='<tr><td colspan="6" width="99%" style="border:1px soild #000;"><b>Job Location:</b> '.$brand.CSalesManager::htmlChars($job_location_arr['address_street_address_1'].' '.$address_2.$postal_code_job).$contact.'</td></tr>';
            }

            $subject_note = "";
            if($subject!=""){
                    $subject_note.='<tr><td colspan="6" width="99%" style="border:1px soild #000;"><b>Subject:</b> '.CSalesManager::htmlChars($subject).'</td></tr>';
            }
            if($subject!="" || $job_location_arr!=""){
                $tr_br = '<tr style="line-height: 50%;"><td></td></tr>';
            }
            
            $sales_agent = "";
            if($sale_person_name!="")
                $sales_agent.="<br>Name: ".$sale_person_name;
            if($sale_person_email!="")
                $sales_agent.="<br>Email: ".$sale_person_email;
            if($sale_person_phone!="")
                $sales_agent .="<br>Phone: ".$sale_person_phone;
            if($sale_coordinator_id!=0)
                $sales_agent .="<br>Service Coordinator: ".$CQuotationManager->get_user_change($sale_coordinator_id);;
            if($sales_agent!=""){
                    $sales_agent.='<tr><td colspan="6" width="99%" style="border:1px soild #000;"><b>Sales Agent:</b> '.$brand.CSalesManager::htmlChars($sales_agent).'</td></tr>';
            }
        
        
        
        $pdf->SetMargins(20,29, 12, -15);
        
        $CTax = new CTax();
        $tax_arr = $CTax->list_tax();
        $currency_symb = '$'; $total_paid_and_tax = 0;
        
        if(is_array($quotation_id)) {
            $quotation_id = $quotation_id[0];
        }

    $dis_label ="";
    $dis_label="";
    $a = '55%';
    $col ="2";
    $quotation_item_arr = $this->get_db_quotation_item($quotation_id, $quotation_revision_id);
    $booldis=0;
    foreach ($quotation_item_arr as $invoice_item1) {
        if($invoice_item1['quotation_item_discount']!=0){
            $booldis=1;
        }
    }

    $total_item_show = $this->get_quotation_item_total($quotation_id, $quotation_revision_id);

    $quotation_rev_details_arr = $this->get_latest_quotation_revision($quotation_id);

    //tax (Anhnn)
   $tax_option = '<option vaue="">---</option>';
    if (count($tax_arr) > 0) {
        foreach ($tax_arr as $tax) {
            $tax_name = $tax['tax_name'];
            $tax_value =0;
            $selected = '';
            if ($tax_id) { // neu tinh trang la update
                if ($tax['tax_id'] == $tax_id) {
                    $selected = 'selected="selected"';
                    $tax_value = $tax['tax_rate'];
                }
            } else { // neu tinh trang la add lay ra ta default
                if ($tax['tax_default'] == 1) {
                    $selected = 'selected="selected"';
                    $tax_value = $tax['tax_rate'];
                }
            }

            $tax_option .= '<option value="'. $tax['tax_id'] .'" '. $selected .'>'. $tax['tax_rate'] .'</option>';
        }
    }
    $total_item_show_last_discount = $total_item_show - $discount;
    $caculating_tax =0;
    if ($tax_id) {
        $caculating_tax = (floatval($total_item_show_last_discount) * floatval($tax_value) / 100);
        if($tax_update!=0)
            $caculating_tax = CSalesManager::round_up($tax_update);
        else
            $caculating_tax = CSalesManager::round_up($caculating_tax);
        $total_item_tax_show = $caculating_tax;
        $total_paid_and_tax += $caculating_tax;
        
    }
    $AmountDue=$total_item_show_last_discount + $total_item_tax_show;
    $tax_select = '<select name="quotation_revision_tax" id="quotation_revision_tax" class="text">'. $tax_option .'</select>';
    
    $percent = "";
    if($tax_value !="")
    {
            $percent = $tax_value."%";
    }
    $total_box = '
                    <table border="0" width="100%" cellspacing="0" cellpadding="3" >
                            <tr>
                                <td width="60%" align="right"><b>Total:</b></td>
                                <td width="41%" align="right"><b>$'.number_format($total_item_show,2).'</b></td>
                            </tr>
                            <tr>
                                <td width="60%" align="right"><b>Discount:</b></td>
                                <td width="41%" align="right"><b>$'.number_format($discount,2).'</b></td>
                            </tr>
                            <tr>
                                <td align="right"><b>GST&nbsp;'.$percent.':</b></td>
                                <td align="right"><b>$'.number_format($caculating_tax,2).'</b></td>
                            </tr>
                            <tr>
                                <td align="right"><b>Total Amount:</b></td>
                                <td align="right"><b>$'.number_format($AmountDue,2).'</b></td>
                            </tr>
                    </table>
        ';
    $tbl_total6 = '
                    <table border="0" width="102%" cellspacing="0" cellpadding="3" >
                            <tr>
                                <td width="50%" align="right">Total:</td>
                                <td width="52%" align="right">$'.number_format($total_item_show,2).'</td>
                            </tr>
                            <tr>
                                <td width="50%" align="right">Discount:</td>
                                <td width="52%" align="right">$'.number_format($discount,2).'</td>
                            </tr>
                            <tr>
                                <td align="right">GST&nbsp;'.$percent.':</td>
                                <td align="right">$'.number_format($caculating_tax,2).'</td>
                            </tr>
                            <tr>
                                <td align="right">Total Amount:</td>
                                <td align="right">$'.number_format($AmountDue,2).'</td>
                            </tr>
                    </table>
        ';
    
    if(isset($_GET['show']) && $_GET['show']==0){
        $total_box = "";
        $tbl_total6 ="";
    }
        $termCo=$quotation_rev_details_arr[0]['quotation_revision_term_condition_contents'];
        if($termCo!=""){
                $term='<table border="0" width="100%" cellspacing="0" cellpadding="2"><tr>
                        <td><b>Terms and Conditions:</b><br>'.CSalesManager::htmlChars($quotation_rev_details_arr[0]['quotation_revision_term_condition_contents']) .'</td>
                </tr></table>';
        }
        if($quotation_rev_details_arr[0]['quotation_revision_notes']!="")
        {
            $note_area='<br><br><b>Notes:</b> <br>'.CSalesManager::htmlChars($quotation_rev_details_arr[0]['quotation_revision_notes']);
        }
        
        if($quotation_rev_details_arr[0]['quotation_revision_notes']!="")
        {
            $note6="<br><div>Notes: <br>".CSalesManager::htmlChars($quotation_rev_details_arr[0]['quotation_revision_notes']) ."</div>";
//            $term_area_and_note6 = '<br><br><table width="100%" border="0" cellspacing="0" cellpadding="4"><tr>
//                                           '.$note6.' 
//                                    </tr></table>';
        }
        
        
        $tbl_sub_heading = "";$sub_heading6="";
        if($sub_heading!="")
        {
            $tbl_sub_heading ='<tr><td colspan="6">'.$sub_heading.'</td></tr>';
            $sub_heading6.='<tr><td colspan="6">'.$sub_heading.'</td></tr>';
        }
        
// VIEW TEMPLATE
        if($template_pdf_1==1)
        {
            $pdf->SetMargins(20,40, 12, -15);
            $tbl_1 = '<table border="0" width="100%" cellpadding="5">'
                        . '<thead>'
                            .'<tr>
                                <td colspan="5" style="text-align:center;font-weight:bold;font-size:1.5em;">
                                     QUOTATION
                                </td>
                            </tr>'
                            . '<tr>'
                                . '<td width="67%">'.$customer.'</td>'
                                . '<td width="33%">'.$info2.'</td>'
                            . '</tr>'
                            .$job_location.$subject_note.$tr_br
                            .'<tr>'
                                    .'<td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
                                    <td style="border:1px soild #000;" width="'.$a.'">Description</td>
                                    <td style="border:1px soild #000;" align="center" width="6%">Qty</td>
                                    <td style="border:1px soild #000;" align="right" width="16%">Unit Price</td>
                                    '.$dis_label.'
                                    <td style="border:1px soild #000;" align="right" width="16%">Amount</td>'
                            . '</tr>'
                        . '</thead>'
                        . '<tbody>';
                            $tbl_1.=$tbl_sub_heading;
                            if (isset($quotation_item_arr) && count($quotation_item_arr) > 0) {
                                     $tr_item = ''; $i = 1; $border = "";
                                     foreach ($quotation_item_arr as $quotation_item) {

                                         if ($quotation_item['quotation_item_type'] == 0) {
                                             $item = $quotation_item['quotation_item'];
                                         } elseif ($quotation_item['quotation_item_type'] == 1) {
                                             $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                                         } elseif ($quotation_item['quotation_item_type'] == 2) {
                                             $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                                         } else {
                                             $item = '<font color="red">Item type not found.</font>';
                                         }

                                         if($booldis==1){
                                             $dis_label='<td  align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                                             $dis_value='<td  align="center" width="11%" align="right">'. $quotation_item['quotation_item_discount'].'%' .'</td>';
                                             $a = '44%';
                                             $col ="3";
                                         }
                                         
                                         $tbl_1.= '<tr>
                                                             <td  align="center" width="6%">'. $i .'</td>
                                                             <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                                             <td  align="center"  width="6%">'. round($quotation_item['quotation_item_quantity']) .'</td>
                                                             <td  align="center" align="right" width="16%">$'. number_format($quotation_item['quotation_item_price'],2) .'</td>
                                                             '.$dis_value.'
                                                             <td  align="center" align="right" width="16%">$'. number_format(CSalesManager::calculate_total_item($quotation_item['quotation_item_price'], $quotation_item['quotation_item_quantity'], $quotation_item['quotation_item_discount']),2) .'</td>
                                                    </tr>';
                                        $i++;
                                     }
                             }
                $tbl_1 .= '</table>';
                if($pdf->last_page_flag)
                {
                    $h = 45;
                  $pdf->SetAutoPageBreak(true, 45);
                }
                else
                {
                    $h = 35;
                    $pdf->SetAutoPageBreak(true, 48);
                }
                $pdf->AddPage('P', 'A4');
                $pdf->writeHTML($tbl_1);
                
//                $hight = $pdf->GetY();
                // Hien note va box Total.
                $tbl_2.='<table><tr>
                                <td colspan="'.$col.'" width="60%">'.$note_area.'</td>
                                <td colspan="3" width="39%">'.$total_box.'</td>
                         </tr></table>';
                
                $tbl_3.='<table><tr>
                            <td colspan="5"><b>Terms and Conditions:</b><br>'.CSalesManager::htmlChars($quotation_rev_details_arr[0]['quotation_revision_term_condition_contents']) .'</td>
                    </tr></table>';
            
//            $tbl_your = "";
//            if($template_pdf_1_array[0]['template_default']==1 && $template_pdf_1_array[0]['footer_text_invoice']!="" && $template_pdf_1_array[0]['template_server']==4)
//            {
//                $tbl_your ='<tr>
//                        <td>Yours Sincerely,<br>&nbsp;&nbsp;&nbsp;&nbsp;Chris Goh</td>
//                    </tr>';
//
//            }
//            else
//            {
//                $tbl_your = '<tr>
//                        <td>Yours Sincerely</td>
//                    </tr>';
//            }
//            
//            
//            $htbl_1 = $pdf->getStringHeight(80, $tbl_1);
//            $htbl_2 = $pdf->getStringHeight(200, $tbl_2);
//            $htbl_3 = $pdf->getStringHeight(80, $tbl_3);
//            $hight = $pdf->GetY();
//            $conlai = $pdf->getPageHeight()+26-$hight-$htbl_2;
            if($conlai<0)
            {
                $pdf->addPage();
                $pdf->Ln(4);
                $pdf->writeHTML($tbl_2);
            }
            else
            {
                $pdf->writeHTML($tbl_2);
            }
//            $hight = $pdf->GetY();
//            $conlai1 = $pdf->getPageHeight()-$h-$hight-$htbl_3-10;
//            //$pdf->writeHTML($conlai1.'-'.$pdf->getPageHeight().'-'.$hight.'-'.$htbl_3.'<br>');
//            if($conlai1<0)
//            {
//                $pdf->addPage();
//                $pdf->Ln(4);
                $pdf->writeHTML($tbl_3);
//            }
//            else
//            {
//                $pdf->writeHTML($tbl_3);
//            }
                

            
//-----------------------------------------------------------------------------------
            
//            
//            $tbl_header.='<table width="100%" border="0" cellpadding="5">
//                        <tr >
//                            <td colspan="5" align="right" style="font-size:1.2em;"><b>Revision No: '.$quotation_rev.'</b><hr></td>
//                        </tr>
//                        <tr>
//                            <td colspan="5" style="text-align:center;font-weight:bold;font-size:1.5em;">
//                                 QUOTATION
//                            </td>
//                        </tr>
//                        <tr>
//                            <td colspan="3">'.$customer.'</td>
//                            <td colspan="3">'.$info2.'</td>
//                        </tr>
//                        '.$job_location.'
//                        '.$subject_note.'
//                        '.$tr_br.'
//                   </table>';
//            $tbl_header_item = '<table width="100%" border="0" cellpadding="5">
//                        <tr style="font-weight:bold;">
//                            <td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
//                            <td style="border:1px soild #000;" width="'.$a.'">Description</td>
//                            <td style="border:1px soild #000;" align="center" width="6%">Qty</td>
//                            <td style="border:1px soild #000;" align="right" width="16%">Unit Price</td>
//                            '.$dis_label.'
//                            <td style="border:1px soild #000;" align="right" width="16%">Amount</td>
//                        </tr>
//                        </table>
//                ';
//            
//            $pdf->SetAutoPageBreak(true, 20);
//            $pdf->AddPage('P', 'A4');
//            $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item.$tbl_sub_heading);
//            $pdf->ln();
//            if (isset($quotation_item_arr) && count($quotation_item_arr) > 0) {
//                     $tr_item = ''; $i = 1; $border = "";
//                     foreach ($quotation_item_arr as $quotation_item) {
//
//                         if ($quotation_item['quotation_item_type'] == 0) {
//                             $item = $quotation_item['quotation_item'];
//                         } elseif ($quotation_item['quotation_item_type'] == 1) {
//                             $item = 'Ipad<br><font color="#AAAAAA">description</font>';
//                         } elseif ($quotation_item['quotation_item_type'] == 2) {
//                             $item = 'Ipad<br><font color="#AAAAAA">description</font>';
//                         } else {
//                             $item = '<font color="red">Item type not found.</font>';
//                         }
//
//                         if($booldis==1){
//                             $dis_label='<td  align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
//                             $dis_value='<td  align="center" width="11%" align="right">'. $quotation_item['quotation_item_discount'].'%' .'</td>';
//                             $a = '44%';
//                             $col ="3";
//                         }
//                         $tr_item = '<table width="100%" border="0" cellpadding="5">
//                                        <tr>
//                                             <td  align="center" width="6%">'. $i .'</td>
//                                             <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
//                                             <td  align="center"  width="6%">'. $quotation_item['quotation_item_quantity'] .'</td>
//                                             <td  align="center" align="right" width="16%">$'. number_format($quotation_item['quotation_item_price'],2) .'</td>
//                                             '.$dis_value.'
//                                             <td  align="center" align="right" width="16%">$'. number_format(CSalesManager::calculate_total_item($quotation_item['quotation_item_price'], $quotation_item['quotation_item_quantity'], $quotation_item['quotation_item_discount']),2) .'</td>
//                                         </tr></table>';
//                        $ctr = $pdf->getStringHeight(0, $tr_item);
//                        if($pdf->GetY()>$pdf->getPageHeight()-55){
//                                  $pdf->AddPage();
//                                  $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item);
//                                  $pdf->ln();
//                              }
//                         $i++;
//                         $pdf->writeHTMLCell('','','','',$tr_item);
//                         $pdf->ln();
//                     }
//             }
//             
//            // Hien thi box total
//            $tbl_footer='<table width="98%" border="0" cellpadding="2">';
//                $tbl_footer.='<tr>
//                        <td colspan="'.$col.'" width="60%">'.$note_area.'</td>
//                        <td colspan="3" width="40%">'.$total_box.'</td>
//                    </tr>';
//            $tbl_footer.='</table>';
//            if($total_box=="")
//                    $hf = 30;
//            else
//                    $hf = 62;
//            if($pdf->GetY()>$pdf->getPageHeight()-$hf){
//                      $pdf->AddPage();
//                      $pdf->writeHTMLCell('','','','',$tbl_header);
//                      $pdf->ln();
//                  }
//            $pdf->writeHTMLCell('','','','',$tbl_footer);
//            $pdf->ln();
//            // Hien thi term and codition
//            $hterm ='';
//            $heiTerm = $pdf->getStringHeight('', $term);
//            if($pdf->getPageHeight()- $pdf->GetY()-60 < $heiTerm){
//                  $pdf->AddPage();
//                  $pdf->writeHTMLCell('','','','',$tbl_header);
//                  $pdf->ln();
//                  $hterm = 130; 
//            }
//            $pdf->writeHTMLCell('','','',$hterm,$term);
//            $pdf->ln();
//            
//            $template_pdf_1_array = $CTemplatePDF->get_template_pdf(1);
//            
//            $your = '<table border="0">
//                <tr>
//                    <td>Yours Sincerely</td>
//                </tr></table>';
//            if($template_pdf_1_array[0]['template_default']==1 && $template_pdf_1_array[0]['footer_text_invoice']!="")
//            {
//                $your = '<table border="0">
//                    <tr>
//                        <td>Yours Sincerely,<br>&nbsp;&nbsp;&nbsp;&nbsp;Chris Goh</td>
//                    </tr></table>';
//
//            }
//                $pdf->writeHTMLCell('','','',260,$your);
            
        }
        else if($template_pdf_2==1)
                {
                        $tbl_header.='<table width="100%" border="0" cellpadding="5">
                                    <tr >
                                        <td colspan="5" align="right" style="font-size:1.2em;"><b>Quotation No: '.$quotation_no.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Revision No: '.$sub_revision_no.'</b><hr></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" style="text-align:center;font-weight:bold;font-size:1.5em;">
                                             QUOTATION
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">'.$customer.'</td>
                                        <td colspan="3">'.$info2.'</td>
                                    </tr>
                                    '.$job_location.'
                                    '.$subject_note.'
                                    '.$tr_br.'
                               </table>';
                        $tbl_header_item = '<table width="100%" border="0" cellpadding="5">
                                    <tr style="font-weight:bold;">
                                        <td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
                                        <td style="border:1px soild #000;" width="'.$a.'">Description</td>
                                        <td style="border:1px soild #000;" align="center" width="6%">Qty</td>
                                        <td style="border:1px soild #000;" align="right" width="16%">Unit Price</td>
                                        '.$dis_label.'
                                        <td style="border:1px soild #000;" align="right" width="16%">Amount</td>
                                    </tr>
                                    </table>
                            ';
                        $pdf->SetAutoPageBreak(true, 20);
                        $pdf->AddPage('P', 'A4');
                        $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item.$tbl_sub_heading);
                        $pdf->ln();

                        if (isset($quotation_item_arr) && count($quotation_item_arr) > 0) {
                                 $tr_item = ''; $i = 1; $border = "";
                                 foreach ($quotation_item_arr as $quotation_item) {

                                     if ($quotation_item['quotation_item_type'] == 0) {
                                         $item = $quotation_item['quotation_item'];
                                     } elseif ($quotation_item['quotation_item_type'] == 1) {
                                         $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                                     } elseif ($quotation_item['quotation_item_type'] == 2) {
                                         $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                                     } else {
                                         $item = '<font color="red">Item type not found.</font>';
                                     }

                                     if($booldis==1){
                                         $dis_label='<td  align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                                         $dis_value='<td  align="center" width="11%" align="right">'. $quotation_item['quotation_item_discount'].'%' .'</td>';
                                         $a = '50%';
                                         $col ="3";
                                     }
                                     $tr_item = '<table width="100%" border="0" cellpadding="5">
                                                    <tr>
                                                         <td  align="center" width="6%">'. $i .'</td>
                                                         <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                                         <td  align="center"  width="6%">'. round($quotation_item['quotation_item_quantity']) .'</td>
                                                         <td  align="center" align="right" width="16%">$'. number_format($quotation_item['quotation_item_price'],2) .'</td>
                                                         '.$dis_value.'
                                                         <td  align="center" align="right" width="16%">$'. number_format(CSalesManager::calculate_total_item($quotation_item['quotation_item_price'], $quotation_item['quotation_item_quantity'], $quotation_item['quotation_item_discount']),2) .'</td>
                                                     </tr></table>';
                                    if($pdf->GetY()>$pdf->getPageHeight()-55){
                                              $pdf->AddPage();
                                              $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item);
                                              $pdf->ln();
                                          }
                                     $i++;
                                     $pdf->writeHTMLCell('','','','',$tr_item);
                                     $pdf->ln();
                                 }
                         }

                        // Hien thi box total
                        $tbl_footer='<table width="98%" border="0" cellpadding="2">';
                            $tbl_footer.='<tr>
                                    <td colspan="'.$col.'" width="60%">'.$note_area.'</td>
                                    <td colspan="3" width="40%">'.$total_box.'</td>
                                </tr>';
                        $tbl_footer.='</table>';
                        if($total_box=="")
                                $hf = 30;
                        else
                                $hf = 62;
                        if($pdf->GetY()>$pdf->getPageHeight()-$hf){
                                  $pdf->AddPage();
                                  $pdf->writeHTMLCell('','','','',$tbl_header);
                                  $pdf->ln();
                              }
                        $pdf->writeHTMLCell('','','','',$tbl_footer);
                        $pdf->ln();
                        // Hien thi term and codition
                        $hterm ='';
                        if($pdf->GetY()>$pdf->getPageHeight()-85){
                              $pdf->AddPage();
                              $pdf->writeHTMLCell('','','','',$tbl_header);
                              $pdf->ln();
                              $hterm = 110; 
                        }
                        $pdf->writeHTMLCell('','','',$hterm,$term);
                        $pdf->ln();
                        $your = '<table>
                            <tr>
                                <td>Yours Sincerely</td>
                            </tr></table>';
                        $pdf->writeHTMLCell('','','',262,$your);
                }
        elseif($template_pdf_3==1){
            $pdf->SetMargins(10,45, 8, -15);
                        $tbl_header.='<table width="100%" border="0" cellpadding="5">
                                    <tr>
                                        <td colspan="3" width="75%">'.$customer.'</td>
                                        <td colspan="3" width="25%">'.$img.'</td>
                                    </tr>
                                    '.$job_location.'
                                    '.$subject_note.'
                                    '.$tr_br.'
                               </table>';
                        $tbl_header_item = '<table width="100%" border="0" cellpadding="5">
                                    <tr style="font-weight:bold;">
                                        <td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
                                        <td style="border:1px soild #000;" width="'.$a.'">Description</td>
                                        <td style="border:1px soild #000;" align="center" width="6%">Qty</td>
                                        <td style="border:1px soild #000;" align="right" width="16%">Unit Price</td>
                                        '.$dis_label.'
                                        <td style="border:1px soild #000;" align="right" width="16%">Amount</td>
                                    </tr>
                                    </table>
                            ';
                        $pdf->SetAutoPageBreak(true, 20);
                        $pdf->AddPage('P', 'A4');
                        $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item.$tbl_sub_heading);
                        $pdf->ln();

                        if (isset($quotation_item_arr) && count($quotation_item_arr) > 0) {
                                 $tr_item = ''; $i = 1; $border = "";
                                 foreach ($quotation_item_arr as $quotation_item) {

                                     if ($quotation_item['quotation_item_type'] == 0) {
                                         $item = $quotation_item['quotation_item'];
                                     } elseif ($quotation_item['quotation_item_type'] == 1) {
                                         $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                                     } elseif ($quotation_item['quotation_item_type'] == 2) {
                                         $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                                     } else {
                                         $item = '<font color="red">Item type not found.</font>';
                                     }

                                     if($booldis==1){
                                         $dis_label='<td  align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                                         $dis_value='<td  align="center" width="11%" align="right">'. $quotation_item['quotation_item_discount'].'%' .'</td>';
                                         $a = '50%';
                                         $col ="3";
                                     }
                                     $tr_item = '<table width="100%" border="0" cellpadding="5">
                                                    <tr>
                                                         <td  align="center" width="6%">'. $i .'</td>
                                                         <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                                         <td  align="center"  width="6%">'. round($quotation_item['quotation_item_quantity']) .'</td>
                                                         <td  align="center" align="right" width="16%">$'. number_format($quotation_item['quotation_item_price'],2) .'</td>
                                                         '.$dis_value.'
                                                         <td  align="center" align="right" width="16%">$'. number_format(CSalesManager::calculate_total_item($quotation_item['quotation_item_price'], $quotation_item['quotation_item_quantity'], $quotation_item['quotation_item_discount']),2) .'</td>
                                                     </tr></table>';
                                    if($pdf->GetY()>$pdf->getPageHeight()-55){
                                              $pdf->AddPage();
                                              $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item);
                                              $pdf->ln();
                                          }
                                     $i++;
                                     $pdf->writeHTMLCell('','','','',$tr_item);
                                     $pdf->ln();
                                 }
                         }

                        // Hien thi box total
                        $tbl_footer='<table width="98%" border="0" cellpadding="2">';
                            $tbl_footer.='<tr>
                                    <td colspan="'.$col.'" width="60%">'.$note_area.'</td>
                                    <td colspan="3" width="40%">'.$total_box.'</td>
                                </tr>';
                        $tbl_footer.='</table>';
                        if($total_box=="")
                                $hf = 30;
                        else
                                $hf = 62;
                        if($pdf->GetY()>$pdf->getPageHeight()-$hf){
                                  $pdf->AddPage();
                                  $pdf->writeHTMLCell('','','','',$tbl_header);
                                  $pdf->ln();
                              }
                        $pdf->writeHTMLCell('','','','',$tbl_footer);
                        $pdf->ln();
                        // Hien thi term and codition
                        $hterm ='';
                        if($pdf->GetY()>$pdf->getPageHeight()-85){
                              $pdf->AddPage();
                              $pdf->writeHTMLCell('','','','',$tbl_header);
                              $pdf->ln();
                              $hterm = 110; 
                        }
                        $pdf->writeHTMLCell('','','',$hterm,$term);
                        $pdf->ln();
                        $your = '<table>
                            <tr>
                                <td>Yours Sincerely</td>
                            </tr></table>';
                        $pdf->writeHTMLCell('','','',262,$your);
            
        }
        elseif($template_pdf_4 == 1){
                    $tbl_header.='<table width="100%" border="0" cellpadding="5">
                                <tr>
                                    <td colspan="3" width="60%">&nbsp;</td>
                                    <td colspan="3" align="left"><b><span style="font-size:1.4em;text-align:left;">QUOTATION</span></b></td>
                                </tr>
                                <tr>
                                    <td colspan="3">'.$customer.'</td>
                                    <td colspan="3">'.$info4.'</td>
                                </tr>
                                '.$job_location.'
                                '.$subject_note.'
                                '.$tr_br.'
                           </table>';
                    $tbl_header_item = '<table width="100%" border="0" cellpadding="5">
                                <tr style="font-weight:bold;">
                                    <td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
                                    <td style="border:1px soild #000;" width="'.$a.'">Description</td>
                                    <td style="border:1px soild #000;" align="center" width="6%">Qty</td>
                                    <td style="border:1px soild #000;" align="right" width="16%">Unit Price</td>
                                    '.$dis_label.'
                                    <td style="border:1px soild #000;" align="right" width="16%">Amount</td>
                                </tr>
                                </table>
                        ';
                    $pdf->SetAutoPageBreak(true, 20);
                    $pdf->AddPage('P', 'A4');
                    $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item.$tbl_sub_heading);
                    $pdf->ln();

                    if (isset($quotation_item_arr) && count($quotation_item_arr) > 0) {
                             $tr_item = ''; $i = 1; $border = "";
                             foreach ($quotation_item_arr as $quotation_item) {

                                 if ($quotation_item['quotation_item_type'] == 0) {
                                     $item = $quotation_item['quotation_item'];
                                 } elseif ($quotation_item['quotation_item_type'] == 1) {
                                     $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                                 } elseif ($quotation_item['quotation_item_type'] == 2) {
                                     $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                                 } else {
                                     $item = '<font color="red">Item type not found.</font>';
                                 }

                                 if($booldis==1){
                                     $dis_label='<td  align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                                     $dis_value='<td  align="center" width="11%" align="right">'. $quotation_item['quotation_item_discount'].'%' .'</td>';
                                     $a = '50%';
                                     $col ="3";
                                 }
                                 $tr_item = '<table width="100%" border="0" cellpadding="5">
                                                <tr>
                                                     <td  align="center" width="6%">'. $i .'</td>
                                                     <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                                     <td  align="center"  width="6%">'. round($quotation_item['quotation_item_quantity']) .'</td>
                                                     <td  align="center" align="right" width="16%">$'. number_format($quotation_item['quotation_item_price'],2) .'</td>
                                                     '.$dis_value.'
                                                     <td  align="center" align="right" width="16%">$'. number_format(CSalesManager::calculate_total_item($quotation_item['quotation_item_price'], $quotation_item['quotation_item_quantity'], $quotation_item['quotation_item_discount']),2) .'</td>
                                                 </tr></table>';
                                if($pdf->GetY()>$pdf->getPageHeight()-55){
                                          $pdf->AddPage();
                                          $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item);
                                          $pdf->ln();
                                      }
                                 $i++;
                                 $pdf->writeHTMLCell('','','','',$tr_item);
                                 $pdf->ln();
                             }
                     }

                    // Hien thi box total
                    $tbl_footer='<table width="98%" border="0" cellpadding="2">';
                        $tbl_footer.='<tr>
                                <td colspan="'.$col.'" width="60%">'.$note_area.'</td>
                                <td colspan="3" width="40%">'.$total_box.'</td>
                            </tr>';
                    $tbl_footer.='</table>';
                    if($total_box=="")
                            $hf = 30;
                    else
                            $hf = 62;
                    if($pdf->GetY()>$pdf->getPageHeight()-$hf){
                              $pdf->AddPage();
                              $pdf->writeHTMLCell('','','','',$tbl_header);
                              $pdf->ln();
                          }
                    $pdf->writeHTMLCell('','','','',$tbl_footer);
                    $pdf->ln();
                    // Hien thi term and codition
                    $hterm ='';
                    if($pdf->GetY()>$pdf->getPageHeight()-85){
                          $pdf->AddPage();
                          $pdf->writeHTMLCell('','','','',$tbl_header);
                          $pdf->ln();
                          $hterm = 110; 
                    }
                    $pdf->writeHTMLCell('','','',$hterm,$term);
                    $pdf->ln();
                    $your = '<table>
                        <tr>
                            <td>Yours Sincerely</td>
                        </tr></table>';
                    $pdf->writeHTMLCell('','','',262,$your);
                }
            elseif($template_pdf_5==1){
                if($img!="")
                    $pdf->SetMargins(10,35, 8, -15);
                else
                    $pdf->SetMargins(10,30, 8, -15);
                $tbl_header.='<table width="100%" border="0" cellpadding="0">
                            <tr>
                                <td colspan="3" width="75%" >'.$customer5.'</td>
                                <td colspan="3" width="23%" align="right"><b><span style="font-size:1.4em;text-align:right;">QUOTATION</span></b></td>
                            </tr>
                            <tr><td style="line-height:60%"></td></tr>
                            </table>
                            <table  width="100%" border="0" cellpadding="5">
                            '.$job_location.'
                            '.$subject_note.'
                            '.$tr_br.'
                       </table>';
                $tbl_header_item = '<table width="100%" border="0" cellpadding="5">
                            <tr style="font-weight:bold;">
                                <td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
                                <td style="border:1px soild #000;" width="'.$a.'">Description</td>
                                <td style="border:1px soild #000;" align="center" width="6%">Qty</td>
                                <td style="border:1px soild #000;" align="right" width="16%">Unit Price</td>
                                '.$dis_label.'
                                <td style="border:1px soild #000;" align="right" width="16%">Amount</td>
                            </tr>
                            </table>
                    ';
                $pdf->SetAutoPageBreak(true, 20);
                $pdf->AddPage('P', 'A4');
                $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item.$tbl_sub_heading);
                $pdf->ln();

                if (isset($quotation_item_arr) && count($quotation_item_arr) > 0) {
                         $tr_item = ''; $i = 1; $border = "";
                         foreach ($quotation_item_arr as $quotation_item) {

                             if ($quotation_item['quotation_item_type'] == 0) {
                                 $item = $quotation_item['quotation_item'];
                             } elseif ($quotation_item['quotation_item_type'] == 1) {
                                 $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                             } elseif ($quotation_item['quotation_item_type'] == 2) {
                                 $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                             } else {
                                 $item = '<font color="red">Item type not found.</font>';
                             }

                             if($booldis==1){
                                 $dis_label='<td  align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                                 $dis_value='<td  align="center" width="11%" align="right">'. $quotation_item['quotation_item_discount'].'%' .'</td>';
                                 $a = '50%';
                                 $col ="3";
                             }
                             $tr_item = '<table width="100%" border="0" cellpadding="5">
                                            <tr>
                                                 <td  align="center" width="6%">'. $i .'</td>
                                                 <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                                 <td  align="center"  width="6%">'. round($quotation_item['quotation_item_quantity']) .'</td>
                                                 <td  align="center" align="right" width="16%">$'. number_format($quotation_item['quotation_item_price'],2) .'</td>
                                                 '.$dis_value.'
                                                 <td  align="center" align="right" width="16%">$'. number_format(CSalesManager::calculate_total_item($quotation_item['quotation_item_price'], $quotation_item['quotation_item_quantity'], $quotation_item['quotation_item_discount']),2) .'</td>
                                             </tr></table>';
                            if($pdf->GetY()>$pdf->getPageHeight()-55){
                                      $pdf->AddPage();
                                      $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item);
                                      $pdf->ln();
                            }
                             $i++;
                             $pdf->writeHTMLCell('','','','',$tr_item);
                             $pdf->ln();
                         }
                 }

                // Hien thi box total
                $tbl_footer='<table width="98%" border="0" cellpadding="2">';
                    $tbl_footer.='<tr>
                            <td colspan="'.$col.'" width="60%">'.$note_area.'</td>
                            <td colspan="3" width="40%">'.$total_box.'</td>
                        </tr>';
                $tbl_footer.='</table>';
                if($total_box=="")
                        $hf = 30;
                else
                        $hf = 62;
                if($pdf->GetY()>$pdf->getPageHeight()-$hf){
                          $pdf->AddPage();
                          $pdf->writeHTMLCell('','','','',$tbl_header);
                          $pdf->ln();
                }
                $pdf->writeHTMLCell('','','','',$tbl_footer);
                $pdf->ln();
                // Hien thi term and codition
                $hterm ='';
                if($pdf->GetY()>$pdf->getPageHeight()-85){
                      $pdf->AddPage();
                      $pdf->writeHTMLCell('','','','',$tbl_header);
                      $pdf->ln();
                      $hterm = 110; 
                }
                $pdf->writeHTMLCell('','','',$hterm,$term);
                $pdf->ln();
//                $your = '<table>
//                    <tr>
//                        <td>Yours Sincerely</td>
//                    </tr></table>';
//                $pdf->writeHTMLCell('','','',262,$your);
            }
        else if($template_pdf_6 == 1)
        {
            if($pdf->last_page_flag)
              $pdf->SetAutoPageBreak(true, 75);
            else
                $pdf->SetAutoPageBreak(true, 75);
            if($img!="")
                $pdf->SetMargins(10,45, 8, -15);
            else
                $pdf->SetMargins(5,45, 8, -15);
            $pdf->AddPage('P', 'A4');
            if($booldis==1){
                $dis_label='<td style="border:1px soild #000;" align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                $dis_value='<td  align="center" width="11%" align="right">'. $quotation_item['quotation_item_discount'].'%' .'</td>';
                $a = '42%';
                $col ="3";
            }
                $txt_body ='<table width="100%" border="0" cellpadding="2">
                                <thead>
                                    <tr >
                                        <td colspan="3" width="55%">'.$customer6.'</td>
                                        <td colspan="3" width="45%" align="left">
                                            <b><span style="font-size:1.6em;text-align:right;width:100%">QUOTATION</span></b><br><br>'.$info6.'
                                        </td>
                                    </tr>
                                    <tr><td style="line-height:50%"></td></tr>
                                    '.$job_location.'
                                    '.$subject_note.'
                                    '.$tr_br.'
                                    <tr style="font-weight:bold;">
                                        <td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
                                        <td style="border:1px soild #000;" width="'.$a.'">Description</td>
                                        <td style="border:1px soild #000;" align="right" width="7%">Qty</td>
                                        <td style="border:1px soild #000;" align="right" width="16%">Unit Price</td>
                                        '.$dis_label.'
                                        <td style="border:1px soild #000;" align="right" width="16%">Amount</td>
                                    </tr>
                                </thead>';
                $txt_body.=$sub_heading6;
                if (isset($quotation_item_arr) && count($quotation_item_arr) > 0) {
                         $tr_item = ''; $i = 1; $border = "";
                         foreach ($quotation_item_arr as $quotation_item) {

                             if ($quotation_item['quotation_item_type'] == 0) {
                                 $item = $quotation_item['quotation_item'];
                             } elseif ($quotation_item['quotation_item_type'] == 1) {
                                 $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                             } elseif ($quotation_item['quotation_item_type'] == 2) {
                                 $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                             } else {
                                 $item = '<font color="red">Item type not found.</font>';
                             }
                             if($booldis==1){
                                 $dis_label='<td style="border:1px soild #000;" align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                                 $dis_value='<td  align="center" width="11%" align="right">'. $quotation_item['quotation_item_discount'].'%' .'</td>';
                                 $a = '43%';
                                 $col ="3";
                             }
                             $txt_body .= '<tr>
                                                 <td  align="center" width="6%">'. $i .'</td>
                                                 <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                                 <td  align="right"  width="6%">'. round($quotation_item['quotation_item_quantity']) .'</td>
                                                 <td  align="right" width="16%">$'. number_format($quotation_item['quotation_item_price'],2) .'</td>
                                                 '.$dis_value.'
                                                 <td  align="center" align="right" width="16%">$'. number_format(CSalesManager::calculate_total_item($quotation_item['quotation_item_price'], $quotation_item['quotation_item_quantity'], $quotation_item['quotation_item_discount']),2) .'</td>
                                             </tr>';
                             $i++;
                         }
                         
                 }
        $txt_body.=    '</table>';
        $txt_body.=$note6;
                    $pdf->writeHTMLCell('','','','',$txt_body);
                    $pdf->ln();
                    
                    

                    // Hien thi box total
//                    $tbl_footer='<table width="99%" border="0" cellpadding="0" spacepadding="0">';
//                        $tbl_footer.='<tr>
//                                <td colspan="'.$col.'" width="72%"><b>Terms & Conditions:</b><br>'.CSalesManager::htmlChars($quotation_rev_details_arr[0]['quotation_revision_term_condition_contents']).'</td>
//                                <td colspan="3" width="28%">'.$tbl_total6.'</td>
//                            </tr>';
//                    $tbl_footer.='</table>';
//                    $pdf->writeHTMLCell('','','',230,$tbl_footer);
 
        } 
                
            ob_end_clean();
            if ($is_send_mail)
                return $pdf->Output('', 'S'); // tao ra chuoi document, ten bo qua
            elseif($dir)
                $pdf->Output($dir.'/'.$quotation_rev.'.pdf','F'); // Save file PDF vao forder
            else
                $pdf->Output($quotation_rev .'.pdf', 'I');
    }
    public function create_quotation_revision_html($quotation_id_arr, $quotation_revision_id_arr) {
        
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        require_once (DP_BASE_DIR . "/modules/contacts/contacts_manager.class.php");
        require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
        
        $ContactManager = new ContactManager();
        $cconfig = new CConfigNew;
        
        if ($quotation_revision_id_arr == null) {
            if (($count = count($quotation_id_arr)) > 0) {
                for ($i = 0; $i < $count; $i++) {
                    $quotation_id = $quotation_id_arr[$i];
                    $quotation_revision_id = $this->get_quotation_revision_lastest($quotation_id);
                }
            }
        } else {
             $quotation_id = $quotation_id_arr;
             $quotation_revision_id = $quotation_revision_id_arr;   
       }

        //print_r ($quotation_revision_id_arr);
        
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        $w = "100%";
        $files = list_files($url);
        if(count($files)>0)
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
            $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" width="200" /><br/></td>';
            $w="73%";
        }
        
//        $files = scandir($url);
//        $i = count($files)-1;
//        $w="100%";
//        if (count($files) > 2 && $files[$i]!=".svn") {
//            $path_file = $url . $files[$i];
//            $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" width="200" /><br/></td>';
//            $w="73%";
//        }
        
        $CTax = new CTax();
        $tax_arr = $CTax->list_tax();
        $currency_symb = '$'; $total_paid_and_tax = 0;
        
        if(is_array($quotation_id)) {
            $quotation_id = $quotation_id[0];
        }
//        if(is_array($quotation_revision_id)) {
//            $quotation_revision_id = $quotation_revision_id[0];
//        }
        $quotation_details_arr = $this->get_db_quotation($quotation_id);
        $quotation_details_arr1 = $this->get_latest_quotation_revision($quotation_id);

        if (count($quotation_details_arr) > 0) {
            $date = $quotation_details_arr[0]['quotation_date'];
            $quotation_no = $quotation_details_arr[0]['quotation_no'];
            $service_order = $quotation_details_arr[0]['service_order'];

            $sale_person_name = $quotation_details_arr[0]['quotation_sale_person'];
            $sale_person_email = $quotation_details_arr[0]['quotation_sale_person_email'];
            $sale_person_phone = $quotation_details_arr[0]['quotation_sale_person_phone'];
            $sale_coordinator_id = $quotation_details_arr[0]['contact_coordinator_id'];
            

            $client_id = $quotation_details_arr[0]['customer_id'];
            $address_id = $quotation_details_arr[0]['address_id'];
            $attention_id = $quotation_details_arr[0]['attention_id'];
            $contact_id = $quotation_details_arr[0]['contact_id'];
            $email_rr=CSalesManager::get_attention_email($attention_id);
            $job_location_id = $quotation_details_arr[0][job_location_id];
            $subject = $quotation_details_arr[0]['quotation_subject'];
            $quotation_co = $quotation_details_arr[0]['quotation_CO'];
        }
        $quotation_rev = $quotation_details_arr1[0]['quotation_revision'];

    $attention_arr = CSalesManager::get_list_attention($client_id);
    if (count($attention_arr) > 0) {
        //$attention_option = '';
        foreach ($attention_arr as $attention) {
          //  $selected = '';
            if ($attention_id == $attention['contact_id'])
              //  $selected = 'selected="selected"';
            $attention_option = $attention['contact_first_name'] .' '. $attention['contact_last_name'] ;
        }
        
    }
    $attention_option_print = $attention_option;
        
    $supplier_arr = CSalesManager::get_supplier_info();
    if (count($supplier_arr) > 0) {
    $sales_owner_name = $supplier_arr['sales_owner_name'];
    $sales_owner_address =$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'];
    //$sales_owner_address += 
    $phone =$supplier_arr['sales_owner_phone1'];
    $fax = $supplier_arr['sales_owner_fax'];
    $email =$supplier_arr['sales_owner_email'];
    $web = $supplier_arr['sales_owner_website'];
    $sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
    $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
    }
    
    $supplier = '<tr><td><b>'. $sales_owner_name .'</b></td></tr>';
    $supplier.= '<tr><td>'.$sales_owner_address.'</td></tr>';
    $supplier .= '<tr><td>Tel: '.$phone.', Fax: '.$fax.'</td></tr>';
//    $supplier .= '<tr><td>Fax: '.$fax.'</td></tr>';
    $supplier .= '<tr><td>GST Reg No: '. $sales_owner_gst_reg_no .'</td></tr>';
//    $supplier.= '<tr><td>Reg No: '. $supplier_arr['sales_owner_postal_code'] .'</td></tr>';
    $supplier .= '<tr><td>Email: '.CSalesManager::htmlChars($email).'</td></tr>';
    if($web!="")
        $supplier .= '<tr><td>Website: '.$web.'</td></tr>';

    $info2 = '<td width="35%">';
    $info2 .= '<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr><td width="40%">Date</td><td width="65%">: '. date('d-m-Y',  strtotime($date)).'</td></tr>';
    $info2 .= '<tr><td>Quotation No</td><td>: '. $quotation_no .'</td></tr>';
    //$info2 .= 'Quotation Rev: '. $quotation_rev.'<br/>';
    $info2 .= '<tr><td>Service Oder</td><td>: '. $service_order.'</td></tr>';
    $info2 .= '<tr><td></td></tr>';
    $info2 .= '</table>';
    $info2 .= '</td>';    
    
    $rows = CSalesManager::get_list_companies();
    if (count($rows) > 0) {
        foreach ($rows as $row) {
            if (intval($row['company_id']) == intval($client_id)) {
                $company_name = $row['company_name'];
                //$img= '<img height="100" width="150" alt="Smiley face" src="modules/sales/images/logo/1_picture030.jpg">';
                break;
            }
        }
        foreach ($rows as $row) {
            if (intval($row['company_id']) == intval($quotation_co)) {
                $quotation_co_name = $row['company_name'];
                //$img= '<img height="100" width="150" alt="Smiley face" src="modules/sales/images/logo/1_picture030.jpg">';
                break;
            }
        }
    }
    

    $client_address =  CSalesManager::get_address_by_id($address_id);
    
//    if (count($client_address) > 0) {
//        $option = $client_address['address_street_address_1'] .'<br/>';
//        $option .= $client_address['address_phone_1'] .', '. $client_address['address_fax'] .', '. $client_address['address_email'];
//    }
    $option="";
        // lay country customer
    $array_countries = $cconfig->getRecordConfigByList('countriesList');
    $list_countries = array();
    foreach ($array_countries as $array_country) {
        $list_countries[$array_country['config_id']] = $array_country['config_name'];
    }
    if (count($client_address) > 0) {
        if($client_address['address_branch_name']!="")
            $option.=$client_address['address_branch_name'].'<br>';
         $option.= $client_address['address_street_address_1'];
        if($client_address['address_street_address_2']!="")
            $option.='<br>'.$client_address['address_street_address_2'];
//        if($list_countries[$client_address['address_country']]!="")
//            $option.=', '.$list_countries[$client_address['address_country']];
        $option .='<br>Singapore '.$client_address['address_postal_zip_code'];
        if($client_address['address_phone_1']!="" || $client_address['address_fax']!="")
            $option.='<br>';
        if($client_address['address_phone_1']!="")
            $option.='Tel: '.$client_address['address_phone_1'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        if($client_address['address_fax']!="")
            $option.='Fax: '.$client_address['address_fax'];
//        if($client_address['ddress_country']!="")
//            $option.=$client_address['ddress_country'];
//        $option = $client_address['address_phone_1'] .', '. $client_address['address_fax'] , '. $client_address['address_email'];
    }
    
        $address .= $option;
    $postal_code ="";
    $job_location_arr =  CSalesManager::get_address_by_id($job_location_id);
    if($job_location_arr>0){
        $postal_code = ', Singapore '.$client_address['address_postal_zip_code'];
    }
    $img_print = '<br/>'. $img;
    
    $customer = '<b>Bill To: '. $company_name .'</b>';
    if($quotation_co_name!="")
        $customer.= '<br><b>C/O: '. $quotation_co_name .'</b>';
    if($address!="")
        $customer .= '<br/>'. $address;
    if($attention_option_print!="")
        $customer .= '<br/>Attention: '. $attention_option_print;
    
    // Lay Email by Attention
    $email_rr = CSalesManager::get_attention_email($attention_id);
    if(count($email_rr) >0){
        if($email_rr['contact_email']!="")
            $email_cus = "Email: ". $email_rr['contact_email']. "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        if($email_rr['contact_mobile']!="")
        $email_cus .= "Mobile: ". $email_rr['contact_mobile'];
    }
    
    if($email_cus!="")
        $customer .= '<br/>'.CSalesManager::htmlChars($email_cus);
//    if($job_location_arr!="")
//        $customer .= '<br/>Job Location: '.$job_location_arr['address_street_address_1'].$postal_code;
    
    //Lay contact la coordinator
    $sales_contact = "";
    $sale_Contaccoord_arr=$ContactManager->get_db_contact($sale_coordinator_id);
    if($sale_person_name!="" || $sale_person_email!="" || $sale_person_phone!="" || $sale_Contaccoord_arr[0]['contact_last_name']!="" || $sale_Contaccoord_arr[0]['contact_first_name']!="")
        $sales_contact = '<b>Sales Agent</b><br/>';
    if($sale_person_name!="")
        $sales_contact .= 'Name: '. $sale_person_name .'<br/>';
    if($sale_person_email!="")
        $sales_contact .= 'Email: '. CSalesManager::htmlChars($sale_person_email) .'<br/>';
    if($sale_person_phone!="")
        $sales_contact .= 'Phone: '. $sale_person_phone.'<br/>';
    if($sale_Contaccoord_arr[0]['contact_last_name']!="" || $sale_Contaccoord_arr[0]['contact_first_name']!="")
        $sales_contact .= 'Service Coordinator: '.$sale_Contaccoord_arr[0]['contact_last_name'] .', '.$sale_Contaccoord_arr[0]['contact_first_name'];
    
    if($sales_contact!=""){
        $sales_contact_td = '<td>'.$sales_contact.'</td>';
    }
    
    $accepted_by = '<b>Accepted by:</b><br/><br/>';
    $accepted_by .= '______________________________________ <br/><br/>';
    $accepted_by .= 'Name: <br/>';
    $accepted_by .= 'Email: <br/>';
    $accepted_by .= 'Designation:';

//    $header_text = 'Header\'s text<br/>';

    $dis_label ="";
    $dis_label="";
    $a = '58%';
    $quotation_item_arr = $this->get_db_quotation_item($quotation_id, $quotation_revision_id);
    $booldis=0;
    foreach ($quotation_item_arr as $invoice_item1) {
        if($invoice_item1['quotation_item_discount']!=0){
            $booldis=1;
        }
    }
    if (isset($quotation_item_arr) && count($quotation_item_arr) > 0) {
            $tr_item = ''; $i = 1;
            foreach ($quotation_item_arr as $quotation_item) {
                if ($quotation_item['quotation_item_type'] == 0) {
                    $item = $quotation_item['quotation_item'];
                } elseif ($quotation_item['quotation_item_type'] == 1) {
                    $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                } elseif ($quotation_item['quotation_item_type'] == 2) {
                    $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                } else {
                    $item = '<font color="red">Item type not found.</font>';
                }
                if($booldis==1){
                    $dis_label='<td width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                    $dis_value='<td align="right">'. $quotation_item['quotation_item_discount'].'%' .'</td>';
                    $a = '47%';
                }
                $tr_item .= '<tr>
                                <td align="center" height="30">'. $i .'</td>
                                <td>'. CSalesManager::htmlChars($item) .'</td>
                                <td align="center">'. $quotation_item['quotation_item_quantity'] .'</td>
                                <td align="right">'. number_format($quotation_item['quotation_item_price'],2) .'</td>
                                '.$dis_value.'
                                <td align="right">'. number_format(CSalesManager::calculate_total_item($quotation_item['quotation_item_price'], $quotation_item['quotation_item_quantity'], $quotation_item['quotation_item_discount']),2) .'</td>
                            </tr>';
                $i++;
            }
    }

    $total_item_show = $this->get_quotation_item_total($quotation_id, $quotation_revision_id);

    $quotation_rev_details_arr = $this->get_latest_quotation_revision($quotation_id);

    if (count($quotation_rev_details_arr) > 0) {
        if($quotation_rev_details_arr[0]['quotation_revision_notes']!="")
//            $note_area ='<b>Notes:</b> '.$invoice_rev_details_arr[0]['invoice_revision_notes'];
            $note_area='<tr valign="top">
                <td width="70%"><b>Notes:</b> <br>'.CSalesManager::htmlChars($quotation_rev_details_arr[0]['quotation_revision_notes']) .'</td>
                <td></td>
             </tr><tr><td height="10"></td><td></td></tr>';
        if($quotation_rev_details_arr[0]['quotation_revision_term_condition_contents']!="")
//            $term_area ='<b>Terms and Conditions:</b> '.$invoice_rev_details_arr[0]['invoice_revision_term_condition'];
            $term_area = '<tr>
                                <td width="70%" ><b>Terms and Conditions:</b><br>'.CSalesManager::htmlChars($quotation_rev_details_arr[0]['quotation_revision_term_condition_contents']) .'</td>
                                <td></td>
                            </tr>
                            <tr><td height="10"></td></tr>';
        $tax_id = $quotation_rev_details_arr[0]['quotation_revision_tax'];
    }
    
    // Load ra Email ca Mobile Cua Jo Location.
    $contact_localtion =  CSalesManager::get_address_by_id($job_location_id);
    $option_location ="";
//    print_r($client_localtion);
//    $contact_location = "";
//    if (count($contact_localtion) > 0) {
//        if($contact_localtion['address_email']!="")
//            $option_location.="Email: ".$contact_localtion['address_email']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
//        if($contact_localtion['address_mobile_1']!="")
//            $option_location.="Mobile: ".$contact_localtion['address_mobile_1'];
//        if($contact_localtion['address_email']!="" || $contact_localtion['address_mobile_1']!="")
//            $contact_location ='<br>'.$option_location;
//    }
   $contact_arr = CSalesManager::get_attention_email($contact_id);
   //print_r($contact_arr);
   $contact = "";
   if(count($contact_arr)>0){
        $contact.="<br>Contact: ".$contact_arr['contact_first_name'].' '.$contact_arr['contact_last_name'].'<br>';
        if($contact_arr['contact_email']!="")
            $contact.="Email: ".$contact_arr['contact_email'].'&nbsp;&nbsp;&nbsp;&nbsp;';
        if($contact_arr['contact_mobile']!="")
            $contact.="Mobile: ".$contact_arr['contact_mobile'];
   }
    
    $brand="";
    if($job_location_arr['address_branch_name']!="")
        $brand=$job_location_arr['address_branch_name'].' - ';
    if($job_location_arr['address_street_address_2']!="")
        $address_2= ', '.$job_location_arr['address_street_address_2'];
    $postal_code_job = ',Singapore ';
    if($job_location_arr['address_postal_zip_code']!="")
        $postal_code_job .=$job_location_arr['address_postal_zip_code'];
    $jod_location = "";
    if($job_location_arr!=""){
            $jod_location.='<tr><td colspan="2"><b>Job Location:</b> '.$brand.CSalesManager::htmlChars($job_location_arr['address_street_address_1'].$address_2.$postal_code_job).$contact.'</td></tr>';
    }
    
    $subject_note = "";
    if($subject!=""){
            $subject_note.='<tr><td colspan="2"><b>Subject:</b> '.CSalesManager::htmlChars($subject).'</td></tr>';
    }

    //tax (Anhnn)
   $tax_option = '<option vaue="">---</option>';
    if (count($tax_arr) > 0) {
        foreach ($tax_arr as $tax) {
            $tax_name = $tax['tax_name'];
            $tax_value =0;
            $selected = '';
            if ($tax_id) { // neu tinh trang la update
                if ($tax['tax_id'] == $tax_id) {
                    $selected = 'selected="selected"';
                    $tax_value = $tax['tax_rate'];
                }
            } else { // neu tinh trang la add lay ra ta default
                if ($tax['tax_default'] == 1) {
                    $selected = 'selected="selected"';
                    $tax_value = $tax['tax_rate'];
                }
            }

            $tax_option .= '<option value="'. $tax['tax_id'] .'" '. $selected .'>'. $tax['tax_rate'] .'</option>';
        }
    }
    
    if ($tax_id) {
        $caculating_tax = (floatval($total_item_show) * floatval($tax_value) / 100);
        $caculating_tax = CSalesManager::round_up($caculating_tax);
        $total_item_tax_show = $caculating_tax;
        $total_paid_and_tax += $caculating_tax;
    }
$amount_due =  ($total_item_show + $caculating_tax);
    $tax_select = '<select name="quotation_revision_tax" id="quotation_revision_tax" class="text">'. $tax_option .'</select>';

$tbl = '<div style="border: 1px solid black; width: 70%; font-size:12px;font-family:times new roman; margin:auto; padding:8px;">
            <table border="0" cellspacing="0" width="100%" cellpadding="0">
                <tr valign = "top" height="60">
                    '.$img.'
                    <td '.$w.' ><table border="0"  width="100%">'.$supplier.'</table></td>
                </tr>
                <tr valign="bottom"><td align="right" colspan="2"><b>Quotation No: '.$quotation_no.'</b></td></tr>
                <tr height="2"><td colspan="2" height="2"><hr></td></tr>
             </table>
            <table border="0" width="100%" cellpadding="0">
                
                <tr valign="top">
                    <td height="35" colspan="2"><h2 style="text-align:center;margin:0;">QUOTATION</h2></td>
                </tr>
          </table>
          <table border="0" width="100%" cellspacing="4" cellpadding="0">
                <tr valign="top">
                    <td>'.$customer.'</td>
                    '.$info2.'
                </tr>
          </table>
          
            <table border="1" cellspacing="0" width="100%" cellpadding="8">
                <tr valign="top">
                    '.$sales_contact_td.'
                </tr>
                '.$jod_location.'
                '.$subject_note.'
           </table>
           
            <table border="0" cellspacing="0" cellpadding="0">
                <tr><td>&nbsp</td></tr>
            </table>
          <table border="1" width="100%" style="border-style:solid;" class="tbl" cellspacing="0" cellpadding="2">
                                <tr>
                                    <td width="3%" align="center" height="35" ><b> #</b></td>
                                    <td width="'.$a.'" height="30"><b> Item</b></td>
                                    <td width="8%" align="center" ><b> Qty</b></td>
                                    <td width="13%" align="center" ><b> Unit Price</b></td>
                                    '.$dis_label.'
                                    <td width="15%" align="center" ><b> Amount</b></td>
                                </tr>
                                '. $tr_item .'
         </table>
         <table border="0" cellspacing="0" width="100%" cellpadding="0">
                <tr>
                    <td colspan="2">
                            <table border="0" cellspacing="0" width="100%" cellpadding="2">
                                <tr align="right">
                                    <td width="50%"></td>
                                    <td width="25%"><b>Total:</b></td>
                                    <td width="25%" align="right"><b>$'.number_format($total_item_show,2).'</b></td>
                                </tr>
                                <tr align="right">
                                    <td width="50%"></td>
                                    <td width="15%"><b>'.$tax_name.'&nbsp;&nbsp;'.$tax_value.'%:</b></td>
                                    <td width="25%" align="right"><b>$'.number_format($caculating_tax,2).'</b></td>
                                </tr>
                                <tr align="right">
                                    <td width="50%"></td>
                                    <td width="15%"><b>Total Amount:</b></td>
                                    <td width="25%" align="right"><b>$'.number_format(round($amount_due,2),2).'</b></td>
                                </tr>
                            </table>
                     </td>
                </tr>
                <tr><td style="height:2px;"></td></tr>
                <tr>
                    <td colspan="2">
                        <table width="100%"  cellspacing="0" cellpadding="0">
                            '.$note_area.'
                            '.$term_area.'
                        </table>
                    </td>
                </tr>
                <tr><td height="2"></td></tr>
                <tr>
                    <td colspan="2">
                        <table width="100%" border="0">
                            <tr>
                                <td width="30%">Yours Sincerely</td>
                                <td></td>
                                <td width="30%" align="center">Confirmation of Order</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td height="60"></td>
                                <td></td>
                            </tr>
                            <tr >
                                <td>Authorized Signature</td>
                                <td></td>
                                <td align="center">Company Stamp<br>&<br>Authorized Signature</td>
                            </tr>
                            <tr >
                                <td height="25"> </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>   
</table></div>';

//$tbl.=
//'<table style="border: 1px solid black; width: 80%; margin:auto">
//    <tr >
//        <td >
//            <table width="100%">
//                <tr align="center">
//                    <td colspan="2"><h1>QUOTATION</h1></td>
//                </tr>
//                <tr >
//                    <td width="70%" valign="top" >
//                        <table width="100%" >
//                            <tr>
//                                <td>'. $img_print .'</td>
//                                <td>'. $supplier .'</td>
//                            </tr>
//                        </table>
//                    </td>
//                    <td width="30%">
//                        <table width="100%">
//                            <tr>
//                                <td >'. $info2 .'</td>
//                            </tr>
//                        </table>
//                    </td>
//                </tr>
//                <tr>
//                    <td width="58%" style="border: 1px solid black">
//                        <table>
//                            <tr>
//                                <td>'. $customer .'</td>
//                            </tr>
//                        </table>
//                    </td>
//                    <td style="border: 1px solid black">'. $sales_contact .'</td>
//                </tr>
//                <tr>
//                    <td colspan="2">'. $header_text .'</td>
//                </tr>
//            </table>
//        </td>
//    </tr>
//
//    <tr>
//        <td>
//            <table border="1">
//                <tr>
//                    <td width="3%"><b> #</b></td>
//                    <td width="45%"><b> Item</b></td>
//                    <td width="20%"><b> Price</b></td>
//                    <td width="15%"><b> Qty</b></td>
//                    <td width="11%"><b> Discount</b></td>
//                    <td width="18%"><b> Amount</b></td>
//                </tr>
//                '. $tr_item .'
//            </table>
//         </td>
//    </tr>    
//    <tr>
//        <td>
//        </td>
//    </tr>
//    <tr>
//        <td>
//            <table>
//                <tr>
//                    <td width="85%"></td>
//                    <td>
//                        <table border="0">
//                            <tr>
//                                <td><b>Total</b></td>
//                                <td><b>$'. number_format($total_item_show,2) .'</b></td>
//                            </tr>
//                            <tr>
//                                <td><b>Tax @ '. $tax_value .'%</b></td>
//                                <td><b>$'. number_format($total_item_tax_show,2) .'</b></td>
//                            </tr>                            
//                            <tr>
//                                <td><b>Amount Due</b></td>
//                                <td><b>$'. number_format($amount_due,2) .'</b></td>
//                            </tr>
//                        </table>
//                    </td>
//                </tr>
//            </table>
//         </td>
//    </tr>
//    <tr>
//        <td>
//        </td>
//    </tr>
//    <tr>
//        <td>
//            <table  width="100%">
//                <tr>
//                    <td>
//                        <table>
//                            <tr>
//                                <td><b>Notes</b></td>
//                            </tr>
//                            <tr>
//                                <td>
//                                    <table >
//                                        <tr>
//                                            <td width="2%"></td>
//                                            <td>'. $note_area .'</td>
//                                        </tr>
//                                    </table>
//                                </td>
//                            </tr>
//                            <br/>
//                            <tr>
//                                <td><b>Terms and conditions</b></td>
//                            </tr>
//                            <tr>
//                                <td><br/>
//                                    <table>                                    
//                                        <tr>
//                                            <td width="3%"></td>
//                                            <td>'. $term_area .'</td>
//                                        </tr>
//                                    </table>
//                                </td>
//                            </tr>
//                            <tr width ="100%">
//                                    <td width ="80%">'. $sales_contact .'</td>
//                                    <td width ="45%">'. $accepted_by .'</td>
//                            </tr>
//                            
//                        </table>
//                    </td>
//                </tr>
//            </table>
//         </td>
//    </tr>
//</table>';

echo $tbl;
        
    }

    /**
     * XXX
     * 
     * @param  int $id XXX
     * @param  string $value XXX
     * @param  string $field_name XXX
     * @return boolean XXX
     * @access public
     */
    public function update_quotation_field($id, $value, $field_name) {
        $CQuotation = new CQuotation();
        $CQuotation->load($id);
        $CQuotation->$field_name = $value;
        return $CQuotation->store();
    }

    /**
     * XXX
     *
     * @param  int $Quotation_id XXX
     * @param  int $status_id XXX
     * @return boolean XXX
     * @access public
     */
    public function update_quotation_status($quotation_id, $status_id)
     {
        trigger_error('Not Implemented!', E_USER_WARNING);
    }

    
    function list_quotation_revision($quotation_id) {
        $CQuotationRevision = new CQuotationRevision();
        return $CQuotationRevision->loadAll(null, 'quotation_id = '.$quotation_id);
    }

    function get_quotation_revision_lastest($quotation_id,$type=false) {
            $q = new DBQuery();
            $q->addTable('sales_quotation_revision');
            if(isset($type) && $type != "")
                $q->addQuery ("*");
            else
                $q->addQuery('quotation_revision_id');

            $q->addWhere('quotation_id = '.intval($quotation_id) . ' ORDER BY quotation_revision_id DESC  LIMIT 0, 1');
            $return = $q->loadList();
            if(isset($type) && $type != "")
                return $return;
            return $return[0]['quotation_revision_id'];
    }

    function get_quotation_revision($quotation_id) {
            $q = new DBQuery();
            $q->addTable('sales_quotation_revision', 'tb1');
            $q->addQuery('tb1.quotation_revision_id, tb1.quotation_revision');
            $q->addJoin('sales_quotation', 'tb2', 'tb1.quotation_id = tb2.quotation_id');
            $q->addWhere('tb1.quotation_id = '.intval($quotation_id));
            return $q->loadList();
            //return $return[0]['quotation_revision_id'];
            /*if (count($rows) > 0){
                foreach ($rows as $row) {
                    return $row['quotation_revision_id'];
                }
            }*/
            
    }
    
    function get_quotation_item_detail($quotation_item_id) {
            $q = new DBQuery();
            $q->addTable('sales_quotation_item');
            $q->addQuery('*');
            $q->addWhere('quotation_item_id = '.intval($quotation_item_id));
            return $q->loadList();
            
    }
    
    function get_quotation_item_total($quotation_id, $quotation_revision_id) {
            $q = new DBQuery();
            $q->addTable('sales_quotation_item');
            $q->addQuery('quotation_item_price, quotation_item_quantity, quotation_item_discount');
            $q->addWhere('quotation_id = '.intval($quotation_id).' AND quotation_revision_id = '.intval($quotation_revision_id));
            $rows = $q->loadList();
            require_once DP_BASE_DIR.'/modules/sales/CSalesManager.php';
            $CSalesManager = new CSalesManager();
            
            $total = 0;
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $total += $CSalesManager->calculate_total_item($row['quotation_item_price'], $row['quotation_item_quantity'], $row['quotation_item_discount']);
                }
            }
            return $total;
    }
    
    
    function get_total_tax_and_paid($quotation_revision_id, $total_show) {
            
            //$total_paid_and_tax = CPaymentManager::get_total_payment($quotation_revision_id);
        
            $q = new DBQuery();
            $q->addTable('sales_quotation_revision', 'tbl1'); 
            $q->addQuery('tbl1.quotation_revision_tax, tbl2.tax_rate, tbl1.quotation_revision_tax_edit, tbl1.quotation_revision_discount');
            $q->addJoin('sales_tax', 'tbl2', 'tbl1.quotation_revision_tax = tbl2.tax_id');
            $q->addWhere('tbl1.quotation_revision_id = '.intval($quotation_revision_id));
            $return = $q->loadList();
            $tax_value = $return[0]['tax_rate'];
            
            $total_show_last_discount = $total_show - $return[0]['quotation_revision_discount'];
            if ($tax_value) {
                $caculating_tax = (floatval($total_show_last_discount) * floatval($tax_value) / 100);
                if($return[0]['quotation_revision_tax_edit']!=0)
                    $caculating_tax = $return[0]['quotation_revision_tax_edit'];
            }
            
            return $total_show_last_discount + $caculating_tax;
    }
    
    
    
    //Convert_quotation_into_invoice
    function create_convert_quotation_into_invoice($quotation_id, $quotation_revision_id, $quotation_status)
    {   require_once 'CSalesManager.php';
        require_once 'CInvoiceManager.php';
        require_once 'CTemplateManager.php';
        
        $CInvoiceManager = new CInvoiceManager();
        $CSalesManager = new CSalesManager();
        $CTemplateManager = new CTemplateManage();
        $quotation_details_arr = $this->get_db_quotation($quotation_id);
        $invoice_arr = array();
        if (count($quotation_details_arr) > 0) {
            $invoice_arr['invoice_id'] = 0;
            $invoice_arr['user_id'] = $quotation_details_arr[0]['user_id'];
            $invoice_arr['quotation_id'] = $quotation_details_arr[0]['quotation_id'];
            $invoice_arr['supplier_id'] = $quotation_details_arr[0]['supplier_id'];
            $invoice_arr['customer_id'] = $quotation_details_arr[0]['customer_id'];
            $invoice_arr['address_id'] = $quotation_details_arr[0]['address_id'];
            $invoice_arr['attention_id'] = $quotation_details_arr[0]['attention_id'];
            $invoice_arr['invoice_date'] = date('Y-m-d', time());
            $invoice_arr['invoice_no'] = $CSalesManager->create_invoice_or_quotation_no();
            $invoice_arr['invoice_sale_person'] = $quotation_details_arr[0]['quotation_sale_person'];
            $invoice_arr['invoice_sale_person_email'] = $quotation_details_arr[0]['quotation_sale_person_email'];
            $invoice_arr['invoice_sale_person_phone'] = $quotation_details_arr[0]['quotation_sale_person_phone'];
            $invoice_arr['invoice_internal_notes'] = $quotation_details_arr[0]['quotation_internal_notes'];
            $invoice_arr['contact_coordinator_id'] = $quotation_details_arr[0]['contact_coordinator_id'];
            $invoice_arr['job_location_id'] = $quotation_details_arr[0]['job_location_id'];
            $invoice_arr['invoice_subject'] = $quotation_details_arr[0]['quotation_subject'];
            $invoice_arr['sub_heading'] = $quotation_details_arr[0]['sub_heading'];
            $invoice_arr['department_id'] = $quotation_details_arr[0]['department_id'];
            
            $invoice_id = $CInvoiceManager->add_invoice($invoice_arr);
            //$invoice_no = $CSalesManager->create_invoice_or_quotation_no();
            
            if($invoice_id)
            {
                // convert contract theo quotation sang invoice
                require_once (DP_BASE_DIR.'/modules/sales/CContractQuotation.php');
                require_once (DP_BASE_DIR.'/modules/sales/CContractInvoice.php');
                $CContractQuotation = new CContractQuotation();
                $CContractInvoice = new CContractInvoice();
                $contract_quotation_arr = $CContractQuotation->getContractQuotaiton($quotation_id);
                foreach ($contract_quotation_arr as $contract_quotation_item) {
                    $obj['invoice_id']=$invoice_id;
                    $obj['contract_id']=$contract_quotation_item['contract_id'];
                    $CContractInvoice->addCContractInvoice($obj);
                }
            }
        }
        
        // Lay Term And Condition Mat dinh
        $term_condition_arr = $CTemplateManager ->getlist_term_condition();
        foreach ($term_condition_arr as $term_row) {
            if($term_row['template_type']==1 && $term_row['term_default']==1){
                $term_area .= "\n".$term_row['term_conttent'];
            }
        }
        
        $quotation_revsion_details_arr = $this->get_db_quotation_revsion($quotation_revision_id);
        $invoice_revsion_arr = array();
        if (count($quotation_revsion_details_arr) > 0){
            $invoice_revsion_arr['invoice_revision_id'] = 0;
            $invoice_revsion_arr['invoice_id'] = $invoice_id;
            $invoice_revsion_arr['invoice_revision'] = $CSalesManager->create_invoice_or_quotation_revision('', $invoice_arr['invoice_no']);
            $invoice_revsion_arr['user_id'] = $quotation_revsion_details_arr['0']['user_id'];
            $invoice_revsion_arr['invoice_revision_tax'] = $quotation_revsion_details_arr['0']['quotation_revision_tax'];
            $invoice_revsion_arr['invoice_revision_currency'] = $quotation_revsion_details_arr['0']['quotation_revision_currency'];
            $invoice_revsion_arr['invoice_revision_notes'] = $quotation_revsion_details_arr['0']['quotation_revision_notes'];
            //$invoice_revsion_arr['invoice_revision_term_condition'] = $quotation_revsion_details_arr['0']['quotation_revision_term_condition_contents'];
            $invoice_revsion_arr['invoice_revision_term_condition'] = $term_area;
            $invoice_revsion_arr['invoice_revision_tax_edit'] = $quotation_revsion_details_arr['0']['quotation_revision_tax_edit'];
            $invoice_revsion_arr['invoice_revision_discount'] = $quotation_revsion_details_arr[0]['quotation_revision_discount'];
            $invoice_revsion_arr['reference_no'] = $quotation_revsion_details_arr[0]['reference_no'];
            
            $invoice_revsion_id = $CInvoiceManager->add_invoice_revision($invoice_revsion_arr);  
            //$quotation_rev = $CSalesManager->create_invoice_or_quotation_revision('', $invoice_no);
        }
        
        $quotation_item_details_arr = $this->get_db_quotation_item($quotation_id, $quotation_revision_id);
        $invoice_item_arr = array();
        if (count($quotation_item_details_arr) > 0){
            foreach ($quotation_item_details_arr as $quotation_item_details){
             
            $invoice_item_arr['invoice_item_id'] = 0;
            $invoice_item_arr['invoice_revision_id'] = $invoice_revsion_id;
            $invoice_item_arr['invoice_id'] = $invoice_id;
            $invoice_item_arr['user_id'] = $invoice_revsion_arr['user_id'];
            $invoice_item_arr['invoice_item'] = $quotation_item_details['quotation_item'];
            $invoice_item_arr['invoice_item_price'] = $quotation_item_details['quotation_item_price'];
            $invoice_item_arr['invoice_item_quantity'] = $quotation_item_details['quotation_item_quantity'];
            $invoice_item_arr['invoice_item_discount'] = $quotation_item_details['quotation_item_discount'];
            $invoice_item_arr['invoice_order'] = $quotation_item_details['quotation_order'];
            $invoice_item_id = $CInvoiceManager->add_invoice_item($invoice_item_arr);              
            }                       
        }
        
        if (count($quotation_details_arr)>0 && count($quotation_revsion_details_arr)>0){
            $quotation_id = $quotation_details_arr[0]['quotation_id'];
            $quotation_revsion_id = $quotation_revsion_details_arr[0]['quotation_revsion_id'];
        }  
        return $invoice_id;   
       }
    
    //End - convert_quotation_into_invoice
    


    //Copy quotation
    function create_copy_quotation($quotation_id_arr, $quotation_revision_id_arr = null)
    {   
        require_once 'CSalesManager.php';
        $CSalesManager = new CSalesManager();
        if (($count = count($quotation_id_arr)) > 0) {
            for ($i = 0; $i < $count; $i++) {

                $quotation_id = $quotation_id_arr[$i];
                if ($quotation_revision_id_arr != null)
                    $quotation_revision_id = $quotation_revision_id_arr[$i];
                else
                    $quotation_revision_id = $this->get_quotation_revision_lastest($quotation_id);
            }
        }
        
        $quotation_details_arr = $this->get_db_quotation($quotation_id);        
        $quotation_arr = array();
        if (count($quotation_details_arr) > 0) {
            $quotation_arr['quotation_id'] = 0;
            $quotation_arr['user_id'] = $quotation_details_arr[0]['user_id'];
            $quotation_arr['supplier_id'] = $quotation_details_arr[0]['supplier_id'];
            $quotation_arr['customer_id'] = $quotation_details_arr[0]['customer_id'];
            $quotation_arr['address_id'] = $quotation_details_arr[0]['address_id'];
            $quotation_arr['attention_id'] = $quotation_details_arr[0]['attention_id'];
            $quotation_arr['quotation_date'] = $quotation_details_arr[0]['quotation_date'];
            $quotation_arr['quotation_no'] = $CSalesManager->create_invoice_or_quotation_no(true);
            $quotation_arr['quotation_sale_person'] = $quotation_details_arr[0]['quotation_sale_person'];
            $quotation_arr['quotation_sale_person_email'] = $quotation_details_arr[0]['quotation_sale_person_email'];
            $quotation_arr['quotation_sale_person_phone'] = $quotation_details_arr[0]['quotation_sale_person_phone'];
            $quotation_arr['quotation_internal_notes'] = $quotation_details_arr[0]['quotation_internal_notes'];            
            //$quotation_arr['quotation_status'] = $quotation_details_arr[0]['quotation_status'];
            $quotation_arr['quotation_relation'] = $quotation_details_arr[0]['quotation_relation'];
            
            $quotation_id_new = $this->add_quotation($quotation_arr);
        }
        
        $quotation_revsion_details_arr = $this->get_db_quotation_revsion($quotation_revision_id);
        $quotation_revsion_arr = array();
        if (count($quotation_revsion_details_arr) > 0){

            $quotation_revsion_arr['quotation_revision_id'] = 0;
            $quotation_revsion_arr['quotation_id'] = $quotation_id_new;
            $quotation_revsion_arr['quotation_revision'] = $CSalesManager->create_quotation_revision('', $quotation_arr['quotation_no']);
            $quotation_revsion_arr['user_id'] = $quotation_revsion_details_arr['0']['user_id'];
            $quotation_revsion_arr['quotation_revision_tax'] = $quotation_revsion_details_arr['0']['quotation_revision_tax'];
            $quotation_revsion_arr['quotation_revision_currency'] = $quotation_revsion_details_arr['0']['quotation_revision_currency'];
            $quotation_revsion_arr['quotation_revision_notes'] = $quotation_revsion_details_arr['0']['quotation_revision_notes'];
            $quotation_revsion_arr['quotation_revision_term_condition_contents'] = $quotation_revsion_details_arr['0']['quotation_revision_term_condition_contents'];
            $quotation_revsion_arr['quotation_revision_is_approve'] = $quotation_revsion_details_arr['0']['quotation_revision_is_approve'];
            $quotation_revsion_arr['quotation_revision_tax_edit'] = $quotation_revsion_details_arr['0']['quotation_revision_tax_edit'];
            $quotation_revsion_arr['quotation_revision_date'] = date('Y-m-d');
            $quotation_revsion_arr['quotation_revision_discount'] = $quotation_revsion_details_arr[0]['quotation_revision_discount'];
            
            $quotation_revision_id_new = $this->add_quotation_revision($quotation_revsion_arr);
        }
        
        $quotation_item_details_arr = $this->get_db_quotation_item($quotation_id, $quotation_revision_id);
        //print_r($quotation_item_details_arr);
        $quotation_item_arr = array();
        if (count($quotation_item_details_arr) > 0){
            foreach ($quotation_item_details_arr as $quotation_item_details){
             
            $quotation_item_arr['quotation_item_id'] = 0;
            $quotation_item_arr['quotation_revision_id'] = $quotation_revision_id_new;
            $quotation_item_arr['quotation_id'] = $quotation_id_new;
            $quotation_item_arr['quotation_item'] = $quotation_item_details['quotation_item'];
            $quotation_item_arr['quotation_item_price'] = $quotation_item_details['quotation_item_price'];
            $quotation_item_arr['quotation_item_quantity'] = $quotation_item_details['quotation_item_quantity'];
            $quotation_item_arr['quotation_item_discount'] = $quotation_item_details['quotation_item_discount'];
            $quotation_item_id_new = $this->add_quotation_item($quotation_item_arr);              
            }                       
        }
        
        if (count($quotation_details_arr)>0 && count($quotation_revsion_details_arr)>0){
            $quotation_id = $quotation_details_arr[0]['quotation_id'];
            $quotation_revsion_id = $quotation_revsion_details_arr[0]['quotation_revsion_id'];
        } return true;    
    }   
    //Copy quotation - end
    
    // truong hop thay doi revesion
    function insert_history_quotation_invoice($quotation_id, $quotation_revision_no_current, $quotation_revision_no=false,$status=false) {
        global $AppUI;
        //cat lay 2 so cuoi cua revision
        $suffix_count = 3;
        $obj_revision_substr = substr($quotation_revision_no, -$suffix_count);
        $obj_revision_substr_from = substr($quotation_revision_no_current, -$suffix_count);
        $user_id = $AppUI->user_id;
        $date = date("Y-m-d H:i:s");    
        
        $Obj = new CQuotationInvoiceHistory();
        $Obj->quo_invc_history_id = 0;
        $Obj->quo_invc_id = $quotation_id;
        $Obj->quo_or_invc_history = 0;
        $Obj->quo_invc_history_type = 0;
        if($status=="add"){
            $Obj->quo_invc_history_update = 'Add quotation revision ' .$quotation_revision_no;
        }
        else if($status=="delete")
            $Obj->quo_invc_history_update = 'Delete quotation revision ' .$obj_revision_substr_from;
        else
            $Obj->quo_invc_history_update = 'Move from revision ' .$obj_revision_substr_from . ' to ' .$obj_revision_substr;
        $Obj->quo_invc_history_user = $user_id;
        $Obj->quo_invc_history_date = $date;
        return $Obj->store();

    }
    
    
    // truong hop thay doi status
    
    function insert_history_quotation_status($quotation_id, $updated_content, $status_update) {
        global $AppUI;
        
        $quotation_status = dPgetSysVal('QuotationStatus');
        
        $user_id = $AppUI->user_id;
        $date = date("Y-m-d H:i:s");    
            $Obj = new CQuotationInvoiceHistory();
            $Obj->quo_invc_history_id = 0;
            $Obj->quo_invc_id = $quotation_id;
            $Obj->quo_or_invc_history = 0;
            $Obj->quo_invc_history_type = 1;
            $Obj->quo_invc_history_update = 'Update status from ' .$quotation_status[$status_update] . ' to ' .$quotation_status[$updated_content];
            $Obj->quo_invc_history_user = $user_id;
            $Obj->quo_invc_history_date = $date;
            return $Obj->store();
    }
    function get_list_db_history($quotation_id) {
        $q = new DBQuery();
        $q->addTable('quo_invc_history');
        $q->addQuery('*');
        $q->addWhere('quo_invc_id = '.$quotation_id.' AND quo_or_invc_history = 0');
        return $q->loadList();
    }
    
    function get_user_change($user_id) {
        $q = new DBQuery();
        $q->addTable('users');
        $q->addQuery('user_username');
        $q->addWhere('user_id = '.$user_id);
        $row = $q->loadList();
        return $row[0]['user_username'];
    }

    function max_order_item($quotation_id,$quotation_rev_id){
        $q = new DBQuery();
        $q->addTable('sales_quotation_item');
        $q->addQuery('quotation_order');
        $q->addWhere ('quotation_id ='.intval($quotation_id));
        $q->addWhere('quotation_revision_id='.intval($quotation_rev_id));
        $q->addOrder('quotation_order DESC');
        $row=$q->loadList();
        return $row[0]['quotation_order'];
    }
    function update_quotation_item_order($id,$quotation_item_order){
        $CQuotationItem = new CQuotationItem();
        $CQuotationItem ->load($id);
        $CQuotationItem->quotation_order = $quotation_item_order;
        $CQuotationItem->store();
        return $CQuotationItem->quotation_item_id;
    }
//    public function update_quotation_item($id, $quo_id, $quo_rev_id, $quotation_item, $quotation_price, $quotation_qty, $quo_dis, $quo_order){
//        $CQuotationItem = new CQuotationItem();
//        $CQuotationItem ->load($id);
//        $CQuotationItem -> quotation_item_discount = $quo_dis;
//        $CQuotationItem -> quotation_revision_id = $quo_rev_id;
//        $CQuotationItem ->quotation_id = $quo_id;
//        $CQuotationItem ->quotation_item = $quotation_item;
//        $CQuotationItem ->quotation_item_quantity = $quotation_qty;
//        $CQuotationItem ->quotation_item_price = $quotation_price;
//        $CQuotationItem ->quotation_item_discount = $quo_dis;
//        $CQuotationItem ->quotation_order = $quo_order;
//        $CQuotationItem->store();
//        return $CQuotationItem->quotation_item_id;
//        
//    }
    function  list_quotation_item_order($quotation_item_id){
        $q = new DBQuery();
        $q->addTable('sales_quotation_item');
        $q->addQuery('quotation_order');
        $q->addWhere('quotation_item_id='.intval($quotation_item_id));
        return $q->loadList();
    }
    function getQuotationsOfCompany($company_id = false, $address_id = false) {
        $q = new DBQuery();
        $q->addTable('sales_quotation');
        $q->addQuery('sales_quotation.quotation_id');
        if ($company_id)
            $q->addWhere('customer_id = '.intval($company_id));
        if ($address_id)
            $q->addWhere('address_id = '.intval($address_id));
        
        return $q->loadList();
    }
    function update_quotation_rev_tax($id,$tax_edit){
        $Revision = new CQuotationRevision();
        $Revision->load($id);
        $Revision->quotation_revision_tax_edit = $tax_edit;
        return $Revision->store();

    }
    
    /**
     * Lay danh sach Customer da gan vao Quotation
     * @return array
     * @access public
     */
    public function getCustomerByQuotation($customer_id=false,$address_id=false, $brand=false)
    {
        $q = new DBQuery();
        $q->addTable('clients','tbl1');
        $q->addQuery('tbl1.company_id, tbl1.company_name, tbl2.address_id,tbl3.address_branch_name,tbl3.address_branch_name,tbl3.address_street_address_2,tbl3.address_postal_zip_code,tbl3.address_street_address_1');
        $q->addJoin('sales_quotation', 'tbl2', 'tbl1.company_id=tbl2.customer_id', 'INNER');
        $q->addJoin('addresses', 'tbl3', 'tbl2.address_id=tbl3.address_id');
        if($customer_id)
            $q->addWhere('tbl1.company_id='.  intval($customer_id));
        if($address_id)
        {
            $q->addWhere('tbl2.address_id='.intval($address_id));
        }
        if($brand)
        {
            $q->addGroup('tbl2.address_id');
        }
        else 
        {
            $q->addGroup('tbl1.company_id');
        }
        $q->addOrder('tbl1.company_name ASC');
        
        return $q->loadList();
    }
    
    public function get_invoice_by_quotation($quotaiton_id)
    {
        $q = new DBQuery();
        $q->addTable('sales_invoice');
        $q->addQuery('invoice_no,invoice_id');
        $q->addWhere('quotation_id='.intval($quotaiton_id));
        $row = $q->loadList();
        return $row;
    }
    public function check_item_quotation($quotation_id,$quotation_revision_id,$quotation_item,$quotation_item_id){
        $q = new DBQuery();
        $q->addTable('sales_quotation_item');
        $q->addQuery('*');
        $q->addWhere('quotation_id='.intval($quotation_id));
        $q->addWhere('quotation_revision_id='.intval($quotation_revision_id));
        $q->addWhere('quotation_item_id!='.intval($quotation_item_id));
        $rows = $q->loadList();
        foreach ($rows as $row) {
            if($row['quotation_item']==$quotation_item)
                return false;
        }
        return true;
    }
    public function get_list_db_quotation() {

            $q = new DBQuery();
            $q->addTable('sales_quotation');
            $q->addQuery('quotation_id');
            return $q->loadList();
            
    }
    function get_quotation_history_today()
    {
        $q = new DBQuery();
        $q->addTable('quo_invc_history');
        $q->addQuery('quo_invc_id');
        $q->addWhere('CAST(quo_invc_history_date AS DATE)="'.date('Y-m-d').'"');
        $q->addWhere('quo_or_invc_history=0');
        return $q->loadList();
    }
    
    public function create_quotation_cron_pdf_file($quotation_id_arr, $quotation_revision_id_arr,$dir) {
        //require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        require_once (DP_BASE_DIR . '/modules/sales/CHeaderQuotation.php');
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        require_once (DP_BASE_DIR . "/modules/contacts/contacts_manager.class.php");
        require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
        require_once(DP_BASE_DIR."/modules/sales/CQuotationManager.php");
        $CQuotationManager = new CQuotationManager();
        require_once(DP_BASE_DIR."/modules/sales/CSalesManager.php");
        $CSalesManager = new CSalesManager();
        $ContactManager = new ContactManager();
        $cconfig = new CConfigNew();
        
        $pdf = new MYPDFQuotation(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->add; 
        $pdf->SetCreator(PDF_CREATOR);
        //$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //$pdf->SetMargins(10, 37, 8, -15);
        
        $pdf->setHeaderMargin(3);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        $pdf->setPrintFooter(true);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 10.3);
            
        
//        if ($quotation_revision_id_arr == null) {
//            if (($count = count($quotation_id_arr)) > 0) {
//                for ($i = 0; $i < $count; $i++) {
//                    $quotation_id = $quotation_id_arr[$i];
//                    $quotation_revision_id = $this->get_quotation_revision_lastest($quotation_id);
//                    
//                }
//            }
//        } else {
             $quotation_id = $quotation_id_arr;
             $quotation_revision_id = $quotation_revision_id_arr;   
//       }
       $quotation_arr =$CQuotationManager->get_db_quotation($quotation_id);
       $quotation_arr_rev = $CQuotationManager->get_db_quotation_revsion($quotation_revision_id);
       
        $customer_id = $quotation_arr[0]['customer_id'];
        $Customer_co_id = $quotation_arr[0]['quotation_CO'];
        $date = $quotation_arr[0]['quotation_date'];
        $quotation_no = $quotation_arr[0]['quotation_no'];
        $service_order = $quotation_arr[0]['service_order'];

        $sale_person_name = $quotation_arr[0]['quotation_sale_person'];
        $sale_person_email = $quotation_arr[0]['quotation_sale_person_email'];
        $sale_person_phone = $quotation_arr[0]['quotation_sale_person_phone'];
        $sale_coordinator_id = $quotation_arr[0]['contact_coordinator_id'];

        $client_id = $quotation_arr[0]['customer_id'];
        $address_id = $quotation_arr[0]['address_id'];
        $attention_id = $quotation_arr[0]['attention_id'];
        $contact_id = $quotation_arr[0]['contact_id'];
        $email_rr=CSalesManager::get_attention_email($attention_id);
        $job_location_id = $quotation_arr[0][job_location_id];
        $subject = $quotation_arr[0]['quotation_subject'];
        
        $tax_id = $quotation_arr_rev[0]['quotation_revision_tax'];
        $tax_update = $quotation_arr_rev[0]['quotation_revision_tax_edit'];
        
        $supplier_arr = $CSalesManager->get_supplier_info();
        if (count($supplier_arr) > 0) {
            $sales_owner_name = $supplier_arr['sales_owner_name'];
            $sales_owner_address =$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'];
            //$sales_owner_address += 
            $phone =$supplier_arr['sales_owner_phone1'];
            $fax = $supplier_arr['sales_owner_fax'];
            $email =$supplier_arr['sales_owner_email'];
            $web = $supplier_arr['sales_owner_website'];
            $sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
            $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
        }
        
        $supplier = '<tr><td style="font-size:1.2em;"><b>'. $sales_owner_name .'</b></td></tr>';
        $supplier.= '<tr><td>'.$sales_owner_address.'</td></tr>';
        if($fax!="")
        {
            $fax5 = ', Fax: '.$fax;
        }
        $supplier .= '<tr><td>Tel: '.$phone.', Fax: '.$fax.'</td></tr>';
    //    $supplier .= '<tr><td>Fax: '.$fax.'</td></tr>';
        if($sales_owner_gst_reg_no!="")
            $supplier .= '<tr><td>GST Reg No: '. $sales_owner_gst_reg_no .'</td></tr>';
    //    $supplier.= '<tr><td>Reg No: '. $supplier_arr['sales_owner_postal_code'] .'</td></tr>';
        $supplier .= '<tr><td>Email: '.$email.'</td></tr>';

        // Info Quotaiton
        $info2 = 'Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            : '. date('d-m-Y',  strtotime($date)).'<br>';
        $info2 .= 'Quotation No&nbsp;&nbsp;: '. $quotation_no .'<br>';
        //$info2 .= 'Quotation Rev: '. $quotation_rev.'<br/>';
        $info2 .= 'Service Oder : '. $service_order;
        
        $info4='Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            : '. date('d-m-Y',  strtotime($date)).'<br>';
        $info4 .= 'Quotation No&nbsp;&nbsp;: '. $quotation_arr_rev[0]['quotation_revision'] .'<br>';
        //$info4 .= 'Quotation Rev: '. $quotation_rev.'<br/>';
        $info4 .= 'Service Oder : '. $service_order;
        
        $quotation_rev = $quotation_arr_rev[0]['quotation_revision'];
        $sub_revision_no = substr($quotation_rev, -2);
        
        // Get LOGO sales //
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        
        $files = list_files($url);
        if(count($files)>0)
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
            $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" /></td>';
        }
        
//        $files = scandir($url);
//        $i = count($files) -1;
//        if (count($files) > 2 && $files[$i]!=".svn") {
//            $path_file = $url . $files[$i];
//            $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" /></td>';
//        } // Get LOGO sales //

        // company name && CO
        $customer_arr = $CSalesManager->get_customer_name($customer_id);
        $customer_co_arr = $CSalesManager->get_customer_name($Customer_co_id);

        // Get address
        $client_address =  CSalesManager::get_address_by_id($address_id);

            if (count($client_address) > 0) {
                if($client_address['address_branch_name']!="")
                    $option.=$client_address['address_branch_name'].'<br>';
                 $option.= $client_address['address_street_address_1'];
                if($client_address['address_street_address_2']!="")
                    $option.='<br>'.$client_address['address_street_address_2'];
        //        if($list_countries[$client_address['address_country']]!="")
        //            $option.=', '.$list_countries[$client_address['address_country']];
                $option .='<br>Singapore '.$client_address['address_postal_zip_code'];
                if($client_address['address_phone_1']!="" || $client_address['address_fax']!="")
                    $option.='<br>';
                if($client_address['address_phone_1']!="")
                    $option.='Tel: '.$client_address['address_phone_1'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                if($client_address['address_fax']!="")
                    $option.='Fax: '.$client_address['address_fax'];
        //        if($client_address['ddress_country']!="")
        //            $option.=$client_address['ddress_country'];
        //        $option = $client_address['address_phone_1'] .', '. $client_address['address_fax'] , '. $client_address['address_email'];
            }
        $address= $option;

        ######
         $quo_attention_arr = CSalesManager::get_salesAttention_by_SalesType($_REQUEST['quotation_id'], "quotation");
         $count_inv_attention = count($quo_attention_arr);
         $contac_titlet_arr = dPgetSysVal('ContactsTitle');

                  if($count_inv_attention<=0){
          $attention_arr = CSalesManager::get_list_attention($client_id);
          $attention_option_print = array();
           if (count($attention_arr) > 0) {
               //$attention_option = '';
               foreach ($attention_arr as $attention) {
                 //  $selected = '';
                   if ($attention_id == $attention['contact_id']){
                      if($attention['contact_title']!=0)
                          $contac_title = $contac_titlet_arr[$attention['contact_title']]." ";
                      $attention_option = $contac_title .$attention['contact_first_name'] .' '. $attention['contact_last_name'] ;
                   }
               }
           }
           if($attention_option!="")
            $attention_option_print[] = $attention_option;
         }else{
              $attention_option_print = array();
              foreach ($quo_attention_arr as $inv_attention_row) {
                  $attention_id = $inv_attention_row['attention_id'];
                  $attention_arr = CSalesManager::get_list_attention($client_id);
                  if (count($attention_arr) > 0) {
                      //$attention_option = '';
                      foreach ($attention_arr as $attention) {
                        //  $selected = '';
                          if ($attention_id == $attention['contact_id']){
                              if($attention['contact_title']!=0)
                                  $contac_title = $contac_titlet_arr[$attention['contact_title']]." ";
                              $attention_option = $contac_title.$attention['contact_first_name'] .' '. $attention['contact_last_name'] ;
                          }
                      }
                  }
                  if($attention_option!="")
                    $attention_option_print[]= $attention_option;
              }
         }

        $title_arr = dPgetSysVal('CustomerTitle');
        $title = "";
        if($title_arr[$customer_arr[0]['company_title']]!="")
            $title = $title_arr[$customer_arr[0]['company_title']]." ";
        $customer = '<b>Bill To: '.$title. $customer_arr[0]['company_name'] .'</b>';
        $quotation_co_name = $customer_co_arr[0]['company_name'];
        if($quotation_co_name!="")
            $customer .='<br/><b>C/O: '.$quotation_co_name.'</b>';
        if($address!="")
            $customer .= '<br/>'. $address;

        if(count($attention_option_print)>0)
            $customer .= '<br/>Attention: '. implode (' / ', $attention_option_print).'';

        $customer5 = '<b>'.$title. $customer_arr[0]['company_name'] .'</b>';
        if($quotation_co_name!="")
            $customer5 .='<br/><b>C/O: '.$quotation_co_name.'</b>';
        if($address!="")
            $customer5 .= '<br/>'. $address;

        if($attention_option_print!="")
            $customer5 .= '<br/>Attention: '. $attention_option_print.'';
        // Get Joblocation
        $postal_code = "";
        $job_location_arr =  CSalesManager::get_address_by_id($job_location_id);
        if($job_location_arr>0){
            $postal_code = ', Singapore '.$client_address['address_postal_zip_code'];
        }

        // Load ra Email ca Mobile Cua Jo Location.
            $contact_arr = CSalesManager::get_attention_email($contact_id);
           //print_r($contact_arr);
           $contact = "";
           if(count($contact_arr)>0){
                $contact.="<br>Contact: ".$contact_arr['contact_first_name'].' '.$contact_arr['contact_last_name'];
                if($contact_arr['contact_email']!="")
                    $contact.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email: ".$contact_arr['contact_email'].'&nbsp;&nbsp;&nbsp;&nbsp;';
                if($contact_arr['contact_mobile']!="")
                    $contact.="Mobile: ".$contact_arr['contact_mobile'];
           }
            $brand="";
            if($job_location_arr['address_branch_name']!="")
                $brand=$job_location_arr['address_branch_name'].' - ';
            if($job_location_arr['address_street_address_2']!="")
                $address_2= ', '.$job_location_arr['address_street_address_2'];
            $postal_code_job = ',Singapore ';
            if($job_location_arr['address_postal_zip_code']!="")
                $postal_code_job .=$job_location_arr['address_postal_zip_code'];
            $job_location = "";

            if($job_location_arr!=""){
                    $job_location.='<tr><td colspan="6" width="98%" style="border:1px soild #000;"><b>Job Location:</b> '.$brand.CSalesManager::htmlChars($job_location_arr['address_street_address_1'].' '.$address_2.$postal_code_job).$contact.'</td></tr>';
            }

            $subject_note = "";
            if($subject!=""){
                    $subject_note.='<tr><td colspan="6" width="98%" style="border:1px soild #000;"><b>Subject:</b> '.CSalesManager::htmlChars($subject).'</td></tr>';
            }
            if($subject!="" || $job_location_arr!=""){
                $tr_br = '<tr style="line-height: 50%;"><td></td></tr>';
            }
            
            $sales_agent = "";
            if($sale_person_name!="")
                $sales_agent.="<br>Name: ".$sale_person_name;
            if($sale_person_email!="")
                $sales_agent.="<br>Email: ".$sale_person_email;
            if($sale_person_phone!="")
                $sales_agent .="<br>Phone: ".$sale_person_phone;
            if($sale_coordinator_id!=0)
                $sales_agent .="<br>Service Coordinator: ".$CQuotationManager->get_user_change($sale_coordinator_id);;
            if($sales_agent!=""){
                    $sales_agent.='<tr><td colspan="6" width="99%" style="border:1px soild #000;"><b>Sales Agent:</b> '.$brand.CSalesManager::htmlChars($sales_agent).'</td></tr>';
            }
        
        
        
        $pdf->SetMargins(10,6, 8, -15);
        
        $CTax = new CTax();
        $tax_arr = $CTax->list_tax();
        $currency_symb = '$'; $total_paid_and_tax = 0;
        
        if(is_array($quotation_id)) {
            $quotation_id = $quotation_id[0];
        }

    $dis_label ="";
    $dis_label="";
    $a = '60%';
    $col ="2";
    $quotation_item_arr = $this->get_db_quotation_item($quotation_id, $quotation_revision_id);
    $booldis=0;
    foreach ($quotation_item_arr as $invoice_item1) {
        if($invoice_item1['quotation_item_discount']!=0){
            $booldis=1;
        }
    }

    $total_item_show = $this->get_quotation_item_total($quotation_id, $quotation_revision_id);

    $quotation_rev_details_arr = $this->get_latest_quotation_revision($quotation_id);

    //tax (Anhnn)
   $tax_option = '<option vaue="">---</option>';
    if (count($tax_arr) > 0) {
        foreach ($tax_arr as $tax) {
            $tax_name = $tax['tax_name'];
            $tax_value =0;
            $selected = '';
            if ($tax_id) { // neu tinh trang la update
                if ($tax['tax_id'] == $tax_id) {
                    $selected = 'selected="selected"';
                    $tax_value = $tax['tax_rate'];
                }
            } else { // neu tinh trang la add lay ra ta default
                if ($tax['tax_default'] == 1) {
                    $selected = 'selected="selected"';
                    $tax_value = $tax['tax_rate'];
                }
            }

            $tax_option .= '<option value="'. $tax['tax_id'] .'" '. $selected .'>'. $tax['tax_rate'] .'</option>';
        }
    }
    $caculating_tax =0;
    if ($tax_id) {
        $caculating_tax = (floatval($total_item_show) * floatval($tax_value) / 100);
        if($tax_update!=0)
            $caculating_tax = CSalesManager::round_up($tax_update);
        else
            $caculating_tax = CSalesManager::round_up($caculating_tax);
        $total_item_tax_show = $caculating_tax;
        $total_paid_and_tax += $caculating_tax;
        
    }
    $AmountDue=$total_item_show + $total_item_tax_show;
    $tax_select = '<select name="quotation_revision_tax" id="quotation_revision_tax" class="text">'. $tax_option .'</select>';
    $percent = "";
    if($tax_value !="")
    {
            $percent = $tax_value."%";
    }
    $total_box = '
                    <table border="0" width="100%" cellspacing="0" cellpadding="2" >
                            <tr>
                                <td width="60%" align="right"><b>Total:</b></td>
                                <td width="41%" align="right"><b>$'.number_format($total_item_show,2).'</b></td>
                            </tr>
                            <tr>
                                <td align="right"><b>GST&nbsp;'.$percent.':</b></td>
                                <td align="right"><b>$'.number_format($caculating_tax,2).'</b></td>
                            </tr>
                            <tr>
                                <td align="right"><b>Total Amount:</b></td>
                                <td align="right"><b>$'.number_format($AmountDue,2).'</b></td>
                            </tr>
                    </table>
        ';
        $termCo=$quotation_rev_details_arr[0]['quotation_revision_term_condition_contents'];
        if($termCo!=""){
                $term='<table border="0" width="100%" cellspacing="0" cellpadding="2"><tr>
                        <td><b>Terms and Conditions:</b><br>'.CSalesManager::htmlChars($quotation_rev_details_arr[0]['quotation_revision_term_condition_contents']) .'</td>
                </tr></table>';
        }
        if($quotation_rev_details_arr[0]['quotation_revision_notes']!="")
        {
            $note_area='<br><br><b>Notes:</b> <br>'.CSalesManager::htmlChars($quotation_rev_details_arr[0]['quotation_revision_notes']);
        }
        // end active default Template pdf
            $tbl_header.='
                <table border="0" cellspacing="0" cellpadding="0">
                        <tr valign = "top" height="60">
                            '.$img.'
                            <td width="71%"><table border="0"  width="100%">'.$supplier.'</table></td>
                        </tr>
                 </table>
                <table width="100%" border="0" cellpadding="5">
                        <tr >
                            <td colspan="5" align="right" style="font-size:1.2em;"><b>Revision No: '.$quotation_rev.'</b><hr></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align:center;font-weight:bold;font-size:1.5em;">
                                 QUOTATION
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">'.$customer.'</td>
                            <td colspan="3">'.$info2.'</td>
                        </tr>
                        '.$job_location.'
                        '.$subject_note.'
                        '.$tr_br.'
                   </table>';
            $tbl_header_item = '<table width="100%" border="0" cellpadding="5">
                        <tr style="font-weight:bold;">
                            <td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
                            <td style="border:1px soild #000;" width="'.$a.'">Description</td>
                            <td style="border:1px soild #000;" align="center" width="6%">Qty</td>
                            <td style="border:1px soild #000;" align="right" width="13%">Unit Price</td>
                            '.$dis_label.'
                            <td style="border:1px soild #000;" align="right" width="13%">Amount</td>
                        </tr>
                        </table>
                ';
            $pdf->SetAutoPageBreak(true, 20);
            $pdf->AddPage('P', 'A4');
            $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item);
            $pdf->ln();
            
            if (isset($quotation_item_arr) && count($quotation_item_arr) > 0) {
                     $tr_item = ''; $i = 1; $border = "";
                     foreach ($quotation_item_arr as $quotation_item) {

                         if ($quotation_item['quotation_item_type'] == 0) {
                             $item = $quotation_item['quotation_item'];
                         } elseif ($quotation_item['quotation_item_type'] == 1) {
                             $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                         } elseif ($quotation_item['quotation_item_type'] == 2) {
                             $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                         } else {
                             $item = '<font color="red">Item type not found.</font>';
                         }

                         if($booldis==1){
                             $dis_label='<td  align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                             $dis_value='<td  align="center" width="11%" align="right">'. $quotation_item['quotation_item_discount'].'%' .'</td>';
                             $a = '50%';
                             $col ="3";
                         }
                         $tr_item = '<table width="100%" border="0" cellpadding="5">
                                        <tr>
                                             <td  align="center" width="6%">'. $i .'</td>
                                             <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                             <td  align="center"  width="6%">'. round($quotation_item['quotation_item_quantity']) .'</td>
                                             <td  align="center" align="right" width="13%">$'. number_format($quotation_item['quotation_item_price'],2) .'</td>
                                             '.$dis_value.'
                                             <td  align="center" align="right" width="13%">$'. number_format(CSalesManager::calculate_total_item($quotation_item['quotation_item_price'], $quotation_item['quotation_item_quantity'], $quotation_item['quotation_item_discount']),2) .'</td>
                                         </tr></table>';
                        if($pdf->GetY()>$pdf->getPageHeight()-55){
                                  $pdf->AddPage();
                                  $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item);
                                  $pdf->ln();
                              }
                         $i++;
                         $pdf->writeHTMLCell('','','','',$tr_item);
                         $pdf->ln();
                     }
             }
             
            // Hien thi box total
            $tbl_footer='<table width="98%" border="0" cellpadding="2">';
                $tbl_footer.='<tr>
                        <td colspan="'.$col.'">'.$note_area.'</td>
                        <td colspan="3">'.$total_box.'</td>
                    </tr>';
            $tbl_footer.='</table>';
            if($total_box=="")
                    $hf = 30;
            else
                    $hf = 62;
            if($pdf->GetY()>$pdf->getPageHeight()-$hf){
                      $pdf->AddPage();
                      $pdf->writeHTMLCell('','','','',$tbl_header);
                      $pdf->ln();
                  }
            $pdf->writeHTMLCell('','','','',$tbl_footer);
            $pdf->ln();
            // Hien thi term and codition
            $hterm ='';
            if($pdf->GetY()>$pdf->getPageHeight()-85){
                  $pdf->AddPage();
                  $pdf->writeHTMLCell('','','','',$tbl_header);
                  $pdf->ln();
                  $hterm = 110; 
            }
            $pdf->writeHTMLCell('','','',$hterm,$term);
            $pdf->ln();
            $your = '<table>
                <tr>
                    <td>Yours Sincerely</td>
                </tr></table>';
            $pdf->writeHTMLCell('','','',262,$your);
            
            ob_end_clean();
            $pdf->Output($dir.'/'.$quotation_rev.'.pdf','F'); // Save file PDF vao forder
            //$pdf->Output($quotation_rev.'.pdf','I');
    } 
    public function get_quotation_by_joblocation($location_id){
        $q = new DBQuery();
        $q->addTable('sales_quotation');
        $q->addQuery('*');
        $q->addWhere('job_location_id = '.intval($location_id));
        $q->addOrder('quotation_no ASC');
        return $q->loadList();
    }
    
    public function permissionQuotationByDepartment($quotation_id)
    {
        global $AppUI;
        require_once(DP_BASE_DIR . '/modules/departments/CDepartment.php');
        $CDepManager = new CDepartment();
        $department_arr = $CDepManager->getDeparmentByUser($AppUI->user_id);
        
        $quotation_arr = $this->get_db_quotation_status($quotation_id);
        $dept_quo_id = $quotation_arr[0]['department_id'];
        foreach ($department_arr as $department_row) {
            if($dept_quo_id == $department_row['dept_id'])
                return true; 
        }
        return false;
    }
    /**
     * Lay quotation co status Accepted va chua tao invoice theo contract
     * @param type $contract_id
     * @return type
     */
    public function getQuotationAcceptedByContract($contract_id,$status=3)
    {
        // Lay quotaiton da covert sang invoice
        
        $quotation_arr=$this->getQuotationIdConvertInvoice($quotaiton_id);
        $quotation_id_arr = array(0=>'0');
        foreach ($quotation_arr as $quotation_item) {
            $quotation_id_arr[] = $quotation_item['quotation_id'];
        }
        
        $q = new DBQuery();
        $q->addQuery('*');
        $q->addTable('sales_quotation','q');
        $q->addJoin('sales_quotation_contract','cq','cq.quotation_id=q.quotation_id', 'INNER');
        $q->addWhere('cq.contract_id='.intval($contract_id));
        $q->addWhere('q.quotation_status='.intval($status));
        $q->addWhere('q.quotation_id NOT IN ('.implode(',', $quotation_id_arr).')');
        
        return $q->loadList();
    }
    
    /**
     * Tinh total cua 1 quotation
     * @param type $quotation_id
     * @return type
     */
    public function calculateTotalQuotation($quotation_id)
    {
        $revison_last_id = $this->get_quotation_revision_lastest($quotation_id);
        $totalItem = $this->get_quotation_item_total($quotation_id, $revison_last_id);
        $total = $this->get_total_tax_and_paid($revison_last_id, $totalItem);
        
        return $total;
    }
    
    /**
     * Tinh total quotation theo contract
     * @param type $contract_id
     */
    public function calculateTotalQuotationByContract($contract_id)
    {
        $contract_quotation_arr = $this->getQuotationAcceptedByContract($contract_id);
        $total = 0;
        foreach ($contract_quotation_arr as $contract_quotation_item) {
            $quotation_id = $contract_quotation_item['quotation_id'];
            $total += $this->calculateTotalQuotation($quotation_id);
        }
        return $total;
    }
    
    /**
     * Lay danh sach cac quotation da convert sang Invoice
     * @return type
     */
    public function getQuotationIdConvertInvoice()
    {
        $q = new DBQuery();
        $q->addTable('sales_quotation','q');
        $q->addQuery('q.quotation_id');
        $q->addJoin('sales_invoice', 'i', 'i.quotation_id=q.quotation_id','INNER');
        
        return $q->loadList();
        
    }
    
    public function listQuotationDepartment($status=false,$dept_id=false,$date_from=false,$date_to=false)
    {
        $q = new DBQuery();
        $q->addTable('sales_quotation','tbl1');
        $q->addJoin('clients', 'tbl2', 'tbl2.company_id = tbl1.customer_id');
        $q->addJoin('addresses', 'tbl3','tbl3.address_id = tbl1.job_location_id');
        //$q->addJoin('sales_quotation_revision', 'tbl4', 'tbl4.quotation_id=tbl1.quotation_id');
        $q->addQuery('tbl1.*,tbl2.company_name,tbl3.*');
        
        if($status!="-1")
            $q->addWhere('tbl1.quotation_status='.intval($status));
        if($date_from)
            $q->addWhere ('tbl1.quotation_date >= "'.$date_from.'"');
        if($date_to)
            $q->addWhere ('tbl1.quotation_date <= "'.$date_to.'"');
        if($dept_id)
            $q->addWhere ('tbl1.department_id='.intval ($dept_id));
        
        return $q->loadList();
    }
    public function calculateTotalQuotationByDepartment($dept_id =false,$status = false,$date_from=false,$date_to=false)
    {
        
        $quotation = new CQuotationManager();
        $quotationDepartment = $quotation->listQuotationDepartment($status,$dept_id,$date_from,$date_to);

       
        $total = 0;
        foreach($quotationDepartment as $data)
        {
            $quotation_id = $data['quotation_id'];
            $total += $this->calculateTotalQuotation($quotation_id);
        }
        return $total;
    }



}
?>
