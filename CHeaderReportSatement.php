<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CReportManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");

$CSalesManager = new CSalesManager();
$CReportManager = new CReportManager();
$CInvoiceManager = new CInvoiceManager();
$sales_owner_name;
$sales_owner_address;
$sales_owner_postal;

require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
class MYPDFStatement extends TCPDF{

    public function Header() {
    global $ocio_config, $AppUI, $CReportManager,$CSalesManager,$CInvoiceManager,$sales_owner_name,$sales_owner_address, $sales_owner_postal;

    require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
    $CTemplatePDF = new CTemplatePDF();
    // Template cho server http://hlaircon.theairconhub.com/
    $tempalte_pdf_1 = $CTemplatePDF->get_template_pdf(1);
    $template_pdf_5 = $CSalesManager->get_template_pdf(5);
    $template_pdf_6 = $CSalesManager->get_template_pdf(6);
   
    
    $customer_id = $_GET['customer_id'];
    $customer_arr = $CSalesManager->get_list_companies($customer_id);
    $customer_name = $customer_arr['company_name'];
    // Thong tin Supplier
    $supplier_arr = $CSalesManager->get_supplier_info();
    if (count($supplier_arr) > 0) {
        $sales_owner_name = $supplier_arr['sales_owner_name'];
        $sales_owner_address =$supplier_arr['sales_owner_address1'] .' '.$supplier_arr['sales_owner_address2'];
        //$sales_owner_address += 
        $sales_owner_code = $supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'];
        $phone =$supplier_arr['sales_owner_phone1'];
        $fax = $supplier_arr['sales_owner_fax'];
        $email =$supplier_arr['sales_owner_email'];
        $web = $supplier_arr['sales_owner_website'];
        $sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
        $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
        $sales_owner_postal = $supplier_arr['sales_owner_country']." ".$supplier_arr['sales_owner_postal_code'];
    }
    if($phone!="")
        $phone = ' Tel: '.$phone;
    if($fax!="")
    {
        $fax_mycoll = ' / '.$fax;
        $fax = ' Fax: '.$fax;
    }
    if($email!="" && $tempalte_pdf_1[0]['template_server'] != 1)
        $email=' Email: '.$email;
    
    
    $supplier = $sales_owner_name .'<br>';
    $supplier.= $sales_owner_address.'<br>';
    if($tempalte_pdf_1[0]['template_default']==1 && $tempalte_pdf_1[0]['template_server'] == 1)
    {
        $supplier .= $sales_owner_code.$phone.$fax_mycoll.'<br>';
        $supplier .= 'CO. Reg No: '. $sales_owner_gst_reg_no ;
        $supplier .= '<br>Email Address: '.$email;
        $supplier .= '<br>Website: '.$web;
    }
    else
    {
        $supplier .= $sales_owner_code.$phone.$fax.'<br>';
        $supplier .= 'GST Reg No: '. $sales_owner_gst_reg_no;
    }
    
    $supplier5= '<tr><td>'.$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].'</td></tr>';
    $supplier5.= '<tr><td>Tel: '.$phone.$fax5.'</td></tr>';
    if($sales_owner_gst_reg_no!="")
        $supplier5 .= '<tr><td>GST Reg No: '. $sales_owner_gst_reg_no .'</td></tr>';
    if($email!="")
        $supplier5 .= '<tr><td>'.$email.'</td></tr>';
    if($web!="")
        $supplier5 .= '<tr><td>Website: '.$web.'</td></tr>';
    
    // server innoflex
    $supplier6= $supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].' '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].'<br>';
    $supplier6.= $phone.'&nbsp;&nbsp;&nbsp;&nbsp;'.$fax.'&nbsp;&nbsp;&nbsp;&nbsp;'.$email.'<br>';
    $supplier6 .= 'GST Reg No: '. $sales_owner_gst_reg_no .'&nbsp;&nbsp;&nbsp;&nbsp;Website: '.$web;
    
    // Logo
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        
        $files = list_files($url);
        if(count($files)>0)
        {
            $i = count($files)-1;
            $path_file = $url . $files[$i];
            if($tempalte_pdf_1[0]['template_server']==1)
            {
                $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="100" />'; 
            }
            else
            {
                $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" />';
            }
            $img5 = '<img height="22.5" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
            $img6 = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
        }
        
