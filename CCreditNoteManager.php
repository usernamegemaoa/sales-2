<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once 'CCreditNote.php';
require_once 'CCreditNoteItem.php';
class CCreditNoteManager{
    
    function add_creditNote($creditNoteOject){
        $CCreditNote = new CCreditNote();
        $CCreditNote->bind($creditNoteOject);
        $CCreditNote->store();
//        if()
        return $CCreditNote->credit_note_id;
//        return false;
    }
    
    function add_creditNote_item($itemOjb){
        $ItemCreditNote = new CCreditNoteItem();
        $ItemCreditNote->bind($itemOjb);
        $ItemCreditNote->store();
        return $ItemCreditNote->credit_note_item_id;
    }
    function list_db_creditNote($creditNote_id=false,$customer_id=false,$status=false){
        $q = new DBQuery();
        $q ->addTable('sales_credit_note');
        $q ->addQuery('*');
        if($creditNote_id)
            $q->addWhere ('credit_note_id='.intval($creditNote_id));
        if($customer_id)
            $q->addWhere ('customer_id='.intval ($customer_id));
        if($status && $status!="")
            $q->addWhere ('credit_note_status='.intval($status));
        return $q->loadList();
    }
    function list_db_creditNote_item($creditNote_id=false,$creditNote_item_id=false){
        $q = new DBQuery();
        $q ->addTable('sales_credit_note_item');
        $q ->addQuery('*');
        if($creditNote_id)
            $q ->addWhere('credit_note_id='.  intval($creditNote_id));
        if($creditNote_item_id)
            $q->addWhere ('credit_note_item_id='.intval ($creditNote_item_id));
        return $q->loadList();
        
    }
    
    function customer_by_creditNote($customer_id){
        $q = new DBQuery();
        $q->addTable('clients');
        $q->addQuery('company_name');
        $q->addWhere('company_id='.intval($customer_id));
        return $q->loadList();
    }
    function update_creditNote($obj){
       

        $CreditNote = new CCreditNote();
        $creditNote_id=$obj['creditNote_id'];
        
        $address_id=$obj['address_id'];
        $invoice_id=$obj['invoice_id'];
        $creditNote_no=$obj['credit_note_no'];
        $creditNote_date=$obj['credit_note_date'];
        $creditNote_tax=$obj['credit_note_tax_id'];
        $creditNote_tax_edit_value=$obj['tax_edit_value'];
        $CreditNote->load($creditNote_id);
        $CreditNote->customer_id=$obj[customer_id];
        $CreditNote->address_id=$obj[address_id];
        $CreditNote->invoice_id=$obj[invoice_id];
        $CreditNote->credit_note_no=$obj[credit_voucher_no];
        $CreditNote->credit_note_date=$obj[credit_date_voucher];
        $CreditNote->credit_note_tax_id=$obj[creditNote_tax];
        $CreditNote->tax_edit_value=$obj[creditNote_tax_edit_value];
        $CreditNote->credit_note_co=$obj[credit_co];
//        
        return $CreditNote->store($CreditNote);
    }
    function delete_creditNote_item($creditNote_item_id_arr){ // format: $creditNote_item_id_arr=(1,2,4,..)
        $sql="delete from sales_credit_note_item where credit_note_item_id IN ".$creditNote_item_id_arr;
        if(db_exec($sql))
            return true;
        return false;
    }
    
    function total_credit_customer($customer_id)
    {
        $q = new DBQuery();
        $q->addTable('sales_credit_note','tbl1');
        $q->addQuery('Sum(credit_note_item_amount) AS totalAmount');
        $q->addJoin('sales_credit_note_item', 'tbl2', 'tbl1.credit_note_id=tbl2.credit_note_id');
        $q->addWhere('customer_id ='.intval($customer_id));
        return $q->loadList();
    }
    function add_credit_note_applied($appliedOject){
        $CCreditNoteApplier = new CreditApplied();
        $CCreditNoteApplier->bind($appliedOject);
        $CCreditNoteApplier->store();
        return $CCreditNoteApplier->credit_note_applied_id;
    }
    function update_credit_note_status($creditNote_id,$credit_note_status,$invoice_id=false){
        $CreditNote = new CCreditNote();
        $CreditNote->load($creditNote_id);
        if($invoice_id)
            $CreditNote->invoice_id=$invoice_id;
        $CreditNote->credit_note_status=$credit_note_status;
        return $CreditNote->store();
    }
    
