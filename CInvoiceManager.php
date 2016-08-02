<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once 'CInvoice.php';
require_once 'CInvoiceItem.php';
require_once 'CInvoiceRevision.php';
require_once 'CQuotationInvoiceHistory.php';

/**
 * XXX detailed description
 *
 * @author    XXX
 * @version   XXX
 * @copyright XXX
 */
class CInvoiceManager {
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
    
    public function list_invoice($customer_id=false, $status_id=false,$order_date=false, $date_from=false, $date_to=false, $address_id = false, $contract_no=false,$invoice_no=false,$invoice_id=false) {

//        if ($user_id != '' && $user_id != null) {
//            $where = 'tbl1.user_id = '.intval($user_id);
//            if ($customer_id != '' && $customer_id != null)
//                $where .= ' AND tbl1.customer_id = '.intval($customer_id);
//            if ($status_id != '' && $status_id != null)
//                $where .= ' AND tbl1.invoice_status = '.intval($status_id);

            $q = new DBQuery();
            $q->addTable('sales_invoice', 'tbl1');
            $q->addQuery('tbl1.*, tbl3.company_name');
            //$q->addQuery('tbl1.*, tbl3.company_name, tbl2.invoice_revision, tbl2.invoice_revision_id');
            //$q->addJoin('sales_invoice_revision', 'tbl2', "tbl2.invoice_id = tbl1.invoice_id");
            $q->addJoin('clients', 'tbl3', "tbl3.company_id = tbl1.customer_id");
            if($customer_id != '' && $customer_id != null && $customer_id)
                $q->addWhere('tbl1.customer_id = '.intval($customer_id));
            if($status_id==4)
                $q->addWhere ('tbl1.invoice_status <> 2');
            else if(($status_id==0 || $status_id) && $status_id!="")
                $q->addWhere ('tbl1.invoice_status = '.  intval($status_id));
            if($date_from)
                $q->addWhere ('tbl1.invoice_date >= "'.$date_from.'"');
            if($address_id)
                $q->addWhere ('tbl1.address_id='.intval ($address_id));
            if($date_to)
                $q->addWhere ('tbl1.invoice_date <= "'.$date_to.'"');
            if($contract_no)
            {
                require_once (DP_BASE_DIR . '/modules/sales/CContractInvoice.php');
                $CContractInvoice = new CContractInvoice();
                $contract_invoice_arr = $CContractInvoice->getInvoiceContract($contract_no);
                $invoice_id_arr = array(0=>'0');
                foreach ($contract_invoice_arr as $contract_invoice_item) {
                    $invoice_id_arr[]= $contract_invoice_item['invoice_id'];
                }

                $q->addWhere ('tbl1.invoice_id IN ('.  implode(",", $invoice_id_arr).')');
            }
            if($invoice_no)
                $q->addWhere ('tbl1.invoice_no LIKE "%'.$invoice_no.'%"');
            if($invoice_id && $invoice_id>0)
                $q->addWhere ('tbl1.invoice_id = '.$invoice_id);
            if($order_date)
                $q->addOrder('tbl1.invoice_no, tbl1.invoice_date ASC');
            else
                $q->addOrder('tbl1.invoice_no DESC , tbl1.invoice_date DESC');
            
            return $q->loadList();

//        } else {
//            return null;
//        }
        
    }

    /**
     * XXX
     * 
     * @param  CInvoice $invoice XXX
     * @return boolean XXX
     * @access public
     */
    public function add_invoice($invoiceObj) {
        $CInvoice = new CInvoice();
        $CInvoice->bind($invoiceObj);
        $CInvoice->store();
        return $CInvoice->invoice_id;
    }
                
    
    public function get_latest_invoice_revision($invoice_id=false) {
        $q = new DBQuery();
        $q->addTable('sales_invoice_revision');
        $q->addQuery('*');
        if($invoice_id)
            $q->addWhere('invoice_id = '.intval($invoice_id) . ' ORDER BY invoice_revision_id DESC  LIMIT 0, 1');
        return $q->loadList();
    }
    
    
    public function get_db_invoice_already($invoice_no) {

            $q = new DBQuery();
            $q->addTable('sales_invoice', 'tbl1');
            $q->addQuery('tbl1.*, tbl3.company_name');
            $q->addJoin('clients', 'tbl3', "tbl3.company_id = tbl1.customer_id");
            $q->addWhere('tbl1.invoice_no = "'.$invoice_no.'"');
            $rows = $q->loadList();
            if(!$rows)
                return FALSE;
            else
                return true;
    }
    
    /**
     * XXX
     * 
     * @param  CInvoiceItem $invoice_itemObj XXX
     * @return boolean XXX
     * @access public
     */
    public function add_invoice_item($invoice_itemObj) {
        $CInvoiceItem = new CInvoiceItem();        
        $CInvoiceItem->bind($invoice_itemObj);
        $CInvoiceItem->store();
        return $CInvoiceItem->invoice_item_id;        
    }

    
    public function get_invoice_revision($invoice_id) {
            $q = new DBQuery();
            $q->addTable('sales_invoice_revision', 'tb1');
            $q->addQuery('tb1.invoice_revision_id, tb1.invoice_revision');
            $q->addJoin('sales_invoice', 'tb2', 'tb1.invoice_id = tb2.invoice_id');
            $q->addWhere('tb1.invoice_id = '.intval($invoice_id));
            return $q->loadList();        
    }
    
    /**
     * XXX
     * 
     * @param  int $invoice_id XXX
     * @param  string $field XXX
     * @param  string $value XXX
     * @return boolean XXX
     * @access public
     */
    public function update_invoice($id, $no, $date, $address, $persion, $email, $phone, $customer, $attend, $our_order, $jobloCation, $term,$po_number,$sub_heading,$department_id) {
        $Invoice = new CInvoice();
        $Invoice->load($id);
        $Invoice->invoice_no = $no;
        $Invoice->invoice_date = $date;
        $Invoice->address_id = $address;
        $Invoice->invoice_sale_person = $persion;
        $Invoice->invoice_sale_person_email = $email;
        $Invoice->invoice_sale_person_phone = $phone;
        $Invoice->customer_id = $customer;
        $Invoice->attention_id = $attend;
        //$Invoice->contact_coordinator_id = $seviceCoordinter;
        $Invoice->our_delivery_order_no = $our_order;
        $Invoice->job_location_id=$jobloCation;
        $Invoice->po_number=$po_number;
        $Invoice->term=$term;
        $Invoice->sub_heading=$sub_heading;
        $Invoice->department_id=$department_id;
        return $Invoice->store();
    }
    public function update_status_invoice($id,$status_id){
        $Invoice = new CInvoice();
        $Invoice->load($id);
        $Invoice->invoice_status=$status_id;
        return $Invoice->store();
    }
    
    public function update_status_invoice_cron($id,$status_id,$cron){
        $Invoice = new CInvoice();
        $Invoice->load($id);
        $Invoice->invoice_status=$status_id;
        return $Invoice->store(false,$cron);
    }
    
    function update_invoice_no_revision($invoice_id, $rev_no, $id, $note, $tax, $condition, $tax_edit,$discount,$reference_no) {
        $Revision = new CInvoiceRevision();
        $Revision->load($id);
        $Revision->invoice_id = $invoice_id;
        $Revision->invoice_revision = $rev_no;
        $Revision->invoice_revision_notes = $note;
        $Revision->invoice_revision_tax = $tax;
        $Revision->invoice_revision_term_condition = $condition;
        $Revision->invoice_revision_tax_edit = $tax_edit;
        $Revision->invoice_revision_discount = $discount;
        $Revision->reference_no = $reference_no;
        return $Revision->store();
        
    }
    
    public function updateInvoice_Revision($invoice_rev_id, $invoice_no, $revesion_arr) {     
        $CInvoiceRevision = new CInvoiceRevision();
        $CInvoiceRevision->load($invoice_rev_id);
        $CInvoiceRevision->invoice_revision = $invoice_no.'-'.$revesion_arr;
        $msg = $CInvoiceRevision->store();
        return $msg;
    }

    /**
     * XXX
     * 
     * @param  int $invoice_revision_id XXX
     * @param  array $invoice_item_deleted_arr XXX
     * @param  array $invoice_item_edited_id_arr XXX
     * @return int XXX
     * @access public
     */
    public function update_invoice_revision($invoice_revision_id, $invoice_item_deleted_arr = array(), $invoice_item_edited_id_arr = array()) {

        global $AppUI;
        
        if ($_POST['status_rev'] == 'update') {
            $invoice_id = $_POST['invoice_id'];
                    $invoice_revision_arr = $this->get_latest_invoice_revision($invoice_id);
            $invoice_revision = $invoice_revision_arr[0]['invoice_revision'];
            if(isset($_POST['invoice_no'])) {
//                $quotation_revision_arr = $this->get_db_quotation_revsion($quotation_revision_id);
//                foreach ($quotation_revision_arr as $quotation_revision_arr1) {
                    $_POST['invoice_revision'] = CSalesManager::create_invoice_revision($invoice_revision, null, $_POST['invoice_no']);
//                }
                
            } else {
                $_POST['invoice_revision'] = CSalesManager::create_invoice_revision($invoice_revision, null);
            }
            $_POST['invoice_revision_id'] = 0; // gan invoice_revision_id = 0 de thuc hien add
            $invoice_revision_id_new = $this->add_invoice_revision($_POST); // add sang invoice_revision moi
        } else {
            $invoice_revision_arr = $this->get_db_invoice_revsion($invoice_revision_id); // lay ra toan bo ban ghi cua revision cu
            $invoice_id = $invoice_revision_arr[0]['invoice_id'];
            $invoice_revision = $invoice_revision_arr[0]['invoice_revision'];
            $invoice_revision_arr[0]['invoice_revision'] = CSalesManager::create_invoice_revision($invoice_revision);
            //$invoice_revision_arr[0]['invoice_revision'] = $this->create_invoice_revision($invoice_id, $invoice_revision); // thay doi invoice_revision nay
            $invoice_revision_arr[0]['invoice_revision_id'] = 0; // thay doi invoice_revision nay
            $invoice_revision_id_new = $this->add_invoice_revision($invoice_revision_arr[0]); // add sang invoice_revision moi
        }
            //print_r($invoice_revision_id_new);
            //print_rD($invoice_revision_arr);
        if ($invoice_item_deleted_arr) { // neu truong hop xoa item kem theo cac ban ghi duoc xoa.
            $invoice_revision_item_arr = $this->get_db_invoice_item($invoice_id, $invoice_revision_id); // lay ra toan bo ban ghi cua revision cu
            if (count($invoice_revision_item_arr) > 0) {
                foreach ($invoice_revision_item_arr as $invoice_revision_item) {
                    if (!in_array($invoice_revision_item['invoice_item_id'], $invoice_item_deleted_arr)) { // kiem tra invoice_item_id co trong mang duoc xoa thi k copy
                        $invoice_revision_item['invoice_item_id'] = 0;
                        $invoice_revision_item['invoice_revision_id'] = $invoice_revision_id_new;
                        $this->add_invoice_item($invoice_revision_item);
                    }
                }
            }
        } else {
            if ($invoice_item_edited_id_arr) { // Truong hop nay khi nhan save all co inline add va inline edit
                $invoice_revision_item_arr = $this->get_db_invoice_item($invoice_id, $invoice_revision_id); // lay ra toan bo ban ghi cua revision cu
                if (count($invoice_revision_item_arr) >= 0) {                    
                    foreach ($invoice_revision_item_arr as $invoice_revision_item) {
                        $in_array_edit = in_array($invoice_revision_item['invoice_item_id'], $invoice_item_edited_id_arr);
                        if ($in_array_edit == false) { // neu id duoc lap qua khong co trong mang post
                            $invoice_revision_item['invoice_item_id'] = 0;
                            $invoice_revision_item['invoice_revision_id'] = $invoice_revision_id_new;
                            $this->add_invoice_item($invoice_revision_item);
                        } else { // thuc hien add cac ban ghi duoc edit
                            $key = array_search($invoice_revision_item['invoice_item_id'], $invoice_item_edited_id_arr); // tim ra key cua mang can tim
                            $invoice_revision_item_edit = array();
                            $invoice_revision_item_edit['invoice_item_id'] = 0;
                            $invoice_revision_item_edit['invoice_revision_id'] = $invoice_revision_id_new;
                            $invoice_revision_item_edit['invoice_id'] = $_POST['invoice_id'];
                            $invoice_revision_item_edit['user_id'] = $AppUI->user_id;
                            $invoice_revision_item_edit['invoice_item'] = $_POST['invoice_item'][$key];
                            $invoice_revision_item_edit['invoice_item_price'] = $_POST['invoice_item_price'][$key];
                            $invoice_revision_item_edit['invoice_item_quantity'] = $_POST['invoice_item_quantity'][$key];
                            $invoice_revision_item_edit['invoice_item_discount'] = $_POST['invoice_item_discount'][$key];
                            $invoice_revision_item_edit['invoice_item_type'] = 0;
                            $invoice_revision_item_edit['invoice_item_notes'] = null;
                            $this->add_invoice_item($invoice_revision_item_edit);
                            
                            unset($invoice_item_edited_id_arr[$key]); // xoa di phan tu trong mang $invoice_item_edited_id_arr
                            
                        }
                    }
                    
                    for ($j = 0; $j < count($invoice_item_edited_id_arr); $j++) { // thuc hien nhiem vu add nhung ban ghi moi
                        if (intval($invoice_item_edited_id_arr[$j]) == 0) { // chi nhung ban ghi item co gia tri = 0 moi duoc add
                            $invoice_revision_item_add = array();
                            $invoice_revision_item_add['invoice_item_id'] = 0;
                            $invoice_revision_item_add['invoice_revision_id'] = $invoice_revision_id_new;
                            $invoice_revision_item_add['invoice_id'] = $_POST['invoice_id'];
                            $invoice_revision_item_add['user_id'] = $AppUI->user_id;
                            $invoice_revision_item_add['invoice_item'] = $_POST['invoice_item'][$j];
                            $invoice_revision_item_add['invoice_item_price'] = $_POST['invoice_item_price'][$j];
                            $invoice_revision_item_add['invoice_item_quantity'] = $_POST['invoice_item_quantity'][$j];
                            $invoice_revision_item_add['invoice_item_discount'] = $_POST['invoice_item_discount'][$j];
                            $invoice_revision_item_add['invoice_item_type'] = 0;
                            $invoice_revision_item_add['invoice_item_notes'] = null;
                            $this->add_invoice_item($invoice_revision_item_add);
                        }
                    }
                    
                 }
            } else { // Truong hop nay khi nhan save all nhung khong co inline add
                $invoice_revision_item_arr = $this->get_db_invoice_item($invoice_id, $invoice_revision_id); // lay ra toan bo ban ghi item cua revision cu
                if (count($invoice_revision_item_arr) > 0) {
                    foreach ($invoice_revision_item_arr as $invoice_revision_item) {
                        $invoice_revision_item['invoice_item_id'] = 0;
                        $invoice_revision_item['invoice_revision_id'] = $invoice_revision_id_new;
                        $this->add_invoice_item($invoice_revision_item);
                    }
                }
            }
        }
            return $invoice_revision_id_new;
    }
    
    public function add_invoice_item_edit_($invoice_id,$invoice_revision_id,$inv_item, $price, $quantity, $discount) {
        global $AppUI;
    
        if (($count = count($inv_item)) > 0) {

            $item_obj = array();

                $item_obj['user_id'] = $AppUI->user_id;
                $item_obj['invoice_id'] = $invoice_id;
                $item_obj['invoice_revision_id'] = $invoice_revision_id;

            for ($i = 0; $i < $count; $i++) {
                $item_obj['invoice_item_id'] = 0;
                $item_obj['invoice_item'] = $inv_item;
                $item_obj['invoice_item_price'] = $price;
                $item_obj['invoice_item_quantity'] = $quantity;
                $item_obj['invoice_item_discount'] = intval($discount);
                $item_obj['invoice_item_type'] = 0;
                $item_obj['invoice_item_notes'] = null;
                $this->add_invoice_item($item_obj);
            }
        }
    }
    

    /**
     * XXX
     * 
     * @param  CInvoiceRevision $invoice_revisionOBj XXX
     * @return boolean XXX
     * @access public
     */
    public function add_invoice_revision($invoice_revisionOBj) {
        $CInvoiceRevision = new CInvoiceRevision();
        $CInvoiceRevision->bind($invoice_revisionOBj);
        $CInvoiceRevision->store();
        return $CInvoiceRevision->invoice_revision_id;
    }

    /**
     * XXX
     * 
     * @param  int $invoice_id XXX
     * @return array XXX
     * @access public
     */
    public function get_db_invoice($invoice_id) {

            $q = new DBQuery();
            $q->addTable('sales_invoice', 'tbl1');
            $q->addQuery('tbl1.*, tbl3.company_name');
            $q->addJoin('clients', 'tbl3', "tbl3.company_id = tbl1.customer_id");
            $q->addWhere('tbl1.invoice_id = '.intval($invoice_id));
            return $q->loadList();
            

        //$CInvoice = new CInvoice();
          //  $CInvoice->load($invoice_id);
            //return $CInvoice;
    }
    
    public function get_list_invoice() {

            $q = new DBQuery();
            $q->addTable('sales_invoice','tbl1');
            $q->addQuery('tbl1.*');
            return $q->loadList();
            

        //$CInvoice = new CInvoice();
          //  $CInvoice->load($invoice_id);
            //return $CInvoice;
    }

    /**
     * XXX
     * 
     * @param  int $invoice_revision_id XXX
     * @return array XXX
     * @access public
     */
    public function get_db_invoice_revsion($invoice_revision_id) {
        
            $q = new DBQuery();
            $q->addTable('sales_invoice_revision', 'tbl1');
            $q->addQuery('tbl1.*');
            $q->addWhere('invoice_revision_id = '.intval($invoice_revision_id));
            return $q->loadList();
            
    }
    
    /**
     * XXX
     *
     * @param  int $invoice_id XXX
     * @param  int $invoice_revision_id XXX
     * @return array XXX
     * @access public
     */
    public function get_db_invoice_item($invoice_id, $invoice_revision_id) {
            $q = new DBQuery();
            $q->addTable('sales_invoice_item', 'tbl1');
            $q->addQuery('tbl1.*');
            $q->addWhere('invoice_id = '.intval($invoice_id));
            $q->addWhere('invoice_revision_id = '.intval($invoice_revision_id));
            $q->addOrder('invoice_order ASC');
            return $q->loadList();
//            $CInvoiceItem = new CInvoiceItem();
//            return $CInvoiceItem->loadAll('invoice_order ASC, invoice_item_id DESC', 'invoice_id = '. intval($invoice_id) .' AND invoice_revision_id = '. intval($invoice_revision_id));
    }

    /**
     * XXX
     * 
     * @param  int $invoice_id_arr XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_invoice($invoice_id_arr) { // format nhu sau: $invoice_id_arr = array(1, 2, 5, 6, 9);
        $CInvoice = new CInvoice();
        $CInvoiceItem = new CInvoiceItem();
        $CInvoiceRevision = new CInvoiceRevision();
        $CInvoiceAtt = new CSalesAttention();
        
        if (($count = count($invoice_id_arr)) > 0) {
            for ($i = 0; $i < $count; $i++) {
                $res1 = $CInvoice->store_delete(null, $invoice_id_arr[$i]);
                $res2 = $CInvoiceItem->store_delete('invoice_id', $invoice_id_arr[$i]);
                $res3 = $CInvoiceRevision->store_delete('invoice_id', $invoice_id_arr[$i]);
                $res4 = $CInvoiceAtt->store_delete('sales_type_id',$invoice_id_arr[$i],'invoice');
            }
        }
        if($res1 && $res2 && $res3 && $res4)
            return true;
        return false;
    }

    /**
     * XXX
     *
     * @param  int $invoice_revision_id_arr XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_invoice_revision($invoice_revision_id_arr) { // format nhu sau: $invoice_revision_id_arr = array(1, 2, 5, 6, 9);
        $CInvoiceItem = new CInvoiceItem();
        $CInvoiceRevision = new CInvoiceRevision();
        if (($count = count($invoice_revision_id_arr)) > 0) {
            $where = 'invoice_revision_id = '. $invoice_revision_id_arr[0];
            for ($i = 0; $i < $count; $i++) {
                $res = $CInvoiceRevision->store_delete(null,$invoice_revision_id_arr[$i]);
                $res1 = $CInvoiceItem->store_delete('invoice_id',$invoice_revision_id_arr[$i]);
            }
        }
        if ($res && $res1)
            return true;
        return false;   
    }

    /**
     * XXX
     *
     * @param  int $invoice_item_id_arr XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_invoice_item($invoice_item_id_arr) { // format nhu sau: $invoice_item_id_arr = array(1, 2, 5, 6, 9);
        $cinvoiceItem = new CInvoiceItem();
        if (($count = count($invoice_item_id_arr)) > 0) {
            for ($i = 0; $i < $count; $i++) {
                $res = $cinvoiceItem->store_delete(null,$invoice_item_id_arr[$i]);
            }
        }
        if($res)
            return true;
        return false;
    }

    /**
     * XXX
     * 
     * @param  array $invoice_id_array XXX
     * @param  array $invoice_revision_id_array XXX
     * @return boolean XXX
     * @access public
     */
//    public function send_mail_invoice_revision($invoice_id_arr, $invoice_revision_id_arr = null) { // format nhu sau: $invoice_id_array = array(1, 2, 5, 6, 9); $invoice_revision_id_array tuong tu
//        
//        global $AppUI;
//
//        $content = 'chua config noi dung';
//
//        require_once($AppUI->getSystemClass( 'libmail' ));
//        $mail = new Mail; // create the mail
//        $mail->From('$email_to');
//        //print_r($invoice_id_arr);
//        //sprint_r($invoice_id_arr);
//        if (($count = count($invoice_id_arr)) > 0) {
//            for ($i = 0; $i < $count; $i++) {
//
//                $invoice_id = $invoice_id_arr[$i];
//                if ($invoice_revision_id_arr != null)
//                    $invoice_revision_id = $invoice_revision_id_arr[$i];
//                else
//                    $invoice_revision_id = $this->get_invoice_revision_lastest($invoice_id);
//                
//                $mail->To(CSalesManager::get_customer_email($invoice_id));
//                $mail->Subject( "Tiêu đề email" );
//                $mail->Body($content);
//                $mail->Attach($this->create_invoice_revision_pdf_file($invoice_id, $invoice_revision_id, true), 'application/pdf', 'inline', 'invoice.pdf');
//                $mail->Send();
//                
//            }
//        }
//        
//        return true;
//        //echo $mail->Get(); // show the mail source
//    }
    
//    public function send_mail_invoice_revision($invoice_id_arr, $invoice_revision_id_arr = null, $content, $sender, $subject, $reciver) { // format nhu sau: $invoice_id_array = array(1, 2, 5, 6, 9); $invoice_revision_id_array tuong tu
//        
//        global $AppUI;
//        require_once($AppUI->getSystemClass( 'libmail' ));
//        
//        if (($count = count($invoice_id_arr)) > 0) {
//            for ($i = 0; $i < $count; $i++) {
//
//                $invoice_id = $invoice_id_arr[$i];
//                if ($invoice_revision_id_arr != null)
//                   $invoice_revision_id = $invoice_revision_id_arr[$i];
//                else
//                    $invoice_revision_id = $this->get_invoice_revision_lastest($invoice_id_arr);
//                
//        $mail = new Mail; // create the mail
//        //$content = 'chua config noi dung';
//        //$from = CSalesManager::get_customer_email_quo($invoice_id_arr);
//        $mail->From($sender);                
//        $mail->To($reciver);
//        $mail->Subject($subject);
//        $mail->Body($content);
//        $mail->Attach($this->create_invoice_revision_pdf_file($invoice_id_arr, $invoice_revision_id_arr, true), 'application/pdf', 'inline', 'invoice.pdf');
//        $mail->Send();
//            }
//        }
//        return true;
//        //echo $mail->Get(); // show the mail source
//    }
    
