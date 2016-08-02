<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR."/modules/clients/company_manager.class.php");
require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");
require_once DP_BASE_DIR."/modules/sales/CPaymentManager.php";

$Cpayment = new CPaymentManager();

function __default() {
    global $AppUI;
    echo '<div id="btn-back">';
        back();
    echo '</div>';
    show_payment_schedule();
   }


/*
 * view Payment
 */
function show_payment_schedule() {
    global $AppUI; global $Cpayment;

    $invoice_id = dPgetCleanParam($_GET, 'invoice_id', 0);
    $invoice_revision = $Cpayment->loadInvoiceRevision($invoice_id);
    foreach ($invoice_revision as $revision) {
        $invoice_revision_id = $revision['invoice_revision_id'];
    }
    
    echo '<div id="payment-schedule-detail">';
      echo '<div id="schedule_detail">';
        echo '<div id="div_title">';
                _infor_form();
        echo '</div>';
        echo '<div id="div_infor" style="width: 400px;">';
                _infor_invoice($invoice_id);
        echo '</div><br/>';
        echo '<div id="div_data" style="width: 100%;">';
                _data_form($invoice_revision_id);
        echo '</div>';
        
        $delete_id = '<a id="del" name="del"><img onclick="delete_pay('.$invoice_id.');" style="border: medium none; cursor:pointer" src="modules/leave/images/delete.png"> delete </a>';
        echo $block_del = $AppUI ->createBlock('del_payment',$delete_id,'style="text-align: right"');
        
        echo '<div id="div_total" style="width: 400px;">';
                _payment_total($invoice_id);
        echo '</div><br/>';

        echo '<div id="history_title" style="width: 800px;">';
            _history_title();
        echo '</div>';
        echo '<div id="payment-history-data" style="float: left; width: 800px;">';
            _payment_history($invoice_id);
        echo '</div>';

       echo '</div>';
    echo '</div>';
}
function _infor_form() {

    global $AppUI;
    echo $tittle_block = $AppUI ->createBlock('div_details','<h1>Payment Details</h1>','style="text-align: center;text-font:bold; float: left; margin-right : 10 px"');
    echo '<br><div style="width:100%; height: 5px; align: left; background-color: #E2E2E2"></div><br>';
    echo $tittle_block = $AppUI ->createBlock('div_invoice','','style="float: left; margin-left : 40%"');

}

function _infor_invoice($invoice_id) {
    global $AppUI; global $Cpayment;
    
        $invoice_arr = $Cpayment->loadInvoiceRevision($invoice_id);
        //print_r($invoice_arr);
        foreach ($invoice_arr as $invoice) {
                $invoice_no = '<p><b>Invoice No: </b>'.$invoice['invoice_no'].'</p>';
                $client = '<p><b>Client: </b>'.$invoice['company_name'].'</p>';
        }
                $invoice_amount = '<p><b>Invoice Amount: </b>$'.$invoice['invoice_revision_currency'].'.00</p>';

   echo $div_supplier = $AppUI ->createBlock('div_logo_supplier', $invoice_no . $client . $invoice_amount, 'style="float: left; margin-right : 40%; width: 600px;"');
   echo '<br/>' ;
}

function _data_form($invoice_revision_id) {

    global $AppUI; global $Cpayment;

    $method = dPgetSysVal('PaymentMethod');
    
    $payment = $Cpayment->list_db_payment($invoice_revision_id);

        $dataTableRaw = new JDataTable('sales_payment_table');
        $dataTableRaw->setWidth('100%');
        $dataTableRaw->setHeaders(array('<input type="checkbox" id="check_all" onclick="check_all(\'sales_payment_table\');">','Date', 'Type', 'Amount', 'Notes'));


        $colAttributes = array(
                'class="checkbox" width="5%" align="center"',
                'class="p_d" align="center" width="20%"',
                'class="p_a" width="20%"',
                'class="p" width="20%"',
                'class="p_n" align="center" width="30%"',
            );
        $rowIds = array();
        $tableData = array();
        if (count($payment) > 0 ) {
            $i = 1;
            foreach ($payment as $d_row) {
                $tableData[]= array(
                    '<input type="hidden" id="checkbox_total" value="'.count($payment).'" /><span id="check_'.$i.'"><input type="checkbox" id="chk" name="chk[]" value="'.$d_row['payment_id'].'"></span>',
                    $d_row['payment_date'],
                    $method[$d_row['payment_method']],'$'.
                    $d_row['payment_amount'].'.00',
                    $d_row['payment_notes'],
                );
                $rowIds[] = $d_row['payment_id'];
                $i++;
            }
        }
        $dataTableRaw->setDataRow($tableData, $colAttributes, $rowIds);
      
        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
        $dataTableRaw->show();
}
function _payment_total($invoice_id) {
    global $AppUI;    global $Cpayment;
    $invoice_arr = $Cpayment->loadInvoiceRevision($invoice_id);
    foreach ($invoice_arr as $invoice) {
        $invoice_revision_id = $invoice['invoice_revision_id'];
        $invoice_total = $invoice['invoice_revision_currency'];
    }
    
    $payment = $Cpayment->list_db_payment($invoice_revision_id);
    $total = array();
    foreach ($payment as $row) {
        $total[] = $row['payment_amount'];
    }
    $sum_total = array_sum($total);
    $bland = $sum_total - $invoice_total;
    $total = '<p><b>Total</b>: $'.$sum_total.'.00</p>';
    $bland = '<p><b>Balance</b>: $ '.$bland.'.00</p>';

    echo $div_total = $AppUI ->createBlock('div_total', $total . $bland, 'style="float: left; margin-right : 40%; width: 400px;"');
}
function _payment_history($invoice_id) {

    global $AppUI; global $Cpayment;

    $payment_history = $Cpayment->loadInvoiceRevision($invoice_id);
    
    $dataTableRaw = new JDataTable('payment_table');
        $dataTableRaw->setWidth('100%');
        $dataTableRaw->setHeaders(array('Invoice No', 'Client Name', 'Amount', 'Notes'));

        $colAttributes = array('class="checkbox" width="5%" align="center"', 'class="invoice_no" align="center" width="10%"', 'class="client_invoice" width="20%"','class="invoice_des" width="20%"','class="invoice_date" align="center" width="13%"','class="invoice_total" align="right" width="13%"','class="invoice_status" align="center" width="13%"',);

        $tableData = array();
        if (count($payment_history) > 0 ) {
            foreach ($payment_history as $pay) {
                $tableData[]= array(
                    $pay['invoice_no'],
                    //$pay['invoice_revision_date'],
                    $pay['company_name'],'$'.
                    $pay['invoice_revision_currency'].'.00',
                    $pay['invoice_revision_notes'],
                );
            }
        }
        $dataTableRaw->setDataRow($tableData, $colAttributes);

        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
        $dataTableRaw->show();
}

