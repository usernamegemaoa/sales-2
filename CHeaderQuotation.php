<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
class MYPDFQuotation extends TCPDF{ 
    
    public $last_page_flag = false;
    
    public function Close() {
        $this->last_page_flag = true;
        parent::Close();
    }
    
    public function Header() { 
        global $ocio_config;
        $supplier_arr = CSalesManager::get_supplier_info();
        require_once(DP_BASE_DIR."/modules/sales/CQuotationManager.php");
        $CQuotationManager = new CQuotationManager();
        require_once(DP_BASE_DIR."/modules/sales/CSalesManager.php");
        $CSalesManager = new CSalesManager();
        require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
        $CTemplatePDF = new CTemplatePDF();
        $template_pdf_1 = $CTemplatePDF->get_template_pdf(1);
        $template_pdf_2 = $CSalesManager->get_template_pdf(2);
        $template_pdf_3 = $CSalesManager->get_template_pdf(3);
        $template_pdf_4 = $CSalesManager->get_template_pdf(4);
        $template_pdf_5 = $CSalesManager->get_template_pdf(5);
        $template_pdf_6 = $CSalesManager->get_template_pdf(6);
        
        $template_server_5 = $CSalesManager->get_template_server(5);
        
         //Get Info Invoice number // 
        $quotation_arr =$CQuotationManager->get_db_quotation($_REQUEST['quotation_id']);
        $quotation_arr_rev = $CQuotationManager->get_db_quotation_revsion($_REQUEST['quotation_revision_id']);
        
        $customer_id = $quotation_arr[0]['customer_id'];
        $Customer_co_id = $quotation_arr[0]['quotation_CO'];
        $date = $quotation_arr[0]['quotation_date'];
        $quotation_no = $quotation_arr[0]['quotation_no'];
        $service_order = $quotation_arr[0]['service_order'];
        
        // Info Quotaiton
        $info2 = 'Quotation No&nbsp;&nbsp;: '. $quotation_arr_rev[0]['quotation_revision'] .'<br>';
        $info2 .= 'Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            : '. date($ocio_config['php_abms_date_format'],  strtotime($date)).'<br>';
        //$info2 .= 'Revision Re: '. $quotation_rev.'<br/>';
        $info2 .= 'Servicer Oder : '. $service_order;
        
        // Info Quotaiton     
        $info5  = '<table border="0" width="100%">';
        
        if( $template_server_5 == 8)
        {
            $reference_no = $quotation_arr_rev[0]['reference_no'];
            $info5 .= '<tr style="font-weight:bold;"><td width="30%">Reference No</td><td width="70%">: '.$reference_no.'</td></tr>';
        }
        $info5 .= '<tr style="font-weight:bold;"><td width="30%">Quotation No</td><td width="70%">: '.$quotation_no.'</td></tr>';
        $info5 .= '<tr ><td>Revision No</td><td>: '.substr($quotation_arr_rev[0]['quotation_revision'], -2).'</td></tr>';
        $info5 .= '<tr><td>Date</td><td>: '.date($ocio_config['php_abms_date_format'],  strtotime($date)).'</td></tr>';
        $info5 .= '<tr><td>Servicer Oder</td><td>: '.$service_order.'</td></tr>';
        $info5 .= '</table>';
         //End Get Info Invoice number // 
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
            $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
            
        $files = list_files($url);
        // active default Template pdf
        
        if (count($supplier_arr) > 0) {
            $sales_owner_name = $supplier_arr['sales_owner_name'];
            if( $template_pdf_1[0]['template_server'] == 7)
            {
                $sales_owner_address =$supplier_arr['sales_owner_address1'];
                if($supplier_arr['sales_owner_address2'] !="")
                    $sales_owner_address.=','.$supplier_arr['sales_owner_address2'].'';
                if($supplier_arr['sales_owner_country'] !="")
                    $sales_owner_address.=', '.$supplier_arr['sales_owner_country'].' ';
                $sales_owner_address.=$supplier_arr['sales_owner_postal_code']!=""?$supplier_arr['sales_owner_postal_code']:"";
            }
            else
                $sales_owner_address =$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'];
            //$sales_owner_address += 
            $phone =$supplier_arr['sales_owner_phone1'];
            $fax = $supplier_arr['sales_owner_fax'];
            $email =$supplier_arr['sales_owner_email'];
            $web = $supplier_arr['sales_owner_website'];
            $sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
            $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
        }
        
        
        // end active default Template pdf

        $supplier = '<tr><td style="font-size:1.2em;"><b>'. $sales_owner_name .'</b></td></tr>';
        
        $supplier.= '<tr><td>'.$sales_owner_address.'</td></tr>';
        if($fax!="")
        {
            $fax5 = ', Fax: '.$fax;
            $fax6 = 'Fax: '.$fax;
            $fax_mycool = '.&nbsp;&nbsp;&nbsp;&nbsp;Contact no: '.$fax;
        }
        
        if($template_pdf_1[0]['template_server'] == 1)
        {
            if($sales_owner_reg_no!="")
                $supplier .= '<tr><td>Reg No: '. $sales_owner_reg_no .'</td></tr>';
            if($sales_owner_gst_reg_no)
                $supplier .= '<tr><td>CO. Reg. No: '. $sales_owner_gst_reg_no .'</td></tr>';
            $supplier .= '<tr><td>Tel: '.$phone.$fax_mycool.'</td></tr>';
            
                $supplier .= '<tr><td>Email Address: '.$email.'</td></tr>';
            
        }
        else if( $template_pdf_1[0]['template_server'] == 7)
        {
            
            if($sales_owner_gst_reg_no)
                $supplier .= '<tr><td>CO. Reg. No: '. $sales_owner_gst_reg_no .'</td></tr>';
            $supplier .= '<tr><td>Tel: '.$phone.$fax_mycool.'</td></tr>';
            $supplier .= '<tr><td>Email: '.$email.'</td></tr>';
            if($sales_owner_reg_no!="")
                $supplier .= '<tr><td>Reg No: '. $sales_owner_reg_no .'</td></tr>';
        }
        else
        {
            if($sales_owner_gst_reg_no!="")
                $supplier .= '<tr><td>GST Reg No: '. $sales_owner_gst_reg_no .'</td></tr>';
            if($sales_owner_reg_no!="")
                $supplier .= '<tr><td>Reg No: '. $sales_owner_reg_no .'</td></tr>';
            $supplier .= '<tr><td>Tel: '.$phone.', Fax: '.$fax.'</td></tr>';
            if($email!="")
                $supplier .= '<tr><td>Email: '.$email.'</td></tr>';
        }
        $supplier5 = '<tr><td>'.$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].'</td></tr>';
        $supplier5 .= '<tr><td>Tel: '.$phone.$fax5.'</td></tr>';
        if($sales_owner_reg_no!="")
            $supplier5 .= '<tr><td>Reg No: '. $sales_owner_reg_no .'</td></tr>';
        $supplier5 .= '<tr><td>Email: '.$email.'</td></tr>';
        
