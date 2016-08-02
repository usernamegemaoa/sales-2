<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once DP_BASE_DIR."/modules/sales/CPaymentManager.php";
require_once DP_BASE_DIR."/modules/sales/CTax.php";
require_once (DP_BASE_DIR."/modules/banks/CBankAccount.php");
require_once DP_BASE_DIR.'/modules/expenses/Expenses_tax_manage.php';
require_once DP_BASE_DIR.'/modules/expenses/ExpensesCategory.php';
require_once DP_BASE_DIR.'/modules/clients/company_manager.class.php';
require_once (DP_BASE_DIR.'/modules/engagements/engagementManager.class.php');
require_once (DP_BASE_DIR.'/modules/sales/CMemoManager.php');

$CPayment = new CPaymentManager();
$CSalesManager = new CSalesManager();
$perms =& $AppUI->acl();
$canAdd = $perms->checkModule($m, 'add');
$canDelete = $perms->checkModule($m, 'delete');
$canEdit = $perms->checkModule($m, 'edit');
$canRead = $perms->checkModule($m, 'view');
$CBankAccount = new CBankAccount();
$CCompany = new CompanyManager();
$CContract = new EngagementManager();
$CMemoManager = new CMemoManager();
function __default() {
    
    global $AppUI, $CSalesManager;
        
    include DP_BASE_DIR."/modules/sales/payment.js.php";
    $rows = $CSalesManager->get_list_companies();
    // new payment update by dungnv
    $add_id = '<a class="icon-add icon-all" id="add-new" onclick="add_new_payment('.$template_id.');" href="#">Add Payment</a>';
    echo $AppUI ->createBlock('block_add', $add_id , 'style="text-align: left; height: 40px;"');
    
    echo '<div id="new_payment" style="margin-bottom: 10px; margin-left: 40px; width: 80%; overflow: auto;">';
    echo '</div>';
    
    $client_option = '<option value="">-- Choose Customer --</option>';
    foreach ($rows as $row) {
        $client_option .= '<option value="'. $row['company_id'] .'">'. $row['company_name'] .'</option>';
    }
//    $customer = 'Customers: <select id="payment_customer" class="text" onchange="load_invoice_by_customer(this.value); return false;">'. $client_option .'</select>';
//    $invoice = 'Invoice: <select id="payment_invoice" class="text"id="change_invoice_by_customer"><option value="">-- All --</option></select>';
//    $search_btn = '<button id="load_payment" class="ui-button ui-state-default ui-corner-all" onclick="load_payment(); return false;">Search</button>';
    
    //echo $AppUI ->createBlock('div_search', $customer .' '. $invoice .' '. $search_btn, 'style="text-align: left; height: 40px;overflow: visible;"');
    
    echo '<div id="detail_payment" style="margin-bottom: 10px; width: 100%; overflow: auto;">';
    echo '</div>';
}


