<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CTemplateManager.php");
require_once (DP_BASE_DIR."/modules/system/roles/roles.class.php");
require_once (DP_BASE_DIR . "/modules/contacts/contacts_manager.class.php");
require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
require_once (DP_BASE_DIR. '/modules/sales/CMemoManager.php');
require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
require_once(DP_BASE_DIR."/modules/clients/company_manager.class.php");
require_once (DP_BASE_DIR . "/modules/serviceOrders/ServiceOrderManager.php");
require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
require_once (DP_BASE_DIR . '/modules/engagements/engagementManager.class.php');
require_once (DP_BASE_DIR . '/modules/sales/CContractInvoice.php');
require_once (DP_BASE_DIR."/modules/sales/CCreditNoteManager.php");
require_once(DP_BASE_DIR . '/modules/departments/CDepartment.php');
$CDepManager = new CDepartment();
$CTemplatePDF = new CTemplatePDF();
//ocio_config
$CSalesManager = new CSalesManager();
$CInvoiceManager = new CInvoiceManager();
$CTemplateManager = new CTemplateManage();
$CContract = new EngagementManager();
$CContractInvoice = new CContractInvoice();
$CCreditManager = new CCreditNoteManager();

$perms =& $AppUI->acl();
$canAdd = $perms->checkModule( $m, 'add');
$canEdit = $perms->checkModule( $m, 'edit');
$canDelete = $perms->checkModule( $m, 'delete');
$canView = $perms->checkModule( $m, 'view');

$canView_invoice =1;
$addView_Invoice = 1;
$editView_Invoice = 1;
$deleteView_Invoice = 1;

$addView_payment = $perms->checkModule( 'sales_payment', 'add');
$addView_creditNote = $perms->checkModule( 'sales_creditNote', 'add');

$canAccess_invoice = $perms->checkModule( 'sales_invoice', 'access');
$addView_Invoice = $perms->checkModule( 'sales_invoice', 'add');
$editView_Invoice = $perms->checkModule( 'sales_invoice', 'edit');
$deleteView_Invoice = $perms->checkModule( 'sales_invoice', 'delete');

//$addView_payment = $perms->checkModule('sales_payment', 'add');

$crole = new CRole();
$ContactManager = new ContactManager();
$cconfig = new CConfigNew();
$CMemoManager = new CMemoManager();
$CCompany = new CompanyManager();
$CSeviceOder = new ServiceOrderManager();


function list_invoice_html($click_tab = false) {

        global $AppUI, $CInvoiceManager,$CSalesManager, $canDelete, $canAccess_invoice, $editView_Invoice, $deleteView_Invoice, $CCreditManager;

        include DP_BASE_DIR."/modules/sales/css/invoice_main.css.php";
        include DP_BASE_DIR."/modules/sales/datatable.js.php";
        include DP_BASE_DIR."/modules/sales/invoice.js.php";
        
//        $a = $CInvoiceManager->getInvoicePaymentByContact(96);
//        print_r($a);
        
        if ($click_tab == true) {
            $status_id = 2;
            $customer_id = null;
        } else {
            $status_id = $_POST['status_id'];
            $customer_id = $_POST['customer_id'];
        }
        $invoice_no = false;
        if(isset($_POST['invoice_no']))
            $invoice_no = $_POST['invoice_no'];
        
        $contract_no=false;
        if(isset($_POST['contract_no']) && $_POST['contract_no']!='null')
            $contract_no = $_POST['contract_no'];
        
        //$invoice_arr = $CInvoiceManager->list_invoice($customer_id, $status_id, false, false, false, false, $contract_no,$invoice_no);

        $invoice_stt = dPgetSysVal('InvoiceStatus');
        
        $table = '<table id="invoice_table" class="tbl" cellspacing="1" cellpadding="2" style="clear: both; width: 100%">'
                    . '<thead>'
                        . '<tr>'
                            . '<th><input name="select_all" id="select_all" type="checkbox" onclick="check_all(\'select_all\', \'check_list\');"></th>'
                            . '<th width="15%">Invoice No</th>'
                            . '<th width="27%">Client</th>'
                            . '<th width="10%">Credit Note</th>'
                            . '<th width="18%">Subject</th>'
                            . '<th width="10%">Your PO Number</th>'
                            . '<th width="8%">Date</th>'
                            . '<th width="9%" align="right">Total</th>'
                            . '<th width="10%" >Status</th>'
                        . '</tr>'
                    . '</thead>'
                . '</table>';
        echo $table;

//        $dataTableRaw = new JDataTable('invoice_table',array('sprocessing'=>true,'bServerSide'=>true,'sAjaxSource'=> "?m=sales&a=server_processing_invoice&"
//                                        . "customer_id=$customer_id&invoice_no=$invoice_no&status_id=$status_id&contract_no=$contract_no&suppressHeaders=true",
//                            "fnHeaderCallback"=> "function() {"
//                                    . "       $('.client_invoice').editable('?m=sales&a=vw_invoice&c=_do_update_invoice_field&suppressHeaders=true&col_name=customer_id', {
//                                            indicator    : 'Loading...',
//                                            width: 220,
//                                            loadurl : '?m=sales&a=vw_invoice&c=_get_customer_inline&suppressHeaders=true',
//                                            type: 'select',
//                                            submit: 'Save',
//                                            cancel: 'Cancel',
//                                            height: 20
//                                        });"
//                            . "}"
//            ));
//        $dataTableRaw->setWidth('100%');
//        $dataTableRaw->setHeaders(array('<input name="select_all" id="select_all" type="checkbox" onclick="check_all(\'select_all\', \'check_list\');">','Invoice No', 'Client','Credit Note', 'Subject','Your PO Number' , 'Date', 'Total', 'Status'));

//        $colAttributes = array(
//            'class="checkbox" width="4%" align="center"',
//            'class="invoice_no" align="left" width="11%"',
//            'class="client_invoice1" width="23%"',
//            'class="client_invoice1" width="11%"',
//            'class="invoice_des" width="21%"',
//            'class="invoice_poNumber" width="12%"',
//            'class="invoice_date" align="center" width="8%"',
//            'class="invoice_total" align="right" width="8%"',
//            'class="invoice_status" align="center" width="7%"',);

        //print_r($invoice_arr);
        //print_rd($invoice_arr);
//        $tableData = array(); $rowIds = array();
//        if (count($invoice_arr) > 0 ) {
//            foreach ($invoice_arr as $invoice) {
//                $invoice_id = $invoice['invoice_id'];
//                $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
//                $total_show=$CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
//                
//                  // get joblocation
//                $job_location_arr =  $CSalesManager->get_address_by_id($invoice['job_location_id']);
//                    $brand="";
//                if($job_location_arr['address_branch_name']!="")
//                    $brand=$job_location_arr['address_branch_name'].' - ';
//                $address_2="";
//                if($job_location_arr['address_street_address_2']!="")
//                    $address_2= ', '.$job_location_arr['address_street_address_2'];
//                $postal_code_job = '';
//                if($job_location_arr['address_postal_zip_code']!="")
//                    $postal_code_job .=', Singapore '.$job_location_arr['address_postal_zip_code'];
//                $job_location = "";
//                if($job_location_arr!=""){
//                        $job_location.=$brand.$CSalesManager->htmlChars($job_location_arr['address_street_address_1'].$address_2.$postal_code_job);    
//                }
//                
//                //Lấy credinote được áp dụng cho invoice//
//                $credit_arr = $CCreditManager->getCreditNoteByInvoice($invoice_id);
//                $credit_no_arr = array();
//                foreach ($credit_arr as $credit_item) {
//                    $credit_no_arr[]='<a href="?m=sales&show_all=1&tab=2&creditNote_id='.$credit_item['credit_note_id'].'&status=update">'.$credit_item['credit_note_no'].'</a>';
//                }
                //END
                
//                $tableData[]= array(
//                    '<input type="checkbox" id="check_list" name="check_list" value="'.$invoice_id.'">
//                     <input id="invoice_status_'.$invoice_id.'" type="hidden" value="'.$invoice['invoice_status'].'" name="invoice_status_'.$invoice_id.'">
//                    ',
//                    '<span onmouseover="load_invocie_rev('.$invoice_id.'); return false;" onclick="invoice_rev_more('.$invoice_id.');" id="invoice-rev-more-'.$invoice_id.'" class="invoice-rev-more" title="Invoice rev more..">[+]</span>
//                        <a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_revision_id.'" onclick="load_invoice('. $invoice_id .', '. $invoice_revision_id .', \'update\'); return false;">'. $invoice['invoice_no'].'</a>
//                        <!-- <a id="a_view_payment" href="#" style="float: right;" onclick="load_payment('. $invoice_id .', '. $invoice['customer_id'] .'); return false;">View payment</a> --> 
//                        <div class="div-invoice-rev" id="div-invoice-rev-'.$invoice_id.'" style="display: none"></div>    ',
//                    '<div class="client_invoice" style="font-weight:bold;" id="'.$invoice_id.'">'.$invoice['company_name'].'.</div>
//                    <div style="font-size:0.9em;color:#666;">'.$job_location.'</div>',
//                    implode(',',$credit_no_arr),
//                    $CSalesManager->htmlChars($invoice['invoice_subject']),
//                    $CSalesManager->htmlChars($invoice['po_number']),
//                    $invoice['invoice_date'],
//                    '$'. number_format($CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, $total_show),2),
//                    $invoice_stt[$invoice['invoice_status']]
//                );
                //$rowIds[] = $invoice_id;
            //}
        //}

        //$dataTableRaw->setDataRow($tableData, $colAttributes, $rowIds);

//        if($editView_Invoice)
//        {
//            //$invoice_stt['selected'];
//            $dataTableRaw->setJEditable(
//                    'invoice_status',
//                    '?m=sales&a=vw_invoice&c=_do_update_invoice_field&suppressHeaders=true&col_name=invoice_status&status='.$invoice['invoice_status'],
//                    array(
//                        'data' => json_encode($invoice_stt),
//                        'type' => 'select',
//                        'cancel' => 'Cancel',
//                        'submit' => 'Save',
//                        'indicator' => 'Loading...',
//                    )
//            );
//            $dataTableRaw->setJEditable(
//                    'invoice_des',
//                    '?m=sales&a=vw_invoice&c=_do_update_invoice_field&suppressHeaders=true&col_name=invoice_subject',
//                    array(
//                        'type' => 'text',
//                        'cancel' => 'Cancel',
//                        'submit' => 'Save',
//                        'indicator' => 'Loading...',
//                    )
//            );
//            $dataTableRaw->setJEditable(
//                    'invoice_date',
//                    '?m=sales&a=vw_invoice&c=_do_update_invoice_field&suppressHeaders=true&col_name=invoice_date',
//                    array(
//                        'value' => 'ffsfrom_date',
//                        'class' => 'ffsfrom_date',
//                        'name' => 'ddfrom_date',
//                        'cancel' => 'Cancel',
//                        'submit' => 'Save',
//                        'indicator' => 'Loading...',
//                    )
//            );
//        
//            $dataTableRaw->setJEditable(
//                    'client_invoice',
//                    '?m=sales&a=vw_invoice&c=_do_update_invoice_field&suppressHeaders=true&col_name=customer_id',
//                    array(
//                        'loadurl' => '?m=sales&a=vw_invoice&c=_get_customer_inline&suppressHeaders=true',
//                        'type' => 'select',
//                        'cancel' => 'Cancel',
//                        'submit' => 'Save',
//                        'indicator' => 'Loading...',
//                    )
//            );
//        }
     
//        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
//        if($canAccess_invoice)
//            $dataTableRaw->show();
        
        if ($deleteView_Invoice) 
            $delete_id = '<a class="icon-delete icon-all" onclick="delete_invoice(0, 0); return false;" href="#">Delete</a>';
       // $email_id = '<a class="icon-all" onclick="email_invoice(0, 0); return false;" href="#"> Email</a>';
        $print_id = '<a class="icon-all" onclick="generate_print_invoice(0, 0); return false;" href="#"> Print to PDF</a>';
        echo $block_del = $AppUI ->createBlock('del_block', $delete_id .$print_id, 'style="text-align: left; clear: both;"');
        echo '<br/><div id="detail_payment_inside" style="overflow: auto;"></div>';
        


}


//////////////////////////////////////////////////////////
/*
 * Bat dau view invoice details
 */
//////////////////////////////////////////////////////////

function view_invoice_detail() {

    global $AppUI,$baseUrl;
    
    // Luu lai status_id thuc hien nut back

    include DP_BASE_DIR."/modules/sales/invoice.js.php";

    $invoice_id = $_REQUEST['invoice_id'];
    echo '<input type="text" value='.$invoice_id.' id="invoice_id_hd" hidden="true" />';
    $invoice_revision_id = $_REQUEST['invoice_revision_id'];
    $status_id = $_POST['status_id'];
    $filter_seacrch = "";
    if($_POST['filter'])
    {
        $filter_seacrch = $_POST['filter'];
    }


    include DP_BASE_DIR."/modules/sales/css/invoice.css.php";
    
    echo '<form id="frm_all_invoice" method="post">';
    //echo '<div id="div_invoice">';
        echo '<div id="div_inv_details" style="width:1055px;margin:0px 0px -20px 0px;">';
            
            echo '<div id="div_0">';
                _form_button($invoice_id, $invoice_revision_id,false, $status_id);
            echo '</div>';

            echo '<div id="div_info">';
                _form_info($invoice_id, $invoice_revision_id);
            echo '</div>';

            echo '<div id="div_inv_3">';
                show_html_table($invoice_id, $invoice_revision_id);
            echo '</div>';

            echo '<div id="div_inv_total_note">';
                _form_total_and_note($invoice_id, $invoice_revision_id);
            echo '</div>';
            
            echo '<div id="div_inv_note">';
                _form_inv_note($invoice_id, $invoice_revision_id);
            echo '</div>';

        echo '</div>';
    //echo '</div>';
        echo '<br/><div id="detail_payment_inside" style="overflow: auto;"></div>';
    echo '</form>';
    echo '<div id="frm_add_brand_new_contact" style="display: none;" align="center"></div>';
    echo '<input type="hidden" value="'.$filter_seacrch.'" id="filter_search" />';
    echo '<div  class="block_loading loading_active" ></div>';
    echo '<div class="block_loading_content loading_active"><img width="40" src="'.$baseUrl.'/images/ajax-loader.gif" /></div>';
}

