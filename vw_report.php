<?php

if(!defined('DP_BASE_DIR')){
    die('You should not access this file directly.');
}
//echo "<script src='js/chosen.jquery.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' type='text/css' href='style/default/chosen.css'/>";

require_once (DP_BASE_DIR."/modules/sales/css/report.css.php");
require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoice.php");
require_once (DP_BASE_DIR."/modules/sales/CReportManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
require_once (DP_BASE_DIR."/modules/sales/CQuotationManager.php");
require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");
require_once(DP_BASE_DIR."/modules/clients/company_manager.class.php");
require_once (DP_BASE_DIR."/modules/po/vw_po.php");
require_once (DP_BASE_DIR."/modules/sales/CTax.php");
require_once (DP_BASE_DIR."/modules/sales/CCreditNoteManager.php");
require_once DP_BASE_DIR.'/modules/po/po_invoice.php';
require_once DP_BASE_DIR.'/modules/expenses/expensesManage.php';
require_once DP_BASE_DIR.'/modules/expenses/ExpensesSupplierManage.php';
require_once DP_BASE_DIR.'/modules/expenses/Expenses_tax_manage.php';
require_once DP_BASE_DIR.'/modules/expenses/ExpensesCategory.php';
require_once DP_BASE_DIR.'/modules/po/CPOPayment.php';
require_once DP_BASE_DIR.'/modules/po/CPOPaymentDetail.php';
require_once DP_BASE_DIR.'/modules/banks/CBankAccount.php';
require_once DP_BASE_DIR.'/modules/banks/CBankCheque.php';

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
$ExpensesCategory = new ExpensesCategory();
$CSalesManager = new CSalesManager();
$CReportManager = new CReportManager();
$CInvoiceManager = new CInvoiceManager();
$CInvoiceManager = new CInvoiceManager();
$CPaymentManager = new CPaymentManager();
$CCompany = new CompanyManager();
$CQuotationManager = new CQuotationManager();
$CPOManager = new CPOManager();
$CTax = new CTax();
$CCreditManager = new CCreditNoteManager();
$po_invoice = new po_invoice();
$bankAccount = new CBankAccount();
$CPOPaymentDetail = new CPOPaymentDetail();
$expensesManage = new ExpensesManager();
$CBankCheque = new CBankCheque();
function __default(){
    global $AppUI,$m;
    include DP_BASE_DIR."/modules/sales/report.js.php";
    echo $aging_report_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="list_aging_report(); return false">Aging report</button>&nbsp;';
    echo $cash_receipt_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="list_cash_receipt(); return false">Cash receipt</button>&nbsp;';
    echo $Invoice_journal_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="list_invoice_juornal(); return false">Invoice journal</button>&nbsp;';
    echo '<div id="aging_report">';
        vw_aging_report();
    echo '</div>';
}
function vw_aging_report(){
    global $AppUI, $CReportManager, $CInvoiceManager,$CSalesManager;
    
    include DP_BASE_DIR."/modules/sales/report.js.php";
    $customer_arr = $CSalesManager->get_list_companies();
   
    
    $option_customer = '';
    foreach ($customer_arr as $customer_rows) {
        $option_customer.="<option value='".$customer_rows['company_id']."'>".$customer_rows['company_name']."</option>";
    }
    
    $timerange = dPgetSysVal('TimeRange');
    $optation_timerange = '<option value="">All</option>';
    foreach ($timerange as $key => $value) {
        $selected ='';
        if($get_time_range == $key)
            $selected = 'selected="selected"';
         $optation_timerange.='<option value="'.$key.'" '. $selected .'>'.$value.'</option>';
        
    }
    echo '<table width="100%" border=0 callpadding="0" cellspacing="0"  class="tbl" style="margin-bottom:5px;">
            <tr height="45">
                <td colspan="5"  style="font-size:20px" align="center">Aging report</td>
                <td align="right" colspan=1" style="font-size:14px">'.date("d.M.Y").'</td>
            </tr>
            <tr height="45">
                <td >Group by :</td>
                <td colspan="6">
                    <select class="text" name="group_by_aging" id="group_by_aging">
                        <option value="1" selected>Customer</option>
                        <option value="2">Branch</option>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    '.$AppUI->_("Include address: ").'
                    <select class="text" name="include_address" id="include_address">
                        <option value="1">Hide</option>
                        <option value="2">Show</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="70">'. $AppUI->_("Customer: ").'</td>
                <td width="330">  
                     <select onchange="change_customer();return false;" class="text" data-placeholder="Choose Customer" name="aging_customer" multiple="multiple" id="aging_customer">'.$option_customer.'</select>
                </td>
                <td width="200">
                    '. $AppUI->_("Time Range: ").'&nbsp;<select class="text" name="time_range" id="time_range">'.$optation_timerange.'</select>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
                
                <td width="190" hidden="true">
                    '.$AppUI->_("Live Report: ").'
                    <input type="checkbox" checked name="check_live_report" id="check_live_report" />
                </td>
                <td width="190">
                    '.$AppUI->_("Date: ").'<input class="text" size="12" type="text" name="aging_date_to" id="aging_date_to" onChange="download_report();return false;"/>
                    <input value="'.date('Y-m-d').'" type="hidden" name="hdd_aging_date_to" id="hdd_aging_date_to" />
                </td>
                <td colspan="2">
                    <button name="submit" class="ui-button ui-state-default ui-corner-all" onclick="get_search_aging();">Search</button>
                </td>
            </tr>
         </table>';
   echo '<div id="tbl_aging" >';
//        view_tbl_aging();
   echo '</div>';
 
}

function view_tbl_aging(){
    
    global $AppUI,$CInvoiceManager, $CReportManager,$CSalesManager,$ocio_config;
    $customer_id = false;
    if(isset($_GET['customer_id']) && $_GET['customer_id']!="" && $_GET['customer_id']!="null")
        $customer_id = $_GET['customer_id'];
    $companyArr= $CReportManager->getCompanyByInvoice($customer_id);
    
    $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
    
    if(isset($_GET['time_range']) && $_GET['time_range']!=""){
        $get_time_range = $_GET['time_range'];
        if($get_time_range == ""){
            $date_from = false;
            $date_to = false;
        }
        if($get_time_range == 1){
            $lastMonthTime30=time()-30*24*60*60;
            $date_from= date("Y-m-d",$lastMonthTime30);
            $date_to = date("Y-m-d");   
            
        }
        else if($get_time_range == 2){
            $lastMonthTime30=time()-31*24*60*60;
            $lastMonthTime60=time()-60*24*60*60;
            $date_from= date("Y-m-d",$lastMonthTime60);
            $date_to = date("Y-m-d",$lastMonthTime30);
        }
        else if($get_time_range == 3){ 
            $lastMonthTime60=time()-61*24*60*60;
            $lastMonthTime90=time()-90*24*60*60;
            $date_from= date("Y-m-d",$lastMonthTime90);
            $date_to = date("Y-m-d",$lastMonthTime60);
        }
        elseif($get_time_range == 4){ 
            $lastMonthTime90=time()-91*24*60*60;
            $date_from= false;
            $date_to = date("Y-m-d",$lastMonthTime90);
        }
    }
    $data_from1=false;$data_to1=false;
    if($_GET['data_from']!="")
        $date_from1 = $_GET['data_from'];
    if($_GET['data_to']!="")
       $date_to1 = $_GET['data_to'];
    if(isset($_GET['include_address']))
    $include_address = $_GET['include_address'];
    $aging_report = 1;
    if(!isset($_GET['print']) || $_GET['print']!=1)
        echo $print_block = '<div style="width:100%;"><button class="ui-button ui-state-default ui-corner-all" onclick="genarate_print_aging_report('.$aging_report.'); return false;" target="_blank">Print HTML</button></div>';
    else
        $title = '<tr>'
                . '<td style="text-align:right" colspan="4"><h3>Aging Report</h3></td>'
                . '<td style="text-align:right" colspan="7">'.date($ocio_config['php_abms_date_format'],  strtotime($date_to1)).'</td>'
            . '</tr>';
    echo '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;" id="tbl_aging_report" class="tbl">
            <thead>
                '.$title.'
                <tr id="title_head" style="font-size:14px" height="30">
                    <td width="28%" >'.$AppUI->_("Number").'</td>
                    <td width="7%" align="center">'.$AppUI->_("Date").'</td>
                    <td width="6%" align="right">'.$AppUI->_("0 - 30 Days").'</td>
                    <td width="2%"></td>
                    <td width="6%" align="right">'.$AppUI->_("31 - 60 Days").'</td>
                    <td width="2%"></td>
                    <td width="6%" align="right">'.$AppUI->_("61 - 90 Days").'</td>
                    <td width="2%"></td>
                    <td width="6%" align="right">'.$AppUI->_("90 + Days").'</td>
                    <td width="2%"></td>
                    <td width="6%" align="right">'.$AppUI->_("Balance").'</td>
               </tr>
           <tr >
                <td colspan="12" style="height:5px;">
                     <input type="hidden" name="hdd_from" id="hdd_from" value="'.$date_from.'" />
                     <input type="hidden" name="hdd_to" id="hdd_to" value="'.$date_to.'" />
                </td>
          </tr>
           </thead>';
    echo '<tbody>';
        foreach ($companyArr as $company_row)
        {
            $agingArr = $CReportManager ->list_agingreport($company_row['company_id'],$date_from,$date_to,$date_from1,$date_to1);
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
                        $total_invoice_date = $CInvoiceManager->get_total_tax_and_paid_date($invoice_revision_id,$total_show, $date_to1,$infoTax);
                                
                        $start =  strtotime($aging_row[invoice_date]);
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
                                if($dayBalance>0){
                                    // get joblocation
                                    //$job_location_arr =  $CSalesManager->get_address_by_id($aging_row['job_location_id']);
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
                                        if($job_location!="")
                                        {
                                            $v_location = '<tr><td style="color:#666;font-size:0.85em;height:0px; padding:0; padding-left:5px;padding-bottom:8px;" colspan="11">'.$job_location.'</td></tr>';
                                        }
                                    }
                                    echo '
                                            <tr valign="top">
                                                <td style="color:#08245b">
                                                    '.$aging_row["invoice_no"].'
                                                </td>
                                                <td id="invoice_date_'.$aging_row["invoice_id"].'" align="center">'.date($ocio_config['php_abms_date_format'],strtotime($aging_row['invoice_date'])).'</td>
                                                <td id="$day30_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day30,2).'</td>
                                                <td ></td>
                                                <td id="$day60_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day60,2).'</td>
                                                <td ></td>
                                                <td id="$day90_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day90,2).'</td>
                                                <td ></td>
                                                <td id="$day_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day,2).'</td>
                                                <td ></td>
                                                <td align="right">$'.number_format($dayBalance,2).'</td>
                                            </tr>';
                                    echo $v_location;
                                }

                
            }
            $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
            if(count($agingArr) == 0){
                $totalDay30=0;$totalDay60=0;$totalDay90=0;$totalDay=0;$totalBalance=0;
            }
            
            if($totalBalance>0){
                echo '
                        <tr style="font-weight:bold;" height="30">
                            <td colspan="2" style="font-size:13px;">'.$company_row['company_name'].'</td>
                            <td  id="tatal_day30_'.$company_row[company_id].'" align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay30,2).'</td>
                            <td ></td>
                            <td id="tatal_day60_'.$company_row[company_id].'" align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay60,2).'</td>
                            <td ></td>
                            <td id="tatal_day90_'.$company_row[company_id].'" align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay90,2).'</td>
                            <td ></td>
                            <td id="tatal_day_'.$company_row[company_id].'" align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay,2).'</td>
                            <td ></td>
                            <td id="tatal_dayBalance_'.$company_row[company_id].'" align="right" style="border-top: 1px solid #000;">$'.number_format($totalBalance,2).'</td>
                        </tr>
                        <tr>
                            <td colspan="12" ><hr style="background:#E9E9E9;"></td>
                        </tr>
                     ';
        $aging_total_30 += $totalDay30;
        $aging_total_60 += $totalDay60;
        $aging_total_90 += $totalDay90;
        $aging_total_day += $totalDay;
        $aging_total_banlance += $totalBalance;
            }
        }
        
    echo '</tbody>';
    if($aging_total_banlance>0){
        echo '<tr style="font-weight:bold;">
                <td colspan="2" style="font-size:15px;">Total</td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_30,2).'</td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_60,2).'</td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_90,2).'</td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_day,2).'</td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_banlance,2).'</td>
            </tr>
            <tr><td colspan="13">&nbsp</td></tr>';
        
        echo '<tr><td colspan="13" height="40">';
        if(!isset($_GET['print']) || $_GET['print']!=1)
            echo $print_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="genarate_print_aging_report('.$aging_report.'); return false;" target="_blank">Print</button>&nbsp;';
         echo '</td></tr>';
    }
    else echo '<tr><td colspan="13">No aging report found.</td></tr>';
    echo '</table>';
}

//function view_table_brand_aging(){
//    global $AppUI,$CInvoiceManager,$CReportManager;
//    
//    $customer_id = false;
//    
//    if(isset($_GET['customer_id']) && $_GET['customer_id']!="" && $_GET['customer_id']!="null")
//        $customer_id = $_GET['customer_id'];
//    //$companyArr= $CReportManager->getCompanyByInvoice($customer_id);
//    $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
//    
//    if(isset($_GET['time_range']) && $_GET['time_range']!=""){
//        $get_time_range = $_GET['time_range'];
//        if($get_time_range == ""){
//            $date_from = false;
//            $date_to = false;
//        }
//        if($get_time_range == 1){
//            $lastMonthTime30=time()-30*24*60*60;
//            $date_from= date("Y-m-d",$lastMonthTime30);
//            $date_to = date("Y-m-d");   
//            
//        }
//        else if($get_time_range == 2){
//            $lastMonthTime30=time()-31*24*60*60;
//            $lastMonthTime60=time()-60*24*60*60;
//            $date_from= date("Y-m-d",$lastMonthTime60);
//            $date_to = date("Y-m-d",$lastMonthTime30);
//        }
//        else if($get_time_range == 3){ 
//            $lastMonthTime60=time()-61*24*60*60;
//            $lastMonthTime90=time()-90*24*60*60;
//            $date_from= date("Y-m-d",$lastMonthTime90);
//            $date_to = date("Y-m-d",$lastMonthTime60);
//        }
//        elseif($get_time_range == 4){ 
//            $lastMonthTime90=time()-91*24*60*60;
//            $date_from= false;
//            $date_to = date("Y-m-d",$lastMonthTime90);
//        }
//    }
//    $data_from1=false;$data_to1=false;
//    if($_GET['data_from']!="")
//        $date_from1 = $_GET['data_from'];
//    if($_GET['data_to']!="")
//        $date_to1 = $_GET['data_to'];
//        echo '<table width="100%" border="0" cellpadding="8" cellspacing="0" id="tbl_aging_report" class="tbl">
//            <thead>
//                <tr id="title_head" style="font-size:14px" height="30">
//                    <td width="28%" >'.$AppUI->_("Number").'</td>
//                    <td width="7%" align="center">'.$AppUI->_("Date").'</td>
//                    <td width="6%" align="right">'.$AppUI->_("0 - 30 Days").'</td>
//                    <td width="2%"></td>
//                    <td width="6%" align="right">'.$AppUI->_("31 - 60 Days").'</td>
//                    <td width="2%"></td>
//                    <td width="6%" align="right">'.$AppUI->_("61 - 90 Days").'</td>
//                    <td width="2%"></td>
//                    <td width="6%" align="right">'.$AppUI->_("90 + Days").'</td>
//                    <td width="2%"></td>
//                    <td width="6%" align="right">'.$AppUI->_("Balance").'</td>
//               </tr>
//                <tr >
//                <td colspan="12" style="height:5px;">
//                     <input type="hidden" name="hdd_from" id="hdd_from" value="'.$date_from.'" />
//                     <input type="hidden" name="hdd_to" id="hdd_to" value="'.$date_to.'" />
//                </td>
//                </tr>
//           </thead>';
//        $job_location_arr = $CInvoiceManager->get_joblocation_inner_invoice($customer_id);
//        //print_r($job_location_arr);
//        echo '<tbody>';
//        foreach ($job_location_arr as $job_location_row) {
//            $job_id=$job_location_row['address_id'];
//            $agingArr = $CReportManager->list_agingreport(false, $date_from, $date_to, $date_from1, $date_to1, $job_id);
//            //$invoice_arr = $CInvoiceManager->get_invoice_by_jolocation($job_id);
//            foreach ($agingArr as $aging_row) {
//                $invoice_id=$aging_row["invoice_id"];
//                $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
//                $total_show = $CInvoiceManager ->get_invoice_item_total($invoice_id, $invoice_revision_id);
//                $total_item_show =  $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show);
//                        $start =  strtotime($aging_row['invoice_date']);
//                        $end = strtotime(date("Y/m/d"));
//                        $day_diff=  round(($end - $start)/86400);
//
//                        if($day_diff>=0 && $day_diff<=30)
//                        {
//
//                            $day30=$total_item_show;
//                            $day60=0;$day90=0;$day=0;$dayBalance=$day30;
//                        }
//                        else if($day_diff>30 && $day_diff<=60)
//                        {
//                            $day60=$total_item_show;
//                            $day30=0;$day90=0;$day=0;$dayBalance=$day60;
//
//                        }
//                        else if($day_diff>60 && $day_diff<=90)
//                        {
//                            $day90=$total_item_show;
//                            $day30=0;$day60=0;$day=0;$dayBalance=$day90;
//                        }
//                        else if( $day_diff>90)
//                        {
//                            $day=$total_item_show;
//                            $day30=0;$day60=0;$day90=0;$dayBalance=$day;
//                        }
//                        $tmpDay30 += $day30; $tmpDay60 +=$day60; $tmpDay90 += $day90;$tmpDay +=$day;$tmpBalance += $dayBalance;
//                        $totalBalance = round($tmpBalance,2); $totalDay30=round($tmpDay30,2); $totalDay60=round($tmpDay60,2); $totalDay90=round($tmpDay90,2); $totalDay= round($tmpDay,2);
//                
//                   echo'<tr>
//                        <td>'.$aging_row['invoice_no'].'</td>
//                        <td>'.date("d/m/Y",strtotime($aging_row['invoice_date'])).'</td>
//                        <td id="$day30_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day30,2).'</td>
//                        <td ></td>
//                        <td id="$day60_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day60,2).'</td>
//                        <td ></td>
//                        <td id="$day90_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day90,2).'</td>
//                        <td ></td>
//                        <td id="$day_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day,2).'</td>
//                        <td ></td>
//                        <td align="right">$'.number_format($dayBalance,2).'</td>
//                    </tr>';
//            }
//            $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
//            if(count($agingArr) == 0){
//                $totalDay30=0;$totalDay60=0;$totalDay90=0;$totalDay=0;$totalBalance=0;
//            }
//            if($totalBalance>0){
//                $address_2 ="";$brand="";$postal_code_job="";
//               if($job_location_row['address_street_address_2'])
//                        $address_2 = ' '.$job_location_row['address_street_address_2'];
//                    if($job_location_row['address_branch_name']!="")
//                        $brand=$job_location_row['address_branch_name'].' - ';
//                    if($job_location_row['address_postal_zip_code']!="")
//                        $postal_code_job .=', Singapore '.$job_location_row['address_postal_zip_code'];
//                echo '
//                        <tr  height="30">
//                            <td colspan="2" style="font-size:13px;"><b>'.$job_location_row['company_name'].'</b> ('.$brand.$job_location_row['address_street_address_1'].$address_2.$postal_code_job.')</td>
//                            <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay30,2).'</td>
//                            <td ></td>
//                            <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay60,2).'</td>
//                            <td ></td>
//                            <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay90,2).'</td>
//                            <td ></td>
//                            <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay,2).'</td>
//                            <td ></td>
//                            <td align="right" style="border-top: 1px solid #000;">$'.number_format($totalBalance,2).'</td>
//                        </tr>
//                        <tr>
//                            <td colspan="12" ><hr style="background:#E9E9E9;"></td>
//                        </tr>
//                     ';
//                $aging_total_30 += $totalDay30;
//                $aging_total_60 += $totalDay60;
//                $aging_total_90 += $totalDay90;
//                $aging_total_day += $totalDay;
//                $aging_total_banlance += $totalBalance;
//            }
////            echo '<tr>
////                    <td colspan="2">'.$job_location_row['address_street_address_1'].' ('.$job_location_row['company_name'].')</td>
////                    <td></td>
////                    <td></td>
////                    <td></td>
////                    <td></td>
////                    <td></td>
////                    <td></td>
////                    <td></td>
////                    <td></td>
////                    <td></td>
////                </tr>';
//        }
//        if($aging_total_banlance>0){
//                echo '<tr style="font-weight:bold;">
//                        <td colspan="2" style="font-size:15px;">Total</td>
//                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_30,2).'</td>
//                        <td></td>
//                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_60,2).'</td>
//                        <td></td>
//                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_90,2).'</td>
//                        <td></td>
//                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_day,2).'</td>
//                        <td></td>
//                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_banlance,2).'</td>
//                    </tr>
//                    <tr><td colspan="13">&nbsp</td></tr>';
//
//                echo '<tr><td colspan="13" height="40">';
//            $aging_report = 1;
//            echo $print_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="genarate_print_aging_report('.$aging_report.'); return false;" target="_blank">Print</button>&nbsp;';
//                 echo '</td></tr>';
//            }
//            else echo '<tr><td colspan="13">No aging report found.</td></tr>';
//        echo '</tbody></table>';
//}