        if($email!="")
        {
            $email6="Email: ".$email;
        }
        if($web!="")
            $web6 = 'Website: '.$web.'&nbsp;&nbsp;';
        
        if($web!="")
            $supplier .= '<tr><td>Website: '.$web.'</td></tr>';
        if($sales_owner_gst_reg_no!="")
        {
           $sales_owner_gst_reg_no6='GST Reg. No: '. $sales_owner_gst_reg_no.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        if($sales_owner_reg_no!="")
        {
            $sales_owner_reg_no6 = 'CO. Reg. No: '. $sales_owner_gst_reg_no.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        
        $supplier6 = "";
        $supplier6.=$supplier_arr['sales_owner_address1'] .' '.$supplier_arr['sales_owner_address2'].' '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].
                            '&nbsp;&nbsp;&nbsp;&nbsp;Tel: '.$phone.'&nbsp;&nbsp;'.$fax6.'&nbsp;&nbsp;'.$email6.'<br>'.$sales_owner_reg_no6.$sales_owner_gst_reg_no6.$web6;

            
        $w="100%";
        if(count($files)>0 && $files[$i]!=".svn")
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
                $img = '<td width="30%" rowspan="2" align="center"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="100" /></td>';
                $img5 = '<img height="21" src="'. $base_url . $files[$i] .'" alt="Smiley face"  />';
                $img6 = '<img height="45" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
                $img6_1 = '<img height="50" src="'.DP_BASE_URL.'/modules/sales/images/sales.jpg" />';
//                $img6_1 = '<img height="58" src="'.DP_BASE_URL.'/modules/sales/images/biz_sales.jpg" />';
                $w="71.5%";
                if($template_pdf_1[0]['template_default'] == 1)
                {
                    if( $template_pdf_1[0]['template_server'] == 7)
                    {
                        $img = '<td width="25%" align="center"><img border="0" src="'. $base_url . $files[$i] .'" alt="Smiley face" height="120" /></td>';
                    }
                    else
                        $img = '<td width="30%" align="center"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" /></td>';
                }
        }
            

