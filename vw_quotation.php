<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR."/modules/clients/company_manager.class.php");

define(FORMAT_DATE_DEFAULT, 'd/m/Y');

//require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once 'CSalesManager.php';
require_once (DP_BASE_DIR."/modules/sales/CQuotationManager.php");
require_once (DP_BASE_DIR."/modules/sales/CTemplateManager.php");
require_once (DP_BASE_DIR."/modules/system/roles/roles.class.php");
require_once (DP_BASE_DIR . "/modules/contacts/contacts_manager.class.php");
require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
require_once (DP_BASE_DIR.'/modules/sales/CMemoManager.php');
require_once(DP_BASE_DIR."/modules/clients/company_manager.class.php");
require_once (DP_BASE_DIR . "/modules/serviceOrders/ServiceOrderManager.php");
require_once (DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
require_once(DP_BASE_DIR . '/modules/departments/CDepartment.php');
require_once (DP_BASE_DIR . '/modules/engagements/engagementManager.class.php');
require_once (DP_BASE_DIR . '/modules/sales/CContractQuotation.php');
require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
require_once (DP_BASE_DIR. '/modules/engagements/contract_breakdown.php');
require_once (DP_BASE_DIR.'/modules/po/CNextNumber.php');
  
global $AppUI;
$CSalesManager = new CSalesManager();
$CQuotationManager = new CQuotationManager();
$CTemplateManager = new CTemplateManage();
$crole = new CRole();
$ContactManager = new ContactManager();
$cconfig = new CConfigNew();
$CMemoManager = new CMemoManager();
$CCompany = new CompanyManager();
$CSeviceOder = new ServiceOrderManager();
$CInvoiceManager = new CInvoiceManager();
$CDepManager = new CDepartment();
$CContract = new EngagementManager();
$CContractQuotation = new CContractQuotation();
$CTemplatePDF = new CTemplatePDF();
$contract_breakdown_manage = new contract_sum_breakdown();
$CNextNumber = new CNextNumber();

// Check roles
$user_id = $AppUI->user_id;
$user_roles_arr = $perms->getUserRoles($user_id);
$user_roles = array();
foreach ($user_roles_arr as $user_roles_item) {
    $user_roles[] = $user_roles_item['value'];
}
$roles_businessDevelopmentManager  = "innoflex_business_development_manager";

$readOnlyReportUserCreste =false;
if(in_array($roles_businessDevelopmentManager,$user_roles))
{
    $readOnlyReportUserCreste = true;
}
$readOnlyReportUserCreste;

// Checking permissions edit
$perms =& $AppUI->acl();
$canEdit = $perms->checkModule( $m, 'edit');

$canAccess_quotation = $perms->checkModule( 'sales_quotation', 'access');
$addView_quotation = $perms->checkModule( 'sales_quotation', 'add');
$editView_quotation = $perms->checkModule( 'sales_quotation', 'edit');
$deleteView_quotation = $perms->checkModule( 'sales_quotation', 'delete');

function save_table_inline_add() {
    
}

function update_field() {
    $id = dPgetCleanParam($_POST, 'id');
    $updated_content = dPgetCleanParam($_POST, 'value');

    $col_des = dPgetCleanParam($_GET, 'description', '');

    $myObject = new CMyModule();
    $myObject->load($id);
    if ($col_des != '') {
        $myObject->$col_des = $updated_content;

        if (($msg = $myObject->store())) {
  
        } else {
            echo $updated_content; 
        }
    }
}
function _action(){
    $left_block = $AppUI->createBlock('left-block', 'Learing English as a second language, Will need more peole who can speaking English,
        Why do we need to learn English,
        Of course one of the reason,
        practice and confident,Please  i can do it! ','style="float: left"');
    $big_block = $AppUI->createBlock('my-block', $left_block . $right_block);
    echo  $left_block ;
}

function list_quotation_html() {

        global $AppUI, $CQuotationManager,$CSalesManager,$CDepManager,$editView_quotation,$canAccess_quotation;
        
        
        //echo $totalContract = $CQuotationManager->calculateTotalQuotationByContract(96);
        
        include DP_BASE_DIR."/modules/sales/css/quotation_main.css.php";
        include DP_BASE_DIR."/modules/sales/jquery.dataTables.min.js";
        include DP_BASE_DIR."/modules/sales/quotation.js.php";

        $status_id = 0;
        $customer_id = null;
        $quotation_no=false;
        if(isset($_POST['status_id']))
            $status_id = $_POST['status_id'];
        if(isset($_POST['customer_id']))
            $customer_id = $_POST['customer_id'];
        if(isset($_POST['quotation_no']))
            $quotation_no = $_POST['quotation_no'];
        
//        // Lay department cua user hien tai
        $dep_user_id=0;
        $dep_user_arr = $CDepManager->getDeparmentByUser($AppUI->user_id);
        if(count($dep_user_arr)>0)
            $dep_user_id = $dep_user_arr[0]['dept_id'];
        $dep_arr = array();
        foreach ($dep_user_arr as $value) {
            $dep_arr[]=$value['dept_id'];
        }
        
        if(isset($_POST['dept_id']))
            $dep_id = $_POST['dept_id'];
        
        if($dep_id)
            $quotation_arr = $CQuotationManager->list_quotation($customer_id, $status_id, $dep_id,$quotation_no);
        else
            $quotation_arr=array();

        $quotation_stt = dPgetSysVal('QuotationStatus');
        
        $table = '<table id="quotation_table" class="tbl" cellspacing="1" cellpadding="2" style="clear: both; width: 100%">'
                    . '<thead>'
                        . '<tr>'
                            . '<th><input name="select_all" id="select_all" type="checkbox" onclick="check_all(\'select_all\', \'check_list\');"></th>'
                            . '<th width="15%">Quotation No</th>'
                            . '<th width="35%">Client</th>'
                            . '<th width="20%">Subject</th>'
                            . '<th width="10%">Date</th>'
                            . '<th width="10%">Total</th>'
                            . '<th width="10%">Status</th>'
                        . '</tr>'
                    . '</thead>'
                . '</table>';
        echo $table;

//        $dataTableRaw = new JDataTable('quotation_table');
//        $dataTableRaw->setWidth('100%');
//        $dataTableRaw->setHeaders(array('<input name="select_all" id="select_all" type="checkbox" onclick="check_all(\'select_all\', \'check_list_quo\');">','Quotation No', 'Client', 'Subject', 'Date', 'Total', 'Status'));
//
//        $colAttributes = array(
//            'class="checkbox" width="4%" align="center"',
//            'class="quotation_no" align="left" width="13%"',
//            'class="client_quotation1" width="28%"',
//            'class="quotation_des" width="25%"',
//            'class="quotation_date" align="center" width="10%"',
//            'class="quotation_total" align="right" width="10%"',
//            'class="quotation_status" align="center" width="7%"',);
//
//        $tableData = array(); $rowIds = array();
//        if (count($quotation_arr) > 0 ) {
//            foreach ($quotation_arr as $quotation) {
//                $quotation_id = $quotation['quotation_id'];
//                $quotation_revision_id = $CQuotationManager->get_quotation_revision_lastest($quotation_id);
//                
//                // get joblocation
//                $job_location_arr =  $CSalesManager->get_address_by_id($quotation['job_location_id']);
//                    $brand="";
//                if($job_location_arr['address_branch_name']!="")
//                    $brand=$job_location_arr['address_branch_name'].' - ';
//                $address_2 ="";
//                if($job_location_arr['address_street_address_2']!="")
//                    $address_2= ', '.$job_location_arr['address_street_address_2'];
//                $postal_code_job = '';
//                if($job_location_arr['address_postal_zip_code']!="")
//                    $postal_code_job .=', Singapore '.$job_location_arr['address_postal_zip_code'];
//                $job_location = "";
//                if($job_location_arr!=""){
//                        $job_location.=$brand.$CSalesManager->htmlChars($job_location_arr['address_street_address_1'].$address_2.$postal_code_job);    
//                }
//                    $load_quotation = '<span onmouseover="load_quotation_rev('.$quotation_id.'); return false;" onclick="quotation_rev_more('.$quotation_id.');" id="quotation-rev-more-'.$quotation_id.'" class="quotation-rev-more" title="Quotation rev more..">[+]</span>
//                            <a href="?m=sales&show_all=1&tab=0&status=update&quotation='.$quotation_id.'&quotation_revision_id='.$quotation_revision_id.'" onclick="load_quotation('.$quotation_id.', '. $quotation_revision_id .', \'update\'); return false;">'. $quotation['quotation_no'].'</a>';
//                $tableData[]= array(
//                    '<input type="checkbox" id="check_list_quo" name="check_list_quo" value="'.$quotation_id.'">
//                    <input type="hidden" id="quotation_status_'.$quotation_id.'" name="quotation_status_'.$quotation_id.'" value="'.$quotation['quotation_status'].'" >',
//                    $load_quotation .'<div class="div-quotation-rev" id="div-quotation-rev-'.$quotation_id.'" style="display: none"></div>    ',
//                    '<div class="client_quotation" style="font-weight:bold;" id="'.$quotation_id.'">'.$quotation['company_name'].'.</div>
//                    <div style="font-size:0.9em;color:#666;">'.$job_location.'</div>',
//                    $CSalesManager->htmlChars($quotation['quotation_subject']),
//                    $quotation['quotation_date'],
//                    $total_item_show = '$'.number_format(round($CQuotationManager->get_total_tax_and_paid($quotation_revision_id, $CQuotationManager->get_quotation_item_total($quotation_id, $quotation_revision_id)),2),2),
//                    $quotation_stt[$quotation['quotation_status']],
//                );
//                $rowIds[] = $quotation_id;
//            }
//        }
//
//        $dataTableRaw->setDataRow($tableData, $colAttributes, $rowIds);
//
//        if($editView_quotation)
//        {
//            //$quotation_stt['selected'];
//            $dataTableRaw->setJEditable(
//                    'quotation_status',
//                    '?m=sales&a=vw_quotation&c=_do_update_quotation_field&suppressHeaders=true&col_name=quotation_status&status='.$quotation['quotation_status'],
//                    array(
//                        'data' => json_encode($quotation_stt),
//                        'type' => 'select',
//                        'cancel' => 'Cancel',
//                        'submit' => 'Save',
//                        'indicator' => 'Loading...',
//                        'onsubmit'=>'function(){
//                            var delpt_id = $("#department").val();
//                            var delpt_arr='.json_encode($dep_arr).';
//                            var in_delpt = delpt_arr.indexOf(delpt_id);
//                            var status_value = $(".quotation_status").html();
//                            if(in_delpt==-1)
//                            {
//                                alert("You are not allowed to update this quotation because you are not a member of this department.");
//                                $("button[type=\'cancel\']").click();
//                                return false;
//                            }
//                            return true;
//                        }',
//                    )
//            );
//            $dataTableRaw->setJEditable(
//                    'quotation_des',
//                    '?m=sales&a=vw_quotation&c=_do_update_quotation_field&suppressHeaders=true&col_name=quotation_subject',
//                    array(
//                        'type' => 'text',
//                        'cancel' => 'Cancel',
//                        'submit' => 'Save',
//                        'indicator' => 'Loading...',
//                        'onsubmit'=>'function(){
//                            var delpt_id = $("#department").val();
//                            var delpt_arr='.json_encode($dep_arr).';
//                            var in_delpt = delpt_arr.indexOf(delpt_id);
//                            if(in_delpt==-1)
//                            {
//                                alert("You are not allowed to update this quotation because you are not a member of this department.");
//                                $("button[type=\'cancel\']").click();
//                                return false;
//                            }
//                            return true;
//                        }',
//                    )
//            );
//            $dataTableRaw->setJEditable(
//                    'quotation_date',
//                    '?m=sales&a=vw_quotation&c=_do_update_quotation_field&suppressHeaders=true&col_name=quotation_date',
//                    array(
//                        'value' => 'ffsfrom_date',
//                        'class' => 'ffsfrom_date',
//                        'name' => 'ddfrom_date',
//                        'cancel' => 'Cancel',
//                        'submit' => 'Save',
//                        'indicator' => 'Loading...',
//                        'onsubmit'=>'function(){
//                            var delpt_id = $("#department").val();
//                            var delpt_arr='.json_encode($dep_arr).';
//                            var in_delpt = delpt_arr.indexOf(delpt_id);
//                            if(in_delpt==-1)
//                            {
//                                alert("You are not allowed to update this quotation because you are not a member of this department.");
//                                $("button[type=\'cancel\']").click();
//                                return false;
//                            }
//                            return true;
//                        }',
//                    )
//            );
//            $dataTableRaw->setJEditable(
//                    'client_quotation',
//                    '?m=sales&a=vw_quotation&c=_do_update_quotation_field&suppressHeaders=true&col_name=customer_id',
//                    array(
//                        'loadurl' => '?m=sales&a=vw_quotation&c=_get_customer_inline&suppressHeaders=true',
//                        'type' => 'select',
//                        'cancel' => 'Cancel',
//                        'submit' => 'Save',
//                        'indicator' => 'Loading...',
//                        'onsubmit'=>'function(){
//                            var delpt_id = $("#department").val();
//                            var delpt_arr='.json_encode($dep_arr).';
//                            var in_delpt = delpt_arr.indexOf(delpt_id);
//                            if(in_delpt==-1)
//                            {
//                                alert("You are not allowed to update this quotation because you are not a member of this department.");
//                                $("button[type=\'cancel\']").click();
//                                return false;
//                            }
//                            return true;
//                        }',
//                    )
//            );
//        }
//     
//        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
//        if($canAccess_quotation)
//            $dataTableRaw->show();
        
        $delete_id = '<a class="icon-delete icon-all" onclick="delete_quotation(0, 0); return false;" href="#">Delete</a>';
        //$email_id = '<a onclick="popupSendEmail(0, 0); return false;" href="#"> Email &nbsp; &nbsp; &nbsp;</a>';
//        $print_id = '<a onclick="generate_print_quotation(0, 0); return false;" href="#"> Print to PDF &nbsp; &nbsp; &nbsp;</a>';
//        $copy_id = '<a onclick="copy_quotation(0, 0); return false;" href="#"> Copy &nbsp; &nbsp; &nbsp;</a>';
//        $history = '<a onclick="view_history(0, 0); return false;" href="#"> History </a>';

        echo $block_del = $AppUI ->createBlock('del_block', $delete_id .$print_id . $copy_id ,'style="text-align: left;"');

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

function frm_add_item_revision_submit($formValues) {
    
    global $AppUI, $CQuotationManager;

    $status_rev = $formValues->frm_add_item_revisionSection->status_rev;
    if ($status_rev == 'update') { // dat code xu ly khi add 1 item se thay doi Quotation_revision
        $quotation_revision_id = $formValues->frm_add_item_revisionSection->quotation_revision_id;
        $quotation_item_id = $formValues->frm_add_item_revisionSection->quotation_item_id;
        if ($quotation_revision_id != 0 && $quotation_revision_id != null && $quotation_revision_id != '') {
            $quotation_revision_id_new = $CQuotationManager->update_quotation_revision($quotation_revision_id, null, $quotation_item_id); // goi den ham xu ly tao quotation revision moi va update cac bang lien quan
            if ($quotation_revision_id_new > 0) { // viec update da thanh cong
                $formValues->frm_add_item_revisionSection->quotation_revision_id = $quotation_revision_id_new;
                $CQuotationManager->add_quotation_item(_format_CQuotationItemObj($formValues));

                $response = array(
                    'successJs' => '$(\'#\'+id_poup).dialog(\'close\'); load_quotation('.$formValues->frm_add_item_revisionSection->quotation_id.', '.$quotation_revision_id_new.', \''.$status_rev.'\');'
                    );
            }
        }
    } elseif ($status_rev == 'add') {
            $CQuotationManager->add_quotation_item(_format_CQuotationItemObj($formValues));
                $response = array(
                    'successJs' => '$(\'#\'+id_poup).dialog(\'close\'); load_quotation('.$formValues->frm_add_item_revisionSection->quotation_id.', '.$formValues->frm_add_item_revisionSection->quotation_revision_id.', \''.$status_rev.'\');'
                    );
    }

    if ($response)
        return $response;
    else
        return array(
                    'successJs' => 'alert(\'Co loi trong qua trinh add.\'); $(\'#\'+id_poup).dialog(\'close\'); load_quotation('.$formValues->frm_add_item_revisionSection->quotation_id.', '.$formValues->frm_add_item_revisionSection->quotation_revision_id.', \''.$status_rev.'\');'
                    );
}

function view_quotation_detail() {

    global $AppUI, $CSalesManager,$canEdit, $CQuotationManager,$baseUrl;

    include DP_BASE_DIR."/modules/sales/quotation.js.php";
    include DP_BASE_DIR."/modules/sales/load_chose.js.php";
        
    
    $quotation_id = $_REQUEST['quotation_id'];
    $quotation_revision_id = $_REQUEST['quotation_revision_id'];
    $status_id = $_POST['status_id'];
    $status_back = $_POST['status_back'];
    $dept_id = $_POST['dept_id'];
    
    $filter_seacrch = "";
    if($_POST['filter'])
    {
        $filter_seacrch = $_POST['filter'];
    }
    
    
    echo '<input type="hidden" id="dept_id" value="'.$dept_id.'" />';
    
    // Kiem tra user co thuoc department cua quotation nay ko
    $canEditDept = 0;
    if($quotation_id!=0)
        $canEditDept = $CQuotationManager->permissionQuotationByDepartment($quotation_id);
    echo '<input type="hidden" id="can_edit_dept" value="'.$canEditDept.'" />';
    
    include DP_BASE_DIR."/modules/sales/css/quotation.css.php";
    echo '<div id="switchOff"><!-- Place at bottom of page --></div>';
    
    echo '<form id="frm_all_quotation" method="post">';
    //echo '<div id="div_quotation">';
        echo '<div id="div_details" style="width:1055px;">';
            echo '<div id="div_0">';
                _form_button($quotation_id, $quotation_revision_id,$status_back,$canEditDept);
            echo '</div>';

            echo '<div id="div_info">';
                _form_info($quotation_id, $quotation_revision_id,$canEditDept,$dept_id);
            echo '</div>';

            echo '<div id="div_3">';
                show_html_table($quotation_id, $quotation_revision_id,$canEditDept);
            echo '</div>';
            
            echo '<div id="div_total_note">';
                _form_total_and_note($quotation_id, $quotation_revision_id,$canEditDept);
            echo '</div>';
            echo '<div id="div_note">';
                _form_quo_note($quotation_id, $quotation_revision_id,$canEditDept);
            echo '</div>';

        echo '</div>';
       echo '<input type="hidden" id="perm_edit" value="'.$canEdit.'" />';
    //echo '</div>';
    echo '</form>';
    echo '<input type="hidden" value="'.$filter_seacrch.'" id="filter_search" />';
    echo '<div  class="block_loading loading_active" ></div>';
    echo '<div class="block_loading_content loading_active"><img width="40" src="'.$baseUrl.'/images/ajax-loader.gif" /></div>';
}


function _form_button($quotation_id = 0, $quotation_revision_id = 0,$status_back=false,$canEditDept) {

      global $AppUI, $canDelete, $canAdd, $CQuotationManager,$CSalesManager,$CDepManager,$editView_quotation,$deleteView_quotation,$addView_quotation;
      
      $isUserDepartment = $CDepManager->IsUserInDepartment();
      
      $coutRev=count_quoRevision_by_quotation($quotation_id);
      echo '<input type="hidden" id="quo_countRev" value="'.$coutRev.'"/>';
      $status = $_POST['status_rev'];
        if ($quotation_id != 0 && $quotation_revision_id != 0 && $status = 'update') {
//          $status = 'update_no_rev';
             $invoice_id = intval($CQuotationManager->get_invoice_id($quotation_id));
             $quotation_arr = $CQuotationManager->get_db_quotation($quotation_id);
             
             $quotation_revision_arr = $CQuotationManager->get_db_quotation_revsion($quotation_revision_id);
             $revison_lastest = $CQuotationManager->get_latest_quotation_revision($quotation_id);
             $revison_lastest_no = $CSalesManager->create_quotation_revision($revison_lastest[0]['quotation_revision'], NULL, $quotation_arr[0]['quotation_no']);
            
            if ($invoice_id != 0) {
                $view_invoice = '<button class="ui-button ui-state-default ui-corner-all" onclick="change_invoice_revision('. $invoice_id .', \''. $status .'\'); return false;">View Invoive</button>&nbsp';
            }
            if($addView_quotation)
                if($isUserDepauortment && $canEditDept)
                    $btn_new = '<button class="ui-button ui-state-default ui-corner-all" onclick="load_quotation(0, 0, \'add\'); return false"><img border="0" src="images/icons/plus.png">New Quotation</button>&nbsp;&nbsp;';
            //$delete_id = '<a class="icon-delete icon-all" onclick="delete_quotation_revision('. $quotation_revision_id .'); return false;" href="#">Delete</a>';
            //$change_rev_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="change_revision('. $quotation_id .', '. $quotation_revision_id .', \''. $status .'\'); return false;">Change Revision</button>&nbsp';
            $print_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="generate_print_quotation('. $quotation_id .', '. $quotation_revision_id .'); return false;">Print</button>&nbsp;';
            //$copy_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="copy_quotation('. $quotation_id .', '. $quotation_revision_id .'); return false;">Copy</button>&nbsp;';
            $history_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="view_history('. $quotation_id .', '. $quotation_revision_id .'); return false;">History</button>&nbsp;';
            $print_preview = '<button class="ui-button ui-state-default ui-corner-all" onclick="print_quotation('. $quotation_id .', '. $quotation_revision_id .'); return false;">Print Preview</button>&nbsp;';
            echo '<div id="deletec-box"></div> 
<a href="?action=delc&cid=2" class="deletec-confirm"></a>
<a href="?action=delc&cid=2" class="deletec-confirm"></a>
<a href="?action=delc&cid=2" class="deletec-confirm"></a>';
            
            if($canEditDept && $editView_quotation)
            {
                //kiem tra co invoice cua quotation
                $email_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="popupSendEmail('. $quotation_id .', '. $quotation_revision_id .'); return false;">Email</button>&nbsp;';
                if($quotation_arr[0]['quotation_status']==3)
                {
                    $convert_block ='<button class="ui-button ui-state-default ui-corner-all" onclick="convert_quotation_into_invoice('. $quotation_id .', '. $quotation_revision_id .','.$invoice_id.'); return false;"> Create Invoice</button>&nbsp';
                    $convert_block_contract ='<button class="ui-button ui-state-default ui-corner-all" onclick="create_contract_by_quotaiton_approved('. $quotation_id .','.$quotation_arr[0]['quotation_status'].'); return false;">Create Contract</button>&nbsp';
                    echo '<div id="add_invoices" display="none" title="Ban muon gan invoice nao vao SO khong ?"></div>';
                    echo '<div id="show_so" display="none"></div>';
                }
            }
            
            //$save = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_all_quotation(); return false;">Save As New Revison</button>&nbsp';
            if($quotation_arr[0]['quotation_no']!="")
                $h1_title = 'Quotation Rev:  <font color="red">'.$quotation_revision_arr[0]['quotation_revision'].'</font><input type="hidden" name="revision_lastest" id="revision_lastest" value="'.$revison_lastest_no.'" />';
            else 
                $h1_title ='Draft';
        } else {
            $h1_title = 'New Quotation ';
            $use_template_block = '<button id="div_details" class="ui-button ui-state-default ui-corner-all"  onclick="load_template('.$quotation_id.', '.$quotation_revision_id.', \''. $status .'\'); return false;">Use Template</button>';
        }

      echo $AppUI ->createBlock('div_title', '<h1 id="quotation_rev_label">'. $h1_title .'</h1>', 'style="float: left;"');
      
      //$back_block = '<button id="div_details" class="ui-button ui-state-default ui-corner-all">Back</button>&nbsp;';
      $back_block = '<input type="button" value="Back" onclick="back_quo_contracts('.$status_back.')" class="ui-button ui-state-default ui-corner-all" />';
      if($status == 'add') {
        $save_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_all_quotation('. $quotation_id .', '. $quotation_revision_id .', \''. $status .'\'); return false;">Save</button>&nbsp';
      } else {
          $status = 'update_no_rev';
          if($canEditDept && $editView_quotation)
          {
            $save_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_all_quotation('. $quotation_id .', '. $quotation_revision_id .', \''. $status .'\'); return false;">Save</button>&nbsp';
            $copy='<option values="Copy" >Copy</option>';
            $delete_rev = '<option value="Delete" >Delete this revision</option>';
            $delete_quo = '<option value="Delete Quotation" >Delete this Quotation</option>';
          }
            $action_quotation='<select class="text" name="action_quotation" onchange="load_action(this.value)">
                      <option value="" >Action</option>
                      <option value="Change Revision" >Change Revision</option>
                      '.$copy.'
                      <option value="History" >History</option>
                      '.$delete_rev.'
                      '.$delete_quo.'
              </select>';
      }

      echo "<div id='waper_btt_block' style='width:100%; overflow:hidden; padding-top:10px;'>";
      echo $AppUI ->createBlock('btt_block',$back_block,'style="text-align: center; float:left; height:25px;"');
      //echo '<div style="text-align: center; float:left; overflow:hidden;">'.$back_block.'</div>';
      echo $AppUI ->createBlock('btt_block',$btn_new . $convert_block . $convert_block_contract . $change_rev_block . $save_block . $save . $copy_block .$print_preview. $print_block . $email_block . $use_template_block . $action_quotation. $new_job, 'style="text-align: center; float: right"');
//      echo $AppUI ->createBlock('btt_block', $back_block . $save_block . $use_template_block, 'style="text-align: center; float: right"');
      echo "</div>";


}


function _form_info($quotation_id = 0, $quotation_revision_id = 0,$canEditDept,$dept_id) {

    global $AppUI, $CQuotationManager, $CSalesManager, $ContactManager, $crole, $cconfig, $CCompany, $CInvoiceManager,$CDepManager,$editView_quotation,$addView_quotation,$deleteView_quotation, $CContractQuotation, $CTemplatePDF;
    include DP_BASE_DIR."/modules/sales/css/quotation_main.css.php";
    $url = DP_BASE_DIR. '/modules/sales/images/logo/';
    $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
    

        $files = list_files($url);
        if(count($files)>0)
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
            $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face"  width="170" /><br/>';
        }
    
//    $files = scandir($url);
//    $i = count($files)-1;
//    if (count($files) > 2 && $files[$i]!=".svn") {
//        $path_file = $url . $files[$i];
//        $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face"  width="170" /><br/>';
//    }

    $client_id ="";
    $status_dis ="";
    if ($quotation_id != 0 && $quotation_revision_id != 0) {
        $quotation_details_arr = $CQuotationManager->get_db_quotation($quotation_id);
        $quotation_details_arr1 = $CQuotationManager->get_db_quotation_revsion($quotation_revision_id);
        if (count($quotation_details_arr) > 0) {
           $date_hidden = $quotation_details_arr[0]['quotation_date'];
            $date_display = date(FORMAT_DATE_DEFAULT, strtotime($date_hidden));
            $quotation_no = $quotation_details_arr[0]['quotation_no'];
            $quotation_rev = $quotation_details_arr1[0]['quotation_revision'];
            $servicer_order= str_replace('"','&quot;',$quotation_details_arr[0]['service_order']);
            $user_id = $quotation_details_arr[0]['user_id'];
            
            // Lay date cua revision
            $quotation_revision_date = $quotation_details_arr1[0]['quotation_revision_date'];
            if($quotation_revision_date!="0000-00-00")
            {
                $date_hidden = $quotation_revision_date;
                $date_display = date(FORMAT_DATE_DEFAULT, strtotime($quotation_revision_date));
            }

            //$contact_coord_arr=$ContactManager->get_db_contact($quotation_details_arr[0]['contact_coordinator_id']);
            $sale_person_name = str_replace('"',"&quot;",$quotation_details_arr[0]['quotation_sale_person']);
            $sale_person_email = str_replace('"',"&quot;",$quotation_details_arr[0]['quotation_sale_person_email']);
            $sale_person_phone = $quotation_details_arr[0]['quotation_sale_person_phone'];
            $sales_sevice_coordinater = $quotation_details_arr[0]['contact_coordinator_id'];
            $sales_subject = $quotation_details_arr[0]['quotation_subject'];

            $client_id = $quotation_details_arr[0]['customer_id'];
            $address_id = $quotation_details_arr[0]['address_id'];
            $attention_id = $quotation_details_arr[0]['attention_id'];
            $job_location = $quotation_details_arr[0]['job_location_id'];
            $contact_id = $quotation_details_arr[0]['contact_id'];
            $quotation_co = $quotation_details_arr[0]['quotation_CO'];
            $quo_department_id = $quotation_details_arr[0]['department_id'];
            
            if($quotation_no=="0"){
                   $quotation_no = $CSalesManager->create_invoice_or_quotation_no(true);
                   $quotation_rev = $CSalesManager->create_quotation_revision('', $quotation_no);
                   $status_dis='disabled="true"';
            }

        } else {
            $date_hidden = date('Y-m-d', time());
            $date_display = date(FORMAT_DATE_DEFAULT, time());
            $quotation_no = '';
            $quotation_rev = '';
            $servicer_order = '';
            $user_id = $AppUI->user_id;
            
            $client_id ="";
            $sale_person_name = '';
            $sale_person_email = '';
            $sale_person_phone = '';
            $sales_sevice_coordinater='';
            $sales_subject = '';
            $quo_department_id =$dept_id;
        }
    } else {

            $date_hidden = date('Y-m-d', time());
            $date_display = date(FORMAT_DATE_DEFAULT, time());
            $quotation_no = $CSalesManager->create_invoice_or_quotation_no(true,$dept_id);
            $quotation_rev = $CSalesManager->create_quotation_revision('',null, $quotation_no);
            $servicer_order = '';
            $user_id = $AppUI->user_id;

            $client_id ="";
            $sale_person_name = '';
            $sale_person_email = '';
            $sale_person_phone = '';
            $sales_sevice_coordinater='';
            $sales_subject = '';
            $quo_department_id =$dept_id;
    }
    //echo $user_id;
    //Update Status (Anhnn)
    $quotation_status_arr = $CSalesManager->get_list_quotation_status($quotation_id);
    if (count($quotation_status_arr) > 0) {
        $status_option = '';
        foreach ($quotation_status_arr as $quotation_status) {
            $selected = '';
            if ($quotation_id == $quotation_status['quotation_id'])
                $selected = 'selected="selected"';
            $status_option .= '<option value="'. $quotation_status['quotation_id'] .'" '. $selected .'>'. $quotation_status['quotation_status'] .'</option>';
        }
    }
    if(!$canEditDept || !$editView_quotation)
        $status_dis='disabled="true"';
    $quotation_stt = dPgetSysVal('QuotationStatus');
   // $quotation_stt_dropdown = arraySelect($quotation_stt, 'quotation_status_id',  'style="width:200px;" '.$status_dis.' id="quotation_status_id"  class="text" size="1" onchange="update_status('. $quotation_id .', this.value, '.$quotation_status['quotation_status'].' , \' quotation_status \')"', $quotation_status['quotation_status'] , true);
    $quotation_stt_dropdown = arraySelect($quotation_stt, 'quotation_status_id',  'style="width:200px;" '.$status_dis.' id="quotation_status_id"  class="text" size="1" onchange="update_status('. $quotation_id .',this.value , '.$quotation_status['quotation_status'].')"', $quotation_status['quotation_status'] , true);
    $template_pdf_1 = $CTemplatePDF->get_template_pdf(1);
    
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
        if($supplier_arr['sales_owner_gst_reg_no'])
            $reg_no = '<p>'. $AppUI->_('GST Reg No') .': '. $supplier_arr['sales_owner_gst_reg_no'] .'</p>';
        $supplier .= '<p>Fax: '. $supplier_arr['sales_owner_fax'] .'</p>';
    }
    $supplier .= '<p>Email: '. $supplier_arr['sales_owner_email'] .'</p>';
    if($supplier_arr['sales_owner_website']!="")
        $supplier .= '<p>Website: '. $supplier_arr['sales_owner_website'] .'</p>';
    if($supplier_arr['sales_owner_reg_no']!="")
        $supplier .= '<p>'. $AppUI->_('Reg No') .': '. $supplier_arr['sales_owner_reg_no'] .'</p>';
    $supplier .= $reg_no;
    
    $invoice_no=array();
    if(isset($quotation_id) && $quotation_id!=0 && $quotation_id!="")
    {
        $invocie_arr = $CQuotationManager->get_invoice_by_quotation($quotation_id);
        foreach ($invocie_arr as $data) {
            $invoice_id = $data['invoice_id'];
            $invoice_revision_id = $CInvoiceManager->get_invoice_revision_lastest($invoice_id);
            $invoice_no[]= '<a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_revision_id.'" return false;">'. $data['invoice_no'].'</a>';
        }
    }
    
    //display the select list
    $buffer = '<select id="department" name="department_id" style="width:200px;" class="text">';
    $buffer .= '<option value="" style="font-weight:bold;">'.$AppUI->_('Select').'</option>'."\n";
    $company = '';
    
    // Lay id department cua cac User hien tai
    $dep_user_id=0;
    $dep_user_arr = $CDepManager->getDeparmentByUser($AppUI->user_id);
    $dep_user_id_arr = array();
    if(count($dep_user_arr)>0)
    {
        //$dep_user_id = $dep_user_arr[0]['dept_id'];
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
                $buffer.=$CDepManager->showchilddept($row,$leve,$quo_department_id);
                $buffer.=$CDepManager->findchilddept($rows, $row['dept_id'],$leve,$quo_department_id);
            }
        }
    }
    $buffer .= '</select>';
    // END display the select list
    
    require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
    $CTemplatePDF = new CTemplatePDF();  
    $reference_no="<input type='hidden' name='reference_no' id='reference_no' value='' type='hidden'/>";
    
    //Kiem tra template, New template_serser = 4( HL)=>thuc hien chuc nang reference_no
    $template_pdf_5 = $CTemplatePDF->get_template_pdf(5);
    if( $template_pdf_5[0]['template_server'] == 8)
    {
        $reference_value="";
        if (count($quotation_details_arr1) > 0) {
           
            $reference_value=$quotation_details_arr1[0]['reference_no'];
        }
        $reference_no="<p>Reference No<input type='text' class='text' name='reference_no' id='reference_no' value='".$reference_value."' /></p>";
    }
    
    $save_status = '<button class="ui-button ui-state-default ui-corner-all" onclick="view_history('. $quotation_id .', '. $quotation_revision_id .'); return false;">History</button>';
    $quotation_status = '<p><font>Status: </font> <span style="width:200px;" id ="quotation_status" >'. $quotation_stt_dropdown .'</span></p>';
    $quotation_info = '<p>Date: <input readonly="true" style="padding-left:5px;" type="text" class="text" name="quotation_date_display" id="quotation_date_display" value="'. $date_display .'">';
    $quotation_info .= '<input type="hidden" name="quotation_date" id="quotation_date" value="'. $date_hidden .'">';
    $quotation_info .= $quotation_status;
    $quotation_info .= $reference_no;
    
    $quotation_info .= '<p>Quotation No: <input type="text" style="padding-left:5px;" class="text" name="quotation_no" id="quotation_no" value="'. $quotation_no .'" onchange="load_quotation_revision(this.value);" onchange="count_change();">';
    $quotation_info .= '<input type="hidden" name="count_click" id="count_click" value="" />';
    $quotation_info .= '<p>Quotation Rev: <span style="float:right" id="quo_rev"><input type="text" style="padding-left:5px;" class="text" readonly="true" name="quotation_revision" id="quotation_revision" value="'.$quotation_rev.'"></span>';
    $quotation_info .= '<p>Service Order: <input type="text" style="padding-left:5px;" class="text" name="service_order" id="service_order" value="'.$servicer_order.'">';
    $quotation_info .= '<p>Department: '.$buffer;
    if(count($invocie_arr)>0 && $quotation_id!=0)
        $quotation_info .= '<p>Invoice No:&nbsp;&nbsp;&nbsp;'.implode (", ",$invoice_no).'</p>';
    $div_header_supplier  = "<p><b>Supplier</b></p>";
    $div_supplier_node = $AppUI ->createBlock('div_logo_supplier_node',$supplier, 'style="background:#fff;color:#222222;padding:4px;"');
    $div_supplier = $AppUI ->createBlock('div_logo_supplier',$div_header_supplier.  $div_supplier_node, 'class="ui-state-default ui-corner-all" style="padding:5px"');
    $div_quotation_info = $AppUI ->createBlock('div_quotation_info', $quotation_info, 'style="border:1px solid #dcdcdc;border-radius:8px; padding: 10px;margin-bottom:15px;"');
    //$div_quotation_status = $AppUI ->createBlock('div_quotation_status', $quotation_status, '');

    $div_logo_left = $AppUI ->createBlock('div_logo_left', $img , 'style="float: left; width: auto; height:auto;"');
 
    $div_logo_right = $AppUI ->createBlock('div_logo_right', $div_supplier . $div_quotation_info , 'style="float: right; width: 380px;"');

    echo '<div id="div_info_1">' . $div_logo_left . $div_logo_right . '</div>';
 /* End Load Billing Adress */
 if($client_id==0){
     // Load Billing Address va Load Job Adress khi add new
     $dropdown_address = '<div class="bill_value"  id="quo_sel_address"><select  name="address_id" id="address_id" onchange="load_client_address(this.value);"><option value="0" selected="selected" >--Select--</option></select></div>';
     $dropdown_location = '<div class="bill_value" id="quo_sel_location"><select  name="job_location_id" id="job_location_id" onchange="_get_contact_jobLocation(this.value);"><option value="0" selected="selected" >--Select--</option></select></div>';
 }
 else{
    // Load Billing Address va Load Job Adress khi update
    $dropdown_address = '<div class="bill_value" id="quo_sel_address">'. _get_address_select($client_id,$address_id) .'</div>';
    $dropdown_location = '<div class="bill_value" id="quo_sel_location">'. _get_job_location_select($client_id,$job_location) .'</div>';
 }
    
    // Doan nay cho companies (Anhnn update)
    $rows = $CSalesManager->get_list_companies();
    //$client_option = '';
    $title_arr = dPgetSysVal('CustomerTitle');
    $client_option = '<option value="0">-- Select --</option>';
    $title_customer = '';
    foreach ($rows as $row) {
        $selected = '';
        if ($client_id == $row['company_id']){
            $title_customer = $title_arr[$row['company_title']];
            $selected = 'selected="selected"';
        }
        $client_option .= '<option value="'. $row['company_id'] .'" '. $selected .'>'. $row['company_name'] .'</option>';
    }
    
    $co_option = '<option value="0"  >-- Select --</option>';
    foreach ($rows as $rows_co) {
        $selected="";
        if($quotation_co == $rows_co['company_id'])
            $selected = 'selected';
        $co_option .= '<option value="'. $rows_co['company_id'] .'" '. $selected .'>'. $rows_co['company_name'] .'</option>';
    }
    if($canEditDept && $editView_quotation)
        $add_address = '<button class="ui-button ui-state-default ui-corner-all" onclick="addNewAddress(); return false;">Add address</button>';
    
    $dropdown_client = '<span id="title_customer" style="left: 8px;position: relative;top: 13px;">'.$title_customer.'</span><div class="bill_value"> <span id="sel_customer"><select style="width:393px" name="customer_id" id="customer_id" onchange="load_address(this.value);">'. $client_option .'</select></span>&nbsp;&nbsp;&nbsp;'.$add_address.'</div>';
      //$dropdown_client = '<div class="bill_value"><select class="text_select" name="customer_id" id="customer_id" onchange="load_address(this.value);">'. $client_option .'</select></div>';  
//load ra phone, fax, email cua customer (Anhnn)
    $co_dropdown = '<div class="bill_value"><select style="width:393px" name="quotation_CO" id="quotation_co" >'. $co_option .'</select></div>';
    $client_address =  $CSalesManager->get_address_by_id($address_id);
    
    // lay country customer
    $array_countries = $cconfig->getRecordConfigByList('countriesList');
    $list_countries = array();
    foreach ($array_countries as $array_country) {
        $list_countries[$array_country['config_id']] = $array_country['config_name'];
    }
    $phone = "";
    $option ="";
    $mobile="";
    $address_print = '<div style="overflow:visible;padding:0;"><div class="bill_lable"></div><div class="bill_value"  style="padding:5px 0;" name="client_address" id="client_address"></div></div>';
    if (count($client_address) > 0) {
        if($client_address['address_phone_1']!="") 
            $phone = 'Phone: '.$client_address['address_phone_1'].'&nbsp;&nbsp;&nbsp;&nbsp;';
        elseif($client_address['address_phone_2']!="")
            $phone = 'Phone: '.$client_address['address_phone_2'].'&nbsp;&nbsp;&nbsp;&nbsp;';
        if($client_address['address_mobile_1']!="") 
            $mobile = 'Mobile Phone: '.$client_address['address_mobile_1'];
        elseif($client_address['address_mobile_2']!="")
            $mobile = 'Mobile Phone: '.$client_address['address_mobile_2'];
        
        if($client_address['address_street_address_2']!="")
            $option.=$client_address['address_street_address_2'].'&nbsp;&nbsp;&nbsp;&nbsp;';
        $option.=$phone;
        if($client_address['address_fax']!="")
            $option.='Fax: '.$client_address['address_fax'];
        $option.=$mobile;
//        $option = $client_address['address_phone_1'] .', '. $client_address['address_fax'] , '. $client_address['address_email'];
    }
        //$address_other = _get_client_address();
        //$address_print .= '<p name="client_address" id="client_address">'.$address_other.'</p>';
        if($option!="")
            $address_print = '<div><div class="bill_lable">&nbsp;</div><div class="bill_value" class="text_select" style="padding:5px 0;" name="client_address" id="client_address">'.$option.'</div></div>';
    
    $client = '<div><div class="bill_lable" style="padding-top: 12px;"><b>Bill To:</b>* &nbsp;<a href="#"  onclick="load_frm_search_customer();return false;"><img width="25" border="0" src="./images/icons/search.png" /></a></div> '. $dropdown_client .'</div>';
    $client .= '<div style="overflow:visible;clear:both;width:100%;padding-top:15px;"><div class="bill_lable">C/O: </div> '. $co_dropdown .'</div>';
    $client .= '<div style="overflow:visible;clear:both;width:100%;padding-top:15px;"><div class="bill_lable">Address: </div>'.$dropdown_address.'</div>';
    $client .= $address_print;       
    
    $quo_attention_arr = $CSalesManager->get_salesAttention_by_SalesType($quotation_id, "quotation");
//    print_r($inv_attention_arr);
    $count_quo_attention = count($quo_attention_arr);
    if($count_quo_attention==0){
        $attention_arr = $CSalesManager->get_list_attention($client_id);
        $attention_option = '<option value="0" >--Select--</option>';
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
        $dropdown_attention = '<span id="sel_title">'.get_title_contact_quo($attention_id).'</span><span id="sel_attention"><select class="text_select" name="attention_id[]" id="attention_id" style="width:319px" onchange="load_email(this.value,0);">'. $attention_option .'</select></span>';
        
        $email_contact="";
        $email='<div> &nbsp;<div style="margin-top:5px;" id="email" class="bill_value"></div></div>';
        if($attention_id != ""){
            $email_rr=$CSalesManager->get_attention_email($attention_id);
            if(count($email_rr)>0){
               if($email_rr['contact_phone'])
                    $phone = $email_rr['contact_phone']."&nbsp;&nbsp;&nbsp;";
               elseif($email_rr['contact_phone2'])
                    $phone = $email_rr['contact_phone2']."&nbsp;&nbsp;&nbsp;";
               
                if($email_rr['contact_email'])
                   $email_contact .= "Email: ". $email_rr['contact_email']. "&nbsp;&nbsp;&nbsp;";
                if($email_rr['contact_mobile'])
                    $email_contact .= "Mobile: ". $email_rr['contact_mobile']."&nbsp;&nbsp;&nbsp;";
                $email_contact.=$phone;
                $email='<div> &nbsp;<div style="margin-top:5px;" id="email" class="bill_value">'.$email_contact.'</div></div>';
            }
        }else{
            $email= '<div> &nbsp;<div style="margin-top:5px;" id="email" class="bill_value">'. _get_email().'</div></div>';
        }
    }
    
    if($canEditDept && $editView_quotation)
        $add_attention = '<button class="ui-button ui-state-default ui-corner-all" onclick="addContactAttention(); return false;">Add contact</button>';
    
    $cash = '
                <table border="0" width="100%">
                    <tbody>';
    if($count_quo_attention>0){
        $tmp=0;
        foreach ($quo_attention_arr as $quo_attention_row) {
            $tmp++;
            $attention_arr = $CSalesManager->get_list_attention($client_id);
            $attention_option = '<option value="0" >--Select--</option>';
            if (count($attention_arr) > 0) {
                foreach ($attention_arr as $attention) {
                    $selected = '';
                    if ($attention['contact_id'] == $quo_attention_row['attention_id']){
                        $selected = 'selected="selected"';
                        $contacts_title_id = $attention['contact_title'];
                    }
                    $attention_option .= '<option value="'. $attention['contact_id'] .'" '. $selected .'>'. $attention['contact_first_name'] .' '. $attention['contact_last_name'] .'</option>';
                }
            }
            $lable_attn = 'Attention: <a class="icon-add icon-all" href="#" style="margin-right:0px;" onclick="add_quo_inline_attention(); return false;">&nbsp;</a>';
            
                $email_contact="";
           $email='<div> &nbsp;<div style="margin-top:5px;" id="email" class="bill_value"></div></div>';
           if($quo_attention_row['attention_id'] != ""){
               $email_rr=$CSalesManager->get_attention_email($quo_attention_row['attention_id']);
               if(count($email_rr)>0){
                   if($email_rr['contact_phone'])
                        $phone = "Phone: ".$email_rr['contact_phone']."&nbsp;&nbsp;&nbsp;";
                   elseif($email_rr['contact_phone2'])
                        $phone = "Phone: ".$email_rr['contact_phone2']."&nbsp;&nbsp;&nbsp;";
                   
                   if($email_rr['contact_email'])
                      $email_contact .= "Email: ". $email_rr['contact_email']. "&nbsp;&nbsp;&nbsp;";
                   if($email_rr['contact_mobile'])
                       $email_contact .= "Mobile: ". $email_rr['contact_mobile']."&nbsp;&nbsp;&nbsp;";
                   $email_contact .=$phone;
                   
                   $email='<div style="margin-top:5px;" id="email" >'.$email_contact.'</div>';
               }
           }else{
               $email= '<div style="margin-top:5px;" id="email">'. _get_email().'</div>';
           }
            
            if($tmp>1){
                $lable_attn ="";
                $add_attention="";
            }
            $dropdown_attention = '<span id="sel_title_'.$quo_attention_row['attention_id'].'">'.get_title_contact_quo($quo_attention_row['attention_id']).'</span>
                <span id="sel_attention"><select class="text_select" name="attention_id[]" id="attention_id'.$quo_attention_row['attention_id'].'" style="width:319px" onchange="load_email(this.value,'.$quo_attention_row['attention_id'].');">'. $attention_option .'</select></span>';
            $cash .=           '<tr valign="top">
                                    <td width="19.5%" style="padding-top:5px;"><input type="hidden" name="attn_id[]" value="'.$quo_attention_row['sales_attention_id'].'" />'.$lable_attn.'</td>
                                    <td width="62%">'.$dropdown_attention.$email.'</td>
                                    <td >'.$add_attention.'</td>
                                </tr>';
        }
    }else{
        
        $cash .=   '<tr valign="top">
                        <td width="19.5%" style="padding-top:5px;">Attention: <a class="icon-add icon-all" href="#" style="margin-right:0px;" onclick="add_quo_inline_attention(); return false;"></a></td>
                        <td width="62%">'.$dropdown_attention.$email.'</td>
                        <td >'.$add_attention.'</td>
                    </tr>';
    }
     $cash.=        '</tbody>
                    <tfoot id="add_line_attn_foot">
                        <input type="hidden" id="count_attn" name="count_attn" value="1" />
                    </tfoot>
                </table>';
    
    
    $joblocation='<div style="width:100%; overflow:visible;clear:both;"><div class="bill_lable">Job location:</div> '.$dropdown_location.'</div>';
    // Load ra contact cua Job Location.
    if($job_location>0)
        $contact_arr = $CCompany->getContactInAddress ($job_location);
    
    $contact_option = '<option value="0">--Select--</option>';
    if (count($contact_arr) > 0) {
        foreach ($contact_arr as $contact_row) {
            $selected = '';
            if ($contact_id == $contact_row['contact_id'])
                $selected = 'selected="selected"';
            $contact_option .= '<option value="'. $contact_row['contact_id'] .'" '. $selected .'>'. $contact_row['contact_first_name'] .' '. $contact_row['contact_last_name'] .'</option>';
        }
    }
    
    $contract_by_quotation = $CContractQuotation->getContractQuotaiton($quotation_id);
    $cout_contract = count($contract_by_quotation);
    if($cout_contract==1)
    {
        
        $view_contract = '<a href="?m=engagements&a=view_details&engagement_id='.$contract_by_quotation[0]['contract_id'].'&tab=0">view Contract</a>';
    }
    else if($cout_contract==0)
    {
        $view_contract="";
    }
    else
    {
        $view_contract = '<span href="#" style="cursor:pointer;color:#08245b;" onclick="viewContract('.$quotation_id.');return false;">view Contract</span>';
    }
    
    if($canEditDept && $editView_quotation)
        $add_contact = '<button class="ui-button ui-state-default ui-corner-all" onclick="addContactAddress(); return false;">Add contact</button>';
    $dropdown_contact = '<div class="bill_value"><select class="text_select" style="width:393px" name="contact_id" id="contact_id" onclick="load_email_contact(this.value)">'. $contact_option .'</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$add_contact.'</div>';
    $contact = '<div><div class="bill_lable">Contact: </div id="contact"> '.$dropdown_contact.'</div>';
    $contract_no = '<div><div class="bill_lable" >Contracts No: </div ><div class="bill_value"><span id="quo_contract_no">'.load_contract_no($client_id,$quotation_id,$job_location).'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$view_contract.'</div></div>';
    
    $address_contact_option = "";$phone="";
    $address_contact = '<div> &nbsp;<div style="margin-top:5px;" id="address_contact" class="bill_value"></div></div>';
    if($contact_id!=""){
        $address_contac_arr=$CSalesManager->get_attention_email($contact_id);
        if(count($address_contac_arr)>0){
           if($address_contac_arr['contact_phone'])
                $phone = "Phone: ".$address_contac_arr['contact_phone']."&nbsp;&nbsp;&nbsp;";
           elseif($address_contac_arr['contact_phone2'])
                $phone = "Phone: ".$address_contac_arr['contact_phone2']."&nbsp;&nbsp;&nbsp;";
            
            if($address_contac_arr['contact_email'])
                $address_contact_option .= "Email: ". $address_contac_arr['contact_email']. "&nbsp;&nbsp;&nbsp;";
            if($address_contac_arr['contact_mobile'])
                $address_contact_option .= "Mobile: ". $address_contac_arr['contact_mobile']. "&nbsp;&nbsp;&nbsp;";
            $address_contact_option.=$phone;
            $address_contact='<div> &nbsp;<div style="margin-top:5px;" id="address_contact" class="bill_value">'.$address_contact_option.'</div></div>';
        }
        else{
            $address_contact= '<div> &nbsp;<div style="margin-top:5px;" id="address_contact" class="bill_value">'. _get_email().'</div></div>';
        }
    }
    $contact.=$address_contact;

    //$div_2_client = $AppUI ->createBlock('div_2_client', $client, '');
    $div_2_client='<div id="div_2_client" style="overflow:visible;">'.$client.'</div>';
    $div_2_cash = '<div id="div_2_cash" style="margin-top:0px;padding:1px;overflow:visible;clear:both;">'.$cash.'</div>';
    //$div_2_email= $AppUI ->createBlock('div_2_email',$email,'style="padding:8px 0;"');
    $div_2_joblocation = '<div id="div_2_joblocation" style="padding-top:8px;overflow:visible;clear:both;">'.$joblocation.'</div>';
    $div_2_contact = '<div id="div_2_contact" style="padding-top:8px;overflow:visible;clear:both;">'.$contact.'</div>';
    $div_2_contract_no = '<div id="div_2_contract_no" style="padding-top:8px;overflow:visible;clear:both;">'.$contract_no.'</div>';
    // Lay nhung contact la coordinator
    $roles_coord = $crole->getRolesByValue('coordinator');
    $UserIDcoord_arr = $crole->getUserIdByRoleId($roles_coord['id']);
    //print_r($UserIDcoord_arr);
    $option_contactCoord ='<option value="0">--Select--</option>';
    foreach ($UserIDcoord_arr as $UserIDcoord_row) {
        //echo $UserIDcoord_row['value'];
        $contact_manager = $ContactManager->getContactByUserId($UserIDcoord_row['value']);
        if($contact_manager['user_id']==$UserIDcoord_row['value']){
            if($contact_manager['contact_id'] == $sales_sevice_coordinater){
                $selected = 'selected="selected"';
            }
            $option_contactCoord .='<option value="'.$contact_manager['contact_id'].'" '.(($contact_manager['contact_id'] == $sales_sevice_coordinater) ? 'selected':'').'>'.$contact_manager['contact_last_name'] .', '. $contact_manager['contact_first_name'].'</option>';
        }
    }// END
    $sales_contact = '<p><b>Sales Agent:</b></p>';
    $sales_contact .= '<p>Name: <input type="text" class="text" name="quotation_sale_person" id="quotation_sale_person" value="'.$sale_person_name.'" onkeyup="show_save(\'quotation_sale_person\');"></p>';
    $sales_contact .= '<p>Email: <input type="text" class="text" name="quotation_sale_person_email" id="quotation_sale_person_email" value="'. $sale_person_email .'"></p>';
    $sales_contact .= '<p>Phone: <input type="text" class="text" name="quotation_sale_person_phone" id="quotation_sale_person_phone" value="'. $sale_person_phone .'"></p>';
    //$sales_contact .= '<p>Service Coordinator: &nbsp&nbsp<select name="contact_coordinator_id" class="text" style="width:200px;float:right;" id="contact_coordinator_id">'.$option_contactCoord.'</select></p>';
    
    $sales_contact .= '<p>Personnel: <input type="text" class="text" style="background:#eee" name="contact_coordinator_id" readonly=true id="contact_coordinator_id" autocomplete ="off" value="'. $CQuotationManager->get_user_change($user_id).'"></p>';

    //$div_2_left = $AppUI ->createBlock('div_2_left', $div_2_client . $div_2_cash, 'style="float: left; width: 550px;overflow:hidden"');
    $div_2_left='<div style="float: left; width: 660px;overflow:visible;clear:both;">'.$div_2_client.$div_2_cash.$div_2_joblocation.$div_2_contact.$div_2_contract_no.'</div>';
    $div_2_right = $AppUI ->createBlock('div_2_right', $sales_contact, 'style="float: right; width: 360px;"');

    echo '<div id="div_info_2" style="border:1px solid #dcdcdc;border-radius:8px;height:340px; padding: 10px;">' . $div_2_left . $div_2_right . '</div>';
    
    $quo_subject = '<b>Subject:</b><br>';
    $quo_subject .= '<textarea cols="60" rows="4" id="quotation_subject" value="" name="quotation_subject" type="text">'.$sales_subject.'</textarea>'
            . '<a class="icon-save icon-all" onclick="save_template_subheading_or_subject(\'subject\',\'quotation\'); return false;" title="Save subject as template" style="cursor: pointer; left: 9px;position: relative;top: -60px;"></a>'
            . '<img border="0" src="images/icons/list.png" onclick="list_template_subject(\'subject\',\'quotation\');return false;" title="List and apply template" style="cursor: pointer; left: 9px;position: relative;top: -60px;left:-5px">';
    
    echo $AppUI->createBlock('div_subject', $quo_subject , 'style="padding-top: 15px;clear:both;"');
    //echo '<button class="ui-button ui-state-default ui-corner-all" onclick="loadTaskEdit_quo(0,'.$quotation_id.','.$client_id.'); return false;">New task</button>&nbsp;';
    if($quotation_id != 0 && $quotation_revision_id != 0){
        $quotation_id;
        $serviceOder_arr = $CSalesManager->get_serviceoder_id($quotation_id, 'quotation');
        $serviceOder_id = $serviceOder_arr[0]['service_order_id'];
        echo '<input type="hidden" name="quotation_serviceoder" id="quotation_serviceoder" value='. $serviceOder_id .'>';
        //print_r($serviceOder_arr);
    }
    
}

//load ra dia chi sau khi chon Bill To (Anhnn)
function _get_address_select($customer_id=false,$address_id=false) {
    
    global $CSalesManager,$CMemoManager;
    include DP_BASE_DIR."/modules/sales/load_chose.js.php";
    /* Load Billing Adress */   
    $address_option = '<option value="0">--Select--</option>';
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
                        $brand=$address['address_branch_name']." - ";
                    $selected = '';
                    if ($address_id == $address['address_id'] || $address['address_id'] == $customer_memo_err[0]['address_id'])
                        $selected = 'selected="selected"';
                    $address_option .= '<option value="'. $address['address_id'] .'" '. $selected .'>'.$address_type.$brand. $address['address_street_address_1'].' '.$address['address_street_address_2'].' Singapore '.$address['address_postal_zip_code'] .'</option>';
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
                                $brand=$address_contractor_value['address_branch_name']." - ";
                            $selected = '';
                            if ($address_id == $address_contractor_value['address_id'] || $address_contractor_value['address_id'] == $customer_memo_err[0]['address_id'])
                                $selected = 'selected="selected"';
                          $address_option .= '<option value="'.$address_contractor_value['address_id'].'" '. $selected .'>'.$address_type.$brand.$address_contractor_value['address_street_address_1'].' '.$address_contractor_value['address_street_address_2'].' Singapore '.$address['address_postal_zip_code'] .'</option>';
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
                            $brand=$address_agent_value['address_branch_name']." - ";
                        $selected = '';
                        if ($address_id == $address_agent_value['address_id'] || $address_agent_value['address_id'] == $customer_memo_err[0]['address_id'])
                            $selected = 'selected="selected"';
                      $address_option .= '<option value="'.$address_agent_value['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value['address_street_address_1'].' '.$address_agent_value['address_street_address_2'].' Singapore '.$address['address_postal_zip_code'] .'</option>';
                  }
                $address_option.='</optgroup>';
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
                                $brand=$address_agent_value1['address_branch_name']." - ";
                            $selected = '';
                            if ($address_id == $address_agent_value1['address_id'] || $address_agent_value1['address_id'] == $customer_memo_err[0]['address_id'])
                                $selected = 'selected="selected"';
                            $address_option .= '<option value="'.$address_agent_value1['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value1['address_street_address_1'].' '.$address_agent_value1['address_street_address_2'].', Singapore '.$address['address_postal_zip_code'] .'</option>';
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
                                $brand=$address_agent_value1['address_branch_name']." - ";
                            $selected = '';
                            if ($address_id == $address_agent_value1['address_id'] || $address_agent_value1['address_id'] == $customer_memo_err[0]['address_id'])
                                $selected = 'selected="selected"';
                            $address_option .= '<option value="'.$address_agent_value1['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value1['address_street_address_1'].' '.$address_agent_value1['address_street_address_2'].' Singapore '.$address['address_postal_zip_code'] .'</option>';
                        }
                     $address_option.='</optgroup>'; 
                }
            }
        }
    if($customer_id)
        return '<select  name="address_id" id="address_id" onchange="load_client_address(this.value);">'.$address_option.'</select>';
    else
        echo '<select   name="address_id" id="address_id" onchange="load_client_address(this.value);">'.$address_option.'</select>';
}

