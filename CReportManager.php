<?php

if(!defined('DP_BASE_DIR')){
    die('You should not access this file directly.');
}
require_once 'CInvoice.php';
require_once (DP_BASE_DIR."/modules/po/vw_po.php");
require_once (DP_BASE_DIR."/modules/sales/CTax.php");
require_once DP_BASE_DIR.'/modules/po/po_invoice.php';
//require_once (DP_BASE_DIR."/modules/sales/css/report.css.php");
require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoice.php");
require_once (DP_BASE_DIR."/modules/sales/CReportManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
require_once (DP_BASE_DIR."/modules/sales/CQuotationManager.php");
require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");
require_once(DP_BASE_DIR."/modules/clients/company_manager.class.php");
require_once (DP_BASE_DIR."/modules/banks/CBankAccount.php");
require_once DP_BASE_DIR.'/modules/expenses/ExpensesCategory.php';

require_once (DP_BASE_DIR."/modules/sales/CCreditNoteManager.php");

require_once DP_BASE_DIR.'/modules/expenses/expensesManage.php';
require_once DP_BASE_DIR.'/modules/expenses/ExpensesSupplierManage.php';
require_once DP_BASE_DIR.'/modules/expenses/Expenses_tax_manage.php';
require_once DP_BASE_DIR.'/modules/po/CPOPayment.php';
$ExpensesCategory = new ExpensesCategory();
$CCreditManager = new CCreditNoteManager();

$CTax = new CTax();
$CPOManager = new CPOManager();
$po_invoice = new po_invoice();
$CPaymentManager = new CPaymentManager();
// Get roles user hien tai
$perms =& $AppUI->acl();
$user_id = $AppUI->user_id;
$user_roles_arr = $perms->getUserRoles($user_id);
if (count($user_roles_arr) >0) {
    $user_roles = array();
    foreach ($user_roles_arr as $user_roles_id) {
        $user_roles[] = $user_roles_id['value'];
    }
}

class CReportManager{
    
    function getCompanyByInvoice($customer_id=false){
            $q = new DBQuery();
            $q->addTable('clients', 'tbl1');
            $q->addQuery('tbl1.company_id, tbl1.company_name');
            $q->addJoin('sales_invoice', 'tbl3', "tbl1.company_id = tbl3.customer_id",'inner');
            $q->addWhere('tbl1.company_id = tbl3.customer_id');
            if($customer_id){
                $q->addWhere('tbl1.company_id IN ('. $customer_id. ')');
            }
            $q->addGroup('tbl1.company_id');
            $q->addOrder('tbl1.company_name ASC');
            return $q->loadList();
        
    }
    
    function list_agingreport($company_id=false,$date_from=false,$date_to=false,$date_from1=false,$date_to1=false,$address_id=false,$branch=false){
        $q = new DBQuery;
        $q->addTable('sales_invoice','tbl1');
        $q->addQuery('tbl1.invoice_id,tbl1.invoice_date,tbl1.invoice_no,tbl1.job_location_id,tbl2.address_branch_name,tbl2.address_street_address_2,tbl2.address_postal_zip_code,tbl2.address_street_address_1,tbl2.address_phone_1,tbl2.address_phone_2');
        $q->addJoin('addresses', 'tbl2', 'tbl2.address_id=tbl1.job_location_id');
        if($company_id)
            $q->addWhere('customer_id ='.$company_id);
        //$q->addWhere('tbl1.invoice_status <> 2');
        if($date_from && $date_to){
            $q->addWhere('tbl1.invoice_date >= "'.$date_from.'"');
            $q->addWhere('tbl1.invoice_date <= "'.$date_to.'"');
        }else if($date_to){
            $q->addWhere('tbl1.invoice_date <"'.$date_to.'"');
        }
//        if($date_from1){
//            $q->addWhere('tbl1.invoice_date >= "'.$date_from1.'"');
//        }
        if($date_to1){
            $q->addWhere('tbl1.invoice_date <= "'.$date_to1.'"');
        }
        
        if($branch && $address_id!=-1){
            if($address_id){
                $q->addWhere('tbl1.address_id='.$address_id);
            }else
            {
                $q->addWhere('tbl1.address_id = 0');
                
            }
        
        }
        else
        {
         
            $q->addOrder('address_street_address_1');
        }
        
        if($address_id!=-1)
            $q->addOrder('invoice_no');
        return $q->loadList();
    }
    function getInvoiceByInvoiceRev($invoice_revesion_id){
        $q = new DBQuery();
        $q->addTable('sales_invoice','tbl1');
        $q->addQuery('tbl1.*,tbl2.*,tbl3.*');
        $q->addJoin('sales_invoice_revision', 'tbl2', 'tbl1.invoice_id=tbl2.invoice_id');
        $q->addJoin('sales_payment_detail', 'tbl3', 'tbl2.invoice_revision_id=tbl3.invoice_revision_id');
        $q->addWhere('tbl3.invoice_revision_id='.$invoice_revesion_id);
        return $q->loadList();
    }
    function getCustomerByInvoiceRev($invoice_revesion_id,$customer_id_arr=false){
        $q = new DBQuery();
        $q->addTable('clients','tbl1');
        $q->addQuery('tbl1.*,tbl2.*,tbl3.*');
        $q->addJoin('sales_invoice', 'tbl2', 'tbl2.customer_id=tbl1.company_id');
        $q->addJoin('sales_invoice_revision', 'tbl3', 'tbl3.invoice_id=tbl2.invoice_id');
        $q->addJoin('sales_payment_detail', 'tbl4', 'tbl4.invoice_revision_id=tbl3.invoice_revision_id');
        $q->addWhere('tbl3.invoice_revision_id='.$invoice_revesion_id);
        if($customer_id_arr){
            $q->addWhere('tbl1.company_id IN ('.$customer_id_arr.')');
        }
        return $q->loadList();
    }
    function get_payment_amount_total($payment_id){
        $q = new DBQuery();
        $q->addTable('sales_payment_detail');
        $q->addQuery('SUM(payment_amount)');
        $q->addWhere('payment_id='.$payment_id);
        $rows = $q->loadList();
        return $rows[0]['SUM(payment_amount)'];
    }
    function getPaymentDetailByPayment($payment_id){
        $q = new DBQuery();
        $q->addTable('sales_payment_detail');
        $q->addQuery('*');
        $q->addWhere('payment_id='.$payment_id);
        return $q->loadList();
    }
    function create_aging_report_pdf_file()
    {
        
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');

        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Aging report');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, 17);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        $pdf->setFontSubsetting(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 10);
       
        
        global $AppUI, $CInvoiceManager, $CSalesManager;
        $customer_id = false;
        if(isset($_GET['customer_id']) && $_GET['customer_id']!="null")
            $customer_id = $_GET['customer_id'];
        
        $companyArr= $this->getCompanyByInvoice($customer_id);
        $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
        
        $data_from1=false;$data_to1=false;
        $date_fomat = "";
//        if($_GET['data_from']!=""){
//            $date_from1 = $_GET['data_from'];
//            $date_fomat.="From: ".date('d/m/Y', strtotime($date_from1))."&nbsp;&nbsp;&nbsp;";
//        }
        if($_GET['data_to']!=""){
            $date_to1 = $_GET['data_to'];
            $date_fomat.="Date: ".date('d.M/Y', strtotime($date_to1));
        }
        if($_GET['include_address']!=""){
            $include_address=$_GET['include_address'];
        }
        
        $html= '<table width="100%" border="0" callpadding="2" cellspacing="0" id="aging_report" class="tbl">
                <tr>
                    <td colspan="5" align="left">'.$date_fomat.'</td>
                    <td colspan="2" align="right">'.date("d.M.Y").'</td>
                </tr>
                <tr valign="top">
                    <td colspan="7" style="font-size:2em;" align="center" >Aging report</td>
                </tr>
                <thead >
                <tr>
                    <td width="18%" height="25"><b>'.$AppUI->_("Number").'</b></td>
                    <td width="13%"><b>'.$AppUI->_("Date").'</b></td>
                    <td width="13%" align="right"><b>'.$AppUI->_("0 - 30 Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("31 - 60 Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("61 - 90 Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("91 + Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("Balance").'</b></td>
               </tr>
               </thead><tbody>
               ';
               
        foreach ($companyArr as $company_row)
        {
            $agingArr = $this ->list_agingreport($company_row['company_id'],$_GET['date_from'],$_GET['date_to'],$date_from1,$date_to1);
            $job_location="";
            foreach ($agingArr as $aging_row)
            {
                    $invoice_id=$aging_row["invoice_id"];
                    $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
                    $total_show = $CInvoiceManager ->get_invoice_item_total($invoice_id, $invoice_revision_id);
                    //$total_item_show = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show);
                    $total_item_show = $CInvoiceManager->get_total_tax_and_paid_date($invoice_revision_id,$total_show, $date_to1);
                            $start =  strtotime($aging_row[invoice_date]);
                            $end = strtotime(date("Y/m/d"));
                            $day_diff=  round(($end - $start)/86400);

                        if($day_diff>=0 && $day_diff<=30)
                        {

                            $day30=$total_item_show;
                            $day60=0;$day90=0;$day=0;$dayBalance=$day30;
                        }
                        else if($day_diff>30 && $day_diff<=60)
                        {
                            $day60=$total_item_show;
                            $day30=0;$day90=0;$day=0;$dayBalance=$day60;

                        }
                        else if($day_diff>60 && $day_diff<=90)
                        {
                            $day90=$total_item_show;
                            $day30=0;$day60=0;$day=0;$dayBalance=$day90;
                        }
                        else if( $day_diff>90)
                        {
                            $day=$total_item_show;
                            $day30=0;$day60=0;$day90=0;$dayBalance=$day;
                        }
                        $tmpDay30 += $day30; $tmpDay60 +=$day60; $tmpDay90 += $day90;$tmpDay +=$day;$tmpBalance += $dayBalance;
                        $totalBalance = round($tmpBalance,2); $totalDay30=round($tmpDay30,2); $totalDay60=round($tmpDay60,2); $totalDay90=round($tmpDay90,2); $totalDay= round($tmpDay,2);
                            if($dayBalance>0){
                                // get joblocation
//                                $job_location_arr =  $CSalesManager->get_address_by_id($aging_row['job_location_id']);
                                if($include_address==2){
                                        $brand="";
                                    if($aging_row['address_branch_name']!="")
                                        $brand=$aging_row['address_branch_name'].' - ';
                                    $address_2="";
                                    if($aging_row['address_street_address_2']!="")
                                        $address_2= ', '.$aging_row['address_street_address_2'];
                                    $postal_code_job = '';
                                    if($aging_row['address_postal_zip_code']!="")
                                        $postal_code_job .=', Singapore '.$aging_row['address_postal_zip_code'];
                                    
                                    $job_location=$brand.$CSalesManager->htmlChars($aging_row['address_street_address_1'].$address_2.$postal_code_job);
                                    if($aging_row['address_phone_1']!="")
                                        $job_location.=", Phone: ".$aging_row['address_phone_1'];
                                    elseif($aging_row['address_phone_2']!="")
                                        $job_location.=", Phone: ".$aging_row['address_phone_2'];
                                }
                                $h_no ="";$h_job = "";
                                if($job_location!=""){
                                        $h_job ='<tr>
                                            <td style="font-size:0.9em;color:#666" colspan="11" height="30">'.$job_location.'</td>
                                        </tr>';
                                }else{
                                    $h_no = 'height="30"';
                                }

                                $html.='
                                        <tr>
   
                                            <td width="18%" height="25">'.$aging_row["invoice_no"].'</td>
                                            <td width="13%">'.date("d/M/Y",strtotime($aging_row['invoice_date'])).'</td>
                                            <td width="13%" align="right">$'.number_format($day30, 2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day60,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day90,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($dayBalance,2).'</td>
                                        </tr>
                                        '.$h_job.'';
                            }

                }
               $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
               if(count($agingArr) == 0){
                    $totalDay30=0;$totalDay60=0;$totalDay90=0;$totalDay=0;$totalBalance=0;
                }
                if($totalBalance){
                    $html.='
                            <tr style="font-weight:bold;" valign="top" >
                               <td colspan="2" id="'.$company_row['company_id'].'" height="40">'.$company_row['company_name'].'</td>
                               <td  id="tatal_day30_'.$company_row['company_id'].'" align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay30,2).'</td>
                                   <td>&nbsp;</td>
                               <td id="tatal_day60_'.$company_row['company_id'].'" align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay60,2).'</td>
                                   <td >&nbsp;</td>
                               <td id="tatal_day90_'.$company_row['company_id'].'" align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay90,2).'</td>
                                   <td >&nbsp;</td>
                               <td id="tatal_day_'.$company_row['company_id'].'" align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay,2).'</td>
                                   <td >&nbsp;</td>
                               <td id="tatal_dayBalance_'.$company_row['company_id'].'" align="right" style="border-top: 1px solid #000;">$'.number_format($totalBalance,2).'</td>
                            </tr><br>
                            ';
                        $aging_total_30 += $totalDay30;
                        $aging_total_60 += $totalDay60;
                        $aging_total_90 += $totalDay90;
                        $aging_total_day += $totalDay;
                        $aging_total_banlance += $totalBalance;
                }
            }
            $html.='<tr style="font-weight:bold;">
                    <td colspan="2" style="font-size:1.1em;">Total</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1.1em;">$'.number_format($aging_total_30,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1.1em;">$'.number_format($aging_total_60,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1.1em;">$'.number_format($aging_total_90,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1.1em;">$'.number_format($aging_total_day,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1.1em;">$'.number_format($aging_total_banlance,2).'</td>
            </tr>';
            $html.='</tbody></table>';
           
        
//        $agingFile = DP_BASE_DIR . '/modules/sales/agingReport_data.txt';
//
//            $file = fopen($agingFile,"w");
//            echo fwrite($file,$html);
//            fclose($file);
//            
            $pdf->AddPage(); 
//           
//            // get esternal file content
//            $utf8text = file_get_contents($agingFile, false);

            
            $pdf->writeHTML($html, true, false, true, false, '');
            ob_end_clean();
            
                $pdf->Output('aging_report.pdf', 'I');
            

    }
    
    function create_aging_report_html(){
        global $AppUI, $CReportManager, $CInvoiceManager,$CSalesManager;
        $customer_id = false;
        if(isset($_GET['customer_id']) && $_GET['customer_id']!="null")
            $customer_id = $_GET['customer_id'];
        $companyArr= $CReportManager->getCompanyByInvoice($customer_id);
        $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
        
        $data_from1=false;$data_to1=false;
        $date_fomat = "";
//        if($_GET['data_from']!=""){
//            $date_from1 = $_GET['data_from'];
//            $date_fomat.="From: ".date('d/m/Y', strtotime($date_from1))."&nbsp;&nbsp;&nbsp;";
//        }
        if($_GET['data_to']!=""){
            $date_to1 = $_GET['data_to'];
            $date_fomat.="Date: ".date('d.M/Y', strtotime($date_to1));
        }
        if($_GET['include_address']!=""){
            $include_address=$_GET['include_address'];
        }
        echo '<table width="100%" border=0 callpadding="2" cellspacing="1" id="aging_report" class"tbl">
                <thead>
                <tr height="45" valign="top">
                    <th colspan="4"  style="font-size:20px" align="right">Aging report</th>
                    <td align="right" colspan=10">'.date("M-d-Y").'</td>
                </tr>
                <tr style="font-weight:bold;" height="50">
                    <td width="13%" >'.$AppUI->_("Number").'</td>
                    <td width="15%">'.$AppUI->_("Date").'</td>
                    <td width="9%" align="right">'.$AppUI->_("0 - 30 Days").'</td>
                    <td width="2%"></td>
                    <td width="9%" align="right">'.$AppUI->_("31 - 60 Days").'</td>
                    <td width="2%"></td>
                    <td width="9%" align="right">'.$AppUI->_("61 - 90 Days").'</td>
                    <td width="2%"></td>
                    <td width="9%" align="right">'.$AppUI->_("91 + Days").'</td>
                    <td width="2%"></td>
                    <td width="9%" align="right">'.$AppUI->_("Balance").'</td>
               </tr></thead>';
        foreach ($companyArr as $company_row)
        {
            $agingArr = $this ->list_agingreport($company_row['company_id'],$_GET['date_from'],$_GET['date_to'],$date_from1,$date_to1);
            $job_location="";
            foreach ($agingArr as $aging_row)
            {
                    $invoice_id=$aging_row["invoice_id"];
                    $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
                    $total_show = $CInvoiceManager ->get_invoice_item_total($invoice_id, $invoice_revision_id);
                    //$total_item_show = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show);
                    $total_item_show = $CInvoiceManager->get_total_tax_and_paid_date($invoice_revision_id,$total_show, $date_to1);
                            $start =  strtotime($aging_row[invoice_date]);
                            $end = strtotime(date("Y/m/d"));
                            $day_diff=  round(($end - $start)/86400);
//
                        if($day_diff>=0 && $day_diff<=30)
                        {

                            $day30=$total_item_show;
                            $day60=0;$day90=0;$day=0;$dayBalance=$day30;
                        }
                        else if($day_diff>30 && $day_diff<=60)
                        {
                            $day60=$total_item_show;
                            $day30=0;$day90=0;$day=0;$dayBalance=$day60;

                        }
                        else if($day_diff>60 && $day_diff<=90)
                        {
                            $day90=$total_item_show;
                            $day30=0;$day60=0;$day=0;$dayBalance=$day90;
                        }
                        else if( $day_diff>90)
                        {
                            $day=$total_item_show;
                            $day30=0;$day60=0;$day90=0;$dayBalance=$day;
                        }
                        $tmpDay30 += $day30; $tmpDay60 +=$day60; $tmpDay90 += $day90;$tmpDay +=$day;$tmpBalance += $dayBalance;
                        $totalBalance = round($tmpBalance,2); $totalDay30=round($tmpDay30,2); $totalDay60=round($tmpDay60,2); $totalDay90=round($tmpDay90,2); $totalDay= round($tmpDay,2);
                            if($dayBalance>0){
                                // get joblocation
//                                $job_location_arr =  $CSalesManager->get_address_by_id($aging_row['job_location_id']);
                                if($include_address==2){
                                        $brand="";
                                    if($aging_row['address_branch_name']!="")
                                        $brand=$aging_row['address_branch_name'].' - ';
                                    $address_2="";
                                    if($aging_row['address_street_address_2']!="")
                                        $address_2= ', '.$aging_row['address_street_address_2'];
                                    $postal_code_job = '';
                                    if($aging_row['address_postal_zip_code']!="")
                                        $postal_code_job .=', Singapore '.$aging_row['address_postal_zip_code'];
                                    
                                    $job_location=$brand.$CSalesManager->htmlChars($aging_row['address_street_address_1'].$address_2.$postal_code_job);
                                    if($aging_row['address_phone_1']!="")
                                        $job_location.=", Phone: ".$aging_row['address_phone_1'];
                                    elseif($aging_row['address_phone_2']!="")
                                        $job_location.=", Phone: ".$aging_row['address_phone_2'];
                                }
                                $h_no ="";$h_job = "";
                                if($job_location!=""){
                                        $h_job ='<tr>
                                            <td style="font-size:0.9em;color:#666" colspan="11" height="30">'.$job_location.'</td>
                                        </tr>';
                                }else{
                                    $h_no = 'height="30"';
                                }
//
                                $html.='
                                        <tr>
   
                                            <td width="18%" height="25">'.$aging_row["invoice_no"].'</td>
                                            <td width="13%">'.date("d/M/Y",strtotime($aging_row['invoice_date'])).'</td>
                                            <td width="13%" align="right">$'.number_format($day30, 2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day60,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day90,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($dayBalance,2).'</td>
                                        </tr>
                                        '.$h_job.'';
                            }
//
                }
               $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
               if(count($agingArr) == 0){
                    $totalDay30=0;$totalDay60=0;$totalDay90=0;$totalDay=0;$totalBalance=0;
                }
                if($totalBalance){
                    $html.='
                            <tr style="font-weight:bold;" valign="top" >
                               <td colspan="2"  height="40">'.$company_row['company_name'].'</td>
                               <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay30,2).'</td>
                                   <td>&nbsp;</td>
                               <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay60,2).'</td>
                                   <td >&nbsp;</td>
                               <td align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay90,2).'</td>
                                   <td >&nbsp;</td>
                               <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay,2).'</td>
                                   <td >&nbsp;</td>
                               <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalBalance,2).'</td>
                            </tr>
                            ';
                        $aging_total_30 += $totalDay30;
                        $aging_total_60 += $totalDay60;
                        $aging_total_90 += $totalDay90;
                        $aging_total_day += $totalDay;
                        $aging_total_banlance += $totalBalance;
                }
            }
            echo $html;
            echo '<tr style="font-weight:bold;">
                    <td colspan="2" style="font-size:16px;">Total</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:16px;">$'.number_format($aging_total_30,2).'</td>
                    <td></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:16px;">$'.number_format($aging_total_60,2).'</td>
                    <td></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:16px;">$'.number_format($aging_total_90,2).'</td>
                    <td></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:16px;">$'.number_format($aging_total_day,2).'</td>
                    <td></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:16px;">$'.number_format($aging_total_banlance,2).'</td>
            </tr></table>';
    }
    function create_cash_receipt_pdf($date_start,$date_end,$customer_id_arr){
        global  $ocio_config;
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Cash Receipt');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('times', '', 9.5);
        $CBankAccount = new CBankAccount();

        global $AppUI, $CReportManager, $CInvoiceManager, $CPaymentManager;
            include DP_BASE_DIR."/modules/sales/report.js.php";
            $PaymentMethods = dPgetSysVal('PaymentMethods');
//            $date_start = $_GET['date_start'];
//            $date_end = $_GET['date_end'];
            $now = getdate();
            if($date_start =="" && $date_end == ""){
                $mgsDate=substr(date("d-M-Y"),3);
            }else{
                if($date_end=="")
                    $date_end_fo = date('');
                else
                    $date_end_fo = date($ocio_config['php_abms_date_format'],strtotime($date_end));
                $mgsDate='From '.date($ocio_config['php_abms_date_format'],strtotime($date_start)).' to '.$date_end_fo;
            }
            $html='<table width="100%"  border="0" callpadding="0" cellspacing="0" id="aging_report" class="tbl">
                    <tr><td align="right" colspan="10" style="font-size:1em">'.date($ocio_config['php_abms_date_format'],strtotime(date("d.M.Y"))).'</td></tr>
                    <tr height="30" valign="top">
                        <td colspan="10"  style="font-size:2em" align="center">Cash receipt</td>
                    </tr>
                    <tr height="45" valign="top">
                        <td align="center" colspan="10">'.$mgsDate.'<br></td>
                    </tr>
                    <thead>
                    <tr style="font-size:1em; font-weight: bold;" height="30" id="">
                        <td width="11%" align="right" >'.$AppUI->_("Date Rec'd").'</td>
                        <td width="1%"></td>
                        <td width="20%" align="left">'.$AppUI->_("Customer").'</td>
                        <td width="15%" align="right">'.$AppUI->_("Payment Amount").'</td>
                        <td width="1%"></td>
                        <td width="10%" align="right">'.$AppUI->_("Cheque Nos").'</td>
                        <td width="1%"></td>
                        <td width="10%" align="right">'.$AppUI->_("Receipt Nos").'</td>
                        <td width="1%"></td>
                        <td width="12%" align="left" >'.$AppUI->_("Bank Account").'</td>
                        <td width="1%"></td>
                        <td width="10%">'.$AppUI->_("Note").'</td>
                        <td width="1%"></td>
                        <td width="15%" align="left" >'.$AppUI->_("Invoices Paid").'</td>
                        
                   </tr>
                   </thead>
                   <tr height="15"><td colspan="10"></td></tr>';

            $total=0;
            if($date_start == "" && $date_end==""){
                $tmp_data = 0;$report_payment_total=0;   
                foreach ($PaymentMethods as $key => $value) {
                    $report_payment_arr = $CPaymentManager->list_db_payment("","","",false,"$key");
                    $tmp=0;$total_method=0;
                    foreach ($report_payment_arr as $report_payment_row){
                        $bank_account_db = $CBankAccount->getBankAccount($report_payment_row['bank_account_id']);
                        $bank_account_name= $bank_account_db[0]['bank_account_name'];
                        $report_payment_month = substr($report_payment_row['payment_date'],5,-3);
                        $report_payment_year = substr($report_payment_row['payment_date'],0,4);
                        if($report_payment_month==$now['mon'] && $report_payment_year==$now['year']){
                            $report_payment_month_year = date('d/M/Y',strtotime($report_payment_row['payment_date']));
                            $report_payment_month_year = substr($report_payment_month_year,3);

                            $report_payment_total_amuont = round($CReportManager->get_payment_amount_total($report_payment_row['payment_id']),2);
                            $report_paymentDetail_arr = $CReportManager->getPaymentDetailByPayment($report_payment_row['payment_id']);

                            $cus_invoice_revision = "";
                            foreach ($report_paymentDetail_arr as $report_paymentDetail_row) {
                                //print_r($report_paymentDetail_row) ;
                                        $report_customer_arr = $CReportManager->getCustomerByInvoiceRev($report_paymentDetail_row['invoice_revision_id']);
                                        foreach ($report_customer_arr as $report_customer_row){
                                            $customer=$report_customer_row['company_name'];
                                            $customer_id = $report_customer_row['company_id'];
                                            $cus_invoice_revision = $report_customer_row['invoice_revision_id'];
                                        }

                                    };
                            if($cus_invoice_revision!=""){
                                if(in_array($customer_id, $customer_id_arr) || count($customer_id_arr)==0){
                                    $html.='
                                            <tr height="4" >
                                            <td align="right"  width="11%">'.date($ocio_config['php_abms_date_format'],strtotime($report_payment_row['payment_date'])).'</td>
                                            <td width="1%"></td>
                                            <td width="20%">'.$customer.'</td>
                                            <td align="right" width="15%">$'.number_format($report_payment_total_amuont,2).'</td>
                                            <td width="1%"></td>
                                            <td width="10%">'.$report_payment_row['payment_cheque_nos'].'</td>
                                            <td width="1%"></td>
                                            <td width="10%">'.$report_payment_row['payment_receipt_no'].'</td>
                                            <td width="1%"></td>
                                            <td width="12%">'.$bank_account_name.'</td>
                                            <td width="1%"></td>
                                            <td width="10%" >'.$report_payment_row['payment_notes'].'</td>
                                            <td width="1%"></td>
                                            <td width="15%" align="left">';
                                    foreach ($report_paymentDetail_arr as $report_paymentDetail_row) {
                                        //print_r($report_paymentDetail_row) ;
                                                $report_invoice_arr = $CReportManager->getInvoiceByInvoiceRev($report_paymentDetail_row['invoice_revision_id']);
                                                foreach ($report_invoice_arr as $report_invoice_row){
                                                    $report_invoice = $report_invoice_row['invoice_no'];       
                                                };
                                                $html.=$report_invoice.": ";
                                                $html.="$".number_format($report_paymentDetail_row['payment_amount'],2)."; ";
                                            }
                                    $html.='</td>'
                                            
                                            . '</tr>';
                                    $tmp_data=1;$report_payment_total+=$report_payment_total_amuont;$tmp=1;
                                    $total_method+=$report_payment_total_amuont;
                                }
                            }
                        }
                    }
                    if($tmp==1)
                        $html.= '
                            <tr style="font-weight:bold;">
                                    <td colspan="3" >Method: '.$value.'</td>
                                    <td colspan="1" align="right" style="border-top: 1px solid #000;">$'.number_format ($total_method, 2).'</td>
                                    <td colspan="4" align="right">&nbsp;</td>
                            </tr><tr><td></td></tr>';
                }
                if($tmp_data==1){
                    $total+=$report_payment_total;
                    $html.='<tr style="font-weight: bold; font-size: 1em;background:#EEEEEE;" height="35">
                            <td colspan="3"  style="background-color:#EEEEEE;">'.$report_payment_month_year.'</td>
                            <td align="right"  style="background-color:#EEEEEE;">$'.number_format($report_payment_total,2).'</td>
                            <td colspan="12"  style="background-color:#EEEEEE;"></td>
                        </tr><tr><td></td></tr>';
                    
                } 
            }else{
                for($i=1;$i<=12;$i++){
                $tmp_data = 0;$report_payment_total=0; 
                foreach ($PaymentMethods as $key => $value) {
                    $report_payment_arr = $CPaymentManager->list_db_payment("",$date_start,$date_end,true,"$key");
                    $tmp=0;$total_method=0;
                    foreach ($report_payment_arr as $report_payment_row){
                        $bank_account_db = $CBankAccount->getBankAccount($report_payment_row['bank_account_id']);
                        $bank_account_name= $bank_account_db[0]['bank_account_name'];
                        $report_payment_month = substr($report_payment_row['payment_date'],5,-3);
                        if($report_payment_month==$i){
                            $report_payment_month_year = date('d/M/Y',strtotime($report_payment_row['payment_date']));
                            $report_payment_month_year = substr($report_payment_month_year,3);
//
                            $report_payment_total_amuont = round($CReportManager->get_payment_amount_total($report_payment_row['payment_id']),2);
                            $report_paymentDetail_arr = $CReportManager->getPaymentDetailByPayment($report_payment_row['payment_id']);
//                            
                            $cus_invoice_revision = "";
                            $customer="";
                            foreach ($report_paymentDetail_arr as $report_paymentDetail_row) {
//                                //print_r($report_paymentDetail_row) ;
                                    $report_customer_arr = $CReportManager->getCustomerByInvoiceRev($report_paymentDetail_row['invoice_revision_id']);
                                   foreach ($report_customer_arr as $report_customer_row){
                                            $customer=$report_customer_row['company_name'];
                                            $customer_id = $report_customer_row['company_id'];
                                            $cus_invoice_revision = $report_customer_row['invoice_revision_id'];
                                        }
                                    };
                            if($cus_invoice_revision!=""){
                                if(in_array($customer_id, $customer_id_arr) || count($customer_id_arr)==0){
                                    $html.='
                                            <tr height="4" >
                                            <td align="right"  width="11%">'.date($ocio_config['php_abms_date_format'],strtotime($report_payment_row['payment_date'])).'</td>
                                            <td width="1%"></td>
                                            <td width="20%">'.$customer.'</td>
                                            <td align="right" width="15%">$'.number_format($report_payment_total_amuont,2).'</td>
                                            <td width="1%"></td>
                                            <td width="10%">'.$report_payment_row['payment_cheque_nos'].'</td>
                                            <td width="1%"></td>
                                            <td width="10%">'.$report_payment_row['payment_receipt_no'].'</td>
                                            <td width="1%"></td>
                                            <td width="12%">'.$bank_account_name.'</td>
                                            <td width="1%"></td>
                                            <td width="10%" >'.$report_payment_row['payment_notes'].'</td>
                                            <td width="1%"></td>
                                            <td width="15%" align="left">';
                                    foreach ($report_paymentDetail_arr as $report_paymentDetail_row) {
                                        //print_r($report_paymentDetail_row) ;
                                                $report_invoice_arr = $CReportManager->getInvoiceByInvoiceRev($report_paymentDetail_row['invoice_revision_id']);
                                                foreach ($report_invoice_arr as $report_invoice_row){
                                                    $report_invoice = $report_invoice_row['invoice_no'];       
                                                };
                                                $html.=$report_invoice.": ";
                                                $html.="$".number_format($report_paymentDetail_row['payment_amount'],2)."; ";
                                            }
                                    $html.='</td></tr>';
                                    $tmp_data=1;$report_payment_total+=$report_payment_total_amuont;$tmp=1;
                                    $total_method+=$report_payment_total_amuont;
                                }
                            }
                        }

                    }
                      if($tmp==1)
                        $html.= '
                            <tr style="font-weight:bold;">
                                    <td colspan="3" >Method: '.$value.'</td>
                                    <td colspan="1" align="right" style="border-top: 1px solid #000;">$'.number_format ($total_method, 2).'</td>
                                    <td colspan="4" align="right">&nbsp;</td>
                            </tr><tr><td></td></tr>';
                }
                if($tmp_data==1){
                    $total+=$report_payment_total;
                    $html.='<tr style="font-weight: bold; font-size: 1em;background:#EEEEEE;" height="35">
                            <td colspan="3"  style="background-color:#EEEEEE;">'.$report_payment_month_year.'</td>
                            <td align="right"  style="background-color:#EEEEEE;">$'.number_format($report_payment_total,2).'</td>
                            <td colspan="12"  style="background-color:#EEEEEE;"></td>
                        </tr><tr><td></td></tr>';
                    
                } 
                }
            }
    
    $html.= '<tr style="font-weight: bold;">
                <td colspan="3">Total:</td>
                <td align="right" style="border-top: 1px solid #000;border-bottom: 1px solid #000; height:25px;">$'.number_format($total,2).'</td>
            </tr>
            </table>'; 
            $pdf->AddPage();              
            $pdf->writeHTML($html, true, false, true, false, '');
            ob_end_clean();
            $pdf->Output('cash_receipt.pdf', 'I');
    }
    function create_cash_receipt_html($date_start,$date_end,$custormer_id_cash){
        global $AppUI, $CReportManager, $CInvoiceManager, $CPaymentManager;
                $CBankAccount = new CBankAccount();

            include DP_BASE_DIR."/modules/sales/report.js.php";
            $PaymentMethods = dPgetSysVal('PaymentMethods');
//            $date_start = $_GET['date_start'];
//            $date_end = $_GET['date_end'];
            $now = getdate();
            if($date_start=="" && $date_end==""){
                $mgsDate=substr(date("d-M-Y"),3);
            }else{
                $mgsDate="From: $date_start to $date_end";
            }
            echo '<table width="100%" border=0 callpadding="0" cellspacing="0" id="aging_report" class="tbl">
                    <thead>
                    <tr><td align="right" colspan="10" style="font-size:14px">'.date("M-d-Y").'</td></tr>
                    <tr height="40" valign="top">
                        <td colspan="10"  style="font-size:20px" align="center">Cash receipt</td>
                    </tr>
                    <tr height="45" valign="top">
                        <td align="center" colspan="10">'.$mgsDate.'</td>
                    </tr>
                    <tr style="font-size:14px; font-weight: bold;" height="30" id="">
                        <td width="10%" align="left" >'.$AppUI->_("Date Ree'd").'</td>
                        <td width="20%" align="left">'.$AppUI->_("Customer").'</td>
                        <td width="2%"></td>
                        <td width="14%" align="right">'.$AppUI->_("Payment Amount").'</td>
                        <td width="2%"></td>
                        <td width="8%" align="left">'.$AppUI->_("Method").'</td>
                        <td width="2%"></td>
                        <td width="8%" align="left">'.$AppUI->_("Note").'</td>
                        <td width="2%"></td>
                        <td width="20%" align="left" >'.$AppUI->_("Invoices Paid").'</td>
                        <td width="2%"></td>
                        <td width="15%" align="left" >'.$AppUI->_("Bank Account").'</td>
                   </tr>
                   <tr height="15"><td colspan="12"></td></tr></thead>';

            echo '<tbody>';
            $total=0;
            if($date_start=="" && $date_end==""){
                $report_payment_arr = $CPaymentManager->list_db_payment();
                $tmp_data = 0;$report_payment_total=0;
                foreach ($report_payment_arr as $report_payment_row){
                    $bank_account_db = $CBankAccount->getBankAccount($report_payment_row['bank_account_id']);
                    $bank_account_name= $bank_account_db[0]['bank_account_name'];
                    $report_payment_month = substr($report_payment_row['payment_date'],5,-3);
                    if($report_payment_month==$now['mon']){
                        $report_payment_month_year = date('d/M/Y',strtotime($report_payment_row['payment_date']));
                        $report_payment_month_year = substr($report_payment_month_year,3);

                        $report_payment_total_amuont = round($CReportManager->get_payment_amount_total($report_payment_row['payment_id']),2);
                        $report_paymentDetail_arr = $CReportManager->getPaymentDetailByPayment($report_payment_row['payment_id']);

                        foreach ($report_paymentDetail_arr as $report_paymentDetail_row) {
                            //print_r($report_paymentDetail_row) ;
                                    $report_customer_arr = $CReportManager->getCustomerByInvoiceRev($report_paymentDetail_row['invoice_revision_id']);
                                    foreach ($report_customer_arr as $report_customer_row){
                                        $customer=$report_customer_row['company_name'];
                                        $customer_id = $report_customer_row['company_id'];
                                    }
                                }
                        if(in_array($customer_id, $custormer_id_cash) || count($custormer_id_cash)==0){
                            echo '<tr height="35">
                                    <td>'.date('m/d/Y',strtotime($report_payment_row['payment_date'])).'</td>
                                    <td>'.$customer.'</td>
                                    <td></td>
                                    <td align="right">$'.number_format($report_payment_total_amuont,2).'</td>
                                    <td></td>
                                    <td>'.$PaymentMethods[$report_payment_row['payment_method']].'</td>
                                    <td></td>
                                    <td>'.$report_payment_row['payment_notes'].'</td>
                                    <td></td>
                                    <td>';
                            foreach ($report_paymentDetail_arr as $report_paymentDetail_row) {
                                //print_r($report_paymentDetail_row) ;
                                        $report_invoice_arr = $CReportManager->getInvoiceByInvoiceRev($report_paymentDetail_row['invoice_revision_id']);
                                        foreach ($report_invoice_arr as $report_invoice_row){
                                            $report_invoice = $report_invoice_row['invoice_no'];       
                                        };
                                        echo $report_invoice.": ";
                                        echo "$".number_format($report_paymentDetail_row['payment_amount'],2)."; ";
                                    }
                            echo'</td>'
                                    . '<td></td>
                                      <td>'.$bank_account_name.'</td>'
                                    . '</tr>';
                            $tmp_data=1;$report_payment_total+=$report_payment_total_amuont;
                        }
                    }
                }
                if($tmp_data==1){
                    $total+=$report_payment_total;
                    echo '<tr style="font-weight: bold; font-size: 14px;" height="35">
                            <td colspan="3" >'.$report_payment_month_year.'</td>
                            <td align="right" style="border-top: 1px solid rgb(0, 0, 0);">$'.number_format($report_payment_total,2).'</td>
                            <td colspan="9"></td>
                        </tr>
                        <tr><td style="background:#E9E9E9; height:1px" colspan="12"></td></tr>
                        <tr height="5"><td colspan="15" style="height:8px"></td></tr>
                        '; 
                }
                
            }else{
                for($i=1;$i<=12;$i++){
                    $report_payment_arr = $CPaymentManager->list_db_payment("",$date_start,$date_end);
                    $tmp_data = 0;$report_payment_total=0;
                    foreach ($report_payment_arr as $report_payment_row){
                        $report_payment_month = substr($report_payment_row['payment_date'],5,-3);
                        if($report_payment_month==$i){
                            $report_payment_month_year = date('d/M/Y',strtotime($report_payment_row['payment_date']));
                            $report_payment_month_year = substr($report_payment_month_year,3);

                            $report_payment_total_amuont = round($CReportManager->get_payment_amount_total($report_payment_row['payment_id']),2);
                            $report_paymentDetail_arr = $CReportManager->getPaymentDetailByPayment($report_payment_row['payment_id']);
                            foreach ($report_paymentDetail_arr as $report_paymentDetail_row) {
                                //print_r($report_paymentDetail_row) ;
                                        $report_customer_arr = $CReportManager->getCustomerByInvoiceRev($report_paymentDetail_row['invoice_revision_id']);
                                        foreach ($report_customer_arr as $report_customer_row){
                                            $customer=$report_customer_row['company_name'];
                                            $customer_id = $report_customer_row['company_id'];
                                        }
                                    };
                            if(in_array($customer_id, $custormer_id_cash) || count($custormer_id_cash)==0){
                                echo    '
                                        <tr height="35">
                                        <td>'.date('m/d/Y',strtotime($report_payment_row['payment_date'])).'</td>
                                        <td>'.$customer.'</td>
                                        <td></td>
                                        <td align="right">$'.number_format($report_payment_total_amuont,2).'</td>
                                        <td></td>
                                        <td>'.$PaymentMethods[$report_payment_row['payment_method']].'</td>
                                        <td></td>
                                        <td>'.$report_payment_row['payment_notes'].'</td>
                                        <td></td>
                                        <td>';
                                foreach ($report_paymentDetail_arr as $report_paymentDetail_row) {
                                    //print_r($report_paymentDetail_row) ;
                                            $report_invoice_arr = $CReportManager->getInvoiceByInvoiceRev($report_paymentDetail_row['invoice_revision_id']);
                                            foreach ($report_invoice_arr as $report_invoice_row){
                                                $report_invoice = $report_invoice_row['invoice_no'];       
                                            };
                                            echo $report_invoice.": ";
                                            echo "$".number_format($report_paymentDetail_row['payment_amount'],2)."; ";
                                        }
                                echo'</td></tr>';
                                $tmp_data=1;$report_payment_total+=$report_payment_total_amuont;
                            }
                        }
                    }
                    if($tmp_data==1){
                        $total+=$report_payment_total;
                        echo '<tr style="font-weight: bold; font-size: 14px;" height="35">
                                <td colspan="3" style="background-color:#EEEEEE;">'.$report_payment_month_year.'</td>
                                <td align="right" style="background-color:#EEEEEE;">$'.number_format($report_payment_total,2).'</td>
                                <td colspan="9" style="background-color:#EEEEEE;"></td>
                            </tr>
                            '; 
                    }
                }
            }
            echo '
                <tr height="20"><td></td></tr>
                <tr style="font-weight: bold">
                    <td colspan="3" >Total:</td>
                    <td align="right" style="border-top: 1px solid rgb(0, 0, 0);border-bottom: 1px solid rgb(0, 0, 0);">$'.number_format($total,2).'<td>
                 </tr>';
            echo '</tbody>
                </table>';
    }
    
    function create_invoice_journal_html($date_start,$date_end,$customer_id_arr){
        global $AppUI,$CReportManager,$CInvoiceManager;
        include DP_BASE_DIR."/modules/sales/report.js.php";
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $now = getdate();
        if($date_start=="" && $date_end==""){
            $mgsDate=substr(date("d.M.Y"),3);
        }else{
            $mgsDate="From: $date_start to $date_end";
        }
        echo '<table width="100%" border=0" callpadding="0" cellspacing="0" id="aging_report" class="tbl">
                <thead>
                <tr><td align="right" colspan="13" style="font-size:14px">'.date("M-d-Y").'</td></tr>
                <tr height="40" valign="top">
                    <td colspan="13"  style="font-size:24px; font-weight:bold" align="center">Invoice journal</td>
                </tr>
                <tr height="45" valign="top">
                    <td align="center" colspan="13">'.$mgsDate.'</td>
                </tr>
                <tr style="font-size:16px; font-weight:bold" height="30">
                    <td width="8%">'.$AppUI->_("Number").'</td>
                    <td width="2%"></td>
                    <td width="10%" align="left" >'.$AppUI->_("Date").'</td>
                    <td width="2%"></td>
                    <td width="30%" align="left">'.$AppUI->_("Customer").'</td>
                    <td width="2%"></td>
                    <td width="10%" align="right">'.$AppUI->_("Subtotal").'</td>
                    <td width="2%"></td>
                    <td width="10%" align="right">'.$AppUI->_("Tax1").'</td>
                    <td width="2%"></td>
                    <td width="10%" align="right">'.$AppUI->_("Tax2").'</td>
                    <td width="2%"></td>
                    <td width="10%" align="right" >'.$AppUI->_("Total").'</td>
               </tr>
               <tr height="15"><td colspan="13"></td></tr></thead>';

        echo '<tbody>';
        $tax2 = 0;
        if($date_start =="" && $date_end==""){
            $jour_invoice_arr = $CInvoiceManager->list_db_invoice("","",$customer_id_arr);
            foreach ($jour_invoice_arr as $jour_invoice_row) {
                $jour_invoice_rev_id=$CInvoiceManager->get_invoice_revision_lastest($jour_invoice_row['invoice_id']);
                $subtotal=  round($CInvoiceManager->get_invoice_item_total($jour_invoice_row['invoice_id'], $jour_invoice_rev_id),2);
                $tax1 = round(floatval($subtotal)*(7/100),2);
                $total = round(floatval($subtotal),2) + round(floatval($tax1),2);

                $invoice_date_month = intval(substr($jour_invoice_row['invoice_date'],5,-3));
            if($invoice_date_month == $now['mon']){
                $date_month_year = date('d/M/Y',strtotime($jour_invoice_row['invoice_date']));
                $date_month_year = substr($date_month_year,3);
                echo'<tr>
                        <td>'.date('d/M/Y',strtotime($jour_invoice_row['invoice_date'])).'</td>
                        <td></td>
                        <td>'.$jour_invoice_row['invoice_date'].'</td>
                        <td></td>
                        <td>'.$jour_invoice_row['company_name'].'</td>
                        <td></td>
                        <td align="right">$'.number_format($subtotal,2).'</td>
                        <td></td>
                        <td align="right">$'.number_format($tax1,2).'</td>
                        <td></td>
                        <td align="right">$'.number_format($tax2,2).'</td>
                        <td></td>
                        <td align="right">$'.number_format($total,2).'</td>
                    </tr>';
                $total_subtotal += $subtotal;
                $total_tax1 += $tax1;
                $total_total += $total;
                }
            }
                $jour_total_subtotal += $total_subtotal;
                $jour_total_tax1 += $total_tax1;
                $jour_total+=$total_total;
            echo'<tr style="font-weight:bold">
                    <td colspan="6">'.$date_month_year.'</td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($total_subtotal,2).'</td>
                    <td ></td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($total_tax1,2).'</td>
                    <td></td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($tax2,2).'</td>
                    <td></td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($total_total,2).'</td>
                </tr>
                <tr><td colspan="13" style="height:8px"></td></tr>
                <tr><td style="background:#E9E9E9; height:1px" colspan="13"></td></tr>
                <tr><td colspan="15" style="height:5px"></td></tr>
                <tr>
                    <td colspan="13" style="height:10px"></td>
                <tr>
                <tr style="font-weight:bold; font-size:15px;">
                <td colspan="6">Total</td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($jour_total_subtotal,2).'</td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($jour_total_tax1,2).'<td>
                <td></td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($jour_total,2).'</td>
            </tr>';
        }
        else{
            for($i=12;$i>=1;$i--){
                $tmp_total_total=0;$tmp_total_subtotal=0;$tmp_total_tax1=0;$tmp = 0;
                $jour_invoice_arr = $CInvoiceManager->list_db_invoice($date_start,$date_end,$customer_id_arr);
                foreach ($jour_invoice_arr as $jour_invoice_row) {
                    $invoice_date_month = intval(substr($jour_invoice_row['invoice_date'],5,-3));
                    if($invoice_date_month == $i){
                        $date_month_year = date('d/M/Y',strtotime($jour_invoice_row['invoice_date']));
                        $date_month_year = substr($date_month_year,3);
                        $jour_invoice_rev_id=$CInvoiceManager->get_invoice_revision_lastest($jour_invoice_row['invoice_id']);
                        $subtotal=round($CInvoiceManager->get_invoice_item_total($jour_invoice_row['invoice_id'], $jour_invoice_rev_id),2);
                        $tax1 = round(floatval($subtotal)*(7/100),2);
                        $total = round(floatval($subtotal),2) + round(floatval($tax1),2);
                        echo'<tr>
                               <td>'.$jour_invoice_row['invoice_no'].'</td>
                               <td></td>
                               <td>'.date('d/M/Y',strtotime($jour_invoice_row['invoice_date'])).'</td>
                               <td></td>
                               <td>'.$jour_invoice_row['company_name'].'</td>
                               <td></td>
                               <td align="right">$'.number_format($subtotal,2).'</td>
                               <td></td>
                               <td align="right">$'.number_format($tax1,2).'</td>
                               <td></td>
                               <td align="right">$'.number_format($tax2,2).'</td>
                               <td></td>
                               <td align="right">$'.number_format($total,2).'</td>
                            </tr>';
                        $tmp_total_subtotal += $subtotal;
                        $tmp_total_tax1 += $tax1;
                        $tmp_total_total+= $total;
                        $tmp=1;
                        }
                 $total_subtotal = $tmp_total_subtotal;
                 $total_tax1 = $tmp_total_tax1;
                 $total_total = $tmp_total_total;
                }
                $jour_total_subtotal += $tmp_total_subtotal;
                $jour_total_tax1 += $tmp_total_tax1;
                $jour_total+=$total_total;
                if($tmp == 1){
                    echo'<tr style="font-weight:bold">
                        <td colspan="6" >'.$date_month_year.'</td>
                        <td style="border-top:1px solid #000" align="right">$'.number_format($total_subtotal,2).'</td>
                        <td></td>
                        <td style="border-top:1px solid #000" align="right">$'.number_format($total_tax1,2).'</td>
                        <td></td>
                        <td align="right" style="border-top:1px solid #000">$'.number_format($tax2,2).'</td>
                        <td></td>
                        <td style="border-top:1px solid #000" align="right">$'.number_format($total_total,2).'</td>
                    </tr>
                    <tr><td colspan="13" style="height:8px"></td></tr>
                    <tr><td style="background:#E9E9E9; height:1px" colspan="12"></td></tr>
                    <tr height="5"><td colspan="15" style="height:15px"></td></tr>
                    <tr><td colspan="13" style="height:8px"></td></tr>';
                }
            }
            echo'<tr style="font-weight:bold">
                <td colspan="6" style="font-size:15px;">Total</td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($jour_total_subtotal,2).'</td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($jour_total_tax1,2).'</td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($tax2,2).'</td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($jour_total,2).'</td>
            </tr>';
        }
        echo '</tbody></table>';
        
    }
    
    function create_invoice_jourmal_pdf($date_start,$date_end,$customer_id_arr){
        
        global $ocio_config;
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Invoice journal');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(15, 5, 7, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('times', '', 10.5);
       
        
        global $AppUI, $CReportManager, $CInvoiceManager,$CSalesManager, $CTax;
        
        include DP_BASE_DIR."/modules/sales/report.js.php";
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $now = getdate();
        if($date_start=="" && $date_end==""){
            $mgsDate=substr(date("d.M.Y"),3);
        }
        else if($date_start!="" && $date_end=="")
        {
            $mgsDate="From ".date($ocio_config['php_abms_date_format'],  strtotime($date_start));
        }
        else if($date_start=="" && $date_end!="")
        {
           $mgsDate='To '. date($ocio_config['php_abms_date_format'],  strtotime($date_end)); 
        }   
        else{
            $mgsDate="From ".date($ocio_config['php_abms_date_format'],  strtotime($date_start)).' to '. date($ocio_config['php_abms_date_format'],  strtotime($date_end));
        }
        $html= '<table width="100%" border="0" callpadding="0" cellspacing="2" id="aging_report" class="tbl">
                <tr><td align="right" colspan="10">'.date($ocio_config['php_abms_date_format'],strtotime(date("d.M.Y"))).'</td></tr>
                <tr height="40" valign="top">
                    <td colspan="10" align="center" style="font-size:2em">Invoice journal</td>
                </tr>
                <tr height="45" valign="top">
                    <td align="center" colspan="10">'.$mgsDate.'</td>
                </tr>
                <tr height="15"><td colspan="10"></td></tr>
                <thead>
                <tr height="40" style="font-weight:bold;">
                    <td width="11%" height="30">'.$AppUI->_("Number").'</td>
                    <td width="11%" align="left" >'.$AppUI->_("Date").'</td>
                    <td width="27%" align="left">'.$AppUI->_("Customer").'</td>
                    <td width="12%" align="right">'.$AppUI->_("Subtotal").'</td>
                        <td width="1%"></td>
                    <td width="12%" align="right">'.$AppUI->_("Tax1").'</td>
                        <td width="1%"></td>
                    <td width="12%" align="right">'.$AppUI->_("Tax2").'</td>
                        <td width="1%"></td>
                    <td width="12%" align="right" >'.$AppUI->_("Total").'</td>
               </tr>
               </thead>';
        $tax2 = 0;
        if($date_start =="" && $date_end==""){
            $jour_invoice_arr = $CInvoiceManager->list_db_invoice("","",$customer_id_arr);
            foreach ($jour_invoice_arr as $jour_invoice_row) {
                $jour_invoice_rev_id=$CInvoiceManager->get_invoice_revision_lastest($jour_invoice_row['invoice_id']);
                $jour_invoice_rev_arr = $CInvoiceManager->get_latest_invoice_revision($jour_invoice_row['invoice_id']);
                $subtotal=round($CInvoiceManager->get_invoice_item_total($jour_invoice_row['invoice_id'], $jour_invoice_rev_id),2);
                $subtotal_last_discount=round($CInvoiceManager->get_invoice_total_last_discount($jour_invoice_row['invoice_id'], $jour_invoice_rev_id),2);
                if($jour_invoice_rev_arr[0]['invoice_revision_tax_edit']!=0)
                {
                    $tax1 = $jour_invoice_rev_arr[0]['invoice_revision_tax_edit'];
                }
                else 
                {
                    $tax_arr = $CTax->get_tax($jour_invoice_rev_arr[0]['invoice_revision_tax']);
                    $tax1 = $subtotal_last_discount*($tax_arr[0]['tax_rate']/100);
                }
                    
                
                $total = $CInvoiceManager->get_total_item_invoice_and_tax($jour_invoice_row['invoice_id'],$jour_invoice_rev_id);

                $invoice_date_month = intval(substr($jour_invoice_row['invoice_date'],5,-3));
                $invoice_date_year = intval(substr($jour_invoice_row['invoice_date'],0,4));
            if($invoice_date_month == $now['mon'] && $invoice_date_year == $now['year']){
                $date_month_year = date('d/M/Y',strtotime($jour_invoice_row['invoice_date']));
                $date_month_year = substr($date_month_year,3);
                $html.='<tr>
                        <td width="11%">'.$jour_invoice_row['invoice_no'].'</td>
                        <td width="11%">'.date($ocio_config['php_abms_date_format'],strtotime($jour_invoice_row['invoice_date'])).'</td>
                        <td width="27%">'.$jour_invoice_row['company_name'].'</td>
                        <td width="12%" align="right">$'.number_format($subtotal_last_discount,2).'</td>
                            <td width="1%"></td>
                        <td width="12%" align="right">$'.number_format($tax1,2).'</td>
                            <td width="1%"></td>
                        <td width="12%" align="right">$'.number_format($tax2,2).'</td>
                            <td width="1%"></td>
                        <td width="12%" align="right">$'.number_format($subtotal_last_discount+$tax1+$tax2,2).'</td>
                    </tr>';
                $total_subtotal += $subtotal_last_discount;
                $total_tax1 += $tax1;
                $total_total += $total;
                }
            }
                $jour_total_subtotal += $total_subtotal;
                $jour_total_tax1 += $total_tax1;
                $jour_total+=$total_total;
            $html.='<tr style="font-weight:bold">
                    <td colspan="3">'.$date_month_year.'</td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($total_subtotal,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($total_tax1,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($tax2,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($total_subtotal+$total_tax1+$tax2,2).'</td>
                </tr>
                <tr><td colspan="10"></td></tr>
                <tr style="font-weight:bold;">
                    <td colspan="3">Total</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_subtotal,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_tax1,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($tax2,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_subtotal+$jour_total_tax1+$tax2,2).'</td>
                </tr>';
        }
        else{
            for($i=1;$i<=12;$i++){
                $tmp_total_total=0;$tmp_total_subtotal=0;$tmp_total_tax1=0;$tmp = 0;
                $jour_invoice_arr = $CInvoiceManager->list_db_invoice($date_start,$date_end,$customer_id_arr);
                foreach ($jour_invoice_arr as $jour_invoice_row) {
                    $invoice_date_month = intval(substr($jour_invoice_row['invoice_date'],5,-3));
                    if($invoice_date_month == $i){
                        $date_month_year = date('d/M/Y',strtotime($jour_invoice_row['invoice_date']));
                        $date_month_year = substr($date_month_year,3);
                        $jour_invoice_rev_id=$CInvoiceManager->get_invoice_revision_lastest($jour_invoice_row['invoice_id']);
                        $jour_invoice_rev_arr = $CInvoiceManager->get_latest_invoice_revision($jour_invoice_row['invoice_id']);
                        $subtotal=round($CInvoiceManager->get_invoice_item_total($jour_invoice_row['invoice_id'], $jour_invoice_rev_id),2);
                        $subtotal_last_discount = $CInvoiceManager->get_invoice_total_last_discount($jour_invoice_row['invoice_id'],$jour_invoice_rev_id);
                        
                        if($jour_invoice_rev_arr[0]['invoice_revision_tax_edit']!=0)
                        {
                            $tax1 = $jour_invoice_rev_arr[0]['invoice_revision_tax_edit'];
                        }
                        else 
                        {
                            $tax_arr = $CTax->get_tax($jour_invoice_rev_arr[0]['invoice_revision_tax']);
                            $tax1 = $subtotal_last_discount*($tax_arr[0]['tax_rate']/100);
                        }


                        $total = $CInvoiceManager->get_total_item_invoice_and_tax($jour_invoice_row['invoice_id'],$jour_invoice_rev_id);
                        $html.='<tr>
                               <td width="11%">'.$jour_invoice_row['invoice_no'].'</td>
                               <td width="11%">'.date($ocio_config['php_abms_date_format'],strtotime($jour_invoice_row['invoice_date'])).'</td>
                               <td width="27%">'.$jour_invoice_row['company_name'].'</td>
                               <td width="12%" align="right">$'.number_format($subtotal_last_discount,2).'</td>
                                   <td width="1%"></td>
                               <td width="12%" align="right">$'.number_format($tax1,2).'</td>
                                   <td width="1%"></td>
                               <td width="12%" align="right">$'.number_format($tax2,2).'</td>
                                   <td width="1%"></td>
                               <td width="12%" align="right">$'.number_format($subtotal_last_discount+$tax1+$tax2,2).'</td>
                            </tr>';
                        $tmp_total_subtotal += $subtotal_last_discount;
                        $tmp_total_tax1 += $tax1;
                        $tmp_total_total+= $total;
                        $tmp=1;
                        }
                 $total_subtotal = $tmp_total_subtotal;
                 $total_tax1 = $tmp_total_tax1;
                 $total_total = $tmp_total_total;
                }
                $jour_total_subtotal += $tmp_total_subtotal;
                $jour_total_tax1 += $tmp_total_tax1;
                $jour_total+=$total_total;
                if($tmp == 1){
                    $html.='<tr style="font-weight:bold">
                            <td colspan="3">'.$date_month_year.'</td>
                            <td align="right" style="border-top:1px solid #000">$'.number_format($total_subtotal,2).'</td>
                                <td width="1%"></td>
                            <td align="right" style="border-top:1px solid #000">$'.number_format($total_tax1,2).'</td>
                                <td width="1%"></td>
                            <td align="right" style="border-top:1px solid #000">$'.number_format($tax2,2).'</td>
                                <td width="1%"></td>
                            <td align="right" style="border-top:1px solid #000">$'.number_format($total_subtotal+$total_tax1+$tax2,2).'</td>
                        </tr>
                        <tr><td colspan="10"></td></tr>';
                }
            }
                $html.='<tr style="font-weight:bold;">
                    <td colspan="3">Total</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_subtotal,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_tax1,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($tax2,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_subtotal+$jour_total_tax1+$tax2,2).'</td>
                </tr>';
        }
        $html.= '</table>';
        
        $pdf->AddPage();              
        $pdf->writeHTML($html, true, false, true, false, '');
        ob_end_clean();
        $pdf->Output('invoice_journal.pdf', 'I');
    }
    
    //print html
    function create_invoice_jourmal_html($date_start,$date_end,$customer_id_arr){
        
       
        
        global $AppUI, $CReportManager, $CInvoiceManager,$CSalesManager, $CTax,$ocio_config;
        
        include DP_BASE_DIR."/modules/sales/report.js.php";
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $now = getdate();
        if($date_start=="" && $date_end==""){
            $mgsDate=substr(date("d.M.Y"),3);
        }
        else if($date_start!="" && $date_end=="")
        {
            $mgsDate="From ".date($ocio_config['php_abms_date_format'],  strtotime($date_start));
        }
        else if($date_start=="" && $date_end!="")
        {
           $mgsDate='To '. date($ocio_config['php_abms_date_format'],  strtotime($date_end)); 
        }   
        else{
            $mgsDate="From ".date($ocio_config['php_abms_date_format'],  strtotime($date_start)).' to '. date($ocio_config['php_abms_date_format'],  strtotime($date_end));
        }
        $html= '<table width="100%" border="0" callpadding="0" cellspacing="2" id="aging_report" class="tbl">
                <tr><td align="right" colspan="10">'.date("d.M.Y").'</td></tr>
                <tr height="40" valign="top">
                    <td colspan="10" align="center" style="font-size:2em">Invoice journal</td>
                </tr>
                <tr height="45" valign="top">
                    <td align="center" colspan="10">'.$mgsDate.'</td>
                </tr>
                <tr height="15"><td colspan="10"></td></tr>
               
                <tr height="40" style="font-weight:bold;">
                    <td width="11%" height="30">'.$AppUI->_("Number").'</td>
                    <td width="11%" align="left" >'.$AppUI->_("Date").'</td>
                    <td width="27%" align="left">'.$AppUI->_("Customer").'</td>
                    <td width="12%" align="right">'.$AppUI->_("Subtotal").'</td>
                        <td width="1%"></td>
                    <td width="12%" align="right">'.$AppUI->_("Tax1").'</td>
                        <td width="1%"></td>
                    <td width="12%" align="right">'.$AppUI->_("Tax2").'</td>
                        <td width="1%"></td>
                    <td width="12%" align="right" >'.$AppUI->_("Total").'</td>
               </tr>
               ';
        $tax2 = 0;
        if($date_start =="" && $date_end==""){
            $jour_invoice_arr = $CInvoiceManager->list_db_invoice("","",$customer_id_arr);
            foreach ($jour_invoice_arr as $jour_invoice_row) {
                $jour_invoice_rev_id=$CInvoiceManager->get_invoice_revision_lastest($jour_invoice_row['invoice_id']);
                $jour_invoice_rev_arr = $CInvoiceManager->get_latest_invoice_revision($jour_invoice_row['invoice_id']);
                $subtotal=round($CInvoiceManager->get_invoice_item_total($jour_invoice_row['invoice_id'], $jour_invoice_rev_id),2);
                $subtotal_last_discount=round($CInvoiceManager->get_invoice_total_last_discount($jour_invoice_row['invoice_id'], $jour_invoice_rev_id),2);
                if($jour_invoice_rev_arr[0]['invoice_revision_tax_edit']!=0)
                {
                    $tax1 = $jour_invoice_rev_arr[0]['invoice_revision_tax_edit'];
                }
                else 
                {
                    $tax_arr = $CTax->get_tax($jour_invoice_rev_arr[0]['invoice_revision_tax']);
                    $tax1 = $subtotal_last_discount*($tax_arr[0]['tax_rate']/100);
                }
                    
                
                $total = $CInvoiceManager->get_total_item_invoice_and_tax($jour_invoice_row['invoice_id'],$jour_invoice_rev_id);

                $invoice_date_month = intval(substr($jour_invoice_row['invoice_date'],5,-3));
                $invoice_date_year = intval(substr($jour_invoice_row['invoice_date'],0,4));
            if($invoice_date_month == $now['mon'] && $invoice_date_year == $now['year']){
                $date_month_year = date('d/M/Y',strtotime($jour_invoice_row['invoice_date']));
                $date_month_year = substr($date_month_year,3);
                $html.='<tr>
                        <td width="11%">'.$jour_invoice_row['invoice_no'].'</td>
                        <td width="11%">'.date($ocio_config['php_abms_date_format'],strtotime($jour_invoice_row['invoice_date'])).'</td>
                        <td width="27%">'.$jour_invoice_row['company_name'].'</td>
                        <td width="12%" align="right">$'.number_format($subtotal_last_discount,2).'</td>
                            <td width="1%"></td>
                        <td width="12%" align="right">$'.number_format($tax1,2).'</td>
                            <td width="1%"></td>
                        <td width="12%" align="right">$'.number_format($tax2,2).'</td>
                            <td width="1%"></td>
                        <td width="12%" align="right">$'.number_format($subtotal_last_discount+$tax1+$tax2,2).'</td>
                    </tr>';
                $total_subtotal += $subtotal_last_discount;
                $total_tax1 += $tax1;
                $total_total += $total;
                }
            }
                $jour_total_subtotal += $total_subtotal;
                $jour_total_tax1 += $total_tax1;
                $jour_total+=$total_total;
            $html.='<tr style="font-weight:bold">
                    <td colspan="3">'.$date_month_year.'</td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($total_subtotal,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($total_tax1,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($tax2,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000">$'.number_format($total_subtotal+$total_tax1+$tax2,2).'</td>
                </tr>
                <tr><td colspan="10"></td></tr>
                <tr style="font-weight:bold;">
                    <td colspan="3">Total</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_subtotal,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_tax1,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($tax2,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_subtotal+$jour_total_tax1+$tax2,2).'</td>
                </tr>';
        }
        else{
            for($i=1;$i<=12;$i++){
                $tmp_total_total=0;$tmp_total_subtotal=0;$tmp_total_tax1=0;$tmp = 0;
                $jour_invoice_arr = $CInvoiceManager->list_db_invoice($date_start,$date_end,$customer_id_arr);
                foreach ($jour_invoice_arr as $jour_invoice_row) {
                    $invoice_date_month = intval(substr($jour_invoice_row['invoice_date'],5,-3));
                    if($invoice_date_month == $i){
                        $date_month_year = date('d/M/Y',strtotime($jour_invoice_row['invoice_date']));
                        $date_month_year = substr($date_month_year,3);
                        $jour_invoice_rev_id=$CInvoiceManager->get_invoice_revision_lastest($jour_invoice_row['invoice_id']);
                        $jour_invoice_rev_arr = $CInvoiceManager->get_latest_invoice_revision($jour_invoice_row['invoice_id']);
                        $subtotal=round($CInvoiceManager->get_invoice_item_total($jour_invoice_row['invoice_id'], $jour_invoice_rev_id),2);
                        $subtotal_last_discount = $CInvoiceManager->get_invoice_total_last_discount($jour_invoice_row['invoice_id'],$jour_invoice_rev_id);
                        
                        if($jour_invoice_rev_arr[0]['invoice_revision_tax_edit']!=0)
                        {
                            $tax1 = $jour_invoice_rev_arr[0]['invoice_revision_tax_edit'];
                        }
                        else 
                        {
                            $tax_arr = $CTax->get_tax($jour_invoice_rev_arr[0]['invoice_revision_tax']);
                            $tax1 = $subtotal_last_discount*($tax_arr[0]['tax_rate']/100);
                        }


                        $total = $CInvoiceManager->get_total_item_invoice_and_tax($jour_invoice_row['invoice_id'],$jour_invoice_rev_id);
                        $html.='<tr>
                               <td width="11%">'.$jour_invoice_row['invoice_no'].'</td>
                               <td width="11%">'.date($ocio_config['php_abms_date_format'],strtotime($jour_invoice_row['invoice_date'])).'</td>
                               <td width="27%">'.$jour_invoice_row['company_name'].'</td>
                               <td width="12%" align="right">$'.number_format($subtotal_last_discount,2).'</td>
                                   <td width="1%"></td>
                               <td width="12%" align="right">$'.number_format($tax1,2).'</td>
                                   <td width="1%"></td>
                               <td width="12%" align="right">$'.number_format($tax2,2).'</td>
                                   <td width="1%"></td>
                               <td width="12%" align="right">$'.number_format($subtotal_last_discount+$tax1+$tax2,2).'</td>
                            </tr>';
                        $tmp_total_subtotal += $subtotal_last_discount;
                        $tmp_total_tax1 += $tax1;
                        $tmp_total_total+= $total;
                        $tmp=1;
                        }
                 $total_subtotal = $tmp_total_subtotal;
                 $total_tax1 = $tmp_total_tax1;
                 $total_total = $tmp_total_total;
                }
                $jour_total_subtotal += $tmp_total_subtotal;
                $jour_total_tax1 += $tmp_total_tax1;
                $jour_total+=$total_total;
                if($tmp == 1){
                    $html.='<tr style="font-weight:bold">
                            <td colspan="3">'.$date_month_year.'</td>
                            <td align="right" style="border-top:1px solid #000">$'.number_format($total_subtotal,2).'</td>
                                <td width="1%"></td>
                            <td align="right" style="border-top:1px solid #000">$'.number_format($total_tax1,2).'</td>
                                <td width="1%"></td>
                            <td align="right" style="border-top:1px solid #000">$'.number_format($tax2,2).'</td>
                                <td width="1%"></td>
                            <td align="right" style="border-top:1px solid #000">$'.number_format($total_subtotal+$total_tax1+$tax2,2).'</td>
                        </tr>
                        <tr><td colspan="10"></td></tr>';
                }
            }
                $html.='<tr style="font-weight:bold;">
                    <td colspan="3">Total</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_subtotal,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_tax1,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($tax2,2).'</td>
                        <td width="1%"></td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">$'.number_format($jour_total_subtotal+$jour_total_tax1+$tax2,2).'</td>
                </tr>';
        }
        $html.= '</table>';
        
        echo $html;
    }

    function get_gst_report($customer_id_arr=false,$from_date=false,$to_date=false){// $customer_id_arr=(1,2,3,..)
        $q = new DBQuery();
        $q->addTable('sales_invoice','tbl1');
        $q->addQuery('tbl1.invoice_id,tbl1.invoice_no,tbl1.invoice_date,tbl5.company_name');
        //$q->addJoin('sales_invoice', 'tbl3', 'tbl3.invoice_id=tbl1.invoice_id');
        $q->addJoin('clients', 'tbl5', 'tbl5.company_id=tbl1.customer_id');
        //$q->addWhere('tbl1.invoice_revision_tax=1');
        if($customer_id_arr)
            $q->addWhere ('tbl5.company_id IN '.$customer_id_arr);
        if($from_date)
            $q->addWhere ('tbl1.invoice_date >="'.$from_date.'"');
        if($to_date)
            $q->addWhere('tbl1.invoice_date <="'.$to_date.'"');
        //$q->addGroup('tbl3.invoice_id');
        $q->addOrder('tbl1.invoice_date ASC');
        return $q->loadList();
        
    }
    function create_gst_report_pdf(){
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('GST Report');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(5, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('times', '', 10);
        $CReportManager = new CReportManager();
        global $AppUI,$CSalesManager,$CInvoiceManager,$CPOManager,$po_invoice,$CPaymentManager,$ExpensesCategory;
        $customer_value_arr =false ;
        $from_date = false;
        $to_date =false;
        $from_date_fomart ="";
        $to_date_fomart ="";
      
        if(isset($_GET['customer_id_arr']) && ($_GET['customer_id_arr'])!=null)
            $customer_value_arr = "(0".$_GET['customer_id_arr'].")";
        if(isset($_GET['from_date']) && $_GET['from_date']!=""){
            $from_date = $_GET['from_date'];
            $from_date_fomart = 'From: '.date('d.M.Y',strtotime($from_date));
        }
        
        if(isset($_GET['to_date']) && $_GET['to_date']!=""){
            $to_date = $_GET['to_date'];
            $to_date_fomart = 'To: '.date('d.M.Y',strtotime($to_date));
        }
        if($_GET['to_date']=="" && $_GET['from_date']!=""){
            $to_date_fomart = 'To: '.date('d.M.Y');
        }
        if($_GET['to_date']=="" && $_GET['from_date']==""){
            $date_fomat = "";
        }  else {
             $date_fomat='<tr valign="top">
                    <td colspan="4" align="center">'.$from_date_fomart.' &nbsp;&nbsp;&nbsp;'.$to_date_fomart.'</td>
                </tr>';
        }
        
        //$html .= '<tr><td colspan="4"><b>Sales Invoice GST Report</b></td></tr>';
        $gst_report_arr = $CReportManager->get_gst_report($customer_value_arr,$from_date,$to_date);
        //$invoice_revision_tax = $gst_report_row['invoice_revision_tax'];
        
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        $CTax = new CTax();
         $html ='<table width="100%"  callpadding="3" cellspacing="4" id="aging_report" class="tbl" >
                <tr><td align="right" colspan="4">'.date("d.M.Y").'</td></tr>
                <tr>
                    <td colspan="7" align="center" style="font-size:1.6em">GST Report</td>
                </tr>
                '.$date_fomat.'<br /><br />
                     <tr><td style="margin-top:20px;margin-bottom:10px;"><b>Sales Invoice GST Report</b></td></tr></table>';
          $html .='<table width="100%"  border="1" callpadding="3" cellspacing="0" id="aging_report" class="tbl" >
                
                <thead>
                    <tr style="font-weight:bold;" height="20px" style="background-color:#dfeffc">
                        <td align="left" height="20px" width="10%"><b>Date</b></td>
                        <td align="left" width="10%"><b>Invoice Nos</b></td>
                        <td align="left" width="30%"><b>Customer</b></td>
                        <td align="right" width="10%"><b>Value</b></td>
                        <td align="right" width="10%"><b>GST</b></td>
                        <td align="right" width="10%"><b>Total Amount</b></td>
                        <td align="right" width="10%"><b>Value Collected</b></td>
                        <td align="right" width="10%"><b>GST Collected</b></td>
                        
                    </tr>
                </thead>';
                foreach ($gst_report_arr as $gst_report_row) {
                    $invoice_id = $gst_report_row['invoice_id'];
        //            $invoice_revision_id = $gst_report_row['invoice_revision_id'];
        //
        //            $tax_id = $gst_report_row['invoice_revision_tax'];
                    $invoice_revision_arr = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
                    $invoice_revision_id = $invoice_revision_arr[0]['invoice_revision_id'];

                    $tax_id = $invoice_revision_arr[0]['invoice_revision_tax'];
                    $tax_arr = $CTax->get_tax($tax_id);
                    $tax_rate = $tax_arr[0]['tax_rate'];

                    $total_item = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
                    $total_item_last_discount = $CInvoiceManager->get_invoice_total_last_discount($invoice_id, $invoice_revision_id);
                    $tax = $total_item_last_discount*$tax_rate/100;
                    if($invoice_revision_arr[0]['invoice_revision_tax_edit']!=0)
                        $tax = $invoice_revision_arr[0]['invoice_revision_tax_edit'];
                    $tax = $CSalesManager->round_up($tax);
                    $total_tax+=$tax;
                    $date = date('d/M/Y', strtotime($gst_report_row['invoice_date']));

                };
                $invoice_purchase_arr = array();
                $i=0;
                foreach ($gst_report_arr as $gst_report_row){

                    $invoice_id = $gst_report_row['invoice_id'];
                    $invoice_revision_arr = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
                    $invoice_revision_id = $invoice_revision_arr[0]['invoice_revision_id'];

                    $tax_id = $invoice_revision_arr[0]['invoice_revision_tax'];
                    $tax_arr = $CTax->get_tax($tax_id);
                    $tax_rate = $tax_arr[0]['tax_rate'];

                    $total_item = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
                    $total_item_last_discount = $CInvoiceManager->get_invoice_total_last_discount($invoice_id, $invoice_revision_id);
                    $tax = $total_item_last_discount*($tax_rate/100);
                    if($invoice_revision_arr[0]['invoice_revision_tax_edit']!=0)
                        $tax = $invoice_revision_arr[0]['invoice_revision_tax_edit'];
                    //$tax = $CSalesManager->round_up($tax);
                    $date = date('d/M/Y', strtotime($gst_report_row['invoice_date']));

                    $invoice_purchase_arr[$i]['date'] =  $date;
                    $invoice_purchase_arr[$i]['number_no']=$gst_report_row['invoice_no'];
                    $invoice_purchase_arr[$i]['customer_name']=$gst_report_row['company_name'];
                    $invoice_purchase_arr[$i]['invoice_value']=$total_item_last_discount;
                    $invoice_purchase_arr[$i]['gst_collected']=$tax;
                    $invoice_purchase_arr[$i]['total_amount']=  $CInvoiceManager->calculateTotalInvoice($invoice_id);
                    $value_payment = $CPaymentManager->get_total_payment($invoice_revision_id);
                    if($value_payment == 0)
                    {
                        $invoice_purchase_arr[$i]['value_collected']=  0;
                        $invoice_purchase_arr[$i]['value_gst']=  0;
                    }
                    else
                    {
                        $invoice_purchase_arr[$i]['value_gst']=  $tax;
                        $invoice_purchase_arr[$i]['value_collected']=  $value_payment-$tax;

                    }
                    $invoice_purchase_arr[$i]['gst_paid']=0;
                    $i++;
                }
                
                $total_gst_colleted= 0;$total_gst_paid_invoice=0;$total_invoice_value=0;$total_purchase_value=0;
                $total_payment_collected = 0;$value_gst_collected=0;
              //  $invoice_purchase_arr = $CInvoiceManager->array_sort($invoice_purchase_arr, 'date');
                foreach ($invoice_purchase_arr as $value_item) {
                    $total_gst_colleted+=$value_item['gst_collected'];
                    $value_gst_collected += $value_item['value_gst'];
                    $total_gst_paid_invoice+=$value_item['total_amount'];
                    $total_invoice_value += $value_item['invoice_value'];
                    $total_purchase_value += $value_item['purchase_value'];
                    $total_payment_collected +=$value_item['value_collected'];

                    $html .=   '<tr id="title_body">
                                <td width="10%" height="30px">'.$value_item['date'].'</td>
                                <td width="10%">'.$value_item['number_no'].'</td>
                                <td width="30%">'.$value_item['customer_name'].'</td>
                                <td width="10%" align="right">$'.number_format($value_item['invoice_value'],2).'</td>
                                <td width="10%" align="right">$'.number_format($value_item['gst_collected'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_item['total_amount'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_item['value_collected'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_item['value_gst'],2).'</td>
                            </tr>';
                }


                if(count($invoice_purchase_arr)==0){
                    $html .= '<tr id="title_body"><td colspan="4">No GST Report Found</td></tr>';
                }
                $html .=    '</tbody>
                        <tfoot >
                            <tr id="title_body" height="20px" style="background-color:#dfeffc">
                                <td colspan="3" height="30px"><b>Total</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_invoice_value,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_gst_colleted,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_gst_paid_invoice,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_payment_collected,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($value_gst_collected,2).'</b></td>
                            </tr>
                        </tfoot>

                    </table><br /><br />';
                
                //    /*============ GST PURCHASE NO ============ */
                $html .='<div style="margin-top:40px;margin-bottom:30px;"><b>Purchased Order (PO) GST</b></div>';
                $html .='<br />';
                $CPOPayment = new CPOPayment();
                $invoice_supplier_arr=array();
                $purchase_supplier_arr = $po_invoice->getListPo(false,$from_date,$to_date,false,$customer_value_arr);
                $poPayment = new CPOPayment();
                $total_purchase_payment = 0;
                $total_gst_po_Paid = 0;
                $total_gst_po_payable=0;
                foreach ($purchase_supplier_arr as $purchase_item) {
                    if($purchase_item['GST_Registered'] == 1){
                    $date = date('d/M/Y', strtotime($purchase_item['po_date']));
                    $po_invoice_id = $purchase_item['po_invoice_id'];
                    $total_purchase_payment += $poPayment->getTotalSIPayment($po_invoice_id);
                    if($poPayment->getTotalSIPayment($po_invoice_id) > 0)
                        $total_gst_po_Paid +=$po_invoice->calculateTotalGSTByPoInv($po_invoice_id);
                    else {
                        $total_gst_po_payable +=$po_invoice->calculateTotalGSTByPoInv($po_invoice_id);;
                    }
                    $invoice_supplier_arr[$i]['date'] =  $date;
                    $invoice_supplier_arr[$i]['number_no']=$purchase_item['po_invoice_no'];
                    $invoice_supplier_arr[$i]['customer_name']=$purchase_item['company_name'];
                    $invoice_supplier_arr[$i]['invoice_value']=0;
                    $invoice_supplier_arr[$i]['gst_collected']=0;
                    $invoice_supplier_arr[$i]['purchase_value']=$po_invoice->calculateTotalNoTaxPo($po_invoice_id);
                    $invoice_supplier_arr[$i]['gst_paid']=$po_invoice->calculateTotalGSTByPoInv($po_invoice_id);
                    $invoice_supplier_arr[$i]['Value_Paid']=$poPayment->getTotalSIPayment($po_invoice_id);

                    $payment_invoice_supplier =$CPOPayment->getTotalSIPayment($po_invoice_id,false);
                    $invoice_supplier_arr[$i]['total_amount']=$po_invoice->getTotalPoLastPayment($po_invoice_id)+$po_invoice->getTotalPaymentByPo($po_invoice_id);
                    if($payment_invoice_supplier > 0)
                        $invoice_supplier_arr[$i]['gst_supplier']=$invoice_supplier_arr[$i]['gst_paid'];

                    $i++;
                    }
                }
                $html .= '<table width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
                        <thead >
                            <tr id="title_head" height="20px" style="background-color:#dfeffc">
                                <td align="left" width="10%"><b>Date</b></td>
                                <td align="left" width="10%"><b>Invoice Nos</b></td>
                                <td align="left" width="30%"><b>Customer</b></td>
                                <td align="right" width="10%"><b>Value</b></td>
                                <td align="right" width="10%"><b>GST </b></td>
                                <td align="right" width="10%"><b>Total Amount</b></td>
                                <td align="right" width="10%"><b>Value Paid</b></td>
                                <td align="right" width="10%"><b>Payable GST</b></td>

                            </tr>
                        </thead>
                        <tbody>';

//                    $invoice_supplier_arr = $CInvoiceManager->array_sort($invoice_supplier_arr, 'date');
                    
                    $total_purchase_value=0;
                    $total_gst_paid=0;
                    $total_amount = 0;
                    $gst_supplier = 0;
                    $gst_paid = 0;

                    foreach ($invoice_supplier_arr as $value_item) {
                        $total_purchase_value +=$value_item['purchase_value'];
                        $total_gst_paid +=$value_item['gst_paid'];
                        $total_amount += $value_item['total_amount'];
                        $paid = 0;
                        if($value_item['total_amount'] > 0){
                                $paid = $value_item['gst_paid'];
                                $gst_paid +=$paid;
                        }   
                        else
                                $paid = 0;
                        $html .=   '<tr id="title_body">
                                    <td height="30px" width="10%">'.$value_item['date'].'</td>
                                    <td width="10%">'.$value_item['number_no'].'</td>
                                    <td width="30%"> '.$value_item['customer_name'].'</td>
                                    <td align="right" width="10%">$'.number_format($value_item['purchase_value'],2).'</td>
                                    <td align="right" width="10%">$'.number_format($value_item['gst_paid'],2).'</td>
                                    <td align="right" width="10%">$'.number_format($value_item['total_amount'],2).'</td>
                                    <td align="right" width="10%">$'.number_format(($value_item['Value_Paid']-$paid),2).'</td>
                                    <td align="right" width="10%">';
                                    if($value_item['total_amount'] > 0){
                                        $html .=  '$'.number_format(0,2);
                                    }   
                                else
                                     $html .=  '$'.number_format($value_item['gst_paid'],2);
                               $html .= '</td></tr>';
                    }


                    if(count($invoice_purchase_arr)==0){
                        $html .= '<tr id="title_body"><td colspan="4">No GST Report Found</td></tr>';
                    }
                $html .=    '</tbody>
                        <tfoot >
                            <tr id="title_body" height="20px" style="background-color:#dfeffc">
                                <td colspan="3" height="30px"><b>Total</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_purchase_value,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_gst_paid,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format(($total_amount-$gst_supplier),2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format(($total_purchase_payment-$gst_paid),2).'</b></td>
                                
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($gst_supplier,2).'</b></td>
                            </tr>
                        </tfoot>

                   </table><br /><br />';
      
                //Expenses GST Report
    $html .= '<div style="margin-top:20px;margin-bottom:10px;"><b>Expenses GST Report</b></div><br />';
    $ExpensesSupplierManage  = new ExpensesSupplierManage();
    $ex_arr = $ExpensesSupplierManage->getExIDByCustomer($customer_value_arr);
    $expensesManage = new ExpensesManager();
    $exTaxManage = new Expenses_tax_manage();
    $expensesGST=array();
    $j=0;
    $total_ex_payable=0;
    $category_arr = dPgetSysVal('Categories');
    foreach ($ex_arr as $data)
    {
        $ex = $expensesManage->getExpensesByExId($data['expenses_id'],$from_date,$to_date);
//        echo count($ex);
        
                
        if(isset($ex[0]['expenses_date']) && $ex[0]['GST_Claimable'] == 1){
        $total_ex_payable += $expensesManage->calculateTotalTaxExpense($data['expenses_id']);
        $expensesGST[$j]['date']=$ex[0]['expenses_date'];
        $expensesGST[$j]['expenses_no']=$ex[0]['expenses_no'];
        $expensesGST[$j]['category_id']=$category_arr[$ex[0]['category_id']];
        $expensesGST[$j]['value']=$ex[0]['expenses_amount'];
        $expensesGST[$j]['GST']=$exTaxManage->getGSTByEx($data['expenses_id']);
        $expensesGST[$j]['Amount']=$expensesManage->CalculateTotalExpenses($data['expenses_id']);
//        $expensesGST[$j]['Receivable_GST']=0;
        $j++;}
 
    }
//    echo '<pre>';
//    print_r($expensesGST);
            $html .= '<table width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
                    <thead >
                        <tr id="title_head" height="20px" style="background-color:#dfeffc">
                            <td align="left" height="30px" width="10%"><b>Date</b></td>
                            <td align="left" width="10%"><b>Expenses Nos.</b></td>
                            <td align="left" width="30%"><b>Category</b></td>
                            <td align="right" width="10%"><b>Value</b></td>
                            <td align="right" width="10%"><b>GST </b></td>
                            <td align="right" width="10%"><b>Total Amount</b></td>
                            <td align="right" width="10%"><b>Value Paid</b></td>
                            <td align="right" width="10%"><b>Payable GST</b></td>

                        </tr>
                    </thead>
                    <tbody>';
            $expenses_value=0;
            $gst_total_ex=0;
            $gst_amount_ex=0;
            $gst_Receivable_GST=0;
            foreach ($expensesGST as $value_ex) {
                    $expenses_value +=$value_ex['value'];
                    $gst_total_ex +=$value_ex['GST'];
                    $gst_amount_ex += $value_ex['Amount'];
                    $gst_Receivable_GST += $value_ex['Receivable_GST'];
                    $html .=   '<tr id="title_body">
                                <td width="10%">'.$value_ex['date'].'</td>
                                <td width="10%">'.$value_ex['expenses_no'].'</td>
                                <td width="30%">'.$value_ex['category_id'].'</td>
                                <td align="right" width="10%">$'.number_format($value_ex['value'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_ex['GST'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_ex['Amount'],2).'</td>
                                <td align="right" width="10%">$'.number_format(0,2).'</td>
                                <td align="right" width="10%">$'.number_format($value_ex['GST'],2).'</td>

                            </tr>';
                }
        //        if(count($expensesGST)==0){
        //            echo '<tr id="title_body"><td colspan="4">No GST Report Found</td></tr>';
        //        }

             $html .=    '</tbody>
                    <tfoot >
                        <tr id="title_body" height="20px" style="background-color:#dfeffc">
                            <td colspan="3" height="30px"><b>Total</b></td>
                            <td align="right" style="border-top:1px solid #000"><b>$'.number_format($expenses_value,2).'</b></td>
                            <td align="right" style="border-top:1px solid #000"><b>$'.number_format($gst_total_ex,2).'</b></td>
                            <td align="right" style="border-top:1px solid #000"><b>$'.number_format($gst_amount_ex,2).'</b></td>
                            <td align="right" style="border-top:1px solid #000"><b>$'.number_format(0,2).'</b></td>
                            <td align="right" style="border-top:1px solid #000"><b>$'.number_format($gst_total_ex,2).'</b></td>
                        </tr>
                    </tfoot>

                </table>';
            $html .= '</div>';

        //     GST Report Summary
            $sale_collected = $total_payment_collected+$value_gst_collected;
    $sale_Recievable= $total_invoice_value-$total_payment_collected;
    $html .=  '<div id="gst_report_summmary">';
      $html .=  '<div style="margin-top:20px;margin-bottom:10px;"><b>Received / Payable GST Summary Report</b></div>';
       $html .=  '<table width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
            <thead >
                <tr id="title_head" height="20px" style="background-color:#dfeffc">
                    <td align="left"height="30px" width="20%">Sales</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"> </td>
                </tr>
            </thead>
            <tbody>';
        $html .=  '<tr>'
                . '<td align="left" width="20%">Total Sales Collected</td>'
                . '<td align="right" width="20%" height="30px">$'.number_format($total_payment_collected,2).'</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        $html .= '<tr>'
                . '<td align="left" width="20%" height="20px" >Total GST Collected</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="right" width="20%">$'.number_format($value_gst_collected,2).'</td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
       
       $html .=  '<tr>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        $html .=  '<tr id="title_head">
                    <td align="left" width="20%">Purchases</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                  
                    <td align="right" width="40%"> </td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total Purchases Paid</td>
                    <td align="right" width="20%">$'.number_format($total_purchase_payment-$gst_paid,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                    
                
                </tr>';
        $html .=  '<tr>
                    <td align="left" width="20%">Total GST Paid</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format(($total_gst_paid-$gst_supplier),2).'</td>
                
                </tr>';
       
        $html .=  '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
        $html .=  '<tr id="title_head">
                    <td align="left" width="20%">Expenses</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                  
                    <td align="right" width="40%"> </td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total Expenses Paid</td>
                    <td align="right" width="20%">$'.number_format(0,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                    
                
                </tr>';
        $html .=  '<tr>
                    <td align="left" width="20%">Total GST Paid</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format(0,2).'</td>
                
                </tr>';
       
        $html .=  '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
       
        $html .=     '</tbody>';
        $html .=  '<tfoot >';
         $html .=  '<tr >
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" colspan="3"><b>Total GST Output /Payable to IRAS</b></td>
                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="20%"></td>
                    
                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="20%">$'.number_format($value_gst_collected,2).'</td>
                  
                
                </tr>';
         
        $html .=  '<tr>
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000" colspan="4"><b>Total GST Input / Refundable from IRAS</b></td>
                    
                    
                    <td align="left"  width="40%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000"></td>
                   
                    <td align="right" width="40%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000"> $'.number_format(($total_gst_paid-$gst_supplier),2).'</td>
                
                </tr>';
        $html .=  '<tr>
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000" colspan="3"><b>Nett GST to be paid to IRAS</b></td>
                   
                    <td align="left"  width="40%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000"></td>

                   
                    <td align="right" width="40%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000">$'.number_format($value_gst_collected-($total_gst_paid-$gst_supplier),2).' </td>
                
                </tr>';
        
        $html .=  '</tfoot>';
            
        $html .=  '</table>';
        
        $html .=  '<br />';
        $html .=  '<br />';
        
        //Payable/Receivable GST Summary Report
      $html .=  '<div style="margin-top:20px;margin-bottom:10px;"><b>Total Value GST Summary Report</b></div>';
       $html .=  '<table width="100%" width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
            <thead >
                <tr id="title_head">
                    <td align="left" width="20%">Sales</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"> </td>
                </tr>
            </thead>
            <tbody>';
        $html .=  '<tr>'
                . '<td align="left" width="20%">Total Sales Value</td>'
                . '<td align="right" width="20%">$'.number_format($sale_Recievable,2).'</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        $html .=  '<tr>'
                . '<td align="left" width="20%">Total GST Value</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="right" width="20%">$'.number_format($total_gst_colleted-$value_gst_collected,2).'</td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        
        $html .=  '<tr>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        $html .=  '<tr id="title_head">
                    <td align="left" width="20%">Purchases</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                  
                    <td align="right" width="40%"> </td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total Payable Purchases\'s</td>
                    <td align="RIGHT" width="20%">$'.number_format(($total_amount-$total_purchase_payment),2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total GST Payable</td>
                    <td align="right" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format($total_gst_paid-$gst_paid,2).'</td>
                    
                
                </tr>';
       
       
        $html .=  '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
        $html .=  '<tr id="title_head">
                    <td align="left" width="20%">Expenses</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                  
                    <td align="right" width="40%"> </td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total Payable Expenses</td>
                    <td align="RIGHT" width="20%">$'.number_format($expenses_value,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total GST Payable</td>
                    <td align="right" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format($gst_total_ex,2).'</td>
                    
                
                </tr>';
       
       
        $html .=  '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
       
        $html .=     '</tbody>';
        $html .=  '<tfoot >';
         $html .=  '<tr >
                     <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" colspan="2"><b>Total GST Output /Payable to IRAS</b></td>

                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="20%"></td>
                    
                    <td align="right" width="20%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000">$'.number_format($total_gst_colleted-$value_gst_collected,2).'</td>
                
                </tr>';
        $html .=  '<tr>
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000" colspan="3"><b>Total GST Input / Refundable from IRAS</b></td>
                   
                    <td align="left"  width="40%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000"></td>

                   
                    <td align="right" width="40%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000">$'.number_format($total_gst_paid-$gst_paid,2).' </td>
                
                </tr>';
        $html .=  '<tr>
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000" colspan="3"><b>Nett GST to be paid to IRAS</b></td>
                   
                    <td align="left"  width="40%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000"></td>

                   
                    <td align="right" width="40%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000">$'.number_format($total_gst_colleted-$value_gst_collected,2).' </td>
                
                </tr>';
        
        $html .=  '</tfoot>';
            
        $html .=  '</table>';
        
        $html .=  '<br />';
        $html .=  '<br />';
        $html .=  '</div>';
        
        $pdf->AddPage();              
        $pdf->writeHTML($html, true, false, true, false, '');
        ob_end_clean();
        $pdf->Output('gst_report.pdf', 'I');
    
    }
    //create_gst_report_summary
    function create_gst_report_summary(){
        global $ocio_config;
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('GST Report');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('times', '', 10);
        $CReportManager = new CReportManager();
        global $AppUI,$CSalesManager,$CInvoiceManager,$CPOManager,$po_invoice,$CPaymentManager;
        $customer_value_arr =false ;
        $from_date = false;
        $to_date =false;
        $from_date_fomart ="";
        $to_date_fomart ="";
      
        if(isset($_GET['customer_id_arr']) && ($_GET['customer_id_arr'])!=null)
            $customer_value_arr = "(0".$_GET['customer_id_arr'].")";
        if(isset($_GET['from_date']) && $_GET['from_date']!=""){
            $from_date = $_GET['from_date'];
            $from_date_fomart = 'From: '.date($ocio_config['php_abms_date_format'],strtotime($from_date));
        }
        
        if(isset($_GET['to_date']) && $_GET['to_date']!=""){
            $to_date = $_GET['to_date'];
            $to_date_fomart = 'To: '.date($ocio_config['php_abms_date_format'],strtotime($to_date));
        }
        if($_GET['to_date']=="" && $_GET['from_date']!=""){
            $to_date_fomart = 'To: '.date($ocio_config['php_abms_date_format'],strtotime(date('d.M.Y')));
        }
        if($_GET['to_date']=="" && $_GET['from_date']==""){
            $date_fomat = "";
        }  else {
             $date_fomat='<tr valign="top">
                    <td colspan="4" align="center">'.$from_date_fomart.' &nbsp;&nbsp;&nbsp;'.$to_date_fomart.'</td>
                </tr>';
        }
        
        //$html .= '<tr><td colspan="4"><b>Sales Invoice GST Report</b></td></tr>';
        $gst_report_arr = $CReportManager->get_gst_report($customer_value_arr,$from_date,$to_date);
        //$invoice_revision_tax = $gst_report_row['invoice_revision_tax'];
        
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        $CTax = new CTax();
         $html ='<table width="100%"  callpadding="3" cellspacing="4" id="aging_report" class="tbl" >
                <tr><td align="right" colspan="4">'.date($ocio_config['php_abms_date_format'],strtotime(date('d.M.Y'))).'</td></tr>
                <tr>
                    <td colspan="7" align="center" style="font-size:1.6em">GST Report Summary</td>
                </tr>
                '.$date_fomat.'<br /><br />';
                     
          
                foreach ($gst_report_arr as $gst_report_row) {
                    $invoice_id = $gst_report_row['invoice_id'];
        //            $invoice_revision_id = $gst_report_row['invoice_revision_id'];
        //
        //            $tax_id = $gst_report_row['invoice_revision_tax'];
                    $invoice_revision_arr = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
                    $invoice_revision_id = $invoice_revision_arr[0]['invoice_revision_id'];

                    $tax_id = $invoice_revision_arr[0]['invoice_revision_tax'];
                    $tax_arr = $CTax->get_tax($tax_id);
                    $tax_rate = $tax_arr[0]['tax_rate'];

                    $total_item = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
                    $total_item_last_discount = $CInvoiceManager->get_invoice_total_last_discount($invoice_id, $invoice_revision_id);
                    $tax = $total_item_last_discount*$tax_rate/100;
                    if($invoice_revision_arr[0]['invoice_revision_tax_edit']!=0)
                        $tax = $invoice_revision_arr[0]['invoice_revision_tax_edit'];
                    $tax = $CSalesManager->round_up($tax);
                    $total_tax+=$tax;
                    $date = date('d/M/Y', strtotime($gst_report_row['invoice_date']));

                };
                $invoice_purchase_arr = array();
                $i=0;
                foreach ($gst_report_arr as $gst_report_row){

                    $invoice_id = $gst_report_row['invoice_id'];
                        $invoice_revision_arr = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
                        $invoice_revision_id = $invoice_revision_arr[0]['invoice_revision_id'];
                        $invoice_revision_discount=$invoice_revision_arr[0]['invoice_revision_discount'];
                        $tax_id = $invoice_revision_arr[0]['invoice_revision_tax'];
                        $tax_arr = $CTax->get_tax($tax_id);
                        $tax_rate = $tax_arr[0]['tax_rate'];

                        $total_item = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
                        $total_item_last_discount = $CInvoiceManager->get_invoice_total_last_discount($invoice_id, $invoice_revision_id,$total_item,$invoice_revision_discount);
                        $tax = $total_item_last_discount*($tax_rate/100);
                        if($invoice_revision_arr[0]['invoice_revision_tax_edit']!=0)
                            $tax = $invoice_revision_arr[0]['invoice_revision_tax_edit'];

                        //insert by Nhu
                        $tax_rate_edit=$invoice_revision_arr[0]['invoice_revision_tax_edit'];
                        $tax_rate_value=$invoice_revision_arr[0]['tax_rate'];
                    $date = date('d/M/Y', strtotime($gst_report_row['invoice_date']));

                    $invoice_purchase_arr[$i]['date'] =  $date;
                    $invoice_purchase_arr[$i]['number_no']=$gst_report_row['invoice_no'];
                    $invoice_purchase_arr[$i]['customer_name']=$gst_report_row['company_name'];
                    $invoice_purchase_arr[$i]['invoice_value']=$total_item_last_discount;
                    $invoice_purchase_arr[$i]['gst_collected']=$tax;
                    $invoice_purchase_arr[$i]['total_amount']=  $CInvoiceManager->calculateTotalInvoice($invoice_id);
                    $value_payment = $CPaymentManager->get_total_payment($invoice_revision_id);
                    if($value_payment == 0)
                    {
                        $invoice_purchase_arr[$i]['value_collected']=  0;
                        $invoice_purchase_arr[$i]['value_gst']=  0;
                    }
                    else
                    {
                        $invoice_purchase_arr[$i]['value_gst']=  $tax;
                        $invoice_purchase_arr[$i]['value_collected']=  $value_payment-$tax;

                    }
                    $invoice_purchase_arr[$i]['gst_paid']=0;
                    $i++;
                }
                
                $total_gst_colleted= 0;$total_gst_paid_invoice=0;$total_invoice_value=0;$total_purchase_value=0;
                $total_payment_collected = 0;$value_gst_collected=0;
              //  $invoice_purchase_arr = $CInvoiceManager->array_sort($invoice_purchase_arr, 'date');
                foreach ($invoice_purchase_arr as $value_item) {
                    $total_gst_colleted+=$value_item['gst_collected'];
                    $value_gst_collected += $value_item['value_gst'];
                    $total_gst_paid_invoice+=$value_item['total_amount'];
                    $total_invoice_value += $value_item['invoice_value'];
                    $total_purchase_value += $value_item['purchase_value'];
                    $total_payment_collected +=$value_item['value_collected'];

                  
                }


                
                
                //    /*============ GST PURCHASE NO ============ */
               
               
                $CPOPayment = new CPOPayment();
                $invoice_supplier_arr=array();
                $purchase_supplier_arr = $po_invoice->getListPo(false,$from_date,$to_date,false,$customer_value_arr);
                $poPayment = new CPOPayment();
                $total_purchase_payment = 0;
                $total_gst_po_Paid = 0;
                $total_gst_po_payable=0;
                foreach ($purchase_supplier_arr as $purchase_item) {
                    if($purchase_item['GST_Registered'] == 1){
                    $date = date('d/M/Y', strtotime($purchase_item['po_date']));
                    $po_invoice_id = $purchase_item['po_invoice_id'];
                    $total_purchase_payment += $poPayment->getTotalSIPayment($po_invoice_id);
                    if($poPayment->getTotalSIPayment($po_invoice_id) > 0)
                        $total_gst_po_Paid +=$po_invoice->calculateTotalGSTByPoInv($po_invoice_id);
                    else {
                        $total_gst_po_payable +=$po_invoice->calculateTotalGSTByPoInv($po_invoice_id);;
                    }
                    $invoice_supplier_arr[$i]['date'] =  $date;
                    $invoice_supplier_arr[$i]['number_no']=$purchase_item['po_invoice_no'];
                    $invoice_supplier_arr[$i]['customer_name']=$purchase_item['company_name'];
                    $invoice_supplier_arr[$i]['invoice_value']=0;
                    $invoice_supplier_arr[$i]['gst_collected']=0;
                    $invoice_supplier_arr[$i]['purchase_value']=$po_invoice->calculateTotalNoTaxPo($po_invoice_id);
                    $invoice_supplier_arr[$i]['gst_paid']=$po_invoice->calculateTotalGSTByPoInv($po_invoice_id);
                    $invoice_supplier_arr[$i]['Value_Paid']=$poPayment->getTotalSIPayment($po_invoice_id);

                    $payment_invoice_supplier =$CPOPayment->getTotalSIPayment($po_invoice_id,false);
                    $invoice_supplier_arr[$i]['total_amount']=$po_invoice->getTotalPoLastPayment($po_invoice_id)+$po_invoice->getTotalPaymentByPo($po_invoice_id);
                    if($payment_invoice_supplier > 0)
                        $invoice_supplier_arr[$i]['gst_supplier']=$invoice_supplier_arr[$i]['gst_paid'];

                    $i++;
                    }
                }
               

//                    $invoice_supplier_arr = $CInvoiceManager->array_sort($invoice_supplier_arr, 'date');
                    
                    $total_purchase_value=0;
                    $total_gst_paid=0;
                    $total_amount = 0;
                    $gst_supplier = 0;
                    $gst_paid = 0;

                    foreach ($invoice_supplier_arr as $value_item) {
                        $total_purchase_value +=$value_item['purchase_value'];
                        $total_gst_paid +=$value_item['gst_paid'];
                        $total_amount += $value_item['total_amount'];
                        $paid = 0;
                        if($value_item['total_amount'] > 0){
                                $paid = $value_item['gst_paid'];
                                $gst_paid +=$paid;
                        }   
                        else
                                $paid = 0;
                       
                    }


                    if(count($invoice_purchase_arr)==0){
                        $html .= '<tr id="title_body"><td colspan="4">No GST Report Found</td></tr>';
                    }
                
      
                //Expenses GST Report
    $ExpensesSupplierManage  = new ExpensesSupplierManage();
    $expensesManage = new ExpensesManager();
    $arr_customer = $_POST['customer_id_arr'];
   
    if($arr_customer != "")
    {
     
        $ex_arr = $ExpensesSupplierManage->getExByCustomer($customer_value_arr);
        
    }
    else
    {
        
        $ex_arr = $expensesManage->getExpensesByExId($data['expenses_id'],$from_date,$to_date);
    }
//    
    $exTaxManage = new Expenses_tax_manage();
    $expensesGST=array();
    $j=0;
    global $ExpensesCategory;
    $total_ex_payable=0;
    $category_arr = dPgetSysVal('Categories');
    
    foreach ($ex_arr as $data)
    {
       
        $ex = $expensesManage->getExpensesByExId($data['expenses_id'],$from_date,$to_date);
       
        
        $ex_ca = $ExpensesCategory->getAllCategoryByEx1($data['expenses_id']);
//        if($data['expenses_no'] == 'PC150062')
//            {
//            echo '<pre>';
//            print_r($ex_ca);
//            }
        $total_ex_amount=0;
        $gst=0;
        $category_value="";
        
        foreach ($ex_ca as $category)
        {
            
            
            if($category['gst_Claimable'] == 1){
           
            $information_ex = $expensesManage->getExpensesByExId($category['expenses_id']);

            $total_ex_amount +=$category['expenses_category_amount'];

            $total_ex_payable += $expensesManage->calculateTotalTaxExpense($category['expenses_id']);
            
            $category_value .=$category_arr[$category['category_id']].'<br/>';
            $expensesGST[$j]['Amount'] =$category['expenses_amount'];
            
            $gst +=$exTaxManage->getGSTByEx($category['expenses_category_id']);
            
            
            }

        
        }
        $expensesGST[$j]['date']=$ex_ca[0]['expenses_date'];
        $expensesGST[$j]['expenses_no']=$information_ex[0]['expenses_no'];
        $expensesGST[$j]['category_id']=$category_value;
        $expensesGST[$j]['GST']=$gst;
        $expensesGST[$j]['value'] =$total_ex_amount;
        $j++;
        
    }

    $expenses_value=0;
    $gst_total_ex=0;
    $gst_amount_ex=0;
    $gst_Receivable_GST=0;
    foreach ($expensesGST as $value_ex) {
            
            if($value_ex['expenses_no'] != ""){
            $expenses_value +=$value_ex['value'];
            $gst_total_ex +=$value_ex['GST'];
            $gst_amount_ex += $value_ex['Amount'];
            $gst_Receivable_GST += $value_ex['Receivable_GST'];
         
            }
        }


    //     GST Report Summary
    $sale_collected = $total_payment_collected+$value_gst_collected;
    $sale_Recievable= $total_invoice_value-$total_payment_collected;
    $infocredit_amount=$CSalesManager->info_amount_all_credit_note(false,$from_date,$to_date);
    
    $amount_credit=$infocredit_amount['amount'];
    $total_credit=$infocredit_amount['total'];
    $html .=  '<div id="gst_report_summmary">';
      $html .=  '<div style="margin-top:20px;margin-bottom:10px;"><b>Received / Payable GST Summary Report</b></div>';
       $html .=  '<table width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
            <thead >
                <tr id="title_head" height="20px" >
                    <td style="background-color:#dfeffc" align="left"height="30px" width="20%">Sales</td>
                    <td style="background-color:#dfeffc" align="left" width="20%"></td>
                    <td style="background-color:#dfeffc" align="left" width="20%"></td>
                    <td style="background-color:#dfeffc" align="right" width="40%"> </td>
                </tr>
            </thead>
            <tbody>';
        $html .=  '<tr>'
                . '<td align="left" width="20%">Total Sales Collected</td>'
                . '<td align="right" width="20%" height="30px">$'.number_format($total_payment_collected,2).'</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        $html .= '<tr>'
                . '<td align="left" width="20%" height="20px" >Total GST Collected</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="right" width="20%">$'.number_format($value_gst_collected,2).'</td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
       
       $html .=  '<tr>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        $html .=  '<tr id="title_head">
                    <td align="left" width="20%" style="background-color:#dfeffc">Purchases</td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                  
                    <td align="right" width="40%" style="background-color:#dfeffc"> </td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total Purchases Paid</td>
                    <td align="right" width="20%">$'.number_format($total_purchase_payment-$gst_paid,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                    
                
                </tr>';
        $html .=  '<tr>
                    <td align="left" width="20%">Total GST Paid</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format(($total_gst_paid-$gst_supplier),2).'</td>
                
                </tr>';
       
        $html .=  '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
        $html .=  '<tr id="title_head">
                    <td align="left" width="20%" style="background-color:#dfeffc">Expenses</td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                  
                    <td align="right" width="40%" style="background-color:#dfeffc"> </td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total Expenses Paid</td>
                    <td align="right" width="20%">$'.number_format(0,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                    
                
                </tr>';
        $html .=  '<tr>
                    <td align="left" width="20%">Total GST Paid</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format(0,2).'</td>
                
                </tr>';
       $html .= '<tr id="title_head">
                    <td align="left" width="20%" style="background-color:#dfeffc">Credit Note</td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                  
                    <td align="right" width="40%" style="background-color:#dfeffc"> </td>
                
                </tr>';
         $html .= '<tr>
                    <td align="left" width="20%">Amount</td>
                    <td align="right" width="20%">$'.number_format($amount_credit,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                    
                
                </tr>';
        $html .= '<tr>
                    <td align="left" width="20%">GST</td>
                    <td align="right" width="20%">$'.number_format($total_credit-$amount_credit,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="40%"></td>
                
                </tr>';
        $html .= '<tr>
                    <td align="left" width="20%">Total</td>
                    <td align="right" width="20%">$'.number_format($total_credit,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="40%"></td>
                
                </tr>';
        $html .=  '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
       
        $html .=     '</tbody>';
        $html .=  '<tfoot >';
         $html .=  '<tr >
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" colspan="3"><b>Total GST Output /Payable to IRAS</b></td>
                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="20%"></td>
                    
                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="20%">$'.number_format($value_gst_collected,2).'</td>
                  
                
                </tr>';
         
        $html .=  '<tr>
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000" colspan="4"><b>Total GST Input / Refundable from IRAS</b></td>
                    
                    
                    <td align="left"  width="40%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000"></td>
                   
                    <td align="right" width="40%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000"> $'.number_format(($total_gst_paid-$gst_supplier),2).'</td>
                
                </tr>';
        $html .=  '<tr>
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000" colspan="3"><b>Nett GST to be paid to IRAS</b></td>
                   
                    <td align="left"  width="40%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000"></td>

                   
                    <td align="right" width="40%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000">$'.number_format($value_gst_collected-($total_gst_paid-$gst_supplier),2).' </td>
                
                </tr>';
        
        $html .=  '</tfoot>';
            
        $html .=  '</table>';
        
        $html .=  '<br />';
        $html .=  '<br />';
        
        //Payable/Receivable GST Summary Report
      $html .=  '<div style="margin-top:20px;margin-bottom:10px;"><b>Total Value GST Summary Report</b></div>';
       $html .=  '<table width="100%" width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
            <thead >
                <tr id="title_head">
                    <td align="left" width="20%" style="background-color:#dfeffc">Sales</td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                    <td align="right" width="40%" style="background-color:#dfeffc"> </td>
                </tr>
            </thead>
            <tbody>';
        $html .=  '<tr>'
                . '<td align="left" width="20%">Total Sales Value</td>'
                . '<td align="right" width="20%">$'.number_format($sale_Recievable,2).'</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        $html .=  '<tr>'
                . '<td align="left" width="20%">Total GST Value</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="right" width="20%">$'.number_format($total_gst_colleted-$value_gst_collected,2).'</td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        
        $html .=  '<tr>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        $html .=  '<tr id="title_head">
                    <td align="left" width="20%" style="background-color:#dfeffc">Purchases</td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                  
                    <td align="right" width="40%"> </td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total Payable Purchases\'s</td>
                    <td align="RIGHT" width="20%">$'.number_format(($total_amount-$total_purchase_payment),2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total GST Payable</td>
                    <td align="right" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format($total_gst_paid-$gst_paid,2).'</td>
                    
                
                </tr>';
       
       
        $html .=  '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
        $html .=  '<tr id="title_head">
                    <td align="left" width="20%" style="background-color:#dfeffc">Expenses</td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                    <td align="left" width="20%" style="background-color:#dfeffc"></td>
                  
                    <td align="right" width="40%" style="background-color:#dfeffc"> </td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total Payable Expenses</td>
                    <td align="RIGHT" width="20%">$'.number_format($expenses_value,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                
                </tr>';
         $html .=  '<tr>
                    <td align="left" width="20%">Total GST Payable</td>
                    <td align="right" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format($gst_total_ex,2).'</td>
                    
                
                </tr>';
       
       
        $html .=  '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
       
        $html .=     '</tbody>';
        $html .=  '<tfoot >';
         $html .=  '<tr >
                     <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" colspan="2"><b>Total GST Output /Payable to IRAS</b></td>

                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="20%"></td>
                    
                    <td align="right" width="20%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000">$'.number_format($total_gst_colleted-$value_gst_collected,2).'</td>
                
                </tr>';
        $html .=  '<tr>
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000" colspan="3"><b>Total GST Input / Refundable from IRAS</b></td>
                   
                    <td align="left"  width="40%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000"></td>

                   
                    <td align="right" width="40%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000">$'.number_format($total_gst_paid-$gst_paid+$gst_total_ex,2).' </td>
                
                </tr>';
        $html .=  '<tr>
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000" colspan="3"><b>Nett GST to be paid to IRAS</b></td>
                   
                    <td align="left"  width="40%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000"></td>

                   
                    <td align="right" width="40%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000">$'.number_format($total_gst_colleted-$value_gst_collected-($total_gst_paid-$gst_paid+$gst_total_ex),2).' </td>
                
                </tr>';
        
        $html .=  '</tfoot>';
            
        $html .=  '</table>';
        
        $html .=  '<br />';
        $html .=  '<br />';
       
        
        $pdf->AddPage();              
        $pdf->writeHTML($html, true, false, true, false, '');
        ob_end_clean();
        $pdf->Output('gst_report.pdf', 'I');
    
    }
    
    //gst report
    function create_gst_report(){
        global $ocio_config;
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('GST Report');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
       $pdf->SetMargins(20, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('times', '', 10);
        $CReportManager = new CReportManager();
        global $AppUI,$CSalesManager,$CInvoiceManager,$CPOManager,$po_invoice,$CPaymentManager,$ExpensesCategory;
        $customer_value_arr =false ;
        $from_date = false;
        $to_date =false;
        $from_date_fomart ="";
        $to_date_fomart ="";
      
        if(isset($_GET['customer_id_arr']) && ($_GET['customer_id_arr'])!=null)
            $customer_value_arr = "(0".$_GET['customer_id_arr'].")";
        if(isset($_GET['from_date']) && $_GET['from_date']!=""){
            $from_date = $_GET['from_date'];
            $from_date_fomart = 'From: '.date($ocio_config['php_abms_date_format'],strtotime($from_date));
        }
        
        if(isset($_GET['to_date']) && $_GET['to_date']!=""){
            $to_date = $_GET['to_date'];
            $to_date_fomart = 'To: '.date($ocio_config['php_abms_date_format'],strtotime($to_date));
        }
        if($_GET['to_date']=="" && $_GET['from_date']!=""){
            $to_date_fomart = 'To: '.date($ocio_config['php_abms_date_format'],strtotime(date('d.M.Y')));
        }
        if($_GET['to_date']=="" && $_GET['from_date']==""){
            $date_fomat = "";
        }  else {
             $date_fomat='<tr valign="top">
                    <td colspan="4" align="center">'.$from_date_fomart.' &nbsp;&nbsp;&nbsp;'.$to_date_fomart.'</td>
                </tr>';
        }
        
        //$html .= '<tr><td colspan="4"><b>Sales Invoice GST Report</b></td></tr>';
        $gst_report_arr = $CReportManager->get_gst_report($customer_value_arr,$from_date,$to_date);
        //$invoice_revision_tax = $gst_report_row['invoice_revision_tax'];
        
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        $CTax = new CTax();
         $html ='<table width="100%"  callpadding="3" cellspacing="4" id="aging_report" class="tbl" >
                <tr><td align="right" colspan="4">'.date($ocio_config['php_abms_date_format'],strtotime(date('d.M.Y'))).'</td></tr>
                <tr>
                    <td colspan="7" align="center" style="font-size:1.6em">GST Report</td>
                </tr>
                '.$date_fomat.'<br /><br />
                     <tr><td style="margin-top:20px;margin-bottom:10px;"><b>Sales Invoice GST Report</b></td></tr></table>';
          $html .='<table width="100%"  border="1" callpadding="3" cellspacing="0" id="aging_report" class="tbl" >
                
                <thead>
                    <tr style="font-weight:bold;" height="20px" style="background-color:#dfeffc">
                        <td align="left" height="20px" width="10%"><b>Date</b></td>
                        <td align="left" width="10%"><b>Invoice Nos</b></td>
                        <td align="left" width="30%"><b>Customer</b></td>
                        <td align="right" width="10%"><b>Value</b></td>
                        <td align="right" width="10%"><b>GST</b></td>
                        <td align="right" width="10%"><b>Total Amount</b></td>
                        <td align="right" width="10%"><b>Value Collected</b></td>
                        <td align="right" width="10%"><b>GST Collected</b></td>
                        
                    </tr>
                </thead>';
                foreach ($gst_report_arr as $gst_report_row) {
                    $invoice_id = $gst_report_row['invoice_id'];
        //            $invoice_revision_id = $gst_report_row['invoice_revision_id'];
        //
        //            $tax_id = $gst_report_row['invoice_revision_tax'];
                    $invoice_revision_arr = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
                    $invoice_revision_id = $invoice_revision_arr[0]['invoice_revision_id'];

                    $tax_id = $invoice_revision_arr[0]['invoice_revision_tax'];
                    $tax_arr = $CTax->get_tax($tax_id);
                    $tax_rate = $tax_arr[0]['tax_rate'];

                    $total_item = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
                    $total_item_last_discount = $CInvoiceManager->get_invoice_total_last_discount($invoice_id, $invoice_revision_id);
                    $tax = $total_item_last_discount*$tax_rate/100;
                    if($invoice_revision_arr[0]['invoice_revision_tax_edit']!=0)
                        $tax = $invoice_revision_arr[0]['invoice_revision_tax_edit'];
                    $tax = $CSalesManager->round_up($tax);
                    $total_tax+=$tax;
                    $date = date($ocio_config['php_abms_date_format'], strtotime($gst_report_row['invoice_date']));

                };
                $invoice_purchase_arr = array();
                $i=0;
                foreach ($gst_report_arr as $gst_report_row){

                    $invoice_id = $gst_report_row['invoice_id'];
                    $invoice_revision_arr = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
                    $invoice_revision_id = $invoice_revision_arr[0]['invoice_revision_id'];

                    $tax_id = $invoice_revision_arr[0]['invoice_revision_tax'];
                    $tax_arr = $CTax->get_tax($tax_id);
                    $tax_rate = $tax_arr[0]['tax_rate'];

                    $total_item = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
                    $total_item_last_discount = $CInvoiceManager->get_invoice_total_last_discount($invoice_id, $invoice_revision_id);
                    $tax = $total_item_last_discount*($tax_rate/100);
                    if($invoice_revision_arr[0]['invoice_revision_tax_edit']!=0)
                        $tax = $invoice_revision_arr[0]['invoice_revision_tax_edit'];
                    //$tax = $CSalesManager->round_up($tax);
                    $date = date($ocio_config['php_abms_date_format'], strtotime($gst_report_row['invoice_date']));

                    $invoice_purchase_arr[$i]['date'] =  $date;
                    $invoice_purchase_arr[$i]['number_no']=$gst_report_row['invoice_no'];
                    $invoice_purchase_arr[$i]['customer_name']=$gst_report_row['company_name'];
                    $invoice_purchase_arr[$i]['invoice_value']=$total_item_last_discount;
                    $invoice_purchase_arr[$i]['gst_collected']=$tax;
                    $invoice_purchase_arr[$i]['total_amount']=  $CInvoiceManager->calculateTotalInvoice($invoice_id);
                    $value_payment = $CPaymentManager->get_total_payment($invoice_revision_id);
                    if($value_payment == 0)
                    {
                        $invoice_purchase_arr[$i]['value_collected']=  0;
                        $invoice_purchase_arr[$i]['value_gst']=  0;
                    }
                    else
                    {
                        $invoice_purchase_arr[$i]['value_gst']=  $tax;
                        $invoice_purchase_arr[$i]['value_collected']=  $value_payment-$tax;

                    }
                    $invoice_purchase_arr[$i]['gst_paid']=0;
                    $i++;
                }
                
                $total_gst_colleted= 0;$total_gst_paid_invoice=0;$total_invoice_value=0;$total_purchase_value=0;
                $total_payment_collected = 0;$value_gst_collected=0;
//                $invoice_purchase_arr = $CInvoiceManager->array_sort($invoice_purchase_arr, 'date');
                foreach ($invoice_purchase_arr as $value_item) {
                    $total_gst_colleted+=$value_item['gst_collected'];
                    $value_gst_collected += $value_item['value_gst'];
                    $total_gst_paid_invoice+=$value_item['total_amount'];
                    $total_invoice_value += $value_item['invoice_value'];
                    $total_purchase_value += $value_item['purchase_value'];
                    $total_payment_collected +=$value_item['value_collected'];

                    $html .=   '<tr id="title_body">
                                <td width="10%" height="30px">'.$value_item['date'].'</td>
                                <td width="10%">'.$value_item['number_no'].'</td>
                                <td width="30%">'.$value_item['customer_name'].'</td>
                                <td width="10%" align="right">$'.number_format($value_item['invoice_value'],2).'</td>
                                <td width="10%" align="right">$'.number_format($value_item['gst_collected'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_item['total_amount'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_item['value_collected'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_item['value_gst'],2).'</td>
                            </tr>';
                }


                if(count($invoice_purchase_arr)==0){
                    $html .= '<tr id="title_body"><td colspan="4">No GST Report Found</td></tr>';
                }
                $html .=    '</tbody>
                        <tfoot >
                            <tr id="title_body" height="20px" style="background-color:#dfeffc">
                                <td colspan="3" height="30px"><b>Total</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_invoice_value,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_gst_colleted,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_gst_paid_invoice,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_payment_collected,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($value_gst_collected,2).'</b></td>
                            </tr>
                        </tfoot>

                    </table><br /><br />';
                
                //    /*============ GST PURCHASE NO ============ */
                $html .='<div style="margin-top:40px;margin-bottom:30px;"><b>Purchased Order (PO) GST</b></div>';
                $html .='<br />';
                $CPOPayment = new CPOPayment();
                $invoice_supplier_arr=array();
                $purchase_supplier_arr = $po_invoice->getListPo(false,$from_date,$to_date,false,$customer_value_arr);
                $poPayment = new CPOPayment();
                $total_purchase_payment = 0;
                $total_gst_po_Paid = 0;
                $total_gst_po_payable=0;
                foreach ($purchase_supplier_arr as $purchase_item) {
                    if($purchase_item['GST_Registered'] == 1){
                    $date = date($ocio_config['php_abms_date_format'], strtotime($purchase_item['po_date']));
                    $po_invoice_id = $purchase_item['po_invoice_id'];
                    $total_purchase_payment += $poPayment->getTotalSIPayment($po_invoice_id);
                    if($poPayment->getTotalSIPayment($po_invoice_id) > 0)
                        $total_gst_po_Paid +=$po_invoice->calculateTotalGSTByPoInv($po_invoice_id);
                    else {
                        $total_gst_po_payable +=$po_invoice->calculateTotalGSTByPoInv($po_invoice_id);;
                    }
                    $invoice_supplier_arr[$i]['date'] =  $date;
                    $invoice_supplier_arr[$i]['number_no']=$purchase_item['po_invoice_no'];
                    $invoice_supplier_arr[$i]['customer_name']=$purchase_item['company_name'];
                    $invoice_supplier_arr[$i]['invoice_value']=0;
                    $invoice_supplier_arr[$i]['gst_collected']=0;
                    $invoice_supplier_arr[$i]['purchase_value']=$po_invoice->calculateTotalNoTaxPo($po_invoice_id);
                    $invoice_supplier_arr[$i]['gst_paid']=$po_invoice->calculateTotalGSTByPoInv($po_invoice_id);
                    $invoice_supplier_arr[$i]['Value_Paid']=$poPayment->getTotalSIPayment($po_invoice_id);

                    $payment_invoice_supplier =$CPOPayment->getTotalSIPayment($po_invoice_id,false);
                    $invoice_supplier_arr[$i]['total_amount']=$po_invoice->getTotalPoLastPayment($po_invoice_id)+$po_invoice->getTotalPaymentByPo($po_invoice_id);
                    if($payment_invoice_supplier > 0)
                        $invoice_supplier_arr[$i]['gst_supplier']=$invoice_supplier_arr[$i]['gst_paid'];

                    $i++;
                    }
                }
                $html .= '<table width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
                        <thead >
                            <tr id="title_head" height="20px" style="background-color:#dfeffc">
                                <td align="left" width="10%"><b>Date</b></td>
                                <td align="left" width="10%"><b>Invoice Nos</b></td>
                                <td align="left" width="30%"><b>Customer</b></td>
                                <td align="right" width="10%"><b>Value</b></td>
                                <td align="right" width="10%"><b>GST </b></td>
                                <td align="right" width="10%"><b>Total Amount</b></td>
                                <td align="right" width="10%"><b>Value Paid</b></td>
                                <td align="right" width="10%"><b>Payable GST</b></td>

                            </tr>
                        </thead>
                        <tbody>';

                    //$invoice_supplier_arr = $CInvoiceManager->array_sort($invoice_supplier_arr, 'date');
                    
                    $total_purchase_value=0;
                    $total_gst_paid=0;
                    $total_amount = 0;
                    $gst_supplier = 0;
                    $gst_paid = 0;

                    foreach ($invoice_supplier_arr as $value_item) {
                        $total_purchase_value +=$value_item['purchase_value'];
                        $total_gst_paid +=$value_item['gst_paid'];
                        $total_amount += $value_item['total_amount'];
                        $paid = 0;
                        if($value_item['total_amount'] > 0){
                                $paid = $value_item['gst_paid'];
                                $gst_paid +=$paid;
                        }   
                        else
                                $paid = 0;
                        $html .=   '<tr id="title_body">
                                    <td height="30px" width="10%">'.$value_item['date'].'</td>
                                    <td width="10%">'.$value_item['number_no'].'</td>
                                    <td width="30%"> '.$value_item['customer_name'].'</td>
                                    <td align="right" width="10%">$'.number_format($value_item['purchase_value'],2).'</td>
                                    <td align="right" width="10%">$'.number_format($value_item['gst_paid'],2).'</td>
                                    <td align="right" width="10%">$'.number_format($value_item['total_amount'],2).'</td>
                                    <td align="right" width="10%">$'.number_format(($value_item['Value_Paid']-$paid),2).'</td>
                                    <td align="right" width="10%">';
//                                    if($value_item['total_amount'] > 0){
//                                        $html .=  '$'.number_format(0,2);
//                                    }   
//                                else
                                     $html .=  '$'.number_format($value_item['gst_paid'],2);
                               $html .= '</td></tr>';
                               if($value_item['total_amount'] > 0)
                                    $gst_supplier += $value_item['gst_paid'] - $paid;
                    }


                    if(count($invoice_purchase_arr)==0){
                        $html .= '<tr id="title_body"><td colspan="4">No GST Report Found</td></tr>';
                    }
                $html .=    '</tbody>
                        <tfoot >
                            <tr id="title_body" height="20px" style="background-color:#dfeffc">
                                <td colspan="3" height="30px"><b>Total</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_purchase_value,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_gst_paid,2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format(($total_amount-$gst_supplier),2).'</b></td>
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format(($total_purchase_payment-$gst_paid),2).'</b></td>
                                
                                <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_gst_paid,2).'</b></td>
                            </tr>
                        </tfoot>

                   </table><br /><br />';
      
                //Expenses GST Report
    $html .= '<div style="margin-top:20px;margin-bottom:10px;"><b>Expenses GST Report</b></div><br />';
    $ExpensesSupplierManage  = new ExpensesSupplierManage();
    $expensesManage = new ExpensesManager();
    $arr_customer = $_REQUEST['customer_id_arr'];
   
    if($arr_customer != "")
    {
     
        $ex_arr = $ExpensesSupplierManage->getExByCustomer($customer_value_arr);
        
    }
    else
    {
        
        $ex_arr = $expensesManage->getExpensesByExId($data['expenses_id'],$from_date,$to_date);
    }
    
    $exTaxManage = new Expenses_tax_manage();
    $expensesGST=array();
    $j=0;
    
    $total_ex_payable=0;
    $category_arr = dPgetSysVal('Categories');
    foreach ($ex_arr as $data)
    {
       $ex = $expensesManage->getExpensesByExId($data['expenses_id'],$from_date,$to_date);
       
        
        $ex_ca = $ExpensesCategory->getAllCategoryByEx1($data['expenses_id']);
       
        $total_ex_amount=0;
        $gst=0;
        $category_value="";
       
        foreach ($ex_ca as $category)
        {
            
       
        if($category['gst_Claimable'] == 1){
            $information_ex = $expensesManage->getExpensesByExId($category['expenses_id']);
            
            $total_ex_amount +=$category['expenses_category_amount'];

            $total_ex_payable += $expensesManage->calculateTotalTaxExpense($category['expenses_id']);

            $category_value .=$category_arr[$category['category_id']].'<br/>';
            $expensesGST[$j]['Amount'] =$category['expenses_amount'];
            
            $gst +=$exTaxManage->getGSTByEx($category['expenses_category_id']);
            }
            
            
        }
        $expensesGST[$j]['date']=$ex_ca[0]['expenses_date'];
        $expensesGST[$j]['expenses_no']=$data['expenses_no'];
        $expensesGST[$j]['category_id']=$category_value;
        $expensesGST[$j]['GST']=$gst;
        $expensesGST[$j]['value'] =$total_ex_amount;
        $j++;
     
 
    }
//    echo '<pre>';
//    print_r($expensesGST);
            $html .= '<table width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
                    <thead >
                        <tr id="title_head" height="20px" style="background-color:#dfeffc">
                            <td align="left" height="30px" width="10%"><b>Date</b></td>
                            <td align="left" width="10%"><b>Expenses Nos.</b></td>
                            <td align="left" width="30%"><b>Category</b></td>
                            <td align="right" width="10%"><b>Value</b></td>
                            <td align="right" width="10%"><b>GST </b></td>
                            <td align="right" width="10%"><b>Total Amount</b></td>
                            <td align="right" width="10%"><b>Value Paid</b></td>
                            <td align="right" width="10%"><b>Payable GST</b></td>

                        </tr>
                    </thead>
                    <tbody>';
           
            $expenses_value=0;
            $gst_total_ex=0;
            $gst_amount_ex=0;
            $gst_Receivable_GST=0;
            foreach ($expensesGST as $value_ex) {

                    if($value_ex['expenses_no'] != ""){
                    $expenses_value +=$value_ex['value'];
                    $gst_total_ex +=$value_ex['GST'];
                    $gst_amount_ex += $value_ex['Amount'];
                    $gst_Receivable_GST += $value_ex['Receivable_GST'];
                    $html .=   '<tr id="title_body">
                                <td width="10%">'.date($ocio_config['php_abms_date_format'],strtotime($value_ex['date'])).'</td>
                                <td width="10%">'.$value_ex['expenses_no'].'</td>
                                <td width="30%">'.$value_ex['category_id'].'</td>
                                <td align="right" width="10%">$'.number_format($value_ex['value'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_ex['GST'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_ex['Amount'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_ex['value'],2).'</td>
                                <td align="right" width="10%">$'.number_format($value_ex['GST'],2).'</td>

                            </tr>';
                    }
                }
        //        if(count($expensesGST)==0){
        //            echo '<tr id="title_body"><td colspan="4">No GST Report Found</td></tr>';
        //        }

             $html .=    '</tbody>
                    <tfoot >
                        <tr id="title_body" height="20px" style="background-color:#dfeffc">
                            <td colspan="3" height="30px"><b>Total</b></td>
                            <td align="right" style="border-top:1px solid #000"><b>$'.number_format($expenses_value,2).'</b></td>
                            <td align="right" style="border-top:1px solid #000"><b>$'.number_format($gst_total_ex,2).'</b></td>
                            <td align="right" style="border-top:1px solid #000"><b>$'.number_format($gst_amount_ex,2).'</b></td>
                            <td align="right" style="border-top:1px solid #000"><b>$'.number_format($expenses_value,2).'</b></td>
                            <td align="right" style="border-top:1px solid #000"><b>$'.number_format($gst_total_ex,2).'</b></td>
                        </tr>
                    </tfoot>

                </table>';
         

        $html .= '<div style="margin-top:20px;margin-bottom:10px;"><b></b></div><br />';
        $html .= '<table width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
                    <thead >
                        <tr id="title_head" height="20px" style="background-color:#dfeffc">
                            <td align="left" height="30px" width="20%"><b>Credit Note</b></td>
                            <td align="left" width="30%"><b>Customer</b></td>
                            <td align="left" width="15%"><b>Date</b></td>
                            <td align="right" width="10%"><b>Invoice No</b></td>
                            <td align="right" width="15%"><b>Total Amount </b></td>
                            <td align="right" width="10%"><b>Status</b></td>
                            

                        </tr>
                    </thead>
                    <tbody>';
        $credit_status =  dPgetSysVal('CreditStatus');
        $CCreditManager=new CCreditNoteManager();
        $creditNote_arr = $CCreditManager->list_db_credit($arr_customer,$from_date,$to_date);
        foreach ($creditNote_arr as $creditNote_row){
                $status = $creditNote_row['credit_note_status'];
                $customer_err = $CCreditManager->customer_by_creditNote($creditNote_row['customer_id']);
                $invoice_id = $creditNote_row['invoice_id'];
                $invoice_manage = new CInvoiceManager();
                $invocie = $invoice_manage->get_db_invoice($invoice_id);
                $invoice_no = $invocie[0]['invoice_no'];

                $html .='<tr>';

                $html .='<td height="30px" width="20%">'.$creditNote_row['credit_note_no'].'</td>';
                $html .='<td width="30%">'.$customer_err[0]['company_name'].'</td>';
                $html .='<td width="15%">'.date('d/m/Y', strtotime($creditNote_row['credit_note_date'])).'</td>';
                $html .='<td width="10%">'.$invoice_no.'</td>';
                $html .='<td width="15%">$'.number_format($CSalesManager->total_creditNote_amount_and_tax($creditNote_row['credit_note_id']),2).'</td>';
                $html .='<td width="10%" align="right">'.$credit_status[$status].'</td>';
                $html .='</tr>';;
                
        }
        $html .='</tbody></table>';
        $pdf->AddPage();              
        $pdf->writeHTML($html, true, false, true, false, '');
        ob_end_clean();
        $pdf->Output('gst_report.pdf', 'I');
    
    }
    function get_payment_sales_by_term_invoice($term_cash=false,$customer_id_arr=false,$from_date=false,$to_date=false){
        $q=new DBQuery;
        $q->addTable('sales_invoice','tbl2');
        $q->addQuery('tbl2.invoice_date,tbl2.invoice_id,tbl2.invoice_no,tbl2.term,tbl3.company_name,tbl3.company_id,tbl4.credit_note_id');
        $q->addJoin('clients','tbl3','tbl3.company_id=tbl2.customer_id');
        $q->addJoin('sales_credit_note','tbl4', 'tbl2.invoice_id=tbl4.invoice_id');
        if($term_cash)
            $q->addWhere('tbl2.term = 7');
        else
            $q->addWhere('(tbl2.term <> 7 OR tbl2.term is NULL)');
        if($customer_id_arr)
            $q->addWhere ('tbl2.customer_id IN '.$customer_id_arr);
        if($from_date)
            $q->addWhere ('tbl2.invoice_date >="'.$from_date.'"');
        if($to_date)
            $q->addWhere ('tbl2.invoice_date <="'.$to_date.'"');
        $q->addOrder('tbl2.invoice_date, tbl2.invoice_no ASC');
        $q->addGroup('invoice_id');
        return $q->loadList();
    }
    function create_sales_report_pdf(){
        global $ocio_config;
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Sales Report');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('times', '', 10);
        
        global $AppUI, $CReportManager,$CInvoiceManager,$CPaymentManager,$CSalesManager;
        

        $customer_value_arr = false;
        $from_date = false;
        $to_date =false;
        $from_date_fomart ="";
        $to_date_fomart ="";
        if(isset($_GET['customer_id_arr']) && $_GET['customer_id_arr']!="")
            $customer_value_arr = "(0".$_GET['customer_id_arr'].")";
        if(isset($_GET['from_date']) && $_GET['from_date']!=""){
            $from_date = $_GET['from_date'];
            $from_date_fomart = 'From: '.date($ocio_config['php_abms_date_format'],strtotime($from_date));
        }
        if(isset($_GET['to_date']) && $_GET['to_date']!=""){
            $to_date = $_GET['to_date'];
            $to_date_fomart = '&nbsp;&nbsp;&nbsp;To: '.date($ocio_config['php_abms_date_format'],strtotime($to_date));
        }
            
//        if(isset($_GET['to_date']) && $_GET['to_date']!=""){
//            $to_date = $_GET['to_date'];
//            $to_date_fomart = 'To: '.date('d-m-Y',strtotime($to_date));
//        }
//        if($_GET['to_date']=="" && $_GET['from_date']!=""){
//            $to_date_fomart = '&nbsp;&nbsp;&nbsp;To: '.date('d-m-Y');
//        }
//        if($_GET['to_date']=="" && $_GET['from_date']==""){
//            $date_fomat = "";
//        }  else {
//             $date_fomat='<tr valign="top">
//                    <td colspan="9" align="center">'.$from_date_fomart.' &nbsp;&nbsp;&nbsp;'.$to_date_fomart.'</td>
//                </tr>';
//        }
        
        $invoice_sales_arr = $CReportManager->get_payment_sales_by_term_invoice(true,$customer_value_arr,$from_date,$to_date);
        $cash_sales_arr = $CReportManager->get_payment_sales_by_term_invoice(false,$customer_value_arr,$from_date,$to_date);
        $report_tyle = $_GET['report_type'];
        if($report_tyle==1){
            $display_invoice = "display: none;";
        }
        if($report_tyle==2){
            $display_cash = "display: none;";
        }

        //$invoice_sales_arr = $CReportManager->get_payment_sales_by_term_invoice(true,$customer_value_arr,$from_date,$to_date);
        //print_r($cash_sales_arr);
        $html= '<table  width="100%" border="0" callpadding="0" cellspacing="2"  >
                    <tr>
                        <td align="lert">'.$from_date_fomart.$to_date_fomart.'</td>
                        <td align="right">'.date($ocio_config['php_abms_date_format'],strtotime(date("d.M.Y"))).'</td></tr>
                    </table>';
        if(count($invoice_sales_arr)>0){
            $html.= '<table width="100%" border="0" style="'.$display_cash.'" cellpadding="0" cellspacing="4" id="aging_report" class="tbl">
                        <tr ><td colspan="9" style="height:35px; font-size:1.8em;" align="center">Cash Sales</td></tr>
                <thead>
                <tr id="title_head">
                    <td width="12%"><b>Date</b></td>
                    <td width="11%"><b>Number</b></td>
                    <td width="35%"><b>Customer</b></td>
                    <td width="10%" align="right"><b>Total</b></td>
                    <td width="1%"></td>
                    <td width="10%"align="right"><b>Paid</b></td>
                   
                    <td width="1%"></td>
                    <td with="10%" align="right"><b>Due</b></td>
                </tr></thead>';
            $i=0;
            foreach ($invoice_sales_arr as $cash_sales_row) {
                $date = date($ocio_config['php_abms_date_format'],  strtotime($cash_sales_row['invoice_date']));
                $invoice_id = $cash_sales_row['invoice_id'];
                $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
                $credit_note_id = $cash_sales_row['credit_note_id'];
                $credit_amount = $CSalesManager->total_creditNote_amount_and_tax($credit_note_id,$from_date,$to_date);
                $total_show = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
                $total = $CInvoiceManager->get_total_amount($invoice_id, $invoice_revision_id);
                $paid = $CPaymentManager->get_total_payment($invoice_revision_id);
                $due = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show);
                $amount = $paid - $credit_amount;
                // Hide truong hop amount invoice = amount paid
                $hide = false;
                if($amount==0 && $amount_credit!=0)
                {
                    $hide=true;
                }
                // End hide
                if(!$hide)
                {
                    $i++;
                    $html.= '<tr>
                             <td width="12%">'.$date.'</td>
                             <td width="11%">'.$cash_sales_row['invoice_no'].'</td>
                             <td width="35%">'.$cash_sales_row['company_name'].'</td>
                             <td width="10%" align="right">$'.number_format($total,2).'</td>
                             <td width="1%"></td>
                             <td width="10%" align="right">$'.number_format($paid,2).'</td>
                            
                             <td width="1%"></td>
                             <td with="10%" align="right">$'.number_format($due,2).'</td>
                         </tr>';
                    $cash_total_total+=$total;
                    $cash_total_paid+=$paid;
                    $cash_total_due+=$due;
                    $cash_total_amount+=$amount;
                }
            }
    //        if(count($cash_sales_arr)>0){
            if($i>0)
            {
                $html.= '<tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="border-top: 1px solid #000;" align="right"><b>$'.number_format($cash_total_total,2).'</b></td>
                        <td></td>
                        <td style="border-top: 1px solid #000;" align="right"><b>$'.number_format($cash_total_paid,2).'</b></td>
                       
                        <td></td>
                        <td style="border-top: 1px solid #000;" align="right"><b>$'.number_format($cash_total_due,2).'</b></td>
                    </tr>';
            }
    //        }else{
    //            $html.= '<tr ><td colspan="7">No report</td></tr>';
    //        }
           $html.= '</table>';
        
// Invoice report
    //$cash_sales_arr = $CReportManager->get_payment_sales_by_term_invoice(false,$customer_value_arr,$from_date,$to_date);
        //print_r($cash_sales_arr);
            if($report_tyle==""){
                $html.='<hr style="'.$display_cash.'">';
            }
        }
        if(count($cash_sales_arr)>0){
            $html.= '<table width="100%" border="0" style="'.$display_invoice.'" cellpadding="0" cellspacing="4" id="aging_report" class="tbl">
                        <tr ><td colspan="9" style="height:35px; font-size:1.8em;" align="center">Invoice Sales</td></tr>
                <thead>
                <tr id="title_head">
                    <td width="12%"><b>Date</b></td>
                    <td width="11%"><b>Number</b></td>
                    <td width="35%"><b>Customer</b></td>
                    <td width="10%" align="right"><b>Total</b></td>
                    <td width="1%"></td>
                    <td width="10%"align="right"><b>Paid</b></td>
                    
                    <td width="1%"></td>
                    <td with="10%" align="right"><b>Due</b></td>
                </tr></thead>';
            $amount =0;$i=0;
            foreach ($cash_sales_arr as $cash_sales_row) {
                $date = date($ocio_config['php_abms_date_format'],  strtotime($cash_sales_row['invoice_date']));
                $invoice_id = $cash_sales_row['invoice_id'];
                $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
                $credit_note_id = $cash_sales_row['credit_note_id'];
                
                $total_show = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
                $total = $CInvoiceManager->get_total_amount($invoice_id, $invoice_revision_id);
                $paid = $CPaymentManager->get_total_payment($invoice_revision_id);
                $due = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show);
                $amount_credit = $CSalesManager->total_creditNote_amount_and_tax($credit_note_id,$from_date,$to_date);
                $amount = $paid-$amount_credit;
                // Hide truong hop amount invoice = amount paid
                $hide = false;
                if($amount==0 && $amount_credit!=0)
                {
                    $hide=true;
                }
                // End hide
                if(!$hide)
                {
                    $i++;
                    $html.= '<tr>
                             <td width="12%">'.$date.'</td>
                             <td width="11%">'.$cash_sales_row['invoice_no'].'</td>
                             <td width="35%">'.$cash_sales_row['company_name'].'</td>
                             <td width="10%" align="right">$'.number_format($total,2).'</td>
                             <td width="1%"></td>
                             <td width="10%" align="right">$'.number_format($paid,2).'</td>
                             
                             <td width="1%"></td>
                             <td with="10%" align="right">$'.number_format($due,2).'</td>
                         </tr>';
                    $total_total+=$total;
                    $total_paid+=$paid;
                    $total_due+=$due;
                    $total_admoun+=$amount;
                }
            }
    //        if(count($cash_sales_arr)>0){
            if($i>0)
            {
                $html.= '<tr>
                        <td ></td>
                        <td ></td>
                        <td ></td>
                        <td  style="border-top: 1px solid #000;" align="right"><b>$'.number_format($total_total,2).'</b></td>
                        <td ></td>
                        <td  style="border-top: 1px solid #000;" align="right"><b>$'.number_format($total_paid,2).'</b></td>
                        
                        <td ></td>
                        <td style="border-top: 1px solid #000;" align="right"><b>$'.number_format($total_due,2).'</b></td>
                    </tr>';
            }
    //        }else{
    //            $html.= '<tr ><td colspan="7">No report</td></tr>';
    //        }
           $html.= '</table>';
            if($report_tyle==1){
                $total_total=0;$total_paid=0;$total_due=0;
            }
            if($report_tyle==2){
                $cash_total_total=0;$cash_total_paid=0;$cash_total_due=0;
            }
           $sub_total = $cash_total_total + $total_total;
           $sub_paid = $cash_total_paid + $total_paid;
           $sub_amount = $cash_total_amount + $total_admoun;
           $sub_due = $cash_total_due + $total_due;
           $html.='<table width="100%" cellpadding="0" cellspacing="4" class="tbl">
                        <tr>
                            <td colspan="8"></td>
                        </tr>
                        <tr>
                            <td width="11%" ><b>Sub total</b></td>
                            <td width="10%" ></td>
                            <td width="36%" ></td>
                            <td width="10%" style="border-top: 1px solid #000; border-bottom: 1px solid #000;" align="right"><b>$'.number_format($sub_total,2).'</b></td>
                            <td width="1%"></td>
                            <td width="10%" style="border-top: 1px solid #000; border-bottom: 1px solid #000;" align="right"><b>$'.number_format($sub_paid,2).'</b></td>
                            
                            <td width="1%" ></td>
                            <td width="10%" style="border-top: 1px solid #000; border-bottom: 1px solid #000;" align="right"><b>$'.number_format($sub_due,2).'</b></td>
                        </tr>
                    </table>
               ';
        }
        
        $pdf->AddPage();              
        $pdf->writeHTML($html, true, false, true, false, '');
        ob_end_clean();
        $pdf->Output('sales_report.pdf', 'I');
    }
//    function get_customer_By_Payment(){
//        $q = new DBQuery;
//        $q->addTable('sales_payment','tbl1');
//        $q->addQuery('tbl1.*,tbl2.*');
//        $q->addJoin('sales_payment', 'tbl2', 'tbl2.payment_id = tbl1.payment_id');
//        $q->addJoin('sales_invoice_revision', 'tbl3', 'tbl1.invoice_revision_id = tbl3.invoice_revision_id');
//        $q->addJoin('sales_invoice', 'tbl4', 'tbl3.invoice_id = tbl4.invoice_id');
//        $q->addJoin('clients', 'tbl5','tbl5.company_id=tbl4.customer_id');
//        return $q->loadList();
//    }
//    function get_customer_By_Payment(){
//        $q = new DBQuery;
//        $q->addTable('sales_payment','tbl1');
//        $q->addQuery('tbl1.*');
//        $q->addJoin('sales_payment', 'tbl2', 'tbl2.payment_id = tbl1.payment_id');
//        $q->addJoin('sales_invoice_revision', 'tbl3', 'tbl1.invoice_revision_id = tbl3.invoice_revision_id');
//        $q->addJoin('sales_invoice', 'tbl4', 'tbl3.invoice_id = tbl4.invoice_id');
//        $q->addJoin('clients', 'tbl5','tbl5.company_id=tbl4.customer_id');
//        return $q->loadList();
//    }
    function print_statement_report_pdf(){
        global $ocio_config;
        require_once (DP_BASE_DIR . '/modules/sales/CHeaderReportSatement.php');
        $pdf = new MYPDFStatement(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Customer Statement');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 80, 15, 5);
        $pdf->setHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(true);
        $pdf->setPrintFooter(true);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 10.3);
        
        global $AppUI, $CReportManager,$CSalesManager,$CInvoiceManager;
        
        $load_inv = false; $date_from=false; $date_to=false;
        if($_GET['load_inv']!="")
            $load_inv = $_GET['load_inv'];
        if($_GET['sta_date_from']!="")
            $date_from = $_GET['sta_date_from'];
        if($_GET['sta_date_from'] !="")
            $date_to = $_GET['sta_date_to'];

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
        }
        $supplier = '<p>'. $sales_owner_name .'</p>';
        $supplier.= '<p>'.$sales_owner_address.'</p>';
        $supplier .= '<p>'.$sales_owner_code.' Tel: '.$phone.' Fax: '.$fax.'</p>';
        $supplier .= '<p>GST Reg No: '. $sales_owner_gst_reg_no .'</p>';

        // Logo
            $url = DP_BASE_DIR. '/modules/sales/images/logo/';
            $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
            
        $files = list_files($url);
        if(count($files)>0)
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
               $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" />';
        }
            
//            $files = scandir($url);
//            $i = count($files) -1;
//            if (count($files) > 2 && $files[$i]!=".svn") {
//                $path_file = $url . $files[$i];
//                $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" />';
//            }
    //End supplier
        
        //Lay customer lien quan;
       $agent_customer_agent_arr = $CSalesManager->get_AgentCustomer_by_customer($customer_id);
       // Lay adress customer gan customer duoc chon lam customer
       $contractor_customer_arr=$CSalesManager->get_Customer_By_AgentCustomer($customer_id);
       //Lay 
       $customer_contractor_arr = $CSalesManager->get_ContractorCustomer_by_customer($customer_id);
        
       $customer_id_arr= array();
        // Get customer  agent company
        foreach ($contractor_customer_arr as $contractor_customer) {
            if($contractor_customer['company_id']!="")
                $customer_id_arr[]=$contractor_customer['company_id'];
        }
        foreach ($agent_customer_agent_arr as $agent_customer_agent) {
            if($agent_customer_agent['company_id']!="")
                $customer_id_arr[]=$agent_customer_agent['company_id'];
        }
        foreach ($customer_contractor_arr as $customer_contractor) {
            if($customer_contractor['company_id']!="")
                $customer_id_arr[]=$customer_contractor['company_id'];
        }
       
        $customer_id_implode =  implode(',', $customer_id_arr);
        $address_agent_arr = $CReportManager->get_list_address_and_agent($customer_id_implode);
        
        //print_r($address_agent_arr);
        $address_id_arr ="";
        foreach ($address_agent_arr as $address_agent_row) {
            $address_id_arr .=",".$address_agent_row['address_id'];
        }
        
       // GET Info Customer
            $state_address = false;
            if($_GET['address_id']!=""){
                $state_address = $_GET['address_id'];
            }
            $agent_address_id_arr = "0".$address_id_arr;
            $address_arr = $CReportManager->get_list_address_and_agent($customer_id, $state_address,$agent_address_id_arr);
            
            //$test = count($address_arr);
            //$attention_arr = $CSalesManager->get_list_attention($customer_id);
            //$address_customer=$customer_name.".";
            
            $i =0;$tes =0;
                foreach ($address_arr as $address_row2) {
                        $address_id12 = $address_row2['address_id'];
                        $tes.=",".$address_id12;
                        $invoice_arr2 =  $CInvoiceManager->list_invoice($customer_id, $load_inv, true, $date_from, $date_to, $address_id12);
                        if(count($invoice_arr2)>0){
                            $i++;
                        }

                }
            
            if($_GET['attention_state']!="")
                //$address_customer.=" Attn: ".$_GET['attention_state'];

        $amount30=0;$amount60=0;$amount90=0;$day=0;
        $row=14;
        $invoice_is = FALSE;
    $invoice_row_count = 0;
    foreach ($address_arr as $address_row) {
        $html ="";
        $address_id = $address_row['address_id'];
        $selected="";$address_2="";$brand="";$address_type="";
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
        $opt_address=$brand.$address_row['address_street_address_1'].$address_2.' '.'Singapore '.$address_row['address_postal_zip_code'];
        
         // Get invoice customer
        $invoice_arr =  $CInvoiceManager->list_invoice($customer_id,$load_inv,true, $date_from, $date_to, $address_id);
        if($state_address!="")
            $invoice_arr =  $CInvoiceManager->list_invoice($customer_id,$load_inv,true, $date_from, $date_to, $state_address);
        $invoice_row_count += count($invoice_arr);
        if(count($invoice_arr)>0){
        if($state_address=="" && $i!=1){
            $html.="<br><b>Address: </b>".$opt_address."<br>";
        }
        else {
            $html.="&nbsp;<br>";
        }
        $html.= '<table border="1" width="99%" cellpadding="8" cellspacing="0">
                <thead>
                    <tr style="font-weight:bold;">
                        <td align="center">Date</td>
                        <td align="center">Invoice Number</td>
                        <td align="right">Amount</td>
                        <td align="right">Payments</td>
                        <td align="right">Due</td>
                    </tr>
                </thead>
        <tbody>';
        $amount_table = 0; $payment_table=0; $due_table=0;
        $j=0;
        foreach ($invoice_arr as $invoice_row) {
            $j++;
            $bottom = 0;
            if($j%16==0)
               $bottom="border-bottom:1px solid #000;";
            if($j>16){
                if($j==32)
                    $bottom.="height:41px;";
                else if($j==48 || $j==64 || $j==80 || $j==96 || $j==112 || $j==128 || $j==144 || $j==160 || $j==176 || $j==192 || $j==208 || $j==244) {
                    $bottom.="height:48px;";
                }
            }
            $invoice_date = date($ocio_config['php_abms_date_format'], strtotime($invoice_row['invoice_date']));
            $invoice_id = $invoice_row['invoice_id'];
            $invoice_revision_arr = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
            $invoice_revision_id = $invoice_revision_arr[0]['invoice_revision_id'];
            $total_show = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
            // Amount Invoice
            $amount = $CInvoiceManager->get_total_amount($invoice_id,$invoice_revision_id);
            // Payment Invoice
            $payment = $CInvoiceManager->get_total_amount_paid1($invoice_revision_id);
            // Due Invocie
            $due = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show);

            // Total
            $total_amount +=$amount;
            $total_payment += $payment;
            $total_due += $due;
            
            $amount_table +=$amount;
            $payment_table += $payment;
            $due_table += $due;
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

            $html.=
                                '<tr>
                                    <td  align="center" style="border-right:1px solid #000;'.$bottom.'">'.$invoice_date.'</td>
                                    <td  align="center" style="border-right:1px solid #000;'.$bottom.'">'.$invoice_row['invoice_no'].'</td>
                                    <td  align="right" style="border-right:1px solid #000;'.$bottom.'">$'.number_format($amount, 2).'</td>
                                    <td  align="right" style="border-right:1px solid #000;'.$bottom.'">$'.number_format($payment, 2).'</td>
                                    <td  align="right" style="border-right:1px solid #000;'.$bottom.'">$'.number_format($due,2).'</td>
                                </tr>';
        }
//        if(count($invoice_arr)>0){
//            $j=$j+1;
//            $invoice_is = TRUE;
//        }
//               if($row>$j){
//            $row1=$row-$j-1;
//            $pdf->SetAutoPageBreak(true, 70);
//        }  else if($row==$j) {
//            $pdf->SetAutoPageBreak(true, 60);
//        }else{
//            $tmp= $j/$row;
//            $tmp=(string)$tmp;
//            $tmp1=$tmp[0] ;
//            $tmp1 = (int)$tmp1;
//            $row1 = $row*($tmp1+1);
//            $row1 = $row1 - $j-1;
//            $pdf->SetAutoPageBreak(true, 72);
//        }
//           if($j==16){
//                $html.= '<tr>
//                        <td style="border-right:1px solid #000;">&nbsp;</td>
//                        <td style="border-right:1px solid #000;">&nbsp;</td>
//                        <td style="border-right:1px solid #000;">&nbsp;</td>
//                        <td style="border-right:1px solid #000;">&nbsp;</td>
//                        <td style="border-right:1px solid #000;">&nbsp;</td>
//                    </tr>';
//            }
            $html.=                '<tr align="right" style="font-weight:bold;">
                                    <td  colspan="2"></td>
                                    <td >$'.number_format($amount_table, 2).'</td>
                                    <td >$'.number_format($payment_table, 2).'</td>
                                    <td >$'.number_format($due_table,2).'</td>
                                </tr>';
             $html.=       '</tbody></table>
        ';
        if($i==1){
            $hf = 60.5;
        }else
            $hf = 60.5;
    
        $pdf->SetAutoPageBreak(true, $hf);
        $pdf->AddPage();              
        $pdf->writeHTML($html, true, false, true, false, '');
        }
        if($state_address!="")
            break;
    }
        
        ob_end_clean();
        $pdf->Output('customer_statement_report.pdf', 'I');
    }
    
    function print_pdf_aging_brand(){
        
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Aging report');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, 17);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        $pdf->setFontSubsetting(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 10.5);
       
        
        global $AppUI, $CInvoiceManager, $CSalesManager;
        $customer_id = false;
        if(isset($_GET['customer_id']) && $_GET['customer_id']!="null")
            $customer_id = $_GET['customer_id'];
        
        $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
        
        $data_from1=false;$data_to1=false;
        $date_fomat = "";
//        if($_GET['data_to']!=""){
//            $date_from1 = $_GET['date_from'];
//            $date_fomat.="From: ".date('d/m/Y', strtotime($date_from1))."&nbsp;&nbsp;&nbsp;";
//        }
        if($_GET['data_to']!=""){
            $date_to1 = $_GET['data_to'];
            $date_fomat.="Date: ".date('d/m/Y', strtotime($date_to1));
        }
        if($_GET['include_address']!=""){
            $include_address=$_GET['include_address'];
        }
        
        $html= '<table width="100%" border="0" callpadding="2" cellspacing="0" id="aging_report" class="tbl">
                <tr>
                    <td colspan="5" align="left">'.$date_fomat.'</td>
                    <td colspan="2" align="right">'.date("M-d-Y").'</td>
                </tr>
                <tr valign="top">
                    <td colspan="7" style="font-size:2em;" align="center" height="50" >Aging report</td>
                </tr>
                <thead >
                <tr>
                    <td width="18%" height="25"><b>'.$AppUI->_("Number").'</b></td>
                    <td width="13%"><b>'.$AppUI->_("Date").'</b></td>
                    <td width="13%" align="right"><b>'.$AppUI->_("0 - 30 Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("31 - 60 Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("61 - 90 Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("91 + Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("Balance").'</b></td>
               </tr>
               </thead><tbody>
               ';
        //$job_location_arr = $CInvoiceManager->get_joblocation_inner_invoice($customer_id);
        $customer_id_arr = $CInvoiceManager->get_customer_by_invoice($customer_id);
        $tmp = $customer_id_arr[0]['address_id'];
        $tmpcus = $customer_id_arr[0]['company_id'];
        $i=1;$j=1;
        foreach ($customer_id_arr as $customer_row)
        {
            $i++;
            if($tmp==$customer_row['address_id'] &&  $i>2 && $customer_row['address_id'] != 0){
                continue;
            }
            else if($customer_row['address_id']==0 && $tmpcus == $customer_row['company_id']){
                $j++;
            }
            else{
                $tmp=$customer_row['address_id'];
                $tmpcus = $customer_row['company_id'];
                $j=1;
            }
            if($j>2){
                continue;
            }
            $address_id=$customer_row['address_id'];
            $agingArr = $this ->list_agingreport($customer_row['company_id'],$_GET['date_from'],$_GET['date_to'],$date_from1,$date_to1,$address_id,true);
            $job_location="";
            foreach ($agingArr as $aging_row)
            {
                    $invoice_id=$aging_row["invoice_id"];
                    $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
                    $total_show = $CInvoiceManager ->get_invoice_item_total($invoice_id, $invoice_revision_id);
                    $total_item_show = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show);
                            $start =  strtotime($aging_row[invoice_date]);
                            $end = strtotime(date("Y/m/d"));
                            $day_diff=  round(($end - $start)/86400);

                        if($day_diff>=0 && $day_diff<=30)
                        {

                            $day30=$total_item_show;
                            $day60=0;$day90=0;$day=0;$dayBalance=$day30;
                        }
                        else if($day_diff>30 && $day_diff<=60)
                        {
                            $day60=$total_item_show;
                            $day30=0;$day90=0;$day=0;$dayBalance=$day60;

                        }
                        else if($day_diff>60 && $day_diff<=90)
                        {
                            $day90=$total_item_show;
                            $day30=0;$day60=0;$day=0;$dayBalance=$day90;
                        }
                        else if( $day_diff>90)
                        {
                            $day=$total_item_show;
                            $day30=0;$day60=0;$day90=0;$dayBalance=$day;
                        }
                        $tmpDay30 += $day30; $tmpDay60 +=$day60; $tmpDay90 += $day90;$tmpDay +=$day;$tmpBalance += $dayBalance;
                        $totalBalance = round($tmpBalance,2); $totalDay30=round($tmpDay30,2); $totalDay60=round($tmpDay60,2); $totalDay90=round($tmpDay90,2); $totalDay= round($tmpDay,2);
                            if($dayBalance>0){
                                // get joblocation
//                                $job_location_arr =  $CSalesManager->get_address_by_id($aging_row['job_location_id']);
                                if($include_address==2){
                                        $brand="";
                                    if($aging_row['address_branch_name']!="")
                                        $brand=$aging_row['address_branch_name'].' - ';
                                    $address_2="";
                                    if($aging_row['address_street_address_2']!="")
                                        $address_2= ', '.$aging_row['address_street_address_2'];
                                    $postal_code_job = '';
                                    if($aging_row['address_postal_zip_code']!="")
                                        $postal_code_job .=', Singapore '.$aging_row['address_postal_zip_code'];
                                    
                                    $job_location=$brand.$CSalesManager->htmlChars($aging_row['address_street_address_1'].$address_2.$postal_code_job);
                                    
                                    if($aging_row['address_phone_1']!="")
                                        $job_location.=", Phone: ".$aging_row['address_phone_1'];
                                    elseif($aging_row['address_phone_2']!="")
                                        $job_location.=", Phone: ".$aging_row['address_phone_2'];
                                }
                                $h_no ="";$h_job = "";
                                if($job_location!=""){
                                        $h_job ='<tr>
                                            <td style="font-size:0.9em;color:#666" colspan="11" height="30">'.$job_location.'</td>
                                        </tr>';
                                }else{
                                    $h_no = 'height="30"';
                                }

                                $html.='
                                        <tr>
   
                                            <td width="18%" '.$h_no.'>'.$aging_row["invoice_no"].'</td>
                                            <td width="13%">'.date("d/m/Y",strtotime($aging_row['invoice_date'])).'</td>
                                            <td width="13%" align="right">$'.number_format($day30, 2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day60,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day90,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($dayBalance,2).'</td>
                                        </tr>
                                        '.$h_job;
                            }

                }
               $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
               if(count($agingArr) == 0){
                    $totalDay30=0;$totalDay60=0;$totalDay90=0;$totalDay=0;$totalBalance=0;
                }
                if($totalBalance){
                    $address1 ="";
                         $address_arr=$CSalesManager->get_address_by_id($customer_row['address_id']);
                         if(count($address_arr)>0){
                             $address1=" (";
                             ($address_arr['address_branch_name']) ? $address1 .= $address_arr['address_branch_name'].' - ' : $address1.="";
                             ($address_arr['address_street_address_1']) ? $address1 .= $address_arr['address_street_address_1'] : $address1="";
                             ($address_arr['address_street_address_2']) ? $address1 .= ' '.$address_arr['address_street_address_2'] : $address1.="";
                             ($address_arr['address_postal_zip_code']) ? $address1 .= ' Singapo '.$address_arr['address_postal_zip_code'] : $address1.="";
                             $address1.=")";
                         }
                    $html.='
                            <tr  valign="top" >
                               <td colspan="2">&nbsp;</td>
                               <td align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay30,2).'</td>
                                   <td>&nbsp;</td>
                               <td align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay60,2).'</td>
                                   <td >&nbsp;</td>
                               <td align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay90,2).'</td>
                                   <td >&nbsp;</td>
                               <td align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay,2).'</td>
                                   <td >&nbsp;</td>
                               <td align="right" style="border-top: 1px solid #000;">$'.number_format($totalBalance,2).'</td>
                            </tr>
                            <tr>
                                <td colspan="11"><b>'.$customer_row['company_name'].'</b>'.$address1.'<br></td>
                            </tr><br>
                            ';
                        $aging_total_30 += $totalDay30;
                        $aging_total_60 += $totalDay60;
                        $aging_total_90 += $totalDay90;
                        $aging_total_day += $totalDay;
                        $aging_total_banlance += $totalBalance;
                }
            }
            $html.='<tr style="font-weight:bold;">
                    <td colspan="2" style="font-size:1.1em;">Total</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1em;">$'.number_format($aging_total_30,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1em;">$'.number_format($aging_total_60,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1em;">$'.number_format($aging_total_90,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1em;">$'.number_format($aging_total_day,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1em;">$'.number_format($aging_total_banlance,2).'</td>
            </tr>';
            $html.='</tbody></table>';
            
            $pdf->AddPage();              
            $pdf->writeHTML($html, true, false, true, false, '');
            ob_end_clean();
            $pdf->Output('aging_report.pdf', 'I');
    }
    
    function get_quotation_statement($customer_id=false,$status_id=false,$address_id=false,$form=false,$to=false,$user_id){
        $q = new DBQuery();
        $q->addTable('sales_quotation','tbl1');
        $q->addJoin('clients', 'tbl2', 'tbl2.company_id = tbl1.customer_id');
        $q->addJoin('addresses', 'tbl3','tbl3.address_id = tbl1.job_location_id');
        //$q->addJoin('sales_quotation_revision', 'tbl4', 'tbl4.quotation_id=tbl1.quotation_id');
        $q->addQuery('tbl1.*,tbl2.company_name,tbl3.*');
        if($customer_id)
            $q->addWhere('tbl1.customer_id='.intval($customer_id));
        if($status_id!="-1")
            $q->addWhere('tbl1.quotation_status='.intval($status_id));
        if($address_id)
            $q->addWhere ('tbl1.address_id='.intval ($address_id));
        if($form)
            $q->addWhere ('tbl1.quotation_date >= "'.$form.'"');
        if($to)
            $q->addWhere ('tbl1.quotation_date <= "'.$to.'"');
        if($user_id)
            $q->addWhere ('tbl1.user_id='.intval ($user_id));
        $q->addGroup('quotation_id');
        $q->addOrder('quotation_no');
        return $q->loadList();
    }
    
    function get_quotation_statement_brand($customer_id=false,$status_id=false,$address_id=false,$form=false,$to=false,$user_id){
        $q = new DBQuery();
        $q->addTable('sales_quotation','tbl1');
        $q->addJoin('clients', 'tbl2', 'tbl2.company_id = tbl1.customer_id');
        $q->addJoin('addresses', 'tbl3','tbl3.address_id = tbl1.job_location_id');
        $q->addQuery('tbl1.*,tbl2.company_name,tbl3.*');
        if($customer_id)
            $q->addWhere('tbl1.customer_id='.intval($customer_id));
        if($address_id)
            $q->addWhere ('tbl1.address_id='.intval ($address_id));
        else
            $q->addWhere ('tbl1.address_id=0');
        if($form)
            $q->addWhere ('tbl1.quotation_date >= "'.$form.'"');
        if($to)
            $q->addWhere ('tbl1.quotation_date <= "'.$to.'"');
        if($status_id!="-1")
            $q->addWhere('tbl1.quotation_status='.intval($status_id));
        if($user_id)
            $q->addWhere ('tbl1.user_id='.intval($user_id));
        $q->addOrder('quotation_no');
        return $q->loadList();
    }
    
    
    function print_quo_statement(){

        global $ocio_config;
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Quotation Statement');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, 17);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        $pdf->setFontSubsetting(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 9.6);
        
        global $AppUI, $CReportManager, $CQuotationManager, $CSalesManager,$user_roles;
        $status_arr = dPgetSysVal('QuotationStatus');
        
        // Kiem tra neu user co roles la "Innoflex Business Development Manager" thi chi duoc view quotaiton cua minh
        $view_report_quotation_user = false;
        if(in_array('innoflex_business_development_manager',$user_roles))
            $view_report_quotation_user = $AppUI->user_id;
        
        // Colors, line width and bold font
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(000);
        //$pdf->SetDrawColor(128, 0, 0);
        //$pdf->SetLineWidth(0.5);
        $pdf->AddPage();
        $txt = "";
        if($_GET['from'] && $_GET['from']!=""){
            $from = $_GET['from'];
            $from_format = date($ocio_config['php_abms_date_format'], strtotime($from));
            $txt .='From: '.$from_format.'   ';
        }
        if($_GET['to'] && $_GET['to']!=""){
            $to = $_GET['to'];
            $from_format = date($ocio_config['php_abms_date_format'], strtotime($to));
            $txt .='To: '.$to;
        }
        $customer_id =false;
        if($_GET['customer_id'] && $_GET['customer_id']!="")
        {
            $customer_id = $_GET['customer_id'];
        }
        if($_GET['status_id'])
            $status_id = $_GET['status_id'];
        if($_GET['address_id'] && $_GET['address-id']!="")
            $address_id = $_GET['address_id'];
        
        $pdf->Cell(150, 0, $txt, 0, 0, 'L', 1);
        $pdf->Cell(48, 0, date('d.M.Y'), 0, 0, 'R', 1);
        $pdf->Ln();
        
        
        $pdf->writeHTMLCell(0,12,80,12,'<h1>Quotation Statement</h1>' , 0, 0, 'C', 1);
        $pdf->Ln();
        
        $html="";
        $html_head= '<table width="100%" cellspacing="0" cellpadding="4" border="1">
                <tr style="font-weight:bold;">
                    <td width="11%">Date</td>
                    <td width="13%">Number</td>
                    <td width="35%">Customer</td>
                    <td width="11%">status</td>
                    <td width="20%">subject</td>
                    <td align="right" width="11%">Total</td>
                </tr>
            </table>';
        $pdf->writeHTMLCell('','','','',$html_head);
        $pdf->ln();
        $i=0;
        $html_body = "";
        $custome_array = $CQuotationManager->getCustomerByQuotation($customer_id);
        foreach($custome_array as $custome_data)
        {
            $totalCustomer = 0;
            $quo_statement_arr = $CReportManager->get_quotation_statement($custome_data['company_id'], $status_id, $address_id, $from, $to,$view_report_quotation_user);
            if(count($quo_statement_arr)>0)
            {
                foreach ($quo_statement_arr as $quo_statement_row) {
                        $i++;
                        $quotation_id = $quo_statement_row['quotation_id'];
                        $quotation_rev_arr = $CQuotationManager->get_latest_quotation_revision($quotation_id);
                        $quotation_revision_id = $quotation_rev_arr[0]['quotation_revision_id'];

                         $brand="";
                         if($quo_statement_row['address_branch_name']!="")
                             $brand=$quo_statement_row['address_branch_name'].' - ';
                         $address_2="";
                         if($quo_statement_row['address_street_address_2']!="")
                             $address_2= ', '.$quo_statement_row['address_street_address_2'];
                         $postal_code_job = '';
                         if($quo_statement_row['address_postal_zip_code']!="")
                             $postal_code_job .=', Singapore '.$quo_statement_row['address_postal_zip_code'];

                         $job_location=$brand.$CSalesManager->htmlChars($quo_statement_row['address_street_address_1'].$address_2.$postal_code_job);   

                         $quotation_rev_f ="";$quotation_rev_r="";
                         $quotation_rev_f = substr($quotation_rev_arr[0]['quotation_revision'],0,10);
                         $total = round($CQuotationManager->get_total_tax_and_paid($quotation_revision_id, $CQuotationManager->get_quotation_item_total($quotation_id, $quotation_revision_id)),2);
                         $totalCustomer+=$total;
                         if(strlen($quotation_rev_arr[0]['quotation_revision'])>10)
                            $quotation_rev_r = '<br>'.substr($quotation_rev_arr[0]['quotation_revision'],9);
                   $html_body= '<table width="100%" cellspacing="0" cellpadding="4" border="1"><tr id="title_head">
                                <td width="11%">'.date($ocio_config['php_abms_date_format'],  strtotime($quo_statement_row['quotation_date'])).'</td>
                                <td width="13%">'.$quotation_rev_f.$quotation_rev_r.'</td>
                                <td width="35%">'.$quo_statement_row['company_name'].'
                                       <div style="color:#666;font-size:0.9em;">'.$job_location.'</div></td>
                                <td width="11%">'.$status_arr[$quo_statement_row['quotation_status']].'</td>
                                <td width="20%">'.$quo_statement_row['quotation_subject'].'</td>
                                <td align="right" width="11%">$'.number_format($total,2).'</td>
                            </tr></table>';
                    if($pdf->GetY()>$pdf->getPageHeight()-50){
                        $pdf->AddPage();
                        $pdf->writeHTMLCell('','','','',$html_head);
                        $pdf->ln();
                    }
                    $pdf->writeHTMLCell('','','','',$html_body);
                    $pdf->ln();
                }
            
                $html_footer = '&nbsp;<table width="100%" cellspacing="0" cellpadding="4" border="1">
                        <tr>
                            <td width="69.7%"><b>'.$custome_data['company_name'].'</b></td>
                            <td align="right" width="30.8%"><b>$'.number_format($totalCustomer,2).'</b></td>
                        </tr>
                    </table>';
            
                $pdf->writeHTML($html_footer);
            }
        }

        ob_end_clean();
        $pdf->Output('quotation.pdf', 'I');
    }