            // Set font
            $this->SetFont('helvetica', '', 9.6);
            if($template_pdf_1[0]['template_server'] == 1 && $template_pdf_1[0]['template_default'] == 1)
            {
                $txt_header = $CSalesManager->HeaderPDFMycool();
            }
            else if( $template_pdf_1[0]['template_server'] == 7)
                 {

                    $txt_header=  $this->HeaderApbiz();
                } 
            else if($template_pdf_1[0]['template_default'] == 1)
                {
                    $txt_header='<table border="0" cellspacing="0" cellpadding="0">
                            <tr valign = "top" height="60">
                                '.$img;
                     if(count($files) >0)
                                $txt_header.='<td width="'.$w.'"><table border="0"   width="100%">'.$supplier.'</table></td>';
                     else
                                $txt_header.='<td width="'.$w.'"><table border="0"   style="text-align:center" width="100%">'.$supplier.'</table></td>';
                     $txt_header.='</tr>
                            <tr>
                                <td colspan="2" align="right" style="font-size:1.2em;" width="100%"><b>Quotation No: '.$quotation_arr_rev[0]['quotation_revision'].'</b><hr></td>
                            </tr>
                       </table>';
                }
            elseif($template_pdf_2 == 1)
            {
                $txt_header='<table border="0" cellspacing="0" cellpadding="0">
                        <tr valign = "top" height="60">
                            '.$img.'
                            <td width="'.$w.'" align="right"><table border="0"  width="100%">'.$supplier.'</table></td>
                        </tr>
                   </table>';
            }
            elseif ($template_pdf_3==1) {
                $txt_header='<table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr valign = "top" height="60">
                            <td width="50%" rowspan="2" align="left"><b><span style="font-size:1.8em;">QUOTATION</span></b>
                                <br><br>'.$info2.'
                            </td>
                            <td width="50%" align="right"><table border="0"  width="100%">'.$supplier.'</table></td>
                        </tr>
                   </table>'; 
            }
            elseif ($template_pdf_4==1){
                $txt_header='<table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr valign = "top" height="60">
                            <td width="75%" '.$pdf_css.'><table border="0"  width="100%">'.$supplier.'</table></td>
                            <td width="25%" rowspan="2" align="left">'.$img.'
                            </td>
                        </tr>
                   </table>'; 
            }
            elseif ($template_pdf_5==1) {
                //$pdf->SetMargins(5,29, 8, -15);
                $txt_header='<table border="0" width="100%" cellspacing="0" cellpadding="5">
                        <tr><td colsapn="2" align="right">'.$img5.'&nbsp;&nbsp;&nbsp;</td></tr>
                        <tr valign = "top" height="60">
                            <td width="43%" rowspan="2" align="left">'.$info5.'</td>
                            <td width="55%" align="right"><table border="0"  width="100%">'.$supplier5.'</table></td>
                        </tr>
                   </table>'; 
            }
            elseif($template_pdf_6==1)
            {
                $txt_header='<table border="0" width="100%" style="margin:auto;" cellspacing="0" cellpadding="3">
                        <tr><td align="center" colspan="2" width="99%">'.$img6.'&nbsp;&nbsp;&nbsp;&nbsp;'.$img6_1.'</td></tr>
                        <tr valign = "top">
                            <td width="1%"></td>
                            <td width="99%" align="center">'.$supplier6.'</td>
                        </tr>
                   </table>'; 
            }