function view_table_brand_aging(){
        global $AppUI,$CInvoiceManager,$CReportManager,$CSalesManager;
    
    $customer_id = false;
    
    if(isset($_GET['customer_id']) && $_GET['customer_id']!="" && $_GET['customer_id']!="null")
        $customer_id = $_GET['customer_id'];
    $companyArr= $CReportManager->getCompanyByInvoice($customer_id);
    $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
    
    if(isset($_GET['time_range']) && $_GET['time_range']!=""){
        $get_time_range = $_GET['time_range'];
        if($get_time_range == ""){
            $date_from = false;
            $date_to = false;
        }
        if($get_time_range == 1){
            $lastMonthTime30=time()-30*24*60*60;
            $date_from= date("Y-m-d",$lastMonthTime30);
            $date_to = date("Y-m-d");   
            
        }
        else if($get_time_range == 2){
            $lastMonthTime30=time()-31*24*60*60;
            $lastMonthTime60=time()-60*24*60*60;
            $date_from= date("Y-m-d",$lastMonthTime60);
            $date_to = date("Y-m-d",$lastMonthTime30);
        }
        else if($get_time_range == 3){ 
            $lastMonthTime60=time()-61*24*60*60;
            $lastMonthTime90=time()-90*24*60*60;
            $date_from= date("Y-m-d",$lastMonthTime90);
            $date_to = date("Y-m-d",$lastMonthTime60);
        }
        elseif($get_time_range == 4){ 
            $lastMonthTime90=time()-91*24*60*60;
            $date_from= false;
            $date_to = date("Y-m-d",$lastMonthTime90);
        }
    }
    $data_from1=false;$data_to1=false;
    if($_GET['data_from']!="")
        $date_from1 = $_GET['data_from'];
    if($_GET['data_to']!="")
        $date_to1 = $_GET['data_to'];
    if(isset($_GET['include_address']))
        $include_address = $_GET['include_address'];
    //echo $include_address;
    $aging_report = 1;
    if(!isset($_GET['print']) || $_GET['print']!=1)
        echo $print_block = '<div style="width:100%;"><button class="ui-button ui-state-default ui-corner-all" onclick="genarate_print_aging_report('.$aging_report.'); return false;" target="_blank">Print</button></div>';
    $title = '<tr>'
                . '<td style="text-align:right" colspan="4"><h3>Aging Report</h3></td>'
                . '<td style="text-align:right" colspan="7">'.date('d/M/Y',  strtotime($date_to1)).'</td>'
            . '</tr>';
    $customer_id_arr = $CInvoiceManager->get_customer_by_invoice($customer_id);
    echo '<table width="100%" border="0" cellpadding="2" style="margin-top:5px;" cellspacing="0" id="tbl_aging_report" class="tbl">
                <thead>
                '.$title.'
                    <tr id="title_head" style="font-size:14px" height="30">
                        <td width="28%" >'.$AppUI->_("Number").'</td>
                        <td width="7%" align="center">'.$AppUI->_("Date").'</td>
                        <td width="6%" align="right">'.$AppUI->_("0 - 30 Days").'</td>
                        <td width="2%"></td>
                        <td width="6%" align="right">'.$AppUI->_("31 - 60 Days").'</td>
                        <td width="2%"></td>
                        <td width="6%" align="right">'.$AppUI->_("61 - 90 Days").'</td>
                        <td width="2%"></td>
                        <td width="6%" align="right">'.$AppUI->_("90 + Days").'</td>
                        <td width="2%"></td>
                        <td width="6%" align="right">'.$AppUI->_("Balance").'</td>
                   </tr>
                    <tr >
                    <td colspan="12" style="height:5px;">
                         <input type="hidden" name="hdd_from" id="hdd_from" value="'.$date_from.'" />
                         <input type="hidden" name="hdd_to" id="hdd_to" value="'.$date_to.'" />
                    </td>
                    </tr>
               </thead>';
    $tmp = $customer_id_arr[0]['address_id'];
    $tmpcus = $customer_id_arr[0]['company_id'];
    $i=1;$j=1;
    foreach ($customer_id_arr as $customer_row) {
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
       //echo $customer_row['address_id'].','.$customer_row['company_id'].'<br>';
        $invoice_arr = $CReportManager->list_agingreport($customer_row['company_id'], $date_from, $date_to, $date_from1, $date_to1, $customer_row['address_id'], true);
        
        foreach ($invoice_arr as $invoice_row) {
            
            $invoice_id=$invoice_row["invoice_id"];
            $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
            $total_show = $CInvoiceManager ->get_invoice_item_total($invoice_id, $invoice_revision_id);
            $total_item_show =  $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show);
                    $start =  strtotime($invoice_row['invoice_date']);
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
            
                $joblocation ="";
                if($include_address==2){
                    ($invoice_row['address_branch_name']) ? $joblocation .= $invoice_row['address_branch_name'].' - ' : $joblocation.="";
                    ($invoice_row['address_street_address_1']) ? $joblocation .= $invoice_row['address_street_address_1'] : $joblocation="";
                    ($invoice_row['address_street_address_2']) ? $joblocation .= ' '.$invoice_row['address_street_address_2'] : $joblocation.="";
                    ($invoice_row['address_postal_zip_code']) ? $joblocation .= ' Singapore '.$invoice_row['address_postal_zip_code'] : $joblocation.="";
                    if($invoice_row['address_phone_1']!="")
                        $joblocation.=", Phone: ".$invoice_row['address_phone_1'];
                    elseif($invoice_row['address_phone_2']!="")
                        $joblocation.=", Phone: ".$invoice_row['address_phone_2'];
                }
                if($joblocation!=""){
                    $joblocation = '<tr><td colspan="12" style="color:#666;font-size:0.85em;height:0px; padding:0; padding-left:5px;padding-bottom:8px;">'.$joblocation.'</td></tr>';
                }
            echo'<tr>
                 <td style="color:#08245b">'.$invoice_row['invoice_no'].'</td>
                 <td>'.date("d/M/Y",strtotime($invoice_row['invoice_date'])).'</td>
                 <td id="$day30_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day30,2).'</td>
                 <td ></td>
                 <td id="$day60_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day60,2).'</td>
                 <td ></td>
                 <td id="$day90_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day90,2).'</td>
                 <td ></td>
                 <td id="$day_'.$aging_row["invoice_id"].'" align="right">$'.number_format($day,2).'</td>
                 <td ></td>
                 <td align="right">$'.number_format($dayBalance,2).'</td>
             </tr>';
            echo $joblocation;
             
        }
        }
        $tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
        if(count($invoice_arr) == 0){
            $totalDay30=0;$totalDay60=0;$totalDay90=0;$totalDay=0;$totalBalance=0;
        }
        
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
        if(count($invoice_arr)>0 && $totalBalance>0){
            echo '<tr style="font-weight:bold;">
                        <td colspan="2" style="font-size:13px;"><b>'.$customer_row['company_name'].'</b>'.$address1.'</td>
                            <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay30,2).'</td>
                            <td ></td>
                            <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay60,2).'</td>
                            <td ></td>
                            <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay90,2).'</td>
                            <td ></td>
                            <td  align="right" style="border-top: 1px solid #000;">$'.number_format($totalDay,2).'</td>
                            <td ></td>
                            <td align="right" style="border-top: 1px solid #000;">$'.number_format($totalBalance,2).'</td>
                    </tr>
                    <tr>
                         <td colspan="12" ><hr style="background:#E9E9E9;"></td>
                    </tr>';

        }
        
        $aging_total_30 += $totalDay30;
        $aging_total_60 += $totalDay60;
        $aging_total_90 += $totalDay90;
        $aging_total_day += $totalDay;
        $aging_total_banlance += $totalBalance;
        
//       
    }
    
        if($aging_total_banlance>0){
                echo '<tr style="font-weight:bold;">
                        <td colspan="2" style="font-size:15px;">Total</td>
                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_30,2).'</td>
                        <td></td>
                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_60,2).'</td>
                        <td></td>
                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_90,2).'</td>
                        <td></td>
                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_day,2).'</td>
                        <td></td>
                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($aging_total_banlance,2).'</td>
                    </tr>
                    <tr><td colspan="13">&nbsp</td></tr>';

                echo '<tr><td colspan="13" height="40">';
            $aging_report = 1;
            if(!isset($_GET['print']) || $_GET['print']!=1)
                echo $print_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="genarate_print_aging_report('.$aging_report.'); return false;" target="_blank">Print</button>&nbsp;';
                 echo '</td></tr>';
            }
            else echo '<tr><td colspan="13">No aging report found.</td></tr>';
    echo '</table>';
    
    //$tmpDay30 = 0;$tmpDay60=0;$tmpDay90=0;$tmpDay=0;$tmpBalance=0;
}

function vw_cash_receipt(){
    global $AppUI, $CReportManager, $CInvoiceManager, $CPaymentManager,$CSalesManager;
    include DP_BASE_DIR."/modules/sales/report.js.php";
    // Get customer
    $customer_arr = $CSalesManager->get_list_companies();
    $option_customer = '';
    foreach ($customer_arr as $customer_rows) {
        $option_customer.="<option value='".$customer_rows['company_id']."'>".$customer_rows['company_name']."</option>";
    }
    
    echo '<table width="100%" border=0 callpadding="0" cellspacing="0" id="aging_report" class="tbl">
            <tr height="40">
                <td colspan="10"  style="font-size:20px" align="center">Cash receipt</td>
            </tr>
            <tr  valign="bottom">
                <td colspan="8">
                Customer:
                    <select name="cash_customer" style="width:300px" id="cash_customer" class="text" data-placeholder="Choose Customer" multiple="multiple">'.$option_customer.'</select>&nbsp;&nbsp;
                From:
                    <input type="text" class="text" size="10" readonly="readonly" name="payment_date_start" id="payment_date_start"  value="'.$date_start.'" />
                    <input type="hidden" id="payment_date_start_hidden" value="'.$date_start.'">&nbsp;
                    to:
                    <input type="text" size="10" class="text" readonly="readonly" name="payment_date_end" id="payment_date_end" value="'.$date_end.'"  />
                    <input type="hidden" id="payment_date_end_hidden" value="'.$date_end.'">&nbsp;&nbsp;
                    <button class="ui-button ui-state-default ui-corner-all" value="Search" type="submit" onclick="loadReport()">Search</button>
                </td>
                <td align="right" colspan="10" style="font-size:14px">'.date("d.M.Y").'</td>
            </tr></table>';
    echo '<div id="tbl_cash_receipt">';
        view_tbl_cash_receipt();
    echo '</div>';
}

function vw_invoice_journal(){
    global $AppUI,$CReportManager,$CInvoiceManager,$CSalesManager;
    include DP_BASE_DIR."/modules/sales/report.js.php";
    // Get customer
    $customer_arr = $CSalesManager->get_list_companies();
    $option_customer = '';
    foreach ($customer_arr as $customer_rows) {
        $option_customer.="<option value='".$customer_rows['company_id']."'>".$customer_rows['company_name']."</option>";
    }
    $month = strtotime(date("Y-m-d") . " -1 month"); 
    $start_month = strftime("%d/%m/%Y", $month);
    
    echo '<table width="100%" border=0" callpadding="0" cellspacing="0" id="aging_report" class="tbl">
                <tr height="40">
                <td colspan="13"  style="font-size:20px" align="center">Invoice journal</td>
            </tr>
            <tr valign="bottom">
                <td colspan="12">
                    Customer:
                    <select name="journal_customer" id="journal_customer" class="text" data-placeholder="Choose Customer" multiple="multiple">'.$option_customer.'</select>&nbsp;&nbsp;
                    From:
                    <input type="text" size="10" class="text" readonly="readonly" name="payment_date_start" id="payment_date_start"  value="'.$start_month.'" />
                    <input type="hidden" id="payment_date_start_hidden" value="'.date('Y-m-d',$month).'">&nbsp;&nbsp;
                    to:
                    <input type="text" size="10" class="text" readonly="readonly" name="payment_date_end" id="payment_date_end" value="'.date('d/m/Y').'"  />
                    <input type="hidden" id="payment_date_end_hidden" value="'.date('Y-m-d').'">&nbsp;&nbsp;
                    <button class="ui-button ui-state-default ui-corner-all" value="Search" type="submit" onclick="load_invoice_journal()">Search</button>
                </td>
                <td align="right" style="font-size:14px">'.date("d.M.Y").'</td>
            </tr>
    </table>';
    echo '<div id="tbl_invoice_journal">';
//        view_tbl_invoice_journal();
    echo '</div>';   
}