function _form_button($invoice_id = 0, $invoice_revision_id = 0, $customer_id = null,$status_id=false) {

      global $AppUI, $canDelete, $canAdd, $CInvoiceManager,$addView_Invoice,$editView_Invoice,$deleteView_Invoice,$addView_creditNote,$addView_payment;    
      include DP_BASE_DIR."/modules/sales/css/invoice_main.css.php";
      //include DP_BASE_DIR."/modules/sales/invoice.js.php";
      //include DP_BASE_DIR."/modules/sales/payment.js.php";
      
//      echo $invoice_id.",";
      $invoice_arr = $CInvoiceManager->get_db_invoice($invoice_id);
      //print_r($invoice_arr);
      $invoice_no = $invoice_arr[0][invoice_no];
      
      $prev_invoice_id=$CInvoiceManager->moveInvoice($invoice_no,"prev");
      $prev_rev_invoice_id = $CInvoiceManager->get_latest_invoice_revision($prev_invoice_id[0]['invoice_id']);
      $next_invoice_id=$CInvoiceManager->moveInvoice($invoice_no,"next");
      $next_rev_invoice_id = $CInvoiceManager->get_latest_invoice_revision($next_invoice_id[0]['invoice_id']);
      $first_invoice_id=$CInvoiceManager->moveInvoice($invoice_no,"first");
      $first_rev_invoice_id = $CInvoiceManager->get_latest_invoice_revision($first_invoice_id[0]['invoice_id']);
      $last_invoice_id=$CInvoiceManager->moveInvoice($invoice_no,"last");
      $last_rev_invoice_id = $CInvoiceManager->get_latest_invoice_revision($last_invoice_id[0]['invoice_id']);
      
      $disabled_prev ="";
      //echo $invoice_id.",";
      //echo $prev_invoice_id[0]['invoice_id'];
      if($prev_invoice_id[0]['invoice_id']==""){
          $disabled_prev = 'disabled = "true"';
          $opacity_prev = 'style="opacity : 0.25"';
      }
      $disabled_next ="";
      if($next_invoice_id[0]['invoice_id']==""){
          $disabled_next = 'disabled = "true"';
          $opacity_next = 'style="opacity : 0.25"';
      }
      if($_POST['status_id'])
        $status_id = $_POST['status_id'];
      else if(!$status_id)
          $status_id = "";
          
      $customer_id = $_POST['customer_id'];
      
      $countRev_arr = $CInvoiceManager->list_invoice_revision($invoice_id);
      $countRev = count($countRev_arr);
      echo '<input type="hidden" id="countRev" value="'.$countRev.'"/>';
      $status = $_POST['status_rev'];
      $invoice_arr = CInvoiceManager::list_invoice($customer_id, $status_id);      
      if (count($invoice_arr) > 0 ) {
        foreach ($invoice_arr as $invoice) {
            $customer = $invoice['customer_id'];
            //$payment_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="list_payment(); return false;">Payment</button>&nbsp';
        }
      }
//        print_r($customer_id);
//        exit();
      $invoice_details_arr = $CInvoiceManager->get_db_invoice($invoice_id);
      //print_r($invoice_details_arr);
      if ($invoice_id != 0 && $invoice_revision_id != 0 && $status = 'update') {
          if($invoice_details_arr[0]['invoice_no']=='0'){
              $h1_title = 'Draft';
          }else
            $h1_title = 'Invoice Rev: ';
//         if ($canDelete) {
//         //echo $canDelete;
//            $delete_id = '<a class="icon-delete icon-all" onclick="delete_invoice_revision('. $invoice_id .', '. $invoice_revision_id .'); return false;" href="#">Delete</a>';
//         }
         $quotation_id = intval($CInvoiceManager->get_quotation_id($invoice_id));
         
         if ($quotation_id != 0) {
            $view_quotation = '<button class="ui-button ui-state-default ui-corner-all" onclick="change_quotation_revision('. $quotation_id .', \''. $status .'\'); return false;">View Quotation</button>&nbsp';
         }
         
        if ($addView_Invoice) {
            $btn_new = '<button class="ui-button ui-state-default ui-corner-all" onclick="load_invoice(0, 0, \'add\'); return false;"><img border="0" src="images/icons/plus.png">New Invoice</button>&nbsp;';
        }
        if($addView_payment)
            $payment_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="load_payment_invoice(\''.$invoice_details_arr[0]['customer_id'].'\'); return false;">Enter Payment</button>&nbsp';
         
//            $change_rev_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="change_revision('. $invoice_id .', '. $invoice_revision_id .', \''. $status .'\'); return false;">Change Revision</button>&nbsp';
            $print_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="generate_print_invoice('. $invoice_id .', '. $invoice_revision_id .'); return false;">Print</button>&nbsp;';
//            $email_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="email_invoice('. $invoice_id .', '. $invoice_revision_id .'); return false;">Email</button>&nbsp;';
        if($editView_Invoice)
            $email_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="popupSendEmail('. $invoice_id .', '. $invoice_revision_id .'); return false;">Email</button>&nbsp;';
            //$history_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="view_history('. $invoice_id .', '. $invoice_revision_id .'); return false;">History</button>&nbsp;';
            if($invoice_arr[0]['invoice_no']!='Draft')
                if($editView_Invoice)
                    $save = '<button id="btn-save-all-revision" class="ui-button ui-state-default ui-corner-all" onclick="save_all_invoice('. $invoice_id .', '. $invoice_revision_id .', \''. $status .'\'); return false;">Save As New Revison</button>&nbsp';
            $print_preview = '<button class="ui-button ui-state-default ui-corner-all" onclick="print_invoice('. $invoice_id .', '. $invoice_revision_id .'); return false;">Print Preview</button>&nbsp;';
            
            if($deleteView_Invoice)
            {
                $delete_rev = '<option values="Delete")>Delete this revision</option>';
                $delete_inv = '<option values="Delete_inv")>Delete this Invoice</option>';
            }
            $action_invoice='<select class="text" name="action_invoice" onchange="load_action(this.value)">
                    <option values="" >Action</option>
                    <option values="Revision" >Change Revision</option>
                    <option values="history">History</option>
                    '.$delete_rev.'
                    '.$delete_inv.'
            </select>';
            if($addView_creditNote)
                $new_credit = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="load_creditNote('.$invoice_details_arr[0]['customer_id'].','.$invoice_details_arr[0]['address_id'].','.$invoice_id.');return false;"><img border="0" src="images/icons/plus.png">Credit Note</button> ';
      } else {
            $h1_title = 'New Invoice ';
            $use_template_block = '<button id="div_details" class="ui-button ui-state-default ui-corner-all"  onclick="load_template_invoice('.$invoice_id.', '.$invoice_revision_id.', \''. $status .'\'); return false;">Use Template</button>';
        }
     echo '<div id="loading"><!-- Place at bottom of page --></div>';   
      $firstPage = '<input type="image" '.$disabled_prev.' '.$opacity_prev.' onclick="load_invoice('.$first_invoice_id[0]['invoice_id'].','.$first_rev_invoice_id[0]['invoice_revision_id'].',\'update\'); return false;" class="first" alt="First" border="0" src="images/first.png" />&nbsp;';
      $prevPage = '<input type="image" '.$disabled_prev.' '.$opacity_prev.' onclick="load_invoice('.$prev_invoice_id[0]['invoice_id'].','.$prev_rev_invoice_id[0]['invoice_revision_id'].',\'update\');return false; " class="prev" alt="First" border="0" src="images/prev.png" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      $nextPage = '<input type="image" '.$disabled_next.' '.$opacity_next.' onclick="load_invoice('.$next_invoice_id[0]['invoice_id'].','.$next_rev_invoice_id[0]['invoice_revision_id'].',\'update\');return false;" class="next" alt="First" border="0" src="images/next.png" />&nbsp;';
      $lastPage = '<input type="image" '.$disabled_next.' '.$opacity_next.' onclick="load_invoice('.$last_invoice_id[0]['invoice_id'].','.$last_rev_invoice_id[0]['invoice_revision_id'].',\'update\');return false;" class="last" alt="First" border="0" src="images/last.png" />';
      if($status!="add")
        echo $AppUI->createBlock('invoicePage',$firstPage.$prevPage.$nextPage.$lastPage,'style="float:right;"');
      
      echo $AppUI ->createBlock('div_title', '<h1 id="invoice_rev_label">'. $h1_title .'</h1>', 'style="float: left;"');
      
      //$back_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="">Back</button>&nbsp;';
      if ($canAdd) {
//        $save_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_all_invoice('. $invoice_id .', '. $invoice_revision_id .', \''. $status .'\'); return false;">Save All</button>&nbsp';
            if($status == 'add' && $editView_Invoice) {
                $save_block = '<button id="btn-save-all-add" class="ui-button ui-state-default ui-corner-all" onclick="save_all_invoice('. $invoice_id .', '. $invoice_revision_id .', \''. $status .'\'); return false;">Save</button>&nbsp';
            } elseif($editView_Invoice) {
                $status = 'update_no_rev';
                $save_block = '<button id="btn-save-all-update" class="ui-button ui-state-default ui-corner-all" onclick="save_all_invoice('. $invoice_id .', '. $invoice_revision_id .', \''. $status .'\'); return false;">Save</button>&nbsp';
            }
      }
      $back_block = '<input type="button" value="Back" onclick="back_list_invoice('.$status_id.')" class="ui-button ui-state-default ui-corner-all" />';
      echo "<div id='waper_btt_block' style='width:100%; overflow:hidden;border-bottom: 5PX solid #E2E2E2;padding-bottom:2px; padding-top:10px;'>";
        echo $AppUI ->createBlock('btt_block',$back_block . $history_block,'style="text-align: center; float:left; height:28px;"');
        //echo $AppUI ->createBlock('btt_block', $delete_id .$view_quotation .$change_rev_block . $save_block . $print_block . $email_block . $cencel_block, 'style="text-align: center; float: right"');
        echo $AppUI ->createBlock('btt_block', $btn_new . $delete_id .  $change_rev_block . $save_block . $save .$print_preview. $print_block . $email_block .$use_template_block.$payment_block.$new_credit.$action_invoice, 'style="text-align: center; float: right"'); //Bo button View Quotation
      echo "</div>";
      

}