    //Update by nhuht 27/07/2016
    function delete_credit_note($creditNote_id_arr){// format: $creditNote_id_arr=(1,2,4,..)
        $sql="delete from sales_credit_note where credit_note_id IN ".$creditNote_id_arr;
        // Xoa cac credit note item lien quan
        $sql_item = "delete from sales_credit_note_item where credit_note_id IN ".$creditNote_id_arr;
        
        //Xoa payment tuong ung voi credit note
        $sql_detete_payment= "select payment_id from sales_payment where credit_note_id IN ".$creditNote_id_arr;
        $allPayment=db_exec($sql_detete_payment);
        foreach ($allPayment as $payment)
        {
            //Xoa payment
            $deletePayment = "delete from sales_payment where payment_id = ".$payment['payment_id'];
            
            //Xoa payment detail
            $deletePaymentDetail = "delete from sales_payment_detail where payment_id = ".$payment['payment_id'];
            
            db_exec($deletePayment);
            db_exec($deletePaymentDetail);
            
        }
        if(db_exec($sql) && db_exec($sql_item))
            return true;
        else
            return false;
    }
    function create_view_print_pdf($creditNote_id,$is_send_mail = false){       
        require_once (DP_BASE_DIR . '/modules/sales/CHeaderCreditNote.php');
        $pdf = new MYPDFCreditNote(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(10,37, 10, 0);
        $pdf->SetHeaderMargin(3);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(true);
        $pdf->setPrintFooter(false);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('times', '', 12);
        
        require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
        require_once(DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
        $CInvoiceManager = new CInvoiceManager();
        $CsalesManager = new CSalesManager();
        $creditNote_arr=$this->list_db_creditNote($creditNote_id);
        foreach ($creditNote_arr as $creditNote_row) {
            $customer_id = $creditNote_row['customer_id'];
            $creditNote_co = $creditNote_row['credit_note_co'];
            $creditNote_no = $creditNote_row['credit_note_no'];
            $invoice_id = $creditNote_row['invoice_id'];
            $credit_date = date('d/m/Y', strtotime($creditNote_row['credit_note_date']));
            $credit_tax = $creditNote_row['credit_note_tax_id'];
            $address_id = $creditNote_row['address_id'];
        }
        $customer_arr = $this->customer_by_creditNote($customer_id);
        $invoice_arr = $CInvoiceManager->get_db_invoice($invoice_id);
        $customer_name="";
        if(count($customer_arr) <=0)
            $customer_name=$invoice_arr[0]['company_name'];
        else
            $customer_name=$customer_arr[0]['company_name'];
        $invoice_date="";
        if($invoice_arr[0]['invoice_date']!="")
            $invoice_date = date('d/m/Y',  strtotime($invoice_arr[0]['invoice_date']));
        
        $address_row = $CsalesManager->get_address_by_id($address_id);
        if(count($address_row)>0){
            if($address_row['address_type']==2)
                    $address_type = "[Billing] ";
            else if($address_row['address_type']==1)
                    $address_type = "[Job Site] ";
            else
                    $address_type = "[NA] ";

                $brand="";
                if($address_row['address_branch_name']!="")
                    $brand = $address_row['address_branch_name']." - ";
                $address_2 = "";
                if($address_row['address_street_address_2']!="")
                    $address_2 = " ".$address_row['address_street_address_2'];

            //$address = $address_row['address_street_address_1'];
            $address = $brand.$address_row['address_street_address_1'].$address_2.'<br>&nbsp;&nbsp;&nbsp;'.'Singapore '.$address_row['address_postal_zip_code'];
        }
        
        $phone1 = $address_arr['address_phone_1'];
        $phone2 = $address_arr['address_phone_2'];
        $fax = $address_arr['address_fax'];
        
        $creditNote_item_arr = $this->list_db_creditNote_item($creditNote_id);
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        $CTax = new CTax();
        $tax_arr = $CTax->list_tax();
        foreach ($tax_arr as $tax_row) {
            if($tax_row['tax_id']==$credit_tax)
                $tax_value=$tax_row['tax_rate'];
        }
       
        
        // Lay thong tin Atention
        $attention_arr = $CsalesManager->get_list_attention($customer_id);

        $SalesAttention_arr = $CsalesManager->get_salesAttention_by_SalesType($creditNote_id,'creditNote');

        $attention_option = '';
        foreach ($attention_arr as $attention_row) {
            if($attention_row['contact_id']==$SalesAttention_arr[0]['attention_id'])
                $attention_option=$attention_row['contact_first_name'] .' '. $attention_row['contact_last_name'];
        }
        $email_rr = $CsalesManager->get_attention_email($SalesAttention_arr[0]['attention_id']);
        $email="";
        if(count($email_rr) >0){
            if($email_rr['contact_email']!="")
                $email .= "Email: ".$email_rr['contact_email']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            if($email_rr['contact_mobile']!="")
                $email .= "Mobile: ".$email_rr['contact_mobile'];
        }
        
        // End Attention
        
        // Lay thong tin CO
        $tr_co="";
        $creditNote_co_arr = $this->customer_by_creditNote($creditNote_co);
        if(count($creditNote_co_arr)>0)
            $tr_co ='<tr>
                        <td>C/O</td>
                        <td>:'.$creditNote_co_arr[0]['company_name'].'</td>
                        <td></td>
                        <td></td>
                     </tr>';
        $tbl = '<table width="100%" border="0" cellpadding="0" cellspacing="0" id="aging_report" >
                    <tr valign="top">
                        <td align="center" colspan="2" height="32"><h2>CREDIT NOTE</h2></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table border="0" width="100%" cellpadding="3" cellspacing="0" >
                                <tr>
                                    <td width="11%" height="25px">Customer</td>
                                    <td width="59%">: '.$customer_name.'</td>
                                    <td width="13%">Credit Note</td>
                                    <td width="17%">: '.$creditNote_no.'</td>
                                </tr>
                                '.$tr_co.'
                                <tr>
                                    <td height="25px">Address</td>
                                    <td>: '.$address.'</td>
                                    <td >Date</td>
                                    <td >: '.$credit_date.'</td>  
                                </tr>
                                <tr>
                                    <td height="25px">Attention</td>
                                    <td>: '.$attention_option.'</td>
                                    <td>Invoice No</td>
                                    <td>: '.$invoice_arr[0]['invoice_no'].'</td>
                                </tr>
                                <tr>
                                    <td height="25px">&nbsp;</td>
                                    <td>&nbsp;&nbsp;'.$email.'</td>
                                    <td>Invoice Date</td>
                                    <td>: '.$invoice_date.'</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td></td></tr>
                    <tr>
                        <td colspan="2">
                            <table border="1" width="100%" cellpadding="3" cellspacing="0">
                                <tr align="center">
                                    <th width="5%"><b>#</b></th>
                                    <th width="80%"><b>DESCRIPTION</b></th>
                                    <th width="15%"><b>AMOUNT</b></th>
                                </tr>';
                    $i=0;$subtoal=0;$total=0;$tax_total=0;
                    foreach ($creditNote_item_arr as $creditNote_item_row) {
                         $subtoal+=$creditNote_item_row['credit_note_item_amount'];
                         $i++;
                         $tbl.='<tr valign="middle">
                                    <td align="center" height="150">'.$i.'</td>
                                    <td>'.$CsalesManager->htmlChars($creditNote_item_row['credit_note_item']).'</td>
                                    <td align="right">$'.number_format($creditNote_item_row['credit_note_item_amount'],2).'</td>
                                </tr>';
                    }
              $tbl.="</table></td></tr>";
                    $tax_value1 = $CsalesManager->round_up($tax_value/100);
                    
                    $tax_total=$subtoal*($tax_value1);
                    if($creditNote_arr[0]['tax_edit_value'])
                    {
                        $tax_total = $creditNote_arr[0]['tax_edit_value'];
                    }
                    
                    $total = $subtoal + $tax_total;
              $tbl.='<tr><td colspan="2">
                  <table border="0" width="100%" cellpadding="3" cellspacing="0">
                        <tr>
                            <td colspan="3">
                                <table cellpadding="3" border = "0" cellspacing="0">
                                    <tr align="right">
                                        <td width="85%" >Sub-Total:</td>
                                        <td width="15%" >$'.number_format($subtoal,2).'</td>
                                    </tr>
                                    <tr align="right">
                                        <td>GST '.$tax_value.'%:</td>
                                        <td>$'.number_format($tax_total, 2).'</td>
                                    </tr>
                                    <tr align="right">
                                        <td>Total:</td>
                                        <td>$'.number_format($total,2).'</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>';
                $tbl.='</table>
                </td></tr></table>';
            $tbl.='<table border="0" width="100%" cellpadding="3" cellspacing="0">
                    <tr><td></td></tr>
                    <tr>
                        <td colspan="2">
                            Thank you & best regards.
                            <br><br><br>
                            Yours Sincerely,
                            <br><br><br>
                            Authorised Signature
                        </td>
                    </tr>
                  </table>';
                        
            $pdf->AddPage('P', 'A4');
            $pdf->writeHTML($tbl, true, false, false, false, '');
            ob_end_clean();
            if (!$is_send_mail)
                //$pdf->Output('ddd.pdf', 'I');
            $pdf->Output('credit_note.pdf', 'I');
            else
                return $pdf->Output('', 'S'); // tao ra chuoi document, ten bo qua
    }
    function send_email_credit($creditNote_id_array,$content,$sender,$subject,$reciver){
        global $AppUI;
        require_once './lib/swift/swift_required.php';
        
        if (($count = count($creditNote_id_array)) > 0) {
            for ($i = 0; $i < $count; $i++) {

                $creditNote_id = $creditNote_id_array[$i];                
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

                    $attachment = Swift_Attachment::newInstance($this->create_view_print_pdf($creditNote_id_array, true), 'creditNote.pdf', 'application/pdf');
                    $mail->attach($attachment);

                    // Send the message
                    $result = $mailer->send($mail);
            }
        }
        return true;
    }
    function update_credit_item($creditItem_id,$creditItem_des,$creditItem_amount,$creditItem_order){
        $creditItem = new CCreditNoteItem();
        $creditItem->load($creditItem_id);
        $creditItem->credit_note_item_id=$creditItem_id;
        $creditItem->credit_note_item = $creditItem_des;
        $creditItem->credit_note_item_amount = $creditItem_amount;
        $creditItem->credit_note_item_order = $creditItem_order;
        $creditItem->store();
        return $creditItem->credit_note_item_id;
    }
    function create_print_html($creditNote_id){
        require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
        require_once(DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
        $CInvoiceManager = new CInvoiceManager();
        $CsalesManager = new CSalesManager();
        $supplier_arr = $CsalesManager->get_supplier_info();
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
        $supplier .= '<tr><td>Tel: '.$phone.', Fax: '.$fax.'</td></tr>';
    //    $supplier .= '<tr><td>Fax: '.$fax.'</td></tr>';
        $supplier .= '<tr><td>GST Reg No: '. $sales_owner_gst_reg_no .'</td></tr>';
    //    $supplier.= '<tr><td>Reg No: '. $supplier_arr['sales_owner_postal_code'] .'</td></tr>';
        $supplier .= '<tr><td>Email: '.$email.'</td></tr>';
        
        if($web!="")
        $supplier .= '<tr><td>Website: '.$web.'</td></tr>';
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        
        $files = list_files($url);
        if(count($files)>0)
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
                $img = '<td width="27%" rowspan="1" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" width="200" /></td>';
                $w="73%";
        }
        
//        $files = scandir($url);
//        $i = count($files) -1;
//        $w="100%";
//        if (count($files) > 2 && $files[$i]!=".svn") {
//            $path_file = $url . $files[$i];
//            $img = '<td width="27%" rowspan="1" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" width="200" /></td>';
//            $w="73%";
//        }
        
        $creditNote_arr=$this->list_db_creditNote($creditNote_id);
        foreach ($creditNote_arr as $creditNote_row) {
            $customer_id = $creditNote_row['customer_id'];
            $creditNote_no = $creditNote_row['credit_note_no'];
            $invoice_id = $creditNote_row['invoice_id'];
            $credit_date = date('d/m/Y', strtotime($creditNote_row['credit_note_date']));
            $credit_tax = $creditNote_row['credit_note_tax_id'];
            $address_id = $creditNote_row['address_id'];
        }
        $customer_arr = $this->customer_by_creditNote($customer_id);
        $invoice_arr = $CInvoiceManager->get_db_invoice($invoice_id);
        $customer_name="";
        if(count($customer_arr) <=0)
            $customer_name=$invoice_arr[0]['company_name'];
        else
            $customer_name=$customer_arr[0]['company_name'];
            
        $invoice_date="";
        if($invoice_arr[0]['invoice_date']!="")
            $invoice_date = date('d/m/Y',  strtotime($invoice_arr[0]['invoice_date']));
        
        $address_arr = $CsalesManager->get_address_by_id($address_id);
        $address = $address_arr['address_street_address_1'];
        $phone1 = $address_arr['address_phone_1'];
        $phone2 = $address_arr['address_phone_2'];
        $fax = $address_arr['address_fax'];
        
        $creditNote_item_arr = $this->list_db_creditNote_item($creditNote_id);
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        $CTax = new CTax();
        $tax_arr = $CTax->list_tax();
        foreach ($tax_arr as $tax_row) {
            if($tax_row['tax_id']==$credit_tax)
                $tax_value=$tax_row['tax_rate'];
        }
        $tbl = '<div style="width:65%; margin:auto;font-family: times new roman; padding:8px; border: 1px solid #333333;">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" id="aging_report" >
                    <tr>
                        '.$img.'
                        <td width="'.$w.'" ><table border="0" style="font-size:15px;font-family: Liberation Serif;"  width="100%">'.$supplier.'</table></td>
                    </tr>
                    <tr><td align="right" colspan="2" style="font-size:16px;font-weight: bold;">Voucher No: '.$creditNote_no.'<hr style="margin:0px;"></td></tr>
                    <tr>
                        <td align="center" colspan="2" height="35"><h2>CREDIT NOTE</h2></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table border="0" width="100%" cellpadding="3" cellspacing="0" >
                                <tr>
                                    <td width="13%" height="25px">Customer</td>
                                    <td width="57%">: '.$customer_name.'</td>
                                </tr>
                                <tr>
                                    <td height="25px">Address</td>
                                    <td>: '.$address.'</td>
                                    <td>Date</td>
                                    <td>: '.$credit_date.'</td>
                                </tr>
                                <tr>
                                    <td height="25px">Phone</td>
                                    <td>: '.$phone1.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$phone2.'</td>
                                    <td>Invoice No</td>
                                    <td>: '.$invoice_arr[0]['invoice_no'].'</td>
                                </tr>
                                <tr>
                                    <td height="25px">Fax</td>
                                    <td>: '.$fax.'</td>
                                    <td>Date</td>
                                    <td>: '.$invoice_date.'</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td></td></tr>
                    <tr>
                        <td colspan="2">
                            <table border="1" width="100%" cellpadding="3" cellspacing="0">
                                <tr align="center">
                                    <th width="5%"><b>#</b></th>
                                    <th width="80%"><b>DESCRIPTION</b></th>
                                    <th width="15%"><b>AMOUNT</b></th>
                                </tr>';
                    $i=0;$subtoal=0;$total=0;$tax_total=0;
                    foreach ($creditNote_item_arr as $creditNote_item_row) {
                         $subtoal+=$creditNote_item_row['credit_note_item_amount'];
                         $i++;
                         $tbl.='<tr valign="middle">
                                    <td align="center" height="150">'.$i.'</td>
                                    <td>'.$creditNote_item_row['credit_note_item'].'</td>
                                    <td align="right">$'.number_format($creditNote_item_row['credit_note_item_amount'],2).'</td>
                                </tr>';
                    }
              $tbl.="</table></td></tr>";
                    $tax_total=$subtoal*($tax_value/100);
                    $total = $subtoal + $tax_total;
              $tbl.='<tr><td colspan="2">
                  <table border="0" width="100%" cellpadding="3" cellspacing="0">
                        <tr>
                            <td colspan="3">
                                <table cellpadding="3" width="100%" border = "0" cellspacing="0">
                                    <tr align="right">
                                        <td width="85%" >Sub-Total:</td>
                                        <td width="15%" >$'.number_format($subtoal,2).'</td>
                                    </tr>
                                    <tr align="right">
                                        <td>GST '.$tax_value.'%:</td>
                                        <td>$'.number_format($tax_total, 2).'</td>
                                    </tr>
                                    <tr align="right">
                                        <td>Total:</td>
                                        <td>$'.number_format($total,2).'</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>';
                $tbl.='</table>
                </td></tr></table>';
            $tbl.='<table border="0" width="100%" cellpadding="3" cellspacing="0">
                    <tr><td></td></tr>
                    <tr>
                        <td colspan="2">
                            Thank you & best regards.
                            <br><br>
                            Yours Sincerely,
                        </td>
                    </tr>
                  </table></div>';
     
    echo $tbl;
        
    }
    function get_exist_creditNote_no($credit_no){
        $credit_arr=$this->list_db_creditNote();
        foreach ($credit_arr as $credit_row) {
            if($credit_no==$credit_row['credit_note_no'])
                return true;
        }
        return false;
    }
    
    function list_db_creditNote_report($customer_id=false,$from_date=false,$to_date=false,$invoice_no=false,$status=false){
        
        $q = new DBQuery();
        $q ->addTable('sales_credit_note','sc');
        $q ->addJoin('clients','c', 'c.company_id = sc.customer_id');
        if(isset($invoice_no) && $invoice_no==1)
        {
            $q ->addJoin('sales_invoice','d', 'sc.invoice_id=d.invoice_id');
            $q ->addQuery('sc.*,c.company_name,d.invoice_no');
        }
        else
            $q ->addQuery('sc.*,c.company_name');
        if($customer_id)
            $q->addWhere ('sc.customer_id = '.intval($customer_id));
        if($from_date)
            $q->addWhere ('sc.credit_note_date >= "'.$from_date.'"');
        if($to_date)
            $q->addWhere ('sc.credit_note_date <= "'.$to_date.'"');
        if($status)
            $q->addWhere ('sc.credit_note_status ='.intval($status));
        return $q->loadList();
    }
    
    public function getCreditNoteByInvoice($invoice_id){
        $q = new DBQuery();
        $q->addTable('sales_credit_note');
        $q->addQuery('credit_note_no,credit_note_id');
        $q->addWhere('invoice_id='.  intval($invoice_id));
        
        return $q->loadList();   
    }
    
    function list_db_credit($customer_arr=false,$from_date=false,$to_date=false){
        
      
        $q = new DBQuery();
        $q ->addTable('sales_credit_note');
        $q ->addQuery('*');
        if($customer_arr)
            $q->addWhere ('customer_id IN'.($customer_arr));
        if($from_date)
            $q->addWhere ('credit_note_date >= "'.$from_date.'"');
        if($to_date)
            $q->addWhere ('credit_note_date <= "'.$to_date.'"');
        return $q->loadList();
    }
    
    function totalCreditNoteByInvoice($invoice_id,$date_from=false,$date_to=false)
    {
        $q = new DBQuery();
        $q->addTable('sales_credit_note');
        $q->addJoin('sales_credit_note_item', 'tbl2', 'sales_credit_note.credit_note_id=tbl2.credit_note_id');
        $q->addQuery('SUM(tbl2.credit_note_item_amount) +SUM(tax_edit_value) as amount ');
        $q->addWhere('invoice_id='.  intval($invoice_id));
        if($date_to)
            $q->addWhere ('credit_note_date <= "'.$date_to.'"');
        if($date_from)
            $q->addWhere ('credit_note_date >= "'.$date_from.'"');
        $allInvice= $q->loadList();  
        $amount =  $allInvice[0]['amount'];
        
        return $amount;
    }
    
    function totalCreditNoteIsInvoice($date_from=false,$date_to=false)
    {
        $q = new DBQuery();
        $q->addTable('sales_credit_note');
        $q->addJoin('sales_credit_note_item', 'tbl2', 'sales_credit_note.credit_note_id=tbl2.credit_note_id');
        $q->addQuery('SUM(tbl2.credit_note_item_amount) +SUM(tax_edit_value) as amount ');
        $q->addWhere('invoice_id > 0');
        if($date_to)
            $q->addWhere ('credit_note_date <= "'.$date_to.'"');
        if($date_from)
            $q->addWhere ('credit_note_date >= "'.$date_from.'"');
        $allInvice= $q->loadList();  
        $amount =  $allInvice[0]['amount'];
        
        return $amount;
    }

}