function _history_title() {
    global $AppUI;
    echo $tittle_block = $AppUI ->createBlock('div_title','<h1>Payment History</h1>','style="text-align: center;text-font:bold; float: left; margin-left : 10 px"');
    echo '<br><div style="width:100%; height: 5px; float: right; background-color: #E2E2E2"></div><br>';
    echo $tittle_block = $AppUI ->createBlock('div_invoice','','style="float: left; margin-left : 40%"');
}

/*function show_method_name() {
    global $AppUI;
    global $Cpayment;
    
    $method_name = $Cpayment->show_method();
    
    $sys_method = dPgetSysVal('PaymentMethod');
    echo $sys_method[$method_name];
}*/

function back() {
    global $AppUI;
    $new_block = $AppUI ->createBlock('button_back','<button class="ui-button ui-state-default ui-corner-all"  onclick="javascript:history.back();">Back</button>&nbsp;&nbsp;','style="text-align: center; float: left; margin-right : 10 px"');
     echo $button_block = $AppUI ->createBlock('button_block',$new_block,'style="text-align: center; float: left"');
    echo '<br><br>';
}

function update_field() {
    
require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");

    $id = dPgetCleanParam($_POST, 'id');
    $updated_content = dPgetCleanParam($_POST, 'value');

    $col_name = dPgetCleanParam($_GET, 'col_name', '');

    $myObject = new CPayment();
    $myObject->load($id);
    if ($col_name != '') {
        $myObject->$col_name = $updated_content;

        if (($msg = $myObject->store())) {

        } else {
            echo $updated_content;
        }
    }
}
function delete_payment() {
    global $AppUI;    global $Cpayment;
    $payment_id = $_GET['chk'];
    //for ($i = 0; $i < count($payment_id); $i++) {
        $msg = $Cpayment->remove_payment($payment_id);
    //}
    if (!$msg)
        return '{"status": "success"}';
    else {
        return '{"status": "Failure", "message": "khong the xoa payment"}';
    }
    
}

?>
<script language="javascript">
   
    
    $(document).ready(function() {
       $('.p_d').editable('?m=sales&a=vw_view_payment_details&c=update_field&suppressHeaders=true&col_name=payment_date', {
            indicator    : "Loading...",
            style: 'display: inline',
            submit: "Save",
            width: 200,
            height: 20,
            cancel    : 'Cancel'
       });
       $(".p_d").click(function() {
            bindDatesPicker('jeditDatePicker');
       });
       <?php
            $payment_method = dPgetSysVal('PaymentMethod');
       ?>
       $('.p_a').editable('?m=sales&a=vw_view_payment_details&c=update_field&suppressHeaders=true&col_name=payment_method', {
            data: '<?php print json_encode($payment_method); ?>',
            type: 'select',
            submit: "Save",
            cancel: 'cancel'
       });
       $('.p').editable('?m=sales&a=vw_view_payment_details&c=update_field&suppressHeaders=true&col_name=payment_amount', {
            type: 'text',
            submit: "Save",
            cancel: 'cancel'
       });
       $('.p_n').editable('?m=sales&a=vw_view_payment_details&c=update_field&suppressHeaders=true&col_name=payment_notes', {
            type: 'text',
            width: 200,
            submit: "Save",
            cancel: 'cancel'
       });
    });
    
    function bindDatesPicker(id) {
        var disp = id;
        $('#'+disp).datepicker({
                showOn: 'button', buttonImage: 'images/calendar.gif', buttonImageOnly: true,
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
        });
    }
    
   function delete_pay(invoice_id) {
       url = '?m=sales&a=vw_view_payment_details&c=delete_payment&suppressHeaders=true';
       
       var checked = [];
       $("input[name='chk[]']:checked").each(function ()
       {
            checked.push(parseInt($(this).val()));
       });
        if (checked != '') {
            if(confirm('<?php echo $AppUI->_('Are you sure delete this payment?'); ?>')) {
                    $.getJSON(url, { chk: checked },
                    function(data) {
                            alert(data);
                        if(data.status == 'success') {
                            load_invoice_payment();
                        } else if(data.status == 'Failure') {
                            alert(data.message);
                        }
                    }
                )};
        } else {
            alert('<?php echo $AppUI->_('Please choose payment.') ; ?>');
        }
   }
</script>