//load ra dia chi sau khi chon Bill To (Anhnn)
function _get_job_location_select($customer_id=false,$address_id=false) { 
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
                    if($address_arr['address_type']==2)
                        $address_type = "[Billing] ";
                    else if($address['address_type']==1)
                        $address_type = "[Job Site] ";
                    else if($address['address_type']==3)
                        $address_type = "[Inactive] ";
                    else
                        $address_type = "[NA] ";
                    $brand="";
                    if($address['address_branch_name']!="")
                        $brand=$address['address_branch_name']." - ";
                    $selected = '';
                    if ($address_id == $address['address_id'])
                        $selected = 'selected="selected"';
                    $address_option .= '<option value="'. $address['address_id'] .'" '. $selected .'>'.$address_type.$brand.$address['address_street_address_1'].' '.$address['address_street_address_2'].', Singapore '.$address['address_postal_zip_code'] .'</option>';
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
                                $brand=$address_contractor_value['address_branch_name']." - ";
                            $selected = '';
                            if ($address_id == $address_contractor_value['address_id'])
                                $selected = 'selected="selected"';
                          $address_option .= '<option value="'.$address_contractor_value['address_id'].'" '. $selected .'>'.$address_type.$brand.$address_contractor_value['address_street_address_1'].' '.$address_contractor_value['address_street_address_2'].' Singapore '.$address['address_postal_zip_code'] .'</option>';
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
                            $brand=$address_agent_value['address_branch_name']." - ";
                        $selected = '';
                        if ($address_id == $address_agent_value['address_id'])
                            $selected = 'selected="selected"';
                      $address_option .= '<option value="'.$address_agent_value['address_id'].'" '.$selected.'>'.$address_type.$brand.$address_agent_value['address_street_address_1'].' '.$address_agent_value['address_street_address_2'].', Singapore '.$address['address_postal_zip_code'] .'</option>';
                  }
                $address_option.='</optgroup>';
            }
        }
    if($customer_id)
        return '<select  name="job_location_id" id="job_location_id" onchange="_get_contact_jobLocation(this.value);">'.$address_option.'</select>';
    else
        echo '<select name="job_location_id" id="job_location_id" onchange="_get_contact_jobLocation(this.value);">'.$address_option.'</select>';
}