//function print_quo_statement(){
//        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
//
//        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//        $pdf->SetTitle('Quotation Statement');
//        $pdf->SetCreator(PDF_CREATOR);
//        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//        $pdf->SetMargins(6, 10, 8, -15);
//        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//        $pdf->SetAutoPageBreak(true, 0);
//        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//        $pdf->SetPrintHeader(false);
//        $pdf->setFontSubsetting(false);
//        //$pdf->setPage(3);
//        $pdf->setLanguageArray($l);
//        $pdf->SetFont('helvetica', '', 10.5);
//        
//        $pdf->AddPage();
//        
//       $html_head= '<table width="100%" cellspacing="0" cellpadding="4" border="1">
//                <tr style="font-weight:bold;">
//                    <td width="11%">Date</td>
//                    <td width="13%">Number</td>
//                    <td width="35%">Customer</td>
//                    <td width="11%">status</td>
//                    <td width="20%">subject</td>
//                    <td align="right" width="11%">Total</td>
//                </tr>
//            </table>';
//        $pdf->writeHTMLCell('','','','',$html_head);
//        $pdf->ln();
//        $html_body= '<table width="100%" cellspacing="0" cellpadding="4" border="1">
//                <tr style="font-weight:bold;">
//                    <td width="11%">Date</td>
//                    <td width="13%">Number</td>
//                    <td width="35%">Customer</td>
//                    <td width="11%">status</td>
//                    <td width="20%">subject</td>
//                    <td align="right" width="11%">Total</td>
//                </tr>
//        </table>';
//        $pdf->writeHTMLCell('','','','',$html_body);
//        
//        
//        $pdf->setAutoPageBreak(true, 30);
//        ob_end_clean();
//        $pdf->Output('quotation.pdf', 'I');
//    
//}
    