function _form_info($invoice_id = 0, $invoice_revision_id = 0) {

    global $AppUI, $CInvoiceManager,$CDepManager, $CSalesManager,$crole,$ContactManager, $cconfig,$CCompany,$addView_Invoice,$editView_Invoice,$CTemplatePDF;
    $url = DP_BASE_DIR. '/modules/sales/images/logo/';
    $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
    $files = scandir($url);
    $department_quotation=0;
    $i = count($files)-1;
    if (count($files) > 2 && $files[$i]!=".svn") {
        $path_file = $url . $files[$i];
        $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" width="170" /><br/>';
    }

    if ($invoice_id != 0 && $invoice_revision_id != 0) {
        _do_update_invoice_order($invoice_id, $invoice_revision_id);
        $invoice_details_arr = $CInvoiceManager->get_db_invoice($invoice_id);
        
        if (count($invoice_details_arr) > 0) {
            $date_hidden = $invoice_details_arr[0]['invoice_date'];
            $date_display = date(FORMAT_DATE_DEFAULT, strtotime($date_hidden));
            $invoice_no = str_replace('"','&quot;',$invoice_details_arr[0]['invoice_no']);
            $invoice_rev = str_replace('"','&quot;',$invoice_details_arr[0]['invoice_revision']);
            if($invoice_no=="0"){
                 $invoice_no = $CSalesManager->create_invoice_or_quotation_no();
                 $invoice_rev = $CSalesManager->create_invoice_or_quotation_revision('', $invoice_no);
            }
            
//            echo $invoice_details_arr[0]['contact_coordinator_id'];
            //$contact_coord_arr=$ContactManager->get_db_contact($invoice_details_arr[0]['contact_coordinator_id']);
            $sale_person_name =  str_replace('"','&quot;',$invoice_details_arr[0]['invoice_sale_person']);
            $sale_person_email = str_replace('"','&quot;',$invoice_details_arr[0]['invoice_sale_person_email']);
            $sale_person_phone = str_replace('"','&quot;',$invoice_details_arr[0]['invoice_sale_person_phone']);
            $sales_sevice_coordinater = $invoice_details_arr[0]['contact_coordinator_id'];
            $po_number = str_replace('"','&quot;',$invoice_details_arr[0]['po_number']);
            $our_delivery = str_replace('"','&quot;',$invoice_details_arr[0]['our_delivery_order_no']);
            $invoice_your_ref = str_replace('"','&quot;',$invoice_details_arr[0]['invoice_your_ref']);
            $sales_term = $invoice_details_arr[0]['term'];
            $sales_invoice_co = $invoice_details_arr[0]['invoice_CO'];
//            print_r($contact_coord_arr);

            $client_id = $invoice_details_arr[0]['customer_id'];
            $department_quotation = $invoice_details_arr[0]['department_id'];
            $address_id = $invoice_details_arr[0]['address_id'];
            $attention_id = $invoice_details_arr[0]['attention_id'];
            $job_location = $invoice_details_arr[0]['job_location_id'];
            $contact_id = $invoice_details_arr[0]['contact_id'];
            $sales_subject = $invoice_details_arr[0]['invoice_subject'];
            $status_inv_id = $invoice_details_arr[0]['invoice_status'];

        } else {
            $date_hidden = date('Y-m-d', time());
            $date_display = date(FORMAT_DATE_DEFAULT, time());
            $invoice_no = '';
            $invoice_rev = '';
            $po_number='';
            $our_delivery ='';
            $invoice_your_ref = '';
            $sales_term = 20;
            $job_location = "";
            $client_id = "";

            $sale_person_name = '';
            $sale_person_email = '';
            $sale_person_phone = '';
            $sales_sevice_coordinater ='';
            $sales_subject = '';
            
        }
    } else {
            $date_hidden = date('Y-m-d', time());
            $date_display = date(FORMAT_DATE_DEFAULT, time());
            $invoice_no = $CSalesManager->create_invoice_or_quotation_no();
            $invoice_rev = $CSalesManager->create_invoice_or_quotation_revision('', $invoice_no);
            $po_number ='';
            $our_delivery = '';
            $invoice_your_ref= '';
            $sales_term = 20;
            $job_location ="";
            $client_id ="";

            $sale_person_name = '';
            $sale_person_email = '';
            $sale_person_phone = '';
            $sales_sevice_coordinater ='';
            $sales_subject = '';
    }
    
    $invoice_stt = dPgetSysVal('InvoiceStatus');

    //print_r($invoice_details_arr);

    // Doan nay cho address (tinhdx update)
    $template_pdf_1 = $CTemplatePDF->get_template_pdf(1);
//    print_r($template_pdf_1);
    
    $supplier_arr = $CSalesManager->get_supplier_info();
    $supplier = '<p><b>'. $supplier_arr['sales_owner_name'] .'</b></p>';
    $supplier .= '<p>'. $supplier_arr['sales_owner_address1'].', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].'</p>';
//    $supplier .= '<p>Postal code: '. $supplier_arr['sales_owner_postal_code'] .'</p>';
    $supplier .= '<p>Tel: '. $supplier_arr['sales_owner_phone1'] .'</p>';
    if($template_pdf_1[0]['template_server']==1)
    {
        $reg_no = '<p>'. $AppUI->_('Co. Reg. No') .': '. $supplier_arr['sales_owner_gst_reg_no'] .'</p>';
        $supplier .= '<p>Contact no: '. $supplier_arr['sales_owner_fax'] .'</p>';
    }
    else
    {
        if($supplier_arr['sales_owner_gst_reg_no']!="")
        $reg_no = '<p>'. $AppUI->_('GST Reg No') .': '. $supplier_arr['sales_owner_gst_reg_no'] .'</p>';
        $supplier .= '<p>Fax: '. $supplier_arr['sales_owner_fax'] .'</p>';
    }
    
    $supplier .= '<p>Email: '. $supplier_arr['sales_owner_email'] .'</p>';
    if($supplier_arr['sales_owner_website']!="")
        $supplier .= '<p>Website: '. $supplier_arr['sales_owner_website'] .'</p>';
    if($supplier_arr['sales_owner_reg_no'])
        $supplier .= '<p>'. $AppUI->_('Reg No') .': '. $supplier_arr['sales_owner_reg_no'] .'</p>';
    $supplier .= $reg_no;
    // (tinhdx end)
    $sales_owner_name = $supplier_arr['sales_owner_name'];
       
    $quotation_id = intval($CInvoiceManager->get_quotation_id($invoice_id));    
    if ($quotation_id != 0) {
        $quotation_revision_id = $CInvoiceManager->get_quotation_revision_field_by_id($quotation_id);
        $quotation_no = $CInvoiceManager->get_quotation_field_by_id($quotation_id, 'quotation_no');
        $view_quotation = '<p>Quotation No: <a target="_blank" href="?m=sales&show_all=1&tab=0&status=update&quotation='.$quotation_id.'&quotation_revision_id='.$quotation_revision_id.'">'.$quotation_no.'</a></p>';
        //$view_quotation = '<p>Quotation No: <a target="_blank" href="?m=sales&a=vw_quotation&c=view_quotation_detail&supperssHeaders=true&quotation_id='.$quotation_id.'&quotation_revision_id='.$quotation_revision_id.'">'.$quotation_no.'</a></p>';
    }
    
    $term = dPgetSysVal('Term');
    $option_term = '<option value="">--Select--</option>';
    foreach ($term as $key => $value) {
        $selected='';
        if($key == $sales_term)
            $selected = 'selected="selected"';
        $option_term .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
    }
    //echo $invoice_no;
    $buffer = '<select id="department" name="department_id" style="width:200px;" class="text">';
    $buffer .= '<option value="" style="font-weight:bold;">'.$AppUI->_('Select').'</option>'."\n";
    $dep_user_id=0;
    $dep_user_arr = $CDepManager->getDeparmentByUser($AppUI->user_id);
    $dep_user_id_arr = array();
    if(count($dep_user_arr)>0)
    {
        
        foreach ($dep_user_arr as $dep_user_row) {
            $dep_user_id_arr[] = $dep_user_row['dept_id'];
            $dep_user_id = $dep_user_row['dept_id'];
        }
    }

    $rows = $CCompany->getCompanyJoinDepartment($dep_user_id_arr);
    $company ="";
    $company_prefix = 'company_';
    foreach ($rows as $row) {
        if ($row['dept_parent'] == 0) {
            if ($company!=$row['company_id']) {
                $buffer .= '<option value="' . $company_prefix.$row['company_id'] . '" style="font-weight:bold;">'. $row['company_name'] . '</option>' . "\n";
                $company=$row['company_id'];
            }
            if ($row['dept_parent']!=null){
                $buffer.=$CDepManager->showchilddept($row,$leve,$department_quotation);
                $buffer.=$CDepManager->findchilddept($rows, $row['dept_id'],$leve,$department_quotation);
            }
        }
    }
    $buffer .= '</select>';
    $invoice_info = '<p>Date: <input type="text" readonly="true" size="8" class="text" name="invoice_date_display" id="invoice_date_display" value="'. $date_display .'">';
    $invoice_info .= '<p>Terms: <select name="term" class="text" style="width:200px;" id="term">'.$option_term.'</select></p>';
    $invoice_info .= '<input type="hidden" name="invoice_date" id="invoice_date" value="'. $date_hidden .'">';
    $invoice_info .= '<p>Department: '.$buffer.'</p>';
    
    require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
    $CTemplatePDF = new CTemplatePDF();  
    $reference_no="<input type='hidden' name='reference_no' id='reference_no' value='' type='hidden'/>";
    
    //Kiem tra template, New template_serser = 4( HL)=>thuc hien chuc nang reference_no
    $template_pdf_5 = $CTemplatePDF->get_template_pdf(5);
    if( $template_pdf_5[0]['template_server'] == 8)
    {
        
        $reference_value="";
        if(isset($_REQUEST['invoice_revision_id']) && $_REQUEST['invoice_revision_id']>0)
        {
            $invoice_rev_details_arr = $CInvoiceManager->get_db_invoice_revsion($_REQUEST['invoice_revision_id']);
            if (count($invoice_rev_details_arr) > 0) {

                $reference_value=$invoice_rev_details_arr[0]['reference_no'];
            }
        }
        $reference_no="<p>Reference No<input type='text' class='text' name='reference_no' id='reference_no' value='".$reference_value."' /></p>";
    }
    $invoice_info .=$reference_no;
    $invoice_info .= '<p>Invoice No: <input type="text" style="padding-left:3px;" class="text" name="invoice_no" id="invoice_no" value="'. $invoice_no .'" onchange = "load_invoice_revision(this.value)" onchange="count_change();"></p>';
    $invoice_info .= '<p>Invoice Rev: <span id="int_rev" style="float:right"><input style="padding-left:3px;" type="text" readonly="true" class="text" name="invoice_revision" id="invoice_revision" value="'. $invoice_rev .'"></span></p>';
   
//    else{
//        $invoice_no_up=$CSalesManager->create_invoice_or_quotation_no();
//        $invoice_info .= '<div><div style="float: left;overflow: hidden;padding-bottom: 5px;width: 158px;">Invoice No:</div><div>Draft</div></p>';
//        $invoice_info .= '<input type="hidden" name="hdd_invoice_no" id="hdd_invoice_no" value="'.$invoice_no_up.'" />';
//        $invoice_info .= '<div><div style="float: left;overflow: hidden;padding-bottom: 5px;width: 158px;">Invoice Rev:</div> <div>Draft</div></div>';
//    }
    $status_inv = "Receivable";
    if($status_inv_id)
        $status_inv = $invoice_stt[$status_inv_id];
    $invoice_info .= '<input type="hidden" name="count_click" id="count_click" value="" />';
    //$invoice_info .= '<p style="clear: both;">Status: <input type="text" style="padding-left:3px;" disabled class="text" name="status_inv" readonly="true" id="status_inv" value="'.$status_inv.'"></p>';
    $invoice_info .= '<p style="clear: both;">Your P.O No: <input type="text" style="padding-left:3px;" class="text" name="po_number" id="po_number" value="'.$po_number.'"></p>';
    $invoice_info .= '<p>Our Delivery Order No: <input style="padding-left:3px;" type="text" class="text" name="our_delivery_order_no" id="our_delivery_order_no" value="'.$our_delivery.'"></p>';
    $invoice_info .= '<p>Our Ref: <input style="padding-left:3px;" type="text" class="text" name="invoice_your_ref" id="invoice_your_ref" value="'.$invoice_your_ref.'" /></p>';
    $invoice_info .= '<p>'.$view_quotation.'</p>';// Hien quotation goc cua invoice dang xet (neu co) - anhnn
    
    
    $div_header_supplier  = "<p><b>Supplier</b></p>";
    $div_supplier_node = $AppUI ->createBlock('div_logo_supplier_node',$supplier, 'style="background:#fff;color:#222222;padding:4px;"');
    $div_supplier = $AppUI ->createBlock('div_logo_supplier', $div_header_supplier.$div_supplier_node, 'class="ui-state-default ui-corner-all" style="padding:5px"');
    $div_invoice_info = $AppUI ->createBlock('div_invoice_info', $invoice_info, 'style="border:1px solid #dcdcdc;border-radius:8px; padding: 10px;"');

    $div_logo_left = $AppUI ->createBlock('div_logo_left', $img, 'style="float: left; width: auto; height:auto;"');

    $div_logo_right = $AppUI ->createBlock('div_logo_right', $div_supplier . $div_invoice_info, 'style="float: right; width: 380px;margin-bottom: 15px"');

    echo '<div id="div_info_1">' . $div_logo_left . $div_logo_right . '</div>';

    
//    // Doan nay cho address (Anhnn update)   
//$address_arr = $CSalesManager->get_list_address($client_id);
//$address_option = '<option value="" >--Select--</option>';
//    if (count($address_arr) > 0) {
//        foreach ($address_arr as $address) {
//            $selected = '';
//            if ($address_id == $address['address_id'])
//                $selected = 'selected="selected"';
//            $address_option .= '<option value="'. $address['address_id'] .'" '. $selected .'>'. $address['address_street_address_1'] .'</option>';
//        }
//    }
//    $dropdown_address = '<select class="text" name="address_id" id="inv_address_id" onchange="load_client_address(this.value);">'. $address_option .'</select>';
//    // (tinhdx end)
//    // 
//// Doan nay cho job location 
//    $location_arr = $CSalesManager->get_list_address($client_id);
////    print_r($location_arr);
//        $location_option = '<option value="" >--Select--</option>';
//        if (count($location_arr) > 0) {
//            foreach ($location_arr as $location) {
//                $selected = '';
//                if ($job_location == $location['address_id'])
//                    $selected = 'selected="selected"';
//                $location_option .= '<option value="'. $location['address_id'] .'" '. $selected .'>'. $location['address_street_address_1'] .'</option>';
//            }
//        } 
//    $dropdown_location = '<select class="text" name="job_location_id" id="inv_job_location_id">'. $location_option .'</select>';
 if($client_id==0){
     // Load Billing Address va Load Job Adress khi add new
     $dropdown_address = '<div class="bill_value" id="sel_address"><select  name="address_id" id="inv_address_id" onchange="load_client_address(this.value);"><option value="0">--Select--</option></select></div>';
     $dropdown_location = '<div class="bill_value" id="sel_job_location"><select name="job_location_id" id="inv_job_location_id" onchange="_get_contact_jobLocation(this.value);"><option value="0">--Select--</option></select></div>';
 }
 else{
    // Load Billing Address va Load Job Adress khi update
    $dropdown_address = '<div class="bill_value" id="sel_address">'._get_address_select($client_id,$address_id) .'</div>';
    $dropdown_location = '<div class="bill_value" id="sel_job_location">'._get_job_address_select($client_id,$job_location).'</div>';
 }
    
    // Doan nay cho companies (Anhnn update)
    $rows = $CSalesManager->get_list_companies();
    //$client_option = '';
//    echo $client_id;
    $client_option = '<option value="0">-- Select --</option>';
//    print_r($rows);
    $title_arr = dPgetSysVal('CustomerTitle');
    $title_customer = '';
    foreach ($rows as $row) {
        $selected = '';
        if ($client_id == $row['company_id']){
            $selected = 'selected="selected"';
            $title_customer = $title_arr[$row['company_title']];
        }
        $client_option .= '<option value="'. $row['company_id'] .'" '. $selected .'>'. $row['company_name'] .'</option>';
    }
    $co_option = '<option value="0">-- Select --</option>';
    foreach ($rows as $rows_co) {
        $selected="";
        if($sales_invoice_co == $rows_co['company_id'])
            $selected = 'selected';
        $co_option .= '<option value="'. $rows_co['company_id'] .'" '. $selected .'>'. $rows_co['company_name'] .'</option>';
    }
    if($addView_Invoice && $editView_Invoice)
        $add_address = '<button class="ui-button ui-state-default ui-corner-all" onclick="addNewAddress(); return false;">Add address</button>';
    $dropdown_client = '<span id="title_customer" style="left: 8px;position: relative;top: 13px;">'.$title_customer.'</span><div class="bill_value"><span id="sel_customer"><select  style="width:393px" name="customer_id" id="inv_customer_id" onchange="load_address(this.value);">'. $client_option .'</select></span>&nbsp;&nbsp;&nbsp;'.$add_address.'</div>';
    $dropdown_co = '<div class="bill_value"><select  style="width:393px" name="invoice_CO" id="int_invoice_CO" >'. $co_option .'</select></div>';
      //$dropdown_client = '<div class="bill_value"><select class="text_select" name="customer_id" id="inv_customer_id" onchange="load_address(this.value);">'. $client_option .'</select></div>';
//load ra phone, fax, email cua customer (Anhnn)
    $client_address =  $CSalesManager->get_address_by_id($address_id);
    
        // lay country customer
    $array_countries = $cconfig->getRecordConfigByList('countriesList');
    $list_countries = array();
    foreach ($array_countries as $array_country) {
        $list_countries[$array_country['config_id']] = $array_country['config_name'];
    }
    
    $option ="";
    $phone ="";
    $mobile="";
    $address_print .= '<div style="overflow:hidden;padding:0;overflow:visible"><div class="bill_lable"></div><div class="bill_value" class="text_select" style="padding:8px 0;" name="inv_client_address" id="inv_client_address"></div></div>';
    if (count($client_address) > 0) {
        if($client_address['address_phone_1']!="") 
            $phone = 'Phone: '.$client_address['address_phone_1'].'&nbsp;&nbsp;&nbsp;&nbsp;';
        elseif($client_address['address_phone_2']!="")
            $phone = 'Phone: '.$client_address['address_phone_2'].'&nbsp;&nbsp;&nbsp;&nbsp;';
        if($client_address['address_mobile_1']!="") 
            $mobile = 'Mobile Phone: '.$client_address['address_mobile_1'];
        elseif($client_address['address_mobile_2']!="")
            $mobile = 'Mobile Phone: '.$client_address['address_mobile_2'];
        
        $option.=$phone;
        if($client_address['address_fax']!="")
            $option.='Fax: '.$client_address['address_fax'].'&nbsp;&nbsp;&nbsp;&nbsp;';
        $option.=$mobile;
//        $option = $client_address['address_phone_1'] .', '. $client_address['address_fax'] , '. $client_address['address_email'];
    }
    if($option!="")
        $address_print = '<div style="overflow:visible;padding:0;overflow:visible"><div class="bill_lable"></div><div class="bill_value" class="text_select" style="padding:8px 0;clear:both;" name="inv_client_address" id="inv_client_address">'.$option.'</div></div>';
    
//    $client = '<div><div class="bill_lable"><b>Bill To:</b> *</div>'. $dropdown_client .'</div>';
//    $client .= '<div style="overflow:hidden;width:100%;padding-top:8px;" ><div class="bill_lable">Address:</div> '.$dropdown_address.'</div>';
//    $client .= '<div style="overflow:hidden;width:100%;padding-top:8px;" ><div class="bill_lable">&nbsp;</div>'.$address_print.'</div>';       
    $client = '<div><div class="bill_lable" style="padding-top: 12px;"><b>Bill To:</b> * <a href="#"  onclick="load_frm_search_customer();return false;"><img width="25" border="0" src="./images/icons/search.png" /></a></div> '. $dropdown_client .'</div>';
    $client.= '<div style="overflow:visible;width:100%;padding-top:15px;clear:both;"><div class="bill_lable">C/O: </div>'.$dropdown_co.'</div>';
    $client .= '<div style="overflow:visible;width:100%;padding-top:15px;clear:both;"><div class="bill_lable">Address: </div>'.$dropdown_address.'</div>';
    $client .= $address_print;    
    // Doan nay cho attention 
    $inv_attention_arr = $CSalesManager->get_salesAttention_by_SalesType($invoice_id, "invoice");
//    print_r($inv_attention_arr);
    $count_inv_attention = count($inv_attention_arr);
    if($count_inv_attention==0){
        $attention_arr = $CSalesManager->get_list_attention($client_id);
        $attention_option = '<option value="" >--Select--</option>';
        if (count($attention_arr) > 0) {
            foreach ($attention_arr as $attention) {
                $selected = '';
                if ($attention_id == $attention['contact_id']){
                    $selected = 'selected="selected"';
                    $contacts_title_id = $attention['contact_title'];
                }
                $attention_option .= '<option value="'. $attention['contact_id'] .'" '. $selected .'>'. $attention['contact_first_name'] .' '. $attention['contact_last_name'] .'</option>';
            }
        }
        $dropdown_attention = '<span id="sel_title">'.get_title_contact($attention_id).'</span><span id="sel_attention"><select class="text_select" name="attention_id[]" id="inv_attention_id" style="width:319px" onchange="load_email(this.value,0);">'. $attention_option .'</select></span>';
        
        $email_attention="";$phone="";
        $email='<div> &nbsp;<div style="margin-top:5px;" id="inv_email" class="bill_value"></div></div>';
        if($attention_id != ""){
            $email_rr=$CSalesManager->get_attention_email($attention_id);
            if(count($email_rr)>0){
               if($email_rr['contact_phone'])
                    $phone = "Phone: ".$email_rr['contact_phone']."&nbsp;&nbsp;&nbsp;";
               elseif($email_rr['contact_phone2'])
                    $phone = "Phone: ".$email_rr['contact_phone2']."&nbsp;&nbsp;&nbsp;";
                
                if($email_rr['contact_email'])
                    $email_attention .= "Email: ". $email_rr['contact_email']. "&nbsp;&nbsp;&nbsp;";
                if($email_rr['contact_mobile'])
                    $email_attention .= "Mobile: ". $email_rr['contact_mobile']. "&nbsp;&nbsp;&nbsp;";
                $email_attention .=$phone;
                $email='<div style="margin-top:5px;" id="inv_email">'.$email_attention.'</div>';
            }
        }else{
            $email= '<div style="margin-top:5px;" id="inv_email">'. _get_email().'</div>';
        }
    }
    
    if($addView_Invoice && $editView_Invoice)
        $add_attention = '<button class="ui-button ui-state-default ui-corner-all" onclick="addContactAttention(); return false;">Add contact</button>';
    
//    $cash = '<div><input type="hidden" id="count_attn" name="count_attn" value="0" />
//                <div class="bill_lable">Attention: &nbsp;<a class="icon-add icon-all" href="#" style="margin-right:0px;" onclick="add_inline_attention(); return false;"></a></div>'. $dropdown_attention .'</div>';

    
    $cash = '
                <table border="0" width="100%">
                    <tbody>';
    if($count_inv_attention>0){
        $tmp=0;
        foreach ($inv_attention_arr as $inv_attention_row) {
            $tmp++;
            $attention_arr = $CSalesManager->get_list_attention($client_id);
            $attention_option = '<option value="" >--Select--</option>';
            if (count($attention_arr) > 0) {
                foreach ($attention_arr as $attention) {
                    $selected = '';
                    if ($attention['contact_id'] == $inv_attention_row['attention_id']){
                        $selected = 'selected="selected"';
                        $contacts_title_id = $attention['contact_title'];
                    }
                    $attention_option .= '<option value="'. $attention['contact_id'] .'" '. $selected .'>'. $attention['contact_first_name'] .' '. $attention['contact_last_name'] .'</option>';
                }
            }
            $lable_attn = 'Attention: <a class="icon-add icon-all" href="#" style="margin-right:0px;" onclick="add_inline_attention(); return false;">&nbsp</a>';
            
            $email_attention="";$phone="";
            $email='<div style="margin-top:5px;" id="inv_email_'.$inv_attention_row['attention_id'].'"></div>';
            if($inv_attention_row['attention_id'] != ""){
                
                $email_rr=$CSalesManager->get_attention_email($inv_attention_row['sale_attention_id']);
                if(count($email_rr)>0){
                   if($email_rr['contact_phone'])
                        $phone = "Phone: ".$email_rr['contact_phone']."&nbsp;&nbsp;&nbsp;";
                   elseif($email_rr['contact_phone2'])
                        $phone = "Phone: ".$email_rr['contact_phone2']."&nbsp;&nbsp;&nbsp;";
                   
                    if($email_rr['contact_email'])
                        $email_attention .= "Email: ". $email_rr['contact_email']. "&nbsp;&nbsp;&nbsp;";
                    if($email_rr['contact_mobile'])
                        $email_attention .= "Mobile: ". $email_rr['contact_mobile']. "&nbsp;&nbsp;&nbsp;";
                    $email_attention.=$phone;
                    $email='<div style="margin-top:5px;" id="inv_email_'.$inv_attention_row['attention_id'].'">'.$email_attention.'</div>';
                }
            }else{
                $email= '<div style="margin-top:5px;" id="inv_email_'.$inv_attention_row['attention_id'].'">'. _get_email().'</div>';
            }
             
            if($tmp>1){
                $lable_attn ="";
                $add_attention="";
            }
            $dropdown_attention = '<span id="sel_title_'.$inv_attention_row['attention_id'].'">'.get_title_contact($inv_attention_row['attention_id']).'</span>
                <span id="sel_attention"><select class="text_select" name="attention_id[]" id="inv_attention_id_'.$inv_attention_row['attention_id'].'" style="width:319px" onchange="load_email(this.value,'.$inv_attention_row['attention_id'].');">'. $attention_option .'</select></span>';
            $cash .=           '<tr valign="top">
                                    <td width="19.5%" style="padding-top:5px;"><input type="hidden" name="attn_id[]" value="'.$inv_attention_row['sales_attention_id'].'" />'.$lable_attn.'</td>
                                    <td width="62%">'.$dropdown_attention.$email.'</td>
                                    <td >'.$add_attention.'</td>
                                </tr>';
        }
    }else{
        
        $cash .=   '<tr valign="top">
                        <td width="19.5%" style="padding-top:5px;">Attention: <a class="icon-add icon-all" href="#" style="margin-right:0px;" onclick="add_inline_attention(); return false;"></a></td>
                        <td width="62%">'.$dropdown_attention.$email.'</td>
                        <td >'.$add_attention.'</td>
                    </tr>';
    }
     $cash.=        '</tbody>
                    <tfoot id="add_line_attn_foot">
                        <input type="hidden" id="count_attn" name="count_attn" value="1" />
                    </tfoot>
                </table>';
    
    $joblocation='<div><div class="bill_lable">Job Location:</div> '.$dropdown_location.'</div>';
    
    //Doan nay lay con contact cua Job Location
    if($job_location>0)
        $contact_arr = $CCompany->getContactInAddress($job_location);
//    print_r($contact_arr);
    
    $contact_option = '<option value="" >--Select--</option>';
    if (count($contact_arr) > 0) {
        foreach ($contact_arr as $contact_row) {
            $selected = '';
            if ($contact_id == $contact_row['contact_id'])
                $selected = 'selected="selected"';
            $contact_option .= '<option value="'. $contact_row['contact_id'] .'" '. $selected .'>'. $contact_row['contact_first_name'] .' '. $contact_row['contact_last_name'] .'</option>';
        }
    }
    if($addView_Invoice && $editView_Invoice)
        $add_contact = '<button class="ui-button ui-state-default ui-corner-all" onclick="addContactAddress(); return false;">Add contact</button>';
    $dropdown_contact = '<div class="bill_value"><select class="text_select" style="width:393px" name="contact_id" id="inv_contact_id" onclick="load_email_contact(this.value)">'. $contact_option .'</select>&nbsp;&nbsp;&nbsp;'.$add_contact.'</div>';
    $contact = '<div><div class="bill_lable">Contact: </div id="inv_contact"> '.$dropdown_contact.'</div>';
    $contract_no = '<div><div class="bill_lable" >Contracts No: </div ><div id="inv_contract_no" class="bill_value">'.load_contract_no($client_id,$invoice_id,$job_location).'</div></div>';
    
    $address_contact_option = "";$phone="";
    $address_contact = '<div> &nbsp;<div style="margin-top:5px;" id="inv_address_contact" class="bill_value"></div></div>';
    
    if($contact_id!=""){
       
        $address_contac_arr=$CSalesManager->get_attention_email($contact_id);
        if(count($address_contac_arr)>0){
           if($email_rr['contact_phone'])
                $phone = "Phone: ".$email_rr['contact_phone']."&nbsp;&nbsp;&nbsp;";
           elseif($email_rr['contact_phone2'])
                $phone = "Phone: ".$email_rr['contact_phone2']."&nbsp;&nbsp;&nbsp;";
            
            if($address_contac_arr['contact_email'])
                $address_contact_option .= "Email: ". $address_contac_arr['contact_email']. "&nbsp;&nbsp;&nbsp;";
            if($address_contac_arr['contact_mobile'])
                $address_contact_option .= "Mobile: ". $address_contac_arr['contact_mobile']. "&nbsp;&nbsp;&nbsp;";
            $address_contact_option.=$phone;
            $address_contact='<div> &nbsp;<div style="margin-top:5px;" id="inv_address_contact" class="bill_value">'.$address_contact_option.'</div></div>';
        }
        else{
            $address_contact= '<div> &nbsp;<div style="margin-top:5px;" id="inv_address_contact" class="bill_value">'. _get_email().'</div></div>';
        }
    }
    $contact.=$address_contact;
//END
    
    $div_2_client = '<div id="div_2_client" style="overflow:hidden">'.$client.'</div>';
    $div_2_cash = '<div id="div_2_cash" style="margin-top:0px;padding:1px">'.$cash.'</div>';
//    $div_2_email= $AppUI ->createBlock('div_2_email',$email,'style="padding: 8px 0px; overflow: auto;"');
    $div_2_joblocation = '<div id=div_2_joblocation style="clear: both;padding-top:8px;overflow:visible;">'.$joblocation.'</div>';
    $div_2_contact = '<div id="div_2_contact" style="clear: both;padding-top:8px">'.$contact.'</div>';
    $div_2_contract = '<div id="div_2_contract" style="clear:both;padding-top:8px;">'.$contract_no.'</div>';
    // Lay nhung contact la coordinator
    $roles_coord = $crole->getRolesByValue('coordinator');
    $UserIDcoord_arr = $crole->getUserIdByRoleId($roles_coord['id']);
    //print_r($UserIDcoord_arr);
    $option_contactCoord ='<option value="">--Select--</option>';
    foreach ($UserIDcoord_arr as $UserIDcoord_row) {
        //echo $UserIDcoord_row['value'];
        $contact_manager = $ContactManager->getContactByUserId($UserIDcoord_row['value']);
        if($contact_manager['user_id']==$UserIDcoord_row['value']){
            if($contact_manager['contact_id'] == $sales_sevice_coordinater){
                $selected = 'selected="selected"';
            }
            $option_contactCoord .='<option value="'.$contact_manager['contact_id'].'" '.(($contact_manager['contact_id'] == $sales_sevice_coordinater) ? 'selected':'').'>'.$contact_manager['contact_last_name'] .', '. $contact_manager['contact_first_name'].'</option>';
        }
    } //END
    $sales_contact = '<p><b>Sales Agent:</b></p>';
    $sales_contact .= '<p>Name: <input type="text" class="text" name="invoice_sale_person" id="invoice_sale_person" value="'.$sale_person_name.'" onkeyup="show_save(\'invoice_sale_person\');">';
    $sales_contact .= '<p>Email: <input type="text" class="text" name="invoice_sale_person_email" id="invoice_sale_person_email" value="'. $sale_person_email .'">';
    $sales_contact .= '<p>Phone: <input type="text" class="text" name="invoice_sale_person_phone" id="invoice_sale_person_phone" autocomplete ="off" value="'. $sale_person_phone .'">';
    //$sales_contact .= '<p>Personnel: <select class="text_select" style="width:200px; float:right" name="contact_coordinator_id" id="contact_coordinator_id">'.$option_contactCoord.'</select></p>';
    if($invoice_id != 0 && $invoice_revision_id != 0)
    {
        if($sales_sevice_coordinater=="")
            $user_sevice_coordinater = "";
        else
            $user_sevice_coordinater = $CInvoiceManager->get_user_change($sales_sevice_coordinater);
        $sales_contact .= '<p>Personnel: <input type="text" class="text" style="background:#eee" readonly=true name="contact_coordinator_id" id="contact_coordinator_id" autocomplete ="off" value="'. $user_sevice_coordinater.'"></p>';
    }
    else
        $sales_contact .= '<p>Personnel: <input type="text" class="text" style="background:#eee" name="contact_coordinator_id" readonly=true id="contact_coordinator_id" autocomplete ="off" value="'. $CInvoiceManager->get_user_change($AppUI->user_id).'"></p>';

    $div_2_left = '<div style="float: left; width: 660px;">'. $div_2_client . $div_2_cash.$div_2_email.$div_2_joblocation.$div_2_contact.$div_2_contract. '</div>';
    $div_2_right = $AppUI ->createBlock('div_2_right', $sales_contact, 'style="float: right; width: 360px;"');

    echo '<div id="div_info_2" style="border:1px solid #dcdcdc;border-radius:8px;overflow: visible;height:340px; padding: 10px;">' . $div_2_left . $div_2_right . '</div>';
    //echo '<br/><div id="detail_payment_inside" style="overflow: auto;"></div>';
    $inv_subject = '<b>Subject:</b><br>';
    $inv_subject .= '<textarea cols="60" rows="4" id="invoice_subject" value="" name="invoice_subject" type="text">'.$sales_subject.'</textarea>'
                    . '<a class="icon-save icon-all" onclick="save_template_subheading_or_subject(\'subject\',\'invoice\'); return false;" title="Save subject as template" style="cursor: pointer; left: 9px;position: relative;top: -60px;"></a>'
            . '<img border="0" src="images/icons/list.png" onclick="list_template_subject(\'subject\',\'invoice\');return false;" title="List and apply template" style="cursor: pointer; left: 9px;position: relative;top: -60px;left:-5px">';
    
    echo $AppUI->createBlock('inv_div_subject', $inv_subject , 'style="padding-top: 15px;clear:both;"');
    if($invoice_id != 0 && $invoice_revision_id != 0){
        $invoice_id;
        $serviceOder_arr = $CSalesManager->get_serviceoder_id($invoice_id, 'invoice');
        $serviceOder_id = $serviceOder_arr[0]['service_order_id'];
        echo '<input type="hidden" name="invoice_serviceoder" id="invoice_serviceoder" value='. $serviceOder_id .'>';
        //print_r($serviceOder_arr);
    }
    //kiem tra invoice revision nay da co Payment chua
    if($invoice_id>0){
        $check_payment = 0;
        if($CInvoiceManager->check_payment_id($invoice_id)==true)
            $check_payment = 1;

        echo '<input type="hidden" name="int_revision_check_payment" id="int_revision_check_payment" value='.$check_payment.'>';
        echo '<input type="hidden" id="status_inv_id" value="'.$status_inv_id.'"/>';
    }
}