    public function send_mail_invoice_revision($invoice_id_arr, $invoice_revision_id_arr = null, $content, $sender, $subject, $reciver) { // format nhu sau: $invoice_id_array = array(1, 2, 5, 6, 9); $invoice_revision_id_array tuong tu
        
        global $AppUI;
        require_once './lib/swift/swift_required.php';
        
        if (($count = count($invoice_id_arr)) > 0) {
            for ($i = 0; $i < $count; $i++) {

                $invoice_id = $invoice_id_arr[$i];
                if ($invoice_revision_id_arr != null)
                   $invoice_revision_id = $invoice_revision_id_arr[$i];
                else
                    $invoice_revision_id = $this->get_invoice_revision_lastest($invoice_id_arr);
                
                    
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

                    $attachment = Swift_Attachment::newInstance($this->create_invoice_revision_pdf_file($invoice_id_arr, $invoice_revision_id_arr, true), 'invoice.pdf', 'application/pdf');
                    $mail->attach($attachment);

                    // Send the message
                    $result = $mailer->send($mail);
            }
        }
        return true;
        //echo $mail->Get(); // show the mail source
    }
    
//     //in pdf - anhnn
//     public function create_invoice_revision_pdf_file($invoice_id_arr, $invoice_revision_id_arr, $is_send_mail = false) {
//        require_once (DP_BASE_DIR . '/modules/sales/CHeaderInvoice.php');
//        //require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
//        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
//        require_once (DP_BASE_DIR . "/modules/contacts/contacts_manager.class.php");
//        require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
//        $cconfig = new CConfigNew();
//        
//        $ContactManager = new ContactManager();
//        
//        $pdf = new MYPDFInvoice(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//        $pdf->SetCreator(PDF_CREATOR);
//        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//        //$pdf->SetMargins(10,130, 6,-70);
//        $pdf->setCellMargins(0, 0, 0, 0);
//        $pdf->SetHeaderMargin(3);
//        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//        $pdf->setFooterMargin(-3);
//        $pdf->SetAutoPageBreak(true, 75);
//        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//        $pdf->SetPrintHeader(true);
//        $pdf->setPrintFooter(true);
//        $pdf->setLanguageArray($l);
//        $pdf->SetFont('helvetica', '', 10.3);
//        
//        
//        if ($invoice_revision_id_arr == null) {
//            if (($count = count($invoice_id_arr)) > 0) {
//                for ($i = 0; $i < $count; $i++) {
//                    $invoice_id = $invoice_id_arr[$i];
//                    $invoice_revision_id = $this->get_invoice_revision_lastest($invoice_id);
//                }
//            }
//        } else {
//             $invoice_id = $invoice_id_arr;
//             $invoice_revision_id = $invoice_revision_id_arr;   
//       }
//       
//       
//        $invoice_details_arr = $this->get_db_invoice($_REQUEST['invoice_id']);
//        $job_location_id = $invoice_details_arr[0]['job_location_id'];
//        $subject = $invoice_details_arr[0]['invoice_subject'];
//        $invoice_co = $invoice_details_arr[0]['invoice_CO'];
//        $address_id = $invoice_details_arr[0]['address_id'];
//        $attention_id = $invoice_details_arr[0]['attention_id'];
//        
//        $margin_header =74;
//        
//        
//        // Address
//        $address = false;
//        $client_address =  CSalesManager::get_address_by_id($address_id);
//        if($client_address['address_street_address_2']){
//            $margin_header+=4;
//            $address = true;
//        }
//        if($client_address['address_branch_name'] ){
//            $margin_header+=4;
//            $address = true;
//            
//        }
//        if($client_address['address_phone_1'] || $client_address['address_fax']){
//            $margin_header+=4;
//             $address = true;
//        }
//        
//        // C/O
//        if(!$invoice_co && $address==true){
//            $margin_header-=4;
//        }
//        
//        $quo_attention_arr = CSalesManager::get_salesAttention_by_SalesType($invoice_id, "invoice");
//        $count_inv_attention = count($quo_attention_arr);
//        $email_rr = CSalesManager::get_attention_email($attention_id);
//        if($count_inv_attention>0){
//            foreach ($quo_attention_arr as $quo_attention_row) {
//                $email_rr = CSalesManager::get_attention_email($quo_attention_row['attention_id']);
//                if(count($email_rr)){
//                    $margin_header+=5;
//                }
//            }
//            $margin_header+=$count_inv_attention*5;
//        }
//        else if($attention_id){
//           if(count($email_rr)>0)
//                $margin_header+=5;
//           $margin_header+=10;
//        }
//        
//
//        
//        if($margin_header)
//        $job_location_arr =  CSalesManager::get_address_by_id($job_location_id);
//        if(count($job_location_arr)>0){
//            $margin_header+=6;
//        }
//        
//        if($subject!="")
//            $margin_header +=13;
//        
//        // Localhost
//        $contact_arr = CSalesManager::get_attention_email($job_location_id);
//        if(count($contact_arr))
//            $margin_header += 10;
//        
//        
//        $pdf->SetMargins(10,$margin_header, 8, -15);
//        
//       //In ra so tien da tra
//       require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");
//        $CPaymentManager = new CPaymentManager();
//        $paymentDetail_arr = $CPaymentManager->lis_db_payment_detail($invoice_revision_id);
//        $tr_paid = 0;
//        if(count($paymentDetail_arr)>0){
//            foreach ($paymentDetail_arr as $paymentDetail){
//                $paymentDetail_amount =  $paymentDetail['payment_amount'];
//                $total_paid_and_tax += floatval($paymentDetail['payment_amount']);
//                $total_paid_and_tax = round($total_paid_and_tax, 2);
//            }
//            $tr_paid = $total_paid_and_tax;
//                    
//        } // END- In ra so tien da tra
// 
//        $CTax = new CTax();
//        $tax_arr = $CTax->list_tax();
//        $currency_symb = '$'; $total_paid_and_tax = 0;
//        
//        if(is_array($invoice_id)) {
//            $invoice_id = $invoice_id[0];
//        }
////        if(is_array($invoice_revision_id)) {
////            $invoice_revision_id = $invoice_revision_id[0];
////        }
//        $invoice_details_arr = $this->get_db_invoice($invoice_id);
//        $invoice_details_arr1 = $this->get_latest_invoice_revision($invoice_id);
//
//        if (count($invoice_details_arr) > 0) {
//            $date = $invoice_details_arr[0]['invoice_date'];
//            $invoice_no = $invoice_details_arr[0]['invoice_no'];
//            $term_value = $invoice_details_arr[0]['term'];
//            $po_number = $invoice_details_arr[0]['po_number'];
//            $our_delivery = $invoice_details_arr[0]['our_delivery_order_no'];      
//
//            $sale_person_name = $invoice_details_arr[0]['invoice_sale_person'];
//            $sale_person_email = $invoice_details_arr[0]['invoice_sale_person_email'];
//            $sale_person_phone = $invoice_details_arr[0]['invoice_sale_person_phone'];
//            $sale_coordinator_id = $invoice_details_arr[0]['contact_coordinator_id'];
//
//            $client_id = $invoice_details_arr[0]['customer_id'];
//            $address_id = $invoice_details_arr[0]['address_id'];
//            $attention_id = $invoice_details_arr[0]['attention_id'];
//            $contact_id= $invoice_details_arr[0]['contact_id'];
//            $job_location_id = $invoice_details_arr[0]['job_location_id'];
//            $subject = $invoice_details_arr[0]['invoice_subject'];
//            $invoice_co = $invoice_details_arr[0]['invoice_CO'];
//
//        }
//        $invoice_rev = $invoice_details_arr1[0]['invoice_revision'];
//
//        
//    $supplier_arr = CSalesManager::get_supplier_info();
//    if (count($supplier_arr) > 0) {
//        $sales_owner_name = $supplier_arr['sales_owner_name'];
//        $sales_owner_address =$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'];
//        //$sales_owner_address += 
//        $phone =$supplier_arr['sales_owner_phone1'];
//        $fax = $supplier_arr['sales_owner_fax'];
//        $email =$supplier_arr['sales_owner_email'];
//        $web = $supplier_arr['sales_owner_website'];
//        $sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
//        $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
//    }
//    
//    $supplier = '<tr><td><b>'. CSalesManager::htmlChars($sales_owner_name) .'</b></td></tr>';
//    $supplier.= '<tr><td>'.CSalesManager::htmlChars($sales_owner_address).'</td></tr>';
//    $supplier .= '<tr><td>Tel: '.$phone.', Fax: '.$fax.'</td></tr>';
////    $supplier .= '<tr><td>Fax: '.$fax.'</td></tr>';
//    $supplier .= '<tr><td>GST Reg No: '. CSalesManager::htmlChars($sales_owner_gst_reg_no) .'</td></tr>';
////    $supplier.= '<tr><td>Reg No: '. $supplier_arr['sales_owner_postal_code'] .'</td></tr>';
//    $supplier .= '<tr><td>Email: '.CSalesManager::htmlChars($email).'</td></tr>';
//    $supplier .= '<tr><td>Website: '.$web.'</td></tr>';
//
//
//    $dis_label ="";
//    $dis_label="";
//    $a = '62%';
//    $invoice_item_arr = $this->get_db_invoice_item($invoice_id, $invoice_revision_id);
//    $booldis=0;
//    foreach ($invoice_item_arr as $invoice_item1) {
//        if($invoice_item1['invoice_item_discount']!=0){
//            $booldis=1;
//        }
//    }
//    if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
//        $tr_item = ''; $i = 1;
//        foreach ($invoice_item_arr as $invoice_item) {
//            if ($invoice_item['invoice_item_type'] == 0) {
//                $item = $invoice_item['invoice_item'];
//            } elseif ($invoice_item['invoice_item_type'] == 1) {
//                    $item = 'Ipad<br><font color="#AAAAAA">description</font>';
//            } elseif ($invoice_item['invoice_item_type'] == 2) {
//                $item = 'Ipad<br><font color="#AAAAAA">description</font>';
//            } else {
//                    $item = '<font color="red">Item type not found.</font>';
//            }
//            if($booldis==1){
//                $dis_label='<td width="10%" align="center" ><b>Discount<br>(%)</b></td>';
//                $dis_value='<td width="10%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
//                $a = '52%';
//            }
//
//            $tr_item .= '<tr>
//                <td align="center" width="4%">'. $i .'</td>
//                <td width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
//                <td width="7%" align="center">'. $invoice_item['invoice_item_quantity'] .'</td>
//                <td width="13%" align="right">$'. number_format($invoice_item['invoice_item_price'],2) .'</td>                                
//                '.$dis_value.'
//                <td width="13%" align="right">$'. number_format(CSalesManager::calculate_total_item($invoice_item['invoice_item_price'], $invoice_item['invoice_item_quantity'], $invoice_item['invoice_item_discount']),2) .'</td>
//                </tr>';
//                $i++;
//        }
//    }
//
//    $total_item_show = $this->get_invoice_item_total($invoice_id, $invoice_revision_id);
//
//    $invoice_rev_details_arr = $this->get_latest_invoice_revision($invoice_id);
//
//    if (count($invoice_rev_details_arr) > 0) {
//        if($invoice_rev_details_arr[0]['invoice_revision_notes']!="")
////            $note_area ='<b>Notes:</b> '.$invoice_rev_details_arr[0]['invoice_revision_notes'];
//            $note_area='<table width="100%" border="0" cellspacing="0" cellpadding="4">
//                <tr><td></td></tr>
//                <tr valign="top">
//                <td><b>Notes:</b> '.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_notes']) .'</td>
//             </tr></table>';
//        if($invoice_rev_details_arr[0]['invoice_revision_term_condition']!="")
////            $term_area ='<b>Terms and Conditions:</b> '.$invoice_rev_details_arr[0]['invoice_revision_term_condition'];
//            $term_area = '<tr><td height="10"></td></tr>
//                          <tr>
//                            <td width="98%" ><b>Terms and Conditions:<br/></b> '.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_term_condition']) .'</td>
//                            <td></td>
//                          </tr>';
//        $tax_id = $invoice_rev_details_arr[0]['invoice_revision_tax'];
//    }
//
//    
//   $tax_option = '<option vaue="">---</option>';
//    if (count($tax_arr) > 0) {
//        foreach ($tax_arr as $tax) {
//            $tax_name = $tax['tax_name'];
//            $tax_value =0;
//            $selected = '';
//            if ($tax_id) { // neu tinh trang la update
//                if ($tax['tax_id'] == $tax_id) {
//                    $selected = 'selected="selected"';
//                    $tax_value = $tax['tax_rate'];
//                }
//            } else { // neu tinh trang la add lay ra ta default
//                if ($tax['tax_default'] == 1) {
//                    $selected = 'selected="selected"';
//                    $tax_value = $tax['tax_rate'];
//                }
//            }
//
//            $tax_option .= '<option value="'. $tax['tax_id'] .'" '. $selected .'>'. $tax['tax_rate'] .'</option>';
//        }
//    }
//    $total_item_tax_show = 0;
//    if ($tax_id) {
//        $caculating_tax = (floatval($total_item_show) * floatval($tax_value) / 100);
//        $total_item_tax_show =CSalesManager::round_up($caculating_tax);
//        if($invoice_rev_details_arr[0]['invoice_revision_tax_edit']!=0)
//            $total_item_tax_show = $invoice_rev_details_arr[0]['invoice_revision_tax_edit'];
//    }
//$amount_due =  $total_item_show + $total_item_tax_show - $tr_paid;
//    $tax_select = '<select name="invoice_revision_tax" id="invoice_revision_tax" class="text">'. $tax_option .'</select>';
//
//$tbl ='<table border="1" style="border-style:solid;" class="tbl" cellspacing="0" cellpadding="4">
//            <thead>
//               <tr>
//                    <td width="4%" align="center" height="35" ><b> #</b></td>
//                    <td width="'.$a.'" height="30"><b> Item</b></td>
//                    <td width="7%" align="center" ><b> Qty</b></td>
//                    <td width="13%" align="center" ><b> Unit Price</b></td>
//                    '.$dis_label.'
//                    <td width="13%" align="center" ><b> Amount</b></td>
//               </tr>
//            </thead>
//                    '. $tr_item .'
//            </table>
//            <table border="0" cellspacing="0" cellpadding="0">
//                <tr>
//                    <td width="53%">'.$note_area.'</td>
//                    <td>
//                            <table border="0" cellspacing="0" cellpadding="3">
//                                <tr align="right">
//                                    <td idth="20%"></td>
//                                    <td width="30%"><b>Total:</b></td>
//                                    <td width="28%" align="right"><b>$'.number_format($total_item_show,2).'</b></td>
//                                </tr>
//                                <tr align="right">
//                                    <td></td>
//                                    <td><b>'.$tax_name.'&nbsp;&nbsp;'.$tax_value.'%:</b></td>
//                                    <td align="right"><b>$'.number_format($total_item_tax_show,2).'</b></td>
//                                </tr>
//                                <tr align="right">
//                                    <td ></td>
//                                    <td ><b>Amount Paid: </b></td>
//                                    <td align="right"><b>$'. number_format($tr_paid,2) .'</b></td>
//                                </tr>
//                                <tr align="right">
//                                    <td></td>
//                                    <td><b>Amount Due:</b></td>
//                                    <td align="right"><b>$'.number_format(round($amount_due,2),2).'</b></td>
//                                </tr>
//                            </table>
//                     </td>
//                </tr>
//                <tr><td height="10"></td></tr>
//                <tr>
//                    <td colspan="2">
//                        <table width="99%"  cellspacing="0" cellpadding="0">
//                            '.$term_area.'
//                        </table>
//                    </td>
//                </tr>  
//</table>';
//
//            $pdf->AddPage('P', 'A4');
//            $pdf->writeHTML($tbl, true, false, false, false, '');
//            
//            ob_end_clean();
//            if (!$is_send_mail)
//                //$pdf->Output('ddd.pdf', 'I');
//            $pdf->Output($invoice_rev .'.pdf', 'I');
//            else
//                return $pdf->Output('', 'S'); // tao ra chuoi document, ten bo qua
//        
//    }
    
