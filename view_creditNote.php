<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

//require_once (DP_BASE_DIR."/modules/sales/CCreditNoteManager.php");
require_once (DP_BASE_DIR."/modules/sales/creditNote.js.php");
echo "<div id='div_creditNote'>";
    $credit_status = array('' => 'All');
    $credit_status += dPgetSysVal('CreditStatus');
    $credit_status_dropdown = arraySelect($credit_status, 'invoice_status_id', 'id="invoice_status_id" onchange="load_creditNote_status(this.value); return false;" class="text" size="1"', 3, true);
    $btnew = '<button class="ui-button ui-state-default ui-corner-all" onclick="load_creditNote(\'\',\'add\')"><img border="0" src="images/icons/plus.png">New Credit Note</button>';
    echo $new_credit_block = $AppUI->createBlock('button_new',$btnew.$credit_status_dropdown,'');
    echo '<div id="div_list_creditNote">';
    echo '</div>';
echo "</div>";
?>
<script>
    $(document).ready(function(){
        load_list_creditNote();
        var status = '<?php echo $_REQUEST['status']; ?>';
        var invoice_id = '<?php echo  $_REQUEST['invoice_id']; ?>';
        var customer_id = '<?php echo $_REQUEST['customer_id']; ?>';
        var address_id = '<?php echo $_REQUEST['address_id']; ?>';
        var action = '<?php echo $_REQUEST['action']; ?>';
        var credit_id = '<?php echo $_REQUEST['creditNote_id'] ?>';
        if(status!="" && invoice_id!=""){
            $('#div_creditNote').html("Loading...");
            $('#div_creditNote').load('?m=sales&a=vw_creditNote&c=view_creditNote_detail&suppressHeaders=true',{customer_id:customer_id,address_id:address_id,invoice_id:invoice_id,status:status,action:action});
        }
        if(credit_id!="" && status!="")
            load_creditNote(credit_id,status);
    });
</script>