//load ra dia chi sau khi chon Bill To (Anhnn)
function _get_address_select($customer_id = false, $address_id=false) {
    
   global $CSalesManager,$CMemoManager;
   include DP_BASE_DIR."/modules/sales/load_chose.js.php";
    /* Load Billing Adress */   
    $address_option = '<option value="" >--Select--</option>';
        if($customer_id && $customer_id!="")
            $address_arr = $CSalesManager->get_list_address($customer_id,false,3);
        else if($_POST['customer_id'] != ""){
            $address_arr = $CSalesManager->get_list_address($_POST['customer_id'],false,3);
            $customer_memo_err=$CMemoManager->get_memo_billing_address($_POST['customer_id']);
        }
            if (count($address_arr) > 0) {
                $address_option .= '<optgroup label="Own Address">';
                foreach ($address_arr as $address) {
                    if($address['address_type']==2)
                        $address_type = "[Billing] ";
                    else if($address['address_type']==1)
                        $address_type = "[Job Site] ";
                    else 
                        $address_type = "[NA] ";
                    $brand="";
                    if($address['address_branch_name']!="")
                        $brand = $address['address_branch_name']." - ";
                    $selected = '';
                    if ($address_id == $address['address_id'] || $address['address_id'] == $customer_memo_err[0]['address_id'])
                        $selected = 'selected="selected"';
                    $address_option .= '<option value="'. $address['address_id'] .'" '. $selected .'>'.$address_type.$brand.$address['address_street_address_1'].' '.$address['address_street_address_2'].' '.'Singapore '.$address['address_postal_zip_code'].'</option>';
                }
                $address_option.='</optgroup>';
            }
        // Lay adress customer gan customer duoc chon lam contractor
            if($customer_id)
                $address_customer_contractor = $CSalesManager->get_Customer_By_ContractorCustomer($customer_id);
            else if($_POST['customer_id'] != ""){
                $address_customer_contractor = $CSalesManager->get_Customer_By_ContractorCustomer($_POST['customer_id']);
            }
            if(count($address_customer_contractor>0)){
                foreach ($address_customer_contractor as $address_customer_value) {
                  $address_option.='<optgroup label="Customer '.$address_customer_value['company_name'].'">';
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
                          $address_option .= '<option value="'.$address_contractor_value['address_id'].'" '. $selected .'>'.$address_type.$brand.$address_contractor_value['address_street_address_1'].' '.$address_contractor_value['address_street_address_2'].' '.'Singapore '.$address_contractor_value['address_postal_zip_code'].'</option>';
                      }
                  $address_option.='</optgroup>';
                }
            }

        // Lay address customer gan customer duoc chon lam Agent
        if($customer_id)
            $address_customer_agent = $CSalesManager->get_Customer_By_AgentCustomer($customer_id);
        else if($_POST['customer_id'] != "")
            $address_customer_agent = $CSalesManager->get_Customer_By_AgentCustomer($_POST['customer_id']);
        if($address_customer_agent>0){
            foreach ($address_customer_agent as $address_cus_agent_value){
                $address_option.='<optgroup label="Customer '.$address_cus_agent_value['company_name'].'">';
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
                      $address_option .= '<option value="'.$address_agent_value['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value['address_street_address_1'].$address_agent_value['address_street_address_2'].' '.' '.'Singapore '.$address_agent_value['address_postal_zip_code'].'</option>';
                  }
                $address_option.='</optgroup>';
            }
        }

        //Lay add customer la agent cua customer duoc chon
        if($customer_id && $customer_id!="")
            $address_agent_customer = $CSalesManager->get_AgentCustomer_by_customer($customer_id);
        else if($_POST['customer_id'] != "")
            $address_agent_customer = $CSalesManager->get_AgentCustomer_by_customer($_POST['customer_id']);
        if($address_agent_customer>0){
            foreach ($address_agent_customer as $address_agent_customer_value) {
                if($address_agent_customer_value['address_id']!=""){
                    $address_option.='<optgroup label="Agent '.$address_agent_customer_value['company_name'].'">';
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
                            $address_option .= '<option value="'.$address_agent_value1['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value1['address_street_address_1'].' '.$address_agent_value1['address_street_address_2'].' '.'Singapore '.$address_agent_value1['address_postal_zip_code'].'</option>';
                        }
                     $address_option.='</optgroup>';
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
                    $address_option.='<optgroup label="Contractor '.$address_contractor_value['company_name'].'">';
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
                            $address_option .= '<option value="'.$address_agent_value1['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value1['address_street_address_1'].' '.$address_agent_value1['address_street_address_2'].' '.'Singapore '.$address_agent_value1['address_postal_zip_code'].'</option>';
                        }
                     $address_option.='</optgroup>'; 
                }
            }
        }
    if($customer_id)
        return '<select  name="address_id" id="inv_address_id" onchange="load_client_address(this.value);">'.$address_option.'</select>';
    else
        echo '<select  name="address_id" id="inv_address_id" onchange="load_client_address(this.value);">'.$address_option.'</select>';
}

function _get_job_address_select($customer_id = false,$address_id=false) {
    
global $CSalesManager,$CMemoManager;
include DP_BASE_DIR."/modules/sales/load_chose.js.php";
    /* Load Job Adress */   
    $address_option = '<option value="0" >--Select--</option>';
        if($customer_id)
            $address_arr = $CSalesManager->get_list_address($customer_id,false,3);
        else if($_POST['customer_id'] != "")
            $address_arr = $CSalesManager->get_list_address($_POST['customer_id'],false,3);
            if (count($address_arr) > 0) {
                $address_option .= '<optgroup label="Own Address">';
                foreach ($address_arr as $address) {
                    if($address['address_type']==2)
                        $address_type = "[Billing] ";
                    else if($address['address_type']==1)
                        $address_type = "[Job Site] ";
//                    else if($address['address_type']==3)
//                        $address_type = "[Inactive] ";
                    else 
                        $address_type = "[NA] ";
                    $brand="";
                    if($address['address_branch_name']!="")
                        $brand = $address['address_branch_name']." - ";  
                    $selected = '';
                    if ($address_id == $address['address_id'])
                        $selected = 'selected="selected"';
                    $address_option .= '<option value="'. $address['address_id'] .'" '. $selected .'>'.$address_type.$brand. $address['address_street_address_1'].' '.$address['address_street_address_2'].' Singapore '.$address['address_postal_zip_code'].'</option>';
                }
                $address_option.='</optgroup>';
            }
        // Lay adress customer gan customer duoc chon lam contractor
            if($customer_id)
                $address_customer_contractor = $CSalesManager->get_Customer_By_ContractorCustomer($customer_id);
            else if($_POST['customer_id'] != "")
                $address_customer_contractor = $CSalesManager->get_Customer_By_ContractorCustomer($_POST['customer_id']);
            if(count($address_customer_contractor>0)){
                foreach ($address_customer_contractor as $address_customer_value) {
                  $address_option.='<optgroup label="Customer '.$address_customer_value['company_name'].'">';
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
                            if ($address_id == $address_contractor_value['address_id'])
                                $selected = 'selected="selected"';
                          $address_option .= '<option value="'.$address_contractor_value['address_id'].'" '. $selected .'>'.$address_type.$brand.$address_contractor_value['address_street_address_1'].' '.$address_contractor_value['address_street_address_2'].' Singapore '.$address['address_postal_zip_code'].'</option>';
                      }
                  $address_option.='</optgroup>';
                }
            }

        // Lay address customer gan customer duoc chon lam Agent
        if($customer_id)
            $address_customer_agent = $CSalesManager->get_Customer_By_AgentCustomer($customer_id);
        else if($_POST['customer_id'] != "")
            $address_customer_agent = $CSalesManager->get_Customer_By_AgentCustomer($_POST['customer_id']);
        if($address_customer_agent>0){
            foreach ($address_customer_agent as $address_cus_agent_value){
                $address_option.='<optgroup label="Customer '.$address_cus_agent_value['company_name'].'">';
                  $address_agent_arr = $CSalesManager->get_list_address($address_cus_agent_value['company_id']);
                  foreach ($address_agent_arr as $address_agent_value) {
                        if($address_agent_value['address_type']==2)
                            $address_type = "[Billing] ";
                        else if($address_agent_value['address_type']==1)
                            $address_type = "[Job Site] ";
                        else 
                            $address_type = "[NA] ";
                        if($address_agent_value['address_branch_name']!="")
                                $brand = $address_agent_value['address_branch_name']." - "; 
                        $selected = '';
                        if ($address_id == $address_agent_value['address_id'])
                            $selected = 'selected="selected"';
                      $address_option .= '<option value="'.$address_agent_value['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value['address_street_address_1'].' '.$address_agent_value['address_street_address_2'].'Singapore '.$address['address_postal_zip_code'].'</option>';
                  }
                $address_option.='</optgroup>';
            }
        }
    if($customer_id)
        return '<select  name="job_location_id" id="inv_job_location_id" onchange="_get_contact_jobLocation(this.value);">'.$address_option.'</select>';
    else
        echo '<select name="job_location_id" id="inv_job_location_id" onchange="_get_contact_jobLocation(this.value);">'.$address_option.'</select>';
}


//Load attention sau khi chon Bill To (Anhnn)
function _get_attention_select() {
    
    global $CSalesManager, $CMemoManager;
    include DP_BASE_DIR."/modules/sales/load_chose.js.php";
    $customer_arr = $CMemoManager->get_memo_billing_address($_POST['customer_id']);
    $attention_arr = $CSalesManager->get_list_attention($_POST['customer_id']);
//    print_r($attention_arr);
    $option = '<option value="0">-- Select --</option>';
    if (count($attention_arr) > 0) {
        foreach ($attention_arr as $attention) {
            $selected="";
            if (($attention_id == $attention['contact_id'] || $attention['contact_id']==$customer_arr[0]['attention_id'] || $attention['contact_id'] == $_POST['contact_id']) && !isset($_POST['act_attn']))
               $selected = 'selected="selected"';
            $option .= '<option value="'.$attention['contact_id'].'" '.$selected.'>'. $attention['contact_first_name'] .' '. $attention['contact_last_name'] .'</option>';
        }
    }
    echo $option;
}

//Load ra phone, fax, email cua customer (Anhnn)
function  _get_client_address(){
    global $CSalesManager, $cconfig, $CMemoManager;
    if(isset($_POST['address_id'])){
        $client_address =  $CSalesManager->get_address_by_id($_POST['address_id']);
    }
    else if(isset($_POST['customer_id']) && $_POST['customer_id']!=""){// lay client address da duoc ghi nho
        $customer_arr = $CMemoManager->get_memo_billing_address($_POST['customer_id']);
        $client_address =  $CSalesManager->get_address_by_id($customer_arr[0]['address_id']);
    }
        // lay country customer
    $array_countries = $cconfig->getRecordConfigByList('countriesList');
    $list_countries = array();
    foreach ($array_countries as $array_country) {
        $list_countries[$array_country['config_id']] = $array_country['config_name'];
    }
    
    $phone="";
    $option ="";
    if (count($client_address) > 0) {
        if($client_address['address_phone_1']!="") 
            $phone = 'Phone: '.$client_address['address_phone_1'].'&nbsp;&nbsp;&nbsp;&nbsp;';
        elseif($client_address['address_phone_2']!="")
            $phone = 'Phone: '.$client_address['address_phone_2'].'&nbsp;&nbsp;&nbsp;&nbsp;';
        
        if($client_address['address_mobile_1']!="") 
            $mobile = 'Mobile Phone: '.$client_address['address_mobile_1'];
        elseif($client_address['address_mobile_2']!="")
            $mobile = 'Mobile Phone: '.$client_address['address_mobile_2'];
        
        $option.=$phone;
        if($client_address['address_fax']!="")
            $option.='Fax: '.$client_address['address_fax'].'&nbsp;&nbsp;&nbsp;&nbsp;';
        $option.=$mobile;
//        $option = $client_address['address_phone_1'] .', '. $client_address['address_fax'] , '. $client_address['address_email'];
    }
    echo $option;
}
// Load ra Email
function _get_email(){
    global $CSalesManager,$CMemoManager;
    if(isset($_POST['attention_id'])){
        $email_rr = $CSalesManager->get_attention_email($_POST['attention_id']);
    }
    else if(isset($_POST['contact_id'])){
        $email_rr = $CSalesManager->get_attention_email($_POST['contact_id']);
    }
    else if(isset($_POST['customer_id']) && $_POST['customer_id']!=""){
        $customer_memo_err=$CMemoManager->get_memo_billing_address($_POST['customer_id']);    
        $email_rr =  $CSalesManager->get_attention_email($customer_memo_err[0]['attention_id']);
    }
    $email="";$phone="";
    if(count($email_rr) >0){
       if($email_rr['contact_phone'])
            $phone = "Phone: ".$email_rr['contact_phone']."&nbsp;&nbsp;&nbsp;";
       elseif($email_rr['contact_phone2'])
            $phone = "Phone: ".$email_rr['contact_phone2']."&nbsp;&nbsp;&nbsp;";
       
        if($email_rr['contact_email']!="")
            $email .= "Email: ".$email_rr['contact_email']."&nbsp;&nbsp;&nbsp;";
        if($email_rr['contact_mobile']!="")
            $email .= "Mobile: ".$email_rr['contact_mobile']."&nbsp;&nbsp;&nbsp;";
        $email.=$phone;
    }
    echo $email;
}
//END

function show_html_table($invoice_id = 0, $invoice_revision_id = 0) {

    global $AppUI, $CSalesManager, $canDelete, $canAdd, $addView_Invoice, $editView_Invoice, $deleteView_Invoice;
    include DP_BASE_DIR."/modules/sales/invoice_table.js.php";
    include DP_BASE_DIR."/modules/sales/css/sales.css.php";
    $status = $_POST['status_rev'];

    $invoice_item_arr = array();
    if ($invoice_id != 0 && $invoice_revision_id != 0 ) { // neu hanh dong la view mot invoice revision da co trong he thong
        _do_update_invoice_order($invoice_id, $invoice_revision_id);
        global $CInvoiceManager;
        $invoice_arr = $CInvoiceManager->get_db_invoice($invoice_id);
        $sub_heading = $invoice_arr[0]['sub_heading'];
        
        $invoice_item_arr = $CInvoiceManager->get_db_invoice_item($invoice_id, $invoice_revision_id);
    }
        echo '<div id="jdatatable_container_detail_invoice_table2" style="width: 100%; padding-bottom: 15px; margin-top:10px;">';
        echo '<table cellspacing="1" cellpadding="2" style="clear: both; width: 100%" id="detail_invoice_table" class="tbl">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="check_all(\'item_select_all\', \'item_check_list\');" id="item_select_all" name="item_select_all"></th>
                        <th>#</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Discount(%)</th>
                        <th>Amount</th>
                    </tr>
                    <tr><td colspan="7"><textarea name="sub_heading" id="sub_heading" rows="2" cols="120">'.$sub_heading.'</textarea>
                        <a class="icon-save icon-all" onclick="save_template_subheading_or_subject(\'subheading\',\'invoice\'); return false;" title="Save sub heading as template" style="cursor: pointer; left: 9px;position: relative;top: -25px;"></a>
                        <img border="0" src="images/icons/list.png" onclick="list_template_subheading(\'subheading\');return false;" title="List and apply template" style="cursor: pointer; left: 9px;position: relative;top: -25px;left:-5px">
                    </td></tr>
                </thead>
                <tbody id="invoice_sortable">';
                
            if (isset($invoice_item_arr) && count($invoice_item_arr) > 0) {
                $i = 1;
                $str_br = "<br />";
                foreach ($invoice_item_arr as $invoice_item) {
                    if ($invoice_item['invoice_item_type'] == 0) {
                        //$item = htmlspecialchars($invoice_item['invoice_item']);
                        $item= $CSalesManager->htmlChars($invoice_item['invoice_item']);
                        //$item = $invoice_item['invoice_item'];
                    } elseif ($invoice_item['invoice_item_type'] == 1) {
                        $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                    } elseif ($invoice_item['invoice_item_type'] == 2) {
                        $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                    } else {
                        $item = '<font color="red">Item type not found.</font>';
                    }
                    $item_id = $invoice_item['invoice_item_id'];
                    
                    if($editView_Invoice)
                        $edit_template = '
                                        <a class="icon-edit icon-all" title="Edit item" onclick="edit_inv_inline('. $item_id .'); return false;" href="#" title="Inline Edit" style="margin-right:0px"></a>
                                        <!--<a class="icon-save icon-all" onclick="save_template_item('.$item_id.'); return false;" title="Save item as template" ></a>-->
                                        <a class="icon-save icon-all" onclick="popupFormSaveItemTemplate('.$item_id.'); return false;" title="Save item as template" ></a>
                            ';
                    echo '
                        <tr style="cursor: pointer;" id="row_item_'.$item_id.'" valign="top" class="items[]_'. $invoice_item['invoice_order'] .'">
                            <td valign="top"  align="center" id="">
                                <input type="checkbox" value="'. $item_id .'" name="item_check_list" id="item_check_list">&nbsp;
                                '.$edit_template.'
                                <input type="hidden" class="text" id="click_invoice'.$item_id.'" name="" value="1"/>
                            </td>
                            <td id="stt_'.$item_id.'" class ="items[]_'.$item_id.'" width="4%" align="center">'.$invoice_item['invoice_order'].'</td>
                            <td valign="top" width="47%" id="item_'. $item_id .'">'. $item .'</td>
                            <td valign="top" width="8%" align="right" id="quantity_'. $item_id .'">'. $invoice_item['invoice_item_quantity'] .'</td>
                            <td valign="top" width="12%" align="right" id="price_'. $item_id .'">'. number_format($invoice_item['invoice_item_price'],2) .'</td>
                            <td valign="top" width="9%" align="right" id="discount_'. $item_id .'">'. $invoice_item['invoice_item_discount'] .'%</td>
                            <td valign="top" width="12%" align="right" id="total_'. $item_id .'">'. number_format($CSalesManager->calculate_total_item($invoice_item['invoice_item_price'], $invoice_item['invoice_item_quantity'], $invoice_item['invoice_item_discount']),2) .'</td>
                        </tr>';
                    $i++;
                }
            }
                echo '</tbody></table></div><br/>';
          $total_item = 0;
          if($invoice_id!=0 && $invoice_revision_id!=0){
              $total_item = $CInvoiceManager->max_inv_order_item($invoice_id, $invoice_revision_id);
          }
          if($total_item=="")
                $total_item = 0;
          echo '<input type="hidden" name="invo_total_item" id="invo_total_item" value="'.$total_item.'">';
          echo '<input type="hidden" name="count_total_item" id="count_total_item" value="'.$i.'">';
          echo '<input type="hidden" name="inv_id" id="inv_id" value="'.$invoice_id.'">';
          echo '<input type="hidden" name="inv_rev_id" id="inv_rev_id" value="'.$invoice_revision_id.'">';
          echo '<input type="hidden" name="click_edit_inv" id="click_edit_inv" value="">';
          
        if($deleteView_Invoice) 
            $delete_id = '<a class="icon-delete icon-all" onclick="delete_invoice_item('. $invoice_id .', '. $invoice_revision_id .', \''.$status.'\'); return false;" href="#">Delete</a>';
        
        //$edit_id = '<a class="icon-edit" onclick="edit_invoice_item('. $invoice_id .', '. $invoice_revision_id .', \''.$status.'\'); return false;" href="#">Edit</a>';
        //$edit_id = '<a class="icon-edit" onclick="edit_all(); return false;" href="#">Edit</a>';
        if ($addView_Invoice) {
            $add_id = '<a class="icon-add icon-all" onclick="add_inline_item(); return false;" href="#">Add item</a>';
        }
        $re_calculator = '<div id="calculator" style="float:right;"><a href="#" onclick="load_rev_calculator(); return false;">Reverse Calculator</a></div>';
        
        echo $block_del = $AppUI ->createBlock('del_block', $delete_id . $edit_id . $add_id. $add_tax. $re_calculator, 'style="text-align: left;"');
   // }
    
}