    public function create_invoice_revision_pdf_file($invoice_id_arr, $invoice_revision_id_arr, $is_send_mail = false, $dir=false) {
        global $ocio_config;
        require_once (DP_BASE_DIR . '/modules/sales/CHeaderInvoice.php');
        //require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        require_once (DP_BASE_DIR . "/modules/contacts/contacts_manager.class.php");
        require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
        require_once(DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
        require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
        $CTemplatePDF = new CTemplatePDF();
        $CInvoiceManager = new CInvoiceManager();
        require_once(DP_BASE_DIR."/modules/sales/CSalesManager.php");
        $CSalesManager = new CSalesManager();
        $cconfig = new CConfigNew();
        
        $ContactManager = new ContactManager();
        
        $pdf = new MYPDFInvoice(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //$pdf->SetMargins(10,130, 6,-70);
        $pdf->setCellMargins(0, 0, 0, 0);
        $pdf->SetHeaderMargin(3);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->setFooterMargin(-3);
        $pdf->SetAutoPageBreak(true, 45);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(true);
        $pdf->setPrintFooter(true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(20,29, 12, -15);
        
        // active default Template pdf
        $template_pdf_1 = $CSalesManager->get_template_pdf(1);
        $template_pdf_2 = $CSalesManager->get_template_pdf(2);
        $template_pdf_3 = $CSalesManager->get_template_pdf(3);
        $template_pdf_4 = $CSalesManager->get_template_pdf(4);
        $template_pdf_5 = $CSalesManager->get_template_pdf(5);
        $template_pdf_6 = $CSalesManager->get_template_pdf(6);
        
        $template_pdf_5_array = $CTemplatePDF->get_template_pdf(5);
        $template_pdf_1_array = $CTemplatePDF->get_template_pdf(1);
        // end active default Template pdf
        
        
        if ($invoice_revision_id_arr == null) {
            if (($count = count($invoice_id_arr)) > 0) {
                for ($i = 0; $i < $count; $i++) {
                    $invoice_id = $invoice_id_arr[$i];
                    $invoice_revision_id = $this->get_invoice_revision_lastest($invoice_id);
                }
            }
        } else {
             $invoice_id = $invoice_id_arr;
             $invoice_revision_id = $invoice_revision_id_arr;
       }
       /* Info Supplier */
        $supplier_arr = CSalesManager::get_supplier_info();
        if (count($supplier_arr) > 0) {
        $sales_owner_name = $supplier_arr['sales_owner_name'];
        $sales_owner_address =$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'];
        //$sales_owner_address += 
        $phone =$supplier_arr['sales_owner_phone1'];
        $fax = $supplier_arr['sales_owner_fax'];
        $email =$supplier_arr['sales_owner_email'];
        $web = $supplier_arr['sales_owner_website'];
        //$sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
        $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
        }

        $supplier = '<tr><td><b>'. CSalesManager::htmlChars($sales_owner_name) .'</b></td></tr>';
        $supplier.= '<tr><td>'.CSalesManager::htmlChars($sales_owner_address).'</td></tr>';
        $supplier .= '<tr><td>Tel: '.$phone.', Fax: '.$fax.'</td></tr>';
    //    $supplier .= '<tr><td>Fax: '.$fax.'</td></tr>';
        $supplier .= '<tr><td>GST Reg No: '. CSalesManager::htmlChars($sales_owner_gst_reg_no) .'</td></tr>';
    //    $supplier.= '<tr><td>Reg No: '. $supplier_arr['sales_owner_postal_code'] .'</td></tr>';
        $supplier .= '<tr><td>Email: '.CSalesManager::htmlChars($email).'</td></tr>';
        if($web!="")
            $supplier .= '<tr><td>Website: '.$web.'</td></tr>';
        // End 
       
        // Get LOGO sales //
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        $files = scandir($url);
        $i = count($files) -1;
        if (count($files) > 2 && $files[$i]!=".svn") {
            $path_file = $url . $files[$i];
            $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" /></td>';
        } // Get LOGO sales //
        
        $invoice_details_arr = $CInvoiceManager->get_db_invoice($invoice_id);
        
        $invocie_id = $invoice_details_arr[0]['invoice_id'];
        $date = $invoice_details_arr[0]['invoice_date'];
        $invoice_no = $invoice_details_arr[0]['invoice_no'];
        $term_value = $invoice_details_arr[0]['term'];
        $po_number = $invoice_details_arr[0]['po_number'];
        $our_delivery = $invoice_details_arr[0]['our_delivery_order_no'];
        $your_ref = $invoice_details_arr[0]['invoice_your_ref'];
        $sub_heading = $invoice_details_arr[0]['sub_heading'];

        $sale_person_name = $invoice_details_arr[0]['invoice_sale_person'];
        $sale_person_email = $invoice_details_arr[0]['invoice_sale_person_email'];
        $sale_person_phone = $invoice_details_arr[0]['invoice_sale_person_phone'];
        $sale_coordinator_id = $invoice_details_arr[0]['contact_coordinator_id'];

        $client_id = $invoice_details_arr[0]['customer_id'];
        $address_id = $invoice_details_arr[0]['address_id'];
        $attention_id = $invoice_details_arr[0]['attention_id'];
        $contact_id= $invoice_details_arr[0]['contact_id'];
        $job_location_id = $invoice_details_arr[0]['job_location_id'];
        $subject = $invoice_details_arr[0]['invoice_subject'];
        $invoice_co = $invoice_details_arr[0]['invoice_CO'];
        
        $invoice_rev_arr = $CInvoiceManager->get_db_invoice_revsion($invoice_revision_id);
        $discount=0;
        if(count($invoice_rev_arr)>0)
            $discount = $invoice_rev_arr[0]['invoice_revision_discount'];
        $invoice_rev = $invoice_rev_arr[0]['invoice_revision'];
        
        // ############Info Invoice##########
        $term_arr = dPgetSysVal('Term');
        $title_arr = dPgetSysVal('CustomerTitle');
        foreach ($term_arr as $key => $value) {
            if($key == $term_value)
                if($value==1)
                    $term = $value.' day';
                else if(is_numeric($value))
                    $term = $value.' days';
                else
                    $term = $value;
        }
        $info2 = 'Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;: '.date($ocio_config['php_abms_date_format'],  strtotime($date)).'<br>';
        if($template_pdf_1_array[0]['template_server']==1)
            $info2 .= 'Invoice Rev &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;: '.$invoice_rev.'<br>';
        if($po_number!="")
            $info2 .= 'Your P.O No&nbsp; 
            : '. CSalesManager::htmlChars($po_number).'<br>';
        if($term!="")
            $info2 .= 'Terms &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            : '. $term.'<br>';
        if($our_delivery!="")
            $info2 .= 'Our D.O No &nbsp;&nbsp;&nbsp;: '.CSalesManager::htmlChars($our_delivery).'<br>';
        if($your_ref!="")
            $info2 .= 'Our Ref &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                . ': '.CSalesManager::htmlChars($your_ref).'<br>';
        
        $info6 = 'Invoice No &nbsp;&nbsp;'
                . ': '.$invoice_no.'<br>';
        $info6 .= 'Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:'
                . ' '.date($ocio_config['php_abms_date_format'],  strtotime($date)).'<br>';
        if($po_number!="")
        $info6 .= 'Your P.O No
            : '. CSalesManager::htmlChars($po_number).'<br>';
        if($term!="")
        $info6 .= 'Terms &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            : '. $term.'<br>';
        if($our_delivery!="")
        $info6 .= 'Our D.O No&nbsp;&nbsp;: '.CSalesManager::htmlChars($our_delivery).'<br>';
        if($your_ref!="")
        $info6 .= 'Our Ref&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.CSalesManager::htmlChars($your_ref);

//        $info6 = '<table border="0" style="text-align:left;width:100%;" cellpadding="0" spacepadding="0">
//                    <tr>
//                        <td width="45%" align="right">Invoice No:</td>
//                        <td width="2%"></td>
//                        <td width="53%">'.$invoice_no.'</td>
//                    </tr>
//                    <tr>
//                        <td width="45%" align="right">Date:</td>
//                        <td width="2%"></td>
//                        <td width="53%">'.date('d-m-Y',  strtotime($date)).'</td>
//                    </tr>';
//       if($po_number!=""){
//                  $info6.='<tr>
//                        <td align="right">Your P.O No:</td>
//                        <td></td>
//                        <td>'. CSalesManager::htmlChars($po_number).'</td>
//                    </tr>';
//       }
//       if($term!="")
//       {
//                   $info6.='<tr>
//                        <td align="right">Term:</td>
//                        <td></td>
//                        <td>'.$term.'</td>
//                    </tr>';
//       }
//       if($our_delivery!="")
//       {
//                    $info6.='<tr>
//                        <td align="right">Our Delivery Order No:</td>
//                        <td></td>
//                        <td>'.CSalesManager::htmlChars($our_delivery).'</td>
//                    </tr>';
//       }
//       if($your_ref!="")
//       {
//                    $info6.='<tr>
//                    <td align="right">Our Ref:</td>
//                        <td></td>
//                        <td>'.CSalesManager::htmlChars($your_ref).'</td>
//                    </tr>';
//       }
//       $info6.='</table>';

        // End info Invocie
        
        // company name && CO
        $customer_arr = $CSalesManager->get_customer_name($client_id);
        $customer_co_arr = $CSalesManager->get_customer_name($invoice_co);

        // Get address
        $client_address =  CSalesManager::get_address_by_id($address_id);
        $phone ="";
            if (count($client_address) > 0) {
                
                if($client_address['address_phone_1']!="") 
                    $phone = '<br/>Phone: '.$client_address['address_phone_1'].'&nbsp;&nbsp;&nbsp;&nbsp;';
                elseif($client_address['address_phone_2']!="")
                    $phone = '<br/>Phone: '.$client_address['address_phone_2'].'&nbsp;&nbsp;&nbsp;&nbsp;';
                
                if($client_address['address_mobile_1']!="") 
                    $mobile = 'Mobile Phone: '.$client_address['address_mobile_1'];
                elseif($client_address['address_mobile_2']!="")
                    $mobile = 'Mobile Phone: '.$client_address['address_mobile_2'];
        
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
                $option.=$phone;
                if($client_address['address_fax']!="")
                    $option.='Fax: '.$client_address['address_fax'].'&nbsp;&nbsp;&nbsp;&nbsp;';
                if($template_pdf_5!=1)
                    $option.=$mobile;
        //        if($client_address['ddress_country']!="")
        //            $option.=$client_address['ddress_country'];
        //        $option = $client_address['address_phone_1'] .', '. $client_address['address_fax'] , '. $client_address['address_email'];
            }
        $address= $option;

        ######
         $quo_attention_arr = CSalesManager::get_salesAttention_by_SalesType($invoice_id, "invoice");
         $count_inv_attention = count($quo_attention_arr);
         $contac_titlet_arr = dPgetSysVal('ContactsTitle');

         if($count_inv_attention<=0){
          $attention_arr = CSalesManager::get_list_attention($client_id);
          $attention_option_print = array();$attention_option_print6="";
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
                $attention_option_print[] = $attention_option;
                $attention_option_print6 .= $attention_option;
           }
            //Lay Email by Attention
           $email_rr = CSalesManager::get_attention_email($attention_id);
           $email_cus="";$email_cus6="";$phone="";
           if(count($email_rr) >0){
               if($email_rr['contact_phone'])
                    $phone = "<br/>Phone: ".$email_rr['contact_phone']."&nbsp;&nbsp;&nbsp;";
               elseif($email_rr['contact_phone2'])
                    $phone = "<br/>Phone: ".$email_rr['contact_phone2']."&nbsp;&nbsp;&nbsp;";
               
               if($email_rr['contact_email']!="")
                   $email_cus = "Email: ". $email_rr['contact_email']. "&nbsp;&nbsp;&nbsp;";
               if($email_rr['contact_mobile']!="")
               {
                   $email_cus .= "Mobile: ". $email_rr['contact_mobile'];
                   $email_cus6 .= "Mobile: ". "&nbsp;&nbsp;&nbsp;";
               }
               $email_cus.=$phone;
               $email_cus6.=$phone;
               if($email_rr['contact_fax']!="")
               {
                   $email_cus6 .="Fax: ". $email_rr['contact_fax'];
               }
           }
           if($email_cus!="")
              $attention_option_print6.='<br>'.$email_cus6;
            
         }else{
              $attention_option_print = array();
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
                  // Lay Email by Attention
                  $email_cus ="";$email_cus6="";$phone="";
                  $email_rr = CSalesManager::get_attention_email($attention_id);
                  if(count($email_rr) >0){
                        if($email_rr['contact_phone'])
                             $phone = "<br/>Phone: ".$email_rr['contact_phone']."&nbsp;&nbsp;&nbsp;";
                        elseif($email_rr['contact_phone2'])
                             $phone = "<br/>Phone: ".$email_rr['contact_phone2']."&nbsp;&nbsp;&nbsp;";
                        
                        if($email_rr['contact_email']!="")
                            $email_cus = "Email: ". $email_rr['contact_email']. "&nbsp;&nbsp;";
                        if($email_rr['contact_mobile']!="")
                        {
                            $email_cus .= "Mobile: ". $email_rr['contact_mobile']. "&nbsp;&nbsp;";
                            $email_cus6 .= "Mobile: ".$email_rr['contact_mobile']. "&nbsp;&nbsp;";
                        }
                        $email_cus.=$phone;
                        $email_cus6.=$phone;
                        if($email_rr['contact_fax']!="")
                        {
                            $email_cus6 .="Fax: ". $email_rr['contact_fax'].'&nbsp;&nbsp;';
                        }
                  }
                  if($email_cus!="")
                  {
                      $attention_option.='<br>'.$email_cus;
                  }
                  if($i<count($quo_attention_arr))
                        $attention_option.='<br>';
                  if($attention_option!="")
                  {
                    $attention_option_print[]= $attention_option;
                    $attention_option_print6 .= $attention_option;
                  }              
                  
                }
         }
        
         
         $title = "";
        if($title_arr[$customer_arr[0]['company_title']]!="")
            $title = $title_arr[$customer_arr[0]['company_title']].' ';
        $customer = '<b>Bill To: '.$title. $customer_arr[0]['company_name'] .'</b>';
        $quotation_co_name = $customer_co_arr[0]['company_name'];
        if($quotation_co_name!="")
            $customer .='<br/><b>C/O: '.$quotation_co_name.'</b>';
        if($address!="")
            $customer .= '<br/>'. $address;
        if(count($attention_option_print)>0)
            $customer .= '<br/><b>Attention:</b> '. implode ($attention_option_print).'';
        
        $customer5 ='<b>'.$title. $customer_arr[0]['company_name'] .'</b>';
        if($quotation_co_name!="")
            $customer5 .='<br/><b>C/O: '.$quotation_co_name.'</b>';
        if($address!="")
            $customer5 .= '<br/>'. $address;

        if(count($attention_option_print)>0)
            $customer5 .= '<br/><b>Attention:</b><br>'. implode ('', $attention_option_print).'';
        
        // Customer template 6
        //$customer6 ='Bill To:<br>'.$title. $customer_arr[0]['company_name'];
        if($quotation_co_name!="")
            $co .='<br>C/O: '.$quotation_co_name;
        if($address!="")
            $address6 .= '<br/>'. $address;

        if(count($attention_option_print)>0)
            $attention6 .= $attention_option_print6;
        
//        $customer6 ='<table border="0">
//                <tr>
//                    <td height="20" colspan="2">Bill To:</td>
//                </tr>
//                <tr>
//                    <td colspan="2">'.$title.$customer_arr[0]['company_name'].'
//                        '.$co.'
//                        '.$address6.'
//                    </td>
//                </tr>
//                <tr>
//                    <td style="line-height:100%">&nbsp;</td>
//                </tr>
//                <tr>
//                    <td colspan="2" height="20">Attn:</td>
//                </tr>
//                <tr>
//                    <td colspan="2">'.$attention6.'</td>
//                </tr>
//            </table>';
        $customer6 ='<b>'.$title.$customer_arr[0]['company_name'].'</b>
                        '.$co.'
                        '.$address6.'
                    <p>Attention To:</p>'.$attention6;
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
           $contact = "";
           if(count($contact_arr)>0){
                if($contact_arr['contact_phone'])
                     $phone = "<br/>Phone: ".$contact_arr['contact_phone']."&nbsp;&nbsp;&nbsp;";
                elseif($contact_arr['contact_phone2'])
                     $phone = "<br/>Phone: ".$contact_arr['contact_phone2']."&nbsp;&nbsp;&nbsp;";
               
                $contact.="<br>Contact: ".$contact_arr['contact_first_name'].' '.$contact_arr['contact_last_name'].'.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                if($contact_arr['contact_email']!="")
                    $contact.="Email: ".$contact_arr['contact_email'].'&nbsp;&nbsp;&nbsp;';
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
                $sales_agent .="<br>Service Coordinator: ".$CInvoiceManager->get_user_change($sale_coordinator_id);;
            if($sales_agent!=""){
                    $sales_agent.='<tr><td colspan="6" width="99%" style="border:1px soild #000;"><b>Sales Agent:</b> '.$brand.CSalesManager::htmlChars($sales_agent).'</td></tr>';
            }
            
        // ############### Item ############## //   
        $dis_label="";
        $col=2;
        $a = '60%';
        $invoice_item_arr = $this->get_db_invoice_item($invoice_id, $invoice_revision_id);
        $booldis=0;
        foreach ($invoice_item_arr as $invoice_item1) {
            if($invoice_item1['invoice_item_discount']!=0){
                $booldis=1;
            }
        }
                if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
                    $tr_item = '';
                    foreach ($invoice_item_arr as $invoice_item) {

                        if ($invoice_item['invoice_item_type'] == 0) {
                            $item = $invoice_item['invoice_item'];
                        } elseif ($invoice_item['invoice_item_type'] == 1) {
                                $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                        } elseif ($invoice_item['invoice_item_type'] == 2) {
                            $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                        } else {
                                $item = '<font color="red">Item type not found.</font>';
                        }
                        if($booldis==1){
                            $dis_label='<td style="border:1px soild #000;" width="11%" align="center" ><b>Discount<br>(%)</b></td>';
                            $dis_value='<td style="'.$border.'" width="11%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
                            $a = '49%';
                            $col=3;
                        }
                    }
                }
        
        
        
        // ########## Total #######//
        
        $total_item_show = $this->get_invoice_item_total($invoice_id, $invoice_revision_id);
        
        $invoice_rev_details_arr = $this->get_latest_invoice_revision($invoice_id);

        if (count($invoice_rev_details_arr) > 0) {
            if($invoice_rev_details_arr[0]['invoice_revision_notes']!="")
    //            $note_area ='<b>Notes:</b> '.$invoice_rev_details_arr[0]['invoice_revision_notes'];
                $note_area='<table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr><td></td></tr>
                    <tr valign="top">
                    <td><b>Notes:</b> '.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_notes']) .'</td>
                 </tr></table>';
            if($invoice_rev_details_arr[0]['invoice_revision_term_condition']!="")
    //            $term_area ='<b>Terms and Conditions:</b> '.$invoice_rev_details_arr[0]['invoice_revision_term_condition'];
                $term_area = '<table width="100%" border="0" cellspacing="0" cellpadding="4"><tr>
                                <td colspan="6" ><b>Terms and Conditions:<br/></b> '.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_term_condition']) .'</td>
                                <td></td>
                              </tr></table>';
            
                if($invoice_rev_details_arr[0]['invoice_revision_notes']!="")
                    $note6="<td width='100%'>Notes:<br>".CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_notes']) ."</td>";
