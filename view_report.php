<?php
if(!defined('DP_BASE_DIR')){
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR."/modules/sales/css/report.css.php");
require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoice.php");
require_once (DP_BASE_DIR."/modules/sales/CReportManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");

$CSalesManager = new CSalesManager();
$CReportManager = new CReportManager();
$CInvoiceManager = new CInvoiceManager();
$CInvoiceManager = new CInvoiceManager();
$CPaymentManager = new CPaymentManager();
    global $AppUI,$m;
    include DP_BASE_DIR."/modules/sales/report.js.php";
    echo $aging_report_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="list_aging_report(); return false">Aging report</button>&nbsp;';
    echo $cash_receipt_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="list_cash_receipt(); return false">Cash receipt</button>&nbsp;';
    echo $Invoice_journal_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="list_invoice_juornal(); return false">Invoice journal</button>&nbsp;';
    echo $gstReport_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="list_gst_report();return false;">GST Report</button>&nbsp;';
    echo $salesReport = '<button class="ui-button ui-state-default ui-corner-all" onclick="list_sales_report();return false;">Sales Report</button>&nbsp;';
    echo $DP_info = '<button class="ui-button ui-state-default ui-corner-all" onclick="list_dpInfo_report();return false;">DP Info</button>&nbsp;';
    echo $statements= '<button class="ui-button ui-state-default ui-corner-all" onclick="list_statement_report();return false;" >Customer Statements</button>&nbsp;';
    echo $statements= '<button class="ui-button ui-state-default ui-corner-all" onclick="list_quo_statement_report();return false;">Quotation Statements</button>&nbsp;';
    echo $credit_note= '<button class="ui-button ui-state-default ui-corner-all" onclick="list_credit_note();return false;">Credit Note</button>&nbsp;';
    
//    echo $Profit= '<button class="ui-button ui-state-default ui-corner-all" style = "margin-top:10px;" onclick="list_profit();return false;">Profit & Loss Statement</button>&nbsp;';
//    echo $cash_flow_summary= '<button class="ui-button ui-state-default ui-corner-all" onclick="list_cash_flow_summary();return false;">Cash Flow Summary</button>&nbsp;';
    echo '<div id="aging_report"></div>';
?>
<script>
    //list_aging_report();
</script>