function _form_total_and_note($invoice_id = 0, $invoice_revision_id = 0){
    global $AppUI, $CTemplateManager,$CSalesManager, $CInvoiceManager;
    $status = $_POST['status_rev'];
  
    $currency_symb = '$'; $total_paid_and_tax = 0;
    
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();
    if($invoice_id != 0)
        $invoice_customer_arr = $CInvoiceManager->get_db_invoice($invoice_id);
    $tr_paid = '<tr>
                    <td class="td_left">Amount Paid :</td>
                    <td class="td_left">'. $currency_symb .'<span id="inv_paid">0.00</span></td>
                </tr>';

    if ($invoice_id != 0 && $invoice_revision_id != 0) {
        
        global $CInvoiceManager;
        $invoice_rev_details_arr = $CInvoiceManager->get_db_invoice_revsion($invoice_revision_id);

        $discount = 0;
        if (count($invoice_rev_details_arr) > 0) {
            $invoice_revision = $invoice_rev_details_arr[0]['invoice_revision'];
            $tax_id = $invoice_rev_details_arr[0]['invoice_revision_tax'];
            $discount = $invoice_rev_details_arr[0]['invoice_revision_discount'];
        }
        
        // Doan xu ly lay ra payment cho total
        require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");
        $CPaymentManager = new CPaymentManager();
        //$payment_arr = $CPaymentManager->list_db_payment($invoice_revision_id);
//        $paymentDetail_arr = $CPaymentManager->lis_db_payment_detail($invoice_revision_id);

//        if (count($payment_arr) > 0) {
//            foreach ($payment_arr as $payment) {
//                $payment_amount = $payment['payment_amount'];
//                $total_paid_and_tax += floatval($payment['payment_amount']);
//                $total_paid_and_tax = round($total_paid_and_tax, 2);
//            }
//            $tr_paid .= 
//                    '<tr>
//                        <td class="td_left">Amount Paid :</td>
//                        <td class="td_right">'. $currency_symb .''. $total_paid_and_tax .'</td>
//                    </tr>';        
//        }
//        if(count($paymentDetail_arr)>0){
//            foreach ($paymentDetail_arr as $paymentDetail){
//                $paymentDetail_amount =  $paymentDetail['payment_amount'];
//                $total_paid_and_tax += floatval($paymentDetail['payment_amount']);
//                $total_paid_and_tax = round($total_paid_and_tax, 2);
//            }
            
            $total_paid_and_tax = $CSalesManager->get_total_amount_paid($invoice_revision_id);
            $tr_paid = 
                    '<tr>
                        <td class="td_left">Amount Paid :</td>
                        <td class="td_left"><a href="?m=sales&show_all=1&tab=3&invoice_id='.$invoice_id.'&customer_id='.$invoice_customer_arr[0]['customer_id'].'&status=Paid">'. $currency_symb .'<span id="inv_paid">'. number_format($total_paid_and_tax,2) .'</span></a></td>
                    </tr>';   
//        }
//        
        
        $total_item_show = $CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id);
        $revison_lastest = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
        //$AppUI->addJSScript('$(\'#invoice_revision\').val(\''.$invoice_revision.'\'); $(\'#invoice_rev_label\').append(\' <font color="red">'.$invoice_revision.'</font>\');');
        //$AppUI->addJSScript('$(\'#invoice_revision\').val(\''.$invoice_revision.'\'); $(\'#invoice_rev_label\').append(\' <font color="red">'.$invoice_revision.'</font><input type="hidden" name="revision_inv_lastest" id="revision_inv_lastest" value="'.$revison_lastest[0]['invoice_revision'].'" />\');');
        //$save = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_all_invoice('. $invoice_id .', '. $invoice_revision_id .', \''. $status .'\'); return false;">Save As New Revision</button>&nbsp';
    }

    $tax_option = '<option value="">---</option>';
    if (count($tax_arr) > 0) {
        foreach ($tax_arr as $tax) {
            $selected = '';
            if ($tax_id) { // neu tinh trang la update
                if ($tax['tax_id'] == $tax_id) {
                    $selected = 'selected="selected"';
                    $tax_value = $tax['tax_rate'];
                }
            } else { // neu tinh trang la add lay ra ta default
                if ($tax['tax_default'] == 1) {
                    $selected = 'selected="selected"';
                    $tax_value = $tax['tax_rate'];
                }
            }

            $tax_option .= '<option value="'. $tax['tax_id'] .'" '. $selected .'>'. $tax['tax_rate'] .'</option>';
        }
    }
    $total_item_show_last_discount = $total_item_show - $discount;
    $total_item_tax_show = 0;
    if ($tax_id) {
        $caculating_tax = (floatval($total_item_show_last_discount) * floatval($tax_value) / 100);
        $total_item_tax_show =$CSalesManager->round_up($caculating_tax);
        if($invoice_rev_details_arr[0]['invoice_revision_tax_edit']!=0)
            $total_item_tax_show = $invoice_rev_details_arr[0]['invoice_revision_tax_edit'];
    }

    $tax_select = '<select name="invoice_revision_tax" id="invoice_revision_tax" class="text" onchange="inv_tax(this.value,1)">'. $tax_option .'</select>';

    
    
    
    $table = '<table border="0" width="100%" class="tbl_total">
        <tr>
            <td class="td_left">'. $AppUI->_('Total') .' :</td>
            <td class="td_left">'. $currency_symb .'<span id="inv_total_item">'. number_format($total_item_show,2) .'</span>
                                  <input type="hidden" name="hidden_invoice_item" id="hidden_invoice_item" value="'.$total_item_show.'" />
            </td>
                
        </tr>
        <tr>
            <td class="td_left">'.$AppUI->_('Discount').':</td>
            <td class="td_left">$<input id="invoice_revision_discount" onchange="load_discount();return false;" name="invoice_revision_discount" value="'.number_format($discount, 2).'"  size="10" style="text-align:right;"/></td>
        </tr>
        <tr>
<!--            <td><div id="inv_add_tax" style="float:right;"><a class="icon-add icon-all" style="margin-right:0px;" onclick="add_tax(); return false;" href="#">Add tax</a></div></td> -->
            <td class="td_left">'. $AppUI->_('GST') .' @ '. $tax_select .'% :</td>
            <td class="td_left">'. $currency_symb .' <input type="text" name="invoice_revision_tax_edit" id="inv_revision_tax" size="10" style="text-align:right;" value="'.number_format($total_item_tax_show,2).'" /></td>
        </tr>
        ' . $tr_paid . '
        <tr>
            <td class="td_left">'. $AppUI->_('Amount Due') .' :</td>
            <td class="td_left">'. $currency_symb .'<span id="inv_due">'.number_format(round($total_item_show_last_discount + $total_item_tax_show - $total_paid_and_tax,2),2) .'</span></td>
        </tr>

        </table>';

    echo '<div id="div_4">'.$table.'</div>';
//    echo $add_tax = '<div id="inv_add_tax" style="float:right;"><a class="icon-add icon-all" style="margin-right:0px;" onclick="add_tax(); return false;" href="#">Add tax</a></div>';
    echo '</br>';
    
}

function _form_inv_note($invoice_id = 0, $invoice_revision_id = 0){
global $AppUI,$CSalesManager, $CTemplateManager,$CTemplatePDF,$addView_Invoice,$editView_Invoice;
    $status = $_POST['status_rev'];
    
    $supplier_arr = $CSalesManager->get_supplier_info();
    $supplier = '<p><b>'. $supplier_arr['sales_owner_name'] .'</b></p>';
    $supplier .= '<p>'. $supplier_arr['sales_owner_address1'].', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'].'</p>';
//    $supplier .= '<p>Postal code: '. $supplier_arr['sales_owner_postal_code'] .'</p>';
    $supplier .= '<p>Tel: '. $supplier_arr['sales_owner_phone1'] .'</p>';
    $supplier .= '<p>Fax: '. $supplier_arr['sales_owner_fax'] .'</p>';
    $supplier .= '<p>Email: '. $supplier_arr['sales_owner_email'] .'</p>';
    if($supplier_arr['sales_owner_website']!="")
        $supplier .= '<p>Website: '. $supplier_arr['sales_owner_website'] .'</p>';
    if($supplier_arr['sales_owner_reg_no']!="")
        $supplier .= '<p>'. $AppUI->_('Reg No') .': '. $supplier_arr['sales_owner_reg_no'] .'</p>';
    if($supplier_arr['sales_owner_gst_reg_no']!="")
        $supplier .= '<p>'. $AppUI->_('GST Reg No') .': '. $supplier_arr['sales_owner_gst_reg_no'] .'</p>';
    // (tinhdx end)
    $sales_owner_name = $supplier_arr['sales_owner_name'];
    $sales_owner_address = $supplier_arr['sales_owner_address1'].', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'];
    
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();
    $term_area ="";
    
    if ($invoice_id != 0 && $invoice_revision_id != 0 && $status = 'update') {
        
        global $CInvoiceManager;
        
        $invoice_rev_details_arr = $CInvoiceManager->get_db_invoice_revsion($invoice_revision_id);
        
        if (count($invoice_rev_details_arr) > 0) {
            $invoice_revision = $invoice_rev_details_arr[0]['invoice_revision'];
            $note_area = $invoice_rev_details_arr[0]['invoice_revision_notes'];
            $term_area = $invoice_rev_details_arr[0]['invoice_revision_term_condition'];

        }
        
        $invoice_arr = $CInvoiceManager->get_db_invoice($invoice_id);
        $invoice_details_arr = $CInvoiceManager->get_db_invoice($invoice_id);
        $revison_lastest = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
        $revison_lastest_no = $CSalesManager->create_invoice_revision($revison_lastest[0]['invoice_revision'], null, $invoice_arr[0]['invoice_no']);
        //$AppUI->addJSScript('$(\'#invoice_revision\').val(\''.$invoice_revision.'\'); $(\'#invoice_rev_label\').append(\' <font color="red">'.$invoice_revision.'</font>\');');
        if($invoice_details_arr[0]['invoice_no']!='0')
            $AppUI->addJSScript('$(\'#invoice_revision\').val(\''.$invoice_revision.'\'); $(\'#invoice_rev_label\').append(\' <font color="red">'.$invoice_revision.'</font><input type="hidden" name="revision_lastest" id="revision_lastest" value="'.$revison_lastest_no.'" />\');');
        if($invoice_arr[0]['invoice_no']!="Draft")
        {
            if($editView_Invoice)
                $save = '<button id="btn-save-all-new-revision" class="ui-button ui-state-default ui-corner-all" onclick="save_all_invoice('. $invoice_id .', '. $invoice_revision_id .', \''. $status .'\'); return false;">Save As New Revision</button>&nbsp';
        }
        else
        {
            if($editView_Invoice)
                $save = '<button id="btn-save-all-confirm" class="ui-button ui-state-default ui-corner-all" onclick="save_confirm('. $invoice_id .', '. $invoice_revision_id .'); return false;">Confirm</button>&nbsp';
        }
    }else{
        // Lay Term And Condition Mat dinh
        $term_condition_arr = $CTemplateManager ->getlist_term_condition();
        foreach ($term_condition_arr as $term_row) {
            if($term_row['template_type']==1 && $term_row['term_default']==1){
                $term_area .= "\n ".$term_row['term_conttent'];
            }
        }
    }
    
    // btn insert term and condition
      //$insert_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="insert_term_condition(); return false;">Insert Term & Condition</button>';
     if($editView_Invoice)
        $insert_block = '<a class="icon-add icon-all" onclick="insert_term_condition(1); return false;" href="#" style="float:right;margin:7px 0px">Insert Term & Condition</a>';
     $textarea = '<textarea id="invoice_revision_notes" class="textArea" style="width: 450; height: 80;" name="invoice_revision_notes">'. $note_area .'</textarea>';
     $textarea1 = '<textarea id="invoice_revision_term_condition" class="textArea" style="width: 450; height: 80;" name="invoice_revision_term_condition" >'. $term_area .'</textarea>';
     

    $note_block = $AppUI ->createBlock('div_notes', '<b>Notes:</b> <br/>'. $textarea, 'style="float: left;"');
    
    $terms_block = $AppUI ->createBlock('div_terms', '<b>Terms and Conditions:</b> <br/>'.$textarea1 . '<br/>'.$insert_block , 'style="float: right;"');
    
//    $save_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_all_invoice('. $invoice_id .', '. $invoice_revision_id .', \''. $status .'\'); return false;">Save All</button>&nbsp;'; 
    if($status == 'add') {
        if($addView_Invoice)
        {
            $save_block = '<button id="btn-save-all-save" class="ui-button ui-state-default ui-corner-all" onclick="save_all_invoice('. $invoice_id .', '. $invoice_revision_id .', \''. $status .'\'); return false;">Save</button>&nbsp';
            $cancel_bloack = '<input type="button" value="Cancel" onclick="back_list_invoice()" class="ui-button ui-state-default ui-corner-all" />';
            $save_as_draft = '<button id="btn-save-all-as-draft" class="ui-button ui-state-default ui-corner-all" onclick="save_all_invoice('. $invoice_id .', '. $invoice_revision_id .', \''. 'as_draft' .'\'); return false;">Save as Draft</button>&nbsp';
        }
      } else {
          if($editView_Invoice)
          {
            $status = 'update_no_rev';
            $save_block = '<button id="btn-save-all-save2" class="ui-button ui-state-default ui-corner-all" onclick="save_all_invoice('. $invoice_id .', '. $invoice_revision_id .', \''. $status .'\'); return false;">Save</button>&nbsp';
          }
      }
    echo '<div id="div_5">' . $note_block . $terms_block . '</div>';
    $supplier_arr = $CSalesManager->get_supplier_info();
    
    
    $template_pdf_5 = $CTemplatePDF->get_template_pdf(5);
    $template_pdf_1 = $CTemplatePDF->get_template_pdf(1);
    if($template_pdf_5[0]['template_default']==1 && $template_pdf_5[0]['footer_text_invoice']!="")
    {
        $footer_tex = '<tr><td>'.nl2br($template_pdf_5[0]['footer_text_invoice']).'</td></tr>';
    }
    else if($template_pdf_1[0]['template_default']==1 && $template_pdf_1[0]['footer_text_invoice']!="")
    {
        $footer_tex = '<tr><td>Kindly make the cheque payable to " '.$sales_owner_name.' " and mail to '.$sales_owner_address.'.<br></td></tr>'
                . '<tr><td><b>'.nl2br($template_pdf_1[0]['footer_text_invoice']).'</b></td></tr>';
    }
    else 
    {
        $footer_tex = '
                        <tr>
                            <td>Kindly make the cheque payable to " '.$sales_owner_name.' " and mail to '.$sales_owner_address.'.<br></td>
                        </tr>
                        <tr>
                            <td><b>Please indicate invoice number when making payment.</b><br></td>
                        </tr>
                        <tr >
                            <td><b>Please note that any overdue payment/s will be listed in DP Credit Bureau\'s records and this record may be accessed by financial institutions and other approving credit companies.</b></td>
                        </tr>
            ';
    } 
    
    echo '<div style="width:100%;" id ="div_6">
            <table width="80%" border="0">
                '.$footer_tex.'
                <tr>
                    <td height="20"></td>
                </tr>
                <tr>
                    <td>Thank you & best regards</td>
                </tr>
                <tr>
                    <td height="30"> </td>
                </tr>
                <tr>
                    <td>Yours sincerely</td>
                </tr>
                <tr>
                    <td height="30"> </td>
                </tr>
                <tr>
                    <td><b>Authorised Signature</b></td>
                <tr>
                <tr >
                    <td height="25"> </td>
                </tr>
            </table>
        </div>';
    echo $AppUI ->createBlock('btt_block',  $save . $save_block.$save_as_draft. $cancel_bloack , 'style="text-align: center; margin:0px 0px -15px;"');
    echo '</br>';
}

/* Ket thuc view invoice details */