function view_tbl_invoice_journal(){
    global $AppUI,$CReportManager,$CInvoiceManager,$CSalesManager,$CTax,$ocio_config;
    $date_start = $_GET['date_start'];
    $date_end = $_GET['date_end'];
    
    $customer_id_arr=false;
    if($_GET['custormer_id']!="" && $_GET['custormer_id']!="null")
        $customer_id_arr = "(".$_GET['custormer_id'].")";
    $now = getdate();
    echo $print_block = '<div style="widht:100%;margin-top:10px;"><button class="ui-button ui-state-default ui-corner-all" onclick="print_invoice_journal(); return false;" target="_blank">Print</button>&nbsp;';
                    echo '<button class="ui-button ui-state-default ui-corner-all" onclick="print_html_invoice_journal(); return false;" target="_blank">Print HTML</button>&nbsp;</div>';

    echo '<table width="100%" border=0" style="margin-top:0px;" callpadding="0" cellspacing="0" id="aging_report" class="tbl">
            <thead>
            <tr><td colspan="13" style="height:5px"></td></tr>
            <tr style="font-size:14px" height="30" id="title_head">
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
           <tr><td colspan="13" style="height:5px"></td></tr></thead>';
    echo '<tbody>';
    $tax2 = 0;
    
    for($i=1;$i<=12;$i++){
            $tmp_total_total=0;$tmp_total_subtotal=0;$tmp_total_tax1=0;$tmp = 0;
            $jour_invoice_arr = $CInvoiceManager->list_db_invoice($date_start,$date_end,$customer_id_arr);
            foreach ($jour_invoice_arr as $jour_invoice_row) {
                $invoice_date_month = intval(substr($jour_invoice_row['invoice_date'],5,-3));
                if($invoice_date_month == $i){
                    $date_month_year = date('d/M/Y',strtotime($jour_invoice_row['invoice_date']));
                    $date_month_year = substr($date_month_year,3);
                    $jour_invoice_rev_id=round($CInvoiceManager->get_invoice_revision_lastest($jour_invoice_row['invoice_id']),2);
                    $jour_invoice_rev_arr = $CInvoiceManager->get_latest_invoice_revision($jour_invoice_row['invoice_id']);
                    $subtotal=$CInvoiceManager->get_invoice_item_total($jour_invoice_row['invoice_id'], $jour_invoice_rev_id);
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
                    echo'<tr>
                           <td>'.$jour_invoice_row['invoice_no'].'</td>
                           <td></td>
                           <td>'.date($ocio_config['php_abms_date_format'],  strtotime($jour_invoice_row['invoice_date'])).'</td>
                           <td></td>
                           <td>'.$jour_invoice_row['company_name'].'</td>
                           <td></td>
                           <td align="right">$'.number_format($subtotal_last_discount,2).'</td>
                           <td></td>
                           <td align="right">$'.number_format($tax1,2).'</td>
                           <td></td>
                           <td align="right">$'.number_format($tax2,2).'</td>
                           <td></td>
                           <td align="right">'.number_format($subtotal_last_discount+$tax1+$tax2,2).'</td>
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
                
                echo'<tr style="font-weight:bold">
                    <td colspan="6" >'.$date_month_year.'</td>
                    <td style="border-top:1px solid #000" align="right">$'.number_format($total_subtotal,2).'</td>
                    <td></td>
                    <td style="border-top:1px solid #000" align="right">$'.number_format($total_tax1,2).'</td>
                    <td></td>
                    <td align="right" border-top:1px solid #000">$'.number_format($tax2,2).'</td>
                    <td></td>
                    <td style="border-top:1px solid #000" align="right">$'.number_format($total_subtotal+$total_tax1+$tax2,2).'</td>
                </tr>
                <tr><td colspan="13" style="height:8px"></td></tr>
                <tr><td style="background:#E9E9E9; height:1px" colspan="12"></td></tr>
                <tr height="5"><td colspan="15" style="height:15px"></td></tr>
                <tr><td colspan="13" style="height:8px"></td></tr>';
            }
        }
        if($jour_total>0){
            echo'<tr style="font-weight:bold">
                <td colspan="6" style="font-size:15px;">Total</td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($jour_total_subtotal,2).'</td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($jour_total_tax1,2).'</td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($tax2,2).'</td>
                <td></td>
                <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;font-size:15px;">$'.number_format($jour_total_subtotal+$jour_total_tax1+$tax2,2).'</td>
            </tr>
            <tr><td colspan="13" style="height:10px"></td></tr>';
            echo'<tr><td colspan="13">';
                $invoice_journal = 3;
                echo $print_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="print_invoice_journal(); return false;" target="_blank">Print</button>&nbsp;';
                echo '<button class="ui-button ui-state-default ui-corner-all" onclick="print_html_invoice_journal(); return false;" target="_blank">Print HTML</button>&nbsp;';
                
            echo '</td></tr>';
        }
        else echo '<tr><td colspan="13">No invoice journal found</td></tr>';
   
    echo '</tbody></table>';
}
function view_tbl_cash_receipt(){
    global $AppUI, $CReportManager, $CInvoiceManager, $CPaymentManager,$CSalesManager,$ocio_config;
    $bankManage = new CBankAccount();
    
    $PaymentMethods = dPgetSysVal('PaymentMethods');
    $date_start = $_GET['date_start'];
    $date_end = $_GET['date_end'];
    $now = getdate();
    //print_r($now);
    $custormer_id_cash = $_GET['customer_id_arr'];
    echo $print_block = '<div style="width:100%; margin-top:10px;"><button class="ui-button ui-state-default ui-corner-all" onclick="genarate_print_cash_report('.$cash_receipt.'); return false;" target="_blank">Print</button>&nbsp;</div><br/>';
    echo '<table width="100%" border=0 callpadding="0" cellspacing="0" id="aging_report" class="tbl">
            <thead>
                <tr style="font-size:14px" height="30" id="title_head">
                    <td width="10%" align="left" >'.$AppUI->_("Date Rec'd").'</td>
                    <td width="22%" align="left">'.$AppUI->_("Customer").'</td>
                    <td width="14%" align="right">'.$AppUI->_("Payment Amount").'</td>
                    <td width="12%" align="right">'.$AppUI->_("Cheque Nos").'</td>
                    <td width="12%" align="right">'.$AppUI->_("Receipt Nos").'</td>
                    <td width="12%" align="right">'.$AppUI->_("Bank Account").'</td>
                    <td width="20%" align="center">'.$AppUI->_("Note").'</td>
                    <td width="24%" align="left" >'.$AppUI->_("Invoices Paid").'</td>
               </tr>
               </thead>';

        echo '<tbody>';
        $total=0;
        if($date_start==""&&$date_end==""){
            $tmp_data = 0;$report_payment_total=0;
            foreach ($PaymentMethods as $key => $value) {
                $report_payment_arr = $CPaymentManager->list_db_payment("","","",true,"$key");
                    $tmp=0;$total_method=0;
                    foreach ($report_payment_arr as $report_payment_row){
                        $report_payment_month = substr($report_payment_row['payment_date'],5,-3);
                        $report_payment_year = substr($report_payment_row['payment_date'],0,4);
                        if($report_payment_month==$now['mon'] && $report_payment_year==$now['year']){
                            $report_payment_month_year = date('d/M/Y',strtotime($report_payment_row['payment_date']));
                            $report_payment_month_year = substr($report_payment_month_year,3);

                            $report_payment_total_amuont = round($CReportManager->get_payment_amount_total($report_payment_row['payment_id']),2);
                            $report_paymentDetail_arr = $CReportManager->getPaymentDetailByPayment($report_payment_row['payment_id']);

                            $customer ="";
                            $cus_invoice_revision = "";
                            foreach ($report_paymentDetail_arr as $report_paymentDetail_row) {
                                //print_r($report_paymentDetail_row) ;
                                        $report_customer_arr = $CReportManager->getCustomerByInvoiceRev($report_paymentDetail_row['invoice_revision_id']);
                                        foreach ($report_customer_arr as $report_customer_row){
                                            $customer=$report_customer_row['company_name'];
                                            $customer_id = $report_customer_row['company_id'];
                                            $cus_invoice_revision = $report_customer_row['invoice_revision_id'];
                                        }

                            }
                            
                           if($cus_invoice_revision!=""){
                                if(in_array($customer_id, $custormer_id_cash) || count($custormer_id_cash)==0){
                                      $bank_id=$report_payment_row['bank_account_id'];
                                      $bank_name = "";
//                                      if($bank_id > 0){
                                          $bank_info=$bankManage->getBankAccount($report_payment_row['bank_account_id']);
                                          
                                          $bank_name=$bank_info[0]['bank_account_name'];
//                                      }
                                     echo '<tr id="title_body">
                                              <td>'.date($ocio_config['php_abms_date_format'],strtotime($report_payment_row['payment_date'])).'</td>
                                              <td>'.$customer.'</td>
                                              <td align="right">$'.number_format($report_payment_total_amuont,2).'</td>
                                              <td align="right">'.$report_payment_row['payment_cheque_nos'].'&nbsp;</td>
                                              <td align="right">'.$report_payment_row['payment_receipt_no'].'&nbsp;</td>
                                              <td align="right">'.$bank_name.'</td>
                                              <td>'.$report_payment_row['payment_notes'].'</td>
                                              <td>';
                                     $report_invoice = "";
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
                                      $tmp_data=1;$report_payment_total+=$report_payment_total_amuont;$tmp=1;
                                      $total_method +=$report_payment_total_amuont;
                                 }
                           }

                        }
                    }
                    if($tmp==1)
                        echo '<tr style="font-weight:bold;">
                                    <td colspan="2" >Method: '.$value.'</td>
                                    <td colspan="1" align="right" style="border-top: 1px solid rgb(0, 0, 0);">'.number_format ($total_method, 2).'</td>
                                    <td colspan="5" align="right">&nbsp;</td>
                            </tr>';
            }
                
                if($tmp_data==1){
                    $total+=$report_payment_total;
                    echo '<tr id="title_body" style="font-weight: bold; font-size: 14px; " height="35">
                            <td colspan="2" style="background:#EEE;">'.$report_payment_month_year.'</td>
                            <td align="right" style="background:#EEE;">$'.number_format($report_payment_total,2).'</td>
                            <td colspan="9" style="background:#EEE;"></td>
                        </tr>
                        '; 
                }
        }else{
    //        print_r($report_payment_arr);
            for($i=1;$i<=12;$i++){
                $tmp_data = 0;$report_payment_total=0;
                foreach ($PaymentMethods as $key => $value) {
                    $report_payment_arr = $CPaymentManager->list_db_payment("",$date_start,$date_end,true,"$key");
                    $tmp=0;$total_method=0;
                    foreach ($report_payment_arr as $report_payment_row){
                        $report_payment_month = substr($report_payment_row['payment_date'],5,-3);
                        if($report_payment_month==$i){
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
                                if(in_array($customer_id, $custormer_id_cash) || count($custormer_id_cash)==0){
                                      
                                      $bank_id = $report_payment_row['bank_account_id'];
                                      $bank_name = "";
                                      if($bank_id > 0){
                                          $bank_info=$bankManage->getBankAccount($bank_id);
                                          $bank_name=$bank_info[0]['bank_account_name'];
                                      }
                                      echo '<tr id="title_body">
                                              <td>'.date($ocio_config['php_abms_date_format'],strtotime($report_payment_row['payment_date'])).'</td>
                                              <td>'.$customer.'</td>
                                              <td align="right">$'.number_format($report_payment_total_amuont,2).'</td>
                                              <td align="right">'.$report_payment_row['payment_cheque_nos'].'&nbsp;</td>
                                              <td align="right">'.$report_payment_row['payment_receipt_no'].'&nbsp;</td>
                                              <td align="right">'.$bank_name.'</td>
                                              <td>'.$report_payment_row['payment_notes'].'</td>
                                              <td>';
                                     $report_invoice = "";
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
                                      $tmp_data=1;$report_payment_total+=$report_payment_total_amuont;$tmp=1;
                                      $total_method +=$report_payment_total_amuont;
                                  }
                            }
                        }
                    }
                    if($tmp==1)
                        echo '<tr style="font-weight:bold;">
                                    <td colspan="2" >Method: '.$value.'</td>
                                    <td colspan="1" align="right" style="border-top: 1px solid rgb(0, 0, 0);">'.number_format ($total_method, 2).'</td>
                                    <td colspan="5" align="right">&nbsp;</td>
                            </tr>';
                }
                if($tmp_data==1){
                    $total+=$report_payment_total;
                    echo '<tr id="title_body" style="font-weight: bold; font-size: 14px;" height="35">
                            <td colspan="2" style="background:#EEE;">'.$report_payment_month_year.'</td>
                            <td align="right" style="background:#EEE;">$'.  number_format($report_payment_total,2).'</td>
                            <td colspan="12" style="background:#EEE;"></td>
                        </tr>
                        <tr><td colspan="11">&nbsp;</td></tr>
                        '; 
                }
        }}
                if($total>0){
                    echo'
                        <tr height="5"><td colspan="11" style="height:8px"></td></tr>
                        <tr style="font-weight: bold; background:#fff">
                            <td colspan="2" >Total:</td>
                            <td align="right" style="border-top: 1px solid rgb(0, 0, 0);border-bottom: 1px solid rgb(0, 0, 0);">$'.  number_format($total,2).'<td>
                                <td colspan="6" ></td>
                         </tr><tr height="5"><td colspan="15" style="height:15px"></td></tr>
                        <tr><td colspan="10">';
                            $cash_receipt=2;
                            echo $print_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="genarate_print_cash_report('.$cash_receipt.'); return false;" target="_blank">Print</button>&nbsp;';
                    echo '</td></tr>'; 
                }
                else echo '<tr><td colspan="10">No cash report found</td></tr>';
                echo'</tbody></table>';
}

function _do_generate_print_aging_report() {
    $select = '<div id="tbl_generate"><br/>
        <form id="generate_value" mothod="POST" action="" name="generate_value">';
            $select .= '<span>Are you sure print to</span>&nbsp; &nbsp;';
            $select .= '<select id="generate" name="generate" class="text">
                    <option value="0">HTML</option>
                    <option value="1" selected>PDF</option>
                </select>&nbsp; &nbsp;
                <span>this Invoice?</span>';
    $select .= '</form></div>';
    echo $select;
}
function _do_print_aging_report(){
    global $CReportManager,$CInvoiceManager;
    $group_by=$_REQUEST['group_by'];
    if($group_by==1)
        $CReportManager->create_aging_report_pdf_file();
    else
        $CReportManager->print_pdf_aging_brand ();
}
function _do_print_cash_receipt(){
    global $AppUI, $CReportManager, $CInvoiceManager, $CPaymentManager;
    $date_start = $_GET['date_start'];
    $date_end = $_GET['date_end'];
    $customer_id_arr = $_GET['customer_id_arr'];
    $CReportManager->create_cash_receipt_pdf($date_start,$date_end,$customer_id_arr);
}
function _do_print_html_agign_report(){
    global $CReportManager;
    $CReportManager->create_aging_report_html();
}
function _do_print_html_cash_receipt(){
    global $AppUI, $CReportManager, $CInvoiceManager, $CPaymentManager;
    $date_start = $_GET['date_start'];
    $date_end = $_GET['date_end'];
    $customer_id_arr = $_GET['customer_id_arr'];
    $CReportManager->create_cash_receipt_html($date_start,$date_end,$customer_id_arr);
}
function _do_print_html_invoice_journal(){
    global $AppUI, $CReportManager, $CInvoiceManager;
    $date_start = $_GET['date_start'];
    $date_end = $_GET['date_end'];
    $customer_id_arr=false;
    if($_GET['customer_id_arr']!="" && $_GET['customer_id_arr']!="null")
        $customer_id_arr = "(".$_GET['customer_id_arr'].")";
    $CReportManager->create_invoice_journal_html($date_start,$date_end,$customer_id_arr);
}
function _do_print_invoice_journal(){
    global $AppUI, $CReportManager, $CInvoiceManager;
    $date_start = $_GET['date_start'];
    $date_end = $_GET['date_end'];
    $customer_id_arr=false;
    if($_GET['customer_id_arr']!="" && $_GET['customer_id_arr']!="null")
        $customer_id_arr = "(".$_GET['customer_id_arr'].")";
    $CReportManager->create_invoice_jourmal_pdf($date_start,$date_end,$customer_id_arr);
}
function _do_print_invoice_html_journal(){
    global $AppUI, $CReportManager, $CInvoiceManager;
    $date_start = $_GET['date_start'];
    $date_end = $_GET['date_end'];
    $customer_id_arr=false;
    if($_GET['customer_id_arr']!="" && $_GET['customer_id_arr']!="null")
        $customer_id_arr = "(".$_GET['customer_id_arr'].")";
    $CReportManager->create_invoice_jourmal_html($date_start,$date_end,$customer_id_arr);
}
function vw_gst_report(){
    global $AppUI,$CInvoiceManager,$CPaymentManager,$CSalesManager;
    include DP_BASE_DIR."/modules/sales/report.js.php";
    
    //lay date trong vong 1 thang ke tu ngay hien tai
    $date_current = date('d/M/Y');
    $date_current_format = date('Y-m-d');
    
    $date_first = date('d/M/Y', strtotime('-1 month'));
    $date_first_format = date('Y-m-d', strtotime('-1 month'));
    // List customer select option
    $customer_arr = $CSalesManager->get_list_companies();
    $option_customer = '';
    foreach ($customer_arr as $customer_row) {
        $option_customer.='<option value="'.$customer_row['company_id'].'" >'.$customer_row['company_name'].'</option>';
    }
    echo '<div style="width:94%">
                <div style="font-size:20px;text-align: center; width:100%; margin-top:15px">GST Report</div>
                Customer: <select id="customer_selesct_gst" data-placeholder="Choose Customer" multiple="multiple">'.$option_customer.'</select>&nbsp;&nbsp;&nbsp;&nbsp;
                From: <input class="text" type="text" name="gst_from" id="gst_from" size="12" value="'.$date_first.'" /><input type="hidden" name="hdd_gst_from" id="hdd_gst_from" value="'.$date_first_format.'" />&nbsp;&nbsp;
                To: <input class="text" type="text" name="gst_to" id="gst_to" size="12" value="'.$date_current.'" /><input type="hidden" name="hdd_gst_to" id="hdd_gst_to" value="'.$date_current_format.'" />&nbsp;&nbsp;&nbsp;
                <button name="submit" class="ui-button ui-state-default ui-corner-all" onclick="get_search_gst();return false;">Search</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                '.date('d.M.Y').'
           </div>';
    echo '<div id="tbl_gst_report" style="margin-top:10px;width:94%;">';
//        view_gst_report();
    echo '</div>';
}
function view_gst_report(){
//    
//    
    $CPOPayment = new CPOPayment();
    global $ocio_config,$AppUI,$CInvoiceManager,$CReportManager,$CSalesManager,$CPOManager,$po_invoice,$CPaymentManager;
  
    $customer_value_arr = false;
    $from_date = false;
    $to_date = false;
    if(isset($_POST['customer_id'])){
        $customer_id_arr = $_POST['customer_id'];
        if($customer_id_arr!="null"){
            $customer_value_arr ="0";
            foreach ($customer_id_arr as $value) {
                $customer_value_arr .= ','.$value;
            }
            $customer_value_arr='('.$customer_value_arr.')';
        }
        
    }
    //echo $customer_value_arr;
    if(isset($_POST['from_date']))
        $from_date = $_POST['from_date'];
    if(isset($_POST['to_date']))
        $to_date = $_POST['to_date'];
//    $gst_report_arr1 = $CReportManager->get_gst_report(false,$from_date,$to_date);
    
    $gst_report_arr = $CReportManager->get_gst_report($customer_value_arr,$from_date,$to_date);
    
    $invoice_revision_tax = $gst_report_row['invoice_revision_tax'];
    
    // Tinh tax
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    
    
    
    echo '<div style="width:100%;margin-top:10px;margin-bottom:3px; ">'
    . '<button class="ui-button ui-state-default ui-corner-all" onclick="show_gst_report();">GST Report</button>&nbsp;'
    . '<button class="ui-button ui-state-default ui-corner-all" onclick="show_summary_report()">GST Report Summary</button>&nbsp;'
            . '<button class="ui-button ui-state-default ui-corner-all" onclick="print_gst_report('.$cash_receipt.'); return false;" target="_blank" style="float:right;">Print FDF</button>&nbsp;</div>';
    echo '<div id="gst_report">';
    echo '<div style="margin-top:20px;margin-bottom:10px;" ><b>Sales Invoice GST Report</b></div>';
    echo '<table width="100%" border="0" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
            <thead >
                <tr id="title_head">
                    <td align="left" width="10%">Date</td>
                    <td align="left" width="10%">Invoice Nos</td>
                    <td align="left" width="40%">Customer</td>
                    
                    <td align="right" width="10%">Value</td>
                    <td align="right" width="10%">GST </td>
                    <td align="right" width="10%">Total Amount</td>
                    <td align="right" width="10%">Value Collected</td>
                    <td align="right" width="10%">GST Collected</td>
                </tr>
            </thead>
            <tbody>';
    
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
        
        //$tax = $CSalesManager->round_up($tax);
        $date = $gst_report_row['invoice_date'];
//        
        $invoice_purchase_arr[$i]['date'] =  $date;
        $invoice_purchase_arr[$i]['number_no']=$gst_report_row['invoice_no'];
        $invoice_purchase_arr[$i]['customer_name']=$gst_report_row['company_name'];
        $invoice_purchase_arr[$i]['invoice_value']=$total_item_last_discount;
        $invoice_purchase_arr[$i]['gst_collected']=$tax;
        $invoice_purchase_arr[$i]['total_amount']=  $total_item_last_discount+$tax;
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
//        $invoice_purchase_arr[$i]['gst_paid']=0;
        $i++;
    }
//    echo '<pre>';
//    print_r($invoice_purchase_arr);
//
//   
    $total_gst_colleted= 0;$total_gst_paid_invoice=0;$total_invoice_value=0;$total_purchase_value=0;
    $total_payment_collected = 0;$value_gst_collected=0;
//    $invoice_purchase_arr = $CInvoiceManager->array_sort($invoice_purchase_arr, 'date',SORT_DESC);
    foreach ($invoice_purchase_arr as $value_item) {
        $total_gst_colleted+=$value_item['gst_collected'];
        $value_gst_collected += $value_item['value_gst'];
        $total_gst_paid_invoice+=$value_item['total_amount'];
        $total_invoice_value += $value_item['invoice_value'];
        $total_purchase_value += $value_item['purchase_value'];
        $total_payment_collected +=$value_item['value_collected'];
        
        echo   '<tr id="title_body">
                    <td>'.date($ocio_config['php_abms_date_format'],  strtotime($value_item['date'])).'</td>
                    <td>'.$value_item['number_no'].'</td>
                    <td>'.$value_item['customer_name'].'</td>
                    <td align="right">$'.number_format($value_item['invoice_value'],2).'</td>
                    <td align="right">$'.number_format($value_item['gst_collected'],2).'</td>
                    <td align="right">$'.number_format($value_item['total_amount'],2).'</td>
                    <td align="right">$'.number_format($value_item['value_collected'],2).'</td>
                    <td align="right">$'.number_format($value_item['value_gst'],2).'</td>
                </tr>';
    }
//
//    
    if(count($invoice_purchase_arr)==0){
        echo '<tr id="title_body"><td colspan="4">No GST Report Found</td></tr>';
    }
    echo    '</tbody>
            <tfoot >
                <tr id="title_body">
                    <td colspan="3" ><b>Total</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_invoice_value,2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_gst_colleted,2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_gst_paid_invoice,2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_payment_collected,2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($value_gst_collected,2).'</b></td>
                </tr>
            </tfoot>
            
        </table>';
    //
    echo '<br /><br/>';
    
    //    /*============ GST PURCHASE NO ============ */
    echo '<div style="margin-top:20px;margin-bottom:10px;"><b>Purchased Order (PO) GST</b></div>';
    

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
        
        $payment_invoice_supplier =$CPOPayment->getTotalSIPayment($po_invoice_id,false);
        
        $invoice_supplier_arr[$i]['Value_Paid']=$poPayment->getTotalSIPayment($po_invoice_id);
        $invoice_supplier_arr[$i]['total_amount']=$po_invoice->getTotalPoLastPayment($po_invoice_id)+$po_invoice->getTotalPaymentByPo($po_invoice_id);
        if($payment_invoice_supplier > 0)
            $invoice_supplier_arr[$i]['gst_supplier']=$invoice_supplier_arr[$i]['gst_paid'];
        
        $i++;
        }
    }
    echo '<table width="100%" border="0" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
            <thead >
                <tr id="title_head">
                    <td align="left" width="10%">Date</td>
                    <td align="left" width="10%">Invoice Nos</td>
                    <td align="left" width="30%">Customer</td>
                   
                    <td align="right" width="10%">Value</td>
                    <td align="right" width="10%">GST </td>
                    <td align="right" width="10%">Total Amount</td>
                    <td align="right" width="10%"><b>Value Paid</b></td>
                     
                    <td align="right" width="10%">Payable GST</td>
                  
                </tr>
            </thead>
            <tbody>';

//        $invoice_supplier_arr = $CInvoiceManager->array_sort($invoice_supplier_arr, 'date');
        
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
            
            $credit_note="";
            if($value_item['credit_note_id']>0)
                $credit_note=$value_item['po_payment_note'];
            echo '</td> </tr>';
            echo   '<tr id="title_body">
                        <td>'.$value_item['date'].'</td>
                        <td>'.$value_item['number_no'].'</td>
                        <td>'.$value_item['customer_name'].'</td>
                        
                        <td align="right">$'.number_format($value_item['purchase_value'],2).'</td>
                        <td align="right">$'.number_format($value_item['gst_paid'],2).'</td>
                        <td align="right">$'.number_format($value_item['total_amount'],2).'</td>
                        <td align="right">$'.number_format(($value_item['Value_Paid']-$value_item['gst_paid']),2).'</td>
                        <td align="right">';