//                if($invoice_rev_details_arr[0]['invoice_revision_term_condition']!="")
//                    $term_area6="<td width='50%' >Terms and Conditions:<br/>'.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_term_condition']) .'</td>";
                if($invoice_rev_details_arr[0]['invoice_revision_notes']!=""||$invoice_rev_details_arr[0]['invoice_revision_term_condition']!="")
                $term_area_and_note6 = '<table width="100%" border="0" cellspacing="0" cellpadding="4"><tr>
                                               '.$note6.' 
                                        </tr></table>';
            $tax_id = $invoice_rev_details_arr[0]['invoice_revision_tax'];
        }
       //In ra so tien da tra
        
       require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");
        $CPaymentManager = new CPaymentManager();
        $paymentDetail_arr = $CPaymentManager->lis_db_payment_detail($invoice_revision_id);
        $tr_paid = 0;
        if(count($paymentDetail_arr)>0){
            foreach ($paymentDetail_arr as $paymentDetail){
                $paymentDetail_amount =  $paymentDetail['payment_amount'];
                $total_paid_and_tax += floatval($paymentDetail['payment_amount']);
                $total_paid_and_tax = round($total_paid_and_tax, 2);
            }
            $tr_paid = $total_paid_and_tax;
                    
        } // END- In ra so tien da tra
 
        $CTax = new CTax();
        $tax_arr = $CTax->list_tax();
        $currency_symb = '$'; $total_paid_and_tax = 0;
        $tax_name = 0;
        if (count($tax_arr) > 0) {
            foreach ($tax_arr as $tax) {
                $tax_name = $tax['tax_name'];
                $tax_value =0;
                if ($tax_id) { // neu tinh trang la update
                    if ($tax['tax_id'] == $tax_id) {
                        $tax_value = $tax['tax_rate'];
                    }
                } else { // neu tinh trang la add lay ra ta default
                    if ($tax['tax_default'] == 1) {
                        $tax_value = $tax['tax_rate'];
                    }
                }
            }
        }
        
        $total_item_show_last_discount = $total_item_show - $discount;
        $total_item_tax_show = 0;
        if ($tax_id) {
            $caculating_tax = (floatval($total_item_show_last_discount) * floatval($tax_value) / 100);
            $total_item_tax_show =CSalesManager::round_up($caculating_tax);
            if($invoice_rev_details_arr[0]['invoice_revision_tax_edit']!=0)
                $total_item_tax_show = $invoice_rev_details_arr[0]['invoice_revision_tax_edit'];
        }
    $amount_due =  $total_item_show_last_discount + $total_item_tax_show - $tr_paid;
        $percent = "";
        if($tax_value !="")
        {
                $percent = $tax_value."%";
        }
        $tbl_total = '<table border="0" width="100%" cellpadding="2" >
                        <tr style="font-weight:bold;">
                            <td align="right">Total:</td>
                            <td align="right">$'.number_format(round($total_item_show,2),2).'</td>
                        </tr>
                        <tr>
                            <td align="right"><b>Discount:</b></td>
                            <td align="right"><b>$'.number_format(round($discount,2),2).'</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td align="right">GST&nbsp;&nbsp;'.$percent.':</td>
                            <td align="right">$'.number_format(round($total_item_tax_show,2),2).'</td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td align="right">Amount Paid:</td>
                            <td align="right">$'.number_format(round($tr_paid,2),2).'</td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td align="right">Amount Due:</td>
                            <td align="right">$'.number_format(round($amount_due,2),2).'</td>
                        </tr>
            </table>';
        
        $tbl_total6 = '<table border="0" width="98%" cellpadding="2">
                        <tr style="font-weight:bold;">
                            <td align="right">Total:</td>
                            <td align="right">$'.number_format(round($total_item_show,2),2).'</td>
                        </tr>
                        <tr >
                            <td align="right">Discount:</td>
                            <td align="right">$'.number_format(round($discount,2),2).'</td>
                        </tr>
                        <tr>
                            <td align="right">GST&nbsp;&nbsp;'.$percent.':</td>
                            <td align="right">$'.number_format(round($total_item_tax_show,2),2).'</td>
                        </tr>
                        <tr >
                            <td align="right">Amount Paid:</td>
                            <td align="right" style="border-bottom:1px soild #000;">$'.number_format(round($tr_paid,2),2).'</td>
                        </tr>
                        <tr>
                            <td align="right">Amount Due:</td>
                            <td align="right" style="border-bottom:2px soild #000;">$'.number_format(round($amount_due,2),2).'</td>
                        </tr>
            </table>';
        
        
        $account_tex = '<tr><td height="30">Yours Sincerely</td><td ></td></tr>';
        
        if($template_pdf_5_array[0]['template_default']==1 && $template_pdf_5_array[0]['footer_text_invoice']!="")
        {
                
                $footer_tex = '
                            <tr><td ></td></tr>
                            <tr><td colspan="5">'.nl2br($template_pdf_5_array[0]['footer_text_invoice']).'</td></tr>
                        ';
        }
        else if($template_pdf_1_array[0]['template_default']==1 && $template_pdf_1_array[0]['footer_text_invoice']!="")
        {
                
                $footer_tex = '
                            <tr><td ></td></tr>
                            <tr>
                                <td colspan="5" height="40">Kindly make the cheque payable to " '.$sales_owner_name.' " and mail to '.$sales_owner_address.'.</td>
                            </tr>
                            <tr><td colspan="5"><b>'.nl2br($template_pdf_1_array[0]['footer_text_invoice']).'</b></td></tr>
                        ';
                if($template_pdf_1_array[0]['template_server']==4)
                    $account_tex = '<tr><td>Yours Sincerely,<br>&nbsp;&nbsp;&nbsp;&nbsp;Chris Goh</td></tr>';
        }
        else
        {
            $footer_tex = '
                            <tr><td ></td></tr>
                            <tr>
                                <td colspan="5" height="40">Kindly make the cheque payable to " '.$sales_owner_name.' " and mail to '.$sales_owner_address.'.</td>
                            </tr>
                            <tr style="">
                                <td colspan="5" height="25"><b>Please indicate invoice number when making payment.</b></td>
                            </tr>
                            <tr >
                                <td colspan="5"><b>Please note that any overdue payment/s will be listed in DP Credit Bureau\'s records and this record may be accessed by financial institutions and other approving credit companies.</b></td>
                            </tr>
                ';
        }
        
       $tbl_sub_heading = "";
        if($sub_heading!="")
            $tbl_sub_heading ='<tr ><td colspan="5">'.nl2br($sub_heading).'</td></tr>';
        
        if($template_pdf_1==1 || $template_pdf_2==1)
        {
            
//                $tbl_header.='<table width="100%" border="0" cellpadding="3">
//                            <tr >
//                                <td colspan="5" align="right" style="font-size:1.2em; border-bottom:1px soild #000;"><b>Invoice No: '.$invoice_no.'</b></td>
//                            </tr>
//                            <tr>
//                                <td colspan="5" style="text-align:center;font-weight:bold;font-size:1.5em;">
//                                     <div>TAX INVOICE</div>
//                                </td>
//                            </tr>
//                            <tr>
//                                <td colspan="3">'.$customer.'</td>
//                                <td colspan="3">'.$info2.'</td>
//                            </tr>
//                            '.$job_location.'
//                            '.$subject_note.'
//                            '.$tr_br.'
//                       </table>';
//                
//                $tbl_header_item = '<table width="100%" border="0" cellpadding="5">
//                            <tr style="font-weight:bold;">
//                                <td height="30" style="border:1px soild #000;" width="6.5%" align="center">S/No</td>
//                                <td style="border:1px soild #000;" width="'.$a.'">Description</td>
//                                <td style="border:1px soild #000;" align="center" width="6.5%">Qty</td>
//                                <td style="border:1px soild #000;" align="right" width="13%">Unit Price</td>
//                                '.$dis_label.'
//                                <td style="border:1px soild #000;" align="right" width="13%">Amount</td>
//                            </tr>
//                            </table>
//                    ';
//                $pdf->SetAutoPageBreak(true, 20);
//                $pdf->AddPage('P', 'A4');
//                $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item.$tbl_sub_heading);
//                $pdf->ln();
//                
//                if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
//                    $tr_item = '';$i=1;$count_tu=0;
//                    foreach ($invoice_item_arr as $invoice_item) {
//
//                        if ($invoice_item['invoice_item_type'] == 0) {
//                            $item = $invoice_item['invoice_item'];
//                            if($i==1)
//                                $count_tu = strlen($item);
//                        } elseif ($invoice_item['invoice_item_type'] == 1) {
//                                $item = 'Ipad<br><font color="#AAAAAA">description</font>';
//                        } elseif ($invoice_item['invoice_item_type'] == 2) {
//                            $item = 'Ipad<br><font color="#AAAAAA">description</font>';
//                        } else {
//                                $item = '<font color="red">Item type not found.</font>';
//                        }
//                        if($booldis==1){
//                            $dis_label='<td  width="11%" align="center" ><b>Discount<br>(%)</b></td>';
//                            $dis_value='<td  width="11%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
//                            $a = '49%';
//                            $col=3;
//                        }
//
//                        $tr_item = '<table width="100%" border="0" cellpadding="5">
//                            <tr>
//                                <td  align="center" width="6%">'. $i.'</td>
//                                <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
//                                <td  width="7%" align="center">'. $invoice_item['invoice_item_quantity'] .'</td>
//                                <td  width="13%" align="right">$'. number_format($invoice_item['invoice_item_price'],2) .'</td>                                
//                                '.$dis_value.'
//                                <td width="13%" align="right">$'. number_format(CSalesManager::calculate_total_item($invoice_item['invoice_item_price'], $invoice_item['invoice_item_quantity'], $invoice_item['invoice_item_discount']),2) .'</td>
//                            </tr></table>';
//                        if($pdf->GetY()>$pdf->getPageHeight()-60){
//                                  $pdf->AddPage();
//                                  $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item);
//                                  $pdf->ln();
//                              }
//                         $i++;
//                         $pdf->writeHTMLCell('','','','',$tr_item);
//                         $pdf->ln();
//                    }
//                }
//
//                // Hien thi box total
//                $tbl_footer='<table width="98%" border="0" cellpadding="2">';
//                    $tbl_footer.='<tr>
//                            <td colspan="'.$col.'">'.$note_area.'</td>
//                            <td colspan="3">'.$tbl_total.'</td>
//                        </tr>';
//                $tbl_footer.='</table>';
//                $hf = 62;
//                if($pdf->GetY()>$pdf->getPageHeight()-$hf || $count_tu>1200){
//                          $pdf->AddPage();
//                          $pdf->writeHTMLCell('','','','',$tbl_header);
//                          $pdf->ln();
//                      }
//                $pdf->writeHTMLCell('','','','',$tbl_footer);
//                $pdf->ln();
//                // Hien thi term and codition
//
//                $pdf->writeHTMLCell('','','','',$term_area);
//                $pdf->ln();
//                if($pdf->GetY()>$pdf->getPageHeight()-75){
//                              $pdf->AddPage();
//                              $pdf->writeHTMLCell('','','','',$tbl_header);
//                              $pdf->ln();
//                          }
//                $pdf->writeHTMLCell('','','','',$footer_tex);
//                $pdf->ln();
//                $thYou = '<table width="98%" border="0" cellpadding="5">
//                                <tr>
//                                    <td>Thank you & best regards</td>
//                                </tr>
//                                <tr>
//                                    <td height="20">&nbsp;</td>
//                                </tr>
//                                '.$account_tex.'
//                        </table>';
//                    $pdf->writeHTMLCell('','','',250,$thYou);
            $pdf->SetMargins(20,38, 8, -15);
            $tbl_1 = '<table border="0" width="100%" cellpadding="4">'
                        . '<thead>'
                            .'<tr>
                                <td colspan="5" style="text-align:center;font-weight:bold;font-size:1.5em;">
                                     TAX INVOICE
                                </td>
                            </tr>'
                            . '<tr>'
                                . '<td width="67%">'.$customer.'</td>'
                                . '<td width="33%">'.$info2.'</td>'
                            . '</tr>'
                            .$job_location.$subject_note.$tr_br
                            .'<tr>'
                                    .'<td height="30" style="border:1px soild #000;" width="6.5%" align="center">S/No</td>
                                    <td style="border:1px soild #000;" width="'.$a.'">Description</td>
                                    <td style="border:1px soild #000;" align="center" width="6.5%">Qty</td>
                                    <td style="border:1px soild #000;" align="right" width="13%">Unit Price</td>
                                    '.$dis_label.'
                                    <td style="border:1px soild #000;" align="right" width="13%">Amount</td>'
                            . '</tr>'
                        . '</thead>'
                        . '<tbody>';
                            $tbl_1.=$tbl_sub_heading;
                            if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
                                     $tr_item = ''; $i = 1; $border = "";
                                     foreach ($invoice_item_arr as $invoice_item) {

                                         if ($invoice_item['invoice_item_type'] == 0) {
                                             $item = $invoice_item['invoice_item'];
                                         } elseif ($invoice_item['invoice_item_type'] == 1) {
                                             $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                                         } elseif ($invoice_item['invoice_item_type'] == 2) {
                                             $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                                         } else {
                                             $item = '<font color="red">Item type not found.</font>';
                                         }

                                         if($booldis==1){
                                             $dis_label='<td  align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                                             $dis_value='<td  align="center" width="11%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
                                             $a = '44%';
                                             $col ="3";
                                         }
                                         
                                         $tbl_1.= '<tr>
                                                             <td  align="center" width="6.5%">'. $i .'</td>
                                                             <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                                             <td  align="center"  width="6.5%">'. $invoice_item['invoice_item_quantity'] .'</td>
                                                             <td  align="center" align="right" width="13%">$'. number_format($invoice_item['invoice_item_price'],2) .'</td>
                                                             '.$dis_value.'
                                                             <td  align="center" align="right" width="13%">$'. number_format(CSalesManager::calculate_total_item($invoice_item['invoice_item_price'], $invoice_item['invoice_item_quantity'], $invoice_item['invoice_item_discount']),2) .'</td>
                                                    </tr>';
                                        $i++;
                                     }
                             }
                $tbl_1 .= '</table>';
                // Hien note va box Total.
                $tbl_2.='<table border="0"><tr>
                                <td colspan="'.$col.'" width="60%">'.$note_area.'</td>
                                <td colspan="3" width="39%">'.$tbl_total.'</td>
                         </tr>';
                if($invoice_rev_details_arr[0]['invoice_revision_term_condition']!="")
                    $tbl_2.='<tr>
                                <td colspan="5"><b>Terms and Conditions:</b><br>'.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_term_condition']) .'</td>
                        </tr>';
                $tbl_2 .= $footer_tex;
            $tbl_2.=    '</tbody>'
                    . '</table>';
            

            if($pdf->last_page_flag)
              $pdf->SetAutoPageBreak(true, 45);
            else
                $pdf->SetAutoPageBreak(true, 30);
            $pdf->AddPage('P', 'A4');
            $pdf->writeHTML($tbl_1);
            $hight = $pdf->GetY();
            if($hight>240)
            {
                
                $pdf->addPage();
                $pdf->Ln(4);
                $pdf->writeHTML($tbl_2);
            }
            else
                $pdf->writeHTML($tbl_2);
        }
        else if($template_pdf_3 == 1)
        {
                    $pdf->SetMargins(10,42, 8, -15);
                    $tbl_header.='<table width="100%" border="0" cellpadding="5"> 
                                <tr>
                                    <td colspan="3" width="75%">'.$customer.'</td>
                                    <td colspan="3" width="25%">'.$img.'</td>
                                </tr>
                                '.$job_location.'
                                '.$subject_note.'
                                '.$tr_br.'
                           </table>';

                    $tbl_header_item = '<table width="100%" border="1" cellpadding="5">
                                <tr style="font-weight:bold;">
                                    <td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
                                    <td style="border:1px soild #000;" width="'.$a.'">Description</td>
                                    <td style="border:1px soild #000;" align="center" width="7%">Qty</td>
                                    <td style="border:1px soild #000;" align="right" width="13%">Unit Price</td>
                                    '.$dis_label.'
                                    <td style="border:1px soild #000;" align="right" width="13%">Amount</td>
                                </tr>
                                </table>
                        ';
                    $pdf->SetAutoPageBreak(true, 20);
                    $pdf->AddPage('P', 'A4');
                    $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item.$tbl_sub_heading);
                    $pdf->ln();

                    if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
                        $tr_item = '';$i=1;
                        foreach ($invoice_item_arr as $invoice_item) {

                            if ($invoice_item['invoice_item_type'] == 0) {
                                $item = $invoice_item['invoice_item'];
                            } elseif ($invoice_item['invoice_item_type'] == 1) {
                                    $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                            } elseif ($invoice_item['invoice_item_type'] == 2) {
                                $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                            } else {
                                    $item = '<font color="red">Item type not found.</font>';
                            }
                            if($booldis==1){
                                $dis_label='<td  width="11%" align="center" ><b>Discount<br>(%)</b></td>';
                                $dis_value='<td  width="11%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
                                $a = '49%';
                                $col=3;
                            }

                            $tr_item = '<table width="100%" border="0" cellpadding="5">
                                <tr>
                                    <td  align="center" width="6%">'. $i .'</td>
                                    <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                    <td  width="7%" align="center">'. $invoice_item['invoice_item_quantity'] .'</td>
                                    <td  width="13%" align="right">$'. number_format($invoice_item['invoice_item_price'],2) .'</td>                                
                                    '.$dis_value.'
                                    <td width="13%" align="right">$'. number_format(CSalesManager::calculate_total_item($invoice_item['invoice_item_price'], $invoice_item['invoice_item_quantity'], $invoice_item['invoice_item_discount']),2) .'</td>
                                </tr></table>';
                            //$heght=GetY()>$pdf->getPageHeight();
                            if($pdf->GetY()>$pdf->getPageHeight()-60){
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
                                <td colspan="3">'.$tbl_total.'</td>
                            </tr>';
                    $tbl_footer.='</table>';
                    $hf = 62;
                    if($pdf->GetY()>$pdf->getPageHeight()-$hf){
                              $pdf->AddPage();
                              $pdf->writeHTMLCell('','','','',$tbl_header);
                              $pdf->ln();
                          }
                    $pdf->writeHTMLCell('','','','',$tbl_footer);
                    $pdf->ln();
                    // Hien thi term and codition

                    $pdf->writeHTMLCell('','','','',$term_area);
                    $pdf->ln();
                    if($pdf->GetY()>$pdf->getPageHeight()-65){
                                  $pdf->AddPage();
                                  $pdf->writeHTMLCell('','','','',$tbl_header);
                                  $pdf->ln();
                              }
                    $pdf->writeHTMLCell('','','','',$footer_tex);
                    $pdf->ln();
                    $thYou = '<table width="98%" border="0" cellpadding="5">
                                    <tr>
                                        <td>Thank you & best regards</td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Yours Sincerely</td>
                                    </tr>
                            </table>';
                        $pdf->writeHTMLCell('','','',253,$thYou);
        }
        else if($template_pdf_4 ==1)
        {
            
                    $tbl_header.='<table width="100%" border="0" cellpadding="5">
                                <tr >
                                    <td colspan="3" width="60%">&nbsp;</td>
                                    <td><b><span style="font-size:1.6em;text-align:left;">TAX INVOICE</span></b></td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="60%">'.$customer.'</td>
                                    <td colspan="3" width="40%">'.$info2.'</td>
                                </tr>
                                '.$job_location.'
                                '.$subject_note.'
                                '.$tr_br.'
                           </table>';

                    $tbl_header_item = '<table width="100%" border="1" cellpadding="5">
                                <tr style="font-weight:bold;">
                                    <td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
                                    <td style="border:1px soild #000;" width="'.$a.'">Description</td>
                                    <td style="border:1px soild #000;" align="center" width="7%">Qty</td>
                                    <td style="border:1px soild #000;" align="right" width="13%">Unit Price</td>
                                    '.$dis_label.'
                                    <td style="border:1px soild #000;" align="right" width="13%">Amount</td>
                                </tr>
                                </table>
                        ';
                    $pdf->SetAutoPageBreak(true, 20);
                    $pdf->AddPage('P', 'A4');
                    $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item.$tbl_sub_heading);
                    $pdf->ln();

                    if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
                        $tr_item = '';$i=1;
                        foreach ($invoice_item_arr as $invoice_item) {

                            if ($invoice_item['invoice_item_type'] == 0) {
                                $item = $invoice_item['invoice_item'];
                            } elseif ($invoice_item['invoice_item_type'] == 1) {
                                    $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                            } elseif ($invoice_item['invoice_item_type'] == 2) {
                                $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                            } else {
                                    $item = '<font color="red">Item type not found.</font>';
                            }
                            if($booldis==1){
                                $dis_label='<td  width="11%" align="center" ><b>Discount<br>(%)</b></td>';
                                $dis_value='<td  width="11%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
                                $a = '49%';
                                $col=3;
                            }

                            $tr_item = '<table width="100%" border="0" cellpadding="5">
                                <tr>
                                    <td  align="center" width="6%">'. $i .'</td>
                                    <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                    <td  width="7%" align="center">'. $invoice_item['invoice_item_quantity'] .'</td>
                                    <td  width="13%" align="right">$'. number_format($invoice_item['invoice_item_price'],2) .'</td>                                
                                    '.$dis_value.'
                                    <td width="13%" align="right">$'. number_format(CSalesManager::calculate_total_item($invoice_item['invoice_item_price'], $invoice_item['invoice_item_quantity'], $invoice_item['invoice_item_discount']),2) .'</td>
                                </tr></table>';
                            if($pdf->GetY()>$pdf->getPageHeight()-60){
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
                                <td colspan="3">'.$tbl_total.'</td>
                            </tr>';
                    $tbl_footer.='</table>';
                    $hf = 62;
                    if($pdf->GetY()>$pdf->getPageHeight()-$hf){
                              $pdf->AddPage();
                              $pdf->writeHTMLCell('','','','',$tbl_header);
                              $pdf->ln();
                          }
                    $pdf->writeHTMLCell('','','','',$tbl_footer);
                    $pdf->ln();
                    // Hien thi term and codition

                    $pdf->writeHTMLCell('','','','',$term_area);
                    $pdf->ln();
                    if($pdf->GetY()>$pdf->getPageHeight()-65){
                                  $pdf->AddPage();
                                  $pdf->writeHTMLCell('','','','',$tbl_header);
                                  $pdf->ln();
                              }
                    $pdf->writeHTMLCell('','','','',$footer_tex);
                    $pdf->ln();
                    $thYou = '<table width="98%" border="0" cellpadding="5">
                                    <tr>
                                        <td>Thank you & best regards</td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Yours Sincerely</td>
                                    </tr>
                            </table>';
                        $pdf->writeHTMLCell('','','',253,$thYou);
        }
        else if($template_pdf_5 == 1)
        {
          if($img!="")
                $pdf->SetMargins(10,38, 8, -15);
            else
                $pdf->SetMargins(5,45, 8, -15);
                    $tbl_header.='<table width="100%" border="0" cellpadding="0">
                                <tr >
                                    <td colspan="3" width="75%">'.$customer5.'</td>
                                    <td colspan="3" width="24%" align="right"><b><span style="font-size:1.6em;text-align:right;">TAX INVOICE</span></b></td>
                                </tr>
                                <tr><td style="line-height:50%"></td></tr>
                                </table>
                                <table width="100%" border="0" cellpadding="5">
                                '.$job_location.'
                                '.$subject_note.'
                                '.$tr_br.'
                           </table>';

                    $tbl_header_item = '<table width="100%" border="0" cellpadding="4">
                                <tr style="font-weight:bold;">
                                    <td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
                                    <td style="border:1px soild #000;" width="'.$a.'">Description</td>
                                    <td style="border:1px soild #000;" align="center" width="7%">Qty</td>
                                    <td style="border:1px soild #000;" align="right" width="13%">Unit Price</td>
                                    '.$dis_label.'
                                    <td style="border:1px soild #000;" align="right" width="13%">Amount</td>
                                </tr>
                                </table>
                        ';
                    $pdf->SetAutoPageBreak(true, 20);
                    $pdf->AddPage('P', 'A4');
                    $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item.$tbl_sub_heading);
                    $pdf->ln();

                    if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
                        $tr_item = '';$i=1;
                        foreach ($invoice_item_arr as $invoice_item) {

                            if ($invoice_item['invoice_item_type'] == 0) {
                                $item = $invoice_item['invoice_item'];
                            } elseif ($invoice_item['invoice_item_type'] == 1) {
                                    $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                            } elseif ($invoice_item['invoice_item_type'] == 2) {
                                $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                            } else {
                                    $item = '<font color="red">Item type not found.</font>';
                            }
                            if($booldis==1){
                                $dis_label='<td  width="11%" align="center" ><b>Discount<br>(%)</b></td>';
                                $dis_value='<td  width="11%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
                                $a = '49%';
                                $col=3;
                            }

                            $tr_item = '<table width="100%" border="0" cellpadding="5">
                                <tr>
                                    <td  align="center" width="6%">'. $i .'</td>
                                    <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                    <td  width="7%" align="center">'. $invoice_item['invoice_item_quantity'] .'</td>
                                    <td  width="13%" align="right">$'. number_format($invoice_item['invoice_item_price'],2) .'</td>                                
                                    '.$dis_value.'
                                    <td width="13%" align="right">$'. number_format(CSalesManager::calculate_total_item($invoice_item['invoice_item_price'], $invoice_item['invoice_item_quantity'], $invoice_item['invoice_item_discount']),2) .'</td>
                                </tr></table>';
                            if($pdf->GetY()>$pdf->getPageHeight()-60){
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
                                <td colspan="3">'.$tbl_total.'</td>
                            </tr>';
                    $tbl_footer.='</table>';
                    $hf = 62;
                    if($pdf->GetY()>$pdf->getPageHeight()-$hf){
                              $pdf->AddPage();
                              $pdf->writeHTMLCell('','','','',$tbl_header);
                              $pdf->ln();
                          }
                    $pdf->writeHTMLCell('','','','',$tbl_footer);
                    $pdf->ln();
                    // Hien thi term and codition

                    $pdf->writeHTMLCell('','','','',$term_area);
                    $pdf->ln();
                    if($pdf->GetY()>$pdf->getPageHeight()-65){
                                  $pdf->AddPage();
                                  $pdf->writeHTMLCell('','','','',$tbl_header);
                                  $pdf->ln();
                              }
                    $pdf->writeHTMLCell('','','','',$footer_tex);
                    $pdf->ln();
                    $thYou = '<table width="98%" border="0" cellpadding="5">
                                    <tr>
                                        <td>Thank you & best regards</td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Yours Sincerely</td>
                                    </tr>
                            </table>';
                        $pdf->writeHTMLCell('','','',253,$thYou);
        }
        else if($template_pdf_6 == 1)
        {
//          if($img!="")
//                $pdf->SetMargins(10,55, 8, -15);
//            else
//                $pdf->SetMargins(5,45, 8, -15);
//                    $tbl_header.='<table width="100%" border="0" cellpadding="0">
//                                <tr >
//                                    <td colspan="3" width="53%">'.$customer6.'</td>
//                                    <td colspan="3" width="47%" align="center">
//                                        <b><span style="font-size:1.6em;text-align:right;width:100%">TAX INVOICE</span></b><br><br>
//                                        '.$info6.'
//                                    </td>
//                                </tr>
//                                <tr><td style="line-height:50%"></td></tr>
//                                </table>
//                                <table width="100%" border="0" cellpadding="5">
//                                '.$job_location.'
//                                '.$subject_note.'
//                                '.$tr_br.'
//                           </table>';
//                    $a="54%";
//                    if($booldis==1)
//                        $a = "44%";
//                    $tbl_header_item = '<table width="100%" border="0" cellpadding="5">
//                                <tr style="font-weight:bold;">
//                                    <td height="30" style="border:1px soild #000;" width="6%" align="center">S/No</td>
//                                    <td style="border:1px soild #000;" width="'.$a.'">Description</td>
//                                    <td style="border:1px soild #000;" align="right" width="7%">Qty</td>
//                                    <td style="border:1px soild #000;" align="right" width="16%">Unit Price</td>
//                                    '.$dis_label.'
//                                    <td style="border:1px soild #000;" align="right" width="16%">Amount</td>
//                                </tr>
//                                </table>
//                        ';
//                    $pdf->SetAutoPageBreak(true, 10);
//                    $pdf->AddPage('P', 'A4');
//                    $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item.$tbl_sub_heading);
//                    $pdf->ln();
//
//                    if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
//                        $tr_item = '';$i=1;
//                        foreach ($invoice_item_arr as $invoice_item) {
//                            
//                            $amount = CSalesManager::calculate_total_item($invoice_item['invoice_item_price'], $invoice_item['invoice_item_quantity'], $invoice_item['invoice_item_discount']);
//                            if ($invoice_item['invoice_item_type'] == 0) {
//                                $item = $invoice_item['invoice_item'];
//                            } elseif ($invoice_item['invoice_item_type'] == 1) {
//                                    $item = 'Ipad<br><font color="#AAAAAA">description</font>';
//                            } elseif ($invoice_item['invoice_item_type'] == 2) {
//                                $item = 'Ipad<br><font color="#AAAAAA">description</font>';
//                            } else {
//                                    $item = '<font color="red">Item type not found.</font>';
//                            }
//                            if($booldis==1){
//                                $dis_label='<td  width="11%" align="center" ><b>Discount<br>(%)</b></td>';
//                                $dis_value='<td  width="11%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
//                                $a = '43%';
//                                $col=3;
//                            }
//
//                            $tr_item = '<table width="100%" border="0" cellpadding="5">
//                                <tr>
//                                    <td  align="center" width="6%">'. $i .'</td>
//                                    <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
//                                    <td  width="7%" align="right">'. $invoice_item['invoice_item_quantity'] .'</td>
//                                    <td  width="16%" align="right">'. CSalesManager::negativeConverParenthesis($invoice_item['invoice_item_price']) .'</td>                                
//                                    '.$dis_value.'
//                                    <td width="16%" align="right">'.CSalesManager::negativeConverParenthesis($amount).'</td>
//                                </tr></table>';
//                            //$tr_item1 = "<table><tr><td>&nbsp;</td></tr></table>";
//                            if($pdf->GetY()>$pdf->getPageHeight()-60){
//                                      $pdf->AddPage();
//                                      $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item);
//                                      $pdf->ln();
//                                  }
//                             $i++;
//                             $pdf->writeHTMLCell('','','','',$tr_item);
//                             $pdf->ln();
////                             $pdf->writeHTMLCell('','','','',"");
////                             $pdf->ln();
//                             
//                        }
//                    }
//                    
//                    if($pdf->GetY()>$pdf->getPageHeight()-65){
//                        $pdf->AddPage();
//                        $pdf->writeHTMLCell('','','','',$term_area_and_note6);
//                        $pdf->ln(); 
//                    }
//                    else
//                        $pdf->writeHTMLCell('','','','',$term_area_and_note6);
//
//   
//                    $pdf->writeHTMLCell('','','','',"&nbsp;");
//                    
//                    //Hien thi 
//                    $tbl_no = '<div style="text-align:center">
//                                    <b>Thank you. We look forword to being of sevice to you again.</b>
//                                    <hr></br>
//                                    <span style="font-weight:bold;">E. & O. E.</span>
//                                </div>';
//                    if($pdf->GetY()>$pdf->getPageHeight()-60){
//                              $pdf->AddPage();
//                              $pdf->writeHTMLCell('','','','',$tbl_header);
//                              $pdf->ln();
//                          }
//                    $pdf->writeHTMLCell('','','',245,$tbl_no);
//
//                    // Hien thi box total
//                    $tbl_footer='<table width="99%" border="0" cellpadding="0" spacepadding="0">';
//                        $tbl_footer.='<tr>
//                                <td colspan="'.$col.'" width="55%">'.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_term_condition']).'</td>
//                                <td colspan="3" width="45%">'.$tbl_total6.'</td>
//                            </tr>';
//                    $tbl_footer.='</table>';
//                    $pdf->writeHTMLCell('','','',255,$tbl_footer);
            
            if($pdf->last_page_flag)
              $pdf->SetAutoPageBreak(true, 75);
            else
                $pdf->SetAutoPageBreak(true, 50);
            if($img!="")
                $pdf->SetMargins(15,45, 10, -15);
            else
                $pdf->SetMargins(15,45, 8, -15);
            $pdf->AddPage('P', 'A4');
            $a = '54%';
            if($booldis==1){
                $dis_label='<td style="border:1px soild #000;" align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                $dis_value='<td  align="center" width="11%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
                $a = '43%';
                $col ="3";
            }
                $txt_body ='<table width="100%" border="0" cellpadding="2">
                                <thead>
                                    <tr >
                                        <td colspan="3" width="64%">'.$customer6.'</td>
                                        <td colspan="3" width="36%" align="left">
                                            <b><span style="font-size:1.6em;width:100%">TAX INVOICE</span></b><br><br>'.$info6.'
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
                if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
                         $tr_item = ''; $i = 1; $border = "";
                         foreach ($invoice_item_arr as $invoice_item) {

                             if ($invoice_item['invoice_item_type'] == 0) {
                                 $item = $invoice_item['invoice_item'];
                             } elseif ($invoice_item['invoice_item_type'] == 1) {
                                 $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                             } elseif ($invoice_item['invoice_item_type'] == 2) {
                                 $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                             } else {
                                 $item = '<font color="red">Item type not found.</font>';
                             }
                             if($booldis==1){
                                 $dis_label='<td style="border:1px soild #000;" align="center" width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                                 $dis_value='<td  align="center" width="11%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
                                 $a = '43%';
                                 $col ="3";
                             }
                             $txt_body .= '<tr>
                                                 <td  align="center" width="6%">'. $i .'</td>
                                                 <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                                                 <td  align="right"  width="6%">'. $invoice_item['invoice_item_quantity'] .'</td>
                                                 <td  align="right" width="16%">$'. number_format($invoice_item['invoice_item_price'],2) .'</td>
                                                 '.$dis_value.'
                                                 <td  align="center" align="right" width="16%">$'. number_format(CSalesManager::calculate_total_item($invoice_item['invoice_item_price'], $invoice_item['invoice_item_quantity'], $invoice_item['invoice_item_discount']),2) .'</td>
                                             </tr>';
                             $i++;
                         }
                         
                 }
        $txt_body.=    '</table>';
        $txt_body.=$note6;
                    $pdf->writeHTMLCell('','','','',$txt_body);
                    $pdf->ln();
 
        }
        // Hien thi box total
//        $tbl_footer='<table width="99%" border="0" cellpadding="0" spacepadding="0">';
//            $tbl_footer.='<tr>
//                    <td colspan="'.$col.'" width="55%">'.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_term_condition']).'</td>
//                    <td colspan="3" width="45%">'.$tbl_total6.'</td>
//                </tr>';
//        $tbl_footer.='</table>';
//        $pdf->writeHTMLCell('','','',250,$tbl_footer);
        
//        $pdf->AddPage('P', 'A4');
//        $pdf->writeHTML($tbl, true, false, false, false, '');

        ob_end_clean();
        
        if($is_send_mail)
            return $pdf->Output('', 'S'); // tao ra chuoi document, ten bo qua
        else if($dir)
            $pdf->Output($dir.'/'.$invoice_rev.'.pdf','F'); // Save file PDF vao forder
        else
            $pdf->Output($invoice_rev .'.pdf', 'I');
    }
     public function create_invoice_revision_html($invoice_id_arr, $invoice_revision_id_arr, $is_send_mail = false) {
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        require_once (DP_BASE_DIR . "/modules/contacts/contacts_manager.class.php");
        require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
        $cconfig = new CConfigNew();
        $ContactManager = new ContactManager();
        $CCompany = new CompanyManager();
        
        if ($invoice_revision_id_arr == null) {
            if (($count = count($invoice_id_arr)) > 0) {
                for ($i = 0; $i < $count; $i++) {
                    $invoice_id = $invoice_id_arr[$i];
                    $invoice_revision_id = $this->get_invoice_revision_lastest($invoice_id);
                }
            }
        } else {
             $invoice_id = $invoice_id_arr;
             $invoice_revision_id = $invoice_revision_id_arr;   
       }

       //In ra so tien da tra
       require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");
        $CPaymentManager = new CPaymentManager();
        $paymentDetail_arr = $CPaymentManager->lis_db_payment_detail($invoice_revision_id);
        $tr_paid = 0;
        if(count($paymentDetail_arr)>0){
            foreach ($paymentDetail_arr as $paymentDetail){
                $paymentDetail_amount =  $paymentDetail['payment_amount'];
                $total_paid_and_tax += floatval($paymentDetail['payment_amount']);
                $total_paid_and_tax = round($total_paid_and_tax, 2);
            }
            $tr_paid = $total_paid_and_tax;
                    
        } // END- In ra so tien da tra
        //print_r ($invoice_revision_id_arr);
        
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        $files = scandir($url);
        $i = count($files)-1;
        $w="100%";
        if (count($files) > 2 && $files[$i]!=".svn" ) {
            $path_file = $url . $files[$i];
            $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" width="200" /><br/></td>';
            $w="73%";
        }
        
        $CTax = new CTax();
        $tax_arr = $CTax->list_tax();
        $currency_symb = '$'; $total_paid_and_tax = 0;
        
        if(is_array($invoice_id)) {
            $invoice_id = $invoice_id[0];
        }
//        if(is_array($invoice_revision_id)) {
//            $invoice_revision_id = $invoice_revision_id[0];
//        }
        //echo $invoice_id;
        $invoice_details_arr = $this->get_db_invoice($invoice_id);
        $invoice_details_arr1 = $this->get_latest_invoice_revision($invoice_id);
        if (count($invoice_details_arr) > 0) {
            $date = $invoice_details_arr[0]['invoice_date'];
            $invoice_no = $invoice_details_arr[0]['invoice_no'];
            $po_number = $invoice_details_arr[0]['po_number'];
            $our_delivery = $invoice_details_arr[0]['our_delivery_order_no'];
            $term_value = $invoice_details_arr[0]['term'];
            

            $sale_person_name = $invoice_details_arr[0]['invoice_sale_person'];
            $sale_person_email = $invoice_details_arr[0]['invoice_sale_person_email'];
            $sale_person_phone = $invoice_details_arr[0]['invoice_sale_person_phone'];
            $sale_coordinator_id = $invoice_details_arr[0]['contact_coordinator_id'];

            $client_id = $invoice_details_arr[0]['customer_id'];
            $address_id = $invoice_details_arr[0]['address_id'];
            $attention_id = $invoice_details_arr[0]['attention_id'];
            $contact_id = $invoice_details_arr[0]['contact_id'];
            $job_location_id = $invoice_details_arr[0]['job_location_id'];
            $subject = $invoice_details_arr[0]['invoice_subject'];
            $invoice_co = $invoice_details_arr[0]['invoice_CO'];

        }
        $invoice_rev = $invoice_details_arr1[0]['invoice_revision'];

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
    //$sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
    $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
    }
    
    $supplier = '<tr><td><b>'. CSalesManager::htmlChars($sales_owner_name) .'</b></td></tr>';
    $supplier.= '<tr><td>'.CSalesManager::htmlChars($sales_owner_address).'</td></tr>';
    $supplier .= '<tr><td>Tel: '.$phone.', Fax: '.$fax.'</td></tr>';
//    $supplier .= '<tr><td>Fax: '.$fax.'</td></tr>';
    $supplier .= '<tr><td>GST Reg No: '. CSalesManager::htmlChars($sales_owner_gst_reg_no) .'</td></tr>';
//    $supplier.= '<tr><td>Reg No: '. $supplier_arr['sales_owner_postal_code'] .'</td></tr>';
    $supplier .= '<tr><td>Email: '.CSalesManager::htmlChars($email).'</td></tr>';
    if($web!="")
        $supplier .= '<tr><td>Website: '.$web.'</td></tr>';

    $term_arr = dPgetSysVal('Term');
    foreach ($term_arr as $key => $value) {
        if($key == $term_value)
            if($value==1)
                $term = $value.' day';
            else if(is_numeric($value))
                $term = $value.' days';
            else
                $term = $value;
    }
    $sale_Contaccoord_arr=$ContactManager->get_db_contact($sale_coordinator_id);
    $info2 .='<table width="100%" style="float:right;" border="0" cellpadding="0" cellspacing="0">';
    $info2 .= '<tr><td width="45%">Date</td><td width="55%">: '. date('d-m-Y',  strtotime($date)).'</td></tr>';
    $info2 .= '<tr><td>Your P.O Number</td><td>: '. CSalesManager::htmlChars($po_number).'</td></tr>';
    //$info2 .= '<tr><td>Invoice No</td><td>: '. $invoice_no.'</td></tr>';
//    $info2 .= 'Invoice Rev: '. $invoice_rev;
    $info2 .= '<tr><td>Our Delivery Order No</td><td>: '.CSalesManager::htmlChars($our_delivery).'</td></tr>';
    $info2 .= '<tr><td>Terms</td><td>: '. $term.'</td></tr>';
    $info2 .= '<tr><td>Service Coordinator</td><td>: '.$sale_Contaccoord_arr[0]['contact_last_name'] .', '.$sale_Contaccoord_arr[0]['contact_first_name'].'</td></tr>';
    $info2 .= '<tr><td></td></tr>';
    $info2 .= '</table>';
    
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
            if (intval($row['company_id']) == intval($invoice_co)) {
                $invoice_co_name = $row['company_name'];
                //$img= '<img height="100" width="150" alt="Smiley face" src="modules/sales/images/logo/1_picture030.jpg">';
                break;
            }
        }
    }
    

    $client_address =  CSalesManager::get_address_by_id($address_id);
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
            $option.=', '.$client_address['address_street_address_2'];
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
    $postal_code = "";
    $job_location_arr =  CSalesManager::get_address_by_id($job_location_id);
    if($job_location_arr>0){
        $postal_code = ', Singapore '.$client_address['address_postal_zip_code'];
    }
    $img_print = $img;
    // Lay Email by Attention
    $email_rr = CSalesManager::get_attention_email($attention_id);
    if(count($email_rr) >0){
        if($email_rr['contact_email']!="")
            $email_cus = "Email: ". $email_rr['contact_email']. "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        if($email_rr['contact_mobile']!="")
            $email_cus .= "Mobile: ". $email_rr['contact_mobile'];
    }
    
    $customer = '<b>Bill To: '. CSalesManager::htmlChars($company_name) .'</b>';
    if($invoice_co_name!="")
        $customer.= '<br><b>C/O: '. CSalesManager::htmlChars($invoice_co_name) .'</b>';
    if($address!="")
        $customer .= '<br/>'. CSalesManager::htmlChars($address);
    if($attention_option_print!="")
        $customer .= '<br/>Attention: '. CSalesManager::htmlChars($attention_option_print);
    if($email_cus!="")
        $customer .= '<br/>'.CSalesManager::htmlChars($email_cus);
