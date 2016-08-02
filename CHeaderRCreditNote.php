<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CReportManager.php");

$CSalesManager = new CSalesManager();
$CReportManager = new CReportManager();

require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
class MYPDFRCredit extends TCPDF{
    
    public function Header() {
        global $AppUI, $CReportManager,$CSalesManager,$ocio_config;
        $this->SetFont('helvetica', '', 9);
        $this->SetMargins(20, 65, 15, '');
        if(isset($_GET['from_date']))
            $from_date = $_GET['from_date'];
        if(isset($_GET['to_date']))
            $to_date = $_GET['to_date'];
        
         // Template cho server http://hlaircon.theairconhub.com/
         $template_pdf_5 = $CSalesManager->get_template_pdf(5);
         $template_pdf_6 = $CSalesManager->get_template_pdf(6);
         $template_pdf_7 = $CSalesManager->get_template_server(1);

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
             $phone = 'Tel: '.$phone;
         if($fax!="")
             $fax = ' Fax: '.$fax;
         if($email!="")
             $email=' Email: '.$email;

         $supplier = $sales_owner_name .'<br>';
         $supplier.= $sales_owner_address.'<br>';
         $supplier .= $sales_owner_code.$phone.$fax.'<br>';
         if($sales_owner_gst_reg_no!="")
            $supplier .= 'GST Reg No: '. $sales_owner_gst_reg_no;

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
                 $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="70" width="120" />';
                 $img5 = '<img height="22.5" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
                 $img6 = '<img width="250" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
        }
             
//             $files = scandir($url);
//             $i = count($files) -1;
//             if (count($files) > 2 && $files[$i]!=".svn") {
//                 $path_file = $url . $files[$i];
//                 $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="70" width="120" />';
//                 $img5 = '<img height="22.5" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
//                 $img6 = '<img width="250" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
//             }

             $txt = '<div width="100%" style="text-align:right">Date: '.date($ocio_config['php_abms_date_format'],  strtotime(date('d-m-Y'))).'</div>';
     //End supplier
             if($template_pdf_5==1)
             {
                 $txt.= '<table border="0" cellpadding="4">
                         <tr>
                             <td>
                                 <table border="0" width="102%">
                                     <tr><td colsapn="2" align="right" width="100%">'.$img5.'</td></tr>
                                     <tr>
                                         <td width="30%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <span style="font-size:2.2em;">Credit Note</span><br><br>
                                                From: '.date($ocio_config['php_abms_date_format'],  strtotime($from_date)).'&nbsp;&nbsp;&nbsp;&nbsp;
                                                To: '.date($ocio_config['php_abms_date_format'],  strtotime($to_date)).'
                                         </td>
                                         <td width="69%" align="right">'.$supplier5.'<br></td>
                                     </tr>
                                 </table>
                             </td>
                         </tr>
                     </table>';
                 
             }
             else if($template_pdf_6==1)
             {
                 $txt.= '<table border="0" cellpadding="4">
                         <tr>
                             <td>
                                 <table border="0" width="99%">
                                     <tr><td align="left" width="99%">'.$img6.'</td></tr>
                                     <tr>
                                         <td width="99%" align="left">'.$supplier6.'</td>
                                     </tr>
                                 </table>
                             </td>
                         </tr>
                         <tr>
                            <td colspan="2" align="center">
                                <span style="font-size:2.2em;">Credit Note</span><br>
                                From: '.date($ocio_config['php_abms_date_format'],  strtotime($from_date)).'&nbsp;&nbsp;&nbsp;&nbsp;
                                To: '.date($ocio_config['php_abms_date_format'],  strtotime($to_date)).'
                            </td>
                         </tr>
                     </table>';
//                 $this->setMargins('', 60);
//                 $this->SetMargins(20, 65, 15, 5);
             }
             else 
             {
                 if($template_pdf_7==7)
                 {
                    $supplier = '<tr><td><p style="font-size:1.5em;"><b>'. $sales_owner_name .'</b></p></td></tr>';
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
                     $txt_header='<table border="0" cellspacing="0" cellpadding="0">
                        <tr valign = "top" height="60">
                            '.$img;
                        if(count($files) >0)
                                   $txt_header.='<td ><table border="0"   width="100%">'.$supplier.'</table></td>';
                        else
                                   $txt_header.='<td ><table border="0"   style="text-align:center" width="100%">'.$supplier.'</table></td>';
                        $txt_header.='</tr>
                               <tr>
                                   <td colspan="2" align="right" style="font-size:1.2em;" width="100%"><hr></td>
                               </tr>
                          </table>';
                     $txt.= '<table border="0" cellpadding="4" width="100%">
                         <tr>
                             <td >
                                 '.$txt_header.'
                             </td>
                         </tr>
                         <tr>
                            <td colspan="2" align="center">
                                <span style="font-size:2.2em;">Credit Note</span><br>
                                From: '.date($ocio_config['php_abms_date_format'],  strtotime($from_date)).'&nbsp;&nbsp;&nbsp;&nbsp;
                                To: '.date($ocio_config['php_abms_date_format'],  strtotime($to_date)).'
                            </td>
                         </tr>
                     </table>';
                }
                
                 else
                 {
                 $txt.= '<table border="0" cellpadding="4" width="100%">
                         <tr>
                             <td >
                                 <table border="0">
                                     <tr>
                                         <td width="130">'.$img.'</td>
                                         <td width="380">'.$supplier.'</td>
                                     </tr>
                                 </table>
                             </td>
                         </tr>
                         <tr>
                            <td colspan="2" align="center">
                                <span style="font-size:2.2em;">Credit Note</span><br>
                                From: '.date($ocio_config['php_abms_date_format'],  strtotime($from_date)).'&nbsp;&nbsp;&nbsp;&nbsp;
                                To: '.date($ocio_config['php_abms_date_format'],  strtotime($to_date)).'
                            </td>
                         </tr>
                     </table>';
             }
             }
             $this->SetFont('helvetica', '', 9);
             $this->WriteHTML($txt);
    }
    
    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', '', 9);
        $txt='<table border="0" width="102%" cellspacing="0">
            <tr><td height="35">&nbsp;</td></tr>
            <tr>
                <td align="right">Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages().'</td>
            </tr>
         </table><br><br>';
        
        $this->WriteHTML($txt);
        //$this->Cell(0, 20,'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false,'T','M');
    }
}