//    function create_aging_report_pdf_file(){
//        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
//        // create new PDF document
//        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//        //$pdf = new TCPDF
//
//        // set document information
//        $pdf->SetCreator(PDF_CREATOR);
//        $pdf->SetAuthor('Nicola Asuni');
//        $pdf->SetTitle('Aging Report');
//        $pdf->SetSubject('TCPDF Tutorial');
//        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
//
//        // set default header data
//        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);
//
//        // set header and footer fonts
//        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
//
//        // set default monospaced font
//        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//
//        // set margins
//        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//
//        // set auto page breaks
//        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//
//        // set image scale factor
//        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//
//        // set font
//        $pdf->SetFont('helvetica', '', 11);
//
//        // add a page
//        $pdf->AddPage();
//
//        // column titles
//        $header = array('Number', 'Date', '0 - 30 Days', '31 - 60 Days','61 - 90 Days','90 + Days', 'Balance');
//
//        // data loading Body
//        global $AppUI, $CInvoiceManager, $CSalesManager;
//        $customer_id = false;
//        if(isset($_GET['customer_id']) && $_GET['customer_id']!="null")
//            $customer_id = $_GET['customer_id'];
//        
//        $companyArr= $this->getCompanyByInvoice($customer_id);
//        $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
//        
//        
//
//        // Colors, line width and bold font
//        $pdf->SetFillColor(255, 255, 255);
//        $pdf->SetTextColor(0);
//        
//        // Header
//        $w = array(20, 20, 30, 30 ,30 ,30 ,30 );
//        $num_headers = count($header);
//        for($i = 0; $i < $num_headers; ++$i) {
//                $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
//        }
//        $pdf->Ln();
//        // Color and font restoration
//        $pdf->SetFillColor(224, 235, 255);
//        $pdf->SetTextColor(0);
//        $pdf->SetFont('');
//        // Data
//        $fill = 0;
//        $lr =0;
//        foreach($companyArr as $row_company) {
//                $pdf->Cell(160, 6, $row_company['company_name'], 0, $lr, 'L', $fill);
//                $pdf->Ln();
//                
//                $agingArr = $this ->list_agingreport($row_company['company_id'],$_GET['date_from'],$_GET['date_to']);
//                foreach ($agingArr as $aging_row) { 
//                    $pdf->Cell(20, 6, $aging_row['invoice_no'], 0, $lr, 'L', $fill);
//                    $pdf->Ln();
//                }
//                
//        }
//
//        // ---------------------------------------------------------
//        ob_end_clean();
//        // close and output PDF document
//        $pdf->Output('aging_report.pdf', 'I');
//
//        //============================================================+
//        // END OF FILE
//        //============================================================+
//    }
    public function get_list_address_and_agent($customer_id_array=false,$address_id=false,$agent_address_id_arr=false) {
            $where_agent="";
            if($agent_address_id_arr)
            {
                $where_agent = ' OR address_id IN ('.$agent_address_id_arr.')';
            }
            $q = new DBQuery();
            $q->addTable('addresses');
            $q->addQuery('address_id, address_street_address_1, address_street_address_2, address_type, address_postal_zip_code,address_branch_name');
            if($customer_id_array)
                $q->addWhere('company_id IN ('.$customer_id_array.')'. $where_agent);
            if($address_id)
                $q->addWhere ('address_id='.  intval($address_id));
            $q->addOrder('address_type DESC');
            return $q->loadList();
    }
    
    public function print_quo_statement_brand()
    {
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Quotation Statement');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(5, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, 17);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        $pdf->setFontSubsetting(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 10.5);
        
        global $AppUI, $CReportManager, $CQuotationManager, $CSalesManager,$user_roles;
        $status_arr = dPgetSysVal('QuotationStatus');
        
        // Kiem tra neu user co roles la "Innoflex Business Development Manager" thi chi duoc view quotaiton cua minh
        $view_report_quotation_user = false;
        if(in_array('innoflex_business_development_manager',$user_roles))
            $view_report_quotation_user = $AppUI->user_id;
        
        // Colors, line width and bold font
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(000);
        //$pdf->SetDrawColor(128, 0, 0);
        //$pdf->SetLineWidth(0.5);
        $pdf->AddPage();
        $txt = "";
        if($_GET['from'] && $_GET['from']!=""){
            $from = $_GET['from'];
            $from_format = date('d-m-Y', strtotime($from));
            $txt .='From: '.$from_format.'   ';
        }
        if($_GET['to'] && $_GET['to']!=""){
            $to = $_GET['to'];
            $from_format = date('d-m-Y', strtotime($to));
            $txt .='To: '.$to;
        }
        $customer_id =false;
        if($_GET['customer_id'] && $_GET['customer_id']!="")
        {
            $customer_id = $_GET['customer_id'];
        }
        if($_GET['status_id'])
            $status_id = $_GET['status_id'];
        if($_GET['address_id'] && $_GET['address-id']!="")
            $address_id = $_GET['address_id'];
        
        $pdf->Cell(150, 0, $txt, 0, 0, 'L', 1);
        $pdf->Cell(48, 0, date('d-m-Y'), 0, 0, 'R', 1);
        $pdf->Ln();
        
        
        $pdf->writeHTMLCell(0,12,80,12,'<h1>Quotation Statement</h1>' , 0, 0, 'C', 1);
        $pdf->Ln();
        
        $html="";
        $html_head= '<table width="100%" cellspacing="0" cellpadding="4" border="1">
                <tr style="font-weight:bold;">
                    <td width="11%">Date</td>
                    <td width="13%">Number</td>
                    <td width="35%">Customer</td>
                    <td width="11%">status</td>
                    <td width="20%">subject</td>
                    <td align="right" width="11%">Total</td>
                </tr>
            </table>';
        $pdf->writeHTMLCell('','','','',$html_head);
        $pdf->ln();
        $i=0;
        $html_body = "";
        $customer_arr = $CQuotationManager->getCustomerByQuotation($customer_id, $address_id, 2);
        foreach($customer_arr as $customer_data)
        {
            $job_location_customer = "";
            if($customer_data['address_branch_name']!="")
                  $brand=$customer_data['address_branch_name'].' - ';
              $address_2="";
              if($customer_data['address_street_address_2']!="")
                  $address_2= ', '.$customer_data['address_street_address_2'];
              $postal_code_job = '';
              if($customer_data['address_postal_zip_code']!="")
                  $postal_code_job .=', Singapore '.$customer_data['address_postal_zip_code'];
              $job_location_customer=$brand.$CSalesManager->htmlChars($customer_data['address_street_address_1'].$address_2.$postal_code_job);
              
            $totalCustomer = 0;
            $quo_statement_arr = $CReportManager->get_quotation_statement_brand($customer_data['company_id'], $status_id,$customer_data['address_id'], $from, $to,$view_report_quotation_user);
            if(count($quo_statement_arr)>0)
            {
                foreach ($quo_statement_arr as $quo_statement_row) {
                        $i++;
                        $quotation_id = $quo_statement_row['quotation_id'];
                        $quotation_rev_arr = $CQuotationManager->get_latest_quotation_revision($quotation_id);
                        $quotation_revision_id = $quotation_rev_arr[0]['quotation_revision_id'];

                         $brand="";
                         if($quo_statement_row['address_branch_name']!="")
                             $brand=$quo_statement_row['address_branch_name'].' - ';
                         $address_2="";
                         if($quo_statement_row['address_street_address_2']!="")
                             $address_2= ', '.$quo_statement_row['address_street_address_2'];
                         $postal_code_job = '';
                         if($quo_statement_row['address_postal_zip_code']!="")
                             $postal_code_job .=', Singapore '.$quo_statement_row['address_postal_zip_code'];

                         $job_location=$brand.$CSalesManager->htmlChars($quo_statement_row['address_street_address_1'].$address_2.$postal_code_job);   

                         $quotation_rev_f ="";$quotation_rev_r="";
                         $quotation_rev_f = substr($quotation_rev_arr[0]['quotation_revision'],0,10);
                         $total = round($CQuotationManager->get_total_tax_and_paid($quotation_revision_id, $CQuotationManager->get_quotation_item_total($quotation_id, $quotation_revision_id)),2);
                         $totalCustomer+=$total;
                         if(strlen($quotation_rev_arr[0]['quotation_revision'])>10)
                            $quotation_rev_r = '<br>'.substr($quotation_rev_arr[0]['quotation_revision'],9);
                   $html_body= '<table width="100%" cellspacing="0" cellpadding="4" border="1"><tr id="title_head">
                                <td width="11%">'.date('d-m-Y',  strtotime($quo_statement_row['quotation_date'])).'</td>
                                <td width="13%">'.$quotation_rev_f.$quotation_rev_r.'</td>
                                <td width="35%">'.$quo_statement_row['company_name'].'
                                       <div style="color:#666;font-size:0.9em;">'.$job_location.'</div></td>
                                <td width="11%">'.$status_arr[$quo_statement_row['quotation_status']].'</td>
                                <td width="20%">'.$quo_statement_row['quotation_subject'].'</td>
                                <td align="right" width="11%">'.number_format($total,2).'</td>
                            </tr></table>';
                    if($pdf->GetY()>$pdf->getPageHeight()-40){
                        $pdf->AddPage();
                        $pdf->writeHTMLCell('','','','',$html_head);
                        $pdf->ln();
                    }
                    $pdf->writeHTMLCell('','','','',$html_body);
                    $pdf->ln();
                }
            }
            if(count($quo_statement_arr)>0)
            {
                $html_footer = '&nbsp;<table width="100%" cellspacing="0" cellpadding="4" border="1">
                        <tr>
                            <td width="69.7%"><b>'.$customer_data['company_name'].' ('.$job_location_customer.')</b></td>
                            <td align="right" width="30.8%"><b>'.number_format($totalCustomer,2).'</b></td>
                        </tr>
                    </table><br>';
                $pdf->writeHTML($html_footer);
            }
        }

        ob_end_clean();
        $pdf->Output('quotation.pdf', 'I');
    }
    
    function print_credit_note()
    {
        global $ocio_config;
        require_once (DP_BASE_DIR . '/modules/sales/CHeaderRCreditNote.php');
        
        $pdf = new MYPDFRCredit(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Quotation Statement');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, 17);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(true);
        $pdf->setFontSubsetting(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 9.5);
        
        $pdf->AddPage();
        
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        require_once (DP_BASE_DIR."/modules/sales/CCreditNoteManager.php");
        
        $CTax = new CTax();
        $CCreditManager = new CCreditNoteManager();
                
        $customer_id=false;$from_date=false;$to_date=false;
        if(isset($_GET['customer_id']))
            $customer_id = $_GET['customer_id'];
        if(isset($_GET['from_date']))
            $from_date = $_GET['from_date'];
        if(isset($_GET['to_date']))
            $to_date = $_GET['to_date'];
        $status = false;
        if(isset($_GET['invoice_status_id']))
            $status = $_GET['invoice_status_id'];

        $credit_arr = $CCreditManager->list_db_creditNote_report($customer_id,$from_date,$to_date,1,$status);

        $html = "";
        $html.= '<table width="100%" cellspacing="0" cellpadding="2" borde>
                    <thead>
                        <tr style="font-weight:bold;">
                            <td align="center" width="6%">S/No</td>
                            <td width="20%">Name</td>
                            <td width="14%">Credit Note No</td>
                            <td align="center" width="12%">Dated</td>
                            <td align="center" width="12%">Invoice Nos</td>
                            <td align="right" width="12%">Amount</td>
                            <td width="1%"></td>
                            <td align="right" width="10%">GST</td>
                            <td width="1%"></td>
                            <td align="right" width="12%">Total</td>
                        </tr>
                    </thead>';
        $i=0;$totalCredit = 0;$amountCredit = 0;$taxCredit=0;
        foreach ($credit_arr as $credit_item) {
            $i++;
            $totalAmount_arr = $CSalesManager->total_creditNote_amount($credit_item['credit_note_id']);
            $amount=$CSalesManager->round_up($totalAmount_arr[0]['totalAmount']);

            $tax_arr = $CTax->list_tax();
            $tax_value = 0;$total_tax=0;
            foreach ($tax_arr as $tax_row) {
                $selected = "";
                if($tax_row['tax_id']==$credit_item['credit_note_tax_id']){
                    $tax_value = $tax_row['tax_rate'];
                }
            }
            $total_tax = $amount*($tax_value/100);
            if($credit_item['tax_edit_value']!=0)
            {
                $total_tax = $credit_item['tax_edit_value'];
            }

            $total = $amount + $total_tax;

            $totalCredit += $total;
            $amountCredit +=$amount;
            $taxCredit +=$total_tax;

            $html.=        '<tr>
                            <td align="center" width="6%">'.$i.'</td>
                            <td width="20%">'.$credit_item["company_name"].'</td>
                            <td width="14%">'.$credit_item["credit_note_no"].'</td>
                            <td align="center" width="12%">'.date($ocio_config['php_abms_date_format'],strtotime($credit_item["credit_note_date"])).'</td>
                            <td align="center" width="12%">'.$credit_item['invoice_no'].'</td>
                            <td align="right" width="12%">$'.number_format($amount,2).'</td>
                            <td width="1%"></td>
                            <td align="right" width="10%">$'.number_format($total_tax,2).'</td>
                            <td width="1%"></td>
                            <td align="right" width="12%">$'.number_format($total,2).'</td>
                        </tr>';
        }
        $html.='<tr style="font-weight:bold;">
                <td colspan="5" width="61%">Total</td>
                <td align="right" style="border-top:1px soild #000;" width="14%">$'.number_format($amountCredit,2).'</td>
                <td width="1%"></td>
                <td align="right" style="border-top:1px soild #000;" width="10%">$'.number_format($taxCredit,2).'</td>
                <td width="1%"></td>
                <td align="right" style="border-top:1px soild #000;" width="14%">$'.number_format($totalCredit,2).'</td>
        </tr>';
        $html.= '</table>';
        
        $pdf->writeHTMLCell('','','','50',$html);
        ob_end_clean();
        $pdf->Output('credit_note.pdf', 'I');
    }
    
    public function print_profit_report_pdf()
    {
        global $CCreditManager;
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Profit Report');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, 17);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        $pdf->setFontSubsetting(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 10.5);
        
        $pdf->AddPage();
        $html = "";
       
        global $ocio_config,$CReportManager,$CSalesManager,$CInvoiceManager,$CCompany,$AppUI;
        require_once (DP_BASE_DIR."/modules/sales/css/report.css.php");
    
        require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
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
                $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="120" />&nbsp;&nbsp;&nbsp;'; 
            }
            else
            {
                $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" width="170" />';
            }
            $img5 = '<img height="25" src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
            $img6 = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" />';
        }

        //suplier
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

            $supplier = $sales_owner_name .' ';
            $supplier.= $sales_owner_address.' ';
            $supplier .= $sales_owner_code;
         $string_date = date('d/m/Y H:i:s');
         $date = date('d', strtotime($string_date)).'/'.date('m', strtotime($string_date)).'/'.date('Y', strtotime($string_date));
         $time = date('H', strtotime($string_date)).':'.date('i', strtotime($string_date)).':'.date('s', strtotime($string_date));
         $from_date = $_GET['from_date'];
        $to_date =  $_GET['to_date'];
      
        $department = new CDepartment();
        $getDepartment = $department->getDeparmentByUser($AppUI->user_id);
        
       $date_view = "";
        if($from_date!="")
            $date_view.='From : '.date($ocio_config['php_abms_date_format'],  strtotime($from_date));
        if($to_date!="")
            $date_view.=' To:'.date($ocio_config['php_abms_date_format'],  strtotime($to_date));
        if($from_date=="" && $to_date =="")
            $date_view = ' To: '.date('d/m/Y');
        
        $CCompany = new CompanyManager();
        $rows = $CCompany->getCompanyJoinDepartment();
        $template_pdf_7 = $CSalesManager->get_template_server(1);
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
                       From: '.date('d.M.Y',  strtotime($from_date)).'&nbsp;&nbsp;&nbsp;&nbsp;
                       To: '.date('d.M.Y',  strtotime($to_date)).'
                   </td>
                </tr>
            </table>';
       }
       $html .='<table >';
       
       $html .='<tr><td style="font-size:20px;text-align: center; width:100%">'
               .$img. '</td>'
               . '</tr>';
       
                
       $html .='<tr><td style="font-size:14px;text-align: center; width:100%;">'
               .$supplier. '</td>'
               . '</tr><br><br>';
       $html .='<tr><td style="font-size:20px;text-align: center; width:100%">Profit & Loss Statement</td>'
               . '</tr>';
       $html .='<tr><td style="font-size:14px;text-align: center; width:100%">'.$date_view.'</td>'
               . '</tr>';
       $html .='<tr><td style="font-size:14px;text-align: left; width:100%">'.$string_date.'</td>'
               . '</tr>';
