<?php

if(!defined('DP_BASE_DIR')){
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
require_once (DP_BASE_DIR."/modules/sales/CCreditNoteManager.php");
require_once DP_BASE_DIR."/modules/sales/CPaymentManager.php";
require_once (DP_BASE_DIR."/modules/banks/CBankAccount.php");

$CSalesManager = new CSalesManager();
$CPayment = new CPaymentManager();
$CInvoiceManager= new CInvoiceManager();
$perms =& $AppUI->acl();
$canAdd = $perms->checkModule($m, 'add');
$canDelete = $perms->checkModule($m, 'delete');
$canEdit = $perms->checkModule($m, 'edit');
$canRead = $perms->checkModule($m, 'view');
$CCreditManager = new CCreditNoteManager();
$CBankAccount = new CBankAccount();

$addView_payment = $perms->checkModule( 'sales_payment', 'add');
$AccessView_payment = $perms->checkModule('sales_payment', 'access');
$deleteView_payment = $perms->checkModule('sales_payment', 'delete');
$editView_payment = $perms->checkModule('sales_payment', 'edit');

function __default(){
    global $AppUI, $CSalesManager;
    include DP_BASE_DIR."/modules/sales/payment.js.php";
    
    /* Add Payment */
    $add_id = '<a class="icon-add icon-all" id="add-new" onclick="add_new_payment_new();" href="#">Add Payment</a>';
    echo $AppUI ->createBlock('block_add', $add_id , 'style="text-align: left; height: 40px;"');
    echo '<div id="new_payment_new" style="margin-bottom: 10px; margin-left: 40px; width: 75%; overflow: auto;">';
    echo '</div>';
    
    /* Search Payment theo customer */
    $rows = $CCompany->getCompanyJoinDepartment();
    $client_option = '<option value="">-- Choose Customer --</option>';
    foreach ($rows as $row) {
        $client_option .= '<option value="'. $row['company_id'] .'">'. $row['company_name'] .'</option>';
    }
    $customer = 'Customers: <select id="payment_customer" class="text" onchange="load_invoice_by_customer(this.value); return false;">'. $client_option .'</select>';
    $invoice = 'Invoice: <select id="payment_invoice" class="text"id="change_invoice_by_customer"><</select>';
    $search_btn = '<button id="load_payment" class="ui-button ui-state-default ui-corner-all" onclick="load_payment(); return false;">Search</button>';
    
    //echo $AppUI ->createBlock('div_search', $customer .' '. $invoice .' '. $search_btn, 'style="text-align: left;overflow: hidden;"');
    echo '<div id="div_search">$search_btn</div>';
    /* List Payment */
    echo '<div id="detail_payment" style="margin-bottom: 10px; width: 100%; overflow: auto;">';
    echo '</div>';
}

function vw_add_payment_new($customer_id = false){
    global $baseUrl,$AppUI, $CSalesManager,$CPayment,$CCreditManager, $CBankAccount;
    include DP_BASE_DIR."/modules/sales/addPayment.js.php";
    $customer = $CSalesManager->get_list_companies(FALSE,1);
    
    $customer_id = $_POST['customer_id'];
    $total_credit_amount="";
    if($_POST['total_amount']!="undefined")
        $total_credit_amount = $_POST['total_amount'];
    $customer_option = '<option value="">-- Choose Customer --</option>';
    $count=0;
    foreach ($customer as $customer_row){
       //$customer_id = $customer_row['company_id'];
       $invoice_customer = $CPayment->get_invoice_by_customer($customer_row['company_id']);
       if(count($invoice_customer) > 0){
           $customer_option .='<option value="'.$customer_row['company_id'].'"'.((isset($customer_id) && $customer_row['company_id']==$customer_id) ? 'selected' : '').'>'.$customer_row['company_name'].'</option> ';
       }
       
    }//$invoice_option = get_invoiceBycustomer_select();
    
    $bank_account_db = $CBankAccount->getBankAccount();
    $bank_account_db_arr = array();
    foreach ($bank_account_db as $bank_account_db_item) {
        $bank_account_db_arr[$bank_account_db_item['bank_account_id']] = $bank_account_db_item['bank_account_name'];
    }
    
    $bank_account_arr = array(''=>'--Select--');
    $bank_account_arr += $bank_account_db_arr;;
    $bank_account = arraySelect($bank_account_arr, 'bank_account_id', 'id="bank_account_id" class="text" size="1" style="width:112px;', '', true);
    
    $option_credit ='<option value="">--Select Credit Note--</option>';
    $receipt_no=$CSalesManager->create_receipt_no();
   $form = '<fieldset class="ui-widget-content ui-corner-all" style="float: left; width: 90%;" >
            <legend><h1>Payment</h1></legend>';
    $form .= '<form id="new_payment" name="new_payment" method="POST">';
    $PaymentMethods = dPgetSysVal('PaymentMethods');
    $option = '';
    foreach ($PaymentMethods as $key => $value) {
        $option .= '<option value="'. $key .'">'. $value .'</option>';
    }
        $form .= '<dl>
                    <dt>
                        Receipt No: <input type="text" class="text" size="12" name="payment_receipt_no" id="payment_receipt_no" value="'.$receipt_no.'" />&nbsp;&nbsp;&nbsp;&nbsp;
                        Customer: <select  name="payment_customer_select" id="payment_customer_select" onchange="load_custoner__(this.value); return false;">'.$customer_option.'</select>&nbsp;&nbsp;&nbsp;
<!--                    Credit Note: <select id="pay_creditNote_id" class="text" name="pay_creditNote_id" onchange="load_total_amount_credit(this.value); return false;" >'.$option_credit.'</select>
                        <span id="credit_amount"><input type="hidden" id="hdd_credit_amount" value="0" /></span>
-->
                    </dt><br>
                 </dl>';
//        $form .= '<dl><dt>Customer:</dt>
//                      <dd>
//                        <select class="text" name="payment_customer_select" id="payment_customer_select" onchange="load_custoner__(this.value); return false;">'.$customer_option.'</select>&nbsp;&nbsp;&nbsp;
//<!--                        Credit Note: <select id="pay_creditNote_id" class="text" name="pay_creditNote_id" onchange="load_total_amount_credit(this.value); return false;" >'.$option_credit.'</select>
//                        <span id="credit_amount"><input type="hidden" id="hdd_credit_amount" value="0" /></span>
//-->
//                      </dd>  
//                </dl>';
        $form .= '<br><dl><dt>Payment Detail</dt></dl>';
        $form .= '<dl>
                    <dd>
                        Payment Amount: <input type="text" class="text" name="payment_amount" size="20" id="payment_amount" onkeyup="load_payment_amount_hd();return true;" value="" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="hidden" name="hdd_invoice_payment_amount" id="hdd_invoice_payment_amount" value="" />
                        Method: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select class="text" name="payment_method" id="payment_method">'.$option.'</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Date: <input type="text" class="text" size="8" readonly="true" name="payment_date" id="payment_date" value="'.date('d/M/Y').'" />
                                <input type="hidden" name="payment_date_hidden" id="payment_date_hidden" value="'.date('Y-m-d').'">
                    </dd><br>
                    <dd>
                        Cheques Nos:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" class="text" size="20" name="payment_cheque_nos" id="payment_cheque_nos" value="" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Bank Account: '.$bank_account.'
                    </dd><br>
                    <dd>
                        Note: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <textarea name="payment_notes" id="payment_notes" style="width: 350px;vertical-align: middle;padding:3px;" rows="4">'.$payment_note.'</textarea>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Description: <textarea name="payment_description" id="payment_description" style="width: 350px;vertical-align: middle;padding:3px;" rows="4"></textarea>
                    </dd>
                </dl>';
        
//        $form .= '<dl><dt>Invoice:</dt></dt>
//                      <dd>
//                        <select  class="text" name="invoice_id" id="invoice_id"><option value="">-- Choose Invoice --</option></select>
//                        <input type="button" class="ui-button ui-state-default ui-corner-all" onclick="add_line_payment_invoice(); return false;" name="submit_add" value="Add invoice" />
//                     </dd>
//        </dl>';
        
        $form .= '<dl><dt>Apply to Outstanding Invoice(s) </dt></dl>';
        
        $form .= '<dl id="">
                    <table class="tbl" cellspacing="1" cellpadding="2" style="clear: both; width: 100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Invoice No</th>
                                <th>Amount Due</th>
                                <th>Payment</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                    <tbody id="payment_invoice_detail">
                        <tr><td colspan="6">No Invoice</td></tr>
                   </tbody>
                            <input type="hidden" id="count_row_amount_payment" value="1">
                            <input type="hidden" id="total_credit_amount" value="0">
                        </table>
                     </dl>';
        
        
//        $form .= '<dl><dt>Method: </dt>';
//        $form .= '<dd><select class="text" name="payment_method" id="payment_method">'.$option.'</select></dd>';
//        $form .= '</dl>';
//        $form .= '<dl>';
//        $form .= '<dt>Date: </dt>';
//        $form .= '<dd><input type="text" class="text" readonly="true" name="payment_date" id="payment_date" value="'.date('Y/m/d').'" />
//                       <input type="hidden" name="payment_date_hidden" id="payment_date_hidden" value=""></dd>';
//        $form .= '</dl>';
//        $form .= '<dl>';
//        $form .= '<dt>Notes: </dt>';
//        $form .= '<dd><textarea name="payment_notes" id="payment_notes" style="width: 350px;" rows="5">'.$payment_note.'</textarea></dd>';
//        $form .= '</dl>';
        $form .= '<dl>';
        $form .= '<input type="button" class="ui-button ui-state-default ui-corner-all" onclick="save_payment_add(); return false;" name="submit_add" value="Save" />&nbsp;';
        $form .= '<input type="button" class="ui-button ui-state-default ui-corner-all" name="cancel" id="cancel" onclick="back_list();" value="cancel" />';
        $form .= '</dl>';
    $form .= '</form></fieldset>';
    echo $form;
    echo '<div  class="block_loading loading_active" ></div>';
    echo '<div class="block_loading_content loading_active"><img width="40" src="'.$baseUrl.'/images/ajax-loader.gif" /></div>';
    
}

function vw_payment_detail() {
    
    global $AppUI, $CSalesManager, $CPayment;
    
    
    $payment_customer = $_POST['payment_customer'];
    if($_POST['receipt_search'] >0 && $_POST['payment_customer'] == "")
    {
        
        $test = $CPayment->get_receit_by_customer(false,false,$_POST['receipt_search']);
        $payment_customer= $test[0]['customer_id'];
    }
    $status_invoice_id=false;
    if(isset($_POST['status_invoice_id']))
        $status_invoice_id = $_POST['status_invoice_id'];
    $field_set = ''; $field_set_1 = '';
    if ($payment_customer != '') {
        $customer_name = $CSalesManager->get_customer_field_by_id($payment_customer, 'company_name');
        $field_set_head .= '<h2>Customer: '. $customer_name .'</h2>';
        $field_set .= $field_set_head;
        $field_set .= '<div id="div_tbl_payment_'. $payment_customer .'">'. vw_tbl_payment($payment_customer) . '</div>';
        $field_set_1 .= $field_set_head;
        $field_set_1 .= '<div id="div_tbl_payment_schedule_'. $payment_customer .'">'. vw_tbl_payment_schedule($payment_customer) .'</div>';
        $field_set_1 .= '</fieldset>';
    } else {
        $q = new DBQuery();
        $q->addQuery('clients.company_id'); 
	$q->addTable('clients');
        
        $rows = $q->loadList();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                
                $invoice_arr = $CPayment->get_invoice_by_customer($row['company_id']);
//                echo '<pre>';
//                print_r($invoice_arr);
                if(count($invoice_arr)>0){
                    $customer_name = $CSalesManager->get_customer_field_by_id($row['company_id'], 'company_name');                
                    $field_set_head = '<fieldset class="ui-corner-all" style="float: left; width: 95%; margin-top:15px;" >';
                    $field_set_head .= '<legend><h2>'. $customer_name .'</h2></legend>';

                    $field_set .= $field_set_head;
                    $field_set .= '<div id="div_tbl_payment_'. $row['company_id'] .'">'. vw_tbl_payment($row['company_id']) .'<div>';
                    $field_set .= '</fieldset>';
                    $field_set .= '<p></p>';
                    $field_set_1 .= $field_set_head;
                    $field_set_1 .= '<div id="div_tbl_payment_schedule_'. $row['company_id'] .'">'. vw_tbl_payment_schedule($row['company_id']) .'<div>';
                    $field_set_1 .= '</fieldset>';
                }
            }
        }
    }
     
    echo '<fieldset class="ui-widget-content ui-corner-all" style="float: left; width: 90%;" >
            <legend><h1>Payment</h1></legend>
            '. $field_set .'
            </fieldset>';
    echo '<div id="detail_payment" style="margin-bottom: 10px; width: 100%; overflow: auto;">';
    echo '</div>';
