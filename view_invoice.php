<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR."/modules/sales/css/quotation_main.css.php");
require_once (DP_BASE_DIR."/modules/sales/css/invoice_main.css.php");
require_once (DP_BASE_DIR."/modules/sales/invoice.js.php");
//require_once (DP_BASE_DIR."/modules/sales/quotation.js.php");
//require_once (DP_BASE_DIR . '/modules/engagements/engagementManager.class.php');

$perms =& $AppUI->acl();
$addView_Invoice = true;
//$canView_invoice = $perms->checkModule( 'sales_invoice', 'view');
$addView_Invoice = $perms->checkModule( 'sales_invoice', 'add');
//$editView_Invoice = $perms->checkModule( 'sales_invoice', 'edit');
//$deleteView_Invoice = $perms->checkModule( 'sales_invoice', 'delete');

//echo "12312";
//$a = db_loadList("SELECT * FROM sales_invoice  WHERE invoice_id = 56");
//print_r($a);

$status_id = 4;
if(isset($_GET['s']))
    $status_id = $_GET['s'];
echo '<div id="div_invoice">';
    $invoice_stt = array('' => 'All');
    $invoice_stt += dPgetSysVal('InvoiceStatus');
    $invoice_stt_dropdown = "Status: ".arraySelect($invoice_stt, 'invoice_status_id', 'id="invoice_status_id" class="text" size="1"', $status_id, true);
    $invoice_stt_dropdown.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if($addView_Invoice)
        $btn_new = '<button class="ui-button ui-state-default ui-corner-all" onclick="load_invoice(0, 0, \'add\')"><img border="0" src="images/icons/plus.png">New Invoice</button>&nbsp;&nbsp;';
    
//    $CContract = new EngagementManager();
//    // Lay tat cac cac contract trong he thong
//    $contract_arr = $CContract->getEngagements();
    
    $contract_dropdown = "Contract no: "."<input class='text' type='text name='search-invoice-by-contract' id='search-invoice-by-contract'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    //$btn_search = '<button class="ui-button ui-state-default ui-corner-all" onclick="searchInvoiceBycontract();return false">Search</button>';
    
//    foreach ($contract_arr as $contract_item) {
//        $contract_dropdown.="<option value='".$contract_item['engagement_id']."'>".$contract_item['engagement_code']."</option>";
//    }
//    $contract_dropdown.="</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    $search_invoice_no = 'Invoice No: <input class="text" type="text" name="search-invoice-no" id="search-invoice-no"  value="" />&nbsp;&nbsp;&nbsp;';
    $search = '<button class="ui-button ui-state-default ui-corner-all" onclick="search_invoice()">Search</button>';
    echo $new_invoice_block = $AppUI->createBlock('button_new', $btn_new . $search_invoice_no . $invoice_stt_dropdown . $contract_dropdown .$btn_search.$search, 'style="padding-bottom:10px;"');
    
    //List Invoice
    echo '<div id="div-list-invoice" style="overflow: auto;">';
    echo '</div>';
echo "</div>";


?>
<script>
        $(document).ready(function() {
//                $('#search-invoice-by-contract').select2({
//                     placeholder: "Select a contract no."
//                });
//                $('#s2id_search-invoice-by-contract').css({'width':'400px'});
                var status_rev = '<?php echo $_GET['status_rev']; ?>';
                var invoice_id = '<?php echo $_GET['invoice_id']; ?>';
                var s = <?php if(isset($_GET['s'])) echo $_GET['s'];
                            else echo "4"; ?>;
                var invoice_revision_id = '<?php echo $_GET['invoice_revision_id']; ?>';
 //               list_invoice('',s,status_rev,invoice_id,invoice_revision_id);
                if(status_rev!="" && invoice_id!="" && invoice_revision_id!="")
                    load_invoice(invoice_id,invoice_revision_id,status_rev);
    
	});
</script>