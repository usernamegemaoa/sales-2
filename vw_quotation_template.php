

<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
global  $AppUI, $m;

require_once (DP_BASE_DIR."/modules/clients/company_manager.class.php");

define(FORMAT_DATE_DEFAULT, 'd/m/Y');

require_once 'CSalesManager.php';
require_once (DP_BASE_DIR."/modules/sales/CQuotationManager.php");

$CSalesManager = new CSalesManager();
$CQuotationManager = new CQuotationManager();

$template = $_POST['template'];
$quotation_id = $_POST['quotation_id'];
$quotation_revision_id = $_POST['quotation_rev_id'];

function __default() {
            vw_quotation_html();
}
function vw_quotation_html() {
    
    $template = $_POST['template'];
    
    global $AppUI;
    echo '<div id="div_0">';
                _form_button($quotation_id=0, $quotation_revision_id=0, $template);
    echo '</div>';
    echo  _form_menu_tab();
    echo '<div id="tab_detail"></div>';
}
function _form_menu_tab() {
    
$template = $_POST['template'];
$quotation_id = $_POST['quotation_id'];
$quotation_revision_id = $_POST['quotation_rev_id'];

   echo '<div id="navcontainer">
           <ul id="navlist">
            <li class="active"><a href="#" onclick="loadtab_sumary(); return false;">Summary</a></li>
            <li><a href="#" onclick="loadTab_item(); return false;">Items</a></li>
            <li><a href="#" onclick="loadtab_note(); return false;">Notes</a></li>
            <li><a href="#" onclick="loadtab_term(); return false;">Term and Codition</a></li>

        </ul>
    </div>
    <input type="hidden" name="quotation_id" id="quotation_id" value="'.$quotation_id.'"/>
    <input type="hidden" name="quotation_revision_id" id="quotation_revision_id" value="'.$quotation_revision_id.'"/>
    <input type="hidden" name="template" id="template" value="'.$template.'"/>
    ';
}
function _form_button($quotation_id = 0, $quotation_revision_id = 0, $template) {

      global $AppUI, $canDelete, $canAdd, $CQuotationManager;
      
      $status = $_POST['status_rev'];
      $quotation_id = $_POST['quotation_id'];
      $quotation_revision_id = $_POST['quotation_rev_id'];

        if ($quotation_id != 0 && $quotation_revision_id != 0 && $status = 'update') {
            $h1_title = 'Quotation Rev: ';
             $invoice_id = intval($CQuotationManager->get_invoice_id($quotation_id));
            
            if ($invoice_id != 0) {
                $view_invoice = '<button class="ui-button ui-state-default ui-corner-all" onclick="change_invoice_revision('. $invoice_id .', \''. $status .'\'); return false;">View Invoive</button>&nbsp';
            }
            $delete_id = '<a class="icon-delete icon-all" onclick="delete_quotation_revision('. $quotation_revision_id .'); return false;" href="#">Delete</a>';
            $convert_block ='<button class="ui-button ui-state-default ui-corner-all" onclick="convert_quotation_into_invoice('. $quotation_id .', '. $quotation_revision_id .'); return false;">Create Invoice</button>&nbsp';
            $change_rev_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="change_revision('. $quotation_id .', '. $quotation_revision_id .', \''. $status .'\'); return false;">Change Revision</button>&nbsp';
            $print_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="generate_print_quotation('. $quotation_id .', '. $quotation_revision_id .'); return false;">Print</button>&nbsp;';
            $email_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="popupSendEmail('. $quotation_id .', '. $quotation_revision_id .'); return false;">Email</button>&nbsp;';
            $copy_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="copy_quotation('. $quotation_id .', '. $quotation_revision_id .'); return false;">Copy</button>&nbsp;';
            $history_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="view_history('. $quotation_id .', '. $quotation_revision_id .'); return false;">History</button>&nbsp;';
        } else {
            $h1_title = 'New Quotation ';
        }

      echo $AppUI ->createBlock('div_title', '<h1 id="quotation_rev_label">'. $h1_title .'</h1>', 'style="float: left;"');
      
      $back_block = '<button id="div_details" class="ui-button ui-state-default ui-corner-all" onclick="back_list_quotation()">Back</button>&nbsp;';
      $save_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_all_quotation('. $quotation_id .', '. $quotation_revision_id .', \''. $status .'\', '.$template.'); return false;">Save All</button>&nbsp';
      echo $AppUI ->createBlock('btt_block', $delete_id . $back_block . $convert_block . $change_rev_block . $save_block . $copy_block . $print_block . $email_block . $history_block, 'style="text-align: center; float: right"');

}

?>

<script type="text/javascript">
    $(document).ready(function() {
    
        $("ul#topnav li").hover(function() { //Hover over event on list item
        $(this).css({ 'background' : '#1376c9 repeat-x'}); //Add background color and image on hovered list item
        $(this).find("span").show(); //Show the subnav
        } , function() { //on hover out...
        $(this).css({ 'background' : 'none'}); //Ditch the background
        $(this).find("span").hide(); //Hide the subnav
        });
        loadtab_sumary();
    });
 </script>