//       $html .='<tr><td style="font-size:14px;text-align: left; width:100%;border-bottom: 1px solid #CCCCCC;">'.$time.'</td>'
//               . '</tr>';
       $html .= '<div style="width:100%;border-bottom: 1px solid #CCCCCC;margin-top:0px;"></div><br>';
       $html .='<tr><td style="font-size:14px;text-align: left; width:100%;border-bottom: 1px solid #CCCCCC;">'
               . '<table style="border-top: 1px solid #CCCCCC;margin-top:10px" >';
                        
                        $info_depart='<tr><td>';
                        $info_depart.= '<table   width="100%"  border="0" style="margin-top:2px; padding-left:20px">';
                         $totalQuotation = 0;
                              foreach ($rows as $data)
                              {
                                  if($data['dept_id'] > 0)
                                  {
                                      $totalInvoice = $CInvoiceManager->calculateTotalInvoiceByDepartment($data['dept_id'],$from_date,$to_date);
                        
                                    $totalSum = $total+$totalInvoice;
                                  
                                      $info_depart .= '<tr>'
                                          . '<td style="width:40%">'.$data['dept_name'].'</td>'
                                          . '<td align="right">$'.number_format($totalSum,2, '.', ',').'</td><td style=""></td>'
                                              . '</tr>';
                                      $totalQuotation += $totalSum;

                                   }
                              }
                            //Tinh payment cua invoice khong co department 
                            $totalInvoiceNotDepartment = $CInvoiceManager->calculateTotalInvoiceByDepartment(0,$from_date,$to_date);
                            $totalCredit += $totalInvoiceNotDepartment;

                            //tinh tong invoice sales
                            $html .= '<tr><td><table   width="100%"  border="0" style="margin-top:2px;">';
                            $html .= '<tr><td style="width:70%;">Income</td>';
                            $html .= '<td style="border-bottom: 1px solid #CCCCCC;" >$'.number_format($totalQuotation+$totalInvoiceNotDepartment,2, '.', ',').'</td></tr>';

                            $html .= $info_depart;

                            $html .= '<tr>';
                            $html .= '<td style="width:40%">Other</td>';

                            $html .= '<td align="right">$'.number_format($totalInvoiceNotDepartment,2, '.', ',').'</td>';
                            $html .= '</tr>';
                            $html .= '</table>'
                        . '</td></tr>';

                        //Less creditnote
                        $totalCredit = $CCreditManager->totalCreditNoteIsInvoice($from_date,$to_date);
                        
                        $html .= '<tr><td><table   width="90%"  border="0" style="margin-top:2px;">';
                        $html .= '<tr><td style="width:70%;">Less Credit Notes</td>';
                        $html .= '<td style="border-bottom: 1px solid #CCCCCC;" >$'.number_format($totalCredit,2, '.', ',').'</td></tr>';
                        $html .= '</td></tr></table><br>';//
                        //
                        //
                        //total income = income - less creditnote
                        $total_income = $totalQuotation+$totalInvoiceNotDepartment-$totalCredit;
                        $html .= '<tr><td><table   width="90%"  border="0" style="margin-top:2px;">';
                        $html .= '<tr><td style="width:70%;">Total Income</td>';
                        $html .= '<td style="border-bottom: 1px solid #CCCCCC;" >$'.number_format($total_income,2).'</td></tr>';
                        $html .= '</td></tr></table><br>';//
