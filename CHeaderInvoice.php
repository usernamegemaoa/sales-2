<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
class MYPDFInvoice extends TCPDF{   
    //Page header
    public $page_count = 1;
    public $last_page_flag = false;
    
    public function Close() {
        $this->last_page_flag = true;
        parent::Close();
    }
    public function Header()
    {
        require_once(DP_BASE_DIR."/modules/sales/CSalesManager.php");
        require_once(DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
        require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
        
        $CTemplatePDF = new CTemplatePDF();
        $CSalesManager= new CSalesManager();
        $CInvoiceManager=new CInvoiceManager();
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        $files = list_files($url);
        $i = count($files) -1;
        $w="100%";$img="";$img5="";$img6="";
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
        // active default Template pdf //
        $template_pdf_1 = $CTemplatePDF->get_template_pdf(1);
        $template_pdf_2 = $CSalesManager->get_template_pdf(2);
        $template_pdf_3 = $CSalesManager->get_template_pdf(3);
        $template_pdf_4 = $CSalesManager->get_template_pdf(4);
        $template_pdf_5 = $CSalesManager->get_template_pdf(5);
        $template_pdf_6 = $CSalesManager->get_template_pdf(6);
        $pdf_css = "";
        if($template_pdf_1[0]['template_default']==1)
            $pdf_css = "";
        else if($template_pdf_2==1)
            $pdf_css = 'align="right"';
        else if($template_pdf_3==1)
        {
            $pdf_css = 'align="right"';
        }
        // end active default Template pdf //
        
        // Get supplier Sales //
        if(count($files)>0)
        {
        $supplier = '<tr ><td style="font-size:1.2em;"><b>'. $sales_owner_name .'</b></td></tr>';
        $supplier.= '<tr><td>'.$sales_owner_address.'</td></tr>';
        }
        else
        {
             $supplier = '<tr ><td style="font-size:1.2em; text-align:center"><b>'. $sales_owner_name .'</b></td></tr>';
            $supplier.= '<tr><td style="text-align:center">'.$sales_owner_address.'</td></tr>';
        }

        if($fax!="")
            $fax_mycool = '.&nbsp;&nbsp;&nbsp;&nbsp;Contact no: '.$fax;
        if($template_pdf_1[0]['template_server'] == 1)
        {
            if(count($files)>0)
            {
                if($sales_owner_reg_no!="")
                    $supplier .= '<tr><td>Reg No: '. $sales_owner_reg_no .'</td></tr>';
                if($sales_owner_gst_reg_no)
                    $supplier .= '<tr><td>CO. Reg. No: '. $sales_owner_gst_reg_no .'</td></tr>';
                $supplier .= '<tr><td>Tel: '.$phone.$fax_mycool.'</td></tr>';
                $supplier .= '<tr><td>Email Address: '.$email.'</td></tr>';
            }
            else
            {
                if($sales_owner_reg_no!="")
                $supplier .= '<tr><td style="text-align:center">Reg No: '. $sales_owner_reg_no .'</td></tr>';
                if($sales_owner_gst_reg_no)
                    $supplier .= '<tr><td style="text-align:center">CO. Reg. No: '. $sales_owner_gst_reg_no .'</td></tr>';
                $supplier .= '<tr><td style="text-align:center">Tel: '.$phone.$fax_mycool.'</td></tr>';
                $supplier .= '<tr><td style="text-align:center">Email Address: '.$email.'</td></tr>';
                
            }
            
        }
        else
        {
            if(count($files)>0)
            {
            if($sales_owner_gst_reg_no!="")
                $supplier .= '<tr><td>GST Reg No: '. $sales_owner_gst_reg_no .'</td></tr>';
            if($sales_owner_reg_no!="")
                $supplier .= '<tr><td>Reg No: '. $sales_owner_reg_no .'</td></tr>';
            $supplier .= '<tr><td>Tel: '.$phone.', Fax: '.$fax.'</td></tr>';
            if($email!="")
                $supplier .= '<tr><td>Email: '.$email.'</td></tr>';
            }
            else
            {
                if($sales_owner_gst_reg_no!="")
                    $supplier .= '<tr><td style="text-align:center">GST Reg No: '. $sales_owner_gst_reg_no .'</td></tr>';
                if($sales_owner_reg_no!="")
                    $supplier .= '<tr><td style="text-align:center">Reg No: '. $sales_owner_reg_no .'</td></tr>';
                $supplier .= '<tr><td style="text-align:center">Tel: '.$phone.', Fax: '.$fax.'</td></tr>';
                if($email!="")
                    $supplier .= '<tr><td style="text-align:center">Email: '.$email.'</td></tr>';
            }
        }
    //    $supplier.= '<tr><td>Reg No: '. $supplier_arr['sales_owner_postal_code'] .'</td></tr>';

        if($web!="")
        {
            if(count($files)>0)
                $supplier .= '<tr><td >Website: '.$web.'</td></tr>';
            else
                $supplier .= '<tr><td style="text-align:center">Website: '.$web.'</td></tr>';
        }
        // End Subplier sales //
        
        if($fax!="")
        {
            $fax5 =  ', Fax: '.$fax;
            $fax6 =  ' &nbsp;&nbsp;Fax: '.$fax;
        }
        // Get supplier Sales Template 5 //
        //$supplier5 = '<tr><td style="font-size:1.2em;"><b>'. $sales_owner_name .'</b></td></tr>';
        $supplier5= '<tr><td>'.$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].'</td></tr>';
        $supplier5.= '<tr><td>Tel: '.$phone.$fax5.'</td></tr>';
    //    $supplier .= '<tr><td>Fax: '.$fax.'</td></tr>';
        $supplier5 .= '<tr><td>Reg. No: '. $sales_owner_reg_no .'</td></tr>';
    //    $supplier.= '<tr><td>Reg No: '. $supplier_arr['sales_owner_postal_code'] .'</td></tr>';
        $supplier5 .= '<tr><td>Email: '.$email.'</td></tr>';
        if($web!="")
        $supplier5 .= '<tr><td>Website: '.$web.'</td></tr>';
        
        $email = ' &nbsp;&nbsp;Email: '.$email;
                
        $supplier6 = "";
        $supplier6.='<tr><td>'.$supplier_arr['sales_owner_address1'] .' '.$supplier_arr['sales_owner_address2'].' '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].'</td></tr>';
        $supplier6.= '<tr><td>Tel: '.$phone.$fax6.$email.'</td></tr>';
        $supplier6 .= '<tr><td>GST Reg No: '. $sales_owner_gst_reg_no .'</td></tr>';
        //$supplier6 .= '<tr><td>Email: '.$email.'</td></tr>';
        if($web!="")
            $supplier6 .= '<tr><td>Website: '.$web.'</td></tr>';
        