//                        if($value_item['total_amount'] > 0){
//                                    echo '$'.number_format(0,2);
//                        }   
//                        else
                             echo '$'.number_format($value_item['gst_paid'],2);
                   echo '</td> </tr>';
            if($value_item['total_amount'] > 0)
                $gst_supplier += $value_item['gst_paid'] - $paid;
        }


        if(count($invoice_purchase_arr)==0){
            echo '<tr id="title_body"><td colspan="4">No GST Report Found</td></tr>';
        }
    echo    '</tbody>
            <tfoot >
                <tr id="title_body">
                    <td colspan="3" ><b>Total</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_purchase_value,2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($total_gst_paid,2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format(($total_amount-$gst_supplier),2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format(($total_purchase_payment-$total_gst_paid),2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format(($total_gst_paid),2).'</b></td>
                </tr>
            </tfoot>
            
        </table>';
            
    //Expenses GST Report
    echo '<div style="margin-top:20px;margin-bottom:10px;"><b>Expenses GST Report</b></div>';//$total_gst_paid-$gst_supplier
    $ExpensesSupplierManage  = new ExpensesSupplierManage();
    $expensesManage = new ExpensesManager();
    $arr_customer = $_POST['customer_id'];
   
    if($arr_customer != "null")
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
        $expensesGST[$j]['date']=date($ocio_config['php_abms_date_format'],  strtotime($ex_ca[0]['expenses_date']));
        $expensesGST[$j]['expenses_no']=$data['expenses_no'];
        $expensesGST[$j]['category_id']=$category_value;
        $expensesGST[$j]['GST']=$gst;
        $expensesGST[$j]['value'] =$total_ex_amount;
        $j++;
        
    }
    
    
    echo '<table width="100%" border="0" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
            <thead >
                <tr id="title_head">
                    <td align="left" width="10%">Date</td>
                    <td align="left" width="10%">Expenses Nos.</td>
                    <td align="left" width="40%">Category</td>
                    <td align="right" width="10%">Value</td>
                    <td align="right" width="10%">GST </td>
                    <td align="right" width="10%">Total Amount</td>
                    <td align="right" width="10%">Value Paid</td>
                    <td align="right" width="10%">Payable GST</td>
                  
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
            echo   '<tr id="title_body">
                        <td>'.$value_ex['date'].'</td>
                        <td>'.$value_ex['expenses_no'].'</td>
                        <td>'.$value_ex['category_id'].'</td>
                        <td align="right">$'.number_format($value_ex['value'],2).'</td>
                        <td align="right">$'.number_format($value_ex['GST'],2).'</td>
                        <td align="right">$'.number_format($value_ex['Amount'],2).'</td>
                        <td align="right">$'.number_format($value_ex['value'],2).'</td>
                        <td align="right">$'.number_format($value_ex['GST'],2).'</td>
                       
                    </tr>';
            }
        }