//                        
                //   Cost of Sales
                    $html .=  '<tr><td>Cost of Sales</td></tr>';   
                    $html .=  '<tr><td style="margin-top:2px; padding-left:10px">Direct Job Costs</td></tr>';   
                    $html .=  '<tr><td>'
                            .'<table   width="100%"  border="0" style="margin-top:2px; padding-left:20px">';
                            $totalPo = 0;
                            foreach ($rows as $data)
                            {
                                if($data['dept_id'] > 0)
                                {
                                    $po_invoice = new po_invoice();
                                    $SIDepartment = $po_invoice->calculatorSIDepartment($from_date,$to_date,$data['dept_id']);
                                    $html .= '<tr>'
                                        . '<td style="width:30%">'.$data['dept_name'].'</td>'
                                        . '<td align="right">$'.number_format($SIDepartment,2, '.', ',').'</td>'
                                            . '</tr>';
                                    $totalPo += $SIDepartment;

                                 }
                            }
                    $totalMater = 0;
                // Lấy SI không thuộc department
                   $SINODepartment = $po_invoice->calculatorSIDepartment($from_date,$to_date,-10);
                   $totalPo += $SINODepartment;
                   $html .= '<tr>'
                                . '<td style="width:30%">Orther</td>'
                                . '<td align="right">$'.number_format($SINODepartment,2, '.', ',').'</td>'
                                    . '</tr>';
            
                    $html .= '</table></td></tr>';   
                    
                    //derect  job cost
                    $html .= '<tr><td><table   width="100%"  border="0" padding-right:20px>';
                    $html .= '<tr><td style="width:35%;">Total Direct Job Costs</td><td></td>';
                    $html .= '<td style="border-bottom: 1px solid #CCCCCC;" align="right">$'.number_format($totalPo,2, '.', ',').'</td></tr>';