        // End Subplier sales //
        
        // Get LOGO sales //
        
        if(count($files)>0)
        {
        $path_file = $url . $files[$i];
        $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" /></td>';
        $img5 = '<img height="21" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
        $img6 = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
        $w="71.5%";
        }
        //Get Info Invoice number // 
        $invoice_details_arr = $CInvoiceManager->get_db_invoice($_REQUEST['invoice_id']);
        
        $invocie_id = $invoice_details_arr[0]['invoice_id'];
        $date = $invoice_details_arr[0]['invoice_date'];
        $invoice_no = $invoice_details_arr[0]['invoice_no'];
        $term_value = $invoice_details_arr[0]['term'];
        $po_number = $invoice_details_arr[0]['po_number'];
        $our_delivery = $invoice_details_arr[0]['our_delivery_order_no'];
        
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
        $info2 = 'Invoice No: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp; '.$invoice_no.'<br>';
        $info2 .= 'Date: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp; '.date('d-m-Y',  strtotime($date)).'<br>';
        $info2 .= 'Your P.O Number: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
             '. CSalesManager::htmlChars($po_number).'<br>';
        $info2 .= 'Terms: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
             '. $term.'<br>';
        $info2 .= 'Our Delivery Order No: '.CSalesManager::htmlChars($our_delivery);
        
        $info5  = '<table border="0" width="100%">';
        
        $template_server_5 = $CSalesManager->get_template_server(5);
        if( $template_server_5 == 8)
        {
            if(isset($_REQUEST['invoice_revision_id']) && $_REQUEST['invoice_revision_id']>0)
            {
                $invoice_rev_details_arr = $CInvoiceManager->get_db_invoice_revsion($_REQUEST['invoice_revision_id']);
                if (count($invoice_rev_details_arr) > 0) {

                    $reference_value=$invoice_rev_details_arr[0]['reference_no'];
                }
            }
            
            $info5 .= '<tr style="font-weight:bold;"><td width="30%">Reference No</td><td width="70%">: '.$reference_value.'</td></tr>';
        }
        $info5 .= '<tr style="font-weight:bold;"><td>Invoice No</td><td>: '.$invoice_no.'</td></tr>';
        $info5 .= '<tr><td>Date</td><td>: '.date('d-m-Y',  strtotime($date)).'</td></tr>';
        if($po_number!="")
            $info5 .= '<tr><td>Your P.O Number</td><td>: '. CSalesManager::htmlChars($po_number).'</td></tr>';
        if($term!="")
            $info5 .= '<tr><td>Terms</td><td>: '.$term.'</td></tr>';
        if($our_delivery!="")
            $info5 .= '<tr><td>Our Service Order No</td><td>: '.CSalesManager::htmlChars($our_delivery).'</td></tr>';
        $info5 .= '</table>';
        