//function vw_payment_detail() {
//    
//    global $AppUI, $CSalesManager;
//    
//    $payment_customer = $_POST['payment_customer'];
//    $field_set = ''; $field_set_1 = '';
//    if ($payment_customer != '') {
//        $customer_name = $CSalesManager->get_customer_field_by_id($payment_customer, 'company_name');
////        $field_set_head = '<fieldset class="ui-corner-all" style="float: left; width: 97%;" >';
////        $field_set_head .= '<legend><h2>'. $customer_name .'</h2></legend>';
//        $field_set_head .= '<h2>Customer: '. $customer_name .'</h2>';
//        
//        $field_set .= $field_set_head;
//        $field_set .= '<div id="div_tbl_payment_'. $payment_customer .'">'. vw_tbl_payment($payment_customer) . '</div>';
////        $field_set .= '</fieldset>';
//        
//        $field_set_1 .= $field_set_head;
//        $field_set_1 .= '<div id="div_tbl_payment_schedule_'. $payment_customer .'">'. vw_tbl_payment_schedule($payment_customer) .'</div>';
//        $field_set_1 .= '</fieldset>';
//    } else {
//        $rows = $CSalesManager->get_list_companies();
//        if (count($rows) > 0) {
//            foreach ($rows as $row) {
//                $customer_name = $CSalesManager->get_customer_field_by_id($row['company_id'], 'company_name');                
//                $field_set_head = '<fieldset class="ui-corner-all" style="float: left; width: 95%;margin-top:10px;" >';
//                $field_set_head .= '<legend><h2>'. $customer_name .'</h2></legend>';
//                
//                $field_set .= $field_set_head;
//                $field_set .= '<div id="div_tbl_payment_'. $row['company_id'] .'">'. vw_tbl_payment($row['company_id']) .'<div>';
//                $field_set .= '</fieldset>';
//                $field_set .= '<p>Total: </p>';
//                
//                $field_set_1 .= $field_set_head;
//                $field_set_1 .= '<div id="div_tbl_payment_schedule_'. $row['company_id'] .'">'. vw_tbl_payment_schedule($row['company_id']) .'<div>';
//                $field_set_1 .= '</fieldset>';
//            }
//        }
//    }
//     
//    echo '<fieldset class="ui-widget-content ui-corner-all" style="float: left; width: 71%;" >
//            <legend><h1>Payment</h1></legend>
//            '. $field_set .'
//            </fieldset>';
//    echo '<div id="detail_payment" style="margin-bottom: 10px; width: 100%; overflow: auto;">';
//    echo '</div>';
////    echo '<fieldset class="ui-widget-content ui-corner-all" style="float: right; width: 47%;" >
////            <legend><h1>Payment schedule</h1></legend>
////            '. $field_set_1 .'
////            </fieldset>';
//    
//}