//        if(count($expensesGST)==0){
//            echo '<tr id="title_body"><td colspan="4">No GST Report Found</td></tr>';
//        }
    
     echo    '</tbody>
            <tfoot >
                <tr id="title_body">
                    <td colspan="3" ><b>Total</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($expenses_value,2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($gst_total_ex,2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($gst_amount_ex,2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($expenses_value,2).'</b></td>
                    <td align="right" style="border-top:1px solid #000"><b>$'.number_format($gst_total_ex,2).'</b></td>
                </tr>
            </tfoot>
            
        </table>';
     
     echo '<br/>';
     echo '<br/>';
     echo '<br/>';
     list_creditNote($customer_value_arr,$from_date,$to_date);
    echo '</div>';
     
//     GST Report Summary
    
    
    $sale_collected = $total_payment_collected+$value_gst_collected;
    $sale_Recievable= $total_invoice_value-$total_payment_collected;
    $infocredit_amount=$CSalesManager->info_amount_all_credit_note(false,$from_date,$to_date);
    
    $amount_credit=$infocredit_amount['amount'];
    $total_credit=$infocredit_amount['total'];
    echo '<div id="gst_report_summmary" hidden="true">';
      echo '<div style="margin-top:20px;margin-bottom:10px;"><b>Received / Payable GST Summary Report</b></div>';
       echo '<table width="100%" border="0" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
            <thead >
                <tr id="title_head">
                    <td align="left" width="20%">Sales</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"> </td>
                </tr>
            </thead>
            <tbody>';
        echo '<tr>'
                . '<td align="left" width="20%">Total Sales Collected</td>'
                . '<td align="right" width="20%">$'.number_format($total_payment_collected,2).'</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        echo '<tr>'
                . '<td align="left" width="20%">Total GST Collected</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="right" width="20%">$'.number_format($value_gst_collected,2).'</td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        
        echo '<tr>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        echo '<tr id="title_head">
                    <td align="left" width="20%">Purchases</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                  
                    <td align="right" width="40%"> </td>
                
                </tr>';
         echo '<tr>
                    <td align="left" width="20%">Total Purchases Paid</td>
                    <td align="right" width="20%">$'.number_format(($total_purchase_payment-$total_gst_paid),2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                    
                
                </tr>';
        echo '<tr>
                    <td align="left" width="20%">Total GST Paid</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format(($total_gst_paid-$gst_supplier),2).'</td>
                
                </tr>';
       
        echo '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
        echo '<tr id="title_head">
                    <td align="left" width="20%">Expenses</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                  
                    <td align="right" width="40%"> </td>
                
                </tr>';
         echo '<tr>
                    <td align="left" width="20%">Total Expenses Paid</td>
                    <td align="right" width="20%">$'.number_format(0,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                    
                
                </tr>';
        echo '<tr>
                    <td align="left" width="20%">Total GST Paid</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format(0,2).'</td>
                
                </tr>';
       echo '<tr id="title_head">
                    <td align="left" width="20%">Credit Note</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                  
                    <td align="right" width="40%"> </td>
                
                </tr>';
         echo '<tr>
                    <td align="left" width="20%">Amount</td>
                    <td align="right" width="20%">$'.number_format($amount_credit,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                    
                
                </tr>';
        echo '<tr>
                    <td align="left" width="20%">GST</td>
                    <td align="right" width="20%">$'.number_format($total_credit-$amount_credit,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="40%"></td>
                
                </tr>';
        echo '<tr>
                    <td align="left" width="20%">Total</td>
                    <td align="right" width="20%">$'.number_format($total_credit,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="40%"></td>
                
                </tr>';
        echo '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
       
        echo    '</tbody>';
        echo '<tfoot >';
         echo '<tr >
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" colspan="2"><b>Total GST Output /Payable to IRAS</b></td>
                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="20%">$'.number_format($value_gst_collected,2).'</td>
                  
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
         
        echo '<tr>
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000" colspan="3"><b>Total GST Input / Refundable from IRAS</b></td>
                   
                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="40%"> $'.number_format(($total_gst_paid-$gst_supplier),2).'</td>
                
                </tr>';
        echo '<tr >
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" colspan="3"><b>Nett GST to be paid to IRAS</b></td>
                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="20%">$'.number_format($value_gst_collected-($total_gst_paid-$gst_supplier),2).'</td>
                  
                
                </tr>';
        echo '</tfoot>';
            
        echo '</table>';
        
        echo '<br />';
        echo '<br />';
        
        //Payable/Receivable GST Summary Report
      echo '<div style="margin-top:20px;margin-bottom:10px;"><b>Total Value GST Summary Report</b></div>';
       echo '<table width="100%" border="0" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
            <thead >
                <tr id="title_head">
                    <td align="left" width="20%">Sales</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"> </td>
                </tr>
            </thead>
            <tbody>';
        echo '<tr>'
                . '<td align="left" width="20%">Total Sales Value</td>'
                . '<td align="right" width="20%">$'.number_format($sale_Recievable,2).'</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        echo '<tr>'
                . '<td align="left" width="20%">Total GST Value</td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="right" width="20%">$'.number_format($total_gst_colleted-$value_gst_collected,2).'</td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        
        echo '<tr>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="20%"></td>'
                . '<td align="left" width="40%"></td>'
                . '</tr>';
        echo '<tr id="title_head">
                    <td align="left" width="20%">Purchases</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                  
                    <td align="right" width="40%"> </td>
                
                </tr>';
         echo '<tr>
                    <td align="left" width="20%">Total Payable Purchases\'s</td>
                    <td align="RIGHT" width="20%">$'.number_format(($total_amount-$total_purchase_payment),2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                
                </tr>';
         echo '<tr>
                    <td align="left" width="20%">Total GST Payable</td>
                    <td align="right" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format($total_gst_paid-$gst_paid,2).'</td>
                    
                
                </tr>';
       
       
        echo '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
        echo '<tr id="title_head">
                    <td align="left" width="20%">Expenses</td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                  
                    <td align="right" width="40%"> </td>
                
                </tr>';//$expenses_value$gst_total_ex
         echo '<tr>
                    <td align="left" width="20%">Total Payable Expenses</td>
                    <td align="RIGHT" width="20%">$'.number_format($expenses_value,2).'</td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%"></td>
                
                </tr>';
         echo '<tr>
                    <td align="left" width="20%">Total GST Payable</td>
                    <td align="right" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="right" width="40%">$'.number_format($gst_total_ex,2).'</td>
                    
                
                </tr>';
       
       
        echo '<tr>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
       
        echo    '</tbody>';
        echo '<tfoot >';
         echo '<tr >
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" colspan="2"><b>Total GST Output /Payable to IRAS</b></td>
                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="20%">$'.number_format($total_gst_colleted-$value_gst_collected,2).'</td>
                    <td align="left" width="20%"></td>
                   
                    <td align="right" width="40%"> </td>
                
                </tr>';
        echo '<tr>
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e;border-top:1px solid #000" colspan="3"><b>Total GST Input / Refundable from IRAS</b></td>
                   
                   
                   
                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="40%">$'.number_format($total_gst_paid-$gst_paid+$gst_total_ex,2).' </td>
                
                </tr>';
         echo '<tr >
                    <td align="left" width="20%" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" colspan="3"><b>Nett GST to be paid to IRAS</b></td>
                    <td align="right" style="background-color:#dfeffc;color:#2e6e9e; border-top:1px solid #000" width="20%">$'.number_format($total_gst_colleted-$value_gst_collected-($total_gst_paid-$gst_paid+$gst_total_ex),2).'</td>
                  
                
                </tr>';
        echo '</tfoot>';
            
        echo '</table>';
        
        echo '<br />';
        echo '<br />';
        
        
        echo '</div>';
//    echo '<pre>';
//    print_r($invoice_supplier_arr);
//    /*============ END GST PURCHASE NO =========== */
// 
//    if(count($invoice_purchase_arr)>0){
//        echo '<button class="ui-button ui-state-default ui-corner-all" onclick="print_gst_report('.$cash_receipt.'); return false;" target="_blank">Print FDF</button>&nbsp;';
//    }
}

function list_creditNote($customer_id=false,$date_from=false,$date_to=false){
    global $AppUI,$CCreditManager,$CSalesManager,$deleteView_creditNote,$canDelete, $accessView_creditNote;
    
    $status = false;
    $credit_status =  dPgetSysVal('CreditStatus');
    $dataTableRow = new JDataTable('');
    $dataTableRow ->setWidth('100%');
    $dataTableRow ->setHeaders(array('Credit Note','Customer','Date','Invoice No','Total Amount','Status'));
    
    $colAttributer = array(
       
        'class="credit no" width="10%"',
        'class="customer_credit" width="48%"',
        'class="date_credit" width="12%" align="center"',
        'class="invocie_no" width="12%" align="center"',
        'class="amount_credit" width="15%" align="right"',
        'calss="status_credit" width="11%" align="center"'
    );
    $dataTable=array(); $rowIds=array();
    $creditNote_arr = $CCreditManager->list_db_credit($customer_id,$date_from,$date_to);
//    print_r($creditNote_arr);
  
    foreach ($creditNote_arr as $creditNote_row){
        $status = $creditNote_row['credit_note_status'];
        $customer_err = $CCreditManager->customer_by_creditNote($creditNote_row['customer_id']);
        $invoice_id = $creditNote_row['invoice_id'];
        $invoice_manage = new CInvoiceManager();
        $invocie = $invoice_manage->get_db_invoice($invoice_id);
        $invoice_no = $invocie[0]['invoice_no'];
        
        $dataTable[] = array(
         
            '<a href="" onclick="load_creditNote('.$creditNote_row['credit_note_id'].',\'update\'); return false;">'.$creditNote_row['credit_note_no'].'</a>',
            $customer_err[0]['company_name'],
            date('d/m/Y', strtotime($creditNote_row['credit_note_date'])),
           $invoice_no,
            '$'.number_format($CSalesManager->total_creditNote_amount_and_tax($creditNote_row['credit_note_id']),2),
            $credit_status[$status]
        );
        $rowIds[] = $creditNote_row['credit_note_id'];
    }
    
    $dataTableRow->setDataRow($dataTable, $colAttributer, $rowIds);
    $dataTableRow->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
    $dataTableRow->show();
   
}



function _do_print_gst_report_pdf(){
    global $AppUI,$CReportManager;
    if($_REQUEST['generate'] == 0)
        $CReportManager->create_gst_report();
    else
        $CReportManager->create_gst_report_summary();
}
function vw_sales_report(){
    global $AppUI,$CSalesManager;
    include DP_BASE_DIR."/modules/sales/report.js.php";
    $now = getdate();
    $subday = $now['mday']-1;
    
    $from_date=date('d/M/Y', strtotime("-".$subday." days"));;
    $to_date = date('d/M/Y');
    $from_date_format=date('Y-m-d', strtotime("- ".$subday." days"));;
    $to_date_format = date('Y-m-d');
    // List customer select option
    $customer_arr = $CSalesManager->get_list_companies();
    $option_customer = '';
    foreach ($customer_arr as $customer_row) {
        $option_customer.='<option value="'.$customer_row['company_id'].'" >'.$customer_row['company_name'].'</option>';
    }
    echo '<div style="width:100%">
                <div style="font-size:20px;text-align: center; width:100%; margin-top:15px">Sales Report</div>
                Report type: <select id="sel_report" class="text">
                            <option value="">All</option>
                            <option value="1">Cash Sales</option>
                            <option value="2">Invoice Sales</option>
                        </select>&nbsp;&nbsp;
                Customer: <select id="customer_selesct_sales" data-placeholder="Choose Customer" multiple="multiple">'.$option_customer.'</select>&nbsp;&nbsp;&nbsp;&nbsp;
                From: <input class="text" type="text" name="sales_from" id="sales_from" size="12" value="'.$from_date.'" /><input type="hidden" name="hdd_sales_from" id="hdd_sales_from" value="'.$from_date_format.'" />&nbsp;&nbsp;
                To: <input class="text" type="text" name="sales_to" id="sales_to" size="12" value="'.$to_date.'" /><input type="hidden" name="hdd_sales_to" id="hdd_sales_to" value="'.$to_date_format.'" />&nbsp;&nbsp;&nbsp;
                <button name="submit" class="ui-button ui-state-default ui-corner-all" onclick="get_search_sales();return false;">Search</button>
                <div style="text-align: right; width:100%;margin-top:5px;">'.date('d/M/Y').'</div>
           </div>';
    echo '<button class="ui-button ui-state-default ui-corner-all" onclick="print_sales_report(); return false;" target="_blank">Print FDF</button>&nbsp;';
    echo '<div id="tbl_sales_report" style="margin-top:0px;width:100%;">';
    echo '</div>';
    echo '<div id="report_hr"></div>';
    echo  '<div id="tbl_invoice_report" style="margin-top:15px;width:100%;">';
            view_sales_report();
            echo '<hr>';
            view_invoice_report();
    echo  '</div>';
    echo '<div id="sales_tbl_total">
        </div>';
}
function view_sales_report(){
    global $ocio_config,$AppUI,$CInvoiceManager,$CReportManager,$CPaymentManager,$CSalesManager;
    
    $customer_value_arr = false;
    $now = getdate();
    $subday = $now['mday']-1;
    $from_date=date('Y-m-d', strtotime('-'.$subday.' days'));
    $to_date = date('Y-m-d');
    if(isset($_POST['customer_id'])){
        $customer_id_arr = $_POST['customer_id'];
        if($customer_id_arr!="null"){
            $customer_value_arr ="0";
            foreach ($customer_id_arr as $value) {
                $customer_value_arr .= ','.$value;
            }
            $customer_value_arr='('.$customer_value_arr.')';
        }
        
    }
    if(isset($_POST['from_date']) && $_POST['from_date']!="")
        $from_date = $_POST['from_date'];
    if(isset($_POST['to_date']) && $_POST['to_date']!="")
        $to_date = $_POST['to_date'];

    $cash_sales_arr = $CReportManager->get_payment_sales_by_term_invoice(true,$customer_value_arr,$from_date,$to_date);
    //print_r($cash_sales_arr);
    
    echo '<table width="100%" border="0" cellpadding="4" cellspacing="0" id="aging_report" class="tbl">
        <tr ><td colspan="10" style="height:30px;color:#2E6E9E; font-size:18px;" align="center">Cash Sales</td></tr>
        <tr id="title_head">
            <td width="10%;">Inv date</td>
            <td width="10%" >Inv Number</td>
            <td width="34%">Customer</td>
            <td align="right" width="9%">Total</td>
            <td></td>
            <td align="right" width="9%">Paid</td>
            
            <td ></td>
            <td align="right" width="9%">Due</td>
        </tr>';
    $total_total=0;$total_paid=0;$total_due=0;$amount_credit=0;
    $i = 0;
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
        $amount = $paid - $amount_credit;
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
            echo '<tr>
                     <td>'.$date.'</td>
                     <td>'.$cash_sales_row['invoice_no'].'</td>
                     <td>'.$cash_sales_row['company_name'].'</td>
                     <td align="right">$'.number_format($total,2).'</td>
                     <td></td>
                     <td align="right">$'.number_format($paid,2).'</td>
                     
                    <td ></td>
                     <td align="right">$'.number_format($due,2).'</td>
                 </tr>';
            $total_total+=$total;
            $total_paid+=$paid;
            $total_due+=$due;
            $total_amount +=$amount;
        }
    }
    if(count($cash_sales_arr)>0 && $i>0){
        echo '<tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="border-top: 1px solid rgb(0, 0, 0);" align="right"><b>$'.number_format($total_total,2).'</b><input type="hidden" id="cash_total_total" value="'.$total_total.'"></td>
                <td></td>
                <td style="border-top: 1px solid rgb(0, 0, 0);" align="right"><b>$'.number_format($total_paid,2).'</b><input type="hidden" id="cash_total_paid" value="'.$total_paid.'"></td>
                
                <td></td>
                <td style="border-top: 1px solid rgb(0, 0, 0);" align="right"><b>$'.number_format($total_due,2).'</b><input type="hidden" id="cash_total_due" value="'.$total_due.'"></td>
            </tr>';
    }else{
        echo '<tr ><td colspan="10">No report</td></tr>';
    }
   echo '</table>';
    
}
function view_invoice_report(){
    global $AppUI,$CInvoiceManager,$CReportManager,$CPaymentManager,$CSalesManager,$ocio_config;
    $customer_value_arr = false;
    $now = getdate();
    $subday = $now['mday']-1;
    $from_date=date('Y-m-d', strtotime('-'.$subday.' days'));
    $to_date = date('Y-m-d');
//    $now = getdate();
//    //print_r($now);
//    echo $now[mon];
    if(isset($_POST['customer_id'])){
        $customer_id_arr = $_POST['customer_id'];
        if($customer_id_arr!="null"){
            $customer_value_arr ="0";
            foreach ($customer_id_arr as $value) {
                $customer_value_arr .= ','.$value;
            }
            $customer_value_arr='('.$customer_value_arr.')';
        }
        
    }
    if(isset($_POST['from_date']) && $_POST['from_date']!="")
        $from_date = $_POST['from_date'];
    if(isset($_POST['to_date']) && $_POST['to_date']!="")
        $to_date = $_POST['to_date'];
//    echo "123";
       $cash_sales_arr = $CReportManager->get_payment_sales_by_term_invoice(false,$customer_value_arr,$from_date,$to_date);
    echo '<table width="100%" border="0" cellpadding="4" cellspacing="0" id="aging_report" class="tbl">
        <tr ><td colspan="10" style="height:30px;color:#2E6E9E; font-size:18px;" align="center">Invoice Sales</td></tr>
        <tr id="title_head">
            <td width="10%;">Inv date</td>
            <td width="10%;">Inv Number</td>
            <td width="34%;">Customer</td>
            <td align="right" width="9%;">Total</td>
            <td></td>
            <td align="right" width="9%;">Paid</td>
            
            <td ></td>
            <td align="right" width="9%;">Due</td>
        </tr>';
    $total_total=0;$total_paid=0;$total_due=0;$amount_credit = 0;
    $i =0 ;
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
        $amount = $paid - $amount_credit;
        $amount = $paid - $amount_credit;
        
        // Hide truong hop amount invoice = amount paid
        $hide = false;
//        if($amount==0 && $amount_credit!=0)
//        {
//            $hide=true;
//        }
//        // End hide
//        if(!$hide)
//        {
            $i++;
            echo '<tr>
                     <td>'.$date.'</td>
                     <td>'.$cash_sales_row['invoice_no'].'</td>
                     <td>'.$cash_sales_row['company_name'].'</td>
                     <td align="right">$'.number_format($total,2).'</td>
                     <td></td>
                     <td align="right">$'.number_format($paid,2).'</td>
                     
                     <td></td>
                     <td align="right">$'.number_format($due,2).'</td>
                 </tr>';
            $total_total+=$total;
            $total_paid+=$paid;
            $total_due+=$due;
            $total_amount +=$amount;
//        }
        
    }
    if(count($cash_sales_arr)>0 && $i>0){
        echo '<tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="border-top: 1px solid rgb(0, 0, 0);" align="right"><b>$'.number_format($total_total,2).'</b><input type="hidden" id="invoice_total_total" value="'.$total_total.'"></td>
                <td></td>
                <td style="border-top: 1px solid rgb(0, 0, 0);" align="right"><b>$'.number_format($total_paid,2).'</b><input type="hidden" id="invoice_total_paid" value="'.$total_paid.'"></td>
                
                <td></td>
                <td style="border-top: 1px solid rgb(0, 0, 0);" align="right"><b>$'.number_format($total_due,2).'</b><input type="hidden" id="invoice_total_due" value="'.$total_due.'"></td>
            </tr>';
    }else
        echo '<tr><td colspan="10">No report</td></tr>';
   echo '</table>';
}
function _do_print_sales_report_pdf(){
    global $AppUI,$CReportManager;
    $CReportManager->create_sales_report_pdf();
}
function vw_dpInfo_report(){
    global $CReportManager;
    echo '<div style="width:100%;margin-top:20px;"><a class="icon-all" onclick="load_export_excel(); return false;" href="#"><img border="0" height="32" alt="Export Excel" src="modules/sales/images/icon/exportexcel.png">&nbsp;&nbsp;Export Excel</a></div>';
}
//function _do_dpInfo_report(){
//    //require_once (DP_BASE_DIR . '/lib/PHPExcel/Classes/PHPExcel.php');
//    
//    /** Error reporting */
//    error_reporting(E_ALL);
//    ini_set('display_errors', TRUE);
//    ini_set('display_startup_errors', TRUE);
//    date_default_timezone_set('Europe/London');
//
//    if (PHP_SAPI == 'cli')
//            die('This example should only be run from a Web Browser');
//
//    /** Include PHPExcel */
//    require_once (DP_BASE_DIR . '/lib/PHPExcel/Classes/PHPExcel.php');
//
//
//    // Create new PHPExcel object
//    $objPHPExcel = new PHPExcel();
//
//    // Set document properties
//    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
//                                                             ->setLastModifiedBy("Maarten Balliauw")
//                                                             ->setTitle("Office 2007 XLSX Test Document")
//                                                             ->setSubject("Office 2007 XLSX Test Document")
//                                                             ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
//                                                             ->setKeywords("office 2007 openxml php")
//                                                             ->setCategory("Test result file");
//
//
//    // Add some data
//    $objPHPExcel->setActiveSheetIndex(0)
//                ->setCellValue('A1', 'Hello')
//                ->setCellValue('B2', 'world!')
//                ->setCellValue('C1', 'Hello')
//                ->setCellValue('D2', 'world!');
//
//    // Miscellaneous glyphs, UTF-8
//    $objPHPExcel->setActiveSheetIndex(0)
//                ->setCellValue('A4', 'Miscellaneous glyphs')
//                ->setCellValue('A5', 'Ã©Ã Ã¨Ã¹Ã¢ÃªÃ®Ã´Ã»Ã«Ã¯Ã¼Ã¿Ã¤Ã¶Ã¼Ã§');
//
//    // Rename worksheet
//    $objPHPExcel->getActiveSheet()->setTitle('Simple');
//
//
//    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
//    $objPHPExcel->setActiveSheetIndex(0);
//
//
//    // Redirect output to a clientâ€™s web browser (Excel5)
//    header('Content-Type: application/vnd.ms-excel');
//    header('Content-Disposition: attachment;filename="01simple.xls"');
//    header('Cache-Control: max-age=0');
//    // If you're serving to IE 9, then the following may be needed
//    header('Cache-Control: max-age=1');
//
//    // If you're serving to IE over SSL, then the following may be needed
//    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
//    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//    header ('Pragma: public'); // HTTP/1.0
//
//    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//    $objWriter->save('php://output');
//    exit;
//}
function vw_statements_report(){
    global $AppUI, $CReportManager, $CSalesManager;
    include DP_BASE_DIR."/modules/sales/report.js.php";
    $customer_arr = $CSalesManager->get_list_companies();
    $option_customer = '<option value="" >Choose Customer</option>';
    foreach ($customer_arr as $customer_row) {
        $option_customer.='<option value="'.$customer_row['company_id'].'" >'.$customer_row['company_name'].'</option>';
    }
    $option_address = '<option value="-1">--Choose Address--</option>';
    $attention_address = '<option value="">--Choose Attention--</option>';
    
    echo '<div style="width:100%">
                <div style="font-size:20px;text-align: center; width:100%; margin-top:15px">Customer Statements</div>
                <div style="clear:both;">Customer: <select id="customer_statement" onchange="load_tabl_customer_statement(this.value,0);return false;">'.$option_customer.'</select></div>
                <div style="clear:both;margin-top:15px;">C/O: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <select id="co_statement">'.$option_customer.'</select></div>
                <div style="margin-top:15px;" id="sel_attention_atatement">Attention: &nbsp;<select id="attention_atatement" class="text">'.$attention_address.'</select></div>
                <div style="margin-top:5px;">Address: &nbsp;&nbsp;<span id="sel_address"><select id="address_atatement" class="text">'.$option_address.'</select></span></div>
                <div style="margin-top:15px;" id ="filter_sta">
                    Load Invoice: <select class="text" name="load_invoice" id="load_invoice">
                        <option value="">All</option>
                        <option value="2">Paid</option>
                        <option value="4" selected >Outstanding</option>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    From: <input class="text" size="12" type="text" name="sta_from" id="sta_from" value=""><input type="hidden" name="hdd_sta_from" id="hdd_sta_from" value="">&nbsp;&nbsp;
                    To:<input class="text" size="12" type="text" name="sta_to" id="sta_to" value=""/><input type="hidden" name="hdd_sta_to" id="hdd_sta_to" value=""/>&nbsp;&nbsp;

                    <input type="button" class="ui-button ui-state-default ui-corner-all" onclick="search_statement_report(); return false;" name="sta_search" id="sta_search" value="Search" />
                </div>
        </div>';
    echo '<div id="tbl_statements" style="width:80%;margin:50px auto;"></div>';
}
function view_tbl_statements(){
    global $CReportManager,$CSalesManager,$CInvoiceManager,$CCompany,$ocio_config;;
    require_once (DP_BASE_DIR."/modules/sales/css/report.css.php");
    
    require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
    $CTemplatePDF = new CTemplatePDF();
    
    $load_inv = 4; $date_from = false; $date_to = false;
    if(isset($_POST['load_inv']))
        $load_inv = $_POST['load_inv'];
    if($_POST['sta_date_from']!="")
        $date_from = $_POST['sta_date_from'];
    if($_POST['sta_date_from'] !="")
        $date_to = $_POST['sta_date_to'];
    //echo $load_inv."lkfsnfkshdfk";
    
    // Template cho server http://hlaircon.theairconhub.com/

    $template_pdf_5 = $CSalesManager->get_template_pdf(5);
    $template_pdf_6 = $CSalesManager->get_template_pdf(6);
    
    $customer_id = $_POST['customer_id'];
    $customer_arr = $CSalesManager->get_list_companies($customer_id);
    $customer_name = $customer_arr['company_name'];

     //Lay customer lien quan;
    $agent_customer_agent_arr = $CSalesManager->get_AgentCustomer_by_customer($customer_id);
    // Lay adress customer gan customer duoc chon lam customer
    $contractor_customer_arr=$CSalesManager->get_Customer_By_AgentCustomer($customer_id);
    //Lay 
    $customer_contractor_arr = $CSalesManager->get_ContractorCustomer_by_customer($customer_id);
    
    $customer_id_arr= array();

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
    //echo $customer_id_implode."567";
    $address_agent_arr = $CReportManager->get_list_address_and_agent($customer_id_implode);
    //print_r($address_agent_arr);
    $address_id_arr ="";
    foreach ($address_agent_arr as $address_agent_row) {
        $address_id_arr .=",".$address_agent_row['address_id'];
    }
//    foreach ($customer_contractor_arr as $customer_contractor) {
//        if($customer_contractor['company_id'])
//            $customer_id_arr.=",".$customer_contractor['company_id'];
//    }
//    //echo $customer_id_arr;
//    echo $customer_id_arr = "(".$customer_id.$customer_id_arr.")";
////    echo $customer_contractor = $customer_contractor_arr[0]['company_id'];
////    echo $contractor_customer = $customer_contractor_arr[0]['company_id'];
        
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
        $fax = ' Fax: '.$fax;
    if($email!="" && $tempalte_pdf_1[0]['template_server'] == 1)
        $email=' Email: '.$email;
    
    $supplier = '<p>'. $sales_owner_name .'</p>';
    $supplier.= '<p>'.$sales_owner_address.'</p>';
    $supplier .= '<p>'.$sales_owner_code.' '.$phone.$fax.'</p>';
    if($tempalte_pdf_1[0]['template_default']==1 && $tempalte_pdf_1[0]['template_server'] == 1)
    {
        $supplier .= '<p>CO. Reg No: '. $sales_owner_gst_reg_no .'</p>';
        $supplier .= 'Email Address: '.$email;
        $supplier .= '<br>Website: '.$web;
    }
    else
    {
        $supplier .= '<p>GST Reg No: '. $sales_owner_gst_reg_no .'</p>';
    }
    
    $supplier5= '<p>'.$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].'</p>';
    $supplier5.= '<p>'.$phone.$fax.'</p>';
    if($sales_owner_gst_reg_no!="")
        $supplier5 .= '<p>Reg No: '. $sales_owner_gst_reg_no .'</p>';
    if($email!="")
        $supplier5 .= '<p>'.$email.'</p>';
    if($web!="")
        $supplier5 .= '<p>Website: '.$web.'</p>';
    
    // server innoflex
    $supplier6= '<p>'.$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].' '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].'</p>';
    $supplier6.= '<p>'.$phone.'&nbsp;&nbsp;&nbsp;&nbsp;'.$fax.'&nbsp;&nbsp;&nbsp;&nbsp;'.$email.'</p>';
    $supplier6 .= '<p>GST Reg No: '. $sales_owner_gst_reg_no .'&nbsp;&nbsp;&nbsp;&nbsp;Website: '.$web.'</p>';
     
    
    
    // Logo
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        $files = scandir($url);
        $i = count($files) -1;
        if (count($files) > 2 && $files[$i]!=".svn") {
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
//End supplier
        $state_address_id = false;
        if($_POST['address_id'])
            $state_address_id = $_POST['address_id'];
        $agent_address_id_arr = "0".$address_id_arr;
        $state_address_id;
   // GET Info Customer
        $address_arr = $CReportManager->get_list_address_and_agent($customer_id, $state_address_id,$agent_address_id_arr);

        $attention_arr = $CSalesManager->get_list_attention($customer_id);
        $address_customer=$customer_name.".";
        if(isset($_POST['co_stament']))
            $address_customer.="<br>C/O: ".$_POST['co_stament'];
        
        //$invoice_arr1 =  $CInvoiceManager->list_invoice($customer_id, $load_inv, true, $date_from, $date_to, false);
        $i =0;
        foreach ($address_arr as $address_row2) {
                $address_id12 = $address_row2['address_id'];
                    $invoice_arr2 =  $CInvoiceManager->list_invoice($customer_id, $load_inv, true, $date_from, $date_to, $address_id12);
                    if(count($invoice_arr2)>0){
                        $i++;
                        $address_id = $address_row2['address_id'];
                        $selected="";$address_2="";$brand="";$address_type="";
                        if($address_row2['address_type']==2)
                            $address_type = "[Billing] ";
                        else if($address_row2['address_type']==1)
                            $address_type = "[Job Site] ";
                        else 
                            $address_type = "[NA] ";
                        $brand="";
                        if($address_row2['address_branch_name']!="")
                            $brand = $address_row2['address_branch_name']." - ";
                        $address_2 = "";
                        if($address_row2['address_street_address_2']!="")
                            $address_2 = " ".$address_row2['address_street_address_2'];

                        $opt_address1=$brand.$address_row2['address_street_address_1'].$address_2.' '.'Singapore '.$address_row2['address_postal_zip_code'];
                    }
                
                
        }
        //echo $tes;
//        if($i==1){
//            $address_customer.='<br>'.$opt_address1;
//            echo '<div id="text_address" style="display:none;">'.$address_id.'</div>';
//        }
        if($state_address_id){
            $address_arr = $CSalesManager->get_list_address(false, $state_address_id);
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
            
            $address_customer.='<br>Address: '.$opt_address1;
        }
        //echo "!qwe".$i;
        if(isset($_POST['attention']) && $_POST['attention']!="")
            $address_customer.="<br>Attn: ".$_POST['attention'];
        
if($template_pdf_5==1)
{
    $tbl_header =  '<div id="hd_statements" style="overflow: hidden;">
                        <div style="width:30%;float:left;">
                        <br>
                            <p style="font-size:36px;">Statement</p>
                            <p>Closing Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '.  date($ocio_config['php_abms_date_format'],  strtotime(date('d.M.Y'))).'</p>
                        </div>
                        <div style="width:70%;text-align:right;float:right;">
                            <div >'.$img5.'</div>
                            <div >'.$supplier5.'</div>
                        </div>
                    </div>';
    $tbl_header_tr = '
                    <tr>
                        <td colspan="3">
                            <p><b>Bill To:</b></p>
                            <div style="border: 1px solid #CCCCCC;width:800px;height:90px;padding:4px;">'.$address_customer.'</div>
                        </td>
                        <tr><td height="10">&nbsp;</td></tr>
                    </tr>
        ';
}
else if($template_pdf_6==1)
{
    $tbl_header = '<div id="hd_statements" style="overflow: hidden;font-size:13px;">
                        <div style="width:100%;text-align:center;float:right;">
                            <div >'.$img6.'</div>
                            <div >'.$supplier6.'</div>
                        </div>
                    </div>';
    $tbl_header_tr = '
                    <tr>
                        <td colspan="3">
                            <div style="width:480px;min-height:40px;padding:4px;float:left;margin-bottom:20px;">
                            <p><b>Bill To:</b></p>
                            '.$address_customer.'
                            </div>
                            <div style="float:right;width:300px;text-align:center">
                                    <p style="font-size:36px;">Statement</p>
                                    <p>Closing Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '.  date($ocio_config['php_abms_date_format'],  strtotime(date('d.M.Y'))).'</p>
                            </div>
                        </td>
                    </tr>
        ';
}
else
{
    $tbl_header =  '<div id="hd_statements">
                        <div id="div_left">
                            <div id="logo_supplier">'.$img.'</div>
                            <div id="info_supplier">'.$supplier.'</div>
                        </div>
                        <div id="div_right">
                            <p style="font-size:36px;">Statement</p><br>
                            <p>Closing Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '.  date($ocio_config['php_abms_date_format'],  strtotime(date('d.M.Y'))).'</p>
                        </div>
                    </div>';
    $tbl_header_tr = '
                    <tr>
                        <td colspan="3">
                            <p><b>Bill To:</b></p>
                            <div style="border: 1px solid #CCCCCC;width:800px;height:90px;padding:4px;">'.$address_customer.'</div>
                        </td>
                        <tr><td height="10">&nbsp;</td></tr>
                    </tr>
        ';
}


echo '<div style="margin-bottom:5px;"><button class="ui-button ui-state-default ui-corner-all" onclick="print_statement_report('.$customer_id.'); return false;" target="_blank">Print PDF</button>&nbsp;</div>';      
echo '<div style="width:96%;border: 1px solid #CCCCCC;padding:15px;">';
    echo $tbl_header;
    echo '<table width="100%" border="0" style="margin-top:20px" callpadding="0" cellspacing="0">
            '.$tbl_header_tr.'
            <tr>
                <td colspan="3">';
    $amount30=0;$amount60=0;$amount90=0;$day=0;
    $invoice_arr = "";
    $invoice_is = FALSE;
    foreach ($address_arr as $address_row) {
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
            $invoice_arr =  $CInvoiceManager->list_invoice($customer_id, $load_inv, true, $date_from, $date_to, $address_id);
        if($state_address_id!="")
            $invoice_arr =  $CInvoiceManager->list_invoice($customer_id, $load_inv, true, $date_from, $date_to, $state_address_id);
        if(count($invoice_arr)>0){
            $invoice_is = TRUE;
            if($state_address_id=="" && $i!=1)
                echo '<br><br><b>Address:</b> '.$opt_address;
            echo '<table border="0" width="100%" style="margin-top:4px;" class="tbl" cellpadding="5" cellspacing="2">
                        <thead>
                            <tr id="title_head">
                                <td align="center">Date</td>
                                <td align="center">Invoice Number</td>
                                <td align="right">Amount</td>
                                <td align="right">Payments</td>
                                <td align="right">Due</td>
                            </tr>
                        </thead>
                    <tbody>';
        }
        $amount_table=0;$payment_table=0;$due_table=0;
        foreach ($invoice_arr as $invoice_row) {
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
            
            //total Table
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

            echo
                                '<tr>
                                    <td align="center">'.$invoice_date.'</td>
                                    <td align="center">'.$invoice_row['invoice_no'].'</td>
                                    <td align="right">'.number_format($amount, 2).'</td>
                                    <td align="right">'.number_format($payment, 2).'</td>
                                    <td align="right">'.number_format($due,2).'</td>
                                </tr>';
        }
        
    
        IF(count($invoice_arr)>0){
            echo                '<tr align="right" style="font-weight:bold;">
                                    <td style="background-color: #EEE;" colspan="2"></td>
                                    <td style="background-color: #EEE;">'.number_format($amount_table, 2).'</td>
                                    <td style="background-color: #EEE;">'.number_format($payment_table, 2).'</td>
                                    <td style="background-color: #EEE;">'.number_format($due_table,2).'</td>
                                </tr>';
        }

        echo       '</tbody></table>';
        if($state_address_id!="")
            break;
    }
      echo  '</td></tr></table>
            <br><br>
            <div>The above statement reflect your outstandings to date. We would appreciate it if you could kindly made a cheque payable to "'.$sales_owner_name.'" & mail to "'.$sales_owner_address.', '.$sales_owner_postal.'."</div><br><br>
            <table border="0" width="100%" class="tbl" callpadding="0" cellspacing="2">
                <tr align="center" style="font-weight:bold;">
                    <td>0 - 30 days</td>
                    <td>31 - 60 days</td>
                    <td>61 - 90 days</td>
                    <td>> 90 days</td>
                    <td>Total</td>
                </tr>
                <tr align="center">
                    <td>'.number_format($amount30, 2).'</td>
                    <td>'.number_format($amount60, 2).'</td>
                    <td>'.number_format($amount90, 2).'</td>
                    <td>'.number_format($amountDay, 2).'</td>
                    <td>'.number_format($total_due, 2).'</td>
                </tr>
            </table>';
 echo "</div><br>";
echo '<button class="ui-button ui-state-default ui-corner-all" onclick="print_statement_report('.$customer_id.'); return false;" target="_blank">Print PDF</button>&nbsp;';
}
function _do_print_statement_report_pdf(){
    global $CReportManager;
    $CReportManager->print_statement_report_pdf();
}
function vw_filter_sta(){
    global $AppUI;
    require_once (DP_BASE_DIR."/modules/sales/load_chose.js.php");
    echo 'Load Invoice: <select class="text" name="load_invoice" id="load_invoice">
                <option value="">All</option>
                <option value="2">Paid</option>
                <option value="4" selected>Outstanding</option>
            </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            From: <input class="text" size="12" type="text" name="sta_from" id="sta_from" value=""><input type="hidden" name="hdd_sta_from" id="hdd_sta_from" value="">&nbsp;&nbsp;
            To:<input class="text" size="12" type="text" name="sta_to" id="sta_to" value=""/><input type="hidden" name="hdd_sta_to" id="hdd_sta_to" value=""/>&nbsp;&nbsp;

            <input type="button" class="ui-button ui-state-default ui-corner-all" onclick="search_statement_report(); return false;" name="sta_search" id="sta_search" value="Search" />';
}
function get_address_by_customer(){
    global $CSalesManager,$CReportManager;
    $customer_id = $_POST['customer_id'];
    
   $address_customer_contractor=$CSalesManager->get_Customer_By_ContractorCustomer($customer_id);
   $address_customer_agent = $CSalesManager->get_Customer_By_AgentCustomer($customer_id);
   $address_agent_customer = $CSalesManager->get_AgentCustomer_by_customer($customer_id);
   $address_contractor_customer = $CSalesManager->get_ContractorCustomer_by_customer($customer_id);
   
    
    $address_arr = $CSalesManager->get_list_address($customer_id);
    $option_address = '<option value="-1">--Choose Address--</option><option value="">All</option>';
    
   
    if(count($address_arr))
    {
        $option_address .= '<optgroup label="Own Address">';
    }
    foreach ($address_arr as $address_row) {
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
            $option_address.='<option value='.$address_row['address_id'].' '.$selected.'>'.$address_type.$brand.$address_row['address_street_address_1'].$address_2.' '.'Singapore '.$address_row['address_postal_zip_code'].'</option>';
    }
    
    if(count($address_customer_contractor>0)){
        foreach ($address_customer_contractor as $address_customer_value) {
          $option_address.='<optgroup label="Customer '.$address_customer_value['company_name'].'">';
              $address_contractor_arr = $CSalesManager->get_list_address($address_customer_value['company_id']);
              foreach ($address_contractor_arr as $address_contractor_value) {
                    if($address_contractor_value['address_type']==2)
                        $address_type = "[Billing] ";
                    else if($address_contractor_value['address_type']==1)
                        $address_type = "[Job Site] ";
                    else
                        $address_type = "[NA] ";
                    $brand="";
                    if($address_contractor_value['address_branch_name']!="")
                        $brand = $address_contractor_value['address_branch_name']." - ";
                    $selected = '';
                    if ($address_id == $address_contractor_value['address_id'] || $address_contractor_value['address_id'] == $customer_memo_err[0]['address_id'])
                        $selected = 'selected="selected"';
                  $option_address .= '<option value="'.$address_contractor_value['address_id'].'" '. $selected .'>'.$address_type.$brand.$address_contractor_value['address_street_address_1'].' '.$address_contractor_value['address_street_address_2'].' '.'Singapore '.$address_contractor_value['address_postal_zip_code'].'</option>';
              }
          $option_address.='</optgroup>';
        }
    }
    
    if($address_customer_agent>0){
        foreach ($address_customer_agent as $address_cus_agent_value){
            $option_address.='<optgroup label="Customer '.$address_cus_agent_value['company_name'].'">';
              $address_agent_arr = $CSalesManager->get_list_address($address_cus_agent_value['company_id']);
              foreach ($address_agent_arr as $address_agent_value) {
                    if($address_agent_value['address_type']==2)
                        $address_type = "[Billing] ";
                    else if($address_agent_value['address_type']==1)
                        $address_type = "[Job Site] ";
                    else
                        $address_type = "[NA] ";
                    $brand="";
                    if($address_agent_value['address_branch_name']!="")
                            $brand = $address_agent_value['address_branch_name']." - ";
                    $selected = '';
                    if ($address_id == $address_agent_value['address_id'] || $address_agent_value['address_id'] == $customer_memo_err[0]['address_id'])
                        $selected = 'selected="selected"';
                  $option_address .= '<option value="'.$address_agent_value['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value['address_street_address_1'].$address_agent_value['address_street_address_2'].' '.' '.'Singapore '.$address_agent_value['address_postal_zip_code'].'</option>';
              }
            $option_address.='</optgroup>';
        }
    }
    
    if($address_agent_customer>0){
        foreach ($address_agent_customer as $address_agent_customer_value) {
            if($address_agent_customer_value['address_id']!=""){
                $option_address.='<optgroup label="Agent '.$address_agent_customer_value['company_name'].'">';
                    $address_agent_arr1 = $CSalesManager->get_list_address($address_agent_customer_value['company_id']);
                    foreach ($address_agent_arr1 as $address_agent_value1) {
                        if($address_agent_value1['address_type']==2)
                            $address_type = "[Billing] ";
                        else if($address_agent_value1['address_type']==1)
                            $address_type = "[Job Site] ";
                        else 
                            $address_type = "[NA] ";
                        $brand="";
                        if($address_agent_value1['address_branch_name']!="")
                            $brand = $address_agent_value1['address_branch_name']." - ";                            $selected = '';
                        if ($address_id == $address_agent_value1['address_id'] || $address_agent_value1['address_id'] == $customer_memo_err[0]['address_id'])
                            $selected = 'selected="selected"';
                        $option_address .= '<option value="'.$address_agent_value1['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value1['address_street_address_1'].' '.$address_agent_value1['address_street_address_2'].' '.'Singapore '.$address_agent_value1['address_postal_zip_code'].'</option>';
                    }
                 $option_address.='</optgroup>';
            }
        }
    }
    
    if($address_contractor_customer>0){
        foreach ($address_contractor_customer as $address_contractor_value) {
            if($address_contractor_value['address_contractor']!=0){
                $option_address.='<optgroup label="Contractor '.$address_contractor_value['company_name'].'">';
                    $address_contractor_arr1 = $CSalesManager->get_list_address($address_contractor_value['address_contractor']);
                    foreach ($address_contractor_arr1 as $address_agent_value1) { 
                        if($address_agent_value1['address_type']==2)
                            $address_type = "[Billing] ";
                        else if($address_agent_value1['address_type']==1)
                            $address_type = "[Job Site] ";
                        else 
                            $address_type = "[NA] ";
                        $brand="";
                        if($address_agent_value1['address_branch_name']!="")
                            $brand = $address_agent_value1['address_branch_name']." - ";  
                        $selected = '';
                        if ($address_id == $address_agent_value1['address_id'] || $address_agent_value1['address_id'] == $customer_memo_err[0]['address_id'])
                            $selected = 'selected="selected"';
                        $option_address .= '<option value="'.$address_agent_value1['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value1['address_street_address_1'].' '.$address_agent_value1['address_street_address_2'].' '.'Singapore '.$address_agent_value1['address_postal_zip_code'].'</option>';
                    }
                 $option_address.='</optgroup>'; 
            }
        }
    }
    
    
    //echo $option_address;
    echo '<select id="address_atatement" class="text">'.$option_address.'</select>';
}
function get_attention_by_customer(){
    global $CSalesManager;
    $customer_id = $_POST['customer_id'];
    $attention_arr = $CSalesManager->get_list_attention($customer_id);
    $option_attention ='<option value="">--Choose Attention--</option>';
    foreach ($attention_arr as $attention_row) {
        $option_attention .= '<option value="'.$attention_row['contact_id'].'">'.$attention_row['contact_first_name']." ".$attention_row['contact_last_name'].'</option>"';
    }
    echo $option_attention;
}
function vw_quotation_statement(){
    global $AppUI, $CReportManager, $CSalesManager;
            
    include DP_BASE_DIR."/modules/sales/report.js.php";
    $customer_arr = $CSalesManager->get_list_companies();
    $option_customer = '<option value="" >All</option>';
    foreach ($customer_arr as $customer_row) {
        $option_customer.='<option value="'.$customer_row['company_id'].'" >'.$customer_row['company_name'].'</option>';
    }
    $option_address = '<option value="">All</option>';
    $attention_address = '<option value="">--Choose Attention--</option>';
    
    $status_arr = array('-1'=>"All");
    $status_arr += dPgetSysVal('QuotationStatus');
    
    echo "<div style='font-size:20px;text-align: center; width:100%; margin-top:15px'>Quotation Statements</div>";
    echo '<div style="margin-top: 20px;">
            <div>
                Group By: &nbsp;&nbsp;&nbsp;
                <select name="quotation_gruop" id="quotation_gruop" class="text">
                    <option value="1">Customer</option>
                    <option value="2">Branch</option>
                </select>
            </div>
            <div>Customer: &nbsp;&nbsp;
                    <select name="select_quo_statement" id="select_quo_statement" onchange="load_address_quo_sta_by_customer(this.value)">
                        '.$option_customer.'
                    </select>
            </div>
            <br>
            <div>Address:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span id="sel_quo_sta_address">
                    <select name="select_quo_statement_address" id="select_quo_statement_address">
                        '.$option_address.'
                    </select>
                </span>
            </div>
            <br>
            <div>Status: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    '.$quotation_stt_dropdown = arraySelect($status_arr, 'quotation_status_id','id="quotation_status_id" class="text"','-1', true).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                From:
                    <input class="text" type="text" name="quo_sta_from_date" readonly="true" id="quo_sta_from_date" size="12" />
                    <input class="text" type="hidden" name="hdd_quo_sta_from_date" id="hdd_quo_sta_from_date"/>&nbsp;&nbsp;&nbsp;&nbsp;
                To:
                    <input class="text" type="text" name="quo_sta_to_date" readonly="true" id="quo_sta_to_date" size="12" />
                    <input class="text" type="hidden" name="hdd_quo_sta_to_date" id="hdd_quo_sta_to_date" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input id="quo_sta_search" name="quo_sta_search" type="button" value="Search" onclick="load_search_quo_statement();return false;" class="ui-button ui-state-default ui-corner-all" />
            </div>
            
        </div><br>';
    echo '<div id="tbl_quotation_statement"></div>';
}

function view_tbl_quotation_statement(){
    global $AppUI, $CReportManager, $CQuotationManager, $CSalesManager,$user_roles,$ocio_config;
    
    // Kiem tra neu user co roles la "Innoflex Business Development Manager" thi chi duoc view quotaiton cua minh
    $view_report_quotation_user = false;
    if(in_array('innoflex_business_development_manager',$user_roles))
            $view_report_quotation_user = $AppUI->user_id;
    
    $status_arr = dPgetSysVal('QuotationStatus');
    $customer_id =false; $status=false; $address_id=false; $from=false; $to = false;
    if(isset($_POST['customer_id']) && $_POST['customer_id']!="")
        $customer_id = $_POST['customer_id'];
    if(isset($_POST['address_id']) && $_POST['address_id']!="")
        $address_id = $_POST['address_id'];
    if($_POST['status_id'] && $_POST['status_id']!="");
        $status = intval($_POST['status_id']);
    if($_POST['from']){
        $from = $_POST['from'];
    }
    if($_POST['to']){
        $to=$_POST['to'];
    }
    
    echo '<div style="margin-bottom:5px;"><button class="ui-button ui-state-default ui-corner-all" onclick="load_print_quo_statement(); return false;" target="_blank">Print PDF</button>&nbsp;</div>';
    echo '<table width="100%" cellspacing="0" cellpadding="6" border="0">
            <thead>
                <tr id="title_head">
                    <td width="10%">Date</td>
                    <td width="10%">Number</td>
                    <td width="40%">Customer</td>
                    <td width="10%">status</td>
                    <td width="22%">subject</td>
                    <td align="right">Total</td>
                </tr>
            </thead>';

   echo    '<tbody>';
    $custome_array = $CQuotationManager->getCustomerByQuotation($customer_id);
        foreach($custome_array as $custome_data)
        {
               $totalCustomer = 0;
               $quo_statement_arr = $CReportManager->get_quotation_statement($custome_data['company_id'], $status, $address_id, $from, $to,$view_report_quotation_user);
               if(count($quo_statement_arr)>0){
                foreach ($quo_statement_arr as $quo_statement_row) {
                    $quotation_id = $quo_statement_row['quotation_id'];
                    $quotation_rev_arr = $CQuotationManager->get_latest_quotation_revision($quotation_id);
                    $quotation_revision_id = $quotation_rev_arr[0]['quotation_revision_id'];

                     $brand="";
             //        $address_type="";
             //        if($quo_statement_row['address_type']==2)
             //            $address_type = "[Billing] ";
             //        else if($quo_statement_row['address_type']==1)
             //            $address_type = "[Job Site] ";
             //        else 
             //            $address_type = "[NA] ";
                     if($quo_statement_row['address_branch_name']!="")
                         $brand=$quo_statement_row['address_branch_name'].' - ';
                     $address_2="";
                     if($quo_statement_row['address_street_address_2']!="")
                         $address_2= ', '.$quo_statement_row['address_street_address_2'];
                     $postal_code_job = '';
                     if($quo_statement_row['address_postal_zip_code']!="")
                         $postal_code_job .=', Singapore '.$quo_statement_row['address_postal_zip_code'];

                     $job_location=$brand.$CSalesManager->htmlChars($quo_statement_row['address_street_address_1'].$address_2.$postal_code_job);   
                     $total = round($CQuotationManager->get_total_tax_and_paid($quotation_revision_id, $CQuotationManager->get_quotation_item_total($quotation_id, $quotation_revision_id)),2);
                     $totalCustomer+=$total;
                     echo        '<tr>
                                      <td>'.date(date($ocio_config['php_abms_date_format'], strtotime($quo_statement_row['quotation_date']))).'</td>
                                      <td>'.$quotation_rev_arr[0]['quotation_revision'].'</td>
                                      <td>'.$quo_statement_row['company_name'].'
                                          <div style="color:#666;font-size:0.9em;">'.$job_location.'</div>
                                      </td>
                                      <td>'.$status_arr[$quo_statement_row['quotation_status']].'</td>
                                      <td>'.$quo_statement_row['quotation_subject'].'&nbsp;</td>
                                      <td align="right">$'.number_format($total,2).'</td>
                                  </tr>';
                }

                echo '<tr valign="top">
                         <td colspan="5"><b>'.$custome_data['company_name'].'</b></td>
                         <td align="right" style="border-top: 1px solid #000;"><b>$'.number_format($totalCustomer,2).'</b></td>
                    </tr>';
                echo '<tr><td colspan="6"><hr></td></tr>';
        }
    }
  echo     '</tbody>
        </table>';
  echo '<br><br><button class="ui-button ui-state-default ui-corner-all" onclick="load_print_quo_statement(); return false;" target="_blank">Print PDF</button>&nbsp;';
}

function view_tbl_quotation_statement_brand(){
    global $AppUI, $CReportManager, $CQuotationManager, $CSalesManager,$user_roles;
    $status_arr = dPgetSysVal('QuotationStatus');
    
    // Kiem tra neu user co roles la "Innoflex Business Development Manager" thi chi duoc view quotaiton cua minh
    $view_report_quotation_user = false;
    if(in_array('innoflex_business_development_manager',$user_roles))
        $view_report_quotation_user = $AppUI->user_id;
    
    $customer_id =false; $status=false; $address_id=false; $from=false; $to = false;
    if(isset($_POST['customer_id']) && $_POST['customer_id']!="")
        $customer_id = $_POST['customer_id'];
    if(isset($_POST['address_id']) && $_POST['address_id']!="")
        $address_id = $_POST['address_id'];
    if($_POST['status_id'] && $_POST['status_id']!="");
        $status = intval($_POST['status_id']);
    if($_POST['from']){
        $from = $_POST['from'];
    }
    if($_POST['to']){
        $to=$_POST['to'];
    }
    
    echo '<div style="margin-bottom:5px;"><button class="ui-button ui-state-default ui-corner-all" onclick="load_print_quo_statement(); return false;" target="_blank">Print PDF</button>&nbsp;</div>';
    echo '<table width="100%" cellspacing="0" cellpadding="6" border="0">
            <thead>
                <tr id="title_head">
                    <td width="10%">Date</td>
                    <td width="10%">Number</td>
                    <td width="40%">Customer</td>
                    <td width="10%">status</td>
                    <td width="22%">subject</td>
                    <td align="right">Total</td>
                </tr>
            </thead>';

    echo    '<tbody>';
    $customer_arr = $CQuotationManager->getCustomerByQuotation($customer_id, $address_id, 2);
    //print_r($customer_arr);
    foreach ($customer_arr as $customer_data) {
        
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
         
        $quo_statement_arr = $CReportManager->get_quotation_statement_brand($customer_data['company_id'], $status,$customer_data['address_id'], $from, $to, $view_report_quotation_user);
        if(count($quo_statement_arr)>0)
        {
            $totalCustomer = 0;
            foreach ($quo_statement_arr as $quo_statement_row) {
                $quotation_id = $quo_statement_row['quotation_id'];
                $quotation_rev_arr = $CQuotationManager->get_latest_quotation_revision($quotation_id);
                $quotation_revision_id = $quotation_rev_arr[0]['quotation_revision_id'];

                 $brand="";
         //        $address_type="";
         //        if($quo_statement_row['address_type']==2)
         //            $address_type = "[Billing] ";
         //        else if($quo_statement_row['address_type']==1)
         //            $address_type = "[Job Site] ";
         //        else 
         //            $address_type = "[NA] ";
                 if($quo_statement_row['address_branch_name']!="")
                     $brand=$quo_statement_row['address_branch_name'].' - ';
                 $address_2="";
                 if($quo_statement_row['address_street_address_2']!="")
                     $address_2= ', '.$quo_statement_row['address_street_address_2'];
                 $postal_code_job = '';
                 if($quo_statement_row['address_postal_zip_code']!="")
                     $postal_code_job .=', Singapore '.$quo_statement_row['address_postal_zip_code'];

                 $job_location=$brand.$CSalesManager->htmlChars($quo_statement_row['address_street_address_1'].$address_2.$postal_code_job);   
                 $total = round($CQuotationManager->get_total_tax_and_paid($quotation_revision_id, $CQuotationManager->get_quotation_item_total($quotation_id, $quotation_revision_id)),2);
                 $totalCustomer+=$total;
                 echo        '<tr>
                                  <td>'.date('d-m-Y',  strtotime($quo_statement_row['quotation_date'])).'</td>
                                  <td>'.$quotation_rev_arr[0]['quotation_revision'].'</td>
                                  <td>'.$quo_statement_row['company_name'].'
                                      <div style="color:#666;font-size:0.9em;">'.$job_location.'</div>
                                  </td>
                                  <td>'.$status_arr[$quo_statement_row['quotation_status']].'</td>
                                  <td>'.$quo_statement_row['quotation_subject'].'&nbsp;</td>
                                  <td align="right">$'.number_format($total,2).'</td>
                              </tr>';

            }
        }
        if(count($quo_statement_arr)>0)
        {
            echo '<tr valign="top">
                     <td colspan="5"><b>'.$customer_data['company_name'].' ('.$job_location_customer.')</b></td>
                     <td align="right" style="border-top: 1px solid #000;"><b>$'.number_format($totalCustomer,2).'</b></td>
                </tr>';
            echo '<tr><td colspan="6"><hr></td></tr>';
        }
    }
//echo '<br><br><button class="ui-button ui-state-default ui-corner-all" onclick="load_print_quo_statement(); return false;" target="_blank">Print PDF</button>&nbsp;';
}

function get_address_quo_by_customer(){
    global $CSalesManager;
    require_once (DP_BASE_DIR."/modules/sales/load_chose.js.php");
    $customer_id = $_POST['customer_id'];
    $address_arr = $CSalesManager->get_list_address($customer_id);
    $option_address = '<option value="">All</option>';
    foreach ($address_arr as $address_row) {
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
            $option_address.='<option value='.$address_row['address_id'].' '.$selected.'>'.$address_type.$brand.$address_row['address_street_address_1'].$address_2.' '.'Singapore '.$address_row['address_postal_zip_code'].'</option>';
    }
    //echo $option_address;
    echo '<select id="select_quo_statement_address" class="text">'.$option_address.'</select>';
}
function _do_print_quo_statement(){
    global $CReportManager;
    $CReportManager->print_quo_statement();
}
function _do_print_quo_statement_brand()
{
    global $CReportManager;
    $CReportManager->print_quo_statement_brand();
}

function view_credit_note()
{
  global $AppUI, $CSalesManager;
    
    $m=date('m');
    $y=date('Y');
    $date_one = strtotime("{$y}-{$m}-01");
    $date_last = strtotime('-1 second', strtotime('+1 month', $date_one));
    $from_date=date('Y-m-d', $date_one);
    $to_date=date('Y-m-d', $date_last);
    
    include DP_BASE_DIR."/modules/sales/report.js.php";
    $customer_arr = $CSalesManager->get_list_companies();
    $option_customer = '<option value="" >All</option>';
    foreach ($customer_arr as $customer_row) {
        $option_customer.='<option value="'.$customer_row['company_id'].'" >'.$customer_row['company_name'].'</option>';
    }
    
    $credit_status = array('' => 'All');
    $credit_status += dPgetSysVal('CreditStatus');
    $credit_status_dropdown = arraySelect($credit_status, 'invoice_status_id', 'id="invoice_status_id" class="text" size="1"', 3, true);
    
    
    echo "<div style='font-size:20px;text-align: center; width:100%; margin-top:15px'>Credit Note</div>";
    echo '<div style="margin-top: 20px;">
            <div>Customer: &nbsp;&nbsp;
                    <select name="credit_customer" id="credit_customer">
                        '.$option_customer.'
                    </select>
                    Status: &nbsp;&nbsp;
                    '.$credit_status_dropdown.'
                    From:
                        <input class="text" type="text" name="credit_from_date" readonly="true" value="'.date('d/M/Y',strtotime($from_date)).'" id="credit_from_date" size="12" />
                        <input class="text" type="hidden" name="hdd_credit_from_date" value="'.$from_date.'" id="hdd_credit_from_date"/>&nbsp;&nbsp;&nbsp;&nbsp;
                    To:
                        <input class="text" type="text" name="credit_to_date" readonly="true" value="'.date('d/M/Y',  strtotime($to_date)).'" id="credit_to_date" size="12" />
                        <input class="text" type="hidden" name="hdd_credit_to_date" id="hdd_credit_to_date" value="'.$to_date.'" />&nbsp;&nbsp;&nbsp;&nbsp;
                    <input id="credit_note_search" name="credit_note_search" type="button" value="Search" onclick="load_data_credit_note();return false;" class="ui-button ui-state-default ui-corner-all" />
            </div>
            
        </div><br>';
    echo '<div id="tbl_credit_note"></div>';
}

function tbl_credit_note(){
    global $CCreditManager,$CSalesManager,$ocio_config;
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    
    $customer_id=false;$from_date=false;$to_date=false;
    if(isset($_POST['customer_id']))
        $customer_id = $_POST['customer_id'];
    if(isset($_POST['from_date']))
        $from_date = $_POST['from_date'];
    if(isset($_POST['to_date']))
        $to_date = $_POST['to_date'];
    $status = false;
    if(isset($_POST['invoice_status_id']))
        $status = $_POST['invoice_status_id'];
        
    $credit_arr = $CCreditManager->list_db_creditNote_report($customer_id,$from_date,$to_date,1,$status);
    echo '<button class="ui-button ui-state-default ui-corner-all" onclick="load_print_credit_note(); return false;" target="_blank">Print PDF</button>&nbsp;';
    echo '<table width="100%" cellspacing="0" cellpadding="6" style="margin-top:5px;" border="0">
                <tr id="title_head">
                    <td align="center">S/No</td>
                    <td>Name</td>
                    <td>Credit Note No</td>
                    <td align="center">Dated</td>
                    <td align="center">Invoice Nos</td>
                    <td align="right">Amount</td>
                    <td align="right">GST</td>
                    <td align="right">Total</td>
                </tr>';
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
        
        echo        '<tr>
                        <td align="center">'.$i.'</td>
                        <td>'.$credit_item["company_name"].'</td>
                        <td>'.$credit_item["credit_note_no"].'</td>
                        <td align="center">'.date($ocio_config['php_abms_date_format'],  strtotime($credit_item["credit_note_date"])).'</td>
                        <td align="center">'.$credit_item['invoice_no'].'</td>
                        <td align="right">$'.number_format($amount,2).'</td>
                        <td align="right">$'.number_format($total_tax,2).'</td>
                        <td align="right">$'.number_format($total,2).'</td>
                    </tr>';
    }
    echo'<tr style="font-weight:bold;">
            <td colspan="5">Total</td>
            <td align="right">$'.number_format($amountCredit,2).'</td>
            <td align="right">$'.number_format($taxCredit,2).'</td>
            <td align="right">$'.number_format($totalCredit,2).'</td>
    </tr>';
    echo '</table>';
}

function _do_print_credit_note()
{
    global $AppUI, $CReportManager;
    $CReportManager->print_credit_note();
}
//function _get_address_select_customer($customer_id = false, $address_id=false) {
//    
//   global $CSalesManager;
//    /* Load Billing Adress */   
//    $address_option = '<option value="" >--Select--</option>';
//        if($customer_id && $customer_id!="")
//            $address_arr = $CSalesManager->get_list_address($customer_id);
//        else if($_POST['customer_id'] != ""){
//            $address_arr = $CSalesManager->get_list_address($_POST['customer_id']);
//        }
//            if (count($address_arr) > 0) {
//                $address_option .= '<optgroup label="Own Address">';
//                foreach ($address_arr as $address) {
//                    if($address['address_type']==2)
//                        $address_type = "[Billing] ";
//                    else if($address['address_type']==1)
//                        $address_type = "[Job Site] ";
//                    else 
//                        $address_type = "[NA] ";
//                    $brand="";
//                    if($address['address_branch_name']!="")
//                        $brand = $address['address_branch_name']." - ";
//                    $selected = '';
//                    if ($address_id == $address['address_id'] || $address['address_id'] == $customer_memo_err[0]['address_id'])
//                        $selected = 'selected="selected"';
//                    $address_option .= '<option value="'. $address['address_id'] .'" '. $selected .'>'.$address_type.$brand.$address['address_street_address_1'].' '.$address['address_street_address_2'].' '.'Singapore '.$address['address_postal_zip_code'].'</option>';
//                }
//                $address_option.='</optgroup>';
//            }
//        // Lay adress customer gan customer duoc chon lam contractor
//            if($customer_id)
//                $address_customer_contractor = $CSalesManager->get_Customer_By_ContractorCustomer($customer_id);
//            else if($_POST['customer_id'] != ""){
//                $address_customer_contractor = $CSalesManager->get_Customer_By_ContractorCustomer($_POST['customer_id']);
//            }
//            if(count($address_customer_contractor>0)){
//                foreach ($address_customer_contractor as $address_customer_value) {
//                  $address_option.='<optgroup label="Customer '.$address_customer_value['company_name'].'">';
//                      $address_contractor_arr = $CSalesManager->get_list_address($address_customer_value['company_id']);
//                      foreach ($address_contractor_arr as $address_contractor_value) {
//                            if($address_contractor_value['address_type']==2)
//                                $address_type = "[Billing] ";
//                            else if($address_contractor_value['address_type']==1)
//                                $address_type = "[Job Site] ";
//                            else
//                                $address_type = "[NA] ";
//                            $brand="";
//                            if($address_contractor_value['address_branch_name']!="")
//                                $brand = $address_contractor_value['address_branch_name']." - ";
//                            $selected = '';
//                            if ($address_id == $address_contractor_value['address_id'] || $address_contractor_value['address_id'] == $customer_memo_err[0]['address_id'])
//                                $selected = 'selected="selected"';
//                          $address_option .= '<option value="'.$address_contractor_value['address_id'].'" '. $selected .'>'.$address_type.$brand.$address_contractor_value['address_street_address_1'].' '.$address_contractor_value['address_street_address_2'].' '.'Singapore '.$address_contractor_value['address_postal_zip_code'].'</option>';
//                      }
//                  $address_option.='</optgroup>';
//                }
//            }
//
//        // Lay address customer gan customer duoc chon lam Agent
//        if($customer_id)
//            $address_customer_agent = $CSalesManager->get_Customer_By_AgentCustomer($customer_id);
//        else if($_POST['customer_id'] != "")
//            $address_customer_agent = $CSalesManager->get_Customer_By_AgentCustomer($_POST['customer_id']);
//        if($address_customer_agent>0){
//            foreach ($address_customer_agent as $address_cus_agent_value){
//                $address_option.='<optgroup label="Customer '.$address_cus_agent_value['company_name'].'">';
//                  $address_agent_arr = $CSalesManager->get_list_address($address_cus_agent_value['company_id']);
//                  foreach ($address_agent_arr as $address_agent_value) {
//                        if($address_agent_value['address_type']==2)
//                            $address_type = "[Billing] ";
//                        else if($address_agent_value['address_type']==1)
//                            $address_type = "[Job Site] ";
//                        else
//                            $address_type = "[NA] ";
//                        $brand="";
//                        if($address_agent_value['address_branch_name']!="")
//                                $brand = $address_agent_value['address_branch_name']." - ";
//                        $selected = '';
//                        if ($address_id == $address_agent_value['address_id'] || $address_agent_value['address_id'] == $customer_memo_err[0]['address_id'])
//                            $selected = 'selected="selected"';
//                      $address_option .= '<option value="'.$address_agent_value['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value['address_street_address_1'].$address_agent_value['address_street_address_2'].' '.' '.'Singapore '.$address_agent_value['address_postal_zip_code'].'</option>';
//                  }
//                $address_option.='</optgroup>';
//            }
//        }
//
//        //Lay add customer la agent cua customer duoc chon
//        if($customer_id && $customer_id!="")
//            $address_agent_customer = $CSalesManager->get_AgentCustomer_by_customer($customer_id);
//        else if($_POST['customer_id'] != "")
//            $address_agent_customer = $CSalesManager->get_AgentCustomer_by_customer($_POST['customer_id']);
//        if($address_agent_customer>0){
//            foreach ($address_agent_customer as $address_agent_customer_value) {
//                if($address_agent_customer_value['address_id']!=""){
//                    $address_option.='<optgroup label="Agent '.$address_agent_customer_value['company_name'].'">';
//                        $address_agent_arr1 = $CSalesManager->get_list_address($address_agent_customer_value['company_id']);
//                        foreach ($address_agent_arr1 as $address_agent_value1) {
//                            if($address_agent_value1['address_type']==2)
//                                $address_type = "[Billing] ";
//                            else if($address_agent_value1['address_type']==1)
//                                $address_type = "[Job Site] ";
//                            else 
//                                $address_type = "[NA] ";
//                            $brand="";
//                            if($address_agent_value1['address_branch_name']!="")
//                                $brand = $address_agent_value1['address_branch_name']." - ";                            $selected = '';
//                            if ($address_id == $address_agent_value1['address_id'] || $address_agent_value1['address_id'] == $customer_memo_err[0]['address_id'])
//                                $selected = 'selected="selected"';
//                            $address_option .= '<option value="'.$address_agent_value1['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value1['address_street_address_1'].' '.$address_agent_value1['address_street_address_2'].' '.'Singapore '.$address_agent_value1['address_postal_zip_code'].'</option>';
//                        }
//                     $address_option.='</optgroup>';
//                }
//            }
//        }
//
//        //Lay add customer la contractor cua customer duoc chon
//        if($customer_id)
//            $address_contractor_customer = $CSalesManager->get_ContractorCustomer_by_customer($customer_id);
//        else if($_POST['customer_id'] != ""){
//            $address_contractor_customer = $CSalesManager->get_ContractorCustomer_by_customer($_POST['customer_id']);
//        }
//        if($address_contractor_customer>0){
//            foreach ($address_contractor_customer as $address_contractor_value) {
//                if($address_contractor_value['address_contractor']!=0){
//                    $address_option.='<optgroup label="Contractor '.$address_contractor_value['company_name'].'">';
//                        $address_contractor_arr1 = $CSalesManager->get_list_address($address_contractor_value['address_contractor']);
//                        foreach ($address_contractor_arr1 as $address_agent_value1) { 
//                            if($address_agent_value1['address_type']==2)
//                                $address_type = "[Billing] ";
//                            else if($address_agent_value1['address_type']==1)
//                                $address_type = "[Job Site] ";
//                            else 
//                                $address_type = "[NA] ";
//                            $brand="";
//                            if($address_agent_value1['address_branch_name']!="")
//                                $brand = $address_agent_value1['address_branch_name']." - ";  
//                            $selected = '';
//                            if ($address_id == $address_agent_value1['address_id'] || $address_agent_value1['address_id'] == $customer_memo_err[0]['address_id'])
//                                $selected = 'selected="selected"';
//                            $address_option .= '<option value="'.$address_agent_value1['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value1['address_street_address_1'].' '.$address_agent_value1['address_street_address_2'].' '.'Singapore '.$address_agent_value1['address_postal_zip_code'].'</option>';
//                        }
//                     $address_option.='</optgroup>'; 
//                }
//            }
//        }
//        echo $address_option;
//}


function view_profit()
{
    global $ocio_config,$CReportManager,$CSalesManager,$CInvoiceManager,$CCompany,$AppUI,$CCreditManager;
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
         //$date = date('d', strtotime($string_date)).'/'.date('m', strtotime($string_date)).'/'.date('Y', strtotime($string_date));
         //$time = date('H', strtotime($string_date)).':'.date('i', strtotime($string_date)).':'.date('s', strtotime($string_date));
         $from_date = $_POST['from_date'];
        $to_date =  $_POST['to_date'];
      
        $department = new CDepartment();
        $getDepartment = $department->getDeparmentByUser($AppUI->user_id);
        
        
        
        
        $CCompany = new CompanyManager();
        $rows = $CCompany->getCompanyJoinDepartment();

        echo '<div style="margin-bottom:5px;">';
        echo '<button target="_blank" onclick="print_profit_report(); return false;" class="ui-button ui-state-default ui-corner-all">Print PDF</button><br />';
        echo '</div>'; 
        echo '<div style="width:96%;border: 1px solid #CCCCCC;padding:15px;">';
        echo '<div class = "header_profit" style="font-size:20px;text-align: center; width:100%">';
        echo '<div id ="logo">'.$img.'</div><br />';
        echo '<p style="font-size:14px; margin-bottom:30px;">'.$supplier.'</p>';
        echo '<div id = "company" profit> Profit & Loss Statement'.'</div>';
        $date_view = "";
        if($from_date!="")
            $date_view.='From : '.date($ocio_config['php_abms_date_format'],  strtotime($from_date));
        if($to_date!="")
            $date_view.=' To:'.date($ocio_config['php_abms_date_format'],  strtotime($to_date));
        if($from_date=="" && $to_date =="")
            $date_view = ' To: '.date('d/m/Y');
        echo '<div style="font-size:14px;text-align: center; width:100%">'.$date_view.'</div><br />';
            
        echo '</div>';
        echo '<div style="margin-bottom:10px;">';
        echo $string_date.'<br />';
      
        echo '</div>';
       
        echo '<div id ="content_profit" style="width:96%;border-top: 1px solid #CCCCCC; "> ';
            echo '<table>';
            
            $info_depart= '<tr><td>';
                $info_depart.= '<table   width="100%"  border="0" style="padding-left:20px">';
                $totalQuotation = 0;
                $total_credit=0;
                //Tinh total invoice co department
                
                foreach ($rows as $data)
                {
                    if($data['dept_id'] > 0)
                    {
                        
                        //Tinh payment cua invoice ko co quotation accepted va department_id > 0
                        $totalInvoice = $CInvoiceManager->calculateTotalInvoiceByDepartment($data['dept_id'],$from_date,$to_date);
                        
                        $totalSum = $total+$totalInvoice;
                       
                        $info_depart.= '<tr>'
                            . '<td >'.$data['dept_name'].'</td><td style="padding-right:300px"></td>'
                            . '<td align="right">$'.number_format($totalSum,2, '.', ',').'</td>'
                                . '</tr>';
                        $totalQuotation += $totalSum;

                     }
                }
                
                
                //Tinh payment cua invoice khong co department 
                $totalInvoiceNotDepartment = $CInvoiceManager->calculateTotalInvoiceByDepartment(0,$from_date,$to_date);
                //$totalCredit += $totalInvoiceNotDepartment[credit_note_amounr];
                
                //tinh tong invoice sales
                echo '<tr><td style="margin-top:10px;">Income</td>';
            
                echo '<td style="border-bottom: 1px solid #CCCCCC; " align="right">$'.number_format($totalQuotation+$totalInvoiceNotDepartment,2, '.', ',').'</td><td style="width:30%" align="right"></td>';
                echo '</tr>';
                
                echo $info_depart;
                
                echo '<tr>';
                echo '<td>Other</td><td style="padding-right:300px"></td>';
               
                echo '<td align="right">$'.number_format($totalInvoiceNotDepartment,2, '.', ',').'</td>';
                echo '</tr>';
                echo '</table>'
            . '</td></tr>';
                $totalCredit = $CCreditManager->totalCreditNoteIsInvoice($from_date,$to_date);
                
                $totalicome = $totalQuotation+$totalInvoiceNotDepartment-$totalCredit;
             echo '<tr><td style="">Less Credit Notes</td>';
             echo '<td style="border-bottom: 1px solid #CCCCCC; " align="right">$'.number_format($totalCredit,2, '.', ',').'</td><td style="width:30%" align="right"></td></tr>';
             echo '<tr><td style="">Total Income</td>';
             echo '<td style="border-bottom: 1px solid #CCCCCC; " align="right">$'.number_format($totalicome,2, '.', ',').'</td><td style="width:30%" align="right"></td></tr>';

            
            
            echo '<tr><td> Cost of Sales</td></tr>';
//            echo '<tr><td style="padding-left:20px">Direct Job Costs</td></tr>';
            echo '<tr>'
            . '<td><table   width="100%"  border="0" style="padding-left:20px" ></td></tr>';
            echo '<td style="">Direct Job Costs</td>';
            $totalPo = 0;
            foreach ($rows as $data)
            {
                if($data['dept_id'] > 0)
                {
                    $po_invoice = new po_invoice();
                    $SIDepartment = $po_invoice->calculatorSIDepartment($from_date,$to_date,$data['dept_id']);
                    echo '<tr>'
                        . '<td style="padding-left:40px">'.$data['dept_name'].'</td>'
                            
                        . '<td align="right">$'.number_format($SIDepartment,2, '.', ',').'</td><td></td>'
                            . '</tr>';
                    $totalPo += $SIDepartment;

                 }
            }?>
            <?php
                // Lấy SI không thuộc department
                $SINODepartment = $po_invoice->calculatorSIDepartment($from_date,$to_date,-10);
                $totalPo += $SINODepartment;
            ?>
            <tr>
                <td style="padding-left:40px">Orther</td>
                    <td align="right">$<?php echo number_format($SINODepartment,2, '.', ','); ?></td>
                    <td></td>
            </tr>
<?php
            $totalMater = 0;
            echo '<tr><td style="">Total Direct Job Costs</td><td></td>';
            echo '<td style="border-bottom: 1px solid #CCCCCC;" align="right">$'.number_format($totalPo,2, '.', ',').'</td></tr>';
//            echo '<tr><td style="">Materials-M&E</td><td></td>';
//            echo '<td style="">$'.number_format($totalMater,2, ',', '.').'</td></tr>';
            echo '</table></td></tr>';

            
            $TotalCostOfSales = $totalMater+$totalPo;
            echo '<tr><td >Total Cost Of Sales</td>';
            
            echo '<td style="border-bottom: 1px solid #CCCCCC;" style="width:30%" align="right">$'.number_format($TotalCostOfSales,2, '.', ',').'</td><td style="width:30%" align="right"></td></tr>';
           
            $Gross_profit = $totalicome - $TotalCostOfSales;
            
            //Gross profit
           
            echo '<tr><td>Gross profit</td>';
            echo '<td style="border-bottom: 1px solid #CCCCCC;" align="right">$'.number_format($Gross_profit,2, '.', ',').'</td>';
            echo '<td style="width:30%" align="right"></td></tr>';
            echo '<br>';
            echo '</div>';
            echo '</table>';
            
        //Expenses
        $category_arr = dPgetSysVal('Categories');
        asort($category_arr);

        echo '<div id ="content_profit" style="width:96%;border-top: 1px solid #CCCCCC; "> ';
        echo '<p style="margin-top:20px;">Expenses</p>';
         echo '<table   width="100%"  border="0" style="margin-top:2px; margin-left:10px">';
            $totalEx = 0;
            foreach ($category_arr as $key => $value)
            {
                $expensesManage = new ExpensesManager();
                
                $ExBycategory = $expensesManage->totalExByCategory($key,$from_date,$to_date);
                
                echo '<tr>'
                        . '<td >'.$value.'</td>'
                        . '<td align="right">$'.number_format($ExBycategory,2, '.', ',').'</td><td style="width:35%"></td>'
                     . '</tr>';
                $totalEx += $ExBycategory;

                
            }
            $totalNoCategory = $expensesManage->totalExByCategory(-1,$from_date,$to_date);
            $totalEx +=$totalNoCategory;
            echo '<tr>'
                    . '<td>Other</td>'
                    . '<td align="right">$'.  number_format($totalNoCategory,2).'</td>'
                    . '<td style="width:35%"></td>'
                  . '</tr>';
            echo '</table>';
         
        echo '<table   width="100%"  border="0" style="margin-top:2px;">';
            echo '<tr><td style="padding-right:30px">Total Expenses</td>';
            echo '<td style="border-bottom: 1px solid #CCCCCC;">$'.number_format($totalEx,2, '.', ',').'</td></tr>';
            echo '</table><br>';
           
        echo '<div>';
        
        //total operating
        $totalOperating = $Gross_profit - $totalEx;
        echo '<div id ="content_profit" style="width:96%;border-top: 1px solid #CCCCCC; margin-top:20px; "> ';
         echo '<table   width="100%"  border="0" style="margin-top:20px;">';
            echo '<tr><td style="padding-right:130px">Operating Profit</td>';
            echo '<td style="border-bottom: 1px solid #CCCCCC;" align="right">$'.number_format($totalOperating,2, '.', ',').'</td><td style="width:20%"></td></tr>';
      
        
        
        //Other income
        $Orther_income = 0;
      
        
//            echo '<tr><td style="padding-right:130px">Other Income</td>';
//            echo '<td style="border-bottom: 1px solid #CCCCCC;">$'.number_format($Orther_income,2, ',', '.').'</td></tr>';
           
            
        //Orther expenses
            $other_ex= 0;
          
        
//            echo '<tr><td style="padding-right:130px">Other Expenses</td>';
//            echo '<td style="border-bottom: 1px solid #CCCCCC;">$'.number_format($other_ex,2, ',', '.').'</td></tr>';
          
            
        //Net Profit/(loss)
            $net_profit = $Orther_income + $totalOperating;
           
       
            echo '<tr><td style="padding-right:130px">Net Profit/(loss)</td>';
            echo '<td style="border-bottom: 1px solid #CCCCCC;" align="right">$'.number_format($net_profit,2, '.', ',').'</td></tr><td style="width:20%"></td>';
           
        echo '</div>';
        
        echo '</div>';
        
       
      
       
       
}

function vw_statements_profit(){
    global $AppUI, $CReportManager, $CSalesManager;
    include DP_BASE_DIR."/modules/sales/report.js.php";
    $customer_arr = $CSalesManager->get_list_companies();
    $option_customer = '<option value="" >Choose Customer</option>';
    foreach ($customer_arr as $customer_row) {
        $option_customer.='<option value="'.$customer_row['company_id'].'" >'.$customer_row['company_name'].'</option>';
    }
    $option_address = '<option value="-1">--Choose Address--</option>';
    $attention_address = '<option value="">--Choose Attention--</option>';
    
    echo '<div style="width:100%">
                <div style="font-size:20px;text-align: center; width:100%; margin-top:15px">Profit & Loss Statement</div>
               
                <div style="margin-top:15px; text-align:center;" id ="filter_sta">
                    
                    From: <input class="text" size="12" type="text" name="sta_from" id="sta_from" value=""><input type="hidden" name="hdd_sta_from" id="hdd_sta_from" value="">&nbsp;&nbsp;
                    To:<input class="text" size="12" type="text" name="sta_to" id="sta_to" value=""/><input type="hidden" name="hdd_sta_to" id="hdd_sta_to" value=""/>&nbsp;&nbsp;

                    <input type="button" class="ui-button ui-state-default ui-corner-all" onclick="search_statements_profit(); return false;" name="sta_search" id="sta_search" value="Search" />
                </div>
        </div>';
    echo '<div id="tbl_profit" style="width:80%;margin:50px auto;"></div>';
    
   
}

function  _do_print_profit_pdf()
{
    global $CReportManager;
    $CReportManager->print_profit_report_pdf();
}
function  _do_read_pdf()
{
    global $CReportManager;
    $file="";
    if(isset($_GET['data_to']))
    {
        $file=DP_BASE_DIR.'/backup/sales_pdf/aging_report/aging_report_'.$_GET['data_to'].".pdf";

    }
    if(file_exists($file)){

        header("Content-Length: " . filesize ( $file ) ); 
        header("Content-type: application/octet-stream"); 
        header("Content-disposition: attachment; filename=".basename($file));
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        ob_clean();
        flush();

        readfile($file);
    }
    else
    {
        echo 'The file aging_report_'.$_GET['data_to'].' does not exist';
    }

}
function _do_generate_print_report() {
    $select = '<div id="tbl_generate"><br/>
        <form id="generate_value" mothod="POST" action="" name="generate_value">';
            $select .= '<span>Are you sure print to</span>&nbsp; &nbsp;';
            $select .= '<select id="generate" name="generate" class="text">
                    <option value="0">GST Report</option>
                    <option value="1" selected>GST Report Summary</option>
                </select>&nbsp; &nbsp;
                <span></span>';
    $select .= '</form></div>';
    echo $select;
}
function vw_cash_flow_summary(){
    global $ocio_config,$AppUI,$CInvoiceManager,$CPaymentManager,$CSalesManager;
    include DP_BASE_DIR."/modules/sales/report.js.php";
    
    //lay date trong vong 1 thang ke tu ngay hien tai
    $date_current = date('d/M/Y');
    $date_current_format = date('Y-m-d');
    
    $date_first = date('d/M/Y', strtotime('-1 month'));
    $date_first_format = date('Y-m-d', strtotime('-1 month'));
    // List customer select option
    $customer_arr = $CSalesManager->get_list_companies();
    $option_customer = '';
    foreach ($customer_arr as $customer_row) {
        $option_customer.='<option value="'.$customer_row['company_id'].'" >'.$customer_row['company_name'].'</option>';
    }
    echo '<div style="width:94%">
                <div style="font-size:20px;text-align: center; width:100%; margin-top:15px">Cash Flow Summary Report</div>
                Customer: <select id="customer_selesct_cash" data-placeholder="Choose Customer" multiple="multiple">'.$option_customer.'</select>&nbsp;&nbsp;&nbsp;&nbsp;
                From: <input class="text" type="text" name="cash_from" id="cash_from" size="12" value="" /><input type="hidden" name="hdd_cash_from" id="hdd_cash_from" value="" />&nbsp;&nbsp;
                To: <input class="text" type="text" name="cash_to" id="cash_to" size="12" value="" /><input type="hidden" name="hdd_cash_to" id="hdd_cash_to" value="" />&nbsp;&nbsp;&nbsp;
                <button name="submit" class="ui-button ui-state-default ui-corner-all" onclick="get_search_cash();return false;">Search</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                '.date('d.M.Y').'
           </div>';
    echo '<div id="tbl_cash_report" style="margin-top:10px;width:94%;">';

    echo '</div>';
}
function view_cash_flow_summary_report(){  
    $CPOPayment = new CPOPayment();
    
    global $AppUI,$CInvoiceManager,$CReportManager,$CSalesManager,$CPOManager,$po_invoice,$CPaymentManager,$bankAccount,$expensesManage,$CPOPaymentDetail,$CBankCheque;
  	$po_status_arr = dPgetSysVal('openBankAccount');
    $date_search=false;
    if(count($po_status_arr) > 0){
        $date_search= date('Y-m-d',  strtotime($po_status_arr[1]));
   }
    
    $customer_value_arr = false;
    $from_date = false;
    $to_date = false;
//    if(isset($_POST['customer_id'])){
//            $customer_id_arr = $_POST['customer_id'];
//            if($customer_id_arr!="null"){
//                $customer_value_arr = "(".$_POST['customer_id'].")";
//            }
//
//        }
    if(isset($_REQUEST['customer_id'])){
        $customer_id_arr = $_REQUEST['customer_id'];
        if($customer_id_arr!="null"){
            $customer_value_arr =  '('.implode(',', $customer_id_arr).')';
//            $customer_value_arr ="";
//            foreach ($customer_id_arr as $value) {
//               // $customer_value_arr .= ','.$value;
//                $customer_value_arr='('.$value.')';
//            }
           // $customer_value_arr='('.$customer_value_arr.')';
        }
        
    }
    
    if(isset($_REQUEST['customer_id_arr']) && $_REQUEST['customer_id_arr'] !="null")
    {
        $customer_value_arr =  '('.$_REQUEST['customer_id_arr'].')';
    }
//    echo $customer_value_arr;
    //echo $customer_value_arr;
    if(isset($_REQUEST['from_date']))
        $from_date = $_REQUEST['from_date'];
    if(isset($_REQUEST['to_date']))
        $to_date = $_REQUEST['to_date'];
    if(isset($_REQUEST['customer_id_arr']))
        echo "";
    else
        echo $print_block = '<div style="width:100%;"><button class="ui-button ui-state-default ui-corner-all" onclick="print_cash_flow_summary(); return false;" target="_blank">Print</button></div>';
    echo '<div id="cash_report">';
////// Cash in Bank ///////
        echo '<div style="margin-top:20px;margin-bottom:10px;" ><b>Cash in Bank</b></div>';
        $tbl_bank = '<table width="100%" border="0" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
                <thead >
                    <tr id="title_head">
                        <td align="left" >Bank Account</td>
                        <td align="right">Balance to Date</td>
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
        
            $bank_account_arr = $bankAccount->getBankAccount($bank_account_id);
            $tbl_bank.='<tr  id="title_body">';
            $tbl_bank.='<td>'.$bank_account_arr[0]['bank_account_name'].'</td>';

           // $tbl_bank.='</tr>';
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
	
            $supplier_debit1 = $CPOPayment->calculateTotalPaymentSupplierByBankCustomer($customer_value_arr,$from_date,$bank_account_id,$date_search);
 
            $expenses_credit = 0;
			$sales_credit1 = $CPaymentManager->getSalesPaymentByCustomer1($bank_account_id,$customer_value_arr,$from_date,$date_search);
            $expenses_debit1 = $expensesManage->getAmountExpensesBankAccount($customer_value_arr, $from_date, $date_search, $bank_account_id);
            $info_credit = $CReportManager->caculatorCreditBeforDate($bank_account_id,$customer_value_arr,$from_date,$date_search);
            $openning = $bank_balance+$info_credit['credit']-$info_credit['debit'];
            $total_debit = $bank_debit + $sales_debit + $supplier_debit + $expenses_debit;
            $total_credit = $bank_credit + $sales_credit + $supplier_credit + $expenses_credit;
          //   $bank_balance1 = $bank_balance + $bank_credit1 - $bank_debit1 + $sales_credit1 - $supplier_debit1 - $expenses_debit1;
			$bank_balance1 = $openning + $sales_credit1 - $supplier_debit1 - $expenses_debit1;
            $totalAll = $bank_balance1 + $total_credit - $total_debit;
            $tbl_bank.='<td align="right">$'.number_format($bank_balance1,2).'</td>';
            $tbl_bank.='<td align="right">$'.number_format($total_debit,2).'</td>';
//            $tbl_bank.='<td align="right">$'.number_format($total_credit,2).'</td>';
            
            $tbl_bank.='<td align="right">$'.  number_format($totalAll,2).'</td>';
            $tbl_bank.='</tr></tbody>';
            $TotaCredit+=$total_credit;
            $TotalDebit+=$total_debit;
            $TotalBalance+=$bank_balance1;
            $TotalAll+=$totalAll;
            }
        }
            $tbl_bank.='<tfoot>'
                    . '<tr id="title_body" style="font-weight: bold; font-size: 14px; " height="35">'
                    . '<td>Total:</td>'
                    . '<td align="right" style="border-top:1px solid #000">$'.number_format($TotalBalance,2).'</td>'
                    . '<td align="right" style="border-top:1px solid #000">$'.number_format($TotalDebit,2).'</td>'
//                    . '<td align="right" style="border-top:1px solid #000">$'.number_format($TotaCredit,2).'</td>'
                    
                    . '<td align="right" style="border-top:1px solid #000">$'.number_format($TotalAll,2).'</td>'
                    . '</tr>'
                    . '</tfoot></table>';
 ///////    Projected Receivable  //////////
        $tbl_bank.= '<div style="margin-top:20px;margin-bottom:10px;" ><b>Projected Receivable</b></div>';
            $tbl_bank.= '<table width="100%" border="0" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
                <thead >
                    <tr id="title_head">
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
        $tbl_bank.='<tr>
                            <td colspan="12" ><hr style="background:#E9E9E9;"></td>
                        </tr>';
    $tbl_bank.= '</tbody>';
//    if($aging_total_banlance>0){
        $tbl_bank.= '<tr style="font-weight:bold;">
                <td style="font-size:15px;">Total</td>
                <td align="right">$'.number_format($aging_total_30,2).'</td>
                <td align="right">$'.number_format($aging_total_60,2).'</td>
                <td align="right">$'.number_format($aging_total_90,2).'</td>
                <td align="right">$'.number_format($aging_total_day,2).'</td>
                         
                <td align="right">$'.number_format($aging_total_banlance,2).'</td>
            </tr>
            <tr><td colspan="13">&nbsp</td></tr>';               
//    } else $tbl_bank.='<tr><td colspan="13">No projected receivable report found.</td></tr>';
///// Projected Payable //////
    $tbl_bank.= '</table>';
    $tbl_bank.= '<div style="margin-top:20px;margin-bottom:10px;" ><b>Projected Payable</b></div>';
            $tbl_bank.= '<table width="100%" border="0" callpadding="4" cellspacing="0" id="aging_report" class="tbl">
                <thead >
                    <tr id="title_head">
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
    $date = date('Y-m-d');
    if(isset($_REQUEST['to_date']) && $_REQUEST['to_date']!="")
       $date = $to_date;
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
//            $tbl_bank.='
//                            <tr valign="top">
//                                <td style="color:#08245b">
//                                    '.$payable_row["po_invoice_no"].'
//                                </td>
//                                <td id="invoice_date_'.$po_id.'" align="center">'.date("d/m/Y",strtotime($payable_row['po_date'])).'</td>
//                                <td id="$day30_'.$po_id.'" align="right">$'.number_format($day30,2).'</td>
//                                <td ></td>
//                                <td id="$day60_'.$po_id.'" align="right">$'.number_format($day60,2).'</td>
//                                <td ></td>
//                                <td id="$day90_'.$po_id.'" align="right">$'.number_format($day90,2).'</td>
//                                <td ></td>
//                                <td id="$day_'.$po_id.'" align="right">$'.number_format($day,2).'</td>
//                                <td ></td>
//                                <td align="right">$'.number_format($dayBalance,2).'</td>
//                            </tr>';
                $payable_total_30 += $totalDay30;
                $payable_total_60 += $totalDay60;
                $payable_total_90 += $totalDay90;
                $payable_total_day += $totalDay;
                $payable_total_banlance += $totalBalance;
        }
    }
    
    $tbl_bank.= '<tr>
                    <td colspan="12" ><hr style="background:#E9E9E9;"></td>
                </tr>'
            . '</tbody>';