//    if($job_location_arr!="")
//        $customer .= '<br/>Job Location: '.$job_location_arr['address_street_address_1'].$postal_code;
    
    //Lay contact la coordinator
    $sales_contact = "";

    if($sale_person_name!="" || $sale_person_email!="" || $sale_person_phone!="" )
        $sales_contact = '<b>Sales Agent</b><br/>';
    if($sale_person_name!="")
        $sales_contact .= 'Name: '. CSalesManager::htmlChars($sale_person_name) .'<br/>';
    if($sale_person_email!="")
        $sales_contact .= 'Email: '. CSalesManager::htmlChars($sale_person_email) .'<br/>';
    if($sale_person_phone!="")
        $sales_contact .= '<br/>Phone: '. $sale_person_phone.'<br/>';
//    if($sale_Contaccoord_arr[0]['contact_last_name']!="" || $sale_Contaccoord_arr[0]['contact_first_name']!="")
//        $sales_contact .= 'Service Coordinator: '.$sale_Contaccoord_arr[0]['contact_last_name'] .', '.$sale_Contaccoord_arr[0]['contact_first_name'];
    
    if($sales_contact!=""){
        $sales_contact_td = '<td>'.$sales_contact.'</td>';
    }
    
//    $accepted_by = '<b>Accepted by:</b><br/><br/>';
//    $accepted_by .= '______________________________________ <br/><br/>';
//    $accepted_by .= 'Name: <br/>';
//    $accepted_by .= 'Email: <br/>';
//    $accepted_by .= 'Designation:';

//    $header_text = 'Header\'s text<br/>';

    $dis_label ="";
    $dis_label="";
    $a = '57%';
    $invoice_item_arr = $this->get_db_invoice_item($invoice_id, $invoice_revision_id);
    $booldis=0;
    foreach ($invoice_item_arr as $invoice_item1) {
        if($invoice_item1['invoice_item_discount']!=0){
            $booldis=1;
        }
    }
    if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
        $tr_item = ''; $i = 1;
        foreach ($invoice_item_arr as $invoice_item) {
            if ($invoice_item['invoice_item_type'] == 0) {
                $item = $invoice_item['invoice_item'];
            } elseif ($invoice_item['invoice_item_type'] == 1) {
                    $item = 'Ipad<br><font color="#AAAAAA">description</font>';
            } elseif ($invoice_item['invoice_item_type'] == 2) {
                $item = 'Ipad<br><font color="#AAAAAA">description</font>';
            } else {
                    $item = '<font color="red">Item type not found.</font>';
            }
            if($booldis==1){
                $dis_label='<td width="11%" align="center" ><b> Discount<br>(%)</b></td>';
                $dis_value='<td align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
                $a = '46%';
            }

            $tr_item .= '<tr>
                <td align="center">'. $i .'</td>
                <td>'. CSalesManager::htmlChars($item) .'</td>
                <td align="right">'. $invoice_item['invoice_item_quantity'] .'</td>
                <td align="right">'. number_format($invoice_item['invoice_item_price'],2) .'</td>                                
                '.$dis_value.'
                <td align="right">'. CSalesManager::calculate_total_item($invoice_item['invoice_item_price'], $invoice_item['invoice_item_quantity'], $invoice_item['invoice_item_discount']) .'</td>
                </tr>';
                $i++;
        }
    }

    $total_item_show = $this->get_invoice_item_total($invoice_id, $invoice_revision_id);

    $invoice_rev_details_arr = $this->get_latest_invoice_revision($invoice_id);

    if (count($invoice_rev_details_arr) > 0) {
        if($invoice_rev_details_arr[0]['invoice_revision_notes']!="")
//            $note_area ='<b>Notes:</b> '.$invoice_rev_details_arr[0]['invoice_revision_notes'];
            $note_area='<tr valign="top">
                <td width="100%"><b>Notes:</b> '.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_notes']) .'</td>
             </tr><tr><td height="10"></td><td></td></tr>';
        if($invoice_rev_details_arr[0]['invoice_revision_term_condition']!="")
//            $term_area ='<b>Terms and Conditions:</b> '.$invoice_rev_details_arr[0]['invoice_revision_term_condition'];
            $term_area = '<tr>
                                <td width="100%" ><b>Terms and Conditions:</b><br> '.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_term_condition']) .'</td>
                            </tr>';
        $tax_id = $invoice_rev_details_arr[0]['invoice_revision_tax'];
    }
    $subject_note ="";
    if($subject!=""){
            $subject_note.='<tr><td colspan="2" width="99%"><b>Subject:</b> '.CSalesManager::htmlChars($subject).'</td></tr>';
    }
    
    // Load ra Email ca Mobile Cua Jo Location.
    $contact_localtion =  CSalesManager::get_address_by_id($job_location_id);
    //print_r($contact_localtion);
    $option_location ="";
//    print_r($client_localtion);
    $contact_location = "";
    
