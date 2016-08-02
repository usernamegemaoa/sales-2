<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR."/modules/clients/company_manager.class.php");
require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");
require_once (DP_BASE_DIR."/modules/sales/sales.js.php");

$Cpayment = new CPaymentManager();

function __default() {
    global $AppUI;
    echo '<div id="div_details_payment">';
    $new_block = $AppUI ->createBlock('button_new','<button class="ui-button ui-state-default ui-corner-all"  onclick="NewPaymentList()"><img border="0" src="images/icons/plus.png">New Payments</button>&nbsp;&nbsp;','style="text-align: center; float: left; margin-right : 10 px"');
     echo $button_block = $AppUI ->createBlock('button_block',$new_block.$print_block.$email_block,'style="text-align: center; float: left"');
    echo '<br><br>';

   $quotation_stt = dPgetSysVal('InvoiceStatus');
    $quotation_stt_dropdown = arraySelect($quotation_stt,'quotation_status_id ', 'class=text size=1', 0, true);
    echo $stt_block = $AppUI->createBlock('stt-block', $quotation_stt_dropdown .'&nbsp;&nbsp;', 'style="float: left; margin-right : 10 px"');

    show_html_table();
    
    echo '</div>';
    echo '<div id="form-detail-payment">';
    echo '</div>';
   }

function show_html_table() {
    
 global $AppUI; global $Cpayment;
    echo $tittle_block = $AppUI ->createBlock('div_invoice','','style="float: left; margin-left : 40%"');

        $invoice_arr = $Cpayment->list_invoice_payment();
        $invoice_stt = dPgetSysVal('InvoiceStatus');

        $dataTableRaw = new JDataTable('sales_payment_table');
        $dataTableRaw->setWidth('100%');
        $dataTableRaw->setHeaders(array('<input type="checkbox" disabled="disabled">','Invoice No', 'Client', 'Description','Date','Total','Status'));

        $colAttributes = array('class="checkbox" width="5%" align="center"', 'class="invoice_no" align="center" width="10%"', 'class="client_invoice" width="20%"','class="invoice_des" width="20%"','class="invoice_date" align="center" width="13%"','class="invoice_total" align="right" width="13%"','class="invoice_status" align="center" width="13%"',);

        $tableData = array();
        if (count($invoice_arr) > 0 ) {
            foreach ($invoice_arr as $invoice) {
                $tableData[]= array(
                    '<input type="checkbox" name="chk[]" id="chk" value="'.$invoice['invoice_id'].'">',
                    '<a onclick="load_payment_details('.$invoice['invoice_id'].')" style="cursor:pointer;">'.$invoice['invoice_no'].'</a>',
                   // '<a onclick="$(\'#'.$_form_id.'\').dialog(\'open\');" style="cursor:pointer;">'.$invoice['invoice_no'].'</a>',
                    $invoice['company_name'],
                    $invoice['invoice_internal_notes'],
                    $invoice['invoice_date'],'$'.
                    $invoice['invoice_revision_currency'].'.00',
                    $invoice_stt[$invoice['invoice_status']]
                );
                //$rowIds[] = $invoice['invoice_id'];
            }
        }
        $dataTableRaw->setDataRow($tableData, $colAttributes);
       
        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
        $dataTableRaw->show();
    echo '<br>';
}


function _action(){
    $left_block = $AppUI->createBlock('left-block', 'Learing English as a second language, Will need more peole who can speaking English,
        Why do we need to learn English,
        Of course one of the reason,
        practice and confident,Please  i can do it! ','style="float: left"');
    $big_block = $AppUI->createBlock('my-block', $left_block . $right_block);
    echo  $left_block ;
}
?>
<script language="javascript">
    
    
    function load_payment_details(invoice_id) {
            $('#form-detail-payment').html('Loading...');
            $('#form-detail-payment').load('?m=sales&a=vw_view_payment_details&suppressHeaders=true&invoice_id='+invoice_id);
            $('#div_details_payment').html('');
    }
   
</script>
