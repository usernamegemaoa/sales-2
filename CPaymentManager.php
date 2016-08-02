<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR."/modules/sales/CReceipt.php");
require_once (DP_BASE_DIR."/modules/sales/CPayment.php");
require_once (DP_BASE_DIR."/modules/sales/CPaymentSchedule.php");
require_once (DP_BASE_DIR."/modules/sales/CPaymentDetail.php");
require_once (DP_BASE_DIR."/modules/sales/CPaymentNew.php");
require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
require_once (DP_BASE_DIR."/modules/banks/CReconcileDedail.php");

$CSalesManager = new CSalesManager();
$CInvoiceManager = new CInvoiceManager();
$CReconcileDetail = new CReconcileDetail();

/**
 * XXX detailed description
 *
 * @author    XXX
 * @version   XXX
 * @copyright XXX
 */
class CPaymentManager {
    // Attributes
    // Associations
    // Operations
    /**
     * XXX
     * 
     * @param  CReceipt $receiptObj XXX
     * @return int XXX
     * @access public
     */

    public function list_invoice_payment() {
        $q = new DBQuery();
        $q->addTable('sales_invoice', 'tbl1');
        $q->addQuery('tbl1.*, tbl1.invoice_date, tbl3.company_name');
        //$q->addJoin('sales_invoice_revision', 'tbl2', "tbl2.invoice_id = tbl1.invoice_id");
        $q->addJoin('clients', 'tbl3', "tbl3.company_id = tbl1.customer_id");
        //$q->addWhere($where . ' ORDER BY tbl2.invoice_revision_date ASC LIMIT 0,1');
        return $q->loadList();

    }
    
    public function loadInvoiceRevision($invoice_id) {
        $q = new DBQuery();
        $q->addTable('sales_invoice_revision', 'tb1');
        $q->addQuery('tb1.*, tb2.invoice_no, tb3.company_name');
        $q->addJoin('sales_invoice', 'tb2', 'tb2.invoice_id = tb1.invoice_id');
        $q->addJoin('clients', 'tb3', 'tb3.company_id = tb2.customer_id');
        $q->addWhere('tb1.invoice_id = '.$invoice_id);
        return $q->loadList();
    }
    
    public function add_receipt($receiptObj) {
            $CReceipt = new CReceipt();
            $CReceipt->bind($receiptObj);
            $CReceipt->store($receiptObj);
            return $CReceipt->receipt_id;
    }

    /**
     * XXX
     * 
     * @param  int $receipt_id XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_receipt($receipt_id)
     {
        trigger_error('Not Implemented!', E_USER_WARNING);
    }

    /**
     * XXX
     * 
     * @param  CPayment $paymentObj XXX
     * @return boolean XXX
     * @access public
     */
    public function add_payment($paymentObj) {
        $Cpayment = new CPayment();
        $Cpayment->bind($paymentObj);
        $Cpayment->store();
        return $Cpayment->payment_id;
    }
    function add_pymentDetail($payment_DetailObj){
        $CPaymentDetal = new CPaymentDetal();
        $CPaymentDetal -> bind($payment_DetailObj);
        $CPaymentDetal ->store($payment_DetailObj);
        return $CPaymentDetal->payment_detail_id;
    }
    function add_pymentNew($payment_newObj){
        $CPayment_new = new CPaymentNew();
        $CPayment_new ->bind($payment_newObj);
        $CPayment_new ->store($payment_newObj);
        return $CPayment_new->payment_id;
    }
    public function show_method() {
        $q = new DBQuery();
        $q->addTable('sales_payment');
        $q->addQuery('payment_amount');
        $rows = $q->loadList();
        foreach ($rows as $row) {
            $method = $row['payment_amount'];
        }
        return $method;
    }
    /**
     * XXX
     * 
     * @param  int $payment_id XXX
     * @param  string $payment_field XXX
     * @param  string $value XXX
     * @return boolean XXX
     * @access public
     */
    public function update_payment($payment_id, $payment_field, $value) {
        $CPayment = new CPayment();
        $CPayment->load($payment_id);
        $CPayment->$payment_field = $value;
        return $CPayment->store();
    }
    
    public function update_payment_detail($payment_detail_id, $payment_field, $value){
        $CPaymentDetail = new CPaymentDetal();
        $CPaymentDetail->load($payment_detail_id);
        $CPaymentDetail->$payment_field = $value;
        return $CPaymentDetail->store();
    }