//    if (count($contact_localtion) > 0) {
//        if($contact_localtion['address_email']!="")
//            $option_location.="Email: ".$contact_localtion['address_email']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
//        if($contact_localtion['address_mobile_1']!="")
//            $option_location.="Mobile: ".$contact_localtion['address_mobile_1'];
//        if($contact_localtion['address_email']!="" || $contact_localtion['address_mobile_1']!="")
//            $contact_location ="<br>".$option_location;
//    }
    
       // Contact cua Job loacation
   //$contact_arr = $CCompany->getContactInAddress($job_location_id);
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
    
    $job_location = "";
    $brand="";
    if($job_location_arr['address_branch_name']!="")
        $brand=$job_location_arr['address_branch_name'].' - ';
    if($job_location_arr['address_street_address_2']!="")
        $address_2= ', '.$job_location_arr['address_street_address_2'];
    $postal_code_job = ',Singapore ';
    if($job_location_arr['address_postal_zip_code']!="")
        $postal_code_job .=$job_location_arr['address_postal_zip_code'];
    if($job_location_arr!="")
        $job_location .= '<tr><td colspan="2" width="99%"><b>Job Location:</b> '.$brand.CSalesManager::htmlChars($job_location_arr['address_street_address_1'].$address_2.$postal_code_job).$contact.'</td></tr>';
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
    $total_item_tax_show = 0;
    if ($tax_id) {
        $caculating_tax = floatval($total_item_show) * floatval($tax_value) / 100;
        $total_item_tax_show = CSalesManager::round_up($caculating_tax);
        $total_paid_and_tax += $caculating_tax;
        if($invoice_rev_details_arr[0]['invoice_revision_tax_edit']!=0)
            $total_item_tax_show = $invoice_rev_details_arr[0]['invoice_revision_tax_edit'];
        
    }
$amount_due =  ($total_item_show + $total_item_tax_show -$tr_paid);
    $tax_select = '<select name="invoice_revision_tax" id="invoice_revision_tax" class="text">'. $tax_option .'</select>';

$tbl = '<div style="border: 1px solid black; width: 70%; margin:auto; font-family:times new roman; padding:8px;">
          <table border="0" cellspacing="0" width="100%" cellpadding="0">
                <tr valign = "top" height="60">
                    '.$img.'
                    <td width="'.$w.'" ><table border="0"  width="100%">'.$supplier.'</table></td>
                </tr>
                <tr valign="bottom"><td align="right" colspan="2"><b>Invoice No: '.$invoice_no.'</b></td></tr>
                <tr height="2"><td colspan="2" height="2"><hr></td></tr>
           </table> 
         <table border="0" width="100%" cellpadding="0">
                
                <tr valign="top">
                    <td height="20" colspan="2"><h2 style="text-align:center; margin:0px;">TAX INVOICE</h2></td>
                </tr>
                <tr valign = "top">
                    <td>'.$customer.'</td>
                    <td width="40%" height="105" >'.$info2.'</td>
 
                </tr>
          </table>
          <table border="1" width="100%" cellspacing="0" cellpadding="8">
                <tr valign="top">
                    '.$sales_contact_td.'
                </tr>
                '.$job_location.'
                '.$subject_note.'
           </table> 
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr><td>&nbsp</td></tr>
            </table>
            <table border="1" width="100%" style="border-style:solid;" class="tbl" cellspacing="0" cellpadding="2">
                
               <tr>
                    <td width="3%" align="center" height="35" ><b> #</b></td>
                    <td width="'.$a.'" height="30"><b> Item</b></td>
                    <td width="8%" align="center" ><b> Qty</b></td>
                    <td width="14%" align="center" ><b> Unit Price</b></td>
                    '.$dis_label.'
                    <td width="17%" align="center" ><b> Amount</b></td>
               </tr>
                    '. $tr_item .'
            </table>
            <table border="0" cellspacing="0" width="100%" cellpadding="0">
                <tr>
                    <td colspan="2">
                            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                <tr align="right">
                                    <td width="60%"></td>
                                    <td width="20%"><b>Total:</b></td>
                                    <td width="20%" align="right"><b>$'.number_format($total_item_show,2).'</b></td>
                                </tr>
                                <tr align="right">
                                    <td width="60%"></td>
                                    <td width="20%"><b>Discount:</b></td>
                                    <td width="20%" align="right"><b>$'.number_format($discount,2).'</b></td>
                                </tr>
                                <tr align="right">
                                    <td width="60%"></td>
                                    <td width="20%"><b>'.$tax_name.'&nbsp;&nbsp;'.$tax_value.'%:</b></td>
                                    <td width="20%" align="right"><b>$'.number_format($total_item_tax_show,2).'</b></td>
                                </tr>
                                <tr align="right">
                                    <td width="60%"></td>
                                    <td width="20%"><b>Amount Paid: </b></td>
                                    <td width="20%" align="right"><b>$'. number_format($tr_paid,2) .'</b></td>
                                </tr>
                                <tr align="right">
                                    <td width="60%"></td>
                                    <td width="20%"><b>Amount Due:</b></td>
                                    <td width="20%" align="right"><b>$'.number_format($amount_due,2).'</b></td>
                                </tr>
                            </table>
                     </td>
                </tr>
                <tr><td height="10"></td></tr>
                <tr>
                    <td colspan="2">
                        <table width="100%"  cellspacing="0" cellpadding="0">
                            '.$note_area.'
                            '.$term_area.'
                        </table>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr><td height="15"></td></tr>
                            <tr>
                                <td>Kindly make the cheque payable to " '.$sales_owner_name.' " and mail to '.$sales_owner_address.'.</td>
                            </tr>
                            <tr>
                                <td><b>Please indicate invoice number when making payment.</b></td>
                            </tr>
                            <tr >
                                <td><b>Please note that any overdue payment/s will be listed in DP Credit Bureau\'s records and this record may be accessed by financial institutions and other approving credit companies.</b></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr><td height="20"></td></tr>
                            <tr>
                                <td>Thank you & best regards</td>
                            </tr>
                            <tr>
                                <td height="30"> </td>
                            </tr>
                            <tr>
                                <td>Yours Sincerely</td>
                            </tr>
                            <tr>
                                <td height="30"> </td>
                            </tr>
                            <tr>
                                <td><b>Authorised Signature</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>   
</table></div>';
//$tbl =
//'<table>
//    <tr>
//        <td>
//            <table>
//                <tr align="center">
//                    <td colspan="2"><h1>INVOICE</h1></td>
//                </tr>
//                <tr>
//                    <td width="58%"><Supplier’s LOGO>'. $img_print .'</td>
//                    <td width="42%">
//                        <table>
//                            <tr>
//                                <td>'. $supplier .'</td>
//                            </tr>
//                            <tr>
//                                <td>'. $info2 .'</td>
//                            </tr>
//                        </table>
//                    </td>
//                </tr>
//                <tr>
//                    <td width="58%">
//                        <table>
//                            <tr>
//                                <td>'. $customer .'</td>
//                            </tr>
//                        </table>
//                    </td>
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
//                    <td width="11%"><b> Qty</b></td>
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
//                                <td><b>$'. $total_item_show .'</b></td>
//                            </tr>
//                            <tr>
//                                <td><b>Tax @ '. $tax_value .'%</b></td>
//                                <td><b>'. $total_item_tax_show .'</b></td>
//                            </tr>      
//                            <tr>
//                                <td><b>Amount Paid</b></td>
//                                <td><b>$'. $tr_paid .'</b></td>
//                            </tr>
//                            <tr>
//                                <td><b>Amount Due</b></td>
//                                <td><b>$'. $amount_due .'</b></td>
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
//            <table width="100%">
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
//                            <tr>
//                                <br/><td width ="55%">'. $sales_contact .'</td>
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
    public function update_invoice_field($id, $value, $field_name) {
        $CInvoice = new CInvoice();
        $CInvoice->load($id);
        $CInvoice->$field_name = $value;
        return $CInvoice->store();
    }

    /**
     * XXX
     *
     * @param  int $invoice_id XXX
     * @param  int $status_id XXX
     * @return boolean XXX
     * @access public
     */
    public function update_invoice_status($invoice_id, $status_id)
     {
        trigger_error('Not Implemented!', E_USER_WARNING);
    }

    
    public function list_invoice_revision($invoice_id) {
        $CInvoiceRevision = new CInvoiceRevision();
        return $CInvoiceRevision->loadAll(null, 'invoice_id = '.$invoice_id);
    }

    public function get_invoice_revision_lastest($invoice_id,$aging=false) {
       
            $q = new DBQuery();
            $q->addTable('sales_invoice_revision');
            if($aging == 1)
                $q->addQuery('invoice_revision_id,invoice_revision_discount');
            else
                $q->addQuery('invoice_revision_id');
            $q->addWhere('invoice_id = '.intval($invoice_id) . ' ORDER BY invoice_revision_id DESC');
            $return = $q->loadList();
            if($aging == 1)
                return $return[0];
            return $return[0]['invoice_revision_id'];
    }

    public function get_invoice_item_detail($invoice_item_id) {
            $q = new DBQuery();
            $q->addTable('sales_invoice_item');
            $q->addQuery('*');
            if($invoice_item_id)
                $q->addWhere('invoice_item_id = '.intval($invoice_item_id));
            return $q->loadList();
            
    }
    
    function get_invoice_item_total($invoice_id, $invoice_revision_id) {
            $q = new DBQuery();
            $q->addTable('sales_invoice_item');
            $q->addQuery('SUM(invoice_item_price*invoice_item_quantity - invoice_item_price*invoice_item_quantity*(invoice_item_discount/100)) as data');
            $q->addWhere('invoice_id = '.intval($invoice_id).' AND invoice_revision_id = '.intval($invoice_revision_id));
            $rows = $q->loadList();
            if(count($rows) > 0)
                return $rows[0]['data'];
            return 0;
            
    }
    
    public function get_invoice_total_last_discount($invoice_id,$invoice_revision_id,$total_show,$invoice_revision_discount=false)
    {
        $total_last_discount = 0;
        if(isset($total_show))
            $total=$total_show;
        else
            $total = $this->get_invoice_item_total($invoice_id, $invoice_revision_id);
        if(!isset($invoice_revision_discount) || $invoice_revision_discount=="")
        {
            $q = new DBQuery();
            $q->addTable('sales_invoice_revision');
            $q->addQuery('invoice_revision_discount');
            $q->addWhere('invoice_revision_id = '.intval($invoice_revision_id));
            $invoice_revision_arr =$q->loadList();
          
            $total_last_discount = $total - $invoice_revision_arr[0]['invoice_revision_discount'];
        }
        else
        {
          
            $total_last_discount = $total - $invoice_revision_discount;
        }
            
        return $total_last_discount;
    }
    
    public function get_invoice_field_by_id($invoice_id, $field_name) {
            $q = new DBQuery();
            $q->addTable('sales_invoice');
            $q->addQuery($field_name);
            $q->addWhere('invoice_id = '.intval($invoice_id));
            $return = $q->loadList();
            return $return[0][$field_name];
    }
    
    public function get_invoice_no_by_revision_id($invoice_revision_id) {
            $q = new DBQuery();
            $q->addTable('sales_invoice_revision', 'tbl1'); 
            $q->addQuery('tbl1.invoice_id, tbl2.invoice_no');
            $q->addJoin('sales_invoice', 'tbl2', 'tbl1.invoice_id = tbl2.invoice_id');
            $q->addWhere('tbl1.invoice_revision_id = '.intval($invoice_revision_id));
            $return = $q->loadList();
            return $return[0]['invoice_no'];
    }
    
    public function get_total_tax_and_paid($invoice_revision_id, $total_show,$return) {
            
            $total_paid_and_tax = $this->get_total_amount_paid1($invoice_revision_id);
           
            if(!isset($return))
            {   
              
                $q = new DBQuery();
                $q->addTable('sales_invoice_revision', 'tbl1'); 
                $q->addQuery('tbl1.invoice_revision_tax, tbl2.tax_rate, tbl1.invoice_revision_tax_edit, tbl1.invoice_revision_discount');
                $q->addJoin('sales_tax', 'tbl2', 'tbl1.invoice_revision_tax = tbl2.tax_id');
                $q->addWhere('tbl1.invoice_revision_id = '.intval($invoice_revision_id));
                $return = $q->loadList();
            }

            $tax_value = $return[0]['tax_rate'];
            
            $total_show_last_discount = $total_show - $return[0]['invoice_revision_discount'];
            if ($tax_value) {
                
                if($return[0][invoice_revision_tax_edit]!=0)
                    $caculating_tax = $return[0][invoice_revision_tax_edit];
                else
                {
                    $caculating_tax = floatval($total_show_last_discount) * floatval($tax_value) / 100;
                    $caculating_tax = $this->round_up1($caculating_tax);
                }
                    
            }
            $total = $total_show_last_discount + $caculating_tax;
            $amount_due = round($total,2) - round($total_paid_and_tax,2);
          
            return $amount_due;
    }
    
    public function getTaxPaid($invoice_revision_id)
    {
        $q = new DBQuery();
        $q->addTable('sales_invoice_revision', 'tbl1'); 
        $q->addQuery('tbl1.invoice_revision_tax, tbl2.tax_rate, tbl1.invoice_revision_tax_edit, tbl1.invoice_revision_discount');
        $q->addJoin('sales_tax', 'tbl2', 'tbl1.invoice_revision_tax = tbl2.tax_id');
        $q->addWhere('tbl1.invoice_revision_id = '.intval($invoice_revision_id));
        $return = $q->loadList();
        return $return;
    }
    
     /**
     *
     * @param 
     */  
    public function count_invoice_revision_id($invoice_id) {  // dem so invoice_revision_id cua mot invoice_id
        $q = new DBQuery();
        $q->addTable('sales_invoice_revision');
        $q->addQuery('count(invoice_revision_id)');
        $q->addWhere('invoice_id = '.intval($invoice_id));
        $rows = $q->loadList();
        return $rows[0]['count(invoice_revision_id)'];
    }
    
    public function check_payment_id($invoice_id) {  // kiem tra invoice_id da duoc thanh toan chua
        $invoice_revision_id = $this->get_invoice_revision_lastest($invoice_id);
        $q = new DBQuery();
        $q->addTable('sales_payment_detail');
        $q->addQuery('payment_id');
        $q->addWhere('invoice_revision_id = '.$invoice_revision_id);
        $rows = $q->loadList();
        if ($rows)
            return true;
        else
            return false;
    }
    
    public function get_quotation_id($invoice_id) { // lay bao gia cua invoice
        $q = new DBQuery();
        $q->addTable('sales_invoice');
        $q->addQuery('quotation_id');
        $q->addWhere('invoice_id = '. $invoice_id);
        $rows = $q->loadList();
        return $rows[0]['quotation_id'];
    }
    
    public function get_quotation_field_by_id($quotation_id, $field_name) {
        $q = new DBQuery();
        $q->addTable('sales_quotation');
        $q->addQuery($field_name);
        $q->addWhere('quotation_id = '.intval($quotation_id));
        $return = $q->loadList();
        return $return[0][$field_name];
    }
    
    public function get_quotation_revision_field_by_id($quotation_id) {
            $q = new DBQuery();
            $q->addTable('sales_quotation_revision');
            $q->addQuery('quotation_revision_id');
            $q->addWhere('quotation_id = '.intval($quotation_id) . ' ORDER BY quotation_revision_id DESC  LIMIT 0, 1');
            $return = $q->loadList();
            return $return[0]['quotation_revision_id'];
    }
    
    public function add_invoice_item_2($invoice_id, $invoice_revision_id) {
        global $AppUI;
        $item = $_POST['invoice_item'];
        $price = $_POST['invoice_item_price'];
        $quantity = $_POST['invoice_item_quantity'];
        $discount = $_POST['invoice_item_discount'];
        if (($count = count($item)) > 0) {
            
            $item_obj = array();
            
                $item_obj['user_id'] = $AppUI->user_id;
                $item_obj['invoice_id'] = $invoice_id;
                $item_obj['invoice_revision_id'] = $invoice_revision_id;

            for ($i = 0; $i < $count; $i++) {
                $item_obj['invoice_item_id'] = 0;
                $item_obj['invoice_item'] = $item[$i];
                $item_obj['invoice_item_price'] = $price[$i];
                $item_obj['invoice_item_quantity'] = $quantity[$i];
                $item_obj['invoice_item_discount'] = intval($discount[$i]);
                $item_obj['invoice_item_type'] = 0;
                $item_obj['invoice_item_notes'] = null;
                $this->add_invoice_item($item_obj);
            }
        }
    }
    
    public function insert_history_invoice($invoice_id, $invoice_revision_no_current, $invoice_revision_no = false, $status=false) {
        global $AppUI;
        //cat lay 2 so cuoi cua revision
        $suffix_count = 3;
        $obj_revision_substr = substr($invoice_revision_no, -$suffix_count);
        $obj_revision_substr_from = substr($invoice_revision_no_current, -$suffix_count);
        $user_id = $AppUI->user_id;
        $date = date("Y-m-d H:i:s");    
        
        $Obj = new CQuotationInvoiceHistory();
        $Obj->quo_invc_history_id = 0;
        $Obj->quo_invc_id = $invoice_id;
        $Obj->quo_or_invc_history = 1;
        $Obj->quo_invc_history_type = 0;
        if($status=="add")
            $Obj->quo_invc_history_update = 'Add invoice revision '.$invoice_revision_no;
        else if($status=="delete")
            $Obj->quo_invc_history_update = 'Delete invoice revision ' .$obj_revision_substr_from;
        elseif($status=="update")
            $Obj->quo_invc_history_update = 'Move from revision ' .$obj_revision_substr_from . ' to ' .$obj_revision_substr;
        else 
            $Obj->quo_invc_history_update = 'Update invoice revision ' .$obj_revision_substr_from;
        $Obj->quo_invc_history_user = $user_id;
        $Obj->quo_invc_history_date = $date;
        return $Obj->store();

    }
    
    public function get_list_db_history($invoice_id) {
        $q = new DBQuery();
        $q->addTable('quo_invc_history');
        $q->addQuery('*');
        $q->addWhere('quo_invc_id = '.$invoice_id.' AND quo_or_invc_history = 1');
        return $q->loadList();
    }
    
    public function get_user_change($user_id) {
        $q = new DBQuery();
        $q->addTable('users');
        $q->addQuery('user_username');
        $q->addWhere('user_id = '.$user_id);
        $row = $q->loadList();
        return $row[0]['user_username'];
    
    }
    public function insert_history_invoice_status($invoice_id, $value, $status) {
        global $AppUI;
        
        $invoice_stt = dPgetSysVal('InvoiceStatus');
        
        $user_id = $AppUI->user_id;
        $date = date("Y-m-d H:i:s");    
            $Obj = new CQuotationInvoiceHistory();
            $Obj->quo_invc_history_id = 0;
            $Obj->quo_invc_id = $invoice_id;
            $Obj->quo_or_invc_history = 1;
            $Obj->quo_invc_history_type = 1;
            $Obj->quo_invc_history_update = 'Update status from ' .$invoice_stt[$status] . ' to ' .$invoice_stt[$value];
            $Obj->quo_invc_history_user = $user_id;
            $Obj->quo_invc_history_date = $date;
            return $Obj->store();
    }
    public function list_db_invoice($date_start="",$date_end="",$custormer_id=false){
        $q = new DBQuery();
        $q->addTable('sales_invoice','tbl1');
        $q->addQuery('tbl2.company_name,tbl1.*');
        $q->addJoin('clients', 'tbl2', 'tbl2.company_id=tbl1.customer_id');
        if($date_start!=""){
            $q->addWhere('tbl1.invoice_date >="'.$date_start.'"');
        }
        if($date_end!=""){
            $q->addWhere('tbl1.invoice_date <="'.$date_end.'"');
        }
        if($custormer_id){
            $q->addWhere('tbl1.customer_id IN '.$custormer_id);
        }
        $q->addOrder('tbl1.invoice_no ASC');
        return $q->loadList();
    }
 
    /**
     * XXX
     * 
     * @param  date $date // thoi gian lap hoa don
     * @param  int $term // ky han phai tra
     * @param  float $total_owe //tong so tien con no
     * @return char XXX
     * @access public
     */
    public function get_check_term($date = false, $term = false, $total_owe = false){
        $day_between =round((strtotime(date("Y/m/d"))-strtotime($date))/86400);
        $term_status ="";
        $total_owe = round($total_owe,2);
        if($total_owe <= 0 && $term>=0)
             $term_status="Paid";
        else if($total_owe > 0 && $day_between<=$term)
             $term_status="Partial";
        else if($day_between > $term)
            $term_status="Overdue";
        return $term_status;
    }
    
    public function update_invoice_item($id,$inv_item,$inv_item_price,$inv_item_quantily,$inv_item_discount, $inv_item_order){
        $invoice_item = new CInvoiceItem();
        $invoice_item ->load($id);
        $invoice_item->invoice_item = $inv_item;
        $invoice_item->invoice_item_price = $inv_item_price;
        $invoice_item->invoice_item_quantity = $inv_item_quantily;
        $invoice_item->invoice_item_discount = $inv_item_discount;
        $invoice_item->invoice_order = $inv_item_order;
        return $invoice_item ->store();
    }
    
    public function max_inv_order_item($invoice_id,$invoice_rev_id){
        $q = new DBQuery();
        $q->addTable('sales_invoice_item');
        $q->addQuery('invoice_order');
        $q->addWhere ('invoice_id ='.intval($invoice_id));
        $q->addWhere('invoice_revision_id='.intval($invoice_rev_id));
        $q->addOrder('invoice_order DESC');
        $row=$q->loadList();
        return $row[0]['invoice_order'];
    }
    
    public function update_invoice_item_order($id,$invoice_item_order){
        $invoice_item = new CInvoiceItem();
        $invoice_item ->load($id);
        $invoice_item ->invoice_order = $invoice_item_order;
        return $invoice_item ->store();
    }
    
    // Xem invoice tiep theo chua tra het tien.
    // $move = {next,prev,first,last};
    public function moveInvoice($invoice_no,$move){
        if($invoice_no =="0")
            $invoice_no = " ";
        if($move == "next"){
            $sql="SELECT invoice_id, invoice_status FROM sales_invoice "
                    . "WHERE invoice_status IN (0,1,3) "
                    . "AND invoice_no >'".$invoice_no ."'"
                    . "ORDER BY invoice_no ASC LIMIT 1";
            $rows = db_loadList($sql);
       }
       else if($move == "last"){
            $sql="SELECT invoice_id, invoice_status FROM sales_invoice "
                    . "WHERE invoice_status IN (0,1,3) "
                    . "AND invoice_no > '".$invoice_no ."'"
                    . " ORDER BY invoice_no DESC LIMIT 1";
            $rows = db_loadList($sql);
       }
       else if($move == "prev"){
            $sql="SELECT invoice_id, invoice_status FROM sales_invoice "
                    . "WHERE invoice_status IN (0,1,3) "
                    . "AND invoice_no < '".$invoice_no."'"
                    . " ORDER BY invoice_no DESC LIMIT 1";
            $rows = db_loadList($sql);
       }
       else if($move == "first"){
            $sql="SELECT invoice_id, invoice_status FROM sales_invoice "
                    . "WHERE invoice_status IN (0,1,3) "
                    . "AND invoice_no < '".$invoice_no ."'"
                    . " ORDER BY invoice_no ASC LIMIT 1";
            $rows = db_loadList($sql);
       }
       return $rows;  
    }
    public function getInvoicesOfCompany($company_id = false, $address_id = false) {
        $q = new DBQuery();
        $q->addTable('sales_invoice');
        $q->addQuery('sales_invoice.invoice_id');
        if ($company_id)
            $q->addWhere('customer_id = '.intval($company_id));
        if ($address_id)
            $q->addWhere('address_id = '.intval($address_id));
        
        return $q->loadList();
    }
    public function add_invoiceAttention($invoiceAtentionObj) {
        $CInvoiceAttention = new CInvoiceAttention();
        $CInvoiceAttention->bind($invoiceAtentionObj);
        $CInvoiceAttention->store();
        return $CInvoiceAttention->invoice_attention_id;
    }
    function get_attention_by_invoice($invoice_id){
        $q = new DBQuery();
        $q->addTable('contacts','tbl1');
        $q->addQuery('tbl1.contact_id,tbl1.contact_first_name,tbl1.contact_last_name,tbl1.contact_email');
        $q->addJoin('sales_invoice_attention', 'tbl2', 'tbl1.contact_id=tbl2.attention_id');
        $q->addWhere('tbl2.invoice_id='.intval($invoice_id));
        return $q->loadList();
    }
    public function get_contactCustomer_by_company($customer_id){
        $q=new DBQuery();
        $q->addTable('contacts', 'tbl1');
        $q->addQuery('tbl1.contact_first_name,tbl1.contact_last_name');
        $q->addJoin('company_contact', 'tbl5', 'tbl5.contact_id=tbl1.contact_id','inner');
        $q->addJoin('users', 'tbl2', 'tbl2.user_contact=tbl1.contact_id' );
        $q->addJoin('gacl_aro', 'tbl3', 'tbl3.value=tbl2.user_id');
        $q->addJoin('gacl_groups_aro_map', 'tbl4', 'tbl3.id=tbl4.aro_id');
        $q->addWhere('tbl5.company_id='.intval($customer_id));
        $q->addWhere('tbl4.group_id=24');
        return $q->loadList();
    }
    public function get_total_invoice_by_customer($customer_id){
        $q=new DBQuery();
        $q->addTable('sales_invoice');
        $q->addWhere('customer_id='.intval($customer_id));
        $row = $q->loadList();
        return $row;
    }
    public function calculate_total_item1($price, $quantity, $discount) {
        //setlocale(LC_MONETARY, 'en_US'); // Cai dat format tien te moi
        $amount_item = (($price * $quantity) - ($price * $quantity * ($discount/100)));
        $amount_item = $this->round_up1($amount_item);
        return $amount_item;
    }
    public static function round_up1($x){
        $r = round($x,2);
        $tmp = $x-$r;
        $tmp = round($tmp,4);
        if($tmp>0)
            return $r+0.01;
        return $r;
    }
    public function get_total_amount_paid1($invoice_revistion_id,$date_to=false){
            $q = new DBQuery();
            $q->addTable('sales_payment_detail');
            $q->addQuery('SUM(payment_amount)');
            $q->addWhere('invoice_revision_id = '. intval($invoice_revistion_id));
            $rows = $q->loadList();
            
            $total_paid = $rows[0]['SUM(payment_amount)'];
           
            return $total_paid;
    }
    public function get_invoice_recAndPartial($customer_id) {
            $q = new DBQuery();
            $q->addTable('sales_invoice');
            $q->addQuery('invoice_id');
//            if($customer_id)
//                $q->addWhere('customer_id = '.intval($customer_id));
            $q->addWhere ('customer_id = '.intval($customer_id).' AND (invoice_status = 0 OR invoice_status = 3)');
            return $q->loadList();
        
    }
    public function get_customer(){
        $q = new DBQuery();
        $q->addTable('clients'); 
        $q->addQuery('*'); 
        $q->addOrder('company_id ASC');
        return $q->loadList();
    }
    public function get_total_item_invoice_and_tax($invoice_id,$invoice_revision_id,$total_item_last_discount=false,$tax_rate_edit=false,$tax_value=false){
        if(!isset($tax_rate_edit) || !isset($tax_value) || $tax_rate_edit=="")
        {
            $q = new DBQuery();
            $q->addTable('sales_invoice_revision', 'tbl1'); 
            $q->addQuery('tbl1.invoice_revision_tax, tbl2.tax_rate, tbl1.invoice_revision_tax_edit');
            $q->addJoin('sales_tax', 'tbl2', 'tbl1.invoice_revision_tax = tbl2.tax_id');
            $q->addWhere('tbl1.invoice_revision_id = '.intval($invoice_revision_id));
            $return = $q->loadList();
            $tax_value = $return[0]['tax_rate'];
            $tax_rate_edit=$return[0]['invoice_revision_tax_edit'];
            
        }
        
        //$total_show = $this->get_invoice_item_total($invoice_id, $invoice_revision_id);
        if(isset($total_item_last_discount) && $total_item_last_discount!="")
            $total_show=$total_item_last_discount;
        else
            $total_show = $this->get_invoice_total_last_discount($invoice_id, $invoice_revision_id);

        if ($tax_value) {
            $caculating_tax = floatval($total_show) * floatval($tax_value) / 100;
            
//            if($return[0]['invoice_revision_tax_edit']!=0)
            $caculating_tax = $tax_rate_edit;
        }
        $total = $total_show + $caculating_tax;
        $amount = round($total,2);
        return $amount;
    }
    
    
    public function get_gst_item_invoice_and_tax($invoice_id,$invoice_revision_id){
        $q = new DBQuery();
        $q->addTable('sales_invoice_revision', 'tbl1'); 
        $q->addQuery('tbl1.invoice_revision_tax, tbl2.tax_rate, tbl1.invoice_revision_tax_edit');
        $q->addJoin('sales_tax', 'tbl2', 'tbl1.invoice_revision_tax = tbl2.tax_id');
        $q->addWhere('tbl1.invoice_revision_id = '.intval($invoice_revision_id));
        $return = $q->loadList();
        $tax_value = $return[0]['tax_rate'];
        
        //$total_show = $this->get_invoice_item_total($invoice_id, $invoice_revision_id);
        $total_show = $this->get_invoice_total_last_discount($invoice_id, $invoice_revision_id);

        if ($tax_value) {
            $caculating_tax = floatval($total_show) * floatval($tax_value) / 100;
            //$caculating_tax = $this->round_up1($caculating_tax);
            if($return[0]['invoice_revision_tax_edit']!=0)
                $caculating_tax = $return[0]['invoice_revision_tax_edit'];
        }
        $total = $total_show + $caculating_tax;
        $amount = round($total,2);
        return $caculating_tax;
    }