//                    $html .= '<tr><td style="width:60%">Materials-M&E</td><td></td>';
                    
//                    $html .= '<td style="border-bottom: 1px solid #CCCCCC;">$'.$totalMater.'</td></tr>';
                    $html .= '</table></td></tr>';
        
                    //total cost sales
                    $TotalCostOfSales = $totalPo +$totalMater ;
                    $html .= '<tr><td><table   width="100%"  border="0" >';
                    $html .= '<tr><td style="width:58%">Total Cost Of Sales</td><td></td>';
                    $html .= '<td style="border-bottom: 1px solid #CCCCCC; " align="right">$'.number_format($TotalCostOfSales,2).'</td></tr>';
                    $html .= '</table><br></td></tr>';
                    $Gross_profit = $total_income - $TotalCostOfSales;
                    
                     //Gross profit
                    $html .= '<tr><td><table   width="100%"  border="0" style="margin-top:2px;">';
                    $html .= '<tr><td style="width:76%;">Gross profit</td>';
                    $html .= '<td style="border-bottom: 1px solid #CCCCCC;" align="right">$'.number_format($Gross_profit,2, '.', ',').'</td></tr>';
                    $html .= '</table></td></tr><br>';
                 
        $html .= '</td></tr>'
               
               .'</table>';
               
         
        //expenses
               $html .= '<div style="width:100%;border-bottom: 1px solid #CCCCCC;padding:5px;"></div><br>';

        $category_arr = dPgetSysVal('Categories');
        asort($category_arr);
        $html .='<tr><td><table style="width:100%;border-top: 1px solid #CCCCCC; ">';
            $html .= '<tr><td style="margin-top:20px;">Expenses</td></tr>';
            $html .= '<tr><td><table   width="100%"  border="0" style="margin-top:2px; padding-left:20px">';
                $totalEx = 0;
                foreach ($category_arr as $key => $value)
                {
                    $expensesManage = new ExpensesManager();
                    $ExBycategory = $expensesManage->totalExByCategory($key,$from_date,$to_date);

                    $html .= '<tr>'
                            . '<td style="width:58%">'.$value.'</td>'
                            . '<td align="right">$'.number_format($ExBycategory,2, '.', ',').'</td>'
                         . '</tr>';
                    $totalEx += $ExBycategory;


               }    
             $totalNoCategory = $expensesManage->totalExByCategory(-1,$from_date,$to_date);
            $totalEx +=$totalNoCategory;
                    $html .= '<tr>'
                            . '<td style="width:58%">'.$value.'</td>'
                            . '<td align="right">$'.number_format($totalNoCategory,2, '.', ',').'</td>'
                         . '</tr>';
            echo '</table>';
            $html .= '</table></td></tr>';
            
            //total expenese
            $html .='<tr><td><table   width="100%"  border="0" style="">
                    <tr><td style="width:76%;">Total Expenses</td>
                <td style="border-bottom: 1px solid #CCCCCC;" align="right">$'.number_format($totalEx,2, '.', ',').'</td></tr>
                    </table></td></tr>';
        $html .='</td></tr></table>';
        
         
        
       
        //total operating
       $html .= '<div style="width:100%;border-bottom: 1px solid #CCCCCC;padding:5px;"></div><br>';

        $totalOperating = $Gross_profit - $totalEx;
            $html .= '<tr><td style="width:100%;border-top: 1px solid #CCCCCC; margin-top:20px; "> ';
            $html .= '<table   width="88%"  border="0" style="margin-top:20px;">';
            $html .= '<tr><td >Operating Profit</td>';
            $html .= '<td style="border-bottom: 1px solid #CCCCCC;" align="right">$'.  number_format($totalOperating,2, '.', ',').'</td></tr>';
            $html .= '</table></td></tr><br>';
        
        
        //Other income