//    echo '<fieldset class="ui-widget-content ui-corner-all" style="float: right; width: 47%;" >
//            <legend><h1>Payment schedule</h1></legend>
//            '. $field_set_1 .'
//            </fieldset>';
    
}

function vw_tbl_payment($customer_id) {
    
    global $AppUI, $CPayment, $canEdit, $canDelete, $CInvoiceManager, $CSalesManager, $editView_payment;
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();
    $payment_invoice = $_POST['payment_invoice'];
    $status_arr = dPgetSysVal('InvoiceStatus');
    
    if(isset($_POST['status_invoice_id']))
        $status_invoice_id=$_POST['status_invoice_id'];
    $tbody = '';
    if ($payment_invoice != '') {
        $invoice = $CInvoiceManager->get_db_invoice($payment_invoice);
        $invoice_id=$payment_invoice;
        $invoice_status = $invoice['invoice_status'];
        
        if(($status_invoice_id =="Paid") 
                        || ($status_invoice_id == "Outstanding" && $invoice_status != 2 )
                        || $status_invoice_id == "")
        {
         
        
            $invoice_revision_id_lastest = $CInvoiceManager->get_invoice_revision_lastest($invoice_id); // lay ra invoice_revision_id cuoi cung
                $invoice_no = $CInvoiceManager->get_invoice_field_by_id($invoice_id, 'invoice_no');
                
                //Lay ra tong tien da tra - anhnn
                $total_amount = 0;$total_item_show = 0;
                $payment_arr = $CPayment->get_invoice_revision_by_customer($customer_id, $invoice_id);
                
                if (count($payment_arr) > 0){
                    foreach ($payment_arr as $payment) {
                            $invoice_revision_id = $payment['invoice_revision_id'];
                                if($invoice_revision_id == "")
                                    $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest ($payment['invoice_id']);
                            $amount = $payment['payment_amount'];
                            $total_amount += round($amount, 2);     
                            $invoice_revision_tax = $payment['invoice_revision_tax'];//Lay ra tax_id
                            $invoice_revision_tax_edit = $payment['invoice_revision_tax_edit'];
                            $discount = $payment['invoice_revision_discount'];
                    }
                 $total_item_show = $CPayment->get_invoice_item_total($invoice_id, $invoice_revision_id, $customer_id).'<br>';//Tong amount (chua tinh thue)
                 $total_item_show_last_discount = $total_item_show - $discount;
                }
                //End- Lay ra tong tien da tra       
                //Lay ra so tien chua tra - anhnn
    
                // Lay ra tax_rate    
                if (count($tax_arr) > 0) {
                    foreach ($tax_arr as $tax) {
                        if ($invoice_revision_tax) { // neu tinh trang la update
                            if ($tax['tax_id'] == $invoice_revision_tax) {
                                $tax_value = $tax['tax_rate'];
                           }
                        } else { // neu tinh trang la add lay ra ta default
                            if ($tax['tax_default'] == 1) {
                                $tax_value = $tax['tax_rate'];
                            }
                        }
                   }
                }

                $caculating_tax = (floatval($total_item_show_last_discount) * floatval($tax_value) / 100); //So tien thue
                $caculating_tax = $CSalesManager->round_up($caculating_tax);
                if($invoice_revision_tax_edit!=0)
                    $caculating_tax = $invoice_revision_tax_edit;
                $balance = ($total_item_show_last_discount - $total_amount + $caculating_tax).'<br/>'; // so tien chua tra
    
                //END - Lay ra so tien chua tra -  anhnn 
                
                $tbody .= '<tbody>
                        <tr>
                                <td colspan="9"> Invoice No: <b><a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_revision_id_lastest.'"><font style="color: red">'. $invoice_no .'</font></a></b> &nbsp;&nbsp;'. $a_add .'</td>
                        </tr>
                        <tbody>';
                $balance=  round($balance,5);
                if(($status_invoice_id == "Outstanding" && $balance >0) || ($status_invoice_id == "Paid" && $balance ==0) || ($status_invoice_id==""))
                {
                $tbody .= '<tbody id="show_tr_payment_'. $invoice_revision_id_lastest .'">';
                $tbody .= vw_tr_payment($customer_id, $invoice_revision_id_lastest, $invoice_id);
                $tbody .= '</tbody>';
                $tbody .= '<tbody><tr><td colspan="9" style="background-color:#F8F8F8">
                        <table cellspacing="0" cellpadding="1" style="clear: both; width: 48%" id="payment_invoice_table_tr" class="tbl">
                            <tr>
                                <td width="177" style="background-color:#F8F8F8" align="right">Paid: $'. number_format($total_amount,2) .'</td>
                                <td align="right" style="background-color:#F8F8F8">Balance: $'. number_format(round($balance, 2),2) .'</td>
                            </tr>
                        </table>
                </td></tr></tbody>';
                
                $total +=$total_amount;
                $total_balance = $total_balance + round($balance,2);
                }
        }
    } else {
        $i=0;
        $total= 0;$total_balance=0;
        $invoice_arr = $CPayment->get_invoice_by_customer($customer_id);
        if (count($invoice_arr) > 0) {
            foreach ($invoice_arr as $invoice) {
                $invoice_id = $invoice['invoice_id'];
                $invoice_status = $invoice['invoice_status'];
                
                if($status_invoice_id =="Paid" && $status_arr[$invoice_status]=="Paid" 
                        || $status_invoice_id == "Outstanding" && $invoice_status !=2 
                        || $status_invoice_id == "")
                {
                $i++;
                
                $invoice_revision_id_lastest = $CInvoiceManager->get_invoice_revision_lastest($invoice_id); // lay ra invoice_revision_id cuoi cung
                $invoice_no = $CInvoiceManager->get_invoice_field_by_id($invoice_id, 'invoice_no');
                
                //Lay ra tong tien da tra - anhnn
                $total_amount = 0;$total_item_show = 0;
                $payment_arr = $CPayment->get_invoice_revision_by_customer($customer_id, $invoice_id);

                if (count($payment_arr) > 0){
                    foreach ($payment_arr as $payment) {
                            $invoice_revision_id = $payment['invoice_revision_id'];
                                if($invoice_revision_id == "")
                                    $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest ($payment['invoice_id']);
                            $amount = $payment['payment_amount'];
                            $total_amount += round($amount, 2);     
                            $invoice_revision_tax = $payment['invoice_revision_tax'];//Lay ra tax_id
                            $invoice_revision_tax_edit = $payment['invoice_revision_tax_edit'];
                            $discount = $payment['invoice_revision_discount'];
                    }
                 $total_item_show = $CPayment->get_invoice_item_total($invoice_id, $invoice_revision_id, $customer_id).'<br>';//Tong amount (chua tinh thue)
                 $total_item_show_last_discount = $total_item_show - $discount;
                }
                //End- Lay ra tong tien da tra       
                //Lay ra so tien chua tra - anhnn
    
                // Lay ra tax_rate    
                if (count($tax_arr) > 0) {
                    foreach ($tax_arr as $tax) {
                        if ($invoice_revision_tax) { // neu tinh trang la update
                            if ($tax['tax_id'] == $invoice_revision_tax) {
                                $tax_value = $tax['tax_rate'];
                           }
                        } else { // neu tinh trang la add lay ra ta default
                            if ($tax['tax_default'] == 1) {
                                $tax_value = $tax['tax_rate'];
                            }
                        }
                   }
                }

                $caculating_tax = (floatval($total_item_show_last_discount) * floatval($tax_value) / 100); //So tien thue
                $caculating_tax = $CSalesManager->round_up($caculating_tax);
                if($invoice_revision_tax_edit!=0)
                    $caculating_tax = $invoice_revision_tax_edit;
                $balance = ($total_item_show_last_discount - $total_amount + $caculating_tax).'<br/>'; // so tien chua tra
                $balance=  round($balance,5);
                if($status_invoice_id == "Outstanding" && $balance >0 || $status_invoice_id == "Paid" && $balance ==0 || $status_invoice_id=="")
                {
                    
                $tbody .= '<tbody>
                        <tr>
                                <td colspan="9"> Invoice No: <b><a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_revision_id_lastest.'"><font style="color: red">'. $invoice_no .'</font></a></b> &nbsp;&nbsp;'. $a_add .'</td>
                        </tr>
                        <tbody>';
            
                $tbody .= '<tbody id="show_tr_payment_'. $invoice_revision_id_lastest .'">';
                $tbody .= vw_tr_payment($customer_id, $invoice_revision_id_lastest, $invoice_id);
                $tbody .= '</tbody>';
                $tbody .= '<tbody><tr><td colspan="9" style="background-color:#F8F8F8">
                        <table cellspacing="0" cellpadding="1" style="clear: both; width: 48%" id="payment_invoice_table_tr" class="tbl">
                            <tr>
                                <td width="177" style="background-color:#F8F8F8" align="right">Paid: $'. number_format($total_amount,2) .'</td>
                                <td align="right" style="background-color:#F8F8F8">Balance: $'. number_format(round($balance, 2),2) .'</td>
                            </tr>
                        </table>
                </td></tr></tbody>';
                
                
                //END - Lay ra so tien chua tra -  anhnn   
                $total +=$total_amount;
                $total_balance = $total_balance + round($balance,2);
                }
            }
        }
        } else {
            $invoice_entry = true;
        }
    }
  
    if (!$invoice_entry) {
        
        $tbl = '<table cellspacing="1" cellpadding="2" style="clear: both; width: 100%" id="payment_invoice_table" class="tbl">
                <thead>
                    <tr>
                        <th width="25"><input type="checkbox" onclick="check_all(\'payment_select_all_'. $customer_id .'\', \'payment_check_list_'. $customer_id .'\');" id="payment_select_all_'.$customer_id.'" name="payment_select_all_'.$customer_id.'"></th>
                        <th align="right" width="150">Amount</th>
                        <th width="130">Method</th>
                        <th width="130">Bank Account</th>
                        <th width="130">Date</th>
                        <th>Notes</th>
                        <th>Cheque Nos</th>
                        <th>Receipt Nos</th>
                        <th>Description</th>
                    </tr>
                </thead>';
                
        $tbl .= $tbody;
        $tbl .= '
                <tfoot>
                </tfoot>
                <table cellspacing="0" cellpadding="1" style="clear: both; width: 48%" id="payment_invoice_table_tr" class="tbl">
                <td align="right" width="177"><b>Total Paid: $'. number_format($total,2) .'</b></td>
                <td align="right"><b>Total Balance: $'. number_format(round($total_balance, 2),2) .'</b></td>
                </table>
            </table><br/>';
        $email = '<a class="icon-email icon-all" onclick="popupSendEmailReceipt('.$customer_id.'); return false;" href="#">Email</a>';
        $print_pdf = '<a class="icon-email icon-all" onclick="create_payment_receipt_pdf('. $customer_id .'); return false;" href="#">Print to PDF</a>';
        if ($canDelete) {
            $delete_id = '<a class="icon-delete icon-all" onclick="delete_payment('. $customer_id.'); return false;" href="#">Delete</a>';
        }
        $tbl .= $AppUI ->createBlock('del_block', $delete_id . $print_pdf . $email, 'style="text-align: left;"');
        
        if ($canEdit) {
$js = <<<EOD
    function edit_payment(value, settings){
        var payment_customer = $('#payment_customer').val();
        var payment_invoice = $('#payment_invoice').val();
        var invoice_id=this.id;
       
        $.post('?m=sales&a=vw_payment&c=_do_update_payment_detail_field&suppressHeaders=true',{id:this.id,value:value,field_name:'payment_amount'},
                function(data){
                        $('#detail_payment').html('Loading...');
                        $('#detail_payment').load('?m=sales&a=vw_payment_new&c=vw_payment_detail&suppressHeaders=true', { payment_invoice: payment_invoice, payment_customer: payment_customer });
                        $.post('?m=sales&a=vw_invoice&c=_do_update_status_invoice1&suppressHeaders=true&invoice_id='+invoice_id, {update_payment:"update"});
                    },"json");
    }
    $(document).ready(function() {
        var url = '?m=sales&a=vw_payment&c=_do_update_payment_field&suppressHeaders=true';
        var url1 = '?m=sales&a=vw_payment&c=_do_update_payment_detail_field&suppressHeaders=true';
//        loadJQueryCalendar('edit_payment_date', '', 'dd/mm/yy', '');
        
        $('.edit_payment_amount').editable(edit_payment, {
            indicator    : "Loading...",
            submitdata : { field_name: 'payment_amount' },
            width: 220,
            submit: "Save",
            cancel: 'Cancel',
            height: 20,
            cssclass: 'editable'
        });
        
        $('.edit_payment_notes').editable(url, {
            indicator    : "Loading...",
            submitdata : { field_name: 'payment_notes' },
            type: 'textarea',
            submit: "Save",
            cancel: 'Cancel',
        });
        
        
        $('.edit_payment_method').editable(url, {
            indicator    : "Loading...",
            submitdata : { field_name: 'payment_method', action: 'for_method' },
            width: 220,
            loadurl : '?m=sales&a=vw_payment&c=_load_payment_method&suppressHeaders=true',
            type: 'select',
            submit: "Save",
            cancel: 'Cancel',
            height: 20
        });
        
        $('.edit_bank_account').editable(url, {
            indicator    : "Loading...",
            submitdata : { field_name: 'bank_account_id', action: 'for_bank' },
            width: 220,
            loadurl : '?m=sales&a=vw_payment&c=_load_bank_account&suppressHeaders=true',
            type: 'select',
            submit: "Save",
            cancel: 'Cancel',
            height: 20
        });
        
        $('.edit_payment_cheque_nos').editable(url, {
            indicator    : "Loading...",
            submitdata : { field_name: 'payment_cheque_nos'},
            type: 'textarea',
            submit: "Save",
            cancel: 'Cancel',
        });
        
        $('.edit_payment_description').editable(url, {
            indicator    : "Loading...",
            submitdata : { field_name: 'payment_description'},
            type: 'textarea',
            submit: "Save",
            cancel: 'Cancel',
        });
        
    });

EOD;
    }
if($editView_payment)
    $AppUI->addJSScript($js);
        return $tbl;
    } else {
        return '<i>No invoice found.</i>'; 
    }
}
function vw_tbl_payment_schedule($customer_id) {
    
    global $AppUI, $CPayment, $canEdit, $canDelete, $deleteView_payment;
    
    $payment_invoice = $_POST['payment_invoice'];
    $tbody = '';
    if ($payment_invoice != '') {
        $tbody .= vw_tbody_payment_schedule($customer_id, $payment_invoice);
    } else {
        $invoice_arr = $CPayment->get_invoice_by_customer($customer_id);
        if (count($invoice_arr) > 0) {
            foreach ($invoice_arr as $invoice) {
                $tbody .= vw_tbody_payment_schedule($customer_id, $invoice['invoice_id']);
            }
        } else {
            $invoice_entry = true;
        }
    }
    
    if (!$invoice_entry) {
        
        $tbl = '<table cellspacing="1" cellpadding="2" style="clear: both; width: 100%" id="payment_schedule_invoice_table" class="tbl">
                <thead>
                    <tr>
                        <th width="25"><input type="checkbox" onclick="check_all(\'payment_schedule_select_all_'. $customer_id .'\', \'payment_schedule_check_list_'. $customer_id .'\');" id="payment_schedule_select_all_'.$customer_id.'" name="payment_schedule_select_all_'.$customer_id.'"></th>
                        <th align="right" width="100">Paid</th>
                        <th width="70">Date</th>
                        <th>Notes</th>
                    </tr>
                </thead>';
                
        $tbl .= $tbody;
        
        $tbl .= '
                <tfoot>
                </tfoot>
            </table><br/>';
        
        //$email = '<a class="icon-email icon-all" onclick="send_mail_payment_receipt('. $customer_id .'); return false;" href="#">Email</a>';
        //$print_pdf = '<a class="icon-email icon-all" onclick="create_payment_receipt_pdf('. $customer_id .'); return false;" href="#">Print to PDF</a>';
        if ($canDelete && $deleteView_payment) {
            $delete_id = '<a class="icon-delete icon-all" onclick="delete_payment_schedule('. $customer_id .'); return false;" href="#">Delete</a>';
        }
        
        $tbl .= $AppUI ->createBlock('del_block', $delete_id . $print_pdf . $email, 'style="text-align: left;"');
        if ($canEdit) {
    $js = <<<EOD
    $(document).ready(function() {
        var url = '?m=sales&a=vw_payment&c=_do_update_payment_schedule_field&suppressHeaders=true';
        
        $('.edit_payment_schedule_paid').editable(url, {
            indicator    : "Loading...",
            submitdata : { field_name: 'payment_schedule_paid' },
            width: 220,
            submit: "Save",
            cancel: 'Cancel',
            height: 20
        });
        
        $('.edit_payment_schedule_notes').editable(url, {
            indicator    : "Loading...",
            submitdata : { field_name: 'payment_schedule_notes' },
            type: 'textarea',
            submit: "Save",
            cancel: 'Cancel',
        });
        
        /*$('.payment_schedule_paid_date').click(function(e) {
            id = $(this).attr('id');
            alert(id)
	});*/
        
    });
EOD;
        }
        $AppUI->addJSScript($js);
        
       return $tbl;
    } else {
       return '<i>No invoice found.</i>'; 
    }
}
function vw_tbody_payment($customer_id, $invoice_id) {
    global $canAdd,$CPayment, $CInvoiceManager,$CSalesManager;

    $invoice_revision_id_lastest = CInvoiceManager::get_invoice_revision_lastest($invoice_id); // lay ra invoice_revision_id cuoi cung
        
        $invoice_no = CInvoiceManager::get_invoice_field_by_id($invoice_id, 'invoice_no');
        
//Lay ra tong tien da tra - anhnn
    $total_amount = 0;
    $payment_arr = $CPayment->get_invoice_revision_by_customer($customer_id, $invoice_id);
    
    if (count($payment_arr) > 0){
        foreach ($payment_arr as $payment) {
                $invoice_revision_id = $payment['invoice_revision_id'];
                    if($invoice_revision_id == "")
                        $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest ($payment['invoice_id']);
                $amount = $payment['payment_amount'];
                $total_amount += round($amount, 2);     
                $invoice_revision_tax = $payment['invoice_revision_tax'];//Lay ra tax_id
                $invoice_revision_tax_edit = $payment['invoice_revision_tax_edit'];
                $discount = $payment['invoice_revision_discount'];
        }
     $total_item_show = $CPayment->get_invoice_item_total($invoice_id, $invoice_revision_id, $customer_id).'<br>';//Tong amount (chua tinh thue)
     $total_item_show_last_discount = $total_item_show - $discount;
    }
//End- Lay ra tong tien da tra       
//Lay ra so tien chua tra - anhnn
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();
    
    // Lay ra tax_rate    
   if (count($tax_arr) > 0) {
        foreach ($tax_arr as $tax) {
            if ($invoice_revision_tax) { // neu tinh trang la update
                if ($tax['tax_id'] == $invoice_revision_tax) {
                    $tax_value = $tax['tax_rate'];
               }
            } else { // neu tinh trang la add lay ra ta default
                if ($tax['tax_default'] == 1) {
                    $tax_value = $tax['tax_rate'];
                }
            }
       }
   }
//    if ($invoice_revision_tax) {
        $caculating_tax = (floatval($total_item_show_last_discount) * floatval($tax_value) / 100); //So tien thue
        $caculating_tax = $CSalesManager->round_up($caculating_tax);
        if($invoice_revision_tax_edit!=0)
            $caculating_tax = $invoice_revision_tax_edit;
        $balance = ($total_item_show_last_discount - $total_amount + $caculating_tax).'<br/>'; // so tien chua tra
    
    
        if ($canAdd) {
//            $a_add = '<a href="#" class="icon-add icon-all" onclick="add_row_payment('. $customer_id .', '. $invoice_revision_id_lastest .', '.$invoice_id.'); return false;">Add</a>';
        }
        
            $tbody = '<tbody>
                        <tr>
                                <td colspan="9"> Invoice No: <b><a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_revision_id_lastest.'"><font style="color: red">'. $invoice_no .'</font></a></b> &nbsp;&nbsp;'. $a_add .'</td>
                        </tr>
                        <tbody>';
            
            $tbody .= '<tbody id="show_tr_payment_'. $invoice_revision_id_lastest .'">';
            $tbody .= vw_tr_payment($customer_id, $invoice_revision_id_lastest, $invoice_id);
            $tbody .= '</tbody>';
            $tbody .= '<tbody><tr><td colspan="9" style="background-color:#F8F8F8">
                        <table cellspacing="0" cellpadding="1" style="clear: both; width: 48%" id="payment_invoice_table_tr" class="tbl">
                            <tr>
                                <td width="177" style="background-color:#F8F8F8" align="right">Paid: $'. number_format($total_amount,2) .'</td>
                                <td align="right" style="background-color:#F8F8F8">Balance: $'. number_format(round($balance, 2),2) .'</td>
                            </tr>
                        </table>
                </td></tr></tbody>';
            
    return $tbody;
}

