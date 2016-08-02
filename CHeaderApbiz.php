<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
class CHeaderApbiz extends TCPDF{ 
    
    public $last_page_flag = false;
    
    public function Close() {
        $this->last_page_flag = true;
        parent::Close();
    }
    
    public function HeaderApbiz($credit_note_no=false) { 
        $supplier_arr = CSalesManager::get_supplier_info();
        require_once(DP_BASE_DIR."/modules/sales/CQuotationManager.php");
        require_once(DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
        $CQuotationManager = new CQuotationManager();
        require_once(DP_BASE_DIR."/modules/sales/CSalesManager.php");
        $CSalesManager = new CSalesManager();
        require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
        $CTemplatePDF = new CTemplatePDF();
        $CInvoiceManager=new CInvoiceManager();
        
        //credit note
        if(isset($_REQUEST['creditNote_id']))
        {
        $CCreditNoteManager = new CCreditNoteManager();
        $creditNote_arr = $CCreditNoteManager->list_db_creditNote($_REQUEST['creditNote_id']);
        }
        
        
         //Get Info Invoice number // 
        if(isset($_REQUEST['quotation_revision_id']))
        {
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
        }
        if($_REQUEST['invoice_id'])
        {
        $invoice_details_arr = $CInvoiceManager->get_db_invoice($_REQUEST['invoice_id']);
        
        $invocie_id = $invoice_details_arr[0]['invoice_id'];
        $date = $invoice_details_arr[0]['invoice_date'];
        $invoice_no = $invoice_details_arr[0]['invoice_no'];
        }
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
         $txt_header='<table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr valign = "top" height="60" width="100%">
                    '.$img;
         if(count($files) >0)
                    $txt_header.='<td width="60%"><table border="0"   width="100%"><br/><br/>'.$supplier.'</table></td>';
         else
                    $txt_header.='<td width="'.$w.'"><table border="0"   style="text-align:left" width="100%"><br/>'.$supplier.'</table></td>';
         if(isset($_REQUEST['quotation_revision_id']))
         {
         $txt_header.='</tr>
                <tr>
                    <td colspan="2" align="right" style="font-size:1.2em;" width="100%"><b>Quotation No: '.$quotation_arr_rev[0]['quotation_revision'].'</b><hr></td>
                </tr>
           </table>';
         }
         else if($invoice_no !="")
         {
             $txt_header.='</tr>
                    <tr>
                        <td colspan="2" align="right" style="font-size:1.2em;" width="100%"><b>Invoice No: '.$invoice_no.'</b><hr></td>
                    </tr>
               </table>';
         }
         else if(count($creditNote_arr)>0 && $credit_note_no=="")
         {
             $txt_header.='</tr>
                    <tr>
                        <td colspan="2" align="right" style="font-size:1.2em;" width="100%"><b>Voucher No: '.$creditNote_arr[0]['credit_note_no'].'</b><hr></td>
                    </tr>
               </table>';
            
         }
         else if(isset($credit_note_no) && $credit_note_no!="")
         {
             $txt_header.='</tr>
                    <tr>
                        <td colspan="2" align="right" style="font-size:1.2em;" width="100%"><b>Credit Note No: '.$credit_note_no.'</b><hr></td>
                    </tr>
               </table>';
         }
         else
         {
             $txt_header.='</tr>
                    <tr>
                        <td colspan="2" align="right" style="font-size:1.2em;" width="100%"><b></b><hr></td>
                    </tr>
               </table>';
         }
         
       } 
       
       return $txt_header;
           
    
           
     }
    
}
?>