//        $Orther_income = 0;
//            $html .= '<tr><td>';
//            $html .= '<table   width="100%"  border="0" style="margin-top:2px;">';
//            $html .= '<tr><td style="width:45%;">Other Income</td>';
//            $html .= '<td style="border-bottom: 1px solid #CCCCCC;">$'.($Orther_income).'</td></tr>';
//            $html .= '</table></td></tr><br>';
            
        //Orther expenses
            $other_ex= 0;
//          
//         $html .= '<tr><td><table   width="100%"  border="0" style="margin-top:2px;">';
//            $html .= '<tr><td style="width:45%;">Other Expenses</td>';
//            $html .= '<td style="border-bottom: 1px solid #CCCCCC;">$'.($other_ex).'</td></tr>';
//            $html .= '</table></td></tr><br>';
            
        //Net Profit/(loss)
            $net_profit = $Orther_income + $totalOperating;
           
         $html .= '<tr><td><table   width="88%"  border="0" style="margin-top:2px;">';
            $html .= '<tr><td style="">Net Profit/(loss)</td>';
            $html .= '<td style="border-bottom: 1px solid #CCCCCC;" align="right">$'.  number_format($net_profit,2, '.', ',').'</td></tr>';
            $html .= '</table></td></tr><br>';
//       
        
        $html .='</td></tr></table>';
        $pdf->writeHTML($html);
        ob_end_clean();
       $now = getdate();
  
    $date=$now['year'].'-'.$now['mon'].'-'.$now['mday'];