//   public function list_invoice_statement($customer_id=false, $status_id=false,$order_date=false, $date_from=false, $date_to=false, $address_id = false) {
//
////        if ($user_id != '' && $user_id != null) {
////            $where = 'tbl1.user_id = '.intval($user_id);
////            if ($customer_id != '' && $customer_id != null)
////                $where .= ' AND tbl1.customer_id = '.intval($customer_id);
////            if ($status_id != '' && $status_id != null)
////                $where .= ' AND tbl1.invoice_status = '.intval($status_id);
//
//            $q = new DBQuery();
//            $q->addTable('sales_invoice', 'tbl1');
//            $q->addQuery('tbl1.*, tbl3.company_name');
//            //$q->addQuery('tbl1.*, tbl3.company_name, tbl2.invoice_revision, tbl2.invoice_revision_id');
//            //$q->addJoin('sales_invoice_revision', 'tbl2', "tbl2.invoice_id = tbl1.invoice_id");
//            $q->addJoin('clients', 'tbl3', "tbl3.company_id = tbl1.customer_id");
//            if($customer_id != '' && $customer_id != null && $customer_id)
//                $q->addWhere('tbl1.customer_id = '.intval($customer_id));
//            if($status_id==4)
//                $q->addWhere ('tbl1.invoice_status <> 2');
//            else if(($status_id==0 || $status_id) && $status_id!="")
//                $q->addWhere ('tbl1.invoice_status = '.  intval($status_id));
//            if($date_from)
//                $q->addWhere ('tbl1.invoice_date >= "'.$date_from.'"');
//            if($address_id)
//                $q->addWhere ('tbl1.address_id='.intval ($address_id));
//            if($date_to)
//                $q->addWhere ('tbl1.invoice_date <= "'.$date_to.'"');
//            if($order_date)
//                $q->addOrder('tbl1.invoice_date ASC');
//            else
//                $q->addOrder('tbl1.invoice_date DESC');
//            return $q->loadList();
//
////        } else {
////            return null;
////        }
//        
//    }
    
public function list_invoice_statement($customer_id=false, $status_id=false,$order_date=false, $date_from=false, $date_to=false, $address_id = false) {

//        if ($user_id != '' && $user_id != null) {
//            $where = 'tbl1.user_id = '.intval($user_id);
//            if ($customer_id != '' && $customer_id != null)
//                $where .= ' AND tbl1.customer_id = '.intval($customer_id);
//            if ($status_id != '' && $status_id != null)
//                $where .= ' AND tbl1.invoice_status = '.intval($status_id);

            $q = new DBQuery();
            $q->addTable('sales_invoice', 'tbl1');
            $q->addQuery('tbl1.*, tbl3.company_name');
            //$q->addQuery('tbl1.*, tbl3.company_name, tbl2.invoice_revision, tbl2.invoice_revision_id');
            //$q->addJoin('sales_invoice_revision', 'tbl2', "tbl2.invoice_id = tbl1.invoice_id");
            $q->addJoin('clients', 'tbl3', "tbl3.company_id = tbl1.customer_id");
            if($customer_id)
                $q->addWhere('tbl1.customer_id IN '.$customer_id);
            if($status_id==4)
                $q->addWhere ('tbl1.invoice_status <> 2');
            else if(($status_id==0 || $status_id) && $status_id!="")
                $q->addWhere ('tbl1.invoice_status = '.  intval($status_id));
            if($date_from)
                $q->addWhere ('tbl1.invoice_date >= "'.$date_from.'"');
            if($address_id)
                $q->addWhere ('tbl1.address_id='.intval ($address_id));
            if($date_to)
                $q->addWhere ('tbl1.invoice_date <= "'.$date_to.'"');
            if($order_date)
                $q->addOrder('tbl1.invoice_date ASC');
            else
                $q->addOrder('tbl1.invoice_date DESC');
            return $q->loadList();

//        } else {
//            return null;
//        }
        
}
public function get_invoice_statusNone($update_payment=false,$invoice_id=false){
    $q = new DBQuery();
    $q->addTable('sales_invoice');
    $q->addQuery('*');
    if($invoice_id){
        $q->addWhere('invoice_id = '.intval($invoice_id));
    }
    else{
        if($update_payment)
            $q->addWhere('invoice_status IN (2,3)');
        else
            $q->addWhere('invoice_status IN (0,1,2,3)');
    }
    $q->addOrder('invoice_id DESC');
    return $q->loadList();
}
public function get_joblocation_inner_invoice($customer_id=false){
    $q = new DBQuery();
    $q->addQuery('tbl1.address_id,tbl1.company_id,tbl1.address_street_address_1,tbl1.address_street_address_2,tbl1.address_branch_name,tbl1.address_postal_zip_code,tbl3.company_name');
    $q->addTable('addresses','tbl1');
    $q->addJoin('sales_invoice', 'tbl2', 'tbl1.address_id=tbl2.job_location_id','INNER');
    $q->addJoin('clients', 'tbl3', 'tbl3.company_id=tbl1.company_id','INNER');
    if($customer_id)
        $q->addWhere ('tbl1.company_id IN ('. $customer_id. ')');
    $q->addOrder('tbl3.company_name ASC');
    $q->addGroup('tbl1.address_id');
    return $q->loadList();
}

public function get_invoice_by_jolocation($job_id){
    $q = new DBQuery();
    $q->addTable('sales_invoice');
    $q->addQuery('*');
    $q->addWhere('job_location_id='.intval($job_id));
    $q->addOrder('invoice_no ASC');
    return $q->loadList();
}

public function get_customer_inner_invoice($address_id,$customer_id){
    $q = new DBQuery();
    $q->addQuery('invoice_id,invoice_no,customer_id,invoice_date,job_location_id');
    $q->addTable('sales_invoice');
    $q->addWhere('address_id ='.$address_id);
    $q->addWhere('customer_id = '.$customer_id);
    $q->addWhere('invoice_status <> 2');
    $q->addOrder('invoice_no ASC');
    return $q->loadList();
}
public function get_customer_by_invoice($customer_arr=false){
    $q = new DBQuery();
    $q->addQuery('tbl1.company_id,tbl2.address_id,tbl1.company_name');
    $q->addTable('clients','tbl1');
    $q->addJoin('sales_invoice', 'tbl2', 'tbl1.company_id=tbl2.customer_id','INNER');
    //$q->addJoin('addresses','tbl3','tbl3.address_id=tbl1.address_id');
    if($customer_arr)
        $q->addWhere('tbl1.company_id IN ('.$customer_arr.')');
    $q->addOrder('tbl1.company_name ASC');
    $q->addOrder('tbl2.address_id DESC');
    //$q->addGroup('tbl2.address_id');
    return $q->loadList();
}
public function update_invoice_rev_tax($id,$tax_edit){
    $Revision = new CInvoiceRevision();
    $Revision->load($id);
    $Revision->invoice_revision_tax_edit = $tax_edit;
    return $Revision->store();
        
}
public function get_total_amount_paid1_date($invoice_revistion_id=false,$date_to=false){
    $q = new DBQuery();
    $q->addTable('sales_payment_detail','tbl1');
    $q->addQuery('tbl1.payment_amount,tbl2.payment_date');
    $q->addJoin('sales_payment','tbl2','tbl1.payment_id = tbl2.payment_id');
//    $q->addWhere('tbl2.credit_note_id = 0 ');
    if($invoice_revistion_id)
        $q->addWhere('tbl1.invoice_revision_id = '. intval($invoice_revistion_id));
    if($date_to)
        $q->addWhere ('tbl2.payment_date <= "'.$date_to.'"');
    $rows = $q->loadList();
    
    $total_paid = 0;
    if($rows>0){
        foreach ($rows as $row) {
            $total_paid += floatval($row['payment_amount']);
        }
    }
    return $total_paid;
}

public function get_total_tax_and_paid_date($invoice_revision_id, $total_show , $date_to,$return) {

        //$total_paid_and_tax = CSalesManager::get_total_amount_paid($invoice_revision_id);
        $total_paid_and_tax = $this->get_total_amount_paid1_date($invoice_revision_id,$date_to);
        if(!isset($return))
        {
            $q = new DBQuery();
            $q->addTable('sales_invoice_revision', 'tbl1'); 
            $q->addQuery('tbl1.invoice_revision_tax, tbl2.tax_rate, tbl1.invoice_revision_tax_edit,tbl1.invoice_revision_discount');
            $q->addJoin('sales_tax', 'tbl2', 'tbl1.invoice_revision_tax = tbl2.tax_id');
            $q->addWhere('tbl1.invoice_revision_id = '.intval($invoice_revision_id));
            $return = $q->loadList();
        }
        $tax_value = $return[0]['tax_rate'];
        
        $total_last_discount = $total_show - $return[0]['invoice_revision_discount'];
        if ($tax_value) {
            $caculating_tax = floatval($total_last_discount) * floatval($tax_value) / 100;
            $caculating_tax = $this->round_up1($caculating_tax);
            if($return[0][invoice_revision_tax_edit]!=0)
                $caculating_tax = $return[0][invoice_revision_tax_edit];
        }
        $total = $total_last_discount + $caculating_tax;
        $amount_due = round($total,2) - round($total_paid_and_tax,2);
        if($amount_due <=0)
            return 0;
        else
            return $amount_due;
}