function vw_tbl_payment($customer_id) {
    
    global $AppUI, $CPayment, $canEdit, $canDelete;
    $payment_invoice = $_POST['payment_invoice'];    
    $tbody = '';
    if ($payment_invoice != '') {
        $tbody .= vw_tbody_payment($customer_id, $payment_invoice);
    } else {
        $invoice_arr = $CPayment->get_invoice_by_customer($customer_id);
        if (count($invoice_arr) > 0) {
            foreach ($invoice_arr as $invoice) {
                $tbody .= vw_tbody_payment($customer_id, $invoice['invoice_id']);
            }
        } else {
            $invoice_entry = true;
        }
    }
//Lay ra tong tien da tra - anhnn
    $total_amount = 0;
    $payment_arr = $CPayment->get_invoice_revision_by_customer($customer_id, $payment_invoice);
    if (count($payment_arr) > 0){
        foreach ($payment_arr as $payment) {
                $invoice_revision_id = $payment['invoice_revision_id'];
                $amount = $payment['payment_amount'];
                $total_amount += round($amount, 2);     
                $invoice_revision_tax = $payment['invoice_revision_tax'];//Lay ra tax_id
                $total_item_show = $CPayment->get_invoice_item_total($payment_invoice, $invoice_revision_id, $customer_id);//Tong amount (chua tinh thue)
        }
    }
//End- Lay ra tong tien da tra    
//Lay ra so tien chua tra - anhnn
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();
    
//    $invoice_revision_id_arr = $CPayment->get_invoice_revision_by_customer($customer_id, $payment_invoice);
//    if(count($invoice_revision_id_arr) > 0){
//        foreach ($invoice_revision_id_arr as $invoice_revision_id_arr1) {
//            $invoice_revision_id = $invoice_revision_id_arr1['invoice_revision_id'];
//            $invoice_revision_tax = $invoice_revision_id_arr1['invoice_revision_tax'];//Lay ra tax_id
//            $total_item_show = $CPayment->get_invoice_item_total($payment_invoice, $invoice_revision_id, $customer_id);//Tong amount (chua tinh thue)
//        }
//    }
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
        $caculating_tax = (floatval($total_item_show) * floatval($tax_value) / 100); //So tien thue
//    }
    $balance = ($total_item_show - $total_amount + $caculating_tax); // so tien chua tra

    
//END - Lay ra so tien chua tra -  anhnn   
    
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
                    </tr>
                </thead>';
                
        $tbl .= $tbody;
        $tbl .= '
                <tfoot>
                </tfoot>
                <table cellspacing="0" cellpadding="1" style="clear: both; width: 50%" id="payment_invoice_table_tr" class="tbl">
                <td><p>Total Paid: $'. $total_amount .'</p>
                <p>Balance: $'. round($balance, 2) .'</p></td>
                </table>
            </table><br/>';
        $email = '<a class="icon-email icon-all" onclick="popupSendEmailReceipt(); return false;" href="#">Email</a>';
        $print_pdf = '<a class="icon-email icon-all" onclick="create_payment_receipt_pdf('. $customer_id .'); return false;" href="#">Print to PDF</a>';
        if ($canDelete) {
            $delete_id = '<a class="icon-delete icon-all" onclick="delete_payment('. $customer_id.','. $invoice_revision_id .', '. $payment_invoice .'); return false;" href="#">Delete</a>';
        }
        $tbl .= $AppUI ->createBlock('del_block', $delete_id . $print_pdf . $email, 'style="text-align: left;"');
        
        if ($canEdit) {
$js = <<<EOD
    $(document).ready(function() {
        var url = '?m=sales&a=vw_payment&c=_do_update_payment_field&suppressHeaders=true';
        
        $('.edit_payment_amount').editable(url, {
            indicator    : "Loading...",
            submitdata : { field_name: 'payment_amount' },
            width: 220,
            submit: "Save",
            cancel: 'Cancel',
            height: 20
        });
        
        $('.edit_payment_notes').editable(url, {
            indicator    : "Loading...",
            submitdata : { field_name: 'payment_notes' },
            type: 'textarea',
            submit: "Save",
            cancel: 'Cancel',
        });
        
       /* $('.edit_payment_date').editable(url, {
            indicator    : "Loading...",
            submitdata : { field_name: 'payment_date' },
            submit: "Save",
            cancel: 'Cancel',
        });*/
        
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
        
        /*$('.edit_payment_date').click(function(e) {
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

function vw_tbl_payment_schedule($customer_id) {
    
    global $AppUI, $CPayment, $canEdit, $canDelete;
    
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
        if ($canDelete) {
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
    global $canAdd;

    $invoice_revision_id_lastest = CInvoiceManager::get_invoice_revision_lastest($invoice_id); // lay ra invoice_revision_id cuoi cung
        
        $invoice_no = CInvoiceManager::get_invoice_field_by_id($invoice_id, 'invoice_no');
        
        if ($canAdd) {
//            $a_add = '<a href="#" class="icon-add icon-all" onclick="add_row_payment('. $customer_id .', '. $invoice_revision_id_lastest .', '.$invoice_id.'); return false;">Add</a>';
        }
        
            $tbody = '<tbody>
                        <tr>
                                <td colspan="5"> Invoice No: <font style="color: red"><b><a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_revision_id_lastest.'">'. $invoice_no .'</a></b></font> &nbsp;&nbsp;'. $a_add .'</td>
                        </tr>
                        <tbody>';
            
            $tbody .= '<tbody id="show_tr_payment_'. $invoice_revision_id_lastest .'">';
            $tbody .= vw_tr_payment($customer_id, $invoice_revision_id_lastest, $invoice_id);
            $tbody .= '</tbody>';
            
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
                                <td colspan="5"> Invoice No: <font style="color: red"><b><a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_revision_id_lastest.'">'. $invoice_no .'</a></b></font> &nbsp;&nbsp;'. $a_add .'</td>
                        </tr>
                        <tbody>';
            
            $tbody .= '<tbody id="show_tr_payment_schedule_'. $invoice_revision_id_lastest .'">';
            $tbody .= vw_tr_payment_schedule($customer_id, $invoice_revision_id_lastest);
            $tbody .= '</tbody>';
            
    return $tbody; 
    
}

function vw_tr_payment($customer_id, $invoice_revision_id_lastest) {
    
    global $AppUI, $CPayment;
    
        
        $paymentDetail_arr = $CPayment->lis_db_payment_detail($invoice_revision_id_lastest);
        $PaymentMethods = array(0=>'');
        $PaymentMethods .= dPgetSysVal('PaymentMethods');
        
        
        $tr = '<tr id="tr_invoice_'.$invoice_revision_id_lastest.'"></tr>';
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
            if(count($paymentDetail_arr)>0){
                foreach ($paymentDetail_arr as $paymentDetail){
                    $payment_arr = $CPayment->get_db_payment($paymentDetail['payment_id']);
                    foreach ($payment_arr as $payment){
                        $payment_id=$payment['payment_id'];
                        $payment_method=$payment['payment_method'];
                        $payment_date=$payment['payment_date'];
                        $payment_node=$payment['payment_notes'];
                    }
                    $tr .='<tr>
                            <td align="right"><input type="checkbox" value="'. $paymentDetail['payment_detail_id'] .'" name="payment_check_list_'. $customer_id .'" id="payment_check_list_'. $customer_id .'"></td>
                            <td class="edit_payment_amount" id="'. $paymentDetail['payment_detail_id'] .'" align="right">'. $paymentDetail['payment_amount'] .'</td>
                            <td class="edit_payment_method" id="'. $payment_id .'">'.$PaymentMethods[$payment_method] .'</td>
                            <td class="edit_payment_date" id="'. $payment_id .'">'. date(FORMAT_DATE_DEFAULT, strtotime($payment_date)) .'</td>
                            <td class="edit_payment_notes" id="'. $payment_id .'">'.  $payment_node .'</td>
                        </tr>';
                }
            }
        
            $tbody = $tr;
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
// add new payment form update by dungnv
function vw_add_payment() {
    global $AppUI, $CSalesManager, $CPayment;
    //include DP_BASE_DIR."/modules/sales/payment.js.php";
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();
    /*
     * get invoice with format: INV012344 {Amount Total} {Customer}
     * update by dungnv
     */
    // get list Customer
    $customer = $CSalesManager->get_list_companies();
    
    $form = '<fieldset class="ui-widget-content ui-corner-all" style="float: left; width: 80%;" >
            <legend><h1>Payment</h1></legend>';
    $form .= '<form id="new_payment" name="new_payment" method="POST">';
    $invoice_option = '<option value="">-- Choose Invoice --</option>';
    foreach ($customer as $cus) {
        $customer_id = $cus['company_id'];
        $invoice_customer = $CPayment->get_invoice_by_customer($customer_id);
        if(count($invoice_customer) > 0) {
            foreach ($invoice_customer as $inv) {
                $invoice_revision_id_lastest = CInvoiceManager::get_invoice_revision_lastest($inv['invoice_id']); // lay ra invoice_revision_id cuoi cung
                
                //Lay ra tong tien da tra - anhnn
                    $total_amount = 0;
                    $payment_arr = $CPayment->get_invoice_revision_by_customer($customer_id, $inv['invoice_id']);
                    if (count($payment_arr) > 0){
                        foreach ($payment_arr as $payment) {
                                $invoice_revision_id = $payment['invoice_revision_id'];
                                $amount = $payment['payment_amount'];
                                $total_amount += round($amount, 2);     
                                $invoice_revision_tax = $payment['invoice_revision_tax'];//Lay ra tax_id
                                $total_item_show = $CPayment->get_invoice_item_total($inv['invoice_id'], $invoice_revision_id_lastest, $customer_id);//Tong amount (chua tinh thue)
                        }
                    }
                    
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
                $caculating_tax =(floatval($total_item_show) * floatval($tax_value) / 100); //So tien thue
                $balance = round(($total_item_show - $total_amount + $caculating_tax),2); // so tien chua tra
                
//                print_r($total_item_show);
//                $total_invoice = $CPayment->get_total_payment($invoice_revision_id_lastest);
                if($balance) {
                    $invoice_option .= '<option value="'.$inv['invoice_id'].','.$customer_id.','.$invoice_revision_id_lastest.','.$balance.'">'.$inv['invoice_no'].' $'.$balance.' ('.$cus['company_name'].')</option>';
                }
            }
        }
    }
        $form .= '<dl>';
        $form .= '<dt>Invoice: </dt>';
        $form .= '<dd><select class="text" name="invoice_id" id="invoice_id">'.$invoice_option.'</select></dd>';
        $form .= '</dl>';
        $form .= '<dl>';
        $form .= '<dt>Amount: </dt>';
        $form .= '<dd><input type="text" class="text" name="payment_amount" id="payment_amount" value="" size="20px" /></dd>';
        $form .= '</dl>';
        $form .= '<dl>';
        
         $PaymentMethods = dPgetSysVal('PaymentMethods');
            $option = '';
            foreach ($PaymentMethods as $key => $value) {
                $option .= '<option value="'. $key .'">'. $value .'</option>';
         }
        $form .= '<dt>Method: </dt>';
        $form .= '<dd><select class="text" name="payment_method" id="payment_method">'.$option.'</select></dd>';
        $form .= '</dl>';
        $form .= '<dl>';
        $form .= '<dt>Date: </dt>';
        $form .= '<dd><input type="text" class="text" name="payment_date" id="payment_date" value="'.date('Y/m/d').'" />
                       <input type="hidden" name="payment_date_hidden" id="payment_date_hidden" value=""></dd>';
        $form .= '</dl>';
        $form .= '<dl>';
        $form .= '<dt>Notes: </dt>';
        $form .= '<dd><textarea name="payment_notes" id="payment_notes" cols="25" style="width: 150px;" rows="5"></textarea></dd>';
        $form .= '</dl>';
        $form .= '<dl>';
        $form .= '<input type="button" class="ui-button ui-state-default ui-corner-all" onclick="save_payment_add(); return false;" name="submit_add" value="Save" />&nbsp;';
        $form .= '<input type="button" class="ui-button ui-state-default ui-corner-all" name="cancel" id="cancel" onclick="back_list();" value="cancel" />';
        $form .= '</dl>';
    $form .= '</form></fieldset>';
    echo $form;
}
function _load_vw_tr_payment() {
    echo vw_tr_payment($_POST['customer_id'], $_POST['invoice_revision_id_lastest']);
}

function _load_vw_tr_payment_schedule() {
    echo vw_tr_payment_schedule($_POST['customer_id'], $_POST['invoice_revision_id_lastest']);
}

function _load_vw_tbl_payment() {
    echo vw_tbl_payment($_POST['customer_id']);
}

function _load_vw_tbl_payment_schedule() {
    echo vw_tbl_payment_schedule($_POST['customer_id']);
}

function _get_invoice_select() {
    
    global $CPayment;
   
    $invoice_arr = $CPayment->get_invoice_by_customer($_POST['customer_id']);
    
    $option = '<option value="">-- All --</option>';
    if (count($invoice_arr) > 0) {
        foreach ($invoice_arr as $invoice) {
            $option .= '<option value="'. $invoice['invoice_id'] .'">'. $invoice['invoice_no'] .'</option>';
        }
    }
    echo $option;
}
function _get_receipt_select() {
    
    global $CPayment;
    $invoice_id=false;
    if(isset($_POST['invoice_id']))
        $invoice_id=$_POST['invoice_id'];
    if(isset($_POST['customer_id']) && $_POST['customer_id']>0)
        $receipt_arr = $CPayment->get_receit_by_customer($_POST['customer_id'],$invoice_id);
    else {
         $receipt_arr = $CPayment->list_db_payment(false,false,false,false,false,'receipt');

    }
    $option = '<option value="">-- All --</option>';
    if (count($receipt_arr) > 0) {
        foreach ($receipt_arr as $invoice) {
            if($invoice['payment_receipt_no'] !="")
                $option .= '<option value="'. $invoice['payment_id'] .'">'. $invoice['payment_receipt_no'] .'</option>';
        }
    }
    echo $option;
}

function _do_add_payment() {
    global $CPayment;
    
    $payment_id = $CPayment->add_payment($_POST);
    if (intval($payment_id) > 0) {
        echo '{"status": "Success"}';
    } else {
        echo '{"status": "Failure", "message": "Co loi trong qua trinh them"}';
    }
    
}

function _do_add_payment_schedule() {
    global $CPayment;
    
    $payment_schedule_id = $CPayment->add_payment_schedule($_POST);
    if (intval($payment_schedule_id) > 0) {
        echo '{"status": "Success"}';
    } else {
        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
    }
    
}

function _do_print_payment() {
    
    global $CPayment;
    
    $customer_id = $_REQUEST['customer_id'];
    $payment_detail_id_arr = $_REQUEST['payment_id'];
    $payment_detail_id = $payment_detail_id_arr[0];
    $CPayment->create_receipt_pdf_file($customer_id, $payment_detail_id);

}



function _do_remove_payment() {
    
    global $CPayment;
    $payment_detail_id_arr = $_REQUEST['payment_detail_id'];
    $payment_detail_arr=$CPayment->get_paymentDetail($payment_detail_id_arr);
    if(count($payment_detail_arr)<=1)
        $payment_id_arr = $payment_detail_arr[0]['payment_id'];

    $db_return = $CPayment->remove_payment($payment_id_arr);

    if ($db_return)
        echo '{"status": "Success"}';
    else
        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
}
function _do_remove_payment_detail(){
    global $CPayment;
    $payment_detail_id_arr = $_REQUEST['payment_detail_id'];
    $db_return = $CPayment ->remove_payment_detail($payment_detail_id_arr);
    if($db_return)
        echo '{"status": "Success"}';
    else
        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
}

function _do_remove_payment_schedule() {
    
    global $CPayment;

    $payment_schedule_id_arr = $_REQUEST['payment_schedule_id'];

    $db_return = $CPayment->remove_payment_schedule($payment_schedule_id_arr);

    if ($db_return)
        echo '{"status": "Success"}';
    else
        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
}

function _do_update_payment_schedule_field() {
    
    global $CPayment;
    
    $field_id = $_POST['id'];
    $field_name = $_POST['field_name'];
    $value = $_POST['value'];
    $CPayment->update_payment_schedule($field_id, $field_name, $value);
    echo $value;
}

function _do_update_payment_field() {
    
    global $CPayment,$CBankAccount;
    
    $field_id = $_POST['id'];
    $field_name = $_POST['field_name'];
    $value = $_POST['value'];
    $CPayment->update_payment($field_id, $field_name, $value);
    
    
    if ($_POST['action'] == 'for_method') {
        $PaymentMethods = dPgetSysVal('PaymentMethods');
        echo $PaymentMethods[$value];
    } elseif ($field_name=="payment_date") {
        echo '{"status": "Success"}';
    }
    else if ($_POST['action'] == 'for_bank') {
        if($value==0)
        {
            echo '';
        }
        else
        {
            $bank_account_db = $CBankAccount->getBankAccount($value);
            echo $bank_account_db[0]['bank_account_name'];
        }
    }
    else
        echo $value;
}
function _do_update_payment_detail_field(){
    global  $CPayment;
    
    $field_id = $_POST['id'];
    $field_name = $_POST['field_name'];
    $value = $_POST['value'];
    $CPayment->update_payment_detail($field_id, $field_name, $value);
    echo $value;
}
function _load_payment_method() {
    $PaymentMethods_arr = dPgetSysVal('PaymentMethods');
    print json_encode($PaymentMethods_arr);
}
function _load_bank_account() {
    global $CBankAccount;

    $bank_account_db = $CBankAccount->getBankAccount();
    $bank_account_arr = array(0=>'');
    foreach ($bank_account_db as $bank_account_db_item) {
        $bank_account_arr[$bank_account_db_item['bank_account_id']] = $bank_account_db_item['bank_account_name'];
    }
    
    //$PaymentMethods_arr = dPgetSysVal('BankAccount');
    print json_encode($bank_account_arr);
}
function _load_paymssent_method() {
    $PaymentMethods_arr = dPgetSysVal('Categories');
    print json_encode($PaymentMethods_arr);
}
function _load_ex_tax() {
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();
 
    $PaymentMethods_arr[0]='---';
    foreach ($tax_arr as $value1)
        $PaymentMethods_arr[$value1['tax_id']] = $value1['tax_rate'];
    print json_encode($PaymentMethods_arr);
}

function _load_customer() {
    $PaymentMethods_arr = dPgetSysVal('Categories');
   
    global  $CCompany;
    $supplier_ex_arr = $CCompany->getCompanies();
    
    $supplier[0]="--Select Customer--";
    foreach ( $supplier_ex_arr as $key=>$value) {

        $supplier[$value['company_id']]= $value['company_name'];
    }

    print json_encode($supplier);
}

function load_contract()
{
    global $CContract;
    $id = $_REQUEST['id'];

    $ex_category = new ExpensesCategory();
    $information = $ex_category->getExIDByCategory($id);
   
    $customer_id=$information[0]['customer_id'];
    $location_id=$information[0]['job_location_id'];
    
   
    //lay tat ca cac contract trong he thong
    if($location_id>0)
    {
        $contract_arr = $CContract->getContractByAddres($location_id);
       
    }
    
    $contract[0]="--Select contract--";
    if($location_id>0){
        foreach ($contract_arr as $contract_item)
        {
            $contract[$contract_item['engagement_id']]=$contract_item['engagement_code'];
            if($contract_item['engagement_start_date'] != "")
                $contract[$contract_item['engagement_id']] .=' - '.date('d M Y',  strtotime($contract_item['engagement_start_date']));
            if($contract_item['engagement_end_date'] != "")       
                $contract[$contract_item['engagement_id']] .=' to '.date('d M Y',  strtotime($contract_item['engagement_end_date']));
        }
        
    }
     print json_encode($contract);
   
}

function load_job_location(){
    
    global $CSalesManager,$CMemoManager,$CContract,$CCompany;
    
    
    $id = $_REQUEST['id'];
    $ex_category = new ExpensesCategory();
    $information = $ex_category->getExIDByCategory($id);
    $customer_id=$information[0]['customer_id'];
   
    /* Load Billing Adress */   
    $address_option[0] = "--Select Job Location--";
        if($customer_id && $customer_id!="")
            $address_arr = $CSalesManager->get_list_address($customer_id);
        else if($_POST['customer_id'] != ""){
            $address_arr = $CSalesManager->get_list_address($_POST['customer_id']);
            $customer_memo_err=$CMemoManager->get_memo_billing_address($_POST['customer_id']);
        }
            if (count($address_arr) > 0) {
                foreach ($address_arr as $address) {
                    if($address['address_type']==2)
                        $address_type = "[Billing] ";
                    else if($address['address_type']==1)
                        $address_type = "[Job Site] ";
                    else
                        $address_type = "[NA] ";
                    $brand="";
                    if($address['address_branch_name']!="")
                        $brand=$address['address_branch_name']." - ";
                    $selected = '';
                    if ($address_id == $address['address_id'] || $address['address_id'] == $customer_memo_err[0]['address_id'])
                        $selected = 'selected="selected"';
                    $address_option[$address['address_id']]= $address_type.$brand. $address['address_street_address_1'].' '.$address['address_street_address_2'].' Singapore '.$address['address_postal_zip_code'];
                }
                
            }
        // Lay adress customer gan customer duoc chon lam contractor
            if($customer_id)
                $address_customer_contractor = $CSalesManager->get_Customer_By_ContractorCustomer($customer_id);
            else if($_POST['customer_id'] != ""){
                $address_customer_contractor = $CSalesManager->get_Customer_By_ContractorCustomer($_POST['customer_id']);
            }
            if(count($address_customer_contractor>0)){
                foreach ($address_customer_contractor as $address_customer_value) {
                 
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
                                $brand=$address_contractor_value['address_branch_name']." - ";
                            $selected = '';
                            if ($address_id == $address_contractor_value['address_id'] || $address_contractor_value['address_id'] == $customer_memo_err[0]['address_id'])
                                $selected = 'selected="selected"';
                          $address_option[$address_contractor_value['address_id']] .= $address_type.$brand.$address_contractor_value['address_street_address_1'].' '.$address_contractor_value['address_street_address_2'].' Singapore '.$address['address_postal_zip_code'] ;
                      }
                 
                }
            }

        // Lay address customer gan customer duoc chon lam Agent
        if($customer_id)
            $address_customer_agent = $CSalesManager->get_Customer_By_AgentCustomer($customer_id);
        else if($_POST['customer_id'] != "")
            $address_customer_agent = $CSalesManager->get_Customer_By_AgentCustomer($_POST['customer_id']);
        if($address_customer_agent>0){
            foreach ($address_customer_agent as $address_cus_agent_value){
               
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
                            $brand=$address_agent_value['address_branch_name']." - ";
                     
                      $address_option[$address_agent_value['address_id']]=$address_type.$brand.$address_agent_value['address_street_address_1'].' '.$address_agent_value['address_street_address_2'].' Singapore '.$address['address_postal_zip_code'];
                  }
               
            }
        }
        //Lay add customer la agent cua customer duoc chon
        if($customer_id)
            $address_agent_customer = $CSalesManager->get_AgentCustomer_by_customer($customer_id);
        else if($_POST['customer_id'] != "")
            $address_agent_customer = $CSalesManager->get_AgentCustomer_by_customer($_POST['customer_id']);
        if($address_agent_customer>0){
            foreach ($address_agent_customer as $address_agent_customer_value) {
                if($address_agent_customer_value['address_id']!=""){
//                    $address_option.='<optgroup label="Agent '.$address_agent_customer_value['company_name'].'">';
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
                                $brand=$address_agent_value1['address_branch_name']." - ";
                         
                            $address_option[$address_agent_value1['address_id']]=$address_type.$brand.$address_agent_value1['address_street_address_1'].' '.$address_agent_value1['address_street_address_2'].', Singapore '.$address['address_postal_zip_code'];
                        }
                    
                }
            }
        }

        //Lay add customer la contractor cua customer duoc chon
        if($customer_id)
            $address_contractor_customer = $CSalesManager->get_ContractorCustomer_by_customer($customer_id);
        else if($_POST['customer_id'] != ""){
            $address_contractor_customer = $CSalesManager->get_ContractorCustomer_by_customer($_POST['customer_id']);
        }
        if($address_contractor_customer>0){
            foreach ($address_contractor_customer as $address_contractor_value) {
                if($address_contractor_value['address_contractor']!=0){
                   
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
                                $brand=$address_agent_value1['address_branch_name']." - ";
                            $selected = '';
                            $address_option[$address_agent_value1['address_id']]=$address_type.$brand.$address_agent_value1['address_street_address_1'].' '.$address_agent_value1['address_street_address_2'].' Singapore '.$address['address_postal_zip_code'];
                        }
                     
                }
            }
        }
        print json_encode($address_option);

}

?>