            $this->SetFont('helvetica', '', 9.6);
            $this->WriteHTML($txt_header); 
           
     }
     public function Footer(){
        $this->SetFont('helvetica', '',10);
        $quotation_id=$_REQUEST['quotation_id'];
        $quotation_revision_id = $_REQUEST['quotation_revision_id'];
        require_once(DP_BASE_DIR."/modules/sales/CQuotationManager.php");
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        $CQuotationManager = new CQuotationManager();
        $quotation_rev_details_arr = $CQuotationManager->get_latest_quotation_revision($quotation_id);
        $quo_user_id = $quotation_rev_details_arr[0]['user_id'];
        $user_name = $CQuotationManager->get_user_change($quo_user_id);
        
        require_once(DP_BASE_DIR."/modules/sales/CSalesManager.php");
            require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
        $CSalesManager= new CSalesManager();
            $CTemplatePDF = new CTemplatePDF();
//        $img6_footer = '<img height="300px" src="'.DP_BASE_URL.'/modules/sales/images/Logo_footnote_2015_Sep.jpg" />';
//        $img5 = '<img height="21" src="'.DP_BASE_URL.'/modules/sales/images/sales.jpg" alt="Smiley face"  />';
        $template_pdf_6 = $CSalesManager->get_template_pdf(6);
        $template_pdf_1_array = $CTemplatePDF->get_template_pdf(1);
        
        $this->SetY(-40);
        $page= $this->getPage();
        $display = "";
        if($page == 1)
        {
            $display = 'style="display:none;"';
        }
        $your = "";
        if($this->last_page_flag)
            $your = 'Yours Sincerely';
        $html= '<table border="0">
                <tr>
                    <td >
                        <table width="100%" border="0">
                            <tr>
                                <td  ><span >'.$your.'</span></td>
                                <td ></td>
                                <td width="43%" align="center">Confirmation of Order</td>
                            </tr>';
            if($template_pdf_1_array[0]['template_default']==1 && $template_pdf_1_array[0]['footer_text_invoice']!="" && $template_pdf_1_array[0]['template_server']==4)
            {    
                $html.='<tr>
                                <td></td>
                                <td ></td>
                                <td></td>
                </tr>';
                $html.=     '<tr ><td >&nbsp;&nbsp;&nbsp;Chris Goh <br><br>Computer generated document, no signature is required.</td>';
            }
            else
            {
                $html.='<tr>
                    <td></td>
                    <td height="35"></td>
                    <td></td>
                </tr>';
                    $html.=    '<tr ><td>Authorized Signature</td>';
            }
                    
                    $html.=    '<td></td>
                                <td align="center">Company Stamp &<br>Authorized Signature</td>
                            </tr>
                            <tr >
                                <td height="20"> </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td align="right" colspan="4">Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages().'</td>
                            </tr>
                            

                        </table>
                    </td>
                </tr>';

                $html .='</table>';
               
        if($template_pdf_6==1)
        {
             $this->SetY(-30); 
        $CTax = new CTax();
        $tax_arr = $CTax->list_tax();
        $tax_id = $quotation_rev_details_arr[0]['quotation_revision_tax'];
        if (count($tax_arr) > 0) {
            foreach ($tax_arr as $tax) {
                $tax_name = $tax['tax_name'];
                $tax_value =0;
                if ($tax_id) {
                    if ($tax['tax_id'] == $tax_id) {
                        $tax_value = $tax['tax_rate'];
                    }
                }
            }
        }
        
        
        $total_item_show = $CQuotationManager->get_quotation_item_total($quotation_id, $quotation_revision_id);;
        $percent=0;
        $caculating_tax=0;
        $AmountDue=0;
        $discount = 0;
        
        if(count($quotation_rev_details_arr)>0)
        {
            $discount = $quotation_rev_details_arr[0]['quotation_revision_discount'];
        }
        
        $total_item_show_last_discount = $total_item_show - $discount;
        $caculating_tax =0;
        if ($tax_id) {
            $caculating_tax = (floatval($total_item_show_last_discount) * floatval($tax_value) / 100);
            if($quotation_rev_details_arr[0]['quotation_revision_tax_edit']!=0)
            {
                $caculating_tax = $quotation_rev_details_arr[0]['quotation_revision_tax_edit'];
            }
        }
        $AmountDue=$total_item_show_last_discount + $caculating_tax;
        
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
                                <td align="right">GST&nbsp;'.$tax_value.'%:</td>
                                <td align="right">$'.number_format($caculating_tax,2).'</td>
                            </tr>
                            <tr>
                                <td align="right">Total Amount:</td>
                                <td align="right">$'.number_format($AmountDue,2).'</td>
                            </tr>
                    </table>
        ';
             
            // Hien thi box total
            $html="";
            if($this->last_page_flag)    
            {
                $this->SetY(-70); 
                $html.='<table width="99%" border="0" cellpadding="0" spacepadding="0">';
                    $html.='<tr>
                            <td colspan="'.$col.'" width="72%"><b>Terms & Conditions:</b><br>'.CSalesManager::htmlChars($quotation_rev_details_arr[0]['quotation_revision_term_condition_contents']).'</td>
                            <td colspan="3" width="28%">'.$tbl_total6.'</td>
                        </tr>';
                $html.='</table><br>';
            }
                     
//                $img6_footer = '<img  height="40" src="'.DP_BASE_URL.'/modules/sales/images/sales.jpg" />';
                               
                $html.= '<table border="0">
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr>
                                <td >
                                    <table width="100%" border="0">
                                        <tr >
                                            <td width="230"><hr>This is a computer-generated quotation. Signature is not required. <br><span style="font-weight:initial;">Prepared by: '.$user_name.'</span></td>
                                            <td width="50">&nbsp;</td>
                                            <td align="left" width="250"><hr>Client name/Signature/Company stamp<br>Date</td>
                                            <td width="185"></td>
                                        </tr>
                                        <tr>
                                            <td align="right" colspan="4">Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages().'</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>';
//               sthis->SetY(-90);
                $html .='</table>'; 
        }
        //Logo_footnote_2015 Sep

        $this->writeHTML($html);
     }
     
     public function HeaderApbiz() { 
        $supplier_arr = CSalesManager::get_supplier_info();
        require_once(DP_BASE_DIR."/modules/sales/CQuotationManager.php");
        $CQuotationManager = new CQuotationManager();
        require_once(DP_BASE_DIR."/modules/sales/CSalesManager.php");
        $CSalesManager = new CSalesManager();
        require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
        $CTemplatePDF = new CTemplatePDF();
        
         //Get Info Invoice number // 
        $quotation_arr =$CQuotationManager->get_db_quotation($_REQUEST['quotation_id']);
        $quotation_arr_rev = $CQuotationManager->get_db_quotation_revsion($_REQUEST['quotation_revision_id']);
        
        $customer_id = $quotation_arr[0]['customer_id'];
        $Customer_co_id = $quotation_arr[0]['quotation_CO'];
        $date = $quotation_arr[0]['quotation_date'];
        $quotation_no = $quotation_arr[0]['quotation_no'];
        $service_order = $quotation_arr[0]['service_order'];
        
        // Info Quotaiton
        $info2 = 'Quotation No&nbsp;&nbsp;: '. $quotation_arr_rev[0]['quotation_revision'] .'<br>';
        $info2 .= 'Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            : '. date('d-m-Y',  strtotime($date)).'<br>';
        //$info2 .= 'Revision Re: '. $quotation_rev.'<br/>';
        $info2 .= 'Servicer Oder : '. $service_order;
        
        // Info Quotaiton     
        $info5  = '<table border="0" width="100%">';
        $info5 .= '<tr style="font-weight:bold;"><td width="30%">Quotation No</td><td width="70%">: '.$quotation_no.'</td></tr>';
        $info5 .= '<tr ><td>Revision No</td><td>: '.substr($quotation_arr_rev[0]['quotation_revision'], -2).'</td></tr>';
        $info5 .= '<tr><td>Date</td><td>: '.date('d-m-Y',  strtotime($date)).'</td></tr>';
        $info5 .= '<tr><td>Servicer Oder</td><td>: '.$service_order.'</td></tr>';
        $info5 .= '</table>';
         //End Get Info Invoice number // 
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
            $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
            
        $files = list_files($url);
        // active default Template pdf
        $template_pdf_1 = $CTemplatePDF->get_template_pdf(1);
        $template_pdf_2 = $CSalesManager->get_template_pdf(2);
        $template_pdf_3 = $CSalesManager->get_template_pdf(3);
        $template_pdf_4 = $CSalesManager->get_template_pdf(4);
        $template_pdf_5 = $CSalesManager->get_template_pdf(5);
        $template_pdf_6 = $CSalesManager->get_template_pdf(6);
        if (count($supplier_arr) > 0) {
            $sales_owner_name = $supplier_arr['sales_owner_name'];
            if( $template_pdf_1[0]['template_server'] == 7)
            {
                $sales_owner_address =$supplier_arr['sales_owner_address1'];
                if($supplier_arr['sales_owner_address2'] !="")
                    $sales_owner_address.=','.$supplier_arr['sales_owner_address2'].'';
                if($supplier_arr['sales_owner_country'] !="")
                    $sales_owner_address.=', '.$supplier_arr['sales_owner_country'].' ';
                $sales_owner_address.=$supplier_arr['sales_owner_postal_code']!=""?$supplier_arr['sales_owner_postal_code']:"";
            }
            else
                $sales_owner_address =$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'];
            //$sales_owner_address += 
            $phone =$supplier_arr['sales_owner_phone1'];
            $fax = $supplier_arr['sales_owner_fax'];
            $email =$supplier_arr['sales_owner_email'];
            $web = $supplier_arr['sales_owner_website'];
            $sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
            $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
        }
        
        
        // end active default Template pdf

        $supplier = '<tr><td style="font-size:1.4em;"><b>'. $sales_owner_name .'</b></td></tr>';
        
        $supplier.= '<tr><td>'.$sales_owner_address.'</td></tr>';
        if($fax!="")
        {
            $fax5 = ', Fax: '.$fax;
            $fax6 = 'Fax: '.$fax;
            $fax_mycool = '.&nbsp;&nbsp;&nbsp;&nbsp;Fax: '.$fax;
        }
        
        if( $template_pdf_1[0]['template_server'] == 7)
        {
            
            if($sales_owner_gst_reg_no)
                $supplier .= '<tr><td>CO. Reg. No: '. $sales_owner_gst_reg_no .'</td></tr>';
            
            if($sales_owner_reg_no!="")
                $supplier .= '<tr><td>Reg No: '. $sales_owner_reg_no .'</td></tr>';
            $supplier .= '<tr><td>Tel: '.$phone.$fax_mycool.'</td></tr>';
            $supplier .= '<tr><td>Email: '.$email.'</td></tr>';
        }
        
        $supplier5 = '<tr><td>'.$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].'</td></tr>';
        $supplier5 .= '<tr><td>Tel: '.$phone.$fax5.'</td></tr>';
        if($sales_owner_reg_no!="")
            $supplier5 .= '<tr><td>Reg No: '. $sales_owner_reg_no .'</td></tr>';
        $supplier5 .= '<tr><td>Email: '.$email.'</td></tr>';
        
        if($email!="")
        {
            $email6="Email: ".$email;
        }
        if($web!="")
            $web6 = 'Website: '.$web.'&nbsp;&nbsp;';
        
        if($web!="")
            $supplier .= '<tr><td>Website: '.$web.'</td></tr>';
        if($sales_owner_gst_reg_no!="")
        {
           $sales_owner_gst_reg_no6='GST Reg. No: '. $sales_owner_gst_reg_no.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        if($sales_owner_reg_no!="")
        {
            $sales_owner_reg_no6 = 'CO. Reg. No: '. $sales_owner_gst_reg_no.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        
        $supplier6 = "";
        $supplier6.=$supplier_arr['sales_owner_address1'] .' '.$supplier_arr['sales_owner_address2'].' '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].
                            '&nbsp;&nbsp;&nbsp;&nbsp;Tel: '.$phone.'&nbsp;&nbsp;'.$fax6.'&nbsp;&nbsp;'.$email6.'<br>'.$sales_owner_reg_no6.$sales_owner_gst_reg_no6.$web6;

            
        $w="100%";
        if(count($files)>0 && $files[$i]!=".svn")
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
                $img = '<td width="30%" rowspan="2" align="center"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="100" /></td>';
                $img5 = '<img height="21" src="'. $base_url . $files[$i] .'" alt="Smiley face"  />';
                $img6 = '<img height="45" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
                $img6_1 = '<img height="50" src="'.DP_BASE_URL.'/modules/sales/images/sales.jpg" />';
//                $img6_1 = '<img height="58" src="'.DP_BASE_URL.'/modules/sales/images/biz_sales.jpg" />';
                $w="71.5%";
               
                $img = '<td width="25%" align="center"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="100" /></td>';
                    
        }
            

            // Set font
        $this->SetFont('helvetica', '', 9.6);
      if( $template_pdf_1[0]['template_server'] == 7)
      {
         $txt_header='<table border="0" cellspacing="0" cellpadding="0">
                <tr valign = "top" height="60">
                    '.$img;
         if(count($files) >0)
                    $txt_header.='<td width="'.$w.'"><table border="0"   width="100%"><br/><br/>'.$supplier.'</table></td>';
         else
                    $txt_header.='<td width="'.$w.'"><table border="0"   style="text-align:center" width="100%"><br/>'.$supplier.'</table></td>';
         $txt_header.='</tr>
                <tr>
                    <td colspan="2" align="right" style="font-size:1.2em;" width="100%"><b>Quotation No: '.$quotation_arr_rev[0]['quotation_revision'].'</b><hr></td>
                </tr>
           </table>';
       } 
       
       return $txt_header;
           
     }
}
?>
