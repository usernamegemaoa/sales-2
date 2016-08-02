<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
class MYPDFCreditNote extends TCPDF{   
    //Page header
    public $page_count = 1;
    public function Header() {        
        $supplier_arr = CSalesManager::get_supplier_info();
        require_once(DP_BASE_DIR."/modules/sales/CCreditNoteManager.php");
        require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
        require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
        $CTemplatePDF = new CTemplatePDF();
        
        $CSalesManager = new CSalesManager();
        $CCreditNoteManager = new CCreditNoteManager();
        $creditNote_arr = $CCreditNoteManager->list_db_creditNote($_REQUEST['creditNote_id']);
        
         // Template cho server http://hlaircon.theairconhub.com/
         $template_pdf_1 = $CTemplatePDF->get_template_pdf(1);
         $template_pdf_5 = $CSalesManager->get_template_pdf(5);
         $template_pdf_6 = $CSalesManager->get_template_pdf(6);
        
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
        $w="100%";
        if(count($files)>0)
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
                $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" /></td>';
                $img5 = '<img height="22.5" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
                $img6 = '<img width="250" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
                $w="73%";
        }
            
//            $files = scandir($url);
//            $i = count($files) -1;
//            $w="100%";
//            if (count($files) > 2 && $files[$i]!=".svn") {
//                $path_file = $url . $files[$i];
//                $img = '<td width="27%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" /></td>';
//                $img5 = '<img height="22.5" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
//                $img6 = '<img width="250" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
//                $w="73%";
//            }
            
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
            
            // Set font
            $this->SetFont('helvetica', '', 10);
            // Title
            if($template_pdf_1[0]['template_server'] == 1)
            {
                $txt = $CSalesManager->HeaderPDFMycool();
                $txt.='<br><hr>';
            }
            else if($template_pdf_1[0]['template_server'] == 3)
            {
                $txt = $CSalesManager->HeaderPDFVeltech();
                $txt.='<br><hr>';
            }
            else if($template_pdf_6==1)
            {
                $txt= '<table border="0" cellpadding="4" style="text-align:center;">
                            <tr width="100%"><td>'.$img5.'</td></tr>
                            <tr>
                                <td width="100%">'.$supplier6.'<br></td>
                            </tr>
                    </table>';
            }
            else if($template_pdf_5==1)
            {
                $txt= '<table border="0" cellpadding="4">
                            <tr align="right" width="100%"><td>'.$img5.'</td></tr>
                            <tr>
                                <td width="100%" align="right">'.$supplier5.'<br></td>
                            </tr>
                    </table>';
            }
            else{
                $txt='<table border="0" cellspacing="0" cellpadding="0">
                        <tr valign = "top" height="60">
                            '.$img.'
                            <td width="'.$w.'" ><table border="0"  width="100%">'.$supplier.'</table></td>
                        </tr>
                        <tr valign="bottom"><td align="right" colspan="2" style="font-size:1.2em;"><b>Voucher No: '.$creditNote_arr[0]['credit_note_no'].'</b></td></tr>
                        <tr height="2"><td colspan="2" height="2"><hr></td></tr>
                   </table>';
            }
            if($template_pdf_1[0]['template_server'] == 7)
            {
                require_once (DP_BASE_DIR."/modules/sales/CHeaderApbiz.php");
                $header_Apbiz=new CHeaderApbiz();
                $txt=$header_Apbiz->HeaderApbiz();
            }
            
            //$this->Cell(0, 0, $txt , 1, false, 'C', 0, '', 0, false, 'M', 'M');
            $this->WriteHTML($txt);
    }
    
}