function vw_tbody_payment_schedule($customer_id, $invoice_id) {
    global $canAdd;

    $invoice_revision_id_lastest = CInvoiceManager::get_invoice_revision_lastest($invoice_id); // lay ra invoice_revision_id cuoi cung
        
        $invoice_no = CInvoiceManager::get_invoice_field_by_id($invoice_id, 'invoice_no');
        
        if ($canAdd) {
//            $a_add = '<a href="#" class="icon-add icon-all" onclick="add_row_payment_schedule('. $customer_id .', '. $invoice_revision_id_lastest .'); return false;">Add</a>';
        }
        
            $tbody = '<tbody>
                        <tr>
                                <td colspan="5"> Invoice No: <b><a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_revision_id_lastest.'"><font style="color: red">'. $invoice_no .'</font></a></b> &nbsp;&nbsp;'. $a_add .'</td>
                        </tr>
                        <tbody>';
            
            $tbody .= '<tbody id="show_tr_payment_schedule_'. $invoice_revision_id_lastest .'">';
            $tbody .= vw_tr_payment_schedule($customer_id, $invoice_revision_id_lastest);
            $tbody .= '</tbody>';
            
    return $tbody; 
    
}
function vw_tr_payment_schedule($customer_id, $invoice_revision_id_lastest) {
    
    global $AppUI, $CPayment;
    
        $payment_schedule_arr = $CPayment->list_db_payment_schedule($invoice_revision_id_lastest);
        
        $tr = '<tr id="tr_invoice_schedule_'.$invoice_revision_id_lastest.'"></tr>';
        if (count($payment_schedule_arr) > 0) {
            foreach ($payment_schedule_arr as $payment_schedule) {
                $tr .= '<tr>
                        <td align="right"><input type="checkbox" value="'. $payment_schedule['payment_schedule_id'] .'" name="payment_schedule_check_list_'. $customer_id .'" id="payment_schedule_check_list_'. $customer_id .'"></td>
                        <td class="edit_payment_schedule_paid" id="'. $payment_schedule['payment_schedule_id'] .'" align="right">'. $payment_schedule['payment_schedule_paid'] .'</td>
                        <td class="edit_payment_schedule_paid_date" id="'. $payment_schedule['payment_schedule_id'] .'">'. date(FORMAT_DATE_DEFAULT, strtotime($payment_schedule['payment_schedule_paid_date'])) .'</td>
                        <td class="edit_payment_schedule_notes" id="'. $payment_schedule['payment_schedule_id'] .'">'. $payment_schedule['payment_schedule_notes'] .'</td>
                    </tr>';
            }
        }
                
            $tbody = $tr;
        return $tbody;
}
function vw_tr_payment($customer_id, $invoice_revision_id_lastest) {
    
    global $AppUI, $CPayment, $CBankAccount;
    $q = new DBQuery();
    $q->addTable('sales_payment_detail');
    $q->addQuery('payment_id,payment_amount,payment_detail_id');
    if($invoice_revision_id_lastest > 0)
        $q->addWhere('invoice_revision_id = '.$invoice_revision_id_lastest);
    $paymentDetail_arr = $q->loadList();
    
    $PaymentMethods = dPgetSysVal('PaymentMethods');
    $PaymentBank = dPgetSysVal('BankAccount');
        
    $tr = '<tr id="tr_invoice_'.$invoice_revision_id_lastest.'"></tr>';
    if(count($paymentDetail_arr)>0){
                foreach ($paymentDetail_arr as $paymentDetail){
                    $payment_arr = $CPayment->get_db_payment($paymentDetail['payment_id']);
                    foreach ($payment_arr as $payment){
                        $payment_receipt_no = $payment['payment_receipt_no'];
                        $payment_id=$payment['payment_id'];
                        $payment_method=$payment['payment_method'];
                        $payment_bank=$payment['bank_account_id'];
                        $payment_date=$payment['payment_date'];
                        $payment_node=$payment['payment_notes'];
                        $payment_desc=$payment['payment_description'];
                        $payment_cheque=$payment['payment_cheque_nos'];
                    }
                   
                    if($payment_bank==0)
                    {
                        $bank_account_name = '';
                    }
                    else {
                        $bank_account_db = $CBankAccount->getBankAccount($payment_bank);
                        $bank_account_name = $bank_account_db[0]['bank_account_name'];
                    }
                    if(isset($_POST['receipt_search']) && $_POST['receipt_search'] > 0)
                    {
                        if($payment_id == $_POST['receipt_search']){
                        $tr .='<tr>
                            <td align="right"><input type="checkbox" value="'. $paymentDetail['payment_detail_id'] .'" name="payment_check_list_'. $customer_id .'" id="payment_check_list_'. $customer_id .'"></td>
                            <td class="edit_payment_amount" id="'. $paymentDetail['payment_detail_id'] .'" align="right">$'. number_format($paymentDetail['payment_amount'],2) .'</td>
                            <td class="edit_payment_method" id="'. $payment_id .'" align="center">'. $PaymentMethods[$payment_method] .'</td>
                            <td class="edit_bank_account" id="'. $payment_id .'" align="center">'.$bank_account_name.'</td>
                            <td class="edit_payment_date" id="'. $payment_id .'" align="center"><div id="date_payment_'.$payment_id.'" onclick="editDate('.$payment_id.',\'date_payment_'.$payment_id.'\',\''.date(FORMAT_DATE_DEFAULT, strtotime($payment_date)).'\',\''.date('Y-m-d', strtotime($payment_date)).'\')">'. date(FORMAT_DATE_DEFAULT, strtotime($payment_date)) .'</div></td>
                            <td class="edit_payment_notes" id="'. $payment_id .'">'.  $payment_node .'</td>
                            <td class="edit_payment_cheque_nos" id="'. $payment_id .'">'.  $payment_cheque .'</td>
                            <td class="edit_payment_receipt_nos" id="'. $payment_id .'">'.  $payment_receipt_no .'</td>
                            <td class="edit_payment_description" id="'. $payment_id .'">'.  $payment_desc .'</td>
                        </tr>';
                        }
                    }
                    else
                    {
                        $tr .='<tr>
                            <td align="right"><input type="checkbox" value="'. $paymentDetail['payment_detail_id'] .'" name="payment_check_list_'. $customer_id .'" id="payment_check_list_'. $customer_id .'"></td>
                            <td class="edit_payment_amount" id="'. $paymentDetail['payment_detail_id'] .'" align="right">$'. number_format($paymentDetail['payment_amount'],2) .'</td>
                            <td class="edit_payment_method" id="'. $payment_id .'" align="center">'. $PaymentMethods[$payment_method] .'</td>
                            <td class="edit_bank_account" id="'. $payment_id .'" align="center">'.$bank_account_name.'</td>
                            <td class="edit_payment_date" id="'. $payment_id .'" align="center"><div id="date_payment_'.$payment_id.'" onclick="editDate('.$payment_id.',\'date_payment_'.$payment_id.'\',\''.date(FORMAT_DATE_DEFAULT, strtotime($payment_date)).'\',\''.date('Y-m-d', strtotime($payment_date)).'\')">'. date(FORMAT_DATE_DEFAULT, strtotime($payment_date)) .'</div></td>
                            <td class="edit_payment_notes" id="'. $payment_id .'">'.  $payment_node .'</td>
                            <td class="edit_payment_cheque_nos" id="'. $payment_id .'">'.  $payment_cheque .'</td>
                            <td class="edit_payment_receipt_nos" id="'. $payment_id .'">'.  $payment_receipt_no .'</td>
                            <td class="edit_payment_description" id="'. $payment_id .'">'.  $payment_desc .'</td>
                        </tr>';
                    }
                }
            }
        
            $tbody = $tr;
       return $tbody;
//        }
    
//        if (count($payment_arr) > 0) {
//            foreach ($payment_arr as $payment) {
//                $tr .= '<tr>
//                        <td align="right"><input type="checkbox" value="'. $payment['payment_id'] .'" name="payment_check_list_'. $customer_id .'" id="payment_check_list_'. $customer_id .'"></td>
//                        <td class="edit_payment_amount" id="'. $payment['payment_id'] .'" align="right">'. $payment['payment_amount'] .'</td>
//                        <td class="edit_payment_method" id="'. $payment['payment_id'] .'">'. $PaymentMethods[$payment['payment_method']] .'</td>
//                        <td class="edit_payment_date" id="'. $payment['payment_id'] .'">'. date(FORMAT_DATE_DEFAULT, strtotime($payment['payment_date'])) .'</td>
//                        <td class="edit_payment_notes" id="'. $payment['payment_id'] .'">'. $payment['payment_notes'] .'</td>
//                          
//                    </tr>';
//            }
//            $tr .= '<input type="hidden" name="invoce_id_payment" id="invoce_id_payment" value="'.$invoice_id.'" />';
//        }
        
//        if(isset($_POST['receipt_search']) && $_POST['receipt_search'] > 0)
//        {
//             
//                
//                    $payment = $CPayment->get_db_payment($_POST['receipt_search']);
//                    
//                        $payment_receipt_no = $payment[0]['payment_receipt_no'];
//                        $payment_id=$payment[0]['payment_id'];
//                        $payment_method=$payment[0]['payment_method'];
//                        $payment_bank=$payment[0]['bank_account_id'];
//                        $payment_date=$payment[0]['payment_date'];
//                        $payment_node=$payment[0]['payment_notes'];
//                        $payment_desc=$payment[0]['payment_description'];
//                        $payment_cheque=$payment[0]['payment_cheque_nos'];
//                    
//                   
//                    if($payment_bank==0)
//                    {
//                        $bank_account_name = '';
//                    }
//                    else {
//                        $bank_account_db = $CBankAccount->getBankAccount($payment_bank);
//                        $bank_account_name = $bank_account_db[0]['bank_account_name'];
//                    }
//                    $tr .='<tr>
//                            <td align="right"><input type="checkbox" value="'. $paymentDetail['payment_detail_id'] .'" name="payment_check_list_'. $customer_id .'" id="payment_check_list_'. $customer_id .'"></td>
//                            <td class="edit_payment_amount" id="'. $paymentDetail['payment_detail_id'] .'" align="right">$'. number_format($paymentDetail['payment_amount'],2) .'</td>
//                            <td class="edit_payment_method" id="'. $payment_id .'" align="center">'. $PaymentMethods[$payment_method] .'</td>
//                            <td class="edit_bank_account" id="'. $payment_id .'" align="center">'.$bank_account_name.'</td>
//                            <td class="edit_payment_date" id="'. $payment_id .'" align="center"><div id="date_payment_'.$payment_id.'" onclick="editDate('.$payment_id.',\'date_payment_'.$payment_id.'\',\''.date(FORMAT_DATE_DEFAULT, strtotime($payment_date)).'\',\''.date('Y-m-d', strtotime($payment_date)).'\')">'. date(FORMAT_DATE_DEFAULT, strtotime($payment_date)) .'</div></td>
//                            <td class="edit_payment_notes" id="'. $payment_id .'">'.  $payment_node .'</td>
//                            <td class="edit_payment_cheque_nos" id="'. $payment_id .'">'.  $payment_cheque .'</td>
//                            <td class="edit_payment_receipt_nos" id="'. $payment_id .'">'.  $payment_receipt_no .'</td>
//                            <td class="edit_payment_description" id="'. $payment_id .'">'.  $payment_desc .'</td>
//                        </tr>';
//                
//            
//        
//            $tbody = $tr;
//        }
//        else
//        {
           
            
}
function _do_payment_new(){
    global $CPayment,$CSalesManager;
    
    if(isset($_POST['payment_receipt_no']) && $CPayment->is_check_payment_receipt($_POST['payment_receipt_no']))
    {
        echo '{"status": "Failure", "message": "This Receipt no has already existed."}';
        
    }
    else
    {
        $objectpayment['payment_date'] = $_POST['payment_date'];
        $objectpayment['payment_method'] = intval($_POST['payment_method']);
        $objectpayment['payment_notes'] = $_POST['payment_notes'];
        $objectpayment['credit_note_id'] = $_POST['credit_note_id'];
        $objectpayment['bank_account_id'] = $_POST['bank_account_id'];
        $objectpayment['payment_cheque_nos'] = $_POST['payment_cheque_nos'];
        $objectpayment['payment_description'] = $_POST['payment_description'];

        if(isset($_POST['payment_receipt_no']))
            $objectpayment['payment_receipt_no'] = $_POST['payment_receipt_no'];
        else 
            $objectpayment['payment_receipt_no'] = $CSalesManager->create_receipt_no();
        $payment_new=$CPayment->add_pymentNew($objectpayment);
        
        if (intval($payment_new) > 0) {
            echo '{"status": "Success","payment_id":'.$payment_new.'}';
        } else {
            echo '{"status": "Failure", "message": "Co loi trong qua trinh them"}';
        }
    }
    
}
function _do_add_paymentDetail_new(){
    global $CPayment;
//    $count_invoice = $_POST['count_payment_invoice'];
//    $count_invoice = $count_invoice-1;
    $payment_invoice_rev_id = $_POST['payment_invoice_rev_id'];
    $payment_amount = $_POST['payment_amount'];
    $payment_amount = $payment_amount;
    $payment_id=$_POST['payment_id'];
    $payment_detail_note = $_POST['payment_detail_note'];
    for($i=0;$i<count($payment_amount);$i++){
        $payment_obj['invoice_revision_id']=$payment_invoice_rev_id[$i];
        $payment_obj['payment_amount']= round($payment_amount[$i],3);
        $payment_obj['payment_id']=$payment_id;
        $payment_obj['payment_detail_note'] = $payment_detail_note[$i];
        $paymentDetail_id = $CPayment->add_pymentDetail($payment_obj);
    }
    if (intval($paymentDetail_id) > 0) {
        echo '{"status": "Success"}';
    } else {
        echo '{"status": "Failure", "message": "Co loi trong qua trinh them"}';
    }
}
function get_invoiceBycustomer_select(){
    global $AppUI, $CSalesManager, $CPayment, $CInvoiceManager;
    $invoice_line = '';
    $customer_id = $_POST['customer_id'];
    $invoice_customer = $CPayment->get_invoice_by_customer($customer_id,1);
    
    
    //print_r($invoice_customer);
    $check_count_inv = false;
    $amount_total = 0;
    foreach ($invoice_customer as $invoice_customer_row) {
        $invoice_id = $invoice_customer_row['invoice_id'];
        $tax = $invoice_customer_row['tax'];
        
        // get invoice revision lastest
        $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
        $invoice_revision_arr = $CInvoiceManager->get_db_invoice_revsion($invoice_revision_id);
        //print_r($invoice_revision_arr);
        // Tong cac item
        $total_item = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
        
        // Payment Amount Due
        $amount_due = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_item);
        $amount_total += round($amount_due,2);
        
        if($amount_due>0){
            $invoice_line.='<tr>
                    <td><input type="checkbox" name="list_checkbox" id="list_checkbox_'.$invoice_id.'" onclick="load_payment_total('.$invoice_id.')" /></td>
                    <td id="invoice_no_'.$invoice_id.'">'.$invoice_customer_row['invoice_no'].'
                        <input type="hidden" name="hdd_invoice_rev_'.$invoice_id.'" id="hdd_invoice_rev_'.$invoice_id.'" value="'.$invoice_revision_id.'" />
                    </td>
                    <td id="invoice_payment_amount_'.$invoice_id.'" align="right">$'.number_format($amount_due,2).'
                        <input type="hidden" name="hdd_invoice_payment_amount_'.$invoice_id.'" id="hdd_invoice_payment_amount_'.$invoice_id.'" value="'.round($amount_due,2).'" />
                    </td>
                    <td id="invoice_payment_'.$invoice_id.'" align="right">
                        <input style="text-align: right;" type="text" name="'.$invoice_id.'" readonly id="payment_invoice_'.$invoice_id.'" value="0.00" onchange="load_total_payment(this.name,0);return false;" />
                        <input style="text-align: right;" type="hidden" name="hdd_'.$invoice_id.'" id="hdd_payment_invoice_'.$invoice_id.'" value="0" />
                    </td>
                    <td id="invoice_note_'.$invoice_id.'" align="right"><textarea name="payment_detail_notes" id="payment_detail_notes_'.$invoice_id.'" style="width: 200px;" readonly rows="1"></textarea></td>
                </tr>';
            $check_count_inv = true;
        }
    }
    
    if($check_count_inv == false)
            $invoice_line = '<tr><td colspan="8">No invoice</td></tr>';
    else{
        $invoice_line .= '<tr style="font-weight: bold;height:35px;">
                            <td style="background-color:#eee;" colspan="2" align="right">Total:</td>
                            <td style="background-color:#eee;" align="right">$'.number_format($amount_total,2).'</td>
                            <td style="background-color:#eee;" align="right"><span id="total_paymen">$0.00</span><input type="hidden" name="hdd_total_paymen" id="hdd_total_paymen" value="0" /></td>
                            <td style="background-color:#eee;"></td>
                        </tr>';
    }
    echo $invoice_line;
}
//function get_invoiceBycustomer_select(){
//    global $AppUI, $CSalesManager, $CPayment;
//            require_once (DP_BASE_DIR."/modules/sales/CTax.php");
//            $CTax = new CTax();
//            $tax_arr = $CTax->list_tax();
//            $customer = $CSalesManager->get_list_companies();
//            //$invoice_option = '<option value="">-- Choose Invoice --</option>';
//            $invoice_option = $_POST['customer_id'];
//            $invoice_id_arr = $_REQUEST['row_inv_id'];
//            $count =0;
//            foreach ($customer as $cus) {
//                $customer_id = $cus['company_id'];
//                $invoice_customer = $CPayment->get_invoice_by_customer($customer_id);
//                if(count($invoice_customer) > 0) {
//                    foreach ($invoice_customer as $inv) {
//                        $invoice_revision_id_lastest = CInvoiceManager::get_invoice_revision_lastest($inv['invoice_id']); // lay ra invoice_revision_id cuoi cung
//
//                        //Lay ra tong tien da tra - anhnn
//                            $total_amount = 0;
//                            $payment_arr = $CPayment->get_invoice_revision_by_customer($customer_id, $inv['invoice_id']);
//                            if (count($payment_arr) > 0){
//                                foreach ($payment_arr as $payment) {
//                                        $invoice_revision_id = $payment['invoice_revision_id'];
//                                        $amount = $payment['payment_amount'];
//                                        $total_amount += round($amount, 2);     
//                                        $invoice_revision_tax = $payment['invoice_revision_tax'];//Lay ra tax_id
//                                        $total_item_show = $CPayment->get_invoice_item_total($inv['invoice_id'], $invoice_revision_id_lastest, $customer_id);//Tong amount (chua tinh thue)
//                                }
//                            }
//                            $tax_value = 0;
//                            if (count($tax_arr) > 0) {
//                                    foreach ($tax_arr as $tax) {
//                                        if ($invoice_revision_tax) { // neu tinh trang la update
//                                            if ($tax['tax_id'] == $invoice_revision_tax) {
//                                                $tax_value = $tax['tax_rate'];
//                                        }
//                                        } else { // neu tinh trang la add lay ra ta default
//                                            if ($tax['tax_default'] == 1) {
//                                                $tax_value = $tax['tax_rate'];
//                                            }
//                                        }
//                                }
//                            }
//                        $caculating_tax =(floatval($total_item_show) * floatval($tax_value) / 100); //So tien thue
//                        $caculating_tax = $CSalesManager->round_up($caculating_tax);
//                        $balance = round(($total_item_show - $total_amount + $caculating_tax),2); // so tien chua tra
//                        //$balance number_format(round($total_item_show = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id)), 2),2),
//                        
//        //                print_r($total_item_show);
//        //                $total_invoice = $CPayment->get_total_payment($invoice_revision_id_lastest);
//                        if($balance>0 && $customer_id==$_POST['customer_id']) {
//                                $disabled ="";
//                                for($i=0;$i<count($invoice_id_arr);$i++){
//                                    if($inv['invoice_id'] == $invoice_id_arr[$i])
//                                        $disabled = 'disabled="true"';
//                                }
//                                $count++;
//                    		$invoice_option .= '<option '.$disabled.'  value="'.$inv['invoice_id'].','.$customer_id.','.$invoice_revision_id_lastest.','.$balance.','.$count.','.$inv['invoice_no'].','.$balance.'">'.$inv['invoice_no'].' $'.$balance.' ('.$cus['company_name'].')</option>';
//                                
//                                
//                        }   
//                    }
//                }
//            }
//    $invoice_option.='<div id="invoice_count"> '.$count.'</div>';
//    echo $invoice_option;
//    
//}
function get_add_line_invoice(){
    echo '<input type="button" class="ui-button ui-state-default ui-corner-all" onclick="add_line_payment_invoice('.$_POST['customer_id'].'); return false;" name="submit_add" value="Add invoice" />';
}