    /**
     * XXX
     * 
     * @param  int $payment_id XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_payment($payment_id) { // format nhu sau: $invoice_id_arr = array(1, 2, 5, 6, 9);
//        if (($count = count($payment_id_arr)) > 0) {
//            $where = 'payment_id = '. $payment_id_arr[0];
//            for ($i = 1; $i < $count; $i++) {
//                $where .= ' OR payment_id = '. $payment_id_arr[$i];
//            }
//        }
        
        if ($where) {
            $sql1 = "delete from sales_payment where payment_id=".$payment_id; // xoa bang invoice
            if(db_exec($sql1)) {
                return true;
            } else
                return false;
        } else
            return false;
    }
    
    public function remove_payment_detail($payment_detail_id_arr){
        $cpaymentDetail = new CPaymentDetal();
        $cpayment = new CPaymentNew();
        $tmp = false;
        $count = count($payment_detail_id_arr);
//        if(($count)>0){
//            $where = 'payment_detail_id='.$payment_detail_id_arr[0];
//            for($i = 1; $i<$count; $i++){
//                $where .= 'OR payment_detail_id = '.$payment_detail_id_arr[$i];
//            }
//        }
//        /* xoa payment tuong ung */
//        for($i=0;$i<$count;$i++){
//            $payment_detail_arr = $this->get_paymentDetail($payment_detail_id_arr[$i]);
//            $payment_detail_arr1= $this->get_paymentDetail(false, $payment_detail_arr[0]['payment_id']);
//            if(count($payment_detail_arr1)<=1){
//                $sql1="delete from sales_payment where payment_id=".$payment_detail_arr[0]['payment_id'];
//                if(db_exec($sql1));
//                    $tmp=true;
//            }
//        }
//        
//        
//        if($where){
//            $sql = "delete from sales_payment_detail where ".$where;
//            if(db_exec($sql));
//                $tmp=true;
//        }
        if($count>0){
            for($i=0;$i<$count;$i++){
                //$sql_detail = "delete from sales_payment_detail where payment_detail_id =".$payment_detail_id_arr[$i];
                $sql_detail = $cpaymentDetail->store_delete(null,$payment_detail_id_arr[$i]);
                
                // Xoa payment khi da xoa het payment detail thuoc payment nay
                $payment_detail_arr = $this->get_paymentDetail($payment_detail_id_arr[$i]);
                $payment_detail_arr1= $this->get_paymentDetail(false, $payment_detail_arr[0]['payment_id']);
                if(count($payment_detail_arr1)<=1){
                    //$sql_payment ="delete from sales_payment where payment_id=".$payment_detail_arr[0]['payment_id'];
                    $sql_payment =$cpayment->store_delete(null, $payment_detail_arr[0]['payment_id']);
                    if(db_exec($sql_payment))
                        $tmp=true;
                }                
                if(db_exec($sql_detail)){
                    $tmp=true; 
                }
            }
        }
       return $tmp;
   
    }

    /**
     * XXX
     * 
     * @param  CPaymentSchedule $payment_schedule_obj XXX
     * @return boolean XXX
     * @access public
     */
    public function add_payment_schedule($payment_schedule_obj)
     {
        $CPaymentSchedule = new CPaymentSchedule();
        $CPaymentSchedule->bind($payment_schedule_obj);
        $CPaymentSchedule->store($payment_schedule_obj);
        return $CPaymentSchedule->payment_schedule_id;
    }

    /**
     * XXX
     * 
     * @param  int $payment_schedule_id XXX
     * @param  string $payment_schedule_field XXX
     * @param  string $value XXX
     * @return boolean XXX
     * @access public
     */
    public function update_payment_schedule($payment_schedule_id, $payment_schedule_field, $value) {
        $CPaymentSchedule = new CPaymentSchedule();
        $CPaymentSchedule->load($payment_schedule_id);
        $CPaymentSchedule->$payment_schedule_field = $value;
        return $CPaymentSchedule->store();
    }

    /**
     * XXX
     * 
     * @param  int $payment_schedule_id XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_payment_schedule($payment_schedule_id_arr) { // format nhu sau: $invoice_id_arr = array(1, 2, 5, 6, 9);
        if (($count = count($payment_schedule_id_arr)) > 0) {
            $where = 'payment_schedule_id = '. $payment_schedule_id_arr[0];
            for ($i = 1; $i < $count; $i++) {
                $where .= ' OR payment_schedule_id = '. $payment_schedule_id_arr[$i];
            }
        }
        
        if ($where) {
            $sql1 = "delete from sales_payment_schedule where ". $where; // xoa bang invoice
            if(db_exec($sql1)) {
                return true;
            } else
                return false;
        } else
            return false;
    }

    /**
     * XXX
     * 
     * @param  int $invoice_revision_id XXX
     * @return array XXX
     * @access public
     */
    
    //Chu y bien credit_note_id
    public function list_db_payment($payment_id="",$date_start="",$date_end="",$creditNote_id=false,$method_id="",$receipt=false) //Credit Note : true or false {true: khong hien thi payment cua CreditNote}
    {
        $q = new DBQuery();
        $q->addTable('sales_payment','tbl1');
        $q->addQuery('tbl1.*');
        if($payment_id!="")
            $q->addWhere('payment_id = '.$payment_id);
        if($date_start!="")
            $q->addWhere('payment_date >="'.$date_start.'"');
        if($date_end!="")
            $q->addWhere ('payment_date<="'.$date_end.'"');
        if($creditNote_id)
            $q->addWhere ('credit_note_id = 0');//Chu y bien credit_note_id
        if($method_id!="")
            $q->addWhere ('payment_method = '.intval($method_id));
        $q->addOrder('payment_date ASC');
        if($receipt != false)
            $q->group_by='payment_receipt_no';
        return $q->loadList();
    }
    public  function lis_db_payment_detail($invoice_revision_id=false){
        $q = new DBQuery();
        $q->addTable('sales_payment_detail');
        $q->addQuery('*');
        if($invoice_revision_id)
            $q->addWhere('invoice_revision_id = '.$invoice_revision_id);
        return $q->loadList();
    }
    
    //kiem tra xem da co receipt no hay chua
    public function is_check_payment_receipt($receipt_no){
       
        $q = new DBQuery();
        $q->addTable('sales_payment');
        $q->addQuery('payment_receipt_no');
     
        $q->addWhere('payment_receipt_no LIKE "'.$receipt_no.'"');
        $arr_payment = $q->loadList();
        
        if(count($arr_payment)>0)
            return true;
        return false;
    }
    
    /**
     * XXX
     * 
     * @param  int $invoice_revision_id XXX
     * @return array XXX
     * @access public
     */
    public function list_db_payment_schedule($invoice_revision_id) {
        $q = new DBQuery();
        $q->addTable('sales_payment_schedule');
        $q->addQuery('*');
        $q->addWhere('invoice_revision_id = '.$invoice_revision_id);
        return $q->loadList();
    }

    /**
     * XXX
     * 
     * @param  array $invoice_id_array XXX
     * @param  array $invoice_revision_id_array XXX
     * @return boolean XXX
     * @access public
     */
    public function send_mail_receipt($customer_id, $payment_id_arr, $content, $sender, $subject, $reciver) { // format nhu sau: $payment_id_arr = array(1, 2, 5, 6, 9);
        
        require_once(DP_BASE_DIR. '/lib/swift/swift_required.php');
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
        
        $attachment = Swift_Attachment::newInstance($this->create_receipt_pdf_file($customer_id, $payment_id_arr, true), 'receipt.pdf', 'application/pdf');
        $mail->attach($attachment);
        
        // Send the message
        $mailer->send($mail);
        
        return true;
        
    }

    /**
     * XXX
     * 
     * @param  int $customer_id XXX
     * @return boolean XXX
     * @access public
     */
    public function create_receipt_pdf_file($customer_id, $payment_detail_id, $is_send_mail = false) {
        global $CSalesManager,$CInvoiceManager;
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(10, 5, 10, -15);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('times', '', 10);
        
        // Template cho server http://hlaircon.theairconhub.com/
        $template_pdf5 = $CSalesManager->get_template_pdf(5);
        $template_pdf7=$CSalesManager->get_template_server(1);
        // Get LOGO sales //
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        
        $files = list_files($url);
        if(count($files)>0)
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
            $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" />';
            $img5 = '<img height="21" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
        }
        

        
        $supplier_arr = $CSalesManager->get_supplier_info();

        if (count($supplier_arr) > 0) {
            $sales_owner_name = $supplier_arr['sales_owner_name'];
            $sales_owner_address =$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'] ;
            $phone =$supplier_arr['sales_owner_phone1'] .', '. $supplier_arr['sales_owner_phone2'];
                $fax = $supplier_arr['sales_owner_fax'];
                $email =$supplier_arr['sales_owner_email'];
                $web = $supplier_arr['sales_owner_website'];
                $sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
                $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
            }
            $phone5="";
            if($supplier_arr['sales_owner_phone1']!="")
                $phone5 .= $supplier_arr['sales_owner_phone1'];
                    if($fax!="");
            $fax5 =  ', Fax: '.$fax;

            $supplier = '<tr><td><p style="font-size:1.5em;">'. $sales_owner_name .'</p></td></tr>';
            $supplier.= '<tr><td>Address: '.$sales_owner_address.'</td></tr>';
            $supplier .= '<tr><td>Tel: '.$phone.'. Fax: '.$fax .' </td></tr>';
            $supplier .= '<tr><td>Reg No: '. $sales_owner_reg_no ;
            if($sales_owner_gst_reg_no !="")
            {
                $supplier .='. GST Reg No: '. $sales_owner_gst_reg_no ;
            }
            $supplier.='</td></tr>';
            $supplier .= '<tr><td>';
            if($email !="")
                $supplier .='Email: '.$email;
            if($web !="")
                $supplier .='. Website: '.$web;
            $supplier.='</td></tr>';
            
            $supplier5= '<tr><td>'.$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].'</td></tr>';
            $supplier5.= '<tr><td>Tel: '.$phone5.$fax5.'</td></tr>';
        //    $supplier .= '<tr><td>Fax: '.$fax.'</td></tr>';
            if($sales_owner_gst_reg_no!="")
                $supplier5 .= '<tr><td>GST Reg No: '. $sales_owner_gst_reg_no .'</td></tr>';
        //    $supplier.= '<tr><td>Reg No: '. $supplier_arr['sales_owner_postal_code'] .'</td></tr>';
            if($email!="")
                $supplier5 .= '<tr><td>Email: '.$email.'</td></tr>';
            if($web!="")
                $supplier5 .= '<tr><td>Website: '.$web.'</td></tr>';

            $PaymentMethods = dPgetSysVal('PaymentMethods');
            $customer = $CSalesManager->get_customer_field_by_id($customer_id, 'company_name');
            
            $payment_detail_arr = $this->get_paymentDetail($payment_detail_id);
            if(count($payment_detail_arr) > 0){
                $payment_id = $payment_detail_arr[0]['payment_id'];
                $invoice_no = $CInvoiceManager->get_invoice_no_by_revision_id($payment_detail_arr[0]['invoice_revision_id']);
                $amount = '$ '.number_format($payment_detail_arr[0]['payment_amount'],2);
            }

            $payment_arr = $this->get_db_payment($payment_id);    
            if (count($payment_arr) > 0) {
                $method = $payment_arr[0]['payment_method'];
                $date = $payment_arr[0]['payment_date'];
                $receipt_no = $payment_arr[0]['payment_receipt_no'];
                $receipt_notes = $payment_arr[0]['payment_notes'];
                $description = $payment_arr[0]['payment_description'];
                $payment_cheque_nos = $payment_arr[0]['payment_cheque_nos'];
            }

            $info5 ='<table border="0" width="100%">
                    <tr style="font-weight:bold;">
                        <td width="32%">Receipt No</td>
                        <td width="68%">: '. $receipt_no .'</td>
                    </tr>
                    <tr>
                        <td >Date</td>
                        <td >: '. date('d-m-Y',  strtotime($date)) .'</td>
                    </tr></table>';

    if($template_pdf5==1)
    {
        $tbl =
            '<table border="0" width="100%" >
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="3">
                                 <tr><td colsapn="2" align="right" width="100.5%">'.$img5.'</td></tr>
                                 <tr valign = "top">
                                     <td width="34%" rowspan="2" align="left">'.$info5.'</td>
                                     <td width="66%" align="right">'.$supplier5.'</td>
                                 </tr>
                            </table>
                    </td>
                </tr>
                <tr align="center">
                    <td colspan="2"><h1>RECEIPT</h1></td>
                </tr>
                <tr align="center">
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>
                        <table border="1" cellpadding="3" cellpadding="4">
                            <tr>
                                <td width="25%">Invoice No:</td>
                                <td width="75%">'. $invoice_no .'</td>
                            </tr>
                            <tr>
                                <td width="25%">Amount:</td>
                                <td width="75%">'. $amount .'</td>
                            </tr>
                            <tr>
                                <td width="25%">Method:</td>
                                <td width="75%">'.$PaymentMethods[$method] .'</td>
                            </tr>
                            <tr>
                                <td width="25%">Notes:</td>
                                <td width="75%">'. $receipt_notes .'</td>
                            </tr>
                            <tr>
                                <td width="25%">Description:</td>
                                <td width="75%">'. $description .'</td>
                            </tr>
                            <tr>
                                <td width="25%">Received From:</td>
                                <td width="75%">'. $customer .'</td>
                            </tr>
                            <tr>
                                <td width="25%">Received By:</td>
                                <td width="75%">'. $sales_owner_name .'</td>
                            </tr>
                            <tr>
                                <td width="25%">Cheque Nos:</td>
                                <td width="75%">'. $payment_cheque_nos .'</td>
                            </tr>
                        </table>
                     </td>
                </tr>
            </table>';
}
else
{
   if($template_pdf7 == 7)
   {
        $img = '<td width="25%" align="center"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="100" /></td>';
        $supplier = '<tr><td><p style="font-size:1.5em;">'. $sales_owner_name .'</p></td></tr>';
        $supplier.= '<tr><td>'.$sales_owner_address.'</td></tr>';
        if($sales_owner_gst_reg_no !="")
        {
            $supplier .='. GST Reg No: '. $sales_owner_gst_reg_no ;
        }
        $supplier .= '<tr><td>Reg No: '. $sales_owner_reg_no ;
        $supplier.='</td></tr>';
        $supplier .= '<tr><td>Tel: '.$phone.'. Fax: '.$fax .' </td></tr>';
        
        $supplier .= '<tr><td>';
        if($email !="")
            $supplier .='Email: '.$email;
        if($web !="")
            $supplier .='. Website: '.$web;
        $supplier.='</td></tr>';
       $txt_header='<table border="0" cellspacing="0" cellpadding="0">
                        <tr valign = "top" height="60">
                            '.$img;
                 if(count($files) >0)
                    $txt_header.='<td width="60%"><table border="0"   width="100%"><br/><br/>'.$supplier.'</table></td>';
         else
                    $txt_header.='<td width="'.$w.'"><table border="0"   style="text-align:left" width="100%"><br/>'.$supplier.'</table></td>';
                 $txt_header.='</tr>
                        <tr>
                            <td colspan="2" align="right" style="font-size:1.2em;" width="100%"><hr></td>
                        </tr>
                   </table>';
       $tbl =
    '<table border="0" width="100%" >
        <tr>
            <td>
                '.$txt_header.'

            </td>
        </tr>
        <tr align="center">
            <td colspan="2"><h1>RECEIPT</h1></td>
        </tr>
        <tr align="center">
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>
                <table border="1" cellpadding="3" cellpadding="3">
                    <tr>
                        <td width="25%">Date:</td>
                        <td width="75%">'. date('d-m-Y',  strtotime($date)) .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Receipt No:</td>
                        <td width="75%">'. $receipt_no .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Invoice No:</td>
                        <td width="75%">'. $invoice_no .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Amount:</td>
                        <td width="75%">'. $amount .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Notes:</td>
                        <td width="75%">'. $receipt_notes .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Description:</td>
                        <td width="75%">'. $description .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Received From:</td>
                        <td width="75%">'. $customer .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Received By:</td>
                        <td width="75%">'. $sales_owner_name .'</td>
                    </tr>
                    <tr>
                                <td width="25%">Cheque Nos:</td>
                                <td width="75%">'. $payment_cheque_nos .'</td>
                            </tr>
                </table>
             </td>
        </tr>
    </table>';
   }
   else
   {
    $tbl =
    '<table border="0" width="100%" >
        <tr>
            <td>
                <table border="0" >
                    <tr>
                        <td width="30%">'.$img.'</td>
                        <td width="70%"><table   border="0" width="100%" >'.$supplier.'</table></td>
                    </tr>
                    <tr><td colspan="2"><hr/></td></tr>
                </table>

            </td>
        </tr>
        <tr align="center">
            <td colspan="2"><h1>RECEIPT</h1></td>
        </tr>
        <tr align="center">
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>
                <table border="1" cellpadding="3" cellpadding="3">
                    <tr>
                        <td width="25%">Date:</td>
                        <td width="75%">'. date('d-m-Y',  strtotime($date)) .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Receipt No:</td>
                        <td width="75%">'. $receipt_no .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Invoice No:</td>
                        <td width="75%">'. $invoice_no .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Amount:</td>
                        <td width="75%">'. $amount .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Notes:</td>
                        <td width="75%">'. $receipt_notes .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Description:</td>
                        <td width="75%">'. $description .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Received From:</td>
                        <td width="75%">'. $customer .'</td>
                    </tr>
                    <tr>
                        <td width="25%">Received By:</td>
                        <td width="75%">'. $sales_owner_name .'</td>
                    </tr>
                    <tr>
                                <td width="25%">Cheque Nos:</td>
                                <td width="75%">'. $payment_cheque_nos .'</td>
                            </tr>
                </table>
             </td>
        </tr>
    </table>';
   }
}
            $pdf->AddPage('P', 'A4');
            $pdf->writeHTML($tbl, true, false, false, false, '');
            ob_end_clean();
            if (!$is_send_mail)
                $pdf->Output('Receipt.pdf', 'I');
            else
                return $pdf->Output('', 'S'); // tao ra chuoi document, ten bo qua
    }

    /**
     * XXX
     * 
     * @param  int $customer_id XXX
     * @return boolean XXX
     * @access public
     */
    public function get_invoice_by_customer($customer_id,$payment=false) {
        
            $q = new DBQuery();
            
            $q->addTable('sales_invoice');
            if($payment == 1)
                $q->addQuery('invoice_id,invoice_no');
            else
                $q->addQuery('*');
            $q->addWhere('customer_id = '. intval($customer_id));
            $q->addOrder('invoice_no ASC');
            return $q->loadList();
    }

    public function get_invoice_revision_by_customer($customer_id, $payment_invoice) {
        
            $q = new DBQuery();
            $q->addTable('sales_invoice', 'tbl1');
            $q->addQuery('tbl1.*, tbl2.*, tbl3.*');
            $q->addJoin('sales_invoice_revision', 'tbl2', 'tbl2.invoice_id = tbl1.invoice_id');
            $q->addJoin('sales_payment_detail', 'tbl3', 'tbl3.invoice_revision_id = tbl2.invoice_revision_id');
            $q->addWhere('customer_id = '. intval($customer_id).' AND tbl1.invoice_id = '.intval($payment_invoice));
            return $q->loadList();
    }
    
    function get_invoice_item_total($payment_invoice, $invoice_revision_id, $customer_id) {
            $q = new DBQuery();
            $q->addTable('sales_invoice_item', 'tbl1');
            $q->addQuery('tbl1.invoice_item_price, tbl1.invoice_item_quantity, tbl1.invoice_item_discount, tbl2.customer_id');
            $q->addJoin('sales_invoice', 'tbl2', 'tbl2.invoice_id = tbl1.invoice_id');
            $q->addWhere('tbl1.invoice_id = '.intval($payment_invoice).' AND tbl1.invoice_revision_id = '.intval($invoice_revision_id).' AND tbl2.customer_id = '.intval($customer_id));
            $rows = $q->loadList();
            $total = 0;
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $total += CSalesManager::calculate_total_item($row['invoice_item_price'], $row['invoice_item_quantity'], $row['invoice_item_discount']);
                }
            }
            return $total;
    }
    
    
   function get_sale_tax($invoice_revision_tax, $payment_invoice){
        $q = new DBQuery();
        $q->addTable('sales_tax', 'tbl1');
        $q->addQuery('tbl1.tax_rate');
        $q->addJoin('sales_invoice_revision', 'tbl2', 'tbl2.invoice_revision_tax = tbl1.tax_id');
        $q->addWhere('tbl1.tax_id = '.intval($invoice_revision_tax).' AND tbl2.invoice_id = '.intval($payment_invoice));
        $q->loadList();
//        foreach ($rows as $row) {
//            return $row;
//        }
    }
    /**
     * 
      public function loadInvoiceRevision($invoice_id) {
        $q = new DBQuery();
        $q->addTable('sales_invoice_revision', 'tb1');
        $q->addQuery('tb1.*, tb2.invoice_no, tb3.company_name');
        $q->addJoin('sales_invoice', 'tb2', 'tb2.invoice_id = tb1.invoice_id');
        $q->addJoin('clients', 'tb3', 'tb3.company_id = tb2.customer_id');
        $q->addWhere('tb1.invoice_id = '.$invoice_id);
        return $q->loadList();
    }
     * 
     * XXX
     * 
     * @param  $payment_id XXX
     * @return array XXX
     * @access public
     */
    public function get_db_payment($payment_id) {
            $q = new DBQuery();
            $q->addTable('sales_payment');
            $q->addQuery('*');
            $q->addWhere('payment_id = '. intval($payment_id));
            return $q->loadList();
    }
        
    /**
     * XXX
     * 
     * @param  $payment_id XXX
     * @return array XXX
     * @access public
     */
    public function get_total_payment($invoice_revision_id) {
            $q = new DBQuery();
            $q->addTable('sales_payment_detail');
            $q->addQuery('SUM(payment_amount) as total');
            $q->addWhere('invoice_revision_id = '. intval($invoice_revision_id));
            $rows = $q->loadList();
            $total_paid=0;
            if(count($rows) >0)
                $total_paid=$rows[0]['total'];
            $total_paid = round($total_paid, 2);
            return $total_paid;
    }
    
    /**
     * 
     * @param type $payment_detal_id
     * @param type $payment_id
     * @return type
     */
    public function get_paymentDetail($payment_detal_id=false, $payment_id=false){
            $q = new DBQuery();
            $q->addTable('sales_payment_detail','tbl');
            $q->addQuery('tbl.*');
            if($payment_detal_id)
                $q->addWhere('payment_detail_id = '. intval($payment_detal_id));
            if($payment_id)
                $q->addWhere ('payment_id='.intval ($payment_id));
            return $q->loadList();
    }
    
    
    /**
     * tính Total Invoice payment theo contract
     * @global CInvoiceManager $CInvoiceManager
     * @param type $contract_id
     * @return type
     */
    public function calculateTotalInvoicePaymentContract($contract_id)
    {
        global $CInvoiceManager;
        $contract_invoice_arr = $CInvoiceManager->getInvoiceByContract($contract_id);
        $total=0;
        foreach ($contract_invoice_arr as $contract_invoice_item) {
            $invoice_id = $contract_invoice_item['invoice_id'];
            $revision_last_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
            $total+=$totalPaymentInvoice = $this->get_total_payment($revision_last_id);
        }
        
        return $total;
    }
    
    /**
     * 
     * @global CReconcileDetail $CReconcileDetail
     * @param type $bank_account_id
     * @param type $date
     * @param type $check_reconcile {all:"Lay tat ca cac payment cua bank account" | reconcile:"Lay tat ca cac payment da duoc sao ke"
     *                                | unReconcile: "Lay tat ca cac payment chua duoc sao ke"}
     * @return type
     */
    public function getPaymentByBankAccount($bank_account_id,$date=false,$check_reconcile="all",$start_date=false,$end_date=false,$payment_id=false,$type=false)
    {
//        global $CReconcileDetail;
//        //Lay danh sach record payment_id da duoc reconcile
//        $payment_id_arr = array(0);
//        $reconcile_arr = $CReconcileDetail->getReconcileByBankAcount($bank_account_id, 'sales');
//        foreach ($reconcile_arr as $reconcile_item) {
//            $payment_id_arr[]=$reconcile_item['payment_record_id'];
//        }//END
        
        $q = new DBQuery();
        $q->addTable('sales_payment','p');
        $q->addQuery('p.*,c.company_name,d.invoice_revision_id,i.invoice_id,i.invoice_no');
        $q->addJoin('sales_payment_detail', 'd', 'd.payment_id=p.payment_id');
        $q->addJoin('sales_invoice_revision', 'ir', 'ir.invoice_revision_id=d.invoice_revision_id');
        $q->addJoin('sales_invoice', 'i', 'ir.invoice_id=i.invoice_id');
        $q->addJoin('clients', 'c', 'i.customer_id=c.company_id');
        $q->addWhere('p.bank_account_id='.intval($bank_account_id));
//        if($check_reconcile=="reconcile")
//        {
//            $q->addQuery('pd.remarks');
//            $q->addJoin('bank_reconcile_detail','pd', 'pd.payment_record_id=p.payment_id','INNER');
//            $q->addWhere('p.payment_id IN ('. implode(',', $payment_id_arr).')');
//        }
//        elseif($check_reconcile=="unReconcile")
//            $q->addWhere('p.payment_id NOT IN ('. implode(',', $payment_id_arr).')');
        if($date)
            $q->addWhere('p.payment_date <= "'.$date.'"');
        if($start_date)
            $q->addWhere('p.payment_date >= "'.$start_date.'"');
        if($end_date)
            $q->addWhere('p.payment_date <= "'.$end_date.'"');
        if($payment_id)
            $q->addWhere('p.payment_id = "'.$payment_id.'"');
        if($payment_id == false)
            $q->addGroup('p.payment_id');
       
        return $q->loadList();
    }
    
    public function calculateTotalPayment($payment_id=false,$date=false)
    {
        $q = new DBQuery();
        $q->addTable('sales_payment_detail','pd');
        $q->addQuery('pd.payment_amount');
        $q->addJoin('sales_payment','sp', 'pd.payment_id=sp.payment_id');
        if($payment_id)
            $q->addWhere('pd.payment_id='.  intval($payment_id));
        if($date)
            $q->addWhere('sp.payment_date<="'.$date.'"');
        
        $rows = $q->loadList();
        $total = 0;
        foreach ($rows as $row_item) {
            $total += $row_item['payment_amount'];
        }
        
        return $total;
    }
    
    public function get_receit_by_customer($customer_id,$payment_invoice,$reipt_id) {
        
            $q = new DBQuery();
            $q->addTable('sales_invoice', 'tbl1');
            $q->addQuery('tbl1.*, tbl2.*, tbl3.*,tbl4.*');
            $q->addJoin('sales_invoice_revision', 'tbl2', 'tbl2.invoice_id = tbl1.invoice_id');
            $q->addJoin('sales_payment_detail', 'tbl3', 'tbl3.invoice_revision_id = tbl2.invoice_revision_id');
            $q->addJoin('sales_payment', 'tbl4', 'tbl3.payment_id = tbl4.payment_id');
            if($reipt_id>0)
                $q->addWhere('tbl4.payment_id = '. intval($reipt_id));
            if($payment_invoice > 0)
                $q->addWhere('customer_id = '. intval($customer_id).' AND tbl1.invoice_id = '.intval($payment_invoice));
            if($customer_id>0)
                $q->addWhere('customer_id = '. intval($customer_id));
            $q->group_by='payment_receipt_no';

            return $q->loadList();
    }
    
    public function calculateTotalPaymentByBank($payment_id=false,$date=false,$bank_id,$date_search=false)
    {
        
        $q = new DBQuery();
        $q->addTable('sales_payment','p');
        $q->addQuery('p.*,c.company_name');
        $q->addJoin('sales_payment_detail', 'd', 'd.payment_id=p.payment_id');
        $q->addJoin('sales_invoice_revision', 'ir', 'ir.invoice_revision_id=d.invoice_revision_id');
        $q->addJoin('sales_invoice', 'i', 'ir.invoice_id=i.invoice_id');
        $q->addJoin('clients', 'c', 'i.customer_id=c.company_id');
        $q->addWhere('p.bank_account_id='.intval($bank_id));
        
        if($date_search && strtotime($date) >= strtotime($date_search))
        {
           
            $q->addWhere ('p.payment_date<"'.($date).'"');
            $q->addWhere ('p.payment_date>="'.($date_search).'"');
        }
        else if($date)
               $q->addWhere('p.payment_date <"'.$date.'"');
        
        $q->addGroup('p.payment_id');
        $row = $q->loadList();
        if($date_search && strtotime($date) < strtotime($date_search))
        {
            
            $row=array();
        }
        
        $total = 0;
        foreach ($row as $data)
            $total +=$this->calculateTotalPayment($data['payment_id']);
        return $total;
    }
    
	 public function getSalesPaymentByCustomer($bank_account_id,$customer_value_arr=false,$start_date=false,$end_date=false)
    {
  //      global $CReconcileDetail;
        //Lay danh sach record payment_id da duoc reconcile
//        $payment_id_arr = array(0);
//        $reconcile_arr = $CReconcileDetail->getReconcileByBankAcount($bank_account_id, 'sales');
//        foreach ($reconcile_arr as $reconcile_item) {
//            $payment_id_arr[]=$reconcile_item['payment_record_id'];
//        }//END
        
        $q = new DBQuery();
        $q->addTable('sales_payment','p');
        $q->addQuery('p.*,c.company_name,d.invoice_revision_id,i.invoice_id,i.invoice_no');
        $q->addJoin('sales_payment_detail', 'd', 'd.payment_id=p.payment_id');
        $q->addJoin('sales_invoice_revision', 'ir', 'ir.invoice_revision_id=d.invoice_revision_id');
        $q->addJoin('sales_invoice', 'i', 'ir.invoice_id=i.invoice_id');
        $q->addJoin('clients', 'c', 'i.customer_id=c.company_id');
        $q->addWhere('p.bank_account_id='.intval($bank_account_id));
//        if($check_reconcile=="reconcile")
//        {
//            $q->addQuery('pd.remarks');
//            $q->addJoin('bank_reconcile_detail','pd', 'pd.payment_record_id=p.payment_id','INNER');
//            $q->addWhere('p.payment_id IN ('. implode(',', $payment_id_arr).')');
//        }
//        elseif($check_reconcile=="unReconcile")
//            $q->addWhere('p.payment_id NOT IN ('. implode(',', $payment_id_arr).')');
        if($customer_value_arr)
            $q->addWhere ('i.customer_id IN'.$customer_value_arr);
//        if($date)
//            $q->addWhere('p.payment_date <= "'.$date.'"');
        if($start_date)
            $q->addWhere('p.payment_date >= "'.$start_date.'"');
        if($end_date)
            $q->addWhere('p.payment_date <= "'.$end_date.'"');
//        if($payment_id)
//            $q->addWhere('p.payment_id = "'.$payment_id.'"');
//        if($payment_id == false)
            $q->addGroup('p.payment_id');
       
        $sales_payment_arr = $q->loadList();
        $sales_credit = 0;
        foreach ($sales_payment_arr as $sales_payment) {
            $sales_credit += $this->calculateTotalPayment($sales_payment['payment_id']);
            
        }
        return $sales_credit;
    }
     public function getSalesPaymentByCustomer1($bank_account_id,$customer_value_arr=false,$start_date=false,$date_search=false)
    {
  
        $q = new DBQuery();
        $q->addTable('sales_payment','p');
        $q->addQuery('p.*,c.company_name,d.invoice_revision_id,i.invoice_id,i.invoice_no');
        $q->addJoin('sales_payment_detail', 'd', 'd.payment_id=p.payment_id');
        $q->addJoin('sales_invoice_revision', 'ir', 'ir.invoice_revision_id=d.invoice_revision_id');
        $q->addJoin('sales_invoice', 'i', 'ir.invoice_id=i.invoice_id');
        $q->addJoin('clients', 'c', 'i.customer_id=c.company_id');
        $q->addWhere('p.bank_account_id='.intval($bank_account_id));
        if($customer_value_arr){
            $q->addWhere ('i.customer_id IN'.$customer_value_arr);
        }
        if($date_search && strtotime($start_date) >= strtotime($date_search))
        {
            $q->addWhere ('p.payment_date<"'.($start_date).'"');
            $q->addWhere ('p.payment_date>="'.($date_search).'"');
        }
        else if($start_date){
               $q->addWhere('p.payment_date <"'.$start_date.'"');
        }
            $q->addGroup('p.payment_id');
       
        $sales_payment_arr = $q->loadList();
        $sales_credit = 0;
        foreach ($sales_payment_arr as $sales_payment) {
            $sales_credit += $this->calculateTotalPayment($sales_payment['payment_id']);
            
        }
        return $sales_credit;
    }
    
}

?>