//    echo $date;
        $Date = date($date);
        $a = date_format($date, "dmY");
        $date = date_create($date);

        
        $pdf->Output('profit&loss_'.date_format($date, 'dmY').'.pdf', 'I');
    }
    
    function cron_aging_report_pdf_file($dateCron,$dir,$dateTime)
    {
        
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
        require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
        
        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Aging report');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, 17);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        $pdf->setFontSubsetting(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 10);
//       
//        
        global $AppUI;
        
        $CInvoiceManager=new CInvoiceManager();
        $CSalesManager=new CSalesManager();
        $customer_id = false;
        if(isset($_GET['customer_id']) && $_GET['customer_id']!="null")
            $customer_id = $_GET['customer_id'];
        
        $companyArr= $this->getCompanyByInvoice($customer_id);
        $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
        
        $data_from1=false;$data_to1=false;
        $date_fomat = "";
//        if($_GET['data_from']!=""){
//            $date_from1 = $_GET['data_from'];
//            $date_fomat.="From: ".date('d/m/Y', strtotime($date_from1))."&nbsp;&nbsp;&nbsp;";
//        }
        if($_GET['data_to']!=""){
            $date_to1 = $_GET['data_to'];
            $date_fomat.="Date: ".date('d.M/Y', strtotime($date_to1));
            $dateCron=$date_fomat;
        }
        else
            $date_fomat=$dateCron;
        $include_address=2;
//        
        $html= '<table width="100%" border="0" callpadding="2" cellspacing="0" id="aging_report" class="tbl">
                <tr>
                    <td colspan="5" align="left">'.$date_fomat.'</td>
                    <td colspan="2" align="right">'.$dateTime.'</td>
                </tr>
                <tr valign="top">
                    <td colspan="7" style="font-size:2em;" align="center" >Aging report</td>
                </tr>
                <thead >
                <tr>
                    <td width="18%" height="25"><b>'.$AppUI->_("Number").'</b></td>
                    <td width="13%"><b>'.$AppUI->_("Date").'</b></td>
                    <td width="13%" align="right"><b>'.$AppUI->_("0 - 30 Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("31 - 60 Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("61 - 90 Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("91 + Days").'</b></td>
                    <td width="1%">&nbsp;</td>
                    <td width="13%" align="right"><b>'.$AppUI->_("Balance").'</b></td>
               </tr>
               </thead><tbody>
               ';
//               
        foreach ($companyArr as $company_row)
        {
            $agingArr = $this ->list_agingreport($company_row['company_id'],false,false,false,$date_fomat);
            $job_location="";
            foreach ($agingArr as $aging_row)
            {
                    $invoice_id=$aging_row["invoice_id"];
                    $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
                    $total_show = $CInvoiceManager ->get_invoice_item_total($invoice_id, $invoice_revision_id);
                    //$total_item_show = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show);
                    $total_item_show = $CInvoiceManager->get_total_tax_and_paid_date($invoice_revision_id,$total_show, $date_to1);
                            $start =  strtotime($aging_row[invoice_date]);
                            $end = strtotime(date("Y/m/d"));
                            $day_diff=  round(($end - $start)/86400);
//
                        if($day_diff>=0 && $day_diff<=30)
                        {

                            $day30=$total_item_show;
                            $day60=0;$day90=0;$day=0;$dayBalance=$day30;
                        }
                        else if($day_diff>30 && $day_diff<=60)
                        {
                            $day60=$total_item_show;
                            $day30=0;$day90=0;$day=0;$dayBalance=$day60;

                        }
                        else if($day_diff>60 && $day_diff<=90)
                        {
                            $day90=$total_item_show;
                            $day30=0;$day60=0;$day=0;$dayBalance=$day90;
                        }
                        else if( $day_diff>90)
                        {
                            $day=$total_item_show;
                            $day30=0;$day60=0;$day90=0;$dayBalance=$day;
                        }
                        $tmpDay30 += $day30; $tmpDay60 +=$day60; $tmpDay90 += $day90;$tmpDay +=$day;$tmpBalance += $dayBalance;
                        $totalBalance = round($tmpBalance,2); $totalDay30=round($tmpDay30,2); $totalDay60=round($tmpDay60,2); $totalDay90=round($tmpDay90,2); $totalDay= round($tmpDay,2);
                            if($dayBalance>0){
                                // get joblocation
//                                $job_location_arr =  $CSalesManager->get_address_by_id($aging_row['job_location_id']);
                                if($include_address==2){
                                        $brand="";
                                    if($aging_row['address_branch_name']!="")
                                        $brand=$aging_row['address_branch_name'].' - ';
                                    $address_2="";
                                    if($aging_row['address_street_address_2']!="")
                                        $address_2= ', '.$aging_row['address_street_address_2'];
                                    $postal_code_job = '';
                                    if($aging_row['address_postal_zip_code']!="")
                                        $postal_code_job .=', Singapore '.$aging_row['address_postal_zip_code'];
                                    
                                    $job_location=$brand.$CSalesManager->htmlChars($aging_row['address_street_address_1'].$address_2.$postal_code_job);
                                    if($aging_row['address_phone_1']!="")
                                        $job_location.=", Phone: ".$aging_row['address_phone_1'];
                                    elseif($aging_row['address_phone_2']!="")
                                        $job_location.=", Phone: ".$aging_row['address_phone_2'];
                                }
                                $h_no ="";$h_job = "";
                                if($job_location!=""){
                                        $h_job ='<tr>
                                            <td style="font-size:0.9em;color:#666" colspan="11" height="30">'.$job_location.'</td>
                                        </tr>';
                                }else{
                                    $h_no = 'height="30"';
                                }

                                $html.='
                                        <tr>
   
                                            <td width="18%" height="25">'.$aging_row["invoice_no"].'</td>
                                            <td width="13%">'.date("d/M/Y",strtotime($aging_row['invoice_date'])).'</td>
                                            <td width="13%" align="right">$'.number_format($day30, 2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day60,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day90,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($day,2).'</td>
                                            <td width="1%">&nbsp;</td>
                                            <td width="13%" align="right">$'.number_format($dayBalance,2).'</td>
                                        </tr>
                                        '.$h_job.'';
                            }
                            

                }
               $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
               if(count($agingArr) == 0){
                    $totalDay30=0;$totalDay60=0;$totalDay90=0;$totalDay=0;$totalBalance=0;
                }
                if($totalBalance){
                    $html.='
                            <tr style="font-weight:bold;" valign="top" >
                               <td colspan="2"  height="40">'.$company_row['company_name'].'</td>
                               <td   align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay30,2).'</td>
                                   <td>&nbsp;</td>
                               <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay60,2).'</td>
                                   <td >&nbsp;</td>
                               <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay90,2).'</td>
                                   <td >&nbsp;</td>
                               <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay,2).'</td>
                                   <td >&nbsp;</td>
                               <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalBalance,2).'</td>
                            </tr><br>
                            ';
                        $aging_total_30 += $totalDay30;
                        $aging_total_60 += $totalDay60;
                        $aging_total_90 += $totalDay90;
                        $aging_total_day += $totalDay;
                        $aging_total_banlance += $totalBalance;
                }
            }
            
            $html.='<tr style="font-weight:bold;">
                    <td colspan="2" style="font-size:1.1em;">Total</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1.1em;">$'.number_format($aging_total_30,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1.1em;">$'.number_format($aging_total_60,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1.1em;">$'.number_format($aging_total_90,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1.1em;">$'.number_format($aging_total_day,2).'</td>
                        <td >&nbsp;</td>
                    <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:1.1em;">$'.number_format($aging_total_banlance,2).'</td>
            </tr>';
            $html.='</tbody></table>';
           
            $pdf->AddPage(); 
//          // get esternal file content
            $pdf->writeHTML($html, true, false, true, false, '');
            ob_end_clean();
          
            $pdf->Output($dir.'/aging_report_'.$dateCron.'.pdf','F'); // Save file PDF vao forder

    }
    
    function read_pdf($file)
    {
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');

        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Aging report');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(20, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, 17);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        $pdf->setFontSubsetting(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('helvetica', '', 10);
//                    window.open("<?php echo DP_BASE_URL"+file,'_blank','');

        if (file_exists($file)) {
    
            header('Content-Description: File Transfer');
            header("Content-type:application/pdf");
            header("Content-Disposition:attachment;filename=downloaded.pdf");
        //    header('Content-Type: application/octet-stream');
        //    header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
    }

//        print file_get_contents( );
	}
    function getBankReport($customer_value_arr=false,$from_date=false,$to_date=false)
    {
        $q = new DBQuery();
        $q->addTable('bank_cheque');
        $q->addQuery('*');
       // $q->addJoin('clients', 'c', 'bc.bank_cheque_description=c.company_id');
//        if($bank_account_id){
//            $q->addWhere('bank_account_id='.int($bank_account_id));
//        }
        if($customer_value_arr){
            $q->addWhere('bank_cheque_description IN '.$customer_value_arr);
        }
        if($from_date){
            $q->addWhere('bank_cheque_date >="'.$from_date.'"');
        }
        if($to_date){
            $q->addWhere('bank_cheque_date <="'.$to_date.'"');
        }
        $q->group_by = 'bank_account_id';
        return $q->loadList();
    }
    function calculateTotalDebit($bank_account_id,$customer_value_arr=false,$from_date=false,$to_date=false){
        $q = new DBQuery();
        $q->addTable('bank_cheque');
        $q->addQuery('*');
        $q->addWhere('bank_account_id ='.intval($bank_account_id));
        if($customer_value_arr){
            $q->addWhere('bank_cheque_description IN '.$customer_value_arr);
        }
        if($from_date){
            $q->addWhere('bank_cheque_date >="'.$from_date.'"');
        }
        if($to_date){
            $q->addWhere('bank_cheque_date <="'.$to_date.'"');
        }
        $row = $q->loadList();
        $total = 0;
        foreach ($row as $value) {
            $total+=$value['bank_cheque_debit'];
        }
        return $total;
    }
    function calculateTotalCredit($bank_account_id,$customer_value_arr=false,$from_date=false,$to_date=false){
        $q = new DBQuery();
        $q->addTable('bank_cheque');
        $q->addQuery('*');
        $q->addWhere('bank_account_id ='.intval($bank_account_id));
        if($customer_value_arr){
            $q->addWhere('bank_cheque_description IN '.$customer_value_arr);
        }
        if($from_date){
            $q->addWhere('bank_cheque_date >="'.$from_date.'"');
        }
        if($to_date){
            $q->addWhere('bank_cheque_date <="'.$to_date.'"');
        }
        $row = $q->loadList();
        $total = 0;
        foreach ($row as $value) {
            $total+=$value['bank_cheque_credit'];
        }
        return $total;
    }
    function getCompanyByInvoice1($customer_id=false){
            $q = new DBQuery();
            $q->addTable('clients', 'tbl1');
            $q->addQuery('tbl1.company_id, tbl1.company_name');
            $q->addJoin('sales_invoice', 'tbl3', "tbl1.company_id = tbl3.customer_id",'inner');
            $q->addWhere('tbl1.company_id = tbl3.customer_id');
            if($customer_id){
                $q->addWhere('tbl1.company_id IN '. $customer_id);
            }
            $q->addGroup('tbl1.company_id');
            $q->addOrder('tbl1.company_name ASC');
            return $q->loadList();
        
    }
    function getSupplierByPOInv($supplier_id_arr=false,$draft=true,$po_id=false)
    {
        
        $q = new DBQuery();
        $q->addTable('po_invoice','p');
        $q->addQuery('cl.company_id,cl.company_name,p.*');
        $q->addJoin('clients','cl','p.po_invoice_supplier_id=cl.company_id','INNER');
        if(!$draft)
            $q->addWhere ('p.po_status <> 1');
        if($po_id)
            $q->addWhere ('p.po_id='.intval ($po_id));
        if($supplier_id_arr)
            $q->addWhere('p.po_invoice_supplier_id IN '.$supplier_id_arr);
        $q->addGroup('cl.company_id');
        
        return $q->loadList();
    }
    function create_cash_flow_report_pdf(){
        require_once (DP_BASE_DIR . '/lib/tcpdf_new/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Cash Flow Summary Report');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(5, 5, 15, 5);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        //$pdf->setPage(1, true);
        $pdf->setLanguageArray($l);
        $pdf->SetFont('times', '', 10);
        $CReportManager = new CReportManager();
        $bankAccount = new CBankAccount();
        $expensesManage = new ExpensesManager();
        $CPOPayment = new CPOPayment();
        global $AppUI,$CSalesManager,$CInvoiceManager,$CPOManager,$po_invoice,$CPaymentManager,$ExpensesCategory;
        $customer_value_arr =false ;
        $from_date = false;
        $to_date =false;
        $from_date_fomart ="";
        $to_date_fomart ="";
        $po_status_arr = dPgetSysVal('openBankAccount');
        $date_search=false;
        if(count($po_status_arr) > 0){
            $date_search= date('Y-m-d',  strtotime($po_status_arr[1]));
       }
//        if(isset($_GET['customer_id_arr']) && ($_GET['customer_id_arr'])!=null)
//            $customer_value_arr = "(0,".$_GET['customer_id_arr'].")";
        if(isset($_GET['customer_id_arr'])){
            $customer_id_arr = $_GET['customer_id_arr'];
            if($customer_id_arr!="null"){
                $customer_value_arr = "(".$_GET['customer_id_arr'].")";
            }

        }
        
        if(isset($_GET['from_date']) && $_GET['from_date']!=""){
            $from_date = $_GET['from_date'];
            $from_date_fomart = 'From: '.date('d.M.Y',strtotime($from_date));
        }
        
        if(isset($_GET['to_date']) && $_GET['to_date']!=""){
            $to_date = $_GET['to_date'];
            $to_date_fomart = 'To: '.date('d.M.Y',strtotime($to_date));
        }
        if($_GET['to_date']=="" && $_GET['from_date']!=""){
            $to_date_fomart = 'To: '.date('d.M.Y');
        }
        if($_GET['to_date']=="" && $_GET['from_date']==""){
            $date_fomat = "";
        }  else {
             $date_fomat='<tr valign="top">
                    <td colspan="4" align="center">'.$from_date_fomart.' &nbsp;&nbsp;&nbsp;'.$to_date_fomart.'</td>
                </tr>';
        }
        
        //$html .= '<tr><td colspan="4"><b>Sales Invoice GST Report</b></td></tr>';
    //    $gst_report_arr = $CReportManager->get_gst_report($customer_value_arr,$from_date,$to_date);
        //$invoice_revision_tax = $gst_report_row['invoice_revision_tax'];
        
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
     //   $CTax = new CTax();
         $html.='<table width="100%"  callpadding="3" cellspacing="4" id="aging_report" class="tbl" >
                <tr><td align="right" colspan="4">'.date("d.M.Y").'</td></tr>
                <tr>
                    <td colspan="7" align="center" style="font-size:1.6em">Cash Flow Summary Report</td>
                </tr>
                '.$date_fomat.'<br /><br />
               </table>';
            $html.= '<div id="cash_report" style="margin-left:50px;">';
////// Cash in Bank ///////
        $html.= '<div style="margin-top:20px;margin-bottom:10px;" ><b>Cash in Bank</b></div><br/>';
        $html.= '<table width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
                <thead >
                    <tr style="font-weight:bold;background-color:#dfeffc" height="20px" id="title_head">
                        <td align="left" >Bank Account</td>
                        <td align="right">Balance</td>
                        <td align="right">Debit</td>
                        
                        
                        <td align="right">Total</td>
                    </tr>
                </thead>
                <tbody>';
        $bank_account_arr = $bankAccount->getBankAccount();
     //   $bank_cheque = $CReportManager->getBankReport($customer_value_arr, $from_date, $to_date);

       $TotalDebit = 0;
       $TotaCredit = 0;
       $TotalBalance = 0;
       $TotalAll = 0;
        foreach ($bank_account_arr as $value) {
           // print_r($bank_cheque[0]['bank_account_id']);
            $bank_account_id = $value['bank_account_id'];
            if($value['bank_account_name'] !="Others")
            {
            $bank_balance = $value['bank_ob'];
    
            if($date_search != "" && strtotime($from_date) < strtotime($date_search)){
                $bank_debit = $CReportManager->calculateTotalDebit($bank_account_id,$customer_value_arr, $date_search, $to_date);
                $bank_credit = $CReportManager->calculateTotalCredit($bank_account_id,$customer_value_arr, $date_search, $to_date);
            }else{
                $bank_debit = $CReportManager->calculateTotalDebit($bank_account_id,$customer_value_arr, $from_date, $to_date);
                $bank_credit = $CReportManager->calculateTotalCredit($bank_account_id,$customer_value_arr, $from_date, $to_date);
            }
        //    $bank_balance = $bank_ob - $bank_debit + $bank_credit;
            $bank_account_arr = $bankAccount->getBankAccount($bank_account_id);
            $html.='<tr  id="title_body">';
            $html.='<td>'.$bank_account_arr[0]['bank_account_name'].'</td>';

           // $html.='</tr>';
            $sales_debit = 0;
            if($date_search != "" && strtotime($from_date) < strtotime($date_search)){
                $sales_credit = $CPaymentManager->getSalesPaymentByCustomer($bank_account_id,$customer_value_arr,$date_search,$to_date);
            }else{
                 $sales_credit = $CPaymentManager->getSalesPaymentByCustomer($bank_account_id,$customer_value_arr,$from_date,$to_date);
            }
            if($date_search != "" && strtotime($from_date) < strtotime($date_search)){
                $supplier_debit = $CPOPayment->getPaymentByCustomer($bank_account_id,$customer_value_arr,$date_search,$to_date);
            }else{
                $supplier_debit = $CPOPayment->getPaymentByCustomer($bank_account_id,$customer_value_arr,$from_date,$to_date);
            }
            $supplier_credit = 0;
    
            if($date_search != "" && strtotime($from_date) < strtotime($date_search)){
                $expenses_debit = $expensesManage->getExpensesByCustomer($customer_value_arr, $date_search, $to_date, $bank_account_id);
            }else{
                $expenses_debit = $expensesManage->getExpensesByCustomer($customer_value_arr, $from_date, $to_date, $bank_account_id);
            }
            $expenses_credit = 0;
	/*		
            if($date_search != "" && strtotime($from_date) < strtotime($date_search)){
                $bank_debit1 = $CReportManager->calculateTotalDebit($bank_account_id,$customer_value_arr,$from_date, $date_search);
                $bank_credit1 = $CReportManager->calculateTotalCredit($bank_account_id,$customer_value_arr, $from_date, $date_search);
            }else{
                $bank_debit1 = $CReportManager->calculateTotalDebit($bank_account_id,$customer_value_arr,$date_search ,$from_date);
                $bank_credit1 = $CReportManager->calculateTotalCredit($bank_account_id,$customer_value_arr, $date_search, $from_date);
            }
            if($date_search != "" && strtotime($from_date) < strtotime($date_search)){
                $sales_credit1 = $CPaymentManager->getSalesPaymentByCustomer($bank_account_id,$customer_value_arr,$from_date,$date_search);
            }else{
                 $sales_credit1 = $CPaymentManager->getSalesPaymentByCustomer($bank_account_id,$customer_value_arr,$date_search,$from_date);
            }
            */
                $supplier_debit1 = $CPOPayment->calculateTotalPaymentSupplierByBankCustomer($customer_value_arr,$from_date,$bank_account_id,$date_search);
        /*    
            if($date_search != "" && strtotime($from_date) < strtotime($date_search)){
                $expenses_debit1 = $expensesManage->getExpensesByCustomer($customer_value_arr, $from_date, $date_search, $bank_account_id);
            }else{
                $expenses_debit1 = $expensesManage->getExpensesByCustomer($customer_value_arr, $date_search, $from_date, $bank_account_id);
            }
		*/
			 $sales_credit1 = $CPaymentManager->getSalesPaymentByCustomer1($bank_account_id,$customer_value_arr,$from_date,$date_search);
            $expenses_debit1 = $expensesManage->getAmountExpensesBankAccount($customer_value_arr, $from_date, $date_search, $bank_account_id);
            $info_credit = $CReportManager->caculatorCreditBeforDate($bank_account_id,$customer_value_arr,$from_date,$date_search);
            $openning = $bank_balance+$info_credit['credit']-$info_credit['debit'];
            $total_debit = $bank_debit + $sales_debit + $supplier_debit + $expenses_debit;
            $total_credit = $bank_credit + $sales_credit + $supplier_credit + $expenses_credit;
           // $bank_balance1 = $bank_balance + $bank_credit1 - $bank_debit1 + $sales_credit1 - $supplier_debit1 - $expenses_debit1;
			$bank_balance1 = $openning + $sales_credit1 - $supplier_debit1 - $expenses_debit1;
            $totalAll = $bank_balance1 + $total_credit - $total_debit;
            
            $html.='<td align="right">$'.number_format($bank_balance1,2).'</td>';
            $html.='<td align="right">$'.number_format($total_debit,2).'</td>';
//            $html.='<td align="right">$'.number_format($total_credit,2).'</td>';
            
            $html.='<td align="right">$'.  number_format($totalAll,2).'</td>';
            $html.='</tr></tbody>';
            $TotaCredit+=$total_credit;
            $TotalDebit+=$total_debit;
            $TotalBalance+=$bank_balance1;
            $TotalAll+=$totalAll;
        }
        }
            $html.='<tfoot>'
                    . '<tr id="title_body" style="font-weight: bold; font-size: 14px;background-color:#dfeffc " height="35">'
                    . '<td>Total:</td>'
                    . '<td align="right" style="border-top:1px solid #000">$'.number_format($TotalBalance,2).'</td>'
                    . '<td align="right" style="border-top:1px solid #000">$'.number_format($TotalDebit,2).'</td>'
//                    . '<td align="right" style="border-top:1px solid #000">$'.number_format($TotaCredit,2).'</td>'
                    
                    . '<td align="right" style="border-top:1px solid #000">$'.number_format($TotalAll,2).'</td>'
                    . '</tr>'
                    . '</tfoot>'
                    . '</table><br/><br/>';
 ///////    Projected Receivable  //////////
        $html.= '<div style="margin-top:20px;margin-bottom:10px;" ><b>Projected Receivable</b></div><br/>';
            $html.= '<table width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
                <thead >
                    <tr style="font-weight:bold;background-color:#dfeffc" height="20px" id="title_head">
                        <td align="left">Name</td>
                        <td align="right">30 Days</td>
                        <td align="right">60 Days</td>
                        <td align="right">90 Days</td>
                        <td align="right">90 Days +</td> 
                        <td align="right">Balance</td>
                        
                    </tr>
                </thead>
                <tbody>';
    
    $companyArr= $CReportManager->getCompanyByInvoice1($customer_value_arr);
    
    $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
   
    
    $aging_report = 1;
        foreach ($companyArr as $company_row)
        {
            $agingArr = $CReportManager ->list_agingreport($company_row['company_id'],false,false,false,false);
//         
            $agingArr;
            $job_location = "";
            $total_item_show =0;
                foreach ($agingArr as $aging_row)
                {
//                    
                        $invoice_id=$aging_row["invoice_id"];
                        $invoice_revision_info = $CInvoiceManager->get_invoice_revision_lastest($invoice_id,1);
                        $invoice_revision_id=$invoice_revision_info['invoice_revision_id'];
                        $total_show = $CInvoiceManager ->get_invoice_item_total($invoice_id, $invoice_revision_id);
                        $total_show_last_discount = $total_show - $invoice_revision_info['invoice_revision_discount'];
//                        
//                        //info tax
                        $infoTax = $CInvoiceManager->getTaxPaid($invoice_revision_id);
//                        
                        $total_item_show =  $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show,$infoTax);
                        $total_invoice_date = $CInvoiceManager->get_total_tax_and_paid_date($invoice_revision_id,$total_show, false,$infoTax);
                                
                        $start =  strtotime($aging_row['invoice_date']);
                                $end = strtotime(date("Y/m/d"));
                                $day_diff=  round(($end - $start)/86400);

                                if($day_diff>=0 && $day_diff<=30)
                                {

//                                    $day30=$total_item_show;
                                    $day30=$total_invoice_date;
                                    $day60=0;$day90=0;$day=0;$dayBalance=$day30;
                                }
                                else if($day_diff>30 && $day_diff<=60)
                                {
//                                    $day60=$total_item_show;
                                    $day60=$total_invoice_date;
                                    $day30=0;$day90=0;$day=0;$dayBalance=$day60;

                                }
                                else if($day_diff>60 && $day_diff<=90)
                                {
//                                    $day90=$total_item_show;
                                    $day90=$total_invoice_date;
                                    $day30=0;$day60=0;$day=0;$dayBalance=$day90;
                                }
                                else if( $day_diff>90)
                                {
//                                    $day=$total_item_show;
                                    $day=$total_invoice_date;
                                    $day30=0;$day60=0;$day90=0;$dayBalance=$day;
                                }
                                $tmpDay30 += $day30; $tmpDay60 +=$day60; $tmpDay90 += $day90;$tmpDay +=$day;$tmpBalance += $dayBalance;
                                $totalBalance = round($tmpBalance,2); $totalDay30=round($tmpDay30,2); $totalDay60=round($tmpDay60,2); $totalDay90=round($tmpDay90,2); $totalDay= round($tmpDay,2);
            }
            $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
            if(count($agingArr) == 0){
                $totalDay30=0;$totalDay60=0;$totalDay90=0;$totalDay=0;$totalBalance=0;
            }
            
            if($totalBalance>0){
//                $tbl_bank.='
//                        <tr  height="30">
//                            <td  style="font-size:13px;">'.$company_row['company_name'].'</td>
//                            <td  id="tatal_day30_'.$company_row[company_id].'" align="right" >$'.number_format($totalDay30,2).'</td>
//                            
//                            <td id="tatal_day60_'.$company_row[company_id].'" align="right" >$'.number_format($totalDay60,2).'</td>
//                            
//                            <td id="tatal_day90_'.$company_row[company_id].'" align="right" >$'.number_format($totalDay90,2).'</td>
//                           
//                            <td id="tatal_day_'.$company_row[company_id].'" align="right" >$'.number_format($totalDay,2).'</td>
//                            
//                            <td id="tatal_dayBalance_'.$company_row[company_id].'" align="right" >$'.number_format($totalBalance,2).'</td>
//                           
//                        </tr>
//                        
//                     ';
        $aging_total_30 += $totalDay30;
        $aging_total_60 += $totalDay60;
        $aging_total_90 += $totalDay90;
        $aging_total_day += $totalDay;
        $aging_total_banlance += $totalBalance;
            }
        }
        
    $html.= '</tbody>';
//    if($aging_total_banlance>0){
        $html.= '<tr style="font-weight:bold;background-color:#dfeffc" height="20px">
                <td style="font-size:15px;">Total</td>
                <td align="right">$'.number_format($aging_total_30,2).'</td>
                <td align="right">$'.number_format($aging_total_60,2).'</td>
                <td align="right">$'.number_format($aging_total_90,2).'</td>
                <td align="right">$'.number_format($aging_total_day,2).'</td>
                         
                <td align="right">$'.number_format($aging_total_banlance,2).'</td>
            </tr>';               
//    } else $html.='<tr><td colspan="13">No projected receivable report found.</td></tr>';
/// Projected Payable //////
    $html.= '</table><br/><br/>';
    $html.= '<div style="margin-top:20px;margin-bottom:10px;" ><b>Projected Payable</b></div><br/>';
            $html.= '<table width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
                <thead >
                    <tr style="font-weight:bold;background-color:#dfeffc" height="20px"  id="title_head">
                        <td align="left">Name</td>
                        <td align="right">30 Days</td>
                        <td align="right">60 Days</td>
                        <td align="right">90 Days</td>
                        <td align="right">90 Days +</td> 
                        <td align="right">Balance</td>
                        
                    </tr>
                </thead>
                <tbody>';
    $supplierByPoArr = $CReportManager->getSupplierByPOInv($customer_value_arr);
    
    foreach ($supplierByPoArr as $company_row)
    {
        $payableArr = $po_invoice->list_payableReport($company_row['company_id'],$date_from,$date_to,$date);
        
        $totalDay30=0;$totalDay60=0;$totalDay90=0;$totalDay=0;$totalBalance=0;
        $tmpDay30=0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
       foreach ($payableArr as $payable_row)
        {
            $po_id=$payable_row["po_invoice_id"];
            $date = date('Y-m-d');
            $total_po_date =$po_invoice->getTotalPoLastPayment($po_id,$date);
                    $start =  strtotime($payable_row['po_date']);
                    $end = strtotime(date("Y/m/d"));
                    $day_diff=  round(($end - $start)/86400);

                    if($day_diff>=0 && $day_diff<=30)
                    {
                        $day30=$total_po_date;
                        $day60=0;$day90=0;$day=0;$dayBalance=$day30;
                    }
                    else if($day_diff>30 && $day_diff<=60)
                    {
                        $day60=$total_po_date;
                        $day30=0;$day90=0;$day=0;$dayBalance=$day60;

                    }
                    else if($day_diff>60 && $day_diff<=90)
                    {
                        $day90=$total_po_date;
                        $day30=0;$day60=0;$day=0;$dayBalance=$day90;
                    }
                    else if( $day_diff>90)
                    {
                        
                        $day=$total_po_date;
                        $day30=0;$day60=0;$day90=0;$dayBalance=$day;
                    }
                    $tmpDay30 += $day30; $tmpDay60 +=$day60; $tmpDay90 += $day90;$tmpDay +=$day;$tmpBalance += $dayBalance;
                    $totalBalance = round($tmpBalance,2); $totalDay30=round($tmpDay30,2); $totalDay60=round($tmpDay60,2); $totalDay90=round($tmpDay90,2); $totalDay= round($tmpDay,2);
                   
        }
        
        if($totalBalance>0){
//            $html.= '
//                <tr  height="30">
//                    <td  style="font-size:13px;">'.$company_row['company_name'].'</td>
//                    <td  id="tatal_day30_'.$company_row[company_id].'" align="right" >$'.number_format($totalDay30,2).'</td>
//                    
//                    <td id="tatal_day60_'.$company_row[company_id].'" align="right" >$'.number_format($totalDay60,2).'</td>
//                    
//                    <td id="tatal_day90_'.$company_row[company_id].'" align="right" >$'.number_format($totalDay90,2).'</td>
//                    
//                    <td id="tatal_day_'.$company_row[company_id].'" align="right" >$'.number_format($totalDay,2).'</td>
//                    
//                    <td id="tatal_dayBalance_'.$company_row[company_id].'" align="right" >$'.number_format($totalBalance,2).'</td>
//                </tr>';
                
                $payable_total_30 += $totalDay30;
                $payable_total_60 += $totalDay60;
                $payable_total_90 += $totalDay90;
                $payable_total_day += $totalDay;
                $payable_total_banlance += $totalBalance;
        }
    }
    
    $html.= '</tbody>';
//    if($payable_total_banlance>0){
        $html.='<tr style="font-weight:bold;background-color:#dfeffc" >
                <td  style="font-size:15px;">Total</td>
                <td align="right" >$'.number_format($payable_total_30,2).'</td>
                
                <td align="right" >$'.number_format($payable_total_60,2).'</td>
                
                <td align="right" >$'.number_format($payable_total_90,2).'</td>
                
                <td align="right" >$'.number_format($payable_total_day,2).'</td>
                
                <td align="right" >$'.number_format($payable_total_banlance,2).'</td>
            </tr>';
           
        
       
//    }
//    else $html.='<tr><td colspan="13">No projected payable report found.</td></tr>';
    $html.= '</table><br/><br/>';
    
// Net Cash Flow /////
    $html.= '<table table width="100%" border="1" callpadding="4" cellspacing="0" id="aging_report" class="tbl">'
            . '<tr style="font-weight:bold;background-color:#dfeffc" height="20px" id="title_head">'
            . '<td>Net Cash Flow</td>'
            . '<td align="right">$'.  number_format($TotalAll+$aging_total_banlance-$payable_total_banlance,2).'</td>'
            . '</tr>'
            . '</table>';
   // echo $tbl_bank;
    $html.='</div>';
        $pdf->AddPage();              
        $pdf->writeHTML($html, true, false, true, false, '');
        ob_end_clean();
        $pdf->Output('cash_flow_summary_report.pdf', 'I');
	}
    public function caculatorCreditBeforDate($bank_account_id,$customer_value_arr=false,$start_date = false,$date_search=false)
    {
       
        $q = new DBQuery();
        $q->addTable('bank_cheque','bc');
        
        if($bank_account_id){
            $q->addWhere('bc.bank_account_id='.intval ($bank_account_id));
        }
        if($customer_value_arr){
            $q->addWhere('bank_cheque_description IN '.$customer_value_arr);
        }
        if($start_date){
            $q->addWhere('bc.bank_cheque_date <"'.$start_date.'"');
        }
        if($date_search && strtotime($start_date) >= strtotime($date_search))
        {
            $q->addWhere ('bc.bank_cheque_date<"'.($start_date).'"');
            $q->addWhere ('bc.bank_cheque_date>="'.($date_search).'"');
        }
        else if($start_date){
            $q->addWhere ('bc.bank_cheque_date<"'.($start_date).'"');
        }
       
        $rows = $q->loadList();
        if($date_search && strtotime($start_date) < strtotime($date_search))
        {
            $rows=array();
        }
        $total = array();
        $total_credit = 0;
        $total_debit = 0;
        if(count($rows) > 0)
        {
            foreach ($rows as $data)
            {
                
                $total_credit +=$data['bank_cheque_credit'];
                $total_debit += $data['bank_cheque_debit'];
            }
        }
        $total['credit'] = $total_credit;
        $total['debit']=$total_debit;
        return $total;
      
    }
}

 
?>