//Load attention sau khi chon Bill To (Anhnn)
function _get_attention_select() {
    
    global $CSalesManager,$CMemoManager;
   
    $attention_arr = $CSalesManager->get_list_attention($_POST['customer_id']);
    $customer_memo_err=$CMemoManager->get_memo_billing_address($_POST['customer_id']);
    $option = '<option value="">-- Select --</option>';
       if (count($attention_arr) > 0) {
        foreach ($attention_arr as $attention) {
            $selected="";
            if ($attention_id == $attention['contact_id'] || $attention['contact_id']==$customer_memo_err[0]['attention_id'] || $attention['contact_id'] == $_POST['contact_id'])
                $selected = 'selected="selected"';
            $option .= '<option value="'. $attention['contact_id'] .'" '. $selected .'>'. $attention['contact_first_name'] .' '. $attention['contact_last_name'] .'</option>';
        }
    }
    echo $option;
}

//Load ra phone, fax, email cua customer (Anhnn)
function  _get_client_address(){
    global $CSalesManager, $cconfig,$CMemoManager ;
    if(isset($_POST['address_id'])){
        $client_address =  $CSalesManager->get_address_by_id($_POST['address_id']);
    }
    else if(isset($_POST['customer_id']) && $_POST['customer_id']!=""){
        $customer_memo_err=$CMemoManager->get_memo_billing_address($_POST['customer_id']);    
        $client_address =  $CSalesManager->get_address_by_id($customer_memo_err[0]['address_id']);
        
    }
    
        // lay country customer
    $array_countries = $cconfig->getRecordConfigByList('countriesList');
    $list_countries = array();
    foreach ($array_countries as $array_country) {
        $list_countries[$array_country['config_id']] = $array_country['config_name'];
    }
    $phone = "";
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
// Load Email va Mobile Job Location
function _get_contact_jobLocation(){
    global $CSalesManager,$CCompany;
    $job_location_id = $_REQUEST['job_location_id'];
    if($job_location_id!="")
        $contact_arr =  $CCompany->getContactInAddress($job_location_id);
//    print_r($client_localtion);
    $contact_option ='<option value="0">--Select--</option>';
    if (count($contact_arr) > 0) {
        foreach ($contact_arr as $contact_row) {
            $selected = '';
            if ($contact_row['contact_id'] == $_REQUEST['contact_id'])
                $selected = 'selected="selected"';
            $contact_option .='<option value="'.$contact_row['contact_id'].'" '.$selected.'>'.$contact_row['contact_first_name'] .' '. $contact_row['contact_last_name'].'</option>';
        }
    }
    echo $contact_option;
    
}
// Load Email tuong ung voi Attention
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
    $email="";
    $phone = "";
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
function show_html_table($quotation_id = 0, $quotation_revision_id = 0,$canEditDept) {
    
    include DP_BASE_DIR."/modules/sales/quotation_table.js.php";
    include DP_BASE_DIR."/modules/sales/css/sales.css.php";
    
    global $AppUI, $CSalesManager,$editView_quotation;
    
    $status = $_POST['status_rev'];

    $quotation_item_arr = array();
    if ($quotation_id != 0 && $quotation_revision_id != 0 ) { // neu hanh dong la view mot quotation revision da co trong he thong
        //_do_update_quotation_order($quotation_id, $quotation_revision_id);
        global $CQuotationManager;
        $invoice_arr = $CQuotationManager->get_db_quotation($quotation_id);
        $sub_heading = $invoice_arr[0]['sub_heading'];
        
        $quotation_item_arr = $CQuotationManager->get_db_quotation_item($quotation_id, $quotation_revision_id);
        
    }
    echo '<input hidden="true" value="'.count($quotation_item_arr).'" id="quotation_count">';
        echo '<div id="div_task"><div id="sales_task_popup-editTask" style="display: none;"></div></div>';
                
        echo '<div id="jdatatable_container_detail_quotation_table2" style="width: 100%; padding-bottom: 15px; margin-top:0px;">';
        echo '<table cellspacing="1" cellpadding="2" style="clear: both; width: 100%" id="detail_quotation_table" class="tbl">
                <thead>
                    <tr>
                        <th style="width: 64px;"><input type="checkbox" onclick="check_all(\'item_select_all\', \'item_check_list\');" id="item_select_all" name="item_select_all"></th>
                        <th style="width: 33px;">#</th>
                        <th style="width: 100px;">Item</th>
                        <th style="width: 78px;">Quantity</th>
                        <th style="width: 115px;">Unit Price</th>
                        <th style="width: 100px;">Discount(%)</th>
                        <th style="width: 120px;">Amount</th>
                    </tr>
                    <tr><td colspan="7"><textarea name="sub_heading" id="sub_heading" rows="2" cols="95">'.$sub_heading.'</textarea>
                            <a class="icon-save icon-all" onclick="save_template_subheading_or_subject(\'subheading\'); return false;" title="Save sub heading as template" style="cursor: pointer; left: 9px;position: relative;top: -25px;"></a>
                            <img border="0" src="images/icons/list.png" onclick="list_template_subheading(\'subheading\');return false;" title="List and apply template" style="cursor: pointer; left: 9px;position: relative;top: -25px;left:-5px">
                        </td></tr>
                </thead>
                <tbody id="quotation_sortable">';
                
            if (isset($quotation_item_arr) && count($quotation_item_arr) > 0) {
                $i = 1;
                foreach ($quotation_item_arr as $quotation_item) {
                    
//                    echo '<pre>';
//                    print_r($quotation_item);
                    if ($quotation_item['quotation_item_type'] == 0) {
                        $item = $CSalesManager->htmlChars($quotation_item['quotation_item']);
                    } elseif ($quotation_item['quotation_item_type'] == 1) {
                        $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                    } elseif ($quotation_item['quotation_item_type'] == 2) {
                        $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                    } else {
                        $item = '<font color="red">Item type not found.</font>';
                    }
                    $item_id = $quotation_item['quotation_item_id'];
                    
                    $editItem = "";
                    if($canEditDept && $editView_quotation)
                        $editItem ='
                                    <a class="icon-edit icon-all" title="Edit item" id="quo_edit_item'.$i.'" onclick="edit_inline('. $item_id .'); return false;" href="#" title="Inline Edit" style="margin-right:0px"></a>
                                    <a class="icon-save icon-all" onclick="popupFormSaveItemTemplate('.$item_id.'); return false;" title="Save item as template" ></a>
                                ';
                    
                    echo '
                        <tr valign="top" id="row_item_'. $item_id .'">
                            <td valign="top" width="9%" align="center" id="">
                                <input type="checkbox" value="'. $item_id .'" name="item_check_list" id="item_check_list">&nbsp
                                <input type="hidden" class="text" id="click_quotation'.$item_id.'" name="" value="1"/>
                                '.$editItem.'
                            </td>
                            <td valign="top" width="4%" align="center" id="stt_'. $item_id .'">'. $i .'</td>
                            <td valign="top" width="48%" id="item_'. $item_id .'">'. $item .'</td>
                            <td valign="top" width="8%" align="right" id="quantity_'. $item_id .'">'. $quotation_item['quotation_item_quantity'] .'</td>
                            <td valign="top" width="12%" align="right" id="price_'. $item_id .'">'. number_format($quotation_item['quotation_item_price'],2) .'</td>
                            <td valign="top" width="10%" align="right" id="discount_'. $item_id .'">'. $quotation_item['quotation_item_discount'] .'%</td>
                            <td valign="top" width="12%" align="right" id="total_'. $item_id .'">'. number_format($CSalesManager->calculate_total_item($quotation_item['quotation_item_price'], $quotation_item['quotation_item_quantity'], $quotation_item['quotation_item_discount']),2) .'</td>
                        </tr>';
                    $i++;
                }
            }
                echo '</tbody>
            </table></div><br/>';
        $total_item = 0;
        if($quotation_id != 0 && $quotation_revision_id != 0){
            $total_item=$CQuotationManager->max_order_item($quotation_id, $quotation_revision_id);
            if($total_item=="")
                $total_item = 0;
        }
        echo '<input type="hidden" name="total_item" id="total_item" value="'.$total_item.'">';
        echo '<input type="hidden" name="count_total_item" id="count_total_item" value="'.$i.'">';
        echo '<input type="hidden" name="quo_id" id="quo_id" value="'.$quotation_id.'">';
        echo '<input type="hidden" name="quo_rev_id" id="quo_rev_id" value="'.$quotation_revision_id.'">';
        echo '<input type="hidden" name="click_edit_quo" id="click_edit_quo" value="">';
        $quotation_status=0;
        if($invoice_arr[0]['quotation_status'] > 0)
            $quotation_status = $invoice_arr[0]['quotation_status'];
//        else
            
        if($editView_quotation)
        {
            if($canEditDept || $status=="add")
            {
                $delete_id = '<a class="icon-delete icon-all" onclick="delete_quotation_item('. $quotation_id .', '. $quotation_revision_id .', \'update__do_remove_quotation_itemno_rev\'); return false;" href="#">Delete</a>';
                $add_id = '<a class="icon-add icon-all" onclick="add_inline_item('.$quotation_status.','.$quotation_id.'); return false;" href="#">Add item</a>';
            }
        }
        
        $re_calculator = '<div id="calculator" style="float:right;"><a href="#" onclick="load_rev_calculator(); return false;">Reverse Calculator</a></div>';
        
        echo $block_del = $AppUI ->createBlock('del_block', $delete_id . $edit_id . $add_id .$re_calculator, 'style="text-align: left;"');
   // }
    
}

function _form_total_and_note($quotation_id = 0, $quotation_revision_id = 0,$canEditDept){

    global $AppUI,$CSalesManager,$addView_quotation,$editView_quotation;
     $status = $_POST['status_rev'];
    $currency_symb = '$'; $total_paid_and_tax = 0;
    
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();

    if ($quotation_id != 0 && $quotation_revision_id != 0 && $status = 'update') {
        
        global $CQuotationManager;
        $quotation_rev_details_arr = $CQuotationManager->get_db_quotation_revsion($quotation_revision_id);
        
        $discount = 0;
        if (count($quotation_rev_details_arr) > 0) {
            $quotation_revision = $quotation_rev_details_arr[0]['quotation_revision'];
            $note_area = $quotation_rev_details_arr[0]['quotation_revision_notes'];
            $term_area = $quotation_rev_details_arr[0]['quotation_revision_term_condition_contents'];
            $tax_id = $quotation_rev_details_arr[0]['quotation_revision_tax'];
            $discount = $quotation_rev_details_arr[0]['quotation_revision_discount'];
        }
        
        // Doan xu ly lay ra payment cho total
//        require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");
//        $CPaymentManager = new CPaymentManager();
//        $payment_arr = $CPaymentManager->list_db_payment($quotation_revision_id);
//        $tr_paid = '';
//        if (count($payment_arr) > 0) {
//            foreach ($payment_arr as $payment) {
//                $tr_paid .= 
//                    '<tr>
//                        <td class="td_left">Amount Paid :</td>
//                        <td class="td_right">'. $currency_symb .''. $payment['payment_amount'] .'</td>
//                    </tr>';
//                
//                $total_paid_and_tax += intval($payment['payment_amount']);
//            }
//        }
        
        $total_item_show = $CQuotationManager->get_quotation_item_total($quotation_id, $quotation_revision_id);
        $revison_lastest = $CQuotationManager->get_latest_quotation_revision($quotation_id);
        //$AppUI->addJSScript('$(\'#quotation_revision\').val(\''.$quotation_revision.'\'); $(\'#quotation_rev_label\').append(\' <font color="red">'.$quotation_revision.'</font><input type="hidden" name="revision_lastest" id="revision_lastest" value="'.$revison_lastest[0]['quotation_revision'].'" />\');');
            $save = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_all_quotation('. $quotation_id .', '. $quotation_revision_id .', \''. $status .'\'); return false;">Save As New Revision</button>&nbsp';
    
    }

    $tax_option = '<option vaue="">---</option>';
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
        $caculating_tax = $CSalesManager->round_up($caculating_tax);
        $total_item_tax_show = number_format($caculating_tax,2);
        $total_paid_and_tax += $caculating_tax;
        if($quotation_rev_details_arr[0]['quotation_revision_tax_edit']!=0)
            $total_item_tax_show = $quotation_rev_details_arr[0]['quotation_revision_tax_edit'];
    }
    $AmountDue=$total_item_show_last_discount + $total_item_tax_show;

    $tax_select = '<select name="quotation_revision_tax" id="quotation_revision_tax" class="text" onchange="quo_tax(this.value,1)">'. $tax_option .'</select>';

    
    
    
    $table = '<table boder="0" width="100%" class="tbl_total">
                <tr>
                    <td class="td_left">'. $AppUI->_('Total') .' :</td>
                    <td class="td_left">
                            '. $currency_symb .'<span id="quo_total_item">'. number_format($total_item_show,2) .'</span>
                            <input type="hidden" name="hidden_quototal_item" id="hidden_quototal_item" value="'.$total_item_show.'" />
                    </td>
                </tr>
                <tr>
                    <td class="td_left">'. $AppUI->_('Discount') .' :</td>
                    <td class="td_left">$<input type="text" id="quotation_revision_discount" name="quotation_revision_discount" value="'.  number_format($discount,2).'" onchange="load_discount();" size="10" style="text-align:right;" /></td>
                </tr>
                <tr>
         <!--           <td width="87%"><div id="inv_add_tax" style="float:right;"><a class="icon-add icon-all" style="margin-right:0px;" onclick="add_tax(); return false;" href="#">Add tax</a></div></td> -->
                    <td class="td_left">'. $AppUI->_('GST') .' @ '. $tax_select .'% :</td>
                    <td class="td_left" >
                        $ <input type="text" name="quotation_revision_tax_edit" id="quo_revision_tax" size="10" style="text-align:right;" value="'.number_format($total_item_tax_show,2).'" />
                        <input type="hidden" name="hidden_quototal_tax" id="hidden_quototal_tax" value="'.$total_item_tax_show.'" />
                    </td>

                </tr>
                <tr>
                    <td class="td_left">'. $AppUI->_('Total Amount') .' :</td>
                    <td class="td_left" >
                        '. $currency_symb .'<span id="quo_due">'. number_format($AmountDue,2) .'</span>
                        <input type="hidden" name="hidden_quototal_due" id="hidden_quototal_due" value="'.$AmountDue.'" />
                    </td>
                </tr>
        </table>';

    echo '<div id="div_4">
            ' . $table . '
         </div>';
    echo '<div style="width:100%; text-align:right;width: 100%;font-weight:bold;margin: 8px 0 0;">
                <input type="radio" value="1" id="option_show" name="option_total" checked onclick="option_total_status(this.value)" /> Show
                <input type="radio" value="0" id="option_hide" name="option_total" onclick="option_total_status(this.value)" /> Hide
            </div>';
    //echo $add_tax = '<div id="inv_add_tax" style="float:right;"><a class="icon-add icon-all" style="margin-right:0px;" onclick="add_tax(); return false;" href="#">Add tax</a></div>';
    // btn insert term and condition
    if($canEditDept || $status=="add")
        $insert_block = '<a class="icon-add icon-all" onclick="insert_term_condition(); return false;" href="#" style="float:right;margin:7px 0px">Insert Term & Condition</a>';
     //$insert_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="insert_term_condition(); return false;">Insert Term & Condition</button>';
     $textarea = '<textarea id="quotation_revision_notes" class="textArea" style="width: 450; height: 80;" name="quotation_revision_notes">'. $note_area .'</textarea>';
     $textarea1 = '<textarea id="quotation_revision_term_condition_contents" class="textArea" style="width: 450; height: 80;" name="quotation_revision_term_condition_contents">'. $term_area .'</textarea>';


    $note_block = $AppUI ->createBlock('div_notes', '<b>Notes:</b> <br/>'. $textarea, 'style="float: left;"');
    
    $terms_block = $AppUI ->createBlock('div_terms', '<b>Terms and Conditions:</b><br/>' . $textarea1 . '<br/>' .$insert_block, 'style="float: right;"');
    
    if($status == 'add') {
        if($addView_quotation)
        {
            $save_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_all_quotation('. $quotation_id .', '. $quotation_revision_id .', \''. $status .'\'); return false;">Save</button>&nbsp';
            $cancel_bloack = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="cancel_quotation()">Cancel</button>&nbsp';
        }
      } else {
          if($editView_quotation)
          {
            $status = 'update_no_rev';
            $save_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_all_quotation('. $quotation_id .', '. $quotation_revision_id .', \''. $status .'\'); return false;">Save</button>&nbsp';
          }
      }
    //echo '<div id="div_5">' . $note_block . $terms_block .'</div>';
//    echo "<div style='width:100%;height:10px'></div>";
//    echo '<div id="div_6">
//            <table width="100%" border="0">
//                <tr>
//                    <td width="30%">Yours Sincerely</td>
//                    <td></td>
//                    <td width="30%" align="center">Confirmation of Order</td>
//                </tr>
//                <tr>
//                    <td></td>
//                    <td height="50"></td>
//                    <td></td>
//                </tr>
//                <tr >
//                    <td>Authorized signature</td>
//                    <td></td>
//                    <td align="center">Company Atamp & Authorized Signature</td>
//                </tr>
//                <tr >
//                    <td height="25"> </td>
//                    <td></td>
//                    <td></td>
//                </tr>
//            </table>
//        </div>';
//    
//    echo $AppUI ->createBlock('btt_block', $save . $save_block . $cancel_bloack , 'style="text-align: center; margin:0px 0px 0px;"'); 
    
}
function _form_quo_note($quotation_id = 0, $quotation_revision_id = 0,$canEditDept){
    
    global $AppUI, $CTemplateManager,$editView_quotation;
     $status = $_POST['status_rev'];
    
    $currency_symb = '$'; $total_paid_and_tax = 0;
    
    if ($quotation_id != 0 && $quotation_revision_id != 0 && $status = 'update') {
        
        global $CQuotationManager,$CSalesManager;
        $quotation_arr = $CQuotationManager->get_db_quotation($quotation_id);
        $quotation_rev_details_arr = $CQuotationManager->get_db_quotation_revsion($quotation_revision_id);

        if (count($quotation_rev_details_arr) > 0) {
            $quotation_revision = $quotation_rev_details_arr[0]['quotation_revision'];
            $note_area = $quotation_rev_details_arr[0]['quotation_revision_notes'];
            $term_area = $quotation_rev_details_arr[0]['quotation_revision_term_condition_contents'];

        }
        
        $quotation_arr = $CQuotationManager->get_db_quotation($quotation_id);
        $revison_lastest = $CQuotationManager->get_latest_quotation_revision($quotation_id);
        $revison_lastest_no = $CSalesManager->create_quotation_revision($revison_lastest[0]['quotation_revision'], NULL, $quotation_arr[0]['quotation_no']);
     
        if($canEditDept && $editView_quotation)
            $save = '<button id="btn-save-all-revision" class="ui-button ui-state-default ui-corner-all" onclick="save_all_quotation('. $quotation_id .', '. $quotation_revision_id .', \''. update .'\'); return false;">Save As New Revision</button>&nbsp';
    
    }else{
        // Lay Term And Condition Mat dinh
        $term_condition_arr = $CTemplateManager ->getlist_term_condition();
        foreach ($term_condition_arr as $term_row) {
            if($term_row['template_type']==0 && $term_row['term_default']==1){
                $term_area .= "\n ".$term_row['term_conttent'];
            }
        }
    }
    
    // btn insert term and condition
    if($editView_quotation)
    {
        if($canEditDept || $status=="add")
            $insert_block = '<a class="icon-add icon-all" onclick="insert_term_condition(); return false;" href="#" style="float:right;margin:7px 0px">Insert Term & Condition</a>';
    }
     //$insert_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="insert_term_condition(); return false;">Insert Term & Condition</button>';
     $textarea = '<textarea id="quotation_revision_notes" class="textArea" style="width: 450; height: 80;" name="quotation_revision_notes">'. $note_area .'</textarea>';
     $textarea1 = '<textarea id="quotation_revision_term_condition_contents" class="textArea" style="width: 450; height: 80;" name="quotation_revision_term_condition_contents" >'. $term_area .'</textarea>';


    $note_block = $AppUI ->createBlock('div_notes', '<b>Notes:</b> <br/>'. $textarea, 'style="float: left;"');
    
    $terms_block = $AppUI ->createBlock('div_terms', '<b>Terms and Conditions:</b><br/>' . $textarea1 . '<br/>' .$insert_block, 'style="float: right;"');
    
    if($status == 'add') {
        $save_block = '<button id="btn-save-all_add" class="ui-button ui-state-default ui-corner-all" onclick="save_all_quotation('. $quotation_id .', '. $quotation_revision_id .', \''. $status .'\'); return false;">Save</button>&nbsp';
        $cancel_bloack = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="back_quo_contracts()">Cancel</button>&nbsp';
      } else {
          $status = 'update_no_rev';
          if($canEditDept && $editView_quotation)
            $save_block = '<button id="btn-save-all_add" class="ui-button ui-state-default ui-corner-all" onclick="save_all_quotation('. $quotation_id .', '. $quotation_revision_id .', \''. $status .'\'); return false;">Save</button>&nbsp';
      }
    echo '<div id="div_5" style="overflow: hidden;">' . $note_block . $terms_block .'</div>';
    echo "<br>";
    echo "<div style='width:100%;height:10px'></div>";
    echo '<div id="div_6">
            <table width="100%" border="0">
                <tr>
                    <td width="30%">Yours Sincerely</td>
                    <td></td>
                    <td width="30%" align="center">Confirmation of Order</td>
                </tr>
                <tr>
                    <td></td>
                    <td height="50"></td>
                    <td></td>
                </tr>
                <tr >
                    <td>Authorized signature</td>
                    <td></td>
                    <td align="center">Company Stamp<br>&<br>Authorized Signature</td>
                </tr>
                <tr >
                    <td height="25"> </td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>';
    
    echo $AppUI ->createBlock('btt_block', $save . $save_block . $cancel_bloack , 'style="text-align: center; margin:0px 0px 0px;"'); 
    
}

/* Ket thuc view quotation details */

function _do_update_quotation_field() {
    
    global $CQuotationManager, $CSalesManager,$contract_breakdown_manage,$AppUI;
   
    $id = dPgetCleanParam($_POST, 'id');
    $quotation_arr = $CQuotationManager->get_db_quotation($id);
    $CContractQuotation = new CContractQuotation();
    $contract_quotation_arr = $CContractQuotation->getContractQuotaiton($id);
    
    if(isset($_POST['quotation_revision_id']))
        $quotation_revision_id = $_POST['quotation_revision_id'];
//    $status_id = dPgetCleanParam($_POST, 'status_id');
    if(isset($_GET['status']))
        $status_update = dPgetCleanParam($_GET, 'status');
    else
        $status_update=$quotation_arr[0]['quotation_status'];
    $updated_content = dPgetCleanParam($_POST, 'value');
    $serviceOder_arr = $CSalesManager->get_serviceoder_id($id, 'quotation');
    $serviceOder_id = $serviceOder_arr[0]['service_order_id'];
    $quotation_item = $CQuotationManager->get_db_quotation_item($id, $quotation_revision_id);
    $col_name = dPgetCleanParam($_GET, 'col_name', '');
    if($updated_content == 3)
    {
        //add contract
        if($quotation_revision_id > 0)
            $quotation_item = $CQuotationManager->get_db_quotation_item($id, $quotation_revision_id);
        if(count($contract_quotation_arr) > 0)
        {
           
            foreach ($contract_quotation_arr as $contract)
            {
                $contract_id = $contract['contract_id'];
                for($i=0;$i<count($quotation_item);$i++) {
                    
                    $obj['breakdown_contract_id'] = '';
                    $obj['contract_id'] = $contract_id;
                    $obj['breakdown_item_name'] = $quotation_item[$i]['quotation_item'];
                    $obj['breakdown_item_value'] = $quotation_item[$i]['quotation_item_price']*$quotation_item[$i]['quotation_item_quantity'];
                   
                    $breakdown_contract_id = $contract_breakdown_manage->add_contract_breakdown($obj);
                }
            }
        }
    }
   $quotation_stt = dPgetSysVal('QuotationStatus');

    if(($col_name == "quotation_status" && $AppUI->user_type !=1) && ($quotation_stt[$status_update] == "Accepted" || $quotation_stt[$_POST['status_update']] == "Accepted")
      
      ){

        
        echo $quotation_stt[$status_update];
//        
        if($status_update == "update_view_status")
            echo "Sorry.You can not update this  Approved Quotation";
        else
            echo "<script> alert('Sorry.You can not update this  Approved Quotation');</script>";
        return ;
    }
   
    $msg = $CQuotationManager->update_quotation_field($id, $updated_content, $col_name);
    
    if (!$msg) {
        if ($col_name == 'quotation_status') {
            $quotation_stt = dPgetSysVal('QuotationStatus');
            if(isset($_POST['act']))
            {
                
                echo '{"mgs":"'.$quotation_stt[$updated_content].'"}';
            }
            else{
                echo $quotation_stt[$updated_content];
                if($quotation_stt[$updated_content]=="Cencelled" && $serviceOder_id>0)
                    echo '<script>sales_done_serviceoder('.$serviceOder_id.','.$quotation_arr[0]['customer_id'].',"quotation");</script>';
            }
            // insert history table
            $msg = $CQuotationManager->insert_history_quotation_status($id, $updated_content, $status_update);
        } else if ($col_name == 'customer_id') {
            $rows = $CSalesManager->get_list_companies();
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    if (intval($row['company_id']) == intval($updated_content)) {
                        echo $row['company_name'].".";
                        break;
                    }
                }
            }
        }
        else if($col_name == 'quotation_subject')
            echo $CSalesManager->htmlChars ($updated_content);
        else if($col_name == 'quotation_date')
            echo $updated_content;
    }
        
    //else
        //echo $updated_content;
}


function change_revision_html() {
    
    global $AppUI, $CQuotationManager;

    $quotation_id = $_POST['quotation_id'];
    $quotation_revision_id = $_POST['quotation_revision_id'];

    $quotation_revision_arr = $CQuotationManager->list_quotation_revision($quotation_id);

    $p = '';
    if (count($quotation_revision_arr) > 0) {
        $i = 1;
        foreach ($quotation_revision_arr as $quotation_revision) {
            $a_1 = '<a href="?m=sales&show_all=1&tab=0&status=update&quotation='.$quotation_revision['quotation_id'].'&quotation_revision_id='.$quotation_revision['quotation_revision_id'].'" onclick="load_quotation('. $quotation_revision['quotation_id'] .', '. $quotation_revision['quotation_revision_id'] .', \'update\'); return false;">';
            $a_2 = '</a>';
            if ($quotation_revision_id == $quotation_revision['quotation_revision_id'])
                $p .= '<p>'. $a_1 .'<font color="#999"><b>'. $i .'.</b> '. $quotation_revision['quotation_revision'] .'</font>'. $a_1 .'</p>';
            else
                $p .= '<p>'. $a_1 .'<b>'. $i .'.</b> '. $quotation_revision['quotation_revision'] .''. $a_2 .'</p>';
            $i++;
        }
    } else {
        $p .= 'Quotation Revision no found';
    }
    
    if ($quotation_revision_id) {
        $quotation_no = $CQuotationManager->get_quotation_field_by_id($quotation_id, 'quotation_no');
   
        echo '<p><h1>Quotation No: '.$quotation_no.'</h1></p><br/>'. $p .'<br/>';
    }
    else
        echo $p;

}

function _popup_new_item( $to_generate = true ) {

    global $AppUI, $CQuotationManager;

    $_form_id = 'frm_add_item_revision';

    $demonstrationForm = new JFormer($_form_id,
                    array(
                        'onSubmitFunctionServerSide' => 'frm_add_item_revision_submit',
                        'action' => '?m=sales&a=vw_quotation&c=_popup_form_process&suppressHeaders=true'
            ));
    $demonstrationSection = new JFormSection($demonstrationForm->id . 'Section', array(
                ///'instanceOptions' => array(
	           //     'max' => 3,
	             //   'addButtonText' => 'Add Item',
	               // 'removeButtonText' => 'Remove Item',
	          //  )
	    ));


    $SalesItemType = dPgetSysVal('SalesItemType');
    $item_jform = array();

    foreach ($SalesItemType as $key => $value) {
        $item_jform[] = array( 'value' => $key, 'label' => $value);
    }

    $item_id = 0;
    if (($quotation_item_id_arr = $_GET['quotation_item_id'])) {
        $quotation_item_arr = $CQuotationManager->get_quotation_item_detail($quotation_item_id_arr[0]);

        if (count($quotation_item_arr) > 0) {
            $item_id = $quotation_item_arr[0]['quotation_item_id'];
            $item = $quotation_item_arr[0]['quotation_item'];
            $price = $quotation_item_arr[0]['quotation_item_price'];
            $quantity = $quotation_item_arr[0]['quotation_item_quantity'];
            $discount = $quotation_item_arr[0]['quotation_item_discount'];
        }
    }



    $demonstrationSection->addJFormComponentArray( array(

        new JFormComponentMultipleChoice('item_type', 'Choose a item type:',
                array(
                    array('value' => '0', 'label' => 'Free text', 'checked' => true),
                    array('value' => '1', 'label' => 'Products', 'disabled' => true),
                    array('value' => '2', 'label' => 'Services', 'disabled' => true),
                ),
                array(
                    'multipleChoiceType' => 'radio',
                    'validationOptions' => array('required'),
                )
        ),
        new JFormComponentSingleLineText('item', 'Item:',
                array(
                    'width' => 'longest',
                    'validationOptions' => array('required'),
                    'dependencyOptions' => array(
                        'dependentOn' => array('item_type', 'type'),
                        'display' => 'hide',
                        'jsFunction' => '$("#item_type-choice1").is(":checked");'
                    ),
                    'initialValue' => $item,
                )
       ),

        //new JFormComponentDropDown('select', 'Item type:',
          //      $item_jform
        //),
        new JFormComponentSingleLineText('quantity', 'Quantity:', array(
            'validationOptions' => array('required'),
            'width' => 'long',
            'mask' => '99999999999',
            'initialValue' => $quantity,
        )),
        new JFormComponentSingleLineText('price', 'Price:', array(
            'validationOptions' => array('required'),
            'width' => 'long',
            'initialValue' => $price,
        )),
        new JFormComponentSingleLineText('discount', 'Discount:', array(
            'width' => 'long',
            'initialValue' => $discount,
            )),
        //new JFormComponentSingleLineText('discount', 'Discount:', array(
            //'width' => 'long',
            //'initialValue' => $discount,
            //)),
        
        new JFormComponentHidden('quotation_item_id', $item_id),
        new JFormComponentHidden('quotation_id', $_POST['quotation_id']),
        new JFormComponentHidden('quotation_revision_id', $_POST['quotation_revision_id']),
        new JFormComponentHidden('status_rev', $_POST['status_rev']),
        new JFormComponentHidden('form_id', $_form_id),
    ));
    
    $demonstrationSection->style = 'height: auto'; // Add the section to the page (place it inside)
    $demonstrationForm->style = 'height: auto';
    $demonstrationForm->addJFormSection($demonstrationSection);
    
    $demonstrationForm->processRequest($to_generate); // Process any request to the form
    $demonstrationForm->clearAllComponentValues();
    //$demonstrationForm->outputHtml();

}

function _popup_form_process() {
    _popup_new_item(false);
}


function _format_CQuotationItemObj($quotation_itemObj) {

        global $AppUI;

        $CQuotationItem = array();
        $CQuotationItem['user_id'] = $AppUI->user_id;
        $CQuotationItem['quotation_id'] = $quotation_itemObj->frm_add_item_revisionSection->quotation_id;
        $CQuotationItem['quotation_revision_id'] = $quotation_itemObj->frm_add_item_revisionSection->quotation_revision_id;
        $CQuotationItem['quotation_item'] = $quotation_itemObj->frm_add_item_revisionSection->item;
        $CQuotationItem['quotation_item_price'] = $quotation_itemObj->frm_add_item_revisionSection->price;
        $CQuotationItem['quotation_item_quantity'] = $quotation_itemObj->frm_add_item_revisionSection->quantity;
        $CQuotationItem['quotation_item_discount'] = $quotation_itemObj->frm_add_item_revisionSection->discount;
        $CQuotationItem['quotation_item_type'] = $quotation_itemObj->frm_add_item_revisionSection->item_type;
        $CQuotationItem['quotation_item_notes'] = $quotation_itemObj->frm_add_item_revisionSection->notes;
        
        return $CQuotationItem;
}

function _do_add_quotation() {
    global $CQuotationManager,$CSalesManager, $AppUI,$CDepManager,$CNextNumber; 

    // lay id va quotation no cua revision chua duoc update
    $quotation_rev_current = $_POST['quotation_revision_id']; 
    $quotation_rev_no_current = $_POST['quotation_revision'];
    $_POST['quotation_revision_tax_edit'] = str_replace(",", "", $_POST['quotation_revision_tax_edit']);
    $_POST['quotation_revision_discount'] = str_replace(",", "", $_POST['quotation_revision_discount']);
    $_POST['quotation_revision_tax_edit'] = $CSalesManager->round_up($_POST['quotation_revision_tax_edit']);
    
    if($_POST['status_rev']=='add')
        $_POST['user_id'] = $AppUI->user_id;


    $quotation_arr = $CQuotationManager->get_quotation_revision($_POST['quotation_id']);
    if($_POST['count_click'] == 1) { // neu co edit quotation_no 
    $quotation_arr_exit = $CQuotationManager->get_db_quotation_already($_POST['quotation_no'],$_POST['department_id']); // Neu sau khi edit, quotation_no trung lap voi quotation_no khac thi dua ra thong bao
    if($quotation_arr_exit) {
//       if(isset($_POST['quotation_no'])) {
          echo '{"status": "Failure", "message": " Quotation Number already exists. A new number has been used."}'; //Thong bao khi trung lap quotation_no
    } else { // Neu khong trung lap thi thuc hien add hoac update binh thuong...
        if($_POST['status_rev'] == 'update_no_rev') {
            $msg = $CQuotationManager->update_quotation($_POST['quotation_id'], $_POST['quotation_no'], $_POST['quotation_date'], $_POST['address_id'], $_POST['quotation_sale_person'], $_POST['quotation_sale_person_email'], $_POST['quotation_sale_person_phone'], $_POST['quotation_status_id'], $_POST['service_order'], $_POST['job_location_id'],$_POST['sub_heading'],$_POST['deparment_id']);
            $msg = $CQuotationManager->update_quotation_no_revision($_POST['quotation_id'], $_POST['quotation_revision'], $_POST['quotation_revision_id'], $_POST['quotation_revision_notes'], $_POST['quotation_revision_tax'], $_POST['quotation_revision_term_condition_contents'], $_POST['quotation_revision_tax_edit'],$_POST['quotation_date'],$_POST['quotation_revision_discount'],$_POST['reference_no']);
            _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
            _do_add_attention_in_quotation($_POST['quotation_id']);
            if(!$msg) {
                if($_POST['click_edit_quo']=="" || $_POST['click_edit_quo']<=0)
                    $exist_item=_do_add_quotation_item($_POST['quotation_id'], $_POST['quotation_revision_id']);
                echo '{"status": "Success","exist_item":"'.$exist_item.'", "quotation_id": ' . $_POST['quotation_id'] . ', "quotation_revision_id": '. $_POST['quotation_revision_id'] .'}';
            } else {
                echo '{"status": "Failure", "message": "sai roi"}';
            }
        }
            if($_POST['contact_coordinator_id']!="")
                $_POST['contact_coordinator_id']=$AppUI->user_id;
            $quotation_id = $CQuotationManager->add_quotation($_POST); // ham nay thuc hien 2 chuc nang add va update, neu gia tri quotation_id co trong csdl thi thuc hien update, k thi add
            if ($quotation_id > 0) {
                _do_add_contract_quotaiton($quotation_id,$_POST); // add and update Contract No
                if ($_POST['status_rev'] == 'add') {
                    $quotation_id = $_POST['quotation_id'] ;
                    $_POST['quotation_revision_date']=$_POST['quotation_date'];
                    $quotation_revision_id = $CQuotationManager->add_quotation_revision($_POST);
                    _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                    if ($quotation_revision_id > 0) {
                        _do_add_attention_in_quotation($quotation_id);
                        $exist_item = _do_add_quotation_item($quotation_id, $quotation_revision_id);
                        $CQuotationManager->insert_history_quotation_invoice($quotation_id, $quotation_revision_id, $_POST['quotation_revision'], "add");
                        echo '{"status": "Success","exist_item":"'.$exist_item.'", "quotation_id": ' . $quotation_id . ', "quotation_revision_id": '. $quotation_revision_id .'}';
                    } else
                        echo '{"status": "Failure", "message": "sai roi"}';
                } elseif($_POST['status_rev'] == 'update') {
                    if (intval($quotation_id) == intval($_POST['quotation_id'])) { // khi update return quotation_id ma = $_POST['quotation_id'] gui len thi thuc hien tiep
                         _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                         if (count($quotation_arr) > 0) {
                            $suffix_count = 2;
                            foreach ($quotation_arr as $quotation) {
                                $quotation_rev_id = $quotation['quotation_revision_id'];
                                $quotation_rev = $quotation['quotation_revision'];
                                $obj_revision_substr = substr($quotation_rev, -$suffix_count); // cat lay 3 ky tu cuoi cung trong chuoi 
                                $msg = $CQuotationManager->updateQuotation_Revision($quotation_rev_id, $_POST['quotation_no'], $obj_revision_substr);
                                
                            }

                            $quotation_revision_id_new = $CQuotationManager->update_quotation_revision($_POST['quotation_revision_id'], array(), $_POST['quotation_item_id']);
                            if ($quotation_revision_id_new > 0) {
                                
                            // //save history quotation change revision or change status
                            //dungnv 10-09-2012
                            $msg = $CQuotationManager->insert_history_quotation_invoice($_POST['quotation_id'], $quotation_rev_no_current, $_POST['quotation_revision']);
                            
                                //_do_add_quotation_item($quotation_id, $quotation_revision_id_new);
                                echo '{"status": "Success", "quotation_id": ' . $quotation_id . ', "quotation_revision_id": '. $quotation_revision_id_new .'}';
                            } else
                                echo '{"status": "Failure", "message": "sai roi"}';
                        }
                    }
                } 
            } else {
                echo '{"status": "Failure", "message": "sai roi"}';
            }
     }
    } else  {
    $quotation_exit = $CQuotationManager->get_db_quotation_already($_POST['quotation_no'],$_POST['department_id']);
    if($quotation_exit && $_POST['status_rev']=='add'){
            echo '{"status": "Failure", "message": "Quotation Number already exists. A new number has been used."}';
    }else{
        if($_POST['contact_coordinator_id']!="")
            $_POST['contact_coordinator_id']=$AppUI->user_id;
        $quotation_id = $CQuotationManager->add_quotation($_POST); // ham nay thuc hien 2 chuc nang add va update, neu gia tri quotation_id co trong csdl thi thuc hien update, k thi add

        if ($quotation_id > 0) {
            _do_add_contract_quotaiton($quotation_id,$_POST); // add and update Contract No
            if($_POST['status_rev'] == 'update_no_rev') {
                $msg = $CQuotationManager->update_quotation($_POST['quotation_id'], $_POST['quotation_no'], $_POST['quotation_date'], $_POST['address_id'], $_POST['quotation_sale_person'], $_POST['quotation_sale_person_email'], $_POST['quotation_sale_person_phone'], $_POST['quotation_status_id'], $_POST['contact_coordinator_id'], $_POST['service_order'], $_POST['job_location_id'],$_POST['sub_heading'],$_POST['deparment_id']);
                $msg = $CQuotationManager->update_quotation_no_revision($_POST['quotation_id'], $_POST['quotation_revision'], $_POST['quotation_revision_id'], $_POST['quotation_revision_notes'], $_POST['quotation_revision_tax'], $_POST['quotation_revision_term_condition_contents'], $_POST['quotation_revision_tax_edit'],$_POST['quotation_date'],$_POST['quotation_revision_discount'],$_POST['reference_no']);
                _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                _do_add_attention_in_quotation($_POST['quotation_id']);
                if($msg) {
                    if($_POST['click_edit_quo']=="" || $_POST['click_edit_quo']<=0)
                    $exist_item=_do_add_quotation_item($_POST['quotation_id'], $_POST['quotation_revision_id']);
                    echo '{"status": "Success","exist_item":"'.$exist_item.'", "quotation_id": ' . $_POST['quotation_id'] . ', "quotation_revision_id": '. $_POST['quotation_revision_id'] .'}';
                } else {
                    echo '{"status": "Failure", "message": "sai roi"}';
                }
            }
            if ($_POST['status_rev'] == 'add') {
                $_POST['quotation_id'] = $quotation_id;
                $_POST['quotation_revision_date'] = $_POST['quotation_date'];
                $quotation_revision_id = $CQuotationManager->add_quotation_revision($_POST);
                _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                if ($quotation_revision_id > 0) {
                    _do_add_attention_in_quotation($quotation_id);
                    $exist_item=_do_add_quotation_item($quotation_id, $quotation_revision_id);
                    $CQuotationManager->insert_history_quotation_invoice($quotation_id, $quotation_revision_id, $_POST['quotation_revision'], "add");
                    echo '{"status": "Success","exist_item":"'.$exist_item.'", "quotation_id": ' . $quotation_id . ', "quotation_revision_id": '. $quotation_revision_id .'}';
                } else
                    echo '{"status": "Failure", "message": "sai roi"}';
            } elseif($_POST['status_rev'] == 'update') {
                if (intval($quotation_id) == intval($_POST['quotation_id'])) { // khi update return quotation_id ma = $_POST['quotation_id'] gui len thi thuc hien tiep
                     _do_add_attention_in_quotation($_POST['customer_id']);
                    _do_memo_billingadress($_POST['customer_id'],$_POST['address_id'],$_POST['attention_id']);
                     if (count($quotation_arr) > 0) {
                        $suffix_count = 2;
                        foreach ($quotation_arr as $quotation) {
                            $quotation_rev_id = $quotation['quotation_revision_id'];
                            $quotation_rev = $quotation['quotation_revision'];
                            $obj_revision_substr = substr($quotation_rev, -$suffix_count); // cat lay 3 ky tu cuoi cung trong chuoi 
                            $msg = $CQuotationManager->updateQuotation_Revision($quotation_rev_id, $_POST['quotation_no'], $obj_revision_substr);
                        }
                        $quotation_revision_id_new = $CQuotationManager->update_quotation_revision($_POST['quotation_revision_id'], array(), $_POST['quotation_item_id']);
                        if ($quotation_revision_id_new > 0) {

                                 //save history quotation change revision or change status
                                 //dungnv 10-09-2012
                                $msg = $CQuotationManager->insert_history_quotation_invoice($_POST['quotation_id'], $quotation_rev_no_current, $_POST['quotation_revision']);
                                
                            //_do_add_quotation_item($quotation_id, $quotation_revision_id_new);
                            echo '{"status": "Success", "quotation_id": ' . $quotation_id . ', "quotation_revision_id": '. $quotation_revision_id_new .'}';
                        } else
                            echo '{"status": "Failure", "message": "sai roi"}';
                    }
                }
            }
        } else {
            echo '{"status": "Failure", "message": "sai roi"}';
        }
        }
        
    }
    
    // Update next number
    $quotation_no = $CSalesManager->create_invoice_or_quotation_no(true,$_POST['department_id']);
    if($_POST['status_rev']=="add" && $quotation_no == $_POST['quotation_no'])
    {
        $save_next = $CNextNumber->update_nextnumber($quotation_no,'sales_quotaiton',$_POST['department_id']);
    }
//    echo '{"status": "'.$_POST['status_rev'].'", "message": "'.$_POST['quotation_no'].'", "quotation_no":"'.$quotation_no.'"}';

}


function _do_add_quotation_item($quotation_id, $quotation_revision_id) {
    
    global $CQuotationManager, $AppUI;
    
    $item = $_POST['quotation_item'];
    //$item = $_POST['quotation_arrr_item'];
    $price = $_POST['quotation_item_price'];
    $price = str_replace(",","",$price);
    $quantity = $_POST['quotation_item_quantity'];
    $discount = $_POST['quotation_item_discount'];
    $order = $_POST['quotation_order'];
    if (($count = count($item)) > 0) {
        
        $item_obj = array();
        
            $item_obj['user_id'] = $AppUI->user_id;
            $item_obj['quotation_id'] = $quotation_id;
            $item_obj['quotation_revision_id'] = $quotation_revision_id;
            $exist=0;
        for ($i = 0; $i < $count; $i++) {
            $check_item = $CQuotationManager->check_item_quotation($quotation_id, $quotation_revision_id, $item[$i]);
            if($check_item==true){
                $item_obj['quotation_item_id'] = 0;
                $item_obj['quotation_item'] = $item[$i];
                $item_obj['quotation_item_price'] = round($price[$i],2);
                $item_obj['quotation_item_quantity'] = round($quantity[$i],2);
                $item_obj['quotation_item_discount'] = floatval($discount[$i]);
                $item_obj['quotation_item_type'] = 0;
                $item_obj['quotation_item_notes'] = null;
                $item_obj['quotation_order'] = intval($order[$i]);
                $CQuotationManager->add_quotation_item($item_obj);
            }
            else
                $exist=1;
        }
            return $exist;
    }
    
}

function _do_update_quotation_item(){
    global $CQuotationManager;
    
    $id = $_POST['item_id'];
    $quo_id = $_POST['quotation_id'];
    $quo_rev_id = $_POST['quotation_rev_id'];
    $quotation_item = $_POST['quotation_item'];
    $quotation_price = round($_POST['quotation_item_price'],2);
    $quotation_qty = intval($_POST['quotation_item_quantity']);
    $quo_dis = floatval($_POST['quotation_item_discount']);
    $quo_order = $_POST['quotation_order'];
    $total_old = $_POST['total_item'];
    $check_item = $CQuotationManager->check_item_quotation($quo_id, $quo_rev_id, $quotation_item, $id);
    if($check_item==false)
    {
        echo '{"status": "Failure", "message":"Adding item failed. Item with the exact same description and amount already exists"}';
    }
    else {
        $item_id = $CQuotationManager ->update_quotation_item($id, $quo_id, $quo_rev_id, $quotation_item, $quotation_price, $quotation_qty, $quo_dis, $quo_order);
        if($item_id>0){
            $total = $CQuotationManager->get_quotation_item_total($quo_id, $quo_rev_id);
            echo '{"status": "Success","Total":'.$total.'}';
            if($total_old!=$total)
                update_tax_quotation($total,$quo_rev_id);
        }else{
            echo '{"status": "Failure", "message":"Error"}';
        }
    }
}


function _do_send_email_quotation() {

    global $CQuotationManager;
    
    if ($_POST['action'] == 'send_mail_in') {
        $quotation_id_array = $_POST['quotation_id'];
        $content = $_POST['content'];
        $reciver = $_POST['reciver'];
        $sender = $_POST['sender'];
        $subject = $_POST['subject'];
        $quotation_revision_id_array = $_POST['quotation_revision_id'];

        $status = $CQuotationManager->send_mail_quotation_revision($quotation_id_array, $quotation_revision_id_array, $content, $sender, $subject, $reciver);
       
    } elseif ($_POST['action'] == 'send_mail_beside') {

        $status = $CQuotationManager->send_mail_quotation_revision($_GET['quotation_id']);
    } 
    if ($status)
            echo '{"status": "Success", "message": "Email sent"}';
        else
            echo '{"status": "Failure", "message": "Email sent error"}';
        
        
}

function _do_print_quotation() {

    global $CQuotationManager;
    
    $quotation_id = $_REQUEST['quotation_id'];
    $quotation_revision_id = $_REQUEST['quotation_revision_id'];
    
//    if (!$quotation_revision_id){
//        $quotation_revision_id = $CQuotationManager->get_quotation_revision_lastest($quotation_id);
        $CQuotationManager->create_quotation_revision_pdf_file($quotation_id, $quotation_revision_id);
//    }else 
//        $CQuotationManager->create_quotation_revision_pdf_file($quotation_id, $quotation_revision_id);
}

function _do_convert_quotation_into_invoice(){
    global $CQuotationManager, $AppUI;
    
    $quotation_id = $_REQUEST['quotation_id'];
    $quotation_revision_id = $_REQUEST['quotation_revision_id'];
    $quotation_details_arr = $CQuotationManager->get_db_quotation($quotation_id);
        if ($quotation_details_arr[0]['quotation_status'] != 3) {
            echo '{"status": "Failure" , "message": "'.$AppUI->_('Quotation has to be approved first').'"}';//message: "Quotation has to be aproved first"
        } else {
                $invocie_id = $CQuotationManager->create_convert_quotation_into_invoice($quotation_id, $quotation_revision_id, $quotation_details_arr[0]['quotation_status']);
                if ($CQuotationManager)
                    echo '{"status": "Success","invocie_id":"'.$invocie_id.'"}';
                else
                    echo '{"status": "Failure", "message": "Error"}';
       }
}

function _do_copy_quotation() {

    global $CQuotationManager;
    
    if ($_POST['action'] == 'copy_in') {
        $quotation_id_array = array(); $quotation_revision_id_array = array();
        $quotation_id_array[] = $_POST['quotation_id'];
        $quotation_revision_id_array[] = $_POST['quotation_revision_id'];

        $status = $CQuotationManager->create_copy_quotation($quotation_id_array, $quotation_revision_id_array);
        
    } elseif ($_POST['action'] == 'copy_beside') {

        $status = $CQuotationManager->create_copy_quotation($_GET['quotation_id']);
    }
        if ($status)
            echo '{"message": "Quotation has been saved"}';
        else
            echo '{"message": "Copy error"}';
}
//END


function _do_remove_quotation() {

    global $CQuotationManager;

    $quotation_id_arr = $_REQUEST['quotation_id'];

    $db_return = $CQuotationManager->remove_quotation($quotation_id_arr);

    if ($db_return)
        echo '{"status": "Success"}';
    else
        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';

}

function _do_remove_quotation_item() {

    global $CQuotationManager;

    $quotation_item_id_arr = $_REQUEST['quotation_item_id'];
    $quotation_id = $_REQUEST['quotation_id'];
    $quotation_revision_id = $_REQUEST['quotation_revision_id'];
    
    $deleteItem = $CQuotationManager->remove_quotation_item($quotation_item_id_arr);
    if($deleteItem)
    {
        echo '{"status": "Success", "quotation_id": '. $quotation_id .', "quotation_revision_id": '. $quotation_revision_id .'}';
    }
    else {
        echo '{"status": "Failure", "message": "Error"}';
    }
    
//    $quotation_revision_id_lastest = $CQuotationManager->get_quotation_revision_lastest($quotation_id); // lay ra quotation_revision_id cuoi cung
//    $quotation_revision_arr = $CQuotationManager->get_db_quotation_revsion($quotation_revision_id_lastest); // truy van lay ra quotation_revision tai quotation_revision_id
//    
//    
//    
//    if (count($quotation_revision_arr) > 0) {
//        $_POST['quotation_revision'] = $quotation_revision_arr[0]['quotation_revision'];
//
//        $quotation_revision_id_new = $CQuotationManager->update_quotation_revision($quotation_revision_id, $quotation_item_id_arr); // goi den ham xu ly tao quotation revision moi va update cac bang lien quan
//        if ($quotation_revision_id_new > 0) { // viec update da thanh cong
//            echo '{"status": "Success", "quotation_id": '. $quotation_id .', "quotation_revision_id": '. $quotation_revision_id_new .'}';
//        } else {
//            echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
//        }
//     //_do_update_quotation_order($_REQUEST['quotation_id'],$_REQUEST['quotation_revision_id']);
//    } else
//        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
    
}

function _do_remove_quotation_revision() {
    global $CQuotationManager;
    $quotation_id = $_POST['quotation_id'];
    $quotation_revision_arr = $CQuotationManager->get_db_quotation_revsion($_POST['quotation_revision_id']);
    if ($_POST['quotation_revision_id']) {
        $quotation_revision_id_arr = array($_POST['quotation_revision_id']);
        $return = $CQuotationManager->remove_quotation_revision($quotation_revision_id_arr);
        if ($return){
            $quotation_rev_id = $CQuotationManager->get_quotation_revision_lastest($quotation_id);
            echo '{"status": "Success","quotation_id":'.$quotation_id.', "quotation_rev_id":'.$quotation_rev_id.'}';
            $CQuotationManager->insert_history_quotation_invoice($quotation_id, $quotation_revision_arr[0]['quotation_revision'],false,"delete");
        }
        else
            echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
    }
}

// Dem so Revision cua 1 quotation
function count_quoRevision_by_quotation($quotation_id){
    global  $CQuotationManager;
    $quoReV_arr=$CQuotationManager->get_db_quotation_rev($quotation_id);
//    print_r($quoReV_arr);
    $count = count($quoReV_arr);
    return $count;
}

function _form_send_mail() {
    global $AppUI, $CQuotationManager;
    require_once($AppUI->getSystemClass( 'libmail' ));
    $mail = new Mail; // create the mail
    $quotation_id = $_REQUEST['quotation_id'];
    $quotation_revision_id = $_REQUEST['quotation_revision_id'];
    if (!$quotation_revision_id){
        $quotation_revision_id = $CQuotationManager->get_latest_quotation_revision($quotation_id);
    }
    $attention_id = $_REQUEST['attention_id'];
    $quotation_details_arr = $CQuotationManager->get_db_quotation_revsion($quotation_revision_id);
    $quotation_rev = $quotation_details_arr[0]['quotation_revision'];
    $content = 'chua config noi dung';
    $from = CSalesManager::get_customer_email_quo($quotation_id);
    $to = CSalesManager::get_attention_email($attention_id);
    $att = $CQuotationManager->create_quotation_revision_pdf_file($quotation_id, $quotation_revision_id, true);
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
            <input id="file_attach" type="text" name="name_file" value="<?php echo $quotation_rev .'.pdf';?>" readonly="true">
             <input class="mainbutton" type="submit" onclick="print_quotation(<?php echo $quotation_id; ?>, <?php echo $quotation_revision_id; ?>); return false;" value="Download" name="dowload_file">
            </td>
        </tr>
        
        <tr>
            <td align="center" colspan="2">
            <input class="mainbutton" type="button" onclick="setSendButton(<?php echo $quotation_id; ?>, <?php echo $quotation_revision_id; ?>)" value="Send" name="send" />
            <input class="mainbutton" type="reset" value="Reset" name="back" />
            </td>
        </tr>
        
        </table>
	</form>

    <?php
}

function change_invoice_revision_html() { // hien thi invoice_no duoc tao ra tu quotation dang xem 
    
    global $AppUI, $CQuotationManager;

    $invoice_id = $_POST['invoice_id'];
    if ($invoice_id) {
        $invoice_no = $CQuotationManager->get_invoice_field_by_id($invoice_id, 'invoice_no');
        echo '<p><h1>Invoice No: <a target="_blank" href="?m=sales">'.$invoice_no.'</a></h1></p><br/>'. $p .'<br/>';
    }
    else
        echo $p;

}
/*
 * dungnv update task Quotation/Invoice tich hop
 * date: 01/10/2012
 */
function _do_view_history() {
    global $AppUI, $CQuotationManager; 
    $history_array = $CQuotationManager->get_list_db_history($_POST['quotation_id']);
    
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
                                    $html .= $CQuotationManager->get_user_change($row['quo_invc_history_user']);
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
                <span>this Quotation?</span>';
    $select .= '</form></div>';
    echo $select;
    
}

function _do_print_html() {
    global $CQuotationManager;
    
    $quotation_id = $_REQUEST['quotation_id'];
    $quotation_revision_id = $_REQUEST['quotation_rev_id'];
    
    $CQuotationManager->create_quotation_revision_html($quotation_id, $quotation_revision_id);
}

function _do_getlist_template() {
    global $CTemplateManager;
    $list_template = $CTemplateManager->getlist_template_for_Quotation();
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
                <tr><input type="hidden" id="quotation_id" name="quotation_id" value="'.$_POST['quotation_id'].'"/></tr>
                <tr><input type="hidden" id="quotation_rev_id" name="quotation_rev_id" value="'.$_POST['quotation_rev_id'].'"/></tr>';
        $i++;
    }
    $html .= '</table></div>';
    echo $html;
    
}
function _do_insert_template_quotation() {
    global $AppUI, $CTemplateManager, $CQuotationManager;
//    $template_id = $_POST['template_id'];
    $status_rev = $_POST['status_rev'];
    $_POST['user_id'] = $AppUI->user_id;
    $_POST['quotation_template'] = $_POST['template_id'];
    
    // get items of template
    $template_items = $CTemplateManager->get_db_template_item($_POST['template_id']);
    // get notes of template
    $template_notes = $CTemplateManager->get_db_note_template($_POST['template_id']);
    if (count($template_notes) > 0) {
        $_POST['quotation_revision_notes'] = $template_notes[0]['note_temp_content'];
    }
    // get term and codition of template
    $template_term = $CTemplateManager->getdb_term_condition($_POST['template_id']);
    if (count($template_term) > 0) {
        $_POST['quotation_revision_term_condition_contents'] = $template_term[0]['note_temp_content'];
    }
    
    // get subheading of template
    $subheading = $CTemplateManager->get_db_sub_heading_template($_POST['template_id']);
    if(count($subheading)>0)
    {
        $_POST['sub_heading'] = $subheading[0]['sub_heading_content'];
    }
    //do add quotation
    $quotation_id = $CQuotationManager->add_quotation($_POST);
    if ($_POST['status_rev'] == 'add') {
            $_POST['quotation_id'] = $quotation_id;
            $quotation_revision_id = $CQuotationManager->add_quotation_revision($_POST);
                if ($quotation_revision_id > 0) {
                    if (count($template_items) > 0) {
                        _do_insert_item_quotation($template_items, $quotation_id, $quotation_revision_id);
                    }
                    echo '{"status": "Success", "quotation_id": ' . $quotation_id . ', "quotation_revision_id": '. $quotation_revision_id .'}';
                }
    }
    
}
function _do_insert_item_quotation($template_items, $quotation_id, $quotation_rev_id) {
    global $CQuotationManager, $AppUI;
    if (count($template_items) > 0) {
            $itemObj = array();
            
            $itemObj['user_id'] = $AppUI->user_id;
            $itemObj['quotation_id'] = $quotation_id;
            $itemObj['quotation_revision_id'] = $quotation_rev_id;
            
            foreach ($template_items as $template) {
                $itemObj['quotation_item_id'] = 0;
                $itemObj['quotation_item'] = $template['item_temp_item'];
                $itemObj['quotation_item_price'] = $template['item_temp_price'];
                $itemObj['quotation_item_quantity'] = $template['item_temp_quan'];
                $itemObj['quotation_item_discount'] = intval($template['item_temp_discount']);
                $itemObj['quotation_item_type'] = 0;
                $itemObj['quotation_item_notes'] = null;
                $CQuotationManager->add_quotation_item($itemObj);
            }
            
    }
}
function select_status($quotation_id = 0){
    global $AppUI,$CSalesManager;
    $quotation_id =$_POST['quotation_id'];
    $quotation_status_arr = $CSalesManager->get_list_quotation_status($quotation_id);
    if (count($quotation_status_arr) > 0) {
        $status_option = '';
        foreach ($quotation_status_arr as $quotation_status) {
            $selected = '';
            if ($quotation_id == $quotation_status['quotation_id'])
                $selected = 'selected="selected"';
            $status_option .= '<option value="'. $quotation_status['quotation_id'] .'" '. $selected .'>'. $quotation_status['quotation_status'] .'</option>';
        }
    }
    $quotation_stt = dPgetSysVal('QuotationStatus');
  //  echo $quotation_stt_dropdown = arraySelect($quotation_stt, 'quotation_status_id', 'id="quotation_status_id" style="float:right;width:200px;"  class="text" size="1" onchange="update_status('. $quotation_id .', this.value, '.$quotation_status['quotation_status'].' , \' quotation_status \')"', $quotation_status['quotation_status'] , true);
       echo $quotation_stt_dropdown = arraySelect($quotation_stt, 'quotation_status_id', 'id="quotation_status_id" style="float:right;width:200px;"  class="text" size="1" onchange="update_status('.$quotation_id .', this.value, '.$quotation_status['quotation_status'].' , \' quotation_status \')"', $quotation_status['quotation_status'] , true);
 
}
function load_html_table(){
    global $CQuotationManager;
    include DP_BASE_DIR."/modules/sales/quotation_table.js.php";
    
    $canEditDept = $CQuotationManager->permissionQuotationByDepartment($_POST['quotation_id']);
    show_html_table($_POST['quotation_id'],$_POST['quotation_rev_id'],$canEditDept);
}
function load_form_total(){
    _form_total_and_note($_POST['quotation_id'],$_POST['quotation_rev_id']);
}
function load_quotationRev_get_quotationNo(){
    global  $CSalesManager;
    //$quotation_rev = $CSalesManager->create_invoice_or_quotation_revision('', $_POST['quotation_no']);
    $obj_revision_substr = strrchr($_POST['quotation_rev'],'-');
    $quotation_rev = $_POST['quotation_no'].$obj_revision_substr;
    echo '<input type="text" style="width:200px;padding-left:5px;" class="text" readonly="true" name="quotation_revision" id="quotation_revision" value="'. $quotation_rev .'">';
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
function _do_update_quotation_order($quotation_id=false,$quotation_revision_id=false){
    global $AppUI, $CQuotationManager;
        if(isset($_POST['quotation_id'])){
            $quotation_order_arr = $_POST['row_item'];
            $quotation_id = $_POST['quotation_id'];
            $quotation_revision_id = $_POST['quotation_rev_id'];
            $count = count($_POST['row_item']);
            for($i=0;$i<$count;$i++)
            {
                $CQuotationManager->update_quotation_item_order($quotation_order_arr[$i],$i+1);
            }
        }
}
function _brank_load_quotation(){
    echo"123123";
}
function _do_load_quotation_no_exist(){
    global $CSalesManager;
    $dept_id = $_POST['dept_id'];
    $quotation_no = $CSalesManager->create_invoice_or_quotation_no(true,$dept_id);
    $quotation_rev = $CSalesManager->create_quotation_revision('', $quotation_no);
    echo '{"quotation_no":"'.$quotation_no.'","quotation_rev":"'.$quotation_no.$quotation_rev.'"}';
    //echo '{"status": "Success", "quotation_id": ' . $quotation_id . ', "quotation_revision_id": '. $quotation_revision_id .'}';
}
function _do_update_done_sevice_order(){
    global $CSalesManager,$CSeviceOder;
    $quotation_id_arr = $_REQUEST['quotation_id'];
    for($i=0;$i<count($quotation_id_arr);$i++){
        $sevice_order_arr = $CSalesManager->get_serviceoder_id($quotation_id_arr[$i], 'quotation');
        print_r($sevice_order_arr);
        if($sevice_order_arr>0){
            $CSeviceOder->updateComment($sevice_order_arr[0]['service_order_id'], $comment=null, 'service_oder_status_history');
            //$CSeviceOder->update_inline_serviceOrder($sevice_order_arr[0]['service_order_id'], 'service_order_invoice', $quotation_id_arr[$i]);
            echo '{"status":"issuce"}';
        }
    }
}
function _do_quo_updat_sevice_order(){
    global $CSeviceOder;
    $sevice_id = $_REQUEST['id_sevice'];
    $quotation_id = $_REQUEST['quotation_id'];
    $CSeviceOder->update_inline_serviceOrder($sevice_id, 'service_order_quotation', $quotation_id);
}
function get_title_contact_quo($contact_id=false){
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
function _do_add_attention_in_quotation($quotation_id){
    global $CSalesManager;
    $attention_id_arr = $_POST['attention_id'];
    $sales_attention_id_arr = $_POST['attn_id'];
    // Neu thay doi customer xoa het attention cua customer do
    if(isset($_POST['onchange_customer']) && $_POST['onchange_customer']==1){
        $salesAtention_id = $CSalesManager->remove_salesAttention (false,$quotation_id);
    }
    for($i=0;$i<count($attention_id_arr);$i++){
        $salesAtention['sales_attention_id']=$sales_attention_id_arr[$i];
        $salesAtention['sales_type_id'] = $quotation_id;
        $salesAtention['attention_id'] = $attention_id_arr[$i];
        $salesAtention['sales_type_name'] = 'quotation';
        if($attention_id_arr[$i]!=0)
            $salesAtention_id = $CSalesManager->add_salesAttention($salesAtention);
        else if($sales_attention_id_arr[$i]!=0)
            $salesAtention_id = $CSalesManager->remove_salesAttention ($sales_attention_id_arr[$i]);
    }
}
function _do_add_template_item(){
    global $CTemplateManager;
    $item_description = $_POST['item_description'];
    $item_quantity = $_POST['item_quantity'];
    $item_price = str_replace(',', '', $_POST['item_price']);
    $item_discount = str_replace(',', '', $_POST['item_discount']);
    $item_total = str_replace(',', '', $_POST['item_total']);
    $template_id = false;
    if($_REQUEST['template_id'])
    {
        $template_id = $_REQUEST['template_id'];
    }
    
    ### Save to List ###
    if($template_id==false){
        // Kiem tra tom tai item tempale
        $check = $CTemplateManager->is_exits_template_item($item_description,$template_id);
        if($check == true){
            echo '{"status":"exits"}';
        }
        else
        {
            $CTemplateManager->insertItem(0, $item_description, $item_quantity, $item_price, $item_discount, $item_total);
            if($CTemplateManager>0)
                echo '{"status":"success"}';
            else
                echo '{"status":"failure"}';
        }
    }
    else
    {
        $check = false;
        for($i=0;$i<count($template_id);$i++) {
            $check = $CTemplateManager->is_exits_template_item($item_description,$template_id[$i]);
            if($check == false){
                $CTemplateManager->insertItem($template_id[$i], $item_description, $item_quantity, $item_price, $item_discount, $item_total);
            }
        }
        if($check==true)
        {
            echo '{"status":"exits"}';
        }
        else
        {
            if($CTemplateManager>0)
                echo '{"status":"success"}';
            else
                echo '{"status":"failure"}';
        }
    }
    
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
function update_tax_quotation($total,$quotation_revision_id){
    global $CQuotationManager,$CSalesManager;
    
    //$invoice_revidion_arr = $CInvoiceManager->get_db_invoice_revsion($quotation_revision_id);
    $quotation_revidion_arr = $CQuotationManager->get_db_quotation_revsion($quotation_revision_id);
    $tax_index = $quotation_revidion_arr[0]['quotation_revision_tax'];
            
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();
    foreach ($tax_arr as $tax_row) {
        if($tax_row['tax_id'] == $tax_index)
            $tax_rate=$tax_row['tax_rate'];
    }
    
    $tax_value = $CSalesManager->round_up($total*($tax_rate/100));
    
    $CQuotationManager->update_quotation_rev_tax($quotation_revision_id,$tax_value);
    
}

function isUserInDepartment()
{
    global $CDepManager;
    $dept_id = $_POST['dept_id'];
    $is = $CDepManager->IsUserInDepartment($dept_id);
    if($is)
        echo '{"status":"success"}';
    else
        echo '{"status":"faild"}';
}

function load_contract_no($customer_id=false,$quotation_id=false,$job_location_id=false)
{
    global $CContract,$CContractQuotation;
    include DP_BASE_DIR."/modules/sales/load_chose.js.php";
    if(isset($_POST['job_location_id']))
        $job_location_id = $_POST['job_location_id'];
    if(isset($_POST['quotation_id']))
        $quotation_id = $_POST['quotation_id'];
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
    $contract_quotation_id_arr = array();
    if($quotation_id)
    {
        $contract_quotation_arr = $CContractQuotation->getContractQuotaiton($quotation_id);
        foreach ($contract_quotation_arr as $contract_quotation_item) {
            $contract_quotation_id_arr[] = $contract_quotation_item['contract_id'];
        }
    }
    //end Lay cac contract duoc gan vao quotaiton
    
    // Group contract Theo Job location
    $contractByJob_id_arr=array();
    $option = '<optgroup label="Same-Location Contracts">';
    foreach ($contractByJob_arr as $contractByJob_item) {
        $select = "";
        if(in_array($contractByJob_item['engagement_id'], $contract_quotation_id_arr))
              $select="selected";  
        $option .='<option '.$select.' value="'.$contractByJob_item['engagement_id'].'">'.$contractByJob_item['engagement_code'].' - '.date('d M Y',  strtotime($contractByJob_item['engagement_start_date'])).' to '.date('d M Y',  strtotime($contractByJob_item['engagement_end_date'])).'</option>';
        $contractByJob_id_arr[] = $contractByJob_item['engagement_id'];
    }
    if(count($contractByJob_arr)<=0)
        $option.='<option value="" >No contracts available</option>'; 
    $option .= '</optgroup>';
    // End group job theo Job location
    
    // Group cac contract khac
    $option .= '<optgroup label="Other Locations">';
    foreach ($contract_arr as $contract_item) {
        $select = "";
        if(in_array($contract_item['engagement_id'], $contract_quotation_id_arr))
              $select="selected";
        if(!in_array($contract_item['engagement_id'],$contractByJob_id_arr))//Loai bo nhung contract theo Job location
            $option.= '<option '.$select.' value="'.$contract_item['engagement_id'].'">'.$contract_item['engagement_code'].' - '.date('d M Y',  strtotime($contract_item['engagement_start_date'])).' to '.date('d M Y',  strtotime($contract_item['engagement_end_date'])).'</option>';
    }
    if(count($contract_arr)<=0)
        $option.='<option value="" >No contracts available</option>'; 
    $option .= '</optgroup>';
    // End group contract khac
    
    if(isset($_POST['job_location_id']) || isset($_POST['customer_id']))
        echo "<select name='select_contract_no[]' multiple id='select_contract_no'>".$option."</select>";
    else
        return "<select name='select_contract_no[]' multiple id='select_contract_no'>".$option."</select>";
}

function _do_add_contract_quotaiton($quotation_id,$post)
{
    global $CContractQuotation;
    $contrac_no_id_arr = $post['select_contract_no'];
    
    $CContractQuotation->deleteContractQuotation($quotation_id);
    foreach ($contrac_no_id_arr as $key => $value) {
        if($value!="" || $value !=0)
        {
            $obj['quotation_id']=$quotation_id;
            $obj['contract_id']=$value;
            $CContractQuotation->addContractQuotation($obj);
        }
    }
}

function reload_form_button()
{
    $quotation_id = $_POST['quotation_id'];
    $quotation_revision_id = $_POST['quotation_revision_id'];
    $status_back = $_POST['status_back'];
    $canEditDept = $_POST['canEditDept'];
    _form_button($quotation_id, $quotation_revision_id, $status_back,$canEditDept);
}

function get_quotation_status(){
    $quotation_stt = dPgetSysVal('QuotationStatus');
    print json_encode($quotation_stt);
}

 //get so by quotation
function get_so_by_quotation($quotation_id) {
    global $CSeviceOder;
    $data = $CSeviceOder->get_so_By_quotation($_POST['quotation_id']);
    echo "<div name='serviceorder' id='serviceorder'>";
        foreach ($data as $result) {
            echo "<input type='radio' name='serviceorder' value={$result['signal_id']} /> <i id={$result['signal_id']}>".$result['service_order_number']."</i> <br />";
        }
    echo "</div>";
    echo "<button class='ui-button ui-state-default ui-corner-all' onclick='closesobyquotation();'); return false;'>Cancel</button>";
    echo "<script type='text/javascript'>
        var serviceorder = $('#serviceorder input[name=serviceorder]').val();
    </script>";
    echo "<button class='ui-button ui-state-default ui-corner-all' onclick='update_invoice_by_so(serviceorder);'); return false;'>Update</button>";
} 
//end get so by quotation

function update_invoice_by_so(){
    
}
?>