function _do_update_invoice_field() {

    global $CInvoiceManager, $CSalesManager;

    $id = dPgetCleanParam($_POST, 'id');
    $updated_content = dPgetCleanParam($_POST, 'value');
    
    $status = dPgetCleanParam($_GET, 'status');
    $col_name = dPgetCleanParam($_GET, 'col_name', '');

    $msg = $CInvoiceManager->update_invoice_field($id, $updated_content, $col_name);
    if (!$msg) {
        if ($col_name == 'invoice_status') {
            $invoice_stt = dPgetSysVal('InvoiceStatus');
            echo $invoice_stt[$updated_content];
            // insert history
            $msg = $CInvoiceManager->insert_history_invoice_status($id, $updated_content, $status);
        } else if ($col_name == 'customer_id') {
            $rows = $CSalesManager->get_list_companies();
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    if (intval($row['company_id']) == intval($updated_content)) {
                        echo $row['company_name'];
                        break;
                    }
                }
            }
        }
        else if($col_name == 'invoice_subject'){
            echo $CSalesManager->htmlChars($updated_content);
        }
        else if($col_name == 'invoice_date'){
            echo $updated_content;
        }
    }
        
    //else
        //echo $updated_content;
}

function change_revision_html() {
    
    global $AppUI, $CInvoiceManager;

    $invoice_id = $_POST['invoice_id'];
    $invoice_revision_id = $_POST['invoice_revision_id'];

    $invoice_revision_arr = $CInvoiceManager->list_invoice_revision($invoice_id);

    $p = '';
    if (count($invoice_revision_arr) > 0) {
        $i = 1;
        foreach ($invoice_revision_arr as $invoice_revision) {
            $a_1 = '<a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_revision['invoice_id'].'&invoice_revision_id='.$invoice_revision['invoice_revision_id'].'" onclick="load_invoice('. $invoice_revision['invoice_id'] .', '. $invoice_revision['invoice_revision_id'] .', \'update\'); return false;">';
            $a_2 = '</a>';
            if ($invoice_revision_id == $invoice_revision['invoice_revision_id'])
                $p .= '<p>'. $a_1 .'<font color="#999"><b>'. $i .'.</b> '. $invoice_revision['invoice_revision'] .'</font>'. $a_2 .'</p>';
            else
                $p .= '<p>'. $a_1 .'<b>'. $i .'.</b> '. $invoice_revision['invoice_revision'] .''. $a_2 .'</p>';
            $i++;
        }
    } else {
        $p .= 'Invoice Revision no found';
    }
    
    if ($invoice_revision_id) {
        $invoice_no = $CInvoiceManager->get_invoice_field_by_id($invoice_id, 'invoice_no');
   
        echo '<p><h1>Invoice No: '.$invoice_no.'</h1></p><br/>'. $p .'<br/>';
    }
    else
        echo $p;

}

//function _do_add_invoice() {
//    
//    global $CInvoiceManager, $AppUI;
//    
//    $_POST['user_id'] = $AppUI->user_id;
//    
//            //print_r($_POST);
//            //xprint_r($item_id);
//    $invoice_id = $CInvoiceManager->add_invoice($_POST); // ham nay thuc hien 2 chuc nang add va update, neu gia tri invoice_id co trong csdl thi thuc hien update, k thi add
//    if ($invoice_id > 0) {
//        if ($_POST['status_rev'] == 'add') {
//            $_POST['invoice_id'] = $invoice_id;
//            //$_POST['invoice_revision'] = $CSalesManager->create_invoice_or_quotation_revision('', $invoice_no);
//            $invoice_revision_id = $CInvoiceManager->add_invoice_revision($_POST);
//            if ($invoice_revision_id > 0) {
//                _do_add_invoice_item($invoice_id, $invoice_revision_id);
//                echo '{status: "Success", invoice_id: ' . $invoice_id . ', invoice_revision_id: '. $invoice_revision_id .'}';
//            } else
//                echo '{status: "Failure", message: "sai roi"}';
//        } elseif($_POST['status_rev'] == 'update') {
//            if ($CInvoiceManager->check_payment_id($_POST['invoice_id']) == false) {
//                if (intval($invoice_id) == intval($_POST['invoice_id'])) { // khi update return invoice_id ma = $_POST['invoice_id'] gui len thi thuc hien tiep
//
//                    $invoice_revision_id_lastest = $CInvoiceManager->get_invoice_revision_lastest($invoice_id); // lay ra invoice_revision_id cuoi cung
//                    $invoice_revision_arr = $CInvoiceManager->get_db_invoice_revsion($invoice_revision_id_lastest); // truy van lay ra invoice_revision tai invoice_revision_id
//
//                    if (count($invoice_revision_arr) > 0) {
//                        $_POST['invoice_revision'] = $invoice_revision_arr[0]['invoice_revision'];
//
//                        $invoice_revision_id_new = $CInvoiceManager->update_invoice_revision($_POST['invoice_revision_id'], array(), $_POST['invoice_item_id']);
//                        if ($invoice_revision_id_new > 0) {
//                            echo '{status: "Success", invoice_id: ' . $invoice_id . ', invoice_revision_id: '. $invoice_revision_id_new .'}';
//                        } else
//                            echo '{status: "Failure", message: "sai roi"}';
//                    }
//                }
//            }
//            else 
//                echo '{status: "Failure", message: "Ton tai payment! Khong the sua."}';
//        }
//    } else {
//        echo '{status: "Failure", message: "sai roi"}';
//    }
//
//}
function _do_add_invoice() {
    global $CInvoiceManager,$CSalesManager, $AppUI;
    $invoice_rev_current_id = $_POST['invoice_revision_id']; 
    $invoice_rev_no_current = $_POST['invoice_revision'];
    
    // Xu ly du gia tri tax truoc khi luu vao CSDL
    $_POST['invoice_revision_tax_edit'] = str_replace(",", "", $_POST['invoice_revision_tax_edit']);
    $_POST['invoice_revision_discount'] = str_replace(",", "", $_POST['invoice_revision_discount']);
    $_POST['invoice_revision_tax_edit'] = $CSalesManager->round_up($_POST['invoice_revision_tax_edit']);
    
    $_POST['user_id'] = $AppUI->user_id;
    $invoice_arr = $CInvoiceManager->get_invoice_revision($_POST['invoice_id']);
    if($_POST['count_click'] == 1) { // neu co edit invoice_no 
    $invoice_arr_exit = $CInvoiceManager->get_db_invoice_already($_POST['invoice_no']); // Neu sau khi edit, invoice_no trung lap voi invoice_no khac thi dua ra thong bao
    if($invoice_arr_exit) {
//       if(isset($_POST['invoice_no'])) {
          echo '{"status": "Failure", "message": "Invoice Number already exists. A new number has been used."}'; //Thong bao khi trung lap invoice_no
    } else { // Neu khong trung lap thi thuc hien add hoac update binh thuong...
        
        if($_POST['status_rev'] == 'update_no_rev') {
            $msg = $CInvoiceManager->update_invoice($_POST['invoice_id'], $_POST['invoice_no'], $_POST['invoice_date'], $_POST['address_id'], $_POST['invoice_sale_person'], $_POST['invoice_sale_person_email'], $_POST['invoice_sale_person_phone'], $_POST['customer_id'], $_POST['attention_id'], $_POST['our_delivery_order_no'], $_POST['job_location_id'], $_POST['term'],$_POST['po_number'],$_POST['sub_heading'],$_POST['department_id']);
            $msg = $CInvoiceManager->update_invoice_no_revision($_POST['invoice_id'], $_POST['invoice_revision'], $_POST['invoice_revision_id'], $_POST['invoice_revision_notes'], $_POST['invoice_revision_tax'], $_POST['invoice_revision_term_condition'], $_POST['invoice_revision_tax_edit'], $_POST['invoice_revision_discount'],$_POST['reference_no']);
            if(!$msg) {
                _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                _do_add_attention_in_invoice($_POST['invoice_id']);
                $CInvoiceManager->insert_history_invoice($invoice_id, $invoice_rev_no_current, $_POST['invoice_revision'], "update_no_rev");
                if($_POST['click_edit_inv']=="" || $_POST['click_edit_inv']<=0)
                    $exit_item = _do_add_invoice_item($_POST['invoice_id'], $_POST['invoice_revision_id']);
                echo '{"status": "Success","exist_item":"'.$exit_item.'", "invoice_id": ' . $_POST['invoice_id'] . ', "invoice_revision_id": '. $_POST['invoice_revision_id'] .'}';
            } else {
                echo '{"status": "Failure", "message": "sai roi"}';
            }
        }
        if($_POST['contact_coordinator_id']!="")
            $_POST['contact_coordinator_id']=$AppUI->user_id;
        if($_POST['status_rev']=="as_draft")
        {
            $_POST['invoice_no']="Draft";
            $_POST['invoice_status'] = 5;
        }
        
        $invoice_id = $CInvoiceManager->add_invoice($_POST); // ham nay thuc hien 2 chuc nang add va update, neu gia tri invoice_id co trong csdl thi thuc hien update, k thi add
            if ($invoice_id > 0) {
                _do_add_contract_invoice($invoice_id, $_POST); // add contract
                if ($_POST['status_rev'] == 'add') {
                    $_POST['invoice_id'] = $invoice_id;
                    $invoice_revision_id = $CInvoiceManager->add_invoice_revision($_POST);
                    if ($invoice_revision_id > 0) {
                        _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                        _do_add_attention_in_invoice($invoice_id);
                        $exit_item = _do_add_invoice_item($invoice_id, $invoice_revision_id);
                        $CInvoiceManager->insert_history_invoice($invoice_id, $invoice_revision_id, $_POST['invoice_revision'], "add");
                        echo '{"status": "Success","exist_item":"'.$exit_item.'", "invoice_id": ' . $invoice_id . ', "invoice_revision_id": '. $invoice_revision_id .'}';
                    } else
                        echo '{"status": "Failure", "message": "sai roi"}';
                } elseif($_POST['status_rev'] == 'update') {
                    if ($CInvoiceManager->check_payment_id($_POST['invoice_id']) == false) {    
                        
                        if (intval($invoice_id) == intval($_POST['invoice_id'])) { // khi update return invoice_id ma = $_POST['invoice_id'] gui len thi thuc hien tiep
                            _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                            _do_add_attention_in_invoice($_POST['invoice_id']);
                            if (count($invoice_arr) > 0) {
                                $suffix_count = 3;
                                foreach ($invoice_arr as $invoice) {
                                    $invoice_rev_id = $invoice['invoice_revision_id'];
                                    $invoice_rev = $invoice['invoice_revision'];
                                    $obj_revision_substr = substr($invoice_rev, -$suffix_count); // cat lay 3 ky tu cuoi cung trong chuoi 
                                    $msg = $CInvoiceManager->updateInvoice_Revision($invoice_rev_id, $_POST['invoice_no'], $obj_revision_substr);
                                }
                                
                                $invoice_revision_id_new = $CInvoiceManager->update_invoice_revision($_POST['invoice_revision_id'], array(), $_POST['invoice_item_id']);
                                if ($invoice_revision_id_new > 0) {
                                    // insert history invoice update
                                    $msg = $CInvoiceManager->insert_history_invoice($_POST['invoice_id'], $invoice_rev_no_current, $_POST['invoice_revision'], "update");
                                    //_do_add_invoice_item($invoice_id, $invoice_revision_id_new);
                                    echo '{"status": "Success", "invoice_id": ' . $invoice_id . ', "invoice_revision_id": '. $invoice_revision_id_new .'}';
                                } else
                                    echo '{"status": "Failure", "message": "error"}';
                            }
                        }
                    }
                    else 
                        echo '{"status": "Failure", "message": "Payment already exists! Can not edit"}';
                }
                // Add invoice as draft
                elseif($_POST['status_rev'] == 'as_draft')
                {
                    $_POST['invoice_id'] = $invoice_id;
                    $_POST['invoice_no'] = "Draft";
                    $_POST['invoice_revision'] = "Draft";
                    
                    $invoice_revision_id = $CInvoiceManager->add_invoice_revision($_POST);
                    if ($invoice_revision_id > 0) {
                        _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                        _do_add_attention_in_invoice($invoice_id);
                        $exit_item = _do_add_invoice_item($invoice_id, $invoice_revision_id);
                        $CInvoiceManager->insert_history_invoice($invoice_id, $invoice_revision_id, $_POST['invoice_revision'], "add");
                        echo '{"status": "Success","exist_item":"'.$exit_item.'", "invoice_id": ' . $invoice_id . ', "invoice_revision_id": '. $invoice_revision_id .'}';
                    } else
                        echo '{"status": "Failure", "message": "sai roi"}';
                }
                
            } else {
                echo '{"status": "Failure", "message": "sai roi"}';
            }
     }
    } else  {
    $invoice_exit = $CInvoiceManager->get_db_invoice_already($_POST['invoice_no']); // Neu sau khi edit, invoice_no trung lap voi invoice_no khac thi dua ra thong bao
    if($invoice_exit && $_POST['status_rev']=='add') {
//       if(isset($_POST['invoice_no'])) {
          echo '{"status": "Failure", "message": "Invoice Number already exists. A new number has been used."}';
          
     }else{
        if($_POST['contact_coordinator_id']!="")
            $_POST['contact_coordinator_id']=$AppUI->user_id;
        if($_POST['status_rev'] == "as_draft")
        {
            $_POST['invoice_no'] = "Draft";
            $_POST['invoice_status'] = 5;
        }
        $invoice_id = $CInvoiceManager->add_invoice($_POST); // ham nay thuc hien 2 chuc nang add va update, neu gia tri invoice_id co trong csdl thi thuc hien update, k thi add

        if ($invoice_id > 0) {
            _do_add_contract_invoice($invoice_id, $_POST);
            if($_POST['status_rev'] == 'update_no_rev') {
                $msg = $CInvoiceManager->update_invoice($_POST['invoice_id'], $_POST['invoice_no'], $_POST['invoice_date'], $_POST['address_id'], $_POST['invoice_sale_person'], $_POST['invoice_sale_person_email'], $_POST['invoice_sale_person_phone'], $_POST['customer_id'], $_POST['attention_id'], $_POST['our_delivery_order_no'], $_POST['job_location_id'], $_POST['term'],$_POST['po_number'],$_POST['sub_heading']);
                $msg = $CInvoiceManager->update_invoice_no_revision($_POST['invoice_id'], $_POST['invoice_revision'], $_POST['invoice_revision_id'], $_POST['invoice_revision_notes'], $_POST['invoice_revision_tax'], $_POST['invoice_revision_term_condition'],$_POST['invoice_revision_tax_edit'],$_POST['invoice_revision_discount'],$_POST['reference_no']);
                if(!$msg) {
                    _do_add_attention_in_invoice($_POST['invoice_id']);
                    _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                    $CInvoiceManager->insert_history_invoice($invoice_id, $invoice_rev_no_current, $_POST['invoice_revision'], "update_no_rev");
                    if($_POST['click_edit_inv']=="" || $_POST['click_edit_inv']<=0)
                        $exit_item = _do_add_invoice_item($_POST['invoice_id'], $_POST['invoice_revision_id']);
                    echo '{"status": "Success","exist_item":"'.$exit_item.'", "invoice_id": ' . $_POST['invoice_id'] . ', "invoice_revision_id": '. $_POST['invoice_revision_id'] .'}';
                } else {
                    echo '{"status": "Failure", "message": "sai roi"}';
                }
            }
            if ($_POST['status_rev'] == 'add') {
                $_POST['invoice_id'] = $invoice_id;
                $invoice_revision_id = $CInvoiceManager->add_invoice_revision($_POST);
                if ($invoice_revision_id > 0) {
                    _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                    _do_add_attention_in_invoice($invoice_id);
                    $exit_item = _do_add_invoice_item($invoice_id, $invoice_revision_id);
                    $CInvoiceManager->insert_history_invoice($invoice_id, $invoice_revision_id, $_POST['invoice_revision'], "add");
                    echo '{"status": "Success","exist_item":"'.$exit_item.'", "invoice_id": ' . $invoice_id . ', "invoice_revision_id": '. $invoice_revision_id .'}';
                } else
                    echo '{"status": "Failure", "message": "error"}';
            } elseif($_POST['status_rev'] == 'update') {
                if ($CInvoiceManager->check_payment_id($_POST['invoice_id']) == false) { 
                    if (intval($invoice_id) == intval($_POST['invoice_id'])) { // khi update return invoice_id ma = $_POST['invoice_id'] gui len thi thuc hien tiep
                        _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                        _do_add_attention_in_invoice($_POST['invoice_id']);
                        if (count($invoice_arr) > 0) {
                            $suffix_count = 3;
                            foreach ($invoice_arr as $invoice) {
                                $invoice_rev_id = $invoice['invoice_revision_id'];
                                $invoice_rev = $invoice['invoice_revision'];
                                $obj_revision_substr = substr($invoice_rev, -$suffix_count); // cat lay 3 ky tu cuoi cung trong chuoi 
                                $msg = $CInvoiceManager->updateInvoice_Revision($invoice_rev_id, $_POST['invoice_no'], $obj_revision_substr);
                            }
                            
                            $invoice_revision_id_new = $CInvoiceManager->update_invoice_revision($_POST['invoice_revision_id'], array(), $_POST['invoice_item_id']);
                            if ($invoice_revision_id_new > 0) {
                                //_do_add_invoice_item($invoice_id, $invoice_revision_id_new);
                                 // insert history invoice update
                                 $msg = $CInvoiceManager->insert_history_invoice($_POST['invoice_id'], $invoice_rev_no_current, $_POST['invoice_revision'], "update");
                                echo '{"status": "Success", "invoice_id": ' . $invoice_id . ', "invoice_revision_id": '. $invoice_revision_id_new .',"invoice_no":"'.$_POST['invoice_no'].'"}';
                            } else
                                echo '{"status": "Failure", "message": "Failure"}';
                        }
                    }
                }
                    else 
                        echo '{"status": "Failure", "message": "Payment already exists! Can not edit"}';
            }
            // Add invoice as draft
            elseif($_POST['status_rev'] == 'as_draft')
            {
                $_POST['invoice_id'] = $invoice_id;
                $_POST['invoice_no'] = "Draft";
                $invoice_revision = "";
                $_POST['invoice_revision'] = "Draft";
                
                $invoice_revision_id = $CInvoiceManager->add_invoice_revision($_POST);
                if ($invoice_revision_id > 0) {
                    _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                    _do_add_attention_in_invoice($invoice_id);
                    $exit_item = _do_add_invoice_item($invoice_id, $invoice_revision_id);
                    $CInvoiceManager->insert_history_invoice($invoice_id, $invoice_revision_id, $_POST['invoice_revision'], "add");
                    echo '{"status": "Success","exist_item":"'.$exit_item.'", "invoice_id": ' . $invoice_id . ', "invoice_revision_id": '. $invoice_revision_id .'}';
                } else
                    echo '{"status": "Failure", "message": "sai roi"}';
            }
        } else {
            echo '{"status": "Failure", "message": "Failure"}';
        }
        }
    }
}

/*
 function _do_add_invoice() {
    global $CInvoiceManager, $AppUI;
    $_POST['user_id'] = $AppUI->user_id;
    $invoice_id = $CInvoiceManager->add_invoice($_POST); // ham nay thuc hien 2 chuc nang add va update, neu gia tri invoice_id co trong csdl thi thuc hien update, k thi add
    if ($invoice_id > 0) {
        if ($_POST['status_rev'] == 'add') {
            $_POST['invoice_id'] = $invoice_id;
            //$_POST['invoice_revision'] = $CSalesManager->create_invoice_or_quotation_revision('', $invoice_no);
            $invoice_revision_id = $CInvoiceManager->add_invoice_revision($_POST);
            if ($invoice_revision_id > 0) {
                _do_add_invoice_item($invoice_id, $invoice_revision_id);
                echo '{status: "Success", invoice_id: ' . $invoice_id . ', invoice_revision_id: '. $invoice_revision_id .'}';
            } else
                echo '{status: "Failure", message: "sai roi"}';
        } elseif($_POST['status_rev'] == 'update') {
            if ($CInvoiceManager->check_payment_id($_POST['invoice_id']) == false) {
                if (intval($invoice_id) == intval($_POST['invoice_id'])) { // khi update return invoice_id ma = $_POST['invoice_id'] gui len thi thuc hien tiep

                    $invoice_revision_id_lastest = $CInvoiceManager->get_invoice_revision_lastest($invoice_id); // lay ra invoice_revision_id cuoi cung
                    $invoice_revision_arr = $CInvoiceManager->get_db_invoice_revsion($invoice_revision_id_lastest); // truy van lay ra invoice_revision tai invoice_revision_id

                    if (count($invoice_revision_arr) > 0) {
                        $_POST['invoice_revision'] = $invoice_revision_arr[0]['invoice_revision'];

                        $invoice_revision_id_new = $CInvoiceManager->update_invoice_revision($_POST['invoice_revision_id'], array(), $_POST['invoice_item_id']);
                        if ($invoice_revision_id_new > 0) {
                            echo '{status: "Success", invoice_id: ' . $invoice_id . ', invoice_revision_id: '. $invoice_revision_id_new .'}';
                        } else
                            echo '{status: "Failure", message: "sai roi"}';
                    }
                }
            }
            else 
                echo '{status: "Failure", message: "Ton tai payment! Khong the sua."}';
        }
    } else {
        echo '{status: "Failure", message: "sai roi"}';
    }
 }
*/

function _do_add_invoice_item($invoice_id, $invoice_revision_id) {
    global $CInvoiceManager, $AppUI;
    $item = $_POST['invoice_item'];
    //$item = $_POST['invoice_arrr_item'];
    $price = $_POST['invoice_item_price'];
    $quantity = $_POST['invoice_item_quantity'];
    $discount = $_POST['invoice_item_discount'];
    if($_POST['invoice_order']!="")
        $order = $_POST['invoice_order'];
    else
        $order = $_POST['invoice_order_hdd'];
    if (($count = count($item)) > 0) {
        
        $item_obj = array();
        
            $item_obj['user_id'] = $AppUI->user_id;
            $item_obj['invoice_id'] = $invoice_id;
            $item_obj['invoice_revision_id'] = $invoice_revision_id;
            
        for ($i = 0; $i < $count; $i++) {
            $check_invoice_item = $CInvoiceManager->check_item_invoice($invoice_id, $invoice_revision_id, $item[$i]);
            if($check_invoice_item==true)
            {
                $item_obj['invoice_item_id'] = 0;
                $item_obj['invoice_item'] = $item[$i];
                $item_obj['invoice_item_price'] = round($price[$i],2);
                $item_obj['invoice_item_quantity'] = round($quantity[$i],2);
                $item_obj['invoice_item_discount'] = floatval($discount[$i]);
                $item_obj['invoice_item_type'] = 0;
                $item_obj['invoice_item_notes'] = null;
                $item_obj['invoice_order'] = $order[$i];
                $CInvoiceManager->add_invoice_item($item_obj);
            }
            else
                $exist=1;
        }
        return $exist;
    }
    
}

    function _do_send_email_invoice() {
        global $CInvoiceManager;
        if ($_POST['action'] == 'send_mail_in') {
            $invoice_id_array = $_POST['invoice_id'];
            $content = $_POST['content'];
            $reciver = $_POST['reciver'];
            $sender = $_POST['sender'];
            $subject = $_POST['subject'];
            $invoice_revision_id_array = $_POST['invoice_revision_id'];

            $status = $CInvoiceManager->send_mail_invoice_revision($invoice_id_array, $invoice_revision_id_array, $content, $sender, $subject, $reciver);
        } elseif ($_POST['action'] == 'send_mail_beside') {

            $status = $CInvoiceManager->send_mail_invoice_revision($_GET['invoice_id']);
        } 
        if ($status)
                echo '{"status": "Success", "message": "Email sent"}';
            else
                echo '{"status": "Failure", "message": "Email sent error"}';
    }

function _do_print_invoice() {

    global $CInvoiceManager;
    
    $invoice_id = $_REQUEST['invoice_id'];
    $invoice_revision_id = $_REQUEST['invoice_revision_id'];
    
    if (!$invoice_revision_id)
        $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);

    $CInvoiceManager->create_invoice_revision_pdf_file($invoice_id, $invoice_revision_id);

}