//        $files = scandir($url);
//        $i = count($files) -1;
//        if (count($files) > 2 && $files[$i]!=".svn") {
//            $path_file = $url . $files[$i];
//            if($tempalte_pdf_1[0]['template_server']==1)
//            {
//                $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="100" />'; 
//            }
//            else
//            {
//                $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" />';
//            }
//            $img5 = '<img height="22.5" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
//            $img6 = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
//        }
        
        
//End supplier
        
   // GET Info Customer
        $address_id = false;
        if($_GET['address_id']!=""){
            $address_id = $_GET['address_id'];
            $address_arr = $CSalesManager->get_list_address(false,$address_id);
        }else{
            //$address_arr = $CSalesManager->get_address_by_id($_GET['text_address']);
            $address_arr = $CSalesManager->get_list_address($customer_id,$_GET['text_address']);
        }
        $attention_arr = $CSalesManager->get_list_attention($customer_id);
        $address_customer=$customer_name.'.';
        
        if($_GET['co_stament_id']!=""){
            $co_stament_arr = $CSalesManager->get_list_companies($_GET['co_stament_id']);
            $address_customer.='<br>C/O: '.$co_stament_arr['company_name'].'.';
        }
                
        foreach ($address_arr as $address_row1) {

            $address_id = $address_row1['address_id'];
            $selected="";$address_2="";$brand="";$address_type="";
            if($address_row1['address_type']==2)
                $address_type = "[Billing] ";
            else if($address_row1['address_type']==1)
                $address_type = "[Job Site] ";
            else 
                $address_type = "[NA] ";
            $brand="";
            if($address_row1['address_branch_name']!="")
                $brand = $address_row1['address_branch_name']." - ";
            $address_2 = "";
            if($address_row1['address_street_address_2']!="")
                $address_2 = " ".$address_row1['address_street_address_2'];
            $opt_address1=$brand.$address_row1['address_street_address_1'].$address_2.' '.'Singapore '.$address_row1['address_postal_zip_code'];
        }
        if($_GET['text_address']!="null" || $_GET['address_id']!="")
            $address_customer.="<br>Address: ".$opt_address1;
        
        if($_GET['attention_state']!="")
                $address_customer.="<br>Attn: ".$_GET['attention_state'];
        $this->SetFont('helvetica', '', 10.3);
        if($template_pdf_5==1)
        {
            $txt = '<table border="0" cellpadding="4">
                    <tr>
                        <td>
                            <table border="0" width="99%">
                                <tr><td colsapn="2" align="right" width="99%">'.$img5.'</td></tr>
                                <tr>
                                    <td width="30%"><span style="font-size:2.2em;">Statement</span><br><br>Closing Date: '.  date($ocio_config['php_abms_date_format'],strtotime(date('d.M.Y'))).'</td>
                                    <td width="69%" align="right">'.$supplier5.'<br></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td width="310"><b>Bill To:'.$address_id12.'</b></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #333; padding:10px;" height="80" width="664" >'.$address_customer.'</td>
                    </tr>
                </table>';
        }
        else if($template_pdf_6==1)
        {
            $this->SetFont('helvetica', '', 10);
            $txt = '<table border="0" cellpadding="4">
                    <tr>
                        <td>
                            <table border="0" width="99%">
                                <tr><td align="center" width="99%">'.$img6.'</td></tr>
                                <tr>
                                    <td width="99%" align="center">'.$supplier6.'<br></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td width="460"><b>Bill To:'.$address_id12.'</b><br>'.$address_customer.'</td>
                        <td><span style="font-size:2.2em;">Statement</span><br><br>Closing Date: '.  date($ocio_config['php_abms_date_format'],strtotime(date('d.M.Y'))).'</td>
                    </tr>
                </table>';
        }
        else
        {
            $txt = '<table border="0" cellpadding="4">
                    <tr>
                        <td width="500" >
                            <table border="0">
                                <tr>
                                    <td width="120">'.$img.'</td>
                                    <td width="380">'.$supplier.'</td>
                                </tr>
                            </table>
                        </td>
                        <td width="170"><span style="font-size:2.2em;">Statement</span><br><br>Closing Date: '.  date($ocio_config['php_abms_date_format'],strtotime(date('d.M.Y'))).'</td>
                    </tr>
                    <tr><td colspan="2" height="30"></td></tr>
                    <tr>
                        <td width="310"><b>Bill To:'.$address_id12.'</b></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #333; padding:10px;" height="90" width="664" >'.$address_customer.'</td>
                    </tr>
                </table>';
        }

 
        $this->WriteHTML($txt);
    }
    
    public function Footer() {
        global $AppUI, $CInvoiceManager,$sales_owner_name,$sales_owner_address,$sales_owner_postal;
        $this->SetY(-55);
        
        $load_inv = false; $date_from=false; $date_to=false;
        if($_GET['load_inv']!="")
            $load_inv = $_GET['load_inv'];
        if($_GET['sta_date_from']!="")
            $date_from = $_GET['sta_date_from'];
        if($_GET['sta_date_from'] !="")
            $date_to = $_GET['sta_date_to'];
        
        $customer_id = $_GET['customer_id'];
        $amount30=0;$amount60=0;$amount90=0;$day=0;
        $address_id = false;
        if($_GET['address_id']!="")
            $address_id = $_GET['address_id'];
        $invoice_arr =  $CInvoiceManager->list_invoice($customer_id,$load_inv,true,$date_from,$date_to, $address_id);
        foreach ($invoice_arr as $invoice_row) {
            $invoice_date = date('d/m/Y', strtotime($invoice_row['invoice_date']));
            $invoice_id = $invoice_row['invoice_id'];
            $invoice_revision_arr = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
            $invoice_revision_id = $invoice_revision_arr[0]['invoice_revision_id'];
            $total_show = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
            // Amount Invoice
            $amount = $CInvoiceManager->get_total_item_invoice_and_tax($invoice_id,$invoice_revision_id);
            // Payment Invoice
            $payment = $CInvoiceManager->get_total_amount_paid1($invoice_revision_id);
            // Due Invocie
            $due = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show);

            // Total
            $total_amount +=$amount;
            $total_payment += $payment;
            $total_due += $due;
            // Agign customer

            $start =  strtotime($invoice_row['invoice_date']);
            $end = strtotime(date("Y/m/d"));
            $day_diff=  round(($end - $start)/86400);
            if($day_diff>=0 && $day_diff<=30){
                $day30=$due;
                $day60=0;$day90=0;$day=0;
                $amount30 += $day30;
            }
            else if($day_diff>30 && $day_diff<=60)
            {
                $day60=$due;
                $day30=0;$day90=0;$day=0;
                $amount60 +=$day60;
            }
            else if($day_diff>60 && $day_diff<=90)
            {
                $day90=$due;
                $day30=0;$day60=0;$day=0;
                $amount90 +=$day90;
            }
            else if( $day_diff>90)
            {
                $day=$due;
                $day30=0;$day60=0;$day90=0;
                $amountDay +=$day;
            }
        }
        $this->SetFont('helvetica','',10.3);
        //$txt = 'The above statement reflect your outstandings to date. We would appreciate it if you could kindly made a cheque payable to "'.$sales_owner_name.'" & mail to "'.$sales_owner_address.'"';
        $txt='<table border="0" width="99%" cellspacing="0">
                    <tr>
                        <td>The above statement reflect your outstandings to date. We would appreciate it if you could kindly made a cheque payable to "'.$sales_owner_name.'" & mail to "'.$sales_owner_address.', '.$sales_owner_postal.'."The above statement reflect your outstandings to date.</td>
                    </tr>
                 </table><br><br>';
        $txt.='<table border="1" width="99%" cellspacing="0" cellpadding="4">
                                   <tr align="center" style="font-weight:bold;">
                                       <td>0 - 30 days</td>
                                       <td>31 - 60 days</td>
                                       <td>61 - 90 days</td>
                                       <td>> 90 days</td>
                                       <td>Total</td>
                                   </tr>
                                   <tr align="center">
                                       <td>$'.number_format($amount30,2).'</td>
                                       <td>$'.number_format($amount60,2).'</td>
                                       <td>$'.number_format($amount90,2).'</td>
                                       <td>$'.number_format($amountDay,2).'</td>
                                       <td>$'.number_format($total_due,2).'</td>
                                   </tr>
                               </table>';
        $txt.='<table border="0" width="97%" cellspacing="0">
            <tr><td height="35">&nbsp;</td></tr>
            <tr>
                <td align="right">Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages().'</td>
            </tr>
         </table><br><br>';
        
        $this->WriteHTML($txt);
        //$this->Cell(0, 20,'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false,'T','M');
    }
}