        // End info Invoice number
        
        if($template_pdf_1[0]['template_server']==1 && $template_pdf_1[0]['template_default']==1)
        {
            $txt_header = $CSalesManager->HeaderPDFMycool();
        }
        else if($template_pdf_1[0]['template_default']==1 || $template_pdf_2==1)
        {
            $txt_header='<table border="0" cellspacing="0" cellpadding="0">
                    <tr valign = "top" height="60">';
                        if(count($files)>0){
                            $txt_header.='<td width="30%" align="center"><img height="95" src="'. $base_url . $files[0] .'" alt="Smiley face" /></td>';
                            $txt_header.='<td width="'.$w.'" '.$pdf_css.'><table border="0"  width="100%">'.$supplier.'</table></td>';
                        }
                        else
                            $txt_header.='<td  width="'.$w.'" '.$pdf_css.'><table style="text_align:center" border="0"  width="100%">'.$supplier.'</table></td>';
                    $txt_header.='</tr>
                    <tr>
                        <td colspan="2" align="right" style="font-size:1.2em;" width="100%"><b>Invoice No: '.$invoice_no.'</b><hr></td>
                    </tr>
               </table>';
            //$this->Image($base_url.$files[0], '6','', '50','30','','','C');
        }
        else if($template_pdf_3==1)
        {
            $txt_header='<table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr valign = "top" height="60">
                        <td width="50%" rowspan="2" align="left"><b><span style="font-size:1.8em;">TAX INVOICE</span></b>
                            <br><br>'.$info2.'
                        </td>
                        <td width="50%" '.$pdf_css.'><table border="0"  width="100%">'.$supplier.'</table></td>
                    </tr>
               </table>'; 
        }
        else if($template_pdf_4==1)
        {
            $txt_header='<table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr valign = "top" height="60">
                        <td width="75%" '.$pdf_css.'><table border="0"  width="100%">'.$supplier.'</table></td>
                        <td width="25%" rowspan="2" align="left">'.$img.'
                        </td>
                    </tr>
               </table>'; 
        }
        else if($template_pdf_5==1)
        {
            $txt_header='<table border="0" width="100%" cellspacing="0" cellpadding="3">
                    <tr><td colsapn="2" align="right" width="99%"></td></tr>
                    <tr valign = "top">
                        <td width="38%" rowspan="2" align="left">'.$info5.'</td>
                        <td width="61%" align="right">'.$supplier5.'</td>
                    </tr>
               </table>';
            $this->Image($base_url.$files[0], '103','3','0','6','','','R');
        }
        else if($template_pdf_6==1)
        {
            $txt_header='<table border="0" width="100%"   cellspacing="0" cellpadding="15">
                    <tr><td align="center" colspan="2" width="99%"></td></tr>
                    <tr valign = "top">
                        <td width="24%"></td>
                        <td width="80%" align="left">'.$supplier6.'</td>
                    </tr>
               </table>'; 
            $this->Image($base_url.$files[0], '50','15', '','','','','C');
            $this->Ln(12);
            $this->setMargins('', 60);
        }
        if($template_pdf_1[0]['template_server'] == 7)
        {
            require_once (DP_BASE_DIR."/modules/sales/CHeaderApbiz.php");
            $header_Apbiz=new CHeaderApbiz();
            $txt_header=$header_Apbiz->HeaderApbiz();
        }
        $this->SetFont('helvetica', '', 9.6);