function _do_remove_invoice() {

    global $CInvoiceManager;

    $invoice_id_arr = $_REQUEST['invoice_id'];
    
    $db_return = $CInvoiceManager->remove_invoice($invoice_id_arr);
    
    if ($db_return){   
        //Update service Oder thanh done neu co
        if($invoice_id != 0 && $invoice_revision_id != 0){
            echo $invoice_id;
            $serviceOder_arr = $CSalesManager->get_serviceoder_id($invoice_id, 'invoice');
            echo $serviceOder_id = $serviceOder_arr[0]['service_order_id'];
        }
        echo '{"status": "Success"}';
    }
    else
        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';

}

function _do_remove_invoice_item() {

    global $CInvoiceManager;

    $invoice_item_id_arr = $_REQUEST['invoice_item_id'];
    $invoice_id = $_REQUEST['invoice_id'];
    $invoice_revision_id = $_REQUEST['invoice_revision_id'];
    
    $deleteItem = $CInvoiceManager->remove_invoice_item($invoice_item_id_arr);
    if($deleteItem)
        echo '{"status": "Success", "invoice_id": '. $invoice_id .', "invoice_revision_id": '. $invoice_revision_id .'}';
    else {
        echo '{"status": "Failure", "message": "Error"}';
    }
    
//    $invoice_revision_id_lastest = $CInvoiceManager->get_invoice_revision_lastest($invoice_id); // lay ra invoice_revision_id cuoi cung
//    $invoice_revision_arr = $CInvoiceManager->get_db_invoice_revsion($invoice_revision_id_lastest); // truy van lay ra invoice_revision tai invoice_revision_id
//    
//    if (count($invoice_revision_arr) > 0) {
//        $_POST['invoice_revision'] = $invoice_revision_arr[0]['invoice_revision'];
//
//        $invoice_revision_id_new = $CInvoiceManager->update_invoice_revision($invoice_revision_id, $invoice_item_id_arr); // goi den ham xu ly tao invoice revision moi va update cac bang lien quan
//        if ($invoice_revision_id_new > 0) { // viec update da thanh cong
//            echo '{"status": "Success", "invoice_id": '. $invoice_id .', "invoice_revision_id": '. $invoice_revision_id_new .'}';
//        } else {
//            echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
//        }
//    } else
//        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
    
}

function _do_remove_invoice_revision() {
    global $CInvoiceManager;
    $invoice_id = $_POST['invoice_id'];
    $invoice_revision_arr = $CInvoiceManager->get_db_invoice_revsion($_POST['invoice_revision_id']);
    if ($_POST['invoice_id'] && $_POST['invoice_revision_id']) {
        $invoice_revision_id_arr = array($_POST['invoice_revision_id']);
        $invoice_id_arr = array($_POST['invoice_id']);
        $return = $CInvoiceManager->remove_invoice_revision($invoice_revision_id_arr);
        if ($return) {
            $CInvoiceManager->insert_history_invoice($invoice_id,$invoice_revision_arr[0]['invoice_revision'], false, "delete");
            $invoice_revision_id_lastest = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
            echo '{"status": "Success", "invoice_id": '.$invoice_id.', "invoice_revision_id":'.$invoice_revision_id_lastest.'}';
        }
        else
            echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
    }
}

function _get_customer_inline() {
    
    global $CSalesManager;
    
    $rows = $CSalesManager->get_list_companies();

    $customer_arr = array();
    foreach ($rows as $row) {
        $customer_arr[$row['company_id']] = $row['company_name'];
    }
    print json_encode($customer_arr);

}


function _form_send_mail() {
    global $AppUI, $CInvoiceManager;
    require_once($AppUI->getSystemClass( 'libmail' ));
    $mail = new Mail; // create the mail
    $invoice_id = $_REQUEST['invoice_id'];
    $invoice_revision_id = $_REQUEST['invoice_revision_id'];
    if (!$invoice_revision_id){
        $invoice_revision_id = $CInvoiceManager->get_latest_invoice_revision($invoice_id);
    }
    $attention_id = $_REQUEST['attention_id'];
    $invoice_details_arr = $CInvoiceManager->get_db_invoice_revsion($invoice_revision_id);
    $invoice_rev = $invoice_details_arr[0]['invoice_revision'];
    $content = 'chua config noi dung';
    $from = CSalesManager::get_customer_email_invoice($invoice_id);
    $to = CSalesManager::get_attention_email($attention_id);
    $att = $CInvoiceManager->create_invoice_revision_pdf_file($invoice_id, $invoice_revision_id, true);
    ?>

	<form action = "index.php?m=sales" method="POST" name="send_email_quo" id="send_email_quo">
        <table id="myTable" align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr style="width:95%;" >
			<td style="text-indent:10px;">From</td>
			<td> <input type="text" value="<?php echo $AppUI->_($from);?>" style=" width:95%;" name="sender" id="sender"></td>
		</tr>
		<tr style="width:95%;">
			<td style="text-indent:10px;">To</td>
			<td><input type="text" value="<?php echo $AppUI->_($to);?>" style=" width:95%;" name="reciver" id="reciver"></td>
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
            <input id="file_attach" type="text" name="name_file" value="<?php echo $invoice_rev .'.pdf';?>" readonly="true">
             <input class="mainbutton" type="submit" onclick="print_invoice(<?php echo $invoice_id; ?>, <?php echo $invoice_revision_id; ?>); return false;" value="Download" name="dowload_file">
            </td>
        </tr>
        
        <tr>
            <td align="center" colspan="2">
            <input class="mainbutton" type="button" onclick="setSendButton(<?php echo $invoice_id; ?>, <?php echo $invoice_revision_id; ?>)" value="Send" name="send" />
            <input class="mainbutton" type="reset" value="Reset" name="back" />
            </td>
        </tr>
        
        </table>
	</form>

    <?php
}


function change_quotation_revision_html() { // hien thi quotation_no 
    
    global $AppUI, $CInvoiceManager;

    $quotation_id = $_POST['quotation_id'];
    
    if ($quotation_id) {
        $quotation_no = $CInvoiceManager->get_quotation_field_by_id($quotation_id, 'quotation_no');
        echo '<p><h1>Quotation No: <a target="_blank" href="?m=sales">'.$quotation_no.'</a></h1></p><br/>'. $p .'<br/>';
    }
    else
        echo $p;

}

function _do_view_history() {
    global $AppUI, $CInvoiceManager; 
    $history_array = $CInvoiceManager->get_list_db_history($_POST['incoive_id']);
    
    $html = '<div id="history">
            <table id="myTable" align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <th>Date Time</th>
                <th>Description</th>
                <th>User</th>
             </tr>';
             if(count($history_array) > 0) {
                 foreach ($history_array as $row) {
                     $html .= '<tr>
                                <td>'.$row['quo_invc_history_date'].'</td>
                                <td>'.$row['quo_invc_history_update'].'</td>
                                <td>';
                                    $html .= $CInvoiceManager->get_user_change($row['quo_invc_history_user']);
                                 $html .= '</td>
                            </tr>';
                 }
             }
            $html .= '</table>
        </div>';
    echo $html;
}


function _do_generate_print() {
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

function _do_print_html() {
    global $CInvoiceManager;
    
    $invoice_id = $_REQUEST['invoice_id'];
    $invoice_revision_id = $_REQUEST['invoice_revision_id'];
    
    if (!$invoice_revision_id)
        $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);

    $CInvoiceManager->create_invoice_revision_html($invoice_id, $invoice_revision_id);
}

/*
 *  USE TEMPLATE
 */
function _do_getlist_template() {
global $CTemplateManager;
    $list_template = $CTemplateManager->getlist_template_for_Invoice();
    $html = '<div>';
    $html .='<table id="list_template" class="tbl" cellspacing="1" cellpadding="2" style="clear: both; width: 100%">
            <tr>
                <th>#</th>
                <th>Template</th>
                <th class="check_option"></th>
            </tr>';
    $i = 1;
    foreach ($list_template as $template) {
        $html .= '<tr>
                    <td>'.$i.'</td>
                    <td>'.$template['templ_name'].'</td>
                    <td><input type="checkbox" id="check_template" name="check_template[]" value="'.$template['templ_id'].'" /></td>
                </tr>
                <tr><input type="hidden" id="count_check" name="count_check" value="'.count($list_template).'"/></tr>
                <tr><input type="hidden" id="invoice_id" name="invoice_id" value="'.$_POST['invoice_id'].'"/></tr>
                <tr><input type="hidden" id="invoice_rev_id" name="invoice_rev_id" value="'.$_POST['invoice_rev_id'].'"/></tr>';
        $i++;
    }
    $html .= '</table></div>';
    echo $html;
}

function _do_insert_template_invoice() {
    global $AppUI, $CTemplateManager, $CInvoiceManager;
//    $template_id = $_POST['template_id'];
    $status_rev = $_POST['status_rev'];
    $_POST['user_id'] = $AppUI->user_id;
    $_POST['invoice_template'] = $_POST['template_id'];
    
    // get items of template
    $template_items = $CTemplateManager->get_db_template_item($_POST['template_id']);
    // get notes of template
    $template_notes = $CTemplateManager->get_db_note_template($_POST['template_id']);
    if (count($template_notes) > 0) {
        $_POST['invoice_revision_notes'] = $template_notes[0]['note_temp_content'];
    }
    // get term and codition of template
    $template_term = $CTemplateManager->getdb_term_condition($_POST['template_id']);
    if (count($template_term) > 0) {
        $_POST['invoice_revision_term_condition'] = $template_notes[0]['note_temp_content'];
    }
    // get subheading of template
    $subheading = $CTemplateManager->get_db_sub_heading_template($_POST['template_id']);
    if(count($subheading)>0)
    {
        $_POST['sub_heading'] = $subheading[0]['sub_heading_content'];
    }
    //do add quotation
    $invoice_id = $CInvoiceManager->add_invoice($_POST);
    if ($_POST['status_rev'] == 'add') {
            $_POST['invoice_id'] = $invoice_id;
            $invoice_revision_id = $CInvoiceManager->add_invoice_revision($_POST);
                if ($invoice_revision_id > 0) {
                    if (count($template_items) > 0) {
                        _do_insert_item_invoice($template_items, $invoice_id, $invoice_revision_id);
                    }
                    echo '{"status": "Success", "invoice_id": ' . $invoice_id . ', "invoice_revision_id": '. $invoice_revision_id .'}';
                }
    }
}

function _do_insert_item_invoice($template_items, $invoice_id, $invoice_rev_id) {
    global $CInvoiceManager, $AppUI;
    if (count($template_items) > 0) {
            $itemObj = array();
            
            $itemObj['user_id'] = $AppUI->user_id;
            $itemObj['invoice_id'] = $invoice_id;
            $itemObj['invoice_revision_id'] = $invoice_rev_id;
            
            foreach ($template_items as $template) {
                $itemObj['invoice_item_id'] = 0;
                $itemObj['invoice_item'] = $template['item_temp_item'];
                $itemObj['invoice_item_price'] = round($template['item_temp_price'],2);
                $itemObj['invoice_item_quantity'] = round($template['item_temp_quan'],2);
                $itemObj['invoice_item_discount'] = intval($template['item_temp_discount']);
                $itemObj['invoice_item_type'] = 0;
                $itemObj['invoice_item_notes'] = null;
                $CInvoiceManager->add_invoice_item($itemObj);
            }
            
    }
}
function _do_update_status_invoice(){
    global $CInvoiceManager;
    $invoice_id = $_POST['invoice_id'];
    $invoice_arr=$CInvoiceManager->get_db_invoice($invoice_id);
    if($invoice_arr>0){
        foreach ($invoice_arr as $invoice_row){
            $date=$invoice_row['invoice_date'];
            $term=$invoice_row['term'];
            $day_between1 =round((strtotime(date("Y/m/d"))-strtotime($date))/86400);
        }
        
        $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
        $total_owe = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, round($CInvoiceManager->get_invoice_item_total($invoice_id, $invoice_revision_id),2));
        
        $status_update=$CInvoiceManager->get_check_term($date, $term, $total_owe);
        if($status_update=="Paid"){
            $status_id = 2;
        }else if($status_update=="Partial"){
            $status_id = 3;
        }
        else if($status_update == "Overdue")
            $status_id = 1;

        $update_st=$CInvoiceManager->update_status_invoice($invoice_id, $status_id);
        if (intval($update_st) > 0) {
            echo '{"status": "Success"}';
        } else {
            echo '{"status": "Failure", "message": "Co loi trong qua trinh update","status_id":"'.$total_owe.','.$status_update.','.$day_between1.'"}';
        }
    }
}
function _do_update_status_invoice1(){
    global $CInvoiceManager;
   
    if(isset($_GET['invoice_id']) || isset($_POST['invoice_save_id']))
    {
        if(isset($_POST['invoice_save_id']))
            $invoice_id = $_POST['invoice_save_id'];
        if(isset($_GET['invoice_id']))
            $invoice_id = $_GET['invoice_id'];
        
        $invoice_arr = $CInvoiceManager->get_invoice_statusNone(false,$invoice_id);
    }
    else
    {
        if(isset($_POST['invoice_save_id'])){
            $invoice_arr = $CInvoiceManager->get_invoice_statusNone();
            $invoice_arr = $CInvoiceManager->get_invoice_statusNone(false,$_POST['invoice_save_id']);
        }
        if(isset($_POST['update_payment'])){
            $invoice_arr = $CInvoiceManager->get_invoice_statusNone();
            $invoice_arr = $CInvoiceManager->get_invoice_statusNone($_POST['update_payment']);
        }
        if(isset($_POST['invoice_id']))
        {
            $invoice_id = $_POST['invoice_id'];
            $invoice_arr = $CInvoiceManager->get_invoice_statusNone(false,$invoice_id);
        }
        else
            $invoice_arr = $CInvoiceManager->get_invoice_statusNone();
    }   
//    if(isset($_GET['invoice_id']))
//    {
//        $invoice_id = $_GET['invoice_id'];
//        $invoice_arr = $CInvoiceManager->get_invoice_statusNone(false,$invoice_id);
//    }
    foreach ($invoice_arr as $invoice_row) {
        $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_row['invoice_id']);
        $total_owe=0;
        $total_owe = $CInvoiceManager->get_total_tax_and_paid($invoice_revision_id, round($CInvoiceManager->get_invoice_item_total($invoice_row['invoice_id'], $invoice_revision_id),2));
       
        $total_owe = round($total_owe,2);
        $invoice_arr=$CInvoiceManager->get_db_invoice($invoice_row['invoice_id']);
        $check_payment=$CInvoiceManager->check_payment_id($invoice_row['invoice_id']); // kiem tra hoa don da duoc thanh toan chua.
            if($invoice_arr>0){
                foreach ($invoice_arr as $invoice_row){ 
                    $date=$invoice_row['invoice_date'];
                    $term_err = dPgetSysVal('Term');
                    foreach ($term_err as $key => $value) {
                        if($key == $invoice_row['term'])
                            $term_value = $value;
                    }
                    $day_between1 =round((strtotime(date("Y/m/d"))-strtotime($date))/86400);
                    }
                $status_update=$CInvoiceManager->get_check_term($date, $term_value, $total_owe);
                if($invoice_row['invoice_status']==5)
                    $status_id = 5;
                else if($status_update=="Paid"){
                    $status_id = 2;
                }else if($status_update=="Partial" && $check_payment == true){
                    $status_id = 3;
                }
                else if($status_update == "Overdue"){
                    $status_id = 1;
                }
                else {
                    $status_id = 0;
                }
                    $update_st=$CInvoiceManager->update_status_invoice_cron($invoice_row['invoice_id'], $status_id,true);
         }
        }
}
function list_invoice_id(){
    global $CInvoiceManager;
        $invoice_arr=$CInvoiceManager->get_list_invoice();
        $invoice_id = "";
        foreach ($invoice_arr as $invoice_row) {
            $invoice_id.= 'invoice_id[]='.$invoice_row['invoice_id'].'&';
        }
        echo $invoice_id;
}
function _do_update_invoice_item(){
    global $CInvoiceManager;
    $id = $_POST['item_id'];
    $inv_item = $_POST['invoice_item'];
    $inv_item_price = round($_POST['invoice_price'],2);
    $inv_item_quantily = intval($_POST['invoice_quantily']);
    $inv_item_discount = floatval($_POST['invoice_discount']);
    $invoice_item_order = intval($_POST['invoice_order']);
    $invoice_id = $_POST['invoice_id'];
    $invocie_rev_id = $_POST['invoice_rev_id'];
    $total_old = $_POST['total_item'];
    $check_invoice_item = $CInvoiceManager->check_item_invoice($invoice_id, $invocie_rev_id, $inv_item,$id);
    if($check_invoice_item==true)
    {
        $invoice_item_id=$CInvoiceManager->update_invoice_item($id, $inv_item, $inv_item_price, $inv_item_quantily, $inv_item_discount, $invoice_item_order);

        $total= $CInvoiceManager->get_invoice_item_total($invoice_id, $invocie_rev_id);

        if($total!=$total_old)
            $update_tax = update_tax_invoice($total,$invocie_rev_id);
        echo '{"status": "Success","total":'.$total.'}';
        echo $invoice_item_id;
    }
    else
    {
        echo '{"status": "Failure", "message":"Adding item failed. Item with the exact same description and amount already exists"}';
    }

}

