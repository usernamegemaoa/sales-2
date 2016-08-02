<?php
if(!defined('DP_BASE_DIR')){
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
require_once (DP_BASE_DIR."/modules/sales/payment.js.php");
require_once DP_BASE_DIR."/modules/sales/CPaymentManager.php";
//require_once (DP_BASE_DIR."/modules/sales/addPayment.js.php");
$CSalesManager = new CSalesManager();
$CPayment = new CPaymentManager();
$CInvoiceManager= new CInvoiceManager();
$perms =& $AppUI->acl();
$canAdd = $perms->checkModule($m, 'add');
$canDelete = $perms->checkModule($m, 'delete');
$canEdit = $perms->checkModule($m, 'edit');
$canRead = $perms->checkModule($m, 'view');

$addView_payment = $perms->checkModule( 'sales_payment', 'add');
$AccessView_payment = $perms->checkModule( 'sales_payment', 'access');


$option_invoice = '<option value="">All</option>';
if(isset($_GET['customer_id']) && isset($_GET['invoice_id']))
{
    $customer_id = $_GET['customer_id'];
    $invoice_id = $_GET['invoice_id'];
    $invoice_arr = $CPayment->get_invoice_by_customer($customer_id);
    foreach ($invoice_arr as $invoice_row) {
        $slect = "";
        if($invoice_id==$invoice_row['invoice_id'])
            $slect = "selected";
        $option_invoice .='<option value="'.$invoice_row['invoice_id'].'" '.$slect.'>'.$invoice_row['invoice_no'].'</option>'; 
    }
}    
    /* Add Payment */
    if($addView_payment)
        $add_id = '<a class="icon-add icon-all" id="add-new" onclick="add_new_payment_new();" href="#">Add Payment</a>';
    echo $AppUI ->createBlock('block_add', $add_id , 'style="text-align: left; height: 40px;"');
    echo '<div id="new_payment_new" style="margin-bottom: 10px; margin-left: 40px; width: 100%; overflow: auto;">';
    echo '</div>';
    
    /* Search Payment theo customer */
    if($AccessView_payment)
    {
        $rows = $CSalesManager->get_list_companies();
        $client_option = '<option value="">All</option>';
        foreach ($rows as $row) {
            $selected = "";
            if($customer_id == $row['company_id'])
                $selected = "selected";
            $client_option .= '<option value="'. $row['company_id'] .'" '.$selected.'>'. $row['company_name'] .'</option>';
        }
        $customer = 'Customers: <select style="width:300px;" id="payment_customer" class="text" onchange="load_invoice_by_customer(this.value); return false;">'. $client_option .'</select>';
        $invoice = 'Invoice: <select onchange="load_reipt(this.value);return false;" id="payment_invoice" class="text"id="change_invoice_by_customer">'.$option_invoice.'</select>';
        $row_receipt = $CPayment->list_db_payment();
        $receipt = '<select style="width:200px;margin-top:0px;" id="receipt_search"><option value="">--All--</option>';
//        foreach ($row_receipt as $data)
//            $receipt .= '<option value='.$data['payment_id'].'>'.$data['payment_receipt_no'].'</option>';
        $receipt .= '</select>';
        $receipt ='&nbsp;&nbsp;Receipt No:  &nbsp;&nbsp;'.$receipt;
        $search_btn = '<button id="load_payment" class="ui-button ui-state-default ui-corner-all" onclick="load_payment(); return false;">Search</button>';
    }
    //status
    $status="";
    
    $status_option='Status: <select id="status_invoice_id">';
    $status_option .= '<option value="">All</option>';
    
    if(isset($_GET['status']) && $_GET['status']=='Paid')
    {
        $status_option .= '<option value="Paid" selected>Paid</option>';
        $status_option .= '<option value="Outstanding" >Outstanding</option>';
    }
    else
    {
    $status_option .= '<option value="Paid">Paid</option>';
    $status_option .= '<option value="Outstanding" selected>Outstanding</option>';
    }
    $status_option .='</select>';
    
    echo $AppUI ->createBlock('div_search', $customer .' '. $invoice .' '.$receipt.'<br/> '.$status_option.' '. $search_btn, 'style="text-align: left; height: 100px;"');
    //echo '<div id="div_search">'.$customer.$search_btn.'</div>';
    /* List Payment */
    echo '<div id="detail_payment" style="margin-bottom: 10px;margin-left:40px; width: 100%; overflow: auto;">';
    echo '</div>';
   

?>
<script>
    $(document).ready(function() {
        $('#receipt_search').select2();
        var status = '<?php echo $_GET['status'] ?>';
        var customer_id = '<?php echo $_GET['customer_id'] ?>';
        var creditNote_id = '<?php echo $_GET['creditNote_id']?>';
        var total_amount = '<?php echo $_GET['total_amount'] ?>';
        var invoice_id = '<?php echo $_GET['invoice_id'] ?>';
        if(status == 'add' && creditNote_id!=""){
            add_new_payment_new(customer_id,creditNote_id,total_amount);
        }
        else if(status == 'add'){
            add_new_payment_new(customer_id);
        }
//        else if(status == 'add' && customer_id!=""){
//            load_custoner__(customer_id);
//        }
        else if(invoice_id!=""){
            load_payment(invoice_id, customer_id);
        }      
    });
</script>