public function get_invoice_history_today()
{
    $q = new DBQuery();
    $q->addTable('quo_invc_history');
    $q->addQuery('quo_invc_id');
    $q->addWhere('CAST(quo_invc_history_date AS DATE)="'.date('Y-m-d').'"');
    $q->addWhere('quo_or_invc_history=1');
    return $q->loadList();
}
public function check_item_invoice($invoice_id,$invoice_revision_id,$invoice_item,$invoice_item_id){
    $q = new DBQuery();
    $q->addTable('sales_invoice_item');
    $q->addQuery('*');
    $q->addWhere('invoice_id='.intval($invoice_id));
    $q->addWhere('invoice_revision_id='.intval($invoice_revision_id));
    $q->addWhere('invoice_item_id!='.intval($invoice_item_id));
    $rows = $q->loadList();
    foreach ($rows as $row) {
        if($row['invoice_item']==$invoice_item)
            return false;
    }
    return true;
}
public function create_invoice_cron_pdf_file($invoice_id_arr, $invoice_revision_id_arr, $dir) {
        //require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        require_once (DP_BASE_DIR . '/modules/sales/CHeaderInvoice.php');
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        require_once (DP_BASE_DIR . "/modules/contacts/contacts_manager.class.php");
        require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
        require_once(DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
        $CInvoiceManager = new CInvoiceManager();
        require_once(DP_BASE_DIR."/modules/sales/CSalesManager.php");
        $CSalesManager = new CSalesManager();
        $cconfig = new CConfigNew();
        
        $ContactManager = new ContactManager();
        
        $pdf = new MYPDFInvoice(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //$pdf->SetMargins(10,130, 6,-70);
        $pdf->setCellMargins(0, 0, 0, 0);
        $pdf->SetHeaderMargin(3);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->setFooterMargin(-3);
        $pdf->SetAutoPageBreak(true, 45);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        $pdf->setPrintFooter(true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 10.3);
        $pdf->SetMargins(10,10, 8, -15);
        
        if ($invoice_revision_id_arr == null) {
            if (($count = count($invoice_id_arr)) > 0) {
                for ($i = 0; $i < $count; $i++) {
                    $invoice_id = $invoice_id_arr[$i];
                    $invoice_revision_id = $this->get_invoice_revision_lastest($invoice_id);
                }
            }
        } else {
             $invoice_id = $invoice_id_arr;
             $invoice_revision_id = $invoice_revision_id_arr;
       }
       /* Info Supplier */
        $supplier_arr = CSalesManager::get_supplier_info();
        if (count($supplier_arr) > 0) {
        $sales_owner_name = $supplier_arr['sales_owner_name'];
        $sales_owner_address =$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'];
        //$sales_owner_address += 
        $phone =$supplier_arr['sales_owner_phone1'];
        $fax = $supplier_arr['sales_owner_fax'];
        $email =$supplier_arr['sales_owner_email'];
        $web = $supplier_arr['sales_owner_website'];
        //$sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
        $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
        }

        $supplier = '<tr><td><b>'. CSalesManager::htmlChars($sales_owner_name) .'</b></td></tr>';
        $supplier.= '<tr><td>'.CSalesManager::htmlChars($sales_owner_address).'</td></tr>';
        $supplier .= '<tr><td>Tel: '.$phone.', Fax: '.$fax.'</td></tr>';
    //    $supplier .= '<tr><td>Fax: '.$fax.'</td></tr>';
        $supplier .= '<tr><td>GST Reg No: '. CSalesManager::htmlChars($sales_owner_gst_reg_no) .'</td></tr>';
    //    $supplier.= '<tr><td>Reg No: '. $supplier_arr['sales_owner_postal_code'] .'</td></tr>';
        $supplier .= '<tr><td>Email: '.CSalesManager::htmlChars($email).'</td></tr>';
        if($web!="")
            $supplier .= '<tr><td>Website: '.$web.'</td></tr>';
        // End 
       
        // Get LOGO sales //
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        $files = scandir($url);
        $i = count($files) -1;
        if (count($files) > 2 && $files[$i]!=".svn") {
            $path_file = $url . $files[$i];
            $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" /></td>';
        } // Get LOGO sales //
        
        $invoice_details_arr = $CInvoiceManager->get_db_invoice($invoice_id);
        
        $invocie_id = $invoice_details_arr[0]['invoice_id'];
        $date = $invoice_details_arr[0]['invoice_date'];
        $invoice_no = $invoice_details_arr[0]['invoice_no'];
        $term_value = $invoice_details_arr[0]['term'];
        $po_number = $invoice_details_arr[0]['po_number'];
        $our_delivery = $invoice_details_arr[0]['our_delivery_order_no'];

        $sale_person_name = $invoice_details_arr[0]['invoice_sale_person'];
        $sale_person_email = $invoice_details_arr[0]['invoice_sale_person_email'];
        $sale_person_phone = $invoice_details_arr[0]['invoice_sale_person_phone'];
        $sale_coordinator_id = $invoice_details_arr[0]['contact_coordinator_id'];

        $client_id = $invoice_details_arr[0]['customer_id'];
        $address_id = $invoice_details_arr[0]['address_id'];
        $attention_id = $invoice_details_arr[0]['attention_id'];
        $contact_id= $invoice_details_arr[0]['contact_id'];
        $job_location_id = $invoice_details_arr[0]['job_location_id'];
        $subject = $invoice_details_arr[0]['invoice_subject'];
        $invoice_co = $invoice_details_arr[0]['invoice_CO'];
        
        $invoice_rev_arr = $CInvoiceManager->get_db_invoice_revsion($invoice_revision_id);
        $discount = 0;
        if(count($invoice_rev_arr)>0)
            $discount = $invoice_rev_arr[0]['invoice_revision_discount'];
        $invoice_rev = $invoice_rev_arr[0]['invoice_revision'];
        
        // ############Info Invoice##########
        $term_arr = dPgetSysVal('Term');
        $title_arr = dPgetSysVal('CustomerTitle');
        foreach ($term_arr as $key => $value) {
            if($key == $term_value)
                if($value==1)
                    $term = $value.' day';
                else if(is_numeric($value))
                    $term = $value.' days';
                else
                    $term = $value;
        }
        $info2 = 'Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;: '.date('d-m-Y',  strtotime($date)).'<br>';
        $info2 .= 'Your P.O Number &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            : '. CSalesManager::htmlChars($po_number).'<br>';
        $info2 .= 'Terms: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            : '. $term.'<br>';
        $info2 .= 'Our Delivery Order No: '.CSalesManager::htmlChars($our_delivery).'<br>';

        // End info Invocie
        
        // company name && CO
        $customer_arr = $CSalesManager->get_customer_name($client_id);
        $customer_co_arr = $CSalesManager->get_customer_name($invoice_co);

        // Get address
        $client_address =  CSalesManager::get_address_by_id($address_id);

            if (count($client_address) > 0) {
                if($client_address['address_branch_name']!="")
                    $option.=$client_address['address_branch_name'].'<br>';
                 $option.= $client_address['address_street_address_1'];
                if($client_address['address_street_address_2']!="")
                    $option.=', '.$client_address['address_street_address_2'];
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
         $quo_attention_arr = CSalesManager::get_salesAttention_by_SalesType($invoice_id, "invoice");
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
         
         $title = "";
        if($title_arr[$customer_arr[0]['company_title']]!="")
            $title = $title_arr[$customer_arr[0]['company_title']].' ';
        $customer = '<b>Bill To: '.$title. $customer_arr[0]['company_name'] .'</b>';
        $quotation_co_name = $customer_co_arr[0]['company_name'];
        if($quotation_co_name!="")
            $customer .='<br/><b>C/O: '.$quotation_co_name.'</b>';
        if($address!="")
            $customer .= '<br/>'. $address;

        if(count($attention_option_print)>0)
            $customer .= '<br/>Attention: '. implode (' / ', $attention_option_print).'';
        

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
                $contact.="<br>Contact: ".$contact_arr['contact_first_name'].' '.$contact_arr['contact_last_name'].'.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
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
                $sales_agent .="<br>Service Coordinator: ".$CInvoiceManager->get_user_change($sale_coordinator_id);;
            if($sales_agent!=""){
                    $sales_agent.='<tr><td colspan="6" width="99%" style="border:1px soild #000;"><b>Sales Agent:</b> '.$brand.CSalesManager::htmlChars($sales_agent).'</td></tr>';
            }
            
            $footer_tex = '
                            <tr><td ></td></tr>
                            <tr>
                                <td colspan="5" height="40">Kindly make the cheque payable to " '.$sales_owner_name.' " and mail to '.$sales_owner_address.'.</td>
                            </tr>
                            <tr style="">
                                <td colspan="5" height="25"><b>Please indicate invoice number when making payment.</b></td>
                            </tr>
                            <tr >
                                <td colspan="5"><b>Please note that any overdue payment/s will be listed in DP Credit Bureau\'s records and this record may be accessed by financial institutions and other approving credit companies.</b></td>
                            </tr>
                ';
            
        // ############### Item ############## //   
        $dis_label="";
        $col=2;
        $a = '60%';
        $invoice_item_arr = $this->get_db_invoice_item($invoice_id, $invoice_revision_id);
        $booldis=0;
        foreach ($invoice_item_arr as $invoice_item1) {
            if($invoice_item1['invoice_item_discount']!=0){
                $booldis=1;
            }
        }
                if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
                    $tr_item = '';
                    foreach ($invoice_item_arr as $invoice_item) {

                        if ($invoice_item['invoice_item_type'] == 0) {
                            $item = $invoice_item['invoice_item'];
                        } elseif ($invoice_item['invoice_item_type'] == 1) {
                                $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                        } elseif ($invoice_item['invoice_item_type'] == 2) {
                            $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                        } else {
                                $item = '<font color="red">Item type not found.</font>';
                        }
                        if($booldis==1){
                            $dis_label='<td style="border:1px soild #000;" width="11%" align="center" ><b>Discount<br>(%)</b></td>';
                            $dis_value='<td style="'.$border.'" width="11%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
                            $a = '49%';
                            $col=3;
                        }
                    }
                }
        
        
        
        // ########## Total #######//
        
        $total_item_show = $this->get_invoice_item_total($invoice_id, $invoice_revision_id);
        
        $invoice_rev_details_arr = $this->get_latest_invoice_revision($invoice_id);

        if (count($invoice_rev_details_arr) > 0) {
            if($invoice_rev_details_arr[0]['invoice_revision_notes']!="")
    //            $note_area ='<b>Notes:</b> '.$invoice_rev_details_arr[0]['invoice_revision_notes'];
                $note_area='<table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr><td></td></tr>
                    <tr valign="top">
                    <td><b>Notes:</b> '.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_notes']) .'</td>
                 </tr></table>';
            if($invoice_rev_details_arr[0]['invoice_revision_term_condition']!="")
    //            $term_area ='<b>Terms and Conditions:</b> '.$invoice_rev_details_arr[0]['invoice_revision_term_condition'];
                $term_area = '<table width="100%" border="0" cellspacing="0" cellpadding="4"><tr>
                                <td colspan="6" ><b>Terms and Conditions:<br/></b> '.CSalesManager::htmlChars($invoice_rev_details_arr[0]['invoice_revision_term_condition']) .'</td>
                                <td></td>
                              </tr></table>';
            $tax_id = $invoice_rev_details_arr[0]['invoice_revision_tax'];
        }
       //In ra so tien da tra
        
       require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");
        $CPaymentManager = new CPaymentManager();
        $paymentDetail_arr = $CPaymentManager->lis_db_payment_detail($invoice_revision_id);
        $tr_paid = 0;
        if(count($paymentDetail_arr)>0){
            foreach ($paymentDetail_arr as $paymentDetail){
                $paymentDetail_amount =  $paymentDetail['payment_amount'];
                $total_paid_and_tax += floatval($paymentDetail['payment_amount']);
                $total_paid_and_tax = round($total_paid_and_tax, 2);
            }
            $tr_paid = $total_paid_and_tax;
                    
        } // END- In ra so tien da tra
 
        $CTax = new CTax();
        $tax_arr = $CTax->list_tax();
        $currency_symb = '$'; $total_paid_and_tax = 0;
        $tax_name = 0;
        if (count($tax_arr) > 0) {
            foreach ($tax_arr as $tax) {
                $tax_name = $tax['tax_name'];
                $tax_value =0;
                if ($tax_id) { // neu tinh trang la update
                    if ($tax['tax_id'] == $tax_id) {
                        $tax_value = $tax['tax_rate'];
                    }
                } else { // neu tinh trang la add lay ra ta default
                    if ($tax['tax_default'] == 1) {
                        $tax_value = $tax['tax_rate'];
                    }
                }
            }
        }
        
        $total_item_show_last_discount = $total_item_show - $discount;
        $total_item_tax_show = 0;
        if ($tax_id) {
            $caculating_tax = (floatval($total_item_show_last_discount) * floatval($tax_value) / 100);
            $total_item_tax_show =CSalesManager::round_up($caculating_tax);
            if($invoice_rev_details_arr[0]['invoice_revision_tax_edit']!=0)
                $total_item_tax_show = $invoice_rev_details_arr[0]['invoice_revision_tax_edit'];
        }
    $amount_due =  $total_item_show_last_discount + $total_item_tax_show - $tr_paid;
    
        $tbl_total = '<table border="0" width="100%" cellpadding="2">
                        <tr style="font-weight:bold;">
                            <td align="right">Total:</td>
                            <td align="right">$'.number_format(round($total_item_show,2),2).'</td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td align="right">GST&nbsp;&nbsp;'.$tax_value.'%:</td>
                            <td align="right">$'.number_format(round($total_item_tax_show,2),2).'</td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td align="right">Amount Paid:</td>
                            <td align="right">$'.number_format(round($tr_paid,2),2).'</td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td align="right">Amount Due:</td>
                            <td align="right">$'.number_format(round($amount_due,2),2).'</td>
                        </tr>
            </table>';
        
        $tbl_header.='
            <table border="0" cellspacing="0" cellpadding="0">
                    <tr valign = "top" height="60">
                        '.$img.'
                        <td width="71%"><table border="0"  width="100%">'.$supplier.'</table></td>
                    </tr>
            </table>
            <table width="100%" border="0" cellpadding="3">
                    <tr >
                        <td colspan="5" align="right" style="font-size:1.2em; border-bottom:1px soild #000;"><b>Invoice No: '.$invoice_no.'</b></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align:center;font-weight:bold;font-size:1.5em;">
                             <div>TAX INVOICE</div>
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
                        <td style="border:1px soild #000;" align="center" width="7%">Qty</td>
                        <td style="border:1px soild #000;" align="right" width="13%">Unit Price</td>
                        '.$dis_label.'
                        <td style="border:1px soild #000;" align="right" width="13%">Amount</td>
                    </tr>
                    <tr><td colspan="7">'.$sub_heading.'</td></tr>
                    </table>
            ';
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage('P', 'A4');
        $pdf->writeHTMLCell('','','','',$tbl_header.$tbl_header_item);
        $pdf->ln();

        if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
            $tr_item = '';$i=1;$count_tu=0;
            foreach ($invoice_item_arr as $invoice_item) {

                if ($invoice_item['invoice_item_type'] == 0) {
                    $item = $invoice_item['invoice_item'];
                    if($i==1)
                        $count_tu = strlen($item);
                } elseif ($invoice_item['invoice_item_type'] == 1) {
                        $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                } elseif ($invoice_item['invoice_item_type'] == 2) {
                    $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                } else {
                        $item = '<font color="red">Item type not found.</font>';
                }
                if($booldis==1){
                    $dis_label='<td  width="11%" align="center" ><b>Discount<br>(%)</b></td>';
                    $dis_value='<td  width="11%" align="right">'. $invoice_item['invoice_item_discount'].'%' .'</td>';
                    $a = '49%';
                    $col=3;
                }

                $tr_item = '<table width="100%" border="0" cellpadding="5">
                    <tr>
                        <td  align="center" width="6%">'. $i.'</td>
                        <td  width="'.$a.'">'. CSalesManager::htmlChars($item) .'</td>
                        <td  width="7%" align="center">'. $invoice_item['invoice_item_quantity'] .'</td>
                        <td  width="13%" align="right">$'. number_format($invoice_item['invoice_item_price'],2) .'</td>                                
                        '.$dis_value.'
                        <td width="13%" align="right">$'. number_format(CSalesManager::calculate_total_item($invoice_item['invoice_item_price'], $invoice_item['invoice_item_quantity'], $invoice_item['invoice_item_discount']),2) .'</td>
                    </tr></table>';
                if($pdf->GetY()>$pdf->getPageHeight()-60){
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
                    <td colspan="3">'.$tbl_total.'</td>
                </tr>';
        $tbl_footer.='</table>';
        $hf = 62;
        if($pdf->GetY()>$pdf->getPageHeight()-$hf || $count_tu>1200){
                  $pdf->AddPage();
                  $pdf->writeHTMLCell('','','','',$tbl_header);
                  $pdf->ln();
              }
        $pdf->writeHTMLCell('','','','',$tbl_footer);
        $pdf->ln();
        // Hien thi term and codition

        $pdf->writeHTMLCell('','','','',$term_area);
        $pdf->ln();
        if($pdf->GetY()>$pdf->getPageHeight()-65){
                      $pdf->AddPage();
                      $pdf->writeHTMLCell('','','','',$tbl_header);
                      $pdf->ln();
                  }
        $pdf->writeHTMLCell('','','','',$footer_tex);
        $pdf->ln();
        $thYou = '<table width="98%" border="0" cellpadding="5">
                        <tr>
                            <td>Thank you & best regards</td>
                        </tr>
                        <tr>
                            <td height="20">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Yours Sincerely</td>
                        </tr>
                </table>';
            $pdf->writeHTMLCell('','','',253,$thYou);
        
        
//        $pdf->AddPage('P', 'A4');
//        $pdf->writeHTML($tbl, true, false, false, false, '');

        ob_end_clean();
        $pdf->Output($dir.'/'.$invoice_rev.'.pdf','F'); // Save file PDF vao forder
        //$pdf->Output($invoice_rev .'.pdf', 'I');
    }
    
    public function get_db_list_invoice() {
            $q = new DBQuery();
            $q->addTable('sales_invoice');
            $q->addQuery('invoice_id');
            return $q->loadList();
    }

    public function confirm($invoice_id,$invoice_revision_id)
    {
        $invoice_no = CSalesManager::create_invoice_or_quotation_no();
        $Invoice = new CInvoice();
        $Invoice->load($invoice_id);
        $Invoice->invoice_no = $invoice_no;
        $Invoice->invoice_status = 1;
        $Invoice->store();
        
        $InvoiceRev = new CInvoiceRevision();
        $InvoiceRev->load($invoice_revision_id);
        $InvoiceRev->invoice_revision = CSalesManager::create_invoice_or_quotation_revision('',$invoice_no,null);
        $InvoiceRev->store();
    }
    
    /**
     * Lay invoice theo contract
     * @param type $contract_id
     */
    public function getInvoiceByContract($contract_id)
    {
        
        $q = new DBQuery();
        $q->addTable('sales_invoice','i');
        $q->addJoin('sales_invoice_contract', 'ic', 'ic.invoice_id = i.invoice_id','INNER');
        $q->addWhere('ic.contract_id = '.intval($contract_id));
        
        return $q->loadList();
        
    }
    
    /**
     * 
     * @param type $invoice_id
     * @return type
     */
    public function calculateTotalInvoice($invoice_id,$revision_last_id=false,$total_item_last_discount=false,$tax_rate_edit=false,$tax_rate_value=false)
    {
        if(!isset($revision_last_id) || $revision_last_id<=0)
        {
            $revision_last_id = $this->get_invoice_revision_lastest($invoice_id);
            
        }
        
        $total = $this->get_total_item_invoice_and_tax($invoice_id, $revision_last_id,$total_item_last_discount,$tax_rate_edit,$tax_rate_value);
        return $total;
    }
    
    public function calculateGSTInvoice($invoice_id)
    {
        $revision_last_id = $this->get_invoice_revision_lastest($invoice_id);
        
        $total = $this->get_gst_item_invoice_and_tax($invoice_id, $revision_last_id);
        return $total;
    }
    
    /**
     * Tính tổng invoice theo contract 
     * @param type $contract_id
     * @return type
     */
    public function calculateTotalInvoiceByContract($contract_id)
    {
        $contract_invoice_arr = $this->getInvoiceByContract($contract_id);
        
        $total=0;
        foreach ($contract_invoice_arr as $contract_invoice_item) {
            $invoice_id = $contract_invoice_item['invoice_id'];
            
            $total+=$this->calculateTotalInvoice($invoice_id);
            
        }
        
        return $total;
    }
    
     public function calculateGSTInvoiceByContract($contract_id)
    {
        $contract_invoice_arr = $this->getInvoiceByContract($contract_id);
       
        $total=0;
        foreach ($contract_invoice_arr as $contract_invoice_item) {
            $invoice_id = $contract_invoice_item['invoice_id'];
            $total+=$this->calculateGSTInvoice($invoice_id);
        }
        
        return $total;
    }
    /**
     * Lấy danh sách các invoice có thanh toán theo contract
     * @param type $contract_id
     * @return type
     */
    public function getInvoicePaymentByContact($contract_id)
    {
        $invoice_last_id_arr = array();
        $contract_invoice_arr = $this->getInvoiceByContract($contract_id);
        foreach ($contract_invoice_arr as $contract_invoice_item) {
            $invoice_id = $contract_invoice_item['invoice_id'];
            $invoice_last_id_arr[] = $this->get_invoice_revision_lastest($invoice_id);
        }
        $invoice_where = implode(',', $invoice_last_id_arr);
        
        $q = new DBQuery();
        $q->addQuery('pd.*,inv.*');
        $q->addTable('sales_payment_detail','pd');
        $q->addJoin('sales_invoice_revision', 're', 're.invoice_revision_id=pd.invoice_revision_id');
        $q->addJoin('sales_invoice', 'inv', 're.invoice_id=inv.invoice_id');
        $q->addJoin('sales_payment', 'pay', 'pay.payment_id=pd.payment_id');
        $q->addJoin('sales_invoice_revision', 'ir', 'ir.invoice_revision_id=pd.invoice_revision_id','INNER');
        $q->addWhere('ir.invoice_revision_id IN (0'.$invoice_where.')');
        
        return $q->loadList();
    }
    
    public function calculateTotalInvoicePaymentByContract($contract_id)
    {
        $list_invoice = $this->getInvoicePaymentByContact($contract_id);
      
        $payment = 0;
        foreach($list_invoice as $invoice_payment)
        {
            $payment += $invoice_payment['payment_amount'];
        }
        return $payment;
    }
    
    public function array_sort($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }
    
    public function get_total_amount($invoice_id,$invoice_revision_id){
       
            $q = new DBQuery();
            $q->addTable('sales_invoice_revision', 'tbl1'); 
            $q->addQuery('tbl1.invoice_revision_tax, tbl2.tax_rate, tbl1.invoice_revision_tax_edit,invoice_revision_discount');
            $q->addJoin('sales_tax', 'tbl2', 'tbl1.invoice_revision_tax = tbl2.tax_id');
            $q->addWhere('tbl1.invoice_revision_id = '.intval($invoice_revision_id));
            $return = $q->loadList();
            $tax_value = $return[0]['tax_rate'];
            $tax_rate_edit=$return[0]['invoice_revision_tax_edit'];
        
            $total_show = $this->get_invoice_item_total($invoice_id, $invoice_revision_id);
            $caculating_tax=0;
            if ($tax_value) {
                $caculating_tax = floatval($total_show) * floatval($tax_value) / 100;

                if($return[0]['invoice_revision_tax_edit']!=0)
                    $caculating_tax = $tax_rate_edit;
            }
            
            $total = $total_show + $caculating_tax-$return[0]['invoice_revision_discount'];
            $amount = round($total,2);
            return $amount;
    }
    
    //Tinh total payment theo department_id
    public function calculateTotalInvoiceByDepartment($dept_id =false,$date_from=false,$date_to=false)
    {
        $CInvoiceManager=new CInvoiceManager();
        $credit =new CCreditNoteManager();
        $q = new DBQuery();
        $q->addTable('sales_invoice_revision', 'tbl1'); 
        $q->addQuery('tbl3.invoice_no,tbl1.invoice_revision_id,tbl1.invoice_id,tbl1.invoice_revision_tax, tbl2.tax_rate, tbl1.invoice_revision_tax_edit,invoice_revision_discount');
        $q->addJoin('sales_tax', 'tbl2', 'tbl1.invoice_revision_tax = tbl2.tax_id');
        $q->addJoin('sales_invoice', 'tbl3', 'tbl1.invoice_id = tbl3.invoice_id');
        
        $q->addWhere('tbl3.department_id = '.intval($dept_id));
        if(isset($date_from) && $date_from != "")
            $q->addWhere ('tbl3.invoice_date >= "'.$date_from.'"');
        if(isset($date_from) && $date_from != "")
            $q->addWhere ('tbl3.invoice_date <= "'.$date_to.'"');
        $q->group_by ='tbl1.invoice_id';
        $return = $q->loadList();
        $total=0;$amount=0;$totalCre=0;
        $total_total=0;
        foreach ($return as $data)
        {
           
            $invoice_id = $data['invoice_id'];
            $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
            
            $total = $CInvoiceManager->get_total_amount($invoice_id, $invoice_revision_id);
            $total_total+=$total;
        }
   
        
        return $total_total;
    }
    
    //Tra ve thong tin cua quotation duoc link voi invoice
    public function getQuotationByInvoice()
    {
        $q = new DBQuery();
        $q->addTable('sales_invoice', 'tbl1');
        $q->addQuery('tbl1.invoice_id,tbl2.*');
        $q->addJoin('sales_quotation', 'tbl2', "tbl2.quotation_id = tbl1.quotation_id");
        $q->addWhere('tbl1.quotation_id  >0');
        $rows = $q->loadList();
        return $rows;
    }
}


?>