function _form_send_mail_receipt() {
    global $AppUI, $CPayment;
    $customer_id = $_REQUEST['customer_id'];
    $payment_detail_arr = $_REQUEST['payment_id'];
    $payment_detail_id = $payment_detail_arr[0];
    $payment_detail_arr = $CPayment->get_paymentDetail($payment_detail_id);
    $payment_arr = $CPayment->get_db_payment($payment_detail_arr[0][payment_id]);
    foreach ($payment_arr as $payment_arr_row) {
        $receipt_no = $payment_arr_row['payment_receipt_no'];
    }
    //$att = $CPayment->create_receipt_pdf_file($customer_id, $payment_id, true);
//    $invoice_revision_id = $_REQUEST['invoice_revision_id'];
//    if (!$invoice_revision_id){
//        $invoice_revision_id = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
//    }
//    $attention_id = $_REQUEST['attention_id'];
//    $invoice_details_arr = $CInvoiceManager->get_db_invoice_revsion($invoice_revision_id);
//    $invoice_rev = $invoice_details_arr[0]['invoice_revision'];
//    $content = 'chua config noi dung';
//    $from = CSalesManager::get_customer_email_invoice($invoice_id);
//    $to = CSalesManager::get_attention_email($attention_id);
//    $att = $CInvoiceManager->create_invoice_revision_pdf_file($invoice_id, $invoice_revision_id, true);
    //$contact_arr = CSalesManager::get_list_attention($customer_id);
    //$to = $contact_arr[0]['contact_email'];
    ?>

	<form action = "index.php?m=sales" method="POST" name="send_email_quo" id="send_email">
        <table id="myTable" align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr style="width:95%;" >
			<td style="text-indent:10px;">From</td>
			<td> <input type="text" value="" style=" width:95%;" name="sender" id="sender"></td>
		</tr>
		<tr style="width:95%;">
			<td style="text-indent:10px;">To</td>
			<td><input type="text" value="" style=" width:95%;" name="reciver" id="reciver"></td>
		</tr>
		<tr style="width:95%;">
			<td style="text-indent:10px;">Subject</td>
			<td><input type="text" value="" style=" width:95%;" name="subject" id="subject"></td>
		</tr>		
        <tr>
            <td style="text-indent:10px;">Content</td>
            <td height="220px">
            <textarea id="content" value="" name="content" type="text" style="width: 565; height: 200;"></textarea>
            </td>
        </tr>    
        
        <tr id="file3">
            <td style="text-indent:10px;"> File Attach</td>
            <td>
            <input id="file_attach" type="text" name="name_file" value="<?php echo $receipt_no .'.pdf';?>" readonly="true">
             <input class="mainbutton" type="submit" onclick="print_receipt(<?php echo $customer_id; ?>, <?php echo $payment_detail_id;  ?>); return false;"  value="Download" name="dowload_file">
            </td>
        </tr>
        
        <tr>
            <td align="center" colspan="2">
            <input class="mainbutton" type="button" value="Send" name="send" onclick="setSendButtonRe(<?php echo $customer_id; ?>, <?php echo $payment_detail_id; ?>)" />
            <input class="mainbutton" type="reset" value="Reset" name="back" />
            </td>
        </tr>
        
        </table>
	</form>

    <?php
}