function load_inv_html_table(){
    global $AppUI;
//    include DP_BASE_DIR."/modules/sales/invoice_table.js.php";
    show_html_table($_REQUEST['invoice_id'],$_REQUEST['invoice_rev_id']);
}
function load_form_inv_total(){
    _form_total_and_note($_POST['invoice_id'],$_POST['invoice_rev_id']);
}
function load_invoiceRev_get_invoiceNo(){
    global  $CSalesManager;
    //$quotation_rev = $CSalesManager->create_invoice_or_quotation_revision('', $_POST['quotation_no']);
    $obj_revision_substr = substr($_POST['invoice_revision'],-3);
    $invoice_rev = $_POST['invoice_no']."-".$obj_revision_substr;
    echo '<input type="text" style="width:200px" readonly="true" class="text" name="invoice_revision" id="invoice_revision" value="'. $invoice_rev .'">';
}
function _do_memo_billingadress($customer_id,$address_id,$attention_id){
    global $CMemoManager;
    $memoObj['customer_id'] = $customer_id;
    $memoObj['address_id']=$address_id;
    $memoObj['attention_id'] = $attention_id;
    
    $memno_arr = $CMemoManager->get_memo_billing_address($customer_id);
    
    if(count($memno_arr)<=0)
        $CMemoManager->add_memo_billing_address($memoObj);
    else
        $CMemoManager->update_memo_billing_address($memno_arr[0]['memo_id'],$customer_id, $address_id, $attention_id);
}

function _do_update_invoice_order($invoice_id=false,$invoice_revision_id=false){
    global $CInvoiceManager; 
        if(isset($_POST['invoice_id'])){
            $invoice_item_row = $_POST['row_item'];
            $invoice_id = $_POST['invoice_id'];
            $invoice_rev_id = $_POST['invoice_rev_id'];
            $count = count($_POST['row_item']);
            for($i=0;$i<$count;$i++)
            {
                $CInvoiceManager->update_invoice_item_order($invoice_item_row[$i],$i+1);
            }
        }
}
function _get_contact_jobLocation(){
    global $CSalesManager,$CCompany;
    $job_location_id = $_REQUEST['job_location_id'];
    if($job_location_id!="")
        $contact_arr =  $CCompany->getContactInAddress($job_location_id);
//    print_r($client_localtion);
    $contact_option ='<option value="">--Select--</option>';
    if (count($contact_arr) > 0) {
        foreach ($contact_arr as $contact_row) {
            if ($_REQUEST['contact_id'] == $contact_row['contact_id'])
                $selected = 'selected="selected"';
            $contact_option .='<option value="'.$contact_row['contact_id'].'" '.$selected.'>'.$contact_row['contact_first_name'] .' '. $contact_row['contact_last_name'].'</option>';
        }
    }
    echo $contact_option;
}
function _do_load_invoice_no_exist(){
    global $CSalesManager;
    $invoice_no = $CSalesManager->create_invoice_or_quotation_no();
    $invoice_rev = $CSalesManager->create_invoice_or_quotation_revision('', $invoice_no);
    echo '{"sales_invoice_no":"'.$invoice_no.'","sales_invoice_rev":"'.$invoice_rev.'"}';
    //echo '{"status": "Success", "quotation_id": ' . $quotation_id . ', "quotation_revision_id": '. $quotation_revision_id .'}';
}
function _do_inv_update_done_sevice_order(){
    global $CSalesManager,$CSeviceOder;
    $invoice_id_arr = $_REQUEST['invoice_id'];
    for($i=0;$i<count($invoice_id_arr);$i++){
        $sevice_order_arr = $CSalesManager->get_serviceoder_id($invoice_id_arr[$i],'invoice');
        //print_r($sevice_order_arr);
        if($sevice_order_arr>0){
            $CSeviceOder->updateComment($sevice_order_arr[0]['service_order_id'], $comment=null, 'service_oder_status_history');
            //$CSeviceOder->update_inline_serviceOrder($sevice_order_arr[0]['service_order_id'], 'service_order_invoice', $invoice_id_arr[$i]);
            echo '{"status":"issuce"}';
        }
    }
}
function _do_inv_updat_sevice_order(){
    global $CSeviceOder;
    $sevice_id = $_REQUEST['id_sevice'];
    $invoice_id = $_REQUEST['invoice_id'];
    $CSeviceOder->update_inline_serviceOrder($sevice_id, 'service_order_invoice', $invoice_id);
}
function get_title_contact($contact_id=false){
    global $CSalesManager,$CMemoManager;
    
    $title_id =0;
    if(isset($_POST['attention_id'])){
        $contact_id = $_POST['attention_id'];
    }
    if(isset($_POST['customer_id']) && $_POST['customer_id']!=""){
        $memno_arr = $CMemoManager->get_memo_billing_address($_POST['customer_id']);
        $contact_id = $memno_arr[0]['attention_id'];
    }
    $attention_arr = $CSalesManager->get_attention_email($contact_id);
    $contacts_title_id = $attention_arr['contact_title'];

    
    if($contacts_title_id!="" && $contacts_title_id !='Null' && $contacts_title_id!=0){
        $title_id = $contacts_title_id;
    }
    if(isset($_POST['act_attn']))
           $title_id=0; 

    $contacts_title = array('' => '');
    $contacts_title += dPgetSysVal('ContactsTitle');
    $contacts_title_dropdown = arraySelect($contacts_title, 'contacts_title_id[]', 'id="contacts_title_id" class="text" style="height:20pt;" size="1"', $title_id, true);
    
    if(isset($_POST['attention_id']) || isset($_POST['customer_id'])){
        echo $contacts_title_dropdown;
    }
//    if(isset($_POST['act_attn']) && $_POST['act_attn']==1)
//        echo $contacts_title_dropdown;
    return $contacts_title_dropdown;
}
function _do_add_attention_in_invoice($invoice_id){
    global $CSalesManager;
    $attention_id_arr = $_POST['attention_id'];
    $sales_attention_id_arr = $_POST['attn_id'];
    // Neu thay doi customer xoa het attention cua customer do
    if(isset($_POST['onchange_customer']) && $_POST['onchange_customer']==1){
        $salesAtention_id = $CSalesManager->remove_salesAttention (false,$invoice_id);
    }
    for($i=0;$i<count($attention_id_arr);$i++){
        $salesAtention['sales_attention_id']=$sales_attention_id_arr[$i];
        $salesAtention['sales_type_id'] = $invoice_id;
        $salesAtention['attention_id'] = $attention_id_arr[$i];
        $salesAtention['sales_type_name'] = 'invoice';
        if($attention_id_arr[$i]!=0)
            $salesAtention_id = $CSalesManager->add_salesAttention($salesAtention);
        else if($sales_attention_id_arr[$i]!=0)
            $salesAtention_id = $CSalesManager->remove_salesAttention ($sales_attention_id_arr[$i]);
    }
    
}
function load_result_search_customer(){
    global $CCompany, $CcontactManager;
    // get value
    $client_search = dPgetParam($_POST, 'company_search');
    
    $found_clients = $CCompany->searchCustomerByAddress($client_search, "address");


    if ($found_clients && count($found_clients) > 0) {

      if (count($found_clients) == 1) {
          echo "<br/><b>Found: " . count($found_clients) . '</b><br/>';
        //echo '<li onclick="fill(\'' . $found_clients['company_name'] . '\');">';
        echo '<span style="top:4px;position: relative;"><input  type="radio" name="check_customer" id="check_customer" value="'.$found_clients[0]['company_id'].'" /></span>&nbsp;'. $found_clients[0]['company_name'];
        // echo '</li>';
      } else {
          $company_a = array();
          $echo = '';
        foreach ($found_clients as $a_client) {
            if (in_array($a_client['company_id'], $company_a)==false) {
          //echo '<li onclick="fill(\'' . $a_client['company_name'] . '\');">';
                $echo .= '<span style="top:4px;position: relative;"><input  type="radio" name="check_customer" id="check_customer" value="'.$a_client['company_id'].'" /></span><span>&nbsp;'. stripcslashes($a_client['company_name']) . '</span><br/>';
                $company_a[] = $a_client['company_id'];
            }
        }
        echo "<br/><b>Found: " . count($company_a) . '</b><br/>';
        echo $echo;
      }
    } else
        echo "<br/><b>Found: " . count($found_clients) . '</b><br/>';
}
function _get_load_customer(){
    global $CSalesManager;
    require_once (DP_BASE_DIR."/modules/sales/load_chose.js.php");
    $customer_id=$_POST['customer_id'];
    $rows = $CSalesManager->get_list_companies();
    
    $client_option = '<option value="">-- Select --</option>';
    foreach ($rows as $row) {
        $selected = '';
        if ($customer_id == $row['company_id'])
            $selected = 'selected="selected"';
        $client_option .= '<option value="'. $row['company_id'] .'" '. $selected .'>'. $row['company_name'] .'</option>';
    }
    echo '<select class="text_select" style="width:393px" name="customer_id" id="inv_customer_id" onchange="load_address(this.value);">'. $client_option .'</select>';
}

function _get_title_customer(){
    global $CSalesManager;
    $title_arr = dPgetSysVal('CustomerTitle');
    if($_POST['customer_id']){
        $rows = $CSalesManager->get_list_companies($_POST['customer_id']);
        echo $title_arr[$rows['company_title']];
    }else
        echo "";
}
function update_tax_invoice($total,$invoice_revision_id){
    global $CInvoiceManager,$CSalesManager;
    
    $invoice_revidion_arr = $CInvoiceManager->get_db_invoice_revsion($invoice_revision_id);
    $tax_index = $invoice_revidion_arr[0]['invoice_revision_tax'];
            
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();
    foreach ($tax_arr as $tax_row) {
        if($tax_arr['tax_id'] == $tax_index)
            $tax_rate=$tax_arr['tax_rate'];
    }
    
    $tax_value = $CSalesManager->round_up($total*($tax_rate/100));
    
    $CInvoiceManager->update_invoice_rev_tax($invoice_revision_id,$tax_value);
}

function _form_save_item_template()
{
    $item_id = $_POST['item_id'];
    ?>
        <table border="0" width="370" cellpadding="5">
            <tr align="center">
                <td><input type="radio" name="rad_item_template" id="rad_item_template" value="0" onclick="load_list_template(this.value)" checked />Save to List</td>
                <td><input type="radio" name="rad_item_template" id="rad_item_template" value="1" onclick="load_list_template(this.value)" />Save to Existing</td>
            </tr>
            <tr><td></td></tr>
            <tr>
                <td colspan="2"><div id="form_list_template"></div></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button name="Save_item_template" onclick="save_template_item(<?php echo $item_id ?>);return false;">Save</button>
                </td>
            </tr>
        </table>
    <?php
}
function list_template()
{
    global $CTemplateManager;
    $template_arr = $CTemplateManager->getlist_template();
        $dataTableRaw = new JDataTable('template_table');
        $dataTableRaw->setWidth('100%');
        $dataTableRaw->setHeaders(array('<input name="select_all" id="select_all" type="checkbox" onclick="check_all(\'select_all\', \'check_list_quo\');">','Template Name', 'Template Type'));

        $colAttributes = array(
            'class="checkbox" width="4%" align="center"',
            'class="templ_name" align="left" width="26%"',
            'class="templ_type" width="20%"',);

        $tableData = array(); $rowIds = array();
        if (count($template_arr) > 0 ) {
            foreach ($template_arr as $template) {
                if ($template['templ_type'] == 0) {
                    $template_type = 'Quotation';
                } else {
                    $template_type = 'Invoice';
                }
                $template_id = $template['templ_id'];
                $tableData[]= array(
                    '<input type="checkbox" id="check_list_quo" name="check_list_quo" value="'.$template_id.'">',
                    $CTemplateManager->htmlChars($template['templ_name']),
                    $template_type,
                );
                $rowIds[] = $template_id;
            }
        }

        $dataTableRaw->setDataRow($tableData, $colAttributes, $rowIds);
     
        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
        $dataTableRaw->show();
}

function _do_confirm_invoice()
{
    global $CInvoiceManager;
    $invoice_id = $_POST['invoice_id'];
    $invoice_revision_id = $_POST['invoice_revision_id'];
    $save_invoice_id = $CInvoiceManager->confirm($invoice_id, $invoice_revision_id);
    if($save_invoice_id)
        echo '{"status":"succes","status":"Fail"}';
}

function load_contract_no($customer_id=false,$invoice_id=false,$job_location_id=false)
{
    global $CContract,$CContractInvoice;
    include DP_BASE_DIR."/modules/sales/load_chose.js.php";
    if(isset($_POST['job_location_id']))
        $job_location_id = $_POST['job_location_id'];
    if(isset($_POST['invoice_id']))
        $invoice_id = $_POST['invoice_id'];
    if(isset($_POST['customer_id']) && $_POST['customer_id']!="")
        $customer_id = $_POST['customer_id'];
    
    if($customer_id!="")
    {
        // Lay tat cac cac contract trong he thong
        $contract_arr = $CContract->getEngagements(false,$customer_id);
    }
    
    // Lay cac contract theo job location duoc chon
    $contractByJob_arr = array();
    if($job_location_id!="")
        $contractByJob_arr = $CContract->getContractByAddres($job_location_id);
    
    //Lay cac contract duoc gan vao quotaiton
    $contract_invoice_arr = $CContractInvoice->getContractInvoice($invoice_id);
    $contract_invoice_id_arr = array();
    foreach ($contract_invoice_arr as $contract_invoice_item) {
        $contract_invoice_id_arr[] = $contract_invoice_item['contract_id'];
    }
    //end Lay cac contract duoc gan vao quotaiton
    
    // Group contract Theo Job location
    $contractByJob_id_arr=array();
    $option = '<optgroup label="Same-Location Contracts">';
    foreach ($contractByJob_arr as $contractByJob_item) {
        $select = "";
        if(in_array($contractByJob_item['engagement_id'], $contract_invoice_id_arr))
              $select="selected";  
        $option .='<option '.$select.' value="'.$contractByJob_item['engagement_id'].'">'.$contractByJob_item['engagement_code'].' - '.date('d M Y',  strtotime($contractByJob_item['engagement_start_date'])).' to '.date('d M Y',  strtotime($contractByJob_item['engagement_end_date'])).'</option>';
        $contractByJob_id_arr[] = $contractByJob_item['engagement_id'];
    }
    if(count($contractByJob_arr)<=0)
        $option.='<option value="">No contracts available</option>'; 
    $option .= '</optgroup>';
    // End group job theo Job location
    
    // Group cac contract khac
    $option .= '<optgroup label="Other Locations">';
    foreach ($contract_arr as $contract_item) {
        $select = "";
        if(in_array($contract_item['engagement_id'], $contract_invoice_id_arr))
              $select="selected";
        if(!in_array($contract_item['engagement_id'],$contractByJob_id_arr))//Loai bo nhung contract theo Job location
            $option.= '<option '.$select.' value="'.$contract_item['engagement_id'].'">'.$contract_item['engagement_code'].' - '.date('d M Y',  strtotime($contract_item['engagement_start_date'])).' to '.date('d M Y',  strtotime($contract_item['engagement_end_date'])).'</option>';
    }
    if(count($contract_arr)<=0)
        $option.='<option value="">No contracts available</option>'; 
    $option .= '</optgroup>';
    // End group contract khac
    
    if(isset($_POST['job_location_id']) || isset($_POST['customer_id']))
        echo "<select name='select_contract_no[]' multiple id='select_contract_no'>".$option."</select>";
    else
        return "<select name='select_contract_no[]' multiple id='select_contract_no'>".$option."</select>";
}

function _do_add_contract_invoice($invoice_id,$post)
{
    global $CContractInvoice;
    $contrac_no_id_arr = $post['select_contract_no'];
    
    $CContractInvoice->deleteContractInvoice($invoice_id);
    foreach ($contrac_no_id_arr as $key => $value) {
        if($value!="" || $value!=0){
            $obj['invoice_id']=$invoice_id;
            $obj['contract_id']=$value;
            $CContractInvoice->addCContractInvoice($obj);
        }
    }
}

function save_template_subheading_or_subject()
{
    global $CTemplateManager;
    
    $obj['templ_id'] = 0;
    $obj['sub_heading_content'] = $_POST['content'];
    $obj['type'] = $_POST['type'];
    $is_exist = $CTemplateManager->is_tempalte_subheading($_POST['content'],$_POST['type']);
    if($is_exist)
    {
        echo '{"status":"exist"}';
    }
    else {
        $id = $CTemplateManager->add_template_sub_heading($obj);
        if($id)
            echo '{"status":"success"}';
        else
            echo '{"status":"faild"}';
    }
   
}

function get_invoice_status(){
    $invoice_stt = dPgetSysVal('InvoiceStatus');
    print json_encode($invoice_stt);
}
?>