//    if($payable_total_banlance>0){
        $tbl_bank.='<tr style="font-weight:bold;">
                <td  style="font-size:15px;">Total</td>
                <td align="right" >$'.number_format($payable_total_30,2).'</td>
                
                <td align="right" >$'.number_format($payable_total_60,2).'</td>
                
                <td align="right" >$'.number_format($payable_total_90,2).'</td>
                
                <td align="right" >$'.number_format($payable_total_day,2).'</td>
                
                <td align="right" >$'.number_format($payable_total_banlance,2).'</td>
            </tr>
            <tr><td colspan="13">&nbsp</td></tr>';
        
       
//    }
//    else $tbl_bank.='<tr><td colspan="13">No aging report found.</td></tr>';
    $tbl_bank.= '</table>';
//// Net Cash Flow /////
    $tbl_bank.= '<table table width="100%" border="0" callpadding="4" cellspacing="0" id="aging_report" class="tbl">'
            . '<tr id="title_head">'
            . '<td>Net Cash Flow</td>'
            . '<td align="right">$'.  number_format($TotalAll+$aging_total_banlance-$payable_total_banlance,2).'</td>'
            . '</tr>'
            . '</table>';
    echo $tbl_bank;
    echo '</div>';
}
function _do_print_cash_flow_report_pdf(){
    global $AppUI,$CReportManager;
    $CReportManager->create_cash_flow_report_pdf();
}

?>
<script type="text/javascript">

    function print_profit_report(){
        var sta_date_from = $('#hdd_sta_from').val();
        var sta_date_to = $('#hdd_sta_to').val();
        window.open('?m=sales&a=vw_report&c=_do_print_profit_pdf&suppressHeaders=true&from_date='+sta_date_from+'&to_date='+sta_date_to,'_blank','');
    }
    function show_gst_report()
    {
        $('#gst_report').show();
         $('#gst_report_summmary').hide();
    }
    function show_summary_report()
    {
        $('#gst_report').hide();
        $('#gst_report_summmary').show();
    }
    
    function change_customer()
    {
        $('#check_live_report').prop('checked', true);

    }
</script>