function _do_send_email_payment() {
    
    global $CPayment;
    $content = $_POST['content'];
    $reciver = $_POST['reciver'];
    $sender = $_POST['sender'];
    $subject = $_POST['subject'];
    $payment_detail_id = $_REQUEST['payment_detail_id'];
    $customer_id = $_REQUEST['customer_id'];
    //$status=1;

    $status = $CPayment->send_mail_receipt($customer_id, $payment_detail_id, $content, $sender, $subject, $reciver);
        
        if ($status)
            echo '{"status": "Success", "message": "Email sent"}';
        else
            echo '{"status": "Failure", "message": "Email sent error"}';
}

function get_total_credit_customer(){
    global $CCreditManager,$CSalesManager;
    $creditNote_id=$_POST['creditNote_id'];
    $cre_amount_total = $CSalesManager->total_creditNote_amount_and_tax($creditNote_id);
    if($creditNote_id=="")
        echo "";
    else
        echo '$'.number_format($cre_amount_total,2).'<input type="hidden" id="hdd_credit_amount" value="'.$cre_amount_total.'" />';
}
function get_credit_by_customer(){
    global $CCreditManager;
    $customer_id = $_POST['customer_id'];
    $option_credit = '<option value="">--Select Credit Note--</option>';
    if($customer_id!=""){
        $credit_arr = $CCreditManager->list_db_creditNote(false, $customer_id);
        foreach ($credit_arr as $credit_row) {
            if($credit_row['credit_note_status']!=2)
                $option_credit .='<option value="'.$credit_row['credit_note_id'].'">'.$credit_row['credit_note_no'].'</option>';
        }
    }
    echo $option_credit;
}
function _do_add_creditNote_applied(){
    global $CCreditManager;
    $credit_note_id = $_POST['credit_note_id'];
    $invoice_id = $_POST['invoice_id'];
    $applied_amount = $_POST['applied_amount'];
    $ojectApplied['credit_note_applied_id'] = 0;
    $ojectApplied['credit_note_id']=$credit_note_id;
    $ojectApplied['invoice_id']=$credit_note_id;
    $ojectApplied['applied_amount']=  floatval($credit_note_id);
    $credit_note_arr=$CCreditManager ->add_credit_note_applied($ojectApplied);
    if(count($credit_note_arr)>0)
        echo '{"status":"Success"}';
    else{
        echo '{"status":"Failure"}';
    }
}
function _do_update_creditNote_status(){
        global $CCreditManager;
    $credit_note_id = $_POST['credit_note_id'];
    $credit_note_status = intval($_POST['status']);
    $creditNote_arr = $CCreditManager->update_credit_note_status($credit_note_id, $credit_note_status);
    if(count($creditNote_arr)>0)
        echo '{"status":"Success"}';
    else{
        echo '{"status":"Failure"}';
    }
}
?>