        $this->WriteHTML($txt_header); 
        
    }

        public function Footer() {
            $this->SetY(-20);
            $this->SetFont('helvetica', '',9.6);
            require_once(DP_BASE_DIR."/modules/sales/CSalesManager.php");
            require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
            require_once(DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
            require_once (DP_BASE_DIR."/modules/sales/CTax.php");
            
            $CSalesManager= new CSalesManager();
            $CTemplatePDF = new CTemplatePDF();
            $CInvoiceManager=new CInvoiceManager();
            
            
            
            $template_pdf_1 = $CSalesManager->get_template_pdf(1);
            $template_pdf_6 = $CSalesManager->get_template_pdf(6);
            $template_pdf_1_array = $CTemplatePDF->get_template_pdf(1);
            
            $account_tex = '<tr><td height="20">&nbsp;</td></tr><tr><td height="35">Yours Sincerely</td></tr>';
            if($template_pdf_1_array[0]['template_server']==4)
                $account_tex = '<tr><td>Yours Sincerely,<br>&nbsp;&nbsp;&nbsp;&nbsp;Chris Goh</td></tr>';
            
            // Get LOGO sales //
            $url = DP_BASE_DIR. '/modules/sales/images/logo/';
            $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
            
            
            $files = list_files($url);
            if(count($files)>0)
            {
                $i = count($files)-1;
                $path_file = $url . $files[$i];
                $img6 = "&nbsp;";
                if($this->last_page_flag)
                    $img6 = '<hr><img width="150" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
            }
            
//            $files = scandir($url);
//            $i = count($files) -1;
//            if (count($files) > 2 && $files[$i]!=".svn") {
//                $path_file = $url . $files[$i];
//                $img6 = "&nbsp;";
//                if($this->last_page_flag)
//                    $img6 = '<hr><img width="150" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
//            } // Get LOGO sales //
            
            $template_pdf_1_array = $CTemplatePDF->get_template_pdf(1);
            
            if($template_pdf_1==1)
            {
                $this->SetY(-20);
            $html = '<table border="0" width="100%">
                     <tr>
                        <td colspan="2">
                            <table width="107%" border="0">';
            if($this->last_page_flag)
            {
                $this->SetY(-30);
                $html.= '<tr>
                                    <td>Thank you & best regards</td>
                                    
                                </tr>
                                '.$account_tex;
            }
            
            $html.= '<tr>';
            if($template_pdf_1_array[0]['template_default']==1 && $template_pdf_1_array[0]['footer_text_invoice']!="")
                   $html.= '<td ><b>Computer generated document, no signature is required.</b></td>';
            else
                   $html.=            '<td ><b>Authorised Signature</b></td>';
            $html.='            </tr>
                                <tr>
                                    <td align="right" >Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages().'</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    </table>
              ';
            }
            else if($template_pdf_6==1)
            {
                if($this->last_page_flag)
                    $this->SetY(-70);
                else
                    $this->SetY(-20);
                
                    $invoice_id=$_REQUEST['invoice_id'];
                    $invoice_revision_id = $_REQUEST['invoice_revision_id'];
                    $invoice_rev_details_arr = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
                    $discount = $invoice_rev_details_arr[0]['invoice_revision_discount'];
                    $tax_id = $invoice_rev_details_arr[0]['invoice_revision_tax'];
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


                    $percent = "";
                    if($tax_value !="")
                    {
                            $percent = $tax_value."%";
                    }
                    $total_item_show = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);

                    $total_item_show_last_discount = $total_item_show - $discount;
                    $total_item_tax_show = 0;
                    if ($tax_id) {
                        $caculating_tax = (floatval($total_item_show_last_discount) * floatval($tax_value) / 100);
                        $total_item_tax_show =$CSalesManager->round_up($caculating_tax);
                        if($invoice_rev_details_arr[0]['invoice_revision_tax_edit']!=0)
                            $total_item_tax_show = $invoice_rev_details_arr[0]['invoice_revision_tax_edit'];
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
                
                $total_item_show = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
                
                $amount_due =  $total_item_show_last_discount + $total_item_tax_show - $tr_paid;
                
    
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
                            
                            <td align="right">$'.number_format($total_item_tax_show,2).'</td>
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
                
                
                // Hien thi box total
                    $tbl_footer = "";$tbl_no="";
                if($this->last_page_flag)
                {
                    $tbl_no = '<div>
                                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    Thank you. We look forward to being of services to you again.</b>
                                    <hr></br>
                                    <span style="font-weight:bold;">E. & O. E.</span>
                                </div>';
                    
                    $tbl_footer='<table width="99%" border="0" cellpadding="0" spacepadding="0">';
                        $tbl_footer.='<tr>
                                <td colspan="'.$col.'" width="55%">'.$CSalesManager->htmlChars($invoice_rev_details_arr[0]['invoice_revision_term_condition']).'</td>
                                <td colspan="3" width="45%">'.$tbl_total6.'</td>
                            </tr>';
                    $tbl_footer.='</table>';
                }
//                $img6_footer = '<img height="40" src="'.DP_BASE_URL.'/modules/sales/images/sales.jpg" />';


                $html=$tbl_no;
                $html .= '<table border="0" width="100%">
                            <tr>
                                <td>'.$tbl_footer.'</td>
                            </tr>
                         <tr>
                            <td colspan="2">
                                <table width="100%" border="0">
                                    <tr>
                                        <td width="160">'.$img6.'</td>
                                        <td align="right" width="560"><br><br>Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages().'</td>    
                                    </tr>
                                </table>
                            </td>
                        </tr>';
//                    $html .='<tr ><td align="center" width="100%" >'.$img6_footer.'</td></tr> ';          

//                $this->SetY(-70);
                $html .='</table>';
            }
            $this->writeHTML($html);
            //$this->Cell(0, 20,'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false,'T','M');
    }
